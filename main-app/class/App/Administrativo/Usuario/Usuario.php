<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';
class Administrativo_Usuario_Usuario extends BDT_Tablas implements BDT_JoinImplements{
    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios';

    public static $primaryKey = 'uss_id';
    
    public static $tableAs = 'uss';

    use BDT_Join;
/**
 * Bloquea o desbloquea a un conjunto de usuarios en función de los parámetros proporcionados.
 *
 * Esta función permite bloquear o desbloquear a un grupo de usuarios específicos 
 * pertenecientes a la institución actual, según el estado indicado en el parámetro `$bloquear`.
 *
 * @param array $usuarios Arreglo de IDs de usuarios a bloquear o desbloquear. Ejemplo: [4, 5, 6].
 * @param bool $bloquear (Opcional) Indica si se deben bloquear (true) o desbloquear (false) los usuarios.
 *                       Por defecto, el valor es true (bloquear).
 * 
 * @return bool Retorna `true` si la actualización en la base de datos fue exitosa, `false` en caso contrario.
 *
 * @throws Exception Si la consulta a la base de datos falla o si los parámetros son inválidos.
 *
 */
    public static function bloquearUsuarios(array $usuarios, $bloquear = true){
        
        foreach ($usuarios as $user) {
            $users[] = parent::formatValor($user);
        };
        $in_usuarios = implode(', ', $users);

        $predicado =
        [
            self::OTHER_PREDICATE   => "uss_id IN ($in_usuarios)",
            "institucion"           => $_SESSION["idInstitucion"]
        ];

        $datos =
        [
            "uss_bloqueado"   => $bloquear?1:0,
        ];
        $sql = parent::Update($datos,$predicado);
        return $sql;
    }

    /**
     * Consulta el último ingreso de un usuario.
     *
     * Esta función recupera el registro más reciente de ingreso de un usuario desde la base de datos,
     * ordenando por el campo `uss_ultimo_ingreso` (última fecha de ingreso) en orden descendente, 
     * y limitando el resultado a solo un registro.
     *
     * @param string $campos (opcional) Los campos a recuperar. El valor por defecto es "*" (todos los campos).
     * @param string $usuario El nombre de usuario para el cual se consulta el último ingreso.
     *
     * @return array|null Retorna un array asociativo con el registro encontrado, o null si no se encuentra ningún registro.
     */
    public static function consultarUltimoIngresoPorUsuario( 
        string $usuario,
        string $campos = "*"
    ) {
        $conexion = Conexion::newConnection('MYSQL');

        $consulta = mysqli_query($conexion, "SELECT $campos FROM " . self::$schema . "." . self::$tableName . "
        WHERE uss_usuario='".trim($usuario)."' AND TRIM(uss_usuario)!='' AND uss_usuario IS NOT NULL 
        ORDER BY uss_ultimo_ingreso DESC 
        LIMIT 1");
        $datos = mysqli_fetch_array($consulta, MYSQLI_ASSOC);

        return $datos;

    }

}