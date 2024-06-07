<?php
if(isset($_GET["idNotify"]) and is_numeric($_GET["idNotify"])){
	mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_alertas SET alr_vista=1 WHERE alr_id='".$_GET["idNotify"]."' AND alr_vista=0");
	$lineaError = __LINE__;
	include("../compartido/reporte-errores.php");
}
$institucionConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones 
WHERE ins_id='".$_SESSION["idInstitucion"]."' AND ins_enviroment='".ENVIROMENT."'");

$institucion = mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH);
$institucionNombre = $institucion['ins_siglas'];
?>


<!-- start header -->
        <div class="page-header navbar navbar-fixed-top">

            <?php include("../compartido/barra-developer.php");?>
			
            <div class="page-header-inner">
                <!-- logo start -->
                <div class="page-logo">
                    <a href="index.php">
                    <span class="logo-default" style="font-weight: bold; font-size: 12px;"><?=$institucionNombre;?></span> </a>
                </div>
                <!-- logo end -->
				<ul class="nav navbar-nav navbar-left in">
					<li><a href="#" class="menu-toggler sidebar-toggler"><i class="icon-menu"></i></a></li>
				</ul>
				
				<?php //include("mega-menu.php");?>
				
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
				
                <!-- start mobile menu -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                    <span></span>
                </a>
               <!-- end mobile menu -->
                <!-- start header menu -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
						
						
                    	<li><a href="javascript:;" class="fullscreen-btn"><i class="fa fa-arrows-alt"></i></a></li>

                        <?php
                            if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
                        ?>
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-calendar-o"></i>    
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
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-calendar-o"></i>    
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
                        <?php } else { ?>
                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-calendar-o"></i>    
                                <span class="username username-hide-on-mobile"> AÑO ACTUAL: <b><?=$_SESSION["bd"];?></b> </span>
                                </a>
                            </li>
                        <?php }?>
						
                    	<!-- start language menu -->
                        <li class="dropdown language-switch" data-step="3" data-intro="<b>Idiomas:</b> Aquí puedes cambiar el idioma de la plataforma." data-position='bottom' data-scrollTo='tooltip'>
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
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <img src="../../config-general/assets/img/flags/<?=$idiomaImg;?>" class="position-left" alt=""> <?=$idiomaNombre;?> <span class="fa fa-angle-down"></span>
                            </a>
							
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="../compartido/cambiar-idioma-tema.php?get=1&idioma=2" class="english"><img src="../../config-general/assets/img/flags/gb.png" alt=""> <?=$frases[261][$datosUsuarioActual['uss_idioma']];?></a>
                                </li>
                                <li>
                                    <a href="../compartido/cambiar-idioma-tema.php?get=1&idioma=1" class="espana"><img src="../../config-general/assets/img/flags/es.png" alt=""> <?=$frases[262][$datosUsuarioActual['uss_idioma']];?></a>
                                </li>
                            </ul>
                        </li>
                        <!-- end language menu -->
                        
						<!-- start notification dropdown -->
                        <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar" data-step="4" data-intro="<b>Notificaciones:</b> Aquí recibirás notificaciones importantes relacionadas contigo." data-position='bottom' data-scrollTo='tooltip'>
                            <!--<span id="notificaciones"></span>-->
                        </li>
                        <!-- end notification dropdown -->
				
                        <!-- start message dropdown -->
                        <?php
                            if($numAsignacionesEncuesta > 0  && ($idPaginaInterna != 'DC0146' && $idPaginaInterna != 'AC0038' && $idPaginaInterna != 'ES0062' && $idPaginaInterna != 'DT0324' && $idPaginaInterna != 'CM0060')){
                        ?>
 						<li class="dropdown dropdown-extended dropdown-inbox" data-step="5" data-intro="<b>Encuestas:</b> Aquí se te redireccionara a las encuestas pendientes." data-position='bottom'>
                            <a href="encuestas-pendientes.php" class="dropdown-toggle">
                                <i class="fa fa-question"></i>
                                <span class="badge headerBadgeColor2" style="right: -12px; top: 5px;"><?=$numAsignacionesEncuesta?></span>
                            </a>
                        </li>
                        <?php } ?>
 						<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar" data-step="5" data-intro="<b>Mensajes:</b> Aquí verás los mensajes directos que te envíen otros usuarios de la plataforma." data-position='bottom' data-scrollTo='tooltip'>
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-envelope-o"></i>
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
                        <!-- end message dropdown -->
 						<!-- start manage user dropdown -->
 						<li class="dropdown dropdown-user" data-step="6" data-intro="<b>Cuenta personal:</b> Aquí puedes acceder a tu perfil a cambiar tus datos personales, y en la opción salir podrás cerrar tu sesión con seguirdad cuando hayas terminado de trabajar con la plataforma." data-position='bottom' data-scrollTo='tooltip'>
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <img alt="" class="img-circle " src="../files/fotos/<?=$datosUsuarioActual['uss_foto'];?>"/>
                                <span class="username username-hide-on-mobile"> <?=UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual);?> </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li><a href="perfil.php"><i class="icon-user"></i><?=$frases[256][$datosUsuarioActual['uss_idioma']];?></a></li>
                                <?php if($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE && $config['conf_cambiar_clave_estudiantes'] == 'NO') { }else{?>
                                    <li><a href="cambiar-clave.php"><i class="icon-lock"></i><?=$frases[253][$datosUsuarioActual['uss_idioma']];?></a></li>
                                <?php }?>
								
                                <li class="divider"> </li>
								<li><a href="https://forms.gle/1NpXSwyqoomKdch76" target="_blank"><i class="icon-question"></i><?=$frases[16][$datosUsuarioActual['uss_idioma']];?>/<?=$frases[257][$datosUsuarioActual['uss_idioma']];?></a></li>
                                <?php if(Modulos::validarSubRol(["DT0202"])){?>
                                    <li><a href="../directivo/solicitud-cancelacion.php"><i class="fa fa-cut"></i><?=$frases[367][$datosUsuarioActual['uss_idioma']];?></a></li>
                                <?php }?>
                                <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DEV || ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && Modulos::validarSubRol(["DT0332"]))){?>
                                    <li><a href="consumo-plan.php"><i class="fa-solid fa-chart-pie"></i>Consumo Del Plan</a></li>
                                <?php }?>
                                <li><a href="../compartido/sintia-refresh.php" onClick="localStorage.clear();"><i class="fa fa-refresh"></i>Refrescar SINTIA</a></li>
                                <li><a href="../controlador/salir.php" onClick="localStorage.clear();"><i class="icon-logout"></i><?=$frases[15][$datosUsuarioActual['uss_idioma']];?></a></li>
                            </ul>
                        </li>
						
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
	                           <i class="fa fa-weixin" id="<?=$objetoEnviarE;?>" onclick="conectarme(this)"></i>
	                        </a>
                        </li>-->
                    </ul>

					
                </div>

            </div>
        </div>
        <!-- end header -->