<?php
if (!empty($data["dataTotal"])) {
	require_once("../Estudiantes.php");
	require_once("../Modulos.php");
	require_once("../Sysjobs.php");
	require_once("../Boletin.php");
}

$permisoReportesNotas = Modulos::validarSubRol(['DT0238']);
$permisoedicion       = Modulos::validarSubRol(['DT0049', 'DT0148', 'DT0129']);
$permisoEditar        = Modulos::validarSubRol(['DT0049']);
$permisoEliminar      = Modulos::validarSubRol(['DT0148']);
$permisoAutologin     = Modulos::validarSubRol(['DT0129']);
$permisoHorarios      = Modulos::validarSubRol(['DT0041']);
$permisoResumen       = Modulos::validarSubRol(['DT0111']);
$permisoIndicadores   = Modulos::validarSubRol(['DT0034']);
$permisoPlanilla      = Modulos::validarSubRol(['DT0239']);
$permisoPlanillaNotas = Modulos::validarSubRol(['DT0237']);
$permisoGenerarInforme = Modulos::validarSubRol(['DT0237']);
$permisoComportamiento = Modulos::validarSubRol(['DT0343']);

$contReg = 1;
foreach ($data["data"] as $resultado) {
	$cargaSP = $resultado['car_id'];
	$periodoSP = $resultado['car_periodo'];
	$marcaMediaTecnica = '';
	if ($resultado['gra_tipo'] == GRADO_INDIVIDUAL) {
		$cantidadEstudiantes = $resultado['cantidad_estudiantes_mt'];
		$marcaMediaTecnica = '<i class="fa fa-bookmark" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Media técnica"></i> ';
	} else {
		$cantidadEstudiantes = $resultado['cantidad_estudiantes'];
	}
	$infoTooltipCargas = "<b>COD:</b> 
	{$resultado['car_id']}<br>
	<b>Director de grupo:</b> 
	{$opcionSINO[$resultado['car_director_grupo']]}<br>
	<b>I.H:</b> 
	{$resultado['car_ih']}<br>
	<b>Puede editar en otros periodos?:</b> 
	{$opcionSINO[$resultado['car_permiso2']]}<br>
	<b>Indicadores automáticos?:</b> 
	{$opcionSINO[$resultado['car_indicador_automatico']]}<br>
	<b>Max. Indicadores:</b> 
	{$resultado['car_maximos_indicadores']}<br>
	<b>Max. Calificaciones:</b> 
	{$resultado['car_maximas_calificaciones']}<br>
	<b>Nro. Estudiantes:</b> 
	{$cantidadEstudiantes}";

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
	   <td><?= $contReg; ?></td>
		<td><a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Información adicional" data-content="<?= $infoTooltipCargas; ?>" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000;"><?= $resultado['id_nuevo_carga']; ?></a></td>
		<td><?= $marcaDG . "" . strtoupper($resultado['uss_nombre'] . " " . $resultado['uss_nombre2'] . " " . $resultado['uss_apellido1'] . " " . $resultado['uss_apellido2']); ?></td>
		<td><?= $marcaMediaTecnica . "[" . $resultado['gra_id'] . "] " . strtoupper($resultado['gra_nombre'] . " " . $resultado['gru_nombre']); ?></td>
		<td><?= "[" . $resultado['mat_id'] . "] " . strtoupper(empty($resultado['mat_nombre'])?'':$resultado['mat_nombre']) . " (" . $resultado['mat_valor'] . "%)"; ?></td>
		<td><?= $resultado['car_ih']; ?></td>
		<td><?= $resultado['car_periodo']; ?></td>
		<?php
		$porcentajeCargas =  $resultado['actividades'] . "%&nbsp;&nbsp;-&nbsp;&nbsp;" . $resultado['actividades_registradas'] . "%";
		if ($permisoReportesNotas) {
			$porcentajeCargas = '<a href="../compartido/reporte-notas.php?carga=' . base64_encode($resultado['car_id']) . '&per=' . base64_encode($resultado['car_periodo']) . '&grado=' . base64_encode($resultado["car_curso"]) . '&grupo=' . base64_encode($resultado["car_grupo"]) . '" target="_blank" style="text-decoration:underline; color:#00F;" title="Calificaciones">' . $resultado['actividades'] . '%&nbsp;&nbsp;-&nbsp;&nbsp;' .$resultado['actividades_registradas'] . '%</a>';
		}
		?>
		<td><?= $porcentajeCargas ?></td>
		<td>
			<div class="btn-group">
				<button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
				<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
					<i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu" >
					<?php if (Modulos::validarPermisoEdicion() && $permisoReportesNotas) { ?>
						<?php if ($permisoEditar) { ?>
							<li><a href="cargas-editar.php?idR=<?= base64_encode($resultado['car_id']); ?>"><?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?></a></li>
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
						<?php 	if ($permisoGenerarInforme) {
							$generarInforme = false;
							$msnajetooltip = "";
							$actividadesDeclaradas = $resultado['actividades'];
							$actividadesRegistradas = $resultado['actividades_registradas'];
							$configGenerarJobs = $config['conf_porcentaje_completo_generar_informe'];
							$numSinNotas=0;
							if ($actividadesDeclaradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
								$generarInforme = false;
								$msnajetooltip = "Las califaciones declaradas no completan el 100% ";
							} else if ($actividadesRegistradas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) { 
								$generarInforme = false;
								$msnajetooltip = "Las califaciones registradas no completan el 100% ";
							} else {
								$generarInforme = true;
							}
							if ($generarInforme) {
								switch (intval($configGenerarJobs)) {
									case 1:
										$numSinNotas            = $resultado["cantidad_estudiantes_sin_nota"];
										if ($numSinNotas < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME) {
											$generarInforme = false;
											$msnajetooltip = "La institución no permite generar informe hasta que todos los estudiantes estén calificados un 100%";
											break;
										}
										break;
									case 2:
										$generarInforme = true;
										$msnajetooltip = "La institución omitirá los estudiantes que no tengan las calificaciones en un 100%";
										break;
									case 3:
										$generarInforme = true;
										$msnajetooltip = "La institución generará el informe con el porcentaje actual de cada estudiante";
										break;
								}
							}				

							$jobsEncontrado = empty($resultado["job_id"]) ? false : true;

							if ($jobsEncontrado) {
								$generarInforme=false;
								switch ($resultado["job_estado"]) {
									case JOBS_ESTADO_ERROR:
										$msnajetooltip =$resultado["job_mensaje"];
										$generarInforme=true;
										break;

									case JOBS_ESTADO_PENDIENTE:
										$msnajetooltip = $resultado["job_mensaje"];
										break;

									case JOBS_ESTADO_PROCESO:
										$msnajetooltip = "El informe está en proceso.";
										break;
									case JOBS_ESTADO_PROCESADO:
										$msnajetooltip = "El informe ya fué procesado.";
										break;

									default:
										$msnajetooltip = "El informe no se puede generar, coloque las notas a todos los estudiantes para generar el informe.";										
										break;
								}
							}

							$tooltip = '';
							if (!empty($msnajetooltip)) {
								$tooltip = ' title="' . $msnajetooltip . '"';
							}
							$onClick='onclick=mostrarGenerarInforme("'.$resultado['car_id'].'")';
							?>
								<li id="informe-<?php $resultado['car_id']?>" class="dropdown-submenu-generar-informe" <?= !$generarInforme ? '' : 'onmouseover="noCerrarToggle(this)"' ?>  data-toggle="tooltip" <?= $tooltip ?>>
								<a style="color:<?= !$generarInforme ? '#bcc6d0' : '#6f6f6f' ?>;" class="dropdown-item dropdown-toggle" href="javascript:void(0);" <?= !$generarInforme ? '' : $onClick ?>  >Generar Informe</a>
								<?php if ($generarInforme) {
									 $parametros='?carga='.base64_encode($resultado["car_id"]).
									              '&periodo='.base64_encode($resultado["car_periodo"]).
												  '&grado='.base64_encode($resultado["car_curso"]).
												  '&grupo='.base64_encode($resultado["car_grupo"]).
												  '&tipoGrado='.base64_encode($resultado["gra_tipo"]);
									?>
									<ul id="generarInforme-<?=$resultado["car_id"]?>" class="dropdown-menu">
										<li><a rel="<?=$configGenerarJobs.'-'.$numSinNotas.'-1';?>" class="dropdown-item"  href="javascript:void(0);" onclick="mensajeGenerarInforme(this)" name="../compartido/generar-informe.php<?=$parametros?>">Manual</a></li>
										<li><a rel="<?=$configGenerarJobs.'-'.$numSinNotas.'-2';?>" class="dropdown-item" href="javascript:void(0);"  onclick="mensajeGenerarInforme(this)" name="../compartido/job-generar-informe.php<?=$parametros?>">Automático</a></li>
									</ul>
								<?php } ?>
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
<script>

	function noCerrarToggle(element){
		 element.addEventListener('click', function(e) {
			e.stopPropagation(); // Evita el cierre al hacer clic dentro del submenú
		 });

	}
	

	function mostrarGenerarInforme(valor) {
		submenu = document.getElementById('generarInforme-' + valor);
		if (submenu.classList.contains('show')) {
			submenu.classList.remove('show');
		} else {
			submenu.classList.add('show');
		};

	}
</script>
<style>
	.dropdown-submenu-generar-informe .dropdown-menu {
		top: 100%;
		/* Mueve el submenú hacia abajo del elemento principal */
		left: 0;
		/* Alinea el submenú con el borde izquierdo del elemento padre */
		margin-top: 0.5rem;
		/* Agrega un pequeño margen superior */
	}
</style>