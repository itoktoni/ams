<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Concerns\ControllerTrait;
use App\Enums\SukuCadang\StatusPermintaanEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\Department;
use App\Models\PermintaanSukuCadang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermintaanSukuCadangController extends Controller
{
    use ControllerTrait;

    public function __construct(PermintaanSukuCadang $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $user = auth()->user();
        $dept = null;
        $budgetInfo = null;
        if ($user && $user->department_id) {
            $dept = Department::find($user->department_id);
            if ($dept) {
                $terpakai = (float) PermintaanSukuCadang::where('department_id', $dept->department_id)
                    ->whereNotIn('permintaan_suku_cadang_status', ['ditolak'])
                    ->sum('permintaan_suku_cadang_subtotal');
                $budgetInfo = [
                    'department' => $dept,
                    'terpakai' => $terpakai,
                    'sisa' => (float) $dept->department_budget - $terpakai,
                ];
            }
        }

        $default = [
            'model' => $this->model,
            'statusOptions' => StatusPermintaanEnum::getOptions(),
            'budgetInfo' => $budgetInfo,
        ];

        return array_merge($default, $data);
    }

    protected function getData()
    {
        $user = auth()->user();
        $query = $this->model->query()->with(['hasSukuCadang', 'hasPeminta', 'hasDepartment']);
        if ($user && in_array($user->role, ['pengguna_aset', 'user'], true)) {
            if ($user->department_id) {
                $deptUserIds = \App\Models\User::where('department_id', $user->department_id)->pluck('id')->all();
                $query->whereIn('permintaan_suku_cadang_id_peminta', $deptUserIds);
            } else {
                $query->where('permintaan_suku_cadang_id_peminta', $user->id);
            }
        }

        return $query;
    }

    public function postCreate(GeneralRequest $request)
    {
        $user = Auth::user();

        $deptId = $user?->department_id;
        if (! $deptId) {
            return $this->response(['status' => false, 'message' => 'Akun Anda belum di-assign ke department.', 'data' => null]);
        }

        $department = Department::find($deptId);
        if (! $department) {
            return $this->response(['status' => false, 'message' => 'Department tidak ditemukan.', 'data' => null]);
        }

        $sukuCadangId = $request->input('permintaan_suku_cadang_id_suku_cadang');
        $jumlah = (float) ($request->input('permintaan_suku_cadang_jumlah') ?? 1);
        $harga = 0;
        if ($sukuCadangId) {
            $harga = (float) (\App\Models\SukuCadang::where('suku_cadang_id', $sukuCadangId)->value('suku_cadang_harga') ?? 0);
        }
        $subtotal = $harga * $jumlah;

        $terpakai = (float) PermintaanSukuCadang::where('department_id', $deptId)
            ->whereNotIn('permintaan_suku_cadang_status', ['ditolak'])
            ->sum('permintaan_suku_cadang_subtotal');
        $sisa = (float) $department->department_budget - $terpakai;

        if ($subtotal > $sisa) {
            return $this->response([
                'status' => false,
                'message' => 'Budget department '.$department->department_nama.' tidak cukup. Sisa '.formatRupiah($sisa).', butuh '.formatRupiah($subtotal).'.',
                'data' => null,
            ]);
        }

        $request->merge([
            'permintaan_suku_cadang_nomor' => 'PS-'.now()->format('YmdHis').'-'.rand(100, 999),
            'permintaan_suku_cadang_id_peminta' => $user?->id,
            'department_id' => $deptId,
            'permintaan_suku_cadang_harga' => $harga,
            'permintaan_suku_cadang_subtotal' => $subtotal,
            'permintaan_suku_cadang_status' => 'menunggu',
            'permintaan_suku_cadang_tanggal_permintaan' => now(),
        ]);

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);
        $user = Auth::user();
        $isPengguna = $user && in_array($user->role, ['pengguna_aset', 'user'], true);
        if ($isPengguna) {
            $request->merge(['permintaan_suku_cadang_id_peminta' => $record->permintaan_suku_cadang_id_peminta]);
            $request->merge(['permintaan_suku_cadang_nomor' => $record->permintaan_suku_cadang_nomor]);
            $request->merge(['department_id' => $record->department_id]);
            $request->merge(['permintaan_suku_cadang_status' => $record->permintaan_suku_cadang_status]);
        }

        $sukuCadangId = $request->input('permintaan_suku_cadang_id_suku_cadang', $record->permintaan_suku_cadang_id_suku_cadang);
        $jumlah = (float) ($request->input('permintaan_suku_cadang_jumlah', $record->permintaan_suku_cadang_jumlah) ?? 1);
        $harga = (float) (\App\Models\SukuCadang::where('suku_cadang_id', $sukuCadangId)->value('suku_cadang_harga') ?? 0);
        $subtotal = $harga * $jumlah;

        $deptId = $request->input('department_id', $record->department_id);
        if ($deptId) {
            $department = Department::find($deptId);
            if ($department) {
                $terpakai = (float) PermintaanSukuCadang::where('department_id', $deptId)
                    ->where('permintaan_suku_cadang_id', '!=', $record->permintaan_suku_cadang_id)
                    ->whereNotIn('permintaan_suku_cadang_status', ['ditolak'])
                    ->sum('permintaan_suku_cadang_subtotal');
                $sisa = (float) $department->department_budget - $terpakai;
                if ($subtotal > $sisa) {
                    return $this->response([
                        'status' => false,
                        'message' => 'Budget department '.$department->department_nama.' tidak cukup. Sisa '.formatRupiah($sisa).', butuh '.formatRupiah($subtotal).'.',
                        'data' => null,
                    ]);
                }
            }
        }

        $request->merge([
            'permintaan_suku_cadang_harga' => $harga,
            'permintaan_suku_cadang_subtotal' => $subtotal,
        ]);

        return $this->response(\App\Actions\UpdateAction::run($request, $id, $this->model));
    }

    public function getApprove(GeneralRequest $request, $id)
    {
        if (! in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true)) abort(403);
        $record = $this->model->findOrFail($id);
        $record->update(['permintaan_suku_cadang_status' => 'disetujui']);
        return $this->response(['status' => true, 'message' => 'Permintaan disetujui.', 'data' => $record->fresh()]);
    }

    public function getReject(GeneralRequest $request, $id)
    {
        if (! in_array(auth()->user()->role ?? '', ['developer','admin','supervisor'], true)) abort(403);
        $record = $this->model->findOrFail($id);
        $record->update(['permintaan_suku_cadang_status' => 'ditolak']);
        return $this->response(['status' => true, 'message' => 'Permintaan ditolak.', 'data' => $record->fresh()]);
    }
}
