<?php
$res = @include '../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';

dol_include_once('/volvo/class/html.formvolvo.class.php');
dol_include_once('/volvo/class/lead.extend.class.php');

$form = new Form($db);
$formvolvo = new FormVolvo($db);

$langs->load('orders');

$leadid = GETPOST('leadid', 'int');
$action = GETPOST('action', 'alpha');

if ($action == 'creatorder') {

	$lead = new Leadext($db);
	$lead->fetch($leadid);
	$lead->fetch_thirdparty();
	$lead->prixvente = GETPOST('prixvente','int');
	$lead->commission = GETPOST('commission', 'int');
	$lead->datelivprev = dol_mktime(0, 0, 0, GETPOST('datelivprev_month', 'int'), GETPOST('datelivprev_day', 'int'), GETPOST('datelivprev_year', 'int'));
	$lead->interne = GETPOST('interne', 'array');
	$lead->externe = GETPOST('externe', 'array');
	$lead->divers = GETPOST('divers', 'array');
	$lead->obligatoire = json_decode(GETPOST('obligatoire'), true);
	$res = $lead->createcmd();
	if ($res<0){
		setEventMessage($lead->errors,'errors');
	} else {
		top_htmlhead('', '');
		print '<script type="text/javascript">'."\n";
		print '	$(document).ready(function () {'."\n";
		print '	window.parent.$(\'#ordercreatedid\').val(\''.$res.'\');'."\n";
		print '	window.parent.$(\'#popCreateOrder\').dialog(\'close\');'."\n";
		print '	window.parent.$(\'#popCreateOrder\').remove();'."\n";
		print '	window.parent.$(\'#wievlead\').dialog(\'close\');'."\n";
		print '	window.parent.$(\'#wievlead\').remove();'."\n";
		print '});'."\n";
		print '</script>'."\n";
		llxFooter();
		exit;
	}

}

$sql0 = "SELECT DISTINCT p.rowid, p.label FROM " . MAIN_DB_PREFIX . "product as p INNER JOIN " . MAIN_DB_PREFIX . "categorie_product as c ON p.rowid = c.fk_product ";
$sql0 .= "WHERE c.fk_categorie = " . $conf->global->VOLVO_OBLIGATOIRE . " AND p.tosell = 1";

$resql = $db->query($sql0);
$obligatoire = array();
if ($resql) {
	while ( $obj = $db->fetch_object($resql) ) {
		$obligatoire[] = $obj->rowid;
	}
} else {
	setEventMessage($db->lasterror, 'errors');
}
$sql1 = "SELECT c.rowid, c.label ";
$sql1.= "FROM " . MAIN_DB_PREFIX . "categorie AS c ";
$sql1.= "WHERE fk_parent = ". $conf->global->VOLVO_INTERNE;
$sql1.= " ORDER BY c.label";
$resql = $db->query($sql1);
$interne = array();
$count1 = 0;
if ($resql) {
	while ( $obj = $db->fetch_object($resql) ) {
		$count1++;
		$sql10 = "SELECT DISTINCT p.rowid, p.label, ";
		$sql10.= "MAX(IF(c.fk_categorie=" . $obj->rowid .",1,0)) AS CATEG, ";
		$sql10.= "MAX(IF(c.fk_categorie=" . $conf->global->VOLVO_OBLIGATOIRE .",1,0)) AS CATEG_EXC ";
		$sql10.= "FROM " . MAIN_DB_PREFIX . "product as p INNER JOIN " . MAIN_DB_PREFIX . "categorie_product as c ON p.rowid = c.fk_product ";
		$sql10.= "WHERE p.tosell = 1 ";
		$sql10.= "GROUP BY p.rowid ";
		$sql10.= "HAVING CATEG = 1 AND CATEG_EXC !=1 ";
		$sql10.= "ORDER BY p.label";

		$resql2 = $db->query($sql10);
		if ($resql2) {
			$list=array();
			while ( $obj2 = $db->fetch_object($resql2) ) {
				$list[$obj2->rowid] = $obj2->label;
			}
			$interne[$obj->label] = $list;
		} else {
			setEventMessage($db->lasterror, 'errors');
		}
	}
} else {
	setEventMessage($db->lasterror, 'errors');
}

$sql2 = "SELECT DISTINCT p.rowid, p.label, ";
$sql2.= "MAX(IF(c.fk_categorie=" . $conf->global->VOLVO_EXTERNE .",1,0)) AS CATEG, ";
$sql2.= "MAX(IF(c.fk_categorie=" . $conf->global->VOLVO_OBLIGATOIRE .",1,0)) AS CATEG_EXC ";
$sql2.= "FROM " . MAIN_DB_PREFIX . "product as p INNER JOIN " . MAIN_DB_PREFIX . "categorie_product as c ON p.rowid = c.fk_product ";
$sql2.= "WHERE p.tosell = 1 ";
$sql2.= "GROUP BY p.rowid ";
$sql2.= "HAVING CATEG = 1 AND CATEG_EXC !=1 ";
$sql2.= "ORDER BY p.label";

