<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0077';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php 
//Hay acciones que solo son permitidos en periodos diferentes al actual.
include("verificar-periodos-iguales.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaCalificaciones=mysqli_query($conexion, "SELECT * FROM academico_indicadores
INNER JOIN academico_indicadores_carga ON ipc_indicador=ind_id
WHERE ind_id='".$_GET["idR"]."'");
$calificacion = mysqli_fetch_array($consultaCalificaciones, MYSQLI_BOTH);
?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<script type="application/javascript">
//CALIFICACIONES	
function notas(enviada){
  var carga = <?=$cargaConsultaActual;?>;	
  var periodo = <?=$periodoConsultaActual;?>;
  var codNota = <?=$_GET["idR"];?>;
  var valorDecimalIndicador = <?=($calificacion['ipc_valor']/100);?>;
  
  var nota = enviada.value;
  var notaAnterior = enviada.name;	
  var codEst = enviada.id;
  var nombreEst = enviada.alt;
  var operacion = enviada.title;
	
  var casilla = document.getElementById(codEst);
 
var notaAnteriorTransformada = (notaAnterior/valorDecimalIndicador);
notaAnteriorTransformada = Math.round(notaAnteriorTransformada * 10) / 10;

if(isNaN(nota)){
	alert('Esto no es un valor numérico: '+nota+'. Si estás usando comas, reemplacelas por un punto.'); 
	casilla.value="";
	casilla.focus();
	return false;	
}	
	
if (nota><?=$config[4];?> || nota < <?=$config[3];?>) {
	alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); 
	casilla.value="";
	casilla.focus();
	return false;
}

/*
if(nota<notaAnteriorTransformada){
   alert(`No es permitido colocar una nota de recuperación menor: ${nota} a la nota anterior: ${notaAnteriorTransformada}.`);
	casilla.value="";
	casilla.focus();
	return false;
}*/
	
if(nota==notaAnteriorTransformada){
   alert(`No es permitido colocar una nota de recuperación igual: ${nota} a la nota anterior: ${notaAnteriorTransformada}.`);
	casilla.value="";
	casilla.focus();
	return false;
}	
	
	
casilla.disabled="disabled";
casilla.style.fontWeight="bold";
		  
$('#respRC').empty().hide().html("Guardando información, espere por favor...").show(1);
	datos = "nota="+(nota)+
			"&codNota="+(codNota)+
			"&notaAnterior="+(notaAnterior)+
			"&carga="+(carga)+
			"&periodo="+(periodo)+
			"&operacion="+(operacion)+
			"&nombreEst="+(nombreEst)+
			"&valorDecimalIndicador="+(valorDecimalIndicador)+
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
                                <div class="page-title"><?=$calificacion['ind_nombre']." (".$calificacion['ipc_valor']."%)";?></div>
								
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="indicadores.php"><?=$frases[63][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$calificacion['ind_nombre'];?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<?php include("info-carga-actual.php");?>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">TABLA DE VALORES</header>

										<div class="panel-body">
											  <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
												<!-- BEGIN -->
												<thead>
												  <tr>
													<th>Desde</th>
													<th>Hasta</th>
													<th>Resultado</th>
												  </tr>
												</thead>
												<tbody>
												 <?php
												 $TablaNotas = mysqli_query($conexion, "SELECT * FROM academico_notas_tipos WHERE notip_categoria='".$config["conf_notas_categoria"]."'");
												 while($tabla = mysqli_fetch_array($TablaNotas, MYSQLI_BOTH)){
												 ?>
												  <tr id="data1" class="odd grade">

													<td><?=$tabla["notip_desde"];?></td>
													<td><?=$tabla["notip_hasta"];?></td>
													<td><?=$tabla["notip_nombre"];?></td>
												  </tr>
												  <?php }
													mysqli_free_result($TablaNotas);
													?>
												</tbody>
											  </table>
										</div>
										
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[63][$datosUsuarioActual['uss_idioma']]);?> </header>
										<div class="panel-body">
											<p>Puedes cambiar a otro indicador rápidamente para calificar a tus estudiantes o hacer modificaciones de notas.</p>
											<?php
											$registrosEnComun = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga
											INNER JOIN academico_indicadores ON ind_id=ipc_indicador
											WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_indicador!='".$_GET["idR"]."'
											ORDER BY ipc_id DESC
											");
											while($regComun = mysqli_fetch_array($registrosEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?idR=<?=$regComun['ipc_indicador'];?>"><?=$regComun['ind_nombre']." (".$regComun['ipc_valor']."%)";?></a></p>
											<?php }
											mysqli_free_result($registrosEnComun);
											?>
										</div>
                                    </div>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></header>
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
														<th>Cod</th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[108][$datosUsuarioActual[8]];?><br>Indicador</th>
														<th>Recup.<br>Indicador</th>
														<th>DEF.<br>PERIODO <?=$periodoConsultaActual;?></th>
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
														 
														//Consulta de recuperaciones si ya la tienen puestas.
														$consultaNotas=mysqli_query($conexion, "SELECT * FROM academico_indicadores_recuperacion WHERE rind_estudiante=".$resultado[0]." AND rind_indicador='".$_GET["idR"]."' AND rind_periodo='".$periodoConsultaActual."' AND rind_carga='".$cargaConsultaActual."'");
														$notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
														

														//Promedio nota indicador según nota de actividades relacionadas
														$consultaNotaIndicador=mysqli_query($conexion, "SELECT ROUND(SUM(cal_nota*(act_valor/100)) / SUM(act_valor/100),2) FROM academico_calificaciones
														INNER JOIN academico_actividades ON act_id=cal_id_actividad AND act_estado=1 AND act_id_tipo='".$_GET["idR"]."' AND act_periodo='".$periodoConsultaActual."' AND act_id_carga='".$cargaConsultaActual."'
														WHERE cal_id_estudiante='".$resultado['mat_id']."'");
														$notaIndicador = mysqli_fetch_array($consultaNotaIndicador, MYSQLI_BOTH);
														 
														$notaRecuperacion = "";
														if($notas['rind_nota']>$notas['rind_nota_original'] and $notas['rind_nota']>$notaIndicador[0]){
															$notaRecuperacion = $notas['rind_nota'];
															
															//Color nota
															if($notaRecuperacion<$config[5] and $notaRecuperacion!="") $colorNota = $config[6]; elseif($notaRecuperacion>=$config[5]) $colorNota = $config[7];
														}
														 $consultaNotasResultado=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$resultado['mat_id']." AND bol_carga=".$cargaConsultaActual." AND bol_periodo=".$periodoConsultaActual);
														$notasResultado = mysqli_fetch_array($consultaNotasResultado, MYSQLI_BOTH);
														 
														if($notasResultado[4]<$config[5] and $notasResultado[4]!="")$color = $config[6]; elseif($notasResultado[4]>=$config[5]) $color = $config[7]; 
														 
														 
														 $colorEstudiante = '#000;';
														 if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';}
													 ?>
                                                    
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['mat_id'];?></td>
														<td style="color: <?=$colorEstudiante;?>">
															<img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
															<?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?>
														</td>
														<td>
															<a href="calificaciones-estudiante.php?usrEstud=<?=$resultado['mat_id_usuario'];?>&periodo=<?=$periodoConsultaActual;?>&carga=<?=$cargaConsultaActual;?>&indicador=<?=$_GET["idR"];?>" style="text-decoration:underline;">
																<?=$notaIndicador[0];?>
															</a>	
														</td>
														<td>
															<?php 
														 	if($notaIndicador[0]==""){
																echo "<span title='No hay notas relacionadas este indicador. Revise las actividades.'>-</span>";	
															}
															elseif($notas['rind_id']==""){
																echo "<span title='No hay un registro de definitiva para este Indicador. Por favor Genere Informe.'>?</span>";	
															}
															elseif($notaIndicador[0]>=$config[5]){
																echo "";	
															}
															else{	
															?>
															<input type="text" style="text-align: center; color:<?=$colorNota;?>" size="5" maxlength="3" value="<?=$notaRecuperacion;?>" name="<?=$notas['rind_nota_actual'];?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" title="9" onChange="notas(this)" tabindex="<?=$contReg;?>">
															<?php }?>
															
															
															<?php if($notas['cal_nota']!=""){?>
															<a href="#" name="guardar.php?get=21&id=<?=$notas['cal_id'];?>" onClick="deseaEliminar(this)">X</a>
															<?php }?>
														</td>
														
														<td>
																<a href="calificaciones-estudiante.php?usrEstud=<?=$resultado['mat_id_usuario'];?>&periodo=<?=$periodoConsultaActual;?>&carga=<?=$cargaConsultaActual;?>" style="text-decoration:underline; color:<?=$color;?>;"><?=$notasResultado[4]."</a>";?>
															</td>
														
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													mysqli_free_result($consulta);
															
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
    <!-- end js include path -->
</body>

</html>