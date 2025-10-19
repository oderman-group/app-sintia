<?php

class Archivos {

	

	function validarArchivo($archivoSize, $archivoName){

		include(ROOT_PATH."/config-general/config.php");

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
		if(!empty($localFilePath) && !empty($cloudFilePath)) {
			$storage->getBucket()->upload(fopen($localFilePath, 'r'), ['name' => $cloudFilePath	]);
			
		} else {
			echo "_FILES: " . $_FILES . "<br>";
			echo "localFilePath: " . $localFilePath . "<br>"; 
			echo "cloudFilePath: " . $cloudFilePath . "<br>"; 
			exit();
		}
	}

	

}



class UsuariosFunciones{

	

	public static function verificarFoto($foto){

		

		$fotoUsr = BASE_URL.'/main-app/files/fotos/default.png';

		

		if($foto!="" and file_exists(BASE_URL.'/main-app/files/fotos/'.$foto)){

			$fotoUsr = BASE_URL.'/main-app/files/fotos/'.$foto;

		}

		

		return $fotoUsr;

		

	}

	

	function verificarTipoUsuario($tipoUsuario, $paginaRedireccion){

		

		switch($tipoUsuario){	

			case 1: $url = BASE_URL.'/main-app/directivo/'.$paginaRedireccion; break;

			case 2: $url = BASE_URL.'/main-app/docente/'.$paginaRedireccion; break;

			case 3: $url = BASE_URL.'/main-app/acudiente/'.$paginaRedireccion; break;

			case 4: $url = BASE_URL.'/main-app/estudiante/'.$paginaRedireccion; break;

			case 5: $url = BASE_URL.'/main-app/directivo/'.$paginaRedireccion; break;

			default: $url = BASE_URL.'/main-app/controlador/salir.php'; break;

	  	}

		

		return $url;

		

	}

	

}



class Cargas {

	

	function verificarNumCargas($num, $idioma=1){
		include(ROOT_PATH."/config-general/idiomas.php");

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
			$destinos = BASE_URL."/main-app/directivo/";
			break;
		case 3:
			$destinos = BASE_URL."/main-app/acudiente/";
			break;
		case 4:
			$destinos =  BASE_URL."/main-app/estudiante/";
			break;
		case 2:
			$destinos = BASE_URL."/main-app/docente/";
			break;
		case 1:
			$destinos = BASE_URL."/main-app/directivo/";
			break;	

		default:
			echo '<script type="text/javascript">window.location.href='.BASE_URL.'/controlador/salir.php";</script>'; exit();
			break;
	}
	return $destinos;
}