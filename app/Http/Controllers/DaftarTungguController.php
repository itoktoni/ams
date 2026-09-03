<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\DaftarTunggu;

class DaftarTungguController extends Controller
{
    use ControllerTrait;

    public function __construct(DaftarTunggu $model)
    {
        $this->model = $model::getModel();
    }
}
