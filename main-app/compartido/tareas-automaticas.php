<?php
include("../directivo/session.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
//PARA DOCENTES
//Generar informes
$cargasConsulta = mysqli_query($conexion, "SELECT DATEDIFF(car_fecha_generar_informe_auto, now()), car_id, car_periodo, car_curso, car_grupo, car_docente FROM ".BD_ACADEMICA.".academico_cargas WHERE car_fecha_generar_informe_auto IS NOT NULL AND car_fecha_generar_informe_auto!='0000-00-00' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");


while($cargasDatos = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)){
	if($cargasDatos[0]==0){
		
		mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_carga='".$cargasDatos['car_id']."' AND bol_periodo='".$cargasDatos['car_periodo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
		
		$filtroAdicional= "AND mat_grado='".$cargasDatos["car_curso"]."' AND mat_grupo='".$cargasDatos["car_grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
		$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"");
		
		
		 $pararProceso = 2;
		 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
			//DEFINITIVAS
			$carga = $cargasDatos['car_id'];
			$periodo = $cargasDatos['car_periodo'];
			$estudiante = $resultado['mat_id'];
			include("../definitivas.php");	
			if($porcentajeActual<96){
				mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_carga='".$cargasDatos['car_id']."' AND bol_periodo='".$cargasDatos['car_periodo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
				
				
				if($porcentajeActual>0){
					$porcentajeFaltante = (100 - $porcentajeActual);
					mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_url_acceso, alr_vista, alr_institucion, alr_year)
					VALUES('No se pudo generar el informe', 'No se pudo generar el informe para la carga ".$cargasDatos['car_id']." con el estudiante ".$resultado['mat_nombres'].". Le falta un ".$porcentajeFaltante."% para completar el 100% de sus notas.', 2, '".$cargasDatos['car_docente']."', now(), 3, 2, 'calificaciones.php?carga=".$cargasDatos['car_id']."&periodo=".$cargasDatos['car_periodo']."', 0,'" . $config['conf_id_institucion'] . "','" . $_SESSION["bd"] . "')");
					
				}

				$pararProceso = 1;
				continue;
			}
			if($pararProceso==2){
				$codigoBOL=Utilidades::generateCode("BOL");
				//INSERTAR LOS DATOS EN LA TABLA BOLETIN	
				mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_boletin(bol_id, bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, institucion, year)
				VALUES('".$codigoBOL."', '".$cargasDatos['car_id']."','".$resultado['mat_id']."','".$cargasDatos['car_periodo']."','".$definitiva."',1, {$config['conf_id_institucion']}, {$_SESSION["bd"]})");	
				
			}
		}
		if($pararProceso==2){
			$update = "car_periodo=car_periodo+1";
			CargaAcademica::actualizarCargaPorID($config, $cargasDatos['car_id'], $update);
		}
	}
}
?>