<?php
Function Update_vh_info_from_suporder($orderid, $vin, $immat,$numom,$ctm,$note, $recursive=0, $origine=0){

	require_once(DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php');

	global $langs, $conf, $db;

	$cmd = New Commande($db);

	$sql = "SELECT fk_source FROM " . MAIN_DB_PREFIX . "element_element WHERE targettype = 'order_supplier' AND sourcetype = 'commande' AND fk_target = " . $orderid;
	$res = $db->query($sql);
	if ($res->num_rows > 0){
		while ($obj = $db->fetch_object($res)){
			if ($obj->fk_source != $origine){
				$rescmd = $cmd->fetch($obj->fk_source);
				if ($rescmd>0){
					$cmd->fetch_thirdparty();
					$cmd->array_options['options_vin'] =  $vin;
					$cmd->array_options['options_immat'] = $immat;
					$cmd->array_options['options_numom'] = $numom;
					$cmd->array_options['options_ctm'] = $ctm;
					$cmd->insertExtraFields();
					if(!empty($cmd->array_options['options_ctm'])){
						dol_include_once('/societe/class/societe.class.php');
						$socctm = New Societe($db);
						$socctm->fetch($cmd->array_options['options_ctm']);
						$note = 'Client: ' . $cmd->thirdparty->name . "\n";
						$note.= 'Contremarque: ' . $socctm->name . "\n";
						$note.= 'N° de Chassis :' . $vin . "\n";
						$note.= 'Immatriculation :' . $immat . "\n";
						$note.= 'Date de Livraison :' . dol_print_date($cmd->date_livraison, 'daytext');
					}else{
						$note = 'Client: ' . $cmd->thirdparty->name . "\n";
						$note.= 'N° de Chassis :' . $vin . "\n";
						$note.= 'Immatriculation :' . $immat . "\n";
						$note.= 'Date de Livraison :' . dol_print_date($cmd->date_livraison, 'daytext');
					}
					$cmd->update_note($note,'_public');
					if ($recursive == 1) Update_vh_info_from_custorder($cmd->id, $vin, $immat,$numom,$ctm,$note,0,0);
				}
			}
		}
	}
}




Function Update_vh_info_from_custorder($orderid, $vin, $immat,$numom, $ctm,$note, $recursive=0, $origine=0){

	require_once(DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php');

	global $langs, $conf, $db;

	$cmdfourn = New CommandeFournisseur($db);

	$sql = "SELECT fk_target FROM " . MAIN_DB_PREFIX . "element_element WHERE sourcetype = 'commande' AND targettype = 'order_supplier' AND fk_source = " . $orderid;
	$res = $db->query($sql);
	if ($res->num_rows > 0){
		while ($objfourn = $db->fetch_object($res)){
			if ($objfourn->fk_target != $origine){
				$rescmd = $cmdfourn->fetch($objfourn->fk_target,'');
				if ($rescmd>0){
					$cmdfourn->array_options['options_vin'] =  $vin;
					$cmdfourn->array_options['options_immat'] = $immat;
					$cmdfourn->array_options['options_numom'] = $numom;
					$cmdfourn->array_options['options_ctm'] = $ctm;
					$cmdfourn->insertExtraFields();
					$cmdfourn->update_note($note,'_public');
					if ($recursive == 1) Update_vh_info_from_suporder($cmdfourn->id, $vin, $immat,$numom,$ctm,$note,0,$origine);
				}
			}
		}
	}
}

function volvoAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("admin");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/volvo/admin/admin_volvo.php", 1);
	$head[$h][1] = 'administration Volvo';
	$head[$h][2] = 'settings';
	$h ++;

	$head[$h][0] = dol_buildpath("/volvo/admin/volvo_analyse.php", 1);
	$head[$h][1] = 'Modèle Analyse Volvo';
	$head[$h][2] = 'analyse';
	$h ++;

	$head[$h][0] = dol_buildpath("/volvo/admin/volvo_analyselg.php", 1);
	$head[$h][1] = 'Modèle Analyse Volvo long';
	$head[$h][2] = 'analyselg';
	$h ++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array(
	// 'entity:+tabname:Title:@lead:/lead/mypage.php?id=__ID__'
	// ); // to add new tab
	// $this->tabs = array(
	// 'entity:-tabname:Title:@lead:/lead/mypage.php?id=__ID__'
	// ); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'lead_admin');

	return $head;
}

Function print_extra($key,$type,$action,$extrafields,$object,$label=1,$lenght = 10,$unit=''){
	global $db;

	$out = '<div style="display: inline" align ="left">';

	if($label==1)$out.= $extrafields->attribute_label[$key];

	if($type=='yesno'){
		require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
		$form = new Form($db);
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';
			$out.= $form->selectyesno('options_'.$key,$object->array_options['options_'.$key],1);
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			$out.= '<span style="margin-left: 1em;">';
			$out.= yn($object->array_options['options_'.$key]);
			$out.= '</span><span style="margin-left: 1em;"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('') . '</a></span>';
		}
	}

	if($type=='chkbox'){
		require_once DOL_DOCUMENT_ROOT . '/volvo/class/html.formvolvo.class.php';
		dol_include_once('/volvo/class/reprise.class.php');
		$reprise = new Reprise($db);
		$form = new FormVolvo($db);
		$list = $extrafields->attribute_param[$key]['options'];
		$selected = explode(',', $object->array_options['options_'.$key]);
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';
			$out.= $form->select_withcheckbox_flat('options_'.$key,$list,$selected);
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			foreach ($list as $cle => $value){
				if(in_array($cle, $selected)) $out.= '<span style="margin-left: 1em;">' . $reprise->show_picto(1) . ' ' . $value .'</span>';
				else $out.= '<span style="margin-left: 1em;">' .$reprise->show_picto(0) . ' ' . $value.'</span>';
			}
			$out.= '<span style="margin-left: 1em;"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('') . '</a></span>';
		}
	}

	if($type=='bool'){
		if ($object->array_options['options_'.$key] == 0) {
			$out.= '<span style="margin-left: 1em;">'.'<a href="' . $_SERVER["PHP_SELF"] . '?action=update_extras&options_' .$key. '=1&attribute=' .$key . '&id=' . $object->id . '">';
			$out.= img_picto('non','switch_off');
			$out.= '</a></span>';
		} else {
			$out.= '<span style="margin-left: 1em;">'.'<a href="' . $_SERVER["PHP_SELF"] . '?action=update_extras&options_' .$key. '=0&attribute=' .$key . '&id=' . $object->id . '">';
			$out.= img_picto('Oui','switch_on');
			$out.= '</a></span>';
		}
	}

	if($type=='date'){
		require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
		$form = new Form($db);
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';
			$out.= $form->select_date($db->jdate($object->array_options['options_'.$key]),'options_'.$key,0,0,1,'',1,1,1);
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			$out.= '<span style="margin-left: 1em;">';
			$out.= dol_print_date($object->array_options['options_'.$key],'daytextshort').'</span>';
			$out.= '<span style="margin-left: 1em;"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('') . '</a></span>';
		}
	}

	if($type=='text'){
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';
			$out.= '<input type="text" name="options_' . $key . '" size="' . $lenght . '" value="' . $object->array_options['options_'.$key] . '"/>'. ' ' . $unit;
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			$out.= '<span style="margin-left: 1em;">';
			$out.= $object->array_options['options_'.$key] . ' ' . $unit;
			$out.= '</span>';
			$out.= '<span style="margin-left: 1em;"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('') . '</a></span>';
		}
	}

	if($type=='num'){
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';
			$out.= '<input type="text" name="options_' . $key . '" size="' . $lenght . '" value="' . price($object->array_options['options_'.$key]) . '"/>'. ' ' . $unit;
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			$out.= '<span style="margin-left: 1em;">';
			$out.= price($object->array_options['options_'.$key]). ' ' . $unit;
			$out.= '</span>';
			$out.= '<span style="margin-left: 1em;"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('') . '</a></span>';
		}
	}

	if($type=='textlong'){
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
			$doleditor=new DolEditor('options_'.$key,$object->array_options['options_'.$key]);
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';
			$out.= $doleditor->Create(1);
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			$out.= '<span style="margin-left: 1em;">';
			$out.= dol_htmlentitiesbr($object->array_options['options_'.$key]);
			$out.= '</span>';
			$out.= '<span style="margin-left: 1em;"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('') . '</a></span>';
		}
	}

	if($type=='chkboxvert'){
		require_once DOL_DOCUMENT_ROOT . '/volvo/class/html.formvolvo.class.php';
		dol_include_once('/volvo/class/reprise.class.php');
		$reprise = new Reprise($db);
		$form = new FormVolvo($db);
		$list = $extrafields->attribute_param[$key]['options'];
		$selected = explode(',', $object->array_options['options_'.$key]);
		$out.= '<table class="nobordernopadding" width="100%"><tr><td>';
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';
			$out.= $form->select_withcheckbox('options_'.$key,$list,$selected);
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			foreach ($list as $cle => $value){
				if(in_array($cle, $selected)) $out.= '<span style="margin-left: 1em;">' . $reprise->show_picto(1) . ' ' . $value .'</span></br>';
				else $out.= '<span style="margin-left: 1em;">' .$reprise->show_picto(0) . ' ' . $value.'</span></br>';
			}
			$out = substr($out, 0,-5);
			$out.= '</td><td>';
			$out.= '<span style="margin-left: 1em;"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('') . '</a></span>';
			$out.='</td></tr></table>';
		}
	}

	$out.= '</div>';


	return $out;
}

