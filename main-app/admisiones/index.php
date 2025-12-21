<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once ROOT_PATH."/main-app/class/Modulos.php";

$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

// Obtener año actual y siguiente
$yearActual = (int)date('Y');
$yearSiguiente = $yearActual + 1;

// Consulta instituciones con inscripciones activas para año actual o siguiente
// Agrupando por ins_id y tomando el año más reciente
$institucionesConsulta = mysqli_query($conexionBaseDatosServicios, "
SELECT ins.*, 
       MAX(cfgi.cfgi_year_inscripcion) as cfgi_year_inscripcion,
       MAX(cfgi.cfgi_id) as cfgi_id,
       MAX(cfgi.cfgi_inscripciones_activas) as cfgi_inscripciones_activas
FROM instituciones ins
INNER JOIN ".BD_ADMISIONES.".config_instituciones cfgi 
    ON cfgi_id_institucion=ins_id 
    AND cfgi_inscripciones_activas=1
    AND (cfgi_year_inscripcion = {$yearActual} OR cfgi_year_inscripcion = {$yearSiguiente})
WHERE 
    ins_estado = 1 
    AND ins_enviroment='".ENVIROMENT."'
    AND (
        EXISTS (
            SELECT 1 FROM instituciones_modulos 
            WHERE ipmod_institucion=ins_id AND ipmod_modulo=".Modulos::MODULO_INSCRIPCIONES."
        )
        OR EXISTS (
            SELECT 1 FROM instituciones_paquetes_extras 
            WHERE paqext_institucion=ins_id 
            AND paqext_id_paquete=".Modulos::MODULO_INSCRIPCIONES." 
            AND paqext_tipo='".MODULOS."'
        )
    )
GROUP BY ins_id
ORDER BY ins_siglas
");

$institucionesCantidad = mysqli_num_rows($institucionesConsulta);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include(ROOT_PATH."/config-general/analytics/instituciones.php");?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admisiones | Plataforma sintia</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        
        <!-- Select2 para búsqueda en selector -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet" />

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
                                        $yearInscripcion = !empty($instituciones['cfgi_year_inscripcion']) ? ' ('.$instituciones['cfgi_year_inscripcion'].')' : '';
                                        ?>
                                        <option value="<?=base64_encode($instituciones['ins_id']);?>" <?=$selected;?>><?=$instituciones['ins_siglas'].$yearInscripcion;?></option>
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
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <script>
            $(document).ready(function() {
                // Inicializar Select2 en el selector de instituciones
                $('#idInst').select2({
                    theme: 'bootstrap',
                    placeholder: 'Escoja una Institución...',
                    allowClear: true,
                    language: {
                        noResults: function() {
                            return "No se encontraron instituciones";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    }
                });
            });
        </script>
    </body>
</html>