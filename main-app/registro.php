<?php
session_start();
require_once("../conexion.php");
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
        <div class=" vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2" id="login">
                        <?php if (empty($_POST['siguiente'])) { ?>
                            <form method="post" action="registro.php" class="needs-validation" novalidate>
                                <?php include("../config-general/mensajes-informativos.php"); ?>
                                <input type="hidden" name="urlDefault" value="<?= !empty($_REQUEST["urlDefault"]) ? $_REQUEST["urlDefault"] : ""; ?>" />
                                <input type="hidden" name="plan" value="<?= !empty($_REQUEST["plan"]) ? $_REQUEST["plan"] : ""; ?>" />
                                <input type="hidden" name="modAdicional[]" value="<?= !empty($_REQUEST["modAdicional"]) ? $_REQUEST["modAdicional"] : ""; ?>" />
                                <input type="hidden" name="paquetes[]" value="<?= !empty($_REQUEST["paquetes"]) ? $_REQUEST["paquetes"] : ""; ?>" />
                                <input type="hidden" name="cuotas" value="<?= !empty($_REQUEST["cuotas"]) ? $_REQUEST["cuotas"] : ""; ?>" />
                                <img class="mb-4" src="../config-general/assets-login-2023/img/logo.png" width="100">

                                <div class="form-floating mt-3">
                                    <select class="form-select select2" id="institucion" name="institucion" aria-label="Default select example" required>
                                        <option value="">Tipo de Institución</option>
                                        <option value="<?=SCHOOL?>" <?= !empty($_REQUEST["institucion"]) && $_REQUEST["institucion"] == SCHOOL ? "selected" : ""; ?>>Colegio</option>
                                        <option value="<?=UNIVERSITY?>" <?= !empty($_REQUEST["institucion"]) && $_REQUEST["institucion"] == UNIVERSITY ? "selected" : ""; ?>>Universidad</option>
                                        <option value="<?=INSTITUTE?>" <?= !empty($_REQUEST["institucion"]) && $_REQUEST["institucion"] == INSTITUTE ? "selected" : ""; ?>>Instituto</option>
                                        <option value="<?=KINDERGARTEN?>" <?= !empty($_REQUEST["institucion"]) && $_REQUEST["institucion"] == KINDERGARTEN ? "selected" : ""; ?>>Jardin infantil</option>
                                    </select>
                                    <label for="institucion">Institucion</label>
                                    <div class="invalid-feedback">Por favor seleccione una institución.</div>
                                </div>

                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control input-login" name="nombreIns" placeholder="Institución" onchange="generarSiglas(this)" value="<?= !empty($_REQUEST["nombreIns"]) ? $_REQUEST["nombreIns"] : ""; ?>" required>
                                    <input type="hidden" name="siglasInst" id="siglasInst">
                                    <label for="emailInput">Institución</label>
                                    <div class="invalid-feedback">Por favor ingrese el nombre de su institución.</div>
                                </div>

                                <script type="text/javascript">
                                    function obtenerPrimerasLetras(frase) {
                                        // Divide la frase en palabras
                                        var palabras = frase.split(" ");
                                        var primerasLetras = "";

                                        // Itera sobre cada palabra y obtén la primera letra
                                        for (var i = 0; i < palabras.length; i++) {
                                            // Asegúrate de que la palabra no esté vacía antes de obtener la primera letra
                                            if (palabras[i].length > 0) {
                                                primerasLetras += palabras[i][0]+palabras[i][1]; // Añade la primera letra de la palabra
                                            }
                                        }

                                        return primerasLetras;
                                    }

                                    function generarSiglas(datos){
                                        var institucion = datos.value;
                                        var siglas = obtenerPrimerasLetras(institucion);
                                        document.getElementById("siglasInst").value = siglas.toUpperCase();
                                    }
                                </script> 
                                
                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control input-login" id="emailInput" name="usuario" value="<?= !empty($_REQUEST["usuario"]) ? $_REQUEST["usuario"] : ""; ?>" placeholder="documento" required>
                                    <label for="emailInput">Documento</label>
                                    <div class="invalid-feedback">Por favor ingrese su numero de documento sin puntos.</div>
                                </div>

                                <div class="form-floating input-group mt-3">
                                    <input type="password" class="form-control input-login" id="password" name="clave" value="<?= !empty($_REQUEST["clave"]) ? $_REQUEST["clave"] : ""; ?>" placeholder="Password" required>
                                    <button class="btn btn-outline-secondary input-group-text toggle-password" type="button">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                    <label for="password">Contraseña</label>
                                    <div class="invalid-feedback">usuario y/o contraseña invalido</div>
                                    <div class="form-text" id="caps-lock-message" style="display: none;">Mayúsculas activadas</div>
                                </div>

                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control input-login" name="nombre" value="<?= !empty($_REQUEST["nombre"]) ? $_REQUEST["nombre"] : ""; ?>" placeholder="Nombres" required>
                                    <label for="emailInput">Nombres</label>
                                    <div class="invalid-feedback">Por favor ingrese su nombre.</div>
                                </div>

                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control input-login" name="apellidos" value="<?= !empty($_REQUEST["apellidos"]) ? $_REQUEST["apellidos"] : ""; ?>" placeholder="Apellidos" required>
                                    <label for="emailInput">Apellidos</label>
                                    <div class="invalid-feedback">Por favor ingrese sus apellidos.</div>
                                </div>

                                <div class="form-floating mt-3">
                                    <input type="email" class="form-control input-login" name="email" value="<?= !empty($_REQUEST["email"]) ? $_REQUEST["email"] : ""; ?>" placeholder="email" required>
                                    <label for="emailInput">Email</label>
                                    <div class="invalid-feedback">Por favor ingrese un correo electrónico válido.</div>
                                </div>

                                <div class="form-floating mt-3">
                                    <input type="text" data-mask="(999) 999-9999" data-mask-reverse="true" class="form-control input-login" name="celular" value="<?= !empty($_REQUEST["celular"]) ? $_REQUEST["celular"] : ""; ?>" placeholder="Celular" required>
                                    <label for="emailInput">Celular</label>
                                    <div class="invalid-feedback">Por favor ingrese un numero celular válido.</div>
                                </div>
                                
                                <?php
                                    if(!empty($_REQUEST['error']) && $_REQUEST['error']==1){
                                        echo '<p class="text-center text-danger fs-12px mb-30">La validación ha sido incorrecta.</p>';
                                    }
                                ?>
                                <input class="w-75 btn btn-lg btn-primary btn-rounded mt-3" type="submit" name="siguiente" value="Siguiente"></input>
                                <div class="d-flex justify-content-center mt-5">
                                    <p><a href="index.php" class="text-body">Login</a></p>
                                </div>
                            </form>
                        <?php } if (!empty($_POST['siguiente'])) { ?>
                            <form method="post" id="miFormulario" class="needs-validation" novalidate>
                                <input type="hidden" name="urlDefault" value="<?= !empty($_REQUEST["urlDefault"]) ? $_REQUEST["urlDefault"] : ""; ?>" />
                                <input type="hidden" name="institucion" value="<?= !empty($_REQUEST["institucion"]) ? $_REQUEST["institucion"] : ""; ?>" />
                                <input type="hidden" name="nombreIns" value="<?= !empty($_REQUEST["nombreIns"]) ? $_REQUEST["nombreIns"] : ""; ?>" />
                                <input type="hidden" name="siglasInst" value="<?= !empty($_REQUEST["siglasInst"]) ? $_REQUEST["siglasInst"] : ""; ?>" />
                                <input type="hidden" name="usuario" value="<?= !empty($_REQUEST["usuario"]) ? $_REQUEST["usuario"] : ""; ?>" />
                                <input type="hidden" name="clave" value="<?= !empty($_REQUEST["clave"]) ? $_REQUEST["clave"] : ""; ?>" />
                                <input type="hidden" name="nombre" value="<?= !empty($_REQUEST["nombre"]) ? $_REQUEST["nombre"] : ""; ?>" />
                                <input type="hidden" name="apellidos" value="<?= !empty($_REQUEST["apellidos"]) ? $_REQUEST["apellidos"] : ""; ?>" />
                                <input type="hidden" name="email" value="<?= !empty($_REQUEST["email"]) ? $_REQUEST["email"] : ""; ?>" />
                                <input type="hidden" name="celular" value="<?= !empty($_REQUEST["celular"]) ? $_REQUEST["celular"] : ""; ?>" />
                                <img class="mb-4" src="../config-general/assets-login-2023/img/logo.png" width="100"> 

                                <div class="form-floating mt-3">
                                    <select class="form-select select2" id="plan" name="plan" aria-label="Default select example" required>
                                        <option value="">Escoge un plan</option>
                                        <?php
                                            $consultaPlanes = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".planes_sintia WHERE plns_tipo='".PLANES."'");
                                            while ($planes = mysqli_fetch_array($consultaPlanes, MYSQLI_BOTH)) {
                                        ?>
                                        <option value="<?=$planes['plns_id']?>" <?= !empty($_REQUEST["plan"]) && $_REQUEST["plan"] == $planes['plns_id'] ? "selected" : ""; ?>><?=$planes['plns_nombre']?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="plan">Escoja un plan</label>
                                    <div class="invalid-feedback">Por favor seleccione un plan.</div>
                                </div>

                                <div class="form-floating mt-3">
                                    <select class="form-select select2" id="modAdicional" name="modAdicional[]" multiple aria-label="Default select example">
                                        <option value="">Escoge los modulos adicionales</option>
                                        <?php
                                            $consultaModulos = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".modulos WHERE mod_estado=1");
                                            while ($modulos = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
                                        ?>
                                        <option value="<?=$modulos['mod_id']?>"><?=$modulos['mod_nombre']?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="modAdicional">Escoge los modulos adicionales</label>
                                    <div class="invalid-feedback">Por favor seleccione un modulo.</div>
                                </div>

                                <!-- Cambiar por consulta a paquetes -->
                                <div class="form-floating mt-3">
                                    <select class="form-select select2" id="paquetes" name="paquetes[]" multiple aria-label="Default select example">
                                        <option value="">Escoge los paquetes adicionales</option>
                                        <option value="1"<?= !empty($_REQUEST["paquetes"]) && $_REQUEST["paquetes"] == 1 ? "selected" : ""; ?>>Paquete #1</option>
                                        <option value="2"<?= !empty($_REQUEST["paquetes"]) && $_REQUEST["paquetes"] == 2 ? "selected" : ""; ?>>Paquete #2</option>
                                        <option value="3"<?= !empty($_REQUEST["paquetes"]) && $_REQUEST["paquetes"] == 3 ? "selected" : ""; ?>>Paquete #3</option>
                                    </select>
                                    <label for="paquetes">Escoge los paquetes adicionales</label>
                                    <div class="invalid-feedback">Por favor seleccione un paquete.</div>
                                </div>

                                <div class="form-floating mt-3">
                                    <select class="form-select select2" id="cuotas" name="cuotas" aria-label="Default select example">
                                        <?php
                                            for($i=1; $i<=64; $i++) {
                                        ?>
                                        <option value="<?=$i?>" <?= !empty($_REQUEST["cuotas"]) && $_REQUEST["cuotas"] == $i ? "selected" : ""; ?>><?=$i?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="cuotas">A cuantas cuotas?</label>
                                </div>

                                <div class="form-floating mt-3">
                                    <?php
                                        $numA1 = rand(1, 10);
                                        $numA2 = rand(1, 10);
                                        $resultadoA = $numA1 + $numA2;
                                    ?>
                                    <input type="hidden" name="sumaReal" value="<?= md5($resultadoA); ?>" />
                                    <input type="text" class="form-control input-login" name="suma" value="<?= !empty($_REQUEST["suma"]) ? $_REQUEST["suma"] : ""; ?>" placeholder="Valida que no eres un Robot. ¿Cuánto es <?= $numA1 . "+" . $numA2; ?>?" required>
                                    <label for="emailInput">Valida que no eres un Robot. ¿Cuánto es <?= $numA1 . "+" . $numA2; ?>?</label>
                                    <div class="invalid-feedback">Por favor ingrese el resultado de la suma.</div>
                                </div>
                                
                                <!-- <button class="w-75 btn btn-lg btn-primary btn-rounded mt-3" type="submit" >Iniciar Prueba Gratis</button> -->
                                <button class="w-75 btn btn-lg btn-primary btn-rounded mt-3" type="button" onclick="enviarFormulario('registro-guardar.php')">Iniciar prueba gratis</button>
                                <button class="w-75 btn btn-lg btn-success btn-rounded mt-3" type="button" onclick="enviarFormulario('pagos-online/index.php')">Realizar pago</button>
                                <div class="d-flex justify-content-center mt-5">
                                    <p><a href="index.php" class="text-body">Login</a></p>
                                </div>
                            </form>

                            <script>
                                function enviarFormulario(accion) {
                                    var formulario = document.getElementById('miFormulario');
                                    formulario.action = accion; // Cambia la acción del formulario
                                    formulario.submit(); // Envía el formulario
                                }
                            </script>
                        <?php }  ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="logo-container vertical-center">
            <img src="registerM.png" alt="Mariana" style="width: 100%;">
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="../config-general/assets-login-2023/js/pages/login.js"></script>
    <script src="../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
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