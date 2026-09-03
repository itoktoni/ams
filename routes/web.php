<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LelangController;
use App\Http\Controllers\WebsiteSettingController;
use App\Models\Notification;
use App\Services\CentrifugoService;
use Buki\AutoRoute\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::middleware('auth')->post('/centrifugo/token', function (Request $request) {
    if (! config('langkahkecil.notification_enable')) {
        return response()->json(['token' => 'disabled']);
    }

    $centrifugo = app(CentrifugoService::class);
    $user = Auth::user();

    if ($request->input('channel')) {
        return response()->json([
            'token' => $centrifugo->generateSubscriptionToken((string) $user->id, $request->input('channel')),
        ]);
    }

    return response()->json([
        'token' => $centrifugo->generateConnectionToken((string) $user->id),
    ]);
});

// Public Lelang — dapat diakses tanpa login, bid butuh auth
Route::get('/lelang', [LelangController::class, 'index'])->name('lelang.index');
Route::get('/lelang/{id}', [LelangController::class, 'show'])->name('lelang.show');
Route::post('/lelang/{id}/bid', [LelangController::class, 'bid'])->middleware(['auth','throttle:10,1'])->name('lelang.bid');

Route::middleware(['auth', 'verified', 'access'])->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::auto('/user', 'UsersController', ['name' => 'user']);

    // ===== AMS — KIRO Asset Management =====
    // Master Data Aset
    Route::auto('/kategori-aset', 'KategoriAsetController', ['name' => 'kategori-aset']);
    Route::auto('/lokasi-aset', 'LokasiAsetController', ['name' => 'lokasi-aset']);
    Route::auto('/aset', 'AsetController', ['name' => 'aset']);
    Route::auto('/dokumen-aset', 'DokumenAsetController', ['name' => 'dokumen-aset']);
    Route::auto('/kelompok-penyusutan', 'KelompokPenyusutanController', ['name' => 'kelompok-penyusutan']);
    Route::auto('/buku-penyusutan', 'BukuPenyusutanController', ['name' => 'buku-penyusutan']);
    // Pemeliharaan
    Route::auto('/teknisi', 'TeknisiController', ['name' => 'teknisi']);
    Route::auto('/tiket', 'TiketController', ['name' => 'tiket']);
    Route::auto('/batch-tiket', 'BatchTiketController', ['name' => 'batch-tiket']);
    // Alert & Notifikasi
    Route::auto('/alert', 'AlertController', ['name' => 'alert']);
    // Peminjaman
    Route::auto('/peminjaman', 'PeminjamanController', ['name' => 'peminjaman']);
    Route::auto('/daftar-tunggu', 'DaftarTungguController', ['name' => 'daftar-tunggu']);
    Route::auto('/reputasi-peminjam', 'ReputasiPeminjamController', ['name' => 'reputasi-peminjam']);
    // Pergerakan & Penghapusan
    Route::auto('/perpindahan', 'PerpindahanController', ['name' => 'perpindahan']);
    Route::auto('/opname', 'OpnameController', ['name' => 'opname']);
    Route::auto('/penghapusan', 'PenghapusanController', ['name' => 'penghapusan']);
    Route::auto('/persetujuan', 'PersetujuanController', ['name' => 'persetujuan']);
    // Pengadaan
    Route::auto('/vendor', 'VendorController', ['name' => 'vendor']);
    Route::auto('/pesanan-pembelian', 'PesananPembelianController', ['name' => 'pesanan-pembelian']);
    Route::auto('/penerimaan', 'PenerimaanController', ['name' => 'penerimaan']);
    Route::auto('/faktur', 'FakturController', ['name' => 'faktur']);
    // Suku Cadang
    Route::auto('/gudang', 'GudangController', ['name' => 'gudang']);
    Route::auto('/suku-cadang', 'SukuCadangController', ['name' => 'suku-cadang']);
    Route::auto('/stok-suku-cadang', 'StokSukuCadangController', ['name' => 'stok-suku-cadang']);
    Route::auto('/pergerakan-stok', 'PergerakanStokController', ['name' => 'pergerakan-stok']);
    // Service
    Route::auto('/template-service', 'TemplateServiceController', ['name' => 'template-service']);
    Route::auto('/jadwal-service', 'JadwalServiceController', ['name' => 'jadwal-service']);
    Route::auto('/riwayat-service', 'RiwayatServiceController', ['name' => 'riwayat-service']);
    // Penjualan
    Route::auto('/penjualan-aset', 'PenjualanAsetController', ['name' => 'penjualan-aset']);

    Route::get('/native-bridge-test', function () {
        return view('pages.settings.native-bridge-test');
    })->name('native-bridge-test');

    Route::get('/settings/website', [WebsiteSettingController::class, 'index'])->name('settings.website');
    Route::post('/settings/website', [WebsiteSettingController::class, 'save'])->name('settings.website.save');

    Route::prefix('notifications-web')->group(function () {
        Route::get('/', function (Request $request) {
            $notifications = Notification::where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->limit($request->input('limit', 50))
                ->get();

            $unreadCount = Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications->map(fn ($n) => [
                    'id' => $n->id,
                    'icon' => $n->icon,
                    'iconColor' => $n->icon_color,
                    'title' => $n->title,
                    'body' => $n->body,
                    'url' => $n->url,
                    'type' => $n->type,
                    'read' => $n->read,
                    'time' => $n->created_at?->diffForHumans() ?? '',
                    'created_at' => $n->created_at->toIso8601String(),
                ]),
                'unread_count' => $unreadCount,
            ]);
        });

        Route::put('/{id}/read', function (int $id) {
            $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
            $notification->update(['read' => true]);

            return response()->json(['message' => 'Marked as read']);
        });

        Route::put('/read-all', function () {
            Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->update(['read' => true]);

            return response()->json(['message' => 'All marked as read']);
        });
    });
});

require __DIR__.'/settings.php';
