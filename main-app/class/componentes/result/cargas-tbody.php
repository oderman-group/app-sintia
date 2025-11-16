<?php
if (!empty($data["dataTotal"])) {
	require_once("../Estudiantes.php");
	require_once("../Modulos.php");
	require_once("../Sysjobs.php");
	require_once("../Boletin.php");
}

if (!isset($opcionSINO) || !is_array($opcionSINO)) {
	$opcionSINO = [0 => 'NO', 1 => 'SI'];
}

$permisoReportesNotas  = Modulos::validarSubRol(['DT0238']);
$permisoedicion        = Modulos::validarSubRol(['DT0049', 'DT0148', 'DT0129']);
$permisoEditar         = Modulos::validarSubRol(['DT0049']);
$permisoEliminar       = Modulos::validarSubRol(['DT0148']);
$permisoAutologin      = Modulos::validarSubRol(['DT0129']);
$permisoHorarios       = Modulos::validarSubRol(['DT0041']);
$permisoResumen        = Modulos::validarSubRol(['DT0111']);
$permisoIndicadores    = Modulos::validarSubRol(['DT0034']);
$permisoPlanilla       = Modulos::validarSubRol(['DT0239']);
$permisoPlanillaNotas  = Modulos::validarSubRol(['DT0237']);
$permisoGenerarInforme = Modulos::validarSubRol(['DT0237']);
$permisoComportamiento = Modulos::validarSubRol(['DT0343']);

$normalizarSiNo = function ($valor) use ($opcionSINO) {
	if (isset($opcionSINO[$valor])) {
		return $opcionSINO[$valor];
	}

	$valorNormalizado = strtoupper(trim((string) $valor));
	if (in_array($valorNormalizado, ['1', 'SI', 'SÍ'], true)) {
		return $opcionSINO[1] ?? 'SI';
	}

	return $opcionSINO[0] ?? 'NO';
};

