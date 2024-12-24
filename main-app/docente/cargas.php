<?php
include("session.php");
$idPaginaInterna = 'DC0033';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/Estudiantes.php");
require_once("../class/Sysjobs.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");

$datosCargaActual = null;

if (!empty($_SESSION["infoCargaActual"])) {
    $datosCargaActual = $_SESSION["infoCargaActual"]['datosCargaActual'];
}
?>
</head>
<style>
    .alert-warning-select {
        color: #4f3e0d;
        background-color: #f5c426;
        border-color: #ffeeba;
    }

    .elemento-draggable {
        cursor: grab;
    }

    .elemento-draggable .blogThumb {
        height: 250px; /* Establece la altura que desees */
        overflow: hidden; /* Oculta el contenido que sobrepase la altura */
    }
</style>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>

<div id="overlayInforme">
    <div id="loader"></div>
    <div id="loading-text">Generando informe…</div>
</div>

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
                            <div class="page-title"><?= $frases[12][$datosUsuarioActual['uss_idioma']]; ?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <!-- start course list -->
                <div class="row">
                    <div class="col-sm-12">
                        <?php include("../../config-general/mensajes-informativos.php"); ?>

                        <?php

                        $filtro      = " AND car_docente = '" . $_SESSION['id'] . "'";
                        $order       = "CAST(car_posicion_docente AS SIGNED),car_curso, car_grupo, am.mat_nombre";
                        $cCargas     = CargaAcademica::listarCargas($conexion, $config, "", $filtro, $order);
                        $contReg     = 1;
                        $index       = 0;
                        $listaCargas = [];
                        while ($fila = $cCargas->fetch_assoc()) {
                            $listaCargas[$index] = $fila;
                            $index++;
                        }

                        $cargasCont = 1;
                        $nCargas = mysqli_num_rows($cCargas);
                        $mensajeCargas = new Cargas;
                        $mensajeCargas->verificarNumCargas($nCargas);
                        if ($nCargas > 0) {
                            ?>
                            <p>
                                <a href="../compartido/planilla-docentes.php?docente=<?= base64_encode($_SESSION["id"]); ?>"
                                    target="_blank" style="text-decoration: underline;">Imprimir todas mis planillas</a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="../compartido/planilla-docentes-notas.php?docente=<?= base64_encode($_SESSION["id"]); ?>"
                                    target="_blank" style="text-decoration: underline;">Imprimir planillas con resumen de
                                    notas</a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="cargas-general.php" style="text-decoration: underline;">Ir a vista general</a>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="javascript:void(0);"
                                    onClick="fetchGeneral('../compartido/progreso-docentes.php?modal=1', 'Progreso de los docentes')"
                                    style="text-decoration: underline;">Ver progreso de los docentes</a>
                            </p>
                        <?php } ?>
                        <div class="row" id="sortable-container">
                            <?php foreach ($listaCargas as $carga) {
                                $ultimoAcceso     = 'Nunca';
                                $fondoCargaActual = '#FFF';
                                $seleccionado     = false;

                                if (!empty($carga['car_ultimo_acceso_docente'])) {
                                    $ultimoAcceso = $carga['car_ultimo_acceso_docente'];
                                }

                                if (!empty($_COOKIE["carga"]) && $carga['car_id'] == $_COOKIE["carga"]) {
                                    $fondoCargaActual = '#2836551f';
                                    $seleccionado     = true;
                                }

                                $porcentajeCargas = $carga['actividades'] . "%&nbsp;&nbsp;-&nbsp;&nbsp;" . $carga['actividades_registradas'] . "%";
                                if ($cargasCont == 1) {
                                    $induccionEntrar  = 'data-hint="Haciendo click sobre el nombre o sobre la imagen puedes entrar a administrar esta carga académica."';
                                    $induccionSabanas = 'data-hint="Puedes ver las sábanas de cada uno de los periodos pasados."';
                                }

                                $marcaDG = '';

                                if ($carga['car_director_grupo'] == 1) {
                                    $marcaDG = '<i class="fa fa-star text-info" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director de grupo"></i> ';
                                }

                                $marcaMediaTecnica = '';

                                if ($carga['gra_tipo'] == GRADO_INDIVIDUAL) {
                                    $marcaMediaTecnica = '<i class="fa fa-bookmark" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Media técnica"></i> ';
                                    $cantidadEstudiantes = $carga['cantidad_estudiantes_mt'];
                                } else {

                                    $cantidadEstudiantes = $carga["cantidad_estudiantes"];
                                }

                                $cantEstudiantes = 0;
                                $verMsj = false;
                                ?>
                                <div class="col-lg-2 col-md-6 col-12 col-sm-6 sortable-item elemento-draggable"
                                    draggable="true" id="carga-<?= $carga['car_id']; ?>">
                                    <div class="blogThumb border border-dark" style="background-color:<?= $fondoCargaActual; ?>;">
                                        <!-- <div class="thumb-center">
                                            <a href="cargas-seleccionar.php?carga=<?= base64_encode($carga['car_id']); ?>&periodo=<?= base64_encode($carga['car_periodo']); ?>"
                                                title="Entrar">
                                                <img class="img-responsive" alt="user"
                                                    src="../../config-general/assets/img/course/course1.jpg">
                                            </a>
                                        </div> -->
                                        <div class="course-box">
                                            <h5 <?= $induccionEntrar; ?>><a
                                                    href="cargas-seleccionar.php?carga=<?= base64_encode($carga['car_id']); ?>&periodo=<?= base64_encode($carga['car_periodo']); ?>"
                                                    title="Entrar"
                                                    style="text-decoration: underline;"><?= strtoupper($carga['mat_nombre']); ?></a>
                                            </h5>

                                            <p>
                                                <span>
                                                    <b><?= $marcaDG . " " . $marcaMediaTecnica . "" . $frases[164][$datosUsuarioActual['uss_idioma']]; ?>:</b>
                                                    <?= strtoupper($carga['gra_nombre'] . " " . $carga['gru_nombre']) . " <b>(" . $cantidadEstudiantes . " Est.)</b> "; ?></span>
                                            </p>


                                            <p align="center" <?= $induccionSabanas; ?>>
                                                <?php for ($i = 1; $i < $carga["car_periodo"]; $i++) { ?><a
                                                        href="../compartido/reportes-sabanas-fast.php?curso=<?= base64_encode($carga["car_curso"]); ?>&grupo=<?= base64_encode($carga["car_grupo"]); ?>&per=<?= base64_encode($i); ?>"
                                                        target="_blank" style="text-decoration:underline; color:#00F;"
                                                        title="Sabanas"><?= $i; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
                                            </p>

                                            <div class="text">
                                                <span class="m-r-10" style="font-size: 10px;"><b>Notas:</b>
                                                    <?= $carga['actividades'] ?>%
                                                    / <?= $carga['actividades_registradas'] ?>% | <b>Periodo:</b>
                                                    <?= $carga['car_periodo']; ?> |
                                                    <b>Posición:</b> <?= $carga['car_posicion_docente']; ?></span>
                                            </div>

                                            <span id="mensajeI<?= $carga['car_id'] ?>">
                                                <?php
                                                $generarInforme = false;
                                                $jobsEncontrado = empty($carga["job_id"]) ? false : true;
                                                $msj  = "";
                                                $actividadesDeclaradas  = $carga['actividades'];
                                                $actividadesRegistradas = $carga['actividades_registradas'];
                                                $configGenerarJobs      = $config['conf_porcentaje_completo_generar_informe'];
                                                $numSinNotas            = $carga["cantidad_estudiantes_sin_nota"];
                                                $tipoAlerta             = 'alert-danger';
                                                $calificarFaltantes     = false;
                                                
                                                if ($actividadesDeclaradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
                                                    $generarInforme = false;
                                                } else if ($actividadesRegistradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
                                                    $generarInforme = false;
                                                } else if ($carga["car_permiso1"] == 0){
                                                    $generarInforme = false;
                                                    $msj = "Sin permiso para generar.";
                                                    $verMsj =true;
                                                }else{
                                                    $generarInforme = true;
                                                }

                                                if ($jobsEncontrado) { 
                                                    $generarInforme=false;
                                                    $intento = intval($carga["job_intentos"]);
                                                    switch ($carga["job_estado"]) {

                                                        case JOBS_ESTADO_ERROR:
                                                            $msj            = $carga["job_mensaje"];
                                                            if ($configGenerarJobs == 1) {                                                              
                                                               $tipoAlerta     = "alert-danger";  
                                                            }else{
                                                               $tipoAlerta     = "alert-info";  
                                                               $generarInforme = true;
                                                            }                                                                                                                      
                                                            $verMsj            = true;
                                                            break;
                    
                                                        case JOBS_ESTADO_PENDIENTE:
                                                            $msj = $carga["job_mensaje"];
                                                            $tipoAlerta = "alert-success";
                                                            if ($intento > 0 && $seleccionado) {
                                                                $tipoAlerta     = "alert-warning-select";  
                                                                $msj            .= "<br><br>(La plataforma ha echo <b>'.$intento.'</b> intentos.)";
                                                            } elseif ($intento > 0) {
                                                                $tipoAlerta     = "alert-warning";  
                                                                $msj            .= "<br><br>(La plataforma ha echo <b>'.$intento.'</b> intentos.)";
                                                            }
                                                            $verMsj =true;
                                                            break;
                    
                                                        case JOBS_ESTADO_PROCESO:
                                                            $msj        = "El informe está en proceso.";
                                                            $tipoAlerta = "alert-success";
                                                            $verMsj     = true;
                                                            break;
                                                        case JOBS_ESTADO_PROCESADO:
                                                            $msj        = "El informe ya fué procesado.";
                                                            $tipoAlerta = "alert-success";
                                                            $verMsj     = true;
                                                            break;
                    
                                                        default:
                                                            $generarInforme=true;
                                                            break;
                                                    }
                                                }
                                                if ($generarInforme) {
                                                    switch (intval($configGenerarJobs)) {
                                                        case 1:
                                                            if ($numSinNotas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
                                                                $generarInforme     = false;
                                                                $msj                = "hay $numSinNotas estudiantes sin notas, El informe no se puede generar, coloque las notas a todos los estudiantes para generar el informe.";
                                                                $tipoAlerta         = "alert-danger";
                                                                $calificarFaltantes = true;
                                                                $verMsj             = true;
                                                                break;
                                                            }
                                                            break;
                                                    }
                                                }
                                                if($generarInforme){ 
                                                    $parametros='?carga='.base64_encode($carga["car_id"]).
                                                    '&periodo='.base64_encode($carga["car_periodo"]).
                                                    '&grado='.base64_encode($carga["car_curso"]).
                                                    '&grupo='.base64_encode($carga["car_grupo"]).
                                                    '&tipoGrado='.base64_encode($carga["gra_tipo"]);
                                                    
                                                    ?>
                                                <div class="btn-group mt-2">
                                                    <button type="button" class="btn red">Generar Informe</button>
                                                    <button type="button" class="btn red dropdown-toggle m-r-20" data-toggle="dropdown">
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            <a
                                                            rel="<?=$configGenerarJobs.'-'.$numSinNotas.'-1';?>" 
                                                            data-toggle="tooltip" 
                                                            data-placement="right" 
                                                            title="Lo hará usted manualmente como siempre." 
                                                            href="javascript:void(0);" 
                                                            name="../compartido/generar-informe.php<?=$parametros?>" 
                                                            onclick="mensajeGenerarInforme(this)">
                                                            Manualmente
                                                            </a>
                                                        </li>
                                                        <!-- <li>
                                                            <a 
                                                            rel="<?=$configGenerarJobs.'-'.$numSinNotas.'-2';?>" 
                                                            data-toggle="tooltip" 
                                                            data-placement="right"
                                                            title="Se programara la generación de informe y se te notificará cuando esté listo" 
                                                            id="<?=$carga["car_id"]?>" 
                                                            href="javascript:void(0);" 
                                                            name="../compartido/job-generar-informe.php<?=$parametros?>" 
                                                            onclick="mensajeGenerarInforme(this)">
                                                            Automático
                                                            </a>
                                                        </li> -->
                                                    </ul>
                                                </div>
                                                <?php } ?>
                                                <?php if($verMsj){ ?>
                                                <div class="alert <?= $tipoAlerta ?> mt-2" role="alert" style="margin-right: 20px;">
                                                        
                                                        <?php if($calificarFaltantes){ ?>
                                                        <a target="_blank" href="calificaciones-faltantes.php?carga=<?=base64_encode($carga["car_id"])?>&periodo=<?=base64_encode($carga["car_periodo"])?>&get=<?=base64_encode(100)?>">
                                                            <?=$msj?>
                                                        </a>
                                                        <?php }else{?>
                                                            <?=$msj?>
                                                        <?php } ?>
                                                </div>
                                                <?php } ?>
                                                <?php if ($carga["car_periodo"] > $carga["gra_periodos"]) { ?>
                                                    <span style='color:blue;'>Terminado</span>
                                                <?php } ?>
                                            </span>

                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>

                </div>

                <!-- End course list -->

            </div>
        </div>
        <!-- end page content -->
        <?php // include("../compartido/panel-configuracion.php"); ?>
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
<script src="../../config-general/assets/plugins/sparkline/jquery.sparkline.js"></script>
<script src="../../config-general/assets/js/pages/sparkline/sparkline-data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- chart js -->
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>
<script src="../../config-general/assets/plugins/chart-js/utils.js"></script>
<script src="../../config-general/assets/js/pages/chart/chartjs/home-data.js"></script>
<!-- summernote -->
<script src="../../config-general/assets/plugins/summernote/summernote.js"></script>
<script src="../../config-general/assets/js/pages/summernote/summernote-data.js"></script>
<!-- end js include path -->

<script>

    const sortableContainer = document.getElementById("sortable-container");
    let draggedItem = null;
    let fromIndex, toIndex;
    let idCarga;
    let target;
    let docente = '<?= $_SESSION["id"]; ?>';

    sortableContainer.addEventListener("dragstart", (e) => {
        draggedItem = e.target;
        fromIndex = Array.from(sortableContainer.children).indexOf(draggedItem);
        idCarga = e.target.id.split('-')[1];

        target = e.target;

        target.style.backgroundColor = "#f0f0f0";
        target.style.transition = "all 0.2s ease";
    });

    sortableContainer.addEventListener("dragover", (e) => {
        e.preventDefault();
        const targetItem = e.target;
        if (targetItem.classList.contains("sortable-item")) {
            toIndex = Array.from(sortableContainer.children).indexOf(targetItem);
        }
    });

    sortableContainer.addEventListener("drop", (e) => {
        e.preventDefault();
        if (fromIndex > -1 && toIndex > -1) {
            if (fromIndex < toIndex) {
                sortableContainer.insertBefore(draggedItem, sortableContainer.children[toIndex].nextSibling);
            } else {
                sortableContainer.insertBefore(draggedItem, sortableContainer.children[toIndex]);
            }
        }
        target = e.target;

        target.style.backgroundColor = "initial";
        target.style.transition = "initial";

        if (typeof toIndex === undefined) {
            toIndex = 1;
        } else {
            toIndex++;
        }

        cambiarPosicion(idCarga, toIndex, docente);
    });

    // Prevenir eventos por defecto
    document.addEventListener("dragover", (e) => {
        e.preventDefault();
    });

</script>
</body>

</html>