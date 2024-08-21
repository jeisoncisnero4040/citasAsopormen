<?php

namespace App\Http\Controllers;

use App\Services\ProfesionalService;
 

class ProfesionalController extends Controller
{
    protected $profesionalService;

    public function __construct(ProfesionalService $profesionalService)
    {
        $this->profesionalService=$profesionalService;
    }

    /**
     * @OA\Get(
     *     path="/api/get_profesionals/{string}",
     *     tags={"Profesionales"},
     *     summary="Buscar profesionales por nombre",
     *     description="Busca profesionales en la base de datos que coincidan con la cadena de búsqueda proporcionada. Retorna una lista de profesionales con información básica como el código, nombre y especialidad.",
     *     @OA\Parameter(
     *         name="string",
     *         in="path",
     *         required=true,
     *         description="Cadena de búsqueda para encontrar profesionales. Se permiten caracteres alfanuméricos.",
     *         @OA\Schema(
     *             type="string",
     *             example="Sergio"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de profesionales obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="ecc", type="string", example="1098707063"),
     *                     @OA\Property(property="enombre", type="string", example="Sergio Andrés Avila Cala"),
     *                     @OA\Property(property="nombre", type="string", example="Neuropsicología")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetro de búsqueda inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="el parametro de búsqueda debe ser válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron registros de profesionales",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="no se han encontado registros"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function getAllProfesionalByStringSearch($string)
    {
        $profesionals=$this->profesionalService->searchProfesionalByString($string)  ;
        return response()->json($profesionals,200);
    }

    /**
     * @OA\Get(
     *     path="/api/get_profesional_calendar/{cedula}",
     *     tags={"Profesionales"},
     *     summary="Obtener el calendario de un profesional",
     *     description="Devuelve el calendario de citas para un profesional específico basado en su cédula. El calendario se transforma en un formato compatible con FullCalendar.",
     *     @OA\Parameter(
     *         name="cedula",
     *         in="path",
     *         required=true,
     *         description="La cédula del profesional para obtener su calendario.",
     *         @OA\Schema(
     *             type="string",
     *             example="1098707063"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Calendario obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Juan Pérez - Consulta"),
     *                     @OA\Property(property="start", type="string", format="date-time", example="2024-08-19T09:00:00Z"),
     *                     @OA\Property(property="end", type="string", format="date-time", example="2024-08-19T09:45:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetro de cédula inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="parámetro de cédula debe ser válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron registros para la cédula proporcionada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="no se encontraron registros para la cédula proporcionada"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function getProfesionalCalendarByCedula($cedula){
        $profesionalCalendar=$this->profesionalService->getCaledarByProfesionalCedula($cedula);
        return response()->json($profesionalCalendar,200);
    }
}
