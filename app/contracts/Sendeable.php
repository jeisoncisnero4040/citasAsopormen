<?php
namespace App\contracts;

interface Sendeable {
    public function sendMessage(string $message,string $telephoneNumber);
}