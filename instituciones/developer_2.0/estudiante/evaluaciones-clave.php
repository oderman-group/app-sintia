<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0009';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="description" content="Responsive Admin Template" />
    <meta name="author" content="SmartUniversity" />
    <title>Plataforma SINTIA</title>
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
	<!-- icons -->
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="fonts/material-design-icons/material-icon.css" rel="stylesheet" type="text/css" />
    <!-- bootstrap -->
	<link href="../../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- style -->
    <link rel="stylesheet" href="../../../config-general/assets/css/pages/extra_pages.css">
	<!-- favicon -->
    <link rel="shortcut icon" href="http://radixtouch.in/templates/admin/smart/source/assets/img/favicon.ico" /> 
</head>
<body>
    <?php
	$evaluacion = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividad_evaluaciones WHERE eva_id='".$_GET["idE"]."' AND eva_estado=1",$conexion));
	//Si la evaluación no tiene clave, lo mandamos de una vez a realizarla.
	if($evaluacion['eva_clave']==""){
		echo '<script type="text/javascript">window.location.href="evaluaciones-realizar.php?idE='.$_GET["idE"].'";</script>';
		exit();
	}
	//ENVIAMOS LA CLAVE DE LA EVALUACIÓN
	if(isset($_GET["claveE"]) and trim($_GET["claveE"])!=""){
		//VERIFICAMOS QUE LA CLAVE DE LA EVALUACIÓN ESTÉ CORRECTA
		if($_GET["claveE"]!=$evaluacion['eva_clave']){
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=202&idE='.$_GET["idE"].'&claveErrada='.$_GET["claveE"].'";</script>';
			exit();
		}
		
		echo '<script type="text/javascript">window.location.href="evaluaciones-realizar.php?idE='.$_GET["idE"].'";</script>';
		exit();
	}
	?>
	<div class="form-title">
        <h1><?=$evaluacion['eva_nombre'];?></h1>
    </div>
    <form action="evaluaciones-clave.php" method="get">
		<input type="hidden" name="idE" value="<?=$_GET["idE"];?>">
	<!-- Login Form-->
    <div class="lockscreen-form text-center">
        <div class="toggle"><a href="evaluaciones.php"><i class="fa fa-times white-color"></i></a>
        </div>
        <div class="form">
            <img src="../files/fotos/<?=$datosUsuarioActual['uss_foto'];?>" class="imgroundcorners" />
            <h3><?=$datosUsuarioActual['uss_nombre'];?></h3>
            <p><?=$frases[151][$datosUsuarioActual[8]];?></p>
			<input type="password" name="claveE" placeholder="<?=$frases[152][$datosUsuarioActual[8]];?>" required autocomplete="off" autofocus />
            <button type="submit"><?=$frases[150][$datosUsuarioActual[8]];?></button>
            <div class="newaccount"><a href="javascript:void(0)"><?=$frases[153][$datosUsuarioActual[8]];?></a>
            </div>
        </div>
    </div>
	</form>
    <!-- start js include path -->
    <script src="../../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../../config-general/assets/js/pages/extra-pages/pages.js" ></script>
    <!-- end js include path -->
</body>

</html>
