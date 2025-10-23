/**
 * Buscador General en Tiempo Real
 * Sistema de búsqueda avanzado con resultados categorizados
 */

let searchTimeout = null;
let currentSearchQuery = '';
let isSearching = false;

// Inicializar el buscador
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('buscador-general-input');
    const searchContainer = document.getElementById('buscador-general-container');
    const resultsContainer = document.getElementById('buscador-resultados');
    
    if (!searchInput) return;
    
    // Event listener para el input
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        handleSearch(query);
        
        // Prevenir el submit del formulario al presionar Enter si hay resultados
        if (query.length >= 2) {
            e.preventDefault();
        }
    });
    
    // Prevenir submit automático cuando hay resultados visibles
    const searchForm = document.getElementById('buscador-general-container');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput.value.trim();
            // Solo prevenir si hay menos de 2 caracteres o si los resultados están visibles
            if (query.length < 2 || resultsContainer.style.display === 'block') {
                // Si hay resultados visibles, no hacer submit, dejar que navegue por click
                if (resultsContainer.style.display === 'block') {
                    e.preventDefault();
                    return false;
                }
            }
        });
    }
    
    // Event listener para el foco
    searchInput.addEventListener('focus', function(e) {
        const query = e.target.value.trim();
        if (query.length >= 2) {
            resultsContainer.style.display = 'block';
        }
    });
    
    // Cerrar resultados al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target)) {
            resultsContainer.style.display = 'none';
        }
    });
    
    // Prevenir que se cierre al hacer click dentro de los resultados
    resultsContainer.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Teclas de navegación
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            resultsContainer.style.display = 'none';
            searchInput.blur();
        }
    });
});

// Manejar la búsqueda con debouncing
function handleSearch(query) {
    currentSearchQuery = query;
    
    // Limpiar el timeout anterior
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    const resultsContainer = document.getElementById('buscador-resultados');
    
    // Si el query es muy corto, limpiar resultados
    if (query.length < 2) {
        resultsContainer.style.display = 'none';
        resultsContainer.innerHTML = '';
        return;
    }
    
    // Mostrar indicador de carga
    showLoadingState();
    
    // Realizar la búsqueda después de 300ms de inactividad
    searchTimeout = setTimeout(function() {
        performSearch(query);
    }, 300);
}

// Mostrar estado de carga
function showLoadingState() {
    const resultsContainer = document.getElementById('buscador-resultados');
    resultsContainer.style.display = 'block';
    resultsContainer.innerHTML = `
        <div class="search-loading">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Buscando...</span>
            </div>
            <p class="mt-2 mb-0">Buscando...</p>
        </div>
    `;
}

// Realizar la búsqueda AJAX
function performSearch(query) {
    if (isSearching) return;
    
    isSearching = true;
    
    // Usar ruta relativa que funcione desde cualquier módulo
    const ajaxUrl = '../compartido/buscador-general-ajax.php?query=' + encodeURIComponent(query);
    
    fetch(ajaxUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la petición: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        isSearching = false;
        displayResults(data);
    })
    .catch(error => {
        isSearching = false;
        console.error('Error en la búsqueda:', error);
        showErrorState();
    });
}

