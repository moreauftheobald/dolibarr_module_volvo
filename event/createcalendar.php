<?php
$res = @include '../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';

dol_include_once('/volvo/class/lead.extend.class.php');

global $user;

$leadid = GETPOST('leadid', 'int');
$action = GETPOST('action', 'alpha');

$etat = array();
$etat[0] = 'A Plannifier';
$etat[1] = 'A réaliser';
$i=2;
While ($i>100){
	$etat[$i] = 'En cours';
	$i++;
}
$etat["100"] = 'Terminé';

$form = new Form($db);
$user_action = New User($db);
$contact = New Contact($db);
$actioncomm = New ActionComm($db);
$formaction = New FormActions($db);
$lead = new Leadext($db);
$lead->fetch($leadid);
$lead->fetch_thirdparty();

$langs->load('orders');

if ($action == 'createcalendar') {
	$datep = dol_mktime(0, 0, 0, GETPOST('date_month','int'), GETPOST('date_day','int'), GETPOST('date_year','int'));
	$datef = dol_mktime(23, 59, 59, GETPOST('date_month','int'), GETPOST('date_day','int'), GETPOST('date_year','int'));
	$action_type = GETPOST('action_type','alpha');
	$libelle = GETPOST('libelle','alpha');
	$contact->fetch(GETPOST('contact','int'));

	$actioncomm->datep = $datep;
	$actioncomm->datef = $datef;
	$actioncomm->fulldayevent = 1;
	$actioncomm->percentage = 1;
	$actioncomm->type_code = $action_type;
	$actioncomm->label = $libelle;
	$actioncomm->socid = $lead->fk_soc;
	$actioncomm->contact_id = $contact->id;
	$actioncomm->elementtype = 'lead';
	$actioncomm->element = $lead->id;
	$actioncomm->array_options['options_affaire'] = $lead->id;
	$actioncomm->userassigned[1]=array('id'=>$user->id);
	$actioncomm->userownerid = $lead->fk_user_author;
	$res = $actioncomm->create($user);

	if ($res<0){
		setEventMessage($actioncomm->errors,'errors');
 	}
}

top_htmlhead('', '');
$var = ! $var;

print '<form name="createorder" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="leadid" value="' . $leadid . '">';
print '<input type="hidden" name="action" value="createcalendar">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<th align="center" colspan="6">' . "calendrier des actions liées a l'affaire</th>";
print '</tr>';

print '<tr class="liste_titre">';
Print '<th width="10%" align="center"> Date </th>';
Print '<th width="10%" align="center"> Intervenant </th>';
Print '<th width="10%" align="center"> Type </th>';
Print '<th width="20%" align="center"> Contact </th>';
Print '<th width="40%" align="center"> Description </th>';
Print '<th width="10%" align="center"> Etat </th>';
print '</tr>';

$sql = "SELECT a.id, a.datep, c.libelle, a.label, a.percent, a.fk_contact, a.fk_user_action";
$sql.= " FROM ". MAIN_DB_PREFIX . "actioncomm AS a";
$sql.= " LEFT JOIN " . MAIN_DB_PREFIX . "c_actioncomm AS c ON c.id = a.fk_action";
$sql.= " LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm_extrafields as ef on a.id = ef.fk_object";
$sql.= " WHERE ef.affaire =" .$leadid ;
$sql.= " OR (a.elementtype = 'lead' AND a.fk_element =" .$leadid . ")";
$sql.= " ORDER BY a.datep DESC";

$res = $db->query($sql);
if ($res){
	While ($obj=$db->fetch_object($res)){
		$user_action->fetch($obj->fk_user_action);
		$contact->fetch($obj->fk_contact);
		$actioncomm->fetch($obj->id);
		if (array_key_exists($obj->percent, $etat)) {
			$statut = $etat[$obj->percent];
		} else {
			$statut = "inconnu";
		}
		print '<tr ' . $bc[$var] . '>';
		print '<td align="center">' .dol_print_date($obj->datep,"%d/%m/%Y").'</td>';
		print '<td align="center">' . $user_action->getNomUrl(1) .'</td>';
		print '<td align="center">' . $obj->libelle .'</td>';
		print '<td align="center">' . $contact->getNomUrl(1) .'</td>';
		print '<td align="center">' . $actioncomm->getNomUrl(1) .'</td>';
		print '<td align="center">' .  $statut .'</td>';
		print '</tr>';
		$var = !$var;
	}
} else {
	print '<tr ' . $bc[$var] . '>';
	print '<td align="center" colspan="6">' . "Aucun rdv a afficher </td>";
	print '</tr>';
}
$test = GETPOST('date_');
if (!empty($test)){
	$date_def = dol_mktime(0, 0, 0, GETPOST('date_month'), GETPOST('date_day'), GETPOST('date_year'));
} else {
	$date_def = dol_now();
}

$test = GETPOST('action_type');
if (!empty($test)){
	$action_def = GETPOST('action_type');
} else {
	$action_def = 'AC_RDV';
}

print '</table>';
print '</br>';
print '</br>';
print '<table class="nobordernopadding" width="100%">';
print '<tr class="liste_titre">';
print '<th align="center" colspan="5">' . "Ajouter des actions a l'affaire</th>";
print '</tr>';
print '<tr class="liste_titre">';
print '<th align="center">date</th>';
print '<th align="center">Type</th>';
print '<th align="center">Libellé</th>';
print '<th align="center">Contact</th>';
print '<th align="center"></th>';
print '</tr>';
print '<tr>';
print '<td>' . $form->select_date($date_def,'date_',0,0,0,'',1,1,1) . '</td>';
print '<td>';
$formaction->select_type_actions($action_def,'action_type','systemauto',0,-1);
print '</td>';
print '<td><input type="text" name="libelle" size="50" value="'. GETPOST('libelle') . '"/></td>';
print '<td>';
$form->select_contacts($lead->fk_soc, GETPOST('contactid'), 'contactid', 1, '', '', 0, 'minwidth200');
print '</td>';
print '<td><input type="submit" align="center" class="button" value="+" name="add" id="add"/></td>';
print '</tr>';
print '</table>';

print '</form>';

llxFooter();
$db->close();
