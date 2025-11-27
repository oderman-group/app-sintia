<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0017';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$usuariosClase = new UsuariosFunciones;

//CONSUTLAR CARGA PARA DIRECTOR DE GRUPO
$carga = CargaAcademica::traerCargaDirectorGrupo($config, $_POST["curso"]);

//PARA NOTAS DE COMPORTAMIENTO - Migrado a PDO
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    
    $sqlCheck = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota
                 WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
    $stmtCheck = $conexionPDO->prepare($sqlCheck);
    $stmtCheck->bindParam(1, $_POST["estudiante"], PDO::PARAM_STR);
    $stmtCheck->bindParam(2, $_POST["periodo"], PDO::PARAM_INT);
    $stmtCheck->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmtCheck->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
    $stmtCheck->execute();
    $numD = $stmtCheck->rowCount();
    
    if($numD==0){
        $sqlDelete = "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota 
                      WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
        $stmtDelete = $conexionPDO->prepare($sqlDelete);
        $stmtDelete->bindParam(1, $_POST["estudiante"], PDO::PARAM_STR);
        $stmtDelete->bindParam(2, $_POST["periodo"], PDO::PARAM_INT);
        $stmtDelete->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtDelete->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtDelete->execute();
        
        $idInsercion=Utilidades::generateCode("DN");
        $sqlInsert = "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(
            dn_id, dn_cod_estudiante, dn_aspecto_academico, dn_aspecto_convivencial, 
            dn_periodo, dn_id_carga, institucion, year
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conexionPDO->prepare($sqlInsert);
        $stmtInsert->bindParam(1, $idInsercion, PDO::PARAM_STR);
        $stmtInsert->bindParam(2, $_POST["estudiante"], PDO::PARAM_STR);
        $stmtInsert->bindParam(3, $_POST["academicos"], PDO::PARAM_STR);
        $stmtInsert->bindParam(4, $_POST["convivenciales"], PDO::PARAM_STR);
        $stmtInsert->bindParam(5, $_POST["periodo"], PDO::PARAM_INT);
        $stmtInsert->bindParam(6, $carga['car_id'], PDO::PARAM_STR);
        $stmtInsert->bindParam(7, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtInsert->bindParam(8, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtInsert->execute();
        $idRegistro = $conexionPDO->lastInsertId();
    }else{
        $sqlUpdate = "UPDATE ".BD_DISCIPLINA.".disiplina_nota 
                      SET dn_aspecto_academico=?, dn_aspecto_convivencial=?, dn_fecha_aspecto=now() 
                      WHERE dn_cod_estudiante=? AND dn_periodo=? AND institucion=? AND year=?";
        $stmtUpdate = $conexionPDO->prepare($sqlUpdate);
        $stmtUpdate->bindParam(1, $_POST["academicos"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(2, $_POST["convivenciales"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(3, $_POST["estudiante"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(4, $_POST["periodo"], PDO::PARAM_INT);
        $stmtUpdate->bindParam(5, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtUpdate->bindParam(6, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtUpdate->execute();
        $idRegistro = $_POST["estudiante"]."-".$_POST["periodo"];
    }
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'aspectos-estudiantiles.php?idR='.$_POST["idR"]."&success=SC_DT_1&id=".base64_encode($idRegistro));

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();