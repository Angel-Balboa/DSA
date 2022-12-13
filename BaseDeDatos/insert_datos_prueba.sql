INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('admin@upv.edu.mx', password('admin'), 'admin', 'Administrador', 'Del Sistema');
INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('etorresr@upv.edu.mx', password('etorresr'), 'director', 'Estela', 'Torres Ramírez');
INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('etorresr2@upv.edu.mx', password('etorresr'), 'director', 'Estela', 'Torres Ramírez');
INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('mrodriguezc@upv.edu.mx', password('mrodriguezc'), 'director', 'Mario Humberto', 'Rodríguez Chávez');
INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('corozcog@upv.edu.mx', password('corozcog'), 'director', 'Carlos', 'Orozco García');
INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('jrodriguezg@upv.edu.mx', password('jrodriguezg'), 'director', 'José Amparo', 'Rodríguez García');
INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('jpenam@upv.edu.mx ', password('jpenam'), 'director', 'Joel Ricardo', 'Peña Mercado');
INSERT INTO usuario (email, pword, tipo, nombre, apellidos) VALUES ('erochar@upv.edu.mx ', password('erochar'), 'director', 'Enrique', 'Rocha Rangel');

# Licenciatura en Administración y Gestión de Pequeñas y Medianas Empresas
INSERT INTO carrera (nombre, clave, nivel, director) VALUES ('Licenciatura en Administración y Gestión de Pequeñas y Medianas Empresas', 'LAyGPyMe', 'Lic', 2);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Licenciatura en Administración y Gestión de Pequeñas y Medianas Empresas', 2018, 'LAyGPyME-2018', 'Lic', 1);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Profesional Asociado en Administración y Gestión de Pequeñas y Medianas Empresas', 2018, 'PAAyGPyME-2018', 'P.A.', 1);
INSERT INTO profesor(nivel_adscripcion, tipo_contrato, categoria, inicio_contrato, fin_contrato, carrera_adscripcion, usuario) VALUES ('M.A.', 'P.T.C', 'B', NOW(), NULL, 1, 2);

INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('dcruzd@upv.edu.mx', password('dcruzd'), 'profesor', 'Daniela', 'Cruz Delgado');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('jgarciaa@upv.edu.mx', password('jgarciaa'), 'profesor', 'Jesús', 'García Amado');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('jgarciam@upv.edu.mx', password('jgarciam'), 'profesor', 'Julio César', 'García Martínez');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('vmartinezr@upv.edu.mx', password('vmartinezr'), 'profesor', 'Victor Manuel', 'Martínez Rocha');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('mvazquezl@upv.edu.mx', password('mvazquezl'), 'profesor', 'Mariana', 'Vázquez Loya');

# Licenciatura en Comercio Internacional y Aduanas
INSERT INTO carrera (nombre, clave, nivel, director) VALUES ('Licenciatura en Comercio Internacional y Aduanas', 'LCIA', 'Lic', 3);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Licenciatura en Comercio Internacional y Aduanas', 2018, 'LCIA-2018', 'Lic', 2);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Profesional Asociado en Comercio Internacional y Aduanas', 2018, 'PACIA-2018', 'P.A.', 2);
# Ingeniería en Tecnologías de la Información
INSERT INTO carrera (nombre, clave, nivel, director) VALUES ('Ingeniería en Tecnologías de la Información', 'ITI', 'Ing', 4);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Ingeniería en Tecnologías de la Información', 2018, 'ITI-2018', 'Lic', 3);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Profesional Asociado en Tecnologías de la Información', 2018, 'PATI-2018', 'P.A.', 3);
INSERT INTO profesor(nivel_adscripcion, tipo_contrato, categoria, inicio_contrato, fin_contrato, carrera_adscripcion, usuario) VALUES ('Dr.', 'P.T.C', 'B', NOW(), NULL, 3, 4);

INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('havilesa@upv.edu.mx', password('havilesa'), 'profesor', 'Héctor Hugo', 'Aviles Arriaga');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('jhernaneza@upv.edu.mx', password('jhernaneza'), 'profesor', 'Jorge Arturo', 'Hernández Almazán');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('hherrerar@upv.edu.mx', password('hherrerar'), 'profesor', 'Hiram', 'Herrera Rivas');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('ojassol@upv.edu.mx', password('ojassol'), 'profesor', 'Jorge Omar', 'Jasso Luna');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('jlopezl@upv.edu.mx', password('jlopezl'), 'profesor', 'José Fidencio', 'López Luna');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('mnunom@upv.edu.mx', password('mnunom'), 'profesor', 'Marco Aurelio', 'Nuño Maganda');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('spolancom@upv.edu.mx', password('spolancom'), 'profesor', 'Said', 'Polanco Martagón');

