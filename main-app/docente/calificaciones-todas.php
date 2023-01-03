<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0011';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaValores=mysqli_query($conexion, "SELECT
(SELECT sum(act_valor) FROM academico_actividades 
WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1),
(SELECT count(*) FROM academico_actividades 
WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1)
");
$valores = mysqli_fetch_array($consultaValores, MYSQLI_BOTH);
$porcentajeRestante = 100 - $valores[0];
?>
<script type="application/javascript">
//CALIFICACIONES	
function notas(enviada){
  var codNota = enviada.name;	 
  var nota = enviada.value;
  var codEst = enviada.id;
  var nombreEst = enviada.alt;
  var operacion = enviada.title;
  var notaAnterior = enviada.step;
 
if(operacion == 1 || operacion == 3){
	if (nota><?=$config[4];?> || isNaN(nota) || nota < <?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;}
}
	  
$('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
	datos = "nota="+(nota)+
			"&codNota="+(codNota)+
			"&operacion="+(operacion)+
			"&nombreEst="+(nombreEst)+
			"&notaAnterior="+(notaAnterior)+
			"&codEst="+(codEst);
		   $.ajax({
			   type: "POST",
			   url: "ajax-calificaciones-registrar.php",
			   data: datos,
			   success: function(data){
			   	$('#respRCT').empty().hide().html(data).show(1);
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
                                <div class="page-title"><?=$frases[243][$datosUsuarioActual['uss_idioma']];?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-12 col-lg-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[243][$datosUsuarioActual['uss_idioma']];?></header>
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
												($datosCargaActual['car_configuracion']==0 and $valores[1]<$datosCargaActual['car_maximas_calificaciones'] 
												 and $periodoConsultaActual<=$datosCargaActual['gra_periodos'] and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)) 
																	
												or($datosCargaActual['car_configuracion']==1 and $valores[1]<$datosCargaActual['car_maximas_calificaciones'] and $periodoConsultaActual<=$datosCargaActual['gra_periodos'] and $porcentajeRestante>0)
												)
											{
											?>
											
													<div class="btn-group">
														<a href="calificaciones-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
													
													
											<?php
											}
											?>
													
											<?php if($datosCargaActual['car_configuracion']==1 and $porcentajeRestante<=0){?>
												<p style="color: tomato;"> Has alcanzado el 100% de valor para las calificaciones. </p>
											<?php }?>
														
											<?php if($datosCargaActual['car_maximas_calificaciones']<=$valores[1]){?>
												<p style="color: tomato;"> Has alcanzado el número máximo de calificaciones permitidas. </p>
											<?php }?>
													
													<div class="btn-group">
														<a href="calificaciones-todas-rapido.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" class="btn bg-purple">
															LLenar más rápido las calificaciones
														</a>
													</div>
											
												</div>
											</div>
											
                                        <div class="table-responsive">
											
											<span id="respRCT"></span>
											
											<?php 
											//Verificar si el periodo es anterior para que no modifique notas.
											$habilitado = 'disabled';
											$deleteOculto = 'style="display:none;"';
											if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){
												$habilitado = '';
												$deleteOculto = 'style="display:block;"';
											}
											?>
											
											<?php
											$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
											$arrayDatos = json_encode($arrayEnviar);
											$objetoEnviar = htmlentities($arrayDatos);
											?>
                                            
											<table class="table table-striped custom-table table-hover">
                                                <thead>
												  <tr>
													<th style="width: 50px;">#</th>
													<th style="width: 400px;"><?=$frases[61][$datosUsuarioActual[8]];?></th>
													<?php
													 $cA = mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$cargaConsultaActual."' AND act_estado=1 AND act_periodo='".$periodoConsultaActual."'");
													 while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
														echo '<th style="text-align:center; font-size:11px; width:100px;"><a href="calificaciones-editar.php?idR='.$rA[0].'" title="'.$rA[1].'">'.$rA[0].'<br>
														'.$rA[1].'<br>
														('.$rA[3].'%)</a><br>
														<a href="#" name="guardar.php?get=12&idR='.$rA[0].'&idIndicador='.$rA['act_id_tipo'].'&carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'" onClick="deseaEliminar(this)" '.$deleteOculto.'><i class="fa fa-times"></i></a><br>
														<input type="text" style="text-align: center; font-weight: bold;" maxlength="3" size="10" title="3" name="'.$rA[0].'" onChange="notas(this)" '.$habilitado.'>
														</th>';
													 }
													?>
													<th style="text-align:center; width:60px;">%</th>
													<th style="text-align:center; width:60px;"><?=$frases[118][$datosUsuarioActual[8]];?></th>
												  </tr>
												</thead>
                                                <tbody>
													<?php
													$contReg = 1; 
													$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas
													INNER JOIN usuarios ON uss_id=mat_id_usuario
													WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido, mat_segundo_apellido, mat_nombres");
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														//DEFINITIVAS
														$carga = $cargaConsultaActual;
														$periodo = $periodoConsultaActual;
														$estudiante = $resultado[0];
														include("../definitivas.php");
														
														$colorEstudiante = '#000;';
														if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';}
													?>
													
													<?php
													$arrayEnviar = array("tipo"=>2, "descripcionTipo"=>"Para ocultar la X y limpiar valor.", "idInput"=>$resultado[0]);
													$arrayDatos = json_encode($arrayEnviar);
													$objetoEnviar = htmlentities($arrayDatos);
													?>
                                                    
													<tr>
                                                        <td style="text-align:center;" style="width: 100px;"><?=$contReg;?></td>
														<td style="color: <?=$colorEstudiante;?>">
															<img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
															<?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?>
														</td>

														<?php
														 $cA = mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$cargaConsultaActual."' AND act_estado=1 AND act_periodo='".$periodoConsultaActual."'");
														 while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
															//LAS CALIFICACIONES
															$consultaNotasResultados=mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$resultado[0]." AND cal_id_actividad=".$rA[0]);
															$notasResultado = mysqli_fetch_array($consultaNotasResultados, MYSQLI_BOTH);
														?>
															<td>
																
															<input size="5" maxlength="3" name="<?=$rA[0]?>" id="<?=$resultado[0];?>" value="<?=$notasResultado[3];?>" title="1" alt="<?=$resultado['mat_nombres'];?>" step="<?=$notasResultado[3];?>" onChange="notas(this)" tabindex="2" style="font-size: 13px; text-align: center; color:<?php if($notasResultado[3]<$config[5] and $notasResultado[3]!="")echo $config[6]; elseif($notasResultado[3]>=$config[5]) echo $config[7]; else echo "black";?>;" <?=$habilitado;?>>
																
															<?php if($notasResultado[3]!=""){?>
																<a href="#" title="<?=$objetoEnviar;?>" id="<?=$notasResultado['cal_id'];?>" name="guardar.php?get=21&id=<?=$notasResultado['cal_id'];?>" onClick="deseaEliminar(this)" <?=$deleteOculto;?>><i class="fa fa-times"></i></a>
																<?php if($notasResultado[3]<$config[5]){?>
																	<br><br><input size="5" maxlength="3" name="<?=$rA[0]?>" id="<?=$resultado[0];?>" title="4" alt="<?=$resultado['mat_nombres'];?>" step="<?=$notasResultado[3];?>" onChange="notas(this)" tabindex="2" style="font-size: 13px; text-align: center; border-color:tomato;" placeholder="Recup" <?=$habilitado;?>>
																<?php }?>
																
															<?php }?>

															</td>
														<?php		
														 }
														if($definitiva<$config[5] and $definitiva!="") $colorDef = $config[6]; elseif($definitiva>=$config[5]) $colorDef = $config[7]; else $colorDef = "black";
														?>

														<td style="text-align:center;"><?=$porcentajeActual;?></td>
                                        				<td style="color:<?php if($definitiva<$config[5] and $definitiva!="")echo $config[6]; elseif($definitiva>=$config[5]) echo $config[7]; else echo "black";?>; text-align:center; font-weight:bold;"><a href="calificaciones-estudiante.php?usrEstud=<?=$resultado['mat_id_usuario'];?>&periodo=<?=$periodoConsultaActual;?>&carga=<?=$cargaConsultaActual;?>" style="text-decoration:underline; color:<?=$colorDef;?>;"><?=$definitiva;?></a></td>
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
							
							<div class="row">
								
								<div class="col-md-6 col-lg-4">
									
									<?php include("filtros-cargas.php");?>
									
								</div>
                                
								<div class="col-md-6 col-lg-4">
									
									<?php include("info-carga-actual.php");?>
									
									<?php include("../compartido/publicidad-lateral.php");?>

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