<?php
include("session.php");

$idPaginaInterna = 'DV0034';

Modulos::verificarPermisoDev();

include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php"); 

$year=$_SESSION["bd"];
if(!empty($_GET['year'])){
    $year=base64_decode($_GET['year']);
}
try{
    $consultaInformacion = mysqli_query($conexion, "SELECT general_informacion.*, ins_siglas, ins_years, ins_id FROM " . $baseDatosServicios . ".general_informacion 
    INNER JOIN ".$baseDatosServicios.".instituciones ON ins_id=info_institucion
    WHERE info_institucion='" . base64_decode($_GET["id"]) . "' AND info_year='" . $year . "'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$datosInstitucion = mysqli_fetch_array($consultaInformacion, MYSQLI_BOTH);
?>

<!-- steps -->
<link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css">


<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <!-- start header -->
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title"><?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> Institucional de <b><?=$datosInstitucion['ins_siglas'];?></b> (<?=$year;?>)</div>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li class="active"><?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> Institucional de <b><?=$datosInstitucion['ins_siglas'];?></b></li>
                        </ol>
                    </div>
                </div>


                <!-- wizard with validation-->
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                            include("../../config-general/mensajes-informativos.php");
                            include("includes/barra-superior-dev-instituciones-configuracion-informacion.php");
                        ?>
                        <br>
                        <div class="card-box">
                            <div class="card-head">
                                <header>Configuraci&oacute;n Institucional de <b><?=$datosInstitucion['ins_siglas'];?></b></header>
                            </div>
                            <div class="card-body">
                                <form name="example_advanced_form" id="example-advanced-form" action="dev-instituciones-Informacion-actualizar.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= base64_decode($_GET["id"]) ?>">
                                    <h3>Información Basica</h3>
                                    <fieldset>
                                        <?php
                                        $infoLogo = "sintia-logo-2023.png";
                                        if (isset($datosInstitucion["info_logo"]) && $datosInstitucion["info_logo"] != "") {
                                            $infoLogo = $datosInstitucion["info_logo"];
                                        }
                                        ?>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Año Actual</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="year" class="form-control col-sm-2" value="<?=$year ?>" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Logo</label>
                                            <div class="col-sm-4">
                                                <img src="../files/images/logo/<?= $infoLogo; ?>" alt="<?= $infoLogo; ?>" style="width: 200px; height: 150px;">
                                                <input type="file" name="logo" class="form-control">
                                            </div>
                                        </div>

                                        <input type="hidden" name="idCI" value="<?= $datosInstitucion["info_id"]; ?>">
                                        <input type="hidden" name="logoAnterior" value="<?= $datosInstitucion["info_logo"]; ?>">


                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">NIT</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="nitI" class="form-control" required value="<?= $datosInstitucion["info_nit"]; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre de la institución</label>
                                            <div class="col-sm-4">
                                                <input name="nomInstI" class="form-control" type="text" required value="<?= $datosInstitucion["info_nombre"]; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Direccion</label>
                                            <div class="col-sm-4">
                                                <input name="direccionI" class="form-control" type="text" required value="<?= $datosInstitucion["info_direccion"]; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Telefono</label>
                                            <div class="col-sm-4">
                                                <input name="telI" class="form-control" type="text" required value="<?= $datosInstitucion["info_telefono"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Clase</label>
                                            <div class="col-sm-4">
                                                <input name="calseI" class="form-control" type="text" required value="<?= $datosInstitucion["info_clase"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Caracter</label>
                                            <div class="col-sm-4">
                                                <input name="caracterI" class="form-control" type="text" required value="<?= $datosInstitucion["info_caracter"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Calendario</label>
                                            <div class="col-sm-4">
                                                <input name="calendarioI" class="form-control" type="text" required value="<?= $datosInstitucion["info_calendario"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Jornada</label>
                                            <div class="col-sm-4">
                                                <input name="jornadaI" class="form-control" type="text" required value="<?= $datosInstitucion["info_jornada"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Horario</label>
                                            <div class="col-sm-4">
                                                <input name="horarioI" class="form-control" type="text" required value="<?= $datosInstitucion["info_horario"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Niveles</label>
                                            <div class="col-sm-4">
                                                <input name="nivelesI" class="form-control" type="text" required value="<?= $datosInstitucion["info_niveles"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Modalidad</label>
                                            <div class="col-sm-4">
                                                <input name="modalidadI" class="form-control" type="text" required value="<?= $datosInstitucion["info_modalidad"] ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Propietario</label>
                                            <div class="col-sm-4">
                                                <input name="propietarioI" class="form-control" type="text" required value="<?= $datosInstitucion["info_propietario"] ?>">
                                            </div>
                                        </div>


                                    </fieldset>

                                    <h3>Información académica</h3>
                                    <fieldset>


                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Rector(a)</label>
                                            <div class="col-sm-4">
                                                <?php
                                                $consulta = UsuariosPadre::consultaUsuariosPorTipo(TIPO_DIRECTIVO, $datosInstitucion["ins_id"], $year);
                                                ?>
                                                <select class="form-control" name="rectorI">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    while ($r = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                        if ($datosInstitucion["info_rector"] == $r["uss_id"]) {
                                                    ?>
                                                            <option value="<?=$r["uss_id"]; ?>" selected><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                        <?php } else {

                                                        ?>
                                                            <option value="<?=$r["uss_id"]; ?>"><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Secretario(a) Académico(a)</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="secretarioI">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    $consulta = UsuariosPadre::consultaUsuariosPorTipo(TIPO_DIRECTIVO, $datosInstitucion["ins_id"], $year);
                                                    while ($r = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                        if ($datosInstitucion["info_secretaria_academica"] == $r["uss_id"]) {
                                                    ?>
                                                            <option value="<?=$r["uss_id"]; ?>" selected><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                        <?php } else {

                                                        ?>
                                                            <option value="<?=$r["uss_id"]; ?>"><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Coordinador(a) Académico(a)</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="coordinadorI">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    $consulta = UsuariosPadre::consultaUsuariosPorTipo(TIPO_DIRECTIVO, $datosInstitucion["ins_id"], $year);
                                                    while ($r = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                        if ($datosInstitucion["info_coordinador_academico"] == $r["uss_id"]) {
                                                    ?>
                                                            <option value="<?=$r["uss_id"]; ?>" selected><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                        <?php } else {

                                                        ?>
                                                            <option value="<?=$r["uss_id"]; ?>"><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Tesorero(a)</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="tesoreroI">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    $consulta = UsuariosPadre::consultaUsuariosPorTipo(TIPO_DIRECTIVO, $datosInstitucion["ins_id"], $year);
                                                    while ($r = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                        if ($datosInstitucion["info_tesorero"] == $r["uss_id"]) {
                                                    ?>
                                                            <option value="<?=$r["uss_id"]; ?>" selected><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                        <?php } else {

                                                        ?>
                                                            <option value="<?=$r["uss_id"]; ?>"><?=UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                    </fieldset>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="wizard" style="display: none;"></div>

            </div>
        </div>
        <!-- end page content -->
        <?php // include("../compartido/panel-configuracion.php");
        ?>
    </div>
    <!-- end page container -->
    <!-- start footer -->
    <?php include("../compartido/footer.php"); ?>
    <!-- end footer -->
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="../../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- steps -->
<script src="../../config-general/assets/plugins/steps/jquery.steps.js"></script>
<script src="../../config-general/assets/js/pages/steps/steps-data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>

<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
<!-- end js include path -->

</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/wizard.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:55 GMT -->

</html>