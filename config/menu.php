<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Menu Configuration
    |--------------------------------------------------------------------------
    |
    | Define menu items for desktop sidebar, mobile drawer, and bottom nav.
    | Each item: route (string), icon (string), label (string)
    | Sections: label (string), items (array)
    | Bottom nav: only 5 items max, uses short label
    |
    */

    'sidebar' => [
        [
            'label' => null,
            'items' => [
                ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Dashboard'],
            ],
        ],
        [
            'label' => 'Master Data Aset',
            'items' => [
                ['route' => 'kategori-aset.getTable', 'icon' => 'category', 'label' => 'Kategori Aset', 'match' => ['kategori-aset.*']],
                ['route' => 'lokasi-aset.getTable', 'icon' => 'pin_drop', 'label' => 'Lokasi Aset', 'match' => ['lokasi-aset.*']],
                ['route' => 'aset.getTable', 'icon' => 'precision_manufacturing', 'label' => 'Aset', 'match' => ['aset.*']],
                ['route' => 'dokumen-aset.getTable', 'icon' => 'folder', 'label' => 'Dokumen Aset', 'match' => ['dokumen-aset.*']],
                ['route' => 'kelompok-penyusutan.getTable', 'icon' => 'percent', 'label' => 'Kelompok Penyusutan', 'match' => ['kelompok-penyusutan.*']],
                ['route' => 'buku-penyusutan.getTable', 'icon' => 'book', 'label' => 'Buku Penyusutan', 'match' => ['buku-penyusutan.*']],
            ],
        ],
        [
            'label' => 'Pemeliharaan',
            'items' => [
                ['route' => 'teknisi.getTable', 'icon' => 'engineering', 'label' => 'Teknisi', 'match' => ['teknisi.*']],
                ['route' => 'tiket.getTable', 'icon' => 'confirmation_number', 'label' => 'Tiket', 'match' => ['tiket.*']],
                ['route' => 'batch-tiket.getTable', 'icon' => 'view_agenda', 'label' => 'Batch Tiket', 'match' => ['batch-tiket.*']],
            ],
        ],
        [
            'label' => 'Alert & Notifikasi',
            'items' => [
                ['route' => 'alert.getTable', 'icon' => 'warning', 'label' => 'Alert', 'match' => ['alert.*']],
            ],
        ],
        [
            'label' => 'Peminjaman',
            'items' => [
                ['route' => 'peminjaman.getTable', 'icon' => 'swap_horiz', 'label' => 'Peminjaman', 'match' => ['peminjaman.*']],
                ['route' => 'daftar-tunggu.getTable', 'icon' => 'hourglass_top', 'label' => 'Daftar Tunggu', 'match' => ['daftar-tunggu.*']],
                ['route' => 'reputasi-peminjam.getTable', 'icon' => 'star', 'label' => 'Reputasi Peminjam', 'match' => ['reputasi-peminjam.*']],
            ],
        ],
        [
            'label' => 'Pergerakan & Penghapusan',
            'items' => [
                ['route' => 'perpindahan.getTable', 'icon' => 'local_shipping', 'label' => 'Perpindahan', 'match' => ['perpindahan.*']],
                ['route' => 'opname.getTable', 'icon' => 'qr_code_scanner', 'label' => 'Opname', 'match' => ['opname.*']],
                ['route' => 'penghapusan.getTable', 'icon' => 'delete_forever', 'label' => 'Penghapusan', 'match' => ['penghapusan.*']],
                ['route' => 'persetujuan.getTable', 'icon' => 'how_to_reg', 'label' => 'Persetujuan', 'match' => ['persetujuan.*']],
            ],
        ],
        [
            'label' => 'Pengadaan',
            'items' => [
                ['route' => 'vendor.getTable', 'icon' => 'store', 'label' => 'Vendor', 'match' => ['vendor.*']],
                ['route' => 'pesanan-pembelian.getTable', 'icon' => 'receipt_long', 'label' => 'Pesanan Pembelian', 'match' => ['pesanan-pembelian.*']],
                ['route' => 'penerimaan.getTable', 'icon' => 'download_done', 'label' => 'Penerimaan', 'match' => ['penerimaan.*']],
                ['route' => 'faktur.getTable', 'icon' => 'request_quote', 'label' => 'Faktur', 'match' => ['faktur.*']],
            ],
        ],
        [
            'label' => 'Suku Cadang',
            'items' => [
                ['route' => 'gudang.getTable', 'icon' => 'warehouse', 'label' => 'Gudang', 'match' => ['gudang.*']],
                ['route' => 'suku-cadang.getTable', 'icon' => 'build', 'label' => 'Suku Cadang', 'match' => ['suku-cadang.*']],
                ['route' => 'stok-suku-cadang.getTable', 'icon' => 'layers', 'label' => 'Stok Suku Cadang', 'match' => ['stok-suku-cadang.*']],
                ['route' => 'pergerakan-stok.getTable', 'icon' => 'sync', 'label' => 'Pergerakan Stok', 'match' => ['pergerakan-stok.*']],
            ],
        ],
        [
            'label' => 'Service',
            'items' => [
                ['route' => 'template-service.getTable', 'icon' => 'playlist_add', 'label' => 'Template Service', 'match' => ['template-service.*']],
                ['route' => 'jadwal-service.getTable', 'icon' => 'event_repeat', 'label' => 'Jadwal Service', 'match' => ['jadwal-service.*']],
                ['route' => 'riwayat-service.getTable', 'icon' => 'history', 'label' => 'Riwayat Service', 'match' => ['riwayat-service.*']],
            ],
        ],
        [
            'label' => 'Penjualan',
            'items' => [
                ['route' => 'penjualan-aset.getTable', 'icon' => 'sell', 'label' => 'Penjualan Aset', 'match' => ['penjualan-aset.*']],
            ],
        ],
        [
            'label' => 'Settings',
            'items' => [
                ['route' => 'settings.website', 'icon' => 'language', 'label' => 'Website'],
                ['route' => 'settings.env', 'icon' => 'settings', 'label' => 'Environment'],
                ['route' => 'native-bridge-test', 'icon' => 'phone_android', 'label' => 'NativeBridge Test'],
            ],
        ],
    ],

    'bottom_nav' => [

        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Home'],
        ['route' => 'aset.getTable', 'icon' => 'precision_manufacturing', 'label' => 'Aset'],
        ['route' => 'tiket.getTable', 'icon' => 'confirmation_number', 'label' => 'Tiket'],
        ['route' => 'alert.getTable', 'icon' => 'warning', 'label' => 'Alert'],
        ['route' => 'peminjaman.getTable', 'icon' => 'swap_horiz', 'label' => 'Pinjam'],

    ],

];
