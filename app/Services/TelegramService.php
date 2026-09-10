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
            // Normalisasi: jika $inlineButtons adalah single button ['text'=>..., 'url'=>...], bungkus jadi row
            // jika sudah row [[button]], biarkan; jika sudah [[row]] biarkan
            $isSingleButton = isset($inlineButtons['text']) && isset($inlineButtons['url']);
            $isRow = isset($inlineButtons[0]) && is_array($inlineButtons[0]) && isset($inlineButtons[0]['text']);
            if ($isSingleButton) {
                $payload['reply_markup'] = json_encode(['inline_keyboard' => [[$inlineButtons]]]);
            } elseif ($isRow) {
                $payload['reply_markup'] = json_encode(['inline_keyboard' => [$inlineButtons]]);
            } else {
                // sudah [[button]] atau [[row]]
                $payload['reply_markup'] = json_encode(['inline_keyboard' => $inlineButtons]);
            }
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
            $isSingleButton = isset($inlineButtons['text']) && isset($inlineButtons['url']);
            $isRow = isset($inlineButtons[0]) && is_array($inlineButtons[0]) && isset($inlineButtons[0]['text']);
            if ($isSingleButton) {
                $payload['reply_markup'] = json_encode(['inline_keyboard' => [[$inlineButtons]]]);
            } elseif ($isRow) {
                $payload['reply_markup'] = json_encode(['inline_keyboard' => [$inlineButtons]]);
            } else {
                $payload['reply_markup'] = json_encode(['inline_keyboard' => $inlineButtons]);
            }
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

    public function kirimKeTeknisi(\App\Models\User $teknisi, $tiket): bool
    {
        if (empty($teknisi->telegram_id)) return false;
        [$msg, $buttons] = $this->formatTiketBaru($tiket);
        return $this->sendToChat($teknisi->telegram_id, $msg, $buttons[0] ?? []);
    }

    public function kirimKeSemuaTeknisi($tiket, ?string $zona = null): int
    {
        $q = \App\Models\User::where('role','teknisi')->whereNotNull('telegram_id');
        // teknisi_zona removed — zona filter no-op sekarang
        $sent = 0;
        [$msg, $buttons] = $this->formatTiketBaru($tiket);
        foreach ($q->get() as $tek) {
            if ($this->sendToChat($tek->telegram_id, $msg, $buttons[0] ?? [])) $sent++;
        }
        if ($sent === 0) $this->sendToGroup($msg, $buttons[0] ?? []);
        return $sent;
    }

    public function kirimKeKategori($tiket): int
    {
        // kategori_teknisi sudah di-drop (1 table users) — langsung ke semua teknisi
        return $this->kirimKeSemuaTeknisi($tiket);
    }

    public function kirimKeMasterGroup($tiket, int $groupId): int
    {
        $group = \App\Models\MasterGroup::with(['hasUsers','hasTeknisi'])->find($groupId);
        if (! $group) return 0;
        $hasUsers = $group->hasUsers && $group->hasUsers->isNotEmpty();
        $hasTeknisi = $group->hasTeknisi && $group->hasTeknisi->isNotEmpty();
        if (! $hasUsers && ! $hasTeknisi) return 0;
        [$msg, $buttons] = $this->formatTiketBaru($tiket);
        // prepend group info
        $msg = "👥 <b>GROUP: ".e($group->master_group_nama)."</b> (".e($group->master_group_kode).")\n".$msg;
        $sent = 0;
        // Prioritas: users (1 table) sesuai instruksi — ambil dari users dengan role teknisi
        if ($hasUsers) {
            foreach ($group->hasUsers->whereNotNull('telegram_id')->filter(fn($u)=>!empty($u->telegram_id)) as $user) {
                if ($this->sendToChat($user->telegram_id, $msg, $buttons[0] ?? [])) $sent++;
            }
        }
        // Fallback teknisi lama jika belum migrasi
        if ($sent === 0 && $hasTeknisi) {
            foreach ($group->hasTeknisi->whereNotNull('teknisi_telegram_id') as $tek) {
                if ($this->sendToChat($tek->teknisi_telegram_id, $msg, $buttons[0] ?? [])) $sent++;
            }
        }
        // fallback to group chat if no personal telegram
        if ($sent === 0) $this->sendToGroup($msg, $buttons[0] ?? []);
        return $sent;
    }

    protected function tiketDetailUrl($tiket): string
    {
        return url('/tiket/show/'.$tiket->tiket_id);
    }
}
