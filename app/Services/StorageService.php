<?php
namespace App\Services;

use App\Services\BaseService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Utils\ResponseManager;
use Illuminate\Http\UploadedFile;
use App\Exceptions\CustomExceptions\ServerErrorException;

class StorageService extends BaseService
{
    protected ResponseManager $responseManager;

    public function __construct(ResponseManager $responseManager)
    {
        $this->responseManager = $responseManager;
    }
    

    public function uploadEvidencie(UploadedFile $uploadedFile,$folder='evidencias')
    {   
        if (!$uploadedFile instanceof UploadedFile) {
            throw new ServerErrorException("No se recibió un archivo válido de tipo UploadedFile", 500);
        }

        if (!$uploadedFile || !$uploadedFile->isValid()) {
            throw new ServerErrorException("Error al subir el archivo: No fue entregado un archivo válido");
        }

        try {  

            $upload = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder' => "pqrs/{$folder}",
                'resource_type' => 'auto' 
            ]);
            $fileUrl = $upload->getSecurePath();
            if (empty($fileUrl)) {
                throw new ServerErrorException("Error al subir el archivo: No se obtuvo la URL segura de Cloudinary", 500);
            }

            return $this->responseManager->success(['url' => $fileUrl]);

        } catch (\Exception $e) {
            throw new ServerErrorException("Error al subir la imagen: " . $e->getMessage(), 500);
        }
    }
    public function deleteFiles(mixed $files): void
    {
        if (is_string($files)) {
            $files = [$files];
        }
        if (!is_array($files) || empty($files)) {
            return; 
        }
        foreach ($files as $file) {
            try {
                if (!empty($file)) {
                    Cloudinary::destroy($file->url_evidencia);
                }
            } catch (\Exception $e) {
                \Log::error("Error al eliminar archivo de Cloudinary: {$file}. Error: " . $e->getMessage());
            }
        }
    }
}
