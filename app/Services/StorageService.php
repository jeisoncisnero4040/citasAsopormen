<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Interfaces\StorageInterface;
use App\Interfaces\Retryable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StorageService implements StorageInterface, Retryable
{
    public function store(UploadedFile $file, string $path): string
    {
        return $this->retryApply(function () use ($file, $path) {
            return $file->store($path, 's3');
        });
    }

    public function destroy(string $key): bool
    {
        return $this->retryApply(function () use ($key) {
            return Storage::disk('s3')->delete($key);
        });
    }

    public function signedUrl(string $key): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $key,
            now()->addMinutes(10)
        );
    }

    public function retryApply(callable $callback, int $retries = 3): mixed
    {
        try {
            return retry($retries, $callback, 200);
        } catch (\Throwable $e) {
            throw new ServerErrorException(
                "Error al ejecutar operación de almacenamiento ".$e->getMessage(),
                500,
                $e
            );
        }
    }
    public function replace(UploadedFile $file, string $path, ?string $oldPath = null): string
    {
        return $this->retryApply(function () use ($file, $path, $oldPath) {
            $newPath = $file->store($path, 's3');
            if (!empty($oldPath)) {
                Storage::disk('s3')->delete($oldPath);
            }
            return $newPath;
        });
    }
}