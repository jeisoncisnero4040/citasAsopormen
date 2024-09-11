<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CentralOfficeService;

class CentralOfficeController extends Controller
{
    private $centralOfficeService;

    public function __construct(CentralOfficeService $centralOfficeService){
        $this->centralOfficeService=$centralOfficeService;
    }
    /**
     * @OA\Get(
     *     path="/get_centrals_office",
     *     tags={"Sedes"},
     *     summary="Obtener listado de sedes centrales",
     *     description="Obtiene una lista de las sedes centrales con su código, nombre y dirección.",
     *     @OA\Response(
     *         response=200,
     *         description="Listado de sedes encontrado exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="cod", type="string", example="001"),
     *                 @OA\Property(property="nombre", type="string", example="Sede Principal"),
     *                 @OA\Property(property="direccion", type="string", example="Calle 123 #45-67, Ciudad")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron sedes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="No se encontraron sedes"),
     *             @OA\Property(property="status", type="integer", example=404),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Server error"),
     *             @OA\Property(property="status", type="integer", example=500),
     *         )
     *     )
     * )
     */

     public function getCentralsOffice(){
        $officesResponse=$this->centralOfficeService->getAllCentralsOffice();
        return response()->json($officesResponse,200);
     }
}
