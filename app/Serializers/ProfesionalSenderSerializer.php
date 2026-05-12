<?php

namespace App\Serializers;
use App\Models\ProfesionalSender;
use App\Commands\ProfesionalSenderCommand;



class ProfesionalSenderSerializer
{
    public static function serialize(ProfesionalSenderCommand $sender): array
    {
        return [
            'codigo' => $sender->getCode(),
            'cedula' => $sender->getDocumentNumber(),
            'nombre' => $sender->getName(),
            'telefono' => $sender->getPhone(),
            'direccion' => $sender->getAddress(),
            'ciudad' => $sender->getCity(),
            'email' => $sender->getEmail(),
            'tipo_doc' => $sender->getDocumentType(),
            'sdt_fecha_registro' => $sender->getRegistrationDate(),
            'usuario_registro' => $sender->getRegisteredBy(),
            'especialidad' => $sender->getSpecialty(),
            'ips' => $sender->getIps()  
        ];
    }
    public static function deserialize(array $data): ProfesionalSenderCommand
    {
        return new ProfesionalSenderCommand(
            code: $data['codigo'],
            documentNumber: $data['cedula'],
            name: $data['nombre'],
            documentType: $data['tipoDocumento'],
            phone: $data['telefono'] ?? null,
            address: $data['direccion'] ?? null,
            city: $data['ciudad'] ?? null,
            email: $data['correo'] ?? null,
            specialty: $data['especialidad'] ?? null,
            ips: $data['ips'] ?? null
        );
    }
}