<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

$contenidoMsj = '
	<p>
		Hola <b>' . strtoupper($data['usuario_nombre']) . '</b>, Bienvenido a la Plataforma SINTIA. Hemos creado su cuenta como DIRECTIVO.<br>
		A continuación encontrará sus datos de acceso.<br>
		<b>Usuario:</b> ' . $data['usuario_usuario'] . '<br>
		<b>Contraseña:</b> ' . $data['usuario_clave'] . '<br>
	</p>

	<p>
		<h3 style="text-align: center;">
		<a href="https://demo.plataformasintia.com/app-sintia/main-app/index.php?inst='.$data['institucion_id'].'" target="_blank" style="color: #41c4c4; font-weight:bold;">ACCEDER A MI CUENTA DE DIRECTIVO AHORA</a>
		</h3>
	</p>

';
?>

<html>
    <body style="background-color:#FFF;">

		<div style="width: 100%; display: grid; place-content: center; justify-content: center;">

            <div style="width:600px; text-align:justify; padding:15px;">
				<img src="<?=$Plataforma->logo;?>" width="100">
			</div>

			<div style="font-family:arial; background:<?=$Plataforma->colorUno;?>; width:600px; color:#FFF; text-align:center; padding:15px;">
				<h3>¡Bienvenido a la Plataforma SINTIA!</h3>
			</div>

			<div style="font-family:arial; background:#FAFAFA; width:600px; color:#000; text-align:justify; padding:15px;"><?=$contenidoMsj;?></div>

			<div style="width:600px; color:#000; text-align:center; padding:15px;">
				<img src="<?=$Plataforma->logo;?>" width="50"><br>
				¡Que tengas un excelente d&iacute;a!<br>
				<a href="https://plataformasintia.com/">www.plataformasintia.com</a>
			</div>

        </div>
		<p>&nbsp;</p>

    <body>
<html>