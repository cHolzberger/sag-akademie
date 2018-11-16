-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. März 2010 um 22:54
-- Server Version: 5.1.41
-- PHP-Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `sagakademie_stable`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `neuigkeit`
--

CREATE TABLE IF NOT EXISTS `neuigkeit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(150) NOT NULL,
  `text` text NOT NULL,
  `datum` datetime NOT NULL,
  `pdf` varchar(255) NOT NULL,
  `geaendert` datetime NOT NULL,
  `geaendert_von` int(11) NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;
