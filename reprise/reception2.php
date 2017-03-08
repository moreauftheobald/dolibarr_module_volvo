<?php
$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

dol_include_once('/volvo/class/reprise.class.php');
dol_include_once('/volvo/class/reception.class.php');
dol_include_once('/volvo/class/expertise.class.php');
dol_include_once('/volvo/lib/reprise.lib.php');
dol_include_once('/core/class/html.form.class.php');
dol_include_once('/core/class/doleditor.class.php');
dol_include_once('/core/lib/files.lib.php');
dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
dol_include_once('/societe/class/societe.class.php');
dol_include_once('/fourn/class/fournisseur.facture.class.php');
dol_include_once('/compta/facture/class/facture.class.php');


$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$confirm = GETPOST('confirm', 'alpha');
$exp = GETPOST('exp', 'int');
$buyer = GETPOST('buyer', 'int');
$selprice = GETPOST('selprice','int');
$desc = GETPOST('desc','alpha');

$form = new Form($db);

$reprise = new Reprise($db);

$expertise = new Expertise($db);

$reprise->fetch($id);
$reprise->fetch_thirdparty();
$reprise->updatestatus($reprise->id);

$reception = new Reception($db);

$upload_dir = $conf->volvo->dir_output .'/'. dol_sanitizeFileName($reprise->ref);

if ($action == 'update_valeur' && $user->rights->volvo->admin){
	$reprise->rachat = GETPOST('rachat', 'int');
	$reprise->update($user);
}

if ($action == 'update_estim' && $user->rights->volvo->admin){
	$reprise->estim = GETPOST('estim', 'int');
	$reprise->update($user);
}

if ($action == 'update_compta' && $user->rights->volvo->facture){
	$res = $reception->fetchbyrep($id);
	$reception->fk_financeur = GETPOST('financeur', 'int');
	$res = $reception->update();
}


$img0 = img_picto('non','statut0');
$img1 = img_picto('non','statut6');

if ($action == 'create_reception' && $user->rights->volvo->modif_rec){
	foreach($reception->value_array as $value){
		if (isset($_POST[$value])){
			$reception->$value = GETPOST($value);
		}
	}
	$reception->fk_reprise = $id;
	if(isset($_POST['date_reception_'])){$reception->date_reception = dol_mktime(0, 0, 0, GETPOST('date_reception_month'), GETPOST('date_reception_day'), GETPOST('date_reception_year'));}
	$res = $reception->create();
	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&action=');
}

if ($action == 'confirm_delete' && $confirm == 'yes' && $user->admin) {
	$res = $reception->delete();
 	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $id.'&action=');
}

if ($action == 'update_reception' && $user->rights->volvo->modif_rec){
	$res = $reception->fetchbyrep($id);
	foreach($reception->value_array as $value){
		if (isset($_POST[$value])){
			$reception->$value = GETPOST($value);
		}
	}
	$reception->fk_reprise = $id;
	if(isset($_POST['date_reception_'])){$reception->date_reception = dol_mktime(0, 0, 0, GETPOST('date_reception_month'), GETPOST('date_reception_day'), GETPOST('date_reception_year'));}
	$res = $reception->update();
	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $id.'&action=');
}

