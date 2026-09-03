<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class StokSukuCadang extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'stok_suku_cadang';

    protected $primaryKey = 'stok_suku_cadang_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'stok_suku_cadang_id_suku_cadang',
        'stok_suku_cadang_id_gudang',
        'stok_suku_cadang_bin',
        'stok_suku_cadang_jumlah',
    ];

    public static $filterColumns = [
        'stok_suku_cadang_id_suku_cadang' => 'Suku Cadang',
        'stok_suku_cadang_id_gudang' => 'Gudang',
        'stok_suku_cadang_bin' => 'Bin',
    ];

    public static $sortColumns = [
        'stok_suku_cadang_id_suku_cadang',
        'stok_suku_cadang_id_gudang',
        'stok_suku_cadang_bin',
        'stok_suku_cadang_jumlah',
    ];

    protected function casts(): array
    {
        return [
            'stok_suku_cadang_jumlah' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'stok_suku_cadang_bin';
    }

    public function rules(): array
    {
        return [
            'stok_suku_cadang_id_suku_cadang' => 'nullable|integer',
            'stok_suku_cadang_id_gudang' => 'nullable|integer',
            'stok_suku_cadang_bin' => 'nullable|string|max:2',
            'stok_suku_cadang_jumlah' => 'nullable|numeric',
        ];
    }

    public function hasSukuCadang()
    {
        return $this->hasOne(SukuCadang::class, 'suku_cadang_id', 'stok_suku_cadang_id_suku_cadang');
    }

    public function hasGudang()
    {
        return $this->hasOne(Gudang::class, 'gudang_id', 'stok_suku_cadang_id_gudang');
    }
}
