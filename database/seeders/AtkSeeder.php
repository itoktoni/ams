<?php

namespace Database\Seeders;

use App\Models\Gudang;
use App\Models\PergerakanStok;
use App\Models\StokSukuCadang;
use App\Models\SukuCadang;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class AtkSeeder extends Seeder
{
    public function run(): void
    {
        $gudang = Gudang::first();
        $vendor = Vendor::first();

        if (! $gudang) {
            $this->command->warn('Tidak ada gudang — jalankan AmsSeeder dulu.');
            return;
        }

        $items = [
            ['ATK-001', 'Kertas HVS A4 80gsm', 'Kertas putih 500 lbr/rim', 58000, 'rim', 10, 100],
            ['ATK-002', 'Kertas HVS F4 70gsm', 'Kertas Folio 500 lbr/rim', 62000, 'rim', 5, 80],
            ['ATK-003', 'Pulpen Biru', 'Bolpoin 0.5mm biru', 2500, 'pcs', 20, 300],
            ['ATK-004', 'Pulpen Hitam', 'Bolpoin 0.5mm hitam', 2500, 'pcs', 20, 300],
            ['ATK-005', 'Pensil 2B', 'Pensil kayu 2B', 2000, 'pcs', 20, 200],
            ['ATK-006', 'Penghapus Putih', 'Penghapus karet putih', 3000, 'pcs', 10, 100],
            ['ATK-007', 'Penggaris 30cm', 'Penggaris plastik bening 30cm', 5000, 'pcs', 10, 100],
            ['ATK-008', 'Stapler HD-10', 'Stapler kecil isi 10', 18000, 'pcs', 5, 50],
            ['ATK-009', 'Isi Staples No.10', 'Isi staples 1000 pcs/box', 4000, 'box', 10, 100],
            ['ATK-010', 'Clip Binder 25mm', 'Clip binder besi 25mm', 8000, 'box', 10, 100],
            ['ATK-011', 'Map Plastik Kancing', 'Map plastik kancing A4', 3500, 'pcs', 20, 200],
            ['ATK-012', 'Map Kertas Bufalo', 'Map kertas 275gsm warna', 1500, 'pcs', 30, 300],
            ['ATK-013', 'Amplop Coklat A4', 'Amplop coklat folio ukuran A4', 1200, 'pcs', 30, 500],
            ['ATK-014', 'Lakban Bening 2 inch', 'Lakban transparan 48mm x 50m', 9000, 'roll', 10, 80],
            ['ATK-015', 'Gunting Kantor', 'Gunting stainless 21cm', 12000, 'pcs', 5, 50],
            ['ATK-016', 'Cutter L500', 'Cutter besar + isi cadangan', 10000, 'pcs', 5, 50],
            ['ATK-017', 'Lem Stick 22g', 'Lem stick kertas 22 gram', 8000, 'pcs', 10, 100],
            ['ATK-018', 'Tinta Printer Hitam', 'Cartridge tinta hitam kompatibel', 95000, 'pcs', 2, 20],
            ['ATK-019', 'Tinta Printer Warna', 'Cartridge tinta warna kompatibel', 110000, 'pcs', 2, 20],
            ['ATK-020', 'Baterai AA', 'Baterai alkaline AA (isi 4)', 18000, 'pack', 10, 100],
            ['ATK-021', 'Spidol Whiteboard Hitam', 'Spidol whiteboard ujung bulat', 7000, 'pcs', 10, 100],
            ['ATK-022', 'Spidol Permanent Biru', 'Spidol permanent biru', 8000, 'pcs', 10, 100],
            ['ATK-023', 'Stabilo Kuning', 'Highlighter stabilo kuning', 6000, 'pcs', 10, 100],
            ['ATK-024', 'Note Pad 3x3 inch', 'Sticky notes 76x76mm', 7000, 'pack', 10, 100],
            ['ATK-025', 'Buku Tulis 58 Lembar', 'Buku tulis garis 58 lbr', 5000, 'pcs', 20, 200],
            ['ATK-026', 'Spiral Note A5', 'Buku catatan spiral A5', 12000, 'pcs', 10, 100],
            ['ATK-027', 'Kalkulator 12 Digit', 'Kalkulator meja 12 digit', 45000, 'pcs', 2, 20],
            ['ATK-028', 'Tinta Stempel Hitam', 'Tinta stempel cair 30ml hitam', 9000, 'botol', 5, 50],
            ['ATK-029', 'Stempel Tanggal', 'Stempel tanggal putar otomatis', 35000, 'pcs', 2, 20],
            ['ATK-030', 'Kabel Ties 20cm', 'Kabel ties hitam 20cm (100 pcs)', 12000, 'pack', 5, 50],
        ];

        $count = 0;
        foreach ($items as [$kode, $nama, $spek, $harga, $satuan, $min, $maks]) {
            if (SukuCadang::where('suku_cadang_kode', $kode)->exists()) {
                continue;
            }

            $sc = SukuCadang::create([
                'suku_cadang_kode' => $kode,
                'suku_cadang_nama' => $nama,
                'suku_cadang_spesifikasi' => $spek,
                'suku_cadang_harga' => $harga,
                'suku_cadang_id_gudang' => $gudang->gudang_id,
                'suku_cadang_id_vendor' => $vendor?->vendor_id,
                'suku_cadang_satuan' => $satuan,
                'suku_cadang_stok_minimum' => $min,
                'suku_cadang_stok_maksimum' => $maks,
            ]);

            StokSukuCadang::firstOrCreate(
                ['stok_suku_cadang_id_suku_cadang' => $sc->suku_cadang_id, 'stok_suku_cadang_id_gudang' => $gudang->gudang_id, 'stok_suku_cadang_bin' => '1'],
                ['stok_suku_cadang_jumlah' => rand(20, 80)]
            );

            PergerakanStok::create([
                'pergerakan_stok_id_suku_cadang' => $sc->suku_cadang_id,
                'pergerakan_stok_id_gudang' => $gudang->gudang_id,
                'pergerakan_stok_tipe' => 'masuk',
                'pergerakan_stok_jumlah' => rand(20, 80),
                'pergerakan_stok_referensi' => 'ATK-seed',
                'pergerakan_stok_catatan' => 'Stok awal ATK',
            ]);

            $count++;
        }

        $this->command->info("AtkSeeder: {$count} item ATK ditambahkan (".count($items)." total katalog, skip jika kode sudah ada).");
    }
}
