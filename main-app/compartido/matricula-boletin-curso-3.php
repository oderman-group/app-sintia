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
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$year=$_SESSION["bd"];
if(isset($_GET["year"])){
    $year=base64_decode($_GET["year"]);
}

if (empty($_GET["periodo"])) {

    $periodoActual = 1;
} else {

    $periodoActual = base64_decode($_GET["periodo"]);
}

$filtro = "";
if (!empty($_GET["id"])) {

    $filtro .= " AND mat_id='" . base64_decode($_GET["id"]) . "'";
}

if (!empty($_REQUEST["curso"])) {

    $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}
if(!empty($_REQUEST["grupo"])){
    $filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";
}

$estudiantesCache = 'estudiantes_' . $_SESSION["idInstitucion"] . '_' . $year . '_' . base64_decode($_REQUEST["curso"]) . '_' .base64_decode($_REQUEST["grupo"]) . '_P' .$periodoActual. '.json';

if (!empty($_GET["id"])) {

    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
   
    $rows = [];

    while ($resultado = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_ASSOC)) {
        $rows[] = $resultado;
    }

} else if (!file_exists($estudiantesCache) && empty($_GET['refreshStudents'])) {
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    $rows = [];

    while ($resultado = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_ASSOC)) {
        $rows[] = $resultado;
    }

    file_put_contents($estudiantesCache, json_encode($rows));

    $ruta = "matricula-boletin-curso-3.php?id=".$_GET["id"]."&periodo=".$_GET["periodo"]."&curso=".$_REQUEST["curso"]."&grupo=".$_REQUEST["grupo"]."&year=".$_GET["year"];

    echo '<script type="text/javascript">window.location.href="'.$ruta.'";</script>';
	exit();

} else {
    $rows = Estudiantes::estudiantesMatriculadosCache($estudiantesCache);
}
Utilidades::validarInfoBoletin($rows);
$tamañoLogo = $_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50; //TODO: Esto debe ser una configuración

$modulo = 1;

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
    : ($_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50); // Por defecto según institución
$logoAlto = $formularioEnviado && isset($_GET['logo_alto']) && is_numeric($_GET['logo_alto'])
    ? (int)$_GET['logo_alto']
    : 0; // 0 significa que no se especifica alto (solo ancho)

// Actualizar $tamañoLogo para mantener compatibilidad
$tamañoLogo = $logoAncho;

?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

