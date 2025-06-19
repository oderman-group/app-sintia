
let txtOpcion = "";
let textDocumento = "";
let htmlButon = "";
let hizoCambio = false; 

document.addEventListener('DOMContentLoaded', () => {   

    const txtIdEstudiante = document.getElementById('txtIdEstudiante').value;
    const txtIdInstitucion = document.getElementById('txtIdInstitucion').value;
    const txtIdAnno = document.getElementById('txtIdAnno').value; 

    const formAdjuntarDocumento = document.getElementById('formAdjuntarDocumento');
    const txtIdDocumento = document.getElementById('txtIdDocumento');
    const txtTitulo = document.getElementById('txtTitulo');
    const txtDescripcion = document.getElementById('txtDescripcion');
    const uplDocumento = document.getElementById('uplDocumento');
    const txtVerDocumento = document.getElementById('txtVerDocumento');
    const chkVisible = document.getElementById('chkVisible');
    const btnSubmitSave = document.getElementById('btnSubmitSave');


    btnSubmitSave.addEventListener('click', btnSubmitSaveClick);
    function btnSubmitSaveClick(e) {
        e.preventDefault();

        if( txtOpcion === "agregar"){

            if (uplDocumento.files.length == 0) {
                mtdMostrarMensaje("Seleccione un documento", "error");
                uplDocumento.focus();
                return;
            }

            htmlButon = btnSubmitSave.innerHTML;

            mtdActivarLoadBoton(btnSubmitSave, "Guardando...");

            var data = new FormData(formAdjuntarDocumento);

            $.ajax({
                url: "../compartido/cargar-documentos.php",
                type: "POST",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                error: function() {
                    mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);
                    mtdMostrarMensaje("No se pudo completar la solicitud", "error");
                },
                success: (respuesta) => {
                    respuesta = JSON.parse(respuesta);
                    if (respuesta["estado"] === 'ok') {

                        var e_datos = {
                            ama_id: txtIdDocumento.value,
                            ama_id_estudiante: txtIdEstudiante,
                            ama_documento: respuesta["datos"].archivo,
                            ama_visible: chkVisible.checked,
                            institucion: txtIdInstitucion,
                            year: txtIdAnno,
                            ama_titulo: txtTitulo.value,
                            ama_descripcion: txtDescripcion.value
                        };

                        $.ajax({
                            url: "matriculas-adjuntar-documentos-agregar.php",
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
                                mtdLimpiarInputs();

                            }
                            if (respuesta["estado"] === 'ko') {
                                mtdMostrarMensaje(respuesta["mensaje"], "error");
                            }
                        });

                    }
                    if (respuesta["estado"] === 'ko') {
                        $.ajax({
                            url: "../compartido/cargar-documentos.php",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                opcion: "eliminar_documento_estudiante",
                                ama_documento: respuesta["datos"].archivo
                            }
                        }).done((respuesta) => {
                            if (respuesta["estado"] === 'ok') {
                                mtdMostrarMensaje(respuesta["mensaje"], "error");
                            }
                        });  
                        
                    }
                    return;
                }
            });
        } else if (txtOpcion === "editar") {

            if (uplDocumento.files.length == 0) {

                htmlButon = btnSubmitSave.innerHTML;

                mtdActivarLoadBoton(btnSubmitSave, "Actualizando...");
                
                var e_datos = {
                    ama_id: txtIdDocumento.value,
                    ama_id_estudiante: txtIdEstudiante,
                    ama_documento: textDocumento,
                    ama_visible: chkVisible.checked,
                    institucion: txtIdInstitucion,
                    year: txtIdAnno,
                    ama_titulo: txtTitulo.value,
                    ama_descripcion: txtDescripcion.value
                };

                $.ajax({
                    url: "matriculas-adjuntar-documentos-editar.php",
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
                        setTimeout(() => {$('#documentoAdjuntoModal').modal('hide');}, 1000);
                    }
                    if (respuesta["estado"] === 'ko') {
                        mtdMostrarMensaje(respuesta["mensaje"], "error");
                    }
                });
            }else {

                htmlButon = btnSubmitSave.innerHTML;

                mtdActivarLoadBoton(btnSubmitSave, "Actualizando...");

                var data = new FormData(formAdjuntarDocumento);

                $.ajax({
                url: "../compartido/cargar-documentos.php",
                type: "POST",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                error: function() {
                    mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);
                    mtdMostrarMensaje("No se pudo completar la solicitud", "error");
                },
                success: (respuesta) => {
                    respuesta = JSON.parse(respuesta);
                    if (respuesta["estado"] === 'ok') {

                        var e_datos = {
                            ama_id: txtIdDocumento.value,
                            ama_id_estudiante: txtIdEstudiante,
                            ama_documento: respuesta["datos"].archivo,
                            ama_visible: chkVisible.checked,
                            institucion: txtIdInstitucion,
                            year: txtIdAnno,
                            ama_titulo: txtTitulo.value,
                            ama_descripcion: txtDescripcion.value
                        };

                        $.ajax({
                            url: "matriculas-adjuntar-documentos-editar.php",
                            type: "POST",
                            crossDomain: true,
                            dataType: 'json',
                            data: JSON.stringify(e_datos),
                            error: function() {
                                mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);
                                mtdMostrarMensaje("No se pudo completar la solicitud", "error");
                            }
                        }).done((respuesta) => {                            

                            if (respuesta["estado"] === 'ok') {

                                // Eliminar el documento anterior si se ha cambiado
                                $.ajax({
                                    url: "../compartido/cargar-documentos.php",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {
                                        opcion: "eliminar_documento_estudiante",
                                        ama_documento: textDocumento
                                    }
                                }).done((respuestaEliminacion) => {

                                    mtdDesactivarLoadBoton(btnSubmitSave, htmlButon);
                                    
                                    if (respuestaEliminacion["estado"] === 'ok') {
                                        mtdMostrarMensaje(respuesta["mensaje"]);
                                        hizoCambio = true;
                                        setTimeout(() => {$('#documentoAdjuntoModal').modal('hide');}, 1000);
                                    }
                                }); 
                            }
                            if (respuesta["estado"] === 'ko') {
                                mtdMostrarMensaje(respuesta["mensaje"], "error");
                            }
                        });

                    }
                    if (respuesta["estado"] === 'ko') {
                        $.ajax({
                            url: "../compartido/cargar-documentos.php",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                opcion: "eliminar_documento_estudiante",
                                ama_documento: respuesta["datos"].archivo
                            }
                        }).done((respuesta) => {
                            if (respuesta["estado"] === 'ok') {
                                mtdMostrarMensaje(respuesta["mensaje"], "error");
                            }
                        });  
                        
                    }
                    return;
                }
            });

            }
        }
    }

    //se ejecuta cuando se cierra el modal
    $("#documentoAdjuntoModal").on("hidden.bs.modal", function () {
        
        if (hizoCambio) {
            mtdActivarLoadPagina();
            location.reload();
        }else{
            mtdLimpiarInputs();
        }
    });
    
});

