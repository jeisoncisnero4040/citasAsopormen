<?php

namespace App\Http\Controllers;
use App\Services\ChatsService;
use App\Dtos\MessageDto;
use Illuminate\Http\Request;


class ChatsController extends Controller
{
    private ChatsService $chatsService;

    public function __construct(ChatsService $chatsService)
    {
        $this->chatsService = $chatsService;
    }

    public function receiveMessage(Request $request)
    {
        $messageDto = MessageDto::fromArray($request->all());
        $response=$this->chatsService->handleMessage($messageDto);
        return response()->json(['status' => 'success', 'data' => $response]);
    }
    public function getChats(Request $request)
    {
        $limit = $request->query('limit', 50);
        $offset = $request->query('offset', 0);
        $chats = $this->chatsService->getAllChats($limit, $offset);
        return response()->json(['status' => 'success', 'data' => $chats]);
    }
    public function dropAllChats()
    {
        $this->chatsService->dropAllChats();
        return response()->json(['status' => 'success', 'message' => 'All chats dropped']);
    }
}