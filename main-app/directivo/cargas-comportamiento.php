<?php
include("session.php");
$idPaginaInterna = 'DT0051';
include("../compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
include("../compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
$carga='';
$grado='';
$grupo='';
$periodo='';
if(!empty($_GET['carga'])){
	$carga=base64_decode($_GET['carga']);
	$grado=base64_decode($_GET['grado']);
	$grupo=base64_decode($_GET['grupo']);
	$periodo=base64_decode($_GET['periodo']);
}
if(!empty($_POST['carga'])){
	$carga=$_POST['carga'];
	$grado=$_POST['grado'];
	$grupo=$_POST['grupo'];
	$periodo=$_POST['periodo'];
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
      
	<script type="text/javascript">
	function notas(nota,codigoe,observacion){
	var codEst =codigoe;
	var periodo=<?=$periodo?>;
	var carga=<?=$carga?>;
	if(nota!=''){
		if (alertValidarNota(nota)) {
		return false;
		}
	}
	$('#resp').empty().hide().html("esperando...").show(1);
		
		if(nota!=''){
		datos = "nota="+(nota)+
				"&periodo="+(periodo)+
				"&carga="+(carga)+
				"&codEst="+(codEst);
		}
		if(observacion!=''){
			datos = "observacion="+(observacion)+
				"&periodo="+(periodo)+
				"&carga="+(carga)+
				"&codEst="+(codEst);
			}
			$.ajax({
				type: "POST",
				url: "ajax-nota-disiplina-registrar.php",
				data: datos,
				success: function(data){
				$('#resp').empty().hide().html(data).show(1);
				}
			});
	}
	</script>
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
                                <div class="page-title">Nota de Comportamiento</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas-comportamiento-filtros.php" onClick="deseaRegresar(this)">Comportamiento</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Nota de Comportamiento</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

								<div class="col-md-12">
									<div class="card card-topline-purple">
										<div class="card-body">
											<div class="alert alert-block alert-warning">
												<h4 class="alert-heading">Información importante!</h4>
												<p>Usted est&aacute; registrando las nota de comportamiento del periodo <span style="font-size:20px; font-weight:bold;"><?=$periodo;?></span>.</p>
											</div>

											<div class="table-scrollable">
												<table class="display" style="width:100%;">
													<thead>
														<tr>
															<th>Nota desde</th>
															<th>Nota hasta</th>
															<th>Resultado</th>
														</tr>
													</thead>
													<tbody>
														<?php
														$TablaNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"]);
														while($tabla = mysqli_fetch_array($TablaNotas, MYSQLI_BOTH)){
														?>
														<tr>
														  <td><?=$tabla["notip_desde"];?></td>
														  <td><?=$tabla["notip_hasta"];?></td>
														  <td><?=$tabla["notip_nombre"];?></td>
														</tr>
														<?php }?>
													</tbody>
												</table>
											</div>
											<div class="alert alert-block alert-warning">
												<h4 class="alert-heading">Nota importante!</h4>
												<p>Coloque la nota num&eacute;rica que corresponda al desempeño que aparecer&aacute; en el bolet&iacute;n.</p>
											</div>
										</div>
									</div>	
								</div>							
								<div class="col-md-12">
									<?php include("../../config-general/mensajes-informativos.php"); ?>
									<span id="resp"></span>	
									<div class="card card-topline-purple">
										<div class="card-head">
											<header>
												<div class="row" style="margin-bottom: 10px;">
												</div>												
											</header>
											<div class="tools">
												<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
												<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
												<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
											</div>
										</div>
										<div class="card-body">					
											<div class="table-scrollable">
												<table id="example1" class="display" style="width:100%;">
													<thead>
														<tr>
															<th style="text-align:center;">Codigo</th>
															<th style="text-align:center;" width="30%">Nombre</th>
															<th style="text-align:center;" width="30%">Nota</th>
															<th style="text-align:center;" width="30%">Observaciones</th>
														</tr>
													</thead>
													<tbody>
														<?php
														$con = 1;
														$filtroAdicional= "AND mat_grado='".$grado."' AND mat_grupo='".$grupo."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
														$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"");
														
														while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
															$nombre = Estudiantes::NombreCompletoDelEstudiante($resultado);	
															try{
																$consultaRnDisiplina=mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$resultado['mat_id']."' AND dn_id_carga='".$carga."' AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
															} catch (Exception $e) {
																include("../compartido/error-catch-to-report.php");
															}
															$rndisiplina=mysqli_fetch_array($consultaRnDisiplina, MYSQLI_BOTH);
															//LAS CALIFICACIONES A MODIFICAR Y LAS OBSERVACIONES
														?>
														<tr id="data1">
															<td style="text-align:center;"><?=$resultado['mat_id'];?></td>
															<td><?=$nombre?></td>
															<td style="text-align: center;">
																<input 
																	size="5"
																	value="<?php if(!empty($rndisiplina["dn_nota"])) echo $rndisiplina["dn_nota"];?>" 
																	onChange="notas(value,'<?=$resultado['mat_id']?>','')" 
																	style="font-size: 13px; text-align: center;" <?=$disabledPermiso;?>
																>

																<?php if(!empty($rndisiplina['dn_nota']) && Modulos::validarPermisoEdicion()){?>
																	<a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este mensaje?','question','cargas-comportamiento-eliminar.php?id=<?=base64_encode($rndisiplina['dn_id']);?>&periodo=<?=base64_encode($periodo);?>&carga=<?=base64_encode($carga);?>&grado=<?=base64_encode($grado);?>&grupo=<?=base64_encode($grupo);?>')">X</a>
																	<?php }?>

															</td>
															<td style="text-align:center;">
																<textarea name="" id="" onChange="notas('','<?=$resultado['mat_id']?>',value)" rows="2" cols="50" <?=$disabledPermiso;?>><?php if(!empty($rndisiplina["dn_observacion"])) echo $rndisiplina["dn_observacion"];?></textarea>
															</td>
														</tr>
														<?php 
														}
														?>
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
</body>

</html>