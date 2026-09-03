<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Penghapusan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'penghapusan';

    protected $primaryKey = 'penghapusan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'penghapusan_nomor',
        'penghapusan_id_aset',
        'penghapusan_alasan',
        'penghapusan_tanggal_request',
        'penghapusan_nilai_buku',
        'penghapusan_nilai_sisa',
        'penghapusan_status',
        'penghapusan_triase',
        'penghapusan_tanggal_akhir_karantina',
        'penghapusan_foto',
        'penghapusan_berita_acara',
        'penghapusan_gain_loss',
        'penghapusan_catatan',
    ];

    public static $filterColumns = [
        'penghapusan_nomor' => 'Nomor',
        'penghapusan_id_aset' => 'Aset',
        'penghapusan_status' => 'Status',
        'penghapusan_triase' => 'Triase',
        'penghapusan_tanggal_request' => 'Tanggal Request',
    ];

    public static $sortColumns = [
        'penghapusan_nomor',
        'penghapusan_id_aset',
        'penghapusan_status',
        'penghapusan_triase',
        'penghapusan_tanggal_request',
    ];

    protected function casts(): array
    {
        return [
            'penghapusan_tanggal_request' => 'datetime',
            'penghapusan_tanggal_akhir_karantina' => 'date',
            'penghapusan_nilai_buku' => 'decimal:2',
            'penghapusan_nilai_sisa' => 'decimal:2',
            'penghapusan_gain_loss' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'penghapusan_nomor';
    }

    public function rules(): array
    {
        return [
            'penghapusan_nomor' => 'required|string|max:255',
            'penghapusan_id_aset' => 'required|integer',
            'penghapusan_alasan' => 'required|string',
            'penghapusan_tanggal_request' => 'required|date',
            'penghapusan_nilai_buku' => 'nullable|numeric',
            'penghapusan_nilai_sisa' => 'nullable|numeric',
            'penghapusan_status' => 'required|string|max:255',
            'penghapusan_triase' => 'nullable|string|max:255',
            'penghapusan_tanggal_akhir_karantina' => 'nullable|date',
            'penghapusan_foto' => 'nullable|string|max:255',
            'penghapusan_berita_acara' => 'nullable|string|max:255',
            'penghapusan_gain_loss' => 'nullable|numeric',
            'penghapusan_catatan' => 'nullable|string',
        ];
    }

    public function getPenghapusanFotoUrlAttribute(): string
    {
        return fileUrl($this->penghapusan_foto);
    }

    public function getPenghapusanBeritaAcaraUrlAttribute(): string
    {
        return fileUrl($this->penghapusan_berita_acara);
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'penghapusan_id_aset');
    }

    public function hasKomponen()
    {
        return $this->hasMany(PenghapusanKomponen::class, 'penghapusan_komponen_id_penghapusan', 'penghapusan_id');
    }
}
