<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\MasterGroup;
use Illuminate\Support\Facades\DB;

class MasterGroupController extends Controller
{
    use ControllerTrait;

    public function __construct(MasterGroup $model)
    {
        $this->model = $model::getModel();
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), [
            'linkedTeknisiIds' => [],
            'linkedUserIds' => [],
        ]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->with(['hasTeknisi','hasUsers'])->findOrFail($id);
        $linkedTeknisi = $data->hasTeknisi->pluck('teknisi_id')->all();
        $linkedUsers = $data->hasUsers->pluck('id')->all();
        // jika group sudah pakai users, pakai itu; fallback ke teknisi lama
        $linked = !empty($linkedUsers) ? $linkedUsers : $linkedTeknisi;

        return $this->views($this->template(), [
            'model' => $data,
            'linkedTeknisiIds' => $linkedTeknisi,
            'linkedUserIds' => $linked,
        ]);
    }

    public function postCreate(GeneralRequest $request)
    {
        if (empty(trim((string) $request->input('master_group_kode')))) {
            $request->merge(['master_group_kode' => 'GRP-'.now()->format('YmdHis').'-'.rand(100,999)]);
        }
        if (empty($request->input('master_group_status'))) {
            $request->merge(['master_group_status' => 'aktif']);
        }
        $userIds = $request->input('user_ids', $request->input('teknisi_ids', []));
        $payload = \App\Actions\CreateAction::run($request, $this->model);
        if (!empty($payload['status']) && $payload['data'] instanceof MasterGroup) {
            $this->syncUsers($payload['data']->master_group_id, $userIds);
            // backward compat: jika masih kirim teknisi_ids, sync juga
            if ($request->has('teknisi_ids')) $this->syncTeknisi($payload['data']->master_group_id, (array) $request->input('teknisi_ids', []));
        }
        return $this->response($payload);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $userIds = $request->input('user_ids', $request->input('teknisi_ids', []));
        $payload = \App\Actions\UpdateAction::run($request, (int) $id, $this->model);
        if (!empty($payload['status'])) {
            $this->syncUsers((int) $id, $userIds);
            if ($request->has('teknisi_ids')) $this->syncTeknisi((int) $id, (array) $request->input('teknisi_ids', []));
        }
        return $this->response($payload);
    }

    private function syncTeknisi(int $groupId, array $ids): void
    {
        $ids = array_filter(array_map('intval', (array) $ids));
        DB::table('master_group_teknisi')->where('master_group_teknisi_id_group', $groupId)->delete();
        foreach ($ids as $tid) {
            DB::table('master_group_teknisi')->insert([
                'master_group_teknisi_id_group' => $groupId,
                'master_group_teknisi_id_teknisi' => $tid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function syncUsers(int $groupId, array $ids): void
    {
        $ids = array_filter(array_map('intval', (array) $ids));
        DB::table('master_group_user')->where('master_group_user_id_group', $groupId)->delete();
        foreach ($ids as $uid) {
            DB::table('master_group_user')->insert([
                'master_group_user_id_group' => $groupId,
                'master_group_user_id_user' => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function getData()
    {
        return $this->model->withCount(['hasTeknisi','hasUsers']);
    }
}
