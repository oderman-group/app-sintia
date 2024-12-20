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
	
	while($area = mysqli_fetch_array($areas, MYSQLI_BOTH)){
		//OBTENER EL PROMEDIO POR AREA
		$asignaturas = CargaAcademica::calcularPromedioAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $area['ar_id'], $year);
		$a = 0;
		$promedioArea = 0;
		while($asignatura = mysqli_fetch_array($asignaturas, MYSQLI_BOTH)){

			$datosBoletinArea = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $datosUsr['mat_id'], $asignatura['car_id'], $year);
			
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
	$conCargas = CargaAcademica::calcularPromedioAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $area['ar_id'], $year);
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){

		$datosBoletin = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
		
		$datosAusencias = Clases::traerDatosAusencias($conexion, $config, $datosUsr['mat_id'], $datosCargas['car_id'], $periodoActual, $year);
		
		$indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $datosCargas['car_id'], $periodoActual, $year);
		
		//INDICADORES PERDIDOS DEL PERIODO ANTERIOR
		$indicadoresPeridos = Indicadores::traerDatosIndicadorPerdidos($config, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
		
		$acumulado = Boletin::traerDefinitivaBoletinCarga($config, $datosCargas['car_id'], $datosUsr['mat_id'], $year);
		
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
						$notaIndicadorPA = Calificaciones::consultaNotaIndicadoresPeriodos($config, $indicadorP['rind_indicador'], $datosUsr['mat_id'], $year);
						
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
						$notaIndicador = Calificaciones::consultaNotaIndicadores($config, $indicador['ipc_indicador'], $datosCargas['car_id'], $datosUsr['mat_id'], $periodoActual, $year);

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
