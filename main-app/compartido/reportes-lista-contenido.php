<?php require_once("../class/Estudiantes.php"); ?>
<div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[97][$datosUsuarioActual['uss_idioma']];?> </div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    if(!$.fn.DataTable) return;
    var tablaReportes = $('#example1').DataTable();
    $('#example1 tbody').on('click', '.expand-btn', function () {
        var $icon = $(this);
        var tr = $icon.closest('tr');
        var row = tablaReportes.row(tr);
        var detalleHtml = tr.data('detalle');

        if (row.child.isShown()) {
            row.child.hide();
            $icon.removeClass('expanded');
        } else {
            row.child(detalleHtml).show();
            $icon.addClass('expanded');
        }
    });
});
</script>
                    
                    <div class="row">
                        <div class="col-md-12">
							<?php
								$filtro = '';
								include("../directivo/includes/barra-superior-reportes-lista.php");
							?>
                            <div class="row">
								
								<div class="col-md-12">
									<?php if(!empty($_GET["filtros"]) && base64_decode($_GET["filtros"])==1){?>
									<p style="background-color: antiquewhite; color: darkblue; padding: 5px;">
									Estás viendo este listado con filtros; para verlo completo quita los filtros.
									<a href="reportes-lista.php">Quitar filtros</a>
									</p>	
									<?php }?>


									<?php
									 if($datosUsuarioActual['uss_tipo']==2 and !isset($_GET["fest"])){?>

									<div class="alert alert-info">

										<i class="icon-exclamation-sign"></i><strong>INFORMACIÓN:</strong> Usted aquí solo verá los reportes disciplinarios que usted haya realizado a los estudiantes.

									</div>
								<?php }?>


									<?php if((!empty($datosCargaActual['car_director_grupo']) && $datosCargaActual['car_director_grupo']==1) && Modulos::validarPermisoEdicion()){?>
									<form class="form-horizontal" action="../compartido/reporte-disciplina-sacar.php" method="post" enctype="multipart/form-data" target="_blank">
										<input type="hidden" name="id" value="12">
										<input type="hidden" name="grado" value="<?=$datosCargaActual['car_curso'];?>">
										<input type="hidden" name="grupo" value="<?=$datosCargaActual['car_grupo'];?>">
										<input type="hidden" name="desde" value="<?=date("Y");?>-01-01">
										<input type="hidden" name="hasta" value="<?=date("Y-m-d");?>">

										<?php if(Modulos::validarPermisoEdicion()){?>
											<input type="submit" class="btn btn-primary" value="Ver reporte a mis estudiantes">&nbsp;
										<?php }?>
									</form>
									<?php }?>

                                    <?php
                                        $totalReportes = 0;
                                        $reportesMes = 0;
                                        $firmasPendientes = 0;
                                        $firmasCompletas = 0;
                                        $topFaltas = [];

                                        try{
                                            $sqlStats = "SELECT 
                                                COUNT(*) AS total,
                                                SUM(CASE WHEN dr_aprobacion_estudiante=1 AND dr_aprobacion_acudiente=1 THEN 1 ELSE 0 END) AS firmas_completas,
                                                SUM(CASE WHEN dr_aprobacion_estudiante=0 OR dr_aprobacion_acudiente=0 THEN 1 ELSE 0 END) AS firmas_pendientes
                                            FROM ".BD_DISCIPLINA.".disciplina_reportes
                                            WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}";
                                            $statsConsulta = mysqli_query($conexion, $sqlStats);
                                            if($statsConsulta){
                                                $stats = mysqli_fetch_array($statsConsulta, MYSQLI_BOTH);
                                                $totalReportes = (int)($stats['total'] ?? 0);
                                                $firmasCompletas = (int)($stats['firmas_completas'] ?? 0);
                                                $firmasPendientes = (int)($stats['firmas_pendientes'] ?? 0);
                                            }

                                            $sqlMes = "SELECT COUNT(*) AS total FROM ".BD_DISCIPLINA.".disciplina_reportes 
                                            WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}
                                            AND MONTH(dr_fecha)=MONTH(CURDATE()) AND YEAR(dr_fecha)=YEAR(CURDATE())";
                                            $consultaMes = mysqli_query($conexion, $sqlMes);
                                            if($consultaMes){
                                                $datosMes = mysqli_fetch_array($consultaMes, MYSQLI_BOTH);
                                                $reportesMes = (int)($datosMes['total'] ?? 0);
                                            }

                                            $sqlTop = "SELECT dfal_nombre, COUNT(*) AS total
                                            FROM ".BD_DISCIPLINA.".disciplina_reportes dr
                                            INNER JOIN ".BD_DISCIPLINA.".disciplina_faltas ON dfal_id=dr_falta AND dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}
                                            WHERE dr.institucion={$config['conf_id_institucion']} AND dr.year={$_SESSION["bd"]}
                                            GROUP BY dfal_nombre
                                            ORDER BY total DESC
                                            LIMIT 3";
                                            $consultaTop = mysqli_query($conexion, $sqlTop);
                                            if($consultaTop){
                                                while($filaTop = mysqli_fetch_array($consultaTop, MYSQLI_BOTH)){
                                                    $topFaltas[] = $filaTop;
                                                }
                                            }
                                        } catch (Exception $e) {
                                            include("../compartido/error-catch-to-report.php");
                                        }
                                    ?>

                                    <div class="row" style="margin-bottom: 20px;">
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%); border: none; color: #fff;">
                                                        <div class="card-body" style="padding: 18px;">
                                                            <span style="text-transform: uppercase; font-size: 12px; opacity: 0.8;">Reportes totales</span>
                                                            <h3 style="margin: 5px 0; font-weight: 700;"><?=$totalReportes;?></h3>
                                                            <small>Acumulado en el año actual</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card" style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); border: none; color: #0f3785;">
                                                        <div class="card-body" style="padding: 18px;">
                                                            <span style="text-transform: uppercase; font-size: 12px; opacity: 0.8;">Reportes del mes</span>
                                                            <h3 style="margin: 5px 0; font-weight: 700;"><?=$reportesMes;?></h3>
                                                            <?php
                                                                $mesActualNumero = date('n');
                                                                $mesesES = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
                                                                $mesesEN = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
                                                                $mesNombre = ($datosUsuarioActual['uss_idioma']=='Ingles') ? $mesesEN[$mesActualNumero] : $mesesES[$mesActualNumero];
                                                            ?>
                                                            <small><?=$mesNombre;?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card" style="background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); border: none; color: #0c3059;">
                                                        <div class="card-body" style="padding: 18px;">
                                                            <span style="text-transform: uppercase; font-size: 12px; opacity: 0.8;">Firmas pendientes</span>
                                                            <h3 style="margin: 5px 0; font-weight: 700;"><?=$firmasPendientes;?></h3>
                                                            <small><?=$firmasCompletas;?> con firmas completas</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card" style="height: 100%;">
                                                <div class="card-body" style="padding: 18px;">
                                                    <span style="text-transform: uppercase; font-size: 12px; color: #999;">Faltas más recurrentes</span>
                                                    <ul style="list-style: none; padding: 0; margin: 15px 0 0 0;">
                                                        <?php if(empty($topFaltas)){ ?>
                                                            <li style="color: #666;">Sin datos suficientes</li>
                                                        <?php } else { 
                                                            foreach($topFaltas as $falt){ ?>
                                                                <li style="margin-bottom: 8px; font-size: 13px; display: flex; justify-content: space-between;">
                                                                    <span><?=$falt['dfal_nombre'];?></span>
                                                                    <strong><?=$falt['total'];?></strong>
                                                                </li>
                                                        <?php } } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[97][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <div class="table-responsive">
                                            <style>
                                                .expand-btn {
                                                    cursor: pointer;
                                                    color: #5867dd;
                                                    transition: transform 0.2s ease;
                                                }
                                                .expand-btn.expanded {
                                                    transform: rotate(90deg);
                                                    color: #2f3c7e;
                                                }
                                                .detalle-reporte-card {
                                                    background: #f7f9fc;
                                                    border: 1px solid #e1e7f5;
                                                    border-radius: 10px;
                                                    padding: 18px;
                                                    margin: 10px 0;
                                                    font-size: 13px;
                                                    color: #2f3c7e;
                                                }
                                                .detalle-reporte-grid {
                                                    display: grid;
                                                    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                                                    gap: 12px;
                                                }
                                                .detalle-item span {
                                                    display: block;
                                                    font-size: 11px;
                                                    text-transform: uppercase;
                                                    color: #8f9bb3;
                                                    letter-spacing: 0.5px;
                                                }
                                                .detalle-item strong {
                                                    font-size: 14px;
                                                }
                                                .detalle-comentario {
                                                    margin-top: 15px;
                                                    padding: 12px;
                                                    background: #fff;
                                                    border-radius: 6px;
                                                    border-left: 3px solid #5867dd;
                                                }
                                            </style>
                                            <table id="example1" class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[222][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[186][$datosUsuarioActual['uss_idioma']];?></th>
														<th title="Firma y aprobación del estudiante">F.E</th>
														<th title="Firma y aprobación del acudiente">F.A</th>
														<?php if(Modulos::validarPermisoEdicion()){?>
															<th>&nbsp;</th>
														<?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													if(!empty($_GET["est"])){$filtro .= " AND dr_estudiante='".base64_decode($_GET["est"])."'";}
													if(!empty($_GET["falta"])){$filtro .= " AND dr_falta='".base64_decode($_GET["falta"])."'";}
												
													if(
														$datosUsuarioActual['uss_tipo']!=TIPO_DIRECTIVO &&
														$datosUsuarioActual['uss_tipo']!=TIPO_DEV &&
														!isset($_GET["fest"])
													) {
														$filtro .= " AND dr_usuario='".$_SESSION["id"]."'";
													}

													include("../directivo/includes/consulta-paginacion-reportes-lista.php");
													
													$sqlConsulta = "SELECT * FROM ".BD_DISCIPLINA.".disciplina_reportes dr
													INNER JOIN ".BD_DISCIPLINA.".disciplina_faltas ON dfal_id=dr_falta AND dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}
													INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
													INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_id_usuario=dr_estudiante AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
													LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat_grado AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]}
													LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
													LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=dr_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
													WHERE dr_id=dr_id AND dr.institucion={$config['conf_id_institucion']} AND dr.year={$_SESSION["bd"]} $filtro
													LIMIT $inicio,$registros";
													$consulta = mysqli_query($conexion, $sqlConsulta);
													$contReg = 1;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													?>
                                                    
                                                    <?php
                                                        ob_start();
                                                    ?>
                                                    <div class="detalle-reporte-card">
                                                        <div class="detalle-reporte-grid">
                                                            <div class="detalle-item">
                                                                <span>Fecha</span>
                                                                <strong><?=$resultado['dr_fecha'];?></strong>
                                                            </div>
                                                            <div class="detalle-item">
                                                                <span>Falta</span>
                                                                <strong><?=$resultado['dfal_codigo']." - ".$resultado['dfal_nombre'];?></strong>
                                                            </div>
                                                            <div class="detalle-item">
                                                                <span>Categoría</span>
                                                                <strong><?=$resultado['dcat_nombre'];?></strong>
                                                            </div>
                                                            <div class="detalle-item">
                                                                <span>Responsable</span>
                                                                <strong><?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></strong>
                                                            </div>
                                                            <div class="detalle-item">
                                                                <span>Firma estudiante</span>
                                                                <strong><?=$resultado['dr_aprobacion_estudiante']==1 ? 'Firmado' : 'Pendiente';?></strong>
                                                            </div>
                                                            <div class="detalle-item">
                                                                <span>Firma acudiente</span>
                                                                <strong><?=$resultado['dr_aprobacion_acudiente']==1 ? 'Firmado' : 'Pendiente';?></strong>
                                                            </div>
                                                        </div>
                                                        <?php if(!empty($resultado['dr_comentario'])){ ?>
                                                        <div class="detalle-comentario">
                                                            <span style="font-size:11px; text-transform:uppercase; color:#8f9bb3;">Comentario</span>
                                                            <?=nl2br(htmlspecialchars($resultado['dr_comentario']));?>
                                                        </div>
                                                        <?php } ?>
                                                        <?php if(!empty($resultado['dr_observaciones'])){ ?>
                                                        <div class="detalle-comentario" style="border-left-color:#00b894;">
                                                            <span style="font-size:11px; text-transform:uppercase; color:#8f9bb3;">Observaciones</span>
                                                            <?=nl2br(htmlspecialchars($resultado['dr_observaciones']));?>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <?php
                                                        $detalleHtml = htmlspecialchars(ob_get_clean(), ENT_QUOTES, 'UTF-8');
                                                    ?>

													<tr id="reg<?=$resultado['dr_id'];?>" class="reporte-row" data-detalle="<?=$detalleHtml;?>">
                                                        <td><i class="fa fa-chevron-right expand-btn"></i></td>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['dr_fecha'];?></td>
														<td><a href="reportes-lista.php?est=<?=base64_encode($resultado['mat_id_usuario']);?>&filtros=<?=base64_encode(1);?>"><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></a><br><?=$resultado['gra_nombre']." ".$resultado['gru_nombre'];?></td>
														<td><?=$resultado['dcat_nombre'];?></td>
														<td><?=$resultado['dfal_codigo'];?></td>
														<td><a href="reportes-lista.php?falta=<?=base64_encode($resultado['dfal_id']);?>&filtros=<?=base64_encode(1);?>"><?=$resultado['dfal_nombre'];?></a></td>
														<td><?=UsuariosPadre::nombreCompletoDelUsuario($resultado);?></td>
														<td>
															<?php if($resultado['dr_aprobacion_estudiante']==0){ echo "-"; }else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_estudiante_fecha'];?>"></i>
															<?php }?>
														</td>
														<td>
															<?php if($resultado['dr_aprobacion_acudiente']==0){ echo "-"; }else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_acudiente_fecha'];?>"></i>
															<?php }?>
														</td>
														<?php if(Modulos::validarPermisoEdicion()){?>
															<td>
															<?php if(Modulos::validarSubRol(['DT0025', 'DT0056', 'DT0055', 'DT0054', 'DT0026'])) {?>
																<?php
																	$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
																	$arrayDatos = json_encode($arrayEnviar);
																	$objetoEnviar = htmlentities($arrayDatos);
																	?>
																
																	<div class="btn-group">
																		<button type="button" class="btn btn-primary">Acciones</button>
																		<button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																			<i class="fa fa-angle-down"></i>
																		</button>
																		<ul class="dropdown-menu" role="menu">
																		<?php if( Modulos::validarSubRol(['DT0025']) ){?>
																			<li><a href="reportes-firmar-estudiante.php?idR=<?=base64_encode($resultado['dr_id']);?>">Firmar por el estudiante</a></li>
																		<?php }?>

																		<?php if( Modulos::validarSubRol(['DT0056']) ){?>
																			<li><a href="reportes-firmar-acudiente.php?idR=<?=base64_encode($resultado['dr_id']);?>">Firmar por el acudiente</a></li>
																		<?php }?>

																		<?php if( Modulos::validarSubRol(['DT0055']) ){?>
																			<li><a href="reportes-firma-quitar-estudiante.php?idR=<?=base64_encode($resultado['dr_id']);?>">Quitar firma estudiante</a></li>
																		<?php }?>

																		<?php if( Modulos::validarSubRol(['DT0054']) ){?>
																			<li><a href="reportes-firmar-quitar-acudiente.php?idR=<?=base64_encode($resultado['dr_id']);?>">Quitar firma acudiente</a></li>
																		<?php }?>
																			
																			<?php if( ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) && Modulos::validarSubRol(['DT0026'])){?>

																				<li><a href="#" title="<?=$objetoEnviar;?>" id="<?=$resultado['dr_id'];?>" name="reportes-eliminar.php?idR=<?=base64_encode($resultado['dr_id']);?>" onClick="deseaEliminar(this)">Eliminar</a></li>
																				
																			<?php }?>

																		</ul>
																	</div>
																<?php }?>
															</td>
														<?php }?>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
                                            </table>
											<?php 
                            				$botones = new botonesGuardar("estudiantes.php", false); ?>
                                            </div>
                                        </div>
                                    </div>
                      				<?php include("../directivo/enlaces-paginacion.php");?>
                                </div>
								
							
                            </div>
                        </div>
                    </div>
                </div>