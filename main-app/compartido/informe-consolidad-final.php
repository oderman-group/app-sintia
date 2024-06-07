<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0230';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$year = $_SESSION["bd"];
if(!empty($_REQUEST["agno"])){
	$year = $_REQUEST["agno"];
}

$cursoV = '';
$grupoV = '';
if (!empty($_GET["curso"])) {
	$cursoV = base64_decode($_GET['curso']);
	$grupoV = base64_decode($_GET['grupo']);
}elseif(!empty($_POST["curso"])) {
	$cursoV = $_POST['curso'];
	$grupoV = $_POST['grupo'];
}

  $consultaCurso = Grados::obtenerDatosGrados($cursoV);
	$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
  
  $consultaGrupo = Grupos::obtenerDatosGrupos($grupoV);
	$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);
  ?>
<head>
	<title>SINTIA | Consolidado Final</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
</head>
<body style="font-family:Arial;">
<?php
$nombreInforme = "CONSOLIDADO FINAL " .$year."<br>" . "CURSO: " .Utilidades::getToString($curso['gra_nombre']). "<br>" . "GRUPO: ".Utilidades::getToString($grupo['gru_nombre']);
include("../compartido/head-informes.php") ?>


<table width="100%" cellspacing="5" cellpadding="5" rules="all" 
  style="
  border:solid; 
  border-color:<?=$Plataforma->colorUno;?>; 
  font-size:11px;
  ">

<tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">

                                        <th rowspan="2" style="font-size:9px;">Mat</th>
                                        <th rowspan="2" style="font-size:9px;">Estudiante</th>
                                        <?php
										$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $cursoV, $grupoV, $year);
										//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
										$numCargasPorCurso = mysqli_num_rows($cargas); 
										while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
										?>
                                        	<th style="font-size:9px; text-align:center; border:groove;" colspan="<?=$config[19]+1;?>" width="5%"><?php if(!empty($carga['mat_nombre'])){echo $carga['mat_nombre'];}?></th>
                                        <?php
										}
										?>
                                        <th rowspan="2" style="text-align:center;">PROM</th>
                                        </tr>
                                        
                                        <tr>
                                            <?php
											$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $cursoV, $grupoV, $year);
                                            while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
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
									 $filtroAdicional = "";
									 if(!empty($_REQUEST["curso"]) and !empty($_REQUEST["grupo"])){
									 	$filtroAdicional= "AND mat_grado='".$cursoV."' AND mat_grupo='".$grupoV."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
									 }
									 $cursoActual=GradoServicios::consultarCurso($cursoV);
									 $consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual,"",$year);
									 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
									 $defPorEstudiante = 0;
									 ?>
									<tr style="border-color:<?=$Plataforma->colorDos;?>;">
										<td style="font-size:9px;"><?=$resultado['mat_matricula'];?></td>
                                        <td style="font-size:9px;"><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
                                        <?php
										$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $cursoV, $grupoV, $year);
										while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
											$p = 1;
                                            $porcPeriodo = array("",0.25,0.15,0.35,0.25);
											$defPorMateria = 0;
											//PERIODOS DE CADA MATERIA
											while($p<=$config[19]){
												$boletin = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $resultado['mat_id'], $carga['car_id'], $year);
												if(!empty($boletin['bol_nota']) and $boletin['bol_nota']<$config[5] and $boletin['bol_nota']!="")$color = $config[6]; elseif(!empty($boletin['bol_nota']) and $boletin['bol_nota']>=$config[5]) $color = $config[7];
												
												$notaBoletinFinal="";
												$title='';
												if(!empty($boletin['bol_nota'])){
													$notaBoletinFinal=$boletin['bol_nota'];
													if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
														$title='title="Nota Cuantitativa: '.$boletin['bol_nota'].'"';
														$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $boletin['bol_nota'],$year);
														$notaBoletinFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
													}

													$defPorMateria += ($boletin['bol_nota']*$porcPeriodo[$p]);
												}
												//DEFINITIVA DE CADA PERIODO
											?>	
												<td style="text-align:center; color:<?=$color;?>" <?=$title;?>><?=$notaBoletinFinal?></td>
                                            <?php
												$p++;
                                            }
											//$defPorMateria = round($defPorMateria/$config[19],2);
											$defPorMateria = round($defPorMateria,2);
												//DEFINITIVA DE CADA MATERIA
												if($defPorMateria<$config[5] and $defPorMateria!="")$color = $config[6]; elseif($defPorMateria>=$config[5]) $color = $config[7];
												$defPorMateriaFinal=$defPorMateria;
												$title='';
												if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
													$title='title="Nota Cuantitativa: '.$defPorMateria.'"';
													$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $defPorMateria,$year);
													$defPorMateriaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
												}
											?>
                                            	<td style="text-align:center; background:#FFC; color:<?=$color;?>; text-decoration:underline;" <?=$title;?>><?=$defPorMateriaFinal;?></td>
                                        <?php
											//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
											$defPorEstudiante += $defPorMateria;   
										}
										if($numCargasPorCurso > 0){
											$defPorEstudiante = round($defPorEstudiante/$numCargasPorCurso,2);
										}	
										
											
											if($defPorEstudiante<$config[5] and $defPorEstudiante!="")$color = $config[6]; elseif($defPorEstudiante>=$config[5]) $color = $config[7];
										?>
                                        	<td style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>"><?=$defPorEstudiante;?></td>
                                      </tr>
                                      <?php }?>
  </table>
  <?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>
</body>
</html>