<?php
include("session.php");
$idPaginaInterna = 'DT0240';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Array para tipos de factura (igual que en movimientos-tbody.php)
$estadosCuentas = array("", "Fact. Venta", "Fact. Compra");
?>
<div class="modal-header bg-primary text-white">
	<h5 class="modal-title">
		<i class="fas fa-filter"></i> Filtros para Informe de Movimientos Financieros
	</h5>
	<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form id="formFiltrosMovimientos" method="get" action="../compartido/reporte-movimientos.php" target="_blank">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_desde">Fecha Desde <span class="text-danger">*</span></label>
					<input type="date" class="form-control" id="filtro_desde" name="desde" required>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_hasta">Fecha Hasta <span class="text-danger">*</span></label>
					<input type="date" class="form-control" id="filtro_hasta" name="hasta" required>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_tipo">Tipo de Movimiento</label>
					<select class="form-control select2" id="filtro_tipo" name="tipo" style="width: 100%;">
						<option value="">Todos los tipos</option>
						<option value="<?= base64_encode(1); ?>">Fact. Venta</option>
						<option value="<?= base64_encode(2); ?>">Fact. Compra</option>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_estado">Estado</label>
					<select class="form-control select2" id="filtro_estado" name="estadoFil" style="width: 100%;">
						<option value="">Todos los estados</option>
						<option value="<?= base64_encode(POR_COBRAR); ?>">Por Cobrar</option>
						<option value="<?= base64_encode(COBRADA); ?>">Cobrada</option>
					</select>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label>
						<input type="checkbox" name="mostrarAnuladas" value="1">
						Mostrar facturas anuladas
					</label>
				</div>
			</div>
		</div>
		
		<div class="alert alert-info">
			<i class="fas fa-info-circle"></i> <strong>Nota:</strong> Los campos de fecha son obligatorios. El informe se generará con los filtros seleccionados.
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">
		<i class="fas fa-times"></i> Cancelar
	</button>
	<button type="button" class="btn btn-primary" onclick="generarReporteMovimientos()">
		<i class="fas fa-file-pdf"></i> Generar Informe
	</button>
</div>

<script>
$(document).ready(function() {
	// Inicializar Select2
	$('#filtro_tipo, #filtro_estado').select2({
		dropdownParent: $('#ModalCentralizado .modal-content'),
		width: '100%',
		minimumResultsForSearch: -1
	});
	
	// Establecer fechas por defecto (año actual)
	var hoy = new Date();
	var primerDia = new Date(hoy.getFullYear(), 0, 1);
	var ultimoDia = new Date(hoy.getFullYear(), 11, 31);
	
	$('#filtro_desde').val(primerDia.toISOString().split('T')[0]);
	$('#filtro_hasta').val(ultimoDia.toISOString().split('T')[0]);
});

function generarReporteMovimientos() {
	var form = document.getElementById('formFiltrosMovimientos');
	
	// Validar formulario
	if (!form.checkValidity()) {
		form.reportValidity();
		return;
	}
	
	// Validar que la fecha desde sea menor o igual a la fecha hasta
	var fechaDesde = new Date($('#filtro_desde').val());
	var fechaHasta = new Date($('#filtro_hasta').val());
	
	if (fechaDesde > fechaHasta) {
		alert('La fecha "Desde" debe ser menor o igual a la fecha "Hasta"');
		$('#filtro_desde').focus();
		return;
	}
	
	// Cerrar modal
	$('#ModalCentralizado').modal('hide');
	
	// Enviar formulario
	form.submit();
}
</script>

