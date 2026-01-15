<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0099';
if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 para dropdowns con búsqueda -->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Estilos personalizados para informes -->
    <link href="../../config-general/assets/css/informes-style.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title"><i class="fas fa-file-alt"></i> Informes</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div id="gifCarga" class="gif-carga"><img alt="Cargando..."></div>
                            
                            <!-- Descripción de la página -->
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle"></i> <strong>Centro de Informes:</strong> Aquí encontrarás todos los reportes disponibles organizados por categorías. Haz clic en cada categoría para ver los informes disponibles.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <!-- ACORDEÓN DE CATEGORÍAS -->
                            <div class="accordion" id="accordionInformes">

                                <!-- INFORMES ACADÉMICOS -->
                                <?php if(Modulos::validarSubRol(['DT0100','DT0082','DT0134','DT0135','DT0133','DT0101','DT0143','DT0136','DT0120','DT0147', 'DT0307', 'DT0234','DT0140','DT0146','DT0141','DT0194','DT0200', 'DT0346'])){?>
                                <div class="card informe-card">
                                    <div class="card-header informe-header informe-header-academico" id="headingAcademicos">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left informe-btn-toggle" type="button" data-toggle="collapse" data-target="#collapseAcademicos" aria-expanded="true" aria-controls="collapseAcademicos">
                                                <i class="fas fa-graduation-cap informe-icon"></i>
                                                <span class="informe-title">Informes Académicos</span>
                                                <i class="fas fa-chevron-down float-right mt-1"></i>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapseAcademicos" class="collapse show" aria-labelledby="headingAcademicos" data-parent="#accordionInformes">
                                        <div class="card-body informe-card-body">
                                            <div class="row">
                                                <!-- Subcategoría: Informes con calificaciones -->
                                                <?php if(Modulos::validarSubRol(['DT0100','DT0082','DT0135','DT0101','DT0143','DT0140','DT0346','DT0134'])){?>
                                                <div class="col-md-6">
                                                    <div class="informe-subcategory">
                                                        <h5 class="informe-subcategory-title">
                                                            <i class="fas fa-chart-line"></i> Informes con calificaciones
                                                        </h5>
                                                        <ul class="informe-list">
                                                            <?php if(Modulos::validarSubRol(['DT0101'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Informe parcial','informe-parcial-grupo-modal.php')" class="informe-link">
                                                                    <i class="fas fa-chart-line"></i> Informe parcial
                                                                    <span class="badge badge-info">Importante</span>
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Reporte de calificaciones y desempeños por período académico</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0140'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Informe de sábanas','informe-reporte-sabana-modal.php')" class="informe-link">
                                                                    <i class="fas fa-table"></i> Informe de sábanas
                                                                    <span class="badge badge-info">Importante</span>
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Planilla detallada de calificaciones por indicadores y actividades</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0134'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Consolidado de asignaturas perdidas','consolidado-perdidos-modal.php')" class="informe-link">
                                                                    <i class="fas fa-exclamation-triangle"></i> Consolidado de asignaturas perdidas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Listado de estudiantes con asignaturas reprobadas por período</div>
                                                            </li>
                                                            <?php } 
                                                            if(Modulos::validarSubRol(['DT0100'])){
                                                                $modalBoletin = new ComponenteModal('boletines','Boletines','../directivo/informes-boletines-modal.php');
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="#" onclick="event.preventDefault(); event.stopImmediatePropagation(); if(event.isTrusted && event.type==='click'){<?=$modalBoletin->getMetodoAbrirModal()?>} return false;" class="informe-link informe-link-boletines">
                                                                    <i class="fas fa-file-pdf"></i> Boletines
                                                                    <span class="badge badge-info">Importante</span>
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Reporte académico detallado con calificaciones por períodos y áreas</div>
                                                            </li>
                                                            <?php } 
                                                            if(Modulos::validarSubRol(['DT0143'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Informe de consolidado final','consolidado-final-filtro-modal.php')" class="informe-link">
                                                                    <i class="fas fa-file-contract"></i> Informe de consolidado final
                                                                    <span class="badge badge-info">Importante</span>
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Resumen consolidado de calificaciones finales y promedios generales</div>
                                                            </li>
                                                            <?php } 
                                                            if(Modulos::validarSubRol(['DT0135'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Libro final','informe-libro-cursos-modal.php')" class="informe-link">
                                                                    <i class="fas fa-book"></i> Libro final
                                                                    <span class="badge badge-info">Importante</span>
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Consolidado final de calificaciones y desempeños por curso y grupo</div>
                                                            </li>
                                                            <?php } 
                                                            if(Modulos::validarSubRol(['DT0082'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Certificados','estudiantes-certificados-modal.php')" class="informe-link">
                                                                    <i class="fas fa-certificate"></i> Certificados
                                                                    <span class="badge badge-info">Importante</span>
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Documentos oficiales de certificación académica de estudiantes</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0346'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Historico de notas','historico-notas-filtros-modal.php')" class="informe-link">
                                                                    <i class="fas fa-history"></i> Historicos de notas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Consulta del historial completo de calificaciones por estudiante</div>
                                                            </li>
                                                            <?php }?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php }?>

                                                <!-- Subcategoría: Cargas Académicas -->
                                                <?php if(Modulos::validarSubRol(['DT0234','DT0146','DT0141','DT0194','DT0200'])){?>
                                                <div class="col-md-6">
                                                    <div class="informe-subcategory">
                                                        <h5 class="informe-subcategory-title">
                                                            <i class="fas fa-chalkboard-teacher"></i> Cargas Académicas
                                                        </h5>
                                                        <ul class="informe-list">
                                                            <?php if(Modulos::validarSubRol(['DT0234'])){?>
                                                            <li class="informe-item">
                                                                <a href="../compartido/informes-generales-docentes-cargas.php" target="_blank" class="informe-link">
                                                                    <i class="fas fa-user-tie"></i> Docentes y cargas académicas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Listado de docentes con sus asignaturas y grupos asignados</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0146'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="../compartido/informe-cargas-duplicadas.php" target="_blank" class="informe-link">
                                                                    <i class="fas fa-copy"></i> Informe de cargas duplicadas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Identificación de cargas académicas duplicadas en el sistema</div>
                                                            </li>
                                                            <?php } 
                                                            if(Modulos::validarSubRol(['DT0141'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Planilla de asistencia','asistencia-planilla-modal.php')" class="informe-link">
                                                                    <i class="fas fa-clipboard-check"></i> Planilla para colocar notas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Registro y control de notas de estudiantes por período</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0194'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Planilla docentes con notas','planilla-docentes-filtros-modal.php')" class="informe-link">
                                                                    <i class="fas fa-clipboard-list"></i> Planilla docentes con notas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Planilla de docentes con el estado de registro de calificaciones</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0200'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Notas declaradas y registradas','notas-registradas-informes-filtros-modal.php')" class="informe-link">
                                                                    <i class="fas fa-pencil-alt"></i> Notas declaradas y registradas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Control de notas declaradas versus notas registradas en el sistema</div>
                                                            </li>
                                                            <?php }?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php }?>

                                                <!-- Subcategoría: Planillas e información general -->
                                                <?php if(Modulos::validarSubRol(['DT0133','DT0221','DT0136','DT0120','DT0147', 'DT0307', 'DT0222', 'DT0223', 'DT0249','DT0251'])){?>
                                                <div class="col-md-6">
                                                    <div class="informe-subcategory">
                                                        <h5 class="informe-subcategory-title">
                                                            <i class="fas fa-clipboard-list"></i> Planillas e información general
                                                        </h5>
                                                        <ul class="informe-list">
                                                            <?php if(Modulos::validarSubRol(['DT0133'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Listado de estudiantes','informe-estudiantes-modal.php')" class="informe-link">
                                                                    <i class="fas fa-list"></i> Listado de estudiantes
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Listado general de estudiantes con información básica y académica</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0133'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="constancia-estudio.php" class="informe-link">
                                                                    <i class="fas fa-file-signature"></i> Constancia de estudio
                                                                    <span class="badge badge-info">Nuevo</span>
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Documento que certifica que el estudiante está matriculado y cursando estudios en el año lectivo</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0221'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="../compartido/reporte-pasos.php" target="_blank" class="informe-link">
                                                                    <i class="fas fa-shoe-prints"></i> Informe pasos matrícula
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Seguimiento del proceso de matrícula por pasos y estados</div>
                                                            </li>
                                                            <?php } 
                                                            if(Modulos::validarSubRol(['DT0136'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Planilla de estudiantes','estudiantes-planilla-modal.php')" class="informe-link">
                                                                    <i class="fas fa-table"></i> Planilla de estudiantes básica
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Planilla básica de estudiantes con algunos datos</div>
                                                            </li>
                                                            <?php } 
                                                            if(Modulos::validarSubRol(['DT0120'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Reporte general de estudiantes','reportes-academicos-consultas-modal.php')" class="informe-link">
                                                                    <i class="fas fa-chart-bar"></i> Reporte general de estudiantes
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Reporte general con información académica y personal de estudiantes</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0222'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="../compartido/reporte-informe-parcial.php" target="_blank" class="informe-link">
                                                                    <i class="fas fa-file-invoice"></i> Reporte informe parcial
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Reporte consolidado de informes parciales por curso y grupo</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0147'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Reporte de asistencia a entrega de informes','asistencia-entrega-informes-filtros-modal.php')" class="informe-link">
                                                                    <i class="fas fa-user-check"></i> Reporte de asistencia a entrega de informes
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Control de asistencia de acudientes a la entrega de informes académicos</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0223'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="../compartido/informe-matriculas-repetidas.php" target="_blank" class="informe-link">
                                                                    <i class="fas fa-redo"></i> Informe Matriculas repetidas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Identificación de estudiantes con matrículas duplicadas en el sistema</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0307'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Informe Matriculas retiradas','matriculas-retiradas-modal.php')" class="informe-link">
                                                                    <i class="fas fa-user-times"></i> Informe Matriculas retiradas
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Listado de estudiantes con matrícula retirada o cancelada</div>
                                                            </li>
                                                            <?php }
                                                            if(Modulos::validarSubRol(['DT0249','DT0251'])){
                                                            ?>
                                                            <li class="informe-item">
                                                                <a href="javascript:void(0);" onclick="abrirModal('Hoja de Matricula','hoja-matricula-modal.php')" class="informe-link">
                                                                    <i class="fas fa-file-alt"></i> Hoja de Matricula
                                                                </a>
                                                                <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Documento individual de matrícula con información completa del estudiante</div>
                                                            </li>
                                                            <?php }?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>

                                <!-- INFORMES FINANCIEROS -->
                                <?php if(Modulos::validarSubRol(['DT0240', 'DT0331'])){?>
                                <div class="card informe-card">
                                    <div class="card-header informe-header informe-header-financiero" id="headingFinancieros">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed informe-btn-toggle" type="button" data-toggle="collapse" data-target="#collapseFinancieros" aria-expanded="false" aria-controls="collapseFinancieros">
                                                <i class="fas fa-dollar-sign informe-icon"></i>
                                                <span class="informe-title">Informes Financieros</span>
                                                <i class="fas fa-chevron-down float-right mt-1"></i>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseFinancieros" class="collapse" aria-labelledby="headingFinancieros" data-parent="#accordionInformes">
                                        <div class="card-body informe-card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <ul class="informe-list">
                                                        <?php if(Modulos::validarSubRol(['DT0240'])){?>
                                                        <li class="informe-item">
                                                            <a href="javascript:void(0);" onclick="abrirModal('Filtros - Informe de Movimientos Financieros','informes-movimientos-filtro-modal.php')" class="informe-link">
                                                                <i class="fas fa-money-bill-wave"></i> Informe de movimientos financieros
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Registro detallado de todos los movimientos financieros del sistema</div>
                                                        </li>
                                                        <?php }
                                                        if(Modulos::validarSubRol(['DT0331'])){
                                                        ?>
                                                        <li class="informe-item">
                                                            <a href="javascript:void(0);" onclick="abrirModal('Paz y salvo','paz-salvo-modal.php')" class="informe-link">
                                                                <i class="fas fa-check-circle"></i> Paz y salvo
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Documento que certifica que el estudiante no tiene obligaciones financieras pendientes</div>
                                                        </li>
                                                        <?php }?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>

                                <!-- INFORMES DISCIPLINARIOS -->
                                <?php if(Modulos::validarSubRol(['DT0116','DT0242'])){?>
                                <div class="card informe-card">
                                    <div class="card-header informe-header informe-header-disciplinario" id="headingDisciplinarios">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed informe-btn-toggle" type="button" data-toggle="collapse" data-target="#collapseDisciplinarios" aria-expanded="false" aria-controls="collapseDisciplinarios">
                                                <i class="fas fa-exclamation-circle informe-icon"></i>
                                                <span class="informe-title">Informes Disciplinarios</span>
                                                <i class="fas fa-chevron-down float-right mt-1"></i>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseDisciplinarios" class="collapse" aria-labelledby="headingDisciplinarios" data-parent="#accordionInformes">
                                        <div class="card-body informe-card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <ul class="informe-list">
                                                        <?php if(Modulos::validarSubRol(['DT0116'])){?>
                                                        <li class="informe-item">
                                                            <a href="javascript:void(0);" onclick="abrirModal('Sacar reportes','reportes-sacar-filtro-modal.php')" class="informe-link">
                                                                <i class="fas fa-clipboard"></i> Sacar reportes
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Generación de reportes disciplinarios y observaciones de estudiantes</div>
                                                        </li>
                                                        <?php }
                                                        if(Modulos::validarSubRol(['DT0242'])){
                                                        ?>
                                                        <li class="informe-item">
                                                            <a href="../compartido/reporte-ver-observador.php" target="_blank" class="informe-link">
                                                                <i class="fas fa-eye"></i> Reporte vista observador
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Visualización de observaciones y reportes disciplinarios registrados</div>
                                                        </li>
                                                        <?php }?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>

                                <!-- EXPORTAR A EXCEL -->
                                <?php if(Modulos::validarSubRol(['DT0243','DT0244', 'DT0340'])){?>
                                <div class="card informe-card">
                                    <div class="card-header informe-header informe-header-excel" id="headingExcel">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed informe-btn-toggle" type="button" data-toggle="collapse" data-target="#collapseExcel" aria-expanded="false" aria-controls="collapseExcel">
                                                <i class="fas fa-file-excel informe-icon"></i>
                                                <span class="informe-title">Exportar a Excel</span>
                                                <i class="fas fa-chevron-down float-right mt-1"></i>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseExcel" class="collapse" aria-labelledby="headingExcel" data-parent="#accordionInformes">
                                        <div class="card-body informe-card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <ul class="informe-list">
                                                        <?php if(Modulos::validarSubRol(['DT0243'])){?>
                                                        <li class="informe-item">
                                                            <a href="../compartido/excel-inscripciones.php" target="_blank" class="informe-link">
                                                                <i class="fas fa-download"></i> Exportar inscripciones
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Exportación a Excel de datos de inscripciones y aspirantes</div>
                                                        </li>
                                                        <?php } 
                                                        if(Modulos::validarSubRol(['DT0244'])){
                                                        ?>
                                                        <li class="informe-item">
                                                            <a href="../compartido/excel-estudiantes.php" target="_blank" class="informe-link">
                                                                <i class="fas fa-download"></i> Exportar matrículas
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Exportación a Excel de datos de estudiantes matriculados</div>
                                                        </li>
                                                        <?php }
                                                        if(Modulos::validarSubRol(['DT0340'])){
                                                        ?>
                                                        <li class="informe-item">
                                                            <a href="javascript:void(0);" onclick="abrirModal('Exportar informe periodico','informe-periodicos-filtros-modal.php')" class="informe-link">
                                                                <i class="fas fa-download"></i> Exportar informe periodico
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Exportación a Excel de informes periódicos con calificaciones</div>
                                                        </li>
                                                        <?php }?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>

                                <!-- INFORMES ADMINISTRATIVOS -->
                                <?php if(Modulos::validarSubRol(['DT0245','DT0246'])){?>
                                <div class="card informe-card">
                                    <div class="card-header informe-header informe-header-administrativo" id="headingAdministrativos">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed informe-btn-toggle" type="button" data-toggle="collapse" data-target="#collapseAdministrativos" aria-expanded="false" aria-controls="collapseAdministrativos">
                                                <i class="fas fa-cog informe-icon"></i>
                                                <span class="informe-title">Informes Administrativos</span>
                                                <i class="fas fa-chevron-down float-right mt-1"></i>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseAdministrativos" class="collapse" aria-labelledby="headingAdministrativos" data-parent="#accordionInformes">
                                        <div class="card-body informe-card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <ul class="informe-list">
                                                        <?php if(Modulos::validarSubRol(['DT0245'])){?>
                                                        <li class="informe-item">
                                                            <a href="../compartido/informe-usuarios-repetidos.php" target="_blank" class="informe-link">
                                                                <i class="fas fa-users-cog"></i> Informe usuarios repetidos
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Identificación de usuarios duplicados en el sistema</div>
                                                        </li>
                                                        <?php } 
                                                        if(Modulos::validarSubRol(['DT0246'])){
                                                        ?>
                                                        <li class="informe-item">
                                                            <a href="../compartido/informe-estudiantes-sin-usuarios.php" target="_blank" class="informe-link">
                                                                <i class="fas fa-user-slash"></i> Informe estudiantes sin usuario
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Listado de estudiantes que no tienen usuario asociado en el sistema</div>
                                                        </li>
                                                        <?php }?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>

                                <!-- INFORMES DE INSCRIPCIÓN -->
                                <?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_INSCRIPCIONES) && Modulos::validarSubRol(['DT0347'])){?>
                                <div class="card informe-card">
                                    <div class="card-header informe-header informe-header-inscripcion" id="headingInscripciones">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed informe-btn-toggle" type="button" data-toggle="collapse" data-target="#collapseInscripciones" aria-expanded="false" aria-controls="collapseInscripciones">
                                                <i class="fas fa-user-plus informe-icon"></i>
                                                <span class="informe-title">Informes de Inscripción</span>
                                                <i class="fas fa-chevron-down float-right mt-1"></i>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseInscripciones" class="collapse" aria-labelledby="headingInscripciones" data-parent="#accordionInformes">
                                        <div class="card-body informe-card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <ul class="informe-list">
                                                        <?php if(Modulos::validarSubRol(['DT0347'])){
                                                            $modalInsDocumentos = new ComponenteModal('insDocumentos','Documentos de Inscripción','../directivo/informes-modal-documentos.php');
                                                        ?>
                                                        <li class="informe-item">
                                                            <a href="#" onclick="event.preventDefault(); event.stopImmediatePropagation(); if(event.isTrusted && event.type==='click'){<?=$modalInsDocumentos->getMetodoAbrirModal()?>} return false;" class="informe-link">
                                                                <i class="fas fa-file-alt"></i> Documentos de Inscripción
                                                            </a>
                                                            <div class="informe-descripcion" style="font-style: italic; color: #555; font-size: 0.9em; margin-top: 4px; padding-left: 25px;">Reporte de documentos requeridos y estado de inscripciones</div>
                                                        </li>
                                                        <?php }?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>

                            </div>
                            <!-- FIN ACORDEÓN -->
                        </div>
                    </div>
                </div>
            </div>
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
    <!-- select2 -->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
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
	
	<!-- Scripts para modales de informes -->
	<script>
		// CRÍTICO: Observer para eliminar backdrops duplicados automáticamente
		$(document).ready(function() {
			// Vigilar cambios en el DOM para eliminar backdrops duplicados
			var observer = new MutationObserver(function(mutations) {
				var backdrops = $('.modal-backdrop');
				if (backdrops.length > 1) {
					// Si hay más de 1 backdrop, eliminar todos menos el último
					backdrops.slice(0, -1).remove();
				}
			});
			
			observer.observe(document.body, {
				childList: true,
				subtree: false
			});
			
			// Limpiar backdrops cada vez que se cierra un modal
			$(document).on('hidden.bs.modal', '.modal', function() {
				setTimeout(function() {
					$('.modal-backdrop').remove();
					if (!$('.modal.show').length) {
						$('body').removeClass('modal-open');
						$('body').css('overflow', '');
						$('body').css('padding-right', '');
					}
				}, 200);
			});
		});
	</script>
	<script>
		// Función global reutilizable para cargar estudiantes por grado en cualquier modal
		window.cargarEstudiantesPorModal = function(gradoId, selectId, modalId, loadingId) {
			var selectEstudiantes = $('#' + selectId);
			var loading = $('#' + loadingId);
			
			selectEstudiantes.empty().prop('disabled', true);
			selectEstudiantes.append('<option value="">Cargando...</option>');
			
			if (!gradoId) {
				selectEstudiantes.empty().append('<option value="">Primero seleccione un grado</option>');
				return;
			}
			
			if (loading.length) loading.show();
			
			$.ajax({
				url: 'ajax-cargar-estudiantes-por-grado.php',
				type: 'POST',
				data: { grado: gradoId },
				dataType: 'json',
				success: function(response) {
					if (loading.length) loading.hide();
					selectEstudiantes.empty();
					
					if (response.success && response.data.length > 0) {
						selectEstudiantes.append('<option value="">Seleccione un estudiante</option>');
						
						response.data.forEach(function(estudiante) {
							selectEstudiantes.append(
								$('<option></option>')
									.attr('value', estudiante.id)
									.text(estudiante.texto_completo)
							);
						});
						
						selectEstudiantes.prop('disabled', false);
						
					if (selectEstudiantes.data('select2')) {
						try { selectEstudiantes.select2('destroy'); } catch(e) {}
					}
					selectEstudiantes.select2({
						dropdownParent: $('#' + modalId + ' .modal-content'),
						width: '100%',
						minimumResultsForSearch: 0,
						placeholder: 'Seleccione un estudiante',
						language: {
							noResults: function() { return 'No se encontraron resultados'; },
							searching: function() { return 'Buscando...'; }
						}
					});
					} else {
						selectEstudiantes.append('<option value="">No hay estudiantes en este grado</option>');
					}
				},
				error: function() {
					if (loading.length) loading.hide();
					selectEstudiantes.empty();
					selectEstudiantes.append('<option value="">Error al cargar estudiantes</option>');
					alert('Error al cargar los estudiantes. Por favor intente nuevamente.');
				}
			});
		};
		
		// Funciones específicas para cada modal (wrappers de la función principal)
		window.cargarEstudiantesPorGrado = function(gradoId, selectId) {
			cargarEstudiantesPorModal(gradoId, selectId, 'ComponeteModal-boletines', 'loadingEstudiantes');
		};
		
		window.cargarEstudiantesCertificado = function(gradoId, selectId) {
			var loadingId = selectId === 'selectEstudiantes1' ? 'loadingEst1' : 'loadingEst2';
			cargarEstudiantesPorModal(gradoId, selectId, 'ModalCentralizado', loadingId);
		};
		
		window.cargarEstudiantesLibro = function(gradoId, selectId) {
			cargarEstudiantesPorModal(gradoId, selectId, 'ModalCentralizado', 'loadingEstLibro');
		};
		
		window.cargarEstudiantesParcial = function(gradoId, selectId) {
			cargarEstudiantesPorModal(gradoId, selectId, 'ModalCentralizado', 'loadingEstParcial');
		};
		
		window.cargarEstudiantesDocumentos = function(gradoId, selectId) {
			cargarEstudiantesPorModal(gradoId, selectId, 'ComponeteModal-insDocumentos', 'loadingEstDoc');
		};
		
		// Auto-disparar carga si el año ya está seleccionado (para modales de boletines)
		window.autoCargarSiYearSeleccionado = function() {
			setTimeout(function() {
				var yearCurso = $('#yearCurso').val();
				var yearEst = $('#yearEst').val();
				
				// Si el año ya está seleccionado al abrir el modal, cargar automáticamente
				if (yearCurso) {
					console.log('Auto-cargando cursos para año:', yearCurso);
					window.cargarCursosPorYear(yearCurso, 'cursoCurso', 'grupoCurso');
				}
				if (yearEst) {
					console.log('Auto-habilitando filtro de grado para año:', yearEst);
					window.habilitarFiltroGrado('yearEst', 'filtroGradoEst');
				}
			}, 1000);
		};
		
		// Auto-cargar para sábanas
		window.autoCargarSabana = function() {
			setTimeout(function() {
				var year = $('#yearSabana').val();
				if (year) {
					console.log('Auto-cargando datos sábanas para año:', year);
					window.cargarCursosPorYearSabana(year);
				}
			}, 1000);
		};
		
		// Auto-cargar para libro final
		window.autoCargarLibro = function() {
			setTimeout(function() {
				var yearCurso = $('#yearLibroCurso').val();
				var yearEst = $('#yearLibroEst').val();
				
				if (yearCurso) {
					console.log('Auto-cargando cursos libro para año:', yearCurso);
					window.cargarCursosPorYearLibro(yearCurso, 'cursoLibroCurso', 'grupoLibroCurso');
				}
				if (yearEst) {
					console.log('Auto-habilitando filtro grado libro para año:', yearEst);
					window.habilitarFiltroGrado('yearLibroEst', 'filtroGradoLibro');
				}
			}, 1000);
		};
		
		// Función para cargar cursos y grupos por año
		window.cargarCursosPorYear = function(year, cursoSelectId, grupoSelectId) {
			var selectCurso = $('#' + cursoSelectId);
			var selectGrupo = $('#' + grupoSelectId);
			var loadingCursos = $('#loadingCursos');
			var loadingGrupos = $('#loadingGrupos');
			
			// Limpiar selects
			selectCurso.empty().prop('disabled', true);
			selectGrupo.empty().prop('disabled', true);
			selectCurso.append('<option value="">Cargando...</option>');
			selectGrupo.append('<option value="">Cargando...</option>');
			
			if (!year) {
				selectCurso.empty().append('<option value="">Primero seleccione un año</option>');
				selectGrupo.empty().append('<option value="">Primero seleccione un año</option>');
				return;
			}
			
			if (loadingCursos.length) loadingCursos.show();
			if (loadingGrupos.length) loadingGrupos.show();
			
			// Cargar cursos
			$.ajax({
				url: 'ajax-cargar-cursos-por-year.php',
				type: 'POST',
				data: { year: year },
				dataType: 'json',
				success: function(response) {
					if (loadingCursos.length) loadingCursos.hide();
					selectCurso.empty();
					
					if (response.success && response.data.length > 0) {
						selectCurso.append('<option value="">Seleccione un curso</option>');
						response.data.forEach(function(curso) {
							selectCurso.append(
								$('<option></option>')
									.attr('value', curso.id)
									.text(curso.texto_completo)
							);
						});
						selectCurso.prop('disabled', false);
						
						// Reinicializar select2
						if (selectCurso.data('select2')) {
							selectCurso.select2('destroy');
						}
						selectCurso.select2({
							dropdownParent: $('#ComponeteModal-boletines .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0,
							placeholder: 'Seleccione un curso'
						});
					} else {
						selectCurso.append('<option value="">No hay cursos disponibles</option>');
					}
				}
			});
			
			// Cargar grupos
			$.ajax({
				url: 'ajax-cargar-grupos-por-year.php',
				type: 'POST',
				data: { year: year },
				dataType: 'json',
				success: function(response) {
					if (loadingGrupos.length) loadingGrupos.hide();
					selectGrupo.empty();
					
					if (response.success && response.data.length > 0) {
						selectGrupo.append('<option value="">Seleccione un grupo (opcional)</option>');
						response.data.forEach(function(grupo) {
							selectGrupo.append(
								$('<option></option>')
									.attr('value', grupo.id)
									.text(grupo.texto_completo)
							);
						});
						selectGrupo.prop('disabled', false);
						
						// Reinicializar select2
						if (selectGrupo.data('select2')) {
							selectGrupo.select2('destroy');
						}
						selectGrupo.select2({
							dropdownParent: $('#ComponeteModal-boletines .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0,
							placeholder: 'Seleccione un grupo'
						});
					} else {
						selectGrupo.append('<option value="">No hay grupos disponibles</option>');
					}
				}
			});
		};
		
		// Función para habilitar el filtro de grado después de seleccionar año
		window.habilitarFiltroGrado = function(yearSelectId, gradoSelectId) {
			var year = $('#' + yearSelectId).val();
			var selectGrado = $('#' + gradoSelectId);
			
			if (year) {
				selectGrado.prop('disabled', false);
			} else {
				selectGrado.prop('disabled', true);
				selectGrado.val('').trigger('change');
			}
		};
		
		// Función para cargar estudiantes por año y grado (cascada completa)
		window.cargarEstudiantesPorYearGrado = function() {
			var year = $('#yearEst').val();
			var gradoId = $('#filtroGradoEst').val();
			var selectEstudiantes = $('#selectEstudiantes');
			var loading = $('#loadingEstudiantes');
			
			selectEstudiantes.empty().prop('disabled', true);
			selectEstudiantes.append('<option value="">Cargando...</option>');
			
			if (!year || !gradoId) {
				selectEstudiantes.empty().append('<option value="">Seleccione año y grado</option>');
				return;
			}
			
			if (loading.length) loading.show();
			
			$.ajax({
				url: 'ajax-cargar-estudiantes-por-year-grado.php',
				type: 'POST',
				data: { year: year, grado: gradoId },
				dataType: 'json',
				success: function(response) {
					if (loading.length) loading.hide();
					selectEstudiantes.empty();
					
					if (response.success && response.data.length > 0) {
						selectEstudiantes.append('<option value="">Seleccione un estudiante</option>');
						
						response.data.forEach(function(estudiante) {
							selectEstudiantes.append(
								$('<option></option>')
									.attr('value', estudiante.id)
									.text(estudiante.texto_completo)
							);
						});
						
						selectEstudiantes.prop('disabled', false);
						
						if (selectEstudiantes.data('select2')) {
							try { selectEstudiantes.select2('destroy'); } catch(e) {}
						}
						selectEstudiantes.select2({
							dropdownParent: $('#ComponeteModal-boletines .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0,
							placeholder: 'Seleccione un estudiante',
							language: {
								noResults: function() { return 'No se encontraron resultados'; },
								searching: function() { return 'Buscando...'; }
							}
						});
					} else {
						selectEstudiantes.append('<option value="">No hay estudiantes en este grado</option>');
					}
				},
				error: function() {
					if (loading.length) loading.hide();
					selectEstudiantes.empty();
					selectEstudiantes.append('<option value="">Error al cargar estudiantes</option>');
				}
			});
		};
		
		// Función para cargar cursos por año para sábanas
		window.cargarCursosPorYearSabana = function(year) {
			var selectCurso = $('#cursoSabana');
			var selectGrupo = $('#grupoSabana');
			var loadingCursos = $('#loadingCursosSabana');
			var loadingGrupos = $('#loadingGruposSabana');
			
			selectCurso.empty().prop('disabled', true);
			selectGrupo.empty().prop('disabled', true);
			selectCurso.append('<option value="">Cargando...</option>');
			selectGrupo.append('<option value="">Cargando...</option>');
			
			if (!year) {
				selectCurso.empty().append('<option value="">Primero seleccione un año</option>');
				selectGrupo.empty().append('<option value="">Primero seleccione un año</option>');
				return;
			}
			
			if (loadingCursos.length) loadingCursos.show();
			if (loadingGrupos.length) loadingGrupos.show();
			
			// Cargar cursos
			$.ajax({
				url: 'ajax-cargar-cursos-por-year.php',
				type: 'POST',
				data: { year: year },
				dataType: 'json',
				success: function(response) {
					if (loadingCursos.length) loadingCursos.hide();
					selectCurso.empty();
					
					if (response.success && response.data.length > 0) {
						selectCurso.append('<option value="">Seleccione un curso</option>');
						response.data.forEach(function(curso) {
							selectCurso.append($('<option></option>').attr('value', curso.id).text(curso.texto_completo));
						});
						selectCurso.prop('disabled', false);
						
						if (selectCurso.data('select2')) { selectCurso.select2('destroy'); }
						selectCurso.select2({
							dropdownParent: $('#ModalCentralizado .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0,
							placeholder: 'Seleccione un curso'
						});
					}
				}
			});
			
			// Cargar grupos
			$.ajax({
				url: 'ajax-cargar-grupos-por-year.php',
				type: 'POST',
				data: { year: year },
				dataType: 'json',
				success: function(response) {
					if (loadingGrupos.length) loadingGrupos.hide();
					selectGrupo.empty();
					
					if (response.success && response.data.length > 0) {
						selectGrupo.append('<option value="">Seleccione un grupo (opcional)</option>');
						response.data.forEach(function(grupo) {
							selectGrupo.append($('<option></option>').attr('value', grupo.id).text(grupo.texto_completo));
						});
						selectGrupo.prop('disabled', false);
						
						if (selectGrupo.data('select2')) { selectGrupo.select2('destroy'); }
						selectGrupo.select2({
							dropdownParent: $('#ModalCentralizado .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0,
							placeholder: 'Seleccione un grupo'
						});
					}
				}
			});
		};
		
		// Función para cargar cursos por año para libro final
		window.cargarCursosPorYearLibro = function(year, cursoSelectId, grupoSelectId) {
			var selectCurso = $('#' + cursoSelectId);
			var selectGrupo = $('#' + grupoSelectId);
			var loadingCursos = $('#loadingCursosLibro');
			var loadingGrupos = $('#loadingGruposLibro');
			
			// Verificar si el formato 4 está seleccionado
			var formatoSeleccionado = $('#formatoLibroCurso').val();
			var esFormato4 = (formatoSeleccionado === '4');
			
			selectCurso.empty().prop('disabled', true);
			selectGrupo.empty().prop('disabled', true);
			
			if (!year) {
				selectCurso.append('<option value="">Primero seleccione un año</option>');
				selectGrupo.append('<option value="">Primero seleccione un año</option>');
				return;
			}
			
			if (loadingCursos.length) loadingCursos.show();
			if (loadingGrupos.length) loadingGrupos.show();
			
			// Cargar cursos
			$.ajax({
				url: 'ajax-cargar-cursos-por-year.php',
				type: 'POST',
				data: { year: year },
				dataType: 'json',
				success: function(response) {
					if (loadingCursos.length) loadingCursos.hide();
					selectCurso.empty();
					
					if (response.success && response.data.length > 0) {
						// Solo agregar opción por defecto si NO es formato 4
						if (!esFormato4) {
							selectCurso.append('<option value="">Seleccione un curso</option>');
						}
						response.data.forEach(function(curso) {
							selectCurso.append($('<option></option>').attr('value', curso.id).text(curso.texto_completo));
						});
						selectCurso.prop('disabled', false);
						
						if (selectCurso.data('select2')) { selectCurso.select2('destroy'); }
						
						// Configurar select2 según si es formato 4 o no
						var select2Config = {
							dropdownParent: $('#ModalCentralizado .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0
						};
						
						if (esFormato4) {
							select2Config.placeholder = 'Seleccione uno o más cursos';
							select2Config.allowClear = true;
						} else {
							select2Config.placeholder = 'Seleccione un curso';
						}
						
						selectCurso.select2(select2Config);
					}
				}
			});
			
			// Cargar grupos
			$.ajax({
				url: 'ajax-cargar-grupos-por-year.php',
				type: 'POST',
				data: { year: year },
				dataType: 'json',
				success: function(response) {
					if (loadingGrupos.length) loadingGrupos.hide();
					selectGrupo.empty();
					
					if (response.success && response.data.length > 0) {
						// Solo agregar opción por defecto si NO es formato 4
						if (!esFormato4) {
							selectGrupo.append('<option value="">Seleccione un grupo (opcional)</option>');
						}
						response.data.forEach(function(grupo) {
							selectGrupo.append($('<option></option>').attr('value', grupo.id).text(grupo.texto_completo));
						});
						selectGrupo.prop('disabled', false);
						
						if (selectGrupo.data('select2')) { selectGrupo.select2('destroy'); }
						
						// Configurar select2 según si es formato 4 o no
						var select2Config = {
							dropdownParent: $('#ModalCentralizado .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0
						};
						
						if (esFormato4) {
							select2Config.placeholder = 'Seleccione uno o más grupos (opcional)';
							select2Config.allowClear = true;
						} else {
							select2Config.placeholder = 'Seleccione un grupo';
						}
						
						selectGrupo.select2(select2Config);
					}
				}
			});
		};
		
		// Función para cargar estudiantes para libro final
		window.cargarEstudiantesLibroYear = function() {
			var year = $('#yearLibroEst').val();
			var gradoId = $('#filtroGradoLibro').val();
			var selectEstudiantes = $('#selectEstudiantesLibro');
			var loading = $('#loadingEstLibro');
			
			selectEstudiantes.empty().prop('disabled', true);
			
			if (!year || !gradoId) {
				selectEstudiantes.append('<option value="">Seleccione año y grado</option>');
				return;
			}
			
			selectEstudiantes.append('<option value="">Cargando...</option>');
			if (loading.length) loading.show();
			
			$.ajax({
				url: 'ajax-cargar-estudiantes-por-year-grado.php',
				type: 'POST',
				data: { year: year, grado: gradoId },
				dataType: 'json',
				success: function(response) {
					if (loading.length) loading.hide();
					selectEstudiantes.empty();
					
					if (response.success && response.data.length > 0) {
						selectEstudiantes.append('<option value="">Seleccione un estudiante</option>');
						response.data.forEach(function(estudiante) {
							selectEstudiantes.append($('<option></option>').attr('value', estudiante.id).text(estudiante.texto_completo));
						});
						selectEstudiantes.prop('disabled', false);
						
						if (selectEstudiantes.data('select2')) {
							try { selectEstudiantes.select2('destroy'); } catch(e) {}
						}
						selectEstudiantes.select2({
							dropdownParent: $('#ModalCentralizado .modal-content'),
							width: '100%',
							minimumResultsForSearch: 0,
							placeholder: 'Seleccione un estudiante',
							language: {
								noResults: function() { return 'No se encontraron resultados'; },
								searching: function() { return 'Buscando...'; }
							}
						});
					} else {
						selectEstudiantes.append('<option value="">No hay estudiantes en este grado</option>');
					}
				}
			});
		};
	</script>
	
    <!-- end js include path -->
</body>

</html>