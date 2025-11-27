<?php
require_once 'Plataforma.php';
require_once 'Tables/BDT_general_perfiles.php';

class TipoUsuario {

    /**
     * Lista los tipos de usuarios disponibles.
     *
     * Recupera y devuelve la información de los tipos de usuarios disponibles en la base de datos.
     *
     * @param string $baseDatosServicios - Nombre de la base de datos de servicios.
     * @param PDO $conexionPDO - Objeto PDO para la conexión a la base de datos.
     *
     * @return PDOStatement|null - Devuelve un objeto PDOStatement con los resultados de la consulta o null si hay un error.
     *
     * @throws Exception - Lanza una excepción si hay un error al preparar la consulta.
     *
     * @example
     * ```php
     * // Ejemplo de uso para listar tipos de usuarios
     * $tiposUsuarios = listarTiposUsuarios($baseDatosServicios, $conexionPDO);
     * if ($tiposUsuarios !== null) {
     *     while ($tipoUsuario = $tiposUsuarios->fetch(PDO::FETCH_ASSOC)) {
     *         // Procesar cada tipo de usuario
     *         echo $tipoUsuario['nombre_tipo_usuario'];
     *     }
     * } else {
     *     // Manejar el caso en que haya un error en la consulta
     * }
     * ```
     */
    public static function listarTiposUsuarios($baseDatosServicios, $conexionPDO)
    {
        $tableName = BDT_GeneralPerfiles::getTableName();

        try {
            $consulta = "SELECT * FROM {$baseDatosServicios}.{$tableName} WHERE pes_disponible = 1";
            $stmt = $conexionPDO->prepare($consulta);

            if ($stmt) {
                $stmt->execute();

                return $stmt;
            } else {
                throw new Exception("Error al preparar la consulta.");
            }
        } catch (PDOException  $e) {
            echo "Excepción capturada: " . $e->getMessage();
            return null;
        }
    }
}