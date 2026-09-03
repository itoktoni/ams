<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Gudang;

class GudangController extends Controller
{
    use ControllerTrait;

    public function __construct(Gudang $model)
    {
        $this->model = $model::getModel();
    }
}
