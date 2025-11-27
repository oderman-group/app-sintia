<?php
include("session.php");
$idPaginaInterna = 'DT0332';
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/MailQueue.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title">CONSUMO DEL PLAN</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row clearfix">
                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card">
                                    <div class="card-head">
                                        <header>USUARIOS</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:location.reload();"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $datosPlan = Plataforma::traerDatosPlanes($conexion, $datosUnicosInstitucion['ins_id_plan']);
                                        $datosPaquetes = Plataforma::contarDatosPaquetes($datosUnicosInstitucion['ins_id'], PAQUETES);
                                        $totalDirectivos = $datosPlan['plns_cant_directivos'] + $datosPaquetes['plns_cant_directivos'];
                                        $totalDocentes = $datosPlan['plns_cant_docentes'] + $datosPaquetes['plns_cant_docentes'];
                                        $totalEstudianteAcudientes = $datosPlan['plns_cant_estudiantes'] + $datosPaquetes['plns_cant_estudiantes'];
                                        
                                        $directivosCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 5);
                                        $docentesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 2);
                                        $acudientesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 3);
                                        $estudiantesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 4);
                                        $estudianteAcudientesCreados = $acudientesCreados + $estudiantesCreados;
                                        
                                        $restanteDirectivos = !empty($datosPlan['plns_cant_directivos']) ? ($totalDirectivos - $directivosCreados) : 0;
                                        $restanteDocentes = !empty($datosPlan['plns_cant_docentes']) ? ($totalDocentes - $docentesCreados) : 0;
                                        $restanteEstudianteAcudientes = $totalEstudianteAcudientes - $estudianteAcudientesCreados;
                                        
                                        $infinitos = $datosUnicosInstitucion['ins_id_plan'] == 3 ? " (Infinitos)" : "";
                                        
                                        // Calcular porcentajes
                                        $porcDirectivos = $totalDirectivos > 0 ? round(($directivosCreados / $totalDirectivos) * 100, 1) : 0;
                                        $porcDocentes = $totalDocentes > 0 ? round(($docentesCreados / $totalDocentes) * 100, 1) : 0;
                                        $porcEstudiantes = $totalEstudianteAcudientes > 0 ? round(($estudianteAcudientesCreados / $totalEstudianteAcudientes) * 100, 1) : 0;
                                        ?>
                                        
                                        <!-- Tarjetas de resumen -->
                                        <div class="row mb-3">
                                            <div class="col-4 text-center">
                                                <div class="card bg-info text-white">
                                                    <div class="card-body p-2">
                                                        <h4 class="mb-0"><?=number_format($directivosCreados)?></h4>
                                                        <small>Directivos</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="card bg-warning text-white">
                                                    <div class="card-body p-2">
                                                        <h4 class="mb-0"><?=number_format($docentesCreados)?></h4>
                                                        <small>Docentes</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="card bg-success text-white">
                                                    <div class="card-body p-2">
                                                        <h4 class="mb-0"><?=number_format($estudianteAcudientesCreados)?></h4>
                                                        <small>Estudiantes/Acudientes</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gráfico de barras horizontales -->
                                        <canvas id="chartUsuarios" style="min-height: 280px;"></canvas>
                                        
                                        <!-- Detalles -->
                                        <div class="mt-3">
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><i class="fa fa-user text-info"></i> <strong>Directivos:</strong> <?=number_format($directivosCreados)?> / <?=number_format($totalDirectivos)?><?=$infinitos?></span>
                                                    <span class="badge badge-<?=$porcDirectivos >= 80 ? 'danger' : ($porcDirectivos >= 50 ? 'warning' : 'info')?>"><?=$porcDirectivos?>%</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?=$porcDirectivos?>%" aria-valuenow="<?=$porcDirectivos?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><i class="fa fa-graduation-cap text-warning"></i> <strong>Docentes:</strong> <?=number_format($docentesCreados)?> / <?=number_format($totalDocentes)?><?=$infinitos?></span>
                                                    <span class="badge badge-<?=$porcDocentes >= 80 ? 'danger' : ($porcDocentes >= 50 ? 'warning' : 'info')?>"><?=$porcDocentes?>%</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?=$porcDocentes?>%" aria-valuenow="<?=$porcDocentes?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><i class="fa fa-users text-success"></i> <strong>Estudiantes/Acudientes:</strong> <?=number_format($estudianteAcudientesCreados)?> / <?=number_format($totalEstudianteAcudientes)?></span>
                                                    <span class="badge badge-<?=$porcEstudiantes >= 80 ? 'danger' : ($porcEstudiantes >= 50 ? 'warning' : 'success')?>"><?=$porcEstudiantes?>%</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?=$porcEstudiantes?>%" aria-valuenow="<?=$porcEstudiantes?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-lg-6">
                                <div class="card">
                                    <div class="card-head">
                                        <header>USO DEL DISCO</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:location.reload();"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        try{
                                            $pesoInstituciones=mysqli_query($conexion, "SELECT plns_espacio_gb FROM $baseDatosServicios.instituciones 
                                            INNER JOIN $baseDatosServicios.planes_sintia  
                                            ON plns_id=ins_id_plan
                                            WHERE ins_id='".$config['conf_id_institucion']."' AND ins_enviroment='".ENVIROMENT."'");
                                        }catch(Exception $e){
                                            echo 'Caught exception: ',  $e->getMessage(), "\n";
                                            exit();
                                        }
                                        $peso=mysqli_fetch_array($pesoInstituciones, MYSQLI_BOTH);
                                        
                                        function calcularFile($dir) {
                                            clearstatcache();
                                            $cont = 0;
                                            if (is_dir($dir)) {
                                                if ($gd = opendir($dir)) {
                                                    while (($archivo = readdir($gd)) !== false) {
                                                        if ($archivo != "." && $archivo != "..") {
                                                            if (is_dir($archivo)) {
                                                                $cont += calcularFile($dir . "/" . $archivo);
                                                            } else {
                                                                if (strpos("/".$archivo,$_SESSION["inst"])){ 
                                                                    $cont += sprintf("%u", filesize($dir . "/" . $archivo));
                                                                }
                                                            }
                                                        }
                                                    }
                                                    closedir($gd);
                                                }
                                            }
                                            return $cont;
                                        }
                                        
                                        $contadorByte = 0;
                                        $contadorByte = calcularFile('../files/archivos');
                                        $contadorByte += calcularFile('../files/clases');
                                        $contadorByte += calcularFile('../files/evaluaciones');
                                        $contadorByte += calcularFile('../files/firmas');
                                        $contadorByte += calcularFile('../files/fotos');
                                        $contadorByte += calcularFile('../files/pclase');
                                        $contadorByte += calcularFile('../files/publicaciones');
                                        $contadorByte += calcularFile('../files/tareas');
                                        $contadorByte += calcularFile('../files/tareas-entregadas');
                                        
                                        $gbUsado = $contadorByte/1073741824;
                                        $gbTotal = !empty($peso[0]) ? $peso[0] : 0;
                                        $gbDisponible = $gbTotal - $gbUsado;
                                        $porcentaje = $gbTotal > 0 ? ($gbUsado/$gbTotal)*100 : 0;
                                        
                                        if($porcentaje <= 50) {
                                            $colorGrafico = 'info';
                                        } elseif($porcentaje > 50 && $porcentaje <= 80) {
                                            $colorGrafico = 'warning';
                                        } else {
                                            $colorGrafico = 'danger';
                                        }
                                        ?>
                                        
                                        <!-- Tarjeta de resumen -->
                                        <div class="row mb-3">
                                            <div class="col-6 text-center">
                                                <div class="card bg-<?=$colorGrafico?> text-white">
                                                    <div class="card-body p-3">
                                                        <h3 class="mb-0"><?=number_format($gbUsado, 2)?> GB</h3>
                                                        <small>Usado</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 text-center">
                                                <div class="card bg-light">
                                                    <div class="card-body p-3">
                                                        <h3 class="mb-0"><?=number_format($gbDisponible, 2)?> GB</h3>
                                                        <small>Disponible</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gráfico circular -->
                                        <div class="text-center mb-3">
                                            <canvas id="chartDisco" style="max-height: 200px;"></canvas>
                                        </div>
                                        
                                        <!-- Barra de progreso detallada -->
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span><i class="fa fa-hdd-o"></i> <strong>Espacio Total:</strong> <?=number_format($gbTotal, 2)?> GB</span>
                                                <span class="badge badge-<?=$colorGrafico?> badge-lg"><?=number_format($porcentaje, 1)?>%</span>
                                            </div>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar progress-bar-<?=$colorGrafico?> progress-bar-striped progress-bar-animated" 
                                                     role="progressbar" 
                                                     style="width: <?=$porcentaje?>%" 
                                                     aria-valuenow="<?=$porcentaje?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <strong><?=number_format($porcentaje, 1)?>%</strong>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <small class="text-muted">
                                                    <?php if($porcentaje <= 50): ?>
                                                        <i class="fa fa-check-circle text-success"></i> Espacio disponible suficiente
                                                    <?php elseif($porcentaje <= 80): ?>
                                                        <i class="fa fa-exclamation-triangle text-warning"></i> Considera liberar espacio
                                                    <?php else: ?>
                                                        <i class="fa fa-times-circle text-danger"></i> Espacio crítico - Libera espacio urgentemente
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // Obtener estadísticas de correos
                        $estadisticasCorreos = MailQueue::obtenerEstadisticas($datosUnicosInstitucion['ins_id']);
                        ?>
                        
                        <div class="row clearfix mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-head">
                                        <header>ESTADÍSTICAS DE CORREOS</header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:location.reload();"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Tarjetas de estadísticas -->
                                            <div class="col-md-3 col-sm-6">
                                                <div class="card bg-success text-white mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="card-title mb-0">Total Enviados</h6>
                                                                <h2 class="mb-0"><?=number_format($estadisticasCorreos['total_enviados'])?></h2>
                                                                <small class="opacity-75">Del historial</small>
                                                            </div>
                                                            <div class="fa fa-envelope fa-3x opacity-50"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 col-sm-6">
                                                <div class="card bg-primary text-white mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="card-title mb-0">Total Intentados</h6>
                                                                <h2 class="mb-0"><?=number_format($estadisticasCorreos['total_intentados'])?></h2>
                                                                <small class="opacity-75">Intentos registrados</small>
                                                            </div>
                                                            <div class="fa fa-paper-plane fa-3x opacity-50"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 col-sm-6">
                                                <div class="card bg-warning text-white mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="card-title mb-0">En Cola (Pendientes)</h6>
                                                                <h2 class="mb-0"><?=number_format($estadisticasCorreos['cola_pendiente'])?></h2>
                                                                <small class="opacity-75">Esperando envío</small>
                                                            </div>
                                                            <div class="fa fa-clock-o fa-3x opacity-50"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 col-sm-6">
                                                <div class="card bg-danger text-white mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="card-title mb-0">Total Fallidos</h6>
                                                                <h2 class="mb-0"><?=number_format($estadisticasCorreos['total_fallidos'])?></h2>
                                                                <small class="opacity-75">Errores definitivos</small>
                                                            </div>
                                                            <div class="fa fa-exclamation-triangle fa-3x opacity-50"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gráfico de estadísticas -->
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <canvas id="chartCorreos" style="min-height: 300px;"></canvas>
                                            </div>
                                        </div>
                                        
                                        <!-- Detalles adicionales -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">Detalles de la Cola</h6>
                                                        <table class="table table-sm">
                                                            <tr>
                                                                <td>Total en cola:</td>
                                                                <td class="text-right"><strong><?=number_format($estadisticasCorreos['cola_total'])?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Pendientes:</td>
                                                                <td class="text-right"><?=number_format($estadisticasCorreos['cola_pendiente'])?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Procesando:</td>
                                                                <td class="text-right"><?=number_format($estadisticasCorreos['cola_procesando'])?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Enviados (cola):</td>
                                                                <td class="text-right text-success"><?=number_format($estadisticasCorreos['cola_enviado'])?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Errores (cola):</td>
                                                                <td class="text-right text-danger"><?=number_format($estadisticasCorreos['cola_error'])?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Descartados:</td>
                                                                <td class="text-right text-muted"><?=number_format($estadisticasCorreos['cola_descartado'])?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">Historial de Correos</h6>
                                                        <table class="table table-sm">
                                                            <tr>
                                                                <td>Total intentados:</td>
                                                                <td class="text-right"><strong><?=number_format($estadisticasCorreos['total_intentados'])?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Enviados exitosamente:</td>
                                                                <td class="text-right text-success"><strong><?=number_format($estadisticasCorreos['total_enviados'])?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Errores (historial):</td>
                                                                <td class="text-right text-danger"><?=number_format($estadisticasCorreos['historial_error'])?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Errores (cola):</td>
                                                                <td class="text-right text-danger"><?=number_format($estadisticasCorreos['cola_error'])?></td>
                                                            </tr>
                                                            <tr class="table-info">
                                                                <td><strong>Total fallidos:</strong></td>
                                                                <td class="text-right"><strong class="text-danger"><?=number_format($estadisticasCorreos['total_fallidos'])?></strong></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page container -->
    <?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>

