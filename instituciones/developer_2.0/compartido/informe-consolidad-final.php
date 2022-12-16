<?php
session_start();
include("../../../config-general/config.php");
include("../../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>SINTIA | Consolidado Final</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    CONSOLIDADO FINAL</br>
</div>   
  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
                                        <th rowspan="2" style="font-size:9px;">Mat</th>
                                        <th rowspan="2" style="font-size:9px;">Estudiante</th>
                                        <?php
										$cargas = mysql_query("SELECT * FROM academico_cargas WHERE car_curso='".$_GET["curso"]."' AND car_grupo='".$_GET["grupo"]."' AND car_activa=1",$conexion);
										//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
										$numCargasPorCurso = mysql_num_rows($cargas); 
										while($carga = mysql_fetch_array($cargas)){
											$materia = mysql_fetch_array(mysql_query("SELECT * FROM academico_materias WHERE mat_id='".$carga[4]."'",$conexion));
										?>
                                        	<th style="font-size:9px; text-align:center; border:groove;" colspan="<?=$config[19]+1;?>" width="5%"><?=$materia[2];?></th>
                                        <?php
										}
										?>
                                        <th rowspan="2" style="text-align:center;">PROM</th>
                                        </tr>
                                        
                                        <tr>
                                            <?php
                                            $cargas = mysql_query("SELECT * FROM academico_cargas WHERE car_curso='".$_GET["curso"]."' AND car_grupo='".$_GET["grupo"]."' AND car_activa=1",$conexion); 
                                            while($carga = mysql_fetch_array($cargas)){
                                                $p = 1;
                                                //PERIODOS DE CADA MATERIA
												while($p<=$config[19]){
                                                    echo '<th style="text-align:center;">'.$p.'</th>';
                                                    $p++;
                                                }
												//DEFINITIVA DE CADA MATERIA
												echo '<th style="text-align:center; background:#FFC">DEF</th>';
                                            }
                                            ?>
                                        </tr>
<?php
									 $consulta = mysql_query("SELECT * FROM academico_matriculas WHERE mat_grado='".$_GET["curso"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido",$conexion);
									 while($resultado = mysql_fetch_array($consulta)){
									 $defPorEstudiante = 0;
									 ?>
  <tr style="font-size:13px;">
     <td style="font-size:9px;"><?=$resultado[1];?></td>
                                        <td style="font-size:9px;"><?=$resultado[3]." ".$resultado[4]." ".$resultado[5];?></td>
                                        <?php
										$cargas = mysql_query("SELECT * FROM academico_cargas WHERE car_curso='".$_GET["curso"]."' AND car_grupo='".$_GET["grupo"]."' AND car_activa=1",$conexion); 
										while($carga = mysql_fetch_array($cargas)){
											$materia = mysql_fetch_array(mysql_query("SELECT * FROM academico_materias WHERE mat_id='".$carga[4]."'",$conexion));
											$p = 1;
                                            $porcPeriodo = array("",0.25,0.15,0.35,0.25);
											$defPorMateria = 0;
											//PERIODOS DE CADA MATERIA
											while($p<=$config[19]){
												$boletin = mysql_fetch_array(mysql_query("SELECT * FROM academico_boletin WHERE bol_carga='".$carga[0]."' AND bol_estudiante='".$resultado[0]."' AND bol_periodo='".$p."'",$conexion));
												if($boletin[4]<$config[5] and $boletin[4]!="")$color = $config[6]; elseif($boletin[4]>=$config[5]) $color = $config[7];
												//$defPorMateria += $boletin[4];
												$defPorMateria += ($boletin[4]*$porcPeriodo[$p]);
												//DEFINITIVA DE CADA PERIODO
											?>	
												<td style="text-align:center; color:<?=$color;?>"><?=$boletin[4];?></td>
                                            <?php
												$p++;
                                            }
											//$defPorMateria = round($defPorMateria/$config[19],2);
											$defPorMateria = round($defPorMateria,2);
												//DEFINITIVA DE CADA MATERIA
												if($defPorMateria<$config[5] and $defPorMateria!="")$color = $config[6]; elseif($defPorMateria>=$config[5]) $color = $config[7];
											?>
                                            	<td style="text-align:center; background:#FFC; color:<?=$color;?>; text-decoration:underline;"><?=$defPorMateria;?></td>
                                        <?php
											//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
											$defPorEstudiante += $defPorMateria;   
										}
											$defPorEstudiante = round($defPorEstudiante/$numCargasPorCurso,2);
											
											if($defPorEstudiante<$config[5] and $defPorEstudiante!="")$color = $config[6]; elseif($defPorEstudiante>=$config[5]) $color = $config[7];
										?>
                                        	<td style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>"><?=$defPorEstudiante;?></td>
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