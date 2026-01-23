<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_configuracion.php");

Modulos::validarAccesoDirectoPaginas();

$idPaginaInterna = 'DT0187';

if ($_POST["configDEV"] == 1) {
    $idPaginaInterna = 'DV0033';
}

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

include("../compartido/historial-acciones-guardar.php");

$_POST["desde"]           = empty($_POST["desde"])           ? 1            : $_POST["desde"];
$_POST["hasta"]           = empty($_POST["hasta"])           ? 5            : $_POST["hasta"];
$_POST["notaMinima"]      = empty($_POST["notaMinima"])      ? 3            : $_POST["notaMinima"];
$_POST["periodoTrabajar"] = empty($_POST["periodoTrabajar"]) ? 4            : $_POST["periodoTrabajar"];
$_POST["porcenAsigan"]    = empty($_POST["porcenAsigan"])    ? 'NO'         : $_POST["porcenAsigan"];
$_POST["certificado"]     = empty($_POST["certificado"])     ? 1            : $_POST["certificado"];
$_POST["formaNotas"]      = empty($_POST["formaNotas"])      ? CUANTITATIVA : $_POST["formaNotas"];

$datos     = [];
$tabActual = "#general";

// Si es desde dev-instituciones-configuracion.php, procesar TODOS los campos
if ($_POST["configDEV"] == 1) {
    // CONFIGURACIÓN GENERAL
    if (!empty($_POST["periodo"])) {
        $datos["conf_periodo"] = $_POST["periodo"];
    }
    if (!empty($_POST["pesoArchivos"])) {
        $datos["conf_max_peso_archivos"] = $_POST["pesoArchivos"];
    }
    
    // COMPORTAMIENTO DEL SISTEMA
    if (isset($_POST["desde"])) {
        $datos["conf_nota_desde"] = $_POST["desde"];
    }
    if (isset($_POST["hasta"])) {
        $datos["conf_nota_hasta"] = $_POST["hasta"];
    }
    if (isset($_POST["notaMinima"])) {
        $datos["conf_nota_minima_aprobar"] = $_POST["notaMinima"];
    }
    if (isset($_POST["periodoTrabajar"])) {
        $datos["conf_periodos_maximos"] = $_POST["periodoTrabajar"];
    }
    if (isset($_POST["decimalesNotas"])) {
        $datos["conf_decimales_notas"] = $_POST["decimalesNotas"];
    }
    if (isset($_POST["porcenAsigna"])) {
        $datos["conf_agregar_porcentaje_asignaturas"] = $_POST["porcenAsigna"];
    }
    if (isset($_POST["estiloNotas"])) {
        $datos["conf_notas_categoria"] = $_POST["estiloNotas"];
    }
    if (isset($_POST["formaNotas"])) {
        $datos["conf_forma_mostrar_notas"] = $_POST["formaNotas"];
    }
    
    // PREFERENCIAS
    if (isset($_POST["ordenEstudiantes"])) {
        $datos["conf_orden_nombre_estudiantes"] = $_POST["ordenEstudiantes"];
    }
    if (isset($_POST["numRegistros"])) {
        $datos["conf_num_registros"] = $_POST["numRegistros"];
    }
    if (isset($_POST["mostrarEstudiantesCancelados"])) {
        $datos["conf_mostrar_estudiantes_cancelados"] = $_POST["mostrarEstudiantesCancelados"];
    }
    if (isset($_POST["mostrarNotasPanelLateral"])) {
        $datos["conf_ocultar_panel_lateral_notas_estudiantes"] = $_POST["mostrarNotasPanelLateral"];
    }
    if (isset($_POST["solicitarAcudiente2"])) {
        $datos["conf_solicitar_acudiente_2"] = $_POST["solicitarAcudiente2"];
    }
    
    // INFORMES Y REPORTES
    if (isset($_POST["formatoBoletin"])) {
        $datos["conf_formato_boletin"] = $_POST["formatoBoletin"];
    }
    if (isset($_POST["estampilla"])) {
        $datos["conf_estampilla_certificados"] = $_POST["estampilla"];
    }
    if (isset($_POST["libroFinal"])) {
        $datos["conf_libro_final"] = $_POST["libroFinal"];
    }
    if (isset($_POST["mostrarEncabezadoInformes"])) {
        $datos["conf_mostrar_encabezado_informes"] = $_POST["mostrarEncabezadoInformes"];
    }
    if (isset($_POST["firmaEstudiante"])) {
        $datos["conf_firma_estudiante_informe_asistencia"] = $_POST["firmaEstudiante"];
    }
    if (isset($_POST["certificado"])) {
        $datos["conf_certificado"] = $_POST["certificado"];
    }
    if (isset($_POST["mostrarNombre"])) {
        $datos["conf_mostrar_nombre"] = $_POST["mostrarNombre"];
    }
    if (isset($_POST["logoAlto"])) {
        $datos["conf_alto_imagen"] = $_POST["logoAlto"];
    }
    if (isset($_POST["logoAncho"])) {
        $datos["conf_ancho_imagen"] = $_POST["logoAncho"];
    }
    if (isset($_POST["fechapa"])) {
        $datos["conf_fecha_parcial"] = $_POST["fechapa"];
    }
    if (isset($_POST["descrip"])) {
        $datos["conf_descripcion_parcial"] = $_POST["descrip"];
    }
    if (isset($_POST["notasReporteSabanas"])) {
        $datos["conf_reporte_sabanas_nota_indocador"] = $_POST["notasReporteSabanas"];
    }
    if (isset($_POST["promedioLibroFinal"])) {
        $datos["conf_promedio_libro_final"] = $_POST["promedioLibroFinal"];
    }
    if (isset($_POST["firmaAsistencia"])) {
        $datos["conf_firma_inasistencia_planilla_notas_doc"] = $_POST["firmaAsistencia"];
    }
    if (isset($_POST["generarInforme"])) {
        $datos["conf_porcentaje_completo_generar_informe"] = $_POST["generarInforme"];
    }
    if (isset($_POST["observacionesMultiples"])) {
        $datos["conf_observaciones_multiples_comportamiento"] = $_POST["observacionesMultiples"];
    }
    
    // PERMISOS
    if (isset($_POST["caliAcudientes"])) {
        $datos["conf_calificaciones_acudientes"] = $_POST["caliAcudientes"];
    }
    if (isset($_POST["caliEstudiantes"])) {
        $datos["conf_mostrar_calificaciones_estudiantes"] = $_POST["caliEstudiantes"];
    }
    if (isset($_POST["permisoConsolidado"])) {
        $datos["conf_editar_definitivas_consolidado"] = $_POST["permisoConsolidado"];
    }
    if (isset($_POST["cambiarNombreUsuario"])) {
        $datos["conf_cambiar_nombre_usuario"] = $_POST["cambiarNombreUsuario"];
    }
    if (isset($_POST["cambiarClaveEstudiantes"])) {
        $datos["conf_cambiar_clave_estudiantes"] = $_POST["cambiarClaveEstudiantes"];
    }
    if (isset($_POST["descargarBoletin"])) {
        $datos["conf_permiso_descargar_boletin"] = $_POST["descargarBoletin"];
    }
    if (isset($_POST["permisoDocentesPuestosSabanas"])) {
        $datos["conf_ver_promedios_sabanas_docentes"] = $_POST["permisoDocentesPuestosSabanas"];
    }
    if (isset($_POST["editarInfoYears"])) {
        $datos["conf_permiso_edicion_years_anteriores"] = $_POST["editarInfoYears"];
    }
    if (isset($_POST["pasosMatricula"])) {
        $datos["conf_mostrar_pasos_matricula"] = $_POST["pasosMatricula"];
    }
    if (isset($_POST["dobleBuscador"])) {
        $datos["conf_doble_buscador"] = $_POST["dobleBuscador"];
    }
    if (isset($_POST["informeParcial"])) {
        $datos["conf_informe_parcial"] = $_POST["informeParcial"];
    }
    if (isset($_POST["activarEncuestaReservaCupo"])) {
        $datos["conf_activar_encuesta"] = $_POST["activarEncuestaReservaCupo"];
    }
    if (isset($_POST["permisoEliminarCargas"])) {
        $datos["conf_permiso_eliminar_cargas"] = $_POST["permisoEliminarCargas"];
    }
    
    // ESTILOS
    if (isset($_POST["perdida"])) {
        $datos["conf_color_perdida"] = $_POST["perdida"];
    }
    if (isset($_POST["ganada"])) {
        $datos["conf_color_ganada"] = $_POST["ganada"];
    }
    
    // Si hay datos, actualizar y redirigir
    if (!empty($datos)) {
        $predicado = [
            "conf_id" => $_POST['id']
        ];
        BDT_Configuracion::update($datos, $predicado, BD_ADMIN);
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="dev-instituciones-configuracion.php?id='.base64_encode($_POST['id'] ?? '').'&year='.base64_encode($_POST['agno'] ?? '').'";</script>';
        exit();
    }
}

