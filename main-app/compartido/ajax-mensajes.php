<?php
/**
 * AJAX para verificar mensajes nuevos
 * Este archivo se ejecuta en CADA carga de página (window.onload)
 * 
 * ESTRATEGIA: Iniciar sesión normal pero cerrar INMEDIATAMENTE después de leer
 * para no bloquear otros requests
 */

// Log de inicio
error_log("🔵 AJAX-MENSAJES INICIO - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN') . " - Time: " . microtime(true));

// Verificar si la sesión ya está activa
if (session_status() === PHP_SESSION_NONE) {
    // Iniciar sesión normalmente (SIN read_and_close que causa problemas)
    session_start();
    error_log("✅ AJAX-MENSAJES: Sesión iniciada normalmente");
} else {
    error_log("⚠️ AJAX-MENSAJES: Sesión ya estaba activa (status: " . session_status() . ")");
}

include("../modelo/conexion.php");

// VALIDACIÓN CRÍTICA: Verificar que las variables de sesión existan
// En condiciones de alta carga, la sesión puede estar vacía o corrupta
if (empty($_SESSION["id"]) || empty($_SESSION["datosUnicosInstitucion"])) {
    error_log("🔴 AJAX-MENSAJES: Sesión VACÍA detectada - ID: " . ($_SESSION["id"] ?? 'NULL') . " - datosUnicosInstitucion: " . (isset($_SESSION["datosUnicosInstitucion"]) ? 'EXISTE pero vacío' : 'NO EXISTE'));
    error_log("   └─ Session ID: " . session_id());
    error_log("   └─ Todas las keys en SESSION: " . implode(', ', array_keys($_SESSION)));
    error_log("   └─ Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'NONE'));
    
    // Salir silenciosamente con valores vacíos (no romper el HTML)
    $mensajesNumero = 0;
    $mensajesConsulta = null;
    $datosUnicosInstitucion = ['ins_url_acceso' => '#'];
} else {
    // Sesión válida - proceder normalmente
    $datosUnicosInstitucion = $_SESSION["datosUnicosInstitucion"];
    
    error_log("✅ AJAX-MENSAJES: Sesión válida - Usuario: " . $_SESSION["id"] . " - Ejecutando query mensajes");
    
    $mensajesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".social_emails 
    INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=ema_de AND uss.institucion={$_SESSION["idInstitucion"]} AND uss.year={$_SESSION["bd"]}
    WHERE ema_para='".$_SESSION["id"]."' AND ema_visto=0 AND ema_institucion={$_SESSION["idInstitucion"]} AND ema_year={$_SESSION["bd"]} ORDER BY ema_id DESC");
    
    $mensajesNumero = mysqli_num_rows($mensajesConsulta);
    
    error_log("✅ AJAX-MENSAJES: Query exitoso - Mensajes encontrados: " . $mensajesNumero);
}

// CRÍTICO: Cerrar la sesión MANUALMENTE después de leer para liberar el bloqueo
// Esto permite que otros requests puedan acceder a la sesión sin esperar
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
    error_log("✅ AJAX-MENSAJES: Sesión cerrada manualmente para liberar bloqueo");
}

error_log("🔵 AJAX-MENSAJES FIN - Time: " . microtime(true));
?>

                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3><span class="bold">Mensajes</span></h3>
                                    <span class="notification-label cyan-bgcolor">Nuevos <?=$mensajesNumero;?></span>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
										
										<?php
										// Solo iterar si la consulta es válida (no null por sesión vacía)
										if ($mensajesConsulta !== null) {
											while($mensajesDatos = mysqli_fetch_array($mensajesConsulta, MYSQLI_BOTH)){
										?>
											<li>
												<a href="mensajes-ver.php?idR=<?=base64_encode($mensajesDatos['ema_id']);?>">
													<span class="from"> <?=$mensajesDatos['uss_nombre'];?></span><br>
													<span class="message"> <?=$mensajesDatos['ema_asunto'];?> </span><br>
													<span class="time"><?=$mensajesDatos['ema_fecha'];?> </span>
												</a>
											</li>
										<?php 
											}
										}
										?>
                                       
                                    </ul>
                                    <div class="dropdown-menu-footer">
                                        <a href="mensajes.php"> Ver todos </a>
                                    </div>
                                </li>
                            </ul>

							<?php if($mensajesNumero>0){?>
							<script type="text/javascript">
								var numero=<?=$mensajesNumero;?>;
								$('#mensajes_numero').empty().hide().html('<span class="badge headerBadgeColor2">'+<?=$mensajesNumero;?>+'</span>').show(1);

								function avisoMsjs(){									
								  $.toast({
										heading: 'Notificación',  
										text: 'Tienes <?=$mensajesNumero;?> mensajes nuevos. Revisalos en el icono del sobre, que está en la parte superior.',
										position: 'bottom-right',
                						showHideTransition: 'slide',
										loaderBg:'#ff6849',
										icon: 'info',
										hideAfter: 10000, 
										stack: 6
									})
									
									localStorage.setItem('msjs', 1);
								}
								
								if(localStorage.getItem('msjs') === null){
									setTimeout('avisoMsjs()',1000);
								}			
								
								
								//Notificaciones de escritorio
								 if(Notification.permission !== "granted"){
									Notification.requestPermission();
								 }

								 function notificarDeskMsj(){
									if(Notification.permission !== "granted"){
										Notification.requestPermission();
									}else{
										var notificacion = new Notification("Mensaje nuevo",
										 {
											 icon: "https://plataformasintia.com/images/logo.png",
											 body: "Tienes <?=$mensajesNumero;?> mensajes nuevos. Ingresa a la plataforma SINTIA para revisarlos."
										 }
										);

										 notificacion.onclick = function(){
											window.open("<?=$datosUnicosInstitucion["ins_url_acceso"];?>?urlDefault=mensajes.php");
										 }
									}
								 }
								
								setTimeout('notificarDeskMsj()',1000);
								
							</script>
							<?php }?>



