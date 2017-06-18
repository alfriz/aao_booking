-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Giu 18, 2017 alle 14:49
-- Versione del server: 10.1.18-MariaDB-cll-lve
-- Versione PHP: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `h473859_g4l_db`
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
  `disporder` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;