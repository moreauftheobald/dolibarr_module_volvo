<?php
/*
 * Copyright (C) 2014 Florian HENRY <florian.henry@open-concept.pro>
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
 * \defgroup	lead	Lead module
 * \brief		Lead module descriptor.
 * \file		core/modules/modLead.class.php
 * \ingroup	lead
 * \brief		Description and activation file for module Lead
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";

/**
 * Description and activation class for module Lead
 */
class modvolvo extends DolibarrModules
{

	/**
	 * Constructor.
	 * Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use a free id here
		// (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 010175;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'volvo';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed)
		// used if translation string 'ModuleXXXName' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description
		// used if translation string 'ModuleXXXDesc' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->description = "Module Spécifique Théobald Trucks";
		// Possible values for version are: 'development', 'experimental' or version
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled
		// (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		// Where to store the module in setup page
		// (0=common,1=interface,2=others,3=very specific)
		$this->special = 3;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png
		// use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png
		// use this->picto='pictovalue@module'
		$this->picto = 'iron02@volvo'; // mypicto@lead
		                            // Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		                            // for default path (eg: /lead/core/xxxxx) (0=disable, 1=enable)
		                            // for specific path of parts (eg: /lead/core/modules/barcode)
		                            // for specific css file (eg: /lead/css/lead.css.php)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory
			// 'triggers' => 1,
			// Set this to 1 if module has its own login method directory
			// 'login' => 0,
			// Set this to 1 if module has its own substitution function file
			// 'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory
			// 'menus' => 0,
			// Set this to 1 if module has its own barcode directory
			// 'barcode' => 0,
			// Set this to 1 if module has its own models directory
			'models' => 1,
			'tpl' => 1,
		// Set this to relative path of css if module has its own css file
		// 'css' => '/lead/css/mycss.css.php',
			'js'=>'/volvo/js/jquery.flot.orderBars.js',
		// Set here all hooks context managed by module
			'hooks' => array('ordercard','ordersuppliercard','thirdpartycard'),
		// Set here all workflow context managed by module
		// 'workflow' => array('order' => array('WORKFLOW_ORDER_AUTOCREATE_INVOICE'))
				);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/lead/temp");
		$this->dirs = array(
			'/volvo',
			'/volvo/import',
			'/volvo/import/immat',
			'/volvo/modelpdf'
		);

		// Config pages. Put here list of php pages
		// stored into lead/admin directory, used to setup module.
		$this->config_page_url = array(
			"admin_volvo.php@volvo"
		);

		// Dependencies
		// List of modules id that must be enabled if this module is enabled
		$this->depends = array();
		// List of modules id to disable if this one is disabled
		$this->requiredby = array();
		// Minimum version of PHP required by module
		$this->phpmin = array(
			5,
			4
		);
		// Minimum version of Dolibarr required by module
		$this->need_dolibarr_version = array(
			3,
			9
		);
		//$this->langfiles = array(
		//	"lead@lead"
		//); // langfiles@lead
		                                       // Constants
		                                       // List of particular constants to add when module is enabled
		                                       // (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		                                       // Example:
		$this->const = array(
			0 => array(
				'MAIN_CAN_HIDE_EXTRAFIELDS',
				'chaine',
				'1',
				'can hiden extrafiled',
				0,
				'current',
				1
			),
			1 => array(
				'COMMANDE_ADDON_PDF',
				'chaine',
				'analysevolvo',
				'',
				1,
				'current',
				1
			),
			2 => array(
				'COMMANDE_ADDON_PDF_2',
				'chaine',
				'analysevolvolg',
				'',
				1,
				'current',
				1
			),
			3 => array(
				'VOLVO_VCM_LIST',
				'chaine',
				'GOLD,GOLDS,SILVER,SILVER+,BLUE',
				'Liste des articles Contrat de maintenance',
				0,
				'current',
				1
			),
			4 => array(
				'VOLVO_PACK_LIST',
				'chaine',
				'PPC,PCC,PVC',
				'Liste des articles Pack Véhicules',
				0,
				'current',
				1
			),
			5 => array(
				'VOLVO_LOCK_DELAI',
				'chaine',
				'6',
				'',
				0,
				'current',
				1
			),
			6 => array(
				'VOLVO_TRUCK',
				'chaine',
				'1',
				'',
				0,
				'current',
				1
			),

			7 => array(
				'VOLVO_SURES',
				'chaine',
				'16',
				'',
				0,
				'current',
				1
			),
			8 => array(
				'VOLVO_COM',
				'chaine',
				'13',
				'',
				0,
				'current',
				1
			),
			9 => array(
				'VOLVO_FORFAIT_LIV',
				'chaine',
				'10',
				'',
				0,
				'current',
				1
			),
			10 => array(
				'VOLVO_OBLIGATOIRE',
				'chaine',
				'5',
				'',
				0,
				'current',
				1
			),
			11 => array(
				'VOLVO_INTERNE',
				'chaine',
				'2',
				'',
				0,
				'current',
				1
			),
			12 => array(
				'VOLVO_EXTERNE',
				'chaine',
				'3',
				'',
				0,
				'current',
				1
			),
			13 => array(
				'VOLVO_DIVERS',
				'chaine',
				'4',
				'',
				0,
				'current',
				1
			),
			14 => array(
				'VOLVO_SOLTRS',
				'chaine',
				'13',
				'',
				0,
				'current',
				1
			),
			15 => array(
				'VOLVO_ANALYSE_X',
				'chaine',
				'8,29.5,55,77,100,129,154,178',
				'',
				0,
				'current',
				1
			),
			16 => array(
				'VOLVO_ANALYSE_Z',
				'chaine',
				'20.5,24.5,21,22,28,24,23,25.5',
				'',
				0,
				'current',
				1
			),
			17 => array(
				'VOLVO_ANALYSE_Y_ENTETE',
				'chaine',
				'17.5,23.5,29.5,35.5,42,48,54,60.5,72.5',
				'',
				0,
				'current',
				1
			),
			18 => array(
				'VOLVO_ANALYSE_Y_INTERNE_NB',
				'chaine',
				'10',
				'',
				0,
				'current',
				1
			),
			19 => array(
				'VOLVO_ANALYSE_Y_INTERNE_OFFSET',
				'chaine',
				'84.5',
				'',
				0,
				'current',
				1
			),
			20 => array(
				'VOLVO_ANALYSE_Y_INTERNE_PAS',
				'chaine',
				'4.8',
				'',
				0,
				'current',
				1
			),
			21 => array(
				'VOLVO_ANALYSE_Y_EXTERNE_NB',
				'chaine',
				'6',
				'',
				0,
				'current',
				1
			),
			22 => array(
				'VOLVO_ANALYSE_Y_EXTERNE_OFFSET',
				'chaine',
				'139.5',
				'',
				0,
				'current',
				1
			),
			23 => array(
				'VOLVO_ANALYSE_Y_EXTERNE_PAS',
				'chaine',
				'4.8',
				'',
				0,
				'current',
				1
			),
			24 => array(
				'VOLVO_ANALYSE_Y_DIVERS_NB',
				'chaine',
				'5',
				'',
				0,
				'current',
				1
			),
			25 => array(
				'VOLVO_ANALYSE_Y_DIVERS_OFFSET',
				'chaine',
				'185.5',
				'',
				0,
				'current',
				1
			),
			26 => array(
				'VOLVO_ANALYSE_Y_DIVERS_PAS',
				'chaine',
				'4.8',
				'',
				0,
				'current',
				1
			),
			27 => array(
				'VOLVO_ANALYSE_Y_VO_NB',
				'chaine',
				'2',
				'',
				0,
				'current',
				1
			),
			28 => array(
				'VOLVO_ANALYSE_Y_VO_OFFSET',
				'chaine',
				'167.5',
				'',
				0,
				'current',
				1
			),
			29 => array(
				'VOLVO_ANALYSE_Y_VO_PAS',
				'chaine',
				'5',
				'',
				0,
				'current',
				1
			),
			30 => array(
				'VOLVO_ANALYSE_Y_PIED',
				'chaine',
				'210.5,217,223,227.5,232.2,236.5,241,245.5,254.5',
				'',
				0,
				'current',
				1
			),
			31 => array(
				'VOLVO_ANALYSELG_X',
				'chaine',
				'6,28.5,53,74.5,98,127,154,178.5',
				'',
				0,
				'current',
				1
			),
			32 => array(
				'VOLVO_ANALYSELG_Z',
				'chaine',
				'21.5,23.5,20.5,22.5,28,26,23.5,25.5',
				'',
				0,
				'current',
				1
			),
			33 => array(
				'VOLVO_ANALYSELG_Y_ENTETE',
				'chaine',
				'157,21.7,27.7,33.7,40.7,55.7,61.7,67.7,73.7,86.7',
				'',
				0,
				'current',
				1
			),
			34 => array(
				'VOLVO_ANALYSELG_Y_INTERNE_NB',
				'chaine',
				'31',
				'',
				0,
				'current',
				1
			),
			35 => array(
				'VOLVO_ANALYSELG_Y_INTERNE_OFFSET',
				'chaine',
				'101.5',
				'',
				0,
				'current',
				1
			),
			36 => array(
				'VOLVO_ANALYSELG_Y_INTERNE_PAS',
				'chaine',
				'6.05',
				'',
				0,
				'current',
				1
			),
			37 => array(
				'VOLVO_ANALYSELG_Y_EXTERNE_NB',
				'chaine',
				'10',
				'',
				0,
				'current',
				1
			),
			38 => array(
				'VOLVO_ANALYSELG_Y_EXTERNE_OFFSET',
				'chaine',
				'12.3',
				'',
				0,
				'current',
				1
			),
			39 => array(
				'VOLVO_ANALYSELG_Y_EXTERNE_PAS',
				'chaine',
				'6.05',
				'',
				0,
				'current',
				1
			),
			40 => array(
				'VOLVO_ANALYSELG_Y_DIVERS_NB',
				'chaine',
				'16',
				'',
				0,
				'current',
				1
			),
			41 => array(
				'VOLVO_ANALYSELG_Y_DIVERS_OFFSET',
				'chaine',
				'80.5',
				'',
				0,
				'current',
				1
			),
			42 => array(
				'VOLVO_ANALYSELG_Y_DIVERS_PAS',
				'chaine',
				'6.05',
				'',
				0,
				'current',
				1
			),
			43 => array(
				'VOLVO_ANALYSELG_Y_VO_NB',
				'chaine',
				'2',
				'',
				0,
				'current',
				1
			),
			44 => array(
				'VOLVO_ANALYSELG_Y_VO_OFFSET',
				'chaine',
				'186',
				'',
				0,
				'current',
				1
			),
			45 => array(
				'VOLVO_ANALYSELG_Y_VO_PAS',
				'chaine',
				'6.05',
				'',
				0,
				'current',
				1
			),
			46 => array(
				'VOLVO_VCM_OBLIG',
				'chaine',
				'1',
				'',
				0,
				'current',
				1
			),
			47 => array(
				'VOLVO_ANALYSELG_Y_PIED',
				'chaine',
				'198.5,205.5,212.5,219,225,231,237,243,254.5',
				'',
				0,
				'current',
				1
			),
		);

		// Array to add new pages in new tabs
		// Example:
		$this->tabs = array(
			//'thirdparty:+tabLead:Module103111Name:lead@lead:$user->rights->lead->read && ($object->client > 0 || $soc->client > 0):/lead/lead/list.php?socid=__ID__',
			//'invoice:+tabAgefodd:AgfMenuSess:agefodd@agefodd:/lead/lead/list.php?search_invoiceid=__ID__',
			//'propal:+tabAgefodd:AgfMenuSess:agefodd@agefodd:/lead/lead/list.php?search_propalid=__ID__',
		// // To add a new tab identified by code tabname1
		'lead:+reprise:Reprise::$user->rights->volvo->lireeprise:/volvo/reprise/card.php?&action=&repid=&id=__ID__',
		'order:-contact',
		//'order:+vcm:VCM::$user->rights->commande->lire:/volvo/vcm/vcm.php?&id=__ID__',
		// // To add another new tab identified by code tabname2
		// 'objecttype:+tabname2:Title2:langfile@lead:$user->rights->othermodule->read:/lead/mynewtab2.php?id=__ID__',
		// // To remove an existing tab identified by code tabname
		// 'objecttype:-tabname'
			'thirdparty:-customer',
			'thirdparty:-price'
			);
		// where objecttype can be
		// 'thirdparty' to add a tab in third party view
		// 'intervention' to add a tab in intervention view
		// 'order_supplier' to add a tab in supplier order view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'invoice' to add a tab in customer invoice view
		// 'order' to add a tab in customer order view
		// 'product' to add a tab in product view
		// 'stock' to add a tab in stock view
		// 'propal' to add a tab in propal view
		// 'member' to add a tab in fundation member view
		// 'contract' to add a tab in contract view
		// 'user' to add a tab in user view
		// 'group' to add a tab in group view
		// 'contact' to add a tab in contact view
		// 'categories_x' to add a tab in category view
		// (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// Dictionnaries
		if (! isset($conf->volvo->enabled)) {
			$conf->volvo = (object) array();
			$conf->volvo->enabled = 0;
		}

		$this->dictionnaries = array(
			'langs' => 'volvo@volvo',
			'tabname' => array(
				MAIN_DB_PREFIX . "c_volvo_bv",
				MAIN_DB_PREFIX . "c_volvo_cabine",
				MAIN_DB_PREFIX . "c_volvo_carrosserie",
				MAIN_DB_PREFIX . "c_volvo_freinage",
				MAIN_DB_PREFIX . "c_volvo_gamme",
				MAIN_DB_PREFIX . "c_volvo_genre",
				MAIN_DB_PREFIX . "c_volvo_marque_pneu",
				MAIN_DB_PREFIX . "c_volvo_marques",
				MAIN_DB_PREFIX . "c_volvo_moteur",
				MAIN_DB_PREFIX . "c_volvo_motif_perte_lead",
				MAIN_DB_PREFIX . "c_volvo_normes",
				MAIN_DB_PREFIX . "c_volvo_ralentisseur",
				MAIN_DB_PREFIX . "c_volvo_silouhette",
				MAIN_DB_PREFIX . "c_volvo_sites",
				MAIN_DB_PREFIX . "c_volvo_solutions_transport",
				MAIN_DB_PREFIX . "c_volvo_suspension_cabine"
			),
			'tablib' => array(
				"Volvo -- boites de vitesse",
				"Volvo -- Types de cabines",
				"Volvo -- Carrosseries",
				"Volvo -- Types de Freinages",
				"Volvo -- Gammes de véhicules",
				"Volvo -- Genres de véhicules",
				"Volvo -- Marque de pneumatiques",
				"Volvo -- Marques de véhicules",
				"Volvo -- Motorisations",
				"Volvo -- Motifs de perte d'affaires",
				"Volvo -- Normes Euro Motorisations",
				"Volvo -- Types de ralentisseurs",
				"Volvo -- Géométries d'essieux",
				"Volvo -- Sites",
				"Volvo -- Solutions de transports",
				"Volvo -- Types de suspensions de cabines"
			),
			'tabsql' => array(
				'SELECT f.rowid as rowid, f.bv as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_bv as f',
				'SELECT f.rowid as rowid, f.cabine as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_cabine as f',
				'SELECT f.rowid as rowid, f.carrosserie as nom, f.labelexcel, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_carrosserie as f',
				'SELECT f.rowid as rowid, f.freinage as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_freinage as f',
				'SELECT f.rowid as rowid, f.gamme as nom, f.cv as canal, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_gamme as f',
				'SELECT f.rowid as rowid, f.genre as nom, f.rep as reprise, f.cv as canal, f.del_rg as delais, f.labelexcel, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_genre as f',
				'SELECT f.rowid as rowid, f.marquepneu as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_marque_pneu as f',
				'SELECT f.rowid as rowid, f.marque as nom, f.labelexcel, f.active  FROM ' . MAIN_DB_PREFIX . 'c_volvo_marques as f',
				'SELECT f.rowid as rowid, f.moteur as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_moteur as f',
				'SELECT f.rowid as rowid, f.motif as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_motif_perte_lead as f',
				'SELECT f.rowid as rowid, f.norme as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_normes as f',
				'SELECT f.rowid as rowid, f.ralentisseur as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_ralentisseur as f',
				'SELECT f.rowid as rowid, f.silouhette as nom, f.cv as canal, f.rep as reprise, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_silouhette as f',
				'SELECT f.rowid as rowid, f.codesite as code, f.nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_sites as f',
				'SELECT f.rowid as rowid, f.nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_solutions_transport as f',
				'SELECT f.rowid as rowid, f.suspcabine as nom, f.active FROM ' . MAIN_DB_PREFIX . 'c_volvo_suspension_cabine as f'
			),
			'tabsqlsort' => array(
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC',
				'rowid ASC'
			),
			'tabfield' => array(
				"nom",
				"nom,labelexcel",
				"nom",
				"nom",
				"nom,canal",
				"nom,canal,reprise,delais,labelexcel",
				"nom",
				"nom,labelexcel",
				"nom",
				"nom",
				"nom",
				"nom",
				"nom,canal,reprise",
				"code,nom",
				"nom",
				"nom"
			),
			'tabfieldvalue' => array(
				"nom",
				"nom,labelexcel",
				"nom",
				"nom",
				"nom,canal",
				"nom,canal,reprise,delais,labelexcel",
				"nom",
				"nom,labelexcel",
				"nom",
				"nom",
				"nom",
				"nom",
				"nom,canal,reprise",
				"code,nom",
				"nom",
				"nom"
			),
			'tabfieldinsert' => array(
				"bv",
				"cabine",
				"carrosserie,labelexcel",
				"freinage",
				"gamme,cv",
				"genre,cv,rep,del_rg,labelexcel",
				"marquepneu",
				"marque,labelexcel",
				"moteur",
				"motif",
				"norme",
				"ralentisseur",
				"silouhette,cv,rep",
				"codesite,nom",
				"nom",
				"suspcabine"
			),
			'tabrowid' => array(
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid",
				"rowid"
			),
			'tabcond' => array(
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled',
				'$conf->volvo->enabled'
			)
		);

		// Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
		$this->boxes = array(); // Boxes list
		$r = 0;
		// Example:


		$this->boxes[$r][1] = "box_pdmsoltrs_indiv@volvo";
		$r ++;

		$this->boxes[$r][1] = "box_pdmsoltrs_global@volvo";
		$r ++;

		$this->boxes[$r][1] = "box_delaicash_indiv@volvo";
		$r ++;

		$this->boxes[$r][1] = "box_delaicash_global@volvo";
		$r ++;
		/*
		 * $this->boxes[$r][1] = "myboxb.php"; $r++;
		 */

