/**
 * Esta función me actualiza un sub rol
 */
function comportamientoPeriodo(datosPeriodo) {
    var idInput         = datosPeriodo.id;
    var periodo         = datosPeriodo.value;
    var idRegistro      = datosPeriodo.getAttribute("data-id-registro");
    var periodoActual   = datosPeriodo.getAttribute("data-periodo-actual");
    var periodoTotal    = datosPeriodo.getAttribute("data-periodo-total");

    var inputElement    = document.getElementById(idInput);

    if (periodo <= periodoTotal) {
        fetch('../directivo/comportamiento-actualizar-periodo.php?periodo=' + periodo + '&id=' + idRegistro, {
            method: 'GET'
        })
            .then(response => response.text()) // Convertir la respuesta a texto
            .then(data => {

                inputElement.dataset.periodoActual = periodo;

                $.toast({

                    heading: 'Proceso completado',
                    text: 'El periodo del comportamiento se actualizo correctamente...',
                    position: 'bottom-right',
                    showHideTransition: 'slide',
                    loaderBg: '#26c281',
                    icon: 'success',
                    hideAfter: 5000,
                    stack: 6

                });
            })
            .catch(error => {
                // Manejar errores
                console.error('Error:', error);
            });
    } else {

        inputElement.value = periodoActual;

        $.toast({

            heading: 'Periodo Invalido',
            text: 'El periodo que ingreso no es valido...',
            position: 'bottom-right',
            showHideTransition: 'slide',
            loaderBg: '#26c281',
            icon: 'warning',
            hideAfter: 5000,
            stack: 6

        });
    }
}