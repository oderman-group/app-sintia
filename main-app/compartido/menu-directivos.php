<?php 
								//MENÚ DIRECTIVOS
								if($datosUsuarioActual['uss_tipo']==TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo']==TIPO_DEV){
									//MÓDULO ACADÉMICO
									if(Modulos::validarSubRol(["DT0102","DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195"]) && Modulos::validarModulosActivos($conexion, 1)){
							?>
							<li <?php agregarClass(MENU_PADRE,["DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195","DT0196","DT0197"]) ?>>
	                            <a <?php validarModuloMenu(1, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="material-icons">assignment_ind</i>
	                                <span class="title"><?=$frases[88][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195","DT0196","DT0197"]) ?> >
									
									<?php 
										if(Modulos::validarSubRol(['DT0001'])){
									?>
	                                	<li <?php agregarClass(MENU,["DT0001"]) ?>><a <?php validarModuloMenu(1, "estudiantes.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0062'])){
									?>
										<li <?php agregarClass(MENU,["DT0062"]) ?>><a <?php validarModuloMenu(1, "cursos.php", MENU) ?>class="nav-link "> <span class="title"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0195'])){
									?>
										<li <?php agregarClass(MENU,["DT0195","DT0196","DT0197"]) ?>><a <?php validarModuloMenu(1, "grupos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[254][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0017'])){
									?>
										<li <?php agregarClass(MENU,["DT0017"]) ?>><a <?php validarModuloMenu(1, "areas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0020'])){
									?>
										<li <?php agregarClass(MENU,["DT0020"]) ?>><a <?php validarModuloMenu(1, "asignaturas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0032'])){
									?>
										<li <?php agregarClass(MENU,["DT0032"]) ?>><a <?php validarModuloMenu(1, "cargas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
											if(Modulos::validarSubRol(['DT0121']) && Modulos::validarModulosActivos($conexion, 9)){
									?>
										<li <?php agregarClass(MENU,["DT0121"]) ?>><a <?php validarModuloMenu(9, "reservar-cupo.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[391][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php }?>
									
	                            </ul>
	                        </li>
							<?php }?>

							<?php 
							//MÓDULO INSCRIPCIONES Y ADMISIONES
								if(Modulos::validarSubRol(["DT0102", "DT0014"]) && Modulos::validarModulosActivos($conexion, 8)){
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0102", "DT0014"]) ?>>
									<a <?php validarModuloMenu(8, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-address-book"></i>
										<span class="title"><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0102", "DT0014"]) ?>>
										<?php
											if(Modulos::validarSubRol(["DT0102"])){
										?>
											<li <?php agregarClass(MENU,["DT0102"]) ?>><a <?php validarModuloMenu(8, "inscripciones.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[392][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>

										<?php
											if(Modulos::validarSubRol(["DT0014"])){
										?>
											<li <?php agregarClass(MENU,["DT0014"]) ?>><a <?php validarModuloMenu(8, "configuracion-admisiones.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO FINANCIERO
								if(Modulos::validarSubRol(["DT0104", "DT0258", "DT0264", "DT0273", "DT0275", "DT0294"]) && Modulos::validarModulosActivos($conexion, 2)){
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0104", "DT0106", "DT0128", "DT0105", "DT0258", "DT0259", "DT0261", "DT0264", "DT0265", "DT0267", "DT0273", "DT0275", "DT0276", "DT0278", "DT0294", "DT0295", "DT0297"]) ?>>
									<a <?php validarModuloMenu(2, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-money"></i>
										<span class="title"><?=$frases[89][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0104", "DT0106", "DT0128", "DT0105", "DT0258", "DT0259", "DT0261", "DT0264", "DT0265", "DT0267", "DT0273", "DT0275", "DT0276", "DT0278", "DT0294", "DT0295", "DT0297"]) ?>>
										<?php
											if(Modulos::validarSubRol(["DT0104"])){
										?>
											<li <?php agregarClass(MENU,["DT0104", "DT0106", "DT0128", "DT0105"]) ?>><a <?php validarModuloMenu(2, "movimientos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0275"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0275", "DT0276", "DT0278"]) ?>><a <?php validarModuloMenu(2, "factura-recurrente.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[415][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0264"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0264", "DT0265", "DT0267"]) ?>><a <?php validarModuloMenu(2, "abonos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0258"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0258", "DT0259", "DT0261"]) ?>><a <?php validarModuloMenu(2, "items.php", MENU) ?> class="nav-link "> <span class="title">Items</span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0294"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0294", "DT0295", "DT0297"]) ?>><a <?php validarModuloMenu(2, "impuestos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[425][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											if(Modulos::validarSubRol(["DT0273"])){
										?>
											<li <?php agregarClass(MENU,["DT0273"]) ?>><a <?php validarModuloMenu(2, "configuracion-finanzas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0305"])){
										?>
											<li <?php agregarClass(MENU,["DT0305"]) ?>><a <?php validarModuloMenu(2, "moviminetos-reportes-graficos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[427][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO DISCIPLINARIO
								if(Modulos::validarSubRol(["DT0119","DT0117","DT0069","DT0066"]) && Modulos::validarModulosActivos($conexion, 3)){
							?>
								<li class="nav-item">
									<a <?php validarModuloMenu(3, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-gavel"></i>
										<span class="title"><?=$frases[90][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu">
										<?php
											if(Modulos::validarPermisoEdicion()){
												if(Modulos::validarSubRol(["DT0119"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(3, "reportes-crear.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[96][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
												}
											}

											if(Modulos::validarSubRol(["DT0117"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(3, "reportes-lista.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[97][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}

											if(Modulos::validarSubRol(["DT0069"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(3, "disciplina-categorias.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[222][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											
											if(Modulos::validarSubRol(["DT0066"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(3, "disciplina-faltas.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
										?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO ADMINISTRTIVO
								if(Modulos::validarSubRol(["DT0126","DT0122","DT0011"]) && Modulos::validarModulosActivos($conexion, 4)){
							?>
							<li <?php agregarClass(MENU_PADRE,["DT0011","DT0122","DT0124","DT0126","DT0204","DT0205"]) ?>>
	                            <a <?php validarModuloMenu(4, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-tachometer"></i>
	                                <span class="title"><?=$frases[87][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0011","DT0122","DT0124","DT0126","DT0204","DT0205"])?>>
									<?php
										if(Modulos::validarSubRol(["DT0126"])){
									?>
										<li <?php agregarClass(MENU,["DT0126","DT0124"]) ?>><a <?php validarModuloMenu(4, "usuarios.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
										if(Modulos::validarSubRol(["DT0122"])){
									?>
										<li <?php agregarClass(MENU,["DT0122"]) ?>><a <?php validarModuloMenu(4, "solicitudes.php", MENU) ?> class="nav-link "> <span class="title">Solicitud desbloqueo</span></a></li>
									<?php
										}
										
										if(Modulos::validarSubRol(["DT0011"])){
									?>
										<li <?php agregarClass(MENU,["DT0011"]) ?>><a <?php validarModuloMenu(4, "galeria.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[223][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
										if( Modulos::validarSubRol(["DT0204"]) && Modulos::validarModulosActivos($conexion, 16)){
									?>
										<li <?php agregarClass(MENU,["DT0204","DT0205"]) ?>><a <?php validarModuloMenu(16, "sub-roles.php", MENU) ?> class="nav-link"> <span class="title">Sub Roles</span></a></li>
									<?php
										}
									?>
	                            </ul>
	                        </li>
							<?php }?>
							
							<?php 
							//MÓDULO CUESTIONARIO EVALUATIVO
								if(Modulos::validarSubRol(["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291","DT0308","DT0309","DT0311"]) && Modulos::validarModulosActivos($conexion, 18)){
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291","DT0308","DT0309","DT0311"]) ?>>
									<a <?php validarModuloMenu(18, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-question"></i>
										<span class="title"><?=$frases[388][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291","DT0308","DT0309","DT0311"])?>>								
										<?php
											if(Modulos::validarSubRol(["DT0281"])){
										?>
											<li <?php agregarClass(MENU,["DT0281","DT0283","DT0285"]) ?>><a <?php validarModuloMenu(18, "evaluaciones.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											if(Modulos::validarSubRol(["DT0288"])){
										?>
											<li <?php agregarClass(MENU,["DT0288","DT0289","DT0291"]) ?>><a <?php validarModuloMenu(18, "preguntas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[139][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											if(Modulos::validarSubRol(["DT0308"])){
										?>
											<li <?php agregarClass(MENU,["DT0308","DT0309","DT0311"]) ?>><a <?php validarModuloMenu(18, "respuesta.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[428][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
										?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO MERCADEO
								if(Modulos::validarModulosActivos($conexion, 6)){
							?>
								<li class="nav-item">
									<a <?php validarModuloMenu(6, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-phone"></i>
										<span class="title"><?=$frases[210][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu">
										<li class="nav-item"><a <?php validarModuloMenu(6, "#", MENU) ?> class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									</ul>
								</li>
							<?php }?>
							
							<?php
								if(Modulos::validarSubRol(["DT0057","DT0060"])){
							?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-cogs"></i></i>
	                                <span class="title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?> </span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
									<?php
										if(Modulos::validarSubRol(["DT0057"])){
									?>
										<li><a href="configuracion-sistema.php"><?=$frases[395][$datosUsuarioActual['uss_idioma']];?></a></li>
									<?php
										}
										
										if(Modulos::validarSubRol(["DT0060"])){
									?>
										<li><a href="configuracion-institucion.php"><?=$frases[396][$datosUsuarioActual['uss_idioma']];?></a></li>
									<?php
										}
									?>
	                            </ul>
	                        </li>
							<?php }?>
							
							<?php
								if(Modulos::validarSubRol(["DT0099"]) && Modulos::validarModulosActivos($conexion, 22)){
							?>
							<li class="nav-item">
	                            <a <?php validarModuloMenu(22, "informes-todos.php", MENU) ?> class="nav-link nav-toggle"> <i class="fa fa-file-text"></i>
	                                <span class="title"><?=$frases[385][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							<?php }?>
							
							<li <?php agregarClass(MENU_PADRE,["DT0335","DT0336"]) ?> class="nav-item bg-warning">
								<a href="#" class="nav-link nav-toggle bg-warning text-dark"><i class="fa-solid fa-cart-plus text-dark"></i>
									<span class="title">Servicios SINTIA</span> <span class="arrow"></span>
								</a>
								<ul class="sub-menu bg-warning text-dark" <?php agregarClass(SUB_MENU,["DT0335","DT0336"])?>>
									<li <?php agregarClass(MENU,["DT0335"]) ?>><a href="servicios-modulos.php" class="nav-link text-dark"> <span class="title">Modulos</span></a></li>
									<li <?php agregarClass(MENU,["DT0336"]) ?>><a href="servicios-paquetes.php" class="nav-link text-dark"> <span class="title">Paquetes</span></a></li>
								</ul>
							</li>

							<?php
								if($datosUsuarioActual['uss_permiso1'] == CODE_DEV_MODULE_PERMISSION && $datosUsuarioActual['uss_tipo'] == TIPO_DEV && ($_SESSION["idInstitucion"] == DEVELOPER_PROD || $_SESSION["idInstitucion"] == DEVELOPER) ){
							?>
								<li  <?php agregarClass(MENU_PADRE,["DV0038","DV0039", "DV0074", "DV0075", "DV0002 "]) ?> >
									<a href="#" class="nav-link nav-toggle"> <i class="fa fa-database"></i>
										<span class="title">DEV-ADMIN</span> <span class="arrow"></span>
									</a>
									<ul  class="sub-menu" <?php agregarClass(SUB_MENU,["DV0038","DV0039", "DV0074", "DV0075", "DV0002"])?>>
										<li <?php agregarClass(MENU,["DV0074", "DV0075", "DV0002"]) ?>><a href="dev-scripts.php" class="nav-link"> <span class="title">scripts SQL</span></a></li>
										<li class="nav-item"><a href="dev-crear-nueva-bd.php" class="nav-link"> <span class="title"><?=$frases[397][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="dev-errores-sistema.php" class="nav-link"> <span class="title"><?=$frases[398][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="dev-console.php" class="nav-link"> <span class="title">Console</span></a></li>
										<li class="nav-item"><a href="dev-historial-acciones.php" class="nav-link"> <span class="title"><?=$frases[400][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="dev-instituciones.php" class="nav-link"> <span class="title"><?=$frases[399][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li  <?php agregarClass(MENU,["DV0038","DV0039"]) ?>><a href="dev-solicitudes-cancelacion.php" class="nav-link"> <span class="title"><?=$frases[401][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="dev-modulos.php" class="nav-link"> <span class="title"><?=$frases[402][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="dev-paginas.php" class="nav-link"> <span class="title"><?=$frases[403][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="configuracion-opciones-generales.php" class="nav-link"> <span class="title"><?=$frases[404][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="#" class="nav-link"> <span class="title"><?=$frases[405][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="dev-contratos.php" class="nav-link"> <span class="title"><?=$frases[406][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="dev-terminos.php" class="nav-link"> <span class="title">T&C</span></a></li>
										<li class="nav-item"><a href="dev-datos-contacto.php" class="nav-link"> <span class="title"><?=$frases[407][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									</ul>
								</li>
								
								<li class="nav-item">
									<a href="#" class="nav-link nav-toggle"> <i class="fa fa-shopping-cart"></i>
										<span class="title">ADMIN-MPS</span> <span class="arrow"></span>
									</a>
									<ul  class="sub-menu">
										<li class="nav-item"><a href="mps-categorias-productos.php" class="nav-link"> <span class="title"><?=$frases[408][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="mps-categorias-servicios.php" class="nav-link"> <span class="title"><?=$frases[409][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="mps-productos.php" class="nav-link"> <span class="title"><?=$frases[410][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li class="nav-item"><a href="mps-empresas.php" class="nav-link"> <span class="title"><?=$frases[411][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									</ul>
								</li>
							<?php }?>
							
							<?php }?>