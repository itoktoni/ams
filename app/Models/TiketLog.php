<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class TiketLog extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'tiket_log';

    protected $primaryKey = 'tiket_log_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'tiket_log_id_tiket',
        'tiket_log_status_dari',
        'tiket_log_status_ke',
        'tiket_log_actor',
        'tiket_log_catatan',
    ];

    public static $filterColumns = [];

    public static $sortColumns = [
        'tiket_log_id_tiket',
        'tiket_log_status_ke',
        'tiket_log_actor',
    ];

    protected function casts(): array
    {
        return [];
    }

    public static function field_name(): string
    {
        return 'tiket_log_status_ke';
    }

    public function rules(): array
    {
        return [
            'tiket_log_id_tiket' => 'required|integer',
            'tiket_log_status_dari' => 'nullable|string|max:50',
            'tiket_log_status_ke' => 'required|string|max:50',
            'tiket_log_actor' => 'nullable|integer',
            'tiket_log_catatan' => 'nullable|string',
        ];
    }

    public function hasTiket()
    {
        return $this->hasOne(Tiket::class, 'tiket_id', 'tiket_log_id_tiket');
    }
}
