-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-07-2025 a las 02:03:44
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
  `notas_cliente` text NOT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `correo_cliente`, `telefono_cliente`, `direccion_cliente`, `fecha_registro`, `notas_cliente`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 'María González', 'maria@gmail.com', '3102546859', 'Calle 123 #45-67, Bogotá', '2025-06-26 01:10:35', 'Cliente frecuente', 0, NULL, NULL),
(2, 'Pedro Sánchez', 'pedro@gmail.com', '3215648957', 'Carrera 8 #12-34, Medellín', '2025-06-26 01:10:35', 'Prefiere contacto por WhatsApp', 0, NULL, NULL),
(3, 'Luisa Martínez', 'luisamartinez@gmail.com', '3115468951', 'Avenida 5 #10-20, Cali', '2025-06-27 01:27:30', 'Nuevo cliente', 0, NULL, NULL),
(4, 'Fernando Reyes', 'ferrereyes@gmail.com', '3205648912', 'cr 9 # 58-12', '2025-07-03 03:42:15', 'Cliente Nuevo', 0, NULL, NULL),
(5, 'Cliente Eliminado 1', 'eliminado1@ejemplo.com', '3111111111', 'Dirección eliminada', '2025-07-11 10:30:00', 'Cliente eliminado de prueba', 1, '2025-07-11 10:30:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_ediciones`
--

CREATE TABLE `clientes_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes_ediciones`
--

INSERT INTO `clientes_ediciones` (`id_edicion`, `id_cliente`, `campo_editado`, `valor_anterior`, `valor_nuevo`, `fecha_edicion`, `editado_por`) VALUES
(1, 1, 'telefono_cliente', '3100000000', '3102546859', '2025-06-25 20:10:35', 3),
(2, 3, 'notas_cliente', 'Cliente potencial', 'Nuevo cliente', '2025-06-26 20:27:30', 1);

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
  `notas_cotizacion` text NOT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id_cotizacion`, `id_usuario`, `id_cliente`, `id_vehiculo`, `fecha_cotizacion`, `subtotal_cotizacion`, `valor_adicional`, `iva`, `total_cotizacion`, `estado_cotizacion`, `notas_cotizacion`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 3, 1, 1, '2025-07-03 02:59:42', 180.00, 4000.00, 34.20, 218.20, 'Aprobado', 'Cliente aprobó presupuesto incluyendo el adicional de dos tornillos', 0, NULL, NULL),
(2, 1, 2, 2, '2025-06-26 01:10:35', 120000.00, 0.00, 999.99, 142800.00, 'Pendiente', 'Esperando confirmación', 0, NULL, NULL),
(3, 3, 3, 3, '2025-06-26 01:10:35', 180000.00, 0.00, 999.99, 214200.00, 'Completada', 'Trabajo terminado el 10/06', 0, NULL, NULL),
(4, 1, 4, 4, '2025-07-03 03:28:58', 300.00, 5000.00, 57.00, 362.00, 'Pendiente', 'el cliente aprobó el valor adicional de los 5 broches', 0, NULL, NULL),
(5, 1, 2, 2, '2025-07-10 14:22:18', 200000.00, 10000.00, 399.00, 250.00, 'Rechazada', 'Cotización rechazada por el cliente', 1, '2025-07-10 14:25:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones_ediciones`
--

CREATE TABLE `cotizaciones_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_cotizacion` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizaciones_ediciones`
--

INSERT INTO `cotizaciones_ediciones` (`id_edicion`, `id_cotizacion`, `campo_editado`, `valor_anterior`, `valor_nuevo`, `fecha_edicion`, `editado_por`) VALUES
(1, 1, 'valor_adicional', '3000.00', '4000.00', '2025-07-02 21:59:42', 3);

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
(2, 2, 120000.00),
(3, 3, 180000.00),
(1, 3, 180000.00),
(4, 2, 120000.00),
(4, 3, 180000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_sistema`
--

CREATE TABLE `logs_sistema` (
  `id_log` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `id_elemento` int(11) NOT NULL,
  `realizado_por` int(11) NOT NULL,
  `fecha_accion` datetime DEFAULT current_timestamp(),
  `detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs_sistema`
--

INSERT INTO `logs_sistema` (`id_log`, `accion`, `tabla_afectada`, `id_elemento`, `realizado_por`, `fecha_accion`, `detalles`) VALUES
(1, 'ELIMINACION', 'clientes', 5, 1, '2025-07-11 05:30:00', 'Cliente eliminado a petición del usuario'),
(2, 'ELIMINACION', 'cotizaciones', 5, 1, '2025-07-10 09:25:00', 'Cotización rechazada eliminada'),
(3, 'RESTAURACION', 'materiales', 5, 1, '2025-07-09 14:15:00', 'Material restaurado por error');

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
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_material`, `nombre_material`, `descripcion_material`, `precio_metro`, `stock_material`, `categoria_material`, `proveedor_material`, `fecha_actualizacion`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 'Vinilcuero', 'Material sintético de alta durabilidad', 45000.00, 25, 'Tapicería', 'Textiles S.A.', '2025-06-26 01:10:35', 0, NULL, NULL),
(2, 'Espuma HD', 'Espuma de alta densidad 2cm grosor', 18000.00, 15, 'Reparación', 'Espumas Colombia', '2025-06-26 01:10:35', 0, NULL, NULL),
(3, 'Alfombra automotriz', 'Color gris, resistente a humedad', 32000.00, 8, 'Cueros', 'Autopartes Ltda.', '2025-06-28 20:01:20', 0, NULL, NULL),
(4, 'Alcantara', 'Material suave al tacto, lujoso y duradero', 75000.00, 8, 'Telas', 'Estilo Automotriz', '2025-07-03 03:43:29', 0, NULL, NULL),
(5, 'Material Eliminado', 'Material descontinuado', 10000.00, 0, 'Varios', 'Proveedor Antiguo', '2025-07-08 15:20:00', 1, '2025-07-08 15:20:00', 1),
(6, 'Cuero Natural', 'Cuero 100% natural de alta calidad', 120000.00, 5, 'Tapicería', 'Cueros Premium', '2025-07-09 10:30:00', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales_ediciones`
--

CREATE TABLE `materiales_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales_ediciones`
--

INSERT INTO `materiales_ediciones` (`id_edicion`, `id_material`, `campo_editado`, `valor_anterior`, `valor_nuevo`, `fecha_edicion`, `editado_por`) VALUES
(1, 4, 'precio_metro', '70000.00', '75000.00', '2025-07-02 22:43:29', 1),
(2, 6, 'stock_material', '3', '5', '2025-07-09 05:30:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto`
--

CREATE TABLE `mensajes_contacto` (
  `id_mensaje` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `asunto` varchar(50) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes_contacto`
--

INSERT INTO `mensajes_contacto` (`id_mensaje`, `nombre_completo`, `correo_electronico`, `telefono`, `asunto`, `mensaje`, `fecha_envio`, `leido`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 'Carlos Andrés Pérez', 'carlos.perez@example.com', '3201234567', 'cotizacion', 'Buen día, necesito una cotización para tapizar los asientos de mi Toyota Corolla 2020. ¿Podrían enviarme información sobre materiales y precios?', '2025-07-10 08:15:22', 1, 0, NULL, NULL),
(2, 'María Fernanda Gómez', 'maria.gomez@example.com', '3159876543', 'consulta', 'Hola, quisiera saber si trabajan con materiales ecológicos para tapicería y cuál sería el tiempo estimado para un vehículo mediano.', '2025-07-11 14:30:45', 0, 0, NULL, NULL),
(3, 'Jorge Eduardo Rodríguez', 'jorge.rodriguez@example.com', '3104567890', 'garantia', 'El tapizado que me hicieron hace 3 meses presenta desgaste prematuro. Quisiera información sobre la garantía del trabajo realizado.', '2025-07-12 09:05:18', 1, 0, NULL, NULL),
(4, 'Spam Message', 'spam@example.com', '0000000000', 'spam', 'Mensaje de spam no deseado', '2025-07-11 11:20:30', 1, 1, '2025-07-11 11:25:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto_ediciones`
--

CREATE TABLE `mensajes_contacto_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_mensaje` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `papelera_sistema`
--

CREATE TABLE `papelera_sistema` (
  `id_papelera` int(11) NOT NULL,
  `tabla_origen` varchar(50) NOT NULL,
  `id_elemento` int(11) NOT NULL,
  `nombre_elemento` varchar(255) NOT NULL,
  `datos_originales` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`datos_originales`)),
  `eliminado_por` int(11) NOT NULL,
  `fecha_eliminacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `papelera_sistema`
--

INSERT INTO `papelera_sistema` (`id_papelera`, `tabla_origen`, `id_elemento`, `nombre_elemento`, `datos_originales`, `eliminado_por`, `fecha_eliminacion`) VALUES
(1, 'clientes', 5, 'Cliente Eliminado 1', '{\"id_cliente\": 5, \"nombre_cliente\": \"Cliente Eliminado 1\", \"correo_cliente\": \"eliminado1@ejemplo.com\", \"telefono_cliente\": \"3111111111\", \"direccion_cliente\": \"Dirección eliminada\", \"notas_cliente\": \"Cliente eliminado de prueba\"}', 1, '2025-07-11 10:30:00'),
(2, 'cotizaciones', 5, 'Cotización #5', '{\"id_cotizacion\": 5, \"id_usuario\": 1, \"id_cliente\": 2, \"id_vehiculo\": 2, \"subtotal_cotizacion\": \"200000.00\", \"valor_adicional\": \"10000.00\", \"iva\": \"399.00\", \"total_cotizacion\": \"250.00\", \"estado_cotizacion\": \"Rechazada\", \"notas_cotizacion\": \"Cotización rechazada por el cliente\"}', 1, '2025-07-10 14:25:00'),
(3, 'materiales', 5, 'Material Eliminado', '{\"id_material\": 5, \"nombre_material\": \"Material Eliminado\", \"descripcion_material\": \"Material descontinuado\", \"precio_metro\": \"10000.00\", \"stock_material\": 0, \"categoria_material\": \"Varios\", \"proveedor_material\": \"Proveedor Antiguo\"}', 1, '2025-07-08 15:20:00'),
(4, 'mensajes_contacto', 4, 'Spam Message', '{\"id_mensaje\": 4, \"nombre_completo\": \"Spam Message\", \"correo_electronico\": \"spam@example.com\", \"telefono\": \"0000000000\", \"asunto\": \"spam\", \"mensaje\": \"Mensaje de spam no deseado\", \"leido\": 1}', 1, '2025-07-11 11:25:00');

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
  `categoria_servicio` varchar(50) NOT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `descripcion_servicio`, `precio_servicio`, `tiempo_estimado`, `categoria_servicio`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 'Tapizado completo', 'Tapizado de asientos en vinilcuero', 350000.00, '3 días', 'Tapicería', 0, NULL, NULL),
(2, 'Cambio de alfombra', 'Instalación de alfombra', 120000.00, '1 día', 'Interior', 0, NULL, NULL),
(3, 'Reparación de asiento', 'Arreglo de estructura y espuma', 180000.00, '2 días', 'Reparación', 0, NULL, NULL),
(4, 'Tratamiento de cuero', 'Aplicación de producto para cuidar cuero de asientos', 100000.00, '1 día', 'Mantenimiento', 0, NULL, NULL),
(5, 'Servicio Descontinuado', 'Servicio que ya no se ofrece', 50000.00, '1 día', 'Varios', 1, '2025-07-07 12:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_ediciones`
--

CREATE TABLE `servicios_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajos`
--

CREATE TABLE `trabajos` (
  `id_trabajos` int(11) NOT NULL,
  `id_cotizacion` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Pendiente','En progreso','Entregado','Cancelado') NOT NULL,
  `notas` text NOT NULL,
  `fotos` varchar(255) NOT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajos`
--

INSERT INTO `trabajos` (`id_trabajos`, `id_cotizacion`, `fecha_inicio`, `fecha_fin`, `estado`, `notas`, `fotos`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 1, '2025-06-10', '2025-06-13', 'Entregado', 'Cliente satisfecho con el trabajo', '/fotos/trabajo1.jpg,/uploads/trabajos/6865fa62a362a_Captura de pantalla (1).png', 0, NULL, NULL),
(2, 2, '2025-06-15', '0000-00-00', 'Pendiente', 'Esperando aprobación final', '', 0, NULL, NULL),
(3, 3, '2025-06-05', '2025-06-07', 'Entregado', 'Se realizó ajuste adicional', '/fotos/trabajo3.jpg', 0, NULL, NULL),
(4, 4, '2025-07-05', '2025-07-07', 'Cancelado', 'Trabajo cancelado por el cliente', '', 1, '2025-07-08 09:45:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajos_ediciones`
--

CREATE TABLE `trabajos_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_trabajo` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

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
  `ultima_actividad` datetime NOT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `username_usuario`, `contrasena_usuario`, `nombre_completo`, `correo_usuario`, `telefono_usuario`, `fecha_creacion`, `activo_usuario`, `ultima_actividad`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 1, 'admin1', 'erosramazoti', 'Jose Alonso', 'alonso@tallertapiceria.com', '3204569555', '2025-07-08 01:51:51', 'Activo', '2025-07-07 20:51:51', 0, NULL, NULL),
(2, 2, 'tecnico1', 'argos1.3', 'Johan Sebastian', 'johan@tallertapiceria.com', '3625489561', '2025-06-30 02:14:54', 'Activo', '2025-06-26 19:51:53', 0, NULL, NULL),
(3, 3, 'vendedor1', 'eros ramazoti', 'Yamm Alonso', 'yamm@tallertapiceria.com', '3125468579', '2025-06-29 02:10:56', 'Activo', '2025-06-28 21:10:56', 0, NULL, NULL),
(4, 1, 'admin2', 'argos 1.3', 'Edith Diasmin', 'edit@gmail.com', '3122654845', '2025-06-28 22:02:00', 'Activo', '2025-06-28 17:02:00', 0, NULL, NULL),
(5, 3, 'vendedor2', 'password123', 'Vendedor Eliminado', 'vendedor@example.com', '3001112233', '2025-07-06 10:30:00', 'Inactivo', '2025-07-05 15:20:00', 1, '2025-07-06 10:30:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_ediciones`
--

CREATE TABLE `usuarios_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id_vehiculo` int(11) NOT NULL,
  `marca_vehiculo` varchar(50) NOT NULL,
  `modelo_vehiculo` varchar(50) NOT NULL,
  `placa_vehiculo` varchar(20) NOT NULL,
  `notas_vehiculo` text NOT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `marca_vehiculo`, `modelo_vehiculo`, `placa_vehiculo`, `notas_vehiculo`, `eliminado`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 'Toyota', 'Corolla', 'ABC123', 'Color blanco, año 2020', 0, NULL, NULL),
(2, 'Chevrolet', 'Spark', 'DEF654', 'Color azul, año 2018', 0, NULL, NULL),
(3, 'Renault', 'Logan', 'GHI789', 'Color gris, año 2019', 0, NULL, NULL),
(4, 'Honda', 'Civic', 'JKL9101', 'carro gris', 0, NULL, NULL),
(5, 'Ford', 'Fiesta', 'MNO234', 'Vehiculo eliminado de prueba', 1, '2025-07-10 16:40:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos_ediciones`
--

CREATE TABLE `vehiculos_ediciones` (
  `id_edicion` int(11) NOT NULL,
  `id_vehiculo` int(11) NOT NULL,
  `campo_editado` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha_edicion` datetime DEFAULT current_timestamp(),
  `editado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `clientes_ediciones`
--
ALTER TABLE `clientes_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_cliente` (`id_cliente`);

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
-- Indices de la tabla `cotizaciones_ediciones`
--
ALTER TABLE `cotizaciones_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_cotizacion` (`id_cotizacion`);

--
-- Indices de la tabla `cotizacion_servicios`
--
ALTER TABLE `cotizacion_servicios`
  ADD KEY `id_cotizacion` (`id_cotizacion`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `realizado_por` (`realizado_por`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id_material`);

--
-- Indices de la tabla `materiales_ediciones`
--
ALTER TABLE `materiales_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_material` (`id_material`);

--
-- Indices de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  ADD PRIMARY KEY (`id_mensaje`);

--
-- Indices de la tabla `mensajes_contacto_ediciones`
--
ALTER TABLE `mensajes_contacto_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_mensaje` (`id_mensaje`);

--
-- Indices de la tabla `papelera_sistema`
--
ALTER TABLE `papelera_sistema`
  ADD PRIMARY KEY (`id_papelera`),
  ADD KEY `eliminado_por` (`eliminado_por`);

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
-- Indices de la tabla `servicios_ediciones`
--
ALTER TABLE `servicios_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD PRIMARY KEY (`id_trabajos`),
  ADD KEY `id_cotizacion` (`id_cotizacion`);

--
-- Indices de la tabla `trabajos_ediciones`
--
ALTER TABLE `trabajos_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_trabajo` (`id_trabajo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `usuarios_ediciones`
--
ALTER TABLE `usuarios_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id_vehiculo`);

--
-- Indices de la tabla `vehiculos_ediciones`
--
ALTER TABLE `vehiculos_ediciones`
  ADD PRIMARY KEY (`id_edicion`),
  ADD KEY `id_vehiculo` (`id_vehiculo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clientes_ediciones`
--
ALTER TABLE `clientes_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id_cotizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cotizaciones_ediciones`
--
ALTER TABLE `cotizaciones_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `logs_sistema`
--
ALTER TABLE `logs_sistema`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `materiales_ediciones`
--
ALTER TABLE `materiales_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mensajes_contacto_ediciones`
--
ALTER TABLE `mensajes_contacto_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `papelera_sistema`
--
ALTER TABLE `papelera_sistema`
  MODIFY `id_papelera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `servicios_ediciones`
--
ALTER TABLE `servicios_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  MODIFY `id_trabajos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `trabajos_ediciones`
--
ALTER TABLE `trabajos_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios_ediciones`
--
ALTER TABLE `usuarios_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `vehiculos_ediciones`
--
ALTER TABLE `vehiculos_ediciones`
  MODIFY `id_edicion` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes_ediciones`
--
ALTER TABLE `clientes_ediciones`
  ADD CONSTRAINT `clientes_ediciones_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

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
-- Filtros para la tabla `cotizaciones_ediciones`
--
ALTER TABLE `cotizaciones_ediciones`
  ADD CONSTRAINT `cotizaciones_ediciones_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`);

--
-- Filtros para la tabla `cotizacion_servicios`
--
ALTER TABLE `cotizacion_servicios`
  ADD CONSTRAINT `cotizacion_servicios_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotizacion_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD CONSTRAINT `logs_sistema_ibfk_1` FOREIGN KEY (`realizado_por`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `materiales_ediciones`
--
ALTER TABLE `materiales_ediciones`
  ADD CONSTRAINT `materiales_ediciones_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `materiales` (`id_material`);

--
-- Filtros para la tabla `mensajes_contacto_ediciones`
--
ALTER TABLE `mensajes_contacto_ediciones`
  ADD CONSTRAINT `mensajes_contacto_ediciones_ibfk_1` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes_contacto` (`id_mensaje`);

--
-- Filtros para la tabla `papelera_sistema`
--
ALTER TABLE `papelera_sistema`
  ADD CONSTRAINT `papelera_sistema_ibfk_1` FOREIGN KEY (`eliminado_por`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `servicios_ediciones`
--
ALTER TABLE `servicios_ediciones`
  ADD CONSTRAINT `servicios_ediciones_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD CONSTRAINT `trabajos_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `trabajos_ediciones`
--
ALTER TABLE `trabajos_ediciones`
  ADD CONSTRAINT `trabajos_ediciones_ibfk_1` FOREIGN KEY (`id_trabajo`) REFERENCES `trabajos` (`id_trabajos`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios_ediciones`
--
ALTER TABLE `usuarios_ediciones`
  ADD CONSTRAINT `usuarios_ediciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `vehiculos_ediciones`
--
ALTER TABLE `vehiculos_ediciones`
  ADD CONSTRAINT `vehiculos_ediciones_ibfk_1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;