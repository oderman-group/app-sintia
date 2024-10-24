<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- dropzone -->
<link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<?php
if (!empty($data["dataTotal"])) {
	require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
	require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
	require_once(ROOT_PATH . "/main-app/class/Modulos.php");
	require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
	$usuariosClase = new UsuariosFunciones;
}

$contReg               				= 1;

$permisoActualizarPeriodo   		= Modulos::validarSubRol(['DT0344']);
$permisoEliminarComportamiento     	= Modulos::validarSubRol(['DT0345']);


foreach ($data["data"] as $resultado) {

	$fotoEstudiante = $usuariosClase->verificarFoto($resultado['mat_foto']);

	$infoTooltipEstudiante = "<p>
								<img src='{$fotoEstudiante}' class='img-thumbnail' width='120px;' height='120px;'>
								</p>
								<b>Fecha de Creación:</b><br>
								{$resultado['dn_fecha']}<br>
								<b>Nota:</b><br>
								{$resultado['dn_nota']}<br>
								<b>Asignatura:</b><br>
								{$resultado['mat_nombre']}<br>
								<b>Curso:</b><br>
								{$resultado['gra_nombre']} {$resultado['gru_nombre']}<br>
							";

	$nombreResponsable = UsuariosPadre::nombreCompletoDelUsuario($resultado);
	$idResponsable     = $resultado['uss_id'];

	$arrayEnviar = array("tipo" => 1, "descripcionTipo" => "Para ocultar fila del registro.");
	$arrayDatos = json_encode($arrayEnviar);
	$objetoEnviar = htmlentities($arrayDatos);
?>
	<tr id="reg<?= $resultado['id_nuevo']; ?>">
		<td><?= $resultado["id_nuevo"]; ?></td>
		<?php $nombre = Estudiantes::NombreCompletoDelEstudiante($resultado); ?>
		<td><a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="<?= $nombre; ?>" data-content="<?= $infoTooltipEstudiante; ?>" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000;"><?= $nombre; ?></a></td>
		<td><?= $resultado["dn_observacion"]; ?></td>
		<td><?= $resultado["dn_nota"]; ?></td>
		<td>
			<?php if (Modulos::validarPermisoEdicion() && $permisoActualizarPeriodo) { ?>
				<input
					type="number"
					value="<?= !empty($resultado['dn_periodo']) ? $resultado['dn_periodo'] : ""; ?>"
					id="P<?= $resultado['id_nuevo']; ?>"
					data-id-registro="<?= $resultado['id_nuevo']; ?>"
					data-periodo-actual="<?= $resultado['dn_periodo']; ?>"
					data-periodo-total="<?= $config['conf_periodos_maximos']; ?>"
					onChange="comportamientoPeriodo(this)"
					tabindex="10<?= $contReg; ?>">
			<?php } else { ?>
				<?= !empty($resultado['dn_periodo']) ? $resultado['dn_periodo'] : ""; ?>
			<?php } ?>
		</td>
		<td><?= $nombreResponsable; ?>
			<?php if (!empty($idResponsable) && !empty($nombreResponsable)) { ?>
				<br><a href="mensajes-redactar.php?para=<?= base64_encode($idResponsable); ?>" style="text-decoration:underline; color:blue;">Enviar mensaje</a>
			<?php } ?>
		</td>
		<td>
			<div class="btn-group">
				<button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
				<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
					<i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu" id="Acciones_<?= $resultado['id_nuevo']; ?>" style="z-index: 10000;">
					<?php if ($permisoEliminarComportamiento) { ?>
						<li><a href="#" title="<?= $objetoEnviar; ?>" id="<?= $resultado['id_nuevo']; ?>" name="comportamiento-eliminar.php?id=<?= base64_encode($resultado['id_nuevo']); ?>" onClick="deseaEliminar(this)">Eliminar</a></li>
					<?php } ?>
				</ul>
			</div>

		</td>
	</tr>
<?php
	$contReg++;
}
?>