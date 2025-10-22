<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0048';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

// Validaciones básicas (solo campos críticos)
if ($_POST["tipoUsuario"] != TIPO_ESTUDIANTE) {
    $mensaje = '';
    
    // Solo validar campos realmente críticos si están presentes
    // Los demás campos son opcionales
    
    if (!empty($mensaje)) {
        echo "Faltan los siguientes datos por diligenciar: <br>" . $mensaje . "<br>
        <a href='javascript:history.go(-1);'>[Regresar al formulario]</a>";
        exit();
    }
}

$notificaciones = 0;
if (!empty($_POST["notificaciones"]) && $_POST["notificaciones"] == 1) $notificaciones = 1;
$mostrarEdad = 0;
if (!empty($_POST["mostrarEdad"]) && $_POST["mostrarEdad"] == 1) $mostrarEdad = 1;

if (empty($_POST["tipoNegocio"])) $_POST["tipoNegocio"] = '0';

//Si es estudiante
if ($_POST["tipoUsuario"] == TIPO_ESTUDIANTE) {
    $update = [
        "uss_nombre" => strtoupper($_POST["nombre"]),
        "uss_nombre2" => strtoupper($_POST["nombre2"]),
        "uss_apellido1" => strtoupper($_POST["apellido1"]),
        "uss_apellido2" => strtoupper($_POST["apellido2"]),
        "uss_email" => strtolower($_POST["email"]),
        "uss_celular" => $_POST["celular"],
        "uss_lugar_nacimiento" => $_POST["lNacimiento"],
        "uss_telefono" => $_POST["telefono"],
        "uss_notificacion" => $notificaciones,
        "uss_mostrar_edad" => $mostrarEdad
    ];
    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);

    //Actualizar matricula a los estudiantes
    $update = [
        "mat_genero" => $_POST["genero"],
        "mat_fecha_nacimiento" => $_POST["fechaN"],
        "mat_celular" => $_POST["celular"],
        "mat_lugar_nacimiento" => $_POST["lNacimiento"],
        "mat_telefono" => $_POST["telefono"]
    ];
    Estudiantes::actualizarMatriculasPorIdUsuario($config, $_SESSION["id"], $update);
} else {
    $documento = $_POST["documento"] ?? null;

    $update = [
        "uss_nombre"            => strtoupper($_POST["nombre"]),
        "uss_nombre2"           => strtoupper($_POST["nombre2"]),
        "uss_apellido1"         => strtoupper($_POST["apellido1"]),
        "uss_apellido2"         => strtoupper($_POST["apellido2"]),
        "uss_email"             => strtolower($_POST["email"]),
        "uss_genero"            => !empty($_POST["genero"]) ? $_POST["genero"] : null,
        "uss_fecha_nacimiento"  => !empty($_POST["fechaN"]) ? $_POST["fechaN"] : null,
        "uss_celular"           => $_POST["celular"],
        "uss_numero_hijos"      => !empty($_POST["numeroHijos"]) ? $_POST["numeroHijos"] : 0,
        "uss_lugar_nacimiento"  => !empty($_POST["lNacimiento"]) ? $_POST["lNacimiento"] : null,
        "uss_nivel_academico"   => !empty($_POST["nAcademico"]) ? $_POST["nAcademico"] : null,
        "uss_telefono"          => $_POST["telefono"],
        "uss_notificacion"      => $notificaciones,
        "uss_mostrar_edad"      => $mostrarEdad,
        "uss_profesion"         => !empty($_POST["profesion"]) ? $_POST["profesion"] : null,
        "uss_estado_laboral"    => !empty($_POST["eLaboral"]) ? $_POST["eLaboral"] : null,
        "uss_religion"          => !empty($_POST["religion"]) ? $_POST["religion"] : null,
        "uss_estado_civil"      => !empty($_POST["eCivil"]) ? $_POST["eCivil"] : null,
        "uss_direccion"         => !empty($_POST["direccion"]) ? mysqli_real_escape_string($conexion, $_POST["direccion"]) : null,
        "uss_estrato"           => !empty($_POST["estrato"]) ? $_POST["estrato"] : null,
        "uss_tipo_vivienda"     => !empty($_POST["tipoVivienda"]) ? $_POST["tipoVivienda"] : null,
        "uss_medio_transporte"  => !empty($_POST["medioTransporte"]) ? $_POST["medioTransporte"] : null,
        "uss_tipo_negocio"      => !empty($_POST["tipoNegocio"]) ? $_POST["tipoNegocio"] : null,
        "uss_sitio_web_negocio" => !empty($_POST["web"]) ? mysqli_real_escape_string($conexion, $_POST["web"]) : null,
        "uss_documento"         => $documento,
    ];

    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);
}

