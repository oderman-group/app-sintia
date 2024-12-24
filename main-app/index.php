<?php
$logoIndex = "../sintia-gris.png";
$logoWidth = 250;

if(!isset($_GET['nodb'])) {
    require_once("index-logica.php");
    require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");

    if (!empty($_GET['inst']) && !empty($_GET['year'])) {
        try {
            $informacionInstConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM ".$baseDatosServicios.".general_informacion WHERE info_institucion='" . base64_decode($_GET['inst']) . "' AND info_year=".base64_decode($_GET['year']));
            $informacion_inst = mysqli_fetch_array($informacionInstConsulta, MYSQLI_BOTH);
            if (!empty($informacion_inst["info_logo"]) && file_exists("files/images/logo/".$informacion_inst["info_logo"])) {
                $logoIndex = "files/images/logo/".$informacion_inst["info_logo"];
                $logoWidth = 300;
            }
            $inst = base64_decode($_GET['inst']);
        } catch(Exception $e){
            header("Location:".REDIRECT_ROUTE."?error=".$e->getMessage());
        }
    }
    
    if (!empty($_GET['error']) && $_GET['error'] == Mensajes_Informativos::USUARIO_BLOQUEADO) {
        require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario_Bloqueado.php");
        require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");

        $predicado = [
            'usblo_id_usuario'  => base64_decode($_GET['idU']),
            'usblo_institucion' => base64_decode($_GET['inst']),
            'usblo_year'        => date("Y")
        ];
    
        $campos = "usblo_motivo";
        $consultaMotivo = Administrativo_Usuario_Usuario_Bloqueado::Select($predicado, $campos, BD_ADMIN);
        $datosMotivo = $consultaMotivo->fetch(PDO::FETCH_ASSOC);
        
        $motivo = !empty($datosMotivo['usblo_motivo']) ? $datosMotivo['usblo_motivo'] : "Motivo no registrado";
        $telefono = !empty($informacion_inst['info_telefono']) ? $informacion_inst['info_telefono'] : "";
    }
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
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="../config-general/assets-login-2023/css/styles.css" rel="stylesheet" />

</head>

<body>
    <div class="login-container">
        <div class="vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2" id="login">
                        <form method="post" action="controlador/autentico.php" class="needs-validation" novalidate>
                            <?php include("../config-general/mensajes-informativos.php"); ?>
		                        <input type="hidden" name="urlDefault" value="<?php if(isset($_GET["urlDefault"])) echo $_GET["urlDefault"];?>" />
                                <input type="hidden" name="directory"  value="<?php if(isset($_GET["directory"]))  echo $_GET["directory"]; ?>" />
                                <img class="mb-4" src="<?=$logoIndex;?>" width="<?=$logoWidth;?>">
                            
                            <div class=" form-floating mt-3">
                                <input type="text" class="form-control input-login" id="emailInput" name="Usuario"
                                    placeholder="Usuario" required>
                                <label for="emailInput">Usuario</label>
                                <div class="invalid-feedback">Por favor ingrese un usuario válido.</div>
                            </div>

                            <div class="form-floating input-group mt-3">
                                <input type="password" class="form-control input-login" id="password" name="Clave"
                                    placeholder="Password" required>
                                <button class="btn btn-outline-secondary input-group-text toggle-password"
                                    type="button">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                                <label for="password">Contraseña</label>
                                <div class="invalid-feedback">Por favor ingresa tu contraseña para continuar</div>
                                <div class="form-text" id="caps-lock-message" style="display: none;">Mayúsculas
                                    activadas</div>
                            </div><br>

                            <?php
                            if (!empty($_GET["error"]) && $_GET["error"] == 3) {
                            $numA1 = rand(1, 10);
                            $numA2 = rand(1, 10);
                            $resultadoA = $numA1 + $numA2;
                            ?>
                            <p style="color: tomato;"><b>Valida que no eres un Robot</b><br>
                                Escribe el resultado de la siguiente operación.</p>
                            <div class=" form-floating mt-3">
                            <input type="hidden" name="sumaReal" value="<?= md5($resultadoA); ?>" />
                                <input type="text" class="form-control input-login" id="suma" name="suma" placeholder="Cuánto es <?= $numA1 . "+" . $numA2; ?>?" autocomplete="off" required>
                                <label for="suma">Cuánto es <?= $numA1 . "+" . $numA2; ?>?</label>
                                <div class="invalid-feedback">Por favor ingrese un numero válido.</div>
                            </div>
                            <?php } ?>
                            
                            <div class="form-floating mt-3" style="display: none;">
                                <select class="form-select select-invalid" id="year" name="year"
                                    aria-label="Default select example">
                                    <option value="" disabled selected>Seleccione un año</option>
                                    <option value="2022" selected>2022</option>
                                </select>
                                <label for="year">Año</label>
                                <div class="invalid-feedback">Por favor seleccione un año.</div>
                            </div>

                            <button class="w-75 btn btn-lg btn-primary btn-rounded mt-3" type="submit">Iniciar sesión</button>
                        </form>

                        <div class="d-flex justify-content-center mt-3">
                            <a class="forgot-password" id="forgot-password" href="recuperar-clave.php">¿Has olvidado tu contraseña?</a>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <p><a href="https://docs.google.com/forms/d/e/1FAIpQLSdiugXhzAj0Ysmt2gthO07tbvjxTA7CHcZqgzBpkefZC6T2qg/viewform" class="text-body btn-sm" target="_blank">¿Requieres soporte?</a></p>
                        </div>

                        <div class="d-block justify-content-center mt-4">
                            <p>¿Quieres registrar tu Institución?</p>
                            <a href="registro.php" class="btn btn-xs btn-secondary btn-rounded">Crear cuenta ahora</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="logo-container vertical-center">
            <lottie-player src="<?php if(!empty($datosContactoSintia['dtc_animacion_login'])) echo $datosContactoSintia['dtc_animacion_login'];?>" background="transparent"
                speed="1" style="width: 100%; height: 100%;" loop autoplay></lottie-player>
            <!--<img src="JhormanTesterDavid.gif" alt="Sherman" style="width: 100%;">-->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="../config-general/assets-login-2023/js/pages/login.js"></script>
    <script>
        $(document).ready(function () {
            $('.form-select').select2({
                theme: 'bootstrap-5'
            });

            $('.select2').on('select2:open', function () {
                $(this).parent().find('.select2-selection--single').addClass('form-control');
            });

            $('form').on('submit', function (e) {
                if (!this.checkValidity()) {
                    $('#institution').addClass('is-invalid');
                    this.classList.add('was-validated');
                    e.preventDefault();
                } else {
                    $('#institution').removeClass('is-invalid');
                }
                
            });
        });
    </script>
    <!-- Core theme JS-->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

</body>

</html>