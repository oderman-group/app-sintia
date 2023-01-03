<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0081';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript">
    function def(enviada){
    var nota = enviada.value;
    var codEst = enviada.id;
    var carga = enviada.name;
    var per = enviada.alt;
    if (nota><?=$config[4];?> || isNaN(nota) || nota< <?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;}	
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
									<b>Curso:</b> <?php if(isset($curso[2])){echo $curso[2];}?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?php if(isset($grupo[2])){echo $grupo[2];}?>
								</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<?php
								$consultaCurso=mysqli_query($conexion, "SELECT * FROM academico_grados WHERE gra_id='".$_POST["curso"]."'");
								$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
								$consultaGrupo=mysqli_query($conexion, "SELECT * FROM academico_grupos WHERE gru_id='".$_POST["grupo"]."'");
								$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);
								?>
								
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
												<p style="font-weight:bold;">Por favor despu&eacute;s de digitar una nota, espere un momento a que el sistema le indique que la nota se guad&oacute; y prosiga con la siguiente.</p>
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
															Sacar Informe <i class="fa fa-plus"></i>
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
													$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$_POST["curso"]."' AND car_grupo='".$_POST["grupo"]."' AND car_activa=1");
													//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
													$numCargasPorCurso = mysqli_num_rows($cargas); 
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
														$consultaMateria=mysqli_query($conexion, "SELECT * FROM academico_materias WHERE mat_id='".$carga[4]."'");
														$materia = mysqli_fetch_array($consultaMateria, MYSQLI_BOTH);
													?>
														<th style="font-size:9px; text-align:center; border:groove;" colspan="<?=$config[19]+1;?>" width="5%"><?=$materia[2];?></th>
													<?php
													}
													?>
													<th rowspan="2" style="text-align:center;">PROM</th>
													</tr>
													
													<tr>
														<?php
														$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$_POST["curso"]."' AND car_grupo='".$_POST["grupo"]."' AND car_activa=1"); 
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
												$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_POST["curso"]."' AND mat_grupo='".$_POST["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
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
													<td style="font-size:9px;"><?=$resultado[12];?></td>
													<td style="font-size:9px;"><?=$resultado[3]." ".$resultado[4]." ".$resultado[5];?></td>
													<?php
													$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$_POST["curso"]."' AND car_grupo='".$_POST["grupo"]."' AND car_activa=1"); 
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
														$consultaMateria=mysqli_query($conexion, "SELECT * FROM academico_materias WHERE mat_id='".$carga[4]."'");
														$materia = mysqli_fetch_array($consultaMateria, MYSQLI_BOTH);
														$p = 1;
														$defPorMateria = 0;
														//PERIODOS DE CADA MATERIA
														while($p<=$config[19]){
															$consultaBoletin=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_carga='".$carga[0]."' AND bol_estudiante='".$resultado[0]."' AND bol_periodo='".$p."'");
															$boletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH);
															if(isset($boletin[4]) and $boletin[4]<$config[5] and $boletin[4]!="")$color = $config[6]; elseif(isset($boletin[4]) and $boletin[4]>=$config[5]) $color = $config[7];
															if(isset($boletin[4])){
																$defPorMateria += $boletin[4];
															}
															if(isset($boletin[5]) and $boletin[5]==1) $tipo = '<span style="color:blue; font-size:9px;">Normal</span>'; 
															elseif(isset($boletin[5]) and $boletin[5]==2) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n Per.</span>';
															elseif(isset($boletin[5]) and $boletin[5]==3) $tipo = '<span style="color:red; font-size:9px;">Recuperaci&oacute;n Ind.</span>';
															elseif(isset($boletin[5]) and $boletin[5]==4) $tipo = '<span style="color:red; font-size:9px;">Directivo</span>';
															
															else $tipo='';
															//DEFINITIVA DE CADA PERIODO
															
															$disabled = "";
															if(isset($boletin[4]) and ($boletin[4]!="" or $carga['car_periodo']<=$p)){
																$disabled = "disabled";
															}
														?>	
															<td style="text-align:center;">
																<input style="text-align:center; width:40px; color:<?=$color;?>" value="<?php if(isset($boletin[4])){ echo $boletin[4];}?>" name="<?=$carga[0];?>" id="<?=$resultado[0];?>" onChange="def(this)" alt="<?=$p;?>" title="Materia: <?=$materia[2];?> - Periodo: <?=$p;?>" <?=$disabled;?>><br><?=$tipo;?>
															</td>
														<?php
															$p++;
														}
														$defPorMateria = round($defPorMateria/$config[19],2);
															//DEFINITIVA DE CADA MATERIA
															if($defPorMateria<$config[5] and $defPorMateria!="")$color = $config[6]; elseif($defPorMateria>=$config[5]) $color = $config[7];
															//CONSULTAR NIVELACIONES
															$consultaNiv=mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_cod_estudiante='".$resultado[0]."' AND niv_id_asg='".$carga[0]."'");
															$cNiv = mysqli_fetch_array($consultaNiv, MYSQLI_BOTH);
															if(isset($cNiv[3]) and $cNiv[3]>$defPorMateria){$defPorMateria=$cNiv[3]; $msj = 'Nivelación';}else{$defPorMateria=$defPorMateria; $msj = '';}
														?>
															<td style="text-align:center; background:#FFC;"><input style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>" value="<?php if(isset($defPorMateria)){ echo $defPorMateria;}?>" disabled><br><span style="font-size:10px; color:rgb(255,0,0); font-weight:bold;"><?php if(isset($msj)){ echo $msj;}?><br><?php if(isset($cNiv[5]) and isset($cNiv[6])){ echo "Acta ".$cNiv[5]." de ".$cNiv[6];}?></span></td>
													<?php
														//DEFINITIVA POR CADA ESTUDIANTE DE TODAS LAS MATERIAS Y PERIODOS
														$defPorEstudiante += $defPorMateria;   
													}
														$defPorEstudiante = round($defPorEstudiante/$numCargasPorCurso,2);
														if($defPorEstudiante<$config[5] and $defPorEstudiante!="")$color = $config[6]; elseif($defPorEstudiante>=$config[5]) $color = $config[7];
														//PRIMER PUESTO
														if($defPorEstudiante>$primerPuestoNota){
															$primerPuestoNota = $defPorEstudiante;
															$primerPuestoNombre = $resultado[3]." ".$resultado[4]." ".$resultado[5];
															$primerPuestoID = $resultado[0];
														}
														//SEGUNDO PUESTO
														if($defPorEstudiante>$segundoPuestoNota and $defPorEstudiante<=$primerPuestoNota and $resultado[0]!=$primerPuestoID){
															$segundoPuestoNota = $defPorEstudiante;
															$segundoPuestoNombre = $resultado[3]." ".$resultado[4]." ".$resultado[5];
															$segundoPuestoID = $resultado[0];
														}
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

										<div class="col-md-12">
											<div class="card card-topline-purple">
												<div class="card-body">

													<div class="table-scrollable">
														<table cellpadding="0" cellspacing="0" border="1"  class="display al" style="width:100%;">
															<thead>
																<tr>
																	<th colspan="3" style="text-align:center; background:#0CC;">PUESTOS POR ESTUDIANTE</th>
																</tr>
																<tr>
																	<th style="text-align:center;">PUESTO</th>
																	<th style="text-align:center;">ESTUDIANTE</th>
																	<th style="text-align:center;">PROMEDIO</th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td style="text-align:center;">1</td>
																	<td><?=$primerPuestoNombre;?></td>
																	<td><?=$primerPuestoNota;?></td>
																</tr>
																<tr>
																	<td style="text-align:center;">2</td>
																	<td><?=$segundoPuestoNombre;?></td>
																	<td><?=$segundoPuestoNota;?></td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
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