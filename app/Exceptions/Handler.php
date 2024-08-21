<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\UnAuthorizateException;
use App\Utils\ResponseManager;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (BadRequestException $e, $request) {
            $responseManager = app(ResponseManager::class);
            $response = $responseManager->badRequest( ($e->getMessage()));
            return response()->json($response,400);
        });
        $this->renderable(function (UnAuthorizateException $e, $request) {
            $responseManager = app(ResponseManager::class);
            $response = $responseManager->unauthorizate( $e->getMessage());
            return response()->json($response,401);
        });
        $this->renderable(function (ServerErrorException $e, $request) {
            $responseManager = app(ResponseManager::class);
            $response = $responseManager->serverError(($e->getMessage()));
            return response()->json($response,500);
        });
        $this->renderable(function (NotFoundException $e, $request) {
            $responseManager = app(ResponseManager::class);
            $response = $responseManager->NotFound(($e->getMessage()));
            return response()->json($response,400);
        });
    }
}
