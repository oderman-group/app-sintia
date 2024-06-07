var select = document.getElementById('moduloSeleccionados');
var checkboxes = document.querySelectorAll('.check');
contarModulosSeleccionados();

/**
 * Esta función es para marcar o desmarcar todas la paginas y tambien la agrega o elimina a la seleción
 */
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('all').addEventListener('change', function(e) {
        var isChecked = this.checked;
        checkboxes.forEach(function(checkElement) {
            var modulo = checkElement.value;  
            if (isChecked) {
                if(checkElement.checked){
                    checkElement.checked = false;
                    eliminarModulo(modulo);
                }
                checkElement.checked = true;
                agregarModulo(modulo);
            } else {
                checkElement.checked = false;
                eliminarModulo(modulo);
            }
        });
    });
});

/**
 * Esta función verifica si la pagina fue selecionada o no para agregar o eliminar de la seleción.
 * @param datos //Datos de la pagina selecionada
 */
function seleccionarModulo(datos) {
    var modulo = datos.value;
    var all = document.getElementById('all'); 
    if (datos.checked) {
        var cont=0;
        checkboxes.forEach(function(checkElement) {
            if(checkElement.checked){
                cont=cont+1;
            }
        });
        if(cont==checkboxes.length){
            all.checked=true;
        }
        agregarModulo(modulo);
    } else {
        if(all.checked){
            all.checked=false;
        }
        eliminarModulo(modulo);
    }
}

/**
 * Esta función agrega una pagina a la seleción cuando es selecionada.
 * @param modulo
 */
function agregarModulo(modulo) {
    var nuevaOpcion = document.createElement('option');
    nuevaOpcion.id = "modulo"+modulo;
    nuevaOpcion.value = modulo;
    nuevaOpcion.textContent = modulo;
    nuevaOpcion.selected = true;
    select.appendChild(nuevaOpcion);
    contarModulosSeleccionados();
}

/**
 * Esta función elimina una pagina de la seleción cuando deja de estar selecionada.
 * @param modulo 
 */
function eliminarModulo(modulo) {
    var opcionAEliminar = document.getElementById('modulo'+modulo);
    if (opcionAEliminar) {
        select.removeChild(opcionAEliminar);
    }
    contarModulosSeleccionados();
}

/**
 * Esta función cuenta las paginas selecionadas.
 */
function contarModulosSeleccionados() {
    var labelCant = document.getElementById('cantSeleccionadas');
    var cantidadSeleccionadas = select.selectedOptions.length;
    labelCant.textContent = cantidadSeleccionadas;
}