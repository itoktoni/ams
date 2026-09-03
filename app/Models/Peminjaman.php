<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Peminjaman extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'peminjaman';

    protected $primaryKey = 'peminjaman_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'peminjaman_nomor',
        'peminjaman_id_aset',
        'peminjaman_id_peminjam',
        'peminjaman_tujuan',
        'peminjaman_tanggal_pinjam',
        'peminjaman_jatuh_tempo',
        'peminjaman_tanggal_kembali',
        'peminjaman_status',
        'peminjaman_grace_jam',
        'peminjaman_denda',
        'peminjaman_kondisi_kembali',
        'peminjaman_foto_kembali',
        'peminjaman_id_approver',
        'peminjaman_perpanjang_ke',
        'peminjaman_catatan',
    ];

    public static $filterColumns = [
        'peminjaman_nomor' => 'Nomor',
        'peminjaman_status' => 'Status',
    ];

    public static $sortColumns = [
        'peminjaman_nomor',
        'peminjaman_tanggal_pinjam',
        'peminjaman_jatuh_tempo',
        'peminjaman_status',
    ];

    protected function casts(): array
    {
        return [
            'peminjaman_id_aset' => 'integer',
            'peminjaman_id_peminjam' => 'integer',
            'peminjaman_id_approver' => 'integer',
            'peminjaman_tanggal_pinjam' => 'datetime',
            'peminjaman_jatuh_tempo' => 'datetime',
            'peminjaman_tanggal_kembali' => 'datetime',
            'peminjaman_grace_jam' => 'integer',
            'peminjaman_denda' => 'decimal:2',
            'peminjaman_perpanjang_ke' => 'integer',
            'peminjaman_foto_kembali' => 'string',
        ];
    }

    public static function field_name(): string
    {
        return 'peminjaman_nomor';
    }

    public function rules(): array
    {
        return [
            'peminjaman_nomor' => 'required|string',
            'peminjaman_id_aset' => 'nullable|integer',
            'peminjaman_id_peminjam' => 'nullable|integer',
            'peminjaman_tujuan' => 'nullable|string',
            'peminjaman_tanggal_pinjam' => 'required|string',
            'peminjaman_jatuh_tempo' => 'required|string',
            'peminjaman_tanggal_kembali' => 'nullable|string',
            'peminjaman_status' => 'required|string',
            'peminjaman_grace_jam' => 'nullable|integer',
            'peminjaman_denda' => 'nullable|numeric',
            'peminjaman_kondisi_kembali' => 'nullable|string',
            'peminjaman_foto_kembali' => 'nullable|string|max:255',
            'peminjaman_id_approver' => 'nullable|integer',
            'peminjaman_perpanjang_ke' => 'nullable|integer',
            'peminjaman_catatan' => 'nullable|string',
        ];
    }

    public function getPeminjamanFotoKembaliUrlAttribute(): string
    {
        return fileUrl($this->peminjaman_foto_kembali);
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'peminjaman_id_aset');
    }


    public function hasPeminjam()
    {
        return $this->hasOne(User::class, 'id', 'peminjaman_id_peminjam');
    }

    public function hasApprover()
    {
        return $this->hasOne(User::class, 'id', 'peminjaman_id_approver');
    }
}
