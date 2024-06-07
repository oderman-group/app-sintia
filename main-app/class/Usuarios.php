<?php
class Usuarios {
    /**
     * Obtiene los datos de un usuario por ID de usuario o nombre de usuario.
     *
     * @param int|string $usuario ID de usuario o nombre de usuario a consultar.
     *
     * @return array|string Devuelve un conjunto de resultados de la consulta de usuarios o una cadena vacía si no se encuentra ningún usuario.
     */
    public static function obtenerDatosUsuario($usuario = 0)
    {
        global $config;
        $resultado = [];

        $sql = "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE (uss_id=? || uss_usuario=?) AND institucion=? AND year=?";
        $parametros = [$usuario, $usuario, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Valida la existencia de un usuario por ID de usuario, nombre de usuario o correo electrónico.
     *
     * @param int|string $usuario ID de usuario, nombre de usuario o correo electrónico a validar.
     *
     * @return int Número de filas que coinciden con la consulta.
     */
    public static function validarExistenciaUsuario($usuario = 0)
    {
        global $config;
        $num = 0;

        $sql = "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE (uss_id=? || uss_usuario=? || uss_email=?) AND institucion=? AND year=?";
        $parametros = [$usuario, $usuario, $usuario, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($consulta);

        return $num;
    }

    /**
     * Obtiene los datos de un usuario para recuperar la clave por correo electrónico o nombre de usuario.
     *
     * @param int $idInstitucion ID de la institución.
     * @param string $usuario Correo electrónico o nombre de usuario para recuperar la clave.
     *
     * @return array Devuelve un conjunto de resultados de la consulta de usuarios para recuperar la clave.
     */
    public static function datosUsuarioParaRecuperarClave($idInstitucion, $usuario = '')
    {
        global $conexion;
        $resultado = [];

        $sql = "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE (uss_email=? || uss_usuario=?) AND institucion=? AND year=?";
        $parametros = [$usuario, $usuario, $idInstitucion, $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Guarda un registro de restauración de clave en la base de datos.
     *
     * @param array $data Datos del usuario y la nueva clave.
     *
     * @return int ID del último registro de restauración de clave guardado.
     */
    public static function guardarRegistroRestauracion($data)
    {
        global $conexion, $baseDatosServicios;

        try {
            mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".restaurar_clave(resc_id_usuario, resc_fec_solicitud, resc_id_institucion, resc_clave_generada) VALUES('" . $data['usuario_id'] . "', now(), '" . $data['institucion_id'] . "', '" . sha1($data['nueva_clave']) . "')");
            $idatosUsuarioltimoRegistro = mysqli_insert_id($conexion);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        $sql = "UPDATE " . BD_GENERAL . ".usuarios SET uss_clave=?, uss_intentos_fallidos=0 WHERE uss_id=? AND institucion=? AND year=?";
        $parametros = [SHA1($data['nueva_clave']), $data['usuario_id'], $data['institucion_id'], $data['institucion_agno']];
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $idatosUsuarioltimoRegistro;
    }

    /**
     * Genera una contraseña aleatoria de la longitud especificada.
     *
     * @param int $length Longitud de la contraseña.
     *
     * @return string Contraseña generada.
     */
    public static function generatePassword($length)
    {
        $key = "";
        $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
        $max = strlen($pattern) - 1;
        for ($i = 0; $i < $length; $i++) {
            $key .= substr($pattern, mt_rand(0, $max), 1);
        }
        return $key;
    }

    /**
     * Valida una contraseña según el patrón especificado.
     *
     * @param string $clave Contraseña a validar.
     *
     * @return bool Devuelve true si la contraseña es válida, de lo contrario, false.
     */
    public static function validarClave($clave)
    {
        $regex = "/^[a-zA-Z0-9\.\$\*]{8,20}$/";
        $validarClave = preg_match($regex, $clave);

        if ($validarClave === 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Este metodo se usa para bloquear o desbloquear usuarios por tipo
     * @param mysqli    $conexion
     * @param int       $tipoUsuarios
     * @param bool      $bloquearDesbloquear
     **/
    public static function bloquearDesbloquearUsuarios($conexion,$tipoUsuarios,$bloquearDesbloquear)
    {
        global $config;

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET uss_bloqueado=? WHERE uss_tipo=? AND institucion=? AND year=?";
        $parametros = [$bloquearDesbloquear, $tipoUsuarios, $config['conf_id_institucion'], $_SESSION["bd"]];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

}