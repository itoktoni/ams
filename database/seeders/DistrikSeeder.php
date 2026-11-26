<?php

namespace Database\Seeders;

use App\Models\Aset;
use App\Models\KategoriAset;
use App\Models\LokasiAset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrikSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        // 5 lokasi: Distrik 1 – Distrik 5.
        $distriks = [];
        for ($d = 1; $d <= 5; $d++) {
            $distriks[$d] = LokasiAset::firstOrCreate(
                ['aset_lokasi_kode' => 'DST'.$d],
                ['aset_lokasi_nama' => 'Distrik '.$d, 'aset_lokasi_zona' => 'distrik']
            );
        }

        $kat = KategoriAset::firstOrCreate(
            ['aset_kategori_kode' => 'FURN'],
            [
                'aset_kategori_nama' => 'Furniture',
                'aset_kategori_masa_manfaat' => 60,
                'aset_kategori_metode_penyusutan' => 'garis_lurus',
            ]
        );

        // Data inventaris Distrik 1: [uraian, nomor aset, jumlah unit, tgl BA, no BA].
        $rows = [
            ['Meja kerja 1/2 Biro', '031/9BSH/2026', 1, '2026-10-19', '12/9BSH/APN/X/2026'],
            ['Meja kerja 1/2 Biro', '034/9BSH/2026', 1, '2026-10-19', '12/9BSH/APN/X/2026'],
            ['Kursi Chairman', '033/9BSH/2026', 1, '2026-10-19', '12/9BSH/APN/X/2026'],
            ['Kursi Chairman', '050/9BSH/2026', 1, '2026-10-19', '12/9BSH/APN/X/2026'],
            ['Meja Kerja 1/2 Biro', '044/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Meja Makan', '062/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Chairman', '047/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Chairman', '049/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Chairman', '032/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Chairman', '048/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Makan', '066/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Makan', '067/9BSH/2026', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Makan', '(tidak tercantum / ....)', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kursi Makan', '(tidak tercantum)', 1, '2026-11-16', '023/9BSH/APN/X/2026'],
            ['Kabinet/Laci Meja', '071/9BSH/2026', 1, '2026-11-23', '025/9BSH/APN/XI/2026'],
            ['Kursi Chairman', '(kosong)', 1, '2026-11-23', '025/9BSH/APN/XI/2026'],
            ['Meja Kerja 1/2 Biro', '040/9BSH/2026', 1, '2026-11-23', '025/9BSH/APN/XI/2026'],
            ['Kabinet/Laci Meja', '073/9BSH/2026', 1, '2026-11-23', '025/9BSH/APN/XI/2026'],
            ['Meja Kerja 1/2 Biro', '043/9BSH/2026', 1, '2026-11-23', '025/9BSH/APN/XI/2026'],
        ];

        $created = 0;
        foreach ($rows as $i => [$uraian, $nomor, $qty, $tgl, $ba]) {
            $kode = 'DST1-'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            $aset = Aset::updateOrCreate(
                ['aset_kode' => $kode],
                [
                    'aset_nama' => $uraian,
                    'aset_label_lama' => $nomor,
                    'aset_id_kategori' => $kat->aset_kategori_id,
                    'aset_id_lokasi' => $distriks[1]->aset_lokasi_id,
                    'aset_id_penanggung_jawab' => $userId,
                    'aset_tanggal_perolehan' => $tgl,
                    'aset_status' => 'aktif',
                    'aset_kondisi' => 'baik',
                    'aset_catatan' => "Jumlah unit: {$qty}. BA {$tgl} (No. {$ba}).",
                ]
            );
            if ($aset->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->command->info("DistrikSeeder: 5 lokasi distrik + {$created} aset baru di Distrik 1.");
    }
}
