<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\TemplateService;

class TemplateServiceController extends Controller
{
    use ControllerTrait;

    public function __construct(TemplateService $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        return $this->model->query()->with(['hasKategori']);
    }
}
