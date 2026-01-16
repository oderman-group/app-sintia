<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0066';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
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
                                <div class="page-title"><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-12">
                                <?php include("../../config-general/mensajes-informativos.php"); ?>
                                <?php
                                    $tieneCategorias = true;
                                    $mensajeCategorias = "";
                                    $totalCategorias = 0;
                                    $totalFaltas = 0;
                                    $tieneFaltas = true;
                                    $mensajeFaltas = "";
                                    $yearsConDatos = [];
                                    $puedeImportar = (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0071']) && Modulos::validarSubRol(['DT0068']));
                                    try{
                                        $consultaCategorias = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM ".BD_DISCIPLINA.".disciplina_categorias
                                        WHERE dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}");
                                        if($consultaCategorias){
                                            $datosCategorias = mysqli_fetch_array($consultaCategorias, MYSQLI_BOTH);
                                            $totalCategorias = (int)($datosCategorias['total'] ?? 0);
                                            if($totalCategorias <= 0){
                                                $tieneCategorias = false;
                                                $mensajeCategorias = "Antes de crear faltas debes registrar al menos una categoría en el módulo de disciplina.";
                                            }
                                        }else{
                                            $tieneCategorias = false;
                                            $mensajeCategorias = "No fue posible verificar las categorías. Intenta nuevamente.";
                                        }
                                    } catch (Exception $e) {
                                        $tieneCategorias = false;
                                        $mensajeCategorias = "Error al consultar las categorías. Intenta nuevamente.";
                                        include("../compartido/error-catch-to-report.php");
                                    }

                                    // Contar faltas del año actual
                                    try{
                                        $consultaFaltas = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM ".BD_DISCIPLINA.".disciplina_faltas
                                        WHERE dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]}");
                                        if($consultaFaltas){
                                            $datosFaltas = mysqli_fetch_array($consultaFaltas, MYSQLI_BOTH);
                                            $totalFaltas = (int)($datosFaltas['total'] ?? 0);
                                            if($totalFaltas <= 0){
                                                $tieneFaltas = false;
                                                $mensajeFaltas = "No hay faltas registradas para el año actual.";
                                            }
                                        }else{
                                            $tieneFaltas = false;
                                            $mensajeFaltas = "No fue posible verificar las faltas. Intenta nuevamente.";
                                        }
                                    } catch (Exception $e) {
                                        $tieneFaltas = false;
                                        $mensajeFaltas = "Error al consultar las faltas. Intenta nuevamente.";
                                        include("../compartido/error-catch-to-report.php");
                                    }

                                    // Años disponibles con datos (categorías + faltas) para importar
                                    try {
                                        if (!empty($_SESSION["datosUnicosInstitucion"]["ins_years"])) {
                                            $yearArrayTmp = explode(",", $_SESSION["datosUnicosInstitucion"]["ins_years"]);
                                            $yearStartTmp = (int)trim($yearArrayTmp[0] ?? '0');
                                            $yearEndTmp   = (int)trim($yearArrayTmp[1] ?? '0');
                                            if ($yearStartTmp > 0 && $yearEndTmp > 0 && $yearEndTmp >= $yearStartTmp) {
                                                for ($y = $yearStartTmp; $y <= $yearEndTmp; $y++) {
                                                    if ($y == (int)$_SESSION["bd"]) continue;
                                                    $cCats = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM ".BD_DISCIPLINA.".disciplina_categorias
                                                        WHERE dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$y}");
                                                    $cFals = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM ".BD_DISCIPLINA.".disciplina_faltas
                                                        WHERE dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$y}");
                                                    if ($cCats && $cFals) {
                                                        $nCats = (int)(mysqli_fetch_array($cCats, MYSQLI_BOTH)['total'] ?? 0);
                                                        $nFals = (int)(mysqli_fetch_array($cFals, MYSQLI_BOTH)['total'] ?? 0);
                                                        if ($nCats > 0 && $nFals > 0) {
                                                            $yearsConDatos[] = [
                                                                'year' => $y,
                                                                'categorias' => $nCats,
                                                                'faltas' => $nFals
                                                            ];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } catch (Exception $e) {
                                        // Si falla, simplemente no mostramos opciones de importación
                                    }
                                ?>
                                <?php if(!$tieneCategorias){ ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Acción requerida:</strong> <?=$mensajeCategorias?><br>
                                        <span style="display:inline-block;margin-top:5px;">
                                            <a href="disciplina-categorias.php" class="btn btn-sm btn-primary">
                                                <i class="fa fa-plus"></i> Crear categorías
                                            </a>
                                            <?php if($puedeImportar && !empty($yearsConDatos)){ ?>
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalImportarFaltasYear" style="margin-left:6px;">
                                                    <i class="fa fa-download"></i> Importar desde otro año
                                                </button>
                                            <?php } ?>
                                        </span>
                                    </div>
                                <?php } ?>

                                <?php if($tieneCategorias && !$tieneFaltas){ ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Sin faltas:</strong> <?=$mensajeFaltas?>
                                        <?php if($puedeImportar && !empty($yearsConDatos)){ ?>
                                            <div style="margin-top:8px;">
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalImportarFaltasYear">
                                                    <i class="fa fa-download"></i> Importar desde otro año
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[248][$datosUsuarioActual['uss_idioma']];?></header>
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
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0068'])){?>
                                                            <?php if($tieneCategorias){ ?>
                                                                <a href="disciplina-faltas-agregar.php" id="addRow" class="btn deepPink-bgcolor">
                                                                    Agregar nuevo <i class="fa fa-plus"></i>
                                                                </a>
                                                            <?php }else{ ?>
                                                                <button type="button" class="btn deepPink-bgcolor" disabled title="Primero crea categorías en el módulo de disciplina">
                                                                    Agregar nuevo <i class="fa fa-plus"></i>
                                                                </button>
                                                            <?php } ?>
                                                        <?php }?>

                                                        <?php if($puedeImportar && !empty($yearsConDatos) && (!$tieneCategorias || !$tieneFaltas)){ ?>
                                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalImportarFaltasYear" style="margin-left:6px;">
                                                                <i class="fa fa-download"></i> Importar de otro año
                                                            </button>
                                                        <?php } ?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th>ID</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Falta</th>
														<th>Categoría</th>
                                                        <?php if(Modulos::validarPermisoEdicion()){?>
														    <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                    $filtro = '';
                                                    if(isset($_GET["cat"])){$filtro .=" AND dfal_id_categoria='".base64_decode($_GET["cat"])."'";}

                                                    try{
                                                        $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disciplina_faltas
                                                        INNER JOIN ".BD_DISCIPLINA.".disciplina_categorias ON dcat_id=dfal_id_categoria AND dcat_institucion={$config['conf_id_institucion']} AND dcat_year={$_SESSION["bd"]}
                                                        WHERE dfal_institucion={$config['conf_id_institucion']} AND dfal_year={$_SESSION["bd"]} $filtro");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['dfal_id'];?></td>
														<td><?=$resultado['dfal_codigo'];?></td>
														<td><?=$resultado['dfal_nombre'];?></td>
														<td><?=$resultado['dcat_nombre'];?></td>
														
                                                        <?php if(Modulos::validarPermisoEdicion()){?>
                                                            <td>
                                                                <?php if(Modulos::validarSubRol(['DT0067', 'DT0160'])) {?>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                    <?php if(Modulos::validarSubRol(['DT0067'])) {?>
                                                                        <li><a href="disciplina-faltas-editar.php?idR=<?=base64_encode($resultado['dfal_id']);?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                    <?php }?>
                                                                    
                                                                    <?php if(Modulos::validarSubRol(['DT0160'])) {?>
                                                                        <li><a href="javascript:void(0);"
                                                                        onClick="sweetConfirmacion('Alerta!','Desea eliminar este registro?','question','disciplina-faltas-eliminar.php?id=<?=base64_encode($resultado['dfal_id_nuevo']);?>')">Eliminar</a></li>
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

    <!-- Modal Importar categorías/faltas desde otro año -->
    <div class="modal fade" id="modalImportarFaltasYear" tabindex="-1" role="dialog" aria-labelledby="modalImportarFaltasYearLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportarFaltasYearLabel">
                        <i class="fa fa-download"></i> Importar categorías y faltas
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Esta acción copiará <strong>categorías y faltas</strong> desde un año anterior al año actual <strong>(<?=$_SESSION["bd"];?>)</strong>.
                        Solo se permite si el año actual está vacío para evitar duplicados.
                    </div>

                    <?php if(empty($yearsConDatos)){ ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            No se encontraron años con categorías y faltas disponibles para importar.
                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label>Selecciona el año origen</label>
                            <select class="form-control" id="importar_year_origen">
                                <option value="">Seleccione...</option>
                                <?php foreach($yearsConDatos as $yd){ ?>
                                    <option value="<?=$yd['year'];?>">
                                        <?=$yd['year'];?> (<?=$yd['categorias'];?> categorías, <?=$yd['faltas'];?> faltas)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div id="importar_faltas_msg" class="alert alert-danger" style="display:none;"></div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnImportarFaltasYear" <?= empty($yearsConDatos) ? 'disabled' : '' ?>>
                        <i class="fa fa-download"></i> Importar
                    </button>
                </div>
            </div>
        </div>
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

    <script>
        $(document).ready(function(){
            $('#btnImportarFaltasYear').on('click', function(){
                $('#importar_faltas_msg').hide().text('');
                var yearOrigen = $('#importar_year_origen').val();
                if(!yearOrigen){
                    $('#importar_faltas_msg').show().text('Debes seleccionar un año origen.');
                    return;
                }

                var btn = $(this);
                var htmlOriginal = btn.html();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Importar categorías y faltas?',
                        html: 'Se copiarán categorías y faltas del año <b>' + yearOrigen + '</b> al año actual <b><?=$_SESSION["bd"];?></b>.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, importar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (!result.isConfirmed) return;
                        ejecutarImportacion(yearOrigen, btn, htmlOriginal);
                    });
                } else {
                    if (confirm('¿Deseas importar categorías y faltas del año ' + yearOrigen + ' al año actual <?=$_SESSION["bd"];?>?')) {
                        ejecutarImportacion(yearOrigen, btn, htmlOriginal);
                    }
                }
            });

            function ejecutarImportacion(yearOrigen, btn, htmlOriginal){
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importando...');
                $.ajax({
                    url: 'ajax-importar-disciplina-faltas-year.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { year_origen: yearOrigen },
                    success: function(resp){
                        btn.prop('disabled', false).html(htmlOriginal);
                        if(resp && resp.success){
                            $('#modalImportarFaltasYear').modal('hide');
                            $.toast({
                                heading: 'Éxito',
                                text: resp.message || 'Importación completada.',
                                position: 'top-right',
                                loaderBg: '#26c281',
                                icon: 'success',
                                hideAfter: 3500
                            });
                            setTimeout(function(){ window.location.reload(); }, 800);
                        } else {
                            var msg = (resp && resp.message) ? resp.message : 'No fue posible importar.';
                            $('#importar_faltas_msg').show().text(msg);
                        }
                    },
                    error: function(xhr){
                        btn.prop('disabled', false).html(htmlOriginal);
                        $('#importar_faltas_msg').show().text('Error de conexión al servidor.');
                    }
                });
            }
        });
    </script>
</body>

</html>