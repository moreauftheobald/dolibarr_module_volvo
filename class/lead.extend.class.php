<?php
dol_include_once('/lead/class/lead.class.php');
class Leadext extends Lead
{
	public $interne = array();
	public $externe = array();
	public $obligatoire = array();
	public $divers = array();
	public $prixvente;
	public $commission;
	public $datelivprev;
	function __construct($db, $load_dict = 1) {
		global $conf, $user;

		$this->db = $db;

		if (! empty($load_dict)) {
			$result_status = $this->loadStatus();
			$result_type = $this->loadType();
		} else {
			$result_status = 1;
			$result_type = 1;
		}

		if (! empty($conf->propal->enabled)) {
			$this->listofreferent['propal'] = array(
					'title' => "Proposal",
					'class' => 'Propal',
					'table' => 'propal',
					'filter' => array(
							'fk_statut' => '0,1,2'
					),
					'test' => $conf->propal->enabled && $user->rights->propale->lire
			);
		}
		if (! empty($conf->facture->enabled)) {
			$this->listofreferent['invoice'] = array(
					'title' => "Bill",
					'class' => 'Facture',
					'table' => 'facture',
					'test' => $conf->facture->enabled && $user->rights->facture->lire
			);
		}
		if (! empty($conf->contrat->enabled)) {
			$this->listofreferent['contract'] = array(
					'title' => "Contrat",
					'class' => 'Contrat',
					'table' => 'contrat',
					'test' => $conf->contrat->enabled && $user->rights->contrat->lire
			);
		}
		if (! empty($conf->commande->enabled)) {
			$this->listofreferent['orders'] = array(
					'title' => "Commande",
					'class' => 'Commande',
					'table' => 'commande',
					'test' => $conf->commande->enabled && $user->rights->commande->lire
			);
		}

		return ($result_status && $result_type);
	}
	private function loadStatus() {
		global $langs;

		$sql = "SELECT rowid, code, label, active FROM " . MAIN_DB_PREFIX . "c_lead_status WHERE active=1";
		dol_syslog(get_class($this) . "::_load_status sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			while ( $obj = $this->db->fetch_object($resql) ) {

				$label = $langs->trans('LeadStatus_' . $obj->code);
				if ($label == 'LeadStatus_' . $obj->code) {
					$label = $obj->label;
				}

				$this->status[$obj->rowid] = $label;
			}
			$this->db->free($resql);

			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::_load_status " . $this->error, LOG_ERR);
			return - 1;
		}
	}

	/**
	 * Load type array
	 */
	private function loadType() {
		global $langs;

		$sql = "SELECT rowid, code, label FROM " . MAIN_DB_PREFIX . "c_lead_type  WHERE active=1";
		dol_syslog(get_class($this) . "::_load_type sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$label = $langs->trans('LeadType_' . $obj->code);
				if ($label == 'LeadType_' . $obj->code) {
					$label = $obj->label;
				}

				$this->type[$obj->rowid] = $label;
			}
			$this->db->free($resql);

			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::_load_type " . $this->error, LOG_ERR);
			return - 1;
		}
	}
	public function getnbchassisreal() {
		$nbchassis = 0;

		$sql = "SELECT SUM(det.qty) as totalchassis ";
		$sql .= "FROM " . MAIN_DB_PREFIX ."commande as cmd ";
		$sql .= "INNER JOIN " . MAIN_DB_PREFIX ."element_element as elmt ON  elmt.fk_source= cmd.rowid ";
		$sql .= "INNER JOIN " . MAIN_DB_PREFIX ."commandedet as det ON det.fk_commande = cmd.rowid ";
		$sql .= "WHERE  elmt.targettype='lead' ";
		$sql .= "AND elmt.sourcetype='commande' ";
		$sql .= "AND cmd.fk_statut <> 0 ";
		$sql .= "AND det.fk_product = 1 ";
		$sql .= "AND elmt.fk_target = " . $this->id;

		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);
				if (! empty($obj->totalchassis))
					$nbchassis = $obj->totalchassis;
			}
			$this->db->free($resql);
		} else {
			return - 1;
		}
		return $nbchassis;
	}



	public function getmargin($type) {
		dol_include_once('/volvo/class/commandevolvo.class.php');
		$margin = 0;

		$cmd = new CommandeVolvo($this->db);

		$sql = "SELECT cmd.rowid ";
		$sql .= "FROM " . MAIN_DB_PREFIX ."commande as cmd ";
		$sql .= "INNER JOIN " . MAIN_DB_PREFIX ."element_element as elmt ON  elmt.fk_source= cmd.rowid ";
		$sql .= "WHERE  elmt.targettype='lead' ";
		$sql .= "AND elmt.sourcetype='commande' ";
		$sql .= "AND cmd.fk_statut <> 0 ";
		$sql .= "AND elmt.fk_target = " . $this->id;

		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				While ( $obj = $this->db->fetch_object($resql) ) {
					$result=$cmd->getCostPriceReal($obj->rowid, $type);
					if ($resiult<0){
						$error++;
					}
					$margin += ($cmd->total_ht - $cmd->total_real_paht);
				}
				$this->db->free($resql);
			} else {
				return - 1;
			}
			return $margin;
		}
	}

	public function fetchAllfolow($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND') {
		dol_syslog(__METHOD__, LOG_DEBUG);

		$subsql1 = 'SELECT ';
		$subsql1.= 'MAX(fourn.ref) AS fourn, ';
		$subsql1.= 'cmd.rowid as cmd, ';
		$subsql1.= 'el2.fk_target as lead ';
		$subsql1.= 'FROM ' . MAIN_DB_PREFIX .'element_element AS el ';
		$subsql1.= 'LEFT JOIN ' . MAIN_DB_PREFIX .'commande_fournisseur as fourn ON el.fk_target = fourn.rowid AND el.targettype = "order_supplier" AND el.sourcetype = "commande" AND fourn.fk_soc = 32553 ';
		$subsql1.= 'LEFT JOIN ' . MAIN_DB_PREFIX .'commande as cmd ON el.fk_source = cmd.rowid ';
		$subsql1.= 'INNER JOIN ' . MAIN_DB_PREFIX .'element_element AS el2 ON el2.fk_source = cmd.rowid AND el2.targettype = "lead" AND el.sourcetype = "commande" ';
		$subsql1.= 'GROUP BY cmd.ref';

		$sql = "SELECT";
		$sql .= " lead.fk_soc AS societe,";
		$sql .= " soc.nom AS socnom,";
		$sql .= " lead.ref AS lead,";
		$sql .= " lead.rowid AS leadid,";
		$sql .= " com.ref AS com,";
		$sql .= " com.rowid AS comid,";
		$sql .= " cf.ref AS fourn,";
		$sql .= " cf.rowid AS fournid,";
		$sql .= " cf.fk_statut AS statut,";
		$sql .= " ef.vin AS vin,";
		$sql .= " ef.immat AS immat,";
		$sql .= " ef.numom AS numom,";
		$sql .= " genre.genre AS genre,";
		$sql .= " gamme.gamme AS gamme,";
		$sql .= " silouhette.silouhette AS silouhette,";
		$sql .= " com.date_valid AS dt_valid_ana,";
		$sql .= " cf.date_commande AS dt_env_usi,";
		$sql .= " ef.dt_blockupdate AS dt_blockupdate,";
		$sql .= " cf.date_livraison AS dt_liv_cons,";
		$sql .= " com.date_livraison AS dt_liv_dem_cli,";
		$sql .= " DATEDIFF(com.date_livraison,cf.date_livraison) AS delaiprep,";
		$sql .= " event6.datep AS dt_recep,";
		$sql .= " DATEDIFF(event6.datep,cf.date_livraison) AS retard_recept,";
		$sql .= " event4.datep AS dt_liv_cli,";
		$sql .= " DATEDIFF(event4.datep,com.date_livraison) AS retard_liv,";
		$sql .= " event3.datep AS dt_fac,";
		$sql .= " event5.datep AS dt_pay,";
		$sql .= " DATEDIFF(IFNULL(event5.datep,CURDATE()),event6.datep) AS delai_cash,";
		$sql .= " lead.fk_user_resp AS commercial,";
		$sql .= " CONCAT(u.firstname, ' ',u.lastname) AS comm,";
		$sql .= " IFNULL(ef.dt_liv_maj,cf.date_livraison) AS dt_sortie,";
		$sql .= " com.total_ht AS pv";

		$sql .= " FROM (" . $subsql1 . ") as idx";


		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "lead as lead on lead.rowid = idx.lead";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "lead_extrafields as lef on lead.rowid = lef.fk_object";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "c_volvo_genre as genre on lef.type = genre.rowid";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "c_volvo_gamme as gamme on lef.gamme = gamme.rowid";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "c_volvo_silouhette as silouhette on lef.silouhette = silouhette.rowid";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "user as u ON u.rowid = lead.fk_user_resp";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande as com ON com.rowid = idx.cmd and com.fk_statut > 0";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande_fournisseur as cf ON cf.ref = idx.fourn";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande_fournisseur_extrafields as ef on ef.fk_object = cf.rowid";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "societe as soc on lead.fk_soc = soc.rowid";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event2 on event2.fk_element = com.rowid AND event2.elementtype = 'order ' AND event2.label LIKE '%validée%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event3 on event3.fk_element = com.rowid AND event3.elementtype = 'order ' AND event3.label LIKE '%Facturée%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event4 on event4.fk_element = com.rowid AND event4.elementtype = 'order ' AND event4.label LIKE '%Livrée%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event5 on event5.fk_element = com.rowid AND event5.elementtype = 'order ' AND event5.label LIKE '%Payée%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event6 on event6.fk_element = cf.rowid AND event6.elementtype = 'order_supplier' AND event6.label LIKE '%reçue%'";


		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ( $filter as $key => $value ) {
				if (($key == 'com.date_valid') || ($key == 'com.date_livraison')|| ($key == 'event4.datep')|| ($key == 'event3.datep')|| ($key == 'event5.datep')|| ($key == 'cf.date_commande')|| ($key == 'event6.datep')|| ($key == 'ef.dt_blockupdate')|| ($key == 'cf.date_livraison')){
					$sqlwhere[] = $key . ' BETWEEN ' . $value;
				}elseif(($key== 'lead.fk_user_resp')||($key== 'com.rowid')) {
					$sqlwhere[] = $key . ' = ' . $value;
				}elseif(($key== 'delaiprep')||($key=='retard_recept')||($key=='retard_liv')||($key=='delai_cash')) {
					$sqlwhere[] = $key . ' BETWEEN ' . $value;
				}elseif(($key== 'search_run')) {
					$sqlwhere[] = '(event5.datep IS NULL OR event3.datep IS NULL OR event4.datep IS NULL)';
				}elseif(($key== 'MONTH_IN')) {
					$sqlwhere[] = 'MONTH(dt_sortie) IN (' . $value . ')';
				}elseif(($key== 'YEAR_IN')) {
					$sqlwhere[] = 'YEAR(dt_sortie) IN (' . $value . ')';
				}elseif(($key== 'PORT')) {
					$sqlwhere[] = '(cf.fk_statut>0 AND event3.datep IS NULL)';
				}else {
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
			$sql .= ' ' . $this->db->plimit($limit+1, $offset);
		}
		$this->business = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$compteur =1;
			while ( $obj = $this->db->fetch_object($resql) ) {
				$line = new Leadext($this->db);
				$line->societe = $obj->societe;
				$line->lead = $obj->leadid;
				$line->leadref = $obj->lead;
				$line->com = $obj->comid;
				$line->fournid = $obj->fournid;
				$line->numom = $obj->numom;
				$line->vin = $obj->vin;
				$line->immat = $obj->immat;
				$line->dt_valid_ana = $this->db->jdate($obj->dt_valid_ana);
				$line->dt_env_usi = $this->db->jdate($obj->dt_env_usi);
				$line->dt_blockupdate = $this->db->jdate($obj->dt_blockupdate);
				$line->dt_liv_cons = $this->db->jdate($obj->dt_liv_cons);
				$line->dt_liv_dem_cli = $this->db->jdate($obj->dt_liv_dem_cli);
				$line->delaiprep = $obj->delaiprep;
				$line->dt_recep = $this->db->jdate($obj->dt_recep);
				$line->retard_recept = $obj->retard_recept;
				$line->dt_liv_cli = $this->db->jdate($obj->dt_liv_cli);
				$line->retard_liv = $obj->retard_liv;
				$line->dt_fac = $this->db->jdate($obj->dt_fac);
				$line->dt_pay = $this->db->jdate($obj->dt_pay);
				$line->delai_cash = $obj->delai_cash;
				$line->commercial = $obj->commercial;
				$line->comm = $obj->comm;
				$line->dt_sortie = $obj->dt_sortie;
				$line->pv = $obj->pv;
				$line->commande = $obj->com;
				$line->socnom = $obj->socnom;
				$line->genre = $obj->genre;
				$line->gamme = $obj->gamme;
				$line->silouhette = $obj->silouhette;

				$this->business[$compteur] = $line;
				$compteur++;
			}
			$this->db->free($resql);
			dol_syslog(__METHOD__ . ' ' . $sql, LOG_ERR);
			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			$this->errors[] = 'Error ' . $sql;
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return $sql;
		}
	}

	public function fetchdelaicash($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND') {
		dol_syslog(__METHOD__, LOG_DEBUG);

		$subsql1 = 'SELECT ';
		$subsql1.= 'MAX(fourn.ref) AS fourn, ';
		$subsql1.= 'cmd.rowid as cmd, ';
		$subsql1.= 'el2.fk_target as lead ';
		$subsql1.= 'FROM ' . MAIN_DB_PREFIX .'element_element AS el ';
		$subsql1.= 'LEFT JOIN ' . MAIN_DB_PREFIX .'commande_fournisseur as fourn ON el.fk_target = fourn.rowid AND el.targettype = "order_supplier" AND el.sourcetype = "commande" AND fourn.fk_soc = 32553 ';
		$subsql1.= 'LEFT JOIN ' . MAIN_DB_PREFIX .'commande as cmd ON el.fk_source = cmd.rowid AND cmd.fk_statut > 0 ';
		$subsql1.= 'INNER JOIN ' . MAIN_DB_PREFIX .'element_element AS el2 ON el2.fk_source = cmd.rowid AND el2.targettype = "lead" AND el.sourcetype = "commande" ';
		$subsql1.= 'GROUP BY cmd.ref';

		$sql = "SELECT";
		$sql .= " lead.fk_soc AS societe,";
		$sql .= " soc.nom AS socnom,";
		$sql .= " lead.ref AS lead,";
		$sql .= " lead.rowid AS leadid,";
		$sql .= " com.ref AS com,";
		$sql .= " com.rowid AS comid,";
		$sql .= " cf.ref AS fourn,";
		$sql .= " cf.rowid AS fournid,";
		$sql .= " ef.vin AS vin,";
		$sql .= " ef.immat AS immat,";
		$sql .= " ef.numom AS numom,";
		$sql .= " ef.dt_blockupdate AS dt_blockupdate,";
		$sql .= " event6.datep AS dt_recep,";
		$sql .= " event4.datep AS dt_liv,";
		$sql .= " event3.datep AS dt_fac,";
		$sql .= " event5.datep AS dt_pay,";
		$sql .= " CONCAT(u.firstname, ' ',u.lastname) AS comm,";
		$sql .= " DATEDIFF(IFNULL(event5.datep,CURDATE()),event6.datep) AS delai_cash,";
		$sql .= " lead.fk_user_resp AS commercial,";
		$sql .= " payterm.libelle AS cond_reg,";
		$sql .= " DATE_ADD(event6.datep, INTERVAL payterm.nbjour DAY) AS date_lim_reg,";
		$sql .= " comef.comm_cash AS comm_cash,";
		$sql .= " payterm.nbjour AS cond_reg_num,";
		$sql .= " (payterm.nbjour - DATEDIFF(IFNULL(event5.datep,CURDATE()),event6.datep)) AS diff_cash";
		$sql .= " FROM (" . $subsql1 . ") as idx";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "lead as lead on lead.rowid = idx.lead";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "user as u ON u.rowid = lead.fk_user_resp";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande as com ON com.rowid = idx.cmd AND com.fk_statut > 0";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande_extrafields as comef ON com.rowid = comef.fk_object";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande_fournisseur as cf ON cf.ref = idx.fourn";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande_fournisseur_extrafields as ef on ef.fk_object = cf.rowid";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "societe as soc on lead.fk_soc = soc.rowid";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event3 on event3.fk_element = com.rowid AND event3.elementtype = 'order ' AND event3.label LIKE '%Facturée%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event4 on event4.fk_element = com.rowid AND event4.elementtype = 'order ' AND event4.label LIKE '%Livrée%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event5 on event5.fk_element = com.rowid AND event5.elementtype = 'order ' AND event5.label LIKE '%Payée%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm as event6 on event6.fk_element = cf.rowid AND event6.elementtype = 'order_supplier' AND event6.label LIKE '%reçue%'";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "c_payment_term as payterm on payterm.rowid = com.fk_cond_reglement";


		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ( $filter as $key => $value ) {
				if (($key == 'event3.datep')|| ($key == 'event5.datep')|| ($key == 'event6.datep')|| ($key == 'ef.dt_blockupdate')|| ($key == 'date_lim_reg')){
					$sqlwhere[] = $key . ' BETWEEN ' . $value;
				}elseif(($key== 'lead.fk_user_resp')||($key== 'com.rowid')) {
					$sqlwhere[] = $key . ' = ' . $value;
				}elseif(($key=='cond_reg')||($key=='delai_cash')||($key=='comm_cash')||($key=='diff_cash')||($key=='cond_reg_num')) {
					$sqlwhere[] = $key . ' BETWEEN ' . $value;
				}elseif(($key== 'search_run')) {
					$sqlwhere[] = '(event5.datep IS NULL OR event3.datep IS NULL OR event4.datep IS NULL)';
				}elseif(($key== 'dt_pay_isnull')) {
					$sqlwhere[] = 'dt_pay IS NULL';
				}else {
					$sqlwhere[] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
				}
			}
		}
		$sql .= ' HAVING event6.datep IS NOT NULL AND (event5.datep IS NULL OR (event5.datep >= DATE_ADD(CURDATE(), INTERVAL -7 DAY))) ';
		if (count($sqlwhere) > 0) {
			$sql .= ' AND (' . implode(' ' . $filtermode . ' ', $sqlwhere) .')';
		}

		if (! empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (! empty($limit)) {
			$sql .= ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->business = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$compteur =1;
			while ( $obj = $this->db->fetch_object($resql) ) {
				$line = new Leadext($this->db);
				$line->societe = $obj->societe;
				$line->socnom = $obj->socnom;
				$line->lead = $obj->leadid;
				$line->leadref = $obj->lead;
				$line->com = $obj->comid;
				$line->comref = $obj->com;
				$line->fournid = $obj->fournid;
				$line->numom = $obj->numom;
				$line->vin = $obj->vin;
				$line->immat = $obj->immat;
				$line->comm = $obj->comm;
				$line->dt_blockupdate = $this->db->jdate($obj->dt_blockupdate);
				$line->dt_recep = $this->db->jdate($obj->dt_recep);
				$line->dt_fac = $this->db->jdate($obj->dt_fac);
				$line->dt_pay = $this->db->jdate($obj->dt_pay);
				$line->delai_cash = $obj->delai_cash;
				$line->commercial = $obj->commercial;
				$line->cond_reg = $obj->cond_reg;
				$datetotest = $this->db->jdate($obj->date_lim_reg);
				$test = $this->num_public_holiday($datetotest, $datetotest,'FR',1);
				if($test==1){
					$datetotest = $datetotest -(24*60*60);
					while($this->num_public_holiday($datetotest,$datetotest,'FR',1)>0){
						$datetotest = $datetotest -(24*60*60);
					}
				}
				$line->date_lim_reg = $datetotest;
				$line->comm_cash = $obj->comm_cash;
				$line->diff_cash = $obj->diff_cash;
				$this->business[$compteur] = $line;
				$compteur++;
			}
			$this->db->free($resql);
			dol_syslog(__METHOD__ . ' ' . $sql, LOG_ERR);
			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			$this->errors[] = 'Error ' . $sql;
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}



	function fetchbyref($ref) {
		global $langs;
		$sql = "SELECT";
		$sql .= " t.rowid,";

		$sql .= " t.ref,";
		$sql .= " t.ref_ext,";
		$sql .= " t.ref_int,";
		$sql .= " t.fk_c_status,";
		$sql .= " t.fk_c_type,";
		$sql .= " t.fk_soc,";
		$sql .= " t.date_closure,";
		$sql .= " t.amount_prosp,";
		$sql .= " t.fk_user_resp,";
		$sql .= " t.description,";
		$sql .= " t.note_private,";
		$sql .= " t.note_public,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.datec,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.tms";

		$sql .= " FROM " . MAIN_DB_PREFIX . "lead as t";
		$sql .= " WHERE t.ref = '" . $ref;
		$sql .= "' AND t.entity IN (" . getEntity('lead', 1) . ")";

		dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->ref = $obj->ref;
				$this->ref_ext = $obj->ref_ext;
				$this->ref_int = $obj->ref_int;
				$this->fk_c_status = $obj->fk_c_status;
				$this->fk_c_type = $obj->fk_c_type;
				$this->fk_soc = $obj->fk_soc;
				$this->date_closure = $this->db->jdate($obj->date_closure);
				$this->amount_prosp = $obj->amount_prosp;
				$this->fk_user_resp = $obj->fk_user_resp;
				$this->description = $obj->description;
				$this->note_private = $obj->note_private;
				$this->note_public = $obj->note_public;
				$this->fk_user_author = $obj->fk_user_author;
				$this->datec = $this->db->jdate($obj->datec);
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->tms = $this->db->jdate($obj->tms);
				$this->status_label = $this->status[$this->fk_c_status];
				$this->type_label = $this->type[$this->fk_c_type];

				$extrafields = new ExtraFields($this->db);
				$extralabels = $extrafields->fetch_name_optionals_label($this->table_element, true);
				if (count($extralabels) > 0) {
					$this->fetch_optionals($this->id, $extralabels);
				}
			}
			$this->db->free($resql);

			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
			return - 1;
		}
	}

	public function gettotal_new_cli($leadref){
		$sql = "SELECT SUM(ef.comm_newclient) AS total ";
		$sql.= "FROM " . MAIN_DB_PREFIX . "commande as c ";
		$sql.= "INNER JOIN " . MAIN_DB_PREFIX . "commande_extrafields as ef ";
		$sql.= "ON ef.fk_object = c.rowid ";
		$sql.= "WHERE c.ref='" . $leadref . "'";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $obj->total;
		}else{
			return 0;
		}
	}

	public function contrat_needed($cmdid){
		$soltrs = $this->prepare_array('VOLVO_VCM_LIST', 'sql');
		$soltrs.= $this->prepare_array('VOLVO_PACK_LIST', 'sql');
		$sql = "SELECT IFNULL(COUNT(det.rowid),0) as nb_contrat ";
 		$sql.= "FROM " . MAIN_DB_PREFIX . "commandedet AS det ";
 		$sql.= "LEFT JOIN ".MAIN_DB_PREFIX . "product AS p ON p.rowid=det.fk_product ";
 		$sql.= "WHERE det.fk_commande =" . $cmdid . " ";
 		$sql.= "AND p.ref IN (" . $soltrs . ") ";
 		$resql = $this->db->query($sql);
 		if ($resql) {
 			$obj = $this->db->fetch_object($resql);
 			return $obj->nb_contrat;
 		}else{
 			return $sql;
 		}
	}

	
	public Function update_chaudes(){
		global $conf;

		$sql = "SELECT l.rowid ";
		$sql.= "FROM " . MAIN_DB_PREFIX . "lead AS l ";
		$sql.= "INNER JOIN ". MAIN_DB_PREFIX . 'lead_extrafields AS ef ON l.rowid = ef.fk_object ';
		$sql.= "WHERE l.fk_c_status NOT IN (6,7,11) ";
		$sql.= "AND ef.chaude = 1 ";

		$resql = $this->db->query($sql);

		if ($resql) {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$this->fetch($obj->rowid);
				$this->array_options['options_chaude'] = 0;
				$this->update;
				$this->output.= 'Mise a jour affaire ' . $obj->rowid . ' Set chaude =0' . "\n";
			}
			return 0;
		}else{
			return -1;
		}
	}

	function num_public_holiday($timestampStart, $timestampEnd, $countrycode='FR', $lastday=0)
	{
		dol_include_once('/core/lib/date.lib.php');
		$nbFerie = 0;

		// Check to ensure we use correct parameters
		if ((($timestampEnd - $timestampStart) % 86400) != 0) return 'ErrorDates must use same hours and must be GMT dates';

		$i=0;
		while (( ($lastday == 0 && $timestampStart < $timestampEnd) || ($lastday && $timestampStart <= $timestampEnd) )
				&& ($i < 50000))		// Loop end when equals (Test on i is a security loop to avoid infinite loop)
		{
			$ferie=false;
			$countryfound=0;

			$jour  = date("d", $timestampStart);
			$mois  = date("m", $timestampStart);
			$annee = date("Y", $timestampStart);
			if ($countrycode == 'FR')
			{
				$countryfound=1;

				// Definition des dates feriees fixes
				if($jour == 1 && $mois == 1)   $ferie=true; // 1er janvier
				if($jour == 1 && $mois == 5)   $ferie=true; // 1er mai
				if($jour == 8 && $mois == 5)   $ferie=true; // 5 mai
				if($jour == 14 && $mois == 7)  $ferie=true; // 14 juillet
				if($jour == 15 && $mois == 8)  $ferie=true; // 15 aout
				if($jour == 1 && $mois == 11)  $ferie=true; // 1 novembre
				if($jour == 11 && $mois == 11) $ferie=true; // 11 novembre
				if($jour == 25 && $mois == 12) $ferie=true; // 25 decembre

				// Calcul du jour de paques
				$date_paques = easter_date($annee);
				$jour_paques = date("d", $date_paques)+1;
				$mois_paques = date("m", $date_paques);
				if($jour_paques == $jour && $mois_paques == $mois) $ferie=true;
				// Paques

				// Calcul du jour de l ascension (38 jours apres Paques)
				$date_ascension = mktime(
						date("H", $date_paques),
						date("i", $date_paques),
						date("s", $date_paques),
						date("m", $date_paques),
						date("d", $date_paques) + 39,
						date("Y", $date_paques)
						);
				$jour_ascension = date("d", $date_ascension);
				$mois_ascension = date("m", $date_ascension);
				if($jour_ascension == $jour && $mois_ascension == $mois) $ferie=true;
				//Ascension

				// Calcul de Pentecote (11 jours apres Paques)
				$date_pentecote = mktime(
						date("H", $date_ascension),
						date("i", $date_ascension),
						date("s", $date_ascension),
						date("m", $date_ascension),
						date("d", $date_ascension) + 11,
						date("Y", $date_ascension)
						);
				$jour_pentecote = date("d", $date_pentecote);
				$mois_pentecote = date("m", $date_pentecote);
				if($jour_pentecote == $jour && $mois_pentecote == $mois) $ferie=true;
				//Pentecote

				// Calul des samedis et dimanches
				$jour_julien = unixtojd($timestampStart);
				$jour_semaine = jddayofweek($jour_julien, 0);
				if($jour_semaine == 0 || $jour_semaine == 6) $ferie=true;
				//Samedi (6) et dimanche (0)
			}

			// Pentecoste and Ascensione in Italy go to the sunday after: isn't holiday.
			// Pentecoste is 50 days after Easter, Ascensione 40
			if ($countrycode == 'IT')
			{
				$countryfound=1;

				// Definition des dates feriees fixes
				if($jour == 1 && $mois == 1) $ferie=true; // Capodanno
				if($jour == 6 && $mois == 1) $ferie=true; // Epifania
				if($jour == 25 && $mois == 4) $ferie=true; // Anniversario Liberazione
				if($jour == 1 && $mois == 5) $ferie=true; // Festa del Lavoro
				if($jour == 2 && $mois == 6) $ferie=true; // Festa della Repubblica
				if($jour == 15 && $mois == 8) $ferie=true; // Ferragosto
				if($jour == 1 && $mois == 11) $ferie=true; // Tutti i Santi
				if($jour == 8 && $mois == 12) $ferie=true; // Immacolata Concezione
				if($jour == 25 && $mois == 12) $ferie=true; // 25 decembre
				if($jour == 26 && $mois == 12) $ferie=true; // Santo Stefano

				// Calcul du jour de paques
				$date_paques = easter_date($annee);
				$jour_paques = date("d", $date_paques);
				$mois_paques = date("m", $date_paques);
				if($jour_paques == $jour && $mois_paques == $mois) $ferie=true;
				// Paques

				// Calul des samedis et dimanches
				$jour_julien = unixtojd($timestampStart);
				$jour_semaine = jddayofweek($jour_julien, 0);
				if($jour_semaine == 0 || $jour_semaine == 6) $ferie=true;
				//Samedi (6) et dimanche (0)
			}

			if ($countrycode == 'ES')
			{
				$countryfound=1;

				// Definition des dates feriees fixes
				if($jour == 1 && $mois == 1)   $ferie=true; // Año nuevo
				if($jour == 6 && $mois == 1)   $ferie=true; // Día Reyes
				if($jour == 1 && $mois == 5)   $ferie=true; // 1 Mayo
				if($jour == 15 && $mois == 8)  $ferie=true; // 15 Agosto
				if($jour == 12 && $mois == 10)  $ferie=true; // Día Hispanidad
				if($jour == 1 && $mois == 11)  $ferie=true; // 1 noviembre
				if($jour == 6 && $mois == 12) $ferie=true; // Constitución
				if($jour == 8 && $mois == 12)  $ferie=true; // Inmaculada
				if($jour == 25 && $mois == 12) $ferie=true; // 25 diciembre

				// Calcul día de Pascua
				$date_paques = easter_date($annee);
				$jour_paques = date("d", $date_paques);
				$mois_paques = date("m", $date_paques);
				if($jour_paques == $jour && $mois_paques == $mois) $ferie=true;
				// Paques

				// Viernes Santo
				$date_viernes = mktime(
						date("H", $date_paques),
						date("i", $date_paques),
						date("s", $date_paques),
						date("m", $date_paques),
						date("d", $date_paques) -2,
						date("Y", $date_paques)
						);
				$jour_viernes = date("d", $date_viernes);
				$mois_viernes = date("m", $date_viernes);
				if($jour_viernes == $jour && $mois_viernes == $mois) $ferie=true;
				//Viernes Santo

				// Calul des samedis et dimanches
				$jour_julien = unixtojd($timestampStart);
				$jour_semaine = jddayofweek($jour_julien, 0);
				if($jour_semaine == 0 || $jour_semaine == 6) $ferie=true;
				//Samedi (6) et dimanche (0)
			}

			// Cas pays non defini
			if (! $countryfound)
			{
				// Calul des samedis et dimanches
				$jour_julien = unixtojd($timestampStart);
				$jour_semaine = jddayofweek($jour_julien, 0);
				if($jour_semaine == 0 || $jour_semaine == 6) $ferie=true;
				//Samedi (6) et dimanche (0)
			}

			// On incremente compteur
			if ($ferie) $nbFerie++;

			// Increase number of days (on go up into loop)
			$timestampStart=dol_time_plus_duree($timestampStart, 1, 'd');


			$i++;
		}

		return $nbFerie;
	}
}




