<?php
require_once(ROOT_PATH . "/main-app/class/Modulos.php");
class botonesGuardar
{
/**
 * Constructor de la clase.
 *
 * @param string $urlBtnAtras URL a la que se redirigirá al presionar el botón "Atrás".
 * @param bool $permiso Permiso para mostrar los botones (predeterminado: true).
 * @param string $textBtnGuardar Texto o ícono para el botón "Guardar" (predeterminado: "N").
 */
    public function __construct($urlBtnAtras, $permiso=true,$textBtnGuardar = "N")
    {
        global $frases, $datosUsuarioActual;
        if($textBtnGuardar== "N"){
            $textBtnGuardar= "<i class='fa fa-save' aria-hidden='true'></i>{$frases[419][$datosUsuarioActual['uss_idioma']]}";
        }
        botonesGuardar::mostrarBotones($urlBtnAtras,$permiso,$textBtnGuardar);
    }

    public static function mostrarBotones($urlBtnAtras,$permiso, $btnGuardar)
    {
        global $frases, $datosUsuarioActual;
        $botnoesHtml = "";
        if(!empty($urlBtnAtras)){
            $botnoesHtml .=  "
            <a href='javascript:void(0);' name='{$urlBtnAtras}' class='btn btn-secondary'  style='text-transform:uppercase' onClick='deseaRegresar(this)'><i class='fa fa-long-arrow-left'></i>{$frases[184][$datosUsuarioActual['uss_idioma']]}</a>
            ";
        }      
        if ($permiso) {
            $botnoesHtml .= "
            <button type='submit' class='btn  btn-info' style='text-transform:uppercase'>
                {$btnGuardar}
            </button>";
        }
        echo $botnoesHtml;
    }
}
