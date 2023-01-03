<?php

class Archivos {

	

	function validarArchivo($archivoSize, $archivoName){

		include("../../config-general/config.php");

		$maxPeso = $config['conf_max_peso_archivos'];

		$extension = end(explode(".", $archivoName));

		if($extension == 'exe' or $extension == 'php' or $extension == 'js' or $extension == 'html' or $extension == 'htm'){

			echo "Este archivo con extensión <b>.".$extension."</b> no está permitido.";

			exit();

		}



		$pesoMB = round($archivoSize/1048576,2);

		if($pesoMB>$maxPeso){

			echo "Este archivo pesa <b>".$pesoMB."MB</b>. Lo ideal es que pese menos de ".$maxPeso."MB. Intente comprimirlo o busque reducir su peso.";

			exit();

		}





	}

	

	function subirArchivo($destino, $archivo, $nombreInputFile){

		$moved = move_uploaded_file($_FILES[$nombreInputFile]['tmp_name'], $destino ."/".$archivo);	

		if($_FILES[$nombreInputFile]['error']>0){echo "Hubo un error al subir el archivo. Error: ".$_FILES[$nombreInputFile]['error']."<br>";}

		if( !$moved ) { echo "Este archivo no pudo ser subido: ".$archivo."<br>"; exit();}

	}

	

}



class Usuarios{

	

	function verificarFoto($foto){

		

		$fotoUsr = '../files/fotos//avatar/man1.png';

		

		if($foto!="" and file_exists('../files/fotos/'.$foto)){

			$fotoUsr = '../files/fotos/'.$foto;

		}

		

		return $fotoUsr;

		

	}

	

	function verificarTipoUsuario($tipoUsuario, $paginaRedireccion){

		

		switch($tipoUsuario){	

			case 2: $url = '../docente/'.$paginaRedireccion; break;

			case 3: $url = '../acudiente/'.$paginaRedireccion; break;

			case 4: $url = '../estudiante/'.$paginaRedireccion; break;

			case 5: $url = '../directivo/'.$paginaRedireccion; break;

			default: $url = '../controlador/salir.php'; break;

	  	}

		

		return $url;

		

	}

	

}



class BaseDatos {

	

	function eliminarPorId($tabla, $clave, $id, $urlRetorno){

		include('../modelo/conexion.php');

		include("../../config-general/config.php");

		

		mysqli_query($conexion, "DELETE FROM ".$tabla." WHERE ".$clave."='".$id."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		echo '<script type="text/javascript">window.location.href="'.$urlRetorno.'";</script>';

		exit();

	}



	

}



class Cargas {

	

	function verificarNumCargas($num){

		if($num>0){

?>

	<div class="alert alert-warning">

		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Haz click sobre la imagen o sobre el nombre del curso al que deseas entrar.

	</div>

<?php



		}else{

?>

			<div class="alert alert-danger">

				<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> No tienes cargas asignadas aún.

			</div>

<?php



		}

	}

	

}

//Funciones independientes

/*
* Validar clave
*/
function validarClave($clave) {
    $regex = "/^[a-zA-Z0-9\.\$\*]{8,20}$/";
    $validarClave = preg_match($regex, $clave);

    if($validarClave === 0){
        echo '
        <div style="font-family: Consolas; padding: 10px; background-color: black; color:white;">
        La clave no cumple con todos los requerimientos:<br>
        - Debe tener entre 8 y 20 caracteres.<br>
        - Solo se admiten caracteres de la a-z, A-Z, números(0-9) y los siguientes simbolos(. y $).
        </div>
        ';
        die();
    }else{
    	return true;
    }
}