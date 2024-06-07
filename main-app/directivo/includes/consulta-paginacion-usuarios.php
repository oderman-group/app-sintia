<?php
    require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
    $nombrePagina="usuarios.php";
    if(empty($_REQUEST["nume"])){$_REQUEST["nume"] = base64_encode(1);}
    
    $consulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios();
    $numRegistros=mysqli_num_rows($consulta);
    $registros= $config['conf_num_registros'];
    $pagina=base64_decode($_REQUEST["nume"]);
    if (is_numeric($pagina)){
        $inicio= (($pagina-1)*$registros);
    }			     
    else{
        $inicio=1;
    }