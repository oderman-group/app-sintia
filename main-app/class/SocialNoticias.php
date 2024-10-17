<?php
require_once("servicios/Servicios.php");

class SocialNoticias extends Servicios
{

    /**
     * Obtiene los datos de una noticia por ID .
     *
     * @param string $idNoticia ID de la noticia  a consultar.
     *
     * @return array|string Devuelve un conjunto de resultados de la consulta de noticias o una cadena vacía si no se encuentra ningún resultado.
     */
    public static function consultarNoticia(string $id_noticia): array|null
    {
        global $config;
        $resultado = [];

        $sql = "
        SELECT * FROM " . BD_ADMIN . ".social_noticias noti 

        INNER JOIN " . BD_GENERAL . ".usuarios uss 
        ON  uss_id          = not_usuario 
        AND uss.institucion = noti.not_institucion
        AND uss.year        = noti.not_year

        WHERE  not_id       = ?        
        LIMIT 1";

        $parametros = [$id_noticia];
        $consulta = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
    }
}
