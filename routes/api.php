<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\ChatsControllers;
use App\Http\Controllers\PrometheusController;

Route::post('whatsapp/handle-incoming-message', [WhatsappController::class, 'handleIncomingMessage']);
Route::get('whatsapp/chats',[ChatsControllers::class,'getAllChats']);
Route::get('whatsapp/chats/{numberCel}',[ChatsControllers::class,'getChatByCelNumber']);
Route::delete('whatsapp/chats/{numberCel}',[ChatsControllers::class,'deleteChatByCelNumber']);
Route::get('whatsapp/chats/update/{numberCel}/{newStatus}',[ChatsControllers::class,'updateStatus']);


Route::get('metrics',[PrometheusController::class,'metrics']);
