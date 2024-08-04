<?php
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");

class Autenticate {

    private static $instance = null;

    private function __construct() {
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Autenticate;
        }

        return self::$instance;
    }

    private function validateUser(string $user) {
        if (empty(trim($user))) {
            return false;
        }

        return true;
    }

    private function validatePass(string $pass, string $user) {
        if (empty(trim($pass))) {
            return false;
        }

        // if($user === $pass) {
        //     throw new Exception("El usuario y la clave no pueden ser iguales");
        // }

        return true;
    }

    public function getUserData(string $user, string $pass): array
    { 
        if (!$this->validateUser($user)) {
            throw new Exception("El usuario es invalido");
        }

        if (!$this->validatePass($pass, $user)) {
            throw new Exception("La contraseña es invalida");
        }

        $sql = "SELECT id_nuevo, uss_usuario, uss_id, institucion, uss_intentos_fallidos FROM ".BD_GENERAL.".usuarios 
        WHERE uss_usuario='".trim($user)."' 
        AND TRIM(uss_usuario)!='' 
        AND uss_clave=SHA1('".$pass."')  
        AND uss_usuario IS NOT NULL  
        ORDER BY uss_ultimo_ingreso DESC 
        LIMIT 1";

        $conexion = Conexion::newConnection('MYSQL');

        $consulta = mysqli_query($conexion, $sql);
        $data     = mysqli_fetch_array($consulta, MYSQLI_ASSOC);

        if (empty($data)) {
            throw new Exception("El usuario o la clave son incorrectos");
        }

        return $data;
    }

    public function cerrarSesion($urlRedirect = null) {

        if(empty($urlRedirect)){
            $urlRedirect = REDIRECT_ROUTE."?inst=".base64_encode($_SESSION["idInstitucion"])."&year=".base64_encode($_SESSION["bd"]);
        }

        setcookie("carga","",time()-3600);
        setcookie("periodo","",time()-3600);
        setcookie("cargaE","",time()-3600);
        setcookie("periodoE","",time()-3600);
        session_destroy();

        Conexion::getConexion()->closeConnection();

        $redis = RedisInstance::getRedisInstance();
        $keysToDelete = [RedisInstance::KEY_SYSTEM_CONFIGURATION, RedisInstance::KEY_MODULES_INSTITUTION];
        $redis->del($keysToDelete);

        header("Location:".$urlRedirect);
    }
}