<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class Aset extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'aset';

    protected $primaryKey = 'aset_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'aset_kode',
        'aset_nama',
        'aset_id_kategori',
        'aset_id_lokasi',
        'aset_id_penanggung_jawab',
        'aset_id_vendor',
        'aset_merek',
        'aset_model',
        'aset_nomor_seri',
        'aset_tanggal_perolehan',
        'aset_harga_perolehan',
        'aset_nilai_sisa',
        'aset_masa_manfaat',
        'aset_metode_penyusutan',
        'aset_tanggal_mulai_susut',
        'aset_status',
        'aset_kondisi',
        'aset_foto',
        'aset_kode_qr',
        'aset_jam_pakai',
        'aset_custom_fields',
        'aset_catatan',
    ];

    public static $filterColumns = [
        'aset_kode' => 'Kode',
        'aset_nama' => 'Nama',
        'aset_merek' => 'Merek',
        'aset_nomor_seri' => 'Nomor Seri',
        'aset_status' => 'Status',
        'aset_kondisi' => 'Kondisi',
    ];

    public static $sortColumns = [
        'aset_kode',
        'aset_nama',
        'aset_merek',
        'aset_tanggal_perolehan',
        'aset_harga_perolehan',
        'aset_status',
        'aset_kondisi',
    ];

    protected function casts(): array
    {
        return [
            'aset_tanggal_perolehan' => 'date',
            'aset_tanggal_mulai_susut' => 'date',
            'aset_harga_perolehan' => 'decimal:2',
            'aset_nilai_sisa' => 'decimal:2',
            'aset_masa_manfaat' => 'integer',
            'aset_jam_pakai' => 'decimal:2',
            'aset_custom_fields' => 'array',
        ];
    }

    public static function field_name(): string
    {
        return 'aset_nama';
    }

    public function rules(): array
    {
        return [
            'aset_kode' => 'required|string|max:40',
            'aset_nama' => 'required|string|max:200',
            'aset_id_kategori' => 'nullable|integer',
            'aset_id_lokasi' => 'nullable|integer',
            'aset_id_penanggung_jawab' => 'nullable|integer',
            'aset_id_vendor' => 'nullable|integer',
            'aset_merek' => 'nullable|string|max:80',
            'aset_model' => 'nullable|string|max:80',
            'aset_nomor_seri' => 'nullable|string|max:100',
            'aset_tanggal_perolehan' => 'nullable|date',
            'aset_harga_perolehan' => 'nullable|numeric|min:0',
            'aset_nilai_sisa' => 'nullable|numeric|min:0',
            'aset_masa_manfaat' => 'nullable|integer|min:0',
            'aset_metode_penyusutan' => 'nullable|string|max:30',
            'aset_tanggal_mulai_susut' => 'nullable|date',
            'aset_status' => 'nullable|string|max:30',
            'aset_kondisi' => 'nullable|string|max:30',
            'aset_foto' => 'nullable|string|max:255',
            'aset_kode_qr' => 'nullable|string|max:100',
            'aset_jam_pakai' => 'nullable|numeric|min:0',
            'aset_custom_fields' => 'nullable|array',
            'aset_custom_fields.*' => 'nullable|string|max:5000',
            'aset_catatan' => 'nullable|string',
        ];
    }

    public function getFotoUrlAttribute(): string
    {
        return fileUrl($this->aset_foto);
    }

    public function hasKategori()
    {
        return $this->hasOne(KategoriAset::class, 'aset_kategori_id', 'aset_id_kategori');
    }

    public function hasLokasi()
    {
        return $this->hasOne(LokasiAset::class, 'aset_lokasi_id', 'aset_id_lokasi');
    }

    public function hasPenanggungJawab()
    {
        return $this->hasOne(User::class, 'id', 'aset_id_penanggung_jawab');
    }

    public function hasVendor()
    {
        return $this->hasOne(Vendor::class, 'vendor_id', 'aset_id_vendor');
    }

    public function hasDokumen()
    {
        return $this->hasMany(DokumenAset::class, 'aset_dokumen_id_aset', 'aset_id');
    }

    public function hasLogStatus()
    {
        return $this->hasMany(LogStatusAset::class, 'log_status_aset_id_aset', 'aset_id');
    }
}
