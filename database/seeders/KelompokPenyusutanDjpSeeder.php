<?php

namespace Database\Seeders;

use App\Models\KelompokPenyusutan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelompokPenyusutanDjpSeeder extends Seeder
{
    public function run(): void
    {
        // Sumber resmi: pajak.go.id/id/penyusutan-dan-amortisasi (PMK 72/2023, update 28 Mei 2025)
        // Detail jenis harta: PMK 96/PMK.03/2009 Lampiran + ringkasan Klikpajak.id/blog/jenis-harta-penyusutan-fiskal/
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        KelompokPenyusutan::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $rows = [
                [
                    'kelompok_penyusutan_kode' => 'DJP-K1',
                    'kelompok_penyusutan_nama' => 'Kelompok 1 — Bukan Bangunan (4 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 4,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 25.00,
                    'kelompok_penyusutan_keterangan' => 'GL 25% | SM 50%. Semua usaha: mebel kayu/rotan bukan bangunan; mesin kantor (tik/hitung/duplikator/fotokopi/akunting, komputer, printer, scanner); amplifier/tape/video/TV; motor/sepeda/becak; dies/jigs/mould; telepon/fax/ponsel inventaris; dapur; alat tenaga manusia (cangkul/garu); mesin ringan mamin; taksi/bus/truk angkutan umum; semi-konduktor (flash memory tester, pose checker); tambat air (anchor/chain/rope/buoy); seluler Base Station Controller.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-K2',
                    'kelompok_penyusutan_nama' => 'Kelompok 2 — Bukan Bangunan (8 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 8,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 12.50,
                    'kelompok_penyusutan_keterangan' => 'GL 12,5% | SM 25%. Semua usaha: mebel kayu + AC/kipas; mobil/bus/truk/speed boat; container. Pertanian: traktor & mesin olah tani/ternak/ikan. Mamin: susu/ikan, minyak kelapa, kopi/beras/gandum, minuman. Mesin ringan (jahit/pompa), kayu, konstruksi (truk berat/dump/crane/buldozer), kapal 0-100 DWT & perahu layar 0-250 DWT + kapal balon, telepon/telegraf/radio, semi-konduktor lengkap (auto loader/oven/die bonder/wire bonder), spooling machines, MSC/BTS/antena seluler.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-K3',
                    'kelompok_penyusutan_nama' => 'Kelompok 3 — Bukan Bangunan (16 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 16,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 6.25,
                    'kelompok_penyusutan_keterangan' => 'GL 6,25% | SM 12,5%. DEFAULT jika harta tak tercantum di lampiran PMK 96/72. Tambang (mesin tambang), tekstil (preparation/bleaching/dyeing/printing/finishing), kayu (penggergajian), kimia (plastik/karet/kulit), mesin menengah/berat (mesin mobil/kapal), kapal 100-1000 DWT, perahu layar >250 DWT & pesawat/helikopter, radio navigasi/radar/kendali jarak jauh.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-K4',
                    'kelompok_penyusutan_nama' => 'Kelompok 4 — Bukan Bangunan (20 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 20,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 5.00,
                    'kelompok_penyusutan_keterangan' => 'GL 5% | SM 10%. Konstruksi: mesin berat. Transportasi: lokomotif uang/listrik/rel lain, kereta/gerbong/kontainer tarik, kapal penumpang/barang >1000 DWT, kapal tunda/suar/pemadam/keruk/dermaga >1000 DWT, dok terapung.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-BP',
                    'kelompok_penyusutan_nama' => 'Bangunan Permanen (20 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 20,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 5.00,
                    'kelompok_penyusutan_keterangan' => 'Hanya GL 5% (SM dilarang). Jika >20thn boleh 20thn atau masa sebenarnya taat asas (lapor DJP s.d. 30 Apr 2024). Permanen = tidak dapat dipindah.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-BT',
                    'kelompok_penyusutan_nama' => 'Bangunan Tidak Permanen (10 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 10,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 10.00,
                    'kelompok_penyusutan_keterangan' => 'Hanya GL 10%. Contoh barak kayu, bangunan semi permanen dapat dipindah.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-A1',
                    'kelompok_penyusutan_nama' => 'Tak Berwujud Kelompok 1 (4 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 4,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 25.00,
                    'kelompok_penyusutan_keterangan' => 'Amortisasi GL 25% | SM 50%. Software aplikasi KHUSUS (perbankan, pasar modal, perhotelan, RS, penerbangan) + peningkatan kapasitas tetap K1. Software UMUM = beban sekaligus (bukan amortisasi). Hak 4thn.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-A2',
                    'kelompok_penyusutan_nama' => 'Tak Berwujud Kelompok 2 (8 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 8,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 12.50,
                    'kelompok_penyusutan_keterangan' => 'Amortisasi GL 12,5% | SM 25%. Hak cipta/paten/waralaba/lisensi masa 8thn.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-A3',
                    'kelompok_penyusutan_nama' => 'Tak Berwujud Kelompok 3 (16 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 16,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 6.25,
                    'kelompok_penyusutan_keterangan' => 'Amortisasi GL 6,25% | SM 12,5%.',
                ],
                [
                    'kelompok_penyusutan_kode' => 'DJP-A4',
                    'kelompok_penyusutan_nama' => 'Tak Berwujud Kelompok 4 (20 Tahun)',
                    'kelompok_penyusutan_masa_manfaat' => 20,
                    'kelompok_penyusutan_metode' => 'garis_lurus',
                    'kelompok_penyusutan_tarif' => 5.00,
                    'kelompok_penyusutan_keterangan' => 'Amortisasi GL 5% | SM 10%. Jika >20thn → pakai K4 atau masa sebenarnya taat asas.',
                ],
            ];

        foreach ($rows as $r) {
            KelompokPenyusutan::create($r);
        }

        $this->command->info('KelompokPenyusutan DJP (PMK 72/2023 + PMK 96/2009 Klikpajak detail) selesai: '.KelompokPenyusutan::count().' kelompok.');
    }
}
