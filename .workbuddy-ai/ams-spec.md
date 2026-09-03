# SPECIFIKASI SISTEM AMS (KIRO ASSET MANAGEMENT)

Semua nama tabel, kolom, label, dan enum **menggunakan Bahasa Indonesia**.
Ikuti konvensi `module_field` dari AGENTS.md: setiap kolom diawali nama tabel singular,
mis. tabel `aset_kategori` → kolom `aset_kategori_id`, `aset_kategori_nama`, `aset_kategori_kode`.

## KONVENSI WAJIB (salin dari app yang sudah ada)

### Model
- Namespace `App\Models`, extend `App\Models\BaseModel`.
- Trait: `use DefaultEntity, Filterable, OptionTrait, Sortable;` (dan `HasUserstamps` bila relevan).
- `$primaryKey = '{tabel}_id';` (bigIncrements), `$keyType='int'`, `$incrementing=true`.
- `$table = '{tabel}';`
- `$fillable` = semua kolom writable.
- Tentukan `public static $filterColumns = [...]` dan `public static $sortColumns = [...]`
  (berisi kolom yang mau tampil di tabel & bisa difilter/di-sort).
- `public static function field_name(): string { return '{tabel_nama}'; }` (kolom nama tampilan).
- `public function rules(): array` berisi aturan validasi.
- `protected function casts(): array` untuk date/dateTime/decimal/json/boolean.
- Relasi: method **selalu diawali `has`**.
  - Ke parent: `public function hasKategori() { return $this->hasOne(KategoriAset::class, 'aset_kategori_id', 'aset_kategori_id'); }`
    (hasOne(Related, 'pk_related', 'fk_di_tabel_ini') — ikuti pola AGENTS.md persis).
  - Ke child (1..many): `public function hasItem() { return $this->hasMany(PesananItem::class, 'pesanan_pembelian_id', 'pesanan_pembelian_id'); }`
  - Akses di blade: `$model->has_kategori->field_name`.
- File field accessor (path): bila kolom simpan path file, tambah
  `public function get{X}UrlAttribute(): string { return fileUrl($this->x); }`

### Migration
- `Schema::create('{tabel}', function (Blueprint $table) { $table->id('{tabel}_id'); ... $table->timestamps(); });`
- `down()`: `Schema::dropIfExists('{tabel}');`
- Tipe shorthand: `string`, `text`, `integer`, `unsignedBigInteger`, `decimal('col',15,2)`,
  `date`, `dateTime`, `boolean->default(false)`, `json`, `foreignId`.
- Foreign key: `$table->unsignedBigInteger('aset_id_kategori')->nullable();`
  `$table->foreign('aset_id_kategori')->references('aset_kategori_id')->on('aset_kategori')->nullOnDelete();`
- Nama file migration: `database/migrations/2026_09_02_XXXXXX_create_{tabel}_table.php`

### Policy
- Namespace `App\Policies`, copy persis `app/Policies/BasePolicy.php` (ganti tidak perlu — pakai generic).
- Nama file `App/Policies/{Model}Policy.php`, class `{Model}Policy extends BasePolicy`.
- Laravel auto-detect `App\Models\{Model}` → `App\Policies\{Model}Policy`.

### Controller
- Namespace `App\Http\Controllers`, extend `App\Http\Controllers\Controller`, `use ControllerTrait;`
- `public function __construct({Model} $model) { $this->model = $model::getModel(); }`
- **JANGAN** buat method `public static function boot()` (auto-route akan menjadikannya route!).
- Bila butuh data tambahan ke view (mis. opsi select), override `share()` seperti `UsersController`.

### View (pages/{modul}/table.blade.php & form.blade.php)
- SALIN persis `resources/views/pages/users/table.blade.php` dan `form.blade.php`.
- Ganti:
  - di table: loop `$model::$sortColumns` untuk head & body (sudah otomatis). Untuk bagian mobile,
    tampilkan field penting dengan `{{ $table->{kolom} }}`.
  - di form: ganti isi `<x-card>` dengan `<x-input>`/`<x-select>`/`<x-file>`/`x-textarea`
    sesuai kolom. Untuk enum pakai `:options="App\Enums\{Domain}\{Enum}::getOptions()"`.
  - `<x-input name="{tabel_nama}">`, `<x-select name="{tabel_status}" :options="...">`.
