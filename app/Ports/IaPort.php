<?php
namespace App\Ports;

use App\Domain\IaResponse;
use App\Domain\Prompt;
use App\Domain\PromptResumeChat;

interface IaPort
{
    public function executePrompt(Prompt $prompt): IaResponse;

}