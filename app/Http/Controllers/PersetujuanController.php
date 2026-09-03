<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Persetujuan;

class PersetujuanController extends Controller
{
    use ControllerTrait;

    public function __construct(Persetujuan $model)
    {
        $this->model = $model::getModel();
    }
}
