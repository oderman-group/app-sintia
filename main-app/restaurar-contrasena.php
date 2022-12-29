<?php
session_start();
if (isset($_SESSION["id"]) and $_SESSION["id"] != "") {
	if (isset($_GET["urlDefault"]) and $_GET["urlDefault"] != "") {

		include("modelo/conexion.php");
		$sesionAbierta = mysql_fetch_array(mysql_query("SELECT * FROM usuarios WHERE uss_id='" . $_SESSION["id"] . "'", $conexion));

		

		header("Location:" . $url);
		exit();
	} else {
		echo "
		<div style='font-family:Arial;'>
		Ya hay una sesi&oacute;n de SINTIA abierta en este navegador: " . $_SESSION["id"] . "<br>
		<a href='controlador/salir.php'>[Cerrar la sesi&oacute;n actual]</a>
		</div>";
		exit();
	}
}
include("../conexion-datos.php");
$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionesConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM ".$baseDatosServicios.".instituciones WHERE ins_estado = 1");
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta name="description" content="Responsive Admin Template" />
	<meta name="author" content="SmartUniversity" />
	<title>Plataforma Educativa SINTIA | Login </title>
	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
	<!-- icons -->
	<link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="fonts/material-design-icons/material-icon.css" rel="stylesheet" type="text/css" />
	<!-- bootstrap -->
	<link href="../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- style -->
	<link rel="stylesheet" href="../config-general/assets/css/pages/extra_pages.css">
	<!-- favicon -->
	<link rel="shortcut icon" href="http://radixtouch.in/templates/admin/smart/source/assets/img/favicon.ico" />
</head>

<body>
	<div class="form-title">
		<h1>PLATAFORMA SINTIA</h1>
	</div>
	<!-- Login Form-->
	<div class="login-form text-center">
		<div class="toggle"><i class="fa fa-user-plus"></i>
		</div>
		<div class="form formLogin">
			<h2>Restablecer contraseña</h2>
			<form method="post" action="guardar.php">
			 <input type="hidden" value="3" name="id">
			 <input type="hidden" value="<?=$_GET['idU']?>" name="idU">
			 <input type="hidden" value="<?=$_GET['idI']?>" name="rBd">
				

				<div id="campos">
					<input type="password" name="clave2" placeholder="nueva contraseña" />
					<input type="password" name="Clave" placeholder="vuelve a escribir la contraseña" />


					<button>Guardar contraseña</button>	
				</div>
			</form>
		</div>

	</div>
	<!-- start js include path -->
	<script src="../config-general/assets/plugins/jquery/jquery.min.js"></script>

	<script src="../config-general/assets/js/pages/extra-pages/pages.js"></script>
	<!-- end js include path -->

	<script>
		function mostrar(data) {
			if (data.value == "")
				document.getElementById("campos").style.display = "none";
			else
				document.getElementById("campos").style.display = "block";
		}
	</script>
</body>

</html>