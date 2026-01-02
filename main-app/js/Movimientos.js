/**
 * Formatea un número con separadores de miles y decimales personalizables.
 * @param {number} number - El número a formatear.
 * @param {number} [decimals=0] - La cantidad de decimales a mostrar (por defecto, 0).
 * @param {string} [decPoint=','] - El separador decimal (por defecto, ',').
 * @param {string} [thousandsSep='.'] - El separador de miles (por defecto, '.').
 * @returns {string} - El número formateado como cadena.
 */
function numberFormat(number, decimals = 0, decPoint = ',', thousandsSep = '.') {
    // Validar que number sea un número
    if (isNaN(number) || number === '' || number === null) {
        return '';
    }

    // Redondear el número al número especificado de decimales
    number = parseFloat(number.toFixed(decimals));

    // Convertir el número a una cadena y separar los miles
    var parts = number.toString().split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

    // Unir la parte entera y decimal con el separador decimal
    var result = parts.join(decPoint);

    return result;
}

/**
 * Actualiza el subtotal según el precio y la cantidad especificados.
 * @param {string} id - Identificador del elemento o 'idNuevo' para un nuevo item.
 */
function actualizarSubtotal(id) {
    var idItem=document.getElementById('idItemNuevo').innerText;
    // Obtener los elementos
    var precioElement = document.getElementById('precioNuevo');
    var cantidadElement = document.getElementById('cantidadItemNuevo');
    var subtotalElement = document.getElementById('subtotalNuevo');
    var descuentoElement = document.getElementById('descuentoNuevo');
    var impuestoElement = document.getElementById('impuestoNuevo');
    var rowElement = null; // Para obtener el tipo de item
    if(id !== 'idNuevo'){
        var idItem=id
        // Obtener los elementos
        var precioElement = document.getElementById('precio'+id);
        var cantidadElement = document.getElementById('cantidadItems'+id);
        var subtotalElement = document.getElementById('subtotal'+id);
        var descuentoElement = document.getElementById('descuento'+id);
        var impuestoElement = document.getElementById('impuesto'+id);
        // Obtener la fila para acceder al data-item-type
        rowElement = document.getElementById('reg'+id);
    }
    
    // Determinar si es crédito antes de validar
    var itemType = rowElement ? (rowElement.getAttribute('data-item-type') || 'D') : (subtotalElement ? (subtotalElement.getAttribute('data-item-type') || 'D') : 'D');
    var isCredito = (itemType === 'C');
    
    // Para items crédito, solo validar precio y cantidad (sin descuento)
    var regex = /^[0-9]+(\.[0-9]+)?$/;
    var validacionOk = false;
    
    if (isCredito) {
        // Items crédito: solo precio y cantidad, sin descuento ni impuesto
        validacionOk = (precioElement.value.trim() !== '' && cantidadElement.value.trim() !== '' && 
                       regex.test(precioElement.value) && regex.test(cantidadElement.value));
    } else {
        // Items débito: precio, cantidad y descuento
        validacionOk = (precioElement.value.trim() !== '' && cantidadElement.value.trim() !== '' && 
                       descuentoElement.value.trim() !== '' && 
                       regex.test(precioElement.value) && regex.test(cantidadElement.value) && regex.test(descuentoElement.value));
    }

    if (validacionOk) {

        // Obtener los valores
        var precio = parseFloat(precioElement.value);
        var cantidad = parseFloat(cantidadElement.value);
        var porcentajeDescuento = isCredito ? 0 : (parseFloat(descuentoElement.value) || 0);
        var impuesto = isCredito ? 0 : (parseFloat(impuestoElement.value) || 0);

        // Calcular el subtotal
        var subtotal = 0;
        
        if (isCredito) {
            // Items crédito: cálculo directo sin descuentos ni impuestos
            subtotal = precio * cantidad;
        } else {
            // Items débito: aplicar descuento
            var vlrDescuento = precio * (porcentajeDescuento / 100);
            subtotal = (precio - vlrDescuento) * cantidad;
        }
        
        var signoNegativo = isCredito ? '-' : '';
        var subtotalFormat = signoNegativo+"$"+numberFormat(subtotal, 0, ',', '.');
        
        fetch('../directivo/ajax-cambiar-subtotal.php?subtotal='+(subtotal)+'&cantidad='+(cantidad)+'&precio='+(precio)+'&idItem='+(idItem)+'&porcentajeDescuento='+(porcentajeDescuento)+'&impuesto='+(impuesto), {
            method: 'GET'
        })
        .then(response => response.text()) // Convertir la respuesta a texto
        .then(data => {
            precioElement.dataset.precio = precio;
            cantidadElement.dataset.cantidad = cantidad;
            if (!isCredito && descuentoElement) {
                descuentoElement.dataset.descuentoAnterior = porcentajeDescuento;
            }
            
            // Recalcular el total después de actualizar el subtotal
            totalizar();

            subtotalElement.innerHTML = '';
            subtotalElement.appendChild(document.createTextNode(subtotalFormat));
            subtotalElement.dataset.subtotalAnterior = subtotal;
            if (itemType) {
                subtotalElement.setAttribute('data-item-type', itemType);
            }

            totalizar();

            $.toast({
                heading: 'Acción realizada',
                text: 'Valor guardado correctamente.',
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
        var mensajeError = isCredito 
            ? "Los campos de precio y cantidad no pueden ir vacío, o con letras"
            : "Los campos de precio, descuento y cantidad no pueden ir vacío, o con letras";
            
        Swal.fire({
            title: 'Campo Vacío',
            text: mensajeError,
            icon: 'warning',
            showCancelButton: false,
            confirmButtonText: 'Ok',
            backdrop: `
                rgba(0,0,123,0.4)
                no-repeat
            `,
        }).then((result) => {
            var precioAnterior = parseFloat(precioElement.getAttribute("data-precio"));
            var cantidadAnterior = parseFloat(cantidadElement.getAttribute("data-cantidad"));
            var descuentoAnterior = isCredito ? 0 : parseFloat(descuentoElement.getAttribute("data-descuento-anterior"));
            
            precioElement.value = precioAnterior;
            cantidadElement.value = cantidadAnterior;
            if (!isCredito && descuentoElement) {
                descuentoElement.value = descuentoAnterior;
            }
        })

    }
}

/**
 * Trae y actualiza la lista de items asociados a una transacción.
 */
function traerItems(){
    // Obtener el valor del ID de transacción desde el elemento HTML
    var idTransaction = document.getElementById('idTransaction').value;
    var typeTransaction = document.getElementById('typeTransaction').value;

    // Mostrar un mensaje de carga mientras se obtienen los items
    $('#mostrarItems').empty().hide().html("Cargando Items...").show(1);
    
    // Realizar una solicitud fetch para obtener los items asociados a la transacción
    fetch('../directivo/ajax-traer-items.php?idTransaction=' + idTransaction + '&typeTransaction=' + typeTransaction, {
        method: 'GET'
    })
    .then(response => response.text()) // Convertir la respuesta a texto
    .then(data => {
        // Actualizar el contenido de 'mostrarItems' con la respuesta obtenida
        $('#mostrarItems').empty().hide().html(data).show(1);

        totalizar();

        $.toast({
            heading: 'Acción realizada',
            text: 'Escoja un nuevo item.',
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
}

/**
 * Guarda un nuevo item asociado a una transacción.
 * @param {HTMLSelectElement} selectElement - El elemento select que contiene la opción seleccionada.
 */
function guardarNuevoItem(selectElement) {
    // Obtener los elementos del DOM
    var itemElement = document.getElementById('idItemNuevo');
    var precioElement = document.getElementById('precioNuevo');
    var descuentoElement = document.getElementById('descuentoNuevo');
    var impuestoElement = document.getElementById('impuestoNuevo');
    var impuestoContainer = document.getElementById('select2-impuestoNuevo-container');
    var descripElement = document.getElementById('descripNueva');
    var cantidadElement = document.getElementById('cantidadItemNuevo');
    var subtotalElement = document.getElementById('subtotalNuevo');
    var idEliminarNuevo = document.getElementById('eliminarNuevo');

    var itemModificar = '';
    var cantidad = 1;
    // Verificar si el contenido del idItemNuevo no esta vacio
    if (itemElement.innerHTML.trim() !== '') {
        var itemModificar = itemElement.innerHTML;
        var cantidad = cantidadElement.value;
    }

    // Obtener el ID de la transacción desde el elemento del DOM
    var idTransaction = document.getElementById('idTransaction').value;
    var typeTransaction = document.getElementById('typeTransaction').value;

    // Obtener la opción seleccionada del elemento select
    var itemSelecionado = selectElement.options[selectElement.selectedIndex];

    // Obtener el ID del item, el precio y calcular el subtotal
    var idItem = itemSelecionado.value;
    var precio = parseFloat(itemSelecionado.getAttribute('name'));

    var subtotal = precio * cantidad;
    var subtotalFormat = "$"+numberFormat(subtotal, 0, ',', '.');

    // Realizar una solicitud fetch para guardar el nuevo item
    fetch('../directivo/ajax-guardar-items.php?idTransaction=' + idTransaction + '&idItem=' + idItem + '&itemModificar=' + itemModificar + '&subtotal=' + subtotal + '&cantidad=' + cantidad + '&precio=' + precio + '&typeTransaction=' + typeTransaction, {
        method: 'GET'
    })
    .then(response => {
        // Verificar si la respuesta es JSON válida
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(text || 'Error en la respuesta del servidor');
            });
        }
        return response.json();
    })
    .then(data => {
        // Verificar si hay error en la respuesta
        if (data.error) {
            throw new Error(data.message || 'Error desconocido');
        }

        // Actualizar los elementos del DOM con los datos recibidos
        itemElement.innerHTML = '';
        itemElement.appendChild(document.createTextNode(data.idInsercion));

        // Usar precio del servidor si está disponible, sino usar el del select
        var precioFinal = data.precio || precio;
        precioElement.disabled = false;
        precioElement.value = precioFinal;
        precioElement.dataset.precio = precioFinal;
        precioElement.dataset.precioAnterior = precioFinal;

        // Actualizar descripción si está disponible
        if (data.descripcion) {
            descripElement.disabled = false;
            descripElement.value = data.descripcion;
        } else {
            descripElement.disabled = false;
        }
        
        cantidadElement.disabled = false;

        // Determinar si es crédito para mostrar signo negativo en el subtotal
        var itemType = data.item_type || 'D';
        var isCredito = (itemType === 'C');
        var applicationTime = isCredito ? (data.application_time || 'ANTE_IMPUESTO') : null;
        var signoNegativo = isCredito ? '-' : '';
        var subtotalFormatFinal = signoNegativo + "$" + numberFormat(subtotal, 0, ',', '.');
        
        subtotalElement.innerHTML = '';
        subtotalElement.appendChild(document.createTextNode(subtotalFormatFinal));
        subtotalElement.dataset.subtotalAnterior = subtotal;
        subtotalElement.setAttribute('data-item-type', itemType);

        // Para items crédito, deshabilitar campos de descuento e impuesto
        if (isCredito) {
            descuentoElement.disabled = true;
            descuentoElement.value = 0;
            impuestoElement.disabled = true;
            if (typeof $ !== 'undefined' && $(impuestoElement).hasClass('select2-hidden-accessible')) {
                $(impuestoElement).val(0).trigger('change');
            } else {
                impuestoElement.value = 0;
            }
        } else {
            descuentoElement.disabled = false;
            descuentoElement.value = 0;
            impuestoElement.disabled = false;
        }

        // Si es un item nuevo, establecer valores por defecto
        if (data.creado == 1) {
            // Para items crédito, no establecer impuesto (ya está deshabilitado)
            if (!isCredito) {
                // Establecer el impuesto del item si existe
                if (data.tax && data.tax != '0') {
                    impuestoElement.value = data.tax;
                    // Actualizar el contenedor de Select2 usando jQuery
                    if (typeof $ !== 'undefined' && $(impuestoElement).hasClass('select2')) {
                        $(impuestoElement).val(data.tax).trigger('change');
                    } else {
                        var taxOption = impuestoElement.querySelector('option[value="' + data.tax + '"]');
                        if (taxOption && impuestoContainer) {
                            impuestoContainer.innerHTML = taxOption.textContent;
                        }
                    }
                } else {
                    impuestoElement.value = 0;
                    if (typeof $ !== 'undefined' && $(impuestoElement).hasClass('select2')) {
                        $(impuestoElement).val(0).trigger('change');
                    } else if (impuestoContainer) {
                        impuestoContainer.innerHTML = 'Ninguno - (0%)';
                    }
                }
            }
            
            // Si es un item nuevo, agregar la fila a la tabla inmediatamente
            // Esto permite que totalizar() pueda calcular correctamente en tiempo real
            var filaNueva = itemElement.closest('tr');
            if (filaNueva) {
                // Agregar atributo data-item-type a la fila para que totalizar() lo detecte
                filaNueva.setAttribute('data-item-type', itemType);
                // Agregar atributo data-application-time para items crédito
                if (isCredito && applicationTime) {
                    filaNueva.setAttribute('data-application-time', applicationTime);
                } else {
                    filaNueva.removeAttribute('data-application-time');
                }
                // Agregar clase si es crédito
                if (isCredito) {
                    filaNueva.classList.add('item-credito');
                } else {
                    filaNueva.classList.remove('item-credito');
                }
            }
        } else {
            // Si está modificando, mantener valores anteriores (pero si es crédito, ya están deshabilitados)
            if (!isCredito) {
                descuentoElement.value = 0;
                impuestoElement.value = 0;
                if (typeof $ !== 'undefined' && $(impuestoElement).hasClass('select2')) {
                    $(impuestoElement).val(0).trigger('change');
                } else if (impuestoContainer) {
                    impuestoContainer.innerHTML = 'Ninguno - (0%)';
                }
            }
        }

        var html='<a href="#" title="Eliminar item nuevo" name="movimientos-items-eliminar.php?idR='+data.idInsercion+'" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" data-toggle="tooltip" onClick="deseaEliminarNuevoItem(this)" data-placement="right">X</a>';
        idEliminarNuevo.innerHTML = html;

        // Recalcular subtotal con el impuesto si existe (solo para items débito)
        if (!isCredito && data.tax && data.tax != '0' && data.creado == 1) {
            actualizarSubtotal('idNuevo');
        } else {
            // Llamar a totalizar() para recalcular el total considerando el nuevo item
            totalizar();
        }

        $.toast({
            heading: 'Acción realizada',
            text: 'Nuevo item agregado correctamente.',
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
        $.toast({
            heading: 'Error',
            text: 'Error al guardar el item: ' + error.message,
            position: 'bottom-right',
            showHideTransition: 'slide',
            loaderBg: '#ff0039',
            icon: 'error',
            hideAfter: 8000,
            stack: 6
        });
    });
}

/**
 * Realiza la acción de añadir un nuevo item.
 * Limpia y actualiza los elementos relacionados con la información del nuevo item.
 */
function nuevoItem() {
    // Realizar la acción de traer nuevos items
    traerItems();

    // Obtener elementos del DOM
    var idItemNuevo = document.getElementById('idItemNuevo');
    var precioNuevo = document.getElementById('precioNuevo');
    var descripElement = document.getElementById('descripNueva');
    var cantidadNuevo = document.getElementById('cantidadItemNuevo');
    var items = document.getElementById('items');
    var itemsContainer = document.getElementById('select2-items-container');
    var idEliminarNuevo = document.getElementById('eliminarNuevo');
    var descuentoElement = document.getElementById('descuentoNuevo');
    var impuestoElement = document.getElementById('impuestoNuevo');
    var impuestoContainer = document.getElementById('select2-impuestoNuevo-container');

    // Limpiar y reiniciar los elementos del DOM relacionados con el nuevo item
    idItemNuevo.innerHTML = '';
    precioNuevo.value = 0;
    precioNuevo.dataset.precio = 0;
    precioNuevo.dataset.precioAnterior = 0;
    precioNuevo.disabled = true;
    descripElement.value = '';
    descripElement.disabled = true;
    cantidadNuevo.value = 1;
    cantidadNuevo.disabled = true;
    subtotalNuevo.innerHTML = '$0';
    subtotalNuevo.dataset.subtotalAnterior = 0;
    items.value = '';
    itemsContainer.innerHTML = 'Seleccione una opción';
    idEliminarNuevo.innerHTML = '';
    descuentoElement.value = 0;
    descuentoElement.dataset.precioItemAnterior = 0;
    descuentoElement.disabled = true;
    impuestoElement.value = 0;
    impuestoElement.disabled = true;
    impuestoContainer.innerHTML = 'Ninguno - (0%)';
}

/**
 * Esta función pide confirmación al usuario antes de eliminar un itenm nuevo
 * @param {Array} dato 
 */
function deseaEliminarNuevoItem(dato) {

    if (dato.title !== 'Eliminar item nuevo') {
        let variable = (dato.title);
        var varObjet = JSON.parse(variable);
        var id = dato.id;
        var registro = document.getElementById("reg" + id);
    }

    // Obtener los elementos del DOM
    var items = document.getElementById('items');
    var itemsContainer = document.getElementById('select2-items-container');
    var itemElement = document.getElementById('idItemNuevo');
    var precioElement = document.getElementById('precioNuevo');
    var descripElement = document.getElementById('descripNueva');
    var cantidadElement = document.getElementById('cantidadItemNuevo');
    var subtotalElement = document.getElementById('subtotalNuevo');
    var idEliminarNuevo = document.getElementById('eliminarNuevo');
    var descuentoElement = document.getElementById('descuentoNuevo');
    var impuestoElement = document.getElementById('impuestoNuevo');
    var impuestoContainer = document.getElementById('select2-impuestoNuevo-container');

    var url = dato.name;

    Swal.fire({
        title: 'Desea eliminar?',
        text: "Al eliminar este registro es posible que se eliminen otros registros que estén relacionados. Desea continuar bajo su responsabilidad?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, deseo eliminar!',
        cancelButtonText: 'No',
        backdrop: `
            rgba(0,0,123,0.4)
            no-repeat
        `,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(url).then(function(response) {
                if (typeof varObjet !== "undefined") {

                        async function miFuncionConDelay() {
                            await new Promise(resolve => setTimeout(resolve, 1000));
                            registro.style.display = "none";
                        }

                        miFuncionConDelay();

                        registro.classList.add('animate__animated', 'animate__bounceOutRight', 'animate__delay-0.5s', 'fila-oculta');

                } else {

                    // Actualizar los elementos del DOM con los datos recibidos
                    items.value = '';
                    itemsContainer.innerHTML = 'Seleccione una opción';

                    itemElement.innerHTML = '';

                    precioElement.disabled = true;
                    precioElement.value = 0;
                    precioElement.dataset.precio = 0;

                    descripElement.value = '';
                    descripElement.disabled = true;

                    cantidadElement.disabled = true;
                    cantidadElement.value = 1;

                    subtotalElement.innerHTML = '$0';
                    subtotalElement.dataset.subtotalAnterior = 0;

                    idEliminarNuevo.innerHTML = '';

                    descuentoElement.value = 0;
                    descuentoElement.dataset.precioItemAnterior = 0;
                    descuentoElement.disabled = true;

                    impuestoElement.value = 0;
                    impuestoElement.disabled = true;
                    impuestoContainer.innerHTML = 'Ninguno - (0%)';
                }

                totalizar();

                $.toast({
                    heading: 'Acción realizada',
                    text: 'El registro fue eliminado correctamente.',
                    position: 'bottom-right',
                    showHideTransition: 'slide',
                    loaderBg: '#26c281',
                    icon: 'success',
                    hideAfter: 5000,
                    stack: 6
                });

            }).catch(function(error) {
                // handle error
                console.error(error);
            });            
        }else{
            return false;
        }
    })
}

/**
 * Actualiza la descripción de un item.
 * @param {string} id - Identificador del elemento o 'idNuevo' para un nuevo item.
 */
function guardarDescripcion(id) {
    var idItem=document.getElementById('idItemNuevo').innerText;
    // Obtener los elementos
    var descripElement = document.getElementById('descripNueva');
    if(id !== 'idNuevo'){
        var idItem=id
        // Obtener los elementos
        var descripElement = document.getElementById('descrip'+id);
    }
    var descripcion = descripElement.value;
    
    fetch('../directivo/ajax-guardar-descripcion.php?descripcion='+(descripcion)+'&idItem='+(idItem), {
        method: 'GET'
    })
    .then(response => response.text()) // Convertir la respuesta a texto
    .then(data => {
        descripElement.value = descripcion;

        $.toast({
            heading: 'Acción realizada',
            text: 'La descripción fue guardada correctamente.',
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
}

/**
 * Esta función anula una movimiento financiero.
 * @param {string} datos
 */
function anularMovimiento(datos) {
    
    var idR = datos.getAttribute('data-id-registro');
    var idUsuario = datos.getAttribute('data-id-usuario');

    Swal.fire({
        title: 'Alerta!',
        text: "¿Deseas anular esta transacción?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, deseo anular!',
        cancelButtonText: 'No',
        backdrop: `
            rgba(0,0,123,0.4)
            no-repeat
        `,
    }).then((result) => {
        if (result.isConfirmed) {

            fetch('../directivo/movimientos-anular.php?idR='+(idR)+'&id='+(idUsuario), {
                method: 'GET'
            })
            .then(response => response.text()) // Convertir la respuesta a texto
            .then(data => {

                document.getElementById("reg"+idR).style.backgroundColor="#ff572238";
                document.getElementById("anulado"+idR).style.display = "none";
                document.getElementById("totalNeto"+idR).dataset.anulado = 1;

                $.toast({
                    heading: 'Acción realizada',
                    text: 'La transacción fue anulada correctamente.',
                    position: 'bottom-right',
                    showHideTransition: 'slide',
                    loaderBg: '#26c281',
                    icon: 'success',
                    hideAfter: 5000,
                    stack: 6
                });

                totalizarMovimientos();
            })
            .catch(error => {
                // Manejar errores
                console.error('Error:', error);
            });
        }else{
            return false;
        }
    })
}

/**
* Se valida input para que solo reciba numeros decimales
*/
function validarInput(datos) {
    var valor = datos.value;

    // Utilizar una expresión regular para verificar si el valor es un número decimal válido
    var regex = /^[0-9]+(\.[0-9]+)?$/;

    if (regex.test(valor)) {
        document.getElementById("resp").style.display = 'none';
        document.getElementById("btnEnviar").style.visibility = 'visible';
        $("#resp").html('');
    } else {
        document.getElementById("resp").style.color = 'red';
        document.getElementById("resp").style.display = 'block';
        document.getElementById("btnEnviar").style.visibility = 'hidden';
        $("#resp").html('Por favor, ingrese solo números.');
    }
}

/**
 * Esta función muestra el campo para escoger el tipo de transacción
 */
function mostrarTipoTransaccion(){
    document.getElementById("divTipoTransaccion").style.display="block";
}

/**
 * Segun el tipo de transacción me habilita algunos campos
 * @param {int} tipo 
 */
function tipoAbono(tipo){

	if(tipo==1){
        var idAbono = document.getElementById('idAbono').value;
        var idUsuario = document.getElementById('select_cliente').value;
    
        document.getElementById("divFacturas").style.display="block";
        document.getElementById("divCuentasContables").style.display="none";

        document.getElementById("opt1").checked="checked";
        document.getElementById("opt2").checked="";
        $('#mostrarFacturas').empty().hide().html("<tr><td colspan='5' align='center' style='font-size: 17px; font-weight:bold;'>Cargando Facturas...</td></tr>").show(1);
        
        fetch('../directivo/ajax-traer-facturas.php?idUsuario=' + idUsuario + '&idAbono=' + idAbono, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            $('#mostrarFacturas').empty().hide().html(data).show(1);
            // Llamar a totalizarAbonos después de cargar las facturas
            setTimeout(function() {
                totalizarAbonos();
            }, 100);
        })
        .catch(error => {
            console.error('Error:', error);
        });
	}
	if(tipo==2){
        $('#mostrarFacturas').empty().hide().html('').show(1);
		document.getElementById("divFacturas").style.display="none";
		document.getElementById("divCuentasContables").style.display="block";

		document.getElementById("opt1").checked="";
		document.getElementById("opt2").checked="checked";
		
		// Habilitar campos para que puedan ser llenados y enviados en el formulario
		// Los campos solo se habilitarán completamente cuando se seleccione un concepto
		// pero necesitamos asegurarnos de que puedan recibir valores
		var precioElement = document.getElementById('precioNuevo');
		var cantidadElement = document.getElementById('cantidadNuevo');
		var descripElement = document.getElementById('descripNueva');
		
		// Los campos seguirán disabled hasta que se seleccione un concepto
		// pero se habilitarán automáticamente cuando guardarNuevoConcepto() se ejecute
	}
}

/**
 * Actualiza lo abonado a una factura (SOLO EN MEMORIA - SIN AJAX)
 * Los datos se guardarán en BD cuando el usuario haga submit del formulario
 * @param {HTMLElement} datos - Input element con el valor del abono
 */
function actualizarAbonado(datos) {
    var abono = datos.value;

    if (abono.trim() !== '') {
        var nuevoAbono = parseFloat(datos.value) || 0;
        var idFactura = datos.getAttribute("data-id-factura");
        var abonoAnterior = parseFloat(datos.getAttribute("data-abono-anterior")) || 0;
        
        // Actualizar SOLO visualmente (sin AJAX)
        var elementTotalNeto = document.getElementById("totalNeto" + idFactura);
        var elementAbono = document.getElementById("abonos" + idFactura);
        var elementPorCobrar = document.getElementById("porCobrar" + idFactura);
        
        if (!elementTotalNeto || !elementAbono || !elementPorCobrar) {
            return;
        }
        
        var totalNeto = parseFloat(elementTotalNeto.getAttribute("data-total-neto")) || 0;
        var totalAbonos = parseFloat(elementAbono.getAttribute("data-abonos")) || 0;

        // Recalcular totales
        var totalAbono = (totalAbonos - abonoAnterior) + nuevoAbono;
        var totalAbonoFinal = "$" + numberFormat(totalAbono, 0, ',', '.');

        var porCobrar = totalNeto - totalAbono;
        var porCobrarFinal = "$" + numberFormat(porCobrar, 0, ',', '.');

        // Actualizar elementos visuales
        elementAbono.innerHTML = '';
        elementAbono.appendChild(document.createTextNode(totalAbonoFinal));
        elementAbono.dataset.abonos = totalAbono;

        elementPorCobrar.innerHTML = '';
        elementPorCobrar.appendChild(document.createTextNode(porCobrarFinal));
        elementPorCobrar.dataset.porCobrar = porCobrar;

        // Guardar el nuevo valor como "anterior" para futuros cambios
        datos.setAttribute("data-abono-anterior", nuevoAbono);

        // Recalcular resumen
        totalizarAbonos();
        
        // Feedback visual sutil (sin toast invasivo)
        datos.style.borderColor = '#26c281';
        setTimeout(function() {
            datos.style.borderColor = '';
        }, 500);

    } else {
        // Si borra el valor, resetear a 0
        datos.value = 0;
        var abonoAnteriorReset = parseFloat(datos.getAttribute("data-abono-anterior")) || 0;
        if (abonoAnteriorReset > 0) {
            datos.setAttribute("data-abono-anterior", 0);
            // Recalcular con valor 0
            actualizarAbonado(datos);
        }
    }
}

/**
 * cambia el estado de una factura a cobrada
 * @param {string} idFactura
 */
function cambiarEstadoFactura(idFactura, estado) {
    
    var registro = document.getElementById("reg" + idFactura);
        
    fetch('../directivo/ajax-cambiar-estado-factura.php?idFactura='+(idFactura)+'&estado='+(estado), {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {

        if (data.estado === "COBRADA") {
            $.toast({
                heading: 'Acción realizada',
                text: 'El registro fue pagado en su totalidad.',
                position: 'bottom-right',
                showHideTransition: 'slide',
                loaderBg: '#26c281',
                icon: 'success',
                hideAfter: 5000,
                stack: 6
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

/**
 * Guarda un nuevo abono.
 * @param {HTMLSelectElement} selectElement - El elemento select que contiene la opción seleccionada.
 */
/**
 * Habilita campos para nuevo concepto contable (SIN AJAX - solo habilita inputs)
 * Los datos se guardarán cuando el usuario haga submit del formulario
 */
function guardarNuevoConcepto(selectElement) {
    var concepto = selectElement.value;
    var precioElement = document.getElementById('precioNuevo');
    var descripElement = document.getElementById('descripNueva');
    var cantidadElement = document.getElementById('cantidadNuevo');
    var conceptoElement = document.getElementById('idConcepto');

    if (concepto && concepto !== '') {
        // Marcar el concepto seleccionado (temporal, solo visual)
        conceptoElement.innerHTML = concepto;
        
        // Habilitar campos para que el usuario pueda ingresar datos
        precioElement.disabled = false;
        descripElement.disabled = false;
        cantidadElement.disabled = false;
        
        // Si ya tenían valores, mantenerlos; si no, inicializar
        if (!precioElement.value || precioElement.value == '0') {
            precioElement.value = '0';
        }
        if (!cantidadElement.value || cantidadElement.value == '0') {
            cantidadElement.value = '1';
        }
        
        // Feedback visual sutil
        selectElement.style.borderColor = '#26c281';
        setTimeout(function() {
            selectElement.style.borderColor = '';
        }, 500);
    }
}

/**
 * Actualiza el subtotal según el precio y la cantidad especificados.
 * @param {string} id
 */
/**
 * Actualiza el subtotal según el precio y la cantidad (SOLO EN MEMORIA - SIN AJAX)
 */
function actualizarSubtotalConceptos(id) {
    var idConcepto = document.getElementById('idConcepto').innerText;
    var precioElement = document.getElementById('precioNuevo');
    var cantidadElement = document.getElementById('cantidadNuevo');
    var subtotalElement = document.getElementById('subtotalNuevo');
    
    if (id !== 'idNuevo') {
        idConcepto = id;
        precioElement = document.getElementById('precio' + id);
        cantidadElement = document.getElementById('cantidad' + id);
        subtotalElement = document.getElementById('subtotal' + id);
    }

    if (precioElement.value.trim() !== '' && cantidadElement.value.trim() !== '') {
        // Obtener los valores
        var precio = parseFloat(precioElement.value) || 0;
        var cantidad = parseFloat(cantidadElement.value) || 0;

        // Calcular el subtotal SOLO visualmente
        var subtotal = precio * cantidad;
        var subtotalFormat = "$" + numberFormat(subtotal, 0, ',', '.');
        
        // Actualizar datos para referencia
        precioElement.dataset.precio = precio;
        cantidadElement.dataset.cantidad = cantidad;

        // Actualizar visualización
        subtotalElement.innerHTML = '';
        subtotalElement.appendChild(document.createTextNode(subtotalFormat));
        
        // Feedback visual sutil
        precioElement.style.borderColor = '#26c281';
        cantidadElement.style.borderColor = '#26c281';
        setTimeout(function() {
            precioElement.style.borderColor = '';
            cantidadElement.style.borderColor = '';
        }, 500);

    } else {
        // Si faltan valores, resetear a valores por defecto
        if (precioElement.value.trim() === '') {
            precioElement.value = parseFloat(precioElement.getAttribute("data-precio")) || 0;
        }
        if (cantidadElement.value.trim() === '') {
            cantidadElement.value = parseFloat(cantidadElement.getAttribute("data-cantidad")) || 1;
        }
    }
}

/**
 * Actualiza la descripción de un abono.
 * @param {string} id
 */
/**
 * Guarda la descripción del concepto (SOLO EN MEMORIA - SIN AJAX)
 * Se guardará cuando el usuario haga submit del formulario
 */
function guardarDescripcionConcepto(id) {
    var descripElement = document.getElementById('descripNueva');
    if (id !== 'idNuevo') {
        descripElement = document.getElementById('descrip' + id);
    }
    
    // Solo feedback visual, sin guardar en BD
    if (descripElement && descripElement.value.trim() !== '') {
        descripElement.style.borderColor = '#26c281';
        setTimeout(function() {
            descripElement.style.borderColor = '';
        }, 500);
    }
}

/**
 * Esta función pide confirmación al usuario antes de eliminar un itenm nuevo
 * @param {Array} dato 
 */
function deseaEliminarNuevoConcepto(dato) {

    // Obtener los elementos del DOM
    var concepto = document.getElementById('concepto');
    var conceptoContainer = document.getElementById('select2-concepto-container');
    var idConcepto = document.getElementById('idConcepto');

    var precioElement = document.getElementById('precioNuevo');
    var descripElement = document.getElementById('descripNueva');
    var cantidadElement = document.getElementById('cantidadNuevo');
    var subtotalElement = document.getElementById('subtotalNuevo');
    var idEliminarNuevo = document.getElementById('eliminarNuevo');

    if (dato.title !== 'Eliminar concepto') {
        var id = dato.title;

        var precioElement = document.getElementById('precio' + id);
        var descripElement = document.getElementById('descrip' + id);
        var cantidadElement = document.getElementById('cantidad' + id);
        var subtotalElement = document.getElementById('subtotal' + id);
        var idEliminarNuevo = document.getElementById('eliminar' + id);
    }

    var url = dato.name;

    Swal.fire({
        title: 'Desea eliminar?',
        text: "Al eliminar este registro es posible que se eliminen otros registros que estén relacionados. Desea continuar bajo su responsabilidad?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, deseo eliminar!',
        cancelButtonText: 'No',
        backdrop: `
            rgba(0,0,123,0.4)
            no-repeat
        `,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(url).then(function(response) {

                // Actualizar los elementos del DOM con los datos recibidos
                concepto.value = '';
                conceptoContainer.innerHTML = 'Seleccione una opción';

                idConcepto.innerHTML = '';

                precioElement.disabled = true;
                precioElement.value = 0;
                precioElement.dataset.precio = 0;

                descripElement.value = '';
                descripElement.disabled = true;

                cantidadElement.disabled = true;
                cantidadElement.value = 1;

                subtotalElement.innerHTML = '$0';

                idEliminarNuevo.innerHTML = '';

                $.toast({
                    heading: 'Acción realizada',
                    text: 'El registro fue eliminado correctamente.',
                    position: 'bottom-right',
                    showHideTransition: 'slide',
                    loaderBg: '#26c281',
                    icon: 'success',
                    hideAfter: 5000,
                    stack: 6
                });

            }).catch(function(error) {
                // handle error
                console.error(error);
            });            
        }else{
            return false;
        }
    })
}

function totalizarAbonos(){
    var tabla = document.getElementById('tablaItems');
    if (!tabla) {
        return; // Si no existe la tabla, salir
    }

    var totalNeto = 0;
    var totalAbonos = 0;
    var totalPorCobrar = 0;
    
    // Buscar todas las filas de facturas (excluyendo la fila de detalles expandida)
    for (let i = 1; i < tabla.rows.length; i++) {
        var fila = tabla.rows[i];
        
        // Saltar filas de detalles expandidas
        if (fila.classList.contains('factura-details-row')) {
            continue;
        }
        
        // La estructura de la tabla tiene:
        // cells[0]: botón expandir
        // cells[1]: Cod. Factura
        // cells[2]: Fecha
        // cells[3]: Total Neto (con data-total-neto)
        // cells[4]: Abonos (con data-abonos)
        // cells[5]: Por Cobrar (con data-por-cobrar)
        // cells[6]: Valor recibido
        
        var celdaTotalNeto = fila.cells[3];
        var celdaAbonos = fila.cells[4];
        var celdaPorCobrar = fila.cells[5];
        
        if (celdaTotalNeto) {
            var total = parseFloat(celdaTotalNeto.getAttribute('data-total-neto'));
            if (!isNaN(total)) {
                totalNeto = totalNeto + total;
            }
        }

        if (celdaAbonos) {
            var abonos = parseFloat(celdaAbonos.getAttribute('data-abonos'));
            if (isNaN(abonos)) {
                abonos = 0;
            }
            totalAbonos = totalAbonos + abonos;
        }

        if (celdaPorCobrar) {
            var porCobrar = parseFloat(celdaPorCobrar.getAttribute('data-por-cobrar'));
            if (!isNaN(porCobrar)) {
                totalPorCobrar = totalPorCobrar + porCobrar;
            }
        }
    }

    //TOTAL NETO
    var totalNetoFinal = "$"+numberFormat(totalNeto, 0, ',', '.');
    var elementTotalNeto = document.getElementById('totalNeto');
    elementTotalNeto.innerHTML = '';
    elementTotalNeto.appendChild(document.createTextNode(totalNetoFinal));

    //TOTAL ABONOS
    var totalAbonosFinal = "$"+numberFormat(totalAbonos, 0, ',', '.');
    var elementAbonos = document.getElementById('abonosNeto');
    elementAbonos.innerHTML = '';
    elementAbonos.appendChild(document.createTextNode(totalAbonosFinal));
    
    //TOTAL POR COBRAR
    var porCobrarNetoFinal = "$"+numberFormat(totalPorCobrar, 0, ',', '.');
    var elementPorCobrarNeto = document.getElementById('porCobrarNeto');
    elementPorCobrarNeto.innerHTML = '';
    elementPorCobrarNeto.appendChild(document.createTextNode(porCobrarNetoFinal));
}

/**
 * Actualiza los KPIs de la página principal
 */
function actualizarKPIs() {
    fetch('../directivo/ajax-calcular-kpis-movimientos.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar cada KPI con animación
            Object.keys(data.kpis).forEach(key => {
                const elemento = document.querySelector(`[data-kpi="${key}"]`);
                if (elemento) {
                    elemento.textContent = '$' + numberFormat(data.kpis[key], 0, ',', '.');
                }
            });
        }
    })
    .catch(error => {
        console.error('Error actualizando KPIs:', error);
    });
}

/**
 * Calcula y muestra el total neto, total abonos y total por cobrar
 * usando el método centralizado para garantizar consistencia con el KPI
 */
function totalizarMovimientos() {
    // Obtener parámetros de filtro de la URL actual
    var urlParams = new URLSearchParams(window.location.search);
    var filtros = {
        mostrarAnuladas: urlParams.get('mostrarAnuladas') === '1',
        tipo: urlParams.get('tipo') || null,
        usuario: urlParams.get('usuario') || null,
        desde: urlParams.get('desde') || null,
        hasta: urlParams.get('hasta') || null
    };

    // Construir URL del endpoint con los filtros
    var url = 'ajax-calcular-kpis-resumen.php?';
    var params = [];
    if (filtros.mostrarAnuladas) {
        params.push('mostrarAnuladas=1');
    }
    if (filtros.tipo) {
        params.push('tipo=' + encodeURIComponent(filtros.tipo));
    }
    if (filtros.usuario) {
        params.push('usuario=' + encodeURIComponent(filtros.usuario));
    }
    if (filtros.desde) {
        params.push('desde=' + encodeURIComponent(filtros.desde));
    }
    if (filtros.hasta) {
        params.push('hasta=' + encodeURIComponent(filtros.hasta));
    }
    url += params.join('&');

    // Llamar al endpoint centralizado
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.kpis) {
                var kpis = response.kpis;
                
                // Actualizar totales de ventas
                var totalNetoVenta = kpis.totalVentas || 0;
                var totalAbonosVenta = kpis.totalCobrado || 0;
                var totalPorCobrarVenta = kpis.totalPorCobrar || 0;
                
                // Actualizar totales de compras
                var totalNetoCompra = kpis.totalCompras || 0;
                // Para compras, por ahora solo tenemos el total neto (no hay separación de abonos/por pagar en el método centralizado)
                // Mantener la lógica anterior para compras por ahora
                var totalAbonosCompra = 0;
                var totalPorCobrarCompra = 0;

                // Si la tabla existe, calcular totales de compras desde las filas (temporal hasta que se agregue al método centralizado)
                var tabla = document.getElementById('tablaItems');
                if (tabla) {
                    for (let i = 1; i < tabla.rows.length; i++) {
                        var fila = tabla.rows[i];
                        if (fila.classList.contains('child')) {
                            continue;
                        }
                        var celdaTotal = fila.querySelector('td[data-total-neto]');
                        if (!celdaTotal || celdaTotal.getAttribute('data-anulado') == 1) { continue; }
                        if (celdaTotal.getAttribute('data-tipo') == 2) {
                            var total = parseFloat(celdaTotal.getAttribute('data-total-neto'));
                            var celdaAbonos = fila.querySelector('td[data-abonos]');
                            var celdaPorCobrar = fila.querySelector('td[data-por-cobrar]');
                            if (celdaAbonos && celdaPorCobrar) {
                                var abonos = parseFloat(celdaAbonos.getAttribute('data-abonos')) || 0;
                                var porCobrar = parseFloat(celdaPorCobrar.getAttribute('data-por-cobrar')) || 0;
                                totalAbonosCompra += abonos;
                                totalPorCobrarCompra += porCobrar;
                            }
                        }
                    }
                }

                // Actualizar resumen de ventas
                var elementTotalNetoVenta = document.getElementById('totalNetoVenta');
                if (elementTotalNetoVenta) {
                    elementTotalNetoVenta.innerHTML = "$" + numberFormat(totalNetoVenta, 0, ',', '.');
                }
                var elementAbonosVenta = document.getElementById('abonosNetoVenta');
                if (elementAbonosVenta) {
                    elementAbonosVenta.innerHTML = "$" + numberFormat(totalAbonosVenta, 0, ',', '.');
                }
                var elementPorCobrarNetoVenta = document.getElementById('porCobrarNetoVenta');
                if (elementPorCobrarNetoVenta) {
                    elementPorCobrarNetoVenta.innerHTML = "$" + numberFormat(totalPorCobrarVenta, 0, ',', '.');
                }

                // Actualizar resumen de compras
                var elementTotalNetoCompra = document.getElementById('totalNetoCompra');
                if (elementTotalNetoCompra) {
                    elementTotalNetoCompra.innerHTML = "$" + numberFormat(totalNetoCompra, 0, ',', '.');
                }
                var elementAbonosCompra = document.getElementById('abonosNetoCompra');
                if (elementAbonosCompra) {
                    elementAbonosCompra.innerHTML = "$" + numberFormat(totalAbonosCompra, 0, ',', '.');
                }
                var elementPorCobrarNetoCompra = document.getElementById('porCobrarNetoCompra');
                if (elementPorCobrarNetoCompra) {
                    elementPorCobrarNetoCompra.innerHTML = "$" + numberFormat(totalPorCobrarCompra, 0, ',', '.');
                }

                // Actualizar footer de la tabla
                var footerTotalVenta = document.getElementById('footerTotalVenta');
                if (footerTotalVenta) {
                    footerTotalVenta.innerHTML = "$" + numberFormat(totalNetoVenta, 0, ',', '.');
                }
                var footerCobradoVenta = document.getElementById('footerCobradoVenta');
                if (footerCobradoVenta) {
                    footerCobradoVenta.innerHTML = "$" + numberFormat(totalAbonosVenta, 0, ',', '.');
                }
                var footerPorCobrarVenta = document.getElementById('footerPorCobrarVenta');
                if (footerPorCobrarVenta) {
                    footerPorCobrarVenta.innerHTML = "$" + numberFormat(totalPorCobrarVenta, 0, ',', '.');
                }

                var footerTotalCompra = document.getElementById('footerTotalCompra');
                if (footerTotalCompra) {
                    footerTotalCompra.innerHTML = "$" + numberFormat(totalNetoCompra, 0, ',', '.');
                }
                var footerCobradoCompra = document.getElementById('footerCobradoCompra');
                if (footerCobradoCompra) {
                    footerCobradoCompra.innerHTML = "$" + numberFormat(totalAbonosCompra, 0, ',', '.');
                }
                var footerPorCobrarCompra = document.getElementById('footerPorCobrarCompra');
                if (footerPorCobrarCompra) {
                    footerPorCobrarCompra.innerHTML = "$" + numberFormat(totalPorCobrarCompra, 0, ',', '.');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al calcular KPIs:', error);
            // En caso de error, mantener valores en 0
        }
    });
}

/**
 * Esta funcion me calcula los totales de una factura
 * Considera items tipo crédito (C) que restan del total
 */
function totalizar(){
    var tabla = document.getElementById('tablaItems');
    if (!tabla) {
        return; // Si no existe la tabla, salir
    }

    // Variables según la lógica correcta
    var subtotalBruto = 0;  // 1. Suma de (precio × cantidad) de items débito ANTES de descuentos
    var descuentosItems = 0;  // 2. Sumatoria de descuentos línea por línea en items débito
    var descuentosComercialesGlobales = 0;  // 3. Suma de créditos ANTE_IMPUESTO
    var impuestos = 0;  // 5. Suma de IVAs sobre base gravable de cada item débito
    var anticiposSaldosFavor = 0;  // 7. Suma de créditos POST_IMPUESTO
    
    for (let i = 1; i < tabla.rows.length; i++) {
        var fila = tabla.rows[i];
        if (fila.cells.length === 9) {
            if (fila.classList.contains('fila-oculta')) {
                continue;
            }

            // Obtener el tipo de item (D= Débito, C= Crédito)
            var itemType = fila.getAttribute('data-item-type');
            if (!itemType && fila.cells[7]) {
                itemType = fila.cells[7].getAttribute('data-item-type');
            }
            if (!itemType) {
                itemType = 'D';
            }
            
            var isCredito = (itemType === 'C');
            var isDebito = !isCredito;
            
            // Para créditos, obtener application_time (por defecto ANTE_IMPUESTO)
            var applicationTime = null;
            if (isCredito) {
                applicationTime = fila.getAttribute('data-application-time');
                if (!applicationTime) {
                    applicationTime = 'ANTE_IMPUESTO';
                }
            }

            // Obtener elementos de la fila
            var precioInput = fila.cells[2] ? fila.cells[2].querySelector('input') : null;
            var descuentoInput = fila.cells[3] ? fila.cells[3].querySelector('input') : null;
            var cantidadInput = fila.cells[6] ? fila.cells[6].querySelector('input') : null;
            var selectImpuesto = fila.cells[4] ? fila.cells[4].querySelector('select') : null;
            var subtotalCell = fila.cells[7];
            
            if (!precioInput || !cantidadInput || !subtotalCell) {
                continue;
            }

            var precio = parseFloat(precioInput.value) || 0;
            var cantidad = parseFloat(cantidadInput.value) || 0;
            
            if (isDebito) {
                // 1. Subtotal Bruto: precio × cantidad (antes de descuentos)
                var precioPorCantidad = precio * cantidad;
                subtotalBruto += precioPorCantidad;
                
                // 2. Descuentos de Items: descuento línea por línea
                if (descuentoInput) {
                    var porcentajeDescuento = parseFloat(descuentoInput.value) || 0;
                    var descuentoLinea = precioPorCantidad * (porcentajeDescuento / 100);
                    descuentosItems += descuentoLinea;
                    
                    // 5. Impuestos: sobre base gravable del item (después de descuento)
                    if (selectImpuesto) {
                        var opcionSeleccionada = selectImpuesto.selectedOptions[0];
                        var impuestoValue = opcionSeleccionada ? opcionSeleccionada.value : 0;
                        var impuestoValor = opcionSeleccionada ? (parseFloat(opcionSeleccionada.getAttribute('data-valor-impuesto')) || 0) : 0;
                        
                        if (impuestoValue > 0 && impuestoValor > 0) {
                            var baseGravableItem = precioPorCantidad - descuentoLinea;
                            var impuestoItem = baseGravableItem * (impuestoValor / 100);
                            impuestos += impuestoItem;
                        }
                    }
                }
            } else if (isCredito) {
                // Obtener el subtotal del crédito
                var subtotalValue = 0;
                if (subtotalCell.getAttribute('data-subtotal-anterior')) {
                    subtotalValue = Math.abs(parseFloat(subtotalCell.getAttribute('data-subtotal-anterior')) || 0);
                } else {
                    var subtotalText = subtotalCell.textContent.trim();
                    subtotalText = subtotalText.replace(/^\$?\s*-?/, '').replace(/\./g, '').replace(',', '.');
                    subtotalValue = Math.abs(parseFloat(subtotalText) || 0);
                }
                
                // 3. Descuentos Comerciales Globales (ANTE_IMPUESTO) o 7. Anticipos (POST_IMPUESTO)
                if (applicationTime === 'ANTE_IMPUESTO') {
                    descuentosComercialesGlobales += subtotalValue;
                } else {
                    anticiposSaldosFavor += subtotalValue;
                }
            }
        }
    }

    // 4. Subtotal Gravable: Subtotal Bruto - Descuentos Items - Descuentos Comerciales Globales
    var subtotalGrabable = subtotalBruto - descuentosItems - descuentosComercialesGlobales;
    
    // 6. Total Facturado: Subtotal Gravable + Impuestos
    var totalFacturado = subtotalGrabable + impuestos;
    
    // VALOR ADICIONAL
    var vlrAdicional = parseFloat(document.getElementById('vlrAdicional') ? document.getElementById('vlrAdicional').value : 0) || 0;
    
    // 8. Total Neto: Total Facturado - Anticipos + Valor Adicional
    var totalNeto = totalFacturado - anticiposSaldosFavor + vlrAdicional;

    // Actualizar elementos del DOM según el orden del plan
    // 1. SUBTOTAL BRUTO
    var subtotalBrutoFinal = "$"+numberFormat(subtotalBruto, 0, ',', '.');
    var idSubtotalBruto = document.getElementById('subtotalBruto');
    if (idSubtotalBruto) {
        idSubtotalBruto.innerHTML = '';
        idSubtotalBruto.appendChild(document.createTextNode(subtotalBrutoFinal));
    }

    // 2. DESCUENTOS DE ÍTEMS (solo de items débito)
    var negativo = descuentosItems === 0 ? '' : '-';
    var descuentoFinal = negativo+"$"+numberFormat(descuentosItems, 0, ',', '.');
    var idDescuento = document.getElementById('valorDescuento');
    if (idDescuento) {
        idDescuento.innerHTML = '';
        idDescuento.appendChild(document.createTextNode(descuentoFinal));
    }

    // 3. DESCUENTOS COMERCIALES GLOBALES (créditos ANTE_IMPUESTO)
    var negativoComerciales = descuentosComercialesGlobales === 0 ? '' : '-';
    var descuentosComercialesFinal = negativoComerciales+"$"+numberFormat(descuentosComercialesGlobales, 0, ',', '.');
    var idDescuentosComerciales = document.getElementById('descuentosComerciales');
    if (idDescuentosComerciales) {
        idDescuentosComerciales.innerHTML = '';
        idDescuentosComerciales.appendChild(document.createTextNode(descuentosComercialesFinal));
    }

    // 4. SUBTOTAL GRABABLE
    var subtotalGrabableFinal = "$"+numberFormat(subtotalGrabable, 0, ',', '.');
    var idSubtotal = document.getElementById('subtotal');
    if (idSubtotal) {
        idSubtotal.innerHTML = '';
        idSubtotal.appendChild(document.createTextNode(subtotalGrabableFinal));
    }

    // 5. IMPUESTOS
    var impuestoFinal = "$"+numberFormat(impuestos, 0, ',', '.');
    var idImpuesto = document.getElementById('valorImpuesto');
    if (idImpuesto) {
        idImpuesto.innerHTML = '';
        idImpuesto.appendChild(document.createTextNode(impuestoFinal));
    }

    // 6. TOTAL FACTURADO
    var totalFacturadoFinal = "$"+numberFormat(totalFacturado, 0, ',', '.');
    var idTotalFacturado = document.getElementById('totalFacturado');
    if (idTotalFacturado) {
        idTotalFacturado.innerHTML = '';
        idTotalFacturado.appendChild(document.createTextNode(totalFacturadoFinal));
    }

    // 7. ANTICIPOS O SALDOS A FAVOR (solo POST_IMPUESTO)
    var anticiposFinal = anticiposSaldosFavor > 0 ? "-$"+numberFormat(anticiposSaldosFavor, 0, ',', '.') : "$0";
    var idCreditos = document.getElementById('valorCreditos');
    if (idCreditos) {
        idCreditos.innerHTML = '';
        idCreditos.appendChild(document.createTextNode(anticiposFinal));
        idCreditos.style.color = anticiposSaldosFavor > 0 ? '#ff5722' : '';
    }

    // VALOR ADICIONAL
    var vlrAdicionalFinal = "$"+numberFormat(vlrAdicional, 0, ',', '.');
    var idValorAdicional = document.getElementById('valorAdicional');
    if (idValorAdicional) {
        idValorAdicional.innerHTML = '';
        idValorAdicional.appendChild(document.createTextNode(vlrAdicionalFinal));
    }
    
    // 8. TOTAL NETO A PAGAR
    var totalNetoFinal = "$"+numberFormat(totalNeto, 0, ',', '.');
    var idTotalNeto = document.getElementById('totalNeto');
    if (idTotalNeto) {
        idTotalNeto.innerHTML = '';
        idTotalNeto.appendChild(document.createTextNode(totalNetoFinal));
    }
}