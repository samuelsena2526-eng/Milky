-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 20-10-2026 a las 02:31:31
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
-- Base de datos: `milky`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos`
--

CREATE TABLE `carritos` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carritos`
--

INSERT INTO `carritos` (`id`, `usuario_id`, `creado_en`, `actualizado_en`) VALUES
(1, NULL, '2026-10-09 22:59:16', '2026-10-20 00:15:51'),
(2, 12, '2026-10-13 19:41:35', '2026-10-13 19:41:35'),
(3, 9, '2026-10-19 22:47:46', '2026-10-20 00:15:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_items`
--

CREATE TABLE `carrito_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `carrito_id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `agregado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carrito_items`
--

INSERT INTO `carrito_items` (`id`, `carrito_id`, `producto_id`, `cantidad`, `agregado_en`) VALUES
(1, 3, 2, 1, '2026-10-19 22:47:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrusel_imagenes`
--

CREATE TABLE `carrusel_imagenes` (
  `id` int(10) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `texto_alternativo` varchar(180) NOT NULL,
  `orden_visual` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carrusel_imagenes`
--

INSERT INTO `carrusel_imagenes` (`id`, `ruta`, `texto_alternativo`, `orden_visual`, `creado_en`, `creado_por`) VALUES
(1, 'vista/img/publiciadad/1.jpeg', 'Anuncio Milky de queso crema', 1, '2026-09-29 00:37:09', NULL),
(2, 'vista/img/publiciadad/2.jpg', 'Anuncio de productos Milky', 2, '2026-09-29 00:37:09', NULL),
(3, 'vista/img/publiciadad/3.jpg', 'Anuncio de producto natural Milky', 3, '2026-09-29 00:37:09', NULL),
(4, 'vista/img/publiciadad/4.jpeg', 'Anuncio de mantequilla Milky', 4, '2026-09-29 00:37:09', NULL),
(5, 'vista/img/publiciadad/5.jpeg', 'Anuncio de nuevo sabor Milky', 5, '2026-09-29 00:37:09', NULL),
(6, 'vista/img/publiciadad/6.jpeg', 'Anuncio de productos Milky', 6, '2026-09-29 00:37:09', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `pregunta` text NOT NULL,
  `respuesta` text DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `respondido_por` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descripcion` text NOT NULL,
  `imagen` varchar(255) NOT NULL DEFAULT 'producto.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `descripcion`, `imagen`) VALUES
(1, 'Leche Fresca', 6500.00, 'Leche entera, rica en calcio y sabor natural.', 'publiciadad/p.jpeg'),
(2, 'Yogur Natural', 5800.00, 'Yogur cremoso con fermentos naturales.', 'publiciadad/2.jpg'),
(3, 'Queso Campesino', 6800.00, 'Queso artesanal con textura suave y sabor tradicional.', 'publiciadad/5.jpeg'),
(4, 'Mantequilla', 9400.00, 'Mantequilla de alta calidad para cocinar y untar.', 'publiciadad/1.jpeg'),
(5, 'Leche sabor fresa macaron', 9900.00, 'Leche semidescremada con sabor a fresa y macaron, en presentación de 250 ml.', 'publiciadad/3.jpg'),
(6, 'Yogur con cereal', 4200.00, 'Yogur cremoso acompañado de cereal frutal y hojuelas crujientes.', 'publiciadad/4.jpeg'),
(7, 'Leche en polvo clásica', 24900.00, 'Leche entera en polvo fortificada con calcio, zinc, proteína y vitaminas.', 'publiciadad/6.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'Usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contraseña`, `rol`) VALUES
(8, 'susana', 'susanita@admin.com', '12344', 'Admin'),
(9, 'Mafe', 'Mafecita@admin.com', '12344', 'Admin'),
(12, 'Maria Angel', 'Maria_Angel@admin.com', '12344', 'Admin'),
(15, 'mariangel vergara', 'mariangel@gmail.com', '12345', 'Admin'),
(16, 'admin', 'admin', '$2y$10$I.HMZunsoiEJoYLBGUyDeurF48mDYS8Cz6xL3nNnLoR', 'admin'),
(17, 'joewound', 'dxrcfvgbuh@ftyvgubh', ' fghjk', 'Usuario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carritos_usuario` (`usuario_id`);

--
-- Indices de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carrito_items_producto` (`carrito_id`,`producto_id`),
  ADD KEY `fk_carrito_items_producto` (`producto_id`);

--
-- Indices de la tabla `carrusel_imagenes`
--
ALTER TABLE `carrusel_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carrusel_creado_por` (`creado_por`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_preguntas_usuario` (`usuario_id`),
  ADD KEY `fk_preguntas_respondido_por` (`respondido_por`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carritos`
--
ALTER TABLE `carritos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `carrusel_imagenes`
--
ALTER TABLE `carrusel_imagenes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD CONSTRAINT `fk_carritos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD CONSTRAINT `fk_carrito_items_carrito` FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carrito_items_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `carrusel_imagenes`
--
ALTER TABLE `carrusel_imagenes`
  ADD CONSTRAINT `fk_carrusel_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `fk_preguntas_respondido_por` FOREIGN KEY (`respondido_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_preguntas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
