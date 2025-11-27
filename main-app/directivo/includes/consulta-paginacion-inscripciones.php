<?php
    $nombrePagina="inscripciones.php";
    if(empty($_REQUEST["nume"])){$_REQUEST["nume"] = base64_encode(1);}
    
    // Primero contar el total de registros sin LIMIT
    $consultaTotal = Estudiantes::listarMatriculasAspirantes($config, $filtro);
    $numRegistros = mysqli_num_rows($consultaTotal);
    if ($consultaTotal) {
        mysqli_free_result($consultaTotal);
    }
    
    // Calcular paginación
    $registros = !empty($config['conf_num_registros']) ? (int)$config['conf_num_registros'] : 20;
    $pagina = base64_decode($_REQUEST["nume"]);
    if (is_numeric($pagina) && $pagina > 0){
        $inicio = (($pagina-1)*$registros);
    } else {
        $inicio = 0;
        $pagina = 1;
    }
    
    // Generar LIMIT para la consulta
    $filtroLimite = 'LIMIT '.$inicio.','.$registros;
    
    // Calcular total de páginas
    $totalPaginas = ceil($numRegistros / $registros);