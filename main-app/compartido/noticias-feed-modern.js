/**
 * ==========================================
 * FEED DE NOTICIAS ESTILO LINKEDIN - JAVASCRIPT
 * Sistema moderno con funciones asíncronas optimizadas
 * ==========================================
 */

class NewsFeedModern {
    constructor() {
        this.page = 0;
        this.loading = false;
        this.hasMore = true;
        this.postsPerPage = 10;
        this.scrollThreshold = 300; // px desde el fondo para cargar más
        this.currentUser = null;
        this.mediaModal = null;
        this.backToTopButton = null;
        this.currentSearch = null; // Para mantener búsqueda activa en scroll infinito

        this.init();
    }

    /**
     * Inicialización del sistema
     */
    init() {
        // Leer parámetro de búsqueda de la URL si existe
        const urlParams = new URLSearchParams(window.location.search);
        const busqueda = urlParams.get('busqueda');
        if (busqueda && busqueda.trim().length > 0) {
            this.currentSearch = busqueda.trim();
            console.log('🔍 Búsqueda detectada en URL:', this.currentSearch);
        }
        
        this.setupScrollListener();
        this.setupBackToTop();
        this.setupMediaModal();
        this.setupPostCreation();
        this.loadUserInfo();
        this.showSkeletonLoading();
        this.loadMorePosts(); // Cargar primeras 10 publicaciones automáticamente
        console.log('✅ Feed Moderno inicializado');
    }

    /**
     * Cargar información del usuario actual
     */
    loadUserInfo() {
        const infoGeneral = document.getElementById('infoGeneral');
        if (infoGeneral) {
            const [userId, userPhoto, userName] = infoGeneral.value.split('|');
            this.currentUser = {
                id: atob(userId),
                photo: userPhoto,
                name: userName
            };
        }
    }

