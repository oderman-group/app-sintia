<?php
include("session.php");
require_once("../class/CargaAcademica.php");
require_once("../class/Boletin.php");

Utilidades::validarParametros($_GET);

if (!empty($_GET["idEstudiante"])) {
    $idEstudiante = base64_decode($_GET["idEstudiante"]);

    // Obtener datos del estudiante
    $datosEstudiante = Estudiantes::obtenerDatosEstudiante($idEstudiante);

    if (!empty($datosEstudiante)) {
        $curso = $datosEstudiante['mat_grado'];
        $grupo = $datosEstudiante['mat_grupo'];

        // Obtener las cargas académicas del estudiante
        $cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo);

        if ($cargas && mysqli_num_rows($cargas) > 0) {
            ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th></th>
                            <th>Código</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th>Intensidad Horaria</th>
                            <th>Período</th>
                            <th>Valor (%)</th>
                            <th>Definitiva Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
                            // Obtener la definitiva actual del estudiante en esta carga
                            $definitiva = Boletin::obtenerPromedioPorTodasLasCargas($idEstudiante, $carga['car_id']);
                            $definitivaFormateada = !empty($definitiva['def']) ? number_format($definitiva['def'], 2) : 'N/A';

                            // Obtener historial de notas por periodos
                            $historialNotas = [];
                            for ($periodo = 1; $periodo <= $config['conf_periodos_maximos']; $periodo++) {
                                $notaPeriodo = Boletin::obtenerNotasBoletin($config, $periodo, $idEstudiante, $carga['car_id']);
                                if (!empty($notaPeriodo['bol_nota'])) {
                                    $historialNotas[] = [
                                        'periodo' => $periodo,
                                        'nota' => number_format($notaPeriodo['bol_nota'], 2)
                                    ];
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($historialNotas)): ?>
                                        <button class="btn btn-sm btn-info toggle-details" data-carga="<?php echo $carga['car_id']; ?>">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $carga['car_id']; ?></td>
                                <td><?php echo "[" . $carga['mat_id'] . "] " . strtoupper($carga['mat_nombre']); ?></td>
                                <td><?php echo strtoupper($carga['uss_nombre'] . " " . $carga['uss_apellido1']); ?></td>
                                <td><?php echo $carga['car_ih']; ?></td>
                                <td><?php echo $carga['car_periodo']; ?></td>
                                <td><?php echo $carga['mat_valor'] . "%"; ?></td>
                                <td><?php echo $definitivaFormateada; ?></td>
                            </tr>
                            <?php if (!empty($historialNotas)): ?>
                            <tr class="details-row" id="details-<?php echo $carga['car_id']; ?>" style="display: none;">
                                <td colspan="8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Historial de Notas - <?php echo "[" . $carga['mat_id'] . "] " . strtoupper($carga['mat_nombre']); ?></h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Período</th>
                                                            <th>Nota</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($historialNotas as $nota): ?>
                                                        <tr>
                                                            <td><?php echo $nota['periodo']; ?></td>
                                                            <td><?php echo $nota['nota']; ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <script>
            $(document).ready(function() {
                $('.toggle-details').on('click', function() {
                    var cargaId = $(this).data('carga');
                    var detailsRow = $('#details-' + cargaId);
                    var icon = $(this).find('i');

                    if (detailsRow.is(':visible')) {
                        detailsRow.hide();
                        icon.removeClass('fa-minus').addClass('fa-plus');
                    } else {
                        detailsRow.show();
                        icon.removeClass('fa-plus').addClass('fa-minus');
                    }
                });
            });
            </script>
            <?php
        } else {
            echo '<div class="alert alert-info">No se encontraron materias asignadas para este estudiante.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">No se encontraron datos del estudiante.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Parámetros inválidos.</div>';
}
?>