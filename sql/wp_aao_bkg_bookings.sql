-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Giu 18, 2017 alle 12:57
-- Versione del server: 5.5.27
-- Versione PHP: 5.6.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wptest`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wp_aao_bkg_bookings`
--

CREATE TABLE IF NOT EXISTS `wp_aao_bkg_bookings` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `dayOfRegistration` date NOT NULL,
  `day` date NOT NULL,
  `areaId` mediumint(9) DEFAULT NULL,
  `persons` text NOT NULL,
  `userdata` text NOT NULL,
  `paymentmode` text NOT NULL,
  `coupon` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
