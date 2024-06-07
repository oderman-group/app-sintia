<link href="../compartido/comentarios.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/css/comentarios-reacciones.css" rel="stylesheet" type="text/css" />
<div class="row">

    <div class="col-md-12">
        <?php include("../compartido/barra-superior-noticias.php");
        include("../class/SocialComentarios.php");
        include("../class/SocialReacciones.php"); ?>
        <div class="row">

            <div class="col-md-4 col-lg-3">

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
                    <div class="panel animate__animated animate__heartBeat animate__delay-1s animate__repeat-2">
                        <header class="panel-heading panel-heading-red">Pagos</header>
                        <div class="panel-body">
                            <p style="text-align: justify;">
                                Estimado <b><?= UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual) ?></b>, le recordamos que,
                                su Institución, <b><?= strtoupper($datosUnicosInstitucion['ins_nombre']) ?></b>, tiene un
                                saldo pendiente con nuestra compañía.<br>
                                A continuación los detalles y las opciónes de pago:<br>
                                <b>Saldo pendiente:</b> <?php if (is_numeric($monto) && $monto > 0) echo "$" . number_format($monto, 0, ".", "."); ?>.<br>
                                <b>Descripción:</b> <?= $datosUnicosInstitucion['ins_concepto_deuda']; ?>.<br>
                                <hr>
                                Puede hacer una transferencia bancaria a la cuenta siguiente cuenta:<br>
                                <a href="javascript:void(0);" onclick="verCuentaBancaria()" id="cuentaBancaria" style="text-decoration: underline;">Ver número de cuenta</a>,<br> o tambien puede hacer el pago en linea, de forma segura, en el siguiente botón.
                            </p>
                            <div class="col-sm-4">
                                <form action="../pagos-online/index.php" method="post" target="_target">
                                    <input type="hidden" class="form-control" name="idUsuario" value="<?= $datosUsuarioActual['uss_id']; ?>">
                                    <input type="hidden" class="form-control" name="emailUsuario" value="<?= $datosUsuarioActual['uss_email']; ?>">
                                    <input type="hidden" class="form-control" name="documentoUsuario" value="<?= $datosUsuarioActual['uss_documento']; ?>">
                                    <input type="hidden" class="form-control" name="nombreUsuario" value="<?= UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual); ?>">
                                    <input type="hidden" class="form-control" name="celularUsuario" value="<?= $datosUsuarioActual['uss_celular']; ?>">
                                    <input type="hidden" class="form-control" name="idInstitucion" value="<?= $config['conf_id_institucion']; ?>">
                                    <input type="hidden" class="form-control" name="monto" value="<?= $monto; ?>">
                                    <input type="hidden" class="form-control" name="nombre" value="<?= $descripcion; ?>">

                                    <button type="submit" class="btn btn-success"><i class="fa fa-credit-card" aria-hidden="true"></i>PAGA EN LINEA AQUÍ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php include("../compartido/modulo-frases-lateral.php"); ?>

                <?php include("../compartido/publicidad-lateral.php"); ?>

            </div>


            <div class="col-md-4 col-lg-6" id="contendedor-publicaciones">
                <?php $page = 0; ?>
                <div class="card card-box">
                    <div class="card-head">
                        <header><?= $frases[168][$datosUsuarioActual['uss_idioma']]; ?></header>
                    </div>
                    <?php
                    $fotoUsrActual = $usuariosClase->verificarFoto($datosUsuarioActual['uss_foto']);
                    ?>
                    <div class="card-body " id="bar-parent1">
                        <form class="form-horizontal" action="../compartido/noticia-rapida-guardar.php" method="post">
                            <input type="hidden" id="infoGeneral" value="<?= base64_encode($datosUsuarioActual['uss_id']); ?>|<?= $fotoUsrActual; ?>|<?= $datosUsuarioActual['uss_nombre']; ?>">
                            <div class="form-group row">
                                <div class="col-sm-12" data-hint="Realiza una publicación rápida, con solo texto.">
                                    <textarea id="contenido" name="contenido" class="form-control" rows="3" placeholder="<?= $frases[169][$datosUsuarioActual['uss_idioma']]; ?>" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="offset-md-5 col-md-7">
                                    <button type="button" class="btn deepPink-bgcolor" onClick="crearNoticia()">
                                        <?= $frases[170][$datosUsuarioActual['uss_idioma']]; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php include("../compartido/encuestas.php"); ?>

                <div id="nuevaPublicacion"></div>

                <?php include("../compartido/publicaciones-lista.php"); ?>
                <div id="gifCarga" class="gif-carga" style="position: fixed !important;">
                    <img alt="Cargando...">
                </div>
                <input type="hidden" id="page" class="form-control" name="nombre" value="<?= $page ?>">
                <input type="hidden" id="paginar" class="form-control" name="paginar" value="true">

                <!--<div class="col-md-4 col-lg-3">

                <div class="panel" data-hint="Se muestran las personas que están de cumpleaños en este día."
                    id="../compartido/cumplimentados.php" title="cumplimentados" onClick="axiosAjax(this)">
                    <header class="panel-heading panel-heading-red">
                        <?php echo $frases[215][$datosUsuarioActual['uss_idioma']]; ?></header>

                    <div id="RESP_cumplimentados" class="panel-body"></div>
                </div>


            </div>-->

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


                    var limite = 5;

                    window.addEventListener('scroll', function() {
                        var paginar = document.getElementById("paginar").value;
                        let paginado = (paginar === "true");

                        console.log(paginado + '  1:' + window.innerHeight + ' 2:' + window.scrollY + ' 3:' + (window.innerHeight + window.scrollY) + ' 4:' + document.body.offsetHeight);
                        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                            if (paginado) {
                                listarPublicaciones();
                            }

                        }
                    });

                    function listarPublicaciones() {
                        console.log('Has llegado al final de la página.');
                        var cont = parseInt(document.getElementById("page").value);
                        document.getElementById("paginar").value = 'false';
                        var data = {
                            "pagina": (cont + limite),
                        };
                        var url = "../compartido/publicaciones-lista.php";
                        document.getElementById("gifCarga").style.display = "block";
                        metodoFetch(url, data, 'html', false, 'pintarPublicacionDiv');

                    }

                    function pintarPublicacionDiv(response, data) {
                        console.log(data["pagina"]);
                        var cont = parseInt(document.getElementById("page").value);
                        var miDiv = document.getElementById("contendedor-publicaciones");
                        var div = document.createElement('div');
                        console.log(response);
                        div.innerHTML = response;
                        console.log(div.childNodes.length);
                        if (div.childNodes.length > 0) {
                            document.getElementById("page").value = (cont + limite);
                            document.getElementById("paginar").value = 'true';
                            miDiv.appendChild(div);
                        } else {
                            document.getElementById("paginar").value = 'false';
                        }
                        document.getElementById("gifCarga").style.display = "none";
                    }
                </script>
            </div>
        </div>
    </div>