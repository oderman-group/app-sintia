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
?>


<!-- start header -->
        <div class="page-header navbar navbar-fixed-top">
			
            <div class="page-header-inner">
                <!-- logo start -->
                <div class="page-logo">
                    <a href="index.php">
                    <img src="../../sintia-gris.png" alt="SINTIA" class="logo-default" style="height: 40px; width: auto; max-width: 100%; object-fit: contain;"> </a>
                </div>
                <?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_PREFERENCIAS)) {?>
                    <!-- logo end -->
                    <ul class="nav navbar-nav navbar-left in" data-toggle="tooltip" data-placement="top" title="Expande y contrae el menú principal según tu preferencia.">
                        <li><a href="#" class="menu-toggler sidebar-toggler"><i class="icon-menu"></i></a></li>
                    </ul>
                <?php }?>
				
				<?php //include("mega-menu.php");?>
				<?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_AYUDA_AVANZADA)) {?>
                    <form class="search-form-opened" action="paginas-buscador.php" method="GET" name="busqueda">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="<?=$frases[260][$datosUsuarioActual['uss_idioma']];?>..." value="<?php if(isset($_GET["query"])){ echo $_GET["query"];}?>" name="query">
                            <span class="input-group-btn">
                            <span class="input-group-btn">
                            <a href="javascript:;" onclick="document.forms.busqueda.submit()" class="btn submit">
                                <i class="icon-magnifier"></i>
                            </a>
                            </span>
                                                    
                        </div>
                        
                        
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
                        <li class="dropdown dropdown-extended dropdown-apps">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-th" data-toggle="tooltip" data-placement="top" title="Aplicaciones de Sintia"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-default apps-grid" style="width: 320px; padding: 15px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background: #fff;">
                                <div class="row" id="apps-visible">
                                    <div class="col-4 text-center mb-4">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-cogs fa-3x text-primary mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Directivo">Directivo</span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center mb-4">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-chalkboard-teacher fa-3x text-success mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Docente">Docente</span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center mb-4">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-graduation-cap fa-3x text-info mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Estudiante">Estudiante</span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center mb-4">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-users fa-3x text-warning mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Acudiente">Acudiente</span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center mb-4">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-clipboard-list fa-3x text-danger mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Admisiones">Admisiones</span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center mb-4" id="more-apps-btn">
                                        <a href="javascript:void(0);" onclick="toggleMoreApps(event)" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-plus fa-3x text-secondary mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Más">Más</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row" id="apps-hidden" style="display: none; flex-wrap: nowrap;">
                                    <div class="col-4 text-center mb-4" style="flex: 0 0 33.333%; max-width: 33.333%;">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-chart-bar fa-3x text-primary mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Reportes">Reportes</span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center mb-4" style="flex: 0 0 33.333%; max-width: 33.333%;">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-calendar fa-3x text-success mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Calendario">Calendario</span>
                                        </a>
                                    </div>
                                    <div class="col-4 text-center mb-4" style="flex: 0 0 33.333%; max-width: 33.333%;">
                                        <a href="#" class="app-link d-block p-3 rounded" style="transition: all 0.3s ease; background: #f8f9fa; border: 1px solid #e9ecef;" onmouseover="this.style.background='#e9ecef'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#f8f9fa'; this.style.transform='scale(1)';">
                                            <i class="fa fa-book fa-3x text-info mb-2"></i><br>
                                            <span class="app-name font-weight-bold text-dark" style="font-size: 10px; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="Biblioteca">Biblioteca</span>
                                        </a>
                                    </div>
                                    <div class="col-12 text-center mt-2">
                                        <a href="javascript:void(0);" onclick="toggleMoreApps(event)" class="btn btn-sm btn-outline-secondary">Mostrar menos</a>
                                    </div>
                                </div>
                                <script>
                                    function toggleMoreApps(event) {
                                        event.stopPropagation();
                                        var hiddenApps = document.getElementById('apps-hidden');
                                        var moreBtn = document.getElementById('more-apps-btn');
                                        if (hiddenApps.style.display === 'none') {
                                            hiddenApps.style.display = 'block';
                                            moreBtn.style.display = 'none';
                                        } else {
                                            hiddenApps.style.display = 'none';
                                            moreBtn.style.display = 'block';
                                        }
                                    }
                                </script>
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
                            <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <i class="fa fa-envelope-o" data-toggle="tooltip" data-placement="top" title="Correo interno: Recibe y envía mensajes."></i>
                                    <span id="mensajes_numero"></span>
                                </a>
                                <span id="mensajes"></span>
                                <script>
                                    socket.on("recibio_correo_<?=$_SESSION['id']?>_<?=$_SESSION['idInstitucion']?>",async (data) => {
                                        mensajes();
                                        $.toast({
                                            heading: data['asunto'],  
                                            text: 'Tienes un mensaje nuevo del usuario '+data['nombreEmisor']+', Revisalo en el icono del sobre que está en la parte superior.',
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
                                            text: 'Tienes un mensaje nuevo, Revisalo en el icono del sobre que está en la parte superior.',
                                            position: 'bottom-right',
                                            showHideTransition: 'slide',
                                            loaderBg:'#ff6849',
                                            icon: 'info',
                                            hideAfter: 10000, 
                                            stack: 6
                                        })
                                    });
                                </script>
                            </li>
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