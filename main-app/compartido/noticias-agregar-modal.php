<?php
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
    $input = json_decode(file_get_contents("php://input"), true);
    if (!empty($input)) {
        $_GET = $input;
    }
}
require_once("../../main-app/class/Grados.php");
?>

<style>
/* Modal más ancho y moderno */
#ComponeteModal-nuevoPublicacion .modal-dialog {
    max-width: 900px !important;
}

#ComponeteModal-nuevoPublicacion .modal-body {
    padding: 0 !important;
}

.modern-form-container {
    padding: 30px 40px;
    background: #ffffff;
}

.modern-form-section {
    margin-bottom: 25px;
}

.modern-form-section:last-child {
    margin-bottom: 0;
}

.modern-label {
    font-size: 14px;
    font-weight: 600;
    color: #32325d;
    margin-bottom: 8px;
    display: block;
}

.modern-label .required {
    color: #f5365c;
    margin-left: 3px;
}

.modern-input,
.modern-textarea,
.modern-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    font-family: inherit;
}

.modern-input:focus,
.modern-textarea:focus,
.modern-select:focus {
    outline: none;
    border-color: #5e72e4;
    box-shadow: 0 0 0 3px rgba(94, 114, 228, 0.1);
}

.modern-textarea {
    resize: vertical;
    min-height: 100px;
}

.modern-file-input {
    padding: 10px 16px;
    border: 2px dashed #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.modern-file-input:hover {
    border-color: #5e72e4;
    background: #f8f9fe;
}

.form-section-divider {
    border: none;
    border-top: 2px solid #f7fafc;
    margin: 30px 0;
}

.form-section-title {
    font-size: 16px;
    font-weight: 700;
    color: #32325d;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title::before {
    content: '';
    width: 4px;
    height: 24px;
    background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%);
    border-radius: 2px;
}

.modern-submit-area {
    padding: 25px 40px;
    background: #f7fafc;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
}

.modern-submit-btn {
    background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%);
    color: white;
    padding: 14px 32px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.modern-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(94, 114, 228, 0.4);
}

.form-help-text {
    font-size: 12px;
    color: #8898aa;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.select2-container--default .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    border: 2px solid #e9ecef !important;
    border-radius: 8px !important;
    min-height: 44px !important;
}

.select2-container--default.select2-container--focus .select2-selection--multiple,
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #5e72e4 !important;
    box-shadow: 0 0 0 3px rgba(94, 114, 228, 0.1) !important;
}
</style>

