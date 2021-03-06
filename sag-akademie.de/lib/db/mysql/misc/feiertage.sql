﻿-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. August 2009 um 18:06
-- Server Version: 5.0.45
-- PHP-Version: 5.2.9-0.dotdeb.1

--
-- Datenbank: `sgieselmann_sag`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `feiertag`
--

CREATE TABLE IF NOT EXISTS `feiertag` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `datum` date NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=157 ;

--
-- Daten für Tabelle `feiertag`
--

INSERT INTO `feiertag` (`id`, `name`, `datum`, `uid`) VALUES
(2, 'Neujahr', '2009-01-01', 1),
(3, 'Neujahr', '2010-01-01', 1),
(4, 'Neujahr', '2011-01-01', 1),
(5, 'Neujahr', '2012-01-01', 1),
(6, 'Neujahr', '2013-01-01', 1),
(7, 'Neujahr', '2014-01-01', 1),
(8, 'Neujahr', '2015-01-01', 1),
(9, 'Neujahr', '2016-01-01', 1),
(10, 'Neujahr', '2017-01-01', 1),
(11, 'Neujahr', '2018-01-01', 1),
(12, 'Heilige Drei K', '2009-01-06', 2),
(13, 'Heilige Drei K', '2010-01-06', 2),
(14, 'Heilige Drei K', '2011-01-06', 2),
(15, 'Heilige Drei K', '2012-01-06', 2),
(16, 'Heilige Drei K', '2013-01-06', 2),
(17, 'Heilige Drei K', '2014-01-06', 2),
(18, 'Heilige Drei K', '2015-01-06', 2),
(19, 'Heilige Drei K', '2016-01-06', 2),
(20, 'Heilige Drei K', '2017-01-06', 2),
(21, 'Heilige Drei K', '2018-01-06', 2),
(22, 'Karfreitag', '2009-04-10', 3),
(23, 'Karfreitag', '2010-04-02', 3),
(24, 'Karfreitag', '2011-04-22', 3),
(25, 'Karfreitag', '2012-04-06', 3),
(26, 'Karfreitag', '2013-03-29', 3),
(27, 'Karfreitag', '2014-04-18', 3),
(28, 'Karfreitag', '2015-04-03', 3),
(29, 'Karfreitag', '2016-03-25', 3),
(30, 'Karfreitag', '2017-04-14', 3),
(31, 'Karfreitag', '2017-03-30', 3),
(32, 'Ostermontag', '2009-04-13', 4),
(33, 'Ostermontag', '2010-04-05', 4),
(34, 'Ostermontag', '2011-04-25', 4),
(35, 'Ostermontag', '2012-04-09', 4),
(36, 'Ostermontag', '2013-04-01', 4),
(37, 'Ostermontag', '2014-04-21', 4),
(38, 'Ostermontag', '2015-04-06', 4),
(39, 'Ostermontag', '2016-03-28', 4),
(40, 'Ostermontag', '2017-04-17', 4),
(41, 'Ostermontag', '2018-04-02', 4),
(42, 'Tag der Arbeit', '2009-05-01', 5),
(43, 'Tag der Arbeit', '2010-05-01', 5),
(44, 'Tag der Arbeit', '2011-05-01', 5),
(45, 'Tag der Arbeit', '2012-05-01', 5),
(46, 'Tag der Arbeit', '2013-05-01', 5),
(47, 'Tag der Arbeit', '2014-05-01', 5),
(48, 'Tag der Arbeit', '2015-05-01', 5),
(49, 'Tag der Arbeit', '2016-05-01', 5),
(50, 'Tag der Arbeit', '2017-05-01', 5),
(51, 'Tag der Arbeit', '2018-05-01', 5),
(52, 'Christi Himmelfahrt', '2009-05-21', 6),
(53, 'Christi Himmelfahrt', '2010-05-13', 6),
(54, 'Christi Himmelfahrt', '2011-06-02', 6),
(55, 'Christi Himmelfahrt', '2012-05-17', 6),
(56, 'Christi Himmelfahrt', '2013-05-09', 6),
(57, 'Christi Himmelfahrt', '2014-05-29', 6),
(58, 'Christi Himmelfahrt', '2015-05-14', 6),
(59, 'Christi Himmelfahrt', '2016-05-05', 6),
(60, 'Christi Himmelfahrt', '2017-05-25', 6),
(61, 'Christi Himmelfahrt', '2018-05-10', 6),
(62, 'Pfingstmontag', '2009-06-01', 7),
(63, 'Pfingstmontag', '2010-05-24', 7),
(64, 'Pfingstmontag', '2011-06-13', 7),
(65, 'Pfingstmontag', '2012-05-28', 7),
(66, 'Pfingstmontag', '2013-05-20', 7),
(67, 'Pfingstmontag', '2014-06-09', 7),
(68, 'Pfingstmontag', '2015-05-25', 7),
(69, 'Pfingstmontag', '2016-05-16', 7),
(70, 'Pfingstmontag', '2017-06-05', 7),
(71, 'Pfingstmontag', '2018-05-21', 7),
(72, 'Fronleichnam', '2009-06-11', 8),
(73, 'Fronleichnam', '2010-06-03', 8),
(74, 'Fronleichnam', '2011-06-23', 8),
(75, 'Fronleichnam', '2012-06-07', 8),
(76, 'Fronleichnam', '2013-05-30', 8),
(77, 'Fronleichnam', '2014-06-19', 8),
(78, 'Fronleichnam', '2015-06-04', 8),
(79, 'Fronleichnam', '2016-05-26', 8),
(80, 'Fronleichnam', '2017-06-15', 8),
(81, 'Fronleichnam', '2018-05-31', 8),
(82, 'Mari', '2009-08-15', 9),
(83, 'Mari', '2010-05-15', 9),
(84, 'Mari', '2011-05-15', 9),
(85, 'Mari', '2012-05-15', 9),
(86, 'Mari', '2013-05-15', 9),
(87, 'Mari', '2014-05-15', 9),
(88, 'Mari', '2015-05-15', 9),
(89, 'Mari', '2016-05-15', 9),
(90, 'Mari', '2017-05-15', 9),
(91, 'Mari', '2018-05-15', 9),
(92, 'Tag der Deutschen Einheit', '2009-10-03', 10),
(93, 'Tag der Deutschen Einheit', '2010-10-03', 10),
(94, 'Tag der Deutschen Einheit', '2011-10-03', 10),
(95, 'Tag der Deutschen Einheit', '2012-10-03', 10),
(96, 'Tag der Deutschen Einheit', '2013-10-03', 10),
(97, 'Tag der Deutschen Einheit', '2014-10-03', 10),
(98, 'Tag der Deutschen Einheit', '2015-10-03', 10),
(99, 'Tag der Deutschen Einheit', '2016-10-03', 10),
(100, 'Tag der Deutschen Einheit', '2017-10-03', 10),
(101, 'Tag der Deutschen Einheit', '2018-10-03', 10),
(102, 'Reformationstag', '2009-10-31', 11),
(103, 'Reformationstag', '2010-10-31', 11),
(104, 'Reformationstag', '2011-10-31', 11),
(105, 'Reformationstag', '2012-10-31', 11),
(106, 'Reformationstag', '2013-10-31', 11),
(107, 'Reformationstag', '2014-10-31', 11),
(108, 'Reformationstag', '2015-10-31', 11),
(109, 'Reformationstag', '2016-10-31', 11),
(110, 'Reformationstag', '2017-10-31', 11),
(111, 'Reformationstag', '2018-10-31', 11),
(112, 'Allerheiligen', '2009-11-01', 12),
(113, 'Allerheiligen', '2010-11-01', 12),
(114, 'Allerheiligen', '2011-11-01', 12),
(115, 'Allerheiligen', '2012-11-01', 12),
(116, 'Allerheiligen', '2013-11-01', 12),
(117, 'Allerheiligen', '2014-11-01', 12),
(118, 'Allerheiligen', '2015-11-01', 12),
(119, 'Allerheiligen', '2016-11-01', 12),
(120, 'Allerheiligen', '2017-11-01', 12),
(121, 'Allerheiligen', '2018-11-01', 12),
(122, 'Bu', '2009-11-18', 13),
(123, 'Bu', '2010-11-17', 13),
(124, 'Bu', '2011-11-16', 13),
(125, 'Bu', '2012-11-21', 13),
(126, 'Bu', '2013-11-20', 13),
(127, 'Bu', '2014-11-19', 13),
(128, 'Bu', '2015-11-18', 13),
(129, 'Bu', '2016-11-16', 13),
(130, 'Bu', '2017-11-22', 13),
(131, 'Bu', '2018-11-21', 13),
(132, '1. Weihnachtstag', '2009-12-25', 14),
(133, '1. Weihnachtstag', '2010-12-25', 14),
(134, '1. Weihnachtstag', '2011-12-25', 14),
(135, '1. Weihnachtstag', '2012-12-25', 14),
(136, '1. Weihnachtstag', '2013-12-25', 14),
(137, '1. Weihnachtstag', '2014-12-25', 14),
(138, '1. Weihnachtstag', '2015-12-25', 14),
(139, '1. Weihnachtstag', '2016-12-25', 14),
(140, '1. Weihnachtstag', '2017-12-25', 14),
(141, '1. Weihnachtstag', '2018-12-25', 14),
(142, '2. Weihnachtstag', '2009-12-26', 15),
(143, '2. Weihnachtstag', '2010-12-26', 15),
(144, '2. Weihnachtstag', '2011-12-26', 15),
(145, '2. Weihnachtstag', '2012-12-26', 15),
(146, '2. Weihnachtstag', '2013-12-26', 15),
(147, '2. Weihnachtstag', '2014-12-26', 15),
(148, '2. Weihnachtstag', '2015-12-26', 15),
(149, '2. Weihnachtstag', '2016-12-26', 15),
(150, '2. Weihnachtstag', '2017-12-26', 15),
(151, '2. Weihnachtstag', '2018-12-26', 15),
(152, '', '0000-00-00', 0),
(153, '', '0000-00-00', 0),
(154, '', '0000-00-00', 0),
(155, '', '0000-00-00', 0),
(156, '', '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `r_feiertag_bundesland`
--

CREATE TABLE IF NOT EXISTS `r_feiertag_bundesland` (
  `id` int(11) NOT NULL auto_increment,
  `feiertag_id` int(11) NOT NULL,
  `bundesland_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=176 ;

--
-- Daten für Tabelle `r_feiertag_bundesland`
--

INSERT INTO `r_feiertag_bundesland` (`id`, `feiertag_id`, `bundesland_id`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 1, 4),
(4, 1, 5),
(5, 1, 6),
(6, 1, 7),
(7, 1, 8),
(8, 1, 9),
(9, 1, 10),
(10, 1, 11),
(11, 1, 12),
(12, 1, 13),
(13, 1, 14),
(14, 1, 15),
(15, 1, 16),
(16, 1, 17),
(17, 2, 2),
(18, 2, 3),
(19, 2, 15),
(20, 3, 2),
(21, 3, 3),
(22, 3, 4),
(23, 3, 5),
(24, 3, 6),
(25, 3, 7),
(26, 3, 8),
(27, 3, 9),
(28, 3, 10),
(29, 3, 11),
(30, 3, 12),
(31, 3, 13),
(32, 3, 14),
(33, 3, 15),
(34, 3, 16),
(35, 3, 17),
(36, 4, 2),
(37, 4, 3),
(38, 4, 4),
(39, 4, 5),
(40, 4, 6),
(41, 4, 7),
(42, 4, 8),
(43, 4, 9),
(44, 4, 10),
(45, 4, 11),
(46, 4, 12),
(47, 4, 13),
(48, 4, 14),
(49, 4, 15),
(50, 4, 16),
(51, 4, 17),
(52, 5, 2),
(53, 5, 3),
(54, 5, 4),
(55, 5, 5),
(56, 5, 6),
(57, 5, 7),
(58, 5, 8),
(59, 5, 9),
(60, 5, 10),
(61, 5, 11),
(62, 5, 12),
(63, 5, 13),
(64, 5, 14),
(65, 5, 15),
(66, 5, 16),
(67, 5, 17),
(68, 6, 2),
(69, 6, 3),
(70, 6, 4),
(71, 6, 5),
(72, 6, 6),
(73, 6, 7),
(74, 6, 8),
(75, 6, 9),
(76, 6, 10),
(77, 6, 11),
(78, 6, 12),
(79, 6, 13),
(80, 6, 14),
(81, 6, 15),
(82, 6, 16),
(83, 6, 17),
(84, 7, 2),
(85, 7, 3),
(86, 7, 4),
(87, 7, 5),
(88, 7, 6),
(89, 7, 7),
(90, 7, 8),
(91, 7, 9),
(92, 7, 10),
(93, 7, 11),
(94, 7, 12),
(95, 7, 13),
(96, 7, 14),
(97, 7, 15),
(98, 7, 16),
(99, 7, 17),
(100, 8, 2),
(101, 8, 3),
(102, 8, 4),
(103, 8, 5),
(104, 8, 6),
(105, 8, 7),
(106, 8, 8),
(107, 8, 9),
(108, 8, 10),
(109, 8, 11),
(110, 8, 12),
(111, 8, 13),
(112, 8, 14),
(113, 8, 15),
(114, 8, 16),
(115, 8, 17),
(116, 9, 13),
(117, 10, 2),
(118, 10, 3),
(119, 10, 4),
(120, 10, 5),
(121, 10, 6),
(122, 10, 7),
(123, 10, 8),
(124, 10, 9),
(125, 10, 10),
(126, 10, 11),
(127, 10, 12),
(128, 10, 13),
(129, 10, 14),
(130, 10, 15),
(131, 10, 16),
(132, 10, 17),
(133, 11, 5),
(134, 11, 9),
(135, 11, 14),
(136, 11, 15),
(137, 11, 17),
(138, 12, 2),
(139, 12, 3),
(140, 12, 11),
(141, 12, 12),
(142, 12, 13),
(143, 13, 14),
(144, 14, 2),
(145, 14, 3),
(146, 14, 4),
(147, 14, 5),
(148, 14, 6),
(149, 14, 7),
(150, 14, 8),
(151, 14, 9),
(152, 14, 10),
(153, 14, 11),
(154, 14, 12),
(155, 14, 13),
(156, 14, 14),
(157, 14, 15),
(158, 14, 16),
(159, 14, 17),
(160, 15, 2),
(161, 15, 3),
(162, 15, 4),
(163, 15, 5),
(164, 15, 6),
(165, 15, 7),
(166, 15, 8),
(167, 15, 9),
(168, 15, 10),
(169, 15, 11),
(170, 15, 12),
(171, 15, 13),
(172, 15, 14),
(173, 15, 15),
(174, 15, 16),
(175, 15, 17);
