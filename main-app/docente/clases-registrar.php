<?php
include("session.php");
$idPaginaInterna = 'DC0071';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

$datosConsulta = Clases::traerDatosClases($conexion, $config, $idR);
?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<style>
/* ============================================
   ESTILOS PARA AUSENCIAS JUSTIFICADAS
   ============================================ */
.checkbox-justificada {
    width: 24px;
    height: 24px;
    cursor: pointer;
    accent-color: #27ae60;
    transition: all 0.3s ease;
}

.checkbox-justificada:checked {
    transform: scale(1.1);
}

.badge-justificada {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-block;
    margin-left: 8px;
}

.badge-si {
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(39, 174, 96, 0.3);
}

.badge-no {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    color: white;
}

.columna-acumulado {
    text-align: center;
    font-weight: 700;
    font-size: 18px;
    padding: 10px !important;
}

.acumulado-bajo {
    color: #3498db;
}

.acumulado-medio {
    color: #f39c12;
}

.acumulado-alto {
    color: #e74c3c;
    animation: pulso 1.5s ease-in-out infinite;
}

@keyframes pulso {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.8;
    }
}

.columna-justificada {
    text-align: center;
    padding: 15px !important;
}

.checkbox-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.input-ausencias {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 16px;
    text-align: center;
    transition: all 0.3s ease;
    width: 80px;
}

.input-ausencias:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.input-ausencias:hover {
    border-color: #bdc3c7;
}
</style>

<script type="application/javascript">
// ============================================
// FUNCIÓN MEJORADA PARA REGISTRAR AUSENCIAS
// ============================================
function notasAusencias(enviada) {
    var codNota = '<?=$idR;?>';
    var nota = enviada.value;
    var codEst = enviada.id;
    var nombreEst = enviada.alt;
    var operacion = enviada.title;
    
    console.log('💾 Guardando ausencia:', { codEst, nota, nombreEst });
    
    $('#respRA').empty().hide().html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Guardando información, espere por favor...</div>').show(1);
    
    datos = "nota=" + (nota) +
            "&codNota=" + (codNota) +
            "&operacion=" + (operacion) +
            "&nombreEst=" + (nombreEst) +
            "&codEst=" + (codEst);
    
    $.ajax({
        type: "POST",
        url: "ajax-ausencias-registrar.php",
        data: datos,
        success: function(data) {
            $('#respRA').empty().hide().html(data).show(1);
            
            // ✅ Recargar la página después de un breve delay para actualizar acumulados
            setTimeout(function() {
                location.reload();
            }, 1500);
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al guardar ausencia:', error);
            $('#respRA').empty().hide().html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Error al guardar. Por favor intenta de nuevo.</div>').show(1);
        }
    });
}

// ============================================
// FUNCIÓN PARA CAMBIAR JUSTIFICACIÓN
// ============================================
function cambiarJustificacion(checkbox) {
    var idAusencia = checkbox.getAttribute('data-id-ausencia');
    var idEstudiante = checkbox.getAttribute('data-id-estudiante');
    var justificada = checkbox.checked ? 1 : 0;
    
    if (!idAusencia) {
        console.warn('⚠️ No hay ID de ausencia para justificar');
        checkbox.checked = !checkbox.checked; // Revertir
        return;
    }
    
    console.log('🔄 Cambiando justificación:', { idAusencia, justificada });
    
    // Deshabilitar checkbox mientras se procesa
    checkbox.disabled = true;
    
    $.ajax({
        type: "POST",
        url: "ajax-ausencias-justificar.php",
        data: {
            idAusencia: idAusencia,
            justificada: justificada
        },
        dataType: 'json',
        success: function(response) {
            checkbox.disabled = false;
            
            if (response.success) {
                // ✅ Actualizar badge visual
                var label = checkbox.nextElementSibling;
                if (label && label.tagName === 'LABEL') {
                    if (justificada == 1) {
                        label.innerHTML = '<span class="badge badge-si badge-justificada"><i class="fa fa-check"></i> Sí</span>';
                        checkbox.style.accentColor = '#27ae60';
                    } else {
                        label.innerHTML = '<span class="badge badge-no badge-justificada"><i class="fa fa-times"></i> No</span>';
                        checkbox.style.accentColor = '#95a5a6';
                    }
                }
                
                // ✅ Toast de confirmación
                $.toast({
                    heading: justificada == 1 ? 'Ausencia Justificada' : 'Ausencia No Justificada',
                    text: response.message,
                    position: 'top-right',
                    showHideTransition: 'slide',
                    loaderBg: justificada == 1 ? '#26c281' : '#95a5a6',
                    icon: justificada == 1 ? 'success' : 'info',
                    hideAfter: 3000,
                    stack: 6
                });
                
                console.log('✅ Justificación actualizada correctamente');
            } else {
                checkbox.checked = !checkbox.checked; // Revertir
                $.toast({
                    heading: 'Error',
                    text: response.message || 'Error al actualizar la justificación',
                    position: 'top-right',
                    showHideTransition: 'slide',
                    loaderBg: '#f4516c',
                    icon: 'error',
                    hideAfter: 5000,
                    stack: 6
                });
            }
        },
        error: function(xhr, status, error) {
            checkbox.disabled = false;
            checkbox.checked = !checkbox.checked; // Revertir
            
            console.error('❌ Error AJAX:', error);
            $.toast({
                heading: 'Error de Conexión',
                text: 'No se pudo actualizar la justificación. Por favor intenta de nuevo.',
                position: 'top-right',
                showHideTransition: 'slide',
                loaderBg: '#f4516c',
                icon: 'error',
                hideAfter: 5000,
                stack: 6
            });
        }
    });
}

