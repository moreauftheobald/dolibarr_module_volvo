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
 * \file Reprise/reprise.class.php
 * \ingroup Reprise
 * \brief This file is an example for a CRUD class file (Create/Read/Update/Delete)
 * Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
// require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
// require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Reprise
 *
 * Put here description of your class
 *
 * @see CommonObject
 */
class Reprise extends CommonObject
{
	/**
	 *
	 * @var string Id to identify managed objects
	 */
	public $element = 'reprise';
	/**
	 *
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'reprise';

	/**
	 *
	 * @var RepriseLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	public $tms = '';
	public $fk_lead;
	public $ref;
	public $police;
	public $fk_soc;
	public $date_entree = '';
	public $fk_restit;
	public $fk_marque;
	public $fk_genre;
	public $type;
	public $fk_silouhette;
	public $place;
	public $fk_norme;
	public $numserie;
	public $carrosserie;
	public $puisfisc;
	public $puiscom;
	public $ptc;
	public $pv;
	public $ptr;
	public $longutil;
	public $largutil;
	public $chargutil;
	public $immat;
	public $circ = '';
	public $kmact;
	public $kmrestit;
	public $validmine = '';
	public $validtachy = '';
	public $valid1;
	public $date1 = '';
	public $valid2;
	public $date2 = '';
	public $agrement;
	public $dateagrement = '';
	public $fk_cabine;
	public $fk_suspcabine;
	public $fk_moteur;
	public $fk_ralentisseur;
	public $fk_bv;
	public $rav;
	public $rar;
	public $sr;
	public $dr;
	public $blocage;
	public $rapport;
	public $fk_freinage;
	public $abs;
	public $asr;
	public $ebs;
	public $esp;
	public $dfr;
	public $suspav;
	public $suspar;
	public $fk_mav;
	public $fk_mar1;
	public $fk_mar2;
	public $fk_mar3;
	public $tav;
	public $tar1;
	public $tar2;
	public $tar3;
	public $dav;
	public $dar1;
	public $dar2;
	public $dar3;
	public $pav;
	public $par1;
	public $par2;
	public $par3;
	public $uav;
	public $uar1;
	public $uar2;
	public $uar3;
	public $nifissure;
	public $nisoude;
	public $etatmeca;
	public $pres;
	public $couchette;
	public $nbreserv;
	public $capago;
	public $adblue;
	public $lve;
	public $rct;
	public $gyro;
	public $echapv;
	public $adr;
	public $hydro;
	public $climtoit;
	public $webasto;
	public $clim;
	public $compresseur;
	public $deflecteur;
	public $jupes;
	public $copiecg;
	public $copiect;
	public $copieca;
	public $photos;
	public $estim;
	public $rachat;
	public $actif;
	public $status;
	var $dict_array = array(
			array(
					'bv',
					'bv',
					'bv'
			),
			array(
					'norme',
					'normes',
					'norme'
			),
			array(
					'cabine',
					'cabine',
					'cabine'
			),
			array(
					'moteur',
					'moteur',
					'moteur'
			),
			array(
					'freinage',
					'freinage',
					'freinage'
			),
			array(
					'ralentisseur',
					'ralentisseur',
					'ralentisseur'
			),
			array(
					'genre',
					'genre',
					'genre'
			),
			array(
					'silouhette',
					'silouhette',
					'silouhette'
			),
			array(
					'suspcabine',
					'suspension_cabine',
					'suspcabine'
			),
			array(
					'marquepneu',
					'marque_pneu',
					'marquepneu'
			),
			array(
					'nom',
					'sites',
					'sites'
			),
			array(
					'nom',
					'sites',
					'sites'
			),
			array(
					'carrosserie',
					'carrosserie',
					'carrosserie_dict'
			),
			array(
					'gamme',
					'gamme',
					'gamme'
			),
			array(
					'marque',
					'marques',
					'marque'
			)
	);
	var $value_array = array(
			'fk_lead',
			'ref',
			'police',
			'fk_soc',
			'date_entree',
			'fk_restit',
			'fk_marque',
			'fk_genre',
			'type',
			'fk_silouhette',
			'place',
			'fk_norme',
			'numserie',
			'carrosserie',
			'puisfisc',
			'puiscom',
			'ptc',
			'pv',
			'ptr',
			'longutil',
			'largutil',
			'chargutil',
			'immat',
			'circ',
			'kmact',
			'kmrestit',
			'validmine',
			'validtachy',
			'valid1',
			'valid2',
			'date1',
			'date2',
			'agrement',
			'dateagrement',
			'fk_cabine',
			'fk_suspcabine',
			'fk_moteur',
			'fk_ralentisseur',
			'fk_bv',
			'rav',
			'rar',
			'sr',
			'dr',
			'blocage',
			'rapport',
			'fk_freinage',
			'abs1',
			'asr',
			'ebs',
			'esp',
			'dfr',
			'suspav',
			'suspar',
			'fk_mav',
			'fk_mar1',
			'fk_mar2',
			'fk_mar3',
			'tav',
			'tar1',
			'tar2',
			'tar3',
			'dav',
			'dar1',
			'dar2',
			'dar3',
			'pav',
			'par1',
			'par2',
			'par3',
			'uav',
			'uar1',
			'uar2',
			'uar3',
			'nifissure',
			'nisoude',
			'etatmeca',
			'pres',
			'couchette',
			'nbreserv',
			'capago',
			'adblue',
			'lve',
			'rct',
			'gyro',
			'echapv',
			'adr',
			'hydro',
			'climtoit',
			'webasto',
			'clim',
			'compresseur',
			'deflecteur',
			'jupes',
			'copiecg',
			'copieca',
			'copiect',
			'photo',
			'estim',
			'rachat',
			'actif',
			'status'
	);

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db) {
		$this->db = $db;

		foreach ( $this->dict_array as $dict ) {
			$this->load_dict($dict);
		}
	}

	/**
	 *
	 * @param unknown $dict
	 * @return number
	 */
	private function load_dict($dict) {
		$sql = "SELECT rowid, " . $dict[0] . " AS val FROM " . MAIN_DB_PREFIX . "c_volvo_" . $dict[1] . " WHERE active=1";
		$resql = $this->db->query($sql);
		$res = array();
		if ($resql) {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$res[$obj->rowid] = $obj->val;
			}
			$this->db->free($resql);
			$this->{$dict[2]} = $res;
			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::_load_" . $dict[2] . " " . $this->error, LOG_ERR);
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

		if (isset($this->fk_lead)) {
			$this->fk_lead = trim($this->fk_lead);
		}
		if (isset($this->ref)) {
			$this->ref = trim($this->ref);
		}
		if (isset($this->police)) {
			$this->police = trim($this->police);
		}
		if (isset($this->fk_soc)) {
			$this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_restit)) {
			$this->fk_restit = trim($this->fk_restit);
		}
		if (isset($this->fk_marque)) {
			$this->fk_marque = trim($this->fk_marque);
		}
		if (isset($this->fk_genre)) {
			$this->fk_genre = trim($this->fk_genre);
		}
		if (isset($this->type)) {
			$this->type = trim($this->type);
		}
		if (isset($this->fk_silouhette)) {
			$this->fk_silouhette = trim($this->fk_silouhette);
		}
		if (isset($this->place)) {
			$this->place = trim($this->place);
		}
		if (isset($this->fk_norme)) {
			$this->fk_norme = trim($this->fk_norme);
		}
		if (isset($this->numserie)) {
			$this->numserie = trim($this->numserie);
		}
		if (isset($this->carrosserie)) {
			$this->carrosserie = trim($this->carrosserie);
		}
		if (isset($this->puisfisc)) {
			$this->puisfisc = trim($this->puisfisc);
		}
		if (isset($this->puiscom)) {
			$this->puiscom = trim($this->puiscom);
		}
		if (isset($this->ptc)) {
			$this->ptc = trim($this->ptc);
		}
		if (isset($this->pv)) {
			$this->pv = trim($this->pv);
		}
		if (isset($this->ptr)) {
			$this->ptr = trim($this->ptr);
		}
		if (isset($this->longutil)) {
			$this->longutil = trim($this->longutil);
		}
		if (isset($this->largutil)) {
			$this->largutil = trim($this->largutil);
		}
		if (isset($this->chargutil)) {
			$this->chargutil = trim($this->chargutil);
		}
		if (isset($this->immat)) {
			$this->immat = trim($this->immat);
		}
		if (isset($this->kmact)) {
			$this->kmact = trim($this->kmact);
		}
		if (isset($this->kmrestit)) {
			$this->kmrestit = trim($this->kmrestit);
		}
		if (isset($this->valid1)) {
			$this->valid1 = trim($this->valid1);
		}
		if (isset($this->valid2)) {
			$this->valid2 = trim($this->valid2);
		}
		if (isset($this->agrement)) {
			$this->agrement = trim($this->agrement);
		}
		if (isset($this->fk_cabine)) {
			$this->fk_cabine = trim($this->fk_cabine);
		}
		if (isset($this->fk_suspcabine)) {
			$this->fk_suspcabine = trim($this->fk_suspcabine);
		}
		if (isset($this->fk_moteur)) {
			$this->fk_moteur = trim($this->fk_moteur);
		}
		if (isset($this->fk_ralentisseur)) {
			$this->fk_ralentisseur = trim($this->fk_ralentisseur);
		}
		if (isset($this->fk_bv)) {
			$this->fk_bv = trim($this->fk_bv);
		}
		if (isset($this->rav)) {
			$this->rav = trim($this->rav);
		}
		if (isset($this->rar)) {
			$this->rar = trim($this->rar);
		}
		if (isset($this->sr)) {
			$this->sr = trim($this->sr);
		}
		if (isset($this->dr)) {
			$this->dr = trim($this->dr);
		}
		if (isset($this->blocage)) {
			$this->blocage = trim($this->blocage);
		}
		if (isset($this->rapport)) {
			$this->rapport = trim($this->rapport);
		}
		if (isset($this->fk_freinage)) {
			$this->fk_freinage = trim($this->fk_freinage);
		}
		if (isset($this->abs)) {
			$this->abs = trim($this->abs);
		}
		if (isset($this->asr)) {
			$this->asr = trim($this->asr);
		}
		if (isset($this->ebs)) {
			$this->ebs = trim($this->ebs);
		}
		if (isset($this->esp)) {
			$this->esp = trim($this->esp);
		}
		if (isset($this->dfr)) {
			$this->dfr = trim($this->dfr);
		}
		if (isset($this->suspav)) {
			$this->suspav = trim($this->suspav);
		}
		if (isset($this->suspar)) {
			$this->suspar = trim($this->suspar);
		}
		if (isset($this->fk_mav)) {
			$this->fk_mav = trim($this->fk_mav);
		}
		if (isset($this->fk_mar1)) {
			$this->fk_mar1 = trim($this->fk_mar1);
		}
		if (isset($this->fk_mar2)) {
			$this->fk_mar2 = trim($this->fk_mar2);
		}
		if (isset($this->fk_mar3)) {
			$this->fk_mar3 = trim($this->fk_mar3);
		}
		if (isset($this->tav)) {
			$this->tav = trim($this->tav);
		}
		if (isset($this->tar1)) {
			$this->tar1 = trim($this->tar1);
		}
		if (isset($this->tar2)) {
			$this->tar2 = trim($this->tar2);
		}
		if (isset($this->tar3)) {
			$this->tar3 = trim($this->tar3);
		}
		if (isset($this->dav)) {
			$this->dav = trim($this->dav);
		}
		if (isset($this->dar1)) {
			$this->dar1 = trim($this->dar1);
		}
		if (isset($this->dar2)) {
			$this->dar2 = trim($this->dar2);
		}
		if (isset($this->dar3)) {
			$this->dar3 = trim($this->dar3);
		}
		if (isset($this->pav)) {
			$this->pav = trim($this->pav);
		}
		if (isset($this->par1)) {
			$this->par1 = trim($this->par1);
		}
		if (isset($this->par2)) {
			$this->par2 = trim($this->par2);
		}
		if (isset($this->par3)) {
			$this->par3 = trim($this->par3);
		}
		if (isset($this->uav)) {
			$this->uav = trim($this->uav);
		}
		if (isset($this->uar1)) {
			$this->uar1 = trim($this->uar1);
		}
		if (isset($this->uar2)) {
			$this->uar2 = trim($this->uar2);
		}
		if (isset($this->uar3)) {
			$this->uar3 = trim($this->uar3);
		}
		if (isset($this->nifissure)) {
			$this->nifissure = trim($this->nifissure);
		}
		if (isset($this->nisoude)) {
			$this->nisoude = trim($this->nisoude);
		}
		if (isset($this->etatmeca)) {
			$this->etatmeca = trim($this->etatmeca);
		}
		if (isset($this->pres)) {
			$this->pres = trim($this->pres);
		}
		if (isset($this->couchette)) {
			$this->couchette = trim($this->couchette);
		}
		if (isset($this->nbreserv)) {
			$this->nbreserv = trim($this->nbreserv);
		}
		if (isset($this->capago)) {
			$this->capago = trim($this->capago);
		}
		if (isset($this->adblue)) {
			$this->adblue = trim($this->adblue);
		}
		if (isset($this->lve)) {
			$this->lve = trim($this->lve);
		}
		if (isset($this->rct)) {
			$this->rct = trim($this->rct);
		}
		if (isset($this->gyro)) {
			$this->gyro = trim($this->gyro);
		}
		if (isset($this->echapv)) {
			$this->echapv = trim($this->echapv);
		}
		if (isset($this->adr)) {
			$this->adr = trim($this->adr);
		}
		if (isset($this->hydro)) {
			$this->hydro = trim($this->hydro);
		}
		if (isset($this->climtoit)) {
			$this->climtoit = trim($this->climtoit);
		}
		if (isset($this->webasto)) {
			$this->webasto = trim($this->webasto);
		}
		if (isset($this->clim)) {
			$this->clim = trim($this->clim);
		}
		if (isset($this->compresseur)) {
			$this->compresseur = trim($this->compresseur);
		}
		if (isset($this->deflecteur)) {
			$this->deflecteur = trim($this->deflecteur);
		}
		if (isset($this->jupes)) {
			$this->jupes = trim($this->jupes);
		}
		if (isset($this->copiecg)) {
			$this->copiecg = trim($this->copiecg);
		}
		if (isset($this->copiect)) {
			$this->copiect = trim($this->copiect);
		}
		if (isset($this->copieca)) {
			$this->copieca = trim($this->copieca);
		}
		if (isset($this->photos)) {
			$this->photos = trim($this->photos);
		}
		if (isset($this->estim)) {
			$this->estim = trim($this->estim);
		}
		if (isset($this->rachat)) {
			$this->rachat = trim($this->rachat);
		}

