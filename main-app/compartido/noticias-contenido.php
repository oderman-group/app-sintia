<!-- CSS y JS Modernos -->
<link href="../compartido/noticias-feed-modern.css" rel="stylesheet" type="text/css" />
<link href="../compartido/modales-noticias.css" rel="stylesheet" type="text/css" />
<link href="../compartido/comentarios.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/css/comentarios-reacciones.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="../compartido/modales-noticias.js"></script>

<div class="feed-container">

    <?php 
    include("../compartido/barra-superior-noticias.php");
    include("../class/SocialComentarios.php");
    include("../class/SocialReacciones.php"); 
    ?>
    
    <div class="feed-layout">
        
        <!-- SIDEBAR IZQUIERDO (FIJO) -->
        <aside class="feed-sidebar-left">

            <!-- Perfil del usuario -->
            <div class="sidebar-card">
                <div class="sidebar-profile">
                    <div class="sidebar-profile-bg"></div>
                    <?php $fotoUsrActual = $usuariosClase->verificarFoto($datosUsuarioActual['uss_foto']); ?>
                    <img src="<?= $fotoUsrActual; ?>" alt="<?= $datosUsuarioActual['uss_nombre']; ?>" class="sidebar-profile-photo">
                    <div class="sidebar-profile-name"><?= UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual); ?></div>
                    <div class="sidebar-profile-role">
                        <?php 
                        $roles = [
                            1 => 'Directivo',
                            2 => 'Docente',
                            3 => 'Acudiente',
                            4 => 'Estudiante',
                            5 => 'Administrador'
                        ];
                        echo $roles[$datosUsuarioActual['uss_tipo']] ?? 'Usuario';
                        ?>
                    </div>
                </div>
                <div class="sidebar-stats">
                    <?php
                    // Contar publicaciones del último mes (solo publicadas, no eliminadas)
                    $fechaHaceUnMes = date('Y-m-d', strtotime('-30 days'));
                    $consultaPosts = mysqli_query($conexion, "SELECT COUNT(*) as total 
                                                              FROM " . $baseDatosServicios . ".social_noticias 
                                                              WHERE not_usuario = '{$_SESSION["id"]}' 
                                                              AND not_year = '{$_SESSION["bd"]}'
                                                              AND not_fecha >= '{$fechaHaceUnMes}'
                                                              AND not_estado IN (0, 1)");
                    $dataPosts = mysqli_fetch_array($consultaPosts, MYSQLI_ASSOC);
                    $totalPosts = $dataPosts['total'] ?? 0;
                    ?>
                    <div class="sidebar-stat-item" style="cursor: pointer;" onclick="window.location.href='noticias.php?usuario=<?= base64_encode($_SESSION['id']); ?>'">
                        <span class="sidebar-stat-label">
                            <i class="fa fa-newspaper-o"></i> Publicaciones (último mes)
                        </span>
                        <span class="sidebar-stat-value"><?= $totalPosts; ?></span>
                    </div>
                </div>
            </div>

            <?php
            include("../compartido/datos-fechas.php");
            if ((($datosUsuarioActual['uss_tipo'] == TIPO_DEV) || ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO)) && ($datosUnicosInstitucion['ins_deuda'] == 1 || $dfDias <= 1)) {
                $monto = 0;
                $descripcion = 'Pago de';
                if ($datosUnicosInstitucion['ins_deuda'] == 1 && !empty($datosUnicosInstitucion['ins_valor_deuda'])) {
                    $monto += $datosUnicosInstitucion['ins_valor_deuda'];
                    $descripcion .= ' saldo pendiente';
                }
                if ($dfDias <= 1 && ($datosUnicosInstitucion['ins_deuda'] == 1 && !empty($datosUnicosInstitucion['ins_valor_deuda']))) {
                    $descripcion .= ' y';
                }
                if ($dfDias <= 1) {
                    $consultaPlan = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".planes_sintia 
                        WHERE plns_id='" . $datosUnicosInstitucion['ins_id_plan'] . "'");
                    $datosPlan = mysqli_fetch_array($consultaPlan, MYSQLI_BOTH);
                    $monto += $datosPlan['plns_valor'];
                    $descripcion .= ' renovación de la plataforma';
                }
            ?>
                <div class="sidebar-card animate__animated animate__pulse">
                    <div class="sidebar-section-title" style="color: #e74c3c;">⚠️ Pagos Pendientes</div>
                    <p style="font-size: 12px; line-height: 1.5; color: var(--text-secondary);">
                        <b>Saldo:</b> <?php if (is_numeric($monto) && $monto > 0) echo "$" . number_format($monto, 0, ".", "."); ?><br>
                        <b>Descripción:</b> <?= $datosUnicosInstitucion['ins_concepto_deuda']; ?>
                    </p>
                    <form action="../pagos-online/index.php" method="post" target="_blank">
                        <input type="hidden" name="idUsuario" value="<?= $datosUsuarioActual['uss_id']; ?>">
                        <input type="hidden" name="emailUsuario" value="<?= $datosUsuarioActual['uss_email']; ?>">
                        <input type="hidden" name="documentoUsuario" value="<?= $datosUsuarioActual['uss_documento']; ?>">
                        <input type="hidden" name="nombreUsuario" value="<?= UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual); ?>">
                        <input type="hidden" name="celularUsuario" value="<?= $datosUsuarioActual['uss_celular']; ?>">
                        <input type="hidden" name="idInstitucion" value="<?= $config['conf_id_institucion']; ?>">
                        <input type="hidden" name="monto" value="<?= $monto; ?>">
                        <input type="hidden" name="nombre" value="<?= $descripcion; ?>">
                        <button type="submit" class="btn btn-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> Pagar en línea
                        </button>
                    </form>
                </div>
            <?php } ?>

        </aside>


        <!-- CONTENIDO PRINCIPAL (FEED) -->
        <main class="feed-main">
            <?php $fotoUsrActual = $usuariosClase->verificarFoto($datosUsuarioActual['uss_foto']); ?>
            <input type="hidden" id="infoGeneral" value="<?= base64_encode($datosUsuarioActual['uss_id']); ?>|<?= $fotoUsrActual; ?>|<?= $datosUsuarioActual['uss_nombre']; ?>">
            
            <!-- Card para crear publicación -->
            <div class="post-create-card">
                <form id="quick-post-form">
                    <div class="post-create-header">
                        <img src="<?= $fotoUsrActual; ?>" alt="<?= $datosUsuarioActual['uss_nombre']; ?>" class="post-create-avatar">
                        <textarea id="contenido" 
                                  name="contenido" 
                                  class="post-create-input" 
                                  placeholder="<?= $frases[169][$datosUsuarioActual['uss_idioma']]; ?>" 
                                  rows="1"
                                  style="resize: none; overflow: hidden;"
                                  required></textarea>
                    </div>
                    <div class="post-create-actions">
                        <?php 
                        // Modal para foto (simple)
                        $modalFoto = new ComponenteModal('nuevoFoto', 'Publicar Foto', '../compartido/noticias-agregar-foto-modal.php');
                        // Modal para video (simple)
                        $modalVideo = new ComponenteModal('nuevoVideo', 'Publicar Video', '../compartido/noticias-agregar-video-modal.php');
                        // Modal para archivo (simple)
                        $modalArchivo = new ComponenteModal('nuevoArchivo', 'Publicar Archivo', '../compartido/noticias-agregar-archivo-modal.php');
                        ?>
                        <button type="button" class="post-create-action action-photo" onclick="<?=$modalFoto->getMetodoAbrirModal()?>" data-hint="Sube una foto con descripción">
                            <i class="fa fa-image"></i>
                            <span>Foto</span>
                        </button>
                        <button type="button" class="post-create-action action-video" onclick="<?=$modalVideo->getMetodoAbrirModal()?>" data-hint="Comparte un video de YouTube">
                            <i class="fa fa-video-camera"></i>
                            <span>Video</span>
                        </button>
                        <button type="button" class="post-create-action action-event" onclick="<?=$modalArchivo->getMetodoAbrirModal()?>" data-hint="Adjunta un documento">
                            <i class="fa fa-file"></i>
                            <span>Archivo</span>
                        </button>
                        <button type="button" class="btn deepPink-bgcolor" style="margin-left: auto; border-radius: 20px; padding: 8px 20px; font-weight: 600;" onClick="crearNoticia()">
                            <i class="fa fa-paper-plane"></i> <?= $frases[170][$datosUsuarioActual['uss_idioma']]; ?>
                        </button>
                    </div>
                </form>
            </div>

            <?php include("../compartido/encuestas.php"); ?>

            <!-- Contenedor de publicaciones -->
            <div id="posts-container"></div>

            <div id="nuevaPublicacion"></div>

        </main>

        <!-- SIDEBAR DERECHO -->
        <aside class="feed-sidebar-right">
            
            <?php include("../compartido/modulo-frases-lateral.php"); ?>
            
            <?php include("../compartido/publicidad-lateral.php"); ?>

            <!-- Sección de cumpleañeros (opcional) -->
            <!--
            <div class="sidebar-section">
                <div class="sidebar-section-title"><?php echo $frases[215][$datosUsuarioActual['uss_idioma']]; ?></div>
                <div id="RESP_cumplimentados"></div>
            </div>
            -->
            
        </aside>
        
    </div>

