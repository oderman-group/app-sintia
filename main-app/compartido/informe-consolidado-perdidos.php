<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0226';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
?>

<head>
    <title>SINTIA | Defintivas del año</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
</head>

<body style="font-family:Arial;">
<?php
$nombreInforme = "ESTUDIANTES CON ASIGNATURAS PERDIDAS";
include("../compartido/head-informes.php") ?>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" 
  style="
  border:solid; 
  border-color:<?=$Plataforma->colorUno;?>; 
  font-size:11px;
  ">

        <tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">
            <th style="font-size:9px;">Mat</th>
            <th style="font-size:9px;">Estudiante</th>
            <?php
			if(isset($_GET["curso"])){$curso=$_GET["curso"]; $grupo=$_GET["grupo"];}
			if(isset($_POST["grado"])){$curso=$_POST["grado"]; $grupo=$_POST["grupo"];}

			$consultaCurso = Grados::obtenerDatosGrados($curso);
			$datosCurso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);

			$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo);
			//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
			$numCargasPorCurso = mysqli_num_rows($cargas); 
			while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
			?>
            <th style="font-size:9px; text-align:center; border:groove;" width="5%"><?=$carga['mat_nombre'];?></th>
            <?php
			}
			?>
            <th style="text-align:center;">PROM</th>
            <th style="text-align:center;">#MP</th>
        </tr>
        <?php
		$filtroAdicional= "AND mat_grado='".$curso."' AND mat_grupo='".$grupo."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
		$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$datosCurso,$grupo);
		while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
		$nombreCompleto =Estudiantes::NombreCompletoDelEstudiante($resultado);
		$defPorEstudiante = 0;
		$materiasPerdidas = 0;	 
		?>
        <tr style="border-color:<?=$Plataforma->colorDos;?>;">
            <td style="font-size:9px;"><?=$resultado['mat_id'];?></td>
            <td style="font-size:9px;"><?=$nombreCompleto?></td>
            <?php
			$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo);
			$cargasPromdio=0;
			while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
				//PRUEBA CONSULTA PHP 8
				$p = 1;
				$porcPeriodo = array("",0.25,0.25,0.25,0.25);
				$defPorMateria = 0;
				//PERIODOS DE CADA MATERIA
				while($p<=$config[19]){
					$boletin = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $resultado['mat_id'], $carga['car_id']);
					if(!empty($boletin['bol_nota']) && $boletin['bol_nota']<$config[5]){$color = $config[6];} elseif(!empty($boletin['bol_nota']) && $boletin['bol_nota']>=$config[5]) {$color = $config[7];}
					//$defPorMateria += $boletin['bol_nota'];
					if(!empty($boletin['bol_nota'])){
						$defPorMateria += ($boletin['bol_nota']*$porcPeriodo[$p]);
					}
					//DEFINITIVA DE CADA PERIODO
					$p++;
				}
				//$defPorMateria = round($defPorMateria/$config[19],2);
				$defPorMateria = round($defPorMateria,2);
					//DEFINITIVA DE CADA MATERIA
					if($defPorMateria<$config[5] and $defPorMateria!=""){$color = $config[6]; $fondoColor = '#FFC'; $materiasPerdidas++;} elseif($defPorMateria>=$config[5]) {$color = $config[7]; $fondoColor = '#FFF';}
				?>
            <td style="text-align:center; background:<?=$fondoColor;?>; color:<?=$color;?>; text-decoration:underline;">
                <?=$defPorMateria;?></td>
            <?php
				//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
				if ($carga['mat_sumar_promedio'] == SI) {
					$defPorEstudiante += $defPorMateria; 
					$cargasPromdio ++;
				}  
			}
				$defPorEstudiante = round($defPorEstudiante/$cargasPromdio,2);
				
				if($defPorEstudiante<$config[5] and $defPorEstudiante!="")$color = $config[6]; elseif($defPorEstudiante>=$config[5]) $color = $config[7];
			?>
            <td style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>"><?=$defPorEstudiante;?></td>
            <td style="text-align:center; width:40px; font-weight:bold; background-color: gainsboro;">
                <?=$materiasPerdidas;?></td>
        </tr>
        <?php }?>
    </table>
    </center>
	<?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>		
</body>

</html>