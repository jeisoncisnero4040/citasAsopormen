<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PqrService;
class PqrController extends Controller

{
    private PqrService $pqrService;
    public function __construct(PqrService $pqrService) {
        $this->pqrService=$pqrService;
    }

    /**
     * @OA\Post(
     *     path="/api/pqrs",
     *     tags={"Pqrs"},
     *     summary="Crear un nuevo PQR",
     *     description="Crea un nuevo registro de PQR (Petición, Queja, Reclamo o Sugerencia). Valida los campos requeridos y devuelve el PQR creado.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos requeridos para crear un PQR",
     *         @OA\JsonContent(
     *             required={
     *                 "descripcion", "nombre_quien_registra", "identificacion_usuario", "celular_usuario",
     *                 "email_usuario", "canal_id", "tipo_id", "tipo_usuario", "nombre_usuario",
     *                 "sede_id", "area_id", "sogcs_id", "motivo_id"
     *             },
     *             @OA\Property(property="descripcion", type="string", example="el usuario solicita un historial de citas del mes de febrero"),
     *             @OA\Property(property="nombre_quien_registra", type="string", example="Jeison Olegario Cisneros"),
     *             @OA\Property(property="identificacion_usuario", type="string", example="1085896127"),
     *             @OA\Property(property="celular_usuario", type="string", example="3222551222"),
     *             @OA\Property(property="email_usuario", type="string", format="email", example="legoladhojaverde@gmail.com"),
     *             @OA\Property(property="canal_id", type="integer", example=1),
     *             @OA\Property(property="tipo_id", type="integer", example=2),
     *             @OA\Property(property="tipo_usuario", type="string", example="Secretaria De Salud Departamental"),
     *             @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *             @OA\Property(property="referencia", type="string", example="Q_4_20250508"),
     *             @OA\Property(property="sede_id", type="integer", example=3),
     *             @OA\Property(property="area_id", type="integer", example=4),
     *             @OA\Property(property="sogcs_id", type="integer", example=5),
     *             @OA\Property(property="motivo_id", type="integer", example=6)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="PQR creado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="created"),
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=18),
     *                 @OA\Property(property="descripcion", type="string", example="el usuario solicita un historial de citas del mes de febrero"),
     *                 @OA\Property(property="fecha_creacion", type="string", format="date-time", example="2025-05-08 11:54:00"),
     *                 @OA\Property(property="estado", type="string", example="area respondido"),
     *                 @OA\Property(property="nombre_quien_registra", type="string", example="Jeison Olegario Cisneros"),
     *                 @OA\Property(property="identificacion_usuario", type="string", example="1085896127"),
     *                 @OA\Property(property="celular_usuario", type="string", example="3222551222"),
     *                 @OA\Property(property="email_usuario", type="string", example="legoladhojaverde@gmail.com"),
     *                 @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *                 @OA\Property(property="tipo_usuario", type="string", example="Secretaria De Salud Departamental"),
     *                 @OA\Property(property="fecha_envio_area", type="string", example="2025-05-12 15:08:00"),
     *                 @OA\Property(property="fecha_respuesta", type="string", example="2025-05-12 15:11:00"),
     *                 @OA\Property(property="respuesta", type="string", example="la llegada de xavi alonso"),
     *                 @OA\Property(property="causas", type="string", example="2200 millones de brasileños"),
     *                 @OA\Property(property="usuario_respuesta_area", type="string", example="jeison olegario cisneros figueroa"),
     *                 @OA\Property(property="fecha_respuesta_usuario", type="string", example="2025-05-19 14:15:00"),
     *                 @OA\Property(property="horas_oportunidad_respuesta_area", type="string", example="36"),
     *                 @OA\Property(property="medio_respuesta_usuario", type="string", nullable=true),
     *                 @OA\Property(property="dias_aportunidad_res_calidad", type="string", nullable=true),
     *                 @OA\Property(property="canal", type="string", example="Correo Electrónico"),
     *                 @OA\Property(property="tipo_pqr", type="string", example="Queja"),
     *                 @OA\Property(property="area_servicio", type="string", example="ADMINISTRATIVO - ABA"),
     *                 @OA\Property(property="cordinador_area", type="string", example="Desarrollo pruebas"),
     *                 @OA\Property(property="celular_coordinador", type="string", example="3222551222"),
     *                 @OA\Property(property="email_cordinador", type="string", example="dev1@asopormen.org.co"),
     *                 @OA\Property(property="sogcs", type="string", example="Continuidad"),
     *                 @OA\Property(property="macromotivo", type="string", example="Macromotivo 1"),
     *                 @OA\Property(property="motivo_general", type="string", example="Motivo General 1"),
     *                 @OA\Property(property="motivo_especifico", type="string", example="Motivo Específico 1"),
     *                 @OA\Property(property="tipo_motivo", type="string", example="Tipo de Motivo 1"),
     *                 @OA\Property(property="causa_motivo", type="string", example="Causa de Motivo 1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="error", type="string", example="El campo nombre_quien_registra es obligatorio"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="error", type="string", example="Error inesperado al crear el PQR"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function new(Request $request){  
        $data = $request->except('pdfInfo');
        $file = $request->file('pdfInfo')??null;
        $response = $this->pqrService->createPqr($data,$file);
        return response()->json($response,201);
    }
    /**
     * @OA\Get(
     *     path="/api/pqrs/{id}",
     *     tags={"Pqrs"},
     *     summary="Obtener un PQR por su ID",
     *     description="Obtiene la información detallada de un PQR dado su ID. Puede filtrarse opcionalmente por estado.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del PQR",
     *         @OA\Schema(type="integer", example=18)
     *     ),
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         required=false,
     *         description="Estado opcional para filtrar el PQR",
     *         @OA\Schema(type="string", example="area respondido")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PQR encontrado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=18),
     *                 @OA\Property(property="descripcion", type="string", example="el usuario solicita un historial de citas del mes de febrero"),
     *                 @OA\Property(property="fecha_creacion", type="string", example="2025-05-08 11:54:00"),
     *                 @OA\Property(property="estado", type="string", example="area respondido"),
     *                 @OA\Property(property="nombre_quien_registra", type="string", example="Jeison Olegario Cisneros"),
     *                 @OA\Property(property="identificacion_usuario", type="string", example="1085896127"),
     *                 @OA\Property(property="celular_usuario", type="string", example="3222551222"),
     *                 @OA\Property(property="email_usuario", type="string", example="legoladhojaverde@gmail.com"),
     *                 @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *                 @OA\Property(property="tipo_usuario", type="string", example="Secretaria De Salud Departamental"),
     *                 @OA\Property(property="referencia", type="string", example="Q_4_20250508"),
     *                 @OA\Property(property="fecha_envio_area", type="string", example="2025-05-12 15:08:00"),
     *                 @OA\Property(property="fecha_respuesta", type="string", example="2025-05-12 15:11:00"),
     *                 @OA\Property(property="respuesta", type="string", example="la llegada de xavi alonso"),
     *                 @OA\Property(property="causas", type="string", example="2200 millones de brasileños"),
     *                 @OA\Property(property="usuario_respuesta_area", type="string", example="jeison olegario cisneros figueroa"),
     *                 @OA\Property(property="medio_respuesta_usuario", type="string", nullable=true),
     *                 @OA\Property(property="fecha_respuesta_usuario", type="string", example="2025-05-19 14:15:00"),
     *                 @OA\Property(property="horas_oportunidad_respuesta_area", type="string", example="36"),
     *                 @OA\Property(property="dias_aportunidad_res_calidad", type="string", nullable=true),
     *                 @OA\Property(property="canal", type="string", example="Correo Electrónico"),
     *                 @OA\Property(property="tipo_pqr", type="string", example="Queja"),
     *                 @OA\Property(property="area_servicio", type="string", example="ADMINISTRATIVO - ABA"),
     *                 @OA\Property(property="cordinador_area", type="string", example="Desarrollo pruebas"),
     *                 @OA\Property(property="celular_coordinador", type="string", example="3222551222"),
     *                 @OA\Property(property="email_cordinador", type="string", example="dev1@asopormen.org.co"),
     *                 @OA\Property(property="sogcs", type="string", example="Continuidad"),
     *                 @OA\Property(property="macromotivo", type="string", example="Macromotivo 1"),
     *                 @OA\Property(property="motivo_general", type="string", example="Motivo General 1"),
     *                 @OA\Property(property="motivo_especifico", type="string", example="Motivo Específico 1"),
     *                 @OA\Property(property="tipo_motivo", type="string", example="Tipo de Motivo 1"),
     *                 @OA\Property(property="causa_motivo", type="string", example="Causa de Motivo 1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="PQR no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="error", type="string", example="PQR no encontrado"),
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
     *             @OA\Property(property="error", type="string", example="Server error"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */

