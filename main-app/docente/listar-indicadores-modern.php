<?php
include("session.php");
$idPaginaInterna = 'DC0034';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");

$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
$porcentajeActual = $sumaIndicadores[1];

$saberes = array("", "Saber saber (55%)", "Saber hacer (35%)", "Saber ser (10%)");
?>

<!-- Estadísticas de Porcentaje -->
<div class="porcentaje-stats">
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
        <div class="porcentaje-value" style="color: #3498db;"><?php echo $sumaIndicadores[2]; ?></div>
        <div style="font-size: 11px; color: #7f8c8d; margin-top: 5px;">
            Máx: <?php echo $datosCargaActual['car_maximos_indicadores']; ?>
        </div>
    </div>
</div>

<!-- Barra de Acciones -->
<div class="actions-bar">
    <div style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
        <?php
        if (
            (
                ($datosCargaActual['car_valor_indicador'] == Indicadores::CONFIG_AUTOMATICO_INDICADOR
                    && $sumaIndicadores[2] < $datosCargaActual['car_maximos_indicadores']
                )
                ||
                ($datosCargaActual['car_valor_indicador'] == Indicadores::CONFIG_MANUAL_INDICADOR
                    && $sumaIndicadores[2] < $datosCargaActual['car_maximos_indicadores']
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

    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="../compartido/indicadores-perdidos-curso.php?curso=<?php echo base64_encode($datosCargaActual['car_curso']); ?>&periodo=<?php echo base64_encode($periodoConsultaActual); ?>" 
           class="btn-secondary-modern" target="_blank">
            <i class="fa fa-file-text-o"></i>
            Ver Perdidos
        </a>
    </div>
</div>

<!-- Alertas -->
<?php if ($datosCargaActual['car_valor_indicador'] == 1 and $porcentajeRestante <= 0) { ?>
    <div class="alert-modern alert-warning-modern">
        <i class="fa fa-exclamation-triangle"></i>
        <span>Has alcanzado el 100% de valor para los indicadores.</span>
    </div>
<?php } ?>

<?php if ($datosCargaActual['car_maximos_indicadores'] <= $sumaIndicadores[2]) { ?>
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
?>
    <!-- Grid de Indicadores -->
    <div class="indicadores-grid" id="indicadores-grid">
        <?php
        $contReg = 1;
        while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        ?>
            <div class="indicador-card" id="indicador-<?php echo $resultado['ipc_id']; ?>" data-id="<?php echo $resultado['ipc_id']; ?>">
                <div class="indicador-header">
                    <div class="indicador-numero"><?php echo $contReg; ?></div>
                    <div class="indicador-actions">
                        <?php if ($resultado['ipc_creado'] == 1 and ($periodoConsultaActual == $datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2'] == 1)) { ?>
                            <button onclick="abrirModalEditar('<?php echo $resultado['ipc_id']; ?>')" 
                                    class="btn-action btn-edit" title="Editar indicador">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button onclick="eliminarIndicador('<?php echo $resultado['ipc_id']; ?>', '<?php echo $resultado['ipc_indicador']; ?>')" 
                                    class="btn-action btn-delete" title="Eliminar indicador">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php } ?>
                        
                        <?php if ($periodoConsultaActual < $datosCargaActual['car_periodo']) { ?>
                            <a href="indicadores-recuperar.php?idR=<?php echo base64_encode($resultado['ipc_indicador']); ?>" 
                               class="btn-action" style="background: #fff3cd; color: #856404;" title="Recuperar indicador">
                                <i class="fa fa-redo"></i>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                
                <div class="indicador-nombre" title="<?php echo $resultado['ind_nombre']; ?>">
                    <?php echo $resultado['ind_nombre']; ?>
                </div>
                
                <div class="indicador-footer">
                    <div class="indicador-valor">
                        <i class="fa fa-percent"></i>
                        <?php echo $resultado['ipc_valor']; ?>%
                    </div>
                    
                    <?php if ($datosCargaActual['car_saberes_indicador'] == 1) { ?>
                        <div class="indicador-tipo">
                            <?php echo $saberes[$resultado['ipc_evaluacion']]; ?>
                        </div>
                    <?php } else { ?>
                        <div class="indicador-tipo">
                            ID: <?php echo $resultado['aipc_id_nuevo']; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php
            $contReg++;
        }
        ?>
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
                    && $sumaIndicadores[2] < $datosCargaActual['car_maximos_indicadores']
                )
                ||
                ($datosCargaActual['car_valor_indicador'] == Indicadores::CONFIG_MANUAL_INDICADOR
                    && $sumaIndicadores[2] < $datosCargaActual['car_maximos_indicadores']
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
<?php
}
?>

<?php include("../compartido/guardar-historial-acciones.php"); ?>