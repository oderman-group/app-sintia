<?php
date_default_timezone_set("America/Bogota");
include("session-compartida.php");
$idPaginaInterna = 'DT0225';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");
$Plataforma = new Plataforma;

// Clave para almacenar preferencias del certificado
define('CERTIFICADO_PREFS_KEY', 'certificado_preferencias_usuario_');

// Configuraciones para manejo de archivos grandes
set_time_limit(300);
ini_set('memory_limit', '256M');

$id="";
if(isset($_REQUEST["id"])){$id=base64_decode($_REQUEST["id"]);}
$desde="";
if(isset($_REQUEST["desde"])){$desde=base64_decode($_REQUEST["desde"]);}
$hasta="";
if(isset($_REQUEST["hasta"])){$hasta=base64_decode($_REQUEST["hasta"]);}
$estampilla="";
if(isset($_REQUEST["estampilla"])){$estampilla=base64_decode($_REQUEST["estampilla"]);}

// Opción para mostrar encabezado (por defecto true, para papel membrete usar false)
$mostrarEncabezado = true;
if(isset($_REQUEST["sin_encabezado"])){
    $sinEncabezadoDecoded = base64_decode($_REQUEST["sin_encabezado"]);
    if($sinEncabezadoDecoded == "1"){
        $mostrarEncabezado = false;
    }
}

// Detectar si el formulario de configuración fue enviado
$formularioEnviado = isset($_GET['config_aplicada']) && $_GET['config_aplicada'] == '1';

/**
 * Cargar preferencias del certificado desde Redis o sesión
 * @return array Array con las preferencias guardadas
 */
function cargarPreferenciasCertificado() {
	$preferencias = [];
	$usuarioId = isset($GLOBALS['datosUsuarioActual']['uss_id']) ? $GLOBALS['datosUsuarioActual']['uss_id'] : 0;
	
	if ($usuarioId > 0) {
		// Intentar cargar desde Redis
		try {
			$redis = RedisInstance::getRedisInstance();
			$redisKey = CERTIFICADO_PREFS_KEY . $usuarioId;
			
			if ($redis->exists($redisKey)) {
				$prefsRedis = json_decode($redis->get($redisKey), true);
				if (is_array($prefsRedis)) {
					$preferencias = $prefsRedis;
				}
			}
		} catch (Exception $e) {
			// Si Redis falla, continuar con sesión
		}
	}
	
	// Si no hay en Redis, intentar desde sesión
	if (empty($preferencias) && isset($_SESSION['certificado_preferencias'])) {
		$preferencias = $_SESSION['certificado_preferencias'];
	}
	
	return $preferencias;
}

/**
 * Guardar preferencias del certificado en Redis y sesión
 * @param array $preferencias Array con las preferencias a guardar
 */
function guardarPreferenciasCertificado($preferencias) {
	$usuarioId = isset($GLOBALS['datosUsuarioActual']['uss_id']) ? $GLOBALS['datosUsuarioActual']['uss_id'] : 0;
	
	// Guardar en sesión (siempre disponible)
	$_SESSION['certificado_preferencias'] = $preferencias;
	
	// Guardar en Redis (persistencia entre sesiones)
	if ($usuarioId > 0) {
		try {
			$redis = RedisInstance::getRedisInstance();
			$redisKey = CERTIFICADO_PREFS_KEY . $usuarioId;
			
			// Guardar en Redis con expiración de 90 días
			$redis->setex($redisKey, 90 * 24 * 60 * 60, json_encode($preferencias));
		} catch (Exception $e) {
			// Si Redis falla, al menos tenemos la sesión
		}
	}
}

// Cargar preferencias guardadas (Redis o sesión)
$preferenciasGuardadas = cargarPreferenciasCertificado();

// Configuraciones de personalización
// Si el formulario fue enviado, usar valores del formulario y guardarlos
// Si no, intentar cargar desde preferencias guardadas, sino usar valores por defecto
if ($formularioEnviado) {
	// Valores del formulario
	// Para checkboxes: si no están presentes en GET, significa que están desmarcados (0)
	$tipoLetra = isset($_GET['tipo_letra']) ? $_GET['tipo_letra'] : ($preferenciasGuardadas['tipo_letra'] ?? 'Arial');
	$tamanoLetra = isset($_GET['tamano_letra']) ? (int)$_GET['tamano_letra'] : ($preferenciasGuardadas['tamano_letra'] ?? 11);
	$mostrarMaterias = isset($_GET['mostrar_materias']) ? (int)$_GET['mostrar_materias'] : 0;
	$incluirLogo = isset($_GET['incluir_logo']) ? (int)$_GET['incluir_logo'] : 0;
	$logoAncho = isset($_GET['logo_ancho']) ? (int)$_GET['logo_ancho'] : ($preferenciasGuardadas['logo_ancho'] ?? 150);
	$logoAlto = isset($_GET['logo_alto']) ? (int)$_GET['logo_alto'] : ($preferenciasGuardadas['logo_alto'] ?? 100);
	$logoAlineacion = isset($_GET['logo_alineacion']) ? $_GET['logo_alineacion'] : ($preferenciasGuardadas['logo_alineacion'] ?? 'centro');
	$firmaRector = isset($_GET['firma_rector']) ? (int)$_GET['firma_rector'] : 0;
	$firmaSecretario = isset($_GET['firma_secretario']) ? (int)$_GET['firma_secretario'] : 0;
	// Para checkboxes: si no están presentes en GET, significa que están desmarcados (0)
	$mostrarFirmaDigitalRector = isset($_GET['mostrar_firma_digital_rector']) ? (int)$_GET['mostrar_firma_digital_rector'] : 0;
	$mostrarFirmaDigitalSecretario = isset($_GET['mostrar_firma_digital_secretario']) ? (int)$_GET['mostrar_firma_digital_secretario'] : 0;
	$posicionFirmaRector = isset($_GET['posicion_firma_rector']) ? (int)$_GET['posicion_firma_rector'] : ($preferenciasGuardadas['posicion_firma_rector'] ?? 10);
	$posicionFirmaSecretario = isset($_GET['posicion_firma_secretario']) ? (int)$_GET['posicion_firma_secretario'] : ($preferenciasGuardadas['posicion_firma_secretario'] ?? 10);
	// Para checkboxes: si no están presentes en GET, significa que están desmarcados (0)
	$espaciadoTabla = isset($_GET['espaciado_tabla']) ? (int)$_GET['espaciado_tabla'] : ($preferenciasGuardadas['espaciado_tabla'] ?? 8);
	$mostrarMensajePromocion = isset($_GET['mostrar_mensaje_promocion']) ? (int)$_GET['mostrar_mensaje_promocion'] : 0;
	$consolidarTextoAnios = isset($_GET['consolidar_texto_anios']) ? (int)$_GET['consolidar_texto_anios'] : 0;
	$mostrarMarcaAgua = isset($_GET['mostrar_marca_agua']) ? (int)$_GET['mostrar_marca_agua'] : 0;
	$marcaAguaOpacidad = isset($_GET['marca_agua_opacidad']) ? (float)$_GET['marca_agua_opacidad'] : ($preferenciasGuardadas['marca_agua_opacidad'] ?? 0.1);
	$marcaAguaTamanio = isset($_GET['marca_agua_tamanio']) ? (int)$_GET['marca_agua_tamanio'] : ($preferenciasGuardadas['marca_agua_tamanio'] ?? 300);
	$espacioCertificado = isset($_GET['espacio_certificado']) ? (int)$_GET['espacio_certificado'] : ($preferenciasGuardadas['espacio_certificado'] ?? 30);
	$interlineado = isset($_GET['interlineado']) ? (float)$_GET['interlineado'] : ($preferenciasGuardadas['interlineado'] ?? 1.6);
	
	// Guardar preferencias
	$preferenciasParaGuardar = [
		'tipo_letra' => $tipoLetra,
		'tamano_letra' => $tamanoLetra,
		'mostrar_materias' => $mostrarMaterias,
		'incluir_logo' => $incluirLogo,
		'logo_ancho' => $logoAncho,
		'logo_alto' => $logoAlto,
		'logo_alineacion' => $logoAlineacion,
		'firma_rector' => $firmaRector,
		'firma_secretario' => $firmaSecretario,
		'mostrar_firma_digital_rector' => $mostrarFirmaDigitalRector,
		'mostrar_firma_digital_secretario' => $mostrarFirmaDigitalSecretario,
		'posicion_firma_rector' => $posicionFirmaRector,
		'posicion_firma_secretario' => $posicionFirmaSecretario,
		'espaciado_tabla' => $espaciadoTabla,
		'mostrar_mensaje_promocion' => $mostrarMensajePromocion,
		'consolidar_texto_anios' => $consolidarTextoAnios,
		'mostrar_marca_agua' => $mostrarMarcaAgua,
		'marca_agua_opacidad' => $marcaAguaOpacidad,
		'marca_agua_tamanio' => $marcaAguaTamanio,
		'espacio_certificado' => $espacioCertificado,
		'interlineado' => $interlineado,
	];
	guardarPreferenciasCertificado($preferenciasParaGuardar);
} else {
	// Cargar desde preferencias guardadas o usar valores por defecto
	$tipoLetra = $preferenciasGuardadas['tipo_letra'] ?? 'Arial';
	$tamanoLetra = $preferenciasGuardadas['tamano_letra'] ?? 11;
	$mostrarMaterias = $preferenciasGuardadas['mostrar_materias'] ?? 1;
	$incluirLogo = $preferenciasGuardadas['incluir_logo'] ?? 0;
	$logoAncho = $preferenciasGuardadas['logo_ancho'] ?? 150;
	$logoAlto = $preferenciasGuardadas['logo_alto'] ?? 100;
	$logoAlineacion = $preferenciasGuardadas['logo_alineacion'] ?? 'centro';
	$firmaRector = $preferenciasGuardadas['firma_rector'] ?? (!empty($informacion_inst["info_rector"]) ? 1 : 0);
	$firmaSecretario = $preferenciasGuardadas['firma_secretario'] ?? (!empty($informacion_inst["info_secretaria_academica"]) ? 1 : 0);
	$mostrarFirmaDigitalRector = $preferenciasGuardadas['mostrar_firma_digital_rector'] ?? 0;
	$mostrarFirmaDigitalSecretario = $preferenciasGuardadas['mostrar_firma_digital_secretario'] ?? 0;
	$posicionFirmaRector = $preferenciasGuardadas['posicion_firma_rector'] ?? 10;
	$posicionFirmaSecretario = $preferenciasGuardadas['posicion_firma_secretario'] ?? 10;
	$espaciadoTabla = $preferenciasGuardadas['espaciado_tabla'] ?? 8;
	$mostrarMensajePromocion = $preferenciasGuardadas['mostrar_mensaje_promocion'] ?? 1;
	$consolidarTextoAnios = $preferenciasGuardadas['consolidar_texto_anios'] ?? 0;
	$mostrarMarcaAgua = $preferenciasGuardadas['mostrar_marca_agua'] ?? 0;
	$marcaAguaOpacidad = $preferenciasGuardadas['marca_agua_opacidad'] ?? 0.1;
	$marcaAguaTamanio = $preferenciasGuardadas['marca_agua_tamanio'] ?? 300;
	$espacioCertificado = $preferenciasGuardadas['espacio_certificado'] ?? 30;
	$interlineado = $preferenciasGuardadas['interlineado'] ?? 1.6;
}

