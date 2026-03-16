<?php

namespace App\Repositories;



use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class BaseRepository
{


    protected static function makePlaceholders(array $data)
    {
        $columns = array_keys($data);

        return implode(',', array_map(
            fn($col) => Str::startsWith($col, 'sdt_')
                ? 'CONVERT(smalldatetime,?,120)'
                : (Str::startsWith($col, 'stt_')
                    ? 'CONVERT(time,?,120)'
                    : '?'),
            $columns
        ));
    }
    
    protected static function makePlaceholdersPlains(array $items){
         return implode(', ', array_fill(0, count($items), '?'));
    }

    protected static function makeColumns(array $data)
    {
        return implode(', ', array_map(fn($col) => str_replace(['sdt_','stt_'],'',$col),array_keys($data)));
    }
    protected static function makeValues(array $data):array{
        return array_values($data);
    }
    protected static function makeSetClause(array $data): string
    {
        $columns = array_keys($data);

        return implode(',', array_map(
            function ($col) {

                if (Str::startsWith($col, 'sdt_')) {
                    $realCol = str_replace('sdt_', '', $col);
                    return "$realCol = CONVERT(smalldatetime, ?, 120)";
                }

                return "$col = ?";
            },
            $columns
        ));
    }
    
    protected static function sendQuery(string $query, ?array $bindings = [], string $typeConsult = 'select'):int|array
    {
        try {
            $bindings = $bindings ?? [];  
    
            switch ($typeConsult) {
                case 'select':
                    return DB::select($query, $bindings);
                case 'insert':
                    DB::insert($query, $bindings);
                    return (int) DB::getPdo()->lastInsertId();
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