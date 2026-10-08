<?php

namespace App\Adapters;
use App\Files\Domain\Pdf;
use Illuminate\Http\UploadedFile;
use App\Exceptions\CustomExceptions\BadRequestException;

class PdfFileAdapter extends HttpFileAdater
{
    public function adapt(UploadedFile $file): Pdf{
        return new Pdf($file->get());
    }
    public function validate(UploadedFile $file): void{
        if ($file->getClientOriginalExtension() !== 'pdf') {
            throw new BadRequestException('El tipo de archivo no es válido. Por favor, sube un archivo PDF.');
        }
    }
}