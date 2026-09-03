<?php

namespace Database\Seeders;

use App\Models\Aset;
use App\Models\PenawaranPenjualan;
use App\Models\PenjualanAset;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LelangSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        // Ambil aset yang belum dilelang — pilih 10 berbeda, prioritaskan kondisi baik/rusak atau masa pakai hampir habis
        $availableAsets = Aset::with('hasKategori')
            ->whereNotIn('aset_id', PenjualanAset::pluck('penjualan_aset_id_aset'))
            ->orderBy('aset_id')
            ->limit(10)
            ->get();

        // Fallback jika kurang dari 10 (misal semua sudah dilelang) — ambil random
        if ($availableAsets->count() < 10) {
            $extra = Aset::whereNotIn('aset_id', $availableAsets->pluck('aset_id'))
                ->inRandomOrder()->limit(10 - $availableAsets->count())->get();
            $availableAsets = $availableAsets->merge($extra);
        }

        if ($availableAsets->isEmpty()) {
            $this->command->warn('Tidak ada aset untuk dilelang.');
            return;
        }

        $statuses = ['ditawarkan', 'ditawarkan', 'ditawarkan', 'negosiasi', 'disetujui', 'terverifikasi', 'diajukan'];
        $alasanTemplates = [
            'Masa manfaat habis — layak lelang sesuai kebijakan penghapusan.',
            'Kondisi kurang baik / rusak berat — biaya perbaikan tidak ekonomis, diajukan lelang.',
            'Penggantian unit baru — aset lama dilelang terbuka untuk umum.',
            'Aset idle >12 bulan — optimalisasi portofolio, dilelang.',
            'Upgrade teknologi — versi lama dilelang.',
        ];

        $bidderNames = [
            ['Budi Santoso', '0812-1001-001'],
            ['Siti Rahayu', '0812-1001-002'],
            ['CV Maju Jaya', '021-555-0101'],
            ['PT Sinar Abadi', '021-555-0102'],
            ['Andi Wijaya', '0813-2002-003'],
            ['Toko Berkah', '0813-2002-004'],
            ['Rina Marlina', '0821-3003-005'],
            ['UD Sumber Rejeki', '0821-3003-006'],
            ['Hendra Gunawan', '0856-4004-007'],
            ['Koperasi Sejahtera', '0856-4004-008'],
        ];

        $created = 0;
        $offersCreated = 0;

        foreach ($availableAsets->take(10) as $idx => $aset) {
            $num = sprintf('LE-%03d', PenjualanAset::count() + 1);
            $nilaiBuku = (int) ($aset->aset_harga_perolehan * (rand(15, 40) / 100));
            $appraisal = (int) ($nilaiBuku * (rand(60, 110) / 100));
            if ($appraisal < 500000) $appraisal = rand(800000, 2500000);
            $status = $statuses[array_rand($statuses)];
            $alasan = $alasanTemplates[array_rand($alasanTemplates)];

            $tanggalRequest = Carbon::now()->subDays(rand(1, 20));

            $penjualan = PenjualanAset::updateOrCreate(
                ['penjualan_aset_nomor' => $num],
                [
                    'penjualan_aset_id_aset' => $aset->aset_id,
                    'penjualan_aset_alasan' => $alasan,
                    'penjualan_aset_nilai_buku' => $nilaiBuku,
                    'penjualan_aset_harga_appraisal' => $appraisal,
                    'penjualan_aset_harga_jual' => null, // belum terjual
                    'penjualan_aset_status' => $status,
                    'penjualan_aset_tanggal_request' => $tanggalRequest,
                    'penjualan_aset_tanggal_jual' => null,
                    'penjualan_aset_tanggal_serah_terima' => null,
                    'penjualan_aset_penerima' => null,
                    'penjualan_aset_kondisi' => $aset->aset_kondisi ?? 'kurang_baik',
                    'penjualan_aset_catatan' => 'Lelang publik — asset: ' . $aset->aset_nama . ' (' . ($aset->hasKategori->aset_kategori_nama ?? '-') . ')',
                ]
            );

            if ($penjualan->wasRecentlyCreated) $created++;

            // Buat 2-5 penawaran per lelang (agar ada kompetisi, pemenang = tertinggi)
            // Hanya untuk status yang masih open
            $isOpen = in_array($status, ['ditawarkan', 'negosiasi', 'disetujui', 'terverifikasi', 'diajukan']);
            if (! $isOpen) continue;

            // Hindari duplikasi penawaran jika sudah ada
            if ($penjualan->hasPenawaran()->exists()) continue;

            $bidCount = rand(2, 5);
            $base = $appraisal;
            $current = $base;
            $shuffled = collect($bidderNames)->shuffle()->take($bidCount);

            foreach ($shuffled as $bIdx => $bidder) {
                // kenaikan 3-15% dari penawaran sebelumnya
                $current = (int) ($current * (1 + rand(3, 15) / 100));
                // waktu mundur: bid terbaru paling akhir
                $waktu = Carbon::now()->subHours(rand(1, 72))->subMinutes(rand(0, 59));

                PenawaranPenjualan::create([
                    'penawaran_penjualan_id_penjualan' => $penjualan->penjualan_aset_id,
                    'penawaran_penjualan_id_user' => $idx % 2 === 0 ? $userId : null, // sebagian link ke user login
                    'penawaran_penjualan_nama_pembeli' => $bidder[0],
                    'penawaran_penjualan_kontak' => $bidder[1],
                    'penawaran_penjualan_harga' => $current,
                    'penawaran_penjualan_tanggal' => $waktu->toDateString(),
                    'penawaran_penjualan_waktu' => $waktu,
                    'penawaran_penjualan_status' => $bIdx === $bidCount - 1 ? 'tertinggi' : 'diajukan',
                    'penawaran_penjualan_hasil' => $bIdx === $bidCount - 1 ? 'Pemenang sementara' : 'Tersalip',
                ]);
                $offersCreated++;
            }
        }

        $this->command->info("LelangSeeder: {$created} penjualan baru + {$offersCreated} penawaran. Total lelang: " . PenjualanAset::count());
    }
}