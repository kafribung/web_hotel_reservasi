-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2021 at 02:58 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_hotel_reverse`
--

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `msg_id` int(10) UNSIGNED NOT NULL,
  `msg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `msg_from` tinytext NOT NULL,
  `msg_body` mediumtext NOT NULL,
  `msg_email` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `rsrv_id` int(10) NOT NULL,
  `rsrv_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rsrv_first_name` varchar(20) NOT NULL,
  `rsrv_last_name` varchar(20) NOT NULL,
  `rsrv_email` varchar(20) DEFAULT NULL,
  `rsrv_contact` varchar(15) NOT NULL,
  `rsrv_room` text NOT NULL,
  `rsrv_bed` int(11) DEFAULT NULL,
  `rsrv_pillow` int(11) DEFAULT NULL,
  `rsrv_towel` int(11) DEFAULT NULL,
  `rsrv_kit` int(11) DEFAULT NULL,
  `rsrv_start` date NOT NULL,
  `rsrv_end` date NOT NULL,
  `rsrv_guest` varchar(5) DEFAULT NULL,
  `rsrv_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`rsrv_id`, `rsrv_timestamp`, `rsrv_first_name`, `rsrv_last_name`, `rsrv_email`, `rsrv_contact`, `rsrv_room`, `rsrv_bed`, `rsrv_pillow`, `rsrv_towel`, `rsrv_kit`, `rsrv_start`, `rsrv_end`, `rsrv_guest`, `rsrv_notes`) VALUES
(41, '2021-02-14 01:57:11', 'Kafri', 'Bung', 'kafri@bung.com', '12345678997', 'Standard', 3, 5, 7, 0, '2021-02-15', '2021-02-23', '8', 'Nemo illum duis est');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(8) NOT NULL,
  `user_name` varchar(12) NOT NULL,
  `user_pw` varchar(20) NOT NULL,
  `user_full` varchar(20) DEFAULT NULL,
  `user_role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_pw`, `user_full`, `user_role`) VALUES
(201305, 'admin', 'admin', 'administrator', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`rsrv_id`),
  ADD UNIQUE KEY `rsrv_name` (`rsrv_first_name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inbox`
--
ALTER TABLE `inbox`
  MODIFY `msg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `rsrv_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201306;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
