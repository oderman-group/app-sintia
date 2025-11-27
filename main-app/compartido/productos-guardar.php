<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0028';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

if (!empty($_FILES['imagen']['name'])) {
    $archivoSubido->validarArchivo($_FILES['imagen']['size'], $_FILES['imagen']['name']);
    $explode=explode(".", $_FILES['imagen']['name']);
    $extension = end($explode);
    $foto = uniqid($_SESSION["empresa"] . '_prod_') . "." . $extension;
    $destino = "../files/marketplace/productos";
    move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $foto);
}

$findme   = '?v=';
$pos = strpos($_POST["video"], $findme) + 3;
$video = substr($_POST["video"], $pos, 11);

$stock = 1;
if( !empty($_POST['stock']) && $_POST['stock'] > 0 ) {
    $stock = $_POST['stock']; 
}

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    
    $sql = "INSERT INTO " . $baseDatosMarketPlace . ".productos(
        prod_nombre, prod_descripcion, prod_foto, prod_precio, prod_activo, 
        prod_estado, prod_empresa, prod_video, prod_keywords, prod_categoria, prod_existencias
    ) VALUES (?, ?, ?, ?, 1, 0, ?, ?, ?, ?, ?)";
    
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["descripcion"], PDO::PARAM_STR);
    $stmt->bindParam(3, $foto, PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["precio"], PDO::PARAM_STR);
    $stmt->bindParam(5, $_SESSION["empresa"], PDO::PARAM_STR);
    $stmt->bindParam(6, $video, PDO::PARAM_STR);
    $stmt->bindParam(7, $_POST["keyw"], PDO::PARAM_STR);
    $stmt->bindParam(8, $_POST["categoria"], PDO::PARAM_STR);
    $stmt->bindParam(9, $stock, PDO::PARAM_INT);
    $stmt->execute();
    
    $idRegistro = $conexionPDO->lastInsertId();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'marketplace.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();