function commande_prepare_head(Commande $object)
{
	global $db, $langs, $conf, $user;
	if (! empty($conf->expedition->enabled)) $langs->load("sendings");
	$langs->load("orders");

	$h = 0;
	$head = array();

	if (! empty($conf->commande->enabled) && $user->rights->commande->lire)
	{
		$head[$h][0] = DOL_URL_ROOT.'/commande/card.php?id='.$object->id;
		$head[$h][1] = $langs->trans("OrderCard");
		$head[$h][2] = 'order';
		$h++;
	}

	$ok = volvo_vcm_ok($object);
	$img =img_picto('','on');
	if($ok>1 ||$ok<0) $img = img_picto('','off');
	$head[$h][0] = dol_buildpath('affaires/volvo/commande/vcm.php').'?id='.$object->id;
	$head[$h][1] = 'VCM' . ' <span class="badge">'.$img .'</span>' ;
	$head[$h][2] = 'vcm';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->commande->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/volvo/commande/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'documents';
	$h++;

	//complete_head_from_modules($conf,$langs,$object,$head,$h,'order');

	$head[$h][0] = DOL_URL_ROOT.'/volvo/commande/info.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'order','remove');

	return $head;
}

function volvo_vcm_ok($object) {
global $conf,$user;
	$res =  1;
	if(empty($object->array_options['options_vcm_site'])) $res = 2;
	if(empty($object->array_options['options_vcm_dt_dem'])) $res =  3;
	if(empty($object->array_options['options_vcm_duree'])) $res =  4;
	if(empty($object->array_options['options_vcm_km'])) $res =  5;
	if(empty($object->array_options['options_vcm_ptra'])) $res =  6;
	if(empty($object->array_options['options_vcm_chant']) && empty($object->array_options['options_vcm_50km'])
			&& empty($object->array_options['options_vcm_ld']) && empty($object->array_options['options_vcm_ville'])) $res =  7;
	if(empty($object->array_options['options_vcm_zone'])) $res =  8;
	if(empty($object->array_options['options_vcm_typ_trans'])) $res =  9;
	if(empty($object->array_options['options_vcm_roul'])) $res =  10;
	if(empty($object->array_options['options_vcm_topo'])) $res =  11;
	if(!empty($object->array_options['options_vcm_pto']) && empty($object->array_options['options_vcm_pto_nbh'])) $res =  12;
	if(!empty($object->array_options['options_vcm_frigo']) &&
			(!empty($object->array_options['options_vcm_blue']) || !empty($object->array_options['options_vcm_silver'])
			|| !empty($object->array_options['options_vcm_silverp']) || !empty($object->array_options['options_vcm_gold']))){
		if(empty($object->array_options['options_vcm_marque'])) $res =  13;
		if(empty($object->array_options['options_vcm_model'])) $res =  14;
		if(empty($object->array_options['options_vcm_fonct'])) $res =  15;
		if(empty($object->array_options['options_vcm_frigo_nbh'])) $res =  16;
	}

	if(($user->admin || $user->rights->volvo->update_cost || $conf->global->VOLVO_VCM_OBLIG == 0) && $res>1) return -1*$res;
	if($res == 1) return $res;
	if($res > 1) return $res;
}

