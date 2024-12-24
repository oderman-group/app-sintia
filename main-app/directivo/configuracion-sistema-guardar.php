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

if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_GENERAL) {
    $datos["conf_periodo"] = $_POST["periodo"];

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
    $datos["conf_porcentaje_completo_generar_informe"]    = $_POST["generarInforme"];
    $datos["conf_observaciones_multiples_comportamiento"] = $_POST["observacionesMultiples"];
    $datos["conf_max_peso_archivos"]                      = $_POST["pesoArchivos"];

    $tabActual = "#comportamiento-sistema";
}

if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_PREFERENCIAS) {
    $datos["conf_orden_nombre_estudiantes"]                = $_POST["ordenEstudiantes"];
    $datos["conf_num_registros"]                           = $_POST["numRegistros"];
    $datos["conf_mostrar_estudiantes_cancelados"]          = $_POST["mostrarEstudiantesCancelados"];
    $datos["conf_ocultar_panel_lateral_notas_estudiantes"] = $_POST["mostrarNotasPanelLateral"];

    $tabActual = "#preferencias";
}

if ($_POST["configTab"] == BDT_Configuracion::CONFIG_SISTEMA_INFORMES) {
    $datos["conf_formato_boletin"]                     = $_POST["formatoBoletin"];
    $datos["conf_estampilla_certificados"]             = $_POST["estampilla"];
    $datos["conf_libro_final"]                         = $_POST["libroFinal"];
    $datos["conf_mostrar_encabezado_informes"]         = $_POST["mostrarEncabezadoInformes"];
    $datos["conf_firma_estudiante_informe_asistencia"] = $_POST["firmaEstudiante"];
    $datos["conf_certificado"]                         = $_POST["certificado"];
    $datos["conf_mostrar_nombre"]                      = $_POST["mostrarNombre"];
    $datos["conf_alto_imagen"]                         = $_POST["logoAlto"];
    $datos["conf_ancho_imagen"]                        = $_POST["logoAncho"];
    $datos["conf_fecha_parcial"]                       = $_POST["fechapa"];
    $datos["conf_descripcion_parcial"]                 = $_POST["descrip"];
    $datos["conf_reporte_sabanas_nota_indocador"]      = $_POST["notasReporteSabanas"];
    $datos["conf_promedio_libro_final"]                = $_POST["promedioLibroFinal"];

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

$predicado = [
    "conf_id" => $_POST['id']
];

BDT_Configuracion::update($datos, $predicado, BD_ADMIN);

if ($_POST["configDEV"] == 0) {
    RedisInstance::getSystemConfiguration(true);
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="configuracion-sistema.php?'.$tabActual.'";</script>';
exit();