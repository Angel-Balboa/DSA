-- MySQL dump 10.19  Distrib 10.3.34-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dsa
-- ------------------------------------------------------
-- Server version	10.3.34-MariaDB-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actividad`
--

DROP TABLE IF EXISTS `actividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actividad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `empresa_receptora` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `horas` tinyint(3) unsigned DEFAULT 0,
  `evidencia` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `planeacion_academica` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_planeacionAcademica_gestionAcademica_planeacionAcademica` (`planeacion_academica`),
  CONSTRAINT `fk_planeacionAcademica_gestionAcademica_planeacionAcademica` FOREIGN KEY (`planeacion_academica`) REFERENCES `planeacion_academica` (`id`),
  CONSTRAINT `chk_tipo_actividad` CHECK (`tipo` in ('GESTION','CAPACITACION','VINCULACION','PROMOCION'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividad`
--

LOCK TABLES `actividad` WRITE;
/*!40000 ALTER TABLE `actividad` DISABLE KEYS */;
/*!40000 ALTER TABLE `actividad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actividad_investigacion`
--

DROP TABLE IF EXISTS `actividad_investigacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actividad_investigacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actividad` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `avance_actual` tinyint(3) unsigned DEFAULT 0,
  `avance_esperado` tinyint(3) unsigned DEFAULT 0,
  `fecha_termino` date NOT NULL,
  `planeacion_academica` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `fk_planeacionAcademica_activInv_planeacionAcademica` (`planeacion_academica`),
  CONSTRAINT `fk_planeacionAcademica_activInv_planeacionAcademica` FOREIGN KEY (`planeacion_academica`) REFERENCES `planeacion_academica` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividad_investigacion`
--

LOCK TABLES `actividad_investigacion` WRITE;
/*!40000 ALTER TABLE `actividad_investigacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `actividad_investigacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carga_academica`
--

DROP TABLE IF EXISTS `carga_academica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carga_academica` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plan_estudios` int(10) unsigned NOT NULL,
  `periodo` tinyint(3) unsigned DEFAULT 1,
  `fecha_inicio` date NOT NULL,
  `fecha_final` date NOT NULL,
  `anio` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_planPeriodoAnio_cargaAcademica` (`plan_estudios`,`periodo`,`anio`),
  CONSTRAINT `fk_planEstudios_planDeEstudio_cargaAcademica` FOREIGN KEY (`plan_estudios`) REFERENCES `plan_de_estudio` (`id`),
  CONSTRAINT `check_periodo_cargaAcademica` CHECK (`periodo` in (1,2,3))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carga_academica`
--

LOCK TABLES `carga_academica` WRITE;
/*!40000 ALTER TABLE `carga_academica` DISABLE KEYS */;
INSERT INTO `carga_academica` VALUES (1,5,1,'2022-01-03','2022-04-21',2022),(2,5,1,'2019-01-07','2019-04-18',2019),(3,5,1,'2020-01-06','2020-04-23',2020),(6,5,3,'2022-09-05','2022-12-22',2022),(7,5,1,'2023-01-02','2023-04-20',2023),(8,5,2,'2022-05-02','2022-08-24',2022);
/*!40000 ALTER TABLE `carga_academica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrera`
--

DROP TABLE IF EXISTS `carrera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrera` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `clave` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nivel` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'Ing',
  `director` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unink_director` (`director`),
  UNIQUE KEY `unik_clave_carrera` (`clave`),
  CONSTRAINT `fk_director_usuario_carrera` FOREIGN KEY (`director`) REFERENCES `usuario` (`id`),
  CONSTRAINT `check_nivel_carrera` CHECK (`nivel` in ('Ing','Lic','M.I.'))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrera`
--

LOCK TABLES `carrera` WRITE;
/*!40000 ALTER TABLE `carrera` DISABLE KEYS */;
INSERT INTO `carrera` VALUES (1,'Licenciatura en Administración y Gestión de Pequeñas y Medianas Empresas','LAyGPyMe','Lic',2),(2,'Licenciatura en Comercio Internacional y Aduanas','LCIA','Lic',3),(3,'Ingeniería en Tecnologías de la Información','ITI','Ing',4),(4,'Ingeniería en Mecatrónica','IM','Ing',5),(5,'Ingeniería en Tecnologías de Manufactura','ITM','Ing',6),(6,'Ingeniería en Sistemas Automotrices','ISA','Ing',7),(7,'Maestría en Ingeniería','MI','M.I.',8);
/*!40000 ALTER TABLE `carrera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coautor`
--

DROP TABLE IF EXISTS `coautor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coautor` (
  `profesor` int(10) unsigned NOT NULL,
  `producto` int(10) unsigned NOT NULL,
  `posicion` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`profesor`,`producto`),
  KEY `fk_producto_autores_productoCientifico` (`producto`),
  CONSTRAINT `fk_producto_autores_productoCientifico` FOREIGN KEY (`producto`) REFERENCES `producto_cientifico` (`id`),
  CONSTRAINT `fk_profesor_autores_profesor` FOREIGN KEY (`profesor`) REFERENCES `profesor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coautor`
--

LOCK TABLES `coautor` WRITE;
/*!40000 ALTER TABLE `coautor` DISABLE KEYS */;
/*!40000 ALTER TABLE `coautor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilidad`
--

DROP TABLE IF EXISTS `disponibilidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disponibilidad` (
  `dia` tinyint(4) NOT NULL,
  `hora` tinyint(4) NOT NULL,
  `profesor` int(10) unsigned NOT NULL,
  UNIQUE KEY `unik_dia_hora_disponibilidad` (`dia`,`hora`,`profesor`),
  KEY `fk_profesor_profesor_disponibilidad` (`profesor`),
  CONSTRAINT `fk_profesor_profesor_disponibilidad` FOREIGN KEY (`profesor`) REFERENCES `profesor` (`id`),
  CONSTRAINT `check_dia_disponibilidad` CHECK (`dia` in (0,1,2,3,4,5)),
  CONSTRAINT `check_hora_disponibilidad` CHECK (`hora` in (0,1,2,3,4,5,6,7,8,9,10,11,12,13))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilidad`
--

LOCK TABLES `disponibilidad` WRITE;
/*!40000 ALTER TABLE `disponibilidad` DISABLE KEYS */;
/*!40000 ALTER TABLE `disponibilidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupo`
--

DROP TABLE IF EXISTS `grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grupo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `turno` tinyint(3) unsigned DEFAULT 1,
  `cuatrimestre` tinyint(3) unsigned DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `finalizado` tinyint(1) DEFAULT 0,
  `carga_academica` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_clave_cargaAcademica` (`clave`,`carga_academica`),
  KEY `fk_cargaAcademica_grupo_cargaAcademica` (`carga_academica`),
  CONSTRAINT `fk_cargaAcademica_grupo_cargaAcademica` FOREIGN KEY (`carga_academica`) REFERENCES `carga_academica` (`id`),
  CONSTRAINT `check_turno_grupo` CHECK (`turno` in (1,2,3,4))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupo`
--

LOCK TABLES `grupo` WRITE;
/*!40000 ALTER TABLE `grupo` DISABLE KEYS */;
INSERT INTO `grupo` VALUES (1,'ITI 1-1',1,1,NULL,NULL,1,6),(2,'ITI 1-2',1,1,NULL,NULL,1,6),(3,'ITI 7-1',1,7,NULL,'2022-11-24',0,6),(4,'ITI 3-1',1,3,NULL,NULL,0,6),(5,'ITI 3-2',1,3,NULL,NULL,0,6),(6,'ITI 7-2',2,7,NULL,'2022-11-17',0,6),(7,'ITI 1-1',1,1,NULL,NULL,0,1),(8,'ITI 1-1',1,1,NULL,NULL,1,8);
/*!40000 ALTER TABLE `grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imparten`
--

DROP TABLE IF EXISTS `imparten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imparten` (
  `id_carrera` int(10) unsigned NOT NULL,
  `id_profesor` int(10) unsigned NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_carrera`,`id_profesor`),
  KEY `fk_idProfesor_profesor_imparten` (`id_profesor`),
  CONSTRAINT `fk_idCarrera_carrera_imparten` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id`),
  CONSTRAINT `fk_idProfesor_profesor_imparten` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imparten`
--

LOCK TABLES `imparten` WRITE;
/*!40000 ALTER TABLE `imparten` DISABLE KEYS */;
INSERT INTO `imparten` VALUES (3,24,'2022-04-25 14:59:36'),(4,24,'2022-04-25 14:59:29');
/*!40000 ALTER TABLE `imparten` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materia`
--

DROP TABLE IF EXISTS `materia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `creditos` tinyint(3) unsigned DEFAULT 8,
  `cuatrimestre` tinyint(3) unsigned NOT NULL,
  `posicion_h` tinyint(3) unsigned NOT NULL,
  `horas_totales` tinyint(3) unsigned NOT NULL,
  `tipo` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'Básica',
  `plan` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_clv_materia` (`plan`,`clave`),
  CONSTRAINT `fk_plan_planDeEstudio_materia` FOREIGN KEY (`plan`) REFERENCES `plan_de_estudio` (`id`),
  CONSTRAINT `check_tipo_materia` CHECK (`tipo` in ('Básica','Especialidad','Valores','Inglés'))
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materia`
--

LOCK TABLES `materia` WRITE;
/*!40000 ALTER TABLE `materia` DISABLE KEYS */;
INSERT INTO `materia` VALUES (1,'ING-01','Inglés I',60,1,1,60,'Inglés',5),(2,'QB-01','Quimica Básica',60,1,2,60,'Valores',5),(3,'AL-01','Algebra Lineal',60,1,3,60,'Básica',5),(4,'IP-01','Introducción a la Programación',90,1,4,90,'Especialidad',5),(5,'ITI-01','Introducción a las Tecnologías de la Información',60,1,5,60,'Especialidad',5),(6,'HO-01','Herramientas Ofimáticas',60,1,6,60,'Básica',5),(7,'EOE-01','Expresión Oral y Escrita I',60,1,7,60,'Valores',5),(8,'ING-02','Inglés II',60,2,1,60,'Inglés',5),(9,'DHV-02','Desarrollo Humano y Valores',60,2,2,60,'Valores',5),(10,'EM-02','Electricidad y Magnetismo',60,1,3,60,'Básica',5),(11,'FIS-02','Física',60,2,4,60,'Básica',5),(12,'FM-02','Funciones Matemáticas',60,2,5,90,'Básica',5),(13,'MBC-02','Matemáticas Básicas Para Computación',90,2,6,120,'Básica',5),(14,'AC-02','Arquitectura de Computadoras',90,2,7,90,'Especialidad',5),(15,'ING-03','Inglés III',60,3,1,60,'Inglés',5),(16,'CD-03','Calculo Diferencial',90,3,2,90,'Básica',5),(17,'IEMC-03','Inteligencia Emocional y Manejo de Conflictos',60,3,3,60,'Valores',5),(18,'IR-03','Introducción a Redes',90,3,4,90,'Especialidad',5),(19,'PE-03','Probabilidad y Estadística',90,3,5,90,'Básica',5),(20,'PR-03','Programación',120,3,6,120,'Especialidad',5),(21,'MEC-03','Mantenimiento a equipo de cómputo',90,3,7,90,'Especialidad',5);
/*!40000 ALTER TABLE `materia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materia_en_grupo`
--

DROP TABLE IF EXISTS `materia_en_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materia_en_grupo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `materia` int(10) unsigned DEFAULT NULL,
  `modificador_horas` int(11) DEFAULT NULL,
  `alumnos_estimados` int(10) unsigned DEFAULT 30,
  `profesor` int(10) unsigned DEFAULT NULL,
  `grupo` int(10) unsigned NOT NULL,
  `equivalente` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_profesor_grupo_materiaEnGrupo` (`profesor`,`grupo`),
  UNIQUE KEY `unik_materia_grupo_materiaEnGrupo` (`materia`,`grupo`),
  KEY `fk_grupo_grupo_materiaGRupo` (`grupo`),
  KEY `fk_equivalente_materiaEnGrupo_materiaEnGrupo` (`equivalente`),
  CONSTRAINT `fk_equivalente_materiaEnGrupo_materiaEnGrupo` FOREIGN KEY (`equivalente`) REFERENCES `materia_en_grupo` (`id`),
  CONSTRAINT `fk_grupo_grupo_materiaGRupo` FOREIGN KEY (`grupo`) REFERENCES `grupo` (`id`),
  CONSTRAINT `fk_materia_materia_materiaEnGrupo` FOREIGN KEY (`materia`) REFERENCES `materia` (`id`),
  CONSTRAINT `fk_profesor_profesor_profesor` FOREIGN KEY (`profesor`) REFERENCES `profesor` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materia_en_grupo`
--

LOCK TABLES `materia_en_grupo` WRITE;
/*!40000 ALTER TABLE `materia_en_grupo` DISABLE KEYS */;
INSERT INTO `materia_en_grupo` VALUES (1,4,0,35,17,1,NULL),(2,4,0,30,17,2,NULL),(3,3,1,35,16,1,NULL),(4,6,0,30,20,1,NULL),(5,2,0,30,24,3,NULL),(6,10,1,35,2,3,NULL),(7,7,0,30,21,3,NULL),(8,3,0,30,23,3,NULL),(9,7,0,30,21,6,NULL),(10,10,0,30,2,6,NULL),(11,16,0,30,NULL,6,NULL),(12,1,0,30,NULL,8,NULL);
/*!40000 ALTER TABLE `materia_en_grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan_de_estudio`
--

DROP TABLE IF EXISTS `plan_de_estudio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plan_de_estudio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `anio` int(10) unsigned NOT NULL,
  `clave` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nivel` char(5) COLLATE utf8_unicode_ci DEFAULT 'Ing',
  `carrera` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_nombre_corto` (`clave`),
  KEY `fk_id_carrera_planDeEstudio` (`carrera`),
  CONSTRAINT `fk_id_carrera_planDeEstudio` FOREIGN KEY (`carrera`) REFERENCES `carrera` (`id`),
  CONSTRAINT `check_nivel_plan_de_estudio` CHECK (`nivel` in ('Ing','M.I.','Esp','P.A.','Lic'))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan_de_estudio`
--

LOCK TABLES `plan_de_estudio` WRITE;
/*!40000 ALTER TABLE `plan_de_estudio` DISABLE KEYS */;
INSERT INTO `plan_de_estudio` VALUES (1,'Licenciatura en Administración y Gestión de Pequeñas y Medianas Empresas',2018,'LAyGPyME-2018','Lic',1),(2,'Profesional Asociado en Administración y Gestión de Pequeñas y Medianas Empresas',2018,'PAAyGPyME-2018','P.A.',1),(3,'Licenciatura en Comercio Internacional y Aduanas',2018,'LCIA-2018','Lic',2),(4,'Profesional Asociado en Comercio Internacional y Aduanas',2018,'PACIA-2018','P.A.',2),(5,'Ingeniería en Tecnologías de la Información',2018,'ITI-2018','Lic',3),(6,'Profesional Asociado en Tecnologías de la Información',2018,'PATI-2018','P.A.',3),(7,'Ingeniería en Mecatrónica',2018,'IM-2018','Lic',4),(8,'Profesional Asociado en Mecatrónica',2018,'PAM-2018','P.A.',4),(9,'Ingeniería en Tecnologías de Manufactura',2018,'ITM-2018','Lic',5),(10,'Profesional Asociado en Tecnologías de Manufactura',2018,'PATM-2018','P.A.',5),(11,'Ingeniería en Sistemas Automotrices',2018,'ISA-2018','Lic',6),(12,'Profesional Asociado en Sistemas Automotrices',2018,'PASA-2018','P.A.',6),(13,'Maestría en Ingeniería',2018,'MI','M.I.',7);
/*!40000 ALTER TABLE `plan_de_estudio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planeacion_academica`
--

DROP TABLE IF EXISTS `planeacion_academica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planeacion_academica` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `periodo` tinyint(3) unsigned DEFAULT 1,
  `year` smallint(5) unsigned NOT NULL,
  `estado` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'iniciada',
  `profesor` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unk_id_periodo_year_profesor_planeacionAcademica` (`periodo`,`year`,`profesor`),
  KEY `fk_profesor_planeacionAcademica_profesor` (`profesor`),
  CONSTRAINT `fk_profesor_planeacionAcademica_profesor` FOREIGN KEY (`profesor`) REFERENCES `profesor` (`id`),
  CONSTRAINT `check_periodo_planeacionAcademica` CHECK (`periodo` in (1,2,3)),
  CONSTRAINT `chk_estado_planeacionAcademica` CHECK (`estado` in ('iniciada','edicion','finalizada','aceptada'))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planeacion_academica`
--

LOCK TABLES `planeacion_academica` WRITE;
/*!40000 ALTER TABLE `planeacion_academica` DISABLE KEYS */;
INSERT INTO `planeacion_academica` VALUES (1,1,2022,'iniciada',2),(2,1,2022,'iniciada',16),(3,1,2022,'iniciada',17),(4,1,2022,'iniciada',18),(5,1,2022,'iniciada',19),(6,1,2022,'iniciada',20),(7,1,2022,'iniciada',21),(8,1,2022,'iniciada',22);
/*!40000 ALTER TABLE `planeacion_academica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planeacion_asesoria`
--

DROP TABLE IF EXISTS `planeacion_asesoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planeacion_asesoria` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institucional_estancia` tinyint(3) unsigned DEFAULT 0,
  `institucional_estadia` tinyint(3) unsigned DEFAULT 0,
  `empresarial_estancia` tinyint(3) unsigned DEFAULT 0,
  `empresarial_estadia` tinyint(3) unsigned DEFAULT 0,
  `planeacion_academica` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unk_planeacionAsesoria_planeacionAcademica` (`planeacion_academica`),
  CONSTRAINT `fk_planeacionAcademica_planeacionAsesoria_planeacionAcademica` FOREIGN KEY (`planeacion_academica`) REFERENCES `planeacion_academica` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planeacion_asesoria`
--

LOCK TABLES `planeacion_asesoria` WRITE;
/*!40000 ALTER TABLE `planeacion_asesoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `planeacion_asesoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto_cientifico`
--

DROP TABLE IF EXISTS `producto_cientifico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto_cientifico` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bibtex` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_cientifico`
--

LOCK TABLES `producto_cientifico` WRITE;
/*!40000 ALTER TABLE `producto_cientifico` DISABLE KEYS */;
/*!40000 ALTER TABLE `producto_cientifico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesor`
--

DROP TABLE IF EXISTS `profesor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profesor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nivel_adscripcion` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tipo_contrato` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'P.A',
  `categoria` char(1) COLLATE utf8_unicode_ci DEFAULT 'A',
  `inicio_contrato` date NOT NULL DEFAULT current_timestamp(),
  `fin_contrato` date DEFAULT NULL,
  `carrera_adscripcion` int(10) unsigned NOT NULL,
  `usuario` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_idProfesor_profesor_usuario` (`usuario`),
  KEY `fk_carreraAdscripcion_carrera_profesor` (`carrera_adscripcion`),
  CONSTRAINT `fk_carreraAdscripcion_carrera_profesor` FOREIGN KEY (`carrera_adscripcion`) REFERENCES `carrera` (`id`),
  CONSTRAINT `fk_usuario_usuario_profesor` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`),
  CONSTRAINT `check_tipo_contrato_profesor` CHECK (`tipo_contrato` in ('P.A','P.T.C')),
  CONSTRAINT `check_categoria_profesor` CHECK (`categoria` in ('A','B','C','D'))
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesor`
--

LOCK TABLES `profesor` WRITE;
/*!40000 ALTER TABLE `profesor` DISABLE KEYS */;
INSERT INTO `profesor` VALUES (1,'M.A.','P.T.C','B','2022-04-05',NULL,1,2),(2,'Dr.','P.T.C','B','2022-04-05',NULL,3,4),(3,'Dr.','P.T.C','D','2022-04-05',NULL,5,6),(4,'Dr.','P.T.C','D','2022-04-05',NULL,5,8),(5,'Dr.','P.T.C','B','2022-04-05',NULL,1,9),(6,'Dr.','P.T.C','B','2022-04-05',NULL,1,10),(7,'Dr.','P.T.C','B','2022-04-05',NULL,1,11),(8,'Dr.','P.T.C','B','2022-04-05',NULL,1,12),(9,'MD.D.E','P.T.C','B','2022-04-05',NULL,1,13),(16,'Dr.','P.T.C','B','2022-04-05',NULL,3,14),(17,'Dr.','P.T.C','B','2022-04-05',NULL,3,15),(18,'Dr.','P.T.C','B','2022-04-06',NULL,3,16),(19,'M.C.','P.T.C','B','2022-04-05',NULL,3,17),(20,'M.S.I','P.T.C','B','2022-04-05',NULL,3,18),(21,'Dr.','P.T.C','C','2022-04-05',NULL,3,19),(22,'Dr.','P.T.C','B','2022-04-05',NULL,3,20),(23,'M.I','P.A','A','2022-04-27','2022-08-24',3,36),(24,'Dr.','P.T.C','B','2022-04-25',NULL,4,25);
/*!40000 ALTER TABLE `profesor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_prestamo`
--

DROP TABLE IF EXISTS `solicitud_prestamo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud_prestamo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `director_solicitante` int(10) unsigned NOT NULL,
  `director_receptor` int(10) unsigned NOT NULL,
  `profesor_solicitado` int(10) unsigned NOT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `aceptada` tinyint(1) DEFAULT 0,
  `fecha_aceptada` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_directorSolicitante_usuario_solicitudPrestamo` (`director_solicitante`),
  KEY `fk_directorReceptor_usuario_solicitudPrestamo` (`director_receptor`),
  KEY `fk_profesorSolicitado_usuario_solicitudPrestamo` (`profesor_solicitado`),
  CONSTRAINT `fk_directorReceptor_usuario_solicitudPrestamo` FOREIGN KEY (`director_receptor`) REFERENCES `usuario` (`id`),
  CONSTRAINT `fk_directorSolicitante_usuario_solicitudPrestamo` FOREIGN KEY (`director_solicitante`) REFERENCES `usuario` (`id`),
  CONSTRAINT `fk_profesorSolicitado_usuario_solicitudPrestamo` FOREIGN KEY (`profesor_solicitado`) REFERENCES `profesor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_prestamo`
--

LOCK TABLES `solicitud_prestamo` WRITE;
/*!40000 ALTER TABLE `solicitud_prestamo` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitud_prestamo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `pword` char(41) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'profesor',
  `activo` tinyint(1) DEFAULT 1,
  `creado` datetime DEFAULT current_timestamp(),
  `nombre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `apellidos` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `telefono` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ext` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_email_usuario` (`email`),
  CONSTRAINT `check_tipo_usuario` CHECK (`tipo` in ('admin','profesor','director','RRHH'))
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'admin@upv.edu.mx','*4ACFE3202A5FF5CF467898FC58AAB1D615029441','admin',1,'2022-04-05 18:09:54','Administrador','Del Sistema',NULL,NULL,NULL),(2,'etorresr@upv.edu.mx','*6A57BC20BBFA959D47EE7A42BBD493B879ADC37E','director',1,'2022-04-05 18:09:54','Estela','Torres Ramírez',NULL,NULL,NULL),(3,'etorresr2@upv.edu.mx','*6A57BC20BBFA959D47EE7A42BBD493B879ADC37E','director',1,'2022-04-05 18:09:54','Estela','Torres Ramírez',NULL,NULL,NULL),(4,'mrodriguezc@upv.edu.mx','*298B6800D9C1E340B59B22A9622F5124CDACD94A','director',1,'2022-04-05 18:09:54','Mario Humberto','Rodríguez Chávez',NULL,NULL,NULL),(5,'corozcog@upv.edu.mx','*2567782B7E56052F2703593E9DC0CAC796271A90','director',1,'2022-04-05 18:09:54','Carlos','Orozco García',NULL,NULL,NULL),(6,'jrodriguezg@upv.edu.mx','*146F9C520EB35CE1045B7B3C0BAD2EBA985F59CD','director',1,'2022-04-05 18:09:54','José Amparo','Rodríguez García',NULL,NULL,NULL),(7,'jpenam@upv.edu.mx ','*F02FC1ADD5C0807A84D8BF4EF7CC2E34F20D41B4','director',1,'2022-04-05 18:09:54','Joel Ricardo','Peña Mercado',NULL,NULL,NULL),(8,'erochar@upv.edu.mx ','*01695CA085791197D1F244D79ECBAB41ACF45453','director',1,'2022-04-05 18:09:54','Enrique','Rocha Rangel',NULL,NULL,NULL),(9,'dcruzd@upv.edu.mx','*5816AB29A960698C11FC00A551381FBE4143F63C','profesor',1,'2022-04-05 18:09:54','Daniela','Cruz Delgado',NULL,NULL,NULL),(10,'jgarciaa@upv.edu.mx','*57AD0BF5CEFFCEFE738437B7D254DCB659EA4369','profesor',1,'2022-04-05 18:09:54','Jesús','García Amado',NULL,NULL,NULL),(11,'jgarciam@upv.edu.mx','*552E6B5DA9109556D85D93B2196B43625D1BDE62','profesor',1,'2022-04-05 18:09:54','Julio César','García Martínez',NULL,NULL,NULL),(12,'vmartinezr@upv.edu.mx','*AA2335864E4FECD2EFD1AB52DA8ABCB5B4B504B8','profesor',1,'2022-04-05 18:09:54','Victor Manuel','Martínez Rocha',NULL,NULL,NULL),(13,'mvazquezl@upv.edu.mx','*520D28181698D7CE8DE3189EC071660708A7A197','profesor',1,'2022-04-05 18:09:54','Mariana','Vázquez Loya',NULL,NULL,NULL),(14,'havilesa@upv.edu.mx','*E5F004524902C672A613E79F0C9605D1C787EF21','profesor',1,'2022-04-05 18:09:54','Héctor Hugo','Aviles Arriaga',NULL,NULL,NULL),(15,'jhernaneza@upv.edu.mx','*26E224E8641861718A3503C7A4351D6094C428BA','profesor',1,'2022-04-05 18:09:54','Jorge Arturo','Hernández Almazán',NULL,NULL,NULL),(16,'hherrerar@upv.edu.mx','*688495B6A6CADC32670CDE88D6D401A6F583B8FF','profesor',1,'2022-04-05 18:09:54','Hiram','Herrera Rivas',NULL,NULL,NULL),(17,'ojassol@upv.edu.mx','*8422A1ADB4E0D8504FA260009786F9277CF77951','profesor',1,'2022-04-05 18:09:54','Jorge Omar','Jasso Luna',NULL,NULL,NULL),(18,'jlopezl@upv.edu.mx','*7986A5AFC271E157F3F0151B4D3A69F54D22E022','profesor',1,'2022-04-05 18:09:54','José Fidencio','López Luna',NULL,NULL,NULL),(19,'mnunom@upv.edu.mx','*EEFEF73AF4BDD4034C57F3FD4BA2CBFD7C194161','profesor',1,'2022-04-05 18:09:54','Marco Aurelio','Nuño Maganda',NULL,NULL,NULL),(20,'spolancom@upv.edu.mx','*1A35331DF60BDD0039FA232859ED62AC39B28A9A','profesor',1,'2022-04-05 18:09:54','Said','Polanco Martagón',NULL,NULL,NULL),(21,'lbortonia@upv.edu.mx','*C9A5E02B5DF6B6D12208FD493CA56B470765CFD6','profesor',1,'2022-04-05 18:09:54','Liborio Jesús','Bortoni Anzures',NULL,NULL,NULL),(22,'ccallesa@upv.edu.mx','*BD8FBACD13F39762232E76E3F5B67D161DE1CD6E','profesor',1,'2022-04-05 18:09:54','Carlos Adrián','Calles Arriaga',NULL,NULL,NULL),(23,'recheverrias@upv.edu.mx','*34366B183F50739CC483B464A642BF49C58A0F32','profesor',1,'2022-04-05 18:09:54','Rodolfo Arturo','Echeverría Solís',NULL,NULL,NULL),(24,'yhernandezm@upv.edu.mx','*85D45BA4498F6E903B8EAF7B68D041761D83DA10','profesor',1,'2022-04-05 18:09:54','Yahir','Hernández Mier',NULL,NULL,NULL),(25,'mortizm@upv.edu.mx','*F68102DD4349DDC3AD00C1002DBA4BA1D3C73361','profesor',1,'2022-04-05 18:09:54','Manuel Benjamín','Ortíz Moctezuma',NULL,NULL,NULL),(26,'wpechr@upv.edu.mx','*0775B8CB6BC4C6AF5B29BB3375B7F3D71DD26FF4','profesor',1,'2022-04-05 18:09:54','Willian Jesús','Pech Rodríguez',NULL,NULL,NULL),(27,'gsuarezv@upv.edu.mx','*67D092239FB921C9883886ABF580C40E58333110','profesor',1,'2022-04-05 18:09:54','Gladis Guadalupe','Súarez Velázquez',NULL,NULL,NULL),(28,'earmendarizm@upv.edu.mx','*404FA0B9E353CC9626F06A747E3159A9D341A021','profesor',1,'2022-04-05 18:09:54','Eddie Nahúm','Armendáriz Mireles',NULL,NULL,NULL),(29,'mburgosq@upv.edu.mx','*56C94AA79B3733EECFE768332648CDBB6F379300','profesor',1,'2022-04-05 18:09:54','Ma. Guadalupe','Burgos Quiroz',NULL,NULL,NULL),(30,'rmachuchoc@upv.edu.mx','*6FD3B3CB7A4380340E82F06C8D1334CF2BC47457','profesor',1,'2022-04-05 18:09:54','Ruben','Machucho Cadena',NULL,NULL,NULL),(31,'jmartinezt@upv.edu.mx','*A472044DBD5B30BA44EDDD009E6C5BDB9C4DFA6A','profesor',1,'2022-04-05 18:09:54','Juan Julian','Martínez Torres',NULL,NULL,NULL),(32,'jlopezh@upv.edu.mx','*2E0BC90DDC72C0F90FCB1EE48D76FF91D3FCE5F3','profesor',1,'2022-04-05 18:09:54','Juan','López Hernández',NULL,NULL,NULL),(33,'hriojasg@upv.edu.mx','*2AB6985D791F5E8BCE993C011D2CE193C34D75A6','profesor',1,'2022-04-05 18:09:54','Héctor Hugo','Riojas González',NULL,NULL,NULL),(34,'lmaldonador@upv.edu.mx','*D921A268B2A2FF1D9BB43DE950AE26404000C72D','profesor',1,'2022-04-05 18:09:54','Leonel','Maldonado Rivera',NULL,NULL,NULL),(35,'acastillor@upv.edu.mx','*F59351482122F23990A8B0E8ADE06F517CE51473','profesor',1,'2022-04-05 18:09:54','Adalberto','Castillo Robles',NULL,NULL,NULL),(36,'mortiza@upv.edu.mx','adadsfasdf','profesor',1,'2022-04-25 14:49:44','María Raquel','Ortiz Alvarez',NULL,NULL,NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-04-29 18:23:52
