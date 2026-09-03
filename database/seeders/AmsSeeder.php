<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Aset;
use App\Models\BatchTiket;
use App\Models\DaftarTunggu;
use App\Models\DokumenAset;
use App\Models\Faktur;
use App\Models\Gudang;
use App\Models\JadwalService;
use App\Models\KategoriAset;
use App\Models\KelompokPenyusutan;
use App\Models\LogStatusAset;
use App\Models\LokasiAset;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\Peminjaman;
use App\Models\PenawaranPenjualan;
use App\Models\Penerimaan;
use App\Models\Penghapusan;
use App\Models\PenghapusanKomponen;
use App\Models\PenjualanAset;
use App\Models\PergerakanStok;
use App\Models\Perpindahan;
use App\Models\Persetujuan;
use App\Models\PesananItem;
use App\Models\PesananPembelian;
use App\Models\ReputasiPeminjam;
use App\Models\RiwayatService;
use App\Models\StokSukuCadang;
use App\Models\SukuCadang;
use App\Models\Teknisi;
use App\Models\TemplateService;
use App\Models\TemplateServiceItem;
use App\Models\Tiket;
use App\Models\Vendor;
use App\Services\PenyusutanService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmsSeeder extends Seeder
{
    public function run(): void
    {
        if (Aset::count() > 0) {
            $this->command->warn('Data AMS sudah ada, seeder dilewati.');

            return;
        }

        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        // ---- Reference: kelompok penyusutan — PMK 72/2023 (https://pajak.go.id/id/penyusutan-dan-amortisasi) ----
        $kelompokDjp = [
            ['DJP-K1','Kelompok 1 — Bukan Bangunan (4 Tahun)',4,'garis_lurus',25,'GL 25% | SM 50%. Contoh: mobil, motor, AC, komputer, printer'],
            ['DJP-K2','Kelompok 2 — Bukan Bangunan (8 Tahun)',8,'garis_lurus',12.5,'GL 12,5% | SM 25%. Contoh: mebel logam, mesin ringan, kapal kecil'],
            ['DJP-K3','Kelompok 3 — Bukan Bangunan (16 Tahun)',16,'garis_lurus',6.25,'GL 6,25% | SM 12,5%. DEFAULT jika tak di lampiran PMK 72'],
            ['DJP-K4','Kelompok 4 — Bukan Bangunan (20 Tahun)',20,'garis_lurus',5,'GL 5% | SM 10%. Contoh: kapal besar, lokomotif'],
            ['DJP-BP','Bangunan Permanen (20 Tahun)',20,'garis_lurus',5,'Hanya GL 5% — SM tidak diperkenankan'],
            ['DJP-BT','Bangunan Tidak Permanen (10 Tahun)',10,'garis_lurus',10,'Hanya GL 10% — barak kayu/semi permanen'],
            ['DJP-A1','Tak Berwujud Kelompok 1 (4 Tahun)',4,'garis_lurus',25,'Amortisasi GL 25% | SM 50%. Software aplikasi khusus'],
            ['DJP-A2','Tak Berwujud Kelompok 2 (8 Tahun)',8,'garis_lurus',12.5,'GL 12,5% | SM 25%'],
            ['DJP-A3','Tak Berwujud Kelompok 3 (16 Tahun)',16,'garis_lurus',6.25,'GL 6,25% | SM 12,5%'],
            ['DJP-A4','Tak Berwujud Kelompok 4 (20 Tahun)',20,'garis_lurus',5,'GL 5% | SM 10%. Jika >20thn pakai Kel 4 atau masa sebenarnya taat asas'],
        ];
        $kpMap = [];
        foreach ($kelompokDjp as [$kode,$nama,$masa,$metode,$tarif,$ket]) {
            $kpMap[$kode] = KelompokPenyusutan::create([
                'kelompok_penyusutan_kode' => $kode, 'kelompok_penyusutan_nama' => $nama,
                'kelompok_penyusutan_masa_manfaat' => $masa, 'kelompok_penyusutan_metode' => $metode,
                'kelompok_penyusutan_tarif' => $tarif, 'kelompok_penyusutan_keterangan' => $ket,
            ]);
        }
        $kp1 = $kpMap['DJP-K1']; $kp2 = $kpMap['DJP-K2']; $kp3 = $kpMap['DJP-A1'];

        // ---- Reference: kategori aset (tree) ----
        $katKend = KategoriAset::create([
            'aset_kategori_nama' => 'Kendaraan', 'aset_kategori_kode' => 'KEND',
            'aset_kategori_masa_manfaat' => 60, 'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_keterangan' => 'Kendaraan dinas',
        ]);
        $katMobil = KategoriAset::create([
            'aset_kategori_nama' => 'Mobil Operasional', 'aset_kategori_kode' => 'MOB',
            'aset_kategori_masa_manfaat' => 60,
            'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'No STNK', 'type' => 'text', 'options' => ''],
                ['label' => 'No KIR', 'type' => 'text', 'options' => ''],
                ['label' => 'No Rangka', 'type' => 'text', 'options' => ''],
                ['label' => 'No Mesin', 'type' => 'text', 'options' => ''],
                ['label' => 'Warna', 'type' => 'text', 'options' => ''],
                ['label' => 'Bahan Bakar', 'type' => 'select', 'options' => 'Bensin,Solar,Listrik,Hybrid'],
            ],
        ]);
        $katMedis = KategoriAset::create([
            'aset_kategori_nama' => 'Peralatan Medis', 'aset_kategori_kode' => 'MED',
            'aset_kategori_masa_manfaat' => 120, 'aset_kategori_metode_penyusutan' => 'saldo_menurun',
        ]);
        $katIt = KategoriAset::create([
            'aset_kategori_nama' => 'Peralatan IT', 'aset_kategori_kode' => 'IT',
            'aset_kategori_masa_manfaat' => 48, 'aset_kategori_metode_penyusutan' => 'garis_lurus',
        ]);

        // ---- Reference: kategori aset lainnya (dengan custom field) ----
        $katTanah = KategoriAset::create([
            'aset_kategori_nama' => 'Tanah', 'aset_kategori_kode' => 'TANAH',
            'aset_kategori_masa_manfaat' => 120, 'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'Luas m2', 'type' => 'number', 'options' => ''],
                ['label' => 'Alamat', 'type' => 'textarea', 'options' => ''],
                ['label' => 'No Sertifikat', 'type' => 'text', 'options' => ''],
                ['label' => 'Jenis Hak', 'type' => 'select', 'options' => 'Hak Milik,HGB,HGU,Sewa'],
                ['label' => 'Tahun Sertifikat', 'type' => 'number', 'options' => ''],
                ['label' => 'NJOP', 'type' => 'number', 'options' => ''],
            ],
        ]);
        $katGedung = KategoriAset::create([
            'aset_kategori_nama' => 'Gedung', 'aset_kategori_kode' => 'GED',
            'aset_kategori_masa_manfaat' => 120, 'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'Luas m2', 'type' => 'number', 'options' => ''],
                ['label' => 'Jumlah Lantai', 'type' => 'number', 'options' => ''],
                ['label' => 'Alamat', 'type' => 'textarea', 'options' => ''],
                ['label' => 'No IMB', 'type' => 'text', 'options' => ''],
                ['label' => 'Tahun Dibangun', 'type' => 'number', 'options' => ''],
            ],
        ]);
        $katSoftware = KategoriAset::create([
            'aset_kategori_nama' => 'Software', 'aset_kategori_kode' => 'SW',
            'aset_kategori_masa_manfaat' => 36, 'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'Nama Lisensi', 'type' => 'text', 'options' => ''],
                ['label' => 'Versi', 'type' => 'text', 'options' => ''],
                ['label' => 'Jumlah Lisensi', 'type' => 'number', 'options' => ''],
                ['label' => 'Tanggal Expired', 'type' => 'date', 'options' => ''],
                ['label' => 'Vendor', 'type' => 'text', 'options' => ''],
            ],
        ]);
        $katMsLicense = KategoriAset::create([
            'aset_kategori_nama' => 'Lisensi Microsoft', 'aset_kategori_kode' => 'MS',
            'aset_kategori_masa_manfaat' => 36,
            'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'Product Key', 'type' => 'text', 'options' => ''],
                ['label' => 'Edition', 'type' => 'select', 'options' => 'Windows 11,Office 2021,Office 365,Azure,SQL Server'],
                ['label' => 'Jumlah Seat', 'type' => 'number', 'options' => ''],
                ['label' => 'Tanggal Aktivasi', 'type' => 'date', 'options' => ''],
                ['label' => 'Tanggal Expired', 'type' => 'date', 'options' => ''],
            ],
        ]);
        $katKomputer = KategoriAset::create([
            'aset_kategori_nama' => 'Komputer', 'aset_kategori_kode' => 'PC',
            'aset_kategori_masa_manfaat' => 48,
            'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'Processor', 'type' => 'text', 'options' => ''],
                ['label' => 'RAM GB', 'type' => 'number', 'options' => ''],
                ['label' => 'Storage', 'type' => 'text', 'options' => ''],
                ['label' => 'Sistem Operasi', 'type' => 'text', 'options' => ''],
                ['label' => 'No Inventaris', 'type' => 'text', 'options' => ''],
            ],
        ]);
        $katLaptop = KategoriAset::create([
            'aset_kategori_nama' => 'Laptop', 'aset_kategori_kode' => 'LAP',
            'aset_kategori_masa_manfaat' => 48,
            'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'Processor', 'type' => 'text', 'options' => ''],
                ['label' => 'RAM GB', 'type' => 'number', 'options' => ''],
                ['label' => 'Storage', 'type' => 'text', 'options' => ''],
                ['label' => 'Sistem Operasi', 'type' => 'text', 'options' => ''],
                ['label' => 'Baterai Wh', 'type' => 'number', 'options' => ''],
            ],
        ]);
        $katMotor = KategoriAset::create([
            'aset_kategori_nama' => 'Motor', 'aset_kategori_kode' => 'MTR',
            'aset_kategori_masa_manfaat' => 60,
            'aset_kategori_metode_penyusutan' => 'garis_lurus',
            'aset_kategori_custom_fields' => [
                ['label' => 'No STNK', 'type' => 'text', 'options' => ''],
                ['label' => 'No Rangka', 'type' => 'text', 'options' => ''],
                ['label' => 'No Mesin', 'type' => 'text', 'options' => ''],
                ['label' => 'CC', 'type' => 'number', 'options' => ''],
                ['label' => 'Warna', 'type' => 'text', 'options' => ''],
            ],
        ]);

        // ---- Reference: lokasi (tree) ----
        $lokPusat = LokasiAset::create([
            'aset_lokasi_nama' => 'Kantor Pusat', 'aset_lokasi_kode' => 'PST',
            'aset_lokasi_alamat' => 'Jl. Merdeka 1', 'aset_lokasi_zona' => 'pusat',
            'aset_lokasi_latitude' => -6.2, 'aset_lokasi_longitude' => 106.8,
        ]);
        $lokRuang = LokasiAset::create([
            'aset_lokasi_nama' => 'Ruang Server', 'aset_lokasi_kode' => 'SRV',
            'aset_lokasi_parent_id' => $lokPusat->aset_lokasi_id, 'aset_lokasi_zona' => 'pusat',
        ]);
        $lokGudang = LokasiAset::create([
            'aset_lokasi_nama' => 'Gudang Cabang', 'aset_lokasi_kode' => 'GDC',
            'aset_lokasi_alamat' => 'Jl. Industri 9', 'aset_lokasi_zona' => 'utara',
            'aset_lokasi_latitude' => -6.1, 'aset_lokasi_longitude' => 106.9,
        ]);

        // ---- Vendor ----
        $v1 = Vendor::create(['vendor_kode' => 'V-001', 'vendor_nama' => 'PT Mitra Teknik', 'vendor_telepon' => '021-111', 'vendor_kategori' => 'alat_berat', 'vendor_rating' => 4.5]);
        $v2 = Vendor::create(['vendor_kode' => 'V-002', 'vendor_nama' => 'CV Sinar Medika', 'vendor_telepon' => '021-222', 'vendor_kategori' => 'medis', 'vendor_rating' => 4.2]);
        $v3 = Vendor::create(['vendor_kode' => 'V-003', 'vendor_nama' => 'Toko Komputer Jaya', 'vendor_telepon' => '021-333', 'vendor_kategori' => 'it', 'vendor_rating' => 4.0]);

        // ---- Gudang ----
        $g1 = Gudang::create(['gudang_kode' => 'G-01', 'gudang_nama' => 'Gudang Utama', 'gudang_id_lokasi' => $lokPusat->aset_lokasi_id, 'gudang_alamat' => 'Jl. Merdeka 1']);
        $g2 = Gudang::create(['gudang_kode' => 'G-02', 'gudang_nama' => 'Gudang Cabang', 'gudang_id_lokasi' => $lokGudang->aset_lokasi_id]);

        // ---- Suku cadang ----
        $sc1 = SukuCadang::create(['suku_cadang_kode' => 'SC-001', 'suku_cadang_nama' => 'Filter Oli', 'suku_cadang_harga' => 50000, 'suku_cadang_id_gudang' => $g1->gudang_id, 'suku_cadang_stok_minimum' => 10, 'suku_cadang_stok_maksimum' => 100, 'suku_cadang_satuan' => 'pcs']);
        $sc2 = SukuCadang::create(['suku_cadang_kode' => 'SC-002', 'suku_cadang_nama' => 'Ban Cadangan', 'suku_cadang_harga' => 400000, 'suku_cadang_id_gudang' => $g1->gudang_id, 'suku_cadang_stok_minimum' => 4, 'suku_cadang_stok_maksimum' => 20, 'suku_cadang_satuan' => 'pcs']);
        $sc3 = SukuCadang::create(['suku_cadang_kode' => 'SC-003', 'suku_cadang_nama' => 'Kabel HDMI', 'suku_cadang_harga' => 75000, 'suku_cadang_id_gudang' => $g2->gudang_id, 'suku_cadang_stok_minimum' => 5, 'suku_cadang_stok_maksimum' => 50, 'suku_cadang_satuan' => 'pcs']);
        $sc4 = SukuCadang::create(['suku_cadang_kode' => 'SC-004', 'suku_cadang_nama' => 'Lampu Operasi LED', 'suku_cadang_harga' => 1200000, 'suku_cadang_id_vendor' => $v2->vendor_id, 'suku_cadang_id_gudang' => $g1->gudang_id, 'suku_cadang_stok_minimum' => 2, 'suku_cadang_stok_maksimum' => 10, 'suku_cadang_satuan' => 'pcs']);
        $sc5 = SukuCadang::create(['suku_cadang_kode' => 'SC-005', 'suku_cadang_nama' => 'SSD 1TB', 'suku_cadang_harga' => 1500000, 'suku_cadang_id_vendor' => $v3->vendor_id, 'suku_cadang_id_gudang' => $g2->gudang_id, 'suku_cadang_stok_minimum' => 3, 'suku_cadang_stok_maksimum' => 30, 'suku_cadang_satuan' => 'pcs']);

        foreach ([$sc1, $sc2, $sc3, $sc4, $sc5] as $sc) {
            StokSukuCadang::create([
                'stok_suku_cadang_id_suku_cadang' => $sc->suku_cadang_id,
                'stok_suku_cadang_id_gudang' => $sc->suku_cadang_id_gudang,
                'stok_suku_cadang_bin' => '1', 'stok_suku_cadang_jumlah' => rand(15, 80),
            ]);
            PergerakanStok::create([
                'pergerakan_stok_id_suku_cadang' => $sc->suku_cadang_id,
                'pergerakan_stok_id_gudang' => $sc->suku_cadang_id_gudang,
                'pergerakan_stok_tipe' => 'masuk', 'pergerakan_stok_jumlah' => 50,
                'pergerakan_stok_referensi' => 'seed', 'pergerakan_stok_catatan' => 'Stok awal',
            ]);
        }

        // ---- Teknisi ----
        $t1 = Teknisi::create(['teknisi_kode' => 'T-001', 'teknisi_nama' => 'Budi Santoso', 'teknisi_telepon' => '0811', 'teknisi_keahlian' => ['mekanikal', 'it'], 'teknisi_zona' => ['pusat'], 'teknisi_status' => 'tersedia', 'teknisi_latitude' => -6.2, 'teknisi_longitude' => 106.8]);
        $t2 = Teknisi::create(['teknisi_kode' => 'T-002', 'teknisi_nama' => 'Sari Wijaya', 'teknisi_telepon' => '0812', 'teknisi_keahlian' => ['elektrikal', 'hvac'], 'teknisi_zona' => ['utara'], 'teknisi_status' => 'tersedia', 'teknisi_latitude' => -6.1, 'teknisi_longitude' => 106.9]);
        $t3 = Teknisi::create(['teknisi_kode' => 'T-003', 'teknisi_nama' => 'Agus Pratama', 'teknisi_telepon' => '0813', 'teknisi_keahlian' => ['it'], 'teknisi_zona' => ['pusat', 'utara'], 'teknisi_status' => 'sibuk']);

        // ---- Aset + penyusutan ----
        $penyusutan = new PenyusutanService;
        $asetData = [
            ['A-001', 'Toyota Innova', $katMobil->aset_kategori_id, $lokPusat->aset_lokasi_id, $v1->vendor_id, 450000000, 60, 'garis_lurus', 'aktif', 'baik'],
            ['A-002', 'Server Dell R740', $katIt->aset_kategori_id, $lokRuang->aset_lokasi_id, $v3->vendor_id, 85000000, 48, 'garis_lurus', 'aktif', 'baik'],
            ['A-003', 'ECG Machine', $katMedis->aset_kategori_id, $lokPusat->aset_lokasi_id, $v2->vendor_id, 32000000, 120, 'saldo_menurun', 'maintenance', 'kurang_baik'],
            ['A-004', 'Generator 5000W', $katKend->aset_kategori_id, $lokGudang->aset_lokasi_id, $v1->vendor_id, 12000000, 60, 'garis_lurus', 'aktif', 'baik'],
            ['A-005', 'PC Workstation', $katIt->aset_kategori_id, $lokRuang->aset_lokasi_id, $v3->vendor_id, 15000000, 48, 'garis_lurus', 'rusak', 'rusak'],
            ['A-006', 'Ambulans', $katMobil->aset_kategori_id, $lokGudang->aset_lokasi_id, $v1->vendor_id, 600000000, 60, 'garis_lurus', 'dipinjam', 'baik'],
            ['A-007', 'Infus Pump', $katMedis->aset_kategori_id, $lokPusat->aset_lokasi_id, $v2->vendor_id, 9000000, 120, 'saldo_menurun', 'aktif', 'baik'],
            ['A-008', 'Switch Cisco', $katIt->aset_kategori_id, $lokRuang->aset_lokasi_id, $v3->vendor_id, 7000000, 48, 'garis_lurus', 'aktif', 'baik'],
            ['A-009', 'Kompresor', $katKend->aset_kategori_id, $lokGudang->aset_lokasi_id, $v1->vendor_id, 8000000, 60, 'garis_lurus', 'aktif', 'kurang_baik'],
            ['A-010', 'Monitor Patient', $katMedis->aset_kategori_id, $lokPusat->aset_lokasi_id, $v2->vendor_id, 11000000, 120, 'saldo_menurun', 'aktif', 'baik'],
        ];
        $asets = [];
        foreach ($asetData as $i => [$kode, $nama, $kat, $lok, $ven, $harga, $bulan, $metode, $status, $kondisi]) {
            $mulai = Carbon::today()->subMonths(rand(1, 12))->startOfMonth();
            $aset = Aset::create([
                'aset_kode' => $kode, 'aset_nama' => $nama, 'aset_id_kategori' => $kat,
                'aset_id_lokasi' => $lok, 'aset_id_vendor' => $ven, 'aset_merek' => 'Brand',
                'aset_model' => 'MOD-'.($i + 1), 'aset_nomor_seri' => 'SN-'.rand(1000, 9999),
                'aset_tanggal_perolehan' => $mulai, 'aset_harga_perolehan' => $harga,
                'aset_nilai_sisa' => round($harga * 0.1, 2), 'aset_masa_manfaat' => $bulan,
                'aset_metode_penyusutan' => $metode, 'aset_tanggal_mulai_susut' => $mulai,
                'aset_status' => $status, 'aset_kondisi' => $kondisi,
                'aset_jam_pakai' => rand(0, 5000), 'aset_catatan' => 'Seeded',
            ]);
            $penyusutan->jalankan($aset);
            $asets[] = $aset;

            LogStatusAset::create([
                'log_status_aset_id_aset' => $aset->aset_id, 'log_status_aset_status_dari' => 'baru',
                'log_status_aset_status_ke' => $status, 'log_status_aset_actor' => $userId,
                'log_status_aset_catatan' => 'Status awal',
            ]);
        }

        // ---- Dokumen aset ----
        DokumenAset::create(['aset_dokumen_id_aset' => $asets[0]->aset_id, 'aset_dokumen_jenis' => 'stnk', 'aset_dokumen_nomor' => 'STNK-001', 'aset_dokumen_tanggal_terbit' => Carbon::today()->subYear(), 'aset_dokumen_tanggal_expired' => Carbon::today()->addDays(20)]);
        DokumenAset::create(['aset_dokumen_id_aset' => $asets[1]->aset_id, 'aset_dokumen_jenis' => 'garansi', 'aset_dokumen_nomor' => 'GR-002', 'aset_dokumen_tanggal_terbit' => Carbon::today()->subMonths(2)]);

        // ---- Aset contoh untuk kategori baru (dengan custom field) ----
        $extraAsetData = [
            ['A-101', 'Tanah Kavling A', $katTanah->aset_kategori_id, $lokPusat->aset_lokasi_id, $v1->vendor_id, 1500000000, 120, 'garis_lurus', 'aktif', 'baik', [
                'luas_m2' => '500', 'alamat' => 'Jl. Merdeka 1', 'no_sertifikat' => 'SHM-001', 'jenis_hak' => 'Hak Milik', 'tahun_sertifikat' => '2018', 'njop' => '1200000000',
            ]],
            ['A-102', 'Gedung Kantor 3 Lantai', $katGedung->aset_kategori_id, $lokPusat->aset_lokasi_id, $v1->vendor_id, 2200000000, 120, 'garis_lurus', 'aktif', 'baik', [
                'luas_m2' => '1200', 'jumlah_lantai' => '3', 'alamat' => 'Jl. Merdeka 1', 'no_imb' => 'IMB-220', 'tahun_dibangun' => '2015',
            ]],
            ['A-103', 'Lisensi AutoCAD', $katSoftware->aset_kategori_id, $lokRuang->aset_lokasi_id, $v3->vendor_id, 35000000, 36, 'garis_lurus', 'aktif', 'baik', [
                'nama_lisensi' => 'AutoCAD', 'versi' => '2024', 'jumlah_lisensi' => '5', 'tanggal_expired' => Carbon::today()->addYear()->format('Y-m-d'), 'vendor' => 'Autodesk',
            ]],
            ['A-104', 'Lisensi MS Office 365', $katMsLicense->aset_kategori_id, $lokRuang->aset_lokasi_id, $v3->vendor_id, 12000000, 36, 'garis_lurus', 'aktif', 'baik', [
                'product_key' => 'XXXXX-XXXXX-XXXXX-XXXXX-XXXXX', 'edition' => 'Office 365', 'jumlah_seat' => '25', 'tanggal_aktivasi' => Carbon::today()->subMonths(3)->format('Y-m-d'), 'tanggal_expired' => Carbon::today()->addMonths(9)->format('Y-m-d'),
            ]],
            ['A-105', 'PC Workstation i7', $katKomputer->aset_kategori_id, $lokRuang->aset_lokasi_id, $v3->vendor_id, 18000000, 48, 'garis_lurus', 'aktif', 'baik', [
                'processor' => 'Intel Core i7-13700', 'ram_gb' => '32', 'storage' => '1TB NVMe SSD', 'sistem_operasi' => 'Windows 11 Pro', 'no_inventaris' => 'INV-PC-001',
            ]],
            ['A-106', 'Laptop ThinkPad', $katLaptop->aset_kategori_id, $lokRuang->aset_lokasi_id, $v3->vendor_id, 21000000, 48, 'garis_lurus', 'aktif', 'baik', [
                'processor' => 'Intel Core i5-1245U', 'ram_gb' => '16', 'storage' => '512GB SSD', 'sistem_operasi' => 'Windows 11 Pro', 'baterai_wh' => '57',
            ]],
            ['A-107', 'Motor Vario', $katMotor->aset_kategori_id, $lokGudang->aset_lokasi_id, $v1->vendor_id, 22000000, 60, 'garis_lurus', 'aktif', 'baik', [
                'no_stnk' => 'STNK-MTR-001', 'no_rangka' => 'MH1XXXXXXXXXXXX', 'no_mesin' => 'JFXXXXXXXXX', 'cc' => '150', 'warna' => 'Hitam',
            ]],
            ['A-108', 'Toyota Fortuner', $katMobil->aset_kategori_id, $lokPusat->aset_lokasi_id, $v1->vendor_id, 520000000, 60, 'garis_lurus', 'aktif', 'baik', [
                'no_stnk' => 'STNK-FT-001', 'no_kir' => 'KIR-FT-001', 'no_rangka' => 'MHFXXXXXXXXXXXX', 'no_mesin' => '2GXXXXXXXXX', 'warna' => 'Putih', 'bahan_bakar' => 'Bensin',
            ]],
        ];
        $extraAssets = [];
        foreach ($extraAsetData as $i => [$kode, $nama, $kat, $lok, $ven, $harga, $bulan, $metode, $status, $kondisi, $cf]) {
            $mulai = Carbon::today()->subMonths(rand(1, 12))->startOfMonth();
            $aset = Aset::create([
                'aset_kode' => $kode, 'aset_nama' => $nama, 'aset_id_kategori' => $kat,
                'aset_id_lokasi' => $lok, 'aset_id_vendor' => $ven, 'aset_merek' => 'Brand',
                'aset_model' => 'MOD-'.($i + 100), 'aset_nomor_seri' => 'SN-'.rand(1000, 9999),
                'aset_tanggal_perolehan' => $mulai, 'aset_harga_perolehan' => $harga,
                'aset_nilai_sisa' => round($harga * 0.1, 2), 'aset_masa_manfaat' => $bulan,
                'aset_metode_penyusutan' => $metode, 'aset_tanggal_mulai_susut' => $mulai,
                'aset_status' => $status, 'aset_kondisi' => $kondisi,
                'aset_custom_fields' => $cf, 'aset_jam_pakai' => rand(0, 5000), 'aset_catatan' => 'Seeded',
            ]);
            $penyusutan->jalankan($aset);
            $extraAssets[] = $aset;

            LogStatusAset::create([
                'log_status_aset_id_aset' => $aset->aset_id, 'log_status_aset_status_dari' => 'baru',
                'log_status_aset_status_ke' => $status, 'log_status_aset_actor' => $userId,
                'log_status_aset_catatan' => 'Status awal',
            ]);
        }

        // ---- Tiket ----
        $tikets = [];
        $tk = [
            ['T-001', $asets[2]->aset_id, 'AC tidak dingin', 'tinggi', 'buka', $lokPusat->aset_lokasi_id, 3],
            ['T-002', $asets[4]->aset_id, 'PC tidak nyala', 'kritis', 'buka', $lokRuang->aset_lokasi_id, 1],
            ['T-003', $asets[0]->aset_id, 'Service berkala', 'sedang', 'progres', $lokPusat->aset_lokasi_id, 5],
            ['T-004', $asets[6]->aset_id, 'Kallet bocor', 'rendah', 'buka', $lokPusat->aset_lokasi_id, 7],
            ['T-005', $asets[7]->aset_id, 'Port mati', 'tinggi', 'buka', $lokRuang->aset_lokasi_id, 2],
        ];
        foreach ($tk as [$nomor, $asetId, $judul, $urg, $status, $lokId, $hariSla]) {
            $tikets[] = Tiket::create([
                'tiket_nomor' => $nomor, 'tiket_id_aset' => $asetId, 'tiket_id_pelapor' => $userId,
                'tiket_judul' => $judul, 'tiket_deskripsi' => 'Keluhan dari pengguna', 'tiket_tingkat_urgensi' => $urg,
                'tiket_status' => $status, 'tiket_id_lokasi' => $lokId, 'tiket_tanggal_lapor' => Carbon::now(),
                'tiket_jatuh_tempo' => Carbon::now()->addDays($hariSla), 'tiket_biaya' => 0,
            ]);
        }

        // ---- Batch tiket ----
        BatchTiket::create([
            'batch_tiket_kode' => 'B-001', 'batch_tiket_id_teknisi' => $t1->teknisi_id,
            'batch_tiket_tanggal' => Carbon::today(), 'batch_tiket_zona' => 'pusat', 'batch_tiket_mode' => 'geo',
            'batch_tiket_status' => 'draft', 'batch_tiket_urutan' => [$tikets[0]->tiket_id, $tikets[2]->tiket_id],
            'batch_tiket_total_eta' => 120, 'batch_tiket_total_jarak' => 8.5,
        ]);

        // ---- Alert ----
        Alert::create(['alert_tipe' => 'sla', 'alert_id_referensi' => $tikets[1]->tiket_id, 'alert_tipe_referensi' => 'tiket', 'alert_judul' => 'SLA kritis', 'alert_pesan' => 'Tiket '.$tikets[1]->tiket_nomor.' kritis.', 'alert_level' => 'kritis', 'alert_kunci_dedup' => 'sla|tiket|'.$tikets[1]->tiket_id, 'alert_status' => 'terbuka', 'alert_level_eskalasi' => 0, 'alert_jatuh_tempo' => Carbon::now()->addDay()]);
        Alert::create(['alert_tipe' => 'peminjaman', 'alert_id_referensi' => 0, 'alert_tipe_referensi' => 'peminjaman', 'alert_judul' => 'Pengingat', 'alert_pesan' => 'Ada peminjaman jatuh tempo.', 'alert_level' => 'info', 'alert_kunci_dedup' => 'info|peminjaman|0', 'alert_status' => 'terbuka', 'alert_level_eskalasi' => 0]);
        Alert::create(['alert_tipe' => 'service', 'alert_id_referensi' => 0, 'alert_tipe_referensi' => 'jadwal_service', 'alert_judul' => 'Service due', 'alert_pesan' => 'Jadwal service jatuh tempo.', 'alert_level' => 'peringatan', 'alert_kunci_dedup' => 'service|jadwal_service|0', 'alert_status' => 'terbuka', 'alert_level_eskalasi' => 0]);

        // ---- Peminjaman ----
        Peminjaman::create(['peminjaman_nomor' => 'P-001', 'peminjaman_id_aset' => $asets[5]->aset_id, 'peminjaman_id_peminjam' => $userId, 'peminjaman_tujuan' => 'Kegiatan lapangan', 'peminjaman_tanggal_pinjam' => Carbon::now(), 'peminjaman_jatuh_tempo' => Carbon::now()->addDays(3), 'peminjaman_status' => 'aktif', 'peminjaman_grace_jam' => 4]);
        Peminjaman::create(['peminjaman_nomor' => 'P-002', 'peminjaman_id_aset' => $asets[7]->aset_id, 'peminjaman_id_peminjam' => $userId, 'peminjaman_tujuan' => 'Server room', 'peminjaman_tanggal_pinjam' => Carbon::now()->subDays(10), 'peminjaman_jatuh_tempo' => Carbon::now()->subDays(2), 'peminjaman_tanggal_kembali' => null, 'peminjaman_status' => 'terlambat', 'peminjaman_grace_jam' => 4, 'peminjaman_denda' => 50000]);
        Peminjaman::create(['peminjaman_nomor' => 'P-003', 'peminjaman_id_aset' => $asets[3]->aset_id, 'peminjaman_id_peminjam' => $userId, 'peminjaman_tujuan' => 'Cadangan', 'peminjaman_tanggal_pinjam' => Carbon::now()->subDays(5), 'peminjaman_jatuh_tempo' => Carbon::now()->addDays(5), 'peminjaman_status' => 'aktif', 'peminjaman_grace_jam' => 4]);

        // ---- Daftar tunggu & reputasi ----
        DaftarTunggu::create(['daftar_tunggu_id_aset' => $asets[8]->aset_id, 'daftar_tunggu_id_peminjam' => $userId, 'daftar_tunggu_tanggal_mulai' => Carbon::now()->addDays(7), 'daftar_tunggu_durasi' => 5, 'daftar_tunggu_status' => 'menunggu']);
        ReputasiPeminjam::create(['reputasi_peminjam_id_user' => $userId, 'reputasi_peminjam_skor' => 90, 'reputasi_peminjam_total_pinjam' => 3, 'reputasi_peminjam_terlambat' => 1, 'reputasi_peminjam_limit_pinjam' => 3, 'reputasi_peminjam_durasi_maks' => 30]);

        // ---- Perpindahan ----
        Perpindahan::create(['perpindahan_nomor' => 'MOV-001', 'perpindahan_id_aset' => $asets[2]->aset_id, 'perpindahan_id_lokasi_asal' => $lokPusat->aset_lokasi_id, 'perpindahan_id_lokasi_tujuan' => $lokGudang->aset_lokasi_id, 'perpindahan_alasan' => 'Relokasi', 'perpindahan_tanggal_request' => Carbon::now(), 'perpindahan_status' => 'diajukan', 'perpindahan_level_approve' => 'supervisor']);

        // ---- Opname ----
        $opname = Opname::create(['opname_nomor' => 'OPN-001', 'opname_id_lokasi' => $lokPusat->aset_lokasi_id, 'opname_tanggal' => Carbon::today(), 'opname_id_petugas' => $userId, 'opname_status' => 'proses', 'opname_total_sistem' => 4, 'opname_total_fisik' => 0, 'opname_total_selisih' => 0]);
        foreach (array_slice($asets, 0, 4) as $a) {
            OpnameDetail::create(['opname_detail_id_opname' => $opname->opname_id, 'opname_detail_id_aset' => $a->aset_id, 'opname_detail_status_sistem' => $a->aset_status, 'opname_detail_kondisi' => $a->aset_kondisi, 'opname_detail_ditemukan' => true]);
        }

        // ---- Penghapusan ----
        $ph = Penghapusan::create(['penghapusan_nomor' => 'DIS-001', 'penghapusan_id_aset' => $asets[4]->aset_id, 'penghapusan_alasan' => 'Rusak berat', 'penghapusan_tanggal_request' => Carbon::now(), 'penghapusan_nilai_buku' => 2000000, 'penghapusan_nilai_sisa' => 0, 'penghapusan_status' => 'draft', 'penghapusan_triase' => 'buang']);
        PenghapusanKomponen::create(['penghapusan_komponen_id_penghapusan' => $ph->penghapusan_id, 'penghapusan_komponen_nama' => 'DDR', 'penghapusan_komponen_jumlah' => 2, 'penghapusan_komponen_id_suku_cadang' => $sc5->suku_cadang_id, 'penghapusan_komponen_kondisi' => 'rusak']);

        // ---- Persetujuan ----
        Persetujuan::create(['persetujuan_modul' => 'perpindahan', 'persetujuan_id_referensi' => $opname->opname_id ?? 1, 'persetujuan_level' => 'supervisor', 'persetujuan_id_user' => $userId, 'persetujuan_status' => 'menunggu', 'persetujuan_catatan' => 'Approval relokasi']);

        // ---- Pengadaan: PO, items, penerimaan, faktur ----
        $po = PesananPembelian::create(['pesanan_pembelian_nomor' => 'PO-001', 'pesanan_pembelian_id_vendor' => $v3->vendor_id, 'pesanan_pembelian_tanggal' => Carbon::today(), 'pesanan_pembelian_tipe' => 'suku_cadang', 'pesanan_pembelian_status' => 'diterima', 'pesanan_pembelian_total' => 1500000, 'pesanan_pembelian_level_approve' => 'manager']);
        PesananItem::create(['pesanan_item_id_pesanan' => $po->pesanan_pembelian_id, 'pesanan_item_tipe' => 'suku_cadang', 'pesanan_item_id_referensi' => $sc5->suku_cadang_id, 'pesanan_item_nama' => $sc5->suku_cadang_nama, 'pesanan_item_jumlah' => 1, 'pesanan_item_harga' => 1500000, 'pesanan_item_subtotal' => 1500000, 'pesanan_item_diterima' => 1]);
        Penerimaan::create(['penerimaan_id_pesanan' => $po->pesanan_pembelian_id, 'penerimaan_nomor' => 'RCV-001', 'penerimaan_tanggal' => Carbon::today(), 'penerimaan_penerima' => 'Gudang', 'penerimaan_catatan' => 'Diterima lengkap']);
        Faktur::create(['faktur_nomor' => 'INV-001', 'faktur_id_pesanan' => $po->pesanan_pembelian_id, 'faktur_tanggal' => Carbon::today(), 'faktur_total' => 1500000, 'faktur_status' => 'cocok', 'faktur_catatan' => 'Three-way match ok']);

        // ---- Template & jadwal & riwayat service ----
        $tpl = TemplateService::create(['template_service_kode' => 'TPL-001', 'template_service_nama' => 'Servis Berkala Kendaraan', 'template_service_id_kategori' => $katMobil->aset_kategori_id, 'template_service_interval_bulan' => 6, 'template_service_estimasi_jam' => 3, 'template_service_keterangan' => 'Ganti oli & cek']);
        TemplateServiceItem::create(['template_service_item_id_template' => $tpl->template_service_id, 'template_service_item_nama' => 'Ganti oli', 'template_service_item_tipe' => 'ganti', 'template_service_item_id_suku_cadang' => $sc1->suku_cadang_id, 'template_service_item_jumlah' => 1, 'template_service_item_urutan' => 1]);
        TemplateServiceItem::create(['template_service_item_id_template' => $tpl->template_service_id, 'template_service_item_nama' => 'Cek tekanan ban', 'template_service_item_tipe' => 'cek', 'template_service_item_urutan' => 2]);

        $jadwal = JadwalService::create(['jadwal_service_id_aset' => $asets[0]->aset_id, 'jadwal_service_id_template' => $tpl->template_service_id, 'jadwal_service_tanggal_mulai' => Carbon::today()->subDays(10), 'jadwal_service_tanggal_jatuh_tempo' => Carbon::today()->addDays(20), 'jadwal_service_interval_bulan' => 6, 'jadwal_service_status' => 'aktif', 'jadwal_service_tanggal_terakhir' => Carbon::today()->subDays(10)]);
        RiwayatService::create(['riwayat_service_id_aset' => $asets[0]->aset_id, 'riwayat_service_id_teknisi' => $t1->teknisi_id, 'riwayat_service_tanggal' => Carbon::now(), 'riwayat_service_jenis' => 'berkala', 'riwayat_service_biaya' => 350000, 'riwayat_service_catatan' => 'Servis berkala selesai', 'riwayat_service_checklist' => ['oli' => true, 'ban' => true]]);

        // ---- Penjualan aset ----
        $pj = PenjualanAset::create(['penjualan_aset_nomor' => 'SALE-001', 'penjualan_aset_id_aset' => $asets[4]->aset_id, 'penjualan_aset_alasan' => 'Sudah tidak layak', 'penjualan_aset_nilai_buku' => 2000000, 'penjualan_aset_harga_appraisal' => 1500000, 'penjualan_aset_status' => 'ditawarkan', 'penjualan_aset_tanggal_request' => Carbon::now(), 'penjualan_aset_gain_loss' => -500000]);
        PenawaranPenjualan::create(['penawaran_penjualan_id_penjualan' => $pj->penjualan_aset_id, 'penawaran_penjualan_nama_pembeli' => 'Toko LeLang', 'penawaran_penjualan_kontak' => '0819', 'penawaran_penjualan_harga' => 1500000, 'penawaran_penjualan_tanggal' => Carbon::today(), 'penawaran_penjualan_status' => 'diajukan', 'penawaran_penjualan_hasil' => 'Menunggu respons']);

        $this->command->info('AMS seeder selesai: '.(count($asets) + count($extraAssets)).' aset + ledger penyusutan, tiket, alert, peminjaman, pengadaan, service & penjualan.');
    }
}
