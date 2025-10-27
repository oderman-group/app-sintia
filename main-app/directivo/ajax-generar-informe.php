<?php
/**
 * GENERAR INFORME ASÍNCRONO
 * Endpoint para generar informes de forma asíncrona sin recargar la página
 */

session_start();
header('Content-Type: application/json; charset=UTF-8');

$idPaginaInterna = 'CM0006';
include_once("../../config-general/config.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_academico_cargas.php");
require_once ROOT_PATH."/main-app/class/Asignaturas.php";
require_once(ROOT_PATH."/main-app/class/Sysjobs.php");

Modulos::validarAccesoDirectoPaginas();

try {
    // Obtener datos de la petición
    $input = json_decode(file_get_contents('php://input'), true);
    
    $tipoGeneracion = isset($input['tipo']) ? $input['tipo'] : 'manual'; // 'manual' o 'automatico'
    $cargaId = isset($input['carga']) ? $input['carga'] : '';
    $periodo = isset($input['periodo']) ? (int)$input['periodo'] : 0;
    $grado = isset($input['grado']) ? (int)$input['grado'] : 0;
    $grupo = isset($input['grupo']) ? (int)$input['grupo'] : 0;
    $tipoGrado = isset($input['tipoGrado']) ? (int)$input['tipoGrado'] : 0;
    
    // Validar datos requeridos
    if (empty($cargaId) || empty($periodo) || empty($grado) || empty($grupo)) {
        throw new Exception('Datos incompletos para generar el informe');
    }
    
    // Validar permisos
    if (!Modulos::validarSubRol(['DT0237'])) {
        throw new Exception('No tienes permisos para generar informes');
    }
    
    $conexionPDO = Conexion::newConnection('PDO');
    
    if ($tipoGeneracion === 'automatico') {
        // GENERACIÓN AUTOMÁTICA (Background Job)
        $parametros = [
            "carga"   => $cargaId,
            "periodo" => $periodo,
            "grado"   => $grado,
            "grupo"   => $grupo
        ];
        
        $mensaje = SysJobs::registrar(JOBS_TIPO_GENERAR_INFORMES, JOBS_PRIORIDAD_BAJA, $parametros);
        
        // Guardar historial
        include("../compartido/guardar-historial-acciones.php");
        
        echo json_encode([
            'success' => true,
            'tipo' => 'automatico',
            'message' => '✅ El informe se está generando en segundo plano. Recibirás una notificación cuando esté listo.',
            'job_id' => $mensaje
        ]);
        
    } else {
        // GENERACIÓN MANUAL (Inmediata)
        
        // Verificar si hay estudiantes sin notas completas
        if ($config['conf_porcentaje_completo_generar_informe'] == Boletin::GENERAR_CON_PORCENTAJE_COMPLETO) {
            $consultaListaEstudantesError = Estudiantes::listarEstudiantesNotasFaltantes($cargaId, $periodo, $tipoGrado);
            
            if (mysqli_num_rows($consultaListaEstudantesError) > 0) {
                $estudiantesFaltantes = [];
                while ($est = mysqli_fetch_array($consultaListaEstudantesError, MYSQLI_BOTH)) {
                    $estudiantesFaltantes[] = UsuariosPadre::nombreCompletoDelUsuario($est);
                }
                
                throw new Exception('No se puede generar el informe. Los siguientes estudiantes tienen notas pendientes: ' . implode(', ', array_slice($estudiantesFaltantes, 0, 5)) . (count($estudiantesFaltantes) > 5 ? '...' : ''));
            }
        }
        
        // Obtener datos de la carga
        $cargaDatos = CargaAcademica::traerCargaMateriaPorID($config, $cargaId);
        
        if (!$cargaDatos) {
            throw new Exception('No se encontró la carga académica');
        }
        
        $area = $cargaDatos['car_saberes_area'];
        $valorAsignatura = !empty($cargaDatos['mat_valor']) ? $cargaDatos['mat_valor'] : 100;
        
        // Obtener lista de estudiantes
        $listaEstudiantes = Estudiantes::listarEstudiantesEnGrados('', $grado, $grupo, $tipoGrado);
        
        $cantidadProcesados = 0;
        
        while ($estudiante = mysqli_fetch_array($listaEstudiantes, MYSQLI_BOTH)) {
            // Generar nota del boletín para este estudiante
            $notaPorPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $periodo, $estudiante['mat_id'], $cargaId);
            
            // Calcular nota equivalente
            $notaEquivalente = ($notaPorPeriodo * $valorAsignatura) / 100;
            
            // Registrar en boletín
            Boletin::guardarNotaBoletin(
                $conexionPDO,
                $estudiante['mat_id'],
                $cargaId,
                $notaPorPeriodo,
                $periodo,
                $notaEquivalente,
                $area
            );
            
            $cantidadProcesados++;
        }
        
        // Guardar historial
        include("../compartido/guardar-historial-acciones.php");
        
        echo json_encode([
            'success' => true,
            'tipo' => 'manual',
            'message' => '✅ Informe generado correctamente',
            'estudiantes_procesados' => $cantidadProcesados,
            'carga_id' => $cargaId,
            'periodo' => $periodo
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error al generar informe: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit();

