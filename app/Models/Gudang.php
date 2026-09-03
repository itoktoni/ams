<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class Gudang extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'gudang';

    protected $primaryKey = 'gudang_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'gudang_kode',
        'gudang_nama',
        'gudang_id_lokasi',
        'gudang_alamat',
        'gudang_catatan',
    ];

    public static $filterColumns = [
        'gudang_kode' => 'Kode',
        'gudang_nama' => 'Nama',
        'gudang_id_lokasi' => 'Lokasi',
    ];

    public static $sortColumns = [
        'gudang_kode',
        'gudang_nama',
    ];

    protected function casts(): array
    {
        return [];
    }

    public static function field_name(): string
    {
        return 'gudang_nama';
    }

    public function rules(): array
    {
        return [
            'gudang_kode' => 'required|string|max:40',
            'gudang_nama' => 'required|string|max:200',
            'gudang_id_lokasi' => 'nullable|integer',
            'gudang_alamat' => 'nullable|string',
            'gudang_catatan' => 'nullable|string',
        ];
    }

    public function hasLokasi()
    {
        return $this->hasOne(LokasiAset::class, 'aset_lokasi_id', 'gudang_id_lokasi');
    }
}
