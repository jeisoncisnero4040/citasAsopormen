<?php

namespace App\Dtos;

use App\Requests\BufferRequest;
use Illuminate\Http\Request;

class EvoBufferDto
{
    private string $cedula;
    private string $historia;
    private string $autoriz;

    public function __construct(Request $request)
    {
        BufferRequest::validateGetBufferData($request->query());
        
        $this->cedula   = (string) $request->query('cedula');
        $this->historia = (string) $request->query('historia');
        $this->autoriz  = (string) $request->query('autoriz');
    }

    public function getCedula(): string
    {
        return $this->cedula;
    }

    public function getHistoria(): string
    {
        return $this->historia;
    }

    public function getAutoriz(): string
    {
        return $this->autoriz;
    }

    public function toArray(): array
    {
        return [
            'cedula'   => $this->cedula,
            'historia' => $this->historia,
            'autoriz'  => $this->autoriz
        ];
    }
}
