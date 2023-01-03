<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>

<?php
$asig=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_GET["curso"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");			
$num_asg=mysqli_num_rows($asig);
$consultaGrados=mysqli_query($conexion, "SELECT * FROM academico_grados, academico_grupos WHERE gra_id='".$_GET["curso"]."' AND gru_id='".$_GET["grupo"]."'");
$grados = mysqli_fetch_array($consultaGrados, MYSQLI_BOTH);
?>
<head>
	<title>Sabanas</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">

    <script src="https://plataformasintia.com/eduardoortega/assets/plugins/jquery/jquery-1.9.1.min.js?v1.3.1"></script>


    <script type="text/javascript">
  function def(enviada){
  var nota = enviada.value;
  var codEst = enviada.id;
  var carga = enviada.name;
  var per = enviada.alt;
 if (nota><?=$config[4];?> || isNaN(nota) || nota<<?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;} 
    $('#resp').empty().hide().html("Esperando...").show(1);
    datos = "nota="+(nota)+
           "&carga="+(carga)+
           "&codEst="+(codEst)+
           "&per="+(per);
         $.ajax({
           type: "POST",
           url: "ajax-definitivas-registrar.php",
           data: datos,
           success: function(data){
           $('#resp').empty().hide().html(data).show(1);
           }
         });

  }
  </script>
</head>
<body style="font-family:Arial;">
	
  <!--
<div style="margin: 10px;">
		<img src="../../files-general/main-app/informes/sabanas.jpg" style="width: 100%;">
	</div>-->
	
<div align="center" style="margin-bottom:20px;">
    <?=$informacion_inst["info_nombre"]?><br>
    PERIODO: <?=$_GET["per"];?></br>
    <b><?=strtoupper($grados["gra_nombre"]." ".$grados["gru_nombre"]);?></b><br>

    <!--
    <p><a href="https://plataformasintia.com/icolven/compartido/reportes-sabanas-indicador.php?curso=<?=$_GET["curso"];?>&grupo=<?=$_GET["grupo"];?>&per=<?=$_GET["per"];?>" target="_blank">VER SABANAS CON INDICADORES</a></p>-->
    
</div>  
<div style="margin: 10px;">

  <span id="resp"></span>

  <table bgcolor="#FFFFFF" width="100%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
        <td align="center">No</b></td>
        <td align="center">C&oacute;digo</td>
        <td align="center">Estudiante</td>
        <!--<td align="center">Gru</td>-->
        <?php
		$materias1=mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso=".$_GET["curso"]." AND car_grupo='".$_GET["grupo"]."'");
		while($mat1=mysqli_fetch_array($materias1, MYSQLI_BOTH)){
			$nombresMat=mysqli_query($conexion, "SELECT * FROM academico_materias WHERE mat_id=".$mat1[4]);
			$Mat=mysqli_fetch_array($nombresMat, MYSQLI_BOTH);
		?>
        	<td align="center"><?=strtoupper($Mat[3]);?></td>      
  		<?php
		}
		?>
        <td align="center" style="font-weight:bold;">PROM</td>
  </tr>
  <?php
  $cont=1;
  $mayor=0;
  $nombreMayor="";
  while($fila=mysqli_fetch_array($asig, MYSQLI_BOTH))
  {
  		$cuentaest=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$fila[0]." AND bol_periodo=".$_GET["per"]." GROUP BY bol_carga");
		$numero=mysqli_num_rows($cuentaest);
		$def='0.0';
		
  ?>
  <tr style="font-size:13px;">
      <td align="center"> <?php echo $cont;?></td>
      <td align="center"> <?php echo $fila[1];?></td>
      <td><?=strtoupper($fila[3]." ".$fila[4]." ".$fila[5]);?></td> 
      <!--<td align="center"><?php if($fila[7]==1)echo "A"; else echo "B";?></td> -->
       <?php
		$suma=0;
		$materias1 = mysqli_query($conexion, "SELECT * FROM academico_cargas 
    WHERE car_curso=".$_GET["curso"]." AND car_grupo='".$_GET["grupo"]."'");

		while($mat1=mysqli_fetch_array($materias1, MYSQLI_BOTH)){
			$notas=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$fila[0]." AND bol_carga=".$mat1[0]." AND bol_periodo=".$_GET["per"]);
			$nota=mysqli_fetch_array($notas, MYSQLI_BOTH);
			$defini = $nota[4];
			if($defini<$config[5]) $color='red'; else $color='blue';
			$suma=($suma+$defini);
		?>
        	<td align="center" style="color:<?=$color;?>;">
           
           <input style="text-align:center; width:40px; color:<?=$color;?>" value="<?=$nota[4];?>" name="<?=$mat1[0];?>" id="<?=$fila[0];?>" onChange="def(this)" alt="<?=$_GET["per"];?>">

          </td>      
  		<?php
		}
		if($numero>0) {
			$def=round(($suma/$numero),2);
		}
		if($def==1)	$def="1.0"; if($def==2)	$def="2.0"; if($def==3)	$def="3.0"; if($def==4)	$def="4.0"; if($def==5)	$def="5.0"; 	
		if($def<$cde[5]) $color='red'; else $color='blue'; 
		$notas1[$cont] = $def;
		$grupo1[$cont] = strtoupper($fila[3]." ".$fila[4]." ".$fila[5]);
		?>
      <td align="center" style="font-weight:bold; color:<?=$color;?>;"><?=$def;?></td>  
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>
   
</div>

</body>
</html>


