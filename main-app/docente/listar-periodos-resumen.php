<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0039';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php 
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
?>

</head>

<div class="row">
    
    <div class="col-md-12">

        <div class="card card-topline-purple">
            <div class="card-head">
                <header><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                
                <span id="respRP"></span>
                
                <table class="table table-striped custom-table table-hover">
                    <thead>
                        <tr>
                            <th style="text-align:center;">#</th>
                            <th style="text-align:center;">ID</th>
                            <th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>

                            <?php
                                // Precalcular porcentajes de todos los periodos una sola vez
                                $porcentajesPeriodos = [];
                                for ($p = 1; $p <= $datosCargaActual['gra_periodos']; $p++) {
                                    $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $p);
                                    $porcentajeGrado = !empty($periodosCursos['gvp_valor']) ? $periodosCursos['gvp_valor'] : 25;
                                    $porcentajesPeriodos[$p] = $porcentajeGrado;
                                    echo '<th style="text-align:center;">' . $p . 'P<br>(' . $porcentajeGrado . '%)</th>';
                                }
                            ?>
                            <th 
                                style="text-align:center;" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="<?=$frases[117][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[120][$datosUsuarioActual['uss_idioma']];?>"
                            >
                                <?=$frases[117][$datosUsuarioActual['uss_idioma']];?>
                            </th>
                            <th 
                                style="text-align:center;"
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="<b><?=$frases[118][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[121][$datosUsuarioActual['uss_idioma']];?>"
                            >
                                <?=$frases[118][$datosUsuarioActual['uss_idioma']];?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $contReg  = 1;
                        $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);

                        // Pre-cargar tipos de nota para modo cualitativo, si aplica
                        $listaDesemp = [];
                        if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                            $tablaNotasRes = Boletin::listarTipoDeNotas($config["conf_notas_categoria"]);
                            while ($filaDes = mysqli_fetch_array($tablaNotasRes, MYSQLI_BOTH)) {
                                $listaDesemp[] = $filaDes;
                            }
                            mysqli_free_result($tablaNotasRes);
                        }

                        while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                            $colorEstudiante = ($resultado['mat_inclusion'] == 1) ? 'blue;' : '#000;';
                        ?>
                        
                        <tr>
                            <td style="text-align:center;"><?=$contReg;?></td>
                            <td style="text-align:center;"><?=$resultado['mat_id'];?></td>
                            <td style="color: <?=$colorEstudiante;?>"><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>

                            <?php
                                $definitiva              = 0;
                                $sumatoria               = 0;
                                $decimal                 = 0;
                                $sumaPorcentaje          = 0;
                                $iteradorPeriodosConNota = 0;

                                for ($periodoIterado = 1; $periodoIterado <= $datosCargaActual['gra_periodos']; $periodoIterado++) {
                                    // Usar porcentajes precalculados
                                    $porcentajeGrado = $porcentajesPeriodos[$periodoIterado] ?? 25;
                                    $decimal         = $porcentajeGrado / 100;
                                    
                                //LAS CALIFICACIONES
                                $notasResultado = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoIterado, $resultado['mat_id'], $cargaConsultaActual);

                                if (!empty($notasResultado) && !empty($notasResultado['bol_nota'])) {
                                    $iteradorPeriodosConNota++;
                                    $definitiva += $notasResultado['bol_nota'] * $decimal;
                                    $sumaPorcentaje += $decimal;
                                }

                                if(isset($notasResultado) && $notasResultado['bol_nota'] < $config['conf_nota_minima_aprobar'] && $notasResultado['bol_nota']!="")
                                    $color = $config['conf_color_perdida']; 
                                elseif(isset($notasResultado) && $notasResultado['bol_nota'] >= $config['conf_nota_minima_aprobar']) 
                                    $color = $config['conf_color_ganada'];

                                $notasResultadoFinal = null;
                                $notasAnteriorFinal  = null;
                                $atributosA          = '';

                                if (!empty($notasResultado)) {
                                    $notasResultadoFinal = $notasResultado['bol_nota'];
                                    $notasAnteriorFinal  = $notasResultado['bol_nota_anterior'];
                                    $atributosA          = '
                                        style="text-decoration:underline; color:'.$color.';"
                                        title="'.$notasResultado['bol_porcentaje'].'%"
                                    ';

                                    if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                                        $atributosA = '
                                            tabindex="0" 
                                            role="button" 
                                            data-toggle="popover" 
                                            data-trigger="hover" 
                                            title="Nota Cuantitativa: '.$notasResultado['bol_nota'].'" 
                                            data-content="<b>Nota Cuantitativa:</b><br>'.$notasResultado['bol_nota'].' ('.$notasResultado['bol_porcentaje'].'%)" 
                                            data-html="true" 
                                            data-placement="top" 
                                            style="
                                                border-bottom: 1px dotted #000; 
                                                color:'.$color.';
                                            "
                                        ';

                                        if (!empty($listaDesemp)) {
                                            $estiloNota          = Boletin::obtenerDatosTipoDeNotasCargadas($listaDesemp, $notasResultado['bol_nota']);
                                            $notasResultadoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                            $estiloNota          = Boletin::obtenerDatosTipoDeNotasCargadas($listaDesemp, $notasResultado['bol_nota_anterior']);
                                            $notasAnteriorFinal  = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                        }
                                    }
                                }

                                $tipo = null;
                                if (isset($notasResultado) && !empty($notasResultado['bol_nota'])) {
                                    switch ($notasResultado['bol_tipo']) {
                                        case Boletin::BOLETIN_TIPO_NOTA_NORMAL:
                                            $tipo = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>';
                                            break;
                                        case Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_PERIODO:
                                            $tipo = '<span style="color:red; font-size:9px;">Rec. Periodo('.$notasAnteriorFinal.')</span>';
                                            break;
                                        case Boletin::BOLETIN_TIPO_NOTA_RECUPERACION_INDICADOR:
                                            $tipo = '<span style="color:red; font-size:9px;">Rec. Indicador('.$notasAnteriorFinal.')</span>';
                                            break;
                                        case Boletin::BOLETIN_TIPO_NOTA_DIRECTIVA:
                                            $tipo = '<span style="color:red; font-size:9px;">Directiva('.$notasAnteriorFinal.')</span>';
                                            break;
                                        default:
                                            $tipo = null;
                                            break;
                                    }
                                }

                            ?>
                                <td style="text-align:center;">
                                    <a 
                                        href="
                                            calificaciones-estudiante.php?
                                            usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>
                                            &periodo=<?=base64_encode($periodoIterado);?>
                                            &carga=<?=base64_encode($cargaConsultaActual);?>
                                        " 
                                        <?=$atributosA;?>
                                    >
                                        <?php echo Utilidades::setFinalZero($notasResultadoFinal); ?>
                                    </a>
                                    <br><?=$tipo;?><br>

                                    <?php if (
                                                !empty($notasResultado['bol_nota']) && 
                                                $notasResultado['bol_nota'] < $config['conf_nota_minima_aprobar'] && 
                                                $periodoIterado < $datosCargaActual['car_periodo']
                                            ) {
                                    ?>
                                        <input 
                                            size="5" 
                                            name="<?=$periodoIterado?>_<?=$cargaConsultaActual;?>" 
                                            id="<?=$resultado['mat_id'];?>" 
                                            value="" 
                                            alt="<?=$notasResultado['bol_nota'];?>" 
                                            onChange="def(this)" 
                                            tabindex="2" 
                                            style="text-align: center;"
                                        >
                                        <br>
                                        <span style="font-size:9px; color:rgb(0,0,153);">
                                            <?php 
                                            if(!empty($notasResultado['bol_observaciones'])) 
                                                echo $notasResultado['bol_observaciones'];
                                            ?>
                                        </span>
                                    <?php }?>

                                </td>
                            <?php		
                                }
                                //CALCULAR NOTA MINIMA EN EL ULTIMO PERIODO PARA APROBAR LA MATERIA
                                    //PREGUNTAMOS SI ESTAMOS EN EL PERIODO ÚLTIMO
                                    if ($datosCargaActual['car_periodo'] == $datosCargaActual['gra_periodos']) {
                                        $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $periodoIterado);

                                        $porcentajeGrado = 25; // Asumimos que son 4 periodos y valen 25% cada uno.
                                        if (!empty($periodosCursos['gvp_valor'])) {
                                            $porcentajeGrado = $periodosCursos['gvp_valor'];
                                        }

                                        $porcentajeGradoDecimal = ($porcentajeGrado / 100);
                                        $notaMinima             = ($config['conf_nota_minima_aprobar'] - $definitiva);
                                        $notaMinima             = $porcentajeGradoDecimal > 0 ? 
                                                                    round(($notaMinima / $porcentajeGradoDecimal), $config['conf_decimales_notas']) 
                                                                    : 0;

                                        if ($notaMinima <= 0) {
                                            $notaMinima    = "-";
                                            $colorFaltante = "green";
                                        } else {
                                            if ($notaMinima <= $config['conf_nota_hasta']) 
                                                $colorFaltante = "blue"; 
                                            else 
                                                $colorFaltante = "red"; 
                                        }
                                    } else {
                                        $notaMinima    = "-";
                                        $colorFaltante = "black";
                                }

                                if ($sumaPorcentaje > 0) {
                                    $definitiva = ($definitiva / $sumaPorcentaje);
                                }

                                $consultaNivelaciones = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $resultado['mat_id'], $cargaConsultaActual);
                                $numNivelaciones      = mysqli_num_rows($consultaNivelaciones);

                                if ($numNivelaciones == 0) {
                                    if ($iteradorPeriodosConNota > 0)
                                        $definitiva          = round(($definitiva), $config['conf_decimales_notas']);
                                        $tipoDefinitivaAnual = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>';
                                } else {
                                    $datosNivelaciones   = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH);
                                    $definitiva          = $datosNivelaciones['niv_definitiva'];
                                    $tipoDefinitivaAnual = '<span style="color:red; font-size:9px;">'.$frases[124][$datosUsuarioActual['uss_idioma']].'</span>';
                                }

                                if ($definitiva < $config['conf_nota_minima_aprobar'])
                                    $color = $config['conf_color_perdida']; 
                                elseif ($definitiva >= $config['conf_nota_minima_aprobar']) 
                                    $color = $config['conf_color_ganada'];

                                $definitivaFinal = Utilidades::setFinalZero($definitiva);
                                $atributosA      = 'style="text-decoration:underline; color:'.$color.';"';

                                if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                                    $atributosA='tabindex="0" role="button" data-toggle="popover" data-trigger="hover" title="Nota Cuantitativa: '.$definitiva.'" data-content="<b>Nota Cuantitativa:</b><br>'.$definitiva.'" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000; color:'.$color.';"';
            
                                    $estiloNota      = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
                                    $definitivaFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                }
                            ?>

                            <td style="text-align:center; color:<?=$colorFaltante;?>; font-weight:bold;"><?=$notaMinima;?></td>

                            <td style="text-align:center; color:<?=$color;?>;">
                                <?=$definitivaFinal."<br>".$tipoDefinitivaAnual;?><br>

                                <?php
                                if (
                                    $iteradorPeriodosConNota == $datosCargaActual['gra_periodos'] && 
                                    $definitiva < $config['conf_nota_minima_aprobar'] &&
                                    $datosCargaActual['car_periodo'] > $datosCargaActual['gra_periodos']
                                ) 
                                    $habilitarInputNivelaciones = ''; 
                                else 
                                    $habilitarInputNivelaciones = 'disabled';
                                ?>
                                <input 
                                    size="5" 
                                    name="<?=$periodoIterado?>_<?=$cargaConsultaActual;?>" 
                                    id="<?=$resultado['mat_id'];?>" 
                                    value="" 
                                    onChange="niv(this)" 
                                    tabindex="2" 
                                    <?=$habilitarInputNivelaciones;?> 
                                    style="font-size: 13px; text-align: center;"
                                >
                            </td>
                        </tr>
                        <?php
                            $contReg++;
                            }
                            ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../compartido/guardar-historial-acciones.php");?>