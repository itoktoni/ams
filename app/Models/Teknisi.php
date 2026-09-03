<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Teknisi extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'teknisi';

    protected $primaryKey = 'teknisi_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'teknisi_id_user',
        'teknisi_kode',
        'teknisi_nama',
        'teknisi_telepon',
        'teknisi_keahlian',
        'teknisi_zona',
        'teknisi_sertifikasi',
        'teknisi_rating',
        'teknisi_total_tiket',
        'teknisi_total_revisi',
        'teknisi_latitude',
        'teknisi_longitude',
        'teknisi_waktu_posisi',
        'teknisi_status',
    ];

    public static $filterColumns = [
        'teknisi_nama' => 'Nama',
        'teknisi_kode' => 'Kode',
        'teknisi_status' => 'Status',
    ];

    public static $sortColumns = [
        'teknisi_nama',
        'teknisi_kode',
        'teknisi_status',
        'teknisi_rating',
        'teknisi_total_tiket',
    ];

    protected function casts(): array
    {
        return [
            'teknisi_rating' => 'decimal:2',
            'teknisi_total_tiket' => 'integer',
            'teknisi_total_revisi' => 'integer',
            'teknisi_latitude' => 'decimal:8',
            'teknisi_longitude' => 'decimal:8',
            'teknisi_waktu_posisi' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'teknisi_nama';
    }

    public function rules(): array
    {
        return [
            'teknisi_id_user' => 'nullable|integer',
            'teknisi_kode' => 'required|string|max:255',
            'teknisi_nama' => 'required|string|max:255',
            'teknisi_telepon' => 'nullable|string|max:50',
            'teknisi_keahlian' => 'nullable|string',
            'teknisi_zona' => 'nullable|string',
            'teknisi_sertifikasi' => 'nullable|string',
            'teknisi_rating' => 'nullable|numeric',
            'teknisi_total_tiket' => 'nullable|integer',
            'teknisi_total_revisi' => 'nullable|integer',
            'teknisi_latitude' => 'nullable|numeric',
            'teknisi_longitude' => 'nullable|numeric',
            'teknisi_waktu_posisi' => 'nullable|date',
            'teknisi_status' => 'required|string|max:50',
        ];
    }

    protected function teknisiKeahlian(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_array($value) ? $value : (is_string($value) && str_starts_with(trim($value), '[') ? (json_decode($value, true) ?: []) : ($value ? array_map('trim', explode(',', (string) $value)) : [])),
            set: function ($value) {
                if ($value === null || $value === '') return null;
                if (is_array($value)) return $value;
                $trim = trim((string) $value);
                if (str_starts_with($trim, '[')) {
                    $decoded = json_decode($trim, true);
                    return is_array($decoded) ? $decoded : [$trim];
                }
                return array_values(array_filter(array_map('trim', explode(',', $trim)), fn ($v) => $v !== ''));
            }
        );
    }

    protected function teknisiZona(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_array($value) ? $value : (is_string($value) && str_starts_with(trim($value), '[') ? (json_decode($value, true) ?: []) : ($value ? array_map('trim', explode(',', (string) $value)) : [])),
            set: function ($value) {
                if ($value === null || $value === '') return null;
                if (is_array($value)) return $value;
                $trim = trim((string) $value);
                if (str_starts_with($trim, '[')) {
                    $decoded = json_decode($trim, true);
                    return is_array($decoded) ? $decoded : [$trim];
                }
                return array_values(array_filter(array_map('trim', explode(',', $trim)), fn ($v) => $v !== ''));
            }
        );
    }

    protected function teknisiSertifikasi(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_array($value) ? $value : (is_string($value) && str_starts_with(trim($value), '[') ? (json_decode($value, true) ?: []) : ($value ? array_map('trim', explode(',', (string) $value)) : [])),
            set: function ($value) {
                if ($value === null || $value === '') return null;
                if (is_array($value)) return $value;
                $trim = trim((string) $value);
                if (str_starts_with($trim, '[')) {
                    $decoded = json_decode($trim, true);
                    return is_array($decoded) ? $decoded : [$trim];
                }
                return array_values(array_filter(array_map('trim', explode(',', $trim)), fn ($v) => $v !== ''));
            }
        );
    }

    public function hasUser()
    {
        return $this->hasOne(User::class, 'id', 'teknisi_id_user');
    }
}
