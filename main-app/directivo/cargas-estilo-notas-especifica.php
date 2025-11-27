<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0045';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/App/Academico/Calificacion.php");

Utilidades::validarParametros($_GET,["id"]);

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
                                                            <button type="button" id="btnAgregarNotaEspecifica" class="btn deepPink-bgcolor">
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
                                                        <th>Nota desde</th>
                                                        <th>Nota hasta</th>
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0046','DT0155'])){?>
														    <th>Acciones</th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$contReg = 1;
                                                    $consulta = Boletin::listarTipoDeNotas(base64_decode($_GET["id"]));
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
                                                                    <button type="button" class="btn btn-primary">Acciones</button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0046'])){?>
                                                                        <li>
																			<a href="javascript:void(0);" 
																			   class="btn-editar-nota-especifica-modal"
																			   data-id="<?=$resultado["notip_id"];?>"
																			   data-codigo="<?=$resultado["notip_id"];?>"
																			   data-nombre="<?=htmlspecialchars($resultado["notip_nombre"], ENT_QUOTES, 'UTF-8');?>"
																			   data-desde="<?=$resultado["notip_desde"];?>"
																			   data-hasta="<?=$resultado["notip_hasta"];?>"
																			>
																				<?=$frases[165][$datosUsuarioActual['uss_idioma']];?> rápida
																			</a>
																		</li>
                                                                        <li>
																			<a href="cargas-estilo-notas-especifica-editar.php?id=<?=base64_encode($resultado["notip_id"]);?>&idCN=<?=$_GET["id"]?>">
																				<?=$frases[165][$datosUsuarioActual['uss_idioma']];?> completa
																			</a>
																		</li>
																		<?php } if(Modulos::validarSubRol(['DT0155']) && !$hayRegistrosAcademicosNotas){?>
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
                                            <?php $botones = new botonesGuardar("cargas-estilo-notas.php",false); ?>
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

<!-- Modal para edición rápida de nota específica -->
<div class="modal fade" id="modalEditarNotaEspecifica" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición rápida de rango</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarNotaEspecifica" action="cargas-estilo-notas-especifica-actualizar.php" method="post">
				<div class="modal-body">
					<input type="hidden" name="id" id="edit_notip_id">
					<input type="hidden" name="categoria" value="<?=$_GET["id"];?>">
					
					<div class="form-group">
						<label>Código</label>
						<input type="text" class="form-control" id="edit_codigo" name="codigo" readonly>
					</div>

					<div class="form-group">
						<label>Nombre / Desempeño <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="edit_nombre" name="nombre" required>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Nota desde <span class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0" class="form-control" id="edit_desde" name="desde" required>
						</div>
						<div class="form-group col-md-6">
							<label>Nota hasta <span class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0" class="form-control" id="edit_hasta" name="hasta" required>
						</div>
					</div>

					<div id="editNotaError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i>
						<span id="editNotaErrorMensaje"></span>
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

<!-- Modal para agregar rápido una nota específica -->
<div class="modal fade" id="modalAgregarNotaEspecifica" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white">
				<h4 class="modal-title"><i class="fa fa-plus-circle"></i> Agregar nuevo rango de nota</h4>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<form id="formAgregarNotaEspecifica" action="cargas-estilo-notas-especifica-guardar.php" method="post">
				<div class="modal-body">
					<input type="hidden" name="categoria" value="<?=$_GET["id"];?>">

					<div class="form-group">
						<label>Código <span class="text-muted">(opcional)</span></label>
						<input type="text" class="form-control" id="add_codigo" name="codigo" placeholder="Ej: A, B, C...">
					</div>

					<div class="form-group">
						<label>Nombre / Desempeño <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="add_nombre" name="nombre" placeholder="Ej: Excelente, Sobresaliente..." required>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Nota desde <span class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0" class="form-control" id="add_desde" name="desde" required>
						</div>
						<div class="form-group col-md-6">
							<label>Nota hasta <span class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0" class="form-control" id="add_hasta" name="hasta" required>
						</div>
					</div>

					<div id="addNotaError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i>
						<span id="addNotaErrorMensaje"></span>
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
	// Abrir modal de agregar
	$('#btnAgregarNotaEspecifica').on('click', function() {
		// Limpiar formulario
		$('#formAgregarNotaEspecifica')[0].reset();
		$('#addNotaError').hide();
		$('#modalAgregarNotaEspecifica').modal('show');
	});

	// Abrir modal de edición rápida
	$(document).on('click', '.btn-editar-nota-especifica-modal', function() {
		var id     = $(this).data('id');
		var codigo = $(this).data('codigo');
		var nombre = $(this).data('nombre');
		var desde  = $(this).data('desde');
		var hasta  = $(this).data('hasta');

		$('#edit_notip_id').val(id);
		$('#edit_codigo').val(codigo);
		$('#edit_nombre').val(nombre);
		$('#edit_desde').val(desde);
		$('#edit_hasta').val(hasta);

		$('#editNotaError').hide();
		$('#modalEditarNotaEspecifica').modal('show');
	});

	// Validación básica en cliente para evitar rangos invertidos
	function validarRango(desde, hasta) {
		if (desde === '' || hasta === '') return true;
		var d = parseFloat(desde);
		var h = parseFloat(hasta);
		return !isNaN(d) && !isNaN(h) && d <= h;
	}

	$('#formAgregarNotaEspecifica').on('submit', function(e) {
		var desde = $('#add_desde').val();
		var hasta = $('#add_hasta').val();
		if (!validarRango(desde, hasta)) {
			e.preventDefault();
			$('#addNotaErrorMensaje').text('La nota "desde" no puede ser mayor que la nota "hasta".');
			$('#addNotaError').show();
		}
	});

	$('#formEditarNotaEspecifica').on('submit', function(e) {
		var desde = $('#edit_desde').val();
		var hasta = $('#edit_hasta').val();
		if (!validarRango(desde, hasta)) {
			e.preventDefault();
			$('#editNotaErrorMensaje').text('La nota "desde" no puede ser mayor que la nota "hasta".');
			$('#editNotaError').show();
		}
	});
});
</script>

</body>

</html>