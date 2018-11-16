-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 17, 2010 at 11:47 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sagakademie_stable`
--

-- --------------------------------------------------------

--
-- Table structure for table `x_download`
--

CREATE TABLE IF NOT EXISTS `x_download` (
  `file_path` varchar(255) NOT NULL,
  `download_name` varchar(255) NOT NULL,
  `store` varchar(255) NOT NULL,
  PRIMARY KEY  (`file_path`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `x_download`
--

INSERT INTO `x_download` (`file_path`, `download_name`, `store`) VALUES
('news_6fdbd61683a06b5b.pdf', '115282_sheet.pdf', '/pdf/');
