<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class DaftarTunggu extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'daftar_tunggu';

    protected $primaryKey = 'daftar_tunggu_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'daftar_tunggu_id_aset',
        'daftar_tunggu_id_peminjam',
        'daftar_tunggu_tanggal_mulai',
        'daftar_tunggu_durasi',
        'daftar_tunggu_status',
        'daftar_tunggu_id_peminjaman',
    ];

    public static $filterColumns = [
        'daftar_tunggu_status' => 'Status',
    ];

    public static $sortColumns = [
        'daftar_tunggu_id',
        'daftar_tunggu_tanggal_mulai',
        'daftar_tunggu_durasi',
        'daftar_tunggu_status',
    ];

    protected function casts(): array
    {
        return [
            'daftar_tunggu_id_aset' => 'integer',
            'daftar_tunggu_id_peminjam' => 'integer',
            'daftar_tunggu_id_peminjaman' => 'integer',
            'daftar_tunggu_tanggal_mulai' => 'datetime',
            'daftar_tunggu_durasi' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'daftar_tunggu_id_aset';
    }

    public function rules(): array
    {
        return [
            'daftar_tunggu_id_aset' => 'nullable|integer',
            'daftar_tunggu_id_peminjam' => 'nullable|integer',
            'daftar_tunggu_tanggal_mulai' => 'required|string',
            'daftar_tunggu_durasi' => 'required|integer',
            'daftar_tunggu_status' => 'required|string',
            'daftar_tunggu_id_peminjaman' => 'nullable|integer',
        ];
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'daftar_tunggu_id_aset');
    }

    public function hasPeminjam()
    {
        return $this->hasOne(User::class, 'id', 'daftar_tunggu_id_peminjam');
    }

    public function hasPeminjaman()
    {
        return $this->hasOne(Peminjaman::class, 'peminjaman_id', 'daftar_tunggu_id_peminjaman');
    }
}
