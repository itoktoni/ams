<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Kirim pesan ke Telegram group (untuk notifikasi teknisi).
     * Diam-diam gagal (log warning) — notifikasi tidak boleh menggagalkan proses utama.
     */
    public function sendToChat(string $chatId, string $message, array $inlineButtons = []): bool
    {
        $token = config('services.telegram.bot_token');
        if (empty($token) || empty($chatId)) {
            return false;
        }
        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];
        if (! empty($inlineButtons)) {
            $payload['reply_markup'] = json_encode(['inline_keyboard' => [$inlineButtons]]);
        }
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);
            if (! $response->successful()) {
                \Illuminate\Support\Facades\Log::warning('Telegram sendMessage gagal: '.$response->body());
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Telegram sendMessage error: '.$e->getMessage());
            return false;
        }
    }

    public function sendToGroup(string $message, array $inlineButtons = []): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.group_id');

        if (empty($token) || empty($chatId)) {
            Log::warning('Telegram notif dilewati: TELEGRAM_BOT_TOKEN / TELEGRAM_GROUP_ID belum diset.');

            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if (! empty($inlineButtons)) {
            // [[ {text, url}, ... ], ...] -> satu baris tombol
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => [$inlineButtons],
            ]);
        }

        try {
            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);

            if (! $response->successful()) {
                Log::warning('Telegram sendMessage gagal: '.$response->body());

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Telegram sendMessage error: '.$e->getMessage());

            return false;
        }
    }

    public function formatTiketBaru($tiket): array
    {
        $urgensiIcon = match ($tiket->tiket_tingkat_urgensi ?? '') {
            'kritis' => '🔴', 'tinggi' => '🟠', 'sedang' => '🟡', 'rendah' => '🟢', default => '⚪',
        };
        $aset = \App\Models\Aset::find($tiket->tiket_id_aset);
        $pelapor = \App\Models\User::find($tiket->tiket_id_pelapor);
        $lokasi = \App\Models\LokasiAset::find($tiket->tiket_id_lokasi);
        $lines = [
            "🛠 <b>TIKET BARU</b> {$urgensiIcon}",
            '<b>Nomor:</b> '.($tiket->tiket_nomor ?? '#'.$tiket->tiket_id),
            '<b>Judul:</b> '.($tiket->tiket_judul ?? '-'),
            '<b>Aset:</b> '.($aset?->aset_nama ?? '-'),
            '<b>Kode:</b> '.($aset?->aset_kode ?? '-'),
            '<b>Lokasi:</b> '.($lokasi?->aset_lokasi_nama ?? '-'),
            '<b>Urgensi:</b> '.ucfirst($tiket->tiket_tingkat_urgensi ?? '-'),
            '<b>Pelapor:</b> '.($pelapor?->name ?? '-'),
            '<b>Jatuh Tempo:</b> '.($tiket->tiket_jatuh_tempo ? formatDate($tiket->tiket_jatuh_tempo, true) : '-'),
        ];
        if (! empty($tiket->tiket_deskripsi)) $lines[] = '<b>Deskripsi:</b> '.\Illuminate\Support\Str::limit($tiket->tiket_deskripsi, 200);
        $lines[] = '<a href="'.$this->tiketDetailUrl($tiket).'">🔗 Buka Detail Tiket</a>';
        return [implode("\n", $lines), [['text' => '👁 Buka Detail Tiket', 'url' => $this->tiketDetailUrl($tiket)]]];
    }

    /**
     * Format notifikasi tiket baru untuk group teknisi.
     */
    public function kirimNotifikasiTiketBaru($tiket): bool
    {
        [$msg, $buttons] = $this->formatTiketBaru($tiket);
        return $this->sendToGroup($msg, $buttons[0] ? $buttons[0] : []);
    }

    public function kirimKeTeknisi(\App\Models\Teknisi $teknisi, $tiket): bool
    {
        if (empty($teknisi->teknisi_telegram_id)) return false;
        [$msg, $buttons] = $this->formatTiketBaru($tiket);
        return $this->sendToChat($teknisi->teknisi_telegram_id, $msg, $buttons[0] ?? []);
    }

    public function kirimKeSemuaTeknisi($tiket, ?string $zona = null): int
    {
        $q = \App\Models\Teknisi::whereNotNull('teknisi_telegram_id');
        if ($zona) $q->whereJsonContains('teknisi_zona', $zona);
        $sent = 0;
        [$msg, $buttons] = $this->formatTiketBaru($tiket);
        foreach ($q->get() as $tek) {
            if ($this->sendToChat($tek->teknisi_telegram_id, $msg, $buttons[0] ?? [])) $sent++;
        }
        if ($sent === 0) $this->sendToGroup($msg, $buttons[0] ?? []);
        return $sent;
    }

    public function kirimKeKategori($tiket): int
    {
        $aset = \App\Models\Aset::find($tiket->tiket_id_aset);
        if (! $aset || ! $aset->aset_id_kategori) return $this->kirimKeSemuaTeknisi($tiket);
        $teknisiIds = \App\Models\KategoriTeknisi::where('kategori_id', $aset->aset_id_kategori)->pluck('teknisi_id')->all();
        if (empty($teknisiIds)) return $this->kirimKeSemuaTeknisi($tiket);
        [$msg, $buttons] = $this->formatTiketBaru($tiket);
        $sent = 0;
        foreach (\App\Models\Teknisi::whereIn('teknisi_id', $teknisiIds)->whereNotNull('teknisi_telegram_id')->get() as $tek) {
            if ($this->sendToChat($tek->teknisi_telegram_id, $msg, $buttons[0] ?? [])) $sent++;
        }
        if ($sent === 0) $this->sendToGroup($msg, $buttons[0] ?? []);
        return $sent;
    }

    protected function tiketDetailUrl($tiket): string
    {
        return url('/tiket/show/'.$tiket->tiket_id);
    }
}
