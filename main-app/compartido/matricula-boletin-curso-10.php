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
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
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
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if(!empty($_GET["id"])){$filtro .= " AND mat_id='".base64_decode($_GET["id"])."'";}
if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".base64_decode($_REQUEST["curso"])."'";}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
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
		//exit();
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
	
	<!--<div align="center" style="margin-bottom: 10px;"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="350"></div>-->
	
	<div align="center" style="margin-bottom: 10px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="200"><br>
    <!-- <?=$informacion_inst["info_nombre"]?><br>
    BOLETÍN DE CALIFICACIONES<br> --></div>
    
	<div style="width:100%">
        <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
            <tr>
                <td>C&oacute;digo:<br> <?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></td>
                <td>Nombre:<br> <?=$nombre?></td>
                <td>Grado:<br> <?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></td>
                <td>Puesto Curso:<br> <?=$puestoCurso;?></td>
            </tr>
            
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?=$informacion_inst["info_nombre"]?></td>
                <td colspan="2">Periodo:<br> <b><?=$periodoActual." (".$year.")";?></b></td>
               <!-- <td>Puesto Colegio:<br> &nbsp;</td>   -->
            </tr>
        </table>
        <p>&nbsp;</p>
    </div>
</div>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
    <thead>
        <tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
            <td width="20%" rowspan="2">AREAS / ASIGNATURAS</td>
            <td width="2%" rowspan="2">I.H.</td>
            
            <?php  
			for($j=1;$j<=$periodoActual;$j++){
				$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosUsr['gra_id'], $j);
				
				$porcentajeGrado=25;
				if(!empty($periodosCursos['gvp_valor'])){
					$porcentajeGrado=$periodosCursos['gvp_valor'];
				}
			?>
                <td width="3%" colspan="2"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$datosUsr['mat_id'];?>&periodo=<?=$j?>" style="color:#000; text-decoration:none;">Periodo <?=$j."<br>(".$porcentajeGrado."%)"?></a></td>
            <?php }?>
            <td width="3%" colspan="2">Acumulado</td>
        </tr> 
        
        <tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
            <?php  for($j=1;$j<=$periodoActual;$j++){ ?>

                <td width="3%">Nota</td>
                <td width="3%">Desempeño</td>
            <?php }?>
            <td width="3%">Nota</td>
            <td width="3%">Desempeño</td>

        </tr>
        
    </thead>
    
    <?php
	$materiasPerdidas = 0;
	$colspan = 2 + (2 * $periodoActual);
    $conAreas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $year);
	while($datosAreas = mysqli_fetch_array($conAreas, MYSQLI_BOTH)){
	?>
    <tbody>
        <!-- AREAS -->
		<tr style="background: lightgray; color:black; height: 30px; font-weight: bold; font-size: 14px;">
            <td colspan="<?=$colspan;?>"><?=strtoupper($datosAreas['ar_nombre']);?></td> 
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>

        </tr>
		
		<?php
		$contador=1;
		$conCargas = CargaAcademica::traerCargasAreasPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $datosAreas['ar_id'], $year);
		while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
		?>
		<!-- ASIGNATURAS -->
		<tr style="background:#fff; height: 25px; font-weight: bold;">
            <td><?=strtoupper($datosCargas['mat_nombre']);?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td> 
            <?php 
			$promedioMateria = 0;
			$sumaPorcentaje = 0;
			for($j=1;$j<=$periodoActual;$j++){
				$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosUsr['gra_id'], $j);
				
				$porcentajeGrado=25;
				if(!empty($periodosCursos['gvp_valor'])){
					$porcentajeGrado=$periodosCursos['gvp_valor'];
				}

				$decimal = $porcentajeGrado/100;
				
                $datosBoletin = Boletin::traerNotaBoletinCargaPeriodo($config, $j, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
				
				$consultaAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM ".BD_ACADEMICA.".academico_clases cls 
                INNER JOIN ".BD_ACADEMICA.".academico_ausencias aus ON aus.aus_id_clase=cls.cls_id AND aus.aus_id_estudiante<='".$datosUsr['mat_id']."' AND aus.institucion={$config['conf_id_institucion']} AND aus.year={$year}
                WHERE cls.cls_id_carga='".$datosCargas['car_id']."' AND cls.cls_periodo='".$j."' AND cls.institucion={$config['conf_id_institucion']} AND cls.year={$year}");
				$datosAusencias = mysqli_fetch_array($consultaAusencias, MYSQLI_BOTH);
				
				$promedioMateria +=$datosBoletin['bol_nota']*$decimal;
				$sumaPorcentaje += $decimal;
				$colorFondoNota = '';
				if($datosBoletin['bol_nota']!="" and $datosBoletin['bol_nota']<$config["conf_nota_minima_aprobar"]){$colorFondoNota = 'tomato';}
            ?>

                <td align="center" style="background-color: <?=$colorFondoNota;?>;"><?=$datosBoletin['bol_nota'];?></td>
                <td align="center"><?=$datosBoletin['notip_nombre'];?></td>
            <?php 
			}
			$promedioMateria = ($promedioMateria / $sumaPorcentaje);
			$promedioMateria = round(($promedioMateria), $config['conf_decimales_notas']);
			
			$colorFondoPromedioM = '';
			if($promedioMateria!="" and $promedioMateria<$config["conf_nota_minima_aprobar"]){$colorFondoPromedioM = 'tomato'; $materiasPerdidas++;}
			
			$promediosMateriaEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioMateria, $year);
			?>
            <td align="center" style="background-color: <?=$colorFondoPromedioM;?>"><?=$promedioMateria;?></td>
            <td align="center"><?=$promediosMateriaEstiloNota['notip_nombre'];?></td>

        </tr>
		
		
		<?php
		$indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $datosCargas['car_id'], $periodoActual, $year);
		while($ind = mysqli_fetch_array($indicadores, MYSQLI_BOTH)){
			$calificacionesIndicadores = Calificaciones::consultaNotaIndicadores($config, $ind['ipc_indicador'], $datosCargas['car_id'], $datosUsr['mat_id'], $periodoActual, $year);
		?>
		<!-- INDICADORES -->
		<tr>
            <td><?=$ind['ipc_indicador'].") ".$ind['ind_nombre'];?></td>
            <td align="center"><?=$ind['ipc_valor']."%";?></td> 
            <?php 
			$promedioMateria = 0;
			for($j=1;$j<=$periodoActual;$j++){

				$notaIndicadorFinal="&nbsp;";
				if($j==$periodoActual){
					$notaIndicadorFinal=$calificacionesIndicadores[0];
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $calificacionesIndicadores[0], $year);
						$notaIndicadorFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
					}
				}
            ?>
                <td align="center">&nbsp;</td>
                <td align="center"><?=$notaIndicadorFinal;?></td>

            <?php 
			}
			?>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>

        </tr>
		<?php }?>
		
		<?php }?>
		
		
    </tbody>
    <?php 
		$contador++;
	}
	?>

	<tfoot>
    	<tr style="font-weight:bold; text-align:center; font-size:13px;">
        	<td style="text-align:left;">PROMEDIO/TOTAL</td>
            <td>-</td> 

            <?php 
            for($j=1;$j<=$periodoActual;$j++){
				$consultaPromediosPeriodos=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),2) as promedio FROM ".BD_ACADEMICA.".academico_boletin  bol
                INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_id=bol_carga AND car_curso='".$datosUsr['mat_grado']."' AND car_grupo='".$datosUsr['mat_grupo']."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
                INNER JOIN ".BD_ACADEMICA.".academico_materias mat ON mat_id=car_materia AND mat_sumar_promedio='SI' AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$year}
                WHERE bol_estudiante='".$datosUsr['mat_id']."' AND bol_periodo='".$j."' AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}");
				$promediosPeriodos = mysqli_fetch_array($consultaPromediosPeriodos, MYSQLI_BOTH);
				
				$promediosEstiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promediosPeriodos['promedio'], $year);
            ?>
                <td><?=$promediosPeriodos['promedio'];?></td>
                <td><?=$promediosEstiloNota['notip_nombre'];?></td>
            <?php }?>

            <td>-</td>
            <td>-</td>
        </tr>
    </tfoot>

