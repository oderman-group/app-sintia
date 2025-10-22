<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0001';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php"); 
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");


Utilidades::validarParametros($_GET);

if (isset($_GET['mode']) && $_GET['mode'] === 'DEV') {
	$redis = RedisInstance::getRedisInstance();

	$arrayTest = [
		[
			'Nombre' => 'Jhon',
			'Edad'   => 33,
			'Genero' => 'M'
		],
		[
			'Nombre' => 'Michelle',
			'Edad'   => 24,
			'Genero' => 'F'
		],
	];

	$redis->set('jhonky', json_encode($arrayTest));
	//echo $redis->ttl('jhonky'); exit();
	print_r(json_decode($redis->get('jhonky'), true));
	echo "<hr>";
	
	$redis->lPush("estudiantes", "Jhon");
	$redis->lPush("estudiantes", "Cristal");
	$redis->lPush("estudiantes", "Michelle");

	$estudiantes = $redis->lRange("estudiantes", 0, 2);
	
	foreach($estudiantes as $valor) {
		echo $valor."<br>";
	}

	exit();
}

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

//$redis = RedisInstance::getRedisInstance();

$jQueryTable = '';
if($config['conf_doble_buscador'] == 1) {
	$jQueryTable = 'id="example1"';
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
	<!-- select2 -->
	<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php
	include("../compartido/body.php");
	include("usuarios-bloquear-modal.php");
?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php"); //1 por otimizar, parece estar repetida ?>
		
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
                                <div class="page-title"><?=$frases[209][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php"); //1 por otimizar, parece estar repetida ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción de la página -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                                <i class="fa fa-info-circle text-info"></i> 
                                Aquí puedes gestionar toda la información de los estudiantes matriculados en la institución. 
                                Utiliza los filtros avanzados para buscar por nombre, documento o usuario, y filtra por curso, grupo o estado de matrícula. 
                                También puedes expandir cada registro para ver información detallada del estudiante.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
								<?php include("../../config-general/mensajes-informativos.php"); ?>
								<span id="respuestaCambiarEstado"></span>

								<?php 
								//include("includes/barra-superior-matriculas.php");	
								// $matKeys = array_slice($keys, $inicio, $registros);
								// foreach ($matKeys as $matKey){
								// 	$matData = $redis->get($matKey);
								// 	$resultado = json_decode($matData, true);
								// }
								// print_r($resultado); exit();
								?>
								
								<?php
								 $filtro="";
								?>

									<?php
									if (Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA)) {
										if (isset($_GET['msgsion'])) {
											$aler = 'alert-danger';
											$mensajeSion = 'Por favor, verifique todos los datos del estudiante y llene los campos vacios.';

											if($_GET['msgsion']!=''){
												$aler = 'alert-success';
												$mensajeSion = base64_decode($_GET['msgsion']);

												if (base64_decode($_GET['stadsion']) != true) {
													$aler='alert-danger';
												}
											}
									?>
										<div class="alert alert-block <?=$aler;?>">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<h4 class="alert-heading">SION!</h4>
											<p><?=$mensajeSion;?></p>
										</div>
									<?php 
										}
									}
									if (isset($_GET['msgsintia'])) {
										$aler='alert-success';

										if ($_GET['stadsintia']!=true) {
											$aler='alert-danger';
										}
									?>
									<div class="alert alert-block <?=$aler;?>">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<h4 class="alert-heading">SINTIA!</h4>
										<p><?=$_GET['msgsintia'];?></p>
									</div>
									<?php }?>

									<!-- Barra de herramientas superior -->
									<div class="row mb-3">
										<div class="col-sm-12">
											<div class="d-flex justify-content-between align-items-center">
												<!-- Botones principales -->
												<div class="btn-group">
													<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0084'])){?>
														<a href="estudiantes-agregar.php" class="btn deepPink-bgcolor">
															<i class="fa fa-plus"></i> Agregar Estudiante
														</a>
													<?php }?>
													
													<?php if(Modulos::validarSubRol(['DT0002'])){?>
														<a href="estudiantes-promedios.php" class="btn btn-info">
															<i class="fa fa-chart-line"></i> Promedios
														</a>
													<?php }?>
													
													<!-- Menú Matrículas -->
													<?php if(Modulos::validarSubRol(['DT0077', 'DT0080', 'DT0075'])){?>
														<div class="btn-group" role="group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
																<i class="fa fa-list"></i> Menú Matrículas <span class="caret"></span>
															</button>
															<ul class="dropdown-menu">
																<?php if(Modulos::validarSubRol(['DT0077'])){?>
																	<li><a href="estudiantes-importar-excel.php"><i class="fa fa-file-excel"></i> Importar desde Excel</a></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0080'])){?>
																	<li><a href="estudiantes-consolidado-final.php"><i class="fa fa-file-alt"></i> Consolidado Final</a></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0075'])){?>
																	<li><a href="estudiantes-nivelaciones.php"><i class="fa fa-balance-scale"></i> Nivelaciones</a></li>
																<?php }?>
															</ul>
														</div>
													<?php }?>
													
													<!-- Más Opciones -->
													<?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0212', 'DT0213', 'DT0214', 'DT0215', 'DT0175', 'DT0216', 'DT0149'])){?>
														<div class="btn-group" role="group">
															<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
																<i class="fa fa-tools"></i> Más Opciones <span class="caret"></span>
															</button>
															<ul class="dropdown-menu">
																<?php if(Modulos::validarSubRol(['DT0212'])){?>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-matricular-todos.php')"><i class="fa fa-check-circle"></i> Matricular a Todos</a></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0213'])){?>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-matriculas-cancelar.php')"><i class="fa fa-times-circle"></i> Cancelar a Todos</a></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0214'])){?>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-grupoa-todos.php')"><i class="fa fa-users"></i> Asignar a Todos al Grupo A</a></li>
																	<li class="divider"></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0215'])){?>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Esta opción removerá a todos lo estudiantes que no estén en estado Matriculado, desea continuar?','question','estudiantes-inactivos-remover.php')"><i class="fa fa-trash"></i> Remover Estudiantes Inactivos</a></li>
																	<li class="divider"></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0175'])){?>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-documento-usuario-actualizar.php')"><i class="fa fa-id-card"></i> Documento como Usuario</a></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0216'])){?>
																	<li><a href="javascript:void(0);" onclick="sweetConfirmacion('Alerta!','Deseas ejecutar esta accion?','question','estudiantes-crear-usuarios.php')"><i class="fa fa-key"></i> Generar Credenciales</a></li>
																<?php }?>
																<?php if(Modulos::validarSubRol(['DT0149'])){?>
																	<li><a href="filtro-general-folio.php"><i class="fa fa-file-pdf"></i> Generar Folios</a></li>
																<?php }?>
															</ul>
														</div>
													<?php }?>
												</div>
												
												<!-- Botón de filtros -->
												<button type="button" class="btn btn-outline-secondary" id="btnToggleFiltros">
													<i class="fa fa-filter"></i> Filtros y Búsqueda
												</button>
											</div>
										</div>
									</div>
											
											<!-- Filtros Mejorados con Multiselect (Colapsable) -->
											<div class="card card-topline-purple mb-3" id="cardFiltros" style="display: none;">
												<div class="card-body">
													<h5 class="mb-3"><i class="fa fa-filter"></i> Filtros y Búsqueda Avanzada</h5>
													
													<!-- Buscador General Potente -->
													<div class="row mb-3">
														<div class="col-md-12">
															<div class="form-group">
																<label><i class="fa fa-search"></i> Buscar Estudiante</label>
																<div class="input-group">
																	<input type="text" id="filtro_busqueda" class="form-control" placeholder="Buscar por nombre completo, apellidos, documento, email o usuario...">
																	<div class="input-group-append">
																		<button class="btn btn-primary" type="button" id="btnBuscar">
																			<i class="fa fa-search"></i> Buscar
																		</button>
																	</div>
																</div>
																<small class="form-text text-muted">
																	<i class="fa fa-info-circle"></i> <strong>Búsqueda potente:</strong> Escribe cualquier combinación de nombres y apellidos, documento, email o usuario. 
																	Ejemplo: "Juan Pérez", "María", "12345678", etc. Presiona Enter o haz clic en "Buscar".
																</small>
															</div>
														</div>
													</div>
													
													<hr>
													
													<!-- Filtros Multiselect -->
													<div class="row">
														<div class="col-md-4">
															<div class="form-group">
																<label><i class="fa fa-graduation-cap"></i> Cursos</label>
																<select id="filtro_cursos" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
																	<?php
																	$grados = Grados::listarGrados(1);
																	while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
																	?>
																		<option value="<?=$grado['gra_id'];?>"><?=$grado['gra_nombre'];?></option>
																	<?php }?>
																</select>
															</div>
														</div>
														
														<div class="col-md-4">
															<div class="form-group">
																<label><i class="fa fa-users"></i> Grupos</label>
																<select id="filtro_grupos" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
																	<?php
																	$grupos = Grupos::listarGrupos();
																	while ($gru = mysqli_fetch_array($grupos, MYSQLI_BOTH)) {
																	?>
																		<option value="<?=$gru['gru_id'];?>"><?=$gru['gru_nombre'];?></option>
																	<?php }?>
																</select>
															</div>
														</div>
														
														<div class="col-md-4">
															<div class="form-group">
																<label><i class="fa fa-info-circle"></i> Estados</label>
																<select id="filtro_estados" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
																	<?php
																	foreach ($estadosMatriculasEstudiantes as $clave => $valor) {
																	?>
																		<option value="<?=$clave;?>"><?=$valor;?></option>
																	<?php }?>
																</select>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="col-md-12 text-right">
															<button type="button" class="btn btn-secondary" id="btnLimpiarFiltros">
																<i class="fa fa-eraser"></i> Limpiar Todo
															</button>
														</div>
													</div>
												</div>
											</div>
											
                                    <div class="card card-topline-purple">
                                        <div class="card-body">
											
											
											
                                        <div>
											
                                    		<table <?=$jQueryTable;?> class="display" style="width:100%;">
												<div id="gifCarga" class="gif-carga">
													<img  alt="Cargando...">
												</div>
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>ID</th>
              <th>Bloq.</th>
              <th><?=$frases[246][$datosUsuarioActual['uss_idioma']];?></th>
              <th><?=$frases[241][$datosUsuarioActual['uss_idioma']];?></th>
              <th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
              <th><?=$frases[26][$datosUsuarioActual['uss_idioma']];?></th>
              <th>Usuario</th>
              <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="matriculas_result">
													<?php
													
													include("includes/consulta-paginacion-estudiantes.php");
													$filtroLimite = 'LIMIT '.$inicio.','.$registros;
													
													$selectSql = ["mat.*",
																  "uss.uss_id","uss.uss_usuario","uss.uss_bloqueado",
																  "gra_nombre","gru_nombre","gra_formato_boletin",
																  "acud.uss_nombre","acud.uss_nombre2","acud.uss_nombre2", "mat.id_nuevo AS mat_id_nuevo",
																  "og_tipo_doc.ogen_nombre as tipo_doc_nombre",
																  "og_genero.ogen_nombre as genero_nombre",
																  "og_estrato.ogen_nombre as estrato_nombre",
																  "og_tipo_sangre.ogen_nombre as tipo_sangre_nombre"];

													$consulta = Estudiantes::listarEstudiantes(0, $filtro, $filtroLimite, $cursoActual,null,$selectSql);
													
													$contReg = 1;

													$index = 0;
													$arraysDatos = array();
													if (!empty($consulta)) {
														while ($fila = $consulta->fetch_assoc()) {
															$arraysDatos[$index] = $fila;
															$index++;
														}
														$consulta->free();
													}
													$lista = $arraysDatos;
													$data["data"] =$lista;
													include(ROOT_PATH . "/main-app/class/componentes/result/matriculas-tbody.php");
													  ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                      				<?php include("enlaces-paginacion.php");?>
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
	<!-- select2 -->
	<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <!-- end js include path -->
	<script>
		$(function () {
			$('[data-toggle="popover"]').popover();
		});

		$('.popover-dismiss').popover({trigger: 'focus'});
		
		// === Filtros Mejorados con Multiselect ===
		
		$(document).ready(function() {
			// Toggle de los filtros
			$('#btnToggleFiltros').on('click', function() {
				const card = $('#cardFiltros');
				const icon = $(this).find('i');
				
				if (card.is(':visible')) {
					card.slideUp(300);
					icon.removeClass('fa-chevron-up').addClass('fa-filter');
					$(this).removeClass('btn-primary').addClass('btn-outline-secondary');
				} else {
					card.slideDown(300);
					icon.removeClass('fa-filter').addClass('fa-chevron-up');
					$(this).removeClass('btn-outline-secondary').addClass('btn-primary');
				}
			});
			
			// Inicializar Select2 en los filtros
			$('.select2-multiple').select2({
				placeholder: "Seleccione una o más opciones",
				allowClear: true,
				language: {
					noResults: function() {
						return "No se encontraron resultados";
					},
					searching: function() {
						return "Buscando...";
					}
				}
			});
			
			// Función para aplicar filtros
			function aplicarFiltros() {
				const cursos = $('#filtro_cursos').val() || [];
				const grupos = $('#filtro_grupos').val() || [];
				const estados = $('#filtro_estados').val() || [];
				const busqueda = $('#filtro_busqueda').val() || '';
				
				console.log('Aplicando filtros:', { cursos, grupos, estados, busqueda });
				
				// Mostrar loader
				$('#gifCarga').show();
				$('#matriculas_result').html('<tr><td colspan="9" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Cargando...</td></tr>');
				
				// Enviar AJAX
				$.ajax({
					url: 'ajax-filtrar-estudiantes.php',
					type: 'POST',
					data: {
						cursos: cursos,
						grupos: grupos,
						estados: estados,
						busqueda: busqueda
					},
					dataType: 'json',
					success: function(response) {
						console.log('Respuesta del filtro:', response);
						
						$('#gifCarga').hide();
						
						if (response.success) {
							// Insertar el HTML
							$('#matriculas_result').html(response.html);
							
							// Forzar que TODAS las filas expandibles estén completamente ocultas
							$('#matriculas_result tr.expandable-row').each(function() {
								$(this).hide();
								$(this).css('display', 'none');
								$(this).attr('style', 'display: none !important;');
							});
							
							// Resetear todos los botones al estado inicial
							$('.expand-btn').removeClass('text-primary').addClass('text-secondary');
							$('.expand-btn i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
							
							console.log('Filas expandibles después de filtrar:', $('#matriculas_result tr.expandable-row').length);
							console.log('Filas visibles:', $('#matriculas_result tr.expandable-row:visible').length);
							
							// Mostrar mensaje de resultados
							let mensajeResultado = 'Se encontraron ' + response.total + ' estudiante(s)';
							if ($('#filtro_busqueda').val()) {
								mensajeResultado += ' con "' + $('#filtro_busqueda').val() + '"';
							}
							
							$.toast({
								heading: 'Filtros Aplicados',
								text: mensajeResultado,
								position: 'top-right',
								loaderBg: '#26c281',
								icon: 'success',
								hideAfter: 3000
							});
						} else {
							$.toast({
								heading: 'Error',
								text: response.error || 'Error al aplicar filtros',
								position: 'top-right',
								loaderBg: '#bf441d',
								icon: 'error',
								hideAfter: 5000
							});
							
							$('#matriculas_result').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar los datos</td></tr>');
						}
					},
					error: function(xhr, status, error) {
						console.error('Error AJAX:', status, error);
						console.error('Response:', xhr.responseText);
						
						$('#gifCarga').hide();
						
						$.toast({
							heading: 'Error de Conexión',
							text: 'No se pudo conectar con el servidor',
							position: 'top-right',
							loaderBg: '#bf441d',
							icon: 'error',
							hideAfter: 5000
						});
						
						$('#matriculas_result').html('<tr><td colspan="9" class="text-center text-danger">Error de conexión</td></tr>');
					}
				});
			}
			
			// Aplicar filtros al hacer clic en el botón
			$('#btnAplicarFiltros').on('click', function() {
				aplicarFiltros();
			});
			
			// Limpiar filtros
			$('#btnLimpiarFiltros').on('click', function() {
				$('#filtro_cursos').val(null).trigger('change');
				$('#filtro_grupos').val(null).trigger('change');
				$('#filtro_estados').val(null).trigger('change');
				
				// Recargar la página para mostrar todos los estudiantes
				location.reload();
			});
			
			// Aplicar filtros automáticamente al cambiar las opciones
			$('.select2-multiple').on('change', function() {
				// Aplicar filtros después de un breve delay para evitar múltiples llamadas
				clearTimeout(window.filtroTimeout);
				window.filtroTimeout = setTimeout(function() {
					aplicarFiltros();
				}, 500);
			});
			
			// Búsqueda al hacer clic en el botón
			$('#btnBuscar').on('click', function() {
				aplicarFiltros();
			});
			
			// Búsqueda al presionar Enter
			$('#filtro_busqueda').on('keypress', function(e) {
				if (e.which === 13) { // Enter key
					e.preventDefault();
					aplicarFiltros();
				}
			});
		});
	</script>
</body>

</html>