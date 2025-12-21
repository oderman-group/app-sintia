<?php
include("session.php");
$idPaginaInterna = 'DT0145';
include("../compartido/historial-acciones-guardar.php");

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Validar que se proporcionó un ID de promoción (decodificar de base64)
$idPromocion = !empty($_GET['id']) ? base64_decode($_GET['id']) : '';

if(empty($idPromocion) || empty($_SESSION['historial_promociones'][$idPromocion])){
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Error - Reversión</title>
        <script src="../../config-general/assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    </head>
    <body>
    <script type="text/javascript">
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se encontró el historial de promoción especificado.",
            confirmButtonText: "Entendido"
        }).then(() => {
            window.location.href = "cursos.php";
        });
    </script>
    </body>
    </html>';
    exit();
}

$historial = $_SESSION['historial_promociones'][$idPromocion];

// Iniciar transacción
mysqli_begin_transaction($conexion);

try {
    $estudiantesRevertidos = 0;
    $errores = [];
    
    foreach($historial['estudiantes'] as $estudiante){
        // Escapar valores para seguridad
        $idEstudiante = mysqli_real_escape_string($conexion, $estudiante['estudiante_id']);
        $gradoAnterior = mysqli_real_escape_string($conexion, $estudiante['grado_anterior']);
        $grupoAnterior = mysqli_real_escape_string($conexion, $estudiante['grupo_anterior']);
        
        // Manejar estado anterior (puede ser NULL, vacío o un número)
        $estadoAnterior = '';
        if(isset($estudiante['estado_anterior']) && $estudiante['estado_anterior'] !== '' && $estudiante['estado_anterior'] !== null){
            $estadoAnterior = (int)$estudiante['estado_anterior'];
            $sqlEstado = "mat_estado_matricula = " . $estadoAnterior;
        } else {
            $sqlEstado = "mat_estado_matricula = NULL";
        }
        
        // Construir la consulta SQL directamente para tener control total
        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas 
                SET mat_grado = '".$gradoAnterior."', 
                    mat_grupo = '".$grupoAnterior."', 
                    ".$sqlEstado.", 
                    mat_promocionado = 0 
                WHERE mat_id = '".$idEstudiante."' 
                AND institucion = ".$config['conf_id_institucion']." 
                AND year = ".$_SESSION['bd'];
        
        // Ejecutar la consulta
        $resultado = mysqli_query($conexion, $sql);
        
        if($resultado){
            // Verificar si realmente se actualizó alguna fila
            $filasAfectadas = mysqli_affected_rows($conexion);
            if($filasAfectadas > 0){
                $estudiantesRevertidos++;
            } else {
                $errores[] = "No se actualizó el estudiante ID: " . $estudiante['estudiante_id'] . " (posiblemente no existe o los datos ya están actualizados)";
            }
        } else {
            $errorDB = mysqli_error($conexion);
            $errores[] = "Error al revertir estudiante ID: " . $estudiante['estudiante_id'] . " - " . $errorDB;
        }
    }
    
    // Verificar si hubo algún error
    if(!empty($errores) && $estudiantesRevertidos == 0){
        // Si no se revirtió ningún estudiante, hacer rollback
        mysqli_rollback($conexion);
        throw new Exception("No se pudo revertir ningún estudiante. Errores: " . implode("; ", $errores));
    }
    
    // Si hubo algunos errores pero al menos uno se revirtió, continuar pero mostrar advertencia
    $mensajeAdvertencia = '';
    if(!empty($errores)){
        $mensajeAdvertencia = '<br><br><strong>Advertencias:</strong> '.count($errores).' estudiante(s) no pudieron ser revertidos.';
    }
    
    // Eliminar del historial solo si se revirtió al menos un estudiante
    if($estudiantesRevertidos > 0){
        unset($_SESSION['historial_promociones'][$idPromocion]);
    }
    
    // Confirmar transacción
    mysqli_commit($conexion);
    
    // Obtener nombres de cursos
    $consultaGrado = Grados::obtenerDatosGrados($historial['para']);
    $gradoDestino = mysqli_fetch_array($consultaGrado, MYSQLI_BOTH);
    
    $consultaGrado = Grados::obtenerDatosGrados($historial['desde']);
    $gradoOrigen = mysqli_fetch_array($consultaGrado, MYSQLI_BOTH);
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Reversión Exitosa</title>
        <script src="../../config-general/assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    </head>
    <body>
    <script type="text/javascript">
        Swal.fire({
            icon: "success",
            title: "¡Reversión Exitosa!",
            html: "Se revirtió la promoción de <strong>'.$estudiantesRevertidos.'</strong> estudiante(s).<br>Los estudiantes fueron devueltos de <strong>'.htmlspecialchars($gradoDestino['gra_nombre']).'</strong> a <strong>'.htmlspecialchars($gradoOrigen['gra_nombre']).'</strong>.'.$mensajeAdvertencia.'",
            confirmButtonText: "Continuar"
        }).then(() => {
            window.location.href = "cursos.php";
        });
    </script>
    </body>
    </html>';
    
} catch (Exception $e) {
    mysqli_rollback($conexion);
    
    error_log("Error en reversión de promoción: " . $e->getMessage());
    Utilidades::logError($e);
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Error - Reversión</title>
        <script src="../../config-general/assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    </head>
    <body>
    <script type="text/javascript">
        Swal.fire({
            icon: "error",
            title: "Error en la Reversión",
            html: "Ocurrió un error al revertir la promoción.<br><br><strong>Detalles:</strong> '.addslashes($e->getMessage()).'",
            confirmButtonText: "Entendido"
        }).then(() => {
            window.location.href = "cursos.php";
        });
    </script>
    </body>
    </html>';
}

exit();
