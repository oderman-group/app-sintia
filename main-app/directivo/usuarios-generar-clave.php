<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

$numUsuarios = (count($_POST["usuario"]));
$contUsuarios = 0;

while ($contUsuarios < $numUsuarios) {
    if($_POST["tipo"]==1){
        $validarClave = Usuarios::validarClave($_POST["clave"]);
        if(!$validarClave){
            echo '<script type="text/javascript">window.location.href="usuarios-generar-clave-filtros.php?error=5";</script>';
            exit();
        }
        UsuariosPadre::actualizarUsuariosClaveManualPorTipoUsuario($config, $_POST["clave"], $_POST["usuario"][$contUsuarios]);
    }elseif($_POST["tipo"]==2){
        UsuariosPadre::actualizarUsuariosClavePorTipoUsuario($config, $_POST["usuario"][$contUsuarios]);
    }
    $contUsuarios++;
}

echo '<script type="text/javascript">window.location.href="usuarios-generar-clave-filtros.php?success=SC_DT_8";</script>';
exit();