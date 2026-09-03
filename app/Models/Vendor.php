<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class Vendor extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'vendor';

    protected $primaryKey = 'vendor_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'vendor_kode',
        'vendor_nama',
        'vendor_telepon',
        'vendor_email',
        'vendor_alamat',
        'vendor_kategori',
        'vendor_rating',
        'vendor_catatan',
    ];

    public static $filterColumns = [
        'vendor_kode' => 'Kode',
        'vendor_nama' => 'Nama',
        'vendor_kategori' => 'Kategori',
        'vendor_email' => 'Email',
    ];

    public static $sortColumns = [
        'vendor_kode',
        'vendor_nama',
        'vendor_kategori',
        'vendor_rating',
    ];

    protected function casts(): array
    {
        return [
            'vendor_rating' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'vendor_nama';
    }

    public function rules(): array
    {
        return [
            'vendor_kode' => 'required|string|max:40',
            'vendor_nama' => 'required|string|max:200',
            'vendor_telepon' => 'nullable|string|max:40',
            'vendor_email' => 'nullable|email|max:120',
            'vendor_alamat' => 'nullable|string',
            'vendor_kategori' => 'nullable|string|max:60',
            'vendor_rating' => 'nullable|numeric',
            'vendor_catatan' => 'nullable|string',
        ];
    }
}
