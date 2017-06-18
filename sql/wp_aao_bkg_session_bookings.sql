-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Giu 18, 2017 alle 13:00
-- Versione del server: 5.5.27
-- Versione PHP: 5.6.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wptest`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wp_aao_bkg_temp_bookings`
--

CREATE TABLE IF NOT EXISTS `wp_aao_bkg_temp_bookings` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `day` date NOT NULL,
  `areaId` mediumint(9) DEFAULT NULL,
  `persons` text NOT NULL,
  `session` bigint(20) NOT NULL,
  `userdata` text NOT NULL,
  `coupon` text NOT NULL,
  PRIMARY KEY (`session`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=92 ;