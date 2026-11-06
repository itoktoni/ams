<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class OtorisasiKeluar extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'otorisasi_keluar';

    protected $primaryKey = 'otorisasi_keluar_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'otorisasi_keluar_id_aset',
        'otorisasi_keluar_nomor',
        'otorisasi_keluar_jenis',
        'otorisasi_keluar_tanggal_mulai',
        'otorisasi_keluar_tanggal_selesai',
        'otorisasi_keluar_status',
        'otorisasi_keluar_id_approver',
        'otorisasi_keluar_catatan',
    ];

    public static $filterColumns = [
        'otorisasi_keluar_nomor' => 'Nomor',
        'otorisasi_keluar_jenis' => 'Jenis',
        'otorisasi_keluar_status' => 'Status',
    ];

    public static $sortColumns = [
        'otorisasi_keluar_nomor',
        'otorisasi_keluar_jenis',
        'otorisasi_keluar_tanggal_mulai',
        'otorisasi_keluar_tanggal_selesai',
        'otorisasi_keluar_status',
    ];

    protected function casts(): array
    {
        return [
            'otorisasi_keluar_id_aset' => 'integer',
            'otorisasi_keluar_id_approver' => 'integer',
            'otorisasi_keluar_tanggal_mulai' => 'datetime',
            'otorisasi_keluar_tanggal_selesai' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'otorisasi_keluar_nomor';
    }

    public function rules(): array
    {
        return [
            'otorisasi_keluar_id_aset' => 'required|integer',
            'otorisasi_keluar_nomor' => 'required|string|max:100',
            'otorisasi_keluar_jenis' => 'required|string|max:30',
            'otorisasi_keluar_tanggal_mulai' => 'nullable|string',
            'otorisasi_keluar_tanggal_selesai' => 'nullable|string',
            'otorisasi_keluar_status' => 'required|string|max:30',
            'otorisasi_keluar_id_approver' => 'nullable|integer',
            'otorisasi_keluar_catatan' => 'nullable|string',
        ];
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'otorisasi_keluar_id_aset');
    }

    public function hasApprover()
    {
        return $this->hasOne(User::class, 'id', 'otorisasi_keluar_id_approver');
    }

    // shortcut: otorisasi berlaku = status allow (range null = tanpa batas waktu)
    public function scopeBerlaku($query, ?int $asetId = null)
    {
        $now = now();
        $query->where('otorisasi_keluar_status', 'allow')
            ->where(function ($q) use ($now) {
                $q->whereNull('otorisasi_keluar_tanggal_mulai')
                    ->orWhere('otorisasi_keluar_tanggal_mulai', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('otorisasi_keluar_tanggal_selesai')
                    ->orWhere('otorisasi_keluar_tanggal_selesai', '>=', $now);
            });
        if ($asetId) {
            $query->where('otorisasi_keluar_id_aset', $asetId);
        }

        return $query;
    }
}
