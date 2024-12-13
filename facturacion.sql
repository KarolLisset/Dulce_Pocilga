-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2024 at 02:54 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `facturacion`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (`n_cantidad` INT, `n_precio` DECIMAL(10,2), `codigo` INT)   BEGIN
	DECLARE nueva_existencia int;
    DECLARE nuevo_total decimal(10,2);
    DECLARE nuevo_precio decimal(10,2);
    
    DECLARE cant_actual int;
    DECLARE pre_actual decimal(10,2);
    
    DECLARE actual_existencia int;
    DECLARE actual_precio decimal(10,2);
    
    SELECT precio,existencia INTO actual_precio,actual_existencia FROM producto WHERE codproducto = codigo;
    SET nueva_existencia = actual_existencia + n_cantidad;
    SET nuevo_total = (actual_existencia * actual_precio) + (n_cantidad * n_precio);
    SET nuevo_precio = nuevo_total / nueva_existencia;
    
    UPDATE producto SET existencia = nueva_existencia, precio = nuevo_precio WHERE codproducto = codigo;
    
    SELECT nueva_existencia,nuevo_precio;
    
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (`codigo` INT, `cantidad` INT, `token_user` VARCHAR(50))   BEGIN
    
     DECLARE precio_actual decimal(10,2); 
  SELECT precio INTO precio_actual FROM producto WHERE codproducto = codigo;
        
        INSERT INTO detalle_temp(token_user,codproducto,cantidad,precio_venta) VALUES(token_user, codigo, cantidad, precio_actual);
        
        SELECT tmp.correlativo, tmp.codproducto,p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp 
        INNER JOIN producto p
        ON tmp.codproducto = p.codproducto
        WHERE tmp.token_user = token_user;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (IN `no_factura` INT)   BEGIN
    DECLARE existe_factura INT;
    DECLARE registros INT;
    DECLARE a INT;

    DECLARE cod_producto INT;
    DECLARE cant_producto INT;
    DECLARE existencia_actual INT;
    DECLARE nueva_existencia INT;

    -- Verificar si existe la factura activa
    SET existe_factura = (SELECT COUNT(*) 
                          FROM factura 
                          WHERE nofactura = no_factura AND estatus = 1);

    IF existe_factura > 0 THEN
        -- Crear tabla temporal
        CREATE TEMPORARY TABLE IF NOT EXISTS tbl_tmp (
            id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            cod_prod BIGINT,
            cant_prod INT
        );

        SET a = 1;

        -- Contar registros en detallefactura
        SET registros = (SELECT COUNT(*) 
                         FROM detallefactura 
                         WHERE nofactura = no_factura);

        IF registros > 0 THEN
            -- Insertar productos en la tabla temporal
            INSERT INTO tbl_tmp(cod_prod, cant_prod) 
            SELECT codproducto, cantidad 
            FROM detallefactura 
            WHERE nofactura = no_factura;

            -- Actualizar existencias por cada producto
            WHILE a <= registros DO
                -- Obtener el producto y cantidad
                SELECT cod_prod, cant_prod 
                INTO cod_producto, cant_producto 
                FROM tbl_tmp 
                WHERE id = a;

                -- Obtener existencia actual
                SELECT existencia 
                INTO existencia_actual 
                FROM producto 
                WHERE codproducto = cod_producto;

                -- Actualizar nueva existencia
                SET nueva_existencia = existencia_actual + cant_producto;

                UPDATE producto 
                SET existencia = nueva_existencia 
                WHERE codproducto = cod_producto;

                SET a = a + 1;
            END WHILE;

            -- Cambiar estatus de la factura
            UPDATE factura 
            SET estatus = 2 
            WHERE nofactura = no_factura;

            -- Eliminar tabla temporal
            DROP TEMPORARY TABLE IF EXISTS tbl_tmp;

            -- Devolver la factura
            SELECT * 
            FROM factura 
            WHERE nofactura = no_factura;
        END IF;
    ELSE
        -- Si no existe la factura activa
        SELECT 0 AS factura;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dataDashboard` ()   BEGIN
    
    	DECLARE usuarios int;
        DECLARE clientes int;
        DECLARE proveedores int;
        DECLARE productos int;
        DECLARE ventas int;
        
        SELECT COUNT(*) INTO usuarios FROM usuario WHERE estatus != 10;
         SELECT COUNT(*) INTO clientes FROM cliente WHERE estatus != 10;
          SELECT COUNT(*) INTO proveedores FROM proveedor WHERE estatus != 10;
           SELECT COUNT(*) INTO productos FROM producto WHERE estatus != 10;
            SELECT COUNT(*) INTO ventas FROM factura WHERE fecha > CURDATE() AND estatus != 10;
            
            SELECT usuarios,clientes,proveedores,productos,ventas;
            
            
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (`id_detalle` INT, `token` VARCHAR(50))   BEGIN
          DELETE FROM detalle_temp WHERE correlativo = id_detalle;
          
          SELECT tmp.correlativo, tmp.codproducto,p.descripcion,tmp.cantidad,tmp.precio_venta FROM detalle_temp tmp
          INNER JOIN producto p 
          ON tmp.codproducto = p.codproducto
          WHERE tmp.token_user = token;
      END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (`cod_usuario` INT, `cod_cliente` INT, `token` VARCHAR(50))   BEGIN
        	DECLARE factura INT;
           
        	DECLARE registros INT;
            DECLARE total DECIMAL(10,2);
            
            DECLARE nueva_existencia int;
            DECLARE existencia_actual int;
            
            DECLARE tmp_cod_producto int;
            DECLARE tmp_cant_producto int;
            DECLARE a INT;
            SET a = 1;
            
            CREATE TEMPORARY TABLE tbl_tmp_tokenuser (
                	id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                	cod_prod BIGINT,
                	cant_prod int);
             SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
             
             IF registros > 0 THEN 
             	INSERT INTO tbl_tmp_tokenuser(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detalle_temp WHERE token_user = token;
                
                INSERT INTO factura(usuario,codcliente) VALUES(cod_usuario,cod_cliente);
                SET factura = LAST_INSERT_ID();
                
                INSERT INTO detallefactura(nofactura,codproducto,cantidad,precio_venta) SELECT (factura) as nofactura, codproducto,cantidad,precio_venta 				FROM detalle_temp WHERE token_user = token; 
                
                WHILE a <= registros DO
                	SELECT cod_prod,cant_prod INTO tmp_cod_producto,tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id = a;
                    SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = tmp_cod_producto;
                    
                    SET nueva_existencia = existencia_actual - tmp_cant_producto;
                    UPDATE producto SET existencia = nueva_existencia WHERE codproducto = tmp_cod_producto;
                    
                    SET a=a+1;
                    
                
                END WHILE; 
                
                SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
                UPDATE factura SET totalfactura = total WHERE nofactura = factura;
                DELETE FROM detalle_temp WHERE token_user = token;
                TRUNCATE TABLE tbl_tmp_tokenuser;
                SELECT * FROM factura WHERE nofactura = factura;
             
             ELSE
             SELECT 0;
 END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `nit` varchar(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `dateadd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nit`, `nombre`, `telefono`, `direccion`, `dateadd`, `usuario_id`, `estatus`) VALUES
(1, '0', 'CF', '1800998080', 'X', '2024-11-16 07:28:27', 1, 1),
(8, '123', 'Airimomo', '2147483647', 'Colonia villa villedas #47', '2024-11-04 23:02:50', 1, 0),
(9, '1234567891', 'Chise', '2147483647', 'Colonia avenida de las americas #404', '2024-11-04 23:07:43', 1, 1),
(10, '2147483647', 'Evillius Yon', '666', 'Avenida del asombro #72', '2024-11-14 17:44:04', 1, 0),
(11, '456789', 'Maria Jose', '2147483647', 'Avenida dos', '2024-11-14 23:23:55', 1, 1),
(12, '3837834', 'Josefina Alva', '2147483647', 'Avenida tres', '2024-11-14 23:52:32', 1, 1),
(13, '4567895', 'Serafina Jose', '2147483647', 'Avenida cuatro', '2024-11-15 01:03:47', 1, 1),
(14, '987654321', 'Luis', '67890987', 'Av. Los Robles #84 ', '2024-11-17 14:39:33', 1, 1),
(15, '33333333', 'Maria Luisa Perez', '2147483647', 'Av. Los Robles #104 ', '2024-11-19 00:05:45', 11, 1),
(16, '2147483647', 'Carina Loaiza', '1800998080', 'Avenida del asombro #24', '2024-11-19 00:16:44', 11, 0),
(17, '2147483647', 'Mark Lopez', '2147483647', 'Avenida del asombro #24', '2024-11-19 00:18:59', 1, 0),
(18, '2147483647', 'Juana Loera', '2147483647', 'Avenida del asombro #22', '2024-11-19 00:22:38', 1, 0),
(19, '2147483647', 'Katy Zamudio', '2147483647', 'Av. Los Robles #55', '2024-11-19 00:25:43', 1, 0),
(20, '2147483647', 'Katy Loreto', '2147483647', 'Av. Los Duraznos  #43', '2024-11-19 00:34:54', 1, 0),
(21, '77777777777', 'Maria Juana Loeira', '4444444444', 'Calle Roberto Agustin #45', '2024-11-19 00:37:52', 1, 1),
(22, '99999999999', 'Juan Ramos', '5555555555', 'Av. Los Robles #50', '2024-11-21 18:34:11', 1, 1),
(23, '9999999999', '', '', '', '2024-11-21 20:28:14', 1, 1),
(24, '6789', 'Adan', '5617889021', 'Av. Los Robles #104 ', '2024-11-27 12:55:40', 1, 1),
(25, '78878343873', 'Gonzalo', '83783478', 'Avenida del asombro #22', '2024-11-27 13:11:48', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `direccion` text NOT NULL,
  `iva` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `configuracion`
--

INSERT INTO `configuracion` (`id`, `nit`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `iva`) VALUES
(1, '555555555555', 'Dulce Pocilga', 'Grupo DulCo S.A de C.V', 7731680967, 'GrupoDulce.PoCo@gmai', 'Calzada Mario Lopez #12, Huesca, Edo.Mex', '16.00');

-- --------------------------------------------------------

--
-- Table structure for table `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(11) NOT NULL,
  `nofactura` bigint(11) DEFAULT NULL,
  `codproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(9, 4, 8, 1, '120.29'),
(10, 5, 3, 2, '24.65'),
(11, 5, 9, 3, '42.66'),
(13, 6, 3, 2, '24.65'),
(14, 6, 5, 1, '14999.99'),
(15, 6, 6, 2, '122.50'),
(16, 7, 8, 1, '120.99'),
(17, 8, 8, 1, '120.99'),
(18, 9, 9, 2, '25.00'),
(19, 10, 6, 1, '122.50'),
(20, 10, 7, 1, '64.99'),
(22, 11, 8, 1, '120.99'),
(23, 12, 5, 1, '14999.99'),
(24, 12, 6, 1, '122.50'),
(25, 12, 7, 1, '64.99'),
(26, 13, 5, 4, '14999.99'),
(27, 13, 6, 6, '122.50'),
(29, 14, 5, 2, '14999.99'),
(30, 14, 6, 5, '122.50'),
(32, 15, 5, 1, '14999.99'),
(33, 15, 6, 1, '122.50'),
(34, 15, 3, 1, '24.65'),
(35, 16, 6, 1, '122.50'),
(36, 16, 7, 1, '64.99'),
(37, 16, 8, 1, '120.99'),
(38, 17, 6, 1, '122.50'),
(39, 17, 8, 2, '120.99'),
(40, 18, 16, 2, '58.49'),
(41, 19, 17, 1, '65.49'),
(42, 19, 6, 1, '122.50'),
(43, 20, 17, 1, '65.49'),
(44, 20, 5, 1, '14999.99');

-- --------------------------------------------------------

--
-- Table structure for table `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `entradas`
--

