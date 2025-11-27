/**
 * Modern Calificaciones JavaScript
 * Enhanced UX for calificaciones-todas-rapido.php
 */

class ModernCalificaciones {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.setupKeyboardShortcuts();
    }

    init() {
        // Initialize tooltips
        this.initTooltips();
        
        // Setup responsive table
        this.setupResponsiveTable();
        
        // Initialize grade color coding
        this.initGradeColors();
        
        // Setup auto-save functionality
        this.setupAutoSave();
    }

    initTooltips() {
        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    setupResponsiveTable() {
        const table = document.getElementById('tabla_notas');
        if (!table) return;

        // Add horizontal scroll indicator
        const container = table.closest('.responsive-table');
        if (container) {
            container.addEventListener('scroll', () => {
                const scrollLeft = container.scrollLeft;
                const maxScroll = container.scrollWidth - container.clientWidth;
                
                if (scrollLeft > 0) {
                    container.classList.add('scrolled-left');
                } else {
                    container.classList.remove('scrolled-left');
                }
                
                if (scrollLeft < maxScroll) {
                    container.classList.add('scrolled-right');
                } else {
                    container.classList.remove('scrolled-right');
                }
            });
        }
    }

    initGradeColors() {
        // Apply color coding to grade inputs
        const gradeInputs = document.querySelectorAll('.input-grade');
        gradeInputs.forEach(input => {
            this.applyGradeColor(input);
            
            // Add event listener for real-time color updates
            input.addEventListener('input', () => {
                this.applyGradeColor(input);
            });
        });
    }

    applyGradeColor(input) {
        const value = parseFloat(input.value);
        if (isNaN(value)) {
            input.className = input.className.replace(/grade-\w+/g, '');
            return;
        }

        // Remove existing grade classes
        input.className = input.className.replace(/grade-\w+/g, '');
        
        // Apply appropriate color class
        if (value >= 4.5) {
            input.classList.add('grade-excellent');
        } else if (value >= 4.0) {
            input.classList.add('grade-good');
        } else if (value >= 3.5) {
            input.classList.add('grade-average');
        } else if (value >= 3.0) {
            input.classList.add('grade-poor');
        } else if (value > 0) {
            input.classList.add('grade-failing');
        }
    }

    setupAutoSave() {
        // Auto-save functionality for grade inputs
        const gradeInputs = document.querySelectorAll('input[data-cod-estudiante]');
        gradeInputs.forEach(input => {
            let saveTimeout;
            
            input.addEventListener('input', () => {
                // Clear previous timeout
                clearTimeout(saveTimeout);
                
                // Show typing indicator
                this.showTypingIndicator(input);
                
                // Set new timeout for auto-save
                saveTimeout = setTimeout(() => {
                    this.autoSaveGrade(input);
                }, 2000); // Auto-save after 2 seconds of inactivity
            });
            
            input.addEventListener('blur', () => {
                clearTimeout(saveTimeout);
                this.hideTypingIndicator(input);
            });
        });
    }

    showTypingIndicator(input) {
        const indicator = input.parentElement.querySelector('.typing-indicator');
        if (!indicator) {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.innerHTML = '<i class="fas fa-circle" style="color: var(--warning-color); font-size: 0.5rem;"></i>';
            input.parentElement.appendChild(typingDiv);
        }
    }

    hideTypingIndicator(input) {
        const indicator = input.parentElement.querySelector('.typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    autoSaveGrade(input) {
        // Only auto-save if the value has changed and is valid
        const currentValue = input.value.trim();
        const originalValue = input.getAttribute('data-nota-anterior') || '';
        
        if (currentValue !== originalValue && this.isValidGrade(currentValue)) {
            this.showNotification('Guardando automáticamente...', 'info');
            // Trigger the existing notasGuardar function
            notasGuardar(input, input.closest('tr').id, 'tabla_notas');
        }
    }

    isValidGrade(value) {
        const num = parseFloat(value);
        return !isNaN(num) && num >= 0 && num <= 5;
    }

    setupEventListeners() {
        // Enhanced notification system
        this.setupNotifications();
        
        // Setup bulk operations
        this.setupBulkOperations();
        
        // Setup keyboard navigation
        this.setupKeyboardNavigation();
    }

    setupNotifications() {
        // Override the original notification function
        window.showNotification = (message, type = 'info', duration = 5000) => {
            this.showNotification(message, type, duration);
        };
    }

    showNotification(message, type = 'info', duration = 5000) {
        const icons = {
            'success': 'fas fa-check-circle',
            'warning': 'fas fa-exclamation-triangle',
            'error': 'fas fa-times-circle',
            'info': 'fas fa-info-circle'
        };
        
        const colors = {
            'success': 'var(--success-color)',
            'warning': 'var(--warning-color)',
            'error': 'var(--danger-color)',
            'info': 'var(--primary-color)'
        };
        
        const notification = document.createElement('div');
        notification.className = 'toast-modern fade-in-up';
        notification.style.borderLeft = `4px solid ${colors[type]}`;
        notification.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="${icons[type]} me-2" style="color: ${colors[type]}"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        const container = document.getElementById('respRCT');
        if (container) {
            container.appendChild(notification);
            
            // Auto-remove after duration
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }
            }, duration);
        }
    }

    setupBulkOperations() {
        // Enhanced bulk grade input
        const bulkInputs = document.querySelectorAll('input[name]');
        bulkInputs.forEach(input => {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.executeBulkGrade(input);
                }
            });
            
            input.addEventListener('blur', () => {
                if (input.value.trim()) {
                    this.executeBulkGrade(input);
                }
            });
        });
    }

    executeBulkGrade(input) {
        const value = input.value.trim();
        if (!this.isValidGrade(value)) {
            this.showNotification('Por favor ingrese una nota válida (0-5)', 'error');
            input.focus();
            return;
        }
        
        // Show confirmation for bulk operations
        this.showNotification(`Aplicando nota ${value} a todos los estudiantes...`, 'info');
        
        // Execute the existing notasMasiva function
        notasMasiva(input);
        
        // Clear the input
        input.value = '';
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl + S: Save all changes
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                this.saveAllChanges();
            }
            
            // Ctrl + R: Refresh data
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                this.refreshData();
            }
            
            // Escape: Clear focused input
            if (e.key === 'Escape') {
                const focusedInput = document.activeElement;
                if (focusedInput && focusedInput.classList.contains('input-grade')) {
                    focusedInput.value = focusedInput.getAttribute('data-nota-anterior') || '';
                    focusedInput.blur();
                }
            }
        });
    }

    setupKeyboardNavigation() {
        const gradeInputs = document.querySelectorAll('.input-grade');
        gradeInputs.forEach((input, index) => {
            input.addEventListener('keydown', (e) => {
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        const nextInput = gradeInputs[index + 1];
                        if (nextInput) nextInput.focus();
                        break;
                        
                    case 'ArrowUp':
                        e.preventDefault();
                        const prevInput = gradeInputs[index - 1];
                        if (prevInput) prevInput.focus();
                        break;
                        
                    case 'Tab':
                        // Let default tab behavior work
                        break;
                }
            });
        });
    }

    saveAllChanges() {
        this.showNotification('Guardando todos los cambios...', 'info');
        // Implementation would depend on your backend requirements
    }

    refreshData() {
        this.showNotification('Actualizando datos...', 'info');
        window.location.reload();
    }

    // Public methods for external use
    highlightStudent(studentId) {
        const row = document.getElementById(`fila_${studentId}`);
        if (row) {
            row.classList.add('highlighted', 'pulse');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            setTimeout(() => {
                row.classList.remove('pulse');
            }, 2000);
        }
    }

    exportToExcel() {
        this.showNotification('Generando archivo Excel...', 'info');
        // Implementation for Excel export
    }

    printTable() {
        window.print();
    }
}

