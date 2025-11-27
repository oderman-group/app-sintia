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

							<?php include_once("menu-metodos.php");?>
							<li <?php agregarClass(MENU,["DT0004"]) ?>>
							<a href="index.php" class="nav-link nav-toggle">
	                                <div class="menu-icon icon-dashboard">
	                                    <i class="fa fa-th-large"></i>
	                                </div>
	                                <span class="title"><?=$frases[100][$datosUsuarioActual['uss_idioma']];?></span>
                                	<span class="selected"></span>
	                            </a>
	                        </li>

							<?php include_once("menu-directivos.php");?>

							<?php include_once("menu-docentes.php");?>
							
							<?php include_once("menu-acudientes.php");?>
							
							<?php include_once("menu-estudiantes.php");?>

	                    </ul>
	                </div>
                </div>
            </div>
            <!-- end sidebar menu --> 