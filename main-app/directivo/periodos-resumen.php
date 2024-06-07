<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0111';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
require_once("../class/Estudiantes.php");

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>
<script type="text/javascript">
  function def(enviada){
  var nota = enviada.value;
  var codEst = enviada.id;
  var per = enviada.name;
  var notaAnterior = enviada.alt;
  var carga = <?=$cargaConsultaActual;?>;
  
  var casilla = document.getElementById(codEst);
  
 	if (alertValidarNota(nota)) {
		return false;
	}	
	
	casilla.disabled="disabled";
	casilla.style.fontWeight="bold";
	
	  $('#respRP').empty().hide().html("esperando...").show(1);
		datos = "nota="+(nota)+
				   "&per="+(per)+
				   "&codEst="+(codEst)+
				   "&notaAnterior="+(notaAnterior)+
				   "&carga="+(carga);
			   $.ajax({
				   type: "POST",
				   url: "ajax-periodos-registrar.php",
				   data: datos,
				   success: function(data){
				   $('#respRP').empty().hide().html(data).show(1);
				   }
			   });

	}
	
function niv(enviada){
  var nota = enviada.value;
  var codEst = enviada.id;
  var per = enviada.name;
  var carga = <?=$cargaConsultaActual;?>;
  if (alertValidarNota(nota)) {
		return false;
	}
	  $('#respRP').empty().hide().html("esperando...").show(1);
		datos = "nota="+(nota)+
				   "&per="+(per)+
				   "&codEst="+(codEst)+
				   "&carga="+(carga);
			   $.ajax({
				   type: "POST",
				   url: "ajax-nivelaciones-registrar.php",
				   data: datos,
				   success: function(data){
				   $('#respRP').empty().hide().html(data).show(1);
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
                                <div class="page-title"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></div>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas.php" onClick="deseaRegresar(this)"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<p><b><?=$frases[117][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[120][$datosUsuarioActual['uss_idioma']];?></p>
											
											<p><b><?=$frases[118][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[121][$datosUsuarioActual['uss_idioma']];?></p>
										</div>
									</div>
									
									<?php include("info-carga-actual.php");?>
									
									<?php include("filtros-cargas.php");?>
									
									<?php include("../compartido/publicidad-lateral.php");?>

									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-responsive">
											
											<span id="respRP"></span>
                                            
											<table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">#</th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>

														<?php
															$p = 1;
															while($p<=$datosCargaActual['gra_periodos']){
																$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $p);
																
																$porcentajeGrado=25;
																if(!empty($periodosCursos['gvp_valor'])){
																	$porcentajeGrado=$periodosCursos['gvp_valor'];
																}
																echo '<th style="text-align:center;">'.$p.'P<br>('.$porcentajeGrado.'%)</th>';
																$p++;
															}
														?> 
														<th style="text-align:center;"><?=$frases[117][$datosUsuarioActual['uss_idioma']];?></th>
														<th style="text-align:center;"><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$contReg = 1;
													$filtro = " AND mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."'";
													$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>
                                                    
													<tr>
                                                        <td style="text-align:center;"><?=$contReg;?></td>
														<td><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>

														<?php
														 $definitiva = 0;
														 $sumatoria = 0;
														 $decimal = 0;
														 $n = 0;
														 for($i=1; $i<=$datosCargaActual['gra_periodos']; $i++){
															$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $i);
															
															$porcentajeGrado=25;
															if(!empty($periodosCursos['gvp_valor'])){
																$porcentajeGrado=$periodosCursos['gvp_valor'];
															}
															 $decimal = $porcentajeGrado/100;
															 
															//LAS CALIFICACIONES
															$notasResultado = Boletin::traerNotaBoletinCargaPeriodo($config, $i, $resultado['mat_id'], $cargaConsultaActual);
															if(!empty($notasResultado)){
																$n++;
																$definitiva += $notasResultado['bol_nota']*$decimal;
															}
															if(!empty($notasResultado['bol_nota']) && $notasResultado['bol_nota']<$config[5])$color = $config[6]; elseif(!empty($notasResultado['bol_nota']) && $notasResultado['bol_nota']>=$config[5]) $color = $config[7];
															 
															if(isset($notasResultado) && $notasResultado['bol_tipo']==2) {$tipo = '<span style="color:red; font-size:9px;">Rec. Periodo('.$notasResultado['bol_nota_anterior'].')</span>';}
															elseif(isset($notasResultado) && $notasResultado['bol_tipo']==3) {$tipo = '<span style="color:red; font-size:9px;">Rec. Indicador('.$notasResultado['bol_nota_anterior'].')</span>';}
															 elseif(isset($notasResultado) && $notasResultado['bol_tipo']==4) {$tipo = '<span style="color:red; font-size:9px;">Directiva('.$notasResultado['bol_nota_anterior'].')</span>';}
															elseif(isset($notasResultado) && $notasResultado['bol_tipo']==1) {$tipo = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>';} 
															 else $tipo='';
															$notaPeriodo="";
															if(!empty($notasResultado['bol_nota']))$notaPeriodo=$notasResultado['bol_nota'];


														?>
															<td style="text-align:center;">
																<a href="calificaciones-estudiante.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($i);?>&carga=<?=base64_encode($cargaConsultaActual);?>" style="text-decoration:underline; color:<?=$color;?>;"><?=$notaPeriodo?></a><br><?=$tipo;?><br>
																<?php if(Modulos::validarPermisoEdicion()){?>
																	<input size="5" name="<?=$i?>" id="<?=$resultado['mat_id'];?>" value="" alt="<?php if(!empty($notasResultado['bol_nota'])) echo $notasResultado['bol_nota'];?>" onChange="def(this)" tabindex="2" style="text-align: center;" <?=$disabledPermiso;?>><br>
																	<span style="font-size:9px; color:rgb(0,0,153);"><?php if(!empty($notasResultado['bol_observaciones'])) echo $notasResultado['bol_observaciones'];?></span>
																<?php }?>
															</td>
														<?php		
														 }
															$consultaN = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $resultado['mat_id'], $cargaConsultaActual);
															$numN = mysqli_num_rows($consultaN);
															$rN = mysqli_fetch_array($consultaN, MYSQLI_BOTH);
															if($numN==0){
																if($n>0)
																	$definitiva = round(($definitiva), $config['conf_decimales_notas']);
																	$tN = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>';
															}else{
																$definitiva = $rN['niv_definitiva'];
																$tN = '<span style="color:red; font-size:9px;">'.$frases[124][$datosUsuarioActual['uss_idioma']].'</span>';
															}
														 if($definitiva<$config[5])$color = $config[6]; elseif($definitiva>=$config[5]) $color = $config[7];
														 
														 //CALCULAR NOTA MINIMA EN EL ULTIMO PERIODO PARA APROBAR LA MATERIA
														 //PREGUNTAMOS SI ESTAMOS EN EL PERIODO PENULTIMO O ULTIMO
														 if($config[2]==$datosCargaActual['gra_periodos']){
															 $notaMinima = ($config[5]-$definitiva);
															 $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $datosCargaActual['gra_periodos']);
															 
															$porcentajeGrado=25;
															if(!empty($periodosCursos['gvp_valor'])){
																$porcentajeGrado=$periodosCursos['gvp_valor'];
															}
															 $decimal2 = $porcentajeGrado/100;
															 $notaMinima = round(($notaMinima / $decimal2), $config['conf_decimales_notas']);
															 if($notaMinima<=0){
																$notaMinima = "-";
																$colorFaltante = "green";
															 }else{
																if($notaMinima<=$config[4]) $colorFaltante = "blue"; else $colorFaltante = "red"; 
															 }
														 }else{
															$notaMinima = "-";
															$colorFaltante = "black";
														}
														?>

														<td style="text-align:center; color:<?=$colorFaltante;?>; font-weight:bold;"><?=$notaMinima;?></td>

														<td style="text-align:center; color:<?=$color;?>;">
															<?=$definitiva."<br>".$tN;?><br>
															<?php
															if($n==$datosCargaActual['gra_periodos']) $e = ''; else $e = 'disabled';
															?>
															<?php if(Modulos::validarPermisoEdicion()){?>
																<input size="5" name="<?=$i?>" id="<?=$resultado['mat_id'];?>" value="" onChange="niv(this)" tabindex="2" <?=$e;?> style="font-size: 13px; text-align: center;" <?=$disabledPermiso;?>>
															<?php }?>
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