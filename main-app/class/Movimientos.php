<?php
require_once("servicios/Servicios.php");
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

class Movimientos {


    /**
     * Lista todas  matrículas académicas con información adicional.
     *
     * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
     *
     * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
     */
    public static function listarTodos($parametrosArray = null)
    {
        global $config;
        if(empty($parametrosArray["institucion"])){
            $institucion=$config['conf_id_institucion'];
        }
        if(empty($parametrosArray["year"])){
            $year=$_SESSION["bd"];
        }
        $busqueda='';
        $sqlFinal ='';
        if(!empty($parametrosArray["valor"])){
            $busqueda=$parametrosArray["valor"];
            $sqlFinal = " AND (
                uss_id LIKE '%".$busqueda."%' 
                OR uss_nombre LIKE '%".$busqueda."%' 
                OR uss_nombre2 LIKE '%".$busqueda."%' 
                OR uss_apellido1 LIKE '%".$busqueda."%' 
                OR uss_apellido2 LIKE '%".$busqueda."%' 
                OR uss_usuario LIKE '%".$busqueda."%' 
                OR uss_email LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(uss_nombre), ' ',TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1), TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
                OR fcu_detalle LIKE '%".$busqueda."%' 
                OR fcu_observaciones LIKE '%".$busqueda."%'
                OR fcu_id LIKE '%".$busqueda."%'
            )";
        }
      $sqlFiltro ='';
      if(!empty($parametrosArray["filtro"])){
        $sqlFiltro =$parametrosArray["filtro"];
      }
      $sqlInicial = "SELECT fc.*, uss.*, fc.fcu_id AS id_nuevo_movimientos FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
      INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=fcu_usuario AND uss.institucion='$institucion' AND uss.year='$year'
	  WHERE fcu_id=fcu_id AND fc.institucion='$institucion' AND fc.year='$year' ".$sqlFinal." ".$sqlFiltro." 
      ORDER BY fcu_id";     
      $sql = $sqlInicial ;
      return Servicios::SelectSql($sql);
    }
  
    /**
     * Lista todas  matrículas académicas con información adicional.
     *
     * @param array|null $parametrosArray Arreglo de parámetros para filtrar la consulta (opcional).
     *
     * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
     */
    public static function calcuarValores($parametrosArray = null)
    {
        global $config;
        if(empty($parametrosArray["institucion"])){
            $institucion=$config['conf_id_institucion'];
        }
        if(empty($parametrosArray["year"])){
            $year=$_SESSION["bd"];
        }
        if(empty($parametrosArray["year"])){
            $year=$_SESSION["bd"];
        }
        if(empty($parametrosArray["tipo"])){
            $tipo='1';
        }else{
            $tipo=$parametrosArray["tipo"];   
        }
           
      $sqlInicial = "SELECT sum(fcu_valor)  as valor FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_tipo='$tipo' AND fcu_anulado='0' AND (fcu_status IS NULL OR fcu_status != '".EN_PROCESO."') AND institucion='$institucion' AND year='$year' ";     
      $sql = $sqlInicial ;
      return Servicios::SelectSql($sql);
    }
    /**
     * Calcula todos los totales de una factura según la lógica legal correcta
     * @param mysqli $conexion
     * @param array $config
     * @param string $idTransaction
     * @param float $valorAdicional
     * @param string $tipo
     * 
     * @return array Array con todos los valores calculados:
     *   - subtotal_bruto: Suma de (precio × cantidad) de items débito antes de descuentos
     *   - descuentos_items: Sumatoria de descuentos línea por línea en items débito
     *   - descuentos_comerciales_globales: Suma de créditos ANTE_IMPUESTO
     *   - subtotal_gravable: Subtotal Bruto - Descuentos Items - Descuentos Comerciales Globales
     *   - impuestos: Sumatoria de IVAs sobre base gravable de cada item débito
     *   - total_facturado: Subtotal Gravable + Impuestos
     *   - anticipos_saldos_favor: Suma de créditos POST_IMPUESTO
     *   - total_neto: Total Facturado - Anticipos + Valor Adicional
     *   - valor_adicional: Valor adicional proporcionado
    **/
    public static function calcularTotalesFactura (
        mysqli $conexion, 
        array $config, 
        string $idTransaction, 
        float $valorAdicional = 0, 
        string $tipo = TIPO_FACTURA
    )
    {
        // Valores iniciales
        $resultado = [
            'subtotal_bruto' => 0.0,
            'descuentos_items' => 0.0,
            'descuentos_comerciales_globales' => 0.0,
            'subtotal_gravable' => 0.0,
            'impuestos' => 0.0,
            'total_facturado' => 0.0,
            'anticipos_saldos_favor' => 0.0,
            'total_neto' => 0.0,
            'valor_adicional' => $valorAdicional
        ];

        try {
            // Construir WHERE según tipo de factura
            $sqlWhere = "";
            if ($tipo == TIPO_RECURRING) {
                $idTransactionEscapado = mysqli_real_escape_string($conexion, $idTransaction);
                if (is_numeric($idTransaction)) {
                    $sqlWhere = "ti.factura_recurrente_id = {$idTransactionEscapado}";
                } else {
                    $sqlBuscarId = "SELECT id FROM ".BD_FINANCIERA.".recurring_invoices 
                                   WHERE id='{$idTransactionEscapado}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} AND is_deleted=0 LIMIT 1";
                    $consultaId = mysqli_query($conexion, $sqlBuscarId);
                    $recurringData = mysqli_fetch_array($consultaId, MYSQLI_BOTH);
                    if ($recurringData && !empty($recurringData['id'])) {
                        $idNumerico = (int)$recurringData['id'];
                        $sqlWhere = "ti.factura_recurrente_id = {$idNumerico}";
                    } else {
                        $resultado['total_neto'] = $valorAdicional;
                        return $resultado;
                    }
                }
            } else {
                $idTransactionEscapado = mysqli_real_escape_string($conexion, $idTransaction);
                $idTransactionNum = (int)$idTransactionEscapado;
                $sqlWhere = "ti.id_transaction = {$idTransactionNum}";
            }
            
            // Obtener todos los items con sus detalles
            $consulta = mysqli_query($conexion, "SELECT 
                ti.price,
                ti.cantity,
                ti.discount,
                ti.subtotal,
                ti.tax,
                i.item_type,
                COALESCE(i.application_time, 'ANTE_IMPUESTO') AS application_time,
                tax.fee AS tax_fee
                FROM ".BD_FINANCIERA.".transaction_items ti
                LEFT JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti.id_item AND i.institucion = {$config['conf_id_institucion']} AND i.year = {$_SESSION["bd"]}
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id = ti.tax AND tax.institucion = {$config['conf_id_institucion']} AND tax.year = {$_SESSION["bd"]}
                WHERE {$sqlWhere}
                AND ti.type_transaction = '{$tipo}'
                AND ti.institucion = {$config['conf_id_institucion']}
                AND ti.year = {$_SESSION["bd"]}");
            
            if ($consulta && mysqli_num_rows($consulta) > 0) {
                while ($item = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                    $itemType = $item['item_type'] ?? 'D';
                    $isDebito = ($itemType == 'D');
                    $isCredito = ($itemType == 'C');
                    $applicationTime = $item['application_time'] ?? 'ANTE_IMPUESTO';
                    
                    $precio = floatval($item['price'] ?? 0);
                    $cantidad = floatval($item['cantity'] ?? 0);
                    $porcentajeDescuento = floatval($item['discount'] ?? 0);
                    $subtotal = floatval($item['subtotal'] ?? 0);
                    $taxFee = floatval($item['tax_fee'] ?? 0);
                    
                    if ($isDebito) {
                        // 1. Subtotal Bruto: precio × cantidad (antes de descuentos)
                        $precioPorCantidad = $precio * $cantidad;
                        $resultado['subtotal_bruto'] += $precioPorCantidad;
                        
                        // 2. Descuentos de Items: descuento línea por línea
                        $descuentoLinea = $precioPorCantidad * ($porcentajeDescuento / 100);
                        $resultado['descuentos_items'] += $descuentoLinea;
                        
                        // 5. Impuestos: sobre base gravable del item (después de descuento)
                        $baseGravableItem = $precioPorCantidad - $descuentoLinea;
                        if ($taxFee > 0) {
                            $impuestoItem = $baseGravableItem * ($taxFee / 100);
                            $resultado['impuestos'] += $impuestoItem;
                        }
                    } elseif ($isCredito) {
                        // 3. Descuentos Comerciales Globales (ANTE_IMPUESTO) o 7. Anticipos (POST_IMPUESTO)
                        if ($applicationTime == 'ANTE_IMPUESTO') {
                            $resultado['descuentos_comerciales_globales'] += abs($subtotal);
                        } else {
                            $resultado['anticipos_saldos_favor'] += abs($subtotal);
                        }
                    }
                }
            }
            
            // Calcular valores derivados
            // 4. Subtotal Gravable: Subtotal Bruto - Descuentos Items - Descuentos Comerciales Globales
            $resultado['subtotal_gravable'] = $resultado['subtotal_bruto'] - $resultado['descuentos_items'] - $resultado['descuentos_comerciales_globales'];
            
            // 6. Total Facturado: Subtotal Gravable + Impuestos
            $resultado['total_facturado'] = $resultado['subtotal_gravable'] + $resultado['impuestos'];
            
            // 8. Total Neto: Total Facturado - Anticipos + Valor Adicional
            $resultado['total_neto'] = $resultado['total_facturado'] - $resultado['anticipos_saldos_favor'] + $valorAdicional;
            
            // Redondear todos los valores a 2 decimales
            foreach ($resultado as $key => $value) {
                $resultado[$key] = round($value, 2);
            }
            
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            // En caso de error, retornar valores con total_neto = valor_adicional
            $resultado['total_neto'] = $valorAdicional;
        }

        return $resultado;
    }

    /**
     * Este metodo me calcula el total neto de un Movimiento
     * @param mysqli $conexion
     * @param array $config
     * @param string $idTransaction
     * @param float $valorAdicional
     * @param string $tipo
     * 
     * @return float $totalNeto
    **/
    public static function calcularTotalNeto (
        mysqli $conexion, 
        array $config, 
        string $idTransaction, 
        float $valorAdicional = 0, 
        string $tipo = TIPO_FACTURA
    )
    {
        // Usar el método centralizado y retornar solo el total_neto
        $totales = self::calcularTotalesFactura($conexion, $config, $idTransaction, $valorAdicional, $tipo);
        return $totales['total_neto'];
    }

    /**
     * Este metodo me trae los items de una factura
     * @param mysqli $conexion
     * @param array $config
     * @param string $idTransaction
     * @param string $tipo
     * 
     * @return mysqli_result $consulta
    **/
    public static function listarItemsTransaction (
        mysqli $conexion, 
        array $config, 
        string $idTransaction, 
        string $tipo = TIPO_FACTURA
    )
    {
        try {
            // item_id es ahora el PK de items (AUTO_INCREMENT INT UNSIGNED)
            // Para TIPO_RECURRING, buscar por factura_recurrente_id, para TIPO_FACTURA por id_transaction
            if ($tipo == TIPO_RECURRING) {
                // Para facturas recurrentes, idTransaction puede venir como código alfanumérico
                // Necesitamos buscar el ID numérico en recurring_invoices
                $idTransactionEscapado = mysqli_real_escape_string($conexion, $idTransaction);
                
                // Primero obtener el ID numérico de la factura recurrente
                $sqlBuscarId = "SELECT id FROM ".BD_FINANCIERA.".recurring_invoices 
                               WHERE id='{$idTransactionEscapado}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} AND is_deleted=0 LIMIT 1";
                $consultaId = mysqli_query($conexion, $sqlBuscarId);
                $recurringData = mysqli_fetch_array($consultaId, MYSQLI_BOTH);
                
                if ($recurringData && !empty($recurringData['id'])) {
                    $facturaRecurrenteId = (int)$recurringData['id'];
                    
                    // Buscar items por factura_recurrente_id
                    // Orden: primero débitos (D), luego créditos (C)
                    $consulta = mysqli_query($conexion, "SELECT ti.id_autoincremental AS idtx, i.item_id AS idit, i.name, i.price AS priceItem, ti.price AS priceTransaction, ti.cantity, ti.subtotal, ti.description, ti.discount, ti.tax, i.item_type, i.application_time
                    FROM ".BD_FINANCIERA.".transaction_items ti
                    INNER JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti.id_item AND i.institucion = {$config['conf_id_institucion']} AND i.year = {$_SESSION["bd"]}
                    WHERE ti.factura_recurrente_id = {$facturaRecurrenteId}
                    AND ti.type_transaction = '{$tipo}'
                    AND ti.institucion = {$config['conf_id_institucion']}
                    AND ti.year = {$_SESSION["bd"]}
                    ORDER BY i.item_type ASC, ti.id_autoincremental");
                } else {
                    // Si no se encuentra la factura recurrente, retornar consulta vacía
                    $consulta = mysqli_query($conexion, "SELECT NULL WHERE 1=0");
                }
            } else {
                // Para facturas normales, idTransaction es fcu_id (INT UNSIGNED)
                $idTransactionNum = (int)$idTransaction;
                
                // id_transaction es fcu_id (INT UNSIGNED)
                // Orden: primero débitos (D), luego créditos (C)
                $consulta = mysqli_query($conexion, "SELECT ti.id_autoincremental AS idtx, i.item_id AS idit, i.name, i.price AS priceItem, ti.price AS priceTransaction, ti.cantity, ti.subtotal, ti.description, ti.discount, ti.tax, i.item_type, i.application_time
                FROM ".BD_FINANCIERA.".transaction_items ti
                INNER JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti.id_item AND i.institucion = {$config['conf_id_institucion']} AND i.year = {$_SESSION["bd"]}
                WHERE ti.id_transaction = {$idTransactionNum}
                AND ti.type_transaction = '{$tipo}'
                AND ti.institucion = {$config['conf_id_institucion']}
                AND ti.year = {$_SESSION["bd"]}
                ORDER BY i.item_type ASC, ti.id_autoincremental");
            }
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            // Retornar consulta vacía en caso de error
            $consulta = mysqli_query($conexion, "SELECT NULL WHERE 1=0");
        }

        return $consulta;
    }

    /**
     * Este metodo me trae todos los items
     * @param mysqli $conexion
     * @param array $config
     * @param string|null $itemType Filtro opcional por tipo: 'D'=Débito, 'C'=Crédito
     * 
     * @return mysqli_result $consulta
    **/
    public static function listarItems (
        mysqli $conexion, 
        array $config,
        ?string $itemType = null
    )
    {
        $filtroTipo = '';
        if (!empty($itemType) && ($itemType == 'D' || $itemType == 'C')) {
            $filtroTipo = " AND item_type = '{$itemType}'";
        }
        
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".items WHERE status=0 AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}{$filtroTipo}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me guarda un item
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * 
     * @return string $codigo
    **/
    public static function guardarItems (
        mysqli $conexion, 
        array $config, 
        array $POST
    )
    {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $itemType = !empty($POST["item_type"]) ? $POST["item_type"] : 'D';
        
        // Si es crédito, obtener application_time, sino NULL
        $applicationTime = null;
        if ($itemType == 'C') {
            $applicationTime = !empty($POST["application_time"]) ? $POST["application_time"] : 'ANTE_IMPUESTO';
        }
        
        try {
            // id_order es AUTO_INCREMENT, no se incluye en el INSERT
            $sql = "INSERT INTO ".BD_FINANCIERA.".items (name, price, description, item_type, application_time, institucion, year) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $POST["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(2, $POST["precio"], PDO::PARAM_STR);
            $stmt->bindParam(3, $POST["descrip"], PDO::PARAM_STR);
            $stmt->bindParam(4, $itemType, PDO::PARAM_STR);
            if ($applicationTime !== null) {
                $stmt->bindParam(5, $applicationTime, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(5, null, PDO::PARAM_NULL);
            }
            $stmt->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
            
            // Retornar el id_order generado (AUTO_INCREMENT)
            $codigo = $conexionPDO->lastInsertId();
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            $codigo = null;
        }

        return $codigo;
    }

    /**
     * Este metodo me trae la informacion de un item
     * @param mysqli $conexion
     * @param array $config
     * @param string $idItem
     * 
     * @return array $resultado
    **/
    public static function traerDatosItems (
        mysqli $conexion, 
        array $config,
        string $idItem
    )
    {
        $resultado = [];
        try {
            // item_id es INT UNSIGNED, convertir a int para la consulta
            $idItemInt = (int)$idItem;
            
            if ($idItemInt <= 0) {
                error_log("traerDatosItems: ID inválido recibido: {$idItem} (convertido a int: {$idItemInt})");
                return $resultado;
            }
            
            // year puede ser string o int, asegurarse de que sea string para la comparación
            $year = (string)$_SESSION["bd"];
            $sql = "SELECT * FROM ".BD_FINANCIERA.".items WHERE item_id={$idItemInt} AND institucion = {$config['conf_id_institucion']} AND year = '{$year}'";
            $consulta = mysqli_query($conexion, $sql);
            
            // Verificar si hubo error en la consulta
            if (!$consulta) {
                $errorSQL = mysqli_error($conexion);
                error_log("Error SQL en traerDatosItems: {$errorSQL}. Query: {$sql}");
            }
        } catch (Exception $e) {
            error_log("Excepción en traerDatosItems: " . $e->getMessage());
            include("../compartido/error-catch-to-report.php");
        }
        
        if ($consulta && mysqli_num_rows($consulta) > 0) {
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        } else {
            error_log("traerDatosItems: No se encontraron resultados para item_id={$idItemInt}, institucion={$config['conf_id_institucion']}, year={$_SESSION["bd"]}");
        }

        return $resultado;
    }

    /**
     * Este metodo me actualiza un item
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
    **/
    public static function actualizarItems (
        mysqli $conexion, 
        array $config, 
        array $POST
    )
    {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $itemType = !empty($POST["item_type"]) ? $POST["item_type"] : 'D';
        
        // Si es crédito, obtener application_time, sino NULL
        $applicationTime = null;
        if ($itemType == 'C') {
            $applicationTime = !empty($POST["application_time"]) ? $POST["application_time"] : 'ANTE_IMPUESTO';
        }

        try {
            // item_id es INT UNSIGNED, convertir a int para la consulta
            $itemId = (int)$POST["id"];
            
            if ($itemId <= 0) {
                throw new Exception("ID de item inválido: " . ($POST["id"] ?? 'N/A'));
            }
            
            // year es char(4), convertir a string
            $year = (string)$_SESSION["bd"];
            
            $sql = "UPDATE ".BD_FINANCIERA.".items SET name=?, price=?, description=?, item_type=?, application_time=? WHERE item_id=? AND institucion=? AND year=?";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $POST["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(2, $POST["precio"], PDO::PARAM_STR);
            $stmt->bindParam(3, $POST["descrip"], PDO::PARAM_STR);
            $stmt->bindParam(4, $itemType, PDO::PARAM_STR);
            if ($applicationTime !== null) {
                $stmt->bindParam(5, $applicationTime, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(5, null, PDO::PARAM_NULL);
            }
            $stmt->bindParam(6, $itemId, PDO::PARAM_INT);
            $stmt->bindParam(7, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(8, $year, PDO::PARAM_STR);
            $stmt->execute();
            
            // Verificar que se actualizó al menos una fila
            if ($stmt->rowCount() === 0) {
                error_log("actualizarItems: No se actualizó ninguna fila. item_id={$itemId}, institucion={$config['conf_id_institucion']}, year={$year}");
            }
        } catch (PDOException $e) {
            $errorMsg = "Error PDO en actualizarItems: " . $e->getMessage() . " | Código: " . $e->getCode();
            error_log($errorMsg);
            throw new Exception($errorMsg);
        } catch (Exception $e) {
            error_log("Error en actualizarItems: " . $e->getMessage());
            throw $e; // Re-lanzar para que el script que llama pueda manejarlo
        }
    }

    /**
     * Este metodo me actualiza un item
     * @param mysqli $conexion
     * @param array $config
     * @param string $idItem
    **/
    public static function eliminarItems (
        mysqli $conexion, 
        array $config, 
        string $idItem
    )
    {
        try {
            // idItem ahora es id_order (INT)
            $idOrder = (int)$idItem;
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".items SET status=1 WHERE id_order={$idOrder} AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me consulta si existe un item en la tabla de relación con las transacciones
     * @param mysqli $conexion
     * @param array $config
     * @param string $idItem
     * 
     * @return int $num
    **/
    public static function validarExistenciaItemsEnTransaction (
        mysqli $conexion, 
        array $config, 
        string $idItem
    )
    {
        // Validar que idItem no esté vacío
        if (empty($idItem)) {
            return 0;
        }

        try {
            // item_id es INT UNSIGNED, convertir a int para la consulta
            $idItemInt = (int)$idItem;
            $consulta = mysqli_query($conexion, "SELECT id_item FROM ".BD_FINANCIERA.".transaction_items WHERE id_item={$idItemInt} AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return 0;
        }
        
        if (!$consulta) {
            return 0;
        }
        
        $num = mysqli_num_rows($consulta);

        return $num;
    }

    /**
     * Este metodo me trae todos los abonos
     * @param mysqli $conexion
     * @param array $config
     * 
     * @return mysqli_result $consulta
    **/
    /**
     * Lista todos los abonos
     * ACTUALIZADO: Usa payments_invoiced consolidado (sin tabla payments)
     * @param mysqli $conexion
     * @param array $config
     * @return mysqli_result
     */
    public static function listarAbonos (
        mysqli $conexion, 
        array $config
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT 
                pi.id, 
                pi.id as cod_payment,
                pi.payment,
                pi.fecha_registro as registration_date, 
                pi.responsible_user, 
                pi.payment_user, 
                pi.invoiced, 
                pi.type_payments, 
                pi.payment_tipo,
                pi.payment_method, 
                pi.attachment as voucher, 
                pi.is_deleted,
                pi.payment_cuenta_bancaria_id,
                cba.cba_nombre as cuenta_bancaria_nombre,
                fc.fcu_id as numeroFactura,
                fc.fcu_id as fcu_id_factura,
                fc.fcu_consecutivo,
                uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2
            FROM ".BD_FINANCIERA.".payments_invoiced pi
            LEFT JOIN ".BD_GENERAL.".usuarios uss 
                ON uss.uss_id=pi.responsible_user 
                AND uss.institucion={$config['conf_id_institucion']} 
                AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_FINANCIERA.".finanzas_cuentas fc
                ON fc.fcu_id=pi.invoiced
                AND fc.institucion=pi.institucion
                AND fc.year=pi.year
            LEFT JOIN ".BD_FINANCIERA.".finanzas_cuentas_bancarias cba
                ON cba.cba_id=pi.payment_cuenta_bancaria_id
                AND cba.institucion=pi.institucion
                AND cba.year=pi.year
            WHERE 
                pi.institucion = {$config['conf_id_institucion']} 
            AND pi.year = {$_SESSION["bd"]}
            ORDER BY pi.id DESC
            ");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me trae las facturas para listar en un select
     * @param mysqli            $conexion
     * @param array             $config
     * @param string            $filtro || OPCIONAL
     * 
     * @return mysqli_result    $consulta
    **/
    public static function listarInvoicedSelect (
        mysqli  $conexion, 
        array   $config, 
        string   $filtro = ""
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas fcu
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=fcu_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ADMIN.".localidad_ciudades ON ciu_id=uss_lugar_nacimiento
            LEFT JOIN ".BD_ADMIN.".localidad_departamentos ON dep_id=ciu_departamento
            WHERE fcu_anulado=0 AND (fcu.fcu_status IS NULL OR fcu.fcu_status != '".EN_PROCESO."') {$filtro} AND fcu.institucion = {$config['conf_id_institucion']} AND fcu.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me guarda un abono
     * ACTUALIZADO: Usa payments_invoiced consolidado (sin tabla payments)
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * @param array $FILES
     * 
     * @return string $idRegistro
    **/
    public static function guardarAbonos (
        mysqli $conexion, 
        array $config, 
        array $POST, 
        array $FILES
    )
    {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        require_once(ROOT_PATH."/main-app/class/Utilidades.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        try {
            $conexionPDO->beginTransaction();
            
            $comprobante = '';
            if (!empty($FILES['comprobante']['name'])) {
                $destino = ROOT_PATH.'/main-app/files/comprobantes';
                if (!file_exists($destino)) {
                    @mkdir($destino, 0777, true);
                }
                $explode = explode(".", $FILES['comprobante']['name']);
                $extension = end($explode);
                $comprobante = uniqid('abono_'.$POST["cliente"].'_') . "." . $extension;
                move_uploaded_file($FILES['comprobante']['tmp_name'], $destino . "/" . $comprobante);
            }

            // Preparar fechas
            $fechaRegistro = date('Y-m-d H:i:s'); // Automática
            $fechaDocumento = null; // Fecha del documento (llenada por usuario)
            
            if (!empty($POST["fecha"])) {
                $fecha = DateTime::createFromFormat('Y-m-d\TH:i', $POST["fecha"]);
                if ($fecha !== false) {
                    $fechaDocumento = $fecha->format('Y-m-d');
                } else {
                    $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $POST["fecha"]);
                    if ($fecha !== false) {
                        $fechaDocumento = $fecha->format('Y-m-d');
                    } else {
                        $fecha = DateTime::createFromFormat('Y-m-d', $POST["fecha"]);
                        if ($fecha !== false) {
                            $fechaDocumento = $fecha->format('Y-m-d');
                        }
                    }
                }
            }
            
            // Validar fecha_documento: no futura, no mayor a 1 año en el pasado
            if ($fechaDocumento !== null) {
                $fechaActual = new DateTime();
                $fechaDoc = new DateTime($fechaDocumento);
                $fechaLimite = (clone $fechaActual)->modify('-1 year');
                
                if ($fechaDoc > $fechaActual) {
                    throw new Exception("La fecha del documento no puede ser futura.");
                }
                if ($fechaDoc < $fechaLimite) {
                    throw new Exception("La fecha del documento no puede ser mayor a un año en el pasado.");
                }
            }
            
            $tipoTransaccion = trim($POST["tipoTransaccion"] ?? '');
            $paymentTipo = 'INGRESO'; // Por defecto es INGRESO, se puede cambiar según lógica de negocio
            
            // Obtener fcu_id de la factura si es tipo INVOICE
            // Si viene idFactura, buscar su fcu_id en finanzas_cuentas
            $invoicedFcuId = null;
            if ($tipoTransaccion == INVOICE && !empty($POST["cliente"])) {
                // El cliente puede ser fcu_id directamente
                $consultaFactura = mysqli_query($conexion, "SELECT fcu_id FROM ".BD_FINANCIERA.".finanzas_cuentas 
                    WHERE fcu_id='".mysqli_real_escape_string($conexion, $POST["cliente"])."'
                    AND institucion = {$config['conf_id_institucion']} 
                    AND year = {$_SESSION["bd"]} 
                    LIMIT 1");
                if ($consultaFactura && mysqli_num_rows($consultaFactura) > 0) {
                    $factura = mysqli_fetch_array($consultaFactura, MYSQLI_BOTH);
                    $invoicedFcuId = $factura['fcu_id'] ?? null;
                }
            }
            
            // PROCESAR ABONOS A FACTURAS (TIPO INVOICE)
            // Puede venir en formato JSON o como array directo del POST
            $abonosAFacturas = [];
            if ($tipoTransaccion == INVOICE) {
                // Primero intentar desde JSON
                if (!empty($POST['abonos_facturas_json'])) {
                    $abonosJson = json_decode($POST['abonos_facturas_json'], true);
                    if (is_array($abonosJson) && count($abonosJson) > 0) {
                        foreach ($abonosJson as $abono) {
                            if (!empty($abono['idFactura']) && !empty($abono['valorAbono'])) {
                                // Buscar fcu_id de la factura
                                $consultaFactura = mysqli_query($conexion, "SELECT fcu_id FROM ".BD_FINANCIERA.".finanzas_cuentas 
                                    WHERE fcu_id='".mysqli_real_escape_string($conexion, $abono['idFactura'])."'
                                    AND institucion = {$config['conf_id_institucion']} 
                                    AND year = {$_SESSION["bd"]} 
                                    LIMIT 1");
                                $fcuIdFactura = null;
                                if ($consultaFactura && mysqli_num_rows($consultaFactura) > 0) {
                                    $factura = mysqli_fetch_array($consultaFactura, MYSQLI_BOTH);
                                    $fcuIdFactura = $factura['fcu_id'] ?? null;
                                }
                                
                                if ($fcuIdFactura) {
                                    $abonosAFacturas[] = [
                                        'idFactura' => $fcuIdFactura, // Usar fcu_id
                                        'valorAbono' => floatval($abono['valorAbono'])
                                    ];
                                }
                            }
                        }
                    }
                }
                
                // Si no hay datos en JSON, intentar desde array directo del POST
                if (empty($abonosAFacturas) && !empty($POST['abono_factura']) && is_array($POST['abono_factura'])) {
                    foreach ($POST['abono_factura'] as $idFactura => $valorAbono) {
                        $valorAbono = floatval($valorAbono);
                        if ($valorAbono > 0) {
                            // Buscar fcu_id de la factura
                            $consultaFactura = mysqli_query($conexion, "SELECT fcu_id FROM ".BD_FINANCIERA.".finanzas_cuentas 
                                WHERE fcu_id='".mysqli_real_escape_string($conexion, $idFactura)."'
                                AND institucion = {$config['conf_id_institucion']} 
                                AND year = {$_SESSION["bd"]} 
                                LIMIT 1");
                            $fcuIdFactura = null;
                            if ($consultaFactura && mysqli_num_rows($consultaFactura) > 0) {
                                $factura = mysqli_fetch_array($consultaFactura, MYSQLI_BOTH);
                                $fcuIdFactura = $factura['fcu_id'] ?? null;
                            }
                            
                            if ($fcuIdFactura) {
                                $abonosAFacturas[] = [
                                    'idFactura' => $fcuIdFactura, // Usar fcu_id
                                    'valorAbono' => $valorAbono
                                ];
                            }
                        }
                    }
                }
                
                // Validar saldos pendientes para cada abono
                foreach ($abonosAFacturas as $index => $abono) {
                    try {
                        $totalNetoFactura = self::calcularTotalNeto($conexion, $config, $abono['idFactura'], 0);
                        $totalAbonadoFactura = self::calcularTotalAbonado($conexion, $config, $abono['idFactura']);
                        $saldoPendiente = $totalNetoFactura - $totalAbonadoFactura;
                        
                        if ($abono['valorAbono'] > $saldoPendiente + 0.5) {
                            throw new Exception("El abono de $" . number_format($abono['valorAbono'], 0, ",", ".") . 
                                " a la factura {$abono['idFactura']} excede el saldo pendiente de $" . 
                                number_format($saldoPendiente, 0, ",", "."));
                        }
                    } catch (Exception $e) {
                        // Si hay error al calcular, lanzar excepción con mensaje claro
                        throw new Exception("Error al validar factura {$abono['idFactura']}: " . $e->getMessage());
                    }
                }
            }
            
            // Validar que haya al menos un abono si es tipo INVOICE
            if ($tipoTransaccion == INVOICE && empty($abonosAFacturas)) {
                throw new Exception("Debes ingresar al menos un valor de abono a una factura.");
            }
            
            // Obtener datos del usuario asociado al pago
            $paymentUser = !empty($POST["cliente"]) ? trim($POST["cliente"]) : null;
            
            // Obtener cuenta bancaria
            $cuentaBancariaId = !empty($POST["cuenta_bancaria_id"]) ? trim($POST["cuenta_bancaria_id"]) : null;
            if ($cuentaBancariaId !== null && $cuentaBancariaId !== '') {
                $consultaCuenta = mysqli_query($conexion, "SELECT cba_id FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
                    WHERE cba_id='".mysqli_real_escape_string($conexion, $cuentaBancariaId)."' 
                    AND institucion = {$config['conf_id_institucion']} 
                    AND year = {$_SESSION["bd"]} 
                    LIMIT 1");
                if (!$consultaCuenta || mysqli_num_rows($consultaCuenta) == 0) {
                    $cuentaBancariaId = null;
                }
            }
            
            // Obtener método de pago
            $paymentMethod = !empty($POST["metodoPago"]) ? trim($POST["metodoPago"]) : null;
            
            // Obtener observaciones y notas
            $observacion = !empty($POST["obser"]) ? trim($POST["obser"]) : null;
            $notas = !empty($POST["notas"]) ? trim($POST["notas"]) : null;
            
            // 1. INSERTAR en payments_invoiced (para tipo INVOICE - abonos a facturas)
            if ($tipoTransaccion == INVOICE && !empty($abonosAFacturas)) {
                $sqlInvoiced = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced (
                    responsible_user, payment_user, type_payments, payment_tipo, payment_method, 
                    payment_cuenta_bancaria_id, invoiced, payment, observation, attachment, note,
                    fecha_registro, fecha_documento, institucion, year
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtInvoiced = $conexionPDO->prepare($sqlInvoiced);
                
                foreach ($abonosAFacturas as $abono) {
                    $stmtInvoiced->bindValue(1, $_SESSION["id"], PDO::PARAM_STR);
                    $stmtInvoiced->bindValue(2, $paymentUser, $paymentUser ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmtInvoiced->bindValue(3, $tipoTransaccion, PDO::PARAM_STR);
                    $stmtInvoiced->bindValue(4, $paymentTipo, PDO::PARAM_STR);
                    $stmtInvoiced->bindValue(5, $paymentMethod, $paymentMethod ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmtInvoiced->bindValue(6, $cuentaBancariaId, $cuentaBancariaId ? PDO::PARAM_INT : PDO::PARAM_NULL);
                    $stmtInvoiced->bindValue(7, $abono['idFactura'], PDO::PARAM_INT); // fcu_id de la factura
                    $stmtInvoiced->bindValue(8, $abono['valorAbono'], PDO::PARAM_STR);
                    $stmtInvoiced->bindValue(9, $observacion, $observacion ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmtInvoiced->bindValue(10, $comprobante, $comprobante ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmtInvoiced->bindValue(11, $notas, $notas ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmtInvoiced->bindValue(12, $fechaRegistro, PDO::PARAM_STR);
                    $stmtInvoiced->bindValue(13, $fechaDocumento, $fechaDocumento ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmtInvoiced->bindValue(14, $config['conf_id_institucion'], PDO::PARAM_INT);
                    $stmtInvoiced->bindValue(15, $_SESSION["bd"], PDO::PARAM_INT);
                    $stmtInvoiced->execute();
                }
            }
            
            // 2. INSERTAR conceptos contables si es tipo ACCOUNT (pagos sin factura)
            if ($tipoTransaccion == ACCOUNT && !empty($POST["conceptos_contables_json"])) {
                $conceptos = json_decode($POST["conceptos_contables_json"], true);
                
                if (is_array($conceptos) && count($conceptos) > 0) {
                    $sqlConcepto = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced (
                        responsible_user, payment_user, type_payments, payment_tipo, payment_method,
                        payment_cuenta_bancaria_id, invoiced, payment, cantity, subtotal, description,
                        observation, attachment, note, fecha_registro, fecha_documento, institucion, year
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmtConcepto = $conexionPDO->prepare($sqlConcepto);
                    
                    foreach ($conceptos as $concepto) {
                        if (!empty($concepto['concepto']) && floatval($concepto['precio']) > 0) {
                            // Para abonos independientes (ACCOUNT), invoiced debe ser NULL
                            $invoicedValue = null;
                            
                            // Construir la descripción completa: concepto + descripción adicional
                            $descripcionCompleta = $concepto['concepto'];
                            if (!empty($concepto['descripcion'])) {
                                $descripcionCompleta .= ' - ' . $concepto['descripcion'];
                            }
                            
                            $stmtConcepto->bindValue(1, $_SESSION["id"], PDO::PARAM_STR);
                            $stmtConcepto->bindValue(2, $paymentUser, $paymentUser ? PDO::PARAM_STR : PDO::PARAM_NULL);
                            $stmtConcepto->bindValue(3, $tipoTransaccion, PDO::PARAM_STR);
                            $stmtConcepto->bindValue(4, $paymentTipo, PDO::PARAM_STR);
                            $stmtConcepto->bindValue(5, $paymentMethod, $paymentMethod ? PDO::PARAM_STR : PDO::PARAM_NULL);
                            $stmtConcepto->bindValue(6, $cuentaBancariaId, $cuentaBancariaId ? PDO::PARAM_INT : PDO::PARAM_NULL);
                            $stmtConcepto->bindValue(7, $invoicedValue, PDO::PARAM_NULL); // invoiced NULL para abonos independientes
                            $stmtConcepto->bindValue(8, $concepto['precio'], PDO::PARAM_STR); // payment
                            $stmtConcepto->bindValue(9, intval($concepto['cantidad']), PDO::PARAM_INT); // cantity
                            $stmtConcepto->bindValue(10, $concepto['subtotal'], PDO::PARAM_STR); // subtotal
                            $stmtConcepto->bindValue(11, $descripcionCompleta, PDO::PARAM_STR); // description (concepto + descripción)
                            $stmtConcepto->bindValue(12, $observacion, $observacion ? PDO::PARAM_STR : PDO::PARAM_NULL);
                            $stmtConcepto->bindValue(13, $comprobante, $comprobante ? PDO::PARAM_STR : PDO::PARAM_NULL);
                            $stmtConcepto->bindValue(14, $notas, $notas ? PDO::PARAM_STR : PDO::PARAM_NULL);
                            $stmtConcepto->bindValue(15, $fechaRegistro, PDO::PARAM_STR);
                            $stmtConcepto->bindValue(16, $fechaDocumento, $fechaDocumento ? PDO::PARAM_STR : PDO::PARAM_NULL);
                            $stmtConcepto->bindValue(17, $config['conf_id_institucion'], PDO::PARAM_INT);
                            $stmtConcepto->bindValue(18, $_SESSION["bd"], PDO::PARAM_INT);
                            $stmtConcepto->execute();
                        }
                    }
                }
            }
            
            // Si no hay abonos ni conceptos, crear un registro base (pago esporádico sin factura ni concepto)
            if (empty($abonosAFacturas) && empty($POST["conceptos_contables_json"])) {
                $sqlBase = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced (
                    responsible_user, payment_user, type_payments, payment_tipo, payment_method,
                    payment_cuenta_bancaria_id, invoiced, observation, attachment, note,
                    fecha_registro, fecha_documento, institucion, year
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtBase = $conexionPDO->prepare($sqlBase);
                $stmtBase->bindValue(1, $_SESSION["id"], PDO::PARAM_STR);
                $stmtBase->bindValue(2, $paymentUser, $paymentUser ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtBase->bindValue(3, $tipoTransaccion ?: 'ACCOUNT', PDO::PARAM_STR);
                $stmtBase->bindValue(4, $paymentTipo, PDO::PARAM_STR);
                $stmtBase->bindValue(5, $paymentMethod, $paymentMethod ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtBase->bindValue(6, $cuentaBancariaId, $cuentaBancariaId ? PDO::PARAM_INT : PDO::PARAM_NULL);
                $stmtBase->bindValue(7, null, PDO::PARAM_NULL); // invoiced NULL para pagos sin factura
                $stmtBase->bindValue(8, $observacion, $observacion ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtBase->bindValue(9, $comprobante, $comprobante ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtBase->bindValue(10, $notas, $notas ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtBase->bindValue(11, $fechaRegistro, PDO::PARAM_STR);
                $stmtBase->bindValue(12, $fechaDocumento, $fechaDocumento ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmtBase->bindValue(13, $config['conf_id_institucion'], PDO::PARAM_INT);
                $stmtBase->bindValue(14, $_SESSION["bd"], PDO::PARAM_INT);
                $stmtBase->execute();
            }
            
            $idRegistro = $conexionPDO->lastInsertId();
            
            $conexionPDO->commit();
            
            return $idRegistro;
            
        } catch (Exception $e) {
            $conexionPDO->rollBack();
            if (!empty($comprobante)) {
                @unlink(ROOT_PATH.'/main-app/files/comprobantes/' . $comprobante);
            }
            // No incluir error-catch-to-report.php aquí, dejar que el código que llama maneje el error
            // include("../compartido/error-catch-to-report.php");
            throw $e;
        }
    }

    /**
     * Este metodo me trae la informacion de un Abono
     * ACTUALIZADO: Usa payments_invoiced consolidado (sin tabla payments)
     * @param mysqli $conexion
     * @param array $config
     * @param string $idAbono
     * 
     * @return array $resultado
    **/
    public static function traerDatosAbonos (
        mysqli $conexion, 
        array $config,
        string $idAbono
    )
    {
        $resultado = [];
        try {
            // Consulta mejorada para traer todos los datos del abono desde payments_invoiced consolidado
            $consulta = mysqli_query($conexion, "SELECT 
                pi.id, 
                pi.fecha_registro as registration_date, 
                pi.fecha_documento,
                pi.responsible_user, 
                pi.payment_user,
                pi.invoiced, 
                pi.type_payments, 
                pi.payment_tipo,
                pi.payment_method, 
                pi.payment_cuenta_bancaria_id,
                pi.attachment as voucher, 
                pi.observation, 
                pi.note,
                pi.is_deleted,
                pi.payment,
                cba.cba_nombre as cuenta_bancaria_nombre, 
                cba.cba_numero_cuenta as cuenta_bancaria_numero, 
                cba.cba_banco as cuenta_bancaria_banco, 
                cba.cba_tipo as cuenta_bancaria_tipo,
                uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2, uss.uss_id, uss.uss_tipo,
                pes.pes_nombre,
                CONCAT_WS(' ', cli.uss_nombre, cli.uss_nombre2, cli.uss_apellido1, cli.uss_apellido2) AS cliente_nombre,
                cli.uss_nombre AS cli_nombre, cli.uss_nombre2 AS cli_nombre2, cli.uss_apellido1 AS cli_apellido1, cli.uss_apellido2 AS cli_apellido2,
                cli.uss_email AS cli_email, cli.uss_celular AS cli_celular, cli.uss_documento AS cli_documento,
                cli.uss_tipo AS cli_tipo, pes_cli.pes_nombre AS cli_perfil,
                fc.fcu_id as numeroFactura,
                fc.fcu_id as fcu_id_factura,
                SUM(pi_detalle.payment) AS valorAbono
            FROM ".BD_FINANCIERA.".payments_invoiced pi
            LEFT JOIN ".BD_GENERAL.".usuarios uss 
                ON uss.uss_id=pi.responsible_user 
                AND uss.institucion={$config['conf_id_institucion']} 
                AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ADMIN.".general_perfiles pes
                ON pes.pes_id=uss.uss_tipo
            LEFT JOIN ".BD_GENERAL.".usuarios cli
                ON cli.uss_id=pi.payment_user
                AND cli.institucion={$config['conf_id_institucion']}
                AND cli.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ADMIN.".general_perfiles pes_cli
                ON pes_cli.pes_id=cli.uss_tipo
            LEFT JOIN ".BD_FINANCIERA.".finanzas_cuentas fc
                ON fc.fcu_id=pi.invoiced
                AND fc.institucion=pi.institucion
                AND fc.year=pi.year
            LEFT JOIN ".BD_FINANCIERA.".finanzas_cuentas_bancarias cba
                ON cba.cba_id=pi.payment_cuenta_bancaria_id
                AND cba.institucion=pi.institucion
                AND cba.year=pi.year
            LEFT JOIN ".BD_FINANCIERA.".payments_invoiced pi_detalle
                ON pi_detalle.id=pi.id
                AND pi_detalle.institucion=pi.institucion
                AND pi_detalle.year=pi.year
            WHERE 
                pi.is_deleted=0 
            AND pi.id='".mysqli_real_escape_string($conexion, $idAbono)."'
            AND pi.institucion = {$config['conf_id_institucion']} 
            AND pi.year = {$_SESSION["bd"]}
            GROUP BY pi.id
            LIMIT 1
            ");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
        $resultado = [];
        if ($consulta && mysqli_num_rows($consulta) > 0) {
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        }

        return $resultado;
    }

    /**
     * Este metodo me actualiza un abono
     * ACTUALIZADO: Usa payments_invoiced consolidado (sin tabla payments)
     * Solo permite actualizar campos no monetarios: observaciones, notas, fecha_documento, método de pago y cuenta bancaria
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * @param array $FILES
    **/
    public static function actualizarAbono (
        mysqli $conexion, 
        array $config, 
        array $POST, 
        array $FILES
    )
    {
        $comprobante = null;
        if (!empty($FILES['comprobante']['name'])) {
            $destino = ROOT_PATH.'/main-app/files/comprobantes';
            if (!file_exists($destino)) {
                @mkdir($destino, 0777, true);
            }
            $explode = explode(".", $FILES['comprobante']['name']);
            $extension = end($explode);
            $comprobante = uniqid('abono_'.$POST["cliente"].'_') . "." . $extension;
            move_uploaded_file($FILES['comprobante']['tmp_name'], $destino . "/" . $comprobante);
        }

        // Preparar fecha_documento si viene en el POST
        $fechaDocumento = null;
        if (!empty($POST["fecha"])) {
            // Convertir datetime-local a formato DATE
            $fecha = DateTime::createFromFormat('Y-m-d\TH:i', $POST["fecha"]);
            if ($fecha !== false) {
                $fechaDocumento = $fecha->format('Y-m-d');
            } else {
                // Intentar otro formato si falla
                $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $POST["fecha"]);
                if ($fecha !== false) {
                    $fechaDocumento = $fecha->format('Y-m-d');
                } else {
                    $fecha = DateTime::createFromFormat('Y-m-d', $POST["fecha"]);
                    if ($fecha !== false) {
                        $fechaDocumento = $fecha->format('Y-m-d');
                    }
                }
            }
            
            // Validar fecha_documento: no futura, no mayor a 1 año en el pasado
            if ($fechaDocumento !== null) {
                $fechaActual = new DateTime();
                $fechaDoc = new DateTime($fechaDocumento);
                $fechaLimite = (clone $fechaActual)->modify('-1 year');
                
                if ($fechaDoc > $fechaActual) {
                    throw new Exception("La fecha del documento no puede ser futura.");
                }
                if ($fechaDoc < $fechaLimite) {
                    throw new Exception("La fecha del documento no puede ser mayor a un año en el pasado.");
                }
            }
        }
        
        try {
            // Solo permitir actualizar campos no monetarios: observaciones, notas, fecha_documento, método de pago y cuenta bancaria
            // NO se permiten cambios en valores monetarios, tipo de transacción, cliente, etc.
            $cuentaBancariaId = !empty($POST["cuenta_bancaria_id"]) ? trim($POST["cuenta_bancaria_id"]) : null;
            
            $sqlUpdate = "UPDATE ".BD_FINANCIERA.".payments_invoiced SET 
                payment_method='".mysqli_real_escape_string($conexion, $POST["metodoPago"] ?? '')."', 
                observation='".mysqli_real_escape_string($conexion, $POST["obser"] ?? '')."', 
                note='".mysqli_real_escape_string($conexion, $POST["notas"] ?? '')."'";
            
            if ($fechaDocumento !== null) {
                $sqlUpdate .= ", fecha_documento='".mysqli_real_escape_string($conexion, $fechaDocumento)."'";
            }
            
            if ($comprobante !== null) {
                $sqlUpdate .= ", attachment='".mysqli_real_escape_string($conexion, $comprobante)."'";
            }
            
            // Agregar cuenta bancaria
            if ($cuentaBancariaId === null || $cuentaBancariaId === '') {
                $sqlUpdate .= ", payment_cuenta_bancaria_id=NULL";
            } else {
                $sqlUpdate .= ", payment_cuenta_bancaria_id='".mysqli_real_escape_string($conexion, $cuentaBancariaId)."'";
            }
            
            $sqlUpdate .= " WHERE id='".mysqli_real_escape_string($conexion, $POST["id"])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}";
            mysqli_query($conexion, $sqlUpdate);
        } catch (Exception $e) {
            if ($comprobante !== null) {
                @unlink(ROOT_PATH.'/main-app/files/comprobantes/' . $comprobante);
            }
            include("../compartido/error-catch-to-report.php");
            throw $e;
        }
    }

    /**
     * Este metodo anula un abono (no lo elimina, solo lo marca como anulado)
     * ACTUALIZADO: Usa payments_invoiced consolidado (sin tabla payments)
     * @param mysqli $conexion
     * @param array $config
     * @param string $idAbono
     * @param string $razonAnulacion Razón por la cual se anula el abono
    **/
    public static function anularAbono (
        mysqli $conexion, 
        array $config, 
        string $idAbono,
        string $razonAnulacion = ''
    )
    {
        try {
            // Marcar como anulado (is_deleted=1) y agregar la razón de anulación en el campo note
            $razonEscapada = mysqli_real_escape_string($conexion, $razonAnulacion);
            $notaAnulacion = !empty($razonAnulacion) ? " [ANULADO: {$razonEscapada}]" : " [ANULADO]";
            
            // Obtener la nota actual para no sobrescribirla completamente
            $consultaActual = mysqli_query($conexion, "SELECT note FROM ".BD_FINANCIERA.".payments_invoiced 
                WHERE id='".mysqli_real_escape_string($conexion, $idAbono)."' 
                AND institucion={$config['conf_id_institucion']} 
                AND year={$_SESSION["bd"]} 
                LIMIT 1");
            
            $notaActual = '';
            if ($consultaActual && mysqli_num_rows($consultaActual) > 0) {
                $datosActual = mysqli_fetch_array($consultaActual, MYSQLI_BOTH);
                $notaActual = $datosActual['note'] ?? '';
            }
            
            // Agregar la razón de anulación a la nota existente
            $notaFinal = trim($notaActual) . $notaAnulacion;
            $fechaAnulacion = date('Y-m-d H:i:s');
            $usuarioAnulacion = $_SESSION["id"] ?? null;
            
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".payments_invoiced 
                SET is_deleted=1, 
                    deleted_at='".mysqli_real_escape_string($conexion, $fechaAnulacion)."',
                    deleted_by='".mysqli_real_escape_string($conexion, $usuarioAnulacion)."',
                    note='".mysqli_real_escape_string($conexion, $notaFinal)."' 
                WHERE id='".mysqli_real_escape_string($conexion, $idAbono)."' 
                AND institucion={$config['conf_id_institucion']} 
                AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }
    
    /**
     * Este metodo me actualiza un abono (DEPRECADO - usar anularAbono)
     * @deprecated Use anularAbono instead
     * @param mysqli $conexion
     * @param array $config
     * @param string $idAbono
    **/
    public static function eliminarAbono (
        mysqli $conexion, 
        array $config, 
        string $idAbono
    )
    {
        // Redirigir a anularAbono para mantener compatibilidad
        self::anularAbono($conexion, $config, $idAbono, 'Anulado desde método deprecado eliminarAbono');
    }

    /**
     * Este metodo me trae la informacion de una cotizacion
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCotizacion
     * 
     * @return array $resultado
    **/
    public static function traerDatosCotizacion (
        mysqli $conexion, 
        array $config,
        string $idCotizacion
    )
    {
        $resultado = [];
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".quotes cotiz
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=user AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ADMIN.".localidad_ciudades ON ciu_id=uss_lugar_nacimiento
            LEFT JOIN ".BD_ADMIN.".localidad_departamentos ON dep_id=ciu_departamento
            WHERE id='{$idCotizacion}' AND cotiz.institucion = {$config['conf_id_institucion']} AND cotiz.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me valida si ya existe una configuración de la institución para finanzas
     * @param mysqli $conexion
     * @param array $config
     * 
     * @return int $num
    **/
    public static function validarConfiguracionFinanzas(
        mysqli $conexion,
        array $config
    )
    {

        try {
            $configConsulta = mysqli_query($conexion,"SELECT * FROM ".BD_FINANCIERA.".configuration WHERE institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $num = mysqli_num_rows($configConsulta);

        return $num;
    }

    /**
     * Este metodo me busca la configuración de la institución para finanzas
     * @param mysqli $conexion
     * @param array $config
     * 
     * @return array $resultado
    **/
    public static function configuracionFinanzas(
        mysqli $conexion,
        array $config
    )
    {
        $resultado = [];

        try {
            $configConsulta = mysqli_query($conexion,"SELECT * FROM ".BD_FINANCIERA.".configuration WHERE institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($configConsulta, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me guarda la configuración de la institución para finanzas
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * 
    **/
    public static function guardarConfiguracionFinanzas(
        mysqli $conexion,
        array $config,
        array $POST
    )
    {

        try {
            mysqli_query($conexion,"INSERT INTO ".BD_FINANCIERA.".configuration(consecutive_start, invoice_footer, institucion, year) VALUES('".$POST['consecutivo']."', '".$POST['pieFactura']."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me actualiza la configuración de la institución para finanzas
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * 
    **/
    public static function actualizarConfiguracionFinanzas(
        mysqli $conexion,
        array $config,
        array $POST
    )
    {

        try {
            mysqli_query($conexion,"UPDATE ".BD_FINANCIERA.".configuration SET consecutive_start='".$POST['consecutivo']."', invoice_footer='".$POST['pieFactura']."' WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me actualiza la configuración de la institución para finanzas
     * @param mysqli $conexion
     * @param array $config
     * @param string $firma
     * 
    **/
    public static function actualizarFirmaConfiguracionFinanzas(
        mysqli $conexion,
        array $config,
        string $firma
    )
    {

        try {
            mysqli_query($conexion,"UPDATE ".BD_FINANCIERA.".configuration SET signature='".$firma."' WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me trae todas las facturas recurrentes
     * @param mysqli $conexion
     * @param array $config
     * 
     * @return mysqli_result $consulta
    **/
    public static function listarRecurrentes (
        mysqli $conexion, 
        array $config
    )
    {
        try{
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".recurring_invoices ri
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=user AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            WHERE is_deleted=0 AND ri.institucion={$config['conf_id_institucion']} AND ri.year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me guarda una factura recurrente
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * 
    **/
    public static function guardarRecurrentes (
        mysqli $conexion, 
        array $config, 
        array $POST
    )
    {
        $fechaFinal = !empty($_POST["fechaFinal"]) ? "'{$_POST["fechaFinal"]}'" : "NULL";
        $dias = implode(',',$POST["dias"]);

        try{
            // Las facturas recurrentes no tienen método de pago ni cuenta bancaria al crearse
            // Estos se definen cuando se procesa el pago real
            // El campo additional_value (valor) ya no es editable, se usa 0 por defecto
            mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".recurring_invoices(id, date_start, detail, additional_value, invoice_type, observation, user, date_finish, frequency, days_in_month, responsible_user, institucion, year)VALUES('" .$POST["id"]. "', '" . $POST["fechaInicio"] . "','" . $POST["detalle"] . "', 0, '" . $POST["tipo"] . "','" . $POST["obs"] . "','" . $POST["usuario"] . "', $fechaFinal,'" . $POST["frecuencia"] . "', '" . $dias . "', '{$_SESSION["id"]}', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me trae la informacion de una factura recurrente
     * @param mysqli $conexion
     * @param array $config
     * @param string $idRecurrente
     * 
     * @return array $resultado
    **/
    public static function traerDatosRecurrentes (
        mysqli $conexion, 
        array $config,
        string $idRecurrente
    )
    {
        $resultado = [];
        try {
            $consulta = mysqli_query($conexion, "SELECT ri.*, uss.*, ciu.*, dep.* FROM ".BD_FINANCIERA.".recurring_invoices ri
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=responsible_user AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ADMIN.".localidad_ciudades ciu ON ciu_id=uss_lugar_nacimiento
            LEFT JOIN ".BD_ADMIN.".localidad_departamentos dep ON dep_id=ciu_departamento
            WHERE ri.id='{$idRecurrente}' AND ri.institucion = {$config['conf_id_institucion']} AND ri.year = {$_SESSION["bd"]}");
            
            if ($consulta && mysqli_num_rows($consulta) > 0) {
                $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
            }
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return [];
        }

        return $resultado ? $resultado : [];
    }

    /**
     * Este metodo me actualiza una factura recurrente
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
    **/
    public static function actualizarRecurrente (
        mysqli $conexion, 
        array $config, 
        array $POST
    )
    {
        // Verificar que la factura recurrente no esté eliminada
        $consultaEstado = mysqli_query($conexion, "SELECT is_deleted FROM ".BD_FINANCIERA.".recurring_invoices 
                                                   WHERE id='".mysqli_real_escape_string($conexion, $POST["id"])."' 
                                                   AND institucion={$config['conf_id_institucion']} 
                                                   AND year={$_SESSION["bd"]} 
                                                   LIMIT 1");
        
        if ($consultaEstado && mysqli_num_rows($consultaEstado) > 0) {
            $estado = mysqli_fetch_array($consultaEstado, MYSQLI_BOTH);
            if (!empty($estado['is_deleted']) && $estado['is_deleted'] == 1) {
                throw new Exception("No se pueden realizar cambios en una factura recurrente eliminada.");
            }
        }
        
        $dias = implode(',',$POST["dias"]);

        try {
            // Las facturas recurrentes no tienen método de pago ni cuenta bancaria
            // El campo additional_value (valor) ya no es editable, no se actualiza
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".recurring_invoices SET detail='".mysqli_real_escape_string($conexion, $POST["detalle"])."', user='".mysqli_real_escape_string($conexion, $POST["usuario"])."', days_in_month='".mysqli_real_escape_string($conexion, $dias)."', observation='".mysqli_real_escape_string($conexion, $POST["obs"])."', invoice_type='".mysqli_real_escape_string($conexion, $POST["tipo"])."' WHERE id='".mysqli_real_escape_string($conexion, $POST["id"])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me elimina una factura recurrente
     * @param mysqli $conexion
     * @param array $config
     * @param string $idRecurrente
    **/
    public static function eliminarRecurrente (
        mysqli $conexion, 
        array $config, 
        string $idRecurrente
    )
    {

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".recurring_invoices SET is_deleted=1 WHERE id='{$idRecurrente}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me trae todas las facturas recurrentes para el JOBS
     * @param mysqli $conexion
     * 
     * @return mysqli_result $consulta
    **/
    public static function listarRecurrentesJobs (
        mysqli $conexion
    )
    {
        try{
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".recurring_invoices WHERE is_deleted=0 AND date_start <= CURDATE() AND (date_finish >= CURDATE() OR date_finish = '0000-00-00' OR date_finish IS NULL)");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me genera una factura recurrente
     * @param mysqli $conexion
     * @param array $datosRecurrente
     * 
    **/
    public static function generarRecurrentes (
        mysqli $conexion, 
        array $datosRecurrente
    )
    {
        // Las facturas recurrentes no tienen método de pago definido al crearse
        // Se establece cuando se procesa el pago real

        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insertar factura generada desde recurrente
        // fcu_id es AUTO_INCREMENT, NO se incluye en el INSERT
        // fcu_created_by: responsable_user de la factura recurrente o $_SESSION["id"] si está disponible
        $createdBy = !empty($datosRecurrente["responsible_user"]) ? mysqli_real_escape_string($conexion, $datosRecurrente["responsible_user"]) : (!empty($_SESSION["id"]) ? mysqli_real_escape_string($conexion, $_SESSION["id"]) : 'SYSTEM');
        $origen = 'RECURRENTE';
        
        // INSERT sin fcu_id (se genera automáticamente)
        $resultadoInsert = mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".finanzas_cuentas(fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado, fcu_cerrado, fcu_status, fcu_created_by, fcu_origen, institucion, year)VALUES(now(),'" . mysqli_real_escape_string($conexion, $datosRecurrente["detail"]) . "','" . $datosRecurrente["additional_value"] . "','" . $datosRecurrente["invoice_type"] . "','" . mysqli_real_escape_string($conexion, $datosRecurrente["observation"]) . "','" . mysqli_real_escape_string($conexion, $datosRecurrente["user"]) . "',0,0,'".POR_COBRAR."','" . $createdBy . "','" . $origen . "', {$datosRecurrente['institucion']}, '{$datosRecurrente["year"]}')");
        
        if (!$resultadoInsert) {
            throw new Exception("Error al insertar factura: " . mysqli_error($conexion));
        }
        
        // Obtener el ID de la factura recién creada
        $idFactura = (int)mysqli_insert_id($conexion);
        
        if ($idFactura <= 0) {
            throw new Exception("Error al obtener ID de la factura creada");
        }

        // Para facturas recurrentes, buscar items por factura_recurrente_id
        $facturaRecurrenteId = (int)$datosRecurrente["id"];
        $itemsConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".transaction_items WHERE factura_recurrente_id = {$facturaRecurrenteId} AND type_transaction = 'INVOICE_RECURRING' AND institucion = {$datosRecurrente["institucion"]} AND year = {$datosRecurrente["year"]}");
        
        if (!$itemsConsulta) {
            throw new Exception("Error al consultar items: " . mysqli_error($conexion));
        }
        
        $numDatos = mysqli_num_rows($itemsConsulta);

        if ($numDatos > 0) {
            while ($fila = mysqli_fetch_array($itemsConsulta, MYSQLI_BOTH)) {
                // Validar que el tax existe en la tabla taxes antes de usarlo
                $taxValue = null;
                if (!empty($fila['tax']) && $fila['tax'] != '0' && $fila['tax'] != '' && $fila['tax'] !== null) {
                    // Verificar que el tax existe en la tabla taxes
                    $taxId = (int)$fila['tax'];
                    $consultaTax = mysqli_query($conexion, "SELECT id FROM ".BD_FINANCIERA.".taxes WHERE id={$taxId} AND institucion={$datosRecurrente['institucion']} AND year='{$datosRecurrente['year']}' LIMIT 1");
                    if ($consultaTax && mysqli_num_rows($consultaTax) > 0) {
                        $taxValue = $taxId;
                    }
                    // Si no existe, $taxValue queda como NULL
                }
                
                // Obtener item_type y application_time del item desde la tabla items
                $itemType = 'D'; // Por defecto débito
                $applicationTime = 'NULL'; // Por defecto NULL
                $itemIdParaConsulta = (int)$fila['id_item'];
                $consultaItemInfo = mysqli_query($conexion, "SELECT item_type, application_time FROM ".BD_FINANCIERA.".items WHERE item_id={$itemIdParaConsulta} AND institucion={$datosRecurrente['institucion']} AND year='{$datosRecurrente['year']}' LIMIT 1");
                if ($consultaItemInfo && mysqli_num_rows($consultaItemInfo) > 0) {
                    $itemInfo = mysqli_fetch_array($consultaItemInfo, MYSQLI_BOTH);
                    $itemType = !empty($itemInfo['item_type']) ? mysqli_real_escape_string($conexion, $itemInfo['item_type']) : 'D';
                    if (!empty($itemInfo['application_time'])) {
                        $applicationTime = "'".mysqli_real_escape_string($conexion, $itemInfo['application_time'])."'";
                    }
                }
                
                // Construir el INSERT con o sin tax según corresponda, incluyendo item_type y application_time
                if ($taxValue === null) {
                    $sqlItems = "INSERT INTO ".BD_FINANCIERA.".transaction_items(id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, description, price, tax, item_type, application_time)VALUES({$idFactura}, 'INVOICE', '".$fila['discount']."', '".$fila['cantity']."', '".$fila['subtotal']."', '".$fila['id_item']."', {$fila['institucion']}, '{$fila['year']}', '".mysqli_real_escape_string($conexion, $fila['description'])."', '".$fila['price']."', NULL, '{$itemType}', {$applicationTime})";
                } else {
                    $sqlItems = "INSERT INTO ".BD_FINANCIERA.".transaction_items(id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, description, price, tax, item_type, application_time)VALUES({$idFactura}, 'INVOICE', '".$fila['discount']."', '".$fila['cantity']."', '".$fila['subtotal']."', '".$fila['id_item']."', {$fila['institucion']}, '{$fila['year']}', '".mysqli_real_escape_string($conexion, $fila['description'])."', '".$fila['price']."', {$taxValue}, '{$itemType}', {$applicationTime})";
                }
                
                $resultadoItems = mysqli_query($conexion, $sqlItems);
                
                if (!$resultadoItems) {
                    throw new Exception("Error al insertar item: " . mysqli_error($conexion));
                }
            }
        }
        
        // Retornar el ID de la factura creada (como entero)
        return $idFactura;

    }

    /**
     * Este metodo me calcula el total de Abonos a una factura
     * @param mysqli $conexion
     * @param array $config
     * @param string $factura
     * 
     * @return float $total
    **/
    /**
     * Calcula el total abonado a una factura
     * ACTUALIZADO: Usa fcu_id y consulta solo payments_invoiced consolidado
     * @param mysqli $conexion
     * @param array $config
     * @param string $factura - fcu_id de la factura
     * @return float
     */
    public static function calcularTotalAbonado (
        mysqli $conexion, 
        array $config,
        string $factura
    )
    {
        try {
            // fcu_id es ahora el ID principal (AUTO_INCREMENT INT)
            // payments_invoiced.invoiced referencia finanzas_cuentas.fcu_id
            $fcuIdFactura = (int)$factura;
            
            // Consultar solo payments_invoiced (consolidado)
            $consulta = mysqli_query($conexion, "SELECT SUM(CAST(pi.payment AS DECIMAL(15,2))) as totalAbono 
                FROM ".BD_FINANCIERA.".payments_invoiced pi
                WHERE pi.invoiced={$fcuIdFactura}
                AND pi.type_payments='INVOICE'
                AND pi.payment_tipo='INGRESO'
                AND pi.is_deleted=0 
                AND pi.institucion = {$config['conf_id_institucion']} 
                AND pi.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
        $total = 0;
        if ($consulta && mysqli_num_rows($consulta) > 0){
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
            $total = floatval($resultado['totalAbono'] ?? 0);
        }

        return $total;
    }

    /**
     * Calcula los KPIs y resumen de facturas de manera centralizada
     * Este método debe ser usado en todas las páginas que necesiten estos cálculos para garantizar consistencia
     * 
     * @param mysqli $conexion
     * @param array $config
     * @param array $filtros Filtros opcionales:
     *   - 'mostrarAnuladas' => bool (default: false)
     *   - 'excluirEnProceso' => bool (default: true)
     *   - 'tipo' => int|null (1=Factura Venta, 2=Factura Compra, null=todos)
     *   - 'usuario' => string|null (ID del usuario)
     *   - 'desde' => string|null (fecha desde YYYY-MM-DD)
     *   - 'hasta' => string|null (fecha hasta YYYY-MM-DD)
     * 
     * @return array Array con las siguientes claves:
     *   - 'totalVentas' => float
     *   - 'totalCompras' => float
     *   - 'totalPorCobrar' => float (solo facturas venta con saldo pendiente > 0)
     *   - 'totalCobrado' => float (suma de todos los abonos de facturas venta)
     */
    public static function calcularKPIsResumen(
        mysqli $conexion,
        array $config,
        array $filtros = []
    )
    {
        $resultado = [
            'totalVentas' => 0.0,
            'totalCompras' => 0.0,
            'totalPorCobrar' => 0.0,
            'totalCobrado' => 0.0
        ];

        try {
            // Construir filtros SQL
            $filtroAnuladas = '';
            if (empty($filtros['mostrarAnuladas']) || !$filtros['mostrarAnuladas']) {
                $filtroAnuladas = " AND fc.fcu_anulado = 0";
            }

            $filtroEnProceso = '';
            if (empty($filtros['excluirEnProceso']) || $filtros['excluirEnProceso']) {
                $filtroEnProceso = " AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')";
            }

            $filtroTipo = '';
            if (!empty($filtros['tipo']) && ($filtros['tipo'] == 1 || $filtros['tipo'] == 2)) {
                $filtroTipo = " AND fc.fcu_tipo = " . intval($filtros['tipo']);
            }

            $filtroUsuario = '';
            if (!empty($filtros['usuario'])) {
                $usuarioEscapado = mysqli_real_escape_string($conexion, $filtros['usuario']);
                $filtroUsuario = " AND fc.fcu_usuario = '{$usuarioEscapado}'";
            }

            $filtroFechaDesde = '';
            if (!empty($filtros['desde'])) {
                $fechaDesdeEscapada = mysqli_real_escape_string($conexion, $filtros['desde']);
                $filtroFechaDesde = " AND fc.fcu_fecha >= '{$fechaDesdeEscapada}'";
            }

            $filtroFechaHasta = '';
            if (!empty($filtros['hasta'])) {
                $fechaHastaEscapada = mysqli_real_escape_string($conexion, $filtros['hasta']);
                $filtroFechaHasta = " AND fc.fcu_fecha <= '{$fechaHastaEscapada}'";
            }

            // Consulta base
            $sql = "SELECT fc.fcu_id, fc.fcu_tipo, fc.fcu_valor 
                    FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
                    WHERE fc.institucion = {$config['conf_id_institucion']} 
                    AND fc.year = {$_SESSION["bd"]}
                    {$filtroAnuladas}
                    {$filtroEnProceso}
                    {$filtroTipo}
                    {$filtroUsuario}
                    {$filtroFechaDesde}
                    {$filtroFechaHasta}";

            $consultaFacturas = mysqli_query($conexion, $sql);

            if ($consultaFacturas) {
                while ($factura = mysqli_fetch_array($consultaFacturas, MYSQLI_BOTH)) {
                    $vlrAdicional = !empty($factura['fcu_valor']) ? floatval($factura['fcu_valor']) : 0.0;
                    $totalNeto = self::calcularTotalNeto($conexion, $config, $factura['fcu_id'], $vlrAdicional);
                    $abonos = self::calcularTotalAbonado($conexion, $config, $factura['fcu_id']);
                    $porCobrar = $totalNeto - $abonos;

                    if ($factura['fcu_tipo'] == 1) {
                        // Factura Venta
                        $resultado['totalVentas'] += $totalNeto;
                        // Sumar TODOS los abonos de facturas de venta (independientemente del estado)
                        $resultado['totalCobrado'] += $abonos;
                        // Sumar el por cobrar de todas las facturas de venta con saldo pendiente > 0
                        if ($porCobrar > 0) {
                            $resultado['totalPorCobrar'] += $porCobrar;
                        }
                    } else if ($factura['fcu_tipo'] == 2) {
                        // Factura Compra
                        $resultado['totalCompras'] += $totalNeto;
                    }
                }
            }

            // Redondear todos los valores a 2 decimales
            foreach ($resultado as $key => $value) {
                $resultado[$key] = round($value, 2);
            }
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $resultado;
    }

    /**
     * Devuelve información extendida de una factura y sus ítems
     * @param mysqli $conexion
     * @param array $config
     * @param string $idFactura
     * @return array
     */
    public static function obtenerDetallesFactura(
        mysqli $conexion,
        array $config,
        string $idFactura
    )
    {
        $detalles = [
            'factura' => null,
            'items' => []
        ];

        try {
            $consultaFactura = mysqli_query($conexion, "SELECT fc.*, uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2, fc.fcu_id AS id_nuevo_movimientos
                FROM ".BD_FINANCIERA.".finanzas_cuentas fc
                LEFT JOIN ".BD_GENERAL.".usuarios uss
                    ON uss.uss_id = fc.fcu_usuario
                    AND uss.institucion = {$config['conf_id_institucion']}
                    AND uss.year = {$_SESSION['bd']}
                WHERE fc.fcu_id = '{$idFactura}'
                  AND fc.institucion = {$config['conf_id_institucion']}
                  AND fc.year = {$_SESSION['bd']}
                LIMIT 1");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            $consultaFactura = false;
        }

        if ($consultaFactura && mysqli_num_rows($consultaFactura) > 0) {
            $detalles['factura'] = mysqli_fetch_array($consultaFactura, MYSQLI_BOTH);
        }

        try {
            $consultaItems = mysqli_query($conexion, "SELECT ti.*, tax.fee as tax_fee, tax.name as tax_name, i.name as item_name, i.item_type, COALESCE(i.application_time, 'ANTE_IMPUESTO') AS application_time
                FROM ".BD_FINANCIERA.".transaction_items ti
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion={$config['conf_id_institucion']} AND tax.year={$_SESSION['bd']}
                LEFT JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti.id_item AND i.institucion={$config['conf_id_institucion']} AND i.year={$_SESSION['bd']}
                WHERE ti.id_transaction='{$idFactura}'
                AND ti.institucion={$config['conf_id_institucion']}
                AND ti.year={$_SESSION['bd']}
                ORDER BY i.item_type ASC, ti.id_autoincremental");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            $consultaItems = false;
        }

        if ($consultaItems) {
            while ($item = mysqli_fetch_array($consultaItems, MYSQLI_BOTH)) {
                $detalles['items'][] = $item;
            }
        }

        return $detalles;
    }

    /**
     * Este metodo me trae las facturas de un usuario para listar
     * @param mysqli            $conexion
     * @param array             $config
     * @param string            $filtro || OPCIONAL
     * 
     * @return mysqli_result    $consulta
    **/
    public static function listarFacturas (
        mysqli  $conexion, 
        array   $config, 
        string   $filtro = ""
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas
            WHERE 
                fcu_anulado=0 {$filtro} 
            AND fcu_status='".POR_COBRAR."'
            AND institucion = {$config['conf_id_institucion']} 
            AND year = {$_SESSION["bd"]}
            ORDER BY fcu_id ASC
            ");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me calcula el total de Abonos a un cliente
     * @param mysqli $conexion
     * @param array $config
     * @param string $cliente
     * @param string $codAbono
     * 
     * @return float $total
    **/
    /**
     * Calcula el total abonado por un cliente en un abono específico
     * ACTUALIZADO: Usa payments_invoiced consolidado (sin tabla payments)
     * @param mysqli $conexion
     * @param array $config
     * @param string $cliente - ID del cliente (uss_id)
     * @param string $codAbono - ID del abono
     * @return float
     */
    public static function calcularTotalAbonadoCliente (
        mysqli $conexion, 
        array $config,
        string $cliente,
        string $codAbono
    )
    {
        try {
            // Consultar solo payments_invoiced (consolidado)
            $consulta = mysqli_query($conexion, "SELECT SUM(pi.payment) as totalAbono 
                FROM ".BD_FINANCIERA.".payments_invoiced pi
                WHERE pi.payment_user='".mysqli_real_escape_string($conexion, $cliente)."' 
                AND pi.id='".mysqli_real_escape_string($conexion, $codAbono)."' 
                AND pi.is_deleted=0 
                AND pi.institucion = {$config['conf_id_institucion']} 
                AND pi.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
        $total = 0;
        if ($consulta && mysqli_num_rows($consulta) > 0){
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
            $total = floatval($resultado['totalAbono'] ?? 0);
        }

        return $total;
    }

    /**
     * Este metodo me trae las facturas de un usuario para listar
     * @param mysqli            $conexion
     * @param array             $config
     * @param string            $codAbono
     * 
     * @return mysqli_result    $consulta
    **/
    public static function listarConceptos (
        mysqli  $conexion, 
        array   $config, 
        ?string   $codAbono = null
    )
    {
        // Si no hay código de abono, retornar resultado vacío
        if (empty($codAbono)) {
            return false;
        }
        
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".payments_invoiced
            WHERE id='{$codAbono}' AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return false;
        }

        return $consulta;
    }

    /**
    * Este metodo me trae todos los impuestos
    * @param mysqli $conexion
    * @param array $config
    * 
    * @return mysqli_result $consulta
   **/
    public static function listarImpuestos (
        mysqli $conexion, 
        array $config
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".taxes tax
            WHERE is_deleted=0 AND tax.institucion = {$config['conf_id_institucion']} AND tax.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me guarda un impuesto
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * 
     * @return string $codigo
    **/
    public static function guardarImpuestos (
        mysqli $conexion, 
        array $config, 
        array $POST
    )
    {

        try {
            mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".taxes (type_tax, name, fee, description, institucion, year)VALUES('".$POST["typeTax"]."', '".$POST["name"]."', '".$POST["fee"]."', '".$POST["description"]."', {$config['conf_id_institucion']}, {$_SESSION["bd"]});");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $idRegistro = mysqli_insert_id($conexion);

        return $idRegistro;
    }

    /**
     * Este metodo me trae la informacion de un impuesto
     * @param mysqli $conexion
     * @param array $config
     * @param string $idImpuesto
     * 
     * @return array $resultado
    **/
    public static function traerDatosImpuestos (
        mysqli $conexion, 
        array $config,
        string $idImpuesto
    )
    {
        $resultado = [];
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".taxes tax
            WHERE id='{$idImpuesto}' AND tax.institucion = {$config['conf_id_institucion']} AND tax.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return [];
        }
        
        if ($consulta && mysqli_num_rows($consulta) > 0) {
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        }

        return $resultado ?: [];
    }

    /**
     * Este metodo me actualiza un impuesto
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
    **/
    public static function actualizarImpuestos (
        mysqli $conexion, 
        array $config, 
        array $POST
    )
    {

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".taxes SET type_tax='".$POST["typeTax"]."', name='".$POST["name"]."', fee='".$POST["fee"]."', description='".$POST["description"]."' WHERE id='".$POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me actualiza un impuesto
     * @param mysqli $conexion
     * @param array $config
     * @param string $idImpuesto
    **/
    /**
     * Valida si un impuesto está siendo utilizado en alguna transacción
     * @param mysqli $conexion
     * @param array $config
     * @param string $idImpuesto
     * @return bool True si está en uso, False si no está en uso
     */
    public static function validarImpuestoEnUso(
        mysqli $conexion,
        array $config,
        string $idImpuesto
    ): bool {
        try {
            $idImpuestoEscapado = mysqli_real_escape_string($conexion, $idImpuesto);
            $consulta = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_FINANCIERA.".transaction_items 
                WHERE tax='{$idImpuestoEscapado}' 
                AND institucion={$config['conf_id_institucion']} 
                AND year={$_SESSION["bd"]}");
            
            if ($consulta && mysqli_num_rows($consulta) > 0) {
                $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
                return ((int)$resultado['total']) > 0;
            }
            return false;
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return false;
        }
    }

    public static function eliminarImpuestos (
        mysqli $conexion, 
        array $config, 
        string $idImpuesto
    )
    {
        // Validar si el impuesto está en uso antes de eliminar
        if (self::validarImpuestoEnUso($conexion, $config, $idImpuesto)) {
            throw new Exception("No se puede eliminar el impuesto porque está asociado a uno o más registros de transacciones.");
        }

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".taxes SET is_deleted=1 WHERE id='{$idImpuesto}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            throw $e;
        }
    }

    /**
     * Este metodo me trae los items mas vendidos
     * @param mysqli            $conexion
     * @param array             $config
     * 
     * @return mysqli_result    $consulta
    **/
    public static function itemsMasVendidos (
        mysqli  $conexion, 
        array   $config
    )
    {
        try {
            // Solo considerar items de naturaleza débito (item_type = 'D')
            // Excluir facturas anuladas y en proceso
            $consulta = mysqli_query($conexion, "SELECT SUM(ti.cantity) AS cantidadTotal, i.item_id, i.name 
            FROM ".BD_FINANCIERA.".transaction_items ti
            INNER JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti.id_item 
                AND i.institucion = {$config['conf_id_institucion']} 
                AND i.year = {$_SESSION["bd"]}
            INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id = ti.id_transaction 
                AND fc.institucion = ti.institucion 
                AND fc.year = ti.year
            WHERE ti.institucion = {$config['conf_id_institucion']} 
                AND ti.year = {$_SESSION["bd"]}
                AND ti.type_transaction = '".TIPO_FACTURA."'
                AND ti.item_type = 'D'
                AND fc.fcu_anulado = 0 
                AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')
            GROUP BY i.item_id, i.name
            ORDER BY cantidadTotal DESC");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me trae los 5 mejores clientes
     * @param mysqli            $conexion
     * @param array             $config
     * 
     * @return mysqli_result    $consulta
    **/
    public static function mejorCliente (
        mysqli  $conexion, 
        array   $config
    )
    {
        try {
            // Usa la misma lógica de calcularTotalesFactura() para calcular el total neto
            // Solo considera facturas de venta (fcu_tipo = 1) - sin filtrar por estado para mostrar todos los clientes con facturas
            $consulta = mysqli_query($conexion, "SELECT 
                fc.fcu_usuario as uss_id,
                uss.uss_nombre, 
                uss.uss_nombre2, 
                uss.uss_apellido1, 
                uss.uss_apellido2,
                uss.uss_id,
                SUM(CAST(fc.fcu_valor AS DECIMAL(12, 2)) + IFNULL(ti.totalNeto, 0)) AS totalPagado
            FROM ".BD_FINANCIERA.".finanzas_cuentas fc
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss.uss_id = fc.fcu_usuario 
                AND uss.institucion = {$config['conf_id_institucion']} 
                AND uss.year = {$_SESSION["bd"]}
            LEFT JOIN (
                SELECT 
                    ti_sub.id_transaction,
                    ti_sub.institucion,
                    ti_sub.year,
                    -- Total Neto = Subtotal Gravable + Impuestos - Anticipos
                    (
                        IFNULL(SUM(CASE WHEN i.item_type = 'D' THEN ti_sub.price * ti_sub.cantity ELSE 0 END), 0)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'D' THEN ti_sub.price * ti_sub.cantity * (ti_sub.discount / 100) ELSE 0 END), 0)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'C' AND COALESCE(i.application_time, 'ANTE_IMPUESTO') = 'ANTE_IMPUESTO' THEN ABS(ti_sub.subtotal) ELSE 0 END), 0)
                        + IFNULL(SUM(CASE WHEN i.item_type = 'D' AND tax.fee > 0 THEN (ti_sub.price * ti_sub.cantity - ti_sub.price * ti_sub.cantity * (ti_sub.discount / 100)) * (tax.fee / 100) ELSE 0 END), 0)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'C' AND COALESCE(i.application_time, 'ANTE_IMPUESTO') = 'POST_IMPUESTO' THEN ABS(ti_sub.subtotal) ELSE 0 END), 0)
                    ) AS totalNeto
                FROM ".BD_FINANCIERA.".transaction_items ti_sub
                LEFT JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti_sub.id_item AND i.institucion = ti_sub.institucion AND i.year = ti_sub.year
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id = ti_sub.tax AND tax.institucion = ti_sub.institucion AND tax.year = ti_sub.year
                WHERE ti_sub.type_transaction = '".TIPO_FACTURA."'
                    AND ti_sub.institucion = {$config['conf_id_institucion']} 
                    AND ti_sub.year = {$_SESSION["bd"]}
                GROUP BY ti_sub.id_transaction, ti_sub.institucion, ti_sub.year
            ) ti ON ti.id_transaction = fc.fcu_id 
                AND ti.institucion = fc.institucion 
                AND ti.year = fc.year
            WHERE fc.fcu_anulado = 0 
                AND (fc.fcu_status IS NULL OR fc.fcu_status != '".ANULADA."')
                AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')
                AND fc.fcu_tipo = '1'
                AND fc.institucion = {$config['conf_id_institucion']} 
                AND fc.year = {$_SESSION["bd"]}
            GROUP BY fc.fcu_usuario, uss.uss_id, uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2
            HAVING totalPagado > 0
            ORDER BY totalPagado DESC
            LIMIT 5");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me el ingreso y egresos de los meses de un año
     * @param mysqli            $conexion
     * @param array             $config
     * 
     * @return mysqli_result    $consulta
    **/
    public static function TotalIngresosEgresos (
        mysqli  $conexion, 
        array   $config
    )
    {
        try {
            // Subconsulta que calcula el total_neto siguiendo la lógica de calcularTotalesFactura()
            // Lógica: Total Neto = (Subtotal Gravable + Impuestos - Anticipos) + fcu_valor
            // donde: Subtotal Gravable = Subtotal Bruto - Descuentos Items - Descuentos Comerciales Globales
            $consulta = mysqli_query($conexion, "SELECT 
                LPAD(MONTH(fc.fcu_fecha), 2, '0') AS mes,
                SUM(CASE WHEN fc.fcu_tipo = 1 THEN (CAST(fc.fcu_valor AS DECIMAL(12, 2)) + IFNULL(ti.totalNeto, 0)) ELSE 0 END) AS totalIngresos,
                SUM(CASE WHEN fc.fcu_tipo = 2 THEN (CAST(fc.fcu_valor AS DECIMAL(12, 2)) + IFNULL(ti.totalNeto, 0)) ELSE 0 END) AS totalEgresos,
                SUM(CASE WHEN fc.fcu_tipo = 1 THEN IFNULL(pi.totalAbonos, 0) ELSE 0 END) AS totalAbonosVentas,
                SUM(CASE WHEN fc.fcu_tipo = 2 THEN IFNULL(pi.totalAbonos, 0) ELSE 0 END) AS totalAbonosEgreso,
                SUM(CASE WHEN fc.fcu_tipo = 1 THEN (CAST(fc.fcu_valor AS DECIMAL(12, 2)) + IFNULL(ti.totalNeto, 0)) ELSE 0 END) AS totalFacturado
            FROM ".BD_FINANCIERA.".finanzas_cuentas fc
            LEFT JOIN (
                SELECT 
                    ti_sub.id_transaction,
                    ti_sub.institucion,
                    ti_sub.year,
                    -- Total Neto = Subtotal Gravable + Impuestos - Anticipos
                    -- Subtotal Gravable = Subtotal Bruto - Descuentos Items - Descuentos Comerciales Globales
                    (
                        -- Subtotal Bruto (precio × cantidad items débito)
                        IFNULL(SUM(CASE WHEN i.item_type = 'D' THEN ti_sub.price * ti_sub.cantity ELSE 0 END), 0)
                        -- Descuentos Items (descuento línea por línea items débito)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'D' THEN ti_sub.price * ti_sub.cantity * (ti_sub.discount / 100) ELSE 0 END), 0)
                        -- Descuentos Comerciales Globales (items crédito ANTE_IMPUESTO)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'C' AND COALESCE(i.application_time, 'ANTE_IMPUESTO') = 'ANTE_IMPUESTO' THEN ABS(ti_sub.subtotal) ELSE 0 END), 0)
                        -- Impuestos (sobre base gravable items débito)
                        + IFNULL(SUM(CASE WHEN i.item_type = 'D' AND tax.fee > 0 THEN (ti_sub.price * ti_sub.cantity - ti_sub.price * ti_sub.cantity * (ti_sub.discount / 100)) * (tax.fee / 100) ELSE 0 END), 0)
                        -- Anticipos (items crédito POST_IMPUESTO)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'C' AND COALESCE(i.application_time, 'ANTE_IMPUESTO') = 'POST_IMPUESTO' THEN ABS(ti_sub.subtotal) ELSE 0 END), 0)
                    ) AS totalNeto
                FROM ".BD_FINANCIERA.".transaction_items ti_sub
                LEFT JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti_sub.id_item AND i.institucion = ti_sub.institucion AND i.year = ti_sub.year
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id = ti_sub.tax AND tax.institucion = ti_sub.institucion AND tax.year = ti_sub.year
                WHERE ti_sub.type_transaction = '".TIPO_FACTURA."'
                    AND ti_sub.institucion = {$config['conf_id_institucion']}
                    AND ti_sub.year = {$_SESSION["bd"]}
                GROUP BY ti_sub.id_transaction, ti_sub.institucion, ti_sub.year
            ) ti ON ti.id_transaction = fc.fcu_id AND ti.institucion = fc.institucion AND ti.year = fc.year
            LEFT JOIN (
                SELECT pi.invoiced, pi.institucion, pi.year, SUM(CAST(pi.payment AS DECIMAL(12, 2))) AS totalAbonos
                FROM ".BD_FINANCIERA.".payments_invoiced pi
                WHERE pi.is_deleted = 0
                    AND pi.institucion = {$config['conf_id_institucion']} 
                    AND pi.year = {$_SESSION["bd"]}
                GROUP BY pi.invoiced, pi.institucion, pi.year
            ) pi ON pi.invoiced = fc.fcu_id AND pi.institucion = fc.institucion AND pi.year = fc.year
            WHERE fc.fcu_anulado = 0
              AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')
              AND fc.institucion = {$config['conf_id_institucion']}
              AND fc.year = {$_SESSION["bd"]}
            GROUP BY mes
            ORDER BY mes");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me trae el total de cuentas por cobrar de cada mes
     * @param mysqli            $conexion
     * @param array             $config
     * 
     * @return mysqli_result    $consulta
    **/
    public static function cuentasPorCobrar (
        mysqli  $conexion, 
        array   $config
    )
    {
        try {
            // Usa la misma lógica de calcularTotalesFactura() para calcular el total neto
            $consulta = mysqli_query($conexion, "SELECT 
                LPAD(MONTH(fc.fcu_fecha), 2, '0') AS mes,
                SUM(
                    GREATEST(
                        (CAST(fc.fcu_valor AS DECIMAL(12, 2)) + IFNULL(ti.totalNeto, 0)) - IFNULL(pi.totalAbonos, 0),
                        0
                    )
                ) AS totalPorCobrar
            FROM ".BD_FINANCIERA.".finanzas_cuentas fc
            LEFT JOIN (
                SELECT 
                    ti_sub.id_transaction,
                    ti_sub.institucion,
                    ti_sub.year,
                    -- Total Neto = Subtotal Gravable + Impuestos - Anticipos
                    (
                        IFNULL(SUM(CASE WHEN i.item_type = 'D' THEN ti_sub.price * ti_sub.cantity ELSE 0 END), 0)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'D' THEN ti_sub.price * ti_sub.cantity * (ti_sub.discount / 100) ELSE 0 END), 0)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'C' AND COALESCE(i.application_time, 'ANTE_IMPUESTO') = 'ANTE_IMPUESTO' THEN ABS(ti_sub.subtotal) ELSE 0 END), 0)
                        + IFNULL(SUM(CASE WHEN i.item_type = 'D' AND tax.fee > 0 THEN (ti_sub.price * ti_sub.cantity - ti_sub.price * ti_sub.cantity * (ti_sub.discount / 100)) * (tax.fee / 100) ELSE 0 END), 0)
                        - IFNULL(SUM(CASE WHEN i.item_type = 'C' AND COALESCE(i.application_time, 'ANTE_IMPUESTO') = 'POST_IMPUESTO' THEN ABS(ti_sub.subtotal) ELSE 0 END), 0)
                    ) AS totalNeto
                FROM ".BD_FINANCIERA.".transaction_items ti_sub
                LEFT JOIN ".BD_FINANCIERA.".items i ON i.item_id = ti_sub.id_item AND i.institucion = ti_sub.institucion AND i.year = ti_sub.year
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id = ti_sub.tax AND tax.institucion = ti_sub.institucion AND tax.year = ti_sub.year
                WHERE ti_sub.type_transaction = '".TIPO_FACTURA."'
                    AND ti_sub.institucion = {$config['conf_id_institucion']}
                    AND ti_sub.year = {$_SESSION["bd"]}
                GROUP BY ti_sub.id_transaction, ti_sub.institucion, ti_sub.year
            ) ti ON ti.id_transaction = fc.fcu_id AND ti.institucion = fc.institucion AND ti.year = fc.year
            LEFT JOIN (
                SELECT pi.invoiced, SUM(CAST(pi.payment AS DECIMAL(12, 2))) AS totalAbonos, pi.institucion, pi.year
                FROM ".BD_FINANCIERA.".payments_invoiced pi 
                WHERE pi.is_deleted = 0 
                    AND pi.type_payments='INVOICE'
                    AND pi.institucion={$config['conf_id_institucion']} 
                    AND pi.year={$_SESSION["bd"]}
                GROUP BY pi.invoiced, pi.institucion, pi.year
            ) pi ON pi.invoiced = fc.fcu_id AND pi.institucion=fc.institucion AND pi.year=fc.year
            WHERE fc.fcu_anulado = 0
              AND fc.fcu_tipo = 1
              AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')
              AND fc.institucion={$config['conf_id_institucion']}
              AND fc.year={$_SESSION["bd"]}
            GROUP BY mes
            ORDER BY mes");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Genera facturas masivas para grupos de usuarios
     * @param mysqli $conexion
     * @param array $config
     * @param array $datosLote Datos del lote de facturación
     * @param array $items Array de IDs de ítems a facturar
     * @param array $cantidades Array de cantidades correspondientes a cada ítem
     * 
     * @return array Resultado con estado, total de facturas generadas y errores
     */
    public static function generarFacturasMasivas(
        mysqli $conexion,
        array $config,
        array $datosLote,
        array $items,
        array $cantidades
    ) {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
        require_once(ROOT_PATH."/main-app/class/Usuarios.php");
        require_once(ROOT_PATH."/main-app/class/Utilidades.php");
        
        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $resultado = [
            'success' => false,
            'total_facturas' => 0,
            'errores' => []
        ];
        
        try {
            $conexionPDO->beginTransaction();
            
            // 1. Crear registro del lote
            $loteCriterios = json_encode($datosLote['criterios']);
            $loteItems = json_encode($items);
            
            $sqlLote = "INSERT INTO ".BD_FINANCIERA.".finanzas_lotes_facturacion (
                lote_nombre, lote_fecha, lote_usuario_responsable, lote_tipo_grupo,
                lote_criterios, lote_items, lote_total_facturas, lote_estado, lote_observaciones,
                institucion, year
            ) VALUES (?, NOW(), ?, ?, ?, ?, 0, 'PROCESANDO', ?, ?, ?)";
            
            $loteNombre = isset($datosLote['lote_nombre']) ? $datosLote['lote_nombre'] : '';
            $loteTipoGrupo = isset($datosLote['tipo_grupo']) ? $datosLote['tipo_grupo'] : '';
            $loteObservaciones = isset($datosLote['lote_observaciones']) ? $datosLote['lote_observaciones'] : '';
            $sessionId = isset($_SESSION["id"]) ? $_SESSION["id"] : '';
            $sessionBd = isset($_SESSION["bd"]) ? intval($_SESSION["bd"]) : 0;
            $confInstitucion = isset($config['conf_id_institucion']) ? intval($config['conf_id_institucion']) : 0;
            
            $stmtLote = $conexionPDO->prepare($sqlLote);
            $stmtLote->bindValue(1, $loteNombre, PDO::PARAM_STR);
            $stmtLote->bindValue(2, $sessionId, PDO::PARAM_STR);
            $stmtLote->bindValue(3, $loteTipoGrupo, PDO::PARAM_STR);
            $stmtLote->bindValue(4, $loteCriterios, PDO::PARAM_STR);
            $stmtLote->bindValue(5, $loteItems, PDO::PARAM_STR);
            $stmtLote->bindValue(6, $loteObservaciones, PDO::PARAM_STR);
            $stmtLote->bindValue(7, $confInstitucion, PDO::PARAM_INT);
            $stmtLote->bindValue(8, $sessionBd, PDO::PARAM_INT);
            $stmtLote->execute();
            
            // Obtener el ID del lote generado automáticamente
            $loteId = (int)$conexionPDO->lastInsertId();
            
            // 2. Obtener usuarios según criterios
            $usuarios = [];
            
            if ($datosLote['tipo_grupo'] === 'ESTUDIANTES') {
                // Obtener estudiantes por grado y grupo
                $filtro = '';
                if (!empty($datosLote['criterios']['grados']) && is_array($datosLote['criterios']['grados'])) {
                    $gradosEscapados = array_map(function($g) use ($conexion) {
                        return "'" . mysqli_real_escape_string($conexion, $g) . "'";
                    }, $datosLote['criterios']['grados']);
                    $filtro .= " AND mat.mat_grado IN (" . implode(',', $gradosEscapados) . ")";
                }
                
                if (!empty($datosLote['criterios']['grupos']) && is_array($datosLote['criterios']['grupos'])) {
                    $gruposEscapados = array_map(function($g) use ($conexion) {
                        return "'" . mysqli_real_escape_string($conexion, $g) . "'";
                    }, $datosLote['criterios']['grupos']);
                    $filtro .= " AND mat.mat_grupo IN (" . implode(',', $gruposEscapados) . ")";
                }
                
                // Solo agregar condición de estado, mat_eliminado ya está en el WHERE base
                $filtro .= " AND (mat.mat_estado_matricula = 1 OR mat.mat_estado_matricula = 2)";
                
                try {
                    $consultaEstudiantes = Estudiantes::listarEstudiantesEnGrados($filtro, 'LIMIT 0, 10000');
                    if ($consultaEstudiantes && is_object($consultaEstudiantes)) {
                        $numFilas = mysqli_num_rows($consultaEstudiantes);
                        while ($estudiante = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)) {
                            if (!empty($estudiante['mat_id_usuario'])) {
                                $usuarios[] = [
                                    'uss_id' => $estudiante['mat_id_usuario'],
                                    'nombre' => Estudiantes::NombreCompletoDelEstudiante($estudiante)
                                ];
                            }
                        }
                        if ($numFilas > 0 && empty($usuarios)) {
                            $resultado['errores'][] = "Se encontraron {$numFilas} estudiantes pero ninguno tiene mat_id_usuario asignado.";
                        } elseif ($numFilas == 0) {
                            $resultado['errores'][] = "No se encontraron estudiantes que cumplan con los criterios especificados. Verifique que los grados y grupos seleccionados tengan estudiantes matriculados.";
                        }
                    } else {
                        $resultado['errores'][] = "La consulta de estudiantes no devolvió resultados válidos.";
                    }
                } catch (Exception $e) {
                    $resultado['errores'][] = "Error al obtener estudiantes: " . $e->getMessage();
                }
            } else {
                // Obtener otros tipos de usuarios
                $tipoUsuario = null;
                switch ($datosLote['tipo_grupo']) {
                    case 'DOCENTES':
                        $tipoUsuario = TIPO_DOCENTE;
                        break;
                    case 'DIRECTIVOS':
                        $tipoUsuario = TIPO_DIRECTIVO;
                        break;
                    case 'ACUDIENTES':
                        $tipoUsuario = TIPO_ACUDIENTE;
                        break;
                }
                
                if ($tipoUsuario) {
                    $selectSql = ["uss_id", "uss_nombre", "uss_apellido1", "uss_apellido2", "uss_estado"];
                    $listaUsuarios = Usuarios::listar($selectSql, [$tipoUsuario], "uss_id");
                    
                    if (is_array($listaUsuarios)) {
                        foreach ($listaUsuarios as $usuario) {
                            $filtroEstado = true;
                            if (!empty($datosLote['criterios']['estado'])) {
                                $filtroEstado = (isset($usuario['uss_estado']) && $usuario['uss_estado'] == $datosLote['criterios']['estado']);
                            }
                            
                            if ($filtroEstado) {
                                $usuarios[] = [
                                    'uss_id' => $usuario['uss_id'],
                                    'nombre' => trim(($usuario['uss_nombre'] ?? '') . ' ' . ($usuario['uss_apellido1'] ?? '') . ' ' . ($usuario['uss_apellido2'] ?? ''))
                                ];
                            }
                        }
                    }
                }
            }
            
            // Validar que se encontraron usuarios
            if (empty($usuarios)) {
                $mensajeError = "No se encontraron usuarios que cumplan con los criterios especificados.";
                if ($datosLote['tipo_grupo'] === 'ESTUDIANTES') {
                    $criteriosUsados = [];
                    if (!empty($datosLote['criterios']['grados'])) {
                        $criteriosUsados[] = "Grados: " . implode(', ', $datosLote['criterios']['grados']);
                    }
                    if (!empty($datosLote['criterios']['grupos'])) {
                        $criteriosUsados[] = "Grupos: " . implode(', ', $datosLote['criterios']['grupos']);
                    }
                    if (!empty($criteriosUsados)) {
                        $mensajeError .= " Criterios utilizados: " . implode('; ', $criteriosUsados);
                    }
                }
                throw new Exception($mensajeError);
            }
            
            // 3. Obtener información de los ítems
            $itemsInfo = [];
            foreach ($items as $index => $itemId) {
                $itemData = self::traerDatosItems($conexion, $config, $itemId);
                if (!empty($itemData)) {
                    $cantidad = isset($cantidades[$index]) ? floatval($cantidades[$index]) : 1;
                    $precio = floatval($itemData['price']);
                    $itemsInfo[] = [
                        'id' => $itemId, // Este es item_id del item seleccionado
                        'item_id' => $itemId, // Mantener compatibilidad
                        'nombre' => $itemData['name'],
                        'precio' => $precio,
                        'cantidad' => $cantidad,
                        'subtotal' => $precio * $cantidad,
                        'tipo' => $itemData['item_type'] ?? 'D',
                        'tax' => $itemData['tax'] ?? 0
                    ];
                }
            }
            
            // Validar que se encontraron ítems
            if (empty($itemsInfo)) {
                throw new Exception("No se encontraron ítems válidos para facturar.");
            }
            
            // 4. Generar factura para cada usuario
            $totalFacturas = 0;
            $fechaActual = date('Y-m-d');
            
            // Obtener consecutivo inicial
            $sqlConsecutivo = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas 
                WHERE fcu_tipo=1 AND institucion=? AND year=? 
                ORDER BY fcu_id DESC LIMIT 1";
            $stmtConsecutivo = $conexionPDO->prepare($sqlConsecutivo);
            $stmtConsecutivo->bindValue(1, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtConsecutivo->bindValue(2, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtConsecutivo->execute();
            $consecutivoActual = $stmtConsecutivo->fetch(PDO::FETCH_ASSOC);
            
            $consecutivo = empty($consecutivoActual['fcu_consecutivo']) 
                ? $config['conf_inicio_recibos_ingreso'] 
                : intval($consecutivoActual['fcu_consecutivo']) + 1;
            
            foreach ($usuarios as $usuario) {
                try {
                    // Calcular valor total de la factura (débitos menos créditos)
                    $totalDebitos = 0;
                    $totalCreditos = 0;
                    foreach ($itemsInfo as $item) {
                        if ($item['tipo'] == 'C') {
                            $totalCreditos += $item['subtotal'];
                        } else {
                            $totalDebitos += $item['subtotal'];
                        }
                    }
                    // No guardamos el valor total en fcu_valor porque calcularTotalNeto lo sumará con los items
                    // fcu_valor debe ser 0 cuando el total viene solo de los items
                    $valorTotalFactura = 0;
                    
                    // Crear factura
                    $detalleFactura = "Facturación masiva - " . $datosLote['lote_nombre'];
                    $tipoFactura = 1; // Factura de venta
                    $observacionesLote = isset($datosLote['lote_observaciones']) ? $datosLote['lote_observaciones'] : '';
                    $estadoInicial = POR_COBRAR; // Estado inicial: POR_COBRAR
                    
                    $sqlFactura = "INSERT INTO ".BD_FINANCIERA.".finanzas_cuentas(
                        fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones,
                        fcu_usuario, fcu_anulado, fcu_cerrado, fcu_consecutivo,
                        fcu_lote_id, fcu_status, fcu_created_by, fcu_origen, institucion, year
                    ) VALUES (?, ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmtFactura = $conexionPDO->prepare($sqlFactura);
                    $fcuCreatedByLote = $_SESSION["id"];
                    $fcuOrigenLote = 'NORMAL';
                    $stmtFactura->bindValue(1, $fechaActual, PDO::PARAM_STR);
                    $stmtFactura->bindValue(2, $detalleFactura, PDO::PARAM_STR);
                    $stmtFactura->bindValue(3, $valorTotalFactura, PDO::PARAM_STR);
                    $stmtFactura->bindValue(4, $tipoFactura, PDO::PARAM_INT);
                    $stmtFactura->bindValue(5, $observacionesLote, PDO::PARAM_STR);
                    $stmtFactura->bindValue(6, $usuario['uss_id'], PDO::PARAM_STR);
                    $stmtFactura->bindValue(7, $consecutivo, PDO::PARAM_STR);
                    $stmtFactura->bindValue(8, $loteId, PDO::PARAM_INT);
                    $stmtFactura->bindValue(9, $estadoInicial, PDO::PARAM_STR);
                    $stmtFactura->bindValue(10, $fcuCreatedByLote, PDO::PARAM_STR);
                    $stmtFactura->bindValue(11, $fcuOrigenLote, PDO::PARAM_STR);
                    $stmtFactura->bindValue(12, $config['conf_id_institucion'], PDO::PARAM_INT);
                    $stmtFactura->bindValue(13, $_SESSION["bd"], PDO::PARAM_INT);
                    $stmtFactura->execute();
                    
                    // Obtener el ID de la factura generado automáticamente
                    $fcuId = (int)$conexionPDO->lastInsertId();
                    
                    // Agregar ítems a la factura
                    foreach ($itemsInfo as $item) {
                        $subtotal = $item['subtotal'];
                        
                        $sqlItem = "INSERT INTO ".BD_FINANCIERA.".transaction_items(
                            id_transaction, type_transaction, discount, cantity, subtotal,
                            id_item, institucion, year, description, price, tax, item_type, application_time
                        ) VALUES (?, ?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        
                        $stmtItem = $conexionPDO->prepare($sqlItem);
                        $tipoTransaction = TIPO_FACTURA;
                        $descripcionItem = $item['nombre'];
                        $precioItem = $item['precio'];
                        
                        // Validar tax: si es 0, vacío o null, usar NULL. Si tiene valor, verificar que existe en la tabla taxes
                        $taxItem = null;
                        if (!empty($item['tax']) && $item['tax'] != '0' && $item['tax'] != 0) {
                            $taxId = (int)$item['tax'];
                            // Verificar que el tax existe en la tabla taxes
                            try {
                                $sqlCheckTax = "SELECT id FROM ".BD_FINANCIERA.".taxes 
                                              WHERE id=? AND institucion=? AND year=? LIMIT 1";
                                $stmtCheckTax = $conexionPDO->prepare($sqlCheckTax);
                                $stmtCheckTax->bindValue(1, $taxId, PDO::PARAM_INT);
                                $stmtCheckTax->bindValue(2, $config['conf_id_institucion'], PDO::PARAM_INT);
                                $stmtCheckTax->bindValue(3, $_SESSION["bd"], PDO::PARAM_INT);
                                $stmtCheckTax->execute();
                                $taxResult = $stmtCheckTax->fetch(PDO::FETCH_ASSOC);
                                if ($taxResult) {
                                    $taxItem = $taxId;
                                }
                            } catch (Exception $e) {
                                // Si hay error al validar, usar NULL
                                $taxItem = null;
                            }
                        }
                        
                        $cantidadItem = $item['cantidad'];
                        // Usar item_id del array, que viene del POST (es item_id de la tabla items)
                        $itemIdParaInsert = (int)($item['id'] ?? $item['item_id'] ?? 0);
                        
                        // Obtener item_type y application_time del item desde la tabla items
                        $itemType = 'D'; // Por defecto débito
                        $applicationTime = null;
                        try {
                            $sqlCheckItem = "SELECT item_type, application_time FROM ".BD_FINANCIERA.".items 
                                           WHERE item_id=? AND institucion=? AND year=? LIMIT 1";
                            $stmtCheckItem = $conexionPDO->prepare($sqlCheckItem);
                            $stmtCheckItem->bindValue(1, $itemIdParaInsert, PDO::PARAM_INT);
                            $stmtCheckItem->bindValue(2, $config['conf_id_institucion'], PDO::PARAM_INT);
                            $stmtCheckItem->bindValue(3, $_SESSION["bd"], PDO::PARAM_INT);
                            $stmtCheckItem->execute();
                            $itemResult = $stmtCheckItem->fetch(PDO::FETCH_ASSOC);
                            if ($itemResult) {
                                $itemType = !empty($itemResult['item_type']) ? $itemResult['item_type'] : 'D';
                                if (!empty($itemResult['application_time'])) {
                                    $applicationTime = $itemResult['application_time'];
                                }
                            }
                        } catch (Exception $e) {
                            // Si hay error, usar valores por defecto
                            $itemType = 'D';
                            $applicationTime = null;
                        }
                        
                        $stmtItem->bindValue(1, $fcuId, PDO::PARAM_INT);
                        $stmtItem->bindValue(2, $tipoTransaction, PDO::PARAM_STR);
                        $stmtItem->bindValue(3, $cantidadItem, PDO::PARAM_INT);
                        $stmtItem->bindValue(4, $subtotal, PDO::PARAM_STR);
                        $stmtItem->bindValue(5, $itemIdParaInsert, PDO::PARAM_INT);
                        $stmtItem->bindValue(6, $config['conf_id_institucion'], PDO::PARAM_INT);
                        $stmtItem->bindValue(7, $_SESSION["bd"], PDO::PARAM_INT);
                        $stmtItem->bindValue(8, $descripcionItem, PDO::PARAM_STR);
                        $stmtItem->bindValue(9, $precioItem, PDO::PARAM_STR);
                        $stmtItem->bindValue(10, $taxItem, $taxItem !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
                        $stmtItem->bindValue(11, $itemType, PDO::PARAM_STR);
                        $stmtItem->bindValue(12, $applicationTime, $applicationTime !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                        $stmtItem->execute();
                    }
                    
                    $totalFacturas++;
                    $consecutivo++;
                    
                } catch (Exception $e) {
                    $resultado['errores'][] = "Error al generar factura para usuario {$usuario['uss_id']}: " . $e->getMessage();
                }
            }
            
            // 5. Actualizar lote con resultado
            $estadoLote = (!empty($resultado['errores']) && $totalFacturas == 0) ? 'ERROR' : 'COMPLETADO';
            
            $sqlUpdateLote = "UPDATE ".BD_FINANCIERA.".finanzas_lotes_facturacion 
                SET lote_total_facturas=?, lote_estado=? 
                WHERE id=? AND institucion=? AND year=?";
            
            $stmtUpdateLote = $conexionPDO->prepare($sqlUpdateLote);
            $stmtUpdateLote->bindValue(1, $totalFacturas, PDO::PARAM_INT);
            $stmtUpdateLote->bindValue(2, $estadoLote, PDO::PARAM_STR);
            $stmtUpdateLote->bindValue(3, $loteId, PDO::PARAM_STR);
            $stmtUpdateLote->bindValue(4, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtUpdateLote->bindValue(5, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtUpdateLote->execute();
            
            $conexionPDO->commit();
            
            $resultado['success'] = true;
            $resultado['total_facturas'] = $totalFacturas;
            $resultado['lote_id'] = $loteId;
            
        } catch (Exception $e) {
            $conexionPDO->rollBack();
            $resultado['errores'][] = "Error general: " . $e->getMessage();
            
            // Actualizar lote con estado ERROR
            try {
                $loteIdValue = $loteId ?? '';
                $sqlUpdateLote = "UPDATE ".BD_FINANCIERA.".finanzas_lotes_facturacion 
                    SET lote_estado='ERROR' 
                    WHERE id=? AND institucion=? AND year=?";
                $stmtUpdateLote = $conexionPDO->prepare($sqlUpdateLote);
                $stmtUpdateLote->bindValue(1, $loteIdValue, PDO::PARAM_STR);
                $stmtUpdateLote->bindValue(2, $config['conf_id_institucion'], PDO::PARAM_INT);
                $stmtUpdateLote->bindValue(3, $_SESSION["bd"], PDO::PARAM_INT);
                $stmtUpdateLote->execute();
            } catch (Exception $e2) {
                // Ignorar error de actualización
            }
        }
        
        return $resultado;
    }

    /**
     * Lista todas las cuentas bancarias
     * @param mysqli $conexion
     * @param array $config
     * @param bool $soloActivas Si es true, solo retorna cuentas activas
     * 
     * @return mysqli_result
     */
    public static function listarCuentasBancarias(
        mysqli $conexion,
        array $config,
        ?string $metodoPago = null,
        bool $soloActivas = false
    ) {
        $filtroActiva = $soloActivas ? " AND cba_activa = 1" : "";
        $filtroMetodo = $metodoPago ? " AND cba_metodo_pago_asociado = '".mysqli_real_escape_string($conexion, $metodoPago)."'" : "";
        
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
                WHERE institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}{$filtroActiva}{$filtroMetodo}
                ORDER BY cba_nombre");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Obtiene los datos de una cuenta bancaria
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCuenta
     * 
     * @return array
     */
    public static function traerDatosCuentaBancaria(
        mysqli $conexion,
        array $config,
        string $idCuenta
    ) {
        $resultado = [];
        try {
            if (empty($idCuenta)) {
                return [];
            }
            
            $idCuentaEscapado = mysqli_real_escape_string($conexion, $idCuenta);
            $sql = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
                WHERE cba_id='{$idCuentaEscapado}' AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}";
            
            $consulta = mysqli_query($conexion, $sql);
            
            if ($consulta === false) {
                // Error en la consulta
                return [];
            }
            
            if (mysqli_num_rows($consulta) > 0) {
                $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
                // Asegurar que es un array válido
                if (!is_array($resultado)) {
                    return [];
                }
            }
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return [];
        }

        return $resultado;
    }

    /**
     * Guarda una cuenta bancaria
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * 
     * @return string ID de la cuenta creada
     */
    public static function guardarCuentaBancaria(
        mysqli $conexion,
        array $config,
        array $POST
    ) {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        
        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // cba_id es AUTO_INCREMENT, no se debe incluir en el INSERT
        $activa = !empty($POST["cba_activa"]) ? 1 : 0;
        
        $saldoInicial = !empty($POST["cba_saldo_inicial"]) ? (float)$POST["cba_saldo_inicial"] : 0.00;
        
        // Manejar campos opcionales
        $cbaBanco = !empty($POST["cba_banco"]) ? trim($POST["cba_banco"]) : '';
        $cbaNumeroCuenta = !empty($POST["cba_numero_cuenta"]) ? trim($POST["cba_numero_cuenta"]) : '';
        $cbaObservaciones = !empty($POST["cba_observaciones"]) ? trim($POST["cba_observaciones"]) : '';
        
        try {
            // cba_id es AUTO_INCREMENT, no se incluye en el INSERT
            $sql = "INSERT INTO `".BD_FINANCIERA."`.`finanzas_cuentas_bancarias` (
                `cba_nombre`, `cba_banco`, `cba_numero_cuenta`, `cba_tipo`, 
                `cba_metodo_pago_asociado`, `cba_saldo_inicial`, `cba_activa`, `cba_observaciones`, 
                `institucion`, `year`, `fecha_registro`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $conexionPDO->prepare($sql);
            
            // Bindear parámetros (sin cba_id, ahora son 10 parámetros en lugar de 11)
            $stmt->bindParam(1, $POST["cba_nombre"], PDO::PARAM_STR);
            $stmt->bindParam(2, $cbaBanco, PDO::PARAM_STR);
            $stmt->bindParam(3, $cbaNumeroCuenta, PDO::PARAM_STR);
            $stmt->bindParam(4, $POST["cba_tipo"], PDO::PARAM_STR);
            $stmt->bindParam(5, $POST["cba_metodo_pago_asociado"], PDO::PARAM_STR);
            $stmt->bindValue(6, $saldoInicial, PDO::PARAM_STR);
            $stmt->bindParam(7, $activa, PDO::PARAM_INT);
            $stmt->bindParam(8, $cbaObservaciones, PDO::PARAM_STR);
            $stmt->bindParam(9, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(10, $_SESSION["bd"], PDO::PARAM_INT);
            
            $stmt->execute();
            
            // Obtener el ID generado automáticamente (AUTO_INCREMENT)
            $codigo = (string)$conexionPDO->lastInsertId();
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            throw $e; // Re-lanzar para que el script que llama pueda manejarlo
        }

        return $codigo;
    }

    /**
     * Actualiza una cuenta bancaria
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     */
    public static function actualizarCuentaBancaria(
        mysqli $conexion,
        array $config,
        array $POST
    ) {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        
        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $activa = !empty($POST["cba_activa"]) ? 1 : 0;
        $saldoInicial = !empty($POST["cba_saldo_inicial"]) ? (float)$POST["cba_saldo_inicial"] : 0.00;

        try {
            $sql = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas_bancarias SET 
                cba_nombre=?, cba_banco=?, cba_numero_cuenta=?, cba_tipo=?, 
                cba_metodo_pago_asociado=?, cba_saldo_inicial=?, cba_activa=?, cba_observaciones=? 
                WHERE cba_id=? AND institucion=? AND year=?";
            
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $POST["cba_nombre"], PDO::PARAM_STR);
            $stmt->bindParam(2, $POST["cba_banco"], PDO::PARAM_STR);
            $stmt->bindParam(3, $POST["cba_numero_cuenta"], PDO::PARAM_STR);
            $stmt->bindParam(4, $POST["cba_tipo"], PDO::PARAM_STR);
            $stmt->bindParam(5, $POST["cba_metodo_pago_asociado"], PDO::PARAM_STR);
            $stmt->bindValue(6, $saldoInicial, PDO::PARAM_STR);
            $stmt->bindParam(7, $activa, PDO::PARAM_INT);
            $stmt->bindParam(8, $POST["cba_observaciones"], PDO::PARAM_STR);
            $stmt->bindParam(9, $POST["cba_id"], PDO::PARAM_STR);
            $stmt->bindParam(10, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(11, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Lista cuentas bancarias por método de pago
     * @param mysqli $conexion
     * @param array $config
     * @param string $metodoPago
     * @param bool $soloActivas
     * 
     * @return mysqli_result
     */
    public static function listarCuentasBancariasPorMetodo(
        mysqli $conexion,
        array $config,
        string $metodoPago,
        bool $soloActivas = true
    ) {
        $filtroActiva = $soloActivas ? " AND cba_activa = 1" : "";
        
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas_bancarias 
                WHERE cba_metodo_pago_asociado='{$metodoPago}' 
                AND institucion = {$config['conf_id_institucion']} 
                AND year = {$_SESSION["bd"]}{$filtroActiva}
                ORDER BY cba_nombre");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Calcula ingresos, egresos y saldo actual de una cuenta bancaria
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCuenta
     * @return array Array con 'ingresos', 'egresos', 'saldo_actual'
     */
    public static function calcularSaldoCuentaBancaria(
        mysqli $conexion,
        array $config,
        string $idCuenta
    ) {
        $resultado = [
            'ingresos' => 0,
            'egresos' => 0,
            'saldo_actual' => 0
        ];
        
        try {
            // Calcular ingresos (abonos a facturas de venta - fcu_tipo = 1)
            $sqlIngresos = "SELECT COALESCE(SUM(CAST(pi.payment AS DECIMAL(12, 2))), 0) AS total_ingresos
                FROM ".BD_FINANCIERA.".payments_invoiced pi
                INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id = pi.invoiced
                    AND fc.institucion = pi.institucion 
                    AND fc.year = pi.year
                WHERE pi.payment_cuenta_bancaria_id = '".mysqli_real_escape_string($conexion, $idCuenta)."'
                    AND pi.is_deleted = 0
                    AND pi.type_payments = '".INVOICE."'
                    AND fc.fcu_tipo = '1'
                    AND fc.fcu_anulado = 0
                    AND pi.institucion = {$config['conf_id_institucion']}
                    AND pi.year = {$_SESSION["bd"]}";
            
            $consultaIngresos = mysqli_query($conexion, $sqlIngresos);
            if ($consultaIngresos && mysqli_num_rows($consultaIngresos) > 0) {
                $row = mysqli_fetch_array($consultaIngresos, MYSQLI_BOTH);
                $resultado['ingresos'] = floatval($row['total_ingresos'] ?? 0);
            }
            
            // Calcular egresos (abonos a facturas de compra - fcu_tipo = 2)
            $sqlEgresos = "SELECT COALESCE(SUM(CAST(pi.payment AS DECIMAL(12, 2))), 0) AS total_egresos
                FROM ".BD_FINANCIERA.".payments_invoiced pi
                INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_id = pi.invoiced
                    AND fc.institucion = pi.institucion 
                    AND fc.year = pi.year
                WHERE pi.payment_cuenta_bancaria_id = '".mysqli_real_escape_string($conexion, $idCuenta)."'
                    AND pi.is_deleted = 0
                    AND pi.type_payments = '".INVOICE."'
                    AND fc.fcu_tipo = '2'
                    AND fc.fcu_anulado = 0
                    AND pi.institucion = {$config['conf_id_institucion']}
                    AND pi.year = {$_SESSION["bd"]}";
            
            $consultaEgresos = mysqli_query($conexion, $sqlEgresos);
            if ($consultaEgresos && mysqli_num_rows($consultaEgresos) > 0) {
                $row = mysqli_fetch_array($consultaEgresos, MYSQLI_BOTH);
                $resultado['egresos'] = floatval($row['total_egresos'] ?? 0);
            }
            
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
        return $resultado;
    }

    /**
     * Valida si una cuenta bancaria está siendo utilizada en algún pago
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCuenta
     * @return bool True si está en uso, False si no está en uso
     */
    public static function validarCuentaBancariaEnUso(
        mysqli $conexion,
        array $config,
        string $idCuenta
    ): bool {
        try {
            $idCuentaEscapado = mysqli_real_escape_string($conexion, $idCuenta);
            $consulta = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ".BD_FINANCIERA.".payments_invoiced 
                WHERE payment_cuenta_bancaria_id='{$idCuentaEscapado}' 
                AND institucion={$config['conf_id_institucion']} 
                AND year={$_SESSION["bd"]}
                AND is_deleted=0");
            
            if ($consulta && mysqli_num_rows($consulta) > 0) {
                $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
                return ((int)$resultado['total']) > 0;
            }
            return false;
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return false;
        }
    }

    /**
     * Obtiene arqueo de caja agrupado por método de pago y cuenta bancaria
     * Basado en abonos (payments) en lugar de facturas
     * @param mysqli $conexion
     * @param array $config
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @param int|null $tipoMovimiento 1=Ingresos, 2=Egresos, null=Todos
     * 
     * @return array Arreglo con agrupación por método de pago y cuenta bancaria
     */
    public static function obtenerArqueoCajaPorMetodo(
        mysqli $conexion,
        array $config,
        string $fechaDesde,
        string $fechaHasta,
        ?int $tipoMovimiento = null
    ) {
        $resultado = [
            'por_metodo' => [],
            'por_cuenta' => [],
            'total_general' => 0
        ];

        try {
            // Mapeo de métodos de pago a nombres
            $mapaNombreFormaPago = [
                'EFECTIVO' => 'Efectivo',
                'CHEQUE' => 'Cheque',
                'T_DEBITO' => 'T. Débito',
                'T_CREDITO' => 'T. Crédito',
                'TRANSFERENCIA' => 'Transferencia',
                'NEQUI' => 'Nequi',
                'DAVIPLATA' => 'Daviplata',
                'BANCOLOMBIA' => 'Bancolombia',
                'DAVIVIENDA' => 'Davivienda',
                'BANCO_OCCIDENTE' => 'Banco de Occidente',
                'OTROS' => 'Otros'
            ];

            // Consulta basada en abonos (payments_invoiced) agrupada por método de pago y cuenta bancaria
            // Considera el tipo de factura (fcu_tipo) para determinar ingresos (venta) vs egresos (compra)
            $sql = "SELECT 
                pi.payment_method,
                pi.payment_cuenta_bancaria_id,
                pi.type_payments,
                cba.cba_nombre AS cuenta_nombre,
                cba.cba_banco AS cuenta_banco,
                fc.fcu_tipo,
                COALESCE(SUM(CAST(pi.payment AS DECIMAL(12, 2))), 0) AS total_abono,
                COUNT(DISTINCT pi.id) AS cantidad_abonos
            FROM ".BD_FINANCIERA.".payments_invoiced pi
            INNER JOIN ".BD_FINANCIERA.".finanzas_cuentas fc 
                ON fc.fcu_id = pi.invoiced 
                AND fc.institucion = pi.institucion 
                AND fc.year = pi.year
            LEFT JOIN ".BD_FINANCIERA.".finanzas_cuentas_bancarias cba 
                ON cba.cba_id = pi.payment_cuenta_bancaria_id 
                AND cba.institucion = pi.institucion 
                AND cba.year = pi.year
            WHERE DATE(pi.fecha_registro) BETWEEN ? AND ?
                AND pi.is_deleted = 0
                AND fc.fcu_anulado = 0
                AND pi.institucion = ?
                AND pi.year = ?
                AND pi.type_payments = '".INVOICE."'
            GROUP BY pi.payment_method, pi.payment_cuenta_bancaria_id, pi.type_payments, fc.fcu_tipo, cba.cba_nombre, cba.cba_banco
            ORDER BY pi.payment_method, cba.cba_nombre";

            require_once(ROOT_PATH."/main-app/class/Conexion.php");
            $conexionPDO = Conexion::newConnection('PDO');
            $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $fechaDesde, PDO::PARAM_STR);
            $stmt->bindParam(2, $fechaHasta, PDO::PARAM_STR);
            $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalGeneral = 0;
            $porMetodo = [];
            $porCuenta = [];
            
            // Agrupar resultados por método y cuenta para calcular totales
            // Separar ingresos (facturas de venta - fcu_tipo = 1) y egresos (facturas de compra - fcu_tipo = 2)
            $agrupados = [];
            foreach ($resultados as $row) {
                $metodoPago = $row['payment_method'] ?? 'OTROS';
                $cuentaId = $row['payment_cuenta_bancaria_id'] ?? null;
                $fcuTipo = intval($row['fcu_tipo'] ?? 0);
                $clave = $metodoPago . '_' . ($cuentaId ?? 'sin_cuenta');
                
                if (!isset($agrupados[$clave])) {
                    $agrupados[$clave] = [
                        'metodo_pago' => $metodoPago,
                        'cuenta_id' => $cuentaId,
                        'cuenta_nombre' => $row['cuenta_nombre'],
                        'cuenta_banco' => $row['cuenta_banco'],
                        'tipo_payments' => $row['type_payments'],
                        'total_ingresos' => 0,
                        'total_egresos' => 0,
                        'cantidad_abonos' => 0
                    ];
                }
                
                $totalAbono = floatval($row['total_abono'] ?? 0);
                if ($fcuTipo == 1) {
                    // Factura de venta = ingreso
                    $agrupados[$clave]['total_ingresos'] += $totalAbono;
                } elseif ($fcuTipo == 2) {
                    // Factura de compra = egreso
                    $agrupados[$clave]['total_egresos'] += $totalAbono;
                }
                
                $agrupados[$clave]['cantidad_abonos'] += intval($row['cantidad_abonos'] ?? 0);
            }
            
            foreach ($agrupados as $clave => $grupo) {
                $metodoPago = $grupo['metodo_pago'];
                $nombreMetodo = $mapaNombreFormaPago[$metodoPago] ?? $metodoPago;
                
                $cuentaId = $grupo['cuenta_id'] ?? null;
                $cuentaNombre = $grupo['cuenta_nombre'] ?? 'Sin cuenta específica';
                $cuentaBanco = $grupo['cuenta_banco'] ?? '';
                $cuentaCompleta = $cuentaNombre . (!empty($cuentaBanco) ? ' - ' . $cuentaBanco : '');
                
                $ingresos = floatval($grupo['total_ingresos'] ?? 0);
                $egresos = floatval($grupo['total_egresos'] ?? 0);
                
                // Aplicar filtro de tipo si está especificado
                if ($tipoMovimiento !== null) {
                    if ($tipoMovimiento == 1 && $ingresos == 0) {
                        continue; // Solo ingresos
                    }
                    if ($tipoMovimiento == 2 && $egresos == 0) {
                        continue; // Solo egresos
                    }
                }
                
                $neto = $ingresos - $egresos;
                $cantidadMovimientos = $grupo['cantidad_abonos'];
                
                // Agrupar por método de pago
                if (!isset($porMetodo[$metodoPago])) {
                    $porMetodo[$metodoPago] = [
                        'nombre' => $nombreMetodo,
                        'total_ingresos' => 0,
                        'total_egresos' => 0,
                        'total_neto' => 0,
                        'cuentas' => []
                    ];
                }
                
                $porMetodo[$metodoPago]['total_ingresos'] += $ingresos;
                $porMetodo[$metodoPago]['total_egresos'] += $egresos;
                $porMetodo[$metodoPago]['total_neto'] += $neto;
                
                // Agregar detalle por cuenta
                $porMetodo[$metodoPago]['cuentas'][] = [
                    'cuenta_id' => $cuentaId,
                    'cuenta_nombre' => $cuentaCompleta,
                    'ingresos' => $ingresos,
                    'egresos' => $egresos,
                    'neto' => $neto,
                    'cantidad' => $cantidadMovimientos
                ];
                
                // Agrupar por cuenta bancaria
                $claveCuenta = $cuentaId ?? 'sin_cuenta';
                if (!isset($porCuenta[$claveCuenta])) {
                    $porCuenta[$claveCuenta] = [
                        'cuenta_id' => $cuentaId,
                        'cuenta_nombre' => $cuentaCompleta,
                        'total_ingresos' => 0,
                        'total_egresos' => 0,
                        'total_neto' => 0,
                        'metodos' => []
                    ];
                }
                
                $porCuenta[$claveCuenta]['total_ingresos'] += $ingresos;
                $porCuenta[$claveCuenta]['total_egresos'] += $egresos;
                $porCuenta[$claveCuenta]['total_neto'] += $neto;
                
                if (!isset($porCuenta[$claveCuenta]['metodos'][$metodoPago])) {
                    $porCuenta[$claveCuenta]['metodos'][$metodoPago] = [
                        'nombre' => $nombreMetodo,
                        'ingresos' => 0,
                        'egresos' => 0,
                        'neto' => 0
                    ];
                }
                
                $porCuenta[$claveCuenta]['metodos'][$metodoPago]['ingresos'] += $ingresos;
                $porCuenta[$claveCuenta]['metodos'][$metodoPago]['egresos'] += $egresos;
                $porCuenta[$claveCuenta]['metodos'][$metodoPago]['neto'] += $neto;
                
                $totalGeneral += $neto;
            }
            
            $resultado['por_metodo'] = $porMetodo;
            $resultado['por_cuenta'] = $porCuenta;
            $resultado['total_general'] = $totalGeneral;
            
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $resultado;
    }

    /**
     * Lista todos los lotes de facturación
     * @param mysqli $conexion
     * @param array $config
     * @return mysqli_result
     */
    public static function listarLotesFacturacion(mysqli $conexion, array $config) {
        $sql = "SELECT l.*, 
                CONCAT_WS(' ', u.uss_nombre, u.uss_apellido1, u.uss_apellido2) as usuario_creador_nombre,
                COUNT(DISTINCT fc.fcu_id) as facturas_generadas,
                SUM(CASE WHEN fc.fcu_status = '".COBRADA."' THEN 1 ELSE 0 END) as facturas_cobradas,
                SUM(CASE WHEN fc.fcu_status = '".POR_COBRAR."' THEN 1 ELSE 0 END) as facturas_por_cobrar
                FROM ".BD_FINANCIERA.".finanzas_lotes_facturacion l
                LEFT JOIN ".BD_GENERAL.".usuarios u ON u.uss_id = l.lote_usuario_responsable 
                    AND u.institucion = l.institucion AND u.year = l.year
                LEFT JOIN ".BD_FINANCIERA.".finanzas_cuentas fc ON fc.fcu_lote_id = l.id 
                    AND fc.institucion = l.institucion AND fc.year = l.year
                WHERE l.institucion = {$config['conf_id_institucion']} 
                AND l.year = {$_SESSION["bd"]}
                GROUP BY l.id
                ORDER BY l.lote_fecha DESC";
        
        return mysqli_query($conexion, $sql);
    }

    /**
     * Obtiene los detalles de un lote de facturación
     * @param mysqli $conexion
     * @param array $config
     * @param string $loteId
     * @return array
     */
    public static function traerDatosLote(mysqli $conexion, array $config, string $loteId) {
        $sql = "SELECT l.*, 
                CONCAT_WS(' ', u.uss_nombre, u.uss_apellido1, u.uss_apellido2) as usuario_creador_nombre
                FROM ".BD_FINANCIERA.".finanzas_lotes_facturacion l
                LEFT JOIN ".BD_GENERAL.".usuarios u ON u.uss_id = l.lote_usuario_responsable 
                    AND u.institucion = l.institucion AND u.year = l.year
                WHERE l.id = '".mysqli_real_escape_string($conexion, $loteId)."'
                AND l.institucion = {$config['conf_id_institucion']} 
                AND l.year = {$_SESSION["bd"]}
                LIMIT 1";
        
        $consulta = mysqli_query($conexion, $sql);
        if ($consulta && mysqli_num_rows($consulta) > 0) {
            return mysqli_fetch_array($consulta, MYSQLI_BOTH);
        }
        return [];
    }

    /**
     * Lista las facturas de un lote específico
     * @param mysqli $conexion
     * @param array $config
     * @param string $loteId
     * @return mysqli_result
     */
    public static function listarFacturasPorLote(mysqli $conexion, array $config, string $loteId) {
        $sql = "SELECT fc.*, 
                CONCAT_WS(' ', u.uss_nombre, u.uss_nombre2, u.uss_apellido1, u.uss_apellido2) as usuario_nombre,
                (SELECT SUM(ti.subtotal) FROM ".BD_FINANCIERA.".transaction_items ti 
                 WHERE ti.id_transaction = fc.fcu_id AND ti.institucion = fc.institucion AND ti.year = fc.year) as total_items,
                (SELECT SUM(pi.payment) FROM ".BD_FINANCIERA.".payments_invoiced pi
                 WHERE pi.invoiced = fc.fcu_id AND pi.is_deleted = 0 
                 AND pi.institucion = fc.institucion AND pi.year = fc.year) as total_abonado
                FROM ".BD_FINANCIERA.".finanzas_cuentas fc
                LEFT JOIN ".BD_GENERAL.".usuarios u ON u.uss_id = fc.fcu_usuario 
                    AND u.institucion = fc.institucion AND u.year = fc.year
                WHERE fc.fcu_lote_id = '".mysqli_real_escape_string($conexion, $loteId)."'
                AND fc.fcu_anulado = 0
                AND (fc.fcu_status IS NULL OR fc.fcu_status != '".EN_PROCESO."')
                AND fc.institucion = {$config['conf_id_institucion']} 
                AND fc.year = {$_SESSION["bd"]}
                ORDER BY fc.fcu_fecha DESC, fc.fcu_consecutivo DESC";
        
        return mysqli_query($conexion, $sql);
    }
}