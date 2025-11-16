<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0062';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");

Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								
								<div class="col-md-12">
									<?php 
									include("../../config-general/mensajes-informativos.php");
									
									// ============================================
									// ESTADÍSTICAS RÁPIDAS DE CURSOS
									// ============================================
									$consultaCursos        = Grados::listarGrados(1);
									$totalCursos           = mysqli_num_rows($consultaCursos);
									$totalCursosActivos    = 0;
									$totalCursosInactivos  = 0;
									
									// Contar activos / inactivos sin volver a consultar
									while ($filaCurso = mysqli_fetch_array($consultaCursos, MYSQLI_BOTH)) {
										if (!empty($filaCurso['gra_estado'])) {
											if ((int)$filaCurso['gra_estado'] === 1) {
												$totalCursosActivos++;
											} else {
												$totalCursosInactivos++;
											}
										} else {
											$totalCursosActivos++;
										}
									}
									mysqli_free_result($consultaCursos);
									
									// Total de estudiantes matriculados (estado 1 o 2)
									$totalEstudiantes = 0;
									$consultaEst = mysqli_query(
										$conexion,
										"SELECT COUNT(*) AS total 
										 FROM ".BD_ACADEMICA.".academico_matriculas 
										 WHERE (mat_estado_matricula=".MATRICULADO." OR mat_estado_matricula=".ASISTENTE.")
										   AND institucion={$config['conf_id_institucion']}
										   AND year={$_SESSION['bd']}"
									);
									if ($consultaEst) {
										$filaEst = mysqli_fetch_array($consultaEst, MYSQLI_BOTH);
										$totalEstudiantes = (int)($filaEst['total'] ?? 0);
										mysqli_free_result($consultaEst);
									}
									
									// Promedio de estudiantes por curso
									$promedioEstudiantesCurso = ($totalCursos > 0)
										? round($totalEstudiantes / $totalCursos, 1)
										: 0;
									?>
									
									<?php if ($totalCursos > 0) { ?>
									<div class="row" style="margin-bottom: 20px;">
										<div class="col-md-3 col-sm-6">
											<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
												<div class="card-body" style="padding: 15px 18px;">
													<div style="font-size: 12px; text-transform: uppercase; color: #7f8c8d; font-weight: 600;">
														Total de cursos
													</div>
													<div style="font-size: 26px; font-weight: 700; color: #2c3e50; margin-top: 5px;">
														<?= $totalCursos; ?>
													</div>
													<div style="font-size: 11px; color: #95a5a6; margin-top: 4px;">
														Activos: <?= $totalCursosActivos; ?> • Inactivos: <?= $totalCursosInactivos; ?>
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-md-3 col-sm-6">
											<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
												<div class="card-body" style="padding: 15px 18px;">
													<div style="font-size: 12px; text-transform: uppercase; color: #7f8c8d; font-weight: 600;">
														Estudiantes matriculados
													</div>
													<div style="font-size: 26px; font-weight: 700; color: #16a085; margin-top: 5px;">
														<?= number_format($totalEstudiantes); ?>
													</div>
													<div style="font-size: 11px; color: #95a5a6; margin-top: 4px;">
														Estados: Matriculados / Asistentes
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-md-3 col-sm-6">
											<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
												<div class="card-body" style="padding: 15px 18px;">
													<div style="font-size: 12px; text-transform: uppercase; color: #7f8c8d; font-weight: 600;">
														Promedio estudiantes / curso
													</div>
													<div style="font-size: 26px; font-weight: 700; color: #2980b9; margin-top: 5px;">
														<?= $promedioEstudiantesCurso; ?>
													</div>
													<div style="font-size: 11px; color: #95a5a6; margin-top: 4px;">
														Distribución general de matrícula
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-md-3 col-sm-6">
											<div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
												<div class="card-body" style="padding: 15px 18px;">
													<div style="font-size: 12px; text-transform: uppercase; color: #7f8c8d; font-weight: 600;">
														% cursos activos
													</div>
													<?php 
													$porcentajeActivos = $totalCursos > 0 
														? round(($totalCursosActivos / $totalCursos) * 100) 
														: 0;
													?>
													<div style="font-size: 26px; font-weight: 700; color: #8e44ad; margin-top: 5px;">
														<?= $porcentajeActivos; ?>%
													</div>
													<div style="font-size: 11px; color: #95a5a6; margin-top: 4px;">
														Salud general de la oferta académica
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php } ?>
									
									<?php if($totalCursos > 0){ ?>
										<?php include("includes/barra-superior-cursos.php"); ?>
									<?php } ?>

                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
														<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0065'])){?>
															<a href="javascript:void(0);"  data-toggle="modal" data-target="#nuevoCursoModal"  class="btn deepPink-bgcolor">
																<?=__('general.agregar_nuevo');?> <i class="fa fa-plus"></i>
															</a>
														<?php }?>
														
														<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0188'])){?>
															<button type="button" class="btn btn-success" id="btnGenerarCursos">
																<i class="fa fa-magic"></i> Generar Cursos
															</button>
														<?php }?>
													</div>
													
													<?php
													// Incluir modal después de los botones
													if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0065'])){
														$idModal="nuevoCursoModal";															
														$contenido="../directivo/cursos-agregar-modal.php"; 
														include("../compartido/contenido-modal.php");
													}
													?>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
														<th style="width:40px;"></th>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=__('cursos.formato_boletin');?></th>
														<th><?=__('cursos.matricula');?></th>
														<th><?=__('cursos.pension');?></th>														
														<th>#P</th>
														<?php if(array_key_exists(10,$arregloModulos) ){?>
															<th><?=$frases[53][$datosUsuarioActual['uss_idioma']];?></th>
														<?php }?>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php	
													$tipo=NULL;

													if (!empty($_GET['tipo'])) {
														$tipo = base64_decode($_GET['tipo']);
													}

													$consulta = Grados::listarGrados(1,$tipo);
													$contReg = 1;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>
													<tr>
														<td style="text-align:center;">
															<button 
																type="button" 
																class="btn btn-link btn-sm text-secondary expand-curso-btn" 
																data-id="<?=$resultado['gra_id'];?>" 
																title="Ver detalles del curso"
															>
																<i class="fa fa-chevron-right"></i>
															</button>
														</td>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['gra_id'];?></td>
														<td><?=$resultado['gra_nombre'];?></td>
														<td><?=$resultado['gra_formato_boletin'];?></td>
														<td>$<?=number_format($resultado['gra_valor_matricula']);?></td>
														<td>$<?=number_format($resultado['gra_valor_pension']);?></td>														
														<td><?=$resultado['gra_periodos'];?></td>
														<?php if(array_key_exists(10,$arregloModulos) ){?>
															<td><?=strtoupper($resultado['gra_tipo']);?></td>
														<?php }?>
														<td>
															<div class="btn-group">
																  <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	<?php if(Modulos::validarPermisoEdicion()){?>
																		<?php if(Modulos::validarSubRol(['DT0064'])){?>
																		<li><a href="javascript:void(0);" class="btn-editar-curso-modal" data-curso-id="<?=$resultado['gra_id'];?>"><i class="fa fa-edit"></i> Edición rápida</a></li>
																		<li><a href="cursos-editar.php?id=<?=base64_encode($resultado['gra_id']);?>"><i class="fa fa-pencil"></i> <?=$frases[165][$datosUsuarioActual['uss_idioma']];?> completa</a></li>
																		<?php } if(Modulos::validarSubRol(['DT0158'])){?>
																		<li><a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','¿Deseas eliminar este curso?','question','cursos-eliminar.php?id=<?=base64_encode($resultado['gra_id']);?>')">Eliminar</a></li>
																	<?php }}?>
																	<?php if(Modulos::validarSubRol(['DT0224'])){?>
																	<li><a href="../compartido/matricula-boletin-curso-<?=$resultado['gra_formato_boletin'];?>.php?curso=<?=base64_encode($resultado['gra_id']);?>&periodo=<?=base64_encode($config[2]);?>" title="Imprimir boletin por curso" target="_blank">Boletin por curso</a></li>
                                                        			<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0250'])){?>
																	<li><a href="../compartido/indicadores-perdidos-curso.php?curso=<?=base64_encode($resultado['gra_id']);?>&periodo=<?=base64_encode($config[2]);?>" title="Imprimir boletin por curso" target="_blank">Indicadores perdidos</a></li>
                                                        			<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0227'])){?>
																	<li><a href="../compartido/matricula-libro-curso-<?=$config['conf_libro_final']?>.php?curso=<?=base64_encode($resultado['gra_id']);?>" title="Imprimir Libro por curso" target="_blank">Libro por curso</a></li>
                                                        			<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0251'])){?>
																	<li><a href="../compartido/matriculas-formato3-curso.php?curso=<?=base64_encode($resultado['gra_id']);?>" title="Hoja de matrícula por curso" target="_blank">Hojas de matrícula</a></li>
                                                        			<?php }?>
																	<?php if(Modulos::validarSubRol(['DT0343'])){?>
																	<li><a href="comportamiento.php?curso=<?=base64_encode($resultado['gra_id']);?>" title="Observaciones de comportamiento registradas">Comportamiento</a></li>
                                                        			<?php }?>
																  </ul>
															  </div>
														</td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								
								
							
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- SweetAlert2 -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- end js include path -->

