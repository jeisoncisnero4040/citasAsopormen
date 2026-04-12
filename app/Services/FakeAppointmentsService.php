<?php

namespace App\Services;
use App\Ports\AppoinmentPort;
use APP\Domain\ExternalResponse;
use App\Domain\Appoinment;
use App\Domain\Client;

class FakeAppointmentsService implements AppoinmentPort
{
    public function cancelAppointment(Appoinment $appointment): ExternalResponse
    {
        // Simula la cancelación de una cita y devuelve una respuesta de éxito
        return ExternalResponse::fromSuccess(message: "success", data: [
            "date" => $appointment->getDate(),
            "time" => $appointment->getTime(),
            "doctor" => $appointment->getNameDoctor(),
            "status" => "cancelled"
        ]);
    }

    /**
     * Simula la obtención de citas para un cliente y devuelve una respuesta de éxito con datos de ejemplo.
     * @param Client $client
     * @return ExternalResponse
     * 
     */
    public function getAppointmentsForClient(Client $client): ExternalResponse{

        return ExternalResponse::fromSuccess(message: "success", data: $this->getMockAppointments());
    }
    private function getMockAppointments(): array
    {
        return [
            Appoinment::fromArray([
                "ids" => "123|||456",
                "date" => "2026-04-05",
                "nameDoctor" => "Cesar Andres Santamaría Niño",
                "time" => "15:00",
                "status" => "scheduled"
            ]),
            Appoinment::fromArray([
                "ids" => "789|||101",
                "date" => "2026-04-10",
                "nameDoctor" => "Maria Fernanda Lopez Garcia",
                "time" => "10:00",
                "status" => "scheduled"
            ])
        ];
    }
}