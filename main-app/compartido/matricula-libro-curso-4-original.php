<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/compartido/overlay.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Tables/BDT_configuracion.php");
require_once(ROOT_PATH . "/main-app/class/Instituciones.php");

$year = $_SESSION["bd"];

if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
if (isset($_POST["year"])) {
    $year = $_POST["year"];
}

$periodoFinal = $config['conf_periodos_maximos'];

$grado = 1;
if (!empty($_GET["curso"])) {
    $grado = base64_decode($_GET["curso"]);
}
if (isset($_POST["curso"])) {
    $grado = $_POST["curso"];
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
    $grupo = base64_decode($_GET["grupo"]);
}
if (!empty($_POST["grupo"])) {
    $grupo = $_POST["grupo"];
}

$idEstudiante = '';
if (isset($_POST["id"])) {
    $idEstudiante = $_POST["id"];
}

if (isset($_GET["id"])) {
    $idEstudiante = base64_decode($_GET["id"]);
}
if (!empty($idEstudiante)) {
    $filtro = " AND mat_id='" . $idEstudiante . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    $estudiante = $matriculadosPorCurso->fetch_assoc();
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $grado = $estudiante["mat_grado"];
        $grupo = $estudiante["mat_grupo"];
    }
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <title>Libro Final</title>
    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
    <link href="../css/ButomDowloadPdf.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    	<!-- notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- favicon -->
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>" />
    <style>
        /* Estilos para impresión */
        @media print {
            .page {
                page-break-after: always;
                page-break-inside: avoid;
            }
            .btn-flotante {
                display: none !important;
            }
        }
        
        /* Estilos para PDF generado con html2pdf */
        .page {
            margin-bottom: 30px;
            padding-bottom: 20px;
            box-sizing: border-box;
            break-inside: avoid;
            overflow: visible;
        }
        
        .page:last-child {
            margin-bottom: 0;
        }
        
        #guardarPDF, #guardarExcel, #imprimir {
            cursor: pointer !important;
            position: fixed !important;
            bottom: 40px !important;
            right: 40px !important;
            font-size: 10px !important;
            padding: 8px 15px !important;
            width: 130px !important;
            z-index: 99 !important;
            text-transform: uppercase !important;
            font-weight: bold !important;
            color: #ffffff !important;
            border-radius: 5px !important;
            letter-spacing: 0.5px !important;
            background-color: #E91E63 !important;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1) !important;
            transition: all 300ms ease 0ms !important;
            border: none !important;
            line-height: 1.2 !important;
        }
        
        #guardarPDF:hover, #guardarExcel:hover, #imprimir:hover {
            background-color: #2c2fa5 !important;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.3) !important;
            transform: translateY(-7px) !important;
        }
        
        #guardarExcel {
            bottom: 100px !important;
        }
        
        #imprimir {
            bottom: 160px !important;
        }
        
        /* Limitar ancho de casilla Documento para evitar desbordamiento */
        .codigo-estudiante {
            max-width: 120px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

       
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
</head>
<?php
// Cosnultas iniciales
$listaDatos = [];
$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
    $tiposNotas[] = $row;
}

if (!empty($grado) && !empty($grupo) && !empty($periodoFinal) && !empty($year)) {
    $periodos = [];
    for ($i = 1; $i <= $periodoFinal; $i++) {
        $periodos[$i] = $i;
    }
    $datos = Boletin::datosBoletin($grado, $grupo, $periodos, $year, $idEstudiante);
    while ($row = $datos->fetch_assoc()) {
        $listaDatos[] = $row;
    }
    include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
}







if ($grado >= 12 && $grado <= 15) {
    $educacion = "PREESCOLAR";
} elseif ($grado >= 1 && $grado <= 5) {
    $educacion = "PRIMARIA";
} elseif ($grado >= 6 && $grado <= 9) {
    $educacion = "SECUNDARIA";
} elseif ($grado >= 10 && $grado <= 11) {
    $educacion = "MEDIA";
}

?>

