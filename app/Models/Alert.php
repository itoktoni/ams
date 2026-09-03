<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Alert extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'alert';

    protected $primaryKey = 'alert_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'alert_tipe',
        'alert_id_referensi',
        'alert_tipe_referensi',
        'alert_judul',
        'alert_pesan',
        'alert_level',
        'alert_kunci_dedup',
        'alert_jatuh_tempo',
        'alert_id_pic',
        'alert_status',
        'alert_level_eskalasi',
        'alert_terakhir_kirim',
    ];

    public static $filterColumns = [
        'alert_judul' => 'Judul',
        'alert_tipe' => 'Tipe',
        'alert_level' => 'Level',
        'alert_status' => 'Status',
    ];

    public static $sortColumns = [
        'alert_id',
        'alert_judul',
        'alert_tipe',
        'alert_level',
        'alert_status',
        'alert_jatuh_tempo',
    ];

    protected function casts(): array
    {
        return [
            'alert_id_referensi' => 'integer',
            'alert_level_eskalasi' => 'integer',
            'alert_jatuh_tempo' => 'datetime',
            'alert_terakhir_kirim' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'alert_judul';
    }

    public function rules(): array
    {
        return [
            'alert_tipe' => 'required|string',
            'alert_id_referensi' => 'nullable|integer',
            'alert_tipe_referensi' => 'nullable|string',
            'alert_judul' => 'required|string',
            'alert_pesan' => 'nullable|string',
            'alert_level' => 'required|string',
            'alert_kunci_dedup' => 'nullable|string',
            'alert_jatuh_tempo' => 'nullable|string',
            'alert_id_pic' => 'nullable|integer',
            'alert_status' => 'required|string',
            'alert_level_eskalasi' => 'nullable|integer',
            'alert_terakhir_kirim' => 'nullable|string',
        ];
    }

    public function hasPic()
    {
        return $this->hasOne(User::class, 'id', 'alert_id_pic');
    }

    public function hasLogPengiriman()
    {
        return $this->hasMany(LogAlert::class, 'log_alert_id_alert', 'alert_id');
    }
}
