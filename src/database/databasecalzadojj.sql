-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2024 a las 08:59:21
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
-- Base de datos: `databasecalzadojj`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUser` (IN `p_id_user` INT)   BEGIN
    DELETE FROM users WHERE id_user = p_id_user;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertUser` (IN `p_user_name` VARCHAR(50), IN `p_name` VARCHAR(50), IN `p_last_name` VARCHAR(50), IN `p_id_range` INT, IN `p_password` VARCHAR(200), IN `p_email` VARCHAR(70))   BEGIN
    INSERT INTO users (user_namee, namee, last_name, id_range, passwordd, email_address)
    VALUES (p_user_name, p_name, p_last_name, p_id_range, p_password, p_email);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_DELETE_PRODUCT` (IN `p_id_shoe` INT)   BEGIN
    DELETE FROM shoes_variations WHERE id_shoe = p_id_shoe;
    DELETE FROM shoes WHERE id_shoe = p_id_shoe;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_DELETE_VARIATION` (IN `p_id_varition` INT)   BEGIN
    DELETE FROM shoes_variations WHERE id_varition = p_id_varition;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_GET_PRODUCTS` ()   BEGIN
    SELECT 
        s.id_shoe,
        b.brands AS brand,
        g.genre AS gender,
        s.model_name,
        s.price,
        s.descriptionn AS description,
        s.img_main,
        s.img_profile,
        s.img_front,
        s.img_rear
    FROM 
        shoes s
    JOIN 
        brands b ON s.id_brand = b.id_brand
    JOIN 
        genres g ON s.id_genre = g.id_genre;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_GET_PRODUCT_BY_ID` (IN `p_id_shoe` INT)   BEGIN
    -- Obtener los datos del zapato
    SELECT 
        s.id_shoe,
        s.id_brand,
        s.id_genre,
        s.model_name,
        s.price,
        s.descriptionn,
        s.img_main,
        s.img_profile,
        s.img_front,
        s.img_rear,
        b.brands AS brand_name,
        g.genre AS genre_name
    FROM 
        shoes s
    JOIN 
        brands b ON s.id_brand = b.id_brand
    JOIN 
        genres g ON s.id_genre = g.id_genre
    WHERE 
        s.id_shoe = p_id_shoe;

    -- Obtener las variaciones del zapato
    SELECT 
        sv.id_varition,
        sv.id_size,
        sz.sizeMX AS size_name,
        sv.id_color,
        c.color AS color_name,
        sv.stock_local,
        sv.stock_tianguis
    FROM 
        shoes_variations sv
    JOIN 
        sizes sz ON sv.id_size = sz.id_size
    JOIN 
        colors c ON sv.id_color = c.id_color
    WHERE 
        sv.id_shoe = p_id_shoe;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_GET_VARIATIONS` (IN `p_id_shoe` INT)   BEGIN
    SELECT 
        sv.id_varition,
        sz.sizeMX AS size,
        c.color,
        sv.stock_local,
        sv.stock_tianguis
    FROM 
        shoes_variations sv
    JOIN 
        sizes sz ON sv.id_size = sz.id_size
    JOIN 
        colors c ON sv.id_color = c.id_color
    WHERE 
        sv.id_shoe = p_id_shoe;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_INSERT_NEW_PRODUCT` (IN `p_id_brand` INT, IN `p_model_name` VARCHAR(50), IN `p_price` DECIMAL(10,2), IN `p_description` VARCHAR(100), IN `p_img_main` VARCHAR(300), IN `p_img_profile` VARCHAR(300), IN `p_img_front` VARCHAR(300), IN `p_img_rear` VARCHAR(300), IN `p_id_genre` INT, IN `p_variations` JSON)   BEGIN
    -- Declarar variables
    DECLARE last_shoe_id INT;
    DECLARE variation_count INT;
    DECLARE i INT DEFAULT 0;

    -- Insertar en la tabla shoes
    INSERT INTO shoes (
        id_brand, id_genre, model_name, price, descriptionn, 
        img_main, img_profile, img_front, img_rear
    ) VALUES (
        p_id_brand, p_id_genre, p_model_name, p_price, p_description, 
        p_img_main, p_img_profile, p_img_front, p_img_rear
    );
    SET last_shoe_id = LAST_INSERT_ID();

    -- Contar el número de variaciones en el JSON
    SET variation_count = JSON_LENGTH(p_variations);

    -- Ciclo para procesar cada variación
    WHILE i < variation_count DO
        -- Insertar en la tabla shoes_variations
        INSERT INTO shoes_variations (
            id_shoe, id_size, id_color, stock_local, stock_tianguis
        ) VALUES (
            last_shoe_id,
            JSON_UNQUOTE(JSON_EXTRACT(p_variations, CONCAT('$[', i, '].id_size'))),
            JSON_UNQUOTE(JSON_EXTRACT(p_variations, CONCAT('$[', i, '].id_color'))),
            JSON_UNQUOTE(JSON_EXTRACT(p_variations, CONCAT('$[', i, '].stock_local'))),
            JSON_UNQUOTE(JSON_EXTRACT(p_variations, CONCAT('$[', i, '].stock_tianguis')))
        );

        -- Incrementar el índice
        SET i = i + 1;
    END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTER_NEW_USER` (IN `p_username` VARCHAR(50), IN `p_name` VARCHAR(50), IN `p_surname` VARCHAR(50), IN `p_email` VARCHAR(70), IN `p_password` VARCHAR(200))   BEGIN
    -- Verificar si el nombre de usuario ya existe
    IF EXISTS (SELECT 1 FROM users WHERE user_namee = p_username) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El nombre de usuario ya está en uso.';
    ELSE
        -- Insertar el nuevo usuario con el rango "Cliente" (id_range = 3)
        INSERT INTO users (
            user_namee, namee, last_name, id_range, passwordd, email_address
        ) VALUES (
            p_username, p_name, p_surname, 3, p_password, p_email
        );
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UPDATE_PRODUCT` (IN `p_id_shoe` INT, IN `p_id_brand` INT, IN `p_id_genre` INT, IN `p_model_name` VARCHAR(50), IN `p_price` DECIMAL(10,2), IN `p_description` VARCHAR(100), IN `p_img_main` VARCHAR(300), IN `p_img_profile` VARCHAR(300), IN `p_img_front` VARCHAR(300), IN `p_img_rear` VARCHAR(300))   BEGIN
    UPDATE shoes
    SET 
        id_brand = p_id_brand,
        id_genre = p_id_genre,
        model_name = p_model_name,
        price = p_price,
        descriptionn = p_description,
        img_main = p_img_main,
        img_profile = p_img_profile,
        img_front = p_img_front,
        img_rear = p_img_rear
    WHERE id_shoe = p_id_shoe;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UPDATE_VARIATION` (IN `p_id_varition` INT, IN `p_id_size` INT, IN `p_id_color` INT, IN `p_stock_local` INT, IN `p_stock_tianguis` INT)   BEGIN
    UPDATE shoes_variations
    SET 
        id_size = p_id_size,
        id_color = p_id_color,
        stock_local = p_stock_local,
        stock_tianguis = p_stock_tianguis
    WHERE id_varition = p_id_varition;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_VALIDATE_USER` (IN `p_username` VARCHAR(50), IN `p_password` VARCHAR(200))   BEGIN
    -- Buscar el usuario por su nombre de usuario
    SELECT id_user, user_namee, id_range, passwordd
    FROM users
    WHERE user_namee = p_username
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUser` (IN `p_id_user` INT, IN `p_user_name` VARCHAR(50), IN `p_name` VARCHAR(50), IN `p_last_name` VARCHAR(50), IN `p_id_range` INT, IN `p_password` VARCHAR(200), IN `p_email` VARCHAR(70))   BEGIN
    UPDATE users
    SET user_namee = p_user_name,
        namee = p_name,
        last_name = p_last_name,
        id_range = p_id_range,
        passwordd = p_password,
        email_address = p_email
    WHERE id_user = p_id_user;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `brands`
--

CREATE TABLE `brands` (
  `id_brand` int(11) NOT NULL,
  `brands` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `brands`
--

INSERT INTO `brands` (`id_brand`, `brands`) VALUES
(1, 'Nike'),
(2, 'Adidas'),
(3, 'Puma'),
(4, 'Converse'),
(5, 'Guess'),
(6, 'Tommy'),
(7, 'Gucci'),
(8, 'Balenciaga');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colors`
--

