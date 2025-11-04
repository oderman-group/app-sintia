<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0027';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;

$clave = rand(10000, 99999);

// Migrado a PDO - Consultas preparadas
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    
    $sql = "INSERT INTO " . $baseDatosMarketPlace . ".empresas(
        emp_nombre, emp_email, emp_telefono, emp_verificada, emp_estado, 
        emp_clave, emp_usuario, emp_institucion
    ) VALUES (?, ?, ?, 0, 1, ?, ?, ?)";
    
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["email"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(4, $clave, PDO::PARAM_INT);
    $stmt->bindParam(5, $_SESSION["id"], PDO::PARAM_STR);
    $stmt->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->execute();
    
    $idRegistro = $conexionPDO->lastInsertId();
    
    if(!empty($_POST["sector"])){
        $sqlCat = "INSERT INTO " . $baseDatosMarketPlace . ".empresas_categorias(excat_empresa, excat_categoria) VALUES (?, ?)";
        $stmtCat = $conexionPDO->prepare($sqlCat);
        
        foreach ($_POST["sector"] as $sector) {
            $stmtCat->bindParam(1, $idRegistro, PDO::PARAM_INT);
            $stmtCat->bindParam(2, $sector, PDO::PARAM_STR);
            $stmtCat->execute();
        }
    }
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$_SESSION["empresa"] = $idRegistro;

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'productos-agregar.php?pp=1');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();