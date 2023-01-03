<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
?>
<head>
	<title>SINTIA | Defintivas del año</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <!--<img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>-->
    <?=$informacion_inst["info_nombre"]?><br>
    ESTUDIANTES CON ASIGNATURAS PERDIDAS</br>
</div>   
  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
                                        <th style="font-size:9px;">Mat</th>
                                        <th style="font-size:9px;">Estudiante</th>
                                        <?php
										$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$_GET["curso"]."' AND car_grupo='".$_GET["grupo"]."' AND car_activa=1");
										//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
										$numCargasPorCurso = mysqli_num_rows($cargas); 
										while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
											$consultaMaterias=mysqli_query($conexion, "SELECT * FROM academico_materias WHERE mat_id='".$carga[4]."'");
											$materia = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH);
										?>
                                        	<th style="font-size:9px; text-align:center; border:groove;" width="5%"><?=$materia[2];?></th>
                                        <?php
										}
										?>
                                        <th style="text-align:center;">PROM</th>
	  									<th style="text-align:center;">#MP</th>
                                        </tr>
									<?php
									 $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_GET["curso"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
									 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
									 $defPorEstudiante = 0;
									 $materiasPerdidas = 0;	 
									 ?>
									  <tr style="font-size:13px;">
										<td style="font-size:9px;"><?=$resultado[1];?></td>
                                        <td style="font-size:9px;"><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
                                        <?php
										$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$_GET["curso"]."' AND car_grupo='".$_GET["grupo"]."' AND car_activa=1"); 
										while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
											//PRUEBA CONSULTA PHP 8
											$consultaMaterias= mysqli_query($conexion, "SELECT * FROM academico_materias WHERE mat_id='".$carga[4]."'");
											$materia = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH);
											$p = 1;
                                            $porcPeriodo = array("",0.25,0.25,0.25,0.25);
											$defPorMateria = 0;
											//PERIODOS DE CADA MATERIA
											while($p<=$config[19]){
												$consultaBoletin=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_carga='".$carga[0]."' AND bol_estudiante='".$resultado[0]."' AND bol_periodo='".$p."'");
												$boletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH);
												if($boletin[4]<$config[5] and $boletin[4]!="")$color = $config[6]; elseif($boletin[4]>=$config[5]) $color = $config[7];
												//$defPorMateria += $boletin[4];
												$defPorMateria += ($boletin[4]*$porcPeriodo[$p]);
												//DEFINITIVA DE CADA PERIODO
												$p++;
                                            }
											//$defPorMateria = round($defPorMateria/$config[19],2);
											$defPorMateria = round($defPorMateria,2);
												//DEFINITIVA DE CADA MATERIA
												if($defPorMateria<$config[5] and $defPorMateria!=""){$color = $config[6]; $fondoColor = '#FFC'; $materiasPerdidas++;} elseif($defPorMateria>=$config[5]) {$color = $config[7]; $fondoColor = '#FFF';}
											?>
                                            	<td style="text-align:center; background:<?=$fondoColor;?>; color:<?=$color;?>; text-decoration:underline;"><?=$defPorMateria;?></td>
                                        <?php
											//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
											$defPorEstudiante += $defPorMateria;   
										}
											$defPorEstudiante = round($defPorEstudiante/$numCargasPorCurso,2);
											
											if($defPorEstudiante<$config[5] and $defPorEstudiante!="")$color = $config[6]; elseif($defPorEstudiante>=$config[5]) $color = $config[7];
										?>
                                        	<td style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>"><?=$defPorEstudiante;?></td>
										  	<td style="text-align:center; width:40px; font-weight:bold; background-color: gainsboro;"><?=$materiasPerdidas;?></td>
                                      </tr>
                                      <?php }?>
  </table>
  </center>
	<div align="center" style="font-size:10px; margin-top:10px;">
      <img src="../files/images/sintia.png" height="50" width="100"><br>
      SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
     </div>
</body>
</html>