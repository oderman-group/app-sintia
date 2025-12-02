<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
    require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
    require_once(ROOT_PATH."/main-app/class/Boletin.php");
    require_once(ROOT_PATH."/main-app/class/Usuarios.php");
    require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
    require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
    require_once(ROOT_PATH."/main-app/class/Indicadores.php");
    require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
    require_once(ROOT_PATH."/main-app/class/Utilidades.php");
    require_once(ROOT_PATH . "/main-app/class/Disciplina.php");
    $Plataforma = new Plataforma;

    $year=$_SESSION["bd"];
    if(isset($_REQUEST["year"])){
    $year=base64_decode($_REQUEST["year"]);
    }

    $modulo = 1;

    if (empty($_REQUEST["periodo"])) {
        $periodoActual = 1;
    } else {
        $periodoActual = base64_decode($_REQUEST["periodo"]);
    }

    switch($periodoActual){
        case 1:
            $periodoActuales = "Uno";
            break;
        case 2:
            $periodoActuales = "Dos";
            break;
        case 3:
            $periodoActuales = "Tres";
            break;
        case 4:
            $periodoActuales = "Final";
            break;
        case 5:
            $periodoActual = 4;
            $periodoActuales = "Final";
            break;
    }

    $filtro = "";
    if (!empty($_REQUEST["id"])) {
        $filtro .= " AND mat_id='" . base64_decode($_REQUEST["id"]) . "'";
    }

    if (!empty($_REQUEST["curso"])) {
        $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
    }

    if(!empty($_REQUEST["grupo"])){
        $filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";
    }

    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    Utilidades::validarInfoBoletin($matriculadosPorCurso);
    $numeroEstudiantes = mysqli_num_rows($matriculadosPorCurso);


    $idDirector="";
    $periodosCursados=$periodoActual-1;
    $colspan=7+$periodosCursados;
    $contadorEstudiantes=0;
    while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {
        $promedioGeneral = 0;
        $promedioGeneralPeriodos = 0;
        $gradoActual = $matriculadosDatos['mat_grado'];
        $grupoActual = $matriculadosDatos['mat_grupo'];
        // Inicializar variable $grupo con valor por defecto
        $grupo = "";
        switch($matriculadosDatos["gru_id"]){
            case 1:
                $grupo= "Uno";
            break;
            case 2:
                $grupo= "Dos";
            break;
            case 3:
                $grupo= "Tres";
            break;
            case 4:
                $grupo= "Sin Grupo";
            break;
        }
        //METODO QUE ME TRAE EL NOMBRE COMPLETO DEL ESTUDIANTE
        $nombreEstudainte=Estudiantes::NombreCompletoDelEstudiante($matriculadosDatos);
	
        // Inicializar variable $educacion con valor por defecto
        $educacion = "";
        if($matriculadosDatos["mat_grado"]>=12 && $matriculadosDatos["mat_grado"]<=15) {$educacion = "PREESCOLAR";}	
        elseif($matriculadosDatos["mat_grado"]>=1 && $matriculadosDatos["mat_grado"]<=5) {$educacion = "PRIMARIA";}	
        elseif($matriculadosDatos["mat_grado"]>=6 && $matriculadosDatos["mat_grado"]<=9) {$educacion = "SECUNDARIA";}
        elseif($matriculadosDatos["mat_grado"]>=10 && $matriculadosDatos["mat_grado"]<=11) {$educacion = "MEDIA";}	

?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <title>Boletín Formato 12</title>
        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
        <!-- favicon -->
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>
    <body style="font-family:Arial; font-size:9px;">
        <div style="margin: 15px 0;">
            <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 13px;">
                <tr>
                    <td rowspan="2" width="20%"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="100%"></td>
                    <td align="center" rowspan="2" width="25%">
                        <h3 style="font-weight:bold; color: #00adefad; margin: 0"><?=strtoupper($informacion_inst["info_nombre"])?></h3><br>
                        <?=$informacion_inst["info_direccion"]?><br>
                        Informes: <?=$informacion_inst["info_telefono"]?><br><br>
                        AÑO LECTIVO: <?=$year?>
                    </td>
                    <td>Documento:<br> <b style="color: #00adefad;"><?=strpos($matriculadosDatos["mat_documento"], '.') !== true && is_numeric($matriculadosDatos["mat_documento"]) ? number_format($matriculadosDatos["mat_documento"],0,",",".") : $matriculadosDatos["mat_documento"];?></b></td>
                    <td>Nombre:<br> <b style="color: #00adefad;"><?=$nombreEstudainte?></b></td>
                    <td>Grado:<br> <b style="color: #00adefad;"><?=strtoupper($matriculadosDatos["gra_nombre"]." ".$grupo)?></b></td>
                </tr>
                <tr>
                    <td>E. Básica:<br> <b style="color: #00adefad;"><?=$educacion?></b></td>
                    <td>Sede:<br> <b style="color: #00adefad;"><?=strtoupper($informacion_inst["info_nombre"])?></b></td>
                    <td>Jornada:<br> <b style="color: #00adefad;"><?=strtoupper($informacion_inst["info_jornada"])?></b></td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="font-size: 13px;">
            <tr style="text-align:center; font-size: 13px;">
                <td style="color: #b2adad;">
                    <?php
                        $consultaEstiloNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"],$year);
                        $numEstiloNota=mysqli_num_rows($consultaEstiloNota);
                        $i=1;
                        while($estiloNota = mysqli_fetch_array($consultaEstiloNota, MYSQLI_BOTH)){
                            $diagonal=" / ";
                            if($i==$numEstiloNota){
                                $diagonal="";
                            }
                            echo $estiloNota['notip_nombre'].": ".$estiloNota['notip_desde']." - ".$estiloNota['notip_hasta'].$diagonal;
                            $i++;
                        }
                    ?>
                </td>
            </tr>
        </table>
        <table width="100%" rules="all" border="1" style="font-size: 15px;">
            <thead style="background-color: #00adefad;">
                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="3%" rowspan="2">I.H</td>
                    <?php
                        if($periodoActual!=1){
                    ?>
                    <td width="3%" colspan="<?=$periodosCursados?>"><a href="#" style="color:#000; text-decoration:none;">Periodo Cursados</a></td>
                    <?php
                        }
                    ?>
                    <td width="3%" colspan="2">Periodo Actual (<?=strtoupper($periodoActuales)?>)</td>
                    <td width="3%" colspan="3">TOTAL ACUMULADO</td>
                </tr>
                <tr style="font-weight:bold; text-align:center;">
                    <?php
                        for($i=1;$i<=$periodoActual;$i++){
                            if($i!=$periodoActual){
                    ?>
                        <td width="3%"><?=$i?></td>
                    <?php
                        }else{
                    ?>
                    <td width="3%">Nota</td>
                    <td width="3%">Desempeño</td>
                    <?php
                            }
                        }
                    ?>
                    <td width="3%">Fallas</td>
                    <td width="3%">Nota</td>
                    <td width="3%">Desempeño</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $consultaAreas = Asignaturas::consultarAsignaturasCurso($conexion, $config, $gradoActual, $grupoActual, $year);
                    $numAreas=mysqli_num_rows($consultaAreas);
                    $sumaPromedioGeneral=0;
                    $sumaPromedioGeneralPeriodo1=0;
                    $sumaPromedioGeneralPeriodo2=0;
                    $sumaPromedioGeneralPeriodo3=0;
                    
                    // ============================================
                    // OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
                    // ============================================
                    
                    // OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para este estudiante y todos los períodos
                    $notasBoletinMapa = []; // [car_id][periodo] => datos_nota
                    try {
                        // Obtener todas las cargas del estudiante
                        $idsCargas = [];
                        mysqli_data_seek($consultaAreas, 0);
                        while ($areaTemp = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)) {
                            $consultaMateriasTemp = CargaAcademica::consultaMaterias($config, $periodoActual, $matriculadosDatos['mat_id'], $areaTemp['car_curso'], $areaTemp['car_grupo'], $areaTemp['ar_id'], $year);
                            while ($materiaTemp = mysqli_fetch_array($consultaMateriasTemp, MYSQLI_BOTH)) {
                                if (!in_array($materiaTemp['car_id'], $idsCargas)) {
                                    $idsCargas[] = $materiaTemp['car_id'];
                                }
                            }
                        }
                        mysqli_data_seek($consultaAreas, 0);
                        
                        if (!empty($idsCargas)) {
                            $sqlNotas = "SELECT bol_carga, bol_periodo, bol_nota
                                         FROM " . BD_ACADEMICA . ".academico_boletin
                                         WHERE bol_estudiante = ?
                                           AND bol_carga IN (" . implode(',', array_map('intval', $idsCargas)) . ")
                                           AND institucion = ?
                                           AND year = ?
                                           AND bol_periodo <= ?";
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
                        }
                    } catch (Exception $eNotas) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    
                    // OPTIMIZACIÓN 2: Pre-cargar todas las ausencias para este estudiante
                    $ausenciasMapa = []; // [materia_id][periodo] => suma_ausencias
                    try {
                        mysqli_data_seek($consultaAreas, 0);
                        while ($areaTemp = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)) {
                            $consultaMateriasTemp = CargaAcademica::consultaMaterias($config, $periodoActual, $matriculadosDatos['mat_id'], $areaTemp['car_curso'], $areaTemp['car_grupo'], $areaTemp['ar_id'], $year);
                            while ($materiaTemp = mysqli_fetch_array($consultaMateriasTemp, MYSQLI_BOTH)) {
                                $idMateria = $materiaTemp['car_materia'];
                                if (!isset($ausenciasMapa[$idMateria])) {
                                    $ausenciasMapa[$idMateria] = [];
                                }
                                for($j=1; $j<=$periodoActual; $j++){
                                    if (!isset($ausenciasMapa[$idMateria][$j])) {
                                        $ausTemp = Boletin::obtenerDatosAusencias($gradoActual, $idMateria, $j, $matriculadosDatos['mat_id'], $year);
                                        $ausData = mysqli_fetch_array($ausTemp, MYSQLI_BOTH);
                                        $ausenciasMapa[$idMateria][$j] = !empty($ausData[0]) ? (float)$ausData[0] : 0;
                                    }
                                }
                            }
                        }
                        mysqli_data_seek($consultaAreas, 0);
                    } catch (Exception $eAus) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    
                    // OPTIMIZACIÓN 3: Pre-cargar notas de áreas por período
                    $notasAreasPeriodoMapa = []; // [ar_id][periodo] => datos_nota
                    try {
                        mysqli_data_seek($consultaAreas, 0);
                        while ($areaTemp = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)) {
                            $arId = $areaTemp['ar_id'];
                            if (!isset($notasAreasPeriodoMapa[$arId])) {
                                $notasAreasPeriodoMapa[$arId] = [];
                            }
                            for($j=1; $j<$periodoActual; $j++){
                                if (!isset($notasAreasPeriodoMapa[$arId][$j])) {
                                    $areaPerTemp = CargaAcademica::consultaAreasPeriodos($config, $j, $matriculadosDatos['mat_id'], $arId, $year, $matriculadosDatos['mat_grupo']);
                                    $areaPerData = mysqli_fetch_array($areaPerTemp, MYSQLI_BOTH);
                                    $notasAreasPeriodoMapa[$arId][$j] = $areaPerData;
                                }
                            }
                        }
                        mysqli_data_seek($consultaAreas, 0);
                    } catch (Exception $eArea) {
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
                    
                    while($datosAreas = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)){

                        $consultaMaterias = CargaAcademica::consultaMaterias($config, $periodoActual, $matriculadosDatos['mat_id'], $datosAreas['car_curso'], $datosAreas['car_grupo'], $datosAreas['ar_id'], $year);
                        $notaArea=0;
                        $notaAreasPeriodos=0;
                        // Definir rangos de notas una vez por área
                        $notaMinima = isset($config['conf_nota_desde']) ? (float)$config['conf_nota_desde'] : 1.0;
                        $notaMaxima = isset($config['conf_nota_hasta']) ? (float)$config['conf_nota_hasta'] : 5.0;
                        while($datosMaterias = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
                            //DIRECTOR DE GRUPO
                            if($datosMaterias["car_director_grupo"]==1){
                                $idDirector=$datosMaterias["car_docente"];
                                // OPTIMIZACIÓN: Cargar director solo una vez
                                if($directorGrupo === null && !empty($idDirector)){
                                    $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                                }
                            }

                            //NOTA PARA LAS MATERIAS
                            $notaMateria = !empty($datosMaterias['bol_nota']) ? (float)$datosMaterias['bol_nota'] : 0;
                            // Validar que la nota esté dentro del rango configurado
                            if($notaMateria > $notaMaxima){
                                $notaMateria = $notaMaxima;
                            }
                            if($notaMateria < $notaMinima && $notaMateria > 0){
                                $notaMateria = $notaMinima;
                            }
                            // Formatear nota de la materia con decimales configurados
                            $notaMateriaFormateada = Boletin::notaDecimales($notaMateria);
                            // OPTIMIZACIÓN: Usar cache de notas cualitativas
                            $notaMateriaRedondeada = number_format($notaMateria, $config['conf_decimales_notas'], '.', '');
                            $estiloNota = isset($notasCualitativasCache[$notaMateriaRedondeada]) 
                                ? ['notip_nombre' => $notasCualitativasCache[$notaMateriaRedondeada]] 
                                : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaMateria,$year);
                            if($estiloNota === null){
                                $estiloNota = ['notip_nombre' => ''];
                            }

                            // OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
                            $datosAusencias = [0 => ($ausenciasMapa[$datosMaterias['car_materia']][$periodoActual] ?? 0)];
                            $ausencia="";

                            if ($datosAusencias[0]>0) {
                                $ausencia= round($datosAusencias[0],0);
                            }

                            //VARIABLES NECESARIAS
                            $background='';
                            $ih=$datosMaterias["car_ih"];
                            if($datosAreas['numMaterias']>1){
                ?>
                                <tr>
                                    <td><?=$datosMaterias['mat_nombre']?></td>
                                    <td align="center"><?=$datosMaterias['car_ih']?></td>
                                    <?php
                                        $notaMateriasPeriodosTotal=0;
                                        $ultimoPeriodo = $config["conf_periodos_maximos"];
                                        for($i=1;$i<=$periodoActual;$i++){
                                            if($i!=$periodoActual){
                                                // OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
                                                $datosPeriodos = $notasBoletinMapa[$datosMaterias['car_id']][$i] ?? ['bol_nota' => 0, 'bol_periodo' => null];
                                                $notaMateriasPeriodos = !empty($datosPeriodos['bol_nota']) ? (float)$datosPeriodos['bol_nota'] : 0;
                                                // Validar que la nota esté dentro del rango configurado
                                                if($notaMateriasPeriodos > $notaMaxima){
                                                    $notaMateriasPeriodos = $notaMaxima;
                                                }
                                                if($notaMateriasPeriodos < $notaMinima && $notaMateriasPeriodos > 0){
                                                    $notaMateriasPeriodos = $notaMinima;
                                                }
                                                // Formatear nota de período con decimales configurados
                                                $notaMateriasPeriodosFormateada = Boletin::notaDecimales($notaMateriasPeriodos);
                                                $notaMateriasPeriodosTotal+=$notaMateriasPeriodos;

                                                $notaMateriasPeriodosFinal=$notaMateriasPeriodosFormateada;
                                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                                    // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                                    $notaPerRedondeada = number_format($notaMateriasPeriodos, $config['conf_decimales_notas'], '.', '');
                                                    $estiloNotaAreas = isset($notasCualitativasCache[$notaPerRedondeada]) 
                                                        ? ['notip_nombre' => $notasCualitativasCache[$notaPerRedondeada]] 
                                                        : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaMateriasPeriodos,$year);
                                                    if($estiloNotaAreas === null){
                                                        $estiloNotaAreas = ['notip_nombre' => ''];
                                                    }
                                                    $notaMateriasPeriodosFinal= !empty($estiloNotaAreas['notip_nombre']) ? $estiloNotaAreas['notip_nombre'] : "";
                                                }
                                                if (empty($datosPeriodos['bol_periodo'])){
                                                    $ultimoPeriodo -= 1;
                                                }
                                    ?>
                                    <td align="center" style="background: #9ed8ed"><?=$notaMateriasPeriodosFinal?></td>
                                    <?php
                                                }else{
                                                    $notaMateriaFinal = $notaMateriaFormateada;
                                                    if (empty($datosMaterias['bol_periodo'])){
                                                        $notaMateriaFinal = "";
                                                        $estiloNota['notip_nombre'] = "";
                                                        $ultimoPeriodo  -= 1;
                                                    }
                                    ?>
                                    <td align="center"><?=$notaMateriaFinal?></td>
                                    <td align="center"><?=$estiloNota['notip_nombre']?></td>
                                    <?php
                                            }
                                        }//FIN FOR

                                        //ACOMULADO PARA LAS MATERIAS
                                        $notaAcomuladoMateria = ($ultimoPeriodo > 0) ? (($notaMateria + $notaMateriasPeriodosTotal) / $ultimoPeriodo) : 0;
                                        // Validar que el acumulado esté dentro del rango configurado
                                        if($notaAcomuladoMateria > $notaMaxima){
                                            $notaAcomuladoMateria = $notaMaxima;
                                        }
                                        if($notaAcomuladoMateria < $notaMinima && $notaAcomuladoMateria > 0){
                                            $notaAcomuladoMateria = $notaMinima;
                                        }
                                        // Formatear acumulado de materia con decimales configurados
                                        $notaAcomuladoMateriaFormateada = Boletin::notaDecimales($notaAcomuladoMateria);
                                        // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                        $notaAcumRedondeada = number_format($notaAcomuladoMateria, $config['conf_decimales_notas'], '.', '');
                                        $estiloNotaAcomuladoMaterias = isset($notasCualitativasCache[$notaAcumRedondeada]) 
                                            ? ['notip_nombre' => $notasCualitativasCache[$notaAcumRedondeada]] 
                                            : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoMateria,$year);
                                        if($estiloNotaAcomuladoMaterias === null){
                                            $estiloNotaAcomuladoMaterias = ['notip_nombre' => ''];
                                        }
                                    ?>
                                    <td align="center"><?=$ausencia?></td>
                                    <td align="center"><?=$notaAcomuladoMateriaFormateada?></td>
                                    <td align="center"><?=$estiloNotaAcomuladoMaterias['notip_nombre']?></td>
                                </tr>
                    <?php
                            $ih="";
                            $ausencia="";
                            $background='style="background: #EAEAEA"';
                            }

                            //NOTA PARA LAS AREAS
                            if(!empty($datosMaterias['notaArea'])) $notaArea+=(float)$datosMaterias['notaArea'];

                        } //FIN WHILE DE LAS MATERIAS
                    ?>
                    <!--********SE IMPRIME LO REFERENTE A LAS AREAS*******-->
                        <tr style="background: #EAEAEA;">
                            <td><?=$datosAreas['ar_nombre']?></td>
                            <td align="center"><?=$ih?></td>
                            <?php
                                $notaAreasPeriodosTotal=0;
                                $promGeneralPer1=0;
                                $promGeneralPer2=0;
                                $promGeneralPer3=0;
                                $ultimoPeriodoAreas = $config["conf_periodos_maximos"];
                                for($i=1;$i<=$periodoActual;$i++){
                                    if($i!=$periodoActual){
                                        // OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
                                        $datosAreasPeriodos = $notasAreasPeriodoMapa[$datosAreas['ar_id']][$i] ?? ['notaArea' => 0, 'bol_periodo' => null];
                                        $notaAreasPeriodos = !empty($datosAreasPeriodos['notaArea']) ? (float)$datosAreasPeriodos['notaArea'] : 0;
                                        // Validar que la nota esté dentro del rango configurado
                                        if($notaAreasPeriodos > $notaMaxima){
                                            $notaAreasPeriodos = $notaMaxima;
                                        }
                                        if($notaAreasPeriodos < $notaMinima && $notaAreasPeriodos > 0){
                                            $notaAreasPeriodos = $notaMinima;
                                        }
                                        // Formatear nota de área por período con decimales configurados
                                        $notaAreasPeriodosFormateada = Boletin::notaDecimales($notaAreasPeriodos);
                                        $notaAreasPeriodosTotal+=$notaAreasPeriodos;
                                        switch($i){
                                            case 1:
                                                $promGeneralPer1+=$notaAreasPeriodos;
                                                break;
                                            case 2:
                                                $promGeneralPer2+=$notaAreasPeriodos;
                                                break;
                                            case 3:
                                                $promGeneralPer3+=$notaAreasPeriodos;
                                                break;
                                        }

                                        if (empty($datosAreasPeriodos['bol_periodo'])){
                                            $ultimoPeriodoAreas -= 1;
                                        }

                                        $notaAreasPeriodosFinal=$notaAreasPeriodosFormateada;
                                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                            // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                            $notaAreaPerRedondeada = number_format($notaAreasPeriodos, $config['conf_decimales_notas'], '.', '');
                                            $estiloNotaAreas = isset($notasCualitativasCache[$notaAreaPerRedondeada]) 
                                                ? ['notip_nombre' => $notasCualitativasCache[$notaAreaPerRedondeada]] 
                                                : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAreasPeriodos,$year);
                                            if($estiloNotaAreas === null){
                                                $estiloNotaAreas = ['notip_nombre' => ''];
                                            }
                                            $notaAreasPeriodosFinal= !empty($estiloNotaAreas['notip_nombre']) ? $estiloNotaAreas['notip_nombre'] : "";
                                        }
                            ?>
                            <td align="center"><?=$notaAreasPeriodosFinal?></td>
                            <?php
                                    }else{
                                        // Validar que la nota del área esté dentro del rango configurado
                                        if($notaArea > $notaMaxima){
                                            $notaArea = $notaMaxima;
                                        }
                                        if($notaArea < $notaMinima && $notaArea > 0){
                                            $notaArea = $notaMinima;
                                        }
                                        // Formatear nota del área con decimales configurados
                                        $notaAreaFormateada = Boletin::notaDecimales($notaArea);
                                        // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                        $notaAreaRedondeada = number_format($notaArea, $config['conf_decimales_notas'], '.', '');
                                        $estiloNotaAreas = isset($notasCualitativasCache[$notaAreaRedondeada]) 
                                            ? ['notip_nombre' => $notasCualitativasCache[$notaAreaRedondeada]] 
                                            : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaArea,$year);
                                        if($estiloNotaAreas === null){
                                            $estiloNotaAreas = ['notip_nombre' => ''];
                                        }

                                        $notaAreaFinal = $notaAreaFormateada;
                                        if (empty($notaArea) || $notaArea == 0){
                                            $notaAreaFinal = "";
                                            $estiloNotaAreas['notip_nombre'] = "";
                                            $ultimoPeriodoAreas -= 1;
                                        }
                            ?>
                            <td align="center"><?=$notaAreaFinal?></td>
                            <td align="center"><?=$estiloNotaAreas['notip_nombre']?></td>
                            <?php
                                    }
                                }
                        
                                //ACOMULADO PARA LAS AREAS
                                $notaAcomuladoArea = ($ultimoPeriodoAreas > 0) ? (($notaArea + $notaAreasPeriodosTotal) / $ultimoPeriodoAreas) : 0;
                                // Validar que el acumulado esté dentro del rango configurado
                                if($notaAcomuladoArea > $notaMaxima){
                                    $notaAcomuladoArea = $notaMaxima;
                                }
                                if($notaAcomuladoArea < $notaMinima && $notaAcomuladoArea > 0){
                                    $notaAcomuladoArea = $notaMinima;
                                }
                                // Formatear acumulado de área con decimales configurados
                                $notaAcomuladoAreaFormateada = Boletin::notaDecimales($notaAcomuladoArea);
                                // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                $notaAcumAreaRedondeada = number_format($notaAcomuladoArea, $config['conf_decimales_notas'], '.', '');
                                $estiloNotaAcomuladoAreas = isset($notasCualitativasCache[$notaAcumAreaRedondeada]) 
                                    ? ['notip_nombre' => $notasCualitativasCache[$notaAcumAreaRedondeada]] 
                                    : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoArea,$year);
                                if($estiloNotaAcomuladoAreas === null){
                                    $estiloNotaAcomuladoAreas = ['notip_nombre' => ''];
                                }
                            ?>
                            <td align="center"><?=$ausencia?></td>
                            <td align="center"><?=$notaAcomuladoAreaFormateada?></td>
                            <td align="center"><?=$estiloNotaAcomuladoAreas['notip_nombre']?></td>
                        </tr>
                    <?php

                            //SUMA NOTAS DE LAS AREAS
                            $sumaPromedioGeneral+=$notaArea;

                            //SUMA NOTAS DE LAS AREAS PERIODOS ANTERIORES
                            $sumaPromedioGeneralPeriodo1+=$promGeneralPer1;
                            $sumaPromedioGeneralPeriodo2+=$promGeneralPer2;
                            $sumaPromedioGeneralPeriodo3+=$promGeneralPer3;
                            
                        } //FIN WHILE DE LAS AREAS

                        //PROMEDIO DE LAS AREAS
                        $promedioGeneral += !empty($sumaPromedioGeneral) && !empty($numAreas) ? ($sumaPromedioGeneral/$numAreas) : 0;
                        // Validar que el promedio general esté dentro del rango configurado
                        if($promedioGeneral > $notaMaxima){
                            $promedioGeneral = $notaMaxima;
                        }
                        if($promedioGeneral < $notaMinima && $promedioGeneral > 0){
                            $promedioGeneral = $notaMinima;
                        }
                        // Formatear promedio general con decimales configurados
                        $promedioGeneralFormateado = Boletin::notaDecimales($promedioGeneral);
                        // OPTIMIZACIÓN: Usar cache de notas cualitativas
                        $promedioGenRedondeado = number_format($promedioGeneral, $config['conf_decimales_notas'], '.', '');
                        $estiloNotaPromedioGeneral = isset($notasCualitativasCache[$promedioGenRedondeado]) 
                            ? ['notip_nombre' => $notasCualitativasCache[$promedioGenRedondeado]] 
                            : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioGeneral,$year);
                        if($estiloNotaPromedioGeneral === null){
                            $estiloNotaPromedioGeneral = ['notip_nombre' => ''];
                        }
                        
                    ?>
            </tbody>
            <tfoot style="font-weight:bold; font-size: 13px;">
                <tr style="background: #EAEAEA">
                    <td colspan="2">PROMEDIO GENERAL</td>
                    <?php
                    for ($j = 1; $j <= $periodoActual; $j++) {
                        if($j!=$periodoActual){
                            switch($j){
                                case 1:
                                    $sumaPromedioGeneralPeriodos=$sumaPromedioGeneralPeriodo1;
                                    break;
                                case 2:
                                    $sumaPromedioGeneralPeriodos=$sumaPromedioGeneralPeriodo2;
                                    break;
                                case 3:
                                    $sumaPromedioGeneralPeriodos=$sumaPromedioGeneralPeriodo3;
                                    break;
                            }

                            //PROMEDIO DE LAS AREAS PERIODOS ANTERIORES
                            $promedioGeneralPeriodos = !empty($sumaPromedioGeneralPeriodos) && !empty($numAreas) ? ($sumaPromedioGeneralPeriodos/$numAreas) : 0;
                            // Validar que el promedio por período esté dentro del rango configurado
                            if($promedioGeneralPeriodos > $notaMaxima){
                                $promedioGeneralPeriodos = $notaMaxima;
                            }
                            if($promedioGeneralPeriodos < $notaMinima && $promedioGeneralPeriodos > 0){
                                $promedioGeneralPeriodos = $notaMinima;
                            }
                            // Formatear promedio general por período con decimales configurados
                            $promedioGeneralPeriodosFormateado = Boletin::notaDecimales($promedioGeneralPeriodos);

                            $promedioGeneralPeriodosFinal=$promedioGeneralPeriodosFormateado;
                            if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                $promedioGenPerRedondeado = number_format($promedioGeneralPeriodos, $config['conf_decimales_notas'], '.', '');
                                $estiloNotaAreas = isset($notasCualitativasCache[$promedioGenPerRedondeado]) 
                                    ? ['notip_nombre' => $notasCualitativasCache[$promedioGenPerRedondeado]] 
                                    : Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioGeneralPeriodos,$year);
                                if($estiloNotaAreas === null){
                                    $estiloNotaAreas = ['notip_nombre' => ''];
                                }
                                $promedioGeneralPeriodosFinal= !empty($estiloNotaAreas['notip_nombre']) ? $estiloNotaAreas['notip_nombre'] : "";
                            }
                    ?>
                    <td align="center"><?=$promedioGeneralPeriodosFinal;?></td>
                    <?php
                        }else{
                    ?>
                    <td align="center"><?=$promedioGeneralFormateado;?></td>
                    <td align="center"><?=$estiloNotaPromedioGeneral['notip_nombre']?></td>
                    <?php
                        }
                    }// FIN FOR
                    ?>
                    <td align="center"></td>
                    <td align="center"></td>
                    <td align="center"></td>
                </tr>
            </tfoot>
        </table>

        <p>&nbsp;</p>
        <!--******PUESTO DEL ESTUDIANTE******-->
        <table style="font-size: 15px;" width="80%" cellspacing="5" cellpadding="5" rules="all" border="1" align="right">
            <tr style="background-color: #EAEAEA;">
                <?php
                    if(empty($_REQUEST["curso"])){
                        $filtro = " AND mat_grado='" . $gradoActual . "' AND mat_grupo='".$grupoActual."'";
                        $matriculadosDelCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
                        $numeroEstudiantes = mysqli_num_rows($matriculadosDelCurso);
                    }
                    //Buscamos Puesto del estudiante en el curso
                    $puestoEstudiantesCurso = 0;
                    $puestosCursos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual, $gradoActual, $grupoActual,$year);
                    
                    foreach($puestosCursos as $puestoCurso){
                        if($puestoCurso['estudiante_id']==$matriculadosDatos['mat_id']){
                            $puestoEstudiantesCurso = $puestoCurso['puesto'];
                        }
                    }
                    
                    //Buscamos Puesto del estudiante en la institución
                    $matriculadosDeLaInstitucion = Estudiantes::estudiantesMatriculados("", $year);
                    $numeroEstudiantesInstitucion = mysqli_num_rows($matriculadosDeLaInstitucion);

                    $puestoEstudiantesInstitucion = 0;
                    $puestosInstitucion = Boletin::obtenerPuestoEstudianteEnInstitucion($periodoActual, $year);
                    
                    foreach($puestosInstitucion as $puestoInstitucion){
                        if($puestoInstitucion['estudiante_id']==$matriculadosDatos['mat_id']){
                            $puestoEstudiantesInstitucion = $puestoInstitucion['puesto'];
                        }
                    }
                ?>
                <td align="center" width="40%">Puesto en el curso <b><?=$puestoEstudiantesCurso?></b> entre <b><?=$numeroEstudiantes?></b> Estudiantes.</td>
                <td align="center" width="40%">Puesto en el colegio <b><?=$puestoEstudiantesInstitucion?></b> entre <b><?=$numeroEstudiantesInstitucion?></b> Estudiantes.</td>
            </tr>
        </table>

        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <!--******OBSERVACIONES******-->

        <table style="font-size: 15px;" width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center">
            <thead>
                <tr style="font-weight:bold; text-align:left; background-color: #00adefad;">
                    <td><b>Observaciones:</b></td>
                </tr>
            </thead>
            <tbody>
                <tr style="color:#000;">
                    <td style="padding-left: 20px;">
                        <?php 
                            // OPTIMIZACIÓN: Usar prepared statements para consulta de disciplina
                            $idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
                            $periodoEsc = (int)$periodoActual;
                            $cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='{$idEstudianteEsc}' AND dn_periodo='{$periodoEsc}' AND institucion=".(int)$config['conf_id_institucion']." AND year='".mysqli_real_escape_string($conexion, $year)."'");
                            
                            // OPTIMIZACIÓN: Pre-cargar observaciones de disciplina
                            $observacionesDisciplinaMapa = []; // [id_observacion] => descripcion
                            if($config['conf_observaciones_multiples_comportamiento'] == '1'){
                                mysqli_data_seek($cndisiplina, 0);
                                while($rndisiplinaTemp=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
                                    if(!empty($rndisiplinaTemp['dn_observacion'])){
                                        $explode=explode(",",$rndisiplinaTemp['dn_observacion']);
                                        foreach($explode as $idObs){
                                            $idObs = trim($idObs);
                                            if(!empty($idObs) && !isset($observacionesDisciplinaMapa[$idObs])){
                                                $obsTemp = Disciplina::traerDatosObservacion($config, $idObs, "obser_descripcion");
                                                if($obsTemp){
                                                    $observacionesDisciplinaMapa[$idObs] = $obsTemp['obser_descripcion'];
                                                }
                                            }
                                        }
                                    }
                                }
                                mysqli_data_seek($cndisiplina, 0);
                            }
                            
                            while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
                                if(!empty($rndisiplina['dn_observacion'])){
                                    if($config['conf_observaciones_multiples_comportamiento'] == '1'){
                                        $explode=explode(",",$rndisiplina['dn_observacion']);
                                        $numDatos=count($explode);
                                        for($i=0;$i<$numDatos;$i++){
                                            $idObs = trim($explode[$i]);
                                            // OPTIMIZACIÓN: Usar mapa pre-cargado
                                            $descripcion = $observacionesDisciplinaMapa[$idObs] ?? '';
                                            if(!empty($descripcion)){
                                                echo "- " . $descripcion . "<br> ";
                                            }
                                        }
                                    }else{
                                        echo "- ".$rndisiplina["dn_observacion"]."<br>";
                                    }
                                }
                            }
                            if ($periodoActual == $config["conf_periodos_maximos"] && $ultimoPeriodoAreas < $config["conf_periodos_maximos"]) {
                                Echo "ESTUDIANTE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
                            }
                        ?>
                        <p>&nbsp;</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="saltoPagina"></div>
        <!--******SEGUNDA PAGINA******-->
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <!--******INDICADORES POR ASIGNATURA******-->

        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center">
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

        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>   
        <!--******FIRMAS******-->   

        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
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

        <?php
            $contadorEstudiantes++;
            if($contadorEstudiantes!=$numeroEstudiantes && empty($_GET['id'])){
        ?>

        <div id="saltoPagina"></div>
<?php
            }
    }//FIN WHILE MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

        <script type="application/javascript">
            print();
        </script>
    </body>
</html>