<div class="modern-form-container">
    <form class="modern-form" action="../compartido/noticias-guardar.php" method="post" enctype="multipart/form-data">
        
        <!-- Campos Obligatorios -->
        <div class="form-section-title">✏️ Información Básica</div>
        
        <div class="modern-form-section">
            <label class="modern-label">
                Título <span class="required">*</span>
            </label>
            <input type="text" 
                   name="titulo" 
                   class="modern-input" 
                   required 
                   placeholder="Escribe un título atractivo para tu publicación">
            <div class="form-help-text">
                <span>💡</span>
                <span>Usa un título claro y descriptivo</span>
            </div>
        </div>

        <div class="modern-form-section">
            <label class="modern-label">
                Descripción <span class="required">*</span>
            </label>
            <textarea name="contenido" 
                      id="editor1" 
                      class="modern-textarea" 
                      required
                      placeholder="Escribe el contenido principal de tu publicación..."
                      rows="5"></textarea>
            <div class="form-help-text">
                <span>💡</span>
                <span>Este será el contenido principal de tu publicación</span>
            </div>
        </div>

        <div class="modern-form-section">
            <label class="modern-label">
                Descripción Final (Opcional)
            </label>
            <textarea name="contenidoPie" 
                      id="editor2" 
                      class="modern-textarea" 
                      placeholder="Texto adicional que aparecerá al final (opcional)"
                      rows="3"></textarea>
            <div class="form-help-text">
                <span>ℹ️</span>
                <span>Este texto aparecerá después de la imagen o video</span>
            </div>
        </div>

        <hr class="form-section-divider">

        <!-- Multimedia -->
        <div class="form-section-title">🎨 Multimedia (Opcional)</div>

        <div class="modern-form-section">
            <label class="modern-label">Imagen</label>
            <input type="file" 
                   name="imagen" 
                   class="modern-input modern-file-input" 
                   onChange="validarPesoArchivo(this)"
                   accept=".png, .jpg, .jpeg">
            <div class="form-help-text">
                <span>📸</span>
                <span>JPG, PNG o JPEG • Máx. 5MB</span>
            </div>
        </div>

        <div class="modern-form-section">
            <label class="modern-label">URL de Imagen Externa</label>
            <input type="text" 
                   name="urlImagen" 
                   class="modern-input" 
                   placeholder="https://ejemplo.com/imagen.jpg">
            <div class="form-help-text">
                <span>🔗</span>
                <span>Pega la URL de una imagen externa</span>
            </div>
        </div>

        <div class="modern-form-section">
            <label class="modern-label">Video de YouTube</label>
            <input type="text" 
                   name="video" 
                   class="modern-input" 
                   placeholder="https://www.youtube.com/watch?v=... o ID del video">
            <div class="form-help-text">
                <span>🎥</span>
                <span>Pega la URL completa o solo el ID del video</span>
            </div>
        </div>

        <div class="modern-form-section">
            <label class="modern-label">Archivo Adjunto</label>
            <input type="file" 
                   name="archivo" 
                   class="modern-input modern-file-input" 
                   onChange="validarPesoArchivo(this)">
            <div class="form-help-text">
                <span>📎</span>
                <span>PDF, DOC, XLS, PPT, ZIP • Máx. 10MB</span>
            </div>
        </div>

        <hr class="form-section-divider">

        <!-- Clasificación -->
        <div class="form-section-title">🏷️ Clasificación (Opcional)</div>

        <div class="modern-form-section">
            <label class="modern-label">Categoría</label>
            <?php
            $datosConsulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".general_categorias WHERE gcat_activa=1");
            ?>
            <select class="modern-select select2" style="width: 100%" name="categoriaGeneral">
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

        <div class="modern-form-section">
            <label class="modern-label">Palabras Clave</label>
            <input type="text" 
                   name="keyw" 
                   class="modern-input tags tags-input" 
                   data-type="tags"
                   placeholder="Escribe y presiona Enter para agregar etiquetas">
            <div class="form-help-text">
                <span>🔖</span>
                <span>Agrega etiquetas para facilitar la búsqueda</span>
            </div>
        </div>

        <?php if ($datosUsuarioActual['uss_tipo'] == TIPO_DEV) { ?>
        <hr class="form-section-divider">
        
        <!-- Opciones de Desarrollador -->
        <div class="form-section-title">⚙️ Opciones Avanzadas</div>

        <div class="modern-form-section">
            <label class="modern-label">ID Video Loom</label>
            <input type="text" name="video2" class="modern-input" placeholder="ID del video de Loom">
        </div>

        <div class="modern-form-section">
            <label class="modern-label">Noticia Global</label>
            <select class="modern-select" name="global">
                <option value="NO">No</option>
                <option value="SI">Sí</option>
            </select>
            <div class="form-help-text">
                <span>🌍</span>
                <span>Las noticias globales se muestran a todas las instituciones</span>
            </div>
        </div>

        <div class="modern-form-section">
            <label class="modern-label">Notificar en Tiempo Real</label>
            <div style="display: flex; align-items: center; gap: 12px;">
                <label class="switchToggle">
                    <input name="notificar" type="checkbox">
                    <span class="slider green round"></span>
                </label>
                <span style="font-size: 13px; color: #8898aa;">Enviar notificación a usuarios conectados</span>
            </div>
        </div>
        <?php } ?>

        <hr class="form-section-divider">

        <!-- Destinatarios -->
        <div class="form-section-title">👥 Destinatarios</div>

        <div class="modern-form-section">
            <label class="modern-label">Para quién es esta publicación</label>
            <select style="width: 100%" class="modern-select select2-multiple" multiple name="destinatarios[]">
                <option value="">Seleccione los destinatarios</option>
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
            <div class="form-help-text">
                <span>👥</span>
                <span>Selecciona los tipos de usuario que verán esta publicación</span>
            </div>
        </div>

        <div class="modern-form-section">
            <label class="modern-label">Cursos Específicos</label>
            <select style="width: 100%" class="modern-select select2-multiple" multiple name="cursos[]">
                <option value="">Seleccione los cursos</option>
                <?php
                $infoConsulta = Grados::traerGradosInstitucion($config);
                while ($infoDatos = mysqli_fetch_array($infoConsulta, MYSQLI_BOTH)) {
                    ?>
                    <option value="<?= $infoDatos['gra_id']; ?>"><?= strtoupper($infoDatos['gra_nombre']); ?></option>
                <?php } ?>
            </select>
            <div class="form-help-text">
                <span>🎓</span>
                <span>Opcional: Limita la visibilidad a cursos específicos</span>
            </div>
        </div>

    </form>
</div>

<!-- Botón de envío en el footer -->
<div class="modern-submit-area">
    <button type="button" class="modern-submit-btn" onclick="submitFullPost()">
        <span>💾</span>
        <span>Publicar</span>
    </button>
</div>

<!-- Scripts necesarios -->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">

<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" style="display:none" onload="
(function(){
    console.log('📝 Inicializando modal de publicación completa...');
    
    // Inicializar Select2
    setTimeout(function() {
        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2();
            $('.select2-multiple').select2();
            console.log('✅ Select2 inicializado');
        }
        
        // Inicializar tags input
        if (typeof $.fn.tagsInput !== 'undefined') {
            $('.tags-input').tagsInput({
                width: '100%',
                height: '40px',
                defaultText: 'Agregar etiqueta',
                delimiter: [',']
            });
            console.log('✅ Tags input inicializado');
        }
        
        // Inicializar CKEditor si está disponible
        if (typeof CKEDITOR !== 'undefined') {
            if (CKEDITOR.instances.editor1) {
                CKEDITOR.instances.editor1.destroy();
            }
            if (CKEDITOR.instances.editor2) {
                CKEDITOR.instances.editor2.destroy();
            }
            
            CKEDITOR.replace('editor1', {
                height: 150,
                toolbar: [
                    ['Bold', 'Italic', 'Underline'],
                    ['NumberedList', 'BulletedList'],
                    ['Link']
                ]
            });
            
            CKEDITOR.replace('editor2', {
                height: 100,
                toolbar: [
                    ['Bold', 'Italic', 'Underline']
                ]
            });
            
            console.log('✅ CKEditor inicializado');
        }
    }, 500);
    
    // Función para enviar el formulario
    window.submitFullPost = function() {
        console.log('🚀 Enviando publicación completa...');
        
        // Obtener datos del CKEditor si existe
        if (typeof CKEDITOR !== 'undefined') {
            if (CKEDITOR.instances.editor1) {
                document.querySelector('[name=\"contenido\"]').value = CKEDITOR.instances.editor1.getData();
            }
            if (CKEDITOR.instances.editor2) {
                document.querySelector('[name=\"contenidoPie\"]').value = CKEDITOR.instances.editor2.getData();
            }
        }
        
        // Enviar el formulario
        var form = document.querySelector('.modern-form');
        if (form) {
            form.submit();
        }
    };
    
    console.log('✅ Modal de publicación completa inicializado');
})();
">

<script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>