<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
    
$year=$_SESSION["bd"];
if(isset($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

$modulo = 1;
if(empty($_GET["periodo"])){
	$periodoActual = 1;
}else{
	$periodoActual = base64_decode($_GET["periodo"]);
}

if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==4) $periodoActuales = "Final";
if($periodoActual==$config[19]) $periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if(!empty($_GET["id"])){$filtro .= " AND mat_id='".base64_decode($_GET["id"])."'";}
if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".base64_decode($_REQUEST["curso"])."'";}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro,$year);
$numMatriculados = mysqli_num_rows($matriculadosPorCurso);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
	//contadores
	$contador_periodos = 0;
	$contador_indicadores = 0;
	$materiasPerdidas = 0;
	if($matriculadosDatos['mat_id']==""){?>
		<script type="text/javascript">window.close();</script>
	<?php
		exit();
	}
$contp = 1;
$puestoCurso = 0;
$puestos = mysqli_query($conexion, "SELECT mat_id, bol_estudiante, bol_carga, mat_nombres, mat_grado, bol_periodo, avg(bol_nota) as prom FROM ".BD_ACADEMICA.".academico_matriculas mat
INNER JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol_estudiante=mat_id AND bol_periodo='".$periodoActual."' AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}
WHERE  mat_grado='".$matriculadosDatos['mat_grado']."' AND mat_grupo='".$matriculadosDatos['mat_grupo']."' AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$year} GROUP BY mat_id ORDER BY prom DESC");	
while($puesto = mysqli_fetch_array($puestos, MYSQLI_BOTH)){
	if($puesto['bol_estudiante']==$matriculadosDatos['mat_id']){$puestoCurso = $contp;}
	$contp ++;
}
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);
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
						
						<tr><td colspan="2"><strong>Alumno:</strong> <?=$nombre?></td></tr>
						
						<tr>
							<td><strong>Ruv:</strong> <?=number_format($datosUsr["mat_documento"],0,",",".");?></td>
							<td><strong>Documento:</strong><br><?=number_format($datosUsr["mat_documento"],0,",",".");?></td>
						</tr>
						
						<tr><td colspan="2"><strong>Grado: </strong><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></td></tr>
						
						<tr>
							<td><strong>Periodo:</strong> <?=$periodoActuales;?></td>
							<td><strong>Año escolar:</strong> <?=$year;?></td>
						</tr>
						
						<tr>
							<td><strong># Estudiantes:</strong> <?=$numMatriculados;?></td>
							<td>
								<?php if($datosUsr['mat_grado']<27){?>
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
	$areas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
	INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
	INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
	WHERE car_curso='".$datosUsr['mat_grado']."' AND car_grupo='".$datosUsr['mat_grupo']."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
	GROUP BY am.mat_area
	");
	
	while($area = mysqli_fetch_array($areas, MYSQLI_BOTH)){
		//OBTENER EL PROMEDIO POR AREA
		$asignaturas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
		INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.mat_area='".$area['ar_id']."' AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
		INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
		WHERE car_curso='".$datosUsr['mat_grado']."' AND car_grupo='".$datosUsr['mat_grupo']."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}");
		$a = 0;
		$promedioArea = 0;
		while($asignatura = mysqli_fetch_array($asignaturas, MYSQLI_BOTH)){

			$consultaDatosBoletinArea=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_boletin
			WHERE bol_carga='".$asignatura['car_id']."' AND bol_estudiante='".$datosUsr['mat_id']."' AND bol_periodo='".$periodoActual."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
			$datosBoletinArea = mysqli_fetch_array($consultaDatosBoletinArea, MYSQLI_BOTH);
			
			$promedioArea += $datosBoletinArea['bol_nota'];
			$a++;
		}
		$promedioArea = round(($promedioArea/$a),1);

		$promedioAreaFinal=$promedioArea;
		if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
		  $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioArea, $year);
		  $promedioAreaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
		}
	?>
    
		<tr style="font-weight:bold;">
            <td width="12%">&nbsp;</td>
            <td width="2%">&nbsp;</td>
			<td width="2%">&nbsp;</td>
			<td width="2%" align="center" style="font-size: 14px; font-weight: bold;"><?=$promedioAreaFinal;?></td>
			<td width="80%"><?=$area['ar_nombre'];?></td>
            <td width="2%">&nbsp;</td>
        </tr>
	<?php 
	//ASIGNATURAS
	$conCargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
	INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.mat_area='".$area['ar_id']."' AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
	INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
	WHERE car_curso='".$datosUsr['mat_grado']."' AND car_grupo='".$datosUsr['mat_grupo']."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}");
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){

		$consultaDatosBoletin=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_boletin bol 
        INNER JOIN ".BD_ACADEMICA.".academico_notas_tipos ntp ON ntp.notip_categoria='".$config["conf_notas_categoria"]."' AND bol_nota>=ntp.notip_desde AND bol_nota<=ntp.notip_hasta AND ntp.institucion={$config['conf_id_institucion']} AND ntp.year={$year}
        WHERE bol_carga='".$datosCargas['car_id']."' AND bol_estudiante='".$datosUsr['mat_id']."' AND bol_periodo='".$periodoActual."' AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}");
        $datosBoletin = mysqli_fetch_array($consultaDatosBoletin, MYSQLI_BOTH);
		
		$datosAusencias = Clases::traerDatosAusencias($conexion, $config, $datosUsr['mat_id'], $datosCargas['car_id'], $periodoActual, $year);
		
		$indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $datosCargas['car_id'], $periodoActual, $year);
		
		//INDICADORES PERDIDOS DEL PERIODO ANTERIOR
		$indicadoresPeridos = Indicadores::traerDatosIndicadorPerdidos($config, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
		
		$consultaAcumulado=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),1) FROM ".BD_ACADEMICA.".academico_boletin
        WHERE bol_carga='".$datosCargas['car_id']."' AND bol_estudiante='".$datosUsr['mat_id']."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
		$acumulado = mysqli_fetch_array($consultaAcumulado, MYSQLI_BOTH);
		
		$acumuladoDesempeno = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $acumulado[0], $year);

		$ausencias=0;
		if(!empty($datosAusencias[0])){
			$ausencias = $datosAusencias[0];
		}

		$notaBoletin=$datosBoletin['bol_nota']."<br>".$datosBoletin['notip_nombre'];
		if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
			$notaBoletin= $datosBoletin['notip_nombre'];
		}
	?>
        <tr>
            <td><?=$datosCargas['mat_nombre'];?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td>
			<td align="center"><?=round($ausencias,0);?></td>
			<td align="center" style="font-size: 12px; font-weight: bold;"><?=$notaBoletin;?></td>
			
			<td>
				<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
					<?php
					//INDICADORES PERDIDOS
					while($indicadorP = mysqli_fetch_array($indicadoresPeridos, MYSQLI_BOTH)){
						$consultaNotaIndicadorPA=mysqli_query($conexion, "SELECT ROUND(AVG(cal_nota),1) FROM ".BD_ACADEMICA.".academico_calificaciones aac
						INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad AND aa.act_id_tipo='".$indicadorP['rind_indicador']."' AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$year}
						WHERE aac.cal_id_estudiante='".$datosUsr['mat_id']."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$year}");
						$notaIndicadorPA = mysqli_fetch_array($consultaNotaIndicadorPA, MYSQLI_BOTH);
						
						if($indicadorP['rind_periodo'] == $periodoActual){
							continue;
						}

                        $notaIndicadorPAFinal=$notaIndicadorPA[0];
                        $notaIndicadorPFinal=$indicadorP['rind_nota'];
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                            $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaIndicadorPA[0], $year);
                            $notaIndicadorPAFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";

                            $estiloNotaP = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $indicadorP['rind_nota'], $year);
                            $notaIndicadorPFinal= !empty($estiloNotaP['notip_nombre']) ? $estiloNotaP['notip_nombre'] : "";
                        }
					?>
						<tr>
							<td width="90%"><b>P.<?=$indicadorP['rind_periodo'];?> Nota <?=$notaIndicadorPAFinal;?>  Rec. <?=$notaIndicadorPFinal;?></b> <?=$indicadorP['ind_nombre'];?></td>
							<td width="10%" align="center">&nbsp;</td>
						</tr>
					<?php
					}
					?>
					
					<?php
					//INDICADORES
					while($indicador = mysqli_fetch_array($indicadores, MYSQLI_BOTH)){
						$consultaNotaIndicador=mysqli_query($conexion, "SELECT ROUND(AVG(cal_nota),1) FROM ".BD_ACADEMICA.".academico_calificaciones aac
						INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad AND aa.act_id_tipo='".$indicador['ipc_indicador']."' AND aa.act_id_carga='".$datosCargas['car_id']."' AND aa.act_periodo='".$periodoActual."' AND aa.act_estado=1 AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$year}
						WHERE aac.cal_id_estudiante='".$datosUsr['mat_id']."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$year}");
						$notaIndicador = mysqli_fetch_array($consultaNotaIndicador, MYSQLI_BOTH);

                        $notaIndicadorFinal=$notaIndicador[0];
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                            $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaIndicador[0], $year);
                            $notaIndicadorFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                        }
					?>
						<tr>
							<td width="90%"><?=$indicador['ind_nombre'];?></td>
							<td width="10%" align="center"><?=$notaIndicadorFinal;?></td>
						</tr>
					<?php
					}

					$notaAcumulado=$acumulado[0]."<br>".$acumuladoDesempeno['notip_nombre'];
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$notaAcumulado= $acumuladoDesempeno['notip_nombre'];
					}
					?>
				</table>
			</td>
            
            <td align="center" style="font-size: 12px; font-weight: bold;"><?=$notaAcumulado;?></td>
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
				$consultaPromedioPeriodos=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),2) as promedio FROM ".BD_ACADEMICA.".academico_boletin 
                WHERE bol_estudiante='".$datosUsr['mat_id']."' AND bol_periodo='".$j."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
				$promediosPeriodos = mysqli_fetch_array($consultaPromedioPeriodos, MYSQLI_BOTH);
				
				$consultaSumaAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM ".BD_ACADEMICA.".academico_clases cls 
                INNER JOIN ".BD_ACADEMICA.".academico_ausencias aus ON aus.aus_id_clase=cls.cls_id AND aus.aus_id_estudiante='".$datosUsr['mat_id']."' AND aus.institucion={$config['conf_id_institucion']} AND aus.year={$year}
                WHERE cls.cls_periodo='".$j."' AND cls.institucion={$config['conf_id_institucion']} AND cls.year={$year}");
				$sumaAusencias = mysqli_fetch_array($consultaSumaAusencias, MYSQLI_BOTH);
				
				$promediosEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promediosPeriodos['promedio'], $year);
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
				$estilosNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
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
			Director de grupo<br>	
        </td>
    </tr>
</table>


<div id="saltoPagina"></div>                                    
<?php
}// FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>


<script type="application/javascript">
print();
</script>   
                              
                          
</body>
</html>
