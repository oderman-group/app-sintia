<?php

require_once("index-logica.php");

if (isset($_POST['usuariosEncontrados'])) {
    $usuario = base64_decode($_GET['valor']);
    $listaUsuarios = unserialize($_POST['usuariosEncontrados']);
    echo '<script type="text/javascript">
    window.onload = function() {
        $("#miModalUsuarios").modal("show");
    }
    </script>';
} else {
    $usuario = '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Plataforma Educativa SINTIA | Recuperar clave</title>
    <!-- Google fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" >
    <!-- Or for RTL support -->
    <link rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" >
    <link href="../config-general/assets-login-2023/css/styles.css" rel="stylesheet" >


    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

</head>

<body>

    <div class="login-container">
        <div class=" vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2" id="login">
                        <form method="post" action="recuperar-clave-enviar-codigo.php" class="needs-validation" novalidate>

                            <?php include '../config-general/mensajes-informativos.php'; ?>

                            <img class="mb-4" src="../config-general/assets-login-2023/img/logo.png" width="100">

                            <div class="form-floating mt-3">
                                <input type="text" class="form-control input-login" id="emailInput" name="Usuario"
                                    placeholder="Usuario" value="<?php echo $usuario ?>" required>
                                <label for="emailInput">Usuario de acceso o Email registrado</label>
                                <div class="invalid-feedback">Por favor ingrese su correo electrónico o usuario de acceso.</div>
                            </div>

                            <div class="text-center text-lg-center mt-4 pt-2">
                                <button type="submit" class="w-75 btn btn-lg btn-primary btn-rounded mt-3">Recuperar contraseña</button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center" style="margin-top: 50px;">
                                <!-- Checkbox -->
                                <a href="index.php" class="text-body">Regresar al login</a>
                                <a href="https://docs.google.com/forms/d/e/1FAIpQLSdiugXhzAj0Ysmt2gthO07tbvjxTA7CHcZqgzBpkefZC6T2qg/viewform" class="text-body" target="_blank">¿Requieres soporte?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="logo-container vertical-center">
            <lottie-player src="https://assets7.lottiefiles.com/packages/lf20_hzgq1iov.json" background="transparent"
                speed="1" style="width: 500px; height: 500px;" loop autoplay></lottie-player>
        </div>
    </div>

    <?php 
    if (isset($_POST['usuariosEncontrados'])) {
        include 'compartido/modal-lista-usuarios.php';
    }
     ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="../config-general/assets-login-2023/js/pages/login.js"></script>

    <!-- Core theme JS-->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>


</body>

</html>