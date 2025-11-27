<?php
include("session.php");
require_once("../class/Inscripciones.php");

Utilidades::validarParametros($_GET);

$idPaginaInterna = 'DT0126'; // Assuming same permission as other student-related AJAX endpoints

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No tiene permisos para acceder a esta funcionalidad']);
    exit();
}

if (!empty($_GET["idEstudiante"])) {
    $idEstudiante = base64_decode($_GET["idEstudiante"]);

    // Obtener el ID de matrícula del estudiante
    $consultaMatricula = mysqli_query($conexion, "SELECT mat_id, mat_solicitud_inscripcion FROM ".BD_ACADEMICA.".academico_matriculas WHERE mat_id = '{$idEstudiante}' AND mat_eliminado = 0 AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");

    if (!$consultaMatricula) {
        error_log("Error en consulta de matrícula: " . mysqli_error($conexion));
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error al consultar matrícula']);
        exit();
    }

    if (mysqli_num_rows($consultaMatricula) > 0) {
        $datosMatricula = mysqli_fetch_array($consultaMatricula, MYSQLI_BOTH);
        $matriculaId = $datosMatricula['mat_id'];

        // Obtener comprobante de pago del aspirante
        $solicitudInscripcion = $datosMatricula['mat_solicitud_inscripcion'] ?? null;
        if ($solicitudInscripcion) {
            $consultaAspirante = mysqli_query($conexion, "SELECT asp_comprobante FROM ".BD_ADMISIONES.".aspirantes WHERE asp_id = '{$solicitudInscripcion}'");

            if (!$consultaAspirante) {
                error_log("Error en consulta de aspirante: " . mysqli_error($conexion));
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Error al consultar aspirante']);
                exit();
            }

            $comprobantePago = null;
            if (mysqli_num_rows($consultaAspirante) > 0) {
                $datosAspirante = mysqli_fetch_array($consultaAspirante, MYSQLI_BOTH);
                $comprobantePago = $datosAspirante['asp_comprobante'];
            } else {
                error_log("No se encontró aspirante con ID: {$solicitudInscripcion}");
            }
        } else {
            $comprobantePago = null;
            error_log("Matrícula {$matriculaId} no tiene solicitud de inscripción asociada");
        }

        // Obtener documentos usando la clase Inscripciones
        try {
            $documentos = Inscripciones::traerDocumentos($conexionPDO, $config, $matriculaId, $_SESSION["bd"]);
            error_log("Documentos obtenidos para matricula {$matriculaId}: " . ($documentos ? 'Encontrados' : 'No encontrados'));
        } catch (Exception $e) {
            error_log("Excepción al obtener documentos: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener documentos']);
            exit();
        }

        if ($documentos) {
            // Formatear los documentos para display
            $documentosFormateados = [
                'pazysalvo' => [
                    'titulo' => 'Paz y Salvo',
                    'archivo' => $documentos['matd_pazysalvo'],
                    'estado' => !empty($documentos['matd_pazysalvo']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-check-circle"></i>'
                ],
                'observador' => [
                    'titulo' => 'Observador',
                    'archivo' => $documentos['matd_observador'],
                    'estado' => !empty($documentos['matd_observador']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-user-graduate"></i>'
                ],
                'eps' => [
                    'titulo' => 'EPS',
                    'archivo' => $documentos['matd_eps'],
                    'estado' => !empty($documentos['matd_eps']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-heartbeat"></i>'
                ],
                'recomendacion' => [
                    'titulo' => 'Recomendación',
                    'archivo' => $documentos['matd_recomendacion'],
                    'estado' => !empty($documentos['matd_recomendacion']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-thumbs-up"></i>'
                ],
                'vacunas' => [
                    'titulo' => 'Tarjeta de Vacunas',
                    'archivo' => $documentos['matd_vacunas'],
                    'estado' => !empty($documentos['matd_vacunas']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-syringe"></i>'
                ],
                'boletines' => [
                    'titulo' => 'Boletines',
                    'archivo' => $documentos['matd_boletines_actuales'],
                    'estado' => !empty($documentos['matd_boletines_actuales']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-chart-line"></i>'
                ],
                'documento_identidad' => [
                    'titulo' => 'Documento de Identidad',
                    'archivo' => $documentos['matd_documento_identidad'],
                    'estado' => !empty($documentos['matd_documento_identidad']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-id-card"></i>'
                ],
                'certificados' => [
                    'titulo' => 'Certificados de Estudio',
                    'archivo' => $documentos['matd_certificados'],
                    'estado' => !empty($documentos['matd_certificados']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-certificate"></i>'
                ],
                'carta_laboral' => [
                    'titulo' => 'Carta Laboral',
                    'archivo' => $documentos['matd_carta_laboral'],
                    'estado' => !empty($documentos['matd_carta_laboral']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-briefcase"></i>'
                ],
                'registro_civil' => [
                    'titulo' => 'Registro Civil',
                    'archivo' => $documentos['matd_registro_civil'],
                    'estado' => !empty($documentos['matd_registro_civil']) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-file-contract"></i>'
                ],
                'comprobante_pago' => [
                    'titulo' => 'Comprobante de Pago',
                    'archivo' => !empty($comprobantePago) ? $comprobantePago : null,
                    'estado' => !empty($comprobantePago) ? 'Subido' : 'Pendiente',
                    'icono' => '<i class="fas fa-money-bill-wave"></i>'
                ]
            ];

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'documentos' => $documentosFormateados
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se encontraron documentos para este estudiante.'
            ]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró matrícula para este estudiante.'
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros inválidos.'
    ]);
}