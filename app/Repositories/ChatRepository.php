<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Redis;
use App\Domain\Chat;
use App\Ports\ChatRepositoryPort;
use App\Config\ChatConfig;
use App\Mappers\ArrayToChat;

class ChatRepository implements ChatRepositoryPort
{
    private const INDEX_KEY = 'chats:index';

    public function save(Chat $chat): Chat
    {
        $data = json_encode($chat->toArray());
        $id = $chat->getId();
        $phone = $chat->getTelephoneNumber();
        Redis::set("chat:$id", $data, 'EX', ChatConfig::TTL);
        Redis::set("chat:phone:$phone", $id, 'EX', ChatConfig::TTL);

        Redis::sadd(self::INDEX_KEY, $id);
        return $chat;
    }

    public function findById(string $id): ?Chat
    {
        $data = Redis::get("chat:$id");
        if (!$data) return null;
        return $this->arrayToChat(json_decode($data, true));
    }

    public function findByTelephone(string $telephoneNumber): ?Chat
    {
        $id = Redis::get("chat:phone:$telephoneNumber");

        if (!$id) return null;

        return $this->findById($id);
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $ids = Redis::smembers(self::INDEX_KEY);
        $ids = array_slice($ids, $offset, $limit);

        return array_values(array_filter(array_map(function ($id) {
            return $this->findById($id);
        }, $ids)));
    }

    public function delete(string $id): void
    {
        $chat = $this->findById($id);

        if (!$chat) return;

        $phone = $chat->getTelephoneNumber();

        Redis::del("chat:$id");
        Redis::del("chat:phone:$phone");

        Redis::srem(self::INDEX_KEY, $id);
    }

    private function arrayToChat(array $data): Chat
    {
        return ArrayToChat::map($data);
    }
    public function dropAll(): void
    {
        $ids = Redis::smembers(self::INDEX_KEY);
        foreach ($ids as $id) {
            $this->delete($id);
        }
    }
}