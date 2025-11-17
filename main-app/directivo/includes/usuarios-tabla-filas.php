<?php
/**
 * Componente reutilizable para generar las filas de la tabla de usuarios
 * Usado tanto por usuarios.php como por ajax-filtrar-usuarios.php
 * 
 * Variables requeridas:
 * - $usuario: Array con los datos del usuario
 * - $contReg: Contador de registros
 * - $config: Configuración general
 * - $usuariosClase: Instancia de la clase Usuarios
 * - $opcionEstado: Array con opciones de estado
 * - $permisoPlantilla: Booleano para validar permisos
 * - $permisoHistorial: Booleano para validar permisos
 * - $datosUsuarioActual: Array con datos del usuario actual
 */

// Calcular variables necesarias
$bgColor = '';
$cheked = '';
if ($usuario['uss_bloqueado'] == SI) {
    $cheked = "checked";
    $bgColor = '#ff572238';
}

$managerPrimary = '';
$uss_permiso1 = $usuario['uss_permiso1'] ?? null;
if ($uss_permiso1 == CODE_PRIMARY_MANAGER && $usuario['uss_tipo'] == TIPO_DIRECTIVO) {
    $managerPrimary = '<i class="fa fa-user-circle text-primary" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director principal"></i> ';
}

$fotoUsuario = $usuariosClase->verificarFoto($usuario['uss_foto']);
$estadoUsuario = !empty($usuario['uss_estado']) ? $opcionEstado[$usuario['uss_estado']] : '';

// Calcular cargas y acudidos si existen
$numCarga = 0;
$mostrarNumCargas = '';
if ($usuario['uss_tipo'] == TIPO_DOCENTE) {
    try {
        $resultado = CargaAcademica::contarCargasDocente($config, $usuario['uss_id']);
        // El método devuelve un array, necesitamos el primer elemento
        $numCarga = is_array($resultado) ? (int)$resultado[0] : (int)$resultado;
        if ($numCarga > 0) {
            $mostrarNumCargas = '<a href="cargas.php?docente=' . base64_encode($usuario['uss_id']) . '"> (' . $numCarga . ')</a>';
        }
    } catch (Exception $e) {
        $numCarga = 0;
    }
}

$mostrarNumAcudidos = '';
if ($usuario['uss_tipo'] == TIPO_ACUDIENTE) {
    if (isset($usuario['cantidad_acudidos']) && $usuario['cantidad_acudidos'] > 0) {
        $mostrarNumAcudidos = '<a href="usuarios-acudidos.php?id=' . base64_encode($usuario['uss_id']) . '"> (' . $usuario['cantidad_acudidos'] . ')</a>';
    }
}

$tieneMatricula = isset($usuario['cantidad_matriculas']) && $usuario['cantidad_matriculas'] > 0;
$backGroundMatricula = $usuario['uss_tipo'] == TIPO_ESTUDIANTE && $tieneMatricula ? 'style="background:#0db4b94d;"' : '';

$disabledPermiso = '';
if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $usuario['uss_tipo'] == TIPO_DIRECTIVO) {
    $disabledPermiso = 'disabled';
}

