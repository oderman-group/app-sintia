<?php
include("session.php");
$idPaginaInterna = 'DT0314';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once(ROOT_PATH . "/main-app/class/EvaluacionGeneral.php");
require_once(ROOT_PATH."/main-app/class/PreguntaGeneral.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$id = !empty($_GET["id"]) ? base64_decode($_GET["id"]) : "";

$resultado = EvaluacionGeneral::consultar($id);
$preguntasEvaluacion = EvaluacionGeneral::traerPreguntasEvaluacion($conexion, $config, $id);
$idPreguntas = array();
foreach ($preguntasEvaluacion as $arrayPreguntas) {
    $idPreguntas[] = $arrayPreguntas['gep_id_pregunta'];
}

$parametros = [
    'pregg_institucion'=>$config['conf_id_institucion'],
    'pregg_year'=>$_SESSION['bd']
];
$consultaPreguntas = PreguntaGeneral::listar($parametros);
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
                            <div class="page-title">Relacionar Preguntas</div>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="evaluaciones.php" onClick="deseaRegresar(this)">Evaluaciones</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Relacionar Preguntas</li>
                        </ol>
                    </div>
                </div>
                <?php include("../../config-general/mensajes-informativos.php"); ?>
                <div class="panel">
                    <header class="panel-heading panel-heading-purple">Relacionar Preguntas</header>
                    <div class="panel-body">
                        <form action="evaluaciones-preguntas-actualizar.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="idE" value="<?= $resultado['evag_id']; ?>">
                            <div class="form-group row">
                                <label class="col-sm-2 ">Evaluacion</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" value="<?= $resultado['evag_id']; ?>" readonly>
                                </div>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" value="<?= $resultado['evag_nombre']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Preguntas ( <label style="font-weight: bold;" id="cantSeleccionadasPreguntas"></label>/<?=!empty($consultaPreguntas) ? mysqli_num_rows($consultaPreguntas) : "0"; ?> )
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
                                                            <th><?=$frases[139][$datosUsuarioActual['uss_idioma']];?></th>
                                                            <th><?=$frases[294][$datosUsuarioActual['uss_idioma']];?> <?=$frases[139][$datosUsuarioActual['uss_idioma']];?></th>
                                                            <th>Visible</th>
                                                            <th>Obligatoria</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $contReg = 1;
                                                        if (!empty($consultaPreguntas)) {
                                                        while ($preguntas = mysqli_fetch_array($consultaPreguntas, MYSQLI_BOTH)) {
                                                            $cheked = '';
                                                            if (!empty($idPreguntas)) {
                                                                $selecionado = in_array($preguntas["pregg_id"], $idPreguntas);
                                                                if ($selecionado) {
                                                                    $cheked = 'checked';
                                                                }
                                                            }

                                                            
                                                            $descripcion = !empty($preguntas['pregg_descripcion']) ? $preguntas['pregg_descripcion'] : "";
                                                            $tipo_pregunta = !empty($preguntas['pregg_tipo_pregunta']) ? $preguntas['pregg_tipo_pregunta'] : "";
                                                            $visible = !empty($preguntas['pregg_visible']) ? $preguntas['pregg_visible'] : "";
                                                            $obligatoria = !empty($preguntas['pregg_obligatoria']) ? $preguntas['pregg_obligatoria'] : "";

                                                        ?>
                                                            <tr>
                                                                <td><?= $contReg; ?></td>
                                                                <td>
                                                                    <div class="input-group spinner col-sm-10">
                                                                        <label class="switchToggle">
                                                                            <input type="checkbox" class="check" onchange="seleccionarPreguntas(this)" value="<?= $preguntas['pregg_id']; ?>" <?= $cheked; ?>>
                                                                            <span class="slider green round"></span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td><?=$descripcion;?></td>
                                                                <td>
                                                                <?php 
                                                                    if($tipo_pregunta === TEXT){?>
                                                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="<?=$frases[421][$datosUsuarioActual['uss_idioma']];?>"><i class="fa fa-inbox"></i></button>
                                                                        <?php }elseif($tipo_pregunta === MULTIPLE){?>
                                                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="<?=$frases[422][$datosUsuarioActual['uss_idioma']];?>"><i class="fa fa-tasks"></i></button>
                                                                    <?php  }else if($tipo_pregunta === SINGLE){?>
                                                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="<?=$frases[423][$datosUsuarioActual['uss_idioma']];?>"><i class="fa fa-check"></i></button>
                                                                <?php }?>
                                                                </td>
                                                                <td>
                                                                <?php 
                                                                    if($visible == 1){?>
                                                                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Esta visible"><i class="fa fa-eye"></i></button>
                                                                    <?php }else{?>
                                                                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="No esta visible"><i class="fa fa-eye-slash"></i></button>
                                                                <?php }?>
                                                                </td>
                                                                <td>
                                                                <?php 
                                                                    if($obligatoria == 1){?>
                                                                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Es requerido"><i class="fa fa-lock"></i></button>
                                                                    <?php }else{?>
                                                                        <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="No es requerido"><i class="fa fa-unlock" aria-hidden="true"></i></button>
                                                                <?php }?>
                                                                </td>

                                                            </tr>
                                                        <?php $contReg++;
                                                        }} ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <select id="preguntasSeleccionadas" name="preguntas[]" multiple hidden>
                                <?php
                                foreach ($preguntasEvaluacion as $arrayPreguntas) {
                                    echo '<option value="' . $arrayPreguntas["pregg_id"] . '"  selected >' . $arrayPreguntas["pregg_id"] . '</option>';
                                }
                                ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9">
                            <?php $botones = new botonesGuardar("evaluaciones.php",Modulos::validarPermisoEdicion()); ?>
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
<script src="../js/Evaluaciones.js" ></script>
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