-- phpMyAdmin SQL Dump
-- version 4.0.10.18
-- https://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generato il: Apr 09, 2017 alle 07:58
-- Versione del server: 5.6.28-76.1-log
-- Versione PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `marcote4_galassi`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wp_aao_bkg_areas`
--

CREATE TABLE `wp_aao_bkg_areas` (
  `id` mediumint(9) NOT NULL,
  `description` varchar(55) NOT NULL,
  `tipologia` int(11) DEFAULT NULL,
  `max` int(11) NOT NULL,
  `min` int(11) NOT NULL,
  `disporder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `wp_aao_bkg_areas`
--

INSERT INTO `wp_aao_bkg_areas` (`id`, `description`, `tipologia`, `max`, `min`, `disporder`) VALUES
(1, 'Gazebo 1', 1, 12, 5, 1),
(2, 'Gazebo 2', 1, 12, 5, 2),
(5, 'Gazebo 3', 1, 12, 5, 3),
(6, 'Gazebo 4', 1, 8, 2, 4),
(7, 'Gazebo 5', 1, 8, 2, 5),
(8, 'Cubo', 1, 20, 8, 6),
(9, 'Glicine', 1, 20, 8, 7),
(10, 'Pioppi', 1, 20, 8, 8),
(11, 'Querce', 1, 50, 20, 9),
(12, 'Meli 1', 1, 10, 2, 10),
(13, 'Meli 2', 1, 10, 2, 11),
(14, 'Terrazza piccola', 2, 3, 2, 12),
(15, 'Terrazza grande', 2, 8, 2, 13);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `wp_aao_bkg_areas`
--
ALTER TABLE `wp_aao_bkg_areas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `wp_aao_bkg_areas`
--
ALTER TABLE `wp_aao_bkg_areas`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
