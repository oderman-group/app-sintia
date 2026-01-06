<?php
include("session.php");
$idPaginaInterna = 'DT0240';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once(ROOT_PATH."/main-app/class/MediosPago.php");

// Cargar cuentas bancarias activas
$consultaCuentas = mysqli_query($conexion, "SELECT cba_id, cba_nombre, cba_numero_cuenta, cba_banco 
    FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
    WHERE cba_activa = 1 
    AND institucion = {$config['conf_id_institucion']} 
    AND year = {$_SESSION["bd"]}
    ORDER BY cba_nombre");
?>
<div class="modal-header bg-primary text-white">
	<h5 class="modal-title">
		<i class="fas fa-university"></i> Filtros para Informe de Movimientos por Cuenta Bancaria
	</h5>
	<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form id="formFiltrosCuentasBancarias" method="get" action="../compartido/reporte-cuentas-bancarias.php" target="_blank">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_desde_cb">Fecha Desde <span class="text-danger">*</span></label>
					<input type="date" class="form-control" id="filtro_desde_cb" name="desde" required>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_hasta_cb">Fecha Hasta <span class="text-danger">*</span></label>
					<input type="date" class="form-control" id="filtro_hasta_cb" name="hasta" required>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_metodo_pago_cb">Método de Pago</label>
					<select class="form-control" id="filtro_metodo_pago_cb" name="metodo_pago" style="width: 100%;">
						<option value="">Todos los métodos</option>
						<?php
						$mediosPago = MediosPago::obtenerMediosPago();
						foreach ($mediosPago as $codigo => $nombre) {
							echo '<option value="'.htmlspecialchars($codigo, ENT_QUOTES).'">'.htmlspecialchars($nombre, ENT_QUOTES).'</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_cuenta_bancaria_cb">Cuenta Bancaria</label>
					<select class="form-control" id="filtro_cuenta_bancaria_cb" name="cuenta_bancaria" style="width: 100%;">
						<option value="">Todas las cuentas</option>
						<?php
						if ($consultaCuentas) {
							while($cuenta = mysqli_fetch_array($consultaCuentas, MYSQLI_BOTH)){
								$nombreCuenta = htmlspecialchars($cuenta['cba_nombre']);
								if (!empty($cuenta['cba_numero_cuenta'])) {
									$nombreCuenta .= " (" . htmlspecialchars($cuenta['cba_numero_cuenta']) . ")";
								}
								if (!empty($cuenta['cba_banco'])) {
									$nombreCuenta .= " - " . htmlspecialchars($cuenta['cba_banco']);
								}
								echo '<option value="' . htmlspecialchars($cuenta['cba_id']) . '">' . $nombreCuenta . '</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
		</div>
		
		<div class="alert alert-info">
			<i class="fas fa-info-circle"></i> <strong>Nota:</strong> Los campos de fecha son obligatorios. El informe mostrará ingresos (abonos) y egresos (facturas de compra) agrupados por cuenta bancaria.
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">
		<i class="fas fa-times"></i> Cancelar
	</button>
	<button type="button" class="btn btn-primary" onclick="generarReporteCuentasBancarias()">
		<i class="fas fa-file-pdf"></i> Generar Informe
	</button>
</div>

<script>
$(document).ready(function() {
	// Establecer fechas por defecto (año actual)
	var hoy = new Date();
	var primerDia = new Date(hoy.getFullYear(), 0, 1);
	var ultimoDia = new Date(hoy.getFullYear(), 11, 31);
	
	$('#filtro_desde_cb').val(primerDia.toISOString().split('T')[0]);
	$('#filtro_hasta_cb').val(ultimoDia.toISOString().split('T')[0]);
});

function generarReporteCuentasBancarias() {
	var form = document.getElementById('formFiltrosCuentasBancarias');
	
	// Validar formulario
	if (!form.checkValidity()) {
		form.reportValidity();
		return;
	}
	
	// Validar que la fecha desde sea menor o igual a la fecha hasta
	var fechaDesde = new Date($('#filtro_desde_cb').val());
	var fechaHasta = new Date($('#filtro_hasta_cb').val());
	
	if (fechaDesde > fechaHasta) {
		alert('La fecha "Desde" debe ser menor o igual a la fecha "Hasta"');
		$('#filtro_desde_cb').focus();
		return;
	}
	
	// Cerrar modal
	$('#ModalCentralizado').modal('hide');
	
	// Enviar formulario
	form.submit();
}
</script>

