<?php
include("session.php");
$idPaginaInterna = 'DT0001';
header('Content-Type: application/json');

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/compartido/sintia-funciones.php");

$matId = $_POST['mat_id'] ?? '';
if (empty($matId)) {
    echo json_encode(['success' => false, 'message' => 'Identificador inválido.']);
    exit();
}

try {
    $datos = Estudiantes::obtenerDatosEstudiante($matId);
    if (empty($datos)) {
        echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado.']);
        exit();
    }

    $usuariosFunciones = new UsuariosFunciones();
    $nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($datos);
    $fotoEstudiante = $usuariosFunciones->verificarFoto($datos['mat_foto'] ?? '');

    $acudienteInfo = ['nombre' => 'No registrado', 'id' => ''];
    if (!empty($datos['mat_acudiente'])) {
        $acudiente = UsuariosPadre::sesionUsuario($datos['mat_acudiente']);
        if (!empty($acudiente)) {
            $acudienteInfo['nombre'] = UsuariosPadre::nombreCompletoDelUsuario($acudiente);
            $acudienteInfo['id'] = $acudiente['uss_id'];
        }
    }

    $nombreGenero = obtenerOpcionGeneralNombre(4, $datos['mat_genero'] ?? null);
    $nombreEstrato = obtenerOpcionGeneralNombre(3, $datos['mat_estrato'] ?? null);
    $nombreTipoDoc = obtenerOpcionGeneralNombre(1, $datos['mat_tipo_documento'] ?? null);
    $nombreTipoSangre = obtenerOpcionGeneralNombre(14, $datos['mat_tipo_sangre'] ?? null);

    ob_start();
    ?>
    <div class="row">
        <div class="col-md-2 text-center">
            <div class="student-photo-container">
                <?php if (!empty($fotoEstudiante)) { ?>
                    <img src="<?= htmlspecialchars($fotoEstudiante); ?>" alt="Foto del estudiante" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                <?php } else { ?>
                    <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; background-color: #f8f9fa;">
                        <i class="fa fa-user fa-3x text-muted"></i>
                    </div>
                <?php } ?>
                <p class="mt-2 mb-0"><small class="text-muted">Foto del Estudiante</small></p>
            </div>
        </div>
        <div class="col-md-4">
            <h6 class="text-primary mb-3"><i class="fa fa-user"></i> Información Personal</h6>
            <div class="row">
                <div class="col-6">
                    <p class="mb-2"><strong>Documento:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_documento'] ?? 'No disponible'); ?></span></p>
                    <p class="mb-2"><strong>Tipo Doc:</strong><br><span class="text-muted"><?= htmlspecialchars($nombreTipoDoc); ?></span></p>
                    <p class="mb-2"><strong>Fecha Nacimiento:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_fecha_nacimiento'] ?? 'No disponible'); ?></span></p>
                    <p class="mb-2"><strong>Género:</strong><br><span class="text-muted"><?= htmlspecialchars($nombreGenero); ?></span></p>
                </div>
                <div class="col-6">
                    <p class="mb-2"><strong>Dirección:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_direccion'] ?? 'No disponible'); ?></span></p>
                    <p class="mb-2"><strong>Barrio:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_barrio'] ?? 'No disponible'); ?></span></p>
                    <p class="mb-2"><strong>Celular:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_celular'] ?? 'No disponible'); ?></span></p>
                    <p class="mb-2"><strong>Email:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_email'] ?? 'No disponible'); ?></span></p>
                    <p class="mb-2"><strong>Estrato:</strong><br><span class="text-muted"><?= htmlspecialchars($nombreEstrato); ?></span></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <h6 class="text-success mb-3"><i class="fa fa-graduation-cap"></i> Información Académica</h6>
            <p class="mb-2"><strong>Nombre:</strong><br><span class="text-muted"><?= htmlspecialchars($nombreEstudiante); ?></span></p>
            <p class="mb-2"><strong>Grado:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['gra_nombre'] ?? 'No disponible'); ?></span></p>
            <p class="mb-2"><strong>Grupo:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['gru_nombre'] ?? 'No disponible'); ?></span></p>
            <p class="mb-2"><strong>Fecha Matrícula:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_fecha'] ?? 'No disponible'); ?></span></p>
            <p class="mb-2"><strong>Usuario:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['uss_usuario'] ?? 'No disponible'); ?></span></p>
        </div>
        <div class="col-md-3">
            <h6 class="text-warning mb-3"><i class="fa fa-users"></i> Información del Acudiente</h6>
            <p class="mb-2"><strong>Nombre:</strong><br>
                <?php if (!empty($acudienteInfo['id'])) { ?>
                    <a href="usuarios-editar.php?id=<?= base64_encode($acudienteInfo['id']); ?>" class="text-primary"><?= htmlspecialchars($acudienteInfo['nombre']); ?></a>
                <?php } else { ?>
                    <span class="text-muted"><?= htmlspecialchars($acudienteInfo['nombre']); ?></span>
                <?php } ?>
            </p>
            <?php if (!empty($acudienteInfo['id'])) { ?>
                <p class="mb-2"><strong>ID Acudiente:</strong><br><span class="text-muted"><?= htmlspecialchars($acudienteInfo['id']); ?></span></p>
                <div class="mt-3">
                    <a href="mensajes-redactar.php?para=<?= base64_encode($acudienteInfo['id']); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-envelope"></i> Enviar Mensaje
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <hr>
            <h6 class="text-info mb-3"><i class="fa fa-info-circle"></i> Información Adicional</h6>
            <div class="row">
                <div class="col-md-3">
                    <p class="mb-1"><strong>Grupo Sanguíneo:</strong><br><span class="text-muted"><?= htmlspecialchars($nombreTipoSangre); ?></span></p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Teléfono:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_telefono'] ?? 'No disponible'); ?></span></p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Celular 2:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_celular2'] ?? 'No disponible'); ?></span></p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Ciudad Residencia:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_ciudad_residencia'] ?? 'No disponible'); ?></span></p>
                </div>
            </div>
            <?php if (!empty($datos['mat_matricula'])) { ?>
            <div class="row mt-2">
                <div class="col-md-3">
                    <p class="mb-1"><strong>Número Matrícula:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_matricula']); ?></span></p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Código Tesorería:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_codigo_tesoreria'] ?? 'No disponible'); ?></span></p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Folio:</strong><br><span class="text-muted"><?= htmlspecialchars($datos['mat_folio'] ?? 'No disponible'); ?></span></p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Valor Matrícula:</strong><br><span class="text-muted">$<?= number_format($datos['mat_valor_matricula'] ?? 0, 0, ',', '.'); ?></span></p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    echo json_encode(['success' => true, 'html' => $html]);
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

function obtenerOpcionGeneralNombre($grupo, $id) {
    static $cache = [];
    global $conexion, $baseDatosServicios;
    if (empty($id)) {
        return 'No disponible';
    }
    $clave = $grupo.'-'.$id;
    if (isset($cache[$clave])) {
        return $cache[$clave];
    }
    $nombre = 'No disponible';
    if ($stmt = mysqli_prepare($conexion, "SELECT ogen_nombre FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=? AND ogen_id=? LIMIT 1")) {
        mysqli_stmt_bind_param($stmt, 'ii', $grupo, $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($fila = mysqli_fetch_assoc($result)) {
            $nombre = $fila['ogen_nombre'];
        }
        mysqli_stmt_close($stmt);
    }
    $cache[$clave] = $nombre;
    return $nombre;
}

