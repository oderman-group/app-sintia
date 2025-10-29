<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0032';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

//Utilidades::validarParametros($_GET);

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once("../class/Estudiantes.php");
require_once("../class/Sysjobs.php");
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
	<style>
		/* Ocultar paginación de DataTable en la página de cargas */
		#example1_paginate,
		#example1_length {
			display: none !important;
		}
		
		/* Ajustar el info de DataTable para que no ocupe espacio innecesario */
		#example1_info {
			display: none !important;
		}
		
		/* Mantener visible el buscador de DataTable */
		#example1_filter {
			margin-bottom: 15px;
		}
		
		/* Mejorar el espaciado de la tabla */
		.dataTables_wrapper {
			padding-top: 10px;
		}
		
		/* Estilo sutil para el botón de expandir */
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
		
		/* Estilos para filas seleccionadas - Edición Masiva */
		.carga-row-selected {
			background-color: #e3f2fd !important;
			transition: background-color 0.3s ease;
		}
		
		.carga-row-selected:hover {
			background-color: #bbdefb !important;
		}
		
		/* Estilos mejorados para el modal de edición masiva */
		#editarMasivoModal .form-group {
			margin-bottom: 20px;
		}
		
		#editarMasivoModal .form-group label {
			font-weight: 600;
			font-size: 14px;
			margin-bottom: 8px;
			color: #495057;
			display: block;
		}
		
		#editarMasivoModal .form-control,
		#editarMasivoModal .select2-container {
			width: 100% !important;
			min-height: 38px;
		}
		
		#editarMasivoModal .select2-container .select2-selection--single {
			height: 38px !important;
			padding: 6px 12px;
			border: 1px solid #ced4da;
			border-radius: 4px;
		}
		
		#editarMasivoModal .select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 26px;
			color: #495057;
		}
		
		#editarMasivoModal .select2-container--default .select2-selection--single .select2-selection__arrow {
			height: 36px;
		}
		
		#editarMasivoModal input[type="number"] {
			height: 38px;
			padding: 6px 12px;
			font-size: 14px;
		}
		
		#editarMasivoModal .row {
			margin-bottom: 10px;
		}
		
		#editarMasivoModal hr {
			margin: 25px 0;
			border-top: 2px solid #dee2e6;
		}
		
		#editarMasivoModal h5 {
			margin-bottom: 15px;
			font-size: 16px;
			font-weight: 600;
		}
	</style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	<div id="overlayInforme">
		<div id="loader"></div>
		<div id="loading-text">Generando informe…</div>
	</div>
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
                                <div class="page-title"><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción de la página -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                                <i class="fa fa-info-circle text-info"></i> 
                                Gestiona las cargas académicas de los docentes. Aquí puedes visualizar y administrar las asignaturas asignadas a cada docente por curso y grupo. 
                                Utiliza los filtros avanzados para buscar por docente, curso, grupo o asignatura. Expande cada carga para ver información detallada sobre actividades y estudiantes.
                            </p>
                        </div>
                    </div>
                    
                    <?php
                        include("../../config-general/mensajes-informativos.php");
                    ?>
                    
                    <!-- Barra de herramientas superior - FUERA DEL CARD -->
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Botones principales -->
                                <div class="btn-group">
                                    <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0052'])) { ?>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#nuevaCargModal" class="btn deepPink-bgcolor">
                                            <i class="fa fa-plus"></i> Nueva Carga
                                        </a>
                                        <?php
                                        $idModal = "nuevaCargModal";
                                        $contenido = "../directivo/cargas-agregar-modal.php";
                                        include("../compartido/contenido-modal.php");
                                        } ?>
                                    
                                    <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0032'])) { ?>
                                        <button type="button" id="moverCargasBtn" class="btn btn-info" disabled>
                                            <i class="fa fa-arrows-alt"></i> Mover Seleccionadas
                                        </button>
                                        
                                        <button type="button" id="editarMasivoBtn" class="btn btn-warning" disabled>
                                            <i class="fa fa-edit"></i> Editar Seleccionadas
                                        </button>
                                    <?php } ?>
                                    
                                    <!-- Más Opciones -->
                                    <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0035'])){?>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                                <i class="fa fa-tools"></i> Más Opciones <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0032'])) { ?>
                                                    <li><a href="cargas-visual.php"><i class="fa fa-th-large"></i> Vista Visual</a></li>
                                                    <li role="separator" class="divider"></li>
                                                <?php } ?>
                                                <li><a href="cargas-informe-progreso.php" target="_blank"><i class="fa fa-file-alt"></i> Informe de Progreso</a></li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="cargas-indicadores-obligatorios.php"><i class="fa fa-list-check"></i> Indicadores Obligatorios</a></li>
                                                <li><a href="cargas-comportamiento-filtros.php"><i class="fa fa-user-check"></i> Notas de Comportamiento</a></li>
                                                <li><a href="javascript:void(0);" data-toggle="modal" data-target="#modalTranferirCargas"><i class="fa fa-exchange-alt"></i> Transferir Cargas</a></li>
                                                <li><a href="cargas-estilo-notas.php"><i class="fa fa-palette"></i> Estilo de Notas</a></li>
                                            </ul>
                                        </div>
                                    <?php }else{ ?>
                                        <!-- Si no tiene permiso para DT0035, mostrar solo las opciones básicas -->
                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0032'])) { ?>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-tools"></i> Más Opciones <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="cargas-visual.php"><i class="fa fa-th-large"></i> Vista Visual</a></li>
                                                    <li role="separator" class="divider"></li>
                                                    <li><a href="cargas-informe-progreso.php" target="_blank"><i class="fa fa-file-alt"></i> Informe de Progreso</a></li>
                                                </ul>
                                            </div>
                                        <?php }else{ ?>
                                            <a href="cargas-informe-progreso.php" target="_blank" class="btn btn-info" title="Ver informe de progreso de notas">
                                                <i class="fa fa-file-alt"></i> Informe de Progreso
                                            </a>
                                        <?php } ?>
                                    <?php }?>
                                </div>
                                
                                <!-- Botón de filtros -->
                                <button type="button" class="btn btn-outline-secondary" id="btnToggleFiltrosCargas">
                                    <i class="fa fa-filter"></i> Filtros Avanzados
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel de Filtros Colapsable -->
                    <div class="card card-topline-purple mb-3" id="cardFiltrosCargas" style="display: none;">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fa fa-filter"></i> Filtros Avanzados</h5>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-graduation-cap"></i> Cursos</label>
                                        <select id="filtro_cargas_cursos" class="form-control select2-multiple-cargas" multiple="multiple" style="width: 100%;">
                                            <?php
                                            $grados = Grados::listarGrados(1);
                                            while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
                                            ?>
                                                <option value="<?=$grado['gra_id'];?>"><?=$grado['gra_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-users"></i> Grupos</label>
                                        <select id="filtro_cargas_grupos" class="form-control select2-multiple-cargas" multiple="multiple" style="width: 100%;">
                                            <?php
                                            $grupos = Grupos::listarGrupos();
                                            while ($gru = mysqli_fetch_array($grupos, MYSQLI_BOTH)) {
                                            ?>
                                                <option value="<?=$gru['gru_id'];?>"><?=$gru['gru_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-chalkboard-teacher"></i> Docentes</label>
                                        <select id="filtro_cargas_docentes" class="form-control select2-multiple-cargas" multiple="multiple" style="width: 100%;">
                                            <?php
                                            try {
                                                $consultaDocentes = mysqli_query($conexion, "SELECT uss_id, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2 
                                                    FROM ".BD_GENERAL.".usuarios 
                                                    WHERE uss_tipo=".TIPO_DOCENTE." AND institucion={$config['conf_id_institucion']} AND year={$_SESSION['bd']}
                                                    ORDER BY uss_nombre ASC");
                                                
                                                while ($doc = mysqli_fetch_array($consultaDocentes, MYSQLI_BOTH)) {
                                                    $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($doc);
                                                ?>
                                                    <option value="<?=$doc['uss_id'];?>"><?=$nombreCompleto;?></option>
                                                <?php 
                                                }
                                            } catch(Exception $e) {
                                                echo "<!-- Error al cargar docentes: " . $e->getMessage() . " -->";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-calendar"></i> Periodos</label>
                                        <select id="filtro_cargas_periodos" class="form-control select2-multiple-cargas" multiple="multiple" style="width: 100%;">
                                            <?php for($i=1; $i<=$config['conf_periodos_maximos']; $i++){?>
                                                <option value="<?=$i;?>">Periodo <?=$i;?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-secondary" id="btnLimpiarFiltrosCargas">
                                        <i class="fa fa-eraser"></i> Limpiar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php 
                            $filtro = '';
                            $curso = '';
                            
                            // Validar y sanitizar parámetros GET antes de usarlos
                            if(!empty($_GET["curso"]) && is_string($_GET['curso'])){ 
                                try {
                                    $cursoDecoded = base64_decode($_GET['curso'], true);
                                    if ($cursoDecoded !== false && is_numeric($cursoDecoded)) {
                                        $curso = mysqli_real_escape_string($conexion, $cursoDecoded);
                                        $filtro .= " AND car_curso='".$curso."'";
                                    }
                                } catch(Exception $e) {
                                    // Ignorar error de decodificación
                                }
                            }
                            
                            if(!empty($_GET["grupo"]) && is_string($_GET['grupo'])){
                                try {
                                    $grupoDecoded = base64_decode($_GET["grupo"], true);
                                    if ($grupoDecoded !== false && is_numeric($grupoDecoded)) {
                                        $grupo = mysqli_real_escape_string($conexion, $grupoDecoded);
                                        $filtro .= " AND car_grupo='".$grupo."'";
                                    }
                                } catch(Exception $e) {
                                    // Ignorar error de decodificación
                                }
                            }
                            
                            if(!empty($_GET["docente"]) && is_string($_GET['docente'])){
                                try {
                                    $docenteDecoded = base64_decode($_GET["docente"], true);
                                    if ($docenteDecoded !== false && !empty($docenteDecoded)) {
                                        $docente = mysqli_real_escape_string($conexion, $docenteDecoded);
                                        $filtro .= " AND car_docente='".$docente."'";
                                    }
                                } catch(Exception $e) {
                                    // Ignorar error de decodificación
                                }
                            }
                            
                            if(!empty($_GET["asignatura"]) && is_string($_GET['asignatura'])){
                                try {
                                    $asignaturaDecoded = base64_decode($_GET["asignatura"], true);
                                    if ($asignaturaDecoded !== false && is_numeric($asignaturaDecoded)) {
                                        $asignatura = mysqli_real_escape_string($conexion, $asignaturaDecoded);
                                        $filtro .= " AND car_materia='".$asignatura."'";
                                    }
                                } catch(Exception $e) {
                                    // Ignorar error de decodificación
                                }
                            }
                            ?>
                            
                            <div class="card card-topline-purple">
                                <div class="card-head">
                                    <header><?=$frases[12][$datosUsuarioActual['uss_idioma']];?></header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                        <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body">
                                        <div>
                                    		<table id="example1" class="display"  style="width:100%;">
												<div id="gifCarga" class="gif-carga">
													<img  alt="Cargando...">
												</div>
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th><input type="checkbox" id="selectAllCargas"></th>
                                                        <th>#</th>
             <th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
             <th>Docente</th>
             <th>Curso</th>
             <th>Asignatura</th>
             <th>I.H</th>
             <th>Periodo Actual</th>
                                            <th style="text-align:center;">NOTAS<br>Declaradas - Registradas</th>
             <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
             </tr>
            </thead>
													<tbody id="cargas_result">
													<?php
													require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");
													
													// Usar paginación por defecto para mejorar rendimiento
													// NOTA: Para mejorar aún más el rendimiento, ejecuta los índices en:
													// documents/database/indices-optimizacion-cargas.sql
													
													// Paginación
													$limit = 200; // Registros por página
													$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
													$offset = ($page - 1) * $limit;
													$filtroLimite = "LIMIT $offset, $limit";
													
													// Contar total de registros
													$totalCargas = CargaAcademicaOptimizada::contarTotalCargas($conexion, $config, $filtro);
													$totalPaginas = ceil($totalCargas / $limit);
													
													$selectSql = ["car.car_id","car.car_periodo","car.car_curso","car.car_ih","car.car_permiso2",
																	"car.car_indicador_automatico","car.car_maximos_indicadores",
																	"car.car_docente","gra.gra_tipo","am.mat_id",
																	"car.car_maximas_calificaciones","car.car_director_grupo","uss.uss_nombre",
																	"uss.uss_id","uss.uss_nombre2","uss.uss_apellido1","uss.uss_apellido2","gra.gra_id","gra.gra_nombre","gra.gra_periodos",
																	"gru.gru_nombre","am.mat_nombre","am.mat_valor","car.car_grupo","car.car_director_grupo", "car.car_activa",
																	"car.id_nuevo AS id_nuevo_carga"];
													
													// Usar método optimizado sin subqueries pesadas
													$busqueda = CargaAcademicaOptimizada::listarCargasOptimizado($conexion, $config, "", $filtro, "car.car_id", $filtroLimite,"",array(),$selectSql);
    												$contReg = 1;
													$index = 0;
													$arraysDatos = array();																									
													while ($fila = $busqueda->fetch_assoc()) {
														$arraysDatos[$index] = $fila;
														$index++;
													}
													$lista = $arraysDatos;
													$data["data"] =$lista;
													include("../class/componentes/result/cargas-tbody.php");
													?>
                            </tbody>
                          </table>
                          
                          <!-- Información de paginación y navegación -->
                          <div class="row mt-3">
                              <div class="col-md-6">
                                  <div class="alert alert-info">
                                      <i class="fa fa-info-circle"></i> 
                                      Mostrando <strong><?= count($arraysDatos) ?></strong> de <strong><?= $totalCargas ?></strong> cargas totales
                                      (Página <?= $page ?> de <?= $totalPaginas ?>)
                                  </div>
                              </div>
                              <div class="col-md-6 text-right">
                                  <nav aria-label="Paginación de cargas">
                                      <ul class="pagination justify-content-end">
                                          <?php if ($page > 1) { ?>
                                              <li class="page-item">
                                                  <a class="page-link" href="?page=1<?= !empty($_GET['busqueda']) ? '&busqueda=' . $_GET['busqueda'] : '' ?><?= !empty($_GET['curso']) ? '&curso=' . $_GET['curso'] : '' ?>">
                                                      <i class="fa fa-angle-double-left"></i> Primera
                                                  </a>
                                              </li>
                                              <li class="page-item">
                                                  <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($_GET['busqueda']) ? '&busqueda=' . $_GET['busqueda'] : '' ?><?= !empty($_GET['curso']) ? '&curso=' . $_GET['curso'] : '' ?>">
                                                      <i class="fa fa-angle-left"></i> Anterior
                                                  </a>
                                              </li>
                                          <?php } ?>
                                          
                                          <?php
                                          // Mostrar páginas cercanas
                                          $rango = 2;
                                          $inicio = max(1, $page - $rango);
                                          $fin = min($totalPaginas, $page + $rango);
                                          
                                          for ($i = $inicio; $i <= $fin; $i++) {
                                              $active = ($i == $page) ? 'active' : '';
                                          ?>
                                              <li class="page-item <?= $active ?>">
                                                  <a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['busqueda']) ? '&busqueda=' . $_GET['busqueda'] : '' ?><?= !empty($_GET['curso']) ? '&curso=' . $_GET['curso'] : '' ?>">
                                                      <?= $i ?>
                                                  </a>
                                              </li>
                                          <?php } ?>
                                          
                                          <?php if ($page < $totalPaginas) { ?>
                                              <li class="page-item">
                                                  <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($_GET['busqueda']) ? '&busqueda=' . $_GET['busqueda'] : '' ?><?= !empty($_GET['curso']) ? '&curso=' . $_GET['curso'] : '' ?>">
                                                      Siguiente <i class="fa fa-angle-right"></i>
                                                  </a>
                                              </li>
                                              <li class="page-item">
                                                  <a class="page-link" href="?page=<?= $totalPaginas ?><?= !empty($_GET['busqueda']) ? '&busqueda=' . $_GET['busqueda'] : '' ?><?= !empty($_GET['curso']) ? '&curso=' . $_GET['curso'] : '' ?>">
                                                      Última <i class="fa fa-angle-double-right"></i>
                                                  </a>
                                              </li>
                                          <?php } ?>
                                      </ul>
                                  </nav>
                              </div>
                          </div>
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
    <!-- <script src="../../config-general/assets/js/pages/table/table_data.js" ></script> -->
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
	<!-- SweetAlert2 -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- CACHE BUSTER: <?= time() ?> -->
    <!-- end js include path -->
	<script>
		console.log('==========================================');
		console.log('INICIO DE SCRIPTS DE CARGAS.PHP - VERSION LIMPIA');
		console.log('jQuery disponible:', typeof $ !== 'undefined');
		console.log('Bootstrap modal disponible:', typeof $.fn.modal === 'function');
		console.log('==========================================');
		
		// Manejar errores de scripts externos para que no bloqueen la página
		window.addEventListener('error', function(e) {
			console.error('Error global capturado:', e.message, e.filename, e.lineno);
			// Silenciar errores de modal/tooltip que bloquean la ejecución
			if (e.message && (e.message.includes('modal') || e.message.includes('tooltip') || e.message.includes('addEventListener'))) {
				console.warn('Error silenciado para no bloquear la página:', e.message);
				e.preventDefault();
				return true;
			}
		});
		
		// Esperar a que TODO esté completamente cargado
		$(window).on('load', function() {
			// Inicializar popovers si están disponibles
			try {
				if (typeof $().popover === 'function') {
					$('[data-toggle="popover"]').popover();
					$('.popover-dismiss').popover({trigger: 'focus'});
				}
			} catch(e) {
				console.warn('Popover no disponible:', e);
			}
		});

		// DataTable initialization with child rows
		$(document).ready(function() {
			console.log('Inicializando DataTable y funcionalidades...');
			if (!$.fn.DataTable.isDataTable('#example1')) {
				var table = $('#example1').DataTable({
					"paging": false,   // Desactivar paginación de DataTable
					"pageLength": 500, // Mostrar todos los registros cargados
					"columnDefs": [
						{
							"targets": 0,
							"orderable": false,
							"searchable": false
						},
						{
							"targets": 1,
							"orderable": false,
							"searchable": false
						}
					],
					"order": [[2, 'asc']],
					"language": {
						"lengthMenu": "<?=__('datatables.length_menu');?>",
						"zeroRecords": "<?=__('datatables.zero_records');?>",
						"info": "<?=__('datatables.info');?>",
						"infoEmpty": "<?=__('datatables.info_empty');?>",
						"infoFiltered": "<?=__('datatables.info_filtered');?>",
						"search": "<?=__('datatables.search');?>",
						"paginate": {
							"first": "<?=__('datatables.first');?>",
							"last": "<?=__('datatables.last');?>",
							"next": "<?=__('datatables.next');?>",
							"previous": "<?=__('datatables.previous');?>"
						}
					},
					"initComplete": function(settings, json) {
						// Attach expand button events initially
						var table = this.api();
						var expandedRows = {}; // Track expanded rows by cargaId

						$('#example1 tbody').on('click', '.expand-btn', function () {
							var button = $(this);
							var cargaId = button.data('id');
							var icon = button.find('i');

							// Get data from button attributes
							var codigo = button.data('codigo');
							var docente = button.data('docente');
							var curso = button.data('curso');
							var asignatura = button.data('asignatura');
							var ih = button.data('ih');
							var periodo = button.data('periodo');
							var actividades = button.data('actividades');
							var actividadesRegistradas = button.data('actividades-registradas');
							var directorGrupo = button.data('director-grupo');
							var permiso2 = button.data('permiso2');
							var indicadorAutomatico = button.data('indicador-automatico');
							var maxIndicadores = button.data('max-indicadores');
							var maxCalificaciones = button.data('max-calificaciones');
							var cantidadEstudiantes = button.data('cantidad-estudiantes');
							var activa = button.data('activa');

							// Find the current row in the table
							var tr = $(this).closest('tr');
							var row = table.row(tr);

							// Check if this row is currently expanded
							var isExpanded = expandedRows[cargaId] || false;

							if (isExpanded) {
								// This row is already open - close it
								try {
									if (row && row.child && typeof row.child === 'function') {
										row.child.hide();
									} else {
										// Fallback: remove any child rows manually
										tr.next('tr').remove();
									}
									expandedRows[cargaId] = false;
									icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
									button.removeClass('text-primary').addClass('text-secondary');
								} catch (error) {
									console.error('Error hiding child row:', error);
									// Fallback cleanup
									tr.next('tr').remove();
									expandedRows[cargaId] = false;
									icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
									button.removeClass('text-primary').addClass('text-secondary');
								}
							} else {
								// Open this row
								try {
									if (row && row.child && typeof row.child === 'function') {
										row.child(formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId)).show();
									} else {
										// Fallback: insert child row manually
										$(formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId)).insertAfter(tr);
									}
									expandedRows[cargaId] = true;
									icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
									button.removeClass('text-secondary').addClass('text-primary');
								} catch (error) {
									console.error('Error showing child row:', error);
									// Fallback insertion
									$(formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId)).insertAfter(tr);
									expandedRows[cargaId] = true;
									icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
									button.removeClass('text-secondary').addClass('text-primary');
								}
							}
						});
					}
				});
			} else {
				var table = $('#example1').DataTable();
			}

		});

		function formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId) {
			var activaBadgeClass = activa == 1 ? 'success' : 'warning';
			var activaText = activa == 1 ? 'Activa' : 'Inactiva';

			var html = '<tr class="expandable-row">' +
				'<td colspan="11" class="expandable-content bg-light border">' +
					'<div class="row no-gutters">' +
						'<div class="col-md-12 p-3">' +
							'<div class="row">' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Código:</strong>' +
										'<span class="badge badge-secondary ml-2">' + codigo + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Docente:</strong>' +
										'<span class="text-dark">' + docente + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Curso:</strong>' +
										'<span class="text-dark">' + curso + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Asignatura:</strong>' +
										'<span class="text-dark">' + asignatura + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">I.H:</strong>' +
										'<span class="text-dark">' + ih + '</span>' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Periodo Actual:</strong>' +
										'<span class="badge badge-info">' + periodo + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Actividades Declaradas:</strong>' +
										'<span class="text-dark">' + actividades + '%</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Actividades Registradas:</strong>' +
										'<span class="text-dark">' + actividadesRegistradas + '%</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Director de Grupo:</strong>' +
										'<span class="badge badge-' + (directorGrupo === 'Si' ? 'success' : 'secondary') + '">' + directorGrupo + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Estado:</strong>' +
										'<span class="badge badge-' + activaBadgeClass + '">' + activaText + '</span>' +
									'</div>' +
								'</div>' +
							'</div>' +
							'<div class="row mt-2">' +
								'<div class="col-12">' +
									'<div class="alert alert-info py-2">' +
										'<i class="fa fa-users mr-2"></i>' +
										'<strong>Estudiantes:</strong> ' + cantidadEstudiantes + ' estudiantes matriculados' +
									'</div>' +
								'</div>' +
							'</div>' +
							'<div class="row mt-2">' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Puede editar en otros periodos:</strong>' +
										'<span class="badge badge-' + (permiso2 === 'Si' ? 'success' : 'secondary') + '">' + permiso2 + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Indicadores automáticos:</strong>' +
										'<span class="badge badge-' + (indicadorAutomatico === 'Si' ? 'success' : 'secondary') + '">' + indicadorAutomatico + '</span>' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Máx. Indicadores:</strong>' +
										'<span class="text-dark">' + maxIndicadores + '</span>' +
									'</div>' +
									'<div class="info-item mb-2">' +
										'<strong class="text-muted">Máx. Calificaciones:</strong>' +
										'<span class="text-dark">' + maxCalificaciones + '</span>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</td>' +
			'</tr>';

			return html;
		}

		// JavaScript for bulk move cargas
		var selectedCargas = [];

		$('#selectAllCargas').on('change', function() {
			if (this.checked) {
				$('.carga-checkbox').prop('checked', true);
				selectedCargas = $('.carga-checkbox').map(function() { return this.value; }).get();
				// Agregar clase de selección a todas las filas
				$('.carga-checkbox').each(function() {
					$(this).closest('tr').addClass('carga-row-selected');
				});
			} else {
				$('.carga-checkbox').prop('checked', false);
				selectedCargas = [];
				// Remover clase de selección de todas las filas
				$('.carga-checkbox').each(function() {
					$(this).closest('tr').removeClass('carga-row-selected');
				});
			}
			toggleActionButtons();
		});

		$(document).on('change', '.carga-checkbox', function() {
			var row = $(this).closest('tr');
			
			if (this.checked) {
				selectedCargas.push(this.value);
				// Agregar sombreado visual a la fila
				row.addClass('carga-row-selected');
			} else {
				selectedCargas = selectedCargas.filter(id => id !== this.value);
				// Remover sombreado visual de la fila
				row.removeClass('carga-row-selected');
			}
			$('#selectAllCargas').prop('checked', $('.carga-checkbox:checked').length === $('.carga-checkbox').length);
			toggleActionButtons();
		});

		function toggleActionButtons() {
			var hasSelection = selectedCargas.length > 0;
			$('#moverCargasBtn').prop('disabled', !hasSelection);
			$('#editarMasivoBtn').prop('disabled', !hasSelection);
		}

		$('#moverCargasBtn').on('click', function() {
			loadPeriodos();
		});

		function loadPeriodos() {
			$.ajax({
				url: 'ajax-get-periodos.php',
				type: 'GET',
				success: function(data) {
					$('#nuevoPeriodo').html(data);
					$('#moverCargasModal').modal('show');
					attachConfirmEvent();
				},
				error: function(xhr, status, error) {
					console.error('Error loading periods:', error);
					// Fallback: provide default periods
					var defaultOptions = '';
					for (var i = 1; i <= 4; i++) {
						defaultOptions += '<option value="' + i + '">Período ' + i + '</option>';
					}
					$('#nuevoPeriodo').html(defaultOptions);
					$('#moverCargasModal').modal('show');
					attachConfirmEvent();
				}
			});
		}

		function attachConfirmEvent() {
			$('#confirmarMover').on('click', function() {
				var nuevoPeriodo = $('#nuevoPeriodo').val();
				if (!nuevoPeriodo) {
					$.toast({
						heading: 'Error',
						text: 'Por favor seleccione un período.',
						showHideTransition: 'slide',
						icon: 'error'
					});
					return;
				}
				$.ajax({
					url: 'cargas-mover.php',
					type: 'POST',
					data: { cargas: selectedCargas, periodo: nuevoPeriodo },
					success: function(response) {
						var res;
						if (typeof response === 'object') {
							res = response;
						} else {
							try {
								res = JSON.parse(response);
							} catch (e) {
								$.toast({
									heading: 'Error',
									text: 'Respuesta inválida del servidor.',
									showHideTransition: 'slide',
									icon: 'error'
								});
								return;
							}
						}
						if (res.success) {
							$('#moverCargasModal').modal('hide');
							$.toast({
								heading: 'Éxito',
								text: 'Las cargas académicas han sido movidas exitosamente.',
								showHideTransition: 'slide',
								icon: 'success'
							});
							setTimeout(function() {
								location.reload();
							}, 2000);
						} else {
							$.toast({
								heading: 'Error',
								text: 'Hubo un error al mover las cargas.',
								showHideTransition: 'slide',
								icon: 'error'
							});
						}
					},
					error: function(xhr, status, error) {
						console.error('Error moving cargas:', error);
						$.toast({
							heading: 'Error',
							text: 'Error de conexión al servidor.',
							showHideTransition: 'slide',
							icon: 'error'
						});
					}
				});
			});
		}

		$('#confirmarMover').on('click', function() {
			alert('Button clicked, selectedCargas: ' + selectedCargas.length + ', periodo: ' + $('#nuevoPeriodo').val());
			var nuevoPeriodo = $('#nuevoPeriodo').val();
			if (!nuevoPeriodo) {
				alert('Seleccione un período.');
				return;
			}
			alert('Sending AJAX');
			$.ajax({
				url: 'cargas-mover.php',
				type: 'POST',
				data: { cargas: selectedCargas, periodo: nuevoPeriodo },
				success: function(response) {
					alert('Response: ' + response);
					try {
						var res = JSON.parse(response);
						if (res.success) {
							$('#moverCargasModal').modal('hide');
							alert('Success');
							location.reload();
						} else {
							alert('Error: ' + res.error);
						}
					} catch (e) {
						alert('Invalid JSON: ' + response);
					}
				},
				error: function(xhr, status, error) {
					alert('AJAX Error: ' + error);
				}
			});
		});

		// ========================================
		// FUNCIONALIDAD DE EDICIÓN MASIVA
		// ========================================

		try {
			console.log('Inicializando funcionalidad de edición masiva...');
			
			// Abrir modal de edición masiva
			$('#editarMasivoBtn').on('click', function() {
			if (selectedCargas.length === 0) {
				$.toast({
					heading: 'Advertencia',
					text: 'Por favor selecciona al menos una carga académica.',
					showHideTransition: 'slide',
					icon: 'warning',
					position: 'top-right'
				});
				return;
			}
			
			// Actualizar contador de cargas seleccionadas
			$('#cantidadCargasSeleccionadas').text(selectedCargas.length);
			
			// Limpiar formulario
			$('#editarMasivoForm')[0].reset();
			
			// Destruir Select2 si ya existe
			if ($('.select2-modal').hasClass("select2-hidden-accessible")) {
				$('.select2-modal').select2('destroy');
			}
			
			// Limpiar valores de los selects
			$('.select2-modal').val('');
			
			// Mostrar modal primero
			$('#editarMasivoModal').modal('show');
		});
		
		// Inicializar Select2 DESPUÉS de que el modal esté completamente visible
		$('#editarMasivoModal').on('shown.bs.modal', function () {
			// Inicializar Select2 en el modal cuando ya está visible
			$('.select2-modal').select2({
				dropdownParent: $('#editarMasivoModal'),
				placeholder: "No modificar",
				allowClear: true,
				width: '100%',
				language: {
					noResults: function() {
						return "No se encontraron resultados";
					},
					searching: function() {
						return "Buscando...";
					}
				}
			});
		});

		// Manejar clic del botón de aplicar cambios masivos usando event delegation
		console.log('Enlazando evento click al botón con delegation...');
		console.log('Botón existe:', $('#btnConfirmarEdicionMasiva').length > 0);
		$(document).on('click', '#btnConfirmarEdicionMasiva', function(e) {
			console.log('=== BOTÓN APLICAR CAMBIOS CLICKEADO ===');
			e.preventDefault();
			e.stopPropagation();
			
			console.log('=== FORMULARIO DE EDICIÓN MASIVA ENVIADO ===');
			
			var formData = $('#editarMasivoForm').serializeArray();
			console.log('FormData serializado:', formData);
			
			var camposAActualizar = {};
			var hayCambios = false;
			
			// Filtrar solo los campos que tienen valor (no vacíos)
			formData.forEach(function(field) {
				console.log('Procesando campo:', field.name, '=', field.value, '(tipo:', typeof field.value, ')');
				
				if (field.value !== '' && field.value !== null && field.value !== undefined) {
					camposAActualizar[field.name] = field.value;
					hayCambios = true;
					console.log('✓ Campo agregado:', field.name, '=', field.value);
				} else {
					console.log('✗ Campo omitido (vacío):', field.name);
				}
			});
			
			console.log('Campos finales a actualizar:', camposAActualizar);
			console.log('Hay cambios:', hayCambios);
			
			// Validar que haya al menos un campo para actualizar
			if (!hayCambios) {
				console.warn('No hay campos para actualizar');
				$.toast({
					heading: 'Advertencia',
					text: 'Debes seleccionar al menos un campo para modificar.',
					showHideTransition: 'slide',
					icon: 'warning',
					position: 'top-right',
					hideAfter: 3000
				});
				return;
			}
			
			// Aplicar cambios directamente sin confirmación adicional
			aplicarEdicionMasiva(camposAActualizar);
		});

		function aplicarEdicionMasiva(camposAActualizar) {
			console.log('Iniciando edición masiva...');
			console.log('Cargas seleccionadas:', selectedCargas);
			console.log('Campos a actualizar:', camposAActualizar);
			
			// Mostrar loader en el botón
			var btnOriginal = $('#btnConfirmarEdicionMasiva').html();
			$('#btnConfirmarEdicionMasiva').html('<i class="fa fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
			
			// Toast de proceso
			$.toast({
				heading: 'Procesando',
				text: 'Actualizando ' + selectedCargas.length + ' carga(s) académica(s)...',
				showHideTransition: 'slide',
				icon: 'info',
				position: 'top-right',
				hideAfter: false,
				loader: true,
				loaderBg: '#3498db'
			});
			
			$.ajax({
				url: 'cargas-editar-masivo.php',
				type: 'POST',
				data: {
					cargas: selectedCargas,
					campos: camposAActualizar
				},
				dataType: 'json',
				success: function(response) {
					console.log('Respuesta del servidor:', response);
					
					$('#btnConfirmarEdicionMasiva').html(btnOriginal).prop('disabled', false);
					
					// Cerrar el toast de procesamiento
					$('.jq-toast-wrap').remove();
					
					if (response.success) {
						// Cerrar modal
						$('#editarMasivoModal').modal('hide');
						
						// Toast de éxito
						$.toast({
							heading: '¡Éxito!',
							text: 'Se actualizaron correctamente ' + response.actualizadas + ' de ' + selectedCargas.length + ' carga(s) académica(s).',
							showHideTransition: 'slide',
							icon: 'success',
							position: 'top-right',
							hideAfter: 4000,
							loaderBg: '#27ae60'
						});
						
						// Recargar solo la tabla de cargas sin recargar toda la página
						setTimeout(function() {
							recargarTablaCargas();
						}, 500);
						
					} else {
						// Toast de error
						$.toast({
							heading: 'Error',
							text: response.message || 'Hubo un error al procesar la edición masiva.',
							showHideTransition: 'slide',
							icon: 'error',
							position: 'top-right',
							hideAfter: 5000,
							loaderBg: '#e74c3c'
						});
					}
				},
				error: function(xhr, status, error) {
					console.error('==== ERROR EN EDICIÓN MASIVA ====');
					console.error('Status:', status);
					console.error('Error:', error);
					console.error('Response Status:', xhr.status);
					console.error('Response Text:', xhr.responseText);
					console.error('Response Headers:', xhr.getAllResponseHeaders());
					
					$('#btnConfirmarEdicionMasiva').html(btnOriginal).prop('disabled', false);
					
					// Cerrar el toast de procesamiento
					$('.jq-toast-wrap').remove();
					
					// Intentar parsear el error para mostrarlo
					var errorMessage = 'No se pudo conectar con el servidor. Por favor intenta de nuevo.';
					try {
						var errorResponse = JSON.parse(xhr.responseText);
						if (errorResponse.message) {
							errorMessage = errorResponse.message;
						}
					} catch(e) {
						// Si no es JSON, usar el texto completo si es corto
						if (xhr.responseText && xhr.responseText.length < 200) {
							errorMessage = xhr.responseText;
						}
					}
					
					// Toast de error de conexión con el mensaje específico
					$.toast({
						heading: 'Error',
						text: errorMessage,
						showHideTransition: 'slide',
						icon: 'error',
						position: 'top-right',
						hideAfter: 8000,
						loaderBg: '#e74c3c'
					});
				}
			});
		}
		
		// Función para recargar la tabla de cargas sin recargar toda la página
		function recargarTablaCargas() {
			console.log('Recargando tabla de cargas...');
			
			// Mostrar loader
			$('#gifCarga').show();
			
			// Obtener filtros actuales
			var cursos = $('#filtro_cargas_cursos').val() || [];
			var grupos = $('#filtro_cargas_grupos').val() || [];
			var docentes = $('#filtro_cargas_docentes').val() || [];
			var periodos = $('#filtro_cargas_periodos').val() || [];
			
			$.ajax({
				url: 'ajax-filtrar-cargas.php',
				type: 'POST',
				data: {
					cursos: cursos,
					grupos: grupos,
					docentes: docentes,
					periodos: periodos
				},
				dataType: 'json',
				success: function(response) {
					$('#gifCarga').hide();
					
					if (response.success) {
						// Actualizar el contenido de la tabla
						$('#cargas_result').html(response.html);
						
						// Limpiar las selecciones
						selectedCargas = [];
						$('#selectAllCargas').prop('checked', false);
						toggleActionButtons();
						
						// IMPORTANTE: Reinicializar event listeners de expandir/contraer
						reinicializarEventListenersExpandir();
						
						// Reinicializar tooltips
						$('[data-toggle="tooltip"]').tooltip();
						
						console.log('Tabla recargada exitosamente');
					} else {
						console.error('Error al recargar tabla:', response.error);
						$.toast({
							heading: 'Advertencia',
							text: 'Los cambios se aplicaron pero hubo un problema al actualizar la vista.',
							showHideTransition: 'slide',
							icon: 'warning',
							position: 'top-right',
							hideAfter: 3000
						});
					}
				},
				error: function(xhr, status, error) {
					$('#gifCarga').hide();
					console.error('Error al recargar tabla:', error);
					// No mostrar error ya que los cambios sí se aplicaron
				}
			});
		}
		
		// Función para reinicializar event listeners de expandir/contraer
		function reinicializarEventListenersExpandir() {
			console.log('Reinicializando event listeners de expandir/contraer...');
			
			$('.expand-btn').off('click').on('click', function() {
				var button = $(this);
				var cargaId = button.data('id');
				var icon = button.find('i');
				
				// Verificar si la fila expandible ya existe
				var expandRow = button.closest('tr').next('tr.expandable-row');
				
				if (expandRow.length && expandRow.is(':visible')) {
					// Contraer
					expandRow.slideUp(300, function() {
						icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
						button.removeClass('text-primary').addClass('text-secondary');
					});
				} else {
					// Expandir - obtener datos de los atributos del botón
					var codigo = button.data('codigo');
					var docente = button.data('docente');
					var curso = button.data('curso');
					var asignatura = button.data('asignatura');
					var ih = button.data('ih');
					var periodo = button.data('periodo');
					var actividades = button.data('actividades');
					var actividadesRegistradas = button.data('actividades-registradas');
					var directorGrupo = button.data('director-grupo');
					var permiso2 = button.data('permiso2');
					var indicadorAutomatico = button.data('indicador-automatico');
					var maxIndicadores = button.data('max-indicadores');
					var maxCalificaciones = button.data('max-calificaciones');
					var cantidadEstudiantes = button.data('cantidad-estudiantes');
					var activa = button.data('activa');
					
					// Crear el HTML de detalles y insertarlo
					var detailsHtml = formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId);
					$(detailsHtml).insertAfter(button.closest('tr')).slideDown(300);
					
					icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
					button.removeClass('text-secondary').addClass('text-primary');
				}
			});
			
			console.log('Event listeners reinicializados correctamente');
		}

		// Limpiar selección al cerrar el modal
		$('#editarMasivoModal').on('hidden.bs.modal', function () {
			$('.select2-modal').select2('destroy');
		});
		
		} catch(error) {
			console.error('Error en funcionalidad de edición masiva:', error);
			console.log('Continuando con el resto de la página...');
		}
	</script>
	<style>
	    .sorting_1 {
			background-color: red !important;
	    }

		.expandable-content {
			padding: 15px;
			margin: 10px 0;
			border-radius: 5px;
			width: 100%;
			box-sizing: border-box;
		}

		.expandable-row {
			background-color: #f8f9fa !important;
		}

		.expandable-row .expandable-content {
			border-left: 3px solid #007bff;
			border-radius: 0;
		}

		.info-item {
			display: flex;
			align-items: flex-start;
			margin-bottom: 8px;
		}

		.info-item strong {
			min-width: 180px;
			margin-right: 10px;
			flex-shrink: 0;
		}

		.info-item span {
			flex: 1;
			word-wrap: break-word;
		}

	  </style>

	<!-- Modal for moving cargas -->
	<div class="modal fade" id="moverCargasModal" tabindex="-1" role="dialog" aria-labelledby="moverCargasModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="moverCargasModalLabel">Mover Cargas Seleccionadas</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="moverCargasForm">
						<div class="form-group">
							<label for="nuevoPeriodo">Seleccionar Nuevo Período</label>
							<select class="form-control" id="nuevoPeriodo" name="nuevoPeriodo" required>
								<!-- options will be loaded via JS -->
							</select>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="confirmarMover">Mover</button>
				</div>
			</div>
		</div>
	</div>

<!-- Modal para edición masiva de cargas -->
<div class="modal fade" id="editarMasivoModal" tabindex="-1" role="dialog" aria-labelledby="editarMasivoModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-warning">
				<h5 class="modal-title" id="editarMasivoModalLabel">
					<i class="fa fa-edit"></i> Edición Masiva de Cargas Académicas
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="editarMasivoForm" method="post" action="javascript:void(0);">
				<div class="modal-body">
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i> 
						<strong>Instrucciones:</strong> Los campos que dejes en blanco o sin seleccionar NO serán modificados. 
						Solo se actualizarán los campos que completes.
					</div>
					
					<div class="alert alert-warning">
						<i class="fa fa-exclamation-triangle"></i> 
						Has seleccionado <strong><span id="cantidadCargasSeleccionadas">0</span></strong> carga(s) académica(s) 
						para editar de forma masiva.
					</div>
					
					<hr>
					<h5 class="text-primary"><i class="fa fa-cog"></i> Campos a Modificar</h5>
					<small class="text-muted">Completa solo los campos que desees actualizar en las cargas seleccionadas</small>
					
					<div class="row mt-3">
						<div class="col-md-6">
							<div class="form-group">
								<label>Periodo</label>
								<select class="form-control select2-modal" id="masivo_periodo" name="periodo">
									<option value="">No modificar</option>
									<?php for($i=1; $i<=$config['conf_periodos_maximos']; $i++){?>
										<option value="<?=$i;?>">Periodo <?=$i;?></option>
									<?php }?>
								</select>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Docente</label>
								<select class="form-control select2-modal" id="masivo_docente" name="docente">
									<option value="">No modificar</option>
									<?php
									try {
										$consultaDocentes = mysqli_query($conexion, "SELECT uss_id, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, uss_bloqueado 
											FROM ".BD_GENERAL.".usuarios 
											WHERE uss_tipo=".TIPO_DOCENTE." AND institucion={$config['conf_id_institucion']} AND year={$_SESSION['bd']}
											ORDER BY uss_nombre ASC");
										
										while ($doc = mysqli_fetch_array($consultaDocentes, MYSQLI_BOTH)) {
											$nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($doc);
											$disabled = $doc['uss_bloqueado'] == 1 ? 'disabled' : '';
										?>
											<option value="<?=$doc['uss_id'];?>" <?=$disabled;?>><?=$nombreCompleto;?></option>
										<?php 
										}
									} catch(Exception $e) {
										echo "<!-- Error al cargar docentes: " . $e->getMessage() . " -->";
									}
									?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Curso</label>
								<select class="form-control select2-modal" id="masivo_curso" name="curso">
									<option value="">No modificar</option>
									<?php
									$grados = Grados::listarGrados(1);
									while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
										$disabled = $grado['gra_estado'] == '0' ? 'disabled' : '';
									?>
										<option value="<?=$grado['gra_id'];?>" <?=$disabled;?>><?=$grado['gra_nombre'];?></option>
									<?php }?>
								</select>
							</div>
						</div>
						
						<div class="col-md-4">
							<div class="form-group">
								<label>Grupo</label>
								<select class="form-control select2-modal" id="masivo_grupo" name="grupo">
									<option value="">No modificar</option>
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
								<label>Intensidad Horaria</label>
								<input type="number" class="form-control" id="masivo_ih" name="ih" placeholder="No modificar" min="1">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Asignatura</label>
								<select class="form-control select2-modal" id="masivo_asignatura" name="asignatura" style="width: 100%;">
									<option value="">No modificar</option>
									<?php
									require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
									$asignaturas = Asignaturas::consultarTodasAsignaturas($conexion, $config);
									while ($asig = mysqli_fetch_array($asignaturas, MYSQLI_BOTH)) {
									?>
										<option value="<?=$asig['mat_id'];?>"><?=$asig['mat_nombre'];?> (<?=$asig['ar_nombre'];?>)</option>
									<?php }?>
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label>Director de Grupo</label>
								<select class="form-control" id="masivo_dg" name="dg">
									<option value="">No modificar</option>
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label>Estado</label>
								<select class="form-control" id="masivo_estado" name="estado">
									<option value="">No modificar</option>
									<option value="1">Activa</option>
									<option value="0">Inactiva</option>
								</select>
							</div>
						</div>
					</div>
					
					<hr>
					<h5 class="text-info"><i class="fa fa-sliders-h"></i> Configuración Avanzada (Opcional)</h5>
					
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Max. Indicadores</label>
								<input type="number" class="form-control" id="masivo_maxIndicadores" name="maxIndicadores" placeholder="No modificar" min="0">
							</div>
						</div>
						
						<div class="col-md-4">
							<div class="form-group">
								<label>Max. Actividades</label>
								<input type="number" class="form-control" id="masivo_maxActividades" name="maxActividades" placeholder="No modificar" min="0">
							</div>
						</div>
						
						<div class="col-md-4">
							<div class="form-group">
								<label>Indicador Automático</label>
								<select class="form-control" id="masivo_indicadorAutomatico" name="indicadorAutomatico">
									<option value="">No modificar</option>
									<option value="1">SI</option>
									<option value="0">NO</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="button" class="btn btn-warning" id="btnConfirmarEdicionMasiva">
						<i class="fa fa-save"></i> Aplicar Cambios Masivos
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal para edición rápida de carga -->
<div class="modal fade" id="modalEditarCarga" tabindex="-1" role="dialog" aria-labelledby="modalEditarCargaLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalEditarCargaLabel">
					<i class="fa fa-edit"></i> Edición Rápida de Carga
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formEditarCarga" action="cargas-actualizar.php" method="post">
				<div class="modal-body">
					<div id="cargaLoader" class="text-center" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-3x"></i>
						<p>Cargando datos...</p>
					</div>
					
					<div id="cargaFormulario" style="display:none;">
						<!-- Campos ocultos -->
						<input type="hidden" id="edit_idR" name="idR">
						<input type="hidden" id="edit_periodoActual" name="periodoActual">
						<input type="hidden" id="edit_docenteActual" name="docenteActual">
						<input type="hidden" id="edit_cursoActual" name="cursoActual">
						<input type="hidden" id="edit_grupoActual" name="grupoActual">
						<input type="hidden" id="edit_asignaturaActual" name="asignaturaActual">
						<input type="hidden" id="edit_cargaEstado" name="cargaEstado">
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>ID de Carga</label>
									<input type="text" id="edit_idCarga" class="form-control" readonly>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Periodo <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_periodo" name="periodo" required>
										<option value="">Seleccione...</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label>Docente <span class="text-danger">*</span></label>
							<select class="form-control" id="edit_docente" name="docente" required>
								<option value="">Seleccione un docente...</option>
							</select>
						</div>
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Curso <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_curso" name="curso" required>
										<option value="">Seleccione un curso...</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Grupo <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_grupo" name="grupo" required>
										<option value="">Seleccione un grupo...</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label>Asignatura <span class="text-danger">*</span></label>
							<select class="form-control" id="edit_asignatura" name="asignatura" required style="height: auto; max-height: 200px;">
								<option value="">Seleccione una asignatura...</option>
							</select>
						</div>
						
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Intensidad H. <span class="text-danger">*</span></label>
									<input type="number" class="form-control" id="edit_ih" name="ih" required min="1">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Director de Grupo <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_dg" name="dg" required>
										<option value="">Seleccione...</option>
										<option value="1">SI</option>
										<option value="0">NO</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Estado <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_estado" name="estado" required>
										<option value="">Seleccione...</option>
										<option value="1">Activa</option>
										<option value="0">Inactiva</option>
									</select>
								</div>
							</div>
						</div>
						
						<hr>
						<h5 class="text-info"><i class="fa fa-cog"></i> Configuración Avanzada (Opcional)</h5>
						
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Max. Indicadores</label>
									<input type="number" class="form-control" id="edit_maxIndicadores" name="maxIndicadores" min="0">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Max. Actividades</label>
									<input type="number" class="form-control" id="edit_maxActividades" name="maxActividades" min="0">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>% Actividades</label>
									<select class="form-control" id="edit_valorActividades" name="valorActividades">
										<option value="">Seleccione...</option>
										<option value="1">Manual</option>
										<option value="0">Automático</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>% Indicadores</label>
									<select class="form-control" id="edit_valorIndicadores" name="valorIndicadores">
										<option value="">Seleccione...</option>
										<option value="1">Manual</option>
										<option value="0">Automático</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Indicador Automático</label>
									<select class="form-control" id="edit_indicadorAutomatico" name="indicadorAutomatico">
										<option value="">Seleccione...</option>
										<option value="1">SI</option>
										<option value="0">NO</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					
					<div id="cargaError" class="alert alert-danger" style="display:none;">
						<i class="fa fa-exclamation-triangle"></i> <span id="errorMensaje"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-primary" id="btnGuardarCarga">
						<i class="fa fa-save"></i> Guardar Cambios
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// Lazy loading de notas declaradas y registradas
	$(document).on('click', '.btn-cargar-notas', function() {
		var btn = $(this);
		var cargaId = btn.data('carga-id');
		var periodo = btn.data('periodo');
		var tdActividades = $('.td-actividades-' + cargaId);
		
		// Mostrar indicador de carga
		btn.html('<i class="fa fa-spinner fa-spin"></i> Cargando...').prop('disabled', true);
		
		// Hacer petición AJAX
		$.ajax({
			url: 'ajax-obtener-datos-adicionales-carga.php',
			type: 'POST',
			data: {
				carga_id: cargaId,
				periodo: periodo
			},
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var datos = response.datos;
					var actividadesTotales = datos.actividades_totales || 0;
					var actividadesRegistradas = datos.actividades_registradas || 0;
					
					// Actualizar la celda con los datos reales
					var nuevoContenido = actividadesTotales + '%&nbsp;&nbsp;-&nbsp;&nbsp;' + actividadesRegistradas + '%';
					
					<?php if ($permisoReportesNotas) { ?>
					// Si tiene permiso, convertir a enlace
					nuevoContenido = '<a href="../compartido/reporte-notas.php?carga=' + btoa(cargaId) + '&per=' + btoa(periodo) + '" target="_blank" style="text-decoration:underline; color:#00F;" title="Calificaciones">' + nuevoContenido + '</a>';
					<?php } ?>
					
					tdActividades.html(nuevoContenido);
					
					// Mostrar notificación de éxito
					$.toast({
						heading: 'Éxito',
						text: 'Datos cargados: ' + actividadesTotales + '% - ' + actividadesRegistradas + '%',
						position: 'top-right',
						loaderBg: '#26c281',
						icon: 'success',
						hideAfter: 2000
					});
				} else {
					btn.html('<i class="fa fa-exclamation-triangle"></i> Error').removeClass('btn-info').addClass('btn-danger');
					$.toast({
						heading: 'Error',
						text: response.message || 'Error al cargar datos',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 3000
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX:', error);
				btn.html('<i class="fa fa-exclamation-triangle"></i> Error').removeClass('btn-info').addClass('btn-danger');
				$.toast({
					heading: 'Error',
					text: 'Error de conexión al servidor',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error',
					hideAfter: 3000
				});
			}
		});
	});
	
	// Edición rápida de carga mediante modal
	$(document).on('click', '.btn-editar-carga-modal', function() {
		var cargaId = $(this).data('carga-id');
		
		// Resetear el modal
		$('#cargaLoader').show();
		$('#cargaFormulario').hide();
		$('#cargaError').hide();
		$('#modalEditarCarga').modal('show');
		
		// Hacer petición AJAX para obtener datos
		$.ajax({
			url: 'ajax-obtener-datos-carga.php',
			type: 'POST',
			data: { carga_id: cargaId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					var carga = response.carga;
					var listas = response.listas;
					
					// Llenar campos ocultos
					$('#edit_idR').val(carga.car_id);
					$('#edit_periodoActual').val(carga.car_periodo);
					$('#edit_docenteActual').val(carga.car_docente);
					$('#edit_cursoActual').val(carga.car_curso);
					$('#edit_grupoActual').val(carga.car_grupo);
					$('#edit_asignaturaActual').val(carga.car_materia);
					$('#edit_cargaEstado').val(carga.car_estado);
					
					// Llenar campos visibles
					$('#edit_idCarga').val(carga.car_id);
					$('#edit_ih').val(carga.car_ih);
					$('#edit_dg').val(carga.car_director_grupo);
					$('#edit_estado').val(carga.car_activa);
					$('#edit_maxIndicadores').val(carga.car_maximos_indicadores || '');
					$('#edit_maxActividades').val(carga.car_maximas_calificaciones || '');
					$('#edit_valorActividades').val(carga.car_configuracion || '');
					$('#edit_valorIndicadores').val(carga.car_valor_indicador || '');
					$('#edit_indicadorAutomatico').val(carga.car_indicador_automatico || '');
					
					// Llenar select de periodos
					$('#edit_periodo').empty().append('<option value="">Seleccione...</option>');
					for (var i = 1; i <= listas.periodos; i++) {
						var selected = (i == carga.car_periodo) ? 'selected' : '';
						$('#edit_periodo').append('<option value="' + i + '" ' + selected + '>Periodo ' + i + '</option>');
					}
					
					// Llenar select de docentes
					$('#edit_docente').empty().append('<option value="">Seleccione un docente...</option>');
					listas.docentes.forEach(function(docente) {
						var selected = (docente.id == carga.car_docente) ? 'selected' : '';
						var disabled = (docente.bloqueado == 1) ? 'disabled' : '';
						$('#edit_docente').append('<option value="' + docente.id + '" ' + selected + ' ' + disabled + '>' + docente.nombre + '</option>');
					});
					
					// Llenar select de grados
					$('#edit_curso').empty().append('<option value="">Seleccione un curso...</option>');
					listas.grados.forEach(function(grado) {
						var selected = (grado.id == carga.car_curso) ? 'selected' : '';
						var disabled = (grado.estado == '0') ? 'disabled' : '';
						$('#edit_curso').append('<option value="' + grado.id + '" ' + selected + ' ' + disabled + '>' + grado.nombre + '</option>');
					});
					
					// Llenar select de grupos
					$('#edit_grupo').empty().append('<option value="">Seleccione un grupo...</option>');
					listas.grupos.forEach(function(grupo) {
						var selected = (grupo.id == carga.car_grupo) ? 'selected' : '';
						$('#edit_grupo').append('<option value="' + grupo.id + '" ' + selected + '>' + grupo.nombre + '</option>');
					});
					
					// Llenar select de asignaturas
					$('#edit_asignatura').empty().append('<option value="">Seleccione una asignatura...</option>');
					listas.asignaturas.forEach(function(asignatura) {
						var selected = (asignatura.id == carga.car_materia) ? 'selected' : '';
						$('#edit_asignatura').append('<option value="' + asignatura.id + '" ' + selected + '>' + asignatura.nombre + '</option>');
					});
					
					// Mostrar formulario
					$('#cargaLoader').hide();
					$('#cargaFormulario').show();
					
				} else {
					$('#cargaLoader').hide();
					$('#errorMensaje').text(response.message || 'Error al cargar datos');
					$('#cargaError').show();
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX:', error);
				$('#cargaLoader').hide();
				$('#errorMensaje').text('Error de conexión al servidor');
				$('#cargaError').show();
			}
		});
	});
	
	// Manejar envío del formulario de edición
	$('#formEditarCarga').on('submit', function(e) {
		e.preventDefault();
		
		var btnGuardar = $('#btnGuardarCarga');
		var textoOriginal = btnGuardar.html();
		
		btnGuardar.html('<i class="fa fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
		
		// Enviar formulario
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function(response) {
				$.toast({
					heading: 'Éxito',
					text: 'Carga actualizada correctamente',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
				
				// Cerrar modal y recargar página
				$('#modalEditarCarga').modal('hide');
				setTimeout(function() {
					location.reload();
				}, 1000);
			},
			error: function(xhr, status, error) {
				btnGuardar.html(textoOriginal).prop('disabled', false);
				$.toast({
					heading: 'Error',
					text: 'Error al actualizar la carga',
					position: 'top-right',
					loaderBg: '#bf441d',
					icon: 'error',
					hideAfter: 3000
				});
			}
		});
	});
	
	// === Filtros Avanzados para Cargas ===
	
	// Toggle del panel de filtros
	$('#btnToggleFiltrosCargas').on('click', function() {
		const card = $('#cardFiltrosCargas');
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
	$('.select2-multiple-cargas').select2({
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
	
	// Función para aplicar filtros de cargas
	function aplicarFiltrosCargas() {
		const cursos = $('#filtro_cargas_cursos').val() || [];
		const grupos = $('#filtro_cargas_grupos').val() || [];
		const docentes = $('#filtro_cargas_docentes').val() || [];
		const periodos = $('#filtro_cargas_periodos').val() || [];
		
		console.log('Aplicando filtros cargas:', { cursos, grupos, docentes, periodos });
		
		// Mostrar loader
		$('#gifCarga').show();
		$('#cargas_result').html('<tr><td colspan="11" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Cargando...</td></tr>');
		
		// Enviar AJAX
		$.ajax({
			url: 'ajax-filtrar-cargas.php',
			type: 'POST',
			data: {
				cursos: cursos,
				grupos: grupos,
				docentes: docentes,
				periodos: periodos
			},
			dataType: 'json',
			success: function(response) {
				console.log('Respuesta del filtro cargas:', response);
				
				$('#gifCarga').hide();
				
				if (response.success) {
					// Insertar el HTML
					$('#cargas_result').html(response.html);
					
					// Restaurar las selecciones previas y sombreado visual
					if (selectedCargas.length > 0) {
						$('.carga-checkbox').each(function() {
							var cargaId = $(this).val();
							if (selectedCargas.includes(cargaId)) {
								$(this).prop('checked', true);
								$(this).closest('tr').addClass('carga-row-selected');
							}
						});
					}
					
					// Reinicializar event listeners para los botones de expandir
					// Esto es necesario porque el HTML se insertó dinámicamente
					$('.expand-btn').off('click').on('click', function() {
						var button = $(this);
						var cargaId = button.data('id');
						var icon = button.find('i');
						
						// Verificar si la fila expandible ya existe
						var expandRow = button.closest('tr').next('tr.expandable-row');
						
						if (expandRow.length && expandRow.is(':visible')) {
							// Contraer
							expandRow.slideUp(300, function() {
								icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
								button.removeClass('text-primary').addClass('text-secondary');
							});
						} else {
							// Expandir - obtener datos de los atributos del botón
							var codigo = button.data('codigo');
							var docente = button.data('docente');
							var curso = button.data('curso');
							var asignatura = button.data('asignatura');
							var ih = button.data('ih');
							var periodo = button.data('periodo');
							var actividades = button.data('actividades');
							var actividadesRegistradas = button.data('actividades-registradas');
							var directorGrupo = button.data('director-grupo');
							var permiso2 = button.data('permiso2');
							var indicadorAutomatico = button.data('indicador-automatico');
							var maxIndicadores = button.data('max-indicadores');
							var maxCalificaciones = button.data('max-calificaciones');
							var cantidadEstudiantes = button.data('cantidad-estudiantes');
							var activa = button.data('activa');
							
							// Crear el HTML de detalles y insertarlo
							var detailsHtml = formatDetailsCargas(codigo, docente, curso, asignatura, ih, periodo, actividades, actividadesRegistradas, directorGrupo, permiso2, indicadorAutomatico, maxIndicadores, maxCalificaciones, cantidadEstudiantes, activa, cargaId);
							$(detailsHtml).insertAfter(button.closest('tr')).slideDown(300);
							
							icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
							button.removeClass('text-secondary').addClass('text-primary');
						}
					});
					
					// Mostrar mensaje de resultados
					$.toast({
						heading: 'Filtros Aplicados',
						text: 'Se encontraron ' + response.total + ' carga(s)',
						position: 'top-right',
						loaderBg: '#26c281',
						icon: 'success',
						hideAfter: 3000
					});
				} else {
					console.error('Error del servidor:', response.error);
					if (response.trace) {
						console.error('Trace:', response.trace);
					}
					if (response.file) {
						console.error('File:', response.file, 'Line:', response.line);
					}
					
					$.toast({
						heading: 'Error',
						text: response.error || 'Error al aplicar filtros',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 5000
					});
					
					$('#cargas_result').html('<tr><td colspan="11" class="text-center text-danger">Error al cargar los datos. Ver consola para detalles.</td></tr>');
				}
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX cargas:', status, error);
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
				
				$('#cargas_result').html('<tr><td colspan="11" class="text-center text-danger">Error de conexión</td></tr>');
			}
		});
	}
	
	// Limpiar filtros de cargas
	$('#btnLimpiarFiltrosCargas').on('click', function() {
		$('#filtro_cargas_cursos').val(null).trigger('change');
		$('#filtro_cargas_grupos').val(null).trigger('change');
		$('#filtro_cargas_docentes').val(null).trigger('change');
		$('#filtro_cargas_periodos').val(null).trigger('change');
		
		// Recargar la página para mostrar todas las cargas
		location.reload();
	});
	
	// Aplicar filtros automáticamente al cambiar las opciones
	$('.select2-multiple-cargas').on('change', function() {
		clearTimeout(window.filtroCargasTimeout);
		window.filtroCargasTimeout = setTimeout(function() {
			aplicarFiltrosCargas();
		}, 500);
	});
	
	// ========================================
	// GENERACIÓN ASÍNCRONA DE INFORMES
	// ========================================
	
	// Event delegation para botones de generar informe
	$(document).on('click', '.btn-generar-informe-async', function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		const datosBtn = $(this).data('carga');
		
		if (!datosBtn) {
			console.error('No se encontraron datos de la carga');
			return;
		}
		
		// Cerrar el dropdown
		$(this).closest('.dropdown-menu').removeClass('show');
		$(this).closest('.btn-group').find('.dropdown-toggle').dropdown('toggle');
		
		// Confirmar con SweetAlert2 moderno
		Swal.fire({
			title: '📊 Generar Informe',
			html: `
				<div style="text-align: left; padding: 20px;">
					<p style="font-size: 15px; margin-bottom: 20px; color: #6b7280;">
						Se generará el informe del boletín para:
					</p>
					<div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
						<div style="display: grid; gap: 12px;">
							<div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: white; border-radius: 8px;">
								<span style="color: #9ca3af; font-weight: 600;">📚 Carga:</span>
								<span style="color: #1f2937; font-weight: 700;">${datosBtn.carga}</span>
							</div>
							<div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: white; border-radius: 8px;">
								<span style="color: #9ca3af; font-weight: 600;">📅 Periodo:</span>
								<span style="color: #1f2937; font-weight: 700;">${datosBtn.periodo}</span>
							</div>
							<div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: white; border-radius: 8px;">
								<span style="color: #9ca3af; font-weight: 600;">🎓 Curso/Grupo:</span>
								<span style="color: #1f2937; font-weight: 700;">${datosBtn.grado} - ${datosBtn.grupo}</span>
							</div>
						</div>
					</div>
					<div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px;">
						<p style="margin: 0; color: #92400e; font-size: 14px;">
							<i class="fa fa-info-circle"></i>
							<strong>Importante:</strong> Este proceso puede tomar unos segundos dependiendo de la cantidad de estudiantes.
						</p>
					</div>
				</div>
			`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: '<i class="fa fa-check"></i> Sí, Generar',
			cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
			confirmButtonColor: '#667eea',
			cancelButtonColor: '#6b7280',
			customClass: {
				popup: 'swal-modern',
				confirmButton: 'btn-modern-confirm',
				cancelButton: 'btn-modern-cancel'
			},
			showLoaderOnConfirm: true,
			preConfirm: () => {
				return generarInformeAsincrono(datosBtn);
			},
			allowOutsideClick: () => !Swal.isLoading()
		});
	});
	
	// Función para generar el informe de forma asíncrona
	function generarInformeAsincrono(datos) {
		console.log('=== Iniciando generación de informe ===');
		console.log('Datos a enviar:', datos);
		
		return fetch('ajax-generar-informe-manual.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(datos)
		})
		.then(response => {
			console.log('Response status:', response.status);
			console.log('Response ok:', response.ok);
			
			if (!response.ok) {
				throw new Error('Error de red: ' + response.status);
			}
			
			return response.text().then(text => {
				console.log('Response text:', text);
				try {
					return JSON.parse(text);
				} catch (e) {
					console.error('Error parsing JSON:', e);
					console.error('Response text:', text);
					throw new Error('Respuesta inválida del servidor: ' + text.substring(0, 100));
				}
			});
		})
		.then(data => {
			console.log('Data recibida:', data);
			
			if (!data.success) {
				// Cerrar el loading de SweetAlert
				Swal.close();
				
				// Convertir saltos de línea a HTML
				const mensajeHTML = (data.message || 'Error desconocido')
					.replace(/\n\n/g, '</p><p style="margin: 10px 0;">')
					.replace(/\n/g, '<br>');
				
				// Mostrar error con formato mejorado
				Swal.fire({
					title: '⚠️ No se puede Generar',
					html: `
						<div style="text-align: left; padding: 20px;">
							<div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
								<p style="margin: 0; color: #991b1b; line-height: 1.6;">
									${mensajeHTML}
								</p>
							</div>
							<p style="text-align: center; color: #6b7280; font-size: 14px; margin: 0;">
								<i class="fa fa-lightbulb-o"></i> Corrige el problema e intenta nuevamente
							</p>
						</div>
					`,
					icon: 'error',
					confirmButtonText: '<i class="fa fa-check"></i> Entendido',
					confirmButtonColor: '#ef4444',
					customClass: {
						popup: 'swal-modern'
					}
				});
				
				// Rechazar la promesa para detener el flujo
				throw new Error(data.message);
			}
			
			// Éxito - mostrar resultado
			Swal.close();
			
			const mensajeConfig = data.mensaje_configuracion ? 
				`<p style="margin: 0 0 10px 0; color: #d97706;"><i class="fa fa-info-circle"></i> ${data.mensaje_configuracion}</p>` : '';
			
			Swal.fire({
				title: '✅ ¡Informe Generado!',
				html: `
					<div style="text-align: center; padding: 20px;">
						<div style="font-size: 64px; margin-bottom: 20px;">🎉</div>
						<h3 style="color: #10b981; margin-bottom: 20px;">Generación Exitosa</h3>
						<div style="background: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; border-radius: 8px; text-align: left;">
							<p style="margin: 0 0 10px 0; color: #065f46;"><strong>📊 Actividades:</strong> ${data.actividades_declaradas}% - ${data.actividades_registradas}%</p>
							<p style="margin: 0 0 10px 0; color: #065f46;"><strong>👥 Estudiantes procesados:</strong> ${data.estudiantes_procesados}</p>
							${data.estudiantes_omitidos > 0 ? `<p style="margin: 0 0 10px 0; color: #78350f;"><strong>⚠️ Estudiantes omitidos:</strong> ${data.estudiantes_omitidos}</p>` : ''}
							${mensajeConfig}
							<p style="margin: 0 0 10px 0; color: #065f46;"><strong>📅 Periodo procesado:</strong> ${data.periodo_procesado}</p>
							<p style="margin: 0; color: #065f46;"><strong>➡️ Periodo siguiente:</strong> ${data.periodo_siguiente}</p>
						</div>
						<p style="margin-top: 20px; color: #6b7280; font-size: 14px;">
							El informe ha sido generado y la carga ha avanzado al periodo ${data.periodo_siguiente}.
						</p>
					</div>
				`,
				icon: 'success',
				confirmButtonText: '<i class="fa fa-check"></i> Entendido',
				confirmButtonColor: '#10b981',
				customClass: {
					popup: 'swal-modern'
				}
			}).then(() => {
				// Recargar solo la tabla de cargas sin recargar toda la página
				recargarTablaCargas();
			});
			
			return data;
		})
		.catch(error => {
			console.error('=== Error capturado ===');
			console.error('Error:', error);
			console.error('Error message:', error.message);
			console.error('Error stack:', error.stack);
			
			// Cerrar cualquier modal de SweetAlert que esté abierto
			Swal.close();
			
			// Mostrar error al usuario
			Swal.fire({
				title: '❌ Error al Generar Informe',
				html: `
					<div style="text-align: left; padding: 20px;">
						<div style="font-size: 48px; text-align: center; margin-bottom: 20px;">😔</div>
						<div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
							<p style="margin: 0; color: #991b1b; font-size: 15px; line-height: 1.6;">
								<strong>Error:</strong><br>
								${error.message || error || 'Error desconocido'}
							</p>
						</div>
						<p style="text-align: center; color: #6b7280; font-size: 14px; margin: 0;">
							<i class="fa fa-lightbulb-o"></i> Si el problema persiste, contacta al soporte técnico
						</p>
					</div>
				`,
				icon: 'error',
				confirmButtonText: '<i class="fa fa-redo"></i> Entendido',
				confirmButtonColor: '#ef4444',
				customClass: {
					popup: 'swal-modern'
				}
			});
			
			// NO hacer throw para evitar "Uncaught (in promise)"
			// Retornar undefined para que SweetAlert maneje el flujo
			return undefined;
		});
	}
});
</script>

<!-- SweetAlert2 Custom Styles -->
<style>
.swal-modern {
	border-radius: 16px !important;
}

.btn-modern-confirm {
	border-radius: 8px !important;
	padding: 12px 30px !important;
	font-weight: 600 !important;
	box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
	transition: all 0.3s ease !important;
}

.btn-modern-confirm:hover {
	transform: translateY(-2px) !important;
	box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
}

.btn-modern-cancel {
	border-radius: 8px !important;
	padding: 12px 30px !important;
	font-weight: 600 !important;
}
</style>

<script>
// Al confirmar la generación, mostrar resultado
$(document).on('click', '.swal2-confirm', function() {
	// Este evento se dispara después de preConfirm
	setTimeout(function() {
		if (Swal.getConfirmButton() && Swal.getConfirmButton().disabled) {
			// Mostrar estado de carga personalizado
			Swal.update({
				title: '⏳ Generando Informe...',
				html: `
					<div style="text-align: center; padding: 30px;">
						<div class="loading-spinner" style="width: 60px; height: 60px; border: 4px solid #e5e7eb; border-top-color: #667eea; border-radius: 50%; margin: 0 auto 20px; animation: spin 1s linear infinite;"></div>
						<p style="color: #6b7280;">Por favor espera mientras procesamos el informe...</p>
					</div>
				`,
				showConfirmButton: false,
				allowOutsideClick: false
			});
		}
	}, 100);
});
</script>

<style>
@keyframes spin {
	to { transform: rotate(360deg); }
}
</style>

<?php
// Incluir modal de transferir cargas
$idModal = "modalTranferirCargas";
$contenido = "../directivo/cargas-transferir-modal.php";
include("../compartido/contenido-modal.php");
?>

</body>

</html>