// Optimización: Cachear tipos de notas para evitar consultas repetidas
$notasCualitativasCache = [];

// Obtener nombre de la ciudad desde el código (info_ciudad ahora guarda el código)
if (!empty($informacion_inst["info_ciudad"]) && is_numeric($informacion_inst["info_ciudad"])) {
	$consultaCiudad = mysqli_query($conexion, "SELECT ciu_nombre, dep_nombre 
		FROM ".BD_ADMIN.".localidad_ciudades 
		INNER JOIN ".BD_ADMIN.".localidad_departamentos ON dep_id = ciu_departamento 
		WHERE ciu_id = " . intval($informacion_inst["info_ciudad"]) . " 
		LIMIT 1");
	if ($consultaCiudad && mysqli_num_rows($consultaCiudad) > 0) {
		$datosCiudad = mysqli_fetch_array($consultaCiudad, MYSQLI_BOTH);
		$informacion_inst["ciu_nombre"] = $datosCiudad["ciu_nombre"];
		$informacion_inst["dep_nombre"] = $datosCiudad["dep_nombre"];
	}
}

// Cargar tipos de notas para usar en determinarRango
$tiposNotas = [];
?>
<!doctype html>
<html class="no-js" lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<title>Certificado de Estudios - SINTIA</title>
	
	<style>
		/* ============================
		   ESTILOS GENERALES
		   ============================ */
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: <?= htmlspecialchars($tipoLetra) ?>, 'Arial', 'Times New Roman', serif;
			font-size: <?= $tamanoLetra ?>pt;
			line-height: <?= $interlineado ?>;
			color: #000;
			background-color: #fff;
			padding: 20px;
			position: relative;
		}

		.container-certificado {
			max-width: 850px;
			margin: 0 auto;
			background: transparent;
			padding: <?= $espacioCertificado ?>px;
			position: relative;
		}

		/* ============================
		   VENTANA DE CONFIGURACIÓN
		   ============================ */
		.config-certificado-form {
			position: fixed;
			top: 10px;
			right: 10px;
			background: #ffffff;
			border: 2px solid #2c3e50;
			border-radius: 8px;
			padding: 20px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			z-index: 1000;
			font-size: 12px;
			min-width: 300px;
			max-width: 400px;
			max-height: 90vh;
			overflow-y: auto;
		}

		.config-certificado-form h4 {
			margin: 0 0 15px 0;
			color: #2c3e50;
			font-size: 16px;
			font-weight: 600;
			border-bottom: 2px solid #2c3e50;
			padding-bottom: 10px;
		}

		.config-certificado-form .form-group {
			margin-bottom: 15px;
		}

		.config-certificado-form label {
			display: block;
			margin-bottom: 5px;
			font-weight: 600;
			color: #2c3e50;
			font-size: 12px;
		}

		.config-certificado-form input[type="text"],
		.config-certificado-form input[type="number"],
		.config-certificado-form select {
			width: 100%;
			padding: 6px 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 12px;
		}

		.config-certificado-form input[type="checkbox"] {
			margin-right: 8px;
			cursor: pointer;
		}

		.config-certificado-form .checkbox-label {
			display: flex;
			align-items: center;
			margin-bottom: 8px;
			cursor: pointer;
			font-weight: normal;
		}

		.config-certificado-form .btn-aplicar {
			margin-top: 15px;
			padding: 10px 20px;
			background: #2c3e50;
			color: #ffffff;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 13px;
			width: 100%;
			font-weight: 600;
		}

		.config-certificado-form .btn-aplicar:hover {
			background: #34495e;
		}

		.config-certificado-form .logo-dimensions {
			display: flex;
			gap: 10px;
		}

		.config-certificado-form .logo-dimensions input {
			flex: 1;
		}

		/* Ocultar formulario de configuración en impresión */
		@media print {
			.config-certificado-form {
				display: none !important;
			}
		}

		/* ============================
		   BOTONES DE ACCIÓN
		   ============================ */
		.botones-accion {
			position: fixed;
			bottom: 30px;
			right: 30px;
			z-index: 999;
			display: flex;
			flex-direction: column;
			gap: 10px;
		}

		.btn-flotante {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			padding: 10px 20px;
			border: 1px solid #999;
			border-radius: 4px;
			font-size: 13px;
			font-weight: 500;
			cursor: pointer;
			transition: all 0.2s ease;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			min-width: 140px;
			text-decoration: none;
			background: white;
			color: #333;
		}

		.btn-print {
			border-color: #2c3e50;
			color: #2c3e50;
		}

		.btn-print:hover {
			background: #2c3e50;
			color: white;
		}

		.btn-close {
			border-color: #7f8c8d;
			color: #7f8c8d;
		}

		.btn-close:hover {
			background: #7f8c8d;
			color: white;
		}

		/* ============================
		   LOGO
		   ============================ */
		.logo-container {
			margin: 20px 0;
			text-align: <?= $logoAlineacion == 'izquierda' ? 'left' : ($logoAlineacion == 'derecha' ? 'right' : 'center') ?>;
		}

		.logo-container img {
			width: <?= $logoAncho ?>px;
			height: <?= $logoAlto ?>px;
			object-fit: contain;
		}

		/* ============================
		   MARCA DE AGUA
		   ============================ */
		.marca-agua {
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			z-index: 1;
			pointer-events: none;
			opacity: <?= $marcaAguaOpacidad ?>;
		}

		.marca-agua img {
			width: <?= $marcaAguaTamanio ?>px;
			height: auto;
			object-fit: contain;
		}

		.container-certificado > * {
			position: relative;
			z-index: 10;
		}
		
		.container-certificado .header-institucional,
		.container-certificado .titulo-certificado,
		.container-certificado .texto-estudiante,
		.container-certificado .tabla-calificaciones {
			background: transparent;
		}
		
		.container-certificado .firmas-container {
			background: white;
		}

		/* ============================
		   ENCABEZADO
		   ============================ */
		.header-institucional {
			text-align: justify;
			margin: 40px 0 30px 0;
			line-height: <?= $interlineado ?>;
			font-size: <?= $tamanoLetra ?>pt;
			background: white;
			position: relative;
			z-index: 10;
		}

		.texto-centrado {
			text-align: center;
			font-weight: bold;
			font-size: <?= $tamanoLetra + 3 ?>pt;
			margin: 25px 0;
			letter-spacing: 2px;
			background: white;
			position: relative;
			z-index: 10;
		}

		.texto-estudiante {
			text-align: justify;
			margin: 20px 0;
			line-height: <?= $interlineado ?>;
			font-size: <?= $tamanoLetra ?>pt;
			background: white;
			position: relative;
			z-index: 10;
		}

		/* ============================
		   TABLAS
		   ============================ */
		.titulo-grado {
			text-align: left;
			font-weight: bold;
			font-size: <?= $tamanoLetra ?>pt;
			margin: 20px 0 10px 0;
		}

		.tabla-calificaciones {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
			font-size: <?= $tamanoLetra ?>pt;
			position: relative;
			z-index: 10;
			background: transparent;
		}

		.tabla-calificaciones th,
		.tabla-calificaciones td {
			border: 1px solid #000;
			padding: <?= $espaciadoTabla ?>px <?= $espaciadoTabla + 4 ?>px;
		}

		.tabla-calificaciones th {
			background-color: rgba(233, 236, 239, 0.85);
			font-weight: bold;
			text-align: center;
			font-size: <?= $tamanoLetra ?>pt;
		}

		.tabla-calificaciones td {
			vertical-align: middle;
			background-color: rgba(255, 255, 255, 0.85);
		}

		.tabla-calificaciones tr.fila-area {
			background-color: rgba(234, 234, 234, 0.85);
			font-weight: normal;
		}

		.tabla-calificaciones tr.fila-materia {
			background-color: rgba(255, 255, 255, 0.85);
		}

		.tabla-calificaciones tfoot td {
			background-color: rgba(248, 249, 250, 0.85);
			padding: 15px;
			font-size: <?= $tamanoLetra - 1 ?>pt;
			line-height: <?= $interlineado ?>;
		}

		.tabla-calificaciones tfoot mark {
			background-color: rgba(255, 243, 205, 0.9);
			padding: 2px 5px;
			border-radius: 3px;
		}

		/* ============================
		   MENSAJES DE PROMOCIÓN
		   ============================ */
		.mensaje-promocion {
			text-align: center;
			font-weight: bold;
			font-style: italic;
			font-size: <?= $tamanoLetra ?>pt;
			margin: 20px 0;
			padding: 15px;
			border: 1px solid #dee2e6;
			background: #f8f9fa;
		}

		.mensaje-promovido {
			border-left: 4px solid #27ae60;
			background: #d4edda;
		}

		.mensaje-no-promovido {
			border-left: 4px solid #e74c3c;
			background: #f8d7da;
		}

		.mensaje-retirado {
			border-left: 4px solid #f39c12;
			background: #fff3cd;
		}

		/* ============================
		   SECCIÓN DE NIVELACIONES
		   ============================ */
		.seccion-nivelaciones {
			margin: 20px 0;
			padding: 15px;
			background: #fff8e1;
			border: 1px solid #ffc107;
			border-radius: 4px;
		}

		.seccion-nivelaciones p {
			margin-bottom: 8px;
			line-height: 1.6;
		}

		/* ============================
		   PIE DEL CERTIFICADO
		   ============================ */
		.pie-certificado {
			font-size: <?= $tamanoLetra ?>pt;
			text-align: justify;
			line-height: 1.8;
			margin: 25px 0;
		}

		/* ============================
		   FIRMAS
		   ============================ */
		.tabla-firmas {
			width: 100%;
			border-collapse: collapse;
			margin-top: 40px;
		}

		.tabla-firmas td {
			text-align: center;
			vertical-align: bottom;
			padding: 10px 20px;
		}

		.firma-linea {
			border-top: 1px solid #000;
			width: 60%;
			margin: 50px auto 5px auto;
		}

		.firma-imagen {
			max-width: 100px;
			height: auto;
			display: block;
			margin-left: auto;
			margin-right: auto;
		}

		.firma-nombre {
			font-weight: bold;
			font-size: <?= $tamanoLetra - 1 ?>pt;
			margin-top: 5px;
		}

		.firma-cargo {
			font-size: <?= $tamanoLetra - 2 ?>pt;
			color: #555;
		}

		/* ============================
		   ESTILOS DE IMPRESIÓN
		   ============================ */
		@media print {
			@page {
				size: letter;
				margin: 1.5cm;
			}

			body {
				background-color: white;
				padding: 0;
				font-family: <?= htmlspecialchars($tipoLetra) ?>, 'Arial', 'Times New Roman', serif;
				font-size: <?= $tamanoLetra ?>pt;
				line-height: <?= $interlineado ?>;
			}

			.container-certificado {
				padding: <?= $espacioCertificado ?>px;
			}

			.botones-accion {
				display: none !important;
			}

			.marca-agua {
				position: fixed;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				z-index: 1;
				pointer-events: none;
				opacity: <?= $marcaAguaOpacidad ?>;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.marca-agua img {
				width: <?= $marcaAguaTamanio ?>px;
				height: auto;
				object-fit: contain;
			}

			.tabla-calificaciones th {
				background-color: rgba(233, 236, 239, 0.85) !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tr.fila-area {
				background-color: rgba(234, 234, 234, 0.85) !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tr.fila-materia {
				background-color: rgba(255, 255, 255, 0.85) !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tfoot td {
				background-color: rgba(248, 249, 250, 0.85) !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tfoot mark {
				background-color: rgba(255, 243, 205, 0.9) !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-promocion {
				background: #f8f9fa !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-promovido {
				background: #d4edda !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-no-promovido {
				background: #f8d7da !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-retirado {
				background: #fff3cd !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.seccion-nivelaciones {
				background: #fff8e1 !important;
				border-color: #ffc107 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			/* Evitar saltos de página inapropiados */
			.titulo-grado,
			.mensaje-promocion,
			.pie-certificado,
			.header-institucional {
				page-break-inside: avoid;
			}

			/* Forzar salto de página para cada año cuando no está consolidado */
			.pagina-certificado-anio {
				page-break-after: always;
				page-break-inside: avoid;
			}

			.pagina-certificado-anio:last-child {
				page-break-after: auto;
			}

			.tabla-calificaciones {
				page-break-inside: auto;
			}

			.tabla-calificaciones tfoot {
				page-break-inside: avoid;
			}

			.tabla-firmas {
				page-break-inside: avoid;
			}
		}
	</style>
</head>

<body>
	<!-- Ventana de Configuración -->
	<div class="config-certificado-form">
		<h4>⚙️ Configuración del Certificado</h4>
		<form method="GET" id="configCertificadoForm">
			<?php
			// Mantener todos los parámetros GET existentes
			if(!empty($_GET["id"])) echo '<input type="hidden" name="id" value="'.htmlspecialchars($_GET["id"]).'">';
			if(!empty($_GET["desde"])) echo '<input type="hidden" name="desde" value="'.htmlspecialchars($_GET["desde"]).'">';
			if(!empty($_GET["hasta"])) echo '<input type="hidden" name="hasta" value="'.htmlspecialchars($_GET["hasta"]).'">';
			if(!empty($_GET["estampilla"])) echo '<input type="hidden" name="estampilla" value="'.htmlspecialchars($_GET["estampilla"]).'">';
			if(!empty($_GET["sin_encabezado"])) echo '<input type="hidden" name="sin_encabezado" value="'.htmlspecialchars($_GET["sin_encabezado"]).'">';
			// Campo hidden para detectar que el formulario fue enviado
			echo '<input type="hidden" name="config_aplicada" value="1">';
			?>
			
			<div class="form-group">
				<label>Tipo de Letra:</label>
				<select name="tipo_letra">
					<option value="Arial" <?= $tipoLetra == 'Arial' ? 'selected' : '' ?>>Arial</option>
					<option value="Times New Roman" <?= $tipoLetra == 'Times New Roman' ? 'selected' : '' ?>>Times New Roman</option>
					<option value="Courier New" <?= $tipoLetra == 'Courier New' ? 'selected' : '' ?>>Courier New</option>
					<option value="Georgia" <?= $tipoLetra == 'Georgia' ? 'selected' : '' ?>>Georgia</option>
					<option value="Verdana" <?= $tipoLetra == 'Verdana' ? 'selected' : '' ?>>Verdana</option>
					<option value="Helvetica" <?= $tipoLetra == 'Helvetica' ? 'selected' : '' ?>>Helvetica</option>
				</select>
			</div>

			<div class="form-group">
				<label>Tamaño de Letra (pt):</label>
				<input type="number" name="tamano_letra" value="<?= $tamanoLetra ?>" min="8" max="18" step="0.5">
			</div>

			<div class="form-group">
				<label>Interlineado (1.0 - 2.5):</label>
				<input type="number" name="interlineado" value="<?= $interlineado ?>" min="1.0" max="2.5" step="0.1" placeholder="1.6">
				<small style="display: block; color: #666; margin-top: 5px;">Valores menores reducen el espacio entre líneas</small>
			</div>

			<div class="form-group">
				<label>Espacio del certificado (px):</label>
				<input type="number" name="espacio_certificado" value="<?= $espacioCertificado ?>" min="10" max="60" step="5" placeholder="30">
				<small style="display: block; color: #666; margin-top: 5px;">Reduce para que quepa en una hoja</small>
			</div>

			<div class="form-group">
				<label>Espaciado de la tabla (px):</label>
				<input type="number" name="espaciado_tabla" value="<?= $espaciadoTabla ?>" min="3" max="15" step="1" placeholder="8">
				<small style="display: block; color: #666; margin-top: 5px;">Reduce el padding de las celdas para ahorrar espacio</small>
			</div>

			<div class="form-group">
				<label class="checkbox-label">
					<input type="checkbox" name="mostrar_materias" value="1" <?= $mostrarMaterias ? 'checked' : '' ?>>
					Incluir materias (si no, solo áreas)
				</label>
			</div>

			<div class="form-group">
				<label class="checkbox-label">
					<input type="checkbox" name="mostrar_mensaje_promocion" value="1" <?= $mostrarMensajePromocion ? 'checked' : '' ?>>
					Mostrar mensaje de promoción
				</label>
			</div>

			<?php 
			// Solo mostrar opción de consolidar si hay múltiples años
			$hayMultiplesAnios = !empty($desde) && !empty($hasta) && ($hasta - $desde) > 0;
			if ($hayMultiplesAnios) { 
			?>
			<div class="form-group">
				<label class="checkbox-label">
					<input type="checkbox" name="consolidar_texto_anios" value="1" <?= $consolidarTextoAnios ? 'checked' : '' ?>>
					Consolidar texto de años (mostrar todos los años en un solo texto)
				</label>
				<small style="display: block; color: #666; margin-top: 5px;">Si está desmarcado, se mostrará el texto por cada año</small>
			</div>
			<?php } ?>

			<div class="form-group">
				<label class="checkbox-label">
					<input type="checkbox" name="incluir_logo" value="1" <?= $incluirLogo ? 'checked' : '' ?> id="incluir_logo_check">
					Incluir logo de la institución
				</label>
			</div>

			<div class="form-group" id="logo_config" style="display: <?= $incluirLogo ? 'block' : 'none' ?>;">
				<label>Alineación del Logo:</label>
				<select name="logo_alineacion">
					<option value="izquierda" <?= $logoAlineacion == 'izquierda' ? 'selected' : '' ?>>Izquierda</option>
					<option value="centro" <?= $logoAlineacion == 'centro' ? 'selected' : '' ?>>Centro</option>
					<option value="derecha" <?= $logoAlineacion == 'derecha' ? 'selected' : '' ?>>Derecha</option>
				</select>
				
				<label style="margin-top: 10px;">Dimensiones del Logo (px):</label>
				<div class="logo-dimensions">
					<input type="number" name="logo_ancho" value="<?= $logoAncho ?>" min="50" max="500" placeholder="Ancho">
					<input type="number" name="logo_alto" value="<?= $logoAlto ?>" min="50" max="500" placeholder="Alto">
				</div>
			</div>

			<div class="form-group">
				<label>Firmas al pie de página:</label>
				<label class="checkbox-label">
					<input type="checkbox" name="firma_rector" value="1" <?= $firmaRector ? 'checked' : '' ?> id="firma_rector_check">
					Rector(a)
				</label>
				<label class="checkbox-label">
					<input type="checkbox" name="firma_secretario" value="1" <?= $firmaSecretario ? 'checked' : '' ?> id="firma_secretario_check">
					Secretario(a)
				</label>
			</div>

			<div class="form-group" id="config_firma_rector" style="display: <?= $firmaRector ? 'block' : 'none' ?>;">
				<label class="checkbox-label">
					<input type="checkbox" name="mostrar_firma_digital_rector" value="1" <?= $mostrarFirmaDigitalRector ? 'checked' : '' ?> id="mostrar_firma_digital_rector_check">
					Mostrar firma digital del Rector(a) (si existe)
				</label>
				<div id="posicion_firma_rector_config" style="display: <?= $mostrarFirmaDigitalRector ? 'block' : 'none' ?>; margin-top: 10px;">
					<label>Posición de la firma digital (px desde la línea):</label>
					<input type="number" name="posicion_firma_rector" value="<?= $posicionFirmaRector ?>" min="-30" max="30" step="5" placeholder="10">
					<small style="display: block; color: #666; margin-top: 5px;">Valores negativos la acercan a la línea, positivos la alejan</small>
				</div>
			</div>

			<div class="form-group" id="config_firma_secretario" style="display: <?= $firmaSecretario ? 'block' : 'none' ?>;">
				<label class="checkbox-label">
					<input type="checkbox" name="mostrar_firma_digital_secretario" value="1" <?= $mostrarFirmaDigitalSecretario ? 'checked' : '' ?> id="mostrar_firma_digital_secretario_check">
					Mostrar firma digital del Secretario(a) (si existe)
				</label>
				<div id="posicion_firma_secretario_config" style="display: <?= $mostrarFirmaDigitalSecretario ? 'block' : 'none' ?>; margin-top: 10px;">
					<label>Posición de la firma digital (px desde la línea):</label>
					<input type="number" name="posicion_firma_secretario" value="<?= $posicionFirmaSecretario ?>" min="-30" max="30" step="5" placeholder="10">
					<small style="display: block; color: #666; margin-top: 5px;">Valores negativos la acercan a la línea, positivos la alejan</small>
				</div>
			</div>

			<div class="form-group">
				<label class="checkbox-label">
					<input type="checkbox" name="mostrar_marca_agua" value="1" <?= $mostrarMarcaAgua ? 'checked' : '' ?> id="mostrar_marca_agua_check">
					Mostrar marca de agua (logo de la institución)
				</label>
			</div>

			<div class="form-group" id="marca_agua_config" style="display: <?= $mostrarMarcaAgua ? 'block' : 'none' ?>;">
				<label>Opacidad de la marca de agua (0.0 - 1.0):</label>
				<input type="number" name="marca_agua_opacidad" value="<?= $marcaAguaOpacidad ?>" min="0" max="1" step="0.05" placeholder="0.1">
				
				<label style="margin-top: 10px;">Tamaño de la marca de agua (px):</label>
				<input type="number" name="marca_agua_tamanio" value="<?= $marcaAguaTamanio ?>" min="100" max="800" placeholder="300">
			</div>

			<button type="submit" class="btn-aplicar">Aplicar Configuración</button>
		</form>
	</div>

	<!-- Botones de acción -->
	<div class="botones-accion">
		<button class="btn-flotante btn-print" onclick="window.print()">
			<span>■</span>
			<span>Imprimir</span>
		</button>
		<button class="btn-flotante btn-close" onclick="window.close()">
			<span>×</span>
			<span>Cerrar</span>
		</button>
	</div>

	<?php if($mostrarMarcaAgua && !empty($informacion_inst["info_logo"])) { 
		$logoPathMarcaAgua = "../files/images/logo/" . htmlspecialchars($informacion_inst["info_logo"]);
		$logoPathFullMarcaAgua = ROOT_PATH . "/main-app/files/images/logo/" . $informacion_inst["info_logo"];
		
		// Verificar si el logo existe
		if(file_exists($logoPathFullMarcaAgua)) {
	?>
		<div class="marca-agua">
			<img src="<?= $logoPathMarcaAgua ?>" alt="Marca de agua" onerror="this.style.display='none'">
		</div>
	<?php 
		}
	} 
	?>

	<div class="container-certificado">
		<?php 
		// Calcular número de años antes de usarlo
		$restaAgnos = ($hasta - $desde) + 1;
		
		// Si está consolidado, mostrar encabezado, logo y título una sola vez antes del loop
		if ($consolidarTextoAnios || $restaAgnos == 1) {
			if($incluirLogo && !empty($informacion_inst["info_logo"])) {
				$logoPath = "../files/images/logo/" . htmlspecialchars($informacion_inst["info_logo"]);
				$logoPathFull = ROOT_PATH . "/main-app/files/images/logo/" . $informacion_inst["info_logo"];
				
				// Verificar si el logo existe
				if(file_exists($logoPathFull)) {
			?>
				<div class="logo-container">
					<img src="<?= $logoPath ?>" alt="Logo Institución" onerror="this.style.display='none'">
				</div>
			<?php 
				}
			} 
			
			if($mostrarEncabezado) { ?>
				<!-- Encabezado institucional -->
				<div class="header-institucional">
					EL SUSCRITO RECTOR DE <b><?= strtoupper($informacion_inst["info_nombre"] ?? 'LA INSTITUCIÓN') ?></b> DEL MUNICIPIO DE <?= !empty($informacion_inst["ciu_nombre"]) ? strtoupper($informacion_inst["ciu_nombre"]) : 'N/A' ?>, CON
					RECONOCIMIENTO OFICIAL SEGÚN RESOLUCIÓN <?= strtoupper($informacion_inst["info_resolucion"] ?? 'N/A') ?>, EMANADA DE LA SECRETARÍA
					DE EDUCACIÓN DEPARTAMENTAL DE <?= strtoupper($informacion_inst["dep_nombre"] ?? 'N/A') ?>, CON DANE <?= $informacion_inst["info_dane"] ?? 'N/A' ?> Y NIT <?= $informacion_inst["info_nit"] ?? 'N/A' ?>, CELULAR <?= $informacion_inst["info_telefono"] ?? 'N/A' ?>.
				</div>
			<?php } ?>

			<p class="texto-centrado" <?= !$mostrarEncabezado ? 'style="margin-top: 40px;"' : ''; ?>><b>C E R T I F I C A</b></p>
		<?php } ?>

		<?php
		$meses = array(" ", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		
		// Obtener datos del estudiante del año actual (donde sabemos que existe) para información general
		$estudianteActual = Estudiantes::obtenerDatosEstudiante($id, $config['conf_agno']);
		if (empty($estudianteActual) || !is_array($estudianteActual)) {
			// Si no existe en el año actual, intentar obtener del último año disponible
			$estudianteActual = Estudiantes::obtenerDatosEstudiante($id, $hasta);
		}
		
		// Obtener nombre desde el año actual
		$nombre = "";
		if (!empty($estudianteActual) && is_array($estudianteActual)) {
			$nombre = Estudiantes::NombreCompletoDelEstudiante($estudianteActual);
		}
		
		// $restaAgnos ya se calculó antes
		$hayMultiplesAnios = $restaAgnos > 1;
		
		// Si hay múltiples años y está configurado para consolidar, recopilar información de todos los años
		$gradosAnios = [];
		$aniosLectivos = [];
		$educacionConsolidada = '';
		$documentoEstudiante = '';
		
		if ($hayMultiplesAnios && $consolidarTextoAnios) {
			$tempInicio = $desde;
			$tempI = 1;
			while ($tempI <= $restaAgnos) {
				$matriculaTemp = Estudiantes::obtenerDatosEstudiante($id, $tempInicio);
				if (!empty($matriculaTemp) && is_array($matriculaTemp)) {
					$gradosAnios[] = strtoupper($matriculaTemp["gra_nombre"]);
					$aniosLectivos[] = $tempInicio;
					if (empty($documentoEstudiante)) {
						$documentoEstudiante = $matriculaTemp["mat_documento"] ?? 'N/A';
					}
					if (empty($educacionConsolidada)) {
						switch ($matriculaTemp["gra_nivel"]) {
							case PREESCOLAR: 
								$educacionConsolidada = "preescolar"; 
							break;
							case BASICA_PRIMARIA: 
								$educacionConsolidada = "básica primaria"; 
							break;
							case BASICA_SECUNDARIA: 
								$educacionConsolidada = "básica secundaria"; 
							break;
							case MEDIA: 
								$educacionConsolidada = "media"; 
							break;
							default: 
								$educacionConsolidada = "básica"; 
							break;
						}
					}
				}
				$tempInicio++;
				$tempI++;
			}
		}
		
		// Mostrar texto consolidado si hay múltiples años y está configurado
		if ($hayMultiplesAnios && $consolidarTextoAnios && !empty($gradosAnios)) {
			$gradosTexto = implode(' y ', array_unique($gradosAnios));
			$aniosTexto = '';
			if (count($aniosLectivos) == 2) {
				$aniosTexto = $aniosLectivos[0] . ' y ' . $aniosLectivos[1] . ' respectivamente';
			} else {
				$aniosTexto = implode(', ', array_slice($aniosLectivos, 0, -1)) . ' y ' . end($aniosLectivos) . ' respectivamente';
			}
		?>
			<div class="texto-estudiante">
				Que <b><?= $nombre ?></b>, identificado con documento número <?= strtoupper($documentoEstudiante); ?>, cursó y aprobó, en esta
				Institución Educativa, el grado <b><?= $gradosTexto ?></b> en año lectivo <?= $aniosTexto ?> de Educación <?= $educacionConsolidada ?> en la sede PRINCIPAL, con intensidad horaria de acuerdo al <?= $informacion_inst["info_decreto_plan_estudio"] ?? 'decreto vigente' ?>.
			</div>
		<?php
		}
		
		$i = 1;
		$inicio = $desde;

		while ($i <= $restaAgnos) {
			// Optimización: Obtener datos del estudiante
			$matricula = Estudiantes::obtenerDatosEstudiante($id, $inicio);
			
			// Validar que el estudiante exista
			if (empty($matricula) || !is_array($matricula)) {
				$i++;
				$inicio++;
				continue;
			}
			
			// Cargar tipos de notas para este año si aún no se han cargado
			if(empty($tiposNotas)){
				$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $inicio);
				if($cosnultaTiposNotas){
					while ($row = $cosnultaTiposNotas->fetch_assoc()) {
						$tiposNotas[] = $row;
					}
				}
			}
			$gradoActual = $matricula['mat_grado'];
			$grupoActual = $matricula['mat_grupo'];

			// Determinar tipo de educación
			switch ($matricula["gra_nivel"]) {
				case PREESCOLAR: 
					$educacion = "preescolar"; 
				break;
				case BASICA_PRIMARIA: 
					$educacion = "básica primaria"; 
				break;
				case BASICA_SECUNDARIA: 
					$educacion = "básica secundaria"; 
				break;
				case MEDIA: 
					$educacion = "media"; 
				break;
				default: 
					$educacion = "básica"; 
				break;
			}
			
			// Si NO está consolidado, cada año va en una página separada
			if (!$consolidarTextoAnios) {
		?>
			<div class="pagina-certificado-anio">
				<?php if($mostrarEncabezado) { ?>
					<!-- Encabezado institucional -->
					<div class="header-institucional">
						EL SUSCRITO RECTOR DE <b><?= strtoupper($informacion_inst["info_nombre"] ?? 'LA INSTITUCIÓN') ?></b> DEL MUNICIPIO DE <?= !empty($informacion_inst["ciu_nombre"]) ? strtoupper($informacion_inst["ciu_nombre"]) : 'N/A' ?>, CON
						RECONOCIMIENTO OFICIAL SEGÚN RESOLUCIÓN <?= strtoupper($informacion_inst["info_resolucion"] ?? 'N/A') ?>, EMANADA DE LA SECRETARÍA
						DE EDUCACIÓN DEPARTAMENTAL DE <?= strtoupper($informacion_inst["dep_nombre"] ?? 'N/A') ?>, CON DANE <?= $informacion_inst["info_dane"] ?? 'N/A' ?> Y NIT <?= $informacion_inst["info_nit"] ?? 'N/A' ?>, CELULAR <?= $informacion_inst["info_telefono"] ?? 'N/A' ?>.
					</div>
				<?php } ?>

				<p class="texto-centrado" <?= !$mostrarEncabezado ? 'style="margin-top: 40px;"' : ''; ?>><b>C E R T I F I C A</b></p>

				<?php if($incluirLogo && !empty($informacion_inst["info_logo"])) {
					$logoPath = "../files/images/logo/" . htmlspecialchars($informacion_inst["info_logo"]);
					$logoPathFull = ROOT_PATH . "/main-app/files/images/logo/" . $informacion_inst["info_logo"];
					if(file_exists($logoPathFull)) {
				?>
					<div class="logo-container">
						<img src="<?= $logoPath ?>" alt="Logo Institución" onerror="this.style.display='none'">
					</div>
				<?php 
					}
				} 
				?>

				<div class="texto-estudiante">
					Que <b><?= $nombre ?></b>, identificado con documento número <?= strtoupper($matricula["mat_documento"] ?? 'N/A'); ?>, cursó y aprobó, en esta
					Institución Educativa, el grado <b><?= strtoupper($matricula["gra_nombre"]); ?></b> en año lectivo <?= $inicio; ?> de Educación <?= $educacion?> en la sede PRINCIPAL, con intensidad horaria de acuerdo al <?= $informacion_inst["info_decreto_plan_estudio"] ?? 'decreto vigente' ?>.
				</div>

				<div class="titulo-grado">
					<?= strtoupper($matricula["gra_nombre"]); ?> <?= $inicio; ?>
				</div>
		<?php
			} else {
				// Si está consolidado, solo mostrar título del grado
		?>
				<div class="titulo-grado">
					<?= strtoupper($matricula["gra_nombre"]); ?> <?= $inicio; ?>
				</div>
		<?php
			}
		?>

		<table class="tabla-calificaciones">
				<thead>
					<tr>
						<th style="width: 55%; text-align: left;">ASIGNATURAS</th>
						<th style="width: 10%;">I.H</th>
						<th style="width: 15%;">DEFINITIVA</th>
						<th style="width: 20%;">NIVEL DE DESEMPEÑO</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Optimización: Obtener todas las asignaturas del curso
					$consultaAreas = Asignaturas::consultarAsignaturasCurso($conexion, $config, $gradoActual, $grupoActual, $inicio);
					$numAreas = mysqli_num_rows($consultaAreas);
					$sumaPromedioGeneral = 0;
					$materiasPerdidas = 0;

					while($datosAreas = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)){
						// Consultar materias del área
						$consultaMaterias = CargaAcademica::consultaMaterias($config, $config["conf_periodos_maximos"], $matricula['mat_id'], $datosAreas['car_curso'], $datosAreas['car_grupo'], $datosAreas['ar_id'], $inicio);
						
						// Calcular promedio del área usando calcularPromedioAreaCompleto (considera ponderado/simple)
						$periodosArray = [];
						$periodosMaximos = !empty($config['conf_periodos_maximos']) ? (int)$config['conf_periodos_maximos'] : 4;
						for($p = 1; $p <= $periodosMaximos; $p++){
							$periodosArray[] = $p;
						}
						$promedioAreaCompleto = Boletin::calcularPromedioAreaCompleto($config, $matricula['mat_id'], $datosAreas['ar_id'], $periodosArray, $datosAreas['car_curso'], $datosAreas['car_grupo'], $inicio);
						$notaAreaAcumulada = $promedioAreaCompleto['acumulado'];
						
						$notaArea = 0;
						$ih = "";

						// Mostrar materias solo si está configurado
						if($mostrarMaterias) {
							while($datosMaterias = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
								// Director de grupo
								if($datosMaterias["car_director_grupo"]==1){
									$idDirector=$datosMaterias["car_docente"];
								}

								$ih = $datosMaterias["car_ih"];
								
								// Si hay múltiples materias en el área, mostrarlas
								if($datosAreas['numMaterias'] > 1){
									$notaMateriasPeriodosTotal = 0;
									$ultimoPeriodo = $config["conf_periodos_maximos"];
									
									// Calcular promedio de la materia por periodos
									for($p = 1; $p <= $config["conf_periodos_maximos"]; $p++){
										$datosPeriodos = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $matricula['mat_id'], $datosMaterias["car_id"], $inicio);
										$notaMateriasPeriodos = !empty($datosPeriodos['bol_nota']) ? round($datosPeriodos['bol_nota'], 1) : 0;
										$notaMateriasPeriodosTotal += $notaMateriasPeriodos;

										if (empty($datosPeriodos['bol_periodo'])){
											$ultimoPeriodo -= 1;
										}
									}

									// Promedio acumulado de la materia
									$notaAcomuladoMateria = 0;
									if ($ultimoPeriodo > 0) {
										$notaAcomuladoMateria = $notaMateriasPeriodosTotal / $ultimoPeriodo;
									}
									
									// Obtener desempeño correcto usando obtenerDatosTipoDeNotas (usar valor numérico)
									$notaAcomuladoMateriaNum = (float)$notaAcomuladoMateria;
									$notaAcomuladoMateriaFormateada = Boletin::notaDecimales($notaAcomuladoMateriaNum);
									$cacheKey = $config['conf_notas_categoria'] . '_' . $notaAcomuladoMateriaNum . '_' . $inicio;
									if (!isset($notasCualitativasCache[$cacheKey])) {
										$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoMateriaNum, $inicio);
									}
									$estiloNotaAcomuladoMaterias = $notasCualitativasCache[$cacheKey];
									
									// Validar que no sea null y tenga el campo notip_nombre
									if(empty($estiloNotaAcomuladoMaterias) || !is_array($estiloNotaAcomuladoMaterias)){
										$estiloNotaAcomuladoMaterias = ['notip_nombre' => ''];
									}
									if(empty($estiloNotaAcomuladoMaterias['notip_nombre'])){
										// Si no hay desempeño, usar determinarRango como fallback
										if(!empty($tiposNotas)){
											$estiloNotaAcomuladoMaterias = Boletin::determinarRango($notaAcomuladoMateriaNum, $tiposNotas);
										} else {
											$estiloNotaAcomuladoMaterias = ['notip_nombre' => 'N/A'];
										}
									}
									
								?>
									<tr class="fila-materia">
										<td style="padding-left: 25px;">
											<?=$datosMaterias['mat_nombre']?>
											<?php 
											// Mostrar porcentaje solo si el usuario es DEVELOPER
											if($datosUsuarioActual['uss_tipo'] == TIPO_DEV && !empty($datosMaterias['mat_valor'])){
												echo ' (' . $datosMaterias['mat_valor'] . '%)';
											}
											?>
										</td>
										<td style="text-align: center;"><?=$datosMaterias['car_ih']?></td>
										<td style="text-align: center;"><?=$notaAcomuladoMateriaFormateada?></td>
										<td style="text-align: center;"><?=!empty($estiloNotaAcomuladoMaterias['notip_nombre']) ? strtoupper($estiloNotaAcomuladoMaterias['notip_nombre']) : 'N/A'?></td>
									</tr>
								<?php
								}

								// Nota para las áreas
								if(!empty($datosMaterias['notaArea'])) {
									$notaArea += round($datosMaterias['notaArea'], 1);
								}
							}
						} else {
							// Si no se muestran materias, obtener solo el IH del área
							$datosMateriasPrimera = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH);
							if($datosMateriasPrimera) {
								$ih = $datosMateriasPrimera["car_ih"];
							}
						}
					?>
						<!-- Fila del área -->
						<tr class="fila-area">
							<td><?=$datosAreas['ar_nombre']?></td>
							<td style="text-align: center;"><?=$ih?></td>
							<?php
								// Usar el promedio calculado con calcularPromedioAreaCompleto (ya considera ponderado/simple)
								$notaAcomuladoArea = $notaAreaAcumulada;
								
								// Obtener desempeño correcto usando obtenerDatosTipoDeNotas (usar valor numérico)
								$notaAcomuladoAreaNum = (float)$notaAcomuladoArea;
								$notaAcomuladoAreaFormateada = Boletin::notaDecimales($notaAcomuladoAreaNum);
								$cacheKey = $config['conf_notas_categoria'] . '_' . $notaAcomuladoAreaNum . '_' . $inicio;
								if (!isset($notasCualitativasCache[$cacheKey])) {
									$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoAreaNum, $inicio);
								}
								$estiloNotaAcomuladoAreas = $notasCualitativasCache[$cacheKey];
								
								// Validar que no sea null y tenga el campo notip_nombre
								if(empty($estiloNotaAcomuladoAreas) || !is_array($estiloNotaAcomuladoAreas)){
									$estiloNotaAcomuladoAreas = ['notip_nombre' => ''];
								}
								if(empty($estiloNotaAcomuladoAreas['notip_nombre'])){
									// Si no hay desempeño, usar determinarRango como fallback
									if(!empty($tiposNotas)){
										$estiloNotaAcomuladoAreas = Boletin::determinarRango($notaAcomuladoAreaNum, $tiposNotas);
									} else {
										$estiloNotaAcomuladoAreas = ['notip_nombre' => 'N/A'];
									}
								}

								if($notaAcomuladoAreaNum < $config['conf_nota_minima_aprobar']){
									$materiasPerdidas++;
								}
							?>
							<td style="text-align: center;"><?=$notaAcomuladoAreaFormateada?></td>
							<td style="text-align: center;"><?=!empty($estiloNotaAcomuladoAreas['notip_nombre']) ? strtoupper($estiloNotaAcomuladoAreas['notip_nombre']) : 'N/A'?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" style="text-align: center;">
							<mark>
								<?php
								// Optimización: Obtener estilos de notas una sola vez
								$consultaEstiloNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $inicio);
								$numEstiloNota = mysqli_num_rows($consultaEstiloNota);
								$estilosTexto = [];
								
								while($estiloNota = mysqli_fetch_array($consultaEstiloNota, MYSQLI_BOTH)){
									$estilosTexto[] = strtoupper($estiloNota['notip_nombre']) . ": " . $estiloNota['notip_desde'] . " - " . $estiloNota['notip_hasta'];
								}
								echo implode(" / ", $estilosTexto);
								
								// Porcentajes de áreas (solo si está configurado el porcentaje de materias)
								if ($config['conf_agregar_porcentaje_asignaturas'] == SI) {
									echo "<br>";
									
									$consultaMaterias = CargaAcademica::consultaMateriasAreas($config, $gradoActual, $grupoActual, $inicio);
									$numMaterias = mysqli_num_rows($consultaMaterias);
									$areaAnterior = null;
									$valorAreas = "PORCENTAJES ÁREAS:";
									
									while($datosArea = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
										$diagonal = " ";
										
										if(!is_null($areaAnterior) && $areaAnterior != $datosArea['mat_area']){
											$diagonal = " // ";
										}
										
										$areaAnterior = $datosArea['mat_area'];
										$valorAreas .= $diagonal . strtoupper($datosArea['mat_nombre']) . " (" . $datosArea['mat_valor'] . ")";
									}
									echo $valorAreas;
								}
								?>
							</mark>
						</td>
					</tr>
				</tfoot>
			</table>

			<?php
			// Nivelaciones
			$nivelaciones = Calificaciones::consultarNivelacionesEstudiante($conexion, $config, $id, $inicio);
			$numNiv = mysqli_num_rows($nivelaciones);

			if ($numNiv > 0) {
			?>
				<div class="seccion-nivelaciones">
					<p style="font-weight: bold; margin-bottom: 10px;">El(la) Estudiante niveló las siguientes materias:</p>
					<?php while ($niv = mysqli_fetch_array($nivelaciones, MYSQLI_BOTH)) { ?>
						<p>
							<b><?= strtoupper($niv["mat_nombre"]) ?> (<?= $niv["niv_definitiva"] ?>)</b> 
							Según acta <?= $niv["niv_acta"] ?> en la fecha de <?= $niv["niv_fecha_nivelacion"] ?>
						</p>
					<?php } ?>
				</div>
			<?php
			}

			// Determinar promoción
			$cargasAcademicasC = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
			$materiasPerdidas = 0;
			$vectorMP = array();
			$periodoFinal = $config['conf_periodos_maximos'];
			
			while ($cargasC = mysqli_fetch_array($cargasAcademicasC, MYSQLI_BOTH)) {
				$boletinC = Boletin::traerDefinitivaBoletinCarga($config, $cargasC["car_id"], $id, $inicio);
				$notaC = !empty($boletinC['promedio']) ? round($boletinC['promedio'], 1) : 0;
				
				if ($notaC < $config[5]) {
					$vectorMP[$materiasPerdidas] = $cargasC["car_id"];
					$materiasPerdidas++;
				}

				if ($boletinC['periodo'] < $config['conf_periodos_maximos']){
					$periodoFinal = $boletinC['periodo'];
				}
			}

			// Verificar nivelaciones
			$niveladas = 0;
			if ($materiasPerdidas > 0) {
				for ($m = 0; $m < $materiasPerdidas; $m++) {
					$nMP = Calificaciones::validarMateriaNivelada($conexion, $config, $id, $vectorMP[$m], $inicio);
					if (mysqli_num_rows($nMP) > 0) {
						$niveladas++;
					}
				}
			}

			// Verificar si hay notas en el último period configurado
			$tieneNotasUltimoPeriodo = false;
			$ultimoPeriodo = $config["conf_periodos_maximos"];
			$cargasParaVerificar = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
			while ($cargaVerificar = mysqli_fetch_array($cargasParaVerificar, MYSQLI_BOTH)) {
				$notaUltimoPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $ultimoPeriodo, $id, $cargaVerificar["car_id"], $inicio);
				if (!empty($notaUltimoPeriodo['bol_nota'])) {
					$tieneNotasUltimoPeriodo = true;
					break;
				}
			}

			// Mensaje de promoción (solo si hay notas en el último periodo y está configurado para mostrarlo)
			// Lógica:
			// - Si "Mostrar mensaje de promoción" está activa Y "Consolidar texto de años" NO está activa:
			//   → Mostrar mensaje en cada año (cada hoja separada)
			// - Si "Mostrar mensaje de promoción" está activa Y "Consolidar texto de años" está activa:
			//   → Mostrar mensaje solo en el último año
			$mostrarMensajePromocionAnio = false;
			if ($mostrarMensajePromocion) {
				if ($consolidarTextoAnios) {
					// Consolidado: solo mostrar en el último año
					if ($i == $restaAgnos) {
						$mostrarMensajePromocionAnio = true;
					}
				} else {
					// No consolidado: mostrar en cada año (cada hoja)
					$mostrarMensajePromocionAnio = true;
				}
			}
			
			if ($tieneNotasUltimoPeriodo && $mostrarMensajePromocionAnio) {
				$claseMensaje = 'mensaje-promocion';
				if($materiasPerdidas == 0 || $niveladas >= $materiasPerdidas){
					$msj = "EL (LA) ESTUDIANTE " . $nombre . " FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-promovido';
				} else {
					$msj = "EL (LA) ESTUDIANTE " . $nombre . " NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-no-promovido';
				}

				if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
					$msj = "EL(LA) ESTUDIANTE " . $nombre . " FUE RETIRADO SIN FINALIZAR AÑO LECTIVO";
					$claseMensaje = 'mensaje-promocion mensaje-retirado';
				}
				?>
				<div class="<?= $claseMensaje; ?>"><?= $msj; ?></div>
			<?php } ?>

			<?php 
			// Si NO está consolidado, mostrar pie y firmas dentro de cada página
			if (!$consolidarTextoAnios) {
				if (date('m') < 10) {
					$mes = substr(date('m'), 1);
				} else {
					$mes = date('m');
				}
			?>
				<!-- PIE DEL CERTIFICADO -->
				<div class="pie-certificado">
					Se expide en <?= !empty($informacion_inst["ciu_nombre"]) ? ucwords(strtolower($informacion_inst["ciu_nombre"])) : 'la ciudad'; ?> el <?= date("d"); ?> de <?= $meses[$mes]; ?> de <?= date("Y"); ?>, con destino al
					interesado. <?php if ($config['conf_estampilla_certificados'] == SI) { echo "Se anula estampilla número <mark style='background: #fff3cd; padding: 2px 5px;'>".$estampilla."</mark>, según ordenanza 012/05 y decreto 005/06."; } ?>
				</div>

				<!-- FIRMAS -->
				<table class="tabla-firmas">
					<tr>
						<?php if($firmaRector) { ?>
						<td style="width: <?= $firmaSecretario ? '50%' : '100%' ?>;">
							<?php
							$nombreRector = 'RECTOR(A)';
							$mostrarFirmaDigitalRectorActual = false;
							
							if (!empty($informacion_inst["info_rector"])) {
								$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
								if (!empty($rector)) {
									$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
									$tieneFirmaDigital = !empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma']);
									
									if($tieneFirmaDigital && $mostrarFirmaDigitalRector){
										$mostrarFirmaDigitalRectorActual = true;
										// El margin-bottom controla directamente el espacio entre la imagen y la línea
										// Valor base de 10px, más el valor de posición (puede ser negativo o positivo)
										$espacioFinal = 10 + $posicionFirmaRector;
										// Asegurar que nunca sea menor a 0
										if($espacioFinal < 0) {
											$espacioFinal = 0;
										}
										$estiloFirmaRector = 'margin-bottom: ' . $espacioFinal . 'px !important;';
										echo '<img class="firma-imagen" src="../files/fotos/'.$rector["uss_firma"].'" alt="Firma Rector" style="max-width: 100px; height: auto; margin-left: auto; margin-right: auto; ' . $estiloFirmaRector . '">';
									}
								}
							}
							?>
							<div class="firma-linea"></div>
							<div class="firma-nombre"><?= strtoupper($nombreRector) ?></div>
							<div class="firma-cargo">Rector(a)</div>
						</td>
						<?php } ?>
						
						<?php if($firmaSecretario) { ?>
						<td style="width: <?= $firmaRector ? '50%' : '100%' ?>;">
							<?php
							$nombreSecretario = 'SECRETARIO(A)';
							$mostrarFirmaDigitalSecretarioActual = false;
							
							if (!empty($informacion_inst["info_secretaria_academica"])) {
								$secretario = Usuarios::obtenerDatosUsuario($informacion_inst["info_secretaria_academica"]);
								if (!empty($secretario)) {
									$nombreSecretario = UsuariosPadre::nombreCompletoDelUsuario($secretario);
									$tieneFirmaDigital = !empty($secretario["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $secretario['uss_firma']);
									
									if($tieneFirmaDigital && $mostrarFirmaDigitalSecretario){
										$mostrarFirmaDigitalSecretarioActual = true;
										// El margin-bottom controla directamente el espacio entre la imagen y la línea
										// Valor base de 10px, más el valor de posición (puede ser negativo o positivo)
										$espacioFinal = 10 + $posicionFirmaSecretario;
										// Asegurar que nunca sea menor a 0
										if($espacioFinal < 0) {
											$espacioFinal = 0;
										}
										$estiloFirmaSecretario = 'margin-bottom: ' . $espacioFinal . 'px !important;';
										echo '<img class="firma-imagen" src="../files/fotos/'.$secretario["uss_firma"].'" alt="Firma Secretario" style="max-width: 100px; height: auto; margin-left: auto; margin-right: auto; ' . $estiloFirmaSecretario . '">';
									}
								}
							}
							?>
							<div class="firma-linea"></div>
							<div class="firma-nombre"><?= strtoupper($nombreSecretario) ?></div>
							<div class="firma-cargo">Secretario(a)</div>
						</td>
						<?php } ?>
					</tr>
				</table>
			</div> <!-- Cierre de pagina-certificado-anio -->
			<?php } ?>

		<?php
			$inicio++;
			$i++;
		}
		?>

		<?php 
		// Si está consolidado, mostrar pie y firmas una sola vez después del loop
		if ($consolidarTextoAnios || $restaAgnos == 1) {
			if (date('m') < 10) {
				$mes = substr(date('m'), 1);
			} else {
				$mes = date('m');
			}
		?>
			<!-- PIE DEL CERTIFICADO -->
			<div class="pie-certificado">
				Se expide en <?= !empty($informacion_inst["ciu_nombre"]) ? ucwords(strtolower($informacion_inst["ciu_nombre"])) : 'la ciudad'; ?> el <?= date("d"); ?> de <?= $meses[$mes]; ?> de <?= date("Y"); ?>, con destino al
				interesado. <?php if ($config['conf_estampilla_certificados'] == SI) { echo "Se anula estampilla número <mark style='background: #fff3cd; padding: 2px 5px;'>".$estampilla."</mark>, según ordenanza 012/05 y decreto 005/06."; } ?>
			</div>

			<!-- FIRMAS -->
			<table class="tabla-firmas">
			<tr>
				<?php if($firmaRector) { ?>
				<td style="width: <?= $firmaSecretario ? '50%' : '100%' ?>;">
					<?php
					$nombreRector = 'RECTOR(A)';
					$mostrarFirmaDigitalRectorActual = false;
					
					if (!empty($informacion_inst["info_rector"])) {
						$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
						if (!empty($rector)) {
							$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
							$tieneFirmaDigital = !empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma']);
							
							if($tieneFirmaDigital && $mostrarFirmaDigitalRector){
								$mostrarFirmaDigitalRectorActual = true;
								// El margin-bottom controla directamente el espacio entre la imagen y la línea
								// Valor base de 10px, más el valor de posición (puede ser negativo o positivo)
								$espacioFinal = 10 + $posicionFirmaRector;
								// Asegurar que nunca sea menor a 0
								if($espacioFinal < 0) {
									$espacioFinal = 0;
								}
								$estiloFirmaRector = 'margin-bottom: ' . $espacioFinal . 'px !important;';
								echo '<img class="firma-imagen" src="../files/fotos/'.$rector["uss_firma"].'" alt="Firma Rector" style="max-width: 100px; height: auto; margin-left: auto; margin-right: auto; ' . $estiloFirmaRector . '">';
							}
						}
					}
					?>
					<div class="firma-linea"></div>
					<div class="firma-nombre"><?= strtoupper($nombreRector) ?></div>
					<div class="firma-cargo">Rector(a)</div>
				</td>
				<?php } ?>
				
				<?php if($firmaSecretario) { ?>
				<td style="width: <?= $firmaRector ? '50%' : '100%' ?>;">
					<?php
					$nombreSecretario = 'SECRETARIO(A)';
					$mostrarFirmaDigitalSecretarioActual = false;
					
					if (!empty($informacion_inst["info_secretaria_academica"])) {
						$secretario = Usuarios::obtenerDatosUsuario($informacion_inst["info_secretaria_academica"]);
						if (!empty($secretario)) {
							$nombreSecretario = UsuariosPadre::nombreCompletoDelUsuario($secretario);
							$tieneFirmaDigital = !empty($secretario["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $secretario['uss_firma']);
							
							if($tieneFirmaDigital && $mostrarFirmaDigitalSecretario){
								$mostrarFirmaDigitalSecretarioActual = true;
								// El margin-bottom controla directamente el espacio entre la imagen y la línea
								// Valor base de 10px, más el valor de posición (puede ser negativo o positivo)
								$espacioFinal = 10 + $posicionFirmaSecretario;
								// Asegurar que nunca sea menor a 0
								if($espacioFinal < 0) {
									$espacioFinal = 0;
								}
								$estiloFirmaSecretario = 'margin-bottom: ' . $espacioFinal . 'px !important;';
								echo '<img class="firma-imagen" src="../files/fotos/'.$secretario["uss_firma"].'" alt="Firma Secretario" style="max-width: 100px; height: auto; margin-left: auto; margin-right: auto; ' . $estiloFirmaSecretario . '">';
							}
						}
					}
					?>
					<div class="firma-linea"></div>
					<div class="firma-nombre"><?= strtoupper($nombreSecretario) ?></div>
					<div class="firma-cargo">Secretario(a)</div>
				</td>
				<?php } ?>
			</tr>
		</table>
		<?php } ?>

	<?php 
	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
	?>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Mostrar/ocultar configuración de logo
			var incluirLogoCheck = document.getElementById('incluir_logo_check');
			var logoConfig = document.getElementById('logo_config');
			
			if(incluirLogoCheck) {
				incluirLogoCheck.addEventListener('change', function() {
					logoConfig.style.display = this.checked ? 'block' : 'none';
				});
			}

			// Mostrar/ocultar configuración de marca de agua
			var mostrarMarcaAguaCheck = document.getElementById('mostrar_marca_agua_check');
			var marcaAguaConfig = document.getElementById('marca_agua_config');
			
			if(mostrarMarcaAguaCheck) {
				mostrarMarcaAguaCheck.addEventListener('change', function() {
					marcaAguaConfig.style.display = this.checked ? 'block' : 'none';
				});
			}

			// Mostrar/ocultar configuración de firma del rector
			var firmaRectorCheck = document.getElementById('firma_rector_check');
			var configFirmaRector = document.getElementById('config_firma_rector');
			
			if(firmaRectorCheck) {
				firmaRectorCheck.addEventListener('change', function() {
					configFirmaRector.style.display = this.checked ? 'block' : 'none';
				});
			}

			// Mostrar/ocultar configuración de firma del secretario
			var firmaSecretarioCheck = document.getElementById('firma_secretario_check');
			var configFirmaSecretario = document.getElementById('config_firma_secretario');
			
			if(firmaSecretarioCheck) {
				firmaSecretarioCheck.addEventListener('change', function() {
					configFirmaSecretario.style.display = this.checked ? 'block' : 'none';
				});
			}

			// Mostrar/ocultar posición de firma digital del rector
			var mostrarFirmaDigitalRectorCheck = document.getElementById('mostrar_firma_digital_rector_check');
			var posicionFirmaRectorConfig = document.getElementById('posicion_firma_rector_config');
			
			if(mostrarFirmaDigitalRectorCheck) {
				mostrarFirmaDigitalRectorCheck.addEventListener('change', function() {
					posicionFirmaRectorConfig.style.display = this.checked ? 'block' : 'none';
				});
			}

			// Mostrar/ocultar posición de firma digital del secretario
			var mostrarFirmaDigitalSecretarioCheck = document.getElementById('mostrar_firma_digital_secretario_check');
			var posicionFirmaSecretarioConfig = document.getElementById('posicion_firma_secretario_config');
			
			if(mostrarFirmaDigitalSecretarioCheck) {
				mostrarFirmaDigitalSecretarioCheck.addEventListener('change', function() {
					posicionFirmaSecretarioConfig.style.display = this.checked ? 'block' : 'none';
				});
			}

			// Atajo de teclado para imprimir
			document.addEventListener('keydown', function(e) {
				if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
					e.preventDefault();
					window.print();
				}
			});
		});
	</script>
</body>
</html>