- Layout tetap `<x-layouts::app>` + `<x-breadcrumb>` + `<x-form>` + `<x-action>`.

### Route & Menu (dikerjakan oleh orchestrator, BUKAN subagent)
- `Route::auto('/{modul}', '{Controller}::class', ['name' => '{modul}']);`
- Menu entry di `config/menu.php`.

---

## KAMUS DATA (kolom per tabel)

Shorthand: I=integer, S=string, T=text, D=date, DT=dateTime, B=boolean, DEC=decimal(15,2),
J=json, FK=unsignedBigInteger fk, ts=timestamps.

### 1) aset_kategori  (modul: kategori-aset, controller KategoriAsetController)
- aset_kategori_id (PK), aset_kategori_nama (S), aset_kategori_kode (S unique),
  aset_kategori_masa_manfaat (I, tahun),
  aset_kategori_metode_penyusutan (S, enum MetodePenyusutan),
  aset_kategori_keterangan (T nullable), ts

### 2) aset_lokasi  (modul: lokasi-aset, controller LokasiAsetController)
- aset_lokasi_id (PK), aset_lokasi_nama (S), aset_lokasi_kode (S), aset_lokasi_alamat (T nullable),
  aset_lokasi_zona (S nullable), aset_lokasi_latitude (DEC nullable), aset_lokasi_longitude (DEC nullable),
  aset_lokasi_parent_id (FK aset_lokasi nullable), ts

### 3) aset  (modul: aset, controller AsetController) — entitas utama
- aset_id (PK), aset_kode (S unique), aset_nama (S), aset_id_kategori (FK aset_kategori),
  aset_id_lokasi (FK aset_lokasi), aset_id_penanggung_jawab (FK users, nullable),
  aset_merek (S nullable), aset_model (S nullable), aset_nomor_seri (S nullable),
  aset_tanggal_perolehan (D), aset_harga_perolehan (DEC), aset_nilai_sisa (DEC default 0),
  aset_masa_manfaat (I, bulan), aset_metode_penyusutan (S enum MetodePenyusutan),
  aset_tanggal_mulai_susut (D), aset_status (S enum StatusAset), aset_kondisi (S enum KondisiAset),
  aset_foto (S nullable path), aset_kode_qr (S nullable), aset_km (DEC default 0),
  aset_jam_pakai (DEC default 0), aset_id_vendor (FK vendor nullable), aset_catatan (T nullable), ts

### 4) aset_dokumen  (modul: dokumen-aset, controller DokumenAsetController)
- aset_dokumen_id (PK), aset_dokumen_id_aset (FK aset), aset_dokumen_jenis (S enum JenisDokumen),
  aset_dokumen_nomor (S nullable), aset_dokumen_file (S nullable path),
  aset_dokumen_tanggal_terbit (D nullable), aset_dokumen_tanggal_expired (D nullable),
  aset_dokumen_keterangan (T nullable), ts

### 5) log_status_aset  (CHILD aset, hanya model+migration, relasi hasLogStatus)
- log_status_aset_id (PK), log_status_aset_id_aset (FK aset), log_status_aset_status_dari (S),
  log_status_aset_status_ke (S), log_status_aset_actor (FK users nullable),
  log_status_aset_catatan (T nullable), ts

### 6) kelompok_penyusutan  (modul: kelompok-penyusutan, controller KelompokPenyusutanController)
- kelompok_penyusutan_id (PK), kelompok_penyusutan_kode (S), kelompok_penyusutan_nama (S),
  kelompok_penyusutan_masa_manfaat (I, tahun), kelompok_penyusutan_metode (S enum MetodePenyusutan),
  kelompok_penyusutan_tarif (DEC), kelompok_penyusutan_keterangan (T nullable), ts

