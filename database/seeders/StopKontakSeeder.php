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

class StopKontakSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        $kat = KategoriAset::firstOrCreate(
            ['aset_kategori_kode' => 'ELEKTRIK'],
            [
                'aset_kategori_nama' => 'Peralatan Elektrik',
                'aset_kategori_masa_manfaat' => 60,
                'aset_kategori_metode_penyusutan' => 'garis_lurus',
                'aset_kategori_custom_fields' => [
                    ['label' => 'Jenis', 'type' => 'text'],
                    ['label' => 'Daya Watt', 'type' => 'number'],
                    ['label' => 'Jumlah Lubang', 'type' => 'number'],
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
            ['aset_lokasi_kode' => 'OPS'],
            ['aset_lokasi_nama' => 'Ruang Operasional', 'aset_lokasi_parent_id' => $lokPusat->aset_lokasi_id, 'aset_lokasi_zona' => 'pusat']
        );
        $vendor = Vendor::firstOrCreate(
            ['vendor_kode' => 'V-005'],
            ['vendor_nama' => 'Toko Listrik Jaya', 'vendor_telepon' => '021-555', 'vendor_kategori' => 'elektrik', 'vendor_rating' => 4.0]
        );

        $mereks = ['Broco', 'Panasonic', 'Schneider', 'Uticon'];
        $ruangans = ['Ruang Operasional', 'Ruang Administrasi', 'Ruang Rapat', 'Ruang Server'];
        $lokasis = [$lokOps, $lokPusat];

        $penyusutan = new PenyusutanService;
        $created = 0;
        for ($i = 1; $i <= 20; $i++) {
            $kode = 'STK-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $merek = $mereks[($i - 1) % count($mereks)];
            $ruangan = $ruangans[($i - 1) % count($ruangans)];
            $lok = $lokasis[($i - 1) % count($lokasis)];
            $lubang = [3, 4, 5, 6][($i - 1) % 4];
            $harga = 100000 + ((($i - 1) % 4) * 50000);
            $mulai = Carbon::today()->subMonths(1 + (($i - 1) % 12))->startOfMonth();

            $cf = [];
            foreach (['Jenis' => 'Stop Kontak', 'Daya Watt' => 2200, 'Jumlah Lubang' => $lubang, 'Ruangan' => $ruangan] as $label => $val) {
                if (isset($keyMap[$label])) {
                    $cf[$keyMap[$label]] = (string) $val;
                }
            }

            $aset = Aset::updateOrCreate(
                ['aset_kode' => $kode],
                [
                    'aset_nama' => 'Stop Kontak',
                    'aset_id_kategori' => $kat->aset_kategori_id,
                    'aset_id_lokasi' => $lok->aset_lokasi_id,
                    'aset_id_vendor' => $vendor->vendor_id,
                    'aset_id_penanggung_jawab' => $userId,
                    'aset_merek' => $merek,
                    'aset_model' => 'STK-'.$lubang.'LB',
                    'aset_nomor_seri' => 'SN-STK-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'aset_tanggal_perolehan' => $mulai,
                    'aset_harga_perolehan' => $harga,
                    'aset_nilai_sisa' => round($harga * 0.1, 2),
                    'aset_masa_manfaat' => 60,
                    'aset_metode_penyusutan' => 'garis_lurus',
                    'aset_tanggal_mulai_susut' => $mulai,
                    'aset_status' => 'aktif',
                    'aset_kondisi' => 'baik',
                    'aset_custom_fields' => $cf,
                    'aset_jam_pakai' => 0,
                    'aset_catatan' => 'Stop kontak '.$ruangan,
                ]
            );

            if ($aset->wasRecentlyCreated) {
                $penyusutan->jalankan($aset);
                LogStatusAset::create([
                    'log_status_aset_id_aset' => $aset->aset_id,
                    'log_status_aset_status_dari' => 'baru',
                    'log_status_aset_status_ke' => 'aktif',
                    'log_status_aset_actor' => $userId,
                    'log_status_aset_catatan' => 'Status awal (StopKontakSeeder)',
                ]);
                $created++;
            }
        }

        $this->command->info('StopKontakSeeder: '.$created.' aset stop kontak baru (total STK-* = 20).');
    }
}
