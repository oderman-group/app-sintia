<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
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
								  $consultaEstudianteActual = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='".$_GET["estudiante"]."'");
								  
								  $datosEstudianteActual = mysqli_fetch_array($consultaEstudianteActual, MYSQLI_BOTH);
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
									$cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$datosEstudianteActual[6]."' AND car_grupo='".$datosEstudianteActual[7]."'");
									$nCargas = mysqli_num_rows($cCargas);
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										$cDatos = mysqli_query($conexion, "SELECT mat_id, mat_nombre, gra_codigo, gra_nombre, uss_id, uss_nombre FROM academico_materias, academico_grados, usuarios WHERE mat_id='".$rCargas[4]."' AND gra_id='".$rCargas[2]."' AND uss_id='".$rCargas[1]."'");
										$rDatos = mysqli_fetch_array($cDatos, MYSQLI_BOTH);
									    //PLAN DE CLASE
										$Cpc = mysqli_query($conexion, "SELECT * FROM academico_pclase WHERE pc_id_carga='".$rCargas[0]."' AND pc_periodo='".$_GET["periodo"]."'");
									    $Rpc = mysqli_fetch_array($Cpc, MYSQLI_BOTH);
									    $Npc = mysqli_num_rows($Cpc);
										//DEFINITIVAS
										$carga = $rCargas[0];
										$periodo = $_GET["periodo"];
										$estudiante = $_GET["estudiante"];
										include("../definitivas.php");
									?>
                                    <tr id="data1" class="odd gradeX">
                                        <td style="text-align:center;"><?=$rCargas[0];?></td>
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
