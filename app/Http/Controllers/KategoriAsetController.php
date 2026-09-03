<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\KategoriAset;

class KategoriAsetController extends Controller
{
    use ControllerTrait;

    public function __construct(KategoriAset $model)
    {
        $this->model = $model::getModel();
    }
}
