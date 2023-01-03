<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../librerias/phpmailer/Exception.php';
require '../../librerias/phpmailer/PHPMailer.php';
require '../../librerias/phpmailer/SMTP.php';

include("../compartido/sintia-funciones.php");
$archivoSubido = new Archivos;
$usuariosClase = new Usuarios;


//include("../modelo/conexion.php");
//GUARDAR NOTICIA RÁPIDA
if ($_POST["id"] == 1) {

	$estado = 1;
	if ($datosUsuarioActual['uss_tipo'] == 4) {
		$estado = 0;
	}

	$destinatarios = "1,2,3,4,5";
	//if($_POST["doc"]==1)$destinatarios .="2,"; if($_POST["acu"]==1)$destinatarios .="3,"; if($_POST["est"]==1)$destinatarios .="4,";  
	mysqli_query($conexion, "INSERT INTO social_noticias(not_usuario, not_descripcion, not_fecha, not_estado, not_para)VALUES('" . $_SESSION["id"] . "','" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "',now(), '" . $estado . "', '" . $destinatarios . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//GUARDAR NOTICIA COMPLETA
if ($_POST["id"] == 2) {

	$estado = 1;
	if ($datosUsuarioActual['uss_tipo'] == 4) {
		$estado = 0;
	}

	$destinatarios = "1,2,3,4,5";
	//if($_POST["doc"]==1)$destinatarios .="2,"; if($_POST["acu"]==1)$destinatarios .="3,"; if($_POST["est"]==1)$destinatarios .="4,";  
	if ($_FILES['imagen']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['imagen']['size'], $_FILES['imagen']['name']);
		$extension = end(explode(".", $_FILES['imagen']['name']));
		$imagen = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
		$destino = "../files/publicaciones";
		move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $imagen);
	}
	if ($_FILES['archivo']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
		$extension = end(explode(".", $_FILES['archivo']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_fileNoti_') . "." . $extension;
		$destino = "../files/publicaciones";
		move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
	}

	$findme   = '?v=';
	$pos = strpos($_POST["video"], $findme) + 3;
	$video = substr($_POST["video"], $pos, 11);

	mysqli_query($conexion, "INSERT INTO social_noticias(not_titulo, not_descripcion, not_usuario, not_fecha, not_estado, not_para, not_imagen, not_archivo, not_keywords, not_url_imagen, not_video, not_id_categoria_general, not_video_url)
	VALUES('" . mysqli_real_escape_string($conexion,$_POST["titulo"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', '" . $_SESSION["id"] . "',now(), '" . $estado . "', '" . $destinatarios . "', '" . $imagen . "', '" . $archivo . "', '" . $_POST["keyw"] . "', '" . mysqli_real_escape_string($conexion,$_POST["urlImagen"]) . "', '" . $video . "', '" . $_POST["categoriaGeneral"] . "', '" . mysqli_real_escape_string($conexion,$_POST["video"]) . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$idRegistro = mysqli_insert_id($conexion);

	mysqli_query($conexion, "DELETE FROM social_noticias_cursos WHERE notpc_noticia='" . $idRegistro . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	if($_POST["cursos"]>0){
		$cont = count($_POST["cursos"]);
		$i = 0;
		while ($i < $cont) {
			mysqli_query($conexion, "INSERT INTO social_noticias_cursos(notpc_noticia, notpc_curso)VALUES('" . $idRegistro . "','" . $_POST["cursos"][$i] . "')");
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
			$i++;
		}
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//GUARDAR CARPETA
if ($_POST["id"] == 3) {
	$archivo = $_POST["nombre"];
	if ($_FILES['archivo']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
		$extension = end(explode(".", $_FILES['archivo']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_fileFolder_') . "." . $extension;
		$destino = "../files/archivos";
		move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
	}
	mysqli_query($conexion, "INSERT INTO general_folders(fold_nombre, fold_padre, fold_activo, fold_fecha_creacion, fold_propietario, fold_id_recurso_principal, fold_categoria, fold_tipo, fold_estado, fold_keywords)
	VALUES('" . $archivo . "', '" . $_POST["padre"] . "', 1, now(), '" . $_SESSION["id"] . "', '" . $_POST["idRecursoP"] . "', '" . $_POST["idCategoria"] . "', '" . $_POST["tipo"] . "', 1, '" . $_POST["keyw"] . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$idRegistro = mysqli_insert_id($conexion);

	mysqli_query($conexion, "DELETE FROM general_folders_usuarios_compartir WHERE fxuc_folder='" . $idRegistro . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$cont = count($_POST["compartirCon"]);
	$i = 0;
	while ($i < $cont) {
		mysqli_query($conexion, "INSERT INTO general_folders_usuarios_compartir(fxuc_folder, fxuc_usuario)VALUES('" . $idRegistro . "','" . $_POST["compartirCon"][$i] . "')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		$i++;
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//EDITAR NOTICIA
if ($_POST["id"] == 4) {
	$destinatarios = "1,2,3,4,5";
	//if($_POST["doc"]==1)$destinatarios .="2,"; if($_POST["acu"]==1)$destinatarios .="3,"; if($_POST["est"]==1)$destinatarios .="4,";  
	if ($_FILES['imagen']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['imagen']['size'], $_FILES['imagen']['name']);
		$extension = end(explode(".", $_FILES['imagen']['name']));
		$imagen = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_imgNoti_') . "." . $extension;
		$destino = "../files/publicaciones";
		move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $imagen);
		mysqli_query($conexion, "UPDATE social_noticias SET not_imagen='" . $imagen . "' WHERE not_id='" . $_POST["idR"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}
	if ($_FILES['archivo']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
		$extension = end(explode(".", $_FILES['archivo']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_fileNoti_') . "." . $extension;
		$destino = "../files/publicaciones";
		move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
		mysqli_query($conexion, "UPDATE social_noticias SET not_archivo='" . $archivo . "' WHERE not_id='" . $_POST["idR"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	$findme   = '?v=';
	$pos = strpos($_POST["video"], $findme) + 3;
	$video = substr($_POST["video"], $pos, 11);

	mysqli_query($conexion, "UPDATE social_noticias SET not_titulo='" . mysqli_real_escape_string($conexion,$_POST["titulo"]) . "', not_descripcion='" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "',  not_keywords='" . mysqli_real_escape_string($conexion,$_POST["keyw"]) . "', not_url_imagen='" . mysqli_real_escape_string($conexion,$_POST["urlImagen"]) . "', not_video='" . $video . "', not_id_categoria_general='" . $_POST["categoriaGeneral"] . "', not_video_url='" . $_POST["video"] . "' WHERE not_id='" . $_POST["idR"] . "'");

	mysqli_query($conexion, "DELETE FROM social_noticias_cursos WHERE notpc_noticia='" . $_POST["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$cont = count($_POST["cursos"]);
	$i = 0;
	while ($i < $cont) {
		mysqli_query($conexion, "INSERT INTO social_noticias_cursos(notpc_noticia, notpc_curso)VALUES('" . $_POST["idR"] . "','" . $_POST["cursos"][$i] . "')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		$i++;
	}

	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//EDITAR CARPETA
if ($_POST["id"] == 5) {
	$archivo = $_POST["nombre"];
	if ($_FILES['archivo']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
		$extension = end(explode(".", $_FILES['archivo']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_fileFolder_') . "." . $extension;
		$destino = "../files/archivos";
		move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
		mysqli_query($conexion, "UPDATE general_folders SET fold_nombre='" . $archivo . "' WHERE fold_id='" . $_POST["idR"] . "'");
	}

	mysqli_query($conexion, "UPDATE general_folders SET fold_nombre='" . $archivo . "', fold_padre='" . $_POST["padre"] . "', fold_tipo='" . $_POST["tipo"] . "', fold_keywords='" . $_POST["keyw"] . "', fold_fecha_modificacion=now() WHERE fold_id='" . $_POST["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "DELETE FROM general_folders_usuarios_compartir WHERE fxuc_folder='" . $_POST["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$cont = count($_POST["compartirCon"]);
	$i = 0;
	while ($i < $cont) {
		mysqli_query($conexion, "INSERT INTO general_folders_usuarios_compartir(fxuc_folder, fxuc_usuario)VALUES('" . $_POST["idR"] . "','" . $_POST["compartirCon"][$i] . "')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		$i++;
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//EDITAR PERFIL
if ($_POST["id"] == 6) {

	validarClave($_POST["clave"]);


	if ($_POST["tipoUsuario"] != 4) {
		$mensaje = '';
		if ($_POST["profesion"] == "") {
			$mensaje .= '- La profesi&oacute;n<br>';
		}
		if ($_POST["eLaboral"] == "") {
			$mensaje .= '- Estado laboral<br>';
		}
		if ($_POST["religion"] == "") {
			$mensaje .= '- Religi&oacute;n<br>';
		}
		if ($_POST["lNacimiento"] == "") {
			$mensaje .= '- Lugar de nacimiento?<br>';
		}
		if ($_POST["eCivil"] == "") {
			$mensaje .= '- Estado civil?<br>';
		}
		if ($_POST["eLaboral"] == 165 and $_POST["tipoNegocio"] == "") {
			$mensaje .= '- Tipo de negocio?<br>';
		}
		if ($_POST["estrato"] == "") {
			$mensaje .= '- Estrato donde reside<br>';
		}
		if ($_POST["tipoVivienda"] == "") {
			$mensaje .= '- Tipo de vivienda donde reside<br>';
		}
		if ($_POST["medioTransporte"] == "") {
			$mensaje .= '- Medio de transporte usual<br>';
		}

		if ($mensaje != "") {
			echo "Faltan los siguientes datos por diligenciar: <br>" . $mensaje . "<br>
			<a href='javascript:history.go(-1);'>[Regresar al formulario]</a>";
			exit();
		}
	}

	$notificaciones = 0;
	if ($_POST["notificaciones"] == 1) $notificaciones = 1;
	$mostrarEdad = 0;
	if ($_POST["mostrarEdad"] == 1) $mostrarEdad = 1;

	if ($_POST["tipoNegocio"] == "") $_POST["tipoNegocio"] = '0';

	//Si es estudiante
	if ($_POST["tipoUsuario"] == 4) {
		mysqli_query($conexion, "UPDATE usuarios SET 
	
		uss_clave='" . mysqli_real_escape_string($conexion,$_POST["clave"]) . "', 
		uss_nombre='" . strtoupper($_POST["nombre"]) . "', 
		uss_email='" . strtolower($_POST["email"]) . "', 
		uss_celular='" . $_POST["celular"] . "', 
		uss_lugar_nacimiento='" . $_POST["lNacimiento"] . "', 
		uss_telefono='" . $_POST["telefono"] . "', 
		uss_notificacion='" . $notificaciones . "', 
		uss_mostrar_edad='" . $mostrarEdad . "',

		uss_ultima_actualizacion=now()

		WHERE uss_id='" . $_SESSION["id"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");

		//Actualizar matricula a los estudiantes
		mysqli_query($conexion, "UPDATE academico_matriculas SET mat_genero='" . $_POST["genero"] . "', mat_fecha_nacimiento='" . $_POST["fechaN"] . "', mat_celular='" . $_POST["celular"] . "', mat_lugar_nacimiento='" . $_POST["lNacimiento"] . "', mat_telefono='" . $_POST["telefono"] . "'
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	} else {
		mysqli_query($conexion, "UPDATE usuarios SET 
		
		uss_clave='" . mysqli_real_escape_string($conexion,$_POST["clave"]) . "', 
		uss_nombre='" . strtoupper($_POST["nombre"]) . "', 
		uss_email='" . strtolower($_POST["email"]) . "', 
		uss_genero='" . $_POST["genero"] . "', 
		uss_fecha_nacimiento='" . $_POST["fechaN"] . "', 
		uss_celular='" . $_POST["celular"] . "', 
		uss_numero_hijos='" . $_POST["numeroHijos"] . "', 
		uss_lugar_nacimiento='" . $_POST["lNacimiento"] . "', 
		uss_nivel_academico='" . $_POST["nAcademico"] . "', 
		uss_telefono='" . $_POST["telefono"] . "', 
		uss_notificacion='" . $notificaciones . "', 
		uss_mostrar_edad='" . $mostrarEdad . "',
		uss_profesion='" . $_POST["profesion"] . "',
		uss_estado_laboral='" . $_POST["eLaboral"] . "',
		uss_religion='" . $_POST["religion"] . "',
		uss_estado_civil='" . $_POST["eCivil"] . "',
		uss_direccion='" . mysqli_real_escape_string($conexion,$_POST["direccion"]) . "',
		uss_estrato='" . $_POST["estrato"] . "',
		uss_tipo_vivienda='" . $_POST["tipoVivienda"] . "',
		uss_medio_transporte='" . $_POST["medioTransporte"] . "',
		uss_tipo_negocio='" . $_POST["tipoNegocio"] . "',
		uss_sitio_web_negocio='" . mysqli_real_escape_string($conexion,$_POST["web"]) . "',

		uss_ultima_actualizacion=now()

		WHERE uss_id='" . $_SESSION["id"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	if ($_FILES['fotoPerfil']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['fotoPerfil']['size'], $_FILES['fotoPerfil']['name']);
		$extension = end(explode(".", $_FILES['fotoPerfil']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
		$destino = "../files/fotos";
		move_uploaded_file($_FILES['fotoPerfil']['tmp_name'], $destino . "/" . $archivo);
		mysqli_query($conexion, "UPDATE usuarios SET uss_foto='" . $archivo . "' WHERE uss_id='" . $_SESSION["id"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");

		$file = $destino . "/" . $archivo;  // Dirección de la imagen
		$imagen = getimagesize($file);    //Sacamos la información
		$ancho = $imagen[0];              //Ancho
		$alto = $imagen[1];               //Alto

		if ($ancho != $alto) {
			switch ($_POST['tipoUsuario']) {
				case 2:
					$url = '../docente/perfil-recortar-foto.php';
					break;
				case 3:
					$url = '../acudiente/perfil-recortar-foto.php';
					break;
				case 4:
					$url = '../estudiante/perfil-recortar-foto.php';
					break;
				case 5:
					$url = '../directivo/perfil-recortar-foto.php';
					break;

				default:
					$url = '../controlador/salir.php';
					break;
			}

			echo '<script type="text/javascript">window.location.href="' . $url . '?ancho=' . $ancho . '&alto=' . $alto . '&ext=' . $extension . '";</script>';
			exit();
		}
	}

	if ($_FILES['firmaDigital']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['firmaDigital']['size'], $_FILES['firmaDigital']['name']);
		$extension = end(explode(".", $_FILES['firmaDigital']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_firma_') . "." . $extension;
		$destino = "../files/fotos";
		move_uploaded_file($_FILES['firmaDigital']['tmp_name'], $destino . "/" . $archivo);
		mysqli_query($conexion, "UPDATE usuarios SET uss_firma='" . $archivo . "' WHERE uss_id='" . $_SESSION["id"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ENVIAR MENSAJE
if ($_POST["id"] == 7) {
	$remitente = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='" . $_SESSION["id"] . "'"), MYSQLI_BOTH);

	$cont = count($_POST["para"]);
	$i = 0;
	while ($i < $cont) {

		$destinatario = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='" . $_POST["para"][$i] . "'"), MYSQLI_BOTH);

		mysqli_query($conexion, "INSERT INTO social_emails(ema_de, ema_para, ema_asunto, ema_contenido, ema_fecha, ema_visto, ema_eliminado_de, ema_eliminado_para)
		VALUES('" . $_SESSION["id"] . "', '" . $_POST["para"][$i] . "', '" . mysqli_real_escape_string($conexion,$_POST["asunto"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', now(), 0, 0, 0)");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		$i++;

		if ($_POST["para"][$i] == 1) {
			//INICIO ENVÍO DE MENSAJE
			$tituloMsj = $_POST["asunto"];
			$bgTitulo = "#4086f4";
			$contenidoMsj = '
				<p style="color:navy;">
				Hola ' . strtoupper($destinatario['uss_nombre']) . ', has recibido un mensaje a través de la plataforma SINTIA.<br>
				<b>Remitente:</b> ' . strtoupper($remitente['uss_nombre']) . '.
				</p>

				<p>' . $_POST["contenido"] . '</p>
			';

			include("../../config-general/plantilla-email-1.php");
			// Instantiation and passing `true` enables exceptions
			$mail = new PHPMailer(true);
			echo '<div style="display:none;">';
			try {
				include("../../config-general/mail.php");

				$mail->addBCC('tecmejia2010@gmail.com');     // Add a recipient con copia oculta

				// Attachments
				//$mail->addAttachment('files/archivos/'.$ficha, 'FICHA');    // Optional name

				// Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $_POST["asunto"];
				$mail->Body = $fin;
				$mail->CharSet = 'UTF-8';

				@$mail->send();
				echo 'Mensaje enviado correctamente.';
			} catch (Exception $e) {
				echo "Error: {$mail->ErrorInfo}";
				exit();
			}
			echo '</div>';
			//FIN ENVÍO DE MENSAJE
		}
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//COMENTARIO AL FORO
if ($_POST["id"] == 8) {
	mysqli_query($conexion, "INSERT INTO academico_actividad_foro_comentarios(com_id_foro, com_descripcion, com_id_estudiante, com_fecha)VALUES('" . mysqli_real_escape_string($conexion,$_POST["foro"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', '" . $_SESSION["id"] . "', now())");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//RESPUESTA AL COMENTARIO DEL FORO
if ($_POST["id"] == 9) {
	mysqli_query($conexion, "INSERT INTO academico_actividad_foro_respuestas(fore_id_estudiante, fore_id_comentario, fore_fecha, fore_respuesta)VALUES('" . $_SESSION["id"] . "', '" . $_POST["comentario"] . "', now(), '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '#PUB' . $_POST["comentario"] . '";</script>';
	exit();
}
//SUGERENCIA PARA MEJORAR SINTIA
if ($_POST["id"] == 10) {
	if (trim($_POST["contenido"]) != "") {
		mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".comentarios(adcom_institucion, adcom_usuario, adcom_fecha, adcom_comentario, adcom_tipo)
		VALUES('" . $config['conf_id_institucion'] . "', '" . $_SESSION["id"] . "', now(), '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', 2)");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");

		mysqli_query($conexion, "UPDATE usuarios SET uss_preguntar_animo=0 WHERE uss_id='" . $_SESSION["id"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");

		//INICIO ENVÍO DE MENSAJE
		$tituloMsj = 'Sugerencia SINTIA';
		$bgTitulo = "#4086f4";
		$contenidoMsj = '
			<p>
			<b>ID Institución:</b> ' . $config['conf_id_institucion'] . '<br>
			<b>Nombre Institución:</b> ' . $_SESSION["inst"] . '<br>
			<b>ID Usuario:</b> ' . $_SESSION["id"] . '<br>
			<b>Nombre Usuario:</b> ' . $_POST["usuario"] . '<br>
			<b>Tipo Usuario:</b> ' . $_POST["tipoUsuario"] . '<br>
			<b>Sugerencia:</b><br>
			' . $_POST["contenido"] . '
			</p>
		';

		include("../../config-general/plantilla-email-1.php");
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);
		echo '<div style="display:none;">';
		try {
			include("../../config-general/mail.php");

			$mail->addAddress('tecmejia2010@gmail.com');     // Add a recipient con copia oculta

			// Attachments
			//$mail->addAttachment('files/archivos/'.$ficha, 'FICHA');    // Optional name

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Sugerencia SINTIA';
			$mail->Body = $fin;
			$mail->CharSet = 'UTF-8';

			$mail->send();
			echo 'Mensaje enviado correctamente.';
		} catch (Exception $e) {
			echo "Error: {$mail->ErrorInfo}";
			exit();
		}
		echo '</div>';
		//FIN ENVÍO DE MENSAJE
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '?msg=1";</script>';
	exit();
}
//RESPUESTA ENCUESTA
if ($_POST["id"] == 11) {
	if (trim($_POST["respuesta"]) != "") {
		mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".comentarios(adcom_institucion, adcom_usuario, adcom_fecha, adcom_respuesta, adcom_tipo, adcom_id_encuesta)
		VALUES('" . $config['conf_id_institucion'] . "', '" . $_SESSION["id"] . "', now(), '" . $_POST["respuesta"] . "', 1, '" . $_POST["encuesta"] . "')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//GUARDAR REPORTE DISCIPLINARIO
if ($_POST["id"] == 12) {

	$acudiente = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_matriculas 
	INNER JOIN usuarios ON uss_id=mat_acudiente
	WHERE mat_id_usuario='" . $_POST["estudiante"] . "'"), MYSQLI_BOTH);



	$cont = count($_POST["faltas"]);
	$i = 0;
	while ($i < $cont) {
		mysqli_query($conexion, "INSERT INTO disciplina_reportes(dr_fecha, dr_estudiante, dr_falta, dr_usuario, dr_aprobacion_estudiante, dr_aprobacion_acudiente, dr_observaciones)VALUES('" . $_POST["fecha"] . "', '" . $_POST["estudiante"] . "', '" . $_POST["faltas"][$i] . "','" . $_POST["usuario"] . "', 0, 0,'" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
		$i++;

		mysqli_query($conexion, "INSERT INTO general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_vista)
		VALUES('Reporte disciplinario', 'Te han hecho un nuevo reporte disciplinario - COD: " . $_POST["faltas"][$i] . ".', 2, '" . $_POST["estudiante"] . "', now(), 3, 2, 0)");
		$idNotify = mysqli_insert_id($conexion);
		mysqli_query($conexion, "UPDATE general_alertas SET alr_url_acceso='reportes-disciplinarios.php?idNotify=" . $idNotify . "' WHERE alr_id='" . $idNotify . "'");

		mysqli_query($conexion, "INSERT INTO general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_vista)
		VALUES('Reporte disciplinario - " . $acudiente['mat_nombres'] . "', 'A tu acudido " . $acudiente['mat_nombres'] . " le han hecho un nuevo reporte disciplinario - COD: " . $_POST["faltas"][$i] . ".', 2, '" . $acudiente['uss_id'] . "', now(), 3, 2, 0)");
		$idNotify = mysqli_insert_id($conexion);
		mysqli_query($conexion, "UPDATE general_alertas SET alr_url_acceso='reportes-disciplinarios.php?idNotify=" . $idNotify . "&usrEstud=" . $_POST["estudiante"] . "' WHERE alr_id='" . $idNotify . "'");
	}

	if ($acudiente['mat_notificacion1'] == 1) {
		//INICIO ENVÍO DE MENSAJE
		$tituloMsj = "REPORTE DISCIPLINARIO - " . $acudiente['mat_nombres'];
		$bgTitulo = "#4086f4";
		$contenidoMsj = '
			<p>
				Hola!<br>
				<b>' . strtoupper($acudiente["uss_nombre"]) . '</b>, a tu acudido ' . $acudiente['mat_nombres'] . ' le han hecho un nuevo reporte disciplinario.<br>
				Te sugerimos ingresar a la plataforma SINTIA para revisar el reporte y realizar tu firma de forma digital.
			</p>
		';

		include("../../config-general/plantilla-email-1.php");
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);
		try {
			include("../../config-general/mail.php");

			$mail->addAddress($acudiente['uss_email'], $acudiente['uss_nombre']);     // Add a recipient con copia oculta

			// Attachments
			//$mail->addAttachment('files/archivos/'.$ficha, 'FICHA');    // Optional name

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'REPORTE DISCIPLINARIO - ' . $acudiente['mat_nombres'];
			$mail->Body = $fin;
			$mail->CharSet = 'UTF-8';

			$mail->send();
			echo 'Mensaje enviado correctamente.';
		} catch (Exception $e) {
			echo "Error: {$mail->ErrorInfo}";
			exit();
		}
		//FIN ENVÍO DE MENSAJE
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//GUARDAR EN CHAT GRUPAL
if ($_POST["id"] == 13) {
	mysqli_query($conexion, "INSERT INTO academico_chat_grupal(chatg_emisor, chatg_carga, chatg_fecha, chatg_mensaje)VALUES('" . $_SESSION["id"] . "', '" . $_POST["carga"] . "', now(), '" . mysqli_real_escape_string($conexion,$_POST["mensaje"]) . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//COMENTARIO O PREGUNTAS A LA CLASE
if ($_POST["id"] == 14) {
	mysqli_query($conexion, "INSERT INTO academico_clases_preguntas(cpp_usuario, cpp_fecha, cpp_id_clase, cpp_contenido)VALUES('" . $_SESSION["id"] . "', now(), '" . $_POST["idClase"] . "', '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	$datos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_clases 
	INNER JOIN academico_cargas ON car_id=cls_id_carga
	INNER JOIN usuarios ON uss_id=car_docente
	WHERE cls_id='" . $_POST["idClase"] . "' AND cls_estado=1"), MYSQLI_BOTH);

	if ($_SESSION["id"] != $datos["uss_id"]) {
		//INICIO ENVÍO DE MENSAJE
		$tituloMsj = 'NUEVO COMENTARIO/PREGUNTA';
		$bgTitulo = "#4086f4";
		$contenidoMsj = '
			<p>
				Hola <b>' . strtoupper($datos["uss_nombre"]) . '</b>, uno de los estudiantes ha realizado un nuevo comentario/pregunta sobre la clase <b>' . $datos["cls_tema"] . '</b>.<br>

				<b>COMENTARIO:</b><br>
				' . $_POST["contenido"] . '
			</p>

			<p>
				Ingresa a la plataforma SINTIA para responder a este comentario de ser necesario.
			</p>
		';

		include("../../config-general/plantilla-email-1.php");
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);

		echo '<div style="display:none;">';
		try {
			include("../../config-general/mail.php");

			$mail->addAddress($datos["uss_email"], $datos["uss_nombre"]);     // Add a recipient con copia oculta

			// Attachments
			//$mail->addAttachment('files/archivos/'.$ficha, 'FICHA');    // Optional name

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Nuevo comentario sobre la clase';
			$mail->Body = $fin;
			$mail->CharSet = 'UTF-8';

			$mail->send();
			echo 'Mensaje enviado correctamente.';
		} catch (Exception $e) {
			echo "Error: {$mail->ErrorInfo}";
			exit();
		}
		echo '</div>';
		//FIN ENVÍO DE MENSAJE
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//GUARDAR DATOS SOLICITADOS DEL DEMO
if ($_POST["id"] == 15) {

	mysqli_query($conexion, "UPDATE usuarios SET 
	uss_celular='" . $_POST["celular"] . "', 
	uss_institucion='" . $_POST["institucion"] . "', 
	uss_institucion_municipio='" . $_POST["instMunicipio"] . "',
	uss_solicitar_datos=0, 
		
	uss_ultima_actualizacion=now()

	WHERE uss_id='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
?>
	<script>
		localStorage.setItem("vGuiada", 1);
	</script>
<?php


	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}

//REGISTRAR EMPRESA EN MARKETPLACE DE SINTIA
if ($_POST["id"] == 16) {
	$clave = rand(10000, 99999);

	mysqli_query($conexion, "INSERT INTO " . $baseDatosMarketPlace . ".empresas(emp_nombre, emp_email, emp_telefono, emp_verificada, emp_estado, emp_clave, emp_usuario, emp_institucion)VALUES('" . mysqli_real_escape_string($conexion,$_POST["nombre"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["email"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["telefono"]) . "', 0, 0, '" . $clave . "', '" . $_SESSION["id"] . "', '" . $config['conf_id_institucion'] . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$idRegistro = mysqli_insert_id($conexion);


	$cont = count($_POST["sector"]);
	$i = 0;
	while ($i < $cont) {
		mysqli_query($conexion, "INSERT INTO " . $baseDatosMarketPlace . ".empresas_categorias(excat_empresa, excat_categoria)VALUES('" . $idRegistro . "', '" . $_POST["sector"][$i] . "')");
		
		$i++;
	}

	$_SESSION["empresa"] = $idRegistro;

	echo '<script type="text/javascript">window.location.href="../acudiente/productos-agregar.php?pp=1";</script>';
	exit();
}

//REGISTRAR PRODUCTOS EN MARKETPLACE DE SINTIA
if ($_POST["id"] == 17) {

	if ($_FILES['imagen']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['imagen']['size'], $_FILES['imagen']['name']);
		$extension = end(explode(".", $_FILES['imagen']['name']));
		$foto = uniqid($_SESSION["empresa"] . '_prod_') . "." . $extension;
		$destino = "../files/marketplace/productos";
		move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $foto);
	}

	$findme   = '?v=';
	$pos = strpos($_POST["video"], $findme) + 3;
	$video = substr($_POST["video"], $pos, 11);

	mysqli_query($conexion, "INSERT INTO " . $baseDatosMarketPlace . ".productos(prod_ref, prod_nombre, prod_descripcion, prod_foto, prod_precio, prod_activo, prod_estado, prod_empresa, prod_video, prod_keywords, prod_categoria)VALUES('" . mysqli_real_escape_string($conexion,$_POST["ref"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["nombre"]) . "', '" . mysqli_real_escape_string($conexion,$_POST["descripcion"]) . "', '" . $foto . "', '" . $_POST["precio"] . "', 0, 1, '" . $_SESSION["empresa"] . "', '" . $video . "', '" . mysqli_real_escape_string($conexion,$_POST["keyw"]) . "', '" . $_POST["categoria"] . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	$idRegistro = mysqli_insert_id($conexion);

	$paginaRed = 'marketplace.php';
	$urlRedireccion = $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'], $paginaRed);


	echo '<script type="text/javascript">window.location.href="' . $urlRedireccion . '";</script>';
	exit();
}
//ENVIAR MENSAJE A VENDEDOR DE MARKETPLACE
if ($_POST["id"] == 18) {
	$remitente = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='" . $_SESSION["id"] . "'"), MYSQLI_BOTH);
	$destinatario = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='" . $_POST["para"] . "'"), MYSQLI_BOTH);

	mysqli_query($conexion, "INSERT INTO social_emails(ema_de, ema_para, ema_asunto, ema_contenido, ema_fecha, ema_visto, ema_eliminado_de, ema_eliminado_para)
	VALUES('" . $_SESSION["id"] . "', '" . $_POST["destinoMarketplace"] . "', '" . $_POST["asuntoMarketplace"] . "', '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', now(), 0, 0, 0)");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");


	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ACTUALIZAR MATRÍCULA
if ($_POST["id"] == 19) {

	mysqli_query($conexion, "UPDATE usuarios SET 
	
		uss_celular='" . $_POST["celular"] . "',
		uss_telefono='" . $_POST["telefono"] . "',

		uss_ultima_actualizacion=now()

		WHERE uss_id='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	//Actualizar matricula a los estudiantes
	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_celular='" . $_POST["celular"] . "', mat_telefono='" . $_POST["telefono"] . "', mat_direccion='" . $_POST["dir"] . "', mat_barrio='" . $_POST["barrio"] . "', mat_estrato='" . $_POST["estrato"] . "', mat_actualizar_datos=1, mat_modalidad_estudio='" . $_POST["modalidad"] . "'
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	//Actualizar datos del acudiente
	mysqli_query($conexion, "UPDATE usuarios SET  uss_email='" . $_POST["emailA"] . "', uss_celular='" . $_POST["celularA"] . "', uss_ocupacion='" . $_POST["ocupacion"] . "', uss_direccion='" . $_POST["dir"] . "'
		WHERE uss_id='" . $_POST["idAcudiente"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");


	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}

//ACEPTAR CONTRATO
if ($_POST["id"] == 20) {

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_contrato=1
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ACEPTAR PAGARÉ
if ($_POST["id"] == 21) {

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_pagare=1
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//PAGO MATRICULA - ADJUNTAR COMPROBANTE
if ($_POST["id"] == 22) {

	if ($_FILES['archivo']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
		$extension = end(explode(".", $_FILES['archivo']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_pagom_') . "." . $extension;
		$destino = "../files/comprobantes";
		move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
	}

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_soporte_pago='".$archivo."'
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ACEPTAR COMPROMISO ACADÉMICO
if ($_POST["id"] == 23) {

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_compromiso_academico=1
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ACEPTAR CONTRATO PARA MAYORES DE 14 AÑOS
if ($_POST["id"] == 24) {

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_mayores14=1
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ADJUNTAR FIRMA MATRÍCULA
if ($_POST["id"] == 25) {

	if ($_FILES['archivo']['name'] != "") {
		$archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
		$extension = end(explode(".", $_FILES['archivo']['name']));
		$archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_firma_') . "." . $extension;
		$destino = "../files/comprobantes";
		move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
	}

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_firma_adjunta='".$archivo."', mat_hoja_firma=1
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ACEPTAR MANUAL DE CONVIVENCIA
if ($_POST["id"] == 26) {

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_manual=1
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ACEPTAR COMPROMISO DE CONVIVENCIA
if ($_POST["id"] == 27) {

	mysqli_query($conexion, "UPDATE academico_matriculas SET  mat_compromiso_convivencia=1
		WHERE mat_id_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//GUARDAR ASPECTOS ESTUDIANTILES
if ($_POST["id"] == 28) {
	
	mysqli_query($conexion, "INSERT INTO matriculas_aspectos(mata_estudiante, mata_usuario, mata_fecha_evento, mata_aspectos_positivos, mata_aspectos_mejorar, mata_tratamiento, mata_descripcion, mata_periodo)VALUES('" . $_POST["estudiante"] . "', '" . $_SESSION['id'] . "', '" . $_POST["fecha"] . "', '" . $_POST["positivos"] . "', '" . $_POST["mejorar"] . "', '" . $_POST["tratamiento"] . "', '" . $_POST["descripcion"] . "', '" . $_POST["periodo"] . "')");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();
}

//GUARDAR ASPECTOS ESTUDIANTILES  COMO LO HACE EL DOCENTE
if ($_POST["id"] == 29) {

	//CONSUTLAR CARGA PARA DIRECTOR DE GRUPO
	$carga = mysqli_fetch_array( mysqli_query($conexion, "SELECT * FROM academico_cargas
	WHERE car_curso='".$_POST["curso"]."' AND car_director_grupo=1"), MYSQLI_BOTH);
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	
	//PARA NOTAS DE COMPORTAMIENTO
	$numD = mysql_num_rows( mysqli_query($conexion, "SELECT * FROM disiplina_nota
	WHERE dn_cod_estudiante='".$_POST["estudiante"]."' AND dn_periodo='".$_POST["periodo"]."'"));
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	if($numD==0){
		mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["estudiante"]."' AND dn_periodo='".$_POST["periodo"]."'");
		
		mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_aspecto_academico, dn_aspecto_convivencial, dn_periodo, dn_id_carga)VALUES('".$_POST["estudiante"]."','".$_POST["academicos"]."','".$_POST["convivenciales"]."', '".$_POST["periodo"]."', '".$carga['car_id']."')");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}else{
		mysqli_query($conexion, "UPDATE disiplina_nota SET dn_aspecto_academico='".$_POST["academicos"]."', dn_aspecto_convivencial='".$_POST["convivenciales"]."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$_POST["estudiante"]."' AND dn_periodo='".$_POST["periodo"]."';");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();
}



##############################################
//CAMBIAR IDIOMA
if ($_GET["get"] == 1) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_idioma='" . $_GET["idioma"] . "' WHERE uss_id='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//CAMBIAR TEMA ENCABEZADO
if ($_GET["get"] == 2) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_tema_header='" . $_GET["temaHeader"] . "' WHERE uss_id='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//CAMBIAR TEMA MENÚ
if ($_GET["get"] == 3) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_tema_sidebar='" . $_GET["temaSidebar"] . "' WHERE uss_id='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//CAMBIAR TEMA LOGO
if ($_GET["get"] == 4) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_tema_logo='" . $_GET["temaLogo"] . "' WHERE uss_id='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//CAMBIAR TODO EL TEMA
if ($_GET["get"] == 5) {
	mysqli_query($conexion, "UPDATE usuarios SET uss_tema_header='" . $_GET["temaHeader"] . "', uss_tema_sidebar='" . $_GET["temaSidebar"] . "', uss_tema_logo='" . $_GET["temaLogo"] . "' WHERE uss_id='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR/MOSTRAR/OCULTAR UNA NOTICIA
if ($_GET["get"] == 6) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado='" . $_GET["e"] . "' WHERE not_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR/MOSTRAR/OCULTAR TODAS LAS NOTICIAS DE UN USUARIO
if ($_GET["get"] == 7) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_estado='" . $_GET["e"] . "' WHERE not_usuario='" . $_SESSION["id"] . "' AND not_estado!=2");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//REACCIONES POR NOTICIA
if ($_GET["get"] == 8) {
	$reaccion = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM social_noticias_reacciones WHERE npr_usuario='" . $_SESSION["id"] . "' AND npr_noticia='" . $_GET["idR"] . "'"), MYSQLI_BOTH);
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	if ($reaccion[0] == "") {
		mysqli_query($conexion, "INSERT INTO social_noticias_reacciones(npr_usuario, npr_noticia, npr_reaccion, npr_fecha, npr_estado)VALUES('" . $_SESSION["id"] . "', '" . $_GET["idR"] . "','" . $_GET["r"] . "',now(),1)");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	} else {
		mysqli_query($conexion, "UPDATE social_noticias_reacciones SET npr_reaccion='" . $_GET["r"] . "' WHERE npr_usuario='" . $_SESSION["id"] . "' AND npr_noticia='" . $_GET["idR"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	mysqli_query($conexion, "INSERT INTO general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_vista)
	VALUES('<b>" . $_GET["usrname"] . "</b> ha reaccionado a tu publicación', '<b>" . $_GET["usrname"] . "</b> ha reaccionado a tu publicación " . $_GET["postname"] . ".', 2, '" . $_GET["postowner"] . "', now(), 3, 2, 0)");
	$idNotify = mysqli_insert_id($conexion);
	mysqli_query($conexion, "UPDATE general_alertas SET alr_url_acceso='noticias.php?idNotify=" . $idNotify . "#PUB" . $_GET["idR"] . "' WHERE alr_id='" . $idNotify . "'");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '#PUB' . $_GET["idR"] . '";</script>';
	exit();
}
//ELIMINAR CARPETA
if ($_GET["get"] == 9) {
	mysqli_query($conexion, "UPDATE general_folders SET fold_estado='0', fold_fecha_eliminacion=now() WHERE fold_padre='" . $_GET["idR"] . "'");
	mysqli_query($conexion, "UPDATE general_folders SET fold_estado='0', fold_fecha_eliminacion=now() WHERE fold_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR NOTIFICACIONES
if ($_GET["get"] == 10) {
	mysqli_query($conexion, "DELETE FROM general_alertas WHERE alr_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR IMAGEN DE LA NOTICIA
if ($_GET["get"] == 11) {
	mysqli_query($conexion, "UPDATE social_noticias SET not_imagen='' WHERE not_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR COMETARIO DEL FORO
if ($_GET["get"] == 12) {
	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_respuestas WHERE fore_id_comentario='" . $_GET["idCom"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_comentarios WHERE com_id='" . $_GET["idCom"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR COMETARIO DEL FORO
if ($_GET["get"] == 13) {
	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_respuestas WHERE fore_id='" . $_GET["idResp"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '#PUB' . $_GET["idCom"] . '";</script>';
	exit();
}
//GUARDAR CLICK PUBLICITARIO
if ($_GET["get"] == 14) {
	if ($_GET["usrAct"] != "") {
		$usuarioActivo = $_GET["usrAct"];
	} elseif ($_SESSION["id"] != "") {
		$usuarioActivo = $_SESSION["id"];
	}

	if ($_GET["idIns"] != "") {
		$idInst = $_GET["idIns"];
	} else {
		$idInst = $config['conf_id_institucion'];
	}



	mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".publicidad_estadisticas(pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, pest_fecha, pest_ip, pest_accion)
	VALUES('" . $_GET["idPub"] . "', '" . $idInst . "', '" . $usuarioActivo . "', '" . $_GET["idPag"] . "', '" . $_GET["idUb"] . "', now(), '" . $_SERVER["REMOTE_ADDR"] . "', 2)");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	if ($_GET["url"] != "") $URL = $_GET["url"];
	else $URL = $_SERVER["HTTP_REFERER"];

	echo '<script type="text/javascript">window.location.href="' . $URL . '";</script>';
	exit();
}
//GUARDAR FRASES MÓDULO
if ($_GET["get"] == 15) {
	mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".publicidad_guardadas(psave_publicidad, psave_institucion, psave_usuario, psave_fecha)
	VALUES('" . $_GET["idPub"] . "', '" . $config['conf_id_institucion'] . "', '" . $_SESSION["id"] . "', now())");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.close();</script>';
	exit();
}
//ELIMINAR TODAS LAS NOTIFICACIONES
if ($_GET["get"] == 16) {
	mysqli_query($conexion, "DELETE FROM general_alertas WHERE alr_usuario='" . $_SESSION["id"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR MENSAJES
if ($_GET["get"] == 17) {
	if ($_GET["elm"] == 1) {
		mysqli_query($conexion, "UPDATE social_emails SET ema_eliminado_de=1 WHERE ema_id='" . $_GET["idR"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	} else {
		mysqli_query($conexion, "UPDATE social_emails SET ema_eliminado_para=1 WHERE ema_id='" . $_GET["idR"] . "'");
		$lineaError = __LINE__;
		include("../compartido/reporte-errores.php");
	}

	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR MENSAJES DEL CHAT GRUPAL
if ($_GET["get"] == 18) {
	mysqli_query($conexion, "DELETE FROM academico_chat_grupal WHERE chatg_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR REPORTE DISCIPLINARIO
if ($_GET["get"] == 19) {
	mysqli_query($conexion, "DELETE FROM disciplina_reportes WHERE dr_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//FIRMA DIGITAL POR EL ESTUDIANTE
if ($_GET["get"] == 20) {
	mysqli_query($conexion, "UPDATE disciplina_reportes SET dr_aprobacion_estudiante=1, dr_aprobacion_estudiante_fecha=now() WHERE dr_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//FIRMA DIGITAL POR EL ACUDIENTE
if ($_GET["get"] == 21) {
	mysqli_query($conexion, "UPDATE disciplina_reportes SET dr_aprobacion_acudiente=1, dr_aprobacion_acudiente_fecha=now() WHERE dr_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//DESHACER FIRMA DEL ESTUDIANTE
if ($_GET["get"] == 22) {
	mysqli_query($conexion, "UPDATE disciplina_reportes SET dr_aprobacion_estudiante=0, dr_aprobacion_estudiante_fecha='0000-00-00' WHERE dr_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//DESHACER FIRMA DEL ACUDIENTE
if ($_GET["get"] == 23) {
	mysqli_query($conexion, "UPDATE disciplina_reportes SET dr_aprobacion_acudiente=0, dr_aprobacion_acudiente_fecha='0000-00-00' WHERE dr_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();
}
//ELIMINAR COMENTARIOS DE LA CLASE
if ($_GET["get"] == 24) {
	mysqli_query($conexion, "DELETE FROM academico_clases_preguntas WHERE cpp_id='" . $_GET["idCom"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR PRODUCTOS DEL MARKETPLACE
if ($_GET["get"] == 25) {
	mysqli_query($conexion, "DELETE FROM " . $baseDatosMarketPlace . ".productos WHERE prod_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR REGISTRO DE OBSERVADOR
if ($_GET["get"] == 26) {
	mysqli_query($conexion, "DELETE FROM matriculas_aspectos WHERE mata_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}
//ELIMINAR REGISTRO DE OBSERVADOR
if ($_GET["get"] == 27) {
	mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
	exit();
}



//EN CASO DE QUE NO ENTRE POR NINGUNA DE LAS ANTERIORES
echo '<script type="text/javascript">window.location.href="https://plataformasintia.com?error=1";</script>';
exit();
?>