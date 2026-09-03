<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class KategoriAset extends BaseModel
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'aset_kategori';

    protected $primaryKey = 'aset_kategori_id';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'aset_kategori_nama',
        'aset_kategori_kode',
        'aset_kategori_masa_manfaat',
        'aset_kategori_metode_penyusutan',
        'aset_kategori_keterangan',
        'aset_kategori_custom_fields',
    ];

    public static $filterColumns = [
        'aset_kategori_kode' => 'Kode',
        'aset_kategori_nama' => 'Nama',
        'aset_kategori_metode_penyusutan' => 'Metode Penyusutan',
    ];

    public static $sortColumns = [
        'aset_kategori_kode',
        'aset_kategori_nama',
        'aset_kategori_masa_manfaat',
        'aset_kategori_metode_penyusutan',
    ];

    protected function casts(): array
    {
        return [
            'aset_kategori_masa_manfaat' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'aset_kategori_nama';
    }

    /**
     * Per-category custom field DEFINITIONS.
     *
     * Stored as JSON; normalized on write so every definition has a stable,
     * unique `key` (slug of the label), a validated `type` and `options`.
     * Decoded back to an array on read.
     *
     * Definition shape:
     *   {"key":"no_stnk","label":"No STNK","type":"text","options":""}
     * Allowed types: text | number | date | textarea | select
     */
    protected function asetKategoriCustomFields(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($value) && $value !== ''
                ? (json_decode($value, true) ?: [])
                : (is_array($value) ? $value : []),
            set: function ($value) {
                $raw = is_string($value) ? json_decode($value, true) : ($value ?? []);
                $raw = is_array($raw) ? $raw : [];
                $allowed = ['text', 'number', 'date', 'textarea', 'select'];
                $usedKeys = [];
                $out = [];

                foreach ($raw as $field) {
                    if (! is_array($field)) {
                        continue;
                    }

                    $label = trim((string) ($field['label'] ?? ''));
                    if ($label === '') {
                        continue; // skip empty rows
                    }

                    $type = in_array($field['type'] ?? 'text', $allowed, true)
                        ? $field['type']
                        : 'text';

                    $base = $this->slugify($label);
                    $key = $base;
                    $n = 1;
                    while (in_array($key, $usedKeys, true)) {
                        $key = $base.'_'.$n++;
                    }
                    $usedKeys[] = $key;

                    $out[] = [
                        'key' => $key,
                        'label' => $label,
                        'type' => $type,
                        'options' => $type === 'select' ? trim((string) ($field['options'] ?? '')) : '',
                    ];
                }

                return json_encode($out);
            },
        );
    }

    /**
     * Convert a human label into a safe machine key: lowercase, non-alphanumeric -> underscore.
     */
    protected function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);
        $text = trim($text, '_');

        return $text === '' ? 'field' : $text;
    }

    public function rules(): array
    {
        return [
            'aset_kategori_nama' => 'required|string|max:150',
            'aset_kategori_kode' => 'required|string|max:30',
            'aset_kategori_masa_manfaat' => 'nullable|integer|min:0',
            'aset_kategori_metode_penyusutan' => 'nullable|string|max:30',
            'aset_kategori_keterangan' => 'nullable|string',
            'aset_kategori_custom_fields' => 'nullable|array',
            'aset_kategori_custom_fields.*.label' => 'nullable|string|max:150',
            'aset_kategori_custom_fields.*.type' => 'nullable|string|max:20',
            'aset_kategori_custom_fields.*.options' => 'nullable|string|max:1000',
        ];
    }


}
