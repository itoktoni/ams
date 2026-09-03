<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class Penerimaan extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'penerimaan';

    protected $primaryKey = 'penerimaan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'penerimaan_id_pesanan',
        'penerimaan_nomor',
        'penerimaan_tanggal',
        'penerimaan_foto',
        'penerimaan_penerima',
        'penerimaan_catatan',
    ];

    public static $filterColumns = [
        'penerimaan_nomor' => 'Nomor',
        'penerimaan_id_pesanan' => 'Pesanan',
        'penerimaan_penerima' => 'Penerima',
    ];

    public static $sortColumns = [
        'penerimaan_nomor',
        'penerimaan_tanggal',
        'penerimaan_penerima',
    ];

    protected function casts(): array
    {
        return [
            'penerimaan_tanggal' => 'date',
        ];
    }

    public static function field_name(): string
    {
        return 'penerimaan_nomor';
    }

    public function rules(): array
    {
        return [
            'penerimaan_id_pesanan' => 'nullable|integer',
            'penerimaan_nomor' => 'required|string|max:60',
            'penerimaan_tanggal' => 'required|date',
            'penerimaan_foto' => 'nullable|string|max:255',
            'penerimaan_penerima' => 'required|string|max:120',
            'penerimaan_catatan' => 'nullable|string',
        ];
    }

    public function hasPesanan()
    {
        return $this->hasOne(PesananPembelian::class, 'pesanan_pembelian_id', 'penerimaan_id_pesanan');
    }

    public function getPenerimaanFotoUrlAttribute(): string
    {
        return fileUrl($this->penerimaan_foto);
    }
}
