<?php
$infoIdPagina = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_id='".$idPaginaInterna."'"), MYSQLI_BOTH);
?>

<div id="noInternet" style="margin-bottom: 10px; margin-top: 10px; padding: 5px; background-color: red; color: white; width: 100%; display: none;">
	Se ha perdido tu conexión a internet. Por favor verifica antes de continuar trabajando en la plataforma.
</div>

<div id="siInternet" style="margin-bottom: 10px; margin-top: 10px; padding: 5px; background-color: green; color: white; width: 100%; display: none;">
	La conexión a internet ha vuelto. Puedes continuar trabajando en la plataforma.
</div>

<span style="display: none;"><b># <?=$frases[237][$datosUsuarioActual[8]];?>:</b> <?=$idPaginaInterna;?></span>

<p style="color: royalblue;" data-step="7" data-intro="<b>IMPORTANTE - Tutorial de ayuda:</b> En cada pantalla de la plataforma podrás encontrar este anuncio para que veas un corto video tutorial de ayuda sobre las opciones de la pantalla en la cual te encuentras. O también puedes usar la vista guiada inteligente de la plataforma." data-position='bottom'>
	
	<i class="fa fa-info-circle"></i> <b><?=$frases[238][$datosUsuarioActual[8]];?>:</b> <?=$frases[239][$datosUsuarioActual[8]];?> <a href="<?=$infoIdPagina['pagp_url_youtube'];?>" style="text-decoration: underline; font-weight: bold;" target="_blank"><?=$frases[240][$datosUsuarioActual[8]];?></a>.<br>
	
	<i class="fa fa-life-ring"></i> <a href="javascript:void(0);" onclick="javascript:introJs().addHints();" style="text-decoration: underline; color: teal;"><b>TAMBIÉN PUEDES USAR LA GUÍA INTELIGENTE</b></a>

</p>

<?php if($datosUsuarioActual['uss_bloqueado']==1){?>
	<div style="margin-bottom: 10px; margin-top: 10px; padding: 5px; background-color: coral;">
	<b><?=$datosUsuarioActual['uss_nombre'];?></b>,	su usuario se encuentra bloqueado para algunas opciones de la plataforma. Si tiene dudas al respecto, pongase en contacto con la Institución.
	</div>
<?php }?>


<?php
$numOP = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
WHERE pubxub_ubicacion=4 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'
"));
if($numOP>0){
	$numOP --;
}
$empezar = rand(0,$numOP);

$publicidadTop = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
WHERE pubxub_ubicacion=4 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'
LIMIT ".$empezar.",1
"), MYSQLI_BOTH);
?>
<?php if(isset($publicidadTop['pubxub_id']) AND $publicidadTop['pubxub_id']!=""){
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".publicidad_estadisticas(pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, pest_fecha, pest_ip, pest_accion)
	VALUES('".$publicidadTop['pub_id']."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', '".$idPaginaInterna."', 4, now(), '".$_SERVER["REMOTE_ADDR"]."', 1)");
	
?>
	<div align="center">
		<?php if($publicidadTop['pub_titulo']!=""){?><h4 align="center"><?=$publicidadTop['pub_titulo'];?></h4><?php }?>
		
		<?php if($publicidadTop['pub_descripcion']!=""){?><p><?=$publicidadTop['pub_descripcion'];?></p><?php }?>
		
		<?php if($publicidadTop['pub_imagen']!=""){?><div class="item"><a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadTop['pub_id'];?>&idUb=4&url=<?=$publicidadTop['pub_url'];?>" target="_blank"><img class="img-re" src="http://plataformasintia.com/files-general/pub/<?=$publicidadTop['pub_imagen'];?>"></a></div><p>&nbsp;</p><?php }?>
		
		<?php if($publicidadTop['pub_video']!=""){?>
		<p><iframe width="450" height="315" src="https://www.youtube.com/embed/<?=$publicidadTop['pub_video'];?>?rel=0&amp;mute=<?=$publicidadTop['pub_mute'];?>&start=<?=$publicidadTop['pub_start'];?>&end=<?=$publicidadTop['pub_end'];?>&autoplay=<?=$publicidadTop['pub_autoplay'];?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe></p><?php }?>
	</div>
<?php }?>
