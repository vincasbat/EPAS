-- phpMyAdmin SQL Dump

--
-- Database: `epaslaugos`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokai`
--

CREATE TABLE IF NOT EXISTS `dokai` (
  `dok_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dok_formos_kodas` varchar(50) DEFAULT 'Nenurodyta',
  `mokestis` decimal(7,2) DEFAULT '0.00',
  `dok_kelias` varchar(255) NOT NULL,
  `naud_email` varchar(100) DEFAULT NULL,
  `pastabos` varchar(255) DEFAULT NULL,
  `status_dabar` enum('Gautas','PZ','IS','AP','PZ OK','IS OK','AP OK','PZ atmestas','IS atmestas','AP atmestas','OK','Atmestas') DEFAULT NULL,
  `status_dabar_date` datetime DEFAULT NULL,
  `from_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`dok_id`),
  UNIQUE KEY `dok_id` (`dok_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=128 ;


--
-- Table structure for table `dok_statusai`
--

CREATE TABLE IF NOT EXISTS `dok_statusai` (
  `dok_status_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dok_id` int(3) NOT NULL,
  `statusID` enum('Gautas','PZ','IS','AP','PZ OK','IS OK','AP OK','PZ atmestas','IS atmestas','AP atmestas','OK','Atmestas') DEFAULT NULL,
  `status_date` datetime NOT NULL,
  `naud_email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`dok_status_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=278 ;


-- --------------------------------------------------------

--
-- Table structure for table `dok_tipai`
--

CREATE TABLE IF NOT EXISTS `dok_tipai` (
  `dok_pavadinimas` varchar(255) NOT NULL,
  `dok_formos_kodas` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`dok_formos_kodas`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dok_tipai`
--

INSERT INTO `dok_tipai` (`dok_pavadinimas`, `dok_formos_kodas`) VALUES
('01', 'IP - 2/94'),
('02', 'IP - 3/94'),
('03', 'EP - 1/98'),
('04', 'PA - 1/2001'),
('05', 'EPK - 1/2006'),
('06', 'EPK - 2/2006'),
('07', 'zp -1/2004'),
('08', 'zp -11/2004'),
('09', 'zp -12/2004'),
('10', 'zp -13/2004'),
('11', 'zp -14/2004'),
('12', 'zp -15/2004'),
('00', 'Nenurodyta'),
('13', 'zp -16/2004'),
('14', 'zp -17/2004'),
('15', 'zp -18/2004'),
('16', 'CTM1-2004'),
('17', 'Priedas'),
('18', 'Tzp - 1/2001'),
('19', 'P - 2/2009'),
('20', 'DP - 1/2008'),
('21', 'DP - 11/2008'),
('22', 'DP - 12/2008'),
('23', 'DP - 13/2008'),
('24', 'DP - 14/2008'),
('25', 'DP - 15/2008'),
('26', 'DP - 16/2008'),
('27', 'DP - 17/2008'),
('28', 'DP - 18/2008'),
('29', 'DP - 22/2008'),
('30', 'DP - 23/2008'),
('31', 'CD - 1/2008'),
('32', 'TDP - 1/2008'),
('33', 'D - 2/2009'),
('00a', 'IP - 1/98');

-- --------------------------------------------------------

--
-- Table structure for table `naudotojai`
--

CREATE TABLE IF NOT EXISTS `naudotojai` (
  `naud_vardas` varchar(100) DEFAULT NULL,
  `naud_pavarde` varchar(100) DEFAULT NULL,
  `naud_passw` varchar(32) DEFAULT NULL,
  `naud_sukurimo_data` datetime DEFAULT NULL,
  `naud_email` varchar(100) NOT NULL DEFAULT '',
  `naud_telef` varchar(50) DEFAULT NULL,
  `naud_adr` varchar(100) DEFAULT NULL,
  `naud_ak` varchar(11) DEFAULT NULL,
  `naud_grupe` enum('admins','pz','pr','is','par','ap') NOT NULL DEFAULT 'par',
  `naud_org` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`naud_email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


