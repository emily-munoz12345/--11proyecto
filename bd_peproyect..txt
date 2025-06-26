-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-06-2025 a las 01:50:23
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
-- Base de datos: `bd_proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(50) NOT NULL,
  `correo_cliente` varchar(70) NOT NULL,
  `telefono_cliente` int(30) NOT NULL,
  `direccion_cliente` text NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notas_cliente` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `correo_cliente`, `telefono_cliente`, `direccion_cliente`, `fecha_registro`, `notas_cliente`) VALUES
(1, 'María Hernandez', 'mariah@gmail.com', 320465189, 'cr 8 N8-4', '2025-06-10 23:06:18', '...'),
(2, 'José Felipe', 'jfelipe@gmail.com', 311548659, 'barrio el triunfo', '2025-06-10 23:07:24', '...'),
(3, 'Patricia Gomez', 'pati@gmail.com', 323456875, 'cr 5 # 2-4', '2025-06-10 23:08:44', '...');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_vehiculo`
--

CREATE TABLE `cliente_vehiculo` (
  `id_cliente` int(11) NOT NULL,
  `id_vehiculo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente_vehiculo`
--

INSERT INTO `cliente_vehiculo` (`id_cliente`, `id_vehiculo`) VALUES
(1, 2),
(2, 3),
(3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

CREATE TABLE `cotizaciones` (
  `id_cotizacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vehiculo` int(11) NOT NULL,
  `fecha_cotizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `subtotal_cotizacion` decimal(10,0) NOT NULL,
  `iva` decimal(10,0) NOT NULL,
  `total_cotizacion` decimal(10,0) NOT NULL,
  `estado_cotizacion` enum('Pendiente','Aprobado','Rechazada','Completada') NOT NULL DEFAULT 'Pendiente',
  `notas_cotizacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id_cotizacion`, `id_usuario`, `id_cliente`, `id_vehiculo`, `fecha_cotizacion`, `subtotal_cotizacion`, `iva`, `total_cotizacion`, `estado_cotizacion`, `notas_cotizacion`) VALUES
(1, 1, 2, 3, '2025-06-10 23:34:53', 100000, 190, 100190, 'Pendiente', '-...'),
(2, 1, 1, 3, '2025-06-10 23:36:42', 60000, 11400, 71400, 'Pendiente', '-...'),
(3, 2, 1, 3, '2025-06-10 23:37:29', 20000, 3800, 23800, 'Pendiente', '-...\r\n');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id_material` int(11) NOT NULL,
  `nombre_material` varchar(70) NOT NULL,
  `descripcion_material` text NOT NULL,
  `precio_metro` decimal(10,0) NOT NULL,
  `stock_material` int(11) NOT NULL,
  `categoria_material` varchar(50) NOT NULL,
  `proveedor_material` varchar(50) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_material`, `nombre_material`, `descripcion_material`, `precio_metro`, `stock_material`, `categoria_material`, `proveedor_material`, `fecha_actualizacion`) VALUES
(1, 'Vinilcuero', 'Es un material sintético, generalmente de vinilo, que imita la apariencia y textura del cuero natural, pero ofrece ventajas como mayor durabilidad, facilidad de limpieza y menor costo', 47700, 1, 'Cueros', 'Calypso', '2025-06-10 23:20:19'),
(2, 'Cuerotex', 'Es una tela sintética, a menudo compuesta de poliéster con una capa de PVC o poliuretano para imitar la apariencia y textura del cuero', 19600, 5, 'Cueros', 'Calypso', '2025-06-10 23:21:58'),
(3, 'Prana', 'material sintético tipo cuero, popularmente conocido como cuero sintético o cuerina, ampliamente utilizado en tapicería de muebles debido a su resistencia, durabilidad y facilidad de limpieza', 38600, 2, 'Cueros', 'Calypso', '2025-06-10 23:24:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descipcion_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descipcion_rol`) VALUES
(1, 'Administrador', 'Acceso completo al sistema'),
(2, 'Tecnico', 'Personal encargado de realizar los trabajos'),
(3, 'Vendedor', 'Personal encargado de ventas y cotizaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(50) NOT NULL,
  `descripcion_servicio` text NOT NULL,
  `precio_servicio` decimal(10,0) NOT NULL,
  `tiempo_estimado` varchar(50) NOT NULL,
  `categoria_servicio` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `descripcion_servicio`, `precio_servicio`, `tiempo_estimado`, `categoria_servicio`) VALUES
(1, 'lavado general ', 'incluye la limpieza del exterior e interior del vehículo, con el objetivo principal de eliminar la suciedad, polvo y manchas, dejando el auto limpio y presentable', 280000, '3 días', 'lavado general'),
(2, 'cambio de modulos', '...', 100000, '1 día', 'modulos'),
(3, 'tapizado de volante', 'con costura de color rojo y doble', 70000, 'medio día', 'volantes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajos`
--

CREATE TABLE `trabajos` (
  `id_trabajos` int(11) NOT NULL,
  `id_cotizacion` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Pendiente','Entregado','En progreso','Cancelado') NOT NULL,
  `notas` text NOT NULL,
  `fotos` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `username_usuario` varchar(50) NOT NULL,
  `contrasena_usuario` varchar(70) NOT NULL,
  `nombre_completo` varchar(70) NOT NULL,
  `correo_usuario` varchar(70) NOT NULL,
  `telefono_ususario` int(20) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activo_usuario` enum('Activo','Inactivo') NOT NULL,
  `ultima_actividad` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `username_usuario`, `contrasena_usuario`, `nombre_completo`, `correo_usuario`, `telefono_ususario`, `fecha_creacion`, `activo_usuario`, `ultima_actividad`) VALUES
(1, 1, 'Alonso', 'alonso12345', 'José Alonso ', 'alonfonsojose@gmail.com', 320465815, '2025-06-10 23:27:31', '', '2025-06-11 01:26:03'),
(2, 1, 'Alonso', 'alonso12345', 'José Alonso', 'olfonsojose@gmail.com', 320465845, '2025-06-10 23:29:54', 'Activo', '2025-06-11 01:28:55'),
(3, 2, 'Sebastian', 'sebastian12345', 'Johan Sebastían', 'sebas@gmail.com', 312456874, '2025-06-10 23:31:10', 'Activo', '2025-06-11 01:29:57'),
(4, 3, 'Alonso segundo', 'alonsosegundo12345', 'Alonso Segundo', 'segundo@gmail.com', 321564895, '2025-06-10 23:32:29', 'Activo', '2025-06-11 01:31:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id_vehiculo` int(11) NOT NULL,
  `marca_vehiculo` varchar(50) NOT NULL,
  `modelo_vehiculo` varchar(50) NOT NULL,
  `placa_vehiculo` varchar(20) NOT NULL,
  `nota_vehiculo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `marca_vehiculo`, `modelo_vehiculo`, `placa_vehiculo`, `nota_vehiculo`) VALUES
(1, 'Toyota', 'Corolla', 'ABC-1234', '...'),
(2, 'Ford', 'Mustang', 'XYZ-5678', '...'),
(3, 'Volkswagen', 'Gol', 'QWE-9012', '...');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`id_cotizacion`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id_material`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id_vehiculo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id_cotizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
