<?php

class Archivos {

	

	function validarArchivo($archivoSize, $archivoName){

		include("../../config-general/config.php");

		$maxPeso = $config['conf_max_peso_archivos'];

		$explode=explode(".", $archivoName);
		$extension = end($explode);

		if($extension == 'exe' or $extension == 'php' or $extension == 'js' or $extension == 'html' or $extension == 'htm'){

			echo "Este archivo con extensión <b>.".$extension."</b> no está permitido.";

			exit();

		}

		$pesoMB = round($archivoSize/1048576,2);
		$urlReferencia = parse_url($_SERVER['HTTP_REFERER']);
    
		$URLREGRESO = $_SERVER['HTTP_REFERER']."?error=ER_DT_17&pesoMB={$pesoMB}";
		if (isset($urlReferencia['query']) && !empty($urlReferencia['query'])) {
			
			$URLREGRESO = $_SERVER['HTTP_REFERER']."&error=ER_DT_17&pesoMB={$pesoMB}";
		}
		

		if($pesoMB>$maxPeso){

			echo '<script type="text/javascript">window.location.href="'.$URLREGRESO.'";</script>';
			exit();

		}





	}

	

	function subirArchivo($destino, $archivo, $nombreInputFile){

		$moved = move_uploaded_file($_FILES[$nombreInputFile]['tmp_name'], $destino ."/".$archivo);	

		if($_FILES[$nombreInputFile]['error']>0){echo "Hubo un error al subir el archivo. Error: ".$_FILES[$nombreInputFile]['error']."<br>";}

		if( !$moved ) { echo "Este archivo no pudo ser subido: ".$archivo."<br>"; exit();}

	}
	  /**
     * Metodo para  subir un archivo a firebase
     *
     * @param string $destino nombre de la carpeta de destino
	 * @param string $archivo Nombre del archivo que se va a crear
     * @param string $nombreInputFile nombre del file que se esta cargando
	 * @param object $storage Objeto con validado con las credenciales de firebase
     */
	function subirArchivoStorage($destino, $archivo, $nombreInputFile,$storage){		    
		$localFilePath = $_FILES[$nombreInputFile]['tmp_name'];// Ruta del archivo local que deseas subir	
		$cloudFilePath =  $destino .$archivo;// Ruta en el almacenamiento en la nube de Firebase donde deseas almacenar el archivo
		$storage->getBucket()->upload(fopen($localFilePath, 'r'), ['name' => $cloudFilePath	]);
	}

	

}



class Usuarios{

	

	public static function verificarFoto($foto){

		

		$fotoUsr = '../files/fotos/default.png';

		

		if($foto!="" and file_exists('../files/fotos/'.$foto)){

			$fotoUsr = '../files/fotos/'.$foto;

		}

		

		return $fotoUsr;

		

	}

	

	function verificarTipoUsuario($tipoUsuario, $paginaRedireccion){

		

		switch($tipoUsuario){	

			case 1: $url = '../directivo/'.$paginaRedireccion; break;

			case 2: $url = '../docente/'.$paginaRedireccion; break;

			case 3: $url = '../acudiente/'.$paginaRedireccion; break;

			case 4: $url = '../estudiante/'.$paginaRedireccion; break;

			case 5: $url = '../directivo/'.$paginaRedireccion; break;

			default: $url = '../controlador/salir.php'; break;

	  	}

		

		return $url;

		

	}

	

}



class Cargas {

	

	function verificarNumCargas($num, $idioma=1){
		include("../../config-general/idiomas.php");

		if($num>0){

?>

	<div class="alert alert-warning">

		<i class="icon-exclamation-sign"></i><strong><?=$frases[119][$idioma];?>:</strong> <?=$frases[328][$idioma];?>

	</div>

<?php



		}else{

?>

			<div class="alert alert-danger">

				<i class="icon-exclamation-sign"></i><strong><?=$frases[119][$idioma];?>:</strong> <?=$frases[329][$idioma];?>

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
    	return false;
    }else{
    	return true;
    }
}


function validarUsuarioActual($datosUsuarioActual) {
	switch ($datosUsuarioActual['uss_tipo']) {
		case 5:
			$destinos = "../directivo/";
			break;
		case 3:
			$destinos = "../acudiente/";
			break;
		case 4:
			$destinos =  "../estudiante/";
			break;
		case 2:
			$destinos = "../docente/";
			break;
		case 1:
			$destinos = "../directivo/";
			break;	

		default:
			echo '<script type="text/javascript">window.location.href="../controlador/salir.php";</script>'; exit();
			break;
	}
	return $destinos;
}