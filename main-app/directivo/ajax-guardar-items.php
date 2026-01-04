<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0254';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

// Obtener datos del item seleccionado para devolver información completa
$datosItem = [];
$idItem = null; // item_id del item (PK y necesario para la FK)
if (!empty($_REQUEST['idItem'])) {
    // idItem ahora es directamente item_id (INT UNSIGNED)
    $idItem = (int)$_REQUEST['idItem'];
    
    // Obtener datos del item usando item_id
    try {
        $sqlBuscarItem = "SELECT * FROM ".BD_FINANCIERA.".items 
                         WHERE item_id=? AND institucion=? AND year=? LIMIT 1";
        $stmtBuscarItem = $conexionPDO->prepare($sqlBuscarItem);
        $stmtBuscarItem->bindParam(1, $idItem, PDO::PARAM_INT);
        $stmtBuscarItem->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtBuscarItem->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtBuscarItem->execute();
        $datosItem = $stmtBuscarItem->fetch(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'Error al buscar item: ' . $e->getMessage()]);
        exit();
    }
    
    // Validar que se encontró el item
    if ($idItem === null || empty($datosItem)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'No se pudo encontrar el item seleccionado.']);
        exit();
    }
}

// Obtener el tipo de transacción primero
$typeTransaction = !empty($_REQUEST['typeTransaction']) ? $_REQUEST['typeTransaction'] : TIPO_FACTURA;

// idTransaction puede ser INT (para TIPO_FACTURA) o STRING (para TIPO_RECURRING)
$idTransaction = null;
$facturaRecurrenteId = null;

if ($typeTransaction == TIPO_RECURRING) {
    // Para facturas recurrentes, necesitamos obtener el ID numérico desde recurring_invoices
    // El idTransaction viene como código alfanumérico, pero necesitamos el ID INT
    $codigoRecurrente = !empty($_REQUEST['idTransaction']) ? trim($_REQUEST['idTransaction']) : '';
    if (empty($codigoRecurrente)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'ID de transacción inválido.']);
        exit();
    }
    
    // Buscar el ID numérico de la factura recurrente usando el código
    try {
        $sqlBuscarRecurrente = "SELECT id FROM ".BD_FINANCIERA.".recurring_invoices 
                               WHERE id=? AND institucion=? AND year=? AND is_deleted=0 LIMIT 1";
        $stmtBuscarRecurrente = $conexionPDO->prepare($sqlBuscarRecurrente);
        // El campo 'id' en recurring_invoices puede ser VARCHAR o INT según el esquema
        // Intentamos como STRING primero
        $stmtBuscarRecurrente->bindParam(1, $codigoRecurrente, PDO::PARAM_STR);
        $stmtBuscarRecurrente->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtBuscarRecurrente->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
        $stmtBuscarRecurrente->execute();
        $recurringInvoice = $stmtBuscarRecurrente->fetch(PDO::FETCH_ASSOC);
        
        if ($recurringInvoice && !empty($recurringInvoice['id'])) {
            // El ID en recurring_invoices es INT, así que lo convertimos
            $facturaRecurrenteId = (int)$recurringInvoice['id'];
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => 'No se encontró la factura recurrente. Debe guardar la factura recurrente primero antes de agregar items.']);
            exit();
        }
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'Error al buscar factura recurrente: ' . $e->getMessage()]);
        exit();
    }
    
    // Para TIPO_RECURRING, id_transaction debe ser NULL
    $idTransaction = null;
} else {
    // Para facturas normales, idTransaction es un INT (fcu_id)
    $idTransaction = !empty($_REQUEST['idTransaction']) ? (int)$_REQUEST['idTransaction'] : 0;
    if ($idTransaction <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'ID de transacción inválido.']);
        exit();
    }
}

