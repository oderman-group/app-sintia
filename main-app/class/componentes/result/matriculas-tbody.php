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

$contReg               = 1;
$moduloMediaTecnica    = Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_MEDIA_TECNICA);
$moduloAdministrativo  = Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_ADMINISTRATIVO);
$moduloFinanciero      = Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_FINANCIERO);
$moduloConvivencia     = Modulos::verificarModulosDeInstitucion($informacion_inst["info_institucion"], Modulos::MODULO_DISCIPLINARIO);

$permisoBloquearUsuario   = Modulos::validarSubRol(['DT0087']);
$permisoCambiarEstado     = Modulos::validarSubRol(['DT0217']);
$permisoEditarUsuario     = Modulos::validarSubRol(['DT0124']);
$permisoEditarEstudiante  = Modulos::validarSubRol(['DT0078']);
$permisoCrearSion         = Modulos::validarSubRol(['DT0218']);
$permisoCambiarGrupo      = Modulos::validarSubRol(['DT0083']);
$permisoRetirar           = Modulos::validarSubRol(['DT0074']);
$permisoReservar          = Modulos::validarSubRol(['DT0219']);
$permisoEliminar          = Modulos::validarSubRol(['DT0162']);
$permisoCrearUsuario      = Modulos::validarSubRol(['DT0220']);
$permisoAutoLogin         = Modulos::validarSubRol(['DT0129']);
$permisoBoletines         = Modulos::validarSubRol(['DT0224']);
$permisoLibroMatricula    = Modulos::validarSubRol(['DT0247']);
$permisoInformeParcial    = Modulos::validarSubRol(['DT0248']);
$permisoHojaMatricula     = Modulos::validarSubRol(['DT0249']);
$permisoAspectos          = Modulos::validarSubRol(['DT0023']);
$permisoFinanzas          = Modulos::validarSubRol(['DT0093']);
$permisoReportes          = Modulos::validarSubRol(['DT0117']);

