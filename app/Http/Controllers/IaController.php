<?php
namespace App\Http\Controllers;

use App\Services\IaService;

class IaController extends Controller
{
    private IaService $iaService;

    public function __construct(IaService $iaService)
    {
        $this->iaService = $iaService;
    }

    public function executeTest()
    {
        $response = $this->iaService->executeTest();
        return response()->json(['status' => 'success', 'data' => $response]);
    }
}