</table>
<p>&nbsp;</p>	

<?php
$estadoAgno = '';
if($periodoActual==$datosUsr['gra_periodos']){
	if($materiasPerdidas==0){$estadoAgno = 'PROMOVIDO';}
	elseif($materiasPerdidas>0 and $materiasPerdidas<$config["conf_num_materias_perder_agno"]){$estadoAgno = 'DEBE NIVELAR';}
	elseif($materiasPerdidas>=$config["conf_num_materias_perder_agno"]){$estadoAgno = 'NO FUE PROMOVIDO';}
}
?>
	
<table width="100%" cellspacing="5" cellpadding="5" rules="none" border="0">
	<tr>
        <td width="40%">
            ________________________________________________________________<br>
            DIRECTOR DE GRADO
        </td>
        <td width="20%">
        	<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            	<?php
				$contador=1;
				$estilosNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
				while($eN = mysqli_fetch_array($estilosNota, MYSQLI_BOTH)){
					if($contador%2==1){$fondoFila = '#EAEAEA';}else{$fondoFila = '#FFF';}
				?>
                <tr style="background:<?=$fondoFila;?>">
                	<td><?=$eN['notip_nombre'];?></td>
                    <td align="center"><?=$eN['notip_desde']." - ".$eN['notip_hasta'];?></td>
                </tr>
                <?php $contador++;}?>
            </table>
        </td>
        <td width="60%">
        	<p style="font-weight:bold;">Observaciones: <?=$estadoAgno;?></p>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________
        </td>
    </tr>
</table>

<div id="saltoPagina"></div>
                                   
<?php
}// FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

<!--
<script type="application/javascript">
print();
</script>   
-->                                 
                          
</body>
</html>
