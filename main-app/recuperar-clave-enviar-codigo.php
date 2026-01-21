<?php
// Iniciar output buffering para capturar cualquier output no deseado
ob_start();

// Configurar manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Función para enviar respuesta JSON de error
function enviarErrorJSON($mensaje, $codigo = 500, $detalles = null) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    http_response_code($codigo);
    header('Content-Type: application/json; charset=UTF-8');
    
    $response = [
        'success' => false,
        'message' => $mensaje,
        'error_code' => $codigo
    ];
    
    if ($detalles !== null) {
        $response['error_details'] = $detalles;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Capturar errores fatales
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR])) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        if (!empty($_REQUEST['async'])) {
            enviarErrorJSON('Error interno del servidor', 500);
        }
    }
});

// Capturar excepciones no capturadas
set_exception_handler(function($exception) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    if (!empty($_REQUEST['async'])) {
        enviarErrorJSON('Error en el servidor: ' . $exception->getMessage(), 500);
    }
});

try {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
    require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");
    require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
    require_once(ROOT_PATH."/main-app/class/Notificacion.php");

    $notificacion = new Notificacion();
} catch (Exception $e) {
    if (!empty($_REQUEST['async'])) {
        enviarErrorJSON('Error al inicializar: ' . $e->getMessage(), 500);
    } else {
        die('Error al inicializar: ' . $e->getMessage());
    }
}

try {
    $conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }
} catch (Exception $e) {
    if (!empty($_REQUEST['async'])) {
        enviarErrorJSON('Error de conexión a la base de datos', 500);
    } else {
        die('Error de conexión: ' . $e->getMessage());
    }
}

$year_actual = date('Y');
$usuariosEncontrados = 1;
$datosUsuario = null;
$todosLosUsuarios = [];

try {
    if (!empty($_REQUEST['Usuario'])) {
        $valorBusqueda = $_REQUEST['Usuario'];
        $resultadosBusqueda = Usuarios::buscarUsuariosRecuperarClave($valorBusqueda, $year_actual);
        $usuariosEncontrados = count($resultadosBusqueda);
        $todosLosUsuarios = $resultadosBusqueda;
        
        if ($usuariosEncontrados == 1) {
            $datosUsuario = !empty($resultadosBusqueda) ? $resultadosBusqueda[0] : null;
        } else {
            $datosUsuario = null;
        }
    } elseif (!empty($_REQUEST['usuarioId'])) {
        $datosUsuario = Usuarios::buscarUsuarioIdNuevo($_REQUEST['usuarioId']);
        $usuariosEncontrados = !empty($datosUsuario) ? 1 : 0;
        $todosLosUsuarios = !empty($datosUsuario) ? [$datosUsuario] : [];
    }
} catch (Exception $e) {
    if (!empty($_REQUEST['async'])) {
        enviarErrorJSON('Error al buscar usuario: ' . $e->getMessage(), 500);
    } else {
        die('Error: ' . $e->getMessage());
    }
} catch (Throwable $t) {
    if (!empty($_REQUEST['async'])) {
        enviarErrorJSON('Error fatal al buscar usuario: ' . $t->getMessage(), 500);
    } else {
        die('Error fatal: ' . $t->getMessage());
    }
}

