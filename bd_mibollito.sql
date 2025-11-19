-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2025 a las 23:07:13
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
-- Base de datos: `bd_mibollito`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `fecha_registro`) VALUES
(1, 'Materia Prima', 'Materiales', '2025-10-22 21:38:56'),
(2, 'Producto Elaborado', 'Bollo Limpio', '2025-11-10 18:57:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_stock`
--

CREATE TABLE `control_stock` (
  `id_control` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `stock_minimo` int(11) DEFAULT 10,
  `stock_maximo` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `detalle_pedido`
--
DELIMITER $$
CREATE TRIGGER `trg_actualizar_total_pedido` AFTER INSERT ON `detalle_pedido` FOR EACH ROW BEGIN
    UPDATE pedidos
    SET total = (
        SELECT SUM(subtotal) FROM detalle_pedido WHERE id_pedido = NEW.id_pedido
    )
    WHERE id_pedido = NEW.id_pedido;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

CREATE TABLE `devoluciones` (
  `id_devolucion` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `estado` enum('Devuelto','Pendiente','Revisado') DEFAULT 'Pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `domicilios`
--

CREATE TABLE `domicilios` (
  `id_domicilio` int(11) NOT NULL,
  `conductor_responsable` varchar(100) DEFAULT NULL,
  `matricula_vehiculo` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `producto` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas_vendedores`
--

CREATE TABLE `entregas_vendedores` (
  `id_entrega` int(11) NOT NULL,
  `vendedor` varchar(100) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_entrega` datetime DEFAULT current_timestamp(),
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entregas_vendedores`
--

INSERT INTO `entregas_vendedores` (`id_entrega`, `vendedor`, `id_vendedor`, `id_producto`, `cantidad`, `fecha_entrega`, `observacion`) VALUES
(4, '', 1, 27, 15, '2025-10-29 22:04:38', NULL),
(5, '', 1, 11, 8, '2025-10-29 22:10:53', NULL),
(6, '', 1, 27, 5, '2025-10-29 22:11:16', NULL),
(7, '', 2, 11, 2, '2025-10-29 22:11:22', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado_producto` enum('En proceso','Finalizado','Detenido','En espera','Por vencer','Vencido','Pendiente','Entregado','Cancelado') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id_estado`, `id_producto`, `nombre`, `estado_producto`, `fecha_registro`) VALUES
(17, 6, '', 'Finalizado', '2025-11-04 20:16:20'),
(18, 7, '', '', '2025-11-04 20:47:02'),
(19, 7, '', 'En espera', '2025-11-04 20:48:32'),
(20, 8, '', 'En proceso', '2025-11-05 21:45:44'),
(21, 8, '', 'Finalizado', '2025-11-10 19:31:02'),
(22, 8, '', 'En proceso', '2025-11-10 19:31:14'),
(23, 8, '', 'En proceso', '2025-11-19 21:55:52'),
(24, 8, '', 'En proceso', '2025-11-19 21:55:59'),
(25, 8, '', 'En proceso', '2025-11-19 21:56:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id_movimiento` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo` enum('entrada','salida','produccion','venta') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp(),
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagado','Entregado','Cancelado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha_pedido`, `total`, `estado`) VALUES
(1, 9, '2025-10-17 21:07:49', 0.00, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_actual` int(11) GENERATED ALWAYS AS (`stock`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `id_categoria`, `descripcion`, `precio`, `stock`, `id_proveedor`, `fecha_registro`) VALUES
(6, 'Bollo', 1, 'Bollo Limpio', 2000.00, 0, 1, '2025-10-08 21:56:06'),
(7, 'Bollo de Maiz', 1, 'Bollo de Maiz', 2000.00, 0, 1, '2025-10-09 19:52:15'),
(8, 'Bollo Limpio', 1, 'Bollo Limpio', 2000.00, 0, 1, '2025-10-09 20:06:57'),
(9, 'Masa', 1, 'Masa', 1500.00, 0, 1, '2025-10-09 20:07:33'),
(11, 'Agua de maiz', 1, 'agua', 2000.00, 0, 1, '2025-10-09 20:12:15'),
(27, 'Bollito', 1, 'a', 2000.00, 0, 1, '2025-10-29 20:19:28'),
(28, 'Mai', 1, 'mai', 2000.00, 0, 1, '2025-10-29 20:22:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id_promocion` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `tipo` enum('porcentaje','fijo') DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre`, `contacto`, `telefono`, `correo`, `direccion`, `fecha_registro`) VALUES
(1, 'Panadería Doña Rosa', 'Rosa Gutiérrez', '3009876543', 'contacto@donarosa.com', 'Cra 45 #23-11', '2025-10-07 16:18:09'),
(2, 'Harinas La Espiga', 'Luis Torres', '3024567890', 'ventas@laespiga.com', 'Calle 8 #12-55', '2025-10-07 16:18:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Empleado'),
(3, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('administrador','empleado') DEFAULT 'empleado',
  `id_rol` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `usuario`, `contraseña`, `rol`, `id_rol`) VALUES
(9, 'imanol', 'imanol@lol.com', '', '$2y$10$TDYrHhShIj5/47OK.guIEeG3W4GvEu2tV/AU9v3mNJhQWTGBknAny', 'empleado', NULL),
(12, 'nafer', 'nafer@esputa.com', '', '$2y$10$Ctmafc/ziSlU.s/ZOyAMeurXFK4DxuTbJw2ETqq8f/fY40vejaEnm', 'empleado', NULL),
(13, 'imanol', 'imanol@sena.com', '', '$2y$10$flQNWx201ONm3F9XHjpAAuAszfGnEECFLx9uulpFuIAwcF53pDFN2', 'administrador', 1),
(15, 'esneider', 'saenz@gmail.com', '', '$2y$10$mBn5gziowUMZRW7pmog0U.lC3pQRAEgsidywcbvgWHVWYBj.3WSdW', 'empleado', NULL),
(16, 'imanolandres', 'imanol@senas.com', '', '$2y$10$sDXAZ1HmwMkYnE5IFKETXup8fGKgtZNiTd6cPBTz6sQtU4k88y0Ma', 'empleado', NULL),
(17, 'imanolandres', 'ada@gmail.com', '', '$2y$10$QO58egQxer0neAa4xkxljeCEYgAfcqd80QmsCd2Nhd89iNdJ/abcW', 'empleado', NULL),
(18, 'Kanner', 'kannertapia0919@gmail.com', '', '$2y$10$PrCU6E6MvWGAaGkU5an77uAVJjjuYY9CQ0ohKW13f7zQlNXah8FRy', 'empleado', NULL),
(19, 'yaimeth  martinez', 'yaimethmartinez1@gmail.com', '', '$2y$10$ypbeFcA/YRuxmhxY4IyN1OzPPLpG9dxKpXZ0/x.S83hu4ldHaPR96', 'empleado', NULL),
(21, 'haniel fernando ramirez romero', 'hanielr98@gmail.com', '', '$2y$10$N.Q2WUqEoRIuuC6Ih.c2HO7jWS6BjQ5ThPi8erX2AWkQZzkmkxOM6', 'empleado', NULL),
(22, 'tiberio', 'vizcainotiberio@gmail.com', '', '$2y$10$2a4QBRiJQINwFUrh12anuuTJTv4NG/wqnylSNIPwbuar3WWlypSFi', 'empleado', NULL),
(23, 'catalina moscote', 'catalinamoscote@gmail.com', '', '$2y$10$iadwQaJxJnsQcgIogYMxF.7GYHxjN5wr1fCtKemJQFv5uDQwXcQ9i', 'empleado', NULL),
(24, 'andres', 'imanoal@sena.com', '', '$2y$10$oWE5LVCy2jbXSBhMKDrzy.DE0NsmJLfhTBBrwXD87gppaAJp784Qu', 'empleado', NULL),
(25, 'franklin pianeta', 'pfranklinsantiago@gmail.com', '', '$2y$10$8Cy/5O7oZeBnBzo3nSdBNuScms5mAPklQtcbxL4zz32qZN3kFni9i', 'empleado', NULL),
(26, 'Juan', 'Kenner@sena.com', '', '$2y$10$TgBxzzWvBi94mAhdBgeAnOP64zywe.X.1S1inqgS6VegH6wQ7jBiK', 'empleado', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedores_ambulantes`
--

CREATE TABLE `vendedores_ambulantes` (
  `id_vendedor` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `zona` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vendedores_ambulantes`
--

INSERT INTO `vendedores_ambulantes` (`id_vendedor`, `id_usuario`, `zona`, `nombre`, `telefono`, `direccion`, `fecha_registro`) VALUES
(1, 0, '', 'Fadul', '3008247471', 'Cra 21 Calle 7E20', '2025-10-29 20:57:59'),
(2, 0, '', 'Fadul', '3008247471', 'Cra 21 Calle 7E20', '2025-10-29 20:58:03');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas` (
`id_pedido` int(11)
,`cliente` varchar(100)
,`fecha_pedido` timestamp
,`total` decimal(10,2)
,`estado` enum('Pendiente','Pagado','Entregado','Cancelado')
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas`
--
DROP TABLE IF EXISTS `vista_ventas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas`  AS SELECT `p`.`id_pedido` AS `id_pedido`, `u`.`nombre` AS `cliente`, `p`.`fecha_pedido` AS `fecha_pedido`, `p`.`total` AS `total`, `p`.`estado` AS `estado` FROM (`pedidos` `p` join `usuarios` `u` on(`p`.`id_usuario` = `u`.`id_usuario`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `control_stock`
--
ALTER TABLE `control_stock`
  ADD PRIMARY KEY (`id_control`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`id_devolucion`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `domicilios`
--
ALTER TABLE `domicilios`
  ADD PRIMARY KEY (`id_domicilio`);

--
-- Indices de la tabla `entregas_vendedores`
--
ALTER TABLE `entregas_vendedores`
  ADD PRIMARY KEY (`id_entrega`),
  ADD KEY `id_vendedor` (`id_vendedor`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `fk_producto_categoria` (`id_categoria`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id_promocion`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `usuario` (`usuario`) USING BTREE,
  ADD KEY `correo` (`correo`) USING BTREE;

--
-- Indices de la tabla `vendedores_ambulantes`
--
ALTER TABLE `vendedores_ambulantes`
  ADD PRIMARY KEY (`id_vendedor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `control_stock`
--
ALTER TABLE `control_stock`
  MODIFY `id_control` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  MODIFY `id_devolucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `domicilios`
--
ALTER TABLE `domicilios`
  MODIFY `id_domicilio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `entregas_vendedores`
--
ALTER TABLE `entregas_vendedores`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `vendedores_ambulantes`
--
ALTER TABLE `vendedores_ambulantes`
  MODIFY `id_vendedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `control_stock`
--
ALTER TABLE `control_stock`
  ADD CONSTRAINT `control_stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD CONSTRAINT `devoluciones_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `entregas_vendedores`
--
ALTER TABLE `entregas_vendedores`
  ADD CONSTRAINT `entregas_vendedores_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores_ambulantes` (`id_vendedor`),
  ADD CONSTRAINT `entregas_vendedores_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `estado`
--
ALTER TABLE `estado`
  ADD CONSTRAINT `estado_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