<body style="font-family:Arial; font-size:9px;">

    <div id="contenido" >
    <p>&nbsp;</p>
        <?php foreach ($estudiantes as $estudiante) {
            $totalNotasPeriodo = [];
            ?>
            <div class="page" style="margin-left: 50px;margin-right: 50px;">
                 <!-- <h1>Página <?= $estudiante["nro"]?></h1> -->
                <div style="margin: 15px 0;">
                    <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 13px;">
                        <tr>
                            <td rowspan="3" width="20%"><img
                                    src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="100%"></td>
                            <td align="center" rowspan="3" width="25%">
                                <h3 style="font-weight:bold; color: #00adefad; margin: 0">
                                    <?= strtoupper($informacion_inst["info_nombre"]) ?>
                                </h3><br>
                                <?= $informacion_inst["info_direccion"] ?><br>
                                Informes: <?= $informacion_inst["info_telefono"] ?>
                            </td>
                            <td class="codigo-estudiante">Código:<br> <b style="color: #00adefad;"><?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?></b></td>
                            <td>Nombre:<br> <b style="color: #00adefad;"><?= $estudiante["nombre"] ?></b></td>
                        </tr>
                        <tr>
                            <td>Curso:<br> <b style="color: #00adefad;"><?= strtoupper($estudiante["gra_nombre"]) ?></b>
                            </td>
                            <td>Sede:<br> <b
                                    style="color: #00adefad;"><?= strtoupper($informacion_inst["info_nombre"]) ?></b></td>
                        </tr>
                        <tr>
                            <td>Jornada:<br> <b
                                    style="color: #00adefad;"><?= strtoupper($informacion_inst["info_jornada"]) ?></b></td>
                            <td>Documento:<br> <b style="color: #00adefad;">BOLETÍN DEFINITIVO DE NOTAS - EDUCACIÓN BÁSICA
                                    <?= strtoupper($educacion) ?></b></td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                </div>
                <table width="100%">
                    <tr>
                        <td>
                            <div class="divBordeado">&nbsp;</div>
                        </td>
                    </tr>
                    <tr style="text-align:center; font-size: 13px;">
                        <td style="color: #b2adad;">
                            <?php
                            $consultaEstiloNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
                            $numEstiloNota = mysqli_num_rows($consultaEstiloNota);
                            $i = 1;
                            while ($estiloNota = mysqli_fetch_array($consultaEstiloNota, MYSQLI_BOTH)) {
                                $diagonal = " / ";
                                if ($i == $numEstiloNota) {
                                    $diagonal = "";
                                }
                                echo $estiloNota['notip_nombre'] . ": " . $estiloNota['notip_desde'] . " - " . $estiloNota['notip_hasta'] . $diagonal;
                                $i++;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr style="text-align:center; font-size: 20px; font-weight:bold;">
                        <td>AÑO LECTIVO: <?= $year ?></td>
                    </tr>
                </table>
                <table width="100%" rules="all" border="1" style="font-size: 15px;">
                    <thead>
                        <tr style="font-weight:bold; text-align:center;">
                            <td width="20%" rowspan="2">ASIGNATURAS</td>
                            <td width="3%" rowspan="2">I.H</td>
                            <td width="3%" colspan="4" style="background-color: #00adefad;"><a href="#"
                                    style="color:#000; text-decoration:none;">Periodo Cursados</a></td>
                            <td width="3%" colspan="2"><a href="#" style="color:#000; text-decoration:none;">DEFINITIVA</a>
                            </td>
                        </tr>
                        <tr style="font-weight:bold; text-align:center;">
                            <?php
                            for ($i = 1; $i <= $periodoFinal; $i++) {
                                ?>
                                <td width="3%" style="background-color: #00adefad;"><?= $i ?></td>
                                <?php
                            }
                            ?>
                            <td width="3%">DEF</td>
                            <td width="3%">Desempeño</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cantidadAreas = 0;
                        $materiasPerdidas = 0;
                        foreach ($estudiante["areas"] as $area) {
                            $cantidadAreas++;
                            $ihArea = 0;
                            $notaAre = [];
                            $desenpenioAre;
                            ?>

                            <?php
                            foreach ($area["cargas"] as $carga) {
                                $promedioMateria = 0;
                                $fallasAcumuladas = 0;
                                $ihArea += $carga['car_ih'];
                                $style = "style='font-weight:bold;background: #EAEAEA;padding-left: 10px;'";
                                $cargaStyle = '';
                                $styleborder = '';
                                ?>
                                <?php if (count($area["cargas"]) > 1) {
                                    $nombre = $carga["mat_nombre"];
                                    $styleborder = '';
                                } else {
                                    $nombre = $area["ar_nombre"];
                                    $cargaStyle = '';
                                } ?>
                                <tr style="<?= $styleborder ?>">
                                    <td style="<?= $cargaStyle ?>  padding-left: 10px;"> <?= $nombre ?></td>
                                    <td style="<?= $cargaStyle ?>" align="center"><?= $carga['car_ih'] ?></td>
                                    <?php
                                    for ($j = 1; $j <= $periodoFinal; $j++) {
                                        $nota = isset($carga["periodos"][$j]["bol_nota"])
                                            ? $carga["periodos"][$j]["bol_nota"]
                                            : 0;
                                        $nota = Boletin::agregarDecimales($nota);
                                        $desempeno = Boletin::determinarRango($nota, $tiposNotas);
                                        $promedioMateria += $nota;
                                        $porcentajeMateria = !empty($carga['mat_valor']) ? $carga['mat_valor'] : 100;
                                        if (isset($notaAre[$j])) {
                                            $notaAre[$j] += $nota * ($porcentajeMateria / 100);
                                        } else {
                                            $notaAre[$j] = $nota * ($porcentajeMateria / 100);
                                        }

                                        if (isset($totalNotasPeriodo[$j])) {
                                            $totalNotasPeriodo[$j] += $nota * ($porcentajeMateria / 100);
                                        } else {
                                            $totalNotasPeriodo[$j] = $nota * ($porcentajeMateria / 100);
                                        }
                                        $background = 'background: #9ed8ed;';
                                        ?>
                                        <td align="center" align="center"
                                            style=" <?= $background ?>;<?= $cargaStyle ?> font-size:12px;">
                                            <?= $nota == 0 ? '' : number_format($nota, $config['conf_decimales_notas']); ?>
                                        </td>
                                    <?php }

                                    $periodoCalcular = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? COUNT($carga["periodos"]) : $config["conf_periodos_maximos"];
                                    $notaAcumulada = $promedioMateria / $periodoCalcular;
                                    $notaAcumulada = round($notaAcumulada, $config['conf_decimales_notas']);
                                    $desempenoAcumulado = Boletin::determinarRango($notaAcumulada, $tiposNotas);
                                    if ($notaAcumulada < $config['conf_nota_minima_aprobar']) {
                                        $materiasPerdidas++;
                                    }
                                    ?>
                                    <td align="center" style=" font-size:12px;"><?= $notaAcumulada <= 0 ? '' : $notaAcumulada ?>
                                    </td>
                                    <td align="center" style=" font-size:12px;">
                                        <?= $notaAcumulada <= 0 ? '' : $desempenoAcumulado["notip_nombre"] ?>
                                    </td>
                                </tr>
                            <?php }
                            if ($ihArea != $carga['car_ih']) { ?>
                                <tr>
                                    <td <?= $style ?>><?= $area["ar_nombre"] ?></td>
                                    <td align="center" <?= $style ?>><?= $ihArea ?></td>
                                    <?php
                                    $notaAreAcumulada = 0;
                                    $periodoAreaCalcular = $config["conf_periodos_maximos"];
                                    for ($j = 1; $j <= $periodoFinal; $j++) {
                                        $notaAreAcumulada += $notaAre[$j]; ?>
                                        <td align="center" <?= $style ?>>
                                            <?= $notaAre[$j] <= 0 ? '' : number_format($notaAre[$j], $config['conf_decimales_notas']); ?>
                                        </td>

                                        <?php
                                        if ($notaAre[$j] <= 0) {
                                            $periodoAreaCalcular -= 1;
                                        }
                                    }

                                    $periodoAreaCalcular = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? $periodoAreaCalcular : $config["conf_periodos_maximos"];
                                    $notaAreAcumulada = number_format($notaAreAcumulada / $periodoAreaCalcular, $config['conf_decimales_notas']);
                                    $desenpenioAreAcumulado = Boletin::determinarRango($notaAreAcumulada, $tiposNotas);

                                    ?>

                                    <td align="center" <?= $style ?>><?= $notaAreAcumulada <= 0 ? '' : $notaAreAcumulada ?></td>
                                    <td align="center" <?= $style ?>>
                                        <?= $notaAreAcumulada <= 0 ? '' : $desenpenioAreAcumulado["notip_nombre"] ?>
                                    </td>
                                </tr>
                            <?php }
                        } ?>
                    </tbody>
                    <tfoot style="font-size: 13px;">
                        <tr style="font-weight:bold;background: #EAEAEA;font-size: 15px">
                            <td colspan="2">PROMEDIO GENERAL</td>
                            <?php
                            $promedioFinal = 0;
                            $periodoCalcular = $config["conf_periodos_maximos"];
                            for ($j = 1; $j <= $periodoFinal; $j++) {
                                $acumuladoPj = ($totalNotasPeriodo[$j] / $cantidadAreas);
                                $acumuladoPj = round($acumuladoPj, $config['conf_decimales_notas']);
                                $promedioFinal += $acumuladoPj;

                                if ($acumuladoPj <= 0) {
                                    $periodoCalcular -= 1;
                                }
                                ?>
                                <td align="center"><?= $acumuladoPj <= 0 ? '' : $acumuladoPj ?> </td>
                            <?php }

                            $periodoCalcularFinal = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? $periodoCalcular : $config["conf_periodos_maximos"];
                            $promedioFinal = round($promedioFinal / $periodoCalcularFinal, $config['conf_decimales_notas']);

                            $desempenoAcumuladoTotal = Boletin::determinarRango($promedioFinal, $tiposNotas);
                            ?>
                            <td align="center"><?= $promedioFinal <= 0 ? '' : $promedioFinal ?></td>
                            <td align="center"><?= $desempenoAcumuladoTotal["notip_nombre"] ?></td>
                        </tr>
                        <tr style="color:#000;">
                            <td style="padding-left: 10px;" colspan="8">
                                <p>&nbsp;</p>
                                <h4 style="font-weight:bold; color: #00adefad;"><b>Observación definitiva:</b></h4>
                                <?php
                                if ($periodoFinal == $config["conf_periodos_maximos"]) {

                                    if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"]) {
                                        $msj = "EL(LA) ESTUDIANTE NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
                                    } elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] && $materiasPerdidas > 0) {
                                        $msj = "EL(LA) ESTUDIANTE DEBE NIVELAR LAS MATERIAS PERDIDAS.";
                                    } else {
                                        $msj = "EL(LA) ESTUDIANTE FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
                                    }

                                    if ($estudiante['mat_estado_matricula'] == CANCELADO && $periodoCalcularFinal < $config["conf_periodos_maximos"]) {
                                        $msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
                                    }
                                }
                                echo "<span style='padding-left: 10px;'>" . $msj . "</span>";
                                ?>
                                <p>&nbsp;</p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <p>&nbsp;</p>
                <!--******FIRMAS******-->
                <table style="text-align:center; font-size:10px;">
                    <tr>
                        <td align="center">
                            <?php
                            // Obtener información de la institución para el año consultado
                            try {
                                $informacionInstYear = Instituciones::getGeneralInformationFromInstitution($config['conf_id_institucion'], $year);
                                $idRector = !empty($informacionInstYear["info_rector"]) ? $informacionInstYear["info_rector"] : null;
                            } catch (Exception $e) {
                                $idRector = null;
                            }
                            
                            // Obtener datos del rector del año consultado
                            $rector = null;
                            $nombreRector = '';
                            
                            if (!empty($idRector)) {
                                $rector = Usuarios::obtenerDatosUsuario($idRector);
                                $nombreRector = !empty($rector) ? UsuariosPadre::nombreCompletoDelUsuario($rector) : '';
                            }
                            
                            // Verificar y mostrar la firma
                            if (!empty($rector) && !empty($rector["uss_firma"])) {
                                $rutaArchivo = ROOT_PATH . '/main-app/files/fotos/' . $rector['uss_firma'];
                                // Verificar que el archivo existe físicamente
                                if (file_exists($rutaArchivo)) {
                                    // Usar ruta relativa como el logo (debe funcionar igual)
                                    echo '<img src="../files/fotos/' . htmlspecialchars($rector["uss_firma"], ENT_QUOTES) . '" width="100" style="max-height: 80px; object-fit: contain;"><br>';
                                } else {
                                    // Si el archivo no existe, mostrar espacios
                                    echo '<p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                                }
                            } else {
                                // Si no hay firma, mostrar espacios
                                echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                            }
                            ?>
                            _________________________________<br>
                            <p>&nbsp;</p>
                            <?= $nombreRector ?><br>
                            Rector(a)
                        </td>
                    </tr>
                </table>
                <p>&nbsp;</p>
            </div>
        <?php } ?>
    </div>
    <input type="button" class="btn  btn-flotante btn-with-icon" id="imprimir" onclick="window.print()"
        value="Imprimir">
    </input>
    <input type="button" class="btn  btn-flotante btn-with-icon" id="guardarExcel" onclick="exportarExcel()"
        value="Descargar Excel">
    </input>
    <input type="button" class="btn  btn-flotante btn-with-icon" id="guardarPDF"
        value="Descargar PDF">
    </input>
    
    <script>
        // Incluir el código JavaScript directamente aquí para evitar problemas de carga
        <?php
        // Leer el contenido del archivo JavaScript y mostrarlo
        $jsPath = ROOT_PATH . '/main-app/js/ButomDowloadPdf.js';
        if (file_exists($jsPath)) {
            $jsContent = file_get_contents($jsPath);
            // Escapar el contenido para que no interfiera con el PHP
            echo $jsContent;
        } else {
            echo "console.error('Error: No se encontró el archivo ButomDowloadPdf.js');";
        }
        ?>
        
        // Asignar el event listener al botón después de que todo esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            const btnPDF = document.getElementById('guardarPDF');
            if (btnPDF && typeof generatePDF !== 'undefined') {
                btnPDF.onclick = function() {
                    generatePDF('contenido', 'LIBRO_FINAL_F2');
                };
                console.log('Botón PDF inicializado correctamente');
            } else {
                console.error('Error: No se pudo inicializar el botón PDF');
                if (btnPDF) {
                    btnPDF.onclick = function() {
                        alert('Error: La función de generar PDF no está disponible. Por favor, recargue la página.');
                    };
                }
            }
        });
        
        // Función para exportar a Excel
        function exportarExcel() {
            const curso = '<?= base64_encode($grado) ?>';
            const grupo = '<?= base64_encode($grupo) ?>';
            const year = '<?= base64_encode($year) ?>';
            const idEstudiante = '<?= !empty($idEstudiante) ? base64_encode($idEstudiante) : "" ?>';
            
            // Mostrar overlay de carga
            document.getElementById('overlay').style.display = 'flex';
            
            // Crear formulario temporal para enviar datos
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'matricula-libro-curso-4-excel.php';
            
            const cursoInput = document.createElement('input');
            cursoInput.type = 'hidden';
            cursoInput.name = 'curso';
            cursoInput.value = curso;
            form.appendChild(cursoInput);
            
            const grupoInput = document.createElement('input');
            grupoInput.type = 'hidden';
            grupoInput.name = 'grupo';
            grupoInput.value = grupo;
            form.appendChild(grupoInput);
            
            const yearInput = document.createElement('input');
            yearInput.type = 'hidden';
            yearInput.name = 'year';
            yearInput.value = year;
            form.appendChild(yearInput);
            
            if (idEstudiante) {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = idEstudiante;
                form.appendChild(idInput);
            }
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Ocultar overlay después de un tiempo
            setTimeout(() => {
                document.getElementById('overlay').style.display = 'none';
            }, 2000);
        }
    </script>
</body>


</html>