/**
 * Esta función permite verifica si hay conexión a internet cada segundo
 * y muestra un mensaje al usuario si se perdió o volvió dicha conexión
 */
function hayInternet() {

    if(navigator.onLine) {

		if(localStorage.getItem("internet") == 0){
            if ( document.getElementById( "siInternet" )) {
                document.getElementById("siInternet").style.display="block";

                Swal.fire({
                    title: 'La conexión ha vuelto!', 
                    text: 'La conexión a internet ha vuelto. Puedes continuar trabajando en la plataforma.', 
                    icon: 'success',
                    backdrop: `
                        rgba(55,55,55,0.4)
                        url("https://media.giphy.com/media/IwTWTsUzmIicM/giphy.gif")
                        left top
                        no-repeat
                    `
                });
            }
		}

		localStorage.setItem("internet", 1);
        if ( document.getElementById( "noInternet" )) {
            document.getElementById("noInternet").style.display="none";
         };
        setTimeout(function() {
            if ( document.getElementById( "siInternet" )) {
            document.getElementById("siInternet").style.display="none";
             };
        }, 10000);
        

	} else {

		if(localStorage.getItem("internet") == 1 || localStorage.getItem("internet") == null) {
            Swal.fire({
                title: 'Se ha perdido la conexión!', 
                text: 'Se ha perdido tu conexión a internet. Por favor verifica antes de continuar trabajando en la plataforma.', 
                icon: 'error',
                backdrop: `
                    rgba(55,55,55)
                    url("../files/noInternet.webp")
                    left top
                    no-repeat
                `
            });
        }
        

        localStorage.setItem("internet", 0);
		document.getElementById("noInternet").style.display="block";

	}

}

setInterval('hayInternet()', 1000);

/**
 * Esta función permite que el usuario confirme antes de regresar a una pagina anterior
 * para evitar que vaya a perder cambios sin guardar
 * @param {Array} dato 
 */
function deseaRegresar(dato){	
	var url = dato.name;

    Swal.fire({
        title: 'Desea regresar?',
        text: "Si va a regresar verifique que no haya hecho cambios en esta página y estén sin guardar. Desea regresar de todas formas?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, deseo regresar!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href=url;
        }
    })

}

/**
 * Esta funcion genera una confimacion de warning personalizada 
 * 
 * @param titulo
 * @param mensaje
 * @param tipos  [success,error,warning,info,question]
 * @return boolean 
 */
 function sweetConfirmacion(titulo, mensaje, tipo ='question', varHeref, async = false, idRegistroTabla = null, method = 'GET',data = null,funcion =null) {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo,
        showCancelButton: true,
        confirmButtonText: 'Si!',
        cancelButtonText: 'No!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            if(varHeref === null) {
                return true;
            } else {
                if(async == true) { 
                    if(data != null){
                         document.getElementById("overlay").style.display = "flex"
                        try {
                            
                            const resultado = await metodoFetchAsync(varHeref, data, 'json', method == 'GET');
                           
                            if (typeof window[funcion] === "function") {
                                window[funcion](resultado, data, true);
                            } else {
                                console.error(`La función "${funcion}" no existe o no está definida.`);
                            }
                           
                        } catch (error) {
                            console.error("Error al ejecutar la función:", error);
                        }
                         document.getElementById("overlay").style.display = "none"
                    }else{
                        // Agregar token CSRF según el método
                        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
                        let urlFinal = varHeref;
                        let fetchOptions = { method: method };
                        
                        if (method === 'GET') {
                            const separator = urlFinal.includes('?') ? '&' : '?';
                            urlFinal = urlFinal + separator + 'csrf_token=' + encodeURIComponent(csrfToken);
                        } else {
                            // Para POST, agregar en el body
                            const formData = new FormData();
                            formData.append('csrf_token', csrfToken);
                            fetchOptions.body = formData;
                        }
                        
                        fetch(urlFinal, fetchOptions)
                        .then(response => response.text()) // Convertir la respuesta a texto
                        .then(data => {
                            if (idRegistroTabla != null) {
                                document.getElementById('registro_'+idRegistroTabla).style.display = 'none';
                            }
    
                            $.toast({
                                heading: 'Proceso completado', 
                                text: 'Se ha completado el proceso correctamente.', 
                                position: 'bottom-right',
                                showHideTransition: 'slide',
                                loaderBg:'#26c281', 
                                icon: 'success', 
                                hideAfter: 3000, 
                                stack: 2
                    
                            });
                        })
                        .catch(error => {
                            // Manejar errores
                            console.error('Error:', error);
                        });

                    }
                    
                } else {
                    // Agregar token CSRF a la URL para protección
                    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
                    const separator = varHeref.includes('?') ? '&' : '?';
                    const urlWithToken = varHeref + separator + 'csrf_token=' + encodeURIComponent(csrfToken);
                    window.location.href=urlWithToken;
                }
            }

        } else {

            if(varHeref === null) {
                return false;
            } else {                
                window.location.href='#';
                if (typeof window[funcion] === "function") {
                    window[funcion](null, data, false);
                }
            }

        }
    })

}