$creado = null;
$idAutoIncremental = null;
if(!empty($_REQUEST['itemModificar'])){
    $idItemModificar = (int)$_REQUEST['itemModificar']; // Es el id_autoincremental
    try {
        // Validar que el tax del item existe en la tabla taxes antes de usarlo
        $taxToUse = null;
        if (!empty($datosItem['tax']) && $datosItem['tax'] != '0' && $datosItem['tax'] != '') {
            try {
                $sqlVerificarTax = "SELECT id FROM ".BD_FINANCIERA.".taxes 
                                   WHERE id=? AND institucion=? AND year=? LIMIT 1";
                $stmtVerificar = $conexionPDO->prepare($sqlVerificarTax);
                $taxIdParaVerificar = (int)$datosItem['tax'];
                $stmtVerificar->bindParam(1, $taxIdParaVerificar, PDO::PARAM_INT);
                $stmtVerificar->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
                $stmtVerificar->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
                $stmtVerificar->execute();
                $taxExiste = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
                
                if ($taxExiste && !empty($taxExiste['id'])) {
                    $taxToUse = (int)$taxExiste['id']; // Convertir a int porque el campo es UNSIGNED INT
                }
            } catch(Exception $e) {
                // Si hay error al verificar, usar NULL por defecto
                $taxToUse = null;
            }
        }
        
        // Obtener item_type y application_time del item
        $itemType = !empty($datosItem['item_type']) ? $datosItem['item_type'] : 'D';
        $applicationTime = null;
        if ($itemType == 'C' && !empty($datosItem['application_time'])) {
            $applicationTime = $datosItem['application_time'];
        }
        
        // Obtener el nombre del item para guardarlo en item_name
        $nombreItem = !empty($datosItem['name']) ? $datosItem['name'] : '';
        
        if ($taxToUse === null) {
            $sql = "UPDATE ".BD_FINANCIERA.".transaction_items 
                    SET id_item=?, cantity=?, subtotal=?, price=?, discount=0, tax=NULL, item_type=?, application_time=?, item_name=? 
                    WHERE id_autoincremental=? AND institucion=? AND year=?";
        } else {
            $sql = "UPDATE ".BD_FINANCIERA.".transaction_items 
                    SET id_item=?, cantity=?, subtotal=?, price=?, discount=0, tax=?, item_type=?, application_time=?, item_name=? 
                    WHERE id_autoincremental=? AND institucion=? AND year=?";
        }
        
        $stmt = $conexionPDO->prepare($sql);
        // Usar item_id para la FK (transaction_items.id_item referencia items.item_id)
        $stmt->bindParam(1, $idItem, PDO::PARAM_INT);
        $stmt->bindParam(2, $_REQUEST['cantidad'], PDO::PARAM_STR);
        $stmt->bindParam(3, $_REQUEST['subtotal'], PDO::PARAM_STR);
        $stmt->bindParam(4, $_REQUEST['precio'], PDO::PARAM_STR);
        if ($taxToUse !== null) {
            $stmt->bindParam(5, $taxToUse, PDO::PARAM_INT);
            $stmt->bindParam(6, $itemType, PDO::PARAM_STR);
            $stmt->bindValue(7, $applicationTime, $applicationTime !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(8, $nombreItem, PDO::PARAM_STR);
            $stmt->bindParam(9, $idItemModificar, PDO::PARAM_INT);
            $stmt->bindParam(10, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(11, $_SESSION["bd"], PDO::PARAM_INT);
        } else {
            $stmt->bindParam(5, $itemType, PDO::PARAM_STR);
            $stmt->bindValue(6, $applicationTime, $applicationTime !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(7, $nombreItem, PDO::PARAM_STR);
            $stmt->bindParam(8, $idItemModificar, PDO::PARAM_INT);
            $stmt->bindParam(9, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(10, $_SESSION["bd"], PDO::PARAM_INT);
        }
        $stmt->execute();
        $idAutoIncremental = $idItemModificar; // Mantener el mismo id_autoincremental
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        exit();
    }
    $creado = 0;
}else{
    // id_autoincremental es AUTO_INCREMENT, no necesitamos generarlo manualmente
    try {
        // Validar que el tax del item existe en la tabla taxes
        // El campo tax en transaction_items debe referenciar taxes.id o ser NULL
        // Por defecto NULL (sin impuesto) para evitar violaciones de FK
        $taxDefault = null;
        if (!empty($datosItem['tax']) && $datosItem['tax'] != '0' && $datosItem['tax'] != '') {
            // Verificar que el tax existe en la tabla taxes
            try {
                $sqlVerificarTax = "SELECT id FROM ".BD_FINANCIERA.".taxes 
                                   WHERE id=? AND institucion=? AND year=? LIMIT 1";
                $stmtVerificar = $conexionPDO->prepare($sqlVerificarTax);
                $taxIdParaVerificar = (int)$datosItem['tax'];
                $stmtVerificar->bindParam(1, $taxIdParaVerificar, PDO::PARAM_INT);
                $stmtVerificar->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
                $stmtVerificar->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
                $stmtVerificar->execute();
                $taxExiste = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
                
                if ($taxExiste && !empty($taxExiste['id'])) {
                    $taxDefault = (int)$taxExiste['id']; // Convertir a int porque el campo es UNSIGNED INT
                }
            } catch(Exception $e) {
                // Si hay error al verificar, usar NULL por defecto
                $taxDefault = null;
            }
        }
        
        // Obtener item_type y application_time del item
        $itemType = !empty($datosItem['item_type']) ? $datosItem['item_type'] : 'D';
        $applicationTime = null;
        if ($itemType == 'C' && !empty($datosItem['application_time'])) {
            $applicationTime = $datosItem['application_time'];
        }
        
        // Obtener el nombre del item para guardarlo en item_name
        $nombreItem = !empty($datosItem['name']) ? $datosItem['name'] : '';
        
        // Construir el SQL según el tipo de transacción
        // Para TIPO_RECURRING usamos factura_recurrente_id, para TIPO_FACTURA usamos id_transaction
        // id_autoincremental es AUTO_INCREMENT, no se incluye en el INSERT
        if ($typeTransaction == TIPO_RECURRING) {
            // Para facturas recurrentes, usar factura_recurrente_id y dejar id_transaction como NULL
            if ($taxDefault === null) {
                $sql = "INSERT INTO ".BD_FINANCIERA.".transaction_items(
                    id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, price, tax, description, factura_recurrente_id, item_type, application_time, item_name
                ) VALUES (NULL, ?, 0, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?)";
            } else {
                $sql = "INSERT INTO ".BD_FINANCIERA.".transaction_items(
                    id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, price, tax, description, factura_recurrente_id, item_type, application_time, item_name
                ) VALUES (NULL, ?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            }
        } else {
            // Para facturas normales, usar id_transaction
            if ($taxDefault === null) {
                $sql = "INSERT INTO ".BD_FINANCIERA.".transaction_items(
                    id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, price, tax, description, item_type, application_time, item_name
                ) VALUES (?, ?, 0, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, ?)";
            } else {
                $sql = "INSERT INTO ".BD_FINANCIERA.".transaction_items(
                    id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, price, tax, description, item_type, application_time, item_name
                ) VALUES (?, ?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            }
        }
        
        $stmt = $conexionPDO->prepare($sql);
        $descripcionItem = !empty($datosItem['description']) ? $datosItem['description'] : '';
        
        if ($typeTransaction == TIPO_RECURRING) {
            // Para facturas recurrentes, bindear factura_recurrente_id
            $stmt->bindParam(1, $typeTransaction, PDO::PARAM_STR);
            $stmt->bindParam(2, $_REQUEST['cantidad'], PDO::PARAM_STR);
            $stmt->bindParam(3, $_REQUEST['subtotal'], PDO::PARAM_STR);
            $stmt->bindParam(4, $idItem, PDO::PARAM_INT);
            $stmt->bindParam(5, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(6, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->bindParam(7, $_REQUEST['precio'], PDO::PARAM_STR);
            
            if ($taxDefault === null) {
                $stmt->bindParam(8, $descripcionItem, PDO::PARAM_STR);
                $stmt->bindParam(9, $facturaRecurrenteId, PDO::PARAM_INT);
                $stmt->bindParam(10, $itemType, PDO::PARAM_STR);
                $stmt->bindValue(11, $applicationTime, $applicationTime !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindParam(12, $nombreItem, PDO::PARAM_STR);
            } else {
                $stmt->bindParam(8, $taxDefault, PDO::PARAM_INT);
                $stmt->bindParam(9, $descripcionItem, PDO::PARAM_STR);
                $stmt->bindParam(10, $facturaRecurrenteId, PDO::PARAM_INT);
                $stmt->bindParam(11, $itemType, PDO::PARAM_STR);
                $stmt->bindValue(12, $applicationTime, $applicationTime !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindParam(13, $nombreItem, PDO::PARAM_STR);
            }
        } else {
            // Para facturas normales, bindear id_transaction
            $stmt->bindParam(1, $idTransaction, PDO::PARAM_INT);
            $stmt->bindParam(2, $typeTransaction, PDO::PARAM_STR);
            $stmt->bindParam(3, $_REQUEST['cantidad'], PDO::PARAM_STR);
            $stmt->bindParam(4, $_REQUEST['subtotal'], PDO::PARAM_STR);
            // id_item es item_id (INT UNSIGNED) - referencia items.item_id
            $stmt->bindParam(5, $idItem, PDO::PARAM_INT);
            $stmt->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->bindParam(8, $_REQUEST['precio'], PDO::PARAM_STR);
            
            if ($taxDefault === null) {
                // Ya está NULL en el SQL, solo bindear la descripción
                $stmt->bindParam(9, $descripcionItem, PDO::PARAM_STR);
                $stmt->bindParam(10, $itemType, PDO::PARAM_STR);
                $stmt->bindValue(11, $applicationTime, $applicationTime !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindParam(12, $nombreItem, PDO::PARAM_STR);
            } else {
                // Bindear tax y descripción
                $stmt->bindParam(9, $taxDefault, PDO::PARAM_INT);
                $stmt->bindParam(10, $descripcionItem, PDO::PARAM_STR);
                $stmt->bindParam(11, $itemType, PDO::PARAM_STR);
                $stmt->bindValue(12, $applicationTime, $applicationTime !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindParam(13, $nombreItem, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        
        // Obtener el id_autoincremental del registro insertado (AUTO_INCREMENT)
        $idAutoIncremental = $conexionPDO->lastInsertId();
        
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        exit();
    }
    $creado = 1;
}

// Preparar respuesta con datos del item
$arrayIdInsercion = [
    "idInsercion" => isset($idAutoIncremental) && !empty($idAutoIncremental) ? $idAutoIncremental : 0, 
    "creado" => $creado,
    "precio" => !empty($datosItem['price']) ? $datosItem['price'] : $_REQUEST['precio'],
    "descripcion" => !empty($datosItem['description']) ? $datosItem['description'] : '',
    "tax" => !empty($datosItem['tax']) ? $datosItem['tax'] : '0',
    "item_type" => !empty($datosItem['item_type']) ? $datosItem['item_type'] : 'D',
    "application_time" => (!empty($datosItem['item_type']) && $datosItem['item_type'] == 'C') ? (!empty($datosItem['application_time']) ? $datosItem['application_time'] : 'ANTE_IMPUESTO') : null,
    "name" => !empty($datosItem['name']) ? $datosItem['name'] : ''
];

header('Content-Type: application/json');
echo json_encode($arrayIdInsercion);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");