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

<style>
.expandable-row {
	background-color: #f8f9fa !important;
	border-left: 4px solid #007bff;
}

.student-photo-container img {
	border-radius: 8px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.expand-btn {
	transition: all 0.3s ease;
	padding: 4px 8px;
	font-size: 14px;
}

.expand-btn:hover {
	text-decoration: none;
	transform: scale(1.2);
}

.expand-btn:focus {
	outline: none;
	box-shadow: none;
}

.expandable-row h6 {
	font-weight: 600;
	margin-bottom: 15px;
}

.expandable-row .text-primary {
	color: #007bff !important;
}

.expandable-row .text-success {
	color: #28a745 !important;
}

.expandable-row .text-warning {
	color: #ffc107 !important;
}

.expandable-row .text-info {
	color: #17a2b8 !important;
}

.expandable-row .badge {
	font-size: 0.75em;
}

@media (max-width: 768px) {
	.expandable-row .col-md-2,
	.expandable-row .col-md-3,
	.expandable-row .col-md-4 {
		margin-bottom: 20px;
	}
}

/* Estilos para el nombre del estudiante */
.editable-name {
	transition: color 0.2s ease;
}
</style>
<?php
if (!empty($data["dataTotal"])) {
	require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
	require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
	require_once(ROOT_PATH . "/main-app/class/Modulos.php");
	require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
	$usuariosClase = new UsuariosFunciones;
}

$contReg               = 1;
$moduloMediaTecnica    = Modulos::verificarModulosDeInstitucion(Modulos::MODULO_MEDIA_TECNICA);
$moduloAdministrativo  = Modulos::verificarModulosDeInstitucion(Modulos::MODULO_ADMINISTRATIVO);
$moduloFinanciero      = Modulos::verificarModulosDeInstitucion(Modulos::MODULO_TRANSACCIONES);
$moduloConvivencia     = Modulos::verificarModulosDeInstitucion(Modulos::MODULO_DISCIPLINARIO);

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
$permisoAdjuntarDocumento = Modulos::validarSubRol(['DT0352']);
$esDev = isset($datosUsuarioActual['uss_tipo']) && (int)$datosUsuarioActual['uss_tipo'] === TIPO_DEV;

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
		(isset($resultado['mat_tipo_matricula']) && $resultado['mat_tipo_matricula'] == GRADO_INDIVIDUAL) && 
		array_key_exists(10, $arregloModulos) 
		&& $moduloMediaTecnica
	) {
		$marcaMediaTecnica = '<i class="fa fa-bookmark" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Media técnica"></i> ';
	}

	// Verificar si el estudiante está en estado de inscripción o cancelado
	$estadoEnInscripcion = ($resultado['mat_estado_matricula'] == Estudiantes::ESTADO_EN_INSCRIPCION);
	$estadoCancelado = ($resultado['mat_estado_matricula'] == Estudiantes::ESTADO_CANCELADO);
	// Solo "En inscripción" deshabilita checkbox y acciones
	$estadoNoModificable = $estadoEnInscripcion;
	$disabledCheckbox = $estadoNoModificable ? 'disabled' : '';
	$disabledAcciones = $estadoNoModificable ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '';
	$titleAcciones = $estadoEnInscripcion ? 'Estudiante en proceso de inscripción - Solo lectura' : $frases[54][$datosUsuarioActual['uss_idioma']];

