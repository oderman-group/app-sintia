<?php

namespace App\Tests;

use App\class\HolaMundo;
use PHPUnit\Framework\TestCase;

class HolaMundoTest extends TestCase {

    /**
     * @covers App\HolaMundo::saluda
     */
    public function testDiceHolaMundo() {
        $holaMundo = new HolaMundo;

        $this->assertEquals('Hola mundo!', $holaMundo->saluda(), "mi comentario");

    }
}