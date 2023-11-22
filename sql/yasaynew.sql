-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2023 at 03:31 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yasaynew`
--

-- --------------------------------------------------------

--
-- Table structure for table `buyingtransactions`
--

CREATE TABLE `buyingtransactions` (
  `invoice_number` varchar(50) DEFAULT NULL,
  `transaction_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `grain_type` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buyingtransactions`
--

INSERT INTO `buyingtransactions` (`invoice_number`, `transaction_id`, `customer_id`, `transaction_date`, `grain_type`, `quantity`, `total_cost`, `status`) VALUES
(NULL, 1, 277, '2023-11-14', 'Rice', 12, '228.00', NULL),
(NULL, 2, 278, '2023-11-14', 'Corn', 100, '0.00', NULL),
(NULL, 3, 279, '2023-11-14', 'Rice', 2, '38.00', NULL),
('2023317925', 4, 281, '2023-11-15', 'Rice', 2, '38.00', NULL),
('2023117471', 5, 282, '2023-11-15', 'Rice', 12, '228.00', NULL),
('2023569008', 6, 283, '2023-11-15', 'Rice', 12, '228.00', NULL),
('2023985483', 7, 284, '2023-11-15', 'Rice', 12, '228.00', NULL),
('2023532394', 8, 285, '2023-11-15', NULL, 22, '0.00', NULL),
('2023904627', 9, 286, '2023-11-15', 'Rice', 12, '228.00', NULL),
('2023397426', 10, 287, '2023-11-15', 'Rice', 2, '38.00', NULL),
('2023279092', 11, 288, '2023-11-15', 'Rice', 23, '437.00', NULL),
('2023386973', 12, 289, '2023-11-15', 'Rice', 12, '228.00', NULL),
('2023261359', 13, 290, '2023-11-15', 'Rice', 1212, '23028.00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `contact_number`, `address`) VALUES
(1, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(2, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(3, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(4, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(5, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(6, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(7, 'Admin', '09772811758', 'Igpit, Opol CDOC'),
(8, 'Admin', '09772811758', 'Igpit, Opol CDOC'),
(9, 'Admin', '09772811758', 'Igpit, Opol CDOC'),
(10, 'Admin', '09772811758', 'Igpit, Opol CDOC'),
(11, 'Admin', '09772811758', NULL),
(12, 'Admin', '09772811758', NULL),
(13, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(14, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(15, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(16, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(17, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(18, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(19, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(20, 'Admin', '09772811758', 'Lapasan CDOC'),
(21, 'Admin', '09772811758', 'Lapasan CDOC'),
(22, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(23, 'Admin', '09772811758', NULL),
(24, 'Admin', '09772811758', NULL),
(25, 'Admin', '09772811758', NULL),
(26, 'Admin', '09772811758', 'Lapasan CDOC'),
(27, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(28, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(29, 'Admin', '09772811758', NULL),
(30, 'Admin', '09772811758', NULL),
(31, 'Admin', '09772811758', NULL),
(32, 'Admin', '09772811758', NULL),
(33, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(34, 'Admin', '09772811758', NULL),
(35, 'Admin', '09772811758', NULL),
(36, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(37, 'Admin', '09772811758', NULL),
(38, 'Admin', '09772811758', NULL),
(39, 'Admin', '09772811758', NULL),
(40, 'Admin', '09772811758', NULL),
(41, 'Admin', '09772811758', NULL),
(42, 'Admin', '09772811758', NULL),
(43, 'Admin', '09772811758', NULL),
(44, 'Admin', '09772811758', NULL),
(45, 'Admin', '09772811758', NULL),
(46, 'Admin', '09772811758', NULL),
(47, 'Admin', '09772811758', NULL),
(48, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(49, 'Admin', '09772811758', NULL),
(50, 'Admin', '09772811758', NULL),
(51, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(52, 'Admin', '09772811758', NULL),
(53, 'Admin', '09772811758', NULL),
(54, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(55, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(56, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(57, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(58, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(59, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(60, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(61, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(62, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(63, 'Admin', '09772811758', 'Lapasan CDOC'),
(64, 'Admin', '09772811758', NULL),
(65, 'Jiro Lobaton', '09772811758', 'Lapasan CDOC'),
(66, 'Admin', '09772811758', NULL),
(67, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(68, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(69, 'Admin', '09772811758', NULL),
(70, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(71, 'Admin', '09772811758', NULL),
(72, 'Jiro Lobaton', '09772811758', 'Bugo CDOC'),
(73, 'Jiro Lobaton', '09772811758', 'Bugo CDOC'),
(74, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(75, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(76, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(77, 'Jiro Lobaton', '09772811758', 'Bugo CDOC'),
(78, 'Admin', '09772811758', NULL),
(79, 'Admin', '09772811758', NULL),
(80, 'Admin', '09772811758', NULL),
(81, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(82, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(83, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(84, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(85, 'Admin', '09772811758', NULL),
(86, 'Admin', '09772811758', NULL),
(87, 'Admin', '09772811758', NULL),
(88, 'Admin', '09772811758', NULL),
(89, 'Admin', '09772811758', NULL),
(90, 'Admin', '09772811758', 'Lapasan CDOC'),
(91, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(92, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(93, 'Admin', '09772811758', 'Lapasan CDOC'),
(94, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(95, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(96, 'Admin', '09772811758', NULL),
(97, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(98, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(99, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(100, 'Admin', '09772811758', 'Lapasan CDOC'),
(101, 'Admin', '09772811758', 'Lapasan CDOC'),
(102, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(103, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(104, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(105, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(106, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(107, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(108, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(109, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(110, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(111, 'Admin', '09772811758', 'Bugo CDOC'),
(112, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(113, 'Admin', '09772811758', 'Bugo CDOC'),
(114, 'Admin', '09772811758', NULL),
(115, 'Admin', '09772811758', NULL),
(116, 'Admin', '09772811758', NULL),
(117, 'Admin', '09772811758', NULL),
(118, 'Admin', '09772811758', NULL),
(119, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(120, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(121, 'Admin', '09772811758', NULL),
(122, 'Admin', '09772811758', NULL),
(123, 'Admin', '09772811758', NULL),
(124, 'Admin', '09772811758', NULL),
(125, 'Admin', '09772811758', NULL),
(126, 'Admin', '09772811758', NULL),
(127, 'Admin', '09772811758', NULL),
(128, 'Admin', '09772811758', NULL),
(129, 'Admin', '09772811758', NULL),
(130, 'Admin', '09772811758', NULL),
(131, 'Admin', '09772811758', NULL),
(132, 'Admin', '09772811758', NULL),
(133, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(134, 'Admin', '09772811758', NULL),
(135, 'Admin', '09772811758', NULL),
(136, 'Admin', '09772811758', NULL),
(137, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(138, 'Admin', '09772811758', NULL),
(139, 'Admin', '09772811758', NULL),
(140, 'Admin', '09772811758', NULL),
(141, 'Admin', '09772811758', NULL),
(142, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(143, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(144, 'Admin', '09772811758', NULL),
(145, 'Admin', '09772811758', NULL),
(146, 'Admin', '09772811758', NULL),
(147, 'Admin', '09772811758', NULL),
(148, 'Admin', '09772811758', NULL),
(149, 'Admin', '09772811758', NULL),
(150, 'Admin', '09772811758', NULL),
(151, 'Admin', '09772811758', NULL),
(152, 'Admin', '09772811758', NULL),
(153, 'Admin', '09772811758', NULL),
(154, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(155, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(156, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(157, 'Admin', '09772811758', NULL),
(158, 'Admin', '09772811758', NULL),
(159, 'Admin', '09772811758', NULL),
(160, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(161, 'Admin', '09772811758', NULL),
(162, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(163, 'Admin', '09772811758', NULL),
(164, 'Admin', '09772811758', NULL),
(165, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(166, 'Syrus Lapinid', '09772811758', 'Bugo CDOC'),
(167, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(168, 'Admin', '09772811758', NULL),
(169, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(170, 'Admin', '09772811758', NULL),
(171, 'Jun Ray Floria', '09772811758', 'Lapasan CDOC'),
(172, 'Admin', '09772811758', NULL),
(173, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(174, 'Kip Bulala', '09772811758', 'Bugo CDOC'),
(175, 'Admin', '09772811758', NULL),
(176, 'Admin', '09772811758', NULL),
(177, 'Admin', '09772811758', NULL),
(178, 'Admin', '09772811758', NULL),
(179, 'Admin', '09772811758', NULL),
(180, 'Admin', '09772811758', NULL),
(181, 'Admin', '09772811758', NULL),
(182, 'Admin', '09772811758', NULL),
(183, 'Admin', '09772811758', NULL),
(184, 'Admin', '09772811758', NULL),
(185, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(186, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(187, 'Admin', '09772811758', NULL),
(188, 'Syrus Lapinid', '09772811758', 'Bugo CDOC'),
(189, 'Admin', '09772811758', NULL),
(190, 'Admin', '09772811758', NULL),
(191, 'Admin', '09772811758', NULL),
(192, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(193, 'Admin', '09772811758', NULL),
(194, 'Admin', '09772811758', NULL),
(195, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(196, 'Admin', '09772811758', NULL),
(197, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(198, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(199, 'Admin', '09772811758', NULL),
(200, 'Admin', '09772811758', NULL),
(201, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(202, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(203, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(204, 'Kip Bulala', '09772811758', 'Lapasan CDOC'),
(205, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(206, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(207, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(208, 'Syrus Lapinid', '09772811758', 'Canitoan CDOC'),
(209, 'Kip Bulala', '09772811758', 'Canitoan CDOC'),
(210, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(211, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(212, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(213, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(214, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(215, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(216, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(217, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(218, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(219, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(220, 'Cherry Ann Daculos', '09772811758', 'Bugo CDOC'),
(221, 'Cherry Ann Daculos', '09772811758', 'Lapasan CDOC'),
(222, 'Cherry Ann Daculos', '09772811758', 'Lapasan CDOC'),
(223, 'Cherry Ann Daculos', '09772811758', 'Lapasan CDOC'),
(224, 'Cherry Ann Daculos', '09772811758', 'Lapasan CDOC'),
(225, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(226, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(227, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(228, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(229, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(230, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(231, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(232, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(233, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(234, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(235, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(236, 'Cherry Ann Daculos', '09772811758', 'Lapasan CDOC'),
(237, 'Cherry Ann Daculos', '09772811758', 'Lapasan CDOC'),
(238, 'Cherry Ann Daculos', '09772811758', 'Lapasan CDOC'),
(239, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(240, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(241, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(242, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(243, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(244, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(245, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(246, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(247, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(248, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(249, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(250, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(251, 'Kip Bulala', '09772811758', 'Canitoan CDOC'),
(252, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(253, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(254, 'Jiro Lobaton', '09772811758', 'Bugo CDOC'),
(255, 'Jiro Lobaton', '09772811758', 'Upper Carmen'),
(256, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(257, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(258, 'Cherry Ann Daculos', '09772811758', 'Canitoan CDOC'),
(259, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(260, 'Jun Ray Floria', '09772811758', 'Bugo CDOC'),
(261, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(262, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(263, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(264, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(265, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(266, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(267, 'Syrus Lapinid', '09772811758', 'Canitoan CDOC'),
(268, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(269, 'Jiro Lobaton', '09772811758', 'Bugo CDOC'),
(270, 'Admin', '09772811758', 'Canitoan CDOC'),
(271, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(272, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(273, 'Admin', '09772811758', 'Bugo CDOC'),
(274, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(275, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(276, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(277, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(278, 'Syrus Lapinid', '09772811758', 'Lapasan CDOC'),
(279, 'Jiro Lobaton', '09772811758', 'Canitoan CDOC'),
(280, 'Admin', '09772811758', NULL),
(281, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(282, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(283, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(284, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(285, 'Admin', '09772811758', 'Canitoan CDOC'),
(286, 'Jun Ray Floria', '09772811758', 'Canitoan CDOC'),
(287, 'Admin', '09772811758', 'Canitoan CDOC'),
(288, 'Admin', '09772811758', 'Canitoan CDOC'),
(289, 'Jun Ray Floria', '12', 'Canitoan CDOC'),
(290, 'Admin', '09772811758', 'Canitoan CDOC');

-- --------------------------------------------------------

--
-- Table structure for table `grainprice`
--

CREATE TABLE `grainprice` (
  `grain_id` int(11) NOT NULL,
  `grain_type` varchar(50) NOT NULL,
  `grain_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grainprice`
--

INSERT INTO `grainprice` (`grain_id`, `grain_type`, `grain_price`) VALUES
(1, 'Rice', '19.00'),
(2, 'Corn', '16.00');

-- --------------------------------------------------------

--
-- Table structure for table `grainsstock`
--

CREATE TABLE `grainsstock` (
  `id` int(11) NOT NULL,
  `grain_type` varchar(255) NOT NULL,
  `available_quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_in_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grainsstock`
--

INSERT INTO `grainsstock` (`id`, `grain_type`, `available_quantity`, `created_at`, `updated_at`, `stock_in_quantity`) VALUES
(1, 'Rice', 2, '2023-11-14 16:32:58', '2023-11-14 17:05:25', 12),
(2, 'Corn', 100, '2023-11-14 16:41:38', '2023-11-14 16:41:38', 100),
(3, 'Rice', 2, '2023-11-14 16:46:11', '2023-11-14 16:46:11', 2),
(4, 'Rice', 2, '2023-11-15 10:33:35', '2023-11-15 10:33:35', 2),
(5, 'Rice', 12, '2023-11-15 12:31:31', '2023-11-15 12:31:31', 12),
(6, 'Rice', 12, '2023-11-15 12:56:28', '2023-11-15 12:56:28', 12),
(7, 'Rice', 12, '2023-11-15 13:07:52', '2023-11-15 13:07:52', 12),
(8, 'Rice', 12, '2023-11-15 13:53:04', '2023-11-15 13:53:04', 12),
(9, 'Rice', 2, '2023-11-15 14:19:36', '2023-11-15 14:19:36', 2),
(10, 'Rice', 23, '2023-11-15 14:20:04', '2023-11-15 14:20:04', 23),
(11, 'Rice', 12, '2023-11-15 14:21:28', '2023-11-15 14:21:28', 12),
(12, 'Rice', 1212, '2023-11-15 14:25:18', '2023-11-15 14:25:18', 1212);

-- --------------------------------------------------------

--
-- Table structure for table `milledgrains`
--

CREATE TABLE `milledgrains` (
  `grain_id` int(11) NOT NULL,
  `grain_type` varchar(50) NOT NULL,
  `variety` varchar(50) NOT NULL,
  `price_per_sack` decimal(10,2) NOT NULL,
  `price_per_kilo` decimal(10,2) NOT NULL,
  `available_stock` int(11) NOT NULL,
  `available_stock_sack` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `milledgrains`
--

INSERT INTO `milledgrains` (`grain_id`, `grain_type`, `variety`, `price_per_sack`, `price_per_kilo`, `available_stock`, `available_stock_sack`) VALUES
(1, 'Rice', 'White Rice', '1600.00', '58.00', 6, 0),
(2, 'Rice', 'Red Rice', '1400.00', '48.00', 0, 0),
(3, 'Corn', 'Cracked Corn', '900.00', '40.00', 0, 0),
(4, 'Corn', 'Yellow Grits', '950.00', '42.00', 0, 0),
(5, 'Corn', 'Yellow Corn Bran', '900.00', '41.00', 0, 0),
(6, 'Corn', 'White Corn Bran', '800.00', '38.00', 0, 0),
(7, 'Corn', 'White Corn Grits #10', '850.00', '40.00', 0, 0),
(8, 'Corn', 'White Corn Grits #12', '825.00', '41.00', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `millingfees`
--

CREATE TABLE `millingfees` (
  `grain_type` varchar(50) NOT NULL,
  `milling_fee` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `millingfees`
--

INSERT INTO `millingfees` (`grain_type`, `milling_fee`) VALUES
('Corn', '16.00'),
('Rice', '19.00');

-- --------------------------------------------------------

--
-- Table structure for table `millingtransactions`
--

CREATE TABLE `millingtransactions` (
  `invoice_number` varchar(50) DEFAULT NULL,
  `transaction_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `grain_type` varchar(50) NOT NULL,
  `milling_variety` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_fee` int(11) NOT NULL,
  `delivery_method` varchar(50) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `date_completed` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `transaction_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `millingtransactions`
