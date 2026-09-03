<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class BukuPenyusutan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'buku_penyusutan';

    protected $primaryKey = 'buku_penyusutan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'buku_penyusutan_id_aset',
        'buku_penyusutan_periode',
        'buku_penyusutan_tanggal',
        'buku_penyusutan_nilai',
        'buku_penyusutan_akumulasi',
        'buku_penyusutan_nilai_buku',
        'buku_penyusutan_reversalisasi_dari',
        'buku_penyusutan_hash',
        'buku_penyusutan_hash_sebelum',
        'buku_penyusutan_dibuat_oleh',
    ];

    public static $filterColumns = [
        'buku_penyusutan_id_aset' => 'Aset',
        'buku_penyusutan_periode' => 'Periode',
    ];

    public static $sortColumns = [
        'buku_penyusutan_periode',
        'buku_penyusutan_nilai',
        'buku_penyusutan_akumulasi',
        'buku_penyusutan_nilai_buku',
    ];

    protected function casts(): array
    {
        return [
            'buku_penyusutan_tanggal' => 'datetime',
            'buku_penyusutan_nilai' => 'decimal:2',
            'buku_penyusutan_akumulasi' => 'decimal:2',
            'buku_penyusutan_nilai_buku' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'buku_penyusutan_periode';
    }

    public function rules(): array
    {
        return [
            'buku_penyusutan_id_aset' => 'nullable|integer',
            'buku_penyusutan_periode' => 'nullable|string|max:7',
            'buku_penyusutan_tanggal' => 'nullable|date',
            'buku_penyusutan_nilai' => 'nullable|numeric|min:0',
            'buku_penyusutan_akumulasi' => 'nullable|numeric',
            'buku_penyusutan_nilai_buku' => 'nullable|numeric',
            'buku_penyusutan_reversalisasi_dari' => 'nullable|integer',
            'buku_penyusutan_hash' => 'nullable|string|max:64',
            'buku_penyusutan_hash_sebelum' => 'nullable|string|max:64',
            'buku_penyusutan_dibuat_oleh' => 'nullable|integer',
        ];
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'buku_penyusutan_id_aset');
    }

    public function hasReversalisasiDari()
    {
        return $this->hasOne(BukuPenyusutan::class, 'buku_penyusutan_id', 'buku_penyusutan_reversalisasi_dari');
    }

    public function hasDibuatOleh()
    {
        return $this->hasOne(User::class, 'id', 'buku_penyusutan_dibuat_oleh');
    }
}
