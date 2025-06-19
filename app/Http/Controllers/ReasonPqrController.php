<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReasonsPqrService;

class ReasonPqrController extends Controller
{
    private ReasonsPqrService $reasonPqrService;
    public function __construct(ReasonsPqrService $reasonPqrService) {
        $this->reasonPqrService=$reasonPqrService;
    }
    /**
     * @OA\Get(
     *     path="/api/reasons",
     *     summary="Listar motivos PQRS",
     *     description="Retorna un listado de motivos por los cuales se realizan PQRS. Cada motivo incluye nivel, nombre, y relaciones jerárquicas.",
     *     tags={"Reasons"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de motivos obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id_motivo", type="string", example="1"),
     *                     @OA\Property(property="nombre", type="string", example="INSATISFACCIÓN RELACIONADA CON LA  ATENCIÓN EN SALUD"),
     *                     @OA\Property(property="id_padre", type="string", nullable=true, example=null),
     *                     @OA\Property(property="nivel", type="string", example="macromotivo")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron motivos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reasons not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */

    public function get(){
        $response=$this->reasonPqrService->getAllReasonsPqrs();
        return response()->json($response,200);
    }
}
