<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;
?>
<html>
    <body style="background-color:#FFF;">

		<div style="width: 100%; display: grid; place-content: center; justify-content: center;">

            <div style="width:600px; text-align:justify; padding:15px;">
				<img src="<?=$Plataforma->logo;?>" width="100">
			</div>

			<div style="font-family:arial; background:<?=$Plataforma->colorUno;?>; width:600px; color:#FFF; text-align:center; padding:15px;">
				<h3>Solicitud de desbloqueo</h3>
			</div>

			<div style="font-family:arial; background:#FAFAFA; width:600px; color:#000; text-align:justify; padding:15px;">
				<p>
					Estimado <b><?=strtoupper($data['usuario_nombre'])?></b>,<br>
					<?=$data['motivo']?><br>
				</p>

				<?php if ($data['usuario_estado'] == Administrativo_General_Solicitud::SOLICITUD_ACEPTADA) { ?>
					<p>
						<h3 style="text-align: center;">
						<a href="<?=REDIRECT_ROUTE;?>" target="_blank" style="color: #41c4c4; font-weight:bold;">ACCEDER A MI CUENTA</a>
						</h3>
					</p>
				<?php } ?>
			</div>

			<div style="width:600px; color:#000; text-align:center; padding:15px;">
				<img src="<?=$Plataforma->logo;?>" width="50"><br>
				¡Que tengas un excelente d&iacute;a!<br>
				<a href="https://plataformasintia.com/">www.plataformasintia.com</a>
			</div>

        </div>
		<p>&nbsp;</p>

    <body>
<html>