<!-- Modal de Generar Cursos Automáticamente -->
<div class="modal fade" id="modalGenerarCursos" tabindex="-1" role="dialog" aria-labelledby="modalGenerarCursosLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h5 class="modal-title" id="modalGenerarCursosLabel">
					<i class="fa fa-magic"></i> Generar Cursos Automáticamente
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formGenerarCursos">
				<div class="modal-body">
					<div class="alert alert-success">
						<i class="fa fa-info-circle"></i> 
						<strong>Instrucciones:</strong> Selecciona qué niveles de cursos deseas generar automáticamente. 
						Puedes seleccionar uno o varios niveles según tus necesidades.
					</div>
					
					<!-- Opciones de niveles -->
					<div class="form-group">
						<label><strong>Selecciona los niveles a generar:</strong></label>
						
						<div class="form-check mb-3">
							<input class="form-check-input" type="checkbox" id="generarPreescolar" name="niveles[]" value="preescolar">
							<label class="form-check-label" for="generarPreescolar">
								<strong><i class="fa fa-baby"></i> Preescolar</strong>
								<br>
								<small class="text-muted">Se crearán 4 cursos: Párvulos, Prejardín, Jardín y Transición</small>
							</label>
						</div>
						
						<div class="form-check mb-3">
							<input class="form-check-input" type="checkbox" id="generarPrimaria" name="niveles[]" value="primaria">
							<label class="form-check-label" for="generarPrimaria">
								<strong><i class="fa fa-child"></i> Primaria (Grados 1° a 5°)</strong>
								<br>
								<small class="text-muted">Se crearán 5 cursos: Primero, Segundo, Tercero, Cuarto y Quinto</small>
							</label>
						</div>
						
						<div class="form-check mb-3">
							<input class="form-check-input" type="checkbox" id="generarSecundaria" name="niveles[]" value="secundaria">
							<label class="form-check-label" for="generarSecundaria">
								<strong><i class="fa fa-graduation-cap"></i> Secundaria (Grados 6° a 9°)</strong>
								<br>
								<small class="text-muted">Se crearán 4 cursos: Sexto, Séptimo, Octavo y Noveno</small>
							</label>
						</div>
						
						<div class="form-check mb-3">
							<input class="form-check-input" type="checkbox" id="generarMedia" name="niveles[]" value="media">
							<label class="form-check-label" for="generarMedia">
								<strong><i class="fa fa-university"></i> Media (Grados 10° y 11°)</strong>
								<br>
								<small class="text-muted">Se crearán 2 cursos: Décimo y Undécimo</small>
							</label>
						</div>
					</div>
					
					<hr>
					
					<div class="alert alert-info">
						<i class="fa fa-lightbulb"></i> 
						<strong>Nota:</strong> Los cursos se crearán con la configuración por defecto. Puedes editarlos posteriormente para ajustar valores de matrícula, pensión, y otros detalles.
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-success" id="btnConfirmarGenerarCursos">
						<i class="fa fa-magic"></i> Generar Cursos Seleccionados
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// ========================================
	// SISTEMA DE GENERAR CURSOS AUTOMÁTICAMENTE
	// ========================================
	
	// Abrir modal de generar cursos
	$('#btnGenerarCursos').on('click', function() {
		$('#modalGenerarCursos').modal('show');
	});
	
	// Enviar formulario de generar cursos
	$('#formGenerarCursos').on('submit', function(e) {
		e.preventDefault();
		
		// Verificar que al menos un nivel esté seleccionado
		var nivelesSeleccionados = [];
		$('input[name="niveles[]"]:checked').each(function() {
			nivelesSeleccionados.push($(this).val());
		});
		
		if (nivelesSeleccionados.length === 0) {
			$.toast({
				heading: 'Advertencia',
				text: 'Debes seleccionar al menos un nivel para generar cursos.',
				showHideTransition: 'slide',
				icon: 'warning',
				position: 'top-right'
			});
			return;
		}
		
		// Calcular total de cursos a crear
		var totalCursos = 0;
		if (nivelesSeleccionados.includes('preescolar')) totalCursos += 4;
		if (nivelesSeleccionados.includes('primaria')) totalCursos += 5;
		if (nivelesSeleccionados.includes('secundaria')) totalCursos += 4;
		if (nivelesSeleccionados.includes('media')) totalCursos += 2;
		
		// Confirmar acción
		Swal.fire({
			title: '¿Confirmar generación?',
			html: `Se crearán <strong>${totalCursos} cursos</strong> automáticamente.<br><br>¿Deseas continuar?`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#28a745',
			cancelButtonColor: '#6c757d',
			confirmButtonText: '<i class="fa fa-check"></i> Sí, generar',
			cancelButtonText: '<i class="fa fa-times"></i> Cancelar'
		}).then((result) => {
			if (result.isConfirmed) {
				generarCursosAutomaticamente(nivelesSeleccionados);
			}
		});
	});
	
	function generarCursosAutomaticamente(niveles) {
		// Deshabilitar botón y mostrar loader
		var btnOriginal = $('#btnConfirmarGenerarCursos').html();
		$('#btnConfirmarGenerarCursos').html('<i class="fa fa-spinner fa-spin"></i> Generando...').prop('disabled', true);
		
		// Mostrar toast de procesamiento
		$.toast({
			heading: 'Generando cursos',
			text: 'Por favor espera mientras se crean los cursos...',
			showHideTransition: 'slide',
			icon: 'info',
			position: 'top-right',
			hideAfter: false,
			loader: true,
			loaderBg: '#28a745'
		});
		
		// Enviar datos por AJAX
		$.ajax({
			url: 'cursos-generar-automaticos.php',
			type: 'POST',
			data: {
				niveles: niveles
			},
			dataType: 'json',
			success: function(response) {
				$('#btnConfirmarGenerarCursos').html(btnOriginal).prop('disabled', false);
				
				// Cerrar toast de procesamiento
				$('.jq-toast-wrap').remove();
				
				if (response.success) {
					// Cerrar modal
					$('#modalGenerarCursos').modal('hide');
					
					// Limpiar checkboxes
					$('input[name="niveles[]"]').prop('checked', false);
					
					// Mostrar mensaje de éxito con SweetAlert
					Swal.fire({
						title: '¡Cursos Generados!',
						html: response.message,
						icon: 'success',
						confirmButtonColor: '#28a745',
						confirmButtonText: '<i class="fa fa-check"></i> Aceptar'
					}).then(() => {
						// Recargar la página
						location.reload();
					});
				} else {
					Swal.fire({
						title: 'Error',
						text: response.message || 'No se pudieron generar los cursos.',
						icon: 'error',
						confirmButtonColor: '#dc3545',
						confirmButtonText: '<i class="fa fa-check"></i> Aceptar'
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX:', error);
				console.error('Response:', xhr.responseText);
				
				$('#btnConfirmarGenerarCursos').html(btnOriginal).prop('disabled', false);
				
				// Cerrar toast de procesamiento
				$('.jq-toast-wrap').remove();
				
				Swal.fire({
					title: 'Error de Conexión',
					text: 'No se pudo conectar con el servidor.',
					icon: 'error',
					confirmButtonColor: '#dc3545',
					confirmButtonText: '<i class="fa fa-check"></i> Aceptar'
				});
			}
		});
	}
	
	// ========================================
	// DETALLES EXPANDIBLES POR CURSO
	// ========================================
	$(document).on('click', '.expand-curso-btn', function() {
		var id   = $(this).data('id');
		var btn  = $(this);
		var icon = btn.find('i');
		var tr   = btn.closest('tr');

		// Buscar si ya existe una fila de detalles inmediatamente después
		var row  = tr.next('tr.expandable-curso-row');

		if (row.length && row.is(':visible')) {
			// Contraer: ocultar y eliminar la fila de detalles
			row.slideUp(200, function() {
				$(this).remove();
				icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
				btn.removeClass('text-primary').addClass('text-secondary');
			});
			return;
		}

		// Si ya existe pero está oculta (caso raro), eliminarla para recrearla limpia
		if (row.length && !row.is(':visible')) {
			row.remove();
		}

		// Calcular cuántas columnas tiene la fila principal
		var colCount = tr.children('td').length;

		// Crear HTML de la fila expandible dinámicamente
		var detallesRowHtml  = '<tr id="expand-curso-' + id + '" class="expandable-curso-row">';
		detallesRowHtml     += '  <td colspan="' + colCount + '">';
		detallesRowHtml     += '    <div class="p-3">';
		detallesRowHtml     += '      <div id="curso-detalles-' + id + '">';
		detallesRowHtml     += '        <p class="text-center mb-0">';
		detallesRowHtml     += '          <i class="fa fa-spinner fa-spin"></i> Cargando detalles del curso...';
		detallesRowHtml     += '        </p>';
		detallesRowHtml     += '      </div>';
		detallesRowHtml     += '    </div>';
		detallesRowHtml     += '  </td>';
		detallesRowHtml     += '</tr>';

		// Insertar la fila inmediatamente después de la fila del curso
		tr.after(detallesRowHtml);

		// Obtener referencia a la nueva fila y al contenedor de detalles
		row = tr.next('tr.expandable-curso-row');

		row.hide().slideDown(200, function() {
			icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
			btn.removeClass('text-secondary').addClass('text-primary');

			var contenedor = $('#curso-detalles-' + id);
			cargarDetallesCurso(id, contenedor);
		});
	});

	function cargarDetallesCurso(idCurso, contenedor) {
		$.ajax({
			url: 'ajax-obtener-detalles-curso.php',
			type: 'POST',
			data: { curso_id: idCurso },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					renderizarDetallesCurso(response.stats, contenedor);
				} else {
					contenedor.html('<p class="text-danger text-center"><i class="fa fa-exclamation-triangle"></i> ' + (response.message || 'Error al cargar detalles.') + '</p>');
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX curso:', error);
				contenedor.html('<p class="text-danger text-center"><i class="fa fa-exclamation-triangle"></i> Error de conexión.</p>');
			}
		});
	}

	function renderizarDetallesCurso(stats, contenedor) {
		var total = stats.total || 0;
		var html  = '';

		if (total === 0) {
			html = '<p class="text-muted text-center mb-0"><em>No hay estudiantes asociados a este curso en el año actual.</em></p>';
		} else {
			var porcentaje = function(valor) {
				if (!total || valor === 0) return '0%';
				return Math.round((valor / total) * 100) + '%';
			};

			html += '<div class="row">';

			html += '<div class="col-md-2 col-sm-4 mb-2">';
			html += '  <div class="card" style="border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
			html += '    <div class="card-body p-2 text-center">';
			html += '      <div style="font-size:11px; text-transform:uppercase; color:#7f8c8d;">Total</div>';
			html += '      <div style="font-size:20px; font-weight:700; color:#2c3e50;">' + total + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			html += '<div class="col-md-2 col-sm-4 mb-2">';
			html += '  <div class="card" style="border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
			html += '    <div class="card-body p-2 text-center">';
			html += '      <div style="font-size:11px; text-transform:uppercase; color:#7f8c8d;">Matriculados</div>';
			html += '      <div style="font-size:18px; font-weight:700; color:#16a085;">' + (stats.matriculados || 0) + '</div>';
			html += '      <div style="font-size:10px; color:#95a5a6;">' + porcentaje(stats.matriculados || 0) + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			html += '<div class="col-md-2 col-sm-4 mb-2">';
			html += '  <div class="card" style="border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
			html += '    <div class="card-body p-2 text-center">';
			html += '      <div style="font-size:11px; text-transform:uppercase; color:#7f8c8d;">Asistentes</div>';
			html += '      <div style="font-size:18px; font-weight:700; color:#2980b9;">' + (stats.asistentes || 0) + '</div>';
			html += '      <div style="font-size:10px; color:#95a5a6;">' + porcentaje(stats.asistentes || 0) + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			html += '<div class="col-md-2 col-sm-4 mb-2">';
			html += '  <div class="card" style="border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
			html += '    <div class="card-body p-2 text-center">';
			html += '      <div style="font-size:11px; text-transform:uppercase; color:#7f8c8d;">Cancelados</div>';
			html += '      <div style="font-size:18px; font-weight:700; color:#c0392b;">' + (stats.cancelados || 0) + '</div>';
			html += '      <div style="font-size:10px; color:#95a5a6;">' + porcentaje(stats.cancelados || 0) + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			html += '<div class="col-md-3 col-sm-6 mb-2">';
			html += '  <div class="card" style="border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
			html += '    <div class="card-body p-2 text-center">';
			html += '      <div style="font-size:11px; text-transform:uppercase; color:#7f8c8d;">No matriculados</div>';
			html += '      <div style="font-size:18px; font-weight:700; color:#7f8c8d;">' + (stats.no_matriculados || 0) + '</div>';
			html += '      <div style="font-size:10px; color:#95a5a6;">' + porcentaje(stats.no_matriculados || 0) + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			html += '<div class="col-md-3 col-sm-6 mb-2">';
			html += '  <div class="card" style="border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
			html += '    <div class="card-body p-2 text-center">';
			html += '      <div style="font-size:11px; text-transform:uppercase; color:#7f8c8d;">En inscripción</div>';
			html += '      <div style="font-size:18px; font-weight:700; color:#8e44ad;">' + (stats.inscritos || 0) + '</div>';
			html += '      <div style="font-size:10px; color:#95a5a6;">' + porcentaje(stats.inscritos || 0) + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			html += '</div>';
		}

		contenedor.html(html);
	}

	// ========================================
	// FIN SISTEMA GENERAR CURSOS + DETALLES
	// ========================================
});
</script>

