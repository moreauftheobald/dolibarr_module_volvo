<?php
$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/volvo/class/reprise.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/reception.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/expertise.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/lib/reprise.lib.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/lead.extend.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';
dol_include_once('/lead/lib/lead.lib.php');
require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';


$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$confirm = GETPOST('confirm', 'alpha');
$repid = GETPOST('repid', 'int');
$exp = GETPOST('exp', 'int');

$form = new Form($db);

$object = new Leadext($db);
$object->fetch($id);
$object->fetch_thirdparty();

$reprise = new Reprise($db);

$expertise = new Expertise($db);
$num = $reprise->fetchAll('','',0,0,array('t.fk_lead'=>$id));
$lines = $reprise->lines;

$actif = 'info' . $repid;

$reprise->fetch($repid);
$reprise->fetch_thirdparty();

$reception = new Reception($db);

$upload_dir = $conf->volvo->dir_output .'/'. dol_sanitizeFileName($reprise->ref);

if ($action == 'update_valeur'){
	$reprise->rachat = GETPOST('rachat', 'int');
	$reprise->update();
}

if ($action == 'update_estim'){
	$reprise->estim = GETPOST('estim', 'int');
	$reprise->update();
}


$img0 = img_picto('non','statut0');
$img1 = img_picto('non','statut6');

if ($action == 'create_reception'){
	foreach($reception->value_array as $value){
		if (isset($_POST[$value])){
			$reception->$value = GETPOST($value);
		}
	}
	$reception->fk_reprise = $repid;
	if(isset($_POST['date_reception_'])){$reception->date_reception = dol_mktime(0, 0, 0, GETPOST('date_reception_month'), GETPOST('date_reception_day'), GETPOST('date_reception_year'));}
	$res = $reception->create();
	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $object->id.'&action=&repid='. $repid);
}



$res = $reception->fetchbyrep($repid);
if ($res === 0){
	$action = 'create';
}

if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->volvo->gere_reprise) {
	$res = $reception->delete();
 	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $object->id.'&action=&repid=' . $repid);
}

if ($action == 'update_reception'){
	foreach($reception->value_array as $value){
		if (isset($_POST[$value])){
			$reception->$value = GETPOST($value);
		}
	}
	$reception->fk_reprise = $repid;
	if(isset($_POST['date_reception_'])){$reception->date_reception = dol_mktime(0, 0, 0, GETPOST('date_reception_month'), GETPOST('date_reception_day'), GETPOST('date_reception_year'));}
	$res = $reception->update();
	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $object->id.'&action=&repid='. $repid);
}

include_once DOL_DOCUMENT_ROOT . '/volvo/template/document_actions_pre_headers.tpl.php';

llxHeader('', 'reprise');
$head = lead_prepare_head($object);
dol_fiche_head($head, 'reprise', 'Affaire', 0, dol_buildpath('/volvo/img/iron02.png', 1), 1);
$form = new Form($db);

