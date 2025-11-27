<?php
if (empty($_SESSION["id"])) {
    include("session.php");
    include("verificar-carga.php");
}
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");

$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajePermitido = 100 - ($sumaIndicadores[0] ?? 0);
$porcentajeRestante = ($porcentajePermitido - ($sumaIndicadores[1] ?? 0));
$porcentajeActual = $sumaIndicadores[1] ?? 0;
$totalIndicadores = $totalIndicadores ?? 0;

$saberes = array("", "Saber saber (55%)", "Saber hacer (35%)", "Saber ser (10%)");
?>

<!-- Barra de Acciones -->
<div class="actions-bar">
    <div style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
        <?php
        if (
            (
                ($datosCargaActual['car_valor_indicador'] == Indicadores::CONFIG_AUTOMATICO_INDICADOR
                    && $totalIndicadores < $datosCargaActual['car_maximos_indicadores']
                )
                ||
                ($datosCargaActual['car_valor_indicador'] == Indicadores::CONFIG_MANUAL_INDICADOR
                    && $totalIndicadores < $datosCargaActual['car_maximos_indicadores']
                    && $porcentajeRestante > 0)
            )
            && CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual)
        ) {
        ?>
            <button onclick="abrirModalAgregar()" class="btn-primary-modern">
                <i class="fa fa-plus"></i>
                Agregar Indicador
            </button>

            <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_AI_INDICADORES)) {?>
            <div class="dropdown" style="display: inline-block;">
                <button class="btn-primary-modern dropdown-toggle" type="button" id="dropdownAI" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        title="Generar indicadores con Inteligencia Artificial">
                    <i class="fa fa-robot"></i>
                    IA Generar
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownAI" style="padding: 20px; min-width: 300px;">
                    <h6 style="font-weight: 700; margin-bottom: 15px; color: var(--primary-color);">
                        <i class="fa fa-robot mr-2"></i>Generar con IA
                    </h6>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 12px; font-weight: 600; color: #7f8c8d;">ASIGNATURA</label>
                        <input type="text" id="asignatura" class="form-control" 
                               value="<?php echo $datosCargaActual['mat_nombre']; ?>" readonly 
                               style="background: #f8f9fa;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 12px; font-weight: 600; color: #7f8c8d;">CURSO</label>
                        <input type="text" id="curso" class="form-control" 
                               value="<?php echo $datosCargaActual['gra_nombre']; ?>" readonly 
                               style="background: #f8f9fa;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 12px; font-weight: 600; color: #7f8c8d;">CANTIDAD</label>
                        <input type="number" id="maxidicadores" class="form-control" 
                               value="1" min="1" max="7">
                    </div>
                    <button onclick="generarIndicadores()" class="btn-primary-modern" style="width: 100%;">
                        <i class="fa fa-magic"></i>
                        Generar Indicadores
                    </button>
                </div>
            </div>
            <?php }?>
        <?php } ?>
    </div>
</div>

<!-- Alertas y Botón Ver Perdidos -->
<?php if ($datosCargaActual['car_valor_indicador'] == 1 and $porcentajeRestante <= 0) { ?>
    <div class="alert-modern alert-warning-modern" style="background: #ffebee; color: #c62828; border-left-color: #e74c3c;">
        <i class="fa fa-check-circle"></i>
        <span style="font-weight: 600;">Has alcanzado el 100% de valor para los indicadores.</span>
    </div>
    <div style="margin-bottom: 20px;">
        <a href="../compartido/indicadores-perdidos-curso.php?curso=<?php echo base64_encode($datosCargaActual['car_curso']); ?>&periodo=<?php echo base64_encode($periodoConsultaActual); ?>" 
           class="btn-secondary-modern" target="_blank" style="background: #34495e; color: white; border-color: #34495e;">
            <i class="fa fa-file-text-o"></i>
            Ver indicadores perdidos
        </a>
    </div>
<?php } ?>

<?php if ($datosCargaActual['car_maximos_indicadores'] <= $totalIndicadores) { ?>
    <div class="alert-modern alert-warning-modern">
        <i class="fa fa-exclamation-triangle"></i>
        <span>Has alcanzado el número máximo de indicadores permitidos.</span>
    </div>
<?php } ?>

<?php
$consulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$numIndicadores = mysqli_num_rows($consulta);

