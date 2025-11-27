<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Paginas extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema = BD_ADMIN;

    public static $tableName = 'paginas_publicidad';

    public static $primaryKey = 'pagp_id';
    
    public static $tableAs = 'pagp';

    use BDT_Join;

    /**
     * Esta función Lista las paginas 
     * @param string $filtro 
     * @param string $limit
     * */
    public static function listarPaginas($filtro = '', $limit = '')
    {
        global $conexion, $baseDatosServicios;
        $resultado = [];

        $sqlExecute = "SELECT * FROM " . $baseDatosServicios . ".paginas_publicidad
       LEFT JOIN " . $baseDatosServicios . ".modulos ON mod_id=pagp_modulo
       LEFT JOIN " . $baseDatosServicios . ".general_perfiles ON pes_id=pagp_tipo_usuario
       WHERE pagp_id=pagp_id $filtro
       ORDER BY pagp_id $limit";
        try {
            $resultado = mysqli_query($conexion, $sqlExecute);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            exit();
        }
        return $resultado;
    }
}
