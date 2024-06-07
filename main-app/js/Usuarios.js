/**
 * Esta función hace una petición asincrona y recibe una respuesta.
 * @param {array} datos 
 */
function validarUsuario(datos) {
    var usuario = datos.value;
    var idUsuario = datos.getAttribute("data-id-usuario");
    
    if(usuario!=""){

        fetch('ajax-comprobar-usuario.php?usuario=' + usuario + '&idUsuario=' + idUsuario, {
            method: 'GET'
        })
        .then(response => response.json()) // Convertir la respuesta a objeto JSON
        .then(data => {
                if (data.success == 1) {
                    $("#respuestaUsuario").html(data.message);
                    $("input").attr('disabled', true); 
                    $("input#usuario").attr('disabled',false); 
                    $("#btnEnviar").attr('disabled', true); 
                } else {
                    $("#respuestaUsuario").html(data.message);
                    $("input").attr('disabled', false); 
                    $("#btnEnviar").attr('disabled', false); 
                    validarCampo(datos)
                }
        })
        .catch(error => {
            // Manejar errores
            console.error('Error:', error);
        });
    } else {
        $("#respuestaUsuario").html("");
        $("input").attr('disabled', false); 
        $("#btnEnviar").attr('disabled', false); 
    }
}

/**
 * Esta función me valida la cantidad de usurios permitido segun el tipo de usuario y el plan de la compañia.
 * @param {array} datos 
 */
function validarCantidadUsuarios(datos) {
    var tipoUsuario = datos.value;
    var subRoles = document.getElementById("subRoles");
    
    if(tipoUsuario!=""){

        fetch('ajax-comprobar-cantidad-usuario.php?tipoUsuario=' + tipoUsuario, {
            method: 'GET'
        })
        .then(response => response.json()) // Convertir la respuesta a objeto JSON
        .then(data => {
                if (data.success == 1) {
                    $("#respuestaUsuario").html(data.message);
                    $("input").attr('disabled', true); 
                    $("input#tipoUsuario").attr('disabled',false); 
                    $("#btnEnviar").attr('disabled', true);
                } else {
                    $("#respuestaUsuario").html(data.message);
                    $("input").attr('disabled', false); 
                    $("#btnEnviar").attr('disabled', false); 
                    if (subRoles) {
                        mostrarSubroles(datos);
                    }
                }
        })
        .catch(error => {
            // Manejar errores
            console.error('Error:', error);
        });
    } else {
        $("#respuestaUsuario").html("");
        $("input").attr('disabled', false); 
        $("#btnEnviar").attr('disabled', false); 
    }
}


function validarCampo(input) {
    var valor = input.value;
    // Expresión regular para permitir solo letras, números y algunos caracteres especiales
    var patron = /^[a-zA-Z0-9\-_]+$/;

    // Verificar si el valor del campo coincide con el patrón
    if (patron.test(valor)) {
        // El valor es válido
        $("#respuestaUsuario2").html('');
        $("input").attr('disabled', false); 
        $("#btnEnviar").attr('disabled', false); 
    } else {
        // El valor no es válido, establecer un mensaje de error personalizado
        $("#respuestaUsuario2").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-exclamation-sign"></i>El campo del usuario no debe contener espacios en blanco ni caracteres especiales.<br>Puedes usar letras, números o combinarlos.</div>');
        $("input").attr('disabled', true); 
        $("input#usuario").attr('disabled',false); 
        $("#btnEnviar").attr('disabled', true); 
    }
}

/**
 * Esta función hace una petición asincrona y recibe una respuesta.
 * @param {array} datos 
 */
function validarDocumento(datos) {
    var documento = datos.value;
    var idUsuario = datos.getAttribute("data-id-usuario");

    if(documento!=""){

        fetch('ajax-comprobar-documento.php?documento=' + documento + '&idUsuario=' + idUsuario, {
            method: 'GET'
        })
        .then(response => response.json()) // Convertir la respuesta a objeto JSON
        .then(data => {
                if (data.success == 1) {
                    $("#respuestaUsuario").html(data.message);
                    $("input").attr('disabled', true); 
                    $("input#documento").attr('disabled',false); 
                    $("#btnEnviar").attr('disabled', true); 
                } else {
                    $("#respuestaUsuario").html(data.message);
                    $("input").attr('disabled', false); 
                    $("#btnEnviar").attr('disabled', false);
                }
        })
        .catch(error => {
            // Manejar errores
            console.error('Error:', error);
        });
    } else {
        $("#respuestaUsuario").html("");
        $("input").attr('disabled', false); 
        $("#btnEnviar").attr('disabled', false); 
    }
}