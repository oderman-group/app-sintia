<?php include("../directivo/session.php");?>
<?php include("../../config-general/config.php");?>
<?php
$modulo = 1;
if($_GET["periodo"]==""){
	$periodoActual = 1;
}else{
	$periodoActual = $_GET["periodo"];
}

if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==4) $periodoActuales = "Final";
if($periodoActual==$config[19]) $periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if(is_numeric($_GET["id"])){$filtro .= " AND mat_id='".$_GET["id"]."'";}
if(is_numeric($_REQUEST["curso"])){$filtro .= " AND mat_grado='".$_REQUEST["curso"]."'";}
$matriculadosPorCurso = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
INNER JOIN academico_grados ON gra_id=mat_grado
INNER JOIN academico_grupos ON gru_id=mat_grupo
LEFT JOIN academico_cargas ON car_curso=mat_grado AND car_grupo=mat_grupo AND car_director_grupo=1
LEFT JOIN usuarios ON uss_id=car_docente
WHERE mat_eliminado=0 $filtro 
GROUP BY mat_id
ORDER BY mat_grupo, mat_primer_apellido");

while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
	//contadores
	$contador_periodos = 0;
	$contador_indicadores = 0;
	$materiasPerdidas = 0;
	if($matriculadosDatos[0]==""){?>
		<script type="text/javascript">window.close();</script>
	<?php
		exit();
	}
