<!-- start sidebar menu -->
 			<div class="sidebar-container" >
 				<div class="sidemenu-container navbar-collapse collapse fixed-menu">
	                <div id="remove-scroll">
				
						<?php
						//Mostrar a los directivos si tiene deuda
						if ($config['conf_deuda'] == 1 && $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
						?>
							<div class="mt-4 p-1" style="background-color: yellow;">
								<p>
									<h4>¡Saldo pendiente!</h4>
                                    <b>NRO. FACTURA:</b> <?=$config['conf_numero_factura'];?><br />
									<b>CONCEPTO:</b> <?=$config['conf_concepto'];?><br />
                                    <b>VALOR NETO:</b> $<?=number_format($config['conf_valor'],0,",",".");?> COP.
                                </p>
						
								<p><a href="https://plataformasintia.com/files-general/qr_sintia_abonos.pdf" class="btn btn-danger" target="_blank">ABONAR CON QR BANCOLOMBIA</a></p>
							</div>
						<?php }?>
						
	                    <ul class="sidemenu  page-header-fixed <?=$datosUsuarioActual['uss_tipo_menu'];?>" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px" data-step="1" data-intro="<b>Menú principal:</b> Aquí encontrarás las opciones principales para el uso de la plataforma. Algunas estarán activas y otras inactivas, dependiendo los módulos que haya contratado su institución." data-position='left'>
	                        <li class="sidebar-toggler-wrapper hide">
	                            <div class="sidebar-toggler">
	                                <span></span>
	                            </div>
	                        </li>
	                        <?php
							$fotoPerfilUsr = $usuariosClase->verificarFoto($datosUsuarioActual['uss_foto']);
							?>
							<li class="sidebar-user-panel">
	                            <div class="user-panel">
	                                <div class="pull-left image">
	                                    <img src="<?=$fotoPerfilUsr;?>" class="img-circle user-img-circle" alt="User Image" />
	                                </div>
	                                <div class="pull-left info">
	                                    <p> <?=UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual);?></p>
	                                </div>
	                            </div>
	                        </li>

							<?php include_once("menu-metodos.php");?>
							<li <?php agregarClass(MENU,["DT0004"]) ?>>
							<a href="index.php" class="nav-link nav-toggle">
	                                <i class="material-icons">dashboard</i>
	                                <span class="title"><?=$frases[100][$datosUsuarioActual['uss_idioma']];?></span>
                                	<span class="selected"></span>
	                            </a>
	                        </li>

							<?php include_once("menu-directivos.php");?>

							<?php include_once("menu-docentes.php");?>
							
							<?php include_once("menu-acudientes.php");?>
							
							<?php include_once("menu-estudiantes.php");?>

							<li class="nav-item">
	                            <a href="noticias.php" class="nav-link nav-toggle">
	                                <i class="fa fa-bullhorn"></i>
	                                <span class="title"><?=$frases[69][$datosUsuarioActual['uss_idioma']];?></span>
	                            </a>
	                        </li>

							<li class="nav-item">
								<a href="javascript:void(0);" class="nav-link nav-toggle"> 
									<i class="fa fa-question-circle"></i>
									<span class="title">Ayuda</span>
								</a>
								<ul class="sub-menu">
									<li class="nav-item start">
										<a href="javascript:void(0);" onclick="javascript:introJs().start();" class="nav-link">
											<span class="title">Tour SINTIA</span>
										</a>
									</li>
									<?php if ($datosUsuarioActual['uss_tipo'] != TIPO_DOCENTE) {?>
										<li class="nav-item start">
											<a href="como-empezar.php" class="nav-link">
												<span class="title"><?=$frases[255][$datosUsuarioActual['uss_idioma']];?></span>
											</a>
										</li>
									<?php }
									if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
									?>
									<li class="nav-item start">
										<a href="https://docs.google.com/document/d/1ZgtUFs0WJQD797Dp5fy8T-lsUs4BddArW-49mAi5JkQ/edit?usp=sharing" target="_blank" class="nav-link">
											<span class="title">Manual de usuario</span>
										</a>
									</li>
									<?php }?>
									<li class="nav-item start">
										<a href="https://forms.gle/1NpXSwyqoomKdch76" target="_blank" class="nav-link">
											<span class="title"><?=$frases[16][$datosUsuarioActual['uss_idioma']];?>/<?=$frases[257][$datosUsuarioActual['uss_idioma']];?></span>
										</a>
									</li>
									<li class="nav-item start">
										<a href="https://docs.google.com/document/d/1ytMzsH-w3qPUVPF7ScPG3tzEXYcSKbAcaIgLN-VtuyM/edit?usp=sharing" target="_blank" class="nav-link">
											<span class="title">Histórico de cambios</span>
										</a>
									</li>
								</ul>
							</li>

	                    </ul>
	                </div>
                </div>
            </div>
            <!-- end sidebar menu --> 