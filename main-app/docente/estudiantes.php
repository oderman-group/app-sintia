<?php
include("session.php");
$idPaginaInterna = 'DC0010';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
require_once("../class/Estudiantes.php");
include("../compartido/head.php");
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
                                <div class="page-title"><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></header>
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
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[241][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[138][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$fotoEstudiante = $usuariosClase->verificarFoto($resultado['uss_foto']);
														$consultaGenero=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id='".$resultado['mat_genero']."'");
														 $genero = mysqli_fetch_array($consultaGenero, MYSQLI_BOTH);
														 if (!$genero) {
															$genero = array('', 'N/A'); // Valor por defecto si no se encuentra
														 }
														//DEFINITIVAS
														$carga = $cargaConsultaActual;
														$periodo = $periodoConsultaActual;
														$estudiante = $resultado['mat_id'];
														include("../definitivas.php");
														if($definitiva<$config[5] and $definitiva!="") $colorNota = $config[6]; elseif($definitiva>=$config[5]) $colorNota = $config[7]; else {$colorNota = 'black'; $definitiva='';}
														 
														$colorEstudiante = '#000;';
														if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';} 
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['mat_id'];?></td>
														<td>
															<?=$resultado['mat_documento'];?>
															
														</td>
														<td style="color: <?=$colorEstudiante;?>">
															<img src="<?=$fotoEstudiante;?>" width="50">
															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
														</td>
														<td><?=$genero[1];?></td>
														<td><a href="calificaciones-estudiante.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carga=<?=base64_encode($cargaConsultaActual);?>" style="text-decoration:underline; color:<?=$colorNota;?>;"><?=$definitiva;?></a></td>
														<td>
														
															<div class="btn-group">
																<button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>

																<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	<i class="fa fa-angle-down"></i>
																</button>

																<ul class="dropdown-menu" role="menu">
																	<?php if(!isset($_SESSION['admin'])){?>
																		<li><a href="auto-login.php?user=<?=base64_encode($resultado['mat_id_usuario']);?>">Autologin</a></li>
																	<?php }?>

																	<?php if($datosCargaActual['car_director_grupo']==1){?>
																		<li><a href="reportes-lista.php?est=<?=base64_encode($resultado['mat_id_usuario']);?>&filtros=<?=base64_encode(1);?>">R. Disciplina</a></li>
																		<li><a href="aspectos-estudiantiles.php?idR=<?=base64_encode($resultado['mat_id_usuario']);?>">Ficha estudiantil</a></li>
																	<?php }?>

																	<li><a href="matriculas-adjuntar-documentos.php?id=<?= base64_encode($resultado['mat_id_usuario']); ?>&idMatricula=<?= base64_encode($resultado['mat_id']); ?>"><?=$frases[434][$datosUsuarioActual['uss_idioma']];?></a></li>
																</ul>
															</div>

								
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