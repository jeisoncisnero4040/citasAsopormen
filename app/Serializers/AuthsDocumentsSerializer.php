<?php

namespace App\Serializers;

use App\Commands\AuthsDocumentsCommand;
use App\Models\AuthsDocumentsViewModel;

class AuthsDocumentsSerializer
{
    public static function toPersistenceArray(AuthsDocumentsCommand $command): array
    {
        return  [
            "autorizacion_id" => $command->getNAutoriza(),
            "tipo_documento_id" => $command->getDocumentTypeId(),
            "cedula_usuario" => $command->getCedulaUser(),
            "codigo_eps" => $command->getEpsCode(),
            "sdt_fecha_registro" => $command->getCreatedAt(),
            "documento_path" => $command->getKeyDocument(),
            "historia" => $command->getClientCode()
        ];
    }
    public static function fillableColumns(): array
    {
        return [
            "id",
            "autorizacion_id",
            "tipo_documento_id",
            "cedula_usuario",
            "codigo_eps",
            "fecha_registro",
            "documento_path",
            "historia"
        ];
    }


    public static function fromArray(array $data): AuthsDocumentsCommand
    {
        return new AuthsDocumentsCommand(

            n_autoriza:$data['n_autoriza'] ?? '',
            documentTypeId:$data['documentTypeId'] ?? 0,
            id:$data['id'] ?? null,
            cedulaUser:$data['cedula_usuario'] ?? '',
            epsCode:$data['codigo_eps'] ?? '',
            created_At:$data['fecha_registro'] ?? '',
            keyDocument:$data['documento_path'] ?? null,
            nameDocument:$data['nameDocument'] ?? '',
            clientCode:$data['historia'] ?? ''
        );
        
    }

    public static function fromArrayToViewModel(array $data): AuthsDocumentsViewModel
    {
        return new AuthsDocumentsViewModel(
            id: $data['id'] ?? null,
            documentName: $data['nombre_tipo_documento'] ?? '',
            n_autoriza: $data['n_autoriza'] ?? '',
            clientCode: $data['historia'] ?? '',
            clientCedula: $data['cedula_usuario'] ?? '',
            epsCode: $data['codigo_eps'] ?? '',
            createdAt: $data['fecha_registro'] ?? '',
            documentPath: $data['documento_path'] ?? '',
            id_doc:(int) $data['id_doc'] ?? 0

        );
    }

    public static function serialize(AuthsDocumentsViewModel $model): array
    {
        return [
            "id" => $model->getId(),
            "documentName" => $model->getDocumentName(),
            "n_autoriza" => $model->getNAutoriza(),
            "clientCode" => $model->getClientCode(),
            "clientCedula" => $model->getClientCedula(),
            "epsCode" => $model->getEpsCode(),
            "createdAt" => $model->getCreatedAt(),
            "url" => $model->getPublicUrl(),
            "id_doc" => $model->getIdDoc()
        ];
    }
}