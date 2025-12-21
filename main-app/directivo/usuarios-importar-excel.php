<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0125';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jQuery Toast -->
<link href="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.css" rel="stylesheet" type="text/css" />

<style>
	/* ========================================
	   DISEÑO MODERNO DE IMPORTACIÓN
	   ======================================== */
	
	.wizard-container {
		max-width: 1200px;
		margin: 0 auto;
	}
	
	/* Pasos del wizard */
	.steps-indicator {
		display: flex;
		justify-content: space-between;
		margin-bottom: 40px;
		position: relative;
	}
	
	.steps-indicator::before {
		content: '';
		position: absolute;
		top: 20px;
		left: 10%;
		right: 10%;
		height: 2px;
		background: #e0e0e0;
		z-index: 0;
	}
	
	.step {
		flex: 1;
		text-align: center;
		position: relative;
		z-index: 1;
	}
	
	.step-number {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		background: #e0e0e0;
		color: #666;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		font-weight: bold;
		margin-bottom: 10px;
		transition: all 0.3s ease;
	}
	
	.step.active .step-number {
		background: #007bff;
		color: white;
		box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2);
	}
	
	.step.completed .step-number {
		background: #28a745;
		color: white;
	}
	
	.step.completed .step-number::after {
		content: '✓';
	}
	
	.step-title {
		font-size: 13px;
		color: #666;
		font-weight: 500;
	}
	
	.step.active .step-title {
		color: #007bff;
		font-weight: 600;
	}
	
	/* Zona de Drag & Drop */
	.drop-zone {
		border: 3px dashed #cbd5e0;
		border-radius: 12px;
		padding: 60px 40px;
		text-align: center;
		transition: all 0.3s ease;
		background: #f8f9fa;
		cursor: pointer;
		position: relative;
	}
	
	.drop-zone:hover {
		border-color: #007bff;
		background: #e7f3ff;
	}
	
	.drop-zone.drag-over {
		border-color: #28a745;
		background: #d4edda;
		transform: scale(1.02);
	}
	
	.drop-zone-icon {
		font-size: 64px;
		color: #cbd5e0;
		margin-bottom: 20px;
	}
	
	.drop-zone:hover .drop-zone-icon {
		color: #007bff;
	}
	
	.drop-zone.drag-over .drop-zone-icon {
		color: #28a745;
	}
	
	.file-input-hidden {
		display: none;
	}
	
	/* Archivo seleccionado */
	.file-selected {
		background: white;
		border: 2px solid #28a745;
		border-radius: 12px;
		padding: 30px;
		display: none;
	}
	
	.file-selected.show {
		display: block;
	}
	
	.file-icon {
		font-size: 48px;
		color: #28a745;
		margin-bottom: 15px;
	}
	
	/* Barra de progreso */
	.progress-container {
		margin: 30px 0;
		display: none;
	}
	
	.progress-container.show {
		display: block;
	}
	
	.progress {
		height: 30px;
		border-radius: 15px;
		overflow: visible;
	}
	
	.progress-bar {
		font-size: 14px;
		line-height: 30px;
		font-weight: 600;
		transition: width 0.5s ease;
	}
	
	/* Card de información */
	.info-card {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		border-radius: 12px;
		padding: 30px;
		margin-bottom: 30px;
	}
	
	.info-card h4 {
		color: white;
		margin-bottom: 20px;
		font-weight: 600;
	}
	
	.info-list {
		list-style: none;
		padding: 0;
	}
	
	.info-list li {
		padding: 10px 0;
		border-bottom: 1px solid rgba(255,255,255,0.2);
		display: flex;
		align-items: center;
	}
	
	.info-list li:last-child {
		border-bottom: none;
	}
	
	.info-list li i {
		margin-right: 15px;
		font-size: 20px;
	}
	
	/* Tabla de resultados */
	.result-table {
		margin-top: 30px;
	}
	
	.result-stats {
		display: flex;
		gap: 20px;
		margin-bottom: 30px;
		flex-wrap: wrap;
	}
	
	.stat-card {
		flex: 1;
		min-width: 200px;
		background: white;
		border-radius: 12px;
		padding: 25px;
		text-align: center;
		box-shadow: 0 4px 6px rgba(0,0,0,0.1);
		transition: transform 0.3s ease;
	}
	
	.stat-card:hover {
		transform: translateY(-5px);
	}
	
	.stat-number {
		font-size: 42px;
		font-weight: bold;
		margin-bottom: 10px;
	}
	
	.stat-label {
		color: #666;
		font-size: 14px;
		text-transform: uppercase;
		letter-spacing: 1px;
	}
	
	.stat-card.success .stat-number {
		color: #28a745;
	}
	
	.stat-card.danger .stat-number {
		color: #dc3545;
	}
	
	.stat-card.warning .stat-number {
		color: #ffc107;
	}
	
	.stat-card.info .stat-number {
		color: #17a2b8;
	}
	
	/* Botones */
	.btn-modern {
		padding: 12px 30px;
		border-radius: 8px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		transition: all 0.3s ease;
		border: none;
	}
	
	.btn-modern:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0,0,0,0.2);
	}
	
	/* Sección de contenido */
	.wizard-step {
		display: none;
		animation: fadeIn 0.5s ease;
	}
	
	.wizard-step.active {
		display: block;
	}
	
	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(20px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}
	
	/* Tabla de tipos de usuario */
	.user-types-table {
		background: white;
		border-radius: 8px;
		overflow: hidden;
		margin-top: 20px;
	}
	
	.user-types-table table {
		width: 100%;
		margin: 0;
	}
	
	.user-types-table th {
		background: #f8f9fa;
		padding: 15px;
		font-weight: 600;
		color: #495057;
		border-bottom: 2px solid #dee2e6;
	}
	
	.user-types-table td {
		padding: 15px;
		border-bottom: 1px solid #dee2e6;
	}
	
	.user-types-table tr:last-child td {
		border-bottom: none;
	}
	
	.type-badge {
		display: inline-block;
		padding: 6px 12px;
		border-radius: 20px;
		font-weight: 600;
		font-size: 12px;
	}
	
	.type-badge.docente {
		background: #e3f2fd;
		color: #1976d2;
	}
	
	.type-badge.directivo {
		background: #f3e5f5;
		color: #7b1fa2;
	}
	
	.type-badge.acudiente {
		background: #fff3e0;
		color: #f57c00;
	}
	
	/* Alertas mejoradas */
	.alert-modern {
		border-radius: 12px;
		border: none;
		padding: 20px;
		margin-bottom: 25px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	}
	
	.alert-modern i {
		font-size: 24px;
		margin-right: 15px;
	}
	
	/* Responsive */
	@media (max-width: 768px) {
		.steps-indicator {
			flex-direction: column;
			gap: 20px;
		}
		
		.steps-indicator::before {
			display: none;
		}
		
		.result-stats {
			flex-direction: column;
		}
		
		.stat-card {
			min-width: 100%;
		}
	}
