<?php
include("session.php");

require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

$notasNuevas=[];

if(!empty($_POST["selectCargasOrigen"])){
    $notasNuevas = array_combine($_POST["selectCargasOrigen"], $_POST["selectCargasDestino"]);
}



$consultaEstudiante = Estudiantes::obtenerListadoDeEstudiantes(" AND mat_id='" . $_POST["estudiante"] . "'");
$estudiante         = mysqli_fetch_array($consultaEstudiante, MYSQLI_BOTH);

$consulta = CargaAcademica::consultarEstudianteMateriasNotasPeridos($estudiante['mat_grado'], $estudiante["mat_id"], $estudiante["mat_grupo"]);
if(!empty($consulta)){
    $respaldo = [];
    $cont     = 0;
    foreach($consulta as $nota){
        $respaldo[$cont]=[
            "car_id"      => $nota["car_id"],
            "car_materia" => $nota["car_materia"],
            "materia"     => $nota["mat_nombre"],
            "id_docente"  => $nota["car_docente"],
            "periodo"     => $nota["bol_periodo"],
            "nota"        => $nota["bol_nota"],
            "grupo"       => $estudiante["mat_grupo"],
            "bol_id"      => $nota["bol_id"]
        ];
        $cont++; 
    }; 
    $json_data = json_encode($respaldo, JSON_PRETTY_PRINT); 
    $directory = 'respaldo/notas/';
    $file_name = $directory.$estudiante["mat_id"].'_'.$config['conf_id_institucion'].'_'.$_SESSION["bd"].'_grupo'.$estudiante['mat_grupo'].'.json';

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true); // Crear el directorio con permisos 0755 y `true` para crear directorios anidados
    }
    file_put_contents($file_name, $json_data);
};



$pasarNotas=!empty($_POST["pasarNotas"]) ? 1 : 0;

$contadorCargasActualizadas = 0;
if($pasarNotas == 1 ){
    foreach ($notasNuevas as $carga => $nuevaCarga){
        $cargaNueva = explode("|", $nuevaCarga);

        for ($i = 0; $i < count($respaldo); $i++ ) {
            if( $respaldo[$i]["car_id"] == $carga ) {
                $filter = " AND bol_id = '".$respaldo[$i]["bol_id"]."'";
                $update = ['bol_carga' => $cargaNueva[0]];
                if( $cargaNueva[0] != $carga ) {
                    Boletin::actualizarBoletinCargaEstudiante($config, $carga, $_POST["estudiante"], $update,$_SESSION["bd"],$filter);
                    $contadorCargasActualizadas ++;
                };
            }
        }
    };
}


$update = [
    'mat_grupo' => $_POST["grupoNuevo"]
];

Estudiantes::actualizarMatriculasPorId($config, $_POST["estudiante"], $update);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

$msj = "Se actualizaron (".$contadorCargasActualizadas.") notas para el estudiante ".Estudiantes::NombreCompletoDelEstudiante($estudiante);
$referer = $_SERVER["HTTP_REFERER"];
if (strpos($referer, needle: '?') !== false) {
    // La URL ya tiene parámetros, usa '&'
    $referer .= '&success=SC_DT_4&summary='.base64_encode($msj).'&id='.base64_encode($_POST["estudiante"]);
} else {
    // La URL no tiene parámetros, usa '?'
    $referer .= '?success=SC_DT_4&summary='.base64_encode($msj).'&id='.base64_encode($_POST["estudiante"]);
}
echo '<script type="text/javascript">window.location.href="'.$referer.'";</script>';
// echo '<script type="text/javascript"> $("#ModalCentralizado").modal("hide");</script>';
exit();