    public function find(int $pqr_id,Request $request){
        $response=$this->pqrService->findPqrById(pqrId:$pqr_id,request:$request->query());
        return response()->json($response,200);
    }
    /**
     * @OA\Get(
     *     path="/api/pqrs",
     *     tags={"Pqrs"},
     *     summary="Listar todos los PQRs",
     *     description="Obtiene una lista de todos los PQRs registrados. Puede filtrarse por estado usando un parámetro de consulta.",
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         required=false,
     *         description="Filtrar por estado del PQR",
     *         @OA\Schema(type="string", example="area respondido")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de PQRs obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=18),
     *                     @OA\Property(property="descripcion", type="string", example="el usuario solicita un historial de citas del mes de febrero"),
     *                     @OA\Property(property="fecha_creacion", type="string", example="2025-05-08 11:54:00"),
     *                     @OA\Property(property="estado", type="string", example="area respondido"),
     *                     @OA\Property(property="nombre_quien_registra", type="string", example="Jeison Olegario Cisneros"),
     *                     @OA\Property(property="identificacion_usuario", type="string", example="1085896127"),
     *                     @OA\Property(property="celular_usuario", type="string", example="3222551222"),
     *                     @OA\Property(property="email_usuario", type="string", example="legoladhojaverde@gmail.com"),
     *                     @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *                     @OA\Property(property="tipo_usuario", type="string", example="Secretaria De Salud Departamental"),
     *                     @OA\Property(property="referencia", type="string", example="Q_4_20250508"),
     *                     @OA\Property(property="fecha_envio_area", type="string", example="2025-05-12 15:08:00"),
     *                     @OA\Property(property="fecha_respuesta", type="string", example="2025-05-12 15:11:00"),
     *                     @OA\Property(property="respuesta", type="string", example="la llegada de xavi alonso"),
     *                     @OA\Property(property="usuario_respuesta_area", type="string", example="jeison olegario cisneros figueroa"),
     *                     @OA\Property(property="medio_respuesta_usuario", type="string", nullable=true),
     *                     @OA\Property(property="fecha_respuesta_usuario", type="string", example="2025-05-19 14:15:00"),
     *                     @OA\Property(property="horas_oportunidad_respuesta_area", type="string", example="36"),
     *                     @OA\Property(property="canal", type="string", example="Correo Electrónico"),
     *                     @OA\Property(property="tipo_pqr", type="string", example="Queja"),
     *                     @OA\Property(property="area_servicio", type="string", example="ADMINISTRATIVO - ABA"),
     *                     @OA\Property(property="cordinador_area", type="string", example="Desarrollo pruebas"),
     *                     @OA\Property(property="email_cordinador", type="string", example="dev1@asopormen.org.co"),
     *                     @OA\Property(property="sogcs", type="string", example="Continuidad"),
     *                     @OA\Property(property="macromotivo", type="string", example="Macromotivo 1"),
     *                     @OA\Property(property="motivo_general", type="string", example="Motivo General 1"),
     *                     @OA\Property(property="motivo_especifico", type="string", example="Motivo Específico 1"),
     *                     @OA\Property(property="tipo_motivo", type="string", example="Tipo de Motivo 1"),
     *                     @OA\Property(property="causa_motivo", type="string", example="Causa de Motivo 1")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="error", type="string", example="Server error"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */


    public function getAll(Request $request){
        $response=$this->pqrService->getAllPqrs(request:$request->query());
        return response()->json($response,200);
    }
    /**
     * @OA\Post(
     *     path="/api/pqrs/notify-area",
     *     tags={"Pqrs"},
     *     summary="Notificar PQR al área correspondiente",
     *     description="Este endpoint notifica por correo al área correspondiente que existe un nuevo PQR. Además, se genera y envía un enlace público para que dicha área pueda responder el PQR. Retorna el PQR actualizado.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Información del PQR que se va a notificar",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"id", "cordinador_area", "url_form", "nombre_usuario", "descripcion", "tipo_pqr", "macromotivo", "motivo_general", "motivo_especifico", "tipo_motivo", "causa_motivo"},
     *             @OA\Property(property="id", type="integer", example=18),
     *             @OA\Property(property="cordinador_area", type="string", example="Desarrollo pruebas"),
     *             @OA\Property(property="url_form", type="string", example="https://miapp.com/responder/Q_4_20250508"),
     *             @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *             @OA\Property(property="descripcion", type="string", example="el usuario solicita un historial de citas del mes de febrero"),
     *             @OA\Property(property="tipo_pqr", type="string", example="Queja"),
     *             @OA\Property(property="macromotivo", type="string", example="Macromotivo 1"),
     *             @OA\Property(property="motivo_general", type="string", example="Motivo General 1"),
     *             @OA\Property(property="motivo_especifico", type="string", example="Motivo Específico 1"),
     *             @OA\Property(property="tipo_motivo", type="string", example="Tipo de Motivo 1"),
     *             @OA\Property(property="causa_motivo", type="string", example="Causa de Motivo 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PQR actualizado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=18),
     *                 @OA\Property(property="estado", type="string", example="notificado"),
     *                 @OA\Property(property="url_form", type="string", example="https://miapp.com/responder/Q_4_20250508"),
     *                 @OA\Property(property="cordinador_area", type="string", example="Desarrollo pruebas"),
     *                 @OA\Property(property="email_cordinador", type="string", example="dev1@asopormen.org.co"),
     *                 @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *                 @OA\Property(property="descripcion", type="string", example="el usuario solicita un historial de citas del mes de febrero"),
     *                 @OA\Property(property="tipo_pqr", type="string", example="Queja"),
     *                 @OA\Property(property="macromotivo", type="string", example="Macromotivo 1"),
     *                 @OA\Property(property="motivo_general", type="string", example="Motivo General 1"),
     *                 @OA\Property(property="motivo_especifico", type="string", example="Motivo Específico 1"),
     *                 @OA\Property(property="tipo_motivo", type="string", example="Tipo de Motivo 1"),
     *                 @OA\Property(property="causa_motivo", type="string", example="Causa de Motivo 1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="error", type="string", example="Faltan campos requeridos"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="PQR no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="PQR no encontrado"),
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
     *             @OA\Property(property="error", type="string", example="Server error"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */

    public function notifyPqrsToArea(Request $pqrs){
        $token=$pqrs->bearerToken();
        $response=$this->pqrService->sendAreaNotification($pqrs->all(),$token??null);
        return response()->json($response,200);
    }
    /**
     * @OA\Post(
     *     path="/api/pqrs/answer-area",
     *     tags={"Pqrs"},
     *     summary="Responder PQR desde el área correspondiente",
     *     description="Este endpoint permite que el área correspondiente responda un PQR, adjuntando acciones correctivas y evidencias. Guarda los archivos en el storage y actualiza el estado del PQR a 'respondido-area'.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Información de la respuesta y acciones correctivas del área",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"id", "usuario_respuesta_area", "respuesta", "causas", "actions"},
     *                 @OA\Property(property="id", type="string", example="Q_4_20250508"),
     *                 @OA\Property(property="usuario_respuesta_area", type="string", example="Carlos Rodríguez"),
     *                 @OA\Property(property="respuesta", type="string", example="Se revisó el historial y se generó un reporte con las citas del mes de febrero."),
     *                 @OA\Property(property="causas", type="string", example="Falta de información en la base de datos."),
     *                 
     *                 @OA\Property(
     *                     property="actions[0][descripcion]",
     *                     type="string",
     *                     example="Se implementó un sistema de alertas para evitar el error."
     *                 ),
     *                 @OA\Property(
     *                     property="actions[0][persona_responsable]",
     *                     type="string",
     *                     example="Andrea Gómez"
     *                 ),
     *                 @OA\Property(
     *                     property="actions[0][evidence]",
     *                     type="string",
     *                     format="binary",
     *                     description="Archivo de imagen JPG/PNG (máx. 2MB)"
     *                 ),
     *                 
     *                 @OA\Property(
     *                     property="actions[1][descripcion]",
     *                     type="string",
     *                     example="Capacitación al equipo sobre el nuevo procedimiento."
     *                 ),
     *                 @OA\Property(
     *                     property="actions[1][persona_responsable]",
     *                     type="string",
     *                     example="Luis Pérez"
     *                 ),
     *                 @OA\Property(
     *                     property="actions[1][evidence]",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta del área registrada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=18),
     *                 @OA\Property(property="estado", type="string", example="respondido-area"),
     *                 @OA\Property(property="respuesta", type="string"),
     *                 @OA\Property(property="acciones", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="descripcion", type="string"),
     *                     @OA\Property(property="persona_responsable", type="string"),
     *                     @OA\Property(property="evidencia_url", type="string")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos inválidos o faltantes",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="error", type="string", example="Debe agregar al menos una acción correctiva"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al guardar respuesta",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al guardar respuesta del área"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function handleAnswerPqrsByArea(Request $request){
        $data = $request->all();
        $actions = $data['actions'] ?? [];
        $files = $request->file('actions') ?? [];
        $result = $this->pqrService->saveAnswerArea($data, $actions, $files);
        return response()->json($result,200);
    }
    /**
     * @OA\Get(
     *     path="/api/pqrs/{pqr_id}/actions",
     *     tags={"Pqrs"},
     *     summary="Obtener acciones registradas para un PQRS",
     *     description="Devuelve una lista de acciones correctivas asociadas a un PQRS específico, incluyendo la descripción, el responsable y la URL de la evidencia (imagen).",
     *     @OA\Parameter(
     *         name="pqr_id",
     *         in="path",
     *         required=true,
     *         description="ID del PQRS",
     *         @OA\Schema(type="integer", example=18)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Acciones recuperadas correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="21"),
     *                     @OA\Property(property="id_pqrs", type="string", example="18"),
     *                     @OA\Property(property="descripcion", type="string", example="aksdkasdk ajsdnasdmasd"),
     *                     @OA\Property(property="persona_responsable", type="string", example="xavi alonso"),
     *                     @OA\Property(
     *                         property="url_evidencia",
     *                         type="string",
     *                         format="url",
     *                         example="https://res.cloudinary.com/dxalvdckk/image/upload/v1747080686/pqrs/evidencias/tnnd8dfgwmczugf1ljjk.png"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="PQRS no encontrado o sin acciones registradas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="not found"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al recuperar acciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */

