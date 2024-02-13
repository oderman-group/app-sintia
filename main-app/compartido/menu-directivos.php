<?php 
								//MENÚ DIRECTIVOS
								if($datosUsuarioActual['uss_tipo']==TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo']==TIPO_DEV){
								
								//MÓDULO ACADÉMICO
								if(!empty($arregloModulos) && array_key_exists(1, $arregloModulos)){
									if(Modulos::validarSubRol(["DT0102","DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195"])){
							?>
							<li <?php agregarClass(MENU_PADRE,["DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195","DT0196","DT0197"]) ?>>
	                            <a href="#" class="nav-link nav-toggle"> <i class="material-icons">assignment_ind</i>
	                                <span class="title"><?=$frases[88][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195","DT0196","DT0197"]) ?> >
									
									<?php 
										if(Modulos::validarSubRol(['DT0001'])){
									?>
	                                	<li <?php agregarClass(MENU,["DT0001"]) ?>><a href="estudiantes.php" class="nav-link "> <span class="title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0062'])){
									?>
										<li <?php agregarClass(MENU,["DT0062"]) ?>><a href="cursos.php" class="nav-link "> <span class="title"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0195'])){
									?>
										<li <?php agregarClass(MENU,["DT0195","DT0196","DT0197"]) ?>><a href="grupos.php" class="nav-link "> <span class="title"><?=$frases[254][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0017'])){
									?>
										<li <?php agregarClass(MENU,["DT0017"]) ?>><a href="areas.php" class="nav-link "> <span class="title"><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0020'])){
									?>
										<li <?php agregarClass(MENU,["DT0020"]) ?>><a href="asignaturas.php" class="nav-link "> <span class="title"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0032'])){
									?>
										<li <?php agregarClass(MENU,["DT0032"]) ?>><a href="cargas.php" class="nav-link "> <span class="title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
										if(!empty($arregloModulos) && array_key_exists(9, $arregloModulos)){
											if(Modulos::validarSubRol(['DT0121'])){
									?>
										<li <?php agregarClass(MENU,["DT0121"]) ?>><a href="reservar-cupo.php" class="nav-link "> <span class="title"><?=$frases[391][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php }}?>
									
	                            </ul>
	                        </li>
							<?php }}?>

							<?php 
							//MÓDULO INSCRIPCIONES Y ADMISIONES
							if(!empty($arregloModulos) && array_key_exists(8, $arregloModulos)){
								if(Modulos::validarSubRol(["DT0102"])){
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0102", "DT0014"]) ?>>
									<a href="#" class="nav-link nav-toggle"> <i class="fa fa-address-book"></i>
										<span class="title"><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0102", "DT0014"]) ?>>
										<?php
											if(Modulos::validarSubRol(["DT0102"])){
										?>
											<li <?php agregarClass(MENU,["DT0102"]) ?>><a href="inscripciones.php" class="nav-link "> <span class="title"><?=$frases[392][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>

										<?php
											if(Modulos::validarSubRol(["DT0014"])){
										?>
											<li <?php agregarClass(MENU,["DT0014"]) ?>><a href="configuracion-admisiones.php" class="nav-link "> <span class="title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>
									</ul>
								</li>
							<?php }}?>
							
							<?php 
							//MÓDULO FINANCIERO
							if(!empty($arregloModulos) && array_key_exists(2, $arregloModulos)){
								if(Modulos::validarSubRol(["DT0104", "DT0258", "DT0264", "DT0273", "DT0275", "DT0294"])){
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0104", "DT0106", "DT0128", "DT0105", "DT0258", "DT0259", "DT0261", "DT0264", "DT0265", "DT0267", "DT0273", "DT0275", "DT0276", "DT0278", "DT0294", "DT0295", "DT0297"]) ?>>
									<a href="#" class="nav-link nav-toggle"> <i class="fa fa-money"></i>
										<span class="title"><?=$frases[89][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0104", "DT0106", "DT0128", "DT0105", "DT0258", "DT0259", "DT0261", "DT0264", "DT0265", "DT0267", "DT0273", "DT0275", "DT0276", "DT0278", "DT0294", "DT0295", "DT0297"]) ?>>
										<?php
											if(Modulos::validarSubRol(["DT0104"])){
										?>
											<li <?php agregarClass(MENU,["DT0104", "DT0106", "DT0128", "DT0105"]) ?>><a href="movimientos.php" class="nav-link "> <span class="title"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0275"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0275", "DT0276", "DT0278"]) ?>><a href="factura-recurrente.php" class="nav-link "> <span class="title"><?=$frases[415][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0264"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0264", "DT0265", "DT0267"]) ?>><a href="abonos.php" class="nav-link "> <span class="title"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0258"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0258", "DT0259", "DT0261"]) ?>><a href="items.php" class="nav-link "> <span class="title">Items</span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0294"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0294", "DT0295", "DT0297"]) ?>><a href="impuestos.php" class="nav-link "> <span class="title"><?=$frases[425][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											if(Modulos::validarSubRol(["DT0273"])){
										?>
											<li <?php agregarClass(MENU,["DT0273"]) ?>><a href="configuracion-finanzas.php" class="nav-link "> <span class="title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0305"])){
										?>
											<li <?php agregarClass(MENU,["DT0305"]) ?>><a href="moviminetos-reportes-graficos.php" class="nav-link "> <span class="title"><?=$frases[427][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>
									</ul>
								</li>
							<?php }}?>
							
							<?php 
							//MÓDULO DISCIPLINARIO
							if(!empty($arregloModulos) && array_key_exists(3, $arregloModulos)){
								if(Modulos::validarSubRol(["DT0119","DT0117","DT0069","DT0066"])){
							?>
								<li class="nav-item">
									<a href="#" class="nav-link nav-toggle"> <i class="fa fa-gavel"></i>
										<span class="title"><?=$frases[90][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu">
										<?php
											if(Modulos::validarPermisoEdicion()){
												if(Modulos::validarSubRol(["DT0119"])){
										?>
											<li class="nav-item"><a href="reportes-crear.php" class="nav-link"> <span class="title"><?=$frases[96][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
												}
											}

											if(Modulos::validarSubRol(["DT0117"])){
										?>
											<li class="nav-item"><a href="reportes-lista.php" class="nav-link"> <span class="title"><?=$frases[97][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}

											if(Modulos::validarSubRol(["DT0069"])){
										?>
											<li class="nav-item"><a href="disciplina-categorias.php" class="nav-link"> <span class="title"><?=$frases[222][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											
											if(Modulos::validarSubRol(["DT0066"])){
										?>
											<li class="nav-item"><a href="disciplina-faltas.php" class="nav-link"> <span class="title"><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
										?>
									</ul>
								</li>
							<?php }}?>
							
							<?php 
							//MÓDULO ADMINISTRTIVO
							if(!empty($arregloModulos) && array_key_exists(4, $arregloModulos)){
								if(Modulos::validarSubRol(["DT0126","DT0122","DT0011"])){
							?>
							<li <?php agregarClass(MENU_PADRE,["DT0011","DT0122","DT0124","DT0126","DT0204","DT0205"]) ?>>
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-tachometer"></i>
	                                <span class="title"><?=$frases[87][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0011","DT0122","DT0124","DT0126","DT0204","DT0205"])?>>
									<?php
										if(Modulos::validarSubRol(["DT0126"])){
									?>
										<li <?php agregarClass(MENU,["DT0126","DT0124"]) ?>><a href="usuarios.php" class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
										if(Modulos::validarSubRol(["DT0122"])){
									?>
										<li <?php agregarClass(MENU,["DT0122"]) ?>><a href="solicitudes.php" class="nav-link "> <span class="title">Solicitud desbloqueo</span></a></li>
									<?php
										}
										
										if(Modulos::validarSubRol(["DT0011"])){
									?>
										<li <?php agregarClass(MENU,["DT0011"]) ?>><a href="galeria.php" class="nav-link "> <span class="title"><?=$frases[223][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
										if( array_key_exists(16, $arregloModulos) && Modulos::validarSubRol(["DT0204"])){
									?>
										<li <?php agregarClass(MENU,["DT0204","DT0205"]) ?>><a href="sub-roles.php" class="nav-link"> <span class="title">Sub Roles</span></a></li>
									<?php
										}
									?>
	                            </ul>
	                        </li>
							<?php }}?>

							<?php 
							//MÓDULO CUESTIONARIO EVALUATIVO
							if(!empty($arregloModulos) && array_key_exists(18, $arregloModulos)){?>
								<li <?php agregarClass(MENU_PADRE,["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291"]) ?>>
									<a href="javascript:void(0);" class="nav-link nav-toggle"> <i class="fa fa-question"></i>
										<span class="title"><?=$frases[388][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291"])?>>
										<li ><a href="javascript:void(0);" class="nav-link "> <span class="title"><?=$frases[393][$datosUsuarioActual['uss_idioma']];?></span></a></li>									
										<li <?php agregarClass(MENU,["DT0288","DT0289","DT0291"]) ?>><a href="preguntas.php" class="nav-link "> <span class="title"><?=$frases[139][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<li <?php agregarClass(MENU,["DT0281","DT0283","DT0285"]) ?>><a href="evaluaciones.php" class="nav-link "> <span class="title"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></span></a></li>
								
	
									</ul>
								</li>
								<?php }?>
							
							<?php 
							//MÓDULO MERCADEO
							if(!empty($arregloModulos) && array_key_exists(6, $arregloModulos)){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-phone"></i>
	                                <span class="title"><?=$frases[210][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item"><a href="#" class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></span></a></li>

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
								if(Modulos::validarSubRol(["DT0099"])){
							?>
							<li class="nav-item">
	                            <a href="informes-todos.php" class="nav-link nav-toggle"> <i class="fa fa-file-text"></i>
	                                <span class="title"><?=$frases[385][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							<?php }?>

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