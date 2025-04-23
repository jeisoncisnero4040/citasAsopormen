<?php

namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;
use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\DB;

class BaseRepository 
{

    protected static function makePlaceholders(array $data)
    {
        $numPlaceholders = count($data);
        return implode(', ', array_fill(0, $numPlaceholders, '?'));
    }
    protected static function makeColumns(array $data)
    {
        return implode(', ', array_keys($data));
    }
    protected static function makeValues(array $data):array{
        return array_values($data);
    }
    protected static function makeSetClause(array $data): string
    {
        $columns = array_keys($data);
        return implode(', ', array_map(fn($col) => "$col = ?", $columns));
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
                    return $lastId = DB::getPdo()->lastInsertId();
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