CREATE TABLE `colors` (
  `id_color` int(11) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `color_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `colors`
--

INSERT INTO `colors` (`id_color`, `color`, `color_code`) VALUES
(1, 'Negro', '#000000'),
(2, 'Blanco', '#FFFFFF'),
(3, 'Azul rey', '#0033A0'),
(4, 'Azul celeste', '#87CEEB'),
(5, 'Rojo', '#FF0000'),
(6, 'Vino', '#800020'),
(7, 'Naranja', '#FFA500'),
(8, 'Verde militar', '#4B5320'),
(9, 'Dorado', '#FFD700'),
(10, 'Amarillo', '#FFFF00'),
(11, 'Verde', '#008000'),
(12, 'Rosa', '#FFC0CB'),
(13, 'Menta', '#98FF98'),
(14, 'Fucsia', '#FF00FF'),
(15, 'Gris', '#808080');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genres`
--

CREATE TABLE `genres` (
  `id_genre` int(11) NOT NULL,
  `genre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genres`
--

INSERT INTO `genres` (`id_genre`, `genre`) VALUES
(1, 'Hombre'),
(2, 'Mujer'),
(3, 'Unisex');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventorys`
--

CREATE TABLE `inventorys` (
  `id_inventory` int(11) NOT NULL,
  `id_variation` int(11) DEFAULT NULL,
  `quantity_local` int(11) DEFAULT NULL,
  `quantity_tianguis` int(11) DEFAULT NULL,
  `locationn` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_operations`
--

CREATE TABLE `log_operations` (
  `id_log` int(11) NOT NULL,
  `descriptionn` varchar(255) DEFAULT NULL,
  `operation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ranges`
--

CREATE TABLE `ranges` (
  `id_range` int(11) NOT NULL,
  `rangee` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ranges`
--

INSERT INTO `ranges` (`id_range`, `rangee`) VALUES
(1, 'Administrador'),
(2, 'Empleado'),
(3, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id_sale` int(11) NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `datee` date DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `fk_shoe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shoes`
--

CREATE TABLE `shoes` (
  `id_shoe` int(11) NOT NULL,
  `id_brand` int(11) DEFAULT NULL,
  `id_genre` int(11) DEFAULT NULL,
  `model_name` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `descriptionn` varchar(100) DEFAULT NULL,
  `img_main` varchar(300) DEFAULT NULL,
  `img_profile` varchar(300) DEFAULT NULL,
  `img_front` varchar(300) DEFAULT NULL,
  `img_rear` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `shoes`
--

INSERT INTO `shoes` (`id_shoe`, `id_brand`, `id_genre`, `model_name`, `price`, `descriptionn`, `img_main`, `img_profile`, `img_front`, `img_rear`) VALUES
(12, 1, 3, 'test procedimiento', 9999.00, 'test de procedimiento e insercion con formulario', 'C:\\xampp\\htdocs\\src\\images\\uploads674272ecbdd9c_scrappy.jpg', 'C:\\xampp\\htdocs\\src\\images\\uploads674272ecbdd9c_scrappy.jpg', 'C:\\xampp\\htdocs\\src\\images\\uploads674272ecbdd9c_scrappy.jpg', 'C:\\xampp\\htdocs\\src\\images\\uploads674272ecbdd9c_scrappy.jpg'),
(13, 1, 1, 'Air Max 2024', 1500.00, 'Comodidad y estilo para correr.', NULL, NULL, NULL, NULL),
(14, 2, 2, 'Ultraboost X', 1800.00, 'Innovación para el máximo rendimiento.', NULL, NULL, NULL, NULL),
(15, 3, 3, 'RS-X Hard Drive', 1400.00, 'Estilo futurista y comodidad.', NULL, NULL, NULL, NULL),
(16, 4, 3, 'Chuck Taylor Classic', 1300.00, 'Un clásico que nunca pasa de moda.', NULL, NULL, NULL, NULL),
(17, 5, 2, 'Guess Active Sneakers', 2000.00, 'Estilo urbano con un toque de lujo.', NULL, NULL, NULL, NULL),
(18, 6, 1, 'Tommy Street Runner', 1600.00, 'Calzado deportivo con un diseño moderno.', NULL, NULL, NULL, NULL),
(19, 7, 3, 'Gucci Evolution', 7500.00, 'Lujo y comodidad en un solo modelo.', NULL, NULL, NULL, NULL),
(20, 8, 1, 'Balenciaga Dynamics', 8500.00, 'Un diseño único para destacar.', NULL, NULL, NULL, NULL),
(21, 1, 2, 'Zoom Fly Elite', 2200.00, 'Perfectos para maratones y largas distancias.', NULL, NULL, NULL, NULL),
(22, 2, 3, 'Superstar Classic', 1200.00, 'Diseño icónico que trasciende generaciones.', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shoes_variations`
--

CREATE TABLE `shoes_variations` (
  `id_varition` int(11) NOT NULL,
  `id_shoe` int(11) DEFAULT NULL,
  `id_size` int(11) DEFAULT NULL,
  `id_color` int(11) DEFAULT NULL,
  `stock_local` int(11) NOT NULL,
  `stock_tianguis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `shoes_variations`
--

INSERT INTO `shoes_variations` (`id_varition`, `id_shoe`, `id_size`, `id_color`, `stock_local`, `stock_tianguis`) VALUES
(10, 12, 2, 7, 96, 3547),
(20, 13, 9, 1, 20, 10),
(21, 13, 10, 2, 25, 15),
(22, 14, 7, 3, 18, 12),
(23, 14, 8, 4, 20, 10),
(24, 15, 11, 5, 22, 14),
(25, 15, 12, 6, 19, 11),
(26, 16, 9, 7, 30, 20),
(27, 16, 10, 8, 25, 15),
(28, 17, 11, 9, 15, 8),
(29, 17, 12, 10, 12, 6),
(30, 18, 9, 11, 10, 5),
(31, 18, 10, 12, 8, 4),
(32, 19, 11, 13, 5, 3),
(33, 19, 12, 14, 4, 2),
(34, 20, 9, 1, 15, 8),
(35, 20, 10, 2, 10, 5),
(36, 21, 11, 3, 20, 10),
(37, 21, 12, 4, 18, 9),
(38, 22, 9, 5, 12, 6),
(39, 22, 10, 6, 10, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sizes`
--

CREATE TABLE `sizes` (
  `id_size` int(11) NOT NULL,
  `sizeMX` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sizes`
--

INSERT INTO `sizes` (`id_size`, `sizeMX`) VALUES
(1, '22'),
(2, '22.5'),
(3, '23'),
(4, '23.5'),
(5, '24'),
(6, '24.5'),
(7, '25'),
(8, '25.5'),
(9, '26'),
(10, '26.5'),
(11, '27'),
(12, '27.5'),
(13, '28'),
(14, '28.5'),
(15, '29'),
(16, '29.5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `user_namee` varchar(50) DEFAULT NULL,
  `namee` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `id_range` int(11) NOT NULL,
  `passwordd` varchar(200) DEFAULT NULL,
  `email_address` varchar(70) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_user`, `user_namee`, `namee`, `last_name`, `id_range`, `passwordd`, `email_address`) VALUES
(5, 'admin1', 'admin', 'admin', 1, '$2y$10$tpUSpC/kp/GVR4akLuURx.XOWktb/VKzBJQhURpi7W5tBFcoVRh46', 'admin@gmail.com'),
(6, 'gema_admin', 'Gema', 'Admin', 1, 'password123', 'gema.admin@example.com'),
(7, 'juan_admin', 'Juan', 'Admin', 1, 'password123', 'juan.admin@example.com'),
(8, 'employee1', 'Pedro', 'Lopez', 2, 'password123', 'pedro.lopez@example.com'),
(9, 'employee2', 'Maria', 'Gomez', 2, 'password123', 'maria.gomez@example.com'),
(10, 'employee3', 'Carlos', 'Hernandez', 2, 'password123', 'carlos.hernandez@example.com'),
(11, 'employee4', 'Lucia', 'Martinez', 2, 'password123', 'lucia.martinez@example.com'),
(12, 'employee5', 'Jorge', 'Perez', 2, 'password123', 'jorge.perez@example.com'),
(13, 'client1', 'Ana', 'Rodriguez', 3, 'password123', 'ana.rodriguez@example.com'),
(14, 'client2', 'Luis', 'Garcia', 3, 'password123', 'luis.garcia@example.com'),
(15, 'client3', 'Sofia', 'Ramirez', 3, 'password123', 'sofia.ramirez@example.com'),
(16, 'client4', 'Miguel', 'Sanchez', 3, 'password123', 'miguel.sanchez@example.com'),
(17, 'client5', 'Laura', 'Vargas', 3, 'password123', 'laura.vargas@example.com'),
(18, 'client6', 'Daniel', 'Gutierrez', 3, 'password123', 'daniel.gutierrez@example.com'),
(19, 'client7', 'Isabel', 'Mendoza', 3, 'password123', 'isabel.mendoza@example.com'),
(20, 'client8', 'Ricardo', 'Ortega', 3, 'password123', 'ricardo.ortega@example.com'),
(21, 'client9', 'Carmen', 'Rivera', 3, 'password123', 'carmen.rivera@example.com'),
(22, 'client10', 'Oscar', 'Nunez', 3, 'password123', 'oscar.nunez@example.com'),
(23, 'client11', 'Elena', 'Blanco', 3, 'password123', 'elena.blanco@example.com'),
(24, 'client12', 'Francisco', 'Cruz', 3, 'password123', 'francisco.cruz@example.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id_brand`);

--
-- Indices de la tabla `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id_color`);

--
-- Indices de la tabla `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id_genre`);

--
-- Indices de la tabla `inventorys`
--
ALTER TABLE `inventorys`
  ADD PRIMARY KEY (`id_inventory`),
  ADD KEY `id_variation` (`id_variation`);

--
-- Indices de la tabla `log_operations`
--
ALTER TABLE `log_operations`
  ADD PRIMARY KEY (`id_log`);

--
-- Indices de la tabla `ranges`
--
ALTER TABLE `ranges`
  ADD PRIMARY KEY (`id_range`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id_sale`),
  ADD KEY `fk_user` (`fk_user`),
  ADD KEY `fk_shoe` (`fk_shoe`);

--
-- Indices de la tabla `shoes`
--
ALTER TABLE `shoes`
  ADD PRIMARY KEY (`id_shoe`),
  ADD KEY `id_brand` (`id_brand`),
  ADD KEY `id_genre` (`id_genre`);

--
-- Indices de la tabla `shoes_variations`
--
ALTER TABLE `shoes_variations`
  ADD PRIMARY KEY (`id_varition`),
  ADD KEY `id_shoe` (`id_shoe`),
  ADD KEY `id_size` (`id_size`),
  ADD KEY `id_color` (`id_color`);

--
-- Indices de la tabla `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id_size`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_range` (`id_range`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `brands`
--
ALTER TABLE `brands`
  MODIFY `id_brand` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `colors`
--
ALTER TABLE `colors`
  MODIFY `id_color` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `genres`
--
ALTER TABLE `genres`
  MODIFY `id_genre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `inventorys`
--
ALTER TABLE `inventorys`
  MODIFY `id_inventory` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log_operations`
--
ALTER TABLE `log_operations`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ranges`
--
ALTER TABLE `ranges`
  MODIFY `id_range` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id_sale` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `shoes`
--
ALTER TABLE `shoes`
  MODIFY `id_shoe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `shoes_variations`
--
ALTER TABLE `shoes_variations`
  MODIFY `id_varition` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id_size` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `inventorys`
--
ALTER TABLE `inventorys`
  ADD CONSTRAINT `inventorys_ibfk_1` FOREIGN KEY (`id_variation`) REFERENCES `shoes_variations` (`id_varition`);

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`fk_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`fk_shoe`) REFERENCES `shoes` (`id_shoe`);

--
-- Filtros para la tabla `shoes`
--
ALTER TABLE `shoes`
  ADD CONSTRAINT `shoes_ibfk_1` FOREIGN KEY (`id_brand`) REFERENCES `brands` (`id_brand`),
  ADD CONSTRAINT `shoes_ibfk_2` FOREIGN KEY (`id_genre`) REFERENCES `genres` (`id_genre`);

--
-- Filtros para la tabla `shoes_variations`
--
ALTER TABLE `shoes_variations`
  ADD CONSTRAINT `shoes_variations_ibfk_1` FOREIGN KEY (`id_shoe`) REFERENCES `shoes` (`id_shoe`),
  ADD CONSTRAINT `shoes_variations_ibfk_2` FOREIGN KEY (`id_size`) REFERENCES `sizes` (`id_size`),
  ADD CONSTRAINT `shoes_variations_ibfk_3` FOREIGN KEY (`id_color`) REFERENCES `colors` (`id_color`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_range`) REFERENCES `ranges` (`id_range`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
