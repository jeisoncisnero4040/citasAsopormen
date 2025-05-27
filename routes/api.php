<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PqrController;
use App\Http\Controllers\ReasonPqrController;
use App\Http\Controllers\UtilsPqrControllers;
use App\Http\Controllers\DataAnalitycsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login',[AuthController::class,'login']);
Route::post('change-password',[AuthController::class,'setPasswordUser']);
Route::post('forgot-password',[AuthController::class,'recoverPassword']);

Route::post('pqrs/add',[PqrController::class,'new'])->middleware('login.check');
Route::get('pqrs/{pqr_id}',[PqrController::class,'find']);
Route::get('pqrs',[PqrController::class,'getAll'])->middleware('login.check');
Route::post('pqrs/notify-area',[PqrController::class,'notifyPqrsToArea']);
Route::post('/pqrs/answer-area', [PqrController::class, 'handleAnswerPqrsByArea']);
Route::get('/pqrs/{pqr_id}/actions',[PqrController::class, 'getActionsDataPqrs']);
Route::get('/pqrs/{pqr_id_encoded}/encoded',[PqrController::class, 'getPqrsEncoded']);
Route::post('/pqrs/{pqr_id}/change-area',[PqrController::class, 'changeArea']);
Route::post('/pqrs/answer/client',[PqrController::class, 'responsePqrsToClient']);
Route::POST('/pqrs/{pqr_id}/close', [PqrController::class, 'closePqrs']);



Route::get('reasons',[ReasonPqrController::class,'get']);
Route::get('utils',[UtilsPqrControllers::class,'get']);

Route::get('data/pqrs',[DataAnalitycsController::class,'getDataFromPqrs']);
