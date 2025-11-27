<?php
include("session.php");
require_once ROOT_PATH . "/main-app/class/Estudiantes.php";
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");

require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;

$idPaginaInterna = 'DT0126';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No tiene permisos para acceder a esta funcionalidad']);
    exit();
}

// Debug: Log received parameters
// error_log("AJAX Request - POST data: " . print_r($_POST, true));

$Plataforma = new Plataforma;

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
}

// Get parameters
$tipo = empty($_POST['tipo']) ? "" : base64_decode($_POST['tipo']);
$busqueda = empty($_POST['busqueda']) ? "" : $_POST['busqueda'];

$filtro = '';
if (!empty($tipo)) {
    $filtro .= " AND uss_tipo='" . $tipo . "'";
}

if (!empty($busqueda)) {
    $filtro .= " AND (
        uss_id LIKE '%" . $busqueda . "%'
        OR uss_nombre LIKE '%" . $busqueda . "%'
        OR uss_nombre2 LIKE '%" . $busqueda . "%'
        OR uss_apellido1 LIKE '%" . $busqueda . "%'
        OR uss_apellido2 LIKE '%" . $busqueda . "%'
        OR uss_usuario LIKE '%" . $busqueda . "%'
        OR uss_email LIKE '%" . $busqueda . "%'
        OR uss_documento LIKE '%" . $busqueda . "%'
        OR CONCAT(TRIM(uss_nombre), ' ',TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '%" . $busqueda . "%'
        OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1), TRIM(uss_apellido2)) LIKE '%" . $busqueda . "%'
        OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '%" . $busqueda . "%'
        OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '%" . $busqueda . "%'
    )";
}

$selectSql = [
    "uss_id",
    "uss_usuario",
    "uss_email",
    "uss_fecha_nacimiento",
    "uss_nombre",
    "uss_nombre2",
    "uss_foto",
    "uss_estado",
    "uss_apellido1",
    "uss_ultimo_ingreso",
    "uss_apellido2",
    "uss_tipo",
    "uss_permiso1",
    "pes_nombre",
    "uss_bloqueado",
    "uss_ultimo_ingreso"
];

$tipos = empty($tipo) ? [TIPO_DEV, TIPO_DOCENTE, TIPO_DIRECTIVO, TIPO_CLIENTE, TIPO_PROVEEDOR] : [$tipo];
$lista = Usuarios::listar($selectSql, $tipos, "uss_id");

$permisoHistorial = Modulos::validarSubRol(['DT0327']);
$permisoPlantilla = Modulos::validarSubRol(['DT0239']);

$contReg = 1;
$tableRows = '';

