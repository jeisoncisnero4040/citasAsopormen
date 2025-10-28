<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Services\WhatsappService;

class CheckExpiringChats extends Command
{
    protected $signature = 'chats:check-expiring';
    protected $description = 'Verifica los chats que están a punto de expirar y ejecuta una acción';

    private $whatsappService;

    public function __construct(WhatsappService $whatsappService) {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    public function handle() {
        $keys = Redis::keys("chat:*");

        foreach ($keys as $key) {
            $numberCel=str_replace("laravel_database_chat:","",$key);
            $chatKey="chat:$numberCel";
            $ttl = Redis::ttl($chatKey);  
            $chat = json_decode(Redis::get($chatKey), true);

            if ($ttl > 0 && $ttl <= 120 && (!isset($chat['alerted']) || !$chat['alerted'])) {  
                $this->whatsappService->sendMessage("hola estas ahí, este chat se cerrara pronto", "whatsapp:+57$numberCel"); 
    
                 
                $chat['alerted'] = true;
                Redis::setex($chatKey, $ttl, json_encode($chat));
            }
        }
    }
    
}

