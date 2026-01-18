<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0066'; // Página disciplina-faltas
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(array $data)
{
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit();
}

// Permisos
if (!Modulos::validarSubRol([$idPaginaInterna]) || !Modulos::validarPermisoEdicion()) {
	jsonResponse([
		'success' => false,
		'message' => 'No tienes permisos para realizar esta acción.'
	]);
}

// Para importar se requiere poder crear categorías y faltas
if (!Modulos::validarSubRol(['DT0071']) || !Modulos::validarSubRol(['DT0068'])) {
	jsonResponse([
		'success' => false,
		'message' => 'No tienes permisos para importar (requiere permisos de crear categorías y faltas).'
	]);
}

$yearOrigen = isset($_POST['year_origen']) ? (int)$_POST['year_origen'] : 0;
$yearDestino = (int)($_SESSION["bd"] ?? 0);
$idInstitucion = (int)($config['conf_id_institucion'] ?? 0);

if ($yearOrigen <= 0 || $yearDestino <= 0) {
	jsonResponse(['success' => false, 'message' => 'Año inválido.']);
}
if ($yearOrigen === $yearDestino) {
	jsonResponse(['success' => false, 'message' => 'El año origen debe ser diferente al año actual.']);
}

// Validar rango de años de la institución
try {
	$insYears = $_SESSION["datosUnicosInstitucion"]["ins_years"] ?? '';
	$parts = array_map('trim', explode(',', (string)$insYears));
	$yStart = (int)($parts[0] ?? 0);
	$yEnd = (int)($parts[1] ?? 0);
	if ($yStart > 0 && $yEnd > 0) {
		if ($yearOrigen < $yStart || $yearOrigen > $yEnd || $yearDestino < $yStart || $yearDestino > $yEnd) {
			jsonResponse(['success' => false, 'message' => 'El año seleccionado no pertenece al rango de la institución.']);
		}
	}
} catch (Exception $e) {
	// Si falla la validación de rango no bloqueamos, pero seguimos con validaciones DB
}

