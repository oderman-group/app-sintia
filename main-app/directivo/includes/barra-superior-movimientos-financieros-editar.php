<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
	<!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  style="color:<?=$Plataforma->colorUno;?>;">
					Más opciones
					<span class="fa fa-angle-down"></span>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="movimientos-factura-venta.php?id=<?=base64_encode($resultado['fcu_id']);?>" target="_blank"><?=$frases[57][$datosUsuarioActual['uss_idioma']];?></a>
				</div>
			</li>
		</ul> 
	</div> -->
	<?php if (!empty($resultado) && isset($resultado['fcu_id']) && !empty($resultado['fcu_id'])) { ?>
	<a href="movimientos-factura-venta.php?id=<?=base64_encode($resultado['fcu_id']);?>" class="btn btn-danger" target="_blank">
		<i class="fa fa-print"></i> Imprimir factura
	</a>
	<?php } ?>
</nav><br>