<?php

namespace App\Interfaces;


interface AuthInterface{
    public function login(string $user,string $password):mixed;
    public function changePasswordUser($cedula,$newPassword):mixed;
    public function getEmailByUserCedula(string $cedula):mixed;
    
}