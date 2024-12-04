<?php
function validarPaginaActual(array $paginas = []): bool
{
	global $idPaginaInterna;
	foreach ($paginas as $idPagina) {
		if ($idPaginaInterna === $idPagina) {
			return true;
		}
	}
	return false;
};
function agregarClass(string $tipoMenu, array $paginas = [])
{
	switch ($tipoMenu) {
		case MENU:
			if (validarPaginaActual($paginas)) {
				echo 'class="active nav-item"';
			}
			break;
		case MENU_PADRE:
			if (validarPaginaActual($paginas)) {
				echo 'class="nav-item open"';
			} else {
				echo 'class="nav-item"';
			}
			break;
		case SUB_MENU:
			if (validarPaginaActual($paginas)) {
				echo 'style="display: block;"';
			}
			break;
	}
};

/**
 * Validates module menu based on module ID and link.
 *
 * @param int $idModulo
 * @param string $enlace
 * @param string $tipoMenu
 * 
 * @return void
 */
function validarModuloMenu (
	int $idModulo,
	string $enlace,
	string $tipoMenu
) {
	$opacity        = "";
	$href           = $enlace;
	$modalOnClick   = "";

	if (!empty($_SESSION["modulos"]) && !array_key_exists($idModulo, $_SESSION["modulos"])) {
		$opacity = 'style="opacity: 0.6;" ';
		$href    = "javascript:void(0);";

		// Preguntamos si es subitem de un menú padre.
		if ($tipoMenu == MENU) {
			$modalOnClick = ' onclick="mostrarModalCompraModulos('.$idModulo.', '.$_SESSION["bd"].')"';
		}
	}
	
	echo $opacity.'href="'.$href.'"'.$modalOnClick;
}
