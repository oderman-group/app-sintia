<?php
include("session.php");
$idPaginaInterna = 'DT0240';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Array para tipos de factura (igual que en movimientos-tbody.php)
$estadosCuentas = array("", "Fact. Venta", "Fact. Compra");

// Cargar usuarios que tienen facturas asociadas
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
$sqlUsuariosConFacturas = "SELECT DISTINCT uss.uss_id, uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2, pes.pes_nombre
	FROM ".BD_GENERAL.".usuarios uss
	INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_usuario = uss.uss_id
	INNER JOIN ".BD_ADMIN.".general_perfiles pes ON pes.pes_id = uss.uss_tipo
	WHERE uss.institucion = {$config['conf_id_institucion']} 
	AND uss.year = {$_SESSION["bd"]}
	AND fc.institucion = {$config['conf_id_institucion']} 
	AND fc.year = {$_SESSION["bd"]}
	ORDER BY uss.uss_apellido1, uss.uss_apellido2, uss.uss_nombre
	LIMIT 50";
$usuariosIniciales = mysqli_query($conexion, $sqlUsuariosConFacturas);
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
					<label for="filtro_usuario">Usuario/Cliente</label>
					<select class="form-control select2" id="filtro_usuario" style="width: 100%;">
						<option value="">Todos los usuarios</option>
						<?php
						if ($usuariosIniciales) {
							while($usuario = mysqli_fetch_array($usuariosIniciales, MYSQLI_BOTH)){
								$nombreUsuario = UsuariosPadre::nombreCompletoDelUsuario($usuario) . " - " . $usuario["pes_nombre"];
								echo '<option value="' . htmlspecialchars($usuario["uss_id"]) . '">' . htmlspecialchars($nombreUsuario) . '</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_metodo_pago">Método de Pago</label>
					<select class="form-control select2" id="filtro_metodo_pago" name="metodo_pago" style="width: 100%;">
						<option value="">Todos los métodos</option>
						<option value="EFECTIVO">Efectivo</option>
						<option value="CHEQUE">Cheque</option>
						<option value="T_DEBITO">T. Débito</option>
						<option value="T_CREDITO">T. Crédito</option>
						<option value="TRANSFERENCIA">Transferencia</option>
						<option value="OTROS">Otros</option>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="filtro_cuenta_bancaria">Cuenta Bancaria</label>
					<select class="form-control select2" id="filtro_cuenta_bancaria" name="cuenta_bancaria" style="width: 100%;">
						<option value="">Todas las cuentas</option>
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
		
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label>
						<input type="checkbox" name="mostrarArqueo" value="1" id="mostrarArqueo">
						Mostrar arqueo de caja agrupado por método de pago y cuenta bancaria
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
	$('#filtro_tipo, #filtro_estado, #filtro_metodo_pago').select2({
		dropdownParent: $('#ModalCentralizado .modal-content'),
		width: '100%',
		minimumResultsForSearch: -1
	});
	
	// Cargar cuentas bancarias cuando cambia el método de pago
	$('#filtro_metodo_pago').on('change', function() {
		const metodoPago = $(this).val();
		$('#filtro_cuenta_bancaria').empty().append('<option value="">Todas las cuentas</option>');
		
		if (metodoPago) {
			$.ajax({
				url: 'ajax-cargar-cuentas-bancarias.php',
				type: 'POST',
				data: { metodo_pago: metodoPago },
				dataType: 'json',
				success: function(response) {
					if (response.success && response.cuentas) {
						$.each(response.cuentas, function(index, cuenta) {
							$('#filtro_cuenta_bancaria').append(
								$('<option></option>')
									.attr('value', cuenta.id)
									.text(cuenta.nombre)
							);
						});
					}
				}
			});
		}
	});
	
	// Inicializar Select2 para cuenta bancaria
	$('#filtro_cuenta_bancaria').select2({
		dropdownParent: $('#ModalCentralizado .modal-content'),
		width: '100%'
	});
	
	// Inicializar Select2 para usuario con búsqueda AJAX
	$('#filtro_usuario').select2({
		dropdownParent: $('#ModalCentralizado .modal-content'),
		width: '100%',
		placeholder: 'Buscar usuario/cliente...',
		allowClear: true,
		ajax: {
			type: 'GET',
			url: '../compartido/ajax-listar-usuarios.php',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					term: params.term || ''
				};
			},
			processResults: function(data) {
				if (!data) {
					data = [];
				}
				return {
					results: $.map(data, function(item) {
						return {
							id: item.value,
							text: item.label
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 0
	});
	
	// Asegurar que el valor se guarde en el select cuando se selecciona
	$('#filtro_usuario').on('select2:select', function (e) {
		var data = e.params.data;
		$(this).val(data.id).trigger('change');
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
	
	// Codificar el usuario en base64 si está seleccionado (igual que tipo y estadoFil)
	// Obtener el valor de Select2 - cuando se usa AJAX, el valor se almacena en el select
	var usuarioSeleccionado = $('#filtro_usuario').val();
	
	// Si no hay valor, intentar obtenerlo del select2 data
	if (!usuarioSeleccionado || usuarioSeleccionado === '' || usuarioSeleccionado === null) {
		var select2Data = $('#filtro_usuario').select2('data');
		if (select2Data && select2Data.id) {
			usuarioSeleccionado = select2Data.id;
		}
	}
	
	if (usuarioSeleccionado && usuarioSeleccionado !== '' && usuarioSeleccionado !== null && usuarioSeleccionado !== undefined) {
		// Crear un input hidden para el usuario codificado
		var usuarioCodificado = btoa(usuarioSeleccionado.toString());
		// Remover cualquier input hidden previo de usuario
		$(form).find('input[name="usuario"]').remove();
		var inputUsuario = $('<input>').attr({
			type: 'hidden',
			name: 'usuario',
			value: usuarioCodificado
		});
		// Asegurar que el input se agregue al formulario antes de enviarlo
		$(form).append(inputUsuario);
	}
	
	// Cerrar modal
	$('#ModalCentralizado').modal('hide');
	
	// Enviar formulario
	form.submit();
}
</script>

