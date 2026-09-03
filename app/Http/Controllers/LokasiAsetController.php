<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\LokasiAset;

class LokasiAsetController extends Controller
{
    use ControllerTrait;

    public function __construct(LokasiAset $model)
    {
        $this->model = $model::getModel();
    }
}
