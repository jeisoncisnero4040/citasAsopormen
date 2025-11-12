<?php 
namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\Http;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

use App\Utils\ResponseManager;

class AuditService extends BaseService
{
    public ResponseManager $responseManager;


    public function __construct(ResponseManager $responseManager) {
        $this->responseManager = $responseManager;

    }

    public function sendNewRegister(array $info): array
    {
        try {
            $body = $this->getBody($info);
            DB::insert("INSERT INTO auditoria_mc (modulo,descripcion) VALUES (?,?)",array_values($body));
            return $this->responseManager->success('Auditoría registrada exitosamente');
        } catch (\Throwable $e) {
            throw new ServerErrorException('El pqrs fue notificado correctamente, pero no fue posible registrar la accion en el modulo de auditoria', 500);
        }
    }

    private function getBody(array $request): array
    {
        $employee = $request['user'] ?? 'Usuario desconocido';
        $pqrUser = $request['nomre_usuario'] ?? 'Sin nombre';
        $pqrId = $request['id'] ?? '0';
        $area = $request['area_servicio'] ?? 'Área desconocida';

        $description = "{$employee} notificó al área {$area} el PQRS con id {$pqrId}";

        return [
            'modulo' => 'calidad',
            'descripcion' => $description,
        ];
    }
}
