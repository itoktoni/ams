<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\StokSukuCadang;

class StokSukuCadangController extends Controller
{
    use ControllerTrait;

    public function __construct(StokSukuCadang $model)
    {
        $this->model = $model::getModel();
    }
}
