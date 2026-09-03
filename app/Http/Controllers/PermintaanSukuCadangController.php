<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\DeleteAction;
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
                $budgetInfo = self::budgetInfo($dept);
            }
        }

        $default = [
            'model' => $this->model,
            'statusOptions' => StatusPermintaanEnum::getOptions(),
            'budgetInfo' => $budgetInfo,
        ];

        return array_merge($default, $data);
    }

    /**
     * Ringkasan budget department: terpakai (sudah disetujui), menunggu (reserve), sisa & tersedia.
     */
    protected static function budgetInfo(Department $dept, mixed $exceptId = null): array
    {
        $terpakai = PermintaanSukuCadang::terpakaiDepartment($dept->department_id, $exceptId);
        $pending = PermintaanSukuCadang::pendingDepartment($dept->department_id, $exceptId);
        $budget = (float) $dept->department_budget;

        return [
            'department' => $dept,
            'terpakai' => $terpakai,
            'pending' => $pending,
            'sisa' => $budget - $terpakai,
            'tersedia' => $budget - $terpakai - $pending,
        ];
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

        $info = self::budgetInfo($department);

        if ($subtotal > $info['tersedia']) {
            return $this->response([
                'status' => false,
                'message' => 'Budget department '.$department->department_nama.' tidak cukup. Tersedia '.formatRupiah($info['tersedia'])
                    .' (terpakai '.formatRupiah($info['terpakai']).', menunggu '.formatRupiah($info['pending']).'), butuh '.formatRupiah($subtotal).'.',
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
                $info = self::budgetInfo($department, $record->permintaan_suku_cadang_id);
                if ($subtotal > $info['tersedia']) {
                    return $this->response([
                        'status' => false,
                        'message' => 'Budget department '.$department->department_nama.' tidak cukup. Tersedia '.formatRupiah($info['tersedia'])
                            .' (terpakai '.formatRupiah($info['terpakai']).', menunggu '.formatRupiah($info['pending']).'), butuh '.formatRupiah($subtotal).'.',
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
        // 'ditolak' bukan status pemakaian → budget department dikembalikan.
        Department::syncTerpakai($record->department_id);
        return $this->response(['status' => true, 'message' => 'Permintaan ditolak.', 'data' => $record->fresh()]);
    }

    /**
     * Delete via query builder tidak memicu event model,
     * jadi budget department disinkronkan manual di sini.
     */
    public function getDelete(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);
        $departmentId = $record->department_id;

        $response = (new DeleteAction)->remove($id, $this->model);

        Department::syncTerpakai($departmentId);

        return $this->response($response);
    }

    public function postDelete(GeneralRequest $request)
    {
        $ids = (array) $request->input('ids', []);

        $departmentIds = $this->model->whereIn($this->model->field_primary(), $ids)
            ->pluck('department_id')
            ->filter()
            ->unique()
            ->all();

        $count = DeleteAction::run($request, $this->model);

        foreach ($departmentIds as $departmentId) {
            Department::syncTerpakai($departmentId);
        }

        return $this->response($count);
    }
}