try {
	require_once(ROOT_PATH . "/main-app/class/Conexion.php");
	require_once(ROOT_PATH . "/main-app/class/Utilidades.php");

	$conexionPDO = Conexion::newConnection('PDO');
	$conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Validar que el destino esté vacío (categorías y faltas)
	$sqlCountCats = "SELECT COUNT(*) FROM " . BD_DISCIPLINA . ".disciplina_categorias WHERE dcat_institucion=? AND dcat_year=?";
	$stmt = $conexionPDO->prepare($sqlCountCats);
	$stmt->bindParam(1, $idInstitucion, PDO::PARAM_INT);
	$stmt->bindParam(2, $yearDestino, PDO::PARAM_INT);
	$stmt->execute();
	$destCats = (int)$stmt->fetchColumn();

	$sqlCountFals = "SELECT COUNT(*) FROM " . BD_DISCIPLINA . ".disciplina_faltas WHERE dfal_institucion=? AND dfal_year=?";
	$stmt2 = $conexionPDO->prepare($sqlCountFals);
	$stmt2->bindParam(1, $idInstitucion, PDO::PARAM_INT);
	$stmt2->bindParam(2, $yearDestino, PDO::PARAM_INT);
	$stmt2->execute();
	$destFals = (int)$stmt2->fetchColumn();

	if ($destCats > 0 || $destFals > 0) {
		jsonResponse([
			'success' => false,
			'message' => 'El año actual ya tiene categorías o faltas. La importación solo se permite cuando el año está vacío.'
		]);
	}

	// Traer categorías del origen
	$sqlCats = "SELECT dcat_id, dcat_nombre
	            FROM " . BD_DISCIPLINA . ".disciplina_categorias
	            WHERE dcat_institucion=? AND dcat_year=?
	            ORDER BY dcat_id_nuevo ASC";
	$stmtCats = $conexionPDO->prepare($sqlCats);
	$stmtCats->bindParam(1, $idInstitucion, PDO::PARAM_INT);
	$stmtCats->bindParam(2, $yearOrigen, PDO::PARAM_INT);
	$stmtCats->execute();
	$catsOrigen = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

	if (empty($catsOrigen)) {
		jsonResponse(['success' => false, 'message' => 'El año origen no tiene categorías para importar.']);
	}

	// Traer faltas del origen
	$sqlFals = "SELECT dfal_codigo, dfal_nombre, dfal_id_categoria
	            FROM " . BD_DISCIPLINA . ".disciplina_faltas
	            WHERE dfal_institucion=? AND dfal_year=?
	            ORDER BY dfal_id_nuevo ASC";
	$stmtFals = $conexionPDO->prepare($sqlFals);
	$stmtFals->bindParam(1, $idInstitucion, PDO::PARAM_INT);
	$stmtFals->bindParam(2, $yearOrigen, PDO::PARAM_INT);
	$stmtFals->execute();
	$falsOrigen = $stmtFals->fetchAll(PDO::FETCH_ASSOC);

	if (empty($falsOrigen)) {
		jsonResponse(['success' => false, 'message' => 'El año origen no tiene faltas para importar.']);
	}

	$conexionPDO->beginTransaction();

	// Insertar categorías destino y mapear IDs
	$mapCategorias = []; // dcat_id_origen => dcat_id_destino
	$sqlInsCat = "INSERT INTO " . BD_DISCIPLINA . ".disciplina_categorias
	              (dcat_id, dcat_nombre, dcat_institucion, dcat_year)
	              VALUES (?, ?, ?, ?)";
	$stmtInsCat = $conexionPDO->prepare($sqlInsCat);

	$insertCats = 0;
	foreach ($catsOrigen as $cat) {
		$dcatIdOrigen = (string)($cat['dcat_id'] ?? '');
		$dcatNombre = (string)($cat['dcat_nombre'] ?? '');
		$dcatNombre = trim($dcatNombre);
		if ($dcatIdOrigen === '' || $dcatNombre === '') {
			throw new Exception('Categoría inválida en el año origen.');
		}

		$dcatIdDestino = Utilidades::generateCode("DCT");
		$stmtInsCat->bindParam(1, $dcatIdDestino, PDO::PARAM_STR);
		$stmtInsCat->bindParam(2, $dcatNombre, PDO::PARAM_STR);
		$stmtInsCat->bindParam(3, $idInstitucion, PDO::PARAM_INT);
		$stmtInsCat->bindParam(4, $yearDestino, PDO::PARAM_INT);
		$stmtInsCat->execute();

		$mapCategorias[$dcatIdOrigen] = $dcatIdDestino;
		$insertCats++;
	}

	// Insertar faltas destino usando el mapeo
	$sqlInsFalta = "INSERT INTO " . BD_DISCIPLINA . ".disciplina_faltas
	                (dfal_id, dfal_nombre, dfal_id_categoria, dfal_codigo, dfal_institucion, dfal_year)
	                VALUES (?, ?, ?, ?, ?, ?)";
	$stmtInsFalta = $conexionPDO->prepare($sqlInsFalta);

	$insertFals = 0;
	foreach ($falsOrigen as $fal) {
		$codigo = (string)($fal['dfal_codigo'] ?? '');
		$nombre = (string)($fal['dfal_nombre'] ?? '');
		$catOrigen = (string)($fal['dfal_id_categoria'] ?? '');
		$nombre = trim($nombre);
		$codigo = trim($codigo);
		$catOrigen = trim($catOrigen);

		if ($nombre === '' || $catOrigen === '') {
			throw new Exception('Falta inválida en el año origen.');
		}
		if (!isset($mapCategorias[$catOrigen])) {
			throw new Exception('No se pudo mapear la categoría de una falta.');
		}

		$dfalId = Utilidades::generateCode("FL");
		$catDestino = $mapCategorias[$catOrigen];

		$stmtInsFalta->bindParam(1, $dfalId, PDO::PARAM_STR);
		$stmtInsFalta->bindParam(2, $nombre, PDO::PARAM_STR);
		$stmtInsFalta->bindParam(3, $catDestino, PDO::PARAM_STR);
		$stmtInsFalta->bindParam(4, $codigo, PDO::PARAM_STR);
		$stmtInsFalta->bindParam(5, $idInstitucion, PDO::PARAM_INT);
		$stmtInsFalta->bindParam(6, $yearDestino, PDO::PARAM_INT);
		$stmtInsFalta->execute();

		$insertFals++;
	}

	$conexionPDO->commit();

	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");

	jsonResponse([
		'success' => true,
		'message' => "Importación completada: {$insertCats} categorías y {$insertFals} faltas.",
		'importado' => [
			'categorias' => $insertCats,
			'faltas' => $insertFals
		]
	]);
} catch (Exception $e) {
	if (!empty($conexionPDO) && $conexionPDO instanceof PDO && $conexionPDO->inTransaction()) {
		$conexionPDO->rollBack();
	}

	jsonResponse([
		'success' => false,
		'message' => 'Error al importar: ' . $e->getMessage()
	]);
}

