						
<?php
require_once(ROOT_PATH."/main-app/class/Grados.php");
$acudiente = UsuariosPadre::sesionUsuario($datosEstudianteActual["mat_acudiente"]);

$classDiv="col-sm-12";
if($config['conf_mostrar_pasos_matricula'] == 1){
	$classDiv="col-sm-9";
}
?>
						<div class="<?=$classDiv?>">
						    <div class="card card-box">
						        <div class="card-head">
						            <header><?= $frases[60][$datosUsuarioActual['uss_idioma']]; ?></header>
						        </div>
						        <div class="card-body " id="bar-parent6">
						            <form action="../compartido/matricula-actualizar.php" method="post" enctype="multipart/form-data">
										<div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[321][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-4">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_codigo_tesoreria"]; ?>" name="codTes" class="form-control" disabled>
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[60][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-4">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_matricula"]; ?>" name="matricula" class="form-control" disabled>
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[319][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_primer_apellido"]; ?>" name="nombre" class="form-control" readonly>
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[320][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_segundo_apellido"]; ?>" name="nombre" class="form-control" readonly>
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[187][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_nombres"]; ?>" name="nombre" class="form-control" readonly>
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[164][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <select class="form-control  select2" name="lNacimiento">
						                            <option value="">Seleccione una opción</option>
						                            <?php
													$opcionesG = Grados::traerGradosInstitucion($config);
                                                    while ($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)) {
                                                    ?>
						                                <option value="<?= $opg['gra_id']; ?>" <?php if ($opg['gra_id'] == $datosEstudianteActual["mat_grado"]) {
                                                                                                    echo "selected";
                                                                                                } ?> disabled><?= $opg['gra_nombre']; ?></option>
						                            <?php } ?>
						                        </select>
						                    </div>
						                </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[189][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                                                <input class="form-control" size="16" type="text" value="<?=$datosUsuarioActual["uss_fecha_nacimiento"];?>" disabled>
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            	</div>
                                            </div>
											<input type="hidden" id="dtp_input1" value="<?=$datosUsuarioActual["uss_fecha_nacimiento"];?>" name="fechaN" disabled>
                                        </div>

						                <hr>

                                        <p style="color: navy;"><?= $frases[322][$datosUsuarioActual['uss_idioma']]; ?></p>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[297][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_direccion"]; ?>" name="dir" class="form-control">
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[298][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_barrio"]; ?>" name="barrio" class="form-control">
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[323][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-4">
						                        <select class="form-control  select2" name="estrato" required>
						                            <option value="">Seleccione una opción</option>
						                            <?php
                                                    $opcionesG = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".opciones_generales WHERE ogen_grupo=3");
                                                    while ($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)) {
                                                    ?>
						                                <option value="<?= $opg['ogen_id']; ?>" <?php if ($opg['ogen_id'] == $datosEstudianteActual["mat_estrato"]) {
                                                                                            echo "selected";
                                                                                        } ?>><?= $opg['ogen_nombre']; ?></option>
						                            <?php } ?>
						                        </select>
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[182][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-4">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_telefono"]; ?>" name="telefono" class="form-control">
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[188][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-4">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_celular"]; ?>" name="celular" class="form-control">
						                    </div>
						                </div>

						                <div class="form-group row">
						                    <label class="col-sm-2 control-label">ID. <?= $frases[324][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-3">
						                        <input type="text" value="<?= $datosEstudianteActual["mat_id"]; ?>" name="cod" class="form-control" disabled>
						                    </div>
						                </div>

						                <input type="hidden" name="modalidad" value="2">
						                <!--
										<div class="form-group row">
						                    <label class="col-sm-2 control-label">Modalidad de estudio</label>
						                    <div class="col-sm-4">
						                        <select class="form-control  select2" name="modalidad" required>
						                            <option value="">Seleccione una opción</option>
													<option value="1" <?php if ($datosEstudianteActual["mat_modalidad_estudio"] == 1) { echo "selected";} ?>>Virtual</option>
													<option value="2" <?php if ($datosEstudianteActual["mat_modalidad_estudio"] == 2) { echo "selected";} ?>>Presencial- alternancia</option>
						                            
						                        </select>
						                    </div>
						                </div>
						            -->

                                        <h3>Datos Acudiente</h3>
                                        <p style="color: navy;"><?= $frases[325][$datosUsuarioActual['uss_idioma']]; ?></p>

                                        <input type="hidden" name="idAcudiente" value="<?= $acudiente["uss_id"]; ?>">

                                        <div class="form-group row">
						                    <label class="col-sm-2 control-label">ID</label>
						                    <div class="col-sm-4">
						                        <input type="text" value="<?= $acudiente["uss_id"]; ?>" name="idA" class="form-control" readonly>
						                    </div>
						                </div>

                                        <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[326][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-4">
						                        <input type="text" value="<?= $acudiente["uss_usuario"]; ?>" name="documentoA" class="form-control" readonly>
						                    </div>
						                </div>

                                        <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[187][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $acudiente["uss_nombre"]; ?>" name="nombreA" class="form-control" readonly>
						                    </div>
						                </div>

                                        <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[181][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $acudiente["uss_email"]; ?>" name="emailA" class="form-control">
						                    </div>
						                </div>

                                        <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[188][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-4">
						                        <input type="text" value="<?= $acudiente["uss_celular"]; ?>" name="celularA" class="form-control">
						                    </div>
						                </div>

                                        <div class="form-group row">
						                    <label class="col-sm-2 control-label"><?= $frases[327][$datosUsuarioActual['uss_idioma']]; ?></label>
						                    <div class="col-sm-10">
						                        <input type="text" value="<?= $acudiente["uss_ocupacion"]; ?>" name="ocupacion" class="form-control">
						                    </div>
						                </div>





						                <?php if ($datosEstudianteActual["mat_iniciar_proceso"] == 1 AND $datosEstudianteActual["mat_actualizar_datos"] == '0') { ?>
						                    <input type="submit" class="btn btn-primary" value="<?= $frases[41][$datosUsuarioActual['uss_idioma']]; ?>">&nbsp;
						                <?php } ?>

						            </form>
						        </div>
						    </div>
						</div>

						<?php if($config['conf_mostrar_pasos_matricula'] == 1){ ?>
							<div class="col-sm-3">
								<?php include("../compartido/matricula-pasos.php"); ?>
							</div>
						<?php } ?>