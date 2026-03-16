<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;

interface StorageInterface
{
    public function store(UploadedFile $file, string $path): string;

    public function destroy(string $key): bool;

    public function signedUrl(string $key): string;

    public function replace(
        UploadedFile $file,
        string $path,
        ?string $oldPath = null
    ): string;
}