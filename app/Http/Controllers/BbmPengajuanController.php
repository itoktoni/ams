<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\DeleteAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Enums\Bbm\JenisKendaraanBbmEnum;
use App\Enums\Bbm\StatusPengajuanBbmEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\BbmPengajuan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BbmPengajuanController extends Controller
{
    use ControllerTrait;

    public function __construct(BbmPengajuan $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $user = auth()->user();

        $default = [
            'model' => $this->model,
            'statusOptions' => StatusPengajuanBbmEnum::getOptions(),
            'jenisKendaraanOptions' => JenisKendaraanBbmEnum::getOptions(),
            'kuotaInfo' => $user ? BbmPengajuan::ringkasanUser($user->id) : null,
            'kendaraanUser' => $user ? self::kendaraanUser($user) : null,
            'userOptions' => User::getOptions(),
            'kendaraanMap' => self::kendaraanMap(),
            'kuotaMap' => self::kuotaMap(),
            'kuotaMapAll' => self::kuotaMapAll(self::periodeOptions()),
            'periodeOptions' => self::periodeOptions(),
            'isAdminBbm' => $this->isAdmin(),
        ];

        return array_merge($default, $data);
    }

    /** Setiap user hanya punya 1 kendaraan — diambil dari data user, bukan dipilih di form. */
    public static function kendaraanUser(User $user): array
    {
        $jenis = $user->jenis_kendaraan;

        return [
            'jenis' => $jenis,
            'label' => $jenis ? JenisKendaraanBbmEnum::getDescription($jenis) : null,
            'nama' => $user->kendaraan,
        ];
    }

    /** Peta user => label kendaraan, dipakai admin saat mengganti peminta di form. */
    public static function kendaraanMap(): array
    {
        $map = [];
        User::query()->whereNotNull('jenis_kendaraan')->get(['id', 'jenis_kendaraan', 'kendaraan'])
            ->each(function (User $u) use (&$map) {
                $map[$u->id] = JenisKendaraanBbmEnum::getDescription($u->jenis_kendaraan)
                    .($u->kendaraan ? ' — '.$u->kendaraan : '');
            });

        return $map;
    }

    /** Daftar periode: Januari–Desember pada tahun terkait (key YYYY-MM, label nama bulan). */
    public static function periodeOptions(?int $year = null): array
    {
        $year ??= (int) now()->format('Y');
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $options = [];
        foreach ($nama as $bulan => $label) {
            $options[sprintf('%04d-%02d', $year, $bulan)] = $label.' '.$year;
        }

        return $options;
    }

    /** Peta user => periode => ringkasan kuota, dipakai widget form. */
    public static function kuotaMapAll(array $periodes): array
    {
        $map = [];
        $userIds = User::query()->whereNotNull('jenis_kendaraan')->pluck('id');
        foreach ($userIds as $id) {
            foreach ($periodes as $p => $label) {
                $periode = is_string($p) && preg_match('/^\d{4}-\d{2}$/', $p) ? $p : $label;
                $map[$id][$periode] = self::formatRingkasan(BbmPengajuan::ringkasanUser($id, $periode));
            }
        }

        return $map;
    }

    private static function formatRingkasan(array $r): array
    {
        return [
            'periode' => $r['periode'],
            'kuota' => formatQty($r['kuota']).' L',
            'sisa' => formatQty($r['sisa']).' L',
            'terpakai' => formatQty($r['terpakai']).' L',
            'menunggu' => formatQty($r['pending']).' L',
            'tersedia' => formatQty($r['tersedia']).' L',
            'pct' => $r['kuota'] > 0 ? min(100, (($r['terpakai'] + $r['pending']) / $r['kuota']) * 100) : 100,
            'over' => $r['tersedia'] < 0 || $r['kuota'] <= 0,
        ];
    }

    /** Peta user => ringkasan kuota, dipakai widget form saat peminta diganti. */
    public static function kuotaMap(?string $periode = null): array
    {
        $periode ??= now()->format('Y-m');
        $map = [];

        User::query()->whereNotNull('jenis_kendaraan')->pluck('id')
            ->each(function ($id) use (&$map, $periode) {
                $r = BbmPengajuan::ringkasanUser($id, $periode);
                $map[$id] = [
                    'periode' => $r['periode'],
                    'kuota' => formatQty($r['kuota']).' L',
                    'sisa' => formatQty($r['sisa']).' L',
                    'terpakai' => formatQty($r['terpakai']).' L',
                    'menunggu' => formatQty($r['pending']).' L',
                    'tersedia' => formatQty($r['tersedia']).' L',
                    'pct' => $r['kuota'] > 0 ? min(100, (($r['terpakai'] + $r['pending']) / $r['kuota']) * 100) : 100,
                    'over' => $r['tersedia'] < 0 || $r['kuota'] <= 0,
                ];
            });

        return $map;
    }

    /** Hanya admin/developer yang melihat semua pengajuan; role lain hanya milik sendiri. */
    protected function getData()
    {
        $query = $this->model->query()->with(['hasUser', 'hasApprover']);
        $user = auth()->user();

        if ($user && ! $this->isAdmin()) {
            $query->where('bbm_pengajuan_id_user', $user->id);
        }

        // Urutan bawaan: Januari–Desember (kalau user tidak memilih sort manual).
        if (! request()->input('sort.0')) {
            $query->orderBy('bbm_pengajuan_periode')->orderBy('bbm_pengajuan_tanggal');
        }

        return $query;
    }

    /** Admin bisa memilih peminta + periode saat buat pengajuan. */
    public function getCreate(GeneralRequest $request)
    {
        $user = auth()->user();

        return $this->views($this->template(), [
            'kuotaInfo' => $user ? BbmPengajuan::ringkasanUser($user->id) : null,
            'kuotaMap' => self::kuotaMap(),
            'kuotaMapAll' => self::kuotaMapAll(self::periodeOptions()),
        ]);
    }

    /** Detail & form edit tidak boleh membuka pengajuan orang lain. */
    public function getUpdate(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);
        $this->checkOwner($record);

        if ($record->bbm_pengajuan_status !== StatusPengajuanBbmEnum::MENUNGGU) {
            return $this->response($this->gagal('Pengajuan yang sudah '.$record->status_label.' tidak dapat diubah.'));
        }

        $tahun = (int) substr($record->bbm_pengajuan_periode, 0, 4);

        return $this->views($this->template(), [
            'model' => $record,
            'kuotaInfo' => BbmPengajuan::ringkasanUser($record->bbm_pengajuan_id_user, $record->bbm_pengajuan_periode),
            'kuotaMap' => self::kuotaMap($record->bbm_pengajuan_periode),
            'kuotaMapAll' => self::kuotaMapAll(self::periodeOptions($tahun)),
            'periodeOptions' => self::periodeOptions($tahun),
        ]);
    }

    public function getShow(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);
        $this->checkOwner($record);

        return $this->payload(TOAST_SUCCESS, $record);
    }

    private function checkOwner(BbmPengajuan $record): void
    {
        if (! $this->isAdmin() && (int) $record->bbm_pengajuan_id_user !== (int) Auth::id()) {
            abort(403);
        }
    }

    public function postCreate(GeneralRequest $request)
    {
        try {
            $this->handleUploads($request, ['bbm_pengajuan_foto_sebelum', 'bbm_pengajuan_foto_sesudah', 'bbm_pengajuan_foto_struk'], 'bbm');
        } catch (\InvalidArgumentException $e) {
            return $this->response($this->gagal('Upload foto gagal: '.$e->getMessage()));
        }

        $user = Auth::user();

        if (! $user) {
            return $this->response($this->gagal('Anda harus login untuk mengajukan BBM.'));
        }

        if (! $user->jenis_kendaraan) {
            return $this->response($this->gagal('Jenis kendaraan Anda belum di-set. Buka Settings → Profile untuk memilih Mobil/Motor, atau hubungi admin.'));
        }

        // Admin/developer boleh membuatkan pengajuan untuk user lain + periode pilihan.
        $targetId = $user->id;
        $periode = now()->format('Y-m');
        if ($this->isAdmin()) {
            $targetId = (int) $request->input('bbm_pengajuan_id_user', $user->id);
            $periode = $this->normalizePeriode($request->input('bbm_pengajuan_periode', $periode));
        }

        $target = User::find($targetId);
        if (! $target) {
            return $this->response($this->gagal('User peminta tidak ditemukan.'));
        }
        if (! $target->jenis_kendaraan) {
            return $this->response($this->gagal('User yang dipilih belum memiliki data kendaraan. Set dulu di halaman Users.'));
        }

        $liter = (float) $request->input('bbm_pengajuan_liter', 0);
        $info = BbmPengajuan::ringkasanUser($targetId, $periode);

        if ($info['kuota'] <= 0) {
            return $this->response($this->gagal('Kuota BBM periode '.$periode.' belum ditetapkan untuk '.$target->name.'. Tetapkan dulu di menu Kuota BBM.'));
        }

        if ($liter <= 0) {
            return $this->response($this->gagal('Jumlah BBM harus lebih dari 0 liter.'));
        }

        if ($liter > $info['tersedia']) {
            return $this->response($this->gagal($this->pesanKuota($info, $liter)));
        }

        $request->merge([
            'bbm_pengajuan_nomor' => 'BBM-'.now()->format('YmdHis').'-'.rand(100, 999),
            'bbm_pengajuan_id_user' => $targetId,
            'bbm_pengajuan_periode' => $periode,
            // Kendaraan selalu diambil dari data user (1 orang = 1 kendaraan).
            'bbm_pengajuan_jenis_kendaraan' => $target->jenis_kendaraan,
            'bbm_pengajuan_kendaraan' => $target->kendaraan ?: '', // nama/no. polisi opsional
            'bbm_pengajuan_status' => StatusPengajuanBbmEnum::MENUNGGU,
            'bbm_pengajuan_tanggal' => now(),
        ]);

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);
        try {
            $this->handleUploads($request, ['bbm_pengajuan_foto_sebelum', 'bbm_pengajuan_foto_sesudah', 'bbm_pengajuan_foto_struk'], 'bbm', $record->toArray());
        } catch (\InvalidArgumentException $e) {
            return $this->response($this->gagal('Upload foto gagal: '.$e->getMessage()));
        }
        $this->checkOwner($record);

        // Pengajuan yang sudah diproses (disetujui/ditolak) tidak bisa diubah siapa pun.
        if ($record->bbm_pengajuan_status !== StatusPengajuanBbmEnum::MENUNGGU) {
            return $this->response($this->gagal('Pengajuan yang sudah '.$record->status_label.' tidak dapat diubah.'));
        }

        $isAdmin = $this->isAdmin();
        $isApprover = $this->isApprover($record);

        // Admin/developer boleh mengganti peminta + periode; role lain terkunci.
        $userId = $isAdmin
            ? (int) $request->input('bbm_pengajuan_id_user', $record->bbm_pengajuan_id_user)
            : (int) $record->bbm_pengajuan_id_user;
        $periodeBaru = $isAdmin
            ? $this->normalizePeriode($request->input('bbm_pengajuan_periode', $record->bbm_pengajuan_periode))
            : $record->bbm_pengajuan_periode;

        $pemilik = $userId ? User::find($userId) : null;
        if (! $pemilik) {
            return $this->response($this->gagal('User peminta tidak ditemukan.'));
        }
        if (! $pemilik->jenis_kendaraan) {
            return $this->response($this->gagal('User yang dipilih belum memiliki data kendaraan. Set dulu di Settings → Profile.'));
        }

        // Kendaraan dan kuota mengikuti user yang berlaku (1 orang = 1 kendaraan).
        $request->merge([
            'bbm_pengajuan_id_user' => $userId,
            'bbm_pengajuan_periode' => $periodeBaru,
            'bbm_pengajuan_jenis_kendaraan' => $pemilik->jenis_kendaraan,
            'bbm_pengajuan_kendaraan' => $pemilik->kendaraan ?: '',
        ]);

        if (! $isAdmin) {
            // Field sistem tidak boleh diubah oleh peminta.
            $request->merge([
                'bbm_pengajuan_nomor' => $record->bbm_pengajuan_nomor,
                'bbm_pengajuan_periode' => $record->bbm_pengajuan_periode,
                'bbm_pengajuan_status' => $record->bbm_pengajuan_status,
                'bbm_pengajuan_tanggal' => $record->bbm_pengajuan_tanggal,
                'bbm_pengajuan_id_approver' => $record->bbm_pengajuan_id_approver,
            ]);
        }

        $liter = (float) $request->input('bbm_pengajuan_liter', $record->bbm_pengajuan_liter);
        $info = BbmPengajuan::ringkasanUser($userId, $periodeBaru, $record->bbm_pengajuan_id);

        if ($info['kuota'] > 0 && $liter > $info['tersedia']) {
            return $this->response($this->gagal($this->pesanKuota($info, $liter)));
        }

        if ($isApprover && $userId !== (int) Auth::id()
            && in_array($request->input('bbm_pengajuan_status'), [StatusPengajuanBbmEnum::DISETUJUI, StatusPengajuanBbmEnum::DITOLAK], true)) {
            $request->merge(['bbm_pengajuan_id_approver' => Auth::id()]);
        }

        return $this->response(UpdateAction::run($request, $id, $this->model));
    }

    /** Hapus pengajuan hanya admin/developer. */
    public function getDelete(GeneralRequest $request, $id)
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        return $this->response((new DeleteAction)->remove($id, $this->model));
    }

    public function postDelete(GeneralRequest $request)
    {
        if (! $this->isAdmin()) {
            abort(403);
        }

        return $this->response(DeleteAction::run($request, $this->model));
    }

    public function getApprove(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);

        if (! $this->isApprover($record)) {
            abort(403);
        }

        if ($record->bbm_pengajuan_status !== StatusPengajuanBbmEnum::MENUNGGU) {
            return $this->response($this->gagal('Hanya pengajuan berstatus menunggu yang bisa disetujui.'));
        }

        $record->update([
            'bbm_pengajuan_status' => StatusPengajuanBbmEnum::DISETUJUI,
            'bbm_pengajuan_id_approver' => Auth::id(),
        ]);

        return $this->response([
            'status' => true,
            'message' => 'Pengajuan BBM disetujui. Pemakaian kuota telah diperbarui.',
            'data' => $record->fresh(),
        ]);
    }

    public function getReject(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);

        if (! $this->isApprover($record)) {
            abort(403);
        }

        if ($record->bbm_pengajuan_status !== StatusPengajuanBbmEnum::MENUNGGU) {
            return $this->response($this->gagal('Hanya pengajuan berstatus menunggu yang bisa ditolak.'));
        }

        $record->update([
            'bbm_pengajuan_status' => StatusPengajuanBbmEnum::DITOLAK,
            'bbm_pengajuan_id_approver' => Auth::id(),
        ]);

        return $this->response([
            'status' => true,
            'message' => 'Pengajuan BBM ditolak.',
            'data' => $record->fresh(),
        ]);
    }

    /** Approve/reject hanya admin/developer, dan tidak boleh terhadap pengajuan sendiri. */
    private function isApprover(?BbmPengajuan $record = null): bool
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, ['developer', 'admin'], true)) {
            return false;
        }

        if ($record && (int) $record->bbm_pengajuan_id_user === (int) $user->id) {
            return false;
        }

        return true;
    }

    private function isAdmin(): bool
    {
        return in_array(auth()->user()?->role ?? '', ['developer', 'admin'], true);
    }

    private function pesanKuota(array $info, float $liter): string
    {
        return sprintf(
            'Kuota BBM periode %s tidak cukup. Kuota %s L, terpakai %s L, menunggu %s L, tersedia %s L. Anda mengajukan %s L.',
            $info['periode'],
            formatQty($info['kuota']),
            formatQty($info['terpakai']),
            formatQty($info['pending']),
            formatQty($info['tersedia']),
            formatQty($liter)
        );
    }

    protected function handleUploads(GeneralRequest $request, array $fields, string $folder, ?array $existing = null): void
    {
        foreach ($fields as $f) {
            if ($request->hasFile($f)) {
                $request->merge([$f => uploadFile($request->file($f), $folder, ['max_size' => 4096])]);
            } elseif ($request->boolean('remove_'.$f)) {
                $request->merge([$f => null]);
            } else {
                $request->merge([$f => $existing[$f] ?? null]);
            }
        }
    }

    private function normalizePeriode(mixed $value): string
    {
        if (is_string($value) && preg_match('/^\d{4}-\d{2}$/', $value)) {
            return $value;
        }

        return now()->format('Y-m');
    }

    private function gagal(string $message): array
    {
        return ['status' => false, 'message' => $message, 'data' => $message];
    }
}
