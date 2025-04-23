<?php
namespace App\Services;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Utils\ResponseManager;

class BaseService{
    protected ResponseManager $responseManager;
    public function __construct(ResponseManager $responseManager) {
        $this->responseManager=$responseManager;
    }
    protected static function sourceDidFound(array $response,string $tag):void
    {
        if(empty($response)){
            throw new NotFoundException("$tag no fué encontrado",500);
        }
    }
    protected static function ensureRowUpdated(int $response,string $tag):void{
        if($response == 0){
            throw new NotFoundException("$tag no fué encontrado",500);
        }
    }
    protected function driveResponse(mixed $response, string $tag)
    {
        if (is_array($response)) {
            self::sourceDidFound($response, $tag);
        } elseif (is_int($response)) {
            self::ensureRowUpdated($response, $tag);
        }
    
        return $this->responseManager->success($response);
    }
}