		// Permissions
		$this->rights = array(); // Permission array used by this module
		$r = 0;
		$this->rights[$r][0] = 0101751;
		$this->rights[$r][1] = 'Administration des V.O.';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'admin';
		$r ++;

		$this->rights[$r][0] = 0101752;
		$this->rights[$r][1] = 'Voir les reprises';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'lireeprise';
		$r ++;

		$this->rights[$r][0] = 0101753;
		$this->rights[$r][1] = 'Saisie et Modification des Informations Générales V.O.';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'modif_ig';
		$r ++;

		$this->rights[$r][0] = 0101754;
		$this->rights[$r][1] = 'Saisie et Modification des Expertises V.O.';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'modif_exp';
		$r ++;

		$this->rights[$r][0] = 0101755;
		$this->rights[$r][1] = 'Saisie et Modification des Réceptions V.O.';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'modif_rec';
		$r ++;

		$this->rights[$r][0] = 0101756;
		$this->rights[$r][1] = 'Facturer les V.O.';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'facture';
		$r ++;

		$this->rights[$r][0] = 0101757;
		$this->rights[$r][1] = 'Modifier les prix de reviens';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'update_cost';
		$r ++;

		$this->rights[$r][0] = 0101758;
		$this->rights[$r][1] = 'Consultation stat Vente exterieur';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'stat_ext';
		$r ++;

