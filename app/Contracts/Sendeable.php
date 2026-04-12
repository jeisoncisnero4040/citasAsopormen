<?php
namespace App\Contracts;

interface Sendeable {
    public function sendMessage(string $message,string $telephoneNumber);
}