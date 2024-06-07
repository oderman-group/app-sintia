/**
 * Esta función recarga la pagina para mostrar solo las paginas selecionadas
 * @param idSubrol
 */
function mostrarActivas(check,idSubrol) {
    var nuevaURL = "sub-roles-editar.php?id="+idSubrol+"&activas="+(check?1:0);            
    window.location.href = nuevaURL;
    onclick="redireccionar()"
}

function encodeBase64(str) {
    return btoa(str);
}

function primeraCarga(idSubrol) {
    var idSubrol64 = encodeBase64(idSubrol);
    listarInformacion('../directivo/async-listar-paginas.php?idMod=7&idRol='+idSubrol64+'&activas=0', 'nav-mod7'+idSubrol, 'POST', null, idSubrol);
}

/**
 * Esta función es para marcar o desmarcar todas la paginas y tambien la agrega o elimina a la seleción
 */
function inicializarCheckAll(idRolActual) {
    document.getElementById('all'+idRolActual).addEventListener('change', function(e) {
        var isChecked = this.checked;
        var checkboxes = document.querySelectorAll('.check'+idRolActual);
        checkboxes.forEach(function(checkElement) {
            var idRol = checkElement.getAttribute("data-id-rol");
            var page = checkElement.value;  
            if (isChecked) {
                if(checkElement.checked){
                    checkElement.checked = false;
                    eliminarPagina(page, idRol);
                }
                checkElement.checked = true;
                agregarPagina(page, idRol);
            } else {
                checkElement.checked = false;
                eliminarPagina(page, idRol);
            }
        });
    });
}

/**
 * Esta función verifica si la pagina fue selecionada o no para agregar o eliminar de la seleción.
 * @param datos //Datos de la pagina selecionada
 */
function seleccionarPagina(datos) {
    var idRol = datos.getAttribute("data-id-rol");
    var page = datos.value;
    var all = document.getElementById('all'+idRol); 
    var checkboxes = document.querySelectorAll('.check'+idRol);
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
        agregarPagina(page, idRol);
    } else {
        if(all.checked){
            all.checked=false;
        }
        eliminarPagina(page, idRol);
    }
}

/**
 * Esta función agrega una pagina a la seleción cuando es selecionada.
 * @param page
 */
function agregarPagina(page, idRol) {
    var select = document.getElementById('paginasSeleccionadas'+idRol);
    var nuevaOpcion = document.createElement('option');
    nuevaOpcion.value = page;
    nuevaOpcion.textContent = page;
    nuevaOpcion.selected = true;
    select.appendChild(nuevaOpcion);
    contarPaginasSeleccionadas(idRol);
    actualizarSubRol(idRol);
}

/**
 * Esta función elimina una pagina de la seleción cuando deja de estar selecionada.
 * @param page 
 */
function eliminarPagina(page, idRol) {
    var select = document.getElementById('paginasSeleccionadas'+idRol);
    var opcionAEliminar = select.querySelector('option[value=' + page + ']');
    if (opcionAEliminar) {
        select.removeChild(opcionAEliminar);
    }
    contarPaginasSeleccionadas(idRol);
    actualizarSubRol(idRol);
}

/**
 * Esta función cuenta las paginas selecionadas.
 */
function contarPaginasSeleccionadas(idRol) {
    var select = document.getElementById('paginasSeleccionadas'+idRol);
    var labelCant = document.getElementById('cantSeleccionadas'+idRol);
    var cantidadSeleccionadas = select.selectedOptions.length;
    labelCant.textContent = cantidadSeleccionadas;
}

/**
 * Esta función valida si la pagina selecionada tiene paginas de dependencias para mostrarle un alert al usuario.
 * @param datosPagina //Datos de la pagina selecionada
 */
function validarPaginasDependencia(datosPagina) {
    var id = datosPagina.value;
    var dependencias = datosPagina.id;
    if(dependencias.length>0 && datosPagina.checked){
        Swal.fire({
            title: 'Desea continuar?',
            text: "Esta pagina tiene otras paginas de dependencia que se añadirán automáticamente a este rol.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si, deseo continuar!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                seleccionarPagina(datosPagina);
            }else{
                datosPagina.checked=false;
            }
        });
    }else{
        seleccionarPagina(datosPagina);
    }
}

/**
 * Esta función me actualiza un sub rol
 */
function actualizarSubRol(idRol) {
    var nombre  = document.getElementById('nombreSubrol'+idRol).value;
    var id  = document.getElementById('idSubRol'+idRol).value;
    
    var selectElementDirectivos = document.getElementById('directivos'+idRol);
    var selectedOptionsDirectivos = [];
    for (var i = 0; i < selectElementDirectivos.options.length; i++) {
        if (selectElementDirectivos.options[i].selected) {
            selectedOptionsDirectivos.push(selectElementDirectivos.options[i].value);
        }
    }

    var directivos = encodeURIComponent(JSON.stringify(selectedOptionsDirectivos));
    
    var selectElementPaginas = document.getElementById('paginasSeleccionadas'+idRol);
    var selectedOptionsPaginas = [];
    for (var i = 0; i < selectElementPaginas.options.length; i++) {
        if (selectElementPaginas.options[i].selected) {
            selectedOptionsPaginas.push(selectElementPaginas.options[i].value);
        }
    }

    var paginas = encodeURIComponent(JSON.stringify(selectedOptionsPaginas));

	fetch('../directivo/sub-roles-actualizar.php?nombre='+nombre+'&directivos='+directivos+'&paginas='+paginas+'&id='+id, {
		method: 'GET'
	})
	.then(response => response.text()) // Convertir la respuesta a texto
	.then(data => {
        $.toast({

            heading: 'Proceso completado', 
            text: 'El Sub Rol se actualizo correctamente...', 
            position: 'bottom-right',
            showHideTransition: 'slide',
            loaderBg:'#26c281', 
            icon: 'success', 
            hideAfter: 5000, 
            stack: 6

        });
	})
	.catch(error => {
		// Manejar errores
		console.error('Error:', error);
	});
}