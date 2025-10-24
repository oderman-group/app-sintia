<?php
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
}
?>

<!-- Campo de entrada -->
<div class="video-input-group">
    <label class="video-label">🎥 URL del video de YouTube</label>
    <div class="video-input-box">
        <span class="video-input-icon">▶️</span>
        <input type="text" 
               id="videoURL" 
               class="video-input" 
               placeholder="https://www.youtube.com/watch?v=...">
    </div>
    <div class="video-help">
        <div class="video-help-title">💡 Cómo usar</div>
        <div class="video-help-example">
            Pega el enlace completo del video de YouTube aquí<br>
            Ejemplo: https://www.youtube.com/watch?v=dQw4w9WgXcQ
        </div>
    </div>
</div>

<!-- Preview -->
<div class="video-preview-box" id="videoPreview">
    <div class="video-embed">
        <iframe id="videoIframe" src="" allowfullscreen></iframe>
    </div>
    <button type="button" class="btn-remove-video" id="btnRemoveVideo">
        ✕
    </button>
</div>

<!-- Campo de texto opcional -->
<div class="photo-text-container" id="videoTextContainer" style="display: none;">
    <label class="photo-text-label">Escribe algo sobre tu video (opcional)</label>
    <textarea id="videoDescription" 
              class="photo-text-area" 
              placeholder="¿Qué quieres compartir?"
              rows="4"
              maxlength="500"></textarea>
    <div class="photo-text-counter">
        <span id="videoCharCount">0</span>/500 caracteres
    </div>
</div>

<!-- Botones de acción -->
<div class="action-buttons">
    <button type="button" class="btn-action btn-cancel" onclick="cerrarModal_nuevoVideo()">
        Cancelar
    </button>
    <button type="button" class="btn-action btn-publish" id="btnPublishVideo" disabled style="background: linear-gradient(87deg, #ff0000 0, #cc0000 100%);">
        Publicar Video
    </button>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlayVideo">
    <div class="loading-content">
        <div class="loading-spinner" style="border-top-color: #ff0000;"></div>
        <div class="loading-text">Procesando video...</div>
    </div>
</div>

<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" style="display:none" onload="
(function(){
    console.log('🎥 Inicializando modal de video INLINE...');
    
    var currentVideoId = null;
    var videoURL = document.getElementById('videoURL');
    var videoPreview = document.getElementById('videoPreview');
    var videoIframe = document.getElementById('videoIframe');
    var btnPublishVideo = document.getElementById('btnPublishVideo');
    var videoTextContainer = document.getElementById('videoTextContainer');
    var videoDescription = document.getElementById('videoDescription');
    var videoCharCount = document.getElementById('videoCharCount');
    var loadingOverlayVideo = document.getElementById('loadingOverlayVideo');
    var btnRemoveVideo = document.getElementById('btnRemoveVideo');
    
    if (!videoURL) {
        console.error('❌ Input no encontrado');
        return;
    }
    
    console.log('✅ Elementos encontrados');
    
    var inputTimeout;
    
    videoURL.oninput = function() {
        clearTimeout(inputTimeout);
        inputTimeout = setTimeout(function() {
            processURL(videoURL.value);
        }, 500);
    };
    
    videoURL.onpaste = function() {
        setTimeout(function() {
            processURL(videoURL.value);
        }, 100);
    };
    
    // Contador
    if (videoDescription) {
        videoDescription.oninput = function() {
            if (videoCharCount) videoCharCount.textContent = this.value.length;
        };
    }
    
    // Remover video
    btnRemoveVideo.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🗑️ Removiendo video');
        clearVideo();
    };
    
    function extractYouTubeID(url) {
        if (!url || url.trim().length === 0) return null;
        url = url.trim();
        
        if (url.length === 11 && !/[\/\?\&\s]/.test(url)) {
            return url;
        }
        
        var patterns = [
            /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/,
            /^([a-zA-Z0-9_-]{11})$/
        ];
        
        for (var i = 0; i < patterns.length; i++) {
            var match = url.match(patterns[i]);
            if (match && match[1]) {
                return match[1];
            }
        }
        
        return null;
    }
    
    function processURL(url) {
        console.log('🔍 Procesando URL:', url);
        
        if (!url || url.trim().length === 0) {
            clearVideo();
            return;
        }
        
        var videoId = extractYouTubeID(url);
        
        if (videoId) {
            console.log('✅ Video ID:', videoId);
            currentVideoId = videoId;
            
            videoIframe.src = 'https://www.youtube.com/embed/' + videoId;
            videoPreview.classList.add('visible');
            videoTextContainer.style.display = 'block';
            btnPublishVideo.disabled = false;
            console.log('✅ Preview mostrado, botón habilitado');
            
            if (videoURL.value !== videoId) {
                videoURL.value = videoId;
            }
        } else {
            console.log('⚠️ URL no válida');
            clearVideo();
        }
    }
    
    function clearVideo() {
        currentVideoId = null;
        videoURL.value = '';
        videoIframe.src = '';
        videoPreview.classList.remove('visible');
        videoTextContainer.style.display = 'none';
        videoDescription.value = '';
        videoCharCount.textContent = '0';
        btnPublishVideo.disabled = true;
    }
    
    // Publicar
    btnPublishVideo.onclick = function() {
        console.log('🚀 Publicando video...');
        
        if (!currentVideoId) {
            alert('❌ Ingresa una URL de YouTube válida');
            return;
        }
        
        console.log('📤 Preparando envío...');
        
        loadingOverlayVideo.classList.add('active');
        btnPublishVideo.disabled = true;
        
        var formData = new FormData();
        formData.append('video', currentVideoId);
        
        var description = videoDescription.value.trim();
        if (description) {
            formData.append('titulo', description.substring(0, 100));
            formData.append('contenido', description);
        } else {
            formData.append('titulo', 'Publicación con video');
            formData.append('contenido', 'Publicación con video');
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
            loadingOverlayVideo.classList.remove('active');
            cerrarModal_nuevoVideo();
            setTimeout(function() {
                window.location.reload();
            }, 300);
        })
        .catch(function(error) {
            console.error('❌ Error:', error);
            loadingOverlayVideo.classList.remove('active');
            btnPublishVideo.disabled = false;
            alert('❌ Error al publicar. Intenta de nuevo.');
        });
    };
    
    console.log('✅ Modal de video inicializado correctamente');
})();
">