// Mostrar los resultados
function displayResults(data) {
    const resultsContainer = document.getElementById('buscador-resultados');
    
    // Verificar si hay resultados
    const totalResults = 
        (data.usuarios ? data.usuarios.length : 0) + 
        (data.estudiantes ? data.estudiantes.length : 0) + 
        (data.asignaturas ? data.asignaturas.length : 0) + 
        (data.areas ? data.areas.length : 0) + 
        (data.cursos ? data.cursos.length : 0) + 
        (data.paginas ? data.paginas.length : 0);
    
    if (totalResults === 0) {
        resultsContainer.innerHTML = `
            <div class="search-no-results">
                <i class="fa fa-search fa-3x text-muted mb-3"></i>
                <p class="mb-0">No se encontraron resultados para "<strong>${escapeHtml(data.query)}</strong>"</p>
                <small class="text-muted">Intenta con otros términos de búsqueda</small>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    // USUARIOS
    if (data.usuarios && data.usuarios.length > 0) {
        html += `
            <div class="search-category">
                <div class="search-category-header">
                    <i class="fa fa-users"></i>
                    <span>Usuarios</span>
                    <span class="search-category-count">${data.usuarios.length}</span>
                </div>
                <div class="search-category-items">
        `;
        
        data.usuarios.forEach(function(usuario) {
            html += `
                <a href="${usuario.url}" class="search-result-item">
                    <div class="search-result-avatar">
                        <img src="../files/fotos/${usuario.foto}" alt="${escapeHtml(usuario.nombre)}" onerror="this.src='../files/fotos/default.png'">
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${highlightText(usuario.nombre, data.query)}</div>
                        <div class="search-result-meta">
                            <span class="badge badge-sm" style="background-color: ${usuario.tipoColor};">
                                <i class="fa ${usuario.tipoIcono}"></i> ${usuario.tipo}
                            </span>
                            ${usuario.email ? '<span class="text-muted ml-2"><i class="fa fa-envelope"></i> ' + escapeHtml(usuario.email) + '</span>' : ''}
                        </div>
                    </div>
                    <div class="search-result-arrow">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    // ESTUDIANTES
    if (data.estudiantes && data.estudiantes.length > 0) {
        html += `
            <div class="search-category">
                <div class="search-category-header">
                    <i class="fa fa-user-graduate"></i>
                    <span>Estudiantes</span>
                    <span class="search-category-count">${data.estudiantes.length}</span>
                </div>
                <div class="search-category-items">
        `;
        
        data.estudiantes.forEach(function(estudiante) {
            html += `
                <a href="${estudiante.url}" class="search-result-item">
                    <div class="search-result-avatar">
                        <img src="../files/fotos/${estudiante.foto}" alt="${escapeHtml(estudiante.nombre)}" onerror="this.src='../files/fotos/default.png'">
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${highlightText(estudiante.nombre, data.query)}</div>
                        <div class="search-result-meta">
                            <span class="badge badge-sm" style="background-color: #f093fb;">
                                <i class="fa fa-graduation-cap"></i> ${estudiante.estado}
                            </span>
                            ${estudiante.matricula ? '<span class="text-muted ml-2">Mat: ' + escapeHtml(estudiante.matricula) + '</span>' : ''}
                            ${estudiante.documento ? '<span class="text-muted ml-2">Doc: ' + escapeHtml(estudiante.documento) + '</span>' : ''}
                        </div>
                    </div>
                    <div class="search-result-arrow">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    // ASIGNATURAS
    if (data.asignaturas && data.asignaturas.length > 0) {
        html += `
            <div class="search-category">
                <div class="search-category-header">
                    <i class="fa fa-book"></i>
                    <span>Asignaturas</span>
                    <span class="search-category-count">${data.asignaturas.length}</span>
                </div>
                <div class="search-category-items">
        `;
        
        data.asignaturas.forEach(function(asignatura) {
            html += `
                <a href="${asignatura.url}" class="search-result-item">
                    <div class="search-result-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <i class="fa fa-book"></i>
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${highlightText(asignatura.nombre, data.query)}</div>
                        <div class="search-result-meta">
                            <span class="badge badge-sm badge-${asignatura.estado === 'Activa' ? 'success' : 'secondary'}">${asignatura.estado}</span>
                            <span class="text-muted ml-2">ID: ${asignatura.id}</span>
                        </div>
                    </div>
                    <div class="search-result-arrow">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    // ÁREAS
    if (data.areas && data.areas.length > 0) {
        html += `
            <div class="search-category">
                <div class="search-category-header">
                    <i class="fa fa-layer-group"></i>
                    <span>Áreas</span>
                    <span class="search-category-count">${data.areas.length}</span>
                </div>
                <div class="search-category-items">
        `;
        
        data.areas.forEach(function(area) {
            html += `
                <a href="${area.url}" class="search-result-item">
                    <div class="search-result-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fa fa-layer-group"></i>
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${highlightText(area.nombre, data.query)}</div>
                        <div class="search-result-meta">
                            <span class="badge badge-sm badge-${area.estado === 'Activa' ? 'success' : 'secondary'}">${area.estado}</span>
                            <span class="text-muted ml-2">ID: ${area.id}</span>
                        </div>
                    </div>
                    <div class="search-result-arrow">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    // CURSOS
    if (data.cursos && data.cursos.length > 0) {
        html += `
            <div class="search-category">
                <div class="search-category-header">
                    <i class="fa fa-graduation-cap"></i>
                    <span>Cursos</span>
                    <span class="search-category-count">${data.cursos.length}</span>
                </div>
                <div class="search-category-items">
        `;
        
        data.cursos.forEach(function(curso) {
            html += `
                <a href="${curso.url}" class="search-result-item">
                    <div class="search-result-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${highlightText(curso.nombre, data.query)}</div>
                        <div class="search-result-meta">
                            <span class="badge badge-sm badge-${curso.estado === 'Activo' ? 'success' : 'secondary'}">${curso.estado}</span>
                            <span class="text-muted ml-2">Código: ${curso.codigo}</span>
                        </div>
                    </div>
                    <div class="search-result-arrow">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    // PÁGINAS
    if (data.paginas && data.paginas.length > 0) {
        html += `
            <div class="search-category">
                <div class="search-category-header">
                    <i class="fa fa-file-alt"></i>
                    <span>Páginas</span>
                    <span class="search-category-count">${data.paginas.length}</span>
                </div>
                <div class="search-category-items">
        `;
        
        data.paginas.forEach(function(pagina) {
            html += `
                <a href="${pagina.url}" class="search-result-item">
                    <div class="search-result-icon" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
                        <i class="fa fa-file-alt"></i>
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${highlightText(pagina.nombre, data.query)}</div>
                        ${pagina.descripcion ? '<div class="search-result-meta"><span class="text-muted">' + escapeHtml(pagina.descripcion.substring(0, 80)) + (pagina.descripcion.length > 80 ? '...' : '') + '</span></div>' : ''}
                    </div>
                    <div class="search-result-arrow">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    resultsContainer.innerHTML = html;
    resultsContainer.style.display = 'block';
}

// Mostrar estado de error
function showErrorState() {
    const resultsContainer = document.getElementById('buscador-resultados');
    resultsContainer.innerHTML = `
        <div class="search-error">
            <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <p class="mb-0">Error al realizar la búsqueda</p>
            <small class="text-muted">Por favor, intenta nuevamente</small>
        </div>
    `;
}

// Resaltar el texto buscado
function highlightText(text, query) {
    if (!query || query.length < 2) return escapeHtml(text);
    
    const regex = new RegExp('(' + escapeRegExp(query) + ')', 'gi');
    return escapeHtml(text).replace(regex, '<mark>$1</mark>');
}

// Escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Escapar caracteres especiales de regex
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Limpiar búsqueda
function clearSearch() {
    const searchInput = document.getElementById('buscador-general-input');
    const resultsContainer = document.getElementById('buscador-resultados');
    
    if (searchInput) searchInput.value = '';
    if (resultsContainer) {
        resultsContainer.innerHTML = '';
        resultsContainer.style.display = 'none';
    }
    
    currentSearchQuery = '';
}

