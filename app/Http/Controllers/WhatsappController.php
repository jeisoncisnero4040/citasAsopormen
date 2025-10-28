<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsappService;

class WhatsappController extends Controller
{   
    private $whatsappService;

    public function __construct(WhatsappService $whatsappService) {
        $this->whatsappService=$whatsappService;
    }

    public function handleIncomingMessage(Request $request){
        $response=$this->whatsappService->handleClientMessage($request->all());
        return response()->json($response, 200);
    }
}
