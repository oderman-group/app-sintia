<?php
include("session.php");

$idPaginaInterna = 'DT0205';
require_once(ROOT_PATH."/main-app/class/SubRoles.php");

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/head.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$id = "";
if (!empty($_GET["id"])) {
    $id = base64_decode($_GET["id"]);
}

$rolActual    = SubRoles::consultar($id);
$activasTodas = empty($_GET["activas"]) ? "0" : "1";
$checkActivas = ($activasTodas == "0") ? "" : "checked";
$listaPaginas = SubRoles::listarPaginas($id, "5", $activasTodas);

$parametrosBuscar = array(
	"institucion" =>$config['conf_id_institucion']
);	
$listaRoles=SubRoles::listar($parametrosBuscar);
?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="../js/Subroles.js"></script>
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
                        <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        <div class=" pull-left">
                            <div class="page-title">Editar Sub Rol</div>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="sub-roles.php?cantidad=10" onClick="deseaRegresar(this)">Sub Roles</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Editar Sub Rol</li>
                        </ol>
                    </div>
                </div>
                <?php include("../../config-general/mensajes-informativos.php"); ?>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">Sub Roles</header>
                            <div class="panel-body">
                                <nav>
                                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
                                        <?php
                                            while ($resultado = mysqli_fetch_array($listaRoles, MYSQLI_BOTH)) {
                                                $active = $id == $resultado['subr_id'] ? "active" : "";
                                                $selected = $id == $resultado['subr_id'] ? "true" : "false";
                                        ?>
                                                <a class="nav-item nav-link <?=$active?>" id="nav-rol<?=$resultado['subr_id']?>-tab" data-toggle="tab" href="#nav-rol<?=$resultado['subr_id']?>" role="tab" aria-controls="nav-rol<?=$resultado['subr_id']?>" aria-selected="<?=$selected?>" onClick="listarInformacion('async-editar-sub-rol.php?idRol=<?=base64_encode($resultado['subr_id'])?>&activas=<?=$activasTodas?>', 'nav-rol<?=$resultado['subr_id']?>', 'POST', null, <?=$resultado['subr_id']?>)"><?=$resultado['subr_nombre']?></a>
                                        <?php } ?>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">Editar Sub Rol</header>
                            <div class="tab-content" id="nav-tabContent">
                                    <?php
                                        $primerRol = "";
                                        $listaRoles2=SubRoles::listar($parametrosBuscar);
                                        while ($resultado2 = mysqli_fetch_array($listaRoles2, MYSQLI_BOTH)) {
                                            $active2 = $id == $resultado2['subr_id'] ? "active" : "";
                                            $show = $id == $resultado2['subr_id'] ? "show" : "";
                                            $primerRol = $id == $resultado2['subr_id'] ? $resultado2['subr_id'] : $primerRol;
                                    ?>
                                            <div class="tab-pane fade <?=$show?> <?=$active2?>" id="nav-rol<?=$resultado2['subr_id']?>" role="tabpanel" aria-labelledby="nav-rol<?=$resultado2['subr_id']?>-tab"></div>
                                    <?php } ?>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    listarInformacion('async-editar-sub-rol.php?idRol=<?=base64_encode($primerRol)?>&activas=<?=$activasTodas?>', 'nav-rol<?=$primerRol?>', 'POST', null, <?=$primerRol?>);
                                });
                            </script>
                            <div class="form-group">
                                <div class="col-md-9">
                                    <a href="javascript:void(0);" name="sub-roles.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i><?= $frases[184][$datosUsuarioActual['uss_idioma']]; ?></a>
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
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<!-- end js include path -->

</body>

</html>