-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 11, 2010 at 10:51 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sagakademie_stable_201004`
--

-- --------------------------------------------------------

--
-- Table structure for table `x_grad`
--

CREATE TABLE IF NOT EXISTS `x_grad` (
  `id` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `x_grad`
--

INSERT INTO `x_grad` (`id`) VALUES
('-'),
('Dipl.-Ing.'),
('Dr.'),
('Prof.');
