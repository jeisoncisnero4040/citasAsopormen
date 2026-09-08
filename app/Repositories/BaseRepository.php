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
    protected function buildCreateQuery(string $table,  array $Exampledata ,$numRegistry = 1): string
    {
        $columns = self::makeColumns($Exampledata);
        $placeholders = self::makePlaceholders($Exampledata );
        $arrayPlaceholders = array_fill(0, $numRegistry, "($placeholders)");
        $placeholdersString = implode(', ', $arrayPlaceholders);

        return "INSERT INTO $table ($columns) VALUES $placeholdersString";
    }
    protected function buildUpdateQuery(string $table, array $data, string $whereClause): string
    {
        $setClause = self::makeSetClause($data);
        return "UPDATE $table SET $setClause WHERE $whereClause";
    }
    protected function buildSelectBaseQueryCommand(array $fillable,string $table): string
    {
        $columns = implode(', ', $fillable);
        return "SELECT $columns FROM $table WHERE 1=1 {{}}";
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
    protected function transactionalQuery(callable $transaction):int|array
    {
        try {
            DB::beginTransaction();

            $result = $transaction();
            DB::commit();

            return $result;

        } catch (\Throwable $e) {
            DB::rollBack();

            throw new ServerErrorException("Error en transacción: " . $e->getMessage(), 500);
        }
    }
    public function execute(QueryBuilder $query, string $type = 'select'): int|array
    {
        return $this->sendQuery($query->toQuery(), $query->getBindings(), $type);
    }


}