### 7) buku_penyusutan  (modul: buku-penyusutan, controller BukuPenyusutanController) — ledger hash-chain
- buku_penyusutan_id (PK), buku_penyusutan_id_aset (FK aset), buku_penyusutan_periode (S, format Y-m),
  buku_penyusutan_tanggal (DT), buku_penyusutan_debet (DEC default 0),
  buku_penyusutan_kredit (DEC default 0), buku_penyusutan_akumulasi (DEC default 0),
  buku_penyusutan_nilai_buku (DEC default 0), buku_penyusutan_tipe (S: periodik/reversalisasi/penyesuaian),
  buku_penyusutan_reversalisasi_dari (FK buku_penyusutan nullable),
  buku_penyusutan_hash (S), buku_penyusutan_hash_sebelum (S),
  buku_penyusutan_dibuat_oleh (FK users nullable), ts
  (Index unik: [buku_penyusutan_id_aset, buku_penyusutan_periode, buku_penyusutan_tipe])

### 8) teknisi  (modul: teknisi, controller TeknisiController)
- teknisi_id (PK), teknisi_id_user (FK users nullable), teknisi_kode (S), teknisi_nama (S),
  teknisi_telepon (S nullable), teknisi_keahlian (J, array JenisKeahlian),
  teknisi_zona (J, array string), teknisi_sertifikasi (J nullable),
  teknisi_rating (DEC default 0), teknisi_total_tiket (I default 0), teknisi_total_revisi (I default 0),
  teknisi_latitude (DEC nullable), teknisi_longitude (DEC nullable),
  teknisi_waktu_posisi (DT nullable), teknisi_status (S enum StatusTeknisi), ts

### 9) tiket  (modul: tiket, controller TiketController)
- tiket_id (PK), tiket_nomor (S unique), tiket_id_aset (FK aset), tiket_id_pelapor (FK users),
  tiket_id_teknisi (FK teknisi nullable), tiket_judul (S), tiket_deskripsi (T nullable),
  tiket_tingkat_urgensi (S enum TingkatUrgensi), tiket_status (S enum StatusTiket),
  tiket_id_lokasi (FK aset_lokasi nullable), tiket_latitude (DEC nullable), tiket_longitude (DEC nullable),
  tiket_foto_sebelum (S nullable path), tiket_foto_sesudah (S nullable path),
  tiket_tanggal_lapor (DT), tiket_tanggal_tugas (DT nullable), tiket_tanggal_mulai (DT nullable),
  tiket_tanggal_selesai (DT nullable), tiket_tanggal_verifikasi (DT nullable),
  tiket_jatuh_tempo (DT nullable, SLA), tiket_terlambat_sla (B default false),
  tiket_level_eskalasi (I default 0), tiket_id_batch (FK batch_tiket nullable),
  tiket_biaya (DEC default 0), tiket_rating (DEC nullable), tiket_catatan (T nullable), ts

### 10) tiket_log  (CHILD tiket, model+migration, relasi hasLogTiket)
- tiket_log_id (PK), tiket_log_id_tiket (FK tiket), tiket_log_status_dari (S),
  tiket_log_status_ke (S), tiket_log_actor (FK users nullable), tiket_log_catatan (T nullable), ts

### 11) tiket_suku_cadang  (CHILD tiket, model+migration, relasi hasSukuCadangTerpakai)
- tiket_suku_cadang_id (PK), tiket_suku_cadang_id_tiket (FK tiket),
  tiket_suku_cadang_id_suku_cadang (FK suku_cadang), tiket_suku_cadang_jumlah (DEC default 1),
  tiket_suku_cadang_harga (DEC default 0), tiket_suku_cadang_subtotal (DEC default 0), ts

### 12) batch_tiket  (modul: batch-tiket, controller BatchTiketController)
- batch_tiket_id (PK), batch_tiket_kode (S), batch_tiket_id_teknisi (FK teknisi),
  batch_tiket_tanggal (D), batch_tiket_zona (S nullable), batch_tiket_mode (S enum ModeBatch),
  batch_tiket_status (S enum StatusBatch), batch_tiket_urutan (J nullable, array id tiket),
  batch_tiket_total_eta (DEC nullable), batch_tiket_total_jarak (DEC nullable), ts

