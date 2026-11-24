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

class KursiStaffSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        $kat = KategoriAset::firstOrCreate(
            ['aset_kategori_kode' => 'FURNITUR'],
            [
                'aset_kategori_nama' => 'Furnitur',
                'aset_kategori_masa_manfaat' => 60,
                'aset_kategori_metode_penyusutan' => 'garis_lurus',
                'aset_kategori_custom_fields' => [
                    ['label' => 'Jenis', 'type' => 'text'],
                    ['label' => 'Bahan', 'type' => 'select', 'options' => 'Kayu,Aluminium,Plastik,Besi,Fabric,Mesh'],
                    ['label' => 'Jumlah', 'type' => 'number'],
                    ['label' => 'Ruangan', 'type' => 'text'],
                ],
            ]
        );
        $kat->refresh();
        $keyMap = [];
        foreach (($kat->aset_kategori_custom_fields ?? []) as $f) {
            $keyMap[$f['label']] = $f['key'];
        }

        $lokPusat = LokasiAset::firstOrCreate(
            ['aset_lokasi_kode' => 'PST'],
            ['aset_lokasi_nama' => 'Kantor Pusat', 'aset_lokasi_alamat' => 'Jl. Merdeka 1', 'aset_lokasi_zona' => 'pusat']
        );
        $lokOps = LokasiAset::firstOrCreate(
            ['aset_lokasi_kode' => 'GA'],
            ['aset_lokasi_nama' => 'Ruang GA', 'aset_lokasi_parent_id' => $lokPusat->aset_lokasi_id, 'aset_lokasi_zona' => 'REGIONAL IX']
        );
        $lokAdm = LokasiAset::firstOrCreate(
            ['aset_lokasi_kode' => 'ADM'],
            ['aset_lokasi_nama' => 'Ruang Administrasi', 'aset_lokasi_parent_id' => $lokPusat->aset_lokasi_id, 'aset_lokasi_zona' => 'REGIONAL IX']
        );
        $vendor = Vendor::firstOrCreate(
            ['vendor_kode' => 'V-004'],
            ['vendor_nama' => 'Informa', 'vendor_telepon' => '021-444', 'vendor_kategori' => 'furnitur', 'vendor_rating' => 4.3]
        );

        $mereks = ['Informa', 'Informa', 'Informa', 'Olympic', 'Chitose', 'Lion'];
        $bahans = ['Fabric', 'Mesh', 'Plastik', 'Aluminium'];
        $ruangans = ['Ruang GA'];
        $lokasis = [$lokOps];
        $kondisis = ['baik', 'baik', 'baik', 'baik', 'kurang_baik', 'rusak_ringan'];

        $penyusutan = new PenyusutanService;
        $created = 0;
        for ($i = 1; $i <= 50; $i++) {
            $kode = 'KRS-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $merek = $mereks[($i - 1) % count($mereks)];
            $bahan = $bahans[($i - 1) % count($bahans)];
            $ruangan = $ruangans[($i - 1) % count($ruangans)];
            $lok = $lokasis[($i - 1) % count($lokasis)];
            $kondisi = $kondisis[($i - 1) % count($kondisis)];
            $status = $kondisi === 'rusak_ringan' ? 'rusak' : 'aktif';
            $harga = 750000 + ((($i - 1) % 5) * 100000);
            $mulai = Carbon::today()->subMonths(1 + (($i - 1) % 18))->startOfMonth();

            $cf = [];
            foreach (['Jenis' => 'Kursi Staff', 'Bahan' => $bahan, 'Jumlah' => 1, 'Ruangan' => $ruangan] as $label => $val) {
                if (isset($keyMap[$label])) {
                    $cf[$keyMap[$label]] = (string) $val;
                }
            }

            $aset = Aset::updateOrCreate(
                ['aset_kode' => $kode],
                [
                    'aset_nama' => 'Kursi Staff',
                    'aset_id_kategori' => $kat->aset_kategori_id,
                    'aset_id_lokasi' => $lok->aset_lokasi_id,
                    'aset_id_vendor' => $vendor->vendor_id,
                    'aset_id_penanggung_jawab' => $userId,
                    'aset_merek' => $merek,
                    'aset_model' => 'STF-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'aset_nomor_seri' => 'SN-KRS-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'aset_tanggal_perolehan' => $mulai,
                    'aset_harga_perolehan' => $harga,
                    'aset_nilai_sisa' => round($harga * 0.1, 2),
                    'aset_masa_manfaat' => 60,
                    'aset_metode_penyusutan' => 'garis_lurus',
                    'aset_tanggal_mulai_susut' => $mulai,
                    'aset_status' => $status,
                    'aset_kondisi' => $kondisi,
                    'aset_custom_fields' => $cf,
                    'aset_jam_pakai' => 0,
                    'aset_catatan' => 'Kursi staff '.$ruangan,
                ]
            );

            if ($aset->wasRecentlyCreated) {
                $penyusutan->jalankan($aset);
                LogStatusAset::create([
                    'log_status_aset_id_aset' => $aset->aset_id,
                    'log_status_aset_status_dari' => 'baru',
                    'log_status_aset_status_ke' => $status,
                    'log_status_aset_actor' => $userId,
                    'log_status_aset_catatan' => 'Status awal (KursiStaffSeeder)',
                ]);
                $created++;
            }
        }

        $this->command->info('KursiStaffSeeder: '.$created.' aset kursi staff baru (total KRS-* = 50).');
    }
}