$resql = $db->query($sql2);
$externe = array();
if ($resql) {
	while ( $obj = $db->fetch_object($resql) ) {
		$externe[$obj->rowid] = $obj->label;
	}
} else {
	setEventMessage($db->lasterror, 'errors');
}

$sql3 = "SELECT DISTINCT p.rowid, p.label, ";
$sql3.= "MAX(IF(c.fk_categorie=" . $conf->global->VOLVO_DIVERS .",1,0)) AS CATEG, ";
$sql3.= "MAX(IF(c.fk_categorie=" . $conf->global->VOLVO_OBLIGATOIRE .",1,0)) AS CATEG_EXC ";
$sql3.= "FROM " . MAIN_DB_PREFIX . "product as p INNER JOIN " . MAIN_DB_PREFIX . "categorie_product as c ON p.rowid = c.fk_product ";
$sql3.= "WHERE p.tosell = 1 ";
$sql3.= "GROUP BY p.rowid ";
$sql3.= "HAVING CATEG = 1 AND CATEG_EXC !=1 ";
$sql3.= "ORDER BY p.label";

$resql = $db->query($sql3);
$divers = array();
if ($resql) {
	while ( $obj = $db->fetch_object($resql) ) {
		$divers[$obj->rowid] = $obj->label;
	}
} else {
	setEventMessage($db->lasterror, 'errors');
}
$internesection='';
foreach ($interne as $key=>$array){
	$internesection.= '<div class="cal_event cal_event_busy" align="left" id="fixe_'. $key . '" style="background:' . $color .'; ';
	$internesection.= 'background: -webkit-gradient(linear, left top, left bottom, from('.$color.'), to('.$color2.')); ';
	$internesection.= 'border-radius:6px; margin-bottom: 3px;">';
	$internesection.= '<h><a href="" onclick="javascript:visibilite(\'' . $key . '\'); return false;" >'. img_edit_add('+','') . '</a>' . $key . '</h>';
	$internesection.= '<div id="' . $key . '" style="display:none;">';
	$internesection.= $formvolvo->select_withcheckbox("interne_".$key,$array);
	$internesection.= '</div>';
	$internesection.= '</di>';
}



top_htmlhead('', '');
$var = ! $var;

print '<form name="createorder" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="leadid" value="' . $leadid . '">';
print '<input type="hidden" name="action" value="creatorder">';
print '<input type="hidden" name="obligatoire" value="' . htmlspecialchars(json_encode($obligatoire)) . '">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<th align="center" colspan="3">' . "Transformation d'une affaire en commande</th>";
print '</tr>';
print '<tr ' . $bc[$var] . '>';
print '<td colspan="3">';
print '<table width="100%" class="nobordernopadding">';
print '<tr ' . $bc[$var] . '>';
print '<td align="center">' . $langs->trans('Prix de vente du véhicule') . ': <input type="text" name="prixvente" size="7" value=""/> €</td>';
print '<td align="center">' . $langs->trans('commission Dealer sur fiche de décision') . ': <input type="text" name="commission" size="7" value=""/> €</td>';
print '<td align="center">' . $langs->trans('Date de livraison souhaitée') . ': ' . $form->select_date('', 'datelivprev_', 0, 0, 1, '', 1, 1, 1, 0, '', '', '') . '</td>';
print '</tr>';
print '</table>';
print '</td>';
print '</tr>';
print '<tr class="liste_titre">';
print '<th align="center">' . $langs->trans('Travaux internes') . '</th>';
print '<th align="center">' . $langs->trans('Travaux externes') . '</th>';
print '<th align="center">' . $langs->trans('Travaux divers') . '</th>';
print '</tr>';
print '<tr >';
print '<td align="left" valign="top">' . $internesection . '</td>';
print '<td align="left" valign="top">' . $formvolvo->select_withcheckbox("externe", $externe) . '</td>';
print '<td align="left" valign="top">' . $formvolvo->select_withcheckbox("divers", $divers) . '</td>';
print '</tr>';

print '</table>';
print '<div class="tabsAction">';
print '<input type="submit" align="center" class="button" value="' . $langs->trans('Save') . '" name="save" id="save"/>';
print '</div>';
print '</form>';


?>
<script type="text/javascript" language="javascript">
function visibilite(thingId) {
	var targetElement;
	targetElement = document.getElementById(thingId) ;
	if (targetElement.style.display == "none") {
		targetElement.style.display = "" ;
	} else {
		targetElement.style.display = "none" ;
	}
}
</script>
<?php
llxFooter();
$db->close();
?>