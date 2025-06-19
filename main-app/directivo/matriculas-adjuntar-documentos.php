<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0352';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
Utilidades::validarParametros($_GET,['id','idMatricula']);
if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$id="";
$idMatricula="";
if(!empty($_GET["id"])){ $id=base64_decode($_GET["id"]);}
if(!empty($_GET["idMatricula"])){ $idMatricula=base64_decode($_GET["idMatricula"]);}

require_once ROOT_PATH . '/main-app/class/App/academico/Matricula_adjuntos.php';
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>

    <!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[434][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <input type="text" class="form-control d-none" id="txtIdEstudiante" value="<?= $id; ?>">
                    <input type="text" class="form-control d-none" id="txtIdInstitucion" value="<?= $_SESSION["idInstitucion"]; ?>">
                    <input type="text" class="form-control d-none" id="txtIdAnno" value="<?= $_SESSION["bd"]; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">								
								<div class="col-md-12">								
									<?php
                                        include("../../config-general/mensajes-informativos.php");									
                                    ?>
                                    <div class="card card-topline-purple">
    <div class="card-head">
        <header><?= $frases[434][$datosUsuarioActual['uss_idioma']]; ?> - <?php 
            echo Estudiantes::NombreCompletoDelEstudiante(Estudiantes::obtenerDatosEstudiante($idMatricula)); 
        ?></header>
        <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
        </div>
    </div>
    <div class="card-body">
        

        <div class="row">
            <div class="col-sm-12">
                <div class="btn-group">
                    <a href='estudiantes.php' class='btn btn-secondary'  style='text-transform:uppercase;margin-right:20px;'><i class='fa fa-long-arrow-left'></i><?=$frases[184][$datosUsuarioActual['uss_idioma']]?></a>
                    <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0353'])){?>
                        <a href="javascript:void(0);" onclick="btnNuevoClic();" style='text-transform:uppercase;' class="btn deepPink-bgcolor">  Agregar <i class="fa fa-plus"></i></a>
                    <?php 
                        $idModal = "documentoAdjuntoModal";
                        include("matriculas-adjuntar-documentos-agregar-modal.php");
                    }?>
                </div>
            </div>
        </div>

                    <div class="table-scrollable">
                        <table id="example1" class="display" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?= $frases[49][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[51][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[222][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[326][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[127][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[186][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[187][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[173][$datosUsuarioActual['uss_idioma']]; ?></th>
                                    <th><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $consulta = Academico_Matriculas_Adjuntos::ObtenerDocumentosxEstudiante($id,$_SESSION["bd"],$_SESSION["idInstitucion"]);
                                $contReg = 1;
                                foreach ($consulta as $resultado){
                                    $ocultar="SI";
                                    if ($resultado['ama_visible']) {
                                        $ocultar="NO";
                                    }

                                    $documento = '<a href="../files/documentos_adjuntos_estudiantes/'.$resultado['ama_documento'].'" target="_blank"> Ver documento</a>';
                                ?>
                                    <tr>
                                        <td><?= $contReg; ?></td>
                                        <td><?= $resultado["ama_id"]; ?></td>
                                        <td><?= $resultado['ama_fecha_registro']; ?></td>
                                        <td><?= $resultado['categoria']; ?></td>
                                        <td><?= $documento; ?></td>
                                        <td><?= $resultado['ama_titulo']; ?></td>
                                        <td><?= $resultado['uss_usuario']; ?></td>
                                        <td><?= $resultado['uss_nombre']; ?></td>
                                        <td><?= $ocultar; ?></td>                                        
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
                                                <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <?php if($resultado['ama_id_responsable'] == $_SESSION["id"] ) {?>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0354'])) { ?>
                                                            <li><a href="javascript:void(0);" onclick="btnEditarClic('<?= $resultado['ama_id']; ?>')"><?= $frases[375][$datosUsuarioActual['uss_idioma']]; ?></a></li>
                                                        <?php } 
                                                        if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0355'])) { ?>
                                                            <li><a href="javascript:void(0);" onclick="btnEliminarClic('<?= $resultado['ama_id']; ?>','<?= $resultado['ama_documento']; ?>')"><?= $frases[174][$datosUsuarioActual['uss_idioma']]; ?></a></li>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                            </div>
                                        </td>                                        
                                    </tr>
                                <?php
                                    $contReg++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>

    <script src="../js/matriculas-adjuntar-documentos.js" ></script>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>

</html>