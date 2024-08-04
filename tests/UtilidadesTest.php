<?php
use PHPUnit\Framework\TestCase;

class UtilidadesTest extends TestCase {

    private $utilidades;

    protected function setUp(): void
    {
        // Define ROOT_PATH si no está definido
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__));
        }

        // Incluir el archivo de clase que quieres probar
        require_once(ROOT_PATH . "/main-app/class/Utilidades.php");

        $this->utilidades = new Utilidades();
    }

    /**
     * @covers Utilidades::setFinalZero
     */
    public function testPonerCeroConPunto() {
        $this->assertEquals('5.0', $this->utilidades->setFinalZero(5));
        $this->assertEquals('4.5', $this->utilidades->setFinalZero(4.5));
        $this->assertEquals('4.50', $this->utilidades->setFinalZero(4.5));
        $this->assertNotEquals('4.5.0', $this->utilidades->setFinalZero(4.5));
    }

    public static function casosDeUso(): array 
    {
        return [
            ['MAT', 'academico_matriculas'],
            ['EST', 'estudiantes'],
            ['USU', 'GENERAL_USUARIOS'],
            [NULL, ''],
            ['ACT', 'academico_actividades'],
            ['RES', 'academico_actividad_respuestas'],
        ];
    }

    /**
     * @dataProvider casosDeUso
     * @covers Utilidades::getPrefixFromTableName
     */
    public function testObtenerLasTresPrimerasLetrasEnMayuscula($resultadoEsperado, $tabla) {
        $this->assertEquals($resultadoEsperado, $this->utilidades->getPrefixFromTableName($tabla));
    }

    /**
     * @covers Utilidades::getNextIdSequence
     */
    public function testObtenerElAutoIncrementoDeUnaTablaEnBD_SinAutoIncremento() {
        // Mock the required dependencies
        $conexionPDO = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);

        // Set up the expectations for the mock objects
        $conexionPDO->expects($this->once())
            ->method('prepare')
            ->with("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_schema = :bd AND table_name = :table")
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['bd' => 'mobiliar_academic', 'table' => 'academico_grados']);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn([]); // No AUTO_INCREMENT value found

        // Call the method under test
        $result = Utilidades::getNextIdSequence($conexionPDO, 'mobiliar_academic', 'academico_grados');

        // Assert that a code is generated
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]+$/', $result);
    }

    /**
     * @covers Utilidades::getNextIdSequence
     */
    public function testObtenerElAutoIncrementoDeUnaTablaEnBD_AutoIncrementoEncontrado() {
        // Mock the required dependencies
        $conexionPDO = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
    
        // Set up the expectations for the mock objects
        $conexionPDO->expects($this->once())
            ->method('prepare')
            ->with("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_schema = :bd AND table_name = :table")
            ->willReturn($stmt);
    
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['bd' => 'mobiliar_academic', 'table' => 'academico_grados']);
    
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['AUTO_INCREMENT' => 10]); // AUTO_INCREMENT value found

            $_SESSION = null; // Reset the session for the next test
    
        // Call the method under test
        $result = Utilidades::getNextIdSequence($conexionPDO, 'mobiliar_academic', 'academico_grados');
    
        // Assert that the AUTO_INCREMENT value is returned
        $this->assertEquals("GRA10", $result);
    }

    /**
     * @covers Utilidades::getNextIdSequence
     */
    public function testObtenerElAutoIncrementoDeUnaTablaEnBD_Excepcion() {
        // Mock the required dependencies
        $conexionPDO = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
    
        // Set up the expectations for the mock objects
        $conexionPDO->expects($this->once())
            ->method('prepare')
            ->with("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_schema = :bd AND table_name = :table")
            ->willReturn($stmt);
    
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['bd' => 'mobiliar_academic', 'table' => 'academico_grados'])
            ->willThrowException(new PDOException('Error al ejecutar la consulta'));
    
        // Call the method under test and expect an exception
        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('Error al ejecutar la consulta');
    
        Utilidades::getNextIdSequence($conexionPDO, 'mobiliar_academic', 'academico_grados');
    }

    /**
     * @covers Utilidades::getNextIdSequence
     */
    public function testObtenerElAutoIncrementoDeUnaTablaEnBD_NombreTablaVacio() {
        // Mock the required dependencies
        $conexionPDO = $this->createMock(PDO::class);
    
        // Call the method under test and expect an exception
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El nombre de la tabla, la bd o la conexión no pueden estar vacíos');
    
        Utilidades::getNextIdSequence($conexionPDO, 'mobiliar_academic', '');
    }

    /**
     * @covers Utilidades::getNextIdSequence
     */
    public function testObtenerElAutoIncrementoDeUnaTablaEnBD_NombreBDVacio() {
        // Mock the required dependencies
        $conexionPDO = $this->createMock(PDO::class);
    
        // Call the method under test and expect an exception
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El nombre de la tabla, la bd o la conexión no pueden estar vacíos');
    
        Utilidades::getNextIdSequence($conexionPDO, '', 'academico_grados');
    }

    /**
     * @covers Utilidades::getNextIdSequence
     */
    public function testObtenerElAutoIncrementoDeUnaTablaEnBD_ConexionVacio() {
        // Mock the required dependencies
        $conexionPDO = null;
    
        // Call the method under test and expect an exception
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El nombre de la tabla, la bd o la conexión no pueden estar vacíos');
    
        Utilidades::getNextIdSequence($conexionPDO, 'mobiliar_academic', 'academico_grados');
    }

    /**
     * @covers Utilidades::getNextIdSequence
     */
    public function testGetNextIdSequenceWithAdditionalParameters()
    {
        // Create a mock object for the PDO connection and statement
        $mockConnection = $this->createMock(PDO::class);
        $mockStatement = $this->createMock(PDOStatement::class);

        // Set up the mock objects to return the expected results
        $mockConnection->method('prepare')->willReturn($mockStatement);
        $mockStatement->method('execute')->willReturn(true);
        $mockStatement->method('fetch')->willReturn(['AUTO_INCREMENT' => 456]);

        // Set up additional parameters
        $_SESSION = ['id' => 101];

        // Call getNextIdSequence with a valid connection, table, and additional parameters
        $result = Utilidades::getNextIdSequence($mockConnection, 'mobiliar_academic', 'test_table');

        // Assert that the method returns the expected value
        $this->assertEquals('TAB456101', $result);
    }

    /**
     * @covers Utilidades::getToString
     */
    public function testGetToStringWithEmptyString()
    {
        $input = "";
        $expectedOutput = "";

        $result = Utilidades::getToString($input);
        $this->assertEquals($expectedOutput, $result, "getToString should return an empty string when the input value is an empty string");
    }

    /**
     * @covers Utilidades::getToString
     */
    public function testGetToStringWithNullValue()
    {
        $input = null;
        $expectedOutput = "";

        $result = Utilidades::getToString($input);
        $this->assertEquals($expectedOutput, $result, "getToString should return an empty string when the input value is null");
    }

}