<!DOCTYPE html>
<html>
<head>
    <title>Recuperación de contraseñas</title>
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

        .text p {
            line-height: 1.5;
            color: #333333;
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
            <h2>Hola {{ $name }},</h2>
            <p>Has solicitado una actualización de tu contraseña el {{ $date }} a través de nuestro sitio oficial.</p>
            <p>Tu nueva contraseña es: <strong>{{ $newPassword }}</strong></p>
            <p>Recuerda que puedes cambiarla en cualquier momento ingresando a nuestro sitio oficial y seleccionando la opción "Cambiar contraseña".</p>
            
            <br><br>
            <small>Si has recibido este mensaje por error, por favor ignóralo.</small>
            <small>Este correo se ha generado automáticamente, por favor no respondas a él.</small>
        </div>
    </div>
    <div class="footer">
        <strong>Copyright &copy; 2024
                            <a target="_blank" href="https://asopormen.org.co"> Instituto Asopormen</a>.</strong> Todos los derechos
                                reservados.
    </div>
</body>
</html>
