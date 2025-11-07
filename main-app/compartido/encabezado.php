<?php
if(isset($_GET["idNotify"]) and is_numeric($_GET["idNotify"])){
	mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_alertas SET alr_vista=1 WHERE alr_id='".$_GET["idNotify"]."' AND alr_vista=0");
	$lineaError = __LINE__;
	include_once(ROOT_PATH."/main-app/compartido/reporte-errores.php");
}
$institucionConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones 
WHERE ins_id='".$_SESSION["idInstitucion"]."' AND ins_enviroment='".ENVIROMENT."'");

$institucion = mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH);
$institucionNombre = $institucion['ins_siglas'];

// Contar mensajes sin leer para badge
$mensajesNoLeidosConsulta = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".$baseDatosServicios.".social_emails 
WHERE ema_para='".$_SESSION["id"]."' AND ema_visto=0 AND ema_institucion={$_SESSION["idInstitucion"]} AND ema_year={$_SESSION["bd"]}");
$mensajesNoLeidos = 0;
if ($mensajesNoLeidosConsulta) {
    $resultado = mysqli_fetch_array($mensajesNoLeidosConsulta, MYSQLI_BOTH);
    $mensajesNoLeidos = (int)$resultado['total'];
}
?>


<!-- start header -->
        <div class="page-header navbar navbar-fixed-top">
			
            <div class="page-header-inner">
                <!-- logo start -->
                <div class="page-logo">
                    <a href="index.php">
                    <img src="../../sintia-color.png" alt="SINTIA" class="logo-default" style="height: 40px; width: auto; max-width: 100%; object-fit: contain;"> </a>
                </div>
                <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_PREFERENCIAS)) {?>
                    <!-- logo end -->
                    <ul class="nav navbar-nav navbar-left in" data-toggle="tooltip" data-placement="top" title="Expande y contrae el menú principal según tu preferencia.">
                        <li><a href="#" class="menu-toggler sidebar-toggler"><i class="icon-menu"></i></a></li>
                    </ul>
                <?php }?>
				
				<?php //include("mega-menu.php");?>
				<?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_AYUDA_AVANZADA)) {?>
                    <form class="search-form-opened" action="paginas-buscador.php" method="GET" name="busqueda" id="buscador-general-container">
                        <div class="input-group">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="buscador-general-input"
                                placeholder="<?=$frases[260][$datosUsuarioActual['uss_idioma']];?>..." 
                                value="<?php if(isset($_GET["query"])){ echo $_GET["query"];}?>" 
                                name="query"
                                autocomplete="off"
                            >
                            <span class="input-group-btn">
                                <a href="javascript:;" onclick="document.forms.busqueda.submit()" class="btn submit">
                                    <i class="icon-magnifier"></i>
                                </a>
                            </span>
                        </div>
                        <!-- Contenedor de resultados en tiempo real -->
                        <div id="buscador-resultados"></div>
                    </form>
                <?php }?>
				
                <!-- start mobile menu -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                    <span></span>
                </a>
               <!-- end mobile menu -->
                <!-- start header menu -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
						
						
                    	<!--<li><a href="javascript:;" class="fullscreen-btn"><i class="fa fa-arrows-alt"></i></a></li>-->

                        <?php
                            if (
                                $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || 
                                $datosUsuarioActual['uss_tipo'] == TIPO_DEV && 
                                Modulos::verificarModulosDeInstitucion(Modulos::MODULO_SEDES)
                            ) {
                                $sites    = Instituciones::getSites();
                                $numSites = mysqli_num_rows($sites);

                                if (
                                    $numSites > 0 && 
                                    Modulos::validarSubRol(['DT0339']) && 
                                    !empty($datosUsuarioActual["uss_documento"])
                                ) {
                        ?>
                                    <li class="dropdown dropdown-user">
                                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                        <i class="fa fa-home" data-toggle="tooltip" data-placement="top" title="Cambia de sede para consultar su información."></i>
                                        <span class="username username-hide-on-mobile"> SEDE ACTUAL: <b><?=$institucionNombre;?></b> </span>
                                            <?php echo '<i class="fa fa-angle-down"></i>'; ?>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-default">
                                            <?php
                                            require_once(ROOT_PATH."/main-app/class/Usuarios/Directivo.php");

                                            while ($site = mysqli_fetch_array($sites, MYSQLI_BOTH)) {
                                                try {
                                                    $mySelf = Directivo::getMyselfByDocument(
                                                        $datosUsuarioActual["uss_documento"], 
                                                        $datosUsuarioActual["uss_tipo"], 
                                                        $site['ins_id']
                                                    );
                                                } catch (Exception $e) {
                                                    continue;
                                                }
                                            ?>
                                                <li><a href="cambiar-sede.php?idInstitucion=<?=base64_encode($site['ins_id']);?>"><?=$site['ins_siglas'];?></a></li>
                                            <?php }?>
                                        </ul>
                                    </li>
                        <?php 
                                }
                        ?>

                            <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_GENERAL)) {?>
                                <li class="dropdown dropdown-user">
                                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <i class="fa fa-calendar-o" data-toggle="tooltip" data-placement="top" title="Consulta la información histórica de los años que anteriores."></i>
                                    <span class="username username-hide-on-mobile"> AÑO ACTUAL: <b><?=$_SESSION["bd"];?></b> </span>
                                        <?php if(Modulos::validarSubRol(['DT0030'])) { echo '<i class="fa fa-angle-down"></i>'; } ?>
                                    </a>
                                    <?php if(Modulos::validarSubRol(['DT0030'])) { ?>
                                        <ul class="dropdown-menu dropdown-menu-default">
                                            <?php
                                            while($yearStart <= $yearEnd){	
                                                if($_SESSION["bd"] == $yearStart) {
                                            ?>
                                                    <li class="active"><a href="javascript:;" style="font-weight:bold;"><?=$yearStart;?></a></li>
                                            <?php
                                                } else {
                                            ?>
                                                    <li><a href="cambiar-bd.php?agno=<?=base64_encode($yearStart);?>"><?=$yearStart;?></a></li>
                                            <?php
                                                }
                                                $yearStart++;
                                            }
                                            $yearStart = $yearArray[0];
                                            ?>
                                        </ul>
                                    <?php }?>
                                </li>
                            <?php }?>

                            <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CONFIGURACION)) {?>
                                <li class="dropdown dropdown-user">
                                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <i class="fa fa-calendar-o" data-toggle="tooltip" data-placement="top" title="Establece el periodo que necesites para consultas. Lo ideal es que sea el periodo en el cual están actualmente."></i>
                                    <span class="username username-hide-on-mobile"> PERIODO ACTUAL: <b><?=$config['conf_periodo'];?></b> </span>
                                        <?php if(Modulos::validarSubRol(['DT0053'])) { echo '<i class="fa fa-angle-down"></i>'; } ?>
                                    </a>
                                    <?php if(Modulos::validarSubRol(['DT0053'])) { ?>
                                        <ul class="dropdown-menu dropdown-menu-default">
                                            <?php
                                            $p = 1;
                                            $pFinal = $config[19] + 1;
                                            while($p <= $pFinal){
                                                $label = 'Periodo '.$p;
                                                if($p == $pFinal) {
                                                    $label = 'AÑO FINALIZADO';
                                                }

                                                if($p==$config['conf_periodo']) {
                                            ?>
                                                <li class="active"><a href="javascript:;" style="font-weight:bold;"><?=$label;?></a></li>
                                            <?php
                                            } else {
                                            ?>
                                                <li><a href="cambiar-periodo.php?periodo=<?=base64_encode($p);?>"><?=$label;?></a></li>
                                            <?php
                                            }
                                                $p++;
                                            }
                                            ?>
                                        </ul>
                                    <?php }?>
                                </li>
                            <?php }?>
                        <?php } else { ?>
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-calendar-o"></i>
                                <span class="username username-hide-on-mobile"> AÑO ACTUAL: <b><?=$_SESSION["bd"];?></b> </span>
                                </a>
                            </li>
                        <?php }?>
						
						<!-- start dark mode toggle -->
						<li class="dropdown" style="padding: 15px 10px;">
							<label class="theme-switch-wrapper" data-toggle="tooltip" data-placement="bottom" title="Cambia entre modo claro y oscuro">
								<input type="checkbox" class="theme-switch" id="themeToggle">
								<span class="theme-slider">
									<i class="fa fa-sun theme-icon-light"></i>
									<i class="fa fa-moon theme-icon-dark"></i>
								</span>
							</label>
						</li>
						<!-- end dark mode toggle -->
						
                    	<!-- start language menu -->
                        <li class="dropdown language-switch" data-scrollTo='tooltip'>
							<?php
							switch($datosUsuarioActual['uss_idioma']){
								case 1:
									$idiomaImg = 'es.png';
									$idiomaNombre = 'Español';
								break;
									
								case 2:
									$idiomaImg = 'gb.png';
									$idiomaNombre = 'English';
								break;
							}
							?>
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 
                                <img src="<?=BASE_URL;?>/config-general/assets/img/flags/<?=$idiomaImg;?>" class="position-left" alt="idiomas" data-toggle="tooltip" data-placement="top" title="Puedes cambiar el idioma en que ves la información de la plataforma"> <?=$idiomaNombre;?> 
                                <span class="fa fa-angle-down"></span>
                            </a>
							
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?=BASE_URL;?>/main-app/compartido/cambiar-idioma-tema.php?get=1&idioma=2" class="english"><img src="<?=BASE_URL;?>/config-general/assets/img/flags/gb.png" alt=""> <?=$frases[261][$datosUsuarioActual['uss_idioma']];?></a>
                                </li>
                                <li>
                                    <a href="<?=BASE_URL;?>/main-app/compartido/cambiar-idioma-tema.php?get=1&idioma=1" class="espana"><img src="<?=BASE_URL;?>/config-general/assets/img/flags/es.png" alt=""> <?=$frases[262][$datosUsuarioActual['uss_idioma']];?></a>
                                </li>
                            </ul>
                        </li>
                        <!-- end language menu -->

                        <!-- start apps dropdown -->
                        <li class="dropdown dropdown-extended dropdown-apps" style="position: relative;">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" style="position: relative;">
                                <i class="fa fa-th" data-toggle="tooltip" data-placement="top" title="Aplicaciones de Sintia"></i>
                                <span id="badge_apps_mensajes" class="notification-dot" style="position: absolute; top: 5px; right: 5px; width: 8px; height: 8px; border-radius: 50%; background: #e74c3c; display: <?php echo ($mensajesNoLeidos > 0) ? 'block' : 'none'; ?>; box-shadow: 0 0 0 0 rgba(231, 76, 60, 1);"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-default apps-grid" style="width: 400px; padding: 20px; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); background: #fff; border: 1px solid #e0e0e0;">
                                <!-- Header del selector -->
                                <div class="apps-header mb-3">
                                    <h6 class="mb-0 text-center" style="color: #333; font-weight: 500;">Aplicaciones de Sintia</h6>
                                </div>
                                
                                <!-- Apps visibles (3x3) -->
                                <div class="apps-grid-container" id="apps-visible">
                                    <!-- Publicaciones - Primera opción -->
                                    <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_PUBLICACIONES)) {?>
                                    <div class="app-item" onclick="navigateToApp('publicaciones')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);">
                                            <i class="fa fa-bullhorn"></i>
                                        </div>
                                        <span class="app-name"><?=$frases[69][$datosUsuarioActual['uss_idioma']];?></span>
                                    </div>
                                    <?php }?>
                                    
                                    <!-- Mensajes - Segunda opción -->
                                    <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CORREO_INTERNO)) {?>
                                    <div class="app-item app-item-mensajes" onclick="navigateToApp('mensajes')" style="position: relative;">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="fa fa-envelope-o"></i>
                                        </div>
                                        <span class="app-name">Mensajes</span>
                                        <span id="mensajes_numero_app" class="app-badge" <?php echo ($mensajesNoLeidos > 0) ? 'style="display: flex;"' : 'style="display: none;"'; ?>>
                                            <?php echo ($mensajesNoLeidos > 0) ? $mensajesNoLeidos : ''; ?>
                                        </span>
                                    </div>
                                    <?php }?>
                                    
                                    <!-- Carpetas - Tercera opción -->
                                    <?php if (false && Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CARPETAS)) {?>
                                    <div class="app-item" onclick="navigateToApp('carpetas')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                            <i class="fa fa-folder"></i>
                                        </div>
                                        <span class="app-name"><?=$frases[216][$datosUsuarioActual['uss_idioma']];?></span>
                                    </div>
                                    <?php }?>
                                    
                                    <!-- Marketplace - Cuarta opción -->
                                    <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_MARKETPLACE)) {?>
                                    <div class="app-item" onclick="navigateToApp('marketplace')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                                            <i class="fa fa-shopping-cart"></i>
                                        </div>
                                        <span class="app-name">Marketplace</span>
                                    </div>
                                    <?php }?>
                                    
                                    <!-- Fila 1 -->
                                    <!-- <div class="app-item" onclick="navigateToApp('directivo')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="fa fa-cogs"></i>
                                        </div>
                                        <span class="app-name">Directivo</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('docente')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                            <i class="fa fa-chalkboard-teacher"></i>
                                        </div>
                                        <span class="app-name">Docente</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('estudiante')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                        <span class="app-name">Estudiante</span>
                                    </div> -->
                                    
                                    <!-- Fila 2 -->
                                    <!-- <div class="app-item" onclick="navigateToApp('acudiente')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                            <i class="fa fa-users"></i>
                                        </div>
                                        <span class="app-name">Acudiente</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('admisiones')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                                            <i class="fa fa-clipboard-list"></i>
                                        </div>
                                        <span class="app-name">Admisiones</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('reportes')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
                                            <i class="fa fa-chart-bar"></i>
                                        </div>
                                        <span class="app-name">Reportes</span>
                                    </div> -->
                                    
                                    <!-- Fila 3 -->
                                    <!-- <div class="app-item" onclick="navigateToApp('calendario')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                            <i class="fa fa-calendar-alt"></i>
                                        </div>
                                        <span class="app-name">Calendario</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('biblioteca')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
                                            <i class="fa fa-book"></i>
                                        </div>
                                        <span class="app-name">Biblioteca</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="toggleMoreApps(event)" id="more-apps-btn">
                                        <div class="app-icon" style="background: #f5f5f5; border: 2px dashed #ccc;">
                                            <i class="fa fa-plus" style="color: #666;"></i>
                                        </div>
                                        <span class="app-name">Más</span>
                                    </div> -->
                                </div>
                                
                                <!-- Apps ocultas -->
                                <div class="apps-grid-container" id="apps-hidden" style="display: none;">
                                    <!-- Fila 4 -->
                                    <div class="app-item" onclick="navigateToApp('financiero')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);">
                                            <i class="fa fa-dollar-sign"></i>
                                        </div>
                                        <span class="app-name">Financiero</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('inventario')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);">
                                            <i class="fa fa-boxes"></i>
                                        </div>
                                        <span class="app-name">Inventario</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('comunicaciones')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);">
                                            <i class="fa fa-comments"></i>
                                        </div>
                                        <span class="app-name">Comunicaciones</span>
                                    </div>
                                    
                                    <!-- Fila 5 -->
                                    <div class="app-item" onclick="navigateToApp('recursos')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                            <i class="fa fa-folder-open"></i>
                                        </div>
                                        <span class="app-name">Recursos</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="navigateToApp('configuracion')">
                                        <div class="app-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                            <i class="fa fa-cog"></i>
                                        </div>
                                        <span class="app-name">Configuración</span>
                                    </div>
                                    
                                    <div class="app-item" onclick="toggleMoreApps(event)" id="less-apps-btn">
                                        <div class="app-icon" style="background: #f5f5f5; border: 2px dashed #ccc;">
                                            <i class="fa fa-minus" style="color: #666;"></i>
                                        </div>
                                        <span class="app-name">Menos</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- end apps dropdown -->

      <!-- start notification dropdown -->
                        <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                            <!--<span id="notificaciones"></span>-->
                        </li>
                        <!-- end notification dropdown -->
				
                        <!-- start message dropdown -->
                        <?php
                            if(
                                $numAsignacionesEncuesta > 0  && 
                                (
                                    $idPaginaInterna != 'DC0146' && 
                                    $idPaginaInterna != 'AC0038' && 
                                    $idPaginaInterna != 'ES0062' && 
                                    $idPaginaInterna != 'DT0324' && 
                                    $idPaginaInterna != 'CM0060'
                                )
                            ) {
                        ?>
                        <li class="dropdown dropdown-extended dropdown-inbox">
                            <a href="encuestas-pendientes.php" class="dropdown-toggle">
                                <i class="fa fa-info"></i>
                                <span class="badge headerBadgeColor2" style="right: -12px; top: 5px;"><?=$numAsignacionesEncuesta?></span>
                            </a>
                        </li>
                        <?php } ?>

                        <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CORREO_INTERNO)) {?>
                            <!-- Mensajes ahora está en Aplicaciones de Sintia -->
                            <span id="mensajes_numero" style="display: none;"></span>
                            <span id="mensajes" style="display: none;"></span>
                            <script>
                                // 🛡️ PROTECCIÓN: Solo ejecutar si socket está disponible (WebSocket habilitado)
                                if (typeof socket !== 'undefined') {
                                    socket.on("recibio_correo_<?=$_SESSION['id']?>_<?=$_SESSION['idInstitucion']?>",async (data) => {
                                        mensajes();
                                        $.toast({
                                            heading: data['asunto'],  
                                            text: 'Tienes un mensaje nuevo del usuario '+data['nombreEmisor']+', Revisalo en Aplicaciones de Sintia.',
                                            position: 'bottom-right',
                                            showHideTransition: 'slide',
                                            loaderBg:'#ff6849',
                                            icon: 'info',
                                            hideAfter: 10000, 
                                            stack: 6
                                        })
                                    });

                                    socket.on("recibio_correo_modulos_dev_<?=$datosUsuarioActual['uss_tipo']?>_<?=$_SESSION['idInstitucion']?>",async (data) => {
                                        mensajes();
                                        $.toast({
                                            heading: data['asunto'],  
                                            text: 'Tienes un mensaje nuevo, Revisalo en Aplicaciones de Sintia.',
                                            position: 'bottom-right',
                                            showHideTransition: 'slide',
                                            loaderBg:'#ff6849',
                                            icon: 'info',
                                            hideAfter: 10000, 
                                            stack: 6
                                        })
                                    });
                                }
                            </script>
                        <?php }?>
                        <!-- end message dropdown -->
 						<!-- start manage user dropdown -->
                        <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_MI_CUENTA)) {?>
                            <li class="dropdown dropdown-user" data-step="500" data-intro="<b>Cuenta personal:</b> Aquí puedes acceder a tu perfil a cambiar tus datos personales, y en la opción salir podrás cerrar tu sesión con seguirdad cuando hayas terminado de trabajar con la plataforma." data-position='bottom' data-scrollTo='tooltip'>
                                <?php 
                                // 🛡️ PROTECCIÓN: Validar foto de perfil para evitar rutas incorrectas
                                $fotoUsuario = !empty($datosUsuarioActual['uss_foto']) && file_exists("../files/fotos/".$datosUsuarioActual['uss_foto']) 
                                    ? $datosUsuarioActual['uss_foto'] 
                                    : 'default.png';
                                ?>
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <img alt="" class="img-circle " src="<?=BASE_URL;?>/main-app/files/fotos/<?=$fotoUsuario;?>"/>
                                    <span class="username username-hide-on-mobile" data-toggle="tooltip" data-placement="top" title="Editar tu perfil, cambia tu clave y más..."> <?=UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual);?> </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
                                    <li><a href="perfil.php"><i class="icon-user"></i><?=$frases[256][$datosUsuarioActual['uss_idioma']];?></a></li>

                                    <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CREDENCIALES)) {?>
                                        <?php if($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE && $config['conf_cambiar_clave_estudiantes'] == 'NO') { }else{?>
                                            <li><a href="cambiar-clave.php"><i class="icon-lock"></i><?=$frases[253][$datosUsuarioActual['uss_idioma']];?></a></li>
                                        <?php }?>
                                    <?php }?>
                                    
                                    <li class="divider"> </li>
                                    
                                    <?php if(Modulos::validarSubRol(["DT0202"])){?>
                                        <li><a href="<?=BASE_URL;?>/main-app/directivo/solicitud-cancelacion.php"><i class="fa fa-cut"></i><?=$frases[367][$datosUsuarioActual['uss_idioma']];?></a></li>
                                    <?php }?>
                                    <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DEV || ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && Modulos::validarSubRol(["DT0332"]))){?>
                                        <li><a href="consumo-plan.php"><i class="fa fa-pie-chart"></i>Consumo Del Plan</a></li>
                                    <?php }?>
                                    <li><a href="<?=BASE_URL;?>/main-app/compartido/sintia-refresh.php" onClick="localStorage.clear();"><i class="fa fa-refresh"></i>Refrescar SINTIA</a></li>
                                    <li><a href="<?=BASE_URL;?>/main-app/controlador/salir.php?logout=true" onClick="localStorage.clear();"><i class="icon-logout"></i><?=$frases[15][$datosUsuarioActual['uss_idioma']];?></a></li>
                                </ul>
                            </li>
                        <?php }?>
						
						<?php
                        /*
						$arrayEnviarE = array("idUsuario"=>$_SESSION["id"], "nombreUsuario"=>$datosUsuariosActual['uss_nombre'], "fotoUsuario"=>$datosUsuariosActual["uss_foto"]);
						$arrayDatosE = json_encode($arrayEnviarE);
						$objetoEnviarE = htmlentities($arrayDatosE);
                        */
						?>
						
                        <!-- end manage user dropdown --
                        <li class="dropdown dropdown-quick-sidebar-toggler">
                             <a id="headerSettingButton" class="mdl-button mdl-js-button mdl-button--icon pull-right" data-upgraded=",MaterialButton">
	                           <i class="fa fa-weixin" id="" onclick="conectarme(this)"></i>
	                        </a>
                        </li>-->
                    </ul>

					
                </div>

            </div>
        </div>
        <!-- end header -->
        
        <!-- CSS para el selector de aplicaciones estilo Google -->
        <style>
        /* Animación de latido para el notification dot */
        @keyframes pulse-dot {
            0% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(231, 76, 60, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
            }
        }
        
        .notification-dot {
            animation: pulse-dot 2s infinite;
        }
        
        .apps-grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .app-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .app-item {
            position: relative;
        }
        
        .app-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            padding: 0 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 10;
        }
        
        .app-badge:empty {
            display: none;
        }
        
        .app-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .app-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        
        .app-item:hover .app-icon {
            transform: scale(1.1);
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        
        .app-icon i {
            font-size: 20px;
            color: white;
        }
        
        .app-name {
            font-size: 11px;
            font-weight: 500;
            color: #333;
            text-align: center;
            line-height: 1.2;
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .apps-header {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 12px;
        }
        
        /* Animación suave para mostrar/ocultar */
        .apps-grid-container {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* ========================================
           RESPONSIVE - HEADER OPTIMIZADO PARA MÓVILES
           ======================================== */
        
        /* Tablets y móviles grandes */
        @media (max-width: 1024px) {
            /* Ocultar textos largos en header */
            .username-hide-on-mobile {
                display: none !important;
            }
            
            /* Buscador más compacto */
            #buscador-general-container {
                max-width: 350px;
            }
            
            #buscador-general-input {
                font-size: 14px;
            }
        }
        
        /* Móviles */
        @media (max-width: 768px) {
            /* Header con altura flexible */
            .page-header .page-header-inner {
                padding: 8px 10px;
            }
            
            /* Logo más pequeño */
            .page-logo {
                width: auto !important;
                padding: 5px 10px !important;
            }
            
            .page-logo img {
                max-height: 35px !important;
            }
            
            /* Buscador más pequeño */
            #buscador-general-container {
                max-width: 200px;
                margin-right: 5px;
            }
            
            #buscador-general-input {
                font-size: 13px;
                padding: 6px 10px;
                height: 35px;
            }
            
            .search-form-opened .input-icon > i {
                top: 8px;
                font-size: 14px;
            }
            
            /* Top menu más compacto */
            .top-menu .navbar-nav > li > a {
                padding: 8px 6px !important;
                font-size: 18px;
            }
            
            .top-menu .dropdown-toggle {
                padding: 6px 8px !important;
            }
            
            /* Ocultar selectores de año/periodo en móvil */
            .top-menu .navbar-nav > li.dropdown.dropdown-extended.dropdown-light:nth-child(1),
            .top-menu .navbar-nav > li.dropdown.dropdown-extended.dropdown-light:nth-child(2) {
                display: none !important;
            }
            
            /* Toggle oscuro más pequeño */
            .dark-mode-toggle {
                transform: scale(0.85);
            }
            
            /* Dropdown de usuario */
            .top-menu .dropdown-user > a {
                padding: 4px 8px !important;
            }
            
            .top-menu .dropdown-user img {
                width: 32px !important;
                height: 32px !important;
            }
        }
        
        /* Móviles muy pequeños */
        @media (max-width: 480px) {
            /* Header ultra compacto */
            .page-header .page-header-inner {
                padding: 5px 8px;
            }
            
            /* Logo aún más pequeño */
            .page-logo {
                padding: 3px 8px !important;
            }
            
            .page-logo img {
                max-height: 28px !important;
            }
            
            /* Buscador ultra compacto */
            #buscador-general-container {
                max-width: 140px;
            }
            
            #buscador-general-input {
                font-size: 12px;
                padding: 4px 8px;
                height: 30px;
            }
            
            .search-form-opened .input-icon > i {
                display: none; /* Ocultar ícono de búsqueda */
            }
            
            /* Iconos más pequeños */
            .top-menu .navbar-nav > li > a {
                padding: 6px 4px !important;
                font-size: 16px;
            }
            
            /* Ocultar selector de idioma en pantallas muy pequeñas */
            .top-menu .navbar-nav > li.dropdown.dropdown-language {
                display: none !important;
            }
            
            /* Foto de perfil más pequeña */
            .top-menu .dropdown-user img {
                width: 28px !important;
                height: 28px !important;
            }
            
            /* Menú hamburguesa más visible */
            .menu-toggler.responsive-toggler {
                padding: 8px 10px;
                font-size: 20px;
            }
        }
        
        /* Ajustes para modo landscape en móviles */
        @media (max-width: 768px) and (orientation: landscape) {
            .page-header .page-header-inner {
                padding: 4px 8px;
            }
            
            #buscador-general-container {
                max-width: 180px;
            }
            
            .page-logo img {
                max-height: 30px !important;
            }
        }
        </style>
        
        <!-- JavaScript para el selector de aplicaciones -->
        <script>
        function toggleMoreApps(event) {
            // Prevenir que se cierre el dropdown
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const hiddenApps = document.getElementById('apps-hidden');
            const moreBtn = document.getElementById('more-apps-btn');
            const lessBtn = document.getElementById('less-apps-btn');
            
            if (hiddenApps.style.display === 'none' || hiddenApps.style.display === '') {
                hiddenApps.style.display = 'grid';
                moreBtn.style.display = 'none';
                lessBtn.style.display = 'flex';
            } else {
                hiddenApps.style.display = 'none';
                moreBtn.style.display = 'flex';
                lessBtn.style.display = 'none';
            }
            
            return false;
        }
        
        function navigateToApp(appName) {
            // Mapeo de aplicaciones a URLs
            <?php
            // Determinar la ruta de publicaciones según el tipo de usuario
            $urlPublicaciones = '';
            if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
                $urlPublicaciones = BASE_URL.'/main-app/directivo/noticias.php';
            } elseif($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) {
                $urlPublicaciones = BASE_URL.'/main-app/docente/noticias.php';
            } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE) {
                $urlPublicaciones = BASE_URL.'/main-app/estudiante/noticias.php';
            } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE) {
                $urlPublicaciones = BASE_URL.'/main-app/acudiente/noticias.php';
            } else {
                $urlPublicaciones = BASE_URL.'/main-app/directivo/noticias.php'; // Por defecto
            }
            ?>
            const appUrls = {
                'publicaciones': '<?=$urlPublicaciones;?>',
                'mensajes': '<?php
                    // Determinar la ruta de mensajes según el tipo de usuario
                    $urlMensajes = '';
                    if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
                        $urlMensajes = BASE_URL.'/main-app/directivo/mensajes.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) {
                        $urlMensajes = BASE_URL.'/main-app/docente/mensajes.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE) {
                        $urlMensajes = BASE_URL.'/main-app/estudiante/mensajes.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE) {
                        $urlMensajes = BASE_URL.'/main-app/acudiente/mensajes.php';
                    } else {
                        $urlMensajes = BASE_URL.'/main-app/directivo/mensajes.php'; // Por defecto
                    }
                    echo $urlMensajes;
                ?>',
                'carpetas': '<?php
                    // Determinar la ruta de carpetas según el tipo de usuario
                    $urlCarpetas = '';
                    if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
                        $urlCarpetas = BASE_URL.'/main-app/directivo/cargas-carpetas.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) {
                        $urlCarpetas = BASE_URL.'/main-app/docente/cargas-carpetas.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE) {
                        $urlCarpetas = BASE_URL.'/main-app/estudiante/cargas-carpetas.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE) {
                        $urlCarpetas = BASE_URL.'/main-app/acudiente/cargas-carpetas.php';
                    } else {
                        $urlCarpetas = BASE_URL.'/main-app/directivo/cargas-carpetas.php'; // Por defecto
                    }
                    echo $urlCarpetas;
                ?>',
                'marketplace': '<?php
                    // Determinar la ruta de marketplace según el tipo de usuario
                    $urlMarketplace = '';
                    if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
                        $urlMarketplace = BASE_URL.'/main-app/directivo/marketplace.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) {
                        $urlMarketplace = BASE_URL.'/main-app/docente/marketplace.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE) {
                        $urlMarketplace = BASE_URL.'/main-app/estudiante/marketplace.php';
                    } elseif($datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE) {
                        $urlMarketplace = BASE_URL.'/main-app/acudiente/marketplace.php';
                    } else {
                        $urlMarketplace = BASE_URL.'/main-app/directivo/marketplace.php'; // Por defecto
                    }
                    echo $urlMarketplace;
                ?>',
                'directivo': '<?=BASE_URL;?>/main-app/directivo/',
                'docente': '<?=BASE_URL;?>/main-app/docente/',
                'estudiante': '<?=BASE_URL;?>/main-app/estudiante/',
                'acudiente': '<?=BASE_URL;?>/main-app/acudiente/',
                'admisiones': '<?=BASE_URL;?>/main-app/admisiones/',
                'reportes': '<?=BASE_URL;?>/main-app/reportes/',
                'calendario': '<?=BASE_URL;?>/main-app/calendario/',
                'biblioteca': '<?=BASE_URL;?>/main-app/biblioteca/',
                'financiero': '<?=BASE_URL;?>/main-app/financiero/',
                'inventario': '<?=BASE_URL;?>/main-app/inventario/',
                'comunicaciones': '<?=BASE_URL;?>/main-app/comunicaciones/',
                'recursos': '<?=BASE_URL;?>/main-app/recursos/',
                'configuracion': '<?=BASE_URL;?>/main-app/configuracion/'
            };
            
            const url = appUrls[appName];
            if (url) {
                window.location.href = url;
            } else {
                console.log('Aplicación no encontrada:', appName);
            }
        }
        
        // Inicializar el estado del botón "Menos"
        document.addEventListener('DOMContentLoaded', function() {
            const lessBtn = document.getElementById('less-apps-btn');
            if (lessBtn) {
                lessBtn.style.display = 'none';
            }
        });
        </script>
        
        <!-- CSS y JavaScript para Dark Mode -->
        <style>
        /* Switch de tema con animación */
        .theme-switch-wrapper {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }
        
        .theme-switch {
            display: none;
        }
        
        .theme-slider {
            position: relative;
            width: 60px;
            height: 30px;
            background: linear-gradient(145deg, #667eea 0%, #764ba2 100%);
            border-radius: 50px;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            padding: 0 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .theme-slider::before {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            transition: transform 0.4s ease;
            transform: translateX(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .theme-switch:checked + .theme-slider {
            background: linear-gradient(145deg, #2c3e50 0%, #34495e 100%);
        }
        
        .theme-switch:checked + .theme-slider::before {
            transform: translateX(30px);
        }
        
        .theme-icon-light,
        .theme-icon-dark {
            position: absolute;
            font-size: 14px;
            transition: all 0.3s ease;
            z-index: 1;
        }
        
        .theme-icon-light {
            left: 8px;
            color: #fff;
            opacity: 1;
        }
        
        .theme-icon-dark {
            right: 8px;
            color: #fff;
            opacity: 0.5;
        }
        
        .theme-switch:checked ~ .theme-icon-light {
            opacity: 0.5;
        }
        
        .theme-switch:checked ~ .theme-icon-dark {
            opacity: 1;
        }
        
        /* Estilos del modo oscuro */
        body.dark-mode {
            background-color: #1a1a2e !important;
            color: #eaeaea !important;
        }
        
        /* Header/Encabezado */
        body.dark-mode .page-header,
        body.dark-mode .page-header.navbar,
        body.dark-mode .page-header-inner {
            background: #16213e !important;
            background-color: #16213e !important;
            border-bottom: 1px solid #0f3460 !important;
        }
        
        body.dark-mode .page-header .top-menu .nav > li > a,
        body.dark-mode .page-header .nav > li > a {
            color: #eaeaea !important;
        }
        
        body.dark-mode .page-header .top-menu .nav > li > a:hover {
            background-color: #0f3460 !important;
        }
        
        body.dark-mode .page-header .username {
            color: #eaeaea !important;
        }
        
        body.dark-mode .page-logo {
            background-color: #16213e !important;
        }
        
        /* Sidebar/Menú - Sobrescribir white-sidebar-color */
        body.dark-mode .page-sidebar,
        body.dark-mode .page-sidebar-wrapper,
        body.dark-mode .page-sidebar .sidebar-wrapper,
        body.dark-mode .sidebar-wrapper,
        body.dark-mode .page-sidebar-inner,
        body.dark-mode .page-sidebar .page-sidebar-menu,
        body.dark-mode .sidebar-content,
        body.dark-mode .sidemenu-container,
        body.dark-mode.white-sidebar-color .sidemenu-container,
        body.dark-mode .white-sidebar-color .sidemenu-container {
            background: #16213e !important;
            background-color: #16213e !important;
        }
        
        body.dark-mode .page-sidebar *:not(.sidebar-menu):not(li):not(a),
        body.dark-mode .sidebar-wrapper *:not(.sidebar-menu):not(li):not(a) {
            background-color: transparent !important;
        }
        
        /* Forzar el fondo del sidebar con especificidad máxima */
        body.dark-mode div.page-sidebar,
        body.dark-mode aside.page-sidebar,
        body.dark-mode .page-sidebar[style] {
            background: #16213e !important;
            background-color: #16213e !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu,
        body.dark-mode .sidebar-menu,
        body.dark-mode .page-sidebar-menu,
        body.dark-mode .page-sidebar-menu-inner {
            background: #16213e !important;
            background-color: #16213e !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu > li > a,
        body.dark-mode .sidebar-menu > li > a {
            color: #eaeaea !important;
            background: transparent !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu > li:hover > a,
        body.dark-mode .sidebar-menu > li:hover > a {
            background: #0f3460 !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu > li.active > a,
        body.dark-mode .sidebar-menu > li.active > a {
            background: #0f3460 !important;
            border-left: 3px solid #667eea !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu > li > a > i,
        body.dark-mode .sidebar-menu > li > a > i {
            color: #eaeaea !important;
        }
        
        /* Submenús */
        body.dark-mode .page-sidebar .sidebar-menu .sub-menu,
        body.dark-mode .sidebar-menu .sub-menu,
        body.dark-mode .sidemenu .sub-menu,
        body.dark-mode.white-sidebar-color .sidemenu-container .sidemenu .sub-menu,
        body.dark-mode .white-sidebar-color .sidemenu-container .sidemenu .sub-menu,
        body.dark-mode.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu,
        body.dark-mode .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu {
            background: #0f3460 !important;
            background-color: #0f3460 !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu .sub-menu > li > a,
        body.dark-mode .sidebar-menu .sub-menu > li > a,
        body.dark-mode .sidemenu .sub-menu > li > a {
            color: #d0d0d0 !important;
            background: transparent !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu .sub-menu > li:hover > a,
        body.dark-mode .sidebar-menu .sub-menu > li:hover > a,
        body.dark-mode .sidemenu .sub-menu > li:hover > a {
            background: #533483 !important;
        }
        
        body.dark-mode .page-sidebar .sidebar-menu .sub-menu > li.active > a,
        body.dark-mode .sidebar-menu .sub-menu > li.active > a,
        body.dark-mode .sidemenu .sub-menu > li.active > a {
            background: #533483 !important;
            color: #eaeaea !important;
        }
        
        body.dark-mode .page-content,
        body.dark-mode .page-content-wrapper,
        body.dark-mode .page-container {
            background-color: #1a1a2e !important;
        }
        
        body.dark-mode .card,
        body.dark-mode .panel,
        body.dark-mode .card-box,
        body.dark-mode .card-body,
        body.dark-mode .panel-body,
        body.dark-mode .portlet,
        body.dark-mode .portlet-body {
            background-color: #16213e !important;
            color: #eaeaea !important;
            border-color: #0f3460 !important;
        }
        
        body.dark-mode .page-bar {
            background-color: transparent !important;
        }
        
        body.dark-mode .panel-heading,
        body.dark-mode .portlet-title {
            background-color: #0f3460 !important;
            color: #eaeaea !important;
            border-color: #0f3460 !important;
        }
        
        body.dark-mode .panel-heading-yellow,
        body.dark-mode .panel-heading-purple,
        body.dark-mode .panel-heading-red,
        body.dark-mode .panel-heading-blue,
        body.dark-mode .panel-heading-green {
            color: #eaeaea !important;
        }
        
        body.dark-mode .list-group-item {
            background-color: #16213e !important;
            color: #eaeaea !important;
            border-color: #0f3460 !important;
        }
        
        body.dark-mode .profile-desc-item {
            color: #eaeaea !important;
        }
        
        body.dark-mode .table {
            color: #eaeaea;
            background-color: #16213e;
        }
        
        body.dark-mode .table thead th {
            background-color: #0f3460 !important;
            color: #eaeaea;
            border-color: #0f3460;
        }
        
        body.dark-mode .table tbody tr {
            background-color: #16213e !important;
            border-color: #0f3460;
        }
        
        body.dark-mode .table tbody tr:hover {
            background-color: #0f3460 !important;
        }
        
        body.dark-mode .table td,
        body.dark-mode .table th {
            border-color: #0f3460;
        }
        
        body.dark-mode .form-control,
        body.dark-mode .form-control:focus {
            background-color: #0f3460;
            color: #eaeaea;
            border-color: #533483;
        }
        
        body.dark-mode .btn,
        body.dark-mode .btn-default {
            background-color: #0f3460 !important;
            color: #eaeaea !important;
            border-color: #533483 !important;
        }
        
        body.dark-mode .btn:hover,
        body.dark-mode .btn-default:hover {
            background-color: #533483 !important;
        }
        
        body.dark-mode .btn-primary {
            background-color: #667eea !important;
            border-color: #667eea !important;
        }
        
        body.dark-mode .btn-success {
            background-color: #27ae60 !important;
            border-color: #27ae60 !important;
        }
        
        body.dark-mode .btn-info {
            background-color: #3498db !important;
            border-color: #3498db !important;
        }
        
        body.dark-mode .btn-warning {
            background-color: #f39c12 !important;
            border-color: #f39c12 !important;
        }
        
        body.dark-mode .btn-danger {
            background-color: #e74c3c !important;
            border-color: #e74c3c !important;
        }
        
        /* Textos generales */
        body.dark-mode p,
        body.dark-mode span,
        body.dark-mode div,
        body.dark-mode label,
        body.dark-mode a {
            color: inherit;
        }
        
        body.dark-mode a:not(.btn) {
            color: #667eea !important;
        }
        
        body.dark-mode a:not(.btn):hover {
            color: #764ba2 !important;
        }
        
        body.dark-mode .dropdown-menu {
            background-color: #16213e !important;
            border-color: #0f3460 !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4) !important;
        }
        
        body.dark-mode .dropdown-menu > li > a {
            color: #eaeaea !important;
        }
        
        body.dark-mode .dropdown-menu > li > a:hover {
            background-color: #0f3460 !important;
        }
        
        /* Apps dropdown */
        body.dark-mode .apps-grid .dropdown-menu {
            background: #16213e !important;
            border-color: #0f3460 !important;
        }
        
        body.dark-mode .apps-header h6 {
            color: #eaeaea !important;
        }
        
        body.dark-mode .app-item:hover {
            background-color: #0f3460 !important;
        }
        
        body.dark-mode .app-name {
            color: #eaeaea !important;
        }
        
        body.dark-mode .modal-content {
            background-color: #16213e;
            color: #eaeaea;
        }
        
        body.dark-mode .modal-header {
            background-color: #0f3460;
            border-bottom-color: #533483;
        }
        
        body.dark-mode .modal-footer {
            background-color: #0f3460;
            border-top-color: #533483;
        }
        
        body.dark-mode h1, 
        body.dark-mode h2, 
        body.dark-mode h3, 
        body.dark-mode h4, 
        body.dark-mode h5, 
        body.dark-mode h6 {
            color: #eaeaea;
        }
        
        body.dark-mode .page-title {
            color: #eaeaea !important;
        }
        
        body.dark-mode .breadcrumb {
            background-color: #16213e;
        }
        
        body.dark-mode .breadcrumb a {
            color: #667eea;
        }
        
        body.dark-mode .alert {
            background-color: #0f3460;
            border-color: #533483;
            color: #eaeaea;
        }
        
        body.dark-mode .nav-tabs {
            border-bottom-color: #0f3460;
        }
        
        body.dark-mode .nav-tabs > li > a {
            color: #eaeaea;
        }
        
        body.dark-mode .nav-tabs > li.active > a {
            background-color: #16213e !important;
            border-color: #0f3460;
            color: #667eea;
        }
        
        /* Cards modernos */
        body.dark-mode .grades-card-modern,
        body.dark-mode .indicadores-card-modern {
            background-color: #16213e !important;
            border-color: #0f3460 !important;
        }
        
        body.dark-mode .grades-card-header,
        body.dark-mode .indicadores-card-header {
            background-color: #0f3460 !important;
            color: #eaeaea !important;
        }
        
        body.dark-mode .table-modern thead th {
            background-color: #0f3460 !important;
            color: #eaeaea !important;
        }
        
        body.dark-mode .table-modern tbody tr {
            background-color: #16213e !important;
        }
        
        body.dark-mode .table-modern tbody tr:hover {
            background-color: #0f3460 !important;
        }
        
        /* Badges y labels */
        body.dark-mode .badge-indicador {
            background-color: #533483 !important;
            color: #eaeaea !important;
        }
        
        body.dark-mode .label {
            background-color: #0f3460 !important;
        }
        
        /* Inputs de búsqueda */
        body.dark-mode .search-form-opened .form-control {
            background-color: #0f3460 !important;
            color: #eaeaea !important;
        }
        
        /* Footer */
        body.dark-mode .page-footer {
            background-color: #16213e !important;
            color: #eaeaea;
            border-top: 1px solid #0f3460;
        }
        
        /* Scrollbar personalizado para dark mode */
        body.dark-mode ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        body.dark-mode ::-webkit-scrollbar-track {
            background: #16213e;
        }
        
        body.dark-mode ::-webkit-scrollbar-thumb {
            background: #533483;
            border-radius: 5px;
        }
        
        body.dark-mode ::-webkit-scrollbar-thumb:hover {
            background: #667eea;
        }
        
        /* Transición suave */
        body,
        .page-header,
        .page-sidebar,
        .page-content,
        .card,
        .panel,
        .table,
        .form-control,
        .btn,
        .dropdown-menu,
        .modal-content {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        </style>
        
        <script>
        // Dark Mode Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            
            // Función para forzar estilos del sidebar
            function forceSidebarStyles(enable) {
                const sidebar = document.querySelector('.page-sidebar');
                const sidemenuContainer = document.querySelector('.sidemenu-container');
                const subMenus = document.querySelectorAll('.sub-menu');
                
                if (sidebar) {
                    if (enable) {
                        sidebar.style.setProperty('background', '#16213e', 'important');
                        sidebar.style.setProperty('background-color', '#16213e', 'important');
                    } else {
                        sidebar.style.removeProperty('background');
                        sidebar.style.removeProperty('background-color');
                    }
                }
                
                if (sidemenuContainer) {
                    if (enable) {
                        sidemenuContainer.style.setProperty('background', '#16213e', 'important');
                        sidemenuContainer.style.setProperty('background-color', '#16213e', 'important');
                    } else {
                        sidemenuContainer.style.removeProperty('background');
                        sidemenuContainer.style.removeProperty('background-color');
                    }
                }
                
                // Forzar estilos en submenús
                subMenus.forEach(function(subMenu) {
                    if (enable) {
                        subMenu.style.setProperty('background', '#0f3460', 'important');
                        subMenu.style.setProperty('background-color', '#0f3460', 'important');
                    } else {
                        subMenu.style.removeProperty('background');
                        subMenu.style.removeProperty('background-color');
                    }
                });
            }
            
            // Verificar si hay una preferencia guardada
            const darkModeEnabled = localStorage.getItem('darkMode') === 'enabled';
            
            // Aplicar el tema guardado
            if (darkModeEnabled) {
                document.body.classList.add('dark-mode');
                themeToggle.checked = true;
                
                // Aplicar estilos inmediatamente y después de un pequeño delay
                forceSidebarStyles(true);
                setTimeout(() => forceSidebarStyles(true), 100);
                setTimeout(() => forceSidebarStyles(true), 500);
            }
            
            // Listener para el cambio de tema
            themeToggle.addEventListener('change', function() {
                if (this.checked) {
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('darkMode', 'enabled');
                    forceSidebarStyles(true);
                    setTimeout(() => forceSidebarStyles(true), 100);
                } else {
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('darkMode', 'disabled');
                    forceSidebarStyles(false);
                }
            });
            
            // Observer para mantener el estilo si el sidebar se recarga o aparecen submenús
            if (darkModeEnabled) {
                const observer = new MutationObserver(function(mutations) {
                    if (document.body.classList.contains('dark-mode')) {
                        forceSidebarStyles(true);
                    }
                });
                
                // Observar cambios en el sidebar
                const sidebarContainer = document.querySelector('.page-sidebar') || document.querySelector('.sidemenu-container');
                if (sidebarContainer) {
                    observer.observe(sidebarContainer, {
                        attributes: true,
                        attributeFilter: ['style', 'class'],
                        childList: true,
                        subtree: true
                    });
                }
            }
            
            // Observer adicional para submenús que se despliegan
            document.addEventListener('click', function(e) {
                if (document.body.classList.contains('dark-mode')) {
                    const menuItem = e.target.closest('.sidemenu > li');
                    if (menuItem) {
                        setTimeout(() => forceSidebarStyles(true), 50);
                    }
                }
            });
        });
        </script>