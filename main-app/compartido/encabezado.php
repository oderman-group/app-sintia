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
                                    <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_CARPETAS)) {?>
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
                                    <div class="app-item" onclick="navigateToApp('directivo')">
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
                                    </div>
                                    
                                    <!-- Fila 2 -->
                                    <div class="app-item" onclick="navigateToApp('acudiente')">
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
                                    </div>
                                    
                                    <!-- Fila 3 -->
                                    <div class="app-item" onclick="navigateToApp('calendario')">
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
                                    </div>
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
                            </script>
                        <?php }?>
                        <!-- end message dropdown -->
 						<!-- start manage user dropdown -->
                        <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_MI_CUENTA)) {?>
                            <li class="dropdown dropdown-user" data-step="500" data-intro="<b>Cuenta personal:</b> Aquí puedes acceder a tu perfil a cambiar tus datos personales, y en la opción salir podrás cerrar tu sesión con seguirdad cuando hayas terminado de trabajar con la plataforma." data-position='bottom' data-scrollTo='tooltip'>
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <img alt="" class="img-circle " src="<?=BASE_URL;?>/main-app/files/fotos/<?=$datosUsuarioActual['uss_foto'];?>"/>
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
                                    <li><a href="<?=BASE_URL;?>/main-app/controlador/salir.php" onClick="localStorage.clear();"><i class="icon-logout"></i><?=$frases[15][$datosUsuarioActual['uss_idioma']];?></a></li>
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