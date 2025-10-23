<?php
include("session.php");

$idPaginaInterna = 'DV0005';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

$Plataforma = new Plataforma;
?>
<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    function guardarAjax(datos){ 
        var idR = datos.id;
        var valor = 0;

        if(document.getElementById(idR).checked){
            valor = 1;
            document.getElementById("Reg"+idR).style.backgroundColor="#ff572238";
        }else{
            valor = 0;
            document.getElementById("Reg"+idR).style.backgroundColor="white";
        }
        var operacion = 3;

        $('#respuestaGuardar').empty().hide().html("").show(1);
            datos = "idR="+(idR)+
                    "&valor="+(valor)+
                    "&operacion="+(operacion);
                    $.ajax({
                        type: "POST",
                        url: "ajax-guardar.php",
                        data: datos,
                        success: function(data){
                        $('#respuestaGuardar').empty().hide().html(data).show(1);
                        }
                    });
    }
</script>
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
                            <div class="page-title"><?=$frases[399][$datosUsuarioActual['uss_idioma']];?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <?php
                                $filtro = '';
                                $plan = '';
                                if (!empty($_GET["plan"])) {
                                    $plan = base64_decode($_GET["plan"]);
                                    $filtro .= " AND ins_id_plan='" . $plan . "'";
                                }                    
                            ?>

                            <div class="col-md-12">
                                <?php
                                include("../../config-general/mensajes-informativos.php");
                                include("includes/barra-superior-dev-instituciones.php");
                                ?>
                                <span id="respuestaGuardar"></span>

                                <div class="card card-topline-purple">
                                    <div class="card-head">
                                        <header><?=$frases[399][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="tools">
                                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="table-scrollable">
                                            <table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Bloq</th>
                                                        <th>Cod</th>
                                                        <th>Fecha Inicio</th>
                                                        <th>Nombre Institución</th>
                                                        <th>Contacto Principal</th>
                                                        <th>Plan</th>
                                                        <th>Espacio (GB)</th>
                                                        <th>Fecha Renovación</th>
                                                        <th>Estado</th>
                                                        <th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
													include("includes/consulta-paginacion-dev-instituciones.php");

                                                    try{
                                                        $consulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones
                                                        LEFT JOIN ".$baseDatosServicios.".planes_sintia ON plns_id=ins_id_plan
                                                        WHERE ins_id=ins_id AND ins_enviroment='".ENVIROMENT."' $filtro
                                                        ORDER BY ins_id
                                                        ");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    $contReg = 1;
                                                    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                        
                                                        $estado="Activo";
                                                        if($resultado['ins_estado']!=1){
                                                            $estado="Inactivo";
                                                        }
                                                        $espacio="";
                                                        if($resultado['plns_id']!="" || $resultado['plns_id']!=NULL){
                                                            $espacio=$resultado['plns_espacio_gb']."GB";
                                                        }

                                                        $bgColor = '';
                                                        if($resultado['ins_bloqueada']==1){$bgColor = '#ff572238';}
                                                        
                                                       $cheked = '';
                                                       if($resultado['ins_bloqueada']==1){$cheked = 'checked';}
                                                    ?>
                                                        <tr id="Reg<?=$resultado['ins_id'];?>" style="background-color:<?=$bgColor;?>;">
                                                            <td><?= $contReg; ?></td>
                                                            <td>
                                                                <div class="input-group spinner col-sm-10">
                                                                    <label class="switchToggle">
                                                                        <input type="checkbox" id="<?=$resultado['ins_id'];?>" name="bloqueado" value="1" onChange="guardarAjax(this)" <?=$cheked;?>>
                                                                        <span class="slider red round"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td><?= $resultado['ins_id']; ?></td>
                                                            <td><?= $resultado['ins_fecha_inicio']; ?></td>
                                                            <td><?= $resultado['ins_nombre']; ?></td>
                                                            <td><?= $resultado['ins_contacto_principal']; ?></td>
                                                            <td><?= $resultado['plns_nombre']; ?></td>
                                                            <td><?= $espacio; ?></td>
                                                            <td><?= $resultado['ins_fecha_renovacion']; ?></td>
                                                            <td><?= $estado; ?></td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                        <li><a href="dev-instituciones-editar-v2.php?id=<?= base64_encode($resultado['ins_id']); ?>">Editar</a></li>
                                                                        <li><a href="dev-instituciones-configuracion.php?id=<?= base64_encode($resultado['ins_id']); ?>"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                        <li><a href="dev-instituciones-Informacion.php?id=<?= base64_encode($resultado['ins_id']); ?>">Información</a></li>
                                                                        <li><a href="auto-login-dev.php?user=<?=base64_encode(1);?>&idInstitucion=<?=base64_encode($resultado['ins_id']);?>&bd=<?=base64_encode($resultado['ins_bd']);?>&yearDefault=<?=base64_encode($resultado['ins_year_default']);?>">Autologin</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php $contReg++;
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php //include("enlaces-paginacion.php");?>
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
<!-- end js include path -->
</body>

</html>