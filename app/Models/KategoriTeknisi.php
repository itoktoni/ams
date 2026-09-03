<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use Illuminate\Database\Eloquent\Model;

class KategoriTeknisi extends Model
{
    use DefaultEntity, Filterable, Sortable;

    protected $table = 'kategori_teknisi';
    protected $fillable = ['kategori_id', 'teknisi_id'];
    public static $filterColumns = [];
    public static $sortColumns = [];
    public static function field_name(): string { return 'id'; }
    public function rules(): array { return ['kategori_id' => 'required|integer', 'teknisi_id' => 'required|integer']; }
    public function hasKategori() { return $this->hasOne(KategoriAset::class, 'aset_kategori_id', 'kategori_id'); }
    public function hasTeknisi() { return $this->hasOne(Teknisi::class, 'teknisi_id', 'teknisi_id'); }
}
