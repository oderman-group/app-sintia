<?php
include("../modelo/conexion.php");
//PARA DOCENTES
//Generar informes
$cargasConsulta = mysqli_query($conexion, "SELECT DATEDIFF(car_fecha_generar_informe_auto, now()), car_id, car_periodo, car_curso, car_grupo, car_docente FROM academico_cargas
WHERE car_fecha_generar_informe_auto IS NOT NULL AND car_fecha_generar_informe_auto!='0000-00-00'");


while($cargasDatos = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)){
	if($cargasDatos[0]==0){
		
		mysqli_query($conexion, "DELETE FROM academico_boletin WHERE bol_carga=".$cargasDatos['car_id']." AND bol_periodo='".$cargasDatos['car_periodo']."'");
		
		
		$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
		WHERE mat_grado='".$cargasDatos['car_curso']."' AND mat_grupo='".$cargasDatos['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
		
		
		 $pararProceso = 2;
		 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
			//DEFINITIVAS
			$carga = $cargasDatos['car_id'];
			$periodo = $cargasDatos['car_periodo'];
			$estudiante = $resultado[0];
			include("../definitivas.php");	
			if($porcentajeActual<96){
				mysqli_query($conexion, "DELETE FROM academico_boletin WHERE bol_carga=".$cargasDatos['car_id']." AND bol_periodo='".$cargasDatos['car_periodo']."'");
				
				
				if($porcentajeActual>0){
					$porcentajeFaltante = (100 - $porcentajeActual);
					mysqli_query($conexion, "INSERT INTO general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_url_acceso, alr_vista)
					VALUES('No se pudo generar el informe', 'No se pudo generar el informe para la carga ".$cargasDatos['car_id']." con el estudiante ".$resultado['mat_nombres'].". Le falta un ".$porcentajeFaltante."% para completar el 100% de sus notas.', 2, '".$cargasDatos['car_docente']."', now(), 3, 2, 'calificaciones.php?carga=".$cargasDatos['car_id']."&periodo=".$cargasDatos['car_periodo']."', 0)");
					
				}

				$pararProceso = 1;
				continue;
			}
			if($pararProceso==2){
				//INSERTAR LOS DATOS EN LA TABLA BOLETIN	
				mysqli_query($conexion, "INSERT INTO academico_boletin(bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo)
				VALUES('".$cargasDatos['car_id']."','".$resultado[0]."','".$cargasDatos['car_periodo']."','".$definitiva."',1)");	
				
			}
		}
		if($pararProceso==2){
			mysqli_query($conexion, "UPDATE academico_cargas SET car_periodo=car_periodo+1 WHERE car_id='".$cargasDatos['car_id']."'");
			
		}
	}
}
?>