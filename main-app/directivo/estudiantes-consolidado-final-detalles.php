<?php
include("session.php");
$idPaginaInterna = 'DT0081';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$consultaCurso = Grados::obtenerDatosGrados($_POST["curso"]);
$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);

$consultaGrupo = Grupos::obtenerDatosGrupos($_POST["grupo"]);
$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
  ?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript">
    function def(enviada){
    var nota = enviada.value;
    var codEst = enviada.id;
    var carga = enviada.name;
    var per = enviada.alt;
	if (alertValidarNota(nota)) {
		return false;
	}	
        $('#resp').empty().hide().html("Esperando...").show(1);
            datos = "nota="+(nota)+
                    "&carga="+(carga)+
                    "&codEst="+(codEst)+
                    "&per="+(per);
                $.ajax({
                    type: "POST",
                    url: "../compartido/ajax-periodos-registrar.php",
                    data: datos,
                    success: function(data){
                    $('#resp').empty().hide().html(data).show(1);
                    }
                });

        }
	</script>
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
                                <div class="page-title">Consolidado Final</div>
                          
								<div>
									<b>Curso:</b> <?php if(isset($curso['gra_nombre'])){echo $curso['gra_nombre'];}?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?php if(isset($grupo['gru_nombre'])){echo $grupo['gru_nombre'];}?>
								</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-8 col-lg-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Consolidado Final</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											<div class="alert alert-block alert-info">
												<h4 class="alert-heading">Información importante!</h4>
												<p>Digite la nota para cada estudiante en el periodo y materia correspondiente y pulse Enter o simplemente cambie de casilla para que los cambios se guarden automaticamente.</p>
												<p style="font-weight:bold;">Por favor despu&eacute;s de digitar una nota, espere un momento a que el sistema le indique que la nota se guard&oacute; y prosiga con la siguiente.</p>
											</div>
											<div class="alert alert-block alert-warning">
												<h4 class="alert-heading">Información importante!</h4>
                              					<p>La definitiva de cada materia se obtiene del promedio de los periodos. Para que esta definitiva pueda ser correcta debe estar la nota de todos los periodos registada.</p>
											</div>
											<span id="resp"></span>
											<input type="hidden" name="periodo" value="<?php if(isset($_POST["periodo"])){echo $_POST["periodo"];}?>" id="periodo">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
														<a href="../compartido/informe-consolidad-final.php?curso=<?=$_POST["curso"];?>&grupo=<?=$_POST["grupo"];?>" id="addRow" class="btn deepPink-bgcolor" target="_blank">
															Sacar Informe
														</a>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display"  cellpadding="0" cellspacing="0" border="0" >
											<thead>
												<tr>
													<th rowspan="2" style="font-size:9px;">Doc</th>
													<th rowspan="2" style="font-size:9px;">Estudiante</th>
													<?php
													//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
													$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_POST["curso"], $_POST["grupo"]);
													$numCargasPorCurso = mysqli_num_rows($cargas); 
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
													?>
														<th style="font-size:9px; text-align:center; border:groove;" colspan="<?=$config[19]+1;?>" width="5%"><?=$carga['mat_nombre'];?></th>
													<?php
													}
													?>
													<th rowspan="2" style="text-align:center;">PROM</th>
													</tr>
													
													<tr>
														<?php
														$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_POST["curso"], $_POST["grupo"]);
														while($carga = mysqli_fetch_array($cargas)){
															$p = 1;
															//PERIODOS DE CADA MATERIA
															while($p<=$config[19]){
																echo '<th style="text-align:center;">'.$p.'</th>';
																$p++;
															}
															//DEFINITIVA DE CADA MATERIA
															echo '<th style="text-align:center; background:#FFC">DEF</th>';
														}
														?>
													</tr>
												
												</thead>
												<!-- END -->
												<!-- BEGIN -->
												<tbody>
												<?php
												$filtroAdicional= "AND mat_grado='".$_REQUEST["curso"]."' AND mat_grupo='".$_REQUEST["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
												$cursoActual=GradoServicios::consultarCurso($_REQUEST["curso"]);
												$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual,$_REQUEST["grupo"]);
												//PRIMER PUESTO
												$primerPuestoNota = 0;
												$primerPuestoNombre = '';
												$primerPuestoID = 0;
												//SEGUNDO PUESTO
												$segundoPuestoNota = 0;
												$segundoPuestoNombre = '';
												$segundoPuestoID = 0;
												while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
												$defPorEstudiante = 0;
												?>
												<tr id="data1" class="odd gradeX">
													<td style="font-size:9px;"><?=$resultado['mat_documento'];?></td>
													<td style="font-size:9px;"><?=Estudiantes::NombreCompletoDelEstudiante($resultado)?></td>
													<?php
													$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_POST["curso"], $_POST["grupo"]);
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
														$p = 1;
														$defPorMateria = 0;
														//PERIODOS DE CADA MATERIA
														while($p<=$config[19]){
															$boletin = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $resultado['mat_id'], $carga['car_id']);
															if(isset($boletin['bol_nota']) and $boletin['bol_nota']<$config[5] and $boletin['bol_nota']!="")$color = $config[6]; elseif(isset($boletin['bol_nota']) and $boletin['bol_nota']>=$config[5]) $color = $config[7];
															if(isset($boletin['bol_nota'])){
																$defPorMateria += $boletin['bol_nota'];
															}
															if(isset($boletin['bol_tipo']) and $boletin['bol_tipo']==1) $tipo = '<span style="color:blue; font-size:9px;">Normal</span>'; 
															elseif(isset($boletin['bol_tipo']) and $boletin['bol_tipo']==2) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n Per.</span>';
															elseif(isset($boletin['bol_tipo']) and $boletin['bol_tipo']==3) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n Ind.</span>';
															elseif(isset($boletin['bol_tipo']) and $boletin['bol_tipo']==4) $tipo = '<span style="color:red; font-size:9px;">Directivo</span>';
															
															else $tipo='';
															//DEFINITIVA DE CADA PERIODO
															
															$disabled = "";
															if((isset($boletin['bol_nota']) and ($boletin['bol_nota']!="" or $carga['car_periodo']<=$p)) and $config['conf_editar_definitivas_consolidado']!=true){
																$disabled = "disabled";
															}
														?>	
															<td style="text-align:center;">
																<input style="text-align:center; width:40px; color:<?=$color;?>" value="<?php if(isset($boletin['bol_nota'])){ echo $boletin['bol_nota'];}?>" name="<?=$carga['car_id'];?>" id="<?=$resultado['mat_id'];?>" onChange="def(this)" alt="<?=$p;?>" title="Materia: <?=$carga['mat_nombre'];?> - Periodo: <?=$p;?>" <?=$disabled;?> <?=$disabledPermiso;?>><br><?=$tipo;?>
															</td>
														<?php
															$p++;
														}
														$defPorMateria = round($defPorMateria/$config[19],2);
															//DEFINITIVA DE CADA MATERIA
															if($defPorMateria<$config[5] and $defPorMateria!="")$color = $config[6]; elseif($defPorMateria>=$config[5]) $color = $config[7];
															//CONSULTAR NIVELACIONES
															$consultaNiv = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $resultado['mat_id'], $carga['car_id']);
															$cNiv = mysqli_fetch_array($consultaNiv, MYSQLI_BOTH);
															if(isset($cNiv['niv_definitiva']) and $cNiv['niv_definitiva']>$defPorMateria){$defPorMateria=$cNiv['niv_definitiva']; $msj = 'Nivelación';}else{$defPorMateria=$defPorMateria; $msj = '';}
														?>
															<td style="text-align:center; background:#FFC;"><input style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>" value="<?php if(isset($defPorMateria)){ echo $defPorMateria;}?>" disabled><br><span style="font-size:10px; color:rgb(255,0,0); font-weight:bold;"><?php if(isset($msj)){ echo $msj;}?><br><?php if(isset($cNiv['niv_acta']) and isset($cNiv['niv_fecha_nivelacion'])){ echo "Acta ".$cNiv['niv_acta']." de ".$cNiv['niv_fecha_nivelacion'];}?></span></td>
													<?php
														//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
														$defPorEstudiante += $defPorMateria;   
													}
														$defPorEstudiante = round($defPorEstudiante/$numCargasPorCurso,2);
														if($defPorEstudiante<$config[5] and $defPorEstudiante!="")$color = $config[6]; elseif($defPorEstudiante>=$config[5]) $color = $config[7];
													?>
														<td style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>"><?=$defPorEstudiante;?></td>
												</tr>
												<?php 
												}
												?>
												
												</tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
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
</body>

</html>