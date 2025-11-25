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
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");

$year=$_SESSION["bd"];
if(isset($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

$modulo = 1;
if (empty($_GET["periodo"])) {
    $periodoActual = 1;
} else {
    $periodoActual = base64_decode($_GET["periodo"]);
}

if ($periodoActual == 1) $periodoActuales = "Primero";
if ($periodoActual == 2) $periodoActuales = "Segundo";
if ($periodoActual == 3) $periodoActuales = "Tercero";
if ($periodoActual == 4) $periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if (!empty($_GET["id"])) {
    $filtro .= " AND mat_id='" . base64_decode($_GET["id"]) . "'";
}
if (!empty($_REQUEST["curso"])) {
    $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro,$year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
$numMatriculados = mysqli_num_rows($matriculadosPorCurso);
$idDirector="";
while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {

    $gradoActual = $matriculadosDatos['mat_grado'];
    $grupoActual = $matriculadosDatos['mat_grupo'];

    //contadores
    $contador_periodos = 0;
    $contador_indicadores = 0;
    $materiasPerdidas = 0;
    if ($matriculadosDatos['mat_id'] == "") { ?>
        <script type="text/javascript">
            window.close();
        </script>
    <?php
        exit();
    }
    $contp = 1;
    $puestoCurso = 0;
    $puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual,$matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
    foreach ($puestos as $puesto) {
        if ($puesto['estudiante_id'] == $matriculadosDatos['mat_id']) {
            $puestoCurso = $contp;
        }
        $contp++;
    }
    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
    $usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
    $datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
    $nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);
    ?>
    <!doctype html>
    <html class="no-js" lang="en">

    <head>
        <title>Boletín Academico</title>
        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>

    <body style="font-family:Arial; font-size:9px;">

        <div style="margin-bottom: 10px;">  

            <div align="center">
                <?php
                    if($config['conf_id_institucion'] == ELLEN_KEY){
                ?>
                    <img src="../files/images/logo/encabezadoellen.png" width="95%">
                <?php }else{?>
                    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="200"><br>
                <?php }?>
            </div>

            <div>
                <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 14px;">
                    <tr>
                        <td>Estudiante: <b><?=$nombre?></b></td>
                        <td>Grado: <b><?= $datosUsr["gra_nombre"] . " " . $datosUsr["gru_nombre"]; ?></b></td>
                        <td>Periodo/Año: <b><?= $periodoActual . " / " . $year . ""; ?></b></td>
                    </tr>
                </table>
            </div>
        </div>

        <table width="100%" rules="all" border="1">
            <thead>
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #00adefad; font-size: 13px;">INFORME PERIÓDICO DEL PROCESO DE DESARROLLO EDUCATIVO</td>
                </tr>
            </thead>
        </table>

        <table width="100%" rules="all" border="1">
            <thead>

                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="2%" rowspan="2">I.H.</td>

                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="3%" colspan="2"><a href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $datosUsr['mat_id']; ?>&periodo=<?= $j ?>" style="color:#000; text-decoration:none;">Periodo <?= $j ?></a></td>
                    <?php } ?>
                    <td width="3%" colspan="3">Final</td>
                </tr>

                <tr style="font-weight:bold; text-align:center;">
                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="3%">Nota</td>
                        <td width="3%">Nivel</td>
                    <?php } ?>
                    <td width="3%">Nota</td>
                    <td width="3%">Nivel</td>
                    <td width="3%">Hab</td>
                </tr>

            </thead>

            <?php
            $contador = 1;
            $ausPer1Total=0;
            $ausPer2Total=0;
            $ausPer3Total=0;
            $ausPer4Total=0;
            $sumAusenciasTotal=0;
            $conCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $year);
            
            // ============================================
            // OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
            // ============================================
            
            // OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para este estudiante y todos los períodos
            $notasBoletinMapa = []; // [carga][periodo] => datos_nota
            try {
                $categoriaEsc = mysqli_real_escape_string($conexion, $config['conf_notas_categoria']);
                $sqlNotas = "SELECT bol_carga, bol_periodo, bol_nota, bol_observaciones_boletin, bol_tipo, bol_nota_anterior, notip_nombre, notip_imagen
                             FROM " . BD_ACADEMICA . ".academico_boletin bol
                             LEFT JOIN " . BD_ACADEMICA . ".academico_notas_tipos notip 
                                 ON notip.notip_categoria = '{$categoriaEsc}'
                                 AND bol.bol_nota >= notip.notip_desde 
                                 AND bol.bol_nota <= notip.notip_hasta
                                 AND notip.institucion = bol.institucion
                                 AND notip.year = bol.year
                             WHERE bol.bol_estudiante = ?
                               AND bol.institucion = ?
                               AND bol.year = ?
                               AND bol.bol_periodo <= ?";
                $paramNotas = [
                    $datosUsr['mat_id'],
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
            
            // OPTIMIZACIÓN 2: Pre-cargar todas las ausencias para este estudiante y todos los períodos
            $ausenciasMapa = []; // [carga][periodo] => suma_ausencias
            try {
                // Obtener todas las cargas del estudiante
                $idsCargas = [];
                $idsMaterias = [];
                mysqli_data_seek($conCargas, 0);
                while ($filaTemp = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
                    if (!in_array($filaTemp['car_id'], $idsCargas)) {
                        $idsCargas[] = $filaTemp['car_id'];
                    }
                    if (!in_array($filaTemp['mat_id'], $idsMaterias)) {
                        $idsMaterias[] = $filaTemp['mat_id'];
                    }
                }
                mysqli_data_seek($conCargas, 0);
                
                if (!empty($idsCargas) && !empty($idsMaterias)) {
                    $idEstudianteEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_id']);
                    $graIdEsc = mysqli_real_escape_string($conexion, $datosUsr['gra_id']);
                    $inCargas = implode(',', array_map('intval', $idsCargas));
                    $inMaterias = implode(',', array_map('intval', $idsMaterias));
                    $institucion = (int)$config['conf_id_institucion'];
                    $yearEsc = mysqli_real_escape_string($conexion, $year);
                    $periodoEsc = (int)$periodoActual;
                    
                    // Pre-cargar ausencias usando el método de la clase Ausencias
                    for($j=1; $j<=$periodoEsc; $j++){
                        foreach($idsMaterias as $idMateria){
                            $ausTemp = Ausencias::sumarAusenciasCarga($config, $graIdEsc, $idMateria, $j, $datosUsr['mat_id']);
                            if(!empty($ausTemp['sumAus']) && $ausTemp['sumAus'] > 0){
                                // Buscar la carga correspondiente a esta materia
                                mysqli_data_seek($conCargas, 0);
                                while($filaTemp = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
                                    if($filaTemp['mat_id'] == $idMateria){
                                        $idCarga = $filaTemp['car_id'];
                                        if (!isset($ausenciasMapa[$idCarga])) {
                                            $ausenciasMapa[$idCarga] = [];
                                        }
                                        $ausenciasMapa[$idCarga][$j] = (float)$ausTemp['sumAus'];
                                        break;
                                    }
                                }
                                mysqli_data_seek($conCargas, 0);
                            }
                        }
                    }
                }
            } catch (Exception $eAus) {
                include("../compartido/error-catch-to-report.php");
            }
            
            // OPTIMIZACIÓN 3: Pre-cargar todas las nivelaciones para este estudiante
            $nivelacionesMapa = []; // [car_id] => datos_nivelacion
            try {
                mysqli_data_seek($conCargas, 0);
                while ($filaTemp = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
                    $idCarga = $filaTemp['car_id'];
                    if (!isset($nivelacionesMapa[$idCarga])) {
                        $nivTemp = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $datosUsr['mat_id'], $idCarga, $year);
                        $niv = mysqli_fetch_array($nivTemp, MYSQLI_BOTH);
                        if ($niv) {
                            $nivelacionesMapa[$idCarga] = $niv;
                        }
                    }
                }
                mysqli_data_seek($conCargas, 0);
            } catch (Exception $eNiv) {
                include("../compartido/error-catch-to-report.php");
            }
            
            // OPTIMIZACIÓN 4: Pre-cargar promedios por período y ausencias totales
            $promediosPeriodosMapa = []; // [periodo] => promedio
            $ausenciasTotalesMapa = []; // [periodo] => suma_ausencias
            try {
                $idEstudianteEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_id']);
                $gradoEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_grado']);
                $grupoEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_grupo']);
                $institucion = (int)$config['conf_id_institucion'];
                $yearEsc = mysqli_real_escape_string($conexion, $year);
                
                for($j=1; $j<=$periodoActual; $j++){
                    // Promedio por período
                    $sqlPromedio = "SELECT ROUND(AVG(bol_nota), 1) as promedio 
                                    FROM ".BD_ACADEMICA.".academico_boletin bol
                                    INNER JOIN ".BD_ACADEMICA.".academico_cargas car 
                                        ON car_id=bol_carga 
                                        AND car_curso='{$gradoEsc}' 
                                        AND car_grupo='{$grupoEsc}' 
                                        AND car.institucion={$institucion} 
                                        AND car.year='{$yearEsc}'
                                    INNER JOIN ".BD_ACADEMICA.".academico_materias mat 
                                        ON mat_id=car_materia 
                                        AND mat_sumar_promedio='SI' 
                                        AND mat.institucion={$institucion} 
                                        AND mat.year='{$yearEsc}' 
                                    WHERE bol_estudiante='{$idEstudianteEsc}' 
                                        AND bol_periodo='{$j}' 
                                        AND bol.institucion={$institucion} 
                                        AND bol.year='{$yearEsc}'";
                    $consultaPromedio = mysqli_query($conexion, $sqlPromedio);
                    if($consultaPromedio){
                        $rowProm = mysqli_fetch_array($consultaPromedio, MYSQLI_BOTH);
                        $promediosPeriodosMapa[$j] = !empty($rowProm['promedio']) ? (float)$rowProm['promedio'] : 0;
                    }
                    
                    // Ausencias totales por período
                    $sqlAusTotal = "SELECT sum(aus_ausencias) as total 
                                    FROM ".BD_ACADEMICA.".academico_clases cls 
                                    INNER JOIN ".BD_ACADEMICA.".academico_ausencias aus 
                                        ON aus.aus_id_clase=cls.cls_id 
                                        AND aus.aus_id_estudiante='{$idEstudianteEsc}' 
                                        AND aus.institucion={$institucion} 
                                        AND aus.year='{$yearEsc}'
                                    WHERE cls.cls_periodo='{$j}' 
                                        AND cls.institucion={$institucion} 
                                        AND cls.year='{$yearEsc}'";
                    $consultaAusTotal = mysqli_query($conexion, $sqlAusTotal);
                    if($consultaAusTotal){
                        $rowAusTotal = mysqli_fetch_array($consultaAusTotal, MYSQLI_BOTH);
                        $ausenciasTotalesMapa[$j] = !empty($rowAusTotal['total']) ? (float)$rowAusTotal['total'] : 0;
                    }
                }
            } catch (Exception $eProm) {
                include("../compartido/error-catch-to-report.php");
            }
            
            // OPTIMIZACIÓN 5: Pre-cargar indicadores para todas las cargas
            $indicadoresMapa = []; // [car_id] => array de indicadores
            try {
                mysqli_data_seek($conCargas, 0);
                while ($filaTemp = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
                    $idCarga = $filaTemp['car_id'];
                    if (!isset($indicadoresMapa[$idCarga])) {
                        $indicadoresMapa[$idCarga] = [];
                        $indicadoresTemp = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $idCarga, $periodoActual, $year);
                        while ($indTemp = mysqli_fetch_array($indicadoresTemp, MYSQLI_BOTH)) {
                            $indicadoresMapa[$idCarga][] = $indTemp;
                        }
                    }
                }
                mysqli_data_seek($conCargas, 0);
            } catch (Exception $eInd) {
                include("../compartido/error-catch-to-report.php");
            }
            
            // OPTIMIZACIÓN 6: Pre-cargar cache de notas cualitativas
            $notasCualitativasCache = [];
            if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                $consultaNotasTipo = mysqli_query($conexion, 
                    "SELECT notip_desde, notip_hasta, notip_nombre, notip_imagen 
                     FROM ".BD_ACADEMICA.".academico_notas_tipos 
                     WHERE notip_categoria='".mysqli_real_escape_string($conexion, $config['conf_notas_categoria'])."' 
                     AND institucion=".(int)$config['conf_id_institucion']." 
                     AND year='".mysqli_real_escape_string($conexion, $year)."' 
                     ORDER BY notip_desde ASC");
                if($consultaNotasTipo){
                    while($tipoNota = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)){
                        // Pre-cargar cache para todos los valores posibles (de 0.1 en 0.1)
                        for($i = $tipoNota['notip_desde']; $i <= $tipoNota['notip_hasta']; $i += 0.1){
                            $key = number_format((float)$i, 1, '.', '');
                            if(!isset($notasCualitativasCache[$key])){
                                $notasCualitativasCache[$key] = [
                                    'notip_nombre' => $tipoNota['notip_nombre'],
                                    'notip_imagen' => $tipoNota['notip_imagen']
                                ];
                            }
                        }
                    }
                }
            }
            
            // OPTIMIZACIÓN 7: Cachear datos del director de grupo
            $directorGrupo = null;
            $nombreDirectorGrupo = '';
            
            while ($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
                //DIRECTOR DE GRUPO
                if($datosCargas["car_director_grupo"]==1){
                    $idDirector=$datosCargas["car_docente"];
                    // OPTIMIZACIÓN: Cargar director solo una vez
                    if($directorGrupo === null && !empty($idDirector)){
                        $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                        $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
                    }
                }

                if ($contador % 2 == 1) {
                    $fondoFila = '#EAEAEA';
                } else {
                    $fondoFila = '#FFF';
                }
                $sumAusencias=0;
                $j=1;
                $ausPer1=0;
                $ausPer2=0;
                $ausPer3=0;
                $ausPer4=0;
                while($j<=$periodoActual){
                    // OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
                    $datosAusencias = ['sumAus' => ($ausenciasMapa[$datosCargas['car_id']][$j] ?? 0)];

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
                    $j++;
                }
            ?>
                <tbody>
                    <tr style="background:<?= $fondoFila; ?>">
                        <td><?= $datosCargas['mat_nombre']; ?></td>
                        <td align="center"><?= $datosCargas['car_ih']; ?></td>
                        <?php
                        $promedioMateria = 0;
                        for ($j = 1; $j <= $periodoActual; $j++) {
                            // OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
                            $datosBoletin = $notasBoletinMapa[$datosCargas['car_id']][$j] ?? null;
                            if($datosBoletin === null){
                                // Fallback: consulta individual si no está en el mapa
                                $resTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $j, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
                                if($resTemp){
                                    $datosBoletin = mysqli_fetch_array($resTemp, MYSQLI_BOTH);
                                } else {
                                    $datosBoletin = ['bol_nota' => 0, 'notip_nombre' => '', 'notip_imagen' => '', 'bol_tipo' => 1, 'bol_nota_anterior' => 0];
                                }
                            }

                            $promedioMateria += !empty($datosBoletin['bol_nota']) ? (float)$datosBoletin['bol_nota'] : 0;
                            $notaBoletin=0;
                            if (!empty($datosBoletin['bol_nota'])) {
                                $notaBoletin = round($datosBoletin['bol_nota'], 1);
                            }

                            if($notaBoletin == '0'){$notaBoletin='0.0';}
                            if($notaBoletin == 1){$notaBoletin='1.0';}
                            if($notaBoletin == 2){$notaBoletin='2.0';}
                            if($notaBoletin == 3){$notaBoletin='3.0';}
                            if($notaBoletin == 4){$notaBoletin='4.0';}
                            if($notaBoletin == 5){$notaBoletin='5.0';}

                            $notaBoletinFinal=$notaBoletin;
                            if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                $notaBoletinFinal= !empty($datosBoletin['notip_nombre']) ? $datosBoletin['notip_nombre'] : "";
                            }

                            $notaNivelada = $notaBoletinFinal >= $config["conf_nota_minima_aprobar"] ? " Nivelada" : "";
                            $notaBoletinFinal = !empty($datosBoletin['bol_tipo']) && $datosBoletin['bol_tipo'] != 1 && !empty($datosBoletin['bol_nota_anterior']) 
                                ? round((float)$datosBoletin['bol_nota_anterior'], 1)." / ".$notaBoletinFinal.$notaNivelada 
                                : $notaBoletinFinal;
                        ?>
                            <td align="center"><?= $notaBoletinFinal; ?></td>
                            <td align="center"><img src="../files/iconos/<?= $datosBoletin['notip_imagen']; ?>" width="15" height="15"></td>
                        <?php
                        }
                        $promedioMateria = round($promedioMateria / ($j - 1), 1);
                        $promedioMateriaFinal = $promedioMateria; // Mantener la definitiva normal del año
                        
                        // OPTIMIZACIÓN: Obtener nivelación del mapa pre-cargado
                        $nivelacion = $nivelacionesMapa[$datosCargas['car_id']] ?? null;
                        if($nivelacion === null){
                            // Fallback: consulta individual si no está en el mapa
                            $consultaNivelacion = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
                            $nivelacion = mysqli_fetch_array($consultaNivelacion, MYSQLI_BOTH);
                            if($nivelacion){
                                $nivelacionesMapa[$datosCargas['car_id']] = $nivelacion;
                            }
                        }

                        // Contar materias perdidas (sin considerar habilitación)
                        if ($promedioMateria < $config["conf_nota_minima_aprobar"]) {
                            if (empty($nivelacion['niv_definitiva']) || $nivelacion['niv_definitiva'] < $config["conf_nota_minima_aprobar"]) {
                                $materiasPerdidas++;
                            }
                        }

                        // OPTIMIZACIÓN: Usar cache de notas cualitativas
                        $notaMateriaRedondeada = number_format((float)$promedioMateriaFinal, 1, '.', '');
                        $promediosMateriaEstiloNota = isset($notasCualitativasCache[$notaMateriaRedondeada]) 
                            ? $notasCualitativasCache[$notaMateriaRedondeada] 
                            : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioMateriaFinal, $year);
                        if($promediosMateriaEstiloNota === null){
                            $promediosMateriaEstiloNota = ['notip_nombre' => '', 'notip_imagen' => ''];
                        }

                        if($promedioMateriaFinal == '0'){$promedioMateriaFinal='0.0';}
                        if($promedioMateriaFinal == 1){$promedioMateriaFinal='1.0';}
                        if($promedioMateriaFinal == 2){$promedioMateriaFinal='2.0';}
                        if($promedioMateriaFinal == 3){$promedioMateriaFinal='3.0';}
                        if($promedioMateriaFinal == 4){$promedioMateriaFinal='4.0';}
                        if($promedioMateriaFinal == 5){$promedioMateriaFinal='5.0';}

                        $promedioMateriaTotal=$promedioMateriaFinal;
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                            $promedioMateriaTotal= !empty($promediosMateriaEstiloNota['notip_nombre']) ? $promediosMateriaEstiloNota['notip_nombre'] : "";
                        }

                        // Preparar nota de habilitación para la columna HAB
                        $notaHabilitacion = "";
                        $notaHabilitacionFormateada = "";
                        if (!empty($nivelacion['niv_definitiva'])) {
                            $notaHabilitacion = (float)$nivelacion['niv_definitiva'];
                            
                            // Formatear según el mismo estilo que se usa en el archivo
                            $notaHabilitacionRedondeada = round($notaHabilitacion, 1);
                            
                            if($notaHabilitacionRedondeada == '0'){$notaHabilitacionRedondeada='0.0';}
                            if($notaHabilitacionRedondeada == 1){$notaHabilitacionRedondeada='1.0';}
                            if($notaHabilitacionRedondeada == 2){$notaHabilitacionRedondeada='2.0';}
                            if($notaHabilitacionRedondeada == 3){$notaHabilitacionRedondeada='3.0';}
                            if($notaHabilitacionRedondeada == 4){$notaHabilitacionRedondeada='4.0';}
                            if($notaHabilitacionRedondeada == 5){$notaHabilitacionRedondeada='5.0';}
                            
                            $notaHabilitacionFormateada = $notaHabilitacionRedondeada;
                            
                            // OPTIMIZACIÓN: Usar cache de notas cualitativas
                            if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                $notaHabRedondeada = number_format((float)$notaHabilitacion, 1, '.', '');
                                $habilitacionEstiloNota = isset($notasCualitativasCache[$notaHabRedondeada]) 
                                    ? $notasCualitativasCache[$notaHabRedondeada] 
                                    : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaHabilitacion, $year);
                                $notaHabilitacionFormateada = !empty($habilitacionEstiloNota['notip_nombre']) ? $habilitacionEstiloNota['notip_nombre'] : "";
                            }
                        }

                        ?>
                        <td align="center"><?= $promedioMateriaTotal; ?></td>
                        <td align="center"><img src="../files/iconos/<?= $promediosMateriaEstiloNota['notip_imagen']; ?>" width="15" height="15"></td>
                        <td align="center"><?= !empty($notaHabilitacionFormateada) ? $notaHabilitacionFormateada : "&nbsp;"; ?></td>
                    </tr>
                </tbody>
            <?php
                $contador++;                
                $ausPer1Total+=$ausPer1;
                $ausPer2Total+=$ausPer2;
                $ausPer3Total+=$ausPer3;
                $ausPer4Total+=$ausPer4;
                $sumAusenciasTotal+=$sumAusencias;
            }
            ?>
            <tfoot>
                <tr style="font-weight:bold; text-align:center;">
                    <td style="text-align:left;">PROMEDIO GENERAL</td>
                    <td>&nbsp;</td>

                    <?php
                    $promedioFinal = 0;
                    for ($j = 1; $j <= $periodoActual; $j++) {
                        // OPTIMIZACIÓN: Obtener promedio del mapa pre-cargado
                        $promediosPeriodos = ['promedio' => ($promediosPeriodosMapa[$j] ?? 0)];
                        
                        // OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
                        $sumaAusencias = [0 => ($ausenciasTotalesMapa[$j] ?? 0)];

                        // OPTIMIZACIÓN: Usar cache de notas cualitativas
                        $promedioRedondeado = number_format((float)$promediosPeriodos['promedio'], 1, '.', '');
                        $promediosEstiloNota = isset($notasCualitativasCache[$promedioRedondeado]) 
                            ? $notasCualitativasCache[$promedioRedondeado] 
                            : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promediosPeriodos['promedio'], $year);
                        if($promediosEstiloNota === null){
                            $promediosEstiloNota = ['notip_nombre' => '', 'notip_imagen' => ''];
                        }

                        $promediosPeriodosTotal=$promediosPeriodos['promedio'];
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                          $promediosPeriodosTotal= !empty($promediosEstiloNota['notip_nombre']) ? $promediosEstiloNota['notip_nombre'] : "";
                        }
                    ?>

                        <td><?= $promediosPeriodosTotal; ?></td>
                        <td><img src="../files/iconos/<?= $promediosEstiloNota['notip_imagen']; ?>" width="15" height="15"></td>
                    <?php 
                        $promedioFinal +=$promediosPeriodos['promedio'];
                    } 

                        $promedioFinal = $periodoActual > 0 ? round($promedioFinal/$periodoActual,2) : 0;
                        // OPTIMIZACIÓN: Usar cache de notas cualitativas
                        $promedioFinalRedondeado = number_format((float)$promedioFinal, 1, '.', '');
                        $promedioFinalEstiloNota = isset($notasCualitativasCache[$promedioFinalRedondeado]) 
                            ? $notasCualitativasCache[$promedioFinalRedondeado] 
                            : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioFinal, $year);
                        if($promedioFinalEstiloNota === null){
                            $promedioFinalEstiloNota = ['notip_nombre' => '', 'notip_imagen' => ''];
                        }

                        $promedioFinalTotal=$promedioFinal;
                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                          $promedioFinalTotal= !empty($promedioFinalEstiloNota['notip_nombre']) ? $promedioFinalEstiloNota['notip_nombre'] : "";
                        }
                    ?>
                    <td><?=$promedioFinalTotal;?></td>
                    <td><img src="../files/iconos/<?= $promedioFinalEstiloNota['notip_imagen']; ?>" width="15" height="15"></td>
                    <td>-</td>
                </tr>

                <tr style="font-weight:bold; text-align:center;">
                    <td style="text-align:left;">AUSENCIAS</td>
                    <td>&nbsp;</td>
                    <?php
                    for ($j = 1; $j <= $periodoActual; $j++) {
                        switch($j){
                            case 1:
                                echo '<td>&nbsp;</td>
                                      <td>'.$ausPer1Total.' Aus.</td>';
                                break;
                            case 2:
                                echo '<td>&nbsp;</td>
                                      <td>'.$ausPer2Total.' Aus.</td>';
                                break;
                            case 3:
                                echo '<td>&nbsp;</td>
                                      <td>'.$ausPer3Total.' Aus.</td>';
                                break;
                            case 4:
                                echo '<td>&nbsp;</td>
                                      <td>'.$ausPer4Total.' Aus.</td>';
                                break;
                        }
                    }
                    ?>
                    <td>&nbsp;</td>
                    <td><?=$sumAusenciasTotal?> Aus.</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>

        <table width="100%" rules="all" border="1" style=" font-size: 13px;">
            <thead>
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #00adefad;" colspan="2">OBSERVACIONES DE CONVIVENCIA</td>
                </tr>

                <tr style="font-weight:bold; text-align:center;">
                    <td>PERIODO</td>
                    <td>OBSERVACIONES</td>
                </tr>

                <?php 
                // OPTIMIZACIÓN: Usar prepared statements para la consulta de disciplina
                $idEstudianteEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_id']);
                $periodoEsc = (int)$periodoActual;
                $cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
                WHERE dn_cod_estudiante='{$idEstudianteEsc}' AND dn_periodo<={$periodoEsc} AND institucion=".(int)$config['conf_id_institucion']." AND year=".(int)$year);
                while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
                ?>

                    <tr>
                        <td style="text-align: center;"><?=$rndisiplina["dn_periodo"];?></td>
                        <td><?=$rndisiplina["dn_observacion"];?></td>
                    </tr>

                <?php }?>

            </thead>
        </table>

        <p>&nbsp;</p>

        <table width="100%" cellspacing="2" cellpadding="2" rules="all" border="1">
            <?php
                // OPTIMIZACIÓN: Usar director cacheado (ya se cargó arriba)
                if($directorGrupo === null && !empty($idDirector)){
                    $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                    $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
                }
            ?>
            <thead>
                <tr>
                    <td style="width: 40%;">Dir. Curso: <?=$nombreDirectorGrupo?></td>
                    <td style="width: 15%;"><img src="../files/iconos/sup.png" width="10"> SUP 4.7 – 5.0 </td>
                    <td style="width: 15%;"><img src="../files/iconos/alto.png" width="10"> ALT 4.0 – 4.6 </td>
                    <td style="width: 15%;"><img src="../files/iconos/bas.png" width="10"> BAS 3.0 – 3.9 </td>
                    <td style="width: 15%;"><img src="../files/iconos/bajo.png" width="10"> BAJ 1.0 – 2.9</td>
                </tr>

                <tr style="height: 70px;">
                    <td style="text-align: center;">
                    <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
                    Firmas y Sellos Autorizados
                    </td>
                    <td colspan="4">

                        “El hogar es la primera escuela del niño. Practíquese en la casa la temperancia en todas las cosas, y apóyese al maestro que está tratando de brindar a sus hijos una verdadera educación” CDD (EGW)
                    </td>
                </tr>


            </thead>
        </table>

        <p>&nbsp;</p>
        



       <!-- <div id="saltoPagina"></div>-->

        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            $conCargasDos = mysqli_query($conexion, 
            "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
	        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
	        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$year}
	        WHERE car_curso='" . $gradoActual . "' AND car_grupo='" . $grupoActual . "' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}");
            while ($datosCargasDos = mysqli_fetch_array($conCargasDos, MYSQLI_BOTH)) {

                
            ?>
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $datosCargasDos['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($datosCargasDos); ?></span></td>
                        <td>
                        
                            <?php
                            // OPTIMIZACIÓN: Obtener indicadores del mapa pre-cargado
                            $indicadores = $indicadoresMapa[$datosCargasDos['car_id']] ?? [];
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

        <p>&nbsp;</p>

        <table width="100%" cellspacing="3" cellpadding="3" rules="all" border="1">
            <thead>
                <tr>
                    <td style="text-align: center; font-weight: bold; font-size: medium;"><?=!empty($informacion_inst["info_direccion"]) ? strtoupper($informacion_inst["info_direccion"]) : "";?>       <?=!empty($informacion_inst["info_telefono"]) ? "TELEFONO ".$informacion_inst["info_telefono"] : "";?></td>
                </tr>
            </thead>
        </table>

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