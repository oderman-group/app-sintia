<?php

class Archivos {

	

	function validarArchivo($archivoSize, $archivoName, $archivoTmpName = null){

		include(ROOT_PATH."/config-general/config.php");

		$maxPeso = $config['conf_max_peso_archivos'];

		$explode=explode(".", $archivoName);
		$extension = strtolower(end($explode));

		// Lista ampliada de extensiones prohibidas (seguridad mejorada)
		$extensionesProhibidas = [
			'exe', 'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
			'js', 'html', 'htm', 'bat', 'cmd', 'com', 'sh',
			'vbs', 'jar', 'scr', 'msi', 'app', 'deb', 'rpm',
			'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py', 'rb',
			'sql', 'db', 'dbf', 'mdb'
		];

		if(in_array($extension, $extensionesProhibidas)){
			echo "Este archivo con extensión <b>.".$extension."</b> no está permitido por razones de seguridad.";
			exit();
		}

		// Validación adicional de MIME type si se proporciona archivo temporal
		if($archivoTmpName !== null && file_exists($archivoTmpName)){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimeType = finfo_file($finfo, $archivoTmpName);
			finfo_close($finfo);
			
			// MIME types peligrosos
			$mimesPeligrosos = [
				'application/x-msdownload', 
				'application/x-msdos-program',
				'application/x-executable',
				'application/x-httpd-php',
				'text/html',
				'text/javascript',
				'application/javascript',
				'application/x-sh',
				'application/x-sql'
			];
			
			if(in_array($mimeType, $mimesPeligrosos)){
				echo "El tipo de archivo no está permitido por razones de seguridad.";
				exit();
			}
		}

		$pesoMB = round($archivoSize/1048576,2);
		$urlReferencia = parse_url($_SERVER['HTTP_REFERER'] ?? '');
    
		$URLREGRESO = ($_SERVER['HTTP_REFERER'] ?? 'javascript:history.back()')."?error=ER_DT_17&pesoMB={$pesoMB}";
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

		

		if($foto!="" and file_exists(ROOT_PATH.'/main-app/files/fotos/'.$foto)){

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
		echo '<script type="text/javascript">window.location.href="../controlador/salir.php?invalid_user=true";</script>'; exit();
		break;
	}
	return $destinos;
}