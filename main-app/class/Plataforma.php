<?php
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class Plataforma {

    /**
     * Colores principales de la plataforma: Encabezado, logo y menú principal.
     * 
     * Actualizado: 28 de Noviembre de 2024
     * 
     * Principal: #283655
     * Secundario: #4D648D
     * 
     * Archivo: config-general\assets\css\theme\light\theme_style.css
     */

    public $colorUno   = '#6017dc';
    public $colorDos   = '#41c4c4';
    public $colorTres  = '#56ffe4';

    public $logo       = 'https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png'; // Es purpura
    public $logoBlanco = 'https://main.plataformasintia.com/app-sintia/sinita-blanco.png'; //Se usa sobre fondo Purpura
    public $logoCian   = 'https://main.plataformasintia.com/app-sintia/sinita-cian.png'; //Se usa sobre fondo cian
    public $logoGris   = 'https://main.plataformasintia.com/app-sintia/sintia-gris.png'; //Es gris

    /**
     * Muestra el contenido de un modal de términos y políticas.
     *
     * Recupera y devuelve la información de un modal de términos y políticas específico.
     *
     * @global resource $conexion - Recurso de conexión a la base de datos.
     * @global string $baseDatosServicios - Nombre de la base de datos de servicios.
     *
     * @param int $i - Identificador del modal de términos y políticas.
     *
     * @return array - Devuelve un array con la información del modal de términos y políticas.
     *
     * @example
     * ```php
     * // Ejemplo de uso para mostrar el contenido de un modal de términos y políticas
     * $idModal = 1;
     * $contenidoModal = mostrarModalTerminos($idModal);
     * if (!empty($contenidoModal)) {
     *     // Mostrar el contenido del modal
     *     echo $contenidoModal['ttp_contenido'];
     * } else {
     *     // Mostrar mensaje o realizar acciones cuando no se encuentra el modal
     * }
     * ```
     */
    public static function mostrarModalTerminos($i = 0)
    {

        global $conexion, $baseDatosServicios;
        $resultado = [];

        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politica WHERE ttp_id=$i");
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;

    }

    /**
     * Este consulta la información de la plataforma sintia y la
     * disponibiliza
     * 
     * @return array
     */
    public static function infoContactoSintia()
    {

        global $conexion, $baseDatosServicios;
        $conexionReal = $conexion;

        if(!$conexion) {
            global $servidorConexion, $usuarioConexion, $claveConexion;
            $conexionReal = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
        }

        $resultado = [];

        try {
            $consulta = mysqli_query($conexionReal, "SELECT * FROM ".$baseDatosServicios.".datos_contacto WHERE dtc_id=1");
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;

    }

    /**
     * Este metodo sirve para consultar la configuración de la institución
     * 
     * @return array
     */
    public static function sesionConfiguracion()
    {

        global $conexion, $baseDatosServicios;

        $config = [];

        try {
            $configConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".configuracion 
            WHERE 
                conf_id_institucion='".$_SESSION["idInstitucion"]."' 
            AND conf_agno='".$_SESSION["bd"]."'");
            $config = mysqli_fetch_array($configConsulta, MYSQLI_BOTH);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $config;

    }

    public static function getConexion() {
        global $conexion;

        return $conexion;
    }

    /**
     * Esta función sirve para guardar los errores que sucedan durante
     * la ejecución de código, sin mostrar ninguna interface al usuario.
     * 
     * @param $e Exception
     */
    public static function soloRegistroErrores(Exception $e) {

        $numError     = $e->getCode();
        $lineaError   = $e->getLine();
        $aRemplezar   = array("'", '"', "#", "´");
        $enRemplezo   = array("\'", "|", "\#", "\´");
        $detalleError = str_replace($aRemplezar, $enRemplezo, $e->getMessage());
        $request_data = json_encode($_REQUEST);
        global $conexion;
        global $baseDatosServicios;
        global $config;
        $request_data_sanitizado = mysqli_real_escape_string($conexion, $request_data);

        try {
            mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".reporte_errores(rperr_numero, rperr_fecha, rperr_ip, rperr_usuario, rperr_pagina_referencia, rperr_pagina_actual, rperr_so, rperr_linea, rperr_institucion, rperr_error, rerr_request, rperr_year)
            VALUES('".$numError."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SESSION["id"]."', '".$_SERVER['HTTP_REFERER']."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$lineaError."', '".$config['conf_id_institucion']."','".$detalleError."', '".$request_data_sanitizado."', '".$_SESSION["bd"]."')");
            $idReporteError = mysqli_insert_id($conexion);
            return $idReporteError;
        } catch (Exception $e) {
            return "Hay un inconveniente al guardar el error: ".$e->getMessage();
        }
    }

    /**
     * Este metodo sirve para consultar la configuración de la institución
     * 
     * @return array
     */
    public static function traerDatosPlanes(
        mysqli  $conexion,
        int     $idPlan
    )
    {
        $resultado = [];

        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".planes_sintia WHERE plns_id='{$idPlan}'");
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        } catch (Exception $e) {
            include(ROOT_PATH."/compartido/error-catch-to-report.php");
        }

        return $resultado;

    }

    public static function listarPlanesPorTipo(
        string     $tipoPlan
    )
    {
        $sql = "SELECT * FROM ".BD_ADMIN.".planes_sintia WHERE plns_tipo=?";

        $parametros = [$tipoPlan];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        return $consulta;

    }

    public static function listarPaquetes(
        string $filtro
    )
    {
        $sql = "SELECT * FROM ".BD_ADMIN.".planes_sintia WHERE plns_tipo!=? {$filtro}";

        $parametros = [PLANES];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        return $consulta;

    }

    public static function validarPaquete(
        string     $idInstitucion,
        string     $idPaquete,
        string     $tipoPaquete
    )
    {
        $sql = "SELECT * FROM ".BD_ADMIN.".instituciones_paquetes_extras WHERE paqext_institucion=? AND paqext_id_paquete=? AND paqext_tipo=?";

        $parametros = [$idInstitucion, $idPaquete, $tipoPaquete];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $numDatos=mysqli_num_rows($consulta);
        if ($numDatos > 0) { 
            return true;
        }
        return false;

    }

    public static function contarDatosPaquetes(
        int     $idInstitucion,
        string  $tipoPaquete
    )
    {
        $sql = "SELECT 
        SUM(plns_valor) AS plns_valor, 
        SUM(plns_espacio_gb) AS plns_espacio_gb, 
        GROUP_CONCAT(DISTINCT CASE WHEN plns_modulos IS NOT NULL AND plns_modulos != '' THEN plns_modulos END ORDER BY plns_modulos SEPARATOR ',') AS plns_modulos, 
        SUM(plns_cant_directivos) AS plns_cant_directivos, 
        SUM(plns_cant_docentes) AS plns_cant_docentes, 
        SUM(plns_cant_estudiantes) AS plns_cant_estudiantes FROM ".BD_ADMIN.".instituciones_paquetes_extras
        INNER JOIN ".BD_ADMIN.".planes_sintia ON plns_id=paqext_id_paquete
        WHERE paqext_institucion=? AND paqext_tipo=?";

        $parametros = [$idInstitucion, $tipoPaquete];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;

    }

}