# Ingeniería en Mecatrónica
INSERT INTO carrera (nombre, clave, nivel, director) VALUES ('Ingeniería en Mecatrónica', 'IM', 'Ing', 5);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Ingeniería en Mecatrónica', 2018, 'IM-2018', 'Lic', 4);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Profesional Asociado en Mecatrónica', 2018, 'PAM-2018', 'P.A.', 4);
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('lbortonia@upv.edu.mx', password('lbortonia'), 'profesor', 'Liborio Jesús', 'Bortoni Anzures');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('ccallesa@upv.edu.mx', password('ccallesa'), 'profesor', 'Carlos Adrián', 'Calles Arriaga');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('recheverrias@upv.edu.mx', password('recheverrias'), 'profesor', 'Rodolfo Arturo', 'Echeverría Solís');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('yhernandezm@upv.edu.mx', password('yhernandezm'), 'profesor', 'Yahir', 'Hernández Mier');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('mortizm@upv.edu.mx', password('mortizm'), 'profesor', 'Manuel Benjamín', 'Ortíz Moctezuma');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('wpechr@upv.edu.mx', password('wpechr'), 'profesor', 'Willian Jesús', 'Pech Rodríguez');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('gsuarezv@upv.edu.mx', password('gsuarezv'), 'profesor', 'Gladis Guadalupe', 'Súarez Velázquez');

# Ingeniería en Tecnologías de Manufactura
INSERT INTO carrera (nombre, clave, nivel, director) VALUES ('Ingeniería en Tecnologías de Manufactura', 'ITM', 'Ing', 6);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Ingeniería en Tecnologías de Manufactura', 2018, 'ITM-2018', 'Lic', 5);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Profesional Asociado en Tecnologías de Manufactura', 2018, 'PATM-2018', 'P.A.', 5);
INSERT INTO profesor(nivel_adscripcion, tipo_contrato, categoria, inicio_contrato, fin_contrato, carrera_adscripcion, usuario) VALUES ('Dr.', 'P.T.C', 'D', NOW(), NULL, 5, 6);
INSERT INTO profesor(nivel_adscripcion, tipo_contrato, categoria, inicio_contrato, fin_contrato, carrera_adscripcion, usuario) VALUES ('Dr.', 'P.T.C', 'D', NOW(), NULL, 5, 8);
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('earmendarizm@upv.edu.mx', password('earmendarizm'), 'profesor', 'Eddie Nahúm', 'Armendáriz Mireles');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('mburgosq@upv.edu.mx', password('mburgosq'), 'profesor', 'Ma. Guadalupe', 'Burgos Quiroz');
# Ingeniería en Sistemas Automotrices
INSERT INTO carrera (nombre, clave, nivel, director) VALUES ('Ingeniería en Sistemas Automotrices', 'ISA', 'Ing', 7);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Ingeniería en Sistemas Automotrices', 2018, 'ISA-2018', 'Lic', 6);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Profesional Asociado en Sistemas Automotrices', 2018, 'PASA-2018', 'P.A.', 6);
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('rmachuchoc@upv.edu.mx', password('rmachuchoc'), 'profesor', 'Ruben', 'Machucho Cadena');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('jmartinezt@upv.edu.mx', password('jmartinezt'), 'profesor', 'Juan Julian', 'Martínez Torres');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('jlopezh@upv.edu.mx', password('jlopezh'), 'profesor', 'Juan', 'López Hernández');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('hriojasg@upv.edu.mx', password('hriojasg'), 'profesor', 'Héctor Hugo', 'Riojas González');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('lmaldonador@upv.edu.mx', password('lmaldonador'), 'profesor', 'Leonel', 'Maldonado Rivera');
INSERT INTO usuario(email, pword, tipo,nombre, apellidos) VALUES ('acastillor@upv.edu.mx', password('acastillor'), 'profesor', 'Adalberto', 'Castillo Robles');
# Maestría en Ingeniería
INSERT INTO carrera (nombre, clave, nivel, director) VALUES ('Maestría en Ingeniería', 'MI', 'M.I.', 8);
INSERT INTO plan_de_estudio (nombre, anio, clave, nivel, carrera) VALUES ('Maestría en Ingeniería', 2018, 'MI', 'M.I.', 7);

INSERT INTO `profesor` VALUES (5,'Dr.','P.T.C','B','2022-04-05',NULL,1,9),
                              (6,'Dr.','P.T.C','B','2022-04-05',NULL,1,10),
                              (7,'Dr.','P.T.C','B','2022-04-05',NULL,1,11),
                              (8,'Dr.','P.T.C','B','2022-04-05',NULL,1,12),
                              (9,'MD.D.E','P.T.C','B','2022-04-05',NULL,1,13),
                              (16,'Dr.','P.T.C','B','2022-04-05',NULL,3,14),
                              (17,'Dr.','P.T.C','B','2022-04-05',NULL,3,15),
                              (18,'Dr.','P.T.C','B','2022-04-06',NULL,3,16),
                              (19,'M.C.','P.T.C','B','2022-04-05',NULL,3,17),
                              (20,'M.S.I','P.T.C','B','2022-04-05',NULL,3,18),
                              (21,'Dr.','P.T.C','C','2022-04-05',NULL,3,19),
                              (22,'Dr.','P.T.C','B','2022-04-05',NULL,3,20);

