<?php
include("session.php");
$idPaginaInterna = 'DT0280';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

$id = '';
if (!empty($_GET['id'])) {
    $id = base64_decode($_GET['id'], true);
    // Si falla con strict mode, intentar sin strict
    if ($id === false || empty($id)) {
        $id = base64_decode($_GET['id']);
    }
}

// Validar que el ID no esté vacío
if (empty($id)) {
    echo '<script type="text/javascript">alert("ID de cuenta bancaria no proporcionado."); window.location.href="cuentas-bancarias.php";</script>';
    exit();
}

// Consulta directa para obtener los datos
// Usar un nombre de variable único para evitar conflictos con archivos incluidos
$datosCuentaBancaria = [];

// Verificar que la conexión existe
if (!isset($conexion) || !$conexion) {
    echo '<script type="text/javascript">alert("Error: No hay conexión a la base de datos."); window.location.href="cuentas-bancarias.php";</script>';
    exit();
}

$idEscapado = mysqli_real_escape_string($conexion, $id);

// Consulta simple solo por ID (sin filtros adicionales para evitar problemas)
$sql = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias WHERE cba_id='{$idEscapado}' LIMIT 1";
$consultaPorId = mysqli_query($conexion, $sql);

if ($consultaPorId === false) {
    // Error en la consulta
    $error = mysqli_error($conexion);
    echo '<script type="text/javascript">alert("Error al consultar la base de datos: '.htmlspecialchars($error).'"); window.location.href="cuentas-bancarias.php";</script>';
    exit();
}

if (mysqli_num_rows($consultaPorId) > 0) {
    $datosCuentaBancaria = mysqli_fetch_array($consultaPorId, MYSQLI_BOTH);
    
    // Verificar que el resultado es válido
    if (empty($datosCuentaBancaria) || !is_array($datosCuentaBancaria)) {
        echo '<script type="text/javascript">alert("Error: Los datos de la cuenta bancaria no están completos."); window.location.href="cuentas-bancarias.php";</script>';
        exit();
    }
    
    // Verificar que tenemos el cba_id
    if (empty($datosCuentaBancaria['cba_id'])) {
        echo '<script type="text/javascript">alert("Error: No se pudo obtener el ID de la cuenta bancaria."); window.location.href="cuentas-bancarias.php";</script>';
        exit();
    }
    
    // Verificar si la cuenta tiene transacciones (abonos o transferencias)
    $cuentaEnUso = Movimientos::validarCuentaBancariaEnUso($conexion, $config, $datosCuentaBancaria['cba_id']);
} else {
    // No se encontró el registro
    echo '<script type="text/javascript">alert("No se encontró la cuenta bancaria con ID: '.htmlspecialchars($id).'"); window.location.href="cuentas-bancarias.php";</script>';
    exit();
}
?>

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                                <div class="page-title">Editar Cuenta Bancaria</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="cuentas-bancarias.php" onClick="deseaRegresar(this)">Cuentas Bancarias</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Cuenta Bancaria</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                                <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
								<div class="panel">
									<header class="panel-heading panel-heading-purple">Editar Cuenta Bancaria</header>
                                	<div class="panel-body">
									<form name="formularioGuardar" action="cuentas-bancarias-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$id?>" name="cba_id">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Nombre <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="cba_nombre" class="form-control" value="<?=htmlspecialchars($datosCuentaBancaria['cba_nombre'] ?? '')?>" required <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Banco</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="cba_banco" class="form-control" value="<?=$datosCuentaBancaria['cba_banco'] ?? ''?>" <?=$disabledPermiso;?>>
                                            </div>

                                            <label class="col-sm-2 control-label">Número de Cuenta</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="cba_numero_cuenta" class="form-control" value="<?=$datosCuentaBancaria['cba_numero_cuenta'] ?? ''?>" <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select name="cba_tipo" class="form-control" required <?=$disabledPermiso;?>>
                                                    <option value="AHORROS" <?=($datosCuentaBancaria['cba_tipo'] ?? '') == 'AHORROS' ? 'selected' : ''?>>Ahorros</option>
                                                    <option value="CORRIENTE" <?=($datosCuentaBancaria['cba_tipo'] ?? '') == 'CORRIENTE' ? 'selected' : ''?>>Corriente</option>
                                                    <option value="NEOQUI" <?=($datosCuentaBancaria['cba_tipo'] ?? '') == 'NEOQUI' ? 'selected' : ''?>>Nequi</option>
                                                    <option value="DAVIPLATA" <?=($datosCuentaBancaria['cba_tipo'] ?? '') == 'DAVIPLATA' ? 'selected' : ''?>>Daviplata</option>
                                                    <option value="CAJA_METALICA" <?=($datosCuentaBancaria['cba_tipo'] ?? '') == 'CAJA_METALICA' ? 'selected' : ''?>>Caja Metálica</option>
                                                    <option value="OTRO" <?=($datosCuentaBancaria['cba_tipo'] ?? '') == 'OTRO' ? 'selected' : ''?>>Otro</option>
                                                </select>
                                            </div>

                                            <label class="col-sm-2 control-label">Método de Pago <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select name="cba_metodo_pago_asociado" class="form-control select2" required <?=$disabledPermiso;?>>
                                                    <?php
                                                    require_once(ROOT_PATH."/main-app/class/MediosPago.php");
                                                    $metodoPagoActual = trim(strtoupper($datosCuentaBancaria['cba_metodo_pago_asociado'] ?? ''));
                                                    echo MediosPago::generarOpcionesSelect($metodoPagoActual, true, 'Seleccione un método');
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Saldo Inicial</label>
                                            <div class="col-sm-4">
                                                <?php 
                                                $readonlySaldo = ($cuentaEnUso || $disabledPermiso) ? 'readonly' : '';
                                                $disabledSaldo = ($cuentaEnUso || $disabledPermiso) ? 'disabled' : '';
                                                ?>
                                                <input type="number" name="cba_saldo_inicial" class="form-control" step="0.01" min="0" value="<?=htmlspecialchars($datosCuentaBancaria['cba_saldo_inicial'] ?? '0', ENT_QUOTES)?>" <?=$readonlySaldo?> <?=$disabledSaldo?> placeholder="0.00">
                                                <?php if ($cuentaEnUso) { ?>
                                                    <small class="form-text text-muted" style="color: #e74c3c;">
                                                        <i class="fa fa-lock"></i> El saldo inicial no puede modificarse porque la cuenta tiene transacciones registradas (abonos o transferencias).
                                                    </small>
                                                <?php } ?>
                                            </div>

                                            <label class="col-sm-2 control-label">Estado</label>
                                            <div class="col-sm-4">
                                                <select name="cba_activa" class="form-control" <?=$disabledPermiso;?>>
                                                    <option value="1" <?=($datosCuentaBancaria['cba_activa'] ?? 1) == 1 ? 'selected' : ''?>>Activa</option>
                                                    <option value="0" <?=($datosCuentaBancaria['cba_activa'] ?? 1) == 0 ? 'selected' : ''?>>Inactiva</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-12 control-label">Observaciones</label>
                                            <div class="col-sm-12">
                                                <textarea name="cba_observaciones" class="form-control" rows="3" <?=$disabledPermiso;?>><?=$datosCuentaBancaria['cba_observaciones'] ?? ''?></textarea>
                                            </div>
                                        </div>
                                        
                                       
                                        <?php $botones = new botonesGuardar("cuentas-bancarias.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- end page container -->
        <?php require_once(ROOT_PATH."/main-app/compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
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
</body>
</html>

