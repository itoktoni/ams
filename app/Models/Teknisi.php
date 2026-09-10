<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Teknisi sekarang dari users role=teknisi (1 table).
 * Model ini proxy ke users agar kode lama tetap jalan (teknisi_id → id, teknisi_nama → name, dll).
 */
class Teknisi extends User
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected static function booted(): void
    {
        static::addGlobalScope('role_teknisi', function (Builder $query) {
            $query->where('role', 'teknisi');
        });
    }

    // Backward compat: $teknisi->teknisi_id, teknisi_nama, etc.
    public function getTeknisiIdAttribute(): mixed
    {
        return $this->attributes['id'] ?? $this->id;
    }

    public function getTeknisiNamaAttribute(): mixed
    {
        return $this->attributes['name'] ?? $this->name;
    }

    public function getTeknisiTeleponAttribute(): mixed
    {
        return $this->attributes['phone'] ?? $this->phone;
    }

    public function getTeknisiTelegramIdAttribute(): mixed
    {
        return $this->attributes['telegram_id'] ?? $this->telegram_id;
    }

    public function getTeknisiKodeAttribute(): mixed
    {
        return $this->attributes['teknisi_kode'] ?? null;
    }

    public static function field_name(): string
    {
        return 'name';
    }

    // hasKategori lama via kategori_teknisi sudah di-drop — return kosong agar tidak error table not found
    public function hasKategori()
    {
        return $this->belongsToMany(KategoriAset::class, 'master_group_user', 'master_group_user_id_user', 'master_group_user_id_group')->whereRaw('1=0');
    }

    public function hasKategoriTeknisi()
    {
        return $this->hasMany(KategoriTeknisi::class, 'teknisi_id', 'id')->whereRaw('1=0');
    }
}