?>
	<tr id="EST<?= $resultado['mat_id']; ?>" <?= $bgColor; ?>>
		<td><button class="btn btn-sm btn-link text-secondary expand-btn" data-id="<?= $resultado['mat_id']; ?>" title="Ver detalles"><i class="fa fa-chevron-right"></i></button></td>
		<td><input type="checkbox" class="estudiante-checkbox" value="<?=$resultado['mat_id'];?>" <?= $disabledCheckbox; ?>></td>
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
			$cursorStyle = '';
			$titleEstado = '';
			
			// Obtener el estado actual del estudiante
			$estadoActual = (int)$resultado['mat_estado_matricula'];
			$estadoMatriculado = ($estadoActual == Estudiantes::ESTADO_MATRICULADO);
			
			// No permitir cambiar estado si está en "En inscripción", "Cancelado" o "Matriculado" (salvo DEV: Matriculado→No matriculado)
			// Matriculado no puede cambiar a Asistente ni a No Matriculado mediante click; usuarios DEV sí pueden Matriculado→No matriculado
			// Cancelado no puede cambiarse desde el badge (se gestiona automáticamente), pero el menú de acciones sí está habilitado
			if ($permisoCambiarEstado && !$estadoNoModificable && !$estadoCancelado && (!$estadoMatriculado || $esDev)) {
				$cambiarEstado = "onclick='cambiarEstadoMatricula(" . $dataParaJavascript . ")'";
				$cursorStyle = "cursor: pointer;";
				if ($estadoMatriculado && $esDev) {
					$titleEstado = 'Clic para pasar a No matriculado (solo usuarios DEV)';
				}
			} else {
				$cursorStyle = "cursor: not-allowed;";
				if ($estadoEnInscripcion) {
					$titleEstado = 'Estudiante en proceso de inscripción - No se puede cambiar el estado';
				} elseif ($estadoCancelado) {
					$titleEstado = 'Estudiante cancelado - No se puede cambiar el estado desde aquí';
				} elseif ($estadoMatriculado && !$esDev) {
					$titleEstado = 'Un estudiante en estado "Matriculado" no puede cambiar a "Asistente" ni a "No matriculado" mediante este botón.';
				}
			}
			
			// Mapear estados a clases de badge
			$badgeClasses = [
				1 => 'badge badge-success',     // Matriculado
				2 => 'badge badge-warning',     // Asistente
				3 => 'badge badge-danger',      // Cancelado
				4 => 'badge badge-secondary',   // No Matriculado
				5 => 'badge badge-info'         // En inscripción
			];
			
			if(!empty($resultado['mat_estado_matricula'])){
				$badgeClass = $badgeClasses[$resultado['mat_estado_matricula']] ?? 'badge badge-secondary';
			?>
			<span class="<?= $badgeClass; ?>" 
				  id="estadoMatricula<?= $resultado['mat_id']; ?>" 
				  style="<?= $cursorStyle; ?>" 
				  <?= $cambiarEstado; ?>
				  <?= !empty($titleEstado) ? 'title="' . htmlspecialchars($titleEstado, ENT_QUOTES) . '"' : ''; ?>>
				<?= $estadosMatriculasEstudiantes[$resultado['mat_estado_matricula']]; ?>
			</span>
			<?php } ?>
		</td>
		<td class="cell-documento"><?= $resultado['mat_documento']; ?></td>
		<?php $nombre = Estudiantes::NombreCompletoDelEstudiante($resultado); ?>

		<td <?= $color; ?>>
			<?= $marcaMediaTecnica; ?><span class="editable-name"><?= $nombre; ?></span>
		</td>
		<td><?= strtoupper($resultado['gra_nombre'] . " " . $resultado['gru_nombre']); ?></td>
		<td><?= $resultado['uss_usuario']; ?></td>
		<td>
			<!-- Botón de tres puntos verticales -->
			<?php if ($estadoNoModificable) { ?>
				<button type="button" class="btn-acciones-menu" <?= $disabledAcciones; ?> title="<?= $titleAcciones; ?>">
					<i class="fa fa-ellipsis-v"></i>
				</button>
			<?php } else { ?>
				<button type="button" class="btn-acciones-menu" onclick="mostrarPanelAcciones(this, '<?= $resultado['mat_id']; ?>')" title="<?= $titleAcciones; ?>">
					<i class="fa fa-ellipsis-v"></i>
				</button>
			<?php } ?>
			
			<!-- Dropdown oculto (solo para almacenar las opciones) -->
			<div style="display: none;">
				<ul class="dropdown-menu" role="menu" id="Acciones_<?= $resultado['mat_id']; ?>">
					<?php if (Modulos::validarPermisoEdicion()) { ?>
						<?php if ($permisoEditarEstudiante) { ?>
							<li><a href="estudiantes-editar.php?id=<?= base64_encode($resultado['mat_id']); ?>"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> matrícula</a></li>
						<?php } ?>
						
						<?php if ($permisoEditarEstudiante) { ?>
							<li><a href="javascript:void(0);" onclick="abrirModalEdicionRapida('<?= htmlspecialchars($resultado['mat_id'], ENT_QUOTES); ?>')">Edición rápida</a></li>
						<?php } ?>

						<?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA) && $permisoCrearSion) { ?>
							<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro que desea transferir este estudiante a SION?','question','estudiantes-crear-sion.php?id=<?= base64_encode($resultado['mat_id']); ?>')">Transferir a SION</a></li>
						<?php } ?>

						<?php if (array_key_exists(4, $arregloModulos) && $moduloAdministrativo && !empty($resultado['uss_id']) && $permisoEditarUsuario) { ?>
							<li><a href="usuarios-editar.php?id=<?= base64_encode($resultado['uss_id']); ?>"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> usuario</a></li>
						<?php } ?>


						<?php 
						// Cambiar de grupo - Mostrar siempre, deshabilitar según condiciones
						$puedeCambiarGrupo = $permisoCambiarGrupo && !empty($resultado['gra_nombre']) && empty($marcaMediaTecnica) && !$estadoCancelado;
						$tooltipCambiarGrupo = '';
						if (!$puedeCambiarGrupo) {
							if (!$permisoCambiarGrupo) {
								$tooltipCambiarGrupo = 'No tiene permisos para cambiar de grupo';
							} elseif ($estadoCancelado) {
								$tooltipCambiarGrupo = 'No se puede cambiar de grupo a estudiantes en estado "Cancelado"';
							} elseif (empty($resultado['gra_nombre'])) {
								$tooltipCambiarGrupo = 'El estudiante no tiene un curso asignado';
							} elseif (!empty($marcaMediaTecnica)) {
								$tooltipCambiarGrupo = 'No se puede cambiar de grupo a estudiantes de Media Técnica';
							}
						}
						?>
						<li>
							<?php if ($puedeCambiarGrupo) { ?>
								<a href="javascript:void(0);" data-toggle="modal" onclick="cambiarGrupo('<?= base64_encode($resultado['mat_id']) ?>')">Cambiar de grupo</a>
							<?php } else { ?>
								<span style="display: block; padding: 3px 20px; color: #999; opacity: 0.5; cursor: not-allowed;" title="<?= htmlspecialchars($tooltipCambiarGrupo, ENT_QUOTES); ?>">Cambiar de grupo</span>
							<?php } ?>
						</li>
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

						<?php 
						// Eliminar - Mostrar siempre, deshabilitar si no es DEV o no tiene permisos
						$puedeEliminar = $permisoEliminar && !empty($resultado['mat_id']) && !empty($resultado['mat_id_usuario']) && isset($datosUsuarioActual['uss_tipo']) && $datosUsuarioActual['uss_tipo'] == TIPO_DEV;
						$tooltipEliminar = '';
						if (!$puedeEliminar) {
							if (!isset($datosUsuarioActual['uss_tipo']) || $datosUsuarioActual['uss_tipo'] != TIPO_DEV) {
								$tooltipEliminar = 'Esta opción solo está disponible para usuarios de tipo Developer (DEV)';
							} elseif (!$permisoEliminar) {
								$tooltipEliminar = 'No tiene permisos para eliminar estudiantes';
							} elseif (empty($resultado['mat_id']) || empty($resultado['mat_id_usuario'])) {
								$tooltipEliminar = 'El estudiante no tiene los datos necesarios para ser eliminado';
							}
						}
						?>
						<li>
							<?php if ($puedeEliminar) { ?>
								<a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta seguro de ejecutar esta acción?','question','estudiantes-eliminar.php?idE=<?= base64_encode($resultado["mat_id"]); ?>&idU=<?= base64_encode($resultado["mat_id_usuario"]); ?>')">Eliminar</a>
							<?php } else { ?>
								<span style="display: block; padding: 3px 20px; color: #999; opacity: 0.5; cursor: not-allowed;" title="<?= htmlspecialchars($tooltipEliminar, ENT_QUOTES); ?>">Eliminar</span>
							<?php } ?>
						</li>

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

					<?php if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA) && !empty($resultado['mat_codigo_tesoreria'])) { ?>
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
					<?php if ($permisoAdjuntarDocumento && !empty($resultado['mat_id_usuario']) && !empty($resultado['mat_id'])) { ?>
						<li><a href="matriculas-adjuntar-documentos.php?id=<?= base64_encode($resultado['mat_id_usuario']); ?>&idMatricula=<?= base64_encode($resultado['mat_id']); ?>"><?=$frases[434][$datosUsuarioActual['uss_idioma']];?></a></li>
					<?php } ?>
				</ul>
			</div>

		</td>
	</tr>
	<tr class="expandable-row" id="expand-<?= $resultado['mat_id']; ?>" style="display: none;">
		<td colspan="10" style="background-color: #f8f9fa; padding: 20px;">
			<div class="detalle-estudiante" data-id="<?= $resultado['mat_id']; ?>" data-loaded="0">
				<div class="text-center text-muted">
					<i class="fa fa-spinner fa-spin"></i> Cargando detalles...
				</div>
			</div>
		</td>
	</tr>
<?php
	$contReg++;
}

