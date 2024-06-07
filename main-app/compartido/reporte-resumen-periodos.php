<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");?>
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
									$cCargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas WHERE car_curso=5 AND car_grupo=3 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										$rDatos = Asignaturas::consultarAsignaturaCursoUsuario($conexion, $config, $rCargas['car_curso'], $rCargas['car_materia'], $rCargas['car_docente']);
									?>
                                    <tr id="data1" class="odd gradeX">
                                        <td style="text-align:center;"><?=$rCargas['car_id'];?></td>
                                        <td><?=$rDatos[1];?></td>
                                        
										<?php
									 	 $definitiva = 0;
										 $n = 0;
										 for($i=1; $i<=$config[19]; $i++){
										 	//LAS CALIFICACIONES
											$notasResultado = Boletin::traerNotaBoletinCargaPeriodo($config, $i, $_GET["estudiante"], $rCargas['car_id']);
											if(!empty($notasResultado)){
												$n++;
												$definitiva += $notasResultado['bol_nota'];
											}
											if($notasResultado['bol_nota']<$config[5] and $notasResultado['bol_nota']!="")$color = $config[6]; elseif($notasResultado['bol_nota']>=$config[5]) $color = $config[7];
											if($notasResultado['bol_tipo']==2) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n</span>'; elseif($notasResultado['bol_tipo']==1) $tipo = '<span style="color:blue; font-size:9px;">Normal</span>'; else $tipo='';
											
										?>
                                        	<td style="text-align:center; color:<?=$color;?>;">
												<?=$notasResultado['bol_nota']."<br>".$tipo;?>
                                            </td>
                                        <?php		
										 }
											$consultaN = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $_GET["estudiante"], $rCargas['car_id']);
											$numN = mysqli_num_rows($consultaN);
											$rN = mysqli_fetch_array($consultaN, MYSQLI_BOTH);
											if($numN==0){
										 		if($n>0)
													$definitiva = round(($definitiva/$n), 1);
												$tN = '<span style="color:blue; font-size:9px;">Normal</span>';
											}else{
												$definitiva = $rN['niv_definitiva'];
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
