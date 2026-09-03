<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class PenjualanAset extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'penjualan_aset';

    protected $primaryKey = 'penjualan_aset_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'penjualan_aset_nomor',
        'penjualan_aset_id_aset',
        'penjualan_aset_alasan',
        'penjualan_aset_nilai_buku',
        'penjualan_aset_harga_appraisal',
        'penjualan_aset_harga_jual',
        'penjualan_aset_status',
        'penjualan_aset_tanggal_request',
        'penjualan_aset_tanggal_jual',
        'penjualan_aset_tanggal_serah_terima',
        'penjualan_aset_penerima',
        'penjualan_aset_kondisi',
        'penjualan_aset_foto_serah_terima',
        'penjualan_aset_gain_loss',
        'penjualan_aset_catatan',
    ];

    public static $filterColumns = [
        'penjualan_aset_nomor' => 'Nomor',
        'penjualan_aset_status' => 'Status',
        'penjualan_aset_id_aset' => 'Aset',
        'penjualan_aset_penerima' => 'Penerima',
    ];

    public static $sortColumns = [
        'penjualan_aset_nomor',
        'penjualan_aset_status',
        'penjualan_aset_tanggal_request',
        'penjualan_aset_harga_jual',
    ];

    protected function casts(): array
    {
        return [
            'penjualan_aset_tanggal_request' => 'datetime',
            'penjualan_aset_tanggal_jual' => 'date',
            'penjualan_aset_tanggal_serah_terima' => 'date',
            'penjualan_aset_nilai_buku' => 'decimal:2',
            'penjualan_aset_harga_appraisal' => 'decimal:2',
            'penjualan_aset_harga_jual' => 'decimal:2',
            'penjualan_aset_gain_loss' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'penjualan_aset_nomor';
    }

    public function rules(): array
    {
        return [
            'penjualan_aset_nomor' => 'required|string|max:60',
            'penjualan_aset_id_aset' => 'required|integer',
            'penjualan_aset_alasan' => 'nullable|string',
            'penjualan_aset_nilai_buku' => 'nullable|numeric|min:0',
            'penjualan_aset_harga_appraisal' => 'nullable|numeric|min:0',
            'penjualan_aset_harga_jual' => 'nullable|numeric|min:0',
            'penjualan_aset_status' => 'nullable|string|max:30',
            'penjualan_aset_tanggal_request' => 'nullable|date',
            'penjualan_aset_tanggal_jual' => 'nullable|date',
            'penjualan_aset_tanggal_serah_terima' => 'nullable|date',
            'penjualan_aset_penerima' => 'nullable|string|max:120',
            'penjualan_aset_kondisi' => 'nullable|string|max:30',
            'penjualan_aset_foto_serah_terima' => 'nullable|string|max:255',
            'penjualan_aset_gain_loss' => 'nullable|numeric',
            'penjualan_aset_catatan' => 'nullable|string',
        ];
    }

    public function getPenjualanAsetFotoSerahTerimaUrlAttribute(): string
    {
        return fileUrl($this->penjualan_aset_foto_serah_terima);
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'penjualan_aset_id_aset');
    }

    public function hasPenawaran()
    {
        return $this->hasMany(PenawaranPenjualan::class, 'penawaran_penjualan_id_penjualan', 'penjualan_aset_id');
    }
}
