<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class OpnameDetail extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'opname_detail';

    protected $primaryKey = 'opname_detail_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'opname_detail_id_opname',
        'opname_detail_id_aset',
        'opname_detail_status_sistem',
        'opname_detail_status_fisik',
        'opname_detail_kondisi',
        'opname_detail_ditemukan',
        'opname_detail_catatan',
    ];

    public static $filterColumns = [];

    public static $sortColumns = [
        'opname_detail_id',
        'opname_detail_id_aset',
    ];

    protected function casts(): array
    {
        return [
            'opname_detail_ditemukan' => 'boolean',
        ];
    }

    public static function field_name(): string
    {
        return 'opname_detail_id_aset';
    }

    public function rules(): array
    {
        return [
            'opname_detail_id_opname' => 'required|integer',
            'opname_detail_id_aset' => 'required|integer',
            'opname_detail_status_sistem' => 'nullable|string|max:255',
            'opname_detail_status_fisik' => 'nullable|string|max:255',
            'opname_detail_kondisi' => 'nullable|string|max:255',
            'opname_detail_ditemukan' => 'boolean',
            'opname_detail_catatan' => 'nullable|string',
        ];
    }

    public function hasOpname()
    {
        return $this->hasOne(Opname::class, 'opname_id', 'opname_detail_id_opname');
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'opname_detail_id_aset');
    }
}
