<?php
include("session.php");

$idPaginaInterna = 'DV0011';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

try{
    $consulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".instituciones 
    WHERE ins_id='" . base64_decode($_GET["id"]) . "' AND ins_enviroment='".ENVIROMENT."'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$datosInstitucion = mysqli_fetch_array($consulta, MYSQLI_BOTH);
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
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                            <div class="page-title">Editar Instituciones</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                        <li><a class="parent-item" href="javascript:void(0);" name="dev-instituciones.php" onClick="deseaRegresar(this)">Insituciones</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Editar Instituciones</li>
                        </ol>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-7">

                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">Institución</header>
                            <div class="panel-body">


                                <form name="formularioGuardar" action="dev-instituciones-guardar.php" method="post">
                                    <input type="hidden" name="id" value="<?=base64_decode($_GET["id"])?>">

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">ID</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control col-sm-2" value="<?= $datosInstitucion['ins_id']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">NIT</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="nit" class="form-control" value="<?= $datosInstitucion['ins_nit']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Nombre Institución</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="nombreInstitucion" class="form-control" value="<?=$datosInstitucion['ins_nombre']?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Siglas Institución</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="siglas" class="form-control" value="<?=$datosInstitucion['ins_siglas']?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Telefono Insitucional</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="telefonoPrincipal" class="form-control" value="<?= $datosInstitucion['ins_telefono_principal']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Email Insitucional</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="emailPrincipal" class="form-control" value="<?= $datosInstitucion['ins_email_institucion']; ?>">
                                        </div>
                                    </div>
												
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Ciudad</label>
                                        <div class="col-sm-4">
                                            <select class="form-control  select2" name="ciudad">
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                try{
                                                    $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
                                                    INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento 
                                                    ORDER BY ciu_nombre
                                                    ");
                                                } catch (Exception $e) {
                                                    include("../compartido/error-catch-to-report.php");
                                                }
                                                while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                                ?>
                                                <option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$datosInstitucion['ins_ciudad']){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Fecha de Inicio</label>
                                        <div class="col-sm-4">
                                            <input type="datetime" class="form-control" value="<?= $datosInstitucion['ins_fecha_inicio']; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Fecha de Renovacion</label>
                                        <div class="col-sm-4">
                                            <div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                                <input class="form-control" size="16" type="text" value="<?=$datosInstitucion['ins_fecha_renovacion'];?>">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span>
                                            </div>
                                        </div>
                                        <input type="hidden" id="dtp_input2" name="fechaRenovacion">
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Base de Datos</label>
                                        <div class="col-sm-4">
                                            <input type="datetime" class="form-control" value="<?= $datosInstitucion['ins_bd']; ?>" readonly>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Contacto Principal</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="contactoPrincipal" class="form-control" value="<?= $datosInstitucion['ins_contacto_principal']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Cargo</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="cargo" class="form-control" value="<?= $datosInstitucion['ins_cargo_contacto']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Celular</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="celular" class="form-control" value="<?= $datosInstitucion['ins_celular_contacto']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Email</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="email" class="form-control" value="<?= $datosInstitucion['ins_email_contacto']; ?>">
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Estado</label>
                                        <div class="col-sm-2">
                                            <select class="form-control  select2" name="estado">
                                                <option value="1" <?php if ($datosInstitucion['ins_estado'] == 1) { echo "selected"; } ?>>Activa</option>
                                                <option value="0" <?php if ($datosInstitucion['ins_estado'] == 0) { echo "selected"; } ?>>Inactiva</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Plan</label>
                                        <div class="col-sm-2">
                                            <select class="form-control  select2" name="plan">
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                try{
                                                    $consultaPlan = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".planes_sintia WHERE plns_tipo='".PLANES."'");
                                                } catch (Exception $e) {
                                                    include("../compartido/error-catch-to-report.php");
                                                }
                                                while($plan = mysqli_fetch_array($consultaPlan, MYSQLI_BOTH)){
                                                ?>
                                                <option value="<?=$plan['plns_id'];?>" <?php if($plan['plns_id']==$datosInstitucion['ins_id_plan']){echo "selected";}?>><?=$plan['plns_nombre']." (".$plan['plns_espacio_gb']."GB)"?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Deuda?</label>
                                        <div class="col-sm-8">
                                            <select class="form-control col-sm-2 select2" name="deuda">
                                                <option value="1" <?php if ($datosInstitucion['ins_deuda'] == 1) { echo "selected"; } ?>>SI</option>
                                                <option value="0" <?php if ($datosInstitucion['ins_deuda'] == 0) { echo "selected"; } ?>>NO</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Valor Deuda</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="valorDeuda" class="form-control" value="<?= $datosInstitucion['ins_valor_deuda']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Concepto de Deuda</label>
                                        <div class="col-sm-10">
                                            <textarea cols="80" id="editor1" name="conceptoDeuda" rows="10"><?= $datosInstitucion['ins_concepto_deuda']; ?></textarea>
                                        </div>
                                    </div>
                                    <select id="moduloSeleccionados" style="width: 100% !important" name="modulos[]" multiple hidden>
                                        <?php
                                            try{
                                                $consultaModulosInstitucion = mysqli_query($conexion, "SELECT ipmod_modulo FROM ".BD_ADMIN.".instituciones_modulos WHERE ipmod_institucion={$datosInstitucion['ins_id']}");
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
                                            $checkActivas = mysqli_num_rows($consultaModulosInstitucion);
                                            while($modulosInstitucion = mysqli_fetch_array($consultaModulosInstitucion, MYSQLI_BOTH)){
                                        ?>
                                        <option value="<?=$modulosInstitucion['ipmod_modulo'];?>" id="modulo<?=$modulosInstitucion['ipmod_modulo'];?>" selected><?=$modulosInstitucion['ipmod_modulo'];?></option>
                                        <?php }?>
                                    </select>

                                    <?php $botones = new botonesGuardar("dev-modulos.php",Modulos::validarPermisoEdicion()); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-5">

                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">Modulos</header>
                            <div class="panel-body">
                                <div class="card card-topline-purple">
                                    <div class="card-head">
                                        <?php
                                            try{
                                                $consultaModulos = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".modulos");
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
                                        ?>
                                        <header>Modulos Disponibles ( <label style="font-weight: bold;" id="cantSeleccionadas"></label>/<?= mysqli_num_rows($consultaModulos) ?> )
                                        </header>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <table id="example3" class="display" name="tabla1" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Cod</th>
                                                        <th>
                                                            <div class="input-group spinner col-sm-10">
                                                                <label class="switchToggle">
                                                                    <input type="checkbox" id="all">
                                                                    <span class="slider green round"></span>
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th>Modulo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    while ($modulos = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
                                                        if (Modulos::validarModulosExtras($conexion, $modulos['mod_id'], base64_decode($_GET["id"]))) { continue; }
                                                        $cheked = Modulos::verificarModulosDeInstitucion(base64_decode($_GET["id"]),$modulos['mod_id']) ? "checked" : "";
                                                    ?>
                                                        <tr>
                                                            <td><?= $modulos['mod_id']; ?></td>
                                                            <td>
                                                                <div class="input-group spinner col-sm-10">
                                                                    <label class="switchToggle">
                                                                        <input type="checkbox" class="check" onchange="seleccionarModulo(this)" value="<?= $modulos['mod_id']; ?>" <?= $cheked; ?>>
                                                                        <span class="slider green round"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td><?= $modulos['mod_nombre']; ?></td>

                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
    </div>
    <?php include("../compartido/footer.php"); ?>
    <script src="../js/Modulos.js" ></script>
    <!-- start js include path -->
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
    <!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js"></script>
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
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        CKEDITOR.replace('editor1');
    </script>
    </body>

    <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

    </html>