function axiosAjax(datos){

    let url = datos.id;
    let divRespuestaNombre = 'RESP_'+datos.title;
    let divRespuesta = document.getElementById(divRespuestaNombre);

    axios.get(url)
    .then(function (response) {
        // handle success
        console.log(response.data);
        divRespuesta.innerHTML = response.data;
    })
    .catch(function (error) {

        // handle error
        console.log(error);
    })
    .then(function () {
        // always executed
    });

}

/**
 * Esta función la solicitó en su momento el nuevo ghandy, pero creemos que no llegó a usarse.
 */
function deseaGenerarIndicadores(dato) {
	document.getElementById('agregarNuevo').style.display="none";
	document.getElementById('preestablecidos').style.display="none";

	var respuesta = sweetConfirmacion('Eliminar indiacdores','Al ejecutar esta acción se eliminaran los indicadores y actividades ya creados. Desea continuar bajo su responsabilidad?');
	var url = dato.name;

	if(respuesta == true){

		$.toast({

			heading: 'Acción en proceso', 
			text: 'Estamos creando los indicadores y actividades para ti, te avisaremos encuanto esten creados.', 
			position: 'bottom-right',
            showHideTransition: 'slide',
			loaderBg:'#26c281', 
			icon: 'warning', 
			hideAfter: 5000, 
			stack: 6

		});

		document.getElementById('msjPree').style.display="block";

		axios.get(url).then(function (response) {
			document.getElementById('msjPree').style.display="none";
			$.toast({

				heading: 'Acción realizada', text: 'Los indicadores y actividades fueron creados correctamente, racargaremos la página.', 
				position: 'bottom-right',
                showHideTransition: 'slide',
				loaderBg:'#26c281', 
				icon: 'success', 
				hideAfter: 5000, 
				stack: 6

			});
			location.reload();
		})
	}
}

/**
 * Esta función pide confirmación al usuario antes de eliminar un registro
 * o ejecutar alguna acción de eliminar algo
 * @param {Array} dato 
 */
