<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\BatchTiket;

class BatchTiketController extends Controller
{
    use ControllerTrait;

    public function __construct(BatchTiket $model)
    {
        $this->model = $model::getModel();
    }
}
