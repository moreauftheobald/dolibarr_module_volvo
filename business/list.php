<?php
/*
 * Copyright (C) 2014 Florian HENRY <florian.henry@open-concept.pro>
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

$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/lead.extend.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.commande.class.php';

$title = 'Suivis des affaires en cours';

// Security check
if (! $user->rights->volvo->business)
	accessforbidden();

 $sortorder = GETPOST('sortorder', 'alpha');
 $sortfield = GETPOST('sortfield', 'alpha');
 $page = GETPOST('page', 'int');

 // view type is special predefined filter
 $viewtype=GETPOST('viewtype','alpha');

// Search criteria
$search_commercial = GETPOST("search_commercial", 'int');
$search_client = GETPOST("search_client");
$search_lead = GETPOST('search_lead');
$search_ana = GETPOST('search_ana');
$search_cmd = GETPOST('search_cmd');
$search_vin = GETPOST('search_vin');
$search_immat = GETPOST('search_immat');
$search_numom = GETPOST('search_numom');
$search_date_valid_min = dol_mktime(0, 0, 0, GETPOST('search_date_valid_min_month'), GETPOST('search_date_valid_min_day'), GETPOST('search_date_valid_min_year'));
$search_date_valid_max = dol_mktime(23, 59, 59, GETPOST('search_date_valid_max_month'), GETPOST('search_date_valid_max_day'), GETPOST('search_date_valid_max_year'));
$search_date_lpc_min = dol_mktime(0, 0, 0, GETPOST('search_date_lpc_min_month'), GETPOST('search_date_lpc_min_day'), GETPOST('search_date_lpc_min_year'));
$search_date_lpc_max = dol_mktime(23, 59, 59, GETPOST('search_date_lpc_max_month'), GETPOST('search_date_lpc_max_day'), GETPOST('search_date_lpc_max_year'));
$search_date_lrc_min = dol_mktime(0, 0, 0, GETPOST('search_date_lrc_min_month'), GETPOST('search_date_lrc_min_day'), GETPOST('search_date_lrc_min_year'));
$search_date_lrc_max = dol_mktime(23, 59, 59, GETPOST('search_date_lrc_max_month'), GETPOST('search_date_lrc_max_day'), GETPOST('search_date_lrc_max_year'));
$search_date_fac_min = dol_mktime(0, 0, 0, GETPOST('search_date_fac_min_month'), GETPOST('search_date_fac_min_day'), GETPOST('search_date_fac_min_year'));
$search_date_fac_max = dol_mktime(23, 59, 59, GETPOST('search_date_fac_max_month'), GETPOST('search_date_fac_max_day'), GETPOST('search_date_fac_max_year'));
$search_date_pai_min = dol_mktime(0, 0, 0, GETPOST('search_date_pai_min_month'), GETPOST('search_date_pai_min_day'), GETPOST('search_date_pai_min_year'));
$search_date_pai_max = dol_mktime(23, 59, 59, GETPOST('search_date_pai_max_month'), GETPOST('search_date_pai_max_day'), GETPOST('search_date_pai_max_year'));
$search_date_ecu_min = dol_mktime(0, 0, 0, GETPOST('search_date_ecu_min_month'), GETPOST('search_date_ecu_min_day'), GETPOST('search_date_ecu_min_year'));
$search_date_ecu_max = dol_mktime(23, 59, 59, GETPOST('search_date_ecu_max_month'), GETPOST('search_date_ecu_max_day'), GETPOST('search_date_ecu_max_year'));
$search_date_blk_min = dol_mktime(0, 0, 0, GETPOST('search_date_blk_min_month'), GETPOST('search_date_blk_min_day'), GETPOST('search_date_blk_min_year'));
$search_date_blk_max = dol_mktime(23, 59, 59, GETPOST('search_date_blk_max_month'), GETPOST('search_date_blk_max_day'), GETPOST('search_date_blk_max_year'));
$search_date_lpu_min = dol_mktime(0, 0, 0, GETPOST('search_date_lpu_min_month'), GETPOST('search_date_lpu_min_day'), GETPOST('search_date_lpu_min_year'));
$search_date_lpu_max = dol_mktime(23, 59, 59, GETPOST('search_date_lpu_max_month'), GETPOST('search_date_lpu_max_day'), GETPOST('search_date_lpu_max_year'));
$search_date_lru_min = dol_mktime(0, 0, 0, GETPOST('search_date_lru_min_month'), GETPOST('search_date_lru_min_day'), GETPOST('search_date_lru_min_year'));
$search_date_lru_max = dol_mktime(23, 59, 59, GETPOST('search_date_lru_max_month'), GETPOST('search_date_lru_max_day'), GETPOST('search_date_lru_max_year'));
$search_prep_min = GETPOST('search_prep_min','int');
$search_prep_max = GETPOST('search_prep_max','int');
$search_ret_usi_min = GETPOST('search_ret_usi_min','int');
$search_ret_usi_max = GETPOST('search_ret_usi_max','int');
$search_ret_cli_min = GETPOST('search_ret_cli_min','int');
$search_ret_cli_max = GETPOST('search_ret_cli_max','int');
$search_cash_min = GETPOST('search_cash_min','int');
$search_cash_max = GETPOST('search_cash_max','int');
$search_run = GETPOST('search_run','int');


// Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x")) {
 	$search_commercial = '';
 	$search_client = '';
 	$search_lead = '';
 	$search_ana = '';
	$search_cmd = '';
 	$search_vin = '';
 	$search_immat = '';
 	$search_numom = '';
 	$search_date_valid_min = '';
 	$search_date_valid_max ='';
 	$search_date_lpc_min = '';
 	$search_date_lpc_max ='';
 	$search_date_lrc_min = '';
 	$search_date_lrc_max ='';
 	$search_date_fac_min = '';
 	$search_date_fac_max ='';
 	$search_date_pai_min = '';
 	$search_date_pai_max ='';
 	$search_date_ecu_min = '';
 	$search_date_ecu_max ='';
 	$search_date_blk_min = '';
 	$search_date_blk_max ='';
 	$search_date_lpu_min = '';
 	$search_date_lpu_max ='';
 	$search_date_lru_min = '';
 	$search_date_lru_max ='';
 	$search_prep_min = '';
 	$search_prep_max = '';
 	$search_ret_usi_min = '';
 	$search_ret_usi_max = '';
 	$search_ret_cli_min = '';
 	$search_ret_cli_max = '';
 	$search_cash_min = '';
 	$search_cash_max = '';
 	$search_run = '';
}
$search_commercial_disabled = 0;
if (empty($user->rights->volvo->stat_all)){
	$search_commercial = $user->id;
	$search_commercial_disabled = 1;
}
$user_included=array();
$sqlusers = "SELECT fk_user FROM " . MAIN_DB_PREFIX . "usergroup_user WHERE fk_usergroup = 1";
$resqlusers  = $db->query($sqlusers);
if($resqlusers){
	while ($users = $db->fetch_object($resqlusers)){
		$user_included[] = $users->fk_user;
	}
}

$filter = array();
if (! empty($search_commercial) && $search_commercial != -1) {
 	$filter['lead.fk_user_resp'] = $search_commercial;
 	$option .= '&search_commercial=' . $search_commercial;
}
if (! empty($search_client)) {
	$filter['soc.nom'] = $search_client;
 	$option .= '&search_client=' . $search_client;
}
if (! empty($search_lead)) {
 	$filter['lead.ref'] = $search_lead;
 	$option .= '&search_lead=' . $search_lead;
}
if (! empty($search_ana)) {
 	$filter['com.ref'] = $search_ana;
 	$option .= '&search_ana=' . $search_ana;
}
if (! empty($search_cmd)) {
 	$filter['cf.ref'] = $search_cmd;
	$option .= '&search_cmd=' . $search_cmd;
}
if (! empty($search_vin)) {
 	$filter['ef.vin'] = $search_vin;
$option .= '&search_vin=' . $search_vin;
}
if (! empty($search_numom)) {
	$filter['ef.numom'] = $search_numom;
	$option .= '&search_numom=' . $search_numom;
}
if (! empty($search_immat)) {
 	$filter['ef.immat'] = $search_immat;
 	$option .= '&search_immat=' . $search_immat;
}
if (! empty($search_date_valid_min) && ! empty($search_date_valid_max)) {
	$filter['com.date_valid'] = "'" . $db->idate($search_date_valid_min) . "' AND '" . $db->idate($search_date_valid_max) . "'";
	$option .= '&search_date_valid_min=' . $search_date_valid_min . '&search_date_valid_max=' . $search_date_valid_max;
}
if (! empty($search_date_lpc_min) && ! empty($search_date_lpc_max)) {
	$filter['com.date_livraison'] = "'" . $db->idate($search_date_lpc_min) . "' AND '" . $db->idate($search_date_lpc_max) . "'";
	$option .= '&search_date_lpc_min=' . $search_date_lpc_min . '&search_date_lpc_max=' . $search_date_lpc_max;
}
if (! empty($search_date_lrc_min) && ! empty($search_date_lrc_max)) {
	$filter['event4.datep'] = "'" . $db->idate($search_date_lrc_min) . "' AND '" . $db->idate($search_date_lrc_max) . "'";
	$option .= '&search_date_lrc_min=' . $search_date_lrc_min . '&search_date_lrc_max=' . $search_date_lrc_max;
}
if (! empty($search_date_fac_min) && ! empty($search_date_fac_max)) {
	$filter['event3.datep'] = "'" . $db->idate($search_date_fac_min) . "' AND '" . $db->idate($search_date_fac_max) . "'";
	$option .= '&search_date_fac_min=' . $search_date_fac_min . '&search_date_fac_max=' . $search_date_fac_max;
}
if (! empty($search_date_pai_min) && ! empty($search_date_pai_max)) {
	$filter['event5.datep'] = "'" . $db->idate($search_date_pai_min) . "' AND '" . $db->idate($search_date_pai_max) . "'";
	$option .= '&search_date_pai_min=' . $search_date_pai_min . '&search_date_pai_max=' . $search_date_pai_max;
}
if (! empty($search_date_ecu_min) && ! empty($search_date_ecu_max)) {
	$filter['cf.date_commande'] = "'" . $db->idate($search_date_ecu_min) . "' AND '" . $db->idate($search_date_ecu_max) . "'";
	$option .= '&search_date_ecu_min=' . $search_date_ecu_min . '&search_date_ecu_max=' . $search_date_ecu_max;
}
if (! empty($search_date_blk_min) && ! empty($search_date_blk_max)) {
	$filter['ef.dt_blockupdate'] = "'" . $db->idate($search_date_blk_min) . "' AND '" . $db->idate($search_date_blk_max) . "'";
	$option .= '&search_date_blk_min=' . $search_date_blk_min . '&search_date_blk_max=' . $search_date_blk_max;
}
if (! empty($search_date_lpu_min) && ! empty($search_date_lpu_max)) {
	$filter['cf.date_livraison'] = "'" . $db->idate($search_date_lpu_min) . "' AND '" . $db->idate($search_date_lpu_max) . "'";
	$option .= '&search_date_lpu_min=' . $search_date_lpu_min . '&search_date_lpu_max=' . $search_date_lpu_max;
}
if (! empty($search_date_lru_min) && ! empty($search_date_lru_max)) {
	$filter['event6.datep'] = "'" . $db->idate($search_date_lru_min) . "' AND '" . $db->idate($search_date_lru_max) . "'";
	$option .= '&search_date_lru_min=' . $search_date_lru_min . '&search_date_lru_max=' . $search_date_lru_max;
}

if (! empty($search_prep_min) && ! empty($search_prep_max)) {
	$filter['delaiprep'] = $search_prep_min . " AND " . $search_prep_max;
	$option .= '&search_prep_min=' . $search_prep_min . '&search_prep_max=' . $search_prep_max;
}
if (! empty($search_ret_usi_min) && ! empty($search_ret_usi_max)) {
	$filter['retard_recept'] = $search_ret_usi_min . " AND " . $search_ret_usi_max;
	$option .= '&search_ret_usi_min=' . $search_ret_usi_min . '&search_ret_usi_max=' . $search_ret_usi_max;
}
if (! empty($search_ret_cli_min) && ! empty($search_ret_cli_max)) {
	$filter['retard_liv'] = $search_ret_cli_min . " AND " . $search_ret_cli_max;
	$option .= '&search_ret_cli_min=' . $search_ret_cli_min . '&search_ret_cli_max=' . $search_ret_cli_max;
}
if (! empty($search_cash_min) && ! empty($search_cash_max)) {
	$filter['delai_cash'] = $search_cash_min . " AND " . $search_cash_max;
	$option .= '&search_cash_min=' . $search_cash_min . '&search_cash_max=' . $search_cash_max;
}

if (! empty($search_run)) {
	$filter['search_run'] = 1;
	$option .= '&search_run=1';
}

if ($page == - 1) {
	$page = 0;
}

$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

$form = new Form($db);
$object = new Leadext($db);
$formother = new FormOther($db);

if (empty($sortorder))
	$sortorder = "ASC";
if (empty($sortfield))
	$sortfield = "cf.date_livraison";

llxHeader('', $title);

// Count total nb of records
$nbtotalofrecords = 0;

if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
$nbtotalofrecords = $object->fetchAllfolow($sortorder, $sortfield, 0, 0, $filter);
}
$resql = $object->fetchAllfolow($sortorder, $sortfield, $conf->liste_limit, $offset, $filter);
if ($resql != - 1) {
$num = $resql;
  	print_barre_liste($title, $page, $_SERVER['PHP_SELF'], $option, $sortfield, $sortorder, '', $num, $nbtotalofrecords);
  	print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";

  	if (! empty($sortfield))
  		print '<input type="hidden" name="sortfield" value="' . $sortfield . '"/>';
  	if (! empty($sortorder))
  		print '<input type="hidden" name="sortorder" value="' . $sortorder . '"/>';
  	if (! empty($page))
  		print '<input type="hidden" name="page" value="' . $page . '"/>';
  	if (! empty($viewtype))
  		print '<input type="hidden" name="viewtype" value="' . $viewtype . '"/>';
  	if (! empty($socid))
  		print '<input type="hidden" name="socid" value="' . $socid . '"/>';

 	$i = 0;
	if(!empty($search_run)){
		$sel = ' checked';
	}else{
		$sel = '';
	}
 	print '<div align="left"><input class="liste_titre" type="image" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/search.png" value="' . dol_escape_htmltag($langs->trans("Search")) . '" title="' . dol_escape_htmltag($langs->trans("Search")) . '">';
 	print '&nbsp;<input type="image" class="liste_titre" name="button_removefilter" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/searchclear.png" value="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '" title="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '"></div>';
	print '<div align="left"><input type="checkbox" name="search_run" value="1"' . $sel . '> Selection uniquement sur les affaires en cours ?</div></br>';
 	print '<table class="noborder" width="100%">';

 	print '<tr class="liste_titre">';
 	print '<th class="liste_titre" colspan="7" align="center"> Références</th>';
 	print '<th class="liste_titre" colspan="4" align="center"> Commande Usine </th>';
 	print '<th class="liste_titre" colspan="5" align="center"> Commande Client </th>';
 	print '<th sclass="liste_titre" colspan="4" align="center"> Délais et retards </th>';
 	print "</tr>";

 	print '<tr class="liste_titre">';
 	print_liste_field_titre('Commercial', $_SERVEUR['PHP_SELF'], "lead.fk_user_resp", "", $option, 'align="left"', $sortfield, $sortorder);
 	print_liste_field_titre('N° O.M.', $_SERVEUR['PHP_SELF'], "ef.numom", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Dossier', $_SERVEUR['PHP_SELF'], "com.ref", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Affaire', $_SERVEUR['PHP_SELF'], "lead.ref", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Client', $_SERVEUR['PHP_SELF'], "lead.fk_soc", "", $option, 'align="left"', $sortfield, $sortorder);
 	print_liste_field_titre('Vin', $_SERVEUR['PHP_SELF'], "ef.vin", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Immat.', $_SERVEUR['PHP_SELF'], "ef.immat", "", $option, 'align="center"', $sortfield, $sortorder);

 	print_liste_field_titre('Date Envoi<br>Cmd Usine', $_SERVEUR['PHP_SELF'], "cf.date_commande", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Date de<br>bloc. Modif.', $_SERVEUR['PHP_SELF'], "ef.dt_blockupdate", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Date de<br>livraison<br>prévue', $_SERVEUR['PHP_SELF'], "cf.date_livraison", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Date de<br>livraison<br>réelle', $_SERVEUR['PHP_SELF'], "event6.datep", "", $option, 'align="center"', $sortfield, $sortorder);

 	print_liste_field_titre('Validation<br>fiche analyse', $_SERVEUR['PHP_SELF'], "com.date_valid", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Date de<br>livraison<br>demandée', $_SERVEUR['PHP_SELF'], "com.date_livraison", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Date de<br>livraison<br>réelle', $_SERVEUR['PHP_SELF'], "event4.datep", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Date de<br>facturation', $_SERVEUR['PHP_SELF'], "event3.datep", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Date de<br>paiement', $_SERVEUR['PHP_SELF'], "event5.datep", "", $option, 'align="center"', $sortfield, $sortorder);

 	print_liste_field_titre('Délai<br>Cash', $_SERVEUR['PHP_SELF'], "DATEDIFF(event5.datep,event6.datep)", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Délai<br>préparation', $_SERVEUR['PHP_SELF'], "DATEDIFF(com.date_livraison,cf.date_livraison)", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Retard<br>liv. Usine', $_SERVEUR['PHP_SELF'], "DATEDIFF(event6.datep,cf.date_livraison)", "", $option, 'align="center"', $sortfield, $sortorder);
 	print_liste_field_titre('Retard<br>liv. Client', $_SERVEUR['PHP_SELF'], "DATEDIFF(event4.datep,com.date_livraison)", "", $option, 'align="center"', $sortfield, $sortorder);
 	print "</tr>\n";

 	print '<tr class="liste_titre">';
 	print '<td align="center">' . $form->select_dolusers($search_commercial,'search_commercial',1,array(),$search_commercial_disabled,$user_included) . '</td>';
 	print '<td><input type="text" class="flat" name="search_numom" value="' . $search_numom . '" size="10"></td>';
 	print '<td><input type="text" class="flat" name="search_ana" value="' . $search_ana . '" size="6"></td>';
 	print '<td><input type="text" class="flat" name="search_lead" value="' . $search_lead . '" size="9"></td>';
 	print '<td><input type="text" class="flat" name="search_client" value="' . $search_client . '" size="30"></td>';
 	print '<td><input type="text" class="flat" name="search_vin" value="' . $search_vin . '" size="17"></td>';
 	print '<td><input type="text" class="flat" name="search_immat" value="' . $search_immat . '" size="8"></td>';

 	print '<td align="center">' . $form->select_date($search_date_ecu_min, 'search_date_ecu_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_ecu_max, 'search_date_ecu_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';
 	print '<td align="center">' . $form->select_date($search_date_blk_min, 'search_date_blk_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_blk_max, 'search_date_blk_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';
 	print '<td align="center">' . $form->select_date($search_date_lpu_min, 'search_date_lpu_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_lpu_max, 'search_date_lpu_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';
 	print '<td align="center">' . $form->select_date($search_date_lru_min, 'search_date_lru_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_lru_max, 'search_date_lru_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';

 	print '<td align="center">' . $form->select_date($search_date_valid_min, 'search_date_valid_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_valid_max, 'search_date_valid_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';
 	print '<td align="center">' . $form->select_date($search_date_lpc_min, 'search_date_lpc_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_lpc_max, 'search_date_lpc_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';
 	print '<td align="center">' . $form->select_date($search_date_lrc_min, 'search_date_lrc_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_lrc_max, 'search_date_lrc_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';
 	print '<td align="center">' . $form->select_date($search_date_fac_min, 'search_date_fac_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_fac_max, 'search_date_fac_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';
 	print '<td align="center">' . $form->select_date($search_date_pai_min, 'search_date_pai_min_',0,0,1,'',1,0,1,0,'','','') . '</br>';
 	print  $form->select_date($search_date_pai_max, 'search_date_pai_max_',0,0,1,'',1,0,1,0,'','','') . '</td>';

 	print '<td><input type="text" class="flat" name="search_cash_min" value="' . $search_cash_min . '" size="6"></br></br><input type="text" class="flat" name="search_cash_max" value="' . $search_cash_max . '" size="6"></td>';
 	print '<td><input type="text" class="flat" name="search_prep_min" value="' . $search_prep_min . '" size="6"></br></br><input type="text" class="flat" name="search_prep_max" value="' . $search_prep_max . '" size="6"></td>';
 	print '<td><input type="text" class="flat" name="search_ret_usi_min" value="' . $search_ret_usi_min . '" size="6"></br></br><input type="text" class="flat" name="search_ret_usi_max" value="' . $search_ret_usi_max . '" size="6"></td>';
 	print '<td><input type="text" class="flat" name="search_ret_cli_min" value="' . $search_ret_cli_min . '" size="6"></br></br><input type="text" class="flat" name="search_ret_cli_max" value="' . $search_ret_cli_max . '" size="6"></td>';
 	print "</tr>\n";
 	print '</form>';

 	$var = true;

 	foreach ($object->business as $line) {
		;$var = ! $var;

 		$comm = New User($db);
 		$comm->fetch($line->commercial);

 		$comfourn = new CommandeFournisseur($db);
 		$result=$comfourn->fetch($line->fournid);

 		$comcli = New Commande($db);
 		$comcli->fetch($line->com);

 		$lead = new Lead($db);
 		$lead->fetch($line->lead);

 		$soc = New Societe($db);
 		$soc->fetch($line->societe);

 		$om_label = '<a href="'.DOL_URL_ROOT.'/fourn/commande/card.php?id=';
 		$om_label.= $comfourn->id .'">';
 		if (!empty($line->numom)){
 			$om_label.= $line->numom;
 		}else{
 			$om_label.= $comfourn->ref;
 		}
 		$om_label.= '</a>';

 		print '<tr ' . $bc[$var] . '>';

 		print '<td>'. $comm->getNomUrl(0) .'</td>';
 		print '<td align="center">' . $om_label . '</td>';
 		print '<td align="center">' . $comcli->getNomUrl(0) . '</td>';
 		print '<td align="center">'. $lead->getNomUrl(0) .'</td>';
 		print '<td>'.$soc->getNomUrl(0,'',33) . '</td>';
 		print '<td align="center">' . $line->vin . '</td>';
 		print '<td align="center">' . $line->immat . '</td>';

 		print '<td align="center">' . dol_print_date($line->dt_env_usi,'day') . '</td>';
 		print '<td align="center">' . dol_print_date($line->dt_blockupdate,'day') . '</td>';
 		print '<td align="center">' . dol_print_date($line->dt_liv_cons,'day') . '</td>';
 		print '<td align="center">' . dol_print_date($line->dt_recep,'day') . '</td>';

 		print '<td align="center">' . dol_print_date($line->dt_valid_ana,'day') . '</td>';
 		print '<td align="center">' . dol_print_date($line->dt_liv_dem_cli,'day') . '</td>';
 		print '<td align="center">' . dol_print_date($line->dt_liv_cli,'day') . '</td>';
 		print '<td align="center">' . dol_print_date($line->dt_fac,'day') . '</td>';
 		print '<td align="center">' . dol_print_date($line->dt_pay,'day') . '</td>';

 		if(!empty($line->dt_recep)){
 			$text = $line->delai_cash . ' Jour(s)';
 		}else{
 			$text ='';
 		}
 		print '<td align="center">' . $text . '</td>';

 		if(!empty($line->dt_liv_dem_cli) && !empty($line->dt_liv_cons)){
 			$text = $line->delaiprep . ' Jour(s)';
 		}else{
 			$text ='';
 		}
 		print '<td align="center">' . $text . '</td>';

 		if(!empty($line->dt_liv_cons) && !empty($line->dt_recep)){
 			$text = $line->retard_recept . ' Jour(s)';
 		}else{
 			$text ='';
 		}
 		print '<td align="center">' . $text . '</td>';

 		if(!empty($line->dt_liv_dem_cli) && !empty($line->dt_liv_cli)){
 			$text = $line->retard_liv . ' Jour(s)';
 		}else{
 			$text ='';
 		}
 		print '<td align="center">' . $text . '</td>';

 		print "</tr>\n";

 		$i ++;
 	}

	print "</table>";

	}



llxFooter();
$db->close();
