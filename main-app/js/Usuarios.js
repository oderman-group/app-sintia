/**
 * Función para habilitar/deshabilitar campos del formulario de edición
 * excluirCampo: campo que NO se debe deshabilitar (ej: 'documento' o 'usuario')
 */
function habilitarCamposFormularioEditar(habilitar, excluirCampo = null) {
    console.log('habilitarCamposFormularioEditar llamado con:', habilitar, 'excluir:', excluirCampo);
    
    // Campos a habilitar/deshabilitar dentro del formulario
    const campos = [
        { selector: '#tipoUsuario', nombre: 'tipoUsuario' },
        { selector: '#usuario', nombre: 'usuario' },
        { selector: '#clave', nombre: 'clave' },
        { selector: 'input[name="nombre"]', nombre: 'nombre' },
        { selector: 'input[name="nombre2"]', nombre: 'nombre2' },
        { selector: 'input[name="apellido1"]', nombre: 'apellido1' },
        { selector: 'input[name="apellido2"]', nombre: 'apellido2' },
        { selector: 'input[name="email"]', nombre: 'email' },
        { selector: 'input[name="celular"]', nombre: 'celular' },
        { selector: 'input[name="telefono"]', nombre: 'telefono' },
        { selector: 'input[name="direccion"]', nombre: 'direccion' },
        { selector: 'input[name="ocupacion"]', nombre: 'ocupacion' },
        { selector: 'select[name="tipoD"]', nombre: 'tipoD' },
        { selector: '#documento', nombre: 'documento' },
        { selector: 'select[name="genero"]', nombre: 'genero' },
        { selector: 'select[name="lExpedicion"]', nombre: 'lExpedicion' },
        { selector: 'input[name="intentosFallidos"]', nombre: 'intentosFallidos' }
    ];
    
    // Buscar dentro del formulario para asegurar que encontramos los elementos
    const $formulario = $('form[name="formularioGuardar"]');
    
    campos.forEach(function(campo) {
        // Si este campo está en la lista de exclusión, no lo deshabilitamos
        if (excluirCampo && campo.nombre === excluirCampo) {
            return;
        }
        
        const $campoElement = $formulario.find(campo.selector);
        if ($campoElement.length > 0) {
            // No deshabilitar campos que ya están readonly o disabled por otras razones
            const esReadonly = $campoElement.attr('readonly') !== undefined;
            const yaDisabled = $campoElement.attr('disabled') !== undefined && !habilitar;
            
            if (!esReadonly) {
                $campoElement.prop('disabled', !habilitar);
                console.log('Campo ' + campo.nombre + ' ' + (habilitar ? 'habilitado' : 'deshabilitado'));
            }
        } else {
            console.warn('Campo no encontrado: ' + campo.selector);
        }
    });
    
    // Habilitar/deshabilitar botón de guardar
    // Primero intentar buscar por ID (más directo y confiable)
    let $btnGuardar = $('#btnGuardarFormulario');
    
    // Si no se encuentra por ID, buscar de manera exhaustiva
    if ($btnGuardar.length === 0) {
        // 1. Buscar dentro del formulario
        $btnGuardar = $formulario.find('button[type="submit"]');
        
        // 2. Si no está dentro, buscar en el panel-body (donde está el formulario)
        if ($btnGuardar.length === 0) {
            const $panelBody = $formulario.closest('.panel-body');
            if ($panelBody.length > 0) {
                $btnGuardar = $panelBody.find('button[type="submit"]');
            }
        }
        
        // 3. Si aún no se encuentra, buscar en el panel completo
        if ($btnGuardar.length === 0) {
            const $panel = $formulario.closest('.panel');
            if ($panel.length > 0) {
                $btnGuardar = $panel.find('button[type="submit"]');
            }
        }
        
        // 4. Buscar botones submit con clase btn-info (que es la clase que usa botonesGuardar)
        if ($btnGuardar.length === 0) {
            const $panel = $formulario.closest('.panel');
            if ($panel.length > 0) {
                $btnGuardar = $panel.find('button[type="submit"].btn-info');
            }
        }
        
        // 5. Último recurso: buscar cualquier botón submit asociado al formulario por su nombre
        if ($btnGuardar.length === 0) {
            const $formContext = $('form[name="formularioGuardar"]');
            if ($formContext.length > 0) {
                const $parentPanel = $formContext.closest('.panel');
                if ($parentPanel.length > 0) {
                    $btnGuardar = $parentPanel.find('button[type="submit"]');
                }
            }
        }
    }
    
    if ($btnGuardar && $btnGuardar.length > 0) {
        $btnGuardar.prop('disabled', !habilitar);
        console.log('Botón guardar encontrado y ' + (habilitar ? 'habilitado' : 'deshabilitado'), $btnGuardar);
    } else {
        console.warn('Botón de guardar no encontrado. Intentando búsqueda más amplia...');
        // Último intento: buscar cualquier botón submit en la página que esté relacionado con el formulario
        const $allSubmitButtons = $('button[type="submit"]');
        if ($allSubmitButtons.length > 0) {
            const $form = $('form[name="formularioGuardar"]');
            if ($form.length > 0) {
                $allSubmitButtons.each(function() {
                    const $btn = $(this);
                    const $formPanel = $form.closest('.panel');
                    const $btnPanel = $btn.closest('.panel');
                    if ($formPanel.length > 0 && $btnPanel.length > 0 && $formPanel[0] === $btnPanel[0]) {
                        $btn.prop('disabled', !habilitar);
                        console.log('Botón guardar encontrado (búsqueda amplia) y ' + (habilitar ? 'habilitado' : 'deshabilitado'), $btn);
                        return false; // break
                    }
                });
            }
        }
    }
    
    // Agregar clase visual para indicar estado deshabilitado
    if (habilitar) {
        $formulario.find('.form-control:disabled, select:disabled').removeClass('bg-light');
    } else {
        $formulario.find('.form-control:disabled, select:disabled').addClass('bg-light');
    }
}

