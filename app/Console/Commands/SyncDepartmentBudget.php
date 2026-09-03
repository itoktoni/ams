<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\PermintaanSukuCadang;
use Illuminate\Console\Command;

class SyncDepartmentBudget extends Command
{
    protected $signature = 'permintaan:sync-budget {--backfill : Isi department_id yang NULL dari department peminta}';

    protected $description = 'Sinkronkan department_budget_terpakai dari permintaan suku cadang yang sudah disetujui';

    public function handle(): int
    {
        $backfilled = 0;

        if ($this->option('backfill')) {
            $backfilled = $this->backfillDepartment();
            $this->info("Permintaan yang department_id-nya diisi ulang: {$backfilled}");
        }

        $orphan = PermintaanSukuCadang::whereNull('department_id')->count();
        if ($orphan > 0) {
            $this->warn("{$orphan} permintaan masih tanpa department dan tidak masuk hitungan budget.");
        }

        $total = Department::syncAllTerpakai();
        $this->info("Department disinkronkan: {$total}");

        foreach (Department::orderBy('department_nama')->get() as $dept) {
            $this->line(sprintf(
                '  %-26s budget %-14s terpakai %-14s menunggu %-14s tersedia %s',
                $dept->department_nama,
                formatRupiah($dept->department_budget),
                formatRupiah($dept->department_budget_terpakai),
                formatRupiah($dept->pending),
                formatRupiah($dept->tersedia),
            ));
        }

        return self::SUCCESS;
    }

    /**
     * Data yang dibuat sebelum kolom department_id ada tidak punya department,
     * sehingga subtotal-nya tidak pernah mengurangi budget.
     */
    private function backfillDepartment(): int
    {
        $count = 0;

        PermintaanSukuCadang::whereNull('department_id')
            ->get()
            ->each(function (PermintaanSukuCadang $record) use (&$count) {
                $departmentId = $record->resolveDepartmentId();

                if (! $departmentId) {
                    return;
                }

                $record->department_id = $departmentId;
                // Simpan tanpa event model supaya tidak sinkron berulang kali
                // (sinkron dijalankan sekali di akhir command).
                $record->saveQuietly();
                $count++;
            });

        return $count;
    }
}
