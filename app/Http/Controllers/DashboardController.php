<?php

namespace App\Http\Controllers;

use App\Charts\DashboardChart;
use App\Models\Alert;
use App\Models\Aset;
use App\Models\DokumenAset;
use App\Models\JadwalService;
use App\Models\Notification;
use App\Models\Peminjaman;
use App\Models\StokSukuCadang;
use App\Models\Teknisi;
use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(DashboardChart $chart)
    {
        $stats = [
            'total_users' => User::count(),
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::where('read', false)->count(),
        ];

        $recentUsers = User::latest()->limit(5)->get();

        // ===== AMS statistics (guarded — works even before migrate) =====
        $amsStats = [
            'total_aset' => $this->safeCount(fn () => Aset::count()),
            'aset_aktif' => $this->safeCount(fn () => Aset::where('aset_status', 'aktif')->count()),
            'aset_maintenance' => $this->safeCount(fn () => Aset::where('aset_status', 'maintenance')->count()),
            'aset_rusak' => $this->safeCount(fn () => Aset::where('aset_status', 'rusak')->orWhere('aset_kondisi', 'rusak')->count()),
            'total_tiket' => $this->safeCount(fn () => Tiket::count()),
            'tiket_buka' => $this->safeCount(fn () => Tiket::where('tiket_status', 'buka')->count()),
            'tiket_progres' => $this->safeCount(fn () => Tiket::where('tiket_status', 'progres')->count()),
            'alert_terbuka' => $this->safeCount(fn () => Alert::where('alert_status', 'terbuka')->count()),
            'total_teknisi' => $this->safeCount(fn () => Teknisi::count()),
            'peminjaman_aktif' => $this->safeCount(fn () => Peminjaman::where('peminjaman_status', 'aktif')->count()),
            'peminjaman_terlambat' => $this->safeCount(fn () => Peminjaman::where('peminjaman_status', 'terlambat')->count()),
            'total_nilai' => $this->safeCount(fn () => (int) Aset::sum('aset_harga_perolehan'), 0),
            'dokumen_expired_soon' => $this->safeCount(fn () => DokumenAset::whereNotNull('aset_dokumen_tanggal_expired')->whereBetween('aset_dokumen_tanggal_expired', [Carbon::today(), Carbon::today()->addDays(30)])->count()),
            'service_due' => $this->safeCount(fn () => JadwalService::where('jadwal_service_status', 'aktif')->whereDate('jadwal_service_tanggal_jatuh_tempo', '<=', Carbon::today()->addDays(14))->count()),
            'stok_menipis' => $this->safeCount(fn () => DB::table('stok_suku_cadang')->join('suku_cadang','suku_cadang.suku_cadang_id','=','stok_suku_cadang.stok_suku_cadang_id_suku_cadang')->whereColumn('stok_suku_cadang_jumlah','<','suku_cadang.suku_cadang_stok_minimum')->count()),
        ];

        // Extra widgets
        $recentAset = $this->safeQuery(fn () => Aset::with(['hasKategori','hasLokasi'])->latest('aset_id')->limit(5)->get(), collect());
        $recentTiket = $this->safeQuery(fn () => Tiket::with(['hasAset'])->latest('tiket_id')->limit(5)->get(), collect());
        $recentAlerts = $this->safeQuery(fn () => Alert::latest('alert_id')->limit(4)->get(), collect());
        $opnameProgress = $this->safeQuery(fn () => DB::table('opname')->where('opname_status', 'proses')->count(), 0);

        // Kategori distribusi for bar
        $kategoriDist = $this->safeQuery(fn () => Aset::select('aset_kategori_nama', DB::raw('count(*) as total'))
            ->leftJoin('aset_kategori', 'aset_kategori.aset_kategori_id', '=', 'aset.aset_id_kategori')
            ->groupBy('aset_kategori_id','aset_kategori_nama')->orderByDesc('total')->limit(5)->get(), collect());

        // Expiring STNK/KIR from custom fields (JSON) — MySQL JSON_EXTRACT
        $expiringCustom = $this->safeQuery(function () {
            $today = Carbon::today()->format('Y-m-d');
            $limit = Carbon::today()->addDays(30)->format('Y-m-d');
            // Only MOB/MTR/KEND categories
            return Aset::whereIn('aset_id_kategori', function ($q) {
                $q->select('aset_kategori_id')->from('aset_kategori')->whereIn('aset_kategori_kode', ['MOB','MTR','KEND']);
            })->get()->filter(function ($a) use ($today,$limit) {
                $cf = $a->aset_custom_fields ?? [];
                $dates = [$cf['tanggal_expired_stnk'] ?? null, $cf['tanggal_expired_kir'] ?? null, $cf['tanggal_pajak'] ?? null];
                foreach ($dates as $d) {
                    if ($d && $d >= $today && $d <= $limit) return true;
                }
                return false;
            })->take(4);
        }, collect());

        $amsChartAset = $this->safeChart(fn () => $chart->asetStatusDistribution());
        $amsChartTiket = $this->safeChart(fn () => $chart->tiketStatusDistribution());

        return view('dashboard', compact('stats', 'recentUsers', 'amsStats', 'amsChartAset', 'amsChartTiket', 'recentAset', 'recentTiket', 'recentAlerts', 'kategoriDist', 'expiringCustom', 'opnameProgress'))
            ->with('userChart', $chart->userRegistrations())
            ->with('notifChart', $chart->notificationStats());
    }

    /**
     * Run a counting query; return 0 if the underlying table does not exist yet.
     */
    private function safeCount(callable $callback, int $default = 0): int
    {
        try {
            return (int) $callback();
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Build a chart; return null if the underlying table does not exist yet.
     */
    private function safeChart(callable $callback): ?object
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function safeQuery(callable $callback, mixed $default = null): mixed
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
