<?php

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
        global $conexion;
        global $yearStart;
        global $yearEnd;
        global $baseDatosServicios;
        global $filtro;
        $index = 0;
        $tableName = BDT_GeneralPerfiles::getTableName();
        $arraysDatos = [];

        while ($yearStart <= $yearEnd) {
            $consultaUsuarioAuto = mysqli_query($conexion, "SELECT * FROM " . BD_GENERAL . ".usuarios uss 
            INNER JOIN " . $baseDatosServicios . ".{$tableName} ON pes_id=uss_tipo
            WHERE uss_usuario LIKE '" . $usuario . "%' AND uss.institucion={$_SESSION["idInstitucion"]} AND uss.year={$yearStart}");

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
    public static function sesionUsuario($idUsuario, $filtroAdicional='')
    {
        global $conexion;

        try{
            $consultaUsuarioAuto = mysqli_query($conexion, "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id='".$idUsuario."' AND institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]} {$filtroAdicional}");
            $datosUsuarioAuto = mysqli_fetch_array($consultaUsuarioAuto, MYSQLI_BOTH);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return [];
        }
    
        return $datosUsuarioAuto;
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
        global $conexion;
        $consultaUsuarioAuto = mysqli_query($conexion, "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_usuario='".$usuario."' AND institucion={$_SESSION["idInstitucion"]} AND year={$year} limit 1");
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
                            mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET 
                            uss_tema_header='" . $_GET["temaHeader"] . "', 
                            uss_tema_sidebar='" . $_GET["temaSidebar"] . "', 
                            uss_tema_logo='" . $_GET["temaLogo"] . "' 
                            WHERE uss_id='" . $_SESSION["id"] . "' AND institucion={$_SESSION["idInstitucion"]} AND year={$yearStart}");
                        }
                        else {
                            mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET $campoTabla='" . $_GET[$campoGet] . "' 
                            WHERE uss_id='" . $_SESSION["id"] . "' AND institucion={$_SESSION["idInstitucion"]} AND year={$yearStart}");
                        }
                    } else {
                        $usuarioSession = $_SESSION["datosUsuario"];
                        $usauriosOtrosAnios = UsuariosPadre::sesionUsuarioAnio($usuarioSession['uss_usuario'], $yearStart);
                        if($usauriosOtrosAnios) {
                            if($get == 5) {
                                mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET 
                                uss_tema_header='" . $_GET["temaHeader"] . "', 
                                uss_tema_sidebar='" . $_GET["temaSidebar"] . "', 
                                uss_tema_logo='" . $_GET["temaLogo"] . "' 
                                WHERE uss_id='" .$usauriosOtrosAnios["uss_id"]."' AND institucion={$_SESSION["idInstitucion"]} AND year={$yearStart}");
                            }
                            else {
                                mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET $campoTabla='" . $_GET[$campoGet] . "' 
                                WHERE uss_id='" .$usauriosOtrosAnios["uss_id"]. "' AND institucion={$_SESSION["idInstitucion"]} AND year={$yearStart}"); 
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
        global $conexion,$baseDatosServicios, $config;
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $consulta= mysqli_query($conexion, "SELECT uss_id,uss_apellido1,uss_apellido2,uss_nombre,uss_nombre2,pes_nombre FROM ".BD_GENERAL.".usuarios uss 
        INNER JOIN ".$baseDatosServicios.".general_perfiles ON pes_id=uss_tipo
        WHERE CONCAT(uss_apellido1,' ',uss_apellido2,' ',uss_nombre,' ',uss_nombre2) LIKE '%".$nombre."%' AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$year} ORDER BY uss_apellido1, uss_apellido2, uss_nombre LIMIT 10");

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
    public static function obtenerTodosLosDatosDeUsuarios($filtroBusqueda='')
    {
        global $conexion, $baseDatosServicios;

        try{
            $consultaUsuario = mysqli_query($conexion, "SELECT * FROM ".BD_GENERAL.".usuarios uss 
            INNER JOIN ".$baseDatosServicios.".general_perfiles ON pes_id=uss_tipo 
            WHERE uss.institucion={$_SESSION["idInstitucion"]} AND uss.year={$_SESSION["bd"]} {$filtroBusqueda}");
            return $consultaUsuario;
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return 0;
        }

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
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE uss_usuario='" . $usuario . "' {$filtro}");
            $num = mysqli_num_rows($consulta);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

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
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM " . BD_GENERAL . ".usuarios WHERE institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]} AND uss_tipo='" . $tipoUsuario . "'");
            $num = mysqli_num_rows($consulta);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $num;
    }

}   