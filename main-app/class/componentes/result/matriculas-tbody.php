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

/* Estilos para edición inline del nombre */
.student-name-display:hover {
	background-color: #f8f9fa;
	border-radius: 4px;
	padding: 2px 4px;
	transition: background-color 0.2s ease;
}

.student-name-display:hover .fa-edit {
	color: #007bff !important;
}

.student-name-edit .form-control-sm {
	font-size: 0.875rem;
	padding: 0.25rem 0.5rem;
}

.student-name-edit .btn-sm {
	font-size: 0.75rem;
	padding: 0.25rem 0.5rem;
}

.editable-name {
	transition: color 0.2s ease;
}

.student-name-display:hover .editable-name {
	color: #007bff;
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

	$acudiente       = $resultado["mat_acudiente"];
	$nombreAcudiente = UsuariosPadre::nombreCompletoDelUsuario($resultado);
	$idAcudiente     = $acudiente;


?>
	<tr id="EST<?= $resultado['mat_id']; ?>" <?= $bgColor; ?>>
		<td><button class="btn btn-sm btn-link text-secondary expand-btn" data-id="<?= $resultado['mat_id']; ?>" title="Ver detalles"><i class="fa fa-chevron-right"></i></button></td>
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
			if ($permisoCambiarEstado) {
				$cambiarEstado = "onclick='cambiarEstadoMatricula(" . $dataParaJavascript . ")'";
				$cursorStyle = "cursor: pointer;";
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
				  <?= $cambiarEstado; ?>>
				<?= $estadosMatriculasEstudiantes[$resultado['mat_estado_matricula']]; ?>
			</span>
			<?php } ?>
		</td>
		<td><?= $resultado['mat_documento']; ?></td>
		<?php $nombre = Estudiantes::NombreCompletoDelEstudiante($resultado); ?>

		<td <?= $color; ?>>
			<div class="student-name-container" style="cursor: pointer;" 
				 onclick="abrirModalEdicionRapida('<?= htmlspecialchars($resultado['mat_id'], ENT_QUOTES); ?>')"
				 title="Hacer clic para editar datos del estudiante">
				<?= $marcaMediaTecnica; ?><span class="editable-name"><?= $nombre; ?></span>
				<i class="fa fa-edit text-muted ml-1" style="font-size: 0.8em;"></i>
			</div>
		</td>
		<td><?= strtoupper($resultado['gra_nombre'] . " " . $resultado['gru_nombre']); ?></td>
		<td><?= $resultado['uss_usuario']; ?></td>
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
					<?php if ($permisoAdjuntarDocumento) { ?>
						<li><a href="matriculas-adjuntar-documentos.php?id=<?= base64_encode($resultado['mat_id_usuario']); ?>&idMatricula=<?= base64_encode($resultado['mat_id']); ?>"><?=$frases[434][$datosUsuarioActual['uss_idioma']];?></a></li>
					<?php } ?>
				</ul>
			</div>

		</td>
	</tr>
	<tr class="expandable-row" id="expand-<?= $resultado['mat_id']; ?>" style="display: none;">
		<td colspan="9" style="background-color: #f8f9fa; padding: 20px;">
			<div class="row">
				<!-- Foto del Estudiante -->
				<div class="col-md-2 text-center">
					<div class="student-photo-container">
						<?php if (!empty($fotoEstudiante)) { ?>
							<img src="<?= $fotoEstudiante; ?>" alt="Foto del estudiante" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
						<?php } else { ?>
							<div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; background-color: #f8f9fa;">
								<i class="fa fa-user fa-3x text-muted"></i>
							</div>
						<?php } ?>
						<p class="mt-2 mb-0"><small class="text-muted">Foto del Estudiante</small></p>
					</div>
				</div>
				
				<!-- Información Personal del Estudiante -->
				<div class="col-md-4">
					<h6 class="text-primary mb-3"><i class="fa fa-user"></i> Información Personal</h6>
					<div class="row">
						<div class="col-6">
							<p class="mb-2"><strong>Documento:</strong><br><span class="text-muted"><?= $resultado['mat_documento'] ?? 'No disponible'; ?></span></p>
							<p class="mb-2"><strong>Tipo Doc:</strong><br><span class="text-muted"><?= $resultado['tipo_doc_nombre'] ?? 'No disponible'; ?></span></p>
							<p class="mb-2"><strong>Fecha Nacimiento:</strong><br><span class="text-muted"><?= $resultado['mat_fecha_nacimiento'] ?? 'No disponible'; ?></span></p>
							<p class="mb-2"><strong>Género:</strong><br><span class="text-muted"><?= $resultado['genero_nombre'] ?? 'No disponible'; ?></span></p>
						</div>
						<div class="col-6">
							<p class="mb-2"><strong>Dirección:</strong><br><span class="text-muted"><?= $resultado['mat_direccion'] ?? 'No disponible'; ?></span></p>
							<p class="mb-2"><strong>Barrio:</strong><br><span class="text-muted"><?= $resultado['mat_barrio'] ?? 'No disponible'; ?></span></p>
							<p class="mb-2"><strong>Celular:</strong><br><span class="text-muted"><?= $resultado['mat_celular'] ?? 'No disponible'; ?></span></p>
							<p class="mb-2"><strong>Email:</strong><br><span class="text-muted"><?= $resultado['mat_email'] ?? 'No disponible'; ?></span></p>
						</div>
					</div>
					<div class="row mt-2">
						<div class="col-6">
							<p class="mb-2"><strong>Estrato:</strong><br><span class="text-muted"><?= $resultado['estrato_nombre'] ?? 'No disponible'; ?></span></p>
						</div>
						<div class="col-6">
							<p class="mb-2"><strong>EPS:</strong><br><span class="text-muted"><?= $resultado['mat_eps'] ?? 'No disponible'; ?></span></p>
						</div>
					</div>
				</div>
				
				<!-- Información Académica -->
				<div class="col-md-3">
					<h6 class="text-success mb-3"><i class="fa fa-graduation-cap"></i> Información Académica</h6>
					<p class="mb-2"><strong>Grado:</strong><br><span class="text-muted"><?= $resultado['gra_nombre'] ?? 'No disponible'; ?></span></p>
					<p class="mb-2"><strong>Grupo:</strong><br><span class="text-muted"><?= $resultado['gru_nombre'] ?? 'No disponible'; ?></span></p>
					<p class="mb-2"><strong>Estado Matrícula:</strong><br>
						<span class="<?= $estadosEtiquetasMatriculas[$resultado['mat_estado_matricula']] ?? 'badge badge-secondary'; ?>">
							<?= $estadosMatriculasEstudiantes[$resultado['mat_estado_matricula']] ?? 'No disponible'; ?>
						</span>
					</p>
					<p class="mb-2"><strong>Fecha Matrícula:</strong><br><span class="text-muted"><?= $resultado['mat_fecha'] ?? 'No disponible'; ?></span></p>
					<p class="mb-2"><strong>Usuario:</strong><br><span class="text-muted"><?= $resultado['uss_usuario'] ?? 'No disponible'; ?></span></p>
					<?php if ($resultado['mat_inclusion'] == 1) { ?>
						<p class="mb-2"><span class="badge badge-info">Estudiante con Inclusión</span></p>
					<?php } ?>
				</div>
				
				<!-- Información del Acudiente -->
				<div class="col-md-3">
					<h6 class="text-warning mb-3"><i class="fa fa-users"></i> Información del Acudiente</h6>
					<?php if (!empty($idAcudiente) && !empty($nombreAcudiente)) { ?>
						<p class="mb-2"><strong>Nombre:</strong><br>
							<?php if ($permisoEditarUsuario) { ?>
								<a href="usuarios-editar.php?id=<?= base64_encode($idAcudiente); ?>" class="text-primary">
									<?= $nombreAcudiente; ?>
								</a>
							<?php } else { ?>
								<span class="text-muted"><?= $nombreAcudiente; ?></span>
							<?php } ?>
						</p>
						<p class="mb-2"><strong>ID Acudiente:</strong><br><span class="text-muted"><?= $idAcudiente; ?></span></p>
						<div class="mt-3">
							<a href="mensajes-redactar.php?para=<?= base64_encode($idAcudiente); ?>" class="btn btn-sm btn-outline-primary">
								<i class="fa fa-envelope"></i> Enviar Mensaje
							</a>
						</div>
					<?php } else { ?>
						<p class="text-muted"><em>No hay acudiente registrado</em></p>
					<?php } ?>
				</div>
			</div>
			
			<!-- Información Adicional -->
			<div class="row mt-3">
				<div class="col-12">
					<hr>
					<h6 class="text-info mb-3"><i class="fa fa-info-circle"></i> Información Adicional</h6>
			<div class="row">
						<div class="col-md-3">
							<p class="mb-1"><strong>Grupo Sanguíneo:</strong><br><span class="text-muted"><?= $resultado['tipo_sangre_nombre'] ?? 'No disponible'; ?></span></p>
						</div>
						<div class="col-md-3">
							<p class="mb-1"><strong>Teléfono:</strong><br><span class="text-muted"><?= $resultado['mat_telefono'] ?? 'No disponible'; ?></span></p>
						</div>
						<div class="col-md-3">
							<p class="mb-1"><strong>Celular 2:</strong><br><span class="text-muted"><?= $resultado['mat_celular2'] ?? 'No disponible'; ?></span></p>
						</div>
						<div class="col-md-3">
							<p class="mb-1"><strong>Ciudad Residencia:</strong><br><span class="text-muted"><?= $resultado['mat_ciudad_residencia'] ?? 'No disponible'; ?></span></p>
						</div>
					</div>
					<?php if (!empty($resultado['mat_matricula'])) { ?>
						<div class="row mt-2">
							<div class="col-md-3">
								<p class="mb-1"><strong>Número Matrícula:</strong><br><span class="text-muted"><?= $resultado['mat_matricula']; ?></span></p>
							</div>
							<div class="col-md-3">
								<p class="mb-1"><strong>Código Tesorería:</strong><br><span class="text-muted"><?= $resultado['mat_codigo_tesoreria'] ?? 'No disponible'; ?></span></p>
							</div>
							<div class="col-md-3">
								<p class="mb-1"><strong>Folio:</strong><br><span class="text-muted"><?= $resultado['mat_folio'] ?? 'No disponible'; ?></span></p>
							</div>
							<div class="col-md-3">
								<p class="mb-1"><strong>Valor Matrícula:</strong><br><span class="text-muted">$<?= number_format($resultado['mat_valor_matricula'] ?? 0, 0, ',', '.'); ?></span></p>
							</div>
				</div>
					<?php } ?>
				</div>
			</div>
		</td>
	</tr>
<?php
	$contReg++;
}

