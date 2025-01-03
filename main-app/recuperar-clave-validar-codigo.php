<?php
require_once("index-logica.php");
$Plataforma = new Plataforma;

$datosUsuario = '';
if (!empty($_REQUEST['datosUsuario'])) {
    $datosUsuarioEncode = base64_decode($_REQUEST['datosUsuario']);
    $datosUsuario = unserialize($datosUsuarioEncode);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Plataforma Educativa SINTIA | Login</title>
    <!-- Google fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="../config-general/assets-login-2023/css/styles.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- libreria de animate.style -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        /* Estilos básicos para el wizard */
        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .buttons {
            margin-top: 20px;
        }

        .code-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }
    </style>

</head>

<body>
    <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, me gustaria recibir mas información de la plataforma." class="float" target="_blank"><i class="fa fa-whatsapp my-float"></i></a>

    <div class="login-container register-container">
        <div class=" vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2" id="login">
                        <form method="post" name="example_advanced_form" id="example-advanced-form" action="recuperar-clave-guardar.php" class="needs-validation" novalidate>
                            <?php include("../config-general/mensajes-informativos.php"); ?>
                            <input type="hidden" id="idRegistro" name="idRegistro" value="<?= !empty($datosUsuario['datos_codigo']['idRegistro']) ? $datosUsuario['datos_codigo']['idRegistro'] : ""; ?>" />
                            <input type="hidden" id="usuarioId" name="usuarioId" value="<?= !empty($datosUsuario['id_nuevo']) ? $datosUsuario['id_nuevo'] : ""; ?>" />

                            <div class="text-center">
                                <img class="mt-4 mb-4" src="<?=$Plataforma->logoCian;?>" width="100">
                                <h1 class="mt-4">Revisa tu bandeja de entrada</h1>
                                <p class="mt-4">Hemos enviado un código de 6 caracteres a <strong id="emailCode"><?=$datosUsuario['usuario_email']?></strong>. Este código será válido durante <strong><span id="contMin">10</span> <span id="textMin">minutos</span></strong>.</p>
                                
                                <!-- Contenedor para los inputs del código -->
                                <div class="d-flex justify-content-center align-items-center mt-4">
                                    <input type="text" maxlength="1" class="form-control mx-1 text-center code-input" style="width: 50px; height: 50px; font-size: 24px;"/>
                                    <input type="text" maxlength="1" class="form-control mx-1 text-center code-input" style="width: 50px; height: 50px; font-size: 24px;"/>
                                    <input type="text" maxlength="1" class="form-control mx-1 text-center code-input" style="width: 50px; height: 50px; font-size: 24px;"/>
                                    <span class="mx-2" style="font-size: 24px;"> - </span>
                                    <input type="text" maxlength="1" class="form-control mx-1 text-center code-input" style="width: 50px; height: 50px; font-size: 24px;"/>
                                    <input type="text" maxlength="1" class="form-control mx-1 text-center code-input" style="width: 50px; height: 50px; font-size: 24px;"/>
                                    <input type="text" maxlength="1" class="form-control mx-1 text-center code-input" style="width: 50px; height: 50px; font-size: 24px;"/>
                                </div>

                                <p class="mt-4 alert alert-success alert-block animate__animated animate__flash animate__repeat-2" id="message">Hemos enviado un nuevo código a tu correo electrónico,<br> si no ves el correo revisa tu carpeta de spam o<br> verifica que hayas ingresado bien tu correo electrónico.</p>
                                <button type="button" id="btnValidarCodigo" class="btn btn-primary mt-4" onclick="verificarCodigo()">Validar Código</button>
                                <p class="mt-4">¿Tienes problemas? Revisa tu carpeta de spam o <a href="javascript:void(0);" id="intNuevo" class="text-decoration-none" data-color-cambio="<?=$Plataforma->colorUno;?>" style="color: #000;">inténtalo de nuevo</a></p>

                                <div class="d-flex justify-content-between align-items-center" style="margin-top: 50px;">
                                    <!-- Checkbox -->
                                    <a href="index.php" class="text-body">Regresar al login</a>
                                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdiugXhzAj0Ysmt2gthO07tbvjxTA7CHcZqgzBpkefZC6T2qg/viewform" class="text-body" target="_blank">¿Requieres soporte?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="logo-container position-relative vertical-center" style="width: 100%; height: 100vh; overflow: hidden;">
            <!-- Lottie como fondo -->
            <lottie-player src="https://assets7.lottiefiles.com/packages/lf20_hzgq1iov.json" background="transparent" speed="1" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" loop autoplay></lottie-player>

            <!-- Contenido centrado encima de la animación -->
            <div class="content-overlay text-center" style="position: relative; z-index: 1; color: #000;">
                <h3 class="mt-5">¿Necesitas ayuda?</h3>

                <div class="button-container d-flex justify-content-center mt-3" style="gap: 15px;">
                    <a class="btn deepPink-bgcolor btn-lg btn-rounded" target="_blank" href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, me gustaria recibir mas información de la plataforma.">3006075800 </a>
                    <a class="btn btn-primary btn-lg btn-rounded" target="_blank" href="mailto:info@plataformasintia.com">info@plataformasintia.com</a>
                </div>
            </div>
        </div>
    </div>
    <script src="js/recuperarClave.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
    <script src="../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <!-- Core theme JS-->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
        startCountdown(10 * 60);
    </script>

</body>

</html>