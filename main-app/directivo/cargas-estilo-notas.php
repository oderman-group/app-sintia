<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0044';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/categoriasNotas.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Calificacion.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Verificar si existen registros académicos (calificaciones) en la institución/año actual
try {
	$predicadoCal = [
		'institucion' => $config['conf_id_institucion'],
		'year'        => $_SESSION['bd']
	];
	$hayRegistrosAcademicosNotas = Academico_Calificacion::contarRegistrosEnCalificaciones($predicadoCal) > 0;
} catch (Exception $e) {
	$hayRegistrosAcademicosNotas = false;
	include("../compartido/error-catch-to-report.php");
}
?>
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
                                <div class="page-title">Categoria Notas</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Categoria Notas</header>
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
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0048'])){?>
                                                            <button type="button" id="btnAgregarCategoriaNotas" class="btn deepPink-bgcolor">
                                                                Agregar nuevo <i class="fa fa-plus"></i>
                                                            </button>
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
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0045','DT0154'])){?>
														    <th>Acciones</th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$contReg = 1;
                                                    $consulta = categoriasNota::traerCategoriasNotasInstitucion($config);
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado["catn_id"];?></td>
														<td><?=$resultado["catn_nombre"];?></td>	
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0045','DT0154'])){?>													
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary">Acciones</button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0045'])){?>
                                                                        <li>
																			<a href="javascript:void(0);"
																			   class="btn-editar-categoria-nota-modal"
																			   data-id="<?=$resultado["catn_id"];?>"
																			   data-nombre="<?=htmlspecialchars($resultado["catn_nombre"], ENT_QUOTES, 'UTF-8');?>"
																			>
																				<?=$frases[165][$datosUsuarioActual['uss_idioma']];?> rápida
																			</a>
																		</li>
																		<li>
																			<a href="cargas-estilo-notas-especifica.php?id=<?=base64_encode($resultado['catn_id']);?>">
																				Configurar rangos
																			</a>
																		</li>
																		<?php } if(Modulos::validarSubRol(['DT0154']) && !$hayRegistrosAcademicosNotas){?>
                                                                        <li>
                                                                            <a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','cargas-estilo-notas-eliminar.php?idR=<?=base64_encode($resultado["catn_id"]);?>')">Eliminar</a>                                                                            
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
                                            <?php $botones = new botonesGuardar("cargas.php",false); ?>
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

<!-- Modal para edición rápida de categoría de notas -->
<div class="modal fade" id="modalEditarCategoriaNotas" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición rápida de categoría</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarCategoriaNotas" action="cargas-estilo-notas-actualizar.php" method="post">
				<div class="modal-body">
					<input type="hidden" name="id" id="edit_catn_id">
					
					<div class="form-group">
						<label>Código</label>
						<input type="text" class="form-control" id="edit_codigo_cat" name="codigo" readonly>
					</div>

					<div class="form-group">
						<label>Nombre de la categoría <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="edit_nombre_cat" name="nombre" required>
					</div>

					<div id="editCatError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i>
						<span id="editCatErrorMensaje"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-save"></i> Guardar cambios
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal para agregar rápida una categoría de notas -->
<div class="modal fade" id="modalAgregarCategoriaNotas" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white">
				<h4 class="modal-title"><i class="fa fa-plus-circle"></i> Agregar nueva categoría</h4>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<form id="formAgregarCategoriaNotas" action="cargas-estilo-notas-guardar.php" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label>Código <span class="text-muted">(opcional)</span></label>
						<input type="text" class="form-control" id="add_codigo_cat" name="codigo" placeholder="Ej: CN1, DESEMP, etc.">
					</div>

					<div class="form-group">
						<label>Nombre de la categoría <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="add_nombre_cat" name="nombre" placeholder="Ej: Desempeño Académico" required>
					</div>

					<div id="addCatError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i>
						<span id="addCatErrorMensaje"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-info">
						<i class="fa fa-save"></i> Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// Abrir modal de agregar categoría
	$('#btnAgregarCategoriaNotas').on('click', function() {
		$('#formAgregarCategoriaNotas')[0].reset();
		$('#addCatError').hide();
		$('#modalAgregarCategoriaNotas').modal('show');
	});

	// Abrir modal de edición rápida de categoría
	$(document).on('click', '.btn-editar-categoria-nota-modal', function() {
		var id     = $(this).data('id');
		var nombre = $(this).data('nombre');

		$('#edit_catn_id').val(id);
		$('#edit_codigo_cat').val(id);
		$('#edit_nombre_cat').val(nombre);

		$('#editCatError').hide();
		$('#modalEditarCategoriaNotas').modal('show');
	});

	// Validación simple: nombre obligatorio
	$('#formAgregarCategoriaNotas').on('submit', function(e) {
		var nombre = $('#add_nombre_cat').val().trim();
		if (!nombre) {
			e.preventDefault();
			$('#addCatErrorMensaje').text('El nombre de la categoría es obligatorio.');
			$('#addCatError').show();
		}
	});

	$('#formEditarCategoriaNotas').on('submit', function(e) {
		var nombre = $('#edit_nombre_cat').val().trim();
		if (!nombre) {
			e.preventDefault();
			$('#editCatErrorMensaje').text('El nombre de la categoría es obligatorio.');
			$('#editCatError').show();
		}
	});
});
</script>

</body>

</html>