CREATE DATABASE IF NOT EXISTS dsa CHARACTER SET utf8 COLLATE utf8_unicode_ci;
use dsa;

DROP TABLE IF EXISTS coautor;
DROP TABLE IF EXISTS producto_cientifico;
DROP TABLE IF EXISTS actividad_investigacion;
DROP TABLE IF EXISTS planeacion_asesoria;
DROP TABLE IF EXISTS actividad;
DROP TABLE IF EXISTS planeacion_academica;
DROP TABLE IF EXISTS materia_en_grupo;
DROP TABLE IF EXISTS grupo;
DROP TABLE IF EXISTS carga_academica;
DROP TABLE IF EXISTS solicitud_prestamo;
DROP TABLE IF EXISTS imparten;
DROP TABLE IF EXISTS disponibilidad;
DROP TABLE IF EXISTS profesor;
DROP TABLE IF EXISTS materia;
DROP TABLE IF EXISTS plan_de_estudio;
DROP TABLE IF EXISTS carrera;
DROP TABLE IF EXISTS usuario;

CREATE TABLE IF NOT EXISTS usuario (
    id INTEGER UNSIGNED AUTO_INCREMENT,
    email VARCHAR(150) NOT NULL,
    pword CHAR(41) NOT NULL,
    tipo VARCHAR(10) DEFAULT 'profesor',
    activo BOOLEAN DEFAULT TRUE,
    creado DATETIME DEFAULT NOW(),
    nombre VARCHAR(150) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    telefono CHAR(10) NULL,
    ext CHAR(4) NULL,
    foto VARCHAR(250) NULL,

    CONSTRAINT pk_id_usuario PRIMARY KEY (id),
    CONSTRAINT unik_email_usuario UNIQUE (email),
    CONSTRAINT check_tipo_usuario CHECK ( tipo in ('admin', 'profesor', 'director', 'RRHH'))
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS carrera (
    id INTEGER UNSIGNED AUTO_INCREMENT,
    nombre VARCHAR(250) NOT NULL,
    clave VARCHAR(10) NULL,
    nivel VARCHAR(10) DEFAULT 'Ing',
    director INTEGER UNSIGNED NOT NULL,

    CONSTRAINT pk_id_carrera PRIMARY KEY (id),
    CONSTRAINT unik_clave_carrera UNIQUE (clave),
    CONSTRAINT check_nivel_carrera CHECK ( nivel in ('Ing', 'Lic', 'M.I.')),
    CONSTRAINT unink_director UNIQUE (director),
    CONSTRAINT fk_director_usuario_carrera FOREIGN KEY (director) REFERENCES usuario(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS plan_de_estudio (
    id INTEGER UNSIGNED AUTO_INCREMENT,
    nombre VARCHAR(250) NOT NULL,
    anio INTEGER UNSIGNED NOT NULL,
    clave VARCHAR(50) NOT NULL,
    nivel CHAR(5) DEFAULT 'Ing',
    carrera INTEGER UNSIGNED NOT NULL,

    CONSTRAINT pk_id_planDeEstudio PRIMARY KEY (id),
    CONSTRAINT unik_nombre_corto UNIQUE (clave),
    CONSTRAINT check_nivel_plan_de_estudio CHECK ( nivel in ('Ing', 'M.I.', 'Esp', 'P.A.', 'Lic')),
    CONSTRAINT fk_id_carrera_planDeEstudio FOREIGN KEY (carrera) REFERENCES carrera(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS materia (
    id INTEGER UNSIGNED AUTO_INCREMENT,
    clave VARCHAR(20) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    creditos TINYINT UNSIGNED DEFAULT 8,
    cuatrimestre TINYINT UNSIGNED NOT NULL,
    posicion_h TINYINT UNSIGNED NOT NULL,
    horas_totales TINYINT UNSIGNED NOT NULL,
    tipo VARCHAR(20) DEFAULT 'Básica',
    plan INTEGER UNSIGNED NOT NULL,

    CONSTRAINT pk_materia PRIMARY KEY (id),
    CONSTRAINT unik_clv_materia UNIQUE (plan, clave),
    CONSTRAINT check_tipo_materia CHECK ( tipo IN ('Básica', 'Especialidad', 'Valores', 'Inglés')),
    CONSTRAINT fk_plan_planDeEstudio_materia FOREIGN KEY (plan) REFERENCES plan_de_estudio(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS profesor (
    id INTEGER UNSIGNED AUTO_INCREMENT,
    nivel_adscripcion VARCHAR(10) NULL,
    tipo_contrato VARCHAR(10) DEFAULT 'P.A',
    categoria CHAR(1) DEFAULT 'A',
    inicio_contrato DATE NOT NULL DEFAULT NOW(),
    fin_contrato DATE NULL,
    carrera_adscripcion INTEGER UNSIGNED NOT NULL,
    usuario INTEGER UNSIGNED NOT NULL,

    CONSTRAINT pk_profesor PRIMARY KEY (id),
    CONSTRAINT unik_idProfesor_profesor_usuario UNIQUE (usuario),
    CONSTRAINT check_tipo_contrato_profesor CHECK (tipo_contrato IN ('P.A', 'P.T.C')),
    CONSTRAINT check_categoria_profesor CHECK (categoria IN ('A', 'B', 'C', 'D')),
    CONSTRAINT fk_carreraAdscripcion_carrera_profesor FOREIGN KEY (carrera_adscripcion) REFERENCES carrera (id),
    CONSTRAINT fk_usuario_usuario_profesor FOREIGN KEY (usuario) REFERENCES usuario (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS disponibilidad (
    dia TINYINT NOT NULL,
    hora TINYINT NOT NULL,
    profesor INTEGER UNSIGNED NOT NULL,
    CONSTRAINT check_dia_disponibilidad CHECK (dia IN (0, 1, 2, 3, 4, 5)),
    CONSTRAINT check_hora_disponibilidad CHECK (hora IN (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13)),
    CONSTRAINT unik_dia_hora_disponibilidad UNIQUE (dia, hora, profesor),
    CONSTRAINT fk_profesor_profesor_disponibilidad FOREIGN KEY (profesor) REFERENCES profesor (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS imparten (
    id_carrera INTEGER UNSIGNED NOT NULL,
    id_profesor INTEGER UNSIGNED NOT NULL,
    fecha_creacion DATETIME DEFAULT NOW(),

    CONSTRAINT pk_idCarrera_idProfesor PRIMARY KEY (id_carrera, id_profesor),
    CONSTRAINT fk_idCarrera_carrera_imparten FOREIGN KEY (id_carrera) REFERENCES carrera(id),
    CONSTRAINT fk_idProfesor_profesor_imparten FOREIGN KEY (id_profesor) REFERENCES profesor (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS solicitud_prestamo (
    id INTEGER UNSIGNED AUTO_INCREMENT,
    director_solicitante INTEGER UNSIGNED NOT NULL,
    director_receptor INTEGER UNSIGNED NOT NULL,
    profesor_solicitado INTEGER UNSIGNED NOT NULL,
    fecha_solicitud DATETIME DEFAULT NOW(),
    aceptada BOOLEAN DEFAULT FALSE,
    fecha_aceptada DATETIME NULL,

    CONSTRAINT pk_idSolicitudPrestamo PRIMARY KEY (id),
    CONSTRAINT fk_directorSolicitante_usuario_solicitudPrestamo FOREIGN KEY  (director_solicitante) REFERENCES usuario (id),
    CONSTRAINT fk_directorReceptor_usuario_solicitudPrestamo FOREIGN KEY (director_receptor) REFERENCES usuario (id),
    CONSTRAINT fk_profesorSolicitado_usuario_solicitudPrestamo FOREIGN KEY (profesor_solicitado) REFERENCES profesor (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS carga_academica (
    id INT UNSIGNED AUTO_INCREMENT,
    plan_estudios INT UNSIGNED NOT NULL,
    periodo TINYINT UNSIGNED DEFAULT 1,
    fecha_inicio DATE NOT NULL,
    fecha_final DATE NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,

    CONSTRAINT pk_idCargaAcademica PRIMARY KEY (id),
    CONSTRAINT check_periodo_cargaAcademica CHECK (periodo in (1, 2, 3)),
    CONSTRAINT unik_planPeriodoAnio_cargaAcademica UNIQUE (plan_estudios, periodo, anio),
    CONSTRAINT fk_planEstudios_planDeEstudio_cargaAcademica FOREIGN KEY (plan_estudios) REFERENCES plan_de_estudio(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS grupo (
    id INT UNSIGNED AUTO_INCREMENT,
    clave VARCHAR(50) NOT NULL,
    turno TINYINT UNSIGNED DEFAULT 1,
    cuatrimestre TINYINT UNSIGNED NULL,
    fecha_inicio DATE NULL,
    fecha_final DATE NULL,
    finalizado BOOLEAN DEFAULT FALSE,
    carga_academica INT UNSIGNED NOT NULL,

    CONSTRAINT pk_grupo PRIMARY KEY (id),
    CONSTRAINT check_turno_grupo CHECK ( turno in (1, 2, 3, 4)),
    CONSTRAINT unik_clave_cargaAcademica UNIQUE (clave, carga_academica),
    CONSTRAINT fk_cargaAcademica_grupo_cargaAcademica FOREIGN KEY (carga_academica) references carga_academica (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS materia_en_grupo (
    id INT UNSIGNED AUTO_INCREMENT,
    materia INT UNSIGNED NULL,
    modificador_horas INT NULL,
    alumnos_estimados INT UNSIGNED DEFAULT 30,
    profesor INT UNSIGNED NULL,
    grupo INT UNSIGNED NOT NULL,
    equivalente INT UNSIGNED NULL,

    CONSTRAINT pk_materiaEnGrupo PRIMARY KEY (id),
    CONSTRAINT fk_materia_materia_materiaEnGrupo FOREIGN KEY (materia) REFERENCES materia (id),
    CONSTRAINT fk_profesor_profesor_profesor FOREIGN KEY (profesor) REFERENCES profesor (id),
    CONSTRAINT fk_grupo_grupo_materiaGRupo FOREIGN KEY (grupo) REFERENCES grupo (id),
    CONSTRAINT unik_profesor_grupo_materiaEnGrupo UNIQUE (profesor, grupo),
    CONSTRAINT unik_materia_grupo_materiaEnGrupo UNIQUE (materia, grupo),
    CONSTRAINT fk_equivalente_materiaEnGrupo_materiaEnGrupo FOREIGN KEY (equivalente) REFERENCES materia_en_grupo (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS planeacion_academica (
    id INT UNSIGNED AUTO_INCREMENT,
    periodo TINYINT UNSIGNED DEFAULT 1,
    year SMALLINT UNSIGNED NOT NULL,
    estado VARCHAR(10) DEFAULT 'iniciada',
    profesor INT UNSIGNED NOT NULL,

    CONSTRAINT pk_planeacionAcademica PRIMARY KEY (id),
    CONSTRAINT unk_id_periodo_year_profesor_planeacionAcademica UNIQUE (periodo, year, profesor),
    CONSTRAINT check_periodo_planeacionAcademica CHECK ( periodo IN (1, 2, 3) ),
    CONSTRAINT chk_estado_planeacionAcademica CHECK (estado IN ('iniciada', 'edicion', 'finalizada', 'aceptada')),
    CONSTRAINT fk_profesor_planeacionAcademica_profesor FOREIGN KEY (profesor) REFERENCES profesor(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS actividad (
    id INT UNSIGNED AUTO_INCREMENT,
    tipo VARCHAR(15) NOT NULL,
    descripcion VARCHAR(500) NOT NULL,
    empresa_receptora VARCHAR(500) NULL,
    horas TINYINT UNSIGNED DEFAULT 0,
    evidencia VARCHAR(500) NOT NULL,
    planeacion_academica INT UNSIGNED NOT NULL,

    CONSTRAINT pk_gestionAcademica PRIMARY KEY (id),
    CONSTRAINT chk_tipo_actividad CHECK ( tipo IN ('GESTION', 'CAPACITACION', 'VINCULACION', 'PROMOCION')),
    CONSTRAINT fk_planeacionAcademica_gestionAcademica_planeacionAcademica FOREIGN KEY (planeacion_academica) REFERENCES planeacion_academica (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS planeacion_asesoria (
    id INT UNSIGNED AUTO_INCREMENT,
    institucional_estancia TINYINT UNSIGNED DEFAULT 0,
    institucional_estadia TINYINT UNSIGNED DEFAULT 0,
    empresarial_estancia TINYINT UNSIGNED DEFAULT 0,
    empresarial_estadia TINYINT UNSIGNED DEFAULT 0,
    planeacion_academica INT UNSIGNED NOT NULL,

    CONSTRAINT pk_planeacionAsesoria PRIMARY KEY (id),
    CONSTRAINT unk_planeacionAsesoria_planeacionAcademica UNIQUE (planeacion_academica),
    CONSTRAINT fk_planeacionAcademica_planeacionAsesoria_planeacionAcademica FOREIGN KEY (planeacion_academica) REFERENCES planeacion_academica (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS actividad_investigacion (
    id INT UNIQUE AUTO_INCREMENT,
    actividad VARCHAR(500) NOT NULL,
    tipo VARCHAR(500) NOT NULL,
    avance_actual TINYINT UNSIGNED DEFAULT 0,
    avance_esperado TINYINT UNSIGNED DEFAULT 0,
    fecha_termino DATE NOT NULL,
    planeacion_academica INT UNSIGNED NOT NULL,

    CONSTRAINT pk_actividadInvestigacion PRIMARY KEY (id),
    CONSTRAINT fk_planeacionAcademica_activInv_planeacionAcademica FOREIGN KEY (planeacion_academica) REFERENCES planeacion_academica (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS producto_cientifico (
    id INT UNSIGNED AUTO_INCREMENT,
    bibtex TEXT NOT NULL,

    CONSTRAINT pk_productoCientifico PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE IF NOT EXISTS coautor (
    profesor INT UNSIGNED NOT NULL,
    producto INT UNSIGNED NOT NULL,
    posicion TINYINT UNSIGNED NOT NULL,

    CONSTRAINT pk_autores PRIMARY KEY (profesor, producto),
    CONSTRAINT fk_profesor_autores_profesor FOREIGN KEY (profesor) REFERENCES profesor (id),
    CONSTRAINT fk_producto_autores_productoCientifico FOREIGN KEY (producto) REFERENCES producto_cientifico (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8mb3_unicode_ci;


DROP USER IF EXISTS 'dsa_admi'@'localhost';
create user 'dsa_admi'@'localhost' IDENTIFIED BY 'sctAPWE_#F|PhzX%';
REVOKE ALL ON *.* FROM 'dsa_admi'@'localhost';
GRANT ALL PRIVILEGES ON dsa.* TO 'dsa_admi'@'localhost' IDENTIFIED BY 'sctAPWE_#F|PhzX%';

DROP USER IF EXISTS 'dsa_director'@'localhost';
create user 'dsa_director'@'localhost' IDENTIFIED BY 'DMFJBXmHjeXk_V_T';
REVOKE ALL ON *.* FROM 'dsa_director'@'localhost';
GRANT SELECT ON dsa.* TO 'dsa_director'@'localhost';
GRANT UPDATE ON dsa.usuario TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE ON dsa.plan_de_estudio TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE ON dsa.materia TO 'dsa_director'@'localhost';
GRANT UPDATE ON dsa.profesor TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE ON dsa.imparten TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE ON dsa.solicitud_prestamo TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE ON dsa.carga_academica TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE ON dsa.grupo TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE, DELETE ON dsa.materia_en_grupo TO 'dsa_director'@'localhost';
GRANT INSERT, UPDATE ON dsa.planeacion_academica TO 'dsa_director'@'localhost';

DROP USER IF EXISTS 'dsa_profesor'@'localhost';
CREATE USER 'dsa_profesor'@'localhost' IDENTIFIED BY 'jSMAwR+y#QMerx@A';
REVOKE ALL on *.* FROM 'dsa_profesor'@'localhost';
GRANT SELECT ON dsa.* TO 'dsa_profesor'@'localhost';
GRANT UPDATE ON dsa.usuario TO 'dsa_profesor'@'localhost';
GRANT INSERT, DELETE, UPDATE ON dsa.disponibilidad TO 'dsa_profesor'@'localhost';
GRANT INSERT, UPDATE, DELETE ON dsa.actividad TO 'dsa_profesor'@'localhost';
GRANT INSERT, UPDATE ON dsa.planeacion_asesoria TO 'dsa_profesor'@'localhost';
GRANT INSERT, UPDATE, DELETE ON dsa.actividad_investigacion TO 'dsa_profesor'@'localhost';
GRANT INSERT, UPDATE, DELETE ON dsa.producto_cientifico TO 'dsa_profesor'@'localhost';
GRANT COUNT, INSERT, UPDATE ON dsa.coautor TO 'dsa_profesor'@'localhost';
GRANT UPDATE ON dsa.planeacion_academica TO 'dsa_profesor'@'localhost';

DROP USER IF EXISTS 'dsa_rrhh'@'localhost';
CREATE USER 'dsa_rrhh'@'localhost' IDENTIFIED BY 'US%Q$nz&c!CKaJUq';
REVOKE ALL ON *.* FROM 'dsa_rrhh'@'localhost';
GRANT SELECT ON dsa.* TO 'dsa_rrhh'@'localhost';
GRANT INSERT, UPDATE ON dsa.usuario TO 'dsa_rrhh'@'localhost';
GRANT INSERT, UPDATE ON dsa.profesor TO 'dsa_rrhh'@'localhost';
GRANT INSERT, UPDATE ON dsa.imparten TO 'dsa_rrhh'@'localhost';

DROP USER IF EXISTS 'dsa_default'@'localhost';
create user 'dsa_default'@'localhost' IDENTIFIED BY 'OHGlLrHZ?YonX@ZW';
REVOKE ALL ON *.* FROM 'dsa_default'@'localhost';
GRANT SELECT ON dsa.* TO 'dsa_default'@'localhost' IDENTIFIED BY 'OHGlLrHZ?YonX@ZW';
