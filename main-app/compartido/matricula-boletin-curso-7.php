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
INNER JOIN academico_cargas ON car_curso=mat_grado AND car_grupo=mat_grupo AND car_director_grupo=1
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

$consultaNumMatriculados=mysqli_query($conexion, "SELECT * FROM academico_matriculas
WHERE mat_eliminado=0 AND mat_grado='".$matriculadosDatos['mat_grado']."' AND mat_grupo='".$matriculadosDatos['mat_grupo']."'
GROUP BY mat_id
ORDER BY mat_grupo, mat_primer_apellido");
$numMatriculados = mysqli_num_rows($consultaNumMatriculados);
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
    <div style="float:left; width:50%"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="80"></div>
    <div style="float:right; width:50%">
        <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
            <tr>
                <td>C&oacute;digo:<br> <?=number_format($matriculadosDatos["mat_documento"],0,",",".");?></td>
                <td>Nombre:<br> <?=strtoupper($matriculadosDatos[3]." ".$matriculadosDatos[4]." ".$matriculadosDatos["mat_nombres"]);?></td>
                <td>Grado:<br> <?=$matriculadosDatos["gra_nombre"]." ".$matriculadosDatos["gru_nombre"];?></td>
                <td>Puesto Curso:<br> <?=$puestoCurso." de ".$numMatriculados;?></td>   
            </tr>
            
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?=$informacion_inst["info_nombre"]?></td>
                <td>Periodo:<br> <b><?=$config["conf_periodo"]." (".$config["conf_agno"].")";?></b></td>
                <td>Fecha Impresión:<br> <?=date("d/m/Y H:i:s");?></td>
            </tr>
        </table>
        <p>&nbsp;</p>
    </div>
</div>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
    <thead>
        <tr style="font-weight:bold; text-align:center;">
            <td width="20%" rowspan="2">ASIGNATURAS</td>
            <td width="2%" rowspan="2">I.H.</td>
            
            <?php  for($j=1;$j<=$config["conf_periodo"];$j++){ ?>
                <td width="3%" colspan="3"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$matriculadosDatos[0];?>&periodo=<?=$j?>" style="color:#000; text-decoration:none;">Periodo <?=$j?></a></td>
            <?php }?>
            <td width="3%" colspan="3">Final</td>
        </tr> 
        
        <tr style="font-weight:bold; text-align:center;">
            <?php  for($j=1;$j<=$config["conf_periodo"];$j++){ ?>
                <td width="3%">Fallas</td>
                <td width="3%">Nota</td>
                <td width="3%">Nivel</td>
            <?php }?>
            <td width="3%">Nota</td>
            <td width="3%">Nivel</td>
            <td width="3%">Hab</td>
        </tr>
        
    </thead>
    
    <?php
	$contador=1;
	$conCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas
	INNER JOIN academico_materias ON mat_id=car_materia
	WHERE car_curso='".$matriculadosDatos['mat_grado']."' AND car_grupo='".$matriculadosDatos['mat_grupo']."'");
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
		if($contador%2==1){$fondoFila = '#EAEAEA';}else{$fondoFila = '#FFF';}
	?>
    <tbody>
        <tr style="background:<?=$fondoFila;?>">
            <td><?=$datosCargas['mat_nombre'];?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td> 
            <?php 
			$promedioMateria = 0;
			for($j=1;$j<=$config["conf_periodo"];$j++){
				
                $consultaDatosBoletin=mysqli_query($conexion, "SELECT * FROM academico_boletin 
                INNER JOIN academico_notas_tipos ON notip_categoria='".$config["conf_notas_categoria"]."' AND bol_nota>=notip_desde AND bol_nota<=notip_hasta
                WHERE bol_carga='".$datosCargas['car_id']."' AND bol_estudiante='".$matriculadosDatos['mat_id']."' AND bol_periodo='".$j."'")
                $datosBoletin = mysqli_fetch_array($consultaDatosBoletin, MYSQLI_BOTH);
				
                $consultaDatosAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM academico_clases 
                INNER JOIN academico_ausencias ON aus_id_clase=cls_id AND aus_id_estudiante='".$matriculadosDatos['mat_id']."'
                WHERE cls_id_carga='".$datosCargas['car_id']."' AND cls_periodo='".$j."'");
				$datosAusencias = mysqli_fetch_array($consultaDatosAusencias, MYSQLI_BOTH);
				
				$promedioMateria +=$datosBoletin['bol_nota'];
            ?>
                <td align="center"><?=round($datosAusencias[0],0);?></td>
                <td align="center"><?=$datosBoletin['bol_nota'];?></td>
                <td align="center"><?=$datosBoletin['notip_nombre'];?></td>
            <?php 
			}
			$promedioMateria = round($promedioMateria/($j-1),2);
			$promedioMateriaFinal = $promedioMateria;
            $consultaNivelacion=mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_id_asg='".$datosCargas['car_id']."' AND niv_cod_estudiante='".$matriculadosDatos['mat_id']."'");
			$nivelacion = mysqli_fetch_array($consultaNivelacion, MYSQLI_BOTH);
			
			// SI PERDIÓ LA MATERIA A FIN DE AÑO
			if($promedioMateria<$config["conf_nota_minima_aprobar"]){
				if($nivelacion['niv_definitiva']>=$config["conf_nota_minima_aprobar"]){
					$promedioMateriaFinal = $nivelacion['niv_definitiva'];
				}else{
					$materiasPerdidas++;
				}	
			}
		
            $ConsultaPromediosMateriaEstiloNota=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
            WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promedioMateriaFinal."'>=notip_desde AND '".$promedioMateriaFinal."'<=notip_hasta");
			$promediosMateriaEstiloNota = mysqli_fetch_array($ConsultaPromediosMateriaEstiloNota, MYSQLI_BOTH);
			
			?>
            <td align="center"><?=$promedioMateriaFinal;?></td>
            <td align="center"><?=$promediosMateriaEstiloNota['notip_nombre'];?></td>
            <td align="center">&nbsp;</td>
        </tr>
    </tbody>
    <?php 
		$contador++;
	}
	?>
    <tfoot>
    	<tr style="font-weight:bold; text-align:center;">
        	<td style="text-align:left;">PROMEDIO/TOTAL</td>
            <td>-</td> 
            <?php 
            $promedioFinal = 0;
            for($j=1;$j<=$config["conf_periodo"];$j++){
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
            <?php 
                $promedioFinal +=$promediosPeriodos['promedio'];
            }

            $promedioFinal = round($promedioFinal/$config["conf_periodo"],2);
            $consultaPromedioFinalEstilioNota=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos 
            WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promedioFinal."'>=notip_desde AND '".$promedioFinal."'<=notip_hasta");
            $promedioFinalEstiloNota = mysqli_fetch_array($consultaPromedioFinalEstilioNota, MYSQLI_BOTH);
            ?>
            <td><?=$promedioFinal;?></td>
            <td><?=$promedioFinalEstiloNota['notip_nombre'];?></td>
            <td>-</td>
        </tr>
    </tfoot>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>	
