<?php
require_once("servicios/Servicios.php");

class SocialReacciones extends Servicios
{

  /**
   * Consulta la cantidad de un npr en específico.
   *
   * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
   *
   * @return array|false Arreglo con la información del  o false si hay un error.
   */
  public static function consultar($parametrosArray = null)
  {
    global $config;
     $sqlInicial = "SELECT * FROM " . BD_ADMIN . ".social_noticias_reacciones npr
    INNER JOIN " . BD_GENERAL . ".usuarios uss ON uss_id=npr_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
    INNER JOIN " . BD_ADMIN . ".social_noticias sn ON not_id=npr_noticia";
    if ($parametrosArray && count($parametrosArray) > 0) {
      $parametrosValidos = array('npr_noticia', 'npr_usuario');
      $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
    };

    return Servicios::getSql($sqlInicial);
  }

  /**
   * Lista los reacciones con información adicional.
   *
   * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
   *
   * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
   */
  public static function listar($parametrosArray = null)
  {
    global $config;
    $sqlInicial = "SELECT * FROM " . BD_ADMIN . ".social_noticias_reacciones
    INNER JOIN " . BD_GENERAL . ".usuarios uss ON uss_id=npr_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}";
    if ($parametrosArray && count($parametrosArray) > 0) {
      $parametrosValidos = array('npr_noticia', 'npr_reaccion');
      $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
    };
    $sqlFinal = "ORDER BY npr_fecha DESC";
    $sql = $sqlInicial . $sqlFinal;
    return Servicios::SelectSql($sql);
  }

  /**
   * Realiza un conteo teniendo encuenta los parametros ingresados.
   *
   * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
   *
   * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
   */
  public static function contarReacciones($parametrosArray = null)
  {
    $sqlInicial = "SELECT npr_reaccion,count(npr_reaccion) as cantidad  FROM " . BD_ADMIN . ".social_noticias_reacciones";
    if ($parametrosArray && count($parametrosArray) > 0) {
      $parametrosValidos = array('npr_noticia');
      $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
    };
    $sqlFinal = "GROUP BY npr_reaccion ORDER BY npr_id DESC";
    $sql = $sqlInicial . $sqlFinal;
    return Servicios::SelectSql($sql);
  }

  
  /**
   * Realiza un conteo teniendo encuenta los parametros ingresados.
   *
   * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
   *
   * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
   */
  public static function contar($parametrosArray = null)
  {
    $sqlInicial = "SELECT Count(*) as cantidad FROM " . BD_ADMIN . ".social_noticias_reacciones";
    if ($parametrosArray && count($parametrosArray) > 0) {
      $parametrosValidos = array('npr_noticia');
      $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
    };
    $sqlFinal = "ORDER BY npr_id DESC";
    $sql = $sqlInicial . $sqlFinal;
    return Servicios::SelectSql($sql)[0]["cantidad"];
  }






  /**
   * elimina el reacciones.
   *
   * @param string $npr_id ID del comentario.
   *
   * @return void
   */
  public static function eliminar($npr_id)
  {
    Servicios::UpdateSql("DELETE FROM " . BD_ADMIN . ".social_noticias_reacciones  WHERE npr_id ='" . $npr_id . "'");
  }

  /**
   * Guarda la información de los reacciones.
   *
   * @param string $idnotica id de la notica a comentar
   * @param string $comentario Comentario de publicacion.
   * @param string $padre si el comentario es una respuesta de un comentario
   *
   * @return int|null Retorna el id del registro
   */
  public static function guardar($idnotica, $comentario, $padre = null)
  {
    global $config;
    $registro = Servicios::InsertSql(
      " INSERT INTO " . BD_ADMIN . ".social_noticias_reacciones(
          npr_usuario, 
          npr_noticia,
          npr_comentario,
          npr_fecha,
          npr_estado,
          npr_padre
                 )
                 VALUES
                 (
                  '" . $_SESSION["id"] . "',
                  '" . $idnotica . "',
                  '" . $comentario . "',
                  now(),
                  '1',
                  '" . $padre . "'
                 )"
    );
    return $registro;
  }
}
