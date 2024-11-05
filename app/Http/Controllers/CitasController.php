<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CitasService;
use Illuminate\Support\Facades\DB;


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
        $citas=$this->citasService->createGroupCitas($request->all());
        return response()->json($citas,201);
    }
    /**
     * @OA\Get(
     *     path="/citas/get_num_citas/{authorization}/{procedim}",
     *     tags={"Citas"},
     *     summary="Obtener número de citas por autorización y procedimiento que no han sido canceladas",
     *     description="Obtiene el número de citas registradas para una autorización específica y un procedimiento determinado.",
     *     @OA\Parameter(
     *         name="authorization",
     *         in="path",
     *         description="Número de autorización",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="AUT123456"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="procedim",
     *         in="path",
     *         description="Código o nombre del procedimiento",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Procedimiento A"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Número de citas encontrado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=5, description="Número de citas encontradas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron citas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="No se encontraron citas"),
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
    public function GetNumCitasFromOrder($authorization,$procedim){
        $numCitas=$this->citasService->GetNumCitasFromOrder($authorization,$procedim);
        return response()->json($numCitas,200);
    }

    /**
     * @OA\Post(
     *     path="/citas/get_citas_client",
     *     tags={"Citas"},
     *     summary="Obtener citas de un cliente en un rango de tiempo",
     *     description="Recupera todas las citas de un cliente específico dentro de un rango de tiempo proporcionado.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo", "startDate", "endDate"},
     *             @OA\Property(property="codigo", type="string", example="123456", description="Código del cliente"),
     *             @OA\Property(property="startDate", type="string", format="date", example="2023-09-01", description="Fecha de inicio del rango en formato Y-m-d"),
     *             @OA\Property(property="endDate", type="string", format="date", example="2023-09-30", description="Fecha de fin del rango en formato Y-m-d")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Citas encontradas exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="fecha", type="string", example="2023-09-05"),
     *                     @OA\Property(property="hora", type="string", example="10:00 AM"),
     *                     @OA\Property(property="procedimiento", type="string", example="Consulta médica"),
     *                     @OA\Property(property="sede", type="string", example="Sede Central")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Rango de fechas no válido o faltan parámetros",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="error", type="string", example="El rango de fechas no es válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron citas para el cliente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="No se encontraron citas para el cliente en el rango de tiempo especificado"),
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

    public function GetCalendarClient(Request $request){
        $calendarClient=$this->citasService->getCitasByClientInRangeTime($request->all());
        return response()->json($calendarClient,200);
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

    public function deleteCitaById($id){
        $citaDelete=$this->citasService->deleteCitaById(($id));
        return response()->json($citaDelete,200);
    }
    /**
     * @OA\Post(
     *     path="/citas/delete_all_citas",
     *     tags={"Citas"},
     *     summary="Eliminar todas las citas de un profesional en un día específico",
     *     description="Elimina todas las citas eliminables de un profesional especificado por su cédula en una fecha determinada. La fecha debe estar en formato Y-m-d.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"profesional_identity", "day"},
     *             @OA\Property(property="profesional_identity", type="string", example="1098707063", description="Cédula del profesional"),
     *             @OA\Property(property="day", type="string", format="date", example="2023-09-15", description="Fecha en formato Y-m-d")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Citas eliminadas exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=10, description="Número de citas eliminadas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta o parámetros inválidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="error", type="string", example="Parámetros inválidos o falta de datos"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron citas para eliminar",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="No se encontraron citas para eliminar en la fecha y cédula especificadas"),
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

    public function deleteAllCitasByProfesionalDay(Request $request){
        $citasDleted=$this->citasService->deleteDayCitasProfesional($request->all());
        return response()->json($citasDleted,200);
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

    public function getCitaById($id){
        $cita=$this->citasService->getCitasById($id);
        return response() ->json($cita,200);
    }
    /**
     * @OA\Post(
     *     path="/citas/{id}",
     *     tags={"Citas"},
     *     summary="Cancelar una cita por ID",
     *     description="Cancela una cita específica por su ID si es cancelable. El campo 'data' en la respuesta es null.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la cita a cancelar",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=123
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cita cancelada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cita no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="cita no encontrada"),
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

    public function cancelCita(Request $request){
       
        $cita=$this->citasService->cancelCita($request->all());
        return response() ->json($cita,200);
    }
    public function getCalendarProfesional(Request $request){
        $calendar=$this->citasService->getCitasByProfesionalInRangeTime($request->all());
        return response()->json($calendar,200);
    }
    public function confirmateCitaBySessionIds(Request $request){
        
        $citas=$this->citasService->corfirmateGroupSessions($request->all());
        return response()->json($citas,200);
    }
    public function GetAllCitasCanceled(){
        $citasCanceled=$this->citasService->getAllCitasCanceled();
        return response()->json($citasCanceled,200);
    }
    public function CancelCitaBySessionsIds(Request $request){
        $dataCitasCanceled=$this->citasService->CancelGroupSsessions($request->all());
        return response()->json($dataCitasCanceled,200);
    }
    
    public function unactivateCita(Request $request){
        $dataCitaUnactivate=$this->citasService->unactivateCitaCanceledById($request->all());
        return response()->json($dataCitaUnactivate,200);
    }

}
