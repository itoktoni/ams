<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class LogStatusAset extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'log_status_aset';

    protected $primaryKey = 'log_status_aset_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'log_status_aset_id_aset',
        'log_status_aset_status_dari',
        'log_status_aset_status_ke',
        'log_status_aset_actor',
        'log_status_aset_catatan',
    ];

    public static $filterColumns = [
        'log_status_aset_status_dari' => 'Status Dari',
        'log_status_aset_status_ke' => 'Status Ke',
    ];

    public static $sortColumns = [
        'log_status_aset_id',
        'log_status_aset_id_aset',
        'log_status_aset_status_dari',
        'log_status_aset_status_ke',
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
        return 'log_status_aset_status_ke';
    }

    public function rules(): array
    {
        return [
            'log_status_aset_id_aset' => 'nullable|integer',
            'log_status_aset_status_dari' => 'nullable|string|max:30',
            'log_status_aset_status_ke' => 'nullable|string|max:30',
            'log_status_aset_actor' => 'nullable|integer',
            'log_status_aset_catatan' => 'nullable|string',
        ];
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'log_status_aset_id_aset');
    }

    public function hasActor()
    {
        return $this->hasOne(User::class, 'id', 'log_status_aset_actor');
    }
}
