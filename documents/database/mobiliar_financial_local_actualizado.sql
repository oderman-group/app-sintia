-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-01-2026 a las 10:40:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mobiliar_financial_local`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuration`
--

CREATE TABLE `configuration` (
  `id` int(11) NOT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `consecutive_start` varchar(45) DEFAULT NULL,
  `invoice_footer` longtext DEFAULT NULL,
  `institucion` int(10) DEFAULT NULL,
  `year` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `finanzas_cobros_masivos`
--

CREATE TABLE `finanzas_cobros_masivos` (
  `mas_id` int(10) UNSIGNED NOT NULL,
  `mas_nombre` varchar(255) DEFAULT NULL,
  `mas_valor` int(10) UNSIGNED DEFAULT NULL,
  `institucion` int(10) DEFAULT NULL,
  `year` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `finanzas_cuentas`
--

CREATE TABLE `finanzas_cuentas` (
  `fcu_id` int(10) UNSIGNED NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp() COMMENT 'Fecha real de registro (automática)',
  `fcu_fecha` date DEFAULT NULL,
  `fcu_detalle` varchar(255) DEFAULT NULL,
  `fcu_valor` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Valor adicional o extra. Se suma a los items asociados.',
  `fcu_tipo` varchar(45) DEFAULT NULL,
  `fcu_observaciones` longtext DEFAULT NULL,
  `fcu_usuario` varchar(45) DEFAULT NULL,
  `fcu_anulado` int(10) UNSIGNED DEFAULT 0 COMMENT '1=si 0=no',
  `fcu_cerrado` int(10) UNSIGNED DEFAULT NULL,
  `fcu_fecha_cerrado` datetime DEFAULT NULL,
  `fcu_cerrado_usuario` varchar(45) DEFAULT NULL,
  `fcu_consecutivo` varchar(45) DEFAULT NULL,
  `fcu_valor_letras` varchar(300) DEFAULT NULL,
  `fcu_status` enum('POR_COBRAR','COBRADA','ANULADA','EN_PROCESO') DEFAULT NULL,
  `institucion` int(10) DEFAULT NULL,
  `year` char(4) DEFAULT NULL,
  `fcu_lote_id` int(10) UNSIGNED DEFAULT NULL,
  `fcu_cuenta_bancaria_id` int(10) UNSIGNED DEFAULT NULL,
  `fcu_created_by` varchar(45) DEFAULT NULL,
  `fcu_origen` enum('NORMAL','RECURRENTE') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `finanzas_cuentas_bancarias`
--

CREATE TABLE `finanzas_cuentas_bancarias` (
  `cba_id` int(10) UNSIGNED NOT NULL,
  `cba_nombre` varchar(255) DEFAULT NULL,
  `cba_banco` varchar(255) DEFAULT NULL,
  `cba_numero_cuenta` varchar(100) DEFAULT NULL,
  `cba_tipo` enum('AHORROS','CORRIENTE','NEOQUI','DAVIPLATA','CAJA_METALICA','OTRO') DEFAULT 'AHORROS',
  `cba_metodo_pago_asociado` varchar(50) DEFAULT NULL,
  `cba_activa` tinyint(4) DEFAULT 1,
  `cba_observaciones` text DEFAULT NULL,
  `institucion` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `cba_saldo_inicial` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `finanzas_lotes_facturacion`
--

CREATE TABLE `finanzas_lotes_facturacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `lote_nombre` varchar(255) DEFAULT NULL,
  `lote_fecha` datetime DEFAULT NULL,
  `lote_usuario_responsable` varchar(50) DEFAULT NULL,
  `lote_tipo_grupo` enum('ESTUDIANTES','DOCENTES','DIRECTIVOS','ACUDIENTES','OTROS') DEFAULT 'ESTUDIANTES',
  `lote_criterios` text DEFAULT NULL,
  `lote_items` text DEFAULT NULL,
  `lote_total_facturas` int(11) UNSIGNED DEFAULT 0,
  `lote_estado` enum('PROCESANDO','COMPLETADO','ERROR') DEFAULT 'PROCESANDO',
  `lote_observaciones` text DEFAULT NULL,
  `institucion` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items`
--

CREATE TABLE `items` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(15,2) UNSIGNED DEFAULT NULL,
  `tax` char(5) DEFAULT NULL,
  `status` int(11) DEFAULT 0 COMMENT 'Eliminado o no',
  `description` longtext DEFAULT NULL,
  `institucion` int(10) DEFAULT NULL,
  `year` char(4) DEFAULT NULL,
  `item_type` enum('D','C') DEFAULT 'D',
  `application_time` enum('ANTE_IMPUESTO','POST_IMPUESTO') DEFAULT NULL COMMENT 'Define si el valor del item se debe aplicar antes o después de los impuestos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments_invoiced`
--

CREATE TABLE `payments_invoiced` (
  `id` int(11) UNSIGNED NOT NULL,
  `responsible_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que registró el abono',
  `payment_user` varchar(50) DEFAULT NULL COMMENT 'Usuario asociado al pago (a quien se le hace el abono)',
  `type_payments` enum('INVOICE','ACCOUNT') DEFAULT NULL COMMENT 'Tipo de abono: INVOICE=con factura, ACCOUNT=sin factura',
  `payment_tipo` enum('INGRESO','EGRESO') DEFAULT NULL COMMENT 'Tipo de movimiento: INGRESO o EGRESO',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Método de pago',
  `payment_cuenta_bancaria_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID de cuenta bancaria',
  `observation` text DEFAULT NULL COMMENT 'Observaciones',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'Ruta del adjunto/comprobante',
  `note` text DEFAULT NULL COMMENT 'Notas adicionales',
  `fecha_registro` datetime DEFAULT current_timestamp() COMMENT 'Fecha real de registro (automática)',
  `fecha_documento` date DEFAULT NULL COMMENT 'Fecha del documento (llenada por usuario, validada)',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT 'Soft delete: 1=anulado, 0=activo',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Fecha de anulación',
  `deleted_by` varchar(50) DEFAULT NULL COMMENT 'Usuario que anuló el abono',
  `invoiced` int(10) UNSIGNED NOT NULL COMMENT 'Referencia a finanzas_cuentas.id_nuevo (puede ser NULL para ingresos/egresos sin factura)',
  `payment` decimal(15,2) UNSIGNED DEFAULT NULL COMMENT 'Valor del abono',
  `cantity` int(11) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `subtotal` decimal(15,2) UNSIGNED DEFAULT NULL,
  `institucion` int(11) NOT NULL,
  `year` varchar(4) NOT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Para relacionar las facturas con lo abonado';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quotes`
--

CREATE TABLE `quotes` (
  `id` int(10) UNSIGNED NOT NULL,
  `quote_date` date DEFAULT current_timestamp(),
  `detail` varchar(255) DEFAULT NULL,
  `general_price` decimal(15,2) UNSIGNED DEFAULT NULL,
  `comments` longtext DEFAULT NULL,
  `user` varchar(45) DEFAULT NULL,
  `responsible_user` varchar(45) DEFAULT NULL,
  `payment_option` enum('EFECTIVO','CHEQUE','T_DEBITO','T_CREDITO','TRANSFERENCIA','OTROS') DEFAULT NULL,
  `invoiced` int(10) UNSIGNED DEFAULT NULL COMMENT 'Factura asociada',
  `institucion` int(10) DEFAULT NULL,
  `year` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recurring_invoices`
--

CREATE TABLE `recurring_invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_date` datetime DEFAULT current_timestamp() COMMENT 'Fecha en que se crea el registro',
  `detail` varchar(255) DEFAULT NULL,
  `user` varchar(45) DEFAULT NULL,
  `days_in_month` varchar(100) DEFAULT NULL,
  `date_start` date NOT NULL COMMENT 'Fecha de inicio de la factura',
  `date_finish` date DEFAULT NULL COMMENT 'Fecha para finalizar la generación de la factura (OPCIONAL)',
  `next_generation_date` date DEFAULT NULL COMMENT 'Fecha de laproxima vez que se generara la factura',
  `frequency` int(11) DEFAULT NULL COMMENT 'Frecuencia con la que se generara la factura',
  `invoice_type` varchar(45) DEFAULT NULL,
  `observation` longtext DEFAULT NULL,
  `additional_value` decimal(15,2) UNSIGNED DEFAULT NULL,
  `responsible_user` varchar(45) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT 'Si esta eliminada o no',
  `institucion` int(11) NOT NULL,
  `year` varchar(4) DEFAULT NULL,
  `cantidad_creaciones` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Veces que se creó esta factura oficialmente.',
  `ids_facturas_creadas` text DEFAULT NULL,
  `fecha_ultima_generacion` datetime DEFAULT NULL COMMENT 'Fecha en la que se ejecutó y se creó la última factura'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `taxes`
--

CREATE TABLE `taxes` (
  `id` int(10) UNSIGNED NOT NULL,
  `type_tax` enum('IVA','ICO','ICUI','OTRO') NOT NULL,
  `name` varchar(45) NOT NULL,
  `fee` float NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `institucion` int(11) NOT NULL,
  `year` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Para gestionar impuestos';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id_autoincremental` int(11) UNSIGNED NOT NULL,
  `id_transaction` int(10) UNSIGNED DEFAULT NULL,
  `type_transaction` enum('INVOICE','QUOTE','INVOICE_RECURRING') DEFAULT NULL,
  `discount` varchar(45) DEFAULT NULL,
  `cantity` int(10) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(15,2) UNSIGNED DEFAULT NULL,
  `institucion` int(10) DEFAULT NULL,
  `year` char(4) DEFAULT NULL,
  `id_item` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) UNSIGNED DEFAULT NULL,
  `tax` int(10) UNSIGNED DEFAULT NULL,
  `factura_recurrente_id` int(10) UNSIGNED DEFAULT NULL,
  `cotizacion_id` int(10) UNSIGNED DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL COMMENT 'Sirve para el histórico',
  `item_type` enum('D','C') DEFAULT NULL COMMENT 'Sirve para el histórico',
  `application_time` enum('ANTE_IMPUESTO','POST_IMPUESTO') DEFAULT NULL COMMENT 'Sirve para el histórico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuration`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `finanzas_cobros_masivos`
--
ALTER TABLE `finanzas_cobros_masivos`
  ADD PRIMARY KEY (`mas_id`);

--
-- Indices de la tabla `finanzas_cuentas`
--
ALTER TABLE `finanzas_cuentas`
  ADD PRIMARY KEY (`fcu_id`),
  ADD UNIQUE KEY `idx_id_nuevo_unique` (`fcu_id`),
  ADD KEY `Index_ordinarios_8` (`fcu_tipo`,`fcu_usuario`),
  ADD KEY `idx_lote_id` (`fcu_lote_id`),
  ADD KEY `idx_cuenta_bancaria` (`fcu_cuenta_bancaria_id`),
  ADD KEY `idx_fecha_registro` (`fecha_registro`),
  ADD KEY `idx_fecha_documento` (`fcu_fecha`),
  ADD KEY `idx_fcu_usuario` (`fcu_usuario`),
  ADD KEY `idx_fcu_lote_id` (`fcu_lote_id`);

--
-- Indices de la tabla `finanzas_cuentas_bancarias`
--
ALTER TABLE `finanzas_cuentas_bancarias`
  ADD PRIMARY KEY (`cba_id`),
  ADD KEY `idx_institucion_year` (`institucion`,`year`),
  ADD KEY `idx_activa` (`cba_activa`),
  ADD KEY `idx_metodo_pago` (`cba_metodo_pago_asociado`),
  ADD KEY `idx_finanzas_cuentas_bancarias_activa` (`cba_activa`);

--
-- Indices de la tabla `finanzas_lotes_facturacion`
--
ALTER TABLE `finanzas_lotes_facturacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_institucion_year` (`institucion`,`year`),
  ADD KEY `idx_estado` (`lote_estado`),
  ADD KEY `idx_tipo_grupo` (`lote_tipo_grupo`),
  ADD KEY `idx_finanzas_lotes_estado` (`lote_estado`);

--
-- Indices de la tabla `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indices de la tabla `payments_invoiced`
--
ALTER TABLE `payments_invoiced`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payments_invoiced_invoiced` (`invoiced`),
  ADD KEY `idx_payments_invoiced_fecha_registro` (`fecha_registro`),
  ADD KEY `idx_payments_invoiced_fecha_documento` (`fecha_documento`),
  ADD KEY `idx_payments_invoiced_payment_tipo` (`payment_tipo`),
  ADD KEY `idx_payments_invoiced_responsible_user` (`responsible_user`),
  ADD KEY `idx_payments_invoiced_payment_user` (`payment_user`),
  ADD KEY `payments_invoiced_finanzas_cuentas_bancarias_FK` (`payment_cuenta_bancaria_id`);

--
-- Indices de la tabla `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `recurring_invoices`
--
ALTER TABLE `recurring_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id_autoincremental`),
  ADD KEY `idx_transaction_items_id_transaction` (`id_transaction`),
  ADD KEY `idx_transaction_items_id_transaction_type` (`id_transaction`,`type_transaction`),
  ADD KEY `transaction_items_taxes_fk` (`tax`),
  ADD KEY `transaction_items_items_fk` (`id_item`),
  ADD KEY `transaction_items_quotes_fk` (`cotizacion_id`),
  ADD KEY `transaction_items_recurring_invoices_FK` (`factura_recurrente_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuration`
--
ALTER TABLE `configuration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `finanzas_cobros_masivos`
--
ALTER TABLE `finanzas_cobros_masivos`
  MODIFY `mas_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `finanzas_cuentas`
--
ALTER TABLE `finanzas_cuentas`
  MODIFY `fcu_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `finanzas_cuentas_bancarias`
--
ALTER TABLE `finanzas_cuentas_bancarias`
  MODIFY `cba_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `finanzas_lotes_facturacion`
--
ALTER TABLE `finanzas_lotes_facturacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `payments_invoiced`
--
ALTER TABLE `payments_invoiced`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recurring_invoices`
--
ALTER TABLE `recurring_invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id_autoincremental` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `finanzas_cuentas`
--
ALTER TABLE `finanzas_cuentas`
  ADD CONSTRAINT `finanzas_cuentas_finanzas_cuentas_bancarias_FK` FOREIGN KEY (`fcu_cuenta_bancaria_id`) REFERENCES `finanzas_cuentas_bancarias` (`cba_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `finanzas_cuentas_finanzas_lotes_facturacion_FK` FOREIGN KEY (`fcu_lote_id`) REFERENCES `finanzas_lotes_facturacion` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `payments_invoiced`
--
ALTER TABLE `payments_invoiced`
  ADD CONSTRAINT `payments_invoiced_finanzas_cuentas_bancarias_FK` FOREIGN KEY (`payment_cuenta_bancaria_id`) REFERENCES `finanzas_cuentas_bancarias` (`cba_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_invoiced_finanzas_cuentas_fk` FOREIGN KEY (`invoiced`) REFERENCES `finanzas_cuentas` (`fcu_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_finanzas_cuentas_fk` FOREIGN KEY (`id_transaction`) REFERENCES `finanzas_cuentas` (`fcu_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_items_items_fk` FOREIGN KEY (`id_item`) REFERENCES `items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_items_quotes_fk` FOREIGN KEY (`cotizacion_id`) REFERENCES `quotes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_items_recurring_invoices_FK` FOREIGN KEY (`factura_recurrente_id`) REFERENCES `recurring_invoices` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_items_taxes_fk` FOREIGN KEY (`tax`) REFERENCES `taxes` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
