<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0004'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php"); ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="../../config-general/assets/css/chat.css" rel="stylesheet">
<style>
</style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />


<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">

        <?php include("../compartido/menu.php"); ?>

        <!-- start page content -->
        <div class="page-content-wrapper">

            <div class="page-content" style="background-color: #ffffff;">
                <!-- <p style="font-size: 4rem;">&#x1F642; &#x1F3F9; &#x1F958; &#x1F96A; &#x1F99C; &#x1F9CA; &#x1FAA2; &#x1F6DD; &#x1FABC;</p> -->
                <div class="row">
                    <div class="card2 chat-app">
                        <div id="plist" class="people-list">
                            <div class="input-group">
                                <form class="search-form-opened" action="#" method="GET" name="busqueda" style="background-color: rgb(0, 0, 0,0.3); margin: 0;">
                                    <div class="input-group">
                                        <input type="text" id="search" name="search" class="form-control" placeholder="Buscar usuario...">
                                        <span class="input-group-btn">
                                            <span class="input-group-btn">
                                                <a href="javascript:;" class="btn submit" style="color: #000;">
                                                    <i class="icon-magnifier"></i>
                                                </a>
                                            </span>
                                    </div>
                                </form>
                            </div>
                            <ul class="list-unstyled chat-list mt-2 mb-0" id="contenedorOriginial">
                                <?php
                                $consultaUsuariosOnline = mysqli_query($conexion, "SELECT uss_id, uss_nombre, uss_apellido1, uss_foto, uss_estado FROM ".BD_GENERAL.".usuarios 
                                        WHERE uss_estado=1 AND uss_bloqueado=0 AND uss_id!='" . $_SESSION['id'] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} 
                                        ORDER BY uss_ultimo_ingreso DESC
                                        LIMIT 10");
                                if (mysqli_num_rows($consultaUsuariosOnline) > 0) {
                                    while ($datosUsuriosOnline = mysqli_fetch_array($consultaUsuariosOnline, MYSQLI_BOTH)) {
                                        $fotoPerfilUsrOnline = $usuariosClase->verificarFoto($datosUsuriosOnline['uss_foto']);
                                ?>
                                        <li class="clearfix" onclick="mostrarChat(this)" id="<?= $datosUsuriosOnline['uss_id'] ?>">
                                            <img src="<?= $fotoPerfilUsrOnline ?>" alt="avatar">
                                            <div class="about">
                                                <div class="name" id="nombre_<?= $datosUsuriosOnline['uss_id'] ?>"><?= $datosUsuriosOnline['uss_nombre'] . ' ' . $datosUsuriosOnline['uss_apellido1'] ?></div>

                                                <div class="status"> <i class="fa fa-circle online"></i> online <span id="notificacion_<?= $datosUsuriosOnline['uss_id'] ?>"> </div>
                                            </div>
                                        </li>
                                    <?php
                                    }
                                }
                                $consultaUsuariosOffline = mysqli_query($conexion, "SELECT uss_id, uss_nombre, uss_apellido1, uss_foto, uss_estado FROM ".BD_GENERAL.".usuarios 
                                        WHERE uss_estado=0 AND uss_bloqueado=0 
                                        AND uss_id!='" . $_SESSION['id'] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} 
                                        ORDER BY uss_ultima_salida DESC
                                        LIMIT 5");
                                if (mysqli_num_rows($consultaUsuariosOffline) > 0) {
                                    while ($datosUsuriosOffline = mysqli_fetch_array($consultaUsuariosOffline, MYSQLI_BOTH)) {
                                        $fotoPerfilUsrOffline = $usuariosClase->verificarFoto($datosUsuriosOffline['uss_foto']);
                                    ?>
                                        <li class="clearfix" onclick="mostrarChat(this)" id="<?= $datosUsuriosOffline['uss_id'] ?>">
                                            <img src="<?= $fotoPerfilUsrOffline ?>" alt="avatar">
                                            <div class="about">
                                                <div class="name" id="nombre_<?= $datosUsuriosOffline['uss_id'] ?>"><?= $datosUsuriosOffline['uss_nombre'] . ' ' . $datosUsuriosOffline['uss_apellido1'] ?></div>
                                                <div class="status"> <i class="fa fa-circle offline"></i> offline <span id="notificacion_<?= $datosUsuriosOnline['uss_id'] ?>"> </div>
                                            </div>
                                        </li>
                                <?php
                                    }
                                }
                                ?>
                            </ul>
                            <ul class="list-unstyled chat-list mt-2 mb-0" id="contenedorSearch" style="display:none;">
                            </ul>
                        </div>
                        <div class="chat2" id="contenedorChat" style="display:none;">
                        </div>
                        <div class="bienvenida" id="contenedorBienvenida">
                            <div class="mensajeBienvenida">
                                <h1><span style='font-family:Arial; font-weight:bold;'>Te damos la bienvenida, <?= $datosUsuarioActual['uss_nombre'] ?></samp></h1>
                                <p>¡Todo listo para chatear! Comencemos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // var urlApi = 'http://localhost:3000';
            var urlApi = 'http://plataformasintia.com:3000';
            var socket = io(urlApi, {
                transports: ['websocket', 'polling', 'flashsocket']
            });
            var chat_remite_usuario = '<?php echo $idSession ?>';
            var chat_destino_usuario = "";
            var foto_url_uss = "<?php echo $datosUsuarioActual["uss_foto"] ?>";
            var nombre_uss = "<?php echo $datosUsuarioActual["uss_nombre"] . " " . $datosUsuarioActual["uss_apellido1"] ?>";
            let iconGrabar = document.getElementById('iconGrabar');
            let audioReprodutor = document.getElementById('audioReprodutor');
            let btnlimpiar = document.getElementById('btnlimpiar');
            let btnDetener = document.getElementById('btnDetener');
            let imputmensaje = document.getElementById("imputMensaje");
            const _chatTipoMensaje = "<?php echo CHAT_TIPO_MENSAJE ?>";
            const _chatTipoDocumento = "<?php echo CHAT_TIPO_DOCUMENTO ?>";
            const _chatTipoImagen = "<?php echo CHAT_TIPO_IMAGEN ?>";
            const _chatTipoAudio = "<?php echo CHAT_TIPO_AUDIO ?>";
            let tipoMensaje = _chatTipoMensaje;


            const constraints = {
                audio: true
            };
            let mediaRecorder;
            let audioChunks = [];
            let audioBlob;

            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.ondataavailable = function(e) {
                        if (e.data.size > 0) {
                            audioChunks.push(e.data);
                        }
                    };

                    mediaRecorder.onstop = function() {
                        audioBlob = new Blob(audioChunks, {
                            type: 'audio/wav'
                        });
                        document.getElementById('audioReprodutor').src = URL.createObjectURL(audioBlob);
                        const audioURL = URL.createObjectURL(audioBlob);
                        audioReprodutor.src = audioURL;
                        audioReprodutor.style.display = 'block';
                        inputAudio = document.getElementById('cargarAudio');
                        inputAudio.src = audioURL
                    };


                })
                .catch(function(err) {
                    console.error('No se puede acceder al micrófono: ' + err);
                });


            socket.emit('join', "sala_" + chat_remite_usuario);

            socket.on("notificacion_chat", (data) => {
                console.log(data);
                uss_id = data["chat_remite_usuario"];
                nombre_uss_notifica = data["nombre_uss"];
                foto_url_uss_notifica = "../files/fotos/" + data["foto_url_uss"];


                listaUsuarios = document.getElementById('contenedorOriginial');
                liUsuario = document.getElementById(uss_id);
                divNombre = document.getElementById("nombre_" + uss_id);

                // si existe se elimina de la lista
                if (liUsuario !== null) {
                    listaUsuarios.removeChild(liUsuario);
                }
                // Crea un nuevo elemento li
                const elementoHTML = notificacionUsuario(uss_id, nombre_uss_notifica, foto_url_uss_notifica, 'online');
                const nuevoElemento = document.createElement('li');
                nuevoElemento.id = uss_id;
                nuevoElemento.className = "clearfix";
                nuevoElemento.innerHTML = elementoHTML;
                nuevoElemento.onclick = function() {
                    mostrarChat(nuevoElemento);
                };



                // Agrega el nuevo elemento li al principio de la lista
                listaUsuarios.insertBefore(nuevoElemento, listaUsuarios.firstChild);
                spanNotificacion = document.getElementById("notificacion_" + uss_id);
                spanNotificacion.className = "badge headerBadgeColor2";
                spanNotificacion.innerHTML = "Nuevo";

            });


            function mostrarChat(datos) {
                console.log("entre a la sala_chat_" + chat_remite_usuario + "_" + chat_destino_usuario);
                socket.emit("leave", "sala_chat_" + chat_remite_usuario + "_" + chat_destino_usuario);
                socket.emit("leave", "listar_chat_" + chat_remite_usuario + "_" + chat_destino_usuario);
                var id = datos.id;

                console.log("id--->" + id);
                $("#contenedorChat").empty().hide();
                if (id !== '') {
                    $.ajax({
                        type: "POST",
                        url: "ajax-chat.php",
                        data: {
                            id: id
                        },
                        success: function(response) {
                            $("#contenedorBienvenida").hide();
                            $("#contenedorChat").show();

                            $.each(response, function(index, item) {
                                var html = '<div class="chat-header2 clearfix">' +
                                    '<div class="row">' +
                                    '<div class="col-lg-6">' +
                                    '<a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">' +
                                    '<img src="' + item.fotoPerfil + '" alt="avatar">' +
                                    '</a>' +
                                    '<div class="chat-about">' +
                                    '<h6 class="m-b-0">' + item.nombre + '</h6>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="chat-history" id="chatHistory">' +
                                    '<ul class="m-b-0" id="contenido_chat">' +

                                    '</ul>' +
                                    '</div>' +

                                    '<div class="chat-message clearfix">' +
                                    '<div id="contenedorImagen"></div>' +
                                    '<audio controls id="audioReprodutor" style="display: none;"></audio>' +
                                    '<div id="miBoton" class="input-group mb-0">' +
                                    '<div class="input-group-prepend">' +
                                    '<span class="input-group-text" onClick="enviarMensaje()"><i class="fa fa-send"></i></span>' +
                                    '</div>' +
                                    '<div class="input-group-prepend">' +
                                    '<span class="input-group-text" onclick="cargarFile(&apos;' + _chatTipoImagen + '&apos;,cargarImagen)" data-toggle="popover" ><i class="fa fa-image"></i></span>' +
                                    '</div>' +
                                    '<div class="input-group-prepend">' +
                                    '<span class="input-group-text" onclick="cargarFile(&apos;' + _chatTipoDocumento + '&apos;,cargarArchivo)" ><i class="fa-solid fa-paperclip"></i></span>' +
                                    '</div>' +

                                    '<div class="input-group-prepend">' +
                                    '<span class="input-group-text" id="btnGrabar" onclick="iniciarGrabacion()" ><i id="iconGrabar" class="fa-solid fa-microphone"></i></span>' +
                                    '<span class="input-group-text" id="btnDetener" onclick="detenerGrabacion()"  style="display: none;" ><i class="fa-solid fa-stop"></i></span>' +
                                    '</div>' +
                                    '<input type="text" id="imputMensaje" onkeydown="ejecutarEnter(event)"  class="form-control" placeholder="Escriba su mensaje aqui...">' +
                                    '<div class="input-group-prepend">' +
                                    '<span id="btnlimpiar" class="input-group-text" onclick="limpiar()" style="display: none;"><i class="fa-solid fa-trash"></i></span>' +
                                    '</div>' +
                                    '</div>' +
                                    '<input type="file" id="cargarImagen"   name="imagen"   style="display: none;"  accept="image/*" >' +
                                    '<input type="file" id="cargarArchivo"  name="archivo"  style="display: none;">' +
                                    '<input type="file" id="cargarAudio"    name="audio"    style="display: none;"   accept="audio/*">   ' +
                                    '</div>';
                                $("#contenedorChat").append(html);
                                const listaItems = document.querySelectorAll("#plist li");
                                listaItems.forEach((li) => {
                                    if (li.classList.contains("active")) {
                                        li.classList.remove("active");
                                    }
                                });
                                document.getElementById(item.datosUsuarios["uss_id"]).classList.add("active");
                                var chatElement = document.getElementById("chatHistory");
                                var contenido_chat = document.getElementById("contenido_chat");
                                audioReprodutor = document.getElementById('audioReprodutor');
                                btnlimpiar = document.getElementById('btnlimpiar');
                                btnDetener = document.getElementById('btnDetener');
                                iconGrabar = document.getElementById('iconGrabar');
                                imputmensaje = document.getElementById("imputMensaje");
                                chat_remite_usuario = <?php echo $idSession ?>;
                                chat_destino_usuario = item.datosUsuarios["uss_id"];
                                // listarChat(chat_remite_usuario,chat_destino_usuario); 
                                socket.emit('join', "sala_chat_" + chat_remite_usuario + "_" + chat_destino_usuario);
                                listarChat(chat_remite_usuario, chat_destino_usuario);
                                socket.on("listar_chat_" + chat_remite_usuario + "_" + chat_destino_usuario, (response) => {
                                    console.log(response);
                                    data = response["data"];
                                    data.forEach(elemento => {
                                        console.log(chat_remite_usuario + "-" + elemento.chat_remite_usuario + " = " + elemento.chat_mensaje);
                                        if (chat_remite_usuario == elemento.chat_remite_usuario) {
                                            fechaCompleta = verificarFecha(elemento.chat_fecha_registro);
                                            contenido_chat.innerHTML += htmlEmisor(elemento.chat_mensaje, fechaCompleta, elemento.chat_tipo + '', elemento.chat_url_file);
                                        } else {
                                            fechaCompleta = verificarFecha(elemento.chat_fecha_registro);
                                            contenido_chat.innerHTML += htmlDestino(elemento.chat_mensaje, fechaCompleta, foto_url_uss_destino, elemento.chat_tipo + '', elemento.chat_url_file);
                                        }
                                        chatElement.scrollTop = chatElement.scrollHeight;
                                    });
                                });
                                socket.on("nuevo_mensaje_chat", (data) => {
                                    chatElement = document.getElementById("chatHistory");
                                    console.log(data);
                                    mensaje = data["body"]["chat_mensaje"];
                                    fecha = data["body"]["chat_fecha_registro"];
                                    tipo = data["body"]["chat_tipo"];
                                    url = data["body"]["chat_url_file"];
                                    fechaCompleta = verificarFecha(fecha);
                                    contenido_chat.innerHTML += htmlDestino(mensaje, fechaCompleta, foto_url_uss_destino, tipo + '', url);
                                    chatElement.scrollTop = chatElement.scrollHeight;
                                });
                                divNombre = document.getElementById("nombre_" + chat_destino_usuario);
                                spanNotificacion = document.getElementById("notificacion_" + chat_destino_usuario);
                                foto_url_uss_destino = item.fotoPerfil;
                                divNombre.style.fontWeight = "400";
                                spanNotificacion.className = "";
                                spanNotificacion.innerHTML = "";
                            });
                        }
                    });
                }
            };

            function ejecutarEnter(event) {
                if (event.key === "Enter") {
                    enviarMensaje();
                }
            };

            function notificacionUsuario(id, nombreCompleto, fotoPerfil, estado) {
                Html = "";
                // '<li class="clearfix" onclick="mostrarChat(this)"  id="'+id+'"   >' +
                Html = '<img src="' + fotoPerfil + '" alt="avatar" />' +
                    '<div class="about">' +
                    '<div class="name" id="nombre_' + id + '" style="font-weight: bold;" >' + nombreCompleto + '</div>' +
                    '<div class="status" > <i class="fa fa-circle online"></i> ' + estado + ' <span  id="notificacion_' + id + '"> </div>' +
                    '</div>';
                return Html;
            };

            function htmlEmisor(mensaje, hora, tipo = "1", url = "") {
                liHtml = "";
                imageHtml = "";
                switch (tipo) {
                    case _chatTipoImagen:
                        imageHtml = '<img src="../files/chat/imagen/' + url + '" alt="avatar">';
                        mensaje = '<div class="cols-12">' + mensaje + '</div>';
                        break;
                    case _chatTipoDocumento:
                        imageHtml = '<a href="../files/chat/documento/' + url + '" download="' + url + '">' +
                            '<img src="../files/iconos/file.png" >' +
                            ' descargar documento</a>'
                        mensaje = '<div class="cols-12">' + mensaje + '</div>';
                        break;
                    case _chatTipoAudio:
                        imageHtml = '<audio controls src="../files/chat/audio/' + url + '" ></audio>';
                        mensaje = '<div class="cols-12">' + mensaje + '</div>';
                        break;
                    case _chatTipoMensaje:
                        break;
                }
                liHtml = '<li class="clearfix">' +
                    '<div class="message-data">' +
                    '<span class="message-data-time">' + hora + '</span>' +
                    '</div>' +
                    '<div class="message my-message">' +
                    imageHtml +
                    mensaje +
                    '</div>' +
                    '</li>';

                return liHtml;
            };

            function htmlDestino(mensaje, hora, imagenUrl, tipo = "1", url = "") {
                liHtml = "";
                imageHtml = "";
                switch (tipo) {
                    case _chatTipoImagen:
                        imageHtml = '<img src="../files/chat/imagen/' + url + '" alt="avatar">';
                        mensaje = '<div class="cols-12">' + mensaje + '</div>';
                        break;
                    case _chatTipoDocumento:
                        imageHtml = '<a href="../files/chat/documento/' + url + '" download="' + url + '">' +
                            '<img src="../files/iconos/file.png" >' +
                            ' descargar documento</a>'
                        mensaje = '<div class="cols-12">' + mensaje + '</div>';
                        break;
                    case _chatTipoAudio:
                        imageHtml = '<audio controls src="../files/chat/audio/' + url + '" ></audio>';
                        mensaje = '<div class="cols-12">' + mensaje + '</div>';
                        break;
                    case _chatTipoMensaje:
                        break;
                }
                imageHtml = '<li class="clearfix">' +
                    '<div class="message-data text-right">' +
                    '<span class="message-data-time">' + hora + '</span>' +
                    '<img src="' + imagenUrl + '" alt="avatar">' +
                    '</div>' +
                    '<div class="message other-message float-right"> ' +
                    imageHtml +
                    mensaje +
                    '</div>' +
                    '</li>';
                return imageHtml;
            };

            function listarChat(uss_remite, uss_detino) {
                chatElement = document.getElementById("chatHistory");
                socket.emit("listar_mensajes_chat", {
                    chat_remite_usuario: chat_remite_usuario,
                    chat_destino_usuario: chat_destino_usuario,
                    salaChat: "sala_chat_" + chat_remite_usuario + "_" + chat_destino_usuario
                });
            }


            function enviarMensaje() {
                mensaje = imputMensaje.value;
                chatElement = document.getElementById("chatHistory");
                switch (tipoMensaje) {
                    case _chatTipoImagen:
                        enviarArchivo(tipoMensaje, "cargarImagen");
                        break;
                    case _chatTipoDocumento:
                        enviarArchivo(tipoMensaje, "cargarArchivo");
                        break;
                    case _chatTipoAudio:
                        enviarArchivo(tipoMensaje, "cargarAudio");
                        break;
                    case _chatTipoMensaje:
                        if (mensaje.trim() === "" || mensaje === null || typeof mensaje === 'undefined') {
                            Swal.fire(
                                '',
                                'Ingrese un mensaje',
                                'info'
                            )
                        } else {
                            enviarArchivo(tipoMensaje, "");
                        };

                        break;

                }


            };

            function cargarFile(tipo, idImput) {
                limpiar();
                const inputImagen = idImput;
                tipoMensaje = tipo;
                inputImagen.click();
                inputImagen.addEventListener('change', mostrarImagen);
                btnlimpiar.style.display = 'block';
                mostrarImagen(tipo);
                imputMensaje.focus();
            };

            function enviarArchivo(tipo, idImput) {
                var formData = new FormData();
                var inputFile = document.getElementById(idImput);
                mensaje = imputMensaje.value;
                formData.append("tipo", tipo);
                if (tipoMensaje != _chatTipoMensaje) {
                    formData.append(tipo, (tipoMensaje == _chatTipoAudio) ? audioBlob : inputFile.files[0]);
                    $.ajax({
                        type: "POST",
                        url: "../compartido/chat-guardar-imagen-ajax.php",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            console.log(data);
                            socket.emit("enviar_mensaje_chat", {
                                chat_tipo: tipoMensaje,
                                chat_url_file: data,
                                foto_url_uss: foto_url_uss,
                                nombre_uss: nombre_uss,
                                chat_fecha_registro: new Date(),
                                chat_remite_usuario: chat_remite_usuario,
                                chat_destino_usuario: chat_destino_usuario,
                                sala: "sala_" + chat_destino_usuario,
                                salaChat: "sala_chat_" + chat_destino_usuario + "_" + chat_remite_usuario,
                                chat_mensaje: mensaje
                            });
                            contenido_chat.innerHTML += htmlEmisor(mensaje, verificarFecha(new Date()), tipoMensaje, data);
                            limpiar();
                            chatElement.scrollTop = chatElement.scrollHeight;
                        }
                    });
                } else {
                    socket.emit("enviar_mensaje_chat", {
                        chat_tipo: tipoMensaje,
                        foto_url_uss: foto_url_uss,
                        nombre_uss: nombre_uss,
                        chat_fecha_registro: new Date(),
                        chat_remite_usuario: chat_remite_usuario,
                        chat_destino_usuario: chat_destino_usuario,
                        sala: "sala_" + chat_destino_usuario,
                        salaChat: "sala_chat_" + chat_destino_usuario + "_" + chat_remite_usuario,
                        chat_mensaje: mensaje
                    });
                    contenido_chat.innerHTML += htmlEmisor(mensaje, verificarFecha(new Date()));
                    limpiar();
                    chatElement.scrollTop = chatElement.scrollHeight;
                }

            };

            function mostrarImagen(tipo) {
                const contenedorImagen = document.getElementById('contenedorImagen');
                const inputImagen = document.getElementById('cargarImagen');
                const inputarchivo = document.getElementById('cargarArchivo');
                const imagenSeleccionada = inputImagen.files[0];
                const archivoSeleccionado = inputarchivo.files[0];

                if (tipo == "imagen") {
                    inputarchivo.value = '';
                } else if (tipo == "archivo") {
                    inputImagen.value = '';
                }


                if (imagenSeleccionada) {
                    const imagenURL = URL.createObjectURL(imagenSeleccionada);
                    // Crear un elemento de imagen y asignar la URL
                    const imagen = document.createElement('img');
                    imagen.src = imagenURL;
                    imagen.classList.add("img-thumbnail");
                    imagen.style.height = "300px";
                    // Agregar la imagen al contenedor
                    contenedorImagen.innerHTML = '';
                    contenedorImagen.appendChild(imagen);

                } else if (archivoSeleccionado) {
                    // Crear un elemento de imagen y asignar la URL
                    const icon = document.createElement('i');
                    icon.classList.add("fas", "fa-file-archive", "fa-lg");
                    icon.style.height = "50px";
                    icon.textContent = " " + archivoSeleccionado.name
                    // Agregar la imagen al contenedor
                    contenedorImagen.innerHTML = '';
                    contenedorImagen.appendChild(icon);

                } else {
                    contenedorImagen.innerHTML = 'No se ha seleccionado una imagen.';
                }
            };

            function iniciarGrabacion() {
                limpiar();
                audioChunks = [];
                tipoMensaje = _chatTipoAudio;
                mediaRecorder.start();
                console.log('Grabación iniciada.');
                btnDetener.style.display = 'block';
                iconGrabar.classList.remove("fa-microphone");
                iconGrabar.classList.add("fa-record-vinyl", "fa-beat-fade");
                // Establecemos un temporizador para detener la grabación después de 60 segundos.
                tiempoDeGrabacion = setTimeout(function() {
                    detenerGrabacion(); // Simula hacer clic en el botón "Detener Grabación".
                }, 69000);
            };

            function detenerGrabacion() {
                mediaRecorder.stop();
                console.log('Grabación detenida.');
                btnDetener.style.display = 'none';
                iconGrabar.classList.remove("fa-record-vinyl", "fa-beat-fade");
                iconGrabar.classList.add("fa-microphone");
                btnlimpiar.style.display = 'block';
                imputMensaje.focus();
            };

            function limpiar() {
                tipoMensaje = _chatTipoMensaje;
                imputMensaje.value = "";
                inputImagen = document.getElementById('cargarImagen');
                inputArchivo = document.getElementById('cargarArchivo');
                inputAudio = document.getElementById('cargarAudio');
                inputArchivo.value = '';
                inputImagen.value = '';
                inputAudio.value = '';
                contenedorImagen.innerHTML = '';
                audioReprodutor.src = "";
                audioReprodutor.style.display = 'none';
                btnlimpiar.style.display = 'none';
                btnDetener.style.display = 'none';
            };



            $(document).ready(function() {
                $('#search').on('input', function() {
                    var search = $(this).val();

                    if (search.length > 2) {
                        $.ajax({
                            type: "POST",
                            url: "ajax-buscador.php",
                            data: {
                                search: search
                            },
                            success: function(response) {
                                if (search === '') {
                                    // Si el término de búsqueda está vacío, vaciar el contenedor de resultados y ocultarlo
                                    $("#contenedorSearch").empty().hide();
                                    $("#contenedorOriginial").show();
                                } else {
                                    // Limpiar los resultados anteriores
                                    $("#contenedorSearch").empty();

                                    if (response.length > 0) {
                                        // Agregar los nuevos productos a la lista
                                        $("#contenedorSearch").show();
                                        $("#contenedorOriginial").hide();
                                        $.each(response, function(index, item) {
                                            var estado = 'offline';
                                            if (item.datosUsuarios.uss_estado == 1) {
                                                var estado = 'online';
                                            }
                                            var html = '<li class="clearfix" id="' + item.datosUsuarios.uss_id + '">' +
                                                '<img src="' + item.fotoPerfil + '" alt="avatar">' +
                                                '<div class="about">' +
                                                '<div class="name">' + item.nombre + '</div>' +
                                                '<div class="status"> <i class="fa fa-circle ' + estado + '"></i> ' + estado + ' </div>' +
                                                '</div>' +
                                                '</li>';

                                            $("#contenedorSearch").append(html);
                                        });
                                    } else {
                                        $("#contenedorSearch").hide();
                                        $("#contenedorOriginial").show();
                                    }
                                }
                            }
                        });
                    } else {
                        // Si el término de búsqueda tiene dos letras, vaciar el contenedor de resultados y ocultarlo
                        $("#contenedorSearch").empty().hide();
                        $("#contenedorOriginial").show();
                    }
                });

            });

            function obtenerDiaDeLaSemana(fecha) {
                const diasDeLaSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
                const fechaIngresada = new Date(fecha);
                const numeroDeDia = fechaIngresada.getDay();
                const nombreDelDia = diasDeLaSemana[numeroDeDia];
                return nombreDelDia;
            }

            function validarFachasIguales(fechaInicial, fechaFinal) {
                if (
                    fechaInicial.getDate() === fechaFinal.getDate() &&
                    fechaInicial.getMonth() === fechaFinal.getMonth() &&
                    fechaInicial.getFullYear() === fechaFinal.getFullYear()
                ) {
                    return true;
                } else {
                    return false;
                }
            }

            function verificarFecha(fecha) {
                fechaActual = new Date(); // Obtener la fecha actual
                fechaIngresada = new Date(fecha); // Convertir la fecha ingresada en un objeto Date
                fechahora = fechaIngresada.toLocaleTimeString('en-US');
                const [month, day, year] = [
                    fechaIngresada.getMonth(),
                    fechaIngresada.getDate(),
                    fechaIngresada.getFullYear(),
                ];

                // Verificar si es hoy
                if (validarFachasIguales(fechaIngresada, fechaActual)) {
                    return fechahora;
                }

                // Calcular la fecha de ayer
                const ayer = new Date(fechaActual);
                ayer.setDate(ayer.getDate() - 1);
                // Verificar si es ayer
                if (validarFachasIguales(fechaIngresada, ayer)) {
                    return fechahora + ',Ayer';
                }
                // Calcular la fecha de la semana pasada
                const semanaPasada = new Date(fechaActual);
                semanaPasada.setDate(semanaPasada.getDate() - 7);
                // Verificar si es la semana pasada
                if (fechaIngresada > semanaPasada && fechaIngresada < ayer) {
                    return fechahora + ' ,' + obtenerDiaDeLaSemana(fecha);
                }

                return "(" + day + "/" + month + "/" + year + ")";
            }
        </script>
        <!-- end page content -->

    </div>
    <!-- end page container -->
    <?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8"></script>
<script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js" charset="UTF-8"></script>
<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- dropzone -->
<script src="../../config-general/assets/plugins/dropzone/dropzone.js"></script>
<!--tags input-->
<script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
<script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<!-- end js include path -->
<script src="../ckeditor/ckeditor.js"></script>
</body>

</html>