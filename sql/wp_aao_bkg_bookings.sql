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


--- view per il summary dati raw
CREATE VIEW `wp_aao_bkg_vw_summary_raw`
AS
SELECT
	dayOfRegistration,
    day,
    areaId,
    paymentmode,
    
	TRIM( 'name=' from SUBSTRING_INDEX(userdata, '&', 1)) AS name,
    TRIM( 'surname=' from SUBSTRING_INDEX(SUBSTRING_INDEX(userdata, '&', 2), '&', -1)) AS surname,
    TRIM( 'email=' from SUBSTRING_INDEX(SUBSTRING_INDEX(userdata, '&', 3), '&', -1)) AS email,
    TRIM( 'tel=' from SUBSTRING_INDEX(SUBSTRING_INDEX(userdata, '&', 4), '&', -1)) AS tel,
   

	TRIM( 'min=' from SUBSTRING_INDEX(persons, '&', 1)) AS min,
    TRIM( 'max=' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 2), '&', -1)) AS max,
    (SELECT description FROM wp_aao_bkg_services where id = SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 3), '&', -1)), '=', 1), '=', -1)) AS service1,
    SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 3), '&', -1)), '=', 2), '=', -1) AS qtyVal1,
    (SELECT description FROM wp_aao_bkg_services where id = SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 5), '&', -1)), '=', 1), '=', -1)) AS service2,
    SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 5), '&', -1)), '=', 2), '=', -1) AS qtyVal2,
    (SELECT description FROM wp_aao_bkg_services where id = SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 7), '&', -1)), '=', 1), '=', -1)) AS service3,
    SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 7), '&', -1)), '=', 2), '=', -1) AS qtyVal3,
    (SELECT description FROM wp_aao_bkg_services where id = SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 9), '&', -1)), '=', 1), '=', -1)) AS service4,
    SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM( 'qty_' from SUBSTRING_INDEX(SUBSTRING_INDEX(persons, '&', 9), '&', -1)), '=', 2), '=', -1) AS qtyVal4
    
    FROM `wp_aao_bkg_bookings`;
    
--- view per il summary usata dalla dbview
CREATE VIEW `wp_aao_bkg_vw_summary`
AS    
    SELECT 
    dayOfRegistration,
    day,
    areaId,
    paymentmode,
    name,
    surname,
    REPLACE(email,'%40','@') as email,
    tel,
    
      IF(qtyval1 IS NULL or LENGTH(qtyval1)=0 ,'-',IF(service1 IS NULL,'-',service1)) 
             AS service1,
    IF(service1 IS NULL,'',qtyval1) 
             AS qtyval1,
     IF(qtyval2 IS NULL or LENGTH(qtyval2)=0 ,'-',IF(service2 IS NULL,'-',service2)) 
             AS service2,
    IF(service2 IS NULL,'',qtyval2) 
             AS qtyval2,
      IF(qtyval3 IS NULL or LENGTH(qtyval3)=0 ,'-',IF(service3 IS NULL,'-',service3)) 
             AS service3,
    IF(service3 IS NULL,'',qtyval3) 
             AS qtyval3,
               IF(qtyval4 IS NULL or LENGTH(qtyval4)=0 ,'-',IF(service4 IS NULL,'-',service4)) 
             AS service4,
    IF(service4 IS NULL,'',qtyval4) 
             AS qtyval4         
    
    
     FROM `wp_aao_bkg_vw_summary_raw` ;
     
 
-- vecchia view di dbview 
--SELECT dayOfRegistration as "Prenotazione del", day as "Per il", a.description as "Nome area", userdata as "Riferimenti contatto", persons as "Dettagli prenotazione",  paymentmode as "Pagamento"
--FROM wp_aao_bkg_bookings as b join wp_aao_bkg_areas as a on a.id = b.areaId
--ORDER BY day DESC


-- nuova view del dbview
--SELECT dayOfRegistration as "Prenotazione del", day as "Per il", a.description as "Nome area", 
--CONCAT(name,' ',surname) as "Nome e Cognome",
--email as "Email",
--tel as "Telefono",
--IF(qtyval1 IS NULL or LENGTH(qtyval1)=0 ,'-',CONCAT(service1, ' Qtà:', qtyval1)) as "Servizio 1",
--IF(qtyval2 IS NULL or LENGTH(qtyval2)=0 ,'-',CONCAT(service2, ' Qtà:', qtyval2)) as "Servizio 2", 
--IF(qtyval3 IS NULL or LENGTH(qtyval3)=0 ,'-',CONCAT(service3, ' Qtà:', qtyval3)) as "Servizio 3", 
--IF(qtyval4 IS NULL or LENGTH(qtyval4)=0 ,'-',CONCAT(service4, ' Qtà:', qtyval4)) as "Servizio 4", 
--paymentmode as "Pagamento"
--FROM wp_aao_bkg_vw_summary as b join wp_aao_bkg_areas as a on a.id = b.areaId
--ORDER BY day DESC    
