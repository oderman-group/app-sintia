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
                title: 'AVISPATE que se ha perdido la conexión!', 
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
function sweetConfirmacion(titulo, mensaje, tipo ='question', varHeref) {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo,
        showCancelButton: true,
        confirmButtonText: 'Si!',
        cancelButtonText: 'No!'
    }).then((result) => {
        if (result.isConfirmed) {
            if(varHeref === null) {
                return true;
            } else {
                window.location.href=varHeref;
            }

        } else {

            if(varHeref === null) {
                return false;
            } else {
                window.location.href='#';
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

    if (dato.title !== '') {

        let variable = (dato.title);
        var varObjet = JSON.parse(variable);
        var input = document.getElementById(parseInt(varObjet.idInput));

    }

    var url = dato.name;
    var id = dato.id;
    var registro = document.getElementById("reg" + id);
    var evaPregunta = document.getElementById("pregunta" + id);
    var publicacion = document.getElementById("PUB" + id);

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
            if (typeof id !== "undefined" && id !== "") {

                if (typeof varObjet !== "undefined") {
                    var input = document.getElementById(parseInt(varObjet.idInput));
                    if (varObjet.tipo === 2 || varObjet.tipo === 5) {
                        var input = document.getElementById(varObjet.idInput);
                    }
                }

                axios.get(url).then(function(response) {
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

const estudiantesPorEstados = {};

function cambiarEstadoMatricula(data) {
    let idHref = 'estadoMatricula'+data.id_estudiante;
    let href   = document.getElementById(idHref);
    
    if (!estudiantesPorEstados.hasOwnProperty(data.id_estudiante)) {
        estudiantesPorEstados[data.id_estudiante] = data.estado_matricula;
    }

    if(estudiantesPorEstados[data.id_estudiante] == 1) {
        href.innerHTML = `<span class="text-warning">No Matriculado</span>`;
        estudiantesPorEstados[data.id_estudiante] = 4;
    } else {
        href.innerHTML = `<span class="text-success">Matriculado</span>`;
        estudiantesPorEstados[data.id_estudiante] = 1;
    }

    let datos = "nuevoEstado="+estudiantesPorEstados[data.id_estudiante]+
                "&idEstudiante="+data.id_estudiante;

    $.ajax({
        type: "POST",
        url: "ajax-cambiar-estado-matricula.php",
        data: datos,
        success: function(data){
            $('#respuestaCambiarEstado').empty().hide().html(data).show(1);
        }

    });
}


const estudiantesPorEstadosBloqueo = {};

function cambiarBloqueo(data) {

    if(data.bloqueado == null || data.bloqueado == '') {
        data.bloqueado = 0;
    }

    let tr   = document.getElementById("EST"+data.id_estudiante);
    
    if (!estudiantesPorEstadosBloqueo.hasOwnProperty(data.id_estudiante)) {
        estudiantesPorEstadosBloqueo[data.id_estudiante] = data.bloqueado;
    }

    var estadoFinal = estudiantesPorEstadosBloqueo[data.id_estudiante];
    let datos = "&idR="+btoa(data.id_usuario.toString())+
                "&lock="+btoa(estadoFinal.toString())
                ;

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

function mensajeGenerarInforme(datos){
    arrayInfo   = datos.rel.split('-');
    var config= arrayInfo[0];
    var sinNotas= arrayInfo[1];
    var opcion= arrayInfo[2];
    var url = datos.name;
    
    if(opcion==2){   
        var id = datos.id;
        var contenedorMensaje = document.getElementById('mensajeI'+id);
        var nuevoContenido = '<div class="alert alert-success mt-2" role="alert" style="margin-right: 20px;">El informe ya se está generando.</div>';
    }

    var mensajeSinNotas='';
    if(sinNotas>0){
        var mensajeSinNotas='Tienes estudiantes a los que les faltan notas por registrar. ';
    }

    if(config==1){
        if(opcion==1){
            window.location.href = url;
        }
        if(opcion==2){            
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
    }else{
        if(config==2){
            var mensaje= mensajeSinNotas+'El informe se generará omitiendo los estudiantes que les falten notas por registrar.';
        }
        if(config==3){
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
                if(opcion==1){
                    window.location.href = url;
                }
                if(opcion==2){            
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
            }else{
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
 * @param {boolean} get  valida si la peticion es GET
 * @param {string} metodoresponse  meodo que se ecutaran con la respuesta del fetch
 */
function metodoFetch(url,data,tipo,get,metodoresponse) {
           if(tipo=='json'){
           var parametros= {
                method:get?"GET":"POST", // or 'PUT'
                body: JSON.stringify(data), // data can be `string` or {object}!
                headers: {
                    "Content-Type": "application/json"
                }
            }
           }else{
            var parametros= {
                method:get?"GET":"POST", // or 'PUT'
                body: JSON.stringify(data), // data can be `string` or {object}!
                headers: {
                    "Content-Type": "text/html"
                }
            }
           }

			fetch(url,parametros)
				.then((res) => tipo=='json'?res.json():res.text())
				.catch((error) => console.error("Error:", error))
				.then(
					function(res) {
                        window[metodoresponse](res,data);
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

        socket.emit("enviar_mensajes_modulos_dev", {
            year: year,
            asunto: 'Un usuario está interesado en el módulo '+data.nombreModulo,
            contenido: data.mensaje
        });

        $("#modalComprarModulo").modal("show");
    })
    .catch(error => {
        console.error('Error:', error);
    });
}