// Mantener función original para compatibilidad
function notas(enviada) {
    notasAusencias(enviada);
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
                                <div class="page-title"><?=$datosConsulta['cls_tema'];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="clases.php"><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$datosConsulta['cls_tema'];?></li>
                            </ol>
                        </div>
                    </div>
					<?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
					<!-- ✅ Barra superior con información de carga y filtros -->
					<?php include("includes/barra-superior-informacion-actual.php"); ?>
					
					<!-- ✅ Dropdown para cambiar de clase -->
					<div class="row mb-3">
						<div class="col-md-12">
							<div class="btn-group" role="group">
								<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fa fa-book"></i> <?=strtoupper($frases[7][$datosUsuarioActual['uss_idioma']]);?> - <?=$datosConsulta['cls_tema'];?>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li class="dropdown-header">
										<strong><i class="fa fa-info-circle"></i> Cambiar de clase</strong>
										<p style="margin: 5px 0; font-size: 12px; color: #666;">Selecciona otra clase para registrar asistencia</p>
									</li>
									<li role="separator" class="divider"></li>
									<?php
									$registrosEnComun = Clases::listarClasesDiferentes($conexion, $config, $idR, $cargaConsultaActual, $periodoConsultaActual);
									$numClases = mysqli_num_rows($registrosEnComun);
									$hayOtrasClases = false;
									while($regComun = mysqli_fetch_array($registrosEnComun, MYSQLI_BOTH)){
										$hayOtrasClases = true;
										$activo = ($regComun['cls_id'] == $idR) ? 'active' : '';
									?>
										<li class="<?=$activo;?>">
											<a href="<?=$_SERVER['PHP_SELF'];?>?idR=<?=base64_encode($regComun['cls_id']);?>">
												<?php if($activo): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-book"></i><?php endif; ?>
												<?=$regComun['cls_tema'];?>
											</a>
										</li>
									<?php }?>
									<?php if(!$hayOtrasClases): ?>
										<li><a href="javascript:void(0);" style="color: #999; cursor: default;"><i class="fa fa-info-circle"></i> No hay otras clases disponibles</a></li>
									<?php endif; ?>
								</ul>
							</div>
						</div>
					</div>
					
                    <div class="row">
                        <div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
										
										
									
										
                                        <div class="card-body">
											
										<span style="color: blue; font-size: 15px;" id="respRA"></span>
											
											
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[30][$datosUsuarioActual['uss_idioma']];?></th>
														<th style="text-align: center;"><i class="fa fa-check-circle"></i> Justificada</th>
														<th style="text-align: center;"><i class="fa fa-chart-bar"></i> Acumulado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $contReg = 1;
													 $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													 $colorNota = "black";
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $notas = [];
														 if($datosConsulta['cls_registrada']==1){
															 //Consulta de ausencias si ya la tienen puestas.
                                                             $notas = Ausencias::traerAusenciasClaseEstudiante($config, $idR, $resultado['mat_id']);
														 }
														 
														 // ✅ Obtener ausencias acumuladas para este estudiante en esta carga
														 $acumuladoAusencias = 0;
														 $idCargaParaAcumulado = !empty($cargaConsultaActual) ? $cargaConsultaActual : (isset($datosConsulta['cls_id_carga']) ? $datosConsulta['cls_id_carga'] : '');
														 if (!empty($idCargaParaAcumulado)) {
															 $acumuladoAusencias = Ausencias::traerAusenciasAcumuladasEstudiante($config, $idCargaParaAcumulado, $resultado['mat_id']);
														 }
														 
														 // Determinar color según acumulado
														 $colorAcumulado = '#3498db'; // Azul (bajo)
														 $claseAcumulado = 'acumulado-bajo';
														 if ($acumuladoAusencias >= 10) {
															 $colorAcumulado = '#e74c3c'; // Rojo (alto)
															 $claseAcumulado = 'acumulado-alto';
														 } else if ($acumuladoAusencias >= 5) {
															 $colorAcumulado = '#f39c12'; // Naranja (medio)
															 $claseAcumulado = 'acumulado-medio';
														 }
													 ?>
                                                    
													<tr>
                                                        <td><?=$contReg;?></td>
														<td>
															<?php
															// ✅ Validar si existe la foto, sino usar imagen por defecto
															$fotoEstudiante = '../files/fotos/default.png';
															if(!empty($resultado['uss_foto']) && file_exists(ROOT_PATH.'/main-app/files/fotos/'.$resultado['uss_foto'])){
																$fotoEstudiante = '../files/fotos/'.$resultado['uss_foto'];
															}
															?>
															<img src="<?=$fotoEstudiante;?>" width="50" height="50" style="border-radius: 50%; object-fit: cover; border: 2px solid #e0e0e0;" alt="Foto del estudiante">
															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
														</td>
														<td>
															<input 
																type="number" 
																class="input-ausencias"
																size="5" 
																maxlength="3" 
																value="<?=!empty($notas['aus_ausencias']) ? $notas['aus_ausencias'] : '';?>" 
																name="N<?=$contReg;?>" 
																id="<?=$resultado['mat_id'];?>" 
																alt="<?=$resultado['mat_nombres'];?>" 
																title="1" 
																onChange="notasAusencias(this)" 
																tabindex="<?=$contReg;?>"
																min="0"
															>
															<?php if(!empty($notas['aus_ausencias'])){?>
															<a href="#" name="clases-ausencia-eliminar.php?id=<?=base64_encode($notas['aus_id']);?>" onClick="deseaEliminar(this)" style="margin-left: 8px; color: #e74c3c; font-weight: bold;">×</a>
															<?php }?>
														</td>
														<td class="columna-justificada">
															<div class="checkbox-container">
																<input 
																	type="checkbox" 
																	class="checkbox-justificada" 
																	id="justificada_<?=$resultado['mat_id'];?>" 
																	data-id-estudiante="<?=$resultado['mat_id'];?>" 
																	data-id-ausencia="<?=!empty($notas['aus_id']) ? $notas['aus_id'] : '';?>" 
																	<?=(!empty($notas['aus_justificadas']) && $notas['aus_justificadas'] == 1) ? 'checked' : '';?> 
																	<?=empty($notas['aus_ausencias']) ? 'disabled' : '';?> 
																	onChange="cambiarJustificacion(this)"
																	title="<?=empty($notas['aus_ausencias']) ? 'Registra ausencias primero' : ($notas['aus_justificadas'] == 1 ? 'Ausencia justificada' : 'Marcar como justificada');?>"
																>
																<label for="justificada_<?=$resultado['mat_id'];?>" style="margin: 0; cursor: pointer;">
																	<?php if(!empty($notas['aus_justificadas']) && $notas['aus_justificadas'] == 1): ?>
																		<span class="badge badge-si badge-justificada"><i class="fa fa-check"></i> Sí</span>
																	<?php else: ?>
																		<span class="badge badge-no badge-justificada"><i class="fa fa-times"></i> No</span>
																	<?php endif; ?>
																</label>
															</div>
														</td>
														<td class="columna-acumulado <?=$claseAcumulado;?>" style="color: <?=$colorAcumulado;?>">
															<span style="font-size: 24px; font-weight: 700;">
																<?=$acumuladoAusencias;?>
															</span>
															<br>
															<small style="color: #7f8c8d; font-size: 11px;">ausencia(s)</small>
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
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!-- end js include path -->
</body>

</html>