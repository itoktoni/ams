<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Persetujuan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'persetujuan';

    protected $primaryKey = 'persetujuan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'persetujuan_modul',
        'persetujuan_id_referensi',
        'persetujuan_level',
        'persetujuan_id_user',
        'persetujuan_status',
        'persetujuan_catatan',
    ];

    public static $filterColumns = [
        'persetujuan_modul' => 'Modul',
        'persetujuan_level' => 'Level',
        'persetujuan_id_user' => 'User',
        'persetujuan_status' => 'Status',
    ];

    public static $sortColumns = [
        'persetujuan_modul',
        'persetujuan_level',
        'persetujuan_id_user',
        'persetujuan_status',
    ];

    protected function casts(): array
    {
        return [];
    }

    public static function field_name(): string
    {
        return 'persetujuan_modul';
    }

    public function rules(): array
    {
        return [
            'persetujuan_modul' => 'required|string|max:255',
            'persetujuan_id_referensi' => 'nullable|integer',
            'persetujuan_level' => 'required|string|max:255',
            'persetujuan_id_user' => 'required|integer',
            'persetujuan_status' => 'required|string|max:255',
            'persetujuan_catatan' => 'nullable|string',
        ];
    }

    public function hasUser()
    {
        return $this->hasOne(User::class, 'id', 'persetujuan_id_user');
    }
}