function volvo_vcm_motif($code) {
	global $conf,$user;
	if($code<0) $code =-1*$code;

	$motif = 'Saisie Valide';


	switch($code){
		case 2:
			$motif = 'Point de service absent ou non valide';
			break;
		case 3:
			$motif = 'Date de début absente ou non valide';
			break;
		case 4:
			$motif = 'Durée absente ou non valide';
			break;
		case 5:
			$motif = 'Kilométrage annuel absent ou non valide';
			break;
		case 6:
			$motif = 'poid total roulant constaté absent ou non valide';
			break;
		case 7:
			$motif = 'paramètres de calcul du cycle de transport absent ou non valide';
			break;
		case 8:
			$motif = 'zone géographique absente ou non valide';
			break;
		case 9:
			$motif = 'type de transport absent ou non valide';
			break;
		case 10:
			$motif = 'condition de roulage absente ou non valide';
			break;
		case 11:
			$motif = 'topographie absente ou non valide';
			break;
		case 12:
			$motif = "PTO selectionnée, mais nombre d'heures annuelle d'utilisation absente ou non valide";
			break;
		case 13:
			$motif = 'Entretien groupe frigo sélectionné, mais marque du groupe absente ou non valide';
			break;
		case 14:
			$motif = 'Entretien groupe frigo sélectionné, mais Modèle du groupe absente ou non valide';
			break;
		case 15:
			$motif = 'Entretien groupe frigo sélectionné, mais mode de fonctionnement du groupe absente ou non valide';
			break;
		case 16:
			$motif = 'Entretien groupe frigo sélectionné, mais durée annuelle de fonctionnement absente ou non valide';
			break;
	}

	return $motif;
}

