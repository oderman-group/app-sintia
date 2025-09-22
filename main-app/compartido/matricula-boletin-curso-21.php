<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Calificaciones.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Ausencias.php");

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
    $year = base64_decode($_GET["year"]);
}

$modulo = 1;
$periodoFinal=false;
if (empty($_GET["periodo"])) {
    $periodoActual = 1;
} else {
    $periodoActual = base64_decode($_GET["periodo"]);
}
$periodoFinal= ($periodoActual == $config['conf_periodos_maximos']);

if ($periodoActual == 1)
    $periodoActuales = "Primero";
if ($periodoActual == 2)
    $periodoActuales = "Segundo";
if ($periodoActual == 3)
    $periodoActuales = "Tercero";
if ($periodoActual == 4)
    $periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if (!empty($_GET["curso"])) {
    $curso = base64_decode($_GET["curso"]);
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
    $grupo = base64_decode($_GET["grupo"]);
}

$idEstudiante = '';
if (!empty($_GET["id"])) {

    $filtro = " AND mat_id='" . base64_decode($_GET["id"]) . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    Utilidades::validarInfoBoletin($matriculadosPorCurso);
    $estudiante = $matriculadosPorCurso->fetch_assoc();
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $curso = $estudiante["mat_grado"];
        $grupo = $estudiante["mat_grupo"];
    } else {
        echo "Excepción catpurada: Estudiante no encontrado ";
        exit();
    }
}

$consultaPuestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual, $curso, $grupo, $year);
$puestosCurso = [];
foreach ($consultaPuestos as $puesto) {
    $puestosCurso[$puesto['estudiante_id']] = $puesto['puesto'];
}

$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
    $tiposNotas[] = $row;
}

$listaCargas = [];
$conCargasDos = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo, $year);
while ($row = $conCargasDos->fetch_assoc()) {

    $indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $row['car_id'], $periodoActual, $year);
    $listaIndicadores = [];
    while ($row2 = $indicadores->fetch_assoc()) {
        $listaIndicadores[] = $row2;
    }
    $row['indicadores'] = $listaIndicadores;
    $listaCargas[] = $row;
}