    public function getActionsDataPqrs(int $pqr_id){
        $response=$this->pqrService->getCtionsDataPqrs($pqr_id);
        return response()->json($response,200);
    }
    /**
     * @OA\Get(
     *     path="/api/pqrs/{pqr_id_encoded}/encoded",
     *     tags={"Pqrs"},
     *     summary="Obtener información de un PQRS por ID codificado",
     *     description="Retorna los datos completos de un PQRS utilizando su ID hasheado (hash).",
     *     @OA\Parameter(
     *         name="pqr_id_encoded",
     *         in="path",
     *         required=true,
     *         description="ID codificado (hash) del PQRS",
     *         @OA\Schema(type="string", example="qwe123zxc456")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PQRS encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="18"),
     *                     @OA\Property(property="descripcion", type="string", example="el usuario solicita un historial de citas del mes de febrero"),
     *                     @OA\Property(property="fecha_creacion", type="string", example="2025-05-08 11:54:00"),
     *                     @OA\Property(property="estado", type="string", example="area respondido"),
     *                     @OA\Property(property="nombre_quien_registra", type="string", example="Jeison Olegario Cisneros"),
     *                     @OA\Property(property="identificacion_usuario", type="string", example="1085896127"),
     *                     @OA\Property(property="celular_usuario", type="string", example="3222551222"),
     *                     @OA\Property(property="email_usuario", type="string", example="legoladhojaverde@gmail.com"),
     *                     @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *                     @OA\Property(property="tipo_usuario", type="string", example="Secretaria De Salud Departamental"),
     *                     @OA\Property(property="referencia", type="string", example="Q_4_20250508"),
     *                     @OA\Property(property="fecha_envio_area", type="string", example="2025-05-12 15:08:00"),
     *                     @OA\Property(property="fecha_respuesta", type="string", example="2025-05-12 15:11:00"),
     *                     @OA\Property(property="respuesta", type="string", example="la llegada de xavi alonso"),
     *                     @OA\Property(property="causas", type="string", example="2200 millones de brasileños"),
     *                     @OA\Property(property="usuario_respuesta_area", type="string", example="jeison olegario cisneros figueroa"),
     *                     @OA\Property(property="medio_respuesta_usuario", type="string", nullable=true, example=null),
     *                     @OA\Property(property="fecha_respuesta_usuario", type="string", example="2025-05-19 14:15:00"),
     *                     @OA\Property(property="horas_oportunidad_respuesta_area", type="string", example="36"),
     *                     @OA\Property(property="dias_aportunidad_res_calidad", type="string", nullable=true, example=null),
     *                     @OA\Property(property="canal", type="string", example="Correo Electrónico"),
     *                     @OA\Property(property="tipo_pqr", type="string", example="Queja"),
     *                     @OA\Property(property="area_servicio", type="string", example="ADMINISTRATIVO - ABA"),
     *                     @OA\Property(property="cordinador_area", type="string", example="Desarrollo pruebas"),
     *                     @OA\Property(property="celular_coordinador", type="string", example="3222551222"),
     *                     @OA\Property(property="email_cordinador", type="string", example="dev1@asopormen.org.co"),
     *                     @OA\Property(property="sogcs", type="string", example="Continuidad"),
     *                     @OA\Property(property="macromotivo", type="string", example="Macromotivo 1"),
     *                     @OA\Property(property="motivo_general", type="string", example="Motivo General 1"),
     *                     @OA\Property(property="motivo_especifico", type="string", example="Motivo Específico 1"),
     *                     @OA\Property(property="tipo_motivo", type="string", example="Tipo de Motivo 1"),
     *                     @OA\Property(property="causa_motivo", type="string", example="Causa de Motivo 1")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="PQRS no encontrado con ese ID codificado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="not found"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al recuperar el PQRS",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */

    public function getPqrsEncoded($pqrs_id_encoded){
        $response=$this->pqrService->getPqrsByIdHashed($pqrs_id_encoded);
        return response()->json($response,200);
    }
    /**
     * @OA\Schema(
     *     schema="Pqrs",
     *     type="object",
     *     title="PQRS",
     *     description="Estructura completa de un PQRS",
     *     @OA\Property(property="id", type="integer", example=18),
     *     @OA\Property(property="descripcion", type="string", example="El usuario solicita un historial de citas del mes de febrero."),
     *     @OA\Property(property="fecha_creacion", type="string", format="date-time", example="2025-05-08 11:54:00"),
     *     @OA\Property(property="estado", type="string", example="cerrado"),
     *     @OA\Property(property="nombre_usuario", type="string", example="Jeison Cisneros"),
     *     @OA\Property(property="tipo_usuario", type="string", example="Secretaria De Salud Departamental"),
     *     @OA\Property(property="respuesta", type="string", example="Respuesta del área."),
     *     @OA\Property(property="usuario_respuesta_area", type="string", example="Coordinador PQRS"),
     *     @OA\Property(property="url_respuesta", type="string", example="https://example.com/respuesta.pdf"),
     *     @OA\Property(property="fecha_respuesta", type="string", format="date-time", example="2025-05-12 15:11:00"),
     *     @OA\Property(property="canal", type="string", example="Correo Electrónico"),
     *     @OA\Property(property="area_servicio", type="string", example="ADMINISTRATIVO - ABA"),
     *     @OA\Property(property="cordinador_area", type="string", example="Desarrollo pruebas"),
     *     @OA\Property(property="macromotivo", type="string", example="Macromotivo 1"),
     *     @OA\Property(property="motivo_general", type="string", example="Motivo General 1"),
     *     @OA\Property(property="motivo_especifico", type="string", example="Motivo Específico 1"),
     *     @OA\Property(property="tipo_motivo", type="string", example="Tipo de Motivo 1"),
     *     @OA\Property(property="causa_motivo", type="string", example="Causa de Motivo 1")
     * )
     */