<script>
    // Gráfico de Usuarios - Barras horizontales
    const ctxUsuarios = document.getElementById('chartUsuarios');
    if (ctxUsuarios) {
        <?php
        $datosPlan = Plataforma::traerDatosPlanes($conexion, $datosUnicosInstitucion['ins_id_plan']);
        $datosPaquetes = Plataforma::contarDatosPaquetes($datosUnicosInstitucion['ins_id'], PAQUETES);
        $totalDirectivos = $datosPlan['plns_cant_directivos'] + $datosPaquetes['plns_cant_directivos'];
        $totalDocentes = $datosPlan['plns_cant_docentes'] + $datosPaquetes['plns_cant_docentes'];
        $totalEstudianteAcudientes = $datosPlan['plns_cant_estudiantes'] + $datosPaquetes['plns_cant_estudiantes'];
        
        $directivosCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 5);
        $docentesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 2);
        $acudientesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 3);
        $estudiantesCreados = UsuariosPadre::contarUsuariosPorTipo($conexion, 4);
        $estudianteAcudientesCreados = $acudientesCreados + $estudiantesCreados;
        
        $restanteDirectivos = !empty($datosPlan['plns_cant_directivos']) ? ($totalDirectivos - $directivosCreados) : 0;
        $restanteDocentes = !empty($datosPlan['plns_cant_docentes']) ? ($totalDocentes - $docentesCreados) : 0;
        $restanteEstudianteAcudientes = $totalEstudianteAcudientes - $estudianteAcudientesCreados;
        ?>
        
        const dataUsuarios = {
            labels: ['Directivos', 'Docentes', 'Estudiantes/Acudientes'],
            datasets: [
                {
                    label: 'Creados',
                    backgroundColor: ['rgba(23, 162, 184, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(40, 167, 69, 0.8)'],
                    borderColor: ['rgba(23, 162, 184, 1)', 'rgba(255, 193, 7, 1)', 'rgba(40, 167, 69, 1)'],
                    borderWidth: 2,
                    data: [<?=$directivosCreados?>, <?=$docentesCreados?>, <?=$estudianteAcudientesCreados?>]
                },
                {
                    label: 'Disponibles',
                    backgroundColor: ['rgba(200, 200, 200, 0.5)', 'rgba(200, 200, 200, 0.5)', 'rgba(200, 200, 200, 0.5)'],
                    borderColor: ['rgba(150, 150, 150, 1)', 'rgba(150, 150, 150, 1)', 'rgba(150, 150, 150, 1)'],
                    borderWidth: 1,
                    data: [<?=$restanteDirectivos?>, <?=$restanteDocentes?>, <?=$restanteEstudianteAcudientes?>]
                }
            ]
        };
        
        const configUsuarios = {
            type: 'horizontalBar',
            data: dataUsuarios,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label + ': ' + tooltipItem.xLabel;
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            }
        };
        new Chart(ctxUsuarios, configUsuarios);
    }

    // Gráfico de Disco - Donut Chart
    const ctxDisco = document.getElementById('chartDisco');
    if (ctxDisco) {
        <?php
        $gbUsado = isset($gbUsado) ? $gbUsado : 0;
        $gbTotal = isset($gbTotal) ? $gbTotal : 0;
        $gbDisponible = isset($gbDisponible) ? $gbDisponible : 0;
        $porcentaje = isset($porcentaje) ? $porcentaje : 0;
        $colorGrafico = isset($colorGrafico) ? $colorGrafico : 'info';
        
        // Definir colores según el estado
        $colorUsado = $colorGrafico == 'danger' ? 'rgba(220, 53, 69, 0.8)' : ($colorGrafico == 'warning' ? 'rgba(255, 193, 7, 0.8)' : 'rgba(23, 162, 184, 0.8)');
        $colorUsadoBorder = $colorGrafico == 'danger' ? 'rgba(220, 53, 69, 1)' : ($colorGrafico == 'warning' ? 'rgba(255, 193, 7, 1)' : 'rgba(23, 162, 184, 1)');
        ?>
        
        const dataDisco = {
            labels: ['Usado', 'Disponible'],
            datasets: [{
                data: [<?=$porcentaje?>, <?=100 - $porcentaje?>],
                backgroundColor: [
                    '<?=$colorUsado?>',
                    'rgba(200, 200, 200, 0.3)'
                ],
                borderColor: [
                    '<?=$colorUsadoBorder?>',
                    'rgba(150, 150, 150, 1)'
                ],
                borderWidth: 2
            }]
        };
        
        const configDisco = {
            type: 'doughnut',
            data: dataDisco,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutoutPercentage: 70,
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const label = data.labels[tooltipItem.index];
                            const value = data.datasets[0].data[tooltipItem.index];
                            if (label === 'Usado') {
                                return label + ': ' + value.toFixed(1) + '% (' + <?=number_format($gbUsado, 2)?> + ' GB)';
                            } else {
                                return label + ': ' + value.toFixed(1) + '% (' + <?=number_format($gbDisponible, 2)?> + ' GB)';
                            }
                        }
                    }
                }
            }
        };
        new Chart(ctxDisco, configDisco);
    }

    // Gráfico de estadísticas de correos
    const ctxCorreos = document.getElementById('chartCorreos');
    if (ctxCorreos) {
        const dataCorreos = {
            labels: [
                'Enviados (Historial)', 
                'Total Intentados', 
                'Pendientes (Cola)', 
                'Procesando (Cola)',
                'Total Fallidos',
                'Descartados'
            ],
            datasets: [{
                label: 'Estadísticas de Correos',
                data: [
                    <?=$estadisticasCorreos['total_enviados']?>,
                    <?=$estadisticasCorreos['total_intentados']?>,
                    <?=$estadisticasCorreos['cola_pendiente']?>,
                    <?=$estadisticasCorreos['cola_procesando']?>,
                    <?=$estadisticasCorreos['total_fallidos']?>,
                    <?=$estadisticasCorreos['cola_descartado']?>
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // Verde - Enviados (Historial)
                    'rgba(0, 123, 255, 0.8)',   // Azul - Total Intentados
                    'rgba(255, 193, 7, 0.8)',   // Amarillo - Pendientes (Cola)
                    'rgba(23, 162, 184, 0.8)',  // Cyan - Procesando (Cola)
                    'rgba(220, 53, 69, 0.8)',   // Rojo - Total Fallidos
                    'rgba(108, 117, 125, 0.8)'  // Gris - Descartados
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(0, 123, 255, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 2
            }]
        };

        const configCorreos = {
            type: 'bar',
            data: dataCorreos,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.labels[tooltipItem.index] + ': ' + tooltipItem.yLabel;
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                }
            }
        };
        new Chart(ctxCorreos, configCorreos);
    }
</script>


<!-- end js include path -->
</body>

</html>