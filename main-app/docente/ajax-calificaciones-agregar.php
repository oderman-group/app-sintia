<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0113';

include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

// Configurar cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Validar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar campos requeridos
    if (empty($_POST['contenido'])) {
        throw new Exception('La descripción es obligatoria');
    }

    if (empty($_POST['indicador'])) {
        throw new Exception('El indicador es obligatorio');
    }

    if (empty($_POST['fecha'])) {
        throw new Exception('La fecha es obligatoria');
    }

    // Obtener datos del indicador
    $indicadoresDatos = Indicadores::consultaIndicadorPeriodo($conexion, $config, $_POST['indicador'], $cargaConsultaActual, $periodoConsultaActual);

    if (empty($indicadoresDatos)) {
        throw new Exception('No se encontró el indicador seleccionado');
    }

    // Consultar valores actuales
    $valores = Actividades::consultarValoresIndicador($config, $cargaConsultaActual, $_POST["indicador"], $periodoConsultaActual);
    $porcentajeRestante = $indicadoresDatos['ipc_valor'] - $valores[0];

    // Validar número máximo de calificaciones
    if ($valores[1] >= $datosCargaActual['car_maximas_calificaciones']) {
        throw new Exception('Has alcanzado el número máximo de calificaciones permitidas (' . $datosCargaActual['car_maximas_calificaciones'] . ')');
    }

    // Preparar datos
    $infoCompartir = (!empty($_POST["compartir"]) && $_POST["compartir"] == 1) ? 1 : 0;
    $fecha = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));
    $contenido = str_replace(['ﬁ', 'ﬂ', 'ﬀ', 'ﬃ', 'ﬄ', 'ﬆ'], ['fi', 'fl', 'ff', 'ffi', 'ffl', 'st'], $_POST["contenido"]);
    $contenido = mysqli_real_escape_string($conexion, $contenido);
    $evidencia = !empty($_POST["evidencia"]) ? $_POST["evidencia"] : 0;

    $idRegistro = null;

    // Verificar si es configuración automática o manual
    if ($datosCargaActual['car_configuracion'] == CONFIG_AUTOMATICO_CALIFICACIONES) {
        // Guardar con configuración automática
        $idRegistro = Actividades::guardarCalificacionAutomatica(
            $conexionPDO, 
            $config, 
            $contenido, 
            $fecha, 
            $cargaConsultaActual, 
            $_POST["indicador"], 
            $periodoConsultaActual, 
            $infoCompartir, 
            $evidencia
        );

        // Actualizar valores de todas las actividades del indicador
        Calificaciones::actualizarValorCalificacionesDeUnIndicador($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $indicadoresDatos);
        
    } else {
        // Configuración manual
        if ($porcentajeRestante <= 0) {
            throw new Exception('Has alcanzado el 100% de valor para las calificaciones de este indicador. Porcentaje restante: ' . $porcentajeRestante . '%');
        }

        $valor = is_numeric($_POST["valor"]) ? $_POST["valor"] : 1;

        // Si el valor es mayor al adecuado, ajustarlo al porcentaje restante
        if ($valor > $porcentajeRestante && $porcentajeRestante > 0) {
            $valor = $porcentajeRestante;
        }

        // Guardar con configuración manual
        $idRegistro = Actividades::guardarCalificacionManual(
            $conexionPDO, 
            $config, 
            $contenido, 
            $fecha, 
            $cargaConsultaActual, 
            $_POST["indicador"], 
            $periodoConsultaActual, 
            $infoCompartir, 
            $valor,
            $evidencia
        );
    }

    if (empty($idRegistro)) {
        throw new Exception('No se pudo generar el ID de la actividad');
    }

    // Guardar historial de acciones
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => '✅ Actividad creada exitosamente',
        'data' => [
            'idActividad' => $idRegistro,
            'descripcion' => $contenido,
            'fecha' => $fecha,
            'indicador' => !empty($indicadoresDatos['ind_nombre']) ? $indicadoresDatos['ind_nombre'] : 'Indicador',
            'valor' => $datosCargaActual['car_configuracion'] == CONFIG_AUTOMATICO_CALIFICACIONES ? 'Automático' : $valor . '%'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '❌ Error: ' . $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '❌ Error fatal del servidor',
        'error_details' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

