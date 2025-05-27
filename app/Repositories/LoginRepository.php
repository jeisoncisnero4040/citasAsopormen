<?php
namespace App\Repositories;

use App\Interfaces\AuthInterface;
use App\Models\User;


class LoginRepository extends BaseRepository implements AuthInterface
{
    public function login(string $user, string $password): mixed
    {
        $query = "SELECT TOP 1 LTRIM(RTRIM(usuario)) as usuario, cedula, password,LTRIM(RTRIM(permisomc)) as permisomc, estado
                  FROM usuarios
                  WHERE cedula = ?";

        $bindings = [$user];
        return  self::senqQuery(query: $query, bindings: $bindings);



    }
    public function changePasswordUser($cedula,$newPassword):mixed{
        $query= "UPDATE usuarios SET password = ? where LTRIM(RTRIM(cedula))= ?";
        $bindings=[$newPassword,$cedula];
        return self::senqQuery(query:$query,bindings:$bindings,typeConsult:'update');
    }
    public function getEmailByUserCedula(string $cedula): mixed
    {
        $query = "SELECT TOP 1  ecc,email FROM emplea WHERE ecc = ?";
        $bindings = [$cedula];
        return  self::senqQuery(query: $query, bindings: $bindings);
    }
    
}
