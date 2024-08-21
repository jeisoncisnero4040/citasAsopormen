<?php
namespace App\utils;

class PasswordGenerator{
    public static function generatePassword(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        $lengthpassword = strlen($chars);
        $newPassword = '';
    
        for ($i = 0; $i < 24; $i++) {
            $index = rand(0, $lengthpassword - 1);
            $newPassword .= $chars[$index];
        }
        return $newPassword;
    }

}