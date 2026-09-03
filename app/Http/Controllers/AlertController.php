<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Alert;

class AlertController extends Controller
{
    use ControllerTrait;

    public function __construct(Alert $model)
    {
        $this->model = $model::getModel();
    }
}