$objetoEnviar = htmlspecialchars(json_encode([
    "mensaje" => "¿Desea eliminar el registro {$usuario['uss_id']}?<br>Tenga en cuenta que este usuario no está relacionado con ninguna matrícula u otra información relevante.",
    "categoria" => "eliminacion",
    "idConfirmacion" => $usuario['uss_id']
]), ENT_QUOTES, 'UTF-8');
?>
<tr id="reg<?= $usuario['uss_id']; ?>" style="background-color:<?= $bgColor; ?>;">
    <td>
        <button class="btn btn-sm btn-link text-secondary expand-btn"
            data-id="<?= $usuario['uss_id']; ?>"
            data-foto="<?= $fotoUsuario; ?>"
            data-nombre="<?= UsuariosPadre::nombreCompletoDelUsuario($usuario); ?>"
            data-usuario="<?= $usuario['uss_usuario']; ?>"
            data-email="<?= $usuario['uss_email'] ?: 'No registrado'; ?>"
            data-fecha-nacimiento="<?= $usuario['uss_fecha_nacimiento'] ?: 'No registrada'; ?>"
            data-tipo="<?= $usuario['pes_nombre']; ?>"
            data-estado="<?= $estadoUsuario ?: 'No definido'; ?>"
            data-ultimo-ingreso="<?= $usuario['uss_ultimo_ingreso'] ?: 'Nunca'; ?>"
            data-bloqueado="<?= $usuario['uss_bloqueado'] ? 'Sí' : 'No'; ?>"
            data-num-carga="<?= $numCarga; ?>"
            data-cantidad-acudidos="<?= isset($usuario['cantidad_acudidos']) ? $usuario['cantidad_acudidos'] : '0'; ?>"
            data-tiene-matricula="<?= $tieneMatricula ? 'Activa' : 'Sin matrícula registrada'; ?>"
            data-tipo-usuario="<?= $usuario['uss_tipo']; ?>"
            data-telefono="<?= $usuario['uss_telefono'] ?: 'No registrado'; ?>"
            data-direccion="<?= $usuario['uss_direccion'] ?: 'No registrada'; ?>"
            data-ocupacion="<?= $usuario['uss_ocupacion'] ?: 'No registrada'; ?>"
            data-genero="<?= $usuario['genero_nombre'] ?: 'No especificado'; ?>"
            data-fecha-registro="<?= $usuario['uss_fecha_registro'] ?: 'No registrada'; ?>"
            data-documento="<?= $usuario['uss_documento'] ?: 'No registrado'; ?>"
            data-tipo-documento="<?= $usuario['uss_tipo_documento'] ?: 'No especificado'; ?>"
            data-lugar-expedicion="<?= $usuario['uss_lugar_expedicion'] ?: 'No especificado'; ?>"
            data-intentos-fallidos="<?= $usuario['uss_intentos_fallidos'] ?: '0'; ?>"
            title="Ver detalles">
            <i class="fa fa-chevron-right"></i>
        </button>
    </td>
    <td><?= $contReg; ?></td>
    <td>
        <input type="checkbox" 
               class="usuario-checkbox" 
               value="<?= $usuario['uss_id']; ?>"
               id="<?= $usuario['uss_id']; ?>_select">
    </td>
    <td>
        <?php if (Modulos::validarPermisoEdicion() && ($usuario['uss_tipo'] != TIPO_DIRECTIVO || $uss_permiso1 != CODE_PRIMARY_MANAGER)) { ?>
            <div class="input-group spinner col-sm-10">
                <label class="switchToggle">
                    <input type="checkbox"
                        id="<?= $usuario['uss_id']; ?>" name="bloqueado"
                        value="1" onChange="ajaxBloqueoDesbloqueo(this)"
                        <?= $cheked ??= ''; ?> <?= $disabledPermiso; ?>>
                    <span class="slider red round"></span>
                </label>
            </div>
        <?php } ?>
    </td>
    <td><?= $usuario['uss_id']; ?></td>
    <td><?= $usuario['uss_usuario']; ?></td>
    <td><?= $managerPrimary; ?><?= UsuariosPadre::nombreCompletoDelUsuario($usuario); ?></td>
    <td <?= $backGroundMatricula; ?>>
        <?= $usuario['pes_nombre'] . "" . $mostrarNumCargas . "" . $mostrarNumAcudidos; ?>
    </td>
    <td>
        <div class="btn-group">
            <button type="button" class="btn btn-primary">Acciones</button>
            <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                <i class="fa fa-angle-down"></i>
            </button>
            <ul class="dropdown-menu" role="menu">
            <?php if (Modulos::validarPermisoEdicion()) { ?>

                <?php
                if (($usuario['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula)) || $usuario['uss_tipo'] != TIPO_ESTUDIANTE) {
                    if (Modulos::validarSubRol(['DT0124']) && ($usuario['uss_tipo'] != TIPO_DIRECTIVO || $uss_permiso1 != CODE_PRIMARY_MANAGER)) {
                ?>
                        <li><a href="javascript:void(0);" class="btn-editar-usuario-modal" data-usuario-id="<?=$usuario['uss_id'];?>"><i class="fa fa-edit"></i> Edición rápida</a></li>
                        <li><a href="usuarios-editar.php?id=<?= base64_encode($usuario['uss_id']); ?>"><i class="fa fa-pencil"></i> Editar completa</a></li>
                <?php }
                }
                ?>

                <?php
                if (
                    ($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $usuario['uss_tipo'] != TIPO_DEV) ||
                    ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $usuario['uss_tipo'] != TIPO_DEV && $usuario['uss_tipo'] != TIPO_DIRECTIVO && !isset($_SESSION['admin']) && !isset($_SESSION['devAdmin']))
                ) {
                    if ($usuario['uss_tipo'] == TIPO_ESTUDIANTE && !empty($tieneMatricula) || $usuario['uss_tipo'] != TIPO_ESTUDIANTE) {
                ?>
                        <li><a href="auto-login.php?user=<?= base64_encode($usuario['uss_id']); ?>&tipe=<?= base64_encode($usuario['uss_tipo']); ?>">Autologin</a></li>
                <?php
                    }
                }
                ?>

                <?php if ($usuario['uss_tipo'] == TIPO_ACUDIENTE && Modulos::validarSubRol(['DT0137'])) { ?>
                    <li><a href="usuarios-acudidos.php?id=<?= base64_encode($usuario['uss_id']); ?>">Acudidos</a></li>
                <?php } ?>

                <?php if ((isset($numCarga) && $numCarga == 0 && $usuario['uss_tipo'] == TIPO_DOCENTE) || $usuario['uss_tipo'] == TIPO_ACUDIENTE || ($usuario['uss_tipo'] == TIPO_ESTUDIANTE && empty($tieneMatricula)) || $usuario['uss_tipo'] == TIPO_CLIENTE || $usuario['uss_tipo'] == TIPO_PROVEEDOR) { ?>
                    <li><a href="javascript:void(0);" title="<?= $objetoEnviar; ?>" name="usuarios-eliminar.php?id=<?= base64_encode($usuario['uss_id']); ?>" onClick="deseaEliminar(this)" id="<?= $usuario['uss_id']; ?>">Eliminar</a></li>
                <?php } ?>
            <?php } ?>

            <?php if ($usuario['uss_tipo'] == TIPO_DOCENTE && $numCarga > 0 && $permisoPlantilla) { ?>
                <li><a href="../compartido/planilla-docentes.php?docente=<?= base64_encode($usuario['uss_id']); ?>" target="_blank">Planillas de las cargas</a></li>
            <?php } ?>

            <?php
            // Verificar si EnviarEmail está disponible
            $enviarEmailDisponible = class_exists('EnviarEmail');
            if (!$enviarEmailDisponible && file_exists(ROOT_PATH . "/main-app/class/EnviarEmail.php")) {
                require_once(ROOT_PATH . "/main-app/class/EnviarEmail.php");
                $enviarEmailDisponible = class_exists('EnviarEmail');
            }
            ?>
            
            <?php if ($enviarEmailDisponible && !empty($usuario['uss_email']) && EnviarEmail::validarEmail($usuario['uss_email'])) { ?>
                <li class="divider"></li>
                <li><a href="javascript:void(0);" onclick="enviarGuiaIndividual('<?= htmlspecialchars($usuario['uss_id'], ENT_QUOTES, 'UTF-8'); ?>', <?= $usuario['uss_tipo']; ?>)"><i class="fa fa-book"></i> Enviar Guía por Email</a></li>
            <?php } ?>

            <?php if (($datosUsuarioActual['uss_tipo'] == TIPO_DEV && $usuario['uss_tipo'] != TIPO_DEV) ||
                    ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && $usuario['uss_tipo'] != TIPO_DEV && $usuario['uss_tipo'] != TIPO_DIRECTIVO) && $permisoHistorial) { ?>
                <li><a href="../compartido/informe-historial-ingreso.php?id=<?= base64_encode($usuario['uss_id']); ?>" target="_blank">Historial de Ingreso</a></li>
            <?php } ?>

            </ul>
        </div>
    </td>
</tr>