<?php
foreach($rows as $matriculadosDatos) {

    //contador materias

    $cont_periodos = 0;

    $contador_indicadores = 0;

    $materiasPerdidas = 0;

    //======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
    $usr = Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
    $num_usr = mysqli_num_rows($usr);


    if ($num_usr == 0) {
        continue;
    }

    $datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
    $nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);	

    $contador_periodos = 0;

    ?>

    <!doctype html>

    <html class="no-js" lang="en">
    <head>

        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">

        <title>Boletín Formato 3</title>
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            #saltoPagina {

                PAGE-BREAK-AFTER: always;

            }
        </style>

    </head>



    <body style="font-family:Arial;">

        <?php

        //CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
        $consulta_mat_area_est = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);

        $numero_periodos = $config["conf_periodo"];

        // ============================================
        // OPTIMIZACIONES: PRE-CARGAR DATOS
        // ============================================

        // OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para todas las cargas y períodos
        // [carga][periodo] => datos_nota (incluye observaciones)
        $notasBoletinMapa = [];
        $consultaCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
        $idsCargas = [];
        while($cargaTemp = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH)){
            $idsCargas[] = $cargaTemp['car_id'];
        }
        mysqli_data_seek($consultaCargas, 0); // Resetear para uso posterior

        if(!empty($idsCargas)){
            $idsCargasEsc = array_map(function($id) use ($conexion) {
                return "'" . mysqli_real_escape_string($conexion, $id) . "'";
            }, $idsCargas);
            $inCargas = implode(',', $idsCargasEsc);
            $idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
            $institucion = (int)$config['conf_id_institucion'];
            $yearEsc = mysqli_real_escape_string($conexion, $year);
            
            $sqlNotas = "SELECT bol_carga, bol_periodo, bol_nota, bol_observaciones_boletin
                         FROM " . BD_ACADEMICA . ".academico_boletin
                         WHERE bol_estudiante = '{$idEstudianteEsc}'
                         AND bol_carga IN ({$inCargas})
                         AND institucion = {$institucion}
                         AND year = '{$yearEsc}'";
            
            $consultaNotas = mysqli_query($conexion, $sqlNotas);
            if($consultaNotas){
                while($nota = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH)){
                    $idCarga = $nota['bol_carga'];
                    $periodo = (int)$nota['bol_periodo'];
                    if(!isset($notasBoletinMapa[$idCarga])){
                        $notasBoletinMapa[$idCarga] = [];
                    }
                    $notasBoletinMapa[$idCarga][$periodo] = $nota;
                }
            }
        }

        // OPTIMIZACIÓN 2: Pre-cargar todas las recuperaciones de indicadores
        // [indicador][carga] => datos_recuperacion
        $recuperacionesMapa = [];
        if(!empty($idsCargas)){
            $idsCargasEsc2 = array_map(function($id) use ($conexion) {
                return "'" . mysqli_real_escape_string($conexion, $id) . "'";
            }, $idsCargas);
            $inCargas2 = implode(',', $idsCargasEsc2);
            
            $sqlRecuperaciones = "SELECT rind_indicador, rind_carga, rind_nota
                                  FROM " . BD_ACADEMICA . ".academico_indicadores_recuperacion
                                  WHERE rind_estudiante = '{$idEstudianteEsc}'
                                  AND rind_carga IN ({$inCargas2})
                                  AND rind_periodo = " . (int)$periodoActual . "
                                  AND institucion = {$institucion}
                                  AND year = '{$yearEsc}'";
            
            $consultaRecuperaciones = mysqli_query($conexion, $sqlRecuperaciones);
            if($consultaRecuperaciones){
                while($rec = mysqli_fetch_array($consultaRecuperaciones, MYSQLI_BOTH)){
                    $idIndicador = $rec['rind_indicador'];
                    $idCarga = $rec['rind_carga'];
                    if(!isset($recuperacionesMapa[$idIndicador])){
                        $recuperacionesMapa[$idIndicador] = [];
                    }
                    $recuperacionesMapa[$idIndicador][$idCarga] = $rec;
                }
            }
        }

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
                while ($notaTipo = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)) {
                    for ($i = $notaTipo['notip_desde']; $i <= $notaTipo['notip_hasta']; $i += 0.1) {
                        $key = number_format((float)$i, $config['conf_decimales_notas'], '.', '');
                        if (!isset($notasCualitativasCache[$key])) {
                            $notasCualitativasCache[$key] = $notaTipo['notip_nombre'];
                        }
                    }
                }
            }
        }

        // OPTIMIZACIÓN 4: Cachear datos de usuarios (director y rector)
        $directorGrupo = null;
        $rector = null;
        $idDirector = null; // Se establecerá en el bucle de materias

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
                        Ancho (%): <input type="number" name="logo_ancho" value="<?= $logoAncho ?>" min="1" max="100" style="width: 60px;">
                    </label>
                    <label style="display: block; margin-bottom: 5px;">
                        Alto (px, 0=auto): <input type="number" name="logo_alto" value="<?= $logoAlto ?>" min="0" style="width: 60px;">
                    </label>
                </div>
                <button type="submit" style="background: #34495e; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%;">Aplicar Configuración</button>
            </form>
        </div>
        <style>
            @media print {
                .config-boletin-form { display: none !important; }
            }
        </style>

        <div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" <?= $logoAncho > 0 ? 'width="'.$logoAncho.'%"' : '' ?> <?= $logoAlto > 0 ? 'height="'.$logoAlto.'"' : '' ?>><br>
    <!-- <?=$informacion_inst["info_nombre"]?><br>
    BOLETÍN DE CALIFICACIONES<br> -->

        </div>





        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">

            <tr>

                <td>Documento:<br> <?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></td>

                <td>Nombre:<br> <?=$nombre?></td>

                <td>Grado:<br> <?= $datosUsr["gra_nombre"] . " " . $datosUsr["gru_nombre"]; ?></td>

                <td>&nbsp;</td>

            </tr>



            <tr>

                <td>Jornada:<br> Mañana</td>

                <td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>

                <td>Periodo:<br> <b><?= $periodoActuales . " (" . $year . ")"; ?></b></td>

                <td>Fecha Impresión:<br> <?= date("d/m/Y H:i:s"); ?></td>

            </tr>

        </table>

        <p>&nbsp;</p>



        <table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

            <tr style="font-weight:bold; background-color:#2e537dab; border-color:#000; height:40px; color:#000; font-size:12px;">

                <td width="2%" align="center">NO</td>

                <td width="20%" align="center">AREAS/ ASIGNATURAS</td>

                <?php if($mostrarIH): ?>
                <td width="2%" align="center">I.H</td>
                <?php endif; ?>

                <td width="2%" align="center">NOTA</td>

            </tr>



            <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->

            <?php

            $contador = 1;

            while ($fila = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {



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
                $consulta_a_mat_indicadores = Boletin::obtenerIndicadoresPorMateria($datosUsr["mat_grado"], $datosUsr["mat_grupo"], $fila["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);

                $numIndicadores = mysqli_num_rows($consulta_a_mat_indicadores);



                $resultado_not_area = mysqli_fetch_array($consulta_notdef_area, MYSQLI_BOTH);

                $numfilas_not_area = mysqli_num_rows($consulta_notdef_area);

                if ($numfilas_not_area > 0) {
                    // Mostrar fila del área solo si está habilitado
                    if ($mostrarAreas) {
            ?>
                    <tr style="background-color: #b9b91730" style="font-size:12px;">
                        <td colspan="<?= $mostrarIH ? '2' : '1'; ?>" style="font-size:12px; height:25px; font-weight:bold;"><?php echo $resultado_not_area["ar_nombre"]; ?></td>
                        <?php if($mostrarIH): ?>
                        <td align="center" style="font-weight:bold; font-size:12px;"></td>
                        <?php endif; ?>
                        <td>&nbsp;</td>
                    </tr>
            <?php
                    }
                    // Las materias siempre se muestran, independientemente de si se muestra el área
                    while ($fila2 = mysqli_fetch_array($consulta_a_mat, MYSQLI_BOTH)) {
                        //DIRECTOR DE GRUPO
                        if($fila2["car_director_grupo"]==1){
                            $idDirector=$fila2["car_docente"];
                        }

                        $contador_periodos = 0;

                        mysqli_data_seek($consulta_a_mat_per, 0);

                    ?>

                        <tr bgcolor="#EAEAEA" style="font-size:12px;">

                            <td align="center"><?= $contador; ?></td>

                            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;"><?php echo $fila2["mat_nombre"]; ?></td>

                            <?php if($mostrarIH): ?>
                            <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?php echo $fila["car_ih"]; ?></td>
                            <?php endif; ?>

                            <td>&nbsp;</td>

                        </tr>

                        <?php

                        if ($mostrarIndicadores && $numIndicadores > 0) {

                            mysqli_data_seek($consulta_a_mat_indicadores, 0);

                            $contador_indicadores = 0;

                            while ($fila4 = mysqli_fetch_array($consulta_a_mat_indicadores, MYSQLI_BOTH)) {

                                    if ($fila4["mat_id"] == $fila2["mat_id"]) {
                                    // OPTIMIZACIÓN: Obtener recuperación del mapa pre-cargado
                                    $recuperacionIndicador = $recuperacionesMapa[$fila4["ind_id"]][$fila2["car_id"]] ?? null;

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
                                        $notaRedondeada = number_format((float)$nota_indicador, $config['conf_decimales_notas'], '.', '');
                                        $notaIndicadorFinal = isset($notasCualitativasCache[$notaRedondeada]) 
                                            ? $notasCualitativasCache[$notaRedondeada] 
                                            : "";
                                    }

                                    $indicador_inclusion = "";

                                    if(!empty($fila4["aii_descripcion_indicador"])){$indicador_inclusion ="<br> <i>&nbsp;&nbsp;&nbsp;&nbsp;Modificado: ". $fila4["aii_descripcion_indicador"]."</i>";}

                        ?>

                                    <tr bgcolor="#FFF" style="font-size:12px;">

                                        <td align="center">&nbsp;</td>

                                        <td style="font-size:12px; height:15px;"><?php echo $contador_indicadores . ". " . $fila4["ind_nombre"].$indicador_inclusion; ?></td>

                                        <?php if($mostrarIH): ?>
                                        <td>&nbsp;</td>
                                        <?php endif; ?>

                                        <td align="center" style="font-weight:bold; font-size:12px;"><?= $notaIndicadorFinal." ".$leyendaRI; ?></td>

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
                        if (!empty($observacion['bol_observaciones_boletin'])) {

                        ?>

                            <tr>

                                <td colspan="4">

                                    <h5 align="center">Observaciones</h5>

                                    <p style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">

                                        <?= $observacion['bol_observaciones_boletin']; ?>

                                    </p>

                                </td>

                            </tr>

                        <?php 
                        }
                        $contador++;
                    } //while fin materias
                }
            } //while fin areas

            // OPTIMIZACIÓN: Usar prepared statements para consulta de Media Técnica
            $idMatriculaEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
            $institucion = (int)$config['conf_id_institucion'];
            $yearEsc = mysqli_real_escape_string($conexion, $year);
            $estadoActivo = mysqli_real_escape_string($conexion, ACTIVO);
            $consultaMediaTecnica=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".mediatecnica_matriculas_cursos 
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_curso=matcur_id_curso AND car_grupo=matcur_id_grupo AND car.institucion={$institucion} AND car.year='{$yearEsc}'
            INNER JOIN ".BD_ACADEMICA.".academico_materias am ON mat_id=car_materia AND am.institucion={$institucion} AND am.year='{$yearEsc}'
            INNER JOIN ".BD_ACADEMICA.".academico_areas ar ON ar_id= mat_area AND ar.institucion={$institucion} AND ar.year='{$yearEsc}'
            WHERE matcur_id_matricula='{$idMatriculaEsc}' AND matcur_estado='{$estadoActivo}' AND matcur_id_institucion='{$institucion}' AND matcur_years='{$yearEsc}'
            GROUP BY ar_id ORDER BY ar_posicion ASC;");
            $numMediaTecnica=mysqli_num_rows($consultaMediaTecnica);
            if ((array_key_exists(10, $_SESSION["modulos"])) && $numMediaTecnica>0){
                $contador = 1;
                while ($fila = mysqli_fetch_array($consultaMediaTecnica, MYSQLI_BOTH)) {
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
                    $consultaNotaDefArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
    
                    //CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
                    $consultaMat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
    
                    //CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
                    $consultaMatPeriodo = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

                    //CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
                    $consultaMatIndicadores = Boletin::obtenerIndicadoresPorMateria($fila["car_curso"], $fila["car_grupo"], $fila["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);
                    
                    $numIndicadores = mysqli_num_rows($consultaMatIndicadores);
                    $resultadoNotArea = mysqli_fetch_array($consultaNotaDefArea, MYSQLI_BOTH);
    
                    $numFilasNotArea = mysqli_num_rows($consultaNotaDefArea);
                    if ($numFilasNotArea > 0) {
                        // Mostrar fila del área solo si está habilitado
                        if ($mostrarAreas) {
                ?>
                        <tr style="background-color: #e0e0153b" style="font-size:12px;">
                            <td colspan="<?= $mostrarIH ? '2' : '1'; ?>" style="font-size:12px; height:25px; font-weight:bold;"><?php echo $resultadoNotArea["ar_nombre"]; ?></td>
                            <?php if($mostrarIH): ?>
                            <td align="center" style="font-weight:bold; font-size:12px;"></td>
                            <?php endif; ?>
                            <td>&nbsp;</td>
                        </tr>
                <?php
                        }
                        // Las materias siempre se muestran, independientemente de si se muestra el área
                        while ($fila2 = mysqli_fetch_array($consultaMat, MYSQLI_BOTH)) {
                            $contador_periodos = 0;
                            mysqli_data_seek($consultaMatPeriodo, 0);
                        ?>
                            <tr bgcolor="#EAEAEA" style="font-size:12px;">
                                <td align="center"><?= $contador; ?></td>
                                <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;"><?php echo $fila2["mat_nombre"]; ?></td>
                                <?php if($mostrarIH): ?>
                                <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?php echo $fila["car_ih"]; ?></td>
                                <?php endif; ?>
                                <td>&nbsp;</td>
                            </tr>
                            <?php
                            if ($mostrarIndicadores && $numIndicadores > 0) {
                                mysqli_data_seek($consultaMatIndicadores, 0);
                                $contadorIndicadores = 0;
                                while ($fila4 = mysqli_fetch_array($consultaMatIndicadores, MYSQLI_BOTH)) {
                                    if ($fila4["mat_id"] == $fila2["mat_id"]) {
                                        // OPTIMIZACIÓN: Obtener recuperación del mapa pre-cargado
                                        $recuperacionIndicador = $recuperacionesMapa[$fila4["ind_id"]][$fila2["car_id"]] ?? null;

                                        $contadorIndicadores++;
                                        $leyendaRI = '';
                                        if(!empty($recuperacionIndicador['rind_nota']) && $recuperacionIndicador['rind_nota']>$fila4["nota"]){
                                            $notaIndicador = (float)$recuperacionIndicador['rind_nota'];
                                            $leyendaRI = '<br><span style="color:navy; font-size:9px;">Recuperdo.</span>';
                                        }else{
                                            $notaIndicador = !empty($fila4["nota"]) ? (float)$fila4["nota"] : 0;
                                        }

                                        $notaIndicador = Boletin::notaDecimales($notaIndicador);

                                        $notaIndicadorFinal=$notaIndicador;
                                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                            // OPTIMIZACIÓN: Usar cache de notas cualitativas
                                            $notaRedondeada = number_format((float)$notaIndicador, $config['conf_decimales_notas'], '.', '');
                                            $notaIndicadorFinal = isset($notasCualitativasCache[$notaRedondeada]) 
                                                ? $notasCualitativasCache[$notaRedondeada] 
                                                : "";
                                        }
                            ?>
                                        <tr bgcolor="#FFF" style="font-size:12px;">
                                            <td align="center">&nbsp;</td>
                                            <td style="font-size:12px; height:15px;"><?php echo $contadorIndicadores . "." . $fila4["ind_nombre"]; ?></td>
                                            <?php if($mostrarIH): ?>
                                            <td>&nbsp;</td>
                                            <?php endif; ?>
                                            <td align="center" style="font-weight:bold; font-size:12px;"><?= $notaIndicadorFinal." ".$leyendaRI; ?></td>
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
                                if (!empty($observacion['bol_observaciones_boletin'])) {
                            ?>
                                <tr>
                                    <td colspan="4">
                                        <h5 align="center">Observaciones</h5>
                                        <p style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">
                                            <?= $observacion['bol_observaciones_boletin']; ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php
                            }    
                            $contador++;
                        } //while fin materias
                        }
                    } //while fin areas
                }
            ?>
        </table>



        <p>&nbsp;</p>

        <?php

        // OPTIMIZACIÓN: Usar prepared statements para consulta de disciplina
        $cndisiplina = null;
        $idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
        $condicionEsc = mysqli_real_escape_string($conexion, $condicion);
        $institucion = (int)$config['conf_id_institucion'];
        $yearEsc = mysqli_real_escape_string($conexion, $year);
        $sqlDisciplina = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='{$idEstudianteEsc}' AND dn_periodo IN ({$condicionEsc}) AND institucion={$institucion} AND year='{$yearEsc}'";
        $cndisiplina = mysqli_query($conexion, $sqlDisciplina);

        if($cndisiplina && mysqli_num_rows($cndisiplina) > 0) {

        ?>

            <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

                <tr style="font-weight:bold; background:#2e537dab; border-color:#036; height:40px; font-size:12px; text-align:center">

                    <td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>

                </tr>

                <tr style="font-weight:bold; background:#b9b91730; height:25px; color:#000; font-size:12px; text-align:center">

                    <td width="8%">Periodo</td>

                    <td>Observaciones</td>

                </tr>

                <?php

                while ($rndisiplina = mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)) {

                ?>

                    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">

                        <td><?= $rndisiplina["dn_periodo"] ?></td>

                        <td align="left"><?= $rndisiplina["dn_observacion"] ?></td>

                    </tr>

                <?php } ?>

            </table>

        <?php } ?>

        <div align="center">

            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="text-align:center; font-size:12px;">

                <tr>

                    <td style="font-weight:bold;" align="left"><?php if(!empty($num_observaciones) && $num_observaciones > 0){ ?>

                            COMPORTAMIENTO:

                        <?php } ?>

                        <b><u>

                                <!-- <?= strtoupper($r_diciplina[3]); ?> -->

                            </u></b><br>

                        <?php

                        ?></td>

                </tr>

            </table>

        </div>

        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>   
        <!--******FIRMAS******-->   

        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
                <td align="center" width="50%">
                    <?php
                        // OPTIMIZACIÓN: Cargar director solo una vez
                        if($directorGrupo === null && !empty($idDirector)){
                            $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                        }
                        if($directorGrupo){
                            $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
                            if(!empty($directorGrupo["uss_firma"])){
                                echo '<img src="../files/fotos/'.$directorGrupo["uss_firma"].'" width="15%"><br>';
                            }else{
                                echo '<p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                            }
                            echo '_________________________________<br>
                                <p>&nbsp;</p>
                                '.$nombreDirectorGrupo.'<br>
                                Director(a) de grupo';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                _________________________________<br>
                                <p>&nbsp;</p>
                                <br>
                                Director(a) de grupo';
                        }
                    ?>
                </td>
                <td align="center" width="50%">
                    <?php
                        // OPTIMIZACIÓN: Cargar rector solo una vez
                        if($rector === null && !empty($informacion_inst["info_rector"])){
                            $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                        }
                        if($rector){
                            $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                            if(!empty($rector["uss_firma"])){
                                echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="25%"><br>';
                            }else{
                                echo '<p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>';
                            }
                            echo '_________________________________<br>
                                <p>&nbsp;</p>
                                '.$nombreRector.'<br>
                                Rector(a)';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                                _________________________________<br>
                                <p>&nbsp;</p>
                                <br>
                                Rector(a)';
                        }
                    ?>
                </td>
            </tr>
        </table>



        </div>



        <div align="center" style="font-size:10px; margin-top:5px; margin-bottom: 10px;">

            <img src="https://plataformasintia.com/images/logo.png" height="50"><br>

            ESTE DOCUMENTO FUE GENERADO POR:<br>

            SINTIA - SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL

        </div>



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