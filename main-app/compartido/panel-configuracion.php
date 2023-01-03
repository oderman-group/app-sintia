
<!-- start chat sidebar -->
            <div class="chat-sidebar-container" data-close-on-body-click="false">
                <div class="chat-sidebar">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#quick_sidebar_tab_1" class="nav-link active tab-icon"  data-toggle="tab"> <i class="material-icons">chat</i>Chat</a>
                        </li>
						
						<!--
                        <li class="nav-item">
                            <a href="#quick_sidebar_tab_3" class="nav-link tab-icon"  data-toggle="tab"> <i class="material-icons">settings</i> Configuración
                            </a>
                        </li>-->
                    </ul>
                    <div class="tab-content">
                        <!-- Start Doctor Chat --> 
 						<div class="tab-pane active chat-sidebar-chat in active show" role="tabpanel" id="quick_sidebar_tab_1">
                        	<div class="chat-sidebar-list">
	                            <div class="chat-sidebar-chat-users slimscroll-style" data-rail-color="#ddd" data-wrapper-class="chat-sidebar-list">
	                                <div class="chat-header"><h5 class="list-heading">En linea</h5></div>
	                                <ul class="media-list list-items">
	                                    <!--
										<li class="media"><img class="media-object" src="../../config-general/assets/img/prof/prof3.jpg" width="35" height="35" alt="...">
	                                        <i class="online dot"></i>
	                                        <div class="media-body">
	                                            <h5 class="media-heading">John Deo</h5>
	                                            <div class="media-heading-sub">Spine Surgeon</div>
	                                        </div>
	                                    </li>
										-->
										<?php
										$datosConsultaChat = mysqli_query($conexion, "SELECT * FROM usuarios 
										INNER JOIN perfiles ON pes_id=uss_tipo
										WHERE uss_estado=1 AND uss_bloqueado=0
										AND YEAR(uss_ultimo_ingreso)='".date("Y")."' AND MONTH(uss_ultimo_ingreso)='".date("m")."'
										ORDER BY uss_nombre
										LIMIT 0, 800");
										while($datosChat = mysqli_fetch_array($datosConsultaChat, MYSQLI_BOTH)){
											
											$consultaSinLeer=mysqli_query($conexion, "SELECT * FROM mobiliar_sintia_social.chat 
											WHERE chat_destino_usuario='".$_SESSION["id"]."' AND chat_remite_usuario='".$datosChat["uss_id"]."'
											AND chat_remite_institucion='".$config['conf_id_institucion']."' AND chat_destino_institucion='".$config['conf_id_institucion']."' AND chat_visto='0'");
											$sinLeer = mysqli_num_rows($consultaSinLeer);
											
											$nombreSeparado = explode(" ", $datosChat["uss_nombre"]);
											
											$fotoChatUsr = $usuariosClase->verificarFoto($datosChat["uss_foto"]);
							
											
											$arrayEnviar = array("idUsuario"=>$datosChat["uss_id"], "nombreUsuario"=>$datosChat["uss_nombre"], "fotoUsuario"=>$datosChat["uss_foto"]);
											$arrayDatos = json_encode($arrayEnviar);
											$objetoEnviar = htmlentities($arrayDatos);
											
											$styleDisplayUser = 'block';
											if($datosChat["uss_id"] == $_SESSION["id"]){$styleDisplayUser = 'none';}
																
										?>
											<li class="media" id="<?=$objetoEnviar;?>" onclick="conectarme(this)" style="display: <?=$styleDisplayUser;?>">
												<?php
												$display = 'none';
												if($sinLeer>0){
													$display = 'block';	
												}
												?>
												<div id="display<?=$datosChat["uss_id"];?>" class="media-status" style="display: <?=$display;?>">
													<span id="contaMsjs<?=$datosChat["uss_id"];?>" class="badge badge-success"><?=$sinLeer;?></span>
												</div> 
												
												<img class="media-object" src="<?=$fotoChatUsr;?>" width="35" height="35">
												<i class="online dot"></i>
												<div class="media-body">
													<h6 class="media-heading"><?=$nombreSeparado[0]." ".$nombreSeparado[1];?></h6>
													<div class="media-heading-sub"><?=$datosChat["pes_nombre"];?></div>
												</div>
											</li>
										<?php }?>
	                                    
	                                </ul>
									
	                            </div>
                            </div>
                            <div class="chat-sidebar-item">
                                <div class="chat-sidebar-chat-user">
                                    <div class="page-quick-sidemenu">
                                        <a href="javascript:;" class="chat-sidebar-back-to-list">
                                            <i class="fa fa-angle-double-left"></i>Atrás
                                        </a>
                                    </div>
									
                                    <div class="chat-sidebar-chat-user-messages" id="mensajesChat"></div>
									
                                    <div class="chat-sidebar-chat-user-form">
                                        <div class="input-group">
                                            <input id="msjChat" type="text" class="form-control" placeholder="Escribe aquí..." onChange="enviarMensaje()">
                                            <div class="input-group-btn">
                                                <button id="btnEnviarChat" type="button" class="btn deepPink-bgcolor" onclick="enviarMensaje()">
                                                    <i class="fa fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
									
									
									<input type="hidden" id="IDInstitucionChat" value="<?=$config['conf_id_institucion'];?>">
									<input type="hidden" id="IDUsuarioChat" value="<?=$_SESSION["id"];?>">
									<input type="hidden" id="nombreUsuarioChat" value="<?=$datosUsuarioActual['uss_nombre'];?>">
									<input type="hidden" id="fotoUsuarioChat" value="<?=$datosUsuarioActual['uss_foto'];?>">
									
									
									<audio id="audioChat" controls style="display: none;">
										<source type="audio/mp3" src="../../files-general/main-app/sonidos/nuevoMensaje.mp3">
									</audio>
									
									<audio id="audioInbox" controls style="display: none;">
										<source type="audio/mp3" src="../../files-general/main-app/sonidos/inbox.mp3">
									</audio>
									
									<script type="application/javascript">
										let ws = null;
										
										var audioChat = document.getElementById("audioChat");
										var audioInbox = document.getElementById("audioInbox");
										
										var elmntDivChat = document.getElementById("mensajesChat");

										var divMensajes = document.getElementById("mensajesChat");
										var btnEnviarChat = document.getElementById("btnEnviarChat");
										
										var BDInstitucionChat = document.getElementById("BDInstitucionChat");
										var agnoChat = document.getElementById("agnoChat");
										var IDInstitucionChat = document.getElementById("IDInstitucionChat");
										var IDUsuarioChat = document.getElementById("IDUsuarioChat");
										var nombreUsuarioChat = document.getElementById("nombreUsuarioChat");
										var fotoUsuarioChat = document.getElementById("fotoUsuarioChat");

										function conectarme(datosChat) {
											
											msjChat.disabled = "disabled";
											
											let variableChat = (datosChat.id);
											var varObjetChat = JSON.parse(variableChat);

											ws = new WebSocket('wss://mobiliariun.com/servidor-node-ws-mysql');
											
											divMensajes.innerHTML = "";
											
											document.getElementById("display"+varObjetChat.idUsuario).style.display="none";

											ws.onopen = (e) => {
												
												
												IDinstitucion = IDInstitucionChat.value;
												usuario = IDUsuarioChat.value;
												usuarioNombre = nombreUsuarioChat.value;
												usuarioFoto = fotoUsuarioChat.value;
												
												destinatario = varObjetChat.idUsuario;
												destinatarioNombre = varObjetChat.nombreUsuario;
												destinatarioFoto = varObjetChat.fotoUsuario;

												ws.send(JSON.stringify({
													tipo: 1,
													data: usuario,
													nombre: usuarioNombre,
													foto: usuarioFoto,
													institucion: IDinstitucion,
													BDInstitucionChat: BDInstitucionChat,
													agnoChat: agnoChat,
													destinatario: destinatario
												}));
												
												//Hacemos ping al servidor cada x segundos
												setInterval(() => {
													ws.send(JSON.stringify({ tipo: 4, data: "Ping"}));
												}, 20000);

												setInterval(() => {
													console.log("Estado actual: " + ws.readyState);
													if(ws.readyState == 1){
														msjChat.disabled = "";
													}else{
														msjChat.disabled = "disabled";
													}
													
												}, 5000);
												
												//Se habilita para escribir mensajes.
												msjChat.disabled = "";
											}
											
											//Cuando recibo mensajes
											ws.onmessage = (e) => {
												mensaje = JSON.parse(e.data)

												console.log(mensaje);


												if (mensaje.tipo == 2) {

													horaRecibo = new Date().getHours();
													minutoRecibo = new Date().getMinutes();
													segundoRecibo = new Date().getSeconds();
													
													if(mensaje.destinatario == IDUsuarioChat.value && destinatario == mensaje.usuario){
														
														audioInbox.play();
														
														divMensajes.innerHTML += `
																<div class="post in">
																	<img class="avatar" alt="" src="../files/fotos/${mensaje.foto}" />
																	<div class="message">
																		<span class="arrow"></span> <a href="javascript:;" class="name">${mensaje.nombre}</a> <span class="datetime">${horaRecibo}:${minutoRecibo}:${segundoRecibo}</span>
																		<span class="body"> ${mensaje.data}</span>
																	</div>
																</div>
															`;

														
													}else if(mensaje.destinatario == IDUsuarioChat.value && destinatario != mensaje.usuario){
														
														audioChat.play();
														
														document.getElementById("display"+mensaje.usuario).style.display="block";

														document.getElementById("contaMsjs"+mensaje.usuario).innerHTML=1;
														
														$.toast({
															heading: mensaje.nombre,  
															text: mensaje.data,
															position: 'left-bottom',
															loaderBg:'#ff6849',
															icon: 'info',
															hideAfter: 5000, 
															stack: 1,
															showHideTransition: 'slide',
															bgColor: '#007bff',
    														textColor: 'white'
														});
														
													}
													
												}
												
												
												if (mensaje.tipo == 5) {
													divMensajes.innerHTML = "";
													mensaje.array.forEach(chatHistorial =>{
														
														hora = new Date(chatHistorial.chat_fecha_registro).getHours();
														minuto = new Date(chatHistorial.chat_fecha_registro).getMinutes();
														segundo = new Date(chatHistorial.chat_fecha_registro).getSeconds();
														
														if(chatHistorial.chat_remite_usuario == IDUsuarioChat.value){
															
															if(chatHistorial.chat_visto == 1){
															  divMensajes.innerHTML += `
																<div class="post out">
																	<img class="avatar" alt="" src="../files/fotos/${fotoUsuarioChat.value}" />
																	<div class="message" style="background-color:#673ab7;">
																		<span class="arrow"></span> <a href="javascript:;" class="name">Yo</a> 
																		<span class="datetime">${hora}:${minuto}:${segundo}</span>
																		<span class="body-out"> ${chatHistorial.chat_mensaje}</span>
																	</div>
																</div>
															`; 
															}else{
															  divMensajes.innerHTML += `
																<div class="post out">
																	<img class="avatar" alt="" src="../files/fotos/${fotoUsuarioChat.value}" />
																	<div class="message">
																		<span class="arrow"></span> <a href="javascript:;" class="name">Yo</a> 
																		<span class="datetime">${hora}:${minuto}:${segundo}</span>
																		<span class="body-out"> ${chatHistorial.chat_mensaje}</span>
																	</div>
																</div>
															`; 
															}
															   
															
														   
														}else{
															
															divMensajes.innerHTML += `
																<div class="post in">
																	<img class="avatar" alt="" src="../files/fotos/${destinatarioFoto}" />
																	<div class="message">
																		<span class="arrow"></span> <a href="javascript:;" class="name">${destinatarioNombre}</a> <span class="datetime">${hora}:${minuto}:${segundo}</span>
																		<span class="body"> ${chatHistorial.chat_mensaje}</span>
																	</div>
																</div>
															`;
														   
														}
													});
													
														elmntDivChat.scrollTop = elmntDivChat.scrollHeight;

												}
											}

											//Cuando hay un error
											ws.onerror = (e) => {
												msjChat.disabled = "disabled";
												alert("Hubo un error en el chat, recargue la página."); 
												console.log("Error en el chat ", e);
											}

											//Cuando se cierra la conexion
											ws.onclose = function (e) {
												//alert("WebSocket se cerró en el cliente:", e);
												msjChat.disabled = "disabled";
											};


										}


										function enviarMensaje() {

											var msjChat = document.getElementById("msjChat");
											
											if(ws.readyState!=1){
												alert("No es posible enviar mensaje en este momento.");
												msjChat.disabled = "disabled";
												return false;
											}
											
											if(msjChat.value == ""){
												return false;
											}

											ws.send(JSON.stringify({
												tipo: 2,
												data: msjChat.value
											}));
											
											horaEnvio = new Date().getHours();
											minutoEnvio = new Date().getMinutes();
											segundoEnvio = new Date().getSeconds();
											
											divMensajes.innerHTML += `
														<div class="post out">
															<img class="avatar" alt="" src="../files/fotos/${fotoUsuarioChat.value}" />
															<div class="message">
																<span class="arrow"></span> <a href="javascript:;" class="name">Yo</a> <span class="datetime">${horaEnvio}:${minutoEnvio}:${segundoEnvio}</span>
																<span class="body-out"> ${msjChat.value}</span>
															</div>
														</div>
													`;
											
											msjChat.value = "";
											
											elmntDivChat.scrollTop = elmntDivChat.scrollHeight;


										}
									</script>
									
									
                                </div>
                            </div>
                        </div>
                        <!-- End Doctor Chat --> 
 						<!-- Start Setting Panel --> 
 						<div class="tab-pane chat-sidebar-settings" role="tabpanel" id="quick_sidebar_tab_3">
                            <div class="chat-sidebar-settings-list slimscroll-style">
                                <div class="chat-header"><h5 class="list-heading">Configuración de diseño</h5></div>
	                            <div class="chatpane inner-content ">
									<div class="settings-list">
										
					                    <div class="setting-item">
					                        <div class="setting-text">Posición del menú</div>
					                        <div class="setting-set">
					                           <select class="sidebar-pos-option form-control input-inline input-sm input-small ">
	                                                <option value="left" selected="selected">Izquierda</option>
	                                                <option value="right">Derecha</option>
                                            	</select>
					                        </div>
					                    </div>
					                    <div class="setting-item">
					                        <div class="setting-text">Encabezado</div>
					                        <div class="setting-set">
					                           <select class="page-header-option form-control input-inline input-sm input-small ">
	                                                <option value="fixed" selected="selected">Fijo</option>
	                                                <option value="default">Móvil</option>
                                            	</select>
					                        </div>
					                    </div>
										
					                    <div class="setting-item">
					                        <div class="setting-text">Estilo Menú </div>
					                        <div class="setting-set">
					                           <select class="sidebar-menu-option form-control input-inline input-sm input-small ">
	                                                <option value="accordion">Acordeón</option>
	                                                <option value="hover">Hover</option>
                                            	</select>
					                        </div>
					                    </div>
										
					                    <div class="setting-item">
					                        <div class="setting-text">Pie de página</div>
					                        <div class="setting-set">
					                           <select class="page-footer-option form-control input-inline input-sm input-small ">
	                                                <option value="fixed">Fijo</option>
	                                                <option value="default" selected="selected">Móvil</option>
                                            	</select>
					                        </div>
					                    </div>
										
					                </div>
									<div class="chat-header"><h5 class="list-heading">Configuración de cuenta</h5></div>
									<div class="settings-list">
					                    <div class="setting-item">
					                        <div class="setting-text">Notificaciones</div>
					                        <div class="setting-set">
					                            <div class="switch">
					                                <label class = "mdl-switch mdl-js-switch mdl-js-ripple-effect" 
									                  for = "switch-1">
									                  <input type = "checkbox" id = "switch-1" 
									                     class = "mdl-switch__input" checked>
									               	</label>
					                            </div>
					                        </div>
					                    </div>
										<!--
					                    <div class="setting-item">
					                        <div class="setting-text">Show Online</div>
					                        <div class="setting-set">
					                            <div class="switch">
					                                <label class = "mdl-switch mdl-js-switch mdl-js-ripple-effect" 
									                  for = "switch-7">
									                  <input type = "checkbox" id = "switch-7" 
									                     class = "mdl-switch__input" checked>
									               	</label>
					                            </div>
					                        </div>
					                    </div>
					                    <div class="setting-item">
					                        <div class="setting-text">Status</div>
					                        <div class="setting-set">
					                            <div class="switch">
					                                <label class = "mdl-switch mdl-js-switch mdl-js-ripple-effect" 
									                  for = "switch-2">
									                  <input type = "checkbox" id = "switch-2" 
									                     class = "mdl-switch__input" checked>
									               	</label>
					                            </div>
					                        </div>
					                    </div>
					                    <div class="setting-item">
					                        <div class="setting-text">2 Steps Verification</div>
					                        <div class="setting-set">
					                            <div class="switch">
					                                <label class = "mdl-switch mdl-js-switch mdl-js-ripple-effect" 
									                  for = "switch-3">
									                  <input type = "checkbox" id = "switch-3" 
									                     class = "mdl-switch__input" checked>
									               	</label>
					                            </div>
					                        </div>
					                    </div>
										-->
					                </div>
									
									<!--
                                    <div class="chat-header"><h5 class="list-heading">General Settings</h5></div>
                                    <div class="settings-list">
					                    <div class="setting-item">
					                        <div class="setting-text">Location</div>
					                        <div class="setting-set">
					                            <div class="switch">
					                                <label class = "mdl-switch mdl-js-switch mdl-js-ripple-effect" 
									                  for = "switch-4">
									                  <input type = "checkbox" id = "switch-4" 
									                     class = "mdl-switch__input" checked>
									               	</label>
					                            </div>
					                        </div>
					                    </div>
					                    <div class="setting-item">
					                        <div class="setting-text">Save Histry</div>
					                        <div class="setting-set">
					                            <div class="switch">
					                                <label class = "mdl-switch mdl-js-switch mdl-js-ripple-effect" 
									                  for = "switch-5">
									                  <input type = "checkbox" id = "switch-5" 
									                     class = "mdl-switch__input" checked>
									               	</label>
					                            </div>
					                        </div>
					                    </div>
					                    <div class="setting-item">
					                        <div class="setting-text">Auto Updates</div>
					                        <div class="setting-set">
					                            <div class="switch">
					                                <label class = "mdl-switch mdl-js-switch mdl-js-ripple-effect" 
									                  for = "switch-6">
									                  <input type = "checkbox" id = "switch-6" 
									                     class = "mdl-switch__input" checked>
									               	</label>
					                            </div>
					                        </div>
					                    </div>
					                </div>
									-->
	                        	</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end chat sidebar -->