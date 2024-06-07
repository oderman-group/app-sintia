<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");

$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionesConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT ins.*, cfgi.* FROM instituciones ins
INNER JOIN instituciones_modulos ON ipmod_institucion=ins_id AND ipmod_modulo=".MODULO_ADMISIONES." 
INNER JOIN ".BD_ADMISIONES.".config_instituciones cfgi ON cfgi_id_institucion=ins_id AND cfgi_inscripciones_activas=1 AND cfgi_year = ".date("Y")." 
WHERE ins_estado = 1 AND ins_enviroment='".ENVIROMENT."'
UNION
SELECT ins.*, cfgi.* FROM instituciones ins
INNER JOIN instituciones_paquetes_extras ON paqext_institucion=ins_id AND paqext_id_paquete=".MODULO_ADMISIONES." AND paqext_tipo='".MODULOS."'
INNER JOIN ".BD_ADMISIONES.".config_instituciones cfgi ON cfgi_id_institucion=ins_id AND cfgi_inscripciones_activas=1 AND cfgi_year = ".date("Y")." 
WHERE ins_estado = 1 AND ins_enviroment='".ENVIROMENT."'");
$institucionesCantidad = mysqli_num_rows($institucionesConsulta);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admisiones | Plataforma sintia</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

        <!-- favicon -->
        <link rel="shortcut icon" href="../sintia-icono.png" />
    </head>
    
<style>
body {
    background-image: url(./../../config-general/assets-login-2023/img/bg-login.png);
    display: grid;
    grid-template-columns: 100%;
    height: 100vh;
    width: 100vw;
}
</style>

    <body>
        <div class="container-fluid" style="padding-left: 0; padding-right: 0;">
            <?php include("menu.php"); ?>
            <div class="row justify-content-md-center">
                <div class="col col-lg-12">
                    <hr class="my-4">
                    <?php include("alertas.php"); ?>
                    <div style="text-align:center;"><img class="mb-4" src="../../config-general/assets-login-2023/img/logo.png" width="100"></div>
                    <?php if($institucionesCantidad > 0) {?>
                    <h5 style="text-align: center;">ESCOGE UNA DE NUESTRAS INSTITUCIONES PARA EMPEZAR</h5>
                    <div class="col col-lg-12">
                        <form action="admision.php" method="post">
                            <div class="form-row justify-content-md-center">
                                <div class="form-group col-md-3">
                                    <select id="idInst" name="idInst" class="form-control" required>
                                        <option value="" selected>Escoja una Institución...</option>
                                        <?php
                                        while($instituciones = mysqli_fetch_array($institucionesConsulta, MYSQLI_BOTH)){
                                        $selected = (isset($_GET['inst']) and $_GET['inst']==$instituciones['ins_id']) ? 'selected' : '';
                                        ?>
                                        <option value="<?=base64_encode($instituciones['ins_id']);?>" <?=$selected;?>><?=$instituciones['ins_siglas'];?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div style="text-align: center;">
                                <button type="submit" class="btn btn-lg" style="background-color:<?=$fondoBarra;?>; color:<?=$colorTexto;?>;">INICIAR PROCESO</button>
                            </div>
                        </form>
                    </div>
                    <?php } else {?>
                        <div class="alert alert-success w-50 mx-auto" style="width: 200px;" role="alert">
                            <h4 class="alert-heading">No se encontraron instituciones disponibles</h4>
                            <p>En este momento no hay insiticiones con proceso de inscripción abierto. Consulte más adelante.</p>
                        </div>
                        <?php }?>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    </body>
</html>