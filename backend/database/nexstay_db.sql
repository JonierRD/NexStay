-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 17-12-2025 a las 03:04:13
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
-- Base de datos: `nexstay_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `ciudad_origen` varchar(100) DEFAULT NULL,
  `ciudad_destino` varchar(100) DEFAULT NULL,
  `profesion` varchar(100) DEFAULT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `habitacion_id` int(10) UNSIGNED DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `cedula`, `ciudad_origen`, `ciudad_destino`, `profesion`, `documento`, `correo`, `telefono`, `habitacion_id`, `direccion`, `creado_at`) VALUES
(29, 'Jonier', '1123324066', 'orito', 'orito', 'inge', 'CC', 'renodnchamorro@gmail.com', '3224999772', 3, 'barrio los alpes', '2025-12-16 00:59:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `cargo` enum('administrador','recepcionista','limpieza','mantenimiento','otro') DEFAULT 'otro',
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `documento`, `cargo`, `correo`, `telefono`, `horario`, `creado_at`) VALUES
(2, 'Juano Perez', '12345678', 'recepcionista', 'juan@example.com', '3220000000', '8am - 6pm', '2025-11-02 06:25:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(10) UNSIGNED NOT NULL,
  `reserva_id` int(11) DEFAULT NULL,
  `parqueadero_id` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `reserva_id`, `parqueadero_id`, `monto`, `metodo_pago`, `fecha`, `notas`) VALUES
(10, NULL, NULL, 250000.00, 'efectivo', '2025-12-01 05:00:00', 'Hospedaje directo sin reserva');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(20) NOT NULL,
  `tipo` enum('sencilla','doble','suite','otro') NOT NULL DEFAULT 'sencilla',
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('disponible','reservada','ocupada','mantenimiento') NOT NULL DEFAULT 'disponible',
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id`, `numero`, `tipo`, `descripcion`, `precio`, `estado`, `creado_at`) VALUES
(1, '101', 'sencilla', 'Individual', 100000.00, 'disponible', '2025-11-02 05:55:23'),
(2, '102', 'doble', 'Doble', 150000.00, 'disponible', '2025-11-02 05:55:23'),
(3, '103', 'suite', 'Suite', 250000.00, 'ocupada', '2025-11-02 05:55:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hospedajes`
--

CREATE TABLE `hospedajes` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `habitacion_id` int(10) UNSIGNED NOT NULL,
  `fecha_entrada` datetime NOT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `hospedajes`
--

INSERT INTO `hospedajes` (`id`, `cliente_id`, `habitacion_id`, `fecha_entrada`, `fecha_salida`, `creado_at`) VALUES
(7, 29, 3, '2025-12-16 01:59:51', NULL, '2025-12-16 00:59:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria` enum('mekato','bebida','aseo','lenceria') NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `precio_unitario` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` int(11) DEFAULT 5,
  `dias_mes` int(11) DEFAULT 30,
  `ventas_mes` int(11) DEFAULT 0,
  `total_existencia` int(11) GENERATED ALWAYS AS (`cantidad` - `ventas_mes`) STORED,
  `unidad` varchar(20) DEFAULT 'unidad',
  `actualizado_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `categoria`, `nombre`, `cantidad`, `precio_unitario`, `stock_minimo`, `dias_mes`, `ventas_mes`, `unidad`, `actualizado_at`) VALUES
(1, '', 'Café', 50, 2000.00, 10, 30, 15, 'unidad', '2025-11-02 07:31:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimientos`
--

CREATE TABLE `mantenimientos` (
  `id` int(10) UNSIGNED NOT NULL,
  `habitacion_id` int(10) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `estado` enum('pendiente','en_proceso','completado') DEFAULT 'pendiente',
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_solucion` timestamp NULL DEFAULT NULL,
  `empleado_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mantenimientos`
--

INSERT INTO `mantenimientos` (`id`, `habitacion_id`, `descripcion`, `estado`, `fecha_reporte`, `fecha_solucion`, `empleado_id`) VALUES
(1, 1, 'Fuga en el baño', 'pendiente', '2025-11-02 05:00:00', '2025-11-04 05:00:00', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parqueadero`
--

CREATE TABLE `parqueadero` (
  `id` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `placa` varchar(20) NOT NULL,
  `tipo_vehiculo` enum('carro','moto') NOT NULL,
  `fecha_entrada` datetime NOT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `tarifa` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parqueadero`
--

INSERT INTO `parqueadero` (`id`, `nombre_cliente`, `placa`, `tipo_vehiculo`, `fecha_entrada`, `fecha_salida`, `tarifa`, `observaciones`) VALUES
(4, 'Carlos Ramirez', 'ABC123', '', '2025-12-01 08:30:00', '2025-12-01 12:00:00', 15000.00, 'Cliente huesped, se quedó 4 horas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `habitacion_id` int(10) UNSIGNED NOT NULL,
  `fecha_entrada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('confirmada','checkin','checkout','cancelada') NOT NULL DEFAULT 'confirmada',
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva_servicio`
--

CREATE TABLE `reserva_servicio` (
  `id` int(10) UNSIGNED NOT NULL,
  `reserva_id` int(10) UNSIGNED NOT NULL,
  `servicio_id` int(10) UNSIGNED NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `disponible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `descripcion`, `precio`, `disponible`) VALUES
(1, 'Servicio de limpieza', 'Limpieza diaria de habitación', 50000.00, 1),
(2, 'Servicio de Limpieza', 'Limpieza de habitación', 20000.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `rol` enum('admin','recepcionista','otro') NOT NULL DEFAULT 'recepcionista',
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `nombre`, `correo`, `rol`, `creado_at`) VALUES
(6, 'Jonier', '$2b$12$/eZfA.fGtKYI7GV.HgGnX.8kORv0kk9BgybqzlmVrqu4RxAAmwmlG', 'Jonier Rendon', 'jonier@example.com', 'admin', '2025-11-18 00:54:48');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cliente_habitacion` (`habitacion_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reserva_id` (`reserva_id`);

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Indices de la tabla `hospedajes`
--
ALTER TABLE `hospedajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `habitacion_id` (`habitacion_id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habitacion_id` (`habitacion_id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `parqueadero`
--
ALTER TABLE `parqueadero`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `habitacion_id` (`habitacion_id`);

--
-- Indices de la tabla `reserva_servicio`
--
ALTER TABLE `reserva_servicio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reserva_id` (`reserva_id`),
  ADD KEY `servicio_id` (`servicio_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `hospedajes`
--
ALTER TABLE `hospedajes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `parqueadero`
--
ALTER TABLE `parqueadero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `reserva_servicio`
--
ALTER TABLE `reserva_servicio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_cliente_habitacion` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `hospedajes`
--
ALTER TABLE `hospedajes`
  ADD CONSTRAINT `hospedaje_cliente_fk` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hospedaje_habitacion_fk` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  ADD CONSTRAINT `mantenimientos_ibfk_1` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mantenimientos_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reserva_servicio`
--
ALTER TABLE `reserva_servicio`
  ADD CONSTRAINT `reserva_servicio_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_servicio_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
