CREATE TABLE IF NOT EXISTS `llx_c_volvo_suspension_cabine` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `suspcabine` varchar(255) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB;