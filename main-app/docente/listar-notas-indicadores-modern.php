<?php
include("session.php");
$idPaginaInterna = 'DC0079';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
?>

<div class="notas-table-container">
    <div class="table-responsive">
        <table class="notas-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 300px; text-align: left;"><?= $frases[61][$datosUsuarioActual['uss_idioma']]; ?></th>

                    <?php
                    $cA = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
                    $numIndicadores = mysqli_num_rows($cA);

                    while ($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)) {
                        echo '<th class="indicador-header-cell">
                            ' . $rA['ind_nombre'] . '
                            <small>ID: ' . $rA['ai_ind_id'] . ' (' . $rA['ipc_valor'] . '%)</small>
                        </th>';
                    }
                    ?>

                    <th style="width:80px;">%</th>
                    <th style="width:80px;"><?= $frases[118][$datosUsuarioActual['uss_idioma']]; ?></th>
                </tr>
            </thead>

            <tbody>
                <?php
                $contReg = 1;
                $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);

                while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                    //DEFINITIVAS
                    $carga = $cargaConsultaActual;
                    $periodo = $periodoConsultaActual;
                    $estudiante = $resultado['mat_id'];
                    include("../definitivas.php");

                    $colorEstudiante = '#000';
                    $labelInclusión = '';
                    if ($resultado['mat_inclusion'] == 1) {
                        $colorEstudiante = '#2196f3';
                        $labelInclusión = ' <i class="fa fa-universal-access" title="Estudiante de inclusión" style="color: #2196f3;"></i>';
                    }

                    $fotoEstudiante = '../files/fotos/' . $resultado['uss_foto'];
                    if (empty($resultado['uss_foto']) || !file_exists($fotoEstudiante)) {
                        $fotoEstudiante = '../files/fotos/default.png';
                    }
                ?>

                    <tr>
                        <td style="text-align:center;"><?= $contReg; ?></td>

                        <td>
                            <div class="estudiante-cell">
                                <img src="<?= $fotoEstudiante; ?>" class="estudiante-foto" 
                                     onerror="this.src='../files/fotos/default.png'">
                                <div>
                                    <div class="estudiante-nombre" style="color: <?= $colorEstudiante; ?>;">
                                        <?= Estudiantes::NombreCompletoDelEstudiante($resultado); ?>
                                        <?= $labelInclusión; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <?php
                        $cA = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);

                        while ($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)) {
                            //LAS CALIFICACIONES
                            $sumaNotas = Calificaciones::definitivaIndicadorEstudiante($rA['ipc_indicador'], $cargaConsultaActual, $resultado['mat_id'], $periodoConsultaActual)[0];

                            $notasResultado = $sumaNotas['definitiva'] ?? 0;

                            // Determinar clase de color
                            $claseNota = 'nota-pendiente';
                            if ($notasResultado < $config[5] && $notasResultado != "") {
                                $claseNota = 'nota-reprobado';
                                $colorNota = $config[6];
                            } elseif ($notasResultado >= $config[5]) {
                                $claseNota = 'nota-aprobado';
                                $colorNota = $config[7];
                            } else {
                                $colorNota = "#616161";
                            }

                            $notasResultadoFinal = $notasResultado ?: '-';
                            $atributosA = '';

                            if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                                $atributosA = 'tabindex="0" role="button" data-toggle="popover" data-trigger="hover" 
                                               title="Nota Cuantitativa: '.$notasResultado.' ('.$sumaNotas['valorPorcentual'].'%)" 
                                               data-content="<b>Nota Cuantitativa:</b><br>'.$notasResultado.'" 
                                               data-html="true" data-placement="top"';
        
                                $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado);
                                $notasResultadoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "-";
                            }
                        ?>

                            <td>
                                <a href="calificaciones-estudiante.php?usrEstud=<?= base64_encode($resultado['mat_id_usuario']); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>&carga=<?= base64_encode($cargaConsultaActual); ?>&indicador=<?= base64_encode($rA['ipc_indicador']); ?>" 
                                   class="nota-link <?= $claseNota; ?>" <?= $atributosA; ?>>
                                    <?= $notasResultadoFinal; ?>
                                </a>
                            </td>

                        <?php
                        }

                        // Definitiva final
                        if ($definitiva < $config[5] and $definitiva != "") {
                            $colorDef = $config[6];
                            $claseDefinitiva = 'nota-reprobado';
                        } elseif ($definitiva >= $config[5]) {
                            $colorDef = $config[7];
                            $claseDefinitiva = 'nota-aprobado';
                        } else {
                            $colorDef = "#616161";
                            $claseDefinitiva = 'nota-pendiente';
                        }

                        $definitivaFinal = $definitiva ?: '-';
                        $atributosA = '';

                        if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                            $atributosA = 'tabindex="0" role="button" data-toggle="popover" data-trigger="hover" 
                                           title="Nota Cuantitativa: '.$definitiva.'" 
                                           data-content="<b>Nota Cuantitativa:</b><br>'.$definitiva.'" 
                                           data-html="true" data-placement="top"';

                            $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
                            $definitivaFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "-";
                        }
                        ?>

                        <td><?= $porcentajeActual; ?>%</td>

                        <td class="definitiva-cell">
                            <a href="calificaciones-estudiante.php?usrEstud=<?= base64_encode($resultado['mat_id_usuario']); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>&carga=<?= base64_encode($cargaConsultaActual); ?>" 
                               class="nota-link <?= $claseDefinitiva; ?>" <?= $atributosA; ?>>
                                <?= $definitivaFinal; ?>
                            </a>
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

<?php if ($numIndicadores == 0): ?>
<div class="empty-state" style="margin-top: 20px;">
    <i class="fa fa-info-circle"></i>
    <h4>No hay indicadores registrados</h4>
    <p>Primero debes agregar indicadores en la pestaña "Indicadores" para poder visualizar las notas.</p>
    <a href="javascript:void(0);" onclick="switchTab('indicadores')" class="btn-primary-modern">
        <i class="fa fa-arrow-left"></i>
        Ir a Indicadores
    </a>
</div>
<?php endif; ?>

<script>
// Inicializar popovers
$(document).ready(function() {
    $('[data-toggle="popover"]').popover({
        html: true,
        trigger: 'hover'
    });
});

console.log('✨ Notas por indicador cargadas correctamente');
console.log('📊 Total de estudiantes:', <?= $contReg - 1; ?>);
console.log('📈 Total de indicadores:', <?= $numIndicadores; ?>);
</script>

<?php include("../compartido/guardar-historial-acciones.php");?>
