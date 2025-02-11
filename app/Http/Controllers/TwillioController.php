<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;

class TwillioController extends Controller
{   
    private $twillioService;
    public function __construct(TwilioService $twillioService)
    {
        $this->twillioService=$twillioService;
    }
    /**
     * @OA\Get(
     *     path="/whatsapp/history/{number}",
     *     tags={"Twillio"},
     *     summary="Obtener historial de mensajes con un número",
     *     description="Recupera los últimos 1000 mensajes enviados y recibidos de un usuario a través de WhatsApp, ordenados por fecha.",
     *     @OA\Parameter(
     *         name="number",
     *         in="path",
     *         required=true,
     *         description="Número de teléfono del cliente con el cual se desea obtener el historial de mensajes.",
     *         @OA\Schema(
     *             type="string",
     *             example="3001234567"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Número máximo de mensajes a recuperar (por defecto 1000).",
     *         @OA\Schema(
     *             type="integer",
     *             example=1000
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historial de mensajes recuperado exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="sid", type="string", example="SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"),
     *                 @OA\Property(property="from", type="string", example="whatsapp:+573001234567"),
     *                 @OA\Property(property="to", type="string", example="whatsapp:+573001234567"),
     *                 @OA\Property(property="body", type="string", example="Mensaje de prueba"),
     *                 @OA\Property(property="status", type="string", example="sent"),
     *                 @OA\Property(property="date_sent", type="string", format="date-time", example="2024-11-22 12:34:56")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Número de teléfono inválido o parámetros incorrectos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="error", type="string", example="Invalid phone number format")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron mensajes para el número especificado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="not found"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="error", type="string", example="No messages found for this number")
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

    public function getHistoryMsm($number){
        $response=$this->twillioService->getMessagesWithNumber($number);
        return response()->json($response);
    }
}