print '<table class="border" width="100%">';
print '<tr class="liste_titre"><td align="center" colspan="6">Synthese des reprises</td></tr>';
print '<tr>';
print '<td width="25%">Montant total des estimations des reprises</td><td width="10%">' . price($reprise->gettotalestim($object->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
print '<td width="23%">Montant total des engagement de rachat</td><td width="10%">' . price($reprise->gettotalrachat($object->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
print '<td width="21%">Montant total des ventes de VO</td><td width="10%">35 000.00 €</td>';
print '</tr>';
print '</table>';
print '</br>';

$head = reprise_prepare_head($object,$lines);
dol_fiche_head($head, $actif, 'reprises', 0, dol_buildpath('/volvo/img/iron02.png', 1), 1);

if ($action=='edit_valeur'){
	print '<form name="estimrachat" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&repid=' . $repid . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_valeur">';
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="6">Synthese de la reprise</td></tr>';
	print '<tr>';
	print '<td width="25%">Montant de l' . "'" . 'estimations de la reprise</td><td width="10%">' . price($reprise->estim) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="23%">Montant de l' . "'" . 'engagement de rachat</td><td width="10%"><input type="text" name="rachat" size="5" value="' . price2num($reprise->rachat) . '"/></td>';
	print '<td width="21%">Montant de la ventes de VO</td><td width="10%">' . price($reprise->getvente($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
 	print '</form>';

} elseif ($action=='estim'){
 	print '<form name="estimreprise" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&repid=' . $repid . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_estim">';
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="6">Synthese de la reprise</td></tr>';
	print '<tr>';
	print '<td width="25%">Montant de l' . "'" . 'estimation de la reprise</td><td width="10%"><input type="text" name="estim" size="5" value="' . price2num($reprise->estim) . '"/></td>';
	print '<td width="23%">Montant de l' . "'" . 'engagement de rachat</td><td width="10%">' . price($reprise->rachat) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="21%">Montant de la ventes de VO</td><td width="10%">' . price($reprise->getvente($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
 	print '</form>';

} else {
 	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="6">Synthese de la reprise</td></tr>';
	print '<tr>';
	print '<td width="25%">Montant de l' . "'" . 'estimation de la reprises</td><td width="10%">' . price($reprise->estim,'HTML') . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="23%">Montant de l' . "'" . 'engagement de rachat</td><td width="10%">' . price($reprise->rachat,'HTML') . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="21%">Montant de la ventes de VO</td><td width="10%">' . price($reprise->getvente($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
	if ($user->rights->volvo->estim_reprise) {
 		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&repid=' . $repid . '&action=estim">' . 'Saisir une estimation' . "</a></div>\n";
 	}
	if ($user->rights->lead->write) {
 		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&repid=' . $repid . '&action=edit_valeur">' . 'Saisir une valeur de rachat' . "</a></div>\n";
 	}
 	print '</div>';
}

$expertise->fetchAll('','',0,0,array('t.fk_reprise'=>$repid));
$head = info_prepare_head($object, $expertise->liste_exp,$repid);
dol_fiche_head($head, 'recep', 'reprise', 0, dol_buildpath('/volvo/img/iron02.png', 1), 1);

if ($action == 'create'){
print '<form name="createrecep" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&repid=' . $repid . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="create_reception">';
	print '<input type="hidden" name="fk_reprise" value="' . $repid . '">';
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
						$doleditor = new DolEditor('comm_etat','' , '', 160, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
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
						$doleditor2 = new DolEditor('presentation_produit','' , '', 160, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
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

} elseif ($action =='edit'){

	print '<form name="updaterecep" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&repid=' . $repid . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_reception">';
	print '<input type="hidden" name="fk_reprise" value="' . $repid . '">';
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
						$doleditor = new DolEditor('comm_etat',$reception->comm_etat , '', 160, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
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
						$doleditor2 = new DolEditor('presentation_produit',$reception->presentation_produit , '', 160, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
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


} else {
	$formconfirm = '';
	if ($action == 'suppr') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id . '&repid=' . $repid, 'Supprimer', 'Supprimer la Réception ?', 'confirm_delete', '', 0, 1);
	}
	print $formconfirm;

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
	print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&repid=' . $repid . '&exp=' . $exp . '&action=edit">' . 'Modifier' . "</a></div>\n";
	if ($user->rights->volvo->gere_reprise) {
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&repid=' . $repid . '&exp=' . $exp . '&action=suppr">' . 'Supprimer' . "</a></div>\n";
	}
	print '</div>';

}

$filearray=dol_dir_list($upload_dir,"files",0,'','(\.meta|_preview\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
	$modulepart = 'volvo';
	$permission = 1;
	$param = '&id=' . $object->id . '&repid='. $reprise->id;

	include_once DOL_DOCUMENT_ROOT . '/volvo/template/document_actions_post_headers.tpl.php';


llxFooter();
$db->close();