$contReg = 1;
foreach ($data["data"] as $resultado) {
	$cargaSP = $resultado['car_id'];
	$periodoSP = $resultado['car_periodo'];
	$marcaMediaTecnica = '';
	if ($resultado['gra_tipo'] == GRADO_INDIVIDUAL) {
		$cantidadEstudiantes = $resultado['cantidad_estudiantes_mt'] ?? 0;
		$marcaMediaTecnica = '<i class="fa fa-bookmark" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Media técnica"></i>';
	} else {
		$cantidadEstudiantes = $resultado['cantidad_estudiantes'] ?? 0;
	}

	$marcaDG = '';
	if ($resultado['car_director_grupo'] == 1) {
		$marcaDG = '<i class="fa fa-star text-info" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Director de grupo"></i> ';
	}

	$claseInactiva = '';

	if (isset($resultado['car_activa']) && $resultado['car_activa'] != 1) {
		$claseInactiva = 'class = "bg-secondary"';
	}
?>
	<tr <?=$claseInactiva;?>>
	   <td>
	   <button class="btn btn-sm btn-link text-secondary expand-btn"
		   data-id="<?=htmlspecialchars($resultado['car_id'], ENT_QUOTES, 'UTF-8');?>"
		   data-codigo="<?=htmlspecialchars($resultado['id_nuevo_carga'], ENT_QUOTES, 'UTF-8');?>"
		   data-docente="<?=htmlspecialchars(UsuariosPadre::nombreCompletoDelUsuario($resultado), ENT_QUOTES, 'UTF-8');?>"
		   data-curso="<?=htmlspecialchars("[" . $resultado['gra_id'] . "] " . strtoupper($resultado['gra_nombre'] . " " . $resultado['gru_nombre']), ENT_QUOTES, 'UTF-8');?>"
		   data-asignatura="<?=htmlspecialchars("[" . $resultado['mat_id'] . "] " . strtoupper(empty($resultado['mat_nombre'])?'':$resultado['mat_nombre']) . " (" . $resultado['mat_valor'] . "%)", ENT_QUOTES, 'UTF-8');?>"
		   data-ih="<?=htmlspecialchars($resultado['car_ih'], ENT_QUOTES, 'UTF-8');?>"
		   data-periodo="<?=htmlspecialchars($resultado['car_periodo'], ENT_QUOTES, 'UTF-8');?>"
		   data-actividades="<?=htmlspecialchars(!empty($resultado['actividades']) ? $resultado['actividades'] : 0, ENT_QUOTES, 'UTF-8');?>"
		   data-actividades-registradas="<?=htmlspecialchars(!empty($resultado['actividades_registradas']) ? $resultado['actividades_registradas'] : 0, ENT_QUOTES, 'UTF-8');?>"
		   data-director-grupo="<?=htmlspecialchars($normalizarSiNo($resultado['car_director_grupo'] ?? 0), ENT_QUOTES, 'UTF-8');?>"
		   data-permiso2="<?=htmlspecialchars($normalizarSiNo($resultado['car_permiso2'] ?? 0), ENT_QUOTES, 'UTF-8');?>"
		   data-indicador-automatico="<?=htmlspecialchars($normalizarSiNo($resultado['car_indicador_automatico'] ?? 0), ENT_QUOTES, 'UTF-8');?>"
		   data-max-indicadores="<?=htmlspecialchars($resultado['car_maximos_indicadores'], ENT_QUOTES, 'UTF-8');?>"
		   data-max-calificaciones="<?=htmlspecialchars($resultado['car_maximas_calificaciones'], ENT_QUOTES, 'UTF-8');?>"
		   data-cantidad-estudiantes="<?=htmlspecialchars($cantidadEstudiantes, ENT_QUOTES, 'UTF-8');?>"
		   data-activa="<?=htmlspecialchars(isset($resultado['car_activa']) ? $resultado['car_activa'] : 1, ENT_QUOTES, 'UTF-8');?>"
		   title="Ver detalles">
		   <i class="fa fa-chevron-right"></i>
	   </button>
	   </td>
	   <td><input type="checkbox" class="carga-checkbox" value="<?=$resultado['car_id'];?>"></td>
	   <td><?= $contReg; ?></td>
		<td><?= $resultado['id_nuevo_carga']; ?></td>
		<td><?= $marcaDG . "" . strtoupper($resultado['uss_nombre'] . " " . $resultado['uss_nombre2'] . " " . $resultado['uss_apellido1'] . " " . $resultado['uss_apellido2']); ?></td>
		<td><?= $marcaMediaTecnica . strtoupper($resultado['gra_nombre'] . " " . $resultado['gru_nombre']); ?></td>
		<td><?= strtoupper(empty($resultado['mat_nombre'])?'':$resultado['mat_nombre']) . " (" . $resultado['mat_valor'] . "%)"; ?></td>
		<td><?= $resultado['car_ih']; ?></td>
		<td><?php 
			$periodoGrado = !empty($resultado['gra_periodos']) ? $resultado['gra_periodos'] : 4;
			$periodoActual = $resultado['car_periodo'];
			echo ($periodoActual > $periodoGrado) ? '<span class="badge badge-success">Finalizado</span>' : $periodoActual;
		?></td>
		<?php
		// Usar valores por defecto si no están disponibles (lazy loading)
		$actividadesTotales = $resultado['actividades'] ?? null;
		$actividadesRegistradas = $resultado['actividades_registradas'] ?? null;
		
		if ($actividadesTotales === null || $actividadesRegistradas === null) {
			// Mostrar botón para cargar datos bajo demanda
			$porcentajeCargas = '<button class="btn btn-xs btn-info btn-cargar-notas" 
									data-carga-id="' . $resultado['car_id'] . '" 
									data-periodo="' . $resultado['car_periodo'] . '"
									title="Clic para ver notas declaradas y registradas">
									<i class="fa fa-eye"></i> Ver notas
								</button>';
		} else {
			$porcentajeCargas = $actividadesTotales . "%&nbsp;&nbsp;-&nbsp;&nbsp;" . $actividadesRegistradas . "%";
			
			if ($permisoReportesNotas) {
				$porcentajeCargas = '<a href="../compartido/reporte-notas.php?carga=' . base64_encode($resultado['car_id']) . '&per=' . base64_encode($resultado['car_periodo']) . '&grado=' . base64_encode($resultado["car_curso"]) . '&grupo=' . base64_encode($resultado["car_grupo"]) . '" target="_blank" style="text-decoration:underline; color:#00F;" title="Calificaciones">' . $actividadesTotales . '%&nbsp;&nbsp;-&nbsp;&nbsp;' . $actividadesRegistradas . '%</a>';
			}
		}
		?>
		<td class="td-actividades-<?= $resultado['car_id']; ?>" style="text-align:center;"><?= $porcentajeCargas ?></td>
		<td>
			<div class="btn-group">
				<button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
				<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
					<i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu" >
					<?php if (Modulos::validarPermisoEdicion() && $permisoReportesNotas) { ?>
						<?php if ($permisoEditar) { ?>
							<li><a href="javascript:void(0);" class="btn-editar-carga-modal" data-carga-id="<?= $resultado['car_id']; ?>" title="Edición rápida"><i class="fa fa-edit"></i> Edición rápida</a></li>
							<li><a href="cargas-editar.php?idR=<?= base64_encode($resultado['car_id']); ?>"><i class="fa fa-pencil"></i> <?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> completa</a></li>
						<?php }
						if ($config['conf_permiso_eliminar_cargas'] == 'SI' && $permisoEliminar) { ?>
							<li>
								<a href="javascript:void(0);" title="Eliminar" onClick="sweetConfirmacion('Alerta!','Deseas eliminar esta accion?','question','cargas-eliminar.php?id=<?= base64_encode($resultado['car_id']); ?>')"><?= $frases[174][$datosUsuarioActual['uss_idioma']]; ?></a>
							</li>
						<?php }
						if ($permisoAutologin && !empty($resultado['uss_id'])) { ?>
							<li>
								<a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta acción te permitirá entrar como docente y ver todos los detalles de esta carga. Deseas continuar?','question','auto-login.php?user=<?= base64_encode($resultado['car_docente']); ?>&tipe=<?= base64_encode(2) ?>&carga=<?= base64_encode($resultado['car_id']); ?>&periodo=<?= base64_encode($resultado['car_periodo']); ?>')">Ver como docente</a>
							</li>
							<?php }
					}
					if ($permisoAutologin) { ?>
							<li><a href="cargas-horarios.php?id=<?= base64_encode($resultado['car_id']); ?>" title="Ingresar horarios">Ingresar Horarios</a></li>
						<?php }
					if ($permisoHorarios) { ?>
							<li><a href="periodos-resumen.php?carga=<?= base64_encode($resultado['car_id']); ?>" title="Resumen Periodos"><?= $frases[84][$datosUsuarioActual['uss_idioma']]; ?></a></li>
						<?php }
					if ($permisoIndicadores) { ?>
							<li><a href="cargas-indicadores.php?carga=<?= base64_encode($resultado['car_id']); ?>&docente=<?= base64_encode($resultado['car_docente']); ?>">Indicadores</a></li>
						<?php } ?>
						<?php if ($permisoPlanilla) { ?>
							<li><a href="../compartido/planilla-docentes.php?carga=<?= base64_encode($resultado['car_id']); ?>" target="_blank">Ver Planilla</a></li>
						<?php }
						if ($permisoPlanillaNotas) { ?>
							<li><a href="../compartido/planilla-docentes-notas.php?carga=<?= base64_encode($resultado['car_id']); ?>" target="_blank">Ver Planilla con notas</a></li>
						<?php } ?>
						<?php if ($permisoGenerarInforme) { 
							// Preparar datos para el botón (siempre habilitado)
							$datosGeneracion = [
								'carga' => $resultado["car_id"],
								'periodo' => $resultado["car_periodo"],
								'grado' => $resultado["car_curso"],
								'grupo' => $resultado["car_grupo"],
								'tipoGrado' => $resultado["gra_tipo"]
							];
							$datosJson = htmlspecialchars(json_encode($datosGeneracion), ENT_QUOTES, 'UTF-8');
							?>
							<li class="dropdown-item-generar-informe">
								<a style="color: #2c3e50; cursor: pointer;" 
								   class="dropdown-item btn-generar-informe-async" 
								   href="javascript:void(0);" 
								   data-carga='<?= $datosJson ?>'
								   title="Generar informe de esta carga">
									📊 Generar Informe
								</a>
							</li>
						<?php } ?>
						<?php if($permisoComportamiento){?>
						<li><a href="comportamiento.php?curso=<?=base64_encode($resultado['car_curso']);?>&grupo=<?=base64_encode($resultado['car_grupo']);?>&asignatura=<?=base64_encode($resultado['mat_id']);?>" title="Observaciones de comportamiento registradas">Comportamiento</a></li>
						<?php }?>
				</ul>
			</div>
		</td>
	</tr>
<?php
	$contReg++;
} ?>