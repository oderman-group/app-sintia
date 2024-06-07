<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class Modulos {

    /**
     * Verifica los permisos de acceso a una página interna según el ID de la página.
     *
     * @param string $idPaginaInterna - El ID de la página interna a verificar.
     *
     * @return bool - Devuelve true si el usuario tiene permisos para acceder a la página, de lo contrario, devuelve false.
     *
     * @throws Exception - Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso para verificar permisos de acceso a una página interna
     * $idPagina = 'D0001'; // ID de la página interna a verificar
     * $permisos = verificarPermisosPaginas($idPagina);
     * if ($permisos) {
     *     echo "El usuario tiene permisos para acceder a la página.";
     * } else {
     *     echo "El usuario no tiene permisos para acceder a la página.";
     * }
     * ```
     */
    public static function verificarPermisosPaginas($idPaginaInterna): bool
    {

        $datosPaginaActual = self::datosPaginaActual($idPaginaInterna);
        if( empty($datosPaginaActual) ) {
            return false;
        }
        return true;

    }

    /**
     * Verifica si el usuario actual tiene permisos de desarrollador.
     *
     * @global array $datosUsuarioActual - Los datos del usuario actual.
     *
     * @throws Exception - Redirige a la página de información con el mensaje de error 301 si el usuario no tiene permisos de desarrollador.
     *
     * @example
     * ```php
     * // Ejemplo de uso para verificar permisos de desarrollador
     * verificarPermisoDev();
     * // El código siguiente a esta llamada solo se ejecutará si el usuario tiene permisos de desarrollador.
     * echo "El usuario tiene permisos de desarrollador.";
     * ```
     */
    public static function verificarPermisoDev(){

        global $datosUsuarioActual;

        if($datosUsuarioActual['uss_tipo']!= TIPO_DEV && $datosUsuarioActual['uss_permiso1'] != CODE_DEV_MODULE_PERMISSION){
            echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
            exit();	
        }
    }

    /**
     * Verifica si el usuario actual tiene permisos de directivo especial.
     *
     * @global array $datosUsuarioActual - Los datos del usuario actual.
     *
     * @throws Exception - Redirige a la página de información con el mensaje de error 301 si el usuario no tiene permisos de directivo especial.
     *
     * @example
     * ```php
     * // Ejemplo de uso para verificar permisos de directivo especial
     * verificarPermisoDirectivoEspecial();
     * // El código siguiente a esta llamada solo se ejecutará si el usuario tiene permisos de directivo especial.
     * echo "El usuario tiene permisos de directivo especial.";
     * ```
     */
    public static function verificarPermisoDirectivoEspecial(){

        global $datosUsuarioActual;

        if($datosUsuarioActual['uss_tipo']!= TIPO_DIRECTIVO && $datosUsuarioActual['uss_permiso1'] != CODE_PRIMARY_MANAGER){
            echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
            exit();	
        }
    }

    /**
     * Verifica si los módulos de una institución están habilitados.
     *
     * @global mysqli $conexion - La conexión a la base de datos.
     * @global string $baseDatosServicios - El nombre de la base de datos de servicios.
     *
     * @param int $idInstitucion - El ID de la institución.
     * @param int $idModulos - El ID del módulo.
     *
     * @return bool - Devuelve `true` si los módulos de la institución están habilitados, de lo contrario, devuelve `false`.
     *
     * @example
     * ```php
     * // Ejemplo de uso para verificar módulos de una institución
     * $idInstitucion = 1;
     * $idModulo = 2;
     * if (verificarModulosDeInstitucion($idInstitucion, $idModulo)) {
     *     echo "Los módulos de la institución están habilitados.";
     * } else {
     *     echo "Los módulos de la institución no están habilitados.";
     * }
     * ```
     */
    public static function verificarModulosDeInstitucion($idInstitucion,$idModulos)
    {
        global $conexion, $baseDatosServicios;

        $consultaModulos = mysqli_query($conexion, "SELECT ipmod_modulo AS id_modulo FROM ".BD_ADMIN.".instituciones_modulos 
        WHERE ipmod_institucion='".$idInstitucion."' AND ipmod_modulo='".$idModulos."'
        UNION
        SELECT paqext_id_paquete AS id_modulo FROM ".BD_ADMIN.".instituciones_paquetes_extras 
        WHERE paqext_institucion='".$idInstitucion."' AND paqext_id_paquete='".$idModulos."' AND paqext_tipo='".MODULOS."'");
        $modulos = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH);
        if (empty($modulos[0])) { 
            $datosPaquetes = Plataforma::contarDatosPaquetes($idInstitucion, PAQUETES);
            if (!empty($datosPaquetes['plns_modulos'])) {
                $modulosArray = explode(',', $datosPaquetes['plns_modulos']);
                if (in_array($idModulos, $modulosArray)) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Valida el acceso directo a las páginas.
     *
     * Verifica si la página ha sido accedida directamente o a través de un enlace.
     * Si la página se accede directamente, redirige a una página de información.
     *
     * @return void - No devuelve ningún valor. Si la página se accede directamente, redirige a otra página.
     *
     * @example
     * ```php
     * // Ejemplo de uso para validar el acceso directo a las páginas
     * validarAccesoDirectoPaginas();
     * ```
     */
    public static function validarAccesoDirectoPaginas(){
        if($_SERVER['HTTP_REFERER']==""){
            echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=303";</script>';
            exit();
        }
    }

    /**
     * Valida el permiso de edición en años anteriores.
     *
     * Verifica si está permitida la edición en años anteriores según la configuración del sistema.
     * Devuelve true si está permitida y false si no lo está.
     *
     * @global array $config - Configuración del sistema.
     *
     * @return bool - Devuelve true si está permitida la edición en años anteriores, false si no lo está.
     *
     * @example
     * ```php
     * Ejemplo de uso para validar el permiso de edición en años anteriores
     * if (validarPermisoEdicion()) {
     *      Realizar acciones permitidas
     * } else {
     *     Mostrar mensaje de error o realizar acciones cuando no está permitida la edición
     * }
     * ```
     */
    public static function validarPermisoEdicion(){
        global $config;

        if($config['conf_permiso_edicion_years_anteriores']==0){
            return false;
        }
        return true;
    }

    /**
     * Este metodo sirve para validar el acceso a las diferentes paginas de los directivos dependiendo de su rol
     * 
     * @param array     $paginas
     * @param bool      $menu
     * 
     * @return bool
    **/
    public static function validarSubRol($paginas){
        global $conexion, $baseDatosServicios, $datosUsuarioActual, $config, $arregloModulos;

        //Si la institución no tiene este módulo (Subroles) asignado entonces devolvemos true siempre
        if( ( $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && (empty($arregloModulos) || !array_key_exists(16, $arregloModulos)) ) 
        || $datosUsuarioActual['uss_tipo'] == TIPO_DEV ) {
            return true;
        }

        if ($datosUsuarioActual['uss_tipo'] != TIPO_DIRECTIVO) { 
            return false;
        }

        try{
            $consultaSubRoles = mysqli_query($conexion, "SELECT spu_id_sub_rol FROM ".$baseDatosServicios.".sub_roles_usuarios 
            WHERE spu_id_usuario='".$datosUsuarioActual['uss_id']."' AND spu_institucion='".$config['conf_id_institucion']."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numSubRoles=mysqli_num_rows($consultaSubRoles);
        if ($numSubRoles<1) { 
            return true;
        }else{
            $subRoles = mysqli_fetch_all($consultaSubRoles, MYSQLI_ASSOC);
            $valoresArray = array_column($subRoles, 'spu_id_sub_rol');
            $valoresCadena = implode(',', $valoresArray);
            try{
                $consultaPaginaSubRoles = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".sub_roles_paginas 
                WHERE spp_id_rol IN ($valoresCadena)");
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
            $subRolesPaginas = mysqli_fetch_all($consultaPaginaSubRoles, MYSQLI_ASSOC);
            $valoresPaginas = array_column($subRolesPaginas, 'spp_id_pagina');
            $permitidos= array_intersect($paginas,$valoresPaginas);
            if(!empty($permitidos)){
                return true;
            }
        }
        return false;
    }

    /**
     * Este metodo sirve para validar si las paginas hijas estan asignadas aun rol
     * 
     * @param string     $idPagina
     * 
     * @return bool
    **/
    public static function validarPaginasHijasSubRol($idPagina){
        global $conexion, $baseDatosServicios;

        try{
            $consultaPaginasHijas=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_pagina_padre='".$idPagina."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numPaginasHijas=mysqli_num_rows($consultaPaginasHijas);
        if ($numPaginasHijas>0) {
            $datosPaginasHijas = mysqli_fetch_all($consultaPaginasHijas, MYSQLI_ASSOC);
            $arrayPaginasHijas = array_column($datosPaginasHijas, 'pagp_id');
            $arrayPaginasHijasCadena = array_map(function($valor) { return "'" . $valor . "'"; }, $arrayPaginasHijas);
            $idPaginasHijas = implode(',', $arrayPaginasHijasCadena);
            try{
                $consultaPaginaSubRoles = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".sub_roles_paginas 
                WHERE spp_id_pagina IN ($idPaginasHijas)");
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
            $subRolesPaginas = mysqli_fetch_all($consultaPaginaSubRoles, MYSQLI_ASSOC);
            $valoresPaginas = array_column($subRolesPaginas, 'spp_id_pagina');
            $permitidos= array_intersect($arrayPaginasHijas,$valoresPaginas);
            if(!empty($permitidos)){
                return false;
            }
        }
        return true;
    }

    /**
     * Obtener Datos de la Página Actual por su Identificador Interno
     *
     * Esta función se utiliza para recuperar datos de una página actual en función de su identificador interno.
     *
     * @param int $idPaginaInterna El identificador interno de la página que se desea obtener.
     *
     * @return array Un array asociativo que contiene los datos de la página actual, o un array vacío si no se encuentra la página.
     */
    public static function datosPaginaActual($idPaginaInterna): array
    {
        global $conexion, $baseDatosServicios, $config;

        $consultaPaginaActualUsuarios = mysqli_query($conexion, "SELECT pp.* FROM ".BD_ADMIN.".paginas_publicidad pp
        INNER JOIN ".BD_ADMIN.".instituciones_modulos ON ipmod_modulo=pagp_modulo AND ipmod_institucion='".$config['conf_id_institucion']."'
        WHERE pagp_id='".$idPaginaInterna."'
        UNION
        SELECT pp.* FROM ".BD_ADMIN.".paginas_publicidad pp
        INNER JOIN ".BD_ADMIN.".instituciones_paquetes_extras ON paqext_institucion='".$config['conf_id_institucion']."' AND paqext_id_paquete=pagp_modulo AND paqext_tipo='".MODULOS."'
        WHERE pagp_id='".$idPaginaInterna."'");
        $paginaActualUsuario = mysqli_fetch_array($consultaPaginaActualUsuarios, MYSQLI_BOTH);
        if (empty($paginaActualUsuario)) { 
            $datosPaquetes = Plataforma::contarDatosPaquetes($config['conf_id_institucion'], PAQUETES);
            if (!empty($datosPaquetes['plns_modulos'])) {
                $consultaPaginaActualUsuarios2 = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".paginas_publicidad pp
                WHERE pagp_id='".$idPaginaInterna."' AND pagp_modulo IN (".$datosPaquetes['plns_modulos'].")");
                $paginaActualUsuario = mysqli_fetch_array($consultaPaginaActualUsuarios2, MYSQLI_BOTH);
                if (!empty($paginaActualUsuario)) { 
                    return $paginaActualUsuario;
                }
            }
            return [];
        }
        return $paginaActualUsuario;
    }

    /**
     * Este metodo sirve para validar si un modulo esta activo o no
     * 
     * @param int   $modulo
     * 
     * @return bool
    **/
    public static function validarModulosActivos($conexion, $modulo){

        try{
            $consultaModulo = mysqli_query($conexion, "SELECT mod_estado FROM ".BD_ADMIN.".modulos WHERE mod_id='".$modulo."' AND mod_types_customer LIKE '%".$_SESSION["datosUnicosInstitucion"]['ins_tipo']."%'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numDatosModulo=mysqli_num_rows($consultaModulo);
        if ($numDatosModulo > 0) { 
            $datosModulo = mysqli_fetch_array($consultaModulo, MYSQLI_BOTH);
            if ($datosModulo['mod_estado'] == 1) {
                return true;
            }
        }
        return false;
    }

    public static function listarModulos(
        mysqli $conexion,
        string $filtro = "",
        string $limit = "LIMIT 0, 2000",
        int $estado = NULL 
    ){
        $filtroEstado = !empty($estado) ? "AND mod_estado={$estado}" : "";

        $sql = "SELECT * FROM ".BD_ADMIN.".modulos
        WHERE mod_id=mod_id {$filtro} {$filtroEstado}
        ORDER BY mod_id
        {$limit}";
        
        $consulta = mysqli_query($conexion, $sql);

        return $consulta;
    }

    public static function consultarModulosIntitucion(
        mysqli  $conexion,
        int     $idInstitucion
    ){
        $arregloModulos = array();
        $modulosSintia = mysqli_query($conexion, "SELECT mod_id, mod_nombre FROM ".BD_ADMIN.".modulos
        INNER JOIN ".BD_ADMIN.".instituciones_modulos ON ipmod_institucion='".$idInstitucion."' AND ipmod_modulo=mod_id
        WHERE mod_estado=1
        UNION
        SELECT mod_id, mod_nombre FROM ".BD_ADMIN.".modulos
        INNER JOIN ".BD_ADMIN.".instituciones_paquetes_extras ON paqext_institucion='".$idInstitucion."' AND paqext_id_paquete=mod_id AND paqext_tipo='".MODULOS."'
        WHERE mod_estado=1");
        while($modI = mysqli_fetch_array($modulosSintia, MYSQLI_BOTH)){
            $arregloModulos [$modI['mod_id']] = $modI['mod_nombre'];
        }

        $datosPaquetes = Plataforma::contarDatosPaquetes($idInstitucion, PAQUETES);
        if (!empty($datosPaquetes['plns_modulos'])) {
            $modulosSintia2 = mysqli_query($conexion, "SELECT mod_id, mod_nombre FROM ".BD_ADMIN.".modulos
            INNER JOIN ".BD_ADMIN.".instituciones_modulos ON ipmod_institucion='".$idInstitucion."' AND ipmod_modulo=mod_id
            WHERE mod_estado=1
            UNION
            SELECT mod_id, mod_nombre FROM ".BD_ADMIN.".modulos WHERE mod_estado=1 AND mod_id IN (".$datosPaquetes['plns_modulos'].")");
            while($modI = mysqli_fetch_array($modulosSintia2, MYSQLI_BOTH)){
                $arregloModulos [$modI['mod_id']] = $modI['mod_nombre'];
            }
        }

        return $arregloModulos;
    }
    /**
     * ListarModulosConPaginas
     *
     * Este método obtiene una lista de módulos con sus respectivas páginas publicitarias
     * asociadas a una institución específica.
     *
     * Realiza una consulta SQL para seleccionar los módulos relacionados con una institución
     * dada, agrupando por el módulo y ordenando por el ID del módulo.
     *
     * @param int   $tipoUsuario
     * 
     * @return mixed La consulta preparada con los resultados de los módulos.
     */
    public static function ListarModulosConPaginas(
        int $tipoUsuario = TIPO_DIRECTIVO
    ){
        $sql = "SELECT m.* FROM ".BD_ADMIN.".instituciones_modulos im 
        INNER JOIN ".BD_ADMIN.".paginas_publicidad pp ON pp.pagp_modulo=im.ipmod_modulo
        INNER JOIN ".BD_ADMIN.".modulos m ON m.mod_id=pp.pagp_modulo
        WHERE pp.pagp_tipo_usuario=? AND im.ipmod_institucion=?
        GROUP BY pp.pagp_modulo
        ORDER BY m.mod_id";

        $parametros = [$tipoUsuario, $_SESSION["idInstitucion"]];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        return $consulta;
    }
    
    public static function validarModulosExtras($conexion, $modulo, $idInstitucion){

        try{
            $consultaModulo = mysqli_query($conexion, "SELECT paqext_id_paquete FROM ".BD_ADMIN.".instituciones_paquetes_extras WHERE paqext_id_paquete='".$modulo."' AND paqext_institucion='".$idInstitucion."' AND paqext_tipo='MODULOS'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numDatosModulo=mysqli_num_rows($consultaModulo);
        if ($numDatosModulo > 0) { 
            return true;
        }
        return false;
    }
}