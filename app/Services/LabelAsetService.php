<?php

namespace App\Services;

use App\Models\Aset;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;

// Nomor label aset: {KEBUN}/{REGIONAL}/{DEPT}/{DISTRIK}/{SEQ}
// contoh: APN/IX/SDM/DST1/078 + QR dengan logo di tengah.
class LabelAsetService
{
    /**
     * Kode 5 digit huruf+angka kapital random, dijamin unik.
     */
    public static function randomCode(int $length = 5): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa I, O, 0, 1 agar tidak ambigu

        for ($i = 0; $i < 50; $i++) {
            $code = '';
            for ($j = 0; $j < $length; $j++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            if (! Aset::where('aset_nomor_label', 'like', $code.'/%')->exists()) {
                return $code;
            }
        }

        return strtoupper(\Illuminate\Support\Str::random($length));
    }

    /**
     * Susun & simpan nomor label untuk aset (idempotent — yang sudah ada tidak diubah,
     * kecuali $force = true untuk migrasi format).
     * Format: {KEBUN}/{REGIONAL}/{DEPT}/{DISTRIK}/{CODE} → contoh APN/IX/SDM/DST1/0126XG2
     * - KEBUN: aset_lokasi_kode (APN)
     * - REGIONAL: angka romawi dari zona ("REGIONAL IX" → "IX")
     * - DEPT: department_kode PJ (fallback SDM agar kompatibel dengan label lama)
     * - DISTRIK: kata terakhir nama lokasi ("Ruang CRO" → "CRO", fallback DST1)
     * - CODE: {MM}{YY}{UNIQ3} dari tanggal perolehan → "0126XG2" (01=Jan, 26=2026, XG2=unik)
     */
    public static function nomorLabel(Aset $aset, bool $force = false): string
    {
        if (! empty($aset->aset_nomor_label) && ! $force) {
            return $aset->aset_nomor_label;
        }

        $aset->loadMissing(['hasLokasi', 'hasDepartment', 'hasPenanggungJawab.hasDepartment']);

        $clean = fn ($v) => strtoupper(trim(preg_replace('/[^A-Z0-9]+/i', '', (string) ($v ?: ''))));

        $lok = $clean($aset->hasLokasi?->aset_lokasi_kode) ?: 'APN';
        $reg = self::extractRegional($aset->hasLokasi?->aset_lokasi_zona) ?: 'IX';
        $dept = $clean($aset->hasDepartment?->department_kode)
            ?: $clean($aset->hasPenanggungJawab?->hasDepartment?->department_kode)
            ?: 'SDM';
        $distrik = self::extractDistrik($aset->hasLokasi?->aset_lokasi_nama) ?: 'DST1';

        $prefix = "{$lok}/{$reg}/{$dept}/{$distrik}";

        $tgl = $aset->aset_tanggal_perolehan ? \Carbon\Carbon::parse($aset->aset_tanggal_perolehan) : now();
        $code = $tgl->format('m').$tgl->format('y').self::uniq3($prefix, $tgl);

        $nomor = $prefix.'/'.$code;

        $aset->update(['aset_nomor_label' => $nomor]);

        return $nomor;
    }

    /**
     * "REGIONAL IX" → "IX". Sudah berupa romawi ("IX") → langsung dipakai.
     */
    private static function extractRegional(?string $zona): string
    {
        $zona = strtoupper(trim((string) $zona));
        if ($zona === '') {
            return '';
        }
        if (preg_match('/\b([IVXLCDM]+)\b/', $zona, $m)) {
            return $m[1];
        }

        return strtoupper(trim(preg_replace('/[^A-Z0-9]+/i', '', $zona)));
    }

    /**
     * "Ruang CRO" → "CRO" (kata terakhir yang alfanumerik).
     */
    private static function extractDistrik(?string $nama): string
    {
        $nama = strtoupper(trim((string) $nama));
        if ($nama === '') {
            return '';
        }
        $parts = preg_split('/\s+/', $nama) ?: [];
        for ($i = count($parts) - 1; $i >= 0; $i--) {
            $tok = preg_replace('/[^A-Z0-9]+/i', '', $parts[$i]);
            if ($tok !== '') {
                return strtoupper($tok);
            }
        }

        return '';
    }

