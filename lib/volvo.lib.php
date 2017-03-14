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
	if($ok==0 ||$ok==-1) $img = img_picto('','off');
	$head[$h][0] = DOL_URL_ROOT.'/volvo/vcm/vcm.php?id='.$object->id;
	$head[$h][1] = 'VCM' . ' <span class="badge">'.$img. $ok . '</span>' ;
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
	//if($user->admin || $user->rights->volvo->update_cost || $conf->global->VOLVO_VCM_OBLIG == 0) return -1;
	if(empty($object->array_options['options_vcm_site'])) return 2;
	if(empty($object->array_options['options_vcm_dt_dem'])) return 3;
	if(empty($object->array_options['options_vcm_duree'])) return 4;
	if(empty($object->array_options['options_vcm_km'])) return 5;
	if(empty($object->array_options['options_vcm_ptra'])) return 6;
	if(empty($object->array_options['options_vcm_chant']) && empty($object->array_options['options_vcm_50km'])
			&& empty($object->array_options['options_vcm_ld']) && empty($object->array_options['options_vcm_ville'])) return 7;
	if(empty($object->array_options['options_vcm_zone'])) return 8;
	if(empty($object->array_options['options_vcm_typ_trans'])) return 9;
	if(empty($object->array_options['options_vcm_roul'])) return 10;
	if(empty($object->array_options['options_vcm_topo'])) return 11;
	if(!empty($object->array_options['options_vcm_pto']) && empty($object->array_options['options_vcm_pto_nbh'])) return 12;
	if(!empty($object->array_options['options_vcm_frigo']) &&
			(!empty($object->array_options['options_vcm_blue']) || !empty($object->array_options['options_vcm_silver'])
			|| !empty($object->array_options['options_vcm_silverp']) || !empty($object->array_options['options_vcm_gold']))){
		if(empty($object->array_options['options_vcm_marque'])) return 13;
		if(empty($object->array_options['options_vcm_model'])) return 14;
		if(empty($object->array_options['options_vcm_fonct'])) return 15;
		if(empty($object->array_options['options_vcm_frigo_nbh'])) return 16;
	}
	return 1;
}

function show_picto_pdf($value) {
	if ($value == 1) {
		return img_picto('non', 'statut6','',0,1);
	} else {
		return img_picto('non', 'statut0','',0,1);
	}
}