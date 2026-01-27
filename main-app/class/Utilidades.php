<?php
require_once(dirname(__DIR__, 2) . '/config-general/constantes.php');
require_once ROOT_PATH."/main-app/class/Conexion.php";
class Utilidades {

    public const CRUD_CREATE      = 'CREATE';
    public const CRUD_READ        = 'READ';
    public const CRUD_UPDATE      = 'UPDATE';
    public const CRUD_DELETE      = 'DELETE';
    public const CRUD_DESCONOCIDO = 'DESCONOCIDO';

/**
 * Valida si el resultado de una consulta SQL o un array está vacío.
 *
 * @param mixed $data Puede ser un resultado de consulta SQL o un array.
 * @return void manda mensjae informativo si no cumple las condiciones
 */
    public static  function validarInfoBoletin($data)
    {   
        global $datosUsuarioActual;
        $url= UsuariosPadre::verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'page-info.php?idmsg=306');
        // Si es un array, validar si está vacío
        if (is_array($data)) {
            if(empty($data)){
                echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';                    
                exit();  
            }
        }
           
        if ($data instanceof mysqli_result) {
            // Si el número de filas es 0, significa que la consulta está vacía
           if($data->num_rows === 0){
           echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';                   
            exit();  
           }
        }
    }
/**
 * Validar los parámetros GET recibidos y redirigir si no son válidos.
 *
 * Esta función valida los parámetros recibidos en la variable GET ($get) asegurándose
 * de que ninguno de ellos sea nulo y que todos estén codificados en base64. Si algún
 * parámetro no cumple con estos requisitos, se redirige al usuario a una página de 
 * información de error.
 *
 * @param array $get Array que contiene los parámetros GET a validar.
 * @param array $parametrosObligatorios si viene lleno los parametros declarados seran obligatorios
 * 
 * @return void Si algún parámetro no es válido, la función termina la ejecución del script
 *              y redirige al usuario a una página de error.
 *
 * @example
 * // Ejemplo de uso en una página PHP:
 * validarParametros($_GET);
 * 
 * @throws Redireccion a 'page-info.php?idmsg=303' si un parámetro no es válido.
 */
    public static  function validarParametros($get,$parametrosObligatorios=[])
    {
        if (isset($get)) {
            
            $faltaObligatorio=false;
            foreach ($parametrosObligatorios as $parametro) {
            // validamos que existen los parametros obligatorios
                if (!isset($get[$parametro])) {
                    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=307";</script>';                    
                    exit();                  
                }
            }
                

            foreach ($get as $key => $value) {
                // validammos que los parametros no sean null y sea base64  excluyendo cuando la llave sea success , error,summary  y validamos que el resultado es codificado es  alfanumerico   
                if ($key != 'success' && $key != 'error' &&  $key != 'summary' &&  $key != 'busqueda' && $key != 'page' && !empty($value) && (!self::esBase64($value) || !self::esAlfanumerico(base64_decode($value)))) {
                    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=307";</script>';
                    exit();
                }
            }
        }
    }

 /**
 * Verifica si una cadena es una codificación válida en base64.
 *
 * Esta función toma una cadena como entrada y realiza una serie de comprobaciones
 * para determinar si la cadena está codificada en base64 de forma válida. Se realizan
 * comprobaciones de longitud, contenido y la capacidad de codificación/decodificación
 * para validar la cadena.
 *
 * @param string $string La cadena a verificar.
 * 
 * @return bool Devuelve `true` si la cadena es una codificación válida en base64,
 *              de lo contrario, devuelve `false`.
 * 
 * @example
 * // Ejemplo de uso:
 * $esValido = esBase64('VGhpcyBpcyBhIHZhbGlkIGJhc2U2NCBlbmNvZGVkIHN0cmluZw==');
 * 
 */    public static function esBase64($string){  
        // Primero, verifica si la longitud de la cadena es un múltiplo de 4
        if (strlen($string) % 4 != 0) {
            return false;
        }
        // Verifica si el contenido solo contiene caracteres válidos para base64        
        if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string) == false) {
            return false;
        }
        // Decodifica la cadena        
        $decoded = base64_decode($string, true);
        // Verifica si la cadena puede ser decodificada y recodificada        
        if ($decoded === false) {
            return false;
        }
        // Verifica si la cadena original coincide con la recodificada        
        if (base64_encode($decoded) !== $string) {
            return false;
        }
        return true;
    
    }
    /**
     * Verifica si una cadena es una codificación válida en alfanumerica.
     *
     * Esta función toma una cadena como entrada y realiza una serie de comprobaciones
     * para determinar si la cadena es alfanumerica forma válida.
     *
     * @param string $valor La cadena a verificar.
     * 
     * @return bool Devuelve `true` si la cadena es una codificación válida en base64,
     *              de lo contrario, devuelve `false`.
     * 
     * @example
     * // Ejemplo de uso:
     * $esValido = esAlfanumerico('nombre123');
     *  */
    public static function esAlfanumerico($valor) {
        return preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ,-_:.!\'@()\s]+$/', $valor);
    }

    /**
     * Obtiene una representación de cadena de un valor.
     *
     * Verifica si el valor proporcionado no es nulo y devuelve el valor como una cadena.
     * En caso de que el valor sea nulo, devuelve una cadena vacía.
     *
     * @param mixed $valor - El valor que se desea convertir a cadena.
     *
     * @return string - Representación de cadena del valor o cadena vacía si el valor es nulo.
     *
     * @example
     * ```php
     * // Ejemplo de uso para obtener la representación de cadena de un valor
     * $resultado = getToString($miVariable);
     * // $resultado contendrá la representación de cadena de $miVariable o una cadena vacía si $miVariable es nulo.
     * ```
     */
    public static  function getToString($valor)
    {   
        // validammos que las variables no sean null 
        if (isset($valor)) {
            return  $valor;
        }else{
            return "";
        }
    
    }

    /**
     * Comprueba si un archivo existe en la ruta especificada.
     *
     * Esta función verifica si el archivo especificado por la ruta existe en el sistema de archivos.
     *
     * @param string $ruta La ruta completa al archivo que se va a comprobar.
     *
     * @return bool Devuelve true si el archivo existe, o false en caso contrario.
     */
    public static  function ArchivoExiste($ruta)
    {   
        if ( file_exists($ruta) ) {
            return  true;
        }

        return false;
    
    }

    /**
     * Generates a unique code based on a given index and a combination of numbers and the current timestamp.
     *
     * @param string $index An optional index to prepend to the generated code.
     * @return string The generated unique code.
     */
    public static function generateCode($index='')
    {
        return !empty($index) ? uniqid($index.'-') : uniqid();
        //return $index."-".self::guidv4();
    }

    /**
     * Generates a version 4 UUID.
     *
     * This function generates a universally unique identifier (UUID) according to RFC 4122,
     * version 4. The UUID generated is based on random or pseudo-random numbers, depending on
     * the availability of the `random_bytes` function in PHP. The version 4 UUID is composed of
     * random digits, with certain bits fixed to indicate the version and variant of the UUID.
     *
     * @param string|null $data Optional. Provide 16 bytes of binary data to use for the UUID generation
     *                          instead of generating random data. Primarily used for testing purposes.
     *
     * @return string Returns a string representation of the UUID, which is 36 characters long,
     *                including four hyphens.
     *
     * @throws Exception If generating random bytes fails.
     *
     * @example
     * echo guidv4();
     * // Output: a randomly generated version 4 UUID, such as "f47ac10b-58cc-4372-a567-0e02b2c3d479".
     */
    public static function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Obtiene el nombre de la página desde una URL y devuelve su versión codificada en base64.
     *
     * Esta función extrae el nombre de la página PHP de una URL dada. Si el nombre de la página
     * se encuentra, se codifica en base64 antes de devolverlo. Esto puede ser útil para manejar
     * referencias a páginas de manera segura o discreta.
     *
     * @param string $url La URL completa desde la cual extraer el nombre de la página.
     * @return string|null El nombre de la página codificado en base64 si se encuentra un archivo .php, 
     *                     de lo contrario, null si la URL está vacía o no contiene un archivo .php.
     */
    public static function getPageFromUrl($url) {
        if (!empty($url)) {
            $urlArray = explode("/", $url);
            $page     = end($urlArray);

            if(strpos($page, '.php')) {
                return base64_encode($page);
            }
        }
    }

    /**
     * Obtiene el directorio de usuario codificado en base64 desde una URL.
     *
     * Analiza la URL proporcionada para determinar si contiene uno de los directorios válidos
     * especificados. Si se encuentra un directorio válido, se devuelve su nombre codificado en base64.
     * Los directorios válidos están definidos en la lista $directoriosValidos. Si la URL no contiene
     * un directorio válido o si está vacía, la función no devuelve nada.
     *
     * @param string $url La URL de la cual extraer el directorio de usuario.
     * @return string|null El directorio de usuario codificado en base64 si es válido, o null si no lo es.
     */
    public static function getDirectoryUserFromUrl($url) {
        if (!empty($url)) {
            $directoriosValidos = [
                'directivo', 'docente', 'acudiente', 'estudiante'
            ];
            $urlArray     = explode("/", $url);
            $cantElements = count($urlArray);

            if ($cantElements > 2) {
                $cantElements = $cantElements  - 2;
            }

            $directorio = $urlArray[$cantElements];

            if (in_array($directorio, $directoriosValidos)) {
                return base64_encode($directorio);
            }
        }
    }

    /**
     * Obtiene el próximo valor AUTO_INCREMENT de una tabla específica en una base de datos.
     *
     * Este método consulta la tabla `information_schema.tables` para obtener el próximo
     * valor AUTO_INCREMENT de la tabla especificada en la base de datos dada. Es útil para
     * prever el próximo ID que se generará al insertar un nuevo registro en una tabla que
     * utiliza AUTO_INCREMENT.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos MySQLi.
     * @param string $bd Nombre de la base de datos donde se encuentra la tabla.
     * @param string $table Nombre de la tabla para la cual se desea obtener el próximo AUTO_INCREMENT.
     * @return int|null El próximo valor AUTO_INCREMENT de la tabla, o null si no se encuentra o hay un error.
     */
    public static function getNextIdSequence($conexionPDO, $bd, $table) {

        if (empty($table) || empty($bd)) {
            throw new InvalidArgumentException('El nombre de la tabla y/o la bd no pueden estar vacíos');
            return null;
        }

        if (!$conexionPDO instanceof PDO || empty($conexionPDO)) {
            $conexionPDO = Conexion::newConnection('PDO');
        }

        global $config;

        $query = "SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_schema = :bd AND table_name = :table";

        // Preparamos la consulta
        $stmt = $conexionPDO->prepare($query);

        try {
            if ($stmt) {
                // Ejecutamos la consulta pasando los parámetros necesarios
                $stmt->execute(['bd' => $bd, 'table' => $table]);
                
                // Obtenemos el primer (y único) resultado
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $idInstitution = !empty($config['conf_id_institucion']) ? $config['conf_id_institucion'] : null;
                $idUser        = !empty($_SESSION["id"])                ? $_SESSION["id"]                : null;

                $tablePrefix = self::getPrefixFromTableName($table);

                if ($row['AUTO_INCREMENT'] <= 1 || is_null($row['AUTO_INCREMENT'])) {
                    $queryMax = "SELECT MAX(id_nuevo) AS nextId FROM {$bd}.{$table};";
                    $stmtMax = $conexionPDO->prepare($queryMax);

                    if ($stmtMax) {
                        $stmtMax->execute();
                        $rowMax = $stmtMax->fetch(PDO::FETCH_ASSOC);
                        $rowMax['nextId'] ++;
                        return !empty($rowMax['nextId']) ? $tablePrefix . $rowMax['nextId'] . $idInstitution . $idUser : self::generateCode(null);
                    }
                }
                
                // Devolvemos el valor AUTO_INCREMENT o null si no se encontró
                return !empty($row['AUTO_INCREMENT']) ? $tablePrefix . $row['AUTO_INCREMENT'] . $idInstitution . $idUser : self::generateCode(null);
            } else {
                return self::generateCode(null);
            }
        } catch (PDOException $e) {
            return self::generateCode(null);
        }
    }

    /**
     * Obtiene un prefijo basado en el nombre de una tabla.
     *
     * Este método analiza el nombre de una tabla proporcionada como argumento para extraer
     * un prefijo. El prefijo se deriva de la primera parte del nombre de la tabla (asumiendo
     * que el nombre de la tabla sigue un formato que incluye al menos un guion bajo "_"). Si
     * el nombre de la tabla contiene guiones bajos, el método devuelve las primeras tres letras
     * de la segunda palabra en el nombre de la tabla. Si el nombre de la tabla no contiene
     * guiones bajos, simplemente devuelve las primeras tres letras del nombre de la tabla.
     * El prefijo se devuelve en mayúsculas.
     *
     * @param string $table El nombre de la tabla de la cual se desea obtener el prefijo.
     * @return string|null El prefijo derivado del nombre de la tabla en mayúsculas, o null si el nombre de la tabla está vacío.
     */
    public static function getPrefixFromTableName($table) {

        if (empty($table)) {
            return null;
        }
    
        // Divide el nombre de la tabla en partes basado en "_" y toma la palabra relevante.
        $parts = explode("_", $table);
        $word  = count($parts) > 2 ? $parts[2] : (count($parts) > 1 ? $parts[1] : $parts[0]);
    
        // Retorna las primeras tres letras de la palabra seleccionada en mayúsculas.
        return strtoupper(substr($word, 0, 3));

    }

    /**
     * Adds a trailing zero to an integer value.
     *
     * This function checks if the provided value is an integer. If it is, the function appends a trailing ".0" to the integer.
     * If the provided value is not an integer, the function returns the value as it is.
     *
     * @param mixed $nota The value to check and potentially modify.
     *
     * @return mixed The modified value with a trailing ".0" if the original value was an integer, or the original value if it was not an integer.
     */
    public static function setFinalZero($nota) {
        if (is_numeric($nota) && strlen($nota) == 1) {
            return $nota.".0";
        }

        return $nota;
    }

    /**
     * Logs an error to the database and returns the ID of the inserted error report.
     *
     * This function captures details of an exception, sanitizes the data, and inserts it into the `reporte_errores` table.
     * It logs various details such as the error code, line number, user information, and request data.
     *
     * @param Exception $e The exception object containing the error details to be logged.
     *
     * @return int The ID of the inserted error report in the database.
     */
    public static function logError(Exception $e): int
    {
        $numError     = $e->getCode();
        $lineaError   = $e->getLine();
        $aRemplezar   = array("'", '"', "#", "´");
        $enRemplezo   = array("\'", "\"", "\#", "\´");
        $detalleError = str_replace($aRemplezar, $enRemplezo, $e->getMessage());
        $request_data = json_encode($_REQUEST);

        global $conexion;
        global $baseDatosServicios;
        global $config;

        $request_data_sanitizado = mysqli_real_escape_string($conexion, $request_data);

        $_SESSION["id"] ??=null;
        $_SESSION["bd"] ??=date("Y");

        mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".reporte_errores(rperr_numero, rperr_fecha, rperr_ip, rperr_usuario, rperr_pagina_referencia, rperr_pagina_actual, rperr_so, rperr_linea, rperr_institucion, rperr_error, rerr_request, rperr_year, rperr_trace_php)
        VALUES('".$numError."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SESSION["id"]."', '".$_SERVER['HTTP_REFERER']."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$lineaError."', '".$config['conf_id_institucion']."','".$detalleError."', '".$request_data_sanitizado."', '".$_SESSION["bd"]."', '".json_encode(debug_backtrace())."')");

        $idReporteError = mysqli_insert_id($conexion);

        return $idReporteError;
    }

    /**
     * Escribe un registro detallado en el archivo de logs del sistema.
     *
     * El archivo se crea (si no existe) dentro de la carpeta /Logs, localizada
     * dentro del directorio MAIN_PATH. El nombre del archivo de log tiene el
     * siguiente formato:
     *
     *     log_<NombreDeLaClase>.log
     *
     * De esta forma, cada clase genera su propio archivo de log independiente.
     *
     * El contenido de cada línea incluye:
     *   - Fecha y hora en formato "YYYY-MM-DD HH:MM:SS"
     *   - ID de transacción único por petición HTTP
     *   - Usuario que ejecuta (de $_SESSION['datosUsuario']['uss_usuario'])
     *   - ID de institución (de $_SESSION['idInstitucion'])
     *   - Año académico (de $_SESSION['bd'])
     *   - Clase y método donde se ejecutó el writeLog
     *   - Caller: quién llamó a la función que ejecutó el writeLog (clase::método@línea)
     *   - Mensaje de log recibido por parámetro
     *   - Línea y archivo donde se ejecutó el writeLog
     *
     * Ejemplo de línea en un log:
     *
     *   [2025-11-09 08:12:41] [TX-691081070d7f4] [Usuario:admin] [Inst:1] [Year:2025]
     *   [Boletin::generarBoletin] [caller:ajax-generar-boletin.php@45] Error al generar reporte
     *   [linea:335] [archivo:C:\xampp\htdocs\app-sintia\main-app\class\Boletin.php]
     *
     * @param string $mensaje Mensaje o excepción que se desea registrar en el log.
     *
     * @return void
     */
    public static function writeLog($mensaje)
    {
        // ─────────────────────────────────────────────────────────────
        // 1) ID DE TRANSACCIÓN: se genera una sola vez por petición HTTP
        // ─────────────────────────────────────────────────────────────
        if (!isset($GLOBALS['LOG_TRANSACTION_ID'])) {
            // puedes cambiar uniqid() por random_bytes si quieres algo más largo
            $GLOBALS['LOG_TRANSACTION_ID'] = uniqid('TX-');
        }
        $txId = $GLOBALS['LOG_TRANSACTION_ID'];

        // Carpeta de logs
        $logDir = MAIN_PATH . '/Logs/Class';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $usuario     = isset($_SESSION['datosUsuario']['uss_usuario']) ? $_SESSION['datosUsuario']['uss_usuario'] : 'SIN_SESION';
        $institucion = isset($_SESSION['idInstitucion']) ? $_SESSION['idInstitucion'] : 'N/A';
        $year        = isset($_SESSION['bd']) ? $_SESSION['bd'] : 'N/A';
        $fecha       = date('Y-m-d H:i:s');

        // Backtrace completo para saber desde dónde fue llamado
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        
        // Función/clase donde se hizo el writeLog
        $clase      = $trace[1]['class']   ?? 'Global';
        $funcion    = $trace[1]['function'] ?? 'N/A';
        $linea      = $trace[0]['line']    ?? 'N/A';
        $archivo    = $trace[0]['file']    ?? 'N/A';
        
        // Caller: quién llamó a la función que hizo el log
        $callerClase    = $trace[2]['class']    ?? '';
        $callerFuncion  = $trace[2]['function'] ?? '';
        $callerLinea    = $trace[1]['line']     ?? 'N/A';
        $callerArchivo  = $trace[1]['file']     ?? 'N/A';
        
        // Formatear caller
        $caller = '';
        if (!empty($callerClase) && !empty($callerFuncion)) {
            $caller = " [caller:{$callerClase}::{$callerFuncion}@{$callerLinea}]";
        } elseif (!empty($callerFuncion)) {
            $caller = " [caller:{$callerFuncion}@{$callerLinea}]";
        } elseif (!empty($callerArchivo)) {
            $archivoCorto = basename($callerArchivo);
            $caller = " [caller:{$archivoCorto}@{$callerLinea}]";
        }

        // Mensaje final con año, institución y caller
        $line = "[{$fecha}] [{$txId}] [Usuario:{$usuario}] [Inst:{$institucion}] [Year:{$year}] [{$clase}::{$funcion}]{$caller} {$mensaje}";
        $line .= " [linea:{$linea}] [archivo:{$archivo}]" . PHP_EOL;

        $logFile = $logDir . '/log_' . $clase . '.log';
        error_log($line, 3, $logFile);
    }

    /**
     * Asigna un valor por defecto a un campo que sea unullo o vacio.
     *
     * @param  string|double|float|bool|array $valorDefecto Puede ser un resultado de consulta SQL o un array.
     * @return string|double|float|bool|array manda mensjae informativo si no cumple las condiciones
     */
    public static  function valordefecto(&$valor,$valorDefecto="")
    {
        $valor=empty($valor)?$valorDefecto:$valor;
    }

    /**
     * Traduce un tipo de operación CRUD (Crear, Leer, Actualizar, Borrar) a su descripción en español con formato HTML.
     *
     * Este método estático toma una cadena de texto que representa una operación CRUD
     * y devuelve una cadena de texto HTML con la descripción correspondiente en español,
     * estilizada con clases de color para una mejor visualización.
     *
     * @param string $operacion La cadena de texto que representa la operación CRUD.
     * Los valores esperados son:
     * - 'READ': Para operaciones de lectura.
     * - 'UPDATE': Para operaciones de actualización.
     * - 'INSERT': Para operaciones de creación/inserción.
     * - 'DELETE': Para operaciones de eliminación.
     * Cualquier otro valor resultará en 'Desconocido'.
     *
     * @return string Una cadena de texto HTML con la descripción traducida de la operación.
     * Ejemplos de retorno:
     * - '<span class="text-info">Lectura</span>'
     * - '<span class="text-warning">Actualización</span>'
     * - '<span class="text-success">Creación</span>'
     * - '<span class="text-danger">Eliminación</span>'
     * - '<span class="text-secondary">Desconocido</span>'
     */
    public static function crudTranslation(string $operacion): string {
        switch($operacion) {
            case self::CRUD_CREATE:
                return '<span class="text-success">Creación</span>';
                break;

            case self::CRUD_READ;
                return '<span class="text-info">Lectura</span>';
                break;
            
            case self::CRUD_UPDATE:
                return '<span class="text-warning">Actualización</span>';
                break;


            case self::CRUD_DELETE:
                return '<span class="text-danger">Eliminación</span>';
                break;

            default;
                return '<span class="text-secondary">Desconocido</span>';
                break;
        }
    }

    /**
     * Sanitiza un texto para evitar problemas de codificación en MySQL
     * - Convierte a UTF-8 válido
     * - Elimina caracteres de control invisibles
     * - Aplica trim para remover espacios innecesarios
     *
     * @param string $texto Texto a sanitizar
     * @return string Texto limpio y seguro para almacenar en DB
     */
    public static function sanitizarTexto(string $texto): string {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
        $texto = preg_replace('/[\x00-\x1F\x7F]/u', '', $texto);
        return trim($texto);
    }

    /**
     * Sanitiza un número entero
     * - Convierte el valor a int
     * - Si no es numérico, devuelve 0
     *
     * @param mixed $valor Valor a sanitizar
     * @return int Entero validado
     */
    public static function sanitizarEntero($valor): int {
        return is_numeric($valor) ? (int)$valor : 0;
    }

    /**
     * Sanitiza un número decimal (float)
     * - Convierte a float
     * - Si no es numérico, devuelve 0.0
     *
     * @param mixed $valor Valor a sanitizar
     * @return float Número en formato float
     */
    public static function sanitizarDecimal($valor): float {
        return is_numeric($valor) ? (float)$valor : 0.0;
    }

    /**
     * Sanitiza un email
     * - Elimina caracteres no válidos
     * - Devuelve null si el correo no es válido
     *
     * @param string $email Correo electrónico a sanitizar
     * @return string|null Email válido o null si no lo es
     */
    public static function sanitizarEmail(string $email): ?string {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    /**
     * Sanitiza una fecha
     * - Verifica si cumple con el formato especificado
     * - Devuelve la fecha formateada o null si es inválida
     *
     * @param string $fecha Fecha a validar
     * @param string $formato Formato esperado (ej: 'Y-m-d')
     * @return string|null Fecha válida o null
     */
    public static function sanitizarFecha(string $fecha, string $formato = 'Y-m-d'): ?string {
        $d = DateTime::createFromFormat($formato, $fecha);
        return ($d && $d->format($formato) === $fecha) ? $fecha : null;
    }

    /**
     * Normaliza una fecha de entrada a Y-m-d para MySQL.
     * Acepta formatos habituales (Y-m-d, d-m-Y, d/m/Y, etc.).
     * Útil para filtrar fechas antes de insertar/actualizar en BD y evitar "Incorrect date value".
     *
     * @param string|mixed $valor Fecha en cualquier formato común (ej. '30-06-2018', '2018-06-30')
     * @return string|null|false Y-m-d si válida, null si vacía, false si no parseable
     */
    public static function normalizarFechaParaBD($valor) {
        $v = is_string($valor) ? trim($valor) : '';
        if ($v === '') {
            return null;
        }
        $formatos = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'm-d-Y', 'd.m.Y', 'Y/m/d'];
        foreach ($formatos as $fmt) {
            $d = DateTime::createFromFormat($fmt, $v);
            if ($d) {
                return $d->format('Y-m-d');
            }
        }
        $ts = strtotime($v);
        if ($ts !== false) {
            $d = new DateTime('@' . $ts);
            return $d->format('Y-m-d');
        }
        return false;
    }

    /**
     * Sanitiza un array asociativo (ej: $_POST)
     * - Aplica sanitización de texto a todos los valores tipo string
     *
     * @param array $data Array con datos a sanitizar
     * @return array Array limpio con los valores sanitizados
     */
    public static function sanitizarArray(array $data): array {
        return array_map(function($valor) {
            return is_string($valor) ? self::sanitizarTexto($valor) : $valor;
        }, $data);
    }

    /**
     * Redirige al usuario a una nueva página.
     *
     * Utiliza un encabezado HTTP 302 para realizar una redirección del lado del servidor.
     * Se asegura de limpiar cualquier salida en el buffer antes de enviar el encabezado.
     * La redirección es de tipo temporal.
     *
     * @param string $redirectPageUrl La URL de la página a la que se redirigirá al usuario.
     * @param int $messageCode El código de mensaje (ej. de advertencia o éxito) que se añadirá a la URL.
     * El valor por defecto es 2.
     * @param array $additionalParams Parametros adicionales que se necesiten enviar por URL de redirección
     * @return void
     */
    public static function redirect(string $redirectPageUrl, int $messageCode = 2, array $additionalParams = []) {
        // Limpia cualquier salida que se haya enviado antes.
        if (ob_get_contents()) {
            ob_end_clean();
        }

        $complement = null;

        // Construye los parametros adicioales para la URL final.
        if (!empty($additionalParams)) {
            foreach ($additionalParams as $key => $value) {
                $complement .= '&'.$key . '=' . $value;
            }
        }

        // Construye la URL de forma segura
        $finalUrl = $redirectPageUrl . '?idmsg=' . $messageCode . $complement;

        // Redirige usando el encabezado Location, estableciendo el código 302 explícitamente
        header("Location: " . $finalUrl, true, 302);
        
        // Termina la ejecución del script inmediatamente
        exit();
    }
}