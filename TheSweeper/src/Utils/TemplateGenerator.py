import jinja2
import os
import datetime

class TemplateGenerator:
    @staticmethod
    def daily_informe_template():
        template_content = """
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
        <img src="{{ logo_url }}" alt="Logo">
    </div>
    <div class="body">
        <div class="text">
            <h2>central de citas Asopormen</h2>
            <p> 
                Adjunto a este Email encontraras un informe que el sistema Chatbot a generado acerca de las el 
                numero de citas notificadas, cancelas y confirmadas el dia {{date}}
            </p>
            <p>Nota:</p>
            <p>El archivo html adjunto a este email es posible que tenga una duracion limitada</p>
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
        """

        
        template = jinja2.Template(template_content)
        rendered_template = template.render(
            date=datetime.datetime.now().strftime('%d de %B de %Y'),
            logo_url="http://asopormen.co/clinico/static/media/logo.587d7c3b8edfbd91b697.png"
        )

        return rendered_template
    
    @staticmethod
    def error_whatsapp_service() -> str:
        """
        Genera una plantilla HTML para notificar un error en el servicio de WhatsApp.
        """
        template_content = """
        <!DOCTYPE html>
        <html>
        <head>
            <title>Notificación de Error - Sistema de Citas</title>
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
                    justify-content: flex-start;
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
                    margin: 2% 5%;
                    background-color: #f1f1f1;
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
                    padding: 10px 0;
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    color: #f1f1f1;
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
                <img src="{{ logo_url }}" alt="Logo">
            </div>
            <div class="body">
                <div class="text">
                    <h2>Área de Sistemas - Asopormen</h2>
                    <p>
                        El sistema chatbot de Asopormen informa una posible falla el día {{ date }}:
                    </p>
                    <h4>Fallo al conectar con la API del servicio de WhatsApp</h4>
                    <p><strong>Posibles causas:</strong></p>
                    <ul>
                        <li>El servidor donde se aloja el sistema no se encuentra disponible o no fue encontrado.</li>
                        <li>La URL pública del dominio ha sido cambiada o eliminada.</li>
                    </ul>
                    <br>
                    <small>Si has recibido este mensaje por error, por favor ignóralo.</small>
                    <br>
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
        """
        
        template = jinja2.Template(template_content)
        rendered_template = template.render(
            date=datetime.datetime.now().strftime('%d de %B de %Y'),
            logo_url="http://asopormen.co/clinico/static/media/logo.587d7c3b8edfbd91b697.png"
        )

        return rendered_template
