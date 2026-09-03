<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['IT', 'Information Technology', 50000000, 'bulanan'],
            ['FIN', 'Finance', 30000000, 'bulanan'],
            ['HRD', 'Human Resources', 15000000, 'bulanan'],
            ['OPS', 'Operations', 40000000, 'bulanan'],
            ['MKT', 'Marketing', 20000000, 'bulanan'],
            ['LOG', 'Logistics', 35000000, 'bulanan'],
            ['ENG', 'Engineering', 60000000, 'bulanan'],
            ['GA', 'General Affairs', 25000000, 'bulanan'],
        ];

        $map = [];
        foreach ($data as [$kode, $nama, $budget, $periode]) {
            $map[$kode] = Department::firstOrCreate(
                ['department_kode' => $kode],
                ['department_nama' => $nama, 'department_budget' => $budget, 'department_budget_terpakai' => 0, 'department_periode' => $periode]
            );
        }

        $assign = [
            'developer' => 'IT',
            'admin' => 'IT',
            'supervisor' => 'OPS',
            'teknisi' => 'ENG',
            'pengguna_aset' => 'IT',
            'user' => 'IT',
            'customer' => null,
        ];

        foreach (User::whereNull('department_id')->get() as $user) {
            $kode = $assign[$user->role] ?? null;
            if ($kode && isset($map[$kode])) {
                $user->update(['department_id' => $map[$kode]->department_id]);
            }
        }

        $this->command->info('DepartmentSeeder: '.count($data).' department ready.');
    }
}
