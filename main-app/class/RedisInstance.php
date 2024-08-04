<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

class RedisInstance {
    const KEY_SYSTEM_CONFIGURATION = 'configuracionDelSistema';
    const KEY_MODULES_INSTITUTION  = 'modulosDeLaInstitucion';
    const KEY_MATRICULAS           = 'matriculas';

    private static $redis;

    /**
     * Establishes a connection to the Redis server.
     *
     * This method creates a new Redis instance, connects it to the specified Redis
     * server and port, and authenticates it with the provided password.
     *
     * @return void
     */
    private static function connect() {
        self::$redis = new Redis();
        self::$redis->connect(REDIS_SERVER, REDIS_PORT);
        self::$redis->auth(REDIS_PASSWORD);
    }

    /**
     * Retrieves the Redis instance.
     *
     * If the Redis instance is not already created, this method establishes a
     * connection by calling the connect method. It then returns the Redis instance
     * for use in the application.
     *
     * @return Redis The Redis instance.
     */
    public static function getRedisInstance() {
        if (!self::$redis) {
            self::connect();
        }

        return self::$redis;
    }

    /**
     * Retrieves the system configuration from Redis or the database.
     *
     * This function first attempts to retrieve the system configuration from Redis.
     * If the configuration is found in Redis and the $delKey parameter is set to true,
     * the configuration is deleted from Redis. If the configuration is not found in Redis,
     * or if the $delKey parameter is set to false, the function retrieves the configuration
     * from the database using the Plataforma::sesionConfiguracion() method and stores it in Redis.
     *
     * @param bool $delKey If set to true, deletes the system configuration from Redis if it exists.
     * @return array The system configuration.
     */
    public static function getSystemConfiguration($delKey = false) {

        $redis = self::getRedisInstance();

        if ($delKey && $redis->exists(self::KEY_SYSTEM_CONFIGURATION)) {
            $redis->del(self::KEY_SYSTEM_CONFIGURATION);
        }

        if ($redis->exists(self::KEY_SYSTEM_CONFIGURATION) && $redis->get(self::KEY_SYSTEM_CONFIGURATION) == 'true') {
            return json_decode($redis->get(self::KEY_SYSTEM_CONFIGURATION), true);
        }

        $config = Plataforma::sesionConfiguracion();
		$redis->set(self::KEY_SYSTEM_CONFIGURATION, json_encode($config));
        return $config;

    }

    public static function getModulesInstitution($delKey = false) {

        $redis = self::getRedisInstance();

        if ($delKey && $redis->exists(self::KEY_MODULES_INSTITUTION)) {
            $redis->del(self::KEY_MODULES_INSTITUTION);
        }

        if ($redis->exists(self::KEY_MODULES_INSTITUTION) && $redis->get(self::KEY_MODULES_INSTITUTION) == 'true') {
            return json_decode($redis->get(self::KEY_MODULES_INSTITUTION), true);
        }

        $config   = self::getSystemConfiguration();
        $conexion = Conexion::newConnection('MYSQL');

        $arregloModulos = Modulos::consultarModulosIntitucion($conexion, $config['conf_id_institucion']);
		$redis->set(self::KEY_MODULES_INSTITUTION, json_encode($arregloModulos));

        return $arregloModulos;

    }

    public static function getMatriculas($delKey = false) {

        $redis  = self::getRedisInstance();
        $config = self::getSystemConfiguration();

        $keysMatriculas = $redis->keys(self::KEY_MATRICULAS."_".$config['conf_id_institucion'].":*");

        if ($delKey && !empty($keysMatriculas)) {
            $redis->del($keysMatriculas);
            //$redis->flushDB();
            $keysMatriculas = $redis->keys(self::KEY_MATRICULAS."_".$config['conf_id_institucion'].":*");
        }
        
        if (empty($keysMatriculas)) {
            $consulta = Estudiantes::listarEstudiantes();

            while($matriculas = $consulta->fetch_assoc()) {
                $redis->set(self::KEY_MATRICULAS."_".$config['conf_id_institucion'].":" . $matriculas['mat_id'], json_encode($matriculas));
            }

            $consulta->free();

            $keysMatriculas = $redis->keys(self::KEY_MATRICULAS."_".$config['conf_id_institucion'].":*");
        }

        $resultadoRedis = [];
        foreach ($keysMatriculas as $key) {
            $data             = $redis->get($key);
            $resultadoRedis[] = json_decode($data, true);
        }

        return $resultadoRedis;

    }

}