    /**
     * 3 karakter unik (tanpa I, O, 0, 1 agar tidak ambigu). Prefix + MMYY ikut
     * dicek sehingga label penuh selalu unik, contoh: 0126 + XG2.
     */
    private static function uniq3(string $prefix, \Carbon\CarbonInterface $tgl): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa I, O, 0, 1 agar tidak ambigu
        $mmyy = $tgl->format('m').$tgl->format('y');

        for ($i = 0; $i < 50; $i++) {
            $uniq = '';
            for ($j = 0; $j < 3; $j++) {
                $uniq .= $chars[random_int(0, strlen($chars) - 1)];
            }
            if (! Aset::where('aset_nomor_label', $prefix.'/'.$mmyy.$uniq)->exists()) {
                return $uniq;
            }
        }

        return strtoupper(\Illuminate\Support\Str::random(3));
    }

    /**
     * SEQ berikutnya untuk prefix: MAX(suffix numerik)+1, lalu pastikan unik.
     * (Dipakai format lama; dipertahankan agar label lama tetap valid.)
     */
    private static function nextSeq(string $prefix): int
    {
        $max = 0;
        $labels = Aset::where('aset_nomor_label', 'like', $prefix.'/%')->pluck('aset_nomor_label');
        foreach ($labels as $label) {
            $tail = substr(strrchr((string) $label, '/') ?: '', 1);
            if (ctype_digit($tail)) {
                $max = max($max, (int) $tail);
            }
        }

        $seq = $max + 1;
        while (Aset::where('aset_nomor_label', $prefix.'/'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT))->exists()) {
            $seq++;
        }

        return $seq;
    }

    /**
     * QR PNG (data URI) dengan logo website di tengah + bingkai putih.
     * Tanpa logo yang valid → QR polos (tetap bisa di-scan).
     */
    public static function qrWithLogoDataUri(string $text, int $size = 400): string
    {
        try {
            $renderer = new GDLibRenderer($size, 2);
            $writer = new Writer($renderer);
            $png = $writer->writeString($text, 'UTF-8', ErrorCorrectionLevel::H()); // EC tertinggi — tahan logo tengah
            $qr = @imagecreatefromstring($png);
            if (! ($qr instanceof \GdImage)) {
                throw new \RuntimeException('Gagal membaca PNG QR.');
            }

            $logo = self::loadLogo();
            if ($logo) {
                $qrW = imagesx($qr);
                $qrH = imagesy($qr);
                $boxW = (int) ($qrW * 0.2);
                $boxH = (int) ($qrH * 0.2);
                $boxX = (int) (($qrW - $boxW) / 2);
                $boxY = (int) (($qrH - $boxH) / 2);

                $white = imagecolorallocate($qr, 255, 255, 255);
                imagefilledrectangle($qr, $boxX, $boxY, $boxX + $boxW, $boxY + $boxH, $white);

                $pad = (int) ($boxW * 0.12);
                $dstW = $boxW - $pad * 2;
                $dstH = $boxH - $pad * 2;
                imagecopyresampled($qr, $logo, $boxX + $pad, $boxY + $pad, 0, 0, $dstW, $dstH, imagesx($logo), imagesy($logo));
                imagedestroy($logo);
            }

            ob_start();
            imagepng($qr);
            $png = ob_get_clean();
            imagedestroy($qr);

            return 'data:image/png;base64,'.base64_encode($png);
        } catch (\Throwable $e) {
            Log::warning('LabelAsetService QR gagal: '.$e->getMessage());

            return '';
        }
    }

    /**
     * Data URI logo (untuk kop label). '' jika tidak ada logo valid.
     */
    public static function logoDataUri(): string
    {
        $candidates = [];

        $configured = config('website.logo');
        if (is_string($configured) && $configured !== '') {
            $rel = preg_replace('#^(storage/|public/|/)#', '', $configured);
            $candidates[] = storage_path('app/public/'.$rel);
            $candidates[] = public_path($rel);
            $candidates[] = public_path($configured);
        }
        $candidates[] = public_path('apple-touch-icon.png');

        foreach (array_unique($candidates) as $path) {
            if (! is_string($path) || ! is_file($path)) continue;
            $info = @getimagesize($path);
            if (! $info || ! str_starts_with($info['mime'] ?? '', 'image/')) continue;
            $bin = @file_get_contents($path);
            if ($bin === false) continue;

            return 'data:'.$info['mime'].';base64,'.base64_encode($bin);
        }

        return '';
    }

    /**
     * Baca favicon.ico (DIB/BMP di dalam ICO) menjadi GdImage via imagecreatefrombmp.
     */
    private static function imageFromIco(string $path): ?\GdImage
    {
        try {
            if (! is_file($path)) return null;
            $bin = @file_get_contents($path);
            if ($bin === false || strlen($bin) < 22) return null;
            if (substr($bin, 0, 4) !== "\x00\x00\x01\x00") return null; // bukan ICO

            $off = unpack('V', substr($bin, 18, 4))[1];
            $size = unpack('V', substr($bin, 14, 4))[1];
            $dib = substr($bin, $off, $size);
            if (strlen($dib) < 40) return null;

            // Tinggi DIB mencakup AND mask → bagi 2
            $h = unpack('V', substr($dib, 8, 4))[1];
            $dib = substr_replace($dib, pack('V', max(1, intdiv($h, 2))), 8, 4);

            $bmp = 'BM'.pack('V', 54 + strlen($dib)).pack('V', 0).pack('V', 54).$dib;
            $img = @imagecreatefromstring($bmp);
            if (! ($img instanceof \GdImage)) {
                $tmp = tempnam(sys_get_temp_dir(), 'ico').'.bmp';
                file_put_contents($tmp, $bmp);
                $img = @imagecreatefrombmp($tmp);
                @unlink($tmp);
            }

            return $img instanceof \GdImage ? $img : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Logo tengah QR: pakai favicon dari config website (WEBSITE_FAVICON).
     * Fallback: WEBSITE_LOGO → apple-touch-icon → null (QR polos).
     */
    private static function resolveConfiguredPaths(?string $configured): array
    {
        if (! is_string($configured) || trim($configured) === '') {
            return [];
        }
        $configured = trim($configured);
        // URL absolut → tidak bisa dibaca sebagai file lokal.
        if (preg_match('#^https?://#i', $configured)) {
            return [];
        }
        $rel = preg_replace('#^(storage/|public/|/)#', '', $configured);
        $paths = [
            public_path($configured),
            public_path($rel),
            public_path('storage/'.$rel),
            storage_path('app/public/'.$rel),
            base_path($rel),
        ];
        // Nilai config kadang sudah "storage/website/xxx.png" dan file fisiknya
        // ada di public/storage/website/xxx.png (di-copy via settings). public_path
        // di atas sudah mencakupnya, tapi pastikan unik.
        return array_unique($paths);
    }

    /**
     * Cari file logo: favicon config website → WEBSITE_LOGO → apple-touch-icon → null.
     */
    private static function loadLogo(): ?\GdImage
    {
        $candidates = array_merge(
            self::resolveConfiguredPaths(config('website.favicon')),
            self::resolveConfiguredPaths(config('website.logo')),
            [public_path('apple-touch-icon.png'), public_path('favicon.ico')]
        );

        foreach (array_unique($candidates) as $path) {
            if (! is_string($path) || ! is_file($path)) continue;
            // ICO perlu parser khusus.
            if (str_ends_with(strtolower($path), '.ico')) {
                $ico = self::imageFromIco($path);
                if ($ico instanceof \GdImage) {
                    return $ico;
                }
                continue;
            }
            $info = @getimagesize($path);
            if (! $info) continue;
            $img = match ($info['mime'] ?? '') {
                'image/png' => @imagecreatefrompng($path),
                'image/jpeg' => @imagecreatefromjpeg($path),
                'image/gif' => @imagecreatefromgif($path),
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
                default => false,
            };
            if ($img instanceof \GdImage) {
                return $img;
            }
        }

        return null;
    }
}
