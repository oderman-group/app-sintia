<!-- start sidebar menu -->
 			<div class="sidebar-container" >
 				<div class="sidemenu-container navbar-collapse collapse fixed-menu">
	                <div id="remove-scroll">
				
						<?php
						//Mostrar a los directivos si tiene deuda
						if($config['conf_deuda']==1 and $datosUsuarioActual['uss_tipo']==5){
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
						
	                    <ul class="sidemenu  page-header-fixed <?=$datosUsuarioActual['uss_tipo_menu'];?>" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px" data-step="1" data-intro="<b>Menú principal:</b> Aquí encontrarás todas las opciones para el uso de la plataforma." data-position='left'>
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
	                                    <p> <?=$datosUsuarioActual['uss_nombre'];?></p>
	                                    <a href="#"><i class="fa fa-circle user-online"></i><span class="txtOnline"> <?=$_SESSION["bd"];?></span></a>
	                                </div>
	                            </div>
	                        </li>
							
							<?php
								if($datosUsuarioActual[3]==5 || $datosUsuarioActual[3]==1){
							?>
							<div class="nav-item">
								<div align="center" style="color:#FC0; font-weight:bold;">
									AÑO CONSULTADO<br />
									<a href="cambiar-bd.php" style="font-size:36px; color:#FC0; font-weight:bold; text-decoration:underline;"><?=$_SESSION["bd"];?></a>
								</div>
							</div>
							<?php }?>
							
							<li class="nav-item start active">
	                            <a href="javascript:void(0);" onclick="javascript:introJs().start();" class="nav-link nav-toggle">
	                                <i class="fa fa-life-ring"></i>
	                                <span class="title">INDUCCIÓN SINTIA</span>
                                	<span class="selected"></span>
	                            </a>
	                        </li>
							
							<li class="nav-item">
	                            <a href="index.php" class="nav-link nav-toggle">
	                                <i class="material-icons">dashboard</i>
	                                <span class="title"><?=$frases[100][$datosUsuarioActual['uss_idioma']];?></span>
                                	<span class="selected"></span>
	                            </a>
	                        </li>
							
							<li class="nav-item" data-step="8" data-intro="<b><?=$frases[69][$datosUsuarioActual['uss_idioma']];?>:</b> Aquí podrás ver y publicar noticias. También verás cumpleaños y otra información de interés." data-position='left'>
	                            <a href="noticias.php" class="nav-link nav-toggle">
	                                <i class="material-icons">view_comfy</i>
	                                <span class="title"><?=$frases[69][$datosUsuarioActual['uss_idioma']];?></span>
	                            </a>
	                        </li>
							
							
							
							<?php 
							//MENÚ DIRECTIVOS
							if($datosUsuarioActual[3]==5 || $datosUsuarioActual[3]==1){?>
							
							<li class="nav-item" data-step="9" data-intro="<b><?=$frases[223][$datosUsuarioActual['uss_idioma']];?>:</b> Aquí podrás ver las fotos de perfil que los usuarios han montado." data-position='left'>
	                            <a href="galeria.php" class="nav-link nav-toggle">
	                                <i class="fa fa-picture-o"></i>
	                                <span class="title"><?=$frases[223][$datosUsuarioActual['uss_idioma']];?></span>
	                            </a>
	                        </li>
							
							<li class="nav-item" data-step="9" data-intro="<b>Reservar Cupos:</b> Aquí podras ver los estudiantes que han reservado cupos." data-position='left'>
	                            <a href="reservar-cupo.php" class="nav-link nav-toggle">
	                                <i class="fa fa-file"></i>
	                                <span class="title">R. Cupos</span>
	                            </a>
	                        </li>
							
							<li class="nav-item" data-step="9" data-intro="<b>Solicitudes de Bloqueo:</b> Aquí podras ver las solicitudes de estudiantes bloqueados." data-position='left'>
	                            <a href="solicitudes.php" class="nav-link nav-toggle">
	                                <i class="fa fa-lock"></i>
	                                <span class="title">S. Desbloqueo</span>
	                            </a>
	                        </li>
							
							
							<?php 
							//MÓDULO ACADÉMICO
							if($arregloModulos[1]==1){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="material-icons">assignment_ind</i>
	                                <span class="title"><?=$frases[88][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item"><a href="inscripciones.php" class="nav-link "> <span class="title">Inscripciones</span></a></li>
	                                <li class="nav-item"><a href="estudiantes.php?cantidad=10" class="nav-link "> <span class="title"><?=$frases[209][$datosUsuarioActual[8]];?></span></a></li>
									<li class="nav-item"><a href="cursos.php" class="nav-link "> <span class="title"><?=$frases[5][$datosUsuarioActual[8]];?></span></a></li>
									<li class="nav-item"><a href="areas.php" class="nav-link "> <span class="title"><?=$frases[93][$datosUsuarioActual[8]];?></span></a></li>
									<li class="nav-item"><a href="asignaturas.php" class="nav-link "> <span class="title"><?=$frases[73][$datosUsuarioActual[8]];?></span></a></li>
									<li class="nav-item"><a href="cargas.php" class="nav-link "> <span class="title"><?=$frases[12][$datosUsuarioActual[8]];?></span></a></li>
	                            </ul>
	                        </li>
							<?php }?>
							
							<?php 
							//MÓDULO FINANCIERO
							if($arregloModulos[2]==1){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-money"></i>
	                                <span class="title"><?=$frases[89][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item"><a href="movimientos.php" class="nav-link "> <span class="title"><?=$frases[95][$datosUsuarioActual[8]];?></span></a></li>

	                            </ul>
	                        </li>
							<?php }?>
							
							<?php 
							//MÓDULO DISCIPLINARIO
							if($arregloModulos[3]==1){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-gavel"></i>
	                                <span class="title"><?=$frases[90][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item"><a href="reportes-crear.php" class="nav-link"> <span class="title"><?=$frases[96][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<li class="nav-item"><a href="reportes-lista.php" class="nav-link"> <span class="title"><?=$frases[97][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<li class="nav-item"><a href="reportes-sacar-filtro.php" class="nav-link"> <span class="title"><?=$frases[249][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<li class="nav-item"><a href="disciplina-categorias.php" class="nav-link"> <span class="title">Categorías</span></a></li>
									<li class="nav-item"><a href="disciplina-faltas.php" class="nav-link"> <span class="title">Faltas</span></a></li>
	                            </ul>
	                        </li>
							<?php }?>
							
							<?php 
							//MÓDULO ADMINISTRTIVO
							if($arregloModulos[4]==1){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-tachometer"></i>
	                                <span class="title"><?=$frases[87][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item"><a href="usuarios.php?cantidad=10" class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual[8]];?></span></a></li>

	                            </ul>
	                        </li>
							<?php }?>
							
							<?php 
							//MÓDULO MERCADEO
							if(isset($arregloModulos[6]) and $arregloModulos[6]==1){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-phone"></i>
	                                <span class="title"><?=$frases[210][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item"><a href="#" class="nav-link "> <span class="title"><?=$frases[75][$datosUsuarioActual[8]];?></span></a></li>

	                            </ul>
	                        </li>
							<?php }?>
							
							<li class="nav-item">
	                            <a href="informes-todos.php" class="nav-link nav-toggle"> <i class="fa fa-file-text"></i>
	                                <span class="title">Informes</span> 
	                            </a>
	                        </li>

							<li class="nav-item">
								<a href="#" class="nav-link nav-toggle"> <i class="fa fa-tasks"></i></i>
									<span class="title">Sacar Reportes</span> <span class="arrow"></span>
								</a>
								<ul class="sub-menu">
									<li><a href="reportes-academicos-consultas.php">Generales</a></li>
									<li><a href="informe-parcial-grupo.php">Informe parcial</a></li>
								</ul>
							</li>

							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-cogs"></i></i>
	                                <span class="title">Configuraci&oacute;n </span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
									<li><a href="configuracion-sistema.php">del Sistema</a></li>
									<li><a href="configuracion-institucion.php">de la Instituci&oacute;n</a></li>
									<li><a href="configuracion-opciones-generales.php">Opciones generales</a></li>
									<li><a href="configuracion-importar-informacion.php">Importar información</a></li>
	                            </ul>
	                        </li>

							<div class="nav-item">
							<?php include("../compartido/peso.php");?>
							</div>
												
							<!--
							<li class="nav-item active" data-step="11" data-intro="<b><?=$frases[175][$datosUsuarioActual[8]];?>:</b> Encuentra los mejores productos y servicios complementarios." data-position='left'>
	                            <a href="marketplace.php" class="nav-link nav-toggle"> <i class="fa fa-shopping-cart"></i>
	                                <span class="title">Marketplace</span> 
	                            </a>
	                        </li>
							
							
							<li class="nav-item">
	                            <a href="../../directivo/index.php" class="nav-link nav-toggle"> <i class="fa fa-undo"></i>
	                                <span class="title">VERSIÓN ANTERIOR</span> 
	                            </a>
	                        </li>
							-->
							
							<?php }?>
							
							<?php 
							//MENÚ DOCENTES
							if($datosUsuarioActual[3]==2){?>
							
							<li class="nav-item" data-step="13" data-intro="<b>Cargas académicas:</b> Aquí encontrarás las cargas académicas que los directivos te han asignado para trabajar. Debes seleccionar una carga primero, antes de empezar a llenar cualquier información como calificaciones, actividades, foros, etc." data-position='right' data-scrollTo='tooltip'>
	                            <a href="cargas.php" class="nav-link nav-toggle"> <i class="material-icons">class</i>
	                                <span class="title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							
													
							<!--
							<li class="nav-item">
	                            <a href="chat-grupal.php" class="nav-link nav-toggle"> <i class="fa fa-comments"></i>
	                                <span class="title">Sala de chat</span> 
	                            </a>
	                        </li>
							-->
							
							<?php if(($_COOKIE["carga"]!="" and $_COOKIE["periodo"]!="") or ($_GET["carga"]!="" and $_GET["periodo"]!="")){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="material-icons">assignment_ind</i>
	                                <span class="title"><?=$frases[88][$datosUsuarioActual['uss_idioma']]?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
									
									<?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
	                                <li class="nav-item"><a href="indicadores.php" class="nav-link "> <span class="title"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<?php }?>
									
									<?php if($datosCargaActual['car_observaciones_boletin']==1){?>
									<li class="nav-item"><a href="observaciones.php" class="nav-link "> <span class="title">Observaciones</span></a> </li>
									<?php }?>
									
									<li class="nav-item"><a href="calificaciones.php" class="nav-link "> <span class="title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<li class="nav-item"><a href="calificaciones-todas.php" class="nav-link "> <span class="title"><?=$frases[243][$datosUsuarioActual['uss_idioma']];?></span></a> </li>

									<li class="nav-item"><a href="notas-indicador.php" class="nav-link "> <span class="title"><?=$frases[252][$datosUsuarioActual['uss_idioma']];?></span></a> </li>
									
									<li class="nav-item"><a href="periodos-resumen.php" class="nav-link "> <span class="title"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></span></a> </li>
									

									<li class="nav-item"><a href="importar-info.php" class="nav-link "> <span class="title"><?=$frases[167][$datosUsuarioActual['uss_idioma']];?></span></a> </li>
									
									<li class="nav-item"><a href="evaluaciones.php" class="nav-link "> <span class="title"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<li class="nav-item"><a href="clases.php" class="nav-link "> <span class="title"><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<?php if($datosCargaActual['car_tematica']==1){?>
									<li class="nav-item"><a href="tematica.php" class="nav-link "> <span class="title">Temática</span></a> </li>
									<?php }?>
									
									<li class="nav-item"><a href="cronograma.php" class="nav-link "> <span class="title"><?=$frases[111][$datosUsuarioActual['uss_idioma']];?></span></a></li>

									<li class="nav-item"><a href="foros.php" class="nav-link "> <span class="title"><?=$frases[113][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<li class="nav-item"><a href="actividades.php" class="nav-link "> <span class="title"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></span></a></li>

	                            </ul>
	                        </li>
							<?php }?>
							
							
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-gavel"></i>
	                                <span class="title"><?=$frases[90][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">
	                                <li class="nav-item"><a href="reportes-crear.php" class="nav-link"> <span class="title"><?=$frases[96][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									<li class="nav-item"><a href="reportes-lista.php" class="nav-link"> <span class="title"><?=$frases[97][$datosUsuarioActual['uss_idioma']];?></span></a></li>
	                            </ul>
	                        </li>
							
							<?php if($datosCargaActual['car_director_grupo']==1){?>
							<li class="nav-item">
	                            <a href="comportamiento.php" class="nav-link nav-toggle"> <i class="fa fa-pencil-square-o"></i>
	                                <span class="title"><?=$frases[234][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							<li class="nav-item">
	                            <a href="aspectos.php" class="nav-link nav-toggle"> <i class="fa fa-pencil-square-o"></i>
	                                <span class="title">Aspectos</span> 
	                            </a>
	                        </li>
							<?php }?>
							
							
							<?php if($datosCargaActual['car_id']!=""){?>
							<li class="nav-item">
	                            <a href="estudiantes.php" class="nav-link nav-toggle"> <i class="fa fa-group"></i>
	                                <span class="title">Mis <?=$frases[55][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
	                        <?php }?>

	                        <li class="nav-item">
	                            <a href="estudiantes-todos.php" class="nav-link nav-toggle"> <i class="fa fa-group"></i>
	                                <span class="title">Todos los estudiantes</span> 
	                            </a>
	                        </li>
							
							<li class="nav-item">
	                            <a href="cargas-carpetas.php" class="nav-link nav-toggle"> <i class="fa fa-folder"></i>
	                                <span class="title"><?=$frases[216][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							
							<li class="nav-item active" data-step="11" data-intro="<b><?=$frases[175][$datosUsuarioActual[8]];?>:</b> Encuentra los mejores productos y servicios complementarios." data-position='left'>
	                            <a href="marketplace.php" class="nav-link nav-toggle"> <i class="fa fa-shopping-cart"></i>
	                                <span class="title">Marketplace</span> 
	                            </a>
	                        </li>
							
							
							<li class="nav-item">
	                            <a href="https://www.youtube.com/playlist?list=PL119_PkDEyLohcyXRnqHd36SqvLeKb5hF" target="_blank" class="nav-link nav-toggle"> <i class="fa fa-youtube"></i>
	                                <span class="title">TUTORIALES DE AYUDA</span> 
	                            </a>
	                        </li>
							
							<?php if($datosUsuarioActual['uss_version1_menu']==1){?>
							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="fa fa-undo"></i>
	                                <span class="title">VERSIÓN ANTERIOR</span> 
	                            </a>
	                        </li>
							<?php }?>
							
							
							
							
							<?php }?>
							
							<?php 
							//MENÚ ACUDIENTES
							if($datosUsuarioActual[3]==3){?>
							
							<li class="nav-item" data-step="10" data-intro="<b><?=$frases[71][$datosUsuarioActual[8]];?>:</b> Aquí verás tus acudidos y toda su información." data-position='left'>
	                            <a href="estudiantes.php" class="nav-link nav-toggle"> <i class="fa fa-group"></i>
	                                <span class="title"><?=$frases[71][$datosUsuarioActual[8]];?></span> 
	                            </a>
	                        </li>
							
							<li class="nav-item active" data-step="11" data-intro="<b><?=$frases[175][$datosUsuarioActual[8]];?>:</b> Encuentra los mejores productos y servicios complementarios." data-position='left'>
	                            <a href="marketplace.php" class="nav-link nav-toggle"> <i class="fa fa-shopping-cart fa-spin"></i>
	                                <span class="title">Marketplace</span> 
	                            </a>
	                        </li>
							
							<li class="nav-item" data-step="12" data-intro="<b><?=$frases[104][$datosUsuarioActual[8]];?>:</b> Aquí verás toda la información relacionada con tu estado de cuenta financiero." data-position='left'>
	                            <a href="estado-de-cuenta.php" class="nav-link nav-toggle"> <i class="material-icons">attach_money</i>
	                                <span class="title"><?=$frases[104][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							
							<?php }?>
							
							<?php 
							//MENÚ ESTUDIANTES
							if($datosUsuarioActual[3]==4){?>

	                        
							
							<li class="nav-item">
	                            <a href="cargas.php" class="nav-link nav-toggle"> <i class="material-icons">class</i>
	                                <span class="title"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>


							<li class="nav-item">
	                            <a href="#" class="nav-link nav-toggle"> <i class="material-icons">assignment_ind</i>
	                                <span class="title"><?=$frases[88][$datosUsuarioActual['uss_idioma']];?></span> <span class="arrow"></span>
	                            </a>
	                            <ul class="sub-menu">

	                            	<?php 
	                            	//Temporal para que el estudiante no vea notas ni nada de eso.
	                            	if($config['conf_servidor']==1){}else{?>

	                                <li class="nav-item"><a href="indicadores.php" class="nav-link "> <span class="title"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></span></a></li>

									
									<li class="nav-item"><a href="calificaciones.php" class="nav-link "> <span class="title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<?php if($config['conf_sin_nota_numerica']==1){}else{?>
									
									<li class="nav-item"><a href="periodos-resumen.php" class="nav-link "> <span class="title"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></span></a> </li>
									
									<?php }?>

									<?php }?>
									
									<li class="nav-item"><a href="ausencias.php" class="nav-link "> <span class="title"><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<li class="nav-item"><a href="cronograma-calendario.php" class="nav-link "> <span class="title"><?=$frases[111][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<li class="nav-item"><a href="actividades.php" class="nav-link "> <span class="title"><?=$frases[112][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<li class="nav-item"><a href="foros.php" class="nav-link "> <span class="title"><?=$frases[113][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
									<li class="nav-item"><a href="evaluaciones.php" class="nav-link "> <span class="title"><?=$frases[114][$datosUsuarioActual['uss_idioma']];?></span></a></li>
									
	                            </ul>
	                        </li>
							
							<li class="nav-item active" data-step="11" data-intro="<b><?=$frases[175][$datosUsuarioActual[8]];?>:</b> Encuentra los mejores productos y servicios complementarios." data-position='left'>
	                            <a href="marketplace.php" class="nav-link nav-toggle"> <i class="fa fa-shopping-cart fa-spin"></i>
	                                <span class="title">Marketplace</span> 
	                            </a>
	                        </li>
							
							<li class="nav-item">
	                            <a href="cargas-carpetas.php" class="nav-link nav-toggle"> <i class="fa fa-folder"></i>
	                                <span class="title"><?=$frases[216][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>

							
							<li class="nav-item">
	                            <a href="matricula.php" class="nav-link nav-toggle"> <i class="fa fa-pencil-square-o"></i>
	                                <span class="title"><?=$frases[60][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							
							<li class="nav-item">
	                            <a href="estado-de-cuenta.php" class="nav-link nav-toggle"> <i class="material-icons">attach_money</i>
	                                <span class="title"><?=$frases[104][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>
							
							<li class="nav-item">
	                            <a href="reportes-disciplinarios.php" class="nav-link nav-toggle"> <i class="material-icons">backspace</i>
	                                <span class="title"><?=$frases[105][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>

							<?php if($config['conf_ver_observador']==1){?>

							<li class="nav-item">
	                            <a href="aspectos.php" class="nav-link nav-toggle"> <i class="material-icons">backspace</i>
	                                <span class="title">Aspectos</span> 
	                            </a>
	                        </li>

							<?php }?>
							
							<li class="nav-item">
	                            <a href="estudiantes.php" class="nav-link nav-toggle"> <i class="material-icons">group</i>
	                                <span class="title"><?=$frases[74][$datosUsuarioActual['uss_idioma']];?></span> 
	                            </a>
	                        </li>

							
							
							<?php }?>

	                    </ul>
	                </div>
                </div>
            </div>
            <!-- end sidebar menu --> 