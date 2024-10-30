CREATE DATABASE DB_JJ_Inventoryv1

USE DB_JJ_Inventoryv1

--PRODUCTOS--

--Tabla de Tallas
CREATE TABLE tallas (id_talla INT PRIMARY KEY IDENTITY, tallaMX VARCHAR (10), tallaEUA VARCHAR (10))

--Tabla de Colores
CREATE TABLE colores (id_color INT PRIMARY KEY IDENTITY, color NVARCHAR (50))

--Tabla de Generos
CREATE TABLE generos (id_genero INT PRIMARY KEY IDENTITY, genero NVARCHAR (50))

--Tabla de Tenis (Productos principales)
CREATE TABLE tenis (id_tenis INT PRIMARY KEY IDENTITY, marca NVARCHAR (50), nombre_modelo NVARCHAR (50), precio INT,
descripcion NVARCHAR (100), img_principal NVARCHAR (300), img_frontal NVARCHAR (300), img_pfl_dcho NVARCHAR (300), img_pfl_izq NVARCHAR (300))

--Tabla para almacenar las combinaciones de variaciones de cada tenis.
CREATE TABLE tenis_estilos (
    id_tenis INT,     -- Referencia a la tabla de tenis (principal)
    id_talla INT,        -- Referencia a la tabla de tallas
    id_color INT,        -- Referencia a la tabla de colores
    id_genero INT,       -- Referencia a la tabla de g�nero
    stock INT,           -- Cantidad disponible
    PRIMARY KEY (id_tenis, id_talla, id_color, id_genero),
    FOREIGN KEY (id_tenis) REFERENCES tenis (id_tenis),
    FOREIGN KEY (id_talla) REFERENCES tallas(id_talla),
    FOREIGN KEY (id_color) REFERENCES colores(id_color),
    FOREIGN KEY (id_genero) REFERENCES generos(id_genero)
)

--USUARIOS Y MODIFICACIONES--

--Tabla de Usuarios
CREATE TABLE usuarios (id_usuario INT PRIMARY KEY IDENTITY, nombre NVARCHAR (50), apellido NVARCHAR (50),
rango NVARCHAR (70) NOT NULL,
contrase�a VARCHAR(200))

ALTER TABLE usuarios ADD correo NVARCHAR (70)

--Tabla de Ventas
CREATE TABLE ventas (id_ventas INT PRIMARY KEY IDENTITY, cantidad DECIMAL (10,2), fecha DATE,
fk_usuario INT CONSTRAINT fk_usuario FOREIGN KEY REFERENCES usuarios (id_usuario),
fk_tenis INT CONSTRAINT fk_tenis FOREIGN KEY REFERENCES tenis (id_tenis))

--Tabla de Inventarios
CREATE TABLE inventarios (
    id_inventario INT PRIMARY KEY IDENTITY,
    cantidad INT,
    ubicacion VARCHAR(100),
	fK_tenis INT FOREIGN KEY (fk_tenis) REFERENCES tenis(id_tenis))

--INSERCCI�N DE DATOS--

--Datos tallas
INSERT INTO tallas(tallaMX,tallaEUA) VALUES
('22', '5'), --1
('22.5','5.5'), --2
('23', '6'), --3
('23.5', '6.5'), --4
('24', '7'), --5
('24.5', '7.5'), --6
('25', '8'), --7
('25.5', '8.5'), --8
('26', '9'), --9
('26.5', '9.5'), --10
('27', '10'), --11
('27.5', '10.5'), --12
('28', '11'), --13
('28.5', '11.5'), --14
('29', '12'), --15
('29.5','12.5') --16

SELECT * FROM tallas
--Datos colores
INSERT INTO colores (color) VALUES
('Negro'), --1
('Blanco'), --2
('Azul rey'), --3
('Azul celeste'), --4
('Rojo'), --5
('Vino'), --6
('Naranja'), --7
('Verde militar'), --8
('Dorado'), --9
('Amarillo'), --10
('Verde'), --11
('Rosa'), --12
('Menta'), --13
('Fucsia'), --14
('Gris') --15

SELECT * FROM colores

--Datos generos

INSERT INTO generos (genero) VALUES 
('Hombre'),
('Mujer'),
('Unisex')

SELECT * FROM generos

--Datos de la tabla principal

INSERT INTO tenis (marca,nombre_modelo, precio,descripcion) VALUES
--('Nike', 'Nike F1 - Negro Total', 450, 'Tenis Nike casual, modelo F1 negro total'),
('Adidas', 'Adidas - Predator', 450, 'Tenis Adidas f�tbol tachones, modelo predator, color rojo y negro'),
('Puma', 'Puma - 3750', 500, 'Tenis Puma, modelo 3750, color negro y rojo'),
('Converse', 'Converse - 1503', 400, 'Tenis Converse bota, modelo 1503, color blanco')

SELECT * FROM tenis

--Datos para insertar combinaciones de variaciones para los tenis

INSERT INTO tenis_estilos (id_tenis, id_talla, id_color, id_genero, stock) VALUES
--(1, 8, 1, 1, 30),
--(1, 9, 1, 1, 25),
(2, 8, 5, 1, 15),
(2, 8, 1, 1, 10),
(2, 9, 1, 1, 15),
(2, 9, 5, 1, 15),
(2, 13, 1, 1, 15),
(2, 13, 5, 1, 15),
(3, 10, 1, 1, 20),
(3, 10, 5, 1, 20),
(3, 12, 1, 1, 20),
(3, 12, 5, 1, 20),
(3, 14, 1, 1, 20),
(3, 14, 5, 1, 20),
(4, 5, 2, 3, 25),
(4, 7, 2, 3, 20)

SELECT * FROM tenis_estilos

SELECT tn.marca, tn.nombre_modelo,tn.precio, tn.descripcion, t.tallaMX, t.tallaEUA, c.color, g.genero, te.stock
FROM tenis tn
JOIN tenis_estilos te ON tn.id_tenis = te.id_tenis
JOIN tallas t ON te.id_talla = t.id_talla
JOIN colores c ON te.id_color = c.id_color
JOIN generos g ON te.id_genero = g.id_genero
WHERE tn.id_tenis = 2;


-- <!-- version 0.0.5 -->