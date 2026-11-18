<?php

namespace App\Models;

class Pembayaran extends BaseModel
{
    protected $table = 'pembayaran';

    protected $primaryKey = 'pembayaran_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'pembayaran_jenis',
        'pembayaran_nama',
        'pembayaran_nomor_pelanggan',
        'pembayaran_tanggal_tagihan',
        'pembayaran_tanggal_jatuh_tempo',
        'pembayaran_tanggal_bayar',
        'pembayaran_nominal',
        'pembayaran_denda',
        'pembayaran_status',
        'pembayaran_bukti',
        'pembayaran_catatan',
    ];

    public static $filterColumns = [
        'pembayaran_jenis' => 'Jenis',
        'pembayaran_nama' => 'Nama',
        'pembayaran_nomor_pelanggan' => 'No. Pelanggan',
        'pembayaran_status' => 'Status',
    ];

    public static $sortColumns = [
        'pembayaran_jenis',
        'pembayaran_nama',
        'pembayaran_tanggal_jatuh_tempo',
        'pembayaran_nominal',
        'pembayaran_status',
    ];

    protected function casts(): array
    {
        return [
            'pembayaran_tanggal_tagihan' => 'date',
            'pembayaran_tanggal_jatuh_tempo' => 'date',
            'pembayaran_tanggal_bayar' => 'date',
            'pembayaran_nominal' => 'decimal:2',
            'pembayaran_denda' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'pembayaran_nama';
    }

    public function rules(): array
    {
        return [
            'pembayaran_jenis' => 'required|string|max:30',
            'pembayaran_nama' => 'required|string|max:200',
            'pembayaran_nomor_pelanggan' => 'nullable|string|max:100',
            'pembayaran_tanggal_tagihan' => 'nullable|date',
            'pembayaran_tanggal_jatuh_tempo' => 'nullable|date',
            'pembayaran_tanggal_bayar' => 'nullable|date',
            'pembayaran_nominal' => 'nullable|numeric|min:0',
            'pembayaran_denda' => 'nullable|numeric|min:0',
            'pembayaran_status' => 'nullable|string|max:30',
            'pembayaran_bukti' => 'nullable|string|max:255',
            'pembayaran_catatan' => 'nullable|string',
        ];
    }

    public function getBuktiUrlAttribute(): string
    {
        return fileUrl($this->pembayaran_bukti);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->pembayaran_nominal + (float) $this->pembayaran_denda;
    }
}
