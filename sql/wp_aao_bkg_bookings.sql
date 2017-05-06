-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Mag 06, 2017 alle 08:20
-- Versione del server: 10.1.18-MariaDB-cll-lve
-- Versione PHP: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `h473859_g4l_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wp_aao_bkg_bookings`
--

CREATE TABLE `wp_aao_bkg_bookings` (
  `id` mediumint(9) NOT NULL,
  `dayOfRegistration` date NOT NULL,
  `day` date NOT NULL,
  `areaId` mediumint(9) DEFAULT NULL,
  `persons` text NOT NULL,
  `userdata` text NOT NULL,
  `paymentmode` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `wp_aao_bkg_bookings`
--
ALTER TABLE `wp_aao_bkg_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `wp_aao_bkg_bookings`
--
ALTER TABLE `wp_aao_bkg_bookings`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
