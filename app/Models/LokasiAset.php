<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class LokasiAset extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'aset_lokasi';

    protected $primaryKey = 'aset_lokasi_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'aset_lokasi_nama',
        'aset_lokasi_kode',
        'aset_lokasi_alamat',
        'aset_lokasi_zona',
        'aset_lokasi_latitude',
        'aset_lokasi_longitude',
        'aset_lokasi_parent_id',
    ];

    public static $filterColumns = [
        'aset_lokasi_kode' => 'Kode',
        'aset_lokasi_nama' => 'Nama',
        'aset_lokasi_zona' => 'Zona',
    ];

    public static $sortColumns = [
        'aset_lokasi_kode',
        'aset_lokasi_nama',
        'aset_lokasi_zona',
    ];

    protected function casts(): array
    {
        return [
            'aset_lokasi_latitude' => 'decimal:8',
            'aset_lokasi_longitude' => 'decimal:8',
        ];
    }

    public static function field_name(): string
    {
        return 'aset_lokasi_nama';
    }

    public function rules(): array
    {
        return [
            'aset_lokasi_nama' => 'required|string|max:150',
            'aset_lokasi_kode' => 'required|string|max:30',
            'aset_lokasi_alamat' => 'nullable|string',
            'aset_lokasi_zona' => 'nullable|string|max:50',
            'aset_lokasi_latitude' => 'nullable|numeric',
            'aset_lokasi_longitude' => 'nullable|numeric',
            'aset_lokasi_parent_id' => 'nullable|integer',
        ];
    }

    public function hasParent()
    {
        return $this->hasOne(LokasiAset::class, 'aset_lokasi_id', 'aset_lokasi_parent_id');
    }

    public function hasChild()
    {
        return $this->hasMany(LokasiAset::class, 'aset_lokasi_parent_id', 'aset_lokasi_id');
    }
}
