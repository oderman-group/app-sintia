<?php
include("session.php");
$idPaginaInterna = 'DT0102';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");

 $configAdmisiones=Inscripciones::configuracionAdmisiones($conexion,$baseDatosAdmisiones,$config['conf_id_institucion'],$_SESSION["bd"]);

$urlInscripcion=REDIRECT_ROUTE.'/admisiones/';
if (!isset($_SESSION['cacheInscripciones'])) {
	$_SESSION['cacheInscripciones'] = [];
}
$cacheInscripciones = &$_SESSION['cacheInscripciones'];
$catalogoGrados = $cacheInscripciones['grados'] ?? [];
if (empty($catalogoGrados)) {
	$grados = Grados::listarGrados(1);
	while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
		$catalogoGrados[] = $grado;
	}
	$cacheInscripciones['grados'] = $catalogoGrados;
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css"/>
	<!-- select2 -->
	<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
	<style>
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
		
		.expandable-row {
			background-color: #f8f9fa !important;
		}
		
		/* ========================================
		   TABS PARA VISIBLES/OCULTOS
		   ======================================== */
		
		.tabs-inscripciones {
			display: flex;
			border-bottom: 2px solid #e0e0e0;
			margin-bottom: 20px;
			gap: 5px;
		}
		
		.tab-inscripcion {
			padding: 12px 24px;
			cursor: pointer;
			border: none;
			background: transparent;
			color: #666;
			font-weight: 500;
			font-size: 14px;
			transition: all 0.2s ease;
			border-bottom: 3px solid transparent;
			display: flex;
			align-items: center;
			gap: 8px;
		}
		
		.tab-inscripcion:hover {
			color: #667eea;
			background: #f8f9fa;
		}
		
		.tab-inscripcion.active {
			color: #667eea;
			border-bottom-color: #667eea;
			font-weight: 600;
		}
		
		.tab-inscripcion .badge {
			font-size: 11px;
			padding: 3px 8px;
			border-radius: 10px;
		}
		
		.tab-content-inscripcion {
			display: none;
		}
		
		.tab-content-inscripcion.active {
			display: block;
		}
		
		/* ========================================
		   SKELETON LOADER
		   ======================================== */
		.skeleton-loader {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: #f5f5f5;
			z-index: 99999;
			overflow-y: auto;
		}
		
		.skeleton-content {
			padding: 20px;
			max-width: 100%;
		}
		
		.skeleton-page-bar {
			height: 60px;
			background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
			background-size: 200% 100%;
			border-radius: 4px;
			margin-bottom: 20px;
			animation: skeleton-loading 1.5s ease-in-out infinite;
		}
		
		.skeleton-description {
			height: 20px;
			width: 70%;
			background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
			background-size: 200% 100%;
			border-radius: 4px;
			margin-bottom: 20px;
			animation: skeleton-loading 1.5s ease-in-out infinite;
		}
		
		.skeleton-buttons {
			display: flex;
			gap: 10px;
			margin-bottom: 20px;
			flex-wrap: wrap;
		}
		
		.skeleton-button {
			height: 38px;
			width: 150px;
			background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
			background-size: 200% 100%;
			border-radius: 4px;
			animation: skeleton-loading 1.5s ease-in-out infinite;
		}
		
		.skeleton-card {
			background: white;
			border-radius: 4px;
			padding: 20px;
			margin-bottom: 20px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.1);
		}
		
		.skeleton-table-header {
			height: 40px;
			background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
			background-size: 200% 100%;
			border-radius: 4px;
			margin-bottom: 15px;
			animation: skeleton-loading 1.5s ease-in-out infinite;
		}
		
		.skeleton-table-row {
			height: 50px;
			background: linear-gradient(90deg, #f8f8f8 25%, #f0f0f0 50%, #f8f8f8 75%);
			background-size: 200% 100%;
			border-radius: 4px;
			margin-bottom: 10px;
			animation: skeleton-loading 1.5s ease-in-out infinite;
		}
		
		@keyframes skeleton-loading {
			0% {
				background-position: 200% 0;
			}
			100% {
				background-position: -200% 0;
			}
		}
		
		.skeleton-loader.hidden {
			opacity: 0;
			visibility: hidden;
			transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
		}
		
		.page-content-wrapper {
			opacity: 0;
			transition: opacity 0.5s ease-in;
		}
		
		.page-content-wrapper.loaded {
			opacity: 1;
		}
	</style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
	<!-- Skeleton Loader -->
	<div class="skeleton-loader" id="skeletonLoader">
		<div class="skeleton-content">
			<!-- Page Bar Skeleton -->
			<div class="skeleton-page-bar"></div>
			
			<!-- Description Skeleton -->
			<div class="skeleton-description"></div>
			
			<!-- Buttons Skeleton -->
			<div class="skeleton-buttons">
				<div class="skeleton-button"></div>
				<div class="skeleton-button" style="width: 180px;"></div>
				<div class="skeleton-button" style="width: 200px;"></div>
				<div class="skeleton-button" style="width: 160px;"></div>
			</div>
			
			<!-- Card Skeleton -->
			<div class="skeleton-card">
				<div class="skeleton-table-header"></div>
				<div class="skeleton-table-row"></div>
				<div class="skeleton-table-row"></div>
				<div class="skeleton-table-row"></div>
				<div class="skeleton-table-row"></div>
				<div class="skeleton-table-row"></div>
				<div class="skeleton-table-row"></div>
				<div class="skeleton-table-row"></div>
			</div>
		</div>
	</div>
	
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper" id="pageContentWrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción de la página -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                                <i class="fa fa-info-circle text-info"></i> 
                                Administra las solicitudes de inscripción de nuevos estudiantes. Aquí puedes revisar, aprobar o rechazar solicitudes, gestionar la documentación y realizar el proceso de admisión. 
                                Utiliza los filtros para buscar por estado, año o grado. Expande cada registro para ver toda la información del aspirante y su acudiente.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
                                           
                                <!-- Mensajes informativos colapsables -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-warning text-white" style="cursor: pointer; padding: 10px 15px;" data-toggle="collapse" data-target="#alertaDisco">
                                                <i class="fa fa-database"></i> 
                                                <strong><?=__('inscripciones.liberar_espacio_disco');?></strong>
                                                <i class="fa fa-chevron-down pull-right"></i>
                                            </div>
                                            <div id="alertaDisco" class="collapse">
                                                <div class="card-body">
                                                    <p class="mb-2"><?=__('inscripciones.recomendacion_descargar_docs');?></p>
                                                    <p class="mb-0"><strong><?=__('general.instruccion');?>:</strong> <?=__('inscripciones.instruccion_borrar_docs');?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-success text-white" style="cursor: pointer; padding: 10px 15px;" data-toggle="collapse" data-target="#alertaEnlace">
                                                <i class="fa fa-link"></i> 
                                                <strong>Enlace de inscripción</strong>
                                                <i class="fa fa-chevron-down pull-right"></i>
                                            </div>
                                            <div id="alertaEnlace" class="collapse">
                                                <div class="card-body">
                                                    <p class="mb-2">Para ir al formulario de inscripción <a href="<?=$urlInscripcion?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> Abrir formulario</a></p>
                                                    <label class="mb-1"><strong>Copiar enlace para enviar:</strong></label>
                                                    <div class="input-group">
                                                        <input type="text" id="enlaceInscripcion" class="form-control" value="<?=$urlInscripcion?>" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="copiarEnlace()" title="Copiar enlace">
                                                                <i class="fa fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    $filtro=""; 
                                    include(ROOT_PATH."/config-general/config-admisiones.php");
                                    include(ROOT_PATH."/config-general/mensajes-informativos.php");
                                    // Barra superior antigua - removida
                                    // include("includes/barra-superior-inscripciones-componente.php");
                                    
                                    // Asegurar que las variables necesarias estén disponibles
                                    if (!isset($estadosSolicitud)) {
                                        $estadosSolicitud = [
                                            1 => 'Pendiente',
                                            2 => 'En proceso',
                                            3 => 'En revisión',
                                            4 => 'Rechazada',
                                            5 => 'Cancelada',
                                            6 => 'Aprobada',
                                            7 => 'Otro'
                                        ];
                                    }
                                ?>

                                    <?php if (isset($_GET["msg"]) and base64_decode($_GET["msg"]) == 1) { ?>
                                    <div class="alert alert-block alert-success">
                                        <h4 class="alert-heading">Documentación eliminada!</h4>
                                        <p>La documentación del aspirante se ha borrado correctamente.</p>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($_GET["msg"]) and base64_decode($_GET["msg"]) == 2) { ?>
                                    <div class="alert alert-block alert-success">
                                        <h4 class="alert-heading">Apisrante eliminado!</h4>
                                        <p>El aspirante se ha borrado correctamente.</p>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($_GET["msg"]) and base64_decode($_GET["msg"]) == 3) { ?>
                                    <div class="alert alert-block alert-success">
                                        <h4 class="alert-heading">Apisrante ocultado!</h4>
                                        <p>El aspirante se ha ocultado correctamente.</p>
                                    </div>
                                    <?php } ?>
                                    
                                    <!-- Barra de herramientas superior -->
                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <!-- Botón de filtros -->
                                                <button type="button" class="btn btn-outline-secondary" id="btnToggleFiltrosInscripciones">
                                                    <i class="fa fa-filter"></i> Filtros y Búsqueda
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Panel de Filtros Colapsable -->
                                    <div class="card card-topline-purple mb-3" id="cardFiltrosInscripciones" style="display: none;">
                                        <div class="card-body">
                                            <h5 class="mb-3"><i class="fa fa-filter"></i> Filtros Avanzados y Búsqueda</h5>
                                            
                                            <!-- Buscador General -->
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-search"></i> Búsqueda General</label>
                                                        <div class="input-group">
                                                            <input type="text" id="filtro_inscripciones_busqueda" class="form-control" placeholder="Buscar por nombre, documento, email del acudiente...">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-primary" type="button" id="btnBuscarInscripciones">
                                                                    <i class="fa fa-search"></i> Buscar
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Busca por: nombres del aspirante, documento, email del acudiente, nombre del acudiente
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <!-- Filtros por categoría -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-graduation-cap"></i> Grado</label>
                                                        <select id="filtro_inscripciones_grado" class="form-control select2-multiple-inscripciones" multiple="multiple" style="width: 100%;">
                                                            <?php foreach ($catalogoGrados as $grado) { ?>
                                                                <option value="<?=$grado['gra_id'];?>"><?=$grado['gra_nombre'];?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-info-circle"></i> Estado de Solicitud</label>
                                                        <select id="filtro_inscripciones_estado" class="form-control select2-multiple-inscripciones" multiple="multiple" style="width: 100%;">
                                                            <?php foreach($estadosSolicitud as $key => $value) {?>
                                                                <option value="<?=$key;?>"><?=$value;?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><i class="fa fa-calendar"></i> Año</label>
                                                        <select id="filtro_inscripciones_anio" class="form-control select2-multiple-inscripciones" multiple="multiple" style="width: 100%;">
                                                            <?php 
                                                            $anioActual = date('Y');
                                                            for($i = $anioActual; $i >= $anioActual - 3; $i--) {
                                                            ?>
                                                                <option value="<?=$i;?>"><?=$i;?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12 text-right">
                                                    <button type="button" class="btn btn-secondary" id="btnLimpiarFiltrosInscripciones">
                                                        <i class="fa fa-eraser"></i> Limpiar Filtros
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        
                                        <?php
                                        // Contar inscripciones visibles y ocultas usando el mismo método
                                        $totalVisibles = 0;
                                        $totalOcultos = 0;
                                        
                                        try {
                                            $selectSql = ["mat_nombres","mat_primer_apellido","mat_segundo_apellido",
                                                          "asp_agno","asp_email_acudiente","asp_estado_solicitud", "mat_nombre2",
                                                          "mat.*", "asp.*"];
                                            
                                            // Contar visibles (asp_oculto IS NULL OR asp_oculto=0)
                                            $filtroVisibles = ' AND (asp.asp_oculto IS NULL OR asp.asp_oculto=0)';
                                            $consultaVisibles = Estudiantes::listarMatriculasAspirantes($config, $filtroVisibles, "", "", $selectSql);
                                            if ($consultaVisibles) {
                                                $totalVisibles = mysqli_num_rows($consultaVisibles);
                                            }
                                            
                                            // Contar ocultos (asp_oculto=1)
                                            $filtroOcultos = ' AND (asp.asp_oculto=1)';
                                            $consultaOcultos = Estudiantes::listarMatriculasAspirantes($config, $filtroOcultos, "", "", $selectSql);
                                            if ($consultaOcultos) {
                                                $totalOcultos = mysqli_num_rows($consultaOcultos);
                                            }
                                        } catch (Exception $e) {
                                            error_log('Error al contar inscripciones: ' . $e->getMessage());
                                            // Los totales quedan en 0 en caso de error
                                        }
                                        ?>
                                        
                                        <!-- Tabs para visibles/ocultos -->
                                        <?php 
                                        $tabActivo = isset($_GET['tab']) ? $_GET['tab'] : 'visibles';
                                        ?>
                                        <div class="tabs-inscripciones">
                                            <button class="tab-inscripcion <?= $tabActivo === 'visibles' ? 'active' : ''; ?>" onclick="cambiarTabInscripciones('visibles')" id="tab-visibles">
                                                <i class="fa fa-eye"></i> 
                                                <span>Visibles</span>
                                                <span class="badge badge-primary" id="badge-visibles"><?= $totalVisibles; ?></span>
                                            </button>
                                            <button class="tab-inscripcion <?= $tabActivo === 'ocultos' ? 'active' : ''; ?>" onclick="cambiarTabInscripciones('ocultos')" id="tab-ocultos">
                                                <i class="fa fa-eye-slash"></i> 
                                                <span>Ocultos</span>
                                                <span class="badge badge-secondary" id="badge-ocultos"><?= $totalOcultos; ?></span>
                                            </button>
                                        </div>
                                        
                                        <!-- Contenido tab VISIBLES -->
                                        <div id="content-visibles" class="tab-content-inscripcion <?= $tabActivo === 'visibles' ? 'active' : ''; ?>">
                                        <?php if ($tabActivo === 'visibles') { ?>
                                        <div class="table">
                                    		<table id="example1" class="table table-striped table-bordered" style="width:100%;">
                                            <div id="gifCarga" class="gif-carga">
										        <img   alt="Cargando...">
									        </div>
												<thead>
													<tr>
                                                        <th></th>
                                                        <th>No.</th>
                                                        <th>ID Matrícula</th>
                                                        <th>#Solicitud</th>
                                                        <th>Fecha</th>
                                                        <th>Documento</th>
                                                        <th>Aspirante</th>
                                                        <th>Año</th>
                                                        <th>Estado</th>
                                                        <th>Comprobante</th>
                                                        <th>Grado</th>
                                                        <th>Acciones</th>
													</tr>
												</thead>
                                                <tbody id="inscripciones_result">
                                                <?php
                                                try {
                                                    // Filtro para mostrar solo visibles
                                                    $filtro = ' AND (asp.asp_oculto IS NULL OR asp.asp_oculto=0)';
                                                    
                                                    include("includes/consulta-paginacion-inscripciones.php");
                                                    $selectSql = ["mat_id","mat_documento","gra.gra_nombre",
																  "asp_observacion","asp_nombre_acudiente","asp_celular_acudiente",
																  "asp_documento_acudiente","asp_id","asp_fecha","asp_comprobante","mat_nombres",
																  "asp_agno","asp_email_acudiente","asp_estado_solicitud", "mat_nombre2", "mat_primer_apellido", "mat_segundo_apellido",
																  "mat.*", "asp.*"];
                                                    
                                                    // $filtroLimite ya está definido en consulta-paginacion-inscripciones.php
                                                    $consulta = Estudiantes::listarMatriculasAspirantes($config, $filtro, $filtroLimite,"",$selectSql);
                                                    
                                                    // Construir array de datos manualmente
                                                    $data = ["data" => []];
                                                    if (!empty($consulta)) {
                                                        while ($fila = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                                                            $data["data"][] = $fila;
                                                        }
                                                    }
													
													$mostrarOcultos = false; // Contexto de visibles
													
													// Verificar si hay datos para mostrar
													if (empty($data["data"])) {
													    echo '<tr><td colspan="12" class="text-center">
													        <i class="fa fa-info-circle fa-2x text-info"></i><br>
													        <strong>No hay inscripciones visibles en este momento.</strong>
													    </td></tr>';
													} else {
													    include("../class/componentes/result/inscripciones-tbody.php");
													}
                                                } catch (Exception $e) {
                                                    echo '<tr><td colspan="12" class="text-center text-warning">
                                                        <i class="fa fa-exclamation-triangle fa-2x"></i><br>
                                                        <strong>No se pudieron cargar las inscripciones en este momento.</strong><br>
                                                        Por favor, intente nuevamente más tarde.
                                                    </td></tr>';
                                                    error_log('Error en inscripciones visibles: ' . $e->getMessage());
                                                }
                                                 ?>
                                                </tbody>
                                            </table>
                                            </div>
                                            
                                            <!-- Paginación para visibles -->
                                            <?php 
                                            if (!empty($numRegistros) && $numRegistros > 0) {
                                                include("enlaces-paginacion.php");
                                            }
                                            ?>
                                        </div>
                                        <?php } ?>
                                        </div><!-- Cierre content-visibles -->
                                        
                                        <!-- Contenido tab OCULTOS -->
                                        <div id="content-ocultos" class="tab-content-inscripcion <?= $tabActivo === 'ocultos' ? 'active' : ''; ?>">
                                        <?php if ($tabActivo === 'ocultos') { ?>
                                        <div class="table">
                                    		<table id="example2" class="table table-striped table-bordered" style="width:100%;">
                                            <div id="gifCarga2" class="gif-carga">
										        <img   alt="Cargando...">
									        </div>
												<thead>
													<tr>
                                                        <th></th>
                                                        <th>No.</th>
                                                        <th>ID Matrícula</th>
                                                        <th>#Solicitud</th>
                                                        <th>Fecha</th>
                                                        <th>Documento</th>
                                                        <th>Aspirante</th>
                                                        <th>Año</th>
                                                        <th>Estado</th>
                                                        <th>Comprobante</th>
                                                        <th>Grado</th>
                                                        <th>Acciones</th>
													</tr>
												</thead>
                                                <tbody id="inscripciones_ocultos_result">
													<?php
													try {
                                                            $filtroOcultos = ' AND (asp.asp_oculto=1)';
                                                            
                                                            // Aplicar paginación también para ocultos
                                                            include("includes/consulta-paginacion-inscripciones.php");
                                                            // Recalcular con el filtro de ocultos
                                                            $consultaTotalOcultos = Estudiantes::listarMatriculasAspirantes($config, $filtroOcultos);
                                                            $numRegistrosOcultos = mysqli_num_rows($consultaTotalOcultos);
                                                            if ($consultaTotalOcultos) {
                                                                mysqli_free_result($consultaTotalOcultos);
                                                            }
                                                            // Recalcular inicio y límite
                                                            $registros = !empty($config['conf_num_registros']) ? (int)$config['conf_num_registros'] : 20;
                                                            $pagina = base64_decode($_REQUEST["nume"]);
                                                            if (is_numeric($pagina) && $pagina > 0){
                                                                $inicio = (($pagina-1)*$registros);
                                                            } else {
                                                                $inicio = 0;
                                                                $pagina = 1;
                                                            }
                                                            $filtroLimite = 'LIMIT '.$inicio.','.$registros;
                                                            $numRegistros = $numRegistrosOcultos; // Para la paginación
                                                            
                                                            $selectSql = ["mat_nombres","mat_primer_apellido","mat_segundo_apellido",
                                                                          "asp_agno","asp_email_acudiente","asp_estado_solicitud", "mat_nombre2",
                                                                          "gra.gra_nombre",
                                                                          "mat.*", "asp.*"];

                                                            $consultaOcultos = Estudiantes::listarMatriculasAspirantes($config, $filtroOcultos, $filtroLimite,"",$selectSql);
                                                            
                                                            // Construir array de datos para ocultos
                                                            $dataOcultos = ["data" => []];
                                                            if (!empty($consultaOcultos)) {
                                                                while ($fila = mysqli_fetch_array($consultaOcultos, MYSQLI_BOTH)) {
                                                                    $dataOcultos["data"][] = $fila;
                                                                }
                                                            }
                                                            
                                                            $contReg = 1;
                                                            $mostrarOcultos = true; // Contexto de ocultos
                                                            $data = $dataOcultos; // Usar los datos de ocultos
                                                            
                                                            // Verificar si hay datos para mostrar
                                                            if (empty($data["data"])) {
                                                                echo '<tr><td colspan="12" class="text-center">
                                                                    <i class="fa fa-info-circle fa-2x text-info"></i><br>
                                                                    <strong>No hay inscripciones ocultas en este momento.</strong>
                                                                </td></tr>';
                                                            } else {
                                                                include(ROOT_PATH."/main-app/class/componentes/result/inscripciones-tbody.php");
                                                            }
                                                    } catch (Exception $e) {
                                                        echo '<tr><td colspan="12" class="text-center text-warning">
                                                            <i class="fa fa-exclamation-triangle fa-2x"></i><br>
                                                            <strong>No se pudieron cargar las inscripciones ocultas en este momento.</strong><br>
                                                            Por favor, intente nuevamente más tarde.
                                                        </td></tr>';
                                                        error_log('Error en inscripciones ocultas: ' . $e->getMessage());
                                                    }
													?>
                                                </tbody>
                                            </table>
                                            </div>
                                            
                                            <!-- Paginación para ocultos -->
                                            <?php 
                                            if (!empty($numRegistros) && $numRegistros > 0) {
                                                include("enlaces-paginacion.php");
                                            }
                                            ?>
                                        </div>
                                        <?php } ?>
                                        </div><!-- Cierre content-ocultos -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                function crearDatos(dato) {
                    console.log(dato);
            };
            </script>
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
		
		// Función para copiar el enlace de inscripción
		function copiarEnlace() {
			var enlaceInput = document.getElementById('enlaceInscripcion');
			enlaceInput.select();
			enlaceInput.setSelectionRange(0, 99999); // Para móviles
			
			try {
				document.execCommand('copy');
				$.toast({
					heading: 'Copiado',
					text: 'Enlace copiado al portapapeles',
					position: 'top-right',
					loaderBg: '#26c281',
					icon: 'success',
					hideAfter: 2000
				});
			} catch (err) {
				alert('Error al copiar: ' + err);
			}
		}
		
		// Cambiar icono del chevron al expandir/colapsar
		$('[data-toggle="collapse"]').on('click', function() {
			var icon = $(this).find('.fa-chevron-down, .fa-chevron-up');
			setTimeout(function() {
				if (icon.hasClass('fa-chevron-down')) {
					icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
				} else {
					icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
				}
			}, 10);
		});
		
		// === Filtros Avanzados para Inscripciones ===
		
		// Toggle del panel de filtros
		$('#btnToggleFiltrosInscripciones').on('click', function() {
			const card = $('#cardFiltrosInscripciones');
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
		$('.select2-multiple-inscripciones').select2({
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
		
		// Función para aplicar filtros de inscripciones
		function aplicarFiltrosInscripciones() {
			const grados = $('#filtro_inscripciones_grado').val() || [];
			const estados = $('#filtro_inscripciones_estado').val() || [];
			const anios = $('#filtro_inscripciones_anio').val() || [];
			const busqueda = $('#filtro_inscripciones_busqueda').val() || '';
			
			// Detectar qué tab está activo
			const tabActivo = $('.tab-inscripcion.active').attr('id') === 'tab-ocultos' ? 'ocultos' : 'visibles';
			const targetId = tabActivo === 'ocultos' ? '#inscripciones_ocultos_result' : '#inscripciones_result';
			
			console.log('Aplicando filtros inscripciones:', { grados, estados, anios, busqueda, tab: tabActivo });
			
			// Mostrar loader
			$(targetId).html('<tr><td colspan="12" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Cargando...</td></tr>');
			
			// Enviar AJAX
			$.ajax({
				url: 'ajax-filtrar-inscripciones.php',
				type: 'POST',
				data: {
					grados: grados,
					estados: estados,
					anios: anios,
					busqueda: busqueda,
					tab: tabActivo
				},
				dataType: 'json',
				success: function(response) {
					console.log('Respuesta del filtro inscripciones:', response);
					
					if (response.success) {
						// Insertar el HTML en el tab activo
						$(targetId).html(response.html);
						
						// No necesitamos DataTables - la paginación se maneja del lado del servidor
						
						// Mensaje dinámico
						let mensaje = 'Se encontraron ' + response.total + ' inscripción/inscripciones';
						if (busqueda.trim() !== '') {
							mensaje += ' para: "' + busqueda + '"';
						}
						
						// Mostrar mensaje de resultados
						$.toast({
							heading: 'Filtros Aplicados',
							text: mensaje,
							position: 'top-right',
							loaderBg: '#26c281',
							icon: 'success',
							hideAfter: 3000
						});
					} else {
						console.error('Error del servidor:', response.error);
						
						$.toast({
							heading: 'Error',
							text: response.error || 'Error al aplicar filtros',
							position: 'top-right',
							loaderBg: '#bf441d',
							icon: 'error',
							hideAfter: 5000
						});
						
						$(targetId).html('<tr><td colspan="12" class="text-center text-danger">Error al cargar los datos</td></tr>');
					}
				},
				error: function(xhr, status, error) {
					console.error('Error AJAX inscripciones:', status, error);
					console.error('Response:', xhr.responseText);
					
					$.toast({
						heading: 'Error de Conexión',
						text: 'No se pudo conectar con el servidor',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 5000
					});
					
					$(targetId).html('<tr><td colspan="12" class="text-center text-danger">Error de conexión</td></tr>');
				}
			});
		}
		
		// Botón de buscar
		$('#btnBuscarInscripciones').on('click', function() {
			aplicarFiltrosInscripciones();
		});
		
		// Enter en el campo de búsqueda
		$('#filtro_inscripciones_busqueda').on('keypress', function(e) {
			if (e.which === 13) { // Enter key
				e.preventDefault();
				aplicarFiltrosInscripciones();
			}
		});
		
		// Limpiar filtros de inscripciones
		$('#btnLimpiarFiltrosInscripciones').on('click', function() {
			$('#filtro_inscripciones_grado').val(null).trigger('change');
			$('#filtro_inscripciones_estado').val(null).trigger('change');
			$('#filtro_inscripciones_anio').val(null).trigger('change');
			$('#filtro_inscripciones_busqueda').val('');
			
			// Recargar la página para mostrar todas las inscripciones
			location.reload();
		});
		
		// Aplicar filtros automáticamente al cambiar las opciones
		$('.select2-multiple-inscripciones').on('change', function() {
			clearTimeout(window.filtroInscripcionesTimeout);
			window.filtroInscripcionesTimeout = setTimeout(function() {
				aplicarFiltrosInscripciones();
			}, 500);
		});
		
		// ========================================
		// SKELETON LOADER - Ocultar cuando la página esté cargada
		// ========================================
		function ocultarSkeletonLoader() {
			const skeletonLoader = document.getElementById('skeletonLoader');
			const pageContentWrapper = document.getElementById('pageContentWrapper');
			
			if (skeletonLoader && pageContentWrapper) {
				// Agregar clase para mostrar el contenido
				pageContentWrapper.classList.add('loaded');
				
				// Ocultar skeleton con transición suave
				setTimeout(function() {
					skeletonLoader.classList.add('hidden');
					
					// Remover del DOM después de la transición
					setTimeout(function() {
						skeletonLoader.style.display = 'none';
					}, 500);
				}, 100);
			}
		}
		
		// Prevenir interacciones mientras el skeleton está visible
		document.addEventListener('DOMContentLoaded', function() {
			const skeletonLoader = document.getElementById('skeletonLoader');
			
			if (skeletonLoader) {
				skeletonLoader.addEventListener('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					return false;
				});
				
				skeletonLoader.addEventListener('keydown', function(e) {
					e.preventDefault();
					e.stopPropagation();
					return false;
				});
				
				skeletonLoader.style.userSelect = 'none';
				skeletonLoader.style.pointerEvents = 'auto';
			}
		});
		
		// Ocultar skeleton cuando todo esté cargado
		window.addEventListener('load', function() {
			setTimeout(ocultarSkeletonLoader, 300);
		});
		
		// Fallback: Si window.load no se dispara, ocultar después de un tiempo razonable
		setTimeout(function() {
			const skeletonLoader = document.getElementById('skeletonLoader');
			if (skeletonLoader && !skeletonLoader.classList.contains('hidden')) {
				ocultarSkeletonLoader();
			}
		}, 3000);
		
		// ========================================
		// SISTEMA DE TABS PARA VISIBLES/OCULTOS
		// ========================================
		
		// NO usar DataTables - Usamos paginación del lado del servidor con PHP
		
		// Función para cambiar entre tabs - recarga la página con el tab seleccionado
		window.cambiarTabInscripciones = function(tab) {
			// Obtener parámetros actuales de la URL
			var urlParams = new URLSearchParams(window.location.search);
			var nuevaUrl = 'inscripciones.php?tab=' + tab;
			
			// Mantener el parámetro de página si existe
			if (urlParams.has('nume')) {
				nuevaUrl += '&nume=' + urlParams.get('nume');
			}
			
			// Mantener otros parámetros de filtros si existen
			if (urlParams.has('filtro_grado')) {
				nuevaUrl += '&filtro_grado=' + urlParams.get('filtro_grado');
			}
			if (urlParams.has('filtro_estado')) {
				nuevaUrl += '&filtro_estado=' + urlParams.get('filtro_estado');
			}
			if (urlParams.has('filtro_anio')) {
				nuevaUrl += '&filtro_anio=' + urlParams.get('filtro_anio');
			}
			if (urlParams.has('busqueda')) {
				nuevaUrl += '&busqueda=' + encodeURIComponent(urlParams.get('busqueda'));
			}
			
			// Recargar la página con el nuevo tab
			window.location.href = nuevaUrl;
		};
		
		// Función para desocultar inscripción
		window.desocultarInscripcion = function(idMatricula) {
			if (!confirm('¿Está seguro de que desea hacer visible esta inscripción?')) {
				return;
			}
			
			$.ajax({
				url: 'ajax-desocultar-inscripcion.php',
				type: 'POST',
				data: { id: idMatricula },
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						$.toast({
							heading: 'Éxito',
							text: response.message,
							position: 'top-right',
							loaderBg: '#26c281',
							icon: 'success',
							hideAfter: 2000
						});
						
						// Recargar ambos tabs para reflejar los cambios
						setTimeout(function() {
							// Recargar el tab de ocultos (actual)
							cambiarTabInscripciones('ocultos');
							
							// Recargar también el tab de visibles en segundo plano
							$.ajax({
								url: 'ajax-cargar-tab-inscripciones.php',
								type: 'POST',
								data: { tab: 'visibles' },
								dataType: 'json',
								success: function(resp) {
									if (resp.success) {
										$('#inscripciones_result').html(resp.html);
										$('#badge-visibles').text(resp.total);
									}
								}
							});
						}, 500);
					} else {
						$.toast({
							heading: 'Error',
							text: response.message,
							position: 'top-right',
							loaderBg: '#bf441d',
							icon: 'error',
							hideAfter: 3000
						});
					}
				},
				error: function(xhr, status, error) {
					console.error('Error AJAX:', error);
					console.error('Response:', xhr.responseText);
					$.toast({
						heading: 'Error de Conexión',
						text: 'No se pudo conectar con el servidor',
						position: 'top-right',
						loaderBg: '#bf441d',
						icon: 'error',
						hideAfter: 3000
					});
				}
			});
		};
	</script>
	
	<style>
	/* Estilos para las alertas colapsables */
	.card-header[data-toggle="collapse"] {
		transition: background-color 0.3s ease;
	}
	
	.card-header[data-toggle="collapse"]:hover {
		opacity: 0.9;
	}
	
	.card-header .fa-chevron-down,
	.card-header .fa-chevron-up {
		transition: transform 0.3s ease;
	}
	
	#alertaDisco .card-body,
	#alertaEnlace .card-body {
		border-top: 2px solid rgba(0,0,0,0.1);
	}
	</style>
</body>

</html>