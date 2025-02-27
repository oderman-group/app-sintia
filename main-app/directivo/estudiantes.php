<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0001';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php"); 
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");


Utilidades::validarParametros($_GET);

if (isset($_GET['mode']) && $_GET['mode'] === 'DEV') {
	$redis = RedisInstance::getRedisInstance();

	$arrayTest = [
		[
			'Nombre' => 'Jhon',
			'Edad'   => 33,
			'Genero' => 'M'
		],
		[
			'Nombre' => 'Michelle',
			'Edad'   => 24,
			'Genero' => 'F'
		],
	];

	$redis->set('jhonky', json_encode($arrayTest));
	//echo $redis->ttl('jhonky'); exit();
	print_r(json_decode($redis->get('jhonky'), true));
	echo "<hr>";
	
	$redis->lPush("estudiantes", "Jhon");
	$redis->lPush("estudiantes", "Cristal");
	$redis->lPush("estudiantes", "Michelle");

	$estudiantes = $redis->lRange("estudiantes", 0, 2);
	
	foreach($estudiantes as $valor) {
		echo $valor."<br>";
	}

	exit();
}

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

//$redis = RedisInstance::getRedisInstance();

$jQueryTable = '';
if($config['conf_doble_buscador'] == 1) {
	$jQueryTable = 'id="example1"';
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php
	include("../compartido/body.php");
	include("usuarios-bloquear-modal.php");
?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php"); //1 por otimizar, parece estar repetida ?>
		
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
                                <div class="page-title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php"); //1 por otimizar, parece estar repetida ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
								<?php include("../../config-general/mensajes-informativos.php"); ?>
								<span id="respuestaCambiarEstado"></span>

								<?php 
								//include("includes/barra-superior-matriculas.php");	
								// $matKeys = array_slice($keys, $inicio, $registros);
								// foreach ($matKeys as $matKey){
								// 	$matData = $redis->get($matKey);
								// 	$resultado = json_decode($matData, true);
								// }
								// print_r($resultado); exit();
								?>
								
								<?php
								 $filtro="";
								include("includes/barra-superior-matriculas-componente.php");	?>

									<?php
									if($config['conf_id_institucion'] == ICOLVEN){
										if(isset($_GET['msgsion'])){
											$aler='alert-danger';
											$mensajeSion='Por favor, verifique todos los datos del estudiante y llene los campos vacios.';
											if($_GET['msgsion']!=''){
												$aler='alert-success';
												$mensajeSion=base64_decode($_GET['msgsion']);
												if(base64_decode($_GET['stadsion'])!=true){
													$aler='alert-danger';
												}
											}
									?>
										<div class="alert alert-block <?=$aler;?>">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<h4 class="alert-heading">SION!</h4>
											<p><?=$mensajeSion;?></p>
										</div>
									<?php 
										}
									}
									if(isset($_GET['msgsintia'])){
										$aler='alert-success';
										if($_GET['stadsintia']!=true){
										$aler='alert-danger';
										}
									?>
									<div class="alert alert-block <?=$aler;?>">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<h4 class="alert-heading">SINTIA!</h4>
										<p><?=$_GET['msgsintia'];?></p>
									</div>
									<?php }?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
														<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0084'])){?>
															<a href="estudiantes-agregar.php" id="addRow" class="btn deepPink-bgcolor">
																Agregar nuevo <i class="fa fa-plus"></i>
															</a>
														<?php }?>
													</div>
												</div>
											</div>
											
                                        <div>
											
                                    		<table <?=$jQueryTable;?> class="display" style="width:100%;">
												<div id="gifCarga" class="gif-carga">
													<img  alt="Cargando...">
												</div>
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
														<th>Bloq.</th>
														<th><?=$frases[246][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[241][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[26][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Usuario</th>
														<th>Acudiente</th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="matriculas_result">
													<?php
													
													include("includes/consulta-paginacion-estudiantes.php");
													$filtroLimite = 'LIMIT '.$inicio.','.$registros;
													
													$selectSql = ["mat.*",
																  "uss.uss_id","uss.uss_usuario","uss.uss_bloqueado",
																  "gra_nombre","gru_nombre","gra_formato_boletin",
																  "acud.uss_nombre","acud.uss_nombre2","acud.uss_nombre2", "mat.id_nuevo AS mat_id_nuevo"];

													$consulta = Estudiantes::listarEstudiantes(0, $filtro, $filtroLimite, $cursoActual,null,$selectSql);
													
													$contReg = 1;

													$index = 0;
													$arraysDatos = array();
													if (!empty($consulta)) {
														while ($fila = $consulta->fetch_assoc()) {
															$arraysDatos[$index] = $fila;
															$index++;
														}
														$consulta->free();
													}
													$lista = $arraysDatos;
													$data["data"] =$lista;
													include(ROOT_PATH . "/main-app/class/componentes/result/matriculas-tbody.php");
													  ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                      				<?php include("enlaces-paginacion.php");?>
                                </div>
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
	<script>
		$(function () {
			$('[data-toggle="popover"]').popover();
		});

		$('.popover-dismiss').popover({trigger: 'focus'});
	</script>
</body>

</html>