<p>&nbsp;</p>
<p>&nbsp;</p>	

<table width="100%" cellspacing="5" cellpadding="5" rules="none" border="0">
	<tr>
        <td width="40%">
            ________________________________________________________________<br>
            <?=strtoupper($matriculadosDatos['uss_nombre']);?><br>
            DIRECTOR DE CURSO
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
		
		<?php
		$msjPromocion = '';
		if($periodoActual==$config['conf_periodos_maximos']){
			if($materiasPerdidas==0){$msjPromocion = 'PROMOVIDO';}
			else{$msjPromocion = 'NO PROMOVIDO';}	
		}
		
		?>
        <td width="60%">
        	<p style="font-weight:bold;">Observaciones: <b><?=$msjPromocion;?></b></p>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________
        </td>
    </tr>
</table>

<div id="saltoPagina"></div>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
    <thead>
        <tr style="font-weight:bold; text-align:center;">
            <td width="30%">Asignaturas</td>
            <td width="70%">Contenidos Evaluados</td>
        </tr>     
    </thead>
    
    <?php
	$conCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas
	INNER JOIN academico_materias ON mat_id=car_materia
	INNER JOIN usuarios ON uss_id=car_docente
	INNER JOIN academico_indicadores ON ind_carga=car_id AND ind_periodo='".$_GET['periodo']."' AND ind_tematica=1
	WHERE car_curso='".$matriculadosDatos['mat_grado']."' AND car_grupo='".$matriculadosDatos['mat_grupo']."'");
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
	?>
    <tbody>
        <tr style="color:#585858;">
            <td><?=$datosCargas['mat_nombre'];?><br><span style="color:#C1C1C1;"><?=$datosCargas['uss_nombre'];?></span></td>
            <td><?=$datosCargas['ind_nombre'];?></td> 
        </tr>
    </tbody>
    <?php 
	}
	?>
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