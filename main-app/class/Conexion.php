<?php
require_once(ROOT_PATH."/main-app/class/Conexion_Factory.php");

class Conexion extends Conexion_Factory{

    private static $conexionInstance = null;
    private $conexionMysql = null;
    private $conexionPDO = null;

    private function __construct()
    {

    }

    public static function getConexion() {
        //if (self::$conexionInstance == null) {
            $conexionInstance = new Conexion;
        //}

        return $conexionInstance;
    }

    /**
     * Esta función establece la conexión PDO a la base de datos
     */
    protected function conexionPDO()
    {

        try {
            //if($this->conexionPDO === null) {
                // Crear una instancia de PDO
                $this->conexionPDO = new PDO("mysql:host=".SERVIDOR_CONEXION.";dbname=".BD_ADMIN.";charset=utf8mb4", USUARIO_CONEXION, CLAVE_CONEXION);

                // Establecer el modo de error PDO a excepciones
                $this->conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Validar el conjunto de caracteres
                $charset = $this->conexionPDO->query("SELECT @@character_set_connection AS charset")->fetch(PDO::FETCH_ASSOC);
                if (strcasecmp($charset['charset'], 'utf8mb4') !== 0) {
                    throw new Exception("Error: El conjunto de caracteres no es utf8mb4, es " . $charset['charset']);
                }
            //}

            return $this->conexionPDO;

        } catch (PDOException  $e) {
            die("Conexión PDO fallida: " . $e->getMessage());
        }
    }

    /**
     * Esta función establece la conexión MYSQLI a la base de datos
     */
    protected function conexion()
    {
        //if($this->conexionMysql === null) {
            $this->conexionMysql = mysqli_connect(SERVIDOR_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION, BD_ADMIN);

            if (mysqli_connect_errno()) {
                die("Conexión MySQLi fallida: " . mysqli_connect_error());
            }

            // Validar el conjunto de caracteres
            if (!mysqli_set_charset($this->conexionMysql, "utf8mb4")) {
                printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", mysqli_error($this->conexionMysql));
                exit();
            }
        //}

        return $this->conexionMysql;
    }

    public static function closeConnection() {
        if (self::$conexionInstance !== null) {
            if (self::$conexionInstance->conexionMysql !== null || self::$conexionInstance->conexionPDO !== null) {
                self::$conexionInstance->conexionMysql->close();
                self::$conexionInstance->conexionMysql = null;
                self::$conexionInstance->conexionPDO = null;
            }
        }
    }

}