<?php
require_once("servicios/Servicios.php");

class SocialComentarios extends Servicios
{

  /**
   * Consulta la cantidad de un comentario en específico.
   *
   * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
   *
   * @return array|false Arreglo con la información del comentario o false si hay un error.
   */
  public static function consultar($parametrosArray = null)
  {
    global $config;
    $sqlInicial = "SELECT * FROM " . BD_ADMIN . ".social_noticias_comentarios ncm
    INNER JOIN " . BD_GENERAL . ".usuarios uss ON uss_id=ncm_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
    INNER JOIN " . BD_ADMIN . ".social_noticias sn ON not_id=ncm_noticia";
    if ($parametrosArray && count($parametrosArray) > 0) {
      $parametrosValidos = array('ncm_noticia', 'ncm_id', 'ncm_usuario');
      $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
    };

    return Servicios::getSql($sqlInicial);
  }

  /**
   * Lista los comentarios con información adicional.
   *
   * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
   *
   * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
   */
  public static function listar($parametrosArray = null)
  {
    global $config;
    $sqlInicial = "SELECT * FROM " . BD_ADMIN . ".social_noticias_comentarios
    INNER JOIN " . BD_GENERAL . ".usuarios uss ON uss_id=ncm_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}";
    if ($parametrosArray && count($parametrosArray) > 0) {
      $parametrosValidos = array('ncm_noticia', 'ncm_id', 'ncm_usuario','ncm_padre');
      $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
    };
    $sqlFinal = "ORDER BY ncm_fecha DESC";
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
    $sqlInicial = "SELECT Count(*) as cantidad FROM " . BD_ADMIN . ".social_noticias_comentarios";
    if ($parametrosArray && count($parametrosArray) > 0) {
      $parametrosValidos = array('ncm_noticia', 'ncm_id', 'ncm_usuario','ncm_padre');
      $sqlInicial = Servicios::concatenarWhereAnd($sqlInicial, $parametrosValidos, $parametrosArray);
    };
    $sqlFinal = "ORDER BY ncm_id DESC";
    $sql = $sqlInicial . $sqlFinal;
    return Servicios::SelectSql($sql)[0]["cantidad"];
  }




  /**
   * elimina el comentarios.
   *
   * @param string $ncm_id ID del comentario.
   *
   * @return void
   */
  public static function eliminar($ncm_id)
  {
    Servicios::UpdateSql("DELETE FROM " . BD_ADMIN . ".social_noticias_comentarios  WHERE ncm_id ='" . $ncm_id . "'");
  }

  /**
   * Guarda la información de los comentarios.
   *
   * @param string $idnotica id de la notica a comentar
   * @param string $comentario Comentario de publicacion.
   * @param string $padre si el comentario es una respuesta de un comentario
   *
   * @return int|null Retorna el id del registro
   */
  public static function guardar($idnotica, $comentario, $padre=null)
  {
    global $config;
    $registro= Servicios::InsertSql(
      " INSERT INTO " . BD_ADMIN . ".social_noticias_comentarios(
          ncm_usuario, 
          ncm_noticia,
          ncm_comentario,
          ncm_fecha,
          ncm_estado,
          ncm_padre
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