<!-- Modal para edición rápida de curso -->
<div class="modal fade" id="modalEditarCurso" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-edit"></i> Edición Rápida de Curso</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="formEditarCurso" action="cursos-actualizar.php" method="post">
				<div class="modal-body">
					<div id="cursoLoader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="cursoFormulario" style="display:none;">
						<input type="hidden" id="edit_id_curso" name="id_curso">
						<input type="hidden" id="edit_tipoG" name="tipoG">
						
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Código</label>
									<input type="text" class="form-control" id="edit_codigoC" name="codigoC">
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<label>Nombre Curso <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="edit_nombreC" name="nombreC" required>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Formato Boletín <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_formatoB" name="formatoB" required>
										<option value="">Seleccione...</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Estado <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_estado" name="estado" required>
										<option value="1">Activo</option>
										<option value="0">Inactivo</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label>Grado Siguiente</label>
							<select class="form-control" id="edit_graSiguiente" name="graSiguiente">
								<option value="">Seleccione...</option>
							</select>
						</div>
						
						<!-- Campos ocultos requeridos -->
						<input type="hidden" id="edit_valorM" name="valorM">
						<input type="hidden" id="edit_valorP" name="valorP">
						<input type="hidden" id="edit_graAnterior" name="graAnterior">
						<input type="hidden" id="edit_notaMin" name="notaMin">
						<input type="hidden" id="edit_periodosC" name="periodosC">
						<input type="hidden" id="edit_nivel" name="nivel">
						<input type="hidden" id="edit_descripcion" name="descripcion">
						<input type="hidden" id="edit_contenido" name="contenido">
						<input type="hidden" id="edit_precio" name="precio">
						<input type="hidden" id="edit_minEstudiantes" name="minEstudiantes">
						<input type="hidden" id="edit_maxEstudiantes" name="maxEstudiantes">
						<input type="hidden" id="edit_horas" name="horas">
						<input type="hidden" id="edit_autoenrollment" name="autoenrollment">
						<input type="hidden" id="edit_activo" name="activo">
					</div>
					
					<div id="cursoError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensajeCurso"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-save"></i> Guardar Cambios
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$(document).on('click', '.btn-editar-curso-modal', function() {
		var cursoId = $(this).data('curso-id');
		
		$('#cursoLoader').show();
		$('#cursoFormulario').hide();
		$('#cursoError').hide();
		$('#modalEditarCurso').modal('show');
		
		$.ajax({
			url: 'ajax-obtener-datos-curso.php',
			type: 'POST',
			data: { curso_id: cursoId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var curso = response.curso;
					console.log('Datos del curso:', curso);
					console.log('Estado del curso:', curso.gra_estado);
					
					$('#edit_id_curso').val(curso.gra_id);
					$('#edit_codigoC').val(curso.gra_codigo || curso.gra_id);
					$('#edit_nombreC').val(curso.gra_nombre);
					
					// Forzar el valor del estado como string
					var estadoActual = (curso.gra_estado !== undefined && curso.gra_estado !== null) ? String(curso.gra_estado) : '1';
					console.log('Estado a establecer:', estadoActual);
					$('#edit_estado').val(estadoActual);
					
					// Llenar campos ocultos con valores reales
					$('#edit_valorM').val(curso.gra_valor_matricula || '0');
					$('#edit_valorP').val(curso.gra_valor_pension || '0');
					$('#edit_tipoG').val(curso.gra_tipo || '1');
					$('#edit_graAnterior').val(curso.gra_grado_anterior || '');
					$('#edit_notaMin').val(curso.gra_nota_minima || '3');
					$('#edit_periodosC').val(curso.gra_periodos || '4');
					$('#edit_nivel').val(curso.gra_nivel || '1');
					$('#edit_descripcion').val(curso.gra_overall_description || '');
					$('#edit_contenido').val(curso.gra_course_content || '');
					$('#edit_precio').val(curso.gra_price || '0');
					$('#edit_minEstudiantes').val(curso.gra_minimum_quota || '0');
					$('#edit_maxEstudiantes').val(curso.gra_maximum_quota || '0');
					$('#edit_horas').val(curso.gra_duration_hours || '0');
					$('#edit_autoenrollment').val(curso.gra_auto_enrollment || '0');
					$('#edit_activo').val(curso.gra_active || '0');
					
					// Llenar select de formatos de boletín
					$('#edit_formatoB').empty().append('<option value="">Seleccione...</option>');
					response.formatos.forEach(function(formato) {
						var selected = (formato.id == curso.gra_formato_boletin) ? 'selected' : '';
						$('#edit_formatoB').append('<option value="' + formato.id + '" ' + selected + '>' + formato.nombre + '</option>');
					});
					
					// Llenar select de grados siguientes
					$('#edit_graSiguiente').empty().append('<option value="">Seleccione...</option>');
					response.grados.forEach(function(grado) {
						var selected = (grado.id == curso.gra_grado_siguiente) ? 'selected' : '';
						$('#edit_graSiguiente').append('<option value="' + grado.id + '" ' + selected + '>' + grado.nombre + '</option>');
					});
					
					$('#cursoLoader').hide();
					$('#cursoFormulario').show();
				} else {
					$('#cursoLoader').hide();
					$('#errorMensajeCurso').text(response.message);
					$('#cursoError').show();
				}
			},
			error: function() {
				$('#cursoLoader').hide();
				$('#errorMensajeCurso').text('Error de conexión');
				$('#cursoError').show();
			}
		});
	});
	
	$('#formEditarCurso').on('submit', function(e) {
		e.preventDefault();
		
		var formData = $(this).serialize();
		console.log('Datos a enviar:', formData);
		console.log('Estado seleccionado:', $('#edit_estado').val());
		
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: formData,
			success: function(response) {
				console.log('Respuesta del servidor:', response);
				
				// Verificar si la respuesta contiene un redirect
				if (response.includes('window.location.href')) {
					// La actualización fue exitosa
					$.toast({
						heading: 'Éxito',
						text: 'Curso actualizado correctamente',
						position: 'top-right',
						loaderBg: '#26c281',
						icon: 'success',
						hideAfter: 3000,
						stack: 6
					});
					$('#modalEditarCurso').modal('hide');
					setTimeout(function() {
						location.reload();
					}, 1000);
				} else {
					// Mostrar error si no hay redirect
					$.toast({
						heading: 'Error',
						text: 'Error al actualizar el curso',
						position: 'top-right',
						loaderBg: '#e74c3c',
						icon: 'error',
						hideAfter: 5000,
						stack: 6
					});
				}
			},
			error: function() {
				$.toast({
					heading: 'Error',
					text: 'Error al actualizar',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error'
				});
			}
		});
	});
});
</script>

</body>

</html>