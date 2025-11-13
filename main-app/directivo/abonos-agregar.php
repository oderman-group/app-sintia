<?php
include("session.php");
$idPaginaInterna = 'DT0265';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

$codigoUnico=Utilidades::generateCode("ABO");
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
    <!-- Estilos mejorados para abonos -->
    <link href="../css/movimientos-mejorado.css" rel="stylesheet" type="text/css" />
    <style>
        .abono-form-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .panel-heading-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .panel-heading-blue {
            background: linear-gradient(135deg, #03a9f4 0%, #00c292 100%);
            color: white;
        }
        input[type="number"], textarea, select, input[type="date"], input[type="datetime-local"] {
            border-radius: 4px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        input[type="number"]:focus, textarea:focus, select:focus, input[type="date"]:focus, input[type="datetime-local"]:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .table-scrollable table {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-scrollable table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 12px;
            border: none;
        }
        .table-scrollable table tbody td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .table-scrollable table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .factura-details-row {
            display: none;
            background-color: #f8f9fa;
        }
        .factura-details-row.show {
            display: table-row;
        }
        .expand-btn {
            cursor: pointer;
            color: #667eea;
            font-size: 18px;
            transition: transform 0.3s ease;
        }
        .expand-btn.expanded {
            transform: rotate(90deg);
        }
        .factura-details-content {
            padding: 15px;
            background: white;
            border-left: 3px solid #667eea;
            margin: 10px 0;
        }
    </style>
</head>
<!-- END HEAD -->
<?php require_once(ROOT_PATH."/main-app/compartido/body.php");?>
    <div class="page-wrapper">
        <?php require_once(ROOT_PATH."/main-app/compartido/encabezado.php");?>
		
        <?php require_once(ROOT_PATH."/main-app/compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php require_once(ROOT_PATH."/main-app/compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?></div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="abonos.php" onClick="deseaRegresar(this)"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                            <div class="panel abono-form-section">
                                <header class="panel-heading panel-heading-purple">
                                    <h4 style="margin: 0; color: white;">
                                        <i class="fa fa-plus-circle"></i> <?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?>
                                    </h4>
                                </header>
                                <div class="panel-body" style="padding: 25px;">
									<form name="formularioGuardar" id="formularioGuardar" action="abonos-guardar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" value="<?=$codigoUnico?>" name="codigoUnicoTemporal" id="idAbono">
										<input type="hidden" name="abonos_facturas_json" id="abonos_facturas_json">
										<input type="hidden" name="conceptos_contables_json" id="conceptos_contables_json">

										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[424][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-5">
                                                <select class="form-control select2" id="select_cliente" name="cliente" onchange="mostrarTipoTransaccion()" required <?=$disabledPermiso;?>>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-3">
                                                <?php 
                                                $fechaActual = date('Y-m-d\TH:i');
                                                ?>
                                                <input type="datetime-local" name="fecha" class="form-control" value="<?=$fechaActual?>" required <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                $('#select_cliente').select2({
                                                placeholder: 'Seleccione el usuario...',
                                                theme: "bootstrap",
                                                multiple: false,
                                                    ajax: {
                                                        type: 'GET',
                                                        url: '../compartido/ajax-listar-usuarios.php',
                                                        processResults: function(data) {
                                                            var radios = document.getElementsByName('tipoTransaccion');
                                                            
                                                            for (var i = 0; i < radios.length; i++) {
                                                                if (radios[i].checked) {
                                                                    radios[i].checked = false;
                                                                }
                                                            }
                                                            $('#mostrarFacturas').empty().hide().html('').show(1);
                                                            document.getElementById("divFacturas").style.display="none";
                                                            document.getElementById("divCuentasContables").style.display="none";
                                                            document.getElementById("divTipoTransaccion").style.display="none";
                                                            data = JSON.parse(data);
                                                            return {
                                                                results: $.map(data, function(item) {
                                                                    return {
                                                                        id: item.value,
                                                                        text: item.label
                                                                    }
                                                                })
                                                            };
                                                        }
                                                    }
                                                });
                                            });
                                        </script>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[414][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-3">
                                                <select class="form-control select2" id="metodoPago" name="metodoPago" required <?=$disabledPermiso;?>>
                                                    <option value="" >Seleccione una opción</option>
													<option value="EFECTIVO" >Efectivo</option>
													<option value="CHEQUE" >Cheque</option>
													<option value="T_DEBITO" >T. Débito</option>
													<option value="T_CREDITO" >T. Crédito</option>
													<option value="TRANSFERENCIA" >Transferencia</option>
													<option value="OTROS" >Otras Formas de pago</option>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label"><?=$frases[345][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="file" name="comprobante" class="form-control" <?=$disabledPermiso;?>>
                                            </div>
										</div>

                                        <div id="divTipoTransaccion" style="display: none;">
                                            <div class="panel" style="margin-top: 20px;">
                                                <header class="panel-heading panel-heading-blue">
                                                    <h5 style="margin: 0; color: white;">
                                                        <i class="fa fa-exchange"></i> Tipo de Transacción
                                                    </h5>
                                                </header>
                                                <div class="panel-body" style="text-align: center; padding: 25px;">
                                                    <span style="font-size: 17px; color: #333;">Ajustar este ingreso a una <b>factura de venta</b> existente en el sistema?</span><br>
                                                    <small style="color: #666;">Recuerda que puedes registrar un ingreso sin necesidad de que este asociado a una factura de venta</small><br><br>
                                                
                                                    <div class="form-group row" style="align-items: center; justify-content: center;">
                                                        <div class="col-sm-3">
                                                            <label style="font-weight: normal; cursor: pointer; padding: 10px 20px; border: 2px solid #667eea; border-radius: 5px; display: inline-block; min-width: 120px; background: white; color: #667eea;">
                                                                <input type="radio" name="tipoTransaccion" id="opt1" value="<?=INVOICE?>" onClick="tipoAbono(1)" style="margin-right: 8px;"> SÍ
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label style="font-weight: normal; cursor: pointer; padding: 10px 20px; border: 2px solid #667eea; border-radius: 5px; display: inline-block; min-width: 120px; background: white; color: #667eea;">
                                                                <input type="radio" name="tipoTransaccion" id="opt2" value="<?=ACCOUNT?>" onClick="tipoAbono(2)" style="margin-right: 8px;"> NO
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel" id="divFacturas" style="display: none; margin-top: 20px;">
                                                <header class="panel-heading panel-heading-blue">
                                                    <h5 style="margin: 0; color: white;">
                                                        <i class="fa fa-file-text-o"></i> Facturas Pendientes
                                                    </h5>
                                                </header>
                                                <div class="panel-body" style="padding: 20px;">

                                                    <div class="table-scrollable">
                                                        <table class="display" style="width:100%;" id="tablaItems">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 40px;"></th>
                                                                    <th>Cod. Factura</th>
                                                                    <th>Fecha</th>
                                                                    <th><?=$frases[107][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th><?=$frases[417][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th><?=$frases[418][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th>Valor recibido</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="mostrarFacturas"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel" id="divCuentasContables" style="display: none;">
                                                <header class="panel-heading panel-heading-blue"> A qué cuentas contables pertenece este ingreso?</header>
                                                <div class="panel-body">

                                                    <div class="table-scrollable">
                                                        <table class="display" style="width:100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Concepto</th>
                                                                    <th>Valor</th>
                                                                    <th>Cant.</th>
                                                                    <th>Descripción</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td id="idConcepto"></td>
                                                                    <td>
                                                                        <div style="padding: 0px;">
                                                                            <select class="form-control  select2" style="width: 100%;" id="concepto" onchange="guardarNuevoConcepto(this)" <?=$disabledPermiso;?>>
                                                                                <option value="">Seleccione una opción</option>
                                                                                <option value="OTROS_INGRESOS" >Otros Ingresos</option>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" id="precioNuevo" data-precio="0" onchange="actualizarSubtotalConceptos('idNuevo')" value="0" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" id="cantidadNuevo" data-cantidad="1" onchange="actualizarSubtotalConceptos('idNuevo')" value="1" style="width: 50px;" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <textarea  id="descripNueva" cols="30" rows="1" onchange="guardarDescripcionConcepto('idNuevo')" disabled></textarea>
                                                                    </td>
                                                                    <td id="subtotalNuevo">$0</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[109][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <textarea cols="80" id="editor1" name="obser" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label"><?=$frases[416][$datosUsuarioActual['uss_idioma']];?>
                                                <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Estas notas no se verán reflejadas en el comprobante."><i class="fa fa-info"></i></button>
                                            </label>
                                            <div class="col-sm-4">
                                                <textarea cols="80" id="editor2" name="notas" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                        </div>
                                        
                                        <?php require_once("../class/componentes/botones-guardar.php");
                            				$botones = new botonesGuardar("abonos.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-3">
                            <div class="panel">
                                <header class="panel-heading panel-heading-blue">TOTAL</header>
                                <div class="panel-body">
                                    <table style="width: 100%;" align="center">
                                        <tr>
                                            <td style="padding-right: 20px;">TOTAL:</td>
                                            <td align="left" id="totalNeto">$0</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 20px;">TOTAL. ABONOS:</td>
                                            <td align="left" id="abonosNeto">$0</td>
                                        </tr>
                                        <tr style="font-size: 15px; font-weight:bold;">
                                            <td style="padding-right: 20px;">TOTAL POR COBRAR:</td>
                                            <td align="left" id="porCobrarNeto">$0</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- end page container -->
        <?php require_once(ROOT_PATH."/main-app/compartido/footer.php");?>
    </div>
    <script src="../js/Movimientos.js" ></script>
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

    <script>
        CKEDITOR.replace( 'editor1' );
        CKEDITOR.replace( 'editor2' );
        
        // Función global para expandir/contraer detalles de facturas
        function toggleFacturaDetails(facturaId) {
            var detailsRow = document.getElementById('details' + facturaId);
            var expandBtn = document.getElementById('expand' + facturaId);
            
            if (detailsRow && expandBtn) {
                if (detailsRow.classList.contains('show')) {
                    detailsRow.classList.remove('show');
                    expandBtn.classList.remove('expanded');
                } else {
                    detailsRow.classList.add('show');
                    expandBtn.classList.add('expanded');
                }
            }
        }
        
        // Interceptar submit del formulario para validar y recopilar abonos
        $(document).ready(function() {
            $('#formularioGuardar').on('submit', function(e) {
                var tipoTransaccion = $('input[name="tipoTransaccion"]:checked').val();
                
                // Validar que se seleccionó un tipo de transacción
                if (!tipoTransaccion) {
                    e.preventDefault();
                    alert('Por favor selecciona si el abono se asociará a una factura o no.');
                    return false;
                }
                
                // Solo procesar si es tipo INVOICE
                if (tipoTransaccion === '<?=INVOICE?>') {
                    var abonosFacturas = [];
                    var totalAbonar = 0;
                    var erroresValidacion = [];
                    
                    // Recorrer todos los inputs de abono en la tabla
                    $('input.input-abono-factura').each(function() {
                        var valorAbono = parseFloat($(this).val()) || 0;
                        var idFactura = $(this).attr('data-id-factura');
                        
                        console.log('Procesando factura:', idFactura, 'valor:', valorAbono);
                        
                        if (valorAbono > 0) {
                            // Validar que el abono no exceda el saldo pendiente
                            var saldoPendiente = parseFloat($(this).attr('max')) || 0;
                            
                            if (valorAbono > saldoPendiente) {
                                var nombreFactura = $(this).closest('tr').find('td:eq(1)').text();
                                erroresValidacion.push('Factura ' + nombreFactura + ': El abono ($' + 
                                    valorAbono.toLocaleString() + ') excede el saldo pendiente ($' + 
                                    saldoPendiente.toLocaleString() + ')');
                            } else {
                                abonosFacturas.push({
                                    idFactura: idFactura,
                                    valorAbono: valorAbono
                                });
                                totalAbonar += valorAbono;
                                console.log('Abono agregado:', {idFactura: idFactura, valorAbono: valorAbono});
                            }
                        }
                    });
                    
                    console.log('Total abonos recopilados:', abonosFacturas.length);
                    console.log('JSON a enviar:', JSON.stringify(abonosFacturas));
                    
                    // Si hay errores de validación, mostrarlos y detener el submit
                    if (erroresValidacion.length > 0) {
                        e.preventDefault();
                        alert('⚠️ Errores de validación:\n\n' + erroresValidacion.join('\n\n'));
                        return false;
                    }
                    
                    // Validar que al menos se haya ingresado un abono
                    if (abonosFacturas.length === 0) {
                        e.preventDefault();
                        alert('Debes ingresar al menos un valor de abono a una factura.');
                        return false;
                    }
                    
                    // Guardar en campo hidden como JSON
                    $('#abonos_facturas_json').val(JSON.stringify(abonosFacturas));
                    $('#conceptos_contables_json').val('');
                    
                } else if (tipoTransaccion === '<?=ACCOUNT?>') {
                    // Procesar conceptos contables
                    var conceptos = [];
                    var concepto = $('#idConcepto').text().trim();
                    var precio = parseFloat($('#precioNuevo').val()) || 0;
                    var cantidad = parseFloat($('#cantidadNuevo').val()) || 1;
                    var descripcion = $('#descripNueva').val().trim();
                    
                    if (concepto && precio > 0) {
                        conceptos.push({
                            concepto: concepto,
                            precio: precio,
                            cantidad: cantidad,
                            subtotal: precio * cantidad,
                            descripcion: descripcion
                        });
                    }
                    
                    if (conceptos.length === 0) {
                        e.preventDefault();
                        alert('Debes seleccionar un concepto contable e ingresar un valor.');
                        return false;
                    }
                    
                    $('#conceptos_contables_json').val(JSON.stringify(conceptos));
                    $('#abonos_facturas_json').val('');
                }
                
                // Continuar con el submit normal
                return true;
            });
        });
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>