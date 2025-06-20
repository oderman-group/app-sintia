
let txtOpcion = "";
let htmlButon = "";
let hizoCambio = false; 
let txtIdIndicadorNuevo = null;
let txtIdEstudiante = null;
let txtIdIndicador = null;

document.addEventListener('DOMContentLoaded', () => {   

    const txtDescripcion = document.getElementById('txtDescripcion');
    const btnSubmitSave = document.getElementById('btnSubmitSave');


    btnSubmitSave.addEventListener('click', btnSubmitSaveClick);
    function btnSubmitSaveClick(e) {
        e.preventDefault();

        if( txtOpcion === "agregar"){

            if (!txtDescripcion.value.trim()) {
                mtdMostrarMensaje("Digite una descripcion", "error");
                txtDescripcion.focus();
                return;
            }

            htmlButon = btnSubmitSave.innerHTML;

            mtdActivarLoadBoton(btnSubmitSave, "Guardando...");           

            var e_datos = {
                aii_id: txtIdIndicadorNuevo,
                aii_id_estudiante: txtIdEstudiante,
                aii_id_indicador: txtIdIndicador,
                aii_descripcion_indicador: txtDescripcion.value
            };

            $.ajax({
                url: "indicadores-estudiantes-inclusion-agregar.php",
                type: "POST",
                crossDomain: true,
                dataType: 'json',
                data: JSON.stringify(e_datos),
                error: function() {
                    mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);
                    mtdMostrarMensaje("No se pudo completar la solicitud", "error");
                }
            }).done((respuesta) => {

                mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);

                if (respuesta["estado"] === 'ok') {
                    mtdMostrarMensaje(respuesta["mensaje"]);
                    hizoCambio = true;
                    setTimeout(() => {$('#indicadorEstudianteInclusionModal').modal('hide');}, 1000);

                }
                if (respuesta["estado"] === 'ko') {
                    mtdMostrarMensaje(respuesta["mensaje"], "error");
                }
            });

        } else if (txtOpcion === "editar") {

            htmlButon = btnSubmitSave.innerHTML;

            mtdActivarLoadBoton(btnSubmitSave, "Actualizando...");
            
            var e_datos = {
                aii_id: txtIdIndicadorNuevo,
                aii_id_estudiante: txtIdEstudiante,
                aii_id_indicador: txtIdIndicador,
                aii_descripcion_indicador: txtDescripcion.value,
            };

            $.ajax({
                url: "indicadores-estudiantes-inclusion-editar.php",
                type: "POST",
                crossDomain: true,
                dataType: 'json',
                data: JSON.stringify(e_datos),
                error: function() {
                    mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);
                    mtdMostrarMensaje("No se pudo completar la solicitud", "error");
                }
            }).done((respuesta) => {

                mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);

                if (respuesta["estado"] === 'ok') {
                    mtdMostrarMensaje(respuesta["mensaje"]);
                    hizoCambio = true;
                    setTimeout(() => {$('#indicadorEstudianteInclusionModal').modal('hide');}, 1000);
                }
                if (respuesta["estado"] === 'ko') {
                    mtdMostrarMensaje(respuesta["mensaje"], "error");
                }
            });
        }
    }

    //se ejecuta cuando se cierra el modal
    $("#indicadorEstudianteInclusionModal").on("hidden.bs.modal", function () {
        
        if (hizoCambio) {
            mtdActivarLoadPagina();
            location.reload();
        }else{
            mtdLimpiarInputs();
        }
    });
    
});

/**
 * Limpia los campos del formulario de indicadores de estudiante.
 * 
 */
function mtdLimpiarInputs() {
    txtDescripcion.value = null;
}


/**
 * Muestra el modal para agregar o editar un indicador de estudiante.
 * @param idIndicador - El ID del indicador a editar, o 0 para agregar uno nuevo.
 * @param idEstudiante - El ID del estudiante al que se le está agregando el indicador.
 * 
 */
function btnEditarClic(idIndicadorNuevo, idEstudiante, idIndicador ) {

    txtIdIndicadorNuevo = idIndicadorNuevo;
    txtIdEstudiante = idEstudiante;
    txtIdIndicador = idIndicador;

    if (txtIdIndicadorNuevo == 0) {

        txtOpcion = "agregar";

        mtdLimpiarInputs();

        $('#indicadorEstudianteInclusionModal').modal('show');
    }else {
        txtOpcion = "editar";


        mtdActivarLoadPagina();

        $.ajax({
            url: "indicadores-estudiantes-inclusion-consultar.php",
            type: "POST",
            crossDomain: true,
            dataType: 'json',
            data: JSON.stringify({
                aii_id: txtIdIndicadorNuevo
            }),
            error: function() {
                mtdDesactivarLoadPagina();
                mtdMostrarMensaje("No se pudo completar la solicitud", "error");
            }
        }).done((respuesta) => {

            mtdDesactivarLoadPagina();

            if (respuesta["estado"] === 'ok') {

                txtDescripcion.value = respuesta["datos"][0].aii_descripcion_indicador;

                $('#indicadorEstudianteInclusionModal').modal('show');

            }
            if (respuesta["estado"] === 'ko') {
                mtdMostrarMensaje(respuesta["mensaje"], "error");
            }
        });
    }
}

/**
 * Muestra una confirmación para eliminar un indicador de estudiante.
 * @param idIndicador - El ID del indicador a eliminar.
 * 
 */
function btnEliminarClic(idIndicador) {
    sweetConfirmacion(
        'Alerta!',
        '¿Está seguro de eliminar el indicador?',
        'question',
        'indicadores-estudiantes-inclusion-eliminar.php',
        true,
        null,
        'POST',
        {   
            aii_id: idIndicador
        },
        'respuestaEliminar'
    );
}

/**
 * Captura la respuesta de la solicitud AJAX para eliminar un documento.
 * @param respuestaAjax - El resultado de la solicitud AJAX.
 * @param datosEnviados - Los datos enviados en la solicitud.
 * @param respuetaSweet - La respuesta de la confirmación de SweetAlert.
 * 
 */
function respuestaEliminar(respuestaAjax, datosEnviados, respuetaSweet) {
    if (!respuetaSweet) {
        return;
    }

    if (respuestaAjax["data"]["estado"] === 'ok') {

        mtdMostrarMensaje(respuestaAjax["data"]["mensaje"]);                
        setTimeout(() => {mtdActivarLoadPagina(); }, 200); 
        setTimeout(() => { location.reload(); }, 1000);  

    } else {
        mtdMostrarMensaje(respuestaAjax["data"]["mensaje"], "error");
    }
}