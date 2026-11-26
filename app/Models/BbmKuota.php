<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

/**
 * Kuota BBM per user per bulan (periode YYYY-MM).
 */
class BbmKuota extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'bbm_kuota';

    protected $primaryKey = 'bbm_kuota_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'bbm_kuota_id_user',
        'bbm_kuota_periode',
        'bbm_kuota_liter',
    ];

    public static $filterColumns = [
        'bbm_kuota_periode' => 'Periode',
    ];

    public static $sortColumns = [
        'bbm_kuota_periode',
        'bbm_kuota_liter',
    ];

    protected function casts(): array
    {
        return [
            'bbm_kuota_id_user' => 'integer',
            'bbm_kuota_liter' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'bbm_kuota_periode';
    }

    public function rules(): array
    {
        return [
            'bbm_kuota_id_user' => 'required|integer',
            'bbm_kuota_periode' => 'required|string|size:7',
            'bbm_kuota_liter' => 'required|numeric|min:0',
        ];
    }

    public function hasUser()
    {
        return $this->hasOne(User::class, 'id', 'bbm_kuota_id_user');
    }

    /**
     * Kuota liter user untuk satu periode. 0 berarti belum ditetapkan admin.
     */
    public static function kuotaUser(mixed $userId, ?string $periode = null): float
    {
        if (! $userId) {
            return 0.0;
        }

        $periode ??= now()->format('Y-m');

        return (float) (static::query()
            ->where('bbm_kuota_id_user', $userId)
            ->where('bbm_kuota_periode', $periode)
            ->value('bbm_kuota_liter') ?? 0);
    }
}
