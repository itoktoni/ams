<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class ReputasiPeminjam extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'reputasi_peminjam';

    protected $primaryKey = 'reputasi_peminjam_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'reputasi_peminjam_id_user',
        'reputasi_peminjam_skor',
        'reputasi_peminjam_total_pinjam',
        'reputasi_peminjam_terlambat',
        'reputasi_peminjam_limit_pinjam',
        'reputasi_peminjam_durasi_maks',
    ];

    public static $filterColumns = [
        'reputasi_peminjam_id_user' => 'User',
    ];

    public static $sortColumns = [
        'reputasi_peminjam_id',
        'reputasi_peminjam_id_user',
        'reputasi_peminjam_skor',
        'reputasi_peminjam_terlambat',
    ];

    protected function casts(): array
    {
        return [
            'reputasi_peminjam_id_user' => 'integer',
            'reputasi_peminjam_skor' => 'decimal:2',
            'reputasi_peminjam_total_pinjam' => 'integer',
            'reputasi_peminjam_terlambat' => 'integer',
            'reputasi_peminjam_limit_pinjam' => 'integer',
            'reputasi_peminjam_durasi_maks' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'reputasi_peminjam_id_user';
    }

    public function rules(): array
    {
        return [
            'reputasi_peminjam_id_user' => 'nullable|integer',
            'reputasi_peminjam_skor' => 'nullable|numeric',
            'reputasi_peminjam_total_pinjam' => 'nullable|integer',
            'reputasi_peminjam_terlambat' => 'nullable|integer',
            'reputasi_peminjam_limit_pinjam' => 'nullable|integer',
            'reputasi_peminjam_durasi_maks' => 'nullable|integer',
        ];
    }

    public function hasUser()
    {
        return $this->hasOne(User::class, 'id', 'reputasi_peminjam_id_user');
    }
}