// Consultar opciones generales para los selects del modal
$opcionesGenero = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=4 ORDER BY ogen_nombre");
$opcionesEstrato = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=3 ORDER BY CAST(ogen_nombre AS UNSIGNED)");
$opcionesTipoSangre = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre FROM ".BD_ADMIN.".opciones_generales WHERE ogen_grupo=14 ORDER BY ogen_nombre");
?>

<!-- Modal de Edición Rápida -->
<div class="modal fade" id="modalEdicionRapida" tabindex="-1" role="dialog" aria-labelledby="modalEdicionRapidaLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="modalEdicionRapidaLabel">
					<i class="fa fa-user-edit"></i> Edición Rápida del Estudiante
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="formEdicionRapida">
					<input type="hidden" id="mat_id_modal" name="mat_id">
					
					<!-- Información Personal -->
					<div class="row">
						<div class="col-md-6">
							<h6 class="text-primary mb-3"><i class="fa fa-user"></i> Información Personal</h6>
							
							<div class="form-group">
								<label for="primer_nombre_modal">Primer Nombre *</label>
								<input type="text" class="form-control" id="primer_nombre_modal" name="primer_nombre" required maxlength="50">
							</div>
							
							<div class="form-group">
								<label for="segundo_nombre_modal">Segundo Nombre</label>
								<input type="text" class="form-control" id="segundo_nombre_modal" name="segundo_nombre" maxlength="50">
							</div>
							
							<div class="form-group">
								<label for="primer_apellido_modal">Primer Apellido *</label>
								<input type="text" class="form-control" id="primer_apellido_modal" name="primer_apellido" required maxlength="50">
							</div>
							
							<div class="form-group">
								<label for="segundo_apellido_modal">Segundo Apellido</label>
								<input type="text" class="form-control" id="segundo_apellido_modal" name="segundo_apellido" maxlength="50">
							</div>
							
							<div class="form-group">
								<label for="fecha_nacimiento_modal">Fecha de Nacimiento</label>
								<input type="date" class="form-control" id="fecha_nacimiento_modal" name="fecha_nacimiento">
							</div>
							
							<div class="form-group">
								<label for="genero_modal">Género</label>
								<select class="form-control" id="genero_modal" name="genero">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesGenero, 0); // Reiniciar el puntero
									while($opcion = mysqli_fetch_array($opcionesGenero, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-md-6">
							<h6 class="text-success mb-3"><i class="fa fa-home"></i> Información de Contacto</h6>
							
							<div class="form-group">
								<label for="direccion_modal">Dirección</label>
								<textarea class="form-control" id="direccion_modal" name="direccion" rows="2" maxlength="200"></textarea>
							</div>
							
							<div class="form-group">
								<label for="barrio_modal">Barrio</label>
								<input type="text" class="form-control" id="barrio_modal" name="barrio" maxlength="100">
							</div>
							
							<div class="form-group">
								<label for="celular_modal">Celular</label>
								<input type="tel" class="form-control" id="celular_modal" name="celular" maxlength="15">
							</div>
							
							<div class="form-group">
								<label for="telefono_modal">Teléfono</label>
								<input type="tel" class="form-control" id="telefono_modal" name="telefono" maxlength="15">
							</div>
							
							<div class="form-group">
								<label for="email_modal">Email</label>
								<input type="email" class="form-control" id="email_modal" name="email" maxlength="100">
							</div>
							
							<div class="form-group">
								<label for="estrato_modal">Estrato</label>
								<select class="form-control" id="estrato_modal" name="estrato">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesEstrato, 0); // Reiniciar el puntero
									while($opcion = mysqli_fetch_array($opcionesEstrato, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
					
					<!-- Información Académica -->
					<div class="row mt-3">
						<div class="col-md-6">
							<h6 class="text-info mb-3"><i class="fa fa-graduation-cap"></i> Información Académica</h6>
							
							<div class="form-group">
								<label for="grado_modal">Grado</label>
								<input type="text" class="form-control" id="grado_modal" name="grado" readonly>
							</div>
							
							<div class="form-group">
								<label for="grupo_modal">Grupo</label>
								<input type="text" class="form-control" id="grupo_modal" name="grupo" readonly>
							</div>
						</div>
						
						<div class="col-md-6">
							<h6 class="text-warning mb-3"><i class="fa fa-heart"></i> Información Médica</h6>
							
							<div class="form-group">
								<label for="tipo_sangre_modal">Grupo Sanguíneo</label>
								<select class="form-control" id="tipo_sangre_modal" name="tipo_sangre">
									<option value="">Seleccionar...</option>
									<?php
									mysqli_data_seek($opcionesTipoSangre, 0); // Reiniciar el puntero
									while($opcion = mysqli_fetch_array($opcionesTipoSangre, MYSQLI_BOTH)) {
										echo '<option value="'.$opcion['ogen_id'].'">'.$opcion['ogen_nombre'].'</option>';
									}
									?>
								</select>
							</div>
							
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
					
					// Llenar formulario
					$('#mat_id_modal').val(matId);
					$('#primer_nombre_modal').val(estudiante.mat_nombres || '');
					$('#segundo_nombre_modal').val(estudiante.mat_nombre2 || '');
					$('#primer_apellido_modal').val(estudiante.mat_primer_apellido || '');
					$('#segundo_apellido_modal').val(estudiante.mat_segundo_apellido || '');
					$('#fecha_nacimiento_modal').val(estudiante.mat_fecha_nacimiento || '');
					$('#genero_modal').val(estudiante.mat_genero || '');
					$('#direccion_modal').val(estudiante.mat_direccion || '');
					$('#barrio_modal').val(estudiante.mat_barrio || '');
					$('#celular_modal').val(estudiante.mat_celular || '');
					$('#telefono_modal').val(estudiante.mat_telefono || '');
					$('#email_modal').val(estudiante.mat_email || '');
					$('#estrato_modal').val(estudiante.mat_estrato || '');
					$('#grado_modal').val(estudiante.gra_nombre || '');
					$('#grupo_modal').val(estudiante.gru_nombre || '');
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
		
		// Mostrar indicador de carga
		var saveBtn = $('#btnGuardarRapido');
		var originalText = saveBtn.html();
		saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
		
		// Preparar datos del formulario
		var formData = {
			mat_id: $('#mat_id_modal').val(),
			primer_nombre: primerNombre,
			segundo_nombre: $('#segundo_nombre_modal').val().trim(),
			primer_apellido: primerApellido,
			segundo_apellido: $('#segundo_apellido_modal').val().trim(),
			fecha_nacimiento: $('#fecha_nacimiento_modal').val(),
			genero: $('#genero_modal').val(),
			direccion: $('#direccion_modal').val().trim(),
			barrio: $('#barrio_modal').val().trim(),
			celular: $('#celular_modal').val().trim(),
			telefono: $('#telefono_modal').val().trim(),
			email: $('#email_modal').val().trim(),
			estrato: $('#estrato_modal').val(),
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
					
					// Mostrar mensaje de éxito
					alert('✅ ' + (response.message || 'Datos del estudiante actualizados correctamente'));
					
					// Actualizar la tabla sin recargar la página
					actualizarTablaEstudiantes();
				} else {
					// Mostrar mensaje de error
					alert('❌ Error: ' + (response.message || 'Error desconocido'));
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

	// Función para actualizar la tabla de estudiantes sin recargar la página
	function actualizarTablaEstudiantes() {
		// Por ahora, simplemente recargar la página para evitar problemas
		// TODO: Implementar actualización AJAX más robusta
		setTimeout(function() {
			location.reload();
		}, 1000);
	}

	$(document).ready(function() {
		// Event listener para el botón de guardar en el modal
		$('#btnGuardarRapido').on('click', function() {
			guardarCambiosRapidos();
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
				// Expand with animation
				row.slideDown(300, function() {
					icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
					button.removeClass('text-secondary').addClass('text-primary');
				});
			}
		});
		
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
