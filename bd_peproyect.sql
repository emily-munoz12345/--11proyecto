-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-07-2025 a las 04:40:16
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
-- Base de datos: `bd_peproyect`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(50) NOT NULL,
  `correo_cliente` varchar(70) NOT NULL,
  `telefono_cliente` varchar(25) NOT NULL,
  `direccion_cliente` text NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notas_cliente` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `correo_cliente`, `telefono_cliente`, `direccion_cliente`, `fecha_registro`, `notas_cliente`) VALUES
(1, 'María González', 'maria@gmail.com', '3102546859', 'Calle 123 #45-67, Bogotá', '2025-06-26 01:10:35', 'Cliente frecuente'),
(2, 'Pedro Sánchez', 'pedro@gmail.com', '3215648957', 'Carrera 8 #12-34, Medellín', '2025-06-26 01:10:35', 'Prefiere contacto por WhatsApp'),
(3, 'Luisa Martínez', 'luisamartinez@gmail.com', '3115468951', 'Avenida 5 #10-20, Cali', '2025-06-27 01:27:30', 'Nuevo cliente'),
(4, 'Fernando Reyes', 'ferrereyes@gmail.com', '3124435221', 'cr 9 # 58-12', '2025-06-27 01:26:40', 'Cliente Nuevo');

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
(1, 1),
(2, 2),
(3, 3),
(4, 4);

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
  `subtotal_cotizacion` decimal(10,2) NOT NULL,
  `valor_adicional` decimal(10,2) DEFAULT 0.00,
  `iva` decimal(5,2) NOT NULL,
  `total_cotizacion` decimal(10,2) NOT NULL,
  `estado_cotizacion` enum('Pendiente','Aprobado','Rechazada','Completada') NOT NULL,
  `notas_cotizacion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id_cotizacion`, `id_usuario`, `id_cliente`, `id_vehiculo`, `fecha_cotizacion`, `subtotal_cotizacion`, `valor_adicional`, `iva`, `total_cotizacion`, `estado_cotizacion`, `notas_cotizacion`) VALUES
(1, 3, 1, 1, '2025-06-30 02:53:32', 185.00, 35.00, 35.15, 220.15, 'Aprobado', 'Cliente aprobó presupuesto incluyendo el adicional de dos tornillos'),
(2, 1, 2, 2, '2025-06-26 01:10:35', 120000.00, 0.00, 22800.00, 142800.00, 'Pendiente', 'Esperando confirmación'),
(3, 3, 3, 3, '2025-06-26 01:10:35', 180000.00, 0.00, 34200.00, 214200.00, 'Completada', 'Trabajo terminado el 10/06');
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_servicios`
--

CREATE TABLE `cotizacion_servicios` (
  `id_cotizacion` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizacion_servicios`
--

INSERT INTO `cotizacion_servicios` (`id_cotizacion`, `id_servicio`, `precio`) VALUES
(1, 3, 180000.00),
(2, 2, 120000.00),
(3, 3, 180000.00);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id_material` int(11) NOT NULL,
  `nombre_material` varchar(70) NOT NULL,
  `descripcion_material` text NOT NULL,
  `precio_metro` decimal(10,2) NOT NULL,
  `stock_material` int(11) NOT NULL,
  `categoria_material` varchar(50) NOT NULL,
  `proveedor_material` varchar(100) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_material`, `nombre_material`, `descripcion_material`, `precio_metro`, `stock_material`, `categoria_material`, `proveedor_material`, `fecha_actualizacion`) VALUES
(1, 'Vinilcuero', 'Material sintético de alta durabilidad', 45000.00, 25, 'Tapicería', 'Textiles S.A.', '2025-06-26 01:10:35'),
(2, 'Espuma HD', 'Espuma de alta densidad 2cm grosor', 18000.00, 15, 'Reparación', 'Espumas Colombia', '2025-06-26 01:10:35'),
(3, 'Alfombra automotriz', 'Color gris, resistente a humedad', 32000.00, 8, 'Cueros', 'Autopartes Ltda.', '2025-06-28 20:01:20'),
(4, 'Alcantara', 'Material suave al tacto, lujoso y duradero', 75000.00, 12, 'Telas', 'Estilo Automotriz', '2025-06-28 20:30:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion_rol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion_rol`) VALUES
(1, 'Administrador', 'Acceso completo al sistema'),
(2, 'Técnico', 'Personal encargado de realizar los trabajos'),
(3, 'Vendedor', 'Personal encargado de ventas y cotizaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(50) NOT NULL,
  `descripcion_servicio` text NOT NULL,
  `precio_servicio` decimal(10,2) NOT NULL,
  `tiempo_estimado` varchar(50) NOT NULL,
  `categoria_servicio` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `descripcion_servicio`, `precio_servicio`, `tiempo_estimado`, `categoria_servicio`) VALUES
