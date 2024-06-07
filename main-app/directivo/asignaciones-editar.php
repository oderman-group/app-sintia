<?php
include("session.php");
$idPaginaInterna = 'DT0321';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Asignaciones.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$id = '';
if (!empty($_GET['id'])) {
    $id = base64_decode($_GET['id']);;
}

$resultado = Asignaciones::traerDatosAsignacion($conexion, $config, $id);
$cursos = !empty($resultado['gal_id_curso']) ? explode(",", $resultado['gal_id_curso']) : array();

$iniciadas = Asignaciones::consultarCantAsignacionesEmpezadas($conexion, $config, $resultado['gal_id']);

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion() || $iniciadas > 0) {
    $disabledPermiso = "disabled";
}
?>

<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- dropzone -->
<link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="../js/Asignaciones.js" ></script>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> Asignación</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="asignaciones.php?idE=<?= base64_encode($resultado['gal_id_evaluacion']); ?>" onClick="deseaRegresar(this)">Asignaciones</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> Asignación</li>
                        </ol>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-12">
                        <?php
                        include("../../config-general/mensajes-informativos.php");
                        ?>
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">Asignación </header>
                            <div class="panel-body">


                                <form name="formularioGuardar" action="asignaciones-actualizar.php" method="post">
                                    <input type="hidden" value="<?= $resultado['gal_id']; ?>" name="id">
                                    <input type="hidden" value="<?= $resultado['gal_id_evaluacion']; ?>" name="idE">
                                    <input type="hidden" value="<?= $resultado['gal_tipo']; ?>" name="tipoEncuestaAnterior">
                                    <input type="hidden" value="<?= $resultado['gal_id_evaluado']; ?>" name="evaluadoAnterior">
                                    <input type="hidden" value="<?= $resultado['gal_tipo_evaluador']; ?>" name="evaluadorAnterior">
                                    <input type="hidden" value="<?= $resultado['gal_id_curso']; ?>" name="evaluadorCursosAnterior">
                                    <input type="hidden" value="<?= $resultado['gal_limite_evaluadores']; ?>" name="limiteEvaluadoresAnterior">
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Tipo de Encuesta
                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="A que o quien se va a enfocar esta asignación."><i class="fa fa-question"></i></button>
                                        </label>
                                        <div class="col-sm-4">
                                            <select class="form-control  select2" id="tipoEncuesta" name="tipoEncuesta" data-id-evaluado="<?= $resultado['gal_id_evaluado']; ?>" onchange="selectEvaluado(this)" <?= $disabledPermiso; ?>>
                                                <option value="">Escoja una opción</option>
                                                <option value="<?=DIRECTIVO?>" <?=$resultado['gal_tipo'] == DIRECTIVO ? "selected": "";?>><?=DIRECTIVO?></option>
                                                <option value="<?=DOCENTE?>" <?=$resultado['gal_tipo'] == DOCENTE ? "selected": "";?>><?=DOCENTE?></option>
                                                <option value="<?=AREA?>" <?=$resultado['gal_tipo'] == AREA ? "selected": "";?>><?=AREA?></option>
                                                <option value="<?=MATERIA?>" <?=$resultado['gal_tipo'] == MATERIA ? "selected": "";?>><?=MATERIA?></option>
                                                <option value="<?=CURSO?>" <?=$resultado['gal_tipo'] == CURSO ? "selected": "";?>><?=CURSO?></option>
                                            </select>
                                        </div>

                                        <label class="col-sm-2 control-label">Evaluado
                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Escoja los usuarios, curso, materia o areas que seran evaluadas."><i class="fa fa-question"></i></button>
                                        </label>
                                        <div class="col-sm-4">
                                            <select class="form-control  select2" id="evaluado" name="evaluado" <?= $disabledPermiso; ?>>
                                                <option value="">Escoja una opción</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Evaluador
                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Escoja el tipo de usuarios o cursos que realizaran esta encuesta."><i class="fa fa-question"></i></button>
                                        </label>
                                        <div class="col-sm-4">
                                            <select class="form-control  select2" name="evaluador" id="evaluador" onchange="mostrarSelectCurso(this)" <?= $disabledPermiso; ?>>
                                                <option value="">Escoja una opción</option>
                                                <option value="<?=ACUDIENTE?>" <?=$resultado['gal_tipo_evaluador'] == ACUDIENTE ? "selected": "";?>><?=ACUDIENTE?></option>
                                                <option value="<?=ESTUDIANTE?>" <?=$resultado['gal_tipo_evaluador'] == ESTUDIANTE ? "selected": "";?>><?=ESTUDIANTE?></option>
                                                <option value="<?=DOCENTE?>" <?=$resultado['gal_tipo_evaluador'] == DOCENTE ? "selected": "";?>><?=DOCENTE?></option>
                                                <option value="<?=DIRECTIVO?>" <?=$resultado['gal_tipo_evaluador'] == DIRECTIVO ? "selected": "";?>><?=DIRECTIVO?></option>
                                                <option value="<?=CURSO?>" <?=$resultado['gal_tipo_evaluador'] == CURSO ? "selected": "";?>><?=CURSO?></option>
                                            </select>
                                        </div>

                                        <div id="elementSelectCurso" style="display: none;">
                                            <label class="col-sm-2 control-label">Escoje los cursos
                                                <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Si el evaluador sera curso, especifique que cursos realizaran la encuesta."><i class="fa fa-question"></i></button>
                                            </label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2-multiple" style="width: 100%;" multiple name="evaluadorCursos[]" <?= $disabledPermiso; ?>>
                                                    <option value="">Escoja una opción</option>
                                                    <?php
                                                        $consultaCursos = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_grados WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                                                        while ($datosCursos = mysqli_fetch_array($consultaCursos, MYSQLI_BOTH)) {
                                                    ?>
                                                    <option value="<?=$datosCursos['gra_id']?>" <?=!empty($resultado['gal_id_curso']) && in_array($datosCursos['gra_id'], $cursos) ? "selected": "";?>><?=$datosCursos['gra_nombre']?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Limite
                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Con este campo determinas cuantos usuarios pueden realizar la encuesta."><i class="fa fa-question"></i></button>
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="number" min="0" name="limiteEvaluadores" class="form-control" autocomplete="off" <?=$iniciadas > 0 ? "disabled" : "";?> value="<?=$resultado['gal_limite_evaluadores'];?>" <?=$disabledPermiso;?>>
                                        </div>
                                    </div>
                                    
                                    <?php $botones = new botonesGuardar("asignaciones.php?idE=".base64_encode($resultado['gal_id_evaluacion']),Modulos::validarPermisoEdicion()); ?>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");
            ?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php"); ?>
    </div>
    <!-- start js include path -->
    <script src="../js/Asignaciones.js" ></script>
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
    <!-- Common js-->
    <script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js"></script>
    <script src="../../config-general/assets/js/theme-color.js"></script>
    <!-- notifications -->
    <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
    <!-- Material -->
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
    <!-- end js include path -->
    <script src="../ckeditor/ckeditor.js"></script>
    <script>
        $(document).ready(mostrarSelectCurso(document.getElementById('evaluador')));
        $(document).ready(selectEvaluado(document.getElementById('tipoEncuesta')));
    </script>
    </body>

    <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

    </html>