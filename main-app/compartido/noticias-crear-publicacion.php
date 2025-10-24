<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0005';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Publicación - <?=$informacion_inst["info_nombre"]?></title>
    
    <!-- CSS del sistema -->
    <link href="../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet">
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .modern-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .modern-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .modern-header-content {
            flex: 1;
        }
        
        .modern-header-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .modern-header-subtitle {
            font-size: 15px;
            opacity: 0.9;
        }
        
        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .modern-content {
            padding: 40px;
        }
        
        .form-section {
            margin-bottom: 35px;
        }
        
        .section-header {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .section-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        .form-field {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-label .required {
            color: #ef4444;
            margin-left: 4px;
        }
        
        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.2s;
            font-family: inherit;
        }
        
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-help {
            font-size: 13px;
            color: #64748b;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .form-file-input {
            padding: 12px 16px;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .form-file-input:hover {
            border-color: #667eea;
            background: #f8f9fe;
        }
        
        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-selection--single {
            border: 2px solid #e2e8f0 !important;
            border-radius: 10px !important;
            min-height: 48px !important;
            padding: 4px !important;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #667eea !important;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
        }
        
        .modern-footer {
            padding: 30px 40px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }
        
        .btn-modern {
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-cancel {
            background: white;
            color: #475569;
            border: 2px solid #cbd5e1;
        }
        
        .btn-cancel:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 30px 0;
        }
        
        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .info-box-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 6px;
        }
        
        .info-box-text {
            font-size: 13px;
            color: #1e40af;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .modern-header {
                padding: 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .modern-content {
                padding: 20px;
            }
            
            .modern-footer {
                padding: 20px;
                flex-direction: column;
            }
            
            .btn-modern {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    
    <div class="modern-container">
        <!-- Header -->
        <div class="modern-header">
            <div class="modern-header-content">
                <div class="modern-header-title">✏️ Crear Publicación Completa</div>
                <div class="modern-header-subtitle">Comparte con tu comunidad educativa</div>
            </div>
            <button type="button" class="btn-back" onclick="window.history.back()">
                <span>←</span>
                <span>Volver</span>
            </button>
        </div>
        
        <!-- Contenido -->
        <div class="modern-content">
            
            <div class="info-box">
                <div class="info-box-title">📝 Información</div>
                <div class="info-box-text">
                    Solo el <strong>Título</strong> y la <strong>Descripción</strong> son obligatorios. 
                    Todos los demás campos son opcionales y puedes agregarlos según necesites.
                </div>
            </div>
            
            <form action="noticias-guardar.php" method="post" enctype="multipart/form-data">
                
                <!-- Sección 1: Información Básica -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">📋</span>
                        <span>Información Básica</span>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">
                            Título de la Publicación
                            <span class="required">*</span>
                        </label>
                        <input type="text" 
                               name="titulo" 
                               class="form-input" 
                               required 
                               placeholder="Escribe un título claro y atractivo">
                        <div class="form-help">
                            <span>💡</span>
                            <span>Usa un título descriptivo que capte la atención</span>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">
                            Descripción Principal
                            <span class="required">*</span>
                        </label>
                        <textarea name="contenido" 
                                  id="editor1" 
                                  class="form-textarea" 
                                  required
                                  placeholder="Escribe el contenido principal de tu publicación..."></textarea>
                        <div class="form-help">
                            <span>✏️</span>
                            <span>Este será el contenido principal que verán todos</span>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">
                            Descripción Final
                            <span style="color: #64748b; font-weight: 400;">(Opcional)</span>
                        </label>
                        <textarea name="contenidoPie" 
                                  id="editor2" 
                                  class="form-textarea" 
                                  style="min-height: 80px;"
                                  placeholder="Texto adicional que aparecerá al final de la publicación..."></textarea>
                        <div class="form-help">
                            <span>ℹ️</span>
                            <span>Este texto aparecerá después de las imágenes o videos</span>
                        </div>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <!-- Sección 2: Multimedia -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">🎨</span>
                        <span>Multimedia (Opcional)</span>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Imagen</label>
                        <input type="file" 
                               name="imagen" 
                               class="form-input form-file-input" 
                               onChange="validarPesoArchivo(this)"
                               accept=".png, .jpg, .jpeg">
                        <div class="form-help">
                            <span>📸</span>
                            <span>JPG, PNG o JPEG • Máximo 5MB</span>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">URL de Imagen Externa</label>
                        <input type="text" 
                               name="urlImagen" 
                               class="form-input" 
                               placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="form-help">
                            <span>🔗</span>
                            <span>Pega la URL de una imagen desde otro sitio web</span>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Video de YouTube</label>
                        <input type="text" 
                               name="video" 
                               class="form-input" 
                               placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                        <div class="form-help">
                            <span>🎥</span>
                            <span>Pega la URL completa del video o solo su ID</span>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Archivo Adjunto</label>
                        <input type="file" 
                               name="archivo" 
                               class="form-input form-file-input" 
                               onChange="validarPesoArchivo(this)">
                        <div class="form-help">
                            <span>📎</span>
                            <span>PDF, DOC, XLS, PPT, ZIP • Máximo 10MB</span>
                        </div>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <!-- Sección 3: Clasificación -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">🏷️</span>
                        <span>Clasificación (Opcional)</span>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Categoría</label>
                        <?php
                        $datosConsulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".general_categorias WHERE gcat_activa=1");
                        ?>
                        <select class="form-select select2" name="categoriaGeneral">
                            <option value="">Seleccione una categoría</option>
                            <?php
                            while ($datos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)) {
                                ?>
                                <option value="<?= $datos['gcat_id']; ?>" <?php if ($datos['gcat_id'] == 15) echo "selected"; ?>>
                                    <?= $datos['gcat_nombre'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Palabras Clave</label>
                        <input type="text" 
                               name="keyw" 
                               class="form-input tags-input" 
                               data-type="tags"
                               placeholder="Presiona Enter después de cada palabra">
                        <div class="form-help">
                            <span>🔖</span>
                            <span>Facilita que otros encuentren tu publicación</span>
                        </div>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <!-- Sección 4: Destinatarios -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">👥</span>
                        <span>¿Para quién es esta publicación?</span>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Destinatarios</label>
                        <select class="form-select select2-multiple" multiple name="destinatarios[]">
                            <?php
                            try {
                                $opcionesConsulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".general_perfiles");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                if ($opcionesDatos['pes_id'] == TIPO_DEV && $datosUsuarioActual['uss_tipo'] != TIPO_DEV) {
                                    continue;
                                }
                                ?>
                                <option value="<?= $opcionesDatos['pes_id']; ?>"><?= $opcionesDatos['pes_nombre']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="form-help">
                            <span>👤</span>
                            <span>Selecciona los tipos de usuario que verán tu publicación</span>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Cursos Específicos</label>
                        <select class="form-select select2-multiple" multiple name="cursos[]">
                            <?php
                            $infoConsulta = Grados::traerGradosInstitucion($config);
                            while ($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)) {
                                ?>
                                <option value="<?= $infoDatos['gra_id']; ?>"><?= strtoupper($infoDatos['gra_nombre']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="form-help">
                            <span>🎓</span>
                            <span>Opcional: Limita la visibilidad a cursos específicos</span>
                        </div>
                    </div>
                </div>
                
                <?php if ($datosUsuarioActual['uss_tipo'] == TIPO_DEV) { ?>
                <div class="divider"></div>
                
                <!-- Sección 5: Opciones Avanzadas (Solo Dev) -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">⚙️</span>
                        <span>Opciones Avanzadas</span>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">ID Video Loom</label>
                        <input type="text" 
                               name="video2" 
                               class="form-input" 
                               placeholder="ID del video de Loom">
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Noticia Global</label>
                        <select class="form-select" name="global">
                            <option value="NO">No - Solo mi institución</option>
                            <option value="SI">Sí - Todas las instituciones</option>
                        </select>
                        <div class="form-help">
                            <span>🌍</span>
                            <span>Las noticias globales se muestran en todas las instituciones del sistema</span>
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-label">Notificar en Tiempo Real</label>
                        <div style="display: flex; align-items: center; gap: 12px; margin-top: 10px;">
                            <label class="switchToggle">
                                <input name="notificar" type="checkbox">
                                <span class="slider green round"></span>
                            </label>
                            <span style="font-size: 14px; color: #64748b;">Enviar notificación push a usuarios conectados</span>
                        </div>
                    </div>
                </div>
                <?php } ?>
                
            </form>
        </div>
        
        <!-- Footer con botones -->
        <div class="modern-footer">
            <button type="button" class="btn-modern btn-cancel" onclick="window.history.back()">
                <span>✕</span>
                <span>Cancelar</span>
            </button>
            <button type="button" class="btn-modern btn-submit" onclick="submitForm()">
                <span>💾</span>
                <span>Publicar Ahora</span>
            </button>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    
    <script>
        // Inicializar componentes
        $(document).ready(function() {
            console.log('🔄 Inicializando componentes...');
            
            // Select2
            try {
                $('.select2').select2({
                    placeholder: 'Selecciona una opción',
                    allowClear: true
                });
                
                $('.select2-multiple').select2({
                    placeholder: 'Selecciona una o más opciones',
                    allowClear: true
                });
                console.log('✅ Select2 inicializado');
            } catch (error) {
                console.error('❌ Error en Select2:', error);
            }
            
            // Tags Input
            try {
                $('.tags-input').tagsInput({
                    width: '100%',
                    height: '44px',
                    defaultText: 'Agregar etiqueta',
                    delimiter: [',', ';']
                });
                console.log('✅ Tags Input inicializado');
            } catch (error) {
                console.error('❌ Error en Tags Input:', error);
            }
        });
        
        // CKEditor se inicializa después de que esté completamente cargado
        window.onload = function() {
            // Dar tiempo para que CKEditor se cargue completamente
            setTimeout(function() {
                console.log('🔄 Inicializando CKEditor...');
                
                if (typeof CKEDITOR !== 'undefined') {
                    try {
                        // Suprimir advertencias de versión en consola
                        CKEDITOR.on('instanceReady', function(evt) {
                            // Deshabilitar advertencias de versión no segura
                            if (evt.editor.config) {
                                evt.editor.config.versionCheck = false;
                            }
                        });
                        
                        // Configuración global para deshabilitar verificación de versión
                        CKEDITOR.config.versionCheck = false;
                        
                        // Destruir instancias previas si existen
                        if (CKEDITOR.instances.editor1) {
                            CKEDITOR.instances.editor1.destroy(true);
                        }
                        if (CKEDITOR.instances.editor2) {
                            CKEDITOR.instances.editor2.destroy(true);
                        }
                        
                        // Crear nueva instancia para editor1
                        var editor1 = CKEDITOR.replace('editor1', {
                            height: 180,
                            toolbar: [
                                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                                { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
                                { name: 'links', items: ['Link'] }
                            ],
                            removePlugins: 'elementspath',
                            resize_enabled: true,
                            language: 'es',
                            versionCheck: false,
                            on: {
                                instanceReady: function() {
                                    console.log('✅ Editor1 listo');
                                }
                            }
                        });
                        
                        // Crear nueva instancia para editor2
                        var editor2 = CKEDITOR.replace('editor2', {
                            height: 120,
                            toolbar: [
                                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] }
                            ],
                            removePlugins: 'elementspath',
                            resize_enabled: true,
                            language: 'es',
                            versionCheck: false,
                            on: {
                                instanceReady: function() {
                                    console.log('✅ Editor2 listo');
                                }
                            }
                        });
                        
                        console.log('✅ CKEditor inicializado correctamente');
                    } catch (error) {
                        console.error('❌ Error al inicializar CKEditor:', error);
                    }
                } else {
                    console.error('❌ CKEditor no está disponible. Verifica que el script se haya cargado.');
                }
            }, 500); // Esperar 500ms para asegurar que CKEditor esté completamente cargado
        };
        
        // Validar peso de archivo
        function validarPesoArchivo(input) {
            if (input.files && input.files[0]) {
                const size = input.files[0].size / 1024 / 1024; // en MB
                const maxSize = input.name === 'imagen' ? 5 : 10;
                
                if (size > maxSize) {
                    alert('❌ El archivo es muy grande. Máximo ' + maxSize + 'MB.');
                    input.value = '';
                    return false;
                }
            }
            return true;
        }
        
        // Enviar formulario
        function submitForm() {
            console.log('📤 Enviando formulario...');
            
            // Actualizar datos de CKEditor
            if (CKEDITOR.instances.editor1) {
                document.querySelector('[name="contenido"]').value = CKEDITOR.instances.editor1.getData();
            }
            if (CKEDITOR.instances.editor2) {
                document.querySelector('[name="contenidoPie"]').value = CKEDITOR.instances.editor2.getData();
            }
            
            // Enviar formulario
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>
