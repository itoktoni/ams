<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class LogAlert extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'log_alert';

    protected $primaryKey = 'log_alert_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'log_alert_id_alert',
        'log_alert_kanal',
        'log_alert_tujuan',
        'log_alert_status',
        'log_alert_dibuka',
        'log_alert_pesan',
    ];

    public static $filterColumns = [
        'log_alert_kanal' => 'Kanal',
        'log_alert_status' => 'Status',
    ];

    public static $sortColumns = [
        'log_alert_id',
        'log_alert_kanal',
        'log_alert_tujuan',
        'log_alert_status',
    ];

    protected function casts(): array
    {
        return [
            'log_alert_id_alert' => 'integer',
            'log_alert_dibuka' => 'boolean',
        ];
    }

    public static function field_name(): string
    {
        return 'log_alert_tujuan';
    }

    public function rules(): array
    {
        return [
            'log_alert_id_alert' => 'nullable|integer',
            'log_alert_kanal' => 'required|string',
            'log_alert_tujuan' => 'required|string',
            'log_alert_status' => 'required|string',
            'log_alert_dibuka' => 'nullable|boolean',
            'log_alert_pesan' => 'nullable|string',
        ];
    }
}
