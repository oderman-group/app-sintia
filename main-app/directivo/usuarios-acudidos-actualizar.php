<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Matricula.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuarios_Por_Estudiantes.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0138';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

if (!empty($_POST["acudidos"])) {
	//SE CONSULTAN LOS ACUDIDOS ACTUALES DEL ACUDIENTE
	$consultaAcudidos = Administrativo_Usuarios_Por_Estudiantes::Select([
		"upe_id_usuario"	=> $_POST["id"],
		"institucion"		=> $_SESSION["idInstitucion"],
		"year"				=> $_SESSION["bd"]
	], "upe_id_estudiante", BD_GENERAL);
	$acudidos = $consultaAcudidos->fetchAll(PDO::FETCH_ASSOC);
	$valoresAcudidos  = array_column($acudidos, 'upe_id_estudiante');

	//SE COMPARAN LOS NUEVOS ACUDIDOS CON LOS ACTUALES PARA SABER CUALES SON NUEVOS
	$resultadoAgregar = array_diff($_POST["acudidos"], $valoresAcudidos);
	if (!empty($resultadoAgregar)) {
		foreach ($resultadoAgregar as $acudido ) {
			//SE AGREGAN LOS ACUDIDOS NUEVOS
			Administrativo_Usuarios_Por_Estudiantes::Insert([
				"upe_id"			=> Utilidades::generateCode("UPE"),
				"upe_id_usuario"	=> $_POST["id"],
				"upe_id_estudiante"	=> $acudido,
				"institucion"		=> $_SESSION["idInstitucion"],
				"year"				=> $_SESSION["bd"]
			], BD_GENERAL);

			//SE CONSULTA ACUDIENTES ACTUALES DEL ESTUDIANTE
			$consultaAcudienteActual = Matricula::Select([
				"mat_id"		=> $acudido,
				"institucion"	=> $_SESSION["idInstitucion"],
				"year"			=> $_SESSION["bd"]
			], "mat_acudiente, mat_acudiente2");
			$acudienteActual = $consultaAcudienteActual->fetch(PDO::FETCH_ASSOC);

			//SE ACTUALIZARA EL ACUDIENTE PRINCIPAL DEL ESTUDIANTE
			$datos = [
				"mat_acudiente"	=> $_POST["id"]
			];
			//SI YA TIENE ACUDIENTE PRINCIPAL ESTE SE ACTUALIZARA COMO ACUDIENTE 2
			if( !empty($acudienteActual['mat_acudiente']) ) {
				$datos = array_merge($datos, [
					"mat_acudiente2" => $acudienteActual['mat_acudiente']
				]);
			}
			
            //SE ACTUALIZAN LOS ACUDIENTES DEL ESTUDIANTE
			Matricula::Update($datos,[
				"mat_id"		=> $acudido,
				"institucion"	=> $_SESSION["idInstitucion"],
				"year"			=> $_SESSION["bd"]
			]);

			//SE ELIMINA LA RELACIÓN DEL ACUDIENTE 2 EN USUARIOS POR ESTUDIANTES
			if( !empty($acudienteActual['mat_acudiente2']) ) {
				Administrativo_Usuarios_Por_Estudiantes::Delete([
					"upe_id_usuario"	=> $acudienteActual['mat_acudiente2'],
					"upe_id_estudiante"	=> $acudido,
					"institucion"		=> $_SESSION["idInstitucion"],
					"year"				=> $_SESSION["bd"]
				], BD_GENERAL);
			}
		}
	}

	//SE COMPARAN LOS ACUDIDOS ACTUALES CON LOS NUEVOS ACUDIDOS PARA SABER CUALES YA NO ESTAN
	$resultadoEliminar = array_diff($valoresAcudidos,$_POST["acudidos"]);
	if (!empty($resultadoEliminar)) {
		foreach ($resultadoEliminar as $acudido ) {
			//SE ELIMINAN LOS ACUDIDOS QUE YA NO ESTAN
			Administrativo_Usuarios_Por_Estudiantes::Delete([
				"upe_id_usuario"	=> $_POST["id"],
				"upe_id_estudiante"	=> $acudido,
				"institucion"		=> $_SESSION["idInstitucion"],
				"year"				=> $_SESSION["bd"]
			], BD_GENERAL);

			//SE ACTUALIZA EL ACUDIENTE PRINCIPAL DEL ESTUDIANTE DONDE EL USUARIO ES EL ACUDIENTE PRINCIPAL
			Matricula::Update([
				"mat_acudiente"	=> NULL
			],[
				"mat_id"		=> $acudido,
				"mat_acudiente"	=> $_POST["id"],
				"institucion"	=> $_SESSION["idInstitucion"],
				"year"			=> $_SESSION["bd"]
			]);

			//SE ACTUALIZA EL ACUDIENTE 2 DEL ESTUDIANTE DONDE EL USUARIO ES EL ACUDIENTE 2
			Matricula::Update([
				"mat_acudiente2"	=> NULL
			],[
				"mat_id"			=> $acudido,
				"mat_acudiente2"	=> $_POST["id"],
				"institucion"		=> $_SESSION["idInstitucion"],
				"year"				=> $_SESSION["bd"]
			]);
		}
	}

} else {
	//SE ELIMINAN TODOS
	Administrativo_Usuarios_Por_Estudiantes::Delete([
		"upe_id_usuario"	=> $_POST["id"],
		"institucion"		=> $_SESSION["idInstitucion"],
		"year"				=> $_SESSION["bd"]
	], BD_GENERAL);

	//SE ACTUALIZA EL ACUDIENTE PRINCIPAL DE LOS ESTUDIANTES DONDE EL USUARIO ES EL ACUDIENTE PRINCIPAL
	Matricula::Update([
		"mat_acudiente"	=> NULL
	],[
		"mat_acudiente"	=> $_POST["id"],
		"institucion"	=> $_SESSION["idInstitucion"],
		"year"			=> $_SESSION["bd"]
	]);

	//SE ACTUALIZA EL ACUDIENTE 2 DE LOS ESTUDIANTES DONDE EL USUARIO ES EL ACUDIENTE 2
	Matricula::Update([
		"mat_acudiente2"	=> NULL
	],[
		"mat_acudiente2"	=> $_POST["id"],
		"institucion"		=> $_SESSION["idInstitucion"],
		"year"				=> $_SESSION["bd"]
	]);
}
include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="usuarios-acudidos.php?id='.base64_encode($_POST["id"]).'&success=SC_DT_2";</script>';
exit();