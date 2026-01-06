<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0077';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php require_once("../class/Sysjobs.php");
Utilidades::validarParametros($_GET);
if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>


	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title">Importar estudiantes desde Excel</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="estudiantes.php" onClick="deseaRegresar(this)"><?=$frases[78][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Importar estudiantes desde Excel</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
						
						<div class="col-sm-3">
							<div class="panel">
								<header class="panel-heading panel-heading-blue">Paso a paso</header>
									<div class="panel-body">
                                        <p><b>1.</b> Descargue la plantilla de excel (Google Sheet) en este enlace. <a href="https://docs.google.com/spreadsheets/d/1-wXDDDzMJAYt_ppWnJ79cyCqcn_TSf_T/edit#gid=845392206" target="_blank" class="btn btn-xs btn-secondary">DESCARGAR PLANTILLA</a></p>
                                        <p><b>2.</b> Llene los campos de los estudiantes y acudientes en el orden que la plantilla los solicita.</p>
                                        <p><b>3.</b> Finalmente guarde la plantilla ya completada, carguela en el campo que dice <mark>Subir la planilla lista</mark> y dele click al botón importar matrículas.</p>
                                        <p><b>4.</b> Si desea puede ver el video de ayuda que hemos preparada para usted. <a href="https://www.loom.com/share/40b97dc0aa4040f18c183d4f366921cc" target="_blank" class="btn btn-xs btn-secondary">VER VIDEO DE AYUDA</a></p>
									</div>
							 </div>

                             <div class="panel">
                                    <header class="panel-heading panel-heading-blue">Consideraciones</header>
									<div class="panel-body">
                                        <p><b>-></b> Tenga en cuenta, para importar los estudiantes, los campos del Nro. de documento, Primer Nombre, Primer Apellido y grado, son obligatorios.</p>
                                        <p><b>-></b> Si el estudiante ya existe en la plataforma, usted puede seleccionar los campos que desea actualizar en el campo que dice <mark>Campos a actualizar</mark>. Si no selecciona ningun campo entonces los estudiantes ya existentes se omitirán y solo se ingresarán los que no existan en la plataforma.</p>
									</div>
							 </div>

                        </div>
                        <div  class="col-sm-9" >
                            <div class="col-sm-12">
                                    <?php include("../../config-general/mensajes-informativos.php"); ?>
                                    <div class="panel">
                                        <header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">

                                    
                                        <form name="formularioGuardar" id="excelImportForm" method="post" enctype="multipart/form-data">
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">Subir la planilla lista</label>
                                                <div class="col-sm-6">
                                                    <input type="file" class="form-control" name="planilla" id="excelFile" accept=".xlsx" required>
                                                    <small class="text-muted">Solo archivos Excel (.xlsx)</small>
                                                    <div id="fileInfo" style="display: none; margin-top: 10px;">
                                                        <div class="alert alert-info">
                                                            <strong>Archivo seleccionado:</strong> <span id="fileName"></span><br>
                                                            <strong>Filas de datos:</strong> <span id="rowCount"></span><br>
                                                            <strong>Tamaño:</strong> <span id="fileSize"></span>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-info" id="previewBtn">
                                                            <i class="fa fa-eye"></i> Ver preview
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">Campos a actualizar</label>
                                                <div class="col-sm-9">
                                                    <select id="multiple" class="form-control select2-multiple" name="actualizarCampo[]" multiple>
                                                        <option value="">Seleccione una opción</option>
                                                        <option value="1">Grado</option>
                                                        <option value="2">Grupo</option>
                                                        <option value="3">Tipo de Documento</option>
                                                        <option value="4">Acudiente</option>
                                                        <option value="5">Segundo nombre del estudiante</option>
                                                        <option value="6">Fecha de nacimiento</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">Estado de matrícula <span class="text-danger">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="estadoMatricula" id="estadoMatricula" required style="max-width: 300px;">
                                                        <option value="1" selected>Matriculado</option>
                                                        <option value="4">No matriculado</option>
                                                    </select>
                                                    <small class="form-text text-muted">Seleccione el estado de matrícula para los estudiantes a importar</small>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">Modo de procesamiento <span class="text-danger">*</span></label>
                                                <div class="col-sm-9">
                                                    <div class="radio">
                                                        <label style="cursor: pointer;">
                                                            <input type="radio" name="modoProcesamiento" value="inmediato" checked style="margin-right: 8px;">
                                                            <strong>Procesar inmediatamente</strong> - Se procesará ahora con barra de progreso
                                                        </label>
                                                    </div>
                                                    <div class="radio" style="margin-bottom: 10px;">
                                                        <label style="cursor: not-allowed; opacity: 0.6;">
                                                            <input type="radio" name="modoProcesamiento" value="job" disabled style="margin-right: 8px;">
                                                            <strong>Procesar después (Job)</strong> - <em>Temporalmente deshabilitado</em>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <a href="javascript:void(0);" name="estudiantes.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

                                            <button type="button" class="btn deepPink-bgcolor" id="btnImportar">
                                                <span id="btnText">Importar matrículas</span> <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                            </button>
                                        </form>

                                        <!-- Barra de progreso para procesamiento inmediato -->
                                        <div id="progressContainer" style="display: none; margin-top: 20px;">
                                            <div class="panel">
                                                <div class="panel-body">
                                                    <h5>Procesando archivo Excel...</h5>
                                                    <div class="progress">
                                                        <div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
                                                            <span id="progressText">0%</span>
                                                        </div>
                                                    </div>
                                                    <div id="progressDetails" style="margin-top: 10px;">
                                                        <small class="text-muted">Preparando archivo...</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de preview del archivo -->
                                        <div id="previewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Preview del archivo Excel</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="previewContent">
                                                            <!-- Contenido del preview -->
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de resultados -->
                                        <div id="resultsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Resultados de la importación</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="resultsContent">
                                                            <!-- Contenido dinámico con resultados -->
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                        <a href="estudiantes.php" class="btn btn-success">
                                                            <i class="fa fa-list"></i> Volver al Listado
                                                        </a>
                                                        <button type="button" class="btn btn-primary" onclick="location.reload()">
                                                            <i class="fa fa-refresh"></i> Procesar otro archivo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">                                   
                                    <div class="panel">
                                        <header class="panel-heading panel-heading-purple">Solicitudes de Importacion </header>
                                        <?php
												
                                                $parametrosBuscar = array(
                                                    "tipo" =>JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL,
                                                    "responsable" => $_SESSION['id'],
                                                    "agno"=>$config['conf_agno'],
                                                    "estado" =>JOBS_ESTADO_PENDIENTE
                                                );										
                                                $listadoCrobjobs=SysJobs::listar($parametrosBuscar);
                                        ?>
                                               
                                        <div class="card-body">

                                                        <div >
                                                            <table id="example1"  style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Cod</th>
                                                                        <th>Fecha</th>
                                                                        <th>mensaje</th>
                                                                        <th>Estado</th>
                                                                    </tr>
                                                                </thead>
                                                        <tbody>
                                                           <?php $contReg = 1;
                                                                    while ($resultado = mysqli_fetch_array($listadoCrobjobs, MYSQLI_BOTH)) {?>
                                                                        <tr>
                                                                            <td><?= $contReg; ?></td>
                                                                            <td><?= $resultado['job_id']; ?></td>
                                                                            <td><?= $resultado['job_fecha_creacion']; ?></td>
                                                                            <td><?= $resultado['job_mensaje']; ?></td> 
                                                                            <td> <?= $resultado['job_estado']; ?></td>
                                                                        </tr>
                                                                    <?php $contReg++;
                                                                    } ?>
                                                         </tbody>
                                                     </table>
                                                </div>
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
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    
    <!-- SheetJS para validación de archivos Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <!-- CSS específico para radio buttons -->
    <style>
        input[name="modoProcesamiento"] {
            opacity: 1 !important;
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        input[name="modoProcesamiento"]:disabled {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        .radio label {
            cursor: pointer !important;
        }
        .radio input[type="radio"] {
            margin-right: 8px !important;
            cursor: pointer !important;
        }
    </style>
    
    <!-- JavaScript para importación de Excel -->
    <script>
    $(document).ready(function() {
        console.log('Script de importación Excel cargado');
        const $form = $('#excelImportForm');
        console.log('Formulario encontrado:', $form.length);
        const $fileInput = $('#excelFile');
        const $progressContainer = $('#progressContainer');
        const $progressBar = $('#progressBar');
        const $progressText = $('#progressText');
        const $progressDetails = $('#progressDetails');
        const $btnImportar = $('#btnImportar');
        const $btnText = $('#btnText');
        const $resultsModal = $('#resultsModal');
        const $resultsContent = $('#resultsContent');
        const $fileInfo = $('#fileInfo');
        const $fileName = $('#fileName');
        const $rowCount = $('#rowCount');
        const $fileSize = $('#fileSize');
        const $previewBtn = $('#previewBtn');
        const $previewModal = $('#previewModal');
        const $previewContent = $('#previewContent');
        
        let currentFileData = null;

        // Asegurar que los radio buttons estén habilitados
        function ensureRadioButtonsEnabled() {
            $('input[name="modoProcesamiento"]').prop('disabled', false);
            $('input[name="modoProcesamiento"]').removeAttr('disabled');
            console.log('Radio buttons habilitados:', $('input[name="modoProcesamiento"]').length);
        }

        // Ejecutar al cargar la página
        ensureRadioButtonsEnabled();

        // Asegurar que se mantengan habilitados después de cambios
        $form.on('change', function() {
            ensureRadioButtonsEnabled();
        });

        // Prevenir envío tradicional del formulario
        $form.on('submit', function(e) {
            console.log('Formulario intentando enviarse tradicionalmente - bloqueando');
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        // También ejecutar después de un tiempo para asegurar que otros scripts no los deshabiliten
        setTimeout(ensureRadioButtonsEnabled, 1000);
        setTimeout(ensureRadioButtonsEnabled, 2000);

        // Eventos específicos para los radio buttons
        $('input[name="modoProcesamiento"]').on('click', function() {
            console.log('Radio button clickeado:', $(this).val());
            ensureRadioButtonsEnabled();
        });

        // Evento para los labels también
        $('.radio label').on('click', function() {
            const radio = $(this).find('input[type="radio"]');
            if (radio.length) {
                radio.prop('checked', true);
                console.log('Label clickeado, radio seleccionado:', radio.val());
            }
        });

        // Función para formatear tamaño de archivo
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Manejo del botón de preview
        $previewBtn.on('click', function() {
            if (currentFileData) {
                showPreview();
            }
        });

        // Función para convertir fechas de Excel
        function convertExcelDate(excelDate) {
            if (!excelDate || excelDate === '') return '';
            
            // Si ya es una fecha válida en formato string, devolverla
            if (typeof excelDate === 'string' && excelDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
                return excelDate;
            }
            
            // Si es un número (fecha serial de Excel)
            if (typeof excelDate === 'number') {
                // Excel cuenta días desde 1900-01-01, pero tiene un bug con el año 1900
                const excelEpoch = new Date(1900, 0, 1);
                const date = new Date(excelEpoch.getTime() + (excelDate - 2) * 24 * 60 * 60 * 1000);
                
                // Verificar que la fecha es válida
                if (isNaN(date.getTime())) return '';
                
                // Formatear como YYYY-MM-DD
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                
                return `${year}-${month}-${day}`;
            }
            
            return '';
        }

        // Función para mostrar preview
        function showPreview() {
            if (!currentFileData) return;
            
            let html = '<div class="table-responsive">';
            html += '<table class="table table-striped table-bordered">';
            html += '<thead class="thead-dark">';
            html += '<tr>';
            
            // Cabeceras completas A-Q
            currentFileData.headers.forEach((header, index) => {
                const columnLetter = String.fromCharCode(65 + index); // A, B, C, etc.
                html += `<th title="Columna ${columnLetter}">${header || ''}</th>`;
            });
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            
            // Mostrar datos estructurados (solo filas válidas)
            if (currentFileData.structuredData && currentFileData.structuredData.length > 0) {
                currentFileData.structuredData.forEach((row, index) => {
                    html += '<tr>';
                    row.forEach(cell => {
                        html += `<td>${cell || ''}</td>`;
                    });
                    html += '</tr>';
                });
            } else {
                html += '<tr><td colspan="17" class="text-center text-muted">No hay datos válidos para mostrar</td></tr>';
            }
            
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            
            if (currentFileData.rowCount > 10) {
                html += `<p class="text-muted">Mostrando las primeras 10 filas válidas de ${currentFileData.rowCount} filas con datos completos.</p>`;
            } else if (currentFileData.rowCount > 0) {
                html += `<p class="text-muted">Mostrando ${currentFileData.rowCount} filas válidas encontradas.</p>`;
            }
            
            $previewContent.html(html);
            $previewModal.modal('show');
        }

        // Validación del archivo Excel
        $fileInput.on('change', function() {
            const file = this.files[0];
            if (!file) return;

            // Limpiar información anterior
            $fileInfo.hide();
            currentFileData = null;
            
            // Validar extensión
            const extension = file.name.split('.').pop().toLowerCase();
            if (extension !== 'xlsx') {
                alert('Por favor seleccione un archivo Excel (.xlsx)');
                this.value = '';
                return;
            }

            // Validar tamaño (máximo 10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 10MB permitido.');
                this.value = '';
                return;
            }

            // Leer cabeceras del Excel para validar
            validateExcelHeaders(file);
        });

        // Función para validar cabeceras del Excel
        function validateExcelHeaders(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    
                    // Convertir a JSON para obtener las cabeceras
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    
                    if (jsonData.length < 3) {
                        alert('El archivo Excel debe tener al menos 3 filas (cabecera en fila 2 + datos)');
                        $fileInput.val('');
                        return;
                    }
                    
                    const headers = jsonData[1]; // Segunda fila son las cabeceras (índice 1)
                    const requiredHeaders = [
                        'Tipo de Documento',    // A
                        'Documento',            // B  
                        'Primer Nombre',         // C
                        'Primer Apellido',       // E
                        'Grado'                  // I
                    ];
                    
                    // Columnas opcionales del estudiante (A-Q)
                    const optionalStudentHeaders = [
                        'Segundo Nombre',        // D
                        'Segundo Apellido',      // F
                        'Genero',                // G
                        'Fecha Nacimiento',      // H
                        'Grupo',                 // J
                        'Direccion',             // K
                        'Barrio',                // L
                        'Celular',               // M
                        'Email',                 // N
                        'Estrato',               // O
                        'Grupo Sanguineo',       // P
                        'EPS'                    // Q
                    ];
                    
                    // Columnas opcionales del acudiente (R-V)
                    const optionalGuardianHeaders = [
                        'Documento Acudiente',      // R
                        'Primer Nombre Acudiente',   // S
                        'Primer Apellido Acudiente', // T
                        'Telefono Acudiente',       // U
                        'Email Acudiente'           // V
                    ];
                    
                    const missingHeaders = requiredHeaders.filter(required => 
                        !headers.some(header => header && header.toString().trim() === required)
                    );
                    
                    if (missingHeaders.length > 0) {
                        alert('Faltan las siguientes cabeceras requeridas:\n' + missingHeaders.join(', '));
                        $fileInput.val('');
                        return;
                    }
                    
                    // Validar que hay datos (más de 2 filas: fila 1 vacía + fila 2 cabeceras + datos)
                    if (jsonData.length < 3) {
                        alert('El archivo no contiene datos para procesar');
                        $fileInput.val('');
                        return;
                    }
                    
                    // Detectar automáticamente las filas válidas (con campos obligatorios A,B,C,E)
                    let validRows = 0;
                    const validRowIndices = [];
                    
                    for (let i = 2; i < jsonData.length; i++) {
                        const row = jsonData[i];
                        // Una fila es válida si tiene los campos obligatorios:
                        // A: Tipo de Documento (opcional), B: Documento, C: Primer Nombre, E: Primer Apellido
                        if (row[1] && row[2] && row[4]) { // B: Documento, C: Primer Nombre, E: Primer Apellido
                            validRows++;
                            validRowIndices.push(i);
                        }
                    }
                    
                    console.log('Archivo Excel válido:', file.name);
                    console.log('Cabeceras encontradas:', headers);
                    console.log('Filas de datos válidas:', validRows);
                    
                    // Crear estructura de datos completa para vista previa (columnas A-Q)
                    const allHeaders = [
                        'Tipo de Documento',    // A
                        'Documento',            // B  
                        'Primer Nombre',         // C
                        'Segundo Nombre',        // D
                        'Primer Apellido',       // E
                        'Segundo Apellido',      // F
                        'Genero',                // G
                        'Fecha Nacimiento',      // H
                        'Grado',                 // I
                        'Grupo',                 // J
                        'Direccion',             // K
                        'Barrio',                // L
                        'Celular',               // M
                        'Email',                 // N
                        'Estrato',               // O
                        'Grupo Sanguineo',       // P
                        'EPS'                    // Q
                    ];
                    
                    // Crear datos estructurados para vista previa
                    const structuredData = [];
                    for (let i = 0; i < Math.min(10, validRowIndices.length); i++) {
                        const rowIndex = validRowIndices[i];
                        const originalRow = jsonData[rowIndex];
                        const structuredRow = [];
                        
                        // Mapear datos a las columnas A-Q (índices 0-16)
                        for (let col = 0; col < 17; col++) {
                            let cellValue = originalRow[col] || '';
                            
                            // Convertir fecha si es la columna H (índice 7)
                            if (col === 7 && cellValue) { // Columna H: Fecha Nacimiento
                                cellValue = convertExcelDate(cellValue);
                            }
                            
                            structuredRow[col] = cellValue;
                        }
                        
                        structuredData.push(structuredRow);
                    }
                    
                    // Guardar datos del archivo para preview
                    currentFileData = {
                        fileName: file.name,
                        headers: allHeaders,
                        data: jsonData,
                        structuredData: structuredData,
                        validRowIndices: validRowIndices,
                        rowCount: validRows,
                        fileSize: formatFileSize(file.size)
                    };
                    
                    // Mostrar información del archivo
                    $fileName.text(file.name);
                    $rowCount.text(validRows);
                    $fileSize.text(formatFileSize(file.size));
                    $fileInfo.show();
                    
                } catch (error) {
                    alert('Error al leer el archivo Excel. Verifique que el archivo no esté corrupto.');
                    console.error('Error:', error);
                    $fileInput.val('');
                }
            };
            reader.readAsArrayBuffer(file);
        }

        // Manejo del click del botón de importar
        $btnImportar.on('click', function(e) {
            console.log('Botón de importar clickeado');
            e.preventDefault();
            e.stopPropagation();
            
            // Validar que se haya seleccionado un archivo
            const file = $fileInput[0].files[0];
            if (!file) {
                alert('Por favor seleccione un archivo Excel');
                return false;
            }
            
            // Validar que se haya seleccionado un modo de procesamiento
            const modoProcesamiento = $('input[name="modoProcesamiento"]:checked').val();
            console.log('Modo seleccionado:', modoProcesamiento);
            
            if (!modoProcesamiento) {
                alert('Por favor seleccione un modo de procesamiento');
                return false;
            }
            
            // Validar estado de matrícula
            const estadoMatricula = $('#estadoMatricula').val();
            if (!estadoMatricula) {
                alert('Por favor seleccione el estado de matrícula');
                return false;
            }
            
            // Validar que no se intente usar el modo job (deshabilitado)
            if (modoProcesamiento === 'job') {
                alert('El modo "Procesar después (Job)" está temporalmente deshabilitado. Por favor seleccione "Procesar inmediatamente".');
                return false;
            }
            
            console.log('Iniciando procesamiento inmediato');
            // Procesamiento inmediato con AJAX
            processImmediately();
            
            return false; // Prevenir cualquier envío adicional
        });

        // Función para procesamiento con job (asíncrono)
        function processWithJob() {
            const formData = new FormData($form[0]);
            
            // Debug: Verificar qué datos se están enviando
            console.log('Datos del formulario (job):');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            formData.append('modo', 'job');

            // Mostrar barra de progreso
            showProgress();
            
            // Deshabilitar botón
            $btnImportar.prop('disabled', true);
            $btnText.text('Creando job...');

            // Enviar datos
            fetch('ajax-excel-importar-estudiantes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    hideProgress();
                    showJobSuccess(data.message, data.jobId);
                } else {
                    hideProgress();
                    alert('Error: ' + data.message);
                    resetForm();
                }
            })
            .catch(error => {
                hideProgress();
                console.error('Error:', error);
                alert('Error de conexión. Intente nuevamente.');
                resetForm();
            });
        }

        // Función para procesamiento inmediato con progreso simulado
        function processImmediately() {
            console.log('Iniciando processImmediately()');
            const formData = new FormData($form[0]);
            
            // Debug: Verificar qué datos se están enviando
            console.log('Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            formData.append('modo', 'inmediato');

            // Mostrar barra de progreso
            console.log('Mostrando barra de progreso');
            showProgress();
            
            // Deshabilitar botón
            $btnImportar.prop('disabled', true);
            $btnText.text('Procesando...');

            // Simular progreso mientras se procesa
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                
                if (progress < 30) {
                    updateProgress(progress, 'Validando archivo...');
                } else if (progress < 60) {
                    updateProgress(progress, 'Procesando datos...');
                } else {
                    updateProgress(progress, 'Guardando información...');
                }
            }, 200);

            console.log('Enviando fetch request');
            // Enviar datos con fetch
            fetch('ajax-excel-importar-estudiantes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Respuesta recibida:', response);
                console.log('Status:', response.status);
                console.log('Headers:', response.headers);
                clearInterval(progressInterval);
                updateProgress(95, 'Finalizando...');
                
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                
                // Verificar el Content-Type
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (!contentType || !contentType.includes('application/json')) {
                    // Si no es JSON, leer como texto para debug
                    return response.text().then(text => {
                        console.log('Respuesta como texto:', text);
                        throw new Error('Respuesta no es JSON válido');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                updateProgress(100, 'Completado');
                
                setTimeout(() => {
                    if (data.success) {
                        hideProgress();
                        showResults(data.results);
                    } else {
                        hideProgress();
                        alert('Error: ' + data.message);
                        resetForm();
                    }
                }, 500);
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                clearInterval(progressInterval);
                hideProgress();
                console.error('Error completo:', error);
                
                // Mostrar error más específico
                let errorMessage = 'Error de conexión. Intente nuevamente.';
                if (error.message.includes('JSON')) {
                    errorMessage = 'Error: El servidor no devolvió una respuesta válida.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'Error: Archivo ajax-excel-importar-estudiantes.php no encontrado.';
                } else if (error.message.includes('500')) {
                    errorMessage = 'Error interno del servidor.';
                }
                
                alert(errorMessage + '\n\nDetalles en consola (F12)');
                resetForm();
            });
        }

        // Función para procesar con barra de progreso
        function processWithProgress(jobId) {
            const startTime = Date.now() / 1000; // Tiempo de inicio en segundos
            
            const checkProgress = () => {
                fetch('ajax-excel-progress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=getProgress&jobId=${jobId}&startTime=${startTime}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateProgress(data.progress, data.message);
                        
                        if (data.completed) {
                            checkJobStatus(jobId, startTime);
                        } else {
                            // Continuar verificando cada segundo
                            setTimeout(checkProgress, 1000);
                        }
                    } else {
                        hideProgress();
                        alert('Error: ' + data.message);
                        resetForm();
                    }
                })
                .catch(error => {
                    hideProgress();
                    console.error('Error:', error);
                    alert('Error al verificar el progreso.');
                    resetForm();
                });
            };
            
            // Iniciar verificación de progreso
            checkProgress();
        }

        // Función para verificar estado del job
        function checkJobStatus(jobId, startTime) {
            fetch('ajax-excel-progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=checkJobStatus&jobId=${jobId}&startTime=${startTime}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.completed) {
                    hideProgress();
                    showResults(data.results);
                } else if (data.success && !data.completed) {
                    // Reintentar en 2 segundos
                    setTimeout(() => checkJobStatus(jobId, startTime), 2000);
                } else {
                    hideProgress();
                    alert('Error: ' + data.message);
                    resetForm();
                }
            })
            .catch(error => {
                hideProgress();
                console.error('Error:', error);
                alert('Error al verificar el estado del procesamiento.');
                resetForm();
            });
        }

        // Función para mostrar barra de progreso
        function showProgress() {
            console.log('Ejecutando showProgress()');
            console.log('Progress container:', $progressContainer.length);
            $progressContainer.show();
            updateProgress(0, 'Preparando archivo...');
        }

        // Función para ocultar barra de progreso
        function hideProgress() {
            $progressContainer.hide();
        }

        // Función para actualizar progreso
        function updateProgress(percent, message) {
            $progressBar.css('width', percent + '%');
            $progressText.text(Math.round(percent) + '%');
            $progressDetails.html('<small class="text-muted">' + message + '</small>');
        }

        // Función para mostrar resultados
        function showResults(results) {
            let html = '<div class="alert alert-success">';
            html += '<h6><i class="fa fa-check-circle"></i> Procesamiento completado</h6>';
            html += '<hr>';
            
            // Sección de Estudiantes
            html += '<h6 class="mt-3"><i class="fa fa-graduation-cap"></i> Estudiantes Importados</h6>';
            html += '<div class="row">';
            html += '<div class="col-md-4"><strong>Creados:</strong> ' + results.created + '</div>';
            html += '<div class="col-md-4"><strong>Actualizados:</strong> ' + results.updated + '</div>';
            html += '<div class="col-md-4"><strong>Errores:</strong> ' + results.errors + '</div>';
            html += '</div>';
            
            // Sección de Usuarios Estudiantes
            if (results.usuariosEstudiantes) {
                html += '<hr>';
                html += '<h6 class="mt-3"><i class="fa fa-user"></i> Usuarios de Estudiantes</h6>';
                html += '<div class="row">';
                html += '<div class="col-md-6"><strong>Nuevos creados:</strong> ' + (results.usuariosEstudiantes.creados || 0) + '</div>';
                html += '<div class="col-md-6"><strong>Reutilizados:</strong> ' + (results.usuariosEstudiantes.reutilizados || 0) + '</div>';
                html += '</div>';
            }
            
            // Sección de Usuarios Acudientes
            if (results.usuariosAcudientes) {
                html += '<hr>';
                html += '<h6 class="mt-3"><i class="fa fa-users"></i> Usuarios de Acudientes</h6>';
                html += '<div class="row">';
                html += '<div class="col-md-4"><strong>Nuevos creados:</strong> ' + (results.usuariosAcudientes.creados || 0) + '</div>';
                html += '<div class="col-md-4"><strong>Reutilizados:</strong> ' + (results.usuariosAcudientes.reutilizados || 0) + '</div>';
                html += '<div class="col-md-4"><strong>Sin datos:</strong> ' + (results.usuariosAcudientes.omitidos || 0) + '</div>';
                html += '</div>';
            }
            
            // Sección de Relaciones
            if (results.relaciones !== undefined) {
                html += '<hr>';
                html += '<h6 class="mt-3"><i class="fa fa-link"></i> Relaciones Estudiante-Acudiente</h6>';
                html += '<p><strong>Relaciones creadas:</strong> ' + results.relaciones + '</p>';
            }
            
            html += '</div>';

            if (results.errorDetails && results.errorDetails.length > 0) {
                html += '<div class="alert alert-warning mt-3">';
                html += '<h6><i class="fa fa-exclamation-triangle"></i> Detalles de errores y advertencias:</h6>';
                html += '<ul style="max-height: 300px; overflow-y: auto;">';
                results.errorDetails.forEach(error => {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }

            $resultsContent.html(html);
            $resultsModal.modal('show');
            resetForm();
        }

        // Función para mostrar éxito del job
        function showJobSuccess(message, jobId) {
            let html = '<div class="alert alert-info">';
            html += '<h6><i class="fa fa-clock-o"></i> Job creado exitosamente</h6>';
            html += '<p><strong>Mensaje:</strong> ' + message + '</p>';
            html += '<p><strong>ID del Job:</strong> ' + jobId + '</p>';
            html += '<p class="text-muted">El procesamiento se realizará en segundo plano. Puede verificar el estado en la tabla de solicitudes de importación.</p>';
            html += '</div>';

            $resultsContent.html(html);
            $resultsModal.modal('show');
            resetForm();
        }

        // Función para resetear formulario
        function resetForm() {
            $btnImportar.prop('disabled', false);
            $btnText.text('Importar matrículas');
        }
    });
    </script>
    
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>