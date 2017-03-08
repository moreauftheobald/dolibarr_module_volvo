
CREATE TABLE IF NOT EXISTS `llx_c_volvo_freinage` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `freinage` varchar(255) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB;


