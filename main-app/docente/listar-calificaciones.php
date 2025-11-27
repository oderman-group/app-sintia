<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0035';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

$valores = Actividades::consultarValores($config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajeRestante = 100 - $valores[0];
?>
</head>

<div class="card card-topline-purple" name="elementoGlobalBloquear">
	<div class="card-head">
		<header><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></header>
		<div class="tools">
			<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
		</div>
	</div>
	
	

	
	<div class="card-body">
		<div class="row" style="margin-bottom: 10px;">
			<div class="col-sm-12">
				
		<?php
		// ============================================
		// VALIDACIÓN DE INDICADORES OBLIGATORIOS
		// ============================================
		$indicadoresObligatorios = ($datosCargaActual['car_indicador_automatico'] != 1);
		$tieneIndicadores = false;
		$mensajeIndicadores = '';
		
		if ($indicadoresObligatorios) {
			$consultaIndicadores = Indicadores::traerIndicadoresCargaPeriodo($cargaConsultaActual, $periodoConsultaActual);
			$numIndicadores = mysqli_num_rows($consultaIndicadores);
			
			if ($numIndicadores > 0) {
				$tieneIndicadores = true;
			} else {
				$mensajeIndicadores = '
				<div class="alert alert-warning" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 4px solid #f39c12; border-radius: 5px; padding: 15px; margin-bottom: 15px;">
					<div style="display: flex; align-items: center; gap: 10px;">
						<i class="fa fa-exclamation-triangle" style="font-size: 24px; color: #f39c12;"></i>
						<div style="flex: 1;">
							<strong style="color: #856404; font-size: 16px;">⚠️ Indicadores Requeridos</strong>
							<p style="margin: 5px 0 0 0; color: #856404;">
								Debes registrar al menos un indicador antes de poder agregar actividades.
								<a href="indicadores.php?carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'" class="alert-link" style="font-weight: 600; text-decoration: underline;">
									Ir a Indicadores <i class="fa fa-arrow-right"></i>
								</a>
							</p>
						</div>
					</div>
				</div>';
			}
		}
		
		// Mostrar mensaje si no tiene indicadores
		if ($indicadoresObligatorios && !$tieneIndicadores) {
			echo $mensajeIndicadores;
		}
		// ============================================
		
		if( CargaAcademica::validarAccionAgregarCalificaciones($datosCargaActual, $valores, $periodoConsultaActual, $porcentajeRestante) ) {
		?>
		
                <div class="btn-group">
                    <button 
                        type="button" 
                        class="btn deepPink-bgcolor <?= ($indicadoresObligatorios && !$tieneIndicadores) ? 'disabled' : ''; ?>" 
                        data-toggle="<?= ($indicadoresObligatorios && !$tieneIndicadores) ? '' : 'modal'; ?>" 
                        data-target="<?= ($indicadoresObligatorios && !$tieneIndicadores) ? '' : '#modalAgregarActividad'; ?>"
                        style="transition: all 0.3s ease; <?= ($indicadoresObligatorios && !$tieneIndicadores) ? 'opacity: 0.6; cursor: not-allowed;' : ''; ?>"
                        <?= ($indicadoresObligatorios && !$tieneIndicadores) ? 'disabled title="Debes registrar indicadores primero"' : ''; ?>
                    >
                        <i class="fa fa-plus-circle"></i> Agregar Actividad
                    </button>
                </div>
				
				
		<?php
		}
		?>
				
		<?php if($datosCargaActual['car_configuracion']==1 and $porcentajeRestante<=0){?>
			<p style="color: tomato;"> Has alcanzado el 100% de valor para las calificaciones. </p>
		<?php }?>
					
		<?php if($datosCargaActual['car_maximas_calificaciones']<=$valores[1]){?>
			<p style="color: tomato;"> Has alcanzado el número máximo de calificaciones permitidas. </p>
		<?php }?>
		
		<?php if( CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) {?>
				<div class="btn-group">
					<a href="calificaciones-todas-rapido.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" class="btn bg-purple">
						LLenar más rápido las calificaciones
					</a>
				</div>
		<?php }?>
		
			</div>
		</div>
		
		
		
	<div class="table-responsive">
		<table class="table table-striped custom-table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
					<th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
					<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
					<th><?=$frases[52][$datosUsuarioActual['uss_idioma']];?></th>
					
					<?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
						<th><?=$frases[68][$datosUsuarioActual['uss_idioma']];?></th>
					<?php }?>
					
					<?php if($datosCargaActual['car_evidencia']==1){?>
						<th>Evidencia</th>
					<?php }?>
					
					<th>#EC/#ET</th>
					<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					// ============================================
					// CONSULTAR ACTIVIDADES ACTIVAS
					// ============================================
					$consulta = Calificaciones::consultarActividadesIndicador($config, $cargaConsultaActual, $periodoConsultaActual);
					$contReg = 1;
					$porcentajeActual = 0;
					$cantidadEstudiantes = Estudiantes::contarEstudiantesParaDocentes($filtroDocentesParaListarEstudiantes);
					
					// Primero mostrar actividades activas
					while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
						$bg = '';
						$numerosEstudiantes = Calificaciones::consultaNumEstudiantesCalificados($config, $datosCargaActual, $resultado['act_id']);
						if($numerosEstudiantes[0]<$cantidadEstudiantesParaDocentes) $bg = '#FCC';
							
						$porcentajeActual +=$resultado['act_valor'];
						
						if($datosCargaActual['car_evidencia']==1){
							$evidencia = Calificaciones::traerDatosEvidencias($config, $resultado['act_id_evidencia']);
						}
					?>
					
					<tr id="reg<?=$resultado['act_id'];?>">
						<td><?=$contReg;?></td>
						<td><?=$resultado['id_nuevo_act'];?></td>
						<td><a href="calificaciones-registrar.php?idR=<?=base64_encode($resultado['act_id']);?>" style="text-decoration: underline;" title="Calificar"><?=$resultado['act_descripcion'];?></a></td>
						<td><?=$resultado['act_fecha'];?></td>
						<td><?=$resultado['act_valor'];?></td>
						
						<?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
							<td style="font-size: 10px;"><?=$resultado['ind_nombre'];?></td>
						<?php }?>
						
						<?php if($datosCargaActual['car_evidencia']==1){?>
							<td><?=$evidencia['evid_nombre']." (".$evidencia['evid_valor']."%)";?></td>
						<?php }?>
						
						<td style="background-color:<?=$bg;?>"><a href="../compartido/reporte-calificaciones.php?idActividad=<?=base64_encode($resultado['act_id']);?>&grado=<?=base64_encode($datosCargaActual['car_curso']);?>&grupo=<?=base64_encode($datosCargaActual['car_grupo']);?>" target="_blank" style="text-decoration: underline;"><?=$numerosEstudiantes[0];?>/<?=$cantidadEstudiantesParaDocentes;?></a></td>
						<td>
							
							<?php
								$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
								$arrayDatos = json_encode($arrayEnviar);
								$objetoEnviar = htmlentities($arrayDatos);
								?>
							
							<?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>
							
							<div class="btn-group">
								<button class="btn btn-xs btn-info dropdown-toggle center no-margin" type="button" data-toggle="dropdown" aria-expanded="false"> Acciones
									<i class="fa fa-angle-down"></i>
								</button>
								<ul class="dropdown-menu pull-left" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 23px, 0px); top: 0px; left: 0px; will-change: transform;">
									<li><a href="calificaciones-registrar.php?idR=<?=base64_encode($resultado['act_id']);?>">Calificar</a></li>
									<li><a href="calificaciones-editar.php?idR=<?=base64_encode($resultado['act_id']);?>">Editar</a></li>
									<li>
										<a 
											href="#" 
											title="<?=$objetoEnviar;?>" 
											id="<?=$resultado['act_id'];?>" 
											data-actividad-id="<?=$resultado['act_id'];?>"
											data-actividad-descripcion="<?=htmlspecialchars($resultado['act_descripcion'], ENT_QUOTES, 'UTF-8');?>"
											data-actividad-fecha="<?=$resultado['act_fecha'];?>"
											data-actividad-valor="<?=$resultado['act_valor'];?>"
											data-actividad-id-tipo="<?=$resultado['act_id_tipo'];?>"
											data-indicador-nombre="<?=htmlspecialchars($resultado['ind_nombre'], ENT_QUOTES, 'UTF-8');?>"
											data-id-nuevo-act="<?=$resultado['id_nuevo_act'];?>"
											data-url="ajax-calificaciones-eliminar.php?idR=<?=base64_encode($resultado['act_id']);?>&idIndicador=<?=base64_encode($resultado['act_id_tipo']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>"
											onClick="eliminarActividadAsincrona(this); return false;"
										>
											Eliminar
										</a>
									</li>
								</ul>
							</div>
							
							<?php } ?>
						</td>
					</tr>
					<?php 
						$contReg++;
					}
					
					// ============================================
					// CONSULTAR ACTIVIDADES ELIMINADAS
					// ============================================
					if (!class_exists('BindSQL')) {
						require_once(ROOT_PATH."/main-app/class/BindSQL.php");
					}
					$sqlEliminadas = "SELECT aa.id_nuevo AS id_nuevo_act, aa.*, ai.* FROM ".BD_ACADEMICA.".academico_actividades aa
					INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=aa.act_id_tipo AND ai.institucion=aa.institucion AND ai.year=aa.year
					WHERE aa.act_id_carga=? AND aa.act_periodo=? AND aa.act_estado=0 AND aa.institucion=? AND aa.year=?
					ORDER BY aa.act_fecha_eliminacion DESC";
					$parametrosEliminadas = [$cargaConsultaActual, $periodoConsultaActual, $config['conf_id_institucion'], $_SESSION["bd"]];
					$consultaEliminadas = BindSQL::prepararSQL($sqlEliminadas, $parametrosEliminadas);
					
					if(mysqli_num_rows($consultaEliminadas) > 0) {
						$numEliminadas = mysqli_num_rows($consultaEliminadas);
						// Separador visual para actividades eliminadas con botón expandir/contraer
						?>
						<tr data-separador-eliminadas="true" style="background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%); border-top: 3px solid #95a5a6;">
							<td colspan="<?=($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null) ? (($datosCargaActual['car_evidencia']==1) ? 8 : 7) : (($datosCargaActual['car_evidencia']==1) ? 7 : 6);?>" style="padding: 12px; text-align: center; font-weight: 700; color: #7f8c8d; font-size: 13px; cursor: pointer;" onclick="toggleActividadesEliminadas()">
								<i class="fa fa-trash"></i> ACTIVIDADES ELIMINADAS 
								<span id="contador-eliminadas">(<?=$numEliminadas;?>)</span>
								<i class="fa fa-chevron-down" id="icono-eliminadas" style="margin-left: 8px; transition: transform 0.3s ease;"></i>
							</td>
						</tr>
						<?php
						
						// Mostrar actividades eliminadas (ocultas por defecto)
						$contadorEliminadas = 0;
						while($resultadoEliminada = mysqli_fetch_array($consultaEliminadas, MYSQLI_BOTH)){
							$bg = '';
							$numerosEstudiantes = Calificaciones::consultaNumEstudiantesCalificados($config, $datosCargaActual, $resultadoEliminada['act_id']);
							if($numerosEstudiantes[0]<$cantidadEstudiantesParaDocentes) $bg = '#FCC';
							
							if($datosCargaActual['car_evidencia']==1){
								$evidencia = Calificaciones::traerDatosEvidencias($config, $resultadoEliminada['act_id_evidencia']);
							}
							$contadorEliminadas++;
						?>
						
						<tr id="reg<?=$resultadoEliminada['act_id'];?>" class="fila-actividad-eliminada" style="background: #f8f9fa; opacity: 0.75; display: none;">
							<td><span style="color: #95a5a6;"><?=$contReg;?></span></td>
							<td><span style="color: #95a5a6;"><?=$resultadoEliminada['id_nuevo_act'];?></span></td>
							<td>
								<span style="text-decoration: line-through; color: #95a5a6;">
									<?=$resultadoEliminada['act_descripcion'];?>
								</span>
								<small style="display: block; color: #e74c3c; font-weight: 600;">
									<i class="fa fa-ban"></i> ELIMINADA
								</small>
							</td>
							<td><span style="color: #95a5a6;"><?=$resultadoEliminada['act_fecha'];?></span></td>
							<td><span style="color: #95a5a6;"><?=$resultadoEliminada['act_valor'];?>%</span></td>
							
							<?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
								<td style="font-size: 10px;"><span style="color: #95a5a6;"><?=$resultadoEliminada['ind_nombre'];?></span></td>
							<?php }?>
							
							<?php if($datosCargaActual['car_evidencia']==1){?>
								<td><span style="color: #95a5a6;"><?=$evidencia['evid_nombre']." (".$evidencia['evid_valor']."%)";?></span></td>
							<?php }?>
							
							<td style="background-color:<?=$bg;?>"><span style="color: #95a5a6;"><?=$numerosEstudiantes[0];?>/<?=$cantidadEstudiantesParaDocentes;?></span></td>
							<td>
								<?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>
									<a 
										href="calificaciones-restaurar.php?idR=<?=base64_encode($resultadoEliminada['act_id']);?>&idIndicador=<?=base64_encode($resultadoEliminada['act_id_tipo']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" 
										class="btn btn-xs btn-success"
										title="Restaurar esta actividad eliminada"
										onclick="return confirm('¿Estás seguro de restaurar esta actividad?\n\nAl restaurarla se recalcularán los porcentajes de todas las actividades del mismo indicador.');"
									>
										<i class="fa fa-undo"></i> Restaurar
									</a>
								<?php } else { ?>
									<span style="color: #95a5a6; font-size: 11px;"><i class="fa fa-lock"></i> Sin permiso</span>
								<?php } ?>
							</td>
						</tr>
						<?php 
							$contReg++;
						}
					}
					?>
			</tbody>
			<tfoot>
				<tr style="font-weight:bold;">
					<td colspan="4"><?=strtoupper($frases[107][$datosUsuarioActual['uss_idioma']]);?></td>
					<td id="porcentaje-total-actividades"><?=$porcentajeActual;?>%</td>
					<td colspan="3"></td>
					</tr>
			</tfoot>
		</table>
		</div>
	</div>
</div>

<?php 
// ============================================
// IDENTIFICADORES PARA CONSTRUIR LA TABLA DE ELIMINADAS
// ============================================
$colspanEliminadas = ($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null) ? (($datosCargaActual['car_evidencia']==1) ? 8 : 7) : (($datosCargaActual['car_evidencia']==1) ? 7 : 6);
$tieneIndicadorManual = ($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null);
$tieneEvidencia = ($datosCargaActual['car_evidencia']==1);
$tienePermisos = ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1);
?>
<script type="text/javascript">
// Asegurar que el script se ejecute después de que el contenido se haya insertado
(function() {
'use strict';

// ============================================
// FUNCIÓN PARA ELIMINAR ACTIVIDAD ASÍNCRONAMENTE
// Asegurar que esté disponible globalmente
// ============================================
window.eliminarActividadAsincrona = function(elemento) {
    const actividadId = elemento.getAttribute('data-actividad-id');
    const descripcion = elemento.getAttribute('data-actividad-descripcion');
    const fecha = elemento.getAttribute('data-actividad-fecha');
    const valor = elemento.getAttribute('data-actividad-valor');
    const idTipo = elemento.getAttribute('data-actividad-id-tipo');
    const indicadorNombre = elemento.getAttribute('data-indicador-nombre');
    const idNuevoAct = elemento.getAttribute('data-id-nuevo-act');
    const url = elemento.getAttribute('data-url');
    const filaRegistro = document.getElementById('reg' + actividadId);
    const elementoGlobalBloquear = document.getElementsByName("elementoGlobalBloquear")[0];

    Swal.fire({
        title: '¿Desea eliminar esta actividad?',
        html: `<div style="text-align: left; padding: 10px;">
            <p><strong>${descripcion}</strong></p>
            <p>Valor: ${valor}%</p>
            ${indicadorNombre ? `<p>Indicador: ${indicadorNombre}</p>` : ''}
            <p style="color: #e74c3c; margin-top: 10px;"><i class="fa fa-exclamation-triangle"></i> Al eliminar, se recalcularán los porcentajes de las actividades restantes.</p>
        </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e74c3c',
        backdrop: `rgba(0,0,123,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar overlay de carga
            let overlay = null;
            if (elementoGlobalBloquear) {
                elementoGlobalBloquear.style.position = 'relative';
                overlay = document.createElement('div');
                overlay.style.position = 'absolute';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(128, 128, 128, 0.7)';
                overlay.style.display = 'flex';
                overlay.style.justifyContent = 'center';
                overlay.style.alignItems = 'center';
                overlay.style.zIndex = '1000';
                overlay.style.color = 'white';
                overlay.style.fontSize = '1.2em';
                overlay.innerHTML = '<div style="text-align: center;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Eliminando actividad y recalculando porcentajes...</div>';
                elementoGlobalBloquear.appendChild(overlay);
            }

            // Animación de salida de la fila
            if (filaRegistro) {
                filaRegistro.style.transition = 'all 0.5s ease-out';
                filaRegistro.style.opacity = '0.5';
                filaRegistro.style.transform = 'translateX(-20px)';
            }

            // Llamada AJAX
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover overlay
                        if (overlay && elementoGlobalBloquear) {
                            elementoGlobalBloquear.removeChild(overlay);
                        }

                        // Ocultar y remover la fila original
                        if (filaRegistro) {
                            setTimeout(() => {
                                filaRegistro.style.display = 'none';
                                filaRegistro.remove();
                                
                                // Actualizar porcentajes de actividades restantes
                                actualizarPorcentajesActividades(data.data.nuevos_porcentajes, data.data.configuracion_automatica);
                                
                                // Actualizar porcentaje total
                                const porcentajeTotalElement = document.getElementById('porcentaje-total-actividades');
                                if (porcentajeTotalElement) {
                                    // Asegurar que sea un número y formatearlo correctamente
                                    const porcentajeTotal = parseFloat(data.data.porcentaje_total) || 0;
                                    porcentajeTotalElement.textContent = porcentajeTotal.toFixed(2).replace(/\.?0+$/, '') + '%';
                                    
                                    // Animación de actualización
                                    porcentajeTotalElement.style.transition = 'all 0.3s ease';
                                    porcentajeTotalElement.style.backgroundColor = '#fff3cd';
                                    setTimeout(() => {
                                        porcentajeTotalElement.style.backgroundColor = '';
                                    }, 500);
                                }
                                
                                // Agregar a la sección de eliminadas
                                agregarActividadEliminada(data.data, '<?=$colspanEliminadas;?>', <?=$tieneIndicadorManual ? 'true' : 'false';?>, <?=$tieneEvidencia ? 'true' : 'false';?>, <?=$tienePermisos ? 'true' : 'false';?>, '<?=base64_encode($cargaConsultaActual);?>', '<?=base64_encode($periodoConsultaActual);?>');
                                
                                // Toast de éxito
                                $.toast({
                                    heading: '✅ Actividad Eliminada',
                                    text: data.message || 'La actividad se eliminó exitosamente. Los porcentajes han sido recalculados.',
                                    position: 'top-right',
                                    loaderBg: '#28a745',
                                    icon: 'success',
                                    hideAfter: 4000,
                                    stack: 1
                                });
                            }, 500);
                        }
                    } else {
                        throw new Error(data.message || 'Error al eliminar la actividad');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Restaurar visualmente la fila
                    if (filaRegistro) {
                        filaRegistro.style.transition = '';
                        filaRegistro.style.opacity = '1';
                        filaRegistro.style.transform = '';
                    }
                    
                    // Remover overlay
                    if (overlay && elementoGlobalBloquear) {
                        elementoGlobalBloquear.removeChild(overlay);
                    }
                    
                    // Toast de error
                    $.toast({
                        heading: '❌ Error',
                        text: error.message || 'No se pudo eliminar la actividad. Por favor, intenta nuevamente.',
                        position: 'top-right',
                        loaderBg: '#dc3545',
                        icon: 'error',
                        hideAfter: 5000,
                        stack: 1
                    });
                });
        }
    });
}

