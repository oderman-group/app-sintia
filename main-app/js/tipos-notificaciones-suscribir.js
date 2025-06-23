let suscribeChecked;
let suscribeInput;

const txtId = document.getElementById('txtId').value;
const txtIdInstitucion = document.getElementById('txtIdInstitucion').value;
const txtIdBd = document.getElementById('txtIdBd').value; 

/**
 * Pregunta si se quiere cambiar el estado de suscripción de un usuario a una notificación.
 * 
 * @param suscribir - Input (CHECKBOX) de entrada que representa la suscripción del usuario.
 */
function actualizarSuscriptor(inputCheckbox) {
    suscribeInput = inputCheckbox;
    suscribeChecked = $(suscribeInput).is(':checked');
    let idUsuario = $(suscribeInput).attr('id').split("_")[0];
    
    sweetConfirmacion(
        'Alerta!',
        '¿Desea '+(suscribeChecked?'suscribir':'desuscribir')+' al usuario?',
        'question',
        'tipos-notificaciones-suscribir-actualizar.php',
        true,
        null,
        'POST',
        { 
            suscribirTodos : false,
            idNotificacion: txtId,
            idUsuario: idUsuario,
            suscribir: suscribeChecked,
            year: txtIdBd,
            idInstitucion: txtIdInstitucion,
        },
        'respuestaSuscripcion'
    );

}

/**
 * Pregunta si se quiere suscribir o desuscribir a todos los usuarios a una notificación.
 * 
 * @param checked  - El estado del checkbox que indica si se deben suscribir o desuscribir todos los usuarios.
 * 
 */
function actualizarSuscriptorTodos(checked){
    suscribeChecked = checked;    
    
    sweetConfirmacion(
        'Alerta!',
        '¿Desea '+(suscribeChecked?'suscribir':'desuscribir')+' todos los usuarios?',
        'question',
        'tipos-notificaciones-suscribir-actualizar.php',
        true,
        null,
        'POST',
        { 
            suscribirTodos : true,
            idNotificacion: txtId,
            idUsuario: 0,
            suscribir: suscribeChecked,
            year: txtIdBd,
            idInstitucion: txtIdInstitucion,
        },
        'respuestaSuscripcion'
    );

}

/**
 * captura la respuesta de la solicitud AJAX.
 * 
 * @param respuestaAjax - El resultado de la solicitud AJAX.
 * @param datosEnviados - Los datos enviados en la solicitud.
 * @param respuetaSweet - Indica la respuesta del usuario en SweetAlert (SI o NO).
 *
 */
function respuestaSuscripcion(respuestaAjax, datosEnviados, respuetaSweet) {

    if (!respuetaSweet) {
        if(!datosEnviados["suscribirTodos"]){
            $(suscribeInput).prop('checked', !suscribeChecked);	
        }
                
    }else{
        $.toast({
                heading: 'Acción realizada',
                text: respuestaAjax["data"]["msg"],
                position: 'bottom-right',
                showHideTransition: 'slide',
                loaderBg: '#26c281',
                icon: respuestaAjax["data"]["ok"] ? 'success' : 'error',
                hideAfter: 5000,
                stack: 6
        });

        if(datosEnviados["suscribirTodos"]){
            const table = $('#example1').DataTable();
            const selectedCheckboxes = table.rows().nodes().to$().find('input[name="suscrito"]');
            selectedCheckboxes.each(function () {
                $(this).prop('checked', suscribeChecked);
            });
        }
    }
};