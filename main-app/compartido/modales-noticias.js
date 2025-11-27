/**
 * ========================================
 * MODALES DE NOTICIAS - JAVASCRIPT
 * ========================================
 */

console.log('✅ Script de modales de noticias cargado');

// Variables globales
window.modalPhotoFile = null;
window.modalVideoId = null;

// ========================================
// FUNCIONES GLOBALES PARA FOTO
// ========================================
window.clearPhoto = function() {
    console.log('🗑️ Limpiando foto');
    
    const photoFile = document.getElementById('photoFile');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const uploadArea = document.getElementById('uploadArea');
    const btnPublish = document.getElementById('btnPublish');
    const photoTextContainer = document.getElementById('photoTextContainer');
    const photoDescription = document.getElementById('photoDescription');
    const charCount = document.getElementById('charCount');
    
    window.modalPhotoFile = null;
    if (photoFile) photoFile.value = '';
    if (previewImage) previewImage.src = '';
    if (previewContainer) previewContainer.classList.remove('visible');
    if (uploadArea) uploadArea.style.display = 'block';
    if (photoTextContainer) photoTextContainer.style.display = 'none';
    if (photoDescription) photoDescription.value = '';
    if (charCount) charCount.textContent = '0';
    if (btnPublish) btnPublish.disabled = true;
};

window.publishPhoto = function() {
    console.log('🚀 Publicando foto...');
    
    if (!window.modalPhotoFile) {
        alert('❌ Selecciona una foto primero');
        return;
    }
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const btnPublish = document.getElementById('btnPublish');
    const photoDescription = document.getElementById('photoDescription');
    
    console.log('📤 Preparando envío...');
    
    if (loadingOverlay) loadingOverlay.classList.add('active');
    if (btnPublish) btnPublish.disabled = true;
    
    const formData = new FormData();
    formData.append('imagen', window.modalPhotoFile);
    
    const description = photoDescription ? photoDescription.value.trim() : '';
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
    .then(response => {
        console.log('✅ Respuesta recibida');
        return response.text();
    })
    .then(data => {
        console.log('✅ Éxito:', data);
        if (loadingOverlay) loadingOverlay.classList.remove('active');
        cerrarModal_nuevoFoto();
        setTimeout(() => window.location.reload(), 300);
    })
    .catch(error => {
        console.error('❌ Error:', error);
        if (loadingOverlay) loadingOverlay.classList.remove('active');
        if (btnPublish) btnPublish.disabled = false;
        alert('❌ Error al subir. Intenta de nuevo.');
    });
};

// ========================================
// FUNCIONES GLOBALES PARA VIDEO
// ========================================
window.clearVideo = function() {
    console.log('🗑️ Limpiando video');
    
    const videoURL = document.getElementById('videoURL');
    const videoIframe = document.getElementById('videoIframe');
    const videoPreview = document.getElementById('videoPreview');
    const btnPublishVideo = document.getElementById('btnPublishVideo');
    
    window.modalVideoId = null;
    if (videoURL) videoURL.value = '';
    if (videoIframe) videoIframe.src = '';
    if (videoPreview) videoPreview.classList.remove('visible');
    if (btnPublishVideo) btnPublishVideo.disabled = true;
};

window.publishVideo = function() {
    console.log('🚀 Publicando video...');
    
    if (!window.modalVideoId) {
        alert('❌ Ingresa una URL de YouTube válida');
        return;
    }
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const btnPublishVideo = document.getElementById('btnPublishVideo');
    
    console.log('📤 Preparando envío...');
    
    if (loadingOverlay) loadingOverlay.classList.add('active');
    if (btnPublishVideo) btnPublishVideo.disabled = true;
    
    const formData = new FormData();
    formData.append('video', window.modalVideoId);
    formData.append('titulo', 'Publicación con video');
    formData.append('contenido', 'Publicación con video');
    formData.append('categoriaGeneral', '15');
    
    console.log('📡 Enviando...');
    
    fetch('../compartido/noticias-guardar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('✅ Respuesta recibida');
        return response.text();
    })
    .then(data => {
        console.log('✅ Éxito:', data);
        if (loadingOverlay) loadingOverlay.classList.remove('active');
        cerrarModal_nuevoVideo();
        setTimeout(() => window.location.reload(), 300);
    })
    .catch(error => {
        console.error('❌ Error:', error);
        if (loadingOverlay) loadingOverlay.classList.remove('active');
        if (btnPublishVideo) btnPublishVideo.disabled = false;
        alert('❌ Error al publicar. Intenta de nuevo.');
    });
};

