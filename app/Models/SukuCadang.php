<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class SukuCadang extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'suku_cadang';

    protected $primaryKey = 'suku_cadang_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'suku_cadang_kode',
        'suku_cadang_nama',
        'suku_cadang_spesifikasi',
        'suku_cadang_id_vendor',
        'suku_cadang_harga',
        'suku_cadang_id_gudang',
        'suku_cadang_stok_minimum',
        'suku_cadang_stok_maksimum',
        'suku_cadang_bin_aktif',
        'suku_cadang_bin_buffer',
        'suku_cadang_satuan',
        'suku_cadang_kompatibilitas',
        'suku_cadang_foto',
    ];

    public static $filterColumns = [
        'suku_cadang_kode' => 'Kode',
        'suku_cadang_nama' => 'Nama',
        'suku_cadang_id_vendor' => 'Vendor',
        'suku_cadang_id_gudang' => 'Gudang',
    ];

    public static $sortColumns = [
        'suku_cadang_kode',
        'suku_cadang_nama',
        'suku_cadang_harga',
        'suku_cadang_stok_minimum',
    ];

    protected function casts(): array
    {
        return [
            'suku_cadang_harga' => 'decimal:2',
            'suku_cadang_stok_minimum' => 'decimal:2',
            'suku_cadang_stok_maksimum' => 'decimal:2',
            'suku_cadang_bin_aktif' => 'decimal:2',
            'suku_cadang_bin_buffer' => 'decimal:2',
            'suku_cadang_kompatibilitas' => 'json',
        ];
    }

    public static function field_name(): string
    {
        return 'suku_cadang_nama';
    }

    public function rules(): array
    {
        return [
            'suku_cadang_kode' => 'required|string|max:40',
            'suku_cadang_nama' => 'required|string|max:200',
            'suku_cadang_spesifikasi' => 'nullable|string',
            'suku_cadang_id_vendor' => 'nullable|integer',
            'suku_cadang_harga' => 'nullable|numeric',
            'suku_cadang_id_gudang' => 'nullable|integer',
            'suku_cadang_stok_minimum' => 'nullable|numeric',
            'suku_cadang_stok_maksimum' => 'nullable|numeric',
            'suku_cadang_bin_aktif' => 'nullable|numeric',
            'suku_cadang_bin_buffer' => 'nullable|numeric',
            'suku_cadang_satuan' => 'nullable|string|max:30',
            'suku_cadang_kompatibilitas' => 'nullable',
            'suku_cadang_foto' => 'nullable|string|max:255',
        ];
    }

    public function hasVendor()
    {
        return $this->hasOne(Vendor::class, 'vendor_id', 'suku_cadang_id_vendor');
    }

    public function hasGudang()
    {
        return $this->hasOne(Gudang::class, 'gudang_id', 'suku_cadang_id_gudang');
    }

    public function hasAsetSukuCadang()
    {
        return $this->hasMany(AsetSukuCadang::class, 'aset_suku_cadang_id_suku_cadang', 'suku_cadang_id');
    }

    public function hasAset()
    {
        return $this->belongsToMany(Aset::class, 'aset_suku_cadang', 'aset_suku_cadang_id_suku_cadang', 'aset_suku_cadang_id_aset')->withPivot(['aset_suku_cadang_jumlah'])->withTimestamps();
    }

    public function getSukuCadangFotoUrlAttribute(): string
    {
        return fileUrl($this->suku_cadang_foto);
    }
}