    /**
     * ==========================================
     * SCROLL INFINITO
     * ==========================================
     */
    setupScrollListener() {
        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    handleScroll() {
        // Botón back to top
        this.updateBackToTopVisibility();

        // Carga infinita
        if (this.loading || !this.hasMore) return;

        const scrollPosition = window.innerHeight + window.pageYOffset;
        const bottomPosition = document.documentElement.offsetHeight;

        if (scrollPosition >= bottomPosition - this.scrollThreshold) {
            this.loadMorePosts();
        }
    }

    /**
     * Cargar más publicaciones
     */
    async loadMorePosts() {
        if (this.loading || !this.hasMore) return;

        this.loading = true;
        
        // Solo mostrar loader si no es la primera carga (ya se muestra skeleton)
        if (this.page > 0) {
            this.showLoader();
        }

        try {
            // Construir URL con parámetro de búsqueda si existe
            let url = '../compartido/noticias-publicaciones-cargar.php';
            if (this.currentSearch) {
                url += '?busqueda=' + encodeURIComponent(this.currentSearch);
            }
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pagina: this.page * this.postsPerPage,
                    limite: this.postsPerPage
                })
            });

            const data = await response.json();

            if (data.success && data.posts && data.posts.length > 0) {
                // Ocultar skeleton en la primera carga
                if (this.page === 0) {
                    this.hideSkeletonLoading();
                }
                
                this.appendPosts(data.posts);
                this.page++;
                
                console.log(`📊 Cargadas ${data.posts.length} publicaciones (Página ${this.page})`);
                
                if (data.posts.length < this.postsPerPage) {
                    this.hasMore = false;
                }
            } else {
                this.hasMore = false;
                
                if (this.page === 0) {
                    this.hideSkeletonLoading();
                    this.showEmptyState();
                }
            }
        } catch (error) {
            console.error('❌ Error al cargar publicaciones:', error);
            this.hideSkeletonLoading();
            this.showToast('Error', 'No se pudieron cargar las publicaciones', 'error');
        } finally {
            this.loading = false;
            this.hideLoader();
        }
    }

    /**
     * Agregar posts al contenedor con animación progresiva VISIBLE
     */
    appendPosts(posts) {
        const container = document.getElementById('posts-container');
        if (!container) return;

        console.log(`🎬 Cargando ${posts.length} posts con progressive loading...`);

        // Agregar posts con delay progresivo para efecto visual MUY VISIBLE
        posts.forEach((post, index) => {
            setTimeout(() => {
                const postElement = this.createPostElement(post);
                
                // Iniciar completamente invisible y desplazado hacia abajo
                postElement.style.opacity = '0';
                postElement.style.transform = 'translateY(40px) scale(0.95)';
                postElement.style.transition = 'none';
                
                container.appendChild(postElement);
                
                // Animar entrada con efecto más notorio
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        postElement.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
                        postElement.style.opacity = '1';
                        postElement.style.transform = 'translateY(0) scale(1)';
                        
                        console.log(`✨ Post ${index + 1}/${posts.length} animado`);
                    }, 100);
                });
            }, index * 200); // 200ms entre cada post para que sea MÁS VISIBLE
        });
    }

    /**
     * ==========================================
     * CREACIÓN DE ELEMENTOS HTML
     * ==========================================
     */
    createPostElement(post) {
        const div = document.createElement('div');
        div.className = 'post-card';
        div.id = `post-${post.id}`;
        
        if (post.estado == 0) div.classList.add('post-hidden');
        if (post.global === 'SI') div.classList.add('post-global');

        div.innerHTML = `
            ${this.generatePostHeader(post)}
            ${this.generatePostContent(post)}
            ${this.generatePostMedia(post)}
            ${this.generatePostStats(post)}
            ${this.generatePostActions(post)}
            ${this.generatePostComments(post)}
        `;

        // Agregar event listeners
        this.attachPostEventListeners(div, post);

        return div;
    }

    generatePostHeader(post) {
        const canManage = this.currentUser && (
            this.currentUser.id == post.usuario ||
            post.usuarioTipo == 1 ||
            post.usuarioTipo == 5
        );

        return `
            <div class="post-header">
                <img src="${post.foto}" alt="${post.nombreUsuario}" class="post-avatar">
                <div class="post-author-info">
                    <a href="noticias.php?usuario=${btoa(post.usuario)}" class="post-author-name">
                        ${post.nombreUsuario}
                    </a>
                    <div class="post-timestamp">${post.fecha}</div>
                </div>
                ${canManage ? this.generatePostMenu(post) : ''}
            </div>
        `;
    }

    generatePostMenu(post) {
        return `
            <div class="post-menu">
                <button class="post-menu-btn" onclick="feedModern.togglePostMenu(${post.id})">
                    <i class="material-icons">more_vert</i>
                </button>
                <div class="dropdown-menu dropdown-menu-modern" id="menu-${post.id}" style="display:none;">
                    <div class="dropdown-item-modern" onclick="feedModern.togglePostVisibility(${post.id}, ${post.estado == 1 ? 0 : 1})">
                        <i class="fa ${post.estado == 1 ? 'fa-eye-slash' : 'fa-eye'}"></i>
                        <span>${post.estado == 1 ? 'Ocultar' : 'Mostrar'}</span>
                    </div>
                    ${this.currentUser && (this.currentUser.id == post.usuario || post.usuarioTipo == 1) ? `
                        <div class="dropdown-item-modern" onclick="window.location.href='noticias-editar.php?idR=${btoa(post.id)}'">
                            <i class="fa fa-pencil"></i>
                            <span>Editar</span>
                        </div>
                        <div class="dropdown-item-modern" onclick="feedModern.deletePost(${post.id})">
                            <i class="fa fa-trash"></i>
                            <span>Eliminar</span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    generatePostContent(post) {
        let content = '';
        
        if (post.titulo) {
            content += `<div class="post-title">${this.escapeHtml(post.titulo)}</div>`;
        }
        
        if (post.descripcion) {
            const shortDesc = this.truncateText(post.descripcion, 300);
            const needsReadMore = post.descripcion.length > 300;
            
            content += `
                <div class="post-description">
                    <div id="desc-${post.id}">
                        ${shortDesc}
                        ${needsReadMore ? `<span class="post-read-more" onclick="feedModern.expandDescription(${post.id})">... ver más</span>` : ''}
                    </div>
                    ${needsReadMore ? `<div id="desc-full-${post.id}" style="display:none;">${post.descripcion}</div>` : ''}
                </div>
            `;
        }

        return content ? `<div class="post-content">${content}</div>` : '';
    }

    generatePostMedia(post) {
        let media = '';

        // Imagen
        if (post.imagen) {
            media += `
                <div class="post-media">
                    <img src="${post.imagenUrl}" alt="${post.titulo}" onclick="feedModern.openMediaModal('${post.imagenUrl}', 'image', '${this.escapeHtml(post.titulo)}')">
                </div>
            `;
        }

        // Video de YouTube
        if (post.video) {
            media += `
                <div class="post-media">
                    <div class="post-video-container">
                        <iframe src="https://www.youtube.com/embed/${post.video}?rel=0" 
                                allow="autoplay; encrypted-media" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
            `;
        }

        // Video de Loom
        if (post.enlace_video2) {
            media += `
                <div class="post-media">
                    <div class="post-video-container">
                        <iframe src="https://www.loom.com/embed/${post.enlace_video2}" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
            `;
        }

        // Archivo adjunto
        if (post.archivo) {
            media += `
                <div class="post-content">
                    <a href="${post.archivoUrl}" target="_blank" class="post-attachment">
                        <i class="fa fa-file"></i>
                        <div>
                            <div style="font-weight:600;">Archivo adjunto</div>
                            <div style="font-size:12px;color:var(--text-secondary);">Click para descargar</div>
                        </div>
                    </a>
                </div>
            `;
        }

        return media;
    }

    generatePostStats(post) {
        const reactions = post.reacciones || 0;
        const comments = post.comentarios || 0;

        if (reactions === 0 && comments === 0) return '';

        return `
            <div class="post-stats">
                ${reactions > 0 ? `
                    <div class="post-reactions-count" onclick="feedModern.showReactionsModal(${post.id})">
                        <div class="reaction-icons-group">
                            <span class="reaction-icon-mini reaction-like"><i class="fa fa-thumbs-up"></i></span>
                        </div>
                        <span>${reactions} reaccion${reactions !== 1 ? 'es' : ''}</span>
                    </div>
                ` : ''}
                ${comments > 0 ? `
                    <div class="post-comments-count" onclick="feedModern.toggleComments(${post.id})">
                        ${comments} comentario${comments !== 1 ? 's' : ''}
                    </div>
                ` : ''}
            </div>
        `;
    }

    generatePostActions(post) {
        const userReaction = post.usuarioReaccion || null;
        const reactionClass = userReaction ? `reacted-${this.getReactionClass(userReaction)}` : '';
        const reactionIcon = userReaction ? this.getReactionIcon(userReaction) : 'fa-thumbs-o-up';
        const reactionText = userReaction ? this.getReactionText(userReaction) : 'Me gusta';

        return `
            <div class="post-actions">
                <button class="post-action-btn ${reactionClass}" onclick="feedModern.handleReaction(${post.id}, 1)" data-post-id="${post.id}">
                    <div class="reactions-selector">
                        <span class="reaction-option" onclick="event.stopPropagation(); feedModern.reactToPost(${post.id}, 1)" title="Me gusta">👍</span>
                        <span class="reaction-option" onclick="event.stopPropagation(); feedModern.reactToPost(${post.id}, 2)" title="Me encanta">❤️</span>
                        <span class="reaction-option" onclick="event.stopPropagation(); feedModern.reactToPost(${post.id}, 3)" title="Me divierte">😄</span>
                        <span class="reaction-option" onclick="event.stopPropagation(); feedModern.reactToPost(${post.id}, 4)" title="Me entristece">😢</span>
                    </div>
                    <i class="fa ${reactionIcon}"></i>
                    <span>${reactionText}</span>
                </button>
                <button class="post-action-btn" onclick="feedModern.toggleComments(${post.id})">
                    <i class="fa fa-comment-o"></i>
                    <span>Comentar</span>
                </button>
                <button class="post-action-btn" onclick="feedModern.sharePost(${post.id})">
                    <i class="fa fa-share"></i>
                    <span>Compartir</span>
                </button>
            </div>
        `;
    }

    generatePostComments(post) {
        return `
            <div class="post-comments" id="comments-section-${post.id}" style="display:none;">
                <div class="comment-input-wrapper">
                    <img src="${this.currentUser ? this.currentUser.photo : ''}" alt="" class="comment-avatar">
                    <div class="comment-input-container">
                        <textarea class="comment-input" 
                                  id="comment-input-${post.id}" 
                                  placeholder="Escribe un comentario..."
                                  rows="1"
                                  onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();feedModern.addComment(${post.id});}"></textarea>
                        <button class="comment-submit-btn" onclick="feedModern.addComment(${post.id})">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <ul class="comments-list" id="comments-list-${post.id}"></ul>
            </div>
        `;
    }

    /**
     * ==========================================
     * REACCIONES
     * ==========================================
     */
    async reactToPost(postId, reactionType) {
        try {
            const response = await fetch('../compartido/noticias-reaccionar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: postId,
                    reaccion: reactionType
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updatePostReaction(postId, reactionType, data);
                this.showToast('Reacción', data.message || 'Reacción registrada', 'success');
            } else {
                throw new Error(data.message || 'Error al reaccionar');
            }
        } catch (error) {
            console.error('❌ Error al reaccionar:', error);
            this.showToast('Error', 'No se pudo registrar la reacción', 'error');
        }
    }

    updatePostReaction(postId, reactionType, data) {
        const postCard = document.getElementById(`post-${postId}`);
        if (!postCard) return;

        // Actualizar botón de reacción
        const reactionBtn = postCard.querySelector('.post-action-btn');
        if (reactionBtn) {
            // Remover clases de reacción anteriores
            reactionBtn.className = 'post-action-btn';
            
            if (data.accion !== 'eliminar') {
                reactionBtn.classList.add(`reacted-${this.getReactionClass(reactionType)}`);
                const icon = reactionBtn.querySelector('i');
                const text = reactionBtn.querySelector('span');
                if (icon) icon.className = `fa ${this.getReactionIcon(reactionType)}`;
                if (text) text.textContent = this.getReactionText(reactionType);
            } else {
                const icon = reactionBtn.querySelector('i');
                const text = reactionBtn.querySelector('span');
                if (icon) icon.className = 'fa fa-thumbs-o-up';
                if (text) text.textContent = 'Me gusta';
            }
        }

        // Actualizar estadísticas
        this.updatePostStats(postId);
    }

    handleReaction(postId, defaultReaction) {
        // Si hace click directo, usa la reacción por defecto (Me gusta)
        this.reactToPost(postId, defaultReaction);
    }

    getReactionClass(type) {
        const classes = {1: 'like', 2: 'love', 3: 'celebrate', 4: 'curious'};
        return classes[type] || 'like';
    }

    getReactionIcon(type) {
        const icons = {1: 'fa-thumbs-up', 2: 'fa-heart', 3: 'fa-smile-o', 4: 'fa-frown-o'};
        return icons[type] || 'fa-thumbs-o-up';
    }

    getReactionText(type) {
        const texts = {1: 'Me gusta', 2: 'Me encanta', 3: 'Me divierte', 4: 'Me entristece'};
        return texts[type] || 'Me gusta';
    }

    /**
     * ==========================================
     * COMENTARIOS
     * ==========================================
     */
    toggleComments(postId) {
        const commentsSection = document.getElementById(`comments-section-${postId}`);
        if (!commentsSection) return;

        const isVisible = commentsSection.style.display !== 'none';
        
        if (isVisible) {
            commentsSection.style.display = 'none';
        } else {
            commentsSection.style.display = 'block';
            
            // Cargar comentarios si no se han cargado
            const commentsList = document.getElementById(`comments-list-${postId}`);
            if (commentsList && commentsList.children.length === 0) {
                this.loadComments(postId);
            }
            
            // Enfocar input
            const input = document.getElementById(`comment-input-${postId}`);
            if (input) setTimeout(() => input.focus(), 100);
        }
    }

    async loadComments(postId) {
        try {
            const response = await fetch('../compartido/noticias-comentarios-cargar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ postId })
            });

            const data = await response.json();

            if (data.success && data.comments) {
                const commentsList = document.getElementById(`comments-list-${postId}`);
                if (commentsList) {
                    commentsList.innerHTML = '';
                    data.comments.forEach(comment => {
                        const commentElement = this.createCommentElement(comment, postId);
                        commentsList.appendChild(commentElement);
                    });
                }
            }
        } catch (error) {
            console.error('❌ Error al cargar comentarios:', error);
        }
    }

    async addComment(postId) {
        const input = document.getElementById(`comment-input-${postId}`);
        if (!input) return;

        const comentario = input.value.trim();
        if (!comentario) return;

        try {
            const response = await fetch('../compartido/noticias-comentario-agregar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    postId,
                    comentario
                })
            });

            const data = await response.json();

            if (data.success) {
                // Agregar comentario a la lista
                const commentsList = document.getElementById(`comments-list-${postId}`);
                if (commentsList) {
                    const commentElement = this.createCommentElement(data.comment, postId);
                    commentsList.insertBefore(commentElement, commentsList.firstChild);
                }

                // Limpiar input
                input.value = '';
                input.style.height = 'auto';

                // Actualizar contador
                this.updatePostStats(postId);

                this.showToast('Comentario', 'Comentario agregado correctamente', 'success');
            } else {
                throw new Error(data.message || 'Error al agregar comentario');
            }
        } catch (error) {
            console.error('❌ Error al agregar comentario:', error);
            this.showToast('Error', 'No se pudo agregar el comentario', 'error');
        }
    }

    createCommentElement(comment, postId) {
        const li = document.createElement('li');
        li.className = 'comment-item';
        li.innerHTML = `
            <img src="${comment.foto}" alt="${comment.nombreUsuario}" class="comment-avatar">
            <div class="comment-content-wrapper">
                <div class="comment-bubble">
                    <a href="#" class="comment-author">${comment.nombreUsuario}</a>
                    <div class="comment-text">${this.escapeHtml(comment.texto)}</div>
                </div>
                <div class="comment-actions">
                    <span class="comment-time">${comment.fecha}</span>
                </div>
                ${comment.respuestas && comment.respuestas.length > 0 ? `
                    <div class="comment-replies">
                        ${comment.respuestas.map(reply => this.createReplyElement(reply).outerHTML).join('')}
                    </div>
                ` : ''}
            </div>
        `;
        return li;
    }

    createReplyElement(reply) {
        const li = document.createElement('li');
        li.className = 'comment-item';
        li.innerHTML = `
            <img src="${reply.foto}" alt="${reply.nombreUsuario}" class="comment-avatar">
            <div class="comment-content-wrapper">
                <div class="comment-bubble">
                    <a href="#" class="comment-author">${reply.nombreUsuario}</a>
                    <div class="comment-text">${this.escapeHtml(reply.texto)}</div>
                </div>
                <div class="comment-actions">
                    <span class="comment-time">${reply.fecha}</span>
                </div>
            </div>
        `;
        return li;
    }

    /**
     * ==========================================
     * GESTIÓN DE POSTS
     * ==========================================
     */
    async togglePostVisibility(postId, newState) {
        try {
            const response = await fetch('../compartido/noticias-gestionar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: postId,
                    estado: newState
                })
            });

            const data = await response.json();

            if (data.success) {
                const postCard = document.getElementById(`post-${postId}`);
                if (postCard) {
                    if (newState == 0) {
                        postCard.classList.add('post-hidden');
                    } else {
                        postCard.classList.remove('post-hidden');
                    }
                }
                
                this.showToast('Éxito', data.message || 'Estado actualizado', 'success');
                this.closePostMenu(postId);
            } else {
                throw new Error(data.message || 'Error al cambiar visibilidad');
            }
        } catch (error) {
            console.error('❌ Error al cambiar visibilidad:', error);
            this.showToast('Error', 'No se pudo cambiar la visibilidad', 'error');
        }
    }

    async deletePost(postId) {
        if (!confirm('¿Estás seguro de que deseas eliminar esta publicación?')) {
            return;
        }

        try {
            const response = await fetch('../compartido/noticias-gestionar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: postId,
                    accion: 'eliminar'
                })
            });

            const data = await response.json();

            if (data.success) {
                const postCard = document.getElementById(`post-${postId}`);
                if (postCard) {
                    postCard.style.opacity = '0';
                    postCard.style.transform = 'scale(0.9)';
                    setTimeout(() => postCard.remove(), 300);
                }
                
                this.showToast('Éxito', 'Publicación eliminada correctamente', 'success');
            } else {
                throw new Error(data.message || 'Error al eliminar');
            }
        } catch (error) {
            console.error('❌ Error al eliminar:', error);
            this.showToast('Error', 'No se pudo eliminar la publicación', 'error');
        }
    }

    togglePostMenu(postId) {
        const menu = document.getElementById(`menu-${postId}`);
        if (!menu) return;

        const isVisible = menu.style.display !== 'none';
        
        // Cerrar todos los menús abiertos
        document.querySelectorAll('.dropdown-menu-modern').forEach(m => {
            m.style.display = 'none';
        });

        if (!isVisible) {
            menu.style.display = 'block';
            
            // Cerrar al hacer click fuera
            setTimeout(() => {
                document.addEventListener('click', () => this.closePostMenu(postId), { once: true });
            }, 0);
        }
    }

    closePostMenu(postId) {
        const menu = document.getElementById(`menu-${postId}`);
        if (menu) menu.style.display = 'none';
    }

    /**
     * ==========================================
     * CREAR NUEVA PUBLICACIÓN
     * ==========================================
     */
    setupPostCreation() {
        // El formulario de creación rápida ya existe, solo agregamos funcionalidad mejorada
        const form = document.querySelector('.post-create-card form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createQuickPost();
            });
        }
    }

    async createQuickPost() {
        const textarea = document.getElementById('contenido');
        if (!textarea) return;

        const contenido = textarea.value.trim();
        if (!contenido) {
            this.showToast('Error', 'Escribe algo para publicar', 'error');
            return;
        }

        // Deshabilitar botón mientras se procesa
        const submitBtn = document.querySelector('.post-create-card .btn.deepPink-bgcolor');
        if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Publicando...';
            submitBtn.dataset.originalText = originalText;
        }

        try {
            const response = await fetch('../compartido/noticia-rapida-guardar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `contenido=${encodeURIComponent(contenido)}`
            });

            const postId = await response.text();
            
            console.log('📝 Respuesta del servidor:', postId);

            // Validar respuesta
            if (postId === 'error' || !postId || postId.trim() === '') {
                throw new Error('El servidor retornó un error');
            }

            // Verificar que sea un ID válido (número)
            const idNumerico = parseInt(postId.trim());
            if (isNaN(idNumerico)) {
                console.error('❌ Respuesta no válida:', postId);
                throw new Error('Respuesta del servidor no válida');
            }

            // Limpiar textarea
            textarea.value = '';
            textarea.style.height = 'auto';
            
            // Recargar posts desde el inicio
            this.page = 0;
            this.hasMore = true;
            this.showSkeletonLoading();
            await this.loadMorePosts();
            
            this.showToast('Éxito', '¡Publicación creada correctamente!', 'success');
            
            // Scroll al top para ver la nueva publicación
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
        } catch (error) {
            console.error('❌ Error al crear publicación:', error);
            this.showToast('Error', 'No se pudo crear la publicación: ' + error.message, 'error');
        } finally {
            // Rehabilitar botón
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalText || '<i class="fa fa-paper-plane"></i> Publicar';
            }
        }
    }

    /**
     * ==========================================
     * MODAL DE MEDIOS
     * ==========================================
     */
    setupMediaModal() {
        // Crear modal si no existe
        if (!document.getElementById('media-modal-modern')) {
            const modal = document.createElement('div');
            modal.id = 'media-modal-modern';
            modal.className = 'media-modal';
            modal.innerHTML = `
                <button class="media-modal-close" onclick="feedModern.closeMediaModal()">
                    <i class="fa fa-times"></i>
                </button>
                <div class="media-modal-content" id="media-modal-content"></div>
            `;
            document.body.appendChild(modal);
            this.mediaModal = modal;

            // Cerrar con ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.closeMediaModal();
            });

            // Cerrar al hacer click en el fondo
            modal.addEventListener('click', (e) => {
                if (e.target === modal) this.closeMediaModal();
            });
        }
    }

    openMediaModal(url, type, title = '') {
        const modal = document.getElementById('media-modal-modern');
        const content = document.getElementById('media-modal-content');
        
        if (!modal || !content) return;

        if (type === 'image') {
            content.innerHTML = `
                <img src="${url}" alt="${title}">
                ${title ? `<div class="media-modal-info"><div class="media-modal-title">${title}</div></div>` : ''}
            `;
        } else if (type === 'video') {
            content.innerHTML = `<video src="${url}" controls autoplay></video>`;
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeMediaModal() {
        const modal = document.getElementById('media-modal-modern');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    /**
     * ==========================================
     * BOTÓN VOLVER AL TOP
     * ==========================================
     */
    setupBackToTop() {
        if (!document.getElementById('back-to-top-btn')) {
            const btn = document.createElement('button');
            btn.id = 'back-to-top-btn';
            btn.className = 'back-to-top';
            btn.innerHTML = '<i class="fa fa-arrow-up"></i>';
            btn.onclick = () => this.scrollToTop();
            document.body.appendChild(btn);
            this.backToTopButton = btn;
        }
    }

    updateBackToTopVisibility() {
        const btn = this.backToTopButton || document.getElementById('back-to-top-btn');
        if (!btn) return;

        if (window.pageYOffset > 300) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    }

    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    /**
     * ==========================================
     * FUNCIONES AUXILIARES
     * ==========================================
     */
    async updatePostStats(postId) {
        try {
            const response = await fetch('../compartido/noticias-stats.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ postId })
            });

            const data = await response.json();

            if (data.success) {
                const postCard = document.getElementById(`post-${postId}`);
                if (!postCard) return;

                let statsSection = postCard.querySelector('.post-stats');
                
                if (data.reacciones > 0 || data.comentarios > 0) {
                    if (!statsSection) {
                        // Crear sección de stats
                        const actionsSection = postCard.querySelector('.post-actions');
                        statsSection = document.createElement('div');
                        statsSection.className = 'post-stats';
                        postCard.insertBefore(statsSection, actionsSection);
                    }

                    statsSection.innerHTML = `
                        ${data.reacciones > 0 ? `
                            <div class="post-reactions-count" onclick="feedModern.showReactionsModal(${postId})">
                                <div class="reaction-icons-group">
                                    <span class="reaction-icon-mini reaction-like"><i class="fa fa-thumbs-up"></i></span>
                                </div>
                                <span>${data.reacciones} reacción${data.reacciones !== 1 ? 'es' : ''}</span>
                            </div>
                        ` : ''}
                        ${data.comentarios > 0 ? `
                            <div class="post-comments-count" onclick="feedModern.toggleComments(${postId})">
                                ${data.comentarios} comentario${data.comentarios !== 1 ? 's' : ''}
                            </div>
                        ` : ''}
                    `;
                } else if (statsSection) {
                    statsSection.remove();
                }
            }
        } catch (error) {
            console.error('❌ Error al actualizar estadísticas:', error);
        }
    }

    showSkeletonLoading() {
        const container = document.getElementById('posts-container');
        if (!container) return;

        const skeletonHTML = `
            <div class="skeleton-post" id="skeleton-loading">
                <div class="post-card skeleton-animate" style="animation-delay: 0s;">
                    <div class="post-header" style="padding: 16px;">
                        <div class="skeleton skeleton-avatar" style="width: 48px; height: 48px; border-radius: 50%; display: inline-block;"></div>
                        <div style="display: inline-block; margin-left: 12px; vertical-align: top; padding-top: 4px;">
                            <div class="skeleton skeleton-text" style="width: 150px; height: 14px; margin-bottom: 8px;"></div>
                            <div class="skeleton skeleton-text" style="width: 100px; height: 12px;"></div>
                        </div>
                    </div>
                    <div class="post-content" style="padding: 12px 16px;">
                        <div class="skeleton skeleton-text" style="width: 100%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton skeleton-text" style="width: 90%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton skeleton-text" style="width: 80%; height: 14px;"></div>
                    </div>
                </div>
                
                <div class="post-card skeleton-animate" style="margin-top: 16px; animation-delay: 0.15s;">
                    <div class="post-header" style="padding: 16px;">
                        <div class="skeleton skeleton-avatar" style="width: 48px; height: 48px; border-radius: 50%; display: inline-block;"></div>
                        <div style="display: inline-block; margin-left: 12px; vertical-align: top; padding-top: 4px;">
                            <div class="skeleton skeleton-text" style="width: 150px; height: 14px; margin-bottom: 8px;"></div>
                            <div class="skeleton skeleton-text" style="width: 100px; height: 12px;"></div>
                        </div>
                    </div>
                    <div class="post-content" style="padding: 12px 16px;">
                        <div class="skeleton skeleton-text" style="width: 100%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton skeleton-text" style="width: 95%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton skeleton-text" style="width: 70%; height: 14px;"></div>
                    </div>
                </div>
                
                <div class="post-card skeleton-animate" style="margin-top: 16px; animation-delay: 0.3s;">
                    <div class="post-header" style="padding: 16px;">
                        <div class="skeleton skeleton-avatar" style="width: 48px; height: 48px; border-radius: 50%; display: inline-block;"></div>
                        <div style="display: inline-block; margin-left: 12px; vertical-align: top; padding-top: 4px;">
                            <div class="skeleton skeleton-text" style="width: 150px; height: 14px; margin-bottom: 8px;"></div>
                            <div class="skeleton skeleton-text" style="width: 100px; height: 12px;"></div>
                        </div>
                    </div>
                    <div class="post-content" style="padding: 12px 16px;">
                        <div class="skeleton skeleton-text" style="width: 100%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton skeleton-text" style="width: 85%; height: 14px;"></div>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = skeletonHTML;
    }

    hideSkeletonLoading() {
        const skeleton = document.getElementById('skeleton-loading');
        if (skeleton) {
            skeleton.remove();
        }
    }

    showLoader() {
        let loader = document.getElementById('feed-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'feed-loader';
            loader.className = 'feed-loader';
            loader.innerHTML = '<div class="loader-spinner"></div>';
            document.getElementById('posts-container').appendChild(loader);
        }
        loader.classList.add('visible');
    }

    hideLoader() {
        const loader = document.getElementById('feed-loader');
        if (loader) {
            loader.classList.remove('visible');
        }
    }

    showEmptyState() {
        const container = document.getElementById('posts-container');
        if (container && container.children.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fa fa-newspaper-o"></i></div>
                    <div class="empty-state-title">No hay publicaciones</div>
                    <div class="empty-state-description">Sé el primero en compartir algo</div>
                </div>
            `;
        }
    }

    showToast(title, message, type = 'info') {
        // Crear contenedor si no existe
        let container = document.getElementById('toast-container-modern');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container-modern';
            container.className = 'toast-container-modern';
            document.body.appendChild(container);
        }

        // Crear toast
        const toast = document.createElement('div');
        toast.className = `toast-modern toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fa ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fa fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength);
    }

    expandDescription(postId) {
        const shortDesc = document.getElementById(`desc-${postId}`);
        const fullDesc = document.getElementById(`desc-full-${postId}`);
        
        if (shortDesc && fullDesc) {
            shortDesc.style.display = 'none';
            fullDesc.style.display = 'block';
        }
    }

    sharePost(postId) {
        // Crear modal de compartir
        const modalHTML = `
            <div class="share-modal-overlay" id="share-modal-${postId}" onclick="if(event.target === this) feedModern.closeShareModal(${postId})">
                <div class="share-modal">
                    <div class="share-modal-header">
                        <h3><i class="fa fa-share-alt"></i> Compartir Publicación</h3>
                        <button class="share-modal-close" onclick="feedModern.closeShareModal(${postId})">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="share-modal-body">
                        <button class="share-option" onclick="feedModern.repostPost(${postId})">
                            <i class="fa fa-retweet"></i>
                            <div>
                                <strong>Repostear</strong>
                                <small>Compartir en tu perfil</small>
                            </div>
                        </button>
                        
                        <button class="share-option" onclick="feedModern.shareViaWhatsApp(${postId})">
                            <i class="fa fa-whatsapp"></i>
                            <div>
                                <strong>WhatsApp</strong>
                                <small>Compartir por WhatsApp</small>
                            </div>
                        </button>
                        
                        <button class="share-option" onclick="feedModern.shareViaEmail(${postId})">
                            <i class="fa fa-envelope"></i>
                            <div>
                                <strong>Correo Electrónico</strong>
                                <small>Compartir por email</small>
                            </div>
                        </button>
                        
                        <button class="share-option" onclick="feedModern.copyLink(${postId})">
                            <i class="fa fa-link"></i>
                            <div>
                                <strong>Copiar Enlace</strong>
                                <small>Copiar al portapapeles</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Agregar modal al body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Animar entrada
        setTimeout(() => {
            const modal = document.getElementById(`share-modal-${postId}`);
            if (modal) modal.classList.add('active');
        }, 10);
    }

    closeShareModal(postId) {
        const modal = document.getElementById(`share-modal-${postId}`);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.remove(), 300);
        }
    }

    async repostPost(postId) {
        if (!confirm('¿Deseas compartir esta publicación en tu perfil?')) {
            return;
        }

        try {
            const response = await fetch('../compartido/noticias-repostear.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ postId })
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Éxito', 'Publicación compartida en tu perfil', 'success');
                this.closeShareModal(postId);
                
                // Recargar feed
                this.page = 0;
                this.hasMore = true;
                this.showSkeletonLoading();
                await this.loadMorePosts();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                throw new Error(data.message || 'Error al repostear');
            }
        } catch (error) {
            console.error('❌ Error al repostear:', error);
            this.showToast('Error', 'No se pudo compartir la publicación', 'error');
        }
    }

    shareViaWhatsApp(postId) {
        const url = encodeURIComponent(window.location.origin + window.location.pathname + '?post=' + postId);
        const text = encodeURIComponent('¡Mira esta publicación!');
        const whatsappUrl = `https://wa.me/?text=${text}%20${url}`;
        window.open(whatsappUrl, '_blank');
        this.closeShareModal(postId);
    }

    shareViaEmail(postId) {
        const url = window.location.origin + window.location.pathname + '?post=' + postId;
        const subject = encodeURIComponent('Te comparto esta publicación');
        const body = encodeURIComponent(`Hola,\n\nTe comparto esta publicación que me pareció interesante:\n\n${url}`);
        window.location.href = `mailto:?subject=${subject}&body=${body}`;
        this.closeShareModal(postId);
    }

    copyLink(postId) {
        const url = window.location.origin + window.location.pathname + '?post=' + postId;
        
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                this.showToast('Enlace copiado', 'El enlace se copió al portapapeles', 'success');
                this.closeShareModal(postId);
            }).catch(() => {
                // Fallback
                this.copyLinkFallback(url, postId);
            });
        } else {
            this.copyLinkFallback(url, postId);
        }
    }

    copyLinkFallback(url, postId) {
        const textarea = document.createElement('textarea');
        textarea.value = url;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            document.execCommand('copy');
            this.showToast('Enlace copiado', 'El enlace se copió al portapapeles', 'success');
        } catch (err) {
            this.showToast('Error', 'No se pudo copiar el enlace', 'error');
        }
        
        document.body.removeChild(textarea);
        this.closeShareModal(postId);
    }

    async showReactionsModal(postId) {
        console.log('📊 Mostrando reacciones del post:', postId);
        
        // Crear modal si no existe
        let modal = document.getElementById('reactions-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'reactions-modal';
            modal.className = 'reactions-modal';
            modal.innerHTML = `
                <div class="reactions-modal-content" onclick="event.stopPropagation()">
                    <div class="reactions-modal-header">
                        <h3 class="reactions-modal-title">Reacciones</h3>
                        <button class="reactions-modal-close" onclick="feedModern.closeReactionsModal()">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="reactions-modal-tabs" id="reactions-tabs"></div>
                    <div class="reactions-modal-body" id="reactions-body">
                        <div class="reactions-loading">
                            <div class="reactions-loading-spinner"></div>
                            <div>Cargando reacciones...</div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Cerrar al hacer clic en el fondo
            modal.addEventListener('click', () => this.closeReactionsModal());
            
            // Cerrar con tecla ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    this.closeReactionsModal();
                }
            });
        }
        
        // Mostrar modal
        modal.classList.add('active');
        
        // Cargar reacciones
        await this.loadReactions(postId);
    }
    
    closeReactionsModal() {
        const modal = document.getElementById('reactions-modal');
        if (modal) {
            modal.classList.remove('active');
        }
    }
    
    async loadReactions(postId) {
        try {
            const response = await fetch('../compartido/noticias-reacciones-lista.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `postId=${postId}`
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al cargar reacciones');
            }
            
            console.log('✅ Reacciones cargadas:', data.total);
            this.renderReactions(data.reacciones);
            
        } catch (error) {
            console.error('❌ Error al cargar reacciones:', error);
            document.getElementById('reactions-body').innerHTML = `
                <div class="reactions-empty">
                    <div class="reactions-empty-icon">😕</div>
                    <div class="reactions-empty-text">No se pudieron cargar las reacciones</div>
                </div>
            `;
        }
    }
    
    renderReactions(reacciones) {
        const tabsContainer = document.getElementById('reactions-tabs');
        const bodyContainer = document.getElementById('reactions-body');
        
        if (!reacciones || reacciones.length === 0) {
            tabsContainer.innerHTML = '';
            bodyContainer.innerHTML = `
                <div class="reactions-empty">
                    <div class="reactions-empty-icon">👍</div>
                    <div class="reactions-empty-text">Aún no hay reacciones</div>
                </div>
            `;
            return;
        }
        
        // Agrupar reacciones por tipo
        const reaccionesPorTipo = {
            all: reacciones,
            1: reacciones.filter(r => r.tipoReaccion === 1),
            2: reacciones.filter(r => r.tipoReaccion === 2),
            3: reacciones.filter(r => r.tipoReaccion === 3),
            4: reacciones.filter(r => r.tipoReaccion === 4)
        };
        
        // Crear tabs
        const tabs = [
            { id: 'all', label: 'Todas', emoji: '', count: reacciones.length }
        ];
        
        if (reaccionesPorTipo[1].length > 0) {
            tabs.push({ id: '1', label: 'Me gusta', emoji: '👍', count: reaccionesPorTipo[1].length });
        }
        if (reaccionesPorTipo[2].length > 0) {
            tabs.push({ id: '2', label: 'Me encanta', emoji: '❤️', count: reaccionesPorTipo[2].length });
        }
        if (reaccionesPorTipo[3].length > 0) {
            tabs.push({ id: '3', label: 'Me divierte', emoji: '😄', count: reaccionesPorTipo[3].length });
        }
        if (reaccionesPorTipo[4].length > 0) {
            tabs.push({ id: '4', label: 'Me entristece', emoji: '😢', count: reaccionesPorTipo[4].length });
        }
        
        // Renderizar tabs
        tabsContainer.innerHTML = tabs.map(tab => `
            <button class="reactions-tab ${tab.id === 'all' ? 'active' : ''}" 
                    onclick="feedModern.filterReactions('${tab.id}', ${JSON.stringify(reaccionesPorTipo).replace(/"/g, '&quot;')})">
                ${tab.emoji ? `<span class="reactions-tab-emoji">${tab.emoji}</span>` : ''}
                <span>${tab.label}</span>
                <span class="reactions-tab-count">${tab.count}</span>
            </button>
        `).join('');
        
        // Renderizar lista inicial (todas)
        this.renderReactionsList(reacciones);
    }
    
    filterReactions(tipoId, reaccionesPorTipo) {
        // Actualizar tabs activos
        document.querySelectorAll('.reactions-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.closest('.reactions-tab').classList.add('active');
        
        // Mostrar reacciones filtradas
        const reacciones = tipoId === 'all' ? reaccionesPorTipo.all : reaccionesPorTipo[tipoId];
        this.renderReactionsList(reacciones);
    }
    
    renderReactionsList(reacciones) {
        const bodyContainer = document.getElementById('reactions-body');
        
        if (!reacciones || reacciones.length === 0) {
            bodyContainer.innerHTML = `
                <div class="reactions-empty">
                    <div class="reactions-empty-icon">🤔</div>
                    <div class="reactions-empty-text">No hay reacciones de este tipo</div>
                </div>
            `;
            return;
        }
        
        bodyContainer.innerHTML = `
            <ul class="reactions-list">
                ${reacciones.map(reaccion => `
                    <li class="reaction-item">
                        <img src="${reaccion.foto}" alt="${reaccion.nombreCompleto}" class="reaction-item-avatar">
                        <div class="reaction-item-content">
                            <div class="reaction-item-info">
                                <div class="reaction-item-name">${this.escapeHtml(reaccion.nombreCompleto)}</div>
                                <div class="reaction-item-time">${reaccion.fecha}</div>
                            </div>
                            <div class="reaction-item-emoji" title="${reaccion.reaccionTexto}">
                                ${reaccion.reaccionEmoji}
                            </div>
                        </div>
                    </li>
                `).join('')}
            </ul>
        `;
    }

    attachPostEventListeners(postElement, post) {
        // Agregar listeners adicionales si es necesario
        // Por ejemplo, para auto-resize de textareas
        const commentInput = postElement.querySelector(`#comment-input-${post.id}`);
        if (commentInput) {
            commentInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    }
}

// Inicializar cuando el DOM esté listo
let feedModern;
document.addEventListener('DOMContentLoaded', () => {
    feedModern = new NewsFeedModern();
});

// Función legacy para compatibilidad
function crearNoticia() {
    if (feedModern) {
        feedModern.createQuickPost();
    }
}

