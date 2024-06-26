<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0248';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
$estudiante="";
if(!empty($_GET["estudiante"])){ $estudiante=base64_decode($_GET["estudiante"]);}
if(!empty($_POST["estudiante"])){ $estudiante=$_POST["estudiante"];}
$year=date("Y");
$cPeriodo=$config[2];
if(isset($_GET["periodo"])){
  $cPeriodo=$_GET["periodo"];
}
if(isset($_POST["periodo"])){
  $cPeriodo=$_POST["periodo"];
}
?>
<head>
	<title>SINTIA - INFORME PARCIAL</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../sintia-icono.png" />
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">

<?php
								  //ESTUDIANTE ACTUAL
								  
								  $datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($estudiante);
								  $nombre = Estudiantes::NombreCompletoDelEstudiante($datosEstudianteActual);
								  ?>
    
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME PARCIAL - PERIODO: <?php echo $cPeriodo;?><br>
    <?php echo $config["conf_fecha_parcial"];?><br>
    <?php 
      $tamano='height="100" width="150"';
      if($config['conf_id_institucion'] == ICOLVEN){
        $tamano='width="100%"';
      }
    ?>
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" <?=$tamano?>><br>
    <?php echo $config["conf_descripcion_parcial"];?><br>
    ESTUDIANTE: <?=$nombre;?></br>
</div>  



                                  
                                  <!-- BEGIN TABLE DATA -->
                                    <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="border:solid; border-color:#6017dc; font-size:11px;">
                                      <tr style="font-weight:bold; height:30px; background:#6017dc; color:#FFF;">
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
									$cCargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas WHERE car_curso='".$datosEstudianteActual['mat_grado']."' AND car_grupo='".$datosEstudianteActual['mat_grupo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
									$nCargas = mysqli_num_rows($cCargas);
									$materiasDividir = 0;
									$promedioG = 0;
									while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
										$cDatos = mysqli_query($conexion, "SELECT mat_id, mat_nombre, mat_sumar_promedio, gra_codigo, gra_nombre, uss_id, uss_nombre FROM ".BD_ACADEMICA.".academico_materias am, ".BD_ACADEMICA.".academico_grados gra, ".BD_GENERAL.".usuarios uss WHERE am.mat_id='".$rCargas['car_materia']."' AND gra_id='".$rCargas['car_curso']."' AND uss_id='".$rCargas['car_docente']."' AND am.institucion={$config['conf_id_institucion']} AND am.year={$_SESSION["bd"]} AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]} AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}");
										$rDatos = mysqli_fetch_array($cDatos, MYSQLI_BOTH);
										//DEFINITIVAS
										$carga = $rCargas['car_id'];
										$estudiante = $estudiante;
										$periodo = $cPeriodo;
										include("../definitivas.php");
										//SOLO SE CUENTAN LAS MATERIAS QUE TIENEN NOTAS.
										if($porcentajeActual>0 && $rDatos['mat_sumar_promedio'] == SI){$materiasDividir++;}

                    $definitivaFinal=$definitiva;
                    if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                      $estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
                      $definitivaFinal= !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
                    }
									?>
                                    <tr id="data1" class="odd gradeX">
                                        <td style="text-align:center;"><?=$rCargas['car_id'];?></td>
                                        <td><?=UsuariosPadre::nombreCompletoDelUsuario($rDatos);?></td>
                                        <td><?=$rDatos['mat_nombre'];?></td>
                                        <td style="text-align:center;"><?=$porcentajeActual;?>%</td>
                                        <td style="color:<?=$colorDefinitiva;?>; text-align:center; font-weight:bold;"><?=$definitivaFinal;?></td>
                                      </tr>
                                   <?php 
                      if($rDatos['mat_sumar_promedio'] == SI){
                        $promedioG += $definitiva;
                      }	
								   }
								   		if($materiasDividir>0){
											$promedioG = round(($promedioG / $materiasDividir),1);
										}	
                    //MEDIA TECNICA
                    if (array_key_exists(10, $_SESSION["modulos"])){
                      $consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config,$year,$estudiante);
                      while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
                        if(!empty($datosEstudianteActualMT)){
                          $cCargas = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas WHERE car_curso='".$datosEstudianteActualMT['matcur_id_curso']."' AND car_grupo='".$datosEstudianteActualMT['matcur_id_grupo']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                          $nCargas = mysqli_num_rows($cCargas);
                          while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
                            $cDatos = mysqli_query($conexion, "SELECT mat_id, mat_nombre, gra_codigo, gra_nombre, uss_id, uss_nombre FROM ".BD_ACADEMICA.".academico_materias am, ".BD_ACADEMICA.".academico_grados gra, ".BD_GENERAL.".usuarios uss WHERE am.mat_id='".$rCargas['car_materia']."' AND gra_id='".$rCargas['car_curso']."' AND uss_id='".$rCargas['car_docente']."' AND am.institucion={$config['conf_id_institucion']} AND am.year={$_SESSION["bd"]} AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]} AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}");
                            $rDatos = mysqli_fetch_array($cDatos, MYSQLI_BOTH);
                            //DEFINITIVAS
                            $carga = $rCargas['car_id'];
                            $periodo = $cPeriodo;
                            $estudiante = $estudiante;
                            include("../definitivas.php");
														$definitivaFinal=$definitiva;
														if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
															$definitivaFinal= !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
														}
                    ?>
                                      <tr id="data1" class="odd gradeX">
                                          <td style="text-align:center;"><?=$rCargas['car_id'];?></td>
                                          <td><?=UsuariosPadre::nombreCompletoDelUsuario($rDatos);?></td>
                                          <td><?=$rDatos['mat_nombre'];?></td>
                                          <td style="text-align:center;"><?=$porcentajeActual;?>%</td>
                                          <td style="color:<?=$colorDefinitiva;?>; text-align:center; font-weight:bold;"><?=$definitivaFinal;?></td>
                                        </tr>
                                     <?php
                     }}}}
                     $promedioGFinal=$promedioG;
                     if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                       $estiloNotaPromedioG = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioG);
                       $promedioGFinal= !empty($estiloNotaPromedioG['notip_nombre']) ? $estiloNotaPromedioG['notip_nombre'] : "";
                     }
								   ?>   
                                    </tbody>
                                    <!-- END -->
                                     <tfoot>
                                      <tr style="font-weight:bold;">
                                        <td colspan="4" style="text-align:right;">PROMEDIO GENERAL</td>
                                        <td style="text-align:center;"><?php echo $promedioGFinal;?></td>
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

Doy constancia de haber recibido del <?=$informacion_inst["info_nombre"]?> el<br>
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
                                        <img src="https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png" width="150"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
 
                  </body>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

                  </html>
