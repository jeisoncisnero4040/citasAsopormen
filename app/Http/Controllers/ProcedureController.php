<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProcedureService;

class ProcedureController extends Controller
{
    private $procedureService;

    public function __construct(ProcedureService $procedureService)
    {
        $this->procedureService=$procedureService;
    }
    /**
     * @OA\Get(
     *     path="/get_procedures",
     *     tags={"Procedimientos"},
     *     summary="Obtener lista de procedimientos",
     *     description="Recupera todos los procedimientos disponibles, incluyendo el nombre, duración y si se envía recordatorio por WhatsApp.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de procedimientos obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Consulta médica"),
     *                 @OA\Property(property="duraccion", type="integer", example=30),
     *                 @OA\Property(property="recordatorio_whatsapp", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lista de procedimientos no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Lista de procesos no encontrada"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
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
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function getAllProcedures(){
        $procedures=$this->procedureService->getAllProcedures();
        return response()->json($procedures,200);
    }
}
