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

Function print_extra($key,$type,$action,$extrafields,$object){
	require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
	$form = new Form($db);

	$out = '<table width="100%" class="nobordernopadding"><tr><td align ="left">';
	$out.= $extrafields->attribute_label[$key] . ': ';

	if($type='yesno'){
		if ($action == 'edit_extra' && GETPOST('attribute') == $key) {
			$out.= '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
			$out.= '<input type="hidden" name="action" value="update_extras">';
			$out.= '<input type="hidden" name="attribute" value="'. $key .'">';
			$out.= '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
			$out.= '<input type="hidden" name="id" value="' . $object->id . '">';;
			$out.= $form->selectyesno('options_'.$key,$object->array_options['options_'.$key],1);
			$out.= '<input type="submit" class="button" value="Modifier">';
			$out.= '</form>';
		} else {
			$out.= yn($object->array_options['options_'.$key]);
			$out.= '</td>';
			$out.= '<td align="center"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=' .$key . '&id=' . $object->id . '">' . img_edit('', 1) . '</a></td>';
		}
	}

	$out.= '</td>';
	$out.='</tr></table>';

	return $out;
}