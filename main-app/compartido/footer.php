<?php
/*
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
			<div class="item"><a href="../compartido/guardar-click-publicitario.php?idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadFooter['pub_id'];?>&idUb=2&url=<?=$publicidadFooter['pub_url'];?>" target="_blank"><img src="http://plataformasintia.com/files-general/pub/<?=$publicidadFooter['pub_imagen'];?>" width="470"></a></div>
			<p>&nbsp;</p>
		<?php }?>
	</div>
<?php }*/ ?>

<?php include("../compartido/guardar-historial-acciones.php"); ?>
<style>
	.float {
		position: fixed;
		width: 60px;
		height: 60px;
		bottom: 40px;
		right: 40px;
		border-radius: 50px;
		text-align: center;
		font-size: 30px;
		box-shadow: 2px 2px 3px #999;
		z-index: 100;
	}

	.my-float {
		margin-top: 16px;
	}

	.my-notificacion {
		position: fixed;
		margin-top: 0px;
		background-color: red;
		border-radius: 50px;
		font-size: 12px;
		border-radius: 50px;
		padding: 7px;
		bottom: 80px;
		right: 40px;
	}
</style>
<script>
	// socket en la espera de una notificacion general
	var id_usuario = '<?=$_SESSION['id']?>';
	var institucion_actual = <?=$_SESSION['idInstitucion']?>;
	socket.on("notificacion_sala_" + id_usuario+"_inst_"+institucion_actual, (data) => {
		let div_notificacion = document.getElementById('div_notificacion');
		let boton_notificacion = document.getElementById('boton_notificacion');
		if(boton_notificacion){
			console.log(data);
			if(div_notificacion!=null){
				if (data> 0) {
				div_notificacion.innerHTML=data;
				div_notificacion.classList.add("fa-beat-fade");
				}else if(data==0){
					boton_notificacion.removeChild(div_notificacion);
				}
			}else{
				const div_notificacion_new = document.createElement('div');
				div_notificacion_new.classList.add("my-notificacion","fa-beat-fade");
				div_notificacion_new.id="div_notificacion";
				div_notificacion_new.innerHTML=data;
				if (data> 0) {
				boton_notificacion.appendChild(div_notificacion_new);
				};
			}
		}
	});
</script>
<?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV){ ?>
<script>
	socket.on("notificar_solicitud_desbloqueo_<?=$_SESSION['idInstitucion']?>", (data) => {
		$.toast({
			heading: 'SOLICITUD DE DESBLOQUEO',  
			text: 'Ha recibido una nueva solicitud de desbloqueo para el estudiante '+data['nombre']+'.',
			position: 'bottom-right',
			showHideTransition: 'slide',
			loaderBg:'#26c281', 
			icon: 'warning', 
			hideAfter: 10000, 
			stack: 6
		})
	});
</script>
<?php } ?>
<!-- boton de chat -->
<?php if($idPaginaInterna != 'DT0209' && $datosUsuarioActual['uss_tipo'] == TIPO_DEV ){ ?>
<a id="boton_notificacion" style="text-shadow: none;color: #fefefe;font-family:arial; background:<?= $Plataforma->colorUno; ?>;" href="../directivo/chat2.php" class="float"> <!-- "fa-beat-fade" se agregará una clase cuando hay una nueva notificacion  -->
	<i class="fa fa-comments my-float"></i>
	<?php

	$consultaNotificaicones = mysqli_query(
		$conexion,
		"SELECT COUNT(chat_visto) as cantidad
	FROM $baseDatosSocial.chat 
	WHERE chat_destino_usuario = '" . $_SESSION['id'] . "' AND  chat_destino_institucion = '" . $_SESSION['idInstitucion'] . "'  AND chat_visto=1"
	);

	while ($resultNotificacion = mysqli_fetch_array($consultaNotificaicones, MYSQLI_BOTH)) {
		if ($resultNotificacion['cantidad'] > 0) {
	?>			<div id="div_notificacion" class="my-notificacion"><?= $resultNotificacion['cantidad'] ?></div>
	<?php 	}
	} ?>
</a>
<?php 	
	} ?>

<script>
const forms = document.querySelectorAll('form[name="formularioGuardar"]');

let formulario = forms[forms.length - 1]; // Obtener el último formulario del documento en caso de encontrar varias coincidencias.

if(typeof formulario !== 'undefined' && formulario !== null) {

	const btnSubmit = formulario.querySelector('button[type=submit]');

	btnSubmit.addEventListener("click", function() {
		// Validar el formulario al enviarlo.
		if (!formulario.checkValidity()) {
			formulario.reportValidity();
			return false; // Si el formulario no es válido, detener el envío.
		}

		btnSubmit.setAttribute("disabled", true);

		let pageStatic = formulario.querySelector('input[name="pageStatic"]');

		if (pageStatic != null && pageStatic.value === 'true') {
			let btnSubmitOriginalText = btnSubmit.innerHTML;
			btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> CARGANDO';

			setTimeout(() => {
				formulario.submit();
				btnSubmit.removeAttribute("disabled");
				btnSubmit.innerHTML = btnSubmitOriginalText;
			}, 1000);

			return false;
		}

		btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> GUARDANDO CAMBIOS';

		setTimeout(() => {
			formulario.submit();
		}, 1000);

		return false;
	});
}
</script>


<!-- start footer -->
<div class="page-footer">
	<div class="page-footer-inner"> <?=date("Y");?> &copy; Plataforma SINTIA By
		<a href="#" target="_top" class="makerCss">ODERMAN</a> | Tiempo de carga de la pagina: <b><?=$tiempoMostrar;?></b> segundos
	</div>

	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- end footer -->

<?php Conexion::getConexion()->closeConnection(); ?>


<!-- <script type="text/javascript">
  window._mfq = window._mfq || [];
  (function() {
    var mf = document.createElement("script");
    mf.type = "text/javascript"; mf.defer = true;
    mf.src = "//cdn.mouseflow.com/projects/ae17c015-82d9-4150-91b6-d01309880044.js";
    document.getElementsByTagName("head")[0].appendChild(mf);
  })();
</script> -->