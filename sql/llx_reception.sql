CREATE TABLE IF NOT EXISTS llx_reception (
  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_reprise` int(11) NOT NULL,
  `date_reception` datetime DEFAULT NULL,
  `km`  int(11) DEFAULT NULL,
  `etat_conforme` int(11) DEFAULT NULL,
  `comm_etat` text DEFAULT NULL,
  `fk_site_actuel` int(11) DEFAULT NULL,
  `presentation_produit` text DEFAULT NULL,
  `date_facture` datetime DEFAULT NULL,
  `fk_receptionnaire` int(11) DEFAULT NULL,
  `fk_financeur` int(11) DEFAULT NULL,
  `buyer` int(11) DEFAULT NULL,
 ) ENGINE=InnoDB;




