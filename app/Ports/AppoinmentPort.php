<?php

namespace App\Ports;
use App\Domain\ExternalResponse;
use App\Domain\Appoinment;
use App\Domain\Client;

interface AppoinmentPort
{
    public function cancelAppointment(Appoinment $appointment): ExternalResponse;
    public function getAppointmentsForClient(Client $client): ExternalResponse;
}