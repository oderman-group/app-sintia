<?php
include("../directivo/session.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
?>
<head>
	<title>TODAS LAS CALIFICACIONES</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/logoodermanp.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="100" width="200"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME DE TODAS LAS CALIFICACIONES<br>
    ESTUDIANTE: <?=$_GET["nombre"];?></br>
</div>  



                                  <?php
								  //ESTUDIANTE ACTUAL
                  $datosEstudianteActual =Estudiantes::obtenerDatosEstudiante($_GET["estudiante"]);
								  ?>
                                  <!-- BEGIN TABLE DATA -->
                                  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
                                      <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
                                        <th style="text-align:center;">Cod</th>
                                        <th style="text-align:center;">Docente</th>
                                        <th style="text-align:center;">Asignatura</th>
                                        <th style="text-align:center;">Periodo</th>
                                        <th style="text-align:center;">Nota</th>
                                      </tr>
                                    <!-- END -->
                                    <!-- BEGIN -->
                                    <tbody>
                                    <?php
                  $cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo']);
									$nCargas = mysqli_num_rows($cCargas);
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
                    $rDatos = Asignaturas::consultarAsignaturaCursoUsuario($conexion, $config, $rCargas['car_curso'], $rCargas['car_materia'], $rCargas['car_docente']);
										//DEFINITIVAS
										$carga = $rCargas['car_id'];
										$periodo = $_GET["periodo"];
										$estudiante = $_GET["estudiante"];
										include("../definitivas.php");
									?>
                                    <tr id="data1" class="odd gradeX">
                                        <td style="text-align:center;"><?=$rCargas['car_id'];?></td>
                                        <td><?=$rDatos[5];?></td>
                                        <td><?=$rDatos[1];?></td>
                                        <td style="text-align:center;"><?=$_GET["periodo"];?></td>
                                        <td style="color:<?=$colorDefinitiva;?>; text-align:center; font-weight:bold;"><?=$definitiva;?></td>
                                      </tr>
                                   <?php 
								   		$promedioG += $definitiva;
								   }
								   		if($nn>0){
											$promedioG = round(($promedioG / $nCargas),1);
										}	
								   ?>   
                                    </tbody>
                                    <!-- END -->
                                     <tfoot>
                                      <tr style="font-weight:bold;">
                                        <td colspan="4" style="text-align:right;">PROMEDIO GENERAL</td>
                                        <td style="text-align:center;"><?php echo $promedioG;?></td>
                                      </tr>
                                    </tfoot>
                                  </table>
                                  <div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
 
                  </body>

                  </html>
