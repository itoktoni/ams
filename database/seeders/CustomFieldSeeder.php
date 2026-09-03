<?php

namespace Database\Seeders;

use App\Models\Aset;
use App\Models\KategoriAset;
use App\Models\LokasiAset;
use App\Models\LogStatusAset;
use App\Models\Vendor;
use App\Services\PenyusutanService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * CustomFieldSeeder
 *
 * Seeder khusus untuk fitur *Custom Field per Kategori Aset*:
 *   1. Mendefinisikan definisi custom field pada setiap kategori aset
 *      (format: [{label, type, options}] — key dihitung otomatis oleh mutator
 *      KategoriAset::asetKategoriCustomFields() via slugify(label)).
 *   2. Membuat aset dummy yang membawa nilai custom field (aset_custom_fields),
 *      dikunci ke key hasil slugify(label) agar cocok dengan definisi kategori.
 *
 * Sifat idempoten: kategori di-updateOrCreate by kode, aset di-updateOrCreate by kode,
 * sehingga seeder bisa dijalankan berulang kali (php artisan db:seed --class=CustomFieldSeeder)
 * tanpa membuat duplikat. Ledger penyusutan hanya dibuat saat aset baru (wasRecentlyCreated).
 */
class CustomFieldSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        // Lokasi & vendor acuan (firstOrCreate agar aman dijalankan mandiri)
        $lokPusat = LokasiAset::firstOrCreate(
            ['aset_lokasi_kode' => 'PST'],
            ['aset_lokasi_nama' => 'Kantor Pusat', 'aset_lokasi_alamat' => 'Jl. Merdeka 1', 'aset_lokasi_zona' => 'pusat', 'aset_lokasi_latitude' => -6.2, 'aset_lokasi_longitude' => 106.8]
        );
        $lokRuang = LokasiAset::firstOrCreate(
            ['aset_lokasi_kode' => 'SRV'],
            ['aset_lokasi_nama' => 'Ruang Server', 'aset_lokasi_parent_id' => $lokPusat->aset_lokasi_id, 'aset_lokasi_zona' => 'pusat']
        );
        $lokGudang = LokasiAset::firstOrCreate(
            ['aset_lokasi_kode' => 'GDC'],
            ['aset_lokasi_nama' => 'Gudang Cabang', 'aset_lokasi_alamat' => 'Jl. Industri 9', 'aset_lokasi_zona' => 'utara', 'aset_lokasi_latitude' => -6.1, 'aset_lokasi_longitude' => 106.9]
        );
        $vKend = Vendor::firstOrCreate(['vendor_kode' => 'V-001'], ['vendor_nama' => 'PT Mitra Teknik', 'vendor_telepon' => '021-111', 'vendor_kategori' => 'alat_berat', 'vendor_rating' => 4.5]);
        $vIt = Vendor::firstOrCreate(['vendor_kode' => 'V-003'], ['vendor_nama' => 'Toko Komputer Jaya', 'vendor_telepon' => '021-333', 'vendor_kategori' => 'it', 'vendor_rating' => 4.0]);
        $vMed = Vendor::firstOrCreate(['vendor_kode' => 'V-002'], ['vendor_nama' => 'CV Sinar Medika', 'vendor_telepon' => '021-222', 'vendor_kategori' => 'medis', 'vendor_rating' => 4.2]);

        // ---------------------------------------------------------------------
        // 1) Definisi custom field per kategori (parent didaftarkan lebih dulu)
        //    type: text | number | date | textarea | select
        // ---------------------------------------------------------------------
        $categoryDefs = [
            // Parent
            'KEND' => [
                'nama' => 'Kendaraan', 'parent' => null, 'masa_manfaat' => 60, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'No Polisi', 'type' => 'text'],
                    ['label' => 'No STNK', 'type' => 'text'],
                    ['label' => 'Tanggal Expired STNK', 'type' => 'date'],
                    ['label' => 'No KIR', 'type' => 'text'],
                    ['label' => 'Tanggal Expired KIR', 'type' => 'date'],
                    ['label' => 'No BPKB', 'type' => 'text'],
                    ['label' => 'Tanggal Pajak', 'type' => 'date'],
                    ['label' => 'No Rangka', 'type' => 'text'],
                    ['label' => 'No Mesin', 'type' => 'text'],
                    ['label' => 'Warna', 'type' => 'text'],
                    ['label' => 'Bahan Bakar', 'type' => 'select', 'options' => 'Bensin,Solar,Listrik,Hybrid'],
                ],
            ],
            'IT' => [
                'nama' => 'Peralatan IT', 'parent' => null, 'masa_manfaat' => 48, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Garansi', 'type' => 'text'],
                    ['label' => 'Spesifikasi', 'type' => 'textarea'],
                    ['label' => 'Tahun Beli', 'type' => 'number'],
                ],
            ],
            'SW' => [
                'nama' => 'Software', 'parent' => null, 'masa_manfaat' => 36, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Nama Lisensi', 'type' => 'text'],
                    ['label' => 'Versi', 'type' => 'text'],
                    ['label' => 'Jumlah Lisensi', 'type' => 'number'],
                    ['label' => 'Tanggal Expired', 'type' => 'date'],
                    ['label' => 'Vendor', 'type' => 'text'],
                ],
            ],
            'MED' => [
                'nama' => 'Peralatan Medis', 'parent' => null, 'masa_manfaat' => 120, 'metode' => 'saldo_menurun', 'kelompok' => 'A',
                'fields' => [
                    ['label' => 'Merk', 'type' => 'text'],
                    ['label' => 'Tahun Produksi', 'type' => 'number'],
                    ['label' => 'Kalibrasi Terakhir', 'type' => 'date'],
                    ['label' => 'Rentang', 'type' => 'select', 'options' => 'Dewasa,Anak,Bayi'],
                ],
            ],
            // Child
            'MOB' => [
                'nama' => 'Mobil Operasional', 'parent' => 'KEND', 'masa_manfaat' => 60, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'No Polisi', 'type' => 'text'],
                    ['label' => 'No STNK', 'type' => 'text'],
                    ['label' => 'Tanggal Expired STNK', 'type' => 'date'],
                    ['label' => 'No KIR', 'type' => 'text'],
                    ['label' => 'Tanggal Expired KIR', 'type' => 'date'],
                    ['label' => 'No BPKB', 'type' => 'text'],
                    ['label' => 'Tanggal Pajak', 'type' => 'date'],
                    ['label' => 'No Rangka', 'type' => 'text'],
                    ['label' => 'No Mesin', 'type' => 'text'],
                    ['label' => 'Warna', 'type' => 'text'],
                    ['label' => 'Bahan Bakar', 'type' => 'select', 'options' => 'Bensin,Solar,Listrik,Hybrid'],
                ],
            ],
            'MTR' => [
                'nama' => 'Motor', 'parent' => 'KEND', 'masa_manfaat' => 60, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'No Polisi', 'type' => 'text'],
                    ['label' => 'No STNK', 'type' => 'text'],
                    ['label' => 'Tanggal Expired STNK', 'type' => 'date'],
                    ['label' => 'No BPKB', 'type' => 'text'],
                    ['label' => 'Tanggal Pajak', 'type' => 'date'],
                    ['label' => 'No Rangka', 'type' => 'text'],
                    ['label' => 'No Mesin', 'type' => 'text'],
                    ['label' => 'CC', 'type' => 'number'],
                    ['label' => 'Warna', 'type' => 'text'],
                ],
            ],
            'PC' => [
                'nama' => 'Komputer', 'parent' => 'IT', 'masa_manfaat' => 48, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Processor', 'type' => 'text'],
                    ['label' => 'RAM GB', 'type' => 'number'],
                    ['label' => 'Storage', 'type' => 'text'],
                    ['label' => 'Sistem Operasi', 'type' => 'text'],
                    ['label' => 'No Inventaris', 'type' => 'text'],
                ],
            ],
            'LAP' => [
                'nama' => 'Laptop', 'parent' => 'IT', 'masa_manfaat' => 48, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Processor', 'type' => 'text'],
                    ['label' => 'RAM GB', 'type' => 'number'],
                    ['label' => 'Storage', 'type' => 'text'],
                    ['label' => 'Sistem Operasi', 'type' => 'text'],
                    ['label' => 'Baterai Wh', 'type' => 'number'],
                ],
            ],
            'MS' => [
                'nama' => 'Lisensi Microsoft', 'parent' => 'SW', 'masa_manfaat' => 36, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Product Key', 'type' => 'text'],
                    ['label' => 'Edition', 'type' => 'select', 'options' => 'Windows 11,Office 2021,Office 365,Azure,SQL Server'],
                    ['label' => 'Jumlah Seat', 'type' => 'number'],
                    ['label' => 'Tanggal Aktivasi', 'type' => 'date'],
                    ['label' => 'Tanggal Expired', 'type' => 'date'],
                ],
            ],
            // Standalone
            'TANAH' => [
                'nama' => 'Tanah', 'parent' => null, 'masa_manfaat' => 120, 'metode' => 'garis_lurus', 'kelompok' => 'A',
                'fields' => [
                    ['label' => 'Luas m2', 'type' => 'number'],
                    ['label' => 'Alamat', 'type' => 'textarea'],
                    ['label' => 'No Sertifikat', 'type' => 'text'],
                    ['label' => 'Jenis Hak', 'type' => 'select', 'options' => 'Hak Milik,HGB,HGU,Hak Pengelolaan,Sewa'],
                    ['label' => 'Tahun Sertifikat', 'type' => 'number'],
                    ['label' => 'NJOP', 'type' => 'number'],
                ],
            ],
            'GED' => [
                'nama' => 'Gedung', 'parent' => null, 'masa_manfaat' => 120, 'metode' => 'garis_lurus', 'kelompok' => 'A',
                'fields' => [
                    ['label' => 'Luas m2', 'type' => 'number'],
                    ['label' => 'Jumlah Lantai', 'type' => 'number'],
                    ['label' => 'Alamat', 'type' => 'textarea'],
                    ['label' => 'No IMB', 'type' => 'text'],
                    ['label' => 'Tahun Dibangun', 'type' => 'number'],
                ],
            ],
            'MESIN' => [
                'nama' => 'Mesin / Alat Berat', 'parent' => null, 'masa_manfaat' => 120, 'metode' => 'garis_lurus', 'kelompok' => 'A',
                'fields' => [
                    ['label' => 'Merk', 'type' => 'text'],
                    ['label' => 'Kapasitas', 'type' => 'text'],
                    ['label' => 'Tahun Produksi', 'type' => 'number'],
                    ['label' => 'Jam Operasi', 'type' => 'number'],
                    ['label' => 'Suku Cadang Utama', 'type' => 'text'],
                ],
            ],
            'AC' => [
                'nama' => 'AC / Pendingin Ruangan', 'parent' => null, 'masa_manfaat' => 60, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Merk', 'type' => 'text'],
                    ['label' => 'PK', 'type' => 'number'],
                    ['label' => 'Jenis', 'type' => 'select', 'options' => 'Split,Dinding,Standing,Cassette'],
                    ['label' => 'Tahun Beli', 'type' => 'number'],
                    ['label' => 'Refrigeran', 'type' => 'text'],
                ],
            ],
            'CCTV' => [
                'nama' => 'CCTV / Security', 'parent' => null, 'masa_manfaat' => 60, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Merk', 'type' => 'text'],
                    ['label' => 'Jumlah Kanal', 'type' => 'number'],
                    ['label' => 'Resolusi', 'type' => 'select', 'options' => '2MP,4MP,5MP,4K'],
                    ['label' => 'Tahun Beli', 'type' => 'number'],
                ],
            ],
            'FURNITUR' => [
                'nama' => 'Furnitur', 'parent' => null, 'masa_manfaat' => 60, 'metode' => 'garis_lurus', 'kelompok' => 'B',
                'fields' => [
                    ['label' => 'Jenis', 'type' => 'text'],
                    ['label' => 'Bahan', 'type' => 'select', 'options' => 'Kayu,Aluminium,Plastik,Besi'],
                    ['label' => 'Jumlah', 'type' => 'number'],
                    ['label' => 'Ruangan', 'type' => 'text'],
                ],
            ],
        ];

        $categories = [];
        foreach ($categoryDefs as $kode => $def) {
            $kat = KategoriAset::updateOrCreate(
                ['aset_kategori_kode' => $kode],
                [
                    'aset_kategori_nama' => $def['nama'],
                    'aset_kategori_masa_manfaat' => $def['masa_manfaat'],
                    'aset_kategori_metode_penyusutan' => $def['metode'],
                    'aset_kategori_custom_fields' => $def['fields'],
                ]
            );
            $kat->refresh();

            // Bangun pemetaan label => key (key dihitung oleh mutator slugify)
            $keyMap = [];
            foreach (($kat->aset_kategori_custom_fields ?? []) as $f) {
                $keyMap[$f['label']] = $f['key'];
            }
            $categories[$kode] = ['model' => $kat, 'keys' => $keyMap];
        }

        // ---------------------------------------------------------------------
        // 2) Aset dummy dengan nilai custom field (dikunci ke key per kategori)
        //    [kode, nama, kodeKategori, lokasi, vendor, harga, masaManfaat,
        //     metodePenyusutan, status, kondisi, [label => nilai]]
        // ---------------------------------------------------------------------
        $assetData = [
            ['CF-001', 'Tanah Kavling B', 'TANAH', $lokPusat, $vKend, 1500000000, 120, 'garis_lurus', 'aktif', 'baik', [
                'Luas m2' => 750, 'Alamat' => 'Jl. Sudirman 10', 'No Sertifikat' => 'SHM-002',
                'Jenis Hak' => 'HGB', 'Tahun Sertifikat' => 2020, 'NJOP' => 1300000000,
            ]],
            ['CF-002', 'Tanah Sawah', 'TANAH', $lokGudang, $vKend, 800000000, 120, 'garis_lurus', 'aktif', 'baik', [
                'Luas m2' => 2000, 'Alamat' => 'Jl. Sawah 5', 'No Sertifikat' => 'SHM-003',
                'Jenis Hak' => 'Hak Milik', 'Tahun Sertifikat' => 2015, 'NJOP' => 700000000,
            ]],
            ['CF-003', 'Gedung Pabrik', 'GED', $lokGudang, $vKend, 3000000000, 120, 'garis_lurus', 'aktif', 'baik', [
                'Luas m2' => 2500, 'Jumlah Lantai' => 2, 'Alamat' => 'Jl. Industri 9',
                'No IMB' => 'IMB-300', 'Tahun Dibangun' => 2012,
            ]],
            ['CF-004', 'Gedung Gudang', 'GED', $lokGudang, $vKend, 900000000, 120, 'garis_lurus', 'aktif', 'kurang_baik', [
                'Luas m2' => 800, 'Jumlah Lantai' => 1, 'Alamat' => 'Jl. Industri 9',
                'No IMB' => 'IMB-301', 'Tahun Dibangun' => 2008,
            ]],
            ['CF-005', 'Toyota Avanza', 'MOB', $lokPusat, $vKend, 230000000, 60, 'garis_lurus', 'aktif', 'baik', [
                'No Polisi' => 'B 1234 XYZ', 'No STNK' => 'STNK-AV-001', 'Tanggal Expired STNK' => Carbon::today()->addMonths(10)->format('Y-m-d'),
                'No KIR' => 'KIR-AV-001', 'Tanggal Expired KIR' => Carbon::today()->addMonths(5)->format('Y-m-d'),
                'No BPKB' => 'BPKB-AV-001', 'Tanggal Pajak' => Carbon::today()->addMonths(10)->format('Y-m-d'),
                'No Rangka' => 'MHF1XA1XXXXX', 'No Mesin' => '1NZFE1XXXXX', 'Warna' => 'Silver', 'Bahan Bakar' => 'Bensin',
            ]],
            ['CF-006', 'Mitsubishi L300', 'MOB', $lokGudang, $vKend, 280000000, 60, 'garis_lurus', 'dipinjam', 'baik', [
                'No Polisi' => 'B 5678 ABC', 'No STNK' => 'STNK-L3-001', 'Tanggal Expired STNK' => Carbon::today()->addMonths(8)->format('Y-m-d'),
                'No KIR' => 'KIR-L3-001', 'Tanggal Expired KIR' => Carbon::today()->addMonths(2)->format('Y-m-d'),
                'No BPKB' => 'BPKB-L3-001', 'Tanggal Pajak' => Carbon::today()->addMonths(8)->format('Y-m-d'),
                'No Rangka' => 'MHK2XA2XXXXX', 'No Mesin' => '4D56XXXXX', 'Warna' => 'Putih', 'Bahan Bakar' => 'Solar',
            ]],
            ['CF-007', 'Honda Beat', 'MTR', $lokPusat, $vKend, 19000000, 60, 'garis_lurus', 'aktif', 'baik', [
                'No Polisi' => 'B 1111 BEA', 'No STNK' => 'STNK-BT-001', 'Tanggal Expired STNK' => Carbon::today()->addMonths(12)->format('Y-m-d'),
                'No BPKB' => 'BPKB-BT-001', 'Tanggal Pajak' => Carbon::today()->addMonths(12)->format('Y-m-d'),
                'No Rangka' => 'MH1XA3XXXXX', 'No Mesin' => 'JF1XXXXX', 'CC' => 110, 'Warna' => 'Merah',
            ]],
            ['CF-008', 'Yamaha NMAX', 'MTR', $lokGudang, $vKend, 29000000, 60, 'garis_lurus', 'aktif', 'baik', [
                'No Polisi' => 'B 2222 NMA', 'No STNK' => 'STNK-NM-001', 'Tanggal Expired STNK' => Carbon::today()->addMonths(6)->format('Y-m-d'),
                'No BPKB' => 'BPKB-NM-001', 'Tanggal Pajak' => Carbon::today()->addMonths(6)->format('Y-m-d'),
                'No Rangka' => 'MH3XA4XXXXX', 'No Mesin' => 'BK1XXXXX', 'CC' => 155, 'Warna' => 'Hitam',
            ]],
            ['CF-009', 'PC Rak Server', 'PC', $lokRuang, $vIt, 25000000, 48, 'garis_lurus', 'aktif', 'baik', [
                'Processor' => 'Xeon E-2336', 'RAM GB' => 64, 'Storage' => '2TB SSD',
                'Sistem Operasi' => 'Ubuntu 22.04', 'No Inventaris' => 'INV-PC-010',
            ]],
            ['CF-010', 'PC Kantor', 'PC', $lokPusat, $vIt, 12000000, 48, 'garis_lurus', 'rusak', 'rusak', [
                'Processor' => 'Intel i3-12100', 'RAM GB' => 8, 'Storage' => '256GB SSD',
                'Sistem Operasi' => 'Windows 10', 'No Inventaris' => 'INV-PC-011',
            ]],
            ['CF-011', 'Laptop Dell', 'LAP', $lokPusat, $vIt, 19000000, 48, 'garis_lurus', 'aktif', 'baik', [
                'Processor' => 'Intel i7-1265U', 'RAM GB' => 16, 'Storage' => '512GB SSD',
                'Sistem Operasi' => 'Windows 11 Pro', 'Baterai Wh' => 54,
            ]],
            ['CF-012', 'Laptop MacBook', 'LAP', $lokRuang, $vIt, 28000000, 48, 'garis_lurus', 'aktif', 'baik', [
                'Processor' => 'Apple M2', 'RAM GB' => 16, 'Storage' => '512GB SSD',
                'Sistem Operasi' => 'macOS 14', 'Baterai Wh' => 70,
            ]],
            ['CF-013', 'Lisensi Windows 11', 'MS', $lokRuang, $vIt, 9000000, 36, 'garis_lurus', 'aktif', 'baik', [
                'Product Key' => 'XXXXX-XXXXX-XXXXX-XXXXX-XXXXX', 'Edition' => 'Windows 11',
                'Jumlah Seat' => 50, 'Tanggal Aktivasi' => '2025-09-01', 'Tanggal Expired' => '2026-09-01',
            ]],
            ['CF-014', 'Lisensi SQL Server', 'MS', $lokRuang, $vIt, 45000000, 36, 'garis_lurus', 'aktif', 'baik', [
                'Product Key' => 'YYYYY-YYYYY-YYYYY-YYYYY-YYYYY', 'Edition' => 'SQL Server',
                'Jumlah Seat' => 10, 'Tanggal Aktivasi' => '2025-01-15', 'Tanggal Expired' => '2026-01-15',
            ]],
            ['CF-015', 'AutoCAD', 'SW', $lokRuang, $vIt, 35000000, 36, 'garis_lurus', 'aktif', 'baik', [
                'Nama Lisensi' => 'AutoCAD', 'Versi' => '2024', 'Jumlah Lisensi' => 5,
                'Tanggal Expired' => '2026-12-31', 'Vendor' => 'Autodesk',
            ]],
            ['CF-016', 'Adobe Creative Cloud', 'SW', $lokRuang, $vIt, 55000000, 36, 'garis_lurus', 'aktif', 'baik', [
                'Nama Lisensi' => 'Adobe CC', 'Versi' => '2025', 'Jumlah Lisensi' => 3,
                'Tanggal Expired' => '2026-08-01', 'Vendor' => 'Adobe',
            ]],
            ['CF-017', 'Mesin CNC', 'MESIN', $lokGudang, $vKend, 750000000, 120, 'garis_lurus', 'aktif', 'baik', [
                'Merk' => 'Haas', 'Kapasitas' => '1000 kg', 'Tahun Produksi' => 2019,
                'Jam Operasi' => 12000, 'Suku Cadang Utama' => 'Spindle',
            ]],
            ['CF-018', 'Genset', 'MESIN', $lokGudang, $vKend, 45000000, 60, 'garis_lurus', 'aktif', 'kurang_baik', [
                'Merk' => 'Honda', 'Kapasitas' => '5000 W', 'Tahun Produksi' => 2021,
                'Jam Operasi' => 800, 'Suku Cadang Utama' => 'Karburator',
            ]],
            ['CF-019', 'AC Split', 'AC', $lokPusat, $vKend, 7000000, 60, 'garis_lurus', 'aktif', 'baik', [
                'Merk' => 'Daikin', 'PK' => 1.5, 'Jenis' => 'Split', 'Tahun Beli' => 2023, 'Refrigeran' => 'R32',
            ]],
            ['CF-020', 'CCTV 8 Kanal', 'CCTV', $lokPusat, $vIt, 8500000, 60, 'garis_lurus', 'aktif', 'baik', [
                'Merk' => 'Hikvision', 'Jumlah Kanal' => 8, 'Resolusi' => '4MP', 'Tahun Beli' => 2024,
            ]],
            ['CF-021', 'Meja Kerja', 'FURNITUR', $lokPusat, $vKend, 1500000, 60, 'garis_lurus', 'aktif', 'baik', [
                'Jenis' => 'Meja Kerja', 'Bahan' => 'Kayu', 'Jumlah' => 20, 'Ruangan' => 'Ruang Rapat',
            ]],
            ['CF-022', 'Kursi Kantor', 'FURNITUR', $lokPusat, $vKend, 900000, 60, 'garis_lurus', 'aktif', 'baik', [
                'Jenis' => 'Kursi Kantor', 'Bahan' => 'Aluminium', 'Jumlah' => 30, 'Ruangan' => 'Ruang Operasional',
            ]],
            ['CF-023', 'ECG Machine', 'MED', $lokPusat, $vMed, 32000000, 120, 'saldo_menurun', 'aktif', 'baik', [
                'Merk' => 'Philips', 'Tahun Produksi' => 2022, 'Kalibrasi Terakhir' => '2026-01-10', 'Rentang' => 'Dewasa',
            ]],
            ['CF-024', 'Infus Pump', 'MED', $lokPusat, $vMed, 9000000, 120, 'saldo_menurun', 'aktif', 'baik', [
                'Merk' => 'B. Braun', 'Tahun Produksi' => 2021, 'Kalibrasi Terakhir' => '2025-11-20', 'Rentang' => 'Anak',
            ]],
        ];

        $penyusutan = new PenyusutanService;
        $created = 0;
        foreach ($assetData as [$kode, $nama, $katKode, $lok, $ven, $harga, $bulan, $metode, $status, $kondisi, $cfByLabel]) {
            $cat = $categories[$katKode] ?? null;
            if (! $cat) {
                continue;
            }

            // Map nilai by label -> key (slugify) agar cocok dengan definisi kategori
            $cf = [];
            foreach ($cfByLabel as $label => $val) {
                if (isset($cat['keys'][$label])) {
                    $cf[$cat['keys'][$label]] = (string) $val;
                }
            }

            $mulai = Carbon::today()->subMonths(rand(1, 18))->startOfMonth();
            $aset = Aset::updateOrCreate(
                ['aset_kode' => $kode],
                [
                    'aset_nama' => $nama,
                    'aset_id_kategori' => $cat['model']->aset_kategori_id,
                    'aset_id_lokasi' => $lok->aset_lokasi_id,
                    'aset_id_vendor' => $ven->vendor_id,
                    'aset_id_penanggung_jawab' => $userId,
                    'aset_merek' => 'Brand',
                    'aset_model' => 'MDL-'.substr($kode, 3),
                    'aset_nomor_seri' => 'SN-'.rand(1000, 9999),
                    'aset_tanggal_perolehan' => $mulai,
                    'aset_harga_perolehan' => $harga,
                    'aset_nilai_sisa' => round($harga * 0.1, 2),
                    'aset_masa_manfaat' => $bulan,
                    'aset_metode_penyusutan' => $metode,
                    'aset_tanggal_mulai_susut' => $mulai,
                    'aset_status' => $status,
                    'aset_kondisi' => $kondisi,
                    'aset_custom_fields' => $cf,
                    'aset_jam_pakai' => rand(0, 8000),
                    'aset_catatan' => 'Custom field dummy',
                ]
            );

            if ($aset->wasRecentlyCreated) {
                $penyusutan->jalankan($aset);
                LogStatusAset::create([
                    'log_status_aset_id_aset' => $aset->aset_id,
                    'log_status_aset_status_dari' => 'baru',
                    'log_status_aset_status_ke' => $status,
                    'log_status_aset_actor' => $userId,
                    'log_status_aset_catatan' => 'Status awal (CustomFieldSeeder)',
                ]);
                $created++;
            }
        }

        $this->command->info('CustomFieldSeeder: '.count($categories).' kategori (definisi custom field) + '.$created.' aset dummy baru (total CF-* = '.count($assetData).').');
    }
}
