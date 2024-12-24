<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

$tituloMensaje = $data['asunto'];
$nombreUsuario = $data['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$tituloMensaje;?></title>

    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 10px;
    }

    .header {
        background-color: <?=$Plataforma->colorUno;?>;
        padding: 20px;
        text-align: center;
        color: #ffffff;
    }

    .header img {
        max-width: 100px;
        margin-bottom: 10px;
    }

    .header h1 {
        font-size: 24px;
        margin: 0;
    }

    .message-body {
        padding: 30px;
        color: #333333;
        line-height: 1.6;
    }

    .message-body h2 {
        font-size: 20px;
        margin-bottom: 20px;
    }

    .message-body p {
        margin-bottom: 15px;
        font-size: 16px;
    }

    .code {
        border-collapse: collapse;
        padding: 20px;
        word-break: break-word;
        border:1px solid #eae8f0;
        text-align:center;
        font-size: 15px;
        font-weight: bold;
        color: #333;
    }

    .code h1 {
        margin: 0;
        padding: 0;
    }

    .footer {
        background-color: #f4f4f4;
        padding: 15px;
        text-align: center;
        color: #777777;
        font-size: 14px;
    }

    .footer p {
        margin: 0;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 20px;
        background-color: <?=$Plataforma->colorUno;?>;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
    }

    /* Responsive Design */
    @media screen and (max-width: 600px) {
        .container {
        width: 100%;
        }

        .header, .message-body, .footer {
        padding-left: 15px;
        padding-right: 15px;
        }

        .header img {
        max-width: 80px;
        }
    }
    </style>
</head>
<body>

    <div class="container">
    <!-- Header Section -->
    <div class="header">
        <img src="<?=$Plataforma->logoBlanco;?>" alt="Logo" width="50">
        <h1><?=$tituloMensaje;?></h1>
    </div>

    <!-- Message Body -->
    <div class="message-body">
        <p>Estimado/a <b><?=$nombreUsuario;?></b>,</p>
        <p>Te enviamos este correo porque hemos recibido una petición para activar tu cuenta en SINTIA.</p>
        <p>Para continuar, utiliza el siguiente código de un solo uso:</p>
        <div class="code">
            <h1>
                <b><?=$data['codigo'];?></b>
            </h1>
        </div>
        <p>Este código es válido por 10 minutos. Si no solicitaste este código, puedes ignorar este mensaje.</p>
        <p style="margin-bottom: 0;">Saludos,</p>
        <p style="font-weight: bold; margin-top: 0;">El equipo de SINTIA</p>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>Por favor, no respondas a este correo electrónico. Este buzón no está siendo supervisado y no podremos responder a las preguntas enviadas a esta dirección.</p>
        <p>&copy; <?=date("Y");?> Oderman Inc. Todos los derechos reservados.</p>
    </div>
    </div>

</body>
</html>