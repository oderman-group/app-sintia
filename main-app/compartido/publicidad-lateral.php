<!-- ESTADO DE CUENTAS -->
<?php
if($saldoEC>0){
?>
	<div class="panel">
		<header class="panel-heading panel-heading-yellow">SALDO PENDIENTE</header>
		<div class="panel-body">
			<p style="color: red;">Tienes un saldo pendiente con la Institución.</p>
			<span style="color: red;"><b >Saldo:</b> $<?=number_format($saldoEC,0,",",".");?></span>
			
			<p>
				Te recomendamos ponerte al día con esta cuenta.<br>
				Si tienes difcultades para pagar, puedes contactar con la Institución para que puedan llegar a un acuerdo de pago.
			</p>
			
			<p>
				<b><?=$informacion_inst['info_nombre'];?></b><br>
				<b>Teléfono:</b> <?=$informacion_inst['info_telefono'];?><br>
				<b>Dirección:</b> <?=$informacion_inst['info_direccion'];?><br>
			</p>
		</div>
	</div>	
<?php
}
?>



<!-- PUBLICIDAD -->
<?php
$numOP = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
WHERE pubxub_ubicacion=1 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'
"));
if($numOP>0){
	$numOP --;
}
$empezar = rand(0,$numOP);

$publicidadLateral = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
WHERE pubxub_ubicacion=1 AND pubxub_id_institucion='".$config['conf_id_institucion']."' AND pubxub_id_pagina='".$idPaginaInterna."'
LIMIT ".$empezar.",1
"), MYSQLI_BOTH);
?>
<?php if(isset($publicidadLateral['pubxub_id']) AND $publicidadLateral['pubxub_id']!=""){
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".publicidad_estadisticas(pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, pest_fecha, pest_ip, pest_accion)
	VALUES('".$publicidadLateral['pub_id']."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', '".$idPaginaInterna."', 1, now(), '".$_SERVER["REMOTE_ADDR"]."', 1)");
	
?>
<div class="panel" style="position: sticky; top:0;">
	<!--<header class="panel-heading panel-heading-green"><?=$frases[133][$datosUsuarioActual['uss_idioma']];?> </header>-->
	<div class="panel-body">
		<?php if($publicidadLateral['pub_titulo']!=""){?>
			<h4><?=$publicidadLateral['pub_titulo'];?></h4>
		<?php }?>
		
		<?php if($publicidadLateral['pub_descripcion']!=""){?>
			<p><?=$publicidadLateral['pub_descripcion'];?></p>
		<?php }?>
		
		
		<?php if($publicidadLateral['pub_imagen']!=""){?>
			<div class="item">
				<a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadLateral['pub_id'];?>&idUb=1&url=<?=$publicidadLateral['pub_url'];?>" target="_blank">
					<img src="../../files-general/pub/<?=$publicidadLateral['pub_imagen'];?>">
				</a>
			</div>
			<p>&nbsp;</p>
		<?php }?>
		
		
		<?php if($publicidadLateral['pub_video']!=""){?>
			<p>
				<iframe width="450" height="315" src="https://www.youtube.com/embed/<?=$publicidadLateral['pub_video'];?>?rel=0&amp;mute=<?=$publicidadLateral['pub_mute'];?>&start=<?=$publicidadLateral['pub_start'];?>&end=<?=$publicidadLateral['pub_end'];?>&autoplay=<?=$publicidadLateral['pub_autoplay'];?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe>
			</p>
		<?php }?>
		
		
		<?php if($publicidadLateral['pub_boton_accion']!=""){?>
			<p align="center">			
				<a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadLateral['pub_id'];?>&idUb=3&url=<?=$publicidadLateral['pub_url'];?>" class="btn btn-success" target="_blank"><?=$publicidadLateral['pub_boton_accion'];?></a>
			</p>	
		<?php }?>
	</div>
</div>
<?php }?>































