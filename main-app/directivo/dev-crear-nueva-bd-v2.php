<?php
include("session.php");

$idPaginaInterna = 'DV0001';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

try{
    $institucionesConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".instituciones 
    WHERE ins_estado = 1 AND ins_enviroment='".ENVIROMENT."'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
?>

<!-- Estilos modernos para el wizard -->
<style>
.wizard-container {
    max-width: 1200px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    overflow: hidden;
}

.wizard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
    color: white;
    text-align: center;
}

.wizard-header h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.wizard-header p {
    margin: 10px 0 0;
    opacity: 0.95;
    font-size: 16px;
}

.wizard-steps {
    display: flex;
    justify-content: space-between;
    padding: 0;
    margin: 0;
    list-style: none;
    background: #f8f9fa;
    border-bottom: 3px solid #e9ecef;
}

.wizard-step {
    flex: 1;
    text-align: center;
    padding: 25px 15px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.wizard-step:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -20px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 20px solid #e9ecef;
    border-top: 35px solid transparent;
    border-bottom: 35px solid transparent;
    z-index: 1;
}

.wizard-step.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.wizard-step.active:not(:last-child)::after {
    border-left-color: #764ba2;
}

.wizard-step.completed {
    background: #28a745;
    color: white;
}

.wizard-step.completed:not(:last-child)::after {
    border-left-color: #28a745;
}

.wizard-step-number {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    margin-bottom: 10px;
    font-weight: bold;
    font-size: 18px;
}

.wizard-step.active .wizard-step-number,
.wizard-step.completed .wizard-step-number {
    background: rgba(255,255,255,0.3);
}

.wizard-step-title {
    display: block;
    font-weight: 600;
    font-size: 14px;
    margin-top: 5px;
}

.wizard-content {
    padding: 40px;
    min-height: 500px;
}

.wizard-section {
    display: none;
    animation: fadeIn 0.5s ease;
}

