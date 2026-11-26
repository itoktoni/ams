<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use App\Enums\Bbm\StatusPengajuanBbmEnum;
use Illuminate\Database\Eloquent\Builder;

/**
 * Pengajuan pembelian BBM. Status disetujui = pemakaian nyata,
 * status menunggu = kuota di-reserve (belum mengurangi pemakaian).
 */
class BbmPengajuan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'bbm_pengajuan';

    protected $primaryKey = 'bbm_pengajuan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    /** Status yang dihitung sebagai pemakaian kuota. */
    public const STATUS_PEMAKAIAN = [
        StatusPengajuanBbmEnum::DISETUJUI,
    ];

    /** Status yang belum mengurangi kuota, tapi tetap di-reserve. */
    public const STATUS_RESERVE = [
        StatusPengajuanBbmEnum::MENUNGGU,
    ];

    protected $fillable = [
        'bbm_pengajuan_nomor',
        'bbm_pengajuan_id_user',
        'bbm_pengajuan_periode',
        'bbm_pengajuan_jenis_kendaraan',
        'bbm_pengajuan_kendaraan',
        'bbm_pengajuan_liter',
        'bbm_pengajuan_status',
        'bbm_pengajuan_tanggal',
        'bbm_pengajuan_catatan',
        'bbm_pengajuan_foto_sebelum',
        'bbm_pengajuan_foto_sesudah',
        'bbm_pengajuan_foto_struk',
        'bbm_pengajuan_id_approver',
    ];

    public static $filterColumns = [
        'bbm_pengajuan_nomor' => 'Nomor',
        'bbm_pengajuan_periode' => 'Periode',
        'bbm_pengajuan_status' => 'Status',
    ];

    public static $sortColumns = [
        'bbm_pengajuan_nomor',
        'bbm_pengajuan_periode',
        'bbm_pengajuan_liter',
        'bbm_pengajuan_status',
        'bbm_pengajuan_tanggal',
    ];

    protected function casts(): array
    {
        return [
            'bbm_pengajuan_id_user' => 'integer',
            'bbm_pengajuan_liter' => 'decimal:2',
            'bbm_pengajuan_tanggal' => 'datetime',
            'bbm_pengajuan_id_approver' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'bbm_pengajuan_nomor';
    }

    public function rules(): array
    {
        return [
            'bbm_pengajuan_nomor' => 'nullable|string|max:50',
            'bbm_pengajuan_id_user' => 'nullable|integer',
            'bbm_pengajuan_periode' => 'nullable|string|size:7',
            'bbm_pengajuan_jenis_kendaraan' => 'required|string|max:20',
            'bbm_pengajuan_kendaraan' => 'nullable|string|max:100',
            'bbm_pengajuan_liter' => 'required|numeric|min:0.01',
            'bbm_pengajuan_status' => 'nullable|string|max:20',
            'bbm_pengajuan_tanggal' => 'nullable|date',
            'bbm_pengajuan_catatan' => 'nullable|string',
            'bbm_pengajuan_foto_sebelum' => 'nullable|string|max:255',
            'bbm_pengajuan_foto_sesudah' => 'nullable|string|max:255',
            'bbm_pengajuan_foto_struk' => 'nullable|string|max:255',
            'bbm_pengajuan_id_approver' => 'nullable|integer',
        ];
    }

    public function hasUser()
    {
        return $this->hasOne(User::class, 'id', 'bbm_pengajuan_id_user');
    }

    public function hasApprover()
    {
        return $this->hasOne(User::class, 'id', 'bbm_pengajuan_id_approver');
    }

    public function getStatusLabelAttribute(): string
    {
        return StatusPengajuanBbmEnum::getDescription($this->bbm_pengajuan_status);
    }

    public function getBbmPengajuanFotoSebelumUrlAttribute(): string
    {
        return fileUrl($this->bbm_pengajuan_foto_sebelum);
    }

    public function getBbmPengajuanFotoSesudahUrlAttribute(): string
    {
        return fileUrl($this->bbm_pengajuan_foto_sesudah);
    }

    public function getBbmPengajuanFotoStrukUrlAttribute(): string
    {
        return fileUrl($this->bbm_pengajuan_foto_struk);
    }

    /** Total liter yang sudah disetujui (pemakaian nyata) untuk user & periode. */
    public static function terpakaiUser(mixed $userId, string $periode, mixed $exceptId = null): float
    {
        return static::sumLiterUser(static::STATUS_PEMAKAIAN, $userId, $periode, $exceptId);
    }

    /** Total liter yang masih menunggu (di-reserve) untuk user & periode. */
    public static function pendingUser(mixed $userId, string $periode, mixed $exceptId = null): float
    {
        return static::sumLiterUser(static::STATUS_RESERVE, $userId, $periode, $exceptId);
    }

    public static function sumLiterUser(array $statuses, mixed $userId, string $periode, mixed $exceptId = null): float
    {
        if (! $userId || ! $periode) {
            return 0.0;
        }

        return (float) static::query()
            ->where('bbm_pengajuan_id_user', $userId)
            ->where('bbm_pengajuan_periode', $periode)
            ->whereIn('bbm_pengajuan_status', $statuses)
            ->when($exceptId, fn (Builder $q) => $q->where('bbm_pengajuan_id', '!=', $exceptId))
            ->sum('bbm_pengajuan_liter');
    }

    /**
     * Ringkasan kuota bulanan user:
     * kuota - terpakai (disetujui) - menunggu (reserve) = tersedia.
     */
    public static function ringkasanUser(mixed $userId, ?string $periode = null, mixed $exceptId = null): array
    {
        $periode ??= now()->format('Y-m');
        $kuota = BbmKuota::kuotaUser($userId, $periode);
        $terpakai = static::terpakaiUser($userId, $periode, $exceptId);
        $pending = static::pendingUser($userId, $periode, $exceptId);

        return [
            'periode' => $periode,
            'kuota' => $kuota,
            'terpakai' => $terpakai,
            'pending' => $pending,
            'sisa' => $kuota - $terpakai,
            'tersedia' => $kuota - $terpakai - $pending,
        ];
    }
}
