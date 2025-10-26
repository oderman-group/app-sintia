<?php
/**
 * CREAR REGISTROS DE CONFIGURACIÓN FALTANTES
 * Crea automáticamente registros en general_informacion y configuracion
 * si no existen para una institución en un año específico
 */

header('Content-Type: application/json; charset=UTF-8');
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");

// Verificar permisos de desarrollador
session_start();
if (empty($_SESSION['id']) || $_SESSION['tipo'] != TIPO_DEV) {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción'
    ]);
    exit();
}

$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

if (!$conexion) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit();
}

mysqli_set_charset($conexion, "utf8mb4");

// Obtener datos de la petición
$input = json_decode(file_get_contents('php://input'), true);
$institucionId = isset($input['institucionId']) ? (int)$input['institucionId'] : 0;
$year = isset($input['year']) ? mysqli_real_escape_string($conexion, $input['year']) : date('Y');

if (empty($institucionId)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de institución no válido'
    ]);
    exit();
}

try {
    // Obtener datos de la institución
    $consultaInst = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".instituciones WHERE ins_id = {$institucionId}");
    $datosInst = mysqli_fetch_array($consultaInst, MYSQLI_BOTH);
    
    if (!$datosInst) {
        throw new Exception('Institución no encontrada');
    }
    
    $nombreInst = mysqli_real_escape_string($conexion, $datosInst['ins_nombre']);
    $siglasInst = mysqli_real_escape_string($conexion, $datosInst['ins_siglas']);
    $bdInstitucion = mysqli_real_escape_string($conexion, $datosInst['ins_bd']);
    $telefono = mysqli_real_escape_string($conexion, $datosInst['ins_telefono_principal'] ?? '');
    $propietario = mysqli_real_escape_string($conexion, $datosInst['ins_contacto_principal'] ?? '');
    
    mysqli_query($conexion, "BEGIN");
    
    $creados = [
        'general_informacion' => false,
        'configuracion' => false
    ];
    
    // VERIFICAR Y CREAR general_informacion
    $consultaExiste = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".general_informacion 
        WHERE info_institucion = {$institucionId} AND info_year = '{$year}'");
    $existeInfo = mysqli_fetch_array($consultaExiste, MYSQLI_BOTH);
    
    if ($existeInfo['total'] == 0) {
        $queryGeneralInfo = "INSERT INTO " . BD_ADMIN . ".general_informacion (
            info_institucion, 
            info_year,
            info_nombre,
            info_telefono,
            info_nit,
            info_dane,
            info_direccion,
            info_clase,
            info_caracter,
            info_calendario,
            info_jornada,
            info_horario,
            info_niveles,
            info_modalidad,
            info_propietario,
            info_logo
        ) VALUES (
            '{$institucionId}',
            '{$year}',
            '{$nombreInst}',
            '{$telefono}',
            '',
            '',
            '',
            'Privado',
            'Mixto',
            'A',
            'Completa',
            '7:00 AM - 2:00 PM',
            'Preescolar, Básica, Media',
            'Académico',
            '{$propietario}',
            'sintia-logo-2023.png'
        )";
        
        if(!mysqli_query($conexion, $queryGeneralInfo)) {
            throw new Exception('Error al crear general_informacion: ' . mysqli_error($conexion));
        }
        $creados['general_informacion'] = true;
        error_log("✅ Creado general_informacion para institución {$institucionId} año {$year}");
    } else {
        error_log("ℹ️ general_informacion ya existe para institución {$institucionId} año {$year}");
    }
    
    // VERIFICAR Y CREAR configuracion
    $consultaExisteConfig = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".configuracion 
        WHERE conf_id_institucion = {$institucionId} AND conf_agno = '{$year}'");
    $existeConfig = mysqli_fetch_array($consultaExisteConfig, MYSQLI_BOTH);
    
    if ($existeConfig['total'] == 0) {
        $queryConfiguracion = "INSERT INTO " . BD_ADMIN . ".configuracion (
            conf_id_institucion,
            conf_agno,
            conf_periodo,
            conf_nota_desde,
            conf_nota_hasta,
            conf_nota_minima_aprobar,
            conf_color_perdida,
            conf_color_ganada,
            conf_periodos_maximos,
            conf_num_registros,
            conf_decimales_notas,
            conf_agregar_porcentaje_asignaturas,
            conf_notas_categoria,
            conf_ancho_imagen,
            conf_alto_imagen,
            conf_mostrar_nombre,
            conf_max_peso_archivos,
            conf_informe_parcial,
            conf_calificaciones_acudientes,
            conf_mostrar_calificaciones_estudiantes,
            conf_orden_nombre_estudiantes,
            conf_editar_definitivas_consolidado,
            conf_cambiar_nombre_usuario,
            conf_cambiar_clave_estudiantes,
            conf_permiso_descargar_boletin,
            conf_certificado,
            conf_firma_estudiante_informe_asistencia,
            conf_permiso_edicion_years_anteriores,
            conf_porcentaje_completo_generar_informe,
            conf_ver_promedios_sabanas_docentes,
            conf_forma_mostrar_notas,
            conf_mostrar_encabezado_informes,
            conf_mostrar_pasos_matricula,
            conf_reporte_sabanas_nota_indocador,
            conf_doble_buscador,
            conf_libro_final,
            conf_estampilla_certificados,
            conf_mostrar_estudiantes_cancelados,
            conf_formato_boletin,
            conf_promedio_libro_final,
            conf_ocultar_panel_lateral_notas_estudiantes,
            conf_firma_inasistencia_planilla_notas_doc,
            conf_permiso_eliminar_cargas,
            conf_observaciones_multiples_comportamiento,
            conf_activar_encuesta,
            conf_base_datos,
            conf_puede_cambiar_grado_y_grupo
        ) VALUES (
            '{$institucionId}',
            '{$year}',
            1,
            1.0,
            5.0,
            3.0,
            '#ff0000',
            '#00aa00',
            4,
            20,
            2,
            'NO',
            1,
            150,
            150,
            1,
            5,
            0,
            1,
            1,
            1,
            0,
            'NO',
            'SI',
            0,
            1,
            1,
            0,
            1,
            1,
            'CUANTITATIVA',
            1,
            0,
            0,
            'NO',
            1,
            'NO',
            'NO',
            7,
            'TODOS_PERIODOS',
            0,
            'SI',
            'NO',
            0,
            0,
            '{$bdInstitucion}',
            0
        )";
        
        if(!mysqli_query($conexion, $queryConfiguracion)) {
            throw new Exception('Error al crear configuracion: ' . mysqli_error($conexion));
        }
        $creados['configuracion'] = true;
        error_log("✅ Creado configuracion para institución {$institucionId} año {$year}");
    } else {
        error_log("ℹ️ configuracion ya existe para institución {$institucionId} año {$year}");
    }
    
    mysqli_query($conexion, "COMMIT");
    
    echo json_encode([
        'success' => true,
        'message' => 'Registros creados exitosamente',
        'creados' => $creados,
        'institucionId' => $institucionId,
        'year' => $year
    ]);
    
} catch (Exception $e) {
    mysqli_query($conexion, "ROLLBACK");
    
    error_log("❌ Error al crear registros de configuración: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit();

