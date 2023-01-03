<?php
include("../../config-general/config.php");
//include("../modelo/conexion.php");

$mensajesConsulta = mysqli_query($conexion, "SELECT * FROM social_emails 
INNER JOIN usuarios ON uss_id=ema_de
WHERE ema_para='".$_POST["usuario"]."' AND ema_visto=0 ORDER BY ema_id DESC");
$mensajesNumero = mysqli_num_rows($mensajesConsulta);
?>

							<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-envelope-o"></i>
                                <?php if($mensajesNumero>0){?><span class="badge headerBadgeColor2"> <?=$mensajesNumero;?> </span><?php }?>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3><span class="bold">Mensajes</span></h3>
                                    <span class="notification-label cyan-bgcolor">Nuevos <?=$mensajesNumero;?></span>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
										
										<?php
										while($mensajesDatos = mysql_fetch_array($mensajesConsulta)){
										?>
											<li>
												<a href="mensajes-ver.php?idR=<?=$mensajesDatos['ema_id'];?>">
													<span class="from"> <?=$mensajesDatos['uss_nombre'];?></span><br>
													<span class="message"> <?=$mensajesDatos['ema_asunto'];?> </span><br>
													<span class="time"><?=$mensajesDatos['ema_fecha'];?> </span>
												</a>
											</li>
										<?php }?>
                                       
                                    </ul>
                                    <div class="dropdown-menu-footer">
                                        <a href="mensajes.php"> Ver todos </a>
                                    </div>
                                </li>
                            </ul>

							<?php if($mensajesNumero>0){?>
							<script type="text/javascript">
								function avisoMsjs(){
								  $.toast({
										heading: 'Notificación',  
										text: 'Tienes <?=$mensajesNumero;?> mensajes nuevos. Revisalos en el icono del sobre, que está en la parte superior.',
										position: 'bottom-right',
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



