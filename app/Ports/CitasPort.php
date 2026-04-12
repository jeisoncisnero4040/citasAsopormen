<?php

namespace App\Ports;

interface CitasPort
{
    public function sendCitaToWait($request) ;

    public function findLaterCitaAvaiableByTelephoneNumber($telephone);

    public function verifyCitaOnWaitMode(string $telephone, array $cita);

    public function updateCita(array $cita, string $column) ;

    public function verifyCitaUpdateFiveMinsBefore( $cita) ;


}