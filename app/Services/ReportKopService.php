<?php

namespace App\Services;

use App\Models\Department;
use App\Models\LokasiAset;
use App\Models\WebsiteSetting;

/**
 * Menyusun kop, judul filter, dan footer untuk semua halaman cetak report.
 *
 * Cara pakai di controller:
 *   $kop = ReportKopService::make('REPORT ASET', $request->only([...]));
 *   return view('pages.laporan.aset.print', array_merge(['data' => $data], $kop));
 *
 * $kop berisi: kop, filterTitle, footer, periodeText.
 */
class ReportKopService
{
    /**
     * @param  string  $judul  Judul utama report, mis. "REPORT ASET".
     * @param  array  $filter  Filter aktif: dari, sampai, divisi/department_id,
     *                         ruangan/aset_id_lokasi, status, kondisi, dsb.
     */
    public static function make(string $judul, array $filter = []): array
    {
        $report = WebsiteSetting::report();
        $parts = [];

        if (! empty($filter['dari']) || ! empty($filter['sampai'])) {
            $dari = ! empty($filter['dari']) ? formatDate($filter['dari']) : '-';
            $sampai = ! empty($filter['sampai']) ? formatDate($filter['sampai']) : '-';
            $parts[] = "Periode: {$dari} s/d {$sampai}";
        }

        // Divisi — terima beberapa nama key filter yang umum dipakai.
        $divisiKey = $filter['divisi'] ?? $filter['department_id']
            ?? $filter['aset_id_department'] ?? null;
        if (! empty($divisiKey)) {
            $dept = Department::find($divisiKey);
            $parts[] = 'Divisi: '.($dept
                ? trim(($dept->department_kode ? $dept->department_kode.' — ' : '').$dept->department_nama)
                : $divisiKey);
        }

        // Ruangan / lokasi — terima beberapa nama key filter yang umum dipakai.
        $ruanganKey = $filter['ruangan'] ?? $filter['lokasi'] ?? $filter['aset_id_lokasi']
            ?? $filter['lokasi_id'] ?? null;
        if (! empty($ruanganKey)) {
            $lok = LokasiAset::find($ruanganKey);
            $parts[] = 'Ruangan: '.($lok
                ? trim(($lok->aset_lokasi_kode ? $lok->aset_lokasi_kode.' — ' : '').$lok->aset_lokasi_nama)
                : $ruanganKey);
        }

        foreach (['status' => 'Status', 'kondisi' => 'Kondisi', 'kategori' => 'Kategori', 'tipe' => 'Tipe', 'level' => 'Level'] as $key => $label) {
            if (! empty($filter[$key])) {
                $parts[] = "{$label}: {$filter[$key]}";
            }
        }

        $footer = $report;
        if (! empty($filter['kota'] ?? null)) {
            $footer['kota'] = $filter['kota'];
        }

        return [
            'kop' => $report,
            'judulReport' => $judul,
            'filterTitle' => implode(' • ', $parts) ?: 'Semua Data',
            'footer' => $footer,
            'periodeText' => implode(' • ', $parts),
        ];
    }

    /** Logo kop sebagai data-URI (aman untuk DomPDF) atau null. */
    public static function logoDataUri(): ?string
    {
        return static::imageDataUri(config('website.logo'));
    }

    /**
     * Gambar report (header kiri/kanan, footer) sebagai data-URI (aman untuk DomPDF).
     * Terima path config seperti "storage/website/xxx.png", "/storage/...", atau URL.
     */
    public static function imageDataUri(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $file = public_path(ltrim(preg_replace('#^/?storage/#', '', $path), '/'));
        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            $file = public_path($path);
            if (! is_file($file)) {
                $file = public_path(ltrim($path, '/'));
            }
        }
        if (! is_file($file)) {
            return null;
        }
        $mime = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            default => 'image/png',
        };

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($file));
    }

    /** Data-URI gambar kiri, kanan, footer untuk view PDF. */
    public static function pdfImages(): array
    {
        $report = WebsiteSetting::report();

        return [
            'kopImgLeft' => static::imageDataUri($report['header_left'] ?? null)
                ?? static::logoDataUri(),
            'kopImgRight' => static::imageDataUri($report['header_image'] ?? null),
            'kopImgFooter' => static::imageDataUri($report['footer_image'] ?? null),
        ];
    }
}
