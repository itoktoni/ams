<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $alert_id
 * @property string $alert_tipe
 * @property int|null $alert_id_referensi
 * @property string|null $alert_tipe_referensi
 * @property string $alert_judul
 * @property string $alert_pesan
 * @property string $alert_level
 * @property string|null $alert_kunci_dedup
 * @property \Carbon\CarbonImmutable|null $alert_jatuh_tempo
 * @property int|null $alert_id_pic
 * @property string $alert_status
 * @property int $alert_level_eskalasi
 * @property \Carbon\CarbonImmutable|null $alert_terakhir_kirim
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LogAlert> $hasLogPengiriman
 * @property-read int|null $has_log_pengiriman_count
 * @property-read \App\Models\User|null $hasPic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertIdPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertIdReferensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertJatuhTempo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertKunciDedup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertLevelEskalasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertPesan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertTerakhirKirim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereAlertTipeReferensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereUpdatedAt($value)
 */
	class Alert extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $aset_id
 * @property string $aset_kode
 * @property string $aset_nama
 * @property int|null $aset_id_kategori
 * @property int|null $aset_id_lokasi
 * @property int|null $aset_id_penanggung_jawab
 * @property int|null $aset_id_vendor
 * @property string|null $aset_merek
 * @property string|null $aset_model
 * @property string|null $aset_nomor_seri
 * @property \Carbon\CarbonImmutable|null $aset_tanggal_perolehan
 * @property numeric $aset_harga_perolehan
 * @property numeric $aset_nilai_sisa
 * @property int $aset_masa_manfaat
 * @property string|null $aset_metode_penyusutan
 * @property \Carbon\CarbonImmutable|null $aset_tanggal_mulai_susut
 * @property string|null $aset_status
 * @property string|null $aset_kondisi
 * @property string|null $aset_foto
 * @property string|null $aset_kode_qr
 * @property numeric $aset_jam_pakai
 * @property array<array-key, mixed>|null $aset_custom_fields
 * @property string|null $aset_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $foto_url
 * @property-read string $qr_url
 * @property-read string $qr_value
 * @property-read string $scan_qr_url
 * @property-read string $tiket_qr_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AsetSukuCadang> $hasAsetSukuCadang
 * @property-read int|null $has_aset_suku_cadang_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DokumenAset> $hasDokumen
 * @property-read int|null $has_dokumen_count
 * @property-read \App\Models\KategoriAset|null $hasKategori
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LogStatusAset> $hasLogStatus
 * @property-read int|null $has_log_status_count
 * @property-read \App\Models\LokasiAset|null $hasLokasi
 * @property-read \App\Models\Peminjaman|null $hasPeminjamanAktif
 * @property-read \App\Models\User|null $hasPenanggungJawab
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SukuCadang> $hasSukuCadang
 * @property-read int|null $has_suku_cadang_count
 * @property-read \App\Models\Vendor|null $hasVendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetHargaPerolehan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetIdLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetIdPenanggungJawab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetIdVendor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetJamPakai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetKodeQr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetKondisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetMasaManfaat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetMerek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetMetodePenyusutan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetNilaiSisa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetNomorSeri($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetTanggalMulaiSusut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereAsetTanggalPerolehan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aset whereUpdatedAt($value)
 */
	class Aset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $aset_suku_cadang_id
 * @property int $aset_suku_cadang_id_aset
 * @property int $aset_suku_cadang_id_suku_cadang
 * @property numeric $aset_suku_cadang_jumlah
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\SukuCadang|null $hasSukuCadang
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang whereAsetSukuCadangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang whereAsetSukuCadangIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang whereAsetSukuCadangIdSukuCadang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang whereAsetSukuCadangJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsetSukuCadang whereUpdatedAt($value)
 */
	class AsetSukuCadang extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperBaseModel
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel sortFields(array|string $fields)
 */
	class BaseModel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $batch_tiket_id
 * @property string $batch_tiket_kode
 * @property int $batch_tiket_id_teknisi
 * @property \Carbon\CarbonImmutable $batch_tiket_tanggal
 * @property string|null $batch_tiket_zona
 * @property string $batch_tiket_mode
 * @property string $batch_tiket_status
 * @property string|null $batch_tiket_urutan
 * @property numeric|null $batch_tiket_total_eta
 * @property numeric|null $batch_tiket_total_jarak
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Teknisi|null $hasTeknisi
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketIdTeknisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketTotalEta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketTotalJarak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketUrutan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereBatchTiketZona($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BatchTiket whereUpdatedAt($value)
 */
	class BatchTiket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $buku_penyusutan_id
 * @property int|null $buku_penyusutan_id_aset
 * @property string|null $buku_penyusutan_periode
 * @property \Carbon\CarbonImmutable|null $buku_penyusutan_tanggal
 * @property numeric $buku_penyusutan_nilai
 * @property numeric $buku_penyusutan_akumulasi
 * @property numeric $buku_penyusutan_nilai_buku
 * @property int|null $buku_penyusutan_reversalisasi_dari
 * @property string|null $buku_penyusutan_hash
 * @property string|null $buku_penyusutan_hash_sebelum
 * @property int|null $buku_penyusutan_dibuat_oleh
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\User|null $hasDibuatOleh
 * @property-read BukuPenyusutan|null $hasReversalisasiDari
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanAkumulasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanDibuatOleh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanHashSebelum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanNilai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanNilaiBuku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanPeriode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanReversalisasiDari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereBukuPenyusutanTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BukuPenyusutan whereUpdatedAt($value)
 */
	class BukuPenyusutan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereUserAgent($value)
 */
	class ContactMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $daftar_tunggu_id
 * @property int|null $daftar_tunggu_id_aset
 * @property int|null $daftar_tunggu_id_peminjam
 * @property \Carbon\CarbonImmutable $daftar_tunggu_tanggal_mulai
 * @property int $daftar_tunggu_durasi
 * @property string $daftar_tunggu_status
 * @property int|null $daftar_tunggu_id_peminjaman
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\User|null $hasPeminjam
 * @property-read \App\Models\Peminjaman|null $hasPeminjaman
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereDaftarTungguDurasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereDaftarTungguId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereDaftarTungguIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereDaftarTungguIdPeminjam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereDaftarTungguIdPeminjaman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereDaftarTungguStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereDaftarTungguTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DaftarTunggu whereUpdatedAt($value)
 */
	class DaftarTunggu extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $department_id
 * @property string $department_kode
 * @property string $department_nama
 * @property numeric $department_budget
 * @property numeric $department_budget_terpakai
 * @property string $department_periode
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read float $pending
 * @property-read float $sisa
 * @property-read string $sisa_formatted
 * @property-read float $tersedia
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PermintaanSukuCadang> $hasPermintaan
 * @property-read int|null $has_permintaan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $hasUsers
 * @property-read int|null $has_users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDepartmentBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDepartmentBudgetTerpakai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDepartmentKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDepartmentNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDepartmentPeriode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $aset_dokumen_id
 * @property int|null $aset_dokumen_id_aset
 * @property string|null $aset_dokumen_jenis
 * @property string|null $aset_dokumen_nomor
 * @property string|null $aset_dokumen_file
 * @property \Carbon\CarbonImmutable|null $aset_dokumen_tanggal_terbit
 * @property \Carbon\CarbonImmutable|null $aset_dokumen_tanggal_expired
 * @property string|null $aset_dokumen_keterangan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $file_url
 * @property-read \App\Models\Aset|null $hasAset
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenTanggalExpired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereAsetDokumenTanggalTerbit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenAset whereUpdatedAt($value)
 */
	class DokumenAset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $faktur_id
 * @property string $faktur_nomor
 * @property int|null $faktur_id_pesanan
 * @property \Carbon\CarbonImmutable|null $faktur_tanggal
 * @property numeric $faktur_total
 * @property string|null $faktur_status
 * @property string|null $faktur_file
 * @property string|null $faktur_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read string $faktur_file_url
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\PesananPembelian|null $hasPesanan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturIdPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereFakturTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faktur whereUpdatedAt($value)
 */
	class Faktur extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $gudang_id
 * @property string $gudang_kode
 * @property string $gudang_nama
 * @property int|null $gudang_id_lokasi
 * @property string|null $gudang_alamat
 * @property string|null $gudang_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\LokasiAset|null $hasLokasi
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereGudangAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereGudangCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereGudangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereGudangIdLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereGudangKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereGudangNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gudang whereUpdatedAt($value)
 */
	class Gudang extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $jadwal_service_id
 * @property int $jadwal_service_id_aset
 * @property int|null $jadwal_service_id_template
 * @property \Carbon\CarbonImmutable|null $jadwal_service_tanggal_mulai
 * @property \Carbon\CarbonImmutable|null $jadwal_service_tanggal_jatuh_tempo
 * @property int|null $jadwal_service_interval_bulan
 * @property numeric|null $jadwal_service_interval_jam
 * @property numeric|null $jadwal_service_odometer_terakhir
 * @property numeric|null $jadwal_service_jam_terakhir
 * @property string|null $jadwal_service_status
 * @property \Carbon\CarbonImmutable|null $jadwal_service_tanggal_terakhir
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\TemplateService|null $hasTemplate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceIdTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceIntervalBulan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceIntervalJam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceJamTerakhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceOdometerTerakhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceTanggalJatuhTempo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereJadwalServiceTanggalTerakhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalService whereUpdatedAt($value)
 */
	class JadwalService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $aset_kategori_id
 * @property string $aset_kategori_nama
 * @property string $aset_kategori_kode
 * @property int $aset_kategori_masa_manfaat
 * @property string|null $aset_kategori_metode_penyusutan
 * @property string|null $aset_kategori_keterangan
 * @property string|null $aset_kategori_custom_fields
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KategoriTeknisi> $hasKategoriTeknisi
 * @property-read int|null $has_kategori_teknisi_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teknisi> $hasTeknisi
 * @property-read int|null $has_teknisi_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereAsetKategoriCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereAsetKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereAsetKategoriKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereAsetKategoriKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereAsetKategoriMasaManfaat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereAsetKategoriMetodePenyusutan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereAsetKategoriNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriAset whereUpdatedAt($value)
 */
	class KategoriAset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $kategori_id
 * @property int $teknisi_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\KategoriAset|null $hasKategori
 * @property-read \App\Models\Teknisi|null $hasTeknisi
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi whereKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi whereTeknisiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriTeknisi whereUpdatedAt($value)
 */
	class KategoriTeknisi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $kelompok_penyusutan_id
 * @property string $kelompok_penyusutan_kode
 * @property string $kelompok_penyusutan_nama
 * @property int $kelompok_penyusutan_masa_manfaat
 * @property string|null $kelompok_penyusutan_metode
 * @property numeric $kelompok_penyusutan_tarif
 * @property string|null $kelompok_penyusutan_keterangan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereKelompokPenyusutanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereKelompokPenyusutanKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereKelompokPenyusutanKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereKelompokPenyusutanMasaManfaat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereKelompokPenyusutanMetode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereKelompokPenyusutanNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereKelompokPenyusutanTarif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KelompokPenyusutan whereUpdatedAt($value)
 */
	class KelompokPenyusutan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $log_alert_id
 * @property int|null $log_alert_id_alert
 * @property string $log_alert_kanal
 * @property string $log_alert_tujuan
 * @property string $log_alert_status
 * @property bool $log_alert_dibuka
 * @property string|null $log_alert_pesan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereLogAlertDibuka($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereLogAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereLogAlertIdAlert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereLogAlertKanal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereLogAlertPesan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereLogAlertStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereLogAlertTujuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogAlert whereUpdatedAt($value)
 */
	class LogAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $log_status_aset_id
 * @property int|null $log_status_aset_id_aset
 * @property string|null $log_status_aset_status_dari
 * @property string|null $log_status_aset_status_ke
 * @property int|null $log_status_aset_actor
 * @property string|null $log_status_aset_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\User|null $hasActor
 * @property-read \App\Models\Aset|null $hasAset
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereLogStatusAsetActor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereLogStatusAsetCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereLogStatusAsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereLogStatusAsetIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereLogStatusAsetStatusDari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereLogStatusAsetStatusKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogStatusAset whereUpdatedAt($value)
 */
	class LogStatusAset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $aset_lokasi_id
 * @property string $aset_lokasi_nama
 * @property string $aset_lokasi_kode
 * @property string|null $aset_lokasi_alamat
 * @property string|null $aset_lokasi_zona
 * @property numeric|null $aset_lokasi_latitude
 * @property numeric|null $aset_lokasi_longitude
 * @property int|null $aset_lokasi_parent_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LokasiAset> $hasChild
 * @property-read int|null $has_child_count
 * @property-read LokasiAset|null $hasParent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereAsetLokasiZona($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LokasiAset whereUpdatedAt($value)
 */
	class LokasiAset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $location
 * @property array<array-key, mixed>|null $items
 * @property bool $is_active
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu withoutTrashed()
 */
	class Menu extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $icon
 * @property string $icon_color
 * @property string $title
 * @property string|null $body
 * @property string|null $url
 * @property string $type
 * @property bool $read
 * @property array<array-key, mixed>|null $meta
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\User|null $has_user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIconColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $opname_id
 * @property string $opname_nomor
 * @property int|null $opname_id_lokasi
 * @property \Carbon\CarbonImmutable $opname_tanggal
 * @property \Carbon\CarbonImmutable|null $opname_tanggal_mulai
 * @property \Carbon\CarbonImmutable|null $opname_tanggal_selesai
 * @property int|null $opname_id_petugas
 * @property string $opname_status
 * @property int $opname_total_sistem
 * @property int $opname_total_fisik
 * @property int $opname_total_selisih
 * @property string|null $opname_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read array $progress
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OpnameDetail> $hasDetailOpname
 * @property-read int|null $has_detail_opname_count
 * @property-read \App\Models\LokasiAset|null $hasLokasi
 * @property-read \App\Models\User|null $hasPetugas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameIdLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameIdPetugas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameTotalFisik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameTotalSelisih($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameTotalSistem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereUpdatedAt($value)
 */
	class Opname extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $opname_detail_id
 * @property int|null $opname_detail_id_opname
 * @property int|null $opname_detail_id_aset
 * @property string|null $opname_detail_status_sistem
 * @property string|null $opname_detail_status_fisik
 * @property string|null $opname_detail_kondisi
 * @property bool $opname_detail_ditemukan
 * @property \Carbon\CarbonImmutable|null $opname_detail_waktu_scan
 * @property int|null $opname_detail_id_petugas_scan
 * @property string|null $opname_detail_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\Opname|null $hasOpname
 * @property-read \App\Models\User|null $hasPetugasScan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailDitemukan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailIdOpname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailIdPetugasScan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailKondisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailStatusFisik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailStatusSistem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailWaktuScan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereUpdatedAt($value)
 */
	class OpnameDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $peminjaman_id
 * @property string $peminjaman_nomor
 * @property int|null $peminjaman_id_aset
 * @property int|null $peminjaman_id_peminjam
 * @property string|null $peminjaman_tujuan
 * @property \Carbon\CarbonImmutable $peminjaman_tanggal_pinjam
 * @property \Carbon\CarbonImmutable $peminjaman_jatuh_tempo
 * @property \Carbon\CarbonImmutable|null $peminjaman_tanggal_kembali
 * @property string $peminjaman_status
 * @property int $peminjaman_grace_jam
 * @property numeric $peminjaman_denda
 * @property string|null $peminjaman_kondisi_kembali
 * @property string|null $peminjaman_foto_kembali
 * @property int|null $peminjaman_id_approver
 * @property int $peminjaman_perpanjang_ke
 * @property string|null $peminjaman_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $peminjaman_foto_kembali_url
 * @property-read \App\Models\User|null $hasApprover
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\User|null $hasPeminjam
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanDenda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanFotoKembali($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanGraceJam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanIdApprover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanIdPeminjam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanJatuhTempo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanKondisiKembali($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanPerpanjangKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanTanggalKembali($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanTanggalPinjam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman wherePeminjamanTujuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereUpdatedAt($value)
 */
	class Peminjaman extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $penawaran_penjualan_id
 * @property int $penawaran_penjualan_id_penjualan
 * @property int|null $penawaran_penjualan_id_user
 * @property string $penawaran_penjualan_nama_pembeli
 * @property string|null $penawaran_penjualan_kontak
 * @property numeric $penawaran_penjualan_harga
 * @property \Carbon\CarbonImmutable|null $penawaran_penjualan_tanggal
 * @property \Carbon\CarbonImmutable|null $penawaran_penjualan_waktu
 * @property string|null $penawaran_penjualan_status
 * @property string|null $penawaran_penjualan_hasil
 * @property string|null $penawaran_penjualan_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\PenjualanAset|null $hasPenjualan
 * @property-read \App\Models\User|null $hasUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanHasil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanIdPenjualan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanNamaPembeli($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan wherePenawaranPenjualanWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenawaranPenjualan whereUpdatedAt($value)
 */
	class PenawaranPenjualan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $penerimaan_id
 * @property int|null $penerimaan_id_pesanan
 * @property string $penerimaan_nomor
 * @property \Carbon\CarbonImmutable|null $penerimaan_tanggal
 * @property string|null $penerimaan_foto
 * @property string $penerimaan_penerima
 * @property string|null $penerimaan_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $penerimaan_foto_url
 * @property-read \App\Models\PesananPembelian|null $hasPesanan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan wherePenerimaanCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan wherePenerimaanFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan wherePenerimaanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan wherePenerimaanIdPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan wherePenerimaanNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan wherePenerimaanPenerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan wherePenerimaanTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penerimaan whereUpdatedAt($value)
 */
	class Penerimaan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $penghapusan_id
 * @property string $penghapusan_nomor
 * @property int|null $penghapusan_id_aset
 * @property string $penghapusan_alasan
 * @property \Carbon\CarbonImmutable $penghapusan_tanggal_request
 * @property numeric $penghapusan_nilai_buku
 * @property numeric $penghapusan_nilai_sisa
 * @property string $penghapusan_status
 * @property string|null $penghapusan_triase
 * @property \Carbon\CarbonImmutable|null $penghapusan_tanggal_akhir_karantina
 * @property string|null $penghapusan_foto
 * @property string|null $penghapusan_berita_acara
 * @property numeric|null $penghapusan_gain_loss
 * @property string|null $penghapusan_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $penghapusan_berita_acara_url
 * @property-read string $penghapusan_foto_url
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PenghapusanKomponen> $hasKomponen
 * @property-read int|null $has_komponen_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanBeritaAcara($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanGainLoss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanNilaiBuku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanNilaiSisa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanTanggalAkhirKarantina($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanTanggalRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan wherePenghapusanTriase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penghapusan whereUpdatedAt($value)
 */
	class Penghapusan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $penghapusan_komponen_id
 * @property int|null $penghapusan_komponen_id_penghapusan
 * @property string $penghapusan_komponen_nama
 * @property numeric $penghapusan_komponen_jumlah
 * @property int|null $penghapusan_komponen_id_suku_cadang
 * @property string|null $penghapusan_komponen_kondisi
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Penghapusan|null $hasPenghapusan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen wherePenghapusanKomponenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen wherePenghapusanKomponenIdPenghapusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen wherePenghapusanKomponenIdSukuCadang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen wherePenghapusanKomponenJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen wherePenghapusanKomponenKondisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen wherePenghapusanKomponenNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanKomponen whereUpdatedAt($value)
 */
	class PenghapusanKomponen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $penjualan_aset_id
 * @property string $penjualan_aset_nomor
 * @property int $penjualan_aset_id_aset
 * @property string $penjualan_aset_alasan
 * @property numeric $penjualan_aset_nilai_buku
 * @property numeric|null $penjualan_aset_harga_appraisal
 * @property numeric|null $penjualan_aset_harga_jual
 * @property string|null $penjualan_aset_status
 * @property \Carbon\CarbonImmutable|null $penjualan_aset_tanggal_request
 * @property \Carbon\CarbonImmutable|null $penjualan_aset_tanggal_jual
 * @property \Carbon\CarbonImmutable|null $penjualan_aset_tanggal_serah_terima
 * @property string|null $penjualan_aset_penerima
 * @property string|null $penjualan_aset_kondisi
 * @property string|null $penjualan_aset_foto_serah_terima
 * @property numeric|null $penjualan_aset_gain_loss
 * @property string|null $penjualan_aset_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $penjualan_aset_foto_serah_terima_url
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PenawaranPenjualan> $hasPenawaran
 * @property-read int|null $has_penawaran_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetFotoSerahTerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetGainLoss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetHargaAppraisal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetHargaJual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetKondisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetNilaiBuku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetPenerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetTanggalJual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetTanggalRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset wherePenjualanAsetTanggalSerahTerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenjualanAset whereUpdatedAt($value)
 */
	class PenjualanAset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $pergerakan_stok_id
 * @property int|null $pergerakan_stok_id_suku_cadang
 * @property int|null $pergerakan_stok_id_gudang
 * @property string|null $pergerakan_stok_tipe
 * @property numeric $pergerakan_stok_jumlah
 * @property string|null $pergerakan_stok_referensi
 * @property string|null $pergerakan_stok_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Gudang|null $hasGudang
 * @property-read \App\Models\SukuCadang|null $hasSukuCadang
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok wherePergerakanStokCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok wherePergerakanStokId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok wherePergerakanStokIdGudang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok wherePergerakanStokIdSukuCadang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok wherePergerakanStokJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok wherePergerakanStokReferensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok wherePergerakanStokTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PergerakanStok whereUpdatedAt($value)
 */
	class PergerakanStok extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $permintaan_suku_cadang_id
 * @property string $permintaan_suku_cadang_nomor
 * @property int|null $permintaan_suku_cadang_id_tiket
 * @property int $permintaan_suku_cadang_id_suku_cadang
 * @property int $permintaan_suku_cadang_id_peminta
 * @property int|null $department_id
 * @property numeric $permintaan_suku_cadang_jumlah
 * @property numeric $permintaan_suku_cadang_harga
 * @property numeric $permintaan_suku_cadang_subtotal
 * @property string $permintaan_suku_cadang_status
 * @property \Carbon\CarbonImmutable|null $permintaan_suku_cadang_tanggal_permintaan
 * @property string|null $permintaan_suku_cadang_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Department|null $hasDepartment
 * @property-read \App\Models\User|null $hasPeminta
 * @property-read \App\Models\SukuCadang|null $hasSukuCadang
 * @property-read \App\Models\Tiket|null $hasTiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangIdPeminta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangIdSukuCadang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangIdTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang wherePermintaanSukuCadangTanggalPermintaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanSukuCadang whereUpdatedAt($value)
 */
	class PermintaanSukuCadang extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $perpindahan_id
 * @property string $perpindahan_nomor
 * @property int|null $perpindahan_id_aset
 * @property int|null $perpindahan_id_lokasi_asal
 * @property int|null $perpindahan_id_lokasi_tujuan
 * @property string|null $perpindahan_alasan
 * @property \Carbon\CarbonImmutable $perpindahan_tanggal_request
 * @property \Carbon\CarbonImmutable|null $perpindahan_tanggal_estimasi
 * @property \Carbon\CarbonImmutable|null $perpindahan_tanggal_kirim
 * @property \Carbon\CarbonImmutable|null $perpindahan_tanggal_terima
 * @property string $perpindahan_status
 * @property string|null $perpindahan_level_approve
 * @property string|null $perpindahan_foto_keluar
 * @property string|null $perpindahan_foto_terima
 * @property string|null $perpindahan_ttd_hash
 * @property numeric|null $perpindahan_latitude
 * @property numeric|null $perpindahan_longitude
 * @property string|null $perpindahan_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $perpindahan_foto_keluar_url
 * @property-read string $perpindahan_foto_terima_url
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\LokasiAset|null $hasLokasiAsal
 * @property-read \App\Models\LokasiAset|null $hasLokasiTujuan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanFotoKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanFotoTerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanIdLokasiAsal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanIdLokasiTujuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanLevelApprove($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanTanggalEstimasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanTanggalKirim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanTanggalRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanTanggalTerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan wherePerpindahanTtdHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perpindahan whereUpdatedAt($value)
 */
	class Perpindahan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $persetujuan_id
 * @property string $persetujuan_modul
 * @property int|null $persetujuan_id_referensi
 * @property string $persetujuan_level
 * @property int|null $persetujuan_id_user
 * @property string $persetujuan_status
 * @property string|null $persetujuan_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\User|null $hasUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan wherePersetujuanCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan wherePersetujuanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan wherePersetujuanIdReferensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan wherePersetujuanIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan wherePersetujuanLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan wherePersetujuanModul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan wherePersetujuanStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persetujuan whereUpdatedAt($value)
 */
	class Persetujuan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $pesanan_item_id
 * @property int|null $pesanan_item_id_pesanan
 * @property string|null $pesanan_item_tipe
 * @property int|null $pesanan_item_id_referensi
 * @property string $pesanan_item_nama
 * @property numeric $pesanan_item_jumlah
 * @property numeric $pesanan_item_harga
 * @property numeric $pesanan_item_subtotal
 * @property numeric $pesanan_item_diterima
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\PesananPembelian|null $hasPesanan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemDiterima($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemIdPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemIdReferensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem wherePesananItemTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananItem whereUpdatedAt($value)
 */
	class PesananItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $pesanan_pembelian_id
 * @property string $pesanan_pembelian_nomor
 * @property int|null $pesanan_pembelian_id_vendor
 * @property \Carbon\CarbonImmutable|null $pesanan_pembelian_tanggal
 * @property \Carbon\CarbonImmutable|null $pesanan_pembelian_tanggal_kirim
 * @property string|null $pesanan_pembelian_tipe
 * @property string|null $pesanan_pembelian_status
 * @property numeric $pesanan_pembelian_total
 * @property string|null $pesanan_pembelian_kode_budget
 * @property string|null $pesanan_pembelian_level_approve
 * @property string|null $pesanan_pembelian_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PesananItem> $hasItem
 * @property-read int|null $has_item_count
 * @property-read \App\Models\Vendor|null $hasVendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianIdVendor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianKodeBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianLevelApprove($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianTanggalKirim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian wherePesananPembelianTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PesananPembelian whereUpdatedAt($value)
 */
	class PesananPembelian extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $reputasi_peminjam_id
 * @property int|null $reputasi_peminjam_id_user
 * @property numeric $reputasi_peminjam_skor
 * @property int $reputasi_peminjam_total_pinjam
 * @property int $reputasi_peminjam_terlambat
 * @property int $reputasi_peminjam_limit_pinjam
 * @property int $reputasi_peminjam_durasi_maks
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\User|null $hasUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereReputasiPeminjamDurasiMaks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereReputasiPeminjamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereReputasiPeminjamIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereReputasiPeminjamLimitPinjam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereReputasiPeminjamSkor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereReputasiPeminjamTerlambat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereReputasiPeminjamTotalPinjam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputasiPeminjam whereUpdatedAt($value)
 */
	class ReputasiPeminjam extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $riwayat_service_id
 * @property int $riwayat_service_id_aset
 * @property int|null $riwayat_service_id_tiket
 * @property int|null $riwayat_service_id_teknisi
 * @property \Carbon\CarbonImmutable|null $riwayat_service_tanggal
 * @property string $riwayat_service_jenis
 * @property numeric $riwayat_service_biaya
 * @property string|null $riwayat_service_catatan
 * @property array<array-key, mixed>|null $riwayat_service_checklist
 * @property string|null $riwayat_service_ttd
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $riwayat_service_ttd_url
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\Teknisi|null $hasTeknisi
 * @property-read \App\Models\Tiket|null $hasTiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceBiaya($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceChecklist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceIdTeknisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceIdTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereRiwayatServiceTtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatService whereUpdatedAt($value)
 */
	class RiwayatService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $stok_suku_cadang_id
 * @property int|null $stok_suku_cadang_id_suku_cadang
 * @property int|null $stok_suku_cadang_id_gudang
 * @property string|null $stok_suku_cadang_bin
 * @property numeric $stok_suku_cadang_jumlah
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Gudang|null $hasGudang
 * @property-read \App\Models\SukuCadang|null $hasSukuCadang
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang whereStokSukuCadangBin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang whereStokSukuCadangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang whereStokSukuCadangIdGudang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang whereStokSukuCadangIdSukuCadang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang whereStokSukuCadangJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokSukuCadang whereUpdatedAt($value)
 */
	class StokSukuCadang extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $suku_cadang_id
 * @property string $suku_cadang_kode
 * @property string $suku_cadang_nama
 * @property string $suku_cadang_tipe
 * @property string|null $suku_cadang_spesifikasi
 * @property int|null $suku_cadang_id_vendor
 * @property numeric $suku_cadang_harga
 * @property int|null $suku_cadang_id_gudang
 * @property numeric $suku_cadang_stok_minimum
 * @property numeric $suku_cadang_stok_maksimum
 * @property numeric $suku_cadang_bin_aktif
 * @property numeric $suku_cadang_bin_buffer
 * @property string|null $suku_cadang_satuan
 * @property array<array-key, mixed>|null $suku_cadang_kompatibilitas
 * @property string|null $suku_cadang_foto
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $suku_cadang_foto_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Aset> $hasAset
 * @property-read int|null $has_aset_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AsetSukuCadang> $hasAsetSukuCadang
 * @property-read int|null $has_aset_suku_cadang_count
 * @property-read \App\Models\Gudang|null $hasGudang
 * @property-read \App\Models\Vendor|null $hasVendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangBinAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangBinBuffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangIdGudang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangIdVendor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangKompatibilitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangSpesifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangStokMaksimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangStokMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereSukuCadangTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SukuCadang whereUpdatedAt($value)
 */
	class SukuCadang extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $teknisi_id
 * @property int|null $teknisi_id_user
 * @property string $teknisi_kode
 * @property string $teknisi_nama
 * @property string|null $teknisi_telepon
 * @property string|null $teknisi_telegram_id
 * @property string|null $teknisi_keahlian
 * @property string|null $teknisi_zona
 * @property string|null $teknisi_sertifikasi
 * @property numeric $teknisi_rating
 * @property int $teknisi_total_tiket
 * @property int $teknisi_total_revisi
 * @property numeric|null $teknisi_latitude
 * @property numeric|null $teknisi_longitude
 * @property \Carbon\CarbonImmutable|null $teknisi_waktu_posisi
 * @property string $teknisi_status
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KategoriAset> $hasKategori
 * @property-read int|null $has_kategori_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KategoriTeknisi> $hasKategoriTeknisi
 * @property-read int|null $has_kategori_teknisi_count
 * @property-read \App\Models\User|null $hasUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiKeahlian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiSertifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiTotalRevisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiTotalTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiWaktuPosisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereTeknisiZona($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teknisi whereUpdatedAt($value)
 */
	class Teknisi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $template_service_id
 * @property string $template_service_kode
 * @property string $template_service_nama
 * @property int|null $template_service_id_kategori
 * @property int|null $template_service_interval_bulan
 * @property numeric|null $template_service_interval_jam
 * @property numeric|null $template_service_estimasi_jam
 * @property string|null $template_service_keterangan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TemplateServiceItem> $hasItemTemplate
 * @property-read int|null $has_item_template_count
 * @property-read \App\Models\KategoriAset|null $hasKategori
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceEstimasiJam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceIntervalBulan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceIntervalJam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereTemplateServiceNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateService whereUpdatedAt($value)
 */
	class TemplateService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $template_service_item_id
 * @property int $template_service_item_id_template
 * @property string $template_service_item_nama
 * @property string|null $template_service_item_tipe
 * @property int|null $template_service_item_id_suku_cadang
 * @property numeric $template_service_item_jumlah
 * @property int $template_service_item_urutan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\TemplateService|null $hasTemplate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereTemplateServiceItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereTemplateServiceItemIdSukuCadang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereTemplateServiceItemIdTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereTemplateServiceItemJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereTemplateServiceItemNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereTemplateServiceItemTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereTemplateServiceItemUrutan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateServiceItem whereUpdatedAt($value)
 */
	class TemplateServiceItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $tiket_id
 * @property string $tiket_nomor
 * @property int $tiket_id_aset
 * @property int $tiket_id_pelapor
 * @property int|null $tiket_id_teknisi
 * @property string $tiket_judul
 * @property string|null $tiket_deskripsi
 * @property string $tiket_tingkat_urgensi
 * @property string $tiket_status
 * @property int|null $tiket_id_lokasi
 * @property numeric|null $tiket_latitude
 * @property numeric|null $tiket_longitude
 * @property string|null $tiket_foto_sebelum
 * @property string|null $tiket_foto_sesudah
 * @property \Carbon\CarbonImmutable $tiket_tanggal_lapor
 * @property \Carbon\CarbonImmutable|null $tiket_tanggal_tugas
 * @property \Carbon\CarbonImmutable|null $tiket_tanggal_mulai
 * @property \Carbon\CarbonImmutable|null $tiket_tanggal_selesai
 * @property \Carbon\CarbonImmutable|null $tiket_tanggal_verifikasi
 * @property \Carbon\CarbonImmutable|null $tiket_jatuh_tempo
 * @property bool $tiket_terlambat_sla
 * @property int $tiket_level_eskalasi
 * @property int|null $tiket_id_batch
 * @property numeric $tiket_biaya
 * @property numeric|null $tiket_rating
 * @property string|null $tiket_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read string $tiket_foto_sebelum_url
 * @property-read string $tiket_foto_sesudah_url
 * @property-read \App\Models\Aset|null $hasAset
 * @property-read \App\Models\BatchTiket|null $hasBatch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TiketLog> $hasLogTiket
 * @property-read int|null $has_log_tiket_count
 * @property-read \App\Models\LokasiAset|null $hasLokasi
 * @property-read \App\Models\User|null $hasPelapor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TiketSukuCadang> $hasSukuCadangTerpakai
 * @property-read int|null $has_suku_cadang_terpakai_count
 * @property-read \App\Models\Teknisi|null $hasTeknisi
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketBiaya($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketFotoSebelum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketFotoSesudah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketIdAset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketIdBatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketIdLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketIdPelapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketIdTeknisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketJatuhTempo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketLevelEskalasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketTanggalLapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketTanggalTugas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketTanggalVerifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketTerlambatSla($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereTiketTingkatUrgensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tiket whereUpdatedAt($value)
 */
	class Tiket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $tiket_log_id
 * @property int $tiket_log_id_tiket
 * @property string|null $tiket_log_status_dari
 * @property string $tiket_log_status_ke
 * @property int|null $tiket_log_actor
 * @property string|null $tiket_log_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Tiket|null $hasTiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereTiketLogActor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereTiketLogCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereTiketLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereTiketLogIdTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereTiketLogStatusDari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereTiketLogStatusKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereUpdatedAt($value)
 */
	class TiketLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $tiket_suku_cadang_id
 * @property int $tiket_suku_cadang_id_tiket
 * @property int $tiket_suku_cadang_id_suku_cadang
 * @property numeric $tiket_suku_cadang_jumlah
 * @property numeric $tiket_suku_cadang_harga
 * @property numeric $tiket_suku_cadang_subtotal
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\SukuCadang|null $hasSukuCadang
 * @property-read \App\Models\Tiket|null $hasTiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereTiketSukuCadangHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereTiketSukuCadangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereTiketSukuCadangIdSukuCadang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereTiketSukuCadangIdTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereTiketSukuCadangJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereTiketSukuCadangSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketSukuCadang whereUpdatedAt($value)
 */
	class TiketSukuCadang extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUser
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $telegram_id
 * @property string|null $telegram_username
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property string $role
 * @property int|null $department_id
 * @property string|null $avatar
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read string $avatar_url
 * @property-read mixed $field_email
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Department|null $hasDepartment
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerifiedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $vendor_id
 * @property string $vendor_kode
 * @property string $vendor_nama
 * @property string|null $vendor_telepon
 * @property string|null $vendor_email
 * @property string|null $vendor_alamat
 * @property string|null $vendor_kategori
 * @property numeric $vendor_rating
 * @property string|null $vendor_catatan
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorTelepon($value)
 */
	class Vendor extends \Eloquent {}
}