function show_picto_pdf($value) {
	if ($value == 1) {
		return img_picto('oui', 'statut6','',0,1);
	} else {
		return img_picto('non', 'statut0','',0,1);
	}
}

function chmod_r($Path,$mode) {
   $dp = opendir($Path);
   while($File = readdir($dp)) {
      if($File != "." AND $File != "..") {
         if(is_dir($File)){
            chmod($File, 0777);
         }else{
             chmod($Path."/".$File, 0777);
             if(is_dir($Path."/".$File)) {
                chmod_r($Path."/".$File,0777);
             }
         }
      }
   }
   closedir($dp);
}

function stat_sell1($year, $commercial,$monthlist,$mode='GROUP'){
	global $db;

	$sql = "SELECT  ";
	if($mode=='BY_REF'){
		$sql.="c.ref as ref, ";
		$sql.="c.rowid as id, ";
		$sql.="soc.nom as socname, ";
		$sql.="soc.rowid as socid, ";
	}
	$sql.= "MONTH(event.datep) as Mois, ";
	$sql.= "COUNT(DISTINCT c.rowid) as nb_facture, ";
	$sql.= "SUM(c.total_ht) AS catotalht, ";
	$sql.= "SUM(IF(lef.type = 1,1,0)) AS nbporteur, ";
	$sql.= "SUM(IF(lef.type = 2,1,0)) AS nbtracteur ";
	$sql.= "FROM " . MAIN_DB_PREFIX . "commande AS c ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm AS event ON event.fk_element = c.rowid AND event.elementtype = 'order' AND event.label LIKE '%Commande V% classée Facturée%' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS elm ON elm.fk_source = c.rowid AND elm.sourcetype ='commande' AND elm.targettype='lead' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead as l on elm.fk_target = l.rowid ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "societe as soc on soc.rowid = l.fk_soc ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead_extrafields lef on lef.fk_object = l.rowid ";
	$sql.= "WHERE YEAR(event.datep) ='" . $year . "' AND c.fk_statut > 0 ";
	if(!empty($monthlist)){
		$sql.= "AND MONTH(event.datep) IN (" . $monthlist . ") ";
	}
	if ($commercial > 0){
		$sql.= "AND l.fk_user_resp = '" . $commercial . "' ";
	}
	if($mode=='GROUP'){
		$sql.= "GROUP BY MONTH(event.datep) ";
	}elseif($mode=='BY_REF'){
		$sql.= "GROUP BY c.ref ";
	}

	$resql = $db->query($sql);
	if($resql){
		$result =array();
		while($obj = $db->fetch_object($resql)){
			if($mode=='GROUP'){
				$result[$obj->Mois]['nb_fact'] = $obj->nb_facture;
				$result[$obj->Mois]['catotalht'] = $obj->catotalht;
				$result[$obj->Mois]['nbporteur'] = $obj->nbporteur;
				$result[$obj->Mois]['nbtracteur'] = $obj->nbtracteur;
			}elseif($mode=='BY_REF'){
				$result[$obj->ref]['ref'] = $obj->ref;
				$result[$obj->ref]['socname'] = $obj->socname;
				$result[$obj->ref]['socid'] = $obj->socid;
				$result[$obj->ref]['id'] = $obj->id;
				$result[$obj->ref]['nb_fact'] = $obj->nb_facture;
				$result[$obj->ref]['catotalht'] = $obj->catotalht;
				$result[$obj->ref]['nbporteur'] = $obj->nbporteur;
				$result[$obj->ref]['nbtracteur'] = $obj->nbtracteur;
			}
		}
		return $result;
	}else{
		return -1;
	}

}

