<?php

use PHPUnit\Framework\TestCase;

class InstitucionesTest extends TestCase {

    private $instituciones;

    protected function setUp(): void
    {
        // Define ROOT_PATH si no está definido
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__));
        }

        // Incluir el archivo de clase que quieres probar
        require_once(ROOT_PATH . "/main-app/class/Instituciones.php");

        $this->instituciones = new Instituciones();
    }

}