<?php

namespace App\Http\Controllers;

use App\Dtos\CreateCitasDto;
use App\Dtos\DeleteAppoDto;
use Illuminate\Http\Request;
use App\Services\CitasService;
use App\Models\UserRequesting;
use App\Dtos\CloneCalendarDto;

class CitasController extends Controller
{
    private $citasService;
    
    public function __construct(CitasService $citasService)
    {
        $this->citasService=$citasService;
    }
    /**
     * @OA\Post(
     *     path="/citas/create_citas",
     *     tags={"Citas"},
     *     summary="Crear un grupo de citas",
     *     description="Crea un grupo de citas con los datos del usuario, del profesional, de la sede y otros detalles relacionados a la cita. Retorna el número de citas creadas exitosamente.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ced_usu", "registro", "cedprof", "sede", "direccion_cita", "nro_hist", "codent", "codent2", "n_autoriza", "procedim", "tiempo", "procedipro", "duration_session", "start_date", "week_days", "num_citas", "num_sessions", "all_sessions", "saved_sessions"},
     *             @OA\Property(property="ced_usu", type="string", example="1098707063"),
     *             @OA\Property(property="registro", type="string", example="Sergio Avila"),
     *             @OA\Property(property="cedprof", type="string", example="1029384756"),
     *             @OA\Property(property="sede", type="string", example="001"),
     *             @OA\Property(property="direccion_cita", type="string", example="Calle 123 #45-67, Ciudad"),
     *             @OA\Property(property="nro_hist", type="string", example="123456"),
     *             @OA\Property(property="codent", type="string", example="ABC001"),
     *             @OA\Property(property="codent2", type="string", example="CONV001"),
     *             @OA\Property(property="n_autoriza", type="string", example="AUT123456"),
     *             @OA\Property(property="procedim", type="string", example="Procedimiento A"),
     *             @OA\Property(property="tiempo", type="string", example="pro028"),
     *             @OA\Property(property="procedipro", type="string", example="Terapia Física"),
     *             @OA\Property(property="recordatorio_wsp", type="boolean", example=true, description="Opcional"),
     *             @OA\Property(property="duration_session", type="integer", example=45),
     *             @OA\Property(property="regobserva", type="string", example="Observaciones de la cita", description="Opcional"),
     *             @OA\Property(property="start_date", type="string", format="date-time", example="2024-09-10T15:30:00Z"),
     *             @OA\Property(property="week_days", type="array", @OA\Items(type="string"), example={"Lunes", "Miércoles"}),
     *             @OA\Property(property="num_citas", type="integer", example=5),
     *             @OA\Property(property="num_sessions", type="integer", example=10),
     *             @OA\Property(property="all_sessions", type="integer", example=20),
     *             @OA\Property(property="saved_sessions", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cita creada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="created"),
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="data", type="integer", example=50, description="Número de citas creadas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="error", type="string", example="Datos incorrectos proporcionados"),
     *             @OA\Property(property="status", type="integer", example=400),
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

    public function createGroupCitas(Request $request){
        $dto=CreateCitasDto::fromRequest(request:$request);
        $response=$this->citasService->create(dto:$dto);
        return response()->json($response,201);
    }

    /**
     * @OA\Delete(
     *     path="/citas/{id}",
     *     tags={"Citas"},
     *     summary="Eliminar una cita por su ID",
     *     description="Elimina una cita específica por su ID si esta no ha sido marcada como cancelada, asistida o no asistida.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la cita",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=123
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cita eliminada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="cita con id 123 was deleted successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No es posible eliminar la cita",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="error", type="string", example="no es posible eliminar esta sección"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cita no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Cita no encontrada"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
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
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *         )
     *     )
     * )
     */

    public function deleteCitaById(Request $request){
        $queryParams = $request->query();
        $dto=DeleteAppoDto::fromRequest($queryParams);
        $dto->setUserRequest($this->user());
        $response = $this->citasService->deleteCitaById(dto:$dto);
        return response()->json($response,200);
    }

    /**
     * @OA\Get(
     *     path="/citas/{id}",
     *     tags={"Citas"},
     *     summary="Obtener detalles de una cita por ID",
     *     description="Recupera los detalles de una cita específica utilizando su ID. La respuesta incluye información como fecha, hora, procedimiento, observaciones, y más.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la cita",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=123
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la cita recuperados exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="fecha", type="string", format="date", example="2024-09-10"),
     *                 @OA\Property(property="hora", type="string", format="time", example="15:00"),
     *                 @OA\Property(property="hora_asignacion", type="string", format="date-time", example="2024-09-10T14:00:00Z"),
     *                 @OA\Property(property="procedimiento", type="string", example="Consulta General"),
     *                 @OA\Property(property="observaciones", type="string", example="Llevar cedula y orden"),
     *                 @OA\Property(property="asistida", type="boolean", example=true),
     *                 @OA\Property(property="cancelada", type="boolean", example=false),
     *                 @OA\Property(property="no_asistida", type="boolean", example=false),
     *                 @OA\Property(property="orden", type="integer", example=1),
     *                 @OA\Property(property="duracion", type="string", example="60"),
     *                 @OA\Property(property="direccion", type="string", example="Calle 123 #45-67, Ciudad"),
     *                 @OA\Property(property="usuario", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="profesional", type="string", example="Dr. María López")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cita no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cita no encontrada"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
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
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *         )
     *     )
     * )
 */

