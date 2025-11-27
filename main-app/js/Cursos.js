var select = document.getElementById('estudiantesSeleccionados');
var checkboxes = document.querySelectorAll('.check');

// Inicializar contador y estado del botón al cargar la página
document.addEventListener("DOMContentLoaded", function() {
    contarEstudiantesSeleccionados();
});

/**
 * Esta función es para marcar o desmarcar todos los estudiantes y tambien la agrega o elimina a la seleción
 */
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('all').addEventListener('change', function(e) {
        var isChecked = this.checked;
        checkboxes.forEach(function(checkElement) {
            var idEstudiante = checkElement.value; 
            var grupo = checkElement.getAttribute('data-grupo'); 
            if (isChecked) {
                if(checkElement.checked){
                    checkElement.checked = false;
                    eliminarEstudiantes(idEstudiante);
                    eliminarGrupoEstudiantes(idEstudiante);
                }
                checkElement.checked = true;
                agregarEstudiantes(idEstudiante);
                crearInputGrupoEstudiante('noThis', idEstudiante, grupo);
            } else {
                checkElement.checked = false;
                eliminarEstudiantes(idEstudiante);
                eliminarGrupoEstudiantes(idEstudiante);
            }
        });
    });
});

/**
 * Esta función verifica si el estudiante fue selecionado o no para agregar o eliminar de la seleción.
 * @param datos //Datos del estudiante selecionado
 */
function seleccionarEstudiantes(datos) {
    var idEstudiante = datos.value;
    var grupo = datos.getAttribute('data-grupo'); 
    var all = document.getElementById('all'); 

    var elementCambiarEstado = document.getElementById('cambiarEstado'+idEstudiante);

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

        agregarEstudiantes(idEstudiante);
        crearInputGrupoEstudiante('noThis', idEstudiante, grupo);

        elementCambiarEstado.disabled = false;
    } else {
        if(all.checked){
            all.checked=false;
        }
        eliminarEstudiantes(idEstudiante);
        eliminarGrupoEstudiantes(idEstudiante);

        elementCambiarEstado.checked = false;
        elementCambiarEstado.disabled = true;
        var existe = existeElemento("hiddenEstado"+idEstudiante);
        if (existe) {
            var miDiv = document.getElementById("divEstudiante");
            var inputEliminar = document.getElementById("hiddenEstado"+idEstudiante);
        
            if (inputEliminar) {
                miDiv.removeChild(inputEliminar);
            }
        }
    }
}

/**
 * Esta función agrega un estudiante a la seleción cuando es selecionado.
 * @param idEstudiante
 */
function agregarEstudiantes(idEstudiante) {
    var nuevaOpcion = document.createElement('option');
    nuevaOpcion.value = idEstudiante;
    nuevaOpcion.textContent = idEstudiante;
    select.appendChild(nuevaOpcion);
    nuevaOpcion.selected = true;
    contarEstudiantesSeleccionados();
}

/**
 * Esta función elimina un estudiante de la seleción cuando deja de estar selecionado.
 * @param idEstudiante 
 */
function eliminarEstudiantes(idEstudiante) {
    var opcionAEliminar = select.querySelector('option[value="' + idEstudiante + '"]');
    if (opcionAEliminar) {
        select.removeChild(opcionAEliminar);
    }
    contarEstudiantesSeleccionados();
}

/**
 * Esta función cuenta los estudiantes selecionados.
 */
function contarEstudiantesSeleccionados() {
    var labelCant = document.getElementById('cantSeleccionadas');
    var cantidadSeleccionadas = select.selectedOptions.length;
    if (labelCant) {
        labelCant.textContent = cantidadSeleccionadas;
    }
    
    // Habilitar/deshabilitar botón de promoción según la cantidad de estudiantes seleccionados
    var btnRealizarPromocion = document.getElementById('btnRealizarPromocion');
    if (btnRealizarPromocion) {
        if (cantidadSeleccionadas > 0) {
            btnRealizarPromocion.disabled = false;
            btnRealizarPromocion.classList.remove('disabled');
        } else {
            btnRealizarPromocion.disabled = true;
            btnRealizarPromocion.classList.add('disabled');
        }
    }
}

