<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class UsuariosPadre {

    /**
     * Obtiene el nombre completo de un usuario a partir de su arreglo de datos.
     *
     * @param array $usuario Arreglo de datos del usuario.
     *
     * @return string Retorna el nombre completo del usuario en mayúsculas o '--' si el usuario no es un arreglo.
     */
    public static function nombreCompletoDelUsuario($usuario)
    {
        if (!is_array($usuario)) {
            return '--';
        }
        $nombre = $usuario['uss_nombre'];
        if (!empty($usuario['uss_nombre2'])) {
            $nombre .= " " . $usuario['uss_nombre2'];
        }
        if (!empty($usuario['uss_apellido1'])) {
            $nombre .= " " . $usuario['uss_apellido1'];
        }
        if (!empty($usuario['uss_apellido2'])) {
            $nombre .= " " . $usuario['uss_apellido2'];
        }
        return strtoupper($nombre);
    }

    /**
     * Lista los usuarios para un año específico cuyo nombre de usuario coincida con el patrón proporcionado.
     *
     * @param string $usuario Patrón de nombre de usuario.
     *
     * @return array Arreglo de usuarios con datos extendidos para cada año.
     */
    public static function listarUsuariosAnio($usuario)
    {
        global $yearStart;
        global $yearEnd;
        $index = 0;
        $arraysDatos = [];

        while ($yearStart <= $yearEnd) {
            $sql = "SELECT * FROM " . BD_GENERAL . ".usuarios uss 
            INNER JOIN " . BD_ADMIN . ".general_perfiles ON pes_id=uss_tipo
            WHERE uss_usuario LIKE '" . $usuario . "%' AND uss.institucion=? AND uss.year=?";
            $parametros = [$_SESSION["idInstitucion"], $yearStart];
            $consultaUsuarioAuto = BindSQL::prepararSQL($sql, $parametros);

            if ($consultaUsuarioAuto->num_rows > 0) {
                while ($fila = $consultaUsuarioAuto->fetch_assoc()) {
                    $fila["anio"] = $yearStart;
                    $arraysDatos[$index] = $fila;
                    $index++;
                }
            }
            $yearStart++;
        }

        return $arraysDatos;
    }

    /**
     * Obtiene los datos de un usuario a partir de su ID de usuario.
     *
     * Esta función consulta la base de datos para recuperar los datos de un usuario utilizando su ID de usuario.
     *
     * @param string $idUsuario - El ID de usuario para el cual se desean obtener los datos.
     * @param string $filtroAdicional (Opcional) - Un filtro adicional que se puede aplicar a la consulta SQL.
     *
     * @return array - Un array que contiene los datos del usuario si se encuentra en la base de datos, o un array vacío si no se encuentra.
     */
    public static function sesionUsuario(
        string $idUsuario, 
        string $filtroAdicional =   '', 
        int    $instiBd         =   NULL, 
        string $yearBd          =   ''
    ){
        $year       = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $idInsti    = !empty($instiBd) ? $instiBd : $_SESSION["idInstitucion"];

        $sql = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id=? AND institucion=? AND year=? {$filtroAdicional}";

        $parametros = [$idUsuario, $idInsti, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        // Obtener la fila de resultados como un array asociativo
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Obtiene los datos de un usuario para un año específico y un nombre de usuario dado.
     *
     * @param string $usuario Nombre de usuario.
     * @param int $year Año para el cual se desea obtener los datos del usuario.
     *
     * @return array Arreglo de datos del usuario para el año y nombre de usuario especificados.
     */
    public static function sesionUsuarioAnio($usuario,$year)
    {
        $sql = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_usuario=? AND institucion=? AND year=? limit 1";
        $parametros = [$usuario, $_SESSION['idInstitucion'], $year];
        $consultaUsuarioAuto = BindSQL::prepararSQL($sql, $parametros);

        $datosUsuarioAuto = mysqli_fetch_array($consultaUsuarioAuto, MYSQLI_BOTH);
        return $datosUsuarioAuto;
    }

    /**
     * Actualiza las preferencias de tema de los usuarios para varios años.
     *
     * @return void
     */
    public static function actualizarUsuariosAnios()
    {
        $get=$_GET["get"];
        $campoGet=null;
        $campoTabla=null;
        global $yearStart;
        global $yearEnd;
        global $conexion;
        switch ($get) {
            case 1://CAMBIAR IDIOMA
                $campoGet="idioma";
                $campoTabla="uss_idioma";
                break;
            case 2://CAMBIAR TEMA ENCABEZADO
                $campoGet="temaHeader";
                $campoTabla="uss_tema_header";
                break;
            case 3://CAMBIAR TEMA MENÚ
                $campoGet="temaSidebar";
                $campoTabla="uss_tema_sidebar";
                break;
            case 4://CAMBIAR TEMA LOGO
                $campoGet="temaLogo";
                $campoTabla="uss_tema_logo";
                break;
            case 5://CAMBIAR TODO EL TEMA
                $campoGet="temaHeader";
                $campoTabla="uss_tema_header";
                break;
        }
        if($campoGet){
                while($yearStart <= $yearEnd){	
                    if ($_SESSION["bd"] == $yearStart) {			
                        if($get == 5) {
                            $sql = "UPDATE ".BD_GENERAL.".usuarios SET 
                            uss_tema_header=?, 
                            uss_tema_sidebar=?, 
                            uss_tema_logo=? 
                            WHERE uss_id=? AND institucion=? AND year=?";
                            $parametros = [$_GET["temaHeader"], $_GET["temaSidebar"], $_GET["temaLogo"], $_SESSION["id"], $_SESSION["idInstitucion"], $yearStart];
                            $resultado = BindSQL::prepararSQL($sql, $parametros);
                        }
                        else {
                            $sql = "UPDATE ".BD_GENERAL.".usuarios SET $campoTabla=? WHERE uss_id=? AND institucion=? AND year=?";
                            $parametros = [$_GET[$campoGet], $_SESSION["id"], $_SESSION["idInstitucion"], $yearStart];
                            $resultado = BindSQL::prepararSQL($sql, $parametros);
                        }
                    } else {
                        $usuarioSession = $_SESSION["datosUsuario"];
                        $usauriosOtrosAnios = UsuariosPadre::sesionUsuarioAnio($usuarioSession['uss_usuario'], $yearStart);
                        if($usauriosOtrosAnios) {
                            if($get == 5) {
                                $sql = "UPDATE ".BD_GENERAL.".usuarios SET 
                                uss_tema_header=?, 
                                uss_tema_sidebar=?, 
                                uss_tema_logo=? 
                                WHERE uss_id=? AND institucion=? AND year=?";
                                $parametros = [$_GET["temaHeader"], $_GET["temaSidebar"], $_GET["temaLogo"], $usauriosOtrosAnios["uss_id"], $_SESSION["idInstitucion"], $yearStart];
                                $resultado = BindSQL::prepararSQL($sql, $parametros);
                            }
                            else {
                                $sql = "UPDATE ".BD_GENERAL.".usuarios SET $campoTabla=? WHERE uss_id=? AND institucion=? AND year=?";
                                $parametros = [$_GET[$campoGet], $usauriosOtrosAnios["uss_id"], $_SESSION["idInstitucion"], $yearStart];
                                $resultado = BindSQL::prepararSQL($sql, $parametros); 
                            }
                        }
                        
                    }
                    $yearStart++;
                }
            $_SESSION["datosUsuario"][$campoTabla] = $_GET[$campoGet];		
        }        	
    }

    /**
     * Lista usuarios cuyos nombres coinciden con un patrón dado para un año específico.
     *
     * @param string $nombre Patrón de nombre de usuario.
     * @param string $BD Base de datos a la que se realizará la consulta.
     * @param string $yearBd Año para el cual se realizará la consulta.
     *
     * @return resource|false Devuelve el resultado de la consulta o false en caso de error.
     */
    public static function listarUsuariosCompartir(
        $nombre='',
        $BD='',
        string $yearBd    = ''
    )
    {
        global $config;
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT uss_id, uss_apellido1, uss_apellido2, uss_nombre, uss_nombre2, pes_nombre FROM ".BD_GENERAL.".usuarios uss 
        INNER JOIN ".BD_ADMIN.".general_perfiles ON pes_id=uss_tipo
        WHERE CONCAT(uss_apellido1,' ',uss_apellido2,' ',uss_nombre,' ',uss_nombre2) LIKE '%".$nombre."%' AND uss.institucion=? AND uss.year=? 
        ORDER BY uss_apellido1, uss_apellido2, uss_nombre LIMIT 10";
        $parametros = [$config['conf_id_institucion'], $year];
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        return $consulta;         
    }

    /**
     * Obtiene todos los datos de usuarios de la base de datos, opcionalmente aplicando un filtro de búsqueda.
     *
     * Esta función realiza una consulta a la base de datos para recuperar todos los datos de los usuarios. 
     * Puede aplicarse un filtro de búsqueda opcional para refinar la consulta.
     *
     * @param string $filtroBusqueda (Opcional) - Un filtro de búsqueda que se puede aplicar a la consulta SQL.
     *
     * @return mixed - Un objeto de resultado de la consulta si tiene éxito, o 0 si ocurre un error.
     */
    public static function obtenerTodosLosDatosDeUsuarios(
        string  $filtroBusqueda     =   '',
        int     $instiBd            =   NULL,
        string  $yearBd             =   ''
    ){
        global $conexion;
        $year       = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $idInsti    = !empty($instiBd) ? $instiBd : $_SESSION["idInstitucion"];

        $sql = "SELECT * FROM ".BD_GENERAL.".usuarios uss 
        INNER JOIN ".BD_ADMIN.".general_perfiles ON pes_id=uss_tipo 
        WHERE uss.institucion=? AND uss.year=? {$filtroBusqueda}";
        $parametros = [$idInsti, $year];
        $consultaUsuario = BindSQL::prepararSQL($sql, $parametros);

        return $consultaUsuario;

    }

	public static function verificarTipoUsuario($tipoUsuario, $paginaRedireccion){
		switch($tipoUsuario){	
			case 1: $url = '../directivo/'.$paginaRedireccion; break;
			case 2: $url = '../docente/'.$paginaRedireccion; break;
			case 3: $url = '../acudiente/'.$paginaRedireccion; break;
			case 4: $url = '../estudiante/'.$paginaRedireccion; break;
			case 5: $url = '../directivo/'.$paginaRedireccion; break;

			default: $url = '../controlador/salir.php'; break;
	  	}
		return $url;
	}

    /**
     * Valida la existencia de un usuario en toda la tabla.
     *
     * @param mysqli $conexion
     * @param string $usuario
     * @param int $idUsuario
     *
     * @return int Número de filas que coinciden con la consulta.
     */
    public static function validarUsuario(
        mysqli $conexion,
        string $usuario,
        int $idUsuario = 0
    ){
        $filtro = "";
        if (!empty($idUsuario) && $idUsuario != 0) {
            $filtro = "AND id_nuevo != '".$idUsuario."'";
        }
        $num = 0;
        
        $sql = "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE uss_usuario=? {$filtro}";
        $parametros = [$usuario];
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($consulta);
        return $num;
    }

    /**
     * Valida la existencia de un usuario en toda la tabla.
     *
     * @param mysqli $conexion
     * @param string $tipoUsuario
     *
     * @return int Número de filas que coinciden con la consulta.
     */
    public static function contarUsuariosPorTipo(
        mysqli $conexion,
        string $tipoUsuario,
    ){
        $num = 0;
        $sql = "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE institucion=? AND year=? AND uss_tipo IN (".$tipoUsuario.")";
        $parametros = [$_SESSION["idInstitucion"], $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($consulta);
        return $num;
    }
    
    /**
     * Valida la existencia de un usuario por su documento en toda la tabla.
     *
     * @param mysqli $conexion
     * @param array $config
     * @param string $documento
     * @param int $idUsuario
     *
     * @return int Número de filas que coinciden con la consulta.
     */
    public static function validarDocumento(
        mysqli $conexion,
        array $config,
        string $documento,
        int $idUsuario = 0
    ){
        $filtro = "";
        if (!empty($idUsuario) && $idUsuario != 0) {
            $filtro = "AND id_nuevo != '".$idUsuario."'";
        }

        $doctSinPuntos = strpos($documento, '.') == true ? str_replace('.', '', $documento) : $documento;
        $doctConPuntos = strpos($documento, '.') !== true && is_numeric($documento) ? str_replace('.', '', $documento) : $documento;
        $num = 0;
        $sql = "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE (uss_documento=? OR uss_documento=?) {$filtro} AND institucion=? AND year=?";
        $parametros = [$doctSinPuntos, $doctConPuntos, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($consulta);
        return $num;
    }

    /**
     * Este metodo me elimina todos los usuarios de una institucion
    **/
    public static function eliminarTodosUsuarios (
        string  $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_GENERAL.".usuarios WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me cambia el tipo de menu para el usuario actual
    **/
    public static function cambiarTipoMenu (
        array   $config,
        string  $tipoMenu,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET uss_tipo_menu=? WHERE uss_id=? AND institucion=? AND year=?";

        $parametros = [$tipoMenu, $_SESSION["id"], $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me cambia la contraseña del usuario actual
    **/
    public static function cambiarClave (
        array   $config,
        string  $clave,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET uss_clave=SHA1(?) WHERE uss_id=? AND institucion=? AND year=?";

        $parametros = [$clave, $_SESSION["id"], $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me trae los datos de los cumplimentados
    **/
    public static function consultarCumplimentados ()
    {
        $sql = "SELECT uss_nombre, YEAR(uss_fecha_nacimiento) AS agno, uss_foto, uss_id, uss_mostrar_edad FROM ".BD_GENERAL.".usuarios 
        WHERE MONTH(uss_fecha_nacimiento)='".date("m")."' AND DAY(uss_fecha_nacimiento)='".date("d")."' AND institucion=? AND year=?";

        $parametros = [$_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae los usuarios duplicados
    **/
    public static function consultarUsuarioDuplicados (
        array $config,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT GROUP_CONCAT( uss_id SEPARATOR ', ') as uss_id, uss_usuario, pes_nombre, uss_apellido1, uss_apellido2, uss_nombre2, uss_nombre, COUNT(*) as duplicados FROM ".BD_GENERAL.".usuarios uss 
        INNER JOIN ".BD_ADMIN.".general_perfiles ON pes_id=uss_tipo
        WHERE uss.institucion=? AND uss.year=?
        GROUP BY uss_usuario
        HAVING COUNT(*) > 1 
        ORDER BY uss_id ASC";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae la entrada de un usuario
    **/
    public static function consultarEntrada (
        array   $config,
        string  $idUsuario,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT (DATEDIFF(uss_ultimo_ingreso, now())*-1) FROM ".BD_GENERAL.".usuarios WHERE uss_id=? AND institucion=? AND year=?";

        $parametros = [$idUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        // Obtener la fila de resultados como un array asociativo
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me trae la salida de un usuario
    **/
    public static function consultarSalida (
        array   $config,
        string  $idUsuario,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT (DATEDIFF(uss_ultima_salida, now())*-1) FROM ".BD_GENERAL.".usuarios WHERE uss_id=? AND institucion=? AND year=?";

        $parametros = [$idUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        // Obtener la fila de resultados como un array asociativo
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me actualiza la informacion de un usuario
    **/
    public static function guardarUsuario (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_GENERAL, 'usuarios');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_GENERAL.".usuarios({$insert}) VALUES ({$signosPreguntas})";
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }

    /**
     * Este metodo me actualiza la informacion de un usuario
    **/
    public static function actualizarUsuarios (
        array   $config,
        string  $idUsuario,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET {$updateSql}, uss_ultima_actualizacion=now() WHERE uss_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idUsuario, $config['conf_id_institucion'], $year]);
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me actualiza el usuario de los estudiantes por su documento
    **/
    public static function actualizarUsuariosEstudiantesDocumento (
        array   $config,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET uss_usuario=(SELECT mat_documento FROM ".BD_ACADEMICA.".academico_matriculas WHERE mat_id_usuario=uss_id AND mat_documento!='' AND institucion=? AND year=?) WHERE uss_tipo=4 AND institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $year, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me actualiza la clave a un tipo de usuarios por su usuario
    **/
    public static function actualizarUsuariosClavePorTipoUsuario (
        array   $config,
        string  $tipoUsuario,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET uss_clave=SHA1(uss_usuario) WHERE uss_tipo=? AND institucion=? AND year=?";

        $parametros = [$tipoUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me actualiza la clave a un tipo de usuarios por una clave ingresada por el directivo
    **/
    public static function actualizarUsuariosClaveManualPorTipoUsuario (
        array   $config,
        string  $clave,
        string  $tipoUsuario,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET uss_clave=SHA1(?) WHERE uss_tipo=? AND institucion=? AND year=?";

        $parametros = [$clave, $tipoUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae los 10 ultimos usuarios que estan en linea
    **/
    public static function consultaUsuariosOnline(
        array   $config,
        string  $idUsuario,
        string  $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT uss_id, uss_nombre, uss_apellido1, uss_foto, uss_estado FROM ".BD_GENERAL.".usuarios WHERE uss_estado=1 AND uss_bloqueado=0 AND uss_id!=? AND institucion=? AND year=? LIMIT 10";

        $parametros = [$idUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae los 5 ultimos usuarios que estan fuera de linea
    **/
    public static function consultaUsuariosOffline(
        array   $config,
        string  $idUsuario,
        string  $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT uss_id, uss_nombre, uss_apellido1, uss_foto, uss_estado FROM ".BD_GENERAL.".usuarios WHERE uss_estado=0 AND uss_bloqueado=0 AND uss_id!=? AND institucion=? AND year=? LIMIT 5";

        $parametros = [$idUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me consulta los usuarios por tipo
    **/
    public static function consultaUsuariosPorTipo(
        string  $tipoUsuario,
        int     $instiBd = NULL,
        string  $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $idInsti    = !empty($instiBd) ? $instiBd : $_SESSION["idInstitucion"];

        $sql = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_tipo=? AND institucion=? AND year=?";

        $parametros = [$tipoUsuario, $idInsti, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me bloquea un usuario
    **/
    public static function bloquearUsuario(
        array   $config,
        string  $idUsuario,
        int     $bloquearDesbloquear,
        string  $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_GENERAL.".usuarios SET uss_bloqueado=? WHERE uss_id=? AND institucion=? AND year=?";

        $parametros = [$bloquearDesbloquear, $idUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina un usuario por su ID
    **/
    public static function eliminarUsuarioPorID(
        array   $config,
        string  $idUsuario,
        string  $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_GENERAL.".usuarios WHERE uss_id=? AND institucion=? AND year=?";

        $parametros = [$idUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina un usuario por su Usuario
    **/
    public static function eliminarUsuarioPorUsuario(
        array   $config,
        string  $usuario,
        string  $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_GENERAL.".usuarios WHERE uss_usuario=? AND institucion=? AND year=?";

        $parametros = [$usuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina todos los usuario de cierto tipo
    **/
    public static function eliminarUsuarioPorTipoUsuario(
        array   $config,
        string  $tipoUsuario,
        string  $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_GENERAL.".usuarios WHERE uss_tipo=? AND institucion=? AND year=?";

        $parametros = [$tipoUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
}   