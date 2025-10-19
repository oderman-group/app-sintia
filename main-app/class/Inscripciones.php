<?php
require_once("servicios/Servicios.php");
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once(ROOT_PATH."/main-app/class/documentManager.php");


class Inscripciones extends BindSQL{

    public const MAXIMO_PESO_ARCHIVO_MB = 5 * 1024 * 1024;
    public const ARCHIVOS_PERMITIDOS    = ['pdf', 'jpg', 'png', 'docx', 'txt', 'xls', 'xlsx', 'jpeg', 'zip', 'rar', 'gz'];


        /**
     * Lista todas  las Inscripciones con información adicional.
     *
     * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
     *
     * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
     */
    public static function listarTodos($parametrosArray = null)
    {
        global $config;
        if(empty($parametrosArray["institucion"])){
            $institucion=$config['conf_id_institucion'];
        }
        if(empty($parametrosArray["year"])){
            $year=$_SESSION["bd"];
        }
        $busqueda='';
        $sqlFinal ='';
        if(!empty($parametrosArray["valor"])){
            $busqueda=$parametrosArray["valor"];
            $sqlFinal = " AND (
                mat_id LIKE '%" . $busqueda . "%' 
                OR mat_nombres LIKE '%" . $busqueda . "%' 
                OR mat_nombre2 LIKE '%" . $busqueda . "%' 
                OR mat_primer_apellido LIKE '%" . $busqueda . "%' 
                OR mat_segundo_apellido LIKE '%" . $busqueda . "%' 
                OR mat_documento LIKE '%" . $busqueda . "%' 
                OR mat_email LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_segundo_apellido), TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombres), '', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_primer_apellido), '', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
                OR gra_nombre LIKE '%" . $busqueda . "%'
                OR asp_email_acudiente LIKE '%" . $busqueda . "%'
                OR asp_nombre_acudiente LIKE '%" . $busqueda . "%'
                OR asp_nombre LIKE '%" . $busqueda . "%'
                OR asp_documento_acudiente LIKE '%" . $busqueda . "%'              
            )";
        }
      $sqlFiltro ='';
      if(!empty($parametrosArray["filtro"])){
        $sqlFiltro =$parametrosArray["filtro"];
      }
      $sqlInicial ="SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
                    INNER JOIN ".BD_ADMISIONES.".aspirantes ON asp_id=mat_solicitud_inscripcion
                    LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=asp_grado AND gra.institucion={$institucion} AND gra.year={$year}
                    WHERE mat_estado_matricula=5 AND mat.institucion={$institucion} AND mat.year={$year} ".$sqlFinal." ".$sqlFiltro." 
                    ORDER BY mat_primer_apellido";     
      $sql = $sqlInicial ;
      return Servicios::SelectSql($sql);
    }
    /**
     * Este metodo me busca la configuración de la institución para admisiones
     * @param mysqli $conexion
     * @param string $baseDatosAdmisiones
     * @param int $idInsti
     * @param int $year
     * 
     * @return array $resultado
    **/
    public static function configuracionAdmisiones($conexion, $baseDatosAdmisiones, $idInsti, $year){
        $resultado = [];

        try {
            $configConsulta = mysqli_query($conexion,"SELECT * FROM {$baseDatosAdmisiones}.config_instituciones WHERE cfgi_id_institucion = ".$idInsti." AND cfgi_year = ".$year);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($configConsulta, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo trae los documentos de un inscrito
     * @param PDO $conexionPDO
     * @param array $config
     * @param string $id
     * @param string $year
     * 
     * @return array $datos
    **/
    public static function traerDocumentos( PDO $conexionPDO, array $config, string $id, string $year= ""){

        try {

            //Documentos
            $documentosQuery = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas_documentos
            WHERE matd_matricula = :id
            AND institucion= :idInstitucion
            AND year= :year
            ";
            $documentos = $conexionPDO->prepare($documentosQuery);
            if (!$documentos) {
                error_log("Error al preparar consulta de documentos: " . implode(", ", $conexionPDO->errorInfo()));
                throw new Exception("Error al preparar la consulta.");
            }

            $documentos->bindParam(':id', $id, PDO::PARAM_STR);
            $documentos->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $documentos->bindParam(':year', $year, PDO::PARAM_STR);

            if ($documentos->execute()) {
                $datosDocumentos = $documentos->fetch();
                error_log("Consulta de documentos ejecutada exitosamente para matricula {$id}, institucion {$config['conf_id_institucion']}, year {$year}. Resultados: " . ($datosDocumentos ? 'Encontrados' : 'No encontrados'));
                return $datosDocumentos;
            } else {
                $errorInfo = $documentos->errorInfo();
                error_log("Error al ejecutar consulta de documentos: " . implode(", ", $errorInfo));
                throw new Exception("Error al ejecutar la consulta.");
            }
        } catch (Exception $e) {
            error_log("Excepción en traerDocumentos: " . $e->getMessage());
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo actualiza los documentos de un inscrito
     * @param PDO $conexionPDO
     * @param array $config
     * @param array $FILES
     * @param array $POST
     * 
     * @return array $documentos
    **/
    public static function actualizarDocumentos(
        PDO $conexionPDO, 
        array $config, 
        array $FILES, 
        array $POST, 
        string $year = ""
    ): ?PDOStatement {
        
        // Inicialización de variables para la consulta SQL
        $pazysalvo = $POST['pazysalvoA'] ?? null;
        $observador = $POST['observadorA'] ?? null;
        $eps = $POST['epsA'] ?? null;
        $recomendacion = $POST['recomendacionA'] ?? null;
        $vacunas = $POST['vacunasA'] ?? null;
        $boletines = $POST['boletinesA'] ?? null;
        $documentoIde = $POST['documentoIdeA'] ?? null;
        $certificado = $POST['certificadoA'] ?? null;
        $cartaLaboral = $POST['cartaLaboralA'] ?? null;

        // Directorio base de destino para todos los archivos
        $destino_base = ROOT_PATH . '/main-app/admisiones/files/otros';

        try {

            // ===============================================
            // PROCESAMIENTO DE ARCHIVOS
            // ===============================================

            // PAZ Y SALVO
            if (!empty($FILES['pazysalvo']['name'])) {
                $pazysalvo_nuevo = DocumentManager::processUploadedFile($FILES['pazysalvo'], $destino_base, 'pyz', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($pazysalvo_nuevo !== null) {
                    $pazysalvo = $pazysalvo_nuevo;
                }
            }

            // OBSERVADOR
            if (!empty($FILES['observador']['name'])) {
                $observador_nuevo = DocumentManager::processUploadedFile($FILES['observador'], $destino_base, 'obs', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($observador_nuevo !== null) {
                    $observador = $observador_nuevo;
                }
            }

            // EPS
            if (!empty($FILES['eps']['name'])) {
                $eps_nuevo = DocumentManager::processUploadedFile($FILES['eps'], $destino_base, 'eps', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($eps_nuevo !== null) {
                    $eps = $eps_nuevo;
                }
            }

            // RECOMENDACIÓN
            if (!empty($FILES['recomendacion']['name'])) {
                $recomendacion_nuevo = DocumentManager::processUploadedFile($FILES['recomendacion'], $destino_base, 'rec', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($recomendacion_nuevo !== null) {
                    $recomendacion = $recomendacion_nuevo;
                }
            }

            // TARJETA DE VACUNAS
            if (!empty($FILES['vacunas']['name'])) {
                $vacunas_nuevo = DocumentManager::processUploadedFile($FILES['vacunas'], $destino_base, 'vac', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($vacunas_nuevo !== null) {
                    $vacunas = $vacunas_nuevo;
                }
            }

            // BOLETINES
            if (!empty($FILES['boletines']['name'])) {
                $boletines_nuevo = DocumentManager::processUploadedFile($FILES['boletines'], $destino_base, 'bol', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($boletines_nuevo !== null) {
                    $boletines = $boletines_nuevo;
                }
            }

            // DOCUMENTO DE IDENTIDAD
            if (!empty($FILES['documentoIde']['name'])) {
                $documentoIde_nuevo = DocumentManager::processUploadedFile($FILES['documentoIde'], $destino_base, 'doc', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($documentoIde_nuevo !== null) {
                    $documentoIde = $documentoIde_nuevo;
                }
            }

            // CERTIFICADO DE ESTUDIOS
            if (!empty($FILES['certificado']['name'])) {
                $certificado_nuevo = DocumentManager::processUploadedFile($FILES['certificado'], $destino_base, 'cert', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($certificado_nuevo !== null) {
                    $certificado = $certificado_nuevo;
                }
            }
            
            // CARTA LABORAL
            if (!empty($FILES['cartaLaboral']['name'])) {
                $cartaLaboral_nuevo = DocumentManager::processUploadedFile($FILES['cartaLaboral'], $destino_base, 'lab', DocumentManager::MAXIMO_PESO_ARCHIVO_BYTES);
                if ($cartaLaboral_nuevo !== null) {
                    $cartaLaboral = $cartaLaboral_nuevo;
                }
            }

            // ===============================================
            // EJECUCIÓN DE LA CONSULTA SQL
            // ===============================================

            $documentosQuery = "UPDATE ".BD_ACADEMICA.".academico_matriculas_documentos SET
            matd_pazysalvo = :pazysalvo, 
            matd_observador = :observador, 
            matd_eps = :eps, 
            matd_recomendacion = :recomendacion, 
            matd_vacunas = :vacunas, 
            matd_boletines_actuales = :boletines,
            matd_documento_identidad = :documentoIde,
            matd_certificados = :certificado,
            matd_carta_laboral  = :cartaLaboral
            WHERE matd_matricula = :idMatricula AND institucion= :idInstitucion AND year= :year";
            $documentos = $conexionPDO->prepare($documentosQuery);

            $documentos->bindParam(':idMatricula', $POST['idMatricula'], PDO::PARAM_STR);
            $documentos->bindParam(':pazysalvo', $pazysalvo, PDO::PARAM_STR);
            $documentos->bindParam(':observador', $observador, PDO::PARAM_STR);
            $documentos->bindParam(':eps', $eps, PDO::PARAM_STR);
            $documentos->bindParam(':vacunas', $vacunas, PDO::PARAM_STR);
            $documentos->bindParam(':boletines', $boletines, PDO::PARAM_STR);
            $documentos->bindParam(':documentoIde', $documentoIde, PDO::PARAM_STR);
            $documentos->bindParam(':recomendacion', $recomendacion, PDO::PARAM_STR);
            $documentos->bindParam(':certificado', $certificado, PDO::PARAM_STR);
            $documentos->bindParam(':cartaLaboral', $cartaLaboral, PDO::PARAM_STR);
            $documentos->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $documentos->bindParam(':year', $year, PDO::PARAM_STR);

            if ($documentos) {
                $documentos->execute();
                return $documentos;
            } else {
                // Esto solo ocurriría si PDO::prepare falla, lo cual ya se maneja por el catch.
                throw new Exception("Error al preparar la consulta.");
            }

        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return null;
        }
    }

    /**
     * Este metodo guarda los documentos de un inscrito
     * @param PDO $conexionPDO
     * @param array $config
     * @param string $id
     * 
     * @return string $codigo
    **/
    public static function guardarDocumentos( PDO $conexionPDO, array $config, string $id){

        try {

            //Documentos
            $documentosQuery = "INSERT INTO ".BD_ACADEMICA.".academico_matriculas_documentos(matd_id, matd_matricula, institucion, year)VALUES(:codigo, :matricula, :idInstitucion, :year)";
            $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_matriculas_documentos');
            $documentos = $conexionPDO->prepare($documentosQuery);
            $documentos->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $documentos->bindParam(':matricula', $id, PDO::PARAM_STR);
            $documentos->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $documentos->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);

            if ($documentos) {
                $documentos->execute();
                return $codigo;
            } else {
                throw new Exception("Error al preparar la consulta.");
            }
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo elimina los documentos de un inscrito
     * @param mysqli $conexion
     * @param array $config
     * @param string $id
    **/
    public static function eliminarDocumentos(mysqli $conexion, array $config, string $id)
    {
        $sql = "UPDATE " . BD_ACADEMICA . ".academico_matriculas_documentos SET matd_fecha_eliminados=now(), matd_usuario_elimados=? WHERE matd_matricula=? AND institucion=? AND year=?";

        $parametros = [$_SESSION["id"], $id, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Actualiza el estado de un aspirante a oculto.
     *
     * @param string $aspiranteId El ID del aspirante que se desea actualizar.
     *
     * @return bool Retorna true si la actualización fue exitosa, de lo contrario false.
     */
    public static function actualizarEstadoAspirante(string $aspiranteId): bool {

        $conexionPDO = Conexion::newConnection('PDO');

        $query = "UPDATE ".BD_ADMISIONES.".aspirantes SET asp_oculto = ".BDT_Aspirante::ESTADO_OCULTO_VERDADERO." 
        WHERE asp_id=:aspirante";
        $pdo = $conexionPDO->prepare($query);

        $pdo->bindParam(':aspirante', $aspiranteId, PDO::PARAM_STR);

        if ($pdo) {
            $pdo->execute();
            return true;
        } else {
            return false;
        }

    }

}