<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0051';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
      
	<script type="text/javascript">
	function notas(nota,codigoe,observacion){
	//carga $_SESSION["carga"]
	//codigo $resultado[0];
	//periodo $datosCargaActual[5]
	var codEst =codigoe;
	var periodo=<?=$datosCargaActual[5]?>;
	var carga=<?=$_SESSION["carga"]?>;
	if(nota!=''){
	if (nota><?=$config[4];?> || isNaN(nota) || nota><?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;}	
	}
	$('#resp').empty().hide().html("esperando...").show(1);
		
		if(nota!=''){
		datos = "nota="+(nota)+
				"&periodo="+periodo+
				"&carga="+carga+
				"&codEst="+(codEst);
		}
		if(observacion!=''){
			datos = "observacion="+(observacion)+
				"&periodo="+periodo+
				"&carga="+carga+
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
                                <li><a class="parent-item" href="#" name="cargas.php" onClick="deseaRegresar(this)">Cargas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
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
												<p>Usted est&aacute; registrando las nota de comportamiento del periodo <span style="font-size:20px; font-weight:bold;"><?=$_GET["periodo"];?></span>.</p>
											</div>

											<div class="table-scrollable">
												
												<?php
												$TablaNotas = mysqli_query($conexion, "SELECT * FROM academico_notas_tipos WHERE notip_categoria='".$config["conf_notas_categoria"]."'");
												?>

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

								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-8 col-lg-9">
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
														$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_GET["grado"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
														//carga $_SESSION["carga"]
														//codigo $resultado[0];
														//periodo $datosCargaActual[5]
														
														while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
															$consultaRnDisiplina=mysqli_query($conexion, "SELECT * FROM disiplina_nota WHERE dn_cod_estudiante='".$resultado[0]."' AND dn_id_carga='".$_GET["carga"]."' AND dn_periodo='".$_GET["periodo"]."'");
															$rndisiplina=mysqli_fetch_array($consultaRnDisiplina, MYSQLI_BOTH);
															//LAS CALIFICACIONES A MODIFICAR Y LAS OBSERVACIONES
														?>
														<tr id="data1">
															<td style="text-align:right;"><?=$resultado[0];?></td>
															<td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
															<td>
																<input maxlength="2" name="" id="" value="<?=$rndisiplina["dn_nota"]?>" onChange="notas(value,'<?=$resultado[0]?>','')" style="font-size: 13px; text-align: center;">
																<?php if($rndisiplina[4]!=""){?>
																	<a href="guardar.php?get=22&id=<?=$rndisiplina[0];?>" onClick="if(!confirm('Desea ejecutar esta accion?')){return false;}">Eliminar</a>
																<?php }?>
															</td>
															<td><textarea name="" id="" onChange="notas('','<?=$resultado[0]?>',value)" rows="2"><?=$rndisiplina["dn_observacion"]?></textarea></td>
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