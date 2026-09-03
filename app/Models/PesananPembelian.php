<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class PesananPembelian extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'pesanan_pembelian';

    protected $primaryKey = 'pesanan_pembelian_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'pesanan_pembelian_nomor',
        'pesanan_pembelian_id_vendor',
        'pesanan_pembelian_tanggal',
        'pesanan_pembelian_tanggal_kirim',
        'pesanan_pembelian_tipe',
        'pesanan_pembelian_status',
        'pesanan_pembelian_total',
        'pesanan_pembelian_kode_budget',
        'pesanan_pembelian_level_approve',
        'pesanan_pembelian_catatan',
    ];

    public static $filterColumns = [
        'pesanan_pembelian_nomor' => 'Nomor',
        'pesanan_pembelian_status' => 'Status',
        'pesanan_pembelian_tipe' => 'Tipe',
        'pesanan_pembelian_id_vendor' => 'Vendor',
    ];

    public static $sortColumns = [
        'pesanan_pembelian_nomor',
        'pesanan_pembelian_tanggal',
        'pesanan_pembelian_status',
        'pesanan_pembelian_total',
    ];

    protected function casts(): array
    {
        return [
            'pesanan_pembelian_tanggal' => 'date',
            'pesanan_pembelian_tanggal_kirim' => 'date',
            'pesanan_pembelian_total' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'pesanan_pembelian_nomor';
    }

    public function rules(): array
    {
        return [
            'pesanan_pembelian_nomor' => 'required|string|max:60',
            'pesanan_pembelian_id_vendor' => 'nullable|integer',
            'pesanan_pembelian_tanggal' => 'required|date',
            'pesanan_pembelian_tanggal_kirim' => 'nullable|date',
            'pesanan_pembelian_tipe' => 'required|string|max:30',
            'pesanan_pembelian_status' => 'required|string|max:30',
            'pesanan_pembelian_total' => 'nullable|numeric',
            'pesanan_pembelian_kode_budget' => 'nullable|string|max:60',
            'pesanan_pembelian_level_approve' => 'nullable|string|max:30',
            'pesanan_pembelian_catatan' => 'nullable|string',
        ];
    }

    public function hasVendor()
    {
        return $this->hasOne(Vendor::class, 'vendor_id', 'pesanan_pembelian_id_vendor');
    }

    public function hasItem()
    {
        return $this->hasMany(PesananItem::class, 'pesanan_item_id_pesanan', 'pesanan_pembelian_id');
    }
}
