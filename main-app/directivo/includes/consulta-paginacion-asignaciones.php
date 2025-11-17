<?php
    $nombrePagina="asignaciones.php";
    if(empty($_REQUEST["nume"])){$_REQUEST["nume"] = base64_encode(1);}
    // Asegurar que $idE sea un entero válido
    $idEInt = is_numeric($idE) ? (int)$idE : 0;
    $consulta = Asignaciones::consultarAsignacionesEvaluacion($conexion, $config, $idEInt, $filtro);
    $numRegistros=mysqli_num_rows($consulta);
    $registros= $config['conf_num_registros'];
    $pagina=base64_decode($_REQUEST["nume"]);
    if (is_numeric($pagina)){
        $inicio= (($pagina-1)*$registros);
    }			     
    else{
        $inicio=1;
    }