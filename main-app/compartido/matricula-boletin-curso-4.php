<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
    

$year=$_SESSION["bd"];
if(isset($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

$modulo = 1;
if(empty($_GET["periodo"])){
	$periodoActual = 1;
}else{
	$periodoActual = base64_decode($_GET["periodo"]);
}

//$periodoActual=2;

if ($periodoActual == 1) $periodoActuales = "Primero";

if ($periodoActual == 2) $periodoActuales = "Segundo";

if ($periodoActual == 3) $periodoActuales = "Tercero";

if ($periodoActual == 4) $periodoActuales = "Final";

// Configuración de visualización de áreas, indicadores, I.H y tamaño del logo
// Detectar si el formulario fue enviado usando un campo hidden que siempre se envía
$formularioEnviado = isset($_GET['config_aplicada']) && $_GET['config_aplicada'] == '1';

// Si el formulario fue enviado, usar los valores enviados, si no, usar valores por defecto
$mostrarAreas = $formularioEnviado 
    ? (isset($_GET['mostrar_areas']) ? (int)$_GET['mostrar_areas'] : 0)
    : 1; // Por defecto visible
$mostrarIndicadores = $formularioEnviado 
    ? (isset($_GET['mostrar_indicadores']) ? (int)$_GET['mostrar_indicadores'] : 0)
    : 1; // Por defecto visible
$mostrarIH = $formularioEnviado 
    ? (isset($_GET['mostrar_ih']) ? (int)$_GET['mostrar_ih'] : 0)
    : 1; // Por defecto visible

// Tamaño del logo (ancho y alto)
$logoAncho = $formularioEnviado && isset($_GET['logo_ancho']) && is_numeric($_GET['logo_ancho'])
    ? (int)$_GET['logo_ancho']
    : 200; // Por defecto 200px
$logoAlto = $formularioEnviado && isset($_GET['logo_alto']) && is_numeric($_GET['logo_alto'])
    ? (int)$_GET['logo_alto']
    : 0; // Por defecto 0 (auto) - mantiene proporción si solo se especifica ancho

// Configuración de visualización de elementos adicionales
$mostrarLogoEncabezado = $formularioEnviado 
    ? (isset($_GET['mostrar_logo_encabezado']) ? (int)$_GET['mostrar_logo_encabezado'] : 0)
    : 1; // Por defecto visible
$mostrarFirmas = $formularioEnviado 
    ? (isset($_GET['mostrar_firmas']) ? (int)$_GET['mostrar_firmas'] : 0)
    : 1; // Por defecto visible
$mostrarLogoPlataforma = $formularioEnviado 
    ? (isset($_GET['mostrar_logo_plataforma']) ? (int)$_GET['mostrar_logo_plataforma'] : 0)
    : 1; // Por defecto visible

?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

<?php
$filtro = "";
if (!empty($_GET["id"])) {

    $filtro .= " AND mat_id='" . base64_decode($_GET["id"]) . "'";
}

if (!empty($_REQUEST["curso"])) {

    $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}

if (!empty($_REQUEST["grupo"])) {

    $filtro .= " AND mat_grupo='" . base64_decode($_REQUEST["grupo"]) . "'";
}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro,$year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {

    //contador materias

    $cont_periodos = 0;

    $contador_indicadores = 0;

    $materiasPerdidas = 0;

    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
    $usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
    $num_usr = mysqli_num_rows($usr);

    $datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
    $nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);

    if ($num_usr == 0) {

?>

        <script type="text/javascript">
            window.close();
        </script>

    <?php

        exit();
    }



    $contador_periodos = 0;

    ?>

    <!doctype html>

    <!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->

    <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->

    <!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->

    <!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->

    <!--[if gt IE 8]><!-->

    <html class="no-js" lang="en">

    <!--<![endif]-->



    <head>

        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">

        <title>Boletín Formato 4</title>
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }

            /* Estilos profesionales para el boletín */
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                color: #333;
            }

            .header-boletin {
                background: #34495e;
                color: #FFF;
                font-weight: bold;
                height: 35px;
                font-size: 13px;
                letter-spacing: 0.5px;
            }

            .tabla-boletin {
                border: 1px solid #dee2e6;
                border-collapse: collapse;
                width: 100%;
                margin-top: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .tabla-boletin th, .tabla-boletin td {
                border: 1px solid #dee2e6;
                padding: 8px;
                text-align: center;
                font-size: 11px;
            }

            .tabla-boletin thead th {
                background: #34495e;
                color: #FFF;
                font-weight: bold;
                height: 30px;
                font-size: 12px;
                letter-spacing: 0.3px;
            }

            .area-row {
                background: #e9ecef;
                font-weight: bold;
                font-size: 12px;
                text-align: left;
            }

            .area-row td:first-child {
                text-align: left !important;
                padding-left: 15px !important;
            }

            .materia-row {
                background: #FFFFFF;
                font-size: 11px;
            }

            .materia-row:nth-child(even) {
                background: #f8f9fa;
            }

            .materia-row td:nth-child(2) {
                text-align: left !important;
                padding-left: 15px !important;
            }

            .indicador-row {
                background: #fdfdfd;
                font-size: 10px;
                text-align: left;
            }

            .indicador-row td:nth-child(2) {
                text-align: left !important;
                padding-left: 30px !important;
            }

            .nota-destacada {
                font-weight: 600;
                font-size: 12px;
            }

            .info-header {
                background: #2c3e50;
                color: #ffffff;
                padding: 12px 15px;
                font-weight: 600;
                letter-spacing: 0.3px;
            }

            .info-content {
                background: #ffffff;
                border: 1px solid #dee2e6;
                padding: 10px 15px;
                color: #495057;
                font-weight: 500;
            }

            .tabla-comportamiento {
                border: 1px solid #dee2e6;
                border-collapse: collapse;
                width: 100%;
                margin-top: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .tabla-comportamiento thead th {
                background: #34495e;
                color: #FFF;
                font-weight: bold;
                padding: 12px;
                font-size: 12px;
                letter-spacing: 0.3px;
            }

            .tabla-comportamiento tbody td {
                border: 1px solid #dee2e6;
                padding: 10px;
                font-size: 11px;
            }
        </style>

    </head>

    <body>

        <?php
        //CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
        $consulta_mat_area_est = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
        
        $numero_periodos = $config["conf_periodo"];

        // ============================================
        // OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
        // ============================================

        // OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para este estudiante y periodo
        $notasBoletinMapa = []; // [carga][periodo] => datos_nota
        try {
            $sqlNotas = "SELECT bol_carga, bol_periodo, bol_nota, bol_observaciones_boletin
                         FROM " . BD_ACADEMICA . ".academico_boletin
                         WHERE bol_estudiante = ?
                           AND institucion = ?
                           AND year = ?
                           AND bol_periodo = ?";
            $paramNotas = [
                $matriculadosDatos['mat_id'],
                $config['conf_id_institucion'],
                $year,
                $periodoActual
            ];
            $resNotas = BindSQL::prepararSQL($sqlNotas, $paramNotas);
            while ($rowNota = mysqli_fetch_array($resNotas, MYSQLI_BOTH)) {
                $idCarga = $rowNota['bol_carga'];
                $per = (int)$rowNota['bol_periodo'];
                if (!isset($notasBoletinMapa[$idCarga])) {
                    $notasBoletinMapa[$idCarga] = [];
                }
                $notasBoletinMapa[$idCarga][$per] = $rowNota;
            }
        } catch (Exception $eNotas) {
            include("../compartido/error-catch-to-report.php");
        }

        // OPTIMIZACIÓN 2: Pre-cargar todas las recuperaciones de indicadores para este estudiante y periodo
        // Nota: Se cargará dentro del bucle de áreas cuando tengamos las cargas disponibles
        $recuperacionesMapa = []; // [indicador][carga] => datos_recuperacion

        // OPTIMIZACIÓN 3: Pre-cargar cache de notas cualitativas
        $notasCualitativasCache = [];
        if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
            $consultaNotasTipo = mysqli_query($conexion, 
                "SELECT notip_desde, notip_hasta, notip_nombre 
                 FROM ".BD_ACADEMICA.".academico_notas_tipos 
                 WHERE notip_categoria='".mysqli_real_escape_string($conexion, $config['conf_notas_categoria'])."' 
                 AND institucion=".(int)$config['conf_id_institucion']." 
                 AND year='".mysqli_real_escape_string($conexion, $year)."' 
                 ORDER BY notip_desde ASC");
            if($consultaNotasTipo){
                $tiposNotas = [];
                while($tipoNota = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)){
                    $tiposNotas[] = $tipoNota;
                    // Pre-cargar cache para todos los valores posibles (de 0.1 en 0.1)
                    for($i = $tipoNota['notip_desde']; $i <= $tipoNota['notip_hasta']; $i += 0.1){
                        $key = number_format((float)$i, $config['conf_decimales_notas'], '.', '');
                        if(!isset($notasCualitativasCache[$key])){
                            $notasCualitativasCache[$key] = $tipoNota['notip_nombre'];
                        }
                    }
                }
            }
        }

        // OPTIMIZACIÓN 4: Cachear datos de usuarios (director y rector)
        $directorGrupo = null;
        $rector = null;
        $idDirector = null; // Se establecerá en el bucle de áreas
        ?>

        <!-- Formulario de Configuración (no se imprime) -->
        <div class="config-boletin-form" style="position: fixed; top: 10px; right: 10px; background: white; padding: 15px; border: 2px solid #34495e; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000; max-width: 300px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <h4 style="margin-top: 0; color: #34495e;">⚙️ Configuración del Boletín</h4>
            <form method="GET" id="configBoletinForm">
                <?php
                // Mantener todos los parámetros GET existentes
                if(!empty($_GET["id"])) echo '<input type="hidden" name="id" value="'.htmlspecialchars($_GET["id"]).'">';
                if(!empty($_GET["periodo"])) echo '<input type="hidden" name="periodo" value="'.htmlspecialchars($_GET["periodo"]).'">';
                if(!empty($_REQUEST["curso"])) echo '<input type="hidden" name="curso" value="'.htmlspecialchars($_REQUEST["curso"]).'">';
                if(!empty($_REQUEST["grupo"])) echo '<input type="hidden" name="grupo" value="'.htmlspecialchars($_REQUEST["grupo"]).'">';
                if(!empty($_GET["year"])) echo '<input type="hidden" name="year" value="'.htmlspecialchars($_GET["year"]).'">';
                // Campo hidden para detectar que el formulario fue enviado
                echo '<input type="hidden" name="config_aplicada" value="1">';
                ?>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_areas" value="1" <?= $mostrarAreas ? 'checked' : '' ?>>
                    Mostrar filas de áreas
                </label>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_indicadores" value="1" <?= $mostrarIndicadores ? 'checked' : '' ?>>
                    Mostrar filas de indicadores
                </label>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_ih" value="1" <?= $mostrarIH ? 'checked' : '' ?>>
                    Mostrar columna I.H (Intensidad Horaria)
                </label>
                <div style="margin: 15px 0;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tamaño del Logo:</label>
                    <label style="display: block; margin-bottom: 5px;">
                        Ancho (px): <input type="number" name="logo_ancho" value="<?= $logoAncho ?>" min="1" style="width: 60px;">
                    </label>
                    <label style="display: block; margin-bottom: 5px;">
                        Alto (px, 0=auto): <input type="number" name="logo_alto" value="<?= $logoAlto ?>" min="0" style="width: 60px;">
                    </label>
                </div>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_logo_encabezado" value="1" <?= $mostrarLogoEncabezado ? 'checked' : '' ?>>
                    Mostrar logo del encabezado
                </label>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_firmas" value="1" <?= $mostrarFirmas ? 'checked' : '' ?>>
                    Mostrar firmas del pie de página
                </label>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_logo_plataforma" value="1" <?= $mostrarLogoPlataforma ? 'checked' : '' ?>>
                    Mostrar logo y leyenda de SINTIA
                </label>
                <button type="submit" style="background: #34495e; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%;">Aplicar Configuración</button>
            </form>
        </div>
        <style>
            @media print {
                .config-boletin-form { display: none !important; }
                /* Evitar saltos de página entre tabla, firmas y logo SINTIA */
                table[width="100%"][rules="none"] {
                    page-break-inside: avoid;
                }
                div[align="center"][style*="font-size:10px"] {
                    page-break-inside: avoid;
                }
            }
        </style>

        <?php if($mostrarLogoEncabezado): ?>
        <div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="<?= $logoAncho ?>" <?= $logoAlto > 0 ? 'height="'.$logoAlto.'"' : '' ?>><br>
    <!-- <?=$informacion_inst["info_nombre"]?><br>
    BOLETÍN DE CALIFICACIONES<br> -->

        </div>
        <?php endif; ?>





        <table width="100%" cellspacing="0" cellpadding="0" border="0" rules="none" style="margin-bottom: 15px;">

            <tr class="info-header">

                <td style="padding: 12px 15px;">Documento:<br> <b><?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></b></td>

                <td style="padding: 12px 15px;">Nombre:<br> <b><?=$nombre?></b></td>

                <td style="padding: 12px 15px;">Grado:<br> <b><?= $datosUsr["gra_nombre"] . " " . $datosUsr["gru_nombre"]; ?></b></td>

                <td style="padding: 12px 15px;">&nbsp;</td>

            </tr>

            <tr class="info-content">

                <td style="padding: 10px 15px;">Jornada:<br> Mañana</td>

                <td style="padding: 10px 15px;">Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>

                <td style="padding: 10px 15px;">Periodo:<br> <b><?= $periodoActuales . " (" . $year . ")"; ?></b></td>

                <td style="padding: 10px 15px;">Fecha Impresión:<br> <?= date("d/m/Y H:i:s"); ?></td>

            </tr>

        </table>

        <p>&nbsp;</p>



        <table width="100%" id="tblBoletin" class="tabla-boletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

            <tr class="header-boletin">

                <td width="2%" align="center">NO</td>

                <td width="20%" align="center">AREAS/ ASIGNATURAS</td>

                <?php if($mostrarIH): ?>
                <td width="2%" align="center">I.H</td>
                <?php endif; ?>

                <td width="2%" align="center">NOTA</td>

                <td width="2%" align="center">DEF.</td>

            </tr>



            <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->

            <?php

            $contador = 1;

            while ($fila = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {
                //DIRECTOR DE GRUPO
                if($fila["car_director_grupo"]==1){
                    $idDirector=$fila["car_docente"];
                }

                $sumaValorMaterias = 0;

                if ($periodoActual == 1) {

                    $condicion = "1";

                    $condicion2 = "1";
                }

                if ($periodoActual == 2) {

                    $condicion = "1,2";

                    $condicion2 = "2";
                }

                if ($periodoActual == 3) {

                    $condicion = "1,2,3";

                    $condicion2 = "3";
                }

                if ($periodoActual == 4) {

                    $condicion = "1,2,3,4";

                    $condicion2 = "4";
                }



                //CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA
                $consulta_notdef_area = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
                $consulta_a_mat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
                $consulta_a_mat_per = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
                // Solo ejecutar la consulta si se van a mostrar los indicadores (optimización)
                $consulta_a_mat_indicadores = null;
                $numIndicadores = 0;
                if($mostrarIndicadores){
                    $consulta_a_mat_indicadores = Boletin::obtenerIndicadoresPorMateria($datosUsr["mat_grado"], $datosUsr["mat_grupo"], $fila["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);
                    $numIndicadores = mysqli_num_rows($consulta_a_mat_indicadores);
                }

                $resultado_not_area = mysqli_fetch_array($consulta_notdef_area, MYSQLI_BOTH);

                $numfilas_not_area = mysqli_num_rows($consulta_notdef_area);

                if ($numfilas_not_area > 0) { 
                    $sumaValorMaterias = Asignaturas::sumarValorAsignaturasArea($conexion, $config, $matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $resultado_not_area["ar_id"], $year);
                    // Mostrar fila del área solo si está habilitado
                    if ($mostrarAreas) {
            ?>
                    <tr class="area-row">
                        <td colspan="<?= $mostrarIH ? '2' : '1'; ?>" align="left" style="font-size:12px; height:25px; font-weight:bold; padding-left: 15px;"><?php echo $resultado_not_area["ar_nombre"]." (".$sumaValorMaterias[0]."%)"; ?></td>
                        <?php if($mostrarIH): ?>
                        <td align="center" style="font-weight:bold; font-size:12px;"></td>
                        <?php endif; ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
            <?php
                    }
                    // Las materias siempre se muestran, independientemente de si se muestra el área
                    $sumaNotasPorArea = 0;
                    while ($fila2 = mysqli_fetch_array($consulta_a_mat, MYSQLI_BOTH)) {

                        $contador_periodos = 0;

                        mysqli_data_seek($consulta_a_mat_per, 0);

                        // OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
                        $datosBoletin = $notasBoletinMapa[$fila2['car_id']][$periodoActual] ?? null;
                        if($datosBoletin === null){
                            // Fallback: consulta individual si no está en el mapa
                            $resTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
                            if($resTemp){
                                $datosBoletin = mysqli_fetch_array($resTemp, MYSQLI_BOTH);
                            } else {
                                $datosBoletin = [];
                            }
                        }

                        $notaFinal = Boletin::obtenerPromedioPorTodasLasCargas($matriculadosDatos['mat_id'], $fila2["car_id"], $year);


                        //Calculo
                        $sumaNotasPorArea += !empty($datosBoletin['bol_nota']) && !empty($fila2["mat_valor"]) ? $datosBoletin['bol_nota'] * ($fila2["mat_valor"] / 100) : 0;

                        $notaBoletin = !empty($datosBoletin['bol_nota']) ? (float)$datosBoletin['bol_nota'] : '';
                        $notaDefFinal = !empty($notaFinal['def']) ? (float)$notaFinal['def'] : '';
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                            // OPTIMIZACIÓN: Usar cache de notas cualitativas
                            if(!empty($datosBoletin['bol_nota'])){
                                $notaRedondeada = number_format((float)$datosBoletin['bol_nota'], $config['conf_decimales_notas'], '.', '');
                                $notaBoletin = isset($notasCualitativasCache[$notaRedondeada]) 
                                    ? $notasCualitativasCache[$notaRedondeada] 
                                    : ($datosBoletin['notip_nombre'] ?? '');
                            }
                            // OPTIMIZACIÓN: Usar cache de notas cualitativas para definitiva
                            if(!empty($notaFinal['def'])){
                                $notaDefRedondeada = number_format((float)$notaFinal['def'], $config['conf_decimales_notas'], '.', '');
                                $notaDefFinal = isset($notasCualitativasCache[$notaDefRedondeada]) 
                                    ? $notasCualitativasCache[$notaDefRedondeada] 
                                    : "";
                            } else {
                                $notaDefFinal = "";
                            }
                        } else {
                            // Formatear notas numéricas con decimales configurados
                            if(!empty($notaBoletin)){
                                $notaBoletin = Boletin::notaDecimales($notaBoletin);
                            }
                            if(!empty($notaDefFinal)){
                                $notaDefFinal = Boletin::notaDecimales($notaDefFinal);
                            }
                        }

                    ?>

                        <tr class="materia-row">

                            <td align="center"><?= $contador; ?></td>

                            <td align="left" style="font-size:12px; height:35px; font-weight:bold; padding-left: 15px;">&raquo; <?php echo $fila2["mat_nombre"]." (".$fila2["mat_valor"]."%)"; ?></td>

                            <?php if($mostrarIH): ?>
                            <td align="center" style="font-weight:bold; font-size:12px;"><?php echo $fila["car_ih"]; ?></td>
                            <?php endif; ?>

                            <td align="center" class="nota-destacada" style="font-weight:bold; font-size:14px;"><?=$notaBoletin;?></td>

                            <td align="center" class="nota-destacada" style="font-weight:bold; font-size:15px;"><?=$notaDefFinal;?></td>

                        </tr>

                        <?php

                        if ($mostrarIndicadores && $numIndicadores > 0) {

                            mysqli_data_seek($consulta_a_mat_indicadores, 0);

                            $contador_indicadores = 0;

                            while ($fila4 = mysqli_fetch_array($consulta_a_mat_indicadores, MYSQLI_BOTH)) {

                                if ($fila4["mat_id"] == $fila2["mat_id"]) {
                                    // OPTIMIZACIÓN: Obtener recuperación del mapa pre-cargado
                                    $recuperacionIndicador = $recuperacionesMapa[$fila4["ind_id"]][$fila2["car_id"]] ?? null;
                                    if($recuperacionIndicador === null){
                                        // Fallback: consulta individual si no está en el mapa
                                        $consultaRecuperacion = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $fila4["ind_id"], $matriculadosDatos['mat_id'], $fila2["car_id"], $periodoActual, $year);
                                        $recuperacionIndicador = mysqli_fetch_array($consultaRecuperacion, MYSQLI_BOTH);
                                        // Guardar en el mapa para próximas iteraciones
                                        if($recuperacionIndicador){
                                            if(!isset($recuperacionesMapa[$fila4["ind_id"]])){
                                                $recuperacionesMapa[$fila4["ind_id"]] = [];
                                            }
                                            $recuperacionesMapa[$fila4["ind_id"]][$fila2["car_id"]] = $recuperacionIndicador;
                                        }
                                    }

                                    $contador_indicadores++;
                                    $leyendaRI = '';
                                    if(!empty($recuperacionIndicador['rind_nota']) && $recuperacionIndicador['rind_nota']>$fila4["nota"]){
                                        $nota_indicador = (float)$recuperacionIndicador['rind_nota'];
                                        $leyendaRI = '<br><span style="color:navy; font-size:9px;">Recuperdo.</span>';
                                    }else{
                                        $nota_indicador = !empty($fila4["nota"]) ? (float)$fila4["nota"] : 0;
                                    }

                                    $nota_indicador = Boletin::notaDecimales($nota_indicador);

                                    $notaIndicadorFinal=$nota_indicador;
                                    if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                        // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                        $notaIndRedondeada = number_format((float)$nota_indicador, $config['conf_decimales_notas'], '.', '');
                                        $notaIndicadorFinal = isset($notasCualitativasCache[$notaIndRedondeada]) 
                                            ? $notasCualitativasCache[$notaIndRedondeada] 
                                            : "";
                                    }

                        ?>

                                    <tr class="indicador-row">

                                        <td align="center">&nbsp;</td>

                                        <td align="left" style="font-size:12px; height:15px; padding-left: 30px;"><?php echo $contador_indicadores . "." . $fila4["ind_nombre"]; ?></td>

                                        <?php if($mostrarIH): ?>
                                        <td>&nbsp;</td>
                                        <?php endif; ?>

                                        <td align="center" class="nota-destacada" style="font-weight:bold; font-size:12px;"><?= $notaIndicadorFinal." ".$leyendaRI; ?></td>

                                        <td>&nbsp;</td>

                                    </tr>

                        <?php

                                } //fin if

                            }
                        }

                        ?>





                        <!-- observaciones de la asignatura-->
                        <?php
                        // OPTIMIZACIÓN: Obtener observación del mapa pre-cargado
                        $observacion = $notasBoletinMapa[$fila2['car_id']][$periodoActual] ?? null;
                        if($observacion === null){
                            // Fallback: consulta individual si no está en el mapa
                            $obsTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
                            if($obsTemp){
                                $observacion = mysqli_fetch_array($obsTemp, MYSQLI_BOTH);
                            } else {
                                $observacion = [];
                            }
                        }

                        if (!empty($observacion['bol_observaciones_boletin'])) {

                        ?>

                            <tr>

                                <td colspan="5">

                                    <h5 align="center">Observaciones</h5>

                                    <p style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">

                                        <?= $observacion['bol_observaciones_boletin']; ?>

                                    </p>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php

                        $contador++;
                    } //while fin materias

                    ?>

            <?php }
            } //while fin areas

            ?>

        </table>



        <p>&nbsp;</p>

        <?php

        // OPTIMIZACIÓN: Usar prepared statements para la consulta de disciplina
        $condicionEsc = mysqli_real_escape_string($conexion, $condicion);
        $idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
        $cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='" . $idEstudianteEsc . "' AND institucion=" . (int)$config['conf_id_institucion'] . " AND year=" . (int)$year . " AND dn_periodo IN(" . $condicionEsc . ")");
        
        // Inicializar $num_observaciones basado en la consulta de disciplina
        $num_observaciones = 0;
        if($cndisiplina){
            $num_observaciones = mysqli_num_rows($cndisiplina);
        }

        if (@mysqli_num_rows($cndisiplina) > 0) {

        ?>

            <table width="100%" class="tabla-comportamiento" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

                <tr>
                    <th colspan="3" style="text-align:center">OBSERVACIONES DE CONVIVENCIA</th>
                </tr>

                <tr>
                    <th width="8%">Periodo</th>
                    <th>Observaciones</th>
                </tr>

                <?php

                while ($rndisiplina = mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)) {
                    // OPTIMIZACIÓN: Usar cache de notas cualitativas
                    $desempenoND = null;
                    if($config['conf_forma_mostrar_notas'] == CUALITATIVA && !empty($rndisiplina["dn_nota"])){
                        $notaRedondeada = number_format((float)$rndisiplina["dn_nota"], $config['conf_decimales_notas'], '.', '');
                        $desempenoND = isset($notasCualitativasCache[$notaRedondeada]) 
                            ? ['notip_nombre' => $notasCualitativasCache[$notaRedondeada]] 
                            : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rndisiplina["dn_nota"], $year);
                    }

                ?>

                    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">

                        <td style="text-align:center;"><?= $rndisiplina["dn_periodo"] ?></td>

                        <td align="left" style="padding-left: 15px;"><?= $rndisiplina["dn_observacion"] ?></td>

                    </tr>

                <?php } ?>

            </table>

        <?php } ?>

        <!--<hr align="center" width="100%">-->

        <div align="center">

            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="text-align:center; font-size:12px;">

                <tr>

                    <td style="font-weight:bold;" align="left"><?php if ($num_observaciones > 0) { ?>

                            COMPORTAMIENTO:

                        <?php } ?>

                        <b><u>

                                <!-- <?= strtoupper($r_diciplina[3]); ?> -->

                            </u></b><br>

                        <?php

                        ?></td>

                </tr>

            </table>

            <?php

            //print_r($vectorT);

            ?>

        </div>

        <!--

