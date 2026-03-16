<?php

namespace App\Interfaces;

use App\Models\User;


interface JwtInterface {
    function generateToken(User $user):string;
    function invalidateToken(string $token):void;
    function refreshToken(string $token):string;
    function getUserByToken(string $token):mixed;
    function validateToken(string $token):void;

}