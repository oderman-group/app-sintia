<?php
/* 
* Esta pagina no hace honor a su nombre ya que se utiliza para verificar si el usuario tiene acceso o no a la pagina que intenta visitar. 
*/

include_once(ROOT_PATH."/main-app/modelo/conexion.php");
error_log("Entrando al inicio de la pagina historial-acciones-guardar.php para verificar permisos: ".$idPaginaInterna . " - ".$_SESSION["id"]);

$tienePermiso = Modulos::verificarPermisosPaginas($idPaginaInterna);

//Haces esto para cada tabla que necesitemos hacer el JOIN con la tabla principal
Modulos::foreignKey(Modulos::INNER, [
	'mod_id' => 'pagp_modulo' //Equivalente a ON mod_id=pagp_modulo
]);

$condicionWhereTablaPrincipal = [
	'pagp_id' => $idPaginaInterna //Equivalente a Where pagp_id = $idPaginaInterna
];

$camposNecesitadosDeLasConsulta = '*'; //Equivalente a Select * FROM ...

//Todas las tablas con las cuales se va hacer el JOIN
$tablasJoin = [
	Modulos::class //Equivalente a INNER JOIN modulos... (INNER porque arriba definimos que sería INNER.)
];

//Obtenemos los datos de la pagina y su módulo
$datosPaginaActualReal = Paginas::SelectJoin($condicionWhereTablaPrincipal, $camposNecesitadosDeLasConsulta, $tablasJoin);

$moduloCodigo = !empty($datosPaginaActualReal) ? $datosPaginaActualReal[0]['mod_id'] : null;
$moduloNombre = !empty($datosPaginaActualReal) ? $datosPaginaActualReal[0]['mod_nombre'] : null;
$paginaNombre = !empty($datosPaginaActualReal) ? $datosPaginaActualReal[0]['pagp_pagina'] : null;

$_SESSION["urlOrigin"] = $datosPaginaActualReal[0]['pagp_ruta'];

//Obtiene los datos de la pagina y su módulos SOLO si la Institución la tiene asignada
$datosPaginaActual = Modulos::datosPaginaActual($idPaginaInterna);

if (empty($datosPaginaActual) && $idPaginaInterna!='DT0107' && !in_array($moduloCodigo, Modulos::MODULOS_GLOBALES_PERMITIDOS_ACTUALMENTE)) {
	if (empty($usuariosClase)) {
		require_once("sintia-funciones.php");
		$usuariosClase = new UsuariosFunciones;
	}

	$url = $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'page-info.php');

	$ingresosAnterioresEstaPagina = Seguridad_Historial_Acciones::numRows(
		[
			'hil_usuario'                                 => $_SESSION['id'],
			'hil_titulo'                                  => $idPaginaInterna,
			'hil_institucion'                             => $config['conf_id_institucion'],
			Seguridad_Historial_Acciones::OTHER_PREDICATE => 'YEAR(hil_fecha)='.$_SESSION["bd"]
		]
	);

	$additionalParams = [
		'idPagina' => $idPaginaInterna,
		'modulo'   => $moduloNombre,
		'pagina'   => $paginaNombre,
		'cantAnterior' => $ingresosAnterioresEstaPagina
	];

	Utilidades::redirect($url, 302, $additionalParams);
}

$datosPaginaActual = Modulos::datosPaginaActual($idPaginaInterna);
