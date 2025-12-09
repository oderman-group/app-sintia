<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH."/main-app/class/Indicadores.php";
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}
$modulo = 1;

$periodoActual = base64_decode($_GET["periodo"]);
if (empty($_GET["periodo"])) {
    $periodoActual = 1;
}

switch($periodoActual){
    case 1:
        $periodoActuales = "Primero";
        $celdas = 2;
        break;
    case 2:
        $periodoActuales = "Segundo";
        $celdas = 4;
        break;
    case 3:
        $periodoActuales = "Tercero";
        $celdas = 6;
        break;
    case 4:
        $periodoActuales = "Final";
        $celdas = 8;
        break;
}
$colspan=5+$celdas;

// Configuración de visualización de elementos
$formularioEnviado = isset($_GET['config_aplicada']) && $_GET['config_aplicada'] == '1';

$mostrarEncabezado = $formularioEnviado 
    ? (isset($_GET['mostrar_encabezado']) ? (int)$_GET['mostrar_encabezado'] : 0)
    : 1; // Por defecto visible
$mostrarFirmas = $formularioEnviado 
    ? (isset($_GET['mostrar_firmas']) ? (int)$_GET['mostrar_firmas'] : 0)
    : 1; // Por defecto visible
$mostrarIndicadores = $formularioEnviado 
    ? (isset($_GET['mostrar_indicadores']) ? (int)$_GET['mostrar_indicadores'] : 0)
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
$contadorEstudiantes=0;
$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
$numeroEstudiantes = mysqli_num_rows($matriculadosPorCurso);
while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {
    $gradoActual = $matriculadosDatos['mat_grado'];
    $grupoActual = $matriculadosDatos['mat_grupo'];
    //contador materias
    $contPeriodos = 0;
    $contadorIndicadores = 0;
    $materiasPerdidas = 0;
    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
    $consultaEstudiantes = Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
    $numEstudiantes = mysqli_num_rows($consultaEstudiantes);
    $datosEstudiantes = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH);
    //METODO QUE ME TRAE EL NOMBRE COMPLETO DEL ESTUDIANTE
    $nombreEstudainte=Estudiantes::NombreCompletoDelEstudiante($datosEstudiantes);
    if ($numEstudiantes == 0) {
    ?>
        <script type="text/javascript">
            window.close();
        </script>
    <?php
        exit();
    }
    $contadorPeriodos = 0;
    $contp = 1;
    $puestoCurso = 0;
    $puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual, $matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
    
    foreach($puestos as $puesto){
        if($puesto['estudiante_id']==$matriculadosDatos['mat_id']){$puestoCurso = $contp;}
        $contp ++;
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Boletín Formato 11</title>
        <!-- favicon -->
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }
        </style>
    </head>

    <body style="font-family:Arial;">
        <?php if($contadorEstudiantes == 0): ?>
        <div class="config-boletin-form" style="position: fixed; top: 10px; right: 10px; background: white; padding: 15px; border: 2px solid #34495e; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000; max-width: 300px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <h4 style="margin-top: 0; color: #34495e;">⚙️ Configuración del Boletín</h4>
            <form method="GET" id="configBoletinForm">
                <?php
                // Mantener todos los parámetros GET existentes
                if(!empty($_GET["id"])) echo '<input type="hidden" name="id" value="'.htmlspecialchars($_GET["id"]).'">';
                if(!empty($_GET["periodo"])) echo '<input type="hidden" name="periodo" value="'.htmlspecialchars($_GET["periodo"]).'">';
                if(!empty($_GET["curso"])) echo '<input type="hidden" name="curso" value="'.htmlspecialchars($_GET["curso"]).'">';
                if(!empty($_GET["grupo"])) echo '<input type="hidden" name="grupo" value="'.htmlspecialchars($_GET["grupo"]).'">';
                if(!empty($_GET["year"])) echo '<input type="hidden" name="year" value="'.htmlspecialchars($_GET["year"]).'">';
                echo '<input type="hidden" name="config_aplicada" value="1">';
                ?>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_encabezado" value="1" <?= $mostrarEncabezado ? 'checked' : '' ?>>
                    Mostrar encabezado completo
                </label>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_firmas" value="1" <?= $mostrarFirmas ? 'checked' : '' ?>>
                    Mostrar firmas del pie de página
                </label>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" name="mostrar_indicadores" value="1" <?= $mostrarIndicadores ? 'checked' : '' ?>>
                    Mostrar segunda hoja (Indicadores de desempeño)
                </label>
                <button type="submit" style="background: #34495e; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%;">Aplicar Configuración</button>
            </form>
        </div>
        <style>
            @media print {
                .config-boletin-form { display: none !important; }
            }
        </style>
        <?php endif; ?>
        <?php
        //CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
        $consultaAreaEstudiante = Boletin::obtenerAreasDelEstudiante($matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
        ?>
        <?php if($mostrarEncabezado): ?>
        <div align="center" style="margin-bottom:20px;">
            <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="50"><br>
            <?= $informacion_inst["info_nombre"] ?><br>BOLETÍN DE CALIFICACIONES<br>
        </div>
        <?php endif; ?>
        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">
            <tr>
                <td>Documento:<br> <?=strpos($datosEstudiantes["mat_documento"], '.') !== true && is_numeric($datosEstudiantes["mat_documento"]) ? number_format($datosEstudiantes["mat_documento"],0,",",".") : $datosEstudiantes["mat_documento"];?></td>
                <td>Nombre:<br> <?= $nombreEstudainte; ?></td>
                <td>Grado:<br> <?= $datosEstudiantes["gra_nombre"] . " " . $datosEstudiantes["gru_nombre"]; ?></td>
                <td>Puesto Curso:<br> <?=$puestoCurso?></td>    
            </tr>
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>
                <td>Periodo (Año):<br> <b><?= $periodoActuales . " (" . $year . ")"; ?></b></td>
                <td>Fecha Impresión:<br> <?= date("d/m/Y H:i:s"); ?></td>
            </tr>
        </table>
        <table width="100%" style="margin-bottom: 15px;" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
                <tr style="font-weight:bold; background-color:#00adefad; border-color:#000; color:#000; font-size:12px;">
                    <td width="1%" align="center" rowspan="2">Nº</td>
                    <td width="20%" align="center" rowspan="2">AREAS/ ASIGNATURAS</td>
                    <td width="2%" align="center" rowspan="2">I.H</td>
                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="2%" align="center" colspan="2"><a href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $matriculadosDatos['mat_id']; ?>&periodo=<?= $j ?>" style="color:#000; text-decoration:none;">Periodo <?= $j ?></a></td>
                    <?php } ?>
                    <td width="3%" colspan="3" align="center">Acumulado</td>
                </tr>

                <tr style="font-weight:bold; text-align:center; background-color:#00adefad; border-color:#000; color:#000; font-size:12px;">
                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="1%">Nota</td>
                        <td width="1%">Desempeño</td>
                    <?php } ?>
                    <td width="1%">Nota</td>
                    <td width="1%">Desempeño</td>
                </tr>
            <?php
            $contador = 1;
            $matPromedio = 0;
            $ausPer1Total=0;
            $ausPer2Total=0;
            $ausPer3Total=0;
            $ausPer4Total=0;
            $sumAusenciasTotal=0;
            $sumaNotaP1 = 0;
            $sumaNotaP2 = 0;
            $sumaNotaP3 = 0;
            $sumaNotaP4 = 0;
            
            // ============================================
            // OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
            // ============================================
            
            // OPTIMIZACIÓN 1: Pre-cargar todas las ausencias para este estudiante y todos los períodos
            $ausenciasMapa = []; // [materia_id][periodo] => suma_ausencias
            try {
                // Obtener todas las materias del estudiante
                $idsMaterias = [];
                mysqli_data_seek($consultaAreaEstudiante, 0);
                while ($areaTemp = mysqli_fetch_array($consultaAreaEstudiante, MYSQLI_BOTH)) {
                    $consultaDefinitivaNombreMateriaTemp = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $areaTemp["ar_id"], "1,2,3,4", $year);
                    while ($materiaTemp = mysqli_fetch_array($consultaDefinitivaNombreMateriaTemp, MYSQLI_BOTH)) {
                        if (!in_array($materiaTemp['mat_id'], $idsMaterias)) {
                            $idsMaterias[] = $materiaTemp['mat_id'];
                        }
                    }
                }
                mysqli_data_seek($consultaAreaEstudiante, 0);
                
                if (!empty($idsMaterias)) {
                    for($j=1; $j<=$periodoActual; $j++){
                        foreach($idsMaterias as $idMateria){
                            $ausTemp = Boletin::obtenerDatosAusencias($datosEstudiantes['gra_id'], $idMateria, $j, $datosEstudiantes['mat_id'], $year);
                            $ausData = mysqli_fetch_array($ausTemp, MYSQLI_BOTH);
                            if(!empty($ausData['sumAus']) && $ausData['sumAus'] > 0){
                                if (!isset($ausenciasMapa[$idMateria])) {
                                    $ausenciasMapa[$idMateria] = [];
                                }
                                $ausenciasMapa[$idMateria][$j] = (float)$ausData['sumAus'];
                            }
                        }
                    }
                }
            } catch (Exception $eAus) {
                include("../compartido/error-catch-to-report.php");
            }
            
            // OPTIMIZACIÓN 2: Pre-cargar indicadores por materia y período
            $indicadoresMateriaPeriodoMapa = []; // [area_id][periodo] => array de indicadores
            try {
                mysqli_data_seek($consultaAreaEstudiante, 0);
                while ($areaTemp = mysqli_fetch_array($consultaAreaEstudiante, MYSQLI_BOTH)) {
                    $arId = $areaTemp['ar_id'];
                    if (!isset($indicadoresMateriaPeriodoMapa[$arId])) {
                       	$indicadoresMateriaPeriodoMapa[$arId] = [];
                    }
                    for($j=1; $j<=$periodoActual; $j++){
                        if (!isset($indicadoresMateriaPeriodoMapa[$arId][$j])) {
                            $indicadoresMateriaPeriodoMapa[$arId][$j] = [];
                            $indicadoresTemp = Boletin::obtenerIndicadoresDeMateriaPorPeriodo($datosEstudiantes["mat_grado"], $datosEstudiantes["mat_grupo"], $arId, $j, $matriculadosDatos['mat_id'], $year);
                            while ($indTemp = mysqli_fetch_array($indicadoresTemp, MYSQLI_BOTH)) {
                                $indicadoresMateriaPeriodoMapa[$arId][$j][] = $indTemp;
                            }
                        }
                    }
                }
                mysqli_data_seek($consultaAreaEstudiante, 0);
            } catch (Exception $eInd) {
                include("../compartido/error-catch-to-report.php");
            }
            
            // OPTIMIZACIÓN 3: Pre-cargar nivelaciones y observaciones
            $nivelacionesMapa = []; // [car_id] => datos_nivelacion
            $observacionesMapa = []; // [car_id] => datos_observacion
            try {
                mysqli_data_seek($consultaAreaEstudiante, 0);
                while ($areaTemp = mysqli_fetch_array($consultaAreaEstudiante, MYSQLI_BOTH)) {
                    $consultaDefinitivaNombreMateriaTemp = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $areaTemp["ar_id"], "1,2,3,4", $year);
                    while ($materiaTemp = mysqli_fetch_array($consultaDefinitivaNombreMateriaTemp, MYSQLI_BOTH)) {
                        $idCarga = $materiaTemp['car_id'];
                        if (!isset($nivelacionesMapa[$idCarga])) {
                            $nivTemp = Boletin::obtenerNivelaciones($idCarga, $matriculadosDatos['mat_id'], $year);
                            $niv = mysqli_fetch_array($nivTemp, MYSQLI_BOTH);
                            if ($niv) {
                                $nivelacionesMapa[$idCarga] = $niv;
                            }
                        }
                        if ($materiaTemp['car_observaciones_boletin'] == 1 && !isset($observacionesMapa[$idCarga])) {
                            $obsTemp = Boletin::obtenerObservaciones($idCarga, $periodoActual, $matriculadosDatos['mat_id'], $year);
                            $obs = mysqli_fetch_array($obsTemp, MYSQLI_BOTH);
                            if ($obs) {
                                $observacionesMapa[$idCarga] = $obs;
                            }
                        }
                    }
                }
                mysqli_data_seek($consultaAreaEstudiante, 0);
            } catch (Exception $eNiv) {
                include("../compartido/error-catch-to-report.php");
            }
            
            // OPTIMIZACIÓN 4: Pre-cargar cache de notas cualitativas
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
                    while($tipoNota = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)){
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
            
            // OPTIMIZACIÓN 5: Cachear datos de usuarios (director y rector)
            $directorGrupo = null;
            $rector = null;
            $idDirector = null; // Se establecerá en el bucle de materias
            
            while ($area = mysqli_fetch_array($consultaAreaEstudiante, MYSQLI_BOTH)) {
                switch($periodoActual){
                    case 1:
                        $condicion = "1";
                        $condicion2 = "1";
                        break;
                    case 2:
                        $condicion = "1,2";
                        $condicion2 = "2";
                        break;
                    case 3:
                        $condicion = "1,2,3";
                        $condicion2 = "3";
                        break;
                    case 4:
                        $condicion = "1,2,3,4";
                        $condicion2 = "4";
                        break;
                }
                //CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA
                $consultanombrePromedioArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $area["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
                $consultaDefinitivaNombreMateria = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $area["ar_id"], $condicion, $year);

                //CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
                $consultaDefinitivaPeriodo = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $area["ar_id"], $condicion, $year);
                
                //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
                $consultaMateriaIndicadores = Boletin::obtenerIndicadoresPorMateria($datosEstudiantes["mat_grado"], $datosEstudiantes["mat_grupo"], $area["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);

                $numIndicadores = mysqli_num_rows($consultaMateriaIndicadores);
                $resultadoNotaArea = mysqli_fetch_array($consultanombrePromedioArea, MYSQLI_BOTH);
                $numfilasNotaArea = mysqli_num_rows($consultanombrePromedioArea);
                if ($numfilasNotaArea > 0) {
            ?>
                
                
                    <tr style="background-color: #EAEAEA" style="font-size:12px;">
                        <td colspan="<?=$colspan?>" style="font-size:12px; font-weight:bold;"><?=$resultadoNotaArea["ar_nombre"]; ?></td>
                    </tr>
                    <?php
                    while ($materia = mysqli_fetch_array($consultaDefinitivaNombreMateria, MYSQLI_BOTH)) {
                        //DIRECTOR DE GRUPO
                        if($materia["car_director_grupo"]==1){
                            $idDirector=$materia["car_docente"];
                            // OPTIMIZACIÓN: Cargar director solo una vez
                            if($directorGrupo === null && !empty($idDirector)){
                                $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                            }
                        }

                        $sumAusencias=0;
                        $ausPer1=0;
                        $ausPer2=0;
                        $ausPer3=0;
                        $ausPer4=0;
                        for($j = 1; $j <= $periodoActual; $j++){
                            // OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
                            $datosAusencias = ['sumAus' => ($ausenciasMapa[$materia['mat_id']][$j] ?? 0)];
        
                            if($datosAusencias['sumAus']>0){
                                switch($j){
                                    case 1:
                                        $ausPer1+=$datosAusencias['sumAus'];
                                        break;
                                    case 2:
                                        $ausPer2+=$datosAusencias['sumAus'];
                                        break;
                                    case 3:
                                        $ausPer3+=$datosAusencias['sumAus'];
                                        break;
                                    case 4:
                                        $ausPer4+=$datosAusencias['sumAus'];
                                        break;
                                }
                                $sumAusencias+=$datosAusencias['sumAus'];
                            }
                        }

                        $contadorPeriodos = 0;
                        mysqli_data_seek($consultaDefinitivaPeriodo, 0);
                    ?>
                        <tr>
                            <td align="center"><?= $contador; ?></td>
                            <td style="font-size:12px; font-weight:bold;"><?=$materia["mat_nombre"]; ?></td>
                            <td align="center" style="font-size:12px;"><?=$materia["car_ih"]; ?></td>
                            <?php
                                $promedioMateria = 0;
                                for ($j = 1; $j <= $periodoActual; $j++) {
                                    // OPTIMIZACIÓN: Obtener indicadores del mapa pre-cargado
                                    $consultaNotaMateriaIndicadoresxPeriodo = $indicadoresMateriaPeriodoMapa[$area["ar_id"]][$j] ?? [];

                                    $numIndicadoresPorPeriodo = count($consultaNotaMateriaIndicadoresxPeriodo);
                                    $sumaNotaEstudiante=0;
                                    foreach ($consultaNotaMateriaIndicadoresxPeriodo as $datosIndicadores) {
                                        $nota=0;
                                        if ($datosIndicadores["mat_id"] == $materia["mat_id"]) {
                                            $nota = ($datosIndicadores["nota"]*($datosIndicadores["ipc_valor"]/100));
                                        }
                                        $sumaNotaEstudiante += $nota;
                                    }
                                    
                                    $estudianteNota=0;
                                    if($numIndicadoresPorPeriodo!=0){
                                        $estudianteNota=$sumaNotaEstudiante;
                                    }
                                    // Formatear nota del estudiante con decimales configurados
                                    $notaEstudianteFormateada = Boletin::notaDecimales((float)$estudianteNota);
                                    $notaEstudiante = (float)$estudianteNota;

                                    // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                    $notaEstRedondeada = number_format($notaEstudiante, $config['conf_decimales_notas'], '.', '');
                                    $desempenoNotaP = isset($notasCualitativasCache[$notaEstRedondeada]) 
                                        ? ['notip_nombre' => $notasCualitativasCache[$notaEstRedondeada]] 
                                        : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaEstudiante,$year);
                                    if($desempenoNotaP === null){
                                        $desempenoNotaP = ['notip_nombre' => ''];
                                    }
                                    $desempenoNotaPFinal= !empty($desempenoNotaP['notip_nombre']) ? $desempenoNotaP['notip_nombre'] : "";

                                    $promedioMateria += $notaEstudiante;
                                    if ($materia["mat_sumar_promedio"] == SI) {
                                        switch($j){
                                            case 1:
                                                $sumaNotaP1 += $notaEstudiante;
                                                break;
                                            case 2:
                                                $sumaNotaP2 += $notaEstudiante;
                                                break;
                                            case 3:
                                                $sumaNotaP3 += $notaEstudiante;
                                                break;
                                            case 4:
                                                $sumaNotaP4 += $notaEstudiante;
                                                break;
                                        }
                                    }
                            ?>
                                <td align="center" style=" font-size:12px;"><?=$notaEstudianteFormateada;?></td>
                                <td align="center" style=" font-size:12px;"><?=$desempenoNotaPFinal;?></td>
                            <?php
                                }
                                if ($materia["mat_sumar_promedio"] == SI) {
                                    $matPromedio ++;
                                }
                                $promedioMateria = ($j - 1) > 0 ? ($promedioMateria / ($j - 1)) : 0;
                                $promedioMateriaFinal = $promedioMateria;

                                // OPTIMIZACIÓN: Obtener nivelación del mapa pre-cargado
                                $nivelacion = $nivelacionesMapa[$materia['car_id']] ?? null;
                                if($nivelacion === null){
                                    // Fallback: consulta individual si no está en el mapa
                                    $consultaNivelacion = Boletin::obtenerNivelaciones($materia['car_id'], $matriculadosDatos['mat_id'], $year);
                                    $nivelacion = mysqli_fetch_array($consultaNivelacion, MYSQLI_BOTH);
                                    if($nivelacion){
                                        $nivelacionesMapa[$materia['car_id']] = $nivelacion;
                                    }
                                }
        
                                // SI PERDIÓ LA MATERIA A FIN DE AÑO
                                if ($promedioMateria < $config["conf_nota_minima_aprobar"]) {
                                    if (!empty($nivelacion['niv_definitiva']) && $nivelacion['niv_definitiva'] >= $config["conf_nota_minima_aprobar"]) {
                                        $promedioMateriaFinal = $nivelacion['niv_definitiva'];
                                    } else {
                                        $materiasPerdidas++;
                                    }
                                }
                                
                                // Formatear promedio de la materia con decimales configurados
                                $promedioMateriaFinalFormateado = Boletin::notaDecimales($promedioMateriaFinal);

                                // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                $notaMateriaRedondeada = number_format($promedioMateriaFinal, $config['conf_decimales_notas'], '.', '');
                                $promediosMateriaEstiloNota = isset($notasCualitativasCache[$notaMateriaRedondeada]) 
                                    ? ['notip_nombre' => $notasCualitativasCache[$notaMateriaRedondeada]] 
                                    : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioMateriaFinal,$year);
                                if($promediosMateriaEstiloNota === null){
                                    $promediosMateriaEstiloNota = ['notip_nombre' => ''];
                                }
                                $promediosMateriaEstiloNotaFinal= !empty($promediosMateriaEstiloNota['notip_nombre']) ? $promediosMateriaEstiloNota['notip_nombre'] : "";
                            ?>
                            <td align="center" style=" font-size:12px;"><?=$promedioMateriaFinalFormateado;?></td>
                            <td align="center" style=" font-size:12px;"><?=$promediosMateriaEstiloNotaFinal;?></td>
                        </tr>
                        <?php
                        if($materia['car_observaciones_boletin'] == 1) {
                            // OPTIMIZACIÓN: Obtener observación del mapa pre-cargado
                            $observacion = $observacionesMapa[$materia["car_id"]] ?? null;
                            if($observacion === null){
                                // Fallback: consulta individual si no está en el mapa
                                $consultaObsevacion=Boletin::obtenerObservaciones($materia["car_id"], $periodoActual, $matriculadosDatos['mat_id'], $year);
                                $observacion = mysqli_fetch_array($consultaObsevacion, MYSQLI_BOTH);
                                if($observacion){
                                    $observacionesMapa[$materia["car_id"]] = $observacion;
                                }
                            }
                            if ($observacion && !empty($observacion['bol_observaciones_boletin'])) {
                            ?>
                                <tr>
                                    <td colspan="<?=$colspan?>">
                                        <h5 align="center" style="margin: 0">Observaciones</h5>
                                        <p style="margin: 0 0 0 10px; font-size: 11px; font-style: italic;">
                                            <?= $observacion['bol_observaciones_boletin']; ?>
                                        </p>
                                    </td>
                                </tr>
                        
                    <?php
                            }
                        }
                        $contador++;
                        $ausPer1Total+=$ausPer1;
                        $ausPer2Total+=$ausPer2;
                        $ausPer3Total+=$ausPer3;
                        $ausPer4Total+=$ausPer4;
                        $sumAusenciasTotal+=$sumAusencias;
                    } //while fin materias
                    ?>
            <?php }
            } //while fin areas
            ?>
            <tr bgcolor="#EAEAEA" style="font-size:12px; text-align:center;">
                <td colspan="3" style="text-align:left;  font-size:12px;">PROMEDIO GENERAL</td>

                <?php
                $promedioFinal = 0;
                for ($j = 1; $j <= $periodoActual; $j++) {
                    switch($j){
                        case 1:
                            $promediosPeriodos = ($matPromedio > 0) ? ($sumaNotaP1/$matPromedio) : 0;
                            break;
                        case 2:
                            $promediosPeriodos = ($matPromedio > 0) ? ($sumaNotaP2/$matPromedio) : 0;
                            break;
                        case 3:
                            $promediosPeriodos = ($matPromedio > 0) ? ($sumaNotaP3/$matPromedio) : 0;
                            break;
                        case 4:
                            $promediosPeriodos = ($matPromedio > 0) ? ($sumaNotaP4/$matPromedio) : 0;
                            break;
                    }
                    // Formatear promedio por período con decimales configurados
                    $promediosPeriodosFormateado = Boletin::notaDecimales((float)$promediosPeriodos);

                    // OPTIMIZACIÓN: Usar cache de notas cualitativas
                    $promedioRedondeado = number_format((float)$promediosPeriodos, $config['conf_decimales_notas'], '.', '');
                    $promediosEstiloNota = isset($notasCualitativasCache[$promedioRedondeado]) 
                        ? ['notip_nombre' => $notasCualitativasCache[$promedioRedondeado]] 
                        : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promediosPeriodos,$year);
                    if($promediosEstiloNota === null){
                        $promediosEstiloNota = ['notip_nombre' => ''];
                    }
                    $promediosEstiloNotaFinal= !empty($promediosEstiloNota['notip_nombre']) ? $promediosEstiloNota['notip_nombre'] : "";
                ?>

                    <td style=" font-size:12px;"><?= $promediosPeriodosFormateado; ?></td>
                    <td style=" font-size:12px;"><?= $promediosEstiloNotaFinal; ?></td>
                <?php 
                    $promedioFinal +=$promediosPeriodos;
                } 

                    $promedioFinal = $periodoActual > 0 ? ($promedioFinal/$periodoActual) : 0;
                    // Formatear promedio final con decimales configurados
                    $promedioFinalFormateado = Boletin::notaDecimales($promedioFinal);

                    // OPTIMIZACIÓN: Usar cache de notas cualitativas
                    $promedioFinalRedondeado = number_format($promedioFinal, $config['conf_decimales_notas'], '.', '');
                    $promedioFinalEstiloNota = isset($notasCualitativasCache[$promedioFinalRedondeado]) 
                        ? ['notip_nombre' => $notasCualitativasCache[$promedioFinalRedondeado]] 
                        : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioFinal,$year);
                    if($promedioFinalEstiloNota === null){
                        $promedioFinalEstiloNota = ['notip_nombre' => ''];
                    }
                    $promedioFinalEstiloNotaFinal= !empty($promedioFinalEstiloNota['notip_nombre']) ? $promedioFinalEstiloNota['notip_nombre'] : "";
                ?>
                <td style=" font-size:12px;"><?=$promedioFinalFormateado;?></td>
                <td style=" font-size:12px;"><?= $promedioFinalEstiloNotaFinal; ?></td>
            </tr>

            <tr bgcolor="#EAEAEA" style="font-size:12px;  text-align:center;">
                <td colspan="3" style="text-align:left;">AUSENCIAS</td>
                <?php
                for ($j = 1; $j <= $periodoActual; $j++) {
                    switch($j){
                        case 1:
                            echo '<td>'.$ausPer1Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                        case 2:
                            echo '<td>'.$ausPer2Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                        case 3:
                            echo '<td>'.$ausPer3Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                        case 4:
                            echo '<td>'.$ausPer4Total.' Aus.</td><td>&nbsp;</td>';
                            break;
                    }
                }
                ?>
                <td><?=$sumAusenciasTotal?> Aús.</td>
                <td>&nbsp;</td>
            </tr>
            
        </table>
        <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
            <tr>
                <td style=" font-size:12px;">Tabla de desempeño:</td>
                <?php
                    $consulta = Boletin::listarTipoDeNotas($config["conf_notas_categoria"],$year);
                    while($estiloNota = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                ?>
                    <td align="center" style="font-size:12px;"><?=$estiloNota['notip_nombre'].": ".$estiloNota['notip_desde']." - ".$estiloNota['notip_hasta'];?></td>
                <?php
                    }
                ?>
            </tr>
        </table>
        <?php
        $cndisciplina = Boletin::obtenerNotaDisciplina($matriculadosDatos['mat_id'], $condicion, $year);
        if (@mysqli_num_rows($cndisciplina) > 0) {
        ?>
            <table width="100%" style="margin-top: 15px;" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
                <tr style=" background:#00adefad; border-color:#036; font-size:12px; text-align:center">
                    <td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>
                </tr>
                <tr style=" background:#EAEAEA; color:#000; font-size:12px; text-align:center">
                    <td width="8%">Periodo</td>
                    <td>Observaciones</td>
                </tr>
                <?php
                while ($rndisciplina = mysqli_fetch_array($cndisciplina, MYSQLI_BOTH)) {
                    // OPTIMIZACIÓN: Usar cache de notas cualitativas
                    $notaDisciplinaRedondeada = number_format((float)$rndisciplina["dn_nota"], $config['conf_decimales_notas'], '.', '');
                    $desempenoND = isset($notasCualitativasCache[$notaDisciplinaRedondeada]) 
                        ? ['notip_nombre' => $notasCualitativasCache[$notaDisciplinaRedondeada]] 
                        : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rndisciplina["dn_nota"],$year);
                    if($desempenoND === null){
                        $desempenoND = ['notip_nombre' => ''];
                    }
                    $desempenoNDFinal= !empty($desempenoND['notip_nombre']) ? $desempenoND['notip_nombre'] : "";
                ?>
                    <tr align="center" style=" font-size:12px;">
                        <td><?= $rndisciplina["dn_periodo"] ?></td>
                        <td align="left"><?= $rndisciplina["dn_observacion"] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
        <!--******FIRMAS******-->   
        <?php if($mostrarFirmas): ?>
        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
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
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                            }
                            echo '<p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                <!-- '.$nombreRector.'<br> -->
                                Rector(a)';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                <!-- <br> -->
                                Rector(a)';
                        }
                    ?>
                </td>
                <td align="center">
                    <?php
                        // OPTIMIZACIÓN: Usar director cacheado (ya se cargó arriba)
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
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                            }
                            echo '<p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                <!-- '.$nombreDirectorGrupo.'<br> -->
                                Director(a) de grupo';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p style="height:0px;"></p>_________________________________<br>
                                <p>&nbsp;</p>
                                <!-- <br> -->
                                Director(a) de grupo';
                        }
                    ?>
                </td>
            </tr>
        </table>
        <?php endif; ?>
        <?php if($mostrarIndicadores): ?>
        <!-- Salto de página antes de la segunda hoja (Indicadores de desempeño) -->
        <div id="saltoPagina"></div>
        <!--******SEGUNDA PAGINA******-->
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <!--******INDICADORES POR ASIGNATURA******-->

        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center" style="font-size:10px;">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            $conCargasDos = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $gradoActual, $grupoActual, $year);
            
            // OPTIMIZACIÓN: Pre-cargar indicadores para la segunda página
            $indicadoresSegundaPaginaMapa = []; // [car_id] => array de indicadores
            try {
                mysqli_data_seek($conCargasDos, 0);
                while ($filaTemp = mysqli_fetch_array($conCargasDos, MYSQLI_BOTH)) {
                    $idCarga = $filaTemp['car_id'];
                    if (!isset($indicadoresSegundaPaginaMapa[$idCarga])) {
                        $indicadoresSegundaPaginaMapa[$idCarga] = [];
                        $indicadoresTemp = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $idCarga, $periodoActual, $year);
                        while ($indTemp = mysqli_fetch_array($indicadoresTemp, MYSQLI_BOTH)) {
                            $indicadoresSegundaPaginaMapa[$idCarga][] = $indTemp;
                        }
                    }
                }
                mysqli_data_seek($conCargasDos, 0);
            } catch (Exception $eInd) {
                include("../compartido/error-catch-to-report.php");
            }
            
            while ($datosCargasDos = mysqli_fetch_array($conCargasDos, MYSQLI_BOTH)) {

                
            ?>
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $datosCargasDos['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($datosCargasDos); ?></span></td>
                        <td>
                        
                            <?php
                            // OPTIMIZACIÓN: Obtener indicadores del mapa pre-cargado
                            $indicadores = $indicadoresSegundaPaginaMapa[$datosCargasDos['car_id']] ?? [];
                            foreach ($indicadores as $indicador) {
                            ?>
                
                        <?= $indicador['ind_nombre']; ?><br>
                    
                <?php
                            }
                ?>
                    </td>
                </tr>
                </tbody>
            <?php
            }
            ?>
        </table>
        <?php endif; ?>
        <!-- Salto de página al final de cada estudiante (antes del siguiente) -->
        <?php
            $contadorEstudiantes++;
            if($contadorEstudiantes!=$numeroEstudiantes && empty($_GET['id'])){
        ?>
        <div id="saltoPagina"></div>
    <?php
            }
    } // FIN DE TODOS LOS MATRICULADOS
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    ?>
    <script type="application/javascript">
        print();
    </script>
    </body>
    </html>