$listaDatos = [];
if (!empty($curso) && !empty($grupo) && !empty($year)) {
    $periodos = [];
    for ($i = 1; $i <= $periodoActual; $i++) {
        $periodos[$i] = $i;
    }
    $datos = Boletin::datosBoletin($curso, $grupo, $periodos, $year, $idEstudiante, false);
    while ($row = $datos->fetch_assoc()) {
        $listaDatos[] = $row;
    }
    include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
}


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
            foreach ($estudiantes as $estudiante) {
                $materiasPerdidas = 0;
                if ($config['conf_id_institucion'] == ELLEN_KEY) {
                    ?>
                    <img src="../files/images/logo/encabezadoellen.png" width="95%">
                <?php } else { ?>
                    <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="150" width="200"><br>
                <?php } ?>
            </div>

            <div>
                <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 14px;">
                    <tr>
                        <td>Estudiante: <b><?= $estudiante["nombre"] ?></b></td>
                        <td>Grado: <b><?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></b></td>
                        <td>Periodo/Año: <b><?= $periodoActual . " / " . $year . ""; ?></b></td>
                    </tr>
                </table>
            </div>
        </div>

        <table width="100%" rules="all" border="1">
            <thead>
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #00adefad; font-size: 13px;">INFORME
                        PERIÓDICO DEL PROCESO DE DESARROLLO EDUCATIVO</td>
                </tr>
            </thead>
        </table>

        <table width="100%" rules="all" border="1">
            <thead>

                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="2%" rowspan="2">I.H.</td>

                    <?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
                        <td width="3%" colspan="2"><a href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $estudiante['mat_id']; ?>&periodo=<?= $j ?>" style="color:#000; text-decoration:none;">Periodo <?= $j ?></a></td>
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
         
            <tbody>
                <?php
                $contador = 1;
                $promedios = [];
                $ausencias = [];
                foreach ($estudiante["areas"] as $area) {

                    ?>
                    <?php foreach ($area["cargas"] as $carga) {
                        
                        if ($contador % 2 == 1) {
                            $fondoFila = '#EAEAEA';
                        } else {
                            $fondoFila = '#FFF';
                        }
                        ?>
                        <tr style="background:<?= $fondoFila; ?>">
                            <td><?= $carga['mat_nombre']; ?></td>
                            <td align="center"><?= $carga['car_ih']; ?></td>
                            <?php for ($j = 1; $j <= $periodoActual; $j++) {
                                Utilidades::valordefecto($carga["periodos"][$j]["bol_tipo"],'1');
                                Utilidades::valordefecto($promedios[$j], 0);
                                Utilidades::valordefecto($ausencias[$j], 0);
                                Utilidades::valordefecto($carga["periodos"][$j]["bol_nota"], 0);
                                $recupero = $carga["periodos"][$j]['bol_tipo'] == '2';
                                
                                if($carga["director_grupo"]==1){
                                    $directorGrupo=$carga["docente"];
                                }
                                
                                $nota = $carga["periodos"][$j]["bol_nota"];
                                ?>
                                <td align="center">                                    
                                   
                                    <?= $recupero ? $carga['periodos'][$j]['bol_nota_anterior'] . " /  <span style='font-weight: bold;'> " . Boletin::formatoNota($nota, $tiposNotas) . " Recu. </span>" :  Boletin::formatoNota($nota, $tiposNotas)?>
                                   
                                </td>
                                <td align="center"><img
                                        src="../files/iconos/<?= Boletin::determinarRango($nota, $tiposNotas)['notip_imagen']; ?>"
                                        width="15" height="15"></td>
                                <?php
                                $promedios[$j] += $nota;
                                if ($nota < $config['conf_nota_minima_aprobar']) {
                                    $materiasPerdidas++;
                                }
                            }

                            $notaFinal =$periodoFinal? $carga["nota_carga_acumulada"]: $carga["suma_nota_carga"]/$periodoActual;
                            $notaFinal = round($notaFinal, $config['conf_decimales_notas']);
						    $notaFinal = number_format($notaFinal, $config['conf_decimales_notas']);
                            Utilidades::valordefecto($promedios[$j], 0);
                            Utilidades::valordefecto($ausencias[$j], 0);
                            $promedios[$j] += $notaFinal;
                            $ausencias[$j] += $carga["fallas"];
                            ?>
                            <td align="center"><?= Boletin::formatoNota($notaFinal, $tiposNotas); ?></td>
                            <td align="center"><img
                                    src="../files/iconos/<?= Boletin::determinarRango($notaFinal, $tiposNotas)['notip_imagen']; ?>"
                                    width="15" height="15"></td>
                            <td align="center">&nbsp;</td>
                        </tr>
                        <?php
                        $contador++;
                    } ?>

                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="font-weight:bold; text-align:center;">
                    <td style="text-align:left;">PROMEDIO GENERAL</td>
                    <td>&nbsp;</td>
                    <?php
                        $promedioFinal     = 0; 
                        $porcentajePeriodo = 0;
                        for ($k = 1; $k <= $periodoActual; $k++) {
                            if ( !empty($estudiante["promedios_generales"][$k]["suma_notas_materias"]) ) {
                                $notaPromedio      = $estudiante["promedios_generales"][$k]["suma_notas_materias"] ;
                                $cantidadMaterias  = $estudiante["promedios_generales"][$k]["cantidad_materias"] ;
                                $porcentajePeriodo = $estudiante["promedios_generales"][$k]["porcentaje_periodo"] ;
                                $promedio = round(( $notaPromedio/ ( $cantidadMaterias)), $config['conf_decimales_notas']);
                            }else{
                                $notaPromedio      = 0;
                                $porcentajePeriodo = 0;
                                $promedio          = 0;
                            }

						
						$promedio = number_format($promedio, $config['conf_decimales_notas']);
                        
                        $promedioFinal +=  $periodoFinal? $promedio * ($porcentajePeriodo/100): $promedio/$periodoActual;
						?>
                        <td align="center" style="font-weight:bold;  font-size:14px;" ><?= Boletin::formatoNota($promedio, $tiposNotas); ?></td>
                        <td align="center"><img
                                src="../files/iconos/<?= Boletin::determinarRango($promedio, $tiposNotas)['notip_imagen']; ?>"
                                width="15" height="15"></td>
					<?php } ?>
                    <td align="center" style="font-weight:bold;  font-size:14px;" ><?= Boletin::formatoNota($promedioFinal, $tiposNotas); ?></td>
                        <td align="center"><img
                                src="../files/iconos/<?= Boletin::determinarRango($promedioFinal, $tiposNotas)['notip_imagen']; ?>"
                                width="15" height="15"></td>
                </tr>
                <tr style="font-weight:bold; text-align:center;">
                    <td style="text-align:left;">AUSENCIAS</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php foreach ($ausencias as $ausencia) {

                        ?>
                        <td align="center"><?= $ausencia ?> Aus.</td>
                        <td>&nbsp;</td>
                    <?php } ?>
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

                <?php foreach ($estudiante["observaciones_generales"] as $observacion) {?>

                    <tr>
                        <td style="text-align: center;"><?=$observacion["periodo"];?></td>
                        <td><?=$observacion["observacion"];?></td>
                    </tr>

                <?php }?>

            </thead>
        </table>



        <p>&nbsp;</p>

        <table width="100%" cellspacing="2" cellpadding="2" rules="all" border="1">
            <?php
                $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
            ?>
            <thead>
                <tr>
                    <td style="width: 40%;">Dir. Curso: <?=$nombreDirectorGrupo?></td>
                    
                    <?php foreach ($tiposNotas as $tipoNota) {?>
                    <td style="width: 15%;"><img src="../files/iconos/<?=$tipoNota["notip_imagen"]?>" width="10"><?= $tipoNota["notip_nombre"] ?>  <?= $tipoNota["notip_desde"] ?> – <?= $tipoNota["notip_hasta"] ?></td>
                    <?php }?>
                </tr>

                <tr style="height: 70px;">
                    <td style="text-align: center;">
                    <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
                    Firmas y Sellos Autorizados
                    </td>
                    <td colspan="<?=count($tiposNotas)?>">

                        “El hogar es la primera escuela del niño. Practíquese en la casa la temperancia en todas las cosas, y apóyese al maestro que está tratando de brindar a sus hijos una verdadera educación” CDD (EGW)
                    </td>
                </tr>


            </thead>
        </table>

        <p>&nbsp;</p>

        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            foreach ($listaCargas as $carga) { ?>

                
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $carga['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($carga); ?></span></td>
                        <td>
                        
                            <?php
                            foreach ($carga["indicadores"] as $indicador) { ?>
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
                    <td style="text-align: center; font-weight: bold; font-size: medium;">
                        <?= !empty($informacion_inst["info_direccion"]) ? strtoupper($informacion_inst["info_direccion"]) : ""; ?>
                        <?= !empty($informacion_inst["info_telefono"]) ? "TELEFONO " . $informacion_inst["info_telefono"] : ""; ?>
                    </td>
                </tr>
            </thead>
        </table>
        <div id="saltoPagina"></div>
        <?php
            } // FIN DE TODOS LOS MATRICULADOS
            include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
            ?>


    <script type="application/javascript">
        print();
    </script>


</body>

</html>