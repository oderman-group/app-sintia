<?php
$logoIndex = "../sintia-gris.png";
$logoWidth = 250;
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

</head>

<body>
    <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, me gustaria recibir mas información de la plataforma." class="float" target="_blank"><i class="fa fa-whatsapp my-float"></i></a>

    <div class="login-container">
        <div class="vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2" id="login">
                        <form method="post" action="recuperar-clave-guardar.php" class="needs-validation" novalidate>
                            <?php include("../config-general/mensajes-informativos.php"); ?>
                            <input type="hidden" id="usuarioId" name="usuarioId" value="<?= !empty($_REQUEST['usuarioId']) ? base64_decode($_REQUEST['usuarioId']) : ""; ?>" />
                            <img class="mb-4" src="<?=$logoIndex;?>" width="<?=$logoWidth;?>">

                            <div class="form-floating input-group mt-3">
                                <input type="password" class="form-control input-login" id="password" name="password" placeholder="password" oninput="validarClaveNueva(this)" required>
                                <button class="btn btn-outline-secondary input-group-text" type="button" onclick="cambiarTipoInput('password', 'icoVerNueva')">
                                    <i class="bi bi-eye" id="icoVerNueva"></i>
                                </button>
                                <label for="password">Contraseña Nueva</label>
                                <div class="invalid-feedback">Por favor ingresa tu contraseña para continuar</div>
                                <div class="form-text" id="caps-lock-message" style="display: none;">Mayúsculas activadas</div>
                            </div>
                            <div id="respuestaClaveNueva" style="display:none"></div>

                            <div class="form-floating input-group mt-3">
                                <input type="password" class="form-control input-login" id="confirPassword" name="confirPassword" oninput="claveNuevaConfirmar(this)" placeholder="confirPassword" required>
                                <button class="btn btn-outline-secondary input-group-text" type="button" onclick="cambiarTipoInput('confirPassword', 'icoVerNuevaDos')">
                                    <i class="bi bi-eye" id="icoVerNuevaDos"></i>
                                </button>
                                <label for="confirPassword">Confirmar Contraseña</label>
                                <div class="invalid-feedback">Por favor ingresa tu contraseña para continuar</div>
                                <div class="form-text" id="caps-lock-message" style="display: none;">Mayúsculas activadas</div>
                            </div>
                            <div id="respuestaConfirmacionClaveNueva" style="display:none"></div>

                            <button class="w-75 btn btn-lg btn-primary btn-rounded mt-3 disabled" type="submit" id="btnEnviar">Confirmar Contraseña</button>
                        </form>

                        <div class="d-flex justify-content-between align-items-center" style="margin-top: 50px;">
                            <!-- Checkbox -->
                            <a href="index.php" class="text-body">Regresar al login</a>
                            <a href="https://docs.google.com/forms/d/e/1FAIpQLSdiugXhzAj0Ysmt2gthO07tbvjxTA7CHcZqgzBpkefZC6T2qg/viewform" class="text-body" target="_blank">¿Requieres soporte?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="logo-container position-relative vertical-center" style="width: 100%; height: 100vh; overflow: hidden;">
            <!-- Lottie como fondo -->
            <!-- <lottie-player src="https://assets7.lottiefiles.com/packages/lf20_hzgq1iov.json" background="transparent" speed="1" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" loop autoplay></lottie-player> -->

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
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <!-- Core theme JS-->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

</body>

</html>