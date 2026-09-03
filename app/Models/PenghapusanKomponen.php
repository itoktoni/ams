<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class PenghapusanKomponen extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'penghapusan_komponen';

    protected $primaryKey = 'penghapusan_komponen_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'penghapusan_komponen_id_penghapusan',
        'penghapusan_komponen_nama',
        'penghapusan_komponen_jumlah',
        'penghapusan_komponen_id_suku_cadang',
        'penghapusan_komponen_kondisi',
    ];

    public static $filterColumns = [];

    public static $sortColumns = [
        'penghapusan_komponen_id',
        'penghapusan_komponen_nama',
    ];

    protected function casts(): array
    {
        return [
            'penghapusan_komponen_jumlah' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'penghapusan_komponen_nama';
    }

    public function rules(): array
    {
        return [
            'penghapusan_komponen_id_penghapusan' => 'required|integer',
            'penghapusan_komponen_nama' => 'required|string|max:255',
            'penghapusan_komponen_jumlah' => 'nullable|numeric',
            'penghapusan_komponen_id_suku_cadang' => 'nullable|integer',
            'penghapusan_komponen_kondisi' => 'nullable|string|max:255',
        ];
    }

    public function hasPenghapusan()
    {
        return $this->hasOne(Penghapusan::class, 'penghapusan_id', 'penghapusan_komponen_id_penghapusan');
    }
}
