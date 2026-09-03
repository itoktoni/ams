<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class TemplateService extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'template_service';

    protected $primaryKey = 'template_service_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'template_service_kode',
        'template_service_nama',
        'template_service_id_kategori',
        'template_service_interval_bulan',
        'template_service_interval_jam',
        'template_service_estimasi_jam',
        'template_service_keterangan',
    ];

    public static $filterColumns = [
        'template_service_kode' => 'Kode',
        'template_service_nama' => 'Nama',
        'template_service_id_kategori' => 'Kategori',
    ];

    public static $sortColumns = [
        'template_service_kode',
        'template_service_nama',
        'template_service_id_kategori',
    ];

    protected function casts(): array
    {
        return [
            'template_service_interval_bulan' => 'integer',
            'template_service_interval_jam' => 'decimal:2',
            'template_service_estimasi_jam' => 'decimal:2',
        ];
    }

    public static function field_name(): string
    {
        return 'template_service_nama';
    }

    public function rules(): array
    {
        return [
            'template_service_kode' => 'required|string|max:40',
            'template_service_nama' => 'required|string|max:200',
            'template_service_id_kategori' => 'nullable|integer',
            'template_service_interval_bulan' => 'nullable|integer|min:0',
            'template_service_interval_jam' => 'nullable|numeric|min:0',
            'template_service_estimasi_jam' => 'nullable|numeric|min:0',
            'template_service_keterangan' => 'nullable|string',
        ];
    }

    public function hasKategori()
    {
        return $this->hasOne(KategoriAset::class, 'aset_kategori_id', 'template_service_id_kategori');
    }

    public function hasItemTemplate()
    {
        return $this->hasMany(TemplateServiceItem::class, 'template_service_item_id_template', 'template_service_id');
    }
}
