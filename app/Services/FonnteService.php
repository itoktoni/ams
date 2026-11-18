<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Pengirim WhatsApp via Fonnte (https://api.fonnte.com/send).
// Fail-soft seperti TelegramService: gagal kirim hanya di-log, tidak melempar exception.
class FonnteService
{
    public function send(string $target, string $message): bool
    {
        $token = config('services.fonnte.token');
        if (empty($token) || empty($target) || empty($message)) {
            Log::warning('Fonnte dilewati: token/target/pesan kosong. Isi WHATSAPP_TOKEN di .env.');

            return false;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => $token])
                ->asForm()
                ->post(config('services.fonnte.url', 'https://api.fonnte.com/send'), [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if (! $response->successful()) {
                Log::warning('Fonnte send gagal: '.$response->body());

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Fonnte send error: '.$e->getMessage());

            return false;
        }
    }

    // Kirim ke nomor/grup default (FONNTE_GROUP di .env — nomor pribadi / grup WA).
    public function sendToGroup(string $message): bool
    {
        $target = config('services.fonnte.group');

        if (empty($target)) {
            Log::warning('Fonnte sendToGroup dilewati: FONNTE_GROUP belum diset di .env.');

            return false;
        }

        return $this->send($target, $message);
    }
}
