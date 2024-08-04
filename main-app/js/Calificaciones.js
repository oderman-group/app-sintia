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
function notasGuardar(enviada, fila = null, tabla_notas = null){
    var nota = enviada.value;

    if (alertValidarNota(nota)) {		
		return false;
	}

    // Puede ser null si es una actividad individual. En este caso se usa el id de la carga académica.
    var carga             = enviada.getAttribute("data-carga-actividad") ?? null;

	var codEst            = enviada.getAttribute("data-cod-estudiante");
	var notaAnterior      = enviada.getAttribute("data-nota-anterior") ?? 0;
    var colorNotaAnterior = enviada.getAttribute("data-color-nota-anterior") ?? '#000000';
	var codNota           = enviada.getAttribute("data-cod-nota");	 
	var nombreEst         = enviada.getAttribute("data-nombre-estudiante");
    var input             = enviada.id;

    var tabla_notas       = document.getElementById(tabla_notas);
    var tbody             = tabla_notas.querySelector("tbody");
    var filaCompleta      = document.getElementById(fila);
    var idColumna         = 'columna_'+input;
    var colunaNota        = filaCompleta.querySelector("td[id='"+idColumna+"']");
    var spinner           = document.createElement('span');

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
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", error);
            },
            complete: function() {
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
    var nota = enviada.value;
	var codNota = enviada.name;	
    var recargarPanel = enviada.title;

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
    var nota = enviada.value;
    var notaAnterior = enviada.name;	
    var nombreEst = enviada.alt;
    var codNota = enviada.title;

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
                    }  
                });
}

/**
 * Esta función sirve para registrar la observacion en una actividad de un estudiante
 * @param enviada //Datos enviados por input
 */
function guardarObservacion(enviada){
    var codEst = enviada.id; 
    var observacion = enviada.value;	
    var nombreEst = enviada.alt;
    var codObservacion = enviada.title;

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
    var nota = enviada.value;
	var carga = enviada.name;	
    var periodo = enviada.title;

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
    var nota = enviada.value;
	var carga = enviada.name;	
    var periodo = enviada.title;
	var codEst = enviada.id;
	var nombreEst = enviada.alt;

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
    var observacion = enviada.value;
	var carga = enviada.getAttribute('step');	
	var codEst = enviada.id;
	var multiple = enviada.getAttribute('alt');

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
    var aspecto = enviada.value;
	var carga = enviada.name;	
    var periodo = enviada.title;
	var codEst = enviada.id;

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
    var aspecto = enviada.value;
	var carga = enviada.name;	
    var periodo = enviada.title;
	var codEst = enviada.id;

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
    var observacion = enviada.value;
	var carga = enviada.name;	
    var periodo = enviada.title;
	var codEst = enviada.id;
    console.log(carga);

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

    var nota = enviada.value;
    var notaAnterior = enviada.name;	
    var codEst = enviada.id;
    var codNota = enviada.alt;
    var valorDecimalIndicador = enviada.title;
        
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

        fetch('../compartido/ajax-estilo-notas.php?nota='+nota, {method: 'GET'})
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