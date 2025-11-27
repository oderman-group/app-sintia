<?php require_once("../class/Estudiantes.php");

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

// Contar registros disponibles
$cantidadEstudiantes = 0;
$cantidadFaltas = 0;
$cantidadUsuarios = 0;

try {
    // Contar estudiantes
    $consultaEstudiantes = Estudiantes::listarEstudiantesParaDocentes('');
    if ($consultaEstudiantes) {
        $cantidadEstudiantes = mysqli_num_rows($consultaEstudiantes);
        mysqli_data_seek($consultaEstudiantes, 0); // Resetear el puntero
    }
    
    // Contar faltas
    $consultaFaltas = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_DISCIPLINA.".disciplina_faltas 
    INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
    WHERE dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}");
    if ($consultaFaltas) {
        $resultadoFaltas = mysqli_fetch_array($consultaFaltas, MYSQLI_BOTH);
        $cantidadFaltas = $resultadoFaltas['total'] ?? 0;
    }
    
    // Contar usuarios (solo si es directivo)
    if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) {
        $consultaUsuarios = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND (uss_tipo = ".TIPO_DOCENTE." OR uss_tipo= ".TIPO_DIRECTIVO.")");
        if ($consultaUsuarios) {
            $cantidadUsuarios = mysqli_num_rows($consultaUsuarios);
            mysqli_data_seek($consultaUsuarios, 0); // Resetear el puntero
        }
    }
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

$puedeCrearReporte = true;
$mensajesError = [];

if ($cantidadEstudiantes == 0) {
    $puedeCrearReporte = false;
    $mensajesError[] = "No hay estudiantes disponibles para seleccionar.";
}

if ($cantidadFaltas == 0) {
    $puedeCrearReporte = false;
    $mensajesError[] = "No hay faltas disciplinarias registradas en el sistema.";
}

if (($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) && $cantidadUsuarios == 0) {
    $puedeCrearReporte = false;
    $mensajesError[] = "No hay usuarios (docentes/directivos) disponibles para asignar el reporte.";
}
?>

					<div class="row">
                        <div class="col-sm-12">
                            <?php if (!$puedeCrearReporte) { ?>
                                <div class="alert alert-warning">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <i class="fa fa-exclamation-triangle"></i> <strong>No se puede crear el reporte</strong>
                                    <hr>
                                    <p>Se han detectado los siguientes problemas:</p>
                                    <ul style="margin-bottom: 0;">
                                        <?php foreach ($mensajesError as $mensaje) { ?>
                                            <li><?=$mensaje?></li>
                                        <?php } ?>
                                    </ul>
                                    <hr>
                                    <p style="margin-bottom: 0;">
                                        <strong>Por favor, asegúrate de:</strong><br>
                                        - Tener estudiantes matriculados en el sistema<br>
                                        - Tener faltas disciplinarias configuradas<br>
                                        <?php if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV) { ?>
                                        - Tener usuarios (docentes/directivos) registrados
                                        <?php } ?>
                                    </p>
                                </div>
                            <?php } ?>
                        
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[96][$datosUsuarioActual['uss_idioma']];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/reportes-guardar.php" method="post" enctype="multipart/form-data" id="formReporte">

										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">  
                                                <input type="date" class="form-control" name="fecha" required value="<?=date("Y-m-d");?>" <?=$disabledPermiso;?> <?=!$puedeCrearReporte ? 'disabled' : ''?>>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[55][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <?php
												$datosConsultaEstudiantes = Estudiantes::listarEstudiantesParaDocentes('');
												?>
                                                <select class="form-control  select2" name="estudiante" required <?=$disabledPermiso;?> <?=!$puedeCrearReporte ? 'disabled' : ''?>>
                                                    <option value="">Seleccione una opción</option>
													<?php
													if ($datosConsultaEstudiantes) {
														while($datos = mysqli_fetch_array($datosConsultaEstudiantes, MYSQLI_BOTH)){
														?>
															<option value="<?=$datos['mat_id'];?>"><?="[".$datos['mat_id']."] ".Estudiantes::NombreCompletoDelEstudiante($datos);?></option>
														<?php 
														}
													}
													?>
                                                </select>
                                                <?php if ($cantidadEstudiantes == 0) { ?>
                                                    <small class="text-danger"><i class="fa fa-warning"></i> No hay estudiantes disponibles</small>
                                                <?php } ?>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">
                                                <select id="multiple" name="faltas[]" class="form-control select2-multiple" multiple <?=$disabledPermiso;?> required <?=!$puedeCrearReporte ? 'disabled' : ''?>>
												<?php
												$consultaFaltasSelect = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_faltas 
												INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
												WHERE dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}
												");
												if ($consultaFaltasSelect) {
													while($datos = mysqli_fetch_array($consultaFaltasSelect, MYSQLI_BOTH)){
													?>	
													  <option value="<?=$datos['dfal_id'];?>"><?=$datos['dfal_codigo'].". ".$datos['dfal_nombre'];?></option>	
													<?php 
													}
												}
												?>	
                                                </select>
                                                <?php if ($cantidadFaltas == 0) { ?>
                                                    <small class="text-danger"><i class="fa fa-warning"></i> No hay faltas disciplinarias registradas</small>
                                                <?php } ?>
                                            </div>
                                        </div>
										
										<?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo'] == TIPO_DEV){?>
											<div class="form-group row">
												<label class="col-sm-2 control-label"><?=$frases[75][$datosUsuarioActual['uss_idioma']];?></label>
												<div class="col-sm-10">
													<?php
													$consultaUsuariosSelect = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND (uss_tipo = ".TIPO_DOCENTE." OR uss_tipo= ".TIPO_DIRECTIVO.")
													ORDER BY uss_tipo, uss_nombre");
													?>
													<select class="form-control  select2" name="usuario" required <?=$disabledPermiso;?> <?=!$puedeCrearReporte ? 'disabled' : ''?>>
														<option value="">Seleccione una opción</option>
														<?php
														if ($consultaUsuariosSelect) {
															while($datos = mysqli_fetch_array($consultaUsuariosSelect, MYSQLI_BOTH)){
															?>
																<option value="<?=$datos['uss_id'];?>"><?=UsuariosPadre::nombreCompletoDelUsuario($datos);?></option>
															<?php 
															}
														}
														?>
													</select>
													<?php if ($cantidadUsuarios == 0) { ?>
														<small class="text-danger"><i class="fa fa-warning"></i> No hay usuarios disponibles</small>
													<?php } ?>
												</div>
											</div>
										<?php }else{?>
											<input type="hidden" name="usuario" value="<?=$_SESSION["id"];?>">
										<?php }?>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="contenido" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?> <?=!$puedeCrearReporte ? 'disabled' : ''?>></textarea>
                                            </div>
                                        </div>
										
										<a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

										<?php if(Modulos::validarPermisoEdicion()){?>
											<button type="submit" class="btn  btn-info" <?=!$puedeCrearReporte ? 'disabled' : ''?>>
												<i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
											</button>
										<?php }?>
                                        
                                        <?php if (!$puedeCrearReporte) { ?>
                                            <div class="alert alert-info" style="margin-top: 15px;">
                                                <i class="fa fa-info-circle"></i> El formulario está deshabilitado porque faltan datos requeridos. Por favor, revisa la alerta en la parte superior.
                                            </div>
                                        <?php } ?>

                                    </form>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-sm-3">
                            <?php include("../compartido/publicidad-lateral.php");?>
                        </div>
						
                    </div>