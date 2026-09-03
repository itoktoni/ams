<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Perpindahan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'perpindahan';

    protected $primaryKey = 'perpindahan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'perpindahan_nomor',
        'perpindahan_id_aset',
        'perpindahan_id_lokasi_asal',
        'perpindahan_id_lokasi_tujuan',
        'perpindahan_alasan',
        'perpindahan_tanggal_request',
        'perpindahan_tanggal_estimasi',
        'perpindahan_tanggal_kirim',
        'perpindahan_tanggal_terima',
        'perpindahan_status',
        'perpindahan_level_approve',
        'perpindahan_foto_keluar',
        'perpindahan_foto_terima',
        'perpindahan_ttd_hash',
        'perpindahan_latitude',
        'perpindahan_longitude',
        'perpindahan_catatan',
    ];

    public static $filterColumns = [
        'perpindahan_nomor' => 'Nomor',
        'perpindahan_id_aset' => 'Aset',
        'perpindahan_status' => 'Status',
        'perpindahan_tanggal_request' => 'Tanggal Request',
    ];

    public static $sortColumns = [
        'perpindahan_nomor',
        'perpindahan_id_aset',
        'perpindahan_status',
        'perpindahan_tanggal_request',
    ];

    protected function casts(): array
    {
        return [
            'perpindahan_tanggal_request' => 'datetime',
            'perpindahan_tanggal_estimasi' => 'date',
            'perpindahan_tanggal_kirim' => 'datetime',
            'perpindahan_tanggal_terima' => 'datetime',
            'perpindahan_latitude' => 'decimal:2',
            'perpindahan_longitude' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'perpindahan_nomor';
    }

    public function rules(): array
    {
        return [
            'perpindahan_nomor' => 'required|string|max:255',
            'perpindahan_id_aset' => 'required|integer',
            'perpindahan_id_lokasi_asal' => 'required|integer',
            'perpindahan_id_lokasi_tujuan' => 'required|integer',
            'perpindahan_alasan' => 'nullable|string',
            'perpindahan_tanggal_request' => 'required|date',
            'perpindahan_tanggal_estimasi' => 'nullable|date',
            'perpindahan_tanggal_kirim' => 'nullable|date',
            'perpindahan_tanggal_terima' => 'nullable|date',
            'perpindahan_status' => 'required|string|max:255',
            'perpindahan_level_approve' => 'nullable|string|max:255',
            'perpindahan_foto_keluar' => 'nullable|string|max:255',
            'perpindahan_foto_terima' => 'nullable|string|max:255',
            'perpindahan_ttd_hash' => 'nullable|string|max:255',
            'perpindahan_latitude' => 'nullable|numeric',
            'perpindahan_longitude' => 'nullable|numeric',
            'perpindahan_catatan' => 'nullable|string',
        ];
    }

    public function getPerpindahanFotoKeluarUrlAttribute(): string
    {
        return fileUrl($this->perpindahan_foto_keluar);
    }

    public function getPerpindahanFotoTerimaUrlAttribute(): string
    {
        return fileUrl($this->perpindahan_foto_terima);
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'perpindahan_id_aset');
    }

    public function hasLokasiAsal()
    {
        return $this->hasOne(LokasiAset::class, 'aset_lokasi_id', 'perpindahan_id_lokasi_asal');
    }

    public function hasLokasiTujuan()
    {
        return $this->hasOne(LokasiAset::class, 'aset_lokasi_id', 'perpindahan_id_lokasi_tujuan');
    }
}
