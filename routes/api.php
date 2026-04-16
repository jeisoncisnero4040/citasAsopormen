<?php

use App\Http\Controllers\AuditContoller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthsController;
use App\Http\Controllers\AuthsDocumentsController;
use App\Http\Controllers\CentralOfficeController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ObservaCitasController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaseOrdersController;
use App\Http\Controllers\ExternalProcedureController;
use App\Http\Controllers\InformesController;
use App\Http\Controllers\ProfesionalSenderController;
use App\Http\Controllers\PrometheusController;
use App\Http\Controllers\KafkaController;
use App\Http\Controllers\AuditController;


Route::get('kafka/publish', [KafkaController::class, 'publish']);

Route::post('login', [AuthController::class, 'login']);
Route::post('login_client', [AuthController::class, 'loginClient']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::get('me', [AuthController::class, 'me']);
Route::get('update', [AuthController::class, 'update']);

Route::post('recover_password', [UserController::class, 'recoverPassword']);
Route::post('update_password',[UserController::class,'updatePasswordByUserCedula']);
Route::get('encriptar_passwords',[UserController::class,'encryptPAsswords']);


Route::get('get_profesionals',[ProfesionalController::class,'getAllProfesionalByStringSearch']);

    
Route::get('get_profesional_calendar/{cedula}',[ProfesionalController::class,'getProfesionalCalendarByCedula']);

Route::get('profesionals/get-calendar',[ProfesionalController::class,'getCalendarProByCCinRangeTime']);
Route::get('profesionals/get-procedims',[ProfesionalController::class,'getProfesionalsProcedims']);
Route::get('profesionals/get-schedule',[ProfesionalController::class,'getSchedule']);

Route::post('clients',[ClientController::class,'store']);
Route::post('clients/update',[ClientController::class,'updateClient']);
Route::get('get_clients',[ClientController::class,'getAllClientByStringSearch']);
Route::patch('clients',[ClientController::class,'toggleActive']);
Route::get('client_info',[ClientController::class,'showDataClientByIdHistory']);
Route::get('clients/get_authorizations',[ClientController::class,'getAuthorizationByClientCode']);
Route::get('clients/get_authorization_data/{authorizationCode}',[ClientController::class,'getDataFromAuthorization']);
Route::post('clients/update_password',[ClientController::class,'UpdatePasswordClient']);
Route::post('clients/request_password',[ClientController::class,'GenerateNewPasswordClient']);
Route::post('clients/update/{codigo}',[ClientController::class,'Update']);
Route::get('clients/history_chat_bot/{codigo}',[ClientController::class,'getHistoryClient']);
Route::get('clients/get_clients_by_cel/{celNumber}',[ClientController::class,'getClientsByNumber']);
Route::get('clients/get-calendar',[ClientController::class,'getCalendarClientByCodInRangeTime']);
Route::get('client/get-full-info',[ClientController::class,'getFullInfoClient']);
Route::get('client/get-utility',[ClientController::class,'getUtility']);



Route::get('get_centrals_office',[CentralOfficeController::class,'getCentralsOffice']);

Route::get('get_procedures',[ProcedureController::class,'getAllProcedures']);
Route::get('get_procedures/{string}',[ProcedureController::class,'searchProceduresByString']);
Route::get('procedures',[ProcedureController::class,'find']);


Route::post('citas',[CitasController::class, 'createGroupCitas'])->middleware('login.check:agenda');
Route::delete('citas', [CitasController::class, 'deleteCitaById'])->middleware('login.check:agenda');
Route::get('citas',[CitasController::class, 'getCitaById'])->middleware('login.check:agenda');
Route::post('citas/cancel_cita',[CitasController::class, 'cancelCita']);
Route::post('citas/confirm_all_sessions_cita',[CitasController::class, 'confirmateCitaBySessionIds']);
Route::get('citas/get_citas_canceled',[CitasController::class,'GetAllCitasCanceled']);
Route::post('citas/cancel_all_sessions_cita',[CitasController::class, 'CancelCitaBySessionsIds']);
Route::post('citas/Unactivate_cita_canceled',[CitasController::class, 'unactivateCita']);
Route::post('citas/change_profesional',[CitasController::class,'ChangeProfesionalCitas'])->middleware('login.check:reasignar-citas');
Route::get('citas/get_citas_client/{clientCode}',[CitasController::class, 'GetCitasClient']);
Route::get('citas/get_citas_client_history/{clientCode}',[CitasController::class, 'GetHistoryCitasClientByCode']);
Route::post('citas/clone-calendar',[CitasController::class,'cloneCalendarProfesional'])->middleware('login.check:replicar-citas');
Route::delete('citas/delete-calendar',[CitasController::class,'deleteCalendarProfesional'])->middleware('login.check:retirar-citas');;


Route::get('observa_citas',[ObservaCitasController::class,'getAllObservaCitas'])->middleware('login.check:agenda');
Route::get('observation/get_observation/{name}', [ObservaCitasController::class, 'getContentObservation'])->middleware('login.check:agenda');


Route::post('case/new', [CaseOrdersController::class, 'create']);
Route::get('case/all', [CaseOrdersController::class, 'getAllCasosAvaiables']);
Route::get('case/{id}', [CaseOrdersController::class, 'getById']);
Route::post('case/accept', [CaseOrdersController::class, 'acceptCase']);
Route::post('case/reject', [CaseOrdersController::class, 'rejectCase']);
Route::post('case/close', [CaseOrdersController::class, 'closeCase']);
Route::post('case/search', [CaseOrdersController::class, 'searchCitasClient']);


Route::get('audit',[AuditController::class, 'index']);


Route::get('informes/new-clients',[InformesController::class,'getNewClientsInforme'])->middleware('login.check:informes-citas');
Route::get('informes/clients-appoiments-not-foud',[InformesController::class,'getClientsAppoimentsNotFound'])->middleware('login.check:informes-citas');
Route::get('informes/appoiments-by-entity',[InformesController::class,'countAppimentsEntity'])->middleware('login.check:informes-citas');
Route::get('informes/new-clients-by-procedure',[InformesController::class,'countNewsClientsByProcedure'])->middleware('login.check:informes-citas');
Route::get('informes/old-users',[InformesController::class,'getOldUser'])->middleware('login.check:informes-citas');
Route::get('informes/old-users-not-citas',[InformesController::class,'getOldUserNotFountCitad'])->middleware('login.check:informes-citas');

Route::post('auths',[AuthsController::class,'store']);
Route::get('metrics',[PrometheusController::class,'metrics']);
Route::get('auths',[AuthsController::class,'index']);
Route::get('auths/detail',[AuthsController::class,'getDetail']);


Route::get('auths/documents',[AuthsDocumentsController::class,'index']);
Route::post('auths/documents',[AuthsDocumentsController::class,'store']);
Route::delete('auths/documents/{id}',[AuthsDocumentsController::class,'destroy']);
Route::get('auths/documents/utility',[AuthsDocumentsController::class,'getUtility']);


Route::get('external-procedures',[ExternalProcedureController::class,'index']);

Route::get('profesional-senders',[ProfesionalSenderController::class,'index']);






