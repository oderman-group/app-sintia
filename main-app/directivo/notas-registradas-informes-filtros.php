<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0200'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>

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
                            <div class="page-title">Notas declaradas y registradas</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="informes-todos.php" onClick="deseaRegresar(this)">Informes Todos</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Notas declaradas y registradas</li>
                        </ol>
                    </div>
                </div>
                <form name="formularioGuardar" action="../compartido/reporte-notas.php" method="post" target="_blank">
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple">POR CURSO </header>
                                <div class="panel-body">

                                      <div class="form-group row">
                                        <label class="col-sm-2 control-label">Curso</label>
                                        <div class="col-sm-8">
                                            <select class="form-control  select2" style="width: 810.666px;" name="grado" id="grado" required onchange="habilitarGrupoPeriodo()">
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                $opcionesConsulta = Grados::traerGradosInstitucion($config);
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    $disabled = '';
                                                    if($opcionesDatos['gra_estado']=='0') $disabled = 'disabled';
                                                ?>
                                                    <option value="<?=$opcionesDatos['gra_id'];?>" <?=$disabled;?>><?=$opcionesDatos['gra_id'].". ".strtoupper($opcionesDatos['gra_nombre']);?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Grupo</label>
                                        <div class="col-sm-4">
                                            <?php
                                            try{
                                                $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_grupos WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                                            } catch (Exception $e) {
                                                include("../compartido/error-catch-to-report.php");
                                            }
                                            ?>
                                            <select class="form-control  select2" style="width: 810.666px;" id="grupo" name="grupo" onchange="traerCargas()" disabled>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                ?>
                                                    <option value="<?=$opcionesDatos['gru_id'];?>"><?=$opcionesDatos['gru_id'].". ".strtoupper($opcionesDatos['gru_nombre']);?></option>
                                                <?php }?>
                                            </select>
                                              
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Periodo</label>
                                        <div class="col-sm-4">
                                            <select class="form-control  select2" style="width: 810.666px;" onchange="traerCargas()"  name="per" id="periodo" required disabled>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                $p = 1;
                                                while($p<=$config[19]){
                                                    echo '<option value="'.$p.'">Periodo '.$p.'</option>';	
                                                    $p++;
                                                }
                                                ?>
                                            </select>
                                            <span id="mensaje" style="color: #6017dc; display:none;">Espere un momento por favor.</span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row" id="carga-container">
                                        <label class="col-sm-2 control-label">Carga</label>
                                        <div class="col-sm-8">
                                            <select class="form-control  select2" style="width: 810.666px;" name="carga" id="carga" required>
                                            </select>
                                            <script type="application/javascript">
                                                 $(document).ready(traerCargas(document.getElementById('grupo')));
                                                function habilitarGrupoPeriodo() {
                                                    var curso = document.getElementById('grado').value;
                                                    var grupo = document.getElementById('grupo');
                                                    var periodo = document.getElementById('periodo');

                                                    if (curso) {
                                                        grupo.removeAttribute('disabled');
                                                        periodo.removeAttribute('disabled');
                                                        traerCargas(grupo);
                                                    } else {
                                                        periodo.setAttribute('disabled', true);
                                                        grupo.setAttribute('disabled', true);
                                                        $('#carga-container').hide();
                                                    }
                                                }
                                                
                                                function traerCargas(enviada){
                                                var grado = $('#grado').val();
                                                var grupo =$('#grupo').val();
                                                var periodo = $('#periodo').val();
                                                if (grado === "" || grupo === "") {
                                                    $('#carga-container').hide();
                                                    return;
                                                }

                                                datos = "grado="+(grado)+
                                                        "&grupo="+(grupo)+
                                                        "&periodo="+(periodo);
                                                console.log(datos);
                                                $('#mensaje').show();
                                                $.ajax({
                                                        type: "POST",
                                                        url: "ajax-traer-cargas.php",
                                                        data: datos,
                                                        success: function(response)
                                                        {
                                                            $('#mensaje').hide();
                                                            $('#carga-container').show();
                                                            $('#carga').empty();
                                                            $('#carga').append(response);
                                                        }
                                                });
                                                }
                                            </script>
                                            
                                        </div>
                                    </div>
                                    
                                    
                                    <input type="submit" class="btn btn-primary" value="Generar informe">&nbsp;

                                    <a href="javascript:void(0);" name="informes-todos.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>




                                </div>
                            </div>
                        </div>
                       
                    </div>
                </form>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");
            ?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php"); ?>
    </div>
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
    </body>

    <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

    </html>