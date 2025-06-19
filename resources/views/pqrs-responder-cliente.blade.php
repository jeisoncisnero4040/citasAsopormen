<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Notificación de Respuesta PQRS</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f9fafb;">

  <!-- Contenedor principal -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9fafb;" background="https://res.cloudinary.com/dxalvdckk/image/upload/v1747664162/Captura_de_pantalla_2025-05-19_090111_jmprza.png">
    <tr>
      <td align="center">

        <!-- Encabezado -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #1b0dd3;">
          <tr>
            <td align="left" style="padding: 10px 5%;">
              <img src="https://res.cloudinary.com/dxalvdckk/image/upload/v1747435854/descarga_ztjs3h.png"
                   alt="Instituto Asopormen" style="max-height: 100px; width: auto;">
            </td>
          </tr>
        </table>

        <!-- Contenido -->
        <table width="80%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; margin: 30px 30px; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
          <tr>
            <td>

              <!-- Saludo -->
              <h2 style="margin-top: 0; color: #1f2937;">Cordial y atento saludo, {{ $user }}</h2>

              <!-- Cuerpo del mensaje -->
              <p style="line-height: 1.6; margin: 20px 0; color: #374151;">
                Desde el área de <strong>Experiencia de Servicio al Cliente</strong>, nos permitimos informarle que su solicitud PQRS, asignada el día <strong>{{ $date }}</strong>, ha sido gestionada oportunamente conforme a los lineamientos institucionales y al compromiso permanente con la mejora continua, con el objetivo de ofrecer un servicio de calidad a nuestros usuarios.
              </p>

              <p style="line-height: 1.6; margin: 20px 0; color: #374151;">
                Lamentamos sinceramente los inconvenientes presentados durante el uso de nuestros servicios. Le aseguramos que el equipo de trabajo de <strong>Asopormen</strong> ha atendido su caso con la debida diligencia, orientado a resolver la situación y a fortalecer nuestros procesos para evitar futuras recurrencias.
              </p>

              <!-- Archivos adjuntos -->
              <p style="line-height: 1.6; margin: 20px 0; color: #374151;">
                Para consultar el contenido completo de la respuesta, le invitamos a revisar los documentos adjuntos a este mensaje.
              </p>

              <!-- Nota al pie -->
              <p style="font-size: 12px; color: #6b7280; margin-top: 40px;">
                Este mensaje ha sido generado automáticamente. Por favor, no responda a este correo. Si requiere información adicional, comuníquese a través de los canales oficiales de atención.
              </p>

            </td>
          </tr>
        </table>

        <!-- Pie de página -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #1b0dd3; text-align: center; color: #ffffff; padding: 20px 0;">
          <tr>
            <td>
              <strong>&copy; 2024 <a href="https://asopormen.org.co" target="_blank" style="color: #ffffff; text-decoration: underline;">Instituto Asopormen</a></strong><br>
              Todos los derechos reservados.
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>

</body>
</html>