// Consultar opciones generales para los selects del modal
$opcionesTipoDocumento = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=1 ORDER BY ogen_nombre");
$opcionesGenero = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=4 ORDER BY ogen_nombre");
$opcionesEstrato = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=3 ORDER BY CAST(ogen_nombre AS UNSIGNED)");
$opcionesTipoSangre = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=14 ORDER BY ogen_nombre");
$opcionesTipoEstudiante = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=5 ORDER BY ogen_nombre");
$opcionesReligion = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=8 ORDER BY ogen_nombre");

// Consultar ciudades para los selects de lugar de expedición y lugar de nacimiento
$catalogoCiudades = [];
try {
	$consultaCiudades = mysqli_query(
		$conexion,
		"SELECT ciu_id, TRIM(ciu_codigo) AS ciu_codigo, ciu_nombre, dep_nombre 
		 FROM {$baseDatosServicios}.localidad_ciudades
		 INNER JOIN {$baseDatosServicios}.localidad_departamentos ON dep_id = ciu_departamento
		 ORDER BY ciu_nombre"
	);
	if ($consultaCiudades) {
		while ($ciudad = mysqli_fetch_assoc($consultaCiudades)) {
			$catalogoCiudades[] = $ciudad;
		}
	}
} catch (Exception $e) {
	// Si hay error, dejar el array vacío
	$catalogoCiudades = [];
}
?>

