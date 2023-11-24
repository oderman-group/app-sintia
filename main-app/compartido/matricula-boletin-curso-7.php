<?php 
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once("../class/Estudiantes.php");
require_once("../class/UsuariosPadre.php");

$year=$_SESSION["bd"];
if(!empty($_GET["year"])){
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

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro,$year);
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
$numMatriculados = mysqli_num_rows($puestos);
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
    <div style="float:left; width:50%"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="80"></div>
    <div style="float:right; width:50%">
        <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
            <tr>
                <td>C&oacute;digo:<br> <?=number_format($datosUsr["mat_documento"],0,",",".");?></td>
                <td>Nombre:<br> <?=$nombre?></td>
                <td>Grado:<br> <?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></td>
                <td>Puesto Curso:<br> <?=$puestoCurso." de ".$numMatriculados;?></td>   
            </tr>
            
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?=$informacion_inst["info_nombre"]?></td>
                <td>Periodo:<br> <b><?=$periodoActual." (".$year.")";?></b></td>
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
            
            <?php  for($j=1;$j<=$periodoActual;$j++){ ?>
                <td width="3%" colspan="3"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$datosUsr['mat_id'];?>&periodo=<?=$j?>" style="color:#000; text-decoration:none;">Periodo <?=$j?></a></td>
            <?php }?>
            <td width="3%" colspan="3">Final</td>
        </tr> 
        
        <tr style="font-weight:bold; text-align:center;">
            <?php  for($j=1;$j<=$periodoActual;$j++){ ?>
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
	$conCargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
	INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
	WHERE car_curso='".$datosUsr['mat_grado']."' AND car_grupo='".$datosUsr['mat_grupo']."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}");
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
		if($contador%2==1){$fondoFila = '#EAEAEA';}else{$fondoFila = '#FFF';}
	?>
    <tbody>
        <tr style="background:<?=$fondoFila;?>">
            <td><?=$datosCargas['mat_nombre'];?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td> 
            <?php 
			$promedioMateria = 0;
			for($j=1;$j<=$periodoActual;$j++){
				
                $consultaDatosBoletin=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_boletin bol 
                INNER JOIN ".BD_ACADEMICA.".academico_notas_tipos ntp ON ntp.notip_categoria='".$config["conf_notas_categoria"]."' AND bol_nota>=ntp.notip_desde AND bol_nota<=ntp.notip_hasta AND ntp.institucion={$config['conf_id_institucion']} AND ntp.year={$year}
                WHERE bol_carga='".$datosCargas['car_id']."' AND bol_estudiante='".$datosUsr['mat_id']."' AND bol_periodo='".$j."' AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}");
                $datosBoletin = mysqli_fetch_array($consultaDatosBoletin, MYSQLI_BOTH);
				
                $consultaDatosAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM ".BD_ACADEMICA.".academico_clases cls 
                INNER JOIN ".BD_ACADEMICA.".academico_ausencias aus ON aus.aus_id_clase=cls.cls_id AND aus.aus_id_estudiante='".$datosUsr['mat_id']."' AND aus.institucion={$config['conf_id_institucion']} AND aus.year={$year}
                WHERE cls.cls_id_carga='".$datosCargas['car_id']."' AND cls.cls_periodo='".$j."' AND cls.institucion={$config['conf_id_institucion']} AND cls.year={$year}");
				$datosAusencias = mysqli_fetch_array($consultaDatosAusencias, MYSQLI_BOTH);
				
				$promedioMateria +=$datosBoletin['bol_nota'];
            ?>
                <td align="center"><?php 
                if ($datosAusencias[0]>0) {
                    echo round($datosAusencias[0],0);
                } 
                ?></td>
                <td align="center"><?=$datosBoletin['bol_nota'];?></td>
                <td align="center"><?=$datosBoletin['notip_nombre'];?></td>
            <?php 
			}
			$promedioMateria = round($promedioMateria/($j-1),2);
			$promedioMateriaFinal = $promedioMateria;
            $consultaNivelacion=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_nivelaciones WHERE niv_id_asg='".$datosCargas['car_id']."' AND niv_cod_estudiante='".$datosUsr['mat_id']."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
			$nivelacion = mysqli_fetch_array($consultaNivelacion, MYSQLI_BOTH);
			
			// SI PERDIÓ LA MATERIA A FIN DE AÑO
			if($promedioMateria<$config["conf_nota_minima_aprobar"]){
				if($nivelacion['niv_definitiva']>=$config["conf_nota_minima_aprobar"]){
					$promedioMateriaFinal = $nivelacion['niv_definitiva'];
				}else{
					$materiasPerdidas++;
				}	
			}
		
            $ConsultaPromediosMateriaEstiloNota=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos 
            WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promedioMateriaFinal."'>=notip_desde AND '".$promedioMateriaFinal."'<=notip_hasta AND institucion={$config['conf_id_institucion']} AND year={$year}");
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
            for($j=1;$j<=$periodoActual;$j++){
                $consultaPromedioPeriodos=mysqli_query($conexion, "SELECT ROUND(AVG(bol_nota),2) as promedio FROM ".BD_ACADEMICA.".academico_boletin 
                WHERE bol_estudiante='".$datosUsr['mat_id']."' AND bol_periodo='".$j."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
				$promediosPeriodos = mysqli_fetch_array($consultaPromedioPeriodos, MYSQLI_BOTH);
				
                $consultaSumaAusencias=mysqli_query($conexion, "SELECT sum(aus_ausencias) FROM ".BD_ACADEMICA.".academico_clases cls 
                INNER JOIN ".BD_ACADEMICA.".academico_ausencias aus ON aus.aus_id_clase=cls.cls_id AND aus.aus_id_estudiante='".$datosUsr['mat_id']."' AND aus.institucion={$config['conf_id_institucion']} AND aus.year={$year}
                WHERE cls.cls_periodo='".$j."' AND cls.institucion={$config['conf_id_institucion']} AND cls.year={$year}");
				$sumaAusencias = mysqli_fetch_array($consultaSumaAusencias, MYSQLI_BOTH);
				
                $consultaPromedioEstiloNota=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos 
				WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promediosPeriodos['promedio']."'>=notip_desde AND '".$promediosPeriodos['promedio']."'<=notip_hasta AND institucion={$config['conf_id_institucion']} AND year={$year}");
				$promediosEstiloNota = mysqli_fetch_array($consultaPromedioEstiloNota, MYSQLI_BOTH);
            ?>
                <td><?php //echo $sumaAusencias[0];?></td>
                <td><?=$promediosPeriodos['promedio'];?></td>
                <td><?=$promediosEstiloNota['notip_nombre'];?></td>
            <?php 
                $promedioFinal +=$promediosPeriodos['promedio'];
            }

            $promedioFinal = round($promedioFinal/$periodoActual,2);
            $consultaPromedioFinalEstilioNota=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos 
            WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND '".$promedioFinal."'>=notip_desde AND '".$promedioFinal."'<=notip_hasta AND institucion={$config['conf_id_institucion']} AND year={$year}");
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
            <?php if(!empty($datosUsr['uss_nombre'])) echo strtoupper($datosUsr['uss_nombre']);?><br>
            DIRECTOR DE CURSO
        </td>
        <td width="20%">
        	<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            	<?php
				$contador=1;
				$estilosNota = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos 
				WHERE notip_categoria='".$config["conf_notas_categoria"]."' AND institucion={$config['conf_id_institucion']} AND year={$year}
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
	$conCargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
	INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
	INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$year}
	INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_carga=car_id AND ai.ind_periodo='".$periodoActual."' AND ai.ind_tematica=1 AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$year}
	WHERE car_curso='".$datosUsr['mat_grado']."' AND car_grupo='".$datosUsr['mat_grupo']."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}");
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
	?>
    <tbody>
        <tr style="color:#585858;">
            <td><?=$datosCargas['mat_nombre'];?><br>
            <span style="color:#C1C1C1;"><?=UsuariosPadre::nombreCompletoDelUsuario($datosCargas);?></span></td>
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