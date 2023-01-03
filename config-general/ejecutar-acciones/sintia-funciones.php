<?php
$permisoDeEjecucion = 2609;

if($permisoDeEjecucion != 2609){
	echo "You don't have permission of execute this action. You had better get away!";
	die();
	exit();
}

inlude("../../conexion-datos.php");
$conexion = mysql_connect($servidorConexion,$usuarioConexion,$claveConexion);

//FOLIO 
#Penultimo uso: 09 de Agosto de 2021
#Ultimo uso: 09 de Agosto de 2022
$bdSelect = 'mobiliar_icolven_2021';

mysql_select_db($bdSelect, $conexion);

$consulta = mysql_query("SELECT * FROM ".$bdSelect.".academico_matriculas
	INNER JOIN academico_grados ON gra_id=mat_grado
	WHERE mat_estado_matricula=1 AND mat_eliminado=0 
	ORDER BY gra_vocal, mat_grupo, mat_primer_apellido, mat_segundo_apellido, mat_nombres
	",$conexion);

$folio = 1;
while($datos = mysql_fetch_array($consulta)){
	mysql_query("UPDATE ".$bdSelect.".academico_matriculas SET mat_folio='".$folio."' 
	WHERE 
	mat_id='".$datos['mat_id']."'
	",$conexion);
	$folio ++ ;
}

//var_dump($consulta);

/**
* PARA JOSE ANTONIO GALAN
* PERSONALIZAR INDICADORES
$indicadores = array(
	"En el transcurso del periodo presentó a tiempo sus actividades, cumpliendo con los requerimientos establecidos por la institución.",

	"En el transcurso del periodo, ha accedido permanentemente a la información, y pudo enviar evidencias correspondientes a todas las actividades que se le han asignado, se recomienda seguir adelante con su proceso de enseñanza aprendizaje.",

	"En el transcurso del periodo, mostró interés para participar del proceso, accediendo a la información y enviando las evidencias de las actividades de su proceso de enseñanza aprendizaje, se recomienda mantener este nivel de su interés por su aprendizaje.",

	"En el transcurso del periodo, cumplió a cabalidad con el envío de las evidencias de actividades desarrolladas; lo (a) exhortamos a seguir cumpliendo así, para desarrollar de forma óptima su proceso de enseñanza aprendizaje.",

	"En el transcurso del periodo, respetó el desarrollo del proceso de enseñanza aprendizaje; se recomienda continuar así, al construir y enviar sus actividades para alcanzar su aprendizaje.",

	"En el transcurso del periodo, participó en la construcción de todas las evidencias de actividades desarrolladas; se recomienda continuar así, al construir y enviar sus actividades para mejorar el desarrollo de su proceso de enseñanza aprendizaje."
);

$valoresIndicadores = array(10, 12, 12, 12, 12, 12);


$i=0;

while($i<=5){
	mysql_query("INSERT INTO ".$bdSelect.".academico_indicadores(ind_nombre, ind_obligatorio, ind_fecha_creacion, ind_valor)VALUES('".$indicadores[$i]."', 1, now(), '".$valoresIndicadores[$i]."')",$conexion);
	

	$i++;
}
*/

/*
$actividades = array('[Nombre de la actividad]', 'Acceso', 'Interés', 'Cumplimiento', 'Respeto', 'Participación');

$cargasAcademicas = mysql_query("SELECT * FROM ".$bdSelect.".academico_cargas LIMIT 300, 300",$conexion);


while($carga = mysql_fetch_array($cargasAcademicas)){

	$indicadores = mysql_query("SELECT * FROM ".$bdSelect.".academico_indicadores WHERE ind_obligatorio=1",$conexion);
	

	$i=0;
	
	while($ind = mysql_fetch_array($indicadores)){

		//Relacionamos los indicadores
		mysql_query("INSERT INTO ".$bdSelect.".academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado)VALUES('".$carga['car_id']."', '".$ind['ind_id']."', '".$ind['ind_valor']."', 4, 0)",$conexion);
		
		$idIndicador = mysql_insert_id();

		//Insertamos las actividades
		mysql_query("INSERT INTO ".$bdSelect.".academico_actividades(act_descripcion, act_fecha, act_valor, act_id_tipo, act_id_carga, act_registrada, act_fecha_creacion, act_estado, act_periodo)VALUES('".$actividades[$i]."', now(), '".$ind['ind_valor']."', '".$ind['ind_id']."', '".$carga['car_id']."', 0, now(), 1, 4)",$conexion);
		

		$i++;
	}
}
*/



//mysql_select_db("mobiliar_icolven_2019", $conexion);
/*
$consulta = mysql_query("SELECT * FROM mobiliar_icolven_2019.academico_matriculas WHERE mat_compromiso=1",$conexion);
while($datos = mysql_fetch_array($consulta)){
	mysql_query("UPDATE mobiliar_icolven_2020.academico_matriculas SET mat_compromiso=1 WHERE mat_id='".$datos['mat_id']."'",$conexion);
}
*/

/*
$consulta = mysql_query("SELECT * FROM mobiliar_icolven_2019.academico_boletin WHERE bol_periodo=4 AND bol_tipo=1 AND bol_carga=218",$conexion);
while($datos = mysql_fetch_array($consulta)){
	echo $datos['bol_observaciones_boletin']."<br>";
	mysql_query("UPDATE mobiliar_icolven_2019.academico_boletin SET bol_observaciones_boletin='".$datos['bol_observaciones_boletin']."' 
	WHERE 
	bol_carga='".$datos['bol_carga']."' 
	AND bol_estudiante='".$datos['bol_estudiante']."' 
	AND bol_periodo=3
	AND bol_nota IS NOT NULL
	",$conexion);
}
*/

/*
$consulta = mysql_query("SELECT * FROM academico_cargas WHERE car_docente=3959",$conexion);
while($datos = mysql_fetch_array($consulta)){
	$estudiantesConsulta = mysql_query("SELECT * FROM academico_matriculas WHERE mat_grado='".$datos['car_curso']."' AND mat_grupo='".$datos['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido",$conexion);
 	
	
	while($estudiante = mysql_fetch_array($estudiantesConsulta)){
		$periodo = 3;
		//while($periodo<=3){
			$notasPorIndicador = mysql_query("SELECT SUM((cal_nota*(act_valor/100))), act_id_tipo FROM academico_calificaciones
			INNER JOIN academico_actividades ON act_id=cal_id_actividad AND act_estado=1 AND act_registrada=1 AND act_periodo='".$periodo."' AND act_id_carga='".$datos['car_id']."'
			INNER JOIN academico_indicadores_carga ON ipc_indicador=act_id_tipo AND ipc_carga='".$datos['car_id']."'
			WHERE cal_id_estudiante='".$estudiante[0]."'
			GROUP BY act_id_tipo",$conexion);
			$sumaNotaIndicador = 0;

			while($notInd = mysql_fetch_array($notasPorIndicador)){
				$num = mysql_num_rows(mysql_query("SELECT * FROM academico_indicadores_recuperacion 
				WHERE rind_carga='".$datos['car_id']."' AND rind_estudiante='".$estudiante[0]."' AND rind_periodo='".$periodo."' AND rind_indicador='".$notInd[1]."'",$conexion));
				

				$sumaNotaIndicador  += $notInd[0];

				if($num==0){
					mysql_query("DELETE FROM academico_indicadores_recuperacion WHERE rind_carga='".$datos['car_id']."' AND rind_estudiante='".$estudiante[0]."' AND rind_periodo='".$periodo."' AND rind_indicador='".$notInd[1]."'",$conexion);
					
					mysql_query("INSERT INTO academico_indicadores_recuperacion(rind_fecha_registro, rind_estudiante, rind_carga, rind_nota, rind_indicador, rind_periodo, rind_actualizaciones, rind_nota_original)VALUES(now(), '".$estudiante[0]."', '".$datos['car_id']."', '".$notInd[0]."', '".$notInd[1]."', '".$periodo."', 0, '".$notInd[0]."')",$conexion);
					
				}else{
					mysql_query("UPDATE academico_indicadores_recuperacion SET rind_nota_original='".$notInd[0]."' WHERE rind_carga='".$datos['car_id']."' AND rind_estudiante='".$estudiante[0]."' AND rind_periodo='".$periodo."' AND rind_indicador='".$notInd[1]."'",$conexion);
					
				}
			}
			
			$sumaNotaIndicador = round($sumaNotaIndicador,1);
			
			$boletinNum = mysql_num_rows(mysql_query("SELECT * FROM academico_boletin WHERE bol_carga='".$datos['car_id']."' AND bol_periodo='".$periodo."' AND bol_estudiante='".$estudiante[0]."' AND bol_tipo=1",$conexion));
			if($boletinNum>0){
				mysql_query("UPDATE academico_boletin SET bol_nota_indicadores='".$sumaNotaIndicador."' WHERE bol_carga='".$datos['car_id']."' AND bol_periodo='".$periodo."' AND bol_estudiante='".$estudiante[0]."' AND bol_tipo=1",$conexion);
					
			}
			
			//$periodo++;
		//}	
		//echo "Terminó el estudiante: ".$estudiante[0]."<br>";
	}
	echo "--Terminó la carga: ".$datos['car_id']."<br>";
}
*/

/*
$consulta = mysql_query("SELECT * FROM academico_cargas WHERE car_docente=3959",$conexion);
while($datos = mysql_fetch_array($consulta)){
	$estudiantesConsulta = mysql_query("SELECT * FROM academico_matriculas WHERE mat_grado='".$datos['car_curso']."' AND mat_grupo='".$datos['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido",$conexion);
 	
	
	while($estudiante = mysql_fetch_array($estudiantesConsulta)){
		$periodo = 1;
		$sumaNotaIndicador = 0;
		while($periodo<=3){

			$notaRecuperadaInd = mysql_fetch_array(mysql_query("SELECT AVG(rind_nota), SUM(rind_nota_original) FROM academico_indicadores_recuperacion 
			WHERE rind_carga='".$datos['car_id']."' AND rind_estudiante='".$estudiante[0]."' AND rind_periodo='".$periodo."'",$conexion));
			
			
			$notaRecup = round($notaRecuperadaInd[0],1);
			
			$boletinNum = mysql_num_rows(mysql_query("SELECT * FROM academico_boletin WHERE bol_carga='".$datos['car_id']."' AND bol_periodo='".$periodo."' AND bol_estudiante='".$estudiante[0]."' AND bol_tipo=1 AND bol_nota<'".$notaRecup."'",$conexion));
			if($boletinNum>0){
				mysql_query("UPDATE academico_boletin SET bol_nota='".$notaRecup."', bol_tipo=2, bol_ultima_actualizacion=now(), bol_observaciones='Recuperada con los indicadores' WHERE bol_carga='".$datos['car_id']."' AND bol_periodo='".$periodo."' AND bol_estudiante='".$estudiante[0]."' AND bol_tipo=1",$conexion);
					
			}
			
			$periodo++;
		}	
		//echo "Terminó el estudiante: ".$estudiante[0]."<br>";
	}
	echo "--Terminó la carga: ".$datos['car_id']."<br>";
}
*/

/*
$recuperacionesInd = mysql_query("SELECT rind_id, rind_fecha_registro, rind_estudiante, rind_carga, rind_nota, rind_indicador, rind_periodo, rind_actualizaciones, rind_ultima_actualizacion,
rind_nota_anterior, rind_nota_original, rind_nota_actual, ipc_indicador, ipc_valor, round(rind_nota*(ipc_valor/100),2), ipc_periodo
FROM academico_indicadores_recuperacion
INNER JOIN academico_indicadores_carga ON ipc_carga=rind_carga AND ipc_indicador=rind_indicador
WHERE rind_nota>rind_nota_original
order by rind_periodo
",$conexion);

while($recupInd = mysql_fetch_array($recuperacionesInd)){
	mysql_query("UPDATE academico_indicadores_recuperacion SET rind_nota_actual='".$recupInd[14]."' WHERE rind_id='".$recupInd[0]."'",$conexion);
	
}
*/


/*
$indRecup = mysql_query("SELECT * FROM academico_indicadores_recuperacion WHERE rind_actualizaciones>=1 AND rind_nota>rind_nota_original",$conexion);

while($indR = mysql_fetch_array($indRecup)){
	
	$notaRecup = mysql_fetch_array(mysql_query("SELECT sum(rind_nota_actual) FROM academico_indicadores_recuperacion WHERE rind_periodo='".$indR['rind_periodo']."' AND rind_carga='".$indR['rind_carga']."' and rind_estudiante='".$indR['rind_estudiante']."'",$conexion));
	
	
	mysql_query("UPDATE academico_boletin SET bol_nota_anterior=bol_nota, bol_nota='".$notaRecup[0]."', bol_nota_indicadores='".$notaRecup[0]."', bol_tipo=3, bol_ultima_actualizacion=now(), bol_observaciones='Recuperada con los indicadores', bol_actualizaciones=bol_actualizaciones+1
	WHERE bol_carga='".$indR['rind_carga']."' AND bol_periodo='".$indR['rind_periodo']."' AND bol_estudiante='".$indR['rind_estudiante']."'",$conexion);
		
}
*/
