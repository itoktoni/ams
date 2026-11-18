<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\JenisPembayaranEnum;
use App\Enums\StatusPembayaranEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\Pembayaran;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    use ControllerTrait {
        postCreate as traitPostCreate;
        postUpdate as traitPostUpdate;
    }

    public function __construct(Pembayaran $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'jenis' => JenisPembayaranEnum::getOptions(),
            'status' => StatusPembayaranEnum::getOptions(),
        ];

        return array_merge($default, $data);
    }

    public function postCreate(GeneralRequest $request)
    {
        $bukti = $this->handleBukti($request, null);
        if ($bukti !== null) {
            $request->merge(['pembayaran_bukti' => $bukti]);
        }

        return $this->traitPostCreate($request);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $row = $this->model->findOrFail($id);
        $existing = $row->pembayaran_bukti ?? null;

        $bukti = $this->handleBukti($request, $existing);
        if ($bukti !== $existing) {
            $request->merge(['pembayaran_bukti' => $bukti]);
        }

        return $this->traitPostUpdate($request, $id);
    }

    private function handleBukti(GeneralRequest $request, ?string $existing): ?string
    {
        if ($request->hasFile('pembayaran_bukti')) {
            try {
                $path = uploadFile($request->file('pembayaran_bukti'), 'pembayaran', ['max_size' => 4096]);
                $this->deleteBuktiFile($existing);

                return $path;
            } catch (\InvalidArgumentException $e) {
                throw ValidationException::withMessages(['pembayaran_bukti' => $e->getMessage()]);
            }
        }

        if ($request->boolean('remove_pembayaran_bukti')) {
            $this->deleteBuktiFile($existing);

            return null;
        }

        return $existing;
    }

    private function deleteBuktiFile(?string $path): void
    {
        if (empty($path)) return;
        $file = storage_path('app/public/'.$path);
        if (file_exists($file)) { unlink($file); }
    }
}
