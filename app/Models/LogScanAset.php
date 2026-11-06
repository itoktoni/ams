<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class LogScanAset extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'log_scan_aset';

    protected $primaryKey = 'log_scan_aset_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'log_scan_aset_id_aset',
        'log_scan_aset_kode',
        'log_scan_aset_sumber',
        'log_scan_aset_ip',
    ];

    public static $filterColumns = [
        'log_scan_aset_kode' => 'Kode',
        'log_scan_aset_sumber' => 'Sumber',
    ];

    public static $sortColumns = [
        'log_scan_aset_id',
        'log_scan_aset_kode',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'log_scan_aset_kode';
    }

    public function rules(): array
    {
        return [
            'log_scan_aset_id_aset' => 'nullable|integer',
            'log_scan_aset_kode' => 'required|string|max:100',
            'log_scan_aset_sumber' => 'nullable|string|max:20',
            'log_scan_aset_ip' => 'nullable|string|max:45',
        ];
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'log_scan_aset_id_aset');
    }
}
