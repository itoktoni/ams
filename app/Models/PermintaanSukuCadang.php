<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use App\Enums\SukuCadang\StatusPermintaanEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PermintaanSukuCadang extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'permintaan_suku_cadang';

    /**
     * Status yang sudah MENGURANGI budget department (pemakaian nyata).
     * Anggaran baru terpakai setelah permintaan disetujui.
     */
    public const STATUS_PEMAKAIAN = [
        StatusPermintaanEnum::DISETUJUI,
        StatusPermintaanEnum::SEBAGIAN,
        StatusPermintaanEnum::SELESAI,
    ];

    /**
     * Status yang belum mengurangi budget, tapi tetap "direserve"
     * supaya dua permintaan menunggu tidak memakai sisa budget yang sama.
     */
    public const STATUS_RESERVE = [
        StatusPermintaanEnum::MENUNGGU,
    ];

    protected $primaryKey = 'permintaan_suku_cadang_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'permintaan_suku_cadang_nomor',
        'permintaan_suku_cadang_id_tiket',
        'permintaan_suku_cadang_id_suku_cadang',
        'permintaan_suku_cadang_id_peminta',
        'department_id',
        'permintaan_suku_cadang_jumlah',
        'permintaan_suku_cadang_harga',
        'permintaan_suku_cadang_subtotal',
        'permintaan_suku_cadang_status',
        'permintaan_suku_cadang_tanggal_permintaan',
        'permintaan_suku_cadang_catatan',
    ];

    public static $filterColumns = [
        'permintaan_suku_cadang_nomor' => 'Nomor',
        'permintaan_suku_cadang_status' => 'Status',
    ];

    public static $sortColumns = [
        'permintaan_suku_cadang_nomor',
        'permintaan_suku_cadang_jumlah',
        'permintaan_suku_cadang_subtotal',
        'permintaan_suku_cadang_status',
        'permintaan_suku_cadang_tanggal_permintaan',
    ];

    protected function casts(): array
    {
        return [
            'permintaan_suku_cadang_jumlah' => 'decimal:2',
            'permintaan_suku_cadang_harga' => 'decimal:2',
            'permintaan_suku_cadang_subtotal' => 'decimal:2',
            'permintaan_suku_cadang_tanggal_permintaan' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'permintaan_suku_cadang_nomor';
    }

    public function rules(): array
    {
        return [
            'permintaan_suku_cadang_nomor' => 'nullable|string|max:50',
            'permintaan_suku_cadang_id_tiket' => 'nullable|integer',
            'permintaan_suku_cadang_id_suku_cadang' => 'required|integer',
            'permintaan_suku_cadang_id_peminta' => 'nullable|integer',
            'permintaan_suku_cadang_jumlah' => 'required|numeric|min:1',
            'permintaan_suku_cadang_harga' => 'nullable|numeric',
            'permintaan_suku_cadang_subtotal' => 'nullable|numeric',
            'permintaan_suku_cadang_status' => 'nullable|string|max:50',
            'permintaan_suku_cadang_tanggal_permintaan' => 'nullable|date',
            'permintaan_suku_cadang_catatan' => 'nullable|string',
        ];
    }

    public function hasTiket()
    {
        return $this->hasOne(Tiket::class, 'tiket_id', 'permintaan_suku_cadang_id_tiket');
    }

    public function hasSukuCadang()
    {
        return $this->hasOne(SukuCadang::class, 'suku_cadang_id', 'permintaan_suku_cadang_id_suku_cadang');
    }

    public function hasPeminta()
    {
        return $this->hasOne(User::class, 'id', 'permintaan_suku_cadang_id_peminta');
    }

    public function hasDepartment()
    {
        return $this->hasOne(Department::class, 'department_id', 'department_id');
    }

    public function getStatusOptions(): array
    {
        return StatusPermintaanEnum::getOptions();
    }

    /**
     * Total subtotal permintaan yang SUDAH mengurangi budget (disetujui/sebagian/selesai).
     */
    public static function terpakaiDepartment(mixed $departmentId, mixed $exceptId = null): float
    {
        return static::sumByStatusDepartment(static::STATUS_PEMAKAIAN, $departmentId, $exceptId);
    }

    /**
     * Total subtotal permintaan yang masih menunggu (reserve, belum mengurangi budget).
     */
    public static function pendingDepartment(mixed $departmentId, mixed $exceptId = null): float
    {
        return static::sumByStatusDepartment(static::STATUS_RESERVE, $departmentId, $exceptId);
    }

    public static function sumByStatusDepartment(array $statuses, mixed $departmentId, mixed $exceptId = null): float
    {
        if (! $departmentId) {
            return 0.0;
        }

        return (float) static::query()
            ->where('department_id', $departmentId)
            ->whereIn('permintaan_suku_cadang_status', $statuses)
            ->when($exceptId, fn (Builder $q) => $q->where('permintaan_suku_cadang_id', '!=', $exceptId))
            ->sum('permintaan_suku_cadang_subtotal');
    }

    /**
     * Department ini milik siapa — kalau belum di-set, turunkan dari department peminta.
     * Data lama (sebelum kolom department_id ada) department_id-nya NULL sehingga
     * tidak pernah ikut perhitungan budget.
     */
    public function resolveDepartmentId(): ?int
    {
        $pemintaId = $this->permintaan_suku_cadang_id_peminta;

        if (! $pemintaId) {
            return null;
        }

        $departmentId = User::where('id', $pemintaId)->value('department_id');

        return $departmentId ? (int) $departmentId : null;
    }

    protected static function booted(): void
    {
        // Isi department_id otomatis (termasuk saat backfill data lama yang NULL).
        static::saving(function (self $model) {
            if (empty($model->department_id)) {
                $model->department_id = $model->resolveDepartmentId();
            }
        });

        static::created(function (self $model) {
            Department::syncTerpakai($model->department_id);
        });

        static::updated(function (self $model) {
            // Department lama ikut dihitung ulang kalau record dipindah department.
            $previous = $model->getOriginal('department_id');
            if ($previous && (int) $previous !== (int) $model->department_id) {
                Department::syncTerpakai((int) $previous);
            }

            Department::syncTerpakai($model->department_id);
        });

        // Hanya jalan untuk delete via model instance. Mass delete (query builder)
        // ditangani di PermintaanSukuCadangController::postDelete().
        static::deleted(function (self $model) {
            Department::syncTerpakai($model->department_id);
        });
    }
}
