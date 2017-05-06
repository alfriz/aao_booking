-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Mag 06, 2017 alle 08:18
-- Versione del server: 10.1.18-MariaDB-cll-lve
-- Versione PHP: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `h473859_g4l_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wp_aao_bkg_area_types`
--

CREATE TABLE `wp_aao_bkg_area_types` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `wp_aao_bkg_area_types`
--

INSERT INTO `wp_aao_bkg_area_types` (`id`, `description`) VALUES
(1, 'AREE A TERRA'),
(2, 'TERRAZZE');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `wp_aao_bkg_area_types`
--
ALTER TABLE `wp_aao_bkg_area_types`
  ADD PRIMARY KEY (`id`);

