<?php 
namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\Http;
use App\Services\BaseService;

use App\Utils\ResponseManager;

class AuditService extends BaseService
{
    public ResponseManager $responseManager;
    private string $url;

    public function __construct(ResponseManager $responseManager) {
        $this->responseManager = $responseManager;
        $this->url = rtrim(env('URL_AUDIT', 'https://citas.asopormen.co:8081/api/'), '/') . "/audit/new";
    }

    public function sendNewRegister(array $info, string $token): array
    {
        try {
            $body = $this->getBody($info);


            $response = Http::withToken($token)
                ->post($this->url, $body);

            if (!$response->successful()) {
                throw new ServerErrorException('El pqr fue notificado correctamente, pero no fue posible registrar la accion en el modulo de auditoria', 500);
            }

            return $this->responseManager->success('Auditoría registrada exitosamente');

        } catch (\Throwable $e) {
            throw new ServerErrorException('El pqr fue notificado correctamente, pero no fue posible registrar la accion en el modulo de auditoria', 500);
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
