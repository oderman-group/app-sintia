<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");

Modulos::validarAccesoDirectoPaginas();
$archivoSubido = new Archivos;
$idPaginaInterna = 'DT0188';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(trim($_POST["nombreC"])==""){
		include("../compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="cursos.php?error=ER_DT_4";</script>';
		exit();
	}

	if(empty($_POST["valorM"])) {$_POST["valorM"] = '0';}
	if(empty($_POST["valorP"])) {$_POST["valorP"] = '0';}
	if(empty($_POST["graSiguiente"])) {$_POST["graSiguiente"] = 1;}
	if(empty($_POST["tipoG"])){ $_POST["tipoG"]=GRADO_GRUPAL;}

	$codigoCurso = "GRA".strtotime("now");
	

		if(empty($_POST["imagen"])) {$_POST["imagen"] = '';}
		if(empty($_POST["descripcion"])) {$_POST["descripcion"] = '';}
		if(empty($_POST["contenido"])) {$_POST["contenido"] = '';}
		if(empty($_POST["precio"])) {$_POST["precio"] = '0';}
		if(empty($_POST["minEstudiantes"])) {$_POST["minEstudiantes"] = '0';}
		if(empty($_POST["maxEstudiantes"])) {$_POST["maxEstudiantes"] = '0';}
		if(empty($_POST["horas"])) {$_POST["horas"] = '0';}
		$_POST["autoenrollment"] = empty($_POST["autoenrollment"]) ? 0 : 1;
		$_POST["activo"] = empty($_POST["activo"]) ? 0 : 1;



	$codGRADO = Grados::guardarCurso($conexionPDO, "gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado,gra_grado_siguiente, gra_periodos, gra_tipo, institucion, year, gra_overall_description, gra_course_content, gra_price, gra_minimum_quota, gra_maximum_quota, gra_duration_hours, gra_auto_enrollment, gra_active, gra_id", [$codigoCurso, $_POST["nombreC"], '1', $_POST["valorM"], $_POST["valorP"], 1, $_POST["graSiguiente"], $config['conf_periodos_maximos'], $_POST["tipoG"], $config['conf_id_institucion'], $_SESSION["bd"], $_POST["descripcion"], $_POST["contenido"], $_POST["precio"], $_POST["minEstudiantes"], $_POST["maxEstudiantes"], $_POST["horas"], $_POST["autoenrollment"], $_POST["activo"]]);

	if (!empty($_FILES['imagenCurso']['name'])) {
		$archivoSubido->validarArchivo($_FILES['imagenCurso']['size'], $_FILES['imagenCurso']['name']);
		$explode=explode(".", $_FILES['imagenCurso']['name']);
		$extension = end($explode);
		$archivo = $_SESSION["inst"] . '_' . $_SESSION["id"] . '_curso_'.$_POST["id_curso"]. "." . $extension;
		$destino = "../files/cursos";
		$localFilePath = $_FILES['imagenCurso']['tmp_name'];// Ruta del archivo local que deseas subir	
		$cloudFilePath = FILE_CURSOS.$archivo;// Ruta en el almacenamiento en la nube de Firebase donde deseas almacenar el archivo
		$storage->getBucket()->upload(fopen($localFilePath, 'r'), ['name' => $cloudFilePath	]);

		$update = "gra_cover_image=".$archivo."";
		Grados::actualizarCursos($config, $codGRADO, $update);
	}

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cursos.php?success=SC_DT_1&id='.base64_encode($codGRADO).'";</script>';
	exit();	