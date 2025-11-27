<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");

// Obtener parámetros
$cursoActual = $_POST['curso'] ?? '';
$filtro = $_POST['filtro'] ?? '';
$pagina = $_POST['pagina'] ?? '1';

// Configurar paginación
$registros = 20; // Número de registros por página
$inicio = ($pagina - 1) * $registros;

// Construir filtro
$filtroLimite = 'LIMIT '.$inicio.','.$registros;

// Consultar estudiantes
$selectSql = ["mat.*",
              "uss.uss_id","uss.uss_usuario","uss.uss_bloqueado",
              "gra_nombre","gru_nombre","gra_formato_boletin",
              "acud.uss_nombre","acud.uss_nombre2","acud.uss_nombre2", "mat.id_nuevo AS mat_id_nuevo"];

$consulta = Estudiantes::listarEstudiantes(0, $filtro, $filtroLimite, $cursoActual, null, $selectSql);

$contReg = 1;
$index = 0;
$arraysDatos = array();

if (!empty($consulta)) {
    while ($fila = $consulta->fetch_assoc()) {
        $arraysDatos[$index] = $fila;
        $index++;
    }
    $consulta->free();
}

$lista = $arraysDatos;
$data["data"] = $lista;

// Generar solo el HTML de la tabla sin JavaScript
$contReg = 1;
foreach ($lista as $resultado) {
    // Aquí va el código HTML de cada fila de la tabla
    // (copiado del archivo matriculas-tbody.php pero sin el JavaScript)
    
    $color = "";
    if ($resultado['mat_estado_matricula'] == 1) {
        $color = 'style="background-color: #ffebee;"';
    } elseif ($resultado['mat_estado_matricula'] == 2) {
        $color = 'style="background-color: #fff3e0;"';
    } elseif ($resultado['mat_estado_matricula'] == 3) {
        $color = 'style="background-color: #e8f5e8;"';
    }
    
    $marcaMediaTecnica = "";
    if ($resultado['mat_tipo'] == 2) {
        $marcaMediaTecnica = '<span class="badge badge-warning">MT</span> ';
    }
    
    $nombre = Estudiantes::NombreCompletoDelEstudiante($resultado);
    
    echo '<tr>';
    echo '<td>' . $contReg . '</td>';
    echo '<td>' . $resultado['mat_documento'] . '</td>';
    echo '<td ' . $color . '>';
    echo '<div class="student-name-container" style="cursor: pointer;" ';
    echo 'onclick="abrirModalEdicionRapida(\'' . htmlspecialchars($resultado['mat_id'], ENT_QUOTES) . '\')" ';
    echo 'title="Hacer clic para editar datos del estudiante">';
    echo $marcaMediaTecnica . '<span class="editable-name">' . $nombre . '</span>';
    echo '<i class="fa fa-edit text-muted ml-1" style="font-size: 0.8em;"></i>';
    echo '</div>';
    echo '</td>';
    echo '<td>' . strtoupper($resultado['gra_nombre'] . " " . $resultado['gru_nombre']) . '</td>';
    echo '<td>' . $resultado['uss_usuario'] . '</td>';
    echo '<td>';
    echo '<div class="btn-group">';
    echo '<button type="button" class="btn btn-primary">Acciones</button>';
    echo '<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">';
    echo '<i class="fa fa-angle-down"></i>';
    echo '</button>';
    echo '<ul class="dropdown-menu" role="menu" id="Acciones_' . $resultado['mat_id'] . '" style="z-index: 10000;">';
    echo '<li><a href="#" onclick="cambiarGrupo(' . $resultado['mat_id'] . ')">Cambiar de grupo</a></li>';
    echo '<li><a href="#" onclick="retirar(' . $resultado['mat_id'] . ')">Retirar Estudiante</a></li>';
    echo '</ul>';
    echo '</div>';
    echo '</td>';
    echo '</tr>';
    
    $contReg++;
}
?>
