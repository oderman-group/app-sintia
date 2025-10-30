<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0081'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php"); ?>
<?php
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$year = $agnoBD;
$Plataforma = new Plataforma;

// Obtener curso y grupo (desde POST o GET para permitir cambios)
$cursoId = isset($_POST["curso"]) ? $_POST["curso"] : (isset($_GET["curso"]) ? base64_decode($_GET["curso"]) : '');
$grupoId = isset($_POST["grupo"]) ? $_POST["grupo"] : (isset($_GET["grupo"]) ? base64_decode($_GET["grupo"]) : '');

$consultaCurso = null;
$curso = null;
$consultaGrupo = null;
$grupo = null;

if (!empty($cursoId)) {
	$consultaCurso = Grados::obtenerDatosGrados($cursoId);
	$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
}

if (!empty($grupoId)) {
	$consultaGrupo = Grupos::obtenerDatosGrupos($grupoId);
	$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);
}

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
	$disabledPermiso = "disabled";
}
?>

<!-- DataTables CSS -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<!-- Toast CSS -->
<link rel="stylesheet" href="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.css">

<style type="text/css">
	body {
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		margin: 0;
		padding: 20px;
		min-height: 100vh;
	}
	
	/* ==================== CONTENEDOR PRINCIPAL ==================== */
	.container-consolidado {
		max-width: 98%;
		margin: 0 auto;
		background: white;
		border-radius: 20px;
		box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
		overflow: hidden;
	}
	
	/* ==================== HEADER MODERNO ==================== */
	.header-consolidado {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		padding: 30px 40px;
		color: white;
		position: relative;
		overflow: hidden;
	}
	
	.header-consolidado::before {
		content: '';
		position: absolute;
		top: -50%;
		right: -5%;
		width: 300px;
		height: 300px;
		background: rgba(255, 255, 255, 0.1);
		border-radius: 50%;
	}
	
	.header-consolidado h2 {
		margin: 0 0 10px 0;
		font-size: 32px;
		font-weight: 700;
		display: flex;
		align-items: center;
		gap: 15px;
		position: relative;
		z-index: 1;
	}
	
	.header-consolidado .subtitle {
		font-size: 15px;
		opacity: 0.95;
		margin: 0;
		position: relative;
		z-index: 1;
	}
	
	/* ==================== SELECTORES MODERNOS ==================== */
	.selectores-container {
		background: #f8fafc;
		padding: 25px 40px;
		border-bottom: 3px solid #e2e8f0;
	}
	
	.selector-group {
		margin-bottom: 0;
	}
	
	.selector-group label {
		display: block;
		font-weight: 600;
		color: #4a5568;
		margin-bottom: 10px;
		font-size: 15px;
		display: flex;
		align-items: center;
		gap: 8px;
	}
	
	/* Select nativo personalizado */
	.selector-group select.form-control {
		border: 2px solid #e2e8f0;
		border-radius: 10px;
		height: 55px;
		padding: 12px 18px;
		font-size: 15px;
		font-weight: 500;
		color: #2d3748;
		background: white;
		cursor: pointer;
		transition: all 0.3s ease;
		appearance: none;
		-webkit-appearance: none;
		-moz-appearance: none;
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23667eea' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
		background-repeat: no-repeat;
		background-position: right 15px center;
		padding-right: 45px;
	}
	
	.selector-group select.form-control:hover {
		border-color: #667eea;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
	}
	
	.selector-group select.form-control:focus {
		border-color: #667eea;
		outline: none;
		box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
	}
	
	.selector-group select.form-control option {
		padding: 12px;
		font-size: 15px;
		font-weight: 500;
	}
	
	.btn-aplicar {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		border: none;
		padding: 12px 30px;
		border-radius: 10px;
		font-weight: 600;
		font-size: 16px;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
		transition: all 0.3s ease;
		width: 100%;
		height: 55px;
		margin-top: 27px;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 10px;
	}
	
	.btn-aplicar:hover:not(:disabled) {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
		color: white;
	}
	
	.btn-aplicar:disabled {
		opacity: 0.5;
		cursor: not-allowed;
	}
	
	.badge-info-curso {
		background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
		padding: 8px 15px;
		border-radius: 8px;
		font-size: 14px;
		font-weight: 600;
		color: white;
		display: inline-block;
		margin-right: 10px;
	}
	
	.badge-info-grupo {
		background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
		padding: 8px 15px;
		border-radius: 8px;
		font-size: 14px;
		font-weight: 600;
		color: white;
		display: inline-block;
	}
	
	/* ==================== BOTONES DE ACCIÓN ==================== */
	.acciones-container {
		padding: 20px 40px;
		background: white;
		border-bottom: 1px solid #e2e8f0;
		display: flex;
		gap: 15px;
		align-items: center;
	}
	
	.btn-action {
		padding: 12px 25px;
		border-radius: 10px;
		font-weight: 600;
		font-size: 14px;
		transition: all 0.3s ease;
		border: none;
		display: inline-flex;
		align-items: center;
		gap: 10px;
		text-decoration: none;
	}
	
	.btn-informe {
		background: linear-gradient(135deg, #10b981 0%, #059669 100%);
		color: white;
		box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
	}
	
	.btn-informe:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
		color: white;
		text-decoration: none;
	}
	
	.btn-info-toggle {
		background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
		color: white;
		box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
	}
	
	.btn-info-toggle:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 18px rgba(245, 158, 11, 0.4);
		color: white;
	}
	
	/* ==================== ALERT INFO ==================== */
	.alert-info-modern {
		background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
		border-left: 5px solid #f59e0b;
		border-radius: 12px;
		padding: 25px;
		margin: 20px 40px;
		box-shadow: 0 2px 10px rgba(245, 158, 11, 0.15);
	}
	
	.alert-info-modern h4 {
		color: #92400e;
		font-weight: 700;
		margin: 0 0 15px 0;
		display: flex;
		align-items: center;
		gap: 10px;
		font-size: 18px;
	}
	
	.alert-info-modern p {
		color: #78350f;
		margin: 10px 0;
		line-height: 1.7;
		font-size: 14px;
	}
	
	.alert-info-modern strong {
		color: #92400e;
		font-weight: 700;
	}
	
	.alert-info-modern kbd {
		background: #92400e;
		color: white;
		padding: 3px 8px;
		border-radius: 4px;
		font-size: 12px;
		font-weight: 600;
	}
	
	/* ==================== TABLA MODERNA ==================== */
	.table-container {
		height: calc(100vh - 380px);
		min-height: 500px;
		overflow: auto;
		position: relative;
		padding: 0 40px 40px 40px;
	}
	
	.scrollable-table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
		font-size: 13px;
	}
	
	/* Encabezado fijo */
	.scrollable-table thead {
		position: -webkit-sticky;
		position: sticky;
		top: 0;
		z-index: 10;
	}
	
	.scrollable-table thead th {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		font-weight: 600;
		padding: 12px 8px;
		text-align: center;
		border: 1px solid rgba(255,255,255,0.2);
		font-size: 11px;
		position: sticky;
		top: 0;
		z-index: 10;
	}
	
	/* Primera fila del encabezado (materias) */
	.scrollable-table thead tr:first-child th {
		background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
		font-size: 11px;
		padding: 12px 6px;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	
	/* Segunda fila del encabezado (periodos) */
	.scrollable-table thead tr:nth-child(2) th {
		background: #f7fafc;
		color: #2d3748;
		font-size: 12px;
		font-weight: 700;
		padding: 10px 6px;
		border: 1px solid #e2e8f0;
	}
	
	/* Columnas fijas - Documento */
	.css_doc {
		position: sticky !important;
		left: 0;
		z-index: 11 !important;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
		box-shadow: 3px 0 8px rgba(0,0,0,0.15);
	}
	
	/* Columnas fijas - Estudiante */
	.css_nombre {
		position: sticky !important;
		left: 100px;
		z-index: 11 !important;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
		box-shadow: 3px 0 8px rgba(0,0,0,0.15);
		min-width: 250px;
	}
	
	/* Columna fija - Promedio */
	.css_prom {
		position: sticky;
		right: 0;
		z-index: 11;
		background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
		box-shadow: -3px 0 8px rgba(0,0,0,0.15);
		font-weight: 700 !important;
		font-size: 13px !important;
	}
	
	/* Celdas del cuerpo - Documento */
	.scrollable-table tbody td:nth-child(1) {
		position: sticky;
		left: 0;
		z-index: 5;
		background-color: #f7fafc;
		font-weight: 600;
		color: #2d3748;
		border-right: 2px solid #e2e8f0;
		box-shadow: 3px 0 5px rgba(0,0,0,0.08);
		font-size: 12px;
	}
	
	/* Celdas del cuerpo - Estudiante */
	.scrollable-table tbody td:nth-child(2) {
		position: sticky;
		left: 100px;
		z-index: 5;
		background-color: #f7fafc;
		font-weight: 600;
		color: #2d3748;
		border-right: 2px solid #e2e8f0;
		box-shadow: 3px 0 5px rgba(0,0,0,0.08);
		text-align: left;
		padding-left: 12px;
		font-size: 13px;
		min-width: 250px;
	}
	
	/* Celdas del cuerpo - Promedio */
	.scrollable-table tbody td:last-child {
		position: sticky;
		right: 0;
		z-index: 5;
		background-color: #fffbeb;
		font-weight: 700;
		font-size: 16px;
		border-left: 3px solid #f59e0b;
		box-shadow: -3px 0 5px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
	}
	
	.scrollable-table tbody td:last-child.recalculando {
		animation: pulse-promedio 0.6s ease-in-out;
	}
	
	@keyframes pulse-promedio {
		0%, 100% { 
			transform: scale(1);
			background: #fffbeb;
		}
		50% { 
			transform: scale(1.2);
			background: #fef3c7;
			box-shadow: -3px 0 15px rgba(245, 158, 11, 0.3), 0 0 20px rgba(245, 158, 11, 0.2);
		}
	}
	
	/* Filas del cuerpo */
	.scrollable-table tbody tr {
		transition: background-color 0.2s ease;
	}
	
	.scrollable-table tbody tr:hover {
		background-color: #edf2f7;
	}
	
	.scrollable-table tbody td {
		padding: 10px 8px;
		text-align: center;
		border: 1px solid #e2e8f0;
		vertical-align: middle;
	}
	
	/* ==================== INPUTS DE NOTAS ==================== */
	.input-nota {
		width: 50px;
		height: 38px;
		text-align: center;
		border: 2px solid #cbd5e0;
		border-radius: 8px;
		font-weight: 600;
		font-size: 14px;
		transition: all 0.3s ease;
		padding: 5px;
		background: white;
	}
	
	.input-nota:hover:not(:disabled) {
		border-color: #667eea;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
	}
	
	.input-nota:focus {
		outline: none;
		border-color: #667eea;
		box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.25);
		transform: scale(1.08);
		z-index: 2;
		position: relative;
	}
	
	.input-nota:disabled {
		background-color: #f7fafc;
		cursor: not-allowed;
		opacity: 0.65;
	}
	
	.input-nota.guardando {
		border-color: #fbbf24;
		animation: pulse-border 1.2s ease-in-out infinite;
	}
	
	@keyframes pulse-border {
		0%, 100% { 
			border-color: #fbbf24;
			box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
		}
		50% { 
			border-color: #f59e0b;
			box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.3);
		}
	}
	
	/* Input definitiva de materia */
	.input-definitiva {
		background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
		font-weight: 700;
		border: 2px solid #fbbf24;
		cursor: default;
		transition: all 0.3s ease;
	}
	
	.input-definitiva.recalculando {
		animation: pulse-definitiva 0.6s ease-in-out;
	}
	
	@keyframes pulse-definitiva {
		0%, 100% { 
			transform: scale(1);
			background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
		}
		50% { 
			transform: scale(1.15);
			background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
			box-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
		}
	}
	
	/* Tipo de nota */
	.tipo-nota {
		font-size: 9px;
		margin-top: 4px;
		display: block;
		font-weight: 600;
		line-height: 1.2;
	}
	
	.tipo-normal { color: #3b82f6; }
	.tipo-recuperacion { color: #ef4444; }
	.tipo-directivo { color: #8b5cf6; }
	
	/* ==================== OVERLAY DE GUARDADO ==================== */
	#overlay-guardando {
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(30, 41, 59, 0.85);
		z-index: 99999;
		backdrop-filter: blur(6px);
	}
	
	.overlay-content {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background: white;
		padding: 45px 50px;
		border-radius: 20px;
		text-align: center;
		box-shadow: 0 20px 60px rgba(0,0,0,0.4);
		min-width: 320px;
	}
	
	.overlay-content .spinner {
		width: 70px;
		height: 70px;
		border: 5px solid #e2e8f0;
		border-top-color: #667eea;
		border-radius: 50%;
		margin: 0 auto 25px;
		animation: spin 1s linear infinite;
	}
	
	@keyframes spin {
		to { transform: rotate(360deg); }
	}
	
	.overlay-content h3 {
		color: #2d3748;
		margin: 0 0 10px 0;
		font-size: 22px;
		font-weight: 700;
	}
	
	.overlay-content p {
		color: #718096;
		margin: 0;
		font-size: 15px;
	}
	
	/* ==================== MENSAJE VACÍO ==================== */
	.mensaje-vacio {
		padding: 80px 40px;
		text-align: center;
		background: white;
	}
	
	.mensaje-vacio .icono {
		font-size: 100px;
		color: #cbd5e0;
		margin-bottom: 25px;
	}
	
	.mensaje-vacio h3 {
		color: #4a5568;
		font-weight: 600;
		margin-bottom: 15px;
		font-size: 24px;
	}
	
	.mensaje-vacio p {
		color: #718096;
		font-size: 16px;
		line-height: 1.6;
	}
	
	/* ==================== DATATABLE PERSONALIZADO ==================== */
	.dataTables_wrapper .dataTables_length,
	.dataTables_wrapper .dataTables_filter {
		margin-bottom: 15px;
	}
	
	.dataTables_wrapper .dataTables_filter input {
		border: 2px solid #e2e8f0;
		border-radius: 8px;
		padding: 8px 15px;
		margin-left: 10px;
		transition: all 0.3s ease;
	}
	
	.dataTables_wrapper .dataTables_filter input:focus {
		border-color: #667eea;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
		outline: none;
	}
	
	.dataTables_wrapper .dataTables_length select {
		border: 2px solid #e2e8f0;
		border-radius: 8px;
		padding: 6px 12px;
		margin: 0 10px;
	}
	
	.dataTables_wrapper .dataTables_paginate .paginate_button {
		border-radius: 6px;
		padding: 6px 12px;
		margin: 0 3px;
	}
	
	.dataTables_wrapper .dataTables_paginate .paginate_button.current {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		border-color: #667eea;
		color: white !important;
	}
	
	.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
		background: #667eea;
		border-color: #667eea;
		color: white !important;
	}
	
	/* ==================== RESPONSIVE ==================== */
	@media (max-width: 768px) {
		body {
			padding: 10px;
		}
		
		.container-consolidado {
			max-width: 100%;
		}
		
		.header-consolidado {
			padding: 20px;
		}
		
		.header-consolidado h2 {
			font-size: 24px;
		}
		
		.selectores-container,
		.acciones-container {
			padding: 15px 20px;
		}
		
		.table-container {
			padding: 0 20px 20px 20px;
			height: calc(100vh - 450px);
		}
	}
</style>

<!-- Bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!-- DataTables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>

<!-- Toast -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>

<script type="text/javascript">
	// Función original de guardado con recálculo en tiempo real
	function def(enviada) {
		var nota = enviada.value;
		var codEst = enviada.id;
		var carga = enviada.name;
		var per = enviada.alt;
		
		if (alertValidarNota(nota)) {
			return false;
		}
		
		// Mostrar overlay
		$('#overlay-guardando').fadeIn(200);
		
		// Agregar clase de guardando
		$(enviada).addClass('guardando');
		
		// Deshabilitar TODOS los inputs temporalmente
		$('.input-nota').prop('disabled', true);
		
		$('#resp').empty().hide().html("Esperando...").show(1);
		
		datos = "nota=" + (nota) +
			"&carga=" + (carga) +
			"&codEst=" + (codEst) +
			"&per=" + (per);
			
		$.ajax({
			type: "POST",
			url: "ajax-periodos-registrar.php",
			data: datos,
			success: function(data) {
				// Ocultar overlay
				$('#overlay-guardando').fadeOut(200);
				
				// Quitar clase guardando
				$(enviada).removeClass('guardando');
				
				// Habilitar inputs
				$('.input-nota').prop('disabled', false);
				
				// Mostrar respuesta original
				$('#resp').empty().hide().html(data).show(1);
				
				// Toast de éxito
				var materiaName = $(enviada).attr('title').split(' - ')[0];
				$.toast({
					heading: '✅ Nota Guardada',
					text: materiaName + ' - Periodo ' + per + ': ' + nota,
					position: 'top-right',
					loaderBg: '#10b981',
					icon: 'success',
					hideAfter: 2500,
					stack: 5
				});
				
				// RECALCULAR DEFINITIVA Y PROMEDIO EN TIEMPO REAL
				recalcularDefinitivasYPromedio(enviada, carga, codEst);
			},
			error: function(xhr, status, error) {
				// Ocultar overlay
				$('#overlay-guardando').fadeOut(200);
				
				// Quitar clase guardando
				$(enviada).removeClass('guardando');
				
				// Habilitar inputs
				$('.input-nota').prop('disabled', false);
				
				// Toast de error
				$.toast({
					heading: '❌ Error al Guardar',
					text: 'No se pudo guardar la nota. Intenta nuevamente.',
					position: 'top-right',
					loaderBg: '#ef4444',
					icon: 'error',
					hideAfter: 4000
				});
			}
		});
	}
	
	// ==========================================
	// RECALCULAR DEFINITIVA DE MATERIA Y PROMEDIO GENERAL
	// ==========================================
	function recalcularDefinitivasYPromedio(inputModificado, cargaId, estudianteId) {
		console.log('Recalculando definitivas para carga:', cargaId, 'estudiante:', estudianteId);
		
		// Obtener la fila del estudiante
		var fila = $(inputModificado).closest('tr');
		var numPeriodos = <?=$config[19]?>;
		var notaMinAprobar = <?=$config[5]?>;
		var colorAprobado = '<?=$config[7]?>';
		var colorReprobado = '<?=$config[6]?>';
		
		// Buscar todos los inputs de periodos de ESTA MATERIA para ESTE ESTUDIANTE
		var inputsPeriodos = fila.find('input[name="' + cargaId + '"][id="' + estudianteId + '"]').not('.input-definitiva');
		
		// Calcular la suma de las notas de los periodos
		var sumaNotas = 0;
		var notasContadas = 0;
		
		inputsPeriodos.each(function() {
			var valorNota = parseFloat($(this).val());
			if (!isNaN(valorNota) && valorNota !== '') {
				sumaNotas += valorNota;
				notasContadas++;
			}
		});
		
		console.log('Suma de notas:', sumaNotas, 'Notas contadas:', notasContadas);
		
		// Calcular la definitiva de la materia (promedio de periodos)
		var definitivaMateria = 0;
		if (notasContadas > 0) {
			definitivaMateria = Math.round((sumaNotas / numPeriodos) * 100) / 100;
		}
		
		console.log('Definitiva calculada:', definitivaMateria);
		
		// Buscar el input de definitiva de esta materia
		var inputDefinitiva = inputsPeriodos.first().closest('td').nextAll('td').find('.input-definitiva').first();
		
		if (inputDefinitiva.length > 0) {
			// Agregar clase de animación
			inputDefinitiva.addClass('recalculando');
			
			// Actualizar valor y color
			setTimeout(function() {
				inputDefinitiva.val(definitivaMateria);
				
				// Actualizar color según aprobación
				if (definitivaMateria >= notaMinAprobar) {
					inputDefinitiva.css('color', colorAprobado);
				} else if (definitivaMateria > 0) {
					inputDefinitiva.css('color', colorReprobado);
				} else {
					inputDefinitiva.css('color', '#718096');
				}
				
				// Quitar clase de animación después de completar
				setTimeout(function() {
					inputDefinitiva.removeClass('recalculando');
				}, 600);
			}, 100);
		}
		
		// RECALCULAR PROMEDIO GENERAL DEL ESTUDIANTE
		setTimeout(function() {
			recalcularPromedioGeneral(fila, notaMinAprobar, colorAprobado, colorReprobado);
		}, 400);
	}
	
	// ==========================================
	// RECALCULAR PROMEDIO GENERAL DEL ESTUDIANTE
	// ==========================================
	function recalcularPromedioGeneral(fila, notaMinAprobar, colorAprobado, colorReprobado) {
		console.log('Recalculando promedio general...');
		
		// Buscar TODAS las definitivas de TODAS las materias para este estudiante
		var inputsDefinitivas = fila.find('.input-definitiva');
		
		var sumaDefinitivas = 0;
		var definitivasContadas = 0;
		
		inputsDefinitivas.each(function() {
			var valorDef = parseFloat($(this).val());
			if (!isNaN(valorDef) && valorDef !== '') {
				sumaDefinitivas += valorDef;
				definitivasContadas++;
			}
		});
		
		console.log('Suma definitivas:', sumaDefinitivas, 'Definitivas contadas:', definitivasContadas);
		
		// Calcular promedio general
		var promedioGeneral = 0;
		if (definitivasContadas > 0) {
			promedioGeneral = Math.round((sumaDefinitivas / definitivasContadas) * 100) / 100;
		}
		
		console.log('Promedio general calculado:', promedioGeneral);
		
		// Buscar la celda de promedio (última columna)
		var celdaPromedio = fila.find('td:last-child');
		
		if (celdaPromedio.length > 0) {
			// Agregar clase de animación
			celdaPromedio.addClass('recalculando');
			
			// Actualizar valor y color
			setTimeout(function() {
				celdaPromedio.text(promedioGeneral);
				
				// Actualizar color según aprobación
				if (promedioGeneral >= notaMinAprobar) {
					celdaPromedio.css('color', colorAprobado);
				} else if (promedioGeneral > 0) {
					celdaPromedio.css('color', colorReprobado);
				} else {
					celdaPromedio.css('color', '#718096');
				}
				
				// Quitar clase de animación después de completar
				setTimeout(function() {
					celdaPromedio.removeClass('recalculando');
				}, 600);
			}, 100);
		}
	}
</script>

</head>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

<!-- Overlay de guardado -->
<div id="overlay-guardando">
	<div class="overlay-content">
		<div class="spinner"></div>
		<h3>💾 Guardando Nota...</h3>
		<p>Por favor espera, no cierres esta ventana</p>
	</div>
</div>

<div class="container-consolidado">
	<!-- Header moderno -->
	<div class="header-consolidado">
		<h2>
			<i class="fa fa-graduation-cap"></i>
			Consolidado Final de Definitivas
		</h2>
		<p class="subtitle">
			Gestiona las notas definitivas de todos los estudiantes por curso y grupo
		</p>
	</div>
	
	<!-- Selectores de curso y grupo -->
	<div class="selectores-container">
		<div class="row">
			<div class="col-md-5">
				<div class="selector-group">
				<label>
					<i class="fa fa-book" style="color: #667eea;"></i>
					Seleccionar Curso
				</label>
				<select class="form-control" id="selector_curso">
					<option value="">Seleccione un curso...</option>
					<?php
					$grados = Grados::listarGrados(1);
					while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
						$selected = ($grado['gra_id'] == $cursoId) ? 'selected' : '';
					?>
						<option value="<?=$grado['gra_id'];?>" <?=$selected?>><?=$grado['gra_nombre'];?></option>
					<?php }?>
				</select>
				</div>
			</div>
			
			<div class="col-md-5">
				<div class="selector-group">
					<label>
						<i class="fa fa-users" style="color: #764ba2;"></i>
						Seleccionar Grupo
					</label>
					<select class="form-control" id="selector_grupo">
						<option value="">Seleccione un grupo...</option>
						<?php
						$grupos = Grupos::listarGrupos();
						while ($grupoItem = mysqli_fetch_array($grupos, MYSQLI_BOTH)) {
							$selected = ($grupoItem['gru_id'] == $grupoId) ? 'selected' : '';
						?>
							<option value="<?=$grupoItem['gru_id'];?>" <?=$selected?>><?=$grupoItem['gru_nombre'];?></option>
						<?php }?>
					</select>
				</div>
			</div>
			
			<div class="col-md-2">
				<button class="btn btn-aplicar" id="btn-aplicar-seleccion" <?=empty($cursoId) || empty($grupoId) ? 'disabled' : ''?>>
					<i class="fa fa-refresh"></i> Cargar Datos
				</button>
			</div>
		</div>
		
		<?php if (!empty($cursoId) && !empty($grupoId)) { ?>
		<div class="row mt-3">
			<div class="col-md-12">
				<div style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); padding: 15px 20px; border-radius: 12px; border: 2px solid #667eea;">
					<strong style="color: #4c51bf; font-size: 15px;">
						<i class="fa fa-check-circle"></i> Mostrando:
					</strong>
					<span class="badge-info-curso">📚 <?=$curso['gra_nombre']?></span>
					<span class="badge-info-grupo">👥 <?=$grupo['gru_nombre']?></span>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	
	<?php if (!empty($cursoId) && !empty($grupoId)) { ?>
	
	<!-- Botones de acción -->
	<div class="acciones-container">
		<a href="../compartido/informe-consolidad-final.php?curso=<?= base64_encode($cursoId); ?>&grupo=<?= base64_encode($grupoId); ?>" 
		   class="btn-action btn-informe" 
		   target="_blank">
			<i class="fa fa-file-pdf-o"></i> Descargar Informe PDF
		</a>
		
		<button class="btn-action btn-info-toggle" 
				data-toggle="collapse" 
				data-target="#collapseInfo">
			<i class="fa fa-info-circle"></i> Información Importante
		</button>
	</div>
	
	<!-- Información colapsable -->
	<div class="collapse" id="collapseInfo">
		<div class="alert-info-modern">
			<h4>
				<i class="fa fa-lightbulb-o"></i>
				¿Cómo usar esta página?
			</h4>
			<p>
				<strong>1)</strong> Digite la nota para cada estudiante en el periodo y materia correspondiente y pulse <kbd>Enter</kbd> o cambie de casilla para que se guarde automáticamente.
			</p>
			<p style="font-weight: 600; color: #92400e;">
				⚠️ Importante: Después de digitar una nota, espere a ver la notificación de confirmación antes de continuar con la siguiente.
			</p>
			<p>
				<strong>2)</strong> La definitiva de cada materia se calcula automáticamente del promedio de los periodos. Para que sea correcta, deben estar todas las notas de los periodos registradas.
			</p>
		</div>
	</div>
	
	<!-- Tabla con notas -->
	<div class="table-container">
		<span id="resp" style="display:none;"></span>
		<?php try {
			
			/**
			 * OPTIMIZACIÓN DE CONSULTAS - CARGA MASIVA DE DATOS
			 * En lugar de hacer N×M×P consultas (una por cada estudiante×materia×periodo),
			 * cargamos todos los datos en 3 consultas optimizadas y luego procesamos en PHP
			 */
			
			// ==========================================
			// 1. CARGAR MATERIAS UNA SOLA VEZ
			// ==========================================
			$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $cursoId, $grupoId);
			$materias = [];
			$numCargasPorCurso = mysqli_num_rows($cargas);
			while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
				$materias[$carga['car_id']] = $carga;
			}
			
		// ==========================================
		// 2. CARGAR ESTUDIANTES
		// ==========================================
		$filtro = " AND mat_grado='" . $cursoId . "' AND mat_grupo='" . $grupoId . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
		$consultaEstudiantes = Estudiantes::listarEstudiantesEnGrados($filtro, "", $curso, $grupoId);
		
		$estudiantes = [];
		$estudiantesIds = [];
		
		// Validar que la consulta sea exitosa antes de procesar
		if ($consultaEstudiantes !== null && $consultaEstudiantes !== false) {
			while ($estudiante = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)) {
				$estudiantes[$estudiante['mat_id']] = $estudiante;
				$estudiantesIds[] = $estudiante['mat_id'];
			}
		} else {
			echo '<div class="alert alert-danger">
				<strong>Error:</strong> No se pudieron cargar los estudiantes. Por favor, verifica que el curso y grupo existan.
			</div>';
			exit();
		}
			
			// ==========================================
			// 3. CARGAR TODAS LAS NOTAS DE UNA VEZ
			// ==========================================
			$notasMap = [];
			if (!empty($estudiantesIds)) {
				$estudiantesIn = "'" . implode("','", $estudiantesIds) . "'";
				$cargasIn = "'" . implode("','", array_keys($materias)) . "'";
				
				// Consulta optimizada para todas las notas de todos los periodos
				$sql = "SELECT bol_estudiante, bol_carga, bol_periodo, bol_nota, bol_tipo, bol_id
						FROM " . BD_ACADEMICA . ".academico_boletin
						WHERE bol_estudiante IN ($estudiantesIn)
						AND bol_carga IN ($cargasIn)
						AND institucion = ?
						AND year = ?";
				
				$parametrosNotas = [$config['conf_id_institucion'], $agnoBD];
				$resultNotas = BindSQL::prepararSQL($sql, $parametrosNotas);
				
				while ($nota = mysqli_fetch_array($resultNotas, MYSQLI_BOTH)) {
					$key = $nota['bol_estudiante'] . '_' . $nota['bol_carga'] . '_' . $nota['bol_periodo'];
					$notasMap[$key] = $nota;
				}
			}
			
			// ==========================================
			// 4. CARGAR TODAS LAS NIVELACIONES DE UNA VEZ
			// ==========================================
			$nivelacionesMap = [];
			if (!empty($estudiantesIds) && !empty(array_keys($materias))) {
				$estudiantesIn = "'" . implode("','", $estudiantesIds) . "'";
				$cargasIn = "'" . implode("','", array_keys($materias)) . "'";
				
				// Consulta optimizada para todas las nivelaciones
				$sql = "SELECT niv_cod_estudiante, niv_id_asg, niv_definitiva, niv_acta, niv_fecha_nivelacion
						FROM " . BD_ACADEMICA . ".academico_nivelaciones
						WHERE niv_cod_estudiante IN ($estudiantesIn)
						AND niv_id_asg IN ($cargasIn)
						AND institucion = ?
						AND year = ?";
				
				$parametrosNiv = [$config['conf_id_institucion'], $agnoBD];
				$resultNiv = BindSQL::prepararSQL($sql, $parametrosNiv);
				
				while ($niv = mysqli_fetch_array($resultNiv, MYSQLI_BOTH)) {
					$key = $niv['niv_cod_estudiante'] . '_' . $niv['niv_id_asg'];
					$nivelacionesMap[$key] = $niv;
				}
			}
			
			// ==========================================
			// FUNCIÓN AUXILIAR PARA OBTENER NOTA
			// ==========================================
			function obtenerNota($notasMap, $estudianteId, $cargaId, $periodo) {
				$key = $estudianteId . '_' . $cargaId . '_' . $periodo;
				return isset($notasMap[$key]) ? $notasMap[$key] : null;
			}
			
			// ==========================================
			// FUNCIÓN AUXILIAR PARA OBTENER NIVELACIÓN
			// ==========================================
			function obtenerNivelacion($nivelacionesMap, $estudianteId, $cargaId) {
				$key = $estudianteId . '_' . $cargaId;
				return isset($nivelacionesMap[$key]) ? $nivelacionesMap[$key] : null;
			}
			
			$contEstudiantes = count($estudiantes);
		?>
			<table id="tabla-consolidado" class="scrollable-table display nowrap" style="width:100%">
				<thead>
					<tr>
						<th rowspan="2" class="css_doc">DOC</th>
						<th rowspan="2" class="css_nombre">ESTUDIANTE</th>
						<?php foreach ($materias as $carga) { ?>
							<th colspan="<?= $config[19] + 1; ?>">
								<?= strtoupper($carga['mat_nombre']); ?>
							</th>
						<?php } ?>
						<th rowspan="2" class="css_prom">PROM</th>
					</tr>
					<tr>
						<?php foreach ($materias as $carga) { ?>
							<?php for ($p = 1; $p <= $config[19]; $p++) { ?>
								<th>P<?= $p ?></th>
							<?php } ?>
							<th style="background:#fffbeb; color:#92400e; font-weight:700;">DEF</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($estudiantes as $resultado) {
						$defPorEstudiante = 0;
					?>
						<tr>
							<td><?= $resultado['mat_documento']; ?></td>
							<td><?= Estudiantes::NombreCompletoDelEstudiante($resultado); ?></td>
							<?php
							foreach ($materias as $carga) {
								$p = 1;
								$defPorMateria = 0;
								
								// PERIODOS DE CADA MATERIA
								while ($p <= $config[19]) {
									$boletin = obtenerNota($notasMap, $resultado['mat_id'], $carga['car_id'], $p);
									
									$color = '#718096';
									$claseNota = '';
									if (isset($boletin['bol_nota']) and $boletin['bol_nota'] < $config[5] and $boletin['bol_nota'] != "") {
										$color = $config[6];
										$claseNota = 'nota-reprobada';
									} elseif (isset($boletin['bol_nota']) and $boletin['bol_nota'] >= $config[5]) {
										$color = $config[7];
										$claseNota = 'nota-aprobada';
									}
									
									if (isset($boletin['bol_nota'])) {
										$defPorMateria += $boletin['bol_nota'];
									}
									
									// Tipo de nota
									$tipo = '';
									$claseTipo = '';
									if (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 1) {
										$tipo = 'Normal';
										$claseTipo = 'tipo-normal';
									} elseif (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 2) {
										$tipo = 'Rec. P.';
										$claseTipo = 'tipo-recuperacion';
									} elseif (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 3) {
										$tipo = 'Rec. I.';
										$claseTipo = 'tipo-recuperacion';
									} elseif (isset($boletin['bol_tipo']) and $boletin['bol_tipo'] == 4) {
										$tipo = 'Directivo';
										$claseTipo = 'tipo-directivo';
									}
									
									//VALIDAR SI SE PUEDE EDITAR
									$disabled = "";
									if ((isset($boletin['bol_nota']) and ($boletin['bol_nota'] != "" or $carga['car_periodo'] <= $p)) and $config['conf_editar_definitivas_consolidado'] != true) {
										$disabled = "disabled";
									}
								?>
									<td>
										<input 
											class="input-nota <?=$claseNota?>" 
											style="color:<?= $color; ?>" 
											value="<?php if (isset($boletin['bol_nota'])) {echo $boletin['bol_nota'];} ?>" 
											name="<?= $carga['car_id']; ?>" 
											id="<?= $resultado['mat_id']; ?>" 
											onChange="def(this)" 
											alt="<?= $p; ?>" 
											title="<?= $carga['mat_nombre']; ?> - Periodo <?= $p; ?>" 
											<?= $disabled; ?> 
											<?= $disabledPermiso; ?>
										/>
										<?php if ($tipo != '') { ?>
										<span class="tipo-nota <?=$claseTipo?>">
											<?= $tipo; ?>
										</span>
										<?php } ?>
									</td>
								<?php
									$p++;
								}
								
								// DEFINITIVA DE CADA MATERIA
								$defPorMateria = round($defPorMateria / $config[19], 2);
								
								$color = '#718096';
								if ($defPorMateria < $config[5] and $defPorMateria != "") {
									$color = $config[6];
								} elseif ($defPorMateria >= $config[5]) {
									$color = $config[7];
								}
								
							// CONSULTAR NIVELACIONES (desde el mapa optimizado)
							$cNiv = obtenerNivelacion($nivelacionesMap, $resultado['mat_id'], $carga['car_id']);
							if (isset($cNiv['niv_definitiva']) and $cNiv['niv_definitiva'] > $defPorMateria) {
								$defPorMateria = $cNiv['niv_definitiva'];
								$msj = 'Nivelación';
								$msjDetalle = '';
								if (isset($cNiv['niv_acta']) and isset($cNiv['niv_fecha_nivelacion'])) {
									$msjDetalle = "Acta " . $cNiv['niv_acta'] . " - " . $cNiv['niv_fecha_nivelacion'];
								}
							} else {
								$msj = '';
								$msjDetalle = '';
							}
								?>
								<td style="background:#fffbeb;">
									<input 
										class="input-nota input-definitiva" 
										style="color:<?= $color; ?>" 
										value="<?php if (isset($defPorMateria)) {echo $defPorMateria;} ?>"
										disabled
										title="Definitiva de <?= $carga['mat_nombre']; ?>"
									/>
									<?php if ($msj != '') { ?>
									<span class="tipo-nota tipo-directivo">
										<?= $msj; ?>
										<?php if ($msjDetalle != '') { ?>
										<br><span style="font-size: 8px;"><?= $msjDetalle; ?></span>
										<?php } ?>
									</span>
									<?php } ?>
								</td>
							<?php
								//DEFINITIVA POR CADA ESTUDIANTE
								$defPorEstudiante += $defPorMateria;
							}
							
							$defPorEstudiante = round($defPorEstudiante / $numCargasPorCurso, 2);
							$color = '#718096';
							if ($defPorEstudiante < $config[5] and $defPorEstudiante != "") {
								$color = $config[6];
							} elseif ($defPorEstudiante >= $config[5]) {
								$color = $config[7];
							}
							?>
							<td style="color:<?= $color; ?>;">
								<?= $defPorEstudiante; ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php
		} catch (Exception $e) {
			include("../compartido/error-catch-to-report.php");
		}
		?>
	</div>
	
	<?php } else { ?>
	<!-- Mensaje cuando no hay selección -->
	<div class="mensaje-vacio">
		<div class="icono">📚</div>
		<h3>Selecciona un Curso y Grupo</h3>
		<p>
			Para comenzar, selecciona un curso y un grupo en los selectores de arriba<br>
			y haz clic en <strong>"Cargar Datos"</strong>
		</p>
	</div>
	<?php } ?>
