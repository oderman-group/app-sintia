<?php
    include("session.php");
    $idPaginaInterna = 'DT0224';
    
    if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
        echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
        exit();
    }
    include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
    require_once("../class/Estudiantes.php");
    require_once(ROOT_PATH."/main-app/class/Grados.php");
    
    $year=$_SESSION["bd"];
    if(isset($_POST["year"])){
    $year=$_POST["year"];
    }
    
    $curso="";
    if(isset($_POST["curso"])){$curso=base64_encode($_POST["curso"]);}
    $grupo="";
    if(isset($_POST["grupo"])){$grupo=base64_encode($_POST["grupo"]);}
    $periodo="";
    if(isset($_POST["periodo"])){$periodo=base64_encode($_POST["periodo"]);}
    $id="";
    if(isset($_POST["estudiante"])){$id=base64_encode($_POST["estudiante"]);}

    $formatoB = "";
    if (empty($_POST["formatoB"])) {
        $consulta="";
        if(isset($_POST["curso"]) AND $_POST["curso"]!=""){
            $consulta = Grados::obtenerDatosGrados($_POST["curso"], $year);
        }

        if(isset($_POST["estudiante"]) AND $_POST["estudiante"]!=""){
        $consulta =Estudiantes::obtenerDatosEstudiantesParaBoletin($_POST["estudiante"],$year);
        }

        $boletin = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        $numDatos=mysqli_num_rows($consulta);
        if($numDatos>0){
            $formatoB = $boletin['gra_formato_boletin'];
        }
    } else {
        $formatoB = $_POST["formatoB"];
    }

    $ruta="informes-todos.php?error=ER_DT_9";
    if(!empty($formatoB)){
        $ruta="../compartido/matricula-boletin-curso-".$formatoB.".php?id=".$id."&periodo=".$periodo."&curso=".$curso."&grupo=".$grupo."&year=".base64_encode($year);
    }
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="'.$ruta.'";</script>';
	exit();