<?php
namespace App\Contracts;
use App\Domain\Chat;

interface Resumable
{
    public function generateResume(Chat $chat): Chat;
}