--

INSERT INTO `millingtransactions` (`invoice_number`, `transaction_id`, `customer_id`, `transaction_date`, `grain_type`, `milling_variety`, `quantity`, `total_fee`, `delivery_method`, `delivery_date`, `date_completed`, `status`, `transaction_type`) VALUES
(NULL, 1, 280, '2023-11-14', 'Rice', 'White Rice', 5, 0, '', NULL, '2023-11-14', 'Completed', '');

-- --------------------------------------------------------

--
-- Table structure for table `sellingtransactions`
--

CREATE TABLE `sellingtransactions` (
  `invoice_number` varchar(50) DEFAULT NULL,
  `transaction_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `grain_type` varchar(50) NOT NULL,
  `milling_variety` varchar(50) NOT NULL,
  `scale_type` varchar(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `delivery_method` varchar(50) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `redry_status` varchar(11) DEFAULT NULL,
  `replacement_status` varchar(11) DEFAULT NULL,
  `replacement_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'stockman', 'stockman', 'stockman');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buyingtransactions`
--
ALTER TABLE `buyingtransactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `grainprice`
--
ALTER TABLE `grainprice`
  ADD PRIMARY KEY (`grain_id`);

--
-- Indexes for table `grainsstock`
--
ALTER TABLE `grainsstock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `milledgrains`
--
ALTER TABLE `milledgrains`
  ADD PRIMARY KEY (`grain_id`);

--
-- Indexes for table `millingfees`
--
ALTER TABLE `millingfees`
  ADD PRIMARY KEY (`grain_type`);

--
-- Indexes for table `millingtransactions`
--
ALTER TABLE `millingtransactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `sellingtransactions`
--
ALTER TABLE `sellingtransactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buyingtransactions`
--
ALTER TABLE `buyingtransactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT for table `grainprice`
--
ALTER TABLE `grainprice`
  MODIFY `grain_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grainsstock`
--
ALTER TABLE `grainsstock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `milledgrains`
--
ALTER TABLE `milledgrains`
  MODIFY `grain_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `millingtransactions`
--
ALTER TABLE `millingtransactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sellingtransactions`
--
ALTER TABLE `sellingtransactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buyingtransactions`
--
ALTER TABLE `buyingtransactions`
  ADD CONSTRAINT `buyingtransactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `sellingtransactions`
--
ALTER TABLE `sellingtransactions`
  ADD CONSTRAINT `sellingtransactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
