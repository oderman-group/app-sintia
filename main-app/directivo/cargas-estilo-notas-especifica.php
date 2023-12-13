<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0045';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
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
                                <div class="page-title">Categoria Notas especificas</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cargas.php" onClick="deseaRegresar(this)">Cargas</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Notas especifica</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Categoria Notas especificas</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0047'])){?>
                                                            <a href="cargas-estilo-notas-especifica-agregar.php?id=<?=$_GET["id"]?>" id="addRow" class="btn deepPink-bgcolor">
                                                                Agregar nuevo <i class="fa fa-plus"></i>
                                                            </a>
                                                        <?php }?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>C&oacute;digo</th>
                                                        <th>Nombre</th>
                                                        <th>Nota desde</th>
                                                        <th>Nota hasta</th>
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0046','DT0155'])){?>
														    <th>Acciones</th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                    try{
                                                        $consulta = mysqli_query($conexion, "SELECT notip_id, notip_nombre, notip_desde, notip_hasta FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_categoria='".base64_decode($_GET["id"])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
													$contReg = 1;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
                                                        <td><?=$resultado["notip_id"];?></td>
                                                        <td><?=$resultado["notip_nombre"];?></td>
                                                        <td><?=$resultado["notip_desde"];?></td>
                                                        <td><?=$resultado["notip_hasta"];?></td>
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0046','DT0155'])){?>														
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0046'])){?>
                                                                        <li><a href="cargas-estilo-notas-especifica-editar.php?id=<?=base64_encode($resultado["notip_id"]);?>&idCN=<?=$_GET["id"]?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
																		<?php } if(Modulos::validarSubRol(['DT0155'])){?>
                                                                        <li>
                                                                        <a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','cargas-estilo-notas-especifica-eliminar.php?idN=<?=base64_encode($resultado["notip_id"]);?>&idNC=<?=$_GET["id"]?>')">Eliminar</a>    
                                                                        </li>
                                                                        <?php }?>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        <?php }?>
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
								
								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
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