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
                                <div class="page-title">Informes</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                    
                        <div class="col-md-12">
                         <div id="gifCarga" class="gif-carga"><img alt="Cargando..."></div>
							<h4>INFORMES ACADEMICOS</h4>
                            <div class="row">
								
								<?php if(Modulos::validarSubRol(['DT0100','DT0082','DT0134','DT0135','DT0133','DT0101','DT0143','DT0136','DT0120','DT0147', 'DT0307'])){?>
								<div class="col-md-6">
									<div class="panel">
										<header class="panel-heading panel-heading-blue">MATRICULAS</header>
										<div class="panel-body">
											<?php if(Modulos::validarSubRol(['DT0100'])){
                                                $modalBoletin = new ComponenteModal('boletines','Boletines','../directivo/informes-boletines-modal.php');
                                                ?>
												<p><a href="javascript:void(0);"  onclick="<?=$modalBoletin->getMetodoAbrirModal()?>"  >1. Boletines</a></p>											
											<?php 
												} 
												if(Modulos::validarSubRol(['DT0082'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Certificados','estudiantes-certificados-modal.php')"  >2. Certificados</a></p>
											<?php 
												} 
												if(Modulos::validarSubRol(['DT0134'])){
											?>
												<p><a href="javascript:void(0);" onclick="abrirModal('Consolidado de asignaturas perdidas','consolidado-perdidos-modal.php')"   >3. Consolidado de asignaturas perdidas</a></p>
											<?php 
												} 
												if(Modulos::validarSubRol(['DT0135'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Libro final','informe-libro-cursos-modal.php')"  >4. Libro final</a></p>
											<?php 
												} 
												if(Modulos::validarSubRol(['DT0133'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Listado de estudiantes','informe-estudiantes-modal.php')"  >5. Listado de estudiantes</a></p>
											<?php 
												} 
												if(Modulos::validarSubRol(['DT0101'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Informe parcial','informe-parcial-grupo-modal.php')"  >6. Informe parcial</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0221'])){
											?>
												<p><a href="../compartido/reporte-pasos.php" target="_blank">7. Informe pasos matrícula</a></p>
											<?php
												} 
												if(Modulos::validarSubRol(['DT0143'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Informe de consolidado final','consolidado-final-filtro-modal.php')"  >8. Informe de consolidado final</a></p>
											<?php 
												} 
												if(Modulos::validarSubRol(['DT0136'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Planilla de estudiantes','estudiantes-planilla-modal.php')"  >9. Planilla de estudiantes</a></p>
											<?php
												} 
												if(Modulos::validarSubRol(['DT0120'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Reporte general de estudiantes','reportes-academicos-consultas-modal.php')" >10. Reporte general de estudiantes</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0222'])){
											?>
												<p><a href="../compartido/reporte-informe-parcial.php" target="_blank">11. Reporte informe parcial</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0147'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Reporte de asistencia a entrega de informes','asistencia-entrega-informes-filtros-modal.php')" >12. Reporte de asistencia a entrega de informes</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0223'])){
											?>
												<p><a href="../compartido/informe-matriculas-repetidas.php" target="_blank">13. Informe Matriculas repetidas</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0307'])){
											?>
												<p><a href="javascript:void(0);" onclick="abrirModal('Informe Matriculas retiradas','matriculas-retiradas-modal.php')">14. Informe Matriculas retiradas</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0249','DT0251'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Hoja de Matricula','hoja-matricula-modal.php')">15. Hoja de Matricula</a></p>
											<?php 
												}
											?>
										</div>
                                	</div>
								</div>
								<?php }?>

								<?php if(Modulos::validarSubRol(['DT0234','DT0140','DT0146','DT0141','DT0194','DT0200'])){?>
								<div class="col-md-6">
									<div class="panel">
										<header class="panel-heading panel-heading-blue">CARGAS ACADÉMICAS</header>
										<div class="panel-body">
											<?php if(Modulos::validarSubRol(['DT0234'])){?>
												<p><a href="../compartido/informes-generales-docentes-cargas.php" target="_blank">1. Docentes y cargas académicas</a></p>
											<?php } if(Modulos::validarSubRol(['DT0140'])){?>
												<p><a href="javascript:void(0);" onclick="abrirModal('Informe de sábanas','informe-reporte-sabana-modal.php')">2. Informe de sábanas</a></p>
											<?php
												}
												if(Modulos::validarSubRol(['DT0146'])){
											?>
												<p><a href="../compartido/informe-cargas-duplicadas.php" target="_blank">3. Informe de cargas duplicadas</a></p>
											<?php } if(Modulos::validarSubRol(['DT0141'])){?>
												<p><a href="javascript:void(0);" onclick="abrirModal('Planilla de asistencia','asistencia-planilla-modal.php')" >4. Planilla de asistencia</a></p>
											<?php
                                            } 
												if(Modulos::validarSubRol(['DT0194'])){
											?>
												<p><a href="javascript:void(0);" onclick="abrirModal('Planilla docentes con notas','planilla-docentes-filtros-modal.php')" >5. Planilla docentes con notas</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0200'])){
											?>
												<p><a href="javascript:void(0);" onclick="abrirModal('Notas declaradas y registradas','notas-registradas-informes-filtros-modal.php')" >6. Notas declaradas y registradas</a></p>
											<?php 
												}
											?>
										</div>
                                	</div>
								</div>
								<?php }?>
							</div>

							<h4>OTROS INFORMES</h4>
							<div class="row">
								<?php if(Modulos::validarSubRol(['DT0240'])){?>
                                <div class="col-md-4">
									<div class="panel">
										<header class="panel-heading panel-heading-green">INFORMES FINANCIEROS</header>
										<div class="panel-body">
											<p><a href="../compartido/reporte-movimientos.php" target="_blank">1. Informe de movimientos financieros</a></p>
											<?php
												if(Modulos::validarSubRol(['DT0331'])){
											?>
												<p><a href="javascript:void(0);"  onclick="abrirModal('Paz y salvo','paz-salvo-modal.php')">2. Paz y salvo</a></p>
											<?php 
												}
											?>
										</div>
                                	</div>
								</div>
								<?php }?>

								<?php if(Modulos::validarSubRol(['DT0116','DT0242'])){?>
                                <div class="col-md-4">
									<div class="panel">
										<header class="panel-heading panel-heading-red">INFORMES DISCPLINARIOS</header>
										<div class="panel-body">
											<?php if(Modulos::validarSubRol(['DT0116'])){?>
											<p><a href="javascript:void(0);" onclick="abrirModal('Sacar reportes','reportes-sacar-filtro-modal.php')" >1. Sacar reportes</a></p>
											<?php 
												}
												if(Modulos::validarSubRol(['DT0242'])){
											?>
											<p><a href="../compartido/reporte-ver-observador.php" target="_blank">2. Reporte vista observador</a></p>
											<?php }?>
										</div>
                                	</div>
								</div>
								<?php }?>
								<?php if(Modulos::validarSubRol(['DT0243','DT0244'])){?>
								<div class="col-md-4">
									<div class="panel">
										<header class="panel-heading panel-heading-yellow">EXPORTAR A EXCEL</header>
										<div class="panel-body">
											<?php if(Modulos::validarSubRol(['DT0243'])){?>
                                            <p><a href="../compartido/excel-inscripciones.php" target="_blank">1. Exportar inscripciones</a></p>
											<?php } if(Modulos::validarSubRol(['DT0244'])){?>
                                            <p><a href="../compartido/excel-estudiantes.php" target="_blank">2. Exportar matrículas</a></p>
											<?php }if(Modulos::validarSubRol(['DT0340'])){
											?>
												<p><a href="javascript:void(0);" onclick="abrirModal('Exportar informe periodico','informe-periodicos-filtros-modal.php')" >3. Exportar informe periodico</a></p>
											<?php 
												
												}
											?>
										</div>
                                	</div>
								</div>
								<?php }?>
								
								<?php if(Modulos::validarSubRol(['DT0245','DT0246'])){?>
								<div class="col-md-4">
									<div class="panel">
										<header class="panel-heading panel-heading-green">INFORMES ADMINISTRATIVOS</header>
										<div class="panel-body">
											<?php if(Modulos::validarSubRol(['DT0245'])){?>
                                            <p><a href="../compartido/informe-usuarios-repetidos.php" target="_blank">1. Informe usuarios repetidos</a></p>
											<?php } if(Modulos::validarSubRol(['DT0246'])){?>
                                            <p><a href="../compartido/informe-estudiantes-sin-usuarios.php" target="_blank">2. Informe estudiantes sin usuario</a></p>
											<?php }?>
										</div>
                                	</div>
								</div>
								<?php }?>
                            </div>
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
</body>

</html>