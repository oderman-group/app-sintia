<?php
$numOPMF = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad
INNER JOIN ".$baseDatosServicios.".general_categorias ON gcat_id=pub_categoria_especifica
WHERE pub_estado=1 AND pub_tipo=2 AND pub_tipo_usuario='".$datosUsuarioActual['uss_tipo']."'
"));
if($numOP>0){
	$numOP --;
}
$empezarMF = rand(0,$numOPMF);

$publicidadLateralMF = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad
INNER JOIN ".$baseDatosServicios.".general_categorias ON gcat_id=pub_categoria_especifica
WHERE pub_estado=1 AND pub_tipo=2 AND pub_tipo_usuario='".$datosUsuarioActual['uss_tipo']."'
LIMIT ".$empezarMF.",1
"), MYSQLI_BOTH);
?>
<?php if(isset($publicidadLateralMF['pub_id']) AND $publicidadLateralMF['pub_id']!=""){
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".publicidad_estadisticas(pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, pest_fecha, pest_ip, pest_accion)
	VALUES('".$publicidadLateralMF['pub_id']."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', '".$idPaginaInterna."', 1, now(), '".$_SERVER["REMOTE_ADDR"]."', 1)");
	
	
	$guardadaNum = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_guardadas
	WHERE psave_publicidad='".$publicidadLateralMF['pub_id']."' AND psave_institucion='".$config['conf_id_institucion']."' AND psave_usuario='".$_SESSION["id"]."'"));
?>
<div class="panel">
	<header class="panel-heading panel-heading-yellow"><?=strtoupper($publicidadLateralMF['gcat_nombre']);?></header>
	<div class="panel-body">
		<?php if($publicidadLateralMF['pub_titulo']!=""){?><h4><?=$publicidadLateralMF['pub_titulo'];?></h4><?php }?>
		
		<?php if($publicidadLateralMF['pub_descripcion']!=""){?><p><?=$publicidadLateralMF['pub_descripcion'];?></p><?php }?>
		
		<?php if($publicidadLateralMF['pub_imagen']!=""){?><div class="item"><a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadLateralMF['pub_id'];?>&idUb=1&url=<?=$publicidadLateralMF['pub_url'];?>" target="_blank"><img class="img-re" src="http://plataformasintia.com/files-general/frases/<?=$publicidadLateralMF['pub_imagen'];?>"></a></div><p>&nbsp;</p><?php }?>
		
		<?php if($publicidadLateralMF['pub_video']!=""){?><p>
		<iframe width="450" height="315" src="https://www.youtube.com/embed/<?=$publicidadLateralMF['pub_video'];?>?rel=0&amp;mute=<?=$publicidadLateralMF['pub_mute'];?>&start=<?=$publicidadLateralMF['pub_start'];?>&end=<?=$publicidadLateralMF['pub_end'];?>&autoplay=<?=$publicidadLateralMF['pub_autoplay'];?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe></p><?php }?>
		
		<script type="application/javascript">
			function guardarRec(){
				document.getElementById("guardarRecurso").innerHTML = "<i class='fa fa-check'></i> Recurso guardado";
			}
		</script>
		
		<?php if($guardadaNum==0){?>
			<p id="guardarRecurso"><a href="../compartido/guardar.php?get=15&idPub=<?=$publicidadLateralMF['pub_id'];?>" target="_blank" onClick="guardarRec()"><i class="fa fa-save"></i> Guardar recurso</a></p>
		<?php }else{?>
			<p><i class="fa fa-check"></i> Recurso guardado</p>
		<?php }?>
		
	</div>
</div>
<?php }?>