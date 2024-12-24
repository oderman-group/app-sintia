<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

$configAdmisiones=Inscripciones::configuracionAdmisiones($conexion,$baseDatosAdmisiones,$config['conf_id_institucion'],$_SESSION["bd"]);

if (!empty($configAdmisiones["cfgi_year_inscripcion"]) && $configAdmisiones["cfgi_year_inscripcion"] != $yearEnd) {
	echo '<script type="text/javascript">window.location.href="inscripciones.php?error=ER_DT_18&yearPasar='.base64_encode($configAdmisiones["cfgi_year_inscripcion"]).'";</script>';
	exit;
}

$year=$agnoBD;
$yearPasar=$agnoBD+1;

$matricula="";

if (!empty($_GET["matricula"])) { 
	$matricula = base64_decode($_GET["matricula"]);
}

$existe = Estudiantes::validarExistenciaEstudiante($matricula, $bdApasar, $yearPasar);

if ($existe > 0) {
	echo '<script type="text/javascript">window.location.href="inscripciones.php?error=ER_DT_19&yearPasar='.base64_encode($configAdmisiones["cfgi_year_inscripcion"]).'";</script>';
	exit;
}

	//SE CREA MATRICULA EN AÑO SIGUIENTE
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, mat_uso_sintia, mat_inicio, mat_meses, mat_fin, mat_folio, mat_codigo_tesoreria, mat_valor_matricula, mat_inclusion, mat_promocionado, mat_extranjero, mat_numero_matricula, mat_compromiso, mat_acudiente2, mat_institucion_procedencia, mat_estado_agno, mat_salon, mat_notificacion1, mat_acudiente_principal, mat_padre, mat_madre, mat_lugar_colegio_procedencia, mat_razon_ingreso_plantel, mat_motivo_retiro_anterior, mat_ciudad_actual, mat_solicitud_inscripcion, mat_tipo_sangre, mat_con_quien_vive, mat_quien_otro, mat_iniciar_proceso, mat_actualizar_datos, mat_pago_matricula, mat_contrato, mat_compromiso_academico, mat_manual, mat_mayores14, mat_hoja_firma, mat_soporte_pago, mat_firma_adjunta, mat_compromiso_convivencia, mat_compromiso_convivencia_opcion, mat_pagare, mat_modalidad_estudio, mat_informe_parcial, mat_informe_parcial_fecha, mat_eps, mat_celular2, mat_ciudad_residencia, mat_nombre2, mat_ciudad_recidencia, mat_tipo_matricula, institucion, year, mat_forma_creacion) 
		SELECT mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, mat_uso_sintia, mat_inicio, mat_meses, mat_fin, mat_folio, mat_codigo_tesoreria, mat_valor_matricula, mat_inclusion, mat_promocionado, mat_extranjero, mat_numero_matricula, mat_compromiso, mat_acudiente2, mat_institucion_procedencia, mat_estado_agno, mat_salon, mat_notificacion1, mat_acudiente_principal, mat_padre, mat_madre, mat_lugar_colegio_procedencia, mat_razon_ingreso_plantel, mat_motivo_retiro_anterior, mat_ciudad_actual, mat_solicitud_inscripcion, mat_tipo_sangre, mat_con_quien_vive, mat_quien_otro, mat_iniciar_proceso, mat_actualizar_datos, mat_pago_matricula, mat_contrato, mat_compromiso_academico, mat_manual, mat_mayores14, mat_hoja_firma, mat_soporte_pago, mat_firma_adjunta, mat_compromiso_convivencia, mat_compromiso_convivencia_opcion, mat_pagare, mat_modalidad_estudio, mat_informe_parcial, mat_informe_parcial_fecha, mat_eps, mat_celular2, mat_ciudad_residencia, mat_nombre2, mat_ciudad_recidencia, mat_tipo_matricula, institucion, {$yearPasar}, '".Estudiantes::MOVIDO."' 
		FROM ".BD_ACADEMICA.".academico_matriculas 
		WHERE 
			mat_id='".$matricula."' 
		AND institucion={$config['conf_id_institucion']} 
		AND year={$year}
		");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
	$idNuevo = mysqli_insert_id($conexion);

	$update = [
		'mat_estado_matricula' => 4, 
		'mat_grupo' => 1
	];
	Estudiantes::actualizarMatriculasPorId($config, $matricula, $update, $yearPasar);

	//CONSULTAMOS DATOS DEL ESTUDIANTE
	$datosMatricula = Estudiantes::obtenerDatosEstudiante($matricula, $year);

	//SE CREA EL USUARIO DEL ESTUDIANTE
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year) 
		SELECT uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, {$yearPasar} FROM ".BD_GENERAL.".usuarios WHERE uss_id='".$datosMatricula["mat_id_usuario"]."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	//SE CREA EL USUARIO DEL ACUDIENTE
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year) 
		SELECT uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, {$yearPasar} FROM ".BD_GENERAL.".usuarios WHERE uss_id='".$datosMatricula["mat_acudiente"]."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	//SE CREA EL USUARIO DEL PADRE
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year) 
		SELECT uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, {$yearPasar} FROM ".BD_GENERAL.".usuarios WHERE uss_id='".$datosMatricula["mat_padre"]."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	//SE CREA EL USUARIO DE LA MADRE
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, year) 
		SELECT uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_lugar_expedicion, uss_direccion, uss_estado_civil, uss_preguntar_animo, uss_mostrar_mensajes, uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_sitio_web_negocio, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda, uss_medio_transporte, uss_tema_sidebar, uss_tema_header, uss_tema_logo, uss_tipo_menu, uss_notificacion, uss_mostrar_edad, uss_ultima_actualizacion, uss_version1_menu, uss_solicitar_datos, uss_institucion, uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, uss_tipo_documento, uss_empresa_labor, uss_firma, uss_apellido1, uss_apellido2, uss_nombre2, uss_documento, institucion, {$yearPasar} FROM ".BD_GENERAL.".usuarios WHERE uss_id='".$datosMatricula["mat_madre"]."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	//SE CREA RELACION ENTRE ACUDIENTE Y ESTUDIANTE
	$codigo=Utilidades::generateCode("UPE");
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante, institucion, year)VALUES('".$codigo."','".$datosMatricula["mat_acudiente"]."', '".$datosMatricula["mat_id_usuario"]."', {$config['conf_id_institucion']}, {$yearPasar})");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	//SE ACTUALIZA EL ESTADO DEL ASPIRANTE
	try{
		mysqli_query($conexion, "UPDATE ".BD_ADMISIONES.".aspirantes SET asp_estado_solicitud=9 WHERE asp_id='".$datosMatricula["mat_solicitud_inscripcion"]."'");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	echo '<script type="text/javascript">window.location.href="inscripciones.php?success=SC_DT_14&yearPasar='.base64_encode($configAdmisiones["cfgi_year_inscripcion"]).'";</script>';
	exit();