CREATE TABLE `entradas` (
  `correlativo` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(1, 1, '2024-11-07 23:03:55', 2, '24999.00', 8),
(2, 2, '2024-11-09 12:53:03', 4, '19999.00', 1),
(3, 3, '2024-11-09 15:04:10', 5, '23.99', 1),
(4, 4, '2024-11-09 21:04:35', 2, '67.99', 1),
(5, 4, '2024-11-10 15:42:55', 4, '200.00', 1),
(6, 4, '2024-11-10 15:54:40', 4, '200.00', 1),
(7, 3, '2024-11-10 22:55:05', 10, '25.99', 1),
(8, 1, '2024-11-10 22:56:51', 3, '28999.00', 1),
(9, 2, '2024-11-10 22:58:28', 5, '59999.00', 1),
(10, 3, '2024-11-10 22:59:57', 5, '23.99', 1),
(11, 3, '2024-11-10 23:00:39', 5, '22.59', 1),
(12, 3, '2024-11-10 23:03:23', 5, '25.99', 1),
(13, 3, '2024-11-10 23:20:03', 2, '22.99', 1),
(14, 5, '2024-11-11 15:51:44', 2, '14999.99', 1),
(15, 6, '2024-11-13 00:16:33', 2, '120.00', 1),
(16, 6, '2024-11-13 00:16:44', 2, '125.00', 1),
(17, 7, '2024-11-13 01:15:14', 1, '64.99', 1),
(18, 8, '2024-11-13 01:16:04', 2, '120.29', 1),
(19, 9, '2024-11-13 01:17:06', 1, '25.99', 1),
(20, 10, '2024-11-14 15:04:56', 1, '39.99', 1),
(21, 10, '2024-11-14 15:05:09', 2, '68.99', 1),
(22, 11, '2024-11-14 17:48:42', 2, '78.99', 1),
(23, 11, '2024-11-14 17:48:57', 2, '80.00', 1),
(24, 12, '2024-11-14 18:06:11', 2, '34.00', 1),
(25, 12, '2024-11-14 18:06:37', 5, '70.99', 1),
(26, 9, '2024-11-15 13:59:09', 2, '50.99', 1),
(27, 13, '2024-11-17 12:19:01', 2, '45.62', 1),
(28, 9, '2024-11-17 14:21:00', 8, '25.00', 1),
(29, 8, '2024-11-17 14:21:22', 8, '120.99', 1),
(30, 7, '2024-11-17 15:24:27', 9, '64.99', 1),
(31, 6, '2024-11-17 15:25:07', 15, '122.50', 1),
(32, 5, '2024-11-17 15:25:47', 23, '14999.99', 1),
(33, 14, '2024-11-18 00:48:28', 32, '360.60', 1),
(34, 15, '2024-11-20 23:17:25', 23, '78.00', 1),
(35, 15, '2024-11-20 23:18:32', 79, '24.00', 1),
(36, 16, '2024-11-21 18:31:45', 10, '59.99', 1),
(37, 16, '2024-11-21 18:33:18', 2, '50.99', 1),
(38, 17, '2024-11-21 20:24:34', 10, '59.99', 1),
(39, 17, '2024-11-21 20:25:48', 10, '70.99', 1),
(40, 18, '2024-11-27 13:14:33', 56, '200.00', 1),
(41, 18, '2024-11-27 13:15:00', 67, '150.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estatus`) VALUES
(3, '2024-11-16 08:29:36', 1, 9, '242.79', 1),
(4, '2024-11-16 13:19:17', 1, 11, '120.29', 2),
(5, '2024-11-17 12:15:15', 1, 12, '177.28', 1),
(6, '2024-11-17 14:16:41', 1, 11, '15294.29', 1),
(7, '2024-11-17 14:21:54', 1, 11, '120.99', 2),
(8, '2024-11-17 14:23:16', 1, 12, '120.99', 1),
(9, '2024-11-17 14:26:41', 1, 9, '50.00', 1),
(10, '2024-11-17 14:39:58', 1, 14, '187.49', 2),
(11, '2024-11-17 14:43:53', 1, 11, '120.99', 2),
(12, '2024-11-17 15:28:34', 1, 13, '15187.48', 2),
(13, '2024-11-19 00:15:17', 1, 11, '60734.96', 1),
(14, '2024-11-19 00:15:56', 11, 15, '30612.48', 1),
(15, '2024-11-19 00:44:44', 1, 21, '15147.14', 1),
(16, '2024-11-19 15:36:57', 1, 11, '308.48', 1),
(17, '2024-11-20 23:20:32', 1, 11, '364.48', 2),
(18, '2024-11-21 18:35:28', 1, 22, '116.98', 2),
(19, '2024-11-21 20:28:22', 1, 22, '187.99', 2),
(20, '2024-11-27 13:16:18', 1, 11, '15065.48', 2);

-- --------------------------------------------------------

--
-- Table structure for table `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `existencia` int(11) DEFAULT NULL,
  `foto` text DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `precio`, `existencia`, `foto`, `date_add`, `usuario_id`, `estatus`) VALUES
(1, 'Moto Alpina Derrapante', 12, '27399.00', 5, 'moto.jpg', '2024-11-07 23:03:55', 8, 0),
(2, 'Sala completa 4 personas', 21, '42221.22', 9, 'img_producto.png', '2024-11-09 12:53:03', 1, 0),
(3, 'Lapicero doble punta cruzada hacia atrás', 1, '24.65', 27, 'img_producto.png', '2024-11-09 15:04:10', 1, 1),
(4, 'Baño triple entrada plus ultra', 24, '185.00', 10, 'img_producto.png', '2024-11-09 21:04:35', 1, 0),
(5, 'Silla transversal caterpilar', 26, '14999.99', 17, 'img_producto.png', '2024-11-11 15:51:44', 1, 1),
(6, 'Botella doble vista impermeable 2', 19, '122.50', 3, 'img_producto.png', '2024-11-13 00:16:33', 1, 1),
(7, 'Anti-estres deluxe', 13, '64.99', 9, 'img_eb32602b33d19773a8623a3296248190.jpg', '2024-11-13 01:15:14', 1, 1),
(8, 'Figuras coleccionables 8cmx5cm', 1, '120.99', 7, 'img_producto.png', '2024-11-13 01:16:04', 1, 1),
(9, 'Insignia mejor puerqui-persona', 24, '25.00', 6, 'img_989ec9c88a508ba50ddcfccfac4dc4b2.jpg', '2024-11-13 01:17:06', 1, 1),
(10, 'Poster Capybara Capitán', 12, '59.32', 3, 'img_e40594993d6212f29a12032bf11290b1.jpg', '2024-11-14 15:04:56', 1, 0),
(11, 'Taza de recuerdo', 12, '79.50', 4, 'img_producto.png', '2024-11-14 17:48:42', 1, 0),
(12, 'a', 13, '60.42', 7, 'img_ef0492777d61cb1113abfb94afbd68eb.jpg', '2024-11-14 18:06:11', 1, 0),
(13, 'Figuritas porcelana a mano', 19, '45.62', 2, 'img_b601502b5dc7f4530c9bbacdf2b3c336.jpg', '2024-11-17 12:19:01', 1, 1),
(14, 'Peluche Cerdito 360° Triple mortal hacia atrás', 29, '360.60', 32, 'img_ef6af5bebb968aa2f7130ea47a95566e.jpg', '2024-11-18 00:48:28', 1, 0),
(15, 'Peluche', 19, '36.18', 102, 'img_producto.png', '2024-11-20 23:17:25', 1, 0),
(16, 'Alcancia', 24, '58.49', 12, 'img_producto.png', '2024-11-21 18:31:45', 1, 1),
(17, 'Alcancia 2', 13, '65.49', 20, 'img_producto.png', '2024-11-21 20:24:34', 1, 1),
(18, 'Alcancia 3', 19, '172.76', 123, 'img_producto.png', '2024-11-27 13:14:33', 1, 1);

--
-- Triggers `producto`
--
DELIMITER $$
CREATE TRIGGER `entradas_A_I` AFTER INSERT ON `producto` FOR EACH ROW BEGIN 
	INSERT INTO entradas(codproducto, cantidad, precio, usuario_id)
    VALUES (new.codproducto, new.existencia, new.precio, new.usuario_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `proveedor`
--

CREATE TABLE `proveedor` (
  `codproveedor` int(11) NOT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` bigint(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `proveedor`
--

INSERT INTO `proveedor` (`codproveedor`, `proveedor`, `contacto`, `telefono`, `direccion`, `date_add`, `usuario_id`, `estatus`) VALUES
(1, 'BIC', 'Claudia Rosales', 328827299858, 'Avenida las Americas', '2024-11-06 23:08:05', 1, 1),
(12, 'Cesar S.A', 'Maria Suarez', 7731688445, 'Colonia villa villar #4', '2024-11-06 23:47:49', 1, 1),
(13, 'Proveedor A', 'Juan Pérez', 5551234567, 'Av. Siempre Viva 123, Ciudad', '2024-11-07 01:12:36', 1, 1),
(17, 'PROVEEDOR B', 'Alex Marquez', 934380823, 'Avenida siempre viva #6789', '2024-11-07 01:15:12', 1, 0),
(19, 'Proveedor C', 'Carlos García', 5553456789, 'Av. Insurgentes 789, Ciudad', '2024-11-07 01:16:38', 11, 1),
(20, 'Proveedor D', 'Laura Martínez', 5554567890, 'Calle Reforma 101, Ciudad', '2024-11-07 01:16:38', 4, 0),
(21, 'Proveedor E', 'Ana Torres', 5555678901, 'Calle Juárez 202, Ciudad', '2024-11-07 01:16:38', 5, 1),
(23, 'Proveedor C', 'Carlos García', 5553456789, 'Av. Insurgentes 789, Ciudad', '2024-11-07 01:16:46', 11, 0),
(24, 'Proveedor D', 'Laura Martínez', 5554567890, 'Calle Reforma 101, Ciudad', '2024-11-07 01:16:46', 4, 1),
(25, 'Proveedor E', 'Ana Torres', 5555678901, 'Calle Juárez 202, Ciudad', '2024-11-07 01:16:46', 5, 0),
(26, 'Proveedor F', 'Ricardo Sánchez', 5556789012, 'Av. Hidalgo 303, Ciudad', '2024-11-07 01:16:46', 12, 1),
(27, 'Proveedor G', 'Sandra Jiménez', 5557890123, 'Av. Morelos 404, Ciudad', '2024-11-07 01:16:46', 7, 1),
(28, 'Proveedor H', 'Miguel Ramírez', 5558901234, 'Calle Revolución 505, Ciudad', '2024-11-07 01:16:46', 8, 1),
(29, 'Proveedor I', 'Lucía Cruz', 5559012345, 'Av. Madero 606, Ciudad', '2024-11-07 01:16:46', 9, 1),
(30, 'Proveedor J', 'José Vargas', 5550123456, 'Av. Victoria 707, Ciudad', '2024-11-07 01:16:46', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Vendedor');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `usuario` varchar(15) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `estatus`) VALUES
(1, 'Karol', 'Karol.mas@gmail.com', 'admin', '202cb962ac59075b964b07152d234b70', 1, 1),
(4, 'Mizuki', 'Mizu5@hotmail.com', 'Mizu', '202cb962ac59075b964b07152d234b70', 3, 1),
(5, 'EnaShinonome', 'EnaShi@gmail.com', 'Enanan', '202cb962ac59075b964b07152d234b70', 2, 0),
(7, 'Ayase ', 'Ayamo@gmail.com', 'Ayase', '78e1672e3d4198c1ccd0e44a24406dee', 3, 0),
(8, 'Reyna', 'WonderRui26@hotmail.com', 'RuiKa', 'bae253ce4567372e91f550739c7183da', 3, 1),
(9, 'Emu', 'EmuOto@gmail.com', 'Wonderhoy', '63173262bb4c8f4d7d52cd89d35519bf', 3, 0),
(10, 'Nene', 'Kusanene@gmail.com', 'Nene', '00d1a7241124ab7fb5a413b0ed871662', 2, 1),
(11, 'Tiago', 'Tsutenma@yahoo.com', 'PegasusTenma', '25f9e794323b453885f5181f1b624d0b', 3, 1),
(12, 'Mafuyu', 'MafuHina@hotmail.com', 'Yuki', '9075f2489826a4f561f7047a7a79bcd5', 3, 1),
(13, 'Kanade', 'KanaYoisa@gmail.com', 'Kana', '3a39bc64566fd1829b206a8b4f23cca8', 2, 1),
(14, 'Akito', 'Akishino@gmail.com', 'Aki', '69ec293c3e57a041be17baca949cf931', 3, 1),
(15, 'An', 'Anshira@gmail.com', 'Anny', '978b697963cfc5abfced8a4b611977c9', 3, 1),
(16, 'Toya', 'Aoyatoya@hotmail.com', 'Toya', '5464018b53efd6660595dfd351686710', 2, 1),
(17, 'Kohanne', 'AzuKohanne@gmail.com', 'Koha', '6e9b53f8be11e32dc2d28d1e08ab4133', 2, 1),
(18, 'Poppy', 'Poppy@gmail.com', 'Jonathanus', '202cb962ac59075b964b07152d234b70', 1, 1),
(19, 'uniqua', 'uniquita2345@gmail.com', 'uniquaj', '202cb962ac59075b964b07152d234b70', 2, 1),
(20, 'Mary', 'Marymori@hotmail.com', 'Marisca', '202cb962ac59075b964b07152d234b70', 3, 1),
(21, 'Jonathanus', 'Yonj@yahoo.com', 'Navarrot', '202cb962ac59075b964b07152d234b70', 3, 1),
(22, 'Evil Yon', 'YonTheKiller@gmail.com', 'Navarrots', '202cb962ac59075b964b07152d234b70', 3, 1),
(23, 'Sabrina', 'Sabrinuchis@hotmail.com', 'Sabriniux', '0243676cad978f78d4f504e8dd07192f', 2, 1),
(24, 'Jonathan', 'Jhon34543@gmail.com', 'Jhon', '202cb962ac59075b964b07152d234b70', 3, 1),
(25, 'Juan', 'Juan1@gmail.com', 'Juan2', '202cb962ac59075b964b07152d234b70', 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `nofactura` (`nofactura`);

--
-- Indexes for table `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `nofactura` (`token_user`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indexes for table `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `codcliente` (`codcliente`);

--
-- Indexes for table `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `proveedor` (`proveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`codproveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`);

--
-- Constraints for table `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_1` FOREIGN KEY (`nofactura`) REFERENCES `factura` (`nofactura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entradas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`codcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`codproveedor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
