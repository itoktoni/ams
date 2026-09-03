<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Teknisi;

class TeknisiController extends Controller
{
    use ControllerTrait;

    public function __construct(Teknisi $model)
    {
        $this->model = $model::getModel();
    }
}
