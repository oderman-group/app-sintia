<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>RESUMEN POR PERIODOS</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/logoodermanp.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logoodermanm.png" height="150" width="250"><br>
    INSTITUTO ODERMAN<br>
    INFORME DE RESUMEN POR PERIODOS<br>
    ESTUDIANTE: <?=$_GET["nombre"];?></br>
</div>  
                                


                                  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
                                      <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
                                        <th style="text-align:center;">Cod</th>
                                        <th style="text-align:center;">Asignatura</th>
                                        <?php
											$p = 1;
											while($p<=$config[19]){
												echo '<th style="text-align:center;">'.$p.'P</th>';
												$p++;
											}
									    ?>
                                        <th style="text-align:center;">DEF</th>
                                      </tr>
                                    <!-- END -->
                                    <!-- BEGIN -->
                                    <tbody>
                                    <?php
									$cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso=5 AND car_grupo=3");
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										$cDatos = mysqli_query($conexion, "SELECT mat_id, mat_nombre, gra_codigo, gra_nombre, uss_id, uss_nombre FROM academico_materias, academico_grados, usuarios WHERE mat_id='".$rCargas[4]."' AND gra_id='".$rCargas[2]."' AND uss_id='".$rCargas[1]."'");
										$rDatos = mysqli_fetch_array($cDatos, MYSQLI_BOTH);
									?>
                                    <tr id="data1" class="odd gradeX">
                                        <td style="text-align:center;"><?=$rCargas[0];?></td>
                                        <td><?=$rDatos[1];?></td>
                                        
										<?php
									 	 $definitiva = 0;
										 $n = 0;
										 for($i=1; $i<=$config[19]; $i++){
										 	//LAS CALIFICACIONES
										 	$notasConsulta = mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$_GET["estudiante"]." AND bol_carga=".$rCargas[0]." AND bol_periodo=".$i);
										 	$notasResultado = mysqli_fetch_array($notasConsulta, MYSQLI_BOTH);
											$numN = mysqli_num_rows($notasConsulta);
											if($numN){
												$n++;
												$definitiva += $notasResultado[4];
											}
											if($notasResultado[4]<$config[5] and $notasResultado[4]!="")$color = $config[6]; elseif($notasResultado[4]>=$config[5]) $color = $config[7];
											if($notasResultado[5]==2) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n</span>'; elseif($notasResultado[5]==1) $tipo = '<span style="color:blue; font-size:9px;">Normal</span>'; else $tipo='';
											
										?>
                                        	<td style="text-align:center; color:<?=$color;?>;">
												<?=$notasResultado[4]."<br>".$tipo;?>
                                            </td>
                                        <?php		
										 }
											$consultaN = mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_cod_estudiante=".$_GET["estudiante"]." AND niv_id_asg=".$rCargas[0]);
											
											$numN = mysqli_num_rows($consultaN);
											$rN = mysqli_fetch_array($consultaN, MYSQLI_BOTH);
											if($numN==0){
										 		if($n>0)
													$definitiva = round(($definitiva/$n), 1);
												$tN = '<span style="color:blue; font-size:9px;">Normal</span>';
											}else{
												$definitiva = $rN[3];
												$tN = '<span style="color:red; font-size:9px;">Nivelada</span>';
											}
										 if($definitiva<$config[5])$color = $config[6]; elseif($definitiva>=$config[5]) $color = $config[7];
									 	?>
                                        <td style="text-align:center; color:<?=$color;?>;">
											<?=$definitiva."<br>".$tN;?>
                                        </td>
                                      </tr>
                                   <?php }?>   
                                    </tbody>
                                    <!-- END -->
                                  </table>
                                  <div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>


                  </body>

                  </html>
