<?php
include("session.php");
$idPaginaInterna = 'DV0056';
include("../compartido/historial-acciones-guardar.php");
Modulos::verificarPermisoDev();
include("../compartido/head.php");
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
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php
        include("../compartido/encabezado.php");
        include("../compartido/panel-color.php");
    ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title">Agregar Empresas</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="mps-empresas.php" onClick="deseaRegresar(this)"><?=$frases[411][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Agregar Empresas</li>
                        </ol>
                    </div>
                </div>
                <span style="color: blue; font-size: 15px;" id="resp"></span>
                <div class="row">

                    <div class="col-sm-12">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>

                        <div class="panel">
                            <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
                            <div class="panel-body">


                                <form name="formularioGuardar" action="mps-empresas-guardar.php" method="post" enctype="multipart/form-data">

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Nombre Empresas <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="nombre" class="form-control" id="nombre" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Email de contacto <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="email" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Teléfono de contacto <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="telefono" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Sector de la Empresas <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <select id="multiple" class="form-control select2-multiple" multiple name="sector[]" required aucomplete="off">
                                            <?php
                                            $infoConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosMarketPlace.".servicios_categorias");
                                            while($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)){
                                            ?>	
                                                <option value="<?=$infoDatos['svcat_id'];?>"><?=strtoupper($infoDatos['svcat_nombre']);?></option>
                                            <?php }?>	
                                            </select>
                                            <span style="color: navy;">Seleccione al menos un sector.</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Pagina Web</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="web" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">Institución <span style="color: red;">(*)</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-control select2"  name="institucion" id="institucion" aucomplete="off" require onchange="mostrarSelects(this)">
                                                <option value="0">Escoje una opción</option>
                                                <?php
                                                $infoConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones WHERE ins_estado=1 AND ins_enviroment='".ENVIROMENT."'");
                                                while($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)){
                                                ?>	
                                                    <option value="<?=$infoDatos['ins_id'];?>"><?=strtoupper($infoDatos['ins_nombre']);?></option>
                                                <?php }?>	
                                            </select>
                                        </div>
                                    </div>

                                    <div id="selectsContainer">
                                    </div>

                                    <script type="text/javascript">
                                        function mostrarSelects(selectElement) {
                                            // Obtener las opcion seleccionada del select
                                            var opcionSeleccionada = selectElement.value;

                                            // Obtener el div contenedor donde se mostrarán los selects adicionales
                                            var selectsContainer = document.getElementById('selectsContainer');
                                            selectsContainer.innerHTML = '';
                                            
                                            if(opcionSeleccionada>0){
                                                var datos = "insti="+opcionSeleccionada;
                                                $.ajax({
                                                    type: "POST",
                                                    url: "ajax-mps-listar-usuarios.php",
                                                    data: datos,
                                                    success: function(data){
                                                        $('#selectsContainer').empty().hide().html(data).show(1);
                                                    }

                                                });
                                            }
                                        }
                                    </script>

                                <?php $botones = new botonesGuardar("mps-empresas.php",Modulos::validarPermisoEdicion()); ?>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- end page content -->
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
</html>