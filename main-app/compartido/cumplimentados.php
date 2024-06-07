<?php
include("../modelo/conexion.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
?>

											<?php
											$cumpleU = UsuariosPadre::consultarCumplimentados();
											while($cumple = mysqli_fetch_array($cumpleU, MYSQLI_BOTH)){
												$edad = date("Y") - $cumple['agno'];
											?>
											<div class="user-panel">
												<div class="pull-left image">
													<img src="../files/fotos/<?=$cumple['uss_foto'];?>" class="img-circle user-img-circle" alt="User Image" />
												</div>
												<div class="pull-left info">
													<p>
														<a href="#">
															<?=strtoupper($cumple['uss_nombre']);?>
															<?php if($cumple['uss_mostrar_edad']==1){echo "(".$edad.")";}?>
														</a><br>
														<a href="mensajes-redactar.php?para=<?=base64_encode($cumple['uss_id']);?>&asunto=<?=base64_encode('TE DESEO UN FELIZ CUMPLEAÑOS')?>" style="font-size: 12px; color: crimson;"><i class="fa fa-envelope-o"></i> Felicitar <?php //echo $frases[236][$datosUsuarioActual['uss_idioma']];?></a>
													</p>
												</div>
											</div>	
											<?php }?>
											<p>&nbsp;</p>
										