<?php
include("session.php");
$idPaginaInterna = 'DT0336';
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH . "/main-app/compartido/head.php");
?>
</head>
<!-- END HEAD -->
<?php include(ROOT_PATH . "/main-app/compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include(ROOT_PATH . "/main-app/compartido/encabezado.php"); ?>
    <?php include(ROOT_PATH . "/main-app/compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include(ROOT_PATH . "/main-app/compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title">Comprar Paquetes Extras</div>
                            <?php include("../compartido/texto-manual-ayuda.php");?>
                        </div>
                    </div>
                </div>
                <!-- start course list -->
                <?php
                    include("includes/barra-superior-servicios-paquetes.php");
                ?>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="row">
                            <?php
                            $serviciosConsulta = Plataforma::listarPaquetes($filtro);
                            $numServicios = mysqli_num_rows($serviciosConsulta);
                            if ($numServicios > 0) {
                                while ($datosServicios = mysqli_fetch_array($serviciosConsulta, MYSQLI_BOTH)) {
                                    $ruta = "../files/paquetes/";
                                    $foto = !empty($datosServicios['plns_imagen']) && file_exists($ruta.$datosServicios['plns_imagen']) ? $ruta.$datosServicios['plns_imagen'] : "../files/paquetes/default.png";

                                    $precio = 0;
                                    if (!empty($datosServicios['plns_valor'])) {
                                        $precio = $datosServicios['plns_valor'];
                                    }

                                    $fondoDisponible    = '';
                                    $fondoDestacado     = 'info';
                                    $destacado         = 'Disponible';
                                    $onClick            = "onClick='mostrarModalCompraPaquete({$datosServicios['plns_id']})'";
                                    $bordeDestacado     = 'border-info rounded-top animate__animated animate__pulse animate__delay-1s animate__repeat-3';
                                    if($datosServicios['plns_tipo'] == MODULOS && Plataforma::validarPaquete($_SESSION["idInstitucion"], $datosServicios['plns_id'], PAQUETES)){
                                        $onClick            = "";
                                        $fondoDestacado     = 'danger';
                                        $bordeDestacado     = 'border-light';
                                        $destacado         = 'Ya Adquirido';
                                        $fondoDisponible    = 'background-color:#9e9e9e33;';
                                    }
                            ?>
                                    <div class="col-lg-3 col-md-6 col-12 col-sm-6 mb-3" id="reg<?= $datosServicios['plns_id']; ?>">
                                        <div class="blogThumb border <?= $bordeDestacado; ?>" style="height: 100%;  <?= $fondoDisponible; ?>">
                                            <div class="thumb-center" style="height: 55%;">
                                                <a href="javascript:void(0);" <?= $onClick; ?>><img class="img-responsive" style="height: 300px;" src="<?= $foto; ?>"></a>
                                            </div>
                                            <div class="course-box" style="height: 45%;  display: flex; flex-direction: column; justify-content: flex-end;">
                                                <h5><a style="color:cadetblue;" name="javascript:void(0);" <?= $onClick; ?>><?= strtoupper($datosServicios['plns_nombre']); ?></a> <span class="badge badge-<?=$fondoDestacado?>"><?=$destacado?></span>
                                                </h5>
                                                <div class="text-muted" style="overflow: hidden;">
                                                    <?php if (!empty($datosServicios['plns_tipo'])) { ?><span class="m-r-10" style="font-size: 9px;"> <kbd><?= $datosServicios['plns_tipo']; ?></kbd></span> <?php } ?>
                                                </div>
                                                <p>
                                                    <span style="font-weight: bold;"> $<?= number_format($precio, 0, ",", ".") ?></span><br>
                                                </p>
                                                <p>
                                                    <?php
                                                    if ($precio >= 1 && ($datosServicios['plns_tipo'] != MODULOS || ($datosServicios['plns_tipo'] == MODULOS && !Plataforma::validarPaquete($_SESSION["idInstitucion"], $datosServicios['plns_id'], PAQUETES)))) {
                                                    ?>
                                                        <a href="javascript:void(0);" <?= $onClick; ?> class="btn btn-info"><i class="fa fa-credit-card"></i>ADQUIRIR PAQUETE</a>
                                                        <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, mi nombre es <?=UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual)?>, soy de la compañía <?=$informacion_inst["info_nombre"]?>, me gustaría recibir más información sobre el paquete <?=strtoupper($datosServicios['plns_nombre'])?>" target="_blank" class="btn btn-success" title="CONTACTAR CON UN ASESOR"><i class="fa fa-envelope"></i></a>
                                                    <?php
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page container -->
    <?php include(ROOT_PATH . "/main-app/compartido/footer.php"); ?>
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
</body>

</html>