// Solo procesar por secciones si NO es desde dev (configDEV != 1)
if ($_POST["configDEV"] != 1) {
    if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_GENERAL) {
        $datos["conf_periodo"] = $_POST["periodo"];
        $datos["conf_max_peso_archivos"] = $_POST["pesoArchivos"];

        $tabActual = "#general";
    }

    if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_COMPORTAMIENTO) {
    $datos["conf_nota_desde"]                             = $_POST["desde"];
    $datos["conf_nota_hasta"]                             = $_POST["hasta"];
    $datos["conf_nota_minima_aprobar"]                    = $_POST["notaMinima"];   
    $datos["conf_periodos_maximos"]                       = $_POST["periodoTrabajar"];
    $datos["conf_decimales_notas"]                        = $_POST["decimalesNotas"];
    $datos["conf_agregar_porcentaje_asignaturas"]         = $_POST["porcenAsigna"];
    $datos["conf_notas_categoria"]                        = $_POST["estiloNotas"];
    $datos["conf_forma_mostrar_notas"]                    = $_POST["formaNotas"];

    $tabActual = "#comportamiento-sistema";
}

    if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_PREFERENCIAS) {
    $datos["conf_orden_nombre_estudiantes"]                = $_POST["ordenEstudiantes"];
    $datos["conf_num_registros"]                           = $_POST["numRegistros"];
    $datos["conf_mostrar_estudiantes_cancelados"]          = $_POST["mostrarEstudiantesCancelados"];
    $datos["conf_ocultar_panel_lateral_notas_estudiantes"] = $_POST["mostrarNotasPanelLateral"];
    $datos["conf_solicitar_acudiente_2"]                   = $_POST["solicitarAcudiente2"] ?? 'NO';

    $tabActual = "#preferencias";
}

    if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_INFORMES) {
    $datos["conf_formato_boletin"]                          = $_POST["formatoBoletin"];
    $datos["conf_estampilla_certificados"]                  = $_POST["estampilla"];
    $datos["conf_libro_final"]                              = $_POST["libroFinal"];
    $datos["conf_mostrar_encabezado_informes"]              = $_POST["mostrarEncabezadoInformes"];
    $datos["conf_firma_estudiante_informe_asistencia"]      = $_POST["firmaEstudiante"];
    $datos["conf_certificado"]                              = $_POST["certificado"];
    $datos["conf_mostrar_nombre"]                           = $_POST["mostrarNombre"];
    $datos["conf_alto_imagen"]                              = $_POST["logoAlto"];
    $datos["conf_ancho_imagen"]                             = $_POST["logoAncho"];
    $datos["conf_fecha_parcial"]                            = $_POST["fechapa"];
    $datos["conf_descripcion_parcial"]                      = $_POST["descrip"];
    $datos["conf_reporte_sabanas_nota_indocador"]           = $_POST["notasReporteSabanas"];
    $datos["conf_promedio_libro_final"]                     = $_POST["promedioLibroFinal"];
    $datos["conf_firma_inasistencia_planilla_notas_doc"]    = $_POST["firmaAsistencia"];
    $datos["conf_porcentaje_completo_generar_informe"]      = $_POST["generarInforme"];
    $datos["conf_observaciones_multiples_comportamiento"]   = $_POST["observacionesMultiples"];

    $tabActual = "#informes";
}

    if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_PERMISOS) {
    $datos["conf_calificaciones_acudientes"]          = $_POST["caliAcudientes"];
    $datos["conf_mostrar_calificaciones_estudiantes"] = $_POST["caliEstudiantes"];
    $datos["conf_editar_definitivas_consolidado"]     = $_POST["permisoConsolidado"];
    $datos["conf_cambiar_nombre_usuario"]             = $_POST["cambiarNombreUsuario"];
    $datos["conf_cambiar_clave_estudiantes"]          = $_POST["cambiarClaveEstudiantes"];
    $datos["conf_permiso_descargar_boletin"]          = $_POST["descargarBoletin"];
    $datos["conf_ver_promedios_sabanas_docentes"]     = $_POST["permisoDocentesPuestosSabanas"];
    $datos["conf_permiso_edicion_years_anteriores"]   = $_POST["editarInfoYears"];
    $datos["conf_mostrar_pasos_matricula"]            = $_POST["pasosMatricula"];
    $datos["conf_doble_buscador"]                     = $_POST["dobleBuscador"];
    $datos["conf_informe_parcial"]                    = $_POST["informeParcial"];
    $datos["conf_activar_encuesta"]                   = $_POST["activarEncuestaReservaCupo"];
    $datos["conf_permiso_eliminar_cargas"]            = $_POST["permisoEliminarCargas"];

    $tabActual = "#permisos";
}

    if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_ESTILOS) {
        $datos["conf_color_perdida"] = $_POST["perdida"];
        $datos["conf_color_ganada"]  = $_POST["ganada"];

        $tabActual = "#estilos-apariencia";
    }
}

// Solo actualizar si hay datos y no es desde dev (ya se procesó arriba)
if ($_POST["configDEV"] != 1 && !empty($datos)) {
    $predicado = [
        "conf_id" => $_POST['id']
    ];

    BDT_Configuracion::update($datos, $predicado, BD_ADMIN);

    RedisInstance::getSystemConfiguration(true);

    include("../compartido/guardar-historial-acciones.php");

    echo '<script type="text/javascript">window.location.href="configuracion-sistema.php?'.$tabActual.'";</script>';
    exit();
}