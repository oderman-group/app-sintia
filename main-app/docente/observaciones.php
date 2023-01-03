<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0080';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php //include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaCalificaciones=mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id='".$_GET["idR"]."' AND act_estado=1");
$calificacion = mysqli_fetch_array($consultaCalificaciones, MYSQLI_BOTH);
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

<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />

<script type="application/javascript">
//CALIFICACIONES	
function notas(enviada){
  var carga = <?=$cargaConsultaActual;?>;
  var periodo = <?=$periodoConsultaActual;?>; 
  var nota = enviada.value;
  var codEst = enviada.id;
  var nombreEst = enviada.alt;
  var operacion = enviada.title;
	  
$('#respRC').empty().hide().html("Guardando información, espere por favor...").show(1);
	datos = "nota="+(nota)+
			"&operacion="+(operacion)+
			"&nombreEst="+(nombreEst)+
			"&carga="+(carga)+
			"&periodo="+(periodo)+
			"&codEst="+(codEst);
		   $.ajax({
			   type: "POST",
			   url: "ajax-calificaciones-registrar.php",
			   data: datos,
			   success: function(data){
			   	$('#respRC').empty().hide().html(data).show(1);
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
                                <div class="page-title">Observaciones</div>
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
                                            <header>Observaciones</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
										
										
									
										
                                        <div class="card-body">
											
											
											
										<span style="color: blue; font-size: 15px;" id="respRC"></span>
											
											
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th>ID</th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th>DEF</th>
														<th><?=$frases[109][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas
													 INNER JOIN usuarios ON uss_id=mat_id_usuario
													 WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
													 $contReg = 1;
													 $colorNota = "black";
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $consultaNotas=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$resultado[0]." AND bol_periodo='".$periodoConsultaActual."' AND bol_carga='".$cargaConsultaActual."'");
														$notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
														 
														//DEFINITIVAS
														/*$carga = $cargaConsultaActual;
														$periodo = $periodoConsultaActual;
														$estudiante = $resultado[0];
                                                        include("../definitivas.php");*/
                                                        $definitiva = $notas['bol_nota'];
														if($definitiva<$config[5] and $definitiva!="") $colorNota = $config[6]; elseif($definitiva>=$config[5]) $colorNota = $config[7]; else {$colorNota = 'black'; $definitiva='';} 
														
													 ?>
                                                    
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['mat_id'];?></td>
														<td width="60%">
															<img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
															<?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?>
														</td>
														
														<td><a href="calificaciones-estudiante.php?usrEstud=<?=$resultado['mat_id_usuario'];?>&periodo=<?=$periodoConsultaActual;?>&carga=<?=$cargaConsultaActual;?>" style="text-decoration:underline; color:<?=$colorNota;?>;"><?=$definitiva;?></a><br><span style="font-size: 10px;"><?=$notas['bol_observaciones'];?></span></td>
														
														<td width="25%">
															
															<textarea rows="7" cols="80" name="O<?=$contReg;?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" title="8" onChange="notas(this)"><?=$notas['bol_observaciones_boletin'];?></textarea>
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
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

</html>