if ($action == 'sell_conf' && $confirm == 'yes' && !empty($buyer) && !empty($selprice) && $user->rights->volvo->facture){
	$res = $reception->fetchbyrep($id);
	$reception->buyer = $buyer;
	$res = $reception->update();
	$fac = new Facture($db);
	if (!empty($reception->fk_financeur) && $reception->fk_financeur>0){
		$fac->socid = $reception->fk_financeur;
		$cli = New Societe($db);
		$cli->fetch($buyer);
		$comm2 = "\n";
		$comm2.= 'Livraison faite chez: '."\n";
		$comm2.= $cli->name . "\n";
		$comm2.= dol_format_address($cli,1,"\n");
	}else{
		$fac->socid = $buyer;
	}
	$fac->fetch_thirdparty();
	$fac->array_options['options_reprise'] = $reprise->getNomUrl2(1,1,'reception2');
	$fac->date_creation = dol_now();
	$fac->date = dol_now();
	$fac->cond_reglement_id = 1;
	$tva_tx = get_default_tva($mysoc, $fac->thirdparty, 17);
	if($selprice>0){
		$fac->type = 0;
	}else{
		$fac->type = 2;
	}

	if($tva_tx == 0){
		$fac->note_public = 'Livraison Intracommunautaire pour CEE ' . $fac->thirdparty->country . "\n" . 'EXONERE DE TVA - ART 262 TER-1 DU CGI' . "\n" . "N° ASSUJETTI: " . $fac->thirdparty->tva_intra;
	}

	$res = $fac->create($user);
	$fac->add_object_linked('reprise', $id);

	$comm = 'Numéro de série: ' . $reprise->numserie . "\n";
	$comm.= 'Immatricualation: ' . $reprise->immat . "\n";
	$comm.= 'Genre: ' . $reprise->genre[$reprise->fk_genre] . "\n";
	$comm.= 'Marque: ' . $reprise->marque[$reprise->fk_marque] . "\n";
	$comm.= 'Type: ' . $reprise->type . "\n";
	$comm.= 'Date de 1ere mise en circulation: ' .  dol_print_date($reprise->circ, 'daytext') . "\n";
	$comm.= 'Kilométrage: ' . $reception->km . "\n";
	$comm.= 'PTAC: ' . $reprise->ptc .' - Poids a vide: ' . $reprise->pv . ' - PTR: ' . $reprise->ptr . "\n";
	$comm.= "VENDU DANS L'ETAT SANS GARANTIE" . "\n";
	$comm.= $comm2;

	$fac->addline($comm, $selprice, 1, $tva_tx,'','',17,0,'','','','','','HT','',1,1,'','reprise',$id);

	if($res !=-1){
 		header('Location:' . DOL_URL_ROOT . "/compta/facture.php?id=" . $res);
 	}
}

if ($action == 'selldiv_conf' && $confirm == 'yes' && !empty($buyer) && !empty($selprice) && !empty($desc) && $user->rights->volvo->facture){
	$fac = new Facture($db);
	$fac->socid = $buyer;
	$fac->fetch_thirdparty();
	$fac->array_options['options_reprise'] = $reprise->getNomUrl2(1,1,'reception2');
	$fac->date_creation = dol_now();
	$fac->date = dol_now();
	$fac->cond_reglement_id = 1;
	$tva_tx = get_default_tva($mysoc, $fac->thirdparty, 21);
	if($selprice>0){
		$fac->type = 0;
	}else{
		$fac->type = 2;
	}

	$res = $fac->create($user);
	$fac->add_object_linked('reprise', $id);

	$fac->addline($desc, $selprice, 1, $tva_tx,'','',21,0,'','','','','','HT','',1,1,'','reprise',$id);

	if($res !=-1){
		header('Location:' . DOL_URL_ROOT . "/compta/facture.php?id=" . $res);
	}
}

if ($action == 'achext_conf' && $confirm == 'yes' && !empty($buyer) && !empty($selprice) && !empty($desc) && $user->rights->volvo->facture){
	$fac = new FactureFournisseur($db);
	$fac->socid = $buyer;
	$fac->fetch_thirdparty();
	$fac->array_options['options_reprise'] = $reprise->getNomUrl2(1,1,'reception2');
	$fac->libelle = 'Facture externe';
	$fac->datec = dol_now();
	$fac->date = dol_now();
	$fac->cond_reglement_id = 1;
	$tva_tx = get_default_tva($mysoc, $fac->thirdparty, 22);

	$res = $fac->create($user);
	$fac->add_object_linked('reprise', $id);

	$fac->addline($desc, $selprice, $tva_tx,'','',1,22,0,'','','','','HT');

	if($res !=-1){
		header('Location:' . DOL_URL_ROOT . "/fourn/facture/card.php?id=" . $res);
	}
}

