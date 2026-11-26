<?php

namespace Database\Seeders;

use App\Models\BbmKuota;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Master user + kendaraan BBM (unit COP) dan kuota bulanan.
 * Aman dijalankan ulang (updateOrCreate).
 */
class BbmMasterSeeder extends Seeder
{
    private const UNIT = 'COP';

    private const KUOTA_LITER = 100;

    private const DATA = [
        ['nama' => 'Erwin Romel Simanjuntak', 'email' => 'erwin.romel.simanjuntak@ams.test', 'kendaraan' => 'B 9848 KBB'],
        ['nama' => 'Gatot Sawarna T.M', 'email' => 'gatot.sawarna.tm@ams.test', 'kendaraan' => 'B 9825 KBB'],
        ['nama' => 'Muh Jajar', 'email' => 'muh.jajar@ams.test', 'kendaraan' => 'B 9104 UBF'],
        ['nama' => 'Hindro Martono', 'email' => 'hindro.martono@ams.test', 'kendaraan' => 'D 8302 FZ'],
        ['nama' => 'Aris Tri Priyanto', 'email' => 'aris.tri.priyanto@ams.test', 'kendaraan' => 'D 8304 FZ'],
        ['nama' => 'Danni Koswara', 'email' => 'danni.koswara@ams.test', 'kendaraan' => 'D 8612 TQ'],
        ['nama' => 'Sujono', 'email' => 'sujono@ams.test', 'kendaraan' => 'B 9891 PBH'],
    ];

    public function run(): void
    {
        $periode = now()->format('Y-m');

        foreach (self::DATA as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['nama'],
                    'role' => 'pengguna_aset',
                    'password' => bcrypt(env('PASSWORD', 'password')),
                    'verified_at' => now(),
                    'email_verified_at' => now(),
                    'unit_kode' => self::UNIT,
                    'jenis_kendaraan' => 'mobil',
                    'kendaraan' => $item['kendaraan'],
                ]
            );

            BbmKuota::updateOrCreate(
                [
                    'bbm_kuota_id_user' => $user->id,
                    'bbm_kuota_periode' => $periode,
                ],
                ['bbm_kuota_liter' => self::KUOTA_LITER]
            );
        }
    }
}
