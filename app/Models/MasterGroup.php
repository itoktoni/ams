<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class MasterGroup extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'master_group';

    protected $primaryKey = 'master_group_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'master_group_kode',
        'master_group_nama',
        'master_group_keterangan',
        'master_group_status',
    ];

    public static $filterColumns = [
        'master_group_kode' => 'Kode',
        'master_group_nama' => 'Nama',
        'master_group_status' => 'Status',
    ];

    public static $sortColumns = [
        'master_group_kode',
        'master_group_nama',
        'master_group_status',
    ];

    protected function casts(): array
    {
        return [];
    }

    public static function field_name(): string
    {
        return 'master_group_nama';
    }

    public function rules(): array
    {
        return [
            'master_group_kode' => 'required|string|max:40',
            'master_group_nama' => 'required|string|max:100',
            'master_group_keterangan' => 'nullable|string',
            'master_group_status' => 'nullable|string|max:20',
            'teknisi_ids' => 'nullable|array',
            'teknisi_ids.*' => 'integer|exists:teknisi,teknisi_id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ];
    }

    // Teknisi sekarang dari users role=teknisi (1 table)
    public function hasUsers()
    {
        return $this->belongsToMany(User::class, 'master_group_user', 'master_group_user_id_group', 'master_group_user_id_user')
            ->withPivot(['master_group_user_id'])
            ->withTimestamps();
    }

    // Deprecated: pivot lama master_group_teknisi → users, keep for BC sebelum migrate drop
    public function hasTeknisi()
    {
        // setelah consolidate, teknisi = users role teknisi via master_group_user
        return $this->hasUsers();
    }
}