### 13) alert  (modul: alert, controller AlertController)
- alert_id (PK), alert_tipe (S enum TipeAlert), alert_id_referensi (I nullable),
  alert_tipe_referensi (S nullable), alert_judul (S), alert_pesan (T),
  alert_level (S enum LevelAlert), alert_kunci_dedup (S nullable), alert_jatuh_tempo (DT nullable),
  alert_id_pic (FK users nullable), alert_status (S enum StatusAlert),
  alert_level_eskalasi (I default 0), alert_terakhir_kirim (DT nullable), ts

### 14) log_alert  (CHILD alert, model+migration, relasi hasLogPengiriman)
- log_alert_id (PK), log_alert_id_alert (FK alert), log_alert_kanal (S enum KanalNotifikasi),
  log_alert_tujuan (S), log_alert_status (S), log_alert_dibuka (B default false),
  log_alert_pesan (T nullable), ts

### 15) peminjaman  (modul: peminjaman, controller PeminjamanController)
- peminjaman_id (PK), peminjaman_nomor (S unique), peminjaman_id_aset (FK aset),
  peminjaman_id_peminjam (FK users), peminjaman_tujuan (T nullable),
  peminjaman_tanggal_pinjam (DT), peminjaman_jatuh_tempo (DT), peminjaman_tanggal_kembali (DT nullable),
  peminjaman_status (S enum StatusPeminjaman), peminjaman_grace_jam (I default 4),
  peminjaman_denda (DEC default 0), peminjaman_kondisi_kembali (S enum KondisiAset nullable),
  peminjaman_foto_kembali (S nullable path), peminjaman_id_approver (FK users nullable),
  peminjaman_perpanjang_ke (I default 0), peminjaman_catatan (T nullable), ts

### 16) daftar_tunggu  (modul: daftar-tunggu, controller DaftarTungguController)
- daftar_tunggu_id (PK), daftar_tunggu_id_aset (FK aset), daftar_tunggu_id_peminjam (FK users),
  daftar_tunggu_tanggal_mulai (DT), daftar_tunggu_durasi (I), daftar_tunggu_status (S),
  daftar_tunggu_id_peminjaman (FK peminjaman nullable), ts

### 17) reputasi_peminjam  (modul: reputasi-peminjam, controller ReputasiPeminjamController)
- reputasi_peminjam_id (PK), reputasi_peminjam_id_user (FK users), reputasi_peminjam_skor (DEC default 100),
  reputasi_peminjam_total_pinjam (I default 0), reputasi_peminjam_terlambat (I default 0),
  reputasi_peminjam_limit_pinjam (I default 3), reputasi_peminjam_durasi_maks (I default 30), ts

### 18) perpindahan  (modul: perpindahan, controller PerpindahanController)
- perpindahan_id (PK), perpindahan_nomor (S unique), perpindahan_id_aset (FK aset),
  perpindahan_id_lokasi_asal (FK aset_lokasi), perpindahan_id_lokasi_tujuan (FK aset_lokasi),
  perpindahan_alasan (T nullable), perpindahan_tanggal_request (DT),
  perpindahan_tanggal_estimasi (D nullable), perpindahan_tanggal_kirim (DT nullable),
  perpindahan_tanggal_terima (DT nullable), perpindahan_status (S enum StatusPerpindahan),
  perpindahan_level_approve (S enum LevelPersetujuan nullable),
  perpindahan_foto_keluar (S nullable path), perpindahan_foto_terima (S nullable path),
  perpindahan_ttd_hash (S nullable), perpindahan_latitude (DEC nullable), perpindahan_longitude (DEC nullable),
  perpindahan_catatan (T nullable), ts

### 19) opname  (modul: opname, controller OpnameController)
- opname_id (PK), opname_nomor (S unique), opname_id_lokasi (FK aset_lokasi),
  opname_tanggal (D), opname_id_petugas (FK users), opname_status (S enum StatusOpname),
  opname_total_sistem (I default 0), opname_total_fisik (I default 0), opname_total_selisih (I default 0),
  opname_catatan (T nullable), ts

