<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;

class TemplateServiceItem extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'template_service_item';

    protected $primaryKey = 'template_service_item_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'template_service_item_id_template',
        'template_service_item_nama',
        'template_service_item_tipe',
        'template_service_item_id_suku_cadang',
        'template_service_item_jumlah',
        'template_service_item_urutan',
    ];

    public static $filterColumns = [
        'template_service_item_nama' => 'Nama',
        'template_service_item_tipe' => 'Tipe',
    ];

    public static $sortColumns = [
        'template_service_item_nama',
        'template_service_item_tipe',
        'template_service_item_urutan',
    ];

    protected function casts(): array
    {
        return [
            'template_service_item_jumlah' => 'decimal:2',
            'template_service_item_urutan' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'template_service_item_nama';
    }

    public function rules(): array
    {
        return [
            'template_service_item_id_template' => 'required|integer',
            'template_service_item_nama' => 'required|string|max:200',
            'template_service_item_tipe' => 'nullable|string|max:20',
            'template_service_item_id_suku_cadang' => 'nullable|integer',
            'template_service_item_jumlah' => 'nullable|numeric|min:0',
            'template_service_item_urutan' => 'nullable|integer|min:0',
        ];
    }

    public function hasTemplate()
    {
        return $this->hasOne(TemplateService::class, 'template_service_id', 'template_service_item_id_template');
    }
}
