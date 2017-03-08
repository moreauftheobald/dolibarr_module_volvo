INSERT INTO `llx_c_volvo_bv` (`rowid`,  `bv`) VALUES(1, 'Manuelle'),
(2, 'Semi-Automatique'),
(3, 'Automatique');

INSERT INTO `llx_c_volvo_cabine` (`rowid`, `cabine`) VALUES(1, 'L1H1'),
(2, 'L2H1'),
(3, 'L2H2'),
(4, 'L2H3');

INSERT INTO `llx_c_volvo_carrosserie` (`rowid`, `carrosserie`) VALUES
(1, 'Fourgon'),
(2, 'Tautliner'),
(3, 'Ampiroll'),
(4, 'Frigo'),
(5, 'Caisse Mobile'),
(6, 'Plateau'),
(7, 'Plateau grue'),
(8, 'Benne TP'),
(9, 'Benne TP + Grue');

INSERT INTO `llx_c_volvo_freinage` (`rowid`, `freinage`) VALUES
(1, 'Disques'),
(2, 'Tambours'),
(3, 'Mixte');

INSERT INTO `llx_c_volvo_gamme` (`rowid`, `gamme`, `cv`, `Active`) VALUES
(1, 'FH', 4, 1),
(2, 'FM', 4, 1),
(3, 'FL', 4, 1),
(4, 'FE', 4, 1),
(5, 'NP300 - Navarra', 5, 1),
(6, 'NV200', 5, 1),
(7, 'NV200 Electrique', 5, 1),
(8, 'NV300', 5, 0),
(9, 'NV400', 5, 1),
(10, 'NT400', 5, 1),
(11, 'NT500', 5, 1),
(12, 'Fourgon', 6, 1),
(13, 'Frigo', 6, 1),
(14, 'Tautliner', 6, 1),
(15, 'Savoyarde', 6, 1),
(16, 'Benne', 6, 1),
(17, 'Porte Container', 6, 1);

INSERT INTO `llx_c_volvo_genre` (`rowid`, `genre`, `rep`, `cv`, `del_rg`) VALUES
(1, 'Porteur', 1, 4, 54),
(2, 'Tracteur Routier', 1, 4, 24),
(3, 'Véhicule Utilitaire léger', 1, 5, 15),
(4, 'Véhicule léger', 1, 5, 15),
(5, 'Remorque', 1, 6, 15),
(6, 'Semie Remorque', 1, 6, 15),
(7, 'Ensemble articulé', 1, 0, 24);

INSERT INTO `llx_c_volvo_sites` (`rowid`, `codesite`, `nom`) VALUES
(1, 'ENN', 'Ennery'),
(2, 'LUD', 'Ludres'),
(3, 'NAB', 'Saint Nabors'),
(4, 'SAR', 'Sarreguemines'),
(5, 'YTZ', 'Yutz');

INSERT INTO `llx_c_volvo_marque_pneu` (`rowid`, `marquepneu`) VALUES
(1, 'Michelin'),
(2, 'Continental'),
(3, 'Goodyear'),
(4, 'Dunlop'),
(5, 'Bridgestone'),
(6, 'Autre');

INSERT INTO `llx_c_volvo_marques` (`rowid`, `marque`) VALUES
(1, 'Volvo'),
(2, 'Renault'),
(3, 'Mercedes'),
(4, 'MAN'),
(5, 'DAF'),
(6, 'Iveco'),
(7, 'Nissan'),
(8, 'Scania');

INSERT INTO `llx_c_volvo_moteur` (`rowid`, `moteur`) VALUES
(1, 'D4'),
(2, 'D7'),
(3, 'D9'),
(4, 'D11'),
(5, 'D13'),
(6, 'D16');

INSERT INTO `llx_c_volvo_normes` (`rowid`, `norme`) VALUES
(1, 'EUR1'),
(2, 'EUR2'),
(3, 'EUR3'),
(4, 'EUR4'),
(5, 'EUR5'),
(6, 'EUR6'),
(7, 'EEV');

