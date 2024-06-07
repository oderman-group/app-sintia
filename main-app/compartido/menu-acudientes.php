<?php 
							//MENÚ ACUDIENTES
							if($datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE){?>
							
							<li class="nav-item" data-step="10" data-intro="<b><?=$frases[71][$datosUsuarioActual['uss_idioma']];?>:</b> Aquí verás tus acudidos y toda su información." data-position='left'>
	                            <a <?php validarModuloMenu(7, "estudiantes.php", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-group"></i>
	                                <span class="title"><?=$frases[71][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							
							<?php if(Modulos::validarModulosActivos($conexion, 2)){ ?>
							<li class="nav-item" data-step="12" data-intro="<b><?=$frases[104][$datosUsuarioActual['uss_idioma']];?>:</b> Aquí verás toda la información relacionada con tu estado de cuenta financiero." data-position='left'>
	                            <a <?php validarModuloMenu(2, "estado-de-cuenta.php", MENU) ?> class="nav-link nav-toggle"> <i class="material-icons">attach_money</i>
	                                <span class="title"><?=$frases[104][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							<?php }?>

							<?php if(Modulos::validarModulosActivos($conexion, 20)){ ?>
							<li class="nav-item active" data-step="11" data-intro="<b><?=$frases[175][$datosUsuarioActual['uss_idioma']];?>:</b> Encuentra los mejores productos y servicios complementarios." data-position='left'>
	                            <a <?php validarModuloMenu(20, "marketplace.php", MENU) ?> class="nav-link nav-toggle bg-warning text-dark"> <i class="fa fa-shopping-cart text-dark"></i>
	                                <span class="title">Marketplace</span> 
	                            </a>
	                        </li>
							<?php }?>
							
							<?php }?>