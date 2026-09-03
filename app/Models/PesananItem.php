<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class PesananItem extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'pesanan_item';

    protected $primaryKey = 'pesanan_item_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'pesanan_item_id_pesanan',
        'pesanan_item_tipe',
        'pesanan_item_id_referensi',
        'pesanan_item_nama',
        'pesanan_item_jumlah',
        'pesanan_item_harga',
        'pesanan_item_subtotal',
        'pesanan_item_diterima',
    ];

    public static $filterColumns = [
        'pesanan_item_id_pesanan' => 'Pesanan',
        'pesanan_item_tipe' => 'Tipe',
    ];

    public static $sortColumns = [
        'pesanan_item_id_pesanan',
        'pesanan_item_tipe',
        'pesanan_item_nama',
        'pesanan_item_jumlah',
    ];

    protected function casts(): array
    {
        return [
            'pesanan_item_jumlah' => 'decimal:2',
            'pesanan_item_harga' => 'decimal:2',
            'pesanan_item_subtotal' => 'decimal:2',
            'pesanan_item_diterima' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'pesanan_item_nama';
    }

    public function rules(): array
    {
        return [
            'pesanan_item_id_pesanan' => 'nullable|integer',
            'pesanan_item_tipe' => 'required|string|max:30',
            'pesanan_item_id_referensi' => 'nullable|integer',
            'pesanan_item_nama' => 'required|string|max:200',
            'pesanan_item_jumlah' => 'nullable|numeric',
            'pesanan_item_harga' => 'nullable|numeric',
            'pesanan_item_subtotal' => 'nullable|numeric',
            'pesanan_item_diterima' => 'nullable|numeric',
        ];
    }

    public function hasPesanan()
    {
        return $this->hasOne(PesananPembelian::class, 'pesanan_pembelian_id', 'pesanan_item_id_pesanan');
    }
}