if ($usuariosEncontrados == 1 && !empty($datosUsuario)) {
	if (!empty($datosUsuario['uss_email'])) {
        try {
            $data = [
                'usuario_nombre'      => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
                'institucion_id'      => $datosUsuario['institucion'],
                'usuario_id'          => $datosUsuario['uss_id'],
                'year'                => $datosUsuario['year'],
                'asunto'              => 'Código de Confirmación: ',
                'body_template_route' => ROOT_PATH .'/config-general/template-email-recuperar-clave-codigo.php',
                'usuario_email'       => $datosUsuario['uss_email'],
                'telefono'            => $datosUsuario['uss_celular'],
                'id_nuevo'            => $datosUsuario['id_nuevo'],
                'datos_codigo'        => [],
            ];

            $canal = Notificacion::CANAL_EMAIL;
            $datosCodigo = $notificacion->enviarCodigoNotificacion($data, $canal, Notificacion::PROCESO_RECUPERAR_CLAVE);
        } catch (Exception $e) {
            if (!empty($_REQUEST['async'])) {
                enviarErrorJSON('Error al enviar código: ' . $e->getMessage(), 500);
            } else {
                die('Error al enviar código: ' . $e->getMessage());
            }
        } catch (Throwable $t) {
            if (!empty($_REQUEST['async'])) {
                enviarErrorJSON('Error fatal al enviar código: ' . $t->getMessage(), 500);
            } else {
                die('Error fatal: ' . $t->getMessage());
            }
        }

		if (!empty($_REQUEST['async'])) {
			while (ob_get_level() > 1) {
				ob_end_clean();
			}
			
			// Sanitizar datosCodigo
			$datosCodigoSanitizado = null;
			if (isset($datosCodigo) && is_array($datosCodigo)) {
				$datosCodigoSanitizado = $datosCodigo;
			} elseif (isset($datosCodigo) && is_object($datosCodigo)) {
				$datosCodigoSanitizado = json_decode(json_encode($datosCodigo), true);
			} elseif (isset($datosCodigo)) {
				$datosCodigoSanitizado = ['valor' => (string)$datosCodigo];
			}
			
			$arrayIdInsercion = [
				"success" => true,
				"message" => "Codigo enviado exitosamente",
				'usuarioEmail' => $datosUsuario['uss_email'],
				'usuarioNombre' => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
				'institucionId' => $datosUsuario['institucion'],
				'usuarioId' => $datosUsuario['uss_id'],
				'year' => $datosUsuario['year'],
				'telefono' => $datosUsuario['uss_celular'],
				'idNuevo' => $datosUsuario['id_nuevo'],
				"datosCodigo" => $datosCodigoSanitizado
			];

			header('Content-Type: application/json; charset=UTF-8');
			
			$jsonResponse = @json_encode($arrayIdInsercion, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
			
			if ($jsonResponse === false) {
				unset($arrayIdInsercion['datosCodigo']);
				$arrayIdInsercion['datosCodigo'] = null;
				$jsonResponse = json_encode($arrayIdInsercion, JSON_UNESCAPED_UNICODE);
				
				if ($jsonResponse === false) {
					enviarErrorJSON('Error al procesar la respuesta', 500);
				}
			}
			
			if (ob_get_level()) {
				ob_end_clean();
			}
			echo $jsonResponse;
			exit;
		} else {
			$data['datos_codigo'] = $datosCodigo;
			$datosUsuarioSerializados = serialize($data);

			echo '<script type="text/javascript">window.location.href="recuperar-clave-validar-codigo.php?datosUsuario=' . base64_encode($datosUsuarioSerializados) . '";</script>';
			exit();
		}
	} else {
		if (!empty($_REQUEST['async'])) {
			while (ob_get_level() > 1) {
				ob_end_clean();
			}
			
			$arrayIdInsercion = ["success" => false, "message" => "No se encontró un email registrado para este usuario. Contacta a tu administrador."];

			header('Content-Type: application/json; charset=UTF-8');
			if (ob_get_level()) {
				ob_end_clean();
			}
			echo json_encode($arrayIdInsercion, JSON_UNESCAPED_UNICODE);
			exit;
		} else {
			echo '<script type="text/javascript">window.location.href="recuperar-clave.php?error=1";</script>';
			exit();
		}
	}
}

if ($usuariosEncontrados > 1) {
	if (!empty($_REQUEST['async'])) {
		while (ob_get_level() > 1) {
			ob_end_clean();
		}
		
		$valorBusqueda = $_REQUEST['Usuario'] ?? '';
		$esEmail = filter_var($valorBusqueda, FILTER_VALIDATE_EMAIL);
		
		if ($esEmail) {
			$arrayIdInsercion = [
				"success" => false,
				"message" => "Se encontraron múltiples usuarios con este correo electrónico. Por favor, utiliza tu nombre de usuario en lugar del correo para recuperar tu contraseña.",
				"multipleUsersEmail" => true
			];
		} else {
			$arrayIdInsercion = [
				"success" => true,
				"multipleUsers" => true,
				"message" => "Se encontraron múltiples usuarios",
				"usuarios" => $todosLosUsuarios
			];
		}

		header('Content-Type: application/json; charset=UTF-8');
		if (ob_get_level()) {
			ob_end_clean();
		}
		echo json_encode($arrayIdInsercion, JSON_UNESCAPED_UNICODE);
		exit;
	} else {
		$usuariosSerializados = serialize($todosLosUsuarios);
		$usuariosSerializadosBase64 = base64_encode($usuariosSerializados);
		
		echo '<form id="form" method="post" action="recuperar-clave.php?valor=' . base64_encode($_REQUEST["Usuario"]) . '">';
		echo '<input type="hidden" name="usuariosEncontrados" value="' . $usuariosSerializadosBase64 . '">';
		echo '</form>';
		echo '<script>document.getElementById("form").submit();</script>';
		exit();
	}
} else {
	if (!empty($_REQUEST['async'])) {
		while (ob_get_level() > 1) {
			ob_end_clean();
		}
		
		$arrayIdInsercion = ["success" => false, "message" => "Usuario no encontrado. Verifica tus datos e intenta nuevamente."];

		header('Content-Type: application/json; charset=UTF-8');
		if (ob_get_level()) {
			ob_end_clean();
		}
		echo json_encode($arrayIdInsercion, JSON_UNESCAPED_UNICODE);
		exit;
	} else {
		echo '<script type="text/javascript">window.location.href="recuperar-clave.php?error=1";</script>';
		exit();
	}
}
