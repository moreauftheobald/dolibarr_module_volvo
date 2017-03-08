<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file Expertise/expertises.class.php
 * \ingroup Expertise
 * \brief This file is an example for a CRUD class file (Create/Read/Update/Delete)
 * Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
// require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
// require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Expertises
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Expertise extends CommonObject
{
	/**
	 *
	 * @var string Id to identify managed objects
	 */
	public $element = 'expertise';
	/**
	 *
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'expertises';

	/**
	 *
	 * @var ExpertisesLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	var $value_array = array(
			'rowid',
			'tms',
			'fk_reprise',
			'fk_rang',
			'lieu',
			'date_exp',
			'fk_client_contact',
			'fk_expert',
			'fk_testeur',
			'date_essai',
			'moteur',
			'embrayage',
			'bv',
			'transmission',
			'pont',
			'freins',
			'fk_defG',
			'fk_defD',
			'fk_defT',
			'fk_visiere',
			'fk_retroG',
			'fk_retroD',
			'fk_retroA',
			'fk_bloc_opt_av_D',
			'fk_bloc_opt_av_G',
			'fk_enjo_phare_av_D',
			'fk_enjo_phare_av_G',
			'fk_parechoc_av_D',
			'fk_parechoc_av_G',
			'fk_calandre',
			'fk_clignotant_av_D',
			'fk_clignotant_av_G',
			'fk_longe_porte_D',
			'fk_longe_porte_G',
			'fk_porte_coffre_D',
			'fk_porte_coffre_G',
			'fk_porte_cabine_D',
			'fk_porte_cabine_G',
			'div_ext_cabine',
			'comm_div_ext_cabine',
			'fk_roue_secours',
			'fk_flexibles',
			'fk_grille_alu',
			'fk_batteries',
			'fk_jupe_G',
			'fk_jupe_D',
			'fk_reservoir_GO_G',
			'fk_reservoir_GO_D',
			'fk_reservoir_adblue',
			'fk_reservoir_hydro',
			'fk_aile_ar_G',
			'fk_aile_ar_D',
			'fk_feux_ar_G',
			'fk_feux_ar_D',
			'fk_support_feux_ar_G',
			'fk_support_feux_ar_D',
			'usure_pneu_av',
			'usure_pneu_ar1',
			'usure_pneu_ar2',
			'usure_pneu_ar3',
			'div_chassis',
			'comm_div_chassis',
			'fk_kit_outils_cric',
			'fk_chappe',
			'fk_etat_interieur',
			'fk_etat_TDB',
			'fk_etat_couchette',
			'fk_autoradio',
			'fk_telephone',
			'fk_tapis_sol',
			'nb_tapis_sol',
			'fk_parebrise',
			'fk_cles',
			'kilometres',
			'comm_baches',
			'comm_barres_AE',
			'comm_caisse',
			'comm_benne',
			'comm_portes_ar',
			'comm_plancher',
			'comm_ridelles',
			'comm_grue',
			'comm_hayon',
			'comm_crochet_attelage',
			'comm_groupe_frigo',
			'comm_interieur',
			'comm_plancher_fosse',
			'div_porteur_semi',
			'comm_div_porteur_semi',
			'autres_observations'
	);
	public $tms = '';
	public $fk_reprise;
	public $fk_rang;
	public $lieu;
	public $date_exp = '';
	public $fk_client_contact;
	public $fk_expert;
	public $fk_testeur;
	public $date_essai = '';
	public $moteur;
	public $embrayage;
	public $bv;
	public $transmission;
	public $pont;
	public $freins;
	public $fk_defG;
	public $fk_defD;
	public $fk_defT;
	public $fk_visiere;
	public $fk_retroG;
	public $fk_retroD;
	public $fk_retroA;
	public $fk_bloc_opt_av_D;
	public $fk_bloc_opt_av_G;
	public $fk_enjo_phare_av_D;
	public $fk_enjo_phare_av_G;
	public $fk_parechoc_av_D;
	public $fk_parechoc_av_G;
	public $fk_calandre;
	public $fk_clignotant_av_D;
	public $fk_clignotant_av_G;
	public $fk_longe_porte_D;
	public $fk_longe_porte_G;
	public $fk_porte_coffre_D;
	public $fk_porte_coffre_G;
	public $fk_porte_cabine_D;
	public $fk_porte_cabine_G;
	public $div_ext_cabine;
	public $comm_div_ext_cabine;
	public $fk_roue_secours;
	public $fk_flexibles;
	public $fk_grille_alu;
	public $fk_batteries;
	public $fk_jupe_G;
	public $fk_jupe_D;
	public $fk_reservoir_GO_G;
	public $fk_reservoir_GO_D;
	public $fk_reservoir_adblue;
	public $fk_reservoir_hydro;
	public $fk_aile_ar_G;
	public $fk_aile_ar_D;
	public $fk_feux_ar_G;
	public $fk_feux_ar_D;
	public $fk_support_feux_ar_G;
	public $fk_support_feux_ar_D;
	public $usure_pneu_av;
	public $usure_pneu_ar1;
	public $usure_pneu_ar2;
	public $usure_pneu_ar3;
	public $div_chassis;
	public $comm_div_chassis;
	public $fk_kit_outils_cric;
	public $fk_chappe;
	public $fk_etat_interieur;
	public $fk_etat_TDB;
	public $fk_etat_couchette;
	public $fk_autoradio;
	public $fk_telephone;
	public $fk_tapis_sol;
	public $nb_tapis_sol;
	public $fk_parebrise;
	public $fk_cles;
	public $kilometres;
	public $comm_baches;
	public $comm_barres_AE;
	public $comm_caisse;
	public $comm_benne;
	public $comm_portes_ar;
	public $comm_plancher;
	public $comm_ridelles;
	public $comm_grue;
	public $comm_hayon;
	public $comm_crochet_attelage;
	public $comm_groupe_frigo;
	public $comm_interieur;
	public $comm_plancher_fosse;
	public $div_porteur_semi;
	public $comm_div_porteur_semi;
	public $autres_observations;

	var $liste_exp = array();

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db) {
		$this->db = $db;

		while ( $i < 16 ) {
			$this->load_dict($i);
			$i ++;
		}
	}

	/**
	 *
	 * @param unknown $i
	 * @return number
	 */
	private function load_dict($i) {
		$sql = "SELECT rowid, nom FROM " . MAIN_DB_PREFIX . "c_volvo_etats WHERE liste LIKE '% " . $i . ",%'";
		$resql = $this->db->query($sql);
		$res = array();
		if ($resql) {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$res[$obj->rowid] = $obj->nom;
			}
			$this->db->free($resql);
			$this->liste[$i] = $res;
			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::_load_liste " . $this->error, LOG_ERR);
			return - 1;
		}
	}

	/**
	 * Create object into database
	 *
	 * @param User $user User that creates
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false) {

		global $langs;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters

		if (isset($this->fk_reprise)) {
			$this->fk_reprise = trim($this->fk_reprise);
		}
		if (isset($this->fk_rang)) {
			$this->fk_rang = trim($this->fk_rang);
		}
		if (isset($this->lieu)) {
			$this->lieu = trim($this->lieu);
		}
		if (isset($this->fk_client_contact)) {
			$this->fk_client_contact = trim($this->fk_client_contact);
		}
		if (isset($this->fk_expert)) {
			$this->fk_expert = trim($this->fk_expert);
		}
		if (isset($this->fk_testeur)) {
			$this->fk_testeur = trim($this->fk_testeur);
		}
		if (isset($this->moteur)) {
			$this->moteur = trim($this->moteur);
		}
		if (isset($this->embrayage)) {
			$this->embrayage = trim($this->embrayage);
		}
		if (isset($this->bv)) {
			$this->bv = trim($this->bv);
		}
		if (isset($this->transmission)) {
			$this->transmission = trim($this->transmission);
		}
		if (isset($this->pont)) {
			$this->pont = trim($this->pont);
		}
		if (isset($this->freins)) {
			$this->freins = trim($this->freins);
		}
		if (isset($this->fk_defG)) {
			$this->fk_defG = trim($this->fk_defG);
		}
		if (isset($this->fk_defD)) {
			$this->fk_defD = trim($this->fk_defD);
		}
		if (isset($this->fk_defT)) {
			$this->fk_defT = trim($this->fk_defT);
		}
		if (isset($this->fk_visiere)) {
			$this->fk_visiere = trim($this->fk_visiere);
		}
		if (isset($this->fk_retroG)) {
			$this->fk_retroG = trim($this->fk_retroG);
		}
		if (isset($this->fk_retroD)) {
			$this->fk_retroD = trim($this->fk_retroD);
		}
		if (isset($this->fk_retroA)) {
			$this->fk_retroA = trim($this->fk_retroA);
		}
		if (isset($this->fk_bloc_opt_av_D)) {
			$this->fk_bloc_opt_av_D = trim($this->fk_bloc_opt_av_D);
		}
		if (isset($this->fk_bloc_opt_av_G)) {
			$this->fk_bloc_opt_av_G = trim($this->fk_bloc_opt_av_G);
		}
		if (isset($this->fk_enjo_phare_av_D)) {
			$this->fk_enjo_phare_av_D = trim($this->fk_enjo_phare_av_D);
		}
		if (isset($this->fk_enjo_phare_av_G)) {
			$this->fk_enjo_phare_av_G = trim($this->fk_enjo_phare_av_G);
		}
		if (isset($this->fk_parechoc_av_D)) {
			$this->fk_parechoc_av_D = trim($this->fk_parechoc_av_D);
		}
		if (isset($this->fk_parechoc_av_G)) {
			$this->fk_parechoc_av_G = trim($this->fk_parechoc_av_G);
		}
		if (isset($this->fk_calandre)) {
			$this->fk_calandre = trim($this->fk_calandre);
		}
		if (isset($this->fk_clignotant_av_D)) {
			$this->fk_clignotant_av_D = trim($this->fk_clignotant_av_D);
		}
		if (isset($this->fk_clignotant_av_G)) {
			$this->fk_clignotant_av_G = trim($this->fk_clignotant_av_G);
		}
		if (isset($this->fk_longe_porte_D)) {
			$this->fk_longe_porte_D = trim($this->fk_longe_porte_D);
		}
		if (isset($this->fk_longe_porte_G)) {
			$this->fk_longe_porte_G = trim($this->fk_longe_porte_G);
		}
		if (isset($this->fk_porte_coffre_D)) {
			$this->fk_porte_coffre_D = trim($this->fk_porte_coffre_D);
		}
		if (isset($this->fk_porte_coffre_G)) {
			$this->fk_porte_coffre_G = trim($this->fk_porte_coffre_G);
		}
		if (isset($this->fk_porte_cabine_D)) {
			$this->fk_porte_cabine_D = trim($this->fk_porte_cabine_D);
		}
		if (isset($this->fk_porte_cabine_G)) {
			$this->fk_porte_cabine_G = trim($this->fk_porte_cabine_G);
		}
		if (isset($this->div_ext_cabine)) {
			$this->div_ext_cabine = trim($this->div_ext_cabine);
		}
		if (isset($this->comm_div_ext_cabine)) {
			$this->comm_div_ext_cabine = trim($this->comm_div_ext_cabine);
		}
		if (isset($this->fk_roue_secours)) {
			$this->fk_roue_secours = trim($this->fk_roue_secours);
		}
		if (isset($this->fk_flexibles)) {
			$this->fk_flexibles = trim($this->fk_flexibles);
		}
		if (isset($this->fk_grille_alu)) {
			$this->fk_grille_alu = trim($this->fk_grille_alu);
		}
		if (isset($this->fk_batteries)) {
			$this->fk_batteries = trim($this->fk_batteries);
		}
		if (isset($this->fk_jupe_G)) {
			$this->fk_jupe_G = trim($this->fk_jupe_G);
		}
		if (isset($this->fk_jupe_D)) {
			$this->fk_jupe_D = trim($this->fk_jupe_D);
		}
		if (isset($this->fk_reservoir_GO_G)) {
			$this->fk_reservoir_GO_G = trim($this->fk_reservoir_GO_G);
		}
		if (isset($this->fk_reservoir_GO_D)) {
			$this->fk_reservoir_GO_D = trim($this->fk_reservoir_GO_D);
		}
		if (isset($this->fk_reservoir_adblue)) {
			$this->fk_reservoir_adblue = trim($this->fk_reservoir_adblue);
		}
		if (isset($this->fk_reservoir_hydro)) {
			$this->fk_reservoir_hydro = trim($this->fk_reservoir_hydro);
		}
		if (isset($this->fk_aile_ar_G)) {
			$this->fk_aile_ar_G = trim($this->fk_aile_ar_G);
		}
		if (isset($this->fk_aile_ar_D)) {
			$this->fk_aile_ar_D = trim($this->fk_aile_ar_D);
		}
		if (isset($this->fk_feux_ar_G)) {
			$this->fk_feux_ar_G = trim($this->fk_feux_ar_G);
		}
		if (isset($this->fk_feux_ar_D)) {
			$this->fk_feux_ar_D = trim($this->fk_feux_ar_D);
		}
		if (isset($this->fk_support_feux_ar_G)) {
			$this->fk_support_feux_ar_G = trim($this->fk_support_feux_ar_G);
		}
		if (isset($this->fk_support_feux_ar_D)) {
			$this->fk_support_feux_ar_D = trim($this->fk_support_feux_ar_D);
		}
		if (isset($this->usure_pneu_av)) {
			$this->usure_pneu_av = trim($this->usure_pneu_av);
		}
		if (isset($this->usure_pneu_ar1)) {
			$this->usure_pneu_ar1 = trim($this->usure_pneu_ar1);
		}
		if (isset($this->usure_pneu_ar2)) {
			$this->usure_pneu_ar2 = trim($this->usure_pneu_ar2);
		}
		if (isset($this->usure_pneu_ar3)) {
			$this->usure_pneu_ar3 = trim($this->usure_pneu_ar3);
		}
		if (isset($this->div_chassis)) {
			$this->div_chassis = trim($this->div_chassis);
		}
		if (isset($this->comm_div_chassis)) {
			$this->comm_div_chassis = trim($this->comm_div_chassis);
		}
		if (isset($this->fk_kit_outils_cric)) {
			$this->fk_kit_outils_cric = trim($this->fk_kit_outils_cric);
		}
		if (isset($this->fk_chappe)) {
			$this->fk_chappe = trim($this->fk_chappe);
		}
		if (isset($this->fk_etat_interieur)) {
			$this->fk_etat_interieur = trim($this->fk_etat_interieur);
		}
		if (isset($this->fk_etat_TDB)) {
			$this->fk_etat_TDB = trim($this->fk_etat_TDB);
		}
		if (isset($this->fk_etat_couchette)) {
			$this->fk_etat_couchette = trim($this->fk_etat_couchette);
		}
		if (isset($this->fk_autoradio)) {
			$this->fk_autoradio = trim($this->fk_autoradio);
		}
		if (isset($this->fk_telephone)) {
			$this->fk_telephone = trim($this->fk_telephone);
		}
		if (isset($this->fk_tapis_sol)) {
			$this->fk_tapis_sol = trim($this->fk_tapis_sol);
		}
		if (isset($this->nb_tapis_sol)) {
			$this->nb_tapis_sol = trim($this->nb_tapis_sol);
		}
		if (isset($this->fk_parebrise)) {
			$this->fk_parebrise = trim($this->fk_parebrise);
		}
		if (isset($this->fk_cles)) {
			$this->fk_cles = trim($this->fk_cles);
		}
		if (isset($this->kilometres)) {
			$this->kilometres = trim($this->kilometres);
		}
		if (isset($this->comm_baches)) {
			$this->comm_baches = trim($this->comm_baches);
		}
		if (isset($this->comm_barres_AE)) {
			$this->comm_barres_AE = trim($this->comm_barres_AE);
		}
		if (isset($this->comm_caisse)) {
			$this->comm_caisse = trim($this->comm_caisse);
		}
		if (isset($this->comm_benne)) {
			$this->comm_benne = trim($this->comm_benne);
		}
		if (isset($this->comm_portes_ar)) {
			$this->comm_portes_ar = trim($this->comm_portes_ar);
		}
		if (isset($this->comm_plancher)) {
			$this->comm_plancher = trim($this->comm_plancher);
		}
		if (isset($this->comm_ridelles)) {
			$this->comm_ridelles = trim($this->comm_ridelles);
		}
		if (isset($this->comm_grue)) {
			$this->comm_grue = trim($this->comm_grue);
		}
		if (isset($this->comm_hayon)) {
			$this->comm_hayon = trim($this->comm_hayon);
		}
		if (isset($this->comm_crochet_attelage)) {
			$this->comm_crochet_attelage = trim($this->comm_crochet_attelage);
		}
		if (isset($this->comm_groupe_frigo)) {
			$this->comm_groupe_frigo = trim($this->comm_groupe_frigo);
		}
		if (isset($this->comm_interieur)) {
			$this->comm_interieur = trim($this->comm_interieur);
		}
		if (isset($this->comm_plancher_fosse)) {
			$this->comm_plancher_fosse = trim($this->comm_plancher_fosse);
		}
		if (isset($this->div_porteur_semi)) {
			$this->div_porteur_semi = trim($this->div_porteur_semi);
		}
		if (isset($this->comm_div_porteur_semi)) {
			$this->comm_div_porteur_semi = trim($this->comm_div_porteur_semi);
		}
		if (isset($this->autres_observations)) {
			$this->autres_observations = trim($this->autres_observations);
		}

		if (empty($this->fk_expert)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'rédacteur');
		}
		if (empty($this->lieu)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'lieu');
		}
		if (empty($this->date_exp)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'date');
		}


		if (empty($error)) {
			// Check parameters
			// Put here code to add control on parameters values

			// Insert request
			$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

			$sql .= 'fk_reprise,';
			$sql .= 'fk_rang,';
			$sql .= 'lieu,';
			$sql .= 'date_exp,';
			$sql .= 'fk_client_contact,';
			$sql .= 'fk_expert,';
			$sql .= 'fk_testeur,';
			$sql .= 'date_essai,';
			$sql .= 'moteur,';
			$sql .= 'embrayage,';
			$sql .= 'bv,';
			$sql .= 'transmission,';
			$sql .= 'pont,';
			$sql .= 'freins,';
			$sql .= 'fk_defG,';
			$sql .= 'fk_defD,';
			$sql .= 'fk_defT,';
			$sql .= 'fk_visiere,';
			$sql .= 'fk_retroG,';
			$sql .= 'fk_retroD,';
			$sql .= 'fk_retroA,';
			$sql .= 'fk_bloc_opt_av_D,';
			$sql .= 'fk_bloc_opt_av_G,';
			$sql .= 'fk_enjo_phare_av_D,';
			$sql .= 'fk_enjo_phare_av_G,';
			$sql .= 'fk_parechoc_av_D,';
			$sql .= 'fk_parechoc_av_G,';
			$sql .= 'fk_calandre,';
			$sql .= 'fk_clignotant_av_D,';
			$sql .= 'fk_clignotant_av_G,';
			$sql .= 'fk_longe_porte_D,';
			$sql .= 'fk_longe_porte_G,';
			$sql .= 'fk_porte_coffre_D,';
			$sql .= 'fk_porte_coffre_G,';
			$sql .= 'fk_porte_cabine_D,';
			$sql .= 'fk_porte_cabine_G,';
			$sql .= 'div_ext_cabine,';
			$sql .= 'comm_div_ext_cabine,';
			$sql .= 'fk_roue_secours,';
			$sql .= 'fk_flexibles,';
			$sql .= 'fk_grille_alu,';
			$sql .= 'fk_batteries,';
			$sql .= 'fk_jupe_G,';
			$sql .= 'fk_jupe_D,';
			$sql .= 'fk_reservoir_GO_G,';
			$sql .= 'fk_reservoir_GO_D,';
			$sql .= 'fk_reservoir_adblue,';
			$sql .= 'fk_reservoir_hydro,';
			$sql .= 'fk_aile_ar_G,';
			$sql .= 'fk_aile_ar_D,';
			$sql .= 'fk_feux_ar_G,';
			$sql .= 'fk_feux_ar_D,';
			$sql .= 'fk_support_feux_ar_G,';
			$sql .= 'fk_support_feux_ar_D,';
			$sql .= 'usure_pneu_av,';
			$sql .= 'usure_pneu_ar1,';
			$sql .= 'usure_pneu_ar2,';
			$sql .= 'usure_pneu_ar3,';
			$sql .= 'div_chassis,';
			$sql .= 'comm_div_chassis,';
			$sql .= 'fk_kit_outils_cric,';
			$sql .= 'fk_chappe,';
			$sql .= 'fk_etat_interieur,';
			$sql .= 'fk_etat_TDB,';
			$sql .= 'fk_etat_couchette,';
			$sql .= 'fk_autoradio,';
			$sql .= 'fk_telephone,';
			$sql .= 'fk_tapis_sol,';
			$sql .= 'nb_tapis_sol,';
			$sql .= 'fk_parebrise,';
			$sql .= 'fk_cles,';
			$sql .= 'kilometres,';
			$sql .= 'comm_baches,';
			$sql .= 'comm_barres_AE,';
			$sql .= 'comm_caisse,';
			$sql .= 'comm_benne,';
			$sql .= 'comm_portes_ar,';
			$sql .= 'comm_plancher,';
			$sql .= 'comm_ridelles,';
			$sql .= 'comm_grue,';
			$sql .= 'comm_hayon,';
			$sql .= 'comm_crochet_attelage,';
			$sql .= 'comm_groupe_frigo,';
			$sql .= 'comm_interieur,';
			$sql .= 'comm_plancher_fosse,';
			$sql .= 'div_porteur_semi,';
			$sql .= 'comm_div_porteur_semi,';
			$sql .= 'autres_observations';

			$sql .= ') VALUES (';

			$sql .= ' ' . (empty($this->fk_reprise) ? 'NULL' : $this->fk_reprise) . ',';
			$sql .= ' ' . (empty($this->fk_rang) ? 'NULL' : $this->fk_rang) . ',';
			$sql .= ' ' . (empty($this->lieu) ? 'NULL' : "'" . $this->db->escape($this->lieu) . "'") . ',';
			$sql .= ' ' . (empty($this->date_exp) || dol_strlen($this->date_exp) == 0 ? 'NULL' : "'" . $this->db->idate($this->date_exp) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_client_contact) ? 'NULL' : $this->fk_client_contact) . ',';
			$sql .= ' ' . (empty($this->fk_expert) ? 'NULL' : $this->fk_expert) . ',';
			$sql .= ' ' . (empty($this->fk_testeur) ? 'NULL' : $this->fk_testeur) . ',';
			$sql .= ' ' . (empty($this->date_essai) || dol_strlen($this->date_essai) == 0 ? 'NULL' : "'" . $this->db->idate($this->date_essai) . "'") . ',';
			$sql .= ' ' . (empty($this->moteur) ? 'NULL' : "'" . $this->db->escape($this->moteur) . "'") . ',';
			$sql .= ' ' . (empty($this->embrayage) ? 'NULL' : "'" . $this->db->escape($this->embrayage) . "'") . ',';
			$sql .= ' ' . (empty($this->bv) ? 'NULL' : "'" . $this->db->escape($this->bv) . "'") . ',';
			$sql .= ' ' . (empty($this->transmission) ? 'NULL' : "'" . $this->db->escape($this->transmission) . "'") . ',';
			$sql .= ' ' . (empty($this->pont) ? 'NULL' : "'" . $this->db->escape($this->pont) . "'") . ',';
			$sql .= ' ' . (empty($this->freins) ? 'NULL' : "'" . $this->db->escape($this->freins) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_defG) ? 'NULL' : $this->fk_defG) . ',';
			$sql .= ' ' . (empty($this->fk_defD) ? 'NULL' : $this->fk_defD) . ',';
			$sql .= ' ' . (empty($this->fk_defT) ? 'NULL' : $this->fk_defT) . ',';
			$sql .= ' ' . (empty($this->fk_visiere) ? 'NULL' : $this->fk_visiere) . ',';
			$sql .= ' ' . (empty($this->fk_retroG) ? 'NULL' : $this->fk_retroG) . ',';
			$sql .= ' ' . (empty($this->fk_retroD) ? 'NULL' : $this->fk_retroD) . ',';
			$sql .= ' ' . (empty($this->fk_retroA) ? 'NULL' : $this->fk_retroA) . ',';
			$sql .= ' ' . (empty($this->fk_bloc_opt_av_D) ? 'NULL' : $this->fk_bloc_opt_av_D) . ',';
			$sql .= ' ' . (empty($this->fk_bloc_opt_av_G) ? 'NULL' : $this->fk_bloc_opt_av_G) . ',';
			$sql .= ' ' . (empty($this->fk_enjo_phare_av_D) ? 'NULL' : $this->fk_enjo_phare_av_D) . ',';
			$sql .= ' ' . (empty($this->fk_enjo_phare_av_G) ? 'NULL' : $this->fk_enjo_phare_av_G) . ',';
			$sql .= ' ' . (empty($this->fk_parechoc_av_D) ? 'NULL' : $this->fk_parechoc_av_D) . ',';
			$sql .= ' ' . (empty($this->fk_parechoc_av_G) ? 'NULL' : $this->fk_parechoc_av_G) . ',';
			$sql .= ' ' . (empty($this->fk_calandre) ? 'NULL' : $this->fk_calandre) . ',';
			$sql .= ' ' . (empty($this->fk_clignotant_av_D) ? 'NULL' : $this->fk_clignotant_av_D) . ',';
			$sql .= ' ' . (empty($this->fk_clignotant_av_G) ? 'NULL' : $this->fk_clignotant_av_G) . ',';
			$sql .= ' ' . (empty($this->fk_longe_porte_D) ? 'NULL' : $this->fk_longe_porte_D) . ',';
			$sql .= ' ' . (empty($this->fk_longe_porte_G) ? 'NULL' : $this->fk_longe_porte_G) . ',';
			$sql .= ' ' . (empty($this->fk_porte_coffre_D) ? 'NULL' : $this->fk_porte_coffre_D) . ',';
			$sql .= ' ' . (empty($this->fk_porte_coffre_G) ? 'NULL' : $this->fk_porte_coffre_G) . ',';
			$sql .= ' ' . (empty($this->fk_porte_cabine_D) ? 'NULL' : $this->fk_porte_cabine_D) . ',';
			$sql .= ' ' . (empty($this->fk_porte_cabine_G) ? 'NULL' : $this->fk_porte_cabine_G) . ',';
			$sql .= ' ' . (empty($this->div_ext_cabine) ? 'NULL' : "'" . $this->db->escape($this->div_ext_cabine) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_div_ext_cabine) ? 'NULL' : "'" . $this->db->escape($this->comm_div_ext_cabine) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_roue_secours) ? 'NULL' : $this->fk_roue_secours) . ',';
			$sql .= ' ' . (empty($this->fk_flexibles) ? 'NULL' : $this->fk_flexibles) . ',';
			$sql .= ' ' . (empty($this->fk_grille_alu) ? 'NULL' : $this->fk_grille_alu) . ',';
			$sql .= ' ' . (empty($this->fk_batteries) ? 'NULL' : $this->fk_batteries) . ',';
			$sql .= ' ' . (empty($this->fk_jupe_G) ? 'NULL' : $this->fk_jupe_G) . ',';
			$sql .= ' ' . (empty($this->fk_jupe_D) ? 'NULL' : $this->fk_jupe_D) . ',';
			$sql .= ' ' . (empty($this->fk_reservoir_GO_G) ? 'NULL' : $this->fk_reservoir_GO_G) . ',';
			$sql .= ' ' . (empty($this->fk_reservoir_GO_D) ? 'NULL' : $this->fk_reservoir_GO_D) . ',';
			$sql .= ' ' . (empty($this->fk_reservoir_adblue) ? 'NULL' : $this->fk_reservoir_adblue) . ',';
			$sql .= ' ' . (empty($this->fk_reservoir_hydro) ? 'NULL' : $this->fk_reservoir_hydro) . ',';
			$sql .= ' ' . (empty($this->fk_aile_ar_G) ? 'NULL' : $this->fk_aile_ar_G) . ',';
			$sql .= ' ' . (empty($this->fk_aile_ar_D) ? 'NULL' : $this->fk_aile_ar_D) . ',';
			$sql .= ' ' . (empty($this->fk_feux_ar_G) ? 'NULL' : $this->fk_feux_ar_G) . ',';
			$sql .= ' ' . (empty($this->fk_feux_ar_D) ? 'NULL' : $this->fk_feux_ar_D) . ',';
			$sql .= ' ' . (empty($this->fk_support_feux_ar_G) ? 'NULL' : $this->fk_support_feux_ar_G) . ',';
			$sql .= ' ' . (empty($this->fk_support_feux_ar_D) ? 'NULL' : $this->fk_support_feux_ar_D) . ',';
			$sql .= ' ' . (empty($this->usure_pneu_av) ? 'NULL' : $this->usure_pneu_av) . ',';
			$sql .= ' ' . (empty($this->usure_pneu_ar1) ? 'NULL' : $this->usure_pneu_ar1) . ',';
			$sql .= ' ' . (empty($this->usure_pneu_ar2) ? 'NULL' : $this->usure_pneu_ar2) . ',';
			$sql .= ' ' . (empty($this->usure_pneu_ar3) ? 'NULL' : $this->usure_pneu_ar3) . ',';
			$sql .= ' ' . (empty($this->div_chassis) ? 'NULL' : "'" . $this->db->escape($this->div_chassis) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_div_chassis) ? 'NULL' : "'" . $this->db->escape($this->comm_div_chassis) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_kit_outils_cric) ? 'NULL' : $this->fk_kit_outils_cric) . ',';
			$sql .= ' ' . (empty($this->fk_chappe) ? 'NULL' : $this->fk_chappe) . ',';
			$sql .= ' ' . (empty($this->fk_etat_interieur) ? 'NULL' : $this->fk_etat_interieur) . ',';
			$sql .= ' ' . (empty($this->fk_etat_TDB) ? 'NULL' : $this->fk_etat_TDB) . ',';
			$sql .= ' ' . (empty($this->fk_etat_couchette) ? 'NULL' : $this->fk_etat_couchette) . ',';
			$sql .= ' ' . (empty($this->fk_autoradio) ? 'NULL' : $this->fk_autoradio) . ',';
			$sql .= ' ' . (empty($this->fk_telephone) ? 'NULL' : $this->fk_telephone) . ',';
			$sql .= ' ' . (empty($this->fk_tapis_sol) ? 'NULL' : $this->fk_tapis_sol) . ',';
			$sql .= ' ' . (empty($this->nb_tapis_sol) ? 'NULL' : $this->nb_tapis_sol) . ',';
			$sql .= ' ' . (empty($this->fk_parebrise) ? 'NULL' : $this->fk_parebrise) . ',';
			$sql .= ' ' . (empty($this->fk_cles) ? 'NULL' : $this->fk_cles) . ',';
			$sql .= ' ' . (empty($this->kilometres) ? 'NULL' : $this->kilometres) . ',';
			$sql .= ' ' . (empty($this->comm_baches) ? 'NULL' : "'" . $this->db->escape($this->comm_baches) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_barres_AE) ? 'NULL' : "'" . $this->db->escape($this->comm_barres_AE) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_caisse) ? 'NULL' : "'" . $this->db->escape($this->comm_caisse) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_benne) ? 'NULL' : "'" . $this->db->escape($this->comm_benne) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_portes_ar) ? 'NULL' : "'" . $this->db->escape($this->comm_portes_ar) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_plancher) ? 'NULL' : "'" . $this->db->escape($this->comm_plancher) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_ridelles) ? 'NULL' : "'" . $this->db->escape($this->comm_ridelles) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_grue) ? 'NULL' : "'" . $this->db->escape($this->comm_grue) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_hayon) ? 'NULL' : "'" . $this->db->escape($this->comm_hayon) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_crochet_attelage) ? 'NULL' : "'" . $this->db->escape($this->comm_crochet_attelage) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_groupe_frigo) ? 'NULL' : "'" . $this->db->escape($this->comm_groupe_frigo) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_interieur) ? 'NULL' : "'" . $this->db->escape($this->comm_interieur) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_plancher_fosse) ? 'NULL' : "'" . $this->db->escape($this->comm_plancher_fosse) . "'") . ',';
			$sql .= ' ' . (empty($this->div_porteur_semi) ? 'NULL' : "'" . $this->db->escape($this->div_porteur_semi) . "'") . ',';
			$sql .= ' ' . (empty($this->comm_div_porteur_semi) ? 'NULL' : "'" . $this->db->escape($this->comm_div_porteur_semi) . "'") . ',';
			$sql .= ' ' . (empty($this->autres_observations) ? 'NULL' : "'" . $this->db->escape($this->autres_observations) . "'");

			$sql .= ')';

			$this->db->begin();

			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			}

			if (! $error) {
				$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

				if (! $notrigger) {
					// Uncomment this and change MYOBJECT to your own tag if you
					// want this action to call a trigger.

					// // Call triggers
					// $result=$this->call_trigger('MYOBJECT_CREATE',$user);
					// if ($result < 0) $error++;
					// // End call triggers
				}
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return $this->id;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int $id Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null) {
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.tms,";
		$sql .= " t.fk_reprise,";
		$sql .= " t.fk_rang,";
		$sql .= " t.lieu,";
		$sql .= " t.date_exp,";
		$sql .= " t.fk_client_contact,";
		$sql .= " t.fk_expert,";
		$sql .= " t.fk_testeur,";
		$sql .= " t.date_essai,";
		$sql .= " t.moteur,";
		$sql .= " t.embrayage,";
		$sql .= " t.bv,";
		$sql .= " t.transmission,";
		$sql .= " t.pont,";
		$sql .= " t.freins,";
		$sql .= " t.fk_defG,";
		$sql .= " t.fk_defD,";
		$sql .= " t.fk_defT,";
		$sql .= " t.fk_visiere,";
		$sql .= " t.fk_retroG,";
		$sql .= " t.fk_retroD,";
		$sql .= " t.fk_retroA,";
		$sql .= " t.fk_bloc_opt_av_D,";
		$sql .= " t.fk_bloc_opt_av_G,";
		$sql .= " t.fk_enjo_phare_av_D,";
		$sql .= " t.fk_enjo_phare_av_G,";
		$sql .= " t.fk_parechoc_av_D,";
		$sql .= " t.fk_parechoc_av_G,";
		$sql .= " t.fk_calandre,";
		$sql .= " t.fk_clignotant_av_D,";
		$sql .= " t.fk_clignotant_av_G,";
		$sql .= " t.fk_longe_porte_D,";
		$sql .= " t.fk_longe_porte_G,";
		$sql .= " t.fk_porte_coffre_D,";
		$sql .= " t.fk_porte_coffre_G,";
		$sql .= " t.fk_porte_cabine_D,";
		$sql .= " t.fk_porte_cabine_G,";
		$sql .= " t.div_ext_cabine,";
		$sql .= " t.comm_div_ext_cabine,";
		$sql .= " t.fk_roue_secours,";
		$sql .= " t.fk_flexibles,";
		$sql .= " t.fk_grille_alu,";
		$sql .= " t.fk_batteries,";
		$sql .= " t.fk_jupe_G,";
		$sql .= " t.fk_jupe_D,";
		$sql .= " t.fk_reservoir_GO_G,";
		$sql .= " t.fk_reservoir_GO_D,";
		$sql .= " t.fk_reservoir_adblue,";
		$sql .= " t.fk_reservoir_hydro,";
		$sql .= " t.fk_aile_ar_G,";
		$sql .= " t.fk_aile_ar_D,";
		$sql .= " t.fk_feux_ar_G,";
		$sql .= " t.fk_feux_ar_D,";
		$sql .= " t.fk_support_feux_ar_G,";
		$sql .= " t.fk_support_feux_ar_D,";
		$sql .= " t.usure_pneu_av,";
		$sql .= " t.usure_pneu_ar1,";
		$sql .= " t.usure_pneu_ar2,";
		$sql .= " t.usure_pneu_ar3,";
		$sql .= " t.div_chassis,";
		$sql .= " t.comm_div_chassis,";
		$sql .= " t.fk_kit_outils_cric,";
		$sql .= " t.fk_chappe,";
		$sql .= " t.fk_etat_interieur,";
		$sql .= " t.fk_etat_TDB,";
		$sql .= " t.fk_etat_couchette,";
		$sql .= " t.fk_autoradio,";
		$sql .= " t.fk_telephone,";
		$sql .= " t.fk_tapis_sol,";
		$sql .= " t.nb_tapis_sol,";
		$sql .= " t.fk_parebrise,";
		$sql .= " t.fk_cles,";
		$sql .= " t.kilometres,";
		$sql .= " t.comm_baches,";
		$sql .= " t.comm_barres_AE,";
		$sql .= " t.comm_caisse,";
		$sql .= " t.comm_benne,";
		$sql .= " t.comm_portes_ar,";
		$sql .= " t.comm_plancher,";
		$sql .= " t.comm_ridelles,";
		$sql .= " t.comm_grue,";
		$sql .= " t.comm_hayon,";
		$sql .= " t.comm_crochet_attelage,";
		$sql .= " t.comm_groupe_frigo,";
		$sql .= " t.comm_interieur,";
		$sql .= " t.comm_plancher_fosse,";
		$sql .= " t.div_porteur_semi,";
		$sql .= " t.comm_div_porteur_semi,";
		$sql .= " t.autres_observations";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->tms = $this->db->jdate($obj->tms);
				$this->fk_reprise = $obj->fk_reprise;
				$this->fk_rang = $obj->fk_rang;
				$this->lieu = $obj->lieu;
				$this->date_exp = $this->db->jdate($obj->date_exp);
				$this->fk_client_contact = $obj->fk_client_contact;
				$this->fk_expert = $obj->fk_expert;
				$this->fk_testeur = $obj->fk_testeur;
				$this->date_essai = $this->db->jdate($obj->date_essai);
				$this->moteur = $obj->moteur;
				$this->embrayage = $obj->embrayage;
				$this->bv = $obj->bv;
				$this->transmission = $obj->transmission;
				$this->pont = $obj->pont;
				$this->freins = $obj->freins;
				$this->fk_defG = $obj->fk_defG;
				$this->fk_defD = $obj->fk_defD;
				$this->fk_defT = $obj->fk_defT;
				$this->fk_visiere = $obj->fk_visiere;
				$this->fk_retroG = $obj->fk_retroG;
				$this->fk_retroD = $obj->fk_retroD;
				$this->fk_retroA = $obj->fk_retroA;
				$this->fk_bloc_opt_av_D = $obj->fk_bloc_opt_av_D;
				$this->fk_bloc_opt_av_G = $obj->fk_bloc_opt_av_G;
				$this->fk_enjo_phare_av_D = $obj->fk_enjo_phare_av_D;
				$this->fk_enjo_phare_av_G = $obj->fk_enjo_phare_av_G;
				$this->fk_parechoc_av_D = $obj->fk_parechoc_av_D;
				$this->fk_parechoc_av_G = $obj->fk_parechoc_av_G;
				$this->fk_calandre = $obj->fk_calandre;
				$this->fk_clignotant_av_D = $obj->fk_clignotant_av_D;
				$this->fk_clignotant_av_G = $obj->fk_clignotant_av_G;
				$this->fk_longe_porte_D = $obj->fk_longe_porte_D;
				$this->fk_longe_porte_G = $obj->fk_longe_porte_G;
				$this->fk_porte_coffre_D = $obj->fk_porte_coffre_D;
				$this->fk_porte_coffre_G = $obj->fk_porte_coffre_G;
				$this->fk_porte_cabine_D = $obj->fk_porte_cabine_D;
				$this->fk_porte_cabine_G = $obj->fk_porte_cabine_G;
				$this->div_ext_cabine = $obj->div_ext_cabine;
				$this->comm_div_ext_cabine = $obj->comm_div_ext_cabine;
				$this->fk_roue_secours = $obj->fk_roue_secours;
				$this->fk_flexibles = $obj->fk_flexibles;
				$this->fk_grille_alu = $obj->fk_grille_alu;
				$this->fk_batteries = $obj->fk_batteries;
				$this->fk_jupe_G = $obj->fk_jupe_G;
				$this->fk_jupe_D = $obj->fk_jupe_D;
				$this->fk_reservoir_GO_G = $obj->fk_reservoir_GO_G;
				$this->fk_reservoir_GO_D = $obj->fk_reservoir_GO_D;
				$this->fk_reservoir_adblue = $obj->fk_reservoir_adblue;
				$this->fk_reservoir_hydro = $obj->fk_reservoir_hydro;
				$this->fk_aile_ar_G = $obj->fk_aile_ar_G;
				$this->fk_aile_ar_D = $obj->fk_aile_ar_D;
				$this->fk_feux_ar_G = $obj->fk_feux_ar_G;
				$this->fk_feux_ar_D = $obj->fk_feux_ar_D;
				$this->fk_support_feux_ar_G = $obj->fk_support_feux_ar_G;
				$this->fk_support_feux_ar_D = $obj->fk_support_feux_ar_D;
				$this->usure_pneu_av = $obj->usure_pneu_av;
				$this->usure_pneu_ar1 = $obj->usure_pneu_ar1;
				$this->usure_pneu_ar2 = $obj->usure_pneu_ar2;
				$this->usure_pneu_ar3 = $obj->usure_pneu_ar3;
				$this->div_chassis = $obj->div_chassis;
				$this->comm_div_chassis = $obj->comm_div_chassis;
				$this->fk_kit_outils_cric = $obj->fk_kit_outils_cric;
				$this->fk_chappe = $obj->fk_chappe;
				$this->fk_etat_interieur = $obj->fk_etat_interieur;
				$this->fk_etat_TDB = $obj->fk_etat_TDB;
				$this->fk_etat_couchette = $obj->fk_etat_couchette;
				$this->fk_autoradio = $obj->fk_autoradio;
				$this->fk_telephone = $obj->fk_telephone;
				$this->fk_tapis_sol = $obj->fk_tapis_sol;
				$this->nb_tapis_sol = $obj->nb_tapis_sol;
				$this->fk_parebrise = $obj->fk_parebrise;
				$this->fk_cles = $obj->fk_cles;
				$this->kilometres = $obj->kilometres;
				$this->comm_baches = $obj->comm_baches;
				$this->comm_barres_AE = $obj->comm_barres_AE;
				$this->comm_caisse = $obj->comm_caisse;
				$this->comm_benne = $obj->comm_benne;
				$this->comm_portes_ar = $obj->comm_portes_ar;
				$this->comm_plancher = $obj->comm_plancher;
				$this->comm_ridelles = $obj->comm_ridelles;
				$this->comm_grue = $obj->comm_grue;
				$this->comm_hayon = $obj->comm_hayon;
				$this->comm_crochet_attelage = $obj->comm_crochet_attelage;
				$this->comm_groupe_frigo = $obj->comm_groupe_frigo;
				$this->comm_interieur = $obj->comm_interieur;
				$this->comm_plancher_fosse = $obj->comm_plancher_fosse;
				$this->div_porteur_semi = $obj->div_porteur_semi;
				$this->comm_div_porteur_semi = $obj->comm_div_porteur_semi;
				$this->autres_observations = $obj->autres_observations;
			}
			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int $limit offset limit
	 * @param int $offset offset limit
	 * @param array $filter filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND') {
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.tms,";
		$sql .= " t.fk_reprise,";
		$sql .= " t.fk_rang,";
		$sql .= " t.lieu,";
		$sql .= " t.date_exp,";
		$sql .= " t.fk_client_contact,";
		$sql .= " t.fk_expert,";
		$sql .= " t.fk_testeur,";
		$sql .= " t.date_essai,";
		$sql .= " t.moteur,";
		$sql .= " t.embrayage,";
		$sql .= " t.bv,";
		$sql .= " t.transmission,";
		$sql .= " t.pont,";
		$sql .= " t.freins,";
		$sql .= " t.fk_defG,";
		$sql .= " t.fk_defD,";
		$sql .= " t.fk_defT,";
		$sql .= " t.fk_visiere,";
		$sql .= " t.fk_retroG,";
		$sql .= " t.fk_retroD,";
		$sql .= " t.fk_retroA,";
		$sql .= " t.fk_bloc_opt_av_D,";
		$sql .= " t.fk_bloc_opt_av_G,";
		$sql .= " t.fk_enjo_phare_av_D,";
		$sql .= " t.fk_enjo_phare_av_G,";
		$sql .= " t.fk_parechoc_av_D,";
		$sql .= " t.fk_parechoc_av_G,";
		$sql .= " t.fk_calandre,";
		$sql .= " t.fk_clignotant_av_D,";
		$sql .= " t.fk_clignotant_av_G,";
		$sql .= " t.fk_longe_porte_D,";
		$sql .= " t.fk_longe_porte_G,";
		$sql .= " t.fk_porte_coffre_D,";
		$sql .= " t.fk_porte_coffre_G,";
		$sql .= " t.fk_porte_cabine_D,";
		$sql .= " t.fk_porte_cabine_G,";
		$sql .= " t.div_ext_cabine,";
		$sql .= " t.comm_div_ext_cabine,";
		$sql .= " t.fk_roue_secours,";
		$sql .= " t.fk_flexibles,";
		$sql .= " t.fk_grille_alu,";
		$sql .= " t.fk_batteries,";
		$sql .= " t.fk_jupe_G,";
		$sql .= " t.fk_jupe_D,";
		$sql .= " t.fk_reservoir_GO_G,";
		$sql .= " t.fk_reservoir_GO_D,";
		$sql .= " t.fk_reservoir_adblue,";
		$sql .= " t.fk_reservoir_hydro,";
		$sql .= " t.fk_aile_ar_G,";
		$sql .= " t.fk_aile_ar_D,";
		$sql .= " t.fk_feux_ar_G,";
		$sql .= " t.fk_feux_ar_D,";
		$sql .= " t.fk_support_feux_ar_G,";
		$sql .= " t.fk_support_feux_ar_D,";
		$sql .= " t.usure_pneu_av,";
		$sql .= " t.usure_pneu_ar1,";
		$sql .= " t.usure_pneu_ar2,";
		$sql .= " t.usure_pneu_ar3,";
		$sql .= " t.div_chassis,";
		$sql .= " t.comm_div_chassis,";
		$sql .= " t.fk_kit_outils_cric,";
		$sql .= " t.fk_chappe,";
		$sql .= " t.fk_etat_interieur,";
		$sql .= " t.fk_etat_TDB,";
		$sql .= " t.fk_etat_couchette,";
		$sql .= " t.fk_autoradio,";
		$sql .= " t.fk_telephone,";
		$sql .= " t.fk_tapis_sol,";
		$sql .= " t.nb_tapis_sol,";
		$sql .= " t.fk_parebrise,";
		$sql .= " t.fk_cles,";
		$sql .= " t.kilometres,";
		$sql .= " t.comm_baches,";
		$sql .= " t.comm_barres_AE,";
		$sql .= " t.comm_caisse,";
		$sql .= " t.comm_benne,";
		$sql .= " t.comm_portes_ar,";
		$sql .= " t.comm_plancher,";
		$sql .= " t.comm_ridelles,";
		$sql .= " t.comm_grue,";
		$sql .= " t.comm_hayon,";
		$sql .= " t.comm_crochet_attelage,";
		$sql .= " t.comm_groupe_frigo,";
		$sql .= " t.comm_interieur,";
		$sql .= " t.comm_plancher_fosse,";
		$sql .= " t.div_porteur_semi,";
		$sql .= " t.comm_div_porteur_semi,";
		$sql .= " t.autres_observations";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ( $filter as $key => $value ) {
				if ($key=='t.fk_reprise') {
					$sqlwhere[] = $key . '=' . $value;
				} else {
					$sqlwhere[] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' ' . $filtermode . ' ', $sqlwhere);
		}

		if (! empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (! empty($limit)) {
			$sql .= ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();
		$this->liste_exp=array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ( $obj = $this->db->fetch_object($resql) ) {
				$line = new ExpertisesLine();

				$line->id = $obj->rowid;

				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_reprise = $obj->fk_reprise;
				$line->fk_rang = $obj->fk_rang;
				$line->lieu = $obj->lieu;
				$line->date_exp = $this->db->jdate($obj->date_exp);
				$line->fk_client_contact = $obj->fk_client_contact;
				$line->fk_expert = $obj->fk_expert;
				$line->fk_testeur = $obj->fk_testeur;
				$line->date_essai = $this->db->jdate($obj->date_essai);
				$line->moteur = $obj->moteur;
				$line->embrayage = $obj->embrayage;
				$line->bv = $obj->bv;
				$line->transmission = $obj->transmission;
				$line->pont = $obj->pont;
				$line->freins = $obj->freins;
				$line->fk_defG = $obj->fk_defG;
				$line->fk_defD = $obj->fk_defD;
				$line->fk_defT = $obj->fk_defT;
				$line->fk_visiere = $obj->fk_visiere;
				$line->fk_retroG = $obj->fk_retroG;
				$line->fk_retroD = $obj->fk_retroD;
				$line->fk_retroA = $obj->fk_retroA;
				$line->fk_bloc_opt_av_D = $obj->fk_bloc_opt_av_D;
				$line->fk_bloc_opt_av_G = $obj->fk_bloc_opt_av_G;
				$line->fk_enjo_phare_av_D = $obj->fk_enjo_phare_av_D;
				$line->fk_enjo_phare_av_G = $obj->fk_enjo_phare_av_G;
				$line->fk_parechoc_av_D = $obj->fk_parechoc_av_D;
				$line->fk_parechoc_av_G = $obj->fk_parechoc_av_G;
				$line->fk_calandre = $obj->fk_calandre;
				$line->fk_clignotant_av_D = $obj->fk_clignotant_av_D;
				$line->fk_clignotant_av_G = $obj->fk_clignotant_av_G;
				$line->fk_longe_porte_D = $obj->fk_longe_porte_D;
				$line->fk_longe_porte_G = $obj->fk_longe_porte_G;
				$line->fk_porte_coffre_D = $obj->fk_porte_coffre_D;
				$line->fk_porte_coffre_G = $obj->fk_porte_coffre_G;
				$line->fk_porte_cabine_D = $obj->fk_porte_cabine_D;
				$line->fk_porte_cabine_G = $obj->fk_porte_cabine_G;
				$line->div_ext_cabine = $obj->div_ext_cabine;
				$line->comm_div_ext_cabine = $obj->comm_div_ext_cabine;
				$line->fk_roue_secours = $obj->fk_roue_secours;
				$line->fk_flexibles = $obj->fk_flexibles;
				$line->fk_grille_alu = $obj->fk_grille_alu;
				$line->fk_batteries = $obj->fk_batteries;
				$line->fk_jupe_G = $obj->fk_jupe_G;
				$line->fk_jupe_D = $obj->fk_jupe_D;
				$line->fk_reservoir_GO_G = $obj->fk_reservoir_GO_G;
				$line->fk_reservoir_GO_D = $obj->fk_reservoir_GO_D;
				$line->fk_reservoir_adblue = $obj->fk_reservoir_adblue;
				$line->fk_reservoir_hydro = $obj->fk_reservoir_hydro;
				$line->fk_aile_ar_G = $obj->fk_aile_ar_G;
				$line->fk_aile_ar_D = $obj->fk_aile_ar_D;
				$line->fk_feux_ar_G = $obj->fk_feux_ar_G;
				$line->fk_feux_ar_D = $obj->fk_feux_ar_D;
				$line->fk_support_feux_ar_G = $obj->fk_support_feux_ar_G;
				$line->fk_support_feux_ar_D = $obj->fk_support_feux_ar_D;
				$line->usure_pneu_av = $obj->usure_pneu_av;
				$line->usure_pneu_ar1 = $obj->usure_pneu_ar1;
				$line->usure_pneu_ar2 = $obj->usure_pneu_ar2;
				$line->usure_pneu_ar3 = $obj->usure_pneu_ar3;
				$line->div_chassis = $obj->div_chassis;
				$line->comm_div_chassis = $obj->comm_div_chassis;
				$line->fk_kit_outils_cric = $obj->fk_kit_outils_cric;
				$line->fk_chappe = $obj->fk_chappe;
				$line->fk_etat_interieur = $obj->fk_etat_interieur;
				$line->fk_etat_TDB = $obj->fk_etat_TDB;
				$line->fk_etat_couchette = $obj->fk_etat_couchette;
				$line->fk_autoradio = $obj->fk_autoradio;
				$line->fk_telephone = $obj->fk_telephone;
				$line->fk_tapis_sol = $obj->fk_tapis_sol;
				$line->nb_tapis_sol = $obj->nb_tapis_sol;
				$line->fk_parebrise = $obj->fk_parebrise;
				$line->fk_cles = $obj->fk_cles;
				$line->kilometres = $obj->kilometres;
				$line->comm_baches = $obj->comm_baches;
				$line->comm_barres_AE = $obj->comm_barres_AE;
				$line->comm_caisse = $obj->comm_caisse;
				$line->comm_benne = $obj->comm_benne;
				$line->comm_portes_ar = $obj->comm_portes_ar;
				$line->comm_plancher = $obj->comm_plancher;
				$line->comm_ridelles = $obj->comm_ridelles;
				$line->comm_grue = $obj->comm_grue;
				$line->comm_hayon = $obj->comm_hayon;
				$line->comm_crochet_attelage = $obj->comm_crochet_attelage;
				$line->comm_groupe_frigo = $obj->comm_groupe_frigo;
				$line->comm_interieur = $obj->comm_interieur;
				$line->comm_plancher_fosse = $obj->comm_plancher_fosse;
				$line->div_porteur_semi = $obj->div_porteur_semi;
				$line->comm_div_porteur_semi = $obj->comm_div_porteur_semi;
				$line->autres_observations = $obj->autres_observations;

				$this->lines[$line->id] = $line;

				$this->liste_exp[]=$line->id;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param User $user User that modifies
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false) {

		global $langs;

		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters

		if (isset($this->fk_reprise)) {
			$this->fk_reprise = trim($this->fk_reprise);
		}
		if (isset($this->fk_rang)) {
			$this->fk_rang = trim($this->fk_rang);
		}
		if (isset($this->lieu)) {
			$this->lieu = trim($this->lieu);
		}
		if (isset($this->fk_client_contact)) {
			$this->fk_client_contact = trim($this->fk_client_contact);
		}
		if (isset($this->fk_expert)) {
			$this->fk_expert = trim($this->fk_expert);
		}
		if (isset($this->fk_testeur)) {
			$this->fk_testeur = trim($this->fk_testeur);
		}
		if (isset($this->moteur)) {
			$this->moteur = trim($this->moteur);
		}
		if (isset($this->embrayage)) {
			$this->embrayage = trim($this->embrayage);
		}
		if (isset($this->bv)) {
			$this->bv = trim($this->bv);
		}
		if (isset($this->transmission)) {
			$this->transmission = trim($this->transmission);
		}
		if (isset($this->pont)) {
			$this->pont = trim($this->pont);
		}
		if (isset($this->freins)) {
			$this->freins = trim($this->freins);
		}
		if (isset($this->fk_defG)) {
			$this->fk_defG = trim($this->fk_defG);
		}
		if (isset($this->fk_defD)) {
			$this->fk_defD = trim($this->fk_defD);
		}
		if (isset($this->fk_defT)) {
			$this->fk_defT = trim($this->fk_defT);
		}
		if (isset($this->fk_visiere)) {
			$this->fk_visiere = trim($this->fk_visiere);
		}
		if (isset($this->fk_retroG)) {
			$this->fk_retroG = trim($this->fk_retroG);
		}
		if (isset($this->fk_retroD)) {
			$this->fk_retroD = trim($this->fk_retroD);
		}
		if (isset($this->fk_retroA)) {
			$this->fk_retroA = trim($this->fk_retroA);
		}
		if (isset($this->fk_bloc_opt_av_D)) {
			$this->fk_bloc_opt_av_D = trim($this->fk_bloc_opt_av_D);
		}
		if (isset($this->fk_bloc_opt_av_G)) {
			$this->fk_bloc_opt_av_G = trim($this->fk_bloc_opt_av_G);
		}
		if (isset($this->fk_enjo_phare_av_D)) {
			$this->fk_enjo_phare_av_D = trim($this->fk_enjo_phare_av_D);
		}
		if (isset($this->fk_enjo_phare_av_G)) {
			$this->fk_enjo_phare_av_G = trim($this->fk_enjo_phare_av_G);
		}
		if (isset($this->fk_parechoc_av_D)) {
			$this->fk_parechoc_av_D = trim($this->fk_parechoc_av_D);
		}
		if (isset($this->fk_parechoc_av_G)) {
			$this->fk_parechoc_av_G = trim($this->fk_parechoc_av_G);
		}
		if (isset($this->fk_calandre)) {
			$this->fk_calandre = trim($this->fk_calandre);
		}
		if (isset($this->fk_clignotant_av_D)) {
			$this->fk_clignotant_av_D = trim($this->fk_clignotant_av_D);
		}
		if (isset($this->fk_clignotant_av_G)) {
			$this->fk_clignotant_av_G = trim($this->fk_clignotant_av_G);
		}
		if (isset($this->fk_longe_porte_D)) {
			$this->fk_longe_porte_D = trim($this->fk_longe_porte_D);
		}
		if (isset($this->fk_longe_porte_G)) {
			$this->fk_longe_porte_G = trim($this->fk_longe_porte_G);
		}
		if (isset($this->fk_porte_coffre_D)) {
			$this->fk_porte_coffre_D = trim($this->fk_porte_coffre_D);
		}
		if (isset($this->fk_porte_coffre_G)) {
			$this->fk_porte_coffre_G = trim($this->fk_porte_coffre_G);
		}
		if (isset($this->fk_porte_cabine_D)) {
			$this->fk_porte_cabine_D = trim($this->fk_porte_cabine_D);
		}
		if (isset($this->fk_porte_cabine_G)) {
			$this->fk_porte_cabine_G = trim($this->fk_porte_cabine_G);
		}
		if (isset($this->div_ext_cabine)) {
			$this->div_ext_cabine = trim($this->div_ext_cabine);
		}
		if (isset($this->comm_div_ext_cabine)) {
			$this->comm_div_ext_cabine = trim($this->comm_div_ext_cabine);
		}
		if (isset($this->fk_roue_secours)) {
			$this->fk_roue_secours = trim($this->fk_roue_secours);
		}
		if (isset($this->fk_flexibles)) {
			$this->fk_flexibles = trim($this->fk_flexibles);
		}
		if (isset($this->fk_grille_alu)) {
			$this->fk_grille_alu = trim($this->fk_grille_alu);
		}
		if (isset($this->fk_batteries)) {
			$this->fk_batteries = trim($this->fk_batteries);
		}
		if (isset($this->fk_jupe_G)) {
			$this->fk_jupe_G = trim($this->fk_jupe_G);
		}
		if (isset($this->fk_jupe_D)) {
			$this->fk_jupe_D = trim($this->fk_jupe_D);
		}
		if (isset($this->fk_reservoir_GO_G)) {
			$this->fk_reservoir_GO_G = trim($this->fk_reservoir_GO_G);
		}
		if (isset($this->fk_reservoir_GO_D)) {
			$this->fk_reservoir_GO_D = trim($this->fk_reservoir_GO_D);
		}
		if (isset($this->fk_reservoir_adblue)) {
			$this->fk_reservoir_adblue = trim($this->fk_reservoir_adblue);
		}
		if (isset($this->fk_reservoir_hydro)) {
			$this->fk_reservoir_hydro = trim($this->fk_reservoir_hydro);
		}
		if (isset($this->fk_aile_ar_G)) {
			$this->fk_aile_ar_G = trim($this->fk_aile_ar_G);
		}
		if (isset($this->fk_aile_ar_D)) {
			$this->fk_aile_ar_D = trim($this->fk_aile_ar_D);
		}
		if (isset($this->fk_feux_ar_G)) {
			$this->fk_feux_ar_G = trim($this->fk_feux_ar_G);
		}
		if (isset($this->fk_feux_ar_D)) {
			$this->fk_feux_ar_D = trim($this->fk_feux_ar_D);
		}
		if (isset($this->fk_support_feux_ar_G)) {
			$this->fk_support_feux_ar_G = trim($this->fk_support_feux_ar_G);
		}
		if (isset($this->fk_support_feux_ar_D)) {
			$this->fk_support_feux_ar_D = trim($this->fk_support_feux_ar_D);
		}
		if (isset($this->usure_pneu_av)) {
			$this->usure_pneu_av = trim($this->usure_pneu_av);
		}
		if (isset($this->usure_pneu_ar1)) {
			$this->usure_pneu_ar1 = trim($this->usure_pneu_ar1);
		}
		if (isset($this->usure_pneu_ar2)) {
			$this->usure_pneu_ar2 = trim($this->usure_pneu_ar2);
		}
		if (isset($this->usure_pneu_ar3)) {
			$this->usure_pneu_ar3 = trim($this->usure_pneu_ar3);
		}
		if (isset($this->div_chassis)) {
			$this->div_chassis = trim($this->div_chassis);
		}
		if (isset($this->comm_div_chassis)) {
			$this->comm_div_chassis = trim($this->comm_div_chassis);
		}
		if (isset($this->fk_kit_outils_cric)) {
			$this->fk_kit_outils_cric = trim($this->fk_kit_outils_cric);
		}
		if (isset($this->fk_chappe)) {
			$this->fk_chappe = trim($this->fk_chappe);
		}
		if (isset($this->fk_etat_interieur)) {
			$this->fk_etat_interieur = trim($this->fk_etat_interieur);
		}
		if (isset($this->fk_etat_TDB)) {
			$this->fk_etat_TDB = trim($this->fk_etat_TDB);
		}
		if (isset($this->fk_etat_couchette)) {
			$this->fk_etat_couchette = trim($this->fk_etat_couchette);
		}
		if (isset($this->fk_autoradio)) {
			$this->fk_autoradio = trim($this->fk_autoradio);
		}
		if (isset($this->fk_telephone)) {
			$this->fk_telephone = trim($this->fk_telephone);
		}
		if (isset($this->fk_tapis_sol)) {
			$this->fk_tapis_sol = trim($this->fk_tapis_sol);
		}
		if (isset($this->nb_tapis_sol)) {
			$this->nb_tapis_sol = trim($this->nb_tapis_sol);
		}
		if (isset($this->fk_parebrise)) {
			$this->fk_parebrise = trim($this->fk_parebrise);
		}
		if (isset($this->fk_cles)) {
			$this->fk_cles = trim($this->fk_cles);
		}
		if (isset($this->kilometres)) {
			$this->kilometres = trim($this->kilometres);
		}
		if (isset($this->comm_baches)) {
			$this->comm_baches = trim($this->comm_baches);
		}
		if (isset($this->comm_barres_AE)) {
			$this->comm_barres_AE = trim($this->comm_barres_AE);
		}
		if (isset($this->comm_caisse)) {
			$this->comm_caisse = trim($this->comm_caisse);
		}
		if (isset($this->comm_benne)) {
			$this->comm_benne = trim($this->comm_benne);
		}
		if (isset($this->comm_portes_ar)) {
			$this->comm_portes_ar = trim($this->comm_portes_ar);
		}
		if (isset($this->comm_plancher)) {
			$this->comm_plancher = trim($this->comm_plancher);
		}
		if (isset($this->comm_ridelles)) {
			$this->comm_ridelles = trim($this->comm_ridelles);
		}
		if (isset($this->comm_grue)) {
			$this->comm_grue = trim($this->comm_grue);
		}
		if (isset($this->comm_hayon)) {
			$this->comm_hayon = trim($this->comm_hayon);
		}
		if (isset($this->comm_crochet_attelage)) {
			$this->comm_crochet_attelage = trim($this->comm_crochet_attelage);
		}
		if (isset($this->comm_groupe_frigo)) {
			$this->comm_groupe_frigo = trim($this->comm_groupe_frigo);
		}
		if (isset($this->comm_interieur)) {
			$this->comm_interieur = trim($this->comm_interieur);
		}
		if (isset($this->comm_plancher_fosse)) {
			$this->comm_plancher_fosse = trim($this->comm_plancher_fosse);
		}
		if (isset($this->div_porteur_semi)) {
			$this->div_porteur_semi = trim($this->div_porteur_semi);
		}
		if (isset($this->comm_div_porteur_semi)) {
			$this->comm_div_porteur_semi = trim($this->comm_div_porteur_semi);
		}
		if (isset($this->autres_observations)) {
			$this->autres_observations = trim($this->autres_observations);
		}

		if (empty($this->fk_expert)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'rédacteur');
		}
		if (empty($this->lieu)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'lieu');
		}
		if (empty($this->date_exp)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'date');
		}
		if (empty($this->fk_client_contact)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'client');
		}

		if (empty($error)) {

			// Check parameters
			// Put here code to add a control on parameters values

			// Update request
			$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

			$sql .= ' tms = ' . (dol_strlen($this->tms) != 0 ? "'" . $this->db->idate($this->tms) . "'" : "'" . $this->db->idate(dol_now()) . "'") . ',';
			$sql .= ' fk_reprise = ' . (!empty($this->fk_reprise) ? $this->fk_reprise : "null") . ',';
			$sql .= ' fk_rang = ' . (!empty($this->fk_rang) ? $this->fk_rang : "null") . ',';
			$sql .= ' lieu = ' . (!empty($this->lieu) ? "'" . $this->db->escape($this->lieu) . "'" : "null") . ',';
			$sql .= ' date_exp = ' . (! !empty($this->date_exp) || dol_strlen($this->date_exp) != 0 ? "'" . $this->db->idate($this->date_exp) . "'" : 'null') . ',';
			$sql .= ' fk_client_contact = ' . (!empty($this->fk_client_contact) ? $this->fk_client_contact : "null") . ',';
			$sql .= ' fk_expert = ' . (!empty($this->fk_expert) ? $this->fk_expert : "null") . ',';
			$sql .= ' fk_testeur = ' . (!empty($this->fk_testeur) ? $this->fk_testeur : "null") . ',';
			$sql .= ' date_essai = ' . (! !empty($this->date_essai) || dol_strlen($this->date_essai) != 0 ? "'" . $this->db->idate($this->date_essai) . "'" : 'null') . ',';
			$sql .= ' moteur = ' . (!empty($this->moteur) ? "'" . $this->db->escape($this->moteur) . "'" : "null") . ',';
			$sql .= ' embrayage = ' . (!empty($this->embrayage) ? "'" . $this->db->escape($this->embrayage) . "'" : "null") . ',';
			$sql .= ' bv = ' . (!empty($this->bv) ? "'" . $this->db->escape($this->bv) . "'" : "null") . ',';
			$sql .= ' transmission = ' . (!empty($this->transmission) ? "'" . $this->db->escape($this->transmission) . "'" : "null") . ',';
			$sql .= ' pont = ' . (!empty($this->pont) ? "'" . $this->db->escape($this->pont) . "'" : "null") . ',';
			$sql .= ' freins = ' . (!empty($this->freins) ? "'" . $this->db->escape($this->freins) . "'" : "null") . ',';
			$sql .= ' fk_defG = ' . (!empty($this->fk_defG) ? $this->fk_defG : "null") . ',';
			$sql .= ' fk_defD = ' . (!empty($this->fk_defD) ? $this->fk_defD : "null") . ',';
			$sql .= ' fk_defT = ' . (!empty($this->fk_defT) ? $this->fk_defT : "null") . ',';
			$sql .= ' fk_visiere = ' . (!empty($this->fk_visiere) ? $this->fk_visiere : "null") . ',';
			$sql .= ' fk_retroG = ' . (!empty($this->fk_retroG) ? $this->fk_retroG : "null") . ',';
			$sql .= ' fk_retroD = ' . (!empty($this->fk_retroD) ? $this->fk_retroD : "null") . ',';
			$sql .= ' fk_retroA = ' . (!empty($this->fk_retroA) ? $this->fk_retroA : "null") . ',';
			$sql .= ' fk_bloc_opt_av_D = ' . (!empty($this->fk_bloc_opt_av_D) ? $this->fk_bloc_opt_av_D : "null") . ',';
			$sql .= ' fk_bloc_opt_av_G = ' . (!empty($this->fk_bloc_opt_av_G) ? $this->fk_bloc_opt_av_G : "null") . ',';
			$sql .= ' fk_enjo_phare_av_D = ' . (!empty($this->fk_enjo_phare_av_D) ? $this->fk_enjo_phare_av_D : "null") . ',';
			$sql .= ' fk_enjo_phare_av_G = ' . (!empty($this->fk_enjo_phare_av_G) ? $this->fk_enjo_phare_av_G : "null") . ',';
			$sql .= ' fk_parechoc_av_D = ' . (!empty($this->fk_parechoc_av_D) ? $this->fk_parechoc_av_D : "null") . ',';
			$sql .= ' fk_parechoc_av_G = ' . (!empty($this->fk_parechoc_av_G) ? $this->fk_parechoc_av_G : "null") . ',';
			$sql .= ' fk_calandre = ' . (!empty($this->fk_calandre) ? $this->fk_calandre : "null") . ',';
			$sql .= ' fk_clignotant_av_D = ' . (!empty($this->fk_clignotant_av_D) ? $this->fk_clignotant_av_D : "null") . ',';
			$sql .= ' fk_clignotant_av_G = ' . (!empty($this->fk_clignotant_av_G) ? $this->fk_clignotant_av_G : "null") . ',';
			$sql .= ' fk_longe_porte_D = ' . (!empty($this->fk_longe_porte_D) ? $this->fk_longe_porte_D : "null") . ',';
			$sql .= ' fk_longe_porte_G = ' . (!empty($this->fk_longe_porte_G) ? $this->fk_longe_porte_G : "null") . ',';
			$sql .= ' fk_porte_coffre_D = ' . (!empty($this->fk_porte_coffre_D) ? $this->fk_porte_coffre_D : "null") . ',';
			$sql .= ' fk_porte_coffre_G = ' . (!empty($this->fk_porte_coffre_G) ? $this->fk_porte_coffre_G : "null") . ',';
			$sql .= ' fk_porte_cabine_D = ' . (!empty($this->fk_porte_cabine_D) ? $this->fk_porte_cabine_D : "null") . ',';
			$sql .= ' fk_porte_cabine_G = ' . (!empty($this->fk_porte_cabine_G) ? $this->fk_porte_cabine_G : "null") . ',';
			$sql .= ' div_ext_cabine = ' . (!empty($this->div_ext_cabine) ? "'" . $this->db->escape($this->div_ext_cabine) . "'" : "null") . ',';
			$sql .= ' comm_div_ext_cabine = ' . (!empty($this->comm_div_ext_cabine) ? "'" . $this->db->escape($this->comm_div_ext_cabine) . "'" : "null") . ',';
			$sql .= ' fk_roue_secours = ' . (!empty($this->fk_roue_secours) ? $this->fk_roue_secours : "null") . ',';
			$sql .= ' fk_flexibles = ' . (!empty($this->fk_flexibles) ? $this->fk_flexibles : "null") . ',';
			$sql .= ' fk_grille_alu = ' . (!empty($this->fk_grille_alu) ? $this->fk_grille_alu : "null") . ',';
			$sql .= ' fk_batteries = ' . (!empty($this->fk_batteries) ? $this->fk_batteries : "null") . ',';
			$sql .= ' fk_jupe_G = ' . (!empty($this->fk_jupe_G) ? $this->fk_jupe_G : "null") . ',';
			$sql .= ' fk_jupe_D = ' . (!empty($this->fk_jupe_D) ? $this->fk_jupe_D : "null") . ',';
			$sql .= ' fk_reservoir_GO_G = ' . (!empty($this->fk_reservoir_GO_G) ? $this->fk_reservoir_GO_G : "null") . ',';
			$sql .= ' fk_reservoir_GO_D = ' . (!empty($this->fk_reservoir_GO_D) ? $this->fk_reservoir_GO_D : "null") . ',';
			$sql .= ' fk_reservoir_adblue = ' . (!empty($this->fk_reservoir_adblue) ? $this->fk_reservoir_adblue : "null") . ',';
			$sql .= ' fk_reservoir_hydro = ' . (!empty($this->fk_reservoir_hydro) ? $this->fk_reservoir_hydro : "null") . ',';
			$sql .= ' fk_aile_ar_G = ' . (!empty($this->fk_aile_ar_G) ? $this->fk_aile_ar_G : "null") . ',';
			$sql .= ' fk_aile_ar_D = ' . (!empty($this->fk_aile_ar_D) ? $this->fk_aile_ar_D : "null") . ',';
			$sql .= ' fk_feux_ar_G = ' . (!empty($this->fk_feux_ar_G) ? $this->fk_feux_ar_G : "null") . ',';
			$sql .= ' fk_feux_ar_D = ' . (!empty($this->fk_feux_ar_D) ? $this->fk_feux_ar_D : "null") . ',';
			$sql .= ' fk_support_feux_ar_G = ' . (!empty($this->fk_support_feux_ar_G) ? $this->fk_support_feux_ar_G : "null") . ',';
			$sql .= ' fk_support_feux_ar_D = ' . (!empty($this->fk_support_feux_ar_D) ? $this->fk_support_feux_ar_D : "null") . ',';
			$sql .= ' usure_pneu_av = ' . (!empty($this->usure_pneu_av) ? $this->usure_pneu_av : "null") . ',';
			$sql .= ' usure_pneu_ar1 = ' . (!empty($this->usure_pneu_ar1) ? $this->usure_pneu_ar1 : "null") . ',';
			$sql .= ' usure_pneu_ar2 = ' . (!empty($this->usure_pneu_ar2) ? $this->usure_pneu_ar2 : "null") . ',';
			$sql .= ' usure_pneu_ar3 = ' . (!empty($this->usure_pneu_ar3) ? $this->usure_pneu_ar3 : "null") . ',';
			$sql .= ' div_chassis = ' . (!empty($this->div_chassis) ? "'" . $this->db->escape($this->div_chassis) . "'" : "null") . ',';
			$sql .= ' comm_div_chassis = ' . (!empty($this->comm_div_chassis) ? "'" . $this->db->escape($this->comm_div_chassis) . "'" : "null") . ',';
			$sql .= ' fk_kit_outils_cric = ' . (!empty($this->fk_kit_outils_cric) ? $this->fk_kit_outils_cric : "null") . ',';
			$sql .= ' fk_chappe = ' . (!empty($this->fk_chappe) ? $this->fk_chappe : "null") . ',';
			$sql .= ' fk_etat_interieur = ' . (!empty($this->fk_etat_interieur) ? $this->fk_etat_interieur : "null") . ',';
			$sql .= ' fk_etat_TDB = ' . (!empty($this->fk_etat_TDB) ? $this->fk_etat_TDB : "null") . ',';
			$sql .= ' fk_etat_couchette = ' . (!empty($this->fk_etat_couchette) ? $this->fk_etat_couchette : "null") . ',';
			$sql .= ' fk_autoradio = ' . (!empty($this->fk_autoradio) ? $this->fk_autoradio : "null") . ',';
			$sql .= ' fk_telephone = ' . (!empty($this->fk_telephone) ? $this->fk_telephone : "null") . ',';
			$sql .= ' fk_tapis_sol = ' . (!empty($this->fk_tapis_sol) ? $this->fk_tapis_sol : "null") . ',';
			$sql .= ' nb_tapis_sol = ' . (!empty($this->nb_tapis_sol) ? $this->nb_tapis_sol : "null") . ',';
			$sql .= ' fk_parebrise = ' . (!empty($this->fk_parebrise) ? $this->fk_parebrise : "null") . ',';
			$sql .= ' fk_cles = ' . (!empty($this->fk_cles) ? $this->fk_cles : "null") . ',';
			$sql .= ' kilometres = ' . (!empty($this->kilometres) ? $this->kilometres : "null") . ',';
			$sql .= ' comm_baches = ' . (!empty($this->comm_baches) ? "'" . $this->db->escape($this->comm_baches) . "'" : "null") . ',';
			$sql .= ' comm_barres_AE = ' . (!empty($this->comm_barres_AE) ? "'" . $this->db->escape($this->comm_barres_AE) . "'" : "null") . ',';
			$sql .= ' comm_caisse = ' . (!empty($this->comm_caisse) ? "'" . $this->db->escape($this->comm_caisse) . "'" : "null") . ',';
			$sql .= ' comm_benne = ' . (!empty($this->comm_benne) ? "'" . $this->db->escape($this->comm_benne) . "'" : "null") . ',';
			$sql .= ' comm_portes_ar = ' . (!empty($this->comm_portes_ar) ? "'" . $this->db->escape($this->comm_portes_ar) . "'" : "null") . ',';
			$sql .= ' comm_plancher = ' . (!empty($this->comm_plancher) ? "'" . $this->db->escape($this->comm_plancher) . "'" : "null") . ',';
			$sql .= ' comm_ridelles = ' . (!empty($this->comm_ridelles) ? "'" . $this->db->escape($this->comm_ridelles) . "'" : "null") . ',';
			$sql .= ' comm_grue = ' . (!empty($this->comm_grue) ? "'" . $this->db->escape($this->comm_grue) . "'" : "null") . ',';
			$sql .= ' comm_hayon = ' . (!empty($this->comm_hayon) ? "'" . $this->db->escape($this->comm_hayon) . "'" : "null") . ',';
			$sql .= ' comm_crochet_attelage = ' . (!empty($this->comm_crochet_attelage) ? "'" . $this->db->escape($this->comm_crochet_attelage) . "'" : "null") . ',';
			$sql .= ' comm_groupe_frigo = ' . (!empty($this->comm_groupe_frigo) ? "'" . $this->db->escape($this->comm_groupe_frigo) . "'" : "null") . ',';
			$sql .= ' comm_interieur = ' . (!empty($this->comm_interieur) ? "'" . $this->db->escape($this->comm_interieur) . "'" : "null") . ',';
			$sql .= ' comm_plancher_fosse = ' . (!empty($this->comm_plancher_fosse) ? "'" . $this->db->escape($this->comm_plancher_fosse) . "'" : "null") . ',';
			$sql .= ' div_porteur_semi = ' . (!empty($this->div_porteur_semi) ? "'" . $this->db->escape($this->div_porteur_semi) . "'" : "null") . ',';
			$sql .= ' comm_div_porteur_semi = ' . (!empty($this->comm_div_porteur_semi) ? "'" . $this->db->escape($this->comm_div_porteur_semi) . "'" : "null") . ',';
			$sql .= ' autres_observations = ' . (!empty($this->autres_observations) ? "'" . $this->db->escape($this->autres_observations) . "'" : "null");

			$sql .= ' WHERE rowid=' . $this->id;

			$this->db->begin();

			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			}

			if (! $error && ! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				// // Call triggers
				// $result=$this->call_trigger('MYOBJECT_MODIFY',$user);
				// if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
				// // End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user User that deletes
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false) {
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$this->db->begin();

		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				// // Call triggers
				// $result=$this->call_trigger('MYOBJECT_DELETE',$user);
				// if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
				// // End call triggers
			}
		}

		if (! $error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 *
	 * @return int New id of clone
	 */
	public function createFromClone($fromid) {
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $user;
		$error = 0;
		$object = new Expertises($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		// Reset object
		$object->id = 0;

		// Clear fields
		// ...

		// Create clone
		$result = $object->create($user);

		// Other options
		if ($result < 0) {
			$error ++;
			$this->errors = $object->errors;
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		// End
		if (! $error) {
			$this->db->commit();

			return $object->id;
		} else {
			$this->db->rollback();

			return - 1;
		}
	}

	/**
	 * Return a link to the user card (with optionaly the picto)
	 * Use this->id,this->lastname, this->firstname
	 *
	 * @param int $withpicto Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 * @param string $option On what the link point to
	 * @param integer $notooltip 1=Disable tooltip
	 * @param int $maxlen Max length of visible user name
	 * @param string $morecss Add more css on link
	 * @return string String with URL
	 */
	function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $maxlen = 24, $morecss = '') {
		global $langs, $conf, $db;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("MyModule") . '</u>';
		$label .= '<div width="100%">';
		$label .= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

		$link = '<a href="' . DOL_URL_ROOT . '/Expertise/card.php?id=' . $this->id . '"';
		$link .= ($notooltip ? '' : ' title="' . dol_escape_htmltag($label, 1) . '" class="classfortooltip' . ($morecss ? ' ' . $morecss : '') . '"');
		$link .= '>';
		$linkend = '</a>';

		if ($withpicto) {
			$result .= ($link . img_object(($notooltip ? '' : $label), 'label', ($notooltip ? '' : 'class="classfortooltip"')) . $linkend);
			if ($withpicto != 2)
				$result .= ' ';
		}
		$result .= $link . $this->ref . $linkend;
		return $result;
	}

	/**
	 * Retourne le libelle du status d'un user (actif, inactif)
	 *
	 * @param int $mode 0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 * @return string Label of status
	 */
	function getLibStatut($mode = 0) {
		return $this->LibStatut($this->status, $mode);
	}

	/**
	 * Renvoi le libelle d'un status donne
	 *
	 * @param int $status Id status
	 * @param int $mode 0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 * @return string Label of status
	 */
	function LibStatut($status, $mode = 0) {
		global $langs;

		if ($mode == 0) {
			$prefix = '';
			if ($status == 1)
				return $langs->trans('Enabled');
			if ($status == 0)
				return $langs->trans('Disabled');
		}
		if ($mode == 1) {
			if ($status == 1)
				return $langs->trans('Enabled');
			if ($status == 0)
				return $langs->trans('Disabled');
		}
		if ($mode == 2) {
			if ($status == 1)
				return img_picto($langs->trans('Enabled'), 'statut4') . ' ' . $langs->trans('Enabled');
			if ($status == 0)
				return img_picto($langs->trans('Disabled'), 'statut5') . ' ' . $langs->trans('Disabled');
		}
		if ($mode == 3) {
			if ($status == 1)
				return img_picto($langs->trans('Enabled'), 'statut4');
			if ($status == 0)
				return img_picto($langs->trans('Disabled'), 'statut5');
		}
		if ($mode == 4) {
			if ($status == 1)
				return img_picto($langs->trans('Enabled'), 'statut4') . ' ' . $langs->trans('Enabled');
			if ($status == 0)
				return img_picto($langs->trans('Disabled'), 'statut5') . ' ' . $langs->trans('Disabled');
		}
		if ($mode == 5) {
			if ($status == 1)
				return $langs->trans('Enabled') . ' ' . img_picto($langs->trans('Enabled'), 'statut4');
			if ($status == 0)
				return $langs->trans('Disabled') . ' ' . img_picto($langs->trans('Disabled'), 'statut5');
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen() {
		$this->id = 0;

		$this->tms = '';
		$this->fk_reprise = '';
		$this->fk_rang = '';
		$this->lieu = '';
		$this->date_exp = '';
		$this->fk_client_contact = '';
		$this->fk_expert = '';
		$this->fk_testeur = '';
		$this->date_essai = '';
		$this->moteur = '';
		$this->embrayage = '';
		$this->bv = '';
		$this->transmission = '';
		$this->pont = '';
		$this->freins = '';
		$this->fk_defG = '';
		$this->fk_defD = '';
		$this->fk_defT = '';
		$this->fk_visiere = '';
		$this->fk_retroG = '';
		$this->fk_retroD = '';
		$this->fk_retroA = '';
		$this->fk_bloc_opt_av_D = '';
		$this->fk_bloc_opt_av_G = '';
		$this->fk_enjo_phare_av_D = '';
		$this->fk_enjo_phare_av_G = '';
		$this->fk_parechoc_av_D = '';
		$this->fk_parechoc_av_G = '';
		$this->fk_calandre = '';
		$this->fk_clignotant_av_D = '';
		$this->fk_clignotant_av_G = '';
		$this->fk_longe_porte_D = '';
		$this->fk_longe_porte_G = '';
		$this->fk_porte_coffre_D = '';
		$this->fk_porte_coffre_G = '';
		$this->fk_porte_cabine_D = '';
		$this->fk_porte_cabine_G = '';
		$this->div_ext_cabine = '';
		$this->comm_div_ext_cabine = '';
		$this->fk_roue_secours = '';
		$this->fk_flexibles = '';
		$this->fk_grille_alu = '';
		$this->fk_batteries = '';
		$this->fk_jupe_G = '';
		$this->fk_jupe_D = '';
		$this->fk_reservoir_GO_G = '';
		$this->fk_reservoir_GO_D = '';
		$this->fk_reservoir_adblue = '';
		$this->fk_reservoir_hydro = '';
		$this->fk_aile_ar_G = '';
		$this->fk_aile_ar_D = '';
		$this->fk_feux_ar_G = '';
		$this->fk_feux_ar_D = '';
		$this->fk_support_feux_ar_G = '';
		$this->fk_support_feux_ar_D = '';
		$this->usure_pneu_av = '';
		$this->usure_pneu_ar1 = '';
		$this->usure_pneu_ar2 = '';
		$this->usure_pneu_ar3 = '';
		$this->div_chassis = '';
		$this->comm_div_chassis = '';
		$this->fk_kit_outils_cric = '';
		$this->fk_chappe = '';
		$this->fk_etat_interieur = '';
		$this->fk_etat_TDB = '';
		$this->fk_etat_couchette = '';
		$this->fk_autoradio = '';
		$this->fk_telephone = '';
		$this->fk_tapis_sol = '';
		$this->nb_tapis_sol = '';
		$this->fk_parebrise = '';
		$this->fk_cles = '';
		$this->kilometres = '';
		$this->comm_baches = '';
		$this->comm_barres_AE = '';
		$this->comm_caisse = '';
		$this->comm_benne = '';
		$this->comm_portes_ar = '';
		$this->comm_plancher = '';
		$this->comm_ridelles = '';
		$this->comm_grue = '';
		$this->comm_hayon = '';
		$this->comm_crochet_attelage = '';
		$this->comm_groupe_frigo = '';
		$this->comm_interieur = '';
		$this->comm_plancher_fosse = '';
		$this->div_porteur_semi = '';
		$this->comm_div_porteur_semi = '';
		$this->autres_observations = '';
	}
}

/**
 * Class ExpertisesLine
 */
class ExpertisesLine
{
	/**
	 *
	 * @var int ID
	 */
	public $id;
	/**
	 *
	 * @var mixed Sample line property 1
	 */
	public $tms = '';
	public $fk_reprise;
	public $fk_rang;
	public $lieu;
	public $date_exp = '';
	public $fk_client_contact;
	public $fk_expert;
	public $fk_testeur;
	public $date_essai = '';
	public $moteur;
	public $embrayage;
	public $bv;
	public $transmission;
	public $pont;
	public $freins;
	public $fk_defG;
	public $fk_defD;
	public $fk_defT;
	public $fk_visiere;
	public $fk_retroG;
	public $fk_retroD;
	public $fk_retroA;
	public $fk_bloc_opt_av_D;
	public $fk_bloc_opt_av_G;
	public $fk_enjo_phare_av_D;
	public $fk_enjo_phare_av_G;
	public $fk_parechoc_av_D;
	public $fk_parechoc_av_G;
	public $fk_calandre;
	public $fk_clignotant_av_D;
	public $fk_clignotant_av_G;
	public $fk_longe_porte_D;
	public $fk_longe_porte_G;
	public $fk_porte_coffre_D;
	public $fk_porte_coffre_G;
	public $fk_porte_cabine_D;
	public $fk_porte_cabine_G;
	public $div_ext_cabine;
	public $comm_div_ext_cabine;
	public $fk_roue_secours;
	public $fk_flexibles;
	public $fk_grille_alu;
	public $fk_batteries;
	public $fk_jupe_G;
	public $fk_jupe_D;
	public $fk_reservoir_GO_G;
	public $fk_reservoir_GO_D;
	public $fk_reservoir_adblue;
	public $fk_reservoir_hydro;
	public $fk_aile_ar_G;
	public $fk_aile_ar_D;
	public $fk_feux_ar_G;
	public $fk_feux_ar_D;
	public $fk_support_feux_ar_G;
	public $fk_support_feux_ar_D;
	public $usure_pneu_av;
	public $usure_pneu_ar1;
	public $usure_pneu_ar2;
	public $usure_pneu_ar3;
	public $div_chassis;
	public $comm_div_chassis;
	public $fk_kit_outils_cric;
	public $fk_chappe;
	public $fk_etat_interieur;
	public $fk_etat_TDB;
	public $fk_etat_couchette;
	public $fk_autoradio;
	public $fk_telephone;
	public $fk_tapis_sol;
	public $nb_tapis_sol;
	public $fk_parebrise;
	public $fk_cles;
	public $kilometres;
	public $comm_baches;
	public $comm_barres_AE;
	public $comm_caisse;
	public $comm_benne;
	public $comm_portes_ar;
	public $comm_plancher;
	public $comm_ridelles;
	public $comm_grue;
	public $comm_hayon;
	public $comm_crochet_attelage;
	public $comm_groupe_frigo;
	public $comm_interieur;
	public $comm_plancher_fosse;
	public $div_porteur_semi;
	public $comm_div_porteur_semi;
	public $autres_observations;

/**
 *
 * @var mixed Sample line property 2
 */
}
