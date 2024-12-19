							<?php 
							//MENÚ DIRECTIVOS
							if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
								//MÓDULO ACADÉMICO
								if (
									Modulos::validarSubRol(["DT0102","DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195"]) &&
									Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_ACADEMICO)
								) {
							?>
							<li <?php agregarClass(MENU_PADRE,["DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195","DT0196","DT0197"]) ?> data-step="2" data-intro="<b>Gestión Académica:</b> Aquí podrás gestionar las opciones académicas: Matriculas, cursos, áreas, asignaturas, cargas académicas, etc." data-position='left'>
	                            <a <?php validarModuloMenu(Modulos::MODULO_ACADEMICO, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> 
									<i class="fa fa-vcard"></i>
	                                <span class="title"><?=$frases[88][$datosUsuarioActual['uss_idioma']];?></span> 
									<span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0001","DT0062","DT0017","DT0020","DT0032","DT0121","DT0195","DT0196","DT0197"]) ?> >
									
									<?php 
										if(Modulos::validarSubRol(['DT0001'])){
									?>
	                                	<li <?php agregarClass(MENU,["DT0001"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ACADEMICO, "estudiantes.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0062'])){
									?>
										<li <?php agregarClass(MENU,["DT0062"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ACADEMICO, "cursos.php", MENU) ?>class="nav-link "> <span class="title"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0195'])){
									?>
										<li <?php agregarClass(MENU,["DT0195","DT0196","DT0197"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ACADEMICO, "grupos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[254][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0017'])){
									?>
										<li <?php agregarClass(MENU,["DT0017"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ACADEMICO, "areas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[93][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0020'])){
									?>
										<li <?php agregarClass(MENU,["DT0020"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ACADEMICO, "asignaturas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
									
										if(Modulos::validarSubRol(['DT0032'])){
									?>
										<li <?php agregarClass(MENU,["DT0032"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ACADEMICO, "cargas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
											if (
												Modulos::validarSubRol(['DT0121']) &&
												Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_RESERVA_CUPO)
											) {
									?>
										<li <?php agregarClass(MENU,["DT0121"]) ?>>
											<a 
												<?php validarModuloMenu(Modulos::MODULO_RESERVA_CUPO, "reservar-cupo.php", MENU) ?> 
												class="nav-link"
											> 
												<span class="title"><?=$frases[391][$datosUsuarioActual['uss_idioma']];?></span>
											</a>
										</li>
									<?php }?>
									
	                            </ul>
	                        </li>
							<?php }?>

							<?php 
							//MÓDULO INSCRIPCIONES Y ADMISIONES
								if (
									Modulos::validarSubRol(["DT0102", "DT0014"]) &&
									Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_ADMISIONES)
								) {
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0102", "DT0014"]) ?> data-step="3" data-intro="<b>Admisiones e inscripciones:</b> Módulo para gestionar las inscripciones de nuevos estudiantes a tus institución." data-position='bottom'>
									<a <?php validarModuloMenu(Modulos::MODULO_ADMISIONES, "#", MENU_PADRE);?> class="nav-link nav-toggle"> <i class="fa fa-address-book"></i>
										<span class="title"><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0102", "DT0014"]) ?>>
										<?php
											if(Modulos::validarSubRol(["DT0102"])){
										?>
											<li <?php agregarClass(MENU,["DT0102"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ADMISIONES, "inscripciones.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[392][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>

										<?php
											if(Modulos::validarSubRol(["DT0014"])){
										?>
											<li <?php agregarClass(MENU,["DT0014"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ADMISIONES, "configuracion-admisiones.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO FINANCIERO
								if (
									Modulos::validarSubRol(["DT0104", "DT0258", "DT0264", "DT0273", "DT0275", "DT0294"]) &&
									Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_FINANCIERO)
								) {
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0104", "DT0106", "DT0128", "DT0105", "DT0258", "DT0259", "DT0261", "DT0264", "DT0265", "DT0267", "DT0273", "DT0275", "DT0276", "DT0278", "DT0294", "DT0295", "DT0297"]) ?> data-step="4" data-intro="<b>G. Financiera:</b> Maneja las finanzas de tu institución desde aqui. Facturas, cobros, abonos." data-position='bottom' data-scrollTo='tooltip'>
									<a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-money"></i>
										<span class="title"><?=$frases[89][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0104", "DT0106", "DT0128", "DT0105", "DT0258", "DT0259", "DT0261", "DT0264", "DT0265", "DT0267", "DT0273", "DT0275", "DT0276", "DT0278", "DT0294", "DT0295", "DT0297"]) ?>>
										<?php
											if(Modulos::validarSubRol(["DT0104"])){
										?>
											<li <?php agregarClass(MENU,["DT0104", "DT0106", "DT0128", "DT0105"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "movimientos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0275"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0275", "DT0276", "DT0278"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "factura-recurrente.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[415][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0264"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0264", "DT0265", "DT0267"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "abonos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0258"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0258", "DT0259", "DT0261"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "items.php", MENU) ?> class="nav-link "> <span class="title">Items</span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0294"])){ 
										?>
											<li <?php agregarClass(MENU,["DT0294", "DT0295", "DT0297"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "impuestos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[425][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											if(Modulos::validarSubRol(["DT0273"])){
										?>
											<li <?php agregarClass(MENU,["DT0273"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "configuracion-finanzas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[17][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php 
											}
											if(Modulos::validarSubRol(["DT0305"])){
										?>
											<li <?php agregarClass(MENU,["DT0305"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_FINANCIERO, "moviminetos-reportes-graficos.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[427][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php }?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO DISCIPLINARIO
								if (
									Modulos::validarSubRol(["DT0119","DT0117","DT0069","DT0066"]) &&
									Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_DISCIPLINARIO)
								) {
							?>
								<li class="nav-item" data-step="5" data-intro="<b>Gestión de comportamiento:</b> Gestiona las categorias y tipifica las faltas de comportamiento. Crea reportes y obten informes disciplinarios." data-position='bottom' data-scrollTo='tooltip'>
									<a <?php validarModuloMenu(Modulos::MODULO_DISCIPLINARIO, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-gavel"></i>
										<span class="title"><?=$frases[90][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu">
										<?php
											if(Modulos::validarPermisoEdicion()){
												if(Modulos::validarSubRol(["DT0119"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(Modulos::MODULO_DISCIPLINARIO, "reportes-crear.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[96][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
												}
											}

											if(Modulos::validarSubRol(["DT0117"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(Modulos::MODULO_DISCIPLINARIO, "reportes-lista.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[97][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}

											if(Modulos::validarSubRol(["DT0069"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(Modulos::MODULO_DISCIPLINARIO, "disciplina-categorias.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[222][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											
											if(Modulos::validarSubRol(["DT0066"])){
										?>
											<li class="nav-item"><a <?php validarModuloMenu(Modulos::MODULO_DISCIPLINARIO, "disciplina-faltas.php", MENU) ?> class="nav-link"> <span class="title"><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
										?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO ADMINISTRTIVO
								if(
									Modulos::validarSubRol(["DT0126","DT0122","DT0011"]) &&
									Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_ADMINISTRATIVO)
								) {
							?>
							<li <?php agregarClass(MENU_PADRE,["DT0011","DT0122","DT0124","DT0126","DT0204","DT0205"]) ?> data-step="6" data-intro="<b>Gestión administrativa:</b> Gestiona tus tipos de usuarios, asigna roles y permisos. También puedes revisar las solicitudes desbloqueo." data-position='bottom' data-scrollTo='tooltip'>
	                            <a <?php validarModuloMenu(Modulos::MODULO_ADMINISTRATIVO, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-briefcase"></i>
	                                <span class="title"><?=$frases[87][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0011","DT0122","DT0124","DT0126","DT0204","DT0205"])?>>
									<?php
										if(Modulos::validarSubRol(["DT0126"])){
									?>
										<li <?php agregarClass(MENU,["DT0126","DT0124"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ADMINISTRATIVO, "usuarios.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
										if(Modulos::validarSubRol(["DT0122"])){
									?>
										<li <?php agregarClass(MENU,["DT0122"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ADMINISTRATIVO, "solicitudes.php", MENU) ?> class="nav-link "> <span class="title">Solicitud desbloqueo</span></a></li>
									<?php
										}
										
										if(Modulos::validarSubRol(["DT0011"])){
									?>
										<li <?php agregarClass(MENU,["DT0011"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_ADMINISTRATIVO, "galeria.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[223][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php
										}
										
										if (
											Modulos::validarSubRol(["DT0204"]) &&
											Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_SUB_ROLES)
										) {
									?>
										<li <?php agregarClass(MENU,["DT0204","DT0205"]) ?>>
											<a 
												<?php validarModuloMenu(Modulos::MODULO_SUB_ROLES, "sub-roles.php", MENU) ?> 
												class="nav-link"
											> 
												<span class="title">Sub Roles</span>
											</a>
										</li>
									<?php
										}
									?>
	                            </ul>
	                        </li>
							<?php }?>
							
							<?php 
							//MÓDULO CUESTIONARIO EVALUATIVO
								if(
									Modulos::validarSubRol(["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291","DT0308","DT0309","DT0311"]) &&
									Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_CUESTIONARIOS)
								) {
							?>
								<li <?php agregarClass(MENU_PADRE,["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291","DT0308","DT0309","DT0311"]) ?> data-step="7" data-intro="<b>Módulo de cuestionarios:</b> Crear cuestionarios para evaluar a los usuarios de todos los roles y obten reportes precisos de dichos cuestionarios." data-position='bottom' data-scrollTo='tooltip'>
									<a <?php validarModuloMenu(Modulos::MODULO_CUESTIONARIOS, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-check-square-o"></i>
										<span class="title"><?=$frases[388][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0281","DT0283","DT0285","DT0288","DT0289","DT0291","DT0308","DT0309","DT0311"])?>>								
										<?php
											if(Modulos::validarSubRol(["DT0281"])){
										?>
											<li <?php agregarClass(MENU,["DT0281","DT0283","DT0285"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_CUESTIONARIOS, "evaluaciones.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											if(Modulos::validarSubRol(["DT0288"])){
										?>
											<li <?php agregarClass(MENU,["DT0288","DT0289","DT0291"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_CUESTIONARIOS, "preguntas.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[139][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
											if(Modulos::validarSubRol(["DT0308"])){
										?>
											<li <?php agregarClass(MENU,["DT0308","DT0309","DT0311"]) ?>><a <?php validarModuloMenu(Modulos::MODULO_CUESTIONARIOS, "respuesta.php", MENU) ?> class="nav-link "> <span class="title"><?=$frases[428][$datosUsuarioActual['uss_idioma']];?></span></a></li>
										<?php
											}
										?>
									</ul>
								</li>
							<?php }?>
							
							<?php 
							//MÓDULO MERCADEO
								if(Modulos::verificarModulosDeInstitucion(null, Modulos::MODULO_MERCADEO)){
							?>
								<li class="nav-item">
									<a <?php validarModuloMenu(Modulos::MODULO_MERCADEO, "#", MENU_PADRE) ?> class="nav-link nav-toggle"> <i class="fa fa-phone"></i>
										<span class="title"><?=$frases[210][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
									</a>
									<ul class="sub-menu">
										<li class="nav-item"><a <?php validarModuloMenu(Modulos::MODULO_MERCADEO, "#", MENU) ?> class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									</ul>
								</li>
							<?php }?>
							
							<?php
							//CONFIGURACIÓN
								if(Modulos::validarSubRol(["DT0057","DT0060"])){
							?>
							<li class="nav-item" data-step="8" data-intro="<b>Configuración:</b> Esta es una de las partes más importantes porque puedes definir los comportamientos que tendrá la plataforma en varios aspectos. ¡Exploralo!" data-position='bottom' data-scrollTo='tooltip'>
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
							//INFORMES
								if (
									Modulos::validarSubRol(["DT0099"]) &&
									Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_INFORMES_BASE)
								) {
							?>
								<li class="nav-item" data-step="9" data-intro="<b>Informes:</b> Obten todos los informes y reportes que tu institución necesita para la toma oportuna de decisiones." data-position='bottom' data-scrollTo='tooltip'>
									<a <?php validarModuloMenu(Modulos::MODULO_INFORMES_BASE, "informes-todos.php", MENU) ?> class="nav-link nav-toggle"> <i class="fa fa-file-text"></i>
										<span class="title"><?=$frases[385][$datosUsuarioActual['uss_idioma']];?></span> 
									</a>
								</li>
							<?php }?>
							
							<!-- Servicios complementarios de SINTIA -->
							<li <?php agregarClass(MENU_PADRE,["DT0335","DT0336"]) ?> class="nav-item">
								<a href="#" class="nav-link nav-toggle"><i class="fa fa-shopping-cart"></i>
									<span class="title">Servicios SINTIA</span> <span class="arrow"></span>
								</a>
								<ul class="sub-menu" <?php agregarClass(SUB_MENU,["DT0335","DT0336"])?>>
									<li <?php agregarClass(MENU,["DT0335"]) ?>><a href="servicios-modulos.php" class="nav-link"> <span class="title">Modulos</span></a></li>
									<li <?php agregarClass(MENU,["DT0336"]) ?>><a href="servicios-paquetes.php" class="nav-link"> <span class="title">Paquetes</span></a></li>
								</ul>
							</li>

							<?php
								if (
									$datosUsuarioActual['uss_permiso1'] == CODE_DEV_MODULE_PERMISSION &&
									$datosUsuarioActual['uss_tipo'] == TIPO_DEV &&
									($_SESSION["idInstitucion"] == DEVELOPER_PROD || $_SESSION["idInstitucion"] == DEVELOPER) 
								) {
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