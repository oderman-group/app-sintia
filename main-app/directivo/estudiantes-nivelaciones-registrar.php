<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0076';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript">
  function niv(enviada){
  var nota = enviada.value;
  var codEst = enviada.id;
  var carga = enviada.name;
  var op = enviada.alt;
 if(op==1){
 	if (nota><?=$config[4];?> || isNaN(nota) || nota< <?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;}	
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
		$consultaCurso=mysqli_query($conexion, "SELECT * FROM academico_grados WHERE gra_id='".$_POST["curso"]."'");
		$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
		$consultaGrupo=mysqli_query($conexion, "SELECT * FROM academico_grupos WHERE gru_id='".$_POST["grupo"]."'");
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
                                <div class="page-title"><b>Curso:</b> <?=$curso[2];?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?=$grupo[2];?></div>
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
									</div>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><b>Curso:</b> <?=$curso[2];?>&nbsp;&nbsp;&nbsp; <b>Grupo:</b> <?=$grupo[2];?></header>
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
														<a href="../compartido/informe-nivelaciones.php?curso=<?=$_POST["curso"];?>&grupo=<?=$_POST["grupo"];?>" id="addRow" class="btn deepPink-bgcolor" target="_blank">
															Sacara Informe <i class="fa fa-plus"></i>
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
													$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$_POST["curso"]."' AND car_grupo='".$_POST["grupo"]."' AND car_activa=1");
													//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
													$numCargasPorCurso = mysqli_num_rows($cargas); 
													while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
														$consultaMateria=mysqli_query($conexion, "SELECT * FROM academico_materias WHERE mat_id='".$carga[4]."'");
														$materia = mysqli_fetch_array($consultaMateria, MYSQLI_BOTH);
													?>
														<th style="font-size:9px; text-align:center; border:groove;" colspan="3" width="5%"><?=$materia[2];?></th>
													<?php
													}
													?>
													<th rowspan="2" style="text-align:center;">PROM</th>
												</tr>
													
												<tr>
													<?php
													$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='".$_POST["curso"]."' AND car_grupo='".$_POST["grupo"]."' AND car_activa=1"); 
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
												$consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_POST["curso"]."' AND mat_grupo='".$_POST["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
												while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
												$defPorEstudiante = 0;
												?>
												<tr id="data1" class="odd gradeX">
													<td style="font-size:9px;"><?=$resultado[1];?></td>
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
															if($boletin[4]<$config[5] and $boletin[4]!="")$color = $config[6]; elseif($boletin[4]>=$config[5]) $color = $config[7];
															$defPorMateria += $boletin[4];
															$p++;
														}
														$defPorMateria = round($defPorMateria/$config[19],2);
														//CONSULTAR NIVELACIONES
														$consultaNiv=mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_cod_estudiante='".$resultado[0]."' AND niv_id_asg='".$carga[0]."'");
														$cNiv = mysqli_fetch_array($consultaNiv, MYSQLI_BOTH);
														if($cNiv[3]>$defPorMateria){$defPorMateria=$cNiv[3]; $msj = 'Nivelación';}else{$defPorMateria=$defPorMateria; $msj = '';}
														//DEFINITIVA DE CADA MATERIA
														if($defPorMateria<$config[5] and $defPorMateria!="")$color = $config[6]; elseif($defPorMateria>=$config[5]) $color = $config[7];
														?>
															<td style="text-align:center; background:#FFC;"><input style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>" value="<?=$defPorMateria;?>" id="<?=$resultado[0];?>" name="<?=$carga[0];?>" alt="1" onChange="niv(this)"><br><span style="font-size:10px; color:rgb(255,0,0);"><?=$msj;?></span><br><?php if($defPorMateria!=""){?><a href="guardar.php?get=57&idNiv=<?=$cNiv[0];?>" onClick="if(!confirm('Desea eliminar este registro?')){return false;}"><img src="../files/iconos/1363803022_001_052.png"></a><?php }?></td>
															<td style="text-align:center;"><input style="text-align:center; width:40px;" value="<?=$cNiv[5];?>" id="<?=$resultado[0];?>" name="<?=$carga[0];?>" alt="2" onChange="niv(this)"></td>
															<td style="text-align:center;"><input type="date" style="text-align:center; width:150px;" value="<?=$cNiv[6];?>" id="<?=$resultado[0];?>" name="<?=$carga[0];?>" alt="3" onChange="niv(this)"></td>
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