</style>

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
							<div class="page-title">
								<i class="fa fa-file-excel"></i> Importar Usuarios desde Excel
							</div>
						</div>
						<ol class="breadcrumb page-breadcrumb pull-right">
							<li><a class="parent-item" href="usuarios.php">Usuarios</a>&nbsp;<i class="fa fa-angle-right"></i></li>
							<li class="active">Importar Excel</li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<?php include("../../config-general/mensajes-informativos.php"); ?>
						
						<div class="wizard-container">
							<!-- Indicador de pasos -->
							<div class="steps-indicator">
								<div class="step active" data-step="1">
									<div class="step-number">1</div>
									<div class="step-title">Información</div>
								</div>
								<div class="step" data-step="2">
									<div class="step-number">2</div>
									<div class="step-title">Subir Archivo</div>
								</div>
								<div class="step" data-step="3">
									<div class="step-number">3</div>
									<div class="step-title">Procesando</div>
								</div>
								<div class="step" data-step="4">
									<div class="step-number">4</div>
									<div class="step-title">Resultados</div>
								</div>
							</div>
							
							<!-- Paso 1: Información -->
							<div class="wizard-step active" id="step1">
								<div class="row">
									<div class="col-md-8">
										<div class="card">
											<div class="card-body" style="padding: 40px;">
												<h3 style="margin-bottom: 30px;">
													<i class="fa fa-info-circle text-info"></i> 
													¿Cómo funciona la importación?
												</h3>
												
												<div class="alert alert-modern alert-info">
													<i class="fa fa-lightbulb"></i>
													<strong>¡Es muy fácil!</strong> Solo necesitas un archivo Excel con las columnas en el orden correcto.
												</div>
												
												<h4 style="margin-top: 30px; margin-bottom: 20px;">
													<i class="fa fa-list-ol"></i> Columnas requeridas (en este orden):
												</h4>
												
												<div class="user-types-table">
													<table class="table">
														<thead>
															<tr>
																<th>Columna</th>
																<th>Descripción</th>
																<th>Ejemplo</th>
																<th>Requerido</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><strong>1. Tipo de Usuario</strong></td>
																<td>Código numérico del tipo</td>
																<td>2, 3, o 5</td>
																<td><span class="badge badge-danger">Sí</span></td>
															</tr>
															<tr>
																<td><strong>2. Primer Apellido</strong></td>
																<td>Apellido principal</td>
																<td>García</td>
																<td><span class="badge badge-danger">Sí</span></td>
															</tr>
															<tr>
																<td><strong>3. Segundo Apellido</strong></td>
																<td>Segundo apellido</td>
																<td>López</td>
																<td><span class="badge badge-secondary">No</span></td>
															</tr>
															<tr>
																<td><strong>4. Primer Nombre</strong></td>
																<td>Nombre principal</td>
																<td>Juan</td>
																<td><span class="badge badge-danger">Sí</span></td>
															</tr>
															<tr>
																<td><strong>5. Segundo Nombre</strong></td>
																<td>Segundo nombre</td>
																<td>Carlos</td>
																<td><span class="badge badge-secondary">No</span></td>
															</tr>
															<tr>
																<td><strong>6. Usuario</strong></td>
																<td>Nombre de usuario para login</td>
																<td>jgarcia</td>
																<td><span class="badge badge-warning">Opcional*</span></td>
															</tr>
															<tr>
																<td><strong>7. Documento</strong></td>
																<td>Número de documento (sin puntos)</td>
																<td>12345678</td>
																<td><span class="badge badge-danger">Sí</span></td>
															</tr>
														</tbody>
													</table>
												</div>
												
												<div class="alert alert-modern alert-warning" style="margin-top: 30px;">
													<i class="fa fa-exclamation-triangle"></i>
													<strong>*Nota:</strong> Si no se proporciona nombre de usuario, se usará automáticamente el número de documento.
												</div>
												
												<h4 style="margin-top: 30px; margin-bottom: 20px;">
													<i class="fa fa-users"></i> Tipos de Usuario:
												</h4>
												
												<div class="user-types-table">
													<table class="table">
														<thead>
															<tr>
																<th>Código</th>
																<th>Tipo</th>
																<th>Descripción</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><strong class="text-primary">2</strong></td>
																<td><span class="type-badge docente">DOCENTE</span></td>
																<td>Profesores y maestros</td>
															</tr>
															<tr>
																<td><strong class="text-danger">3</strong></td>
																<td><span class="type-badge acudiente">ACUDIENTE</span></td>
																<td>Padres y representantes</td>
															</tr>
															<tr>
																<td><strong class="text-purple">5</strong></td>
																<td><span class="type-badge directivo">DIRECTIVO</span></td>
																<td>Directores y coordinadores</td>
															</tr>
														</tbody>
													</table>
												</div>
												
												<div class="text-center" style="margin-top: 40px;">
													<button type="button" class="btn btn-primary btn-modern btn-lg" onclick="irAPaso(2)">
														<i class="fa fa-arrow-right"></i> Entendido, Continuar
													</button>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="info-card">
											<h4><i class="fa fa-download"></i> Plantilla de Ejemplo</h4>
											<p>Descarga nuestra plantilla pre-configurada con las columnas en el orden correcto:</p>
											<a href="generar-plantilla-excel.php" class="btn btn-light btn-block btn-modern">
												<i class="fa fa-file-excel"></i> Descargar Plantilla
											</a>
										</div>
										
										<div class="card">
											<div class="card-body">
												<h5><i class="fa fa-shield-alt text-success"></i> Seguridad</h5>
												<ul class="info-list" style="color: #333; list-style: disc; padding-left: 20px;">
													<li>Los datos se importan de forma segura</li>
													<li>Contraseña por defecto: <strong>12345678</strong></li>
													<li>Los usuarios duplicados se omiten</li>
													<li>Proceso reversible</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<!-- Paso 2: Subir archivo -->
							<div class="wizard-step" id="step2">
								<div class="card">
									<div class="card-body" style="padding: 40px;">
										<h3 style="margin-bottom: 30px; text-align: center;">
											<i class="fa fa-cloud-upload-alt"></i> Sube tu archivo Excel
										</h3>
										
										<form id="importForm" enctype="multipart/form-data">
											<!-- Drop Zone -->
											<div class="drop-zone" id="dropZone">
												<div class="drop-zone-icon">
													<i class="fa fa-cloud-upload-alt"></i>
												</div>
												<h4>Arrastra y suelta tu archivo aquí</h4>
												<p style="color: #666; margin: 15px 0;">o</p>
												<button type="button" class="btn btn-primary btn-modern" onclick="document.getElementById('fileInput').click()">
													<i class="fa fa-folder-open"></i> Seleccionar Archivo
												</button>
												<p style="color: #999; margin-top: 20px; font-size: 13px;">
													Formatos aceptados: .xlsx, .xls | Tamaño máximo: 5MB
												</p>
												<input type="file" id="fileInput" name="planilla" class="file-input-hidden" accept=".xlsx,.xls" required>
											</div>
											
											<!-- Archivo seleccionado -->
											<div class="file-selected" id="fileSelected">
												<div class="text-center">
													<div class="file-icon" id="fileIcon">
														<i class="fa fa-file-excel"></i>
													</div>
													<h4 id="fileName">archivo.xlsx</h4>
													<p style="color: #666;">
														Tamaño: <span id="fileSize">0 KB</span>
													</p>
													<div id="validationStatus" style="margin: 15px 0;">
														<!-- Se llenará dinámicamente -->
													</div>
													<button type="button" class="btn btn-danger btn-sm" onclick="removeFile()">
														<i class="fa fa-times"></i> Cambiar archivo
													</button>
												</div>
											</div>
											
											<div class="text-center" style="margin-top: 30px;">
												<button type="button" class="btn btn-secondary btn-modern" onclick="irAPaso(1)">
													<i class="fa fa-arrow-left"></i> Atrás
												</button>
												<button type="button" class="btn btn-success btn-modern btn-lg" id="btnProcesar" onclick="procesarArchivo()" disabled>
													<i class="fa fa-cogs"></i> Procesar Archivo
												</button>
											</div>
										</form>
									</div>
								</div>
							</div>
							
							<!-- Paso 3: Procesando -->
							<div class="wizard-step" id="step3">
								<div class="card">
									<div class="card-body" style="padding: 60px; text-align: center;">
										<div style="font-size: 80px; color: #007bff; margin-bottom: 30px;">
											<i class="fa fa-spinner fa-spin"></i>
										</div>
										<h3 style="margin-bottom: 20px;">Procesando tu archivo...</h3>
										<p style="color: #666; font-size: 16px; margin-bottom: 40px;">
											Por favor espera mientras importamos los usuarios
										</p>
										
										<div class="progress-container show">
											<div class="progress">
												<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
													 role="progressbar" 
													 id="progressBar" 
													 style="width: 0%">
													0%
												</div>
											</div>
											<p id="progressText" style="margin-top: 15px; color: #666; font-weight: 600;">
												Iniciando...
											</p>
										</div>
									</div>
								</div>
							</div>
							
							<!-- Paso 4: Resultados -->
							<div class="wizard-step" id="step4">
								<div class="card">
									<div class="card-body" style="padding: 40px;">
										<div class="text-center" style="margin-bottom: 40px;">
											<div style="font-size: 80px; color: #28a745; margin-bottom: 20px;">
												<i class="fa fa-check-circle"></i>
											</div>
											<h2 style="margin-bottom: 10px;">¡Importación Completada!</h2>
											<p style="color: #666; font-size: 16px;">
												El proceso ha finalizado exitosamente
											</p>
										</div>
										
										<div class="result-stats" id="resultStats">
											<!-- Se llenará dinámicamente -->
										</div>
										
										<div class="result-table" id="resultDetails">
											<!-- Se llenará dinámicamente -->
										</div>
										
										<div class="text-center" style="margin-top: 40px;">
											<a href="usuarios.php" class="btn btn-primary btn-modern btn-lg">
												<i class="fa fa-users"></i> Ir a Lista de Usuarios
											</a>
											<button type="button" class="btn btn-success btn-modern btn-lg" onclick="resetWizard()">
												<i class="fa fa-redo"></i> Importar Más Usuarios
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end page content -->
			<?php include("../compartido/panel-configuracion.php");?>
		</div>
		<!-- end page container -->
		<?php include("../compartido/footer.php");?>
	</div>