/**
 * Recalcula el porcentaje y definitiva de un estudiante
 * @param {string} codEst - Código del estudiante
 */
function recalcularDefinitiva(codEst) {
    const fila = document.getElementById(`fila_${codEst}`);
    if (!fila) {
        console.warn(`No se encontró la fila del estudiante ${codEst}`);
        return;
    }

    // Obtener todos los inputs de notas del estudiante
    const inputsNotas = fila.querySelectorAll('input[data-cod-estudiante="' + codEst + '"][data-valor-nota]');
    
    let sumaNotas = 0;
    let sumaPorcentajes = 0;
    let notasRegistradas = 0;

    inputsNotas.forEach(input => {
        const nota = parseFloat(input.value);
        const porcentaje = parseFloat(input.getAttribute('data-valor-nota'));
        
        if (!isNaN(nota) && nota > 0 && !isNaN(porcentaje)) {
            const notaPonderada = nota * (porcentaje / 100);
            sumaNotas += notaPonderada;
            sumaPorcentajes += porcentaje;
            notasRegistradas++;
        }
    });

    // Calcular porcentaje completado
    const porcentajeCompletado = sumaPorcentajes;
    
    // Calcular definitiva
    let definitiva = 0;
    if (sumaPorcentajes > 0) {
        definitiva = (sumaNotas / sumaPorcentajes) * 100;
    }
    
    // Redondear a 1 decimal
    definitiva = Math.round(definitiva * 10) / 10;
    
    // Actualizar la celda de porcentaje
    const celdaPorcentaje = fila.querySelector('td[style*="text-align:center"]:nth-last-child(2)');
    if (celdaPorcentaje) {
        celdaPorcentaje.innerHTML = porcentajeCompletado.toFixed(0) + '<p>&nbsp;</p>';
        // Agregar animación de actualización
        celdaPorcentaje.classList.add('valor-actualizado');
        setTimeout(() => celdaPorcentaje.classList.remove('valor-actualizado'), 600);
    }
    
    // Actualizar la celda de definitiva
    const enlaceDefinitiva = fila.querySelector('a[id^="definitiva_"]');
    if (enlaceDefinitiva) {
        const valorAnterior = parseFloat(enlaceDefinitiva.textContent);
        enlaceDefinitiva.textContent = definitiva.toFixed(1);
        
        // Aplicar color según la nota
        const color = aplicarColorNota(definitiva);
        if (color) {
            enlaceDefinitiva.style.color = color;
        }
        
        // Agregar animación si el valor cambió
        if (!isNaN(valorAnterior) && valorAnterior !== definitiva) {
            const celdaDefinitiva = enlaceDefinitiva.closest('td');
            if (celdaDefinitiva) {
                celdaDefinitiva.classList.add('valor-actualizado');
                setTimeout(() => celdaDefinitiva.classList.remove('valor-actualizado'), 600);
            }
        }
    }
    
    console.log(`Definitiva recalculada para ${codEst}: ${definitiva.toFixed(1)} (${porcentajeCompletado}%)`);
}

