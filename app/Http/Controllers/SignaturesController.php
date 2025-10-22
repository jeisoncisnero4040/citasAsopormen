<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class SignaturesController extends Controller
{

    public function serve($filename)
    {

        $filename = basename($filename);
        $fullPath = "/home/Firmas/{$filename}";

        if (!File::exists($fullPath)) {
            abort(404, 'Firma no encontrada');
        }

        $mime = File::mimeType($fullPath);
        $content = File::get($fullPath);

        return Response::make($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'. $filename .'"'
        ]);
    }
}
