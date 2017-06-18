-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Giu 18, 2017 alle 12:56
-- Versione del server: 5.5.27
-- Versione PHP: 5.6.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wptest`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wp_aao_bkg_coupons`
--

CREATE TABLE IF NOT EXISTS `wp_aao_bkg_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` text NOT NULL,
  `fromdate` date NOT NULL,
  `todate` date NOT NULL,
  `maxcount` int(11) NOT NULL,
  `areaid` int(11) NOT NULL,
  `serviceid` int(11) NOT NULL,
  `discountperc` int(11) NOT NULL,
  `discountval` float NOT NULL,
  `usedcount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;