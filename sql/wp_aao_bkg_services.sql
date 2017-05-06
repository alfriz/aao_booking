-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Mag 06, 2017 alle 08:22
-- Versione del server: 10.1.18-MariaDB-cll-lve
-- Versione PHP: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `h473859_g4l_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wp_aao_bkg_services`
--

CREATE TABLE `wp_aao_bkg_services` (
  `id` mediumint(9) NOT NULL,
  `description` varchar(55) NOT NULL,
  `areaType` mediumint(9) NOT NULL,
  `prezzo` float NOT NULL,
  `disporder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `wp_aao_bkg_services`
--

INSERT INTO `wp_aao_bkg_services` (`id`, `description`, `areaType`, `prezzo`, `disporder`) VALUES
(15, 'Grigliata - 14€', 1, 14, 1),
(16, 'Grigliata (bambino fino a 12 anni) - 10€', 1, 10, 2),
(20, 'Pizza/picnic - 11€', 1, 11, 3),
(21, 'Pizza/picnic (bambino fino a 12 anni) - 8€', 1, 8, 4),
(22, 'Posto tavola in terrazza - Prezzo unico', 2, 23, 5);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `wp_aao_bkg_services`
--
ALTER TABLE `wp_aao_bkg_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `wp_aao_bkg_services`
--
ALTER TABLE `wp_aao_bkg_services`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
