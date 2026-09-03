<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BatchTiket extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'batch_tiket';

    protected $primaryKey = 'batch_tiket_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'batch_tiket_kode',
        'batch_tiket_id_teknisi',
        'batch_tiket_tanggal',
        'batch_tiket_zona',
        'batch_tiket_mode',
        'batch_tiket_status',
        'batch_tiket_urutan',
        'batch_tiket_total_eta',
        'batch_tiket_total_jarak',
    ];

    public static $filterColumns = [
        'batch_tiket_kode' => 'Kode',
        'batch_tiket_mode' => 'Mode',
        'batch_tiket_status' => 'Status',
    ];

    public static $sortColumns = [
        'batch_tiket_kode',
        'batch_tiket_tanggal',
        'batch_tiket_mode',
        'batch_tiket_status',
    ];

    protected function casts(): array
    {
        return [
            'batch_tiket_tanggal' => 'date',
            'batch_tiket_total_eta' => 'decimal:2',
            'batch_tiket_total_jarak' => 'decimal:2',
        ];
    }

    protected function batchTiketUrutan(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_array($value) ? $value : (is_string($value) && str_starts_with(trim($value), '[') ? (json_decode($value, true) ?: []) : ($value ? array_values(array_filter(array_map('trim', explode(',', (string) $value)))) : [])),
            set: function ($value) {
                if ($value === null || $value === '') return null;
                if (is_array($value)) return $value;
                $trim = trim((string) $value);
                if (str_starts_with($trim, '[')) {
                    $dec = json_decode($trim, true);
                    return is_array($dec) ? $dec : [$trim];
                }
                return array_values(array_filter(array_map('trim', explode(',', $trim)), fn ($v) => $v !== ''));
            }
        );
    }

    public static function field_name(): string
    {
        return 'batch_tiket_kode';
    }

    public function rules(): array
    {
        return [
            'batch_tiket_kode' => 'required|string|max:255',
            'batch_tiket_id_teknisi' => 'required|integer',
            'batch_tiket_tanggal' => 'required|date',
            'batch_tiket_zona' => 'nullable|string|max:100',
            'batch_tiket_mode' => 'required|string|max:50',
            'batch_tiket_status' => 'required|string|max:50',
            'batch_tiket_urutan' => 'nullable|string',
            'batch_tiket_total_eta' => 'nullable|numeric',
            'batch_tiket_total_jarak' => 'nullable|numeric',
        ];
    }

    public function hasTeknisi()
    {
        return $this->hasOne(Teknisi::class, 'teknisi_id', 'batch_tiket_id_teknisi');
    }
}