// ============================================
// ACTUALIZAR PORCENTAJES DE ACTIVIDADES RESTANTES
// Asegurar que esté disponible globalmente
// ============================================
window.actualizarPorcentajesActividades = function(nuevosPorcentajes, configuracionAutomatica) {
    Object.keys(nuevosPorcentajes).forEach(actId => {
        const fila = document.getElementById('reg' + actId);
        if (fila) {
            // Buscar la columna de valor (índice 4, pero puede variar)
            const celdas = fila.querySelectorAll('td');
            if (celdas.length > 4) {
                const celdaValor = celdas[4];
                const nuevoValor = nuevosPorcentajes[actId];
                
                // Animación de cambio
                celdaValor.style.transition = 'all 0.3s ease';
                celdaValor.style.backgroundColor = '#fff3cd';
                setTimeout(() => {
                    celdaValor.textContent = nuevoValor.toFixed(2).replace(/\.?0+$/, '') + '%';
                    setTimeout(() => {
                        celdaValor.style.backgroundColor = '';
                    }, 300);
                }, 150);
            }
        }
    });
}

// ============================================
// FUNCIÓN AUXILIAR PARA BASE64
// Asegurar que esté disponible globalmente
// ============================================
window.base64EncodeCompat = function(str) {
    try {
        return btoa(unescape(encodeURIComponent(str)));
    } catch(e) {
        // Fallback si btoa no está disponible
        return str;
    }
}

