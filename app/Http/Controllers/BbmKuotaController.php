<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\BbmKuota;
use App\Models\User;
use Carbon\Carbon;

class BbmKuotaController extends Controller
{
    use ControllerTrait;

    public function __construct(BbmKuota $model)
    {
        // Modul kuota hanya untuk admin/developer.
        if (! in_array(auth()->user()?->role ?? '', ['developer', 'admin'], true)) {
            abort(403);
        }

        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'userOptions' => User::getOptions(),
        ];

        return array_merge($default, $data);
    }

    protected function getData()
    {
        $query = $this->model->query()->with(['hasUser']);

        // Urutan bawaan: Januari–Desember (kalau tidak ada sort manual).
        if (! request()->input('sort.0')) {
            $query->orderBy('bbm_kuota_periode')->orderBy('bbm_kuota_id');
        }

        return $query;
    }

    public function postCreate(GeneralRequest $request)
    {
        $userId = $request->input('bbm_kuota_id_user');
        $periode = $this->normalizePeriode($request->input('bbm_kuota_periode'));

        if ($this->isDuplicate($userId, $periode)) {
            return $this->response($this->gagal('Kuota untuk user dan periode '.$periode.' sudah ada. Silakan edit data yang existing.'));
        }

        $request->merge(['bbm_kuota_periode' => $periode]);

        return $this->response(CreateAction::run($request, $this->model));
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $record = $this->model->findOrFail($id);
        $userId = $request->input('bbm_kuota_id_user', $record->bbm_kuota_id_user);
        $periode = $this->normalizePeriode($request->input('bbm_kuota_periode', $record->bbm_kuota_periode));

        if ($this->isDuplicate($userId, $periode, $record->bbm_kuota_id)) {
            return $this->response($this->gagal('Kuota untuk user dan periode '.$periode.' sudah ada. Silakan edit data yang existing.'));
        }

        $request->merge(['bbm_kuota_periode' => $periode]);

        return $this->response(UpdateAction::run($request, $id, $this->model));
    }

    private function isDuplicate(mixed $userId, string $periode, mixed $exceptId = null): bool
    {
        if (! $userId) {
            return false;
        }

        return BbmKuota::query()
            ->where('bbm_kuota_id_user', $userId)
            ->where('bbm_kuota_periode', $periode)
            ->when($exceptId, fn ($q) => $q->where('bbm_kuota_id', '!=', $exceptId))
            ->exists();
    }

    private function normalizePeriode(mixed $value): string
    {
        if (is_string($value) && preg_match('/^\d{4}-\d{2}$/', $value)) {
            return $value;
        }

        if ($value) {
            try {
                return Carbon::parse($value)->format('Y-m');
            } catch (\Throwable) {
                // fallback ke periode berjalan
            }
        }

        return now()->format('Y-m');
    }

    private function gagal(string $message): array
    {
        return ['status' => false, 'message' => $message, 'data' => $message];
    }
}
