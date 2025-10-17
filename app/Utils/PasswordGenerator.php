<?php
namespace App\Utils;

class PasswordGenerator{
    public static function generatePassword(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        $lengthpassword = strlen($chars);
        $newPassword = '';
    
        for ($i = 0; $i <= 10; $i++) {
            $index = rand(0, $lengthpassword - 1);
            $newPassword .= $chars[$index];
        }
        return $newPassword;
    }
    
    public static function generatePasswordNumeric($length = 4)
    {
        $chars = '0123456789';
        $newPassword = '';
        
        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, strlen($chars) - 1);
            $newPassword .= $chars[$index];
        }
        
        return $newPassword;
    }

}