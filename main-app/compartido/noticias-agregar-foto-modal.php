<?php
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
}
?>

<!-- Área de subida -->
<div class="upload-area" id="uploadArea">
    <div class="upload-icon">📸</div>
    <div class="upload-title">Selecciona tu foto</div>
    <div class="upload-subtitle">Arrastra y suelta aquí, o haz clic para elegir</div>
    <button type="button" class="btn-choose" id="btnChoosePhoto">
        Elegir Foto
    </button>
    <div class="upload-info">JPG, PNG o JPEG • Máximo 5MB</div>
</div>

<input type="file" id="photoFile" accept="image/jpeg,image/jpg,image/png" style="display: none;">

<!-- Preview -->
<div class="preview-container" id="previewContainer">
    <img id="previewImage" class="preview-img" src="" alt="Preview">
    <button type="button" class="btn-remove-preview" id="btnRemovePhoto">
        ✕
    </button>
</div>

<!-- Campo de texto opcional -->
<div class="photo-text-container" id="photoTextContainer" style="display: none;">
    <label class="photo-text-label">Escribe algo sobre tu foto (opcional)</label>
    <textarea id="photoDescription" 
              class="photo-text-area" 
              placeholder="¿Qué quieres compartir?"
              rows="4"
              maxlength="500"></textarea>
    <div class="photo-text-counter">
        <span id="charCount">0</span>/500 caracteres
    </div>
</div>

<!-- Botones de acción -->
<div class="action-buttons">
    <button type="button" class="btn-action btn-cancel" onclick="cerrarModal_nuevoFoto()">
        Cancelar
    </button>
    <button type="button" class="btn-action btn-publish" id="btnPublish" disabled>
        Publicar Foto
    </button>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <div class="loading-text">Subiendo foto...</div>
    </div>
</div>

<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" style="display:none" onload="
(function(){
    console.log('📸 Inicializando modal de foto INLINE...');
    
    var selectedFile = null;
    var photoFile = document.getElementById('photoFile');
    var uploadArea = document.getElementById('uploadArea');
    var previewContainer = document.getElementById('previewContainer');
    var previewImage = document.getElementById('previewImage');
    var btnPublish = document.getElementById('btnPublish');
    var photoTextContainer = document.getElementById('photoTextContainer');
    var photoDescription = document.getElementById('photoDescription');
    var charCount = document.getElementById('charCount');
    var loadingOverlay = document.getElementById('loadingOverlay');
    var btnChoosePhoto = document.getElementById('btnChoosePhoto');
    var btnRemovePhoto = document.getElementById('btnRemovePhoto');
    
    if (!photoFile) {
        console.error('❌ Elementos no encontrados');
        return;
    }
    
    console.log('✅ Elementos encontrados');
    
    // Click en botón elegir
    btnChoosePhoto.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('👆 Click en botón');
        photoFile.click();
    };
    
    // Click en área
    uploadArea.onclick = function(e) {
        if (e.target.id !== 'btnChoosePhoto') {
            console.log('👆 Click en área');
            photoFile.click();
        }
    };
    
    // Cambio de archivo
    photoFile.onchange = function() {
        console.log('📁 Archivo seleccionado');
        if (this.files && this.files[0]) {
            handleFile(this.files[0]);
        }
    };
    
    // Drag & Drop
    uploadArea.ondragover = function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('active');
    };
    
    uploadArea.ondragleave = function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('active');
    };
    
    uploadArea.ondrop = function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('active');
        console.log('📥 Drop detectado');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    };
    
    // Contador
    if (photoDescription) {
        photoDescription.oninput = function() {
            if (charCount) charCount.textContent = this.value.length;
        };
    }
    
    // Remover foto
    btnRemovePhoto.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🗑️ Removiendo foto');
        clearPhoto();
    };
    
    function clearPhoto() {
        selectedFile = null;
        photoFile.value = '';
        previewImage.src = '';
        previewContainer.classList.remove('visible');
        uploadArea.style.display = 'block';
        photoTextContainer.style.display = 'none';
        photoDescription.value = '';
        charCount.textContent = '0';
        btnPublish.disabled = true;
    }
    
    function handleFile(file) {
        console.log('📸 Procesando:', file.name, file.size, file.type);
        
        if (file.size > 5 * 1024 * 1024) {
            alert('❌ Imagen muy grande. Máximo 5MB.');
            return;
        }
        
        if (!file.type.match('image/(jpeg|jpg|png)')) {
            alert('❌ Solo JPG, PNG o JPEG.');
            return;
        }
        
        selectedFile = file;
        console.log('✅ Archivo válido');
        
        var reader = new FileReader();
        reader.onload = function(e) {
            console.log('🖼️ Mostrando preview...');
            previewImage.src = e.target.result;
            previewContainer.classList.add('visible');
            uploadArea.style.display = 'none';
            photoTextContainer.style.display = 'block';
            btnPublish.disabled = false;
            console.log('✅ Preview mostrado, botón habilitado');
        };
        reader.readAsDataURL(file);
    }
    
    // Publicar
    btnPublish.onclick = function() {
        console.log('🚀 Publicando foto...');
        
        if (!selectedFile) {
            alert('❌ Selecciona una foto primero');
            return;
        }
        
        console.log('📤 Preparando envío...');
        
        loadingOverlay.classList.add('active');
        btnPublish.disabled = true;
        
        var formData = new FormData();
        formData.append('imagen', selectedFile);
        
        var description = photoDescription.value.trim();
        if (description) {
            formData.append('titulo', description.substring(0, 100));
            formData.append('contenido', description);
        } else {
            formData.append('titulo', 'Publicación con foto');
            formData.append('contenido', 'Publicación con foto');
        }
        
        formData.append('categoriaGeneral', '15');
        
        console.log('📡 Enviando...');
        
        fetch('../compartido/noticias-guardar.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            console.log('✅ Respuesta recibida');
            return response.text();
        })
        .then(function(data) {
            console.log('✅ Éxito:', data);
            loadingOverlay.classList.remove('active');
            cerrarModal_nuevoFoto();
            setTimeout(function() {
                window.location.reload();
            }, 300);
        })
        .catch(function(error) {
            console.error('❌ Error:', error);
            loadingOverlay.classList.remove('active');
            btnPublish.disabled = false;
            alert('❌ Error al subir. Intenta de nuevo.');
        });
    };
    
    console.log('✅ Modal inicializado correctamente');
})();
">