foreach ($data["data"] as $resultado) {

	$bgColor = $resultado['uss_bloqueado'] == 1 ? 'style="background-color: #ff572238;"' : '';
	$color = $resultado["mat_inclusion"] == 1 ? 'style="color: blue;"' : '';


	$miArray = [
		'id_estudiante'    => $resultado['mat_id'],
		'estado_matricula' => $resultado['mat_estado_matricula'],
		'bloqueado' 	   => $resultado['uss_bloqueado'],
		'id_usuario'       => $resultado['uss_id'],
	];

	$dataParaJavascript = json_encode($miArray);

	$cheked = '';
	if ($resultado['uss_bloqueado'] == 1) {
		$cheked = 'checked';
	}

	$fotoEstudiante = $usuariosClase->verificarFoto($resultado['mat_foto']);

	$marcaMediaTecnica     = '';
	if (
		$resultado['mat_tipo_matricula'] == GRADO_INDIVIDUAL && 
		array_key_exists(10, $arregloModulos) 
		&& $moduloMediaTecnica
	) {
		$marcaMediaTecnica = '<i class="fa fa-bookmark" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Media técnica"></i> ';
	}

	$infoTooltipEstudiante = "<p>
								<img src='{$fotoEstudiante}' class='img-thumbnail' width='120px;' height='120px;'>
							  </p>
							  <b>Fecha de matrícula:</b><br>
							  {$resultado['mat_fecha']}<br>
							  <b>Teléfono:</b><br>
							  {$resultado['mat_telefono']}<br>
							  <b>Documento:</b><br>
							  {$resultado['mat_documento']}<br>
							  <b>Email:</b><br>
							  {$resultado['mat_email']}<br>
							  <b>Fecha de nacimiento:</b><br>
							  {$resultado['mat_fecha_nacimiento']}
							";

	$acudiente       = $resultado["mat_acudiente"];
	$nombreAcudiente = UsuariosPadre::nombreCompletoDelUsuario($resultado);
	$idAcudiente     = $acudiente;


?>
	<tr id="EST<?= $resultado['mat_id']; ?>" <?= $bgColor; ?>>
		<td>
			<?php if ($resultado["mat_compromiso"] == 1) { ?>
				<a href="javascript:void(0);" title="Activar para la matricula" onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-activar.php?id=<?= base64_encode($resultado["mat_id"]); ?>')"><img src="../files/iconos/agt_action_success.png" height="20" width="20"></a>
			<?php } elseif (!empty($resultado["mat_compromiso"])) { ?>
				<a href="javascript:void(0);" title="Bloquear para la matricula" onClick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-bloquear.php?id=<?= base64_encode($resultado["mat_id"]); ?>')"><img src="../files/iconos/msn_blocked.png" height="20" width="20"></a>
			<?php } ?>
			<?= $resultado["mat_id_nuevo"]; ?>
		</td>
		<td>
			<?php if (!empty($resultado['uss_usuario']) && $permisoBloquearUsuario) { ?>
				<div class="input-group spinner col-sm-10" style="padding-top: 5px;">
					<label class="switchToggle">
						<input type="checkbox" id="checkboxCambiarBloqueo<?= $resultado['mat_id']; ?>" value="1" onChange='cambiarBloqueo(<?= $dataParaJavascript; ?>)' <?= $cheked; ?>>
						<span class="slider red round"></span>
					</label>
				</div>
			<?php } ?>
		</td>
		<td>
			<?php
			$cambiarEstado = '';
			if ($permisoCambiarEstado) {
				$cambiarEstado = "onclick='cambiarEstadoMatricula(" . $dataParaJavascript . ")'";
			}
			if(!empty($resultado['mat_estado_matricula'])){
			?>
			<a style="cursor: pointer;" id="estadoMatricula<?= $resultado['mat_id']; ?>" <?= $cambiarEstado; ?>>
				<span class="<?= $estadosEtiquetasMatriculas[$resultado['mat_estado_matricula']]; ?>">
					<?= $estadosMatriculasEstudiantes[$resultado['mat_estado_matricula']]; ?>
				</span>
			</a>
			<?php } ?>
		</td>
		<td><?= $resultado['mat_documento']; ?></td>
		<?php $nombre = Estudiantes::NombreCompletoDelEstudiante($resultado); ?>

		<td <?= $color; ?>><?= $marcaMediaTecnica; ?><a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="<?= Estudiantes::NombreCompletoDelEstudiante($resultado); ?>" data-content="<?= $infoTooltipEstudiante; ?>" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000;"><?= $nombre; ?></a></td>
		<td><?= strtoupper($resultado['gra_nombre'] . " " . $resultado['gru_nombre']); ?></td>
		<td><?= $resultado['uss_usuario']; ?></td>
		<?php
		$editarAcudiente = $nombreAcudiente;
		if ($permisoEditarUsuario && !empty($idAcudiente)) {
			$editarAcudiente = '<a href="usuarios-editar.php?id=' . base64_encode($idAcudiente) . '" style="text-decoration: underline;">' . $nombreAcudiente . '</a>';
		}
		?>
		<td><?= $editarAcudiente; ?>
			<?php if (!empty($idAcudiente) && !empty($nombreAcudiente)) { ?>
				<br><a href="mensajes-redactar.php?para=<?= base64_encode($idAcudiente); ?>" style="text-decoration:underline; color:blue;">Enviar mensaje</a>
			<?php } ?>
		</td>
		<td>
			<div class="btn-group">
				<button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
				<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
					<i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu" id="Acciones_<?= $resultado['mat_id']; ?>" style="z-index: 10000;">
					<?php if (Modulos::validarPermisoEdicion()) { ?>
						<?php if ($permisoEditarEstudiante) { ?>
							<li><a href="estudiantes-editar.php?id=<?= base64_encode($resultado['mat_id']); ?>"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> matrícula</a></li>
						<?php } ?>

						<?php if ($config['conf_id_institucion'] == ICOLVEN && $permisoCrearSion) { ?>
							<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro que desea transferir este estudiante a SION?','question','estudiantes-crear-sion.php?id=<?= base64_encode($resultado['mat_id']); ?>')">Transferir a SION</a></li>
						<?php } ?>

						<?php if (array_key_exists(4, $arregloModulos) && $moduloAdministrativo && !empty($resultado['uss_id']) && $permisoEditarUsuario) { ?>
							<li><a href="usuarios-editar.php?id=<?= base64_encode($resultado['uss_id']); ?>"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> usuario</a></li>
						<?php } ?>


						<?php if (!empty($resultado['gra_nombre']) && $permisoCambiarGrupo  &&  empty($marcaMediaTecnica)) { ?>
							<li><a href="javascript:void(0);" data-toggle="modal" onclick="cambiarGrupo('<?= base64_encode($resultado['mat_id']) ?>')">Cambiar de grupo</a></li>
						<?php } ?>
						<?php if ($permisoRetirar && !empty($resultado['mat_id'])) {
							$retirarRestaurar = 'Retirar';
							if ($resultado['mat_estado_matricula'] == CANCELADO) {
								$retirarRestaurar = 'Restaurar';
							}
						?>
							<li><a href="javascript:void(0);" data-toggle="modal" onclick="retirar('<?= base64_encode($resultado['mat_id']) ?>')"><?= $retirarRestaurar ?></a></li>
						<?php } ?>
						<?php if (!empty($resultado['mat_grado']) && !empty($resultado['mat_grupo']) && $permisoReservar) { ?>
							<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro que desea reservar el cupo para este estudiante?','question','estudiantes-reservar-cupo.php?idEstudiante=<?= base64_encode($resultado['mat_id']); ?>')">Reservar cupo</a></li>
						<?php } ?>

						<?php if ($permisoEliminar) { ?>
							<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro de ejecutar esta acción?','question','estudiantes-eliminar.php?idE=<?= base64_encode($resultado["mat_id"]); ?>&idU=<?= base64_encode($resultado["mat_id_usuario"]); ?>')">Eliminar</a></li>
						<?php } ?>

						<?php if ($permisoCrearUsuario) { ?>
							<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Está seguro de ejecutar esta acción?','question','estudiantes-crear-usuario-estudiante.php?id=<?= base64_encode($resultado["mat_id"]); ?>')">Generar usuario</a></li>
						<?php } ?>

						<?php if (!empty($resultado['uss_usuario']) && $permisoAutoLogin) { ?>
							<li><a href="auto-login.php?user=<?= base64_encode($resultado['mat_id_usuario']); ?>&tipe=<?= base64_encode(4) ?>">Autologin</a></li>
						<?php } ?>

					<?php } ?>

					<?php if (!empty($resultado['mat_grado']) && !empty($resultado['mat_grupo'])) { ?>
						<?php if ($permisoBoletines && ($resultado['mat_estado_matricula'] != NO_MATRICULADO && $resultado['mat_estado_matricula'] != EN_INSCRIPCION)) { ?>
							<li><a href="../compartido/matricula-boletin-curso-<?= $resultado['gra_formato_boletin']; ?>.php?id=<?= base64_encode($resultado["mat_id"]); ?>&periodo=<?= base64_encode($config[2]); ?>" target="_blank">Boletín</a></li>
						<?php } ?>
						<?php if ($permisoLibroMatricula) { ?>
							<li><a href="../compartido/matricula-libro-curso-<?= $config['conf_libro_final'] ?>.php?id=<?= base64_encode($resultado["mat_id"]); ?>&periodo=<?= base64_encode($config[2]); ?>" target="_blank">Libro Final</a></li>
						<?php } ?>
						<?php if ($permisoInformeParcial) { ?>
							<li><a href="../compartido/informe-parcial.php?estudiante=<?= base64_encode($resultado["mat_id"]); ?>" target="_blank">Informe parcial</a></li>
						<?php } ?>
					<?php } ?>

					<?php if (!empty($resultado['mat_matricula']) && $permisoHojaMatricula) { ?>
						<li><a href="../compartido/matriculas-formato3.php?ref=<?= base64_encode($resultado["mat_matricula"]); ?>" target="_blank">Hoja de matrícula</a></li>
					<?php } ?>

					<?php if ($config['conf_id_institucion'] == ICOLVEN && !empty($resultado['mat_codigo_tesoreria'])) { ?>
						<li><a href="http://sion.icolven.edu.co/Services/ServiceIcolven.svc/GenerarEstadoCuenta/<?= $resultado['mat_codigo_tesoreria']; ?>/<?= date('Y'); ?>" target="_blank">SION - Estado de cuenta</a></li>
					<?php } ?>

					<?php if (!empty($resultado['uss_usuario'])) { ?>
						<?php if ($permisoAspectos) { ?>
							<li><a href="aspectos-estudiantiles.php?idR=<?= base64_encode($resultado['mat_id_usuario']); ?>">Ficha estudiantil</a></li>
						<?php }
						if (array_key_exists(2, $arregloModulos) && $moduloFinanciero && $permisoFinanzas) { ?>
							<!-- <li><a href="finanzas-cuentas.php?id=<?= base64_encode($resultado["mat_id_usuario"]); ?>" target="_blank">Estado de cuenta</a></li> -->
						<?php }
						if (array_key_exists(3, $arregloModulos) && $moduloConvivencia && $permisoReportes) { ?>
							<!-- <li><a href="reportes-lista.php?est=<?= base64_encode($resultado["mat_id_usuario"]); ?>&filtros=<?= base64_encode(1); ?>" target="_blank">Disciplina</a></li> -->
					<?php }
					} ?>
				</ul>
			</div>

		</td>
	</tr>
<?php
	$contReg++;
}
?>
<script type="application/javascript">
	async function cambiarGrupo(mat_id) {
		var data = {
			"id": mat_id
		};
		abrirModal("Cambiar de grupo", "estudiantes-cambiar-grupo-modal.php", data);
	}

	async function retirar(mat_id) {
		var data = {
			"id": mat_id
		};
		abrirModal("Retirar Estudiante", "estudiantes-retirar-modal.php", data);
	}
</script>