<!-- Modal de Edición Rápida -->
<div class="modal fade" id="modalEdicionRapida" tabindex="-1" role="dialog" aria-labelledby="modalEdicionRapidaLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="modalEdicionRapidaLabel">
					<i class="fa fa-user-edit"></i> Edición Rápida del Estudiante
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
				<form id="formEdicionRapida">
					<input type="hidden" id="mat_id_modal" name="mat_id">
					
					<!-- Información de Identificación -->
					<div class="row mb-3">
						<div class="col-md-12">
							<h6 class="text-primary mb-3"><i class="fa fa-id-card"></i> Información de Identificación</h6>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="tipo_documento_modal">Tipo de Documento</label>
								<select class="form-control" id="tipo_documento_modal" name="tipo_documento">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesTipoDocumento, 0);
									while($opcion = mysqli_fetch_array($opcionesTipoDocumento, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="documento_modal">Número de Documento</label>
								<input type="text" class="form-control" id="documento_modal" name="documento" readonly style="background-color: #e9ecef;">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="lugar_expedicion_modal">Lugar de Expedición</label>
								<select class="form-control select2" id="lugar_expedicion_modal" name="lugar_expedicion" style="width: 100%;">
									<option value="">Seleccione una opción</option>
									<?php foreach($catalogoCiudades as $ciudad){ ?>
										<option value="<?=$ciudad['ciu_id'];?>"><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
									<?php }?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="matricula_modal">Número de Matrícula</label>
								<input type="text" class="form-control" id="matricula_modal" name="matricula" readonly style="background-color: #e9ecef;">
							</div>
						</div>
					</div>
					
					<!-- Información Personal -->
					<div class="row mb-3">
						<div class="col-md-12">
							<h6 class="text-primary mb-3"><i class="fa fa-user"></i> Información Personal</h6>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="primer_nombre_modal">Primer Nombre *</label>
								<input type="text" class="form-control" id="primer_nombre_modal" name="primer_nombre" required maxlength="50">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="segundo_nombre_modal">Segundo Nombre</label>
								<input type="text" class="form-control" id="segundo_nombre_modal" name="segundo_nombre" maxlength="50">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="primer_apellido_modal">Primer Apellido *</label>
								<input type="text" class="form-control" id="primer_apellido_modal" name="primer_apellido" required maxlength="50">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="segundo_apellido_modal">Segundo Apellido</label>
								<input type="text" class="form-control" id="segundo_apellido_modal" name="segundo_apellido" maxlength="50">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group" id="fNacModalGroup">
								<label for="fecha_nacimiento_modal">Fecha de Nacimiento</label>
								<input type="date" class="form-control" id="fecha_nacimiento_modal" name="fecha_nacimiento" max="<?=date('Y-m-d', strtotime('-1 year'));?>">
								<small id="fNacModalError" class="text-danger" style="display:none;">La fecha de nacimiento no puede ser futura ni menor de 1 año.</small>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="lugar_nacimiento_modal">Lugar de Nacimiento</label>
								<select class="form-control select2" id="lugar_nacimiento_modal" name="lugar_nacimiento" style="width: 100%;">
									<option value="">Seleccione una opción</option>
									<?php foreach($catalogoCiudades as $ciudad){ ?>
										<option value="<?=$ciudad['ciu_id'];?>"><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
									<?php }?>
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="genero_modal">Género</label>
								<select class="form-control" id="genero_modal" name="genero">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesGenero, 0);
									while($opcion = mysqli_fetch_array($opcionesGenero, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="religion_modal">Religión</label>
								<select class="form-control" id="religion_modal" name="religion">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesReligion, 0);
									while($opcion = mysqli_fetch_array($opcionesReligion, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
					
					<!-- Información de Contacto -->
					<div class="row mb-3">
						<div class="col-md-12">
							<h6 class="text-success mb-3"><i class="fa fa-home"></i> Información de Contacto</h6>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="direccion_modal">Dirección</label>
								<textarea class="form-control" id="direccion_modal" name="direccion" rows="2" maxlength="200"></textarea>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="barrio_modal">Barrio</label>
								<input type="text" class="form-control" id="barrio_modal" name="barrio" maxlength="100">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="ciudad_residencia_modal">Ciudad de Residencia</label>
								<input type="text" class="form-control" id="ciudad_residencia_modal" name="ciudad_residencia" maxlength="100">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="celular_modal">Celular</label>
								<input type="tel" class="form-control" id="celular_modal" name="celular" maxlength="15">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="celular2_modal">Celular 2</label>
								<input type="tel" class="form-control" id="celular2_modal" name="celular2" maxlength="15">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="telefono_modal">Teléfono</label>
								<input type="tel" class="form-control" id="telefono_modal" name="telefono" maxlength="15">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="email_modal">Email</label>
								<input type="email" class="form-control" id="email_modal" name="email" maxlength="100">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="estrato_modal">Estrato</label>
								<select class="form-control" id="estrato_modal" name="estrato">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesEstrato, 0);
									while($opcion = mysqli_fetch_array($opcionesEstrato, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
					
					<!-- Información Académica -->
					<div class="row mb-3">
						<div class="col-md-12">
							<h6 class="text-info mb-3"><i class="fa fa-graduation-cap"></i> Información Académica</h6>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="grado_modal">Grado</label>
								<input type="text" class="form-control" id="grado_modal" name="grado" readonly style="background-color: #e9ecef;">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="grupo_modal">Grupo</label>
								<input type="text" class="form-control" id="grupo_modal" name="grupo" readonly style="background-color: #e9ecef;">
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="tipo_estudiante_modal">Tipo de Estudiante</label>
								<select class="form-control" id="tipo_estudiante_modal" name="tipo_estudiante">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesTipoEstudiante, 0);
									while($opcion = mysqli_fetch_array($opcionesTipoEstudiante, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="inclusion_modal">Inclusión</label>
								<select class="form-control" id="inclusion_modal" name="inclusion">
									<option value="0">No</option>
									<option value="1">Sí</option>
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label for="estado_matricula_modal">Estado de Matrícula</label>
								<input type="text" class="form-control" id="estado_matricula_modal" name="estado_matricula" readonly style="background-color: #e9ecef;">
							</div>
						</div>
					</div>
					
					<!-- Información Médica -->
					<div class="row mb-3">
						<div class="col-md-12">
							<h6 class="text-warning mb-3"><i class="fa fa-heart"></i> Información Médica</h6>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="tipo_sangre_modal">Grupo Sanguíneo</label>
								<select class="form-control" id="tipo_sangre_modal" name="tipo_sangre">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesTipoSangre, 0);
									while($opcion = mysqli_fetch_array($opcionesTipoSangre, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label for="eps_modal">EPS</label>
								<input type="text" class="form-control" id="eps_modal" name="eps" maxlength="100">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<i class="fa fa-times"></i> Cancelar
				</button>
				<button type="button" class="btn btn-primary" id="btnGuardarRapido">
					<i class="fa fa-save"></i> Guardar Cambios
				</button>
			</div>
		</div>
	</div>
</div>

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

	// Función para abrir modal de edición rápida
	function abrirModalEdicionRapida(matId) {
		console.log('Abriendo modal para estudiante ID:', matId);
		console.log('Modal existe:', $('#modalEdicionRapida').length > 0);
		
		// Mostrar indicador de carga
		$('#btnGuardarRapido').html('<i class="fa fa-spinner fa-spin"></i> Cargando...').prop('disabled', true);
		
		// Limpiar formulario
		$('#formEdicionRapida')[0].reset();
		
		// Obtener datos del estudiante
		console.log('Enviando petición AJAX para obtener datos...');
		$.ajax({
			url: 'ajax-obtener-datos-estudiante.php',
			method: 'POST',
			data: { mat_id: matId },
			dataType: 'json',
			success: function(response) {
				console.log('Respuesta recibida:', response);
				if (response.success) {
					var estudiante = response.data;
					
					// Inicializar select2 para los campos de ciudades ANTES de establecer valores
					if (!$('#lugar_expedicion_modal').hasClass('select2-hidden-accessible')) {
						$('#lugar_expedicion_modal').select2({
							dropdownParent: $('#modalEdicionRapida'),
							language: {
								noResults: function() {
									return "No se encontraron resultados";
								}
							}
						});
					}
					if (!$('#lugar_nacimiento_modal').hasClass('select2-hidden-accessible')) {
						$('#lugar_nacimiento_modal').select2({
							dropdownParent: $('#modalEdicionRapida'),
							language: {
								noResults: function() {
									return "No se encontraron resultados";
								}
							}
						});
					}
					
					// Llenar formulario - Información de Identificación
					$('#mat_id_modal').val(matId);
					$('#tipo_documento_modal').val(estudiante.mat_tipo_documento || '');
					$('#documento_modal').val(estudiante.mat_documento || '');
					$('#lugar_expedicion_modal').val(estudiante.mat_lugar_expedicion || '').trigger('change');
					$('#matricula_modal').val(estudiante.mat_numero_matricula || estudiante.mat_matricula || '');
					
					// Información Personal
					$('#primer_nombre_modal').val(estudiante.mat_nombres || '');
					$('#segundo_nombre_modal').val(estudiante.mat_nombre2 || '');
					$('#primer_apellido_modal').val(estudiante.mat_primer_apellido || '');
					$('#segundo_apellido_modal').val(estudiante.mat_segundo_apellido || '');
					$('#fecha_nacimiento_modal').val(estudiante.mat_fecha_nacimiento || '');
					$('#lugar_nacimiento_modal').val(estudiante.mat_lugar_nacimiento || '').trigger('change');
					$('#genero_modal').val(estudiante.mat_genero || '');
					$('#religion_modal').val(estudiante.mat_religion || '');
					
					// Información de Contacto
					$('#direccion_modal').val(estudiante.mat_direccion || '');
					$('#barrio_modal').val(estudiante.mat_barrio || '');
					$('#ciudad_residencia_modal').val(estudiante.mat_ciudad_residencia || '');
					$('#celular_modal').val(estudiante.mat_celular || '');
					$('#celular2_modal').val(estudiante.mat_celular2 || '');
					$('#telefono_modal').val(estudiante.mat_telefono || '');
					$('#email_modal').val(estudiante.mat_email || '');
					$('#estrato_modal').val(estudiante.mat_estrato || '');
					
					// Información Académica
					$('#grado_modal').val(estudiante.gra_nombre || '');
					$('#grupo_modal').val(estudiante.gru_nombre || '');
					$('#tipo_estudiante_modal').val(estudiante.mat_tipo || '');
					$('#inclusion_modal').val(estudiante.mat_inclusion || '0');
					$('#estado_matricula_modal').val(estudiante.estado_matricula_nombre || '');
					
					// Información Médica
					$('#tipo_sangre_modal').val(estudiante.mat_tipo_sangre || '');
					$('#eps_modal').val(estudiante.mat_eps || '');
					
					// Mostrar modal
					$('#modalEdicionRapida').modal('show');
					
					// Restaurar botón
					$('#btnGuardarRapido').html('<i class="fa fa-save"></i> Guardar Cambios').prop('disabled', false);
					
					// Enfocar primer campo
					$('#primer_nombre_modal').focus();
				} else {
					alert('❌ Error al cargar datos del estudiante: ' + (response.message || 'Error desconocido'));
					$('#btnGuardarRapido').html('<i class="fa fa-save"></i> Guardar Cambios').prop('disabled', false);
				}
			},
			error: function(xhr, status, error) {
				console.error('Error al cargar datos del estudiante:', error);
				alert('❌ Error de conexión. Intente nuevamente.');
				$('#btnGuardarRapido').html('<i class="fa fa-save"></i> Guardar Cambios').prop('disabled', false);
			}
		});
	}

	// Función para guardar cambios del estudiante
	function guardarCambiosRapidos() {
		
		// Validar formulario
		if (!$('#formEdicionRapida')[0].checkValidity()) {
			$('#formEdicionRapida')[0].reportValidity();
			return;
		}
		
		// Validar campos obligatorios
		var primerNombre = $('#primer_nombre_modal').val().trim();
		var primerApellido = $('#primer_apellido_modal').val().trim();
		
		
		if (!primerNombre || !primerApellido) {
			alert('El primer nombre y primer apellido son obligatorios.');
			return;
		}
		
		// Validar fecha de nacimiento
		var fechaNacimiento = $('#fecha_nacimiento_modal').val();
		if (fechaNacimiento) {
			var today = new Date();
			var maxDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
			var selectedDate = new Date(fechaNacimiento);
			
			if (selectedDate > maxDate) {
				$('#fNacModalError').text('La fecha de nacimiento no puede ser futura ni menor de 1 año.').show();
				$('#fNacModalGroup').addClass('has-error');
				$('#fecha_nacimiento_modal').focus();
				return;
			}
		}
		
		// Mostrar indicador de carga
		var saveBtn = $('#btnGuardarRapido');
		var originalText = saveBtn.html();
		saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
		
		// Preparar datos del formulario
		var formData = {
			mat_id: $('#mat_id_modal').val(),
			tipo_documento: $('#tipo_documento_modal').val(),
			lugar_expedicion: $('#lugar_expedicion_modal').val() || '',
			primer_nombre: primerNombre,
			segundo_nombre: $('#segundo_nombre_modal').val().trim(),
			primer_apellido: primerApellido,
			segundo_apellido: $('#segundo_apellido_modal').val().trim(),
			fecha_nacimiento: $('#fecha_nacimiento_modal').val(),
			lugar_nacimiento: $('#lugar_nacimiento_modal').val() || '',
			genero: $('#genero_modal').val(),
			religion: $('#religion_modal').val(),
			direccion: $('#direccion_modal').val().trim(),
			barrio: $('#barrio_modal').val().trim(),
			ciudad_residencia: $('#ciudad_residencia_modal').val().trim(),
			celular: $('#celular_modal').val().trim(),
			celular2: $('#celular2_modal').val().trim(),
			telefono: $('#telefono_modal').val().trim(),
			email: $('#email_modal').val().trim(),
			estrato: $('#estrato_modal').val(),
			tipo_estudiante: $('#tipo_estudiante_modal').val(),
			inclusion: $('#inclusion_modal').val(),
			tipo_sangre: $('#tipo_sangre_modal').val(),
			eps: $('#eps_modal').val().trim()
		};
		
		
		// Enviar datos por AJAX
		$.ajax({
			url: 'ajax-actualizar-estudiante-rapido.php',
			method: 'POST',
			data: formData,
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					// Cerrar modal
					$('#modalEdicionRapida').modal('hide');
					
					// Mostrar mensaje de éxito con toast
					$.toast({
						heading: 'Éxito',
						text: response.message || 'Datos del estudiante actualizados correctamente',
						position: 'top-right',
						loaderBg: '#26c281',
						icon: 'success',
						hideAfter: 3000
					});
					
					// Actualizar solo la fila del estudiante sin recargar la página
					actualizarFilaEstudiante($('#mat_id_modal').val());
				} else {
					// Mostrar mensaje de error
					$.toast({
						heading: 'Error',
						text: response.message || 'Error desconocido',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 5000
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Error en la petición:', error);
				alert('❌ Error de conexión. Intente nuevamente.');
			},
			complete: function() {
				saveBtn.html(originalText).prop('disabled', false);
			}
		});
	}

	// Función para actualizar solo la fila del estudiante sin recargar la página
	function actualizarFilaEstudiante(matId) {
		// Obtener los datos actualizados del estudiante
		$.ajax({
			url: 'ajax-obtener-datos-estudiante.php',
			method: 'POST',
			data: { mat_id: matId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var estudiante = response.data;
					var nombreCompleto = (estudiante.mat_primer_apellido || '') + ' ' + 
										(estudiante.mat_segundo_apellido || '') + ' ' + 
										(estudiante.mat_nombres || '') + ' ' + 
										(estudiante.mat_nombre2 || '');
					nombreCompleto = nombreCompleto.trim();
					
					// Buscar la fila en la tabla por el documento del estudiante
					// La estructura de la tabla tiene el documento en una celda específica
					var $fila = null;
					
					// Intentar encontrar la fila por el documento
					$('#example1 tbody tr, table tbody tr').each(function() {
						var $tr = $(this);
						// Buscar en la segunda columna (índice 1) que contiene el documento
						var $docCell = $tr.find('td').eq(1);
						if ($docCell.length > 0 && $docCell.text().trim() === (estudiante.mat_documento || '').trim()) {
							$fila = $tr;
							return false; // Salir del each
						}
					});
					
					if ($fila && $fila.length > 0) {
						// Actualizar el nombre en la celda correspondiente (tercera columna)
						var $nombreCell = $fila.find('td').eq(2);
						if ($nombreCell.length > 0) {
							$nombreCell.find('.editable-name').text(nombreCompleto);
						}
					}
					
					// Si hay DataTables, redibujar la tabla manteniendo la página actual
					if ($.fn.DataTable.isDataTable('#example1')) {
						var table = $('#example1').DataTable();
						// Obtener la página actual antes de redibujar
						var currentPage = table.page();
						table.draw(false); // false para mantener la página actual y no perder la posición
					}
				}
			},
			error: function() {
				console.error('Error al actualizar fila del estudiante');
			}
		});
	}

	$(document).ready(function() {
		// Event listener para el botón de guardar en el modal
		$('#btnGuardarRapido').on('click', function() {
			guardarCambiosRapidos();
		});
		
		// Destruir select2 cuando se cierra el modal para evitar problemas de inicialización múltiple
		$('#modalEdicionRapida').on('hidden.bs.modal', function() {
			if ($('#lugar_expedicion_modal').hasClass('select2-hidden-accessible')) {
				$('#lugar_expedicion_modal').select2('destroy');
			}
			if ($('#lugar_nacimiento_modal').hasClass('select2-hidden-accessible')) {
				$('#lugar_nacimiento_modal').select2('destroy');
			}
		});
		
		// Validación en tiempo real de fecha de nacimiento en el modal
		$('#fecha_nacimiento_modal').on('change', function() {
			var fechaNacimiento = $(this).val();
			var $error = $('#fNacModalError');
			var $group = $('#fNacModalGroup');
			
			if (fechaNacimiento) {
				var today = new Date();
				var maxDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
				var selectedDate = new Date(fechaNacimiento);
				
				if (selectedDate > maxDate) {
					$error.text('La fecha de nacimiento no puede ser futura ni menor de 1 año.').show();
					$group.addClass('has-error');
					$(this).addClass('is-invalid');
				} else {
					$error.hide();
					$group.removeClass('has-error');
					$(this).removeClass('is-invalid');
				}
			} else {
				$error.hide();
				$group.removeClass('has-error');
				$(this).removeClass('is-invalid');
			}
		});
		
		// Use event delegation for dynamically loaded content
		$(document).on('click', '.expand-btn', function() {
			var id = $(this).data('id');
			var row = $('#expand-' + id);
			var icon = $(this).find('i');
			var button = $(this);

			if (row.is(':visible')) {
				// Collapse with animation
				row.slideUp(300, function() {
					icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
					button.removeClass('text-primary').addClass('text-secondary');
				});
			} else {
				var contenedor = row.find('.detalle-estudiante');
				if (contenedor.data('loaded') === 0) {
					cargarDetalleEstudiante(id, contenedor);
				}
				// Expand with animation
				row.slideDown(300, function() {
					icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
					button.removeClass('text-secondary').addClass('text-primary');
				});
			}
		});

		function cargarDetalleEstudiante(id, contenedor) {
			contenedor.html('<div class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Cargando detalles...</div>');
			$.ajax({
				url: 'ajax-detalle-estudiante.php',
				type: 'POST',
				dataType: 'json',
				data: { mat_id: id },
				success: function(resp) {
					if (resp.success) {
						contenedor.html(resp.html);
						contenedor.data('loaded', 1).attr('data-loaded', '1');
					} else {
						contenedor.html('<div class="alert alert-danger mb-0"><i class="fa fa-exclamation-triangle"></i> ' + (resp.message || 'No se pudo cargar la información.') + '</div>');
					}
				},
				error: function() {
					contenedor.html('<div class="alert alert-danger mb-0"><i class="fa fa-plug"></i> Error de conexión al cargar detalles.</div>');
				}
			});
		}
		
		// Event listener para editar nombre
		$(document).on('click', '.student-name-display', function() {
			var matId = $(this).closest('.student-name-container').data('id');
			editarNombre(matId);
		});
		
		// Permitir cancelar con Escape
		$(document).on('keydown', '.student-name-edit input', function(e) {
			if (e.key === 'Escape') {
				var matId = $(this).attr('id').split('_')[1];
				cancelarEdicionNombre(matId);
			}
		});
		
		// Permitir guardar con Enter
		$(document).on('keydown', '.student-name-edit input', function(e) {
			if (e.key === 'Enter') {
				var matId = $(this).attr('id').split('_')[1];
				guardarNombre(matId);
			}
		});
	});
</script>
