<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>CALIFICACIONES POR MATERIA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/logoodermanp.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logoodermanm.png" height="150" width="250"><br>
    INSTITUTO ODERMAN<br>
    INFORME DE CALIFICACIONES POR MATERIA<br>
    ESTUDIANTE: <?=$_GET["nombre"];?></br>
</div>  

                                  <!-- BEGIN TABLE DATA -->
                                  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
                                      <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
                                        <th>Cod</th>
                                        <th>Descripci&oacute;n</th>
                                        <th>Fecha</th>
                                        <th>Valor</th>
                                        <th>Nota</th>
                                        <th>Observaciones</th>
                                      </tr>
                                     <?php
									 $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_actividades WHERE act_id_carga='".$_GET["carga"]."' AND act_registrada=1 AND act_estado=1 AND act_periodo='".$_GET["periodo"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
									 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                    $consultaNotas=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad='".$resultado['act_id']."' AND cal_id_estudiante='".$_GET["estudiante"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
									 	$nota = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
										$porNuevo = ($resultado['act_valor'] / 100);
										$acumulaValor = ($acumulaValor + $porNuevo);
										$notaMultiplicada = ($nota['cal_nota'] * $porNuevo);
										$sumaNota = ($sumaNota + $notaMultiplicada);
										//COLOR DE CADA NOTA
										if($nota['cal_nota']<$config[5])
											$colorNota = $config[6];
										else
											$colorNota = $config[7];	
									 ?>
                                      <tr>
                                        <td><?=$resultado['act_id'];?></td>
                                        <td><?=$resultado['act_descripcion'];?></td>
                                        <td><?=$resultado['act_fecha'];?></td>
                                        <td><?=$resultado['act_valor'];?></td>
                                        <td style="color:<?=$colorNota;?>"><?=$nota['cal_nota'];?></td>
                                        <td><?=$nota['cal_observaciones'];?></td>
                                      </tr>
                                      <?php 
									  }
										//DEFINITIVAS
										$carga = $_GET["carga"];
										$periodo = $_GET["periodo"];
										$estudiante = $_GET["estudiante"];
										include("../definitivas.php");
									  ?>
                                    <tfoot>
                                        <tr style="font-weight:bold;">
                                            <td colspan="3">TOTALES</td>
                                            <td><?=$porcentajeActual;?>%</td>
                                            <td style="color:<?=$colorDefinitiva;?>"><?=$definitiva;?></td>
                                            <td></td>
                                         </tr>
                                    </tfoot>
                                    <!-- END -->
                                  </table>
                                    <div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
                                 
                  </body>

                  </html>