</div>

<script>
$(document).ready(function() {
	console.log('=== Consolidado Final Moderno - Iniciando ===');
	
	// ==========================================
	// VALIDAR Y HABILITAR BOTÓN
	// ==========================================
	function validarSeleccion() {
		var curso = $('#selector_curso').val();
		var grupo = $('#selector_grupo').val();
		
		if (curso && grupo) {
			$('#btn-aplicar-seleccion').prop('disabled', false);
		} else {
			$('#btn-aplicar-seleccion').prop('disabled', true);
		}
	}
	
	$('#selector_curso, #selector_grupo').on('change', function() {
		validarSeleccion();
	});
	
	// ==========================================
	// APLICAR SELECCIÓN
	// ==========================================
	$('#btn-aplicar-seleccion').on('click', function() {
		var curso = $('#selector_curso').val();
		var grupo = $('#selector_grupo').val();
		
		if (!curso || !grupo) {
			$.toast({
				heading: 'Atención',
				text: 'Por favor selecciona un curso y un grupo',
				position: 'top-right',
				loaderBg: '#f59e0b',
				icon: 'warning',
				hideAfter: 3000
			});
			return;
		}
		
		// Mostrar loading en el botón
		var btnHtml = $(this).html();
		$(this).html('<i class="fa fa-spinner fa-spin"></i> Cargando...').prop('disabled', true);
		
		// Recargar con POST
		var form = $('<form method="POST" action="estudiantes-consolidado-final-detalles2.php"></form>');
		form.append('<input type="hidden" name="curso" value="' + curso + '">');
		form.append('<input type="hidden" name="grupo" value="' + grupo + '">');
		$('body').append(form);
		form.submit();
	});
	
	<?php if (!empty($cursoId) && !empty($grupoId)) { ?>
	// ==========================================
	// INICIALIZAR DATATABLE
	// ==========================================
	var tabla = $('#tabla-consolidado').DataTable({
		"language": {
			"sProcessing": "Procesando...",
			"sLengthMenu": "Mostrar _MENU_ estudiantes",
			"sZeroRecords": "No se encontraron resultados",
			"sEmptyTable": "No hay estudiantes en este curso/grupo",
			"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ estudiantes",
			"sInfoEmpty": "Mostrando 0 estudiantes",
			"sInfoFiltered": "(filtrado de _MAX_ estudiantes en total)",
			"sSearch": "🔍 Buscar estudiante:",
			"oPaginate": {
				"sFirst": "Primero",
				"sLast": "Último",
				"sNext": "Siguiente",
				"sPrevious": "Anterior"
			}
		},
		"pageLength": 25,
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
		"order": [[1, 'asc']], // Ordenar por estudiante
		"columnDefs": [
			{ "orderable": true, "targets": [1] }, // Solo estudiante ordenable
			{ "orderable": false, "targets": '_all' }
		],
		"scrollX": false,
		"autoWidth": false,
		"dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
			   '<"row"<"col-sm-12"tr>>' +
			   '<"row"<"col-sm-5"i><"col-sm-7"p>>'
	});
	
	console.log('✓ DataTable inicializada con <?=$contEstudiantes?> estudiantes');
	
	// ==========================================
	// NAVEGACIÓN CON TECLADO (ENTER)
	// ==========================================
	$(document).on('keypress', '.input-nota:not(:disabled)', function(e) {
		if (e.which == 13) { // Enter
			e.preventDefault();
			
			// Buscar todos los inputs habilitados
			var inputs = $('.input-nota:not(:disabled)');
			var index = inputs.index(this);
			
			if (index < inputs.length - 1) {
				// Pasar al siguiente input
				inputs.eq(index + 1).focus().select();
			}
		}
	});
	<?php } ?>
	
	console.log('✓ Sistema cargado correctamente');
});
</script>

</body>
</html>
