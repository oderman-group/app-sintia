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
    if(id !== 'idNuevo'){
        var idItem=id
        // Obtener los elementos
        var precioElement = document.getElementById('precio'+id);
        var cantidadElement = document.getElementById('cantidadItems'+id);
        var subtotalElement = document.getElementById('subtotal'+id);
        var descuentoElement = document.getElementById('descuento'+id);
        var impuestoElement = document.getElementById('impuesto'+id);
    }
    
    var regex = /^[0-9]+(\.[0-9]+)?$/;

    if ((precioElement.value.trim() !== '' && cantidadElement.value.trim() !== '' && descuentoElement.value.trim() !== '' && regex.test(precioElement.value) && regex.test(cantidadElement.value) && regex.test(descuentoElement.value))) {

        // Obtener los valores
        var precio = parseFloat(precioElement.value);
        var cantidad = parseFloat(cantidadElement.value);
        var porcentajeDescuento= parseFloat(descuentoElement.value);
        var impuesto= parseFloat(impuestoElement.value);

        // Calcular el subtotal
        var vlrDescuento = precio * (porcentajeDescuento / 100);
        var vlrDescuentoAnterior = vlrDescuento * cantidad;

        var subtotal = (precio-vlrDescuento) * cantidad;
        var subtotalFormat = "$"+numberFormat(subtotal, 0, ',', '.');
        
        fetch('../directivo/ajax-cambiar-subtotal.php?subtotal='+(subtotal)+'&cantidad='+(cantidad)+'&precio='+(precio)+'&idItem='+(idItem)+'&porcentajeDescuento='+(porcentajeDescuento)+'&impuesto='+(impuesto), {
            method: 'GET'
        })
        .then(response => response.text()) // Convertir la respuesta a texto
        .then(data => {
            precioElement.dataset.precio = precio;
            cantidadElement.dataset.cantidad = cantidad;
            descuentoElement.dataset.descuentoAnterior = porcentajeDescuento;

            subtotalElement.innerHTML = '';
            subtotalElement.appendChild(document.createTextNode(subtotalFormat));
            subtotalElement.dataset.subtotalAnterior = subtotal;

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

        Swal.fire({
            title: 'Campo Vacío',
            text: "Los campos de precio, descuento y cantidad no pueden ir vacío, o con letras",
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
            var descuentoAnterior = parseFloat(descuentoElement.getAttribute("data-descuento-anterior"));
            
            precioElement.value = precioAnterior;
            cantidadElement.value = cantidadAnterior;
            descuentoElement.value = descuentoAnterior;
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
    .then(response => response.json()) // Convertir la respuesta a objeto JSON
    .then(data => {
        // Actualizar los elementos del DOM con los datos recibidos
        itemElement.innerHTML = '';
        itemElement.appendChild(document.createTextNode(data.idInsercion));

        precioElement.disabled = false;
        precioElement.value = precio;
        precioElement.dataset.precio = precio;
        precioElement.dataset.precioAnterior = precio;

        descripElement.disabled = false;
        cantidadElement.disabled = false;

        subtotalElement.innerHTML = '';
        subtotalElement.appendChild(document.createTextNode(subtotalFormat));
        subtotalElement.dataset.subtotalAnterior = subtotal;

        descuentoElement.disabled = false;
        
        impuestoElement.disabled = false;

        if (data.creado == 0) {
            descuentoElement.value = 0;

            impuestoElement.value = 0;
            impuestoContainer.innerHTML = 'Ninguno - (0%)';
        }

        var html='<a href="#" title="Eliminar item nuevo" name="movimientos-items-eliminar.php?idR='+data.idInsercion+'" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" data-toggle="tooltip" onClick="deseaEliminarNuevoItem(this)" data-placement="right">X</a>';
        idEliminarNuevo.innerHTML = html;

        totalizar();

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
	}
}

/**
 * Actualiza o guarda lo abonado a una factura6
 * @param {array} datos
 */
function actualizarAbonado(datos) {
    var abono       = datos.value;

    if (abono.trim() !== '') {
        var nuevoAbono  = parseFloat(datos.value);
        var idAbono     = datos.getAttribute("data-id-abono");
        var idFactura   = datos.getAttribute("data-id-factura");
        var abonoAnterior   = parseFloat(datos.getAttribute("data-abono-anterior"));
        
        fetch('../directivo/ajax-guardar-abono.php?type=INVOICE&abono='+(nuevoAbono)+'&idAbono='+(idAbono)+'&idFactura='+(idFactura)+'&abonoAnterior='+(abonoAnterior), {
            method: 'GET'
        })
        .then(response => response.text()) // Convertir la respuesta a texto
        .then(data => {
            var elementTotalNeto    = document.getElementById("totalNeto"+idFactura);
            var elementAbono        = document.getElementById("abonos"+idFactura);
            var elementPorCobrar    = document.getElementById("porCobrar"+idFactura);
            
            var totalNeto           = parseFloat(elementTotalNeto.getAttribute("data-total-neto"));
            var totalAbonos         = elementAbono.getAttribute("data-abonos");

            var totalAbono          = (totalAbonos - abonoAnterior) + nuevoAbono;
            var totalAbonoFinal     = "$"+numberFormat(totalAbono, 0, ',', '.');

            var porCobrar           = totalNeto - totalAbono;
            var porCobrarFinal      = "$"+numberFormat(porCobrar, 0, ',', '.');

            elementAbono.innerHTML = '';
            elementAbono.appendChild(document.createTextNode(totalAbonoFinal));
            elementAbono.dataset.abonos = totalAbono;

            elementPorCobrar.innerHTML = '';
            elementPorCobrar.appendChild(document.createTextNode(porCobrarFinal));
            elementPorCobrar.dataset.porCobrar = porCobrar;

            if (porCobrar < 1) {
                cambiarEstadoFactura(idFactura, 1);
            } else if (porCobrar > 0) {
                cambiarEstadoFactura(idFactura, 2);
            }

            datos.dataset.abonoAnterior = nuevoAbono;

            totalizarAbonos()

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
            datos.value = 0;
            console.error('Error:', error);
        });

    } else {

        Swal.fire({
            title: 'Campo Vacío',
            text: "Los campos valor recibido no pueden ir vacío",
            icon: 'warning',
            showCancelButton: false,
            confirmButtonText: 'Ok',
            backdrop: `
                rgba(0,0,123,0.4)
                no-repeat
            `,
        }).then((result) => {
            datos.value = 0;
        })

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
function guardarNuevoConcepto(selectElement) {
    var concepto = selectElement.value;
    var idAbono = document.getElementById('idAbono').value;
    var precioElement = document.getElementById('precioNuevo');
    var descripElement = document.getElementById('descripNueva');
    var cantidadElement = document.getElementById('cantidadNuevo');
    var conceptoElement = document.getElementById('idConcepto');

    var conceptoModificar = '';
    if (conceptoElement.innerHTML.trim() !== '') {
        var conceptoModificar = conceptoElement.innerHTML;
    }

    fetch('../directivo/ajax-guardar-abono.php?type=ACCOUNT&conceptoModificar='+conceptoModificar+'&idAbono=' + idAbono + '&concepto=' + concepto + '&precio=0&cantidad=1&subtotal=0', {
        method: 'GET'
    })
    .then(response => response.json()) // Convertir la respuesta a objeto JSON
    .then(data => {
        conceptoElement.innerHTML = '';
        conceptoElement.appendChild(document.createTextNode(data.idInsercion));

        precioElement.disabled = false;
        descripElement.disabled = false;
        cantidadElement.disabled = false;

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
    });
}

/**
 * Actualiza el subtotal según el precio y la cantidad especificados.
 * @param {string} id
 */
function actualizarSubtotalConceptos(id) {
    var idConcepto=document.getElementById('idConcepto').innerText;
    // Obtener los elementos
    var precioElement = document.getElementById('precioNuevo');
    var cantidadElement = document.getElementById('cantidadNuevo');
    var subtotalElement = document.getElementById('subtotalNuevo');
    if(id !== 'idNuevo'){
        var idConcepto=id
        // Obtener los elementos
        var precioElement = document.getElementById('precio'+id);
        var cantidadElement = document.getElementById('cantidad'+id);
        var subtotalElement = document.getElementById('subtotal'+id);
    }

    if (precioElement.value.trim() !== '' && cantidadElement.value.trim() !== '') {

        // Obtener los valores
        var precio = parseFloat(precioElement.value);
        var cantidad = parseFloat(cantidadElement.value);

        // Calcular el subtotal
        var subtotal = precio * cantidad;
        var subtotalFormat = "$"+numberFormat(subtotal, 0, ',', '.');
        
        fetch('../directivo/ajax-cambiar-subtotal-concepto.php?subtotal='+(subtotal)+'&cantidad='+(cantidad)+'&precio='+(precio)+'&idConcepto='+(idConcepto), {
            method: 'GET'
        })
        .then(response => response.text()) // Convertir la respuesta a texto
        .then(data => {
            precioElement.dataset.precio = precio;

            subtotalElement.innerHTML = '';
            subtotalElement.appendChild(document.createTextNode(subtotalFormat));

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

        Swal.fire({
            title: 'Campo Vacío',
            text: "Los campos de precio y cantidad no pueden ir vacío",
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
            
            precioElement.value = precioAnterior;
            cantidadElement.value = cantidadAnterior;
        })

    }
}

/**
 * Actualiza la descripción de un abono.
 * @param {string} id
 */
function guardarDescripcionConcepto(id) {
    var idConcepto=document.getElementById('idConcepto').innerText;
    // Obtener los elementos
    var descripElement = document.getElementById('descripNueva');
    if(id !== 'idNuevo'){
        var idConcepto=id
        // Obtener los elementos
        var descripElement = document.getElementById('descrip'+id);
    }
    var descripcion = descripElement.value;
    
    fetch('../directivo/ajax-guardar-descripcion-concepto.php?descripcion='+(descripcion)+'&idConcepto='+(idConcepto), {
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

    var totalNeto = 0;
    var totalAbonos = 0;
    var totalPorCobrar = 0;
    for (let i = 1; i < tabla.rows.length; i++) {
        var fila = tabla.rows[i];

        var total = parseFloat(fila.cells[1].getAttribute('data-total-neto'));
        totalNeto = totalNeto + total;

        var abonos = parseFloat(fila.cells[2].getAttribute('data-abonos'));
        if (isNaN(abonos)) {
            var abonos = 0;
        }
        totalAbonos = totalAbonos + abonos;

        var porCobrar = parseFloat(fila.cells[3].getAttribute('data-por-cobrar'));
        totalPorCobrar = totalPorCobrar + porCobrar;
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
 * Calcula y muestra el total neto, total abonos y total por cobrar
 * basado en los valores de la tabla 'tablaItems'.
 */
function totalizarMovimientos() {
    // Obtener el elemento de la tabla por su ID
    var tabla = document.getElementById('tablaItems');

    // Inicializar variables para almacenar valores totales
    var totalNeto = 0;
    var totalAbonos = 0;
    var totalPorCobrar = 0;

    // Iterar a través de las filas de la tabla, comenzando desde el índice 1
    for (let i = 1; i < tabla.rows.length; i++) {
        // Obtener la fila actual
        var fila = tabla.rows[i];

        // Obtener y acumular el valor neto total del atributo de datos
        var total = parseFloat(fila.cells[4].getAttribute('data-total-neto'));
        totalNeto = totalNeto + total;

        // Obtenga y acumule el valor total de abonos del atributo de datos
        var abonos = parseFloat(fila.cells[5].getAttribute('data-abonos'));
        // Validar si abonos es un número válido, establecer en 0 si NaN
        if (isNaN(abonos)) {
            abonos = 0;
        }
        totalAbonos = totalAbonos + abonos;

        // Obtener y acumular el valor total por cobrar del atributo de datos
        var porCobrar = parseFloat(fila.cells[6].getAttribute('data-por-cobrar'));
        totalPorCobrar = totalPorCobrar + porCobrar;
    }

    // Actualiza los elementos DOM con los valores calculados.

    // Actualiza total neto
    var totalNetoFinal = "$" + numberFormat(totalNeto, 0, ',', '.');
    var elementTotalNeto = document.getElementById('totalNeto');
    elementTotalNeto.innerHTML = '';
    elementTotalNeto.appendChild(document.createTextNode(totalNetoFinal));

    // Actualiza total abonos
    var totalAbonosFinal = "$" + numberFormat(totalAbonos, 0, ',', '.');
    var elementAbonos = document.getElementById('abonosNeto');
    elementAbonos.innerHTML = '';
    elementAbonos.appendChild(document.createTextNode(totalAbonosFinal));

    // Actualiza total por cobrar
    var porCobrarNetoFinal = "$" + numberFormat(totalPorCobrar, 0, ',', '.');
    var elementPorCobrarNeto = document.getElementById('porCobrarNeto');
    elementPorCobrarNeto.innerHTML = '';
    elementPorCobrarNeto.appendChild(document.createTextNode(porCobrarNetoFinal));
}

/**
 * Esta funcion me calcula los totales de una factura
 */
function totalizar(){
    var tabla = document.getElementById('tablaItems');

    var totalPrecio = 0;
    var totalDescuento = 0;
    var totalImpuesto = 0;
    for (let i = 1; i < tabla.rows.length; i++) {
        var fila = tabla.rows[i];
        if (fila.cells.length === 9) {
            if (fila.classList.contains('fila-oculta')) {
                continue;
            }

            var precio = parseFloat(fila.cells[2].querySelector('input').value);
            var porcentajeDescuento = parseFloat(fila.cells[3].querySelector('input').value);
            var cantidad = parseFloat(fila.cells[6].querySelector('input').value);
            var selectImpuesto = fila.cells[4].querySelector('select');
            var opcionSeleccionada = selectImpuesto.selectedOptions[0];
            var impuestoValue = opcionSeleccionada.value;
            var impuestoValor = parseFloat(opcionSeleccionada.getAttribute('data-valor-impuesto'));
            var impuestoName = opcionSeleccionada.getAttribute('data-name-impuesto');

            var precioNeto = (precio * cantidad);
            var totalPrecio = totalPrecio + precioNeto;

            var descuento = precioNeto * (porcentajeDescuento / 100)
            var totalDescuento = totalDescuento + descuento;

            if (impuestoValue > 0) {
                var impuesto = (precioNeto - descuento) * (impuestoValor / 100);
                var totalImpuesto = totalImpuesto + impuesto;
            }
        }
    }

    //SUBTOTAL NETO
    var totalPrecioFinal = "$"+numberFormat(totalPrecio, 0, ',', '.');
    var idSubtotal = document.getElementById('subtotal');
    idSubtotal.innerHTML = '';
    idSubtotal.appendChild(document.createTextNode(totalPrecioFinal));

    //VALOR ADICIONAL
    var vlrAdicional = parseFloat(document.getElementById('vlrAdicional').value);
    var vlrAdicionalFinal = "$"+numberFormat(vlrAdicional, 0, ',', '.');
    var idValorAdicional = document.getElementById('valorAdicional');
    idValorAdicional.innerHTML = '';
    idValorAdicional.appendChild(document.createTextNode(vlrAdicionalFinal));

    //TOTAL DESCUENTO
    var negativo = totalDescuento === 0 ? '' : '-';
    var descuentoFinal = negativo+"$"+numberFormat(totalDescuento, 0, ',', '.');
    var idDescuento = document.getElementById('valorDescuento');
    idDescuento.innerHTML = '';
    idDescuento.appendChild(document.createTextNode(descuentoFinal));

    //IMPUESTOS
    var impuestoFinal = "$"+numberFormat(totalImpuesto, 0, ',', '.');
    var idImpuesto = document.getElementById('valorImpuesto');
    idImpuesto.innerHTML = '';
    idImpuesto.appendChild(document.createTextNode(impuestoFinal));
    
    //TOTAL NETO
    var totalNeto = ((totalPrecio + vlrAdicional) - totalDescuento) + totalImpuesto;
    var totalNetoFinal = "$"+numberFormat(totalNeto, 0, ',', '.');
    var idTotalNeto = document.getElementById('totalNeto');
    idTotalNeto.innerHTML = '';
    idTotalNeto.appendChild(document.createTextNode(totalNetoFinal));
}