foreach ($lista as $usuario) {
    $bgColor = '';
    if ($usuario['uss_bloqueado'] == 1)
        $bgColor = '#ff572238';

    $cheked = '';
    if ($usuario['uss_bloqueado'] == 1) {
        $cheked = 'checked';
    }

    $mostrarNumAcudidos = '';
    if (isset($usuario['cantidad_acudidos']) && $usuario['uss_tipo'] == TIPO_ACUDIENTE) {
        $mostrarNumAcudidos = '<br><span style="font-size:9px; color:darkblue">(' . $usuario['cantidad_acudidos'] . ')  Acudidos)</span>';
    }

    $mostrarNumCargas = '';
    if (isset($usuario['cantidad_cargas']) && $usuario['uss_tipo'] == TIPO_DOCENTE) {
        $numCarga = $usuario['cantidad_cargas'];
        $mostrarNumCargas = '<br><span style="font-size:9px; color:darkblue">(' . $usuario['cantidad_cargas'] . ' Cargas)</span>';
    }

    $tieneMatricula = '';
    $backGroundMatricula = '';
    if ($usuario['uss_tipo'] == TIPO_ESTUDIANTE) {
        $tieneMatricula = $usuario['mat_id_usuario'];
        if (empty($usuario['mat_id_usuario'])) {
            $backGroundMatricula = 'style="background-color:gold;" class="animate__animated animate__pulse animate__delay-2s" data-toggle="tooltip" data-placement="right" title="Este supuesto estudiante no cuenta con un registro en las matrículas."';
        }
    }

    $managerPrimary = '';
    if ($usuario['uss_permiso1'] == CODE_PRIMARY_MANAGER && $usuario['uss_tipo'] == TIPO_DIRECTIVO) {
        $managerPrimary = '<i class="fa fa-user-circle text-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director principal"></i> ';
    }

    $fotoUsuario = $usuariosClase->verificarFoto($usuario['uss_foto']);
    $estadoUsuario = !empty($usuario['uss_estado']) ? $opcionEstado[$usuario['uss_estado']] : '';

    $infoTooltip = "
    <p>
        <img src='{$fotoUsuario}' class='img-thumbnail' width='120px;' height='120px;'>
    </p>
    <b>Sesión:</b><br>
    {$estadoUsuario}<br>
    <b>Último ingreso:</b><br>
    {$usuario['uss_ultimo_ingreso']}<br><br>
    <b>Email:</b><br>
    {$usuario['uss_email']}<br>
    <b>Fecha de nacimiento:</b><br>
    {$usuario['uss_fecha_nacimiento']}
    ";

    $tableRows .= '<tr id="reg' . $usuario['uss_id'] . '" style="background-color:' . $bgColor . ';">';
    $tableRows .= '<td><button class="btn btn-sm btn-info expand-btn" data-id="' . $usuario['uss_id'] . '"><i class="fa fa-plus"></i></button></td>';
    $tableRows .= '<td>' . $contReg . '</td>';
    $tableRows .= '<td>';
    $tableRows .= '<div class="input-group spinner col-sm-10">';
    $tableRows .= '<label class="switchToggle">';
    $tableRows .= '<input type="checkbox" onChange="getSelecionados(\'example1\',\'selecionado\',\'lblCantSeleccionados\')" id="' . $usuario['uss_id'] . '_select" name="selecionado">';
    $tableRows .= '<span class="slider aqua round"></span>';
    $tableRows .= '</label>';
    $tableRows .= '</div>';
    $tableRows .= '</td>';
    $tableRows .= '<td>';
    if (Modulos::validarPermisoEdicion() && ($usuario['uss_tipo'] != TIPO_DIRECTIVO || $usuario['uss_permiso1'] != CODE_PRIMARY_MANAGER)) {
        $tableRows .= '<div class="input-group spinner col-sm-10">';
        $tableRows .= '<label class="switchToggle">';
        $tableRows .= '<input type="checkbox" id="' . $usuario['uss_id'] . '" name="bloqueado" value="1" onChange="ajaxBloqueoDesbloqueo(this)" ' . $cheked . ' ' . $disabledPermiso . '>';
        $tableRows .= '<span class="slider red round"></span>';
        $tableRows .= '</label>';
        $tableRows .= '</div>';
    }
    $tableRows .= '</td>';
    $tableRows .= '<td>' . $usuario['uss_id'] . '</td>';
    $tableRows .= '<td>' . $usuario['uss_usuario'] . '</td>';
    $tableRows .= '<td>' . $managerPrimary;
    $tableRows .= '<a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="' . UsuariosPadre::nombreCompletoDelUsuario($usuario) . '" data-content="' . htmlspecialchars($infoTooltip) . '" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000;">' . UsuariosPadre::nombreCompletoDelUsuario($usuario) . '</a>';
    $tableRows .= '</td>';
    $tableRows .= '<td ' . $backGroundMatricula . '>' . $usuario['pes_nombre'] . '' . $mostrarNumCargas . '' . $mostrarNumAcudidos . '</td>';
    $tableRows .= '<td><span style="font-size: 11px;">' . $usuario['uss_ultimo_ingreso'] . '</span></td>';
    $tableRows .= '<td>';
    $tableRows .= '<div class="btn-group">';
    $tableRows .= '<button type="button" class="btn btn-primary">Acciones</button>';
    $tableRows .= '<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">';
    $tableRows .= '<i class="fa fa-angle-down"></i>';
    $tableRows .= '</button>';
    $tableRows .= '<ul class="dropdown-menu" role="menu">';

    if (Modulos::validarPermisoEdicion()) {
        if (($usuario['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula)) || $usuario['uss_tipo'] != TIPO_ESTUDIANTE) {
            if (Modulos::validarSubRol(['DT0124']) && ($usuario['uss_tipo'] != TIPO_DIRECTIVO || $usuario['uss_permiso1'] != CODE_PRIMARY_MANAGER)) {
                $tableRows .= '<li><a href="usuarios-editar.php?id=' . base64_encode($usuario['uss_id']) . '">Editar</a></li>';
            }
        }

        if (($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $usuario['uss_tipo'] != TIPO_DEV) ||
            ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $usuario['uss_tipo'] != TIPO_DEV && $usuario['uss_tipo'] != TIPO_DIRECTIVO && !isset($_SESSION['admin']) && !isset($_SESSION['devAdmin']))) {
            if ($usuario['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula) || $usuario['uss_tipo'] != TIPO_ESTUDIANTE) {
                $tableRows .= '<li><a href="auto-login.php?user=' . base64_encode($usuario['uss_id']) . '&tipe=' . base64_encode($usuario['uss_tipo']) . '">Autologin</a></li>';
            }
        }

        if ($usuario['uss_tipo'] == TIPO_ACUDIENTE && Modulos::validarSubRol(['DT0137'])) {
            $tableRows .= '<li><a href="usuarios-acudidos.php?id=' . base64_encode($usuario['uss_id']) . '">Acudidos</a></li>';
        }

        if ((isset($numCarga) && $numCarga == 0 && $usuario['uss_tipo'] == TIPO_DOCENTE) || $usuario['uss_tipo'] == TIPO_ACUDIENTE || ($usuario['uss_tipo'] == TIPO_ESTUDIANTE && empty($tieneMatricula)) || $usuario['uss_tipo'] == TIPO_CLIENTE || $usuario['uss_tipo'] == TIPO_PROVEEDOR) {
            $tableRows .= '<li><a href="javascript:void(0);" title="" name="usuarios-eliminar.php?id=' . base64_encode($usuario['uss_id']) . '" onClick="deseaEliminar(this)" id="' . $usuario['uss_id'] . '">Eliminar</a></li>';
        }
    }

    if ($usuario['uss_tipo'] == TIPO_DOCENTE && $numCarga > 0 && $permisoPlantilla) {
        $tableRows .= '<li><a href="../compartido/planilla-docentes.php?docente=' . base64_encode($usuario['uss_id']) . '" target="_blank">Planillas de las cargas</a></li>';
    }

    if (($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $usuario['uss_tipo'] != TIPO_DEV) ||
        ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $usuario['uss_tipo'] != TIPO_DEV && $usuario['uss_tipo'] != TIPO_DIRECTIVO) && $permisoHistorial) {
        $tableRows .= '<li><a href="../compartido/informe-historial-ingreso.php?id=' . base64_encode($usuario['uss_id']) . '" target="_blank">Historial de Ingreso</a></li>';
    }

    $tableRows .= '</ul>';
    $tableRows .= '</div>';
    $tableRows .= '</td>';
    $tableRows .= '</tr>';

    // Details row
    $tableRows .= '<tr class="details-row" id="details-' . $usuario['uss_id'] . '" style="display: none;">';
    $tableRows .= '<td colspan="9">';
    $tableRows .= '<div class="card">';
    $tableRows .= '<div class="card-body">';
    $tableRows .= '<div class="row">';
    $tableRows .= '<div class="col-md-3">';
    $tableRows .= '<img src="' . $fotoUsuario . '" class="img-thumbnail" width="120px;" height="120px;" alt="Foto de usuario">';
    $tableRows .= '</div>';
    $tableRows .= '<div class="col-md-9">';
    $tableRows .= '<h5>Información Detallada</h5>';
    $tableRows .= '<p><strong>ID:</strong> ' . $usuario['uss_id'] . '</p>';
    $tableRows .= '<p><strong>Usuario:</strong> ' . $usuario['uss_usuario'] . '</p>';
    $tableRows .= '<p><strong>Nombre Completo:</strong> ' . UsuariosPadre::nombreCompletoDelUsuario($usuario) . '</p>';
    $tableRows .= '<p><strong>Email:</strong> ' . $usuario['uss_email'] . '</p>';
    $tableRows .= '<p><strong>Fecha de Nacimiento:</strong> ' . $usuario['uss_fecha_nacimiento'] . '</p>';
    $tableRows .= '<p><strong>Tipo:</strong> ' . $usuario['pes_nombre'] . '</p>';
    $tableRows .= '<p><strong>Estado:</strong> ' . $estadoUsuario . '</p>';
    $tableRows .= '<p><strong>Último Ingreso:</strong> ' . $usuario['uss_ultimo_ingreso'] . '</p>';
    $tableRows .= '<p><strong>Bloqueado:</strong> ' . ($usuario['uss_bloqueado'] ? 'Sí' : 'No') . '</p>';
    if ($usuario['uss_tipo'] == TIPO_DOCENTE && isset($numCarga)) {
        $tableRows .= '<p><strong>Cargas:</strong> ' . $numCarga . '</p>';
    }
    if ($usuario['uss_tipo'] == TIPO_ACUDIENTE && isset($usuario['cantidad_acudidos'])) {
        $tableRows .= '<p><strong>Acudidos:</strong> ' . $usuario['cantidad_acudidos'] . '</p>';
    }
    if ($usuario['uss_tipo'] == TIPO_ESTUDIANTE) {
        $tableRows .= '<p><strong>Matrícula:</strong> ' . ($tieneMatricula ? 'Activa' : 'Sin matrícula') . '</p>';
    }
    $tableRows .= '</div>';
    $tableRows .= '</div>';
    $tableRows .= '</div>';
    $tableRows .= '</div>';
    $tableRows .= '</td>';
    $tableRows .= '</tr>';

    $contReg++;
}

header('Content-Type: application/json');
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'tableRows' => $tableRows,
    'totalRecords' => count($lista)
]);
?>