function deseaEliminar(dato) {
    
    let varObjet = undefined;

    if (dato.title !== '' && dato.title !== undefined) {
        try {
            let variable = (dato.title);
            varObjet = JSON.parse(variable);
            var input = document.getElementById(parseInt(varObjet.idInput));
        } catch (e) {
            console.log('ℹ️ No hay datos JSON en title (o no es válido), continuando con eliminación simple');
            // No es un error crítico, simplemente no hay datos extra
        }
    }

    var url = dato.name;
    var id = dato.id;
    var registro = document.getElementById("reg" + id);
    var evaPregunta = document.getElementById("pregunta" + id);
    var publicacion = document.getElementById("PUB" + id);
    var elementoGlobalBloquear = document.getElementsByName("elementoGlobalBloquear")[0];

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
            
            // Variable para el overlay (scope accesible en then y catch)
            let overlay = null;

            if (elementoGlobalBloquear) {
                elementoGlobalBloquear.style.position = 'relative';
                overlay = document.createElement('div');
                overlay.style.position = 'absolute';
                overlay.style.top = 0;
                overlay.style.left = 0;
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(128, 128, 128, 0.7)';
                overlay.style.display = 'flex';
                overlay.style.justifyContent = 'center';
                overlay.style.alignItems = 'center';
                overlay.style.color = 'white';
                overlay.style.fontSize = '2em';
                overlay.innerText = 'Eliminando el registro, por favor espere...';
                elementoGlobalBloquear.appendChild(overlay);
            }

            if (typeof id !== "undefined" && id !== "") {

                if (typeof varObjet !== "undefined") {
                    var input = document.getElementById(parseInt(varObjet.idInput));
                    if (varObjet.tipo === 2 || varObjet.tipo === 5) {
                        var input = document.getElementById(varObjet.idInput);
                    }
                }

                // Agregar token CSRF a la URL para protección
                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
                const separator = url.includes('?') ? '&' : '?';
                const urlWithToken = url + separator + 'csrf_token=' + encodeURIComponent(csrfToken);
                
                axios.get(urlWithToken)
                .then(function(response) {
                    // Verificar si la respuesta es JSON
                    const data = response.data;
                    
                    console.log('✅ Respuesta de eliminación:', data);
                    
                    // Manejar respuesta JSON estructurada
                    if (data && typeof data === 'object' && data.hasOwnProperty('success')) {
                        // Remover overlay de carga
                        if (elementoGlobalBloquear && overlay) {
                            elementoGlobalBloquear.removeChild(overlay);
                        }
                        
                        if (data.success) {
                            // ÉXITO: Mostrar toast y eliminar el registro
                            if (typeof $.toast === 'function') {
                                $.toast({
                                    heading: 'Eliminado Exitosamente',
                                    text: data.message || 'El registro ha sido eliminado correctamente',
                                    showHideTransition: 'slide',
                                    icon: 'success',
                                    position: 'top-right',
                                    hideAfter: 4000
                                });
                            }
                            
                            // Animar y eliminar el registro visual
                            if (registro) {
                                registro.classList.add('animate__animated', 'animate__fadeOutRight');
                                setTimeout(() => {
                                    registro.style.display = "none";
                                    registro.remove();
                                }, 800);
                            }
                        } else {
                            // ERROR: Mostrar mensaje de error
                            if (typeof $.toast === 'function') {
                                $.toast({
                                    heading: 'Error al Eliminar',
                                    text: data.message || 'No se pudo eliminar el registro',
                                    showHideTransition: 'slide',
                                    icon: 'error',
                                    position: 'top-right',
                                    hideAfter: 6000
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al Eliminar',
                                    text: data.message || 'No se pudo eliminar el registro'
                                });
                            }
                        }
                        return;
                    }
                    
                    // FALLBACK: Si no es JSON estructurado, usar lógica antigua
                    console.log('⚠️ Respuesta no es JSON estructurado, usando lógica legacy');
                    if (typeof varObjet !== "undefined") {
                        // handle success
                        if (varObjet.tipo === 1) {

                            async function miFuncionConDelay() {
                                await new Promise(resolve => setTimeout(resolve, 1000));
                                registro.style.display = "none";
                                registro.remove();
                            }

                            

                            registro.classList.add('animate__animated', 'animate__bounceOutRight', 'animate__delay-0.5s');
                            miFuncionConDelay();                          
                            if (varObjet.restar !== undefined) {
                                var restar              =  varObjet.restar;
                        
                                var idSubtotal          = document.getElementById('subtotal');
                                var subtotalNeto        = parseFloat(idSubtotal.getAttribute("data-subtotal"));
                                var subtotal            = subtotalNeto-restar;
                                var subtotalFinal       = "$"+numberFormat(subtotal, 0, ',', '.');
                        
                                var idTotalNeto         = document.getElementById('totalNeto');
                                var totalNeto           = parseFloat(idTotalNeto.getAttribute("data-total-neto"));
                                var total               = totalNeto-restar;
                                var totalFinal          = "$"+numberFormat(total, 0, ',', '.');
                                
                                idSubtotal.innerHTML = '';
                                idSubtotal.appendChild(document.createTextNode(subtotalFinal));
                                idSubtotal.dataset.subtotal = subtotal;
                                
                                idTotalNeto.innerHTML = '';
                                idTotalNeto.appendChild(document.createTextNode(totalFinal));
                                idTotalNeto.dataset.totalNeto = total;
                            }
                        }

                        if (varObjet.tipo === 2 || varObjet.tipo === 5) {
                            document.getElementById(id).style.display = "none";
                            input.value = "";
                            
                            // ✅ RECALCULAR DEFINITIVA Y PROMEDIOS DESPUÉS DE ELIMINAR NOTA
                            if (typeof recalcularDefinitiva === 'function') {
                                const codEst = input.getAttribute('data-cod-estudiante');
                                if (codEst) {
                                    setTimeout(() => {
                                        recalcularDefinitiva(codEst);
                                    }, 100);
                                }
                            }
                            
                            if (typeof recalcularPromedios === 'function') {
                                setTimeout(() => {
                                    recalcularPromedios();
                                }, 200);
                            }
                        }

                        if (varObjet.tipo === 3) {
                            evaPregunta.style.display = "none";
                        }

                        if (varObjet.tipo === 4) {

                            async function miFuncionConDelay() {
                                await new Promise(resolve => setTimeout(resolve, 1000));
                                publicacion.style.display = "none";
                            }

                            miFuncionConDelay();

                            publicacion.classList.add('animate__animated', 'animate__bounceOutRight', 'animate__delay-0.5s');
                            
                        }
                        
                        // Toast legacy para tipos antiguos
                        if (typeof $.toast === 'function') {
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
                        }

                        if (elementoGlobalBloquear && overlay) {
                            elementoGlobalBloquear.removeChild(overlay);
                        }
                    }

                }).catch(function(error) {
                    console.error('❌ Error en eliminación:', error);
                    
                    // Remover overlay de carga
                    if (elementoGlobalBloquear && overlay) {
                        try {
                            elementoGlobalBloquear.removeChild(overlay);
                        } catch(e) {}
                    }
                    
                    // Mostrar error al usuario
                    if (typeof $.toast === 'function') {
                        $.toast({
                            heading: 'Error de Conexión',
                            text: 'No se pudo completar la eliminación. Verifica tu conexión e intenta de nuevo.',
                            showHideTransition: 'slide',
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 6000
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Conexión',
                            text: 'No se pudo completar la eliminación. Verifica tu conexión e intenta de nuevo.'
                        });
                    }
                });
            } else {
                window.location.href = url;
            }
            
        }else{
            return false;
        }
    })

}

