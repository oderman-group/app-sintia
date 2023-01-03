<?php
include("../modelo/conexion.php");

include("../compartido/sintia-funciones.php");
//Instancia de Clases generales
$usuariosClase = new Usuarios();

										$datosConsultaChat = mysqli_query($conexion, "SELECT * FROM usuarios 
										INNER JOIN perfiles ON pes_id=uss_tipo
										WHERE uss_estado=1 AND uss_bloqueado=0 AND uss_id!='".$_POST["usuario"]."' 
										AND YEAR(uss_ultimo_ingreso)='".date("Y")."' AND MONTH(uss_ultimo_ingreso)='".date("m")."' AND DAY(uss_ultimo_ingreso)='".date("d")."'
										ORDER BY uss_nombre
										LIMIT 0, 200");
										while($datosChat = mysqli_fetch_array($datosConsultaChat, MYSQLI_BOTH)){
											
											$consultaSinLeer=mysqli_query($conexion, "SELECT * FROM mobiliar_sintia_social.chat 
											WHERE chat_destino_usuario='".$_POST["usuario"]."' AND chat_remite_usuario='".$datosChat["uss_id"]."'
											AND chat_remite_institucion='".$_POST["institucion"]."' AND chat_destino_institucion='".$_POST["institucion"]."' AND chat_visto='0'");
											$sinLeer = mysqli_num_rows($consultaSinLeer);
											
											$nombreSeparado = explode(" ", $datosChat["uss_nombre"]);
											
											$fotoChatUsr = $usuariosClase->verificarFoto($datosChat["uss_foto"]);
							
											
											$arrayEnviar = array("idUsuario"=>$datosChat["uss_id"], "nombreUsuario"=>$datosChat["uss_nombre"], "fotoUsuario"=>$datosChat["uss_foto"]);
											$arrayDatos = json_encode($arrayEnviar);
											$objetoEnviar = htmlentities($arrayDatos);
																
										?>
											<li class="media" id="<?=$objetoEnviar;?>" onclick="conectarme(this)">
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













