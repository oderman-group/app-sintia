<?php
include("session.php");
require_once(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Cronograma.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

header('Content-Type: application/json');

try {
    $cargaActual = $_POST['cargaActual'];
    $periodoActual = $_POST['periodoActual'];
    $cargaImportar = $_POST['cargaImportar'];
    $periodoImportar = $_POST['periodoImportar'];
    $selectedItems = $_POST['selectedItems'];
    
    if (empty($cargaActual) || empty($periodoActual) || empty($cargaImportar) || empty($periodoImportar) || empty($selectedItems)) {
        throw new Exception('Parámetros requeridos faltantes');
    }
    
    // Iniciar transacción
    mysqli_autocommit($conexion, false);
    
    $resultados = [
        'indicadores' => 0,
        'calificaciones' => 0,
        'clases' => 0,
        'actividades' => 0,
        'evaluaciones' => 0,
        'cronograma' => 0
    ];
    
    // Procesar cada tipo de elemento seleccionado
    foreach ($selectedItems as $item) {
        switch ($item) {
            case 'indicadores':
                if (empty($selectedItems['calificaciones'])) {
                    $resultados['indicadores'] = importarIndicadores($conexion, $conexionPDO, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar);
                }
                break;
                
            case 'calificaciones':
                $resultados['calificaciones'] = importarCalificaciones($conexion, $conexionPDO, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar);
                break;
                
            case 'clases':
                $resultados['clases'] = importarClases($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar);
                break;
                
            case 'actividades':
                $resultados['actividades'] = importarActividades($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar);
                break;
                
            case 'evaluaciones':
                $resultados['evaluaciones'] = importarEvaluaciones($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar);
                break;
                
            case 'cronograma':
                $resultados['cronograma'] = importarCronograma($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar);
                break;
        }
    }
    
    // Confirmar transacción
    mysqli_commit($conexion);
    
    // Guardar historial
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    
    echo json_encode([
        'success' => true,
        'data' => $resultados,
        'message' => 'Importación completada exitosamente'
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    mysqli_rollback($conexion);
    
    echo json_encode([
        'success' => false,
        'message' => 'Error en la importación: ' . $e->getMessage()
    ]);
} finally {
    mysqli_autocommit($conexion, true);
}

// Función para importar indicadores
function importarIndicadores($conexion, $conexionPDO, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar) {
    // Eliminar indicadores existentes
    Indicadores::eliminarCargaIndicadorPeriodo($conexion, $config, $cargaActual, $periodoActual);
    Actividades::eliminarActividadImportarCalificaciones($config, $cargaActual, $periodoImportar, $periodoActual);
    
    // Consultar indicadores a importar
    $indImpConsulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaImportar, $periodoImportar);
    $contador = 0;
    
    while($indImpDatos = mysqli_fetch_array($indImpConsulta, MYSQLI_BOTH)) {
        $idRegInd = $indImpDatos['ind_id'];
        
        // Si el indicador NO es obligatorio, recrearlo
        if($indImpDatos['ind_obligatorio'] == 0) {
            $idRegInd = Indicadores::guardarIndicador($conexionPDO, "ind_nombre, ind_periodo, ind_carga, ind_publico, institucion, year, ind_id", [
                mysqli_real_escape_string($conexion, $indImpDatos['ind_nombre']), 
                $periodoActual, 
                $cargaActual, 
                $indImpDatos['ind_publico'], 
                $config['conf_id_institucion'], 
                $_SESSION["bd"]
            ]);
        }
        
        $copiado = 0;
        if($indImpDatos['ipc_copiado'] != 0) $copiado = $indImpDatos['ipc_copiado'];
        
        Indicadores::guardarRelacionIndicadorCarga($conexionPDO, "ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado, institucion, year, ipc_id", [
            $cargaActual, 
            $idRegInd, 
            $indImpDatos['ipc_valor'], 
            $periodoActual, 
            1, 
            $copiado, 
            $config['conf_id_institucion'], 
            $_SESSION["bd"]
        ]);
        
        $contador++;
    }
    
    return $contador;
}

// Función para importar calificaciones
function importarCalificaciones($conexion, $conexionPDO, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar) {
    // Eliminar indicadores y calificaciones existentes
    Indicadores::eliminarCargaIndicadorPeriodo($conexion, $config, $cargaActual, $periodoActual);
    Actividades::eliminarActividadImportarCalificaciones($config, $cargaActual, $periodoImportar, $periodoActual);
    
    // Consultar indicadores a importar
    $indImpConsulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaImportar, $periodoImportar);
    $contadorCalificaciones = 0;
    
    while($indImpDatos = mysqli_fetch_array($indImpConsulta, MYSQLI_BOTH)) {
        $idRegInd = $indImpDatos['ai_ind_id'];
        
        // Si el indicador NO es obligatorio, recrearlo
        if($indImpDatos['ind_obligatorio'] == 0) {
            $idRegInd = Indicadores::guardarIndicador($conexionPDO, "ind_nombre, ind_periodo, ind_carga, ind_publico, institucion, year, ind_id", [
                mysqli_real_escape_string($conexion, $indImpDatos['ind_nombre']), 
                $periodoActual, 
                $cargaActual, 
                $indImpDatos['ind_publico'], 
                $config['conf_id_institucion'], 
                $_SESSION["bd"]
            ]);
        }
        
        $copiado = 0;
        if($indImpDatos['ipc_copiado'] != 0) $copiado = $indImpDatos['ipc_copiado'];
        
        Indicadores::guardarRelacionIndicadorCarga($conexionPDO, "ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado, institucion, year, ipc_id", [
            $cargaActual, 
            $idRegInd, 
            $indImpDatos['ipc_valor'], 
            $periodoActual, 
            1, 
            $copiado, 
            $config['conf_id_institucion'], 
            $_SESSION["bd"]
        ]);
        
        // Consultar calificaciones del indicador
        $calImpConsulta = Actividades::traerActividadesCargaIndicador($config, $indImpDatos['ai_ind_id'], $cargaImportar, $periodoImportar);
        
        while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)) {
            Actividades::guardarCalificacionManual($conexionPDO, $config, 
                mysqli_real_escape_string($conexion, $calImpDatos['act_descripcion']), 
                $calImpDatos['act_fecha'], 
                $cargaActual, 
                $idRegInd, 
                $periodoActual, 
                $calImpDatos['act_compartir'], 
                $calImpDatos['act_valor']
            );
            $contadorCalificaciones++;
        }
    }
    
    return $contadorCalificaciones;
}

