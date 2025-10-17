<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class SignaturesController extends Controller
{
    public function serve($path)
    {
        // reconstruyo la ruta completa en el servidor
        $basePath = '\\\\192.168.39.150\\MANAGER\\FIRMAS\\';
        $fullPath = $basePath . str_replace('/', '\\', $path);

        if (!file_exists($fullPath)) {
            abort(404, 'Firma no encontrada');
        }

        $mime = mime_content_type($fullPath);

        return Response::make(file_get_contents($fullPath), 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.basename($fullPath).'"',
        ]);
    }
}