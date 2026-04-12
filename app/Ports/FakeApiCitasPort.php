<?php
namespace App\Ports;

interface FakeApiCitasPort {
    public function sendRequestToConfirmCita($request);
    public function sendRequestToCancelCita($request);
}