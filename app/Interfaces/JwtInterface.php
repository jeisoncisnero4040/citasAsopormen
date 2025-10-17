<?php

namespace App\Interfaces;

use App\Models\ProfesionalModel;
use Attribute;

interface JwtInterface {
    function generateToken(ProfesionalModel $Profesional):string;
    function invalidateToken(string $token):void;
    function refreshToken(string $token):string;
    function getUserByToken(string $token):mixed;
    function validateToken(string $token):void;

}