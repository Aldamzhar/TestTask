<?php

namespace App\Http\Controllers;

use App\Services\CalculateService;
use Illuminate\Http\Request;

class CalculateController extends Controller {

    private CalculateService $calculateService;

    public function __construct(CalculateService $calculateService) {
        $this->calculateService = $calculateService;
    }

    public function calculate(Request $request) {
        return $this->calculateService->calculate($request);
    }

    public function saveCalculation(Request $request) {
        return $this->calculateService->saveCalculation($request);
    }

}
