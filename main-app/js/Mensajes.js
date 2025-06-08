/**
 * Envía mensajes de correo electrónico a varios receptores.
 * @param {string} year - Año del mensaje.
 * @param {string} institucion - Institución relacionada con el mensaje.
 * @param {string} emisor - Emisor del mensaje.
 * @param {string} nombreEmisor - Nombre del Emisor del mensaje.
 */
function enviarMensajes(year, institucion, emisor, nombreEmisor) {
    //Obtenemos los datos del formulario que envia el mensaje
    const formulario = document.getElementById('formularioEnviarMensajes');
    const botonEnviar = document.getElementById('btnEnviarMensaje'); // Obtener el botón por su ID

    // Deshabilitar todos los elementos de entrada dentro del formulario
    for (let i = 0; i < formulario.elements.length; i++) {
        formulario.elements[i].disabled = true;
    }

    // Deshabilitar explícitamente el botón de envío.
    if (botonEnviar) {
        botonEnviar.disabled = true;
        botonEnviar.textContent = 'Enviando...';
    }

    // Obtener el elemento del select de usuarios
    var selectUsuario = document.getElementById('select_usuario');
    
    // Obtener el asunto y contenido del mensaje
    var asunto = document.getElementById('asunto').value;
    var contenido = document.getElementById('editor1').value;
    
    // Almacenar los receptores seleccionados en el select múltiple
    var receptores = [];

    //Guardamos en el localStorage la cantidad de destinatarios
    localStorage.setItem("cantidadDestinatarios", selectUsuario.options.length);

    // Iterar sobre todas las opciones del select
    for (var i = 0; i < selectUsuario.options.length; i++) {
        var option = selectUsuario.options[i];
        
        // Verificar si la opción está seleccionada
        if (option.selected) {
            // Agregar el valor de la opción a la lista de receptores
            receptores.push(option.value);
        }
    }

    // Enviar un mensaje para cada receptor
    receptores.forEach(function (receptor) {
        // Emitir el evento para enviar el mensaje de correo al servidor
        socket.emit("enviar_mensaje_correo", {
            year: year,
            institucion: institucion,
            emisor: emisor,
            nombreEmisor: nombreEmisor,
            asunto: asunto,
            contenido: contenido,
            receptor: receptor
        });
    });
}