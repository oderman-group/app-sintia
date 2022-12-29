<?php
session_start();
if (isset($_SESSION["id"]) and $_SESSION["id"] != "") {
	if (isset($_GET["urlDefault"]) and $_GET["urlDefault"] != "") {

		include("modelo/conexion.php");
		$sesionAbierta = mysql_fetch_array(mysql_query("SELECT * FROM usuarios WHERE uss_id='" . $_SESSION["id"] . "'", $conexion));

		switch ($sesionAbierta[3]) {
			case 1:
				$url = 'directivo/' . $_GET["urlDefault"];
				break;
			case 2:
				$url = 'docente/' . $_GET["urlDefault"];
				break;
			case 3:
				$url = 'acudiente/' . $_GET["urlDefault"];
				break;
			case 4:
				$url = 'estudiante/' . $_GET["urlDefault"];
				break;
			case 5:
				$url = '../directivo/index.php';
				break;
			default:
				$url = 'controlador/salir.php';
				break;
		}

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
			<h2>Ingreso a la plataforma</h2>
			<form method="post" action="controlador/autentico.php">
				<input type="hidden" name="urlDefault" value="<?php if(isset($_GET["urlDefault"])) echo $_GET["urlDefault"]; ?>" />

				<div class="form-group row">

					<div class="col-sm-12">
						<select class="form-control  select2" name="bd" required onChange="mostrar(this)">
							<option value="">Seleccione su Institución</option>
							<?php
							while($instituciones = mysqli_fetch_array($institucionesConsulta, MYSQLI_BOTH)){
							?>
								<option value="<?=$instituciones['ins_id'];?>"><?=$instituciones['ins_siglas'];?></option>
							<?php }?>
							

						</select>
					</div>
				</div>

				<div id="campos" style="display: none;">
					<input type="text" name="Usuario" placeholder="Usuario" />
					<input type="password" name="Clave" placeholder="Contraseña" />

					<div class="form-group row">

						<div class="col-sm-12">
							<select class="form-control  select2" name="agnoIngreso" required>
								<option value="2020">Seleccione el año</option>
								<option value="2021">2021</option>
								<option value="2022" selected>2022</option>
							</select>
						</div>
					</div>

					<?php
					if (isset($_GET["error"]) and $_GET["error"] == 3) {
						$numA1 = rand(1, 10);
						$numA2 = rand(1, 10);
						$resultadoA = $numA1 + $numA2;
					?>
						<p style="color: tomato;"><b>Valida que no eres un Robot</b><br>
							Escribe el resultado de la siguiente operación.</p>
						<input type="hidden" name="sumaReal" value="<?= md5($resultadoA); ?>" />
						<input type="text" name="suma" placeholder="Cuánto es <?= $numA1 . "+" . $numA2; ?>?" required autocomplete="off" style="font-weight: bold;" />
					<?php } ?>

					<button>Entrar</button>
					<div class="forgetPassword"><a href="javascript:void(0)">Olvidaste tu contraseña?</a> </div>
				</div>





			</form>
		</div>

		<div class="form formRegister">
			<h2>Create an account</h2>
			<form>
				<input type="text" placeholder="Username" />
				<input type="password" placeholder="Password" />
				<input type="email" placeholder="Email Address" />
				<input type="text" placeholder="Full Name" />
				<input type="tel" placeholder="Phone Number" />
				<button>Register</button>
			</form>
		</div>

		<div class="form formReset">
			<h2>Recordaremos tu contraseña</h2>
			<form action="guardar.php" method="post">
				<input type="hidden" value="2" name="id">
				<div class="form-group row">
					<div class="col-sm-12">
						<select class="form-control  select2" name="rBd" required>
							<option value="">Seleccione su Institución</option>
							<?php
							$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
							$institucionesConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM ".$baseDatosServicios.".instituciones WHERE ins_estado = 1");
							while($instituciones2 = mysqli_fetch_array($institucionesConsulta, MYSQLI_BOTH)){
							?>
								<option value="<?=$instituciones2['ins_id'];?>"><?=$instituciones2['ins_siglas'];?></option>
							<?php }?>
						</select>
					</div>
				</div>
				<input type="email" name="email" placeholder="Tu email" />
				<button type="submit">Verificar Email</button>
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

