<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Modulos extends BDT_Tablas implements BDT_JoinImplements{

    public static $schema = BD_ADMIN;

    public static $tableName = 'modulos';

    public static $primaryKey = 'mod_id';
    
    public static $tableAs = 'modulo'; //El alias no puede ser una palabra reservada SQL

    use BDT_Join;

    public const MODULO_ACADEMICO                           = 1;
    public const MODULO_FINANCIERO                          = 2;
    public const MODULO_DISCIPLINARIO                       = 3;
    public const MODULO_ADMINISTRATIVO                      = 4;
    public const MODULO_PUBLICACIONES                       = 5;
    public const MODULO_MERCADEO                            = 6;
    public const MODULO_GENERAL                             = 7;
    public const MODULO_INSCRIPCIONES                       = 8;
    public const MODULO_RESERVA_CUPO                        = 9;

    public const MODULO_MEDIA_TECNICA                       = 10;
    public const MODULO_CLASES                              = 11; //También incluye las unidades temáticas
    public const MODULO_EVALUACIONES                        = 12;
    public const MODULO_FOROS                               = 13;
    public const MODULO_ACTIVIDAES                          = 14; //Tareas para la casa
    public const MODULO_CRONOGRAMA                          = 15;
    public const MODULO_SUB_ROLES                           = 16;
    public const MODULO_MI_CUENTA                           = 17;
    public const MODULO_CUESTIONARIOS                       = 18;
    public const MODULO_CARPETAS                            = 19;

    public const MODULO_MARKETPLACE                         = 20;
    public const MODULO_IMPORTAR_INFORMACION_ACADEMICA      = 21;
    public const MODULO_INFORMES_ACADEMICOS_BASICOS         = 22;
    public const MODULO_CUALITATIVO                         = 23; //Calificaciones cualitativas (Con descripción o solo desempeño)
    public const MODULO_API_SION_ACADEMICA                  = 24; //Se conecta con el sistema SION de la UNAC
    public const MODULO_NOTIFICACIONES_NOTAS_BAJAS          = 25; //Envía un mensaje por correo al acudiente cuando un estudiante obtiene una nota baja.
    public const MODULO_NOTIFICACIONES_REPORTES_CONVIVENCIA = 26; //Envía un mensaje por correo al acudiente cuando un estudiante le hacen un reporte disciplinario
    public const MODULO_MENSAJES_CORREO                     = 27; //Envía un mensaje por correo a los usuarios cuando se envían mensajes por el correo interno
    public const MODULO_RECUPERAR_INDICADOR                 = 28; //Permite a los docentes insertar recuperación a los indicadores de los estudiantes
    public const MODULO_ADJUNTAR_DOCUMENTOS                 = 29; //Permite adjuntar documentos a los usuarios

    public const MODULO_FACTURA_RECURRENTES                 = 30;
    public const MODULO_REPORTES_FINANCIEROS_GRAFICOS       = 31; //Reportes gráficos en las finanzas
    public const MODULO_AI_GENERAR_IMAGEN_MT                = 32; //Genera imagenes para los cursos de media técnica
    public const MODULO_INFORMES_ADMIN_BASICOS              = 33; 
    public const MODULO_CHAT_ATENCION                       = 34;
    public const MODULO_AI_INDICADORES                      = 35; //Permite a los docentes generar indicadores para sus cursos y asignaturas
    public const MODULO_TIPOS_NOTIFICACIONES                = 36; //Ver y suscribir usuarios los tipos de notificaciones
    public const MODULO_INFORMES_ACADEMICOS_AVANZADOS       = 37;
    public const MODULO_INFORMES_CONVIVENCIA_BASICOS        = 38; 
    public const MODULO_INFORMES_FINANCIEROS_BASICOS        = 39;

    public const MODULO_INFORMES_INSCRIPCIONES_BASICOS      = 40; 
    public const MODULO_AREAS                               = 41; 
    public const MODULO_ASIGNATURAS                         = 42; 
    public const MODULO_AYUDA_AVANZADA                      = 43; 
    public const MODULO_CALIFICACIONES                      = 44; 
    public const MODULO_CARGAS_ACADEMICAS                   = 45; 
    public const MODULO_SMS                                 = 46; // todo: Puede ser reemplazado porque no tiene paginas asociadas.
    public const MODULO_COMPORTAMIENTO                      = 47; 
    public const MODULO_CONFIGURACION                       = 48; 
    public const MODULO_CORREO_INTERNO                      = 49;

    public const MODULO_CREDENCIALES                        = 50; 
    public const MODULO_CURSO_Y_GRADOS                      = 51; 
    public const MODULO_DEMO_SINTIA                         = 52; 
    public const MODULO_ESTILOS_NOTAS                       = 53; 
    public const MODULO_FALTAS_Y_CATEGORIAS                 = 54; 
    public const MODULO_FEEDBACK_SINTIA                     = 55; 
    public const MODULO_FIRMAS_DIGITALES                    = 56; 
    public const MODULO_HERRAMIENTAS                        = 57; 
    public const MODULO_HORARIOS                            = 58; 
    public const MODULO_IMPUESTOS                           = 59;

    public const MODULO_INDICADORES                         = 60; 
    public const MODULO_INDICADORES_INCLUSION               = 61; 
    public const MODULO_MATRICULAS                          = 62; 
    public const MODULO_NOTIFICACIONES_INTERNAS             = 63; 
    public const MODULO_OBSERVACIONES_BOLETIN               = 64; 
    public const MODULO_MATRICULAS_PASOS                    = 65; 
    public const MODULO_PREFERENCIAS                        = 66; 
    public const MODULO_PUBLICIDAD_SINTIA                   = 67; 
    public const MODULO_SEDES                               = 68; 
    public const MODULO_SERVICIOS_SINTIA                    = 69; 

    public const MODULO_TEMATICA                            = 70; 
    public const MODULO_TRANSACCIONES                       = 71; 
    public const MODULO_USO_SINTIA                          = 72; 
    public const MODULO_USUARIOS                            = 73; 
    public const MODULO_AI_GENERAR_DESCRIPCION_MT           = 74; //Genera imagenes para los cursos de media técnica
    public const MODULO_COMUNICADOS                         = 75; //Permite enviar comunicados a usuarios por Email, SMS y WhatsApp

    //TODO: Esto es un workaround para los 9 clientes actuales para mantener la retrocompatibilidad.
    public const MODULOS_GLOBALES_PERMITIDOS_ACTUALMENTE = [
        SELF::MODULO_GENERAL, //Aplica para todos los clientes.
        SELF::MODULO_MI_CUENTA, //Aplica para todos los clientes.
        SELF::MODULO_CONFIGURACION, //Aplica para todos los clientes.
        SELF::MODULO_PREFERENCIAS, //Aplica para todos los clientes.
        SELF::MODULO_USO_SINTIA, //Aplica para todos los clientes.
        SELF::MODULO_USUARIOS, //Aplica para todos los clientes.
        SELF::MODULO_INFORMES_ACADEMICOS_BASICOS,
        SELF::MODULO_INFORMES_ADMIN_BASICOS,
        SELF::MODULO_AREAS,
        SELF::MODULO_ASIGNATURAS,
        SELF::MODULO_CURSO_Y_GRADOS,
        SELF::MODULO_AYUDA_AVANZADA,
        SELF::MODULO_CALIFICACIONES,
        SELF::MODULO_CARGAS_ACADEMICAS,
        SELF::MODULO_CORREO_INTERNO,
        SELF::MODULO_ESTILOS_NOTAS,
        SELF::MODULO_INDICADORES,
        SELF::MODULO_MATRICULAS
    ];

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
     * Utiliza los modulos activos para la institución cargados en la sesion al momento
     * de la autenticación.
     * 
     * 
     */
    public static function verificarModulosDeInstitucion(int $idModulo): bool
    {
        return !empty($_SESSION["modulos"]) && 
                array_key_exists($idModulo, $_SESSION["modulos"]) || 
                (in_array($idModulo, Modulos::MODULOS_GLOBALES_PERMITIDOS_ACTUALMENTE));
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
        if (!isset($_SERVER['HTTP_REFERER']) || (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']=="")) {
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

        if(
            isset($config['conf_permiso_edicion_years_anteriores']) && 
            $config['conf_permiso_edicion_years_anteriores']==0 && 
            $_SESSION["bd"] <> date("Y")
        ) {
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
        if( 
            ( 
                $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && 
                (empty($arregloModulos) || !array_key_exists(self::MODULO_SUB_ROLES, $arregloModulos)) 
            ) || 
            $datosUsuarioActual['uss_tipo'] == TIPO_DEV 
        ) {
            return true;
        }

        if ($datosUsuarioActual['uss_tipo'] != TIPO_DIRECTIVO) { 
            return false;
        }

        $numSubRoles = count($_SESSION["datosUsuario"]["sub_roles"]);

        // Si al usuario directivo no le han asignado ningun subrol entonces tiene acceso a todo.
        if ($numSubRoles < 1) {
            return true;
        } else {
            $permitidos = array_intersect($paginas, $_SESSION["datosUsuario"]["sub_roles_paginas"]);

            if (!empty($permitidos)) {
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
        global $conexion, $config;

        $sql = "SELECT pp.*, md.* 
        FROM ".BD_ADMIN.".paginas_publicidad pp
        INNER JOIN ".BD_ADMIN.".instituciones_modulos im
            ON ipmod_modulo=pagp_modulo 
            AND ipmod_institucion='".$config['conf_id_institucion']."'
        INNER JOIN ".BD_ADMIN.".modulos md
            ON md.mod_id=im.ipmod_modulo
        WHERE pagp_id='".$idPaginaInterna."'
        UNION
        SELECT pp.*, md.* FROM ".BD_ADMIN.".paginas_publicidad pp
        INNER JOIN ".BD_ADMIN.".instituciones_paquetes_extras 
            ON paqext_institucion='".$config['conf_id_institucion']."' 
            AND paqext_id_paquete=pagp_modulo 
            AND paqext_tipo='".MODULOS."'
        INNER JOIN ".BD_ADMIN.".modulos md
            ON md.mod_id=pp.pagp_modulo
        WHERE 
            pagp_id='".$idPaginaInterna."'
        "; 

        $consultaPaginaActualUsuarios = mysqli_query($conexion, $sql);

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
    public static function validarModulosActivos($conexion, $modulo) {

        try{
            $consultaModulo = mysqli_query($conexion, "SELECT mod_estado FROM ".BD_ADMIN.".modulos 
            WHERE 
                mod_id='".$modulo."' 
            AND mod_types_customer LIKE '%".$_SESSION["datosUnicosInstitucion"]['ins_tipo']."%'
            ");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numDatosModulo = mysqli_num_rows($consultaModulo);
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