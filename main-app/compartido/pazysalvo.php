<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>SINTIA | Saldos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
    <style type="text/css">
	.pieP{
		margin-top:100px;
		display:flex;
		flex-direction:row;
		justify-content:space-between;
	}
	.headerP{
		display:flex;
		flex-direction:row;
		justify-content:space-between;
	}
    </style>
</head>
<body style="font-family:Arial;">
<div class="headerP">
    <div><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"></div>
    <div align="center" style="margin-bottom:20px;">
        REPUBLICA DE COLOMBIA<br>
        <?=$informacion_inst["info_nombre"]?><br>
        <h1>PAZ Y SALVO</h1>
    </div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
</div>   
<?php
 $meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 $consultaUsuario=mysqli_query($conexion, "SELECT * FROM academico_matriculas
 INNER JOIN academico_grados ON gra_id=mat_grado
 WHERE mat_id_usuario='".$_GET["id"]."'");
 $usuario = mysqli_fetch_array($consultaUsuario, MYSQLI_BOTH);
?>
    <div align="justify">
    <p>Certificamos que <b><?=strtoupper($usuario['mat_primer_apellido']." ".$usuario['mat_segundo_apellido']." ".$usuario['mat_nombres']." ".$usuario['mat_nombre2']);?></b> del grado <b><?=$usuario['gra_nombre'];?></b> se encuentra a PAZ y SALVO por todo concepto en el colegio <?=$informacion_inst["info_nombre"]?>.</p>
    <p>Para constancia  de lo anterior se firma en Paz de Ariporo, a los <?=date("d");?> días del mes de <?=$meses[date("m")];?> de <?=date("Y");?>.</p>
    </div>
    
    <div class="pieP">
    	<div>Rector(a)</div>
        <div>Coordinador(a)</div>
        <div>Director(a) de grupo</div>
        <div>Tienda Escolar</div>
    </div>

	<div align="center" style="font-size:10px; margin-top:10px;">
      <img src="../files/images/sintia.png" height="50" width="100"><br>
      SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
     </div>
     <script type="text/javascript">print();</script>
</body>
</html>


