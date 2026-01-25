<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0035';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

Utilidades::validarParametros($_GET,["carga"]);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$idCarga = base64_decode($_GET["carga"]);
$datosCarga = CargaAcademica::traerCargaMateriaPorID($config, $idCarga);

// Obtener todos los indicadores obligatorios disponibles
$consultaIndicadores = Indicadores::consultarIndicadoresObligatorios();

// Obtener períodos máximos del grado
$periodosMaximos = $config['conf_periodos_maximos'] ?? 4;
?>
	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- select2-->
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
                                <div class="page-title">Aplicar Indicadores Obligatorios</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas.php" onClick="deseaRegresar(this)">Cargas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Aplicar Indicadores</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
                        <div class="col-sm-12">
							<div class="panel">
								<header class="panel-heading panel-heading-purple">Aplicar Indicadores Obligatorios a Carga Académica</header>
                                <div class="panel-body">
                                    <div class="alert alert-info">
                                        <strong>Información:</strong> Seleccione los indicadores obligatorios que desea aplicar a esta carga académica. 
                                        El sistema validará automáticamente si ya existen para evitar duplicados.
                                    </div>
                                   
									<form name="formularioGuardar" action="cargas-aplicar-indicadores-guardar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" name="carga" value="<?=$idCarga;?>">
										
										<div class="form-group row">
											<label class="col-sm-2 control-label">Carga Académica</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" value="<?=$datosCarga['gra_nombre'];?> - <?=$datosCarga['gru_nombre'];?> - <?=$datosCarga['mat_nombre'];?>" readonly>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Indicadores Disponibles</label>
											<div class="col-sm-10">
												<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
													<?php 
													$hayIndicadores = false;
													while($indicador = mysqli_fetch_array($consultaIndicadores, MYSQLI_BOTH)): 
														$hayIndicadores = true;
													?>
														<div class="checkbox">
															<label>
																<input type="checkbox" name="indicadores[]" value="<?=$indicador['ind_id'];?>">
																<strong><?=$indicador['ind_nombre'];?></strong> - Valor: <?=$indicador['ind_valor'];?>%
															</label>
														</div>
													<?php endwhile; ?>
													<?php if (!$hayIndicadores): ?>
														<p class="text-muted">No hay indicadores obligatorios disponibles.</p>
													<?php endif; ?>
												</div>
												<small class="text-muted">Seleccione los indicadores que desea aplicar a esta carga.</small>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-2 control-label">Períodos</label>
											<div class="col-sm-10">
												<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
													<?php for ($p = 1; $p <= $periodosMaximos; $p++): ?>
														<div class="checkbox">
															<label>
																<input type="checkbox" name="periodos[]" value="<?=$p;?>" checked>
																Período <?=$p;?>
															</label>
														</div>
													<?php endfor; ?>
												</div>
												<small class="text-muted">Seleccione los períodos en los que se aplicarán los indicadores seleccionados.</small>
											</div>
										</div>
										
                                    <?php $botones = new botonesGuardar("cargas-editar.php?idR=".base64_encode($idCarga),true); ?>
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
	<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>
</html>