function stat_sell2($year, $commercial,$monthlist,$mode='GROUP'){
	global $db;

	dol_include_once('/volvo/class/lead.extend.class.php');
	$leadext = new Leadext($db);

	$soltrs = $leadext->prepare_array('VOLVO_VCM_LIST', 'sql');
	$soltrs.= $leadext->prepare_array('VOLVO_PACK_LIST', 'sql');

	$sql = "SELECT  ";
	if($mode=='BY_REF'){
		$sql.="c.ref as ref, ";
		$sql.="ef.vin as vin, ";
	}
	$sql.= "MONTH(event.datep) as Mois, ";
	$sql.= "SUM(IF(p.ref IN(" . $soltrs . "),1,0)) as vcm, ";
	$sql.= "SUM(IF(p.ref LIKE 'DFOL%',1,0)) as dfol, ";
	$sql.= "SUM(IF(p.ref = 'DDED',1,0)) as dded, ";
	$sql.= "SUM(IF(p.ref = 'MEM',1,0)) as mem, ";
	$sql.= "SUM(IF(p.ref = 'FIN_LIX',1,0)) as lixbail, ";
	$sql.= "SUM(IF(p.ref = 'FIN_VFS',1,0)) as vfs ";
	$sql.= "FROM " . MAIN_DB_PREFIX . "commande AS c ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm AS event ON event.fk_element = c.rowid AND event.elementtype = 'order' AND event.label LIKE '%Commande V% classée Facturée%' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS elm ON elm.fk_source = c.rowid AND elm.sourcetype ='commande' AND elm.targettype='lead' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead as l on elm.fk_target = l.rowid ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commandedet as det on c.rowid = det.fk_commande ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commande_extrafields as ef on c.rowid = ef.fk_object ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "product as p on p.rowid = det.fk_product ";
	$sql.= "WHERE YEAR(event.datep) ='" . $year . "'  AND c.fk_statut > 0 ";
	if(!empty($monthlist)){
		$sql.= "AND MONTH(event.datep) IN (" . $monthlist . ") ";
	}
	if ($commercial > 0){
		$sql.= "AND l.fk_user_resp = '" . $commercial . "' ";
	}
	if($mode=='GROUP'){
		$sql.= "GROUP BY MONTH(event.datep) ";
	}elseif($mode=='BY_REF'){
		$sql.= "GROUP BY c.ref ";
	}

	$resql = $db->query($sql);
	if($resql){
		$result =array();
		while($obj = $db->fetch_object($resql)){
			if($mode=='GROUP'){
				$result[$obj->Mois]['vcm'] = $obj->vcm;
				$result[$obj->Mois]['dfol'] = $obj->dfol;
				$result[$obj->Mois]['dded'] = $obj->dded;
				$result[$obj->Mois]['mem'] = $obj->mem;
				$result[$obj->Mois]['lixbail'] = $obj->lixbail;
				$result[$obj->Mois]['vfs'] = $obj->vfs;
			}elseif($mode=='BY_REF'){
				$result[$obj->ref]['vcm'] = $obj->vcm;
				$result[$obj->ref]['vin'] = $obj->vin;
				$result[$obj->ref]['dfol'] = $obj->dfol;
				$result[$obj->ref]['dded'] = $obj->dded;
				$result[$obj->ref]['mem'] = $obj->mem;
				$result[$obj->ref]['lixbail'] = $obj->lixbail;
				$result[$obj->ref]['vfs'] = $obj->vfs;
			}

		}
		return $result;
	}else{
		return -1;
	}

}

