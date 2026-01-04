<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0279';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

// Validar que el total neto sea mayor a 0 antes de actualizar
if (!empty($_POST['id'])) {
    $vlrAdicional = !empty($_POST["additional_value"]) ? floatval($_POST["additional_value"]) : 0;
    $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $_POST['id'], $vlrAdicional, TIPO_RECURRING);
    
    if ($totalNeto <= 0) {
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">alert("No se puede guardar la factura recurrente. El total neto debe ser mayor a cero. Verifique que los items débito sumen más que los items crédito."); window.location.href="factura-recurrente-editar.php?error=ER_DT_TOTAL_INVALIDO&id='.base64_encode($_POST['id']).'";</script>';
        exit();
    }
    
    // Obtener datos anteriores para auditoría (antes del UPDATE)
    $datosAnteriores = [];
    $consultaAnterior = mysqli_query($conexion, "SELECT id, user, date_start, date_finish, frequency, days_in_month, detail, additional_value, invoice_type, observation, is_deleted, responsible_user, institucion, year FROM ".BD_FINANCIERA.".recurring_invoices WHERE id='".mysqli_real_escape_string($conexion, $_POST['id'])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} LIMIT 1");
    if ($consultaAnterior && mysqli_num_rows($consultaAnterior) > 0) {
        $datosAnteriores = mysqli_fetch_assoc($consultaAnterior);
    }
}

try {
    Movimientos::actualizarRecurrente($conexion, $config, $_POST);
    
    // Registrar en auditoría (después del UPDATE, complementa el trigger)
    if (!empty($datosAnteriores) && !empty($_POST['id'])) {
        require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaFinanciera.php");
        
        // Obtener datos nuevos después del UPDATE
        $consultaNueva = mysqli_query($conexion, "SELECT id, user, date_start, date_finish, frequency, days_in_month, detail, additional_value, invoice_type, observation, is_deleted, responsible_user, institucion, year FROM ".BD_FINANCIERA.".recurring_invoices WHERE id='".mysqli_real_escape_string($conexion, $_POST['id'])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} LIMIT 1");
        $datosNuevos = [];
        if ($consultaNueva && mysqli_num_rows($consultaNueva) > 0) {
            $datosNuevos = mysqli_fetch_assoc($consultaNueva);
        }
        
        if (!empty($datosNuevos)) {
            // Detectar campos modificados
            $camposModificados = [];
            foreach ($datosNuevos as $campo => $valorNuevo) {
                if ($campo === 'institucion' || $campo === 'year') {
                    continue; // Campos que no se modifican
                }
                $valorAnterior = $datosAnteriores[$campo] ?? null;
                if ($valorAnterior != $valorNuevo) {
                    $camposModificados[$campo] = [
                        'anterior' => $valorAnterior,
                        'nuevo' => $valorNuevo
                    ];
                }
            }
            
            AuditoriaFinanciera::registrarActualizacion(
                'recurring_invoices',
                (string)$_POST['id'],
                $datosAnteriores,
                $datosNuevos,
                !empty($camposModificados) ? $camposModificados : null
            );
        }
    }
    
    include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="factura-recurrente-editar.php?success=SC_DT_2&id='.base64_encode($_POST['id']).'";</script>';
} catch (Exception $e) {
    echo '<script type="text/javascript">alert("Error: '.htmlspecialchars($e->getMessage()).'"); window.location.href="factura-recurrente-editar.php?error=ER_DT_CREATE&id='.base64_encode($_POST['id']).'";</script>';
}
exit();