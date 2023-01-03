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
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if(is_numeric($_GET["id"])){$filtro .= " AND mat_id='".$_GET["id"]."'";}
if(is_numeric($_REQUEST["curso"])){$filtro .= " AND mat_grado='".$_REQUEST["curso"]."'";}

$matriculadosPorCurso = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
INNER JOIN academico_grados ON gra_id=mat_grado
INNER JOIN academico_grupos ON gru_id=mat_grupo
INNER JOIN academico_cargas ON car_curso=mat_grado
INNER JOIN usuarios ON uss_id=car_docente
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
		//exit();
	}
$contp = 1;
$puestoCurso = 0;
$puestos = mysqli_query($conexion, "SELECT mat_id, bol_estudiante, bol_carga, mat_nombres, mat_grado, bol_periodo, avg(bol_nota) as prom FROM academico_matriculas
INNER JOIN academico_boletin ON bol_estudiante=mat_id AND bol_periodo='".$_GET["periodo"]."'
WHERE  mat_grado='".$matriculadosDatos['mat_grado']."' GROUP BY mat_id ORDER BY prom DESC");	
while($puesto = mysqli_fetch_array($puestos, MYSQLI_BOTH)){
	if($puesto['bol_estudiante']==$matriculadosDatos['mat_id']){$puestoCurso = $contp;}
	$contp ++;
}	
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
	
	<div align="center" style="margin-bottom: 10px;"><img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="150" width="200"></div>
    
	<div style="width:100%">
        <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
            <tr>
                <td>C&oacute;digo:<br> <?=number_format($matriculadosDatos["mat_documento"],0,",",".");?></td>
                <td>Nombre:<br> <?=strtoupper($matriculadosDatos[3]." ".$matriculadosDatos[4]." ".$matriculadosDatos["mat_nombres"]);?></td>
                <td>Grado:<br> <?=$matriculadosDatos["gra_nombre"]." ".$matriculadosDatos["gru_nombre"];?></td>
                <td>Puesto Curso:<br> <?=$puestoCurso;?></td>
            </tr>
            
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?=$informacion_inst["info_nombre"]?></td>
                <td colspan="2">Periodo:<br> <b><?=$_GET["periodo"]." (".$config['conf_agno'].")";?></b></td>
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
			for($j=1;$j<=$_GET["periodo"];$j++){
			$consultaPeriodosCursos=mysqli_query($conexion, "SELECT * FROM academico_grados_periodos
			WHERE gvp_grado='".$matriculadosDatos['gra_id']."' AND gvp_periodo='".$j."'");
			$periodosCursos = mysqli_fetch_array($consultaPeriodosCursos, MYSQLI_BOTH);
			$periodosCursos['gvp_valor'] = 25;
			?>
                <td width="3%" colspan="2"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$matriculadosDatos[0];?>&periodo=<?=$j?>" style="color:#000; text-decoration:none;">Periodo <?=$j."<br>(".$periodosCursos['gvp_valor']."%)"?></a></td>
            <?php }?>
            <td width="3%" colspan="2">Acumulado</td>
        </tr> 
        
        <tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
            <?php  for($j=1;$j<=$_GET["periodo"];$j++){ ?>

                <td width="3%">Nota</td>
                <td width="3%">Desempeño</td>
            <?php }?>
            <td width="3%">Nota</td>
            <td width="3%">Desempeño</td>

        </tr>
        
    </thead>
    
    <?php
	$materiasPerdidas = 0;
	$colspan = 2 + (2 * $_GET["periodo"]);
	$conAreas = mysqli_query($conexion, "SELECT * FROM academico_cargas
	INNER JOIN academico_materias ON mat_id=car_materia
	INNER JOIN academico_areas ON ar_id=mat_area
	WHERE car_curso='".$matriculadosDatos['mat_grado']."' AND car_grupo='".$matriculadosDatos['mat_grupo']."'
	GROUP BY mat_area
	ORDER BY ar_posicion
	");
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
		$conCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas
		INNER JOIN academico_materias ON mat_id=car_materia AND mat_area='".$datosAreas['ar_id']."'
		WHERE car_curso='".$matriculadosDatos['mat_grado']."' AND car_grupo='".$matriculadosDatos['mat_grupo']."'
		");
		while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
		?>
		<!-- ASIGNATURAS -->
		<tr style="background:#fff; height: 25px; font-weight: bold;">
            <td><?=strtoupper($datosCargas['mat_nombre']);?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td> 
            <?php 
			$promedioMateria = 0;
			$sumaPorcentaje = 0;
			for($j=1;$j<=$_GET["periodo"];$j++){
				$consultaPeriodosCursos=mysqli_query($conexion, "SELECT * FROM academico_grados_periodos
				WHERE gvp_grado='".$matriculadosDatos['gra_id']."' AND gvp_periodo='".$j."'");
				$periodosCursos = mysqli_fetch_array($consultaPeriodosCursos, MYSQLI_BOTH);
				
				$periodosCursos['gvp_valor'] = 25;

				$decimal = $periodosCursos['gvp_valor']/100;
				
				$consultaBoletin=mysqli_query($conexion, "SELECT * FROM academico_boletin 
                INNER JOIN academico_notas_tipos ON notip_categoria='".$config["conf_notas_categoria"]."' AND bol_nota>=notip_desde AND bol_nota<=notip_hasta
                WHERE bol_carga='".$datosCargas['car_id']."' AND bol_estudiante='".$matriculadosDatos['mat_id']."' AND bol_periodo='".$j."'");
                $datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH);
				
				$consultaAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM academico_clases 
                INNER JOIN academico_ausencias ON aus_id_clase=cls_id AND aus_id_estudiante<='".$matriculadosDatos['mat_id']."'
                WHERE cls_id_carga='".$datosCargas['car_id']."' AND cls_periodo='".$j."'");
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
			
			$consultaPromediosMateriasEstiloNotas=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
			WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promedioMateria."'>=notip_desde AND '".$promedioMateria."'<=notip_hasta");
			$promediosMateriaEstiloNota = mysqli_fetch_array($consultaPromediosMateriasEstiloNotas, MYSQLI_BOTH);
			?>
            <td align="center" style="background-color: <?=$colorFondoPromedioM;?>"><?=$promedioMateria;?></td>
            <td align="center"><?=$promediosMateriaEstiloNota['notip_nombre'];?></td>

        </tr>
		
		
		<?php
		$indicadores = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga
		INNER JOIN academico_indicadores ON ind_id=ipc_indicador
		WHERE ipc_carga='".$datosCargas['car_id']."' AND ipc_periodo='".$_GET["periodo"]."'
		");
		while($ind = mysqli_fetch_array($indicadores, MYSQLI_BOTH)){
			$consultaCalificacionesIndicadores=mysqli_query($conexion, "SELECT ROUND(AVG(cal_nota),2) FROM academico_calificaciones
			INNER JOIN academico_actividades ON act_id=cal_id_actividad AND act_id_tipo='".$ind['ipc_indicador']."' AND act_id_carga='".$datosCargas['car_id']."' AND act_periodo='".$_GET["periodo"]."' AND act_estado=1
			WHERE cal_id_estudiante='".$matriculadosDatos['mat_id']."'");
			$calificacionesIndicadores = mysqli_fetch_array($consultaCalificacionesIndicadores, MYSQLI_BOTH);
		?>
		<!-- INDICADORES -->
		<tr>
            <td><?=$ind['ipc_indicador'].") ".$ind['ind_nombre'];?></td>
            <td align="center"><?=$ind['ipc_valor']."%";?></td> 
            <?php 
			$promedioMateria = 0;
			for($j=1;$j<=$_GET["periodo"];$j++){
            ?>
                <td align="center">&nbsp;</td>
                <td align="center"><?php if($j==$_GET["periodo"])echo $calificacionesIndicadores[0]; else echo "&nbsp;";?></td>

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
            for($j=1;$j<=$_GET['periodo'];$j++){
				$consultaPromediosPeriodos=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),2) as promedio FROM academico_boletin 
                WHERE bol_estudiante='".$matriculadosDatos['mat_id']."' AND bol_periodo='".$j."'");
				$promediosPeriodos = mysqli_fetch_array($consultaPromediosPeriodos, MYSQLI_BOTH);
				
				$consultaPromediosEstiloNota=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
				WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promediosPeriodos['promedio']."'>=notip_desde AND '".$promediosPeriodos['promedio']."'<=notip_hasta");
				$promediosEstiloNota = mysqli_fetch_array($consultaPromediosEstiloNota, MYSQLI_BOTH);
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
if($_GET["periodo"]==$matriculadosDatos['gra_periodos']){
	if($materiasPerdidas==0){$estadoAgno = 'PROMOVIDO';}
	elseif($materiasPerdidas>0 and $materiasPerdidas<$config["conf_num_materias_perder_agno"]){$estadoAgno = 'DEBE NIVELAR';}
	elseif($materiasPerdidas>=$config["conf_num_materias_perder_agno"]){$estadoAgno = 'NO FUE PROMOVIDO';}
}
?>
	
<table width="100%" cellspacing="5" cellpadding="5" rules="none" border="0">
	<tr>
        <td width="40%">
            ________________________________________________________________<br>
            <?=strtoupper($matriculadosDatos['uss_nombre']);?><br>
            DIRECTOR DE GRADO
        </td>
        <td width="20%">
        	<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            	<?php
				$contador=1;
				$estilosNota = mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
				WHERE notip_categoria='".$config["conf_notas_categoria"]."'
				ORDER BY notip_desde DESC");
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
?>

<!--
<script type="application/javascript">
print();
</script>   
-->                                 
                          
</body>
</html>
