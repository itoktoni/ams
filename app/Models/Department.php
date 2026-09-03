<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Department extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'department';
    protected $primaryKey = 'department_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'department_kode',
        'department_nama',
        'department_budget',
        'department_budget_terpakai',
        'department_periode',
    ];

    public static $filterColumns = [
        'department_kode' => 'Kode',
        'department_nama' => 'Nama',
        'department_periode' => 'Periode',
    ];

    public static $sortColumns = [
        'department_kode',
        'department_nama',
        'department_budget',
        'department_budget_terpakai',
        'department_periode',
    ];

    protected function casts(): array
    {
        return [
            'department_budget' => 'decimal:2',
            'department_budget_terpakai' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'department_nama';
    }

    public function rules(): array
    {
        return [
            'department_kode' => 'required|string|max:20',
            'department_nama' => 'required|string|max:100',
            'department_budget' => 'required|numeric|min:0',
            'department_budget_terpakai' => 'nullable|numeric|min:0',
            'department_periode' => 'nullable|string|max:20',
        ];
    }

    public function hasUsers()
    {
        return $this->hasMany(User::class, 'department_id', 'department_id');
    }

    public function hasPermintaan()
    {
        return $this->hasMany(PermintaanSukuCadang::class, 'department_id', 'department_id');
    }

    /**
     * Sisa budget berdasarkan pemakaian nyata (sudah disetujui).
     */
    public function getSisaAttribute(): float
    {
        return (float) $this->department_budget - (float) $this->department_budget_terpakai;
    }

    /**
     * Nilai permintaan yang masih menunggu — belum mengurangi budget, tapi sudah direserve.
     */
    public function getPendingAttribute(): float
    {
        return PermintaanSukuCadang::pendingDepartment($this->department_id);
    }

    /**
     * Sisa yang benar-benar masih bisa dipakai = budget - terpakai - menunggu.
     */
    public function getTersediaAttribute(): float
    {
        return (float) $this->department_budget - (float) $this->department_budget_terpakai - $this->pending;
    }

    public function getSisaFormattedAttribute(): string
    {
        return formatRupiah($this->sisa);
    }

    /**
     * Hitung ulang & simpan department_budget_terpakai dari permintaan yang sudah disetujui.
     */
    public static function syncTerpakai(mixed $departmentId): ?float
    {
        if (! $departmentId) {
            return null;
        }

        $department = static::find($departmentId);

        if (! $department) {
            return null;
        }

        $terpakai = PermintaanSukuCadang::terpakaiDepartment($departmentId);

        static::where('department_id', $departmentId)->update([
            'department_budget_terpakai' => $terpakai,
        ]);

        return $terpakai;
    }

    /**
     * Sinkronisasi semua department — dipakai command `permintaan:sync-budget`.
     */
    public static function syncAllTerpakai(): int
    {
        $count = 0;

        static::query()->pluck('department_id')->each(function ($departmentId) use (&$count) {
            static::syncTerpakai($departmentId);
            $count++;
        });

        return $count;
    }
}
