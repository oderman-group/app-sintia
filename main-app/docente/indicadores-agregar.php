<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0019';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=0),
(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1),
(SELECT count(*) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1)");
$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

if(
	(
		( $datosCargaActual['car_valor_indicador'] == CONFIG_AUTOMATICO_INDICADOR && $sumaIndicadores[2] < $datosCargaActual['car_maximos_indicadores'] ) 
																
		or ($datosCargaActual['car_valor_indicador'] == CONFIG_MANUAL_INDICADOR && $sumaIndicadores[2] < $datosCargaActual['car_maximos_indicadores'] && $periodoConsultaActual<=$datosCargaActual['gra_periodos'] && $porcentajeRestante > 0 )
	)
	&& CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual)
){
	
}else{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=210";</script>';
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
                                <div class="page-title"><?=$frases[56][$datosUsuarioActual[8]];?> <?=$frases[63][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="indicadores.php"><?=$frases[63][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[56][$datosUsuarioActual[8]];?> <?=$frases[63][$datosUsuarioActual[8]];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


						<?php include("info-carga-actual.php");?>

							
                            <!-- <div class="panel">
								<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                <div class="panel-body">
									<p><b>Banco de datos:</b> Tienes la opción de usar información que ya existe y así no tengas que escribir todo de nuevo. <mark>Sólo debes usar una de las 2 alternativas:</mark> o llenas la información desde cero o escoges la existente. Si usas las 2, <mark>el banco de datos tendrá prioridad</mark> y esta será lo que el sistema use.<br>
									<mark> - MIO :</mark> Significa que la información fue creada por ti.
									</p>
									<p><b>Compartir:</b> Compartir la información <mark>es una manera de colaborar con tus colegas.</mark> La información irá al banco de datos y podrá ser usada por ti o por otros colegas tuyos más adelante. En caso de que no desees compartirla puedes dar click sobre el botón para que se desactive y la información sólo puedas verla tú.</p>
								</div>
							</div> -->
                        </div>
						
                        <div class="col-sm-9">


								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual[8]];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="indicadores-guardar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" method="post">
										
										<!-- Esto es porque hay un campo que existe o no dependiendo la configuración de la carga y afecta la función javascript-->
										<input type="hidden" value="<?=$datosCargaActual['car_valor_indicador'];?>" name="configInd">

										<div id="infoCero">
											<p style="color: blue;">Puedes llenar toda la información desde cero.</p>
											<div class="form-group row">
												<label class="col-sm-2 control-label">Descripción</label>
												<div class="col-sm-10">
													<input type="text" name="contenido" class="form-control" autocomplete="off" required>
												</div>
											</div>

											<?php if($datosCargaActual['car_valor_indicador']==1){?>
												<p><b>Valor máximo restante:</b> <?=$porcentajeRestante;?>%. <mark>Si superas este valor, el sistema lo ajustará automáticamente.</mark></p>
												<div class="form-group row">
													<label class="col-sm-2 control-label">Valor (%)</label>
													<div class="col-sm-2">
														<input type="text" name="valor" class="form-control" autocomplete="off" required>
													</div>
												</div>
											<?php }?>
											
										<?php if($datosCargaActual['car_saberes_indicador']==1){?>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de evaluación</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="saberes" required>
                                                    <option value="0">Seleccione una opción</option>
													<option value="1">Saber saber (55%)</option>
													<option value="2">Saber hacer (35%)</option>
													<option value="3">Saber ser (10%)</option>
                                                </select>
                                            </div>
                                        </div>
										<?php }else{?>
										<input type="hidden" name="saberes" class="form-control" value="0">
										<?php }?>

											<!-- <div class="form-group row">
												<label class="col-sm-2 control-label">Compartir</label>
												<div class="input-group spinner col-sm-10">
													<label class="switchToggle">
														<input type="checkbox" name="compartir" value="1" checked>
														<span class="slider red round"></span>
													</label>
												</div>
											 </div> -->
										</div>
										
										<!-- div necesario para el Jscript-->
										<div id="infoCeroDos"></div>
										
										
										<!-- <p style="color: blue;">Ó si quieres puedes usar el <b>banco de datos</b>. Tal vez te sirva algo de lo que ya existe.</p>
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><b>Banco de datos</b></label>
                                            <div class="col-sm-10">
												<?php
												$opcionesConsulta = mysqli_query($conexion, "SELECT * FROM academico_indicadores
												INNER JOIN academico_indicadores_carga ON ipc_indicador=ind_id
												WHERE ind_obligatorio=0
												");
												?>
                                                <select class="form-control  select2" name="bancoDatos" onChange="avisoBancoDatos(this)">
                                                    <option value="">Seleccione una opción</option>
													<option value="0" selected>--Ninguno--</option>
													<?php
													while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
														if(($opcionesDatos['ind_publico']==0 and $opcionesDatos['ipc_carga']!=$cargaConsultaActual) or ($opcionesDatos['ipc_carga']==$cargaConsultaActual and $opcionesDatos['ipc_periodo']==$periodoConsultaActual)) continue;
														$recursoPropio = '';
														if($opcionesDatos['ipc_carga']==$cargaConsultaActual)$recursoPropio = ' - MIO';
													?>
                                                    	<option value="<?=$opcionesDatos['ind_id'];?>"><?=$opcionesDatos['ind_nombre']." (".$opcionesDatos['ipc_valor']."%)".$recursoPropio;?></option>
													<?php }?>
                                                </select>
                                            </div>
                                        </div> -->


										<button type="submit" class="btn  btn-info">
										<i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
									</button>
										
										<a href="#" name="indicadores.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
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