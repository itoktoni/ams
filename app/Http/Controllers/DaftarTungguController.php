<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\DaftarTunggu;

class DaftarTungguController extends Controller
{
    use ControllerTrait {
        getData as traitGetData;
    }

    public function __construct(DaftarTunggu $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->traitGetData()->with(['hasAset', 'hasPeminjam']);
    }
}
