<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0209'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php"); ?>

<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="../../config-general/assets/css/chat2.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
<style>
    .contenedor2 {

        position: relative;
        padding: 10px;
    }

    .div-interior2 {
        position: absolute;
        bottom: -40px;
        left: 40px;
        /* Establece el color del elemento i interior */
    }

    .esquina-superior {
        position: absolute;
        top: 15px;
        left: 10px;
        transform: translateX(-50%);
        /* Establece el color del span en la esquina superior izquierda */
    }
</style>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>
    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">

        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">

            <div class="page-content">
                </br>
                <div class="row">
                    <div class="col-3">
                        <div class="col-12 chat-height">
                            <div class="row">
                                <div class="col-12">
                                    </br>
                                    <div class="form-icon-container">
                                        <input class="form-control form-icon-input" type="text" onkeyup="buscarUsuario(this.value)" style="background-color: rgb(0, 0, 0,0.1); margin: 0;" placeholder="Buscar usuarios..">
                                        <svg class="svg-inline--fa fa-user text-900 fs--1 form-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" role="img" viewBox="0 0 448 512">
                                            <path fill="currentColor" d="M224 256c70.7 0 128-57.31 128-128s-57.3-128-128-128C153.3 0 96 57.31 96 128S153.3 256 224 256zM274.7 304H173.3C77.61 304 0 381.6 0 477.3c0 19.14 15.52 34.67 34.66 34.67h378.7C432.5 512 448 496.5 448 477.3C448 381.6 370.4 304 274.7 304z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    </br>
                                    <div class="btn-group btn-group-toggle col-12" data-toggle="buttons">
                                        <label id="btn-chat" class="btn btn-primary  col-6">
                                            <input type="radio" name="options" onchange="mostrarListarUsuarios(1)" autocomplete="off" checked>
                                            <i class="fa-regular fa-comment"></i>Chat
                                        </label>
                                        <label id="btn-all" class="btn btn-secondary  col-6">
                                            <input type="radio" name="options" onchange="mostrarListarUsuarios(2)" autocomplete="off">
                                            <i class="fa-solid fa-earth-americas"></i>All
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    </br>

                                    <div id="chat-list" class="people-list scrollable-div">
                                        <ul class="list-unstyled chat-list mt-2 mb-0" id="listaChat">
                                            <?php
                                            $chats = [];
                                            $consultaUsuariosChat = mysqli_query(
                                                $conexion,
                                                "SELECT 
                                                uss_id,
                                                uss_nombre,
                                                uss_foto,
                                                uss_apellido1,
                                                uss_estado,
                                                chat_remite_usuario,
                                                chat_remite_institucion,
                                                chat_visto,
                                                chat_tipo,
                                                chat_destino_usuario,                                                
                                                chat_destino_institucion,
                                                $baseDatosSocial.ultimoTipo(chat_remite_usuario,chat_remite_institucion,chat_destino_usuario,chat_destino_institucion)as ulitmo_tipo,
                                                $baseDatosSocial.fechaUltimoMensaje(chat_remite_usuario,chat_remite_institucion,chat_destino_usuario,chat_destino_institucion)as fecha_ulitmo_msj,
                                                $baseDatosSocial.ultimoMensaje(chat_remite_usuario,chat_remite_institucion,chat_destino_usuario,chat_destino_institucion)as ulitmo_msj,
                                                $baseDatosSocial.cantNoLeidos(chat_remite_usuario,chat_remite_institucion,chat_destino_usuario,chat_destino_institucion)as cantidad
                                                
                                                FROM $baseDatosSocial.chat  
                                                
                                                INNER JOIN ".BD_GENERAL.".usuarios uss 
                                                ON (uss_id=chat_destino_usuario) AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}

                                                WHERE ( (chat_remite_usuario = '" . $_SESSION['id'] . "' and chat_remite_institucion= '" . $institucion["ins_id"] . "')
                                                         OR  (chat_destino_usuario = '" . $_SESSION['id'] . "' and chat_destino_institucion= '" . $institucion["ins_id"] . "') ) 

                                                GROUP BY chat_remite_usuario,chat_destino_usuario
                                                ORDER BY fecha_ulitmo_msj DESC"
                                            );
                                            if (mysqli_num_rows($consultaUsuariosChat) > 0) {
                                                while ($datosUsurios = mysqli_fetch_array($consultaUsuariosChat, MYSQLI_BOTH)) {

                                                    $fotoPerfil = $usuariosClase->verificarFoto($datosUsurios['uss_foto']);
                                                    $isntitucion_remite = $datosUsurios['chat_remite_institucion'];                                                    
                                                    $ussId = $datosUsurios['uss_id'];
                                                    $nombre = $datosUsurios['uss_nombre'] . ' ' . $datosUsurios['uss_apellido1'];
                                                    $cantidad = $datosUsurios['cantidad'];
                                                    $estado = $datosUsurios['uss_estado'] == "1" ? "online" : "offline";
                                                    $chatTipo = $datosUsurios['ulitmo_tipo'];
                                                    if ($ussId == $_SESSION['id'] ) { //&& $institucion["ins_id"]==$isntitucion_remite <--- para validar conversaciones con otas instituciones
                                                        $miId = $_SESSION['id'];                                                       
                                                        $ussId = $datosUsurios['chat_remite_usuario'];
                                                        $isntitucion_destino = $datosUsurios['chat_destino_institucion'];
                                                        $consultaUsuario = mysqli_query($conexion, "SELECT uss_id, uss_nombre, uss_apellido1, uss_foto, uss_estado,$baseDatosSocial.cantNoLeidos($miId,$isntitucion_remite,$ussId,$isntitucion_destino)as cantidad FROM ".BD_GENERAL.".usuarios WHERE  uss_bloqueado=0 AND uss_id ='" . $ussId . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} ");
                                                        while ($datosUsuarios = mysqli_fetch_array($consultaUsuario, MYSQLI_BOTH)) {
                                                            $fotoPerfil = $usuariosClase->verificarFoto($datosUsuarios['uss_foto']);
                                                            $nombre = $datosUsuarios['uss_nombre'];
                                                            if ($datosUsuarios['uss_apellido1'] != "" || $datosUsuarios['uss_apellido1'] != NULL) {
                                                                $nombre .= " " . $datosUsuarios['uss_apellido1'];
                                                            }
                                                            $cantidad = $datosUsuarios['cantidad'];
                                                        }
                                                    }
                                                    $idChat= $ussId;//."-".$isntitucion_remite;
                                                    if (!in_array($idChat, $chats)) {
                                                        $chats[] =$idChat;
                                                        if (strlen($datosUsurios['ulitmo_msj']) > 20) {
                                                            $mensaje = substr($datosUsurios['ulitmo_msj'], 0, 20) . "...";
                                                        } else {
                                                            $mensaje = $datosUsurios['ulitmo_msj'];
                                                        }
                                                        $iconAdjunto = "";
                                                        $imagen = false;
                                                        switch ($chatTipo) {
                                                            case 1:
                                                                $iconAdjunto = "";
                                                                $imagen = false;
                                                                break;
                                                            case 2:
                                                                $chatTipo = "Foto";
                                                                $iconAdjunto = "../files/iconos/imagen16.png";
                                                                $imagen = true;
                                                                break;
                                                            case 3:
                                                                $chatTipo = "Archivo";
                                                                $iconAdjunto = "../files/iconos/file16.png";
                                                                $imagen = true;
                                                                break;
                                                            case 4:
                                                                $chatTipo = "Audio";
                                                                $iconAdjunto = "../files/iconos/audio.png";
                                                                $imagen = true;
                                                                break;
                                                        }

                                                        if ($cantidad != "0") {
                                                            $styleName = "font-weight: 700;";
                                                            $className = "badge headerBadgeColor2";
                                                        } else {
                                                            $cantidad = "";
                                                            $styleName = "";
                                                            $className = "";
                                                        }

                                            ?>

                                                        <li class="clearfix" onclick="mostrarChat(this)" id="<?= $ussId ?>">
                                                            <div class="contenedor2">
                                                                <span class="<?= $className ?> esquina-superior" id="notificacion_<?= $ussId ?>">
                                                                    <?= $cantidad ?>
                                                                </span>
                                                                <img src="<?= $fotoPerfil ?>" alt="avatar">
                                                                <i class="fa fa-circle <?= $estado ?> div-interior2"></i>
                                                            </div>

                                                            <div class="about">
                                                                <div class="name" Style="<?= $styleName ?>" id="nombre_<?= $ussId ?>"><?= $nombre ?></div>
                                                                <div class="status">
                                                                    <?php if ($imagen) { ?>
                                                                        <img src="<?= $iconAdjunto ?>" style="height: 16px;width:16px;"><?= $chatTipo ?> </img>
                                                                    <?php } ?>
                                                                    <?= $mensaje ?>
                                                                </div>
                                                            </div>
                                                        </li>
                                            <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <div id="plist" style="display: none" class="people-list scrollable-div">

                                        <ul class="list-unstyled chat-list mt-2 mb-0" id="listaUsuario">

                                            <?php
                                            $consultaUsuariosOnline = UsuariosPadre::consultaUsuariosOnline($config, $_SESSION['id']);
                                            if (mysqli_num_rows($consultaUsuariosOnline) > 0) {
                                                while ($datosUsuriosOnline = mysqli_fetch_array($consultaUsuariosOnline, MYSQLI_BOTH)) {
                                                    $fotoPerfilUsrOnline = $usuariosClase->verificarFoto($datosUsuriosOnline['uss_foto']);
                                            ?>
                                                    <li class="clearfix" onclick="mostrarChat(this)" id="<?= $datosUsuriosOnline['uss_id'] ?>_uss">
                                                        <img src="<?= $fotoPerfilUsrOnline ?>" alt="avatar">
                                                        <div class="about">
                                                            <div class="name" id="nombre_<?= $datosUsuriosOnline['uss_id'] ?>"><?= $datosUsuriosOnline['uss_nombre'] . ' ' . $datosUsuriosOnline['uss_apellido1'] ?></div>

                                                            <div class="status"> <i class="fa fa-circle online"></i> online <span id="notificacion_<?= $datosUsuriosOnline['uss_id'] ?>"> </div>
                                                        </div>
                                                    </li>
                                                <?php
                                                }
                                            }
                                            $consultaUsuariosOffline = UsuariosPadre::consultaUsuariosOffline($config, $_SESSION['id']);
                                            if (mysqli_num_rows($consultaUsuariosOffline) > 0) {
                                                while ($datosUsuriosOffline = mysqli_fetch_array($consultaUsuariosOffline, MYSQLI_BOTH)) {
                                                    $fotoPerfilUsrOffline = $usuariosClase->verificarFoto($datosUsuriosOffline['uss_foto']);
                                                ?>
                                                    <li class="clearfix" onclick="mostrarChat(this)" id="<?= $datosUsuriosOffline['uss_id'] ?>_uss">
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
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-9" style="padding-left: 0px;padding-right: 0px;">
                        <div class="contenedor" id="contenedorChat">
                            <div class="table-cell__container">
                                <span class="table-cell__content">
                                    <div class="div-center image">
                                        <img src="<?= $fotoPerfilUsr; ?>" height="200px" class="img-circle user-img-circle" alt="User Image">

                                        <h1>
                                            <span style='font-family:Arial; font-weight:bold;'>Te damos la bienvenida, <?= $datosUsuarioActual['uss_nombre'] ?></samp>
                                        </h1>
                                        <p>¡Todo listo para chatear! Comencemos.</p>
                                    </div>

                                </span>
                            </div>
                        </div>
                    </div>
                    <div id="modalImagen" class="modal">
                        <span class="close"></span>
                        <img class="modal-content" id="imagenModal">
                        <div id="caption"></div>
                    </div>



                </div>
            </div>
        </div>
        <script>
            // Datos del usuario  remitente
            var chat_remite_usuario = '<?= $idSession ?>';
            var remite_foto_url_uss = "<?php echo $fotoPerfilUsr ?>";
            var remite_nombre_uss = "<?php echo $datosUsuarioActual["uss_nombre"] . " " . $datosUsuarioActual["uss_apellido1"] ?>";           
            var institucion_actual = <?php echo $institucion["ins_id"] ?>;
            var chat_remite_institucion =institucion_actual;
            // Datos del usuario  destinatario
            var chat_destino_usuario = "";
            let destino_foto_url_uss = "";
            var destino_nombre_uss = "";

            let iconGrabar = document.getElementById('iconGrabar');
            let audioReprodutor = document.getElementById('audioReprodutor');
            let btnDetener = document.getElementById('btnDetener');
            let imputmensaje = document.getElementById("imputMensaje");
            const _chatTipoMensaje = "<?php echo CHAT_TIPO_MENSAJE ?>";
            const _chatTipoDocumento = "<?php echo CHAT_TIPO_DOCUMENTO ?>";
            const _chatTipoImagen = "<?php echo CHAT_TIPO_IMAGEN ?>";
            const _chatTipoAudio = "<?php echo CHAT_TIPO_AUDIO ?>";
            let tipoMensaje = _chatTipoMensaje;
            const audioElement = document.getElementById('reproductor');
            const btnChat = document.getElementById('btn-chat');
            const btnAll = document.getElementById('btn-all');
            const plist = document.getElementById('plist');
            const chatList = document.getElementById('chat-list');
            const constraints = {
                audio: true
            };
            let mediaRecorder;
            let audioChunks = [];
            let audioBlob;
            let salasCreadas = []; //llevamos un registro de las salas abiertas

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
        </script>
        <script src="../../config-general/assets/js/chat.js"></script>
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
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- end js include path -->

</body>

</html>