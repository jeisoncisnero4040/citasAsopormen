<?php

namespace App\Repositories;

use App\Dtos\UpdateProDto;
use App\Interfaces\ProfesionalRepositoryInterface;
use App\Models\ProfesionalModel;
use App\Exceptions\CustomExceptions\NotFoundException;


class ProfesionalRepository  extends BaseRepository implements ProfesionalRepositoryInterface{

    public function getProfesionalByIdentity(string $identityNumber): ProfesionalModel
    {
        $query="SELECT 
                us.cedula AS ecc,
                us.responsable AS nombre,
				us.usuario,
                us.password,
                us.rol_id,
                em.dias_habiles_evolucion ,
                CASE 
                    WHEN us.estado = 'ACTIVO' THEN '1' 
                    ELSE '0'
                END AS eactivo,
                em.agenda,
                em.email,
                em.etel AS cel,
                em.edirecc as direccion,
                us.contrasenia_cambiada,
                em.url_imagen,
                esp.nombre AS especialidad,
                r.sub_rol
            FROM usuarios us
            INNER JOIN emplea em ON em.ecc =us.cedula
            INNER JOIN especial esp ON em.especialidad = esp.cod
            INNER JOIN roles_mc r ON r.id=us.rol_id
            WHERE us.cedula = ?";

        $bindings = [$identityNumber];
        $profesional=self::sendQuery(query:$query,bindings:$bindings);

        if(empty($profesional)){
            throw new NotFoundException("No se encontró un usuario con la cédula proporcionada",404);
        }
        return new ProfesionalModel($profesional[0]);
    }
    public function changePasswordProfesional(string $cedula, string $newPassword, bool $firstChange): int
    {
        $bindings = [$newPassword];
        
        $query = "UPDATE usuarios SET password = ?";
        
        if ($firstChange) {
            $query .= ", contrasenia_cambiada = '1'";
        }

        $query .= " WHERE cedula = ?";
        $bindings[] = $cedula;

        return self::sendQuery(query: $query, bindings: $bindings, typeConsult: 'update');
    }
    public function getProcedipros(string $cedula): array
    {
        return self::sendQuery(query:"SELECT DISTINCT ci.procedipro AS nombre,
                                    pro.id
                                    FROM citas ci
                                    INNER JOIN procedipro pro ON pro.nombre = ci.procedipro 
                                    WHERE ci.cedprof=? ",bindings:[$cedula]);
                                
    }

    public function getInfo(string $cedula){
        return self::sendQuery(
            query:"SELECT 
            edirecc  AS direccion,
            ebarrio AS barrio,
            email,
            celular,
            url_imagen,
            firma
            FROM  emplea
            WHERE ecc = ?",bindings:[$cedula]);
    }
    public function searchByString(string $param): array
    {
        $param = trim($param);

        return self::sendQuery(
            "SELECT TOP 20 RTRIM(emp.ecc) as cedula, RTRIM(emp.enombre) as nombre
                FROM emplea emp
                INNER JOIN usuarios usu ON emp.ecc = usu.cedula
                WHERE emp.agenda = 1 
                AND UPPER(emp.enombre) LIKE ?
                AND estado = 'ACTIVO'
                ORDER BY emp.enombre",
            ["%$param%"]
        );
    }
    public function updatePro(string $cedula, UpdateProDto $dto): void
    {
        $data=$dto->toArrayPersistence();
        $setCaluse=self::makeSetClause($data);
        $query="UPDATE emplea SET {$setCaluse} WHERE ecc = ?";
        $values=self::makeValues($data);
        $bindings=[...$values,$cedula];
        self::sendQuery(query:$query,bindings:$bindings,typeConsult:'update');

    }
}