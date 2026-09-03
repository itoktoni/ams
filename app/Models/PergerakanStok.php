<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class PergerakanStok extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'pergerakan_stok';

    protected $primaryKey = 'pergerakan_stok_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'pergerakan_stok_id_suku_cadang',
        'pergerakan_stok_id_gudang',
        'pergerakan_stok_tipe',
        'pergerakan_stok_jumlah',
        'pergerakan_stok_referensi',
        'pergerakan_stok_catatan',
    ];

    public static $filterColumns = [
        'pergerakan_stok_id_suku_cadang' => 'Suku Cadang',
        'pergerakan_stok_id_gudang' => 'Gudang',
        'pergerakan_stok_tipe' => 'Tipe',
    ];

    public static $sortColumns = [
        'pergerakan_stok_id_suku_cadang',
        'pergerakan_stok_id_gudang',
        'pergerakan_stok_tipe',
        'pergerakan_stok_jumlah',
    ];

    protected function casts(): array
    {
        return [
            'pergerakan_stok_jumlah' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'pergerakan_stok_tipe';
    }

    public function rules(): array
    {
        return [
            'pergerakan_stok_id_suku_cadang' => 'nullable|integer',
            'pergerakan_stok_id_gudang' => 'nullable|integer',
            'pergerakan_stok_tipe' => 'required|string|max:30',
            'pergerakan_stok_jumlah' => 'nullable|numeric',
            'pergerakan_stok_referensi' => 'nullable|string|max:120',
            'pergerakan_stok_catatan' => 'nullable|string',
        ];
    }

    public function hasSukuCadang()
    {
        return $this->hasOne(SukuCadang::class, 'suku_cadang_id', 'pergerakan_stok_id_suku_cadang');
    }

    public function hasGudang()
    {
        return $this->hasOne(Gudang::class, 'gudang_id', 'pergerakan_stok_id_gudang');
    }
}
