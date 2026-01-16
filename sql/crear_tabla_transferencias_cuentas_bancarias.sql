-- Tabla para registrar transferencias entre cuentas bancarias
-- Las transferencias NO afectan métodos de pago, solo mueven dinero entre cuentas

-- mobiliar_financial_local.transferencias_cuentas_bancarias definition

CREATE TABLE `transferencias_cuentas_bancarias` (
  `tcb_id` int(11) NOT NULL AUTO_INCREMENT,
  `tcb_cuenta_origen_id` int(10) unsigned NOT NULL,
  `tcb_cuenta_destino_id` int(10) unsigned NOT NULL,
  `tcb_monto` decimal(12,2) NOT NULL,
  `tcb_fecha` date NOT NULL,
  `tcb_observaciones` text DEFAULT NULL,
  `tcb_responsible_user` varchar(50) NOT NULL,
  `tcb_fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `institucion` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY (`tcb_id`),
  KEY `idx_cuenta_origen` (`tcb_cuenta_origen_id`,`institucion`,`year`),
  KEY `idx_cuenta_destino` (`tcb_cuenta_destino_id`,`institucion`,`year`),
  KEY `idx_fecha` (`tcb_fecha`,`institucion`,`year`),
  CONSTRAINT `transferencias_cuentas_bancarias_finanzas_cuentas_bancarias_FK` FOREIGN KEY (`tcb_cuenta_origen_id`) REFERENCES `finanzas_cuentas_bancarias` (`cba_id`) ON UPDATE CASCADE,
  CONSTRAINT `transferencias_cuentas_bancarias_finanzas_cuentas_bancarias_FK_1` FOREIGN KEY (`tcb_cuenta_destino_id`) REFERENCES `finanzas_cuentas_bancarias` (`cba_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
