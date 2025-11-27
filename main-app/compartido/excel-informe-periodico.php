<?php
// Importante: NO imprimir nada antes de generar el Excel
ob_start(); // Capturar cualquier salida accidental

include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/componentes/Excel/ExcelUtil.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once("../class/Informes.php");

use PhpOffice\PhpSpreadsheet\Style\Border;

$num = 0;
try {
    // Validar parámetros POST
    if(empty($_POST["gradoInforme"]) || empty($_POST["materiasInforme"])){
        ob_end_clean(); // Limpiar buffer
        echo "Error: Parámetros incompletos para generar el informe.";
        exit();
    }
    
    $curos[0]         = $_POST["gradoInforme"];
    $listagrupos      = !empty($_POST["gruposInforme"]) ? $_POST["gruposInforme"] : [];
    $listaMaterias    = $_POST["materiasInforme"];
    $consulta         = Informes::informePeriodico($curos, $listagrupos, $listaMaterias);
    $estilosNota      = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $_SESSION["bd"]);
    $listaTipoNotas   = $estilosNota->fetch_all(MYSQLI_ASSOC);
    // colores
    $ColorSuperior    = '14bd09';
    $ColorAlto        = 'f1d909';
    $ColorBasico      = '09adbd';
    $ColorBajo        = 'e71208';
    $ColorTextoClaro  = 'f9f1e8';
    $ColorTextoOscuro = '302e2c';
    $ColorCabecera    = '71f6e6';
    $ColorCabecera2   = 'f6e871';

    if (!empty($listaMaterias)) {
        
        $excelUtil = new ExcelUtil("uno");

        $indice = 0;
        $inicio = false;
        $sheet  = $excelUtil->sheet[0];

        foreach ($listaMaterias as $idMateria) {

            $materia = Asignaturas::consultarMateria($idMateria);

            if (!$inicio) {
                $sheet->setTitle($materia["mat_nombre"]);
                $inicio = true;
            } else {
                $excelUtil->agregarHoja($materia["mat_nombre"]);
            }
            // /////////////////////////////ENCABEZADO////////////////////////////////////////////////
            // Style del cuadro de las cabecera
            $excelUtil->sheet[$indice]->getStyle('A1:K5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); //Todos los bordes sencillos
            $excelUtil->sheet[$indice]->getStyle('K1:K5')->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM);     //bordes de la derecha gruesos
            $excelUtil->sheet[$indice]->getStyle('A1:A5')->getBorders()->getLeft()->setBorderStyle(Border::BORDER_MEDIUM);    //bordes de la Izquierda gruesos
            $excelUtil->sheet[$indice]->getStyle('A5:k5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);   //bordes de abajo gruesos

            //celdas de imagen de la institucion 
            $excelUtil->sheet[$indice]->mergeCells('A1:B5');
            $excelUtil->sheet[$indice]->getColumnDimension('A')->setWidth(10);
            $excelUtil->sheet[$indice]->getColumnDimension('B')->setWidth(15);
            $excelUtil->sheet[$indice]->getRowDimension('5')->setRowHeight(20);

            // Validar logo de la institución
            $urlImage = null;
            if(!empty($informacion_inst["info_logo"])){
                $urlImage = '../files/images/logo/' . $informacion_inst["info_logo"];
                if (!Utilidades::ArchivoExiste($urlImage)) {
                    $urlImage = null;
                }
            }
            
            // Intentar logo alternativo si no existe el principal
            if(empty($urlImage)){
                $urlImage = '../files/images/logo/sintia-logo-2023.png';
                if (!Utilidades::ArchivoExiste($urlImage)) {
                    $urlImage = null;
                }
            }
            
            // Solo agregar imagen si existe
            if(!empty($urlImage)){
                $excelUtil->agregarImagenLogo('A1', $urlImage, 25, 2);
            }
            // //celdas de Nombre del reporte
            $excelUtil->agregarTitulo('C1', 'INFORME DE EVALUACIÓN INTERNA DE ESTUDIANTES', 14);
            $excelUtil->sheet[$indice]->mergeCells('C1:H5'); // Combinar celdas
            // // columna de datos cabecera
            $excelUtil->textoNegrita('I1:I5');
            $num = 1;
            $excelUtil->agregarTexto('I' . $num++, 'CODIGO');
            $excelUtil->agregarTexto('I' . $num++, 'VERSIÓN');
            $excelUtil->agregarTexto('I' . $num++, 'FECHA');
            $excelUtil->agregarTexto('I' . $num++, 'Página');
            // columna de datos cabecera valores
            $num = 1;
            $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);   // Combinar celdas   
            $excelUtil->agregarTexto('J' . $num++, '');

            $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);   // Combinar celdas
            $excelUtil->agregarTexto('J' . $num++, '');

            $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);   // Combinar celdas  
            $excelUtil->agregarTexto('J' . $num++, '');

            $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);   // Combinar celdas  
            $excelUtil->agregarTexto('J' . $num++, ($indice + 1) . ' de ' . count($listaMaterias));
            // // // /////////////////////////////DATOS INSTITUCION////////////////////////////////////////////////
            $excelUtil->textoNegrita('A7:B9'); // colocamos negrita un rango de celdas
            $excelUtil->sheet[$indice]->mergeCells('A7:B7');   // Combinar celdas                                            
            $excelUtil->sheet[$indice]->mergeCells('A8:B8');  // Combinar celdas                                          
            $excelUtil->sheet[$indice]->mergeCells('A9:B9'); // Combinar celdas

            $num = 7;
            $excelUtil->agregarTexto('A' . $num++, 'INSTITUCIÓN EDUCATIVA:');
            $excelUtil->agregarTexto('A' . $num++, 'CÓDIGO DANE:');
            $excelUtil->agregarTexto('A' . $num++, 'MUNICIPIO:');

            $num = 7;
            $excelUtil->sheet[$indice]->mergeCells('C' . $num . ':H' . $num); // Combinar celdas
            $excelUtil->agregarTexto('C' . $num++, !empty($informacion_inst["info_nombre"]) ? $informacion_inst["info_nombre"] : '');
            $excelUtil->sheet[$indice]->mergeCells('C' . $num . ':H' . $num); // Combinar celdas          
            $excelUtil->agregarTexto('C' . $num++, !empty($informacion_inst["info_dane"]) ? $informacion_inst["info_dane"] . ' ' : '');
            $excelUtil->sheet[$indice]->mergeCells('C' . $num . ':H' . $num); // Combinar celdas
            $excelUtil->agregarTexto('C' . $num++, !empty($informacion_inst["info_direccion"]) ? $informacion_inst["info_direccion"] . ' ' : '');
            // // // /////////////////////////////CUADRO DE DESEMPENO///////////////////////////////////////////////
            $num = 6;
            $excelUtil->sheet[$indice]->getStyle('I6:K11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); //Todos los bordes sencillos
            $excelUtil->sheet[$indice]->mergeCells('I6:K7');   // Combinar celdas 
            $excelUtil->agregarTexto('I' . $num, 'DESEMPEÑO');
            $excelUtil->ajustarTexto('I' . $num);
            $num = 8;
            
            // Validar que existan los tipos de notas antes de acceder
            if(isset($listaTipoNotas[3])){
                $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);
                $excelUtil->agregarTexto('I' . $num, $listaTipoNotas[3]["notip_desde"] . ' - ' . $listaTipoNotas[3]["notip_hasta"], $ColorTextoClaro, $ColorSuperior);
                $excelUtil->centrarTexto('I' . $num);
                $excelUtil->agregarTexto('J' . $num++, $listaTipoNotas[3]["notip_nombre"]);
            }

            if(isset($listaTipoNotas[2])){
                $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);
                $excelUtil->agregarTexto('I' . $num, $listaTipoNotas[2]["notip_desde"] . ' - ' . $listaTipoNotas[2]["notip_hasta"], $ColorTextoOscuro, $ColorAlto);
                $excelUtil->centrarTexto('I' . $num);
                $excelUtil->agregarTexto('J' . $num++, $listaTipoNotas[2]["notip_nombre"]);
            }

            if(isset($listaTipoNotas[1])){
                $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);
                $excelUtil->agregarTexto('I' . $num, $listaTipoNotas[1]["notip_desde"] . ' - ' . $listaTipoNotas[1]["notip_hasta"], $ColorTextoClaro, $ColorBasico);
                $excelUtil->centrarTexto('I' . $num);
                $excelUtil->agregarTexto('J' . $num++, $listaTipoNotas[1]["notip_nombre"]);
            }

            if(isset($listaTipoNotas[0])){
                $excelUtil->sheet[$indice]->mergeCells('J' . $num . ':K' . $num);
                $excelUtil->agregarTexto('I' . $num, $listaTipoNotas[0]["notip_desde"] . ' - ' . $listaTipoNotas[0]["notip_hasta"], $ColorTextoClaro, $ColorBajo);
                $excelUtil->centrarTexto('I' . $num);
                $excelUtil->agregarTexto('J' . $num++, $listaTipoNotas[0]["notip_nombre"]);
            }
            // // // /////////////////////////////DATOS ASIGNADTURA////////////////////////////////////////////////
            $excelUtil->sheet[$indice]->mergeCells('A10:H11'); // Combinar celdas
            $num = 10;
            $excelUtil->agregarTitulo('A' . $num++, 'ASIGNATURA: ' . $materia["mat_nombre"]);
            // // // /////////////////////////////CABECERA DATOS////////////////////////////////////////////////
            $num = 12;
            $excelUtil->sheet[$indice]->mergeCells('A12:K12'); // Combinar celdas 
            $excelUtil->agregarTitulo('A' . $num++, 'ANO: ' . $_SESSION["bd"], null, null, $ColorCabecera);
            // // // /////////////////////////////CABECERA DATOS CAMPOS////////////////////////////////////////////////
            $excelUtil->sheet[$indice]->getStyle('A12:K14')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); //Todos los bordes sencillos
            $excelUtil->sheet[$indice]->mergeCells('A' . $num . ':A' . $num + 1); // Combinar celdas 
            $excelUtil->agregarTitulo('A' . $num, 'No.', null, null, $ColorCabecera2);

            $excelUtil->sheet[$indice]->mergeCells('B' . $num . ':B' . $num + 1); // Combinar celdas 
            $excelUtil->agregarTitulo('B' . $num, 'TIPO DOC', null, null, $ColorCabecera2);

            $excelUtil->sheet[$indice]->mergeCells('C' . $num . ':C' . $num + 1); // Combinar celdas 
            $excelUtil->agregarTitulo('C' . $num, 'N° DOCUMENTO', null, null, $ColorCabecera2);

            $excelUtil->sheet[$indice]->mergeCells('D' . $num . ':D' . $num + 1); // Combinar celdas 
            $excelUtil->agregarTitulo('D' . $num, 'NOMBRE DEL ESTUDIANTE', null, null, $ColorCabecera2);

            $excelUtil->sheet[$indice]->mergeCells('E' . $num . ':E' . $num + 1); // Combinar celdas 
            $excelUtil->agregarTitulo('E' . $num, 'GRADO', null, null, $ColorCabecera2);

            $excelUtil->sheet[$indice]->mergeCells('F' . $num . ':F' . $num + 1); // Combinar celdas 
            $excelUtil->agregarTitulo('F' . $num, 'SEDE', null, null, $ColorCabecera2);

            $excelUtil->sheet[$indice]->mergeCells('G' . $num . ':K' . $num); // Combinar celdas 
            $excelUtil->agregarTitulo('G' . $num++, 'PERIODOS', null, null, $ColorCabecera2);
            $excelUtil->agregarTitulo('G' . $num, '1', null, null, $ColorCabecera2);
            $excelUtil->agregarTitulo('H' . $num, '2', null, null, $ColorCabecera2);
            $excelUtil->agregarTitulo('I' . $num, '3', null, null, $ColorCabecera2);
            $excelUtil->agregarTitulo('J' . $num, '4', null, null, $ColorCabecera2);
            $excelUtil->agregarTitulo('K' . $num, 'FINAL', null, null, $ColorCabecera2);

            $indice++;
        }
        // // // /////////////////////////////RESULTADOS DE CONSULTA////////////////////////////////////////////////

        $mat_id = "";
        $car_materia = "";
        $cont = 0;
        $numInicial = 15;
        $num = 15;

        foreach ($consulta as $registro) {

            if ($mat_id != $registro["mat_id"] . '-' . $registro["car_materia"]) {

                $indice = array_search($registro["car_materia"], $listaMaterias);

                if ($car_materia != $registro["car_materia"]) {
                    $car_materia = $registro["car_materia"];
                    $cont = 1;
                    $num = 15;
                } else {
                    $cont++;
                    $num++;
                }

                $excelUtil->sheet[$indice]->getStyle('A' . $num . ':K' . $num)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); //Todos los bordes sencillos 
                $excelUtil->indice = $indice;

                $excelUtil->agregarTexto('A' . $num, $cont);
                $excelUtil->ajustarTexto('A' . $num);
                $excelUtil->agregarTexto('B' . $num, !empty($registro["ogen_nombre"]) ? $registro["ogen_nombre"] : '');

                if (!empty($registro["mat_documento"])) {
                    $excelUtil->agregarTexto('C' . $num, $registro["mat_documento"]);
                    $excelUtil->ajustarTexto('C' . $num);
                }

                // Validar que el array tenga los campos necesarios antes de llamar a la función
                $nombreCompleto = '';
                if (!empty($registro["mat_nombres"]) || !empty($registro["mat_primer_apellido"])) {
                    $nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($registro);
                }
                if (!empty($nombreCompleto)) {
                    $excelUtil->agregarTexto('D' . $num, $nombreCompleto);
                }

                if (!empty($registro["gra_nombre"])) {
                    $grupoNombre = !empty($registro["gru_nombre"]) ? $registro["gru_nombre"] : '';
                    $excelUtil->agregarTexto('E' . $num, $registro["gra_nombre"] . '-' . $grupoNombre);
                    $excelUtil->ajustarTexto('E' . $num);
                }

                if (!empty($datosUnicosInstitucion["ins_siglas"])) {
                    $excelUtil->agregarTexto('F' . $num, $datosUnicosInstitucion["ins_siglas"]);
                    $excelUtil->ajustarTexto('F' . $num);
                }

               

                $mat_id = $registro["mat_id"] . '-' . $registro["car_materia"];
            }

            $estiloNota = Boletin::obtenerDatosTipoDeNotasCargadas($listaTipoNotas, $registro["bol_nota"]);
            $Colorfondo = "ffffff";
            $texto = "000000";

            // Validar que estiloNota y listaTipoNotas tengan datos
            if(!empty($estiloNota['notip_nombre'])){
                if(isset($listaTipoNotas[3]) && $estiloNota['notip_nombre'] == $listaTipoNotas[3]["notip_nombre"]){
                    $Colorfondo = $ColorSuperior;
                    $texto = $ColorTextoClaro;
                }
                elseif(isset($listaTipoNotas[2]) && $estiloNota['notip_nombre'] == $listaTipoNotas[2]["notip_nombre"]){
                    $Colorfondo = $ColorAlto;
                    $texto = $ColorTextoOscuro;
                }
                elseif(isset($listaTipoNotas[1]) && $estiloNota['notip_nombre'] == $listaTipoNotas[1]["notip_nombre"]){
                    $Colorfondo = $ColorBasico;
                    $texto = $ColorTextoClaro;
                }
                elseif(isset($listaTipoNotas[0]) && $estiloNota['notip_nombre'] == $listaTipoNotas[0]["notip_nombre"]){
                    $Colorfondo = $ColorBajo;
                    $texto = $ColorTextoClaro;
                }
            }

            if ($registro["bol_periodo"] == '1') {
                $excelUtil->agregarTexto('G' . $num, $registro["bol_nota"], $texto, $Colorfondo);
                $excelUtil->centrarTexto('G' . $num);
                continue;
            }

            if ($registro["bol_periodo"] == '2') {
                $excelUtil->agregarTexto('H' . $num, $registro["bol_nota"], $texto, $Colorfondo);
                $excelUtil->centrarTexto('H' . $num);
                continue;
            }

            if ($registro["bol_periodo"] == '3') {
                $excelUtil->agregarTexto('I' . $num, $registro["bol_nota"], $texto, $Colorfondo);
                $excelUtil->centrarTexto('I' . $num);
                continue;
            }

            if ($registro["bol_periodo"] == '4') {
                $excelUtil->agregarTexto('J' . $num, $registro["bol_nota"], $texto, $Colorfondo);
                $excelUtil->centrarTexto('J' . $num);
                continue;
            }

            if ($registro["bol_periodo"] == '5') {
                $excelUtil->agregarTexto('K' . $num, $registro["bol_nota"], $texto, $Colorfondo);
                $excelUtil->centrarTexto('K' . $num);
                continue;
            }
        }
        
        // Limpiar cualquier salida antes de enviar el Excel
        ob_end_clean();
        
        // Descargar Excel con nombre válido (sin barras diagonales)
        $excelUtil->descargarExcel("Informe_periodico_" . date("d_m_Y") . "-SINTIA.xlsx");
    }

    exit;
} catch (Exception $e) {
    // Log del error pero no imprimir nada que corrompa el Excel
    error_log("Error en excel-informe-periodico.php: " . $e->getMessage());
    echo "Error al generar el informe. Por favor, contacte al administrador.";
    exit();
}
