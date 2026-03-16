<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class AuditModel extends BaseModel
{
    use HasFactory;

    protected $table = "auditoria_mc";

    protected $selecteable = ["fecha_creacion", "descripcion", "modulo"];
    protected $searchable = ["descripcion", "fecha_creacion"];

    // Método para crear un registro en la tabla de auditoría
    public function create($dataAction)

    {   
        
        $table = (new self())->table;
        $bindings = array_values($dataAction);
        $placeholders = self::makePlaceholders($dataAction);
        $columns = self::makeValues($dataAction);
        
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        return self::senqQuery($query, $bindings, 'insert');
    }

    // Método para obtener registros por módulo, con rango de fechas opcional
    public function getRegisterByModule($module, $from = null, $to = null)
    {
        $table = (new self())->table;
        $columns = implode(', ', (new self())->selecteable);
        $bindings = [$module];

        $query = "SELECT {$columns} FROM {$table} WHERE modulo = ?";

        if ($from && $to) {
            $query .= " AND fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) AND CONVERT(smalldatetime, ?, 120)";
            $bindings[] = $from;
            $bindings[] = $to;
        }

        return self::senqQuery($query, $bindings);
    }

    
    public function searchRegister($search, $from = null, $to = null)
    {
        $table = (new self())->table;
        $keywords = array_filter(explode(' ', strtolower($search)));
        $fields = (new self())->searchable;
    
        $conditions = [];
        $bindings = [];
    
        foreach ($keywords as $word) {
            $wordConditions = [];
            foreach ($fields as $field) {
                $wordConditions[] = "$field LIKE ?";
                $bindings[] = '%' . $word . '%';
            }
             
            $conditions[] = '(' . implode(' OR ', $wordConditions) . ')';
        }
    
         
        $query = "SELECT * FROM {$table} WHERE " . implode(' AND ', $conditions);
    
        if ($from && $to) {
            $query .= " AND fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) 
                        AND CONVERT(smalldatetime, ?, 120)";
            $bindings[] = $from;
            $bindings[] = $to;
        }
    
        $query .= " ORDER BY 
            (CASE WHEN descripcion LIKE ? THEN 1 ELSE 0 END +
             CASE WHEN fecha_creacion LIKE ? THEN 1 ELSE 0 END) DESC";
    
        $bindings[] = '%' . $search . '%';
        $bindings[] = '%' . $search . '%';
    
        return self::senqQuery($query, $bindings, 'select', $this->database);
    }
    
}
