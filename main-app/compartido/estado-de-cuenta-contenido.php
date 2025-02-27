					<div class="row">
                        <div class="col-md-12">
                            <div class="row">
								<?php
								$resumen = mysqli_fetch_array(mysqli_query($conexion, "SELECT
								(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
								(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=2 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
								(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=3 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
								(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=4 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]})
								"), MYSQLI_BOTH);
								$saldo = ($resumen[0] - $resumen[2]);
								$mensajeSaldo=$frases[309][$datosUsuarioActual['uss_idioma']];
								if($saldo>0){$mensajeSaldo=$frases[310][$datosUsuarioActual['uss_idioma']];}
								if($saldo<0){$mensajeSaldo=$frases[311][$datosUsuarioActual['uss_idioma']];}
								?>
								<div class="col-md-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[312][$datosUsuarioActual['uss_idioma']]);?> </header>
                                        <div class="panel-body">
											<?php
											if(empty($resumen[2])){
												$resumen[2]=0;
											}
											if(empty($resumen[0])){
												$resumen[0]=0;
											}
											?>
											<p><b><?=strtoupper($frases[313][$datosUsuarioActual['uss_idioma']]);?>:</b> $<?=number_format($resumen[2],0,",",".");?></p>
											<p><b><?=strtoupper($frases[314][$datosUsuarioActual['uss_idioma']]);?>:</b> $<?=number_format($resumen[0],0,",",".");?></p>
											<hr>
											<p><b><?=strtoupper($frases[315][$datosUsuarioActual['uss_idioma']]);?>:</b> $<?=number_format($saldo,0,",",".");?></p>
											<p style="color: blueviolet;"><?=$mensajeSaldo;?></p>
										</div>
									</div>

									<?php if($config['conf_id_institucion'] == ICOLVEN) {?>
										<div align="center">
											<p><mark><?=$frases[316][$datosUsuarioActual['uss_idioma']];?>: <b><?php if(!empty($datosEstudianteActual['mat_codigo_tesoreria'])){ echo $datosEstudianteActual['mat_codigo_tesoreria'];}?></b>  (cuatro dígitos, sin el 0 a la izquierda).</mark></p>

											<p><a href="https://www.avalpaycenter.com/wps/portal/portal-de-pagos/web/pagos-aval/resultado-busqueda/realizar-pago-facturadores?idConv=00022724&origen=buscar" class="btn btn-info" target="_blank"><?=strtoupper($frases[317][$datosUsuarioActual['uss_idioma']]);?></a></p>

											<p><a href="http://sion.icolven.edu.co/Services/ServiceIcolven.svc/GenerarEstadoCuenta/<?=$datosEstudianteActual['mat_codigo_tesoreria'];?>/<?=date('Y');?>" class="btn btn-success" target="_blank"><?=strtoupper($frases[104][$datosUsuarioActual['uss_idioma']]);?></a></p>
										</div>
									<?php }?>

								</div>
									
								<div class="col-md-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[104][$datosUsuarioActual['uss_idioma']];?></header>
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
														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[162][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[53][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[52][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas 
													 WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $colorValor = 'black';
														 if($resultado['fcu_tipo']==3) $colorValor = 'red';
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['fcu_fecha'];?></td>
														<td><?=$resultado['fcu_detalle'];?></td>
														<td><?=$tipoEstadoFinanzas[$resultado['fcu_tipo']];?></td>
														<td style="color:<?=$colorValor;?>;">$<?=number_format($resultado['fcu_valor'],0,",",".");?></td>
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