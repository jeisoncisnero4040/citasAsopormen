<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Interfaces\StorageInterface;
use App\Interfaces\Retryable;
use App\Interfaces\Keyable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FakeStorage implements StorageInterface, Retryable
{
    /**
     * Disco usado por este adaptador. Configúralo vía config/filesystems.php
     * (normalmente 'local' apunta a storage/app/private o storage/app).
     */
    private string $disk;

    public function __construct(string $disk = 'local')
    {
        $this->disk = $disk;
    }

    public function store(UploadedFile $file, string $path): string
    {
        return $this->retryApply(function () use ($file, $path) {
            return $file->store($path, $this->disk);
        });
    }

    public function put(Keyable $key, string $contents): bool
    {
        return $this->retryApply(function () use ($key, $contents) {
            return Storage::disk($this->disk)->put($key->getKey(), $contents);
        });
    }

    public function destroy(string $key): bool
    {
        return $this->retryApply(function () use ($key) {
            return Storage::disk($this->disk)->delete($key);
        });
    }

    public function signedUrl(string $key): string
    {
        // El disco local no soporta URLs firmadas (temporaryUrl lanza excepción).
        // Devolvemos una URL normal al archivo.
        return Storage::disk($this->disk)->url($key);
    }

    public function retryApply(callable $callback, int $retries = 3): mixed
    {
        try {
            return retry($retries, $callback, 200);
        } catch (\Throwable $e) {
            throw new ServerErrorException(
                "Error al ejecutar operación de almacenamiento ".$e->getMessage(),
                500
            );
        }
    }

    public function replace(UploadedFile $file, string $path, ?string $oldPath = null): string
    {
        return $this->retryApply(function () use ($file, $path, $oldPath) {
            $newPath = $file->store($path, $this->disk);
            if (!empty($oldPath)) {
                Storage::disk($this->disk)->delete($oldPath);
            }
            return $newPath;
        });
    }
}