// ============================================
// AGREGAR ACTIVIDAD ELIMINADA A LA SECCIÓN
// Asegurar que esté disponible globalmente
// ============================================
window.agregarActividadEliminada = function(datos, colspan, tieneIndicadorManual, tieneEvidencia, tienePermisos, carga, periodo) {
    const tbody = document.querySelector('table tbody');
    if (!tbody) return;
    
    // Buscar si ya existe el separador de eliminadas - buscar de múltiples formas
    let separadorEliminadas = tbody.querySelector('tr[data-separador-eliminadas]');
    
    // Si no se encontró por atributo, buscar por contenido de texto
    if (!separadorEliminadas) {
        const todasLasFilas = tbody.querySelectorAll('tr');
        todasLasFilas.forEach(fila => {
            const texto = fila.textContent || '';
            if (texto.includes('ACTIVIDADES ELIMINADAS')) {
                // Verificar que no tenga fondo blanco (que sea el separador)
                const estilo = window.getComputedStyle(fila);
                if (estilo.background && estilo.background.includes('gradient')) {
                    separadorEliminadas = fila;
                    // Agregar el atributo para futuras búsquedas
                    separadorEliminadas.setAttribute('data-separador-eliminadas', 'true');
                }
            }
        });
    }
    
    // Si todavía no existe, crear el separador
    if (!separadorEliminadas) {
        separadorEliminadas = document.createElement('tr');
        separadorEliminadas.setAttribute('data-separador-eliminadas', 'true');
        separadorEliminadas.style.background = 'linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%)';
        separadorEliminadas.style.borderTop = '3px solid #95a5a6';
        separadorEliminadas.style.cursor = 'pointer';
        separadorEliminadas.onclick = function() { toggleActividadesEliminadas(); };
        separadorEliminadas.innerHTML = `<td colspan="${colspan}" style="padding: 12px; text-align: center; font-weight: 700; color: #7f8c8d; font-size: 13px;">
            <i class="fa fa-trash"></i> ACTIVIDADES ELIMINADAS 
            <span id="contador-eliminadas">(1)</span>
            <i class="fa fa-chevron-down" id="icono-eliminadas" style="margin-left: 8px; transition: transform 0.3s ease;"></i>
        </td>`;
        tbody.appendChild(separadorEliminadas);
    }
    
    // Crear fila de actividad eliminada (oculta por defecto)
    const filaEliminada = document.createElement('tr');
    filaEliminada.id = 'reg' + datos.actividad_id;
    filaEliminada.className = 'fila-actividad-eliminada';
    filaEliminada.style.background = '#f8f9fa';
    filaEliminada.style.opacity = '0.75';
    filaEliminada.style.display = 'none'; // Oculto por defecto
    filaEliminada.style.animation = 'fadeIn 0.5s ease-in';
    
    let colsHTML = `
        <td><span style="color: #95a5a6;">-</span></td>
        <td><span style="color: #95a5a6;">${datos.id_nuevo_act || ''}</span></td>
        <td>
            <span style="text-decoration: line-through; color: #95a5a6;">
                ${datos.actividad_descripcion}
            </span>
            <small style="display: block; color: #e74c3c; font-weight: 600;">
                <i class="fa fa-ban"></i> ELIMINADA
            </small>
        </td>
        <td><span style="color: #95a5a6;">${datos.actividad_fecha}</span></td>
        <td><span style="color: #95a5a6;">${datos.actividad_valor}%</span></td>
    `;
    
    if (tieneIndicadorManual) {
        colsHTML += `<td style="font-size: 10px;"><span style="color: #95a5a6;">${datos.indicador_nombre || ''}</span></td>`;
    }
    
    if (tieneEvidencia) {
        colsHTML += `<td><span style="color: #95a5a6;">-</span></td>`;
    }
    
    colsHTML += `<td><span style="color: #95a5a6;">-/-</span></td>`;
    colsHTML += `<td>`;
    
    if (tienePermisos) {
        const idActividadB64 = base64EncodeCompat(datos.actividad_id);
        const idIndicadorB64 = base64EncodeCompat(datos.actividad_id_tipo);
        colsHTML += `<a 
            href="calificaciones-restaurar.php?idR=${idActividadB64}&idIndicador=${idIndicadorB64}&carga=${carga}&periodo=${periodo}" 
            class="btn btn-xs btn-success"
            title="Restaurar esta actividad eliminada"
            onclick="return confirm('¿Estás seguro de restaurar esta actividad?\\n\\nAl restaurarla se recalcularán los porcentajes de todas las actividades del mismo indicador.');"
        >
            <i class="fa fa-undo"></i> Restaurar
        </a>`;
    } else {
        colsHTML += `<span style="color: #95a5a6; font-size: 11px;"><i class="fa fa-lock"></i> Sin permiso</span>`;
    }
    
    colsHTML += `</td>`;
    filaEliminada.innerHTML = colsHTML;
    
    // Verificar que no exista ya una fila con ese ID (por si se está duplicando)
    const filaExistente = document.getElementById('reg' + datos.actividad_id);
    if (filaExistente && filaExistente !== filaEliminada) {
        // Si ya existe, no agregar de nuevo
        console.warn('La actividad eliminada ya existe en la tabla');
        return;
    }
    
    // Agregar después del separador (solo si no existe ya)
    if (!filaExistente) {
        separadorEliminadas.insertAdjacentElement('afterend', filaEliminada);
        
        // Actualizar contador de eliminadas
        actualizarContadorEliminadas();
    }
    
    // Añadir estilo de animación si no existe
    if (!document.getElementById('estilo-animacion-fadein')) {
        const style = document.createElement('style');
        style.id = 'estilo-animacion-fadein';
        style.textContent = '@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 0.75; transform: translateY(0); } }';
        document.head.appendChild(style);
    }
};

