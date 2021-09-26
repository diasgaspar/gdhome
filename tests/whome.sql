-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: fdb3.awardspace.com
-- Generation Time: Dec 29, 2016 at 09:51 AM
-- Server version: 5.7.16-log
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `whome`
--
CREATE DATABASE IF NOT EXISTS `whome` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `whome`;

-- --------------------------------------------------------

--
-- Table structure for table `DoorData`
--

CREATE TABLE `DoorData` (
  `TimeStamp` datetime NOT NULL,
  `State` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `DoorData`
--

INSERT INTO `DoorData` (`TimeStamp`, `State`) VALUES
('2016-12-28 11:27:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `DoorDataHourly`
--

CREATE TABLE `DoorDataHourly` (
  `TimeStamp` datetime NOT NULL,
  `Freq` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `Logs`
--

CREATE TABLE `Logs` (
  `TimeStampPhp` datetime NOT NULL,
  `Log` varchar(64) NOT NULL,
  `TimeStampDb` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

---------

--
-- Table structure for table `PowerDevices`
--

CREATE TABLE `PowerDevices` (
  `deviceId` varchar(11) NOT NULL,
  `Power` int(11) NOT NULL,
  `Desc` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `PowerTracking`
--

CREATE TABLE `PowerTracking` (
  `TimeStamp` datetime NOT NULL,
  `deviceId` varchar(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Table structure for table `RawData`
--

CREATE TABLE `RawData` (
  `TimeStamp` datetime NOT NULL,
  `Temp` int(11) NOT NULL,
  `RxI` int(11) NOT NULL COMMENT 'I (ampere*10)',
  `RxPw` int(11) NOT NULL COMMENT 'watt'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Table structure for table `RawDataDaily`
--

CREATE TABLE `RawDataDaily` (
  `TimeStamp` datetime NOT NULL,
  `Temp` int(11) NOT NULL,
  `TempMax` int(11) NOT NULL,
  `TempMin` int(11) NOT NULL,
  `RxI` int(11) NOT NULL,
  `Ewh` int(11) NOT NULL,
  `RxPwMin` int(11) NOT NULL,
  `RxPwMax` int(11) NOT NULL,
  `EwhF` int(11) NOT NULL COMMENT 'P fora vazio',
  `EwhV` int(11) NOT NULL COMMENT 'P vazio'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Table structure for table `RawDataHourly`
--

CREATE TABLE `RawDataHourly` (
  `TimeStamp` datetime NOT NULL,
  `Temp` int(11) NOT NULL,
  `RxI` int(11) NOT NULL,
  `RxPw` int(11) NOT NULL,
  `RxPwF` int(11) NOT NULL COMMENT 'P fora vazio',
  `RxPwV` int(11) NOT NULL COMMENT 'P vazio'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `User` varchar(40) NOT NULL,
  `Password` varchar(40) NOT NULL,
  `Email` varchar(60) NOT NULL,
  `IsSMSAlertOn` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `WallMeas` (
  `TimeStamp` datetime NOT NULL,
  `Ekwh` int(11) NOT NULL,
  `EkwhF` int(11) NOT NULL,
  `EkwhV` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--

--
-- Indexes for table `DoorData`
--
ALTER TABLE `DoorData`
  ADD PRIMARY KEY (`TimeStamp`);

--
-- Indexes for table `DoorDataHourly`
--
ALTER TABLE `DoorDataHourly`
  ADD PRIMARY KEY (`TimeStamp`);

--
-- Indexes for table `PowerTracking`
--
ALTER TABLE `PowerTracking`
  ADD PRIMARY KEY (`TimeStamp`,`deviceId`);

--
-- Indexes for table `RawData`
--
ALTER TABLE `RawData`
  ADD UNIQUE KEY `TimeStamp` (`TimeStamp`);

--
-- Indexes for table `RawDataDaily`
--
ALTER TABLE `RawDataDaily`
  ADD UNIQUE KEY `TimeStamp` (`TimeStamp`);

--
-- Indexes for table `RawDataHourly`
--
ALTER TABLE `RawDataHourly`
  ADD UNIQUE KEY `TimeStamp` (`TimeStamp`);

--
-- Indexes for table `RawDataHourlyOrig`
--
ALTER TABLE `RawDataHourlyOrig`
  ADD UNIQUE KEY `TimeStamp` (`TimeStamp`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`User`);

--
-- Indexes for table `WallMeas`
--
ALTER TABLE `WallMeas`
  ADD PRIMARY KEY (`TimeStamp`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
