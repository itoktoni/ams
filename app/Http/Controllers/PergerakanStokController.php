<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\PergerakanStok;

class PergerakanStokController extends Controller
{
    use ControllerTrait;

    public function __construct(PergerakanStok $model)
    {
        $this->model = $model::getModel();
    }
}
