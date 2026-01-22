	<div align="center" style="font-size:10px; margin-top:10px;color:<?= $Plataforma->colorUno; ?>">
		<?php 
		// Si $mostrarLogoEnFooter no está definida, mostrar el logo por defecto (compatibilidad con otros archivos)
		$mostrarLogo = isset($mostrarLogoEnFooter) ? $mostrarLogoEnFooter : true;
		if ($mostrarLogo): ?>
			<img src="<?= $Plataforma->logo; ?>" height="75" width="150">
			<br>
		<?php endif; ?>
		<?php 
		// Si $mostrarLeyendaEnFooter no está definida, mostrar la leyenda por defecto (compatibilidad con otros archivos)
		$mostrarLeyenda = isset($mostrarLeyendaEnFooter) ? $mostrarLeyendaEnFooter : true;
		if ($mostrarLeyenda): 
			// Función para formatear fecha en español
			$dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
			$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
			$diaSemana = $dias[date('w')];
			$dia = date('d');
			$mes = $meses[(int)date('n')];
			$anio = date('Y');
			$fechaEspanol = $diaSemana . ', ' . $dia . ' de ' . $mes . ' de ' . $anio;
		?>
			PLATAFORMA EDUCATIVA SINTIA - <?= $fechaEspanol; ?>
		<?php endif; ?>
	</div>