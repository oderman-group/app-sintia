-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- ------------------------------------------------------
-- Server version	5.5.5-10.3.37-MariaDB-log-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

BEGIN;

--
-- Table structure for table `academico_actividad_evaluacion_preguntas`
--

DROP TABLE IF EXISTS `academico_actividad_evaluacion_preguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_evaluacion_preguntas` (
  `evp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `evp_id_evaluacion` int(10) unsigned DEFAULT NULL,
  `evp_id_pregunta` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`evp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_evaluaciones`
--

DROP TABLE IF EXISTS `academico_actividad_evaluaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_evaluaciones` (
  `eva_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eva_nombre` varchar(255) DEFAULT NULL,
  `eva_descripcion` longtext DEFAULT NULL,
  `eva_desde` datetime DEFAULT NULL,
  `eva_hasta` datetime DEFAULT NULL,
  `eva_clave` varchar(45) DEFAULT NULL,
  `eva_id_carga` int(10) unsigned DEFAULT NULL,
  `eva_periodo` int(10) unsigned DEFAULT NULL,
  `eva_estado` int(10) unsigned DEFAULT NULL,
  `eva_formato` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`eva_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_evaluaciones_estudiantes`
--

DROP TABLE IF EXISTS `academico_actividad_evaluaciones_estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_evaluaciones_estudiantes` (
  `epe_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `epe_id_estudiante` int(10) unsigned DEFAULT NULL,
  `epe_id_evaluacion` int(10) unsigned DEFAULT NULL,
  `epe_inicio` datetime DEFAULT NULL,
  `epe_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`epe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_evaluaciones_resultados`
--

DROP TABLE IF EXISTS `academico_actividad_evaluaciones_resultados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_evaluaciones_resultados` (
  `res_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `res_id_pregunta` int(10) unsigned DEFAULT NULL,
  `res_id_respuesta` int(10) unsigned DEFAULT NULL,
  `res_id_estudiante` int(10) unsigned DEFAULT NULL,
  `res_id_evaluacion` int(10) unsigned DEFAULT NULL,
  `res_id_monitoreo` int(10) unsigned DEFAULT NULL,
  `res_archivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`res_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_foro`
--

DROP TABLE IF EXISTS `academico_actividad_foro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_foro` (
  `foro_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `foro_nombre` varchar(255) DEFAULT NULL,
  `foro_descripcion` longtext DEFAULT NULL,
  `foro_id_carga` int(10) unsigned DEFAULT NULL,
  `foro_periodo` int(10) unsigned DEFAULT NULL,
  `foro_estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`foro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_foro_comentarios`
--

DROP TABLE IF EXISTS `academico_actividad_foro_comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_foro_comentarios` (
  `com_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `com_id_foro` int(10) unsigned DEFAULT NULL,
  `com_descripcion` longtext DEFAULT NULL,
  `com_id_estudiante` int(10) unsigned DEFAULT NULL,
  `com_fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`com_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_foro_respuestas`
--

DROP TABLE IF EXISTS `academico_actividad_foro_respuestas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_foro_respuestas` (
  `fore_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fore_id_estudiante` int(10) unsigned DEFAULT NULL,
  `fore_id_comentario` int(10) unsigned DEFAULT NULL,
  `fore_fecha` datetime DEFAULT NULL,
  `fore_respuesta` longtext DEFAULT NULL,
  PRIMARY KEY (`fore_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_preguntas`
--

DROP TABLE IF EXISTS `academico_actividad_preguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_preguntas` (
  `preg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `preg_descripcion` longtext DEFAULT NULL,
  `preg_valor` int(10) unsigned DEFAULT NULL,
  `preg_id_carga` int(10) unsigned DEFAULT NULL,
  `preg_folder` int(10) unsigned DEFAULT NULL,
  `preg_imagen1` varchar(255) DEFAULT NULL,
  `preg_imagen2` varchar(255) DEFAULT NULL,
  `preg_critica` int(10) unsigned DEFAULT NULL,
  `preg_tipo_pregunta` int(10) unsigned DEFAULT NULL COMMENT 'Multiple, archivo, f o v.',
  `preg_archivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`preg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_respuestas`
--

DROP TABLE IF EXISTS `academico_actividad_respuestas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_respuestas` (
  `resp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resp_descripcion` longtext DEFAULT NULL,
  `resp_correcta` int(10) unsigned DEFAULT NULL,
  `resp_id_pregunta` int(10) unsigned DEFAULT NULL,
  `resp_imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`resp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_tareas`
--

DROP TABLE IF EXISTS `academico_actividad_tareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_tareas` (
  `tar_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tar_titulo` varchar(255) DEFAULT NULL,
  `tar_descripcion` longtext DEFAULT NULL,
  `tar_id_carga` int(10) unsigned DEFAULT NULL,
  `tar_fecha_disponible` datetime DEFAULT NULL,
  `tar_fecha_entrega` datetime DEFAULT NULL,
  `tar_archivo` varchar(300) DEFAULT NULL,
  `tar_impedir_retrasos` int(10) unsigned DEFAULT NULL COMMENT 'Impide los envios retrasados',
  `tar_periodo` int(10) unsigned DEFAULT NULL,
  `tar_estado` int(10) unsigned DEFAULT NULL,
  `tar_archivo2` varchar(255) DEFAULT NULL,
  `ar_archivo3` varchar(255) DEFAULT NULL,
  `tar_peso1` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`tar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividad_tareas_entregas`
--

DROP TABLE IF EXISTS `academico_actividad_tareas_entregas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividad_tareas_entregas` (
  `ent_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ent_id_estudiante` int(10) unsigned DEFAULT NULL,
  `ent_id_actividad` int(10) unsigned DEFAULT NULL,
  `ent_archivo` varchar(255) DEFAULT NULL,
  `ent_fecha` datetime DEFAULT NULL,
  `ent_comentario` longtext DEFAULT NULL,
  `ent_archivo2` varchar(255) DEFAULT NULL,
  `ent_archivo3` varchar(255) DEFAULT NULL,
  `ent_peso1` varchar(45) DEFAULT NULL,
  `ent_peso2` varchar(45) DEFAULT NULL,
  `ent_peso3` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_actividades`
--

DROP TABLE IF EXISTS `academico_actividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_actividades` (
  `act_id` int(11) NOT NULL AUTO_INCREMENT,
  `act_descripcion` varchar(255) DEFAULT NULL,
  `act_fecha` date DEFAULT NULL,
  `act_valor` decimal(5,2) DEFAULT NULL,
  `act_id_tipo` int(11) DEFAULT NULL,
  `act_id_carga` int(11) DEFAULT NULL,
  `act_registrada` int(11) DEFAULT NULL,
  `act_fecha_creacion` datetime DEFAULT NULL,
  `act_fecha_registro` datetime DEFAULT NULL,
  `act_fecha_modificacion` datetime DEFAULT NULL,
  `act_estado` int(11) DEFAULT NULL COMMENT 'eliminada o no',
  `act_periodo` int(10) unsigned DEFAULT NULL,
  `act_id_evidencia` char(5) DEFAULT NULL,
  `act_compartir` int(10) unsigned DEFAULT NULL,
  `act_fecha_eliminacion` datetime DEFAULT NULL,
  `act_motivo_eliminacion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`act_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_areas`
--

DROP TABLE IF EXISTS `academico_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_areas` (
  `ar_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ar_nombre` varchar(100) DEFAULT NULL,
  `ar_posicion` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_areas`
--

LOCK TABLES `academico_areas` WRITE;
/*!40000 ALTER TABLE `academico_areas` DISABLE KEYS */;
INSERT INTO `academico_areas` VALUES (1,'AREA DE PRUEBA',1);
/*!40000 ALTER TABLE `academico_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academico_ausencias`
--

DROP TABLE IF EXISTS `academico_ausencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_ausencias` (
  `aus_id` int(11) NOT NULL AUTO_INCREMENT,
  `aus_id_clase` int(11) DEFAULT NULL,
  `aus_id_estudiante` int(11) DEFAULT NULL,
  `aus_ausencias` decimal(2,1) DEFAULT NULL,
  `aus_justificadas` int(11) DEFAULT NULL,
  PRIMARY KEY (`aus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_boletin`
--

DROP TABLE IF EXISTS `academico_boletin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_boletin` (
  `bol_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bol_carga` int(10) unsigned DEFAULT NULL,
  `bol_estudiante` int(10) unsigned DEFAULT NULL,
  `bol_periodo` int(10) unsigned DEFAULT NULL,
  `bol_nota` double(5,2) DEFAULT NULL,
  `bol_tipo` int(10) unsigned DEFAULT NULL,
  `bol_observaciones` longtext DEFAULT NULL,
  `bol_observaciones_boletin` longtext DEFAULT NULL,
  `bol_actualizaciones` int(10) unsigned DEFAULT NULL,
  `bol_fecha_registro` datetime DEFAULT NULL,
  `bol_ultima_actualizacion` datetime DEFAULT NULL,
  `bol_nota_anterior` double(5,2) DEFAULT NULL COMMENT 'Cuando es recuperacion de periodos',
  `bol_nota_indicadores` double(5,2) DEFAULT NULL,
  `bol_porcentaje` char(10) DEFAULT NULL COMMENT 'Saber con qué porcentaje fue la definitiva',
  `bol_historial_actualizacion` longtext DEFAULT NULL,
  PRIMARY KEY (`bol_id`),
  KEY `Index_ordinarios_2` (`bol_carga`,`bol_estudiante`,`bol_periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_calificaciones`
--

DROP TABLE IF EXISTS `academico_calificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_calificaciones` (
  `cal_id` int(11) NOT NULL AUTO_INCREMENT,
  `cal_id_actividad` int(11) DEFAULT NULL,
  `cal_id_estudiante` int(11) DEFAULT NULL,
  `cal_nota` double(5,2) DEFAULT NULL,
  `cal_observaciones` longtext DEFAULT NULL,
  `cal_fecha_registrada` datetime DEFAULT NULL,
  `cal_fecha_modificada` datetime DEFAULT NULL COMMENT 'Ultima vez que modifico',
  `cal_cantidad_modificaciones` int(10) unsigned DEFAULT NULL,
  `cal_nota_anterior` double(5,2) DEFAULT NULL,
  `cal_tipo` int(10) unsigned DEFAULT NULL COMMENT 'Si fue modificada normal o por recuperacion',
  PRIMARY KEY (`cal_id`),
  KEY `Index_ordinarios_3` (`cal_id_actividad`,`cal_id_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_cargas`
--

DROP TABLE IF EXISTS `academico_cargas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_cargas` (
  `car_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `car_docente` int(10) unsigned DEFAULT NULL,
  `car_curso` int(10) unsigned DEFAULT NULL,
  `car_grupo` int(10) unsigned DEFAULT NULL,
  `car_materia` int(10) unsigned DEFAULT NULL,
  `car_periodo` int(10) unsigned DEFAULT NULL,
  `car_activa` int(10) unsigned DEFAULT NULL,
  `car_permiso1` int(10) unsigned DEFAULT NULL COMMENT 'generar informes',
  `car_director_grupo` int(10) unsigned DEFAULT NULL COMMENT '1=es director del grupo 0=no es director del grupo',
  `car_ih` int(10) unsigned DEFAULT NULL COMMENT 'Intensidad horaria',
  `car_fecha_creada` datetime DEFAULT NULL,
  `car_responsable` int(10) unsigned DEFAULT NULL,
  `car_configuracion` int(10) unsigned DEFAULT NULL,
  `car_valor_indicador` int(10) unsigned DEFAULT NULL COMMENT '1=Manual 0=Auto',
  `car_posicion_docente` char(5) DEFAULT NULL,
  `car_primer_acceso_docente` datetime DEFAULT NULL,
  `car_ultimo_acceso_docente` datetime DEFAULT NULL,
  `car_permiso2` int(10) unsigned DEFAULT NULL COMMENT 'Hacer cambios en otros periodos',
  `car_maximos_indicadores` int(10) unsigned DEFAULT NULL COMMENT 'Indicadores maximos que podra crear',
  `car_maximas_calificaciones` int(10) unsigned DEFAULT NULL COMMENT 'Calificaciones o actividades maximas que podra crear',
  `car_fecha_generar_informe_auto` date DEFAULT NULL,
  `car_fecha_automatica` int(10) unsigned DEFAULT NULL COMMENT 'Que guarde la fecha automatica de hoy en calificaciones',
  `car_evidencia` int(10) unsigned DEFAULT NULL,
  `car_saberes_indicador` int(10) unsigned DEFAULT NULL,
  `car_inicio` date DEFAULT NULL,
  `car_fin` date DEFAULT NULL,
  `car_indicador_automatico` int(10) unsigned DEFAULT NULL COMMENT 'No manejan indicadores. Que guarde automatico el definitivo del 100',
  `car_observaciones_boletin` int(10) unsigned DEFAULT NULL,
  `car_tematica` int(10) unsigned DEFAULT NULL,
  `car_curso_extension` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`car_id`),
  KEY `Index_ordinarios_5` (`car_docente`,`car_curso`,`car_grupo`,`car_materia`,`car_periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_cargas`
--

LOCK TABLES `academico_cargas` WRITE;
/*!40000 ALTER TABLE `academico_cargas` DISABLE KEYS */;
INSERT INTO `academico_cargas` VALUES (1,3,1,1,1,1,1,1,3,2,'0000-00-00 00:00:00',2,0,0,1,NULL,NULL,0,10,100,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `academico_cargas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academico_cargas_acceso`
--

DROP TABLE IF EXISTS `academico_cargas_acceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_cargas_acceso` (
  `carpa_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carpa_id_carga` int(10) unsigned DEFAULT NULL,
  `carpa_id_estudiante` int(10) unsigned DEFAULT NULL,
  `carpa_primer_acceso` datetime DEFAULT NULL,
  `carpa_ultimo_acceso` datetime DEFAULT NULL,
  `carpa_cantidad` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`carpa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_cargas_estudiantes`
--

DROP TABLE IF EXISTS `academico_cargas_estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_cargas_estudiantes` (
  `carpest_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carpest_carga` int(10) unsigned DEFAULT NULL,
  `carpest_estudiante` int(10) unsigned DEFAULT NULL,
  `carpest_estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`carpest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_categorias_notas`
--

DROP TABLE IF EXISTS `academico_categorias_notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_categorias_notas` (
  `catn_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catn_nombre` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`catn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_categorias_notas`
--

LOCK TABLES `academico_categorias_notas` WRITE;
/*!40000 ALTER TABLE `academico_categorias_notas` DISABLE KEYS */;
INSERT INTO `academico_categorias_notas` VALUES (1,'Desempeños (Bajo a Superior)'),(2,'Letras (D a E)'),(3,'Numerica de 0 a 100'),(4,'Caritas (Llorando - Contento)'),(5,'bachiller '),(6,'Juan Pérez ');
/*!40000 ALTER TABLE `academico_categorias_notas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academico_chat_grupal`
--

DROP TABLE IF EXISTS `academico_chat_grupal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_chat_grupal` (
  `chatg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chatg_emisor` int(10) unsigned DEFAULT NULL,
  `chatg_carga` int(10) unsigned DEFAULT NULL,
  `chatg_fecha` datetime DEFAULT NULL,
  `chatg_mensaje` longtext DEFAULT NULL,
  PRIMARY KEY (`chatg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_clases`
--

DROP TABLE IF EXISTS `academico_clases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_clases` (
  `cls_id` int(11) NOT NULL AUTO_INCREMENT,
  `cls_tema` varchar(255) DEFAULT NULL,
  `cls_fecha` date DEFAULT NULL,
  `cls_id_carga` int(11) DEFAULT NULL,
  `cls_registrada` int(11) DEFAULT NULL,
  `cls_fecha_creacion` datetime DEFAULT NULL,
  `cls_fecha_registro` datetime DEFAULT NULL,
  `cls_fecha_modificacion` datetime DEFAULT NULL,
  `cls_estado` int(11) DEFAULT NULL,
  `cls_periodo` int(10) unsigned DEFAULT NULL,
  `cls_archivo` varchar(255) DEFAULT NULL COMMENT 'Algun archivo o presentacion',
  `cls_video` varchar(45) DEFAULT NULL COMMENT 'Video de youtube sobre el tema o la clase grabada',
  `cls_compartir` int(10) unsigned DEFAULT NULL,
  `cls_video_url` longtext DEFAULT NULL,
  `cls_descripcion` longtext DEFAULT NULL,
  `cls_archivo2` varchar(255) DEFAULT NULL,
  `cls_archivo3` varchar(255) DEFAULT NULL,
  `cls_nombre_archivo1` varchar(255) DEFAULT NULL,
  `cls_nombre_archivo2` varchar(255) DEFAULT NULL,
  `cls_nombre_archivo3` varchar(255) DEFAULT NULL,
  `cls_disponible` int(10) unsigned DEFAULT NULL COMMENT 'Disponible para ser vista por los estudiantes.',
  `cls_meeting` varchar(45) DEFAULT NULL,
  `cls_clave_docente` varchar(45) DEFAULT NULL,
  `cls_clave_estudiante` varchar(45) DEFAULT NULL,
  `cls_peso1` varchar(45) DEFAULT NULL,
  `cls_peso2` varchar(45) DEFAULT NULL,
  `cls_peso3` varchar(45) DEFAULT NULL,
  `cls_hipervinculo` longtext DEFAULT NULL,
  `cls_unidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`cls_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_clases_preguntas`
--

DROP TABLE IF EXISTS `academico_clases_preguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_clases_preguntas` (
  `cpp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cpp_usuario` int(10) unsigned DEFAULT NULL,
  `cpp_fecha` datetime DEFAULT NULL,
  `cpp_id_clase` int(10) unsigned DEFAULT NULL,
  `cpp_contenido` longtext DEFAULT NULL,
  `cpp_eliminado` int(11) DEFAULT NULL,
  PRIMARY KEY (`cpp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_cronograma`
--

DROP TABLE IF EXISTS `academico_cronograma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_cronograma` (
  `cro_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cro_tema` varchar(250) DEFAULT NULL,
  `cro_fecha` date DEFAULT NULL,
  `cro_id_carga` int(10) unsigned DEFAULT NULL,
  `cro_recursos` varchar(250) DEFAULT NULL,
  `cro_periodo` int(10) unsigned DEFAULT NULL,
  `cro_color` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_evidencias`
--

DROP TABLE IF EXISTS `academico_evidencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_evidencias` (
  `evid_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `evid_nombre` varchar(255) DEFAULT NULL,
  `evid_valor` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`evid_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_formatos`
--

DROP TABLE IF EXISTS `academico_formatos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_formatos` (
  `form_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_nombre` varchar(100) DEFAULT NULL,
  `form_carga` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_grados`
--

DROP TABLE IF EXISTS `academico_grados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_grados` (
  `gra_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gra_codigo` varchar(45) DEFAULT NULL,
  `gra_nombre` varchar(45) DEFAULT NULL,
  `gra_formato_boletin` int(10) unsigned DEFAULT NULL,
  `gra_valor_matricula` int(10) unsigned DEFAULT NULL,
  `gra_valor_pension` int(10) unsigned DEFAULT NULL,
  `gra_estado` tinyint(1) DEFAULT 1,
  `gra_grado_siguiente` int(10) unsigned DEFAULT NULL,
  `gra_vocal` char(2) DEFAULT NULL COMMENT 'Es para el orden',
  `gra_nivel` int(10) unsigned DEFAULT NULL,
  `gra_grado_anterior` int(10) unsigned DEFAULT NULL,
  `gra_periodos` int(10) unsigned DEFAULT NULL COMMENT 'periodos que maneja este grado',
  `gra_nota_minima` double(5,2) DEFAULT NULL,
  `gra_tipo` varchar(45) NOT NULL DEFAULT 'grupal',
  PRIMARY KEY (`gra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_grados`
--

LOCK TABLES `academico_grados` WRITE;
/*!40000 ALTER TABLE `academico_grados` DISABLE KEYS */;
INSERT INTO `academico_grados` VALUES 
(1,'0','PRIMERO',8,0,0,1,2,NULL,NULL,15,4,NULL,'grupal'),
(2,'0','SEGUNDO',8,0,0,1,3,NULL,NULL,1,4,NULL,'grupal'),
(3,'0','TERCERO',8,0,0,1,4,NULL,NULL,2,4,NULL,'grupal'),
(4,'0','CUARTO',8,0,0,1,5,NULL,NULL,3,4,NULL,'grupal'),
(5,'0','QUINTO',8,0,0,1,6,NULL,NULL,4,4,NULL,'grupal'),
(6,'0','SEXTO',8,0,0,1,7,NULL,NULL,5,4,NULL,'grupal'),
(7,'0','SEPTIMO',8,0,0,1,8,NULL,NULL,6,4,NULL,'grupal'),
(8,'0','OCTAVO',8,0,0,1,9,NULL,NULL,7,4,NULL,'grupal'),
(9,'0','NOVENO',8,0,0,1,10,NULL,NULL,8,4,NULL,'grupal'),
(10,'0','DECIMO',8,0,0,1,11,NULL,NULL,9,4,NULL,'grupal'),
(11,'0','UNDECIMO',8,0,0,1,0,NULL,NULL,10,4,NULL,'grupal'),
(12,'0','PARVULOS',8,0,0,1,13,NULL,NULL,0,4,NULL,'grupal'),
(13,'0','PREJARDIN',8,0,0,1,14,NULL,NULL,12,4,NULL,'grupal'),
(14,'0','JARDIN',8,0,0,1,15,NULL,NULL,13,4,NULL,'grupal'),
(15,'0','TRANSICION',8,0,0,1,1,NULL,NULL,14,4,NULL,'grupal');
/*!40000 ALTER TABLE `academico_grados` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `academico_grados_periodos`
--

DROP TABLE IF EXISTS `academico_grados_periodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_grados_periodos` (
  `gvp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gvp_grado` int(10) unsigned DEFAULT NULL,
  `gvp_periodo` int(10) unsigned DEFAULT NULL,
  `gvp_valor` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`gvp_id`),
  KEY `Index_ordinarios_6` (`gvp_grado`,`gvp_periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_grupos`
--

DROP TABLE IF EXISTS `academico_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_grupos` (
  `gru_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gru_codigo` int(10) unsigned DEFAULT NULL,
  `gru_nombre` varchar(45) DEFAULT NULL,
  `gru_jornada` varchar(45) DEFAULT NULL,
  `gru_horario` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`gru_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_grupos`
--

LOCK TABLES `academico_grupos` WRITE;
/*!40000 ALTER TABLE `academico_grupos` DISABLE KEYS */;
INSERT INTO `academico_grupos` VALUES (1,1267,'A',NULL,NULL),(2,1268,'B',NULL,NULL),(3,1269,'C',NULL,NULL),(4,1270,'Sin grupo',NULL,NULL);
/*!40000 ALTER TABLE `academico_grupos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `academico_horarios`
--

DROP TABLE IF EXISTS `academico_horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_horarios` (
  `hor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hor_id_carga` int(10) unsigned DEFAULT NULL,
  `hor_dia` int(10) unsigned DEFAULT NULL,
  `hor_desde` time DEFAULT NULL,
  `hor_hasta` time DEFAULT NULL,
  `hor_estado` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`hor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_indicadores`
--

DROP TABLE IF EXISTS `academico_indicadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_indicadores` (
  `ind_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ind_nombre` longtext DEFAULT NULL,
  `ind_obligatorio` int(10) unsigned DEFAULT NULL,
  `ind_valor` decimal(5,2) DEFAULT NULL,
  `ind_periodo` int(10) unsigned DEFAULT NULL,
  `ind_carga` int(10) unsigned DEFAULT NULL,
  `ind_fecha_creacion` datetime DEFAULT NULL,
  `ind_fecha_modificacion` datetime DEFAULT NULL,
  `ind_tematica` int(10) unsigned DEFAULT NULL,
  `ind_publico` int(10) unsigned DEFAULT NULL COMMENT 'Puede ser usado por otros docentes.',
  `ind_definitivo` int(10) unsigned DEFAULT NULL COMMENT 'Es el definitivo de 100 que toman los docentes para no llenar indicadores',
  PRIMARY KEY (`ind_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_indicadores_carga`
--

DROP TABLE IF EXISTS `academico_indicadores_carga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_indicadores_carga` (
  `ipc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ipc_carga` int(10) unsigned DEFAULT NULL,
  `ipc_indicador` int(10) unsigned DEFAULT NULL,
  `ipc_valor` decimal(5,2) DEFAULT NULL,
  `ipc_periodo` int(10) unsigned DEFAULT NULL,
  `ipc_creado` int(10) unsigned DEFAULT NULL,
  `ipc_copiado` int(10) unsigned DEFAULT NULL COMMENT 'ID de donde lo copio',
  `ipc_evaluacion` int(10) unsigned DEFAULT NULL COMMENT 'Saberes (Ser, Conocer, Hacer)',
  `ipc_eliminado` int(11) DEFAULT NULL,
  PRIMARY KEY (`ipc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_indicadores_recuperacion`
--

DROP TABLE IF EXISTS `academico_indicadores_recuperacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_indicadores_recuperacion` (
  `rind_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rind_fecha_registro` datetime DEFAULT NULL,
  `rind_estudiante` int(10) unsigned DEFAULT NULL,
  `rind_carga` int(10) unsigned DEFAULT NULL,
  `rind_nota` double(5,2) DEFAULT NULL,
  `rind_indicador` int(10) unsigned DEFAULT NULL,
  `rind_periodo` int(10) unsigned DEFAULT NULL,
  `rind_actualizaciones` int(10) unsigned DEFAULT NULL,
  `rind_ultima_actualizacion` datetime DEFAULT NULL,
  `rind_nota_anterior` double(5,2) DEFAULT NULL,
  `rind_nota_original` double(5,2) DEFAULT NULL,
  `rind_nota_actual` double(5,2) DEFAULT NULL,
  `rind_tipo_ultima_actualizacion` int(10) unsigned DEFAULT NULL COMMENT '1=G. informe. 2=Rec. Indicadores.',
  `rind_valor_indicador_registro` decimal(5,2) DEFAULT NULL,
  `rind_valor_indicador_actualizacion` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`rind_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_intensidad_curso`
--

DROP TABLE IF EXISTS `academico_intensidad_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_intensidad_curso` (
  `ipc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ipc_curso` int(10) unsigned DEFAULT NULL,
  `ipc_materia` int(10) unsigned DEFAULT NULL,
  `ipc_intensidad` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ipc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_materias`
--

DROP TABLE IF EXISTS `academico_materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_materias` (
  `mat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mat_codigo` varchar(45) DEFAULT NULL,
  `mat_nombre` varchar(150) DEFAULT NULL,
  `mat_siglas` varchar(45) DEFAULT NULL,
  `mat_area` int(10) unsigned DEFAULT NULL,
  `mat_oficial` int(10) unsigned DEFAULT NULL COMMENT 'Si la materia NO (0) es oficial entonces no se tendra en cuenta en el promedio.',
  `mat_portada` varchar(255) DEFAULT NULL,
  `mat_valor` varchar(45) DEFAULT NULL COMMENT 'Valor porcentual dentro de cada area',
  PRIMARY KEY (`mat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_materias`
--

LOCK TABLES `academico_materias` WRITE;
/*!40000 ALTER TABLE `academico_materias` DISABLE KEYS */;
INSERT INTO `academico_materias` VALUES (1,'1','MATERIA DE PRUEBA','PRU',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `academico_materias` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `academico_matricula_proceso`
--

DROP TABLE IF EXISTS `academico_matricula_proceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_matricula_proceso` (
  `matp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matp_estudiante` int(10) unsigned DEFAULT NULL,
  `matp_iniciar_proceso` int(10) unsigned DEFAULT NULL COMMENT 'Si puede iniciar o no el proceso.',
  `matp_actualizar_datos` int(10) unsigned DEFAULT NULL,
  `matp_pago` int(10) unsigned DEFAULT NULL,
  `matp_contrato` int(10) unsigned DEFAULT NULL,
  `matp_compromisos` int(10) unsigned DEFAULT NULL COMMENT 'Academico y convivencia',
  `matp_manual` int(10) unsigned DEFAULT NULL,
  `matp_mayores14` int(10) unsigned DEFAULT NULL,
  `matp_adjuntar_firma` int(10) unsigned DEFAULT NULL,
  `matp_firma_adjunta` varchar(255) DEFAULT NULL,
  `matp_recibo_pago` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`matp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_matriculas`
--

DROP TABLE IF EXISTS `academico_matriculas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_matriculas` (
  `mat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mat_matricula` varchar(45) DEFAULT NULL,
  `mat_fecha` datetime DEFAULT NULL,
  `mat_primer_apellido` varchar(45) DEFAULT NULL,
  `mat_segundo_apellido` varchar(45) DEFAULT NULL,
  `mat_nombres` varchar(45) DEFAULT NULL,
  `mat_grado` int(10) unsigned DEFAULT NULL,
  `mat_grupo` int(10) unsigned DEFAULT NULL,
  `mat_genero` int(10) unsigned DEFAULT NULL,
  `mat_fecha_nacimiento` varchar(45) DEFAULT NULL,
  `mat_lugar_nacimiento` varchar(45) DEFAULT NULL,
  `mat_tipo_documento` int(10) unsigned DEFAULT NULL,
  `mat_documento` varchar(45) DEFAULT NULL,
  `mat_lugar_expedicion` varchar(45) DEFAULT NULL,
  `mat_religion` int(10) unsigned DEFAULT NULL,
  `mat_direccion` varchar(45) DEFAULT NULL,
  `mat_barrio` varchar(45) DEFAULT NULL,
  `mat_telefono` varchar(45) DEFAULT NULL,
  `mat_celular` varchar(45) DEFAULT NULL,
  `mat_estrato` int(10) unsigned DEFAULT NULL,
  `mat_foto` varchar(300) DEFAULT NULL,
  `mat_tipo` int(10) unsigned DEFAULT NULL COMMENT 'nuevo, antiguo',
  `mat_estado_matricula` int(10) unsigned DEFAULT 1 COMMENT 'mat, asis, can, no mat., inscripcion',
  `mat_id_usuario` int(10) unsigned DEFAULT NULL,
  `mat_eliminado` int(10) unsigned DEFAULT 0,
  `mat_email` varchar(100) DEFAULT NULL,
  `mat_acudiente` int(10) unsigned DEFAULT NULL,
  `mat_privilegio1` int(10) unsigned DEFAULT 0 COMMENT 'crear noticias',
  `mat_privilegio2` varchar(45) DEFAULT '0' COMMENT 'chat',
  `mat_privilegio3` int(10) unsigned DEFAULT 0 COMMENT 'muro social',
  `mat_uso_sintia` int(10) unsigned DEFAULT NULL,
  `mat_inicio` date DEFAULT NULL,
  `mat_meses` int(10) unsigned DEFAULT NULL,
  `mat_fin` date DEFAULT NULL,
  `mat_folio` varchar(45) DEFAULT NULL,
  `mat_codigo_tesoreria` varchar(45) DEFAULT NULL,
  `mat_valor_matricula` int(10) unsigned DEFAULT NULL,
  `mat_inclusion` int(10) unsigned DEFAULT NULL,
  `mat_promocionado` int(10) unsigned DEFAULT NULL,
  `mat_extranjero` int(10) unsigned DEFAULT NULL,
  `mat_numero_matricula` varchar(45) DEFAULT NULL,
  `mat_compromiso` int(10) unsigned DEFAULT NULL,
  `mat_acudiente2` int(10) unsigned DEFAULT NULL,
  `mat_institucion_procedencia` varchar(255) DEFAULT NULL,
  `mat_estado_agno` int(10) unsigned DEFAULT NULL COMMENT 'perdio o gano el aÃ±o',
  `mat_salon` varchar(45) DEFAULT NULL,
  `mat_notificacion1` int(10) unsigned DEFAULT NULL COMMENT 'Notificaciones de notas y demas por correo.',
  `mat_acudiente_principal` int(10) unsigned DEFAULT NULL,
  `mat_padre` int(10) unsigned DEFAULT NULL,
  `mat_madre` int(10) unsigned DEFAULT NULL,
  `mat_lugar_colegio_procedencia` varchar(255) DEFAULT NULL,
  `mat_razon_ingreso_plantel` longtext DEFAULT NULL,
  `mat_motivo_retiro_anterior` longtext DEFAULT NULL,
  `mat_ciudad_actual` varchar(255) DEFAULT NULL,
  `mat_solicitud_inscripcion` int(10) unsigned DEFAULT 0,
  `mat_tipo_sangre` varchar(45) DEFAULT NULL,
  `mat_con_quien_vive` int(10) unsigned DEFAULT NULL,
  `mat_quien_otro` varchar(255) DEFAULT NULL,
  `mat_iniciar_proceso` int(10) unsigned DEFAULT NULL,
  `mat_actualizar_datos` int(10) unsigned DEFAULT NULL,
  `mat_pago_matricula` int(10) unsigned DEFAULT NULL,
  `mat_contrato` int(10) unsigned DEFAULT NULL,
  `mat_compromiso_academico` int(10) unsigned DEFAULT NULL,
  `mat_manual` int(10) unsigned DEFAULT NULL,
  `mat_mayores14` int(10) unsigned DEFAULT NULL,
  `mat_hoja_firma` int(10) unsigned DEFAULT NULL,
  `mat_soporte_pago` varchar(255) DEFAULT NULL,
  `mat_firma_adjunta` varchar(255) DEFAULT NULL,
  `mat_compromiso_convivencia` int(10) unsigned DEFAULT NULL,
  `mat_compromiso_convivencia_opcion` int(10) unsigned DEFAULT NULL,
  `mat_pagare` int(10) unsigned DEFAULT NULL,
  `mat_modalidad_estudio` int(10) unsigned DEFAULT NULL,
  `mat_informe_parcial` int(10) unsigned DEFAULT 0 COMMENT 'Contar las veces que vio el informe parcial',
  `mat_informe_parcial_fecha` datetime DEFAULT NULL COMMENT 'La ultima vez que lo vio',
  `mat_eps` varchar(45) DEFAULT NULL,
  `mat_celular2` varchar(45) DEFAULT NULL,
  `mat_ciudad_residencia` varchar(10) DEFAULT NULL,
  `mat_nombre2` varchar(45) DEFAULT NULL,
  `mat_ciudad_recidencia` varchar(100) DEFAULT NULL,
  `mat_tipo_matricula` varchar(45) NOT NULL DEFAULT 'grupal' COMMENT 'Se requiere para definir si el estudiante podrÃ¡ estar en varios cursos a la vez',
  PRIMARY KEY (`mat_id`),
  KEY `Index_ordinarios_1` (`mat_grado`,`mat_grupo`,`mat_estado_matricula`,`mat_id_usuario`,`mat_eliminado`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_matriculas`
--

LOCK TABLES `academico_matriculas` WRITE;
/*!40000 ALTER TABLE `academico_matriculas` DISABLE KEYS */;
INSERT INTO `academico_matriculas` VALUES (1,'00001','0000-00-00 00:00:00','PRUEBA','DE','ESTUDIANTE',1,1,126,'1993-10-21','1',108,'0000000000','1',111,'Cra 00 #00-00','B. Prueba',NULL,NULL,116,NULL,129,1,5,0,'',4,0,'0',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'grupal');
/*!40000 ALTER TABLE `academico_matriculas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `academico_matriculas_cursos`
--

DROP TABLE IF EXISTS `academico_matriculas_cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_matriculas_cursos` (
  `matcur_id` varchar(45) NOT NULL,
  `matcur_id_matricula` int(10) unsigned NOT NULL,
  `matcur_id_curso` int(10) unsigned NOT NULL,
  PRIMARY KEY (`matcur_id`),
  KEY `academico_matriculas_cursos_FK` (`matcur_id_curso`),
  CONSTRAINT `academico_matriculas_cursos_FK` FOREIGN KEY (`matcur_id_curso`) REFERENCES `academico_grados` (`gra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='GuardarÃ¡ los cursos adicionales de cada estudiante.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_matriculas_documentos`
--

DROP TABLE IF EXISTS `academico_matriculas_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_matriculas_documentos` (
  `matd_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matd_matricula` int(10) unsigned DEFAULT NULL,
  `matd_pazysalvo` varchar(255) DEFAULT NULL,
  `matd_observador` varchar(255) DEFAULT NULL,
  `matd_eps` varchar(255) DEFAULT NULL,
  `matd_recomendacion` varchar(255) DEFAULT NULL,
  `matd_vacunas` varchar(255) DEFAULT NULL,
  `matd_boletines_actuales` varchar(255) DEFAULT NULL,
  `matd_documento_identidad` varchar(255) DEFAULT NULL,
  `matd_certificados` varchar(255) DEFAULT NULL,
  `matd_fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `matd_fecha_eliminados` datetime DEFAULT NULL,
  `matd_usuario_elimados` int(10) unsigned DEFAULT NULL,
  `matd_registro_civil` varchar(255) DEFAULT NULL,
  `matd_carta_laboral` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`matd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_matriculas_referencias`
--

DROP TABLE IF EXISTS `academico_matriculas_referencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_matriculas_referencias` (
  `matref_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matref_estudiante` int(10) unsigned DEFAULT NULL,
  `matref_institucion` varchar(255) DEFAULT NULL,
  `matref_grado` varchar(45) DEFAULT NULL,
  `matref_agno` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`matref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_matriculas_retiradas`
--

DROP TABLE IF EXISTS `academico_matriculas_retiradas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_matriculas_retiradas` (
  `matret_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matret_estudiante` int(10) unsigned DEFAULT NULL,
  `matret_fecha` date DEFAULT NULL,
  `matret_motivo` longtext DEFAULT NULL,
  `matret_responsable` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`matret_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_monitoreo`
--

DROP TABLE IF EXISTS `academico_monitoreo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_monitoreo` (
  `moni_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moni_fecha` datetime DEFAULT NULL,
  `moni_evaluador` int(10) unsigned DEFAULT NULL,
  `moni_evaluado` int(10) unsigned DEFAULT NULL,
  `moni_id_formato` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`moni_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_nivelaciones`
--

DROP TABLE IF EXISTS `academico_nivelaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_nivelaciones` (
  `niv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `niv_id_asg` int(10) unsigned DEFAULT NULL,
  `niv_cod_estudiante` int(10) unsigned DEFAULT NULL,
  `niv_definitiva` double(2,1) DEFAULT NULL,
  `niv_fecha` datetime DEFAULT NULL,
  `niv_acta` int(10) unsigned DEFAULT NULL,
  `niv_fecha_nivelacion` date DEFAULT NULL,
  PRIMARY KEY (`niv_id`),
  KEY `Index_ordinarios_7` (`niv_id_asg`,`niv_cod_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_niveles`
--

DROP TABLE IF EXISTS `academico_niveles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_niveles` (
  `nive_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nive_nombre` varchar(255) DEFAULT NULL,
  `nive_nombre2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nive_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_notas_tipos`
--

DROP TABLE IF EXISTS `academico_notas_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_notas_tipos` (
  `notip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notip_nombre` varchar(45) DEFAULT NULL,
  `notip_desde` double(5,2) DEFAULT NULL,
  `notip_hasta` double(5,2) DEFAULT NULL,
  `notip_categoria` int(10) unsigned DEFAULT NULL,
  `notip_nombre2` varchar(45) DEFAULT NULL,
  `notip_imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`notip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academico_notas_tipos`
--

LOCK TABLES `academico_notas_tipos` WRITE;
/*!40000 ALTER TABLE `academico_notas_tipos` DISABLE KEYS */;
INSERT INTO `academico_notas_tipos` VALUES (1,'Bajo',1.00,3.49,1,NULL,'bajo.png'),(2,'Basico',3.50,3.99,1,NULL,'bas.png'),(3,'Alto',4.00,4.59,1,NULL,'alto.png'),(4,'Superior',4.60,5.00,1,NULL,'sup.png');
/*!40000 ALTER TABLE `academico_notas_tipos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Table structure for table `academico_pclase`
--

DROP TABLE IF EXISTS `academico_pclase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_pclase` (
  `pc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pc_plan` varchar(300) DEFAULT NULL,
  `pc_id_carga` int(10) unsigned DEFAULT NULL,
  `pc_periodo` int(10) unsigned DEFAULT NULL,
  `pc_fecha_subido` datetime DEFAULT NULL,
  PRIMARY KEY (`pc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_recuperaciones_notas`
--

DROP TABLE IF EXISTS `academico_recuperaciones_notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_recuperaciones_notas` (
  `rec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rec_cod_estudiante` int(10) unsigned DEFAULT NULL,
  `rec_id_nota` int(10) unsigned DEFAULT NULL,
  `rec_nota` decimal(2,1) DEFAULT NULL,
  `rec_fecha` datetime DEFAULT NULL,
  `rec_nota_anterior` decimal(2,1) DEFAULT NULL,
  PRIMARY KEY (`rec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academico_unidades`
--

DROP TABLE IF EXISTS `academico_unidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academico_unidades` (
  `uni_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uni_nombre` varchar(255) DEFAULT NULL,
  `uni_id_carga` int(10) unsigned DEFAULT NULL,
  `uni_periodo` int(10) unsigned DEFAULT NULL,
  `uni_eliminado` int(10) unsigned DEFAULT NULL,
  `uni_descripcion` longtext DEFAULT NULL,
  PRIMARY KEY (`uni_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aulas`
--

DROP TABLE IF EXISTS `aulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas` (
  `aul_id` int(11) NOT NULL AUTO_INCREMENT,
  `aul_nombre` varchar(100) DEFAULT NULL,
  `aul_descripcion` longtext DEFAULT NULL,
  `aul_disponible` int(11) DEFAULT NULL,
  PRIMARY KEY (`aul_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disciplina_categorias`
--

DROP TABLE IF EXISTS `disciplina_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disciplina_categorias` (
  `dcat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dcat_nombre` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`dcat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disciplina_faltas`
--

DROP TABLE IF EXISTS `disciplina_faltas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disciplina_faltas` (
  `dfal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dfal_nombre` longtext DEFAULT NULL,
  `dfal_id_categoria` int(10) unsigned DEFAULT NULL,
  `dfal_codigo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`dfal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disciplina_matricula_condicional`
--

DROP TABLE IF EXISTS `disciplina_matricula_condicional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disciplina_matricula_condicional` (
  `cond_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cond_fecha` date NOT NULL,
  `cond_estudiante` int(10) unsigned NOT NULL,
  `cond_observacion` longtext DEFAULT NULL,
  `cond_usuario` int(10) unsigned NOT NULL,
  `cond_estado` int(11) DEFAULT 1 COMMENT '1=acativa 0=cancelada',
  PRIMARY KEY (`cond_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disciplina_reportes`
--

DROP TABLE IF EXISTS `disciplina_reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disciplina_reportes` (
  `dr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dr_fecha` datetime DEFAULT NULL,
  `dr_estudiante` int(10) unsigned DEFAULT NULL,
  `dr_falta` longtext DEFAULT NULL,
  `dr_tipo` int(10) unsigned DEFAULT NULL,
  `dr_usuario` int(10) unsigned DEFAULT NULL,
  `dr_aprobacion_estudiante` int(10) unsigned DEFAULT NULL,
  `dr_aprobacion_estudiante_fecha` datetime DEFAULT NULL,
  `dr_aprobacion_acudiente` int(10) unsigned DEFAULT NULL,
  `dr_aprobacion_acudiente_fecha` datetime DEFAULT NULL,
  `dr_observaciones` longtext DEFAULT NULL,
  `dr_comentario` longtext DEFAULT NULL,
  `dr_eliminado` int(11) DEFAULT NULL,
  PRIMARY KEY (`dr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disiplina_nota`
--

DROP TABLE IF EXISTS `disiplina_nota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disiplina_nota` (
  `dn_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dn_cod_estudiante` int(10) unsigned DEFAULT NULL,
  `dn_id_carga` int(10) unsigned DEFAULT NULL,
  `dn_observacion` longtext DEFAULT NULL,
  `dn_nota` double(5,2) DEFAULT NULL,
  `dn_fecha` date DEFAULT NULL,
  `dn_periodo` int(10) unsigned DEFAULT NULL,
  `dn_aspecto_academico` longtext DEFAULT NULL,
  `dn_aspecto_convivencial` longtext DEFAULT NULL,
  `dn_fecha_aspecto` timestamp NOT NULL DEFAULT current_timestamp(),
  `dn_ultima_lectura` datetime DEFAULT NULL,
  `dn_aprobado` int(10) unsigned DEFAULT 0 COMMENT 'Firma de estar de acuerdo.',
  `dn_fecha_aprobado` datetime DEFAULT NULL,
  `dn_eliminado` int(11) DEFAULT NULL,
  PRIMARY KEY (`dn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `finanzas_cobros_masivos`
--

DROP TABLE IF EXISTS `finanzas_cobros_masivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `finanzas_cobros_masivos` (
  `mas_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mas_nombre` varchar(255) DEFAULT NULL,
  `mas_valor` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`mas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `finanzas_cuentas`
--

DROP TABLE IF EXISTS `finanzas_cuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `finanzas_cuentas` (
  `fcu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fcu_fecha` date DEFAULT NULL,
  `fcu_detalle` varchar(255) DEFAULT NULL,
  `fcu_valor` varchar(255) DEFAULT NULL,
  `fcu_tipo` varchar(45) DEFAULT '1' COMMENT 'Ingreso, Egreso, CXC, CXP',
  `fcu_observaciones` longtext DEFAULT NULL,
  `fcu_usuario` varchar(45) DEFAULT NULL,
  `fcu_anulado` int(10) unsigned DEFAULT 0 COMMENT '1=si 0=no',
  `fcu_forma_pago` int(10) unsigned DEFAULT NULL,
  `fcu_cerrado` int(10) unsigned DEFAULT NULL,
  `fcu_fecha_cerrado` datetime DEFAULT NULL,
  `fcu_cerrado_usuario` int(10) unsigned DEFAULT NULL,
  `fcu_consecutivo` varchar(45) DEFAULT NULL,
  `fcu_valor_letras` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`fcu_id`),
  KEY `Index_ordinarios_8` (`fcu_tipo`,`fcu_usuario`,`fcu_forma_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matriculas_aspectos`
--

DROP TABLE IF EXISTS `matriculas_aspectos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matriculas_aspectos` (
  `mata_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mata_estudiante` int(10) unsigned DEFAULT NULL,
  `mata_aspecto_academico` longtext DEFAULT NULL,
  `mata_aspecto_disciplinario` longtext DEFAULT NULL,
  `mata_usuario` int(10) unsigned DEFAULT NULL,
  `mata_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mata_fecha_evento` date DEFAULT NULL,
  `mata_aspectos_positivos` longtext DEFAULT NULL,
  `mata_aspectos_mejorar` longtext DEFAULT NULL,
  `mata_tratamiento` longtext DEFAULT NULL,
  `mata_descripcion` longtext DEFAULT NULL,
  `mata_periodo` int(10) unsigned DEFAULT NULL,
  `mata_aprobacion_acudiente` int(10) unsigned DEFAULT 0,
  `mata_aprobacion_acudiente_fecha` datetime DEFAULT NULL,
  `mata_eliminado` int(11) DEFAULT NULL,
  PRIMARY KEY (`mata_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `uss_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uss_usuario` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `uss_clave` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `uss_tipo` int(10) unsigned DEFAULT NULL,
  `uss_nombre` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `uss_estado` int(10) unsigned DEFAULT NULL COMMENT 'Sesion abierta o cerrada. 1 o 0',
  `uss_foto` varchar(300) DEFAULT NULL,
  `uss_portada` varchar(300) DEFAULT NULL,
  `uss_idioma` int(10) unsigned DEFAULT NULL,
  `uss_tema` varchar(45) DEFAULT NULL,
  `uss_perfil` longtext DEFAULT NULL,
  `uss_ocupacion` varchar(300) DEFAULT NULL,
  `uss_email` varchar(45) DEFAULT NULL,
  `uss_fecha_nacimiento` date DEFAULT NULL,
  `uss_permiso1` int(10) unsigned DEFAULT NULL COMMENT 'Datos personales',
  `uss_celular` varchar(20) DEFAULT NULL,
  `uss_genero` int(10) unsigned DEFAULT NULL,
  `uss_ultimo_ingreso` datetime DEFAULT NULL,
  `uss_ultima_salida` datetime DEFAULT NULL,
  `uss_telefono` varchar(45) DEFAULT NULL,
  `uss_bloqueado` int(10) unsigned DEFAULT NULL COMMENT '0 = NO - 1 = SI',
  `uss_fecha_registro` datetime DEFAULT NULL,
  `uss_responsable_registro` int(10) unsigned DEFAULT NULL,
  `uss_lugar_expedicion` varchar(255) DEFAULT NULL,
  `uss_direccion` varchar(255) DEFAULT NULL,
  `uss_estado_civil` int(10) unsigned DEFAULT NULL,
  `uss_preguntar_animo` int(10) unsigned DEFAULT NULL,
  `uss_mostrar_mensajes` int(10) unsigned DEFAULT NULL,
  `uss_profesion` int(10) unsigned DEFAULT NULL,
  `uss_estado_laboral` int(10) unsigned DEFAULT NULL,
  `uss_nivel_academico` int(10) unsigned DEFAULT NULL,
  `uss_religion` varchar(255) DEFAULT NULL,
  `uss_tiene_hijos` int(10) unsigned DEFAULT NULL,
  `uss_numero_hijos` int(10) unsigned DEFAULT NULL,
  `uss_lugar_nacimiento` int(10) unsigned DEFAULT NULL,
  `uss_sitio_web_negocio` longtext DEFAULT NULL,
  `uss_tipo_negocio` int(10) unsigned DEFAULT NULL,
  `uss_estrato` int(10) unsigned DEFAULT NULL,
  `uss_tipo_vivienda` int(10) unsigned DEFAULT NULL,
  `uss_medio_transporte` int(10) unsigned DEFAULT NULL,
  `uss_tema_sidebar` varchar(45) DEFAULT NULL,
  `uss_tema_header` varchar(45) DEFAULT NULL,
  `uss_tema_logo` varchar(45) DEFAULT NULL,
  `uss_tipo_menu` varchar(45) DEFAULT NULL,
  `uss_notificacion` int(10) unsigned DEFAULT NULL,
  `uss_mostrar_edad` int(10) unsigned DEFAULT NULL,
  `uss_ultima_actualizacion` datetime DEFAULT NULL,
  `uss_version1_menu` int(10) unsigned DEFAULT NULL,
  `uss_solicitar_datos` int(10) unsigned DEFAULT NULL,
  `uss_institucion` varchar(255) DEFAULT NULL,
  `uss_institucion_municipio` int(10) unsigned DEFAULT NULL,
  `uss_intentos_fallidos` int(10) unsigned DEFAULT 1,
  `uss_parentezco` varchar(255) DEFAULT NULL,
  `uss_tipo_documento` int(10) unsigned DEFAULT NULL,
  `uss_empresa_labor` varchar(255) DEFAULT NULL,
  `uss_firma` varchar(255) DEFAULT NULL,
  `uss_apellido1` varchar(45) DEFAULT NULL,
  `uss_apellido2` varchar(45) DEFAULT NULL,
  `uss_nombre2` varchar(45) DEFAULT NULL,
  `uss_documento` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`uss_id`),
  KEY `Index_ordinarios_4` (`uss_usuario`,`uss_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES 
(1,'sintia',SHA1('sintia2014$'),1,'ADMINISTRACIÓN PLATAFORMA SINTIA',0,'mobiliar_dev_1_img_639e74c3624ac.png','default.png',1,'orange','','Administrador','soporte@plataformasintia.com','2022-12-06',1298,'(313) 591-2073',126,'2023-01-26 05:56:36','2023-01-26 05:55:46','853755',0,NULL,NULL,'','calle 44 # 77 71',156,0,NULL,21,165,151,'111',NULL,0,150,'https://plataformasintia.com',169,117,177,182,'white-sidebar-color','header-white','logo-indigo',NULL,0,0,'2022-12-17 21:02:42',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(2,'pruebaDT',SHA1('pruebaDT'),5,'USUARIO',0,'mobiliar_dev_1_img_639e74c3624ac.png','default.png',1,'orange','','DIRECTIVO',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,156,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'DIRECTIVO',NULL,'DE PRUEBA',NULL),
(3,'pruebaDC',SHA1('pruebaDC'),2,'USUARIO',0,'mobiliar_dev_1_img_639e74c3624ac.png','default.png',1,'orange','','DOCENTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,156,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'DOCENTES',NULL,'DE PRUEBA',NULL),
(4,'pruebaAC',SHA1('pruebaAC'),3,'USUARIO',0,'mobiliar_dev_1_img_639e74c3624ac.png','default.png',1,'orange','','ACUDIENTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,156,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'ACUDIENTES',NULL,'DE PRUEBA',NULL),
(5,'pruebaES',SHA1('pruebaES'),4,'USUARIO',0,'mobiliar_dev_1_img_639e74c3624ac.png','default.png',1,'orange','','ESTUDIANTE',NULL,NULL,0,NULL,126,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,156,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'ESTUDIANTES',NULL,'DE PRUEBA',NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios_paginas`
--

DROP TABLE IF EXISTS `usuarios_paginas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_paginas` (
  `usp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usp_id_usuario` int(10) unsigned DEFAULT NULL,
  `usp_id_pagina` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`usp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Paginas a las que NO tiene permiso el Usuario.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_por_estudiantes`
--

DROP TABLE IF EXISTS `usuarios_por_estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_por_estudiantes` (
  `upe_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upe_id_usuario` int(10) unsigned DEFAULT NULL,
  `upe_id_estudiante` int(10) unsigned DEFAULT NULL,
  `upe_parentezco` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`upe_id`),
  KEY `Index_ordinario_16` (`upe_id_usuario`,`upe_id_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

COMMIT;

--
-- Dumping routines for database 'mobiliar_dev_2022'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-01-26 12:57:05