// Función para importar clases
function importarClases($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar) {
    // Eliminar clases existentes
    Clases::eliminarClasesCargas($conexion, $config, $cargaActual, $periodoActual);
    
    // Consultar clases a importar
    $claImpConsulta = Clases::traerClasesCargaPeriodo($conexion, $config, $cargaImportar, $periodoImportar);
    $contador = 0;
    $datosInsert = '';
    
    while($claImpDatos = mysqli_fetch_array($claImpConsulta, MYSQLI_BOTH)) {
        $codigoCLS = Utilidades::generateCode("CLS");
        $datosInsert .= "('".$codigoCLS."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_tema'])."', now(), '".$cargaActual."', 0, now(), 1, '".$periodoActual."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_archivo'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_video'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_video_url'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_descripcion'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_archivo2'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_archivo3'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_nombre_archivo1'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_nombre_archivo2'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_nombre_archivo3'])."', '".mysqli_real_escape_string($conexion, $claImpDatos['cls_disponible'])."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";
        $contador++;
    }
    
    if(!empty($datosInsert)) {
        $datosInsert = substr($datosInsert, 0, -1);
        $query = "INSERT INTO ".BD_ACADEMICA.".academico_clases(cls_id, cls_tema, cls_fecha, cls_id_carga, cls_registrada, cls_fecha_creacion, cls_estado, cls_periodo, cls_archivo, cls_video, cls_video_url, cls_descripcion, cls_archivo2, cls_archivo3, cls_nombre_archivo1, cls_nombre_archivo2, cls_nombre_archivo3, cls_disponible, institucion, year) VALUES $datosInsert";
        
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al insertar clases: ' . mysqli_error($conexion));
        }
    }
    
    return $contador;
}

