<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

$nombreUsuario      = $data['usuario_nombre'] ?? 'Usuario';
$institucionNombre  = $data['institucion_nombre'] ?? 'Plataforma SINTIA';
$consecutivoFactura = $data['consecutivo_factura'] ?? '';
$fechaFactura       = $data['fecha_factura'] ?? '';
$saldoFormateado    = $data['saldo_formateado'] ?? '';
$detalleFactura     = $data['detalle_factura'] ?? '';
$urlPortal          = $data['url_portal'] ?? REDIRECT_ROUTE;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Recordatorio de Saldo Pendiente</title>
	<style>
		*{margin:0;padding:0;box-sizing:border-box;}
		body{
			font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
			background:#f3f4f6;
			padding:24px;
			color:#1f2937;
		}
		.email-container{
			max-width:640px;
			margin:0 auto;
			background:#ffffff;
			border-radius:24px;
			overflow:hidden;
			box-shadow:0 25px 60px rgba(15,23,42,0.18);
		}
		.email-header{
			background:linear-gradient(135deg, <?=$Plataforma->colorUno;?> 0%, <?=$Plataforma->colorDos;?> 100%);
			padding:48px 32px;
			text-align:center;
			position:relative;
			overflow:hidden;
		}
		.email-header::before,.email-header::after{
			content:'';
			position:absolute;
			width:220px;
			height:220px;
			border:50px solid rgba(255,255,255,0.12);
			border-radius:50%;
			top:-120px;
			left:-120px;
			animation:pulse 18s linear infinite;
		}
		.email-header::after{
			top:auto;
			left:auto;
			bottom:-140px;
			right:-140px;
			animation-delay:-9s;
		}
		@keyframes pulse{
			0%{transform:scale(1) rotate(0deg);}
			50%{transform:scale(1.05) rotate(180deg);}
			100%{transform:scale(1) rotate(360deg);}
		}
		.icon-wrapper{
			width:96px;
			height:96px;
			margin:0 auto 18px;
			border-radius:24px;
			background:rgba(255,255,255,0.18);
			display:flex;
			align-items:center;
			justify-content:center;
			backdrop-filter:blur(6px);
		}
		.icon-wrapper svg{
			width:58px;
			height:58px;
			fill:#ffffff;
		}
		.email-header h1{
			color:#fff;
			font-size:30px;
			font-weight:800;
			margin-bottom:10px;
			position:relative;
			z-index:1;
			text-shadow:0 8px 18px rgba(15,23,42,0.25);
		}
		.email-header p{
			color:rgba(255,255,255,0.92);
			font-size:17px;
			position:relative;
			z-index:1;
		}
		.email-body{
			padding:38px 34px 42px;
		}
		.saludo{
			font-size:17px;
			line-height:1.6;
			margin-bottom:24px;
			color:#374151;
		}
		.highlight-card{
			background:linear-gradient(135deg, rgba(255,255,255,0.96) 0%, rgba(249,250,251,0.96) 100%);
			border:2px solid rgba(99,102,241,0.1);
			border-radius:20px;
			padding:26px 28px;
			box-shadow:0 18px 35px rgba(79,70,229,0.1);
			margin-bottom:30px;
		}
		.highlight-title{
			font-size:19px;
			font-weight:700;
			color:#4338ca;
			margin-bottom:22px;
			display:flex;
			align-items:center;
			gap:10px;
		}
		.highlight-title svg{
			width:22px;
			height:22px;
			fill:#4338ca;
		}
		.data-grid{
			display:grid;
			grid-template-columns:repeat(2,minmax(0,1fr));
			gap:18px;
		}
		.data-card{
			background:#ffffff;
			border-radius:16px;
			padding:18px;
			border:1px solid #e5e7eb;
			box-shadow:0 8px 18px rgba(15,23,42,0.08);
		}
		.data-label{
			font-size:13px;
			text-transform:uppercase;
			letter-spacing:0.08em;
			color:#9ca3af;
			margin-bottom:8px;
		}
		.data-value{
			font-size:20px;
			font-weight:700;
			color:#111827;
			word-break:break-word;
		}
		.data-value.negative{color:#b91c1c;}
		.detalle-box{
			margin-top:24px;
			background:#f9fafb;
			border-radius:18px;
			padding:22px;
			border:1px solid #e5e7eb;
		}
		.detalle-box h3{
			font-size:16px;
			font-weight:700;
			color:#1f2937;
			margin-bottom:12px;
		}
		.detalle-box p{
			font-size:15px;
			color:#4b5563;
			line-height:1.6;
		}
		.callout{
			margin:30px 0;
			padding:20px 22px;
			background:linear-gradient(135deg,#fff7ed 0%,#ffedd5 100%);
			border-left:5px solid #fb923c;
			border-radius:18px;
		}
		.callout h4{
			font-size:15px;
			font-weight:700;
			color:#c2410c;
			margin-bottom:10px;
			display:flex;
			align-items:center;
			gap:10px;
		}
		.callout p{
			font-size:14px;
			color:#b45309;
			margin:0;
			line-height:1.5;
		}
		.cta-wrapper{
			text-align:center;
			margin:34px 0;
		}
		.cta-button{
			display:inline-block;
			padding:16px 42px;
			border-radius:16px;
			background:linear-gradient(135deg,#10b981 0%,#059669 100%);
			color:#fff !important;
			text-decoration:none;
			font-weight:700;
			font-size:17px;
			box-shadow:0 22px 44px rgba(16,185,129,0.3);
			transition:transform 0.25s ease,box-shadow 0.25s ease;
		}
		.cta-button:hover{
			transform:translateY(-2px);
			box-shadow:0 28px 55px rgba(13,148,136,0.32);
		}
		.signature{
			margin-top:30px;
			line-height:1.6;
			color:#374151;
			font-size:15px;
		}
		.signature strong{
			display:block;
			margin-top:10px;
			font-size:16px;
			color:#1f2937;
		}
		.email-footer{
			text-align:center;
			padding:28px;
			background:#111827;
			color:#9ca3af;
			font-size:13px;
		}
		.footer-logo img{
			width:52px;
			height:auto;
			margin-bottom:12px;
		}
		.footer-links a{
			color:#818cf8;
			text-decoration:none;
			margin:0 6px;
		}
		@media(max-width:600px){
			body{padding:18px;}
			.email-body{padding:30px 22px;}
			.data-grid{grid-template-columns:1fr;}
			.cta-button{width:100%;}
		}
	</style>
</head>
<body>
	<div class="email-container">
		<div class="email-header">
			<div class="icon-wrapper">
				<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 2a10 10 0 100 20 10 10 0 000-20zm.75 5a.75.75 0 10-1.5 0v5a.75.75 0 00.326.62l3.5 2.333a.75.75 0 01-.848 1.248L11 13.4V7a.75.75 0 011.5 0z"/>
				</svg>
			</div>
			<h1>Recordatorio de saldo pendiente</h1>
			<p>Te ayudamos a mantener tus compromisos al día</p>
		</div>

		<div class="email-body">
			<p class="saludo">
				Hola <strong><?=htmlspecialchars($nombreUsuario);?></strong>,
				<br><br>
				Este es un recordatorio amigable sobre el saldo pendiente registrado en la plataforma de <?=htmlspecialchars($institucionNombre);?>.
			</p>

			<div class="highlight-card">
				<div class="highlight-title">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15 7a4 4 0 11-8 0 4 4 0 018 0zm-3 7a6 6 0 00-6 6h12a6 6 0 00-6-6z"/></svg>
					Resumen de tu factura
				</div>
				<div class="data-grid">
					<div class="data-card">
						<div class="data-label">Consecutivo</div>
						<div class="data-value"><?=htmlspecialchars($consecutivoFactura);?></div>
					</div>
					<div class="data-card">
						<div class="data-label">Fecha de emisión</div>
						<div class="data-value"><?=htmlspecialchars($fechaFactura);?></div>
					</div>
					<div class="data-card">
						<div class="data-label">Saldo pendiente</div>
						<div class="data-value negative"><?=htmlspecialchars($saldoFormateado);?></div>
					</div>
					<div class="data-card">
						<div class="data-label">Estado</div>
						<div class="data-value" style="color:#0369a1;">Pendiente por pago</div>
					</div>
				</div>
				<?php if(!empty($detalleFactura)): ?>
				<div class="detalle-box">
					<h3>Detalle de la factura</h3>
					<p><?=nl2br(htmlspecialchars($detalleFactura));?></p>
				</div>
				<?php endif; ?>
			</div>

			<div class="callout">
				<h4>
					<svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm.007 4a1.25 1.25 0 11-.014 2.5 1.25 1.25 0 01.014-2.5zM11 10h2v8h-2z"/></svg>
					¿Ya realizaste tu pago?
				</h4>
				<p>
					Si ya completaste el pago, por favor ignora este mensaje. De lo contrario, te invitamos a realizarlo cuanto antes para evitar interrupciones en el servicio.
					Puedes responder este correo si deseas reportar el pago o necesitas soporte adicional.
				</p>
			</div>

			<div class="cta-wrapper">
				<a href="<?=htmlspecialchars($urlPortal);?>" target="_blank" class="cta-button">
					Ir a mi portal de pagos
				</a>
			</div>

			<div class="signature">
				Gracias por tu atención,<br>
				<strong>Equipo administrativo - <?=htmlspecialchars($institucionNombre);?></strong>
				<p style="margin-top:10px; color:#6b7280;">
					Estamos disponibles para ayudarte con cualquier duda relacionada al proceso de pago.
				</p>
			</div>
		</div>

		<div class="email-footer">
			<div class="footer-logo">
				<img src="<?=$Plataforma->logo;?>" alt="Logo SINTIA">
			</div>
			<p>PLATAFORMA SINTIA<br>Transformando la gestión educativa con tecnología</p>
			<div class="footer-links" style="margin-top:12px;">
				<a href="https://plataformasintia.com" target="_blank">Sitio web</a> •
				<a href="https://sintia.co/blog/" target="_blank">Blog</a> •
				<a href="https://plataformasintia.com/terminos" target="_blank">Términos</a>
			</div>
			<p style="margin-top:14px; font-size:12px;">© <?=date('Y');?> SINTIA. Todos los derechos reservados.</p>
		</div>
	</div>
</body>
</html>

