<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0195';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
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
                                <div class="page-title"><?=$frases[254][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">								
								<div class="col-md-12">								
									<?php
                                        include("../../config-general/mensajes-informativos.php");									
                                        include ("includes/grupos-listar.php");
                                    ?>						
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

<!-- Modal para edición rápida de grupo -->
<div class="modal fade" id="modalEditarGrupo" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición Rápida de Grupo</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarGrupo" action="grupos-actualizar.php" method="post">
				<div class="modal-body">
					<div id="grupoLoader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="grupoFormulario" style="display:none;">
						<input type="hidden" id="edit_id_grupo" name="id">
						
						<div class="form-group">
							<label>Código <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="edit_codigo_grupo" name="codigoG" required>
						</div>
						
						<div class="form-group">
							<label>Nombre Grupo <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="edit_nombre_grupo" name="nombreG" required>
						</div>
					</div>
					
					<div id="grupoError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensajeGrupo"></span>
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
	$(document).on('click', '.btn-editar-grupo-modal', function() {
		var grupoId = $(this).data('grupo-id');
		
		$('#grupoLoader').show();
		$('#grupoFormulario').hide();
		$('#grupoError').hide();
		$('#modalEditarGrupo').modal('show');
		
		$.ajax({
			url: 'ajax-obtener-datos-grupo.php',
			type: 'POST',
			data: { grupo_id: grupoId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var grupo = response.grupo;
					$('#edit_id_grupo').val(grupo.gru_id);
					$('#edit_codigo_grupo').val(grupo.gru_codigo);
					$('#edit_nombre_grupo').val(grupo.gru_nombre);
					
					$('#grupoLoader').hide();
					$('#grupoFormulario').show();
				} else {
					$('#grupoLoader').hide();
					$('#errorMensajeGrupo').text(response.message);
					$('#grupoError').show();
				}
			},
			error: function() {
				$('#grupoLoader').hide();
				$('#errorMensajeGrupo').text('Error de conexión');
				$('#grupoError').show();
			}
		});
	});
	
	$('#formEditarGrupo').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function() {
				$.toast({
					heading: 'Éxito',
					text: 'Grupo actualizado correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
				$('#modalEditarGrupo').modal('hide');
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