    public function getCitaById(Request $request){
        $data=$request->query();
        $cita=$this->citasService->getCitasById(id:(int)$data['id']);
        return response() ->json($cita,200);
    }

    /**
     * @OA\Post(
     *     path="/api/citas/confirm_all_sessions_cita",
     *     summary="Confirmar múltiples sesiones de citas",
     *     tags={"Citas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(
     *                 property="ids",
     *                 type="string",
     *                 example="123|||456|||789",
     *                 description="IDs de las citas separadas por '|||'"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sesiones confirmadas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=3, description="Número de citas confirmadas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta - Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="error", type="object", example={"ids": {"El campo ids es obligatorio."}}),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Citas no encontradas o no confirmables",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="No es posible confirmar estas citas"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error en la base de datos"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function confirmateCitaBySessionIds(Request $request){
        
        $citas=$this->citasService->corfirmateGroupSessions($request->all());
        return response()->json($citas,200);
    }
    /**
     * @OA\Get(
     *     path="/api/citas/get_citas_canceled",
     *     summary="Obtener todas las citas canceladas",
     *     tags={"Citas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de citas canceladas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="cantidad", type="integer", example=3, description="Número de sesiones canceladas"),
     *                 @OA\Property(property="reasignadas", type="integer", example=1, description="Número de sesiones reasignadas"),
     *                 @OA\Property(property="ids", type="string", example="12|||45|||78", description="IDs de las sesiones canceladas"),
     *                 @OA\Property(property="fecha", type="string", format="date", example="2025-03-10", description="Fecha de cancelación"),
     *                 @OA\Property(property="direccion_cita", type="string", example="Carrera 15 #10-20"),
     *                 @OA\Property(property="nro_hist", type="string", example="123456"),
     *                 @OA\Property(property="codent", type="string", example="EPS001"),
     *                 @OA\Property(property="codent2", type="string", example="EPS002"),
     *                 @OA\Property(property="autoriz", type="string", example="Autorizado"),
     *                 @OA\Property(property="procedim", type="string", example="Consulta General"),
     *                 @OA\Property(property="tiempo", type="string", example="30 minutos"),
     *                 @OA\Property(property="procedimiento", type="string", example="Consulta Externa"),
     *                 @OA\Property(property="razon", type="string", example="Paciente no asistió"),
     *                 @OA\Property(property="cod_sede", type="string", example="S001"),
     *                 @OA\Property(property="copago", type="number", format="float", example=5000.00),
     *                 @OA\Property(property="medio_cancelacion", type="string", example="Teléfono"),
     *                 @OA\Property(property="recordatorio_whatsapp", type="boolean", example=true),
     *                 @OA\Property(property="duracion", type="string", example="45 minutos"),
     *                 @OA\Property(property="sede", type="string", example="Sede Central"),
     *                 @OA\Property(property="profesional", type="string", example="Dr. Juan Pérez"),
     *                 @OA\Property(property="cliente", type="string", example="Ana Gómez"),
     *                 @OA\Property(property="celular", type="string", example="3001234567"),
     *                 @OA\Property(property="nombre_plantilla_observacion", type="string", example="Observación General")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No hay citas canceladas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="no hay citas canceladas"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error en la base de datos"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function GetAllCitasCanceled(){
        $citasCanceled=$this->citasService->getAllCitasCanceled();
        return response()->json($citasCanceled,200);
    }
    /**
     * @OA\Post(
     *     path="/api/citas/cancel_all_sessions_cita",
     *     summary="Cancelar múltiples sesiones de citas",
     *     tags={"Citas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids", "razon", "fecha_cita", "meanCancel"},
     *             @OA\Property(property="ids", type="string", example="12|||45|||78", description="IDs de las sesiones a cancelar"),
     *             @OA\Property(property="razon", type="string", example="Paciente no asistió", description="Motivo de la cancelación"),
     *             @OA\Property(property="fecha_cita", type="string", format="date-time", example="2025-03-15 14:30", description="Fecha de la cita cancelada"),
     *             @OA\Property(property="meanCancel", type="string", example="Llamada telefónica", description="Medio por el cual se cancela la cita")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Citas canceladas con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=3, description="Número de citas canceladas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de los datos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="object", example={"ids": "El campo ids es obligatorio"}),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al cancelar citas"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function CancelCitaBySessionsIds(Request $request){
        $dataCitasCanceled=$this->citasService->CancelGroupSsessions($request->all(),$this->user());
        return response()->json($dataCitasCanceled,200);
    }


    /**
     * @OA\Post(
     *     path="/api/citas/Unactivate_cita_canceled",
     *     summary="Desactivar una cita cancelada",
     *     tags={"Citas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=123, description="ID de la cita cancelada a desactivar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cita cancelada desactivada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=1, description="Número de registros actualizados")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="ID de la cita es requerido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Cita's ID is required"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cita cancelada no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="cita canceled not found"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al desactivar la cita cancelada"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function unactivateCita(Request $request){
        $dataCitaUnactivate=$this->citasService->unactivateCitaCanceledById($request->all());
        return response()->json($dataCitaUnactivate,200);
    }
    /**
     * @OA\Post(
     *     path="/api/citas/change_profesional",
     *     summary="Cambiar el profesional asignado a citas específicas",
     *     tags={"Citas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids", "cedprof"},
     *             @OA\Property(
     *                 property="ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={101, 102, 103},
     *                 description="Lista de IDs de citas a actualizar"
     *             ),
     *             @OA\Property(
     *                 property="cedprof",
     *                 type="string",
     *                 example="12345678",
     *                 description="Cédula del nuevo profesional asignado"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profesional cambiado correctamente en las citas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=3, description="Número de citas actualizadas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en los datos enviados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="The ids field is required."),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al cambiar profesional en citas"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */


    public Function ChangeProfesionalCitas(Request $request){
        $citasChangedProfesional=$this->citasService->ChangeProfesionalToCitaIdsGroup($request->all());
        return response()->json($citasChangedProfesional,200);
    }
    /**
     * @OA\Get(
     *     path="/api/citas/get_citas_client/{clientCode}",
     *     summary="Obtener las citas de un cliente específico",
     *     tags={"Citas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="clientCode",
     *         in="path",
     *         required=true,
     *         description="Código del cliente",
     *         @OA\Schema(type="string", example="12345")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de citas agrupadas por sesión",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="fecha", type="string", format="date", example="2025-03-15"),
     *                     @OA\Property(property="start", type="string", format="date-time", example="2025-03-15T09:00:00Z"),
     *                     @OA\Property(property="end", type="string", format="date-time", example="2025-03-15T09:30:00Z"),
     *                     @OA\Property(property="procedimiento", type="string", example="Terapia Física"),
     *                     @OA\Property(property="profesional", type="string", example="Dr. Juan Pérez"),
     *                     @OA\Property(property="autorizacion", type="string", example="A123456"),
     *                     @OA\Property(property="observaciones", type="string", example="Paciente debe traer informe médico"),
     *                     @OA\Property(property="asistida", type="boolean", example=false),
     *                     @OA\Property(property="cancelada", type="boolean", example=false),
     *                     @OA\Property(property="no_asistida", type="boolean", example=false)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Código de cliente inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="El código de cliente debe ser válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al obtener citas del cliente"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function GetCitasClient(string $clientCode){
        $resposne=$this->citasService->getCitasClient($clientCode);
        return response()->json($resposne,200);
   }   

    /**
     * @OA\Get(
     *     path="/api/citas/get_citas_client_history/{clientCode}",
     *     summary="Obtener el historial de citas de un cliente a partir del primero de enero de 2025",
     *     tags={"Citas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="clientCode",
     *         in="path",
     *         required=true,
     *         description="Código único del cliente",
     *         @OA\Schema(type="string", example="C12345")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historial de citas obtenido con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="fecha", type="string", format="date", example="2025-03-10"),
     *                     @OA\Property(property="hora", type="string", example="14:30"),
     *                     @OA\Property(property="autorizacion", type="string", example="A98765"),
     *                     @OA\Property(property="procedimiento", type="string", example="Terapia Física"),
     *                     @OA\Property(property="observaciones", type="string", example="Ninguna"),
     *                     @OA\Property(property="asistida", type="boolean", example=true),
     *                     @OA\Property(property="cancelada", type="boolean", example=false),
     *                     @OA\Property(property="tiempo", type="string", example="ORD123"),
     *                     @OA\Property(property="direccion", type="string", example="Cra 10 #23-45"),
     *                     @OA\Property(property="no_asistida", type="boolean", example=false),
     *                     @OA\Property(property="duracion", type="integer", example=60),
     *                     @OA\Property(property="profesional", type="string", example="Dr. Juan Pérez")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Código de cliente inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="El código de cliente debe ser válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error en la consulta de citas"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function GetHistoryCitasClientByCode($clientCode){
        $response=$this->citasService->getHistoryCitasClient($clientCode);
        return response()->json($response,200);
    }

    public function cloneCalendarProfesional(Request $request){
        $data= $request->all();
        $userRequesting=$request->attributes->get('userPayload', []);
        $dto=CloneCalendarDto::fromArray(data: $data, dataUserRequest: $userRequesting);
        $response=$this->citasService->cloneScheduleProfesional($dto);
        return response()->json($response,200);
    }

    public function deleteCalendarProfesional(Request $request){
        $response=$this->citasService->deleteScheduleProfesional($request->query());
        return response()->json($response,200);
    }
    
    

}
