<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

class Faktur extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'faktur';

    protected $primaryKey = 'faktur_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'faktur_nomor',
        'faktur_id_pesanan',
        'faktur_tanggal',
        'faktur_total',
        'faktur_status',
        'faktur_file',
        'faktur_catatan',
    ];

    public static $filterColumns = [
        'faktur_nomor' => 'Nomor',
        'faktur_status' => 'Status',
        'faktur_id_pesanan' => 'Pesanan',
    ];

    public static $sortColumns = [
        'faktur_nomor',
        'faktur_tanggal',
        'faktur_status',
        'faktur_total',
    ];

    protected function casts(): array
    {
        return [
            'faktur_tanggal' => 'date',
            'faktur_total' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'faktur_nomor';
    }

    public function rules(): array
    {
        return [
            'faktur_nomor' => 'required|string|max:60',
            'faktur_id_pesanan' => 'nullable|integer',
            'faktur_tanggal' => 'required|date',
            'faktur_total' => 'nullable|numeric',
            'faktur_status' => 'required|string|max:30',
            'faktur_file' => 'nullable|string|max:255',
            'faktur_catatan' => 'nullable|string',
        ];
    }

    public function hasPesanan()
    {
        return $this->hasOne(PesananPembelian::class, 'pesanan_pembelian_id', 'faktur_id_pesanan');
    }

    public function getFakturFileUrlAttribute(): string
    {
        return fileUrl($this->faktur_file);
    }
}
