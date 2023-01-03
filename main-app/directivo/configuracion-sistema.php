<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0057';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>

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
                                <div class="page-title"><?=$frases[17][$datosUsuarioActual[8]];?> del Sistema</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active"><?=$frases[17][$datosUsuarioActual[8]];?> del Sistema</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


                        </div>
						
                        <div class="col-sm-9">
                                <?php
                                $consultaCfg=mysqli_query($conexion, "SELECT * FROM configuracion WHERE conf_id=1");
                                $cfg = mysqli_fetch_array($consultaCfg, MYSQLI_BOTH);
                                ?>
                                
                                <script type="application/javascript">
                                function verPrivado(){
                                    document.getElementById("privado").style.display="block";
                                }
                                </script>


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                    <div class="header">
                                    <h4><?=$frases[17][$datosUsuarioActual[8]];?></h4> <a href="#" style="color:rgb(255,255,255);" onDblClick="verPrivado();">...</a>
                                    </div>
                                   
									<form name="formularioGuardar" action="configuracion-sistema-guardar.php" method="post">
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Periodos a trabajar</label>
											<div class="col-sm-8">
												<input type="text" name="periodoTrabajar" class="form-control col-sm-2" value="<?=$cfg[19];?>">
                                                <span style="color:#F06; font-size:11px;">Las instituciones normalmente manejan 4 periodos. Los colegios semestralizados o de bachillerato acelerado manejan 2 periodos.</span>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Año Actual</label>
											<div class="col-sm-8">
												<input type="text" name="agno" class="form-control col-sm-2" value="<?=$cfg[1];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Periodos Actual</label>
											<div class="col-sm-8">
												<input type="text" name="periodo" class="form-control col-sm-2" value="<?=$cfg[2];?>">
											</div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estilo de calificación</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="estiloNotas" required>
                                                    <option value="">Seleccione una opción</option>
                                                    <?php 
                                                        $opcionesGeneralesConsulta = mysqli_query($conexion, "SELECT * FROM academico_categorias_notas");
                                                        while($opcionesGeneralesDatos = mysqli_fetch_array($opcionesGeneralesConsulta, MYSQLI_BOTH)){
                                                            if($cfg[22]==$opcionesGeneralesDatos[0])
                                                                echo '<option value="'.$opcionesGeneralesDatos[0].'" selected>'.$opcionesGeneralesDatos[1].'</option>';
                                                            else
                                                                echo '<option value="'.$opcionesGeneralesDatos[0].'">'.$opcionesGeneralesDatos[1].'</option>';	
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row"  style="background:rgb(255,255,204);">
											<label class="col-sm-2 control-label">N&uacute;mero m&aacute;ximo de indicadores o tipos de notas que puede crear el docente.</label>
											<div class="col-sm-10">
												<input type="text"style="margin-top: 20px;" name="numIndicadores" class="form-control col-sm-2" value="<?=$cfg[20];?>">
											</div>
										</div>
										
										<div class="form-group row"  style="background:rgb(255,255,204);">
											<label class="col-sm-2 control-label">Valor m&aacute;ximo que tendr&aacute; la suma de los indicadores o tipos de notas creados por el docente.</label>
											<div class="col-sm-10">
												<input type="text"style="margin-top: 20px;" name="valorIndicadores" class="form-control col-sm-2" value="<?=$cfg[21];?>">
                                                <span style="color:#F06; font-size:11px;">Este valor m&aacute;s la suma de los indicadores obligatorios debe ser igual a 100.</span>
                                                <a class="btn btn-danger" href="cargas-indicadores-obligatorios.php">Ir a los Indicadores Obligatorios</a>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Rango de las notas (Desde - Hasta)</label>
											<div class="col-sm-10">
												<input type="text"style="margin-top: 20px;" name="desde" class="col-sm-1" value="<?=$cfg[3];?>">
												<input type="text"style="margin-top: 20px;" name="hasta" class="col-sm-1" value="<?=$cfg[4];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Nota minima para aprobar</label>
											<div class="col-sm-2">
												<input type="text" name="notaMinima" class="form-control" value="<?=$cfg[5];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Color de las notas (Perdidas -  Ganadas)</label>
											<div class="col-sm-10">
												<input type="color"style="margin-top: 20px;" name="perdida" class="col-sm-1" value="<?=$cfg[6];?>">
												<input type="color"style="margin-top: 20px;" name="ganada" class="col-sm-1" value="<?=$cfg[7];?>">
											</div>
										</div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Si un usuario tiene saldo pendiente...</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="saldoPendiente" required>
                                                    <option value="1">Restringir acceso a la plataforma</option>
                                                    <option value="2">No hacer nada</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Fecha que aparecerá en el proximo Informe Parcial</label>
											<div class="col-sm-2">
												<input type="text" name="fechapa" class="form-control" value="<?=$cfg[28];?>">
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Texto de arriba del informe parcial</label>
											<div class="col-sm-10">
                                                <textarea cols="80" id="editor1" name="descrip" rows="10"><?=$cfg[29];?></textarea>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Medidas del Logo en los informes (Ancho -  Alto)</label>
											<div class="col-sm-10">
												<input type="text"style="margin-top: 20px;" name="logoAncho" class="col-sm-1" value="<?=$cfg[30];?>">
												<input type="text"style="margin-top: 20px;" name="logoAlto" class="col-sm-1" value="<?=$cfg[31];?>">
                                                <span style="color:#F06; font-size:11px;">Coloque solo el n&uacute;mero(Recomendado 200 x 150).</span>
											</div>
										</div>
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Mostrar Nombre del colegio en el encabezado de los informes (0=NO - 1=SI)</label>
											<div class="col-sm-2">
												<input type="text" name="mostrarNombre" class="form-control" value="<?=$cfg[32];?>">
											</div>
										</div>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
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
    <script src="../ckeditor/ckeditor.js"></script>

    <script>
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        CKEDITOR.replace( 'editor1' );
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>