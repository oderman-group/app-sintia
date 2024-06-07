<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0048';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
$usuariosClase = new Usuarios;
$archivoSubido = new Archivos;

if ($_POST["tipoUsuario"] != 4) {
    $mensaje = '';
    if (empty($_POST["profesion"])) {
        $mensaje .= '- La profesi&oacute;n<br>';
    }
    if (empty($_POST["eLaboral"])) {
        $mensaje .= '- Estado laboral<br>';
    }
    if (empty($_POST["religion"])) {
        $mensaje .= '- Religi&oacute;n<br>';
    }
    if (empty($_POST["lNacimiento"])) {
        $mensaje .= '- Lugar de nacimiento?<br>';
    }
    if (empty($_POST["eCivil"])) {
        $mensaje .= '- Estado civil?<br>';
    }
    if ($_POST["eLaboral"] == 165 and empty($_POST["tipoNegocio"])) {
        $mensaje .= '- Tipo de negocio?<br>';
    }
    if (empty($_POST["estrato"])) {
        $mensaje .= '- Estrato donde reside<br>';
    }
    if (empty($_POST["tipoVivienda"])) {
        $mensaje .= '- Tipo de vivienda donde reside<br>';
    }

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
if ($_POST["tipoUsuario"] == 4) {
    $update = "
        uss_nombre='" . strtoupper($_POST["nombre"]) . "', 
        uss_nombre2='" . strtoupper($_POST["nombre2"]) . "', 
        uss_apellido1='" . strtoupper($_POST["apellido1"]) . "', 
        uss_apellido2='" . strtoupper($_POST["apellido2"]) . "', 
        uss_email='" . strtolower($_POST["email"]) . "', 
        uss_celular='" . $_POST["celular"] . "', 
        uss_lugar_nacimiento='" . $_POST["lNacimiento"] . "', 
        uss_telefono='" . $_POST["telefono"] . "', 
        uss_notificacion='" . $notificaciones . "', 
        uss_mostrar_edad='" . $mostrarEdad . "'
    ";
    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);

    //Actualizar matricula a los estudiantes
    $update = "mat_genero=" . $_POST["genero"] . ", mat_fecha_nacimiento=" . $_POST["fechaN"] . ", mat_celular=" . $_POST["celular"] . ", mat_lugar_nacimiento=" . $_POST["lNacimiento"] . ", mat_telefono=" . $_POST["telefono"] . "";
    Estudiantes::actualizarMatriculasPorIdUsuario($config, $_SESSION["id"], $update);
} else {
    $update = "
        uss_nombre='" . strtoupper($_POST["nombre"]) . "', 
        uss_nombre2='" . strtoupper($_POST["nombre2"]) . "', 
        uss_apellido1='" . strtoupper($_POST["apellido1"]) . "', 
        uss_apellido2='" . strtoupper($_POST["apellido2"]) . "', 
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
        uss_sitio_web_negocio='" . mysqli_real_escape_string($conexion,$_POST["web"]) . "'
    ";
    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);
}

if (!empty($_FILES['firmaDigital']['name'])) {
    $archivoSubido->validarArchivo($_FILES['firmaDigital']['size'], $_FILES['firmaDigital']['name']);
    $explode=explode(".", $_FILES['firmaDigital']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_firma_') . "." . $extension;
    $destino = "../files/fotos";
    move_uploaded_file($_FILES['firmaDigital']['tmp_name'], $destino . "/" . $archivo);

    $update = "uss_firma='" . $archivo . "'";
    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);
}

if (!empty($_FILES['fotoPerfil']['name'])) {
    $archivoSubido->validarArchivo($_FILES['fotoPerfil']['size'], $_FILES['fotoPerfil']['name']);
    $explode=explode(".", $_FILES['fotoPerfil']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_img_') . "." . $extension;
    $destino = "../files/fotos";
    move_uploaded_file($_FILES['fotoPerfil']['tmp_name'], $destino . "/" . $archivo);

    $update = "uss_foto='" . $archivo . "'";
    UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);

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