### 20) opname_detail  (CHILD opname, model+migration, relasi hasDetailOpname)
- opname_detail_id (PK), opname_detail_id_opname (FK opname), opname_detail_id_aset (FK aset),
  opname_detail_status_sistem (S nullable), opname_detail_status_fisik (S nullable),
  opname_detail_kondisi (S enum KondisiAset nullable), opname_detail_ditemukan (B default false),
  opname_detail_catatan (T nullable), ts

### 21) penghapusan  (modul: penghapusan, controller PenghapusanController)
- penghapusan_id (PK), penghapusan_nomor (S unique), penghapusan_id_aset (FK aset),
  penghapusan_alasan (T), penghapusan_tanggal_request (DT), penghapusan_nilai_buku (DEC default 0),
  penghapusan_nilai_sisa (DEC default 0), penghapusan_status (S enum StatusPenghapusan),
  penghapusan_triase (S enum TriasePenghapusan nullable),
  penghapusan_tanggal_akhir_karantina (D nullable), penghapusan_foto (S nullable path),
  penghapusan_berita_acara (S nullable path), penghapusan_gain_loss (DEC nullable),
  penghapusan_catatan (T nullable), ts

### 22) penghapusan_komponen  (CHILD penghapusan, model+migration, relasi hasKomponen)
- penghapusan_komponen_id (PK), penghapusan_komponen_id_penghapusan (FK penghapusan),
  penghapusan_komponen_nama (S), penghapusan_komponen_jumlah (DEC default 1),
  penghapusan_komponen_id_suku_cadang (FK suku_cadang nullable),
  penghapusan_komponen_kondisi (S enum KondisiAset nullable), ts

### 23) persetujuan  (modul: persetujuan, controller PersetujuanController) — generik multi-level
- persetujuan_id (PK), persetujuan_modul (S: perpindahan/penghapusan/pesanan_pembelian/penjualan_aset),
  persetujuan_id_referensi (I), persetujuan_level (S enum LevelPersetujuan),
  persetujuan_id_user (FK users), persetujuan_status (S enum StatusPersetujuan),
  persetujuan_catatan (T nullable), ts

### 24) vendor  (modul: vendor, controller VendorController)
- vendor_id (PK), vendor_kode (S), vendor_nama (S), vendor_telepon (S nullable),
  vendor_email (S nullable), vendor_alamat (T nullable), vendor_kategori (S nullable),
  vendor_rating (DEC default 0), vendor_catatan (T nullable), ts

### 25) pesanan_pembelian  (modul: pesanan-pembelian, controller PesananPembelianController)
- pesanan_pembelian_id (PK), pesanan_pembelian_nomor (S unique), pesanan_pembelian_id_vendor (FK vendor),
  pesanan_pembelian_tanggal (D), pesanan_pembelian_tanggal_kirim (D nullable),
  pesanan_pembelian_tipe (S enum TipePesanan), pesanan_pembelian_status (S enum StatusPesanan),
  pesanan_pembelian_total (DEC default 0), pesanan_pembelian_kode_budget (S nullable),
  pesanan_pembelian_level_approve (S enum LevelPersetujuan nullable), pesanan_pembelian_catatan (T nullable), ts

### 26) pesanan_item  (CHILD PO, model+migration, relasi hasItem)
- pesanan_item_id (PK), pesanan_item_id_pesanan (FK pesanan_pembelian),
  pesanan_item_tipe (S enum TipePesanan), pesanan_item_id_referensi (I nullable, suku_cadang_id/kategori),
  pesanan_item_nama (S), pesanan_item_jumlah (DEC default 1), pesanan_item_harga (DEC default 0),
  pesanan_item_subtotal (DEC default 0), pesanan_item_diterima (DEC default 0), ts

### 27) penerimaan  (modul: penerimaan, controller PenerimaanController)
- penerimaan_id (PK), penerimaan_id_pesanan (FK pesanan_pembelian), penerimaan_nomor (S),
  penerimaan_tanggal (D), penerimaan_foto (S nullable path), penerimaan_penerima (S),
  penerimaan_catatan (T nullable), ts

