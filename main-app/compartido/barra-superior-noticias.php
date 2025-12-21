<!-- Barra Superior Moderna de Noticias -->
<style>
.news-toolbar {
    background: var(--card-bg, #fff);
    border-radius: 8px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.08), 0 2px 2px rgba(0,0,0,.08);
    padding: 12px 16px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.news-toolbar-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.news-toolbar-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.news-toolbar-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.news-toolbar-search {
    flex: 1;
    min-width: 250px;
    max-width: 400px;
    position: relative;
}

.news-toolbar-search input {
    width: 100%;
    padding: 10px 40px 10px 40px;
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: 24px;
    font-size: 14px;
    transition: all 0.2s;
}

.news-toolbar-search input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.news-toolbar-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary, #666);
}

.news-toolbar-search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--text-secondary, #666);
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    padding: 4px;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    transition: all 0.2s;
}

.news-toolbar-search-clear.visible {
    opacity: 1;
    visibility: visible;
}

.news-toolbar-search-clear:hover {
    background: var(--hover-bg, #f3f2ef);
    color: var(--text-primary, #000);
}

.news-toolbar-dropdown {
    position: relative;
}

.news-toolbar-dropdown-btn {
    background: transparent;
    border: 1px solid var(--border-color, #e0e0e0);
    color: var(--text-primary, #000);
    padding: 10px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.news-toolbar-dropdown-btn:hover {
    background: var(--hover-bg, #f3f2ef);
    border-color: #667eea;
}

.news-toolbar-badge {
    display: inline-block;
    background: #0a66c2;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}

.news-toolbar-clear-filter {
    background: #f44336;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.news-toolbar-clear-filter:hover {
    background: #d32f2f;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4);
}

@media (max-width: 768px) {
    .news-toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .news-toolbar-search {
        max-width: 100%;
        order: -1;
    }
    
    .news-toolbar-btn,
    .news-toolbar-dropdown-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="news-toolbar">
    <!-- Botón Crear Publicación Completa -->
    <button class="news-toolbar-btn news-toolbar-btn-primary" 
            onclick="window.location.href='../compartido/noticias-crear-publicacion.php'"
            data-hint="Crea una publicación con imágenes, videos y archivos">
        <i class="fa fa-plus-circle"></i>
        <span><?=$frases[263][$datosUsuarioActual['uss_idioma']];?></span>
    </button>

    <!-- Buscador Mejorado -->
    <div class="news-toolbar-search">
        <i class="fa fa-search news-toolbar-search-icon"></i>
        <input type="search" 
               id="news-search-input"
               placeholder="Buscar publicaciones..."
               value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>"
               autocomplete="off">
        <button type="button" class="news-toolbar-search-clear" id="clear-search-btn" onclick="clearSearch()">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <!-- Dropdown de Más Acciones -->
    <div class="news-toolbar-dropdown">
        <button class="news-toolbar-dropdown-btn dropdown-toggle" 
                type="button" 
                data-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false">
            <i class="fa fa-ellipsis-h"></i>
            <span>Más acciones</span>
        </button>
        <div class="dropdown-menu dropdown-menu-modern">
            <a class="dropdown-item-modern" href="../compartido/noticias-gestionar-noticia.php?e=<?=base64_encode(1)?>">
                <i class="fa fa-eye"></i>
                <span><?=$frases[135][$datosUsuarioActual['uss_idioma']];?></span>
            </a>
            <a class="dropdown-item-modern" href="../compartido/noticias-gestionar-noticia.php?e=<?=base64_encode(0)?>">
                <i class="fa fa-eye-slash"></i>
                <span><?=$frases[136][$datosUsuarioActual['uss_idioma']];?></span>
            </a>
            <a class="dropdown-item-modern" href="#" name="../compartido/noticias-gestionar-noticia.php?e=<?=base64_encode(2)?>" onClick="deseaEliminar(this)">
                <i class="fa fa-trash"></i>
                <span><?=$frases[137][$datosUsuarioActual['uss_idioma']];?></span>
            </a>
        </div>
    </div>

    <!-- Limpiar Filtros (si hay búsqueda activa) -->
    <?php if(!empty($_GET["busqueda"]) || !empty($_GET["usuario"])): ?>
        <button class="news-toolbar-clear-filter" onclick="window.location.href='<?=$_SERVER['PHP_SELF'];?>'">
            <i class="fa fa-times-circle"></i>
            <span>Limpiar filtros</span>
        </button>
        <?php if(!empty($_GET["busqueda"])): ?>
            <span class="news-toolbar-badge">
                Buscando: "<?=htmlspecialchars($_GET["busqueda"]);?>"
            </span>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// ==========================================
// BUSCADOR SIMPLE - SIN DEPENDENCIAS
// ==========================================

(function() {
    console.log('🔧 Inicializando buscador simple...');
    
    // Evitar redeclaración - usar window si ya existe
    var searchTimeout = window.barraSuperiorSearchTimeout || null;
    window.barraSuperiorSearchTimeout = searchTimeout;
    let isSearching = false;
    
    // Esperar a que el DOM esté listo
    function init() {
        const searchInput = document.getElementById('news-search-input');
        const clearBtn = document.getElementById('clear-search-btn');
        
        if (!searchInput) {
            console.error('❌ Input no encontrado');
            return;
        }
        
        console.log('✅ Buscador inicializado');
        
        // Mostrar/ocultar botón X
        function updateClearButton() {
            if (clearBtn) {
                if (searchInput.value.length > 0) {
                    clearBtn.classList.add('visible');
                } else {
                    clearBtn.classList.remove('visible');
                }
            }
        }
        
        updateClearButton();
        
        // Al escribir
        searchInput.addEventListener('input', function() {
            const value = this.value.trim();
            updateClearButton();
            
            if (searchTimeout) clearTimeout(searchTimeout);
            
            if (value.length === 0) {
                // Borra todo - recargar sin filtro
                window.location.href = 'noticias.php';
            } else if (value.length >= 2) {
                // Buscar después de 800ms
                searchTimeout = setTimeout(function() {
                    window.location.href = 'noticias.php?busqueda=' + encodeURIComponent(value);
                }, 800);
                window.barraSuperiorSearchTimeout = searchTimeout;
            }
        });
        
        // Enter para buscar inmediato
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.value.trim();
                if (value.length >= 2) {
                    window.location.href = 'noticias.php?busqueda=' + encodeURIComponent(value);
                }
            }
        });
    }
    
    // Ejecutar cuando DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Función global para limpiar búsqueda
function clearSearch() {
    window.location.href = 'noticias.php';
}

</script>