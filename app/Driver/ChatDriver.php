<?php
namespace App\Driver;

use App\ChatHandlers\LoginUser;
use App\Services\CitasService;
use App\Utils\ResponseManager;
use App\Services\ChatsService;
use App\ChatHandlers\MenuOptions;
use App\ChatHandlers\UploadOrder;
use App\ChatHandlers\SpecialStates;
use App\ChatHandlers\ConsultCitas;
use App\ChatHandlers\CancelCitas;
use App\ChatHandlers\EndChat;
use App\Services\CaseOrderService;
use App\ChatHandlers\ConsultOrders;

class ChatDriver {
    public static function handleChat($status, $numberCel,$text, $mediaUrl, $buttonPayload,$messageType) {
        
        $citasService = new CitasService(new ResponseManager());
        $chatsServices= new ChatsService(new ResponseManager());
        $caseOrderService=new CaseOrderService(new ResponseManager(),new ChatsService(new ResponseManager()));

        switch (true) {  
            case $status >= 0.0 && $status < 1.0:
                $loginUser = new LoginUser($status, $numberCel,$text,$buttonPayload,$mediaUrl,$citasService, $chatsServices);
                return $loginUser->driveLogin();
            case $status >= 1.0 && $status < 2.0:
                $menuDriver=new MenuOptions($status, $numberCel,$text,$buttonPayload,$mediaUrl, $citasService, $chatsServices);
                return $menuDriver->menuDriver();
            case $status >= 2.0 && $status <3.0:
                $uploadOrder=new UploadOrder($status, $numberCel,$text,$buttonPayload,$mediaUrl,$messageType, $citasService, $chatsServices,$caseOrderService);
                return $uploadOrder->uploadOrderDriver();
            case $status >= 3.0 && $status <4.0:
                $ordersConsulter=new ConsultOrders($status, $numberCel,$text,$buttonPayload,$mediaUrl,$messageType, $citasService, $chatsServices,$caseOrderService);
                return $ordersConsulter->consultOrdersDriver();
            case $status >= 4.0 && $status <5.0:
                $citasConsulter=new ConsultCitas($status, $numberCel,$text,$buttonPayload,$mediaUrl, $citasService, $chatsServices);
                return $citasConsulter->consultCitasDriver();
            case $status >= 5.0 && $status <6.0:
                $citasCancelator=new CancelCitas($status, $numberCel,$text,$buttonPayload,$mediaUrl, $citasService, $chatsServices);
                return $citasCancelator->cancelCitasDriver();
            case $status >= 9.0 && $status <10.0:
                $enderChat=new EndChat($status, $numberCel,$text,$buttonPayload,$mediaUrl, $citasService, $chatsServices);
                return $enderChat->endChatDriver();
            case $status >= 10.0 && $status <11.0:
                $specialStates=new SpecialStates($status, $numberCel,$text,$buttonPayload,$mediaUrl,$citasService, $chatsServices);
                return $specialStates->SpecialStatusDrive();
            default:
                return "Estado no reconocido.";
        }
    }
}

