<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Department;

class DepartmentController extends Controller
{
    use ControllerTrait;

    public function __construct(Department $model)
    {
        $this->model = $model::getModel();
    }
}