if ($action == 'cession_conf' && $confirm == 'yes' && !empty($buyer) && !empty($selprice) && !empty($desc) && $user->rights->volvo->facture){
	$fac = new FactureFournisseur($db);
	$fac->socid = $buyer;
	$fac->array_options['options_reprise'] = $reprise->getNomUrl2(1,1,'reception2');
	$fac->libelle = 'Cession Interne';
	$fac->fetch_thirdparty();
	$fac->datec = dol_now();
	$fac->date = dol_now();
	$fac->cond_reglement_id = 1;
	$tva_tx = get_default_tva($mysoc, $fac->thirdparty, 23);

	$res = $fac->create($user);
	$fac->add_object_linked('reprise', $id);

	$fac->addline($desc, $selprice, $tva_tx,'','',1,23,0,'','','','','HT');

	if($res !=-1){
		header('Location:' . DOL_URL_ROOT . "/fourn/facture/card.php?id=" . $res);
	}
}

if ($action == 'buyvo_conf' && $confirm == 'yes' && !empty($buyer) && !empty($selprice) && $user->rights->volvo->facture){
	$fac = new FactureFournisseur($db);
	$fac->socid = $buyer;
	$fac->ref_supplier = $buyer . '-' . $reprise->ref;
	$fac->array_options['options_reprise'] = $reprise->getNomUrl2(1,1,'reception2');
	$fac->libelle = 'Achat VO';
	$fac->fetch_thirdparty();
	$fac->datec = dol_now();
	$fac->date = dol_now();
	$fac->cond_reglement_id = 1;
	$tva_tx = get_default_tva($mysoc, $fac->thirdparty, 17);

	$res = $fac->create($user);
	$fac->add_object_linked('reprise', $id);

	$comm = 'Numéro de série: ' . $reprise->numserie . "\n";
	$comm.= 'Immatricualation: ' . $reprise->immat . "\n";
	$comm.= 'Genre: ' . $reprise->genre[$reprise->fk_genre] . "\n";
	$comm.= 'Marque: ' . $reprise->marque[$reprise->fk_marque] . "\n";
	$comm.= 'Type: ' . $reprise->type . "\n";
	$comm.= 'Date de 1ere mise en circulation: ' .  dol_print_date($reprise->circ, 'daytext') . "\n";
	$comm.= 'Kilométrage: ' . $reception->km . "\n";
	$comm.= 'PTAC: ' . $reprise->ptc .' - Poids a vide: ' . $reprise->pv . ' - PTR: ' . $reprise->ptr . "\n";

	$fac->addline($comm, $selprice, $tva_tx,'','',1,17,0,'','','','','HT');

	if($res !=-1){
		header('Location:' . DOL_URL_ROOT . "/fourn/facture/card.php?id=" . $res);
	}
}

if ($action == 'depre_conf' && $confirm == 'yes' && !empty($selprice) && $user->rights->volvo->facture){
	$fac = new FactureFournisseur($db);
	$fac->socid = 33699;
	$fac->array_options['options_reprise'] = $reprise->getNomUrl2(1,1,'reception2');
	$fac->libelle = 'Dépréciation execptionelle';
	$fac->fetch_thirdparty();
	$fac->datec = dol_now();
	$fac->date = dol_now();
	$fac->cond_reglement_id = 1;
	$tva_tx = get_default_tva($mysoc, $fac->thirdparty, 24);

	$res = $fac->create($user);
	$fac->add_object_linked('reprise', $id);

	$comm = 'Numéro de série: ' . $reprise->numserie . "\n";
	$comm.= 'Immatricualation: ' . $reprise->immat . "\n";
	$comm.= 'Date de 1ere mise en circulation: ' .  dol_print_date($reprise->circ, 'daytext') . "\n";

	$fac->addline($comm, $selprice, $tva_tx,'','',1,24,0,'','','','','HT');

	if($res !=-1){
		header('Location:' . DOL_URL_ROOT . "/fourn/facture/card.php?id=" . $res);
	}
}

include_once DOL_DOCUMENT_ROOT . '/volvo/template/document_actions_pre_headers2.tpl.php';

$res = $reception->fetchbyrep($id);
if ($res === 0){
	$action = 'create';
}

llxHeader('', 'reprise');
$form = new Form($db);

$expertise->fetchAll('','',0,0,array('t.fk_reprise'=>$id));
$head = info_prepare_head2($expertise->liste_exp,$id);
dol_fiche_head($head, 'recep', 'reprise', 0, dol_buildpath('/volvo/img/object_iron02.png', 1), 1);

