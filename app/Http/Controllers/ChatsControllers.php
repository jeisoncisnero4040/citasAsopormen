<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatsService;

class ChatsControllers extends Controller
{
    private $chatsService;
    
    public function __construct(ChatsService $chatsService){
        $this->chatsService=$chatsService;
    }
    public function getAllChats(){
        $response=$this->chatsService->getAllChats();
        return response()->json($response);
    }
    public function getChatByCelNumber($numberCel){
        $response=$this->chatsService->getChatByNumCel($numberCel);
        return response()->json($response);
    }
    public function deleteChatByCelNumber($numberCel){
        $response=$this->chatsService->deleteChatByCelNumber($numberCel);
        return response()->json($response);
    }
    
    public function updateStatus($numberCel,$newStatus){
        $response=$this->chatsService->updateChatStatus($numberCel,$newStatus);
        return response()->json($response);
    }
}
