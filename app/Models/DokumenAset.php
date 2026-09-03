<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class DokumenAset extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'aset_dokumen';

    protected $primaryKey = 'aset_dokumen_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'aset_dokumen_id_aset',
        'aset_dokumen_jenis',
        'aset_dokumen_nomor',
        'aset_dokumen_file',
        'aset_dokumen_tanggal_terbit',
        'aset_dokumen_tanggal_expired',
        'aset_dokumen_keterangan',
    ];

    public static $filterColumns = [
        'aset_dokumen_id_aset' => 'Aset',
        'aset_dokumen_jenis' => 'Jenis',
        'aset_dokumen_nomor' => 'Nomor',
    ];

    public static $sortColumns = [
        'aset_dokumen_jenis',
        'aset_dokumen_nomor',
        'aset_dokumen_tanggal_terbit',
        'aset_dokumen_tanggal_expired',
    ];

    protected function casts(): array
    {
        return [
            'aset_dokumen_tanggal_terbit' => 'date',
            'aset_dokumen_tanggal_expired' => 'date',
        ];
    }

    public static function field_name(): string
    {
        return 'aset_dokumen_nomor';
    }

    public function rules(): array
    {
        return [
            'aset_dokumen_id_aset' => 'nullable|integer',
            'aset_dokumen_jenis' => 'nullable|string|max:30',
            'aset_dokumen_nomor' => 'nullable|string|max:80',
            'aset_dokumen_file' => 'nullable|string|max:255',
            'aset_dokumen_tanggal_terbit' => 'nullable|date',
            'aset_dokumen_tanggal_expired' => 'nullable|date',
            'aset_dokumen_keterangan' => 'nullable|string',
        ];
    }

    public function getFileUrlAttribute(): string
    {
        return fileUrl($this->aset_dokumen_file);
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'aset_dokumen_id_aset');
    }
}
