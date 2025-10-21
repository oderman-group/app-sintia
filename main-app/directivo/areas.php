<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0017';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");

Utilidades::validarParametros($_GET);

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
                                <div class="page-title"><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-8 col-lg-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></header>
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
                                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0019'])) { ?>
                                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#nuevaAreaModal" class="btn deepPink-bgcolor">
                                                            Agregar nuevo <i class="fa fa-plus"></i>
                                                        </a>
                                                        <?php
                                                        $idModal = "nuevaAreaModal";
                                                        $contenido = "../directivo/areas-agregar-modal.php";
                                                        include("../compartido/contenido-modal.php");
                                                        } ?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Posición</th>
														<th><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Materias</th>
                                                        <?php if(Modulos::validarPermisoEdicion()){?>
														    <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                    $consulta = Areas::traerAreasInstitucion($config);
                                                    $contReg = 1;
                                                    while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                    $numMaterias = Asignaturas::contarAsignaturasArea($conexion, $config, $resultado['ar_id']);
                                                    ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['ar_id'];?></td>
														<td><?=$resultado['ar_posicion'];?></td>
														<td><?=$resultado['ar_nombre'];?></td>
														<?php 
															$materias = $numMaterias[0];

															if (Modulos::validarSubRol(['DT0020'])) {
																$materias = '<a href="asignaturas.php?area='.base64_encode($resultado['ar_id']).'" class="text-dark">'.$numMaterias[0].'</a>';
															}
														?>
														<td><span class="badge badge-warning"><?=$materias;?></span></td>
														
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0018','DT0150'])){?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0018'])){?>
                                                                            <li><a href="javascript:void(0);" class="btn-editar-area-modal" data-area-id="<?=$resultado['ar_id'];?>"><i class="fa fa-edit"></i> Edición rápida</a></li>
                                                                            <li><a href="areas-editar.php?id=<?=base64_encode($resultado['ar_id']);?>"><i class="fa fa-pencil"></i> <?=$frases[165][$datosUsuarioActual['uss_idioma']];?> completa</a></li>
                                                                        <?php } if($numMaterias[0]==0 && Modulos::validarSubRol(['DT0150'])){?><li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar esta area?','question','areas-eliminar.php?id=<?=base64_encode($resultado['ar_id']);?>')">Eliminar</a></li><?php }?>
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

<!-- Modal para edición rápida de área -->
<div class="modal fade" id="modalEditarArea" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición Rápida de Área</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarArea" action="areas-actualizar.php" method="post">
				<div class="modal-body">
					<div id="areaLoader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="areaFormulario" style="display:none;">
						<input type="hidden" id="edit_idA" name="idA">
						
						<div class="form-group">
							<label>Nombre del Área <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="edit_nombreA" name="nombreA" required>
						</div>
						
						<div class="form-group">
							<label>Posición</label>
							<input type="number" class="form-control" id="edit_posicion" name="posicionA" min="1">
						</div>
					</div>
					
					<div id="areaError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensajeArea"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-save"></i> Guardar Cambios
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// Edición rápida de área
	$(document).on('click', '.btn-editar-area-modal', function() {
		var areaId = $(this).data('area-id');
		
		$('#areaLoader').show();
		$('#areaFormulario').hide();
		$('#areaError').hide();
		$('#modalEditarArea').modal('show');
		
		$.ajax({
			url: 'ajax-obtener-datos-area.php',
			type: 'POST',
			data: { area_id: areaId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var area = response.area;
					$('#edit_idA').val(area.ar_id);
					$('#edit_nombreA').val(area.ar_nombre);
					$('#edit_posicion').val(area.ar_posicion || '');
					
					$('#areaLoader').hide();
					$('#areaFormulario').show();
				} else {
					$('#areaLoader').hide();
					$('#errorMensajeArea').text(response.message);
					$('#areaError').show();
				}
			},
			error: function() {
				$('#areaLoader').hide();
				$('#errorMensajeArea').text('Error de conexión');
				$('#areaError').show();
			}
		});
	});
	
	$('#formEditarArea').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function() {
				$.toast({
					heading: 'Éxito',
					text: 'Área actualizada correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
				$('#modalEditarArea').modal('hide');
				setTimeout(function() { location.reload(); }, 1000);
			},
			error: function() {
				$.toast({
					heading: 'Error',
					text: 'Error al actualizar',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error'
				});
			}
		});
	});
});
</script>

</body>

</html>