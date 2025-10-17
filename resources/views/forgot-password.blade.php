<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperacion Contraseña Clínico Asopormen</title>
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
            <h2 style="margin-top: 0; color: #1f2937;">Cordial saludo, {{ $user }}</h2>

            <!-- Cuerpo del mensaje -->
            <p style="line-height: 1.6; margin: 20px 0; color: #374151;">
            Desde el Instituto Clínico Asopormen, le informamos que se ha generado una nueva contraseña para su cuenta de acceso al sistema institucional, como respuesta a su solicitud de recuperación realizada el día <strong>{{ $date }}</strong>.
            </p>

            <p style="line-height: 1.6; margin: 20px 0; color: #374151;">
            A continuación, encontrará la contraseña temporal asignada. Le recomendamos ingresar al sistema y cambiarla lo antes posible por una de su preferencia, garantizando así la seguridad de su información.
            </p>

            <!-- Aquí puedes incluir la contraseña en el diseño -->
            <p style="line-height: 1.6; margin: 20px 0; color: #374151;">
            <strong>Contraseña temporal:</strong> {{ $password }}
            </p>

            <!-- Nota al pie -->
            <p style="font-size: 12px; color: #6b7280; margin-top: 40px;">
            Este mensaje ha sido generado automáticamente. Por favor, no lo responda. Si requiere asistencia adicional, comuníquese a través de los canales oficiales de atención.
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
