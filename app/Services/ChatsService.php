<?php
namespace App\Services;

use App\Utils\ResponseManager;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Events\MessageInterativeEvent;

class ChatsService {

    private $responseManager;

    public function __construct(ResponseManager $responseManager) {
        $this->responseManager = $responseManager;
    }

    public function addChat($numberCel) {
        $chatKey = "chat:$numberCel";
        $data = json_encode(['status' => 0.0]);

        try {
            Redis::setex($chatKey, 3600, $data);
            return $this->responseManager->success("Chat registrado exitosamente.");
        } catch (\Exception $e) {
            throw new ServerErrorException("Error al registrar el chat: " . $e->getMessage(), 500);
        }
    }

    public function getChatByNumCel($numberCel) {
        $chatKey = "chat:$numberCel";
        $chat = json_decode(Redis::get($chatKey), true);

        if (empty($chat)) {
            return $this->responseManager->notFound('chat not found');
        }

        return $this->responseManager->success($chat);
    }

    public function getAllChats() {
        try {
            $keys = Redis::keys("chat:*");
            $chats = [];
            foreach ($keys as $key) {
                $numberCel=str_replace("laravel_database_chat:","",$key);
                $chats[$numberCel] = json_decode(Redis::get("chat:$numberCel"), true);
            }

            return $this->responseManager->success($chats);
        } catch (\Exception $e) {
            throw new ServerErrorException("Error al obtener todos los chats: " . $e->getMessage(), 500);
        }
    }

    public function addKeysToChat($numberCel, array $newData) {
        $chatKey = "chat:$numberCel";
        $chat = json_decode(Redis::get($chatKey), true);

        if (empty($chat)) {
            throw new NotFoundException("Chat no encontrado", 404);
        }

        $updatedChat = array_merge($chat, $newData);
        Redis::setex($chatKey, 600, json_encode($updatedChat));

        return $this->responseManager->success("Datos añadidos al chat.");
    }
    public function removeKeyToChat($numberCel, string $oldKey) {
        $chatKey = "chat:$numberCel";
        $chat = json_decode(Redis::get($chatKey), true);
    
        if (empty($chat)) {
            throw new NotFoundException("Chat no encontrado", 404);
        }
    
        if (isset($chat[$oldKey])) { 
            unset($chat[$oldKey]);
        } 

        Redis::setex($chatKey, 600, json_encode($chat));
    
        return $this->responseManager->success("Clave '$oldKey' eliminada del chat.");
    }
    

    public function updateChatStatus($numberCel, $newStatus) {
        $chatKey = "chat:$numberCel";
        $chat = json_decode(Redis::get($chatKey), true);

        if (empty($chat)) {
            throw new NotFoundException("Chat no encontrado", 404);
        }

        $chat['status'] = $newStatus;
        Redis::setex($chatKey, 600, json_encode($chat));
        return $this->responseManager->success("Estado del chat actualizado.");
    }

    public function resetTTL($numberCel) {
        $chatKey = "chat:$numberCel";
        $chat = json_decode(Redis::get($chatKey), true);
    
        if (empty($chat)) {
            throw new NotFoundException("Chat no encontrado", 404);
        }
        $chat['alerted'] = false;
        Redis::setex($chatKey, 600, json_encode($chat));
        return $this->responseManager->success("TTL del chat reseteado a 600 segundos.");
    }
    
    public function deleteChatByCelNumber($numberCel){
        $chatKey = "chat:$numberCel";
        Redis::del($chatKey);
        return $this->responseManager->delete('chat');
    }
    public function updateKeyInChat($numberCel, string $key, $newValue) {
        $chatKey = "chat:$numberCel";
        $chat = json_decode(Redis::get($chatKey), true);
    
        if (empty($chat)) {
            throw new NotFoundException("Chat no encontrado", 404);
        }
    
        $chat[$key] = $newValue;
        Redis::setex($chatKey, 600, json_encode($chat));
    
        return $this->responseManager->success("Clave '$key' actualizada en el chat.");
    }
}
