<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Cronograma.php");

header('Content-Type: application/json');

try {
    $carga = $_POST['carga'];
    $periodo = $_POST['periodo'];
    
    if (empty($carga) || empty($periodo)) {
        throw new Exception('Parámetros requeridos faltantes');
    }
    
    $conteos = [
        'indicadores' => 0,
        'calificaciones' => 0,
        'clases' => 0,
        'actividades' => 0,
        'evaluaciones' => 0,
        'cronograma' => 0
    ];
    
    // Contar Indicadores
    $indConsulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $carga, $periodo);
    $conteos['indicadores'] = mysqli_num_rows($indConsulta);
    
    // Contar Calificaciones (actividades relacionadas con indicadores)
    if ($conteos['indicadores'] > 0) {
        $totalCalificaciones = 0;
        mysqli_data_seek($indConsulta, 0); // Resetear el puntero
        while($indDatos = mysqli_fetch_array($indConsulta, MYSQLI_BOTH)) {
            $calConsulta = Actividades::traerActividadesCargaIndicador($config, $indDatos['ai_ind_id'], $carga, $periodo);
            $totalCalificaciones += mysqli_num_rows($calConsulta);
        }
        $conteos['calificaciones'] = $totalCalificaciones;
    }
    
    // Contar Clases
    $claConsulta = Clases::traerClasesCargaPeriodo($conexion, $config, $carga, $periodo);
    $conteos['clases'] = mysqli_num_rows($claConsulta);
    
    // Contar Actividades (tareas)
    $actConsulta = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_actividad_tareas 
        WHERE tar_id_carga='".$carga."' AND tar_periodo='".$periodo."' AND tar_estado=1 
        AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
    $actDatos = mysqli_fetch_array($actConsulta, MYSQLI_BOTH);
    $conteos['actividades'] = $actDatos['total'];
    
    // Contar Evaluaciones (foros)
    $evalConsulta = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_actividad_foro 
        WHERE foro_id_carga='".$carga."' AND foro_periodo='".$periodo."' AND foro_estado=1 
        AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
    $evalDatos = mysqli_fetch_array($evalConsulta, MYSQLI_BOTH);
    $conteos['evaluaciones'] = $evalDatos['total'];
    
    // Contar Cronograma
    $croConsulta = Cronograma::traerDatosCompletosCronograma($conexion, $config, $carga, $periodo);
    $conteos['cronograma'] = mysqli_num_rows($croConsulta);
    
    echo json_encode([
        'success' => true,
        'data' => $conteos
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
