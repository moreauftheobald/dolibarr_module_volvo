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

	public function calcvhprice($cmdnum, $prixtot) {
		global  $conf;
		require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';

		$cmd = new Commande($this->db);
		$cmd->fetch($cmdnum);
		$cmd->fetch_lines(1);

		$cost = 0;
		$costvnc = 0;
		foreach ($cmd->lines as $line){
			if($line->fk_product !=$conf->global->VOLVO_TRUCK){
				if($line->fk_product == $conf->global->VOLVO_SURES){
					if($line->pa_ht>0) $costvnc+= $line->total_ht;
					$cost+=$line->total_ht;
				}elseif($line->fk_product == $conf->global->VOLVO_COM){
					$cost+=$line->total_ht;
				}else{
					$cost+=$line->total_ht;
					$costvnc+=$line->total_ht;
				}
			}
		}
		$ret=array();
		$ret['prixvh'] = $prixtot-$cost;
		$ret['vnc'] = $prixtot -$costvnc;
		return $ret;
	}

	public function updatevhpriceandvnc($cmdnum,$prixtot=0) {
		global $user,$conf;
		require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
		$cmd = new Commande($this->db);
		$cmd->fetch($cmdnum);
		$cmd->fetch_lines(1);
		if($prixtot==0){
			$prixtot = $cmd->total_ht;
		}

		$value = array();
		$value = $this->calcvhprice($cmdnum,$prixtot);
		foreach ($cmd->lines as $line){
			if($line->fk_product ==$conf->global->VOLVO_TRUCK){
				$cmd->updateline($line->id, $line->label, $value['prixvh'], $line->qty, $line->remise_percent, $line->tva_tx,0,0,'HT',0,'','',0,0,0,0,$value['prixvh']);
			}
		}

		$cmd->array_options['options_vnac']=$value['vnc'];
		$cmd->update_extrafields($user);
		$cmd->update_price();
	}

	public function createcmd() {
		global $conf;
		require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
		require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
		require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
		dol_include_once('/volvo/class/reprise.class.php');


		$extrafields = new Extrafields($this->db);
		$extrafields->fetch_name_optionals_label($this->table_element, true);
		$user = new user($this->db);
		$product = new product($this->db);


		$reprise = new Reprise($this->db);

		if ($this->getnbchassisreal() < $this->array_options['options_nbchassis']){
		$surestim = ($reprise->gettotalrachat($this->id) - $reprise->gettotalestim($this->id)) / $this->array_options['options_nbchassis'];
		} else {
			$surestim = 0;
		}

		$user->fetch($this->fk_user_resp);

		$cmd = new Commande($this->db);
		$cmd->socid = $this->thirdparty->id;
		$cmd->date = dol_now();
		$cmd->ref_client = $this->ref_int;
		$cmd->date_livraison = $this->datelivprev;
		$cmd->array_options['options_vnac'] = 0;
		$cmd->array_options['options_ctm'] = $this->array_options['options_ctm'];
		if(!empty($cmd->array_options['options_ctm'])){
			dol_include_once('/societe/class/societe.class.php');
			$socctm = new Societe($this->db);
			$socctm->fetch($cmd->array_options['options_ctm']);
			$cmd->note_public = 'Contremarque: ' . $socctm->name . "\n";
		}
		if ($this->array_options["options_type"] == 1) {
			$cmd->cond_reglement_id = 11;
		} elseif ($this->array_options["options_type"] == 2) {
			$cmd->cond_reglement_id = 9;
		} else {
			$cmd->cond_reglement_id = 10;
		}
		$rang =1;
		$line = New OrderLine($db);
		$line->subprice = 0;
		$line->qty = 1;
		$line->tva_tx = 0;
		$line->fk_product = 1;
		$line->pa_ht = 0;
		$line->rang=$rang;
		$rang++;
		$cmd->lines[] = $line;

		if (count($this->obligatoire) > 0) {
			foreach ( $this->obligatoire as $art ) {
				$product->fetch($art);
				$line = New OrderLine($db);
				$line->subprice = $product->price;
				$line->qty = 1;
				$line->tva_tx = 0;
				$line->fk_product = $product->id;
				$line->pa_ht = $product->cost_price;
				$line->rang=$rang;
				$rang++;
				$cmd->lines[] = $line;
			}
		}

		$line = New OrderLine($db);
		$line->desc = 'Sous-Total Véhicule';
		$line->subprice = 0;
		$line->qty = 99;
		$line->product_type = 9;
		$line->special_code = 104777;
		$line->rang=$rang;
		$rang++;
		$cmd->lines[] = $line;

		$line = New OrderLine($db);
		$line->desc = 'Travaux Interne';
		$line->subprice = 0;
		$line->qty = 1;
		$line->product_type = 9;
		$line->special_code = 104777;
		$line->rang=$rang;
		$rang++;
		$cmd->lines[] = $line;

		if (count($this->interne) > 0) {
			foreach ( $this->interne as $art ) {
				$product->fetch($art);
				$line = New OrderLine($db);
				$line->subprice = $product->price;
				$line->qty = 1;
				$line->tva_tx = 0;
				$line->fk_product = $product->id;
				$line->pa_ht = $product->cost_price;
				$line->rang=$rang;
				$rang++;
				$cmd->lines[] = $line;
			}
		}

		$line = New OrderLine($db);
		$line->desc = 'Sous-Total Travaux Interne';
		$line->subprice = 0;
		$line->qty = 99;
		$line->product_type = 9;
		$line->special_code = 104777;
		$line->rang=$rang;
		$rang++;
		$cmd->lines[] = $line;

		if (count($this->externe) > 0) {
			$line = New OrderLine($db);
			$line->desc = 'Travaux Externe';
			$line->subprice = 0;
			$line->qty = 1;
			$line->product_type = 9;
			$line->special_code = 104777;
			$line->rang=$rang;
			$rang++;
			$cmd->lines[] = $line;

			foreach ( $this->externe as $art ) {
				$product->fetch($art);
				$line = New OrderLine($db);
				$line->subprice = $product->price;
				$line->qty = 1;
				$line->tva_tx = 0;
				$line->fk_product = $product->id;
				$line->pa_ht = $product->cost_price;
				$line->rang=$rang;
				$rang++;
				$cmd->lines[] = $line;
			}
			$line = New OrderLine($db);
			$line->desc = 'Sous-Total Travaux Externe';
			$line->subprice = 0;
			$line->qty = 99;
			$line->product_type = 9;
			$line->special_code = 104777;
			$line->rang=$rang;
			$rang++;
			$cmd->lines[] = $line;
		}

		if (count($this->divers) > 0) {
			$line = New OrderLine($db);
			$line->desc = 'Divers';
			$line->subprice = 0;
			$line->qty = 1;
			$line->product_type = 9;
			$line->special_code = 104777;
			$line->rang=$rang;
			$rang++;
			$cmd->lines[] = $line;

			foreach ( $this->divers as $art ) {
				$product->fetch($art);
				$line = New OrderLine($db);
				$line->subprice = $product->price;
				$line->qty = 1;
				$line->tva_tx = 0;
				$line->fk_product = $product->id;
				$line->pa_ht = $product->cost_price;
				$line->rang=$rang;
				$rang++;
				$cmd->lines[] = $line;
			}
			$line = New OrderLine($db);
			$line->desc = 'Sous-Total Divers';
			$line->subprice = 0;
			$line->qty = 99;
			$line->product_type = 9;
			$line->special_code = 104777;
			$line->rang=$rang;
			$rang++;
			$cmd->lines[] = $line;
		}

		$num = $reprise->fetchAll('','',0,0,array('t.fk_lead'=>$this->id));
		if ($num > 0){
			$line = New OrderLine($db);
			$line->desc = 'Reprise VO';
			$line->subprice = 0;
			$line->qty = 1;
			$line->product_type = 9;
			$line->special_code = 104777;
			$line->rang=$rang;
			$rang++;
			$cmd->lines[] = $line;

			$line = New OrderLine($db);
			$line->subprice = $surestim;
			$line->qty = 1;
			$line->tva_tx = 0;
			$line->fk_product = $conf->global->VOLVO_SURES;
			$line->pa_ht = $surestim;
			$line->rang=$rang;
			$rang++;
			$cmd->lines[] = $line;

			$line = New OrderLine($db);
			$line->desc = 'Sous-Total Reprise VO';
			$line->subprice = 0;
			$line->qty = 99;
			$line->product_type = 9;
			$line->special_code = 104777;
			$line->rang=$rang;
			$rang++;
			$cmd->lines[] = $line;
		}

		$line = New OrderLine($db);
		$line->desc = 'Commission Volvo';
		$line->subprice = 0;
		$line->qty = 1;
		$line->product_type = 9;
		$line->special_code = 104777;
		$line->rang=$rang;
		$rang++;
		$cmd->lines[] = $line;

		$line = New OrderLine($db);
		$line->subprice = $this->commission;
		$line->qty = 1;
		$line->tva_tx = 0;
		$line->fk_product = $conf->global->VOLVO_COM;
		$line->pa_ht = 0;
		$line->rang=$rang;
		$rang++;
		$cmd->lines[] = $line;

		$line = New OrderLine($db);
		$line->desc = 'Sous-Total Commission Volvo';
		$line->subprice = 0;
		$line->qty = 99;
		$line->product_type = 9;
		$line->special_code = 104777;
		$line->rang=$rang;
		$rang++;
		$cmd->lines[] = $line;


		$result = $cmd->create($user);
		if ($result < 0) {
			$this->error = $cmd->error;
			$this->errors[] = $this->error;
			return $result;
		}

		$this->updatevhpriceandvnc($result,$this->prixvente);
		$this->add_object_linked("commande", $result);


		return $result;
	}

	public function getRealAmount2() {
		$totalinvoiceamount = 0;

		$sql = "SELECT SUM(cmd.total_ht) as totalamount ";
		$sql .= "FROM " . MAIN_DB_PREFIX ."commande as cmd ";
		$sql .= "INNER JOIN " . MAIN_DB_PREFIX ."element_element as elmt ON  elmt.fk_source= cmd.rowid ";
		$sql .= "WHERE  elmt.targettype='lead' ";
		$sql .= "AND elmt.sourcetype='commande' ";
		$sql .= "AND cmd.fk_statut <> 0 ";
		$sql .= "AND elmt.fk_target = " . $this->id;

		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);
				if (! empty($obj->totalamount))
					$totalinvoiceamount = $obj->totalamount;
			}
			$this->db->free($resql);
		} else {
			return - 1;
		}
		return $totalinvoiceamount;
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
		$sql .= " ef.vin AS vin,";
		$sql .= " ef.immat AS immat,";
		$sql .= " ef.numom AS numom,";
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
		$sql .= " lead.fk_user_resp AS commercial";

		$sql .= " FROM (" . $subsql1 . ") as idx";


		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "lead as lead on lead.rowid = idx.lead";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "user as u ON u.rowid = lead.fk_user_resp";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande as com ON com.rowid = idx.cmd";
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
				$line->lead = $obj->leadid;
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

	public function fetchdelaicash($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND') {
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
		$sql .= " ef.vin AS vin,";
		$sql .= " ef.immat AS immat,";
		$sql .= " ef.numom AS numom,";
		$sql .= " ef.dt_blockupdate AS dt_blockupdate,";
		$sql .= " event6.datep AS dt_recep,";
		$sql .= " event4.datep AS dt_liv,";
		$sql .= " event3.datep AS dt_fac,";
		$sql .= " event5.datep AS dt_pay,";
		$sql .= " DATEDIFF(IFNULL(event5.datep,CURDATE()),event6.datep) AS delai_cash,";
		$sql .= " lead.fk_user_resp AS commercial,";
		$sql .= " payterm.libelle AS cond_reg,";
		$sql .= " DATE_ADD(event6.datep, INTERVAL payterm.nbjour DAY) AS date_lim_reg,";
		$sql .= " comef.comm_cash AS comm_cash,";
		$sql .= " (payterm.nbjour - DATEDIFF(IFNULL(event5.datep,CURDATE()),event6.datep)) AS diff_cash";
		$sql .= " FROM (" . $subsql1 . ") as idx";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "lead as lead on lead.rowid = idx.lead";
		$sql .= " INNER JOIN " . MAIN_DB_PREFIX . "user as u ON u.rowid = lead.fk_user_resp";
		$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "commande as com ON com.rowid = idx.cmd";
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
				}elseif(($key=='cond_reg')||($key=='delai_cash')||($key=='comm_cash')||($key=='diff_cash')) {
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
		$sql .= ' HAVING event6.datep IS NOT NULL';
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

	public function find_dt_pay($cmdid){
		$sql = "SELECT MAX(datep) as dt_pay ";
		$sql.= "FROM " . MAIN_DB_PREFIX . "actioncomm ";
		$sql.= "WHERE fk_element =" . $cmdid . " ";
		$sql.= "AND elementtype = 'order' ";
		$sql.= "AND label LIKE '%Commande V% classée Payée%'";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $obj->dt_pay;
		}else{
			return 0;
		}
	}

	public function find_dt_liv($cmdid){

		$sql = "SELECT MAX(ac.datep) as dt_liv ";
		$sql.= "FROM " . MAIN_DB_PREFIX . "actioncomm AS ac ";
		$sql.= "INNER JOIN " . MAIN_DB_PREFIX . "commande_fournisseur as c ON c.rowid=ac.fk_element AND elementtype = 'order_supplier' ";
		$sql.= "WHERE c.source =" . $cmdid . " ";
		$sql.= "AND c.ref LIKE '%VTFRA%' ";
		$sql.= "AND ac.label LIKE '%Commande fournisseur VTFRA-% reçue%'";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $obj->dt_liv;
		}else{
			return 0;
		}
	}

	public function find_dt_ship($cmdid){
		$sql = "SELECT MAX(datep) as dt_ship ";
		$sql.= "FROM " . MAIN_DB_PREFIX . "actioncomm ";
		$sql.= "WHERE fk_element =" . $cmdid . " ";
		$sql.= "AND elementtype = 'order' ";
		$sql.= "AND label LIKE '%Commande V% classée Livrée%'";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $obj->dt_ship;
		}else{
			return 0;
		}
	}


	public function find_dt_bill($cmdid){
		$sql = "SELECT MAX(datep) as dt_bill ";
		$sql.= "FROM " . MAIN_DB_PREFIX . "actioncomm ";
		$sql.= "WHERE fk_element =" . $cmdid . " ";
		$sql.= "AND elementtype = 'order' ";
		$sql.= "AND label LIKE '%Commande V% classée Payée%'";
		$resql = $this->db->query($sql);
		if ($resql) {
			$obj = $this->db->fetch_object($resql);
			return $obj->dt_bill;
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

	public function calc_prime($cmdid) {
		dol_include_once('/volvo/class/commandevolvo.class.php');
		dol_include_once('/user/class/user.class.php');

		$cmd = new CommandeVolvo($this->db);
		$cmd->fetch($cmdid);
		$cmd->getCostPriceReal($cmd->id,'real');

		$this->fetchbyref($cmd->ref_client);

		$commercial = New User($this->db);
		$commercial->fetch($this->fk_user_resp);

		//calcul de la prime de base
		$cmd->array_options['options_comm'] = ($cmd->total_ht - $cmd->total_real_paht)*($commercial->array_options['options_ventevn']/100);

		//calcul de la prime Nouveau client
		if(!empty($this->array_options['options_new'])){
			$total = $this->gettotal_new_cli($this->ref);
			if($total == 0) $cmd->array_options['options_comm_newclient'] = $commercial->array_options['options_newclient'];
		}else{
			$cmd->array_options['options_comm_newclient'] = 0;
		}

		//calcul de la prime VCM et Pack
		$primevcm = 0;
		$primepack = 0;
		$listvcm=$this->prepare_array('VOLVO_VCM_LIST', 'array');
		$listpack = $this->prepare_array('VOLVO_PACK_LIST', 'array');
 		foreach ($cmd->lines as $line){
 			if(in_array($line->product_ref, $listvcm)){
 				$primevcm = 1;
 			}
 			if(in_array($line->product_ref, $listpack)){
 				$primepack = 1;
 			}
		}
		$cmd->array_options['options_comm_vcm'] = $primevcm*$commercial->array_options['options_primevcm'];
		$cmd->array_options['options_comm_pack'] = ($primepack * $commercial->array_options['options_primepack']);

		//calcul de la prime delai cash
		$dt_pay = $this->find_dt_pay($cmd->id);
		$dt_liv = $this->find_dt_liv($cmd->id);

		if(!empty($dt_liv) && !empty($dt_pay)){

			$type ='u';

			if ($this->array_options['options_type'] ==2) $type='t';
			if ($this->array_options['options_type'] ==1) $type='p';

			if ($this->array_options['options_ao']==1) $type.='ao';

			$new_dt_liv = New DateTime($dt_liv);
			$new_dt_pay = New DateTime($dt_pay);
			$delaiscash= date_diff($new_dt_pay, $new_dt_liv);
			$dc=$delaiscash->format('%a');

			//exit;
			foreach ($commercial->array_options as $key=>$value){
				$code = explode(_, $key);
				if ($code[1] =='dc' && $code[2] == $type && $code[4] >= $dc  && $dc >= $code[3]){
					$cmd->array_options['options_comm_cash'] = $value;
				}
			}
		}else{
			$cmd->array_options['options_comm_cash'] = 0;
		}
		$cmd->insertExtraFields();
	}

	public Function update_all_prime(){
		global $conf;

		$sql = "SELECT rowid, ref ";
		$sql.= "FROM " . MAIN_DB_PREFIX . "commande ";
		$sql.= "WHERE (date_cloture >= DATE_ADD(NOW(),INTERVAL -";
		$sql.= $conf->global->VOLVO_LOCK_DELAI + 6;
		$sql.= " MONTH) ";
		$sql.= "OR date_cloture IS NULL) ";
		$sql.= "AND fk_statut > 0 ";
		$sql.= "ORDER BY rowid";

		$resql = $this->db->query($sql);

		if ($resql) {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$this->output.= 'Mise a jour prime de la commande ' . $obj->ref . "\n";
				$this->calc_prime($obj->rowid);
			}
			return 0;
		}else{
			$this->output.= $sql;
			$this->output.= $this->db->lasterror;
			return -1;
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

	public Function prepare_array($var,$mode){
		global $conf;

		if($mode == 'sql'){
			$outtemp = explode(',',$conf->global->$var);
			foreach ($outtemp as $value){
				$out.= "'" . $value ."',";
			}
			$out = substr($out, 0,-1);
		}elseif($mode ='array'){
			$out = explode(',',$conf->global->$var);
		}
		return $out;

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
				var_dump($jour_pentecote);
				var_dump($mois_pentecote);
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
			//var_dump($jour.' '.$mois.' '.$annee.' '.$timestampStart);

			$i++;
		}

		return $nbFerie;
	}
}