function inhabilitarBotones(idBtn) {
    // Obtener el botón por su ID
    const miBoton = document.getElementById(idBtn);

    // Deshabilitar el botón
    miBoton.disabled = true;
}

function ejecutarOtrasFunciones(params) {
    // Esperar a que el documento esté listo (DOMContentLoaded)
    document.addEventListener("DOMContentLoaded", function() {
        // Después de que el documento esté listo, esperar 1 milisegundo y luego llamar a la función
        setTimeout(function() {
            inhabilitarBotones(params);
        }, 100);
    });
}


function ocultarNoticia(datos) {
    arrayInfo   = datos.id.split('|');
    var url     = datos.name;
    var pub     = document.getElementById("PANEL"+arrayInfo[0]);
    var accion  = arrayInfo[1];
    var mensaje = null;

    axios.get(url).then(function(response) {

        //Ocultar
        if(accion == 2) {
            pub.style.backgroundColor="#999";
            pub.style.opacity="0.7";
            mensaje = 'La noticia fue ocultada correctamente.';
        } 
        // Mostrar
        else {
            pub.style.backgroundColor="#fff";
            pub.style.opacity="1";
            mensaje = 'La noticia fue mostrada correctamente.';
        }

        $.toast({
            heading: 'Acción realizada',
            text: mensaje,
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

}

/**
 * Esta función crea una publicación rápida
 */
function crearNoticia() {

    var campoContenido = document.getElementById("contenido");
    const contenedor   = document.getElementById("nuevaPublicacion");
    const nuevoDiv     = document.createElement("div");
    var infoGeneral    = document.getElementById("infoGeneral");
    var arrayInfo      = infoGeneral.value.split('|');
    var idUsuario      = arrayInfo[0];
    var fotoUsuario    = arrayInfo[1]; 
    var nombreUsuario  = arrayInfo[2]; 

    if( campoContenido.value.trim() === '' ) {
        campoContenido.classList.remove('animate__animated', 'animate__shakeY', 'animate__delay-0.5s');
        void campoContenido.offsetWidth; // Truco para forzar una reflow (actualización del estilo)
        campoContenido.classList.add('animate__animated', 'animate__shakeY', 'animate__delay-0.5s');
        campoContenido.value = '';
        campoContenido.style.borderColor = "tomato";
        campoContenido.placeholder = "Escribe algo antes de publicar...";
        campoContenido.focus();

        return false;
    }

    const url = "../compartido/noticia-rapida-guardar.php?contenido="+campoContenido.value;

    axios.get(url)
    .then(function(response) {
        var idRegistro           = response.data;
        var idRegistroEncriptado = btoa(idRegistro.toString());

        nuevoDiv.id = "PUB"+idRegistroEncriptado;
        nuevoDiv.className = "row";

    // Establece el contenido HTML en el nuevo div
        nuevoDiv.innerHTML = `
            <div class="col-sm-12 animate__animated animate__rubberBand animate__delay-1s animate__slow">
                <div id="PANEL${idRegistroEncriptado}" class="panel">
                    <div class="card-head">
                        <header></header>
                        <button id="panel-${idRegistro}"
                            class="mdl-button mdl-js-button mdl-button--icon pull-right"
                            data-upgraded=",MaterialButton">
                            <i class="material-icons">more_vert</i>
                        </button>
                    </div>

                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="${fotoUsuario}" class="img-circle user-img-circle" alt="User Image"
                                height="50" width="50" />
                        </div>
                        <div class="pull-left info">
                            <p><a href="<?=$_SERVER['PHP_SELF'];?>?usuario=${nombreUsuario}">${nombreUsuario}</a><br>
                                    <span style="font-size: 11px;">Ahora mismo</span></p>
                        </div>
                    </div>

                    <div class="card-body">
                        ${campoContenido.value}
                    </div>

                    <div class="card-body">
                        <a id="#" class="pull-left"><i class="fa fa-thumbs-o-up"></i> Me gusta</a>
                    </div>

                </div>
                
            </div>
        `;

        contenedor.insertBefore(nuevoDiv, contenedor.firstChild);

        campoContenido.value="";

        $.toast({
            heading: 'Acción realizada',
            text: 'La noticia se publicado correctamente',
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
}

// Evitar redeclaración de la variable
if (typeof window.estudiantesPorEstados === 'undefined') {
    window.estudiantesPorEstados = {};
}
const estudiantesPorEstados = window.estudiantesPorEstados;

function cambiarEstadoMatricula(data) {
    let idHref = 'estadoMatricula'+data.id_estudiante;
    let badge  = document.getElementById(idHref);
    
    if (!estudiantesPorEstados.hasOwnProperty(data.id_estudiante)) {
        estudiantesPorEstados[data.id_estudiante] = data.estado_matricula;
    }

    // Mapeo de estados a badges
    const estadosInfo = {
        1: { clase: 'badge badge-success', texto: 'Matriculado' },
        2: { clase: 'badge badge-warning', texto: 'Asistente' },
        3: { clase: 'badge badge-danger', texto: 'Cancelado' },
        4: { clase: 'badge badge-secondary', texto: 'No Matriculado' },
        5: { clase: 'badge badge-info', texto: 'En inscripción' }
    };

    const estadoActual = parseInt(estudiantesPorEstados[data.id_estudiante]);
    let nuevoEstado = null;

    // Determinar el siguiente estado válido según las reglas:
    // - En Inscripción (5): No puede modificarse
    // - Asistente (2): Solo puede cambiar a Matriculado (1)
    // - No matriculado (4): Solo puede cambiar a Matriculado (1)
    // - Matriculado (1): Puede cambiar a otros estados, pero no a No matriculado (4)
    // - Cancelado (3): Se gestiona automáticamente

    if (estadoActual === 5) {
        // En Inscripción: No puede modificarse
        alert('El estado "En inscripción" no puede modificarse. Se gestiona automáticamente desde el módulo de admisiones.');
        return;
    }

    if (estadoActual === 3) {
        // Cancelado: Se gestiona automáticamente; usuarios DEV pueden restaurar a Matriculado (1)
        if (typeof window !== 'undefined' && window.usuarioEsDev === true) {
            nuevoEstado = 1;
        } else {
            alert('El estado "Cancelado" no puede modificarse desde aquí. Se gestiona desde otros módulos del sistema.');
            return;
        }
    }

    if (estadoActual === 2) {
        // Asistente: Solo puede cambiar a Matriculado (1)
        nuevoEstado = 1;
    } else if (estadoActual === 4) {
        // No matriculado: Solo puede cambiar a Matriculado (1)
        nuevoEstado = 1;
    } else if (estadoActual === 1) {
        // Matriculado: NO puede cambiar a Asistente (2) ni a No matriculado (4), salvo Directivos o usuarios DEV (Matriculado → No matriculado)
        if (typeof window !== 'undefined' && (window.usuarioPuedeMatriculadoANoMatriculado === true || window.usuarioEsDev === true || window.usuarioEsDirectivo === true)) {
            nuevoEstado = 4;
        } else {
            alert('El estado "Matriculado" no puede cambiarse desde aquí. Solo los estudiantes en estado "Asistente" o "No matriculado" pueden cambiar a "Matriculado".');
            return;
        }
    } else {
        // Para cualquier otro estado, intentar cambiar a Matriculado
        nuevoEstado = 1;
    }

    // Actualizar el badge con el nuevo estado
    const estadoInfo = estadosInfo[nuevoEstado];
    
    badge.className = estadoInfo.clase;
    badge.textContent = estadoInfo.texto;
    estudiantesPorEstados[data.id_estudiante] = nuevoEstado;

    let datos = "nuevoEstado="+nuevoEstado+
                "&idEstudiante="+data.id_estudiante;

    $.ajax({
        type: "POST",
        url: "ajax-cambiar-estado-matricula.php",
        data: datos,
        success: function(response){
            $('#respuestaCambiarEstado').empty().hide().html(response).show(1);
        },
        error: function(xhr, status, error) {
            // Si hay error, revertir el cambio visual
            const estadoInfoActual = estadosInfo[estadoActual];
            badge.className = estadoInfoActual.clase;
            badge.textContent = estadoInfoActual.texto;
            estudiantesPorEstados[data.id_estudiante] = estadoActual;
            
            // Mostrar mensaje de error
            $('#respuestaCambiarEstado').empty().hide().html(
                '<div class="alert alert-danger">' +
                '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                '<p><strong>Error:</strong> No se pudo cambiar el estado de la matrícula.</p>' +
                '</div>'
            ).show(1);
        }
    });
}


const estudiantesPorEstadosBloqueo = {};

function cambiarBloqueo(data) {

    if(data.bloqueado == null || data.bloqueado == '') {
        data.bloqueado = 0;
    }

    if (!estudiantesPorEstadosBloqueo.hasOwnProperty(data.id_estudiante)) {
        estudiantesPorEstadosBloqueo[data.id_estudiante] = data.bloqueado;
    }

    var estadoFinal = estudiantesPorEstadosBloqueo[data.id_estudiante];
    var checkEstudiantes = document.getElementById("checkboxCambiarBloqueo" + data.id_estudiante);
    let datos = "&idR="+btoa(data.id_usuario.toString())+
                "&lock="+btoa(estadoFinal.toString());

    // Determinar el nuevo estado del checkbox
    if (checkEstudiantes.checked) {
        // Mostrar el modal
        $('#motivoModal').modal('show');

        // Al confirmar el motivo
        $('#confirmarMotivo').off('click').on('click', function () {
            var motivo = document.getElementById("motivo").value.trim();

            if (motivo === "") {
                alert("Debe ingresar un motivo.");
                return;
            }

            // Ocultar el modal
            $('#motivoModal').modal('hide');

            // Limpiar el contenido del textarea para futuros usos
            document.getElementById("motivo").value = "";

            datos = datos + "&motivo=" + encodeURIComponent(motivo);

            enviarAjaxCambiarBloqueo(data, datos);
        });

        // Al cancelar el motivo
        $('#cancelarMotivo').off('click').on('click', function () {
            checkEstudiantes.checked = false;
        });
   
        // Al cancelar el motivo
        $('#boton-cerrar-modal-motivo').off('click').on('click', function () {
            checkEstudiantes.checked = false;
        });
    } else {
        enviarAjaxCambiarBloqueo(data, datos);
    }
}

function enviarAjaxCambiarBloqueo(data, datos) {

    let tr   = document.getElementById("EST"+data.id_estudiante);

    if(estudiantesPorEstadosBloqueo[data.id_estudiante] == 0) {
        estudiantesPorEstadosBloqueo[data.id_estudiante] = 1;
    } else {
        estudiantesPorEstadosBloqueo[data.id_estudiante] = 0;
    }

    $.ajax({
        type: "GET",
        url: "usuarios-cambiar-estado.php",
        data: datos,
        success: function(data){
            var mensaje = 'Ocurrió un error inesperado';
            var icon    = 'error';
            if(data == 1) {
                mensaje = 'El estudiante fue bloqueado';
                icon    = 'success';
                tr.style.backgroundColor="#ff572238";
            } else if(data == 0) {
                mensaje = 'El estudiante fue desbloqueado';
                icon    = 'success';
                tr.style.backgroundColor="";
            } else if(data == 2) {
                mensaje = 'Usted no tiene permisos para esta acción';
                icon    = 'error';
            }

            $.toast({
                heading: 'Acción realizada',
                text: mensaje,
                position: 'bottom-right',
                showHideTransition: 'slide',
                loaderBg: '#26c281',
                icon: icon,
                hideAfter: 5000,
                stack: 6
            });
        }

    });
}

function minimoUno(data) {
    if( parseInt(data.value) <= 0 ) {
        data.value = 1;
    }
}

function mensajeGenerarInforme(datos) {

    var arrayInfo = datos.rel.split('-');
    var config    = arrayInfo[0];
    var sinNotas  = arrayInfo[1];
    var opcion    = arrayInfo[2];
    var url       = datos.name;
    
    if (opcion == 2) {
        var id = datos.id;
        var contenedorMensaje = document.getElementById('mensajeI'+id);
        var nuevoContenido = '<div class="alert alert-success mt-2" role="alert" style="margin-right: 20px;">El informe ya se está generando.</div>';
    }

    var mensajeSinNotas='';

    if (sinNotas > 0) {
        var mensajeSinNotas='Tienes estudiantes a los que les faltan notas por registrar. ';
    }

    if (config == 1) {

        if (opcion == 1) {
            document.getElementById("overlayInforme").style.display = "flex";
            window.location.href = url;
        }

        if (opcion == 2) {
            axios.get(url).then(function(response) {

                    contenedorMensaje.innerHTML = nuevoContenido;
        
                    $.toast({
                        heading: 'Acción realizada',
                        text: 'El informe ya se está generando.',
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
                window.location.href = url;
            });
        }
    } else {
        if (config ==2 ) {
            var mensaje= mensajeSinNotas+'El informe se generará omitiendo los estudiantes que les falten notas por registrar.';
        }

        if (config == 3) {
            var mensaje= mensajeSinNotas+'El informe se generará guardando los estudiantes con el porcentaje que tienen actualmente.';
        }

        Swal.fire({
            title: 'Generar informe',
            text: mensaje+' Desea continuar con la generación de este informe?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si, deseo continuar!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                if (opcion == 1) {
                    document.getElementById("overlayInforme").style.display = "flex";
                    window.location.href = url;
                }

                if (opcion == 2) {
                    axios.get(url).then(function(response) {
                            contenedorMensaje.innerHTML = nuevoContenido;

                            $.toast({
                                heading: 'Acción realizada',
                                text: 'El informe ya se está generando.',
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
                        window.location.href = url;
                    });
                }
            } else {
                document.getElementById("overlayInforme").style.display = "none";
                return false;
            }
        });
    }
}

function mostrarImagen(idFile, idImg) {
    var input = document.getElementById(idFile);
    var imagenSelect = document.getElementById(idImg);
    // Verificar si se seleccionó un archivo
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            imagenSelect.classList.add('animate__animated', 'animate__fadeIn');
            imagenSelect.src = e.target.result;

        };

        // Leer el archivo como una URL de datos
        reader.readAsDataURL(input.files[0]);
    }
}
/**
 * me crea un metodo fetch y creaun metodo de respuesta
 * @param {string} url url la cual se va a ejecutar
 * @param {array} data datos que se enviuaran
 * @param {string} tipo tipo de envio si es un Json o un html
 * @param {boolean} isGet  valida si la peticion es GET
 * @param {string} metodoresponse  meodo que se ecutaran con la respuesta del fetch
 */
function metodoFetch(url, data, tipo, isGet, metodoresponse) {
    var parametros;
    if (tipo == 'json') {
        parametros = {
            method: isGet ? "GET" : "POST", // or 'PUT'
            body: JSON.stringify(data), // data can be `string` or {object}!
            headers: {
                "Content-Type": "application/json"
            }
        }
    } else {
        parametros = {
            method: isGet ? "GET" : "POST", // or 'PUT'
            body: JSON.stringify(data), // data can be `string` or {object}!
            headers: {
                "Content-Type": "text/html"
            }

        }
    }

    fetch(url, parametros)
        .then((res) => tipo == 'json' ? res.json() : res.text())
        .catch((error) => console.error("Error:", error))
        .then(
            function (res) {
                window[metodoresponse](res, data);
            });

}
/**
 * me crea un metodo fetch de manera Asyncrona
 * @param {string} url url la cual se va a ejecutar
 * @param {array} data datos que se enviuaran
 * @param {string} tipo tipo de envio si es un Json o un html
 * @param {boolean} get  valida si la peticion es GET
 */
async function metodoFetchAsync(url, data, tipo, get) {
    if (tipo == 'json') {
        var parametros = {
            method: get ? "GET" : "POST", // or 'PUT'
            body: JSON.stringify(data), // data can be `string` or {object}!
            headers: {
                "Content-Type": "application/json"
            }
        }
    } else {
        var parametros = {
            method: get ? "GET" : "POST", // or 'PUT'
            body: JSON.stringify(data), // data can be `string` or {object}!
            headers: {
                "Content-Type": "text/html"
            }
        }
    }

    var response = await fetch(url, parametros)
        .then((res) => tipo == 'json' ? res.json() : res.text())
        .catch((error) => console.error("Error:", error))
        .then(
            function (res) {
                var result = {
                    parametros: data,
                    data: res
                }
                return result;
            });
    return response;

}

/**
 * muestra modal para comprar modulos
 * @param {int} idModulo
 */
function mostrarModalCompraModulos(idModulo, year) {

    fetch('../compartido/ajax-consultar-modulos.php?idModulo='+(idModulo), {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('tituloModulo').innerHTML = "MÓDULO "+data.nombreModulo;
        document.getElementById('imgModulo').src = data.imgModulo;
        if (data.descripcionModulo !== "") {
            document.getElementById('tituloDescripcion').innerHTML = "DESCRIPCIÓN DEL MÓDULO";
            document.getElementById('descripcionModulo').innerHTML = data.descripcionModulo;
        }
        document.getElementById('enlaceWhatsapp').href = "https://api.whatsapp.com/send?phone=573006075800&text="+data.mensaje;
        document.getElementById('montoModulo').value = data.montoModulo;
        document.getElementById('nombreModulo').value = "MÓDULO "+data.nombreModulo;
        document.getElementById('idModulo').value = idModulo;

        // 🛡️ PROTECCIÓN: Solo ejecutar si socket está disponible (WebSocket habilitado)
        if (typeof socket !== 'undefined') {
            socket.emit("enviar_mensajes_modulos_dev", {
                year: year,
                asunto: 'Un usuario está interesado en el módulo '+data.nombreModulo,
                contenido: data.mensaje
            });
        }

        $("#modalComprarModulo").modal("show");
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

/**
 * muestra modal para comprar paquete
 * @param {int} idPaquete
 */
function mostrarModalCompraPaquete(idPaquete) {

    fetch('../compartido/ajax-consultar-paquete.php?idPaquete='+(idPaquete), {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('tituloPaquete').innerHTML = "PAQUETE "+data.nombrePaquete;
        document.getElementById('imgPaquete').src = data.imgPaquete;
        if (data.descripcionPaquete !== "") {
            document.getElementById('tituloDescripcionPaquete').innerHTML = "DESCRIPCIÓN DEL PAQUETE";
            document.getElementById('descripcionPaquete').innerHTML = data.descripcionPaquete;
        }
        document.getElementById('enlaceWhatsappPaquete').href = "https://api.whatsapp.com/send?phone=573006075800&text="+data.mensaje;
        document.getElementById('montoPaquete').value = data.montoPaquete;
        document.getElementById('nombrePaquete').value = "PAQUETE "+data.nombrePaquete;
        document.getElementById('idPaquete').value = idPaquete;
        document.getElementById('tipoPaquete').value = data.tipoPaquete;

        $("#modalComprarPaquete").modal("show");
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function contadorUsuariosBloqueados(){
    var contadorSolicitudes = document.getElementById("contador_solicitudes");

    fetch('ajax-contar-solicitudes.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.numeroSolicitudes > 0) {
            contadorSolicitudes.innerHTML = data.numeroSolicitudes;
            contadorSolicitudes.classList.remove('hidden');
        }
    })
    .catch(error => {
        contadorSolicitudes.classList.add('hidden');
        console.error('Error:', error);
    });

}

/**
 * Muestra un mensaje de notificación en la esquina inferior derecha.
 * @param {string} mensaje - El mensaje a mostrar.
 * @param {string} [tipo="success"] - El tipo de mensaje, puede ser
 */
function mtdMostrarMensaje(mensaje, tipo = "success") {
    $.toast({
        heading: tipo === "success" ? "Acción realizada" : "Error",
        text: mensaje,
        position: 'bottom-right',
        showHideTransition: 'slide',
        loaderBg: '#26c281',
        icon: tipo,
        hideAfter: 5000,
        stack: 6
    });
}

/**
 * Desactiva el botón con un mensaje de carga.
 * @param {HTMLElement} boton - El botón a activar o desactivar.
 * @param {string} [texto="cargando... "] - El texto que se mostrará en el botón mientras está cargando.
 *  
 */
function mtdActivarLoadBoton(boton,texto = "cargando... ") {
    boton.innerHTML ='<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + texto;
    boton.disabled = true;
}

/**
 * Activa el botón y restablece su texto.
 * @param {HTMLElement} boton - El botón a desactivar.
 * @param {string} [texto="Cargar"] - El texto que se mostrará en el botón cuando esté desactivado.
 * 
 */
function mtdDesactivarLoadBoton(boton,texto = "Cargar") {
    boton.innerHTML = texto;
    boton.disabled = false;
}

/**
 * Muestra un overlay de carga en la página.
 * 
 */
function mtdActivarLoadPagina() {
   document.getElementById("overlay").style.display = "flex";
}

/**
 * Oculta el overlay de carga en la página.
 * 
 */
function mtdDesactivarLoadPagina() {
    document.getElementById("overlay").style.display = "none";
}

//document.addEventListener('DOMContentLoaded', contadorUsuariosBloqueados);