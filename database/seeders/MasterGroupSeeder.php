<?php

namespace Database\Seeders;

use App\Models\MasterGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterGroupSeeder extends Seeder
{
    public function run(): void
    {
        // ambil user dengan role teknisi (1 table)
        $teknisiUsers = User::where('role', 'teknisi')->orderBy('name')->get(['id','name']);
        if ($teknisiUsers->isEmpty()) {
            $this->command->warn('Tidak ada user role=teknisi, seeder dilewati. Buat user role teknisi dulu.');
            return;
        }

        $groups = [
            [
                'kode' => 'GRP-TEKNISI-IT',
                'nama' => 'Teknisi IT',
                'keterangan' => 'Group untuk aset IT / Server / Komputer — notifikasi tiket IT',
                'status' => 'aktif',
                'user_ids' => $teknisiUsers->pluck('id')->all(), // semua teknisi
            ],
            [
                'kode' => 'GRP-TEKNISI-UMUM',
                'nama' => 'Teknisi Umum',
                'keterangan' => 'Group teknisi umum untuk aset non-IT',
                'status' => 'aktif',
                'user_ids' => $teknisiUsers->take(1)->pluck('id')->all(),
            ],
            [
                'kode' => 'GRP-TEKNISI-KOMPUTER',
                'nama' => 'Teknisi Komputer',
                'keterangan' => 'Khusus aset komputer/laptop — sebelumnya Teknisi Komputer (master_group_id 3)',
                'status' => 'aktif',
                'user_ids' => $teknisiUsers->pluck('id')->all(),
            ],
        ];

        foreach ($groups as $g) {
            $group = MasterGroup::updateOrCreate(
                ['master_group_kode' => $g['kode']],
                [
                    'master_group_nama' => $g['nama'],
                    'master_group_keterangan' => $g['keterangan'],
                    'master_group_status' => $g['status'],
                ]
            );

            // sync users (1 table) — hapus lama, insert baru
            DB::table('master_group_user')->where('master_group_user_id_group', $group->master_group_id)->delete();
            foreach ($g['user_ids'] as $uid) {
                DB::table('master_group_user')->insert([
                    'master_group_user_id_group' => $group->master_group_id,
                    'master_group_user_id_user' => $uid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // untuk kompatibilitas lama, kosongkan pivot teknisi (sudah tidak dipakai, teknisi ambil dari users)
            // DB::table('master_group_teknisi')->where('master_group_teknisi_id_group', $group->master_group_id)->delete();

            $this->command->info("MasterGroup {$group->master_group_kode} → {$group->master_group_nama} : ".count($g['user_ids'])." user teknisi");
        }

        // update contoh aset A-002 (Server Dell R740) agar pakai group baru yang berbasis users
        $asetSample = \App\Models\Aset::where('aset_kode', 'A-002')->first();
        if ($asetSample) {
            $itGroup = MasterGroup::where('master_group_kode', 'GRP-TEKNISI-IT')->first();
            if ($itGroup) {
                $asetSample->update(['aset_id_group' => $itGroup->master_group_id]);
                $this->command->info("Aset {$asetSample->aset_kode} di-assign ke group {$itGroup->master_group_kode}");
            }
        }
    }
}