</div>

<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- end js include path -->

<script>
	let selectedFile = null;
	let currentStep = 1;
	
	// Navegación entre pasos
	function irAPaso(paso) {
		// Ocultar paso actual
		$('.wizard-step').removeClass('active');
		$('.step').removeClass('active completed');
		
		// Mostrar nuevo paso
		$('#step' + paso).addClass('active');
		$('[data-step="' + paso + '"]').addClass('active');
		
		// Marcar pasos completados
		for(let i = 1; i < paso; i++) {
			$('[data-step="' + i + '"]').addClass('completed');
		}
		
		currentStep = paso;
		
		// Scroll arriba
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}
	
	// Drag & Drop
	const dropZone = document.getElementById('dropZone');
	const fileInput = document.getElementById('fileInput');
	
	// Prevenir comportamiento por defecto
	['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
		dropZone.addEventListener(eventName, preventDefaults, false);
	});
	
	function preventDefaults(e) {
		e.preventDefault();
		e.stopPropagation();
	}
	
	// Resaltar zona al arrastrar
	['dragenter', 'dragover'].forEach(eventName => {
		dropZone.addEventListener(eventName, highlight, false);
	});
	
	['dragleave', 'drop'].forEach(eventName => {
		dropZone.addEventListener(eventName, unhighlight, false);
	});
	
	function highlight(e) {
		dropZone.classList.add('drag-over');
	}
	
	function unhighlight(e) {
		dropZone.classList.remove('drag-over');
	}
	
	// Manejar soltar archivo
	dropZone.addEventListener('drop', handleDrop, false);
	
	function handleDrop(e) {
		const dt = e.dataTransfer;
		const files = dt.files;
		
		if(files.length > 0) {
			fileInput.files = files;
			handleFiles(files);
		}
	}
	
	// Manejar selección de archivo
	fileInput.addEventListener('change', function(e) {
		handleFiles(this.files);
	});
	
	function handleFiles(files) {
		if(files.length === 0) return;
		
		const file = files[0];
		
		// Validar tipo de archivo
		const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		if(!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls)$/)) {
			Swal.fire({
				icon: 'error',
				title: 'Archivo no válido',
				text: 'Por favor selecciona un archivo Excel (.xlsx o .xls)'
			});
			return;
		}
		
		// Validar tamaño (5MB)
		const maxSize = 5 * 1024 * 1024;
		if(file.size > maxSize) {
			Swal.fire({
				icon: 'error',
				title: 'Archivo muy grande',
				text: 'El archivo no debe superar los 5MB'
			});
			return;
		}
		
		selectedFile = file;
		
		// Mostrar información del archivo
		document.getElementById('fileName').textContent = file.name;
		document.getElementById('fileSize').textContent = formatFileSize(file.size);
		
		// Ocultar drop zone y mostrar archivo seleccionado
		dropZone.style.display = 'none';
		document.getElementById('fileSelected').classList.add('show');
		document.getElementById('btnProcesar').disabled = true; // Deshabilitar hasta validar cabeceras
		
		// Validar cabeceras inmediatamente
		validarCabeceras(file);
	}
	
	function formatFileSize(bytes) {
		if(bytes === 0) return '0 Bytes';
		const k = 1024;
		const sizes = ['Bytes', 'KB', 'MB', 'GB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));
		return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
	}
	
	function removeFile() {
		selectedFile = null;
		fileInput.value = '';
		document.getElementById('fileSelected').classList.remove('show');
		dropZone.style.display = 'block';
		document.getElementById('btnProcesar').disabled = true;
		document.getElementById('validationStatus').innerHTML = '';
	}
	
	// Validar cabeceras del archivo
	function validarCabeceras(file) {
		const formData = new FormData();
		formData.append('planilla', file);
		
		// Mostrar estado de validación
		document.getElementById('validationStatus').innerHTML = `
			<div style="color: #ffc107;">
				<i class="fa fa-spinner fa-spin"></i> Validando formato del archivo...
			</div>
		`;
		
		$.ajax({
			url: 'validar-cabeceras-excel.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function(response) {
				if(response.success && response.cabeceras_correctas) {
					// Cabeceras correctas
					document.getElementById('fileIcon').innerHTML = '<i class="fa fa-check-circle" style="color: #28a745;"></i>';
					document.getElementById('validationStatus').innerHTML = `
						<div style="color: #28a745;">
							<i class="fa fa-check-circle"></i> 
							<strong>Archivo válido</strong><br>
							<small>Se encontraron ${response.filas_datos} filas de datos</small>
						</div>
					`;
					document.getElementById('btnProcesar').disabled = false;
					
					// Toast de éxito
					$.toast({
						heading: 'Archivo válido',
						text: `Se encontraron ${response.filas_datos} filas de datos para procesar`,
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right',
						hideAfter: 3000
					});
				} else {
					// Cabeceras incorrectas
					document.getElementById('fileIcon').innerHTML = '<i class="fa fa-exclamation-triangle" style="color: #dc3545;"></i>';
					
					let erroresHtml = '<div style="color: #dc3545; text-align: left; margin-top: 10px;">';
					erroresHtml += '<strong>Errores encontrados:</strong><br>';
					response.errores.forEach(error => {
						erroresHtml += `• ${error}<br>`;
					});
					erroresHtml += '</div>';
					
					document.getElementById('validationStatus').innerHTML = `
						<div style="color: #dc3545;">
							<i class="fa fa-exclamation-triangle"></i> 
							<strong>Formato incorrecto</strong>
						</div>
						${erroresHtml}
						<div style="margin-top: 10px;">
							<small style="color: #666;">
								Descarga la plantilla correcta desde el Paso 1
							</small>
						</div>
					`;
					document.getElementById('btnProcesar').disabled = true;
					
					// Toast de error
					$.toast({
						heading: 'Formato incorrecto',
						text: 'Las cabeceras del archivo no coinciden con el formato esperado',
						showHideTransition: 'slide',
						icon: 'error',
						position: 'top-right',
						hideAfter: 5000
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Error validando cabeceras:', error);
				
				document.getElementById('fileIcon').innerHTML = '<i class="fa fa-times-circle" style="color: #dc3545;"></i>';
				document.getElementById('validationStatus').innerHTML = `
					<div style="color: #dc3545;">
						<i class="fa fa-times-circle"></i> 
						<strong>Error al validar</strong><br>
						<small>No se pudo verificar el formato del archivo</small>
					</div>
				`;
				document.getElementById('btnProcesar').disabled = true;
				
				Swal.fire({
					icon: 'error',
					title: 'Error de validación',
					text: 'No se pudo validar el archivo. Por favor intenta de nuevo.',
					footer: '<a href="javascript:void(0)" onclick="removeFile()">Cambiar archivo</a>'
				});
			}
		});
	}
	
	// Procesar archivo
	function procesarArchivo() {
		if(!selectedFile) {
			Swal.fire({
				icon: 'warning',
				title: 'No hay archivo',
				text: 'Por favor selecciona un archivo primero'
			});
			return;
		}
		
		// Ir al paso de procesamiento
		irAPaso(3);
		
		// Preparar FormData
		const formData = new FormData();
		formData.append('planilla', selectedFile);
		
		// Simular progreso
		let progress = 0;
		const progressInterval = setInterval(() => {
			progress += Math.random() * 15;
			if(progress > 90) progress = 90;
			
			document.getElementById('progressBar').style.width = progress + '%';
			document.getElementById('progressBar').textContent = Math.round(progress) + '%';
			
			// Mensajes de progreso
			if(progress < 30) {
				document.getElementById('progressText').textContent = 'Leyendo archivo...';
			} else if(progress < 60) {
				document.getElementById('progressText').textContent = 'Validando datos...';
			} else {
				document.getElementById('progressText').textContent = 'Importando usuarios...';
			}
		}, 300);
		
		// Enviar archivo
		$.ajax({
			url: 'excel-importar-usuarios-procesar.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function(response) {
				clearInterval(progressInterval);
				document.getElementById('progressBar').style.width = '100%';
				document.getElementById('progressBar').textContent = '100%';
				document.getElementById('progressText').textContent = '¡Completado!';
				
				setTimeout(() => {
					mostrarResultados(response);
					irAPaso(4);
				}, 1000);
			},
			error: function(xhr, status, error) {
				clearInterval(progressInterval);
				console.error('Error:', error);
				console.error('Response:', xhr.responseText);
				
				Swal.fire({
					icon: 'error',
					title: 'Error al procesar',
					text: 'Hubo un error al importar los usuarios. Por favor verifica el archivo.',
					footer: '<a href="javascript:void(0)" onclick="irAPaso(2)">Volver a intentar</a>'
				});
			}
		});
	}
	
	function mostrarResultados(response) {
		// Estadísticas
		const stats = `
			<div class="stat-card success">
				<div class="stat-number">${response.exitosos || 0}</div>
				<div class="stat-label">Importados</div>
			</div>
			<div class="stat-card warning">
				<div class="stat-number">${response.omitidos || 0}</div>
				<div class="stat-label">Omitidos</div>
			</div>
			<div class="stat-card danger">
				<div class="stat-number">${response.errores || 0}</div>
				<div class="stat-label">Errores</div>
			</div>
			<div class="stat-card info">
				<div class="stat-number">${response.total || 0}</div>
				<div class="stat-label">Total Procesados</div>
			</div>
		`;
		
		document.getElementById('resultStats').innerHTML = stats;
		
		// Detalles
		if(response.detalles && response.detalles.length > 0) {
			let detallesHtml = `
				<h4 style="margin-bottom: 20px;"><i class="fa fa-list"></i> Detalles del Proceso</h4>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Fila</th>
								<th>Usuario</th>
								<th>Nombre</th>
								<th>Estado</th>
								<th>Mensaje</th>
							</tr>
						</thead>
						<tbody>
			`;
			
			response.detalles.forEach(detalle => {
				const badgeClass = detalle.estado === 'success' ? 'badge-success' : 
								   detalle.estado === 'warning' ? 'badge-warning' : 'badge-danger';
				const icon = detalle.estado === 'success' ? 'fa-check' : 
							detalle.estado === 'warning' ? 'fa-exclamation-triangle' : 'fa-times';
				
				detallesHtml += `
					<tr>
						<td>${detalle.fila}</td>
						<td>${detalle.usuario || 'N/A'}</td>
						<td>${detalle.nombre || 'N/A'}</td>
						<td><span class="badge ${badgeClass}"><i class="fa ${icon}"></i> ${detalle.estadoTexto}</span></td>
						<td>${detalle.mensaje}</td>
					</tr>
				`;
			});
			
			detallesHtml += `
						</tbody>
					</table>
				</div>
			`;
			
			document.getElementById('resultDetails').innerHTML = detallesHtml;
		}
	}
	
	function resetWizard() {
		selectedFile = null;
		fileInput.value = '';
		document.getElementById('fileSelected').classList.remove('show');
		dropZone.style.display = 'block';
		document.getElementById('btnProcesar').disabled = true;
		document.getElementById('validationStatus').innerHTML = '';
		document.getElementById('fileIcon').innerHTML = '<i class="fa fa-file-excel"></i>';
		document.getElementById('progressBar').style.width = '0%';
		document.getElementById('progressBar').textContent = '0%';
		irAPaso(1);
	}
</script>

</body>
</html>
