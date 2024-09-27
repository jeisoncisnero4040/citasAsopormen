<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentralOfficeController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::get('me', [AuthController::class, 'me']);

Route::post('recover_password', [UserController::class, 'recoverPassword']);
Route::post('update_password',[UserController::class,'updatePasswordByUserCedula'])->middleware('login.check');
Route::get('encriptar_passwords',[UserController::class,'encryptPAsswords']);
//Route::get('{cp}/',[UserController::class,'f']);

Route::get('get_profesionals/{string}',[ProfesionalController::class,'getAllProfesionalByStringSearch'])
    ->middleware('login.check');
    
Route::get('get_profesional_calendar/{cedula}',[ProfesionalController::class,'getProfesionalCalendarByCedula'])
    ->middleware('login.check');

Route::get('get_clients/{string}',[ClientController::class,'getAllClientByStringSearch'])->middleware('login.check');
Route::post('client_info',[ClientController::class,'showDataClientByIdHistory'])->middleware('login.check');
Route::get('clients/get_authorizations/{clientCode}',[ClientController::class,'getAuthorizationByClientCode'])->middleware('login.check');
Route::get('clients/get_authorization_data/{authorizationCode}',[ClientController::class,'getDataFromAuthorization'])->middleware('login.check');

Route::get('get_centrals_office',[CentralOfficeController::class,'getCentralsOffice']);

Route::get('get_procedures',[ProcedureController::class,'getAllProcedures']);
Route::get('get_procedures/{string}',[ProcedureController::class,'searchProceduresByString']);


Route::post('citas/create_citas',[CitasController::class, 'createGroupCitas']);
Route::get('citas/get_num_citas/{authorization}/{procedim}',[CitasController::class,'GetNumCitasFromOrder'])->middleware('login.check');
Route::post('citas/get_citas_client',[CitasController::class,'GetCalendarClient'])->middleware('login.check');
Route::post('citas/get_citas_profesional',[CitasController::class,'getCalendarProfesional']);
Route::delete('citas/{id}', [CitasController::class, 'deleteCitaById'])->where('id', '\d+')->middleware('login.check');
Route::post('citas/delete_all_citas',[CitasController::class, 'deleteAllCitasByProfesionalDay'])->middleware('login.check');
Route::get('citas/{id}',[CitasController::class, 'getCitaById'])->where('id', '\d+')->middleware('login.check');
Route::post('citas/cancel_cita',[CitasController::class, 'cancelCita'])->where('id', '\d+')->middleware('login.check');
