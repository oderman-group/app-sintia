<?php
include("../../config-general/config.php");
//include("../modelo/conexion.php");
$notificacionesConsulta = mysqli_query($conexion, "SELECT * FROM general_alertas WHERE alr_usuario='".$_POST["usuario"]."' AND alr_vista=0 ORDER BY alr_id DESC");
$notificacionesNumero = mysqli_num_rows($notificacionesConsulta);
?>

							<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-bell-o"></i>
                                <?php if($notificacionesNumero>0){?><span class="badge headerBadgeColor1"> <?=$notificacionesNumero;?> </span> <?php }?>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3><span class="bold">Notificaciones</span></h3>
                                    <span class="notification-label purple-bgcolor">Nuevas <?=$notificacionesNumero;?></span>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
                                        <?php
										$i=1;
										while($notificacionesDatos = mysqli_fetch_array($notificacionesConsulta, MYSQLI_BOTH)){
											if($i==6) break;
										?>
											<li>
												<a href="<?=$notificacionesDatos['alr_url_acceso'];?>">
													<span class="details">
													<?=$notificacionesDatos['alr_nombre'];?><br>
													<span style="font-size: 10px;"><?=$notificacionesDatos['alr_fecha_envio'];?></span>
													</span>
												</a>
											</li>
										<?php $i++;}?>
                                    </ul>
                                    <div class="dropdown-menu-footer">
                                        <a href="notificaciones-lista.php"> Ver todas </a>
                                    </div>
                                </li>
                            </ul>

							<?php if($notificacionesNumero>0){?>
							<script type="text/javascript">
								
								function avisoFive(){
								  $.toast({
										heading: 'Notificación',  
										text: 'Tienes <?=$notificacionesNumero;?> notificaciones nuevas. Revisalas en el icono de la campanita, que está en la parte superior.',
										position: 'bottom-left',
										loaderBg:'#ff6849',
										icon: 'info',
										hideAfter: 10000, 
										stack: 6
									})
									
									localStorage.setItem('notify', 1);
								}
								

								if(localStorage.getItem('notify') === null){
									setTimeout('avisoFive()',1000);
								}
								
								
								//Notificaciones de escritorio
								 if(Notification.permission !== "granted"){
									Notification.requestPermission();
								 }

								 function notificarDeskNotif(){
									if(Notification.permission !== "granted"){
										Notification.requestPermission();
									}else{
										var notificacion = new Notification("Notificación nueva",
										 {
											 icon: "https://plataformasintia.com/images/logo.png",
											 body: "Tienes <?=$notificacionesNumero;?> notificaciones nuevas. Ingresa a la plataforma SINTIA para revisarlas."
										 }
										);

										 notificacion.onclick = function(){
											window.open("<?=$datosUnicosInstitucion["ins_url_acceso"];?>?urlDefault=notificaciones-lista.php");
										 }
									}
								 }
								
								setTimeout('notificarDeskNotif()',1000);
								
							</script>
							<?php }?>