### 28) faktur  (modul: faktur, controller FakturController) — three-way matching
- faktur_id (PK), faktur_nomor (S), faktur_id_pesanan (FK pesanan_pembelian), faktur_tanggal (D),
  faktur_total (DEC default 0), faktur_status (S enum StatusFaktur), faktur_file (S nullable path),
  faktur_catatan (T nullable), ts

### 29) gudang  (modul: gudang, controller GudangController)
- gudang_id (PK), gudang_kode (S), gudang_nama (S), gudang_id_lokasi (FK aset_lokasi nullable),
  gudang_alamat (T nullable), gudang_catatan (T nullable), ts

### 30) suku_cadang  (modul: suku-cadang, controller SukuCadangController)
- suku_cadang_id (PK), suku_cadang_kode (S), suku_cadang_nama (S), suku_cadang_spesifikasi (T nullable),
  suku_cadang_id_vendor (FK vendor nullable), suku_cadang_harga (DEC default 0),
  suku_cadang_id_gudang (FK gudang nullable), suku_cadang_stok_minimum (DEC default 0),
  suku_cadang_stok_maksimum (DEC default 0), suku_cadang_bin_aktif (DEC default 0),
  suku_cadang_bin_buffer (DEC default 0), suku_cadang_satuan (S nullable),
  suku_cadang_kompatibilitas (J nullable, array id aset/kategori), suku_cadang_foto (S nullable path), ts

### 31) stok_suku_cadang  (modul: stok-suku-cadang, controller StokSukuCadangController)
- stok_suku_cadang_id (PK), stok_suku_cadang_id_suku_cadang (FK suku_cadang),
  stok_suku_cadang_id_gudang (FK gudang), stok_suku_cadang_bin (S: 1/2),
  stok_suku_cadang_jumlah (DEC default 0), ts (unique [suku_cadang, gudang, bin])

### 32) pergerakan_stok  (modul: pergerakan-stok, controller PergerakanStokController)
- pergerakan_stok_id (PK), pergerakan_stok_id_suku_cadang (FK suku_cadang),
  pergerakan_stok_id_gudang (FK gudang), pergerakan_stok_tipe (S enum TipePergerakanStok),
  pergerakan_stok_jumlah (DEC default 0), pergerakan_stok_referensi (S nullable),
  pergerakan_stok_catatan (T nullable), ts

### 33) template_service  (modul: template-service, controller TemplateServiceController)
- template_service_id (PK), template_service_kode (S), template_service_nama (S),
  template_service_id_kategori (FK aset_kategori nullable), template_service_interval_bulan (I nullable),
  template_service_interval_jam (DEC nullable), template_service_estimasi_jam (DEC nullable),
  template_service_keterangan (T nullable), ts

### 34) template_service_item  (CHILD template, model+migration, relasi hasItemTemplate)
- template_service_item_id (PK), template_service_item_id_template (FK template_service),
  template_service_item_nama (S), template_service_item_tipe (S: cek/ganti),
  template_service_item_id_suku_cadang (FK suku_cadang nullable), template_service_item_jumlah (DEC default 1),
  template_service_item_urutan (I default 0), ts

### 35) jadwal_service  (modul: jadwal-service, controller JadwalServiceController)
- jadwal_service_id (PK), jadwal_service_id_aset (FK aset), jadwal_service_id_template (FK template_service),
  jadwal_service_tanggal_mulai (D), jadwal_service_tanggal_jatuh_tempo (D),
  jadwal_service_interval_bulan (I nullable), jadwal_service_interval_jam (DEC nullable),
  jadwal_service_odometer_terakhir (DEC nullable), jadwal_service_jam_terakhir (DEC nullable),
  jadwal_service_status (S enum StatusService), jadwal_service_tanggal_terakhir (D nullable), ts

### 36) riwayat_service  (modul: riwayat-service, controller RiwayatServiceController)
- riwayat_service_id (PK), riwayat_service_id_aset (FK aset), riwayat_service_id_tiket (FK tiket nullable),
  riwayat_service_id_teknisi (FK teknisi nullable), riwayat_service_tanggal (DT),
  riwayat_service_jenis (S), riwayat_service_biaya (DEC default 0), riwayat_service_catatan (T nullable),
  riwayat_service_checklist (J nullable), riwayat_service_ttd (S nullable), ts

