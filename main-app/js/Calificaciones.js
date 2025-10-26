/**
 * Esta función me guarda una definitiva
 * @param {array} enviada 
 * @returns 
 */
function def(enviada){
    var split = enviada.name.split('_');
    var nota = enviada.value;
    var codEst = enviada.id;
    var per = split[0];
    var notaAnterior = enviada.alt;
    var carga = split[1];

    var casilla = document.getElementById(codEst);

    if (alertValidarNota(nota)) {
        return false;
    }

    casilla.disabled="disabled";
    casilla.style.fontWeight="bold";

    $('#respRP').empty().hide().html("esperando...").show(1);
    datos = "nota="+(nota)+
                "&per="+(per)+
                "&codEst="+(codEst)+
                "&notaAnterior="+(notaAnterior)+
                "&carga="+(carga);
            $.ajax({
                type: "POST",
                url: "ajax-periodos-registrar.php",
                data: datos,
                success: function(data){
                    $('#respRP').empty().hide().html(data).show(1);
                    
                    // ✅ ACTUALIZAR LA NOTA ANTERIOR: Guardar la nota que acabamos de colocar
                    // para que la próxima vez que se cambie sin recargar, tengamos el valor correcto
                    enviada.alt = nota;
                }
            });

}

/**
 * Esta función me guarda una nivelacion
 * @param {array} enviada 
 * @returns 
 */
function niv(enviada){
    var split = enviada.name.split('_');
    var nota = enviada.value;
    var codEst = enviada.id;
    var per = split[0];
    var carga = split[1];

    var casilla = document.getElementById(codEst);

    if (alertValidarNota(nota)) {
        return false;
    }

    casilla.disabled="disabled";
    casilla.style.fontWeight="bold";

    $('#respRP').empty().hide().html("esperando...").show(1);
    datos = "nota="+(nota)+
                "&per="+(per)+
                "&codEst="+(codEst)+
                "&carga="+(carga);
            $.ajax({
                type: "POST",
                url: "ajax-nivelaciones-registrar.php",
                data: datos,
                success: function(data){
                $('#respRP').empty().hide().html(data).show(1);
                }
            });

}

/**
 * Esta función sirve para registrar la notas de un estudiante
 * @param enviada //Datos enviados por imput
 */
