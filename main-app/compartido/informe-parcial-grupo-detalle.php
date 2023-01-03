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
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME PARCIAL - PERIODO: <?php echo $config[2];?><br>
    <?php echo $config["conf_fecha_parcial"];?><br>
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="100" width="150"><br>
    <?php echo $config["conf_descripcion_parcial"];?><br>
</div> 

<?php
$matriculadosPorCurso = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_REQUEST["curso"]."' AND mat_grupo='".$_REQUEST["grupo"]."' AND mat_eliminado=0 AND (mat_estado_matricula=1) ORDER BY mat_primer_apellido");
?>

<?php
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
	$nombre = strtoupper($matriculadosDatos[3]." ".$matriculadosDatos[4]." ".$matriculadosDatos[5]);	  
?>
<div align="center" style="margin-bottom:20px;">
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
									$cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas 
									INNER JOIN academico_materias ON mat_id=car_materia
									INNER JOIN academico_grados ON gra_id=car_curso
									INNER JOIN usuarios ON uss_id=car_docente
									WHERE car_curso='".$matriculadosDatos[6]."' AND car_grupo='".$matriculadosDatos[7]."'");
									$nCargas = mysqli_num_rows($cCargas);
									$materiasDividir = 0;
									$promedioG = 0;
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										//DEFINITIVAS
										$carga = $rCargas[0];
										$periodo = $config[2];
										$estudiante = $matriculadosDatos[0];
										include("../definitivas.php");
										if($definitiva>=$config[5] or $porcentajeActual==0) continue;
										//SOLO SE CUENTAN LAS MATERIAS QUE TIENEN NOTAS.
										if($porcentajeActual>0){$materiasDividir++;}
									?>
                                    <tr id="data1" class="odd gradeX">
                                        <td style="text-align:center;"><?=$rCargas[0];?></td>
                                        <td><?=$rCargas['uss_nombre'];?></td>
                                        <td><?=$rCargas['mat_nombre'];?></td>
                                        <td style="text-align:center;"><?=$porcentajeActual;?>%</td>
                                        <td style="color:<?=$colorDefinitiva;?>; text-align:center; font-weight:bold;"><?=$definitiva;?></td>
                                      </tr>
                                   <?php 
								   		$promedioG += $definitiva;
								   }
								   		if($nn>0 and $materiasDividir>0){
											$promedioG = round(($promedioG / $materiasDividir),1);
										}	
								   ?>   
                                    </tbody>
                                    <!-- END --
                                     <tfoot>
                                      <tr style="font-weight:bold;">
                                        <td colspan="4" style="text-align:right;">PROMEDIO GENERAL</td>
                                        <td style="text-align:center;"><?php echo $promedioG;?></td>
                                      </tr>
                                    </tfoot>
                                    -->
                                  </table>
                                  
    
<p>&nbsp;</p>                              
<!--                                  
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
 -->                                 
                                  
                                  
                                    
 <?php }?>
 <div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
</body>

</html>
