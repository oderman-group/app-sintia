<?php 
include("session.php");
require_once("../class/Estudiantes.php");

$opcionesConsulta = Estudiantes::listarEstudiantes(0,'','LIMIT 0, 100');
// $jsonData['acudidos'] = array();
$i=0;
// Migrado a PDO - Consulta preparada
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');
$sql = "SELECT * FROM ".BD_GENERAL.".usuarios_por_estudiantes 
        WHERE upe_id_usuario=? AND upe_id_estudiante=? AND institucion=? AND year=?";
$stmt = $conexionPDO->prepare($sql);

while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
    try{
        $stmt->bindParam(1, $_REQUEST['idA'], PDO::PARAM_STR);
        $stmt->bindParam(2, $opcionesDatos['mat_id'], PDO::PARAM_STR);
        $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
        $num = $stmt->rowCount();
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $nombre = Estudiantes::NombreCompletoDelEstudiante($opcionesDatos);
    $selected = " ";
    if($opcionesDatos['mat_acudiente']==$_REQUEST['idA'] AND $num>0){ $selected = 'selected';}
    $jsonData[$i]['value'] = $opcionesDatos['mat_id'];
    $jsonData[$i]['nombre'] = $nombre;
    $jsonData[$i]['select'] = $selected;
    $i++;
}
echo json_encode($jsonData);
?>