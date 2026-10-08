<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;
use App\Domain\KeyStorage;
use App\Interfaces\Keyable;

interface StorageInterface
{
    public function store(UploadedFile $file, string $path): string;

    public function destroy(string $key): bool;

    public function signedUrl(string $key): string;

    public function put(Keyable $key, string $contents): bool;

    public function replace(
        UploadedFile $file,
        string $path,
        ?string $oldPath = null
    ): string;
}