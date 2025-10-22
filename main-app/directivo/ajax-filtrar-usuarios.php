<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/compartido/sintia-funciones.php");

header('Content-Type: application/json');

try {
    // Recibir filtros
    $tiposFiltro = isset($_POST['tipos']) ? $_POST['tipos'] : [];
    $estadosFiltro = isset($_POST['estados']) ? $_POST['estados'] : [];
    
    // Preparar array de tipos para el método (debe ser array, no string)
    $tiposArray = !empty($tiposFiltro) && is_array($tiposFiltro) ? $tiposFiltro : [];
    
    // Campos a seleccionar (los mismos que usa la página principal)
    $selectSql = [
        'us.uss_id', 'us.uss_usuario', 'us.uss_nombre', 'us.uss_nombre2', 
        'us.uss_apellido1', 'us.uss_apellido2', 'us.uss_email', 'us.uss_foto',
        'us.uss_tipo', 'us.uss_estado', 'us.uss_bloqueado', 'us.uss_ultimo_ingreso',
        'us.uss_fecha_nacimiento', 'us.uss_telefono', 'us.uss_direccion', 'us.uss_ocupacion',
        'us.uss_genero', 'us.uss_fecha_registro', 'us.uss_documento', 'us.uss_tipo_documento',
        'us.uss_lugar_expedicion', 'us.uss_intentos_fallidos',
        'pes.pes_nombre', 
        'ogen_genero.ogen_nombre as genero_nombre'
    ];
    
    // Consultar usuarios con filtros
    // El segundo parámetro debe ser un string con filtros adicionales, pero el método internamente espera que $tipos sea array
    // Vamos a modificar el approach: usar el método directamente con SQL personalizado
    
    // Construir el filtro WHERE completo
    $filtroWhere = " AND us.institucion = {$config['conf_id_institucion']} AND us.year = {$_SESSION['bd']}";
    
    // Filtro de tipos (múltiple)
    if (!empty($tiposArray)) {
        $tiposStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($tiposArray), $conexion), $tiposArray));
        $filtroWhere .= " AND us.uss_tipo IN ('{$tiposStr}')";
    }
    
    // Filtro de estados (múltiple)
    if (!empty($estadosFiltro) && is_array($estadosFiltro)) {
        $estadosStr = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($estadosFiltro), $conexion), $estadosFiltro));
        $filtroWhere .= " AND us.uss_estado IN ('{$estadosStr}')";
    }
    
    // Construir la consulta SQL directamente
    $stringSelect = implode(", ", $selectSql);
    $sql = "SELECT $stringSelect
            FROM ".BD_GENERAL.".usuarios us
            LEFT JOIN ".BD_ADMIN.".general_perfiles pes ON pes.pes_id=us.uss_tipo
            LEFT JOIN ".BD_ADMIN.".opciones_generales ogen_genero ON ogen_genero.ogen_id = us.uss_genero AND ogen_genero.ogen_grupo = 4
            WHERE 1=1 $filtroWhere
            ORDER BY us.uss_nombre ASC";
    
    $usuarios = mysqli_query($conexion, $sql);
    
    $listaUsuarios = [];
    if (!empty($usuarios)) {
        while ($fila = mysqli_fetch_array($usuarios, MYSQLI_BOTH)) {
            $listaUsuarios[] = $fila;
        }
    }
    
    // Variables necesarias para generar el HTML
    $usuariosClase = new UsuariosFunciones;
    $permisoHistorial = Modulos::validarSubRol(['DT0327']);
    $permisoPlantilla = Modulos::validarSubRol(['DT0239']);
    $permisoBloquearUsuarios = Modulos::validarSubRol(['DT0173']);
    $opcionEstado = ['Inactivo', 'Activo'];
    $contReg = 1;
    
    // Generar el HTML de las filas
    ob_start();
    
    foreach ($listaUsuarios as $usuario) {
        $bgColor = '';
        if ($usuario['uss_bloqueado'] == 1) {
            $bgColor = '#ff572238';
        }
        
        $fotoUsuario = $usuariosClase->verificarFoto($usuario['uss_foto']);
        $estadoUsuario = !empty($usuario['uss_estado']) ? $opcionEstado[$usuario['uss_estado']] : '';
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
                    data-estado="<?= $estadoUsuario; ?>"
                    data-ultimo-ingreso="<?= $usuario['uss_ultimo_ingreso'] ?: 'Nunca'; ?>"
                    data-bloqueado="<?= $usuario['uss_bloqueado'] == 1 ? 'Sí' : 'No'; ?>"
                    data-num-carga="<?= $usuario['num_cargas'] ?? 0; ?>"
                    data-cantidad-acudidos="<?= $usuario['cantidad_acudidos'] ?? 0; ?>"
                    data-tiene-matricula="<?= !empty($usuario['cantidad_matriculas']) ? 'Sí' : 'No'; ?>"
                    data-tipo-usuario="<?= $usuario['uss_tipo']; ?>"
                    data-telefono="<?= $usuario['uss_telefono'] ?: 'No registrado'; ?>"
                    data-direccion="<?= $usuario['uss_direccion'] ?: 'No registrada'; ?>"
                    data-ocupacion="<?= $usuario['uss_ocupacion'] ?: 'No especificada'; ?>"
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
                <div class="input-group spinner col-sm-10">
                    <label class="switchToggle">
                        <input type="checkbox" id="<?= $usuario['uss_id']; ?>" name="estado" value="1" <?php if ($usuario['uss_bloqueado'] == 1) { echo "checked";  } ?>>
                        <span class="slider green round"></span>
                    </label>
                </div>
            </td>
            <td><?= $usuario['pes_nombre']; ?></td>
            <td><?= UsuariosPadre::nombreCompletoDelUsuario($usuario); ?></td>
            <td><?= $usuario['uss_usuario']; ?></td>
            <td><?= $usuario['uss_email']; ?></td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        Acciones <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="usuarios-editar.php?id=<?= base64_encode($usuario['uss_id']); ?>"><i class="fa fa-edit"></i> Editar</a></li>
                        <li><a href="javascript:void(0);" class="btn-editar-usuario-modal" data-usuario-id="<?= $usuario['uss_id']; ?>"><i class="fa fa-bolt"></i> Edición Rápida</a></li>
                        <?php if (Modulos::validarPermisoEdicion()) { ?>
                            <?php if ($permisoHistorial) { ?>
                                <li><a href="historial-acciones.php?usuario=<?= base64_encode($usuario['uss_id']); ?>" target="_blank"><i class="fa fa-history"></i> Historial</a></li>
                            <?php } ?>
                            <?php if ($permisoPlantilla) { ?>
                                <li><a href="../compartido/impresion-credencial.php?id=<?= base64_encode($usuario['uss_id']); ?>" target="_blank"><i class="fa fa-id-card"></i> Imprimir Carnet</a></li>
                            <?php } ?>
                            <li class="divider"></li>
                            <li><a href="usuarios-eliminar.php?id=<?= base64_encode($usuario['uss_id']); ?>" onClick="return confirm('Desea eliminar el registro');"><i class="fa fa-trash"></i> Eliminar</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </td>
        </tr>
        <?php
        $contReg++;
    }
    
    $html = ob_get_clean();
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => count($listaUsuarios),
        'filtros' => [
            'tipos' => $tipos,
            'estados' => $estados
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al filtrar: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Error $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error fatal: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

