<?php
include("../conexion-datos.php");
$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionesConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM ".$baseDatosServicios.".instituciones WHERE ins_estado = 1");

//Verificamos que el link no tenga más de 24 horas.
$consultaTiempo = mysqli_query($conexion, "SELECT TIMESTAMPDIFF(HOUR, resc_fec_solicitud, NOW()) as horas FROM ".$baseDatosServicios.".restaurar_clave WHERE resc_id_md5 = '".$_GET['idRegistro']."'");
$datosTiempo = mysqli_fetch_array($consultaTiempo, MYSQLI_BOTH);

if($datosTiempo['horas'] > 24){
    echo "Este link ya expiró, por favor genere uno nuevo."; 
    exit();  
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta name="description" content="Responsive Admin Template" />
	<meta name="author" content="SmartUniversity" />
	<title>Plataforma Educativa SINTIA | Restaurar contraseña </title>
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
			<form method="post" action="restaurar-contrasena-guardar.php">
			 <input type="hidden" value="<?=$_GET['idRegistro']?>" name="idRegistro">
			 <input type="hidden" value="22" name="rBd"> <!-- Este dato debe cambiarse por el dinámico de la Insti. -->
				
				<div id="campos">
					<input type="password" name="clave2" placeholder="Nueva contraseña" />
					<input type="password" name="clave" placeholder="Vuelva a escribir la contraseña" />
					<button>Restaurar contraseña</button>	
				</div>
			</form>
		</div>

	</div>
	<!-- start js include path -->
	<script src="../config-general/assets/plugins/jquery/jquery.min.js"></script>

	<script src="../config-general/assets/js/pages/extra-pages/pages.js"></script>
	<!-- end js include path -->

</body>

</html>