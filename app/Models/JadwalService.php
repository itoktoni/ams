<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class JadwalService extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'jadwal_service';

    protected $primaryKey = 'jadwal_service_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'jadwal_service_id_aset',
        'jadwal_service_id_template',
        'jadwal_service_tanggal_mulai',
        'jadwal_service_tanggal_jatuh_tempo',
        'jadwal_service_interval_bulan',
        'jadwal_service_interval_jam',
        'jadwal_service_odometer_terakhir',
        'jadwal_service_jam_terakhir',
        'jadwal_service_status',
        'jadwal_service_tanggal_terakhir',
    ];

    public static $filterColumns = [
        'jadwal_service_id_aset' => 'Aset',
        'jadwal_service_id_template' => 'Template',
        'jadwal_service_status' => 'Status',
        'jadwal_service_tanggal_mulai' => 'Tanggal Mulai',
    ];

    public static $sortColumns = [
        'jadwal_service_tanggal_mulai',
        'jadwal_service_tanggal_jatuh_tempo',
        'jadwal_service_status',
        'jadwal_service_id_aset',
    ];

    protected function casts(): array
    {
        return [
            'jadwal_service_tanggal_mulai' => 'date',
            'jadwal_service_tanggal_jatuh_tempo' => 'date',
            'jadwal_service_tanggal_terakhir' => 'date',
            'jadwal_service_interval_bulan' => 'integer',
            'jadwal_service_interval_jam' => 'decimal:2',
            'jadwal_service_odometer_terakhir' => 'decimal:2',
            'jadwal_service_jam_terakhir' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'jadwal_service_tanggal_mulai';
    }

    public function rules(): array
    {
        return [
            'jadwal_service_id_aset' => 'required|integer',
            'jadwal_service_id_template' => 'nullable|integer',
            'jadwal_service_tanggal_mulai' => 'nullable|date',
            'jadwal_service_tanggal_jatuh_tempo' => 'nullable|date',
            'jadwal_service_interval_bulan' => 'nullable|integer|min:0',
            'jadwal_service_interval_jam' => 'nullable|numeric|min:0',
            'jadwal_service_odometer_terakhir' => 'nullable|numeric|min:0',
            'jadwal_service_jam_terakhir' => 'nullable|numeric|min:0',
            'jadwal_service_status' => 'nullable|string|max:30',
            'jadwal_service_tanggal_terakhir' => 'nullable|date',
        ];
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'jadwal_service_id_aset');
    }

    public function hasTemplate()
    {
        return $this->hasOne(TemplateService::class, 'template_service_id', 'jadwal_service_id_template');
    }
}