    public function changeArea($pqr_id,Request $newDataPqrsData){
        $response=$this->pqrService->changeAreaPqrs($pqr_id,$newDataPqrsData->all());
        return response()->json($response,200);
    }
    /**
     * @OA\Post(
     *     path="/pqrs/answer/client",
     *     summary="Responder PQRS al cliente",
     *     description="Envía una respuesta al cliente vía email indicando que el PQRS fue resuelto, adjuntando un documento PDF obligatorio y evidencias opcionales. Actualiza el estado del PQRS a respondido y retorna el PQRS actualizado.",
     *     tags={"Pqrs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id", "user", "date", "canal", "user_type", "file"},
     *                 @OA\Property(
     *                     property="id",
     *                     type="string",
     *                     description="ID del PQRS a responder",
     *                     example="18"
     *                 ),
     *                 @OA\Property(
     *                     property="user",
     *                     type="string",
     *                     description="Nombre del usuario que responde",
     *                     example="Jeison Cisneros"
     *                 ),
     *                 @OA\Property(
     *                     property="date",
     *                     type="string",
     *                     format="date-time",
     *                     description="Fecha de la respuesta",
     *                     example="2025-05-19T14:15:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="canal",
     *                     type="string",
     *                     description="Canal por el que se responde",
     *                     example="Correo Electrónico"
     *                 ),
     *                 @OA\Property(
     *                     property="user_type",
     *                     type="string",
     *                     description="Tipo de usuario que responde",
     *                     example="Secretaria De Salud Departamental"
     *                 ),
     *                 @OA\Property(
     *                     property="file",
     *                     type="file",
     *                     description="Documento PDF obligatorio con la respuesta"
     *                 ),
     *                 @OA\Property(
     *                     property="files_adjunt",
     *                     type="array",
     *                     description="Archivos de evidencias opcionales",
     *                     @OA\Items(
     *                         type="file"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta exitosa con el PQRS actualizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pqrs")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación de datos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function responsePqrsToClient(Request $request){
        $data = $request->all();
        $file = $request->file('file') ?? null;
        $adjuntos = $request->file('files_adjunt');
        $response=$this->pqrService->saveAnswerToUser($file,$data,$adjuntos);
        return response()->json($response,200);

    }
    /**
     * @OA\Post(
     *     path="/pqrs/{pqr_id}/close",
     *     summary="Cerrar PQRS",
     *     description="Establece el PQRS identificado por su ID en estado 'cerrado'. Retorna la información actualizada del PQRS.",
     *     tags={"Pqrs"},
     *     @OA\Parameter(
     *         name="pqr_id",
     *         in="path",
     *         required=true,
     *         description="ID del PQRS a cerrar",
     *         @OA\Schema(type="integer", example=18)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PQRS cerrado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pqrs")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="PQRS no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="PQRS not found")
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


    public function closePqrs($pqrs_id){
        $response=$this->pqrService->closePqrs($pqrs_id);
        return response()->json($response,200);

    }
}
