<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0018';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">

<script type="text/javascript">
function guardarAjax(datos){ 
  var idR = datos.id;
  var valor = 0;
	if(document.getElementById(idR).checked){
		valor = 1;
	}
  var operacion = 4;	

$('#respuestaGuardar').empty().hide().html("").show(1);
	datos = "idR="+(idR)+
			"&valor="+(valor)+
			"&operacion="+(operacion);
		   $.ajax({
			   type: "POST",
			   url: "ajax-guardar.php",
			   data: datos,
			   success: function(data){
			   	$('#respuestaGuardar').empty().hide().html(data).show(1);
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
                                <div class="page-title"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">	
									
									<?php include("info-carga-actual.php");?>
									
									<?php include("filtros-cargas.php");?>
									
									<?php include("../compartido/publicidad-lateral.php");?>
								
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													
											<?php
											if(
												($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)) 
																	
												or($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and $porcentajeRestante>0)
												)
											{
											?>
											
													<div class="btn-group">
														<a href="actividades-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
													
													
											<?php
											}
											?>
													
											
												</div>
											</div>
											<span id="respuestaGuardar"></span>	
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual[8]];?></th>
														<th>No Retrasos?</th>
														<th><?=$frases[127][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[51][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[128][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas 
													 WHERE tar_id_carga='".$cargaConsultaActual."' AND tar_periodo='".$periodoConsultaActual."' AND tar_estado=1 ");
													$contReg=1; 
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$consultafd=mysqli_query($conexion, "SELECT DATEDIFF('".$resultado[5]."','".date("Y-m-d")."')");
														$fd = mysqli_fetch_array($consultafd, MYSQLI_BOTH);
														$consultasd=mysqli_query($conexion, "SELECT DATEDIFF('".$resultado[4]."','".date("Y-m-d")."')");
														$sd = mysqli_fetch_array($consultasd, MYSQLI_BOTH);
														
														$cheked = '';
														if($resultado['tar_impedir_retrasos']==1){$cheked = 'checked';}
													 ?>
													<tr id="reg<?=$resultado[0];?>">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado[0];?></td>
														<td>
															<div class="input-group spinner col-sm-10">
																<label class="switchToggle">
																	<input type="checkbox" id="<?=$resultado['tar_id'];?>" name="retrasos" value="1" onChange="guardarAjax(this)" <?=$cheked;?>>
																	<span class="slider red round"></span>
																</label>
															</div>
														</td>
														<td><a href="actividades-entregas.php?idR=<?=$resultado['tar_id'];?>" style="text-decoration: underline;"><?=$resultado[1];?></a></td>
														<td><?=$frases[125][$datosUsuarioActual[8]];?>: <?=$resultado[4];?><br><?=$frases[126][$datosUsuarioActual[8]];?>: <?=$resultado[5];?></td>
														<td><?php if($resultado[6]!="" and file_exists('../files/tareas/'.$resultado[6])){?><a href="../files/tareas/<?=$resultado[6];?>" style="text-decoration: underline;" target="_blank">Descargar</a><?php }?></td>
														<td>
															
															<?php
																$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
																$arrayDatos = json_encode($arrayEnviar);
														 		$objetoEnviar = htmlentities($arrayDatos);
																?>
															
															<?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>

															<div class="btn-group">
																<button class="btn btn-xs btn-info dropdown-toggle center no-margin" type="button" data-toggle="dropdown" aria-expanded="false"> Acciones
																	<i class="fa fa-angle-down"></i>
																</button>
																<ul class="dropdown-menu pull-left" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 23px, 0px); top: 0px; left: 0px; will-change: transform;">
																	
																		<li><a href="actividades-entregas.php?idR=<?=$resultado['tar_id'];?>">Entregas</a></li>
																	  	<li><a href="actividades-editar.php?idR=<?=$resultado['tar_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>">Editar</a></li>
																	 	 
																	<li><a href="#" title="<?=$objetoEnviar;?>" id="<?=$resultado[0];?>" name="guardar.php?get=17&idR=<?=$resultado['tar_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" onClick="deseaEliminar(this)">Eliminar</a></li>
 
																</ul>
															</div>
															<?php } ?>
														</td>
                                                    </tr>
													<?php 
														 $contReg++;
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