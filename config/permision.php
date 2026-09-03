<?php

/**
 * Permission restrict map — blacklist style (BasePolicy).
 * $restrict[role][module] = false  → deny all actions for that module
 * $restrict[role][module] = ['create'=>false, 'delete'=>false] → deny specific actions
 * $restrict[role][module] = ['create','delete'] → deny listed actions (legacy numeric array)
 * Empty / missing → allow all.
 *
 * Module key matches route name prefix: 'aset' matches 'aset.getTable', 'aset.getCreate', etc.
 * For CMS blueprint: 'cms-type', 'field', 'section', 'content', 'category', 'tag', 'menu'
 */

$restrict = [];

// ── DEVELOPER: semua menu keluar (no restrict)
$restrict['developer'] = [];

// ── ADMIN: contoh sesuai request — blueprint & settings dibatasi, WMS create dibatasi
// Hampir semua AMS boleh, kecuali blueprint (hanya developer)
$restrict['admin'] = [
    'cms-type' => false,
    'field' => false,
    'section' => false,
    'content' => false,
    'category' => false,
    'tag' => false,
    'menu' => false,
    'user' => false,
    'wms-product' => ['create' => false],
    'wms-pekerjaan' => false,
    'settings.company' => false,
    'settings.env' => false,
    // AMS tambahan: admin boleh semua AMS, jadi tidak di-restrict
];

// ── SUPERVISOR (approval): hanya boleh persetujuan + view lain, tidak boleh ubah blueprint/user
$restrict['supervisor'] = [
    'cms-type' => false,
    'field' => false,
    'section' => false,
    'content' => false,
    'category' => false,
    'tag' => false,
    'menu' => false,
    'user' => false,
    'settings.env' => false,
    // boleh: persetujuan, perpindahan, penghapusan, pesanan-pembelian (approval)
    // deny create/delete yang bukan approval → contoh batasi
    'kategori-aset' => ['create' => false, 'delete' => false],
    'lokasi-aset' => ['create' => false, 'delete' => false],
    'kelompok-penyusutan' => false,
    'buku-penyusutan' => false,
    'teknisi' => false,
    'vendor' => false,
    'department' => ['create' => false, 'delete' => false],
    'gudang' => false,
];

// ── TEKNISI: hanya pemeliharaan + lihat aset
$restrict['teknisi'] = [
    'cms-type' => false,
    'field' => false,
    'section' => false,
    'content' => false,
    'category' => false,
    'tag' => false,
    'menu' => false,
    'user' => false,
    'settings.company' => false,
    'settings.env' => false,
    'kategori-aset' => false,
    'lokasi-aset' => false,
    'kelompok-penyusutan' => false,
    'buku-penyusutan' => false,
    'vendor' => false,
    'pesanan-pembelian' => false,
    'penerimaan' => false,
    'faktur' => false,
    'gudang' => false,
    'department' => false,
    'stok-suku-cadang' => false,
    'pergerakan-stok' => false,
    'penjualan-aset' => false,
    'persetujuan' => false,
    'penghapusan' => false,
    'perpindahan' => false,
    'opname' => false,
    'peminjaman' => false,
    'daftar-tunggu' => false,
    'reputasi-peminjam' => false,
    // boleh: tiket, batch-tiket, teknisi (self), aset (view), alert, permintaan-suku-cadang
];

// ── PENGGUNA ASET (karyawan): pakai aset + pinjam + lihat dokumen — hanya lihat aset yang di-assign, tidak boleh edit/hapus/recalc
$restrict['pengguna_aset'] = [
    'cms-type' => false,
    'field' => false,
    'section' => false,
    'content' => false,
    'category' => false,
    'tag' => false,
    'menu' => false,
    'user' => false,
    'settings.company' => false,
    'settings.env' => false,
    'kategori-aset' => false,
    'lokasi-aset' => false,
    'kelompok-penyusutan' => false,
    'teknisi' => false,
    'department' => false,
    'batch-tiket' => false,
    'tiket' => ['delete' => false], // boleh lihat & buat tiket kerusakan untuk aset sendiri
    'alert' => false,
    'persetujuan' => false,
    'penghapusan' => false,
    'perpindahan' => false,
    'opname' => false,
    'vendor' => false,
    'pesanan-pembelian' => false,
    'penerimaan' => false,
    'faktur' => false,
    'gudang' => false,
    'suku-cadang' => false,
    'stok-suku-cadang' => false,
    'pergerakan-stok' => false,
    'template-service' => false,
    'jadwal-service' => false,
    'riwayat-service' => false,
    'penjualan-aset' => false,
    'permintaan-suku-cadang' => ['delete' => false],
    // aset & buku: hanya boleh table/show + beritaAcara, tidak boleh create/update/delete/recalc (detail buku tetap boleh lihat untuk aset sendiri)
    'aset' => ['create' => false, 'update' => false, 'delete' => false, 'recalc' => false],
    'buku-penyusutan' => ['create' => false, 'update' => false, 'delete' => false],
];

// ── CUSTOMER (lelang): hanya public + lelang, semua admin diblok (juga di-handle AccessMiddleware redirect)
$restrict['customer'] = [
    'dashboard' => false,
    'cms-type' => false,
    'field' => false,
    'section' => false,
    'content' => false,
    'category' => false,
    'tag' => false,
    'menu' => false,
    'user' => false,
    'wms-product' => false,
    'wms-pekerjaan' => false,
    'settings.company' => false,
    'settings.env' => false,
    'kategori-aset' => false,
    'lokasi-aset' => false,
    'aset' => false,
    'dokumen-aset' => false,
    'kelompok-penyusutan' => false,
    'buku-penyusutan' => false,
    'teknisi' => false,
    'tiket' => false,
    'batch-tiket' => false,
    'alert' => false,
    'peminjaman' => false,
    'daftar-tunggu' => false,
    'reputasi-peminjam' => false,
    'perpindahan' => false,
    'opname' => false,
    'penghapusan' => false,
    'persetujuan' => false,
    'vendor' => false,
    'pesanan-pembelian' => false,
    'penerimaan' => false,
    'faktur' => false,
    'gudang' => false,
    'department' => false,
    'suku-cadang' => false,
    'stok-suku-cadang' => false,
    'pergerakan-stok' => false,
    'template-service' => false,
    'jadwal-service' => false,
    'riwayat-service' => false,
    'penjualan-aset' => false,
    'permintaan-suku-cadang' => false,
    // lelang.* tidak di-restrict (public), biar lolos
];

// ── USER / EDITOR legacy (tetap)
$restrict['user'] = [
    'cms-type' => false,
    'field' => false,
    'section' => false,
    'user' => false,
    'settings.env' => false,
    'buku-penyusutan' => ['create' => false, 'update' => false, 'delete' => false],
];
$restrict['editor'] = [
    'cms-type' => false,
    'field' => false,
    'section' => false,
    'user' => false,
    'settings.env' => false,
];

return $restrict;
