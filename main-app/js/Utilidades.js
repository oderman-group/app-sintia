$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

/**
 * Esta función hace una petición asincrona y recibe una respuesta.
 * Pueden enviarse parametros POST o GET de ser necesario.
 * @param {String} url 
 * @param {String} title
 * @param {String} method = 'POST'
 * @param {String} paramsJSON 
 */
function fetchGeneral(url, title, method='POST', paramsJSON=null) {

    document.getElementById("overlay").style.display = "flex";
    
    const formData = new FormData();

    for (const clave in paramsJSON) {
        if (paramsJSON.hasOwnProperty(clave)) {
            const valor = paramsJSON[clave];
            formData.append(clave, valor);
        }
    }

    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => response.text()) // Convertir la respuesta a texto
    .then(data => {

        document.getElementById("overlay").style.display = "none";
        $("#modalGeneral").modal("show");
        document.getElementById("respuestaTituloGeneral").innerHTML = title;
        document.getElementById("respuestaGeneral").innerHTML = data;

    })
    .catch(error => {
        // Manejar errores
        console.error('Error:', error);

        document.getElementById("overlay").style.display = "none";
    });

}


/**
 * Realiza una solicitud a un servidor para obtener información y la muestra en una sección del documento.
 *
 * Esta función muestra un esqueleto de carga, realiza una solicitud al servidor y reemplaza el esqueleto con la información obtenida.
 *
 * @param {string} url - La URL a la que se realizará la solicitud.
 * @param {string} idResponseTag - El ID del elemento HTML donde se mostrará la información.
 * @param {string} method - El método de solicitud HTTP (por defecto, 'POST').
 * @param {Object} paramsJSON - Un objeto con los datos que se enviarán en la solicitud (opcional).
 */
function listarInformacion(url, idResponseTag, method='POST', paramsJSON=null, idRol=null) {

    const skeletonContent = `
        <div id="skeleton" class="skeleton">
            <div class="skeleton-header"></div>
            <div class="skeleton-content"></div>
            <div class="skeleton-content"></div>
            <div class="skeleton-content"></div>
            <div class="skeleton-content"></div>
            <div class="skeleton-content"></div>
        </div>
    `;

    document.getElementById(idResponseTag).innerHTML = skeletonContent;

    const formData = new FormData();

    for (const clave in paramsJSON) {
        if (paramsJSON.hasOwnProperty(clave)) {
            const valor = paramsJSON[clave];
            formData.append(clave, valor);
        }
    }

    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => response.text()) // Convertir la respuesta a texto
    .then(data => {
        document.getElementById(idResponseTag).innerHTML = data;

        if (idRol && $('#'+idResponseTag+' #panel-body').length) {  
            $('#'+idResponseTag+' #panel-body .select2').select2();

            contarPaginasSeleccionadas(idRol);
            primeraCarga(idRol);
        }

        if (idRol && $('#'+idResponseTag+' #example3').length) {
            $('#'+idResponseTag+' #example3').DataTable();
            
            inicializarCheckAll(idRol);
        }
    })
    .catch(error => {
        // Manejar errores
        console.error('Error:', error);
    });

}

/**
 * Valida el peso de un archivo seleccionado en un campo de entrada de tipo "file".
 *
 * Esta función compara el peso del archivo seleccionado con un peso máximo permitido
 * y muestra un mensaje de error si el archivo excede ese límite.
 *
 * @returns {boolean} Devuelve "false" si el archivo excede el peso máximo permitido, de lo contrario, "true".
 */
function validarPesoArchivo(archivoInput) {
    const maxPeso = 5 * 1024 * 1024; // Peso máximo permitido (en bytes), por ejemplo, 5 MB

    const archivo = archivoInput.files[0]; // Obtiene el archivo seleccionado
    const pesoArchivoMB = ((archivo.size / 1024) / 1024).toFixed(2);

    if (archivo) {
        if (archivo.size > maxPeso) {
            Swal.fire(`Este archivo pesa ${pesoArchivoMB} MB Lo ideal es que pese menos de 5 MB. Intente comprimirlo o busque reducir su peso.`);
            archivoInput.value = ""; // Borra la selección del archivo
            return false;
        }
    }
}

/**
 * Esta función me hace un gardado general
 * @param {array} datos 
 */
function guardarAjaxGeneral(datos){ 
    var idR = datos.id;
    var valor = 0;
    if(document.getElementById(idR).checked){
        valor = 1;
    }
    var operacion = 3;	

$('#respuestaGuardar').empty().hide().html("").show(1);
    datos = "idR="+(idR)+
            "&valor="+(valor)+
            "&operacion="+(operacion);
            $.ajax({
                type: "POST",
                url: "ajax-guardar.php",
                data: datos,
                success: function(data){
                    $('#respuestaGuardar').empty().hide().html(data).show(1);
                }
        });
}

/**
 * Limita la selección en un elemento <select> múltiple a una sola opción y muestra un mensaje de alerta si se seleccionan más de una.
 *
 * @param {HTMLSelectElement} select - El elemento <select> en el que se desea limitar la selección.
 */
