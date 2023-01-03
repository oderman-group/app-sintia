<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0067';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>

<!-- Axios -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script>
<?php include("../compartido/sintia-funciones-js.php");?>
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
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Resumen de notas</title>
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


<style type="text/css">
body {
  margin: 0;
  padding: 2rem;
	font-family: Arial;
}

table {
  text-align: left;
  position: relative;
  border-collapse: collapse; 
}
th, td {
  padding: 0.25rem;
}

th {
  background-color:lightgrey;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

</style>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	
</head>
<!-- END HEAD -->

<body>
	

                                            <span id="respRCT"></span>
		
											<p>
												<a href="calificaciones-todas.php" type="button" class="btn btn-primary">Regresar</a>
												<!--
												<a href="calificaciones-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" type="button" class="btn btn-danger">Agregar nuevo</a>
												-->
											</p>
	
											<?php 
											//Verificar si el periodo es anterior para que no modifique notas.
											$habilitado = 'disabled';
											$deleteOculto = 'style="display:none;"';
											if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){
												$habilitado = '';
												$deleteOculto = 'style="display:block;"';
											}
											?>
	
											<table width="100%" border="1" rules="rows">
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
														
														$colorFondo = '';
														if($resultado['mat_id']==$_GET["idEst"]){$colorFondo = 'yellow;';}
													?>
													
													<?php
													$arrayEnviar = array("tipo"=>2, "descripcionTipo"=>"Para ocultar la X y limpiar valor.", "idInput"=>$resultado[0]);
													$arrayDatos = json_encode($arrayEnviar);
													$objetoEnviar = htmlentities($arrayDatos);
													?>
                                                    
													<tr style="background-color: <?=$colorFondo;?>">
                                                        <td style="text-align:center;" style="width: 100px;"><?=$contReg;?></td>
														<td style="color: <?=$colorEstudiante;?>">
															<?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?>
														</td>

														<?php
														 $cA = mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$cargaConsultaActual."' AND act_estado=1 AND act_periodo='".$periodoConsultaActual."'");
														 while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
															//LAS CALIFICACIONES
															$consultaNotasResultados=mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$resultado[0]." AND cal_id_actividad=".$rA[0]);
															$notasResultado = mysqli_fetch_array($consultaNotasResultados, MYSQLI_BOTH);
														?>
															<td style="text-align:center;">
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
											

    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
	
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <!-- end js include path -->
</body>

</html>