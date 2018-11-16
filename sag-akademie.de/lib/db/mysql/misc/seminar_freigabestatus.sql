-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 18. August 2009 um 10:56
-- Server Version: 5.0.45
-- PHP-Version: 5.2.9-0.dotdeb.1


-- Datenbank: `sgieselmann_sag`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `seminar_freigabestatus`
--

CREATE TABLE IF NOT EXISTS `seminar_freigabestatus` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `flag` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `seminar_freigabestatus`
--

INSERT INTO `seminar_freigabestatus` (`id`, `name`, `flag`) VALUES
(1, 'Planung', 'P'),
(2, 'Vorlage', 'V'),
(3, 'Bestätigt', 'B');
