<?php
include("session.php");
$idPaginaInterna = 'DT0064';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

$consultaCurso=mysqli_query($conexion, "SELECT * FROM academico_grados WHERE gra_id=".$_GET["id"]);
$resultadoCurso=mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
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
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Agregar Cursos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="cursos.php" onClick="deseaRegresar(this)"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Agregar Cursos</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						<div class="col-sm-3">
                        </div>
                        <div class="col-sm-9">
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                <div class="panel-body">
                                
                                    <form name="formularioGuardar" action="cursos-actualizar.php" method="post">
                                        <input type="hidden" name="id_curso" value="<?php echo $_GET["id"] ?>">
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Codigo</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="codigoC" class="form-control"  value="<?=$resultadoCurso["gra_codigo"]; ?>">
                                            </div>
                                        </div>	
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre Curso</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="nombreC" class="form-control" value="<?=$resultadoCurso["gra_nombre"]; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Formato Boletin</label>
                                            <div class="col-sm-2">
                                                <select class="form-control  select2" name="formatoB" required>
                                                    <option value="">Seleccione una opci�n</option>
                                                    <?php
                                                        for($i=1;$i<=4;$i++){
                                                            $select='';
					                                        if($resultadoCurso["gra_formato_boletin"]==$i){$select='selected';}
                                                    ?>
                                                        <option value="<?=$i; ?>" <?=$select; ?>><?=$i; ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nota Minima</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="notaMin" class="form-control"  value="<?=$resultadoCurso["gra_nota_minima"]; ?>">
                                            </div>
                                        </div>	
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Periodos</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="periodosC" class="form-control"  value="<?=$resultadoCurso["gra_periodos"]; ?>">
                                            </div>
                                        </div>	
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Valor Matricula</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="valorM" class="form-control" value="<?=$resultadoCurso["gra_valor_matricula"]; ?>">
                                            </div>
                                        </div>	
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Valor Pension</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="valorP" class="form-control" value="<?=$resultadoCurso["gra_valor_pension"]; ?>">
                                            </div>
                                        </div>	
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Curso Siguiente</label>
                                            <div class="col-sm-10">
                                                <?php
                                                $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_grados ORDER BY gra_vocal ");
                                                ?>
                                                <select class="form-control  select2" name="graSiguiente" >
                                                    <option value="">Seleccione una opci�n</option>
                                                    <?php
                                                    while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                            $select='';
					                                        if($resultadoCurso["gra_grado_siguiente"]==$opcionesDatos[0]){$select='selected';}
                                                    ?>
                                                        <option value="<?=$opcionesDatos[0];?>" <?=$select; ?>><?=strtoupper($opcionesDatos['gra_nombre']);?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Curso Anterior</label>
                                            <div class="col-sm-10">
                                                <?php
                                                $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_grados ORDER BY gra_vocal ");
                                                ?>
                                                <select class="form-control  select2" name="graAnterior" >
                                                    <option value="">Seleccione una opci�n</option>
                                                    <?php
                                                    while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                            $select='';
					                                        if($resultadoCurso["gra_grado_anterior"]==$opcionesDatos[0]){$select='selected';}
                                                    ?>
                                                        <option value="<?=$opcionesDatos[0];?>" <?=$select; ?>><?=strtoupper($opcionesDatos['gra_nombre']);?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nivel Educativo</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="nivel" >
                                                    <option value="">Seleccione una opci�n</option>
                                                    <option value="1" <?php if($resultadoCurso['gra_nivel']==1){ echo 'selected'; } ?>>Educación Precolar</option>
                                                    <option value="2" <?php if($resultadoCurso['gra_nivel']==2){ echo 'selected'; } ?>>Educación Basica Primaria</option>
                                                    <option value="3" <?php if($resultadoCurso['gra_nivel']==3){ echo 'selected'; } ?>>Educación Basica Secundaria</option>
                                                    <option value="4" <?php if($resultadoCurso['gra_nivel']==4){ echo 'selected'; } ?>>Educación Media</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if($datosUsuarioActual[3]==1) {?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Estado</label>
                                            <div class="col-sm-2">
                                                <select class="form-control  select2" name="estado" >
                                                    <option value="">Seleccione una opci�n</option>
                                                    <option value="1" <?php if($resultadoCurso['gra_estado']==1){ echo 'selected'; } ?>>Activo</option>
                                                    <option value="0" <?php if($resultadoCurso['gra_estado']==0){ echo 'selected'; } ?>>Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php }?>


                                        <input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
                                        
                                        <a href="#" name="cursos.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
                                    </form>
                                </div>
                            </div>
                        </div>
						
                    </div>

                </div>
                <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>