<?php

use App\Http\Controllers\AuditContoller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentralOfficeController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ObservaCitasController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaseOrdersController;

Route::post('login', [AuthController::class, 'login']);
Route::post('login_client', [AuthController::class, 'loginClient']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::get('me', [AuthController::class, 'me']);
Route::get('update', [AuthController::class, 'update']);

Route::post('recover_password', [UserController::class, 'recoverPassword']);
Route::post('update_password',[UserController::class,'updatePasswordByUserCedula'])->middleware('login.check');
Route::get('encriptar_passwords',[UserController::class,'encryptPAsswords']);


Route::get('get_profesionals/{string}',[ProfesionalController::class,'getAllProfesionalByStringSearch'])
    ->middleware('login.check');
    
Route::get('get_profesional_calendar/{cedula}',[ProfesionalController::class,'getProfesionalCalendarByCedula'])
    ->middleware('login.check');

Route::get('get_clients/{string}',[ClientController::class,'getAllClientByStringSearch'])->middleware('login.check');
Route::post('client_info',[ClientController::class,'showDataClientByIdHistory'])->middleware('login.check');
Route::get('clients/get_authorizations/{clientCode}',[ClientController::class,'getAuthorizationByClientCode'])->middleware('login.check');
Route::get('clients/get_authorization_data/{authorizationCode}',[ClientController::class,'getDataFromAuthorization'])->middleware('login.check');
Route::post('clients/update_password',[ClientController::class,'UpdatePasswordClient']);
Route::post('clients/request_password',[ClientController::class,'GenerateNewPasswordClient']);
Route::post('clients/update',[ClientController::class,'UpdateClient']);
Route::get('clients/history_chat_bot/{codigo}',[ClientController::class,'getHistoryClient']);
Route::get('clients/get_clients_by_cel/{celNumber}',[ClientController::class,'getClientsByNumber']);



Route::get('get_centrals_office',[CentralOfficeController::class,'getCentralsOffice']);

Route::get('get_procedures',[ProcedureController::class,'getAllProcedures']);
Route::get('get_procedures/{string}',[ProcedureController::class,'searchProceduresByString']);


Route::post('citas/create_citas',[CitasController::class, 'createGroupCitas']);
Route::get('citas/get_num_citas/{authorization}/{procedim}/{cod_client}',[CitasController::class,'GetNumCitasFromOrder']);
Route::post('citas/get_citas_client',[CitasController::class,'GetCalendarClient'])->middleware('login.check');
Route::post('citas/get_citas_profesional',[CitasController::class,'getCalendarProfesional'])->middleware('login.check');
Route::delete('citas/{id}', [CitasController::class, 'deleteCitaById'])->where('id', '\d+')->middleware('login.check');
Route::post('citas/delete_all_citas',[CitasController::class, 'deleteAllCitasByProfesionalDay'])->middleware('login.check');
Route::get('citas/{id}',[CitasController::class, 'getCitaById'])->where('id', '\d+');
Route::post('citas/cancel_cita',[CitasController::class, 'cancelCita'])->middleware('login.check');
Route::post('citas/confirm_all_sessions_cita',[CitasController::class, 'confirmateCitaBySessionIds']);
Route::get('citas/get_citas_canceled',[CitasController::class,'GetAllCitasCanceled']);
Route::post('citas/cancel_all_sessions_cita',[CitasController::class, 'CancelCitaBySessionsIds']);
Route::post('citas/Unactivate_cita_canceled',[CitasController::class, 'unactivateCita']);
Route::post('citas/change_profesional',[CitasController::class,'ChangeProfesionalCitas']);
Route::get('citas/get_citas_client/{clientCode}',[CitasController::class, 'GetCitasClient']);
Route::get('citas/get_citas_client_history/{clientCode}',[CitasController::class, 'GetHistoryCitasClientByCode']);
Route::post('citas/notify_order',[CitasController::class,'NotifyOrderProgramed']);
Route::get('observa_citas',[ObservaCitasController::class,'getAllObservaCitas']);
Route::get('observation/get_observation/{name}', [ObservaCitasController::class, 'getContentObservation']);
Route::post('citas/restart/{id}', [CitasController::class, 'restartCitaById'])->where('id', '\d+')->middleware('login.check');

Route::post('case/new', [CaseOrdersController::class, 'create']);
Route::get('case/all', [CaseOrdersController::class, 'getAllCasosAvaiables']);
Route::get('case/{id}', [CaseOrdersController::class, 'getById'])->where('id', '\d+');
Route::post('case/accept', [CaseOrdersController::class, 'acceptCase']);
Route::post('case/reject', [CaseOrdersController::class, 'rejectCase']);
Route::post('case/close', [CaseOrdersController::class, 'closeCase']);
Route::post('case/search', [CaseOrdersController::class, 'searchCitasClient']);

Route::get('audit/search/{param}',[AuditContoller::class, 'searchAudit']);