function stat_sell3($year, $commercial,$monthlist,$mode='GROUP'){
	global $db;

	dol_include_once('/volvo/class/lead.extend.class.php');
	$leadext = new Leadext($db);

	$soltrs1 = $leadext->prepare_array('VOLVO_VCM_LIST', 'array');
	$soltrs2= $leadext->prepare_array('VOLVO_PACK_LIST', 'array');
	$soltrs3= array('Volvo','LIVUSI','TPNEU');
	$soltrs =array_merge($soltrs1,$soltrs2,$soltrs3);

	$sql = "SELECT  ";
	if($mode=='BY_REF'){
		$sql.="c.ref as ref_aff, ";
	}
	$sql.= "MONTH(event.datep) as Mois, ";
	$sql.= "p.ref as ref, ";
	$sql.= "det.total_ht as total_ht ";
	$sql.= "FROM llx_commande AS c ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm AS event ON event.fk_element = c.rowid AND event.elementtype = 'order' AND event.label LIKE '%Commande V% classée Facturée%' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS elm ON elm.fk_source = c.rowid AND elm.sourcetype ='commande' AND elm.targettype='lead' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead as l on elm.fk_target = l.rowid ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commandedet as det on c.rowid = det.fk_commande ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "product as p on p.rowid = det.fk_product ";
	$sql.= "WHERE YEAR(event.datep) ='" . $year . "' AND p.ref IS NOT NULL  AND c.fk_statut > 0 ";
	if(!empty($monthlist)){
		$sql.= "AND MONTH(event.datep) IN (" . $monthlist . ") ";
	}
	if ($commercial > 0){
		$sql.= "AND l.fk_user_resp = '" . $commercial . "' ";
	}
	$sql.= "ORDER BY MONTH(event.datep) ";

	$resql = $db->query($sql);
	if($resql){
		$result =array();
		while($obj = $db->fetch_object($resql)){
			if(!in_array($obj->ref, $soltrs) && $mode=='GROUP'){
				$result[$obj->Mois]['cavolvo']+= $obj->total_ht;
			}Elseif(!in_array($obj->ref, $soltrs) && $mode=='BY_REF'){
				$result[$obj->ref_aff]['cavolvo']+= $obj->total_ht;
			}
		}
		return $result;
	}else{
		return $sql;
	}

}

