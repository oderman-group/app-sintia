<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
    
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
Utilidades::validarInfoBoletin($matriculadosPorCurso);
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
$puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual,$matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
foreach($puestos as $puesto){
	if($puesto['estudiante_id']==$matriculadosDatos['mat_id']){$puestoCurso = $contp;}
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
	<title>Boletín Formato 5</title>
	<link rel="shortcut icon" href="../sintia-icono.png" />
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
								Jornada: <?=$informacion_inst["info_jornada"]?><br>
								<?=!empty($informacion_inst["info_resolucion"]) ? strtoupper($informacion_inst["info_resolucion"]) : "";?><br>
								<?=!empty($informacion_inst["info_direccion"]) ? strtoupper($informacion_inst["info_direccion"]) : "";?> <?=!empty($informacion_inst["info_telefono"]) ? "Tel(s). ".$informacion_inst["info_telefono"] : "";?><br>
								<?=!empty($informacion_inst["ciu_nombre"]) ? $informacion_inst["ciu_nombre"]."/".$informacion_inst["dep_nombre"] : "";?>
							</td>   
						</tr>
					</table>
				</td>
				
				<td width="30%">
					<table width="100%" border="1" rules="all">
						<tr align="center"><td colspan="2"><strong>EVALUACIÓN ACADÉMICA</strong></td></tr>
						
						<tr><td colspan="2"><strong>Alumno:</strong> <?=$nombre?></td></tr>
						
						<tr>
							<td><strong>Ruv:</strong> <?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></td>
							<td><strong>Documento:</strong><br><?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></td>
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
	$areas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
	
	// ============================================
	// OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
	// ============================================
	
	// OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para este estudiante y periodo
	$notasBoletinMapa = []; // [carga] => datos_nota
	try {
		$sqlNotas = "SELECT bol_carga, bol_nota, bol_observaciones_boletin
					 FROM " . BD_ACADEMICA . ".academico_boletin
					 WHERE bol_estudiante = ?
					   AND institucion = ?
					   AND year = ?
					   AND bol_periodo = ?";
		$paramNotas = [
			$datosUsr['mat_id'],
			$config['conf_id_institucion'],
			$year,
			$periodoActual
		];
		$resNotas = BindSQL::prepararSQL($sqlNotas, $paramNotas);
		while ($rowNota = mysqli_fetch_array($resNotas, MYSQLI_BOTH)) {
			$idCarga = $rowNota['bol_carga'];
			$notasBoletinMapa[$idCarga] = $rowNota;
		}
	} catch (Exception $eNotas) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 2: Pre-cargar todas las ausencias para este estudiante y periodo
	$ausenciasMapa = []; // [carga] => suma_ausencias
	try {
		$sqlAusencias = "SELECT cls.cls_id_carga, SUM(aus.aus_ausencias) as total_ausencias
						 FROM " . BD_ACADEMICA . ".academico_clases cls
						 INNER JOIN " . BD_ACADEMICA . ".academico_ausencias aus 
						     ON aus.aus_id_clase = cls.cls_id 
						     AND aus.aus_id_estudiante = ?
						     AND aus.institucion = cls.institucion 
						     AND aus.year = cls.year
						 WHERE cls.cls_periodo = ?
						   AND cls.institucion = ?
						   AND cls.year = ?
						 GROUP BY cls.cls_id_carga";
		$paramAusencias = [
			$datosUsr['mat_id'],
			$periodoActual,
			$config['conf_id_institucion'],
			$year
		];
		$resAusencias = BindSQL::prepararSQL($sqlAusencias, $paramAusencias);
		while ($rowAus = mysqli_fetch_array($resAusencias, MYSQLI_BOTH)) {
			$ausenciasMapa[$rowAus['cls_id_carga']] = (float)$rowAus['total_ausencias'];
		}
	} catch (Exception $eAus) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 3: Pre-cargar todas las definitivas para este estudiante
	$definitivasMapa = []; // [carga] => [def, desempeno]
	try {
		// Obtener todas las cargas del estudiante
		$idsCargas = [];
		mysqli_data_seek($areas, 0);
		while ($areaTemp = mysqli_fetch_array($areas, MYSQLI_BOTH)) {
			$asignaturasTemp = CargaAcademica::calcularPromedioAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $areaTemp['ar_id'], $year);
			while ($asigTemp = mysqli_fetch_array($asignaturasTemp, MYSQLI_BOTH)) {
				if (!in_array($asigTemp['car_id'], $idsCargas)) {
					$idsCargas[] = $asigTemp['car_id'];
				}
			}
		}
		mysqli_data_seek($areas, 0);
		
		if (!empty($idsCargas)) {
			foreach ($idsCargas as $idCarga) {
				$defTemp = Boletin::traerDefinitivaBoletinCarga($config, $idCarga, $datosUsr['mat_id'], $year);
				if (!empty($defTemp[0])) {
					$definitivasMapa[$idCarga] = $defTemp;
				}
			}
		}
	} catch (Exception $eDef) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 4: Pre-cargar todos los indicadores perdidos para este estudiante
	$indicadoresPerdidosMapa = []; // [carga] => array de indicadores perdidos
	try {
		mysqli_data_seek($areas, 0);
		while ($areaTemp = mysqli_fetch_array($areas, MYSQLI_BOTH)) {
			$asignaturasTemp = CargaAcademica::calcularPromedioAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $areaTemp['ar_id'], $year);
			while ($asigTemp = mysqli_fetch_array($asignaturasTemp, MYSQLI_BOTH)) {
				$idCarga = $asigTemp['car_id'];
				if (!isset($indicadoresPerdidosMapa[$idCarga])) {
					$indicadoresPerdidosMapa[$idCarga] = [];
					$indicadoresPerdidos = Indicadores::traerDatosIndicadorPerdidos($config, $datosUsr['mat_id'], $idCarga, $year);
					while ($indPerdido = mysqli_fetch_array($indicadoresPerdidos, MYSQLI_BOTH)) {
						$indicadoresPerdidosMapa[$idCarga][] = $indPerdido;
					}
				}
			}
		}
		mysqli_data_seek($areas, 0);
	} catch (Exception $eInd) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 5: Pre-cargar cache de notas cualitativas
	$notasCualitativasCache = [];
	if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
		$consultaNotasTipo = mysqli_query($conexion, 
			"SELECT notip_desde, notip_hasta, notip_nombre 
			 FROM ".BD_ACADEMICA.".academico_notas_tipos 
			 WHERE notip_categoria='".mysqli_real_escape_string($conexion, $config['conf_notas_categoria'])."' 
			 AND institucion=".(int)$config['conf_id_institucion']." 
			 AND year='".mysqli_real_escape_string($conexion, $year)."' 
			 ORDER BY notip_desde ASC");
		if($consultaNotasTipo){
			while($tipoNota = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)){
				// Pre-cargar cache para todos los valores posibles (de 0.1 en 0.1)
				for($i = $tipoNota['notip_desde']; $i <= $tipoNota['notip_hasta']; $i += 0.1){
					$key = number_format((float)$i, 1, '.', '');
					if(!isset($notasCualitativasCache[$key])){
						$notasCualitativasCache[$key] = $tipoNota['notip_nombre'];
					}
				}
			}
		}
	}
	
	while($area = mysqli_fetch_array($areas, MYSQLI_BOTH)){
		//OBTENER EL PROMEDIO POR AREA
		$asignaturas = CargaAcademica::calcularPromedioAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $area['ar_id'], $year);
		$a = 0;
		$promedioArea = 0;
		while($asignatura = mysqli_fetch_array($asignaturas, MYSQLI_BOTH)){
			// OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
			$datosBoletinArea = $notasBoletinMapa[$asignatura['car_id']] ?? null;
			if($datosBoletinArea === null){
				// Fallback: consulta individual si no está en el mapa
				$resTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $datosUsr['mat_id'], $asignatura['car_id'], $year);
				if($resTemp){
					$datosBoletinArea = mysqli_fetch_array($resTemp, MYSQLI_BOTH);
				} else {
					$datosBoletinArea = ['bol_nota' => 0];
				}
			}
			
			$promedioArea += !empty($datosBoletinArea['bol_nota']) ? (float)$datosBoletinArea['bol_nota'] : 0;
			$a++;
		}
		$promedioArea = $a > 0 ? ($promedioArea/$a) : 0;
		
		// Formatear promedio del área con decimales configurados
		$promedioAreaFormateado = Boletin::notaDecimales($promedioArea);
		
		$promedioAreaFinal = $promedioAreaFormateado;
		if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
			// OPTIMIZACIÓN: Usar cache de notas cualitativas
			$notaRedondeada = number_format((float)$promedioArea, $config['conf_decimales_notas'], '.', '');
			$promedioAreaFinal = isset($notasCualitativasCache[$notaRedondeada]) 
				? $notasCualitativasCache[$notaRedondeada] 
				: "";
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
	$conCargas = CargaAcademica::calcularPromedioAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $area['ar_id'], $year);
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){

		// OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
		$datosBoletin = $notasBoletinMapa[$datosCargas['car_id']] ?? null;
		if($datosBoletin === null){
			// Fallback: consulta individual si no está en el mapa
			$resTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
			if($resTemp){
				$datosBoletin = mysqli_fetch_array($resTemp, MYSQLI_BOTH);
			} else {
				$datosBoletin = ['bol_nota' => 0, 'notip_nombre' => ''];
			}
		}
		
		// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
		$ausencias = $ausenciasMapa[$datosCargas['car_id']] ?? 0;
		
		$indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $datosCargas['car_id'], $periodoActual, $year);
		
		// OPTIMIZACIÓN: Obtener indicadores perdidos del mapa pre-cargado
		$indicadoresPeridos = isset($indicadoresPerdidosMapa[$datosCargas['car_id']]) 
			? $indicadoresPerdidosMapa[$datosCargas['car_id']] 
			: [];
		// Convertir array a mysqli_result simulado para compatibilidad
		$indicadoresPeridosArray = $indicadoresPeridos;
		
		// OPTIMIZACIÓN: Obtener acumulado del mapa pre-cargado
		$acumulado = $definitivasMapa[$datosCargas['car_id']] ?? [0];
		
		// Formatear acumulado con decimales configurados
		$acumuladoNum = !empty($acumulado[0]) ? (float)$acumulado[0] : 0;
		$acumuladoFormateado = Boletin::notaDecimales($acumuladoNum);
		
		// OPTIMIZACIÓN: Usar cache de notas cualitativas para acumulado
		$acumuladoDesempeno = ['notip_nombre' => ''];
		if($config['conf_forma_mostrar_notas'] == CUALITATIVA && !empty($acumuladoNum)){
			$notaAcumRedondeada = number_format($acumuladoNum, $config['conf_decimales_notas'], '.', '');
			$acumuladoDesempeno['notip_nombre'] = isset($notasCualitativasCache[$notaAcumRedondeada]) 
				? $notasCualitativasCache[$notaAcumRedondeada] 
				: "";
		}

		// Formatear nota del boletín con decimales configurados
		$notaBoletinFormateada = !empty($datosBoletin['bol_nota']) ? Boletin::notaDecimales((float)$datosBoletin['bol_nota']) : '';
		$notaBoletin = !empty($notaBoletinFormateada) ? $notaBoletinFormateada."<br>".($datosBoletin['notip_nombre'] ?? '') : '';
		if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
			$notaBoletin = !empty($datosBoletin['notip_nombre']) ? $datosBoletin['notip_nombre'] : '';
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
					foreach($indicadoresPeridosArray as $indicadorP){
						$notaIndicadorPA = Calificaciones::consultaNotaIndicadoresPeriodos($config, $indicadorP['rind_indicador'], $datosUsr['mat_id'], $year);
						
						if($indicadorP['rind_periodo'] == $periodoActual){
							continue;
						}

                        // Formatear notas de indicadores perdidos con decimales configurados
                        $notaIndicadorPANum = !empty($notaIndicadorPA[0]) ? (float)$notaIndicadorPA[0] : 0;
                        $notaIndicadorPNum = !empty($indicadorP['rind_nota']) ? (float)$indicadorP['rind_nota'] : 0;
                        
                        $notaIndicadorPAFinal = Boletin::notaDecimales($notaIndicadorPANum);
                        $notaIndicadorPFinal = Boletin::notaDecimales($notaIndicadorPNum);
                        
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
							// OPTIMIZACIÓN: Usar cache de notas cualitativas
							$notaPARedondeada = number_format($notaIndicadorPANum, $config['conf_decimales_notas'], '.', '');
                            $notaIndicadorPAFinal = isset($notasCualitativasCache[$notaPARedondeada]) 
								? $notasCualitativasCache[$notaPARedondeada] 
								: "";

							$notaPRedondeada = number_format($notaIndicadorPNum, $config['conf_decimales_notas'], '.', '');
                            $notaIndicadorPFinal = isset($notasCualitativasCache[$notaPRedondeada]) 
								? $notasCualitativasCache[$notaPRedondeada] 
								: "";
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
						$notaIndicador = Calificaciones::consultaNotaIndicadores($config, $indicador['ipc_indicador'], $datosCargas['car_id'], $datosUsr['mat_id'], $periodoActual, $year);

                        // Formatear nota de indicador con decimales configurados
                        $notaIndicadorNum = !empty($notaIndicador[0]) ? (float)$notaIndicador[0] : 0;
                        $notaIndicadorFinal = Boletin::notaDecimales($notaIndicadorNum);
                        
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
							// OPTIMIZACIÓN: Usar cache de notas cualitativas
							$notaIndRedondeada = number_format($notaIndicadorNum, $config['conf_decimales_notas'], '.', '');
                            $notaIndicadorFinal = isset($notasCualitativasCache[$notaIndRedondeada]) 
								? $notasCualitativasCache[$notaIndRedondeada] 
								: "";
                        }
					?>
						<tr>
							<td width="90%"><?=$indicador['ind_nombre'];?></td>
							<td width="10%" align="center"><?=$notaIndicadorFinal;?></td>
						</tr>
					<?php
					}

					// Usar acumulado formateado con decimales configurados
					$notaAcumulado = $acumuladoFormateado."<br>".$acumuladoDesempeno['notip_nombre'];
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$notaAcumulado = $acumuladoDesempeno['notip_nombre'];
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
