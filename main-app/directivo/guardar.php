<?php
$modulo = 4; ?>
<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php 
include("../../config-general/config.php");

include("../compartido/sintia-funciones.php");
?>

<?php
if (isset($_POST["id"])) {
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('" . $_SESSION["id"] . "', '" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "', 'Acciones POST - " . $_SERVER['HTTP_REFERER'] . "', now())");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
} elseif ($_GET["get"]) {
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('" . $_SESSION["id"] . "', '" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "', 'Acciones GET - " . $_SERVER['HTTP_REFERER'] . "', now())");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
} else {
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('" . $_SESSION["id"] . "', '" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "', 'Acciones DESCONOCIDA - " . $_SERVER['HTTP_REFERER'] . "', now())");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
}
?>
<?php
//GUARDAR MOVIMIENTO
if ($_POST["id"] == 8) {
	if (trim($_POST["fecha"]) == "" or trim($_POST["detalle"]) == "" or trim($_POST["valor"]) == "" or trim($_POST["tipo"]) == "" or trim($_POST["forma"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	if ($_POST["tipo"] == 1) {
		$consultaConsecutivoActual=mysqli_query($conexion, "SELECT * FROM finanzas_cuentas WHERE fcu_tipo=1 ORDER BY fcu_id DESC");
		$consecutivoActual = mysqli_fetch_array($consultaConsecutivoActual, MYSQLI_BOTH);
		if ($consecutivoActual['fcu_consecutivo'] == "") {
			$consecutivo = $config['conf_inicio_recibos_ingreso'];
		} else {
			$consecutivo = $consecutivoActual['fcu_consecutivo'] + 1;
		}
	}
	if ($_POST["tipo"] == 2) {
		$consultaConsecutivoActual=mysqli_query($conexion, "SELECT * FROM finanzas_cuentas WHERE fcu_tipo=2 ORDER BY fcu_id DESC");
		$consecutivoActual = mysqli_fetch_array($consultaConsecutivoActual, MYSQLI_BOTH);
		if ($consecutivoActual['fcu_consecutivo'] == "") {
			$consecutivo = $config['conf_inicio_recibos_egreso'];
		} else {
			$consecutivo = $consecutivoActual['fcu_consecutivo'] + 1;
		}
	}
	mysqli_query($conexion, "INSERT INTO finanzas_cuentas(fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado, fcu_forma_pago, fcu_cerrado, fcu_consecutivo)VALUES('" . $_POST["fecha"] . "','" . $_POST["detalle"] . "','" . $_POST["valor"] . "','" . $_POST["tipo"] . "','" . $_POST["obs"] . "','" . $_POST["usuario"] . "',0,'" . $_POST["forma"] . "',0,'" . $consecutivo . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="movimientos.php";</script>';
	exit();
}
//GUARDAR REPORTE
if ($_POST["id"] == 9) {
	if (trim($_POST["fecha"]) == "" or trim($_POST["codigo"]) == "" or trim($_POST["falta"]) == "" or trim($_POST["tipo"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	$consultaUsuarioResponsable=mysqli_query($conexion, "SELECT * FROM usuarios_por_estudiantes WHERE upe_id_estudiante='" . $_POST["codigo"] . "'");
	$usuarioResponsable = mysqli_fetch_array($consultaUsuarioResponsable, MYSQLI_BOTH);
	mysqli_query($conexion, "INSERT INTO general_alertas(alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_vista, alr_categoria, alr_importancia)VALUES('Reporte disciplinario','El estudiante " . $_POST["codigo"] . " le han hecho un reporte disciplinario',2,'" . $usuarioResponsable[1] . "',now(),0,2,2)");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	mysqli_query($conexion, "INSERT INTO disciplina_reportes(dr_fecha, dr_estudiante, dr_falta, dr_tipo, dr_usuario)VALUES('" . $_POST["fecha"] . "','" . $_POST["codigo"] . "','" . $_POST["falta"] . "','" . $_POST["tipo"] . "','" . $_SESSION["id"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="disciplina-listado-reportes.php";</script>';
	exit();
}
//ACTUALIZAR CONFIGURACION REPORTES
if ($_POST["id"] == 13) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["color_borde"]) == "" or trim($_POST["color_encabezado"]) == "" or trim($_POST["tborde"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	//mysqli_query($conexion, "UPDATE configuracion SET conf_color_borde='#009900', conf_color_encabezado='#00FF99',conf_tam_borde=3 WHERE conf_id=2;");
	mysqli_query($conexion, "UPDATE configuracion SET conf_color_borde='" . $_POST["color_borde"] . "', conf_color_encabezado='" . $_POST["color_encabezado"] . "',conf_tam_borde=" . $_POST["tborde"] . " WHERE conf_id=1;");
	echo '<script type="text/javascript">window.location.href="config-reporte.php";</script>';
	exit();
}
//ACTUALIZAR USUARIO
if ($_POST["id"] == 16) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS

	validarClave($_POST["clave"]);

	mysqli_query($conexion, "UPDATE usuarios SET 
	uss_usuario='" . $_POST["usuario"] . "', 
	uss_clave='" . $_POST["clave"] . "', 
	uss_tipo=" . $_POST["tipoUsuario"] . ", 
	uss_nombre='" . $_POST["nombre"] . "', 
	uss_email='" . strtolower($_POST["email"]) . "', 
	uss_genero='" . $_POST["genero"] . "',
	uss_celular='" . $_POST["celular"] . "',
	uss_ocupacion='" . $_POST["ocupacion"] . "',
	uss_lugar_expedicion='" . $_POST["lExpedicion"] . "',
	uss_direccion='" . $_POST["direccion"] . "',
	uss_telefono='" . $_POST["telefono"] . "',

	uss_ultima_actualizacion=now()
	WHERE uss_id='" . $_POST["idR"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	if ($_POST["tipoUsuario"] == 4) {
		mysqli_query($conexion, "UPDATE academico_matriculas SET mat_email='" . strtolower($_POST["email"]) . "'");
		
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}

//MODIFICAR CARGAS ACADEMICAS
if ($_POST["id"] == 17) {
	mysqli_query($conexion, "UPDATE academico_cargas SET car_docente='" . $_POST["docente"] . "', car_curso='" . $_POST["curso"] . "', car_grupo='" . $_POST["grupo"] . "', car_materia='" . $_POST["asignatura"] . "', car_periodo='" . $_POST["periodo"] . "', car_director_grupo='" . $_POST["dg"] . "', car_ih=" . $_POST["ih"] . ", car_activa='" . $_POST["estado"] . "', car_maximos_indicadores='" . $_POST["maxIndicadores"] . "', car_maximas_calificaciones='" . $_POST["maxActividades"] . "', car_configuracion='" . $_POST["valorActividades"] . "', car_valor_indicador='" . $_POST["valorIndicadores"] . "', car_permiso1='" . $_POST["permiso1"] . "', car_permiso2='" . $_POST["permiso2"] . "', car_indicador_automatico='" . $_POST["indicadorAutomatico"] . "' WHERE car_id=" . $_POST["idR"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "DELETE FROM academico_intensidad_curso WHERE ipc_curso='" . $_POST["curso"] . "' AND ipc_materia='" . $_POST["asignatura"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "INSERT INTO academico_intensidad_curso(ipc_curso, ipc_materia, ipc_intensidad)VALUES('" . $_POST["curso"] . "','" . $_POST["asignatura"] . "','" . $_POST["ih"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//CREAR CARGAS ACADEMICAS
if ($_POST["id"] == 18) {
	$numero = (count($_POST["grupo"]));
	$contador = 0;
	while ($contador < $numero) {
		mysqli_query($conexion, "INSERT INTO academico_cargas (car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, car_maximos_indicadores, car_maximas_calificaciones, car_configuracion, car_valor_indicador, car_permiso2, car_indicador_automatico)VALUES('" . $_POST["docente"] . "', '" . $_POST["curso"] . "', '" . $_POST["grupo"][$contador] . "','" . $_POST["asignatura"] . "', '" . $_POST["periodo"] . "', 1, 1, '" . $_POST["dg"] . "', '" . $_POST["ih"] . "', now(), '" . $_SESSION["id"] . "', '" . $_POST["maxIndicadores"] . "', '" . $_POST["maxActividades"] . "', '" . $_POST["valorActividades"] . "', '" . $_POST["valorIndicadores"] . "', '" . $_POST["permiso2"] . "', '" . $_POST["indicadorAutomatico"] . "')");
		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
		$contador++;
	}

	echo '<script type="text/javascript">window.location.href="cargas.php";</script>';
	exit();
}
//CREAR INGRESOS-EGRESOS
if ($_POST["id"] == 19) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["fechaIE"]) == "" or trim($_POST["detalleIE"]) == "" or trim($_POST["valorIE"]) == "" or trim($_POST["tipoIE"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO finanzas_ingresos_egresos(ieg_fecha, ieg_detalle, ieg_valor, ieg_tipo, ieg_observaciones) VALUES('" . $_POST["fechaIE"] . "','" . $_POST["detalleIE"] . "'," . $_POST["valorIE"] . "," . $_POST["tipoIE"] . ",'" . $_POST["obsIE"] . "');");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//CREAR INGRESOS-EGRESOS
if ($_POST["id"] == 20) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["fechaIE"]) == "" or trim($_POST["detalleIE"]) == "" or trim($_POST["valorIE"]) == "" or trim($_POST["tipoIE"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE finanzas_ingresos_egresos SET ieg_fecha='" . $_POST["fechaIE"] . "', ieg_detalle='" . $_POST["detalleIE"] . "', ieg_valor=" . $_POST["valorIE"] . ", ieg_tipo=" . $_POST["tipoIE"] . ", ieg_observaciones='" . $_POST["obsIE"] . "' WHERE ieg_id=" . $_POST["id_IE"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ACTUALIZAR CONFIGURACION INSTITUCION
if ($_POST["id"] == 21) {
	mysqli_query($conexion, "UPDATE configuracion SET conf_periodo=" . $_POST["periodoActualC"] . ", conf_nota_desde=" . $_POST["notaMinC"] . ", conf_nota_hasta=" . $_POST["notaMaxC"] . ", conf_nota_minima_aprobar=" . $_POST["notaMinAprobarC"] . ", conf_color_perdida='" . $_POST["colorNotasPC"] . "', conf_color_ganada='" . $_POST["colorNotasGC"] . "',conf_pie='" . $_POST["configPie"] . "', conf_num_materias_perder_ano=" . $_POST["numMateriasMinRC"] . ", conf_ini_matrucula='" . $_POST["iniciomatC"] . "', conf_fin_matricul='" . $_POST["finmatC"] . "', conf_apertura_academica='" . $_POST["aperturaacademicaAC"] . "', conf_clausura_academica='" . $_POST["clausuraacademicaAC"] . "'
WHERE conf_id=" . $_POST["id_IC"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="configuracion-institucion.php";</script>';
	exit();
}
//ACTUALIZAR INFORMACION INSTITUCION
if ($_POST["id"] == 22) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["rectorI"]) == "" or trim($_POST["secretarioI"]) == "" or trim($_POST["nitI"]) == "" or trim($_POST["nomInstI"]) == "" or trim($_POST["direccionI"]) == "" or trim($_POST["telI"]) == "" or trim($_POST["calseI"]) == "" or trim($_POST["caracterI"]) == "" or trim($_POST["calendarioI"]) == "" or trim($_POST["jornadaI"]) == "" or trim($_POST["horarioI"]) == "" or trim($_POST["nivelesI"]) == "" or trim($_POST["modalidadI"]) == "" or trim($_POST["propietarioI"]) == "" or trim($_POST["coordinadorI"]) == "" or trim($_POST["tesoreroI"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	if ($_FILES['logo']['name'] != "") {
		$archivo = $_FILES['logo']['name'];
		$archivoAnt = $_POST["logoAnterior"];
		$destino = "../files/images/logo/";
		@unlink($destino . "/" . $archivoAnt);
		move_uploaded_file($_FILES['logo']['tmp_name'], $destino . "/" . $archivo);
	} else {
		$archivo = $_POST["logoAnterior"];
	}
	mysqli_query($conexion, "UPDATE general_informacion SET info_rector='" . $_POST["rectorI"] . "', info_secretaria_academica='" . $_POST["secretarioI"] . "', info_logo='" . $archivo . "', info_nit='" . $_POST["nitI"] . "', info_nombre='" . $_POST["nomInstI"] . "', info_direccion='" . $_POST["direccionI"] . "', info_telefono='" . $_POST["telI"] . "', info_clase='" . $_POST["calseI"] . "', info_caracter='" . $_POST["caracterI"] . "',info_calendario='" . $_POST["calendarioI"] . "', info_jornada='" . $_POST["jornadaI"] . "', info_horario='" . $_POST["horarioI"] . "', info_niveles='" . $_POST["nivelesI"] . "', info_modalidad='" . $_POST["modalidadI"] . "', info_propietario='" . $_POST["propietarioI"] . "', info_coordinador_academico='" . $_POST["coordinadorI"] . "', info_tesorero='" . $_POST["tesoreroI"] . "'
WHERE info_id=" . $_POST["idCI"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//GUARDAR MATRICULA CONDICIONAL
if ($_POST["id"] == 23) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["fecha"]) == "" or trim($_POST["codigo"]) == "" or trim($_POST["obsMC"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO disciplina_matricula_condicional(cond_fecha, cond_estudiante, cond_observacion, cond_usuario)VALUES('" . $_POST["fecha"] . "','" . $_POST["codigo"] . "','" . $_POST["obsMC"] . "','" . $_SESSION["id"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="disiplina-matricula-condicional-lista.php";</script>';
	exit();
}
//CREAR USUARIO
if ($_POST["id"] == 24) {
	$consultaUsuarioA = mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_usuario='" . $_POST["usuario"] . "'");
	$numUsuarioA = mysqli_num_rows($consultaUsuarioA);
	$datosUsuarioA = mysqli_fetch_array($consultaUsuarioA, MYSQLI_BOTH);
	if ($numUsuarioA > 0) {
		echo "<span style='font-family:Arial; color:red;'>Este nombre de usuario(<b>" . $_POST["usuario"] . "</b>) ya existe para otra persona. Cambie el nombre de usuario por favor.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO usuarios (uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_email, uss_celular, uss_genero, uss_foto, uss_portada, uss_idioma, uss_tema, uss_permiso1, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_ocupacion)VALUES(
		'" . $_POST["usuario"] . "',
		'" . $_POST["clave"] . "',
		" . $_POST["tipoUsuario"] . ",
		'" . $_POST["nombre"] . "',
		0,
		'" . strtolower($_POST["email"]) . "',
		'" . $_POST["celular"] . "',
		" . $_POST["genero"] . ",
		'default.png',
		'default.png',
		1, 
		'green', 
		1,
		0,
		now(),
		'" . $_SESSION["id"] . "', 
		'" . $_POST["ocupacion"] . "'
		)");
	$idRegistro = mysqli_insert_id($conexion);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="usuarios-editar.php?id=' . $idRegistro . '";</script>';
	exit();
}
//MODIFICAR REPORTE
if ($_POST["id"] == 27) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["fecha"]) == "" or trim($_POST["falta"]) == "" or trim($_POST["tipo"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE disciplina_reportes SET dr_fecha='" . $_POST["fecha"] . "', dr_falta='" . $_POST["falta"] . "', dr_tipo=" . $_POST["tipo"] . " WHERE dr_id=" . $_POST["idR"] . "");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//CREAR AREAS
if ($_POST["id"] == 28) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombreA"]) == "" or trim($_POST["posicionA"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO academico_areas (ar_nombre,ar_posicion)VALUES('" . $_POST["nombreA"] . "'," . $_POST["posicionA"] . ");");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//MODIFICAR AREAS
if ($_POST["id"] == 29) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombreA"]) == "" or trim($_POST["posicionA"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE academico_areas SET ar_nombre='" . $_POST["nombreA"] . "', ar_posicion=" . $_POST["posicionA"] . " WHERE ar_id=" . $_POST["idA"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//CREAR MATERIAS
if ($_POST["id"] == 30) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["codigoM"]) == "" or trim($_POST["nombreM"]) == "" or trim($_POST["siglasM"]) == "" or trim($_POST["areaM"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO academico_materias(mat_codigo, mat_nombre, mat_siglas, mat_area, mat_oficial) VALUES ('" . $_POST["codigoM"] . "','" . $_POST["nombreM"] . "','" . $_POST["siglasM"] . "','" . $_POST["areaM"] . "',1);");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//MODIFICAR MATERIAS
if ($_POST["id"] == 31) {
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	mysqli_query($conexion, "UPDATE academico_materias SET mat_codigo='" . $_POST["codigoM"] . "', mat_nombre='" . $_POST["nombreM"] . "', mat_siglas='" . $_POST["siglasM"] . "', mat_area=" . $_POST["areaM"] . ", mat_oficial=1 WHERE mat_id=" . $_POST["idM"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//GUARDAR AUTOCONOCIMIENTO
if ($_POST["id"] == 34) {
	//Me gusta
	$preferencia = $_POST["gusto"];
	$longitud = count($preferencia);
	for ($i = 0; $i < $longitud; $i++) {
		$consultaNumPref=mysqli_query($conexion, "SELECT * FROM social_preferencias_usuarios WHERE preu_usuario='" . $_SESSION["id"] . "' AND preu_gusto=1 AND preu_preferencia='" . $preferencia[$i] . "'");
		$nPref = mysqli_num_rows($consultaNumPref);
		if ($nPref == 0) {
			mysqli_query($conexion, "INSERT INTO social_preferencias_usuarios(preu_usuario, preu_preferencia, preu_gusto)VALUES('" . $_SESSION["id"] . "','" . $preferencia[$i] . "',1)");
			
		}
	}
	if (trim($_POST["gustoAdicional"]) != "") {
		$cons = mysqli_query($conexion, "SELECT lower(prel_nombre) FROM social_preferencias_lista WHERE lower(prel_nombre)='" . strtolower($_POST["gustoAdicional"]) . "' LIMIT 0,1");
		
		$numGustoAdicional = mysqli_num_rows($cons);
		$dat = mysqli_fetch_array($cons, MYSQLI_BOTH);
		if ($numGustoAdicional > 0) {
			similar_text($dat[0], strtolower($_POST["gustoAdicional"]), $parecido);
			if ($parecido < 80) {
				$permiso = 1;
			} else {
				$permiso = 0;
			}
		} else {
			$permiso = 1;
		}
		if ($permiso == 1) {
			mysqli_query($conexion, "INSERT INTO social_preferencias_lista(prel_nombre, prel_guardado, prel_fecha)VALUES('" . $_POST["gustoAdicional"] . "',2,now())");
			$idInsercion = mysqli_insert_id($conexion);
			
			mysqli_query($conexion, "INSERT INTO social_preferencias_usuarios(preu_usuario, preu_preferencia, preu_gusto)VALUES('" . $_SESSION["id"] . "','" . $idInsercion . "',1)");
			
		}
	}

	// No me gusta
	$preferencia = $_POST["nogusto"];
	$longitud = count($preferencia);
	for ($i = 0; $i < $longitud; $i++) {
		$consultaNumPref=mysqli_query($conexion, "SELECT * FROM social_preferencias_usuarios WHERE preu_usuario='" . $_SESSION["id"] . "' AND preu_no_gusto=1 AND preu_preferencia='" . $preferencia[$i] . "'");
		$nPref = mysqli_num_rows($consultaNumPref);
		if ($nPref == 0) {
			mysqli_query($conexion, "INSERT INTO social_preferencias_usuarios(preu_usuario, preu_preferencia, preu_no_gusto)VALUES('" . $_SESSION["id"] . "','" . $preferencia[$i] . "',1)");
			
		}
	}

	// Conocimiento
	$preferencia = $_POST["conocimiento"];
	$longitud = count($preferencia);
	for ($i = 0; $i < $longitud; $i++) {
		$consultaNumPref=mysqli_query($conexion, "SELECT * FROM social_preferencias_usuarios WHERE preu_usuario='" . $_SESSION["id"] . "' AND preu_conocimiento=1 AND preu_preferencia='" . $preferencia[$i] . "'");
		$nPref = mysqli_num_rows($consultaNumPref);
		if ($nPref == 0) {
			mysqli_query($conexion, "INSERT INTO social_preferencias_usuarios(preu_usuario, preu_preferencia, preu_conocimiento)VALUES('" . $_SESSION["id"] . "','" . $preferencia[$i] . "',1)");
			
		}
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ASIGNAR EVALUACIONES
if ($_POST["id"] == 37) {
	if (trim($_POST["eva"]) == "" or trim($_POST["curso"]) == "" or trim($_POST["grupo"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
			<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
		exit();
	}
	$numero = (count($_POST["usuario"]));
	$contador = 0;
	while ($contador < $numero) {
		mysqli_query($conexion, "INSERT INTO general_evaluacion_asignar(epag_id_evaluacion, epag_curso, epag_grupo, epag_usuario)VALUES('" . $_POST["eva"] . "','" . $_POST["curso"] . "','" . $_POST["grupo"] . "','" . $_POST["usuario"][$contador] . "')");
		
		$contador++;
	}
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//GUARDAR EVALUACIONES GENERALES
if ($_POST["id"] == 38) {
	if (trim($_POST["titulo"]) == "" or trim($_POST["contenido"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
			<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO general_evaluaciones(evag_nombre, evag_descripcion, evag_fecha, evag_creada)VALUES('" . $_POST["titulo"] . "','" . $_POST["contenido"] . "',now(),'" . $_SESSION["id"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ACTUALIZAR EVALUACIONES GENERALES
if ($_POST["id"] == 39) {
	if (trim($_POST["titulo"]) == "" or trim($_POST["contenido"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
			<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE general_evaluaciones SET evag_nombre='" . $_POST["titulo"] . "', evag_descripcion='" . $_POST["contenido"] . "', evag_editada='" . $_SESSION["id"] . "' WHERE evag_id='" . $_POST["idN"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="general-evaluacion.php#N' . $_POST["idN"] . '";</script>';
	exit();
}
//GUARDAR PREGUNTAS GENERALES
if ($_POST["id"] == 40) {
	if (trim($_POST["contenido"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
			<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO general_preguntas(pregg_descripcion, pregg_id_evaluacion)VALUES('" . $_POST["contenido"] . "','" . $_POST["eva"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="general-preguntas.php?eva=' . $_POST["eva"] . '";</script>';
	exit();
}
//ACTUALIZAR PREGUNTAS GENERALES
if ($_POST["id"] == 41) {
	if (trim($_POST["contenido"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE general_preguntas SET pregg_descripcion='" . $_POST["contenido"] . "' WHERE pregg_id='" . $_POST["idN"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="general-preguntas.php?eva=' . $_POST["eva"] . '";</script>';
	exit();
}
//GUARDAR RESPUESTAS GENERALES
if ($_POST["id"] == 42) {
	if (trim($_POST["respuesta"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO general_respuestas(resg_descripcion, resg_id_pregunta)VALUES('" . $_POST["respuesta"] . "','" . $_POST["idPregunta"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="general-preguntas.php?eva=' . $_POST["eva"] . '";</script>';
	exit();
}
//ASIGNAR ACUDIDOS A LOS ACUDIENTES
if ($_POST["id"] == 43) {
	$numero = (count($_POST["acudidos"]));
	$contador = 0;
	while ($contador < $numero) {
		mysqli_query($conexion, "DELETE FROM usuarios_por_estudiantes WHERE upe_id_usuario='" . $_POST["acudiente"] . "' AND upe_id_estudiante='" . $_POST["acudidos"][$contador] . "'");
		mysqli_query($conexion, "INSERT INTO usuarios_por_estudiantes(upe_id_usuario, upe_id_estudiante)VALUES('" . $_POST["acudiente"] . "','" . $_POST["acudidos"][$contador] . "')");
		mysqli_query($conexion, "UPDATE academico_matriculas SET mat_acudiente='" . $_POST["acudiente"] . "' WHERE mat_id='" . $_POST["acudidos"][$contador] . "'");
		
		$contador++;
	}
	echo '<script type="text/javascript">window.location.href="usuarios-acudidos.php?id=' . $_POST["acudiente"] . '";</script>';
	exit();
}
//CREAR USUARIO AUTORIZADO
if ($_POST["id"] == 44) {
	//IMAGENES
	$archivo = $_FILES['imagen']['tmp_name'];
	$nombre = $_FILES['imagen']['name'];
	$destino = "../files/fotos/";
	move_uploaded_file($archivo, $destino . "/" . $nombre);

	mysqli_query($conexion, "INSERT INTO autorizados (aut_documento, aut_nombre, aut_foto, aut_estudiante, aut_estado, aut_parentezco)VALUES('" . $_POST["documento"] . "','" . $_POST["nombre"] . "','" . $nombre . "','" . $_POST["estudiante"] . "','" . $_POST["estado"] . "','" . $_POST["parentezco"] . "')");
	$idIte = mysqli_insert_id($conexion);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="usuarios-autorizados.php"</script>';
	exit();
}
//EDITAR USUARIO AUTORIZADO
if ($_POST["id"] == 45) {
	$conextra = "";
	//IMAGENES
	$archivo = $_FILES['imagen']['tmp_name'];
	$nombre = $_FILES['imagen']['name'];
	$destino = "../files/fotos/";
	move_uploaded_file($archivo, $destino . "/" . $nombre);

	if ($nombre != "") {
		$consultaRefoto=mysqli_query($conexion, "SELECT aut_foto FROM autorizados WHERE aut_id='" . $_POST["idaut"] . "'");
		$refoto = mysqli_fetch_array($consultaRefoto, MYSQLI_BOTH);
		//@unlink("../../files/fotos/".$refoto["aut_foto"]."");
		$conextra = ", aut_foto='" . $nombre . "'";
	}
	mysqli_query($conexion, "UPDATE autorizados SET aut_documento='" . $_POST["documento"] . "', aut_nombre='" . $_POST["nombre"] . "' " . $conextra . ", aut_estudiante='" . $_POST["estudiante"] . "', aut_estado='" . $_POST["estado"] . "', aut_parentezco='" . $_POST["parentezco"] . "' WHERE aut_id='" . $_POST["idaut"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="usuarios-autorizados.php"</script>';
	exit();
}
//CREAR OPCION GENERALS
if ($_POST["id"] == 46) {

	mysqli_query($conexion, "INSERT INTO opciones_generales (ogen_nombre, ogen_grupo)VALUES('" . $_POST["nombre"] . "','" . $_POST["grupo"] . "')");
	$idIte = mysqli_insert_id($conexion);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="opciones-generales.php"</script>';
	exit();
}
//EDITAR OPCION GENERALS
if ($_POST["id"] == 47) {
	mysqli_query($conexion, "UPDATE opciones_generales SET ogen_nombre='" . $_POST["nombre"] . "', ogen_grupo='" . $_POST["grupo"] . "' WHERE ogen_id='" . $_POST["idogen"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="opciones-generales.php"</script>';
	exit();
}
//CREAR OBSERVACIÓN
if ($_POST["id"] == 48) {
	mysqli_query($conexion, "INSERT INTO observacion_docente (obs_fecha, obs_docente, obs_director, obs_descripcion)VALUES(now(),'" . $_POST["idDoc"] . "','" . $_SESSION["id"] . "','" . $_POST["descripcion"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="observacion-docente.php?idDoc=' . $_POST["idDoc"] . '"</script>';
	exit();
}
//CREAR AUSENCIA
if ($_POST["id"] == 49) {
	mysqli_query($conexion, "INSERT INTO ausencia_docente (aus_fecha, aus_docente, aus_director, aus_fech_ini, aus_fech_fin, aus_motivo, aus_observacion)VALUES(now(),'" . $_POST["idDoc"] . "','" . $_SESSION["id"] . "','" . $_POST["fIni"] . "','" . $_POST["fFin"] . "','" . $_POST["motivo"] . "','" . $_POST["observacion"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="ausencia-docente.php?idDoc=' . $_POST["idDoc"] . '"</script>';
	exit();
}
//GENERAR COBRO MASIVO 
if ($_POST["id"] == 50) {
	if (trim($_POST["grado"]) == "" or trim($_POST["fecha"]) == "" or trim($_POST["detalle"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
			<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
		exit();
	}
	$consultaValor=mysqli_query($conexion, "SELECT * FROM finanzas_cobros_masivos WHERE mas_id='" . $_POST["detalle"] . "'");
	$cValor = mysqli_fetch_array($consultaValor, MYSQLI_BOTH);
	$valor = $cValor[2];
	$detalle = $cValor[1];
	$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='" . $_POST["grado"] . "'");
	while ($datosE = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
		mysqli_query($conexion, "INSERT INTO finanzas_cuentas(fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado, fcu_forma_pago, fcu_cerrado)VALUES('" . $_POST["fecha"] . "','" . $detalle . "','" . $valor . "','" . $_POST["tipo"] . "','" . $_POST["obs"] . "','" . $datosE["mat_id_usuario"] . "',0,5,0)");
		
	}
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//CREAR COBROS MASIVOS
if ($_POST["id"] == 52) {
	mysqli_query($conexion, "INSERT INTO finanzas_cobros_masivos (mas_nombre, mas_valor)VALUES('" . $_POST["nombre"] . "','" . $_POST["costo"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '"</script>';
	exit();
}
//EDITAR COBROS MASIVOS
if ($_POST["id"] == 53) {
	mysqli_query($conexion, "UPDATE finanzas_cobros_masivos SET mas_nombre='" . $_POST["nombre"] . "', mas_valor='" . $_POST["costo"] . "' WHERE mas_id='" . $_POST["idMas"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '"</script>';
	exit();
}
//EDITAR INDICADORES DE LOS DOCENTES
if ($_POST["id"] == 56) {
	include("verificar-carga.php");
	//include("verificar-periodos-diferentes.php");

	$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=0),
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1),
	(SELECT count(*) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1)");
	$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);
	$porcentajePermitido = 100 - $sumaIndicadores[0];
	$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
	$porcentajeRestante = ($porcentajeRestante + $_POST["valorIndicador"]);


	mysqli_query($conexion, "UPDATE academico_indicadores SET ind_nombre='" . $_POST["contenido"] . "' WHERE ind_id='" . $_POST["idInd"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	//Si vamos a relacionar los indicadores con los SABERES
	if ($datosCargaActual['car_saberes_indicador'] == 1) {
		mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_evaluacion='" . $_POST["saberes"] . "' WHERE ipc_id='" . $_POST["idR"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	//Para los DIRECTIVOS los valores de los indicadores son de forma manual
	if (!is_numeric($_POST["valor"])) {
		$_POST["valor"] = 1;
	}
	//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.
	if ($_POST["valor"] > $porcentajeRestante and $porcentajeRestante > 0) {
		$_POST["valor"] = $porcentajeRestante;
	}
	mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='" . $_POST["valor"] . "', ipc_creado='" . $_POST["creado"] . "' WHERE ipc_id='" . $_POST["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");


	echo '<script type="text/javascript">window.location.href="cargas-indicadores.php?carga=' . $_GET["carga"] . '&docente=' . $_GET["docente"] . '";</script>';
	exit();
}
//AGREGAR INDICADORES
if ($_POST["id"] == 57) {
	include("verificar-carga.php");
	//include("verificar-periodos-diferentes.php");
	$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=0),
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1),
	(SELECT count(*) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1)");
	$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$porcentajePermitido = 100 - $sumaIndicadores[0];
	$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

	if ($sumaIndicadores[2] >= $datosCargaActual['car_maximos_indicadores']) {
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=209";</script>';
		exit();
	}



	mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_obligatorio, ind_publico) VALUES('" . $_POST["contenido"] . "', '" . $_POST["creado"] . "', 0)");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$idRegistro = mysqli_insert_id($conexion);
	//Si decide poner los valores porcentuales de los indicadores de forma manual
	if ($datosCargaActual['car_valor_indicador'] == 1) {
		if ($porcentajeRestante <= 0) {
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=210&restante=' . $porcentajeRestante . '";</script>';
			exit();
		}
		if (!is_numeric($_POST["valor"])) {
			$_POST["valor"] = 1;
		}
		//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.
		if ($_POST["valor"] > $porcentajeRestante and $porcentajeRestante > 0) {
			$_POST["valor"] = $porcentajeRestante;
		}
		mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_evaluacion)
			VALUES('" . $cargaConsultaActual . "', '" . $idRegistro . "', '" . $_POST["valor"] . "', '" . $periodoConsultaActual . "', 1, '" . $_POST["saberes"] . "')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}
	//El sistema reparte los porcentajes automáticamente y equitativamente.
	else {
		$valorIgualIndicador = ($porcentajePermitido / ($sumaIndicadores[2] + 1));
		mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_periodo, ipc_creado, ipc_evaluacion)
			VALUES('" . $cargaConsultaActual . "', '" . $idRegistro . "', '" . $periodoConsultaActual . "', 1, '" . $_POST["saberes"] . "')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		//Actualiza todos valores de la misma carga y periodo; incluyendo el que acaba de crear.
		mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='" . $valorIgualIndicador . "' 
			WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}




	//Si las calificaciones son de forma automática.
	if ($datosCargaActual['car_configuracion'] == 0) {
		//Repetimos la consulta de los indicadores porque los valores fueron actualizados
		$indicadoresConsultaActualizado = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
		WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1");
		//Actualizamos todas las actividades por cada indicador
		while ($indicadoresDatos = mysqli_fetch_array($indicadoresConsultaActualizado, MYSQLI_BOTH)) {
			$consultaNumActividades=mysqli_query($conexion, "SELECT * FROM academico_actividades 
			WHERE act_id_tipo='" . $indicadoresDatos['ipc_indicador'] . "' AND act_periodo='" . $periodoConsultaActual . "' AND act_id_carga='" . $cargaConsultaActual . "' AND act_estado=1");
			$actividadesNum = mysqli_num_rows($consultaNumActividades);
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
			//Si hay actividades relacionadas al indicador, actualizamos su valor.
			if ($actividadesNum > 0) {
				$valorIgualActividad = ($indicadoresDatos['ipc_valor'] / $actividadesNum);
				mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='" . $valorIgualActividad . "' 
				WHERE act_id_tipo='" . $indicadoresDatos['ipc_indicador'] . "' AND act_periodo='" . $periodoConsultaActual . "' AND act_id_carga='" . $cargaConsultaActual . "' AND act_estado=1");
				$lineaError = __LINE__;
				include("../compartido/reporte-errores.php");
			}
		}
	}

	echo '<script type="text/javascript">window.location.href="cargas-indicadores.php?carga=' . $cargaConsultaActual . '&periodo=' . $periodoConsultaActual . '&docente=' . $_GET["docente"] . '";</script>';
	exit();
}
//ACTUALIZAR CATEGORÍAS FALTAS
if ($_POST["id"] == 58) {
	mysqli_query($conexion, "UPDATE disciplina_categorias SET dcat_nombre='" . $_POST["categoria"] . "' WHERE dcat_id=" . $_POST["idR"] . ";");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ACTUALIZAR FALTAS
if ($_POST["id"] == 59) {
	mysqli_query($conexion, "UPDATE disciplina_faltas SET dfal_codigo='" . $_POST["codigo"] . "', dfal_nombre='" . $_POST["nombre"] . "', dfal_id_categoria='" . $_POST["categoria"] . "' 
	WHERE dfal_id='" . $_POST["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//AGREGAR FALTAS
if ($_POST["id"] == 60) {
	mysqli_query($conexion, "INSERT INTO disciplina_faltas(dfal_nombre, dfal_id_categoria, dfal_codigo)
			VALUES('" . $_POST["nombre"] . "', '" . $_POST["categoria"] . "', '" . $_POST["codigo"] . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//AGREGAR FALTAS
if ($_POST["id"] == 61) {
	mysqli_query($conexion, "INSERT INTO disciplina_categorias(dcat_nombre)
			VALUES('" . $_POST["categoria"] . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//========================================== GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET  GET GET GET GET GET GET GET GET GET GET GET GET GET ======================

//CAMBIAR DE ESTADO LAS NOTICIAS
if ($_GET["get"] == 1) {
	$consulta = mysqli_query($conexion, "SELECT * FROM social_noticias WHERE not_id='" . $_GET["id"] . "'");
	$resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
	if ($resultado[5] == 0) $estado = 1;
	else $estado = 0;
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado='" . $estado . "' WHERE not_id='" . $_GET["id"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="noticias.php#N' . $_GET["id"] . '";</script>';
	exit();
}
//ELIMINAR NOTICIAS
if ($_GET["get"] == 2) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=2 WHERE not_id='" . $_GET["id"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//MOSTRAR TODAS MIS NOTICIAS
if ($_GET["get"] == 3) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=1 WHERE not_usuario='" . $_SESSION["id"] . "' AND not_estado!=2");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//OCULTAR TODAS MIS NOTICIAS
if ($_GET["get"] == 4) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=0 WHERE not_usuario='" . $_SESSION["id"] . "' AND not_estado!=2");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR TODAS MIS NOTICIAS
if ($_GET["get"] == 5) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=2 WHERE not_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR USUARIOS
if ($_GET["get"] == 6) {
	mysqli_query($conexion, "DELETE FROM usuarios WHERE uss_id='".$_GET["id"]."' AND uss_tipo!=5");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();
}


//ENVIAR SOLICITUD AMISTAD
if ($_GET["get"] == 7) {
	mysqli_query($conexion, "INSERT INTO social_amigos(ams_usuario, ams_amigo, ams_estado, ams_destacado)VALUES('" . $_SESSION["id"] . "', '" . $_GET["usuario"] . "', 0, 0)");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//MOSTRAR TODAS MIS NOTICIAS
if ($_GET["get"] == 8) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=1 WHERE not_estado!=2");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//OCULTAR TODAS MIS NOTICIAS
if ($_GET["get"] == 9) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=0 WHERE not_estado!=2");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR TODAS MIS NOTICIAS
if ($_GET["get"] == 10) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=2");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ANULAR MOVIMIENTO
if ($_GET["get"] == 11) {
	mysqli_query($conexion, "UPDATE finanzas_cuentas SET fcu_anulado=1 WHERE fcu_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="movimientos.php?id=' . $_GET["id"] . '";</script>';
	exit();
}
//ELIMINAR REPORTE
if ($_GET["get"] == 12) {
	mysqli_query($conexion, "DELETE FROM disciplina_reportes WHERE dr_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//DESHABILITAR CURSO
if ($_GET["get"] == 13) {/*
	mysqli_query($conexion, "UPDATE academico_grados SET gra_estado=0 WHERE gra_id='" . $_GET["id"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();*/
}
//DESHABILITAR INGRESO-EGRESO
if ($_GET["get"] == 14) {
	mysqli_query($conexion, "UPDATE finanzas_cuentas SET fcu_anulado=1 WHERE fcu_id='" . $_GET["id"] . "';");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//CANCELAR MATRICULA CONDICIONAL
if ($_GET["get"] == 15) {
	mysqli_query($conexion, "UPDATE disciplina_matricula_condicional SET cond_estado=0 WHERE cond_id=" . $_GET["idMC"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//BLOQUEAR O DESBLOQUEAR UN USUARIO
if ($_GET["get"] == 17) {
	if ($_GET["lock"] == 1) $estado = 0;
	else $estado = 1;
	mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado='" . $estado . "' WHERE uss_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ME GUSTA MURO
if ($_GET["get"] == 19) {
	include("../modelo/conexion-admon.php");
	$consulta = mysqli_query($conexion, "SELECT * FROM social_muro_acciones WHERE mpa_muro='" . $_GET["muro"] . "' AND mpa_usuario='" . $_SESSION["id"] . "' AND mpa_institucion='" . $_GET["i"] . "' AND mpa_accion='" . $_GET["ac"] . "'");
	if ($num = mysqli_num_rows($consulta) == 0) {
		mysqli_query($conexion, "INSERT INTO social_muro_acciones(mpa_muro, mpa_usuario, mpa_fecha, mpa_accion, mpa_institucion)VALUES('" . $_GET["muro"] . "','" . $_SESSION["id"] . "',now(),'" . $_GET["ac"] . "','" . $_GET["i"] . "')");
		
	}
	echo '<script type="text/javascript">window.location.href="social-muro.php#M' . $_GET["muro"] . '";</script>';
	exit();
}
//ELIMINAR RESPUESTAS GENERALES
if ($_GET["get"] == 21) {
	mysqli_query($conexion, "DELETE FROM general_respuestas WHERE resg_id=" . $_GET["idN"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="general-preguntas.php?eva=' . $_GET["eva"] . '";</script>';
	exit();
}
//ELIMINAR PREGUNTAS
if ($_GET["get"] == 22) {
	mysqli_query($conexion, "DELETE FROM general_respuestas WHERE resg_id_pregunta=" . $_GET["idN"] . ";");
	mysqli_query($conexion, "DELETE FROM general_preguntas WHERE pregg_id=" . $_GET["idN"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="general-preguntas.php?eva=' . $_GET["eva"] . '";</script>';
	exit();
}
//ELIMINAR EVALUACIONES
if ($_GET["get"] == 23) {
	mysqli_query($conexion, "DELETE FROM general_preguntas WHERE pregg_id_evaluacion=" . $_GET["idN"] . ";");
	mysqli_query($conexion, "DELETE FROM general_evaluacion_asignar WHERE epag_id_evaluacion=" . $_GET["idN"] . ";");
	mysqli_query($conexion, "DELETE FROM general_evaluaciones WHERE evag_id=" . $_GET["idN"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR EVALUACIONES
if ($_GET["get"] == 24) {
	mysqli_query($conexion, "DELETE FROM general_evaluacion_asignar WHERE epag_id=" . $_GET["idN"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="general-evaluacion.php";</script>';
	exit();
}
//ACTUALIZAR FOTO
if ($_GET["get"] == 25) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_foto='default.png', uss_portada='banner-sintia.png'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_foto='default.png'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//BLOQUEAR TODO
if ($_GET["get"] == 27) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado=1 WHERE uss_tipo!=5 AND uss_tipo!=1");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//DESBLOQUEAR TODO
if ($_GET["get"] == 28) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado=0");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//APAGAR SESION
if ($_GET["get"] == 29) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_estado=0");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//APAGAR SESION
if ($_GET["get"] == 30) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_estado=1");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ACTUALIZAR CLAVE
if ($_GET["get"] == 31) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_clave='sintia1234'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR ACUDIDOS DE LOS ACUDIENTES
if ($_GET["get"] == 34) {
	mysqli_query($conexion, "DELETE FROM usuarios_por_estudiantes WHERE upe_id=" . $_GET["id"] . ";");
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_acudiente=null WHERE mat_id='" . $_GET["est"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR TODOS ACUDIDOS DE LOS ACUDIENTES
if ($_GET["get"] == 35) {
	mysqli_query($conexion, "DELETE FROM usuarios_por_estudiantes WHERE upe_id_usuario=" . $_GET["usr"] . ";");
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_acudiente=null WHERE mat_acudiente='" . $_GET["usr"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//FORMATO 1
if ($_GET["get"] == 38) {
	mysqli_query($conexion, "UPDATE academico_grados SET gra_formato_boletin=1");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//PONER EN 0 LOS VALORES DE LA MATRICULA
if ($_GET["get"] == 39) {
	mysqli_query($conexion, "UPDATE academico_grados SET gra_valor_matricula=0");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//PONER EN 0 LOS VALORES DE LA PENSIÓN
if ($_GET["get"] == 40) {
	mysqli_query($conexion, "UPDATE academico_grados SET gra_valor_pension=0");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR CARGA ACADEMICA
if ($_GET["get"] == 42) {
	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones WHERE eva_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_actividad_foro WHERE foro_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_actividad_foro WHERE foro_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_actividad_preguntas WHERE preg_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_actividad_tareas WHERE tar_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_actividades WHERE act_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_boletin WHERE bol_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_cargas WHERE car_id='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_clases WHERE cls_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_cronograma WHERE cro_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_horarios WHERE hor_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_indicadores_carga WHERE ipc_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_nivelaciones WHERE niv_id_asg='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_pclase WHERE pc_id_carga='" . $_GET["id"] . "'");
	mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_id_carga='" . $_GET["id"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//CIERRE DE CAJA
if ($_GET["get"] == 44) {
	mysqli_query($conexion, "UPDATE finanzas_cuentas SET fcu_cerrado=1, fcu_fecha_cerrado=now(), fcu_cerrado_usuario='" . $_SESSION["id"] . "' WHERE fcu_tipo=1 AND fcu_anulado=0 AND fcu_forma_pago=1 AND fcu_cerrado=0");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR AREAS
if ($_GET["get"] == 46) {
	mysqli_query($conexion, "DELETE FROM academico_areas WHERE ar_id=" . $_GET["id"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR MATERIAS
if ($_GET["get"] == 47) {
	mysqli_query($conexion, "DELETE FROM academico_materias WHERE mat_id=" . $_GET["id"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR USUARIOS AUTORIZADOS
if ($_GET["get"] == 48) {
	mysqli_query($conexion, "DELETE FROM autorizados WHERE aut_id='" . $_GET["idaut"] . "'");
	echo '<script type="text/javascript">window.location.href="usuarios-autorizados.php";</script>';
	exit();
}
//ELIMINAR USUARIOS
if ($_GET["get"] == 49) {
	mysqli_query($conexion, "DELETE FROM usuarios WHERE uss_id='" . $_GET["iduss"] . "'");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR OPCION GENERAL
if ($_GET["get"] == 50) {
	mysqli_query($conexion, "DELETE FROM opciones_generales WHERE ogen_id='" . $_GET["idogen"] . "'");
	echo '<script type="text/javascript">window.location.href="opciones-generales.php";</script>';
	exit();
}
//ELIMINAR AUSENCIA
if ($_GET["get"] == 51) {
	mysqli_query($conexion, "DELETE FROM ausencia_docente WHERE aus_id='" . $_GET["idaus"] . "'");
	echo '<script type="text/javascript">window.location.href="ausencia-docente.php?idDoc=' . $_GET["idDoc"] . '";</script>';
	exit();
}
//ELIMINAR OBSERVACIONES
if ($_GET["get"] == 52) {
	mysqli_query($conexion, "DELETE FROM observacion_docente WHERE obs_id='" . $_GET["idobs"] . "'");
	echo '<script type="text/javascript">window.location.href="observacion-docente.php?idDoc=' . $_GET["idDoc"] . '";</script>';
	exit();
}
//ELIMINAR MOVIMIENTOS FINANCIEROS
if ($_GET["get"] == 53) {
	mysqli_query($conexion, "DELETE FROM finanzas_cuentas");
	echo '<script type="text/javascript">window.location.href="finanzas-movimientos-lista.php";</script>';
	exit();
}
//PROMOCIONAR ESTUDIANTES
if ($_GET["get"] == 54) {
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_promocionado=0");
	$grados = mysqli_query($conexion, "SELECT * FROM academico_grados");
	while ($g = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
		if ($g[7] != "") {
			mysqli_query($conexion, "UPDATE academico_matriculas SET mat_grado='" . $g[7] . "', mat_promocionado=1 WHERE mat_grado='" . $g[0] . "' AND mat_promocionado=0 AND mat_eliminado=0");
		}
	}
	echo '<script type="text/javascript">window.location.href="estudiantes.php";</script>';
	exit();
}
//ELIMINAR COBRO MASIVO
if ($_GET["get"] == 55) {
	mysqli_query($conexion, "DELETE FROM finanzas_cobros_masivos WHERE mas_id='" . $_GET["idMas"] . "'");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR NOTA NIVELACION
if ($_GET["get"] == 57) {
	mysqli_query($conexion, "DELETE FROM academico_nivelaciones WHERE niv_id='" . $_GET["idNiv"] . "'");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR TODOS LOS ACUDIENTES
if ($_GET["get"] == 59) {
	mysqli_query($conexion, "DELETE FROM usuarios WHERE uss_tipo=3");
	mysqli_query($conexion, "DELETE FROM usuarios_por_estudiantes");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//DEVOLVER ESTUDIANTES
if ($_GET["get"] == 64) {
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_promocionado=0");
	$grados = mysqli_query($conexion, "SELECT * FROM academico_grados");
	while ($g = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
		if ($g[10] != "") {
			mysqli_query($conexion, "UPDATE academico_matriculas SET mat_grado='" . $g[10] . "', mat_promocionado=0 WHERE mat_grado='" . $g[0] . "' AND mat_eliminado=0");
		}
	}
	echo '<script type="text/javascript">window.location.href="estudiantes.php";</script>';
	exit();
}
//REACCIONES POR NOTICIA
if ($_GET["get"] == 67) {
	$consultaReaccion=mysqli_query($conexion, "SELECT * FROM social_noticias_reacciones WHERE npr_usuario='" . $_SESSION["id"] . "' AND npr_noticia='" . $_GET["post"] . "'");
	$reaccion = mysqli_fetch_array($consultaReaccion, MYSQLI_BOTH);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	if ($reaccion[0] == "") {
		mysqli_query($conexion, "INSERT INTO social_noticias_reacciones(npr_usuario, npr_noticia, npr_reaccion, npr_fecha, npr_estado)VALUES('" . $_SESSION["id"] . "', '" . $_GET["post"] . "','" . $_GET["r"] . "',now(),1)");
		
	} else {
		mysqli_query($conexion, "UPDATE social_noticias_reacciones SET npr_reaccion='" . $_GET["r"] . "' WHERE npr_usuario='" . $_SESSION["id"] . "' AND npr_noticia='" . $_GET["post"] . "'");
		
	}
	echo '<script type="text/javascript">window.location.href="noticias.php";</script>';
	exit();
}
//ELIMINAR INDICADORES DE LOS DOCENTES
if ($_GET["get"] == 68) {

	include("verificar-carga.php");
	//include("verificar-periodos-diferentes.php");


	$actividadesRelacionadasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividades 
	WHERE act_id_tipo='" . $_GET["idIndicador"] . "' AND act_id_carga='" . $_GET["carga"] . "' AND act_periodo='" . $_GET["periodo"] . "' AND act_estado=1");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	while ($actividadesRelacionadasDatos = mysqli_fetch_array($actividadesRelacionadasConsulta, MYSQLI_BOTH)) {

		mysqli_query($conexion, "UPDATE academico_actividades SET act_estado=0, act_fecha_eliminacion=now(), act_motivo_eliminacion='DIRECTIVO " . $_SESSION["id"] . ": Eliminar indicadores de carga: " . $cargaConsultaActual . ", del P: " . $periodoConsultaActual . "' WHERE act_id='" . $actividadesRelacionadasDatos['act_id'] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	mysqli_query($conexion, "DELETE FROM academico_indicadores_carga WHERE ipc_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=0),
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1),
	(SELECT count(*) FROM academico_indicadores_carga 
	WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1)");
	$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);
	$porcentajePermitido = 100 - $sumaIndicadores[0];
	$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

	//Si decide poner los valores porcentuales de los indicadores de forma manual
	if ($datosCargaActual['car_valor_indicador'] == 1) {
	}
	//El sistema reparte los porcentajes automáticamente y equitativamente.
	else {
		$valorIgualIndicador = ($porcentajePermitido / ($sumaIndicadores[2]));
		//Actualiza todos valores de la misma carga y periodo.
		mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='" . $valorIgualIndicador . "' 
		WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");

		//Si decide que los valores de las calificaciones son de forma automática.
		if ($datosCargaActual['car_configuracion'] == 0) {
			//Repetimos la consulta de los indicadores porque los valores fueron actualizados
			$indicadoresConsultaActualizado = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
			WHERE ipc_carga='" . $cargaConsultaActual . "' AND ipc_periodo='" . $periodoConsultaActual . "' AND ipc_creado=1");
			//Actualizamos todas las actividades por cada indicador
			while ($indicadoresDatos = mysqli_fetch_array($indicadoresConsultaActualizado, MYSQLI_BOTH)) {
				$consultaNumActividades=mysqli_query($conexion, "SELECT * FROM academico_actividades 
				WHERE act_id_tipo='" . $indicadoresDatos['ipc_indicador'] . "' AND act_periodo='" . $periodoConsultaActual . "' AND act_id_carga='" . $cargaConsultaActual . "' AND act_estado=1");
				$actividadesNum = mysqli_num_rows($consultaNumActividades);
				$lineaError = __LINE__;
				include("../compartido/reporte-errores.php");
				//Si hay actividades relacionadas al indicador, actualizamos su valor.
				if ($actividadesNum > 0) {
					$valorIgualActividad = ($indicadoresDatos['ipc_valor'] / $actividadesNum);
					mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='" . $valorIgualActividad . "' 
					WHERE act_id_tipo='" . $indicadoresDatos['ipc_indicador'] . "' AND act_periodo='" . $periodoConsultaActual . "' AND act_id_carga='" . $cargaConsultaActual . "' AND act_estado=1");
					$lineaError = __LINE__;
					include("../compartido/reporte-errores.php");
				}
			}
		}
	}

	echo '<script type="text/javascript">window.location.href="cargas-indicadores.php?carga=' . $_GET["carga"] . '&docente=' . $_GET["docente"] . '";</script>';
	exit();
}
//BLOQUEAR ESTUDIANTES
if ($_GET["get"] == 69) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado=1 WHERE uss_tipo=4");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="usuarios.php?tipo=4&cantidad=50";</script>';
	exit();
}
//DESBLOQUEAR ESTUDIANTES
if ($_GET["get"] == 70) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado=0 WHERE uss_tipo=4");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="usuarios.php?tipo=4&cantidad=50";</script>';
	exit();
}
//ENCUESTA RESERVA DE CUPO
if($_GET["get"]==71){
	//echo $_GET["idEstudiante"]; exit();

	mysqli_query($conexion, "INSERT INTO general_encuestas(genc_estudiante, genc_fecha, genc_respuesta, genc_comentario)
	VALUES('".$_GET["idEstudiante"]."', now(), 1, 'Reservado por un directivo.')");
	
	
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();
}
?>