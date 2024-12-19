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
      $sqlInicial = "SELECT fc.*, uss.*, fc.id_nuevo AS id_nuevo_movimientos FROM " . BD_FINANCIERA . ".finanzas_cuentas fc
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
           
      $sqlInicial = "SELECT sum(fcu_valor)  as valor FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_tipo='$tipo' AND fcu_anulado='0' AND institucion='$institucion' AND year='$year' ";     
      $sql = $sqlInicial ;
      return Servicios::SelectSql($sql);
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
        $totalNeto = $valorAdicional;

        try {
            $consulta = mysqli_query($conexion,"SELECT SUM(ti.subtotal + CASE WHEN ti.tax != 0 THEN (ti.subtotal * (tax.fee / 100)) ELSE 0 END) AS totalItems FROM ".BD_FINANCIERA.".transaction_items ti
            LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion = {$config['conf_id_institucion']} AND tax.year = {$_SESSION["bd"]}
            WHERE ti.id_transaction = '{$idTransaction}'
            AND ti.type_transaction = '{$tipo}'
            AND ti.institucion = {$config['conf_id_institucion']}
            AND ti.year = {$_SESSION["bd"]}
            GROUP BY ti.id_transaction");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        if(mysqli_num_rows($consulta)>0) {
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
            $totalNeto += $resultado['totalItems'];
        }

        return $totalNeto;
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
            $consulta = mysqli_query($conexion, "SELECT ti.id AS idtx, i.id AS idit, i.name, i.price AS priceItem, ti.price AS priceTransaction, ti.cantity, ti.subtotal, ti.description, ti.discount, ti.tax
            FROM ".BD_FINANCIERA.".transaction_items ti
            INNER JOIN ".BD_FINANCIERA.".items i ON i.id = ti.id_item AND i.institucion = {$config['conf_id_institucion']} AND i.year = {$_SESSION["bd"]}
            WHERE ti.id_transaction = '{$idTransaction}'
            AND ti.type_transaction = '{$tipo}'
            AND ti.institucion = {$config['conf_id_institucion']}
            AND ti.year = {$_SESSION["bd"]}
            ORDER BY id_autoincremental");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me trae todos los items
     * @param mysqli $conexion
     * @param array $config
     * 
     * @return mysqli_result $consulta
    **/
    public static function listarItems (
        mysqli $conexion, 
        array $config
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".items WHERE status=0 AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");
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

        $codigo=Utilidades::generateCode("IT");
        try {
            mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".items (id, name, price, tax, description, institucion, year)VALUES('".$codigo."', '".$POST["nombre"]."', ".$POST["precio"].", '".$POST["iva"]."', '".$POST["descrip"]."', {$config['conf_id_institucion']}, {$_SESSION["bd"]});");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
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
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".items WHERE id='{$idItem}' AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

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

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".items SET name='".$POST["nombre"]."', price=".$POST["precio"].", tax='".$POST["iva"]."', description='".$POST["descrip"]."' WHERE id='".$POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
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
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".items SET status=1 WHERE id='{$idItem}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
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

        try {
            $consulta = mysqli_query($conexion, "SELECT id_item FROM ".BD_FINANCIERA.".transaction_items WHERE id_item='{$idItem}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
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
    public static function listarAbonos (
        mysqli $conexion, 
        array $config
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT pay.id, pay.registration_date, pay.responsible_user, pay.invoiced, pay.cod_payment, pay.payment_method, pay.voucher,
            uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2,
            pi.invoiced as numeroFactura
            FROM ".BD_FINANCIERA.".payments pay
            INNER JOIN ".BD_GENERAL.".usuarios uss 
                ON uss_id=responsible_user 
                AND uss.institucion={$config['conf_id_institucion']} 
                AND uss.year={$_SESSION["bd"]}
            INNER JOIN ".BD_FINANCIERA.".payments_invoiced pi 
                ON pi.payments=pay.cod_payment
            WHERE 
                is_deleted=0 
            AND pay.institucion = {$config['conf_id_institucion']} 
            AND pay.year = {$_SESSION["bd"]}
            ORDER BY pay.id DESC
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
            WHERE fcu_anulado=0 {$filtro} AND fcu.institucion = {$config['conf_id_institucion']} AND fcu.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }

    /**
     * Este metodo me guarda un abono
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * @param array $FILES
     * 
     * @return string $codigo
    **/
    public static function guardarAbonos (
        mysqli $conexion, 
        array $config, 
        array $POST, 
        array $FILES
    )
    {

        $comprobante= '';

        if (!empty($FILES['comprobante']['name'])) {
            $destino = ROOT_PATH.'/main-app/files/comprobantes';
            $explode = explode(".", $FILES['comprobante']['name']);
            $extension = end($explode);
            $comprobante= uniqid('abono_'.$POST["cliente"].'_') . "." . $extension;
            @unlink($destino . "/" . $comprobante);
            move_uploaded_file($FILES['comprobante']['tmp_name'], $destino . "/" . $comprobante);
        }

        try {
            mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".payments (responsible_user, invoiced, cod_payment, type_payments, payment_method, observation, voucher, note, institucion, year)VALUES('{$_SESSION["id"]}', '".$POST["cliente"]."', '".$POST["codigoUnico"]."', '".$POST["tipoTransaccion"]."', '".$POST["metodoPago"]."', '".$POST["obser"]."', '".$comprobante."', '".$POST["notas"]."', {$config['conf_id_institucion']}, {$_SESSION["bd"]});");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        $idRegistro = mysqli_insert_id($conexion);

        return $idRegistro;
    }

    /**
     * Este metodo me trae la informacion de un Abono
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
            $consulta = mysqli_query($conexion, "SELECT pay.id, pay.registration_date, pay.responsible_user, pay.invoiced, pay.cod_payment, pay.payment_method, pay.voucher,
            uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2,
            pi.invoiced as numeroFactura, pi.payment as valorAbono
            FROM ".BD_FINANCIERA.".payments pay
            INNER JOIN ".BD_GENERAL.".usuarios uss 
                ON uss_id=responsible_user 
                AND uss.institucion={$config['conf_id_institucion']} 
                AND uss.year={$_SESSION["bd"]}
            INNER JOIN ".BD_FINANCIERA.".payments_invoiced pi 
                ON pi.payments=pay.cod_payment
            WHERE 
                is_deleted=0 
            AND pay.id='{$idAbono}'
            AND pay.institucion = {$config['conf_id_institucion']} 
            AND pay.year = {$_SESSION["bd"]}
            ");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me actualiza un abono
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

        if (!empty($FILES['comprobante']['name'])) {
            $destino = ROOT_PATH.'/main-app/files/comprobantes';
            $explode = explode(".", $FILES['comprobante']['name']);
            $extension = end($explode);
            $comprobante= uniqid('abono_'.$POST["cliente"].'_') . "." . $extension;
            @unlink($destino . "/" . $comprobante);
            move_uploaded_file($FILES['comprobante']['tmp_name'], $destino . "/" . $comprobante);
        
            try {
                mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".payments SET voucher='".$comprobante."' WHERE id='".$POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".payments SET invoiced='".$POST["cliente"]."', payment_method='".$POST["metodoPago"]."', observation='".$POST["obser"]."', note='".$POST["notas"]."' WHERE id='".$POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo me actualiza un abono
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

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".payments SET is_deleted=1 WHERE id='{$idAbono}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
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
        $fechaFinal = !empty($POST["fechaFinal"]) ? $POST["fechaFinal"] : NULL;
        $dias = implode(',',$POST["dias"]);

        try{
            mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".recurring_invoices(id, date_start, detail, additional_value, invoice_type, observation, user, date_finish, frequency, days_in_month, payment_method, responsible_user, institucion, year)VALUES('" .$POST["id"]. "', '" . $POST["fechaInicio"] . "','" . $POST["detalle"] . "','" . $POST["valor"] . "','" . $POST["tipo"] . "','" . $POST["obs"] . "','" . $POST["usuario"] . "', '" . $fechaFinal . "','" . $POST["frecuencia"] . "', '" . $dias . "', '" . $POST["metodoPago"] . "','{$_SESSION["id"]}', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
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
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".recurring_invoices ri
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=responsible_user AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ADMIN.".localidad_ciudades ON ciu_id=uss_lugar_nacimiento
            LEFT JOIN ".BD_ADMIN.".localidad_departamentos ON dep_id=ciu_departamento
            WHERE id='{$idRecurrente}' AND ri.institucion = {$config['conf_id_institucion']} AND ri.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
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
        $dias = implode(',',$POST["dias"]);

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".recurring_invoices SET detail='".$POST["detalle"]."', user='".$POST["usuario"]."', days_in_month='".$dias."', payment_method='".$POST["metodoPago"]."', observation='".$POST["obs"]."', invoice_type='".$POST["tipo"]."', additional_value='".$POST["valor"]."' WHERE id='".$POST["id"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
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
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".recurring_invoices WHERE is_deleted=0 AND date_start <= CURDATE() AND (date_finish >= CURDATE() OR date_finish = '0000-00-00')");
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
        switch ($datosRecurrente["payment_method"]){
            case "EFECTIVO":
                $metodoPago= 1;
            break;
            case "CHEQUE":
                $metodoPago= 2;
            break;
            case "T_DEBITO":
                $metodoPago= 3;
            break;
            case "T_CREDITO":
                $metodoPago= 4;
            break;
            case "TRANSFERENCIA":
                $metodoPago= 5;
            break;
            case "OTROS":
                $metodoPago= 6;
            break;
        }

        $idFactura=Utilidades::generateCode("FCU");

        try{
            mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".finanzas_cuentas(fcu_id, fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, fcu_usuario, fcu_anulado, fcu_forma_pago, fcu_cerrado, institucion, year)VALUES('" .$idFactura . "', now(),'" . $datosRecurrente["detail"] . "','" . $datosRecurrente["additional_value"] . "','" . $datosRecurrente["invoice_type"] . "','" . $datosRecurrente["observation"] . "','" . $datosRecurrente["user"] . "',0,'" . $metodoPago . "',0, {$datosRecurrente['institucion']}, '{$datosRecurrente["year"]}')");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }

        try {
            $itemsConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".transaction_items WHERE id_transaction = '{$datosRecurrente["id"]}' AND type_transaction = 'INVOICE_RECURRING' AND institucion = {$datosRecurrente["institucion"]} AND year = {$datosRecurrente["year"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $numDatos = mysqli_num_rows($itemsConsulta);

        if ($numDatos > 0) {

            while ($fila = mysqli_fetch_array($itemsConsulta, MYSQLI_BOTH)) {

                $idItems=Utilidades::generateCode("TXI_");

                try {
                    mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".transaction_items(id, id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, description, price, tax)VALUES('".$idItems."', '" .$idFactura . "', 'INVOICE', '".$fila['discount']."', '".$fila['cantity']."', '".$fila['subtotal']."', '".$fila['id_item']."', {$fila['institucion']}, '{$fila['year']}', '".$fila['description']."', '".$fila['price']."', '".$fila['tax']."')");
                } catch (Exception $e) {
                    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
                }

            }
        }

    }

    /**
     * Este metodo me calcula el total de Abonos a una factura
     * @param mysqli $conexion
     * @param array $config
     * @param string $factura
     * 
     * @return float $total
    **/
    public static function calcularTotalAbonado (
        mysqli $conexion, 
        array $config,
        string $factura
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT SUM(pi.payment) as totalAbono FROM ".BD_FINANCIERA.".payments_invoiced pi
            INNER JOIN ".BD_FINANCIERA.".payments p ON p.cod_payment=pi.payments AND p.institucion = {$config['conf_id_institucion']} AND p.year = {$_SESSION["bd"]}
            WHERE pi.invoiced='{$factura}' AND p.is_deleted=0 AND pi.institucion = {$config['conf_id_institucion']} AND pi.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
        $total = 0;
        if (mysqli_num_rows($consulta) > 0){
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
            $total = $resultado['totalAbono'];
        }

        return $total;
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
            ORDER BY id_nuevo ASC
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
    public static function calcularTotalAbonadoCliente (
        mysqli $conexion, 
        array $config,
        string $cliente,
        string $codAbono
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT SUM(pi.payment) as totalAbono FROM ".BD_FINANCIERA.".payments p
            INNER JOIN ".BD_FINANCIERA.".payments_invoiced pi ON p.cod_payment=pi.payments AND pi.institucion = {$config['conf_id_institucion']} AND pi.year = {$_SESSION["bd"]}
            WHERE p.invoiced='{$cliente}' AND pi.payments='{$codAbono}' AND p.is_deleted=0 AND p.institucion = {$config['conf_id_institucion']} AND p.year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
        $total = 0;
        if (mysqli_num_rows($consulta) > 0){
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
            $total = $resultado['totalAbono'];
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
        string   $codAbono
    )
    {
        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".payments_invoiced
            WHERE payments='{$codAbono}' AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
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
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
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
    public static function eliminarImpuestos (
        mysqli $conexion, 
        array $config, 
        string $idImpuesto
    )
    {

        try {
            mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".taxes SET is_deleted=1 WHERE id='{$idImpuesto}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
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
            $consulta = mysqli_query($conexion, "SELECT SUM(cantity) AS cantidadTotal, i.id, i.name FROM ".BD_FINANCIERA.".transaction_items ti
            INNER JOIN ".BD_FINANCIERA.".items i ON i.id=ti.id_item AND i.institucion={$config['conf_id_institucion']} AND i.year={$_SESSION["bd"]}
            WHERE ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
            GROUP BY id_item
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
            $consulta = mysqli_query($conexion, "SELECT SUM((ti.price * ti.cantity * (1 - ti.discount / 100) * CASE WHEN ti.tax != 0 THEN (1 + tax.fee / 100) ELSE 1 END) + fc.fcu_valor) AS totalPagado, uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2 
            FROM ".BD_FINANCIERA.".finanzas_cuentas fc
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=fcu_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            INNER JOIN ".BD_FINANCIERA.".transaction_items ti ON fc.fcu_id = ti.id_transaction  AND ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion = {$config['conf_id_institucion']} AND tax.year = {$_SESSION["bd"]}
            WHERE fc.fcu_anulado = 0 AND fc.fcu_status = '".COBRADA."' AND fc.institucion={$config['conf_id_institucion']} AND fc.year={$_SESSION["bd"]}
            GROUP BY fc.fcu_usuario
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
            $consulta = mysqli_query($conexion, "SELECT 
                LPAD(MONTH(fc.fcu_fecha), 2, '0') AS mes,
                SUM(CASE WHEN fc.fcu_tipo = '1' THEN (CAST(fc.fcu_valor AS DECIMAL(10, 2)) + IFNULL(ti.totalItems, 0)) ELSE 0 END) AS totalIngresos,
                SUM(CASE WHEN fc.fcu_tipo = '2' THEN (CAST(fc.fcu_valor AS DECIMAL(10, 2)) + IFNULL(ti.totalItems, 0)) ELSE 0 END) AS totalEgresos
            FROM ".BD_FINANCIERA.".finanzas_cuentas fc
            LEFT JOIN (
                SELECT ti.id_transaction, SUM(ti.price * ti.cantity * (1 - ti.discount / 100) * CASE WHEN ti.tax != 0 THEN (1 + tax.fee / 100) ELSE 1 END) AS totalItems, ti.institucion, ti.year
                FROM ".BD_FINANCIERA.".transaction_items ti 
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion = {$config['conf_id_institucion']} AND tax.year = {$_SESSION["bd"]}
                WHERE ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
                GROUP BY ti.id_transaction
            ) ti ON fc.fcu_id = ti.id_transaction AND ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
            WHERE fc.institucion={$config['conf_id_institucion']} AND fc.year={$_SESSION["bd"]}
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
            $consulta = mysqli_query($conexion, "SELECT 
                LPAD(MONTH(fc.fcu_fecha), 2, '0') AS mes,
                SUM(CASE WHEN fc.fcu_tipo = '1' AND fc.fcu_status = 'POR_COBRAR' THEN (CAST(fc.fcu_valor AS DECIMAL(10, 2)) + IFNULL(ti.totalItems, 0)) ELSE 0 END) AS totalPorCobrar
            FROM ".BD_FINANCIERA.".finanzas_cuentas fc
            LEFT JOIN (
                SELECT ti.id_transaction, SUM(ti.price * ti.cantity * (1 - ti.discount / 100) * CASE WHEN ti.tax != 0 THEN (1 + tax.fee / 100) ELSE 1 END) AS totalItems, ti.institucion, ti.year
                FROM ".BD_FINANCIERA.".transaction_items ti
                LEFT JOIN ".BD_FINANCIERA.".taxes tax ON tax.id=ti.tax AND tax.institucion = {$config['conf_id_institucion']} AND tax.year = {$_SESSION["bd"]}
                WHERE ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
                GROUP BY ti.id_transaction
            ) ti ON fc.fcu_id = ti.id_transaction AND ti.institucion={$config['conf_id_institucion']} AND ti.year={$_SESSION["bd"]}
            WHERE fc.institucion={$config['conf_id_institucion']} AND fc.year={$_SESSION["bd"]}
            GROUP BY mes
            ORDER BY mes");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        return $consulta;
    }
}