$contp = 1;
$puestoCurso = 0;
$puestos = mysqli_query($conexion, "SELECT mat_id, bol_estudiante, bol_carga, mat_nombres, mat_grado, bol_periodo, avg(bol_nota) as prom FROM academico_matriculas
INNER JOIN academico_boletin ON bol_estudiante=mat_id AND bol_periodo='".$_GET["periodo"]."'
WHERE  mat_grado='".$matriculadosDatos['mat_grado']."' AND mat_grupo='".$matriculadosDatos['mat_grupo']."' GROUP BY mat_id ORDER BY prom DESC");	
while($puesto = mysqli_fetch_array($puestos, MYSQLI_BOTH)){
	if($puesto['bol_estudiante']==$matriculadosDatos['mat_id']){$puestoCurso = $contp;}
	$contp ++;
}

$numMatriculados = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM academico_matriculas
WHERE mat_eliminado=0 AND mat_grado='".$matriculadosDatos['mat_grado']."' AND mat_grupo='".$matriculadosDatos['mat_grupo']."'
GROUP BY mat_id
ORDER BY mat_grupo, mat_primer_apellido"));
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
	<style>
    	#saltoPagina{PAGE-BREAK-AFTER: always;}
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
</head>

<body style="font-family:Arial; font-size:9px;">

<div>
    <div style="float:right; width:100%">
        <table width="100%" border="1" rules="all">
			<tr>
                <td width="20%" align="center"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="100"></td>  
				
				<td width="50%">
					<table align="center">
						<tr align="center">
							<td align="center">
								<h2><?=$informacion_inst["info_nombre"]?></h2>
								Jornada: Completa<br>
								Aprobación y Resolución :  Lic. de funcionamiento 001183 de Octubre 8 de 2004<br>
								CARRERA 43 N. 70 - 206 - Tel(s). 3312532<br>
								Barranquilla<br>
								Correo :info@maxtrummer.edu.co 
							</td>   
						</tr>
					</table>
				</td>
				
				<td width="30%">
					<table width="100%" border="1" rules="all">
						<tr align="center"><td colspan="2"><strong>EVALUACIÓN ACADÉMICA</strong></td></tr>
						
						<tr><td colspan="2"><strong>Alumno:</strong> <?=strtoupper($matriculadosDatos[3]." ".$matriculadosDatos[4]." ".$matriculadosDatos["mat_nombres"]);?></td></tr>
						
						<tr>
							<td><strong>Ruv:</strong> <?=number_format($matriculadosDatos["mat_documento"],0,",",".");?></td>
							<td><strong>Documento:</strong><br><?=number_format($matriculadosDatos["mat_documento"],0,",",".");?></td>
						</tr>
						
						<tr><td colspan="2"><strong>Grado: </strong><?=$matriculadosDatos["gra_nombre"]." ".$matriculadosDatos["gru_nombre"];?></td></tr>
						
						<tr>
							<td><strong>Periodo:</strong> <?=$periodoActuales;?></td>
							<td><strong>Año escolar:</strong> <?=$config["conf_agno"];?></td>
						</tr>
						
						<tr>
							<td><strong># Estudiantes:</strong> <?=$numMatriculados;?></td>
							<td>
								<?php if($matriculadosDatos['mat_grado']<27){?>
									<strong>Puesto Curso: </strong><?=$puestoCurso;?>
								<?php }?>
							</td>
						</tr>
					</table>
				</td>
            </tr>
            
        </table>
        
    </div>
</div>

<br>
	
<table width="100%" rules="all" border="1">
    <thead>
        <tr style="font-weight:bold; text-align:center;">
            <td width="12%">ASIGNATURAS</td>
            <td width="2%">Ihs.</td>
			<td width="2%">Aus.</td>
			<td width="2%">Eva.</td>
			<td width="80%">AREAS/ LOGROS ACADÉMICOS/ Observaciones</td>
            <td width="2%">Acumulado</td>
        </tr>
        
    </thead>
	
    <tbody>
    <?php
	//AREAS
	$contador=1;
	$areas = mysqli_query($conexion, "SELECT * FROM academico_cargas
	INNER JOIN academico_materias ON mat_id=car_materia
	INNER JOIN academico_areas ON ar_id=mat_area
	WHERE car_curso='".$matriculadosDatos['mat_grado']."' AND car_grupo='".$matriculadosDatos['mat_grupo']."'
	GROUP BY mat_area
	");
	
	while($area = mysqli_fetch_array($areas, MYSQLI_BOTH)){
		//OBTENER EL PROMEDIO POR AREA
		$asignaturas = mysqli_query($conexion, "SELECT * FROM academico_cargas
		INNER JOIN academico_materias ON mat_id=car_materia AND mat_area='".$area['ar_id']."'
		INNER JOIN academico_areas ON ar_id=mat_area
		WHERE car_curso='".$matriculadosDatos['mat_grado']."' AND car_grupo='".$matriculadosDatos['mat_grupo']."'");
		$a = 0;
		$promedioArea = 0;
		while($asignatura = mysqli_fetch_array($asignaturas, MYSQLI_BOTH)){

			$consultaDatosBoletinArea=mysqli_query($conexion, "SELECT * FROM academico_boletin
			WHERE bol_carga='".$asignatura['car_id']."' AND bol_estudiante='".$matriculadosDatos['mat_id']."' AND bol_periodo='".$_GET["periodo"]."'");
			$datosBoletinArea = mysqli_fetch_array($consultaDatosBoletinArea, MYSQLI_BOTH);
			
			$promedioArea += $datosBoletinArea['bol_nota'];
			$a++;
		}
		$promedioArea = round(($promedioArea/$a),1);
	?>
    
		<tr style="font-weight:bold;">
            <td width="12%">&nbsp;</td>
            <td width="2%">&nbsp;</td>
			<td width="2%">&nbsp;</td>
			<td width="2%" align="center" style="font-size: 14px; font-weight: bold;"><?=$promedioArea;?></td>
			<td width="80%"><?=$area['ar_nombre'];?></td>
            <td width="2%">&nbsp;</td>
        </tr>
	<?php 
	//ASIGNATURAS
	$conCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas
	INNER JOIN academico_materias ON mat_id=car_materia AND mat_area='".$area['ar_id']."'
	INNER JOIN academico_areas ON ar_id=mat_area
	WHERE car_curso='".$matriculadosDatos['mat_grado']."' AND car_grupo='".$matriculadosDatos['mat_grupo']."'");
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){

		$consultaDatosBoletin=mysqli_query($conexion, "SELECT * FROM academico_boletin 
        INNER JOIN academico_notas_tipos ON notip_categoria='".$config["conf_notas_categoria"]."' AND bol_nota>=notip_desde AND bol_nota<=notip_hasta
        WHERE bol_carga='".$datosCargas['car_id']."' AND bol_estudiante='".$matriculadosDatos['mat_id']."' AND bol_periodo='".$_GET["periodo"]."'");
        $datosBoletin = mysqli_fetch_array($consultaDatosBoletin, MYSQLI_BOTH);
		
		$consultaDatosAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM academico_clases 
        INNER JOIN academico_ausencias ON aus_id_clase=cls_id AND aus_id_estudiante='".$matriculadosDatos['mat_id']."'
        WHERE cls_id_carga='".$datosCargas['car_id']."' AND cls_periodo='".$_GET["periodo"]."'");
		$datosAusencias = mysqli_fetch_array($consultaDatosAusencias, MYSQLI_BOTH);
		
		$indicadores = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
		INNER JOIN academico_indicadores ON ind_id=ipc_indicador
		WHERE ipc_carga='".$datosCargas['car_id']."' AND ipc_periodo='".$_GET["periodo"]."'");
		
		//INDICADORES PERDIDOS DEL PERIODO ANTERIOR
		$indicadoresPeridos = mysqli_query($conexion, "SELECT * FROM academico_indicadores_recuperacion
		INNER JOIN academico_indicadores ON ind_id=rind_indicador
		WHERE rind_carga='".$datosCargas['car_id']."' AND rind_estudiante='".$matriculadosDatos['mat_id']."' AND rind_nota>rind_nota_original
		");
		
		$consultaAcumulado=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),1) FROM academico_boletin
        WHERE bol_carga='".$datosCargas['car_id']."' AND bol_estudiante='".$matriculadosDatos['mat_id']."'");
		$acumulado = mysqli_fetch_array($consultaAcumulado, MYSQLI_BOTH);
		
		$consultaAcumuladoDesempeno=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
		WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND $acumulado[0]>=notip_desde AND $acumulado[0]<=notip_hasta");
		$acumuladoDesempeno = mysqli_fetch_array($consultaAcumuladoDesempeno, MYSQLI_BOTH);
	?>
        <tr>
            <td><?=$datosCargas['mat_nombre'];?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td>
			<td align="center"><?=round($datosAusencias[0],0);?></td>
			<td align="center" style="font-size: 12px; font-weight: bold;"><?=$datosBoletin['bol_nota'];?><br><?=$datosBoletin['notip_nombre'];?></td>
			
			<td>
				<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
					<?php
					//INDICADORES PERDIDOS
					while($indicadorP = mysqli_fetch_array($indicadoresPeridos, MYSQLI_BOTH)){
						$consultaNotaIndicadorPA=mysqli_query($conexion, "SELECT ROUND(AVG(cal_nota),1) FROM academico_calificaciones
						INNER JOIN academico_actividades ON act_id=cal_id_actividad AND act_id_tipo='".$indicadorP['rind_indicador']."'
						WHERE cal_id_estudiante='".$matriculadosDatos['mat_id']."'");
						$notaIndicadorPA = mysqli_fetch_array($consultaNotaIndicadorPA, MYSQLI_BOTH);
						
						if($indicadorP['rind_periodo'] == $_GET["periodo"]){
							continue;
						}
					?>
						<tr>
							<td width="90%"><b>P.<?=$indicadorP['rind_periodo'];?> Nota <?=$notaIndicadorPA[0];?>  Rec. <?=$indicadorP['rind_nota'];?></b> <?=$indicadorP['ind_nombre'];?></td>
							<td width="10%" align="center">&nbsp;</td>
						</tr>
					<?php
					}
					?>
					
					<?php
					//INDICADORES
					while($indicador = mysqli_fetch_array($indicadores, MYSQLI_BOTH)){
						$consultaNotaIndicador=mysqli_query($conexion, "SELECT ROUND(AVG(cal_nota),1) FROM academico_calificaciones
						INNER JOIN academico_actividades ON act_id=cal_id_actividad AND act_id_tipo='".$indicador['ipc_indicador']."' AND act_id_carga='".$datosCargas['car_id']."' AND act_periodo='".$_GET["periodo"]."' AND act_estado=1
						WHERE cal_id_estudiante='".$matriculadosDatos['mat_id']."'");
						$notaIndicador = mysqli_fetch_array($consultaNotaIndicador, MYSQLI_BOTH);
					?>
						<tr>
							<td width="90%"><?=$indicador['ind_nombre'];?></td>
							<td width="10%" align="center"><?=$notaIndicador[0];?></td>
						</tr>
					<?php
					}
					?>
				</table>
			</td>
            
            <td align="center" style="font-size: 12px; font-weight: bold;"><?=$acumulado[0];?><br><?=$acumuladoDesempeno['notip_nombre'];?></td>
        </tr>
    
<?php 
		$contador++;
	}
}
?>
</tbody>
	<!--
    <tfoot>
    	<tr style="font-weight:bold; text-align:center;">
        	<td style="text-align:left;">PROMEDIO/TOTAL</td>
            <td>-</td> 
            <?php 
				$consultaPromedioPeriodos=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),2) as promedio FROM academico_boletin 
                WHERE bol_estudiante='".$matriculadosDatos['mat_id']."' AND bol_periodo='".$j."'");
				$promediosPeriodos = mysqli_fetch_array($consultaPromedioPeriodos, MYSQLI_BOTH);
				
				$consultaSumaAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM academico_clases 
                INNER JOIN academico_ausencias ON aus_id_clase=cls_id AND aus_id_estudiante='".$matriculadosDatos['mat_id']."'
                WHERE cls_periodo='".$j."'");
				$sumaAusencias = mysqli_fetch_array($consultaSumaAusencias, MYSQLI_BOTH);
				
				$consultaPromedioEstiloNota=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
				WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promediosPeriodos['promedio']."'>=notip_desde AND '".$promediosPeriodos['promedio']."'<=notip_hasta");
				$promediosEstiloNota = mysqli_fetch_array($consultaPromedioEstiloNota, MYSQLI_BOTH);
            ?>
                <td><?php //echo $sumaAusencias[0];?></td>
                <td><?=$promediosPeriodos['promedio'];?></td>
                <td><?=$promediosEstiloNota['notip_nombre'];?></td>
				<td><?=$promediosEstiloNota['notip_nombre'];?></td>
        </tr>
    </tfoot>
	-->
</table>
<p>&nbsp;</p>
	
<table width="100%" rules="all" border="1">
	<tr>

        	<?php
				$contador=1;
				$estilosNota = mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
				WHERE notip_categoria='".$config["conf_notas_categoria"]."'
				ORDER BY notip_desde DESC");
				while($eN = mysqli_fetch_array($estilosNota, MYSQLI_BOTH)){
				?>

                	<td><?=$eN['notip_desde']." - ".$eN['notip_hasta'];?> <?=$eN['notip_nombre'];?></td>
                <?php $contador++;}?>
	</tr>	

</table>
	
		<?php
		$msjPromocion = '';
		if($periodoActual==$config['conf_periodos_maximos']){
			if($materiasPerdidas==0){$msjPromocion = 'PROMOVIDO';}
			else{$msjPromocion = 'NO PROMOVIDO';}	
		}
		?>
<table width="100%" rules="all" border="1">
	<tr>
        <td width="50%">
           Observaciones:
			<p>&nbsp;</p><p>&nbsp;</p>
        </td>
		
		<td width="50%" align="center">
            <p>&nbsp;</p><p>&nbsp;</p>
			<?=strtoupper($matriculadosDatos['uss_nombre']);?><br>
			Director de grupo<br>	
        </td>
    </tr>
</table>


<div id="saltoPagina"></div>                                    
<?php
}// FIN DE TODOS LOS MATRICULADOS
?>


<script type="application/javascript">
print();
</script>   
                              
                          
</body>
</html>
