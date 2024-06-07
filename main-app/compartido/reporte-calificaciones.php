<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

$grado="";
if(!empty($_GET["grado"])){ $grado=base64_decode($_GET["grado"]);}
$grupo="";
if(!empty($_GET["grupo"])){ $grupo=base64_decode($_GET["grupo"]);}
$idActividad="";
if(!empty($_GET["idActividad"])){ $idActividad=base64_decode($_GET["idActividad"]);}
?>
<head>
	<title>Informes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/logoodermanp.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME DE CALIFICACIONES - ACTIVIDAD: <?=$idActividad;?></br>
</div>   
   <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
        <th class="center">C&oacute;digo</th>
                                        <th class="center">Nombre</th>
                                        <th class="center">Nota</th>
                                        <th class="center">Observaciones</th>
  </tr>
  <?php
									 $cont = 1;
                                     $filtroAdicional= "AND mat_grado='".$grado."' AND mat_grupo='".$grupo."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
                                     $consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"");
									 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                        $nombre =Estudiantes::NombreCompletoDelEstudiante($resultado);
										 //LAS CALIFICACIONES A MODIFICAR Y LAS OBSERVACIONES
                                        $notasResultado = Calificaciones::traerCalificacionActividadEstudiante($config, $idActividad, $resultado['mat_id']);

                                        $notasResultadoFinal="";
                                        if(!empty($notasResultado['cal_nota'])){
                                            $notasResultadoFinal=$notasResultado['cal_nota'];
                                            if($config['conf_forma_mostrar_notas'] == CUALITATIVA){                     
                                                $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado['cal_nota']);
                                                $notasResultadoFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                            }
                                        }
									 ?>
  <tr style="font-size:13px;">
      <td class="center"><?=$resultado['mat_id'];?></td>
                                        <td><?=$nombre?></td>
                                        <td class="center" style="font-size: 13px; text-align: center; color:<?php if(!empty($notasResultado['cal_nota']) && $notasResultado['cal_nota']<$config[5]){ echo $config[6]; }elseif(!empty($notasResultado['cal_nota']) && $notasResultado['cal_nota']>=$config[5]){ echo $config[7]; }else{ echo "black";}?>"><?=$notasResultadoFinal;?></td>
                                        <td class="center"><?php if(!empty($notasResultado['cal_observaciones'])){ echo $notasResultado['cal_observaciones'];}?></td>
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>
  </center>
	<div align="center" style="font-size:10px; margin-top:10px;">
    	<img src="../files/images/sintia.png" height="50" width="100"><br>
        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
    </div>
</body>
</html>


