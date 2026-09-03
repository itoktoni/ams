<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\KelompokPenyusutan;

class KelompokPenyusutanController extends Controller
{
    use ControllerTrait;

    public function __construct(KelompokPenyusutan $model)
    {
        $this->model = $model::getModel();
    }
}
