<?php
$numOP = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
WHERE pubxub_ubicacion=2 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'
"));
if($numOP>0){
	$numOP --;
}
$empezar = rand(0,$numOP);

$publicidadFooter = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
WHERE pubxub_ubicacion=2 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'
LIMIT ".$empezar.",1
"), MYSQLI_BOTH);
?>

<?php if(isset($publicidadFooter['pubxub_id']) AND $publicidadFooter['pubxub_id']!=""){
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".publicidad_estadisticas(pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, pest_fecha, pest_ip, pest_accion)
	VALUES('".$publicidadFooter['pub_id']."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', '".$idPaginaInterna."', 2, now(), '".$_SERVER["REMOTE_ADDR"]."', 1)");
	
?>
	<div align="center" style="background-color: beige; padding: 10px;">
		<?php if($publicidadFooter['pub_titulo']!=""){?><h4><?=$publicidadFooter['pub_titulo'];?></h4><?php }?>
		<?php if($publicidadFooter['pub_descripcion']!=""){?><p><?=$publicidadFooter['pub_descripcion'];?></p><?php }?>
		<?php if($publicidadFooter['pub_imagen']!=""){?>
			<div class="item"><a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadFooter['pub_id'];?>&idUb=2&url=<?=$publicidadFooter['pub_url'];?>" target="_blank"><img src="http://plataformasintia.com/files-general/pub/<?=$publicidadFooter['pub_imagen'];?>" width="470"></a></div>
			<p>&nbsp;</p>
		<?php }?>
	</div>
<?php }?>

<!-- start footer -->
<div class="page-footer">

    <div class="page-footer-inner"> 2018 &copy; Plataforma SINTIA By
    	<a href="#" target="_top" class="makerCss">ODERMAN</a>
    </div>
	
    <div class="scroll-to-top">
    	<i class="icon-arrow-up"></i>
    </div>
</div>
<!-- end footer -->