<div>

<table width="100%" cellspacing="0" cellpadding="0"  border="0" style="text-align:center; font-size:12px;">

  <tr>

    <td style="font-weight:bold;" align="left">

    OBSERVACIONES:_____________________________________________________________________________________________________________<br><br>

    ____________________________________________________________________________________________________________________________<br><br>

    ____________________________________________________________________________________________________________________________<br>

    </td>

  </tr>

</table>



</div>

-->




        <!--******FIRMAS******-->   
        <?php if($mostrarFirmas): ?>
        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px; margin-top: 5px;">
            <tr>
                <td align="center">
                    <?php
                        // OPTIMIZACIÓN: Cargar director solo una vez
                        if($directorGrupo === null && !empty($idDirector)){
                            $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                        }
                        if($directorGrupo){
                            $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
                            if(!empty($directorGrupo["uss_firma"])){
                                echo '<img src="../files/fotos/'.$directorGrupo["uss_firma"].'" width="100"><br>';
                            }else{
                                echo '<p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                            }
                            echo '<p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                '.$nombreDirectorGrupo.'<br>
                                Director(a) de grupo';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                <br>
                                Director(a) de grupo';
                        }
                    ?>
                </td>
                <td align="center">
                    <?php
                        // OPTIMIZACIÓN: Cargar rector solo una vez
                        if($rector === null && !empty($informacion_inst["info_rector"])){
                            $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                        }
                        if($rector){
                            $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                            if(!empty($rector["uss_firma"])){
                                echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="100"><br>';
                            }else{
                                echo '<p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                            }
                            echo '<p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                '.$nombreRector.'<br>
                                Rector(a)';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                <br>
                                Rector(a)';
                        }
                    ?>
                </td>
            </tr>
        </table>
        <?php endif; ?>





        <!--

<br>

<div align="center">

<table width="100%" cellspacing="0" cellpadding="0"  border="1" style="text-align:center; font-size:8px; background:#FFFFCC;">

  <tr style="text-transform:uppercase;">

    <td style="font-weight:bold;" align="right">ESCALA NACIONAL</td><td>Desempe&ntilde;o Superior</td><td>Desempe&ntilde;o Alto</td><td>Desempe&ntilde;o B&aacute;sico</td><td>Desempe&ntilde;o Bajo</td>

  </tr>

  

  <tr>

  	<td style="font-weight:bold;" align="right">RANGO INSTITUCIONAL</td>

  	<td>NO HAY</td><td>NO HAY</td><td>NO HAY</td><td>NO HAY</td>  

  </tr>



</table>

-->



        </div>

        <?php

        /*if ($periodoActual == 4) {

            if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"])

                $msj = "<center>EL (LA) ESTUDIANTE " . strtoupper($datosUsr['mat_primer_apellido'] . " " . $datosUsr['mat_segundo_apellido'] . " " . $datosUsr["mat_nombres"]) . " NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";

            elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] and $materiasPerdidas > 0)

                $msj = "<center>EL (LA) ESTUDIANTE " . strtoupper($datosUsr['mat_primer_apellido'] . " " . $datosUsr['mat_segundo_apellido'] . " " . $datosUsr["mat_nombres"]) . " DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";

            else

                $msj = "<center>EL (LA) ESTUDIANTE " . strtoupper($datosUsr['mat_primer_apellido'] . " " . $datosUsr['mat_segundo_apellido'] . " " . $datosUsr["mat_nombres"]) . " FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
        }*/

        // Inicializar $msj para evitar warning
        $msj = "";

        ?>

        <?php if(!empty($msj)): ?>
        <p align="center" style="margin: 5px 0;">
            <div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:12px;" align="center">
                <?= $msj; ?>
            </div>
        </p>
        <?php endif; ?>

        <?php if($mostrarLogoPlataforma): ?>
        <div align="center" style="font-size:10px; margin-top: 2px; margin-bottom: 10px;">

            <img src="https://plataformasintia.com/images/logo.png" height="50"><br>

            ESTE DOCUMENTO FUE GENERADO POR:<br>

            SINTIA - SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL

        </div>
        <?php endif; ?>



        <div id="saltoPagina"></div>

    <?php

} // FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

    ?>

    <script type="application/javascript">
        print();
    </script>

    </body>



    </html>