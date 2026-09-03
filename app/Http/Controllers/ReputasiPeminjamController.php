<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\ReputasiPeminjam;

class ReputasiPeminjamController extends Controller
{
    use ControllerTrait;

    public function __construct(ReputasiPeminjam $model)
    {
        $this->model = $model::getModel();
    }
}
