<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0128';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

$parametrosObligatorios = ["id"];

Utilidades::validarParametros($_GET, $parametrosObligatorios);

require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (empty($_GET['id'])) {
        echo '<script type="text/javascript">alert("ID de transacción no proporcionado."); window.location.href="movimientos.php";</script>';
        exit();
    }
    
    $idMovimiento = base64_decode($_GET['id'], true);
    
    // Validar que el ID no esté vacío después de decodificar
    if ($idMovimiento === false || empty($idMovimiento)) {
        // Intentar decodificar sin strict mode si falla
        $idMovimiento = base64_decode($_GET['id']);
        if (empty($idMovimiento)) {
            echo '<script type="text/javascript">alert("ID de transacción inválido. ID recibido: '.htmlspecialchars($_GET['id']).'"); window.location.href="movimientos.php";</script>';
            exit();
        }
    }
    
    // fcu_id es ahora el ID principal (AUTO_INCREMENT INT UNSIGNED)
    $sql = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_id=? AND institucion=? AND year=?";
    $stmt = $conexionPDO->prepare($sql);
    $idMovimientoInt = (int)$idMovimiento;
    $stmt->bindParam(1, $idMovimientoInt, PDO::PARAM_INT);
    $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
    $datosMovimiento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Validar que se encontró el registro
    if ($datosMovimiento === false || !is_array($datosMovimiento) || empty($datosMovimiento['fcu_id'])) {
        echo '<script type="text/javascript">alert("No se encontró la transacción solicitada.\\n\\nID buscado: '.htmlspecialchars($idMovimiento).'\\n\\nPor favor verifica que la transacción existe en la base de datos."); window.location.href="movimientos.php";</script>';
        exit();
    }
    
    // En este punto, $datosMovimiento debería ser válido (ya validado arriba)
    // Solo verificamos una vez más por seguridad
    if (!isset($datosMovimiento['fcu_id']) || empty($datosMovimiento['fcu_id'])) {
        echo '<script type="text/javascript">alert("Error inesperado: Los datos de la transacción no están completos."); window.location.href="movimientos.php";</script>';
        exit();
    }
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
    echo '<script type="text/javascript">alert("Error al cargar la transacción: '.htmlspecialchars($e->getMessage()).'"); window.location.href="movimientos.php";</script>';
    exit();
}

// Verificar una vez más que $datosMovimiento existe antes de calcular abonos
if (!isset($datosMovimiento) || !is_array($datosMovimiento) || empty($datosMovimiento['fcu_id'])) {
    echo '<script type="text/javascript">alert("Error: No se pudieron cargar los datos de la transacción después de la consulta."); window.location.href="movimientos.php";</script>';
    exit();
}

// Calcular abonos usando fcu_id (ahora es el ID principal)
$abonos = Movimientos::calcularTotalAbonado($conexion, $config, $datosMovimiento['fcu_id']);

// Asegurar que $abonos sea un número válido (manejar null, false, o valores no numéricos)
if ($abonos === null || $abonos === false || !is_numeric($abonos)) {
    $abonos = 0;
} else {
    $abonos = floatval($abonos);
}

// Determinar qué campos están deshabilitados
// Solo se puede editar si está en estado EN_PROCESO y no tiene abonos
$estadoActual = $datosMovimiento['fcu_status'] ?? '';
$puedeEditar = false;

// Se puede editar solo si:
// 1. Tiene permisos de edición
// 2. No está anulada
// 3. Está en estado EN_PROCESO
// 4. No tiene abonos
if(Modulos::validarPermisoEdicion() 
    && (!isset($datosMovimiento['fcu_anulado']) || $datosMovimiento['fcu_anulado']==0) 
    && $estadoActual == EN_PROCESO 
    && $abonos == 0){
    $puedeEditar = true;
}

// Establecer disabled según si puede editar
$disabledPermiso = $puedeEditar ? "" : "disabled";