function stat_sell4($year, $commercial,$monthlist,$mode='GROUP'){
	global $db;

	dol_include_once('/volvo/class/commandevolvo.class.php');
	$cmd = new CommandeVolvo($db);

	$sql = "SELECT  ";
	if($mode=='BY_REF'){
		$sql.="c.ref as ref, ";
	}
	$sql.= "MONTH(event.datep) as Mois, ";
	$sql.= "c.rowid as cmdid,  ";
	$sql.= "c.total_ht as total_ht ";
	$sql.= "FROM llx_commande AS c ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm AS event ON event.fk_element = c.rowid AND event.elementtype = 'order' AND event.label LIKE '%Commande V% classée Facturée%' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS elm ON elm.fk_source = c.rowid AND elm.sourcetype ='commande' AND elm.targettype='lead' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead as l on elm.fk_target = l.rowid ";
	$sql.= "WHERE YEAR(event.datep) ='" . $year . "'  AND c.fk_statut > 0 ";
	if(!empty($monthlist)){
		$sql.= "AND MONTH(event.datep) IN (" . $monthlist . ") ";
	}
	if ($commercial > 0){
		$sql.= "AND l.fk_user_resp = '" . $commercial . "' ";
	}
	$sql.= "ORDER BY MONTH(event.datep) ";

	$resql = $db->query($sql);
	if($resql){
		$result =array();
		while($obj = $db->fetch_object($resql)){
			if($mode=='GROUP'){
				$cmd->getCostPriceReal($obj->cmdid,'real');
				$result[$obj->Mois]['margereal']+= ($obj->total_ht-$cmd->total_real_paht);
				$cmd->getCostPriceReal($obj->cmdid,'theo');
				$result[$obj->Mois]['margetheo']+= ($obj->total_ht-$cmd->total_real_paht);
			}elseif($mode=='BY_REF'){
				$cmd->getCostPriceReal($obj->cmdid,'real');
				$result[$obj->ref]['margereal']+= ($obj->total_ht-$cmd->total_real_paht);
				$cmd->getCostPriceReal($obj->cmdid,'theo');
				$result[$obj->ref]['margetheo']+= ($obj->total_ht-$cmd->total_real_paht);
			}
		}
		return $result;
	}else{
		return $sql;
	}
}

