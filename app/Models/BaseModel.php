<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    use HasFactory;
    protected $table="auditoria_mc";
    protected $selecteable=["fecha_creacion","descripcion","modulo"];

    
    protected static function makePlaceholders(array $data)
    {
        $numPlaceholders = count($data);
        return implode(', ', array_fill(0, $numPlaceholders, '?'));
    }
    protected static function makeValues(array $data)
    {
        return implode(', ', array_keys($data));
    }
    protected static function senqQuery(string $query, ?array $bindings = [], string $typeConsult = 'select')
    {
        try {
            $bindings = $bindings ?? [];  
    
            switch ($typeConsult) {
                case 'select':
                    return DB::select($query, $bindings);
                case 'insert':
                    DB::insert($query, $bindings);
                    $lastId = DB::getPdo()->lastInsertId();
                    return DB::select("SELECT * FROM casos_ordenes WHERE id = ?", [$lastId]);
                case 'update':
                    return DB::update($query, $bindings);
                case 'delete':
                    return DB::delete($query, $bindings);
                default:
                    throw new ServerErrorException("Error al ejecutar la consulta: tipo de consulta no válido", 500);
            }
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
}