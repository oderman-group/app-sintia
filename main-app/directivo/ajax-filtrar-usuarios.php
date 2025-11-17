<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
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
        'us.uss_fecha_nacimiento', 'us.uss_telefono', 'us.uss_celular', 'us.uss_direccion', 'us.uss_ocupacion',
        'us.uss_genero', 'us.uss_fecha_registro', 'us.uss_documento', 'us.uss_tipo_documento',
        'us.uss_lugar_expedicion', 'us.uss_intentos_fallidos', 'us.uss_permiso1',
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
    
    // Generar el HTML de las filas usando el componente reutilizable
    ob_start();
    
    foreach ($listaUsuarios as $usuario) {
        include(ROOT_PATH . "/main-app/directivo/includes/usuarios-tabla-filas.php");
        $contReg++;
    }
    
    $html = ob_get_clean();
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => count($listaUsuarios),
        'filtros' => [
            'tipos' => $tiposFiltro,
            'estados' => $estadosFiltro
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

