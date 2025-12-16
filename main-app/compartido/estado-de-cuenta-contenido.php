<?php require_once(ROOT_PATH."/main-app/class/Movimientos.php"); ?>
					<div class="row">
                        <div class="col-md-12">
                            <div class="row">
								<?php
								$resumen = mysqli_fetch_array(mysqli_query($conexion, "SELECT
								(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
								(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=2 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
								(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=3 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
								(SELECT sum(pi.payment) FROM ".BD_FINANCIERA.".payments p
								INNER JOIN ".BD_FINANCIERA.".payments_invoiced pi ON pi.payments=p.cod_payment
								WHERE p.invoiced='".$_SESSION["id"]."' AND p.institucion={$config['conf_id_institucion']} AND p.year={$_SESSION["bd"]})
								"), MYSQLI_BOTH);
								$saldo = ($resumen[3] - $resumen[0]);
								$colorSaldo = 'black';
								$mensajeSaldo=$frases[309][$datosUsuarioActual['uss_idioma']];
								if($saldo>0){$mensajeSaldo=$frases[310][$datosUsuarioActual['uss_idioma']]; $colorSaldo = 'green';}
								if($saldo<0){$mensajeSaldo=$frases[311][$datosUsuarioActual['uss_idioma']]; $colorSaldo = 'red';}
								?>
								<div class="col-md-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[312][$datosUsuarioActual['uss_idioma']]);?> </header>
                                        <div class="panel-body">
											<?php
											if(empty($resumen[4])){
												$resumen[4]=0;
											}
											if(empty($resumen[1])){
												$resumen[1]=0;
											}
											// Validar que los valores no sean null antes de formatear
											$resumen0 = $resumen[0] ?? 0;
											$resumen3 = $resumen[3] ?? 0;
											$saldoFormateado = $saldo ?? 0;
											?>
											<p><b><?=strtoupper($frases[313][$datosUsuarioActual['uss_idioma']]);?>:</b> $<?=number_format((float)$resumen0, 0, ",", ".");?></p>
											<p><b><?=strtoupper($frases[413][$datosUsuarioActual['uss_idioma']]);?>:</b> $<?=number_format((float)$resumen3, 0, ",", ".");?></p>
											<hr>
											<p><b><?=strtoupper($frases[315][$datosUsuarioActual['uss_idioma']]);?>:</b> <span style="color: <?=$colorSaldo;?>;">$<?=number_format((float)$saldoFormateado, 0, ",", ".");?></span></p>
											<p style="color: blueviolet;"><?=$mensajeSaldo;?></p>
										</div>
									</div>

									<?php if(Modulos::verificarModulosDeInstitucion(Modulos::MODULO_API_SION_ACADEMICA)) {?>
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
														<th><?= $frases[107][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[413][$datosUsuarioActual['uss_idioma']]; ?></th>
														<th><?= $frases[418][$datosUsuarioActual['uss_idioma']]; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
														$consulta = mysqli_query($conexion, "SELECT * FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
														WHERE fcu_usuario='{$_SESSION["id"]}' AND fcu_anulado=0
															AND fc.institucion={$_SESSION['idInstitucion']} 
															AND fc.year='{$_SESSION["bd"]}' 
														ORDER BY fc.id_nuevo DESC");
														$contReg = 1;
														while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
															$vlrAdicional = !empty($resultado['fcu_valor']) ? $resultado['fcu_valor'] : 0;
															$totalNeto    = Movimientos::calcularTotalNeto($conexion, $config, $resultado['fcu_id'], $vlrAdicional);
															$abonos       = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);
															$porCobrar    = $totalNeto - $abonos;

															$colorValor = $porCobrar > 0 ? 'red' : 'black';
													?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['fcu_fecha'];?></td>
														<td><?=$resultado['fcu_detalle'];?></td>
														<td id="totalNeto<?= $resultado['fcu_id']; ?>" data-tipo="<?= $resultado['fcu_tipo'] ?>" data-total-neto="<?= $totalNeto ?>">$<?= !empty($totalNeto) ? number_format($totalNeto, 0, ",", ".") : 0 ?></td>
														<td data-abonos="<?= $abonos ?>">$<?= !empty($abonos) ? number_format($abonos, 0, ",", ".") : 0 ?></td>
														<td style="color:<?=$colorValor;?>;" data-por-cobrar="<?= $porCobrar ?>">$<?= !empty($porCobrar) ? number_format($porCobrar, 0, ",", ".") : 0 ?></td>
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