/**
 * Recalcula los promedios de todas las actividades
 */
function recalcularPromedios() {
    const tabla = document.getElementById('tabla_notas');
    if (!tabla) {
        console.warn('No se encontró la tabla tabla_notas');
        return;
    }

    const tbody = tabla.querySelector('tbody');
    const filaPromedios = tbody.querySelector('tr.fila-promedios');
    
    if (!filaPromedios) {
        console.warn('No se encontró la fila de promedios');
        return;
    }

    // Obtener todas las filas de estudiantes (excluyendo la fila de promedios)
    const filasEstudiantes = tbody.querySelectorAll('tr:not(.fila-promedios)');
    
    if (filasEstudiantes.length === 0) {
        console.warn('No se encontraron filas de estudiantes');
        return;
    }

    // Obtener el número de columnas de la primera fila de estudiante
    const primeraFila = filasEstudiantes[0];
    const totalColumnas = primeraFila.children.length;
    
    console.log(`Total columnas: ${totalColumnas}, Total estudiantes: ${filasEstudiantes.length}`);
    
    // Recorrer cada columna (excepto las primeras 3: #, ID, Nombre y las últimas 2: %, Definitiva)
    for (let colIndex = 3; colIndex < totalColumnas - 2; colIndex++) {
        let sumaNotas = 0;
        let cantidadNotas = 0;
        
        // Recorrer cada fila de estudiante para esta columna
        filasEstudiantes.forEach((fila, filaIndex) => {
            const celda = fila.children[colIndex];
            if (celda) {
                // Buscar el input dentro de la celda
                const input = celda.querySelector('input[data-cod-estudiante][data-valor-nota]');
                if (input) {
                    const valor = input.value.trim();
                    if (valor !== '') {
                        const nota = parseFloat(valor);
                        if (!isNaN(nota) && nota >= 0) {
                            sumaNotas += nota;
                            cantidadNotas++;
                        }
                    }
                }
            }
        });
        
        // Calcular promedio para esta actividad
        const promedio = cantidadNotas > 0 ? (sumaNotas / cantidadNotas) : 0;
        
        // Actualizar la celda de promedio correspondiente
        const celdaPromedio = filaPromedios.children[colIndex];
        if (celdaPromedio) {
            const color = aplicarColorNota(promedio);
            celdaPromedio.innerHTML = `
                <div style="text-align: center;">
                    <span style="font-weight: 700; font-size: 1rem; color: ${color}; display: block;">
                        ${promedio > 0 ? promedio.toFixed(1) : '-'}
                    </span>
                    <small style="font-size: 0.7rem; color: var(--text-secondary); display: block; margin-top: 0.25rem;">
                        (${cantidadNotas} ${cantidadNotas === 1 ? 'nota' : 'notas'})
                    </small>
                </div>
            `;
            
            console.log(`Columna ${colIndex}: Promedio = ${promedio.toFixed(2)} (${cantidadNotas} notas)`);
        }
    }
    
    // Calcular promedio general de definitivas
    let sumaDefinitivas = 0;
    let cantidadDefinitivas = 0;
    
    filasEstudiantes.forEach(fila => {
        const enlaceDefinitiva = fila.querySelector('a[id^="definitiva_"]');
        if (enlaceDefinitiva && enlaceDefinitiva.textContent) {
            const textoDefinitiva = enlaceDefinitiva.textContent.trim();
            const definitiva = parseFloat(textoDefinitiva);
            if (!isNaN(definitiva) && definitiva > 0) {
                sumaDefinitivas += definitiva;
                cantidadDefinitivas++;
            }
        }
    });
    
    const promedioGeneral = cantidadDefinitivas > 0 ? (sumaDefinitivas / cantidadDefinitivas) : 0;
    
    // Actualizar celda de promedio general (última columna)
    const celdaPromedioGeneral = filaPromedios.children[totalColumnas - 1];
    if (celdaPromedioGeneral) {
        const color = aplicarColorNota(promedioGeneral);
        celdaPromedioGeneral.innerHTML = `
            <div style="text-align: center;">
                <span style="font-weight: 700; font-size: 1.1rem; color: ${color}; display: block;">
                    ${promedioGeneral > 0 ? promedioGeneral.toFixed(1) : '-'}
                </span>
                <small style="font-size: 0.7rem; color: var(--text-secondary); display: block; margin-top: 0.25rem;">
                    Promedio General
                </small>
            </div>
        `;
    }
    
    console.log(`✅ Promedios recalculados. Promedio general: ${promedioGeneral.toFixed(1)} (${cantidadDefinitivas} estudiantes)`);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.modernCalificaciones = new ModernCalificaciones();
    
    // Calcular promedios iniciales
    setTimeout(() => {
        recalcularPromedios();
    }, 500);
});

// Exponer funciones globalmente
window.recalcularDefinitiva = recalcularDefinitiva;
window.recalcularPromedios = recalcularPromedios;

// Enhanced error handling
window.addEventListener('error', (e) => {
    console.error('Error:', e.error);
    if (window.modernCalificaciones) {
        window.modernCalificaciones.showNotification('Ha ocurrido un error inesperado', 'error');
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    .typing-indicator {
        position: absolute;
        top: 5px;
        right: 5px;
        animation: pulse 1s infinite;
    }
    
    .scrolled-left::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 20px;
        background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
        pointer-events: none;
        z-index: 5;
    }
    
    .scrolled-right::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 20px;
        background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
        pointer-events: none;
        z-index: 5;
    }
`;
document.head.appendChild(style);
