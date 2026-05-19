CREATE DATABASE NightFest;
USE NightFest;

CREATE TABLE IF NOT EXISTS usuarios (     
id INT AUTO_INCREMENT PRIMARY KEY,     
nombre VARCHAR(100) NOT NULL,     
email VARCHAR(150) NOT NULL UNIQUE,     
password VARCHAR(255) NOT NULL,     
rol ENUM('estandar', 'admin') DEFAULT 'estandar',     
foto_perfil VARCHAR(255) DEFAULT 'default.png',     
fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
INDEX (email) 
) ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador', 'admin@correo.com', '123456', 'admin');

INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador', 'carol@correo.com', '12345', 'admin');

CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artista VARCHAR(100) NOT NULL,
    imagen VARCHAR(255) DEFAULT 'default.jpg', -- Aquí pondrás nombres como 'latinmafia.jpg'
    fecha_evento DATE NOT NULL,
    hora VARCHAR(20) NOT NULL,
    localidad VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(100) NOT NULL,
    estado ENUM('DISPONIBLE', 'AGOTADO', 'PRÓXIMAMENTE') DEFAULT 'DISPONIBLE'
);

-- Limpiamos la tabla para empezar de cero
TRUNCATE TABLE eventos;

INSERT INTO eventos (artista, imagen, fecha_evento, hora, localidad, ubicacion, estado) VALUES
-- PÁGINA 1
('LATIN MAFIA', 'estandar.jpg', '2026-05-10', '20:30', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('BAD GYAL', 'estandar2.jpg', '2026-05-12', '21:00', 'Barcelona', 'Pacha', 'DISPONIBLE'),
('QUEVEDO', 'estandar.jpg', '2026-05-15', '22:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('BORO BORO', 'estandar2.jpg', '2026-05-18', '23:30', 'Barcelona', 'Sutton', 'DISPONIBLE'),
('DURO FESTIVAL', 'estandar.jpg', '2026-05-20', '18:00', 'Montmeló', 'Circuito', 'DISPONIBLE'),
('OPIUM GALA', 'estandar2.jpg', '2026-05-22', '23:55', 'Barcelona', 'Opium Beach', 'DISPONIBLE'),
('RELS B', 'estandar.jpg', '2026-05-25', '21:30', 'Barcelona', 'Razzmatazz', 'DISPONIBLE'),
('SOTO ASA', 'estandar2.jpg', '2026-05-28', '20:00', 'Madrid', 'La Riviera', 'DISPONIBLE'),

-- PÁGINA 2
('SAIKO', 'estandar.jpg', '2026-06-01', '22:00', 'Granada', 'Plaza de Toros', 'DISPONIBLE'),
('DELLAFUENTE', 'estandar2.jpg', '2026-06-03', '21:00', 'Madrid', 'Bernabéu', 'DISPONIBLE'),
('PEPE Y VIZIO', 'estandar.jpg', '2026-06-05', '20:30', 'Sevilla', 'Cartuja Center', 'DISPONIBLE'),
('MORAD', 'estandar2.jpg', '2026-06-08', '22:00', 'Hospitalet', 'Sala Salamandra', 'DISPONIBLE'),
('CRVSH GALA', 'estandar.jpg', '2026-06-10', '23:30', 'Barcelona', 'Downtown', 'DISPONIBLE'),
('MYKE TOWERS', 'estandar2.jpg', '2026-06-12', '21:00', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('TRUENO', 'estandar.jpg', '2026-06-15', '20:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('ELADIO CARRIÓN', 'estandar2.jpg', '2026-06-18', '21:00', 'Valencia', 'Marina Norte', 'DISPONIBLE'),

-- PÁGINA 3
('FEID', 'estandar.jpg', '2026-06-20', '21:30', 'Madrid', 'Metropolitano', 'DISPONIBLE'),
('BIZARRAP', 'estandar2.jpg', '2026-06-22', '23:00', 'Ibiza', 'Ushuaïa', 'DISPONIBLE'),
('TINI', 'estandar.jpg', '2026-06-25', '20:00', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('DUKI', 'estandar2.jpg', '2026-06-28', '21:00', 'Madrid', 'Bernabéu', 'DISPONIBLE'),
('RBF 2026', 'estandar.jpg', '2026-07-01', '12:00', 'Barcelona', 'Playa Fórum', 'DISPONIBLE'),
('JAY WHEELER', 'estandar2.jpg', '2026-07-03', '20:30', 'Barcelona', 'Sant Jordi Club', 'DISPONIBLE'),
('MORA', 'estandar.jpg', '2026-07-05', '21:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('NICKI NICOLE', 'estandar2.jpg', '2026-07-08', '20:00', 'Barcelona', 'Razzmatazz', 'DISPONIBLE'),

-- PÁGINA 4
('ROSALÍA', 'estandar.jpg', '2026-07-10', '21:30', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('C. TANGANA', 'estandar2.jpg', '2026-07-12', '22:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('OZUNA', 'estandar.jpg', '2026-07-15', '21:00', 'Barcelona', 'Pacha', 'DISPONIBLE'),
('ANUEL AA', 'estandar2.jpg', '2026-07-18', '20:30', 'Madrid', 'Bernabéu', 'DISPONIBLE'),
('JHAYCO', 'estandar.jpg', '2026-07-20', '22:00', 'Sevilla', 'Estadio Olímpico', 'DISPONIBLE'),
('SECH', 'estandar2.jpg', '2026-07-22', '21:00', 'Valencia', 'Ciudad Artes', 'DISPONIBLE'),
('MANUEL TURIZO', 'estandar.jpg', '2026-07-25', '20:00', 'Malaga', 'Marenostrum', 'DISPONIBLE'),
('ARCANGEL', 'estandar2.jpg', '2026-07-28', '21:00', 'Bilbao', 'BEC', 'DISPONIBLE'),

-- PÁGINA 5
('KAROL G', 'estandar.jpg', '2026-08-01', '21:00', 'Madrid', 'Bernabéu', 'DISPONIBLE'),
('DADDY YANKEE', 'estandar2.jpg', '2026-08-03', '22:00', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('MALUMA', 'estandar.jpg', '2026-08-05', '21:30', 'Marbella', 'Starlite', 'DISPONIBLE'),
('PESO PLUMA', 'estandar2.jpg', '2026-08-08', '20:30', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('YOUNG MIKO', 'estandar.jpg', '2026-08-10', '21:00', 'Madrid', 'La Riviera', 'DISPONIBLE'),
('RYAN CASTRO', 'estandar2.jpg', '2026-08-12', '22:00', 'Barcelona', 'Shoko', 'DISPONIBLE'),
('BLESSD', 'estandar.jpg', '2026-08-15', '20:30', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('CHIMBALA', 'estandar2.jpg', '2026-08-18', '23:30', 'Tenerife', 'Papagayo', 'DISPONIBLE'),

-- PÁGINA 6
('AITANA', 'estandar.jpg', '2026-09-01', '21:00', 'Madrid', 'Bernabéu', 'DISPONIBLE'),
('LOLA INDIGO', 'estandar2.jpg', '2026-09-03', '20:30', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('NATY PELUSO', 'estandar.jpg', '2026-09-05', '21:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('PTAZETA', 'estandar2.jpg', '2026-09-08', '22:00', 'Gran Canaria', 'Arena', 'DISPONIBLE'),
('VILLANO ANTILLANO', 'estandar.jpg', '2026-09-10', '21:30', 'Barcelona', 'Razzmatazz', 'DISPONIBLE'),
('ZION Y LENNOX', 'estandar2.jpg', '2026-09-12', '23:00', 'Madrid', 'Fabrik', 'DISPONIBLE'),
('WISIN Y YANDEL', 'estandar.jpg', '2026-09-15', '21:00', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('DON OMAR', 'estandar2.jpg', '2026-09-18', '22:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),

-- PÁGINA 7
('J BALVIN', 'estandar.jpg', '2026-10-01', '21:30', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('FARRUKO', 'estandar2.jpg', '2026-10-03', '22:00', 'Madrid', 'Bernabéu', 'DISPONIBLE'),
('RAUW ALEJANDRO', 'estandar.jpg', '2026-10-05', '21:00', 'Barcelona', 'Pacha', 'DISPONIBLE'),
('TINY', 'estandar2.jpg', '2026-10-08', '23:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('OVI', 'estandar.jpg', '2026-10-10', '22:30', 'Barcelona', 'Sutton', 'DISPONIBLE'),
('BRYANT MYERS', 'estandar2.jpg', '2026-10-12', '21:00', 'Valencia', 'Marina', 'DISPONIBLE'),
('CHUCKY73', 'estandar.jpg', '2026-10-15', '23:00', 'Madrid', 'Shoko', 'DISPONIBLE'),
('EL ALFA', 'estandar2.jpg', '2026-10-18', '22:00', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),

-- PÁGINA 8
('OMAR MONTES', 'estandar.jpg', '2026-11-01', '20:30', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('RVFV', 'estandar2.jpg', '2026-11-03', '21:00', 'Almería', 'Recinto Ferial', 'DISPONIBLE'),
('MAIKEL DELACALLE', 'estandar.jpg', '2026-11-05', '22:00', 'Tenerife', 'Recinto Ferial', 'DISPONIBLE'),
('CRIS MJ', 'estandar2.jpg', '2026-11-08', '21:30', 'Barcelona', 'Razzmatazz', 'DISPONIBLE'),
('POLIMÁ WESTCOAST', 'estandar.jpg', '2026-11-10', '21:00', 'Madrid', 'La Riviera', 'DISPONIBLE'),
('PABLO CHILL-E', 'estandar2.jpg', '2026-11-12', '20:30', 'Sevilla', 'Sala Custom', 'DISPONIBLE'),
('KHEA', 'estandar.jpg', '2026-11-15', '21:00', 'Barcelona', 'Apolo', 'DISPONIBLE'),
('LIT KILLAH', 'estandar2.jpg', '2026-11-18', '20:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),

-- PÁGINA 9
('NATALIA LACUNZA', 'estandar.jpg', '2026-11-20', '20:30', 'Madrid', 'La Riviera', 'DISPONIBLE'),
('GUITAR_RICARDELAFUENTE', 'estandar2.jpg', '2026-11-22', '21:00', 'Barcelona', 'Apolo', 'DISPONIBLE'),
('SENA SENRA', 'estandar.jpg', '2026-11-25', '21:30', 'Vigo', 'Auditorio', 'DISPONIBLE'),
('RECALDE', 'estandar2.jpg', '2026-11-28', '22:00', 'Bilbao', 'Santana 27', 'DISPONIBLE'),
('ALVARO DIAZ', 'estandar.jpg', '2026-12-01', '21:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('RELS B - ESPECIAL', 'estandar2.jpg', '2026-12-03', '21:30', 'Barcelona', 'Palau Sant Jordi', 'DISPONIBLE'),
('TIAGO PZK', 'estandar.jpg', '2026-12-05', '22:00', 'Madrid', 'Bernabéu', 'DISPONIBLE'),
('FMK', 'estandar2.jpg', '2026-12-08', '20:00', 'Barcelona', 'Razzmatazz', 'DISPONIBLE'),

-- PÁGINA 10
('CA7RIEL', 'estandar.jpg', '2026-12-10', '21:00', 'Madrid', 'Sala But', 'DISPONIBLE'),
('DILLOM', 'estandar2.jpg', '2026-12-12', '21:30', 'Barcelona', 'Razzmatazz 2', 'DISPONIBLE'),
('YSY A', 'estandar.jpg', '2026-12-15', '22:00', 'Valencia', 'Sala Moon', 'DISPONIBLE'),
('DUKI - CIERRE', 'estandar2.jpg', '2026-12-18', '21:00', 'Madrid', 'Wizink Center', 'DISPONIBLE'),
('FIN DE AÑO', 'estandar.jpg', '2026-12-31', '23:30', 'Barcelona', 'Poble Espanyol', 'DISPONIBLE'),
('ESPECIAL REYES', 'estandar2.jpg', '2027-01-05', '23:00', 'Madrid', 'Fabrik', 'DISPONIBLE'),
('AFTERLIFE', 'estandar.jpg', '2027-01-10', '22:00', 'Barcelona', 'Fira Gran Via', 'DISPONIBLE'),
('CIRCOLOCO', 'estandar2.jpg', '2027-01-15', '18:00', 'Madrid', 'IFEMA', 'DISPONIBLE');
