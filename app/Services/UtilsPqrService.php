<?php
namespace App\Services;


use App\Interfaces\UtilsPqrRepositoryInterface;
use App\Utils\ResponseManager;
use App\Services\BaseService;


class UtilsPqrService extends  BaseService {
    protected ResponseManager $responseManager;
    private UtilsPqrRepositoryInterface $utilsRepository;
    private string $tag;

    public function __construct(ResponseManager $responseManager, UtilsPqrRepositoryInterface $utilsRepository) {
        $this->responseManager=$responseManager;
        $this->utilsRepository=$utilsRepository;
        $this->tag='utilería(S)';
    }
    /**
     * @OA\Get(
     *     path="/api/utils",
     *     summary="Obtener datos utilitarios para creación de PQRS",
     *     description="Retorna una lista de elementos como áreas, sedes, tipos de usuario, características, sogcs, tipos de PQRS, entre otros, necesarios para la creación de un PQRS.",
     *     tags={"Utils"},
     *     @OA\Response(
     *         response=200,
     *         description="Datos utilitarios obtenidos exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="1"),
     *                     @OA\Property(property="nombre", type="string", example="ADMINISTRATIVO - DIRECCIÓN O SUBDIRECCIÓN"),
     *                     @OA\Property(property="tipo", type="string", example="area", description="Categoría del dato utilitario: sede, area, caracteristica, sogsc, tipo_usuario, etc.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron datos utilitarios",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utils not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */

    public function getUtility():array{
        $utility=$this->utilsRepository->getAll();
        return $this->driveResponsePersistence($utility,$this->tag);
    }
}