</div>

<!-- Scripts modernos -->
<script src="../compartido/noticias-feed-modern.js"></script>

<!-- Scripts antiguos (se mantendrán para compatibilidad y se migrarán gradualmente) -->
<script type="text/javascript">
                    function recargarInclude(id) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "../compartido/reacciones-lista.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                document.getElementById("reacciones-content-" + id).innerHTML = xhr.responseText;
                            }
                        };
                        var parametros = "id=" + encodeURIComponent(id);
                        xhr.send(parametros);
                    }

                    function recargarIncludeListaUsuarios(id) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "../compartido/reacciones-lista-usuarios.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                document.getElementById("dropdown-reacciones-usuarios-" + id).innerHTML = xhr.responseText;
                            }
                        };
                        var parametros = "id=" + encodeURIComponent(id);
                        xhr.send(parametros);
                    }

                    function recargarIncludeOpciones(id) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "../compartido/reacciones-lista-opciones.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                document.getElementById("opciones-" + id).innerHTML = xhr.responseText;
                            }
                        };
                        var parametros = "id=" + encodeURIComponent(id);
                        xhr.send(parametros);
                    }


                    function mostrarReaciones(dato) {
                        var id = 'dropdown-reacciones-usuarios-' + dato.name;
                        console.log(dato);
                        console.log(id);
                        document.getElementById(id).style.display = "block";
                    }

                    function reaccionar(id, reaccion, postname, usrname, postowner) {
                        var url = "../compartido/noticias-reaccionar-fetch.php";
                        var data = {
                            "id": id,
                            "reaccion": reaccion,
                            "postname": postname,
                            "usrname": usrname,
                            "postowner": postowner
                        };
                        metodoFetch(url, data, 'json', false, 'respuesta');
                    }

                    function respuesta(response) {
                        console.log(JSON.stringify(response));
                        if (response["ok"]) {
                            reaccionesNombre = ["", " Me gusta ", " Me encanta ", " Me divierte ", " Me entristece "];
                            reaccionesIconos = ["", "fa-thumbs-o-up", "fa-heart", "fa-smile-o", "fa-frown-o"];
                            reaccionesCalss = ["", "me_gusta", "me_encanta", "me_divierte", "me_entristece"];

                            index = parseInt(response["reaccion"]);

                            reacion = document.getElementById("reacciones-" + response["id"]);
                            dropdown = document.getElementById("dropdown-" + response["id"]);
                            panel = document.getElementById("panel-" + response["id"] + "-reaccion");


                            if (reacion) {
                                reacion.classList = [];
                                if (parseInt(response["cantidad"]) > 0) {
                                    reacion.innerText = response["cantidad"] + " Reacciones";
                                    reacion.classList.add('animate__animated', 'animate__fadeInDown');
                                } else {
                                    reacion.innerText = "";
                                    reacion.classList.add('animate__animated', 'animate__fadeInDown');
                                }
                            } else {
                                var divDropdownReacciones = document.createElement('div'); // se crea la etiqueta div
                                divDropdownReacciones.id = "dropdown-reacciones-usuarios-" + response["id"];
                                divDropdownReacciones.style = "width:400px ;";
                                divDropdownReacciones.classList.add('animate__animated', 'animate__fadeInUp', 'dropdown-menu');
                                divDropdownReacciones.setAttribute("aria-labelledby", "reacciones-" + response["id"]);

                                var divReaccionesContent = document.createElement('div'); // se crea la etiqueta div
                                divReaccionesContent.id = "reacciones-content-" + response["id"];
                                divReaccionesContent.classList.add('dropdown-content');

                                var reacionNueva = document.createElement('a'); // se crea la etiqueta a
                                reacionNueva.id = "reacciones-" + response["id"];
                                reacionNueva.name = response["id"];
                                reacionNueva.classList = [];
                                reacionNueva.classList.add('animate__animated', 'animate__fadeInDown', 'dropbtn');
                                reacionNueva.setAttribute("role", "button");
                                reacionNueva.setAttribute("data-toggle", "dropdown");
                                reacionNueva.setAttribute("aria-haspopup", "true");
                                reacionNueva.setAttribute("aria-expanded", "false");
                                reacionNueva.innerText = response["cantidad"] + " Reacciones";

                                dropdown.appendChild(reacionNueva);
                                dropdown.appendChild(divReaccionesContent);
                                dropdown.appendChild(divDropdownReacciones);

                               
                            }

                            panel.innerText = '';
                            if (response["accion"] == '<?php echo ACCION_ELIMINAR ?>') {
                                var icon = document.createElement('i'); // se crea la icono
                                icon.classList.add('fa', reaccionesIconos[1]);
                                panel.appendChild(icon);
                                var texto = document.createTextNode(reaccionesNombre[1]);
                                panel.appendChild(texto);
                                panel.classList = [];
                                panel.classList.add('dropdown-toggle', 'animate__animated', 'animate__fadeInDown');
                            } else {
                                var icon = document.createElement('i'); // se crea la icono
                                icon.classList.add('fa', reaccionesIconos[index]);
                                panel.appendChild(icon);
                                var texto = document.createTextNode(reaccionesNombre[index]);
                                panel.appendChild(texto);
                                panel.classList = [];
                                panel.classList.add(reaccionesCalss[index], 'dropdown-toggle', 'animate__animated', 'animate__fadeInDown');
                            }


                            recargarInclude(response["id"]);
                            recargarIncludeListaUsuarios(response["id"]);
                            recargarIncludeOpciones(response["id"]);
                            $.toast({
                                heading: 'Acción realizada',
                                text: response["msg"],
                                position: 'bottom-right',
                                showHideTransition: 'slide',
                                loaderBg: '#26c281',
                                icon: 'success',
                                hideAfter: 5000,
                                stack: 6
                            });
                        }
                    }

                    function enviarComentario(id, tipo, padre) {
                        if (tipo == "comentario") {
                            comentario = document.getElementById(tipo + "-" + id).value;
                        } else {
                            comentario = document.getElementById(tipo + "-" + id + "-" + padre).value;
                        }
                        var url = "../compartido/noticias-comentario-fetch.php";
                        var data = {
                            "id": id,
                            "comentario": comentario,
                            "padre": padre,
                            "tipo": tipo
                        };

                        metodoFetch(url, data, 'json', false, 'respuestaComentario');
                    }

                    function respuestaComentario(response) {
                        if (response["tipo"] == "comentario") {
                            var url = "../compartido/comentario-li.php";
                            metodoFetch(url, response, 'html', false, 'pintarComentarioLi');
                            document.getElementById("comentario-" + response["idNotica"]).value = "";
                        } else {
                            var url = "../compartido/respuesta-li.php";
                            metodoFetch(url, response, 'html', false, 'pintarRespuestaLi');
                            document.getElementById("respuesta-" + response["idNotica"] + "-" + response["padre"]).value = "";
                        }
                    }

                    function pintarComentarioLi(response, data) {
                        var lista = document.getElementById("comments-list-" + data["idNotica"]);
                        var i = document.createElement('li');
                        i.innerHTML = response;
                        lista.insertBefore(i, lista.firstChild);
                        // cambiar el valor de los comentarios
                        comentarios = document.getElementById("comentarios-" + data["idNotica"]);
                        comentarios.classList = [];
                        comentarios.innerText = data["cantidad"] + " Comentarios ";
                        var icon = document.createElement('i');
                        icon.classList.add('fa', 'fa-comments-o');
                        comentarios.appendChild(icon);
                        comentarios.classList.add('pull-right', 'animate__animated', 'animate__fadeInDown');
                        //notificacion de registro exitoso
                        $.toast({
                            heading: 'Acción realizada',
                            text: data["msg"],
                            position: 'bottom-right',
                            showHideTransition: 'slide',
                            loaderBg: '#26c281',
                            icon: 'success',
                            hideAfter: 5000,
                            stack: 6
                        });
                    }

                    function pintarRespuestaLi(response, data) {
                        console.log(response);
                        console.log(data);
                        var lista = document.getElementById("lista-respuesta-" + data["padre"]);
                        var miDiv = document.getElementById("div-respuesta-" + data["padre"]);
                        miDiv.classList.remove('show');
                        lista.classList.add('show');
                        var i = document.createElement('li');
                        i.innerHTML = response;
                        lista.insertBefore(i, lista.firstChild);
                        // cambiar el valor de los comentarios
                        respuestasCanatidad = document.getElementById("cantidad-respuestas-" + data["padre"]);
                        respuestasCanatidad.classList = [];
                        respuestasCanatidad.innerText = data["cantidad"] + " Respuestas ";
                        var icon = document.createElement('i');
                        icon.classList.add('fa', 'fa-comments-o');
                        respuestasCanatidad.appendChild(icon);
                        respuestasCanatidad.classList.add('pull-right', 'animate__animated', 'animate__fadeInDown');
                        //notificacion de registro exitoso
                        $.toast({
                            heading: 'Acción realizada',
                            text: data["msg"],
                            position: 'bottom-right',
                            showHideTransition: 'slide',
                            loaderBg: '#26c281',
                            icon: 'success',
                            hideAfter: 5000,
                            stack: 6
                        });
                    }


    // Auto-expand textarea
    const contenidoTextarea = document.getElementById('contenido');
    if (contenidoTextarea) {
        contenidoTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
</script>