<?php
namespace App\Requests;
use Illuminate\Support\Str;

use App\Exceptions\CustomExceptions\BadRequestException;
use Illuminate\Support\Facades\Validator;

class OrdersCaseValidator {
    public static function validateNewCase(array $newCase) {
        
        $validator= Validator::make($newCase, [
            'num_historia' => 'nullable|string|max:10',
            'cedula_cliente' => 'required|string|max:20',
            'nombre_cliente' => 'required|string|max:100',
            'celular_cliente' => 'required|string|max:10',
            'eps_cliente' => 'required|string|max:100',
            'direccion_cliente' => 'nullable|string|max:200',
            'email_cliente' => 'nullable|email|max:100',
            'codigo_autorizacion' => 'nullable|string|max:30',
            'url_imagen_cedula1' => 'required|url|max:100',
            'url_imagen_cedula2' => 'required|url|max:100',
            'url_imagen_order' => 'nullable|url|max:100',
            'url_imagen_historia_1' => 'nullable|url|max:100',
            'url_imagen_historia_2' => 'nullable|url|max:100',
            'url_imagen_historia_3' => 'nullable|url|max:100',
            'url_imagen_historia_4' => 'nullable|url|max:100',
            'url_imagen_historia_5' => 'nullable|url|max:100',
            'url_imagen_preautoriz' => 'nullable|url|max:100',
            'url_imagen_autoriz' => 'nullable|url|max:100',
            'url_case_in_pdf' => 'nullable|url|max:100',
            'descripcion_caso_particular' => 'nullable|string|max:1000',
            'observaciones_caso' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()){
            throw new BadRequestException("Error al ingresar el nuevo caso, por favor, revisa la documentacion e intenta de nuevo ",400);
        }
    }
    public static function validateAcceptCase(array $data)
    {   
  
        $validator = Validator::make($data, [
            'usuario' => 'required|string|max:100', 
            'id' => 'required|integer|exists:casos_ordenes,id', 
        ]);

        if ($validator->fails()) {
            throw new BadRequestException("Error al acceptar el caso, se requiere un nombre de un usuario o un Id valido ",400);
        }
    }
    public static function  validateRejectCase($data){
        $validator = Validator::make($data, [
            'usuario' => 'required|string|max:100', 
            'id' => 'required|integer|exists:casos_ordenes,id', 
            'observaciones'=>'required|string'
        ]);

        if ($validator->fails()) {
            throw new BadRequestException("Error al acceptar el caso, se requiere un nombre de un usuario o un Id valido ",400);
        }
    }
    public static function validateCloseCase($data){
        $validator = Validator::make($data, [
            'usuario' => 'required|string|max:100', 
            'id' => 'required|integer|exists:casos_ordenes,id', 
        ]);

        if ($validator->fails()) {
            throw new BadRequestException("Error al acceptar el caso, se requiere un nombre de un usuario o un Id valido ",400);
        }

    }
    public static function CheckDataClientToSearchCases($data){
        $validator = Validator::make($data, [
            'celular' => 'required|string|max:10', 
            'cedula' => 'required|string', 
            'codigo'=>'optional|string'
        ]);

        if ($validator->fails()) {
            throw new BadRequestException("Error al encontrar los casos, la informacion del cliente no es correcta",400);
        }
    }
}