function limitarSeleccion(select) {
    var opcionesSeleccionadas = [];
    for (var i = 0; i < select.options.length; i++) {
        if (select.options[i].selected) {
            opcionesSeleccionadas.push(select.options[i]);
        }
    }

    if (opcionesSeleccionadas.length > 1) {
        alert('Solo puedes seleccionar una opción. Si escoje más de una opción, la plataforma tendrá en cuenta solo la últma opción que aparezca en la selección.');

        // Desmarcar todas las opciones excepto la última seleccionada
        for (var i = 0; i < select.options.length; i++) {
            select.options[i].selected = false;
        }

        // Marcar solo la última opción seleccionada
        opcionesSeleccionadas[opcionesSeleccionadas.length - 1].selected = true;
    }
}
/**
 * Esta función me imprime la cuenta bancaria
 */
function verCuentaBancaria() {
    document.getElementById("cuentaBancaria").innerHTML = `
    Cuenta de ahorros Bancolombia número <b>431-565882-54</b>
    `;
}

/**
 * Esta función me cambia la posición de una carga
 * @param {string} idCarga 
 * @param {int} posicionNueva 
 * @param {string} docente 
 */
function cambiarPosicion(idCarga, posicionNueva, docente) {
	fetch('../compartido/cambiar-posicion-cargas.php?idCarga='+idCarga+'&posicionNueva='+posicionNueva+'&docente='+docente, {
		method: 'GET'
	})
	.then(response => response.text()) // Convertir la respuesta a texto
	.then(data => {

        if(data == 1) {
            $.toast({

                heading: 'Proceso completado', 
                text: 'Se ha guardado la nueva posición '+posicionNueva+' para la carga '+idCarga, 
                position: 'bottom-right',
                showHideTransition: 'slide',
                loaderBg:'#26c281', 
                icon: 'success', 
                hideAfter: 5000, 
                stack: 6

            });
        }
	})
	.catch(error => {
		// Manejar errores
		console.error('Error:', error);
	});
}

/**
 * Obtiene datos de la URL especificada y muestra un mensaje de éxito usando $.toast.
 * @param {string} url - La URL para obtener los datos.
 */
function fetchSoloAccion(url) {

    fetch(url, {
        method: 'GET'
    })
    .then(response => response.text()) // Convertir la respuesta a texto
    .then(data => {
        $.toast({

			heading: 'Acción exitosa', 
			text: 'La acción fue realizada con éxito', 
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

/**
 * Cambia el estado de un registro y actualiza la interfaz de usuario.
 *
 * @param {HTMLElement} data - Elemento HTML que contiene atributos de datos necesarios.
 */
function cambiarEstados (data) {
    const estados = {
        1: 'Pendiente',
        2: 'En proceso',
        3: 'Aceptada',
        4: 'Rechazada'
    };
    var idRegistro = data.getAttribute('data-id-registro');
    var idEstado = data.getAttribute('data-id-estado');
    var idRecurso = data.getAttribute('data-id-recurso');
    var idUsuario = data.getAttribute('data-id-usuario');

    var motivo = "Su solicitud de desbloqueo a sido aceptada, ya puede ingresar a la plataforma.";
    if (idEstado == 4) {
        // Mostrar el modal
        $('#motivoModal').modal('show');

        // Al confirmar el motivo
        $('#confirmarMotivo').off('click').on('click', function () {
            motivo = "Su solicitud de desbloqueo a sido rechazada,<br><b>Motivo:</b><br>" + document.getElementById("motivo").value.trim();

            if (motivo === "") {
                alert("Debe ingresar un motivo.");
                return;
            }

            // Ocultar el modal
            $('#motivoModal').modal('hide');

            // Limpiar el contenido del textarea para futuros usos
            document.getElementById("motivo").value = "";
    
            document.getElementById('estado' + idRegistro).innerHTML = estados[idEstado];
            var url = 'solicitudes-estado-actualizar.php?idRegistro=' + idRegistro + '&estado=' + idEstado + '&idUsuario=' + idUsuario + '&motivo=' + motivo;
            fetchSoloAccion(url);
            contadorUsuariosBloqueados();
        });
    } else {
    
        document.getElementById('estado' + idRegistro).innerHTML = estados[idEstado];
        var url = 'solicitudes-estado-actualizar.php?idRegistro=' + idRegistro + '&estado=' + idEstado + '&idUsuario=' + idUsuario + '&motivo=' + motivo;
        fetchSoloAccion(url);
        contadorUsuariosBloqueados();
    }
}

/**
 * Esta función habilita o deshabilita el campo clave
 */
function habilitarClave() {
    var cambiarClave = document.getElementById("cambiarClave");
    var clave = document.getElementById("clave");
    
    if (cambiarClave.checked) {
    clave.disabled = false;
    clave.required = 'required';
    } else {
    clave.disabled = true;
    clave.required = '';
    clave.value = '';
    }
}

/**
 * Displays or hides the subroles section based on the selected role value.
 *
 * This function checks the value of the provided 'enviada' element. If the value is '5',
 * it will display the HTML element with the id 'subRoles'. For any other value, it will
 * hide the 'subRoles' element. Typically used in response to a 'change' event on a select
 * element where the user's role is chosen.
 *
 * @param {HTMLSelectElement} enviada - The select element that triggers the function.
 */
function mostrarSubroles(enviada) {
    var valor = enviada.value;
    if (valor == '5') {
        document.getElementById("subRoles").style.display='block';
    } else {
        document.getElementById("subRoles").style.display='none';
    }
}