if ($numIndicadores > 0) {
    mysqli_data_seek($consulta, 0); // Reset pointer
    $contReg = 1;
    $sumaPorcentajes = 0;
?>
    <!-- Tabla de Indicadores -->
    <div class="indicadores-table-container" id="indicadores-table-container">
        <div class="table-responsive">
            <table class="indicadores-table-modern">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 100px;">Cod</th>
                        <th>Descripción</th>
                        <th style="width: 120px;">Valor</th>
                        <th style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                        $sumaPorcentajes += $resultado['ipc_valor'];
                    ?>
                        <tr id="indicador-row-<?php echo $resultado['ipc_id']; ?>" data-id="<?php echo $resultado['ipc_id']; ?>">
                            <td style="text-align: center; font-weight: 600;"><?php echo $contReg; ?></td>
                            <td style="text-align: center;"><?php echo $resultado['aipc_id_nuevo']; ?></td>
                            <td><?php echo htmlspecialchars($resultado['ind_nombre']); ?></td>
                            <td style="text-align: center; font-weight: 600; color: var(--secondary-color);">
                                <?php echo number_format($resultado['ipc_valor'], 2); ?>%
                            </td>
                            <td style="text-align: center;">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        Acciones <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php if ($resultado['ipc_creado'] == 1 and ($periodoConsultaActual == $datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2'] == 1)) { ?>
                                            <li>
                                                <a href="javascript:void(0);" onclick="abrirModalEditar('<?php echo $resultado['ipc_id']; ?>')">
                                                    <i class="fa fa-edit"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" onclick="eliminarIndicador('<?php echo $resultado['ipc_id']; ?>', '<?php echo $resultado['ipc_indicador']; ?>')">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                </a>
                                            </li>
                                        <?php } ?>
                                        
                                        <?php if ($periodoConsultaActual < $datosCargaActual['car_periodo']) { ?>
                                            <li>
                                                <a href="indicadores-recuperar.php?idR=<?php echo base64_encode($resultado['ipc_indicador']); ?>">
                                                    <i class="fa fa-redo"></i> Recuperar
                                                </a>
                                            </li>
                                        <?php } ?>
                                        
                                        <li>
                                            <a href="indicadores-estudiantes-inclusion.php?idIndicadorNuevo=<?php echo base64_encode($resultado['aipc_id_nuevo']); ?>&idIndicador=<?php echo base64_encode($resultado['ipc_id']); ?>">
                                                <i class="fa fa-users"></i> E. Inclusión
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php
                        $contReg++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: 700; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <td colspan="3" style="text-align: center;">TOTAL</td>
                        <td style="text-align: center; color: var(--primary-color);"><?php echo number_format($sumaPorcentajes, 2); ?>%</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Estadísticas de Porcentaje (Cards) - Después de la tabla -->
    <div class="porcentaje-stats" style="margin-top: 30px;">
        <div class="porcentaje-card">
            <div class="porcentaje-label">Porcentaje Usado</div>
            <div class="porcentaje-value"><?php echo number_format($porcentajeActual, 1); ?>%</div>
            <div class="porcentaje-progress">
                <div class="porcentaje-progress-bar" style="width: <?php echo $porcentajeActual; ?>%;"></div>
            </div>
        </div>
        <div class="porcentaje-card">
            <div class="porcentaje-label">Porcentaje Disponible</div>
            <div class="porcentaje-value" style="color: #27ae60;"><?php echo number_format($porcentajeRestante, 1); ?>%</div>
            <div class="porcentaje-progress">
                <div class="porcentaje-progress-bar" style="width: <?php echo $porcentajeRestante; ?>%; background: #27ae60;"></div>
            </div>
        </div>
        <div class="porcentaje-card">
            <div class="porcentaje-label">Total Indicadores</div>
            <div class="porcentaje-value" style="color: #3498db;"><?php echo $totalIndicadores; ?></div>
            <div style="font-size: 11px; color: #7f8c8d; margin-top: 5px;">
                Máx: <?php echo $datosCargaActual['car_maximos_indicadores']; ?>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <!-- Empty State -->
    <div class="empty-state">
        <i class="fa fa-clipboard-list"></i>
        <h4>No hay indicadores registrados</h4>
        <p>Comienza agregando tu primer indicador para esta carga académica.</p>
        <?php
        if (
            (
                ($datosCargaActual['car_valor_indicador'] == Indicadores::CONFIG_AUTOMATICO_INDICADOR
                    && $totalIndicadores < $datosCargaActual['car_maximos_indicadores']
                )
                ||
                ($datosCargaActual['car_valor_indicador'] == Indicadores::CONFIG_MANUAL_INDICADOR
                    && $totalIndicadores < $datosCargaActual['car_maximos_indicadores']
                    && $porcentajeRestante > 0)
            )
            && CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual)
        ) {
        ?>
            <button onclick="abrirModalAgregar()" class="btn-primary-modern">
                <i class="fa fa-plus"></i>
                Agregar Primer Indicador
            </button>
        <?php } ?>
    </div>
    
    <!-- Estadísticas de Porcentaje (Cards) - También cuando no hay indicadores -->
    <div class="porcentaje-stats" style="margin-top: 30px;">
        <div class="porcentaje-card">
            <div class="porcentaje-label">Porcentaje Usado</div>
            <div class="porcentaje-value"><?php echo number_format($porcentajeActual, 1); ?>%</div>
            <div class="porcentaje-progress">
                <div class="porcentaje-progress-bar" style="width: <?php echo $porcentajeActual; ?>%;"></div>
            </div>
        </div>
        <div class="porcentaje-card">
            <div class="porcentaje-label">Porcentaje Disponible</div>
            <div class="porcentaje-value" style="color: #27ae60;"><?php echo number_format($porcentajeRestante, 1); ?>%</div>
            <div class="porcentaje-progress">
                <div class="porcentaje-progress-bar" style="width: <?php echo $porcentajeRestante; ?>%; background: #27ae60;"></div>
            </div>
        </div>
        <div class="porcentaje-card">
            <div class="porcentaje-label">Total Indicadores</div>
            <div class="porcentaje-value" style="color: #3498db;"><?php echo $totalIndicadores; ?></div>
            <div style="font-size: 11px; color: #7f8c8d; margin-top: 5px;">
                Máx: <?php echo $datosCargaActual['car_maximos_indicadores']; ?>
            </div>
        </div>
    </div>
<?php
}
?>

<?php include("../compartido/guardar-historial-acciones.php"); ?>