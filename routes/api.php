<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentralOfficeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
 

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::get('me', [AuthController::class, 'me']);

Route::post('recover_password', [UserController::class, 'recoverPassword']);
Route::post('update_password',[UserController::class,'updatePasswordByUserCedula']);
//Route::get('{cp}/',[UserController::class,'f']);

Route::get('get_profesionals/{string}/',[ProfesionalController::class,'getAllProfesionalByStringSearch']);
Route::get('get_profesional_calendar/{cedula}/',[ProfesionalController::class,'getProfesionalCalendarByCedula']);

Route::get('get_clients/{string}/',[ClientController::class,'getAllClientByStringSearch']);
Route::post('client_info',[ClientController::class,'showDataClientByIdHistory']);

Route::get('get_centrals_office',[CentralOfficeController::class,'getCentralsOffice']);

Route::get('get_procedures',[ProcedureController::class,'getAllProcedures']);
 




 


