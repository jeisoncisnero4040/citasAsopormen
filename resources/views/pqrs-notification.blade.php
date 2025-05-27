<!DOCTYPE html>
<html>
<head>
    <title>Notificación de nueva PQRS</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
            font-family: Arial, sans-serif;
        }

        .header {
            width: 100%;
            display: flex;
            background-color: #1b0dd3;
            justify-content: start;
        }

        .header img {
            max-height: 50px;
            width: auto;
            margin-left: 5%;
        }

        .body {
            display: flex;
            justify-content: center;
            background-color: #f1f1f1;
        }

        .text {
            width: 100%;
            margin-top: 2%;
            margin-bottom: 2%;
            background-color: #f1f1f1;
            margin-left: 5%;
            margin-right: 5%;
        }

        .text p, .text ul {
            line-height: 1.5;
            color: #333333;
        }

        .text ul {
            padding-left: 1rem;
        }

        .text a {
            color: #1a73e8;
            text-decoration: none;
        }

        .text small {
            display: flex;
            justify-content: center;
            align-items: center;
            color: #666666;
        }

        .footer {
            width: 100%;
            background: #1b0dd3;
            text-align: center;
            padding: 0;
            position: fixed;
            bottom: 0;
            left: 0;
            z-index: 1000;
            color: #f1f1f1;
            min-height: 15%;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
        }

        @media screen and (max-width: 768px) {
            .body {
                flex-direction: column;
                align-items: center;
            }

            .text {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Logo">
    </div>
    <div class="body">
        <div class="text">
            <h2>Hola estimado {{ $nameArea }},</h2>
            <p>El área de Calidad le informa que tiene una nueva PQRS que debe ser atendida.</p>

            <h4>Detalles de la PQRS:</h4>
            <ul>
                <li><strong>Tipo de PQRS:</strong> {{ $PqrType }}</li>
                <li><strong>Usuario:</strong> {{ $userPqrName }}</li>
                <li><strong>Macromotivo:</strong> {{ $macromotivePqrs }}</li>
                <li><strong>Motivo General:</strong> {{ $generalMotivetivePqrs }}</li>
                <li><strong>Motivo Específico:</strong> {{ $specificMotivetivePqrs }}</li>
                <li><strong>Tipo de Motivo:</strong> {{ $typeMotivetivePqrs }}</li>
                <li><strong>Causa:</strong> {{ $causeMotivetivePqrs }}</li>
                <li><strong>Descripción:</strong> {{ $descriptionPqrs }}</li>
            </ul>

            <p>Pueden ingresar al siguiente enlace para revisar y responder esta solicitud:</p>

            <p>
                <a href="{{ $urlFormAnswerPqr }}">Responder PQRS</a>
            </p>

            <p><strong>Nota:</strong> A partir de esta notificación cuentan con un plazo de <strong>36 horas hábiles</strong> para dar respuesta a la solicitud registrada.</p>

            <br><br>
            <small>Este correo se ha generado automáticamente, por favor no respondas a él.</small>
        </div>
    </div>
    <div class="footer">
        <strong>&copy; 2024
            <a target="_blank" href="https://asopormen.org.co">Instituto Asopormen</a>.
        </strong> Todos los derechos reservados.
    </div>
</body>
</html>
