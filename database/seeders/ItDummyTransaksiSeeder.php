<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Aset;
use App\Models\DaftarTunggu;
use App\Models\DokumenAset;
use App\Models\Faktur;
use App\Models\Gudang;
use App\Models\JadwalService;
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
use App\Models\PermintaanSukuCadang;
use App\Models\Perpindahan;
use App\Models\Persetujuan;
use App\Models\PesananItem;
use App\Models\PesananPembelian;
use App\Models\RiwayatService;
use App\Models\SukuCadang;
use App\Models\Teknisi;
use App\Models\Tiket;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// Dummy transaksi lengkap perusahaan IT (Jan 2024 – Nov 2026).
// Idempoten: semua by nomor via updateOrCreate/firstOrCreate.
class ItDummyTransaksiSeeder extends Seeder
{
    private int $n = 0;

    public function run(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;
        $teknisi = Teknisi::pluck('id')->all() ?: [null];
        $lok = LokasiAset::pluck('aset_lokasi_id', 'aset_lokasi_kode')->all();
        $aset = Aset::pluck('aset_id', 'aset_kode')->all();
        $a = fn ($kode) => $aset[$kode] ?? reset($aset);

        // ── 1. Dokumen aset (garansi, STNK, lisensi, sertifikat) ──
        $docs = [
            [$a('A-002'), 'garansi', 'GR-DELL-740', '2024-03-10', '2027-03-10', 'Garansi ProSupport 3thn'],
            [$a('A-008'), 'garansi', 'GR-CISCO-9300', '2024-05-01', '2025-05-01', 'Garansi SmartNet 1thn'],
            [$a('A-105'), 'garansi', 'GR-HP-Z2-01', '2025-06-15', '2028-06-15', 'Garansi onsite 3thn'],
            [$a('A-106'), 'garansi', 'GR-TP-T14-01', '2025-07-20', '2028-07-20', 'Garansi premier 3thn'],
            [$a('CF-011'), 'garansi', 'GR-DELL-LAT-01', '2024-07-01', '2027-07-01', 'Garansi ProSupport'],
            [$a('CF-012'), 'garansi', 'GR-MBP14-01', '2025-07-01', '2026-07-01', 'AppleCare+'],
            [$a('CF-009'), 'garansi', 'GR-DELL-R450', '2024-03-01', '2027-03-01', 'Garansi ProSupport 3thn'],
            [$a('A-001'), 'stnk', 'STNK-ZENIX-01', '2024-01-15', '2029-01-15', 'STNK Innova Zenix'],
            [$a('A-001'), 'bpkb', 'BPKB-ZENIX-01', '2024-01-15', null, 'BPKB Innova Zenix'],
            [$a('A-108'), 'stnk', 'STNK-FORT-01', '2024-08-20', '2029-08-20', 'STNK Fortuner'],
            [$a('CF-005'), 'stnk', 'STNK-AVZ-01', '2025-05-10', '2030-05-10', 'STNK Avanza'],
            [$a('CF-007'), 'stnk', 'STNK-BEAT-01', '2024-12-01', '2029-12-01', 'STNK Beat'],
            [$a('A-103'), 'lainnya', 'LIC-ACAD-2024', '2024-01-01', '2026-12-31', 'Lisensi AutoCAD 5 seat'],
            [$a('A-104'), 'lainnya', 'LIC-O365-E3', '2024-10-01', '2026-10-01', 'Lisensi Office 365 E3 25 seat'],
            [$a('CF-013'), 'lainnya', 'LIC-W11-OLP', '2025-09-01', null, 'Lisensi Windows 11 Pro OLP 50 seat'],
            [$a('CF-020'), 'garansi', 'GR-HIK-8CH', '2025-02-01', '2027-02-01', 'Garansi CCTV 2thn'],
            [$a('CF-019'), 'garansi', 'GR-DAIKIN-01', '2024-09-01', '2025-09-01', 'Garansi AC 1thn'],
            [$a('A-101'), 'sertifikat', 'SHM-001', '2018-06-01', null, 'SHM Tanah Kavling A'],
            [$a('A-102'), 'lainnya', 'IMB-220', '2015-03-01', null, 'IMB Gedung Kantor'],
        ];
        foreach ($docs as [$asetId, $jenis, $nomor, $terbit, $expired, $ket]) {
            DokumenAset::updateOrCreate(['aset_dokumen_nomor' => $nomor], [
                'aset_dokumen_id_aset' => $asetId, 'aset_dokumen_jenis' => $jenis,
                'aset_dokumen_tanggal_terbit' => $terbit,
                'aset_dokumen_tanggal_expired' => $expired,
                'aset_dokumen_keterangan' => $ket,
            ]);
            $this->n++;
        }

        // ── 2. Tiket IT (lapor tersebar 2024-2026) ──
        $tikets = [
            ['T-100', 'A-106', 'Laptop lemot setelah update Windows', 'sedang', 'selesai', '2024-02-12', '2024-02-14'],
            ['T-101', 'A-002', 'Server down, RAID degraded', 'kritis', 'terverifikasi', '2024-04-03', '2024-04-05'],
            ['T-102', 'A-008', 'WiFi lantai 2 putus-putus', 'tinggi', 'selesai', '2024-06-17', '2024-06-19'],
            ['T-103', 'CF-010', 'PC kantor tidak bisa booting', 'tinggi', 'selesai', '2024-09-02', '2024-09-04'],
            ['T-104', 'A-008', 'Upgrade firmware switch core', 'sedang', 'terverifikasi', '2024-11-11', '2024-11-13'],
            ['T-105', 'CF-020', 'CCTV gudang offline 2 kamera', 'tinggi', 'selesai', '2025-01-20', '2025-01-22'],
            ['T-106', 'CF-019', 'AC ruang server tidak dingin', 'kritis', 'selesai', '2025-03-08', '2025-03-09'],
            ['T-107', 'A-105', 'Butuh tambah RAM 32GB workstation', 'rendah', 'menunggu_part', '2025-05-19', null],
            ['T-108', 'CF-011', 'Keyboard laptop error sebagian', 'sedang', 'progres', '2025-07-07', null],
            ['T-109', 'CF-012', 'Instalasi Adobe CC desainer baru', 'rendah', 'selesai', '2025-08-25', '2025-08-26'],
            ['T-110', 'A-002', 'Backup database gagal 3 hari', 'kritis', 'ditugaskan', '2025-10-14', null],
            ['T-111', 'A-106', 'VPN tidak bisa konek dari rumah', 'tinggi', 'progres', '2025-12-01', null],
            ['T-112', 'CF-009', 'Migrasi VM ke server baru', 'sedang', 'ditugaskan', '2026-01-12', null],
            ['T-113', 'A-105', 'Lisensi AutoCAD expired peringatan', 'sedang', 'buka', '2026-02-20', null],
            ['T-114', 'A-008', 'Penambahan access point lantai 3', 'rendah', 'buka', '2026-03-15', null],
            ['T-115', 'CF-010', 'Ganti SSD 512GB PC kasir', 'sedang', 'menunggu_part', '2026-04-06', null],
            ['T-116', 'CF-020', 'NVR penuh, arsip 30 hari gagal', 'tinggi', 'progres', '2026-05-11', null],
            ['T-117', 'A-106', 'Layar flickering ThinkPad', 'sedang', 'buka', '2026-06-22', null],
            ['T-118', 'A-002', 'Patching keamanan bulanan server', 'sedang', 'ditugaskan', '2026-07-13', null],
            ['T-119', 'CF-012', 'MacBook tidak charge via dock', 'tinggi', 'buka', '2026-08-17', null],
            ['T-120', 'A-105', 'Blue screen saat render 3D', 'tinggi', 'progres', '2026-09-09', null],
            ['T-121', 'CF-009', 'Monitoring Zabbix alert disk 90%', 'tinggi', 'buka', '2026-10-21', null],
            ['T-122', 'A-008', 'Audit kabel UTP rack server', 'rendah', 'buka', '2026-11-02', null],
            ['T-123', 'CF-011', 'Ganti thermal paste laptop support', 'sedang', 'selesai', '2026-11-01', '2026-11-03'],
            ['T-124', 'A-105', 'Upgrade SSD workstation render', 'tinggi', 'progres', '2026-11-02', null],
        ];
        $tiketIds = [];
        foreach ($tikets as [$nomor, $kode, $judul, $urg, $status, $lapor, $selesai]) {
            $punyaTeknisi = ! in_array($status, ['buka'], true);
            $tugas = $punyaTeknisi ? Carbon::parse($lapor)->addDay() : null;
            $done = in_array($status, ['selesai', 'terverifikasi'], true);
            $t = Tiket::updateOrCreate(['tiket_nomor' => $nomor], [
                'tiket_id_aset' => $a($kode), 'tiket_id_pelapor' => $userId,
                'tiket_id_teknisi' => $punyaTeknisi ? $teknisi[array_rand($teknisi)] : null,
                'tiket_judul' => $judul, 'tiket_deskripsi' => $judul.' — dilaporkan via helpdesk IT.',
                'tiket_tingkat_urgensi' => $urg, 'tiket_status' => $status,
                'tiket_id_lokasi' => $lok['SRV'] ?? reset($lok),
                'tiket_tanggal_lapor' => Carbon::parse($lapor),
                'tiket_tanggal_tugas' => $tugas,
                'tiket_tanggal_mulai' => $tugas,
                'tiket_tanggal_selesai' => $selesai ? Carbon::parse($selesai) : null,
                'tiket_tanggal_analisa' => $done || $status === 'progres' ? Carbon::parse($lapor)->addDay() : null,
                'tiket_analisa' => $done || $status === 'progres' ? 'Analisa: '.$judul.'. Root cause teridentifikasi, tindakan perbaikan dilakukan.' : null,
                'tiket_kesimpulan' => $done ? 'Selesai dan diverifikasi user.' : null,
                'tiket_durasi_menit' => $selesai ? Carbon::parse($lapor)->diffInMinutes(Carbon::parse($selesai)) : null,
                'tiket_jatuh_tempo' => Carbon::parse($lapor)->addDays(3),
                'tiket_biaya' => $selesai ? rand(0, 1500000) : 0,
            ]);
            $tiketIds[$nomor] = $t->tiket_id;
            $this->n++;
        }

        // ── 3. Peminjaman (laptop/proyektor/kendaraan operasional) ──
        $pinjams = [
            ['P-100', 'A-106', 'Presentasi ke klien Sudirman', '2024-03-05', '2024-03-07', 'dikembalikan', 0],
            ['P-101', 'CF-011', 'Backup laptop tim support', '2024-07-15', '2024-07-20', 'dikembalikan', 0],
            ['P-102', 'CF-008', 'Survey lokasi tower BTS', '2025-02-10', '2025-02-12', 'dikembalikan', 0],
            ['P-103', 'A-108', 'Antar server ke data center', '2025-06-02', '2025-06-03', 'dikembalikan', 0],
            ['P-104', 'A-106', 'Work from client site 2 minggu', '2025-09-01', '2025-09-15', 'dikembalikan', 25000],
            ['P-105', 'CF-012', 'Editing video company profile', '2025-11-17', '2025-11-24', 'terlambat', 75000],
            ['P-106', 'CF-006', 'Mobilisasi UPS ke cabang', '2026-01-08', '2026-01-10', 'dikembalikan', 0],
            ['P-107', 'A-106', 'Onsite troubleshooting klien', '2026-04-13', '2026-04-17', 'berjalan', 0],
            ['P-108', 'CF-011', 'Laptop magang batch 3', '2026-06-01', '2026-08-31', 'berjalan', 0],
            ['P-109', 'A-108', 'Dinas luar kota instalasi', '2026-09-21', '2026-09-25', 'berjalan', 0],
            ['P-110', 'CF-008', 'Patroli cek CCTV cabang', '2026-10-12', '2026-10-14', 'berjalan', 0],
            ['P-111', 'CF-012', 'Demo produk ke calon klien', '2026-11-01', '2026-11-05', 'berjalan', 0],
        ];
        foreach ($pinjams as [$nomor, $kode, $tujuan, $pinjam, $tempo, $status, $denda]) {
            Peminjaman::updateOrCreate(['peminjaman_nomor' => $nomor], [
                'peminjaman_id_aset' => $a($kode), 'peminjaman_id_peminjam' => $userId,
                'peminjaman_tujuan' => $tujuan,
                'peminjaman_tanggal_pinjam' => Carbon::parse($pinjam),
                'peminjaman_jatuh_tempo' => Carbon::parse($tempo),
                'peminjaman_tanggal_kembali' => $status === 'dikembalikan' ? Carbon::parse($tempo) : null,
                'peminjaman_status' => $status, 'peminjaman_grace_jam' => 4,
                'peminjaman_denda' => $denda,
            ]);
            $this->n++;
        }
        foreach ([
            ['A-106', '2026-11-10', 7], ['CF-011', '2026-11-12', 14],
            ['A-108', '2026-11-15', 3], ['CF-012', '2026-11-20', 5],
        ] as $i => [$kode, $mulai, $durasi]) {
            DaftarTunggu::firstOrCreate(
                ['daftar_tunggu_id_aset' => $a($kode), 'daftar_tunggu_id_peminjam' => $userId, 'daftar_tunggu_tanggal_mulai' => Carbon::parse($mulai)],
                ['daftar_tunggu_durasi' => $durasi, 'daftar_tunggu_status' => 'menunggu']
            );
            $this->n++;
        }

        // ── 4. Perpindahan + persetujuan ──
        $pindahs = [
            ['MOV-100', 'CF-010', 'PST', 'SRV', 'Konsolidasi PC ke ruang server', '2024-05-20', 'disetujui'],
            ['MOV-101', 'A-106', 'SRV', 'PST', 'Laptop dialokasikan ke tim dev', '2025-02-14', 'disetujui'],
            ['MOV-102', 'CF-011', 'PST', 'GDC', 'Laptop cadangan ke gudang cabang', '2025-08-30', 'disetujui'],
            ['MOV-103', 'CF-009', 'GDC', 'SRV', 'Server rak pindah ke DC utama', '2026-06-10', 'diajukan'],
        ];
        foreach ($pindahs as [$nomor, $kode, $asal, $tujuan, $alasan, $tgl, $status]) {
            $p = Perpindahan::updateOrCreate(['perpindahan_nomor' => $nomor], [
                'perpindahan_id_aset' => $a($kode),
                'perpindahan_id_lokasi_asal' => $lok[$asal] ?? reset($lok),
                'perpindahan_id_lokasi_tujuan' => $lok[$tujuan] ?? reset($lok),
                'perpindahan_alasan' => $alasan,
                'perpindahan_tanggal_request' => Carbon::parse($tgl),
                'perpindahan_status' => $status, 'perpindahan_level_approve' => 'supervisor',
            ]);
            Persetujuan::firstOrCreate(
                ['persetujuan_modul' => 'perpindahan', 'persetujuan_id_referensi' => $p->perpindahan_id],
                ['persetujuan_level' => 'supervisor', 'persetujuan_id_user' => $userId,
                 'persetujuan_status' => $status === 'diajukan' ? 'menunggu' : 'disetujui',
                 'persetujuan_catatan' => 'Approval relokasi — '.$nomor]
            );
            $this->n += 2;
        }

        // ── 5. Opname semesteran ruang server ──
        $opname = Opname::firstOrCreate(
            ['opname_nomor' => 'OPN-100'],
            ['opname_id_lokasi' => $lok['SRV'] ?? reset($lok), 'opname_tanggal' => Carbon::create(2026, 6, 30),
             'opname_id_petugas' => $userId, 'opname_status' => 'selesai',
             'opname_total_sistem' => 8, 'opname_total_fisik' => 8, 'opname_total_selisih' => 0]
        );
        foreach (['A-002', 'A-008', 'A-105', 'A-106', 'CF-009', 'CF-011', 'CF-012', 'CF-020'] as $kode) {
            OpnameDetail::firstOrCreate(
                ['opname_detail_id_opname' => $opname->opname_id, 'opname_detail_id_aset' => $a($kode)],
                ['opname_detail_status_sistem' => 'aktif', 'opname_detail_kondisi' => 'baik', 'opname_detail_ditemukan' => true]
            );
            $this->n++;
        }

        // ── 6. Penghapusan aset rusak ──
        $hapus = [
            ['DIS-100', 'CF-010', 'Motherboard mati total', '2025-04-15', 3000000, 'buang'],
            ['DIS-101', 'A-005', 'PSU terbakar, tidak ekonomis diperbaiki', '2026-02-20', 1500000, 'kanibal'],
        ];
        foreach ($hapus as [$nomor, $kode, $alasan, $tgl, $nilai, $triase]) {
            $ph = Penghapusan::updateOrCreate(['penghapusan_nomor' => $nomor], [
                'penghapusan_id_aset' => $a($kode), 'penghapusan_alasan' => $alasan,
                'penghapusan_tanggal_request' => Carbon::parse($tgl),
                'penghapusan_nilai_buku' => $nilai, 'penghapusan_nilai_sisa' => 0,
                'penghapusan_status' => 'disetujui', 'penghapusan_triase' => $triase,
            ]);
            Persetujuan::firstOrCreate(
                ['persetujuan_modul' => 'penghapusan', 'persetujuan_id_referensi' => $ph->penghapusan_id],
                ['persetujuan_level' => 'manager', 'persetujuan_id_user' => $userId,
                 'persetujuan_status' => 'disetujui', 'persetujuan_catatan' => 'Approved — '.$nomor]
            );
            $this->n += 2;
        }
        PenghapusanKomponen::firstOrCreate(
            ['penghapusan_komponen_id_penghapusan' => Penghapusan::where('penghapusan_nomor', 'DIS-101')->value('penghapusan_id'), 'penghapusan_komponen_nama' => 'SSD 256GB'],
            ['penghapusan_komponen_jumlah' => 1, 'penghapusan_komponen_kondisi' => 'baik']
        );

        // ── 7. Pengadaan IT: PO + penerimaan + faktur ──
        $vendors = Vendor::pluck('vendor_id', 'vendor_kode')->all();
        $sc = SukuCadang::pluck('suku_cadang_id', 'suku_cadang_kode')->all();
        $gudang = Gudang::orderBy('gudang_id')->value('gudang_id');
        $pos = [
            ['PO-100', 'V-003', '2024-02-01', 'suku_cadang', 'diterima', 12500000, [['SC-005', 5, 2500000], ['SC-003', 20, 75000]], '2024-02-10'],
            ['PO-101', 'V-003', '2024-08-12', 'suku_cadang', 'diterima', 8000000, [['SC-001', 40, 50000], ['SC-005', 4, 1500000]], '2024-08-20'],
            ['PO-102', 'V-001', '2025-05-06', 'suku_cadang', 'diterima', 15000000, [['SC-002', 10, 400000], ['SC-004', 8, 1200000], ['SC-003', 20, 70000]], '2025-05-15'],
            ['PO-103', 'V-003', '2026-01-19', 'suku_cadang', 'diterima', 30000000, [['SC-005', 20, 1500000]], '2026-01-28'],
            ['PO-104', 'V-003', '2026-09-07', 'suku_cadang', 'disetujui', 18000000, [['SC-005', 12, 1500000]], null],
        ];
        foreach ($pos as [$nomor, $vkode, $tgl, $tipe, $status, $total, $items, $terima]) {
            $po = PesananPembelian::updateOrCreate(['pesanan_pembelian_nomor' => $nomor], [
                'pesanan_pembelian_id_vendor' => $vendors[$vkode] ?? reset($vendors),
                'pesanan_pembelian_tanggal' => Carbon::parse($tgl),
                'pesanan_pembelian_tipe' => $tipe, 'pesanan_pembelian_status' => $status,
                'pesanan_pembelian_total' => $total, 'pesanan_pembelian_level_approve' => 'manager',
            ]);
            foreach ($items as [$sckode, $qty, $harga]) {
                PesananItem::updateOrCreate(
                    ['pesanan_item_id_pesanan' => $po->pesanan_pembelian_id, 'pesanan_item_id_referensi' => $sc[$sckode] ?? reset($sc)],
                    ['pesanan_item_tipe' => 'suku_cadang', 'pesanan_item_nama' => $sckode,
                     'pesanan_item_jumlah' => $qty, 'pesanan_item_harga' => $harga,
                     'pesanan_item_subtotal' => $qty * $harga, 'pesanan_item_diterima' => $terima ? $qty : 0]
                );
            }
            if ($terima) {
                Penerimaan::firstOrCreate(['penerimaan_nomor' => 'RCV-'.substr($nomor, 3)], [
                    'penerimaan_id_pesanan' => $po->pesanan_pembelian_id,
                    'penerimaan_tanggal' => Carbon::parse($terima),
                    'penerimaan_penerima' => 'Gudang', 'penerimaan_id_gudang' => $gudang,
                    'penerimaan_catatan' => 'Diterima lengkap — '.$nomor,
                ]);
                Faktur::firstOrCreate(['faktur_nomor' => 'INV-'.substr($nomor, 3)], [
                    'faktur_id_pesanan' => $po->pesanan_pembelian_id,
                    'faktur_tanggal' => Carbon::parse($terima), 'faktur_total' => $total,
                    'faktur_status' => 'cocok', 'faktur_catatan' => 'Three-way match ok',
                ]);
            }
            $this->n++;
        }

        // ── 8. Permintaan suku cadang (budget IT) ──
        $deptIT = DB::table('department')->where('department_kode', 'IT')->value('department_id');
        $mintak = [
            ['MNT-001', 'T-107', 'SC-005', 2, 1500000, 'selesai', '2025-05-20', 'Upgrade RAM workstation render'],
            ['MNT-002', 'T-108', 'SC-003', 5, 75000, 'disetujui', '2025-07-08', 'Kabel HDMI meeting room'],
            ['MNT-003', 'T-110', 'SC-005', 4, 1500000, 'disetujui', '2025-10-15', 'SSD cache server backup'],
            ['MNT-004', null, 'SC-001', 20, 50000, 'selesai', '2025-12-05', 'Stok filter genset'],
            ['MNT-005', 'T-115', 'SC-005', 3, 1500000, 'disetujui', '2026-04-07', 'Ganti SSD PC kasir'],
            ['MNT-006', null, 'SC-002', 4, 400000, 'disetujui', '2026-05-30', 'Ban operasional L300'],
            ['MNT-007', 'T-116', 'SC-005', 6, 1500000, 'menunggu', '2026-06-15', 'HDD NVR CCTV 6TB'],
            ['MNT-008', 'T-117', 'SC-003', 10, 75000, 'menunggu', '2026-07-01', 'Kabel display stok teknisi'],
            ['MNT-009', 'T-120', 'SC-005', 2, 1500000, 'menunggu', '2026-09-10', 'NVMe workstation render'],
            ['MNT-010', 'T-121', 'SC-005', 8, 1500000, 'menunggu', '2026-10-22', 'Disk shelf monitoring'],
            ['MNT-011', null, 'SC-004', 2, 1200000, 'ditolak', '2026-03-11', 'Lampu cadangan — stok cukup'],
            ['MNT-012', 'T-122', 'SC-003', 15, 75000, 'disetujui', '2026-11-03', 'Kabel UTP audit rack'],
        ];
        foreach ($mintak as [$nomor, $tiket, $sckode, $qty, $harga, $status, $tgl, $cat]) {
            PermintaanSukuCadang::updateOrCreate(['permintaan_suku_cadang_nomor' => $nomor], [
                'permintaan_suku_cadang_id_tiket' => $tiket ? ($tiketIds[$tiket] ?? null) : null,
                'permintaan_suku_cadang_id_suku_cadang' => $sc[$sckode] ?? reset($sc),
                'permintaan_suku_cadang_id_peminta' => $userId, 'department_id' => $deptIT,
                'permintaan_suku_cadang_jumlah' => $qty, 'permintaan_suku_cadang_harga' => $harga,
                'permintaan_suku_cadang_subtotal' => $qty * $harga,
                'permintaan_suku_cadang_status' => $status,
                'permintaan_suku_cadang_tanggal_permintaan' => Carbon::parse($tgl),
                'permintaan_suku_cadang_catatan' => $cat,
            ]);
            $this->n++;
        }

        // ── 9. Pergerakan stok ──
        foreach ([
            ['SC-005', 'masuk', 20, 'PO-103', '2026-01-28'], ['SC-005', 'keluar', 2, 'MNT-001', '2025-05-21'],
            ['SC-003', 'masuk', 20, 'PO-102', '2025-05-15'], ['SC-003', 'keluar', 5, 'MNT-002', '2025-07-09'],
            ['SC-001', 'masuk', 40, 'PO-101', '2024-08-20'], ['SC-002', 'masuk', 10, 'PO-102', '2025-05-15'],
            ['SC-005', 'keluar', 4, 'MNT-003', '2025-10-16'], ['SC-004', 'masuk', 8, 'PO-102', '2025-05-15'],
            ['SC-005', 'keluar', 3, 'MNT-005', '2026-04-08'], ['SC-001', 'keluar', 20, 'MNT-004', '2025-12-06'],
        ] as [$sckode, $tipe, $qty, $ref, $tgl]) {
            PergerakanStok::firstOrCreate(
                ['pergerakan_stok_referensi' => $ref, 'pergerakan_stok_tipe' => $tipe,
                 'pergerakan_stok_id_suku_cadang' => $sc[$sckode] ?? reset($sc)],
                ['pergerakan_stok_id_gudang' => $gudang, 'pergerakan_stok_jumlah' => $qty,
                 'pergerakan_stok_catatan' => 'Dummy IT '.$ref.' '.$tgl]
            );
            $this->n++;
        }

        // ── 10. Jadwal + riwayat service ──
        $jadwals = [
            ['A-001', '2024-01-10', '2024-07-10', 'Servis berkala 10rb km'],
            ['A-002', '2024-04-01', '2024-10-01', 'Preventive maintenance server'],
            ['A-008', '2025-03-01', '2025-09-01', 'Health check jaringan'],
            ['CF-020', '2025-06-01', '2025-12-01', 'Pembersihan kamera & NVR'],
            ['A-001', '2026-07-10', '2027-01-10', 'Servis berkala + ganti ban'],
            ['A-002', '2026-10-01', '2027-04-01', 'Preventive maintenance server'],
        ];
        foreach ($jadwals as $i => [$kode, $mulai, $tempo, $ket]) {
            JadwalService::firstOrCreate(
                ['jadwal_service_id_aset' => $a($kode), 'jadwal_service_tanggal_mulai' => Carbon::parse($mulai)],
                ['jadwal_service_tanggal_jatuh_tempo' => Carbon::parse($tempo),
                 'jadwal_service_interval_bulan' => 6,
                 'jadwal_service_status' => Carbon::parse($tempo)->isPast() ? 'selesai' : 'aktif',
                 'jadwal_service_tanggal_terakhir' => Carbon::parse($mulai)]
            );
            $this->n++;
        }
        foreach ([
            ['A-001', '2024-07-12', 'berkala', 2500000, 'Servis 20rb km + oli + filter'],
            ['A-002', '2024-10-03', 'preventif', 1500000, 'Ganti thermal paste + cleaning'],
            ['A-008', '2025-09-03', 'preventif', 500000, 'Audit + label ulang patch panel'],
            ['CF-020', '2025-12-05', 'korektif', 750000, 'Ganti 2 kamera korosi'],
            ['CF-007', '2025-08-17', 'berkala', 350000, 'Servis ringan + ganti oli'],
            ['A-001', '2025-01-11', 'berkala', 2500000, 'Servis 30rb km'],
            ['A-108', '2026-03-22', 'berkala', 2800000, 'Servis 40rb km + spooring'],
            ['A-002', '2026-04-05', 'preventif', 1500000, 'RAID rebuild + firmware'],
        ] as [$kode, $tgl, $jenis, $biaya, $cat]) {
            RiwayatService::firstOrCreate(
                ['riwayat_service_id_aset' => $a($kode), 'riwayat_service_tanggal' => Carbon::parse($tgl)],
                ['riwayat_service_id_teknisi' => $teknisi[array_rand($teknisi)],
                 'riwayat_service_jenis' => $jenis, 'riwayat_service_biaya' => $biaya,
                 'riwayat_service_catatan' => $cat]
            );
            $this->n++;
        }

        // ── 11. Penjualan aset bekas + alert ──
        $pj = PenjualanAset::updateOrCreate(['penjualan_aset_nomor' => 'SALE-100'], [
            'penjualan_aset_id_aset' => $a('CF-010'), 'penjualan_aset_alasan' => 'Peremajaan PC kantor 2026',
            'penjualan_aset_nilai_buku' => 4000000, 'penjualan_aset_harga_appraisal' => 3500000,
            'penjualan_aset_status' => 'ditawarkan', 'penjualan_aset_tanggal_request' => Carbon::create(2026, 9, 1),
            'penjualan_aset_gain_loss' => -500000,
        ]);
        foreach ([['Budi Hartono', '0812-100-200', 3600000], ['PT Sinar Abadi', '021-555-0199', 3500000]] as [$nama, $kontak, $harga]) {
            PenawaranPenjualan::firstOrCreate(
                ['penawaran_penjualan_id_penjualan' => $pj->penjualan_aset_id, 'penawaran_penjualan_nama_pembeli' => $nama],
                ['penawaran_penjualan_kontak' => $kontak, 'penawaran_penjualan_harga' => $harga,
                 'penawaran_penjualan_tanggal' => Carbon::create(2026, 10, 5), 'penawaran_penjualan_status' => 'diajukan',
                 'penawaran_penjualan_hasil' => 'Menunggu keputusan lelang']
            );
            $this->n++;
        }
        foreach ([
            ['sla', 'tiket', 'T-121 dibuka: disk monitoring 90%', 'peringatan'],
            ['service', 'jadwal_service', 'Preventive server jatuh tempo Apr 2027', 'info'],
            ['peminjaman', 'peminjaman', 'P-105 terlambat: laptop editing', 'peringatan'],
            ['stok', 'suku_cadang', 'SSD 1TB di bawah stok minimum', 'kritis'],
            ['dokumen', 'aset_dokumen', 'Garansi SmartNet switch expired', 'peringatan'],
            ['budget', 'permintaan_suku_cadang', '4 permintaan menunggu approval IT', 'info'],
        ] as [$tipe, $ref, $judul, $level]) {
            Alert::firstOrCreate(['alert_kunci_dedup' => 'it|'.$tipe.'|'.$judul], [
                'alert_tipe' => $tipe, 'alert_id_referensi' => 0, 'alert_tipe_referensi' => $ref,
                'alert_judul' => $judul, 'alert_pesan' => $judul,
                'alert_level' => $level, 'alert_status' => 'terbuka', 'alert_level_eskalasi' => 0,
            ]);
            $this->n++;
        }

        // ── 12. Dummy November 2026 (agar report default periode berjalan terisi) ──
        $mov = Perpindahan::updateOrCreate(['perpindahan_nomor' => 'MOV-104'], [
            'perpindahan_id_aset' => $a('A-106'),
            'perpindahan_id_lokasi_asal' => $lok['PST'] ?? reset($lok),
            'perpindahan_id_lokasi_tujuan' => $lok['GDC'] ?? reset($lok),
            'perpindahan_alasan' => 'Rotasi laptop ke cabang November',
            'perpindahan_tanggal_request' => Carbon::create(2026, 11, 5),
            'perpindahan_status' => 'diajukan', 'perpindahan_level_approve' => 'supervisor',
        ]);
        Persetujuan::firstOrCreate(
            ['persetujuan_modul' => 'perpindahan', 'persetujuan_id_referensi' => $mov->perpindahan_id],
            ['persetujuan_level' => 'supervisor', 'persetujuan_id_user' => $userId,
             'persetujuan_status' => 'menunggu', 'persetujuan_catatan' => 'Approval rotasi — MOV-104']
        );
        $this->n += 2;

        $opn = Opname::firstOrCreate(
            ['opname_nomor' => 'OPN-101'],
            ['opname_id_lokasi' => $lok['GDC'] ?? reset($lok), 'opname_tanggal' => Carbon::create(2026, 11, 6),
             'opname_id_petugas' => $userId, 'opname_status' => 'proses',
             'opname_total_sistem' => 5, 'opname_total_fisik' => 4, 'opname_total_selisih' => 1]
        );
        foreach ([['CF-006', true], ['A-107', true], ['CF-008', true], ['CF-017', true], ['CF-018', false]] as [$kode, $ketemu]) {
            OpnameDetail::firstOrCreate(
                ['opname_detail_id_opname' => $opn->opname_id, 'opname_detail_id_aset' => $a($kode)],
                ['opname_detail_status_sistem' => 'aktif', 'opname_detail_kondisi' => 'baik', 'opname_detail_ditemukan' => $ketemu,
                 'opname_detail_waktu_scan' => $ketemu ? Carbon::create(2026, 11, 6, 10) : null]
            );
            $this->n++;
        }

        $ph2 = Penghapusan::updateOrCreate(['penghapusan_nomor' => 'DIS-102'], [
            'penghapusan_id_aset' => $a('CF-018'), 'penghapusan_alasan' => 'Genset aus, kanibal karburator untuk genset cadangan',
            'penghapusan_tanggal_request' => Carbon::create(2026, 11, 7),
            'penghapusan_nilai_buku' => 5000000, 'penghapusan_nilai_sisa' => 500000,
            'penghapusan_status' => 'diajukan', 'penghapusan_triase' => 'kanibal',
        ]);
        Persetujuan::firstOrCreate(
            ['persetujuan_modul' => 'penghapusan', 'persetujuan_id_referensi' => $ph2->penghapusan_id],
            ['persetujuan_level' => 'manager', 'persetujuan_id_user' => $userId,
             'persetujuan_status' => 'menunggu', 'persetujuan_catatan' => 'Approval kanibal — DIS-102']
        );
        $this->n += 2;

        $po5 = PesananPembelian::updateOrCreate(['pesanan_pembelian_nomor' => 'PO-105'], [
            'pesanan_pembelian_id_vendor' => $vendors['V-003'] ?? reset($vendors),
            'pesanan_pembelian_tanggal' => Carbon::create(2026, 11, 10),
            'pesanan_pembelian_tipe' => 'suku_cadang', 'pesanan_pembelian_status' => 'disetujui',
            'pesanan_pembelian_total' => 9750000, 'pesanan_pembelian_level_approve' => 'manager',
        ]);
        foreach ([['SC-005', 6, 1500000], ['SC-003', 10, 75000]] as [$sckode, $qty, $harga]) {
            PesananItem::updateOrCreate(
                ['pesanan_item_id_pesanan' => $po5->pesanan_pembelian_id, 'pesanan_item_id_referensi' => $sc[$sckode] ?? reset($sc)],
                ['pesanan_item_tipe' => 'suku_cadang', 'pesanan_item_nama' => $sckode,
                 'pesanan_item_jumlah' => $qty, 'pesanan_item_harga' => $harga,
                 'pesanan_item_subtotal' => $qty * $harga, 'pesanan_item_diterima' => 0]
            );
        }
        $this->n++;

        foreach ([
            ['A-002', '2026-11-11', 'preventif', 1500000, 'Patching + cek RAID bulanan'],
            ['A-106', '2026-11-12', 'korektif', 450000, 'Ganti keyboard ThinkPad'],
        ] as [$kode, $tgl, $jenis, $biaya, $cat]) {
            RiwayatService::firstOrCreate(
                ['riwayat_service_id_aset' => $a($kode), 'riwayat_service_tanggal' => Carbon::parse($tgl)],
                ['riwayat_service_id_teknisi' => $teknisi[array_rand($teknisi)],
                 'riwayat_service_jenis' => $jenis, 'riwayat_service_biaya' => $biaya,
                 'riwayat_service_catatan' => $cat]
            );
            $this->n++;
        }
        JadwalService::firstOrCreate(
            ['jadwal_service_id_aset' => $a('A-002'), 'jadwal_service_tanggal_mulai' => Carbon::create(2026, 11, 1)],
            ['jadwal_service_tanggal_jatuh_tempo' => Carbon::create(2027, 5, 1),
             'jadwal_service_interval_bulan' => 6, 'jadwal_service_status' => 'aktif',
             'jadwal_service_tanggal_terakhir' => Carbon::create(2026, 11, 1)]
        );
        $this->n++;

        $this->command->info('ItDummyTransaksiSeeder: '.$this->n.' record dummy IT.');
    }
}