// ============================================
// FUNCIÓN PARA EXPANDIR/CONTRAER ACTIVIDADES ELIMINADAS
// ============================================
window.toggleActividadesEliminadas = function() {
    const filasEliminadas = document.querySelectorAll('.fila-actividad-eliminada');
    const icono = document.getElementById('icono-eliminadas');
    const separador = document.querySelector('tr[data-separador-eliminadas]');
    
    if (!filasEliminadas || filasEliminadas.length === 0) {
        return;
    }
    
    // Verificar si están visibles o no (verificar la primera fila)
    const primeraFila = filasEliminadas[0];
    const estanOcultas = primeraFila.style.display === 'none' || window.getComputedStyle(primeraFila).display === 'none';
    
    // Toggle de visibilidad
    filasEliminadas.forEach(function(fila) {
        if (estanOcultas) {
            fila.style.display = 'table-row';
        } else {
            fila.style.display = 'none';
        }
    });
    
    // Animar icono
    if (icono) {
        if (estanOcultas) {
            icono.style.transform = 'rotate(180deg)';
            icono.classList.remove('fa-chevron-down');
            icono.classList.add('fa-chevron-up');
        } else {
            icono.style.transform = 'rotate(0deg)';
            icono.classList.remove('fa-chevron-up');
            icono.classList.add('fa-chevron-down');
        }
    }
    
    // Cambiar estilo del separador cuando está expandido
    if (separador) {
        if (estanOcultas) {
            separador.style.background = 'linear-gradient(135deg, #d5dbdb 0%, #aab7b8 100%)';
        } else {
            separador.style.background = 'linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%)';
        }
    }
};

// Actualizar contador cuando se agrega una actividad eliminada dinámicamente
window.actualizarContadorEliminadas = function() {
    const contador = document.getElementById('contador-eliminadas');
    if (contador) {
        const filasEliminadas = document.querySelectorAll('.fila-actividad-eliminada');
        const numEliminadas = filasEliminadas.length;
        contador.textContent = '(' + numEliminadas + ')';
    }
};

// Ejecutar inmediatamente para que las funciones estén disponibles
console.log('✅ Funciones de eliminación de actividades cargadas correctamente');

})();
</script>

<?php include("../compartido/guardar-historial-acciones.php");?>