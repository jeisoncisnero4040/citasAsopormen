<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;


class CaseOrderModel extends BaseModel
{
    use HasFactory;
    protected $table = 'casos_ordenes';
    public static function create(array $case)
    {
        $placeholders = self::makePlaceholders($case);
        $columns = self::makeValues($case);
        $table = (new self())->table;
        $query = "INSERT INTO  $table ($columns) VALUES ($placeholders)";
        return self::senqQuery($query,array_values($case),'insert');
    }
    
    public static function CreateCaseParticularUser(array $case){
        return self::create($case);
    }
    public static function CreateCaseWithPdf(array $case){
        return self::create($case);
    }
    public static function getAllUnfinishedCases(){
        $table = (new self())->table;
        $query="
            SELECT 
            id,
            num_historia,
            cedula_cliente,
            nombre_cliente,
            celular_cliente,
            eps_cliente,
            direccion_cliente,
            email_cliente,
            codigo_autorizacion,
            url_imagen_cedula1,
            url_imagen_cedula2,
            url_imagen_order,
            url_imagen_historia_1,
            url_imagen_historia_2,
            url_imagen_historia_3,
            url_imagen_historia_4,
            url_imagen_historia_5,
            url_imagen_preautoriz,
            url_imagen_autoriz,
            url_case_in_pdf,
            descripcion_caso_particular,
            observaciones_caso,
            fecha_creacion,
            aceptada
        FROM $table
        WHERE rechazada = 0 
            AND terminada = 0
        ORDER BY fecha_creacion ASC;
        ";
        return self::senqQuery($query,null);
    }
    public static function getCaseById(int $id){
        $table = (new self())->table;
        $query="SELECT * FROM $table WHERE id = ?";
        return self::senqQuery($query,[$id]);

    }
    public static function rejectCase(array $data){
        $table = (new self())->table;
        $bindings=[$data['observaciones'],$data['usuario'],$data['date'],$data['id']];
        $query="
                UPDATE $table 
                SET rechazada = 1, 
                observaciones_rechazo = ?,
                user_rechazo= ?,
                fecha_rechazo=CONVERT(smalldatetime, ?,120)
                WHERE id=?
                ";
        return self::senqQuery($query,$bindings,'update');
    }
    public static function acceptCase(array $data){
        $table = (new self())->table;
        $bindings=[$data['usuario'],$data['date'],$data['id']];
        $query="
                UPDATE $table 
                SET aceptada = 1, 
                user_aceptacion= ?,
                fecha_aceptacion=CONVERT(smalldatetime, ?,120)
                WHERE id=?
                ";
        return self::senqQuery($query,$bindings,'update');
    }
    public static function finishCase (array $data){
        $table = (new self())->table;
        $bindings=[$data['usuario'],$data['date'],$data['id']];
        $query="
                UPDATE $table 
                SET terminada = 1, 
                user_cierre= ?,
                fecha_cierre=CONVERT(smalldatetime, ?,120)
                WHERE id=?
                ";
        return self::senqQuery($query,$bindings,'update');
    }
    public static function getcasesByCodigo(array $data){
        $table = (new self())->table;
        $bindins=[$data['codigo']];
        $query="
            select id,
            en_proceso,
            aceptada,
            rechazada,
            finalizada,
            fecha_creacion,
            fecha_aceptacion,
            fecha_cierre,
            fecha_rechazo,
            observaciones_rechazo
            FROM $table 
            WHERE num_historia = ?


        ";
        return self::senqQuery($query,$bindins);
    }
    public static function getCasesByIdAndCedula($data){
        $table = (new self())->table;
        $bindins=[$data['cedula'],$data['celular']];
        $query="
            select id,
            en_proceso,
            aceptada,
            rechazada,
            finalizada,
            fecha_creacion,
            fecha_aceptacion,
            fecha_cierre,
            fecha_rechazo,
            observaciones_rechazo
            FROM $table 
            WHERE cedula_cliente = ? 
            AND celular_cliente = ?


        ";
        return self::senqQuery($query,$bindins);
    }

    
}
