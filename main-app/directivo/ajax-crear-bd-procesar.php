<?php
/**
 * IMPORTANTE: Este archivo debe devolver SOLO JSON
 * Capturar TODO el output para evitar HTML mezclado con JSON
 */

// Capturar absolutamente TODO el output
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Función para garantizar que solo se devuelva JSON
function sendJsonResponse($response) {
    // Limpiar TODO el buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    exit;
}

// Configurar manejador de errores personalizado
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    include("session.php");
    
    // Asegurar que las constantes de BD estén disponibles
    if (!defined('BD_GENERAL')) {
        require_once(ROOT_PATH."/conexion.php");
    }
    
    require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
    require_once(ROOT_PATH."/main-app/class/Modulos.php");
    
} catch (Exception $e) {
    sendJsonResponse([
        'success' => false,
        'message' => 'Error al cargar dependencias: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}

// Limpiar cualquier output que pudiera haberse generado
ob_end_clean();

// Iniciar nuevo buffer
ob_start();

Modulos::verificarPermisoDev();

date_default_timezone_set("America/New_York");

$response = [
    'success' => false,
    'message' => '',
    'institucionId' => null,
    'usuario' => null,
    'clave' => null,
    'email' => null
];

// Verificar que la conexión esté disponible
if (!isset($conexion)) {
    $response['message'] = 'Error: Conexión a base de datos no disponible';
    sendJsonResponse($response);
}

// Verificar que las constantes de BD estén definidas
if (!defined('BD_GENERAL') || !defined('BD_ACADEMICA') || !defined('BD_ADMIN')) {
    $response['message'] = 'Error: Constantes de base de datos no definidas';
    sendJsonResponse($response);
}

try {
    // Iniciar transacción
    mysqli_query($conexion, "BEGIN");
    
    // Variables necesarias
    $nueva = $_POST['tipoInsti']; // 1 = NUEVA, 0 = RENOVACIÓN
    $fecha = date("Y-m-d");
    $fechaCompleta = date("Y-m-d H:i:s");
    
    if ($nueva == 0) {
        // ============================================
        // RENOVACIÓN DE AÑO EXISTENTE
        // ============================================
        
        $idInsti = $_POST['idInsti'];
        $year = $_POST['yearA'];
        $yearAnterior = ($year - 1);
        
        // Obtener datos de la institución
        $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".instituciones 
        WHERE ins_id = ".$idInsti." AND ins_enviroment='".ENVIROMENT."'");
        $datosInsti = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        
        $siglasBD = $datosInsti['ins_bd'];
        
        // CURSOS/GRADOS - CON CAMPOS REALES
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_grados(
            gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, 
            gra_estado, gra_grado_siguiente, gra_vocal, gra_nivel, gra_grado_anterior, gra_periodos, 
            gra_nota_minima, gra_tipo, institucion, year, gra_overall_description, gra_cover_image, 
            gra_intro_video, gra_price, gra_minimum_quota, gra_maximum_quota, gra_course_content, 
            gra_featured, gra_active, gra_duration_hours, gra_auto_enrollment, gra_fecha_creacion, 
            gra_permiso_adelantar_periodo
        )
        SELECT 
            gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, 
            gra_estado, gra_grado_siguiente, gra_vocal, gra_nivel, gra_grado_anterior, gra_periodos, 
            gra_nota_minima, gra_tipo, institucion, {$year}, gra_overall_description, gra_cover_image, 
            gra_intro_video, gra_price, gra_minimum_quota, gra_maximum_quota, gra_course_content, 
            gra_featured, gra_active, gra_duration_hours, gra_auto_enrollment, gra_fecha_creacion, 
            gra_permiso_adelantar_periodo
        FROM ".BD_ACADEMICA.".academico_grados 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_grados SET gra_periodos=4");
        
        // GRUPOS - CON CAMPOS REALES
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_grupos(
            gru_id, gru_codigo, gru_nombre, gru_jornada, gru_horario, institucion, year, 
            gru_fecha_creacion
        ) 
        SELECT 
            gru_id, gru_codigo, gru_nombre, gru_jornada, gru_horario, institucion, {$year}, 
            gru_fecha_creacion
        FROM ".BD_ACADEMICA.".academico_grupos 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        // CATEGORIA NOTAS - CON CAMPOS REALES
        // Verificar si ya existen categorías para este año antes de insertar
        $checkCategorias = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_categorias_notas 
            WHERE institucion={$idInsti} AND year={$year}");
        $checkRow = mysqli_fetch_assoc($checkCategorias);
        
        if ($checkRow['total'] == 0) {
            $resultCategorias = mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_categorias_notas(
                catn_id, catn_nombre, institucion, year
            ) 
            SELECT 
                catn_id, catn_nombre, institucion, {$year}
            FROM ".BD_ACADEMICA.".academico_categorias_notas 
            WHERE institucion={$idInsti} AND year={$yearAnterior}");
            
            if (!$resultCategorias) {
                $errorMsg = mysqli_error($conexion);
                throw new Exception("Error al copiar categorías de notas desde el año anterior. Tabla: academico_categorias_notas. Detalles: " . $errorMsg);
            }
        }
        
        // TIPOS DE NOTAS - CON CAMPOS REALES
        // Verificar si ya existen tipos de notas para este año antes de insertar
        $checkTipos = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_notas_tipos 
            WHERE institucion={$idInsti} AND year={$year}");
        $checkRowTipos = mysqli_fetch_assoc($checkTipos);
        
        if ($checkRowTipos['total'] == 0) {
            $resultTipos = mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_notas_tipos(
                notip_id, notip_nombre, notip_desde, notip_hasta, notip_categoria, notip_nombre2, 
                notip_imagen, institucion, year
            ) 
            SELECT 
                notip_id, notip_nombre, notip_desde, notip_hasta, notip_categoria, notip_nombre2, 
                notip_imagen, institucion, {$year}
            FROM ".BD_ACADEMICA.".academico_notas_tipos 
            WHERE institucion={$idInsti} AND year={$yearAnterior}");
            
            if (!$resultTipos) {
                $errorMsg = mysqli_error($conexion);
                throw new Exception("Error al copiar tipos de notas desde el año anterior. Tabla: academico_notas_tipos. Detalles: " . $errorMsg);
            }
        }
        
        // AREAS - CON CAMPOS REALES
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_areas(
            ar_id, ar_nombre, ar_posicion, institucion, year, ar_fecha_creacion
        ) 
        SELECT 
            ar_id, ar_nombre, ar_posicion, institucion, {$year}, ar_fecha_creacion
        FROM ".BD_ACADEMICA.".academico_areas 
        WHERE institucion={$idInsti} AND year={$yearAnterior}");
        
        // MATERIAS - CON CAMPOS REALES
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_materias(
            mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area, mat_oficial, mat_portada, 
            mat_valor, institucion, year, mat_sumar_promedio, mate_fecha_creacion
        ) 
        SELECT 
            mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area, mat_oficial, mat_portada, 
            mat_valor, institucion, {$year}, mat_sumar_promedio, mate_fecha_creacion
        FROM ".BD_ACADEMICA.".academico_materias 
        WHERE institucion={$idInsti} AND year={$yearAnterior}");
        
        // USUARIOS - ACTUALIZADO CON TODOS LOS CAMPOS
        $sqlDelete = "DELETE FROM ".BD_GENERAL.".usuarios
        WHERE institucion={$idInsti} AND year={$year}";
        mysqli_query($conexion, $sqlDelete);
        
        mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(
            uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, 
            uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, 
            uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, 
            uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, 
            uss_responsable_registro, institucion, year, uss_apellido1, uss_apellido2, 
            uss_nombre2, uss_documento, uss_lugar_expedicion, uss_direccion, uss_estado_civil,
            uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, 
            uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_tipo_documento,
            uss_empresa_labor, uss_firma, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda,
            uss_medio_transporte, uss_notificacion, uss_solicitar_datos, 
            uss_institucion_municipio, uss_intentos_fallidos, uss_parentezco, 
            uss_cambio_notificacion
        ) 
        SELECT 
            uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, 
            uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, 
            uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, 
            uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, 
            uss_responsable_registro, institucion, {$year}, uss_apellido1, uss_apellido2, 
            uss_nombre2, uss_documento, uss_lugar_expedicion, uss_direccion, uss_estado_civil,
            uss_profesion, uss_estado_laboral, uss_nivel_academico, uss_religion, 
            uss_tiene_hijos, uss_numero_hijos, uss_lugar_nacimiento, uss_tipo_documento,
            uss_empresa_labor, uss_firma, uss_tipo_negocio, uss_estrato, uss_tipo_vivienda,
            uss_medio_transporte, uss_notificacion, uss_solicitar_datos, 
            uss_institucion_municipio, 0, uss_parentezco, 0
        FROM ".BD_GENERAL.".usuarios 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        // MATRICULAS - CON CAMPOS REALES
        $sqlDelete = "DELETE FROM ".BD_ACADEMICA.".academico_matriculas 
        WHERE institucion={$idInsti} AND year={$year}";
        mysqli_query($conexion, $sqlDelete);
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(
            mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, 
            mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, 
            mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, 
            mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, 
            mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, 
            mat_acudiente, mat_folio, mat_codigo_tesoreria, institucion, year, mat_etnia, 
            mat_tiene_discapacidad, mat_tipo_situacion, mat_fecha_creacion, mat_forma_creacion, 
            mat_padre, mat_madre, mat_numero_matricula, mat_nombre2, mat_acudiente2,
            mat_inclusion, mat_extranjero, mat_modalidad_estudio, mat_privilegio1, 
            mat_privilegio2, mat_privilegio3, mat_celular2, mat_tipo_sangre, mat_eps
        ) 
        SELECT 
            mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, 
            mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, 
            mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, 
            mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, 
            mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, 
            mat_acudiente, mat_folio, mat_codigo_tesoreria, institucion, {$year}, mat_etnia, 
            mat_tiene_discapacidad, mat_tipo_situacion, mat_fecha_creacion, mat_forma_creacion, 
            mat_padre, mat_madre, mat_numero_matricula, mat_nombre2, mat_acudiente2,
            mat_inclusion, mat_extranjero, mat_modalidad_estudio, mat_privilegio1, 
            mat_privilegio2, mat_privilegio3, mat_celular2, mat_tipo_sangre, mat_eps
        FROM ".BD_ACADEMICA.".academico_matriculas 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        // Reiniciar datos de matrícula
        mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_matriculas SET 
            mat_fecha='0000-00-00', 
            mat_estado_matricula=4, 
            mat_promocionado=0, 
            mat_estado_agno=0 
        WHERE institucion={$idInsti} AND year={$year}
        ");
        
        // USUARIOS POR ESTUDIANTES - CON CAMPOS REALES
        mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(
            upe_id, upe_id_usuario, upe_id_estudiante, institucion, year, upe_parentezco
        ) 
        SELECT 
            upe_id, upe_id_usuario, upe_id_estudiante, institucion, {$year}, upe_parentezco
        FROM ".BD_GENERAL.".usuarios_por_estudiantes 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        // CARGAS - ACTUALIZADO CON TODOS LOS CAMPOS
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_cargas(
            car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, 
            car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, 
            car_configuracion, car_valor_indicador, car_posicion_docente, car_permiso2, 
            car_maximos_indicadores, car_maximas_calificaciones, car_saberes_indicador, 
            car_indicador_automatico, car_observaciones_boletin, car_tematica, 
            car_curso_extension, institucion, year, car_estado, car_fecha_automatica,
            car_evidencia, car_inicio, car_fin, car_primer_acceso_docente, car_ultimo_acceso_docente,
            car_fecha_generar_informe_auto
        ) 
        SELECT 
            car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, 
            car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, 
            car_configuracion, car_valor_indicador, car_posicion_docente, car_permiso2, 
            car_maximos_indicadores, car_maximas_calificaciones, car_saberes_indicador, 
            car_indicador_automatico, car_observaciones_boletin, car_tematica, 
            car_curso_extension, institucion, {$year}, 'SINTIA', car_fecha_automatica,
            car_evidencia, car_inicio, car_fin, car_primer_acceso_docente, car_ultimo_acceso_docente,
            car_fecha_generar_informe_auto
        FROM ".BD_ACADEMICA.".academico_cargas 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        // Actualizar cargas al período 1
        mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_cargas SET 
            car_periodo=1,
            car_estado = 'SINTIA'
        WHERE institucion={$idInsti} AND year={$year}
        ");
        
        // DOCUMENTOS ADJUNTOS PARA ESTUDIANTES
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_matriculas_adjuntos(
            ama_id_estudiante, ama_documento, ama_id_responsable, ama_visible, 
            ama_fecha_registro, institucion, year, ama_titulo, ama_descripcion
        ) 
        SELECT 
            ama_id_estudiante, ama_documento, ama_id_responsable, ama_visible, 
            ama_fecha_registro, institucion, {$year}, ama_titulo, ama_descripcion 
        FROM ".BD_ACADEMICA.".academico_matriculas_adjuntos 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        // SUSCRIPCIÓN DE USUARIOS DIRECTIVOS A NOTIFICACIONES
        mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_notificaciones(
            upn_tipo_notificacion, upn_usuario, institucion, year
        ) 
        SELECT 
            upn_tipo_notificacion, upn_usuario, institucion, {$year}
        FROM ".BD_GENERAL.".usuarios_notificaciones 
        WHERE institucion={$idInsti} AND year={$yearAnterior}
        ");
        
        // CREAR CONFIGURACIÓN DE LA INSTITUCIÓN - ACTUALIZADA CON TODOS LOS CAMPOS
        mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".configuracion (
            conf_agno, conf_periodo, conf_nota_desde, conf_nota_hasta, conf_nota_minima_aprobar,
            conf_color_perdida, conf_color_ganada, conf_saldo_pendiente, conf_num_restaurar,
            conf_restaurar_cantidad, conf_color_borde, conf_color_encabezado, conf_tam_borde,
            conf_num_materias_perder_agno, conf_inicio_matrucula, conf_fin_matricula,
            conf_apertura_academica, conf_clausura_academica, conf_periodos_maximos,
            conf_num_indicadores, conf_valor_indicadores, conf_notas_categoria,
            conf_id_institucion, conf_base_datos, conf_servidor, conf_num_registros,
            conf_agregar_porcentaje_asignaturas, conf_fecha_parcial, conf_descripcion_parcial,
            conf_ancho_imagen, conf_alto_imagen, conf_mostrar_nombre, conf_deuda,
            conf_permiso_eliminar_cargas, conf_concepto, conf_inicio_recibos_ingreso,
            conf_inicio_recibos_egreso, conf_decimales_notas, conf_activar_encuesta,
            conf_sin_nota_numerica, conf_numero_factura, conf_max_peso_archivos,
            conf_informe_parcial, conf_ver_observador, conf_ficha_estudiantil,
            conf_orden_nombre_estudiantes, conf_editar_definitivas_consolidado,
            conf_solicitar_acudiente_2, conf_mostrar_campos, conf_calificaciones_acudientes,
            conf_mostrar_calificaciones_estudiantes, conf_observaciones_multiples_comportamiento,
            conf_cambiar_nombre_usuario, conf_cambiar_clave_estudiantes,
            conf_permiso_descargar_boletin, conf_certificado,
            conf_firma_estudiante_informe_asistencia, conf_permiso_edicion_years_anteriores,
            conf_porcentaje_completo_generar_informe, conf_ver_promedios_sabanas_docentes,
            conf_forma_mostrar_notas, conf_pie_factura, conf_mostrar_encabezado_informes,
            conf_mostrar_pasos_matricula, conf_reporte_sabanas_nota_indocador,
            conf_doble_buscador, conf_libro_final, conf_estampilla_certificados,
            conf_mostrar_estudiantes_cancelados, conf_formato_boletin,
            conf_promedio_libro_final, conf_ocultar_panel_lateral_notas_estudiantes,
            conf_firma_inasistencia_planilla_notas_doc, conf_puede_cambiar_grado_y_grupo
        ) 
        SELECT 
            '".$year."', conf_periodo, conf_nota_desde, conf_nota_hasta, conf_nota_minima_aprobar,
            conf_color_perdida, conf_color_ganada, conf_saldo_pendiente, conf_num_restaurar,
            conf_restaurar_cantidad, conf_color_borde, conf_color_encabezado, conf_tam_borde,
            conf_num_materias_perder_agno, conf_inicio_matrucula, conf_fin_matricula,
            conf_apertura_academica, conf_clausura_academica, conf_periodos_maximos,
            conf_num_indicadores, conf_valor_indicadores, conf_notas_categoria,
            conf_id_institucion, conf_base_datos, conf_servidor, conf_num_registros,
            conf_agregar_porcentaje_asignaturas, conf_fecha_parcial, conf_descripcion_parcial,
            conf_ancho_imagen, conf_alto_imagen, conf_mostrar_nombre, conf_deuda,
            conf_permiso_eliminar_cargas, conf_concepto, conf_inicio_recibos_ingreso,
            conf_inicio_recibos_egreso, conf_decimales_notas, conf_activar_encuesta,
            conf_sin_nota_numerica, conf_numero_factura, conf_max_peso_archivos,
            conf_informe_parcial, conf_ver_observador, conf_ficha_estudiantil,
            conf_orden_nombre_estudiantes, conf_editar_definitivas_consolidado,
            conf_solicitar_acudiente_2, conf_mostrar_campos, conf_calificaciones_acudientes,
            conf_mostrar_calificaciones_estudiantes, conf_observaciones_multiples_comportamiento,
            conf_cambiar_nombre_usuario, conf_cambiar_clave_estudiantes,
            conf_permiso_descargar_boletin, conf_certificado,
            conf_firma_estudiante_informe_asistencia, conf_permiso_edicion_years_anteriores,
            conf_porcentaje_completo_generar_informe, conf_ver_promedios_sabanas_docentes,
            conf_forma_mostrar_notas, conf_pie_factura, conf_mostrar_encabezado_informes,
            conf_mostrar_pasos_matricula, conf_reporte_sabanas_nota_indocador,
            conf_doble_buscador, conf_libro_final, conf_estampilla_certificados,
            conf_mostrar_estudiantes_cancelados, conf_formato_boletin,
            conf_promedio_libro_final, conf_ocultar_panel_lateral_notas_estudiantes,
            conf_firma_inasistencia_planilla_notas_doc, conf_puede_cambiar_grado_y_grupo
        FROM ".BD_ADMIN.".configuracion 
        WHERE conf_agno='".$yearAnterior."' AND conf_id_institucion='".$idInsti."'");
        
        // CONSULTAR Y ACTUALIZAR AÑOS DE LA INSTITUCIÓN
        $consultaInsti = mysqli_query($conexion, "SELECT ins_years, ins_email_contacto, ins_contacto_principal, ins_nombre FROM ".BD_ADMIN.".instituciones WHERE ins_id='".$idInsti."'");
        $datosInsti = mysqli_fetch_array($consultaInsti, MYSQLI_BOTH);
        $yearArray  = explode(",", $datosInsti['ins_years']);
        $yearStart  = $yearArray[0];
        
        mysqli_query($conexion, "UPDATE ".BD_ADMIN.".instituciones SET 
            ins_years='".$yearStart.",".$year."',
            ins_year_default='".$year."'
        WHERE ins_id='".$idInsti."'");
        
        // INFORMACIÓN GENERAL DE LA INSTITUCIÓN
        mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".general_informacion (
            info_rector, info_secretaria_academica, info_logo, info_nit, info_nombre,
            info_direccion, info_telefono, info_clase, info_caracter, info_calendario,
            info_jornada, info_horario, info_niveles, info_modalidad, info_propietario,
            info_coordinador_academico, info_tesorero, info_dane, info_ciudad,
            info_resolucion, info_decreto_plan_estudio, info_institucion, info_year
        ) 
        SELECT 
            info_rector, info_secretaria_academica, info_logo, info_nit, info_nombre,
            info_direccion, info_telefono, info_clase, info_caracter, info_calendario,
            info_jornada, info_horario, info_niveles, info_modalidad, info_propietario,
            info_coordinador_academico, info_tesorero, info_dane, info_ciudad,
            info_resolucion, info_decreto_plan_estudio, info_institucion, '".$year."' 
        FROM ".BD_ADMIN.".general_informacion 
        WHERE info_institucion='".$idInsti."' AND info_year='".$yearAnterior."'");
        
        // CONSULTAMOS SI LA INSTITUCIÓN TIENE EL MODULO DE INSCRIPCIONES
        $consultaModuloInscripcion = mysqli_query($conexion, "SELECT ipmod_modulo FROM ".BD_ADMIN.".instituciones_modulos 
        WHERE ipmod_institucion='".$idInsti."' AND ipmod_modulo=".Modulos::MODULO_INSCRIPCIONES."");
        
        if (mysqli_num_rows($consultaModuloInscripcion) > 0) {
            // Configuración para inscripciones del nuevo año
            mysqli_query($conexion, "INSERT INTO ".BD_ADMISIONES.".config_instituciones (
                cfgi_id_institucion, cfgi_year, cfgi_color_barra_superior, cfgi_valor_inscripcion,
                cfgi_inscripciones_activas, cfgi_texto_inicial, cfgi_banner_inicial,
                cfgi_politicas_texto, cfgi_politicas_adjunto, cfgi_color_texto,
                cfgi_activar_boton_pagar_prematricula, cfgi_link_boton_pagar_prematricula,
                cfgi_mostrar_banner, cfgi_mostrar_politicas, cfgi_texto_info_cuenta,
                cfgi_year_inscripcion
            ) 
            SELECT 
                cfgi_id_institucion, '".$year."', cfgi_color_barra_superior, cfgi_valor_inscripcion,
                cfgi_inscripciones_activas, cfgi_texto_inicial, cfgi_banner_inicial,
                cfgi_politicas_texto, cfgi_politicas_adjunto, cfgi_color_texto,
                cfgi_activar_boton_pagar_prematricula, cfgi_link_boton_pagar_prematricula,
                cfgi_mostrar_banner, cfgi_mostrar_politicas, cfgi_texto_info_cuenta,
                cfgi_year_inscripcion 
            FROM ".BD_ADMISIONES.".config_instituciones 
            WHERE cfgi_id_institucion='".$idInsti."' AND cfgi_year='".$yearAnterior."'");
        }
        
        // ENVIAR CORREO DE CONFIRMACIÓN DE RENOVACIÓN (si está marcado)
        $mensajeCorreo = '';
        $correoExitoso = false;
        $enviarCorreoRenovacion = ($_POST['enviarCorreoRenovacion'] ?? '0') === '1';
        
        if ($enviarCorreoRenovacion) {
            try {
                // Obtener datos del contacto principal de la institución
                $emailContacto = !empty($datosInsti['ins_email_contacto']) ? $datosInsti['ins_email_contacto'] : null;
                $nombreContacto = !empty($datosInsti['ins_contacto_principal']) ? $datosInsti['ins_contacto_principal'] : 'Contacto Principal';
                $nombreInstitucion = !empty($datosInsti['ins_nombre']) ? $datosInsti['ins_nombre'] : 'Institución';
                
                if ($emailContacto) {
                    $data = [
                        'institucion_id'   => $idInsti,
                        'institucion_agno' => $year,
                        'institucion_nombre' => $nombreInstitucion,
                        'usuario_email'    => $emailContacto,
                        'usuario_nombre'   => $nombreContacto,
                        'year_anterior'    => $yearAnterior,
                        'year_nuevo'       => $year,
                        'url_acceso'       => REDIRECT_ROUTE.'/index.php?inst='.base64_encode($idInsti).'&year='.base64_encode($year)
                    ];
                    $asunto = 'Año Académico '.$year.' Renovado Exitosamente - '.$nombreInstitucion;
                    $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-renovacion-ano.php';
                    
                    // EnviarEmail::enviar() retorna void, lanza excepción si falla
                    EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
                    
                    // Si llegamos aquí, el correo se envió exitosamente
                    $mensajeCorreo = '✉️ Correo de confirmación enviado exitosamente a '.$emailContacto;
                    $correoExitoso = true;
                } else {
                    $mensajeCorreo = '⚠️ No se encontró email del contacto principal en la institución.';
                }
                
            } catch(Exception $emailError) {
                $mensajeCorreo = '⚠️ No se pudo enviar el correo de confirmación.';
                $correoExitoso = false;
                error_log("Error al enviar correo de renovación - Institución: ".$idInsti." - Error: ".$emailError->getMessage());
            }
        }
        
        $response['institucionId'] = $idInsti;
        $response['message'] = 'Año '.$year.' renovado exitosamente para la institución';
        $response['correoEnviado'] = $correoExitoso;
        $response['mensajeCorreo'] = $mensajeCorreo;
        
    } else {
        // ============================================
        // NUEVA INSTITUCIÓN
        // ============================================
        
        $siglasBD = $_POST['siglasBD'];
        $nombreInsti = $_POST['nombreInsti'];
        $siglasInst = $_POST['siglasInst'];
        $year = $_POST['yearN'];
        $bdInstitucion = BD_PREFIX.$siglasBD;
        
        // CREAR LA INSTITUCIÓN
        $dataToInsert = array(
            'ins_nombre' => $nombreInsti,
            'ins_fecha_inicio' => $fechaCompleta,
            'ins_telefono_principal' => $_POST['celular'],
            'ins_contacto_principal' => $_POST['nombre1']." ".$_POST['nombre2']." ".$_POST['apellido1']." ".$_POST['apellido2'],
            'ins_cargo_contacto' => NULL,
            'ins_celular_contacto' => $_POST['celular'],
            'ins_email_contacto' => $_POST['email'],
            'ins_email_institucion' => NULL,
            'ins_ciudad' => NULL,
            'ins_enviroment' => ENVIROMENT,
            'ins_nit' => NULL,
            'ins_medio_info' => NULL,
            'ins_estado' => 1,
            'ins_url_acceso' => NULL,
            'ins_bd' => $bdInstitucion,
            'ins_deuda' => NULL,
            'ins_valor_deuda' => NULL,
            'ins_concepto_deuda' => NULL,
            'ins_bloqueada' => 0,
            'ins_years' => $year . "," . $year,
            'ins_notificaciones_acudientes' => 0,
            'ins_siglas' => $siglasInst,
            'ins_fecha_renovacion' => $fechaCompleta,
            'ins_id_plan' => 1,
            'ins_year_default' => $year
        );
        
        $query = "INSERT INTO ".BD_ADMIN.".instituciones (";
        $columns = array_keys($dataToInsert);
        $values = array_values($dataToInsert);
        
        $query .= implode(', ', $columns);
        $query .= ") VALUES (";
        $query .= "'" . implode("', '", $values) . "'";
        $query .= ")";
        
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al crear institución: ' . mysqli_error($conexion));
        }
        $idInsti = mysqli_insert_id($conexion);
        
        if (!$idInsti) {
            throw new Exception('No se pudo obtener el ID de la institución creada');
        }
        
        // ASIGNAR MÓDULOS A LA INSTITUCIÓN
        if (!mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".instituciones_modulos (ipmod_institucion,ipmod_modulo) 
        VALUES ($idInsti,4),($idInsti,5),($idInsti,7),($idInsti,17),($idInsti,22)")) {
            throw new Exception('Error al asignar módulos: ' . mysqli_error($conexion));
        }
        
        // CREAR CONFIGURACIÓN - CON TODOS LOS CAMPOS
        if (!mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".configuracion (
            conf_agno, conf_periodo, conf_nota_desde, conf_nota_hasta, conf_nota_minima_aprobar,
            conf_color_perdida, conf_color_ganada, conf_periodos_maximos, conf_num_indicadores,
            conf_notas_categoria, conf_id_institucion, conf_base_datos, conf_num_registros,
            conf_decimales_notas, conf_max_peso_archivos, conf_informe_parcial, 
            conf_ver_observador, conf_ficha_estudiantil, conf_solicitar_acudiente_2,
            conf_mostrar_campos, conf_calificaciones_acudientes, conf_mostrar_calificaciones_estudiantes,
            conf_orden_nombre_estudiantes, conf_editar_definitivas_consolidado,
            conf_observaciones_multiples_comportamiento, conf_cambiar_nombre_usuario,
            conf_cambiar_clave_estudiantes, conf_permiso_descargar_boletin, conf_certificado,
            conf_firma_estudiante_informe_asistencia, conf_permiso_edicion_years_anteriores,
            conf_porcentaje_completo_generar_informe, conf_ver_promedios_sabanas_docentes,
            conf_forma_mostrar_notas, conf_mostrar_encabezado_informes, conf_mostrar_pasos_matricula,
            conf_reporte_sabanas_nota_indocador, conf_doble_buscador, conf_libro_final,
            conf_estampilla_certificados, conf_mostrar_estudiantes_cancelados, conf_formato_boletin,
            conf_promedio_libro_final, conf_ocultar_panel_lateral_notas_estudiantes,
            conf_firma_inasistencia_planilla_notas_doc, conf_puede_cambiar_grado_y_grupo,
            conf_color_borde, conf_color_encabezado, conf_tam_borde, conf_ancho_imagen, conf_alto_imagen,
            conf_mostrar_nombre, conf_permiso_eliminar_cargas, conf_agregar_porcentaje_asignaturas
        ) VALUES (
            '".$year."', 1, 1, 5, 3, '#e10000', '#0000d5', 4, NULL, 1, '".$idInsti."', 
            '".$bdInstitucion."', 20, 2, '5', 0, 0, 0, 'NO', 1, 1, 1, 1, 0, 0, 'SI', 'SI', 1, 1, 1, 1, 
            1, 1, 'CUANTITATIVA', 1, 0, 0, '0', 1, 'NO', 'NO', 7, 'TODOS_PERIODOS', 0, 'SI', 0, 
            '#000000', '#ff0080', 1, '200', '150', 1, 'NO', 'NO'
        )")) {
            throw new Exception('Error al crear configuración: ' . mysqli_error($conexion));
        }
        
        // CREAR INFORMACIÓN GENERAL
        mysqli_query($conexion, "INSERT INTO ".BD_ADMIN.".general_informacion (
            info_rector, info_secretaria_academica, info_logo, info_nit, info_nombre,
            info_direccion, info_telefono, info_clase, info_caracter, info_calendario,
            info_jornada, info_horario, info_niveles, info_modalidad, info_propietario,
            info_coordinador_academico, info_tesorero, info_institucion, info_year
        ) VALUES (
            '2', '2', 'sintia-logo-2023.png', '0000000000-0', '".$nombreInsti."',
            'Cra 00 # 00-00', '(000)000-0000', 'Privado', 'Mixto', 'A', 'Mañana',
            '6:00 am - 12:30 pm', 'Preescolar, Basica, Media', 'Academica', 
            'PROPIETARIO PRUEBA', '2', '2', '".$idInsti."', '".$year."'
        )");
        
        // CURSOS INICIALES
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_grados(
            gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, 
            gra_estado, institucion, year, gra_grado_siguiente, gra_vocal, gra_nivel, gra_grado_anterior, 
            gra_periodos, gra_nota_minima, gra_tipo
        ) VALUES 
        ('1','0','PRIMERO',8,0,0,1,'".$idInsti."','".$year."','2',NULL,2,'15',4,NULL,'grupal'),
        ('2','0','SEGUNDO',8,0,0,1,'".$idInsti."','".$year."','3',NULL,2,'1',4,NULL,'grupal'),
        ('3','0','TERCERO',8,0,0,1,'".$idInsti."','".$year."','4',NULL,2,'2',4,NULL,'grupal'),
        ('4','0','CUARTO',8,0,0,1,'".$idInsti."','".$year."','5',NULL,2,'3',4,NULL,'grupal'),
        ('5','0','QUINTO',8,0,0,1,'".$idInsti."','".$year."','6',NULL,2,'4',4,NULL,'grupal'),
        ('6','0','SEXTO',8,0,0,1,'".$idInsti."','".$year."','7',NULL,3,'5',4,NULL,'grupal'),
        ('7','0','SEPTIMO',8,0,0,1,'".$idInsti."','".$year."','8',NULL,3,'6',4,NULL,'grupal'),
        ('8','0','OCTAVO',8,0,0,1,'".$idInsti."','".$year."','9',NULL,3,'7',4,NULL,'grupal'),
        ('9','0','NOVENO',8,0,0,1,'".$idInsti."','".$year."','10',NULL,3,'8',4,NULL,'grupal'),
        ('10','0','DECIMO',8,0,0,1,'".$idInsti."','".$year."','11',NULL,4,'9',4,NULL,'grupal'),
        ('11','0','UNDECIMO',8,0,0,1,'".$idInsti."','".$year."','0',NULL,4,'10',4,NULL,'grupal'),
        ('12','0','PARVULOS',8,0,0,1,'".$idInsti."','".$year."','13',NULL,1,'0',4,NULL,'grupal'),
        ('13','0','PREJARDIN',8,0,0,1,'".$idInsti."','".$year."','14',NULL,1,'12',4,NULL,'grupal'),
        ('14','0','JARDIN',8,0,0,1,'".$idInsti."','".$year."','15',NULL,1,'13',4,NULL,'grupal'),
        ('15','0','TRANSICION',8,0,0,1,'".$idInsti."','".$year."','1',NULL,1,'14',4,NULL,'grupal')
        ");
        
        // GRUPOS INICIALES
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_grupos(
            gru_id, gru_codigo, gru_nombre, institucion, year
        ) VALUES 
        ('1',1267,'A','".$idInsti."','".$year."'),
        ('2',1268,'B','".$idInsti."','".$year."'),
        ('3',1269,'C','".$idInsti."','".$year."'),
        ('4',1270,'Sin grupo','".$idInsti."','".$year."')
        ");
        
        // CATEGORIAS DE NOTAS
        // Verificar si ya existen categorías para este año antes de insertar
        $checkCategorias = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_categorias_notas 
            WHERE institucion={$idInsti} AND year={$year}");
        $checkRow = mysqli_fetch_assoc($checkCategorias);
        
        if ($checkRow['total'] == 0) {
            $resultCategorias = mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_categorias_notas(
                catn_id, catn_nombre, institucion, year
            ) VALUES 
            ('1','Desempeños (Bajo a Superior)','".$idInsti."','".$year."'),
            ('2','Letras (D a E)','".$idInsti."','".$year."'),
            ('3','Numerica de 0 a 100','".$idInsti."','".$year."'),
            ('4','Caritas (Llorando - Contento)','".$idInsti."','".$year."')
            ");
            
            if (!$resultCategorias) {
                $errorMsg = mysqli_error($conexion);
                throw new Exception("Error al crear categorías de notas iniciales. Tabla: academico_categorias_notas. Detalles: " . $errorMsg);
            }
        }
        
        // TIPOS DE NOTAS
        // Verificar si ya existen tipos de notas para este año antes de insertar
        $checkTipos = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_notas_tipos 
            WHERE institucion={$idInsti} AND year={$year}");
        $checkRowTipos = mysqli_fetch_assoc($checkTipos);
        
        if ($checkRowTipos['total'] == 0) {
            $resultTipos = mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_notas_tipos(
                notip_id, notip_nombre, notip_desde, notip_hasta, notip_categoria, notip_imagen, institucion, year
            ) VALUES 
            ('1','Bajo',1.00,3.49,'1','bajo.png','".$idInsti."','".$year."'),
            ('2','Basico',3.50,3.99,'1','bas.png','".$idInsti."','".$year."'),
            ('3','Alto',4.00,4.59,'1','alto.png','".$idInsti."','".$year."'),
            ('4','Superior',4.60,5.00,'1','sup.png','".$idInsti."','".$year."')
            ");
            
            if (!$resultTipos) {
                $errorMsg = mysqli_error($conexion);
                throw new Exception("Error al crear tipos de notas iniciales. Tabla: academico_notas_tipos. Detalles: " . $errorMsg);
            }
        }
        
        // AREAS
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_areas(
            ar_id, ar_nombre, ar_posicion, institucion, year
        ) VALUES ('1','AREA DE PRUEBA',1,'".$idInsti."','".$year."')");
        
        // MATERIAS
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_materias(
            mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area, institucion, year
        ) VALUES ('1','1','MATERIA DE PRUEBA','PRU','1','".$idInsti."','".$year."')");
        
        // USUARIOS
        $clave = '12345678';
        mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios(
            uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_nombre2, uss_apellido1, 
            uss_apellido2, uss_estado, uss_foto, uss_idioma, uss_tema, uss_ocupacion, uss_email, 
            uss_permiso1, uss_celular, uss_genero, uss_bloqueado, uss_tipo_documento, uss_documento, 
            institucion, year, uss_portada
        ) VALUES 
        ('1','sintia-".$idInsti."',SHA1('sintia2014$'),1,'ADMINISTRACIÓN',NULL,'SINTIA',NULL,
        0,'default.png',1,'orange','Administrador','soporte@plataformasintia.com',1298,
        '(313) 591-2073',126,0,NULL,NULL,'".$idInsti."','".$year."','default.png'),
        
        ('2','".$_POST['documento']."-".$idInsti."',SHA1('".$clave."'),5,'".$_POST['nombre1']."',
        '".$_POST['nombre2']."','".$_POST['apellido1']."','".$_POST['apellido2']."',
        0,'default.png',1,'orange','DIRECTIVO','".$_POST['email']."',1298,'".$_POST['celular']."',
        126,0,'".$_POST['tipoDoc']."','".$_POST['documento']."','".$idInsti."','".$year."','default.png'),
        
        ('3','pruebaDC-".$idInsti."',SHA1('".$clave."'),2,'USUARIO',NULL,'DOCENTE',NULL,
        0,'default.png',1,'orange','DOCENTE',NULL,0,NULL,126,0,NULL,NULL,'".$idInsti."','".$year."','default.png'),
        
        ('4','pruebaAC-".$idInsti."',SHA1('".$clave."'),3,'USUARIO',NULL,'ACUDIENTE',NULL,
        0,'default.png',1,'orange','ACUDIENTE',NULL,0,NULL,126,0,NULL,NULL,'".$idInsti."','".$year."','default.png'),
        
        ('5','pruebaES-".$idInsti."',SHA1('".$clave."'),4,'USUARIO',NULL,'ESTUDIANTE',NULL,
        0,'default.png',1,'orange','ESTUDIANTE',NULL,0,NULL,126,0,NULL,NULL,'".$idInsti."','".$year."','default.png')
        ");
        
        // MATRICULAS
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(
            mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, 
            mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, 
            mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, 
            mat_barrio, mat_estrato, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, 
            mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, institucion, year
        ) VALUES (
            '1','00001','0000-00-00 00:00:00','PRUEBA','DE','ESTUDIANTE','1','1',126,'1993-10-21',
            '1',108,'0000000000','1',111,'Cra 00 #00-00','B. Prueba',116,129,1,'5',0,'4',0,'0',0,
            '".$idInsti."','".$year."'
        )");
        
        // USUARIOS POR ESTUDIANTES
        mysqli_query($conexion, "INSERT INTO ".BD_GENERAL.".usuarios_por_estudiantes(
            upe_id, upe_id_usuario, upe_id_estudiante, upe_parentezco, institucion, year
        ) VALUES ('1','4','1','Padre','".$idInsti."','".$year."')");
        
        // CARGAS
        mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_cargas(
            car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, 
            car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, 
            institucion, year, car_configuracion, car_valor_indicador, car_posicion_docente, 
            car_permiso2, car_maximos_indicadores, car_maximas_calificaciones, 
            car_indicador_automatico, car_estado
        ) VALUES (
            '1','3','1','1','1',1,1,1,'3',2,'0000-00-00 00:00:00',2,'".$idInsti."','".$year."',
            0,0,1,0,10,100,NULL,'SINTIA'
        )");
        
        // Enviar correo de bienvenida
        try {
            $data = [
                'institucion_id'   => $idInsti,
                'institucion_agno' => $year,
                'usuario_id'       => '2',
                'usuario_email'    => $_POST['email'],
                'usuario_nombre'   => $_POST["nombre1"]." ".$_POST["apellido1"],
                'usuario_usuario'  => $_POST["documento"]."-".$idInsti,
                'usuario_clave'    => $clave
            ];
            $asunto = 'Bienvenido a la Plataforma SINTIA';
            $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-bienvenida.php';
            
            EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
        } catch(Exception $emailError) {
            // Si falla el envío de email, continuar sin romper el proceso
            // El error se loguea pero no detiene la creación
        }
        
        $response['institucionId'] = $idInsti;
        $response['usuario'] = $_POST["documento"]."-".$idInsti;
        $response['clave'] = $clave;
        $response['email'] = $_POST['email'];
        $response['message'] = 'Institución creada exitosamente';
    }
    
    // Confirmar transacción
    mysqli_query($conexion, "COMMIT");
    
    $response['success'] = true;
    
} catch(Exception $e) {
    // Revertir en caso de error
    if (isset($conexion)) {
        mysqli_query($conexion, "ROLLBACK");
    }
    
    // Extraer información del error de MySQL si está disponible
    $errorMessage = $e->getMessage();
    $tabla = 'Desconocida';
    $operacion = 'Desconocida';
    
    // Intentar identificar tabla y operación del mensaje de error
    if (preg_match('/Tabla:\s*([^\s.]+)/i', $errorMessage, $matches)) {
        $tabla = $matches[1];
    } elseif (preg_match('/(INSERT|UPDATE|DELETE)\s+INTO\s+[^`]*`?([^`\s.]+)`?/i', $errorMessage, $matches)) {
        $operacion = strtoupper($matches[1]);
        if (isset($matches[2])) {
            $tabla = $matches[2];
        }
    } elseif (preg_match('/academico_(\w+)/i', $errorMessage, $matches)) {
        $tabla = 'academico_' . $matches[1];
    } elseif (preg_match('/Duplicate entry.*for key.*catn_id/i', $errorMessage)) {
        $tabla = 'academico_categorias_notas';
        $operacion = 'INSERT';
    } elseif (preg_match('/Duplicate entry.*for key.*notip_id/i', $errorMessage)) {
        $tabla = 'academico_notas_tipos';
        $operacion = 'INSERT';
    } elseif (preg_match('/Duplicate entry.*for key.*(\w+_id)/i', $errorMessage, $matches)) {
        // Intentar identificar tabla por el nombre del campo de clave
        $campoId = $matches[1];
        $mapaTablas = [
            'catn_id' => 'academico_categorias_notas',
            'notip_id' => 'academico_notas_tipos',
            'gra_id' => 'academico_grados',
            'gru_id' => 'academico_grupos',
            'ar_id' => 'academico_areas',
            'mat_id' => 'academico_materias',
            'car_id' => 'academico_cargas',
            'conf_id' => 'configuracion',
            'info_id' => 'general_informacion'
        ];
        if (isset($mapaTablas[$campoId])) {
            $tabla = $mapaTablas[$campoId];
            $operacion = 'INSERT';
        }
    }
    
    // Construir mensaje más descriptivo
    $tipoProceso = isset($nueva) && $nueva == '1' ? 'creación' : 'renovación';
    $mensajeUsuario = "Error en el proceso de " . $tipoProceso . ":\n\n";
    $mensajeUsuario .= "📋 Tabla: " . $tabla . "\n";
    $mensajeUsuario .= "⚙️ Operación: " . $operacion . "\n";
    $mensajeUsuario .= "❌ Error: " . $errorMessage;
    
    // Si es un error de duplicado, agregar sugerencia
    if (preg_match('/Duplicate entry/i', $errorMessage)) {
        $mensajeUsuario .= "\n\n💡 Sugerencia: Ya existe un registro con estos valores. Verifica si el año ya fue creado anteriormente.";
    }
    
    $response['success'] = false;
    $response['message'] = $mensajeUsuario;
    $response['error_details'] = [
        'code' => $e->getCode(),
        'line' => $e->getLine(),
        'file' => basename($e->getFile()),
        'tabla' => $tabla,
        'operacion' => $operacion,
        'error_original' => $errorMessage
    ];
    
    // Log del error sin generar HTML
    try {
        require_once(ROOT_PATH."/main-app/class/Utilidades.php");
        Utilidades::logError($e);
    } catch(Exception $logError) {
        // Si falla el log, no hacer nada para evitar romper el JSON
    }
}

// Limpiar buffer y enviar JSON
sendJsonResponse($response);