.wizard-section.active {
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

.form-group-modern {
    margin-bottom: 30px;
}

.form-group-modern label {
    display: block;
    font-weight: 600;
    color: #344767;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group-modern .required-asterisk {
    color: #dc3545;
    margin-left: 4px;
}

.form-control-modern {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control-modern:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-control-modern.error {
    border-color: #dc3545;
}

.form-control-modern.success {
    border-color: #28a745;
}

.validation-message {
    display: none;
    margin-top: 8px;
    font-size: 13px;
    font-weight: 500;
}

.validation-message.error {
    display: block;
    color: #dc3545;
}

.validation-message.success {
    display: block;
    color: #28a745;
}

.validation-message.info {
    display: block;
    color: #17a2b8;
}

.input-icon-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.input-icon.show {
    opacity: 1;
}

.input-icon.loading {
    opacity: 1;
    color: #667eea;
}

.input-icon.success {
    opacity: 1;
    color: #28a745;
}

.input-icon.error {
    opacity: 1;
    color: #dc3545;
}

.wizard-buttons {
    display: flex;
    justify-content: space-between;
    padding: 30px 40px;
    background: #f8f9fa;
    border-top: 2px solid #e9ecef;
}

.btn-wizard {
    padding: 14px 32px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-wizard-prev {
    background: #6c757d;
    color: white;
}

.btn-wizard-prev:hover {
    background: #5a6268;
    transform: translateX(-3px);
}

.btn-wizard-next {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-wizard-next:hover {
    transform: translateX(3px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-wizard-submit {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.btn-wizard-submit:hover {
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-wizard:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.info-box {
    background: #e7f3ff;
    border-left: 4px solid #2196F3;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.info-box-icon {
    display: inline-block;
    margin-right: 10px;
    color: #2196F3;
}

.row-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.card-option {
    border: 3px solid #e9ecef;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.card-option:hover {
    border-color: #667eea;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
}

.card-option.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.card-option-icon {
    font-size: 48px;
    margin-bottom: 15px;
    color: #667eea;
}

.card-option-title {
    font-size: 20px;
    font-weight: 700;
    color: #344767;
    margin-bottom: 10px;
}

.card-option-description {
    font-size: 14px;
    color: #6c757d;
    line-height: 1.6;
}

.progress-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
    margin-top: 30px;
    display: none;
}

.progress-container.show {
    display: block;
}

.progress-bar-container {
    background: #e9ecef;
    border-radius: 10px;
    height: 30px;
    overflow: hidden;
    margin-bottom: 20px;
    position: relative;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.5s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 13px;
}

.progress-log {
    background: white;
    border-radius: 8px;
    padding: 20px;
    max-height: 300px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

.progress-log-item {
    padding: 8px;
    border-left: 3px solid #e9ecef;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.progress-log-item.success {
    border-left-color: #28a745;
    background: rgba(40, 167, 69, 0.05);
}

.progress-log-item.error {
    border-left-color: #dc3545;
    background: rgba(220, 53, 69, 0.05);
}

.progress-log-item.info {
    border-left-color: #17a2b8;
    background: rgba(23, 162, 184, 0.05);
}

.tooltip-info {
    display: inline-block;
    width: 20px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    background: #667eea;
    color: white;
    border-radius: 50%;
    font-size: 12px;
    font-weight: bold;
    cursor: help;
    margin-left: 5px;
}

.section-title {
    font-size: 24px;
    font-weight: 700;
    color: #344767;
    margin-bottom: 10px;
}

.section-subtitle {
    font-size: 15px;
    color: #6c757d;
    margin-bottom: 30px;
}

/* Animación de spinner */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner {
    animation: spin 1s linear infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .wizard-steps {
        flex-direction: column;
    }
    
    .wizard-step:not(:last-child)::after {
        display: none;
    }
    
    .wizard-content {
        padding: 20px;
    }
    
    .wizard-buttons {
        flex-direction: column;
        gap: 15px;
    }
    
    .row-modern {
        grid-template-columns: 1fr;
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
                    
                    <div class="wizard-container">
                        <div class="wizard-header">
                            <h1>🏢 Crear Nueva Institución o Renovar Año</h1>
                            <p>Proceso guiado paso a paso con validación en tiempo real</p>
                        </div>
                        
                        <ul class="wizard-steps">
                            <li class="wizard-step active" data-step="1">
                                <span class="wizard-step-number">1</span>
                                <span class="wizard-step-title">Tipo de Operación</span>
                            </li>
                            <li class="wizard-step" data-step="2">
                                <span class="wizard-step-number">2</span>
                                <span class="wizard-step-title">Datos Básicos</span>
                            </li>
                            <li class="wizard-step" data-step="3">
                                <span class="wizard-step-number">3</span>
                                <span class="wizard-step-title">Contacto Principal</span>
                            </li>
                            <li class="wizard-step" data-step="4">
                                <span class="wizard-step-number">4</span>
                                <span class="wizard-step-title">Confirmación</span>
                            </li>
                            <li class="wizard-step" data-step="5">
                                <span class="wizard-step-number">5</span>
                                <span class="wizard-step-title">Procesamiento</span>
                            </li>
                        </ul>
                        
                        <div class="wizard-content">
                            <!-- PASO 1: Tipo de operación -->
                            <div class="wizard-section active" data-section="1">
                                <h2 class="section-title">Selecciona el tipo de operación</h2>
                                <p class="section-subtitle">¿Deseas crear una nueva institución o renovar el año de una existente?</p>
                                
                                <div class="row-modern">
                                    <div class="card-option" data-option="nueva" onclick="selectOption('tipoInsti', '1')">
                                        <div class="card-option-icon">🆕</div>
                                        <h3 class="card-option-title">Nueva Institución</h3>
                                        <p class="card-option-description">
                                            Crear una institución completamente nueva con todos sus datos básicos, 
                                            configuraciones iniciales y usuario administrador.
                                        </p>
                                    </div>
                                    
                                    <div class="card-option" data-option="renovacion" onclick="selectOption('tipoInsti', '0')">
                                        <div class="card-option-icon">🔄</div>
                                        <h3 class="card-option-title">Renovar Año</h3>
                                        <p class="card-option-description">
                                            Copiar la información del año anterior al nuevo año académico
                                            para una institución ya existente (estudiantes, docentes, cursos, etc.)
                                        </p>
                                    </div>
                                </div>
                                
                                <input type="hidden" id="tipoInsti" name="tipoInsti" value="">
                            </div>
                            
                            <!-- PASO 2: Datos básicos -->
                            <div class="wizard-section" data-section="2">
                                <!-- Contenido para institución NUEVA -->
                                <div id="datosNuevaInstitucion" style="display:none;">
                                    <h2 class="section-title">Datos de la Nueva Institución</h2>
                                    <p class="section-subtitle">Información básica de la institución y configuración de base de datos</p>
                                    
                                    <div class="info-box">
                                        <span class="info-box-icon">ℹ️</span>
                                        <strong>Importante:</strong> Los nombres de las bases de datos se generarán automáticamente 
                                        con el formato: <code><?=BD_PREFIX?>{siglasBD}_{año}</code>
                                    </div>
                                    
                                    <div class="row-modern">
                                        <div class="form-group-modern">
                                            <label>
                                                Nombre de la Institución
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="input-icon-wrapper">
                                                <input type="text" 
                                                       class="form-control-modern" 
                                                       id="nombreInsti" 
                                                       name="nombreInsti" 
                                                       placeholder="Ej: Colegio San José"
                                                       autocomplete="off">
                                                <span class="input-icon"></span>
                                            </div>
                                            <div class="validation-message"></div>
                                        </div>
                                        
                                        <div class="form-group-modern">
                                            <label>
                                                Siglas de la Institución
                                                <span class="required-asterisk">*</span>
                                                <span class="tooltip-info" title="Nombre corto para identificar la institución">?</span>
                                            </label>
                                            <div class="input-icon-wrapper">
                                                <input type="text" 
                                                       class="form-control-modern" 
                                                       id="siglasInst" 
                                                       name="siglasInst" 
                                                       placeholder="Ej: CSJ"
                                                       autocomplete="off"
                                                       maxlength="10">
                                                <span class="input-icon"></span>
                                            </div>
                                            <div class="validation-message"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="row-modern">
                                        <div class="form-group-modern">
                                            <label>
                                                Siglas para Base de Datos
                                                <span class="required-asterisk">*</span>
                                                <span class="tooltip-info" title="Identificador único para las bases de datos. Solo letras minúsculas y números.">?</span>
                                            </label>
                                            <div class="input-icon-wrapper">
                                                <input type="text" 
                                                       class="form-control-modern" 
                                                       id="siglasBD" 
                                                       name="siglasBD" 
                                                       placeholder="Ej: csj"
                                                       autocomplete="off"
                                                       maxlength="20"
                                                       pattern="[a-z0-9_]+">
                                                <span class="input-icon"></span>
                                            </div>
                                            <div class="validation-message"></div>
                                            <small class="text-muted">Se creará como: <?=BD_PREFIX?><span id="bdPreview">___</span>_<?=date("Y")?></small>
                                        </div>
                                        
                                        <div class="form-group-modern">
                                            <label>
                                                Año a Crear
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="input-icon-wrapper">
                                                <input type="number" 
                                                       class="form-control-modern" 
                                                       id="yearN" 
                                                       name="yearN" 
                                                       value="<?=date("Y")?>"
                                                       min="2020" 
                                                       max="2050"
                                                       autocomplete="off">
                                                <span class="input-icon"></span>
                                            </div>
                                            <div class="validation-message"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contenido para RENOVACIÓN -->
                                <div id="datosRenovacion" style="display:none;">
                                    <h2 class="section-title">Renovación de Año Académico</h2>
                                    <p class="section-subtitle">Selecciona la institución y el año a crear</p>
                                    
                                    <div class="info-box">
                                        <span class="info-box-icon">ℹ️</span>
                                        <strong>Importante:</strong> Se copiarán los datos del año anterior al nuevo año: 
                                        estudiantes, docentes, cursos, materias, configuraciones, etc.
                                    </div>
                                    
                                    <div class="row-modern">
                                        <div class="form-group-modern">
                                            <label>
                                                Institución a Renovar
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="input-icon-wrapper">
                                                <select class="form-control-modern" 
                                                        id="idInsti" 
                                                        name="idInsti">
                                                    <option value="">Seleccione una institución...</option>
                                                    <?php
                                                    while($instituciones = mysqli_fetch_array($institucionesConsulta, MYSQLI_BOTH)){
                                                    ?>
                                                        <option value="<?=$instituciones['ins_id'];?>" 
                                                                data-bd="<?=$instituciones['ins_bd'];?>"
                                                                data-years="<?=$instituciones['ins_years'];?>">
                                                            <?=$instituciones['ins_nombre'];?> (<?=$instituciones['ins_siglas'];?>)
                                                        </option>
                                                    <?php }?>
                                                </select>
                                                <span class="input-icon"></span>
                                            </div>
                                            <div class="validation-message"></div>
                                        </div>
                                        
                                        <div class="form-group-modern">
                                            <label>
                                                Año a Crear
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="input-icon-wrapper">
                                                <input type="number" 
                                                       class="form-control-modern" 
                                                       id="yearA" 
                                                       name="yearA" 
                                                       value="<?=date("Y")?>"
                                                       min="2020" 
                                                       max="2050"
                                                       autocomplete="off">
                                                <span class="input-icon"></span>
                                            </div>
                                            <div class="validation-message"></div>
                                            <small class="text-muted" id="yearInfo"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PASO 3: Contacto principal (solo para nuevas) -->
                            <div class="wizard-section" data-section="3">
                                <h2 class="section-title">Contacto Principal</h2>
                                <p class="section-subtitle">Datos del usuario administrador de la institución</p>
                                
                                <div class="info-box">
                                    <span class="info-box-icon">👤</span>
                                    <strong>Usuario Administrador:</strong> Esta persona tendrá acceso completo al sistema 
                                    y recibirá las credenciales por correo electrónico.
                                </div>
                                
                                <div class="row-modern">
                                    <div class="form-group-modern">
                                        <label>
                                            Tipo de Documento
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <select class="form-control-modern" id="tipoDoc" name="tipoDoc">
                                            <option value="">Seleccione...</option>
                                            <?php
                                            $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales
                                            WHERE ogen_grupo=1");
                                            while($o = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                echo '<option value="'.$o['ogen_id'].'">'.$o['ogen_nombre'].'</option>';
                                            }
                                            ?>
                                        </select>
                                        <div class="validation-message"></div>
                                    </div>
                                    
                                    <div class="form-group-modern">
                                        <label>
                                            Número de Documento
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <div class="input-icon-wrapper">
                                            <input type="text" 
                                                   class="form-control-modern" 
                                                   id="documento" 
                                                   name="documento" 
                                                   placeholder="Ej: 1234567890"
                                                   autocomplete="off">
                                            <span class="input-icon"></span>
                                        </div>
                                        <div class="validation-message"></div>
                                    </div>
                                </div>
                                
                                <div class="row-modern">
                                    <div class="form-group-modern">
                                        <label>
                                            Primer Nombre
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control-modern" 
                                               id="nombre1" 
                                               name="nombre1" 
                                               placeholder="Ej: Juan"
                                               autocomplete="off">
                                        <div class="validation-message"></div>
                                    </div>
                                    
                                    <div class="form-group-modern">
                                        <label>Segundo Nombre</label>
                                        <input type="text" 
                                               class="form-control-modern" 
                                               id="nombre2" 
                                               name="nombre2" 
                                               placeholder="Ej: Carlos"
                                               autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="row-modern">
                                    <div class="form-group-modern">
                                        <label>
                                            Primer Apellido
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control-modern" 
                                               id="apellido1" 
                                               name="apellido1" 
                                               placeholder="Ej: Pérez"
                                               autocomplete="off">
                                        <div class="validation-message"></div>
                                    </div>
                                    
                                    <div class="form-group-modern">
                                        <label>Segundo Apellido</label>
                                        <input type="text" 
                                               class="form-control-modern" 
                                               id="apellido2" 
                                               name="apellido2" 
                                               placeholder="Ej: González"
                                               autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="row-modern">
                                    <div class="form-group-modern">
                                        <label>
                                            Correo Electrónico
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <div class="input-icon-wrapper">
                                            <input type="email" 
                                                   class="form-control-modern" 
                                                   id="email" 
                                                   name="email" 
                                                   placeholder="Ej: admin@institucion.edu.co"
                                                   autocomplete="off">
                                            <span class="input-icon"></span>
                                        </div>
                                        <div class="validation-message"></div>
                                    </div>
                                    
                                    <div class="form-group-modern">
                                        <label>Celular</label>
                                        <input type="tel" 
                                               class="form-control-modern" 
                                               id="celular" 
                                               name="celular" 
                                               placeholder="Ej: 3001234567"
                                               autocomplete="off">
                                        <div class="validation-message"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PASO 4: Confirmación -->
                            <div class="wizard-section" data-section="4">
                                <h2 class="section-title">Confirmación de Datos</h2>
                                <p class="section-subtitle">Verifica que toda la información sea correcta antes de continuar</p>
                                
                                <div id="confirmacionResumen" style="background: #f8f9fa; border-radius: 12px; padding: 30px;">
                                    <!-- Se llenará dinámicamente con JavaScript -->
                                </div>
                                
                                <div class="info-box" style="margin-top: 30px;">
                                    <span class="info-box-icon">⚠️</span>
                                    <strong>Advertencia:</strong> Una vez iniciado el proceso, no podrá ser cancelado. 
                                    Asegúrate de que todos los datos sean correctos.
                                </div>
                            </div>
                            
                            <!-- PASO 5: Procesamiento -->
                            <div class="wizard-section" data-section="5">
                                <h2 class="section-title">Procesando...</h2>
                                <p class="section-subtitle">Por favor espera mientras se crea la institución / año académico</p>
                                
                                <div class="progress-container show">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" id="progressBar" style="width: 0%;">0%</div>
                                    </div>
                                    
                                    <div class="progress-log" id="progressLog">
                                        <div class="progress-log-item info">
                                            <span>ℹ️</span>
                                            <span>Iniciando proceso...</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="resultadoFinal" style="display: none; margin-top: 30px;">
                                    <!-- Se mostrará el resultado final -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="wizard-buttons">
                            <button type="button" class="btn-wizard btn-wizard-prev" id="btnPrev" onclick="previousStep()" style="display: none;">
                                <i class="fa fa-arrow-left"></i>
                                Anterior
                            </button>
                            
                            <div style="flex: 1;"></div>
                            
                            <button type="button" class="btn-wizard btn-wizard-next" id="btnNext" onclick="nextStep()" disabled>
                                Siguiente
                                <i class="fa fa-arrow-right"></i>
                            </button>
                            
                            <button type="button" class="btn-wizard btn-wizard-submit" id="btnSubmit" onclick="procesarCreacion()" style="display: none;">
                                <i class="fa fa-check-circle"></i>
                                Crear y Finalizar
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <!-- end page content -->
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    
    <!-- Scripts -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js"></script>
    <script src="../../config-general/assets/js/theme-color.js"></script>
    
    <script src="dev-crear-nueva-bd-v2.js"></script>
</body>
</html>

