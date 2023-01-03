<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	if (trim($_POST["agno"]) == "" or trim($_POST["periodo"]) == "" or trim($_POST["desde"]) == "" or trim($_POST["hasta"]) == "" or trim($_POST["notaMinima"]) == "" or trim($_POST["perdida"]) == "" or trim($_POST["ganada"]) == "" or trim($_POST["periodoTrabajar"]) == "" or trim($_POST["numIndicadores"]) == "" or trim($_POST["valorIndicadores"]) == "" or trim($_POST["estiloNotas"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}

	mysqli_query($conexion, "UPDATE configuracion SET conf_agno='" . $_POST["agno"] . "', conf_periodo='" . $_POST["periodo"] . "', conf_nota_desde='" . $_POST["desde"] . "', conf_nota_hasta='" . $_POST["hasta"] . "', conf_nota_minima_aprobar='" . $_POST["notaMinima"] . "', conf_color_perdida='" . $_POST["perdida"] . "', conf_color_ganada='" . $_POST["ganada"] . "', conf_periodos_maximos='" . $_POST["periodoTrabajar"] . "', conf_num_indicadores='" . $_POST["numIndicadores"] . "', conf_valor_indicadores='" . $_POST["valorIndicadores"] . "', conf_notas_categoria='" . $_POST["estiloNotas"] . "', conf_fecha_parcial='" . $_POST["fechapa"] . "', conf_descripcion_parcial='" . $_POST["descrip"] . "', conf_ancho_imagen='" . $_POST["logoAncho"] . "', conf_alto_imagen='" . $_POST["logoAlto"] . "', conf_mostrar_nombre='" . $_POST["mostrarNombre"] . "', conf_inicio_recibos_ingreso='" . $_POST["iri"] . "', conf_inicio_recibos_egreso='" . $_POST["ire"] . "' WHERE conf_id=1");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	if ($_POST["claveSeguridad"] == "Oderman2014$") {
		mysqli_query($conexion, "UPDATE configuracion SET conf_base_datos='" . $_POST["baseDatos"] . "', conf_servidor='" . $_POST["servidorConexion"] . "', conf_usuario='" . $_POST["usuarioConexion"] . "', conf_clave='" . $_POST["claveConexion"] . "', conf_id_institucion='" . $_POST["idColegio"] . "' WHERE conf_id=1");
		
	}

	echo '<script type="text/javascript">window.location.href="configuracion-sistema.php";</script>';
	exit();