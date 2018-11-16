-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 10, 2010 at 08:34 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sagakademie_stable_201004`
--

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_anmeldung`
--

CREATE TABLE IF NOT EXISTS `newsletter_anmeldung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `md5` varchar(255) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL,
  `datum` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `anrede` int(1) NOT NULL,
  `newsletter` int(11) NOT NULL,
  `newsletter_abmeldedatum` date NOT NULL,
  `newsletter_anmeldedatum` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `newsletter_anmeldung`
--

INSERT INTO `newsletter_anmeldung` (`id`, `email`, `md5`, `datum`, `name`, `vorname`, `anrede`, `newsletter`, `newsletter_abmeldedatum`, `newsletter_anmeldedatum`) VALUES
(1, 'ch@mosaik-software.de', 'f4ff9ce7aaa84e2d26aa35a8fdea7974', '2010-05-06 17:11:35', 'asd', 'Christian', 0, 0, '0000-00-00', '0000-00-00');