/**
 * Limpia los campos del formulario de adjuntar documentos.
 * 
 */
function mtdLimpiarInputs() {
    txtIdDocumento.value = 0;
    txtTitulo.value = null;
    txtDescripcion.value = null;
    uplDocumento.value = null;
    chkVisible.checked = false;
}

/**
 * Muestra el modal para agregar un nuevo documento adjunto.
 * 
 */
function btnNuevoClic() {

    txtOpcion = "agregar";
    txtVerDocumento.classList.add("d-none");

    mtdLimpiarInputs();

    $('#documentoAdjuntoModal').modal('show');
}

/**
 * Muestra el modal para editar la informacion del documento adjunto.
 * @param idDocumento - El ID del documento a editar.
 * 
 */
function btnEditarClic(idDocumento) {

    txtOpcion = "editar";
    txtVerDocumento.classList.remove("d-none");

     mtdActivarLoadPagina();

    $.ajax({
        url: "matriculas-adjuntar-documentos-consultar.php",
        type: "POST",
        crossDomain: true,
        dataType: 'json',
        data: JSON.stringify({
            ama_id: idDocumento
        }),
        error: function() {
            mtdDesactivarLoadPagina();
            mtdMostrarMensaje("No se pudo completar la solicitud", "error");
        }
    }).done((respuesta) => {

        mtdDesactivarLoadPagina();

        if (respuesta["estado"] === 'ok') {
            txtIdDocumento.value = respuesta["datos"][0].ama_id;
            txtTitulo.value = respuesta["datos"][0].ama_titulo;
            txtDescripcion.value = respuesta["datos"][0].ama_descripcion;
            uplDocumento.value = null;
            textDocumento = respuesta["datos"][0].ama_documento;
            chkVisible.checked = respuesta["datos"][0].ama_visible === 0 ? true : false;

            txtVerDocumento.href = "../files/documentos_adjuntos_estudiantes/" + textDocumento;

            $('#documentoAdjuntoModal').modal('show');

        }
        if (respuesta["estado"] === 'ko') {
            mtdMostrarMensaje(respuesta["mensaje"], "error");
        }
    });
}

/**
 * Pregunta si se quiere eliminar un documento adjunto.
 * @param idDocumento - El ID del documento a eliminar.
 * @param nombreDocumento - El nombre del documento a eliminar.
 * 
 */
function btnEliminarClic(idDocumento, nombreDocumento) {
    sweetConfirmacion(
        'Alerta!',
        '¿Está seguro de eliminar este documento?',
        'question',
        'matriculas-adjuntar-documentos-eliminar.php',
        true,
        null,
        'POST',
        {   
            ama_id: idDocumento,
            ama_documento: nombreDocumento
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

        mtdActivarLoadPagina();

        $.ajax({
            url: "../compartido/cargar-documentos.php",
            type: "POST",
            dataType: 'json',
            data: {
                opcion: "eliminar_documento_estudiante",
                ama_documento: datosEnviados["ama_documento"]
            },
            error: function() {
                mtdDesactivarLoadPagina();
                mtdMostrarMensaje("No se pudo completar la solicitud", "error");
            }
        }).done((respuesta) => {

            mtdDesactivarLoadPagina();

            if (respuesta["estado"] === 'ok') {
                mtdMostrarMensaje(respuestaAjax["data"]["mensaje"]);                
                setTimeout(() => { location.reload(); }, 1000);
            }

            if (respuesta["estado"] === 'ko') {
                mtdMostrarMensaje(respuesta["mensaje"], "error");
            }
        });        
    } else {
        mtdMostrarMensaje(respuestaAjax["data"]["mensaje"], "error");
    }
}