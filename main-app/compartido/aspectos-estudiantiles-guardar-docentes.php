<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0017';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$usuariosClase = new Usuarios;

//CONSUTLAR CARGA PARA DIRECTOR DE GRUPO
$carga = CargaAcademica::traerCargaDirectorGrupo($config, $_POST["curso"]);

//PARA NOTAS DE COMPORTAMIENTO
try{
    $numD = mysqli_num_rows( mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota
    WHERE dn_cod_estudiante='".$_POST["estudiante"]."' AND dn_periodo='".$_POST["periodo"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"));
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

if($numD==0){
    try{
        mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$_POST["estudiante"]."' AND dn_periodo='".$_POST["periodo"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
    
    $idInsercion=Utilidades::generateCode("DN");
    try{
        mysqli_query($conexion, "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_aspecto_academico, dn_aspecto_convivencial, dn_periodo, dn_id_carga, institucion, year)VALUES('" .$idInsercion . "', '".$_POST["estudiante"]."','".$_POST["academicos"]."','".$_POST["convivenciales"]."', '".$_POST["periodo"]."', '".$carga['car_id']."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
        $idRegistro = mysqli_insert_id($conexion);
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}else{
    try{
        mysqli_query($conexion, "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_aspecto_academico='".$_POST["academicos"]."', dn_aspecto_convivencial='".$_POST["convivenciales"]."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$_POST["estudiante"]."' AND dn_periodo='".$_POST["periodo"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        $idRegistro = $_POST["estudiante"]."-".$_POST["periodo"];
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'aspectos-estudiantiles.php?idR='.$_POST["idR"]."&success=SC_DT_1&id=".base64_encode($idRegistro));

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();