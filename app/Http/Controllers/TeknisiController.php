<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TeknisiController extends Controller
{
    use ControllerTrait {
        getData as traitGetData;
        share as traitShare;
    }

    public function __construct(User $model)
    {
        // Teknisi sekarang dari users role=teknisi (1 table)
        $this->model = $model::getModel();
    }

    protected function getData(): Builder
    {
        return $this->traitGetData()->where('role', 'teknisi');
    }

    protected function share($data = [])
    {
        // paksa role teknisi saat create/update via teknisi module
        if (request()->isMethod('post')) {
            request()->merge(['role' => 'teknisi']);
        }
        return $this->traitShare($data);
    }

    public function getCreate(\App\Http\Requests\GeneralRequest $request)
    {
        return $this->views($this->template(), []);
    }

    public function getUpdate(\App\Http\Requests\GeneralRequest $request, $id)
    {
        $data = $this->model->where('role','teknisi')->findOrFail($id);
        return $this->views($this->template(), ['model' => $data]);
    }
}
