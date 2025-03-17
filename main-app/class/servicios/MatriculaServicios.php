<?php
require_once("Servicios.php");
class MatriculaServicios 
{
  /**
     * Lista las matrículas académicas con información adicional.
     *
     * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
     *
     * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
     */
    public static function listar($parametrosArray = null)
    {
      $sqlInicial = "SELECT * FROM " . BD_ACADEMICA . ".academico_matriculas";
      if ($parametrosArray && count($parametrosArray) > 0) {
        $parametrosValidos = array('mat_grado', 'mat_grupo', 'mat_tipo_matricula', 'mat_acudiente', 'institucion', 'year');
        $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
      };
      $sqlFinal = "";
      $sql = $sqlInicial . $sqlFinal;
      return Servicios::SelectSql($sql);
    }

    /**
     * Consulta la información de una matrícula específica.
     *
     * @param int $idDato Identificador de la matrícula.
     *
     * @return array|false Arreglo con la información de la matrícula o false si hay un error.
     */
    public static function consultar($idDato = 1)
    {
        global $config;
        return Servicios::getSql("SELECT * FROM " . BD_ACADEMICA . ".academico_matriculas WHERE mat_id='" . $idDato."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
    }

      /**
     * Consulta la información de una matrícula específica.
     *
     * @param int $documento numero del documento de la matrícula.
     *
     * @return array|false Arreglo con la información de la matrícula o false si hay un error.
     */
    public static function consultarDocumento($documento = 1,$institucion = null, $year = null)
    {
      global $config;
      $institucion = empty($institucion) ? $config['conf_id_institucion'] : $institucion;
      $year = empty($year) ? $config['conf_agno'] : $year;
      return Servicios::getSql("SELECT * FROM " . BD_ACADEMICA . ".academico_matriculas WHERE mat_documento ='" . $documento."' AND institucion={$institucion} AND year={$year} ");
    }

    /**
     * Lista los estudiantes por nombre.
     *
     * @param string $nombre Nombre del estudiante.
     *
     * @return array|false Arreglo con la información de los estudiantes que coinciden con el nombre o false si hay un error.
     */
    public static function listarEstudianteNombre($nombre = '')
    {
      global $config;
      $sql = "SELECT mat_id,mat_primer_apellido,mat_segundo_apellido,mat_nombres,mat_nombre2 FROM " . BD_ACADEMICA . ".academico_matriculas
      WHERE mat_eliminado IN (0) AND institucion={$config['conf_id_institucion']} AND year = '{$_SESSION["bd"]}' AND (
      mat_nombres LIKE '%" . $nombre . "%' OR
      mat_nombre2 LIKE '%" . $nombre . "%' OR
      mat_primer_apellido LIKE '%" . $nombre . "%' OR
      mat_segundo_apellido LIKE '%" . $nombre . "%' OR
      CONCAT(TRIM(mat_nombres),TRIM(mat_nombre2),TRIM(mat_primer_apellido),TRIM(mat_segundo_apellido)) LIKE '%" . $nombre . "%' OR
      CONCAT(TRIM(mat_nombres),' ',TRIM(mat_nombre2),' ',TRIM(mat_primer_apellido),' ',TRIM(mat_segundo_apellido)) LIKE '%" . $nombre . "%' OR
      CONCAT(TRIM(mat_primer_apellido),TRIM(mat_segundo_apellido),TRIM(mat_nombres),TRIM(mat_nombre2)) LIKE '%" . $nombre . "%' OR
      CONCAT(TRIM(mat_primer_apellido),' ',TRIM(mat_segundo_apellido),' ',TRIM(mat_nombres),' ',TRIM(mat_nombre2)) LIKE '%" . $nombre . "%')
      ORDER BY mat_grado, mat_grupo, mat_primer_apellido, mat_segundo_apellido, mat_nombres";
      return Servicios::SelectSql($sql, 'LIMIT 5');
    }

    /**
     * Obtiene los apellidos del estudiante.
     *
     * @param array $matricula Datos de la matrícula.
     *
     * @return string Apellidos del estudiante en mayúsculas.
     */
    public static function apellidos($matricula)
    {
        return strtoupper($matricula['mat_primer_apellido'] . " " . $matricula['mat_segundo_apellido']);
    }

    /**
     * Obtiene los nombres del estudiante.
     *
     * @param array $matricula Datos de la matrícula.
     *
     * @return string Nombres del estudiante en mayúsculas.
     */
    public static function nombres($matricula)
    {
        return strtoupper($matricula['mat_nombres'] . " " . $matricula['mat_nombre2']);
    }

    /**
     * Obtiene el nombre completo del estudiante.
     *
     * @param array $matricula Datos de la matrícula.
     *
     * @return string Nombre completo del estudiante en mayúsculas.
     */
    public static function nombreCompleto($matricula)
    {
        return MatriculaServicios::apellidos($matricula) . " " . MatriculaServicios::nombres($matricula);
    }

}