		$this->rights[$r][0] = 0101764;
		$this->rights[$r][1] = 'Consultation stat tout vendeurs';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'stat_all';
		$r ++;

		$this->rights[$r][0] = 0101760;
		$this->rights[$r][1] = 'Consulter ses primes';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'prime_read';
		$r ++;

		$this->rights[$r][0] = 0101761;
		$this->rights[$r][1] = 'Consulter primes vendeurs';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'prime_read_all';
		$r ++;

		$this->rights[$r][0] = 0101762;
		$this->rights[$r][1] = 'préparer primes vendeurs';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'prime_admin';
		$r ++;

		$this->rights[$r][0] = 0101763;
		$this->rights[$r][1] = 'Valider primes vendeurs';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'prime_valid';
		$r ++;

		$this->rights[$r][0] = 0101765;
		$this->rights[$r][1] = 'Accéder aux etats comptable';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'compta';
		$r ++;

		$this->rights[$r][0] = 0101766;
		$this->rights[$r][1] = 'consulter suivi Business';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'business';
		$r ++;

		$this->rights[$r][0] = 0101767;
		$this->rights[$r][1] = 'Consulter Suivi délai cash';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'delai_cash';
		$r ++;

		$this->rights[$r][0] = 0101770;
		$this->rights[$r][1] = 'Consulter Suvi d\'activité';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'activite';
		$r ++;