function stat_sell5($year, $commercial,$monthlist,$mode='GROUP'){
	global $db;

	$sql = "SELECT  ";
	if($mode=='BY_REF'){
		$sql.="cmd.ref as ref, ";
		$sql.="cmd.rowid as id, ";
	}
	$sql.= "MONTH( IFNULL( ef.dt_liv_maj, c.date_livraison ) ) AS Mois, ";
	$sql.= "COUNT(DISTINCT c.rowid) as nb_port ";
	$sql.= "FROM " . MAIN_DB_PREFIX . "commande AS cmd ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS el ON el.fk_source = cmd.rowid AND el.targettype =  'order_supplier' AND el.sourcetype =  'commande' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commande_fournisseur AS c ON c.rowid = el.fk_target ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commande_fournisseur_extrafields as ef on ef.fk_object = c.rowid ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm AS event ON event.fk_element = cmd.rowid AND event.elementtype = 'order' AND
			event.label LIKE '%Commande V% classée Facturée%' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS elm ON elm.fk_source = cmd.rowid AND elm.targettype =  'lead' AND elm.sourcetype =  'commande' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead as l on elm.fk_target = l.rowid ";
	$sql.= "WHERE YEAR( IFNULL( ef.dt_liv_maj, c.date_livraison ) ) = " . $year . " AND c.fk_soc =32553 AND c.fk_statut >0 AND event.datep IS NULL ";

	if(!empty($monthlist)){
		$sql.= "AND MONTH( IFNULL( ef.dt_liv_maj, c.date_livraison ) ) IN (" . $monthlist . ") ";
	}
	if ($commercial > 0){
		$sql.= "AND l.fk_user_resp = '" . $commercial . "' ";
	}

	if($mode=='GROUP'){
		$sql.= "GROUP BY MONTH( IFNULL( ef.dt_liv_maj, c.date_livraison )) ";
	}elseif($mode=='BY_REF'){
		$sql.= "GROUP BY cmd.ref ";
	}




	$resql = $db->query($sql);
	if($resql){
		$result =array();
		while($obj = $db->fetch_object($resql)){
			if($mode=='GROUP'){
				$result[$obj->Mois]['nb_port'] = $obj->nb_port;
			}elseif($mode=='BY_REF'){
				$result[$obj->ref]['id'] = $obj->id;
			}
		}
		return $result;
	}else{
		return $sql;
	}

}
function stat_sell($filter=array()){
	$year = $filter['year'];
	$monthlist = $filter['monthlist'];
	$search_commercial = $filter['search_commercial'];

	$month =array(
			1=>Array('mois'=>'Janvier','moisn'=>1),
			2=>Array('mois'=>'Fevrier','moisn'=>2),
			3=>Array('mois'=>'Mars','moisn'=>3),
			4=>Array('mois'=>'Avril','moisn'=>4),
			5=>Array('mois'=>'Mai','moisn'=>5),
			6=>Array('mois'=>'Juin','moisn'=>6),
			7=>Array('mois'=>'Juillet','moisn'=>7),
			8=>Array('mois'=>'Aout','moisn'=>8),
			9=>Array('mois'=>'Septembre','moisn'=>9),
			10=>Array('mois'=>'Octobre','moisn'=>10),
			11=>Array('mois'=>'Novembre','moisn'=>11),
			12=>Array('mois'=>'Décembre','moisn'=>12)
	);
	$result = array();

	$arrayresult1 = stat_sell1($year, $search_commercial,$monthlist);
	$arrayresult2 = stat_sell2($year, $search_commercial,$monthlist);
	$arrayresult3 = stat_sell3($year, $search_commercial,$monthlist);
	$arrayresult4 = stat_sell4($year, $search_commercial,$monthlist);
	$arrayresult5 = stat_sell5($year, $search_commercial,$monthlist);
	$array = array_replace_recursive($month,$arrayresult1,$arrayresult2,$arrayresult3,$arrayresult4,$arrayresult5);

	return $array;
}


function stat_sell_ref($filter=array()){
	$year = $filter['year'];
	$monthlist = $filter['month'];
	$search_commercial = $filter['search_commercial'];
	$test= GETPOST('year');

	$result = array();

	$arrayresult1 = stat_sell1($year, $search_commercial,$monthlist,'BY_REF');
	$arrayresult2 = stat_sell2($year, $search_commercial,$monthlist,'BY_REF');
	$arrayresult3 = stat_sell3($year, $search_commercial,$monthlist,'BY_REF');
	$arrayresult4 = stat_sell4($year, $search_commercial,$monthlist,'BY_REF');
	$array = array_replace_recursive($arrayresult1,$arrayresult2,$arrayresult3,$arrayresult4);

	return $array;
}