<?php

use App\Http\Controllers\CitasController;
use App\Http\Controllers\TwillioController;
use App\Http\Controllers\WhatsappController;

use Illuminate\Support\Facades\Route;

Route::post('whatsapp/start_chat', [WhatsappController::class, 'startChat']);
Route::post('whatsapp/failed', [WhatsappController::class, 'failedMessage']);
Route::post('whatsapp/retrieve_password', [WhatsappController::class, 'SendMesageRetrievePassword']);
Route::post('whatsapp/handle_message_client', [WhatsappController::class, 'handlemessageToClient']);
Route::post('/whatsapp/confirm_programation', [WhatsappController::class, 'sendConfirmationOrderProgramedMessage']);
Route::post('/citas/wait/', [CitasController::class, 'sendCitaToWait']);
Route::get('/whatsapp/history/{number}', [TwillioController::class ,'getHistoryMsm']);
Route::post('/test',[WhatsappController::class, 'test']);
