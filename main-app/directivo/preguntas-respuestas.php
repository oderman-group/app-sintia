<?php
include("session.php");
$idPaginaInterna = 'DT0316';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once(ROOT_PATH . "/main-app/class/PreguntaGeneral.php");
require_once(ROOT_PATH."/main-app/class/Respuesta.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$id = !empty($_GET["id"]) ? base64_decode($_GET["id"]) : "";

$resultado = PreguntaGeneral::consultar($id);

$respuestaPreguntas = PreguntaGeneral::traerRespuestasPreguntas($conexion, $config, $id);
$idRespuestas = array();
foreach ($respuestaPreguntas as $arrayRespuestas) {
    $idRespuestas[] = $arrayRespuestas['gpr_id_respuesta'];
}

$consultaRespuestas = Respuesta::listarRespuestas($conexion, $config);
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
                        <?php include("../compartido/texto-manual-ayuda.php");?>
                        <div class=" pull-left">
                            <div class="page-title">Relacionar Respuestas</div>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="preguntas.php" onClick="deseaRegresar(this)">Preguntas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Relacionar Respuestas</li>
                        </ol>
                    </div>
                </div>
                <?php include("../../config-general/mensajes-informativos.php"); ?>
                <div class="panel">
                    <header class="panel-heading panel-heading-purple">Relacionar Respuestas</header>
                    <div class="panel-body">
                        <form action="preguntas-respuestas-actualizar.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="idP" value="<?= $resultado['pregg_id']; ?>">
                            <div class="form-group row">
                                <label class="col-sm-2 ">Pregunta</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" value="<?= $resultado['pregg_id']; ?>" readonly>
                                </div>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" value="<?= $resultado['pregg_descripcion']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Respuestas ( <label style="font-weight: bold;" id="cantSeleccionadasRespuesta"></label>/<?= mysqli_num_rows($consultaRespuestas) ?> )
                                            </header>
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <table id="example3" class="display" name="tabla1" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>
                                                                <div class="input-group spinner col-sm-10">
                                                                    <label class="switchToggle">
                                                                        <input type="checkbox" id="all">
                                                                        <span class="slider green round"></span>
                                                                    </label>
                                                                </div>
                                                            </th>
                                                            <th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
                                                            <th><?=$frases[52][$datosUsuarioActual['uss_idioma']];?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $contReg = 1;
                                                        while ($datosRespuestas = mysqli_fetch_array($consultaRespuestas, MYSQLI_BOTH)) {
                                                            $cheked = '';
                                                            if (!empty($idRespuestas)) {
                                                                $selecionado = in_array($datosRespuestas["resg_id"], $idRespuestas);
                                                                if ($selecionado) {
                                                                    $cheked = 'checked';
                                                                }
                                                            }

                                                        ?>
                                                            <tr>
                                                                <td><?= $contReg; ?></td>
                                                                <td>
                                                                    <div class="input-group spinner col-sm-10">
                                                                        <label class="switchToggle">
                                                                            <input type="checkbox" class="check" onchange="seleccionarRespuesta(this)" value="<?= $datosRespuestas['resg_id']; ?>" <?= $cheked; ?>>
                                                                            <span class="slider green round"></span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td><?=$datosRespuestas['resg_descripcion'];?></td>
                                                                <td><?=$datosRespuestas['resg_valor'];?></td>
                                                            </tr>
                                                        <?php $contReg++;
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <select id="respuestasSeleccionadas" name="respuestas[]" multiple hidden>
                                <?php
                                foreach ($respuestaPreguntas as $arrayRespuestas) {
                                    echo '<option value="' . $arrayRespuestas["resg_id"] . '"  selected >' . $arrayRespuestas["resg_id"] . '</option>';
                                }
                                ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9">
                            <?php $botones = new botonesGuardar("preguntas.php",Modulos::validarPermisoEdicion()); ?>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<!-- end page container -->
<?php include("../compartido/footer.php"); ?>
<script src="../js/Preguntas.js" ></script>
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
<script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
<!-- end js include path -->

</body>

</html>