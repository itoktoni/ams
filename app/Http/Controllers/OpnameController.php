<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Opname;

class OpnameController extends Controller
{
    use ControllerTrait;

    public function __construct(Opname $model)
    {
        $this->model = $model::getModel();
    }
}
