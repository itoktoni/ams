<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class TiketSukuCadang extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'tiket_suku_cadang';

    protected $primaryKey = 'tiket_suku_cadang_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'tiket_suku_cadang_id_tiket',
        'tiket_suku_cadang_id_suku_cadang',
        'tiket_suku_cadang_jumlah',
        'tiket_suku_cadang_harga',
        'tiket_suku_cadang_subtotal',
    ];

    public static $filterColumns = [];

    public static $sortColumns = [
        'tiket_suku_cadang_id_tiket',
        'tiket_suku_cadang_id_suku_cadang',
        'tiket_suku_cadang_jumlah',
    ];

    protected function casts(): array
    {
        return [
            'tiket_suku_cadang_jumlah' => 'decimal:2',
            'tiket_suku_cadang_harga' => 'decimal:2',
            'tiket_suku_cadang_subtotal' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'tiket_suku_cadang_id';
    }

    public function rules(): array
    {
        return [
            'tiket_suku_cadang_id_tiket' => 'required|integer',
            'tiket_suku_cadang_id_suku_cadang' => 'required|integer',
            'tiket_suku_cadang_jumlah' => 'nullable|numeric',
            'tiket_suku_cadang_harga' => 'nullable|numeric',
            'tiket_suku_cadang_subtotal' => 'nullable|numeric',
        ];
    }

    public function hasTiket()
    {
        return $this->hasOne(Tiket::class, 'tiket_id', 'tiket_suku_cadang_id_tiket');
    }

    public function hasSukuCadang()
    {
        return $this->hasOne(SukuCadang::class, 'suku_cadang_id', 'tiket_suku_cadang_id_suku_cadang');
    }
}