// Función para verificar si un elemento con el ID especificado existe
function existeElemento(id) {
    return document.getElementById(id) !== null;
}

/**
 * Esta función cre un input hidden con la relacion de la carga.
 * @param datos
 */
function crearInputCarga(datos) {
    var id = "hidden"+datos.id;
    var idCarga = datos.id;
    var cargaActual = datos.getAttribute('data-carga');
    var cargaPasar = datos.value;
        
    fetch('../directivo/ajax-validar-cargas.php?cargaActual='+(cargaActual)+'&cargaPasar='+(cargaPasar), {
        method: 'GET'
    })
    .then(response => response.json()) // Convertir la respuesta a texto
    .then(data => {

        var existe = existeElemento(id);

        if(data.coinciden === false){

            Swal.fire({
                title: 'Cargas no coinciden',
                text: "El grupo o la asignatura no coinciden con el grupo y la asignatura de la carga con la que deseas relacionarla.",
                icon: 'warning',
                showCancelButton: false,
                confirmButtonText: 'Ok',
                backdrop: `
                    rgba(0,0,123,0.4)
                    no-repeat
                `,
            })

        }

        if (!existe) {
            var input = document.createElement("input");
            input.type = "hidden";
            input.id = id; 
            input.name = idCarga; 
            input.value = cargaPasar; 
    
            var miDiv = document.getElementById("divCargas");
            miDiv.appendChild(input);
        } else {
            var input = document.getElementById(id);
            input.value = cargaPasar; 
        }
    })
}

/**
 * Esta función cre un input hidden con el grupo al que pertenece el estudiante.
 * @param datos
 */
function crearInputGrupoEstudiante(datos, idEstudiante, grupo) {
    var idHidden = "hiddengrupo"+idEstudiante;
    var id = "grupo"+idEstudiante;
    var valorGrupo = grupo;

    if (grupo === 'noGrupo') {
        var valorGrupo = datos.value;
    }

    var existe = existeElemento(idHidden);
    if (!existe) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.id = idHidden; 
        input.name = id; 
        input.value = valorGrupo; 

        var miDiv = document.getElementById("divEstudiante");
        miDiv.appendChild(input);
    } else {
        var input = document.getElementById(idHidden);
        input.value = valorGrupo; 
    }
}

/**
 * Esta función elimina un input hidden con el grupo al que pertenece el estudiante.
 * @param idEstudiante 
 */
function eliminarGrupoEstudiantes(idEstudiante) {
    var miDiv = document.getElementById("divEstudiante");
    var inputEliminar = document.getElementById("hiddengrupo"+idEstudiante);

    if (inputEliminar) {
        miDiv.removeChild(inputEliminar);
    }
}

/**
 * Esta función habilita los campos para selecionar los grupos de forma generar cuando
 * se van a relacionar las cargas.
 */
function relacionCargasGrupos(data) {
    if (data.checked == true) {
        document.getElementById("elementGroup").style.display = "flex";
        document.getElementById("grupoDesde").required = true;
        document.getElementById("grupoPara").required = true;
    } else {
        document.getElementById("grupoDesde").required = false;
        document.getElementById("grupoPara").required = false;
        document.getElementById("elementGroup").style.display = "none";
    }
}

/**
 * Esta función crea un input hidden con la opcion de si desea cambiar el estado del estudiante.
 * @param id
 */
function crearInputEstadoEstudiante(datos) {
    var miDiv = document.getElementById("divEstudiante");

    var idEstudiante = datos.getAttribute("data-id-estudiante");
    var idHidden = "hiddenEstado"+idEstudiante;
    var id = "estado"+idEstudiante;

    if (datos.checked == true) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.id = idHidden; 
        input.name = id; 
        input.value = 1; 

        miDiv.appendChild(input);
    } else {
        var existe = existeElemento(idHidden);
        if (existe) {
            var inputEliminar = document.getElementById(idHidden);
        
            if (inputEliminar) {
                miDiv.removeChild(inputEliminar);
            }
        }
    }
}