// ========================================
// INICIALIZACIÓN DEL MODAL DE FOTO
// ========================================
function initPhotoModal() {
    console.log('📸 Inicializando modal de foto...');
    
    const photoFile = document.getElementById('photoFile');
    const uploadArea = document.getElementById('uploadArea');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const btnPublish = document.getElementById('btnPublish');
    const photoTextContainer = document.getElementById('photoTextContainer');
    const photoDescription = document.getElementById('photoDescription');
    const charCount = document.getElementById('charCount');
    
    if (!photoFile || !uploadArea) {
        console.error('❌ Elementos no encontrados');
        return;
    }
    
    console.log('✅ Elementos encontrados');
    
    // Remover listeners previos
    const newPhotoFile = photoFile.cloneNode(true);
    photoFile.parentNode.replaceChild(newPhotoFile, photoFile);
    
    const newUploadArea = uploadArea.cloneNode(true);
    uploadArea.parentNode.replaceChild(newUploadArea, uploadArea);
    
    // Obtener referencias frescas
    const freshPhotoFile = document.getElementById('photoFile');
    const freshUploadArea = document.getElementById('uploadArea');
    
    // Click en área
    freshUploadArea.addEventListener('click', function(e) {
        console.log('👆 Click en área');
        if (e.target.tagName !== 'BUTTON') {
            freshPhotoFile.click();
        }
    });
    
    // Cambio de archivo
    freshPhotoFile.addEventListener('change', function(e) {
        console.log('📁 Cambio detectado');
        if (this.files && this.files[0]) {
            handlePhotoFile(this.files[0]);
        }
    });
    
    // Drag & Drop
    freshUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('active');
    });
    
    freshUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('active');
    });
    
    freshUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('active');
        console.log('📥 Drop detectado');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handlePhotoFile(e.dataTransfer.files[0]);
        }
    });
    
    // Contador
    if (photoDescription) {
        photoDescription.addEventListener('input', function() {
            if (charCount) charCount.textContent = this.value.length;
        });
    }
    
    function handlePhotoFile(file) {
        console.log('📸 Procesando archivo:', file.name, file.size, file.type);
        
        if (file.size > 5 * 1024 * 1024) {
            alert('❌ Imagen muy grande. Máximo 5MB.');
            return;
        }
        
        if (!file.type.match('image/(jpeg|jpg|png)')) {
            alert('❌ Solo JPG, PNG o JPEG.');
            return;
        }
        
        window.modalPhotoFile = file;
        console.log('✅ Archivo válido');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            console.log('🖼️ Mostrando preview...');
            previewImage.src = e.target.result;
            previewContainer.classList.add('visible');
            freshUploadArea.style.display = 'none';
            photoTextContainer.style.display = 'block';
            btnPublish.disabled = false;
            console.log('✅ Preview mostrado, botón habilitado');
        };
        reader.readAsDataURL(file);
    }
    
    console.log('✅ Modal inicializado correctamente');
}

// ========================================
// INICIALIZACIÓN DEL MODAL DE VIDEO
// ========================================
function initVideoModal() {
    console.log('🎥 Inicializando modal de video...');
    
    const videoURL = document.getElementById('videoURL');
    const videoPreview = document.getElementById('videoPreview');
    const videoIframe = document.getElementById('videoIframe');
    const btnPublishVideo = document.getElementById('btnPublishVideo');
    
    if (!videoURL) {
        console.error('❌ Input de video no encontrado');
        return;
    }
    
    console.log('✅ Elementos encontrados');
    
    let timeout;
    
    // Remover listener previo
    const newVideoURL = videoURL.cloneNode(true);
    videoURL.parentNode.replaceChild(newVideoURL, videoURL);
    const freshVideoURL = document.getElementById('videoURL');
    
    freshVideoURL.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            processVideoURL(this.value);
        }, 500);
    });
    
    freshVideoURL.addEventListener('paste', function() {
        setTimeout(() => processVideoURL(this.value), 100);
    });
    
    function extractYouTubeID(url) {
        if (!url || url.trim().length === 0) return null;
        url = url.trim();
        
        if (url.length === 11 && !/[\/\?\&\s]/.test(url)) {
            return url;
        }
        
        const patterns = [
            /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/,
            /^([a-zA-Z0-9_-]{11})$/
        ];
        
        for (let pattern of patterns) {
            const match = url.match(pattern);
            if (match && match[1]) {
                return match[1];
            }
        }
        
        return null;
    }
    
    function processVideoURL(url) {
        console.log('🔍 Procesando URL:', url);
        
        if (!url || url.trim().length === 0) {
            clearVideo();
            return;
        }
        
        const videoId = extractYouTubeID(url);
        
        if (videoId) {
            console.log('✅ Video ID:', videoId);
            window.modalVideoId = videoId;
            
            videoIframe.src = `https://www.youtube.com/embed/${videoId}`;
            videoPreview.classList.add('visible');
            btnPublishVideo.disabled = false;
            console.log('✅ Preview mostrado, botón habilitado');
            
            if (freshVideoURL.value !== videoId) {
                freshVideoURL.value = videoId;
            }
        } else {
            console.log('⚠️ URL no válida');
            clearVideo();
        }
    }
    
    console.log('✅ Modal inicializado correctamente');
}

// ========================================
// AUTO-INICIALIZACIÓN CON EVENTOS
// ========================================
if (typeof $ !== 'undefined') {
    $(document).on('shown.bs.modal', '#ComponeteModal-nuevoFoto', function() {
        console.log('🎬 Evento modal foto detectado');
        setTimeout(initPhotoModal, 300);
    });
    
    $(document).on('shown.bs.modal', '#ComponeteModal-nuevoVideo', function() {
        console.log('🎬 Evento modal video detectado');
        setTimeout(initVideoModal, 300);
    });
} else {
    console.error('❌ jQuery no disponible');
}