/**
 * Esta función hace una petición asincrona y recibe una respuesta.
 * @param {array} datos 
 */
function validarUsuario(datos) {
    var usuario = datos.value.trim();
    var idUsuario = datos.getAttribute("data-id-usuario");
    
    // Limpiar mensajes anteriores
    $("#respuestaUsuario").html("");
    $("#alerta_usuario_existente_editar").slideUp(300);
    
    if(usuario == ""){
        // Habilitar campos si el usuario está vacío
        usuarioValidadoEditar = true;
        habilitarCamposFormularioEditar(true);
        return;
    }

    // Mostrar indicador de carga
    $("#alerta_usuario_existente_editar").hide();

    fetch('ajax-comprobar-usuario.php?usuario=' + usuario + '&idUsuario=' + idUsuario, {
        method: 'GET'
    })
    .then(response => response.json()) // Convertir la respuesta a objeto JSON
    .then(data => {
        console.log('Respuesta validación usuario:', data);
        
        if (data.success == 1) {
            // Usuario existe
            $("#respuestaUsuario").html(data.message);
            $("#alerta_usuario_existente_editar").slideDown(300);
            usuarioValidadoEditar = false;
            // Deshabilitar todos los campos excepto el usuario
            habilitarCamposFormularioEditar(false, 'usuario');
        } else {
            // Usuario disponible
            $("#respuestaUsuario").html(data.message || '');
            $("#alerta_usuario_existente_editar").slideUp(300);
            usuarioValidadoEditar = true;
            // Habilitar todos los campos (solo si el documento también está válido)
            if (typeof documentoValidadoEditar === 'undefined' || documentoValidadoEditar) {
                habilitarCamposFormularioEditar(true);
            }
            validarCampo(datos);
        }
    })
    .catch(error => {
        // Manejar errores
        console.error('Error:', error);
        // Habilitar campos en caso de error
        habilitarCamposFormularioEditar(true);
    });
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
                    // Deshabilitar todos los campos excepto el tipo de usuario
                    habilitarCamposFormularioEditar(false, 'tipoUsuario');
                } else {
                    $("#respuestaUsuario").html(data.message);
                    // Habilitar campos solo si usuario y documento están válidos
                    if ((typeof usuarioValidadoEditar === 'undefined' || usuarioValidadoEditar) &&
                        (typeof documentoValidadoEditar === 'undefined' || documentoValidadoEditar)) {
                        habilitarCamposFormularioEditar(true);
                    }
                    if (subRoles) {
                        mostrarSubroles(datos);
                    }
                }
        })
        .catch(error => {
            // Manejar errores
            console.error('Error:', error);
            // Habilitar campos en caso de error
            habilitarCamposFormularioEditar(true);
        });
    } else {
        $("#respuestaUsuario").html("");
        // Habilitar campos solo si usuario y documento están válidos
        if ((typeof usuarioValidadoEditar === 'undefined' || usuarioValidadoEditar) &&
            (typeof documentoValidadoEditar === 'undefined' || documentoValidadoEditar)) {
            habilitarCamposFormularioEditar(true);
        }
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

// Variables globales para rastrear el estado de validación
var documentoValidadoEditar = true;
var usuarioValidadoEditar = true;

/**
 * Esta función hace una petición asincrona y recibe una respuesta.
 * @param {array} datos 
 */
function validarDocumento(datos) {
    var documento = datos.value.trim();
    var idUsuario = datos.getAttribute("data-id-usuario");

    // Limpiar mensajes anteriores
    $("#respuestaUsuario").html("");
    $("#alerta_documento_existente_editar").slideUp(300);

    if(documento == ""){
        // Habilitar campos si el documento está vacío
        documentoValidadoEditar = true;
        habilitarCamposFormularioEditar(true);
        return;
    }

    // Mostrar indicador de carga
    $("#alerta_documento_existente_editar").hide();

    fetch('ajax-comprobar-documento.php?documento=' + documento + '&idUsuario=' + idUsuario, {
        method: 'GET'
    })
    .then(response => response.json()) // Convertir la respuesta a objeto JSON
    .then(data => {
        console.log('Respuesta validación documento:', data);
        
        if (data.success == 1) {
            // Documento existe
            $("#respuestaUsuario").html(data.message);
            $("#alerta_documento_existente_editar").slideDown(300);
            documentoValidadoEditar = false;
            // Deshabilitar todos los campos excepto el documento
            habilitarCamposFormularioEditar(false, 'documento');
        } else {
            // Documento disponible
            $("#respuestaUsuario").html(data.message || '');
            $("#alerta_documento_existente_editar").slideUp(300);
            documentoValidadoEditar = true;
            // Habilitar todos los campos (solo si el usuario también está válido)
            if (typeof usuarioValidadoEditar === 'undefined' || usuarioValidadoEditar) {
                habilitarCamposFormularioEditar(true);
            }
        }
    })
    .catch(error => {
        // Manejar errores
        console.error('Error:', error);
        documentoValidadoEditar = true;
        // Habilitar campos en caso de error
        habilitarCamposFormularioEditar(true);
    });
}

function ajaxBloqueoDesbloqueo(datos) {
    var idR = datos.id;
    var operacion = 1;
    var checkUsurio = document.getElementById(idR);

    $('#respuestaGuardar').empty().hide().html("").show(1);

    // Determinar el nuevo estado del checkbox
    if (document.getElementById(idR).checked) {
        // Mostrar el modal
        $('#motivoModal').modal('show');

        // Al confirmar el motivo
        $('#confirmarMotivo').off('click').on('click', function () {
            valor = 1;
            document.getElementById("reg" + idR).style.backgroundColor = "#ff572238";
            var motivo = document.getElementById("motivo").value.trim();

            if (motivo === "") {
                alert("Debe ingresar un motivo.");
                return;
            }

            // Ocultar el modal
            $('#motivoModal').modal('hide');

            // Limpiar el contenido del textarea para futuros usos
            document.getElementById("motivo").value = "";

            datos = "idR=" + (idR) +
                "&valor=" + (valor) +
                "&operacion=" + (operacion) +
                "&motivo=" + encodeURIComponent(motivo);

            enviarAjaxBloqueoDesbloqueo(datos);
        });

        // Al cancelar el motivo
        $('#cancelarMotivo').off('click').on('click', function () {
            checkUsurio.checked = false;
        });

        // Al cancelar el motivo
        $('#boton-cerrar-modal-motivo').off('click').on('click', function () {
            checkUsurio.checked = false;
        });
    } else {
        valor = 0;
        document.getElementById("reg" + idR).style.backgroundColor = "white";

        datos = "idR=" + (idR) +
            "&valor=" + (valor) +
            "&operacion=" + (operacion)

        enviarAjaxBloqueoDesbloqueo(datos);
    }
}

function enviarAjaxBloqueoDesbloqueo(datos) {

    $.ajax({
        type: "POST",
        url: "ajax-guardar.php",
        data: datos,
        success: function (data) {
            $('#respuestaGuardar').empty().hide().html(data).show(1);
        }
    });
}