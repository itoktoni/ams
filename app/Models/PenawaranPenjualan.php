<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class PenawaranPenjualan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'penawaran_penjualan';

    protected $primaryKey = 'penawaran_penjualan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'penawaran_penjualan_id_penjualan',
        'penawaran_penjualan_id_user',
        'penawaran_penjualan_nama_pembeli',
        'penawaran_penjualan_kontak',
        'penawaran_penjualan_harga',
        'penawaran_penjualan_tanggal',
        'penawaran_penjualan_waktu',
        'penawaran_penjualan_status',
        'penawaran_penjualan_hasil',
        'penawaran_penjualan_catatan',
    ];

    public static $filterColumns = [
        'penawaran_penjualan_nama_pembeli' => 'Pembeli',
        'penawaran_penjualan_status' => 'Status',
    ];

    public static $sortColumns = [
        'penawaran_penjualan_nama_pembeli',
        'penawaran_penjualan_harga',
        'penawaran_penjualan_tanggal',
        'penawaran_penjualan_status',
    ];

    protected function casts(): array
    {
        return [
            'penawaran_penjualan_harga' => 'decimal:2',
            'penawaran_penjualan_tanggal' => 'date',
            'penawaran_penjualan_waktu' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'penawaran_penjualan_nama_pembeli';
    }

    public function rules(): array
    {
        return [
            'penawaran_penjualan_id_penjualan' => 'required|integer',
            'penawaran_penjualan_id_user' => 'nullable|integer',
            'penawaran_penjualan_nama_pembeli' => 'required|string|max:150',
            'penawaran_penjualan_kontak' => 'nullable|string|max:60',
            'penawaran_penjualan_harga' => 'required|numeric|min:0',
            'penawaran_penjualan_tanggal' => 'nullable|date',
            'penawaran_penjualan_waktu' => 'nullable|date',
            'penawaran_penjualan_status' => 'nullable|string|max:30',
            'penawaran_penjualan_hasil' => 'nullable|string',
            'penawaran_penjualan_catatan' => 'nullable|string',
        ];
    }

    public function hasPenjualan()
    {
        return $this->hasOne(PenjualanAset::class, 'penjualan_aset_id', 'penawaran_penjualan_id_penjualan');
    }

    public function hasUser()
    {
        return $this->hasOne(User::class, 'id', 'penawaran_penjualan_id_user');
    }
}