INSERT INTO `llx_c_volvo_motif_perte_lead` (`rowid`, `motif`) VALUES
(1, 'Prix'),
(2, 'Fidelité'),
(3, 'Achat VO'),
(4, 'Délais'),
(5, 'Hors Zone'),
(6, 'Mal Suivis'),
(7, 'Produit'),
(8, 'Abandon'),
(9, 'Location'),
(10, 'PB Garantie'),
(11, 'Financement'),
(12, 'Réciprocité'),
(13, 'Proximité'),
(14, 'SAV'),
(15, 'Relationnel'),
(16, 'Protocoles Flottes'),
(17, 'Autres motifs divers');

INSERT INTO `llx_c_volvo_ralentisseur` (`rowid`, `ralentisseur`) VALUES
(1, 'V.E.B.'),
(2, 'V.E.B. +'),
(3, 'Hydraulique'),
(4, 'Electrique'),
(5, 'Sans Ralentisseur');

INSERT INTO `llx_c_volvo_silouhette` (`rowid`, `silouhette`, `cv`, `rep`) VALUES
(1, '4x2', 4, 1),
(2, '4x4', 4, 1),
(3, '6x2', 4, 1),
(4, '6x4', 4, 1),
(5, '6x6', 4, 1),
(6, '8x2', 4, 1),
(7, '8x4', 4, 1),
(8, '8x6', 4, 1),
(9, '8x8', 4, 1),
(10, '4x4', 5, 0),
(11, '1 Essieu', 6, 1),
(12, '2 Essieux', 6, 1),
(13, '3 Essieux', 6, 1),
(14, '4x2', 5, 0);

INSERT INTO `llx_c_volvo_solutions_transport` (`rowid`, `nom`, `active`) VALUES
(1, 'FIN: Financement VFS', 1),
(2, 'FIN: Financement Lixbail', 1),
(3, 'DFOL: Dynafleet Fuel et Environnement', 1),
(4, 'DFOL: Dynafleet Positionnement', 1),
(5, 'DFOL: Dynafleet Positionnement +', 1),
(6, 'DFOL: Dynafleet Driver Time Management', 1),
(7, 'DFOL: Dynafleet Messagerie', 1),
(8, 'VCM: Pack Prévention', 1),
(9, 'VCM: Pack Protection cinématique', 1),
(10, 'VCM: Pack Protection véhicule', 1),
(11, 'VCM: Pack Blue', 1),
(12, 'VCM: Pack Silver', 1),
(13, 'VCM: Pack Silver +', 1),
(14, 'VCM: Contrat GOLD', 1),
(15, 'Fuel Advice', 1),
(16, 'Driver Dev', 1);

INSERT INTO `llx_c_volvo_suspension_cabine` (`rowid`, `suspcabine`) VALUES
(1, 'Mécanique'),
(2, 'Mixte'),
(3, 'Pneumatique');

INSERT INTO `llx_c_volvo_etats` (`rowid`, `nom`, `liste`) VALUES
(1, 'OK', '1,2,4,6,7,8,9,10,13,14,'),
(2, 'Rayé(e)(s)', '1,2,'),
(3, 'Fissuré(e)(s)', '1,2,5,14,'),
(4, 'cassé(e)(s)', '1,2,'),
(5, 'NON', '1,3,6,7,8,13,'),
(6, 'Troué(e)(s)', '2,'),
(7, 'Manquant(e)(s)', '1,2,9,10,13,'),
(8, 'Neuve', '3,'),
(9, 'Usure 25%', '3,'),
(10, 'Usure 50%', '3,'),
(11, 'Usure 75%', '3,'),
(12, 'HS', '3,'),
(13, 'Détendus', '4,'),
(14, 'Arraché(e)(s)', '4,5,'),
(15, 'Coupés', '4,'),
(16, 'Pliée', '5,'),
(17, 'A Changer', '6,8,14,'),
(18, 'Bosse', '7,8,'),
(19, 'Rouille', '7,8,'),
(20, 'Incomplet', '9,'),
(21, 'Propre', '11,12,'),
(22, 'Moyen', '11,'),
(23, 'Sale', '11,12,'),
(24, 'Abimé', '12,13,'),
(25, 'Impact(s)', '14,'),
(26, 'Nombreux Impacts', '14,');
