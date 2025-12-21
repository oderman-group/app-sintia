<?php
include("session.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/Estudiantes.php");
require_once("../class/BindSQL.php");
require '../../librerias/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Set unlimited time limit
set_time_limit(0);
ini_set('memory_limit', '-1');

// Initialize progress
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['import_progress'] = 0;
session_write_close(); // Close session to allow progress polling

$totalRows = 0;
$processedRows = 0;
$createdCount = 0;
$updatedCount = 0;
$errorCount = 0;
$errors = [];

try {
    if (empty($_FILES['planilla']['name'])) {
        throw new Exception("No se ha seleccionado ningún archivo.");
    }

    $fileName = $_FILES['planilla']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileExtension !== 'xlsx') {
        throw new Exception("El archivo debe ser un formato .xlsx");
    }

    $filaFinal = isset($_POST['filaFinal']) ? intval($_POST['filaFinal']) : 200;
    $crearActualizar = isset($_POST['crear_actualizar']) ? intval($_POST['crear_actualizar']) : 1;
    $actualizarCampo = isset($_POST['actualizarCampo']) ? $_POST['actualizarCampo'] : [];

    $temName = $_FILES['planilla']['tmp_name'];
    $spreadsheet = IOFactory::load($temName);
    $worksheet = $spreadsheet->getActiveSheet();

    // Header validation
    $expectedHeaders = ['mat_documento', 'mat_nombres', 'mat_primer_apellido', 'mat_grado', 'mat_grupo', 'mat_tipo_documento', 'mat_nombre2', 'mat_segundo_apellido', 'mat_fecha_nacimiento', 'mat_email', 'acudiente_documento'];
    $headerRow = $worksheet->getRowIterator(1, 1)->current();
    $cellIterator = $headerRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $actualHeaders = [];
    foreach ($cellIterator as $cell) {
        $actualHeaders[] = $cell->getValue();
    }

    if ($expectedHeaders !== $actualHeaders) {
        throw new Exception("Las cabeceras del archivo no coinciden con la plantilla. Cabeceras esperadas: " . implode(', ', $expectedHeaders));
    }

    $totalRows = $filaFinal - 1; // Assuming data starts from row 2

    $clavePorDefectoUsuarios = SHA1("12345678");

    for ($row = 2; $row <= $filaFinal; $row++) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Assuming column order from the template
        $nDoc = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
        $nombres = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
        $apellido1 = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
        $grado = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
        $grupo = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
        $tipoD = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
        $nombre2 = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
        $apellido2 = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
        $fNac = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
        $email = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
        $acudienteDoc = $worksheet->getCellByColumnAndRow(11, $row)->getValue();

        if (empty($nDoc) || empty($nombres) || empty($apellido1) || empty($grado)) {
            $errors[] = "Fila $row: Faltan datos obligatorios (Documento, Nombre, Apellido, Grado).";
            $errorCount++;
            continue;
        }

        $existe = Estudiantes::validarExistenciaEstudiante($nDoc);
        $idAcudiente = null;
        if(!empty($acudienteDoc)){
            $datosAcudiente = Usuarios::obtenerDatosUsuario($acudienteDoc);
            if($datosAcudiente){
                $idAcudiente = $datosAcudiente['uss_id'];
            }
        }

        if ($existe > 0 && $crearActualizar == 2) {
            // Update existing student
            $datosEstudiante = Estudiantes::obtenerDatosEstudiante($nDoc);
            $idEstudiante = $datosEstudiante['mat_id'];
            
            $updateData = [];
            if (in_array('1', $actualizarCampo)) $updateData['mat_grado'] = $grado;
            if (in_array('2', $actualizarCampo)) $updateData['mat_grupo'] = $grupo;
            if (in_array('3', $actualizarCampo)) $updateData['mat_tipo_documento'] = $tipoD;
            if (in_array('4', $actualizarCampo) && $idAcudiente) $updateData['mat_acudiente'] = $idAcudiente;
            if (in_array('5', $actualizarCampo)) $updateData['mat_nombre2'] = $nombre2;
            if (in_array('6', $actualizarCampo)) $updateData['mat_fecha_nacimiento'] = $fNac;
            
            if (!empty($updateData)) {
                Estudiantes::actualizarMatriculasPorId($config, $idEstudiante, $updateData);
                $updatedCount++;
            }

        } elseif ($existe == 0) {
            // Create new student
            $idUsuarioEstudiante = UsuariosPadre::guardarUsuario($conexionPDO, "uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_idioma, uss_bloqueado, uss_fecha_registro, uss_responsable_registro, uss_intentos_fallidos, uss_tipo_documento, uss_apellido1, uss_apellido2, uss_nombre2,uss_documento, institucion, year, uss_id",
            [$nDoc, $clavePorDefectoUsuarios, 4, $nombres, 0, 1, 0, date('Y-m-d H:i:s'), $_SESSION["id"], 0, $tipoD, $apellido1, $apellido2, $nombre2, $nDoc, $config['conf_id_institucion'], $_SESSION["bd"]]);

            $postData = [
                "nDoc" => $nDoc,
                "nombres" => $nombres,
                "apellido1" => $apellido1,
                "grado" => $grado,
                "grupo" => $grupo,
                "tipoD" => $tipoD,
                "nombre2" => $nombre2,
                "apellido2" => $apellido2,
                "fNac" => $fNac,
                "email" => $email
            ];
            
            Estudiantes::insertarEstudiantes(
                $conexionPDO,
                $postData,
                $idUsuarioEstudiante,
                '', // result_numMat
                '', // procedencia
                $idAcudiente, // idAcudiente
                Estudiantes::IMPORTAR_EXCEL,
                $config['conf_id_institucion'],
                $_SESSION["bd"]
            );
            $createdCount++;
        }

        $processedRows++;
        $_SESSION['import_progress'] = ($processedRows / $totalRows) * 100;
        session_write_close(); // Close session after updating progress
        usleep(100000); // Sleep for 100ms to simulate work and prevent server overload
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['import_progress'] = 100;
    session_write_close();

    $summary = "Importación completada: \n";
    $summary .= "$createdCount estudiantes creados.\n";
    $summary .= "$updatedCount estudiantes actualizados.\n";
    $summary .= "$errorCount errores.\n";
    if ($errorCount > 0) {
        $summary .= "Errores:\n" . implode("\n", $errors);
    }
    echo $summary;

} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error: " . $e->getMessage();
}
