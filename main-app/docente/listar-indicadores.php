<?php
include("session.php");
$idPaginaInterna = 'DC0034';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("../compartido/head.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");

$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
?>
</head>
<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css" />
<div id="gifCarga" class="gif-carga">
    <img alt="Cargando...">
</div>
<div class="card card-topline-purple" id="idElemento" name="elementoGlobalBloquear">
    <div class="card-head">
        <header><?= $frases[63][$datosUsuarioActual['uss_idioma']]; ?></header>
        <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-6">
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
                    <div class="btn-group" id="agregarNuevo">
                        <a href="indicadores-agregar.php?carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>" id="addRow" class="btn deepPink-bgcolor">
                            Agregar nuevo<i class="fa fa-plus"></i>
                        </a>
                        <?php if (Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_AI_INDICADORES)) {?>
                        <a class="dropdown-toggle btn deepPink-bgcolor" title="Genera los indicadores con inteligencia artificial teniendo en cuenta la asginatura y el curso actual" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            IA <i class="fa fa-list"></i>
                        </a>


                        <div class="dropdown-menu panel" aria-labelledby="navbarDropdown">
                            <header class="panel-heading panel-heading-yellow">Crear indicadores para:</header>

                            <div class="panel-body">
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <b><?= strtoupper($frases[116][$datosUsuarioActual['uss_idioma']]); ?></b>
                                        <div class="profile-desc-item pull-right"><?= strtoupper($datosCargaActual['mat_nombre']); ?></div>
                                        <input type="text" id="asignatura" hidden name="asignatura" class="form-control text-center" value="<?= $datosCargaActual['mat_nombre'] ?>">
                                    </li>
                                    <li class="list-group-item">
                                        <b><?= strtoupper($frases[26][$datosUsuarioActual['uss_idioma']]); ?></b>
                                        <div class="profile-desc-item pull-right"><?= strtoupper($datosCargaActual['gra_nombre'] . " " . $datosCargaActual['gru_nombre']); ?></div>
                                        <input type="text" id="curso" hidden name="curso" class="form-control text-center" value="<?= $datosCargaActual['gra_nombre'] ?>">
                                    </li>
                                    <li class="list-group-item">
                                        <b>CANTIDAD:</b>
                                        <div class="profile-desc-item pull-right">
                                            <input type="number" id="maxidicadores" name="maxidicadores" class="form-control text-center" value="1" min="1">
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <p align="center"><a href="javascript:;" onclick="generarIndicadores()" class="btn yellow">Generar indicadores</a></p>

                        </div>
                        <?php }?>
                    </div>
                <?php } ?>

                <?php if ($datosCargaActual['car_valor_indicador'] == 1 and $porcentajeRestante <= 0) { ?>
                    <p style="color: tomato;"> Has alcanzado el 100% de valor para los indicadores. </p>
                <?php } ?>

                <?php if ($datosCargaActual['car_maximos_indicadores'] <= $sumaIndicadores[2]) { ?>
                    <p style="color: tomato;"> Has alcanzado el número máximo de indicadores permitidos. </p>
                <?php } ?>

                <div class="btn-group">
                    <a href="../compartido/indicadores-perdidos-curso.php?curso=<?= base64_encode($datosCargaActual['car_curso']); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>" class="btn btn-secondary" target="_blank">
                        Ver indicadores perdidos<i class="fa fa-file-text-o"></i>
                    </a>
                </div>
                <div class="btn-group" style="width: 300px;" id="AgregarIndicadores">

                </div>
            </div>


        </div>
        <div class="table-scrollable">
            <table class="table table-striped table-bordered table-hover table-checkable order-column valign-middle" id="example4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= $frases[49][$datosUsuarioActual['uss_idioma']]; ?></th>
                        <th><?= $frases[50][$datosUsuarioActual['uss_idioma']]; ?></th>
                        <th><?= $frases[52][$datosUsuarioActual['uss_idioma']]; ?></th>

                        <?php if ($datosCargaActual['car_saberes_indicador'] == 1) { ?>
                            <th>Tipo evaluación</th>
                        <?php } ?>

                        <th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
                    </tr>
                </thead>
                <tbody id="contenido-dinamico">
                    <?php include 'listar-indicadores-tbody.php' ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight:bold;">
                        <td colspan="3"><?= strtoupper($frases[107][$datosUsuarioActual['uss_idioma']]); ?></td>
                        <td id="porcentajeActual"><?= $porcentajeActual; ?>%</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include("../compartido/guardar-historial-acciones.php"); ?>