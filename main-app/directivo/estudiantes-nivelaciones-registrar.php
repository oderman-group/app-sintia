<?php
include("session.php");
$idPaginaInterna = 'DT0076';
include("../compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript">
	function niv(enviada){
		var nota = enviada.value;
		var codEst = enviada.id;
		var carga = enviada.name;
		var op = enviada.alt;
		if(op==1){
			if (alertValidarNota(nota)) {
				return false;
			}	
		}
		$('#resp').empty().hide().html("Esperando...").show(1);
		datos = "nota="+(nota)+
					"&carga="+(carga)+
					"&codEst="+(codEst)+
					"&op="+(op);
				$.ajax({
					type: "POST",
					url: "../compartido/ajax-nivelaciones-registrar.php",
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
		<?php
		$curso = Grados::obtenerGrado($_REQUEST["curso"]);
		$consultaGrupo = Grupos::obtenerDatosGrupos($_REQUEST["grupo"]);
		$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);
		?>
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
                                <div class="page-title"><b>Curso:</b> <?=$curso['gra_nombre'];?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?=$grupo['gru_nombre'];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12 col-lg-12">
                          
									<div class="alert alert-block alert-info">
									<h4 class="alert-heading">Información importante!</h4>
									<p>Digite la Nivelación, el acta y la fecha para cada estudiante en la materia correspondiente y pulse Enter o simplemente cambie de casilla para que los cambios se guarden automaticamente.</p>
									<p style="font-weight:bold;">Por favor despu&eacute;s de digitar cada dato, espere un momento a que el sistema le indique que estos se guadaron y prosiga.</p>
									<p style="font-weight:bold;">Para ver los cambios reflejados en pantalla debe actualizar (Tecla F5) la página.</p>
									</div>
									<div id="resp"></div>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><b>Curso:</b> <?=$curso['gra_nombre'];?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?=$grupo['gru_nombre'];?></header>
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
														<a href="../compartido/informe-nivelaciones.php?curso=<?=$_REQUEST["curso"];?>&grupo=<?=$_REQUEST["grupo"];?>" id="addRow" class="btn deepPink-bgcolor" target="_blank">
															Sacar Informe
														</a>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
											<thead>
												<tr>
													<th rowspan="2" style="font-size:9px;">Mat</th>
													<th rowspan="2" style="font-size:9px;">Estudiante</th>
													<?php
													//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
													$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_REQUEST["curso"], $_REQUEST["grupo"]);
													$numCargasPorCurso = mysqli_num_rows($cargas); 
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
													?>
														<th style="font-size:9px; text-align:center; border:groove;" colspan="3" width="5%"><?=$carga['mat_nombre'];?></th>
													<?php
													}
													?>
													<th rowspan="2" style="text-align:center;">PROM</th>
												</tr>
													
												<tr>
													<?php
													$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_REQUEST["curso"], $_REQUEST["grupo"]);
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
													?>	
													<th style="text-align:center;">DEF</th>
													<th style="text-align:center;">Acta</th>
													<th style="text-align:center;">Fecha</th>
													<?php
													}
													?>
												</tr>
												
												</thead>
                                                <tbody>
												<?php
									 			$filtroAdicional = "";
												if(!empty($_REQUEST["curso"]) and !empty($_REQUEST["grupo"])){
													$filtroAdicional= "AND mat_grado='".$_REQUEST["curso"]."' AND mat_grupo='".$_REQUEST["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
												}
												$cursoActual=GradoServicios::consultarCurso($_REQUEST["curso"]);
												$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual);
												while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
												$nombre = Estudiantes::NombreCompletoDelEstudiante($resultado);	
												$defPorEstudiante = 0;
												?>
												<tr id="data1" class="odd gradeX">
													<td style="font-size:9px;"><?=$resultado['mat_matricula'];?></td>
													<td style="font-size:9px;"><?=$nombre?></td>
													<?php
													$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $_REQUEST["curso"], $_REQUEST["grupo"]);
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
														$p = 1;
														$defPorMateria = 0;
														//PERIODOS DE CADA MATERIA
														while($p<=$config[19]){
															$boletin = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $resultado['mat_id'], $carga['car_id']);
															if(!empty($boletin['bol_nota'])){
																if($boletin['bol_nota']<$config[5])$color = $config[6]; elseif($boletin['bol_nota']>=$config[5]) $color = $config[7];
																$defPorMateria += $boletin['bol_nota'];
															}
															$p++;
														}
														$defPorMateria = round($defPorMateria/$config[19],2);
														//CONSULTAR NIVELACIONES
														$consultaNiv = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $resultado['mat_id'], $carga['car_id']);
														$cNiv = mysqli_fetch_array($consultaNiv, MYSQLI_BOTH);
														if(!empty($cNiv['niv_definitiva']) && $cNiv['niv_definitiva']>$defPorMateria){$defPorMateria=$cNiv['niv_definitiva']; $msj = 'Nivelación';}else{$defPorMateria=$defPorMateria; $msj = '';}
														//DEFINITIVA DE CADA MATERIA
														if($defPorMateria<$config[5] and $defPorMateria!="")$color = $config[6]; elseif($defPorMateria>=$config[5]) $color = $config[7];
														?>
															<td style="text-align:center; background:#FFC;"><input style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>" value="<?=$defPorMateria;?>" id="<?=$resultado['mat_id'];?>" name="<?=$carga['car_id'];?>" alt="1" onChange="niv(this)" <?=$disabledPermiso;?>><br>
																<?php if(!empty($cNiv['niv_id'])){?>
																	<span style="font-size:10px; color:rgb(255,0,0);"><?=$msj;?></span><br>
																	<a href="javascript:void(0);" 
																	onClick="sweetConfirmacion('Alerta!','Desea eliminar este registro?','question','estudiantes-nivelaciones-eliminar.php?idNiv=<?=$cNiv['niv_id'];?>&curso=<?=$_REQUEST["curso"];?>&grupo=<?=$_REQUEST["grupo"];?>')"
																	>
																	<img src="../files/iconos/1363803022_001_052.png"></a>
																<?php }?>
															</td>
															<td style="text-align:center;"><input style="text-align:center; width:40px;" value="<?php if(!empty($cNiv['niv_acta'])) echo $cNiv['niv_acta'];?>" id="<?=$resultado['mat_id'];?>" name="<?=$carga['car_id'];?>" alt="2" onChange="niv(this)" <?=$disabledPermiso;?>></td>
															<td style="text-align:center;"><input type="date" style="text-align:center; width:150px;" value="<?php if(!empty($cNiv['niv_fecha_nivelacion'])) echo $cNiv['niv_fecha_nivelacion'];?>" id="<?=$resultado['mat_id'];?>" name="<?=$carga['car_id'];?>" alt="3" onChange="niv(this)" <?=$disabledPermiso;?>></td>
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