// Contar items de la factura para validar antes de confirmar
$numItems = 0;
if ($puedeEditar) {
    try {
        $consultaItems = Movimientos::listarItemsTransaction($conexion, $config, (string)$datosMovimiento['fcu_id'], TIPO_FACTURA);
        if ($consultaItems) {
            $numItems = mysqli_num_rows($consultaItems);
        }
    } catch (Exception $e) {
        $numItems = 0;
    }
}
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
    <!-- Movimientos Mejorado CSS -->
    <link href="../css/movimientos-mejorado.css" rel="stylesheet" type="text/css" />
    <style>
    /* Fijar ancho de columna de impuesto */
    #tablaItems th:nth-child(5),
    #tablaItems td:nth-child(5) {
        width: 150px !important;
        max-width: 150px !important;
        min-width: 150px !important;
    }
    
    #tablaItems td:nth-child(5) .select2-container {
        width: 100% !important;
        max-width: 150px !important;
    }
    
    #tablaItems td:nth-child(5) .select2-selection {
        width: 100% !important;
        max-width: 150px !important;
    }
    
    .invoice-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }
    .invoice-info-card {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .invoice-info-card h5 {
        margin: 0 0 10px 0;
        color: #667eea;
        font-weight: 600;
    }
    .invoice-info-card p {
        margin: 5px 0;
        color: #555;
    }
    .items-table-wrapper {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .total-summary {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        border: 2px solid #e9ecef;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .total-row:last-child {
        border-bottom: none;
        font-size: 18px;
        font-weight: bold;
        color: #667eea;
        margin-top: 10px;
        padding-top: 15px;
        border-top: 2px solid #667eea;
    }
    .btn-add-item {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-add-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .form-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    </style>
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
                                <div class="page-title">Editar Movimientos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="movimientos.php" onClick="deseaRegresar(this)"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Movimientos</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-12">
                                <?php 
                                    include("../../config-general/mensajes-informativos.php");
                                    
                                    // Usar $datosMovimiento en lugar de $datosMovimiento para evitar conflictos con includes
                                    // Calcular totales
                                    $fcuId = $datosMovimiento['fcu_id'];
                                    $fcuValor = isset($datosMovimiento['fcu_valor']) ? $datosMovimiento['fcu_valor'] : 0;
                                    $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $fcuId, $fcuValor);
                                    
                                    // Determinar texto y color del estado
                                    $estadoFactura = $datosMovimiento['fcu_status'] ?? '';
                                    if ($estadoFactura == COBRADA) {
                                        $estadoTexto = 'Cobrada';
                                        $estadoColor = '#00c292';
                                    } elseif ($estadoFactura == EN_PROCESO) {
                                        $estadoTexto = 'En Proceso';
                                        $estadoColor = '#3498db';
                                    } elseif ($estadoFactura == ANULADA) {
                                        $estadoTexto = 'Anulada';
                                        $estadoColor = '#e74c3c';
                                    } else {
                                        $estadoTexto = 'Por Cobrar';
                                        $estadoColor = '#ffc107';
                                    }
                                    $tipoTexto = (!empty($datosMovimiento['fcu_tipo']) && $datosMovimiento['fcu_tipo'] == 1) ? 'Fact. Venta' : 'Fact. Compra';
                                    $displayConsecutivoFactura = (isset($datosMovimiento['fcu_consecutivo']) && $datosMovimiento['fcu_consecutivo'] !== '' && $datosMovimiento['fcu_consecutivo'] !== null)
                                        ? (int)$datosMovimiento['fcu_consecutivo'] : ($datosMovimiento['fcu_id'] ?? '');
                                    
                                    // Pasar $datosMovimiento como $resultado al include para compatibilidad
                                    $resultado = $datosMovimiento;
                                    // Asegurar que el estado esté disponible en $resultado para el include
                                    $resultado['fcu_status'] = $datosMovimiento['fcu_status'] ?? '';
                                    include("includes/barra-superior-movimientos-financieros-editar.php");
                                ?>
								
								<!-- Header de la Factura -->
								<div class="invoice-header">
									<div class="row">
										<div class="col-md-6">
											<h2 style="margin: 0; color: white;">
												<i class="fa fa-file-text-o"></i> <?=$frases[95][$datosUsuarioActual['uss_idioma']];?>
											</h2>
											<p style="margin: 10px 0 0 0; opacity: 0.9;">
												# <?= $displayConsecutivoFactura; ?> | <?=$tipoTexto;?>
											</p>
										</div>
										<div class="col-md-6 text-right">
											<div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 6px; display: inline-block;">
												<strong style="display: block; font-size: 14px; opacity: 0.9;">Estado</strong>
												<span style="font-size: 18px; font-weight: bold;"><?=$estadoTexto;?></span>
											</div>
										</div>
									</div>
								</div>

								<!-- Información de la Factura -->
								<div class="row" style="margin-top: 20px;">
									<div class="col-md-4">
										<div class="invoice-info-card">
											<h5><i class="fa fa-user"></i> Cliente</h5>
											<p style="font-weight: 600; margin-top: 10px;">
												<?php
												if (!empty($datosMovimiento['fcu_usuario'])) {
													$datosUsuario = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$datosMovimiento['fcu_usuario']."'");
													$usuarioFactura = mysqli_fetch_array($datosUsuario, MYSQLI_BOTH);
													echo UsuariosPadre::nombreCompletoDelUsuario($usuarioFactura);
												} else {
													echo 'N/A';
												}
												?>
											</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="invoice-info-card">
											<h5><i class="fa fa-calendar"></i> Fecha</h5>
											<p style="font-weight: 600; margin-top: 10px;">
												<?=!empty($datosMovimiento['fcu_fecha']) ? date('d/m/Y', strtotime($datosMovimiento['fcu_fecha'])) : 'N/A';?>
											</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="invoice-info-card">
											<h5><i class="fa fa-money"></i> Total Abonado</h5>
											<p style="font-weight: 600; margin-top: 10px; color: #00c292; font-size: 18px;">
												$<?=number_format((float)($abonos ?? 0), 0, ",", ".");?>
											</p>
										</div>
									</div>
								</div>
								
								<?php if(!$puedeEditar): ?>
								<div class="alert alert-warning" style="margin-top: 20px;">
									<i class="fa fa-exclamation-triangle"></i> 
									<strong>Información:</strong> 
									<?php 
									if($estadoActual != EN_PROCESO) {
										echo "Esta factura ya está confirmada (Estado: ".$estadoActual."). No se permite editar ningún dato.";
									} elseif($abonos > 0) {
										echo "Esta factura tiene abonos asociados. No se permite editar ningún dato.";
									} else {
										echo "No tiene permisos para editar esta factura.";
									}
									?>
								</div>
								<?php endif; ?>

								<!-- Formulario Principal -->
								<div class="form-section">
									<div class="form-section-title">
										<i class="fa fa-edit"></i> Información de la Transacción
									</div>

									<form name="formularioGuardar" action="movimientos-actualizar.php" method="post">
										<input type="hidden" value="<?=$datosMovimiento['fcu_id'] ?? '';?>" name="idU" id="idTransaction">
										<input type="hidden" value="<?=TIPO_FACTURA;?>" name="typeTransaction" id="typeTransaction">
										
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Número de Factura</label>
													<input type="text" name="idNuevo" class="form-control" value="<?= $displayConsecutivoFactura; ?>" disabled style="background: #f5f5f5;">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Usuario <span style="color: red;">*</span></label>
													<select class="form-control select2" id="select_usuario" name="usuario" required <?=$disabledPermiso;?>>
														<?php
														// Precargar el usuario actual como opción inicial
														if (!empty($datosMovimiento['fcu_usuario'])) {
															$datosConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$datosMovimiento['fcu_usuario']."'");
															$usuarioActual = null;
															if ($datosConsulta) {
																$usuarioActual = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH);
																if ($usuarioActual) {
																	// Asegurar que todos los campos estén presentes y limpios
																	$usuarioLimpio = [
																		'uss_id' => $usuarioActual['uss_id'] ?? '',
																		'uss_nombre' => isset($usuarioActual['uss_nombre']) ? trim($usuarioActual['uss_nombre']) : '',
																		'uss_nombre2' => isset($usuarioActual['uss_nombre2']) ? trim($usuarioActual['uss_nombre2']) : '',
																		'uss_apellido1' => isset($usuarioActual['uss_apellido1']) ? trim($usuarioActual['uss_apellido1']) : '',
																		'uss_apellido2' => isset($usuarioActual['uss_apellido2']) ? trim($usuarioActual['uss_apellido2']) : '',
																	];
																	$nombreUsuario = UsuariosPadre::nombreCompletoDelUsuario($usuarioLimpio)." - ".(isset($usuarioActual['pes_nombre']) ? trim($usuarioActual['pes_nombre']) : 'N/A');
														?>
															<option value="<?=$usuarioActual['uss_id'];?>" selected><?=$nombreUsuario;?></option>
														<?php
																}
															}
														}
														?>
													</select>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Fecha del documento <span style="color: red;">*</span></label>
													<input type="date" name="fecha" class="form-control" required 
														value="<?=$datosMovimiento['fcu_fecha'] ?? '';?>" 
														max="<?=date('Y-m-d');?>" 
														min="<?=date('Y-m-d', strtotime('-1 year'));?>"
														<?=$disabledPermiso;?>>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Tipo de movimiento <span style="color: red;">*</span></label>
													<select class="form-control select2" name="tipo" required <?=$disabledPermiso;?>>
														<option value="">Seleccione una opción</option>
														<option value="1" <?php if(isset($datosMovimiento['fcu_tipo']) && $datosMovimiento['fcu_tipo']==1){ echo "selected";}?>>Fact. Venta</option>
														<option value="2" <?php if(isset($datosMovimiento['fcu_tipo']) && $datosMovimiento['fcu_tipo']==2){ echo "selected";}?>>Fact. Compra</option>
													</select>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Descripción general <span style="color: red;">*</span></label>
													<input type="text" name="detalle" class="form-control" value="<?=htmlspecialchars($datosMovimiento['fcu_detalle'] ?? '');?>" required <?=$disabledPermiso;?>>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Valor adicional</label>
													<input type="number" min="0" id="vlrAdicional" name="valor" class="form-control" value="0" required disabled data-vlr-adicional-anterior="0">
												</div>
											</div>
										</div>

										<!-- NOTA: Los campos de Medio de pago y Cuenta Bancaria fueron eliminados según el plan.
										     El medio de pago solo se registra en el abono asociado, no en la factura. -->
										
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Estado</label>
													<select class="form-control select2" disabled>
														<option value="">Seleccione una opción</option>
														<option value="<?=EN_PROCESO?>" <?php if(isset($datosMovimiento['fcu_status']) && $datosMovimiento['fcu_status']==EN_PROCESO){ echo "selected";}?>>En Proceso</option>
														<option value="<?=POR_COBRAR?>" <?php if(isset($datosMovimiento['fcu_status']) && $datosMovimiento['fcu_status']==POR_COBRAR){ echo "selected";}?>>Por Cobrar</option>
														<option value="<?=COBRADA?>" <?php if(isset($datosMovimiento['fcu_status']) && $datosMovimiento['fcu_status']==COBRADA){ echo "selected";}?>>Cobrada</option>
														<option value="<?=ANULADA?>" <?php if(isset($datosMovimiento['fcu_status']) && $datosMovimiento['fcu_status']==ANULADA){ echo "selected";}?>>Anulada</option>
													</select>
													<?php if(!empty($estadoFactura)): ?>
													<p style="margin-top: 5px; color: <?=$estadoColor;?>; font-weight: 600;">
														<i class="fa fa-info-circle"></i> Estado actual: <?=$estadoTexto;?>
													</p>
													<?php endif; ?>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Cerrado?</label>
													<select class="form-control select2" name="cerrado" disabled style="background-color: #e9ecef; cursor: not-allowed;">
														<option value="0" <?php if(isset($datosMovimiento['fcu_cerrado']) && $datosMovimiento['fcu_cerrado']==0){ echo "selected";}?>>Abierto</option>
														<option value="1" <?php if(isset($datosMovimiento['fcu_cerrado']) && $datosMovimiento['fcu_cerrado']==1){ echo "selected";}?>>Cerrado</option>
													</select>
													<input type="hidden" name="cerrado" value="<?=isset($datosMovimiento['fcu_cerrado']) ? $datosMovimiento['fcu_cerrado'] : '0';?>">
													<small class="form-text text-muted" style="font-style: italic; color: #6c757d;">Este campo está reservado para otro proceso y no puede ser modificado.</small>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Anulado</label>
													<select class="form-control select2" name="anulado" required disabled>
														<option value="">Seleccione una opción</option>
														<option value="0" <?php if(isset($datosMovimiento['fcu_anulado']) && $datosMovimiento['fcu_anulado']==0){ echo "selected";}?>>No</option>
														<option value="1" <?php if(isset($datosMovimiento['fcu_anulado']) && $datosMovimiento['fcu_anulado']==1){ echo "selected";}?>>Si</option>
													</select>
													<input type="hidden" name="anulado" value="<?=isset($datosMovimiento['fcu_anulado']) ? $datosMovimiento['fcu_anulado'] : 0;?>">
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label>Total Abonado</label>
													<input type="text" class="form-control" value="<?="$".number_format((float)($abonos ?? 0), 0, ",", ".");?>" readonly style="background: #f5f5f5; font-weight: bold; color: #00c292; font-size: 16px;">
												</div>
											</div>
										</div>

                                        <script>
                                            $(document).ready(function() {
                                                $('#select_usuario').select2({
                                                    placeholder: 'Seleccione el usuario...',
                                                    theme: "bootstrap",
                                                    multiple: false,
                                                    allowClear: true,
                                                    minimumInputLength: 0,
                                                    ajax: {
                                                        type: 'GET',
                                                        url: '../compartido/ajax-listar-usuarios.php',
                                                        dataType: 'text', // Forzar texto para evitar parse automático
                                                        data: function (params) {
                                                            return {
                                                                term: params.term || '',
                                                                todos: '1' // Cargar todos los usuarios, no solo los que tienen facturas
                                                            };
                                                        },
                                                        processResults: function(data) {
                                                            try {
                                                                // Parsear manualmente el JSON
                                                                var datos = JSON.parse(data);
                                                                return {
                                                                    results: $.map(datos || [], function(item) {
                                                                        return {
                                                                            id: item.value,
                                                                            text: item.label
                                                                        }
                                                                    })
                                                                };
                                                            } catch (e) {
                                                                console.error('Error parsing JSON:', e, data);
                                                                return { results: [] };
                                                            }
                                                        },
                                                        cache: true
                                                    }
                                                });
                                                
                                                // Validación de fecha en frontend
                                                $('input[name="fecha"]').on('change', function() {
                                                    const fechaIngresada = new Date($(this).val());
                                                    const fechaActual = new Date();
                                                    const fechaLimite = new Date();
                                                    fechaLimite.setFullYear(fechaLimite.getFullYear() - 1);
                                                    
                                                    if (fechaIngresada > fechaActual) {
                                                        alert('La fecha del documento no puede ser futura.');
                                                        $(this).val('<?=!empty($datosMovimiento['fcu_fecha']) ? date('Y-m-d', strtotime($datosMovimiento['fcu_fecha'])) : '';?>');
                                                        return false;
                                                    }
                                                    
                                                    if (fechaIngresada < fechaLimite) {
                                                        alert('La fecha del documento no puede ser mayor a un año en el pasado.');
                                                        $(this).val('<?=!empty($datosMovimiento['fcu_fecha']) ? date('Y-m-d', strtotime($datosMovimiento['fcu_fecha'])) : '';?>');
                                                        return false;
                                                    }
                                                });
                                            });
                                        </script>

                                        <!-- Sección de Items -->
                                        <div class="items-table-wrapper" style="margin-top: 20px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                                <h4 style="margin: 0; color: #333; font-weight: 600;">
                                                    <i class="fa fa-list"></i> Items de la Factura
                                                </h4>
                                                <?php if($puedeEditar){?>
                                                <button type="button" class="btn btn-add-item" onclick="abrirModalCrearItem()">
                                                    <i class="fa fa-plus"></i> Crear Item Nuevo
                                                </button>
                                                <?php } ?>
                                            </div>
                                            <div class="panel-body" style="padding: 0;">

                                                <div class="table-scrollable">
                                                    <table class="table table-bordered table-hover" style="width:100%; margin-bottom: 0;" id="tablaItems">
                                                        <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                            <tr>
                                                                <th style="color: white; border: none;">#</th>
                                                                <th style="color: white; border: none;">Item</th>
                                                                <th style="color: white; border: none;">Precio</th>
                                                                <th style="color: white; border: none;">Desc %</th>
                                                                <th style="color: white; border: none; width: 150px; max-width: 150px;">Impuesto</th>
                                                                <th style="color: white; border: none;">Descripción</th>
                                                                <th style="color: white; border: none;">Cant.</th>
                                                                <th style="color: white; border: none;">Total</th>
                                                                <th style="color: white; border: none;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="mostrarItems">
                                                            <?php
                                                                $idTransaction = base64_decode($_GET['id']);
                                                                
                                                                $itemsConsulta = Movimientos::listarItemsTransaction($conexion, $config, $idTransaction);

                                                                $subtotal=0;
                                                                $numItems=mysqli_num_rows($itemsConsulta);
                                                                if($numItems>0){
                                                                // Manejar el resultado según tus necesidades
                                                                    while ($fila = mysqli_fetch_array($itemsConsulta, MYSQLI_BOTH)) {
                                                                        $arrayEnviar = array("tipo"=>1, "restar"=>$fila['subtotal'], "descripcionTipo"=>"Para ocultar fila del registro.");
                                                                        $arrayDatos = json_encode($arrayEnviar);
                                                                        $objetoEnviar = htmlentities($arrayDatos);
                                                                        
                                                                        // Determinar si es item tipo crédito (C) o débito (D)
                                                                        $itemType = isset($fila['item_type']) ? $fila['item_type'] : 'D';
                                                                        $isCredito = ($itemType == 'C');
                                                                        $rowClass = $isCredito ? 'item-credito' : '';
                                                                        $signoSubtotal = $isCredito ? '-' : '';
                                                                        // Obtener application_time para créditos (por defecto ANTE_IMPUESTO si no existe)
                                                                        $applicationTime = ($isCredito && isset($fila['application_time'])) ? $fila['application_time'] : ($isCredito ? 'ANTE_IMPUESTO' : null);
                                                                        
                                                                        // Construir nombre del item con información de aplicación para créditos
                                                                        if ($isCredito) {
                                                                            $textoApplicationTime = ($applicationTime == 'POST_IMPUESTO') ? 'Después del Impuesto' : 'Antes del Impuesto';
                                                                            $nombreItem = $fila['name'] . ' <small style="color: #666; font-size: 0.85em;">(Crédito - ' . $textoApplicationTime . ')</small>';
                                                                        } else {
                                                                            $nombreItem = $fila['name'];
                                                                        }
                                                            ?>
                                                                <?php
                                                                    // Obtener snapshot del impuesto (tax_name, tax_fee) con fallback
                                                                    $taxNameSnapshot = !empty($fila['tax_name']) ? $fila['tax_name'] : '';
                                                                    $taxFeeSnapshot = !empty($fila['tax_fee']) ? floatval($fila['tax_fee']) : 0;
                                                                ?>
                                                                <tr id="reg<?=$fila['idtx'];?>" class="<?=$rowClass;?>" data-item-type="<?=$itemType;?>"<?=$applicationTime ? ' data-application-time="'.$applicationTime.'"' : '';?> data-tax-name="<?=htmlspecialchars($taxNameSnapshot, ENT_QUOTES, 'UTF-8')?>" data-tax-fee="<?=$taxFeeSnapshot?>">
                                                                    <td><?=$fila['idtx'];?></td>
                                                                    <td><?=$nombreItem;?></td>
                                                                    <td>
                                                                        <input type="number" min="0" id="precio<?=$fila['idtx'];?>" data-precio="<?=$fila['priceTransaction'];?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" value="<?=$fila['priceTransaction']?>" <?=$disabledPermiso;?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" id="descuento<?=$fila['idtx'];?>" data-descuento-anterior="<?=$fila['discount']?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" value="<?=$fila['discount']?>" <?=$disabledPermiso;?><?=$isCredito ? ' disabled' : '';?>>
                                                                    </td>
                                                                    <td style="width: 150px; max-width: 150px;">
                                                                        <div class="col-sm-12" style="padding: 0px;">
                                                                            <select class="form-control select2" id="impuesto<?=$fila['idtx'];?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" style="width: 100%;" <?=$disabledPermiso;?><?=$isCredito ? ' disabled' : '';?>>
                                                                                <option value="0" name="0">Ninguno - (0%)</option>
                                                                                <?php
                                                                                    $consulta= Movimientos::listarImpuestos($conexion, $config);
                                                                                    $impuestoEncontrado = false;
                                                                                    while($datosConsulta = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                                                        $selected = $fila['tax'] == $datosConsulta['id'] ? "selected" : "";
                                                                                        if ($selected) {
                                                                                            $impuestoEncontrado = true;
                                                                                        }
                                                                                        
                                                                                        // Si es el impuesto seleccionado y hay snapshot, usar el snapshot para mostrar
                                                                                        if ($selected && !empty($taxNameSnapshot) && $taxFeeSnapshot > 0) {
                                                                                            // Usar snapshot para el texto y data-valor-impuesto (formato consistente con el resto del código)
                                                                                            $textoImpuesto = $taxNameSnapshot." - (".$taxFeeSnapshot."%)";
                                                                                            $valorImpuesto = $taxFeeSnapshot;
                                                                                        } else {
                                                                                            // Usar valor actual del impuesto
                                                                                            $textoImpuesto = $datosConsulta['type_tax']." - (".$datosConsulta['fee']."%)";
                                                                                            $valorImpuesto = $datosConsulta['fee'];
                                                                                        }
                                                                                ?>
                                                                                <option value="<?=$datosConsulta['id']?>" data-name-impuesto="<?=$datosConsulta['type_tax']?>" data-valor-impuesto="<?=$valorImpuesto?>" <?=$selected?>><?=$textoImpuesto?></option>
                                                                                <?php } ?>
                                                                                <?php
                                                                                    // Si hay snapshot pero el impuesto no está en la lista actual, agregar opción especial
                                                                                    if (!empty($taxNameSnapshot) && $taxFeeSnapshot > 0 && !empty($fila['tax']) && $fila['tax'] != 0 && !$impuestoEncontrado) {
                                                                                ?>
                                                                                <option value="<?=$fila['tax']?>" data-name-impuesto="<?=htmlspecialchars($taxNameSnapshot, ENT_QUOTES, 'UTF-8')?>" data-valor-impuesto="<?=$taxFeeSnapshot?>" selected><?=$taxNameSnapshot." - (".$taxFeeSnapshot."%)"?> [Histórico]</option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <textarea  id="descrip<?=$fila['idtx'];?>" cols="30" rows="1" onchange="guardarDescripcion('<?=$fila['idtx'];?>')" <?=$disabledPermiso;?>><?=htmlspecialchars(strip_tags($fila['description'] ?? ''), ENT_QUOTES, 'UTF-8')?></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" title="cantity" min="0" id="cantidadItems<?=$fila['idtx'];?>" data-cantidad="<?=$fila['cantity'];?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" value="<?=$fila['cantity'];?>" style="width: 50px;" <?=$disabledPermiso;?>>
                                                                    </td>
                                                                    <td id="subtotal<?=$fila['idtx'];?>" data-subtotal-anterior="<?=$fila['subtotal'];?>" data-item-type="<?=$itemType;?>"><?=$signoSubtotal;?>$<?=number_format($fila['subtotal'] ?? 0, 0, ",", ".")?></td>
                                                                    <td>
                                                                        <?php if($puedeEditar){?>
                                                                            <a href="#" title="<?=$objetoEnviar;?>" id="<?=$fila['idtx'];?>" name="movimientos-items-eliminar.php?idR=<?=$fila['idtx'];?>" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" onClick="deseaEliminarNuevoItem(this)">X</a>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            <?php 
                                                                    }
                                                                }
                                                            ?>
                                                        </tbody>
                                                        <tbody>
                                                            <tr>
                                                                <td id="idItemNuevo"></td>
                                                                <td>
                                                                    <div class="col-sm-12" style="padding: 0px;">
                                                                        <div style="display: flex; gap: 5px;">
                                                                            <select class="form-control select2" id="items" onchange="guardarNuevoItem(this)" style="flex: 1;" <?=$disabledPermiso;?>>
                                                                                <option value="">Seleccione una opción</option>
                                                                                <?php
                                                                                    $consulta= Movimientos::listarItems($conexion, $config);
                                                                                    while($datosConsulta = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                                                ?>
                                                                                <option value="<?=$datosConsulta['item_id']?>" name="<?=$datosConsulta['price']?>"><?=$datosConsulta['name']?> - $<?=number_format($datosConsulta['price'] ?? 0, 0, ",", ".")?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                            <?php if($puedeEditar){?>
                                                                            <button type="button" class="btn btn-sm btn-success" onclick="abrirModalCrearItem()" title="Crear nuevo item" style="white-space: nowrap;">
                                                                                <i class="fa fa-plus"></i>
                                                                            </button>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="0" id="precioNuevo" data-precio="0" onchange="actualizarSubtotal('idNuevo')" value="0" <?=$disabledPermiso;?>>
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="descuentoNuevo" data-total-precio="0" data-precio-item-anterior="0" data-descuento-anterior="0" onchange="actualizarSubtotal('idNuevo')" value="0" <?=$disabledPermiso;?>>
                                                                </td>
                                                                <td>
                                                                    <div class="col-sm-12" style="padding: 0px;">
                                                                        <select class="form-control  select2" id="impuestoNuevo" onchange="actualizarSubtotal('idNuevo')" <?=$disabledPermiso;?>>
                                                                            <option value="0" name="0">Ninguno - (0%)</option>
                                                                            <?php
                                                                                $consulta= Movimientos::listarImpuestos($conexion, $config);
                                                                                while($datosConsulta = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                                            ?>
                                                                            <option value="<?=$datosConsulta['id']?>" data-name-impuesto="<?=$datosConsulta['type_tax']?>" data-valor-impuesto="<?=$datosConsulta['fee']?>"><?=$datosConsulta['type_tax']." - (".$datosConsulta['fee']."%)"?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <textarea  id="descripNueva" cols="30" rows="1" onchange="guardarDescripcion('idNuevo')" <?=$disabledPermiso;?>></textarea>
                                                                </td>
                                                                <td><input type="number" min="0" id="cantidadItemNuevo" data-cantidad="1" onchange="actualizarSubtotal('idNuevo')" value="1" style="width: 50px;" <?=$disabledPermiso;?>></td>
                                                                <td id="subtotalNuevo" data-subtotal-anterior="0">$0</td>
                                                                <td id="eliminarNuevo"></td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot id="tfootTotalizar">
                                                            <?php if($puedeEditar){?>
                                                                <tr>
                                                                    <td colspan="9" style="padding: 15px;">
                                                                        <button type="button" title="Agregar nueva línea para item" class="btn btn-sm btn-primary" data-toggle="tooltip" onclick="nuevoItem()" data-placement="right">
                                                                            <i class="fa fa-plus"></i> Agregar línea
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            <?php }?>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <!-- Resumen de Totales -->
                                            <div class="row" style="margin-top: 20px;">
                                                <div class="col-md-6"></div>
                                                <div class="col-md-6">
                                                    <div class="total-summary">
                                                        <div class="total-row">
                                                            <span>SUBTOTAL BRUTO:</span>
                                                            <span id="subtotalBruto" data-subtotal-bruto="0">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>(-) DESCUENTOS DE ÍTEMS:</span>
                                                            <span id="valorDescuento" data-valor-descuento="0" style="color: #ff5722;">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>(-) DESCUENTOS COMERCIALES GLOBALES:</span>
                                                            <span id="descuentosComerciales" style="color: #ff5722;">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>(=) SUBTOTAL GRABABLE:</span>
                                                            <span id="subtotal" data-subtotal="0" data-subtotal-anterior-sub="0">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>(+) IMPUESTOS:</span>
                                                            <span id="valorImpuesto">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>(=) TOTAL FACTURADO:</span>
                                                            <span id="totalFacturado">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>(-) ANTICIPOS O SALDOS A FAVOR:</span>
                                                            <span id="valorCreditos" style="color: #ff5722;">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>VALOR ADICIONAL:</span>
                                                            <span id="valorAdicional" data-valor-adicional="0">$0</span>
                                                        </div>
                                                        <div class="total-row">
                                                            <span>(=) TOTAL NETO A PAGAR:</span>
                                                            <span id="totalNeto" data-total-neto="0" data-total-neto-anterior="0">$0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <script>
                                            $(document).ready(function() {
                                                // Inicializar totalizar para calcular correctamente los items crédito/débito
                                                totalizar();
                                                
                                                // Deshabilitar campos de descuento e impuesto para items crédito existentes
                                                $('#tablaItems tbody tr[data-item-type="C"]').each(function() {
                                                    var $fila = $(this);
                                                    var idItem = $fila.attr('id').replace('reg', '');
                                                    if (idItem) {
                                                        $('#descuento' + idItem).prop('disabled', true);
                                                        $('#impuesto' + idItem).prop('disabled', true);
                                                    }
                                                });
                                                
                                                // Inicializar Select2 para los campos de impuesto con ancho fijo
                                                $('#tablaItems select.select2').each(function() {
                                                    if (!$(this).hasClass('select2-hidden-accessible')) {
                                                        $(this).select2({
                                                            theme: 'bootstrap',
                                                            width: '100%',
                                                            dropdownAutoWidth: false,
                                                            containerCssClass: 'select2-container-fixed-width'
                                                        });
                                                    }
                                                });
                                            });
                                        </script>

                                        <!-- Observaciones -->
                                        <div class="form-section" style="margin-top: 20px;">
                                            <div class="form-section-title">
                                                <i class="fa fa-comment"></i> Observaciones
                                            </div>
                                            <div class="form-group">
                                                <!-- Observaciones: solo editables si NO hay abonos -->
                                                <textarea cols="80" id="editor1" name="obs" class="form-control" rows="6" placeholder="Escribe observaciones adicionales..." style="resize: vertical;" <?=$disabledPermiso;?>><?=htmlspecialchars($datosMovimiento['fcu_observaciones'] ?? '');?></textarea>
                                            </div>
                                        </div>
										
                                        <!-- Botones de Acción -->
                                        <div class="form-section" style="margin-top: 20px;">
                                            <div class="text-left" style="display: flex; gap: 10px;">
                                                <?php if($puedeEditar): ?>
                                                    <!-- Botón: Guardar cambios (mantiene EN_PROCESO) -->
                                                    <button type="submit" name="accion" value="guardar" class="btn btn-primary" style="padding: 10px 20px;">
                                                        <i class="fa fa-save"></i> Guardar cambios
                                                    </button>
                                                    
                                                    <!-- Botón: Confirmar creación (cambia a POR_COBRAR) -->
                                                    <button type="submit" name="accion" value="confirmar" id="btnConfirmarFactura" class="btn btn-success" style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;" <?= ($numItems == 0) ? 'disabled title="No se puede confirmar la factura sin items asociados"' : ''; ?>>
                                                        <i class="fa fa-check-circle"></i> Confirmar creación de factura
                                                    </button>
                                                    <small id="msgItemsRequeridos" class="text-danger" style="display: <?= ($numItems == 0) ? 'block' : 'none'; ?>; margin-top: 5px;">
                                                        <i class="fa fa-exclamation-triangle"></i> Debe agregar al menos un item para confirmar la factura
                                                    </small>
                                                    
                                                    <a href="movimientos.php" class="btn btn-default" style="padding: 10px 20px;">
                                                        <i class="fa fa-times"></i> Cancelar
                                                    </a>
                                                <?php else: ?>
                                                    <a href="movimientos.php" class="btn btn-default" style="padding: 10px 20px;">
                                                        <i class="fa fa-arrow-left"></i> Volver
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
						
                    </div>

                </div>
                <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        
        <!-- Modal Crear Item Nuevo -->
        <div class="modal fade" id="modalCrearItem" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">
                            <i class="fa fa-plus-circle"></i> Crear Nuevo Item
                        </h4>
                    </div>
                    <form id="formCrearItem" action="items-guardar.php" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nombre del Item <span style="color: red;">*</span></label>
                                <input type="text" name="nombre" class="form-control" required placeholder="Ej: Producto X, Servicio Y">
                            </div>
                            <div class="form-group">
                                <label>Precio <span style="color: red;">*</span></label>
                                <input type="number" min="0" step="0.01" name="precio" class="form-control" required placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label>Tipo <span style="color: red;">*</span></label>
                                <select class="form-control" name="item_type" id="item_type_modal" required onchange="toggleApplicationTimeModal()">
                                    <option value="D">Débito (Cargo)</option>
                                    <option value="C">Crédito (Descuento)</option>
                                </select>
                            </div>
                            <div class="form-group" id="div_application_time_modal" style="display: none;">
                                <label>Aplicación <span style="color: red;">*</span></label>
                                <select class="form-control" name="application_time" id="application_time_modal">
                                    <option value="ANTE_IMPUESTO">Antes del Impuesto</option>
                                    <option value="POST_IMPUESTO">Después del Impuesto</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea name="descrip" class="form-control" rows="3" placeholder="Descripción del item..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn deepPink-bgcolor">
                                <i class="fa fa-save"></i> Guardar Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
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
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
    <script src="../ckeditor/ckeditor.js"></script>

    <!-- Movimientos JS -->
    <script src="../js/Movimientos.js"></script>
    
    <script>
        // Actualizar estado del botón de confirmar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarEstadoBotonConfirmar();
        });
        
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        CKEDITOR.replace( 'editor1' );
        
        // Función para abrir modal de crear item
        function abrirModalCrearItem() {
            // Resetear formulario y campos
            $('#formCrearItem')[0].reset();
            $('#item_type_modal').val('D');
            $('#div_application_time_modal').hide();
            $('#modalCrearItem').modal('show');
        }
        
        // Función para mostrar/ocultar campo de aplicación según el tipo de item
        function toggleApplicationTimeModal() {
            var itemType = $('#item_type_modal').val();
            if (itemType === 'C') {
                $('#div_application_time_modal').show();
                $('#application_time_modal').prop('required', true);
            } else {
                $('#div_application_time_modal').hide();
                $('#application_time_modal').prop('required', false);
                $('#application_time_modal').val('ANTE_IMPUESTO');
            }
        }
        
        // Manejar el envío del formulario de crear item
        $('#formCrearItem').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');
            
            // Deshabilitar botón mientras se procesa
            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
            
            $.ajax({
                url: 'items-guardar.php',
                type: 'POST',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Cerrar modal
                        $('#modalCrearItem').modal('hide');
                        
                        // Limpiar formulario
                        $form[0].reset();
                        
                        // Recargar solo el select de items
                        setTimeout(function() {
                            // Recargar la página para mostrar el nuevo item
                            location.reload();
                        }, 500);
                        
                        $.toast({
                            heading: 'Éxito',
                            text: 'Item creado correctamente. Recargando...',
                            position: 'bottom-right',
                            showHideTransition: 'slide',
                            loaderBg: '#26c281',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 6
                        });
                    } else {
                        throw new Error('Error en la respuesta del servidor');
                    }
                },
                error: function(xhr, status, error) {
                    $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar Item');
                    
                    var errorMsg = 'Error al crear el item. Por favor intenta nuevamente.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $.toast({
                        heading: 'Error',
                        text: errorMsg,
                        position: 'bottom-right',
                        showHideTransition: 'slide',
                        loaderBg: '#ff5722',
                        icon: 'error',
                        hideAfter: 5000,
                        stack: 6
                    });
                }
            });
        });
    </script>
</body>
</html>