<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class RiwayatService extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'riwayat_service';

    protected $primaryKey = 'riwayat_service_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'riwayat_service_id_aset',
        'riwayat_service_id_tiket',
        'riwayat_service_id_teknisi',
        'riwayat_service_tanggal',
        'riwayat_service_jenis',
        'riwayat_service_biaya',
        'riwayat_service_catatan',
        'riwayat_service_checklist',
        'riwayat_service_ttd',
    ];

    public static $filterColumns = [
        'riwayat_service_jenis' => 'Jenis',
        'riwayat_service_id_aset' => 'Aset',
        'riwayat_service_id_teknisi' => 'Teknisi',
    ];

    public static $sortColumns = [
        'riwayat_service_tanggal',
        'riwayat_service_jenis',
        'riwayat_service_id_aset',
        'riwayat_service_biaya',
    ];

    protected function casts(): array
    {
        return [
            'riwayat_service_tanggal' => 'datetime',
            'riwayat_service_biaya' => 'decimal:2',
            'riwayat_service_checklist' => 'json',
        ];
    }

    public static function field_name(): string
    {
        return 'riwayat_service_jenis';
    }

    public function rules(): array
    {
        return [
            'riwayat_service_id_aset' => 'required|integer',
            'riwayat_service_id_tiket' => 'nullable|integer',
            'riwayat_service_id_teknisi' => 'nullable|integer',
            'riwayat_service_tanggal' => 'nullable|date',
            'riwayat_service_jenis' => 'required|string|max:100',
            'riwayat_service_biaya' => 'nullable|numeric|min:0',
            'riwayat_service_catatan' => 'nullable|string',
            'riwayat_service_checklist' => 'nullable|json',
            'riwayat_service_ttd' => 'nullable|string|max:255',
        ];
    }

    public function getRiwayatServiceTtdUrlAttribute(): string
    {
        return fileUrl($this->riwayat_service_ttd);
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'riwayat_service_id_aset');
    }

    public function hasTiket()
    {
        return $this->hasOne(Tiket::class, 'tiket_id', 'riwayat_service_id_tiket');
    }

    public function hasTeknisi()
    {
        return $this->hasOne(Teknisi::class, 'teknisi_id', 'riwayat_service_id_teknisi');
    }
}
