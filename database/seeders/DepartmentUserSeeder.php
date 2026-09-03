<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentUserSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['IT', 'Information Technology', 50000000, 'bulanan'],
            ['FIN', 'Finance', 30000000, 'bulanan'],
            ['HRD', 'Human Resources', 15000000, 'bulanan'],
            ['OPS', 'Operations', 40000000, 'bulanan'],
            ['MKT', 'Marketing', 20000000, 'bulanan'],
            ['LOG', 'Logistics', 35000000, 'bulanan'],
            ['ENG', 'Engineering', 60000000, 'bulanan'],
            ['GA', 'General Affairs', 25000000, 'bulanan'],
        ];

        $deptMap = [];
        foreach ($departments as [$kode, $nama, $budget, $periode]) {
            $deptMap[$kode] = Department::firstOrCreate(
                ['department_kode' => $kode],
                ['department_nama' => $nama, 'department_budget' => $budget, 'department_budget_terpakai' => 0, 'department_periode' => $periode]
            );
        }

        $users = [
            ['name' => 'Admin IT', 'email' => 'it@test.com', 'role' => 'admin', 'dept' => 'IT'],
            ['name' => 'Finance User', 'email' => 'finance@test.com', 'role' => 'user', 'dept' => 'FIN'],
            ['name' => 'HRD User', 'email' => 'hrd@test.com', 'role' => 'user', 'dept' => 'HRD'],
            ['name' => 'Ops Supervisor', 'email' => 'ops@test.com', 'role' => 'supervisor', 'dept' => 'OPS'],
            ['name' => 'Marketing User', 'email' => 'mkt@test.com', 'role' => 'user', 'dept' => 'MKT'],
            ['name' => 'Logistics User', 'email' => 'log@test.com', 'role' => 'user', 'dept' => 'LOG'],
            ['name' => 'Engineering User', 'email' => 'eng@test.com', 'role' => 'teknisi', 'dept' => 'ENG'],
            ['name' => 'GA User', 'email' => 'ga@test.com', 'role' => 'user', 'dept' => 'GA'],
            ['name' => 'IT User 2', 'email' => 'it2@test.com', 'role' => 'pengguna_aset', 'dept' => 'IT'],
            ['name' => 'Finance Supervisor', 'email' => 'fin-sup@test.com', 'role' => 'supervisor', 'dept' => 'FIN'],
        ];

        foreach ($users as $u) {
            $dept = $deptMap[$u['dept']] ?? null;
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'role' => $u['role'],
                    'department_id' => $dept?->department_id,
                    'password' => bcrypt(env('PASSWORD', 'password')),
                    'verified_at' => now(),
                    'email_verified_at' => now(),
                ]
            );
            if ($dept) {
                User::where('email', $u['email'])->update(['department_id' => $dept->department_id]);
            }
        }

        $roleToDept = [
            'developer' => 'IT',
            'admin' => 'IT',
            'supervisor' => 'OPS',
            'teknisi' => 'ENG',
        ];
        foreach (User::whereNull('department_id')->get() as $user) {
            $kode = $roleToDept[$user->role] ?? null;
            if ($kode && isset($deptMap[$kode])) {
                $user->update(['department_id' => $deptMap[$kode]->department_id]);
            }
        }

        $this->command->info('DepartmentUserSeeder: '.count($departments).' department + '.count($users).' users seeded & linked.');
    }
}
