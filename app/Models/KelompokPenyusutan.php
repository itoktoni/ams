<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class KelompokPenyusutan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'kelompok_penyusutan';

    protected $primaryKey = 'kelompok_penyusutan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'kelompok_penyusutan_kode',
        'kelompok_penyusutan_nama',
        'kelompok_penyusutan_masa_manfaat',
        'kelompok_penyusutan_metode',
        'kelompok_penyusutan_tarif',
        'kelompok_penyusutan_keterangan',
    ];

    public static $filterColumns = [
        'kelompok_penyusutan_kode' => 'Kode',
        'kelompok_penyusutan_nama' => 'Nama',
        'kelompok_penyusutan_metode' => 'Metode',
    ];

    public static $sortColumns = [
        'kelompok_penyusutan_kode',
        'kelompok_penyusutan_nama',
        'kelompok_penyusutan_masa_manfaat',
        'kelompok_penyusutan_metode',
        'kelompok_penyusutan_tarif',
    ];

    protected function casts(): array
    {
        return [
            'kelompok_penyusutan_masa_manfaat' => 'integer',
            'kelompok_penyusutan_tarif' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'kelompok_penyusutan_nama';
    }

    public function rules(): array
    {
        return [
            'kelompok_penyusutan_kode' => 'required|string|max:30',
            'kelompok_penyusutan_nama' => 'required|string|max:150',
            'kelompok_penyusutan_masa_manfaat' => 'nullable|integer|min:0',
            'kelompok_penyusutan_metode' => 'nullable|string|max:30',
            'kelompok_penyusutan_tarif' => 'nullable|numeric|min:0',
            'kelompok_penyusutan_keterangan' => 'nullable|string',
        ];
    }
}
