<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0277';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}
?>

<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title">Facturación Masiva</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="movimientos.php" onClick="deseaRegresar(this)">Movimientos</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Facturación Masiva</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php 
                                include("../../config-general/mensajes-informativos.php");
                            ?>
							<div class="panel">
								<header class="panel-heading panel-heading-purple">Facturación Masiva</header>
                            	<div class="panel-body">
									<form name="formularioGuardar" action="facturacion-masiva-guardar.php" method="post" id="formFacturacionMasiva">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre del lote <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="text" name="lote_nombre" class="form-control" required <?=$disabledPermiso;?> placeholder="Ej: Facturación mensual Enero 2025">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de grupo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control select2" id="tipo_grupo" name="tipo_grupo" required <?=$disabledPermiso;?> onchange="cambiarTipoGrupo()">
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="ESTUDIANTES">Estudiantes</option>
                                                    <option value="DOCENTES">Docentes</option>
                                                    <option value="DIRECTIVOS">Directivos</option>
                                                    <option value="ACUDIENTES">Acudientes</option>
                                                    <option value="OTROS">Otros</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Criterios para ESTUDIANTES -->
                                        <div id="criterios_estudiantes" style="display:none;">
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Grados <span style="color: red;">(*)</span></label>
                                                <div class="col-sm-5">
                                                    <select class="form-control select2-multiple" id="grados" name="grados[]" multiple style="width: 100%;" <?=$disabledPermiso;?>>
                                                        <option value="">Seleccione grados</option>
                                                        <?php
                                                            $gradosConsulta = Grados::listarGrados(1);
                                                            while($grado = mysqli_fetch_array($gradosConsulta, MYSQLI_BOTH)){
                                                        ?>
                                                        <option value="<?=$grado['gra_id']?>"><?=$grado['gra_id']?>. <?=$grado['gra_nombre']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <label class="col-sm-1 control-label">Grupos</label>
                                                <div class="col-sm-4">
                                                    <select class="form-control select2-multiple" id="grupos" name="grupos[]" multiple style="width: 100%;" <?=$disabledPermiso;?>>
                                                        <option value="">Seleccione grupos</option>
                                                        <?php
                                                            $gruposConsulta = Grupos::listarGrupos();
                                                            while($grupo = mysqli_fetch_array($gruposConsulta, MYSQLI_BOTH)){
                                                        ?>
                                                        <option value="<?=$grupo['gru_id']?>"><?=$grupo['gru_id']?>. <?=$grupo['gru_nombre']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Criterios para otros tipos de usuarios -->
                                        <div id="criterios_otros" style="display:none;">
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Estado</label>
                                                <div class="col-sm-4">
                                                    <select class="form-control select2" id="estado_usuario" name="estado_usuario" <?=$disabledPermiso;?>>
                                                        <option value="">Todos</option>
                                                        <option value="1">Activo</option>
                                                        <option value="0">Inactivo</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Observaciones</label>
                                            <div class="col-sm-10">
                                                <textarea name="lote_observaciones" class="form-control" rows="3" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                        </div>

                                        <div class="panel">
                                            <header class="panel-heading panel-heading-blue">Ítems a Facturar</header>
                                            <div class="panel-body">
                                                <div class="table-scrollable">
                                                    <table class="display" style="width:100%;" id="tablaItems">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Item</th>
                                                                <th>Tipo</th>
                                                                <th>Precio</th>
                                                                <th>Cant.</th>
                                                                <th>Total</th>
                                                                <th>Acción</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="mostrarItems">
                                                        </tbody>
                                                        <tbody>
                                                            <tr>
                                                                <td id="idItemNuevo"></td>
                                                                <td>
                                                                    <select class="form-control select2" id="items" onchange="agregarItemMasivo(this)" <?=$disabledPermiso;?>>
                                                                        <option value="">Seleccione un ítem</option>
                                                                        <?php
                                                                            $consulta= Movimientos::listarItems($conexion, $config);
                                                                            while($datosConsulta = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                                                $tipoItem = $datosConsulta['item_type'] ?? 'D';
                                                                                $tipoTexto = $tipoItem == 'C' ? 'Crédito' : 'Débito';
                                                                        ?>
                                                                        <option value="<?=$datosConsulta['item_id'] ?? ''?>" data-precio="<?=$datosConsulta['price']?>" data-tipo="<?=$tipoItem?>" data-nombre="<?=htmlspecialchars($datosConsulta['name'])?>"><?=$datosConsulta['name']?> (<?=$tipoTexto?>)</option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td id="tipoItemNuevo"></td>
                                                                <td>
                                                                    <input type="number" min="0" id="precioNuevo" value="0" disabled style="width: 100px;">
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="1" id="cantidadItemNuevo" value="1" style="width: 70px;" disabled>
                                                                </td>
                                                                <td id="subtotalNuevo">$0</td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="7">
                                                                    <div id="resumenItems" style="text-align: right; padding: 10px;">
                                                                        <strong>Total Débitos: <span id="totalDebitos">$0</span></strong><br>
                                                                        <strong>Total Créditos: <span id="totalCreditos">$0</span></strong><br>
                                                                        <strong>Total Neto: <span id="totalNeto">$0</span></strong>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php $botones = new botonesGuardar("movimientos.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
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
        let contadorItems = 0;
        let itemsSeleccionados = [];

        function cambiarTipoGrupo() {
            const tipoGrupo = $('#tipo_grupo').val();
            $('#criterios_estudiantes').hide();
            $('#criterios_otros').hide();
            
            if (tipoGrupo === 'ESTUDIANTES') {
                $('#criterios_estudiantes').show();
                $('#grados').prop('required', true);
                
                // Inicializar Select2 para grados y grupos si no están inicializados
                setTimeout(function() {
                    if (!$('#grados').hasClass('select2-hidden-accessible')) {
                        $('#grados').select2({
                            placeholder: 'Seleccione grados',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                    if (!$('#grupos').hasClass('select2-hidden-accessible')) {
                        $('#grupos').select2({
                            placeholder: 'Seleccione grupos',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                }, 100);
            } else if (tipoGrupo && tipoGrupo !== '') {
                $('#criterios_otros').show();
                $('#grados').prop('required', false);
            }
        }
        
        // Inicializar Select2 al cargar la página si ya está seleccionado Estudiantes
        $(document).ready(function() {
            if ($('#tipo_grupo').val() === 'ESTUDIANTES') {
                cambiarTipoGrupo();
            }
            
            // Inicializar Select2 para el tipo de grupo
            $('#tipo_grupo').select2({
                width: '100%'
            });
        });

        function agregarItemMasivo(select) {
            const option = $(select).find('option:selected');
            if (!option.val()) return;

            const itemId = option.val();
            const itemNombre = option.data('nombre');
            const itemPrecio = parseFloat(option.data('precio')) || 0;
            const itemTipo = option.data('tipo') || 'D';
            const cantidad = parseFloat($('#cantidadItemNuevo').val()) || 1;
            const subtotal = itemPrecio * cantidad;

            // Verificar si el ítem ya fue agregado
            if (itemsSeleccionados.includes(itemId)) {
                alert('Este ítem ya fue agregado');
                $(select).val('').trigger('change');
                return;
            }

            contadorItems++;
            itemsSeleccionados.push(itemId);

            const tipoTexto = itemTipo == 'C' ? '<span class="label label-success">Crédito</span>' : '<span class="label label-info">Débito</span>';
            const fila = `
                <tr id="filaItem${contadorItems}">
                    <td>${contadorItems}</td>
                    <td>${itemNombre}</td>
                    <td>${tipoTexto}</td>
                    <td>$${itemPrecio.toLocaleString('es-CO')}</td>
                    <td>
                        <input type="number" min="1" class="form-control cantidad-item" 
                               data-item-id="${itemId}" data-precio="${itemPrecio}" data-fila="${contadorItems}"
                               value="${cantidad}" style="width: 80px; display: inline-block;"
                               onchange="actualizarCantidadItem(${contadorItems}, '${itemId}', ${itemPrecio})">
                    </td>
                    <td class="total-item" id="totalItem${contadorItems}">$${subtotal.toLocaleString('es-CO')}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarItem(${contadorItems}, '${itemId}')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                    <input type="hidden" name="items[]" value="${itemId}">
                    <input type="hidden" class="hidden-cantidad" name="cantidades[]" value="${cantidad}" data-fila="${contadorItems}">
                </tr>
            `;

            $('#mostrarItems').append(fila);
            $(select).val('').trigger('change');
            $('#cantidadItemNuevo').val(1);
            actualizarTotales();
        }

        function eliminarItem(filaId, itemId) {
            $(`#filaItem${filaId}`).remove();
            itemsSeleccionados = itemsSeleccionados.filter(id => id !== itemId);
            actualizarTotales();
        }

        function actualizarCantidadItem(filaId, itemId, precio) {
            const input = $(`#filaItem${filaId} .cantidad-item`);
            const cantidad = parseFloat(input.val()) || 1;
            if (cantidad < 1) {
                cantidad = 1;
                input.val(1);
            }
            
            const subtotal = precio * cantidad;
            $(`#totalItem${filaId}`).text('$' + subtotal.toLocaleString('es-CO'));
            
            // Actualizar el hidden input
            $(`#filaItem${filaId} .hidden-cantidad`).val(cantidad);
            
            actualizarTotales();
        }

        function actualizarTotales() {
            let totalDebitos = 0;
            let totalCreditos = 0;

            $('#mostrarItems tr').each(function() {
                const fila = $(this);
                const tipoTexto = fila.find('td').eq(2).text().trim();
                const totalTexto = fila.find('.total-item').text().replace('$', '').replace(/\./g, '');
                const total = parseFloat(totalTexto) || 0;

                if (tipoTexto.includes('Débito')) {
                    totalDebitos += total;
                } else if (tipoTexto.includes('Crédito')) {
                    totalCreditos += total;
                }
            });

            const totalNeto = totalDebitos - totalCreditos;

            $('#totalDebitos').text('$' + totalDebitos.toLocaleString('es-CO'));
            $('#totalCreditos').text('$' + totalCreditos.toLocaleString('es-CO'));
            $('#totalNeto').text('$' + totalNeto.toLocaleString('es-CO'));
        }

        $('#cantidadItemNuevo').on('change', function() {
            const select = $('#items');
            if (select.val()) {
                agregarItemMasivo(select[0]);
            }
        });

        $('#formFacturacionMasiva').on('submit', function(e) {
            const tipoGrupo = $('#tipo_grupo').val();
            if (!tipoGrupo) {
                e.preventDefault();
                alert('Debe seleccionar un tipo de grupo');
                return false;
            }

            if (tipoGrupo === 'ESTUDIANTES') {
                const grados = $('#grados').val();
                if (!grados || grados.length === 0) {
                    e.preventDefault();
                    alert('Debe seleccionar al menos un grado para estudiantes');
                    return false;
                }
            }

            if (itemsSeleccionados.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un ítem para facturar');
                return false;
            }
        });
    </script>
</body>
</html>

