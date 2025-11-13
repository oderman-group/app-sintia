<?php
include("session.php");
require_once ROOT_PATH."/main-app/class/Modulos.php";

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validar permisos
    Modulos::verificarPermisoDev();
    
    // Recibir datos
    $institucionId = isset($_POST['institucion_id']) ? intval($_POST['institucion_id']) : 0;
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    
    // Verificar si es acción masiva (múltiples módulos) o individual
    $modulosIds = [];
    if (isset($_POST['modulos_ids']) && is_array($_POST['modulos_ids'])) {
        // Acción masiva
        $modulosIds = array_map('intval', $_POST['modulos_ids']);
    } elseif (isset($_POST['modulo_id'])) {
        // Acción individual
        $modulosIds = [intval($_POST['modulo_id'])];
    }
    
    if ($institucionId <= 0 || empty($modulosIds) || empty($accion)) {
        $response['message'] = 'Datos incompletos';
        echo json_encode($response);
        exit();
    }
    
    // Verificar que la institución existe
    $consultaInst = mysqli_query($conexion, "SELECT ins_id FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_id = {$institucionId} AND ins_enviroment='" . ENVIROMENT . "'");
    
    if (mysqli_num_rows($consultaInst) == 0) {
        $response['message'] = 'Institución no encontrada';
        echo json_encode($response);
        exit();
    }
    
    $totalProcesados = 0;
    $totalErrores = 0;
    $modulosConfigurados = []; // Array para rastrear módulos que necesitan configuración
    
    if ($accion === 'agregar') {
        foreach ($modulosIds as $moduloId) {
            // Verificar si ya existe
            $consultaExiste = mysqli_query($conexion, "SELECT ipmod_id FROM " . BD_ADMIN . ".instituciones_modulos 
                WHERE ipmod_institucion = {$institucionId} AND ipmod_modulo = {$moduloId}");
            
            if (mysqli_num_rows($consultaExiste) == 0) {
                // Insertar el módulo
                $insertado = mysqli_query($conexion, "INSERT INTO " . BD_ADMIN . ".instituciones_modulos (ipmod_institucion, ipmod_modulo) 
                    VALUES ({$institucionId}, {$moduloId})");
                
                if ($insertado) {
                    $totalProcesados++;
                    
                    // Marcar módulos que necesitan configuración
                    if ($moduloId == Modulos::MODULO_INSCRIPCIONES) {
                        $modulosConfigurados['inscripciones'] = true;
                    }
                    if ($moduloId == Modulos::MODULO_FINANCIERO) {
                        $modulosConfigurados['financiero'] = true;
                    }
                } else {
                    $totalErrores++;
                }
            } else {
                // Ya existe, contar como procesado
                $totalProcesados++;
            }
        }
        
        // Configurar módulo de Inscripciones (ID: 8)
        if (!empty($modulosConfigurados['inscripciones'])) {
            try {
                // Verificar si ya existe configuración
                $consultaConfig = mysqli_query($conexion, "SELECT cfgi_id FROM {$baseDatosAdmisiones}.config_instituciones 
                    WHERE cfgi_id_institucion = {$institucionId} AND cfgi_year = {$_SESSION["bd"]}");
                
                if (mysqli_num_rows($consultaConfig) == 0) {
                    $colorBG = !empty($_SESSION["datosUnicosInstitucion"]['ins_color_barra']) ? $_SESSION["datosUnicosInstitucion"]['ins_color_barra'] : '#41c4c4';
                    $yearInscription = $_SESSION["bd"] + 1;
                    
                    $sql = "INSERT INTO {$baseDatosAdmisiones}.config_instituciones(
                        cfgi_id_institucion,
                        cfgi_year,
                        cfgi_color_barra_superior,
                        cfgi_inscripciones_activas,
                        cfgi_politicas_texto,
                        cfgi_color_texto,
                        cfgi_mostrar_banner,
                        cfgi_year_inscripcion
                    ) VALUES (?, ?, ?, '0', 'Lorem ipsum...', 'white', '0', ?)";
                    
                    $stmt = mysqli_prepare($conexion, $sql);
                    mysqli_stmt_bind_param($stmt, "iisi", $institucionId, $_SESSION["bd"], $colorBG, $yearInscription);
                    mysqli_stmt_execute($stmt);
                }
            } catch (Exception $e) {
                // Log del error pero continuar
                error_log("Error configurando módulo de inscripciones: " . $e->getMessage());
            }
        }
        
        // Configurar módulo Financiero (ID: 2)
        if (!empty($modulosConfigurados['financiero'])) {
            try {
                // Verificar si ya existe configuración
                $consultaConfig = mysqli_query($conexion, "SELECT id FROM ".BD_FINANCIERA.".configuration 
                    WHERE institucion = {$institucionId} AND year = {$_SESSION["bd"]}");
                    if (mysqli_num_rows($consultaConfig) == 0) {
                        // Insertar configuración inicial para el módulo financiero

                    $sql = "INSERT INTO ".BD_FINANCIERA.".configuration(
                        consecutive_start,
                        invoice_footer,
                        institucion,
                        `year`
                    ) VALUES ('1', 'Gracias por su preferencia', ?, ?)";
                    
                    $stmt = mysqli_prepare($conexion, $sql);
                    mysqli_stmt_bind_param($stmt, "is", $institucionId, $_SESSION["bd"]);
                    mysqli_stmt_execute($stmt);
                }
            } catch (Exception $e) {
                // Log del error pero continuar
                error_log("Error configurando módulo financiero: " . $e->getMessage());
            }
        }
        
        $response['success'] = true;
        $response['total_procesados'] = $totalProcesados;
        $response['total_errores'] = $totalErrores;
        
        // Construir mensaje informativo
        $mensajeExtra = [];
        if (!empty($modulosConfigurados['inscripciones'])) {
            $mensajeExtra[] = 'Inscripciones configurado';
        }
        if (!empty($modulosConfigurados['financiero'])) {
            $mensajeExtra[] = 'Financiero configurado';
        }
        
        if (count($modulosIds) == 1) {
            $response['message'] = 'Módulo asignado correctamente';
            if (!empty($mensajeExtra)) {
                $response['message'] .= ' (' . implode(', ', $mensajeExtra) . ')';
            }
        } else {
            $response['message'] = "{$totalProcesados} módulos asignados correctamente";
            if ($totalErrores > 0) {
                $response['message'] .= " ({$totalErrores} errores)";
            }
            if (!empty($mensajeExtra)) {
                $response['message'] .= ' | ' . implode(', ', $mensajeExtra);
            }
        }
        $response['modulos_configurados'] = $modulosConfigurados;
        
    } elseif ($accion === 'remover') {
        // Construir lista de IDs para DELETE masivo
        $modulosIdsStr = implode(',', $modulosIds);
        
        $deleted = mysqli_query($conexion, "DELETE FROM " . BD_ADMIN . ".instituciones_modulos 
            WHERE ipmod_institucion = {$institucionId} AND ipmod_modulo IN ({$modulosIdsStr})");
        
        if ($deleted) {
            $totalProcesados = mysqli_affected_rows($conexion);
            $response['success'] = true;
            $response['total_procesados'] = $totalProcesados;
            
            if (count($modulosIds) == 1) {
                $response['message'] = 'Módulo removido correctamente';
            } else {
                $response['message'] = "{$totalProcesados} módulos removidos correctamente";
            }
        } else {
            $response['message'] = 'Error al remover módulos';
        }
        
    } else {
        $response['message'] = 'Acción no válida';
    }
    
    // Registrar en historial
    include("../compartido/guardar-historial-acciones.php");
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>


