<?php include("session.php"); ?>
<?php include("../estudiante/verificar-usuario.php");?>
<?php //include("verificar-sanciones.php"); ?>
<?php $idPaginaInterna = 'AC0015'; ?>
<?php include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php"); ?>
<?php //include("verificar-carga.php"); ?>
<?php include("../compartido/head.php");
	$usrEstud="";
	if(!empty($_GET["usrEstud"])){ $usrEstud=base64_decode($_GET["usrEstud"]);}
	$periodo="";
	if(!empty($_GET["periodo"])){ $periodo=base64_decode($_GET["periodo"]);}
?>


<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- dropzone -->
<link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title"><?=$frases[264][$datosUsuarioActual[8]];?></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <?php
                    $aspectos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
                    WHERE dn_cod_estudiante=" . $datosEstudianteActual['mat_id'] . " AND dn_periodo='" . $periodo . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);
                       
                    if(!empty($aspectos[0])){
                        mysqli_query($conexion, "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_ultima_lectura=now()
                        WHERE dn_cod_estudiante=" . $datosEstudianteActual['mat_id'] . " AND dn_periodo='" . $periodo . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
                    }   
                    
                    ?>

                    <div class="col-sm-9">
                        <div class="card card-box">
                            <div class="card-head">
                                <header><?=$frases[264][$datosUsuarioActual[8]];?></header>
                            </div>
                            <div class="card-body " id="bar-parent6">
                                <form action="aspectos-firmar.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="estudiante" value="<?=$datosEstudianteActual['mat_id'];?>">
                                    <input type="hidden" name="periodo" value="<?=$periodo;?>">

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label"><?=$frases[281][$datosUsuarioActual[8]];?></label>
                                        <div class="col-sm-10">
                                            <?php if(!empty($aspectos["dn_aspecto_academico"])){ echo $aspectos["dn_aspecto_academico"];} ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label"><?=$frases[282][$datosUsuarioActual[8]];?></label>
                                        <div class="col-sm-10">
                                            <?php if(!empty($aspectos["dn_aspecto_convivencial"])){ echo $aspectos["dn_aspecto_convivencial"];} ?>
                                        </div>
                                    </div>

                                    <?php if($config['conf_ver_observador']==1 && !empty($aspectos[0]) && $aspectos["dn_aprobado"]=='0'){ ?>
                                        <input type="submit" class="btn btn-primary" value="He leído y estoy de acuerdo">&nbsp;
                                    <?php } ?>

                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">

                    <div class="panel">
									<header class="panel-heading panel-heading-purple"><?= $frases[106][$datosUsuarioActual['uss_idioma']]; ?> </header>
									<div class="panel-body">
										<?php
										$porcentaje = 0;
										for ($i = 1; $i <= $datosEstudianteActual['gra_periodos']; $i++) {
											$periodosCursos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_grados_periodos
												WHERE gvp_grado='" . $datosEstudianteActual['mat_grado'] . "' AND gvp_periodo='" . $i . "'
												"), MYSQLI_BOTH);
												$porcentajeGrado=25;
												if(!empty($periodosCursos['gvp_valor'])){
                                                    $porcentajeGrado=$periodosCursos['gvp_valor'];
												}

											$notapp = mysqli_fetch_array(mysqli_query($conexion, "SELECT bol_nota FROM academico_boletin 
												WHERE bol_estudiante='" . $datosEstudianteActual['mat_id'] . "' AND bol_periodo='" . $i . "'"), MYSQLI_BOTH);
                                            $porcentaje=0;
                                            if(!empty($notapp[0])){
                                                $porcentaje = ($notapp[0]/$config['conf_nota_hasta'])*100;
                                            }
											if (!empty($notapp[0]) && $notapp[0] < $config['conf_nota_minima_aprobar']) $colorGrafico = 'danger';
											else $colorGrafico = 'info';
											if ($i == $periodo) $estiloResaltadoP = 'style="color: orange;"';
											else $estiloResaltadoP = '';
										?>
											<p>
												<a href="<?= $_SERVER['PHP_SELF']; ?>?usrEstud=<?= base64_encode($usrEstud); ?>&periodo=<?= base64_encode($i); ?>" <?= $estiloResaltadoP; ?>><?= strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]); ?> <?= $i; ?> (<?= $porcentajeGrado; ?>%)</a>

                                                <?php
                                                    if(!empty($notapp[0]) and $config['conf_sin_nota_numerica']!=1){

                                                    $notaPorPeriodo=$notapp[0];
                                                    if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                                        $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notapp[0]);
                                                        $notaPorPeriodo= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                                    }
                                                ?>
													<div class="work-monitor work-progress">
														<div class="states">
															<div class="info">
																<div class="desc pull-left"><b><?= $frases[62][$datosUsuarioActual['uss_idioma']]; ?>:</b>
																	<?= $notaPorPeriodo; ?>
																</div>
																<div class="percent pull-right"><?= $porcentaje; ?>%</div>
															</div>

															<div class="progress progress-xs">
																<div class="progress-bar progress-bar-<?= $colorGrafico; ?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?= $porcentaje; ?>%">
																	<span class="sr-only">90% </span>
																</div>
															</div>

														</div>
													</div>
												<?php } ?>

											</p>
											<hr>
										<?php } ?>

									</div>
								</div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page content -->
        <?php // include("../compartido/panel-configuracion.php"); ?>
    </div>
    <!-- end page container -->
    <?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8"></script>
<script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js" charset="UTF-8"></script>
<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- dropzone -->
<script src="../../config-general/assets/plugins/dropzone/dropzone.js"></script>
<!--tags input-->
<script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
<script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

</html>