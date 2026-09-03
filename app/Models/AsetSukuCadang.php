<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class AsetSukuCadang extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'aset_suku_cadang';
    protected $primaryKey = 'aset_suku_cadang_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = ['aset_suku_cadang_id_aset', 'aset_suku_cadang_id_suku_cadang', 'aset_suku_cadang_jumlah', 'aset_id', 'suku_cadang_id', 'jumlah', 'catatan'];

    public static $filterColumns = ['aset_suku_cadang_id_aset' => 'Aset', 'aset_suku_cadang_id_suku_cadang' => 'Suku Cadang'];
    public static $sortColumns = ['aset_suku_cadang_id_aset', 'aset_suku_cadang_id_suku_cadang', 'aset_suku_cadang_jumlah'];

    protected function casts(): array
    {
        return ['aset_suku_cadang_jumlah' => 'decimal:2', 'jumlah' => 'decimal:2'];
    }

    public static function field_name(): string
    {
        return 'aset_suku_cadang_id';
    }

    public function rules(): array
    {
        return [
            'aset_suku_cadang_id_aset' => 'nullable|integer',
            'aset_suku_cadang_id_suku_cadang' => 'nullable|integer',
            'aset_id' => 'nullable|integer',
            'suku_cadang_id' => 'nullable|integer',
            'jumlah' => 'nullable|numeric|min:0',
        ];
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'aset_suku_cadang_id_aset');
    }

    public function hasSukuCadang()
    {
        return $this->hasOne(SukuCadang::class, 'suku_cadang_id', 'aset_suku_cadang_id_suku_cadang');
    }
}
