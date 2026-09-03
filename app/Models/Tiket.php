<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use App\Enums\Tiket\StatusTiketEnum;
use App\Enums\Tiket\TingkatUrgensiEnum;
use Illuminate\Database\Eloquent\Model;

class Tiket extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'tiket';

    protected $primaryKey = 'tiket_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'tiket_nomor',
        'tiket_id_aset',
        'tiket_id_pelapor',
        'tiket_id_teknisi',
        'tiket_judul',
        'tiket_deskripsi',
        'tiket_tingkat_urgensi',
        'tiket_status',
        'tiket_id_lokasi',
        'tiket_latitude',
        'tiket_longitude',
        'tiket_foto_sebelum',
        'tiket_foto_sesudah',
        'tiket_tanggal_lapor',
        'tiket_tanggal_tugas',
        'tiket_tanggal_mulai',
        'tiket_tanggal_selesai',
        'tiket_tanggal_verifikasi',
        'tiket_jatuh_tempo',
        'tiket_terlambat_sla',
        'tiket_level_eskalasi',
        'tiket_id_batch',
        'tiket_biaya',
        'tiket_rating',
        'tiket_catatan',
    ];

    public static $filterColumns = [
        'tiket_nomor' => 'Nomor',
        'tiket_judul' => 'Judul',
        'tiket_status' => 'Status',
        'tiket_tingkat_urgensi' => 'Urgensi',
    ];

    public static $sortColumns = [
        'tiket_nomor',
        'tiket_judul',
        'tiket_status',
        'tiket_tingkat_urgensi',
        'tiket_tanggal_lapor',
    ];

    protected function casts(): array
    {
        return [
            'tiket_latitude' => 'decimal:8',
            'tiket_longitude' => 'decimal:8',
            'tiket_biaya' => 'decimal:2',
            'tiket_rating' => 'decimal:2',
            'tiket_terlambat_sla' => 'boolean',
            'tiket_level_eskalasi' => 'integer',
            'tiket_tanggal_lapor' => 'datetime',
            'tiket_tanggal_tugas' => 'datetime',
            'tiket_tanggal_mulai' => 'datetime',
            'tiket_tanggal_selesai' => 'datetime',
            'tiket_tanggal_verifikasi' => 'datetime',
            'tiket_jatuh_tempo' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'tiket_judul';
    }

    public function rules(): array
    {
        return [
            'tiket_nomor' => 'required|string|max:255',
            'tiket_id_aset' => 'required|integer',
            'tiket_id_pelapor' => 'required|integer',
            'tiket_id_teknisi' => 'nullable|integer',
            'tiket_judul' => 'required|string|max:255',
            'tiket_deskripsi' => 'nullable|string',
            'tiket_tingkat_urgensi' => 'required|string|max:50',
            'tiket_status' => 'required|string|max:50',
            'tiket_id_lokasi' => 'nullable|integer',
            'tiket_latitude' => 'nullable|numeric',
            'tiket_longitude' => 'nullable|numeric',
            'tiket_foto_sebelum' => 'nullable|string|max:255',
            'tiket_foto_sesudah' => 'nullable|string|max:255',
            'tiket_tanggal_lapor' => 'nullable|date',
            'tiket_tanggal_tugas' => 'nullable|date',
            'tiket_tanggal_mulai' => 'nullable|date',
            'tiket_tanggal_selesai' => 'nullable|date',
            'tiket_tanggal_verifikasi' => 'nullable|date',
            'tiket_jatuh_tempo' => 'nullable|date',
            'tiket_terlambat_sla' => 'nullable|boolean',
            'tiket_level_eskalasi' => 'nullable|integer',
            'tiket_id_batch' => 'nullable|integer',
            'tiket_biaya' => 'nullable|numeric',
            'tiket_rating' => 'nullable|numeric',
            'tiket_catatan' => 'nullable|string',
        ];
    }

    public function getTiketFotoSebelumUrlAttribute(): string
    {
        return fileUrl($this->tiket_foto_sebelum);
    }

    public function getTiketFotoSesudahUrlAttribute(): string
    {
        return fileUrl($this->tiket_foto_sesudah);
    }

    public function hasAset()
    {
        return $this->hasOne(Aset::class, 'aset_id', 'tiket_id_aset');
    }

    public function hasPelapor()
    {
        return $this->hasOne(User::class, 'id', 'tiket_id_pelapor');
    }

    public function hasTeknisi()
    {
        return $this->hasOne(Teknisi::class, 'teknisi_id', 'tiket_id_teknisi');
    }

    public function hasLokasi()
    {
        return $this->hasOne(LokasiAset::class, 'aset_lokasi_id', 'tiket_id_lokasi');
    }

    public function hasBatch()
    {
        return $this->hasOne(BatchTiket::class, 'batch_tiket_id', 'tiket_id_batch');
    }

    public function hasLogTiket()
    {
        return $this->hasMany(TiketLog::class, 'tiket_log_id_tiket', 'tiket_id');
    }

    public function hasSukuCadangTerpakai()
    {
        return $this->hasMany(TiketSukuCadang::class, 'tiket_suku_cadang_id_tiket', 'tiket_id');
    }
}
