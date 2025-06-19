<?php

namespace App\Http\Controllers;

use App\Services\DataAnlitycsPqrsService;
use Illuminate\Http\Request;

class DataAnalitycsController extends Controller
{
    private DataAnlitycsPqrsService $dataAnalitycsService;
    public function __construct(DataAnlitycsPqrsService $dataAnalitycsService)
    {
        $this->dataAnalitycsService=$dataAnalitycsService;
    }
    /**
     * @OA\Get(
     *     path="/api/data/pqrs",
     *     summary="Obtener datos analíticos de PQRS",
     *     description="Retorna una colección de datos analizados de PQRS para visualización gráfica: históricos, tendencias, porcentajes por sede, área, característica, causa, entre otros.",
     *     tags={"Analytics"},
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         required=true,
     *         description="Fecha inicial del rango de análisis (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-05-01")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         required=true,
     *         description="Fecha final del rango de análisis (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-05-31")
     *     ),
     *     @OA\Parameter(
     *         name="from_tendencie",
     *         in="query",
     *         required=false,
     *         description="Fecha inicial del rango de tendencia (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-04-01")
     *     ),
     *     @OA\Parameter(
     *         name="to_tendencie",
     *         in="query",
     *         required=false,
     *         description="Fecha final del rango de tendencia (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-04-30")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos analíticos obtenidos correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="history", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoTypes", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoTypesByDate", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoCanals", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoCanalsByDate", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoSedes", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoSedeByDate", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPorcentsPqrsBySede", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsTendencie", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsTendencieByUsers", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsvsSedes", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsbyArea", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsVsSedesVsAreas", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsVsAreasVsSedes", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="tendenciePqrsByAreas", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsByCharacter", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsVsCharacterVsArea", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="infoPqrsByCause", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="tendenceRangeTimeResponsePqrs", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="tendenciaPqrsOnTime", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="dataResponseTimeByArea", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Parámetros inválidos en la solicitud",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="from", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="to", type="array", @OA\Items(type="string"))
     *             )
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

    public function getDataFromPqrs(Request $request){
        $response=$this->dataAnalitycsService->getDataAnalisysPqrs($request->query());
        return response()->json($response,200);

    }
}
