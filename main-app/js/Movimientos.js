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
    if(id !== 'idNuevo'){
        var idItem=id
        // Obtener los elementos
        var precioElement = document.getElementById('precio'+id);
        var cantidadElement = document.getElementById('cantidadItems'+id);
        var subtotalElement = document.getElementById('subtotal'+id);
    }

    if (precioElement.value.trim() !== '' && cantidadElement.value.trim() !== '') {

        var idSubtotal = document.getElementById('subtotal');
        var idTotalNeto = document.getElementById('totalNeto');

        // Obtener los valores
        var precio = parseFloat(precioElement.value);
        var cantidad = parseFloat(cantidadElement.value);
        var subtotalAnterior = parseFloat(subtotalElement.getAttribute("data-subtotal-anterior"));
        var subtotalNeto = parseFloat(idSubtotal.getAttribute("data-subtotal"));
        var total = parseFloat(idTotalNeto.getAttribute("data-total-neto"));

        // Calcular el subtotal
        var subtotal = precio * cantidad;
        var subtotalFormat = "$"+numberFormat(subtotal, 0, ',', '.');

        var subtotalNetoFinal= (subtotalNeto-subtotalAnterior)+subtotal;
        var subtotalNetoFormat = "$"+numberFormat(subtotalNetoFinal, 0, ',', '.');

        var totalNetoFinal= (total-subtotalAnterior)+subtotal;
        var totalFormat = "$"+numberFormat(totalNetoFinal, 0, ',', '.');
        
        fetch('../directivo/ajax-cambiar-subtotal.php?subtotal='+(subtotal)+'&cantidad='+(cantidad)+'&precio='+(precio)+'&idItem='+(idItem), {
            method: 'GET'
        })
        .then(response => response.text()) // Convertir la respuesta a texto
        .then(data => {
            precioElement.dataset.precio = precio;

            subtotalElement.innerHTML = '';
            subtotalElement.appendChild(document.createTextNode(subtotalFormat));
            subtotalElement.dataset.subtotalAnterior = subtotal;

            idSubtotal.innerHTML = '';
            idSubtotal.appendChild(document.createTextNode(subtotalNetoFormat));
            idSubtotal.dataset.subtotal = subtotalNetoFinal;
            idSubtotal.dataset.subtotalAnteriorSub = subtotalNetoFinal;

            idTotalNeto.innerHTML = '';
            idTotalNeto.appendChild(document.createTextNode(totalFormat));
            idTotalNeto.dataset.totalNeto = totalNetoFinal;
            idTotalNeto.dataset.totalNetoAnterior = totalNetoFinal;

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
 * Trae y actualiza la lista de items asociados a una transacción.
 */
function traerItems(){
    // Obtener el valor del ID de transacción desde el elemento HTML
    var idTransaction = document.getElementById('idTransaction').value;
    var vlrAdicional = document.getElementById('vlrAdicional').value;
    var typeTransaction = document.getElementById('typeTransaction').value;

    // Mostrar un mensaje de carga mientras se obtienen los items
    $('#mostrarItems').empty().hide().html("Cargando Items...").show(1);
    
    // Realizar una solicitud fetch para obtener los items asociados a la transacción
    fetch('../directivo/ajax-traer-items.php?idTransaction=' + idTransaction + '&vlrAdicional=' + vlrAdicional + '&typeTransaction=' + typeTransaction, {
        method: 'GET'
    })
    .then(response => response.text()) // Convertir la respuesta a texto
    .then(data => {
        // Actualizar el contenido de 'mostrarItems' con la respuesta obtenida
        $('#mostrarItems').empty().hide().html(data).show(1);

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
    var descripElement = document.getElementById('descripNueva');
    var cantidadElement = document.getElementById('cantidadItemNuevo');
    var subtotalElement = document.getElementById('subtotalNuevo');
    var idSubtotal = document.getElementById('subtotal');
    var idTotalNeto = document.getElementById('totalNeto');
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
    var subtotalNeto = parseFloat(idSubtotal.getAttribute("data-subtotal-anterior-sub"));
    var total = parseFloat(idTotalNeto.getAttribute("data-total-neto-anterior"));

    var subtotal = precio * cantidad;
    var subtotalFormat = "$"+numberFormat(subtotal, 0, ',', '.');

    var subtotalNetoFinal= subtotalNeto+subtotal;
    var subtotalNetoFormat = "$"+numberFormat(subtotalNetoFinal, 0, ',', '.');

    var totalNetoFinal= total+subtotal;
    var totalFormat = "$"+numberFormat(totalNetoFinal, 0, ',', '.');

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

        descripElement.disabled = false;
        cantidadElement.disabled = false;

        subtotalElement.innerHTML = '';
        subtotalElement.appendChild(document.createTextNode(subtotalFormat));
        subtotalElement.dataset.subtotalAnterior = subtotal;

        var html='<a href="#" title="Eliminar item nuevo" name="movimientos-items-eliminar.php?idR='+data.idInsercion+'" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" data-toggle="tooltip" onClick="deseaEliminarNuevoItem(this)" data-placement="right">X</a>';
        idEliminarNuevo.innerHTML = html;

        idSubtotal.innerHTML = '';
        idSubtotal.appendChild(document.createTextNode(subtotalNetoFormat));
        idSubtotal.dataset.subtotal = subtotalNetoFinal;

        idTotalNeto.innerHTML = '';
        idTotalNeto.appendChild(document.createTextNode(totalFormat));
        idTotalNeto.dataset.totalNeto = totalNetoFinal;

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
    var subtotalNuevo = document.getElementById('subtotalNuevo');
    var items = document.getElementById('items');
    var itemsContainer = document.getElementById('select2-items-container');
    var idEliminarNuevo = document.getElementById('eliminarNuevo');

    // Limpiar y reiniciar los elementos del DOM relacionados con el nuevo item
    idItemNuevo.innerHTML = '';
    precioNuevo.value = 0;
    precioNuevo.dataset.precio = 0;
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
}

/**
 * Realiza la acción de añadir o modificar el valor adiconal.
 * Limpia y actualiza los elementos relacionados con la información de valor adicional.
 */
function cambiarAdiconal(data) {

    var idValorAdicional = document.getElementById('valorAdicional');
    var idTotalNeto = document.getElementById('totalNeto');

    var vlrAdicional= parseFloat(data.value);
    var vlrAdicionalAnteriorValor= parseFloat(data.getAttribute('data-vlr-adicional-anterior'));
    var totalNeto= parseFloat(idTotalNeto.getAttribute('data-total-neto'));
    
    if (data.value.trim() !== '') {
        console.log('Entro aqui');

        var total= (totalNeto-vlrAdicionalAnteriorValor)+vlrAdicional;

        var vlrAdicionalFinal = "$"+numberFormat(vlrAdicional, 0, ',', '.');
        var totalFinal = "$"+numberFormat(total, 0, ',', '.');

        // Limpiar y reiniciar los elementos del DOM relacionados con el nuevo item
        idValorAdicional.innerHTML = '';
        idValorAdicional.appendChild(document.createTextNode(vlrAdicionalFinal));
        idValorAdicional.dataset.valorAdicional = vlrAdicional;
        data.dataset.vlrAdicionalAnterior = vlrAdicional;
        
        idTotalNeto.innerHTML = '';
        idTotalNeto.appendChild(document.createTextNode(totalFinal));
        idTotalNeto.dataset.totalNeto = total;
        idTotalNeto.dataset.totalNetoAnterior = total;

    } else {

        Swal.fire({
            title: 'Campo Vacío',
            text: "El campo de valor adicional no puede ir vacío",
            icon: 'warning',
            showCancelButton: false,
            confirmButtonText: 'Ok',
            backdrop: `
                rgba(0,0,123,0.4)
                no-repeat
            `,
        }).then((result) => {
            data.dataset.vlrAdicionalAnterior = vlrAdicionalAnteriorValor;
            data.value = vlrAdicionalAnteriorValor;
        })
        
    }
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
    var idSubtotal = document.getElementById('subtotal');
    var idTotalNeto = document.getElementById('totalNeto');
    var idEliminarNuevo = document.getElementById('eliminarNuevo');

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

                        registro.classList.add('animate__animated', 'animate__bounceOutRight', 'animate__delay-0.5s');
                        if (varObjet.restar !== undefined) {
                            var restar              =  varObjet.restar;
                    
                            var subtotalNeto        = parseFloat(idSubtotal.getAttribute("data-subtotal"));
                            var subtotal            = subtotalNeto-restar;
                            var subtotalFinal       = "$"+numberFormat(subtotal, 0, ',', '.');
                    
                            var totalNeto           = parseFloat(idTotalNeto.getAttribute("data-total-neto"));
                            var total               = totalNeto-restar;
                            var totalFinal          = "$"+numberFormat(total, 0, ',', '.');
                            
                            idSubtotal.innerHTML = '';
                            idSubtotal.appendChild(document.createTextNode(subtotalFinal));
                            idSubtotal.dataset.subtotal = subtotal;
                            idSubtotal.dataset.subtotalAnteriorSub = subtotal;
                            
                            idTotalNeto.innerHTML = '';
                            idTotalNeto.appendChild(document.createTextNode(totalFinal));
                            idTotalNeto.dataset.totalNeto = total;
                            idTotalNeto.dataset.totalNetoAnterior = total;
                        }

                } else {

                    // Obtener el ID del item, el precio y calcular el subtotal
                    var restar = parseFloat(subtotalElement.getAttribute('data-subtotal-anterior'));
                    var subtotalNeto = parseFloat(idSubtotal.getAttribute("data-subtotal"));
                    var total = parseFloat(idTotalNeto.getAttribute("data-total-neto"));
                
                    var subtotalNetoFinal= subtotalNeto-restar;
                    var subtotalNetoFormat = "$"+numberFormat(subtotalNetoFinal, 0, ',', '.');
                
                    var totalNetoFinal= total-restar;
                    var totalFormat = "$"+numberFormat(totalNetoFinal, 0, ',', '.');

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

                    idSubtotal.innerHTML = '';
                    idSubtotal.appendChild(document.createTextNode(subtotalNetoFormat));
                    idSubtotal.dataset.subtotal = subtotalNetoFinal;
                    idSubtotal.dataset.subtotalAnteriorSub = subtotalNetoFinal;

                    idTotalNeto.innerHTML = '';
                    idTotalNeto.appendChild(document.createTextNode(totalFormat));
                    idTotalNeto.dataset.totalNeto = totalNetoFinal;
                    idTotalNeto.dataset.totalNetoAnterior = totalNetoFinal;
                }

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
        
        fetch('../directivo/ajax-guardar-abono.php?abono='+(nuevoAbono)+'&idAbono='+(idAbono)+'&idFactura='+(idFactura)+'&abonoAnterior='+(abonoAnterior), {
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
                cambiarEstadoFactura(idFactura);
            }

            datos.dataset.abonoAnterior = nuevoAbono;

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
function cambiarEstadoFactura(idFactura) {
    
    var registro = document.getElementById("reg" + idFactura);
        
    fetch('../directivo/ajax-cambiar-estado-factura.php?idFactura='+(idFactura), {
        method: 'GET'
    })
    .then(response => response.text()) // Convertir la respuesta a texto
    .then(data => {

        async function miFuncionConDelay() {
            await new Promise(resolve => setTimeout(resolve, 1000));
            registro.style.display = "none";
        }

        miFuncionConDelay();

        registro.classList.add('animate__animated', 'animate__bounceOutRight', 'animate__delay-0.5s');

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
    })
    .catch(error => {
        console.error('Error:', error);
    });
}