		if (empty($this->fk_soc)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'client');
		}
		if (empty($this->fk_lead)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'affaire');
		}

		// Check parameters
		// Put here code to add control on parameters values
		if (empty($error)) {
			// Insert request
			$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';

			$sql .= 'fk_lead,';
			$sql .= 'ref,';
			$sql .= 'police,';
			$sql .= 'fk_soc,';
			$sql .= 'dateentree,';
			$sql .= 'fk_siterestit,';
			$sql .= 'fk_marque,';
			$sql .= 'fk_genre,';
			$sql .= 'type,';
			$sql .= 'fk_silouhette,';
			$sql .= 'place,';
			$sql .= 'fk_norme,';
			$sql .= 'numserie,';
			$sql .= 'carrosserie,';
			$sql .= 'puisfisc,';
			$sql .= 'puiscom,';
			$sql .= 'ptc,';
			$sql .= 'pv,';
			$sql .= 'ptr,';
			$sql .= 'longutil,';
			$sql .= 'largutil,';
			$sql .= 'chargutil,';
			$sql .= 'immat,';
			$sql .= '1circ,';
			$sql .= 'kmact,';
			$sql .= 'kmrestit,';
			$sql .= 'validmine,';
			$sql .= 'validtachy,';
			$sql .= 'valid1,';
			$sql .= 'date1,';
			$sql .= 'valid2,';
			$sql .= 'date2,';
			$sql .= 'agrement,';
			$sql .= 'dateagrement,';
			$sql .= 'fk_cabine,';
			$sql .= 'fk_suspcabine,';
			$sql .= 'fk_moteur,';
			$sql .= 'fk_ralentisseur,';
			$sql .= 'fk_bv,';
			$sql .= 'rav,';
			$sql .= 'rar,';
			$sql .= 'sr,';
			$sql .= 'dr,';
			$sql .= 'blocage,';
			$sql .= 'rapport,';
			$sql .= 'fk_freinage,';
			$sql .= 'abs,';
			$sql .= 'asr,';
			$sql .= 'ebs,';
			$sql .= 'esp,';
			$sql .= 'dfr,';
			$sql .= 'suspav,';
			$sql .= 'suspar,';
			$sql .= 'fk_mav,';
			$sql .= 'fk_mar1,';
			$sql .= 'fk_mar2,';
			$sql .= 'fk_mar3,';
			$sql .= 'tav,';
			$sql .= 'tar1,';
			$sql .= 'tar2,';
			$sql .= 'tar3,';
			$sql .= 'dav,';
			$sql .= 'dar1,';
			$sql .= 'dar2,';
			$sql .= 'dar3,';
			$sql .= 'pav,';
			$sql .= 'par1,';
			$sql .= 'par2,';
			$sql .= 'par3,';
			$sql .= 'uav,';
			$sql .= 'uar1,';
			$sql .= 'uar2,';
			$sql .= 'uar3,';
			$sql .= 'nifissure,';
			$sql .= 'nisoude,';
			$sql .= 'etatmeca,';
			$sql .= 'pres,';
			$sql .= 'couchette,';
			$sql .= 'nbreserv,';
			$sql .= 'capago,';
			$sql .= 'adblue,';
			$sql .= '2lve,';
			$sql .= '2rct,';
			$sql .= 'gyro,';
			$sql .= 'echapv,';
			$sql .= 'adr,';
			$sql .= 'hydro,';
			$sql .= 'climtoit,';
			$sql .= 'webasto,';
			$sql .= 'clim,';
			$sql .= 'compresseur,';
			$sql .= 'deflecteur,';
			$sql .= 'jupes,';
			$sql .= 'copiecg,';
			$sql .= 'copiect,';
			$sql .= 'copieca,';
			$sql .= 'photos,';
			$sql .= 'estim,';
			$sql .= 'rachat,';
			$sql .= 'actif,';
			$sql .= 'fk_status';

			$sql .= ') VALUES (';

			$sql .= ' ' . (empty($this->fk_lead) ? 'NULL' : $this->fk_lead) . ',';
			$sql .= ' ' . (empty($this->ref) ? 'NULL' : "'" . $this->db->escape($this->ref) . "'") . ',';
			$sql .= ' ' . (empty($this->police) ? 'NULL' : "'" . $this->db->escape($this->police) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_soc) ? 'NULL' : $this->fk_soc) . ',';
			$sql .= ' ' . (empty($this->date_entree) || dol_strlen($this->date_entree) == 0 ? 'NULL' : "'" . $this->db->idate($this->date_entree) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_restit) ? 'NULL' : $this->fk_restit) . ',';
			$sql .= ' ' . (empty($this->fk_marque) ? 'NULL' : $this->fk_marque) . ',';
			$sql .= ' ' . (empty($this->fk_genre) ? 'NULL' : $this->fk_genre) . ',';
			$sql .= ' ' . (empty($this->type) ? 'NULL' : "'" . $this->db->escape($this->type) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_silouhette) ? 'NULL' : $this->fk_silouhette) . ',';
			$sql .= ' ' . (empty($this->place) ? 'NULL' : $this->place) . ',';
			$sql .= ' ' . (empty($this->fk_norme) ? 'NULL' : $this->fk_norme) . ',';
			$sql .= ' ' . (empty($this->numserie) ? 'NULL' : "'" . $this->db->escape($this->numserie) . "'") . ',';
			$sql .= ' ' . (empty($this->carrosserie) ? 'NULL' : "'" . $this->db->escape($this->carrosserie) . "'") . ',';
			$sql .= ' ' . (empty($this->puisfisc) ? 'NULL' : $this->puisfisc) . ',';
			$sql .= ' ' . (empty($this->puiscom) ? 'NULL' : $this->puiscom) . ',';
			$sql .= ' ' . (empty($this->ptc) ? 'NULL' : "'" . $this->ptc . "'") . ',';
			$sql .= ' ' . (empty($this->pv) ? 'NULL' : "'" . $this->pv . "'") . ',';
			$sql .= ' ' . (empty($this->ptr) ? 'NULL' : "'" . $this->ptr . "'") . ',';
			$sql .= ' ' . (empty($this->longutil) ? 'NULL' : "'" . $this->longutil . "'") . ',';
			$sql .= ' ' . (empty($this->largutil) ? 'NULL' : "'" . $this->largutil . "'") . ',';
			$sql .= ' ' . (empty($this->chargutil) ? 'NULL' : "'" . $this->chargutil . "'") . ',';
			$sql .= ' ' . (empty($this->immat) ? 'NULL' : "'" . $this->db->escape($this->immat) . "'") . ',';
			$sql .= ' ' . (empty($this->circ) || dol_strlen($this->circ) == 0 ? 'NULL' : "'" . $this->db->idate($this->circ) . "'") . ',';
			$sql .= ' ' . (empty($this->kmact) ? 'NULL' : $this->kmact) . ',';
			$sql .= ' ' . (empty($this->kmrestit) ? 'NULL' : $this->kmrestit) . ',';
			$sql .= ' ' . (empty($this->validmine) || dol_strlen($this->validmine) == 0 ? 'NULL' : "'" . $this->db->idate($this->validmine) . "'") . ',';
			$sql .= ' ' . (empty($this->validtachy) || dol_strlen($this->validtachy) == 0 ? 'NULL' : "'" . $this->db->idate($this->validtachy) . "'") . ',';
			$sql .= ' ' . (empty($this->valid1) ? 'NULL' : "'" . $this->db->escape($this->valid1) . "'") . ',';
			$sql .= ' ' . (empty($this->date1) || dol_strlen($this->date1) == 0 ? 'NULL' : "'" . $this->db->idate($this->date1) . "'") . ',';
			$sql .= ' ' . (empty($this->valid2) ? 'NULL' : "'" . $this->db->escape($this->valid2) . "'") . ',';
			$sql .= ' ' . (empty($this->date2) || dol_strlen($this->date2) == 0 ? 'NULL' : "'" . $this->db->idate($this->date2) . "'") . ',';
			$sql .= ' ' . (empty($this->agrement) ? 'NULL' : "'" . $this->db->escape($this->agrement) . "'") . ',';
			$sql .= ' ' . (empty($this->dateagrement) || dol_strlen($this->dateagrement) == 0 ? 'NULL' : "'" . $this->db->idate($this->dateagrement) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_cabine) ? 'NULL' : $this->fk_cabine) . ',';
			$sql .= ' ' . (empty($this->fk_suspcabine) ? 'NULL' : $this->fk_suspcabine) . ',';
			$sql .= ' ' . (empty($this->fk_moteur) ? 'NULL' : $this->fk_moteur) . ',';
			$sql .= ' ' . (empty($this->fk_ralentisseur) ? 'NULL' : $this->fk_ralentisseur) . ',';
			$sql .= ' ' . (empty($this->fk_bv) ? 'NULL' : $this->fk_bv) . ',';
			$sql .= ' ' . (empty($this->rav) ? 'NULL' : $this->rav) . ',';
			$sql .= ' ' . (empty($this->rar) ? 'NULL' : $this->rar) . ',';
			$sql .= ' ' . (empty($this->sr) ? 'NULL' : $this->sr) . ',';
			$sql .= ' ' . (empty($this->dr) ? 'NULL' : $this->dr) . ',';
			$sql .= ' ' . (empty($this->blocage) ? 'NULL' : $this->blocage) . ',';
			$sql .= ' ' . (empty($this->rapport) ? 'NULL' : "'" . $this->rapport . "'") . ',';
			$sql .= ' ' . (empty($this->fk_freinage) ? 'NULL' : $this->fk_freinage) . ',';
			$sql .= ' ' . (empty($this->abs) ? 'NULL' : $this->abs) . ',';
			$sql .= ' ' . (empty($this->asr) ? 'NULL' : $this->asr) . ',';
			$sql .= ' ' . (empty($this->ebs) ? 'NULL' : $this->ebs) . ',';
			$sql .= ' ' . (empty($this->esp) ? 'NULL' : $this->esp) . ',';
			$sql .= ' ' . (empty($this->dfr) ? 'NULL' : $this->dfr) . ',';
			$sql .= ' ' . (empty($this->suspav) ? 'NULL' : "'" . $this->db->escape($this->suspav) . "'") . ',';
			$sql .= ' ' . (empty($this->suspar) ? 'NULL' : "'" . $this->db->escape($this->suspar) . "'") . ',';
			$sql .= ' ' . (empty($this->fk_mav) ? 'NULL' : $this->fk_mav) . ',';
			$sql .= ' ' . (empty($this->fk_mar1) ? 'NULL' : $this->fk_mar1) . ',';
			$sql .= ' ' . (empty($this->fk_mar2) ? 'NULL' : $this->fk_mar2) . ',';
			$sql .= ' ' . (empty($this->fk_mar3) ? 'NULL' : $this->fk_mar3) . ',';
			$sql .= ' ' . (empty($this->tav) ? 'NULL' : "'" . $this->db->escape($this->tav) . "'") . ',';
			$sql .= ' ' . (empty($this->tar1) ? 'NULL' : "'" . $this->db->escape($this->tar1) . "'") . ',';
			$sql .= ' ' . (empty($this->tar2) ? 'NULL' : "'" . $this->db->escape($this->tar2) . "'") . ',';
			$sql .= ' ' . (empty($this->tar3) ? 'NULL' : "'" . $this->db->escape($this->tar3) . "'") . ',';
			$sql .= ' ' . (empty($this->dav) ? 'NULL' : "'" . $this->dav . "'") . ',';
			$sql .= ' ' . (empty($this->dar1) ? 'NULL' : "'" . $this->dar1 . "'") . ',';
			$sql .= ' ' . (empty($this->dar2) ? 'NULL' : "'" . $this->dar2 . "'") . ',';
			$sql .= ' ' . (empty($this->dar3) ? 'NULL' : "'" . $this->dar3 . "'") . ',';
			$sql .= ' ' . (empty($this->pav) ? 'NULL' : "'" . $this->db->escape($this->pav) . "'") . ',';
			$sql .= ' ' . (empty($this->par1) ? 'NULL' : "'" . $this->db->escape($this->par1) . "'") . ',';
			$sql .= ' ' . (empty($this->par2) ? 'NULL' : "'" . $this->db->escape($this->par2) . "'") . ',';
			$sql .= ' ' . (empty($this->par3) ? 'NULL' : "'" . $this->db->escape($this->par3) . "'") . ',';
			$sql .= ' ' . (empty($this->uav) ? 'NULL' : $this->uav) . ',';
			$sql .= ' ' . (empty($this->uar1) ? 'NULL' : $this->uar1) . ',';
			$sql .= ' ' . (empty($this->uar2) ? 'NULL' : $this->uar2) . ',';
			$sql .= ' ' . (empty($this->uar3) ? 'NULL' : $this->uar3) . ',';
			$sql .= ' ' . (empty($this->nifissure) ? 'NULL' : $this->nifissure) . ',';
			$sql .= ' ' . (empty($this->nisoude) ? 'NULL' : $this->nisoude) . ',';
			$sql .= ' ' . (empty($this->etatmeca) ? 'NULL' : "'" . $this->db->escape($this->etatmeca) . "'") . ',';
			$sql .= ' ' . (empty($this->pres) ? 'NULL' : "'" . $this->db->escape($this->pres) . "'") . ',';
			$sql .= ' ' . (empty($this->couchette) ? 'NULL' : $this->couchette) . ',';
			$sql .= ' ' . (empty($this->nbreserv) ? 'NULL' : $this->nbreserv) . ',';
			$sql .= ' ' . (empty($this->capago) ? 'NULL' : $this->capago) . ',';
			$sql .= ' ' . (empty($this->adblue) ? 'NULL' : $this->adblue) . ',';
			$sql .= ' ' . (empty($this->lve) ? 'NULL' : $this->lve) . ',';
			$sql .= ' ' . (empty($this->rct) ? 'NULL' : $this->rct) . ',';
			$sql .= ' ' . (empty($this->gyro) ? 'NULL' : $this->gyro) . ',';
			$sql .= ' ' . (empty($this->echapv) ? 'NULL' : $this->echapv) . ',';
			$sql .= ' ' . (empty($this->adr) ? 'NULL' : $this->adr) . ',';
			$sql .= ' ' . (empty($this->hydro) ? 'NULL' : $this->hydro) . ',';
			$sql .= ' ' . (empty($this->climtoit) ? 'NULL' : $this->climtoit) . ',';
			$sql .= ' ' . (empty($this->webasto) ? 'NULL' : $this->webasto) . ',';
			$sql .= ' ' . (empty($this->clim) ? 'NULL' : $this->clim) . ',';
			$sql .= ' ' . (empty($this->compresseur) ? 'NULL' : $this->compresseur) . ',';
			$sql .= ' ' . (empty($this->deflecteur) ? 'NULL' : $this->deflecteur) . ',';
			$sql .= ' ' . (empty($this->jupes) ? 'NULL' : $this->jupes) . ',';
			$sql .= ' ' . (empty($this->copiecg) ? 'NULL' : $this->copiecg) . ',';
			$sql .= ' ' . (empty($this->copiect) ? 'NULL' : $this->copiect) . ',';
			$sql .= ' ' . (empty($this->copieca) ? 'NULL' : $this->copieca) . ',';
			$sql .= ' ' . (empty($this->photos) ? 'NULL' : $this->photos) . ',';
			$sql .= ' ' . (empty($this->estim) ? 'NULL' : "'" . $this->estim . "'") . ',';
			$sql .= ' ' . (empty($this->rachat) ? 'NULL' : "'" . $this->rachat . "'"). ',';
			$sql .= ' ' . (empty($this->actif) ? '0' : "'" . $this->actif . "'"). ',';
			$sql .= ' ' . (empty($this->status) ? '0' : "'" . $this->status . "'");

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
	 *
	 * @param unknown $value
	 * @return string
	 */
	public function show_picto($value) {
		if ($value == 1) {
			return img_picto('non', 'statut6','',1);
		} else {
			return img_picto('non', 'statut0','',1);
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
		$sql .= " t.fk_lead,";
		$sql .= " t.ref,";
		$sql .= " t.police,";
		$sql .= " t.fk_soc,";
		$sql .= " t.dateentree,";
		$sql .= " t.fk_siterestit,";
		$sql .= " t.fk_marque,";
		$sql .= " t.fk_genre,";
		$sql .= " t.type,";
		$sql .= " t.fk_silouhette,";
		$sql .= " t.place,";
		$sql .= " t.fk_norme,";
		$sql .= " t.numserie,";
		$sql .= " t.carrosserie,";
		$sql .= " t.puisfisc,";
		$sql .= " t.puiscom,";
		$sql .= " t.ptc,";
		$sql .= " t.pv,";
		$sql .= " t.ptr,";
		$sql .= " t.longutil,";
		$sql .= " t.largutil,";
		$sql .= " t.chargutil,";
		$sql .= " t.immat,";
		$sql .= " t.1circ AS circ,";
		$sql .= " t.kmact,";
		$sql .= " t.kmrestit,";
		$sql .= " t.validmine,";
		$sql .= " t.validtachy,";
		$sql .= " t.valid1,";
		$sql .= " t.date1,";
		$sql .= " t.valid2,";
		$sql .= " t.date2,";
		$sql .= " t.agrement,";
		$sql .= " t.dateagrement,";
		$sql .= " t.fk_cabine,";
		$sql .= " t.fk_suspcabine,";
		$sql .= " t.fk_moteur,";
		$sql .= " t.fk_ralentisseur,";
		$sql .= " t.fk_bv,";
		$sql .= " t.rav,";
		$sql .= " t.rar,";
		$sql .= " t.sr,";
		$sql .= " t.dr,";
		$sql .= " t.blocage,";
		$sql .= " t.rapport,";
		$sql .= " t.fk_freinage,";
		$sql .= " t.abs,";
		$sql .= " t.asr,";
		$sql .= " t.ebs,";
		$sql .= " t.esp,";
		$sql .= " t.dfr,";
		$sql .= " t.suspav,";
		$sql .= " t.suspar,";
		$sql .= " t.fk_mav,";
		$sql .= " t.fk_mar1,";
		$sql .= " t.fk_mar2,";
		$sql .= " t.fk_mar3,";
		$sql .= " t.tav,";
		$sql .= " t.tar1,";
		$sql .= " t.tar2,";
		$sql .= " t.tar3,";
		$sql .= " t.dav,";
		$sql .= " t.dar1,";
		$sql .= " t.dar2,";
		$sql .= " t.dar3,";
		$sql .= " t.pav,";
		$sql .= " t.par1,";
		$sql .= " t.par2,";
		$sql .= " t.par3,";
		$sql .= " t.uav,";
		$sql .= " t.uar1,";
		$sql .= " t.uar2,";
		$sql .= " t.uar3,";
		$sql .= " t.nifissure,";
		$sql .= " t.nisoude,";
		$sql .= " t.etatmeca,";
		$sql .= " t.pres,";
		$sql .= " t.couchette,";
		$sql .= " t.nbreserv,";
		$sql .= " t.capago,";
		$sql .= " t.adblue,";
		$sql .= " t.2lve,";
		$sql .= " t.2rct,";
		$sql .= " t.gyro,";
		$sql .= " t.echapv,";
		$sql .= " t.adr,";
		$sql .= " t.hydro,";
		$sql .= " t.climtoit,";
		$sql .= " t.webasto,";
		$sql .= " t.clim,";
		$sql .= " t.compresseur,";
		$sql .= " t.deflecteur,";
		$sql .= " t.jupes,";
		$sql .= " t.copiecg,";
		$sql .= " t.copiect,";
		$sql .= " t.copieca,";
		$sql .= " t.photos,";
		$sql .= " t.estim,";
		$sql .= " t.rachat,";
		$sql .= " t.actif,";
		$sql .= " t.fk_status";

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
				$this->fk_lead = $obj->fk_lead;
				$this->ref = $obj->ref;
				$this->police = $obj->police;
				$this->fk_soc = $obj->fk_soc;
				$this->date_entree = $this->db->jdate($obj->dateentree);
				$this->fk_restit = $obj->fk_siterestit;
				$this->fk_marque = $obj->fk_marque;
				$this->fk_genre = $obj->fk_genre;
				$this->type = $obj->type;
				$this->fk_silouhette = $obj->fk_silouhette;
				$this->place = $obj->place;
				$this->fk_norme = $obj->fk_norme;
				$this->numserie = $obj->numserie;
				$this->carrosserie = $obj->carrosserie;
				$this->puisfisc = $obj->puisfisc;
				$this->puiscom = $obj->puiscom;
				$this->ptc = $obj->ptc;
				$this->pv = $obj->pv;
				$this->ptr = $obj->ptr;
				$this->longutil = $obj->longutil;
				$this->largutil = $obj->largutil;
				$this->chargutil = $obj->chargutil;
				$this->immat = $obj->immat;
				$this->circ = $this->db->jdate($obj->circ);
				$this->kmact = $obj->kmact;
				$this->kmrestit = $obj->kmrestit;
				$this->validmine = $this->db->jdate($obj->validmine);
				$this->validtachy = $this->db->jdate($obj->validtachy);
				$this->valid1 = $obj->valid1;
				$this->date1 = $this->db->jdate($obj->date1);
				$this->valid2 = $obj->valid2;
				$this->date2 = $this->db->jdate($obj->date2);
				$this->agrement = $obj->agrement;
				$this->dateagrement = $this->db->jdate($obj->dateagrement);
				$this->fk_cabine = $obj->fk_cabine;
				$this->fk_suspcabine = $obj->fk_suspcabine;
				$this->fk_moteur = $obj->fk_moteur;
				$this->fk_ralentisseur = $obj->fk_ralentisseur;
				$this->fk_bv = $obj->fk_bv;
				$this->rav = $obj->rav;
				$this->rar = $obj->rar;
				$this->sr = $obj->sr;
				$this->dr = $obj->dr;
				$this->blocage = $obj->blocage;
				$this->rapport = $obj->rapport;
				$this->fk_freinage = $obj->fk_freinage;
				$this->abs = $obj->abs;
				$this->asr = $obj->asr;
				$this->ebs = $obj->ebs;
				$this->esp = $obj->esp;
				$this->dfr = $obj->dfr;
				$this->suspav = $obj->suspav;
				$this->suspar = $obj->suspar;
				$this->fk_mav = $obj->fk_mav;
				$this->fk_mar1 = $obj->fk_mar1;
				$this->fk_mar2 = $obj->fk_mar2;
				$this->fk_mar3 = $obj->fk_mar3;
				$this->tav = $obj->tav;
				$this->tar1 = $obj->tar1;
				$this->tar2 = $obj->tar2;
				$this->tar3 = $obj->tar3;
				$this->dav = $obj->dav;
				$this->dar1 = $obj->dar1;
				$this->dar2 = $obj->dar2;
				$this->dar3 = $obj->dar3;
				$this->pav = $obj->pav;
				$this->par1 = $obj->par1;
				$this->par2 = $obj->par2;
				$this->par3 = $obj->par3;
				$this->uav = $obj->uav;
				$this->uar1 = $obj->uar1;
				$this->uar2 = $obj->uar2;
				$this->uar3 = $obj->uar3;
				$this->nifissure = $obj->nifissure;
				$this->nisoude = $obj->nisoude;
				$this->etatmeca = $obj->etatmeca;
				$this->pres = $obj->pres;
				$this->couchette = $obj->couchette;
				$this->nbreserv = $obj->nbreserv;
				$this->capago = $obj->capago;
				$this->adblue = $obj->adblue;
				$this->lve = $obj->lve;
				$this->rct = $obj->rct;
				$this->gyro = $obj->gyro;
				$this->echapv = $obj->echapv;
				$this->adr = $obj->adr;
				$this->hydro = $obj->hydro;
				$this->climtoit = $obj->climtoit;
				$this->webasto = $obj->webasto;
				$this->clim = $obj->clim;
				$this->compresseur = $obj->compresseur;
				$this->deflecteur = $obj->deflecteur;
				$this->jupes = $obj->jupes;
				$this->copiecg = $obj->copiecg;
				$this->copiect = $obj->copiect;
				$this->copieca = $obj->copieca;
				$this->photos = $obj->photos;
				$this->estim = $obj->estim;
				$this->rachat = $obj->rachat;
				$this->actif = $obj->actif;
				$this->status = $obj->fk_status;
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
		$sql .= " t.fk_lead,";
		$sql .= " t.ref,";
		$sql .= " t.police,";
		$sql .= " t.fk_soc,";
		$sql .= " t.dateentree,";
		$sql .= " t.fk_siterestit,";
		$sql .= " t.fk_marque,";
		$sql .= " t.fk_genre,";
		$sql .= " t.type,";
		$sql .= " t.fk_silouhette,";
		$sql .= " t.place,";
		$sql .= " t.fk_norme,";
		$sql .= " t.numserie,";
		$sql .= " t.carrosserie,";
		$sql .= " t.puisfisc,";
		$sql .= " t.puiscom,";
		$sql .= " t.ptc,";
		$sql .= " t.pv,";
		$sql .= " t.ptr,";
		$sql .= " t.longutil,";
		$sql .= " t.largutil,";
		$sql .= " t.chargutil,";
		$sql .= " t.immat,";
		$sql .= " t.1circ AS circ,";
		$sql .= " t.kmact,";
		$sql .= " t.kmrestit,";
		$sql .= " t.validmine,";
		$sql .= " t.validtachy,";
		$sql .= " t.valid1,";
		$sql .= " t.date1,";
		$sql .= " t.valid2,";
		$sql .= " t.date2,";
		$sql .= " t.agrement,";
		$sql .= " t.dateagrement,";
		$sql .= " t.fk_cabine,";
		$sql .= " t.fk_suspcabine,";
		$sql .= " t.fk_moteur,";
		$sql .= " t.fk_ralentisseur,";
		$sql .= " t.fk_bv,";
		$sql .= " t.rav,";
		$sql .= " t.rar,";
		$sql .= " t.sr,";
		$sql .= " t.dr,";
		$sql .= " t.blocage,";
		$sql .= " t.rapport,";
		$sql .= " t.fk_freinage,";
		$sql .= " t.abs,";
		$sql .= " t.asr,";
		$sql .= " t.ebs,";
		$sql .= " t.esp,";
		$sql .= " t.dfr,";
		$sql .= " t.suspav,";
		$sql .= " t.suspar,";
		$sql .= " t.fk_mav,";
		$sql .= " t.fk_mar1,";
		$sql .= " t.fk_mar2,";
		$sql .= " t.fk_mar3,";
		$sql .= " t.tav,";
		$sql .= " t.tar1,";
		$sql .= " t.tar2,";
		$sql .= " t.tar3,";
		$sql .= " t.dav,";
		$sql .= " t.dar1,";
		$sql .= " t.dar2,";
		$sql .= " t.dar3,";
		$sql .= " t.pav,";
		$sql .= " t.par1,";
		$sql .= " t.par2,";
		$sql .= " t.par3,";
		$sql .= " t.uav,";
		$sql .= " t.uar1,";
		$sql .= " t.uar2,";
		$sql .= " t.uar3,";
		$sql .= " t.nifissure,";
		$sql .= " t.nisoude,";
		$sql .= " t.etatmeca,";
		$sql .= " t.pres,";
		$sql .= " t.couchette,";
		$sql .= " t.nbreserv,";
		$sql .= " t.capago,";
		$sql .= " t.adblue,";
		$sql .= " t.2lve,";
		$sql .= " t.2rct,";
		$sql .= " t.gyro,";
		$sql .= " t.echapv,";
		$sql .= " t.adr,";
		$sql .= " t.hydro,";
		$sql .= " t.climtoit,";
		$sql .= " t.webasto,";
		$sql .= " t.clim,";
		$sql .= " t.compresseur,";
		$sql .= " t.deflecteur,";
		$sql .= " t.jupes,";
		$sql .= " t.copiecg,";
		$sql .= " t.copiect,";
		$sql .= " t.copieca,";
		$sql .= " t.photos,";
		$sql .= " t.estim,";
		$sql .= " t.rachat,";
		$sql .= " t.actif,";
		$sql .= " t.fk_status,";
		$sql .= " s.nom,";
		$sql .= " genre.genre as genre_label,";
		$sql .= " silouhette.silouhette as silouhette_label,";
		$sql .= " marque.marque as marque_label,";
		$sql .= " norme.norme as norme_label,";
		$sql .= " cabine.cabine as cabine_label,";
		$sql .= " bv.bv as bv_label,";
		$sql .= ' CONCAT(IF(t.sr = 1,"SR - ",""), IF(t.dr = 1,"DR - ",""),IF(t.blocage = 1,"BLOCAGE","")) as pont_label,';
		$sql .= ' CONCAT( IF(t.abs = 1,"ABS - ",""), IF(t.asr = 1,"ASR - ",""), IF(t.ebs = 1,"EBS - ",""), IF(t.esp = 1,"ESP - ",""), IF(t.dfr = 1,"DFR", "") ) as freinage_label,';
		$sql .= ' CONCAT( IF(2lve = 1,"2LVE - ",""), IF(2rct = 1,"2RCT - ",""), IF(gyro = 1,"Gyrophare - ",""), IF(echapv = 1,"Echappement Vertical - ",""), ';
		$sql .= ' IF(adr = 1,"ADR - ",""), IF(hydro = 1,"Hydrolique - ",""), IF(compresseur = 1,"Compresseur - ",""), IF(climtoit = 1,"Clim de toit - ",""), IF(clim = 1,"Clim - ",""), ';
		$sql .= ' IF(webasto = 1,"Webasto - ",""), IF(deflecteur = 1,"Deflecteurs - ",""), IF(jupes = 1,"Jupes", "") ) as option_label,';
		$sql .= ' IF(t.estim > 0,1,0) as estim_ok,';
		$sql .= ' IF(t.rachat > 0,1,0) as offre_ok';

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_genre' . ' as genre ON genre.rowid=t.fk_genre';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_silouhette' . ' as silouhette ON silouhette.rowid=t.fk_silouhette';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_marques' . ' as marque ON marque.rowid=t.fk_marque';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_normes' . ' as norme ON norme.rowid=t.fk_norme';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_cabine' . ' as cabine ON cabine.rowid=t.fk_cabine';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_bv' . ' as bv ON bv.rowid=t.fk_bv';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'societe' . ' as s ON s.rowid=t.fk_soc';

		if (array_key_exists('l.fk_user_resp', $filter)) {
			$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "lead as l ON l.rowid=t.fk_lead";
		}

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ( $filter as $key => $value ) {
				if (($key == 't.fk_lead') || ($key == 't.fk_silouhette') || ($key == 't.fk_marque') || ($key == 't.fk_genre') || ($key == 't.fk_norme') || ($key == 'l.fk_user_resp') || ($key == 'expertise_ok') || ($key == 'reception_ok') || ($key == 'estim_ok') || ($key == 'offre_ok')) {
					$sqlwhere[] = $key . '=' . $value;
				} elseif (($key == 't.kmrestit') || ($key == 't.capago')){
					$sqlwhere[] = $key . ' BETWEEN ' . $value;
				} else {
					$sqlwhere[] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' HAVING ' . implode(' ' . $filtermode . ' ', $sqlwhere);
		}

		if (! empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (! empty($limit)) {
			$sql .= ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ( $obj = $this->db->fetch_object($resql) ) {
				$line = new RepriseLine($this->db);

				$line->id = $obj->rowid;

				$line->tms = $this->db->jdate($obj->tms);
				$line->fk_lead = $obj->fk_lead;
				$line->ref = $obj->ref;
				$line->police = $obj->police;
				$line->fk_soc = $obj->fk_soc;
				$line->date_entree = $this->db->jdate($obj->dateentree);
				$line->fk_restit = $obj->fk_siterestit;
				$line->fk_marque = $obj->fk_marque;
				$line->fk_genre = $obj->fk_genre;
				$line->type = $obj->type;
				$line->fk_silouhette = $obj->fk_silouhette;
				$line->place = $obj->place;
				$line->fk_norme = $obj->fk_norme;
				$line->numserie = $obj->numserie;
				$line->carrosserie = $obj->carrosserie;
				$line->puisfisc = $obj->puisfisc;
				$line->puiscom = $obj->puiscom;
				$line->ptc = $obj->ptc;
				$line->pv = $obj->pv;
				$line->ptr = $obj->ptr;
				$line->longutil = $obj->longutil;
				$line->largutil = $obj->largutil;
				$line->chargutil = $obj->chargutil;
				$line->immat = $obj->immat;
				$line->circ = $this->db->jdate($obj->circ);
				$line->kmact = $obj->kmact;
				$line->kmrestit = $obj->kmrestit;
				$line->validmine = $this->db->jdate($obj->validmine);
				$line->validtachy = $this->db->jdate($obj->validtachy);
				$line->valid1 = $obj->valid1;
				$line->date1 = $this->db->jdate($obj->date1);
				$line->valid2 = $obj->valid2;
				$line->date2 = $this->db->jdate($obj->date2);
				$line->agrement = $obj->agrement;
				$line->dateagrement = $this->db->jdate($obj->dateagrement);
				$line->fk_cabine = $obj->fk_cabine;
				$line->fk_suspcabine = $obj->fk_suspcabine;
				$line->fk_moteur = $obj->fk_moteur;
				$line->fk_ralentisseur = $obj->fk_ralentisseur;
				$line->fk_bv = $obj->fk_bv;
				$line->rav = $obj->rav;
				$line->rar = $obj->rar;
				$line->sr = $obj->sr;
				$line->dr = $obj->dr;
				$line->blocage = $obj->blocage;
				$line->rapport = $obj->rapport;
				$line->fk_freinage = $obj->fk_freinage;
				$line->abs = $obj->abs;
				$line->asr = $obj->asr;
				$line->ebs = $obj->ebs;
				$line->esp = $obj->esp;
				$line->dfr = $obj->dfr;
				$line->suspav = $obj->suspav;
				$line->suspar = $obj->suspar;
				$line->fk_mav = $obj->fk_mav;
				$line->fk_mar1 = $obj->fk_mar1;
				$line->fk_mar2 = $obj->fk_mar2;
				$line->fk_mar3 = $obj->fk_mar3;
				$line->tav = $obj->tav;
				$line->tar1 = $obj->tar1;
				$line->tar2 = $obj->tar2;
				$line->tar3 = $obj->tar3;
				$line->dav = $obj->dav;
				$line->dar1 = $obj->dar1;
				$line->dar2 = $obj->dar2;
				$line->dar3 = $obj->dar3;
				$line->pav = $obj->pav;
				$line->par1 = $obj->par1;
				$line->par2 = $obj->par2;
				$line->par3 = $obj->par3;
				$line->uav = $obj->uav;
				$line->uar1 = $obj->uar1;
				$line->uar2 = $obj->uar2;
				$line->uar3 = $obj->uar3;
				$line->nifissure = $obj->nifissure;
				$line->nisoude = $obj->nisoude;
				$line->etatmeca = $obj->etatmeca;
				$line->pres = $obj->pres;
				$line->couchette = $obj->couchette;
				$line->nbreserv = $obj->nbreserv;
				$line->capago = $obj->capago;
				$line->adblue = $obj->adblue;
				$line->lve = $obj->lve;
				$line->rct = $obj->rct;
				$line->gyro = $obj->gyro;
				$line->echapv = $obj->echapv;
				$line->adr = $obj->adr;
				$line->hydro = $obj->hydro;
				$line->climtoit = $obj->climtoit;
				$line->webasto = $obj->webasto;
				$line->clim = $obj->clim;
				$line->compresseur = $obj->compresseur;
				$line->deflecteur = $obj->deflecteur;
				$line->jupes = $obj->jupes;
				$line->copiecg = $obj->copiecg;
				$line->copiect = $obj->copiect;
				$line->copieca = $obj->copieca;
				$line->photos = $obj->photos;
				$line->estim = $obj->estim;
				$line->rachat = $obj->rachat;
				$line->actif = $obj->actif;
				$line->status = $line->updatestatus($obj->rowid);
				$line->genre_label=$obj->genre_label;
				$line->silouhette_label=$obj->silouhette_label;
				$line->marque_label=$obj->marque_label;
				$line->norme_label=$obj->norme_label;
				$line->cabine_label=$obj->cabine_label;
				$line->bv_label=$obj->bv_label;
				$line->pont_label=$obj->pont_label;
				$line->freinage_label=$obj->freinage_label;
				$line->option_label=$obj->option_label;

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);
			dol_syslog(__METHOD__ . ' ' . $sql, LOG_ERR);
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

		if (isset($this->fk_lead)) {
			$this->fk_lead = trim($this->fk_lead);
		}
		if (isset($this->ref)) {
			$this->ref = trim($this->ref);
		}
		if (isset($this->police)) {
			$this->police = trim($this->police);
		}
		if (isset($this->fk_soc)) {
			$this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->fk_restit)) {
			$this->fk_restit = trim($this->fk_restit);
		}
		if (isset($this->fk_marque)) {
			$this->fk_marque = trim($this->fk_marque);
		}
		if (isset($this->fk_genre)) {
			$this->fk_genre = trim($this->fk_genre);
		}
		if (isset($this->type)) {
			$this->type = trim($this->type);
		}
		if (isset($this->fk_silouhette)) {
			$this->fk_silouhette = trim($this->fk_silouhette);
		}
		if (isset($this->place)) {
			$this->place = trim($this->place);
		}
		if (isset($this->fk_norme)) {
			$this->fk_norme = trim($this->fk_norme);
		}
		if (isset($this->numserie)) {
			$this->numserie = trim($this->numserie);
		}
		if (isset($this->carrosserie)) {
			$this->carrosserie = trim($this->carrosserie);
		}
		if (isset($this->puisfisc)) {
			$this->puisfisc = trim($this->puisfisc);
		}
		if (isset($this->puiscom)) {
			$this->puiscom = trim($this->puiscom);
		}
		if (isset($this->ptc)) {
			$this->ptc = trim($this->ptc);
		}
		if (isset($this->pv)) {
			$this->pv = trim($this->pv);
		}
		if (isset($this->ptr)) {
			$this->ptr = trim($this->ptr);
		}
		if (isset($this->longutil)) {
			$this->longutil = trim($this->longutil);
		}
		if (isset($this->largutil)) {
			$this->largutil = trim($this->largutil);
		}
		if (isset($this->chargutil)) {
			$this->chargutil = trim($this->chargutil);
		}
		if (isset($this->immat)) {
			$this->immat = trim($this->immat);
		}
		if (isset($this->kmact)) {
			$this->kmact = trim($this->kmact);
		}
		if (isset($this->kmrestit)) {
			$this->kmrestit = trim($this->kmrestit);
		}
		if (isset($this->valid1)) {
			$this->valid1 = trim($this->valid1);
		}
		if (isset($this->valid2)) {
			$this->valid2 = trim($this->valid2);
		}
		if (isset($this->agrement)) {
			$this->agrement = trim($this->agrement);
		}
		if (isset($this->fk_cabine)) {
			$this->fk_cabine = trim($this->fk_cabine);
		}
		if (isset($this->fk_suspcabine)) {
			$this->fk_suspcabine = trim($this->fk_suspcabine);
		}
		if (isset($this->fk_moteur)) {
			$this->fk_moteur = trim($this->fk_moteur);
		}
		if (isset($this->fk_ralentisseur)) {
			$this->fk_ralentisseur = trim($this->fk_ralentisseur);
		}
		if (isset($this->fk_bv)) {
			$this->fk_bv = trim($this->fk_bv);
		}
		if (isset($this->rav)) {
			$this->rav = trim($this->rav);
		}
		if (isset($this->rar)) {
			$this->rar = trim($this->rar);
		}
		if (isset($this->sr)) {
			$this->sr = trim($this->sr);
		}
		if (isset($this->dr)) {
			$this->dr = trim($this->dr);
		}
		if (isset($this->blocage)) {
			$this->blocage = trim($this->blocage);
		}
		if (isset($this->rapport)) {
			$this->rapport = trim($this->rapport);
		}
		if (isset($this->fk_freinage)) {
			$this->fk_freinage = trim($this->fk_freinage);
		}
		if (isset($this->abs)) {
			$this->abs = trim($this->abs);
		}
		if (isset($this->asr)) {
			$this->asr = trim($this->asr);
		}
		if (isset($this->ebs)) {
			$this->ebs = trim($this->ebs);
		}
		if (isset($this->esp)) {
			$this->esp = trim($this->esp);
		}
		if (isset($this->dfr)) {
			$this->dfr = trim($this->dfr);
		}
		if (isset($this->suspav)) {
			$this->suspav = trim($this->suspav);
		}
		if (isset($this->suspar)) {
			$this->suspar = trim($this->suspar);
		}
		if (isset($this->fk_mav)) {
			$this->fk_mav = trim($this->fk_mav);
		}
		if (isset($this->fk_mar1)) {
			$this->fk_mar1 = trim($this->fk_mar1);
		}
		if (isset($this->fk_mar2)) {
			$this->fk_mar2 = trim($this->fk_mar2);
		}
		if (isset($this->fk_mar3)) {
			$this->fk_mar3 = trim($this->fk_mar3);
		}
		if (isset($this->tav)) {
			$this->tav = trim($this->tav);
		}
		if (isset($this->tar1)) {
			$this->tar1 = trim($this->tar1);
		}
		if (isset($this->tar2)) {
			$this->tar2 = trim($this->tar2);
		}
		if (isset($this->tar3)) {
			$this->tar3 = trim($this->tar3);
		}
		if (isset($this->dav)) {
			$this->dav = trim($this->dav);
		}
		if (isset($this->dar1)) {
			$this->dar1 = trim($this->dar1);
		}
		if (isset($this->dar2)) {
			$this->dar2 = trim($this->dar2);
		}
		if (isset($this->dar3)) {
			$this->dar3 = trim($this->dar3);
		}
		if (isset($this->pav)) {
			$this->pav = trim($this->pav);
		}
		if (isset($this->par1)) {
			$this->par1 = trim($this->par1);
		}
		if (isset($this->par2)) {
			$this->par2 = trim($this->par2);
		}
		if (isset($this->par3)) {
			$this->par3 = trim($this->par3);
		}
		if (isset($this->uav)) {
			$this->uav = trim($this->uav);
		}
		if (isset($this->uar1)) {
			$this->uar1 = trim($this->uar1);
		}
		if (isset($this->uar2)) {
			$this->uar2 = trim($this->uar2);
		}
		if (isset($this->uar3)) {
			$this->uar3 = trim($this->uar3);
		}
		if (isset($this->nifissure)) {
			$this->nifissure = trim($this->nifissure);
		}
		if (isset($this->nisoude)) {
			$this->nisoude = trim($this->nisoude);
		}
		if (isset($this->etatmeca)) {
			$this->etatmeca = trim($this->etatmeca);
		}
		if (isset($this->pres)) {
			$this->pres = trim($this->pres);
		}
		if (isset($this->couchette)) {
			$this->couchette = trim($this->couchette);
		}
		if (isset($this->nbreserv)) {
			$this->nbreserv = trim($this->nbreserv);
		}
		if (isset($this->capago)) {
			$this->capago = trim($this->capago);
		}
		if (isset($this->adblue)) {
			$this->adblue = trim($this->adblue);
		}
		if (isset($this->lve)) {
			$this->lve = trim($this->lve);
		}
		if (isset($this->rct)) {
			$this->rct = trim($this->rct);
		}
		if (isset($this->gyro)) {
			$this->gyro = trim($this->gyro);
		}
		if (isset($this->echapv)) {
			$this->echapv = trim($this->echapv);
		}
		if (isset($this->adr)) {
			$this->adr = trim($this->adr);
		}
		if (isset($this->hydro)) {
			$this->hydro = trim($this->hydro);
		}
		if (isset($this->climtoit)) {
			$this->climtoit = trim($this->climtoit);
		}
		if (isset($this->webasto)) {
			$this->webasto = trim($this->webasto);
		}
		if (isset($this->clim)) {
			$this->clim = trim($this->clim);
		}
		if (isset($this->compresseur)) {
			$this->compresseur = trim($this->compresseur);
		}
		if (isset($this->deflecteur)) {
			$this->deflecteur = trim($this->deflecteur);
		}
		if (isset($this->jupes)) {
			$this->jupes = trim($this->jupes);
		}
		if (isset($this->copiecg)) {
			$this->copiecg = trim($this->copiecg);
		}
		if (isset($this->copiect)) {
			$this->copiect = trim($this->copiect);
		}
		if (isset($this->copieca)) {
			$this->copieca = trim($this->copieca);
		}
		if (isset($this->photos)) {
			$this->photos = trim($this->photos);
		}
		if (isset($this->estim)) {
			$this->estim = trim($this->estim);
		}
		if (isset($this->rachat)) {
			$this->rachat = trim($this->rachat);
		}

		if (empty($this->fk_soc)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'client');
		}
		if (empty($this->fk_lead)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'affaire');
		}

		if (empty($error)) {
			// Check parameters
			// Put here code to add a control on parameters values

			// Update request
			$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';

			$sql .= ' tms = ' . (dol_strlen($this->tms) != 0 ? "'" . $this->db->idate($this->tms) . "'" : "'" . $this->db->idate(dol_now()) . "'") . ',';
			$sql .= ' fk_lead = ' . (! empty($this->fk_lead) ? $this->fk_lead : "null") . ',';
			$sql .= ' ref = ' . (! empty($this->ref) ? "'" . $this->db->escape($this->ref) . "'" : "null") . ',';
			$sql .= ' police = ' . (! empty($this->police) ? "'" . $this->db->escape($this->police) . "'" : "null") . ',';
			$sql .= ' fk_soc = ' . (! empty($this->fk_soc) ? $this->fk_soc : "null") . ',';
			$sql .= ' dateentree = ' . (! ! empty($this->date_entree) || dol_strlen($this->date_entree) != 0 ? "'" . $this->db->idate($this->date_entree) . "'" : 'null') . ',';
			$sql .= ' fk_siterestit = ' . (! empty($this->fk_restit) ? $this->fk_restit : "null") . ',';
			$sql .= ' fk_marque = ' . (! empty($this->fk_marque) ? $this->fk_marque : "null") . ',';
			$sql .= ' fk_genre = ' . (! empty($this->fk_genre) ? $this->fk_genre : "null") . ',';
			$sql .= ' type = ' . (! empty($this->type) ? "'" . $this->db->escape($this->type) . "'" : "null") . ',';
			$sql .= ' fk_silouhette = ' . (! empty($this->fk_silouhette) ? $this->fk_silouhette : "null") . ',';
			$sql .= ' place = ' . (! empty($this->place) ? $this->place : "null") . ',';
			$sql .= ' fk_norme = ' . (! empty($this->fk_norme) ? $this->fk_norme : "null") . ',';
			$sql .= ' numserie = ' . (! empty($this->numserie) ? "'" . $this->db->escape($this->numserie) . "'" : "null") . ',';
			$sql .= ' carrosserie = ' . (! empty($this->carrosserie) ? "'" . $this->db->escape($this->carrosserie) . "'" : "null") . ',';
			$sql .= ' puisfisc = ' . (! empty($this->puisfisc) ? $this->puisfisc : "null") . ',';
			$sql .= ' puiscom = ' . (! empty($this->puiscom) ? $this->puiscom : "null") . ',';
			$sql .= ' ptc = ' . (! empty($this->ptc) ? $this->ptc : "null") . ',';
			$sql .= ' pv = ' . (! empty($this->pv) ? $this->pv : "null") . ',';
			$sql .= ' ptr = ' . (! empty($this->ptr) ? $this->ptr : "null") . ',';
			$sql .= ' longutil = ' . (! empty($this->longutil) ? $this->longutil : "null") . ',';
			$sql .= ' largutil = ' . (! empty($this->largutil) ? $this->largutil : "null") . ',';
			$sql .= ' chargutil = ' . (! empty($this->chargutil) ? $this->chargutil : "null") . ',';
			$sql .= ' immat = ' . (! empty($this->immat) ? "'" . $this->db->escape($this->immat) . "'" : "null") . ',';
			$sql .= ' 1circ = ' . (! ! empty($this->circ) || dol_strlen($this->circ) != 0 ? "'" . $this->db->idate($this->circ) . "'" : 'null') . ',';
			$sql .= ' kmact = ' . (! empty($this->kmact) ? $this->kmact : "null") . ',';
			$sql .= ' kmrestit = ' . (! empty($this->kmrestit) ? $this->kmrestit : "null") . ',';
			$sql .= ' validmine = ' . (! ! empty($this->validmine) || dol_strlen($this->validmine) != 0 ? "'" . $this->db->idate($this->validmine) . "'" : 'null') . ',';
			$sql .= ' validtachy = ' . (! ! empty($this->validtachy) || dol_strlen($this->validtachy) != 0 ? "'" . $this->db->idate($this->validtachy) . "'" : 'null') . ',';
			$sql .= ' valid1 = ' . (! empty($this->valid1) ? "'" . $this->db->escape($this->valid1) . "'" : "null") . ',';
			$sql .= ' date1 = ' . (! ! empty($this->date1) || dol_strlen($this->date1) != 0 ? "'" . $this->db->idate($this->date1) . "'" : 'null') . ',';
			$sql .= ' valid2 = ' . (! empty($this->valid2) ? "'" . $this->db->escape($this->valid2) . "'" : "null") . ',';
			$sql .= ' date2 = ' . (! ! empty($this->date2) || dol_strlen($this->date2) != 0 ? "'" . $this->db->idate($this->date2) . "'" : 'null') . ',';
			$sql .= ' agrement = ' . (! empty($this->agrement) ? "'" . $this->db->escape($this->agrement) . "'" : "null") . ',';
			$sql .= ' dateagrement = ' . (! ! empty($this->dateagrement) || dol_strlen($this->dateagrement) != 0 ? "'" . $this->db->idate($this->dateagrement) . "'" : 'null') . ',';
			$sql .= ' fk_cabine = ' . (! empty($this->fk_cabine) ? $this->fk_cabine : "null") . ',';
			$sql .= ' fk_suspcabine = ' . (! empty($this->fk_suspcabine) ? $this->fk_suspcabine : "null") . ',';
			$sql .= ' fk_moteur = ' . (! empty($this->fk_moteur) ? $this->fk_moteur : "null") . ',';
			$sql .= ' fk_ralentisseur = ' . (! empty($this->fk_ralentisseur) ? $this->fk_ralentisseur : "null") . ',';
			$sql .= ' fk_bv = ' . (! empty($this->fk_bv) ? $this->fk_bv : "null") . ',';
			$sql .= ' rav = ' . (! empty($this->rav) ? $this->rav : "null") . ',';
			$sql .= ' rar = ' . (! empty($this->rar) ? $this->rar : "null") . ',';
			$sql .= ' sr = ' . (! empty($this->sr) ? $this->sr : "null") . ',';
			$sql .= ' dr = ' . (! empty($this->dr) ? $this->dr : "null") . ',';
			$sql .= ' blocage = ' . (! empty($this->blocage) ? $this->blocage : "null") . ',';
			$sql .= ' rapport = ' . (! empty($this->rapport) ? $this->rapport : "null") . ',';
			$sql .= ' fk_freinage = ' . (! empty($this->fk_freinage) ? $this->fk_freinage : "null") . ',';
			$sql .= ' abs = ' . (! empty($this->abs) ? $this->abs : "null") . ',';
			$sql .= ' asr = ' . (! empty($this->asr) ? $this->asr : "null") . ',';
			$sql .= ' ebs = ' . (! empty($this->ebs) ? $this->ebs : "null") . ',';
			$sql .= ' esp = ' . (! empty($this->esp) ? $this->esp : "null") . ',';
			$sql .= ' dfr = ' . (! empty($this->dfr) ? $this->dfr : "null") . ',';
			$sql .= ' suspav = ' . (! empty($this->suspav) ? "'" . $this->db->escape($this->suspav) . "'" : "null") . ',';
			$sql .= ' suspar = ' . (! empty($this->suspar) ? "'" . $this->db->escape($this->suspar) . "'" : "null") . ',';
			$sql .= ' fk_mav = ' . (! empty($this->fk_mav) ? $this->fk_mav : "null") . ',';
			$sql .= ' fk_mar1 = ' . (! empty($this->fk_mar1) ? $this->fk_mar1 : "null") . ',';
			$sql .= ' fk_mar2 = ' . (! empty($this->fk_mar2) ? $this->fk_mar2 : "null") . ',';
			$sql .= ' fk_mar3 = ' . (! empty($this->fk_mar3) ? $this->fk_mar3 : "null") . ',';
			$sql .= ' tav = ' . (! empty($this->tav) ? "'" . $this->db->escape($this->tav) . "'" : "null") . ',';
			$sql .= ' tar1 = ' . (! empty($this->tar1) ? "'" . $this->db->escape($this->tar1) . "'" : "null") . ',';
			$sql .= ' tar2 = ' . (! empty($this->tar2) ? "'" . $this->db->escape($this->tar2) . "'" : "null") . ',';
			$sql .= ' tar3 = ' . (! empty($this->tar3) ? "'" . $this->db->escape($this->tar3) . "'" : "null") . ',';
			$sql .= ' dav = ' . (! empty($this->dav) ? $this->dav : "null") . ',';
			$sql .= ' dar1 = ' . (! empty($this->dar1) ? $this->dar1 : "null") . ',';
			$sql .= ' dar2 = ' . (! empty($this->dar2) ? $this->dar2 : "null") . ',';
			$sql .= ' dar3 = ' . (! empty($this->dar3) ? $this->dar3 : "null") . ',';
			$sql .= ' pav = ' . (! empty($this->pav) ? "'" . $this->db->escape($this->pav) . "'" : "null") . ',';
			$sql .= ' par1 = ' . (! empty($this->par1) ? "'" . $this->db->escape($this->par1) . "'" : "null") . ',';
			$sql .= ' par2 = ' . (! empty($this->par2) ? "'" . $this->db->escape($this->par2) . "'" : "null") . ',';
			$sql .= ' par3 = ' . (! empty($this->par3) ? "'" . $this->db->escape($this->par3) . "'" : "null") . ',';
			$sql .= ' uav = ' . (! empty($this->uav) ? $this->uav : "null") . ',';
			$sql .= ' uar1 = ' . (! empty($this->uar1) ? $this->uar1 : "null") . ',';
			$sql .= ' uar2 = ' . (! empty($this->uar2) ? $this->uar2 : "null") . ',';
			$sql .= ' uar3 = ' . (! empty($this->uar3) ? $this->uar3 : "null") . ',';
			$sql .= ' nifissure = ' . (! empty($this->nifissure) ? $this->nifissure : "null") . ',';
			$sql .= ' nisoude = ' . (! empty($this->nisoude) ? $this->nisoude : "null") . ',';
			$sql .= ' etatmeca = ' . (! empty($this->etatmeca) ? "'" . $this->db->escape($this->etatmeca) . "'" : "null") . ',';
			$sql .= ' pres = ' . (! empty($this->pres) ? "'" . $this->db->escape($this->pres) . "'" : "null") . ',';
			$sql .= ' couchette = ' . (! empty($this->couchette) ? $this->couchette : "null") . ',';
			$sql .= ' nbreserv = ' . (! empty($this->nbreserv) ? $this->nbreserv : "null") . ',';
			$sql .= ' capago = ' . (! empty($this->capago) ? $this->capago : "null") . ',';
			$sql .= ' adblue = ' . (! empty($this->adblue) ? $this->adblue : "null") . ',';
			$sql .= ' 2lve = ' . (! empty($this->lve) ? $this->lve : "null") . ',';
			$sql .= ' 2rct = ' . (! empty($this->rct) ? $this->rct : "null") . ',';
			$sql .= ' gyro = ' . (! empty($this->gyro) ? $this->gyro : "null") . ',';
			$sql .= ' echapv = ' . (! empty($this->echapv) ? $this->echapv : "null") . ',';
			$sql .= ' adr = ' . (! empty($this->adr) ? $this->adr : "null") . ',';
			$sql .= ' hydro = ' . (! empty($this->hydro) ? $this->hydro : "null") . ',';
			$sql .= ' climtoit = ' . (! empty($this->climtoit) ? $this->climtoit : "null") . ',';
			$sql .= ' webasto = ' . (! empty($this->webasto) ? $this->webasto : "null") . ',';
			$sql .= ' clim = ' . (! empty($this->clim) ? $this->clim : "null") . ',';
			$sql .= ' compresseur = ' . (! empty($this->compresseur) ? $this->compresseur : "null") . ',';
			$sql .= ' deflecteur = ' . (! empty($this->deflecteur) ? $this->deflecteur : "null") . ',';
			$sql .= ' jupes = ' . (! empty($this->jupes) ? $this->jupes : "null") . ',';
			$sql .= ' copiecg = ' . (! empty($this->copiecg) ? $this->copiecg : "null") . ',';
			$sql .= ' copiect = ' . (! empty($this->copiect) ? $this->copiect : "null") . ',';
			$sql .= ' copieca = ' . (! empty($this->copieca) ? $this->copieca : "null") . ',';
			$sql .= ' photos = ' . (! empty($this->photos) ? $this->photos : "null") . ',';
			$sql .= ' estim = ' . (! empty($this->estim) ? $this->estim : "null") . ',';
			$sql .= ' rachat = ' . (! empty($this->rachat) ? $this->rachat : "null"). ',';
			$sql .= ' actif = ' . (! empty($this->actif) ? $this->actif : "0"). ',';
			$sql .= ' fk_status = ' . $this->getstatus();

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
		$object = new Reprise($this->db);

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

		$label = '<u>' . $langs->trans("Reprise") . '</u>';
		$label .= '<div width="100%">';
		$label .= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

		$link = '<a href="' . dol_buildpath('/volvo/reprise/card.php',2).'?id=' . $this->fk_lead . '&repid='.$this->id.'"';
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

	function getNomUrl2($withpicto = 0, $option = '', $dest ='card2') {
		global $langs, $conf, $db;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Reprise") . '</u>';
		$label .= '<b>Reprise N°:</b> ' . $this->id;

		$link = '<a href="' . dol_buildpath('/volvo/reprise/'. $dest . '.php',2).'?id=' . $this->id .'"';
		$link .= ' title="' . dol_escape_htmltag($label, 1) . '" class="classfortooltip"';
		$link .= '>';
		if($option == 1){
			$linkend = 'VO Stock N°: ' . $this->ref;
		}
		$linkend.= '</a>';

		if ($withpicto) {
			$link .= (img_picto(($notooltip ? '' : $label), 'detail', ($notooltip ? '' : 'class="classfortooltip"')));
			if ($withpicto != 2)
				$result .= ' ';
		}
		$result = $link . $linkend;
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

		if ($mode == 0 || $mode == 1) {
			$prefix = '';
			if ($status == 0)
				return 'A expertiser';
			if ($status == 1)
				return 'A estimer';
			if ($status == 2)
				return "Offre d'achat a faire";
			if ($status == 3)
				return 'Affaire perdue';
			if ($status == 4)
				return 'Véhcule a rentrer';
			if ($status == 5)
				return 'En stock';
			if ($status == 6)
				return 'Mis hors Stock';
			if ($status == 7)
				return 'Vendu';
			if ($status == 8)
				return '!!!Erreur!!!';
		}

		if ($mode == 2) {
			if ($status == 0)
				return img_picto('A expertiser', 'statut0') . ' A expertiser';
			if ($status == 1)
				return img_picto('A estimer', 'statut1') . ' A estimer';
			if ($status == 2)
				return img_picto("Offre d'achat a faire", 'statut2') . " Offre d'achat a faire";
			if ($status == 3)
				return img_picto('Affaire perdue', 'warning') . ' Affaire perdue';
			if ($status == 4)
				return img_picto('Véhcule a rentrer', 'object_action') . ' Véhcule a rentrer';
			if ($status == 5)
				return img_picto('En stock', 'tick') . ' En stock';
			if ($status == 6)
				return img_picto('Mis hors Stock', 'statut8') . ' Mis hors Stock';
			if ($status == 7)
				return img_picto('Vendu', 'statut6') . ' Vendu';
			if ($status == 8)
				return img_picto('!!!Erreur!!!', 'recent') . ' !!!Erreur!!!';
		}

		if ($mode == 3 || $mode == 4) {
			if ($status == 0)
				return img_picto('A expertiser', 'statut0');
			if ($status == 1)
				return img_picto('A estimer', 'statut1');
			if ($status == 2)
				return img_picto("Offre d'achat a faire", 'statut2');
			if ($status == 3)
				return img_picto('Affaire perdue', 'warning');
			if ($status == 4)
				return img_picto('Véhcule a rentrer', 'object_action');
			if ($status == 5)
				return img_picto('En stock', 'tick');
			if ($status == 6)
				return img_picto('Mis hors Stock', 'statut8');
			if ($status == 7)
				return img_picto('Vendu', 'statut6');
			if ($status == 8)
				return img_picto('!!!Erreur!!!', 'recent');
		}

		if ($mode == 5) {
			if ($status == 0)
				return 'A expertiser ' . img_picto('A expertiser', 'statut0');
			if ($status == 1)
				return 'A estimer ' . img_picto('A estimer', 'statut1');
			if ($status == 2)
				return "Offre d'achat a faire " . img_picto("Offre d'achat a faire", 'statut2');
			if ($status == 3)
				return 'Affaire perdue ' . img_picto('Affaire perdue', 'warning');
			if ($status == 4)
				return 'Véhcule a rentrer ' . img_picto('Véhcule a rentrer', 'object_action');
			if ($status == 5)
				return 'En stock ' . img_picto('En stock', 'tick');
			if ($status == 6)
				return 'Mis hors Stock ' . img_picto('Mis hors Stock', 'statut8');
			if ($status == 7)
				return 'Vendu ' . img_picto('Vendu', 'statut6');
			if ($status == 8)
				return '!!!Erreur!!! ' . img_picto('!!!Erreur!!!', 'recent');
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
		$this->fk_lead = '';
		$this->ref = '';
		$this->police = '';
		$this->fk_soc = '';
		$this->date_entree = '';
		$this->fk_restit = '';
		$this->fk_marque = '';
		$this->fk_genre = '';
		$this->type = '';
		$this->fk_silouhette = '';
		$this->place = '';
		$this->fk_norme = '';
		$this->numserie = '';
		$this->carrosserie = '';
		$this->puisfisc = '';
		$this->puiscom = '';
		$this->ptc = '';
		$this->pv = '';
		$this->ptr = '';
		$this->longutil = '';
		$this->largutil = '';
		$this->chargutil = '';
		$this->immat = '';
		$this->circ = '';
		$this->kmact = '';
		$this->kmrestit = '';
		$this->validmine = '';
		$this->validtachy = '';
		$this->valid1 = '';
		$this->date1 = '';
		$this->valid2 = '';
		$this->date2 = '';
		$this->agrement = '';
		$this->dateagrement = '';
		$this->fk_cabine = '';
		$this->fk_suspcabine = '';
		$this->fk_moteur = '';
		$this->fk_ralentisseur = '';
		$this->fk_bv = '';
		$this->rav = '';
		$this->rar = '';
		$this->sr = '';
		$this->dr = '';
		$this->blocage = '';
		$this->rapport = '';
		$this->fk_freinage = '';
		$this->abs = '';
		$this->asr = '';
		$this->ebs = '';
		$this->esp = '';
		$this->dfr = '';
		$this->suspav = '';
		$this->suspar = '';
		$this->fk_mav = '';
		$this->fk_mar1 = '';
		$this->fk_mar2 = '';
		$this->fk_mar3 = '';
		$this->tav = '';
		$this->tar1 = '';
		$this->tar2 = '';
		$this->tar3 = '';
		$this->dav = '';
		$this->dar1 = '';
		$this->dar2 = '';
		$this->dar3 = '';
		$this->pav = '';
		$this->par1 = '';
		$this->par2 = '';
		$this->par3 = '';
		$this->uav = '';
		$this->uar1 = '';
		$this->uar2 = '';
		$this->uar3 = '';
		$this->nifissure = '';
		$this->nisoude = '';
		$this->etatmeca = '';
		$this->pres = '';
		$this->couchette = '';
		$this->nbreserv = '';
		$this->capago = '';
		$this->adblue = '';
		$this->lve = '';
		$this->rct = '';
		$this->gyro = '';
		$this->echapv = '';
		$this->adr = '';
		$this->hydro = '';
		$this->climtoit = '';
		$this->webasto = '';
		$this->clim = '';
		$this->compresseur = '';
		$this->deflecteur = '';
		$this->jupes = '';
		$this->copiecg = '';
		$this->copiect = '';
		$this->copieca = '';
		$this->photos = '';
		$this->estim = '';
		$this->rachat = '';
		$this->actif = '';
		$this->status = '';
	}

	/**
	 *
	 * @param unknown $lead
	 * @return number
	 */
	public function gettotalestim($lead) {
		$sql = "SELECT SUM(estim) AS total FROM " . MAIN_DB_PREFIX . "reprise WHERE fk_lead =" . $lead;
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			$this->db->free($resql);
			return $obj->total;
		} else {
			return 0;
		}
	}

	/**
	 *
	 * @param unknown $lead
	 * @return number
	 */
	public function gettotalrachat($lead) {
		$sql = "SELECT SUM(rachat) AS total FROM " . MAIN_DB_PREFIX . "reprise WHERE fk_lead =" . $lead;
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			$this->db->free($resql);
			return $obj->total;
		} else {
			return 0;
		}
	}

	public function getvente($rep,$product,$pro=0) {
		dol_include_once('/compta/facture/class/facture.class.php');
		$total = 0;
		$fac = new Facture($this->db);
		$sql = "SELECT fk_target FROM " . MAIN_DB_PREFIX . "element_element ";
		$sql.= "WHERE sourcetype = 'reprise' AND targettype = 'facture' AND fk_source = " . $rep;
		$resql = $this->db->query($sql);
		if ($resql) {
			while($obj = $this->db->fetch_object($resql)){
				$res = $fac->fetch($obj->fk_target);
				if($res>0 && $fac->statut !=3 && $fac->statut !=0 && $pro==0){
					$fac->fetch_lines();
					foreach ($fac->lines as $ligne){
						if ($ligne->fk_product == $product){
							$total += $ligne->total_ht;
						}
					}

				}elseif ($res>0 && $fac->statut ==0 && $pro==1){
					$fac->fetch_lines();
					foreach ($fac->lines as $ligne){
						if ($ligne->fk_product == $product){
							$total += $ligne->total_ht;
						}
					}
				}
			}
			$this->db->free($resql);
			return $total;
		} else {
			return 0;
		}
	}

	public function gettotalvente($lead,$product,$pro=0) {
		$total = 0;
		$sql = "SELECT rowid FROM " . MAIN_DB_PREFIX . "reprise WHERE fk_lead =" . $lead;
		$resql = $this->db->query($sql);
		if ($resql) {
			while($obj = $this->db->fetch_object($resql)){
				$total+= $this->getvente($obj->rowid,$product,$pro);
			}
			$this->db->free($resql);
			return $total;
		} else {
			return 0;
		}
	}


	public function getachat($rep,$product,$pro=0) {
		dol_include_once('/fourn/class/fournisseur.facture.class.php');
		$total = 0;
		$fac = new FactureFournisseur($this->db);
		$sql = "SELECT fk_target FROM " . MAIN_DB_PREFIX . "element_element ";
		$sql.= "WHERE sourcetype = 'reprise' AND targettype = 'invoice_supplier' AND fk_source = " . $rep;
		$resql = $this->db->query($sql);
		if ($resql) {
			while($obj = $this->db->fetch_object($resql)){
				$res = $fac->fetch($obj->fk_target);
				if($res>0 && $fac->statut !=3 && $fac->statut !=0 && $pro==0){
					$fac->fetch_lines();
					foreach ($fac->lines as $ligne){
						if ($ligne->fk_product == $product){
							$total += $ligne->total_ht;
						}
					}

				}elseif ($res>0 && $fac->statut ==0 && $pro==1){
					$fac->fetch_lines();
					foreach ($fac->lines as $ligne){
						if ($ligne->fk_product == $product){
							$total += $ligne->total_ht;
						}
					}
				}
			}
			$this->db->free($resql);
			return $total;
		} else {
			return 0;
		}
	}

	public function getachatlastdate($rep,$product) {
		$sql = "SELECT MAX(fac.datef) AS date_max FROM " . MAIN_DB_PREFIX . "element_element AS el ";
		$sql.= "INNER JOIN " . MAIN_DB_PREFIX . "facture_fourn AS fac ON fac.rowid = el.fk_target ";
		$sql.= "INNER JOIN " . MAIN_DB_PREFIX . "facture_fourn_det AS det ON fac.rowid = det.fk_facture_fourn ";
		$sql.= "WHERE sourcetype = 'reprise' AND targettype = 'invoice_supplier' AND fk_source = " . $rep . " AND det.fk_product = " . $product . " AND fac.fk_statut <> 0";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $obj->date_max;
			$this->db->free($resql);
		} else {
			return 0;
		}
	}

	public function getventelastdate($rep,$product) {
		$sql = "SELECT MAX(fac.datef) AS date_max FROM " . MAIN_DB_PREFIX . "element_element AS el ";
		$sql.= "INNER JOIN " . MAIN_DB_PREFIX . "facture AS fac ON fac.rowid = el.fk_target ";
		$sql.= "INNER JOIN " . MAIN_DB_PREFIX . "facturedet AS det ON fac.rowid = det.fk_facture ";
		$sql.= "WHERE sourcetype = 'reprise' AND targettype = 'facture' AND fk_source = " . $rep . " AND det.fk_product = " . $product . " AND fac.fk_statut <> 0";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $obj->date_max;
			$this->db->free($resql);
		} else {
			return 0;
		}
	}

	public function gettotalachat($lead,$product,$pro=0) {
		$total = 0;
		$sql = "SELECT rowid FROM " . MAIN_DB_PREFIX . "reprise WHERE fk_lead =" . $lead;
		$resql = $this->db->query($sql);
		if ($resql) {
			while($obj = $this->db->fetch_object($resql)){
				$total+= $this->getachat($obj->rowid,$product,$pro);
			}
			$this->db->free($resql);
			return $total;
		} else {
			return 0;
		}
	}


	/**
	 *
	 * @return string
	 */

	public function getstatus(){
		global $conf,$user;
		dol_include_once('/volvo/class/expertise.class.php');
		dol_include_once('/volvo/class/reception.class.php');

		$exp = new Expertise($this->db);
		$recep = new Reception($this->db);

		$exp1 =0;
		$estim = 0;
		$rachat = 0;
		$rec = 0;
		$actif = 0;
		$statut = 8;

		$res = $exp->fetchAll('', '', 0, 0, array(
				't.fk_reprise' => $this->id
		));
		//echo $this->id;
		//exit;
		if ($res >0) $exp1 = 1;

		if (!empty($this->estim)) $estim =1;
		if (!empty($this->rachat)) $rachat =1;

		$res = $recep->fetchbyrep($this->id);
		if ($res == 1) $rec = 1;

		if($this->getvente($this->id,17) > 0) $vente =1;
		$actif = $this->actif;

		if($exp1 == 1){
			if($estim == 1){
				if($rachat ==1){
					if($rec ==1){
						if($vente ==1){
							$statut = 7;
						}else{
							if($actif == 1){
								$statut = 5;
							}else{
								$statut = 6;
							}
						}
					}else{
						if($actif == 1){
							$statut = 4;
						}else{
							$statut = 3;
						}
					}
				}else{
					if($actif == 1){
						$statut = 2;
					}else{
						$statut = 3;
					}
				}
			}else{
			if($actif == 1){
				$statut = 1;
			}else{
				$statut = 3;
			}
			}
		}else{
			if($actif == 1){
				$statut = 0;
			}else{
				$statut = 3;
			}
		}
		if ($statut != $this->status){
			$this->status = $statut;
			$this->update($user);
		}
		return $statut;

	}

	function updatestatus($rep){
		global $conf,$user;
		$reprise = New Reprise($this->db);
		$reprise->fetch($rep);
		$reprise->status = $reprise->getstatus();
		$reprise->update($user);
		return $reprise->status;
	}

	public function fetchAllforlist($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND') {

		$sql = 'SELECT ';
		$sql.= 'r.rowid AS id,';
		$sql.= 'r.ref AS ref,';
		$sql.= 'r.fk_status AS status,';
		$sql.= 'r.fk_soc AS cd_ex_client,';
		$sql.= 's.nom AS ex_client,';
		$sql.= 'g.genre AS genre,';
		$sql.= 'm.marque AS marque,';
		$sql.= 'r.type AS type,';
		$sql.= 'sl.silouhette AS silouhette,';
		$sql.= 'r.puiscom AS puissance,';
		$sql.= 'c.cabine AS cabine,';
		$sql.= 'bv.bv AS bv,';
		$sql.= 'r.estim AS estim,';
		$sql.= 'r.rachat AS rachat,';
		$sql.= 'mt.moteur AS moteur,';
		$sql.= 'r.capago AS capago,';
		$sql.= 'eu.norme AS norme,';
		$sql.= 'r.kmrestit AS km,';
		$sql.= 'CONCAT( IF(2lve = 1,"2LVE - ",""), IF(2rct = 1,"2RCT - ",""), IF(gyro = 1,"Gyrophare - ",""), IF(echapv = 1,"Echappement Vertical - ",""), IF(adr = 1,"ADR - ",""), IF(hydro = 1,"Hydrolique - ",""), IF(compresseur = 1,"Compresseur - ",""), IF(climtoit = 1,"Clim de toit - ",""), IF(clim = 1,"Clim - ",""),IF(webasto = 1,"Webasto - ",""), IF(deflecteur = 1,"Deflecteurs - ",""), IF(jupes = 1,"Jupes", "") ) AS op,';
		$sql.= 'r.immat AS immat,';
		$sql.= 'r.numserie AS vin,';
		$sql.= 'r.1circ AS dt_1_circ,';
		$sql.= 'rec.date_reception AS date_reception,';
		$sql.= 'st.nom AS site,';
		$sql.= 'r.ptc AS ptc,';
		$sql.= 'DATEDIFF(NOW(), rec.date_reception) AS nbj_stock,';
		$sql.= 'ach.nom AS buyer,';
		$sql.= 'rec.buyer AS cd_buyer,';
		$sql.= 'pays.label AS pays_vente,';
		$sql.= 'typent.libelle AS type_ach,';
		$sql.= 'rec.fk_financeur AS financeur ';

		$sql.= 'FROM ' . MAIN_DB_PREFIX . 'reprise as r ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'societe as s ON  r.fk_soc=s.rowid ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_genre as g ON g.rowid = r.fk_genre ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_marques as m on m.rowid  = r.fk_marque ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_silouhette as sl on sl.rowid = r.fk_silouhette ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_cabine as c ON c.rowid = r.fk_cabine ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_bv as bv on bv.rowid = r.fk_bv ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_moteur as mt on mt.rowid = r.fk_moteur ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_normes as eu on eu.rowid = fk_norme ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'reception as rec on rec.fk_reprise = r.rowid ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_volvo_sites as st on st.rowid = rec.fk_site_actuel ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'societe as ach on ach.rowid = rec.buyer ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_country as pays on pays.rowid = ach.fk_pays ';
		$sql.= 'LEFT JOIN ' . MAIN_DB_PREFIX . 'c_typent as typent on typent.id = ach.fk_typent ';


		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ( $filter as $key => $value ) {
				if (($key == 'id') || ($key == 'cd_ex_client') || ($key == 'cd_buyer')) {
					$sqlwhere[] = $key . '=' . $value;
				} elseif (($key == 'puissance') || ($key == 'estim') || ($key == 'rachat') || ($key == 'capago') || ($key == 'km') || ($key == 'prix_achat')
						|| ($key == 'PTC') || ($key == 'nbj_stock') || ($key == 'surest') || ($key == 'cession') || ($key == 'frais_ext') || ($key == 'vd')
						 || ($key == 'fac_av') || ($key == 'prix_revient') || ($key == 'prix_vente') || ($key == 'margecom') || ($key == 'dt_1_circ') || ($key == 'date_reception')
						 || ($key == 'date_vente')){
					$sqlwhere[] = $key . ' BETWEEN ' . $value;
				} elseif (($key == 'status')){
						$sqlwhere[] = $key . ' IN ' . $value;
				} else {
					$sqlwhere[] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' HAVING ' . implode(' ' . $filtermode . ' ', $sqlwhere);
		}

		if (! empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (! empty($limit)) {
			$sql .= ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ( $obj = $this->db->fetch_object($resql) ) {
				$line = new RepriseLine($this->db);

				$pr = $this->getachat($obj->id,17);
				$pr+= $this->getachat($obj->id,17,1);
				$pr+= $this->getachat($obj->id,23);
				$pr+= $this->getachat($obj->id,23,1);
				$pr+= $this->getachat($obj->id,22);
				$pr+= $this->getachat($obj->id,22,1);
				$pr+= $this->getachat($obj->id,24);
				$pr-= $this->getvente($obj->id,21);
				$pr-= $this->getvente($obj->id,21,1);

				$marge = $this->getvente($obj->id,17);
				$marge+= $this->getvente($obj->id,17,1);
				$marge-= $pr;


				$line->id = $obj->id;
				$line->ref = $obj->ref;
				$line->updatestatus($obj->id);
				$line->status = $line->updatestatus($obj->id);
				$line->ex_client = $obj->ex_client;
				$line->cd_ex_client = $obj->cd_ex_client;
				$line->genre_label = $obj->genre;
				$line->marque_label = $obj->marque;
				$line->type = $obj->type;
				$line->silouhette_label=$obj->silouhette;
				$line->puiscom = $obj->puissance;
				$line->cabine_label=$obj->cabine;
				$line->bv_label=$obj->bv;
				$line->rachat = $obj->rachat;
				$line->estim = $obj->estim;
				$line->moteur = $obj->moteur;
				$line->capago = $obj->capago;
				$line->norme_label=$obj->norme_label;
				$line->kmrestit = $obj->km;
				$line->option_label=$obj->op;
				$line->immat = $obj->immat;
				$line->numserie = $obj->vin;
				$line->circ = $this->db->jdate($obj->dt_1_circ);
				$line->prix_achat = $this->getachat($obj->id,17);
				$line->date_entree = $this->db->jdate($obj->date_reception);
				$line->site_label = $obj->site;
				$line->nbj_stock = $obj->nbj_stock;
				$line->surest = $this->getachat($obj->id,24)+$this->getachat($obj->id,24,1);
				$line->cession = $this->getachat($obj->id,23);
				$line->frais_ext = $this->getachat($obj->id,22);
				$line->vd = $this->getvente($obj->id,21);
				$line->fac_av = $this->getachat($obj->id,23,1)+$this->getachat($obj->id,22,1)+ $this->getachat($obj->id,17,1)-$this->getvente($obj->id,21,1);
				$line->prix_revient = $pr;
				$line->prix_vente = $this->getvente($obj->id,17)+$this->getvente($obj->id,17,1);
				$line->margecom = $marge;
				$line->date_vente = $this->db->jdate($this->getventelastdate($obj->id,17));
				$line->cd_buyer = $obj->cd_buyer;
				$line->buyer = $obj->buyer;
				$line->dt_fac_ach = $this->db->jdate($this->getachatlastdate($obj->id,17));
				$line->pays_vente = $obj->pays_vente;
				$line->type_ach = $obj->type_ach;
				$line->financeur = $obj->financeur;

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);
			dol_syslog(__METHOD__ . ' ' . $sql, LOG_ERR);
			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}

	}
	function getsures(){
		global $conf,$user;
		if($this->status == 7 && !empty($this->rachat)){
			$sures = ($this->getvente($this->id,17) - $this->rachat);
		}elseif ($this->status != 3 && $this->status != 8 && !empty($this->rachat) && !empty($this->estim)){
			$sures = ($this->estim - $this->rachat);
		}else{
			$sures = '';
		}
		return $sures;
	}
	public function gettotalsures($lead) {
		$rep = new Reprise($this->db);
		$total = 0;
		$sql = "SELECT rowid FROM " . MAIN_DB_PREFIX . "reprise WHERE fk_lead =" . $lead;
		$resql = $this->db->query($sql);
		if ($resql) {
			while($obj = $this->db->fetch_object($resql)){
				$rep->fetch($obj->rowid);
				$total+= $rep->getsures($obj->rowid);
			}
			$this->db->free($resql);
			return $total;
		} else {
			return 0;
		}
	}
	public function listsellinvoices() {
		$sql = 'SELECT ';
		$sql.= 'fk_target as target ';
		$sql.= 'FROM ' . MAIN_DB_PREFIX . 'element_element ';
		$sql.= 'WHERE sourcetype="reprise" AND targettype="facture" ';
		$sql.= 'AND fk_source =' .$this->id;

		$resql = $this->db->query($sql);
		$reponse = array();
		if ($resql) {
			while($obj = $this->db->fetch_object($resql)){
				$reponse[]=$obj->target;
			}
			$this->db->free($resql);
		}
		return $reponse;

	}
	public function listbuyinvoices() {
		$sql = 'SELECT ';
		$sql.= 'fk_target as target ';
		$sql.= 'FROM ' . MAIN_DB_PREFIX . 'element_element ';
		$sql.= 'WHERE sourcetype="reprise" AND targettype="invoice_supplier" ';
		$sql.= 'AND fk_source =' .$this->id;

		$resql = $this->db->query($sql);
		$reponse = array();
		if ($resql) {
			while($obj = $this->db->fetch_object($resql)){
				$reponse[]=$obj->target;
			}
			$this->db->free($resql);
		}
		return $reponse;

	}
}

/**
 * Class RepriseLine
 */
class RepriseLine extends Reprise
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
	public $fk_lead;
	public $ref;
	public $police;
	public $fk_soc;
	public $date_entree = '';
	public $fk_restit;
	public $fk_marque;
	public $fk_genre;
	public $type;
	public $fk_silouhette;
	public $place;
	public $fk_norme;
	public $numserie;
	public $carrosserie;
	public $puisfisc;
	public $puiscom;
	public $ptc;
	public $pv;
	public $ptr;
	public $longutil;
	public $largutil;
	public $chargutil;
	public $immat;
	public $circ = '';
	public $kmact;
	public $kmrestit;
	public $validmine = '';
	public $validtachy = '';
	public $valid1;
	public $date1 = '';
	public $valid2;
	public $date2 = '';
	public $agrement;
	public $dateagrement = '';
	public $fk_cabine;
	public $fk_suspcabine;
	public $fk_moteur;
	public $fk_ralentisseur;
	public $fk_bv;
	public $rav;
	public $rar;
	public $sr;
	public $dr;
	public $blocage;
	public $rapport;
	public $fk_freinage;
	public $abs;
	public $asr;
	public $ebs;
	public $esp;
	public $dfr;
	public $suspav;
	public $suspar;
	public $fk_mav;
	public $fk_mar1;
	public $fk_mar2;
	public $fk_mar3;
	public $tav;
	public $tar1;
	public $tar2;
	public $tar3;
	public $dav;
	public $dar1;
	public $dar2;
	public $dar3;
	public $pav;
	public $par1;
	public $par2;
	public $par3;
	public $uav;
	public $uar1;
	public $uar2;
	public $uar3;
	public $nifissure;
	public $nisoude;
	public $etatmeca;
	public $pres;
	public $couchette;
	public $nbreserv;
	public $capago;
	public $adblue;
	public $lve;
	public $rct;
	public $gyro;
	public $echapv;
	public $adr;
	public $hydro;
	public $climtoit;
	public $webasto;
	public $clim;
	public $compresseur;
	public $deflecteur;
	public $jupes;
	public $copiecg;
	public $copiect;
	public $copieca;
	public $photos;
	public $estim;
	public $rachat;

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db) {
		parent::__construct($db);
	}

}
