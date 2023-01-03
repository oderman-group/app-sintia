<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0025';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<?php
if(
	($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)) 
													
	or($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and $porcentajeRestante>0)
)
{
	
}else{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=212";</script>';
	exit();
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
                                <div class="page-title"><?=$frases[56][$datosUsuarioActual[8]];?> <?=$frases[7][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="clases.php" onClick="deseaRegresar(this)"><?=$frases[7][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[56][$datosUsuarioActual[8]];?> <?=$frases[7][$datosUsuarioActual[8]];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


						<?php include("info-carga-actual.php");?>

							
                            <div class="panel">
								<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                <div class="panel-body">
									<p><b>Banco de datos:</b> Tienes la opción de usar información que ya existe y así no tengas que escribir todo de nuevo. <mark>Sólo debes usar una de las 2 alternativas:</mark> o llenas la información desde cero o escoges la existente. Si usas las 2, <mark>el banco de datos tendrá prioridad</mark> y esta será lo que el sistema use.<br>
									<mark> - MIO :</mark> Significa que la información fue creada por ti.
									</p>
									<p><b>Compartir:</b> Compartir la información <mark>es una manera de colaborar con tus colegas.</mark> La información irá al banco de datos y podrá ser usada por ti o por otros colegas tuyos más adelante. En caso de que no desees compartirla puedes dar click sobre el botón para que se desactive y la información sólo puedas verla tú.</p>
								</div>
							</div>
                        </div>
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form id="form_subir" name="formularioGuardar" action="guardar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" method="post" enctype="multipart/form-data">
										<input type="hidden" value="11" name="id">
										<input type="hidden" value="<?=$config['conf_id_institucion']."".$cargaConsultaActual;?>" name="idMeeting">


										<div id="infoCero">
											<p style="color: blue;">Puedes llenar toda la información desde cero.</p>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Tema</label>
												<div class="col-sm-10">
													<input type="text" name="contenido" class="form-control" autocomplete="off" required>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Descripción</label>
												<div class="col-sm-10">
													<textarea name="descripcion" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
												</div>
											</div>
											
											<div class="form-group row">
													<label class="col-sm-2 control-label">Fecha</label>
													<div class="col-sm-4">
														<input type="date" name="fecha" class="form-control" autocomplete="off" value="<?=date("Y-m-d");?>" required>
													</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-2 control-label">Disponible para estudiantes</label>
												<div class="input-group spinner col-sm-4">
													<label class="switchToggle">
														<input type="checkbox" name="disponible" value="1" checked>
														<span class="slider yellow round"></span>
													</label>
												</div>
												<span class="col-sm-6 control-label" style="color: tomato;">Puede o no ser vista por los estudiantes.</span>
											 </div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Compartir con docentes</label>
												<div class="input-group spinner col-sm-10">
													<label class="switchToggle">
														<input type="checkbox" name="compartir" value="1">
														<span class="slider red round"></span>
													</label>
												</div>
											 </div>
											
											
											
											<p class="text-warning">Opcional.</p>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Video de youtube</label>
												<div class="col-sm-10">
													<input type="text" name="video" class="form-control" autocomplete="off">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Archivo 1</label>
												<div class="col-sm-5">
													<input type="file" name="file" class="form-control" autocomplete="off" onChange="archivoPeso(this)">
												</div>
												<div class="col-sm-5">
													<input type="text" name="archivo1" class="form-control" autocomplete="off" placeholder="Nombre del archivo 1">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Archivo 2</label>
												<div class="col-sm-5">
													<input type="file" name="file2" class="form-control" autocomplete="off" onChange="archivoPeso(this)">
												</div>
												<div class="col-sm-5">
													<input type="text" name="archivo2" class="form-control" autocomplete="off" placeholder="Nombre del archivo 2">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Archivo 3</label>
												<div class="col-sm-5">
													<input type="file" name="file3" class="form-control" autocomplete="off" onChange="archivoPeso(this)">
												</div>
												<div class="col-sm-5">
													<input type="text" name="archivo3" class="form-control" autocomplete="off" placeholder="Nombre del archivo 3">
												</div>
											</div>

											<div class="form-group row">
												<div class="col-sm-12">
													<div class="progress">
													  <div class="progress-bar progress-bar-striped bg-success" id="barra_estado" role="progressbar" style="width: 1%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">1%</div>
													</div>
												</div>	
											</div>

											
											<p class="text-info">Para clases en vivo.</p>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Clave para moderadores</label>
												<div class="col-sm-4">
													<input type="text" name="claveDocente" class="form-control" autocomplete="off">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-2 control-label">Clave para estudiantes</label>
												<div class="col-sm-4">
													<input type="text" name="claveEstudiante" class="form-control" autocomplete="off">
												</div>
											</div>
											
										</div>
										
										<!-- div necesario para el Jscript-->
										<div id="infoCeroDos"></div>
										
										
										<p style="color: blue;">Ó si quieres puedes usar el <b>banco de datos</b>. Tal vez te sirva algo de lo que ya existe.</p>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><b>Banco de datos</b></label>
                                            <div class="col-sm-10">
												<?php
												$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_clases 
												WHERE cls_estado=1 AND ((cls_compartir=1 AND cls_id_carga!='".$cargaConsultaActual."') OR (cls_id_carga='".$cargaConsultaActual."' AND cls_periodo!='".$periodoConsultaActual."'))");
												?>
                                                <select class="form-control  select2" name="bancoDatos" onChange="avisoBancoDatos(this)">
                                                    <option value="">Seleccione una opción</option>
													<option value="0" selected>--Ninguno--</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														$recursoPropio = '';
														if($opcionesDatos['act_id_carga']==$cargaConsultaActual)$recursoPropio = ' - MIO';
													?>
                                                    	<option value="<?=$opcionesDatos['act_id'];?>"><?=$opcionesDatos['act_descripcion']." (".$opcionesDatos['act_valor']."%)".$recursoPropio;?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div>


										<input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
										
										<a href="#" name="clases.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
									</form>
									

									<script>
											document.addEventListener("DOMContentLoaded", () =>{
												let form = document.getElementById("form_subir");

												form.addEventListener("submit", function(event) {
													event.preventDefault();

													subir_archivos(this);
												});
											});

											function subir_archivos(form){
												let barra_estado = form.children[0];

												let peticion = new XMLHttpRequest();

												peticion.upload.addEventListener("progress", (event) => {
													let porcentaje = Math.round((event.loaded / event.total) * 100);

													document.getElementById("barra_estado").innerHTML = porcentaje+"%";
													document.getElementById("barra_estado").style.width = porcentaje+"%";

												});

												peticion.addEventListener("load", () => {
													document.getElementById("barra_estado").innerHTML = "Subido totalmente(100%)";
													
													setTimeout(redirect(), 2000);
													
													function redirect(){
														location.href='clases.php';
													}

												});

												peticion.open("POST", "guardar.php");
												peticion.send(new FormData(form));

											}

										</script>


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