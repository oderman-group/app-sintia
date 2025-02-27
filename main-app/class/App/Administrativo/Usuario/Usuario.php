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

}