function notasGuardar(enviada, fila = null, tabla_notas = null) {
    var nota         = enviada.value.trim();
    var notaAnterior = enviada.getAttribute("data-nota-anterior") ?? 0;

    // Validate student ID
    var codEst = enviada.getAttribute("data-cod-estudiante");
    if (!codEst || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante. Por favor recargue la página e intente nuevamente.');
        enviada.value = notaAnterior;
        return;
    }

    // Validate activity ID
    var codNota = enviada.getAttribute("data-cod-nota");
    if (!codNota || codNota <= 0) {
        Swal.fire('Error de validación','No se pudo identificar la actividad. Por favor recargue la página e intente nuevamente.');
        enviada.value = notaAnterior;
        return;
    }

    var NoEsValidaNota = alertValidarNota(nota);

    if (NoEsValidaNota) {
        enviada.value = notaAnterior;
        return;
	}

    // Puede ser null si es una actividad individual. En este caso se usa el id de la carga académica.
    var carga             = enviada.getAttribute("data-carga-actividad") ?? null;

    var input             = enviada.id;
	var codEst            = enviada.getAttribute("data-cod-estudiante");
    var colorNotaAnterior = enviada.getAttribute("data-color-nota-anterior") ?? '#000000';
	var codNota           = enviada.getAttribute("data-cod-nota");
	var nombreEst         = enviada.getAttribute("data-nombre-estudiante");
    var valorNota         = enviada.getAttribute("data-valor-nota") ?? 0;
    var origen            = enviada.getAttribute("data-origen") ?? null;

    // Determine the correct table ID
    var tableId = tabla_notas || 'tabla_notas_calificaciones';
    var tabla_notas       = document.getElementById(tableId);
    if (!tabla_notas) {
        Swal.fire('Error de validación','No se pudo encontrar la tabla de notas. Por favor recargue la página e intente nuevamente.');
        enviada.value = notaAnterior;
        return;
    }
    var tbody             = tabla_notas.querySelector("tbody");
    if (!tbody) {
        Swal.fire('Error de validación','No se pudo encontrar el cuerpo de la tabla de notas. Por favor recargue la página e intente nuevamente.');
        enviada.value = notaAnterior;
        return;
    }
    var filaCompleta      = document.getElementById(fila);
    if (!filaCompleta) {
        Swal.fire('Error de validación','No se pudo encontrar la fila del estudiante. Por favor recargue la página e intente nuevamente.');
        enviada.value = notaAnterior;
        return;
    }
    var idColumna         = 'columna_'+input;
    var colunaNota        = filaCompleta.querySelector("td[id='"+idColumna+"']");
    if (!colunaNota) {
        Swal.fire('Error de validación','No se pudo encontrar la columna de la nota. Por favor recargue la página e intente nuevamente.');
        enviada.value = notaAnterior;
        return;
    }
    var spinner           = document.createElement('span');
    var hrefDefinitiva    = filaCompleta.querySelector("a[id='definitiva_"+codEst+"']");
    var inputRecuperacion = filaCompleta.querySelector("input[data-id='recuperacion_"+codEst+""+carga+"']");

    if (origen == 2) {
        var sumaPorcentaje = 0;
        var calculo        = 0;

        filaCompleta.querySelectorAll("input[data-origen='2']").forEach(input => {
            if (input.value !== '') {
                calculo         += input.value * parseFloat(input.getAttribute('data-valor-nota') / 100);
                sumaPorcentaje  += parseFloat(input.getAttribute('data-valor-nota'));
            }
        });

        var definitiva      = calculo / (sumaPorcentaje / 100);
        var colorDefinitiva = aplicarColorNota(definitiva);

        if (hrefDefinitiva) {
            hrefDefinitiva.innerText = definitiva.toFixed(2);
            hrefDefinitiva.style.color = colorDefinitiva ?  colorDefinitiva : '#000000';
        }
    }
    
    tabla_notas.querySelectorAll("input").forEach(input => input.disabled = true);
    
    tbody.querySelectorAll('a').forEach(a => {
        a.style.visibility = 'hidden';
    });
    
    enviada.disabled = true;
    
    spinner.className = 'spinner-border spinner-border-sm';
    spinner.setAttribute('role', 'status');
    spinner.setAttribute('aria-hidden', 'true');
    spinner.style.display = 'block';
    spinner.style.margin = '0 auto';
    spinner.style.marginBottom = '5px';
    
    colunaNota.insertBefore(spinner, colunaNota.firstChild);
    
	var colorAplicado = aplicarColorNota(nota, input);
    
    notaCualitativa(nota, codEst, carga, colorAplicado)
    .then(function(res) {
        
        let idHref = 'CU'+codEst+carga;
        let href   = document.getElementById(idHref);
        
        if(!res.success) {
            console.error("Error al obtener la calificación cualitativa.");
            href.innerHTML    = '<span style="color:red;">Error al guardar la nota</span>';
            enviada.disabled  = false;
            enviada.value     = notaAnterior;
            document.getElementById(input).style.color = colorNotaAnterior;
            spinner.remove();
            tabla_notas.querySelectorAll("input").forEach(input => input.disabled = false);
            tbody.querySelectorAll('a').forEach(a => {
                a.style.visibility = 'visible';
            });
            
            return;
        }
        
        $('#respRCT').empty().hide().html("Guardando la nota, espere por favor...").show(1);
        
        datos = "nota="+(nota)+
        "&codNota="+(codNota)+
        "&notaAnterior="+(notaAnterior)+
        "&nombreEst="+(nombreEst)+
        "&codEst="+(codEst);

        return $.ajax({
            type: "POST",
            url: "ajax-notas-guardar.php",
            data: datos,
            success: function(data) {
                $('#respRCT').empty().hide().html(data).show(1);

                // ✅ ACTUALIZAR LA NOTA ANTERIOR: Guardar la nota que acabamos de colocar
                // para que la próxima vez que se cambie sin recargar, tengamos el valor correcto
                enviada.setAttribute("data-nota-anterior", nota);

                // ✅ RECALCULAR PORCENTAJE Y DEFINITIVA DEL ESTUDIANTE
                recalcularDefinitiva(codEst);

                // ✅ RECALCULAR PROMEDIOS GENERALES
                if (typeof recalcularPromedios === 'function') {
                    recalcularPromedios();
                }

                if (inputRecuperacion) {
                    // Usar la nota mínima para aprobar configurada por la institución
                    var notaMinimaAprobar = (window.CONFIG_INSTITUCION && window.CONFIG_INSTITUCION.notaMinimaAprobar) 
                        ? window.CONFIG_INSTITUCION.notaMinimaAprobar 
                        : 3.5;
                    
                    if (nota < notaMinimaAprobar) {
                        inputRecuperacion.style.visibility = 'visible';
                    } else {
                        inputRecuperacion.style.visibility = 'hidden';
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", error);
            },
            complete: function() {
                // --- LÓGICA CLAVE PARA MOVER EL FOCO ---
                
                // 1. Obtener el tabindex actual y el siguiente
                const currentTabindex = parseInt(enviada.tabIndex);
                const nextTabindex = currentTabindex + 1;
                
                // 2. Usar querySelector para encontrar el input con el siguiente tabindex
                const nextInput = document.querySelector(`[tabindex="${nextTabindex}"]`);
                console.log(enviada.tabIndex);
                
                // 3. Mover el foco si se encuentra el siguiente input
                if (nextInput) {
                    // Usamos setTimeout para que el focus se ejecute al final de la cola de eventos
                    // Esto asegura que el bloque `complete` y cualquier otra manipulación del DOM haya terminado.
                    setTimeout(() => {
                        nextInput.focus();
                    }, 10); // Un pequeño retraso de 10ms
                }

                enviada.disabled = false;
                spinner.remove();
                tabla_notas.querySelectorAll("input").forEach(input => input.disabled = false);
                tbody.querySelectorAll('a').forEach(a => {
                    a.style.visibility = 'visible';
                });
            }
        });

    }).catch(function(error) {
        console.error("ERROR: ", error);
    });

}

/**
 * Esta función sirve para registrar una misma nota a todos los estudiantes
 * @param enviada //Datos enviados por imput
 */
function notasMasiva(enviada){
    var nota = enviada.value.trim();
	var codNota = enviada.name;
    var recargarPanel = enviada.title;

    // Validate activity ID
    if (!codNota || codNota <= 0) {
        Swal.fire('Error de validación','ID de actividad inválido para calificación masiva.');
        return false;
    }

    if (alertValidarNota(nota)) {
        return false;
    }

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "nota="+(nota)+
                "&codNota="+(codNota)+
                "&recargarPanel="+(recargarPanel);
                $.ajax({
                    type: "POST",
                    url: "ajax-notas-masiva-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar una nota de recuperacion a un estudiante
 * @param enviada //Datos enviados por input
 */
function notaRecuperacion(enviada){
    var carga = enviada.step;

    var codEst = enviada.id;
    var nota = enviada.value.trim();
    var notaAnterior = enviada.name;
    var nombreEst = enviada.alt;
    var codNota = enviada.title;

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para la recuperación. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    // Validate activity ID
    if (!codNota  || codNota <= 0) {
        Swal.fire('Error de validación','No se pudo identificar la actividad para la recuperación. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    if (alertValidarNota(nota)) {
        return false;
    }

    notaCualitativa(nota,codEst,carga);

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "nota="+(nota)+
                "&codNota="+(codNota)+
                "&notaAnterior="+(notaAnterior)+
                "&nombreEst="+(nombreEst)+
                "&codEst="+(codEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-nota-recuperacion-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                        
                        // ✅ RECALCULAR PORCENTAJE Y DEFINITIVA DEL ESTUDIANTE
                        if (typeof recalcularDefinitiva === 'function') {
                            recalcularDefinitiva(codEst);
                        }

                        // ✅ RECALCULAR PROMEDIOS GENERALES
                        if (typeof recalcularPromedios === 'function') {
                            recalcularPromedios();
                        }
                    }  
                });
}

/**
 * Esta función sirve para registrar la observacion en una actividad de un estudiante
 * @param enviada //Datos enviados por input
 */
function guardarObservacion(enviada){
    var codEst = enviada.id;
    var observacion = enviada.value.trim();
    var nombreEst = enviada.alt;
    var codObservacion = enviada.title;

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para las observaciones. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    // Validate activity ID
    if (!codObservacion || codObservacion <= 0) {
        Swal.fire('Error de validación','No se pudo identificar la actividad para las observaciones. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "observacion="+(observacion)+
                "&codObservacion="+(codObservacion)+
                "&nombreEst="+(nombreEst)+
                "&codEst="+(codEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-observaciones-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar una misma nota de disciplina a todos los estudiantes
 * @param enviada //Datos enviados por input
 */
function notasMasivaDisciplina(enviada){
    var nota = enviada.value.trim();
	var carga = enviada.name;
    var periodo = enviada.title;

    // Validate period ID
    if (!periodo || isNaN(periodo) || periodo <= 0) {
        Swal.fire('Error de validación','No se pudo identificar el periodo para la calificación masiva de disciplina. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    if (alertValidarNota(nota)) {
        return false;
    }

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "nota="+(nota)+
                "&carga="+(carga)+
                "&periodo="+(periodo);
                $.ajax({
                    type: "POST",
                    url: "ajax-notas-disciplina-masiva-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar la nota de disciplina de un estudiante
 * @param enviada //Datos enviados por input
 */
function notasDisciplina(enviada){
    var nota = enviada.value.trim();
	var carga = enviada.name;
    var periodo = enviada.title;
	var codEst = enviada.id;
	var nombreEst = enviada.alt;

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para la calificación de disciplina. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    // Validate period ID
    if (!periodo || isNaN(periodo) || periodo <= 0) {
        Swal.fire('Error de validación','No se pudo identificar el periodo para la calificación de disciplina. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    if (alertValidarNota(nota)) {
        return false;
    }

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "nota="+(nota)+
                "&carga="+(carga)+
                "&periodo="+(periodo)+
                "&codEst="+(codEst)+
                "&nombreEst="+(nombreEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-nota-disciplina-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar una observación disciplinaria a un estudiante
 * @param enviada //Datos enviados por textarea
 */
function observacionDisciplina(enviada){
    var periodo = enviada.title;
    var observacion = enviada.value.trim();
	var carga = enviada.getAttribute('step');
	var codEst = enviada.id;
	var multiple = enviada.getAttribute('alt');

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para las observaciones disciplinarias. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    // Validate period ID
    if (!periodo || isNaN(periodo) || periodo <= 0) {
        Swal.fire('Error de validación','No se pudo identificar el periodo para las observaciones disciplinarias. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    if(multiple == 1){
        var nameId = enviada.name;
        var observaciones = document.getElementById(nameId);
        var observacion = [];
        for (let i = 0; i < observaciones.options.length; i++) {
            if (observaciones.options[i].selected) {
                observacion.push(observaciones.options[i].value);
            }
        }
    }

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "observacion="+(observacion)+
                "&carga="+(carga)+
                "&periodo="+(periodo)+
                "&codEst="+(codEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-observacion-disciplina-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar el aspecto academico de un estudiante
 * @param enviada //Datos enviados por textarea
 */
function aspectosAcademicos(enviada){
    var aspecto = enviada.value.trim();
	var carga = enviada.name;
    var periodo = enviada.title;
	var codEst = enviada.id;

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para el aspecto académico. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    // Validate period ID
    if (!periodo || isNaN(periodo) || periodo <= 0) {
        Swal.fire('Error de validación','No se pudo identificar el periodo para el aspecto académico. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "aspecto="+(aspecto)+
                "&carga="+(carga)+
                "&periodo="+(periodo)+
                "&codEst="+(codEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-aspectos-academicos-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar el aspecto convivencial de un estudiante
 * @param enviada //Datos enviados por textarea
 */
function aspectosConvivencial(enviada){
    var aspecto = enviada.value.trim();
	var carga = enviada.name;
    var periodo = enviada.title;
	var codEst = enviada.id;

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para el aspecto convivencial. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    // Validate period ID
    if (!periodo || isNaN(periodo) || periodo <= 0) {
        Swal.fire('Error de validación','No se pudo identificar el periodo para el aspecto convivencial. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    $('#respRCT').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "aspecto="+(aspecto)+
                "&carga="+(carga)+
                "&periodo="+(periodo)+
                "&codEst="+(codEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-aspectos-convivencional-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRCT').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar las observaciones que se ven reflejadas en el boletín de un estudiante
 * @param enviada //Datos enviados por textarea
 */
function observacionesBoletin(enviada){
    var observacion = enviada.value.trim();
	var carga = enviada.name;
    var periodo = enviada.title;
	var codEst = enviada.id;

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para las observaciones del boletín. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    // Validate period ID
    if (!periodo || isNaN(periodo) || periodo <= 0) {
        Swal.fire('Error de validación','No se pudo identificar el periodo para las observaciones del boletín. Por favor recargue la página e intente nuevamente.');
        return false;
    }

    $('#respOBS').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "observacion="+(observacion)+
                "&carga="+(carga)+
                "&periodo="+(periodo)+
                "&codEst="+(codEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-observacion-boletin-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respOBS').empty().hide().html(data).show(1);
                    }  
                });
}

/**
 * Esta función sirve para registrar la recuperación en un indicador de un estudiante
 * @param enviada //Datos enviados por input
 */
function recuperarIndicador(enviada){
    var split = enviada.step.split('_');
    var carga = split[0];
    var periodo = split[1];

    var nota = enviada.value.trim();
    var notaAnterior = enviada.name;
    var codEst = enviada.id;
    var codNota = enviada.alt;
    var valorDecimalIndicador = enviada.title;

    // Validate student ID
    if (!codEst  || codEst <= 0) {
        Swal.fire('Error de validación','No se pudo identificar al estudiante para la recuperación de indicador. Por favor recargue la página e intente nuevamente.');
        enviada.value = "";
        enviada.focus();
        return false;
    }

    // Validate activity ID
    if (!codNota  || codNota <= 0) {
        Swal.fire('Error de validación','No se pudo identificar la actividad para la recuperación de indicador. Por favor recargue la página e intente nuevamente.');
        enviada.value = "";
        enviada.focus();
        return false;
    }

    // Validate period ID
    if (!periodo || isNaN(periodo) || periodo <= 0) {
        Swal.fire('Error de validación','No se pudo identificar el periodo para la recuperación de indicador. Por favor recargue la página e intente nuevamente.');
        enviada.value = "";
        enviada.focus();
        return false;
    }
        
    var casilla = document.getElementById(codEst);

    var notaAnteriorTransformada = (notaAnterior/valorDecimalIndicador);
    notaAnteriorTransformada = Math.round(notaAnteriorTransformada * 10) / 10;

    if(isNaN(nota)){
        Swal.fire('Esto no es un valor numérico: '+nota+'. Si estás usando comas, reemplacelas por un punto.'); 
        casilla.value="";
        casilla.focus();
        return false;	
    }	

    if (alertValidarNota(nota)) {
        casilla.value="";
        casilla.focus();
        return false;
    }
    if(nota==notaAnteriorTransformada){
        Swal.fire(`No es permitido colocar una nota de recuperación igual: ${nota} a la nota anterior: ${notaAnteriorTransformada}.`);
        casilla.value="";
        casilla.focus();
        return false;
    }	
    notaCualitativa(nota,codEst,carga);
        
        
    casilla.disabled="disabled";
    casilla.style.fontWeight="bold";
            
    $('#respRC').empty().hide().html("Guardando información, espere por favor...").show(1);
        datos = "nota="+(nota)+
                "&codNota="+(codNota)+
                "&notaAnterior="+(notaAnterior)+
                "&carga="+(carga)+
                "&periodo="+(periodo)+
                "&codEst="+(codEst);
                $.ajax({
                    type: "POST",
                    url: "ajax-recuperacion-indicadores-guardar.php",
                    data: datos,
                    success: function(data){
                        $('#respRC').empty().hide().html(data).show(1);
                    }
                });
}

/**
 * Esta función me muestra la nota cualitativa
 * @param {boolean} nota
 * @param {string} idEstudiante
 * @param {string} idCarga
 */
function notaCualitativa(nota, idEstudiante, idCarga, color='black') {
    return new Promise((resolve, reject) => {
        let idHref = 'CU'+idEstudiante+idCarga;
        let href   = document.getElementById(idHref);
        let response;

        if (href === null) {
            console.error('Elemento no encontrado: ', idHref, idEstudiante, idCarga);
            reject('Elemento no encontrado: ', idHref);
        }

        href.innerHTML = '<span style="color:gray;">Calculando...</span>';

        fetch('../compartido/ajax-estilo-notas.php?nota='+nota+'&idEstudiante='+idEstudiante+'&idCarga='+idCarga, {method: 'GET'})
        .then(response => response.text()) // Convertir la respuesta a texto
        .then(data => {
            href.innerHTML = '<span style="color:'+color+';">'+data+'</span>';
            response = {
                success: true,
                data: data
            };

            resolve(response);
        })
        .catch(error => {
            // Manejar errores
            console.error('Error:', error);
            reject('Error al obtener la notaCualitativa' + error);
        });
    });
}