if ($action == 'create' && $user->rights->volvo->modif_rec){
print '<form name="createrecep" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="create_reception">';
	print '<input type="hidden" name="fk_reprise" value="' . $id . '">';
	print '<TABLE width="100%">';
		print '<TR>';
			print '<TD valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR><TD width="30%" rowspan="2"><img src="' . dol_buildpath('/volvo/img/logo theobald.png',2) . '" height="60" width="142"></TD><TD><H1>FICHE DE RECEPTION VEHICULE OCCASION</H1></TD></TR>';
				print '</TABLE>';
				print '</br>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD>';
				print '<TABLE width="100%"  class="border">';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Synthèse de la réception</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD width="50%"><b>Date de réception : </b>'  . $form->select_date('', 'date_reception_',0,0,1,'',1,1,1,0,'','','') .  '</TD>';
						print '<TD width="50%"><b>site de réception : </b>' . $reprise->sites[$reprise->fk_restit] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD><b>Kilométrage a la réception : </b><input type="text" name="km" size="20" value=""/> km</TD>';
						print '<TD><b>Réceptionnaire : </b>' . $form->select_dolusers('','fk_receptionnaire',1) . '</TD>';
					print '</TR>';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Commentaire sur l' ."'" . 'état général du véhicule</TD>';
					print '</TR>';
					print '<TR><TD colspan="2">';
						$doleditor = new DolEditor('comm_etat','' , '', 100, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
						$doleditor->Create();
						print '</TD></TR>';
					print '<TR>';
						print '<TD><b>Etat de réception conforme a la derniere expertise ? </b><INPUT type="checkbox" name="etat_conforme" value="1"/></TD>';
						print '<TD><b>Site actuel : </b>'. $form->selectarray('fk_site_actuel',$reprise->sites,'',1) .'</TD>';
					print '</TR>';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Présentation du Produit</TD>';
					print '</TR>';
					print '<TR><TD colspan="2">';
						$doleditor2 = new DolEditor('presentation_produit','' , '', 100, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
						$doleditor2->Create();
					print '</TD></TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
	print '</TABLE>';

	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
	print '</form>';

} elseif ($action =='edit' && $user->rights->volvo->modif_rec){

	print '<form name="updaterecep" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_reception">';
	print '<input type="hidden" name="fk_reprise" value="' . $id . '">';
	print '<TABLE width="100%">';
		print '<TR>';
			print '<TD valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR><TD width="30%" rowspan="2"><img src="' . dol_buildpath('/volvo/img/logo theobald.png',2) . '" height="60" width="142"></TD><TD><H1>FICHE DE RECEPTION VEHICULE OCCASION</H1></TD></TR>';
				print '</TABLE>';
				print '</br>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD>';
				print '<TABLE width="100%"  class="border">';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Synthèse de la réception</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD width="50%"><b>Date de réception : </b>'  . $form->select_date($reception->date_reception, 'date_reception_',0,0,1,'',1,1,1,0,'','','') .  '</TD>';
						print '<TD width="50%"><b>site de réception : </b>' . $reprise->sites[$reprise->fk_restit] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD><b>Kilométrage a la réception : </b><input type="text" name="km" size="20" value="' . $reception->km . '"/> km</TD>';
						print '<TD><b>Réceptionnaire : </b>' . $form->select_dolusers($reception->fk_receptionnaire,'fk_receptionnaire',1) . '</TD>';
					print '</TR>';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Commentaire sur l' ."'" . 'état général du véhicule</TD>';
					print '</TR>';
					print '<TR><TD colspan="2">';
						$doleditor = new DolEditor('comm_etat',$reception->comm_etat , '', 100, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
						$doleditor->Create();
						print '</TD></TR>';
					print '<TR>';
						if($reception->etat_conforme==1){$ck = ' checked="checked"';}
						print '<TD><b>Etat de réception conforme a la derniere expertise ? </b><INPUT type="checkbox" name="etat_conforme" value="1"' . $chk . '/></TD>';
						print '<TD><b>Site actuel : </b>'. $form->selectarray('fk_site_actuel',$reprise->sites,$reception->fk_site_actuel,1) .'</TD>';
					print '</TR>';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Présentation du Produit</TD>';
					print '</TR>';
					print '<TR><TD colspan="2">';
						$doleditor2 = new DolEditor('presentation_produit',$reception->presentation_produit , '', 100, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
						$doleditor2->Create();
					print '</TD></TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
	print '</TABLE>';

	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
	print '</form>';


} elseif($user->rights->volvo->lireeprise) {
	$formconfirm = '';
	if ($action == 'suppr' && $user->admin) {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id , 'Supprimer', 'Supprimer la Réception ?', 'confirm_delete', '', 0, 1);
	}

	if ($action == 'sell' && $user->rights->volvo->facture) {
			$formquestion = array(
				array(
				'type'=>'other',
				'name'=>'buyer',
				'label' => 'Acheteur',
				'value'=> $form->select_thirdparty_list('','buyer')
				),
 				Array(
 				'type'=>'text',
				'name' => 'selprice',
				'label' => 'Prix de Vente HT'
 				)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'Facturer', '', 'sell_conf', $formquestion, 0, 1);

	}

	if ($action == 'selldiv' && $user->rights->volvo->facture) {
		$formquestion = array(
				array(
						'type'=>'other',
						'name'=>'buyer',
						'label' => 'Client Facturé',
						'value'=> $form->select_thirdparty_list('','buyer')
				),
				Array(
						'type'=>'text',
						'name' => 'selprice',
						'label' => 'Prix de Vente HT'
						),
				Array(
						'type'=>'text',
						'name' => 'desc',
						'label' => 'Nature de la Facturation'
						)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'Facture Diverse', '', 'selldiv_conf', $formquestion, 0, 1);

	}

	if ($action == 'achext' && $user->rights->volvo->facture) {
		$formquestion = array(
				array(
						'type'=>'other',
						'name'=>'buyer',
						'label' => 'fournisseur',
						'value'=> $form->select_thirdparty_list('','buyer','s.fournisseur=1')
				),
				Array(
						'type'=>'text',
						'name' => 'selprice',
						'label' => "Prix d'achat HT"
						),
				Array(
						'type'=>'text',
						'name' => 'desc',
						'label' => "Nature des travaux"
						)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'Achats Externes', '', 'achext_conf', $formquestion, 0, 1);

	}


	if ($action == 'cession' && $user->rights->volvo->facture) {
		$formquestion = array(
				array(
						'type'=>'other',
						'name'=>'buyer',
						'label' => 'fournisseur',
						'value'=> $form->select_thirdparty_list('','buyer','s.fournisseur=1')
				),
				Array(
						'type'=>'text',
						'name' => 'selprice',
						'label' => "Prix d'achat HT"
						),
				Array(
						'type'=>'text',
						'name' => 'desc',
						'label' => "Nature des travaux"
						)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'cession interne', '', 'cession_conf', $formquestion, 0, 1);

	}

	if ($action == 'buyvo' && $user->rights->volvo->facture) {
		$formquestion = array(
				array(
						'type'=>'other',
						'name'=>'buyer',
						'label' => 'Vendeur du VO',
						'value'=> $form->select_thirdparty_list($reprise->fk_soc,'buyer')
				),
				Array(
						'type'=>'text',
						'name' => 'selprice',
						'label' => "Prix d'achat HT",
						'value' => $reprise->rachat
						)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'Facturer', '', 'buyvo_conf', $formquestion, 0, 1);

	}

	if ($action == 'depre' && $user->rights->volvo->facture) {
		$formquestion = array(
				Array(
						'type'=>'text',
						'name' => 'selprice',
						'label' => "Montant HT de dépréciation",
						)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'Facturer', '', 'depre_conf', $formquestion, 0, 1);

	}

	print $formconfirm;
	$formconfirm = '';
	print '<TABLE width="100%">';
		print '<TR>';
			print '<TD valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR><TD width="30%" rowspan="2"><img src="' . dol_buildpath('/volvo/img/logo theobald.png',2) . '" height="60" width="142"></TD><TD><H1>FICHE DE RECEPTION VEHICULE OCCASION</H1></TD></TR>';
				print '</TABLE>';
				print '</br>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD>';
				print '<TABLE width="100%"  class="border">';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Synthèse de la réception</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD width="50%"><b>Date de réception : </b>' . dol_print_date($reception->date_reception, 'daytext') . '</TD>';
						print '<TD width="50%"><b>site de réception : </b>' . $reprise->sites[$reprise->fk_restit] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD><b>Kilométrage a la réception : </b>' . price($reception->km) . ' km</TD>';
						$userstatic = new User($db);
						$result = $userstatic->fetch($reception->fk_receptionnaire);
						print '<TD><b>Réceptionnaire : </b>' . $userstatic->getFullName($langs) . '</TD>';
					print '</TR>';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Commentaire sur l' ."'" . 'état général du véhicule</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD colspan="2">' . $reception->comm_etat . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD><b>Etat de réception conforme a la derniere expertise ? </b>' . $reprise->show_picto($reception->etat_conforme) . '</TD>';
						print '<TD><b>Site actuel : </b>' . $reprise->sites[$reception->fk_site_actuel] . '</TD>';
					print '</TR>';
					print '<TR class="liste_titre">';
						print '<TD colspan="2" align="center">Présentation du Produit</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD colspan="2">' . $reception->presentation_produit . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
	print '</TABLE>';
	print '<div class="tabsAction">';
	if($user->rights->volvo->modif_rec){
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&exp=' . $exp . '&action=edit">' . 'Modifier' . "</a></div>\n";
	}
	if ($user->admin) {
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&exp=' . $exp . '&action=suppr">' . 'Supprimer' . "</a></div>\n";
	}
	print '</div>';

}

$action = GETPOST('action');
$fina = new Societe($db);
$resfina = $fina->fetch($reception->fk_financeur);
$ach = new Societe($db);
$resbuy = $ach->fetch($reception->buyer);

if ($action=='edit_compta' && $user->rights->facture){
	$sell = $reprise->listsellinvoices();
	print '<form name="edit_compta" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_compta">';
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="6">Bilan comptable de la reprise</td></tr>';
	print '<tr>';
	print '<td colspan ="3" width="50%">Organisme Finaceur: ' . $form->select_thirdparty_list($reception->fk_financeur,'financeur','fk_typent=235',1) . ' <input type="submit" class="button" value="' . $langs->trans('Modify') . '"></td>';
	print '<td colspan ="3" width="50%">Acheteur du V.O.: ' . ($resbuy==1?$ach->getNomUrl(1):'') . '</td>';
	print '</tr>';
	print '<tr class="liste_titre"><td align="center" colspan="6">Mouvements comptables</td></tr>';
	print '<tr class="liste_titre">';
	print '<td align="center"width="16%">Date</td>';
	print '<td align="center" width="17%">Référence</td>';
	print '<td align="center" width="17%">Type</td>';
	print '<td align="center" width="26%">Tiers</td>';
	print '<td align="center" width="12%">Montant</td>';
	print '<td align="center" width="12%">statut</td>';
	print '</tr>';
	foreach($sell as $invoice){
		$inv = new Facture($db);
		$res=$inv->fetch($invoice);
		if ($res){
			$inv->fetch_thirdparty();
			print '<tr>';
			print '<td>' . dol_print_date($inv->date, 'daytext') . '</td>';
			print '<td>' . $inv->getNomUrl(1) . '</td>';
			print '<td>' . $inv->getLibType() . '</td>';
			print '<td>' . $inv->thirdparty->getNomUrl(1) . '</td>';
			print '<td>' . price($inv->total_ht) .  $langs->getCurrencySymbol($conf->currency) . '</td>';
			print '<td>' .$inv->getLibStatut(2);
		}
	}
	foreach($buy as $invoice){
		$inv = new FactureFournisseur($db);
		$res=$inv->fetch($invoice);
		if ($res){
			$inv->fetch_thirdparty();
			print '<tr>';
			print '<td>' . dol_print_date($inv->date, 'daytext') . '</td>';
			print '<td>' . $inv->getNomUrl(1) . '</td>';
			print '<td>' . $inv->libelle . '</td>';
			print '<td>' . $inv->thirdparty->getNomUrl(1) . '</td>';
			print '<td>' . price($inv->total_ht) .  $langs->getCurrencySymbol($conf->currency) . '</td>';
			print '<td>' .$inv->getLibStatut(2);
		}
	}
	print '</table>';
	print '</form>';

} elseif($user->rights->volvo->facture) {
	$sell = $reprise->listsellinvoices();
	$buy = $reprise->listbuyinvoices();
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="6">Bilan comptable de la reprise</td></tr>';
	print '<tr>';
	print '<td colspan ="3" width="50%">Organisme Finaceur: ' . ($resfina==1?$fina->getNomUrl(1):'') . '<a href="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&action=edit_compta">' . img_edit('Modifier', 1) . '</a></td>';
	print '<td colspan ="3" width="50%">Acheteur du V.O.: ' . ($resbuy==1?$ach->getNomUrl(1):'') . '</td>';
	print '</tr>';
	print '<tr class="liste_titre"><td align="center" colspan="6">Mouvements comptables</td></tr>';
	print '<tr class="liste_titre">';
	print '<td align="center"width="16%">Date</td>';
	print '<td align="center" width="17%">Référence</td>';
	print '<td align="center" width="17%">Type</td>';
	print '<td align="center" width="26%">Tiers</td>';
	print '<td align="center" width="12%">Montant</td>';
	print '<td align="center" width="12%">statut</td>';
	print '</tr>';
	foreach($sell as $invoice){
		$inv = new Facture($db);
		$res=$inv->fetch($invoice);
		if ($res){
			$inv->fetch_thirdparty();
			print '<tr>';
			print '<td>' . dol_print_date($inv->date, 'daytext') . '</td>';
			print '<td>' . $inv->getNomUrl(1) . '</td>';
			print '<td>' . $inv->getLibType() . '</td>';
			print '<td>' . $inv->thirdparty->getNomUrl(1) . '</td>';
			print '<td>' . price($inv->total_ht) .  $langs->getCurrencySymbol($conf->currency) . '</td>';
			print '<td>' .$inv->getLibStatut(2);
		}
	}
	foreach($buy as $invoice){
		$inv = new FactureFournisseur($db);
		$res=$inv->fetch($invoice);
		if ($res){
			$inv->fetch_thirdparty();
			print '<tr>';
			print '<td>' . dol_print_date($inv->date, 'daytext') . '</td>';
			print '<td>' . $inv->getNomUrl(1) . '</td>';
			print '<td>' . $inv->libelle . '</td>';
			print '<td>' . $inv->thirdparty->getNomUrl(1) . '</td>';
			print '<td>' . price($inv->total_ht) .  $langs->getCurrencySymbol($conf->currency) . '</td>';
			print '<td>' .$inv->getLibStatut(2);
		}
	}
	print '</table>';
	print '<div class="tabsAction">';
	if ($user->rights->volvo->facture) {
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=sell">' . 'Facturer le véhicule' . "</a></div>\n";
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=selldiv">' . 'Saisir une Facture Diverse' . "</a></div>\n";
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=achext">' . 'Saisir une Facture de travaux externes' . "</a></div>\n";
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=cession">' . 'Saisir une cession Interne' . "</a></div>\n";
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=buyvo">' . "Saisir une Facture d'achat de VO" . "</a></div>\n";
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=depre">' . 'Saisir une dépréciation exeptionelle' . "</a></div>\n";
	}
	print '</div>';
}

if ($user->rights->volvo->facture){
	$pr = $reprise->getachat($id,17);
	$pr+= $reprise->getachat($id,17,1);
	$pr+= $reprise->getachat($id,23);
	$pr+= $reprise->getachat($id,23,1);
	$pr+= $reprise->getachat($id,22);
	$pr+= $reprise->getachat($id,22,1);
	$pr+= $reprise->getachat($id,24);
	$pr-= $reprise->getvente($id,21);
	$pr-= $reprise->getvente($id,21,1);

	$marge = $reprise->getvente($id,17);
	$marge+= $reprise->getvente($id,17,1);
	$marge-= $pr;

	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="14">Etude de rentabilité</td></tr>';
	print '<tr class="liste_titre">';
	print '<td align="center"width="14%" Colspan="2">Achats</td>';
	print '<td align="center"width="14%" Colspan="2">Cession</td>';
	print '<td align="center"width="14%" Colspan="2">Factures Div.</td>';
	print '<td align="center"width="14%" Colspan="2">Dépréciaitions</td>';
	print '<td align="center"width="14%" Colspan="2">Ventes Diverses</td>';
	print '<td align="center"width="8%" Rowspan="2">prix de</br>Reviens</td>';
	print '<td align="center"width="14%" Colspan="2">Vente VO</td>';
	print '<td align="center"width="8%" Rowspan="2">Marge</br>Commerciale</td>';
	print '</tr>';
	print '<tr class="liste_titre">';
	print '<td align="center"width="7%">Réel</td>';
	print '<td align="center"width="7%">Prov.</td>';
	print '<td align="center"width="7%">Réel</td>';
	print '<td align="center"width="7%">Prov.</td>';
	print '<td align="center"width="7%">Réel</td>';
	print '<td align="center"width="7%">Prov.</td>';
	print '<td align="center"width="7%">Ordi.</td>';
	print '<td align="center"width="7%">Excep.</td>';
	print '<td align="center"width="7%">Réel</td>';
	print '<td align="center"width="7%">Prov.</td>';
	print '<td align="center"width="7%">Réel</td>';
	print '<td align="center"width="7%">Prov.</td>';
	print '</tr>';
	print '<tr>';
	Print '<td>' . price($reprise->getachat($id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getachat($id,17,1)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getachat($id,23)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getachat($id,23,1)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getachat($id,22)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getachat($id,22,1)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . 0 . '</td>';
	Print '<td>' . price($reprise->getachat($id,24)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getvente($id,21)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getvente($id,21,1)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($pr) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getvente($id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . price($reprise->getvente($id,17,1)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	Print '<td>' . $marge . '</td>';
	print '</tr>';
	print '</table>';
	print '</br>';
	print '</br>';
}



if ($action=='edit_valeur' && $user->rights->volvo->admin){
	print '<form name="estimrachat" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_valeur">';
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="4">Synthese de la reprise</td></tr>';
	print '<tr>';
	print '<td width="25%">Estimation de la reprise: ' . price($reprise->estim) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Engagement de rachat: <input type="text" name="rachat" size="5" value="' . price2num($reprise->rachat) . '"/><input type="submit" class="button" value="' . $langs->trans('Modify') . '"></td>';
	print '<td width="25%">Montant de la vente VO: ' . price($reprise->getvente($reprise->id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Surestimation du VO: ' . price($reprise->getsures($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
 	print '</form>';

} elseif ($action=='estim' && $user->rights->volvo->admin){
 	print '<form name="estimreprise" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_estim">';
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="4">Synthese de la reprise</td></tr>';
	print '<tr>';
	print '<td width="25%">Estimation de la reprise: <input type="text" name="estim" size="5" value="' . price2num($reprise->estim) . '"/><input type="submit" class="button" value="' . $langs->trans('Modify') . '"></td>';
	print '<td width="25%">Engagement de rachat: ' . price($reprise->rachat) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Montant de la vente VO: ' . price($reprise->getvente($reprise->id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Surestimation du VO: ' . price($reprise->getsures($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
 	print '</form>';

} elseif($user->rights->volvo->lireeprise) {
 	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="4">Synthese des reprises</td></tr>';
	print '<tr>';
	print '<td width="25%">Estimation de la reprise: ' . price($reprise->estim) . $langs->getCurrencySymbol($conf->currency) . ($user->rights->volvo->admin? '<a href="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&action=estim">' . img_edit('Modifier', 1) . '</a>':'') . '</td>';
	print '<td width="25%">Engagement de rachat: ' . price($reprise->rachat) . $langs->getCurrencySymbol($conf->currency) .  ($user->rights->volvo->admin? '<a href="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&action=edit_valeur">' . img_edit('Modifier', 1) . '</a>':'') . '</td>';
	print '<td width="25%">Montant de la vente VO: ' . price($reprise->getvente($reprise->id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Surestimation du VO: ' . price($reprise->getsures($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 }


 $filearray=dol_dir_list($upload_dir,"files",0,'','(\.meta|_preview\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
 $modulepart = 'volvo';
 $permission = 1;
 $param = '&id=' . $id ;

 include_once DOL_DOCUMENT_ROOT . '/volvo/template/document_actions_post_headers2.tpl.php';

llxFooter();
$db->close();