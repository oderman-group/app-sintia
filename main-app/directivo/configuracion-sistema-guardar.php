<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0187';
if($_POST["configDEV"]==1){
	$idPaginaInterna = 'DV0033';
}

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

if (trim($_POST["periodo"]) == "" or trim($_POST["perdida"]) == "" or trim($_POST["ganada"]) == "" or trim($_POST["estiloNotas"]) == "") {
	include("../compartido/guardar-historial-acciones.php");
	echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
	exit();
}
if(empty($_POST["desde"])) {$_POST["desde"] = 1;}
if(empty($_POST["hasta"])) {$_POST["hasta"] = 5;}
if(empty($_POST["notaMinima"])) {$_POST["notaMinima"] = 3;}
if(empty($_POST["periodoTrabajar"])) {$_POST["periodoTrabajar"] = 4;}
if(empty($_POST["porcenAsigan"])) {$_POST["porcenAsigan"] = 'NO';}
if(empty($_POST["certificado"])) {$_POST["certificado"] = 1;}
if(empty($_POST["formaNotas"])) {$_POST["formaNotas"] = CUANTITATIVA;}

try {
	mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".configuracion SET 
	conf_periodo='" . $_POST["periodo"] . "', 
	conf_nota_desde='" . $_POST["desde"] . "', 
	conf_nota_hasta='" . $_POST["hasta"] . "', 
	conf_nota_minima_aprobar='" . $_POST["notaMinima"] . "', 
	conf_color_perdida='" . $_POST["perdida"] . "', 
	conf_color_ganada='" . $_POST["ganada"] . "', 
	conf_periodos_maximos='" . $_POST["periodoTrabajar"] . "',  
	conf_notas_categoria='" . $_POST["estiloNotas"] . "',
	conf_agregar_porcentaje_asignaturas='" . $_POST["porcenAsigna"] . "',
	conf_fecha_parcial='" . $_POST["fechapa"] . "', 
	conf_descripcion_parcial='" . $_POST["descrip"] . "',
	conf_ancho_imagen='" . $_POST["logoAncho"] . "',
	conf_alto_imagen='" . $_POST["logoAlto"] . "', 
	conf_mostrar_nombre='" . $_POST["mostrarNombre"] . "',
	conf_calificaciones_acudientes='" . $_POST["caliAcudientes"] . "',
	conf_mostrar_calificaciones_estudiantes='" . $_POST["caliEstudiantes"] . "',
	conf_orden_nombre_estudiantes='" . $_POST["ordenEstudiantes"] . "',
	conf_editar_definitivas_consolidado='" . $_POST["permisoConsolidado"] . "',
	conf_informe_parcial='" . $_POST["informeParcial"] . "',
	conf_decimales_notas='" . $_POST["decimalesNotas"] . "',
	conf_num_registros='" . $_POST["numRegistros"] . "',
	conf_observaciones_multiples_comportamiento='" . $_POST["observacionesMultiples"] . "',
	conf_cambiar_nombre_usuario='" . $_POST["cambiarNombreUsuario"] . "',
	conf_cambiar_clave_estudiantes='" . $_POST["cambiarClaveEstudiantes"] . "',
	conf_certificado='" . $_POST["certificado"] . "',
	conf_permiso_descargar_boletin='" . $_POST["descargarBoletin"] . "',
	conf_firma_estudiante_informe_asistencia='" . $_POST["firmaEstudiante"] . "',
	conf_ver_promedios_sabanas_docentes='" . $_POST["permisoDocentesPuestosSabanas"] . "',
	conf_permiso_edicion_years_anteriores='" . $_POST["editarInfoYears"] . "',
	conf_porcentaje_completo_generar_informe='" . $_POST["generarInforme"] . "',
	conf_activar_encuesta='" . $_POST["activarEncuestaReservaCupo"] . "',
	conf_forma_mostrar_notas='" . $_POST["formaNotas"] . "',
	conf_mostrar_encabezado_informes='" . $_POST["mostrarEncabezadoInformes"] . "',
	conf_mostrar_pasos_matricula='" . $_POST["pasosMatricula"] . "',
	conf_reporte_sabanas_nota_indocador='" . $_POST["notasReporteSabanas"] . "',
	conf_doble_buscador='" . $_POST["dobleBuscador"] . "',
	conf_libro_final='" . $_POST["libroFinal"] . "',
	conf_estampilla_certificados='" . $_POST["estampilla"] . "'

	WHERE conf_id='".$_POST['id']."'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

if($_POST["configDEV"]==0){
	RedisInstance::getSystemConfiguration(true);
}

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="configuracion-sistema.php";</script>';
exit();