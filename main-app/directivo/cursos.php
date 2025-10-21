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
								
									<?php include("../../config-general/mensajes-informativos.php"); ?>
									
									<?php include("includes/barra-superior-cursos.php"); ?>
								

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
																Agregar nuevo <i class="fa fa-plus"></i>
															</a>
														<?php 
													$idModal="nuevoCursoModal";															
													$contenido="../directivo/cursos-agregar-modal.php"; 
													include("../compartido/contenido-modal.php");
													}?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Formato boletín</th>
														<th>Matrícula</th>
														<th>Pensión</th>														
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
    <!-- end js include path -->

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