### 37) penjualan_aset  (modul: penjualan-aset, controller PenjualanAsetController)
- penjualan_aset_id (PK), penjualan_aset_nomor (S unique), penjualan_aset_id_aset (FK aset),
  penjualan_aset_alasan (T), penjualan_aset_nilai_buku (DEC default 0),
  penjualan_aset_harga_appraisal (DEC nullable), penjualan_aset_harga_jual (DEC nullable),
  penjualan_aset_status (S enum StatusPenjualan), penjualan_aset_tanggal_request (DT),
  penjualan_aset_tanggal_jual (D nullable), penjualan_aset_tanggal_serah_terima (D nullable),
  penjualan_aset_penerima (S nullable), penjualan_aset_kondisi (S enum KondisiAset nullable),
  penjualan_aset_foto_serah_terima (S nullable path), penjualan_aset_gain_loss (DEC nullable),
  penjualan_aset_catatan (T nullable), ts

### 38) penawaran_penjualan  (CHILD penjualan, model+migration, relasi hasPenawaran)
- penawaran_penjualan_id (PK), penawaran_penjualan_id_penjualan (FK penjualan_aset),
  penawaran_penjualan_nama_pembeli (S), penawaran_penjualan_kontak (S nullable),
  penawaran_penjualan_harga (DEC default 0), penawaran_penjualan_tanggal (D),
  penawaran_penjualan_status (S: diajukan/negosiasi/diterima/ditolak),
  penawaran_penjualan_hasil (T nullable), penawaran_penjualan_catatan (T nullable), ts

---

## ENUM (sudah dibuat orchestrator, namespace App\Enums\{Domain})
- Aset: StatusAset(aktif,dipinjam,maintenance,rusak,dihapus,afkir),
  KondisiAset(baru,baik,kurang_baik,rusak), MetodePenyusutan(garis_lurus,saldo_menurun,unit_produksi),
  JenisDokumen(bpkb,stnk,sim,faktur,garansi,sertifikat,lainnya)
- Tiket: StatusTiket(buka,ditugaskan,progres,menunggu_part,selesai,terverifikasi),
  TingkatUrgensi(kritis,tinggi,sedang,rendah), ModeBatch(geo,fifo),
  StatusBatch(draft,ditawarkan,diterima,ditolak,selesai), StatusTeknisi(tersedia,sibuk,offline),
  JenisKeahlian(elektrikal,hvac,it,mekanikal)
- Alert: TipeAlert(sim_stnk,langganan,service,peminjaman,sla), LevelAlert(info,peringatan,kritis),
  StatusAlert(terbuka,diakui,selesai,eskalasi), KanalNotifikasi(email,whatsapp,push,in_app)
- Peminjaman: StatusPeminjaman(diajukan,disetujui,aktif,terlambat,dikembalikan,dibatalkan,ditolak)
- Persetujuan: StatusPersetujuan(menunggu,disetujui,ditolak),
  LevelPersetujuan(supervisor,manager,admin_aset,keuangan,direksi)
- Perpindahan: StatusPerpindahan(diajukan,disetujui,transit,diterima,terverifikasi,ditolak)
- Penghapusan: StatusPenghapusan(draft,diajukan,setuju_manager,setuju_keuangan,setuju_direksi,karantina,dibuang,dibatalkan),
  TriasePenghapusan(perbaiki,kanibal,buang)
- PO: StatusPesanan(draft,disetujui,dikirim,sebagian,diterima,ditutup,dibatalkan),
  TipePesanan(aset,suku_cadang,jasa), StatusFaktur(cocok,belum_cocok,selisih)
- Stok: TipePergerakanStok(masuk,keluar,opname)
- Opname: StatusOpname(draft,proses,selesai)
- Service: StatusService(aktif,selesai,terlewat)
- Penjualan: StatusPenjualan(draft,diajukan,terverifikasi,disetujui,ditawarkan,negosiasi,disepakati,disetujui_harga,terjual,serah_terima,dibatalkan)

Gunakan di blade: `:options="App\Enums\Aset\StatusAset::getOptions()"` (butuh use EnumTrait).
