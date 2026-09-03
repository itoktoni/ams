<?php

namespace Database\Seeders;

use App\Models\Teknisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OnboardingSeeder extends Seeder
{
    public function run(): void
    {
        $pass = bcrypt(env('PASSWORD', 'password'));
        $now = now();

        $users = [
            [
                'email' => 'admin@ams.test',
                'name' => 'Admin AMS',
                'role' => 'admin',
                'phone' => '0811000001',
            ],
            [
                'email' => 'supervisor@ams.test',
                'name' => 'Supervisor Approval',
                'role' => 'supervisor',
                'phone' => '0811000002',
            ],
            [
                'email' => 'teknisi@ams.test',
                'name' => 'Budi Teknisi',
                'role' => 'teknisi',
                'phone' => '0811000003',
            ],
            [
                'email' => 'karyawan@ams.test',
                'name' => 'Karyawan Pengguna',
                'role' => 'pengguna_aset',
                'phone' => '0811000004',
            ],
            [
                'email' => 'customer@ams.test',
                'name' => 'Customer Lelang',
                'role' => 'customer',
                'phone' => '0811000005',
            ],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'role' => $u['role'],
                    'phone' => $u['phone'],
                    'password' => $pass,
                    'verified_at' => $now,
                    'email_verified_at' => $now,
                ]
            );
        }

        // Link teknisi user -> teknisi table (buat profil teknisi untuk login teknisi@ams.test)
        $teknisiUser = User::where('email', 'teknisi@ams.test')->first();
        if ($teknisiUser) {
            \Illuminate\Support\Facades\DB::table('teknisi')->updateOrInsert(
                ['teknisi_kode' => 'T-ONB'],
                [
                    'teknisi_nama' => 'Budi Teknisi',
                    'teknisi_telepon' => '0811000003',
                    'teknisi_keahlian' => json_encode(['it', 'elektrikal']),
                    'teknisi_zona' => json_encode(['pusat']),
                    'teknisi_status' => 'tersedia',
                    'teknisi_latitude' => -6.2,
                    'teknisi_longitude' => 106.8,
                    'teknisi_rating' => 0,
                    'teknisi_total_tiket' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $this->command->info('OnboardingSeeder: 5 users (admin/supervisor/teknisi/karyawan/customer) ready — web login, password from env PASSWORD (default password). Customer hanya boleh lelang & public (redirect).');
    }
}
