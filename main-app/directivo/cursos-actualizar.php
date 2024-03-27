<?php
include("session.php");
require_once("../class/servicios/GradoServicios.php");
require_once("../class/servicios/MediaTecnicaServicios.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");

Modulos::validarAccesoDirectoPaginas();
$archivoSubido = new Archivos;
$idPaginaInterna = 'DT0173';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if (trim($_POST["nombreC"]) == "" or trim($_POST["formatoB"]) == "" or trim($_POST["valorM"]) == "" or trim($_POST["valorP"]) == "") {
	echo '<script type="text/javascript">window.location.href="cursos-editar.php?error=ER_DT_4&id=' . base64_encode($_POST["id_curso"]) . '";</script>';
	exit();
}

if (empty($_POST["estado"])) {
	$_POST["estado"] = 1;
}
$esMediaTecnica = !is_null($_POST["tipoG"]);
if (!$esMediaTecnica) {
	$resultadoCurso = GradoServicios::consultarCurso($_POST["id_curso"]);
	$_POST["tipoG"] = $resultadoCurso['gra_tipo'];
}
if (empty($_POST["tipoG"])) {
	$_POST["tipoG"] = GRADO_GRUPAL;
}
if (empty($_POST["estado"])) {
	$_POST["estado"] = 1;
}
if (empty($_POST["imagen"])) {
	$_POST["imagen"] = '';
}
if (empty($_POST["descripcion"])) {
	$_POST["descripcion"] = '';
}
if (empty($_POST["contenido"])) {
	$_POST["contenido"] = '';
}
if (empty($_POST["precio"])) {
	$_POST["precio"] = '0';
}
if (empty($_POST["minEstudiantes"])) {
	$_POST["minEstudiantes"] = '0';
}
if (empty($_POST["maxEstudiantes"])) {
	$_POST["maxEstudiantes"] = '0';
}
if (empty($_POST["horas"])) {
	$_POST["horas"] = '0';
}
$_POST["autoenrollment"] = empty($_POST["autoenrollment"]) ? 0 : 1;
$_POST["activo"] = empty($_POST["activo"]) ? 0 : 1;


if (!empty($_POST["imagenCursoAi"])) {
	$rutaImagen=$_POST["imagenCursoAi"];
	$imagen = file_get_contents($rutaImagen);
	$archivo = $_SESSION["inst"] . '_' . $_SESSION["id"] . '_curso_'.$_POST["id_curso"]. ".png";
	$destino = "../files/cursos/".$archivo;
	$cloudFilePath = FILE_CURSOS.$archivo;// Ruta en el almacenamiento en la nube de Firebase donde deseas almacenar el archivo
	file_put_contents($destino, $imagen);
	$storage->getBucket()->upload(fopen($destino, 'r'), ['name' => $cloudFilePath	]);
	try{
        mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_grados SET gra_cover_image = '" . $archivo. "' WHERE gra_id='" . $_POST["id_curso"] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
	unlink($destino);
}
if (!empty($_FILES['imagenCurso']['name'])) {
    $archivoSubido->validarArchivo($_FILES['imagenCurso']['size'], $_FILES['imagenCurso']['name']);
    $explode=explode(".", $_FILES['imagenCurso']['name']);
    $extension = end($explode);
    $archivo = $_SESSION["inst"] . '_' . $_SESSION["id"] . '_curso_'.$_POST["id_curso"]. "." . $extension;
    $destino = "../files/cursos";
	$localFilePath = $_FILES['imagenCurso']['tmp_name'];// Ruta del archivo local que deseas subir	
	$cloudFilePath = FILE_CURSOS.$archivo;// Ruta en el almacenamiento en la nube de Firebase donde deseas almacenar el archivo
	$storage->getBucket()->upload(fopen($localFilePath, 'r'), ['name' => $cloudFilePath	]);
    // move_uploaded_file($_FILES['imagenCurso']['tmp_name'], $destino . "/" . $archivo);
    try{
        mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_grados SET gra_cover_image = '" . $archivo. "' WHERE gra_id='" . $_POST["id_curso"] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

try {
	mysqli_query($conexion, "UPDATE " . BD_ACADEMICA . ".academico_grados SET 
	gra_codigo='" . $_POST["codigoC"] . "', 
	gra_nombre='" . $_POST["nombreC"] . "', 
	gra_formato_boletin='" . $_POST["formatoB"] . "', 
	gra_valor_matricula='" . $_POST["valorM"] . "', 
	gra_valor_pension='" . $_POST["valorP"] . "', 
	gra_grado_siguiente='" . $_POST["graSiguiente"] . "', 
	gra_grado_anterior='" . $_POST["graAnterior"] . "', 
	gra_nota_minima='" . $_POST["notaMin"] . "', 
	gra_periodos='" . $_POST["periodosC"] . "', 
	gra_nivel='" . $_POST["nivel"] . "', 
	gra_estado='" . $_POST["estado"] . "',
	gra_tipo='" . $_POST["tipoG"] . "',
	gra_overall_description = '" . $_POST["descripcion"] . "',
	gra_course_content = '" . $_POST["contenido"] . "',
	gra_price = '" . $_POST["precio"] . "',
	gra_minimum_quota = '" . $_POST["minEstudiantes"] . "',
	gra_maximum_quota = '" . $_POST["maxEstudiantes"] . "',
	gra_duration_hours = '" . $_POST["horas"] . "',
	gra_auto_enrollment = '" . $_POST["autoenrollment"] . "',
	gra_active = '" . $_POST["activo"] . "'
	WHERE gra_id='" . $_POST["id_curso"] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="cursos-editar.php?success=SC_DT_2&id=' . base64_encode($_POST["id_curso"]) . '";</script>';
exit();
