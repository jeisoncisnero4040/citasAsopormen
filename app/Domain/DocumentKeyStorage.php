<?php

namespace App\Domain;
use App\Interfaces\Keyable;
use Illuminate\Support\Str;

// Generar UUID v4 estándar
$uuid = (string) Str::uuid();

class DocumentKeyStorage implements Keyable
{
    private string $ROOT_DIRECTORY_STORAGE = "autorizaciones";
    private string $clientCode;
    private string $n_autoriza;
    private string $extension = "pdf";
    private ? string $name;

    public function __construct(string $clientCode, string $n_autoriza, ?string $name = null, string $extension = "pdf")
    {
        $this->clientCode = $clientCode;
        $this->n_autoriza = $n_autoriza;
        $this->name = $name ?? (string) Str::uuid();
        $this->extension = $extension;
    }

    public function getKey(): string
    {
        return "Documentos/" . $this->clientCode . '/'. $this->ROOT_DIRECTORY_STORAGE . '/' . $this->n_autoriza.'/'.$this->name .'.'.$this->extension;
    }

}