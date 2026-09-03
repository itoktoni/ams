<?php

namespace App\Http\Controllers;

use App\Charts\DashboardChart;
use App\Models\Alert;
use App\Models\Aset;
use App\Models\Department;
use App\Models\DokumenAset;
use App\Models\JadwalService;
use App\Models\Peminjaman;
use App\Models\PermintaanSukuCadang;
use App\Models\Teknisi;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(DashboardChart $chart)
    {
        $user = auth()->user();
        $role = $user->role ?? 'guest';
        $deptId = $user->department_id ?? null;

        $amsStats = $this->buildAmsStats($role, $user, $deptId);
        $extra = $this->buildExtra($role, $user, $deptId);

        // Hanya 2 chart yang masih dipakai view: distribusi status aset & tiket.
        // (Chart registrasi user & notifikasi dihapus karena nilai operasionalnya rendah.)
        $amsChartAset = in_array($role, ['developer', 'admin', 'supervisor'], true)
            ? $this->safeChart(fn () => $chart->asetStatusDistribution())
            : null;
        $amsChartTiket = in_array($role, ['developer', 'admin', 'supervisor', 'teknisi'], true)
            ? $this->safeChart(fn () => $chart->tiketStatusDistribution())
            : null;

        return view('dashboard', array_merge(compact('amsStats', 'amsChartAset', 'amsChartTiket'), $extra, ['role' => $role]));
    }

    private function buildAmsStats(string $role, $user, $deptId): array
    {
        $myAsetIds = null;
        if (in_array($role, ['pengguna_aset', 'user'], true)) {
            $myAsetIds = $this->safeQuery(fn () => Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all(), []);
        }

        $isFull = in_array($role, ['developer', 'admin', 'supervisor'], true);
        $isTeknisi = $role === 'teknisi';
        $isPengguna = in_array($role, ['pengguna_aset', 'user'], true);
        $isCustomer = $role === 'customer';

        if ($isCustomer) {
            return [
                'total_aset' => 0, 'aset_aktif' => 0, 'aset_maintenance' => 0, 'aset_rusak' => 0,
                'total_tiket' => 0, 'tiket_buka' => 0, 'tiket_progres' => 0, 'alert_terbuka' => 0,
                'total_teknisi' => 0, 'peminjaman_aktif' => 0, 'peminjaman_terlambat' => 0,
                'total_nilai' => 0, 'dokumen_expired_soon' => 0, 'service_due' => 0, 'stok_menipis' => 0,
                'permintaan_menunggu' => 0, 'budget_sisa' => 0,
            ];
        }

        $stats = [
            'total_aset' => $this->safeCount(fn () => $isPengguna ? Aset::whereIn('aset_id', $myAsetIds ?? [])->count() : Aset::count()),
            'aset_aktif' => $this->safeCount(fn () => $isPengguna ? Aset::whereIn('aset_id', $myAsetIds ?? [])->where('aset_status', 'aktif')->count() : Aset::where('aset_status', 'aktif')->count()),
            'aset_maintenance' => $this->safeCount(fn () => $isPengguna ? Aset::whereIn('aset_id', $myAsetIds ?? [])->where('aset_status', 'maintenance')->count() : Aset::where('aset_status', 'maintenance')->count()),
            'aset_rusak' => $this->safeCount(fn () => $isPengguna ? Aset::whereIn('aset_id', $myAsetIds ?? [])->where(function ($q) { $q->where('aset_status', 'rusak')->orWhere('aset_kondisi', 'rusak'); })->count() : Aset::where('aset_status', 'rusak')->orWhere('aset_kondisi', 'rusak')->count()),
            'total_tiket' => $this->safeCount(fn () => $this->tiketCount($role, $user, $myAsetIds)),
            'tiket_buka' => $this->safeCount(fn () => $this->tiketCount($role, $user, $myAsetIds, 'buka')),
            'tiket_progres' => $this->safeCount(fn () => $this->tiketCount($role, $user, $myAsetIds, 'progres')),
            'alert_terbuka' => $this->safeCount(fn () => Alert::where('alert_status', 'terbuka')->count()),
            'total_teknisi' => $this->safeCount(fn () => $isPengguna ? 0 : Teknisi::count()),
            'peminjaman_aktif' => $this->safeCount(fn () => Peminjaman::where('peminjaman_status', 'aktif')->count()),
            'peminjaman_terlambat' => $this->safeCount(fn () => Peminjaman::where('peminjaman_status', 'terlambat')->count()),
            'total_nilai' => $this->safeCount(fn () => (int) ($isPengguna ? Aset::whereIn('aset_id', $myAsetIds ?? [])->sum('aset_harga_perolehan') : Aset::sum('aset_harga_perolehan')), 0),
            'dokumen_expired_soon' => $this->safeCount(fn () => $isPengguna || $isTeknisi ? 0 : DokumenAset::whereNotNull('aset_dokumen_tanggal_expired')->whereBetween('aset_dokumen_tanggal_expired', [Carbon::today(), Carbon::today()->addDays(30)])->count()),
            'service_due' => $this->safeCount(fn () => $isPengguna ? 0 : JadwalService::where('jadwal_service_status', 'aktif')->whereDate('jadwal_service_tanggal_jatuh_tempo', '<=', Carbon::today()->addDays(14))->count()),
            'stok_menipis' => $this->safeCount(fn () => $isPengguna ? 0 : DB::table('stok_suku_cadang')->join('suku_cadang', 'suku_cadang.suku_cadang_id', '=', 'stok_suku_cadang.stok_suku_cadang_id_suku_cadang')->whereColumn('stok_suku_cadang_jumlah', '<', 'suku_cadang.suku_cadang_stok_minimum')->count()),
            'permintaan_menunggu' => $this->safeCount(function () use ($role, $deptId, $user) {
                $q = PermintaanSukuCadang::where('permintaan_suku_cadang_status', 'menunggu');
                if (in_array($role, ['pengguna_aset', 'user'], true) && $deptId) {
                    $q->where('department_id', $deptId);
                } elseif (in_array($role, ['pengguna_aset', 'user'], true)) {
                    $q->where('permintaan_suku_cadang_id_peminta', $user->id);
                }
                return $q->count();
            }),
            'budget_sisa' => $this->safeCount(function () use ($deptId) {
                if (! $deptId) return 0;
                $dept = Department::find($deptId);
                if (! $dept) return 0;
                $terpakai = PermintaanSukuCadang::terpakaiDepartment($deptId);
                return (int) ((float) $dept->department_budget - $terpakai);
            }, 0),
        ];

        return $stats;
    }

    private function tiketCount(string $role, $user, ?array $myAsetIds, ?string $status = null): int
    {
        $q = Tiket::query();
        if ($status) $q->where('tiket_status', $status);
        if (in_array($role, ['pengguna_aset', 'user'], true) && $myAsetIds !== null) {
            $q->where(function ($qq) use ($user, $myAsetIds) {
                $qq->where('tiket_id_pelapor', $user->id);
                if (! empty($myAsetIds)) $qq->orWhereIn('tiket_id_aset', $myAsetIds);
            });
        } elseif ($role === 'teknisi') {
            $tek = Teknisi::where('teknisi_id_user', $user->id)->orWhere('teknisi_nama', $user->name)->first();
            if ($tek) $q->where(function ($qq) use ($tek) { $qq->where('tiket_id_teknisi', $tek->teknisi_id)->orWhereNull('tiket_id_teknisi'); });
        }
        return $q->count();
    }

    private function buildExtra(string $role, $user, $deptId): array
    {
        $myAsetIds = null;
        if (in_array($role, ['pengguna_aset', 'user'], true)) {
            $myAsetIds = $this->safeQuery(fn () => Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all(), []);
        }

        $isPengguna = in_array($role, ['pengguna_aset', 'user'], true);
        $isFull = in_array($role, ['developer', 'admin', 'supervisor'], true);

        $recentAset = $this->safeQuery(function () use ($isPengguna, $myAsetIds) {
            $q = Aset::with(['hasKategori', 'hasLokasi']);
            if ($isPengguna) $q->whereIn('aset_id', $myAsetIds ?? []);
            return $q->latest('aset_id')->limit(5)->get();
        }, collect());

        $recentTiket = $this->safeQuery(function () use ($role, $user, $myAsetIds) {
            $q = Tiket::with(['hasAset']);
            if (in_array($role, ['pengguna_aset', 'user'], true)) {
                $q->where(function ($qq) use ($user, $myAsetIds) {
                    $qq->where('tiket_id_pelapor', $user->id);
                    if (! empty($myAsetIds)) $qq->orWhereIn('tiket_id_aset', $myAsetIds);
                });
            } elseif ($role === 'teknisi') {
                $tek = Teknisi::where('teknisi_id_user', $user->id)->orWhere('teknisi_nama', $user->name)->first();
                if ($tek) $q->where(function ($qq) use ($tek) { $qq->where('tiket_id_teknisi', $tek->teknisi_id)->orWhereNull('tiket_id_teknisi'); });
            }
            return $q->latest('tiket_id')->limit(5)->get();
        }, collect());

        $opnameProgress = $this->safeQuery(fn () => DB::table('opname')->where('opname_status', 'proses')->count(), 0);
        $kategoriDist = $this->safeQuery(function () use ($isPengguna, $myAsetIds) {
            $q = Aset::select('aset_kategori_nama', DB::raw('count(*) as total'))->leftJoin('aset_kategori', 'aset_kategori.aset_kategori_id', '=', 'aset.aset_id_kategori');
            if ($isPengguna) $q->whereIn('aset.aset_id', $myAsetIds ?? []);
            return $q->groupBy('aset_kategori_id', 'aset_kategori_nama')->orderByDesc('total')->limit(5)->get();
        }, collect());

        $expiringCustom = $this->safeQuery(function () use ($isPengguna, $myAsetIds) {
            $today = Carbon::today()->format('Y-m-d');
            $limit = Carbon::today()->addDays(30)->format('Y-m-d');
            $q = Aset::whereIn('aset_id_kategori', function ($qq) {
                $qq->select('aset_kategori_id')->from('aset_kategori')->whereIn('aset_kategori_kode', ['MOB', 'MTR', 'KEND']);
            });
            if ($isPengguna) $q->whereIn('aset_id', $myAsetIds ?? []);
            return $q->get()->filter(function ($a) use ($today, $limit) {
                $cf = $a->aset_custom_fields ?? [];
                $dates = [$cf['tanggal_expired_stnk'] ?? null, $cf['tanggal_expired_kir'] ?? null, $cf['tanggal_pajak'] ?? null];
                foreach ($dates as $d) { if ($d && $d >= $today && $d <= $limit) return true; }
                return false;
            })->take(4);
        }, collect());

        $permintaanRecent = $this->safeQuery(function () use ($role, $deptId, $user) {
            $q = PermintaanSukuCadang::with(['hasSukuCadang'])->latest('permintaan_suku_cadang_id')->limit(4);
            if (in_array($role, ['pengguna_aset', 'user'], true)) {
                if ($deptId) $q->where('department_id', $deptId);
                else $q->where('permintaan_suku_cadang_id_peminta', $user->id);
            }
            return $q->get();
        }, collect());

        $budgetInfo = null;
        if ($deptId) {
            $budgetInfo = $this->safeQuery(function () use ($deptId) {
                $dept = Department::find($deptId);
                if (! $dept) return null;
                $terpakai = PermintaanSukuCadang::terpakaiDepartment($deptId);
                $pending = PermintaanSukuCadang::pendingDepartment($deptId);
                return [
                    'department' => $dept,
                    'terpakai' => $terpakai,
                    'pending' => $pending,
                    'sisa' => (float) $dept->department_budget - $terpakai,
                    'tersedia' => (float) $dept->department_budget - $terpakai - $pending,
                ];
            }, null);
        }

        return compact('recentAset', 'recentTiket', 'kategoriDist', 'expiringCustom', 'opnameProgress', 'permintaanRecent', 'budgetInfo');
    }

    private function safeCount(callable $callback, int $default = 0): int
    {
        try { return (int) $callback(); } catch (\Throwable $e) { return $default; }
    }

    private function safeChart(callable $callback): ?object
    {
        try { return $callback(); } catch (\Throwable $e) { return null; }
    }

    private function safeQuery(callable $callback, mixed $default = null): mixed
    {
        try { return $callback(); } catch (\Throwable $e) { return $default; }
    }
}
