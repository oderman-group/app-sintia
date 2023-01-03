<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>SINTIA - INFORME PARCIAL</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/logoodermanp.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">

<?php
								  //ESTUDIANTE ACTUAL
								  $consultaEstudianteActual = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='".$_GET["estudiante"]."'");
								  
								  $datosEstudianteActual = mysqli_fetch_array($consultaEstudianteActual, MYSQLI_BOTH);
								  $nombre = strtoupper($datosEstudianteActual['mat_primer_apellido']." ".$datosEstudianteActual['mat_segundo_apellido']." ".$datosEstudianteActual['mat_nombres']." ".$datosEstudianteActual['mat_nombre2']);
								  ?>
    
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME PARCIAL - PERIODO: <?php echo $config[2];?><br>
    <?php echo $config["conf_fecha_parcial"];?><br>
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="100" width="150"><br>
    <?php echo $config["conf_descripcion_parcial"];?><br>
    ESTUDIANTE: <?=$nombre;?></br>
</div>  



                                  
                                  <!-- BEGIN TABLE DATA -->
                                  <table bgcolor="#FFFFFF" width="100%" cellspacing="2" cellpadding="2" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>; font-size:10px;" align="center">
                                      <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
                                        <th style="text-align:center;">Cod</th>
                                        <th style="text-align:center;">Docente</th>
                                        <th style="text-align:center;">Asignatura</th>
                                        <th style="text-align:center;">%</th>
                                        <th style="text-align:center;">Nota</th>
                                      </tr>
                                    <!-- END -->
                                    <!-- BEGIN -->
                                    <tbody>
                                    <?php
									$cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$datosEstudianteActual[6]."' AND car_grupo='".$datosEstudianteActual[7]."'");
									$nCargas = mysqli_num_rows($cCargas);
									$materiasDividir = 0;
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										$cDatos = mysqli_query($conexion, "SELECT mat_id, mat_nombre, gra_codigo, gra_nombre, uss_id, uss_nombre FROM academico_materias, academico_grados, usuarios WHERE mat_id='".$rCargas[4]."' AND gra_id='".$rCargas[2]."' AND uss_id='".$rCargas[1]."'");
										$rDatos = mysqli_fetch_array($cDatos, MYSQLI_BOTH);
									    //PLAN DE CLASE
                        $cPeriodo=$config[2];
                      if(isset($_GET["periodo"])){
                        $cPeriodo=$_GET["periodo"];
                      }
										$Cpc = mysqli_query($conexion, "SELECT * FROM academico_pclase WHERE pc_id_carga='".$rCargas[0]."' AND pc_periodo='".$cPeriodo."'");
									    $Rpc = mysqli_fetch_array($Cpc, MYSQLI_BOTH);
									    $Npc = mysqli_num_rows($Cpc);
										//DEFINITIVAS
										$carga = $rCargas[0];
										$periodo = $config[2];
										$estudiante = $_GET["estudiante"];
										include("../definitivas.php");
										//SOLO SE CUENTAN LAS MATERIAS QUE TIENEN NOTAS.
										if($porcentajeActual>0){$materiasDividir++;}
									?>
                                    <tr id="data1" class="odd gradeX">
                                        <td style="text-align:center;"><?=$rCargas[0];?></td>
                                        <td><?=$rDatos[5];?></td>
                                        <td><?=$rDatos[1];?></td>
                                        <td style="text-align:center;"><?=$porcentajeActual;?>%</td>
                                        <td style="color:<?=$colorDefinitiva;?>; text-align:center; font-weight:bold;"><?=$definitiva;?></td>
                                      </tr>
                                   <?php 
								   		$promedioG += $definitiva;
								   }
								   		if($nn>0){
											$promedioG = round(($promedioG / $materiasDividir),1);
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
                                  
                                  
                                  <p>&nbsp;</p>
<div style="float:left; margin-left:20px; position:relative; max-width:200px; margin-top:-20px; font-size:12px;" align="center">
_________________________<br>
Coordinador(a) Acad&eacute;mico(a)
</div>

<div style="position:relative; float:right; margin-right:20px; max-width:200px; margin-top:-20px; font-size:12px;" align="center">
_________________________<br>
Director(a) De Grupo
</div>

<div style="position:relative; margin-top:60px; font-size:12px;" align="center">
Yo__________________________________________________________________<br>

Doy constancia de haber recibido del INSTITUTO COLOMBO VENEZOLANO el<br>
informe acad&eacute;mico parcial de mi acudido y a la vez la citaci&oacute;n<br>
respectiva para la reuni&oacute;n en donde se me informar&aacute; las causas y<br>
recomendaciones del bajo demsempe&ntilde;o, establecidas pora la comisi&oacute;n de<br>
evaluaci&oacute;n y promocion.
</div>

<div style="margin-top:10px; position:relative; font-size:12px;" align="center">
_______________________________<br>
Firma Del Padre Y/O Acudiente
</div>

<div align="center" style="margin-top:20px; font-size:12px;">En el Se&ntilde;or, pon tu confianza. Salmos 11:01</div>  
                                  
                                  
                                  <div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
 
                  </body>

                  </html>
