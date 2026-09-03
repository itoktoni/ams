<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\JadwalService;

class JadwalServiceController extends Controller
{
    use ControllerTrait;

    public function __construct(JadwalService $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasAset', 'hasTemplate']);
    }
}