if (!empty($_FILES['firmaDigital']['name']) && ($datosUsuarioActual['uss_tipo'] != TIPO_ESTUDIANTE || $config['conf_id_institucion'] != ICOLVEN)) { //TODO: Esto debe ser una configuración
    $archivoSubido->validarArchivo($_FILES['firmaDigital']['size'], $_FILES['firmaDigital']['name']);
    $explode=explode(".", $_FILES['firmaDigital']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_firma_') . "." . $extension;
    $destino = "../files/fotos";
    move_uploaded_file($_FILES['firmaDigital']['tmp_name'], $destino . "/" . $archivo);

    $update = ['uss_firma' => $archivo];
    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);
}

// Manejo de foto de perfil recortada (desde el sistema nuevo)
if (!empty($_POST['fotoRecortada']) && ($datosUsuarioActual['uss_tipo'] != TIPO_ESTUDIANTE || $config['conf_id_institucion'] != ICOLVEN)) {
    $imgBase64 = $_POST['fotoRecortada'];
    
    // Remover el prefijo data:image
    $img = str_replace('data:image/jpeg;base64,', '', $imgBase64);
    $img = str_replace(' ', '+', $img);
    $imgData = base64_decode($img);
    
    if ($imgData !== false) {
        $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . ".jpg";
        $destino = "../files/fotos";
        
        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }
        
        $rutaCompleta = $destino . "/" . $archivo;
        
        if (file_put_contents($rutaCompleta, $imgData)) {
            $update = ['uss_foto' => $archivo];
            UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);

            $updateEstudiantes = [
                "mat_foto" => $archivo
            ];
            
            Estudiantes::actualizarMatriculasPorIdUsuario($config, $_SESSION["id"], $updateEstudiantes);
        }
    }
}
// Manejo de foto de perfil sin recortar (sistema antiguo para compatibilidad)
elseif (!empty($_FILES['fotoPerfil']['name']) && ($datosUsuarioActual['uss_tipo'] != TIPO_ESTUDIANTE || $config['conf_id_institucion'] != ICOLVEN)) { //TODO: Esto debe ser una configuración
    $archivoSubido->validarArchivo($_FILES['fotoPerfil']['size'], $_FILES['fotoPerfil']['name']);
    $explode=explode(".", $_FILES['fotoPerfil']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
    $destino = "../files/fotos";
    move_uploaded_file($_FILES['fotoPerfil']['tmp_name'], $destino . "/" . $archivo);

    $update = ['uss_foto' => $archivo];
    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);

    $updateEstudiantes = [
        "mat_foto" => $archivo
    ];
    
    Estudiantes::actualizarMatriculasPorIdUsuario($config, $_SESSION["id"], $updateEstudiantes);

    $file = $destino . "/" . $archivo;  // Dirección de la imagen
    $imagen = getimagesize($file);    //Sacamos la información
    $ancho = $imagen[0];              //Ancho
    $alto = $imagen[1];               //Alto

    if ($ancho != $alto) {

        $_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

        $url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'perfil-recortar-foto.php');
        
        include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="' .$url. '?ancho=' . base64_encode($ancho) . '&alto=' . base64_encode($alto) . '&ext=' . base64_encode($extension) . '";</script>';
        exit();
    }
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'perfil.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();