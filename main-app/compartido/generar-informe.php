<?php
session_start();
include("../../config-general/config.php");
 
//Consultamos los estudiantes del grado y grupo
$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_GET["grado"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
$lineaError = __LINE__;
include("../compartido/reporte-errores.php");


 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	 
	//DEFINITIVAS
	$carga = $_GET["carga"];
	$periodo = $_GET["periodo"];
	$estudiante = $resultado[0];
	include("../definitivas.php");
	 
	//Consultamos si tiene registros en el boletín
	$consultaBoletinDatos=mysqli_query($conexion, "SELECT * FROM academico_boletin 
	WHERE bol_carga='".$_GET["carga"]."' AND bol_periodo='".$_GET["periodo"]."' AND bol_estudiante='".$resultado[0]."'");
	$boletinDatos = mysqli_fetch_array($consultaBoletinDatos, MYSQLI_BOTH); 
	 
	 //Verificamos que el estudiante tenga sus notas al 100%
	if($porcentajeActual<96 and $boletinDatos['bol_nota']==""){
?>
		<script language="javascript">window.location.href="../docente/page-info.php?idmsg=108&est=<?=$resultado['mat_nombres']." ".$resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido'];?>&idEst=<?=$resultado['mat_id'];?>&valorActual=<?=$porcentajeActual;?>";</script>
<?php		
		exit();
	}
	 
	
	$caso = 1; //Inserta la definitiva que viene normal 
	
	 
	//Si ya existe un registro previo de definitiva TIPO 1
	 if($boletinDatos['bol_id']!="" and $boletinDatos['bol_tipo']==1){
		 
		if($boletinDatos['bol_nota']!=$definitiva){
			$caso = 2;//Se cambia la definitiva que tenía por la que viene. Sea menor o mayor.
		}else{
			$caso = 3;//No se hacen cambios. Todo sigue igual
			continue;
		}
		 
	}
	//Si ya existe un registro previo de recuperación de periodo TIPO 2
	 elseif($boletinDatos['bol_id']!="" and $boletinDatos['bol_tipo']==2){
		
		//Si la definitiva que viene está perdida 
		if($definitiva<$config[5]){
			$caso = 3;//No se hacen cambios. Todo sigue igual
			continue;
		}else{
			$caso = 4;//Se reemplaza la nota de recuperación actual por la definitiva que viene. Igual está ganada y no requiere de recuperación.
		}
		 
	}
	//Si ya existe un registro previo de recuperación por Indicadores TIPO 3
	elseif($boletinDatos['bol_id']!="" and ($boletinDatos['bol_tipo']==3 or $boletinDatos['bol_tipo']==4)){
		 $caso = 5;//Se actualiza la definitiva que viene y se cambia la recuperación del Indicador a nota anterior. 
	}
	 
	
	//Vamos a obtener las definitivas por cada indicador y la definitiva general de la asignatura
	$notasPorIndicador = mysqli_query($conexion, "SELECT SUM((cal_nota*(act_valor/100))), act_id_tipo, ipc_valor FROM academico_calificaciones
	INNER JOIN academico_actividades ON act_id=cal_id_actividad AND act_estado=1 AND act_registrada=1 AND act_periodo='".$_GET["periodo"]."' AND act_id_carga='".$_GET["carga"]."'
	INNER JOIN academico_indicadores_carga ON ipc_indicador=act_id_tipo AND ipc_carga='".$_GET["carga"]."' AND ipc_periodo='".$_GET["periodo"]."'
	WHERE cal_id_estudiante='".$resultado[0]."'
	GROUP BY act_id_tipo");
	$sumaNotaIndicador = 0; 
	while($notInd = mysqli_fetch_array($notasPorIndicador, MYSQLI_BOTH)){
		$consultaNum=mysqli_query($conexion, "SELECT * FROM academico_indicadores_recuperacion 
		WHERE rind_carga='".$_GET["carga"]."' AND rind_estudiante='".$resultado[0]."' AND rind_periodo='".$_GET["periodo"]."' AND rind_indicador='".$notInd[1]."'");
		$num = mysqli_num_rows($consultaNum);
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		
		$sumaNotaIndicador  += $notInd[0];
		
		if($num==0){
			mysqli_query($conexion, "DELETE FROM academico_indicadores_recuperacion WHERE rind_carga='".$_GET["carga"]."' AND rind_estudiante='".$resultado[0]."' AND rind_periodo='".$_GET["periodo"]."' AND rind_indicador='".$notInd[1]."'");
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
			
			mysqli_query($conexion, "INSERT INTO academico_indicadores_recuperacion(rind_fecha_registro, rind_estudiante, rind_carga, rind_nota, rind_indicador, rind_periodo, rind_actualizaciones, rind_nota_original, rind_nota_actual, rind_valor_indicador_registro)VALUES(now(), '".$resultado[0]."', '".$_GET["carga"]."', '".$notInd[0]."', '".$notInd[1]."', '".$_GET["periodo"]."', 0, '".$notInd[0]."', '".$notInd[0]."', '".$notInd[2]."')");
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
		}else{
			mysqli_query($conexion, "UPDATE academico_indicadores_recuperacion SET rind_nota_anterior=rind_nota, rind_nota='".$notInd[0]."', rind_actualizaciones=rind_actualizaciones+1, rind_ultima_actualizacion=now(), rind_nota_actual='".$notInd[0]."', rind_tipo_ultima_actualizacion=1, rind_valor_indicador_actualizacion='".$notInd[2]."' WHERE rind_carga='".$_GET["carga"]."' AND rind_estudiante='".$resultado[0]."' AND rind_periodo='".$_GET["periodo"]."' AND rind_indicador='".$notInd[1]."'");
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
		}
	} 
	
	$sumaNotaIndicador = round($sumaNotaIndicador,1);
	 
	if($caso == 2 or $caso == 4 or $caso == 5){
		mysqli_query($conexion, "UPDATE academico_boletin SET bol_nota_anterior=bol_nota, bol_nota='".$definitiva."', bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now(), bol_nota_indicadores='".$sumaNotaIndicador."', bol_tipo=1, bol_observaciones='Reemplazada' WHERE bol_carga='".$_GET["carga"]."' AND bol_periodo='".$_GET["periodo"]."' AND bol_estudiante='".$resultado[0]."'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");	
	}elseif($caso == 1){
		//Eliminamos por si acaso hay algún registro
		mysqli_query($conexion, "DELETE FROM academico_boletin 
		WHERE bol_carga='".$_GET["carga"]."' AND bol_periodo='".$_GET["periodo"]."' AND bol_estudiante='".$resultado[0]."'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
			
		//INSERTAR LOS DATOS EN LA TABLA BOLETIN
		mysqli_query($conexion, "INSERT INTO academico_boletin(bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_fecha_registro, bol_actualizaciones, bol_nota_indicadores)VALUES('".$_GET["carga"]."', '".$resultado[0]."', '".$_GET["periodo"]."', '".$definitiva."', 1, now(), 0, '".$sumaNotaIndicador."')");	
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");		
	}
	 
		 	
}
mysqli_query($conexion, "UPDATE academico_cargas SET car_periodo=car_periodo+1 WHERE car_id='".$_GET["carga"]."'");
$lineaError = __LINE__;
include("../compartido/reporte-errores.php");
?>
	<script language="javascript">window.location.href="../docente/page-info.php?idmsg=109";</script>
<?php
	exit();
?>
