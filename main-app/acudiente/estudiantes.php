<?php include("session.php");?>
<?php //include("verificar-sanciones.php");?>
<?php $idPaginaInterna = 'AC0005';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
?>
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
                                <div class="page-title"><?=$frases[71][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-12">
									
									<?php if($_GET["req"]==1){?>
										<div class="card card-topline-red">
											<div class="card-head">
												<header>Solicitud de desbloqueo</header>
											</div>
											<div class="card-body">
												<p>Si cree que el estudiante <b><?=strtoupper($_GET["nameE"]);?></b> está bloqueado por error, entonces envíe la solicitud de desbloqueo a los directivos de la Institución.</p>
												<form class="form-horizontal" action="guardar.php" method="post">
													<input type="hidden" name="id" value="1">
													<input type="hidden" name="idRecurso" value="<?=$_GET["idE"];?>">
													<div class="form-group row">
														<div class="col-sm-12">
															<textarea name="contenido" class="form-control" rows="3" placeholder="Si desea puede escribir aquí un comentario" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
														</div>
													</div>

													<div class="form-group">
														<div class="offset-md-3 col-md-9">
															<button type="submit" class="btn btn-info">Enviar solicitud</button>
															<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual[8]];?></button>
														</div>
													</div>
												</form>
											</div>
										</div>
									<?php }?>
									
									<?php if($_GET["req"]==2){?>
										<div class="card card-topline-green">
											<div class="card-head">
												<header>Reserva de cupos</header>
											</div>
											<div class="card-body">
												<p>¿Desea reservar el cupo para el estudiante <b><?=strtoupper($_GET["nameE"]);?></b> para el siguiente año escolar?.</p>
												<form name="formularioCupo" class="form-horizontal" action="guardar.php" method="post">
													<input type="hidden" name="id" value="2">
													<input type="hidden" name="idEstudiante" value="<?=$_GET["idE"];?>">
													
													<div class="col-sm-12">
														<input type="radio" name="respuesta" value="1" onClick="cupoNo(1)" /> SI
													</div>
													
													<div class="col-sm-12">
														<input type="radio" name="respuesta" value="2" onClick="cupoNo(2)" /> NO
													</div>
													
													<div id="motivoNo" style="display: none;">
													<p>Nos gustaría saber el motivo por el cual no desea reservar el cupo para este estudiante.</p>
													<div class="form-group row">
														<div class="col-sm-12">
															<textarea name="motivo" class="form-control" rows="3" placeholder="Escriba el motivo..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
														</div>
													</div>
													</div>	

													<div class="form-group">
														<div class="offset-md-3 col-md-9">
															<button type="submit" class="btn btn-info">Enviar respuesta</button>
															<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual[8]];?></button>
														</div>
													</div>
												</form>
											</div>
										</div>
									<?php }?>
									
                                    <div class="card card-topline-purple" data-hint="Listado de todos los acudidos que tienes registrados.">
                                        <div class="card-head">
                                            <header><?=$frases[71][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Credenciales</th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[138][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[26][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = Estudiantes::listarEstudiantesParaAcudientes($datosUsuarioActual[0]);
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $genero = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id='".$resultado[8]."'"), MYSQLI_BOTH);

														 $aspectos1 = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM disiplina_nota 
                    										WHERE dn_cod_estudiante=" . $resultado['mat_id'] . " AND dn_periodo=1"), MYSQLI_BOTH);

															$aspectos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM disiplina_nota 
															WHERE dn_cod_estudiante=" . $resultado['mat_id'] . " AND dn_periodo='" . $config['conf_periodo'] . "'"), MYSQLI_BOTH);
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
                                                        <td>
                                                        	<?php 
                                                        	echo $resultado['uss_usuario']."<br>";
                                                        	echo '<span style="font-size:10px; color:tomato;">'.$resultado['uss_clave'].'</span>';
                                                        	?>
                                                        	
                                                        </td>
														<td><?=strtoupper($resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido']." ".$resultado['mat_nombres']);?></td>
														<td><?=$genero[1];?></td>
														<td><?=strtoupper($resultado['gra_nombre']." ".$resultado['gru_nombre']);?></td>
														<td>
															<?php 
														 	if($config['conf_activar_encuesta']==1){
																$respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_encuestas 
																WHERE genc_estudiante='".$resultado['mat_id']."'"));
															}
														 
														 if($config['conf_activar_encuesta']!=1 or $respuesta>0){	
														 	if($datosUsuarioActual['uss_bloqueado']!=1){
																if($resultado['uss_bloqueado']!=1){		
															?>
																<div class="btn-group" data-hint="Despliegue el botón de acciones para ver todas las posibilidades por cada uno de sus acudidos.">
																	  <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual[8]];?></button>
																	  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																		  <i class="fa fa-angle-down"></i>
																	  </button>
																	  <ul class="dropdown-menu" role="menu">
																		  
																	  		<?php if($config['conf_calificaciones_acudientes']==1){?>
																				<?php if($config['conf_sin_nota_numerica']!=1){?>
																						<li><a href="periodos-resumen.php?usrEstud=<?=$resultado['mat_id_usuario'];?>"><?=$frases[84][$datosUsuarioActual[8]];?></a></li>
																				<?php }?>
																				<li><a href="notas-actuales.php?usrEstud=<?=$resultado['mat_id_usuario'];?>"><?=$frases[242][$datosUsuarioActual[8]];?></a></li>
																			<?php }?>

																		  <li><a href="reportes-disciplinarios.php?usrEstud=<?=$resultado['mat_id_usuario'];?>">R. Disciplina</a></li>
																		  <li><a href="aspectos.php?usrEstud=<?=$resultado['mat_id_usuario'];?>&periodo=<?=$config[2];?>">Aspectos</a></li>
																		  
																		  <?php if($config['conf_id_institucion'] == 9){?>
																		  <li><a href="../compartido/matricula-boletin-curso-<?=$resultado['gra_formato_boletin'];?>.php?id=<?=$resultado["mat_id"];?>&periodo=<?=$config[2];?>" target="_blank" >Descargar Boletín</a></li>
																		  <?php }?>
																		  
																		  <?php if($config['conf_id_institucion'] == 1){
																			  
																			if($aspectos1["dn_aprobado"] == 1 and $aspectos["dn_aprobado"] == 1){ 
																			?>
																		  
																		  	<li><a href="../compartido/matricula-boletin-curso-<?=$resultado['gra_formato_boletin'];?>.php?id=<?=$resultado["mat_id"];?>&periodo=<?=$config[2];?>" target="_blank" >Descargar Boletín</a></li>

																		  <?php 
																			}

																		  if($config['conf_informe_parcial']==1){?>
																		  	<li><a href="../compartido/informe-parcial.php?estudiante=<?=$resultado["mat_id"];?>&acu=1" target="_blank" >Informe parcial</a></li>
																		  <?php }

																		  if($config['conf_ficha_estudiantil']==1){?>
																		  	<li><a href="ficha-estudiantil.php?idR=<?=$resultado["mat_id_usuario"];?>">Ficha estudiantil</a></li>
																		  <?php }?>

																		  
																		  <?php }?>

																		  <?php if(!isset($_SESSION['admin'])){?>
																		  	<li><a href="auto-login.php?user=<?=$resultado['mat_id_usuario'];?>">Autologin</a></li>
																		  <?php }?>

																	  </ul>
																  </div>
															<?php
																	}else{
																		echo "
																		<span style='color:red;'>Bloqueado</span><br>
																		<a href='".$_SERVER['PHP_SELF']."?req=1&idE=".$resultado['mat_id']."&nameE=".$resultado['uss_nombre']."' style='text-decoration:underline;'>Solicitar desbloqueo</a>
																		";
																	}	
																}
																else{}
															}else{
																echo "
																<a href='".$_SERVER['PHP_SELF']."?req=2&idE=".$resultado['mat_id']."&nameE=".$resultado['uss_nombre']."' style='text-decoration:underline;'>¿Desea reservar el cupo?</a>
																";	
															}
															?>
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
</body>

</html>