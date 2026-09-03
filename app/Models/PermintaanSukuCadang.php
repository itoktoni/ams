<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use App\Enums\SukuCadang\StatusPermintaanEnum;
use Illuminate\Database\Eloquent\Model;

class PermintaanSukuCadang extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'permintaan_suku_cadang';

    protected $primaryKey = 'permintaan_suku_cadang_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'permintaan_suku_cadang_nomor',
        'permintaan_suku_cadang_id_tiket',
        'permintaan_suku_cadang_id_suku_cadang',
        'permintaan_suku_cadang_id_peminta',
        'department_id',
        'permintaan_suku_cadang_jumlah',
        'permintaan_suku_cadang_harga',
        'permintaan_suku_cadang_subtotal',
        'permintaan_suku_cadang_status',
        'permintaan_suku_cadang_tanggal_permintaan',
        'permintaan_suku_cadang_catatan',
    ];

    public static $filterColumns = [
        'permintaan_suku_cadang_nomor' => 'Nomor',
        'permintaan_suku_cadang_status' => 'Status',
    ];

    public static $sortColumns = [
        'permintaan_suku_cadang_nomor',
        'permintaan_suku_cadang_jumlah',
        'permintaan_suku_cadang_subtotal',
        'permintaan_suku_cadang_status',
        'permintaan_suku_cadang_tanggal_permintaan',
    ];

    protected function casts(): array
    {
        return [
            'permintaan_suku_cadang_jumlah' => 'decimal:2',
            'permintaan_suku_cadang_harga' => 'decimal:2',
            'permintaan_suku_cadang_subtotal' => 'decimal:2',
            'permintaan_suku_cadang_tanggal_permintaan' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'permintaan_suku_cadang_nomor';
    }

    public function rules(): array
    {
        return [
            'permintaan_suku_cadang_nomor' => 'nullable|string|max:50',
            'permintaan_suku_cadang_id_tiket' => 'nullable|integer',
            'permintaan_suku_cadang_id_suku_cadang' => 'required|integer',
            'permintaan_suku_cadang_id_peminta' => 'nullable|integer',
            'permintaan_suku_cadang_jumlah' => 'required|numeric|min:1',
            'permintaan_suku_cadang_harga' => 'nullable|numeric',
            'permintaan_suku_cadang_subtotal' => 'nullable|numeric',
            'permintaan_suku_cadang_status' => 'nullable|string|max:50',
            'permintaan_suku_cadang_tanggal_permintaan' => 'nullable|date',
            'permintaan_suku_cadang_catatan' => 'nullable|string',
        ];
    }

    public function hasTiket()
    {
        return $this->hasOne(Tiket::class, 'tiket_id', 'permintaan_suku_cadang_id_tiket');
    }

    public function hasSukuCadang()
    {
        return $this->hasOne(SukuCadang::class, 'suku_cadang_id', 'permintaan_suku_cadang_id_suku_cadang');
    }

    public function hasPeminta()
    {
        return $this->hasOne(User::class, 'id', 'permintaan_suku_cadang_id_peminta');
    }

    public function hasDepartment()
    {
        return $this->hasOne(Department::class, 'department_id', 'department_id');
    }

    public function getStatusOptions(): array
    {
        return StatusPermintaanEnum::getOptions();
    }
}