// Función para importar actividades
function importarActividades($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar) {
    // Desactivar actividades existentes
    $query = "UPDATE ".BD_ACADEMICA.".academico_actividad_tareas SET tar_estado=0 
        WHERE tar_id_carga='".$cargaActual."' AND tar_periodo='".$periodoActual."' 
        AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}";
    
    if (!mysqli_query($conexion, $query)) {
        throw new Exception('Error al desactivar actividades existentes: ' . mysqli_error($conexion));
    }
    
    // Consultar actividades a importar
    $query = "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_tareas 
        WHERE tar_id_carga='".$cargaImportar."' AND tar_periodo='".$periodoImportar."' 
        AND tar_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}";
    
    $calImpConsulta = mysqli_query($conexion, $query);
    if (!$calImpConsulta) {
        throw new Exception('Error al consultar actividades: ' . mysqli_error($conexion));
    }
    
    $contador = 0;
    $datosInsert = '';
    
    while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)) {
        $codigo = Utilidades::generateCode("TAR");
        $datosInsert .= "('".$codigo."', '".mysqli_real_escape_string($conexion, $calImpDatos['tar_titulo'])."', '".mysqli_real_escape_string($conexion, $calImpDatos['tar_descripcion'])."', '".$cargaActual."', '".$calImpDatos['tar_fecha_disponible']."', '".$calImpDatos['tar_fecha_entrega']."', '".mysqli_real_escape_string($conexion, $calImpDatos['tar_archivo'])."', '".$calImpDatos['tar_impedir_retrasos']."', '".$periodoActual."', 1, '".mysqli_real_escape_string($conexion, $calImpDatos['tar_archivo2'])."', '".mysqli_real_escape_string($conexion, $calImpDatos['ar_archivo3'])."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";
        $contador++;
    }
    
    if(!empty($datosInsert)) {
        $datosInsert = substr($datosInsert, 0, -1);
        $query = "INSERT INTO ".BD_ACADEMICA.".academico_actividad_tareas(tar_id, tar_titulo, tar_descripcion, tar_id_carga, tar_fecha_disponible, tar_fecha_entrega, tar_archivo, tar_impedir_retrasos, tar_periodo, tar_estado, tar_archivo2, ar_archivo3, institucion, year) VALUES $datosInsert";
        
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al insertar actividades: ' . mysqli_error($conexion));
        }
    }
    
    return $contador;
}

// Función para importar evaluaciones
function importarEvaluaciones($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar) {
    // Desactivar evaluaciones existentes
    $query = "UPDATE ".BD_ACADEMICA.".academico_actividad_foro SET foro_estado=0 
        WHERE foro_id_carga='".$cargaActual."' AND foro_periodo='".$periodoActual."' 
        AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}";
    
    if (!mysqli_query($conexion, $query)) {
        throw new Exception('Error al desactivar evaluaciones existentes: ' . mysqli_error($conexion));
    }
    
    // Consultar evaluaciones a importar
    $query = "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_foro 
        WHERE foro_id_carga='".$cargaImportar."' AND foro_periodo='".$periodoImportar."' 
        AND foro_estado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}";
    
    $calImpConsulta = mysqli_query($conexion, $query);
    if (!$calImpConsulta) {
        throw new Exception('Error al consultar evaluaciones: ' . mysqli_error($conexion));
    }
    
    $contador = 0;
    $datosInsert = '';
    
    while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)) {
        $codigo = Utilidades::generateCode("FORO");
        $datosInsert .= "('".$codigo."', '".mysqli_real_escape_string($conexion, $calImpDatos['foro_nombre'])."', '".mysqli_real_escape_string($conexion, $calImpDatos['foro_descripcion'])."', '".$cargaActual."', '".$periodoActual."', 1, {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";
        $contador++;
    }
    
    if(!empty($datosInsert)) {
        $datosInsert = substr($datosInsert, 0, -1);
        $query = "INSERT INTO ".BD_ACADEMICA.".academico_actividad_foro(foro_id, foro_nombre, foro_descripcion, foro_id_carga, foro_periodo, foro_estado, institucion, year) VALUES $datosInsert";
        
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al insertar evaluaciones: ' . mysqli_error($conexion));
        }
    }
    
    return $contador;
}

// Función para importar cronograma
function importarCronograma($conexion, $config, $cargaActual, $periodoActual, $cargaImportar, $periodoImportar) {
    // Consultar cronograma a importar
    $calImpConsulta = Cronograma::traerDatosCompletosCronograma($conexion, $config, $cargaImportar, $periodoImportar);
    
    $contador = 0;
    $datosInsert = '';
    
    while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)) {
        $idInsercion = Utilidades::generateCode("CRO");
        $datosInsert .= "('".$idInsercion."', '".mysqli_real_escape_string($conexion, $calImpDatos['cro_tema'])."', '".$calImpDatos['cro_fecha']."', '".$cargaActual."', '".mysqli_real_escape_string($conexion, $calImpDatos['cro_recursos'])."', '".$periodoActual."', '".mysqli_real_escape_string($conexion, $calImpDatos['cro_color'])."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}),";
        $contador++;
    }
    
    if(!empty($datosInsert)) {
        $datosInsert = substr($datosInsert, 0, -1);
        $query = "INSERT INTO ".BD_ACADEMICA.".academico_cronograma(cro_id, cro_tema, cro_fecha, cro_id_carga, cro_recursos, cro_periodo, cro_color, institucion, year) VALUES $datosInsert";
        
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al insertar cronograma: ' . mysqli_error($conexion));
        }
    }
    
    return $contador;
}
?>