(1, 'Tapizado completo', 'Tapizado de asientos en vinilcuero', 350000.00, '3 días', 'Tapicería'),
(2, 'Cambio de alfombra', 'Instalación de alfombra', 120000.00, '1 día', 'Interior'),
(3, 'Reparación de asiento', 'Arreglo de estructura y espuma', 180000.00, '2 días', 'Reparación'),
(4, 'Tratamiento de cuero', 'Aplicación de producto para cuidar cuero de asientos', 50000.00, '2 horas', 'Mantenimiento');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajos`
--

CREATE TABLE `trabajos` (
  `id_trabajos` int(11) NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Pendiente','En progreso','Entregado','Cancelado') NOT NULL DEFAULT 'Pendiente',
  `notas` text DEFAULT NULL,
  `fotos` text DEFAULT NULL,
  PRIMARY KEY (`id_trabajos`),
  KEY `id_cotizacion` (`id_cotizacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Volcado de datos para la tabla `trabajos`
--

INSERT INTO `trabajos` (`id_trabajos`, `id_cotizacion`, `fecha_inicio`, `fecha_fin`, `estado`, `notas`, `fotos`) VALUES
(1, 1, '2025-06-10', '2025-06-13', 'Entregado', 'Cliente satisfecho con el trabajo', '/fotos/trabajo1_1.jpg,/fotos/trabajo1_2.jpg'),
(2, 2, '2025-06-15', NULL, 'Pendiente', 'Esperando aprobación final', NULL),
(3, 3, '2025-06-05', '2025-06-07', 'Entregado', 'Se realizó ajuste adicional', '/fotos/trabajo3_1.jpg,/fotos/trabajo3_2.jpg,/fotos/trabajo3_3.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajo_fotos`
--

CREATE TABLE IF NOT EXISTS `trabajo_fotos` (
  `id_foto` int(11) NOT NULL AUTO_INCREMENT,
  `id_trabajo` int(11) NOT NULL,
  `ruta_foto` varchar(255) NOT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_foto`),
  KEY `id_trabajo` (`id_trabajo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Volcado de datos para la tabla `trabajo_fotos`
--
INSERT INTO `trabajo_fotos` (`id_trabajo`, `ruta_foto`) VALUES
--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `username_usuario` varchar(50) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `nombre_completo` varchar(70) NOT NULL,
  `correo_usuario` varchar(70) NOT NULL,
  `telefono_usuario` varchar(25) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activo_usuario` enum('Activo','Inactivo') NOT NULL,
  `ultima_actividad` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `username_usuario`, `contrasena_usuario`, `nombre_completo`, `correo_usuario`, `telefono_usuario`, `fecha_creacion`, `activo_usuario`, `ultima_actividad`) VALUES
(1, 1, 'admin1', 'erosramazoti', 'Jose Alonso', 'alonso@tallertapiceria.com', '3204569555', '2025-06-29 02:14:06', 'Activo', '2025-06-28 21:14:06'),
(2, 2, 'tecnico1', 'argos1.3', 'Johan Sebastian', 'johan@tallertapiceria.com', '3625489561', '2025-06-30 02:14:54', 'Activo', '2025-06-26 19:51:53'),
(3, 3, 'vendedor1', 'eros ramazoti', 'Yamm Alonso', 'yamm@tallertapiceria.com', '3125468579', '2025-06-29 02:10:56', 'Activo', '2025-06-28 21:10:56'),
(4, 1, 'admin2', 'argos 1.3', 'Edith Diasmin', 'edit@gmail.com', '3122654845', '2025-06-28 22:02:00', 'Activo', '2025-06-28 17:02:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id_vehiculo` int(11) NOT NULL,
  `marca_vehiculo` varchar(50) NOT NULL,
  `modelo_vehiculo` varchar(50) NOT NULL,
  `placa_vehiculo` varchar(20) NOT NULL,
  `notas_vehiculo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `marca_vehiculo`, `modelo_vehiculo`, `placa_vehiculo`, `notas_vehiculo`) VALUES
(1, 'Toyota', 'Corolla', 'ABC123', 'Color blanco, año 2020'),
(2, 'Chevrolet', 'Spark', 'DEF456', 'Color azul, año 2018'),
(3, 'Renault', 'Logan', 'GHI789', 'Color gris, año 2019'),
(4, 'Honda', 'Civic', 'JKL9101', 'carro rojo');
--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `cliente_vehiculo`
--
ALTER TABLE `cliente_vehiculo`
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vehiculo` (`id_vehiculo`);

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`id_cotizacion`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_vehiculo` (`id_vehiculo`);

--
-- Indices de la tabla `cotizacion_servicios`
--
ALTER TABLE `cotizacion_servicios`
  ADD KEY `id_cotizacion` (`id_cotizacion`),
  ADD KEY `id_servicio` (`id_servicio`);

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
-- Indices de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD PRIMARY KEY (`id_trabajos`),
  ADD KEY `id_cotizacion` (`id_cotizacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

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
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id_cotizacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  MODIFY `id_trabajos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente_vehiculo`
--
ALTER TABLE `cliente_vehiculo`
  ADD CONSTRAINT `cliente_vehiculo_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cliente_vehiculo_ibfk_2` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotizaciones_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotizaciones_ibfk_3` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cotizacion_servicios`
--
ALTER TABLE `cotizacion_servicios`
  ADD CONSTRAINT `cotizacion_servicios_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotizacion_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD CONSTRAINT `trabajos_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
