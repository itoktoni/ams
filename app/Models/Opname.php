<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Opname extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'opname';

    protected $primaryKey = 'opname_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'opname_nomor',
        'opname_id_lokasi',
        'opname_tanggal',
        'opname_id_petugas',
        'opname_status',
        'opname_total_sistem',
        'opname_total_fisik',
        'opname_total_selisih',
        'opname_catatan',
    ];

    public static $filterColumns = [
        'opname_nomor' => 'Nomor',
        'opname_id_lokasi' => 'Lokasi',
        'opname_status' => 'Status',
        'opname_tanggal' => 'Tanggal',
    ];

    public static $sortColumns = [
        'opname_nomor',
        'opname_id_lokasi',
        'opname_tanggal',
        'opname_status',
    ];

    protected function casts(): array
    {
        return [
            'opname_tanggal' => 'date',
        ];
    }

    public static function field_name(): string
    {
        return 'opname_nomor';
    }

    public function rules(): array
    {
        return [
            'opname_nomor' => 'required|string|max:255',
            'opname_id_lokasi' => 'required|integer',
            'opname_tanggal' => 'required|date',
            'opname_id_petugas' => 'required|integer',
            'opname_status' => 'required|string|max:255',
            'opname_total_sistem' => 'nullable|integer',
            'opname_total_fisik' => 'nullable|integer',
            'opname_total_selisih' => 'nullable|integer',
            'opname_catatan' => 'nullable|string',
        ];
    }

    public function hasLokasi()
    {
        return $this->hasOne(LokasiAset::class, 'aset_lokasi_id', 'opname_id_lokasi');
    }

    public function hasPetugas()
    {
        return $this->hasOne(User::class, 'id', 'opname_id_petugas');
    }

    public function hasDetailOpname()
    {
        return $this->hasMany(OpnameDetail::class, 'opname_detail_id_opname', 'opname_id');
    }
}
