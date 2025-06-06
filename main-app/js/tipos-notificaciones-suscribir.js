let suscribeChecked;
let suscribeInput;
const txtId = document.getElementById('txtId').value;
const txtIdInstitucion = document.getElementById('txtIdInstitucion').value;
const txtIdBd = document.getElementById('txtIdBd').value; 

function actualizarSuscriptor(suscribir){
    suscribeInput = suscribir;
    suscribeChecked = $(suscribeInput).is(':checked');
    let idUsuario = $(suscribeInput).attr('id').split("_");
    
    sweetConfirmacion(
        'Alerta!',
        '¿Desea '+(suscribeChecked?'suscribir':'desuscribir')+' al usuario?',
        'question',
        'tipos-notificaciones-suscribir-actualizar.php',
        true,
        null,
        'POST',
        { 
            suscribirtodos : false,
            idNotificacion: txtId,
            idUsuario: idUsuario[0],
            suscribir: suscribeChecked,
            year: txtIdBd,
            idInstitucion: txtIdInstitucion,
        },
        'respuestaSuscripcion'
    );

}

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

function respuestaSuscripcion(result,data, respuetaSweet) {

    if (!respuetaSweet) {
        if(!data["suscribirTodos"]){
            $(suscribeInput).prop('checked', !suscribeChecked);	
        }

                
    }else{
        let  resultado = result["data"];
        $.toast({
                heading: 'Acción realizada',
                text: resultado["msg"],
                position: 'bottom-right',
                showHideTransition: 'slide',
                loaderBg: '#26c281',
                icon: resultado["ok"] ? 'success' : 'error',
                hideAfter: 5000,
                stack: 6
        });

        if(data["suscribirTodos"]){
            const table = $('#example1').DataTable();
            const selectedCheckboxes = table.rows().nodes().to$().find('input[name="suscrito"]');
            selectedCheckboxes.each(function () {
                $(this).prop('checked', suscribeChecked);
            });
        }
    }
};