		$this->rights[$r][0] = 0101771;
		$this->rights[$r][1] = 'Consulter Affaires chaudes';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'chaudes';
		$r ++;

		$this->rights[$r][0] = 0101772;
		$this->rights[$r][1] = 'Consulter liste des contrats';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'contrat';
		$r ++;

		$this->rights[$r][0] = 0101773;
		$this->rights[$r][1] = 'Consulter tableau de bord solutions transports';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'soltrs';
		$r ++;

		$this->rights[$r][0] = 0101774;
		$this->rights[$r][1] = 'Import des données OM';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'om';
		$r ++;

		$this->rights[$r][0] = 0101775;
		$this->rights[$r][1] = 'Import du fichier Immatriculation';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'immat';
		$r ++;

		$this->rights[$r][0] = 0101776;
		$this->rights[$r][1] = 'Consulter le portefeuille de commande';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'port';
		$r ++;


		// $r++;
		// Main menu entries
		$this->menus = array(); // List of menus to add
		$r = 0;

		$this->menu[$r] = array(
			'fk_menu' => 0,
			'type' => 'top',
			'titre' => 'Theobald',
			'mainmenu' => 'volvo',
			'url' => '/volvo/index.php',
			'langs' => '',
			'position' => 100,
			'enabled' => '1',
			'perms' => '1',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=volvo',
			'type' => 'left',
			'titre' => "Véhicules d'occasion",
			'mainmenu' => 'volvo',
			'leftmenu' => 'VO',
			'url' => '/volvo/reprise/vo.php',
			'langs' => '',
			'position' => 100+$r,
			'enabled' => '$user->rights->volvo->lireeprise',
			'perms' => '$user->rights->volvo->lireeprise',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=VO',
				'type' => 'left',
				'titre' => 'Liste des VO',
				'mainmenu' => 'volvo',
				'leftmenu' => 'reprise',
				'url' => '/volvo/reprise/list.php',
				'langs' => '',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->lireeprise',
				'perms' => '$user->rights->volvo->lireeprise',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=VO',
				'type' => 'left',
				'titre' => 'Activité VO',
				'mainmenu' => 'volvo',
				'leftmenu' => 'activitevo',
				'url' => '/mydoliboard/mydoliboard.php?idboard=4',
				'langs' => '',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->lireeprise',
				'perms' => '$user->rights->volvo->lireeprise',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo',
				'type' => 'left',
				'titre' => 'Imports',
				'mainmenu' => 'volvo',
				'leftmenu' => 'imports',
				'url' => '/volvo/import/index.php',
				'langs' => '',
				'position' => 100+$r,
				'enabled' => '1',
				'perms' => '1',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=imports',
				'type' => 'left',
				'titre' => 'Import Immat',
				'mainmenu' => 'volvo',
				'leftmenu' => 'immat',
				'url' => '/volvo/import/import_immat.php?step=1',
				'langs' => '',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->immat',
				'perms' => '$user->rights->volvo->immat',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=imports',
				'type' => 'left',
				'titre' => 'Import OM',
				'mainmenu' => 'volvo',
				'leftmenu' => 'om',
				'url' => '/volvo/import/import_om.php?step=1',
				'langs' => '',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->om',
				'perms' => '$user->rights->volvo->om',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo',
				'type' => 'left',
				'titre' => 'etats',
				'mainmenu' => 'volvo',
				'leftmenu' => 'etats',
				'url' => '/volvo/business/list.php?search_run=1',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '1',
				'perms' => '$user->rights->volvo->business',
				'target' => '',
				'user' => 0
		);
		$r ++;


		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=etats',
			'type' => 'left',
			'titre' => 'Suivis Business',
			'mainmenu' => 'volvo',
			'leftmenu' => 'business',
			'url' => '/volvo/business/list.php?search_run=1',
			'langs' => 'lead@lead',
			'position' => 100+$r,
			'enabled' => '$user->rights->volvo->business',
			'perms' => '$user->rights->volvo->business',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=etats',
				'type' => 'left',
				'titre' => 'Suivi Délai Cash',
				'mainmenu' => 'volvo',
				'leftmenu' => 'cash',
				'url' => '/volvo/business/delaicash.php?search_run=1',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->delai_cash',
				'perms' => '$user->rights->volvo->delai_cash',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=etats',
				'type' => 'left',
				'titre' => 'Suivi d\'activité',
				'mainmenu' => 'volvo',
				'leftmenu' => 'resume',
				'url' => '/volvo/business/resume.php',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->activite',
				'perms' => '$user->rights->volvo->activite',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=etats',
				'type' => 'left',
				'titre' => 'Affaires chaudes',
				'mainmenu' => 'volvo',
				'leftmenu' => 'chaudes',
				'url' => '/mydoliboard/mydoliboard.php?idboard=5',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->chaudes',
				'perms' => '$user->rights->volvo->chaudes',
				'target' => '',
				'user' => 0
		);
		$r ++;
		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=etats',
				'type' => 'left',
				'titre' => 'Portefeuille cmd',
				'mainmenu' => 'volvo',
				'leftmenu' => 'portefeuille',
				'url' => '/volvo/business/portefeuille.php',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->port',
				'perms' => '$user->rights->volvo->port',
				'target' => '',
				'user' => 0
		);
		$r ++;
		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=etats',
				'type' => 'left',
				'titre' => 'Liste des contrats',
				'mainmenu' => 'volvo',
				'leftmenu' => 'contrat',
				'url' => '/volvo/business/listcontrat.php',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->contrat',
				'perms' => '$user->rights->volvo->contrat',
				'target' => '',
				'user' => 0
		);
		$r ++;
		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=etats',
				'type' => 'left',
				'titre' => 'Tableau de bord Sol. Trs.',
				'mainmenu' => 'volvo',
				'leftmenu' => 'tdbsoltrs',
				'url' => '/mydoliboard/mydoliboard.php?idboard=6',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->soltrs',
				'perms' => '$user->rights->volvo->soltrs',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo',
				'type' => 'left',
				'titre' => 'Etats comptables',
				'mainmenu' => 'volvo',
				'leftmenu' => 'compta',
				'url' => '/volvo/index.php',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->compta',
				'perms' => '$user->rights->volvo->compta',
				'target' => '',
				'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=volvo,fk_leftmenu=compta',
				'type' => 'left',
				'titre' => 'Etat des provision VN Volvo',
				'mainmenu' => 'volvo',
				'leftmenu' => 'provnvolvo',
				'url' => '/volvo/compta/etat_provisions_vn.php',
				'langs' => 'lead@lead',
				'position' => 100+$r,
				'enabled' => '$user->rights->volvo->compta',
				'perms' => '$user->rights->volvo->compta',
				'target' => '',
				'user' => 0
		);
		$r ++;



		//$this->menu[$r] = array(
		//	'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
		//	'type' => 'left',
		//	'titre' => 'LeadList',
		//	'url' => '/lead/lead/list.php',
		//	'langs' => 'lead@lead',
		//	'position' => 100+$r,
		//	'enabled' => '$user->rights->lead->read',
		//	'perms' => '$user->rights->lead->read',
		//	'target' => '',
		//	'user' => 0
		//);
		//$r ++;

		//$this->menu[$r] = array(
		//	'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
		//	'type' => 'left',
		//	'titre' => 'LeadListCurrent',
		//	'url' => '/lead/lead/list.php?viewtype=current',
		//	'langs' => 'lead@lead',
		//	'position' => 100+$r,
		//	'enabled' => '$user->rights->lead->read',
		//	'perms' => '$user->rights->lead->read',
		//	'target' => '',
		//	'user' => 0
		//);
		//$r ++;

		//$this->menu[$r] = array(
		//	'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
		//	'type' => 'left',
		//	'titre' => 'LeadListMyLead',
		//	'url' => '/lead/lead/list.php?viewtype=my',
		//	'langs' => 'lead@lead',
		//	'position' => 100+$r,
		//	'enabled' => '$user->rights->lead->read',
		//	'perms' => '$user->rights->lead->read',
		//	'target' => '',
		//	'user' => 0
		//);
		//$r ++;

		//$this->menu[$r] = array(
		//	'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
		//	'type' => 'left',
		//	'titre' => 'LeadListLate',
		//	'url' => '/lead/lead/list.php?viewtype=late',
		//	'langs' => 'lead@lead',
		//	'position' => 100+$r,
		//	'enabled' => '$user->rights->lead->read',
		//	'perms' => '$user->rights->lead->read',
		//	'target' => '',
		//	'user' => 0
		//);
		//$r ++;

		// Exports
		//$r = 0;
		//$r ++;
		//$this->export_code [$r] = $this->rights_class . '_' . $r;
		//$this->export_label [$r] = 'ExportDataset_lead';
		//$this->export_icon [$r] = 'lead@lead';
		//$this->export_permission [$r] = array (
				//array (
						//"lead",
						//"export"
				//)
		//);
		//$this->export_fields_array [$r] = array (
			//	'l.rowid' => 'Id',
			//	'l.ref' => 'Ref',
			//	'l.ref_ext' => 'LeadRefExt',
			//	'l.ref_int' => 'LeadRefInt',
			//	'so.nom' => 'Company',
			//	'dictstep.code' => 'LeadStepCode',
			//	'dictstep.label' => 'LeadStepLabel',
			//	'dicttype.code' => 'LeadTypeCode',
			//	'dicttype.label' => 'LeadTypeLabel',
			//	'l.date_closure' => 'LeadDeadLine',
			//	'l.amount_prosp' => 'LeadAmountGuess',
			//	'l.description' => 'LeadDescription',
		//);
		//$this->export_TypeFields_array [$r] = array (
			//	'l.rowid' => 'Text',
			//	'l.ref' => 'Text',
			//	'l.ref_ext' => 'Text',
			//	'l.ref_int' => 'Text',
			//	'so.nom' => 'Text',
			//	'dictstep.code' => 'Text',
			//	'dictstep.label' => 'Text',
			//	'dicttype.code' => 'Text',
			//	'dicttype.label' => 'Text',
			//	'l.date_closure' => 'Date',
			//	'l.amount_prosp' => 'Numeric',
			//	'l.description' => 'Text',
		//);
		//$this->export_entities_array [$r] = array (
				//'l.rowid' => 'lead@lead',
				//'l.ref' => 'lead@lead',
				//'l.ref_ext' => 'lead@lead',
				//'l.ref_int' => 'lead@lead',
				//'so.nom' => 'company',
				//'dictstep.code' => 'lead@lead',
				//'dictstep.label' => 'lead@lead',
				//'dicttype.code' => 'lead@lead',
				//'dicttype.label' => 'lead@lead',
				//'l.date_closure' => 'lead@lead',
				//'l.amount_prosp' => 'lead@lead',
				//'l.description' => 'lead@lead',
		//);

		//$this->export_sql_start [$r] = 'SELECT DISTINCT ';
		//$this->export_sql_end [$r] = ' FROM ' . MAIN_DB_PREFIX . 'lead as l';
		//$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "societe as so ON so.rowid=l.fk_soc";
		//$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "user as usr ON usr.rowid=l.fk_user_resp";
		//$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "c_lead_status as dictstep ON dictstep.rowid=l.fk_c_status";
		//$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "c_lead_type as dicttype ON dicttype.rowid=l.fk_c_type";
		//$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "lead_extrafields as extra ON extra.fk_object=l.rowid";
		//$this->export_sql_end [$r] .= ' WHERE l.entity IN (' . getEntity("lead", 1) . ')';

		// Add extra fields
		//$sql="SELECT name, label, type, param FROM ".MAIN_DB_PREFIX."extrafields WHERE elementtype = 'lead'";
		//$resql=$this->db->query($sql);
		//if ($resql)    // This can fail when class is used on old database (during migration for example)
		//{
			//while ($obj=$this->db->fetch_object($resql))
			//{
				//$fieldname='extra.'.$obj->name;
				//$fieldlabel=ucfirst($obj->label);
				//$typeFilter="Text";
				//switch($obj->type)
				//{
					//case 'int':
					//case 'double':
					//case 'price':
						//$typeFilter="Numeric";
						//break;
					//case 'date':
					//case 'datetime':
						//$typeFilter="Date";
						//break;
					//case 'boolean':
						//$typeFilter="Boolean";
						//break;
					//case 'sellist':
						//$typeFilter="List:".$obj->param;
						//break;
				//}
				//$this->export_fields_array[$r][$fieldname]=$fieldlabel;
				//$this->export_TypeFields_array[$r][$fieldname]=$typeFilter;
				//$this->export_entities_array[$r][$fieldname]='lead';
			//}
		//}

		// Example:
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// // Translation key (used only if key ExportDataset_xxx_z not found)
		// $this->export_label[$r]='CustomersInvoicesAndInvoiceLines';
		// // Condition to show export in list (ie: '$user->id==3').
		// // Set to 1 to always show when module is enabled.
		// $this->export_enabled[$r]='1';
		// $this->export_permission[$r]=array(array("facture","facture","export"));
		// $this->export_fields_array[$r]=array(
		// 's.rowid'=>"IdCompany",
		// 's.nom'=>'CompanyName',
		// 's.address'=>'Address',
		// 's.cp'=>'Zip',
		// 's.ville'=>'Town',
		// 's.fk_pays'=>'Country',
		// 's.tel'=>'Phone',
		// 's.siren'=>'ProfId1',
		// 's.siret'=>'ProfId2',
		// 's.ape'=>'ProfId3',
		// 's.idprof4'=>'ProfId4',
		// 's.code_compta'=>'CustomerAccountancyCode',
		// 's.code_compta_fournisseur'=>'SupplierAccountancyCode',
		// 'f.rowid'=>"InvoiceId",
		// 'f.facnumber'=>"InvoiceRef",
		// 'f.datec'=>"InvoiceDateCreation",
		// 'f.datef'=>"DateInvoice",
		// 'f.total'=>"TotalHT",
		// 'f.total_ttc'=>"TotalTTC",
		// 'f.tva'=>"TotalVAT",
		// 'f.paye'=>"InvoicePaid",
		// 'f.fk_statut'=>'InvoiceStatus',
		// 'f.note'=>"InvoiceNote",
		// 'fd.rowid'=>'LineId',
		// 'fd.description'=>"LineDescription",
		// 'fd.price'=>"LineUnitPrice",
		// 'fd.tva_tx'=>"LineVATRate",
		// 'fd.qty'=>"LineQty",
		// 'fd.total_ht'=>"LineTotalHT",
		// 'fd.total_tva'=>"LineTotalTVA",
		// 'fd.total_ttc'=>"LineTotalTTC",
		// 'fd.date_start'=>"DateStart",
		// 'fd.date_end'=>"DateEnd",
		// 'fd.fk_product'=>'ProductId',
		// 'p.ref'=>'ProductRef'
		// );
		// $this->export_entities_array[$r]=array('s.rowid'=>"company",
		// 's.nom'=>'company',
		// 's.address'=>'company',
		// 's.cp'=>'company',
		// 's.ville'=>'company',
		// 's.fk_pays'=>'company',
		// 's.tel'=>'company',
		// 's.siren'=>'company',
		// 's.siret'=>'company',
		// 's.ape'=>'company',
		// 's.idprof4'=>'company',
		// 's.code_compta'=>'company',
		// 's.code_compta_fournisseur'=>'company',
		// 'f.rowid'=>"invoice",
		// 'f.facnumber'=>"invoice",
		// 'f.datec'=>"invoice",
		// 'f.datef'=>"invoice",
		// 'f.total'=>"invoice",
		// 'f.total_ttc'=>"invoice",
		// 'f.tva'=>"invoice",
		// 'f.paye'=>"invoice",
		// 'f.fk_statut'=>'invoice',
		// 'f.note'=>"invoice",
		// 'fd.rowid'=>'invoice_line',
		// 'fd.description'=>"invoice_line",
		// 'fd.price'=>"invoice_line",
		// 'fd.total_ht'=>"invoice_line",
		// 'fd.total_tva'=>"invoice_line",
		// 'fd.total_ttc'=>"invoice_line",
		// 'fd.tva_tx'=>"invoice_line",
		// 'fd.qty'=>"invoice_line",
		// 'fd.date_start'=>"invoice_line",
		// 'fd.date_end'=>"invoice_line",
		// 'fd.fk_product'=>'product',
		// 'p.ref'=>'product'
		// );
		// $this->export_sql_start[$r] = 'SELECT DISTINCT ';
		// $this->export_sql_end[$r] = ' FROM (' . MAIN_DB_PREFIX . 'facture as f, '
		// . MAIN_DB_PREFIX . 'facturedet as fd, ' . MAIN_DB_PREFIX . 'societe as s)';
		// $this->export_sql_end[$r] .= ' LEFT JOIN ' . MAIN_DB_PREFIX
		// . 'product as p on (fd.fk_product = p.rowid)';
		// $this->export_sql_end[$r] .= ' WHERE f.fk_soc = s.rowid '
		// . 'AND f.rowid = fd.fk_facture';
		// $r++;
	}

	/**
	 * Function called when module is enabled.
	 * The init function add constants, boxes, permissions and menus
	 * (defined in constructor) into Dolibarr database.
	 * It also creates data directories
	 *
	 * @param string $options Enabling module ('', 'noboxes')
	 * @return int if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf;

		$sql = array();

		dol_include_once('/core/class/extrafields.class.php');
		$extrafields=new ExtraFields($this->db);
		$res = $extrafields->addExtraField('buyingprice_real', 'Prix d achat réél', 'price', 0, '', 'commandedet',0, 0,'', array('options'=>''),0);
		$res = $extrafields->addExtraField('dt_invoice', 'Date facture', 'date', 0, '', 'commandedet',0, 0,'', array('options'=>''),0);
		$res = $extrafields->addExtraField('fk_supplier', 'Fournisseur', 'sellist', 0, '', 'commandedet',0, 0,'', array('options'=>array('societe:nom:rowid::fournisseur=1'=>null)),0);
		$res = $extrafields->addExtraField('notupdatecost', 'Cout non modifiable (MAJ prix commande)', 'boolean', 0, '', 'product',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('notupdatecostreal', 'Pas de maj du prix de reviens automatique (MAJ prix commande)', 'boolean', 0, '', 'product',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('supplierorderable', 'Disponible pour la commande fournisseur', 'boolean', 0, '', 'product',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('dt_invoice', 'Date de facturation', 'date', 0, '', 'commande',0, 0,'', array('options'=>''),0);
		$res = $extrafields->addExtraField('vnac', 'VNC', 'price', 0, '', 'commande',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('dt_blockupdate', 'Date blocage modification', 'date', 0, '', 'commande_fournisseur',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('nbchassis', 'Nombre de châssis annoncés', 'int', 0, '', 'lead',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('new', 'Nouveau client ?', 'boolean', 0, '', 'lead',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('chaude', 'Affaire chaude ?', 'boolean', 0, '', 'lead',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('gamme', 'Gamme', 'sellist', 0, '', 'lead',0, 0,'', array('options'=>array('c_volvo_gamme:gamme:rowid:leadtype|cv:active=1'=>null)),1);
		$res = $extrafields->addExtraField('specif', 'N° de spécification', 'varcahr', 255, '', 'lead',0, 0,'', array('options'=>''),0);
		$res = $extrafields->addExtraField('motif', 'Motif de perte', 'chkbxlst', 0, '', 'lead',0, 0,'', array('options'=>array('c_volvo_motif_perte_lead:motif:rowid::active=1'=>null)),1);
		$res = $extrafields->addExtraField('soltrs', 'Solutions Transport', 'chkbxlst', 0, '', 'lead',0, 0,'', array('options'=>array('c_volvo_solutions_transport:nom:rowid::active=1'=>null)),1);
		$res = $extrafields->addExtraField('marque', 'Marque Traitée', 'sellist', 0, '', 'lead',0, 0,'', array('options'=>array('c_volvo_marques:marque:rowid::active=1'=>null)),1);
		$res = $extrafields->addExtraField('silouhette', 'Silouhette', 'sellist', 0, '', 'lead',0, 0,'', array('options'=>array('c_volvo_silouhette:silouhette:rowid:leadtype|cv:active=1'=>null)),1);
		$res = $extrafields->addExtraField('type', 'Type', 'sellist', 0, '', 'lead',0, 0,'', array('options'=>array('c_volvo_genre:genre:rowid:leadtype|cv:active=1'=>null)),1);
		$res = $extrafields->addExtraField('carroserie', 'Carroserie', 'sellist', 0, '', 'lead',0, 0,'', array('options'=>array('c_volvo_carrosserie:carrosserie:rowid::active=1'=>null)),1);
		$res = $extrafields->addExtraField('canton', 'Canton', 'varchar', 255, '', 'societe',0, 0,'', array('options'=>''),0);
		$res = $extrafields->addExtraField('debranch', 'Client Débranché', 'boolean', 0, '', 'societe',0, 0,'', array('options'=>''),1);
		$res = $extrafields->addExtraField('codecm', 'Code Contact CM', 'varchar', 255, '', 'socpeople',0, 0,'', array('options'=>''),0);
		$res = $extrafields->addExtraField('affaire', 'Affaire', 'sellist', 0, '', 'actioncomm',0, 0,'', array('options'=>'lead:ref|ref_int:rowid:socid|fk_soc:fk_c_status IN (4,8,9,12)'),1);
		$res = $extrafields->addExtraField('vin', 'VIN', 'varchar', 18, '', 'commande_fournisseur',0, 0,'', array('options'=>''));
		$res = $extrafields->addExtraField('immat', 'Immat', 'varchar', 10, '', 'commande_fournisseur',0, 0,'', array('options'=>''));
		$res = $extrafields->addExtraField('vin', 'VIN', 'varchar', 18, '', 'commande',0, 0,'', array('options'=>''));
		$res = $extrafields->addExtraField('immat', 'Immat', 'varchar', 10, '', 'commande',0, 0,'', array('options'=>''));

		$res = dolibarr_del_const($this->db,'MAIN_AGENDA_ACTIONAUTO_ORDER_VALIDATE',$conf->entity);
		$res = dolibarr_del_const($this->db,'MAIN_AGENDA_ACTIONAUTO_ORDER_CLASSIFY_BILLED',$conf->entity);

		$result = $this->loadTables();


		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		$result=dol_copy(dol_buildpath('/volvo/core/doctemplate/ANALYSE CDE VOLVO.pdf'),DOL_DATA_ROOT.'/volvo/modelpdf/ficheanalyse.pdf',0,0);
		$result=dol_copy(dol_buildpath('/volvo/core/doctemplate/ANALYSE CDE VOLVO LG.pdf'),DOL_DATA_ROOT.'/volvo/modelpdf/ficheanalyselg.pdf',0,0);
		$result=dol_copy(dol_buildpath('/volvo/core/doctemplate/VCM.pdf'),DOL_DATA_ROOT.'/volvo/modelpdf/vcm.pdf',0,0);

		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from Dolibarr database.
	 * Data directories are not deleted
	 *
	 * @param string $options Enabling module ('', 'noboxes')
	 * @return int if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

	/**
	 * Create tables, keys and data required by module
	 * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * and create data commands must be stored in directory /lead/sql/
	 * This function is called by this->init
	 *
	 * @return int if KO, >0 if OK
	 */
	private function loadTables()
	{
		return $this->_load_tables('/volvo/sql/');
	}
}
