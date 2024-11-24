-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2024 a las 22:53:34
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_VALIDATE_USER` (IN `p_username` VARCHAR(50), IN `p_password` VARCHAR(200))   BEGIN
    -- Buscar el usuario por su nombre de usuario
    SELECT id_user, user_namee, id_range, passwordd
    FROM users
    WHERE user_namee = p_username
    LIMIT 1;
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
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `colors`
--

INSERT INTO `colors` (`id_color`, `color`) VALUES
(1, 'Negro'),
(2, 'Blanco'),
(3, 'Azul rey'),
(4, 'Azul celeste'),
(5, 'Rojo'),
(6, 'Vino'),
(7, 'Naranja'),
(8, 'Verde militar'),
(9, 'Dorado'),
(10, 'Amarillo'),
(11, 'Verde'),
(12, 'Rosa'),
(13, 'Menta'),
(14, 'Fucsia'),
(15, 'Gris');

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
(12, 1, 3, 'test procedimiento', 9999.00, 'test de procedimiento e insercion con formulario', 'src/images/uploads/productos/6740f440db731_zapato1.jpeg', 'src/images/uploads/productos/6740f440dc266_zapato2.jpeg', 'src/images/uploads/productos/6740f440dcc6c_zapato3.jpeg', 'src/images/uploads/productos/6740f440dd3d5_zapato4.jpeg');

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
(8, 12, 1, 1, 87, 456),
(9, 12, 7, 1, 576, 34),
(10, 12, 2, 7, 96, 3547);

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
(1, 'gem', 'gema', 'vega', 1, '123', 'gemarubio@gmail.com'),
(2, 'test de cliente', 'Emanuel', 'Vazquez', 3, '$2y$10$bjIhna2wQYM0TE5MMBGdF.HsGGLLKykLpjSid9i/n3wRivwu4vWra', 'emanuel.vazquez@uttn.mx');

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
  MODIFY `id_shoe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `shoes_variations`
--
ALTER TABLE `shoes_variations`
  MODIFY `id_varition` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id_size` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
