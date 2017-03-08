<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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

/**
 *   	\file       immat/immat_list.php
 *		\ingroup    immat
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-07-18 21:17
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php');
require_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');
require_once(DOL_DOCUMENT_ROOT.'/user/class/user.class.php');
require_once(DOL_DOCUMENT_ROOT.'/volvo/class/lead.extend.class.php');
require_once(DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
dol_include_once('/volvo/class/immat.class.php');

// Load traductions files requiredby by page
$langs->load("immat");
$langs->load("other");

$form = new Form($db);

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_genre=GETPOST('search_genre','int');
$search_marque=GETPOST('search_marque','int');
$search_carrosserie=GETPOST('search_carrosserie','int');
$search_const_dist=GETPOST('search_const_dist','alpha');
$search_ptr=GETPOST('search_ptr','int');
$search_gvw=GETPOST('search_gvw','alpha');
$search_charutpl=GETPOST('search_charutpl','int');
$search_puissfisc=GETPOST('search_puissfisc','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_address=GETPOST('search_address','alpha');
$search_zip=GETPOST('search_zip','alpha');
$search_town=GETPOST('search_town','alpha');
$search_csp_prop=GETPOST('search_csp_prop','alpha');
$search_dt_carte_grise_DAY=GETPOST('search_dt_carte_grise_DAY','alpha');
$search_dt_carte_grise_MONTH=GETPOST('search_dt_carte_grise_MONTH','alpha');
$search_dt_carte_grise_YEAR=GETPOST('search_dt_carte_grise_YEAR','alpha');
$search_dt_carte_grise = dol_mktime(0, 0, 0, $search_dt_carte_grise_MONTH, $search_dt_carte_grise_DAY, $search_dt_carte_grise_YEAR);
$search_immat=GETPOST('search_immat','alpha');
$search_vin=GETPOST('search_vin','alpha');
$search_num_serie=GETPOST('search_num_serie','alpha');
$search_modele=GETPOST('search_modele','alpha');
$search_county=GETPOST('search_county','alpha');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_lead=GETPOST('search_fk_lead','int');
$search_fk_order=GETPOST('search_fk_order','int');

$sql = 'SELECT rowid, labelexcel FROM ' .MAIN_DB_PREFIX . 'c_volvo_genre WHERE labelexcel IS NOT NULL';
$resql = $db->query($sql);
if ($resql){
	$genre_array = array();
	while ($obj=$db->fetch_object($resql)){
		$genre_array[$obj->rowid] = $obj->labelexcel;
	}
}

$sql = 'SELECT rowid, labelexcel FROM ' .MAIN_DB_PREFIX . 'c_volvo_marques WHERE labelexcel IS NOT NULL';
$resql = $db->query($sql);
if ($resql){
	$marque_array = array();
	while ($obj=$db->fetch_object($resql)){
		$marque_array[$obj->rowid] = $obj->labelexcel;
	}
}

$sql = 'SELECT rowid, carrosserie FROM ' .MAIN_DB_PREFIX . 'c_volvo_carrosserie WHERE labelexcel IS NOT NULL';
$resql = $db->query($sql);
if ($resql){
	$carrosserie_array = array();
	while ($obj=$db->fetch_object($resql)){
		$carrosserie_array[$obj->rowid] = $obj->carrosserie;
	}
}

// Load variable for pagination
$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="t.rowid"; // Set here default search field
if (! $sortorder) $sortorder="ASC";

// Protection if external user
$socid=0;
if ($user->societe_id > 0)
{
    $socid = $user->societe_id;
	accessforbidden();
}

// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
$hookmanager->initHooks(array('immatlist'));

// Load object if id or ref is provided as parameter
$object=new Immat($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Definition of fields for list
$arrayfields=array(
't.genre'=>array('label'=>$langs->trans("Genre"), 'checked'=>1),
't.marque'=>array('label'=>$langs->trans("Marque"), 'checked'=>1),
't.carrosserie'=>array('label'=>$langs->trans("Carrosserie"), 'checked'=>1),
't.const_dist'=>array('label'=>$langs->trans("Activité"), 'checked'=>1),
't.ptr'=>array('label'=>$langs->trans("PTR"), 'checked'=>0),
't.gvw'=>array('label'=>$langs->trans("Catégorie PTC"), 'checked'=>1),
't.charutpl'=>array('label'=>$langs->trans("Charge utile"), 'checked'=>0),
't.puissfisc'=>array('label'=>$langs->trans("Puissance Fiscale"), 'checked'=>0),
't.fk_soc'=>array('label'=>$langs->trans("Société"), 'checked'=>1),
's.address'=>array('label'=>$langs->trans("Adresse"), 'checked'=>0),
's.zip'=>array('label'=>$langs->trans("Code Postal"), 'checked'=>1),
's.town'=>array('label'=>$langs->trans("Ville"), 'checked'=>1),
't.csp_prop'=>array('label'=>$langs->trans("Activité"), 'checked'=>1),
't.immat'=>array('label'=>$langs->trans("Immat"), 'checked'=>1),
't.dt_carte_grise'=>array('label'=>$langs->trans("Date Immat"), 'checked'=>1),
't.vin'=>array('label'=>$langs->trans("VIN"), 'checked'=>0),
't.num_serie'=>array('label'=>$langs->trans("N° Série"), 'checked'=>1),
't.modele'=>array('label'=>$langs->trans("Modele"), 'checked'=>1),
't.county'=>array('label'=>$langs->trans("Canton"), 'checked'=>0),
't.fk_user'=>array('label'=>$langs->trans("Commercial"), 'checked'=>1),
);


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if (GETPOST('cancel')) { $action='list'; $massaction=''; }
if (! GETPOST('confirmmassaction')) { $massaction=''; }

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") ||GETPOST("button_removefilter")) // All test are required to be compatible with all browsers
{

$search_genre='';
$search_marque='';
$search_carrosserie='';
$search_const_dist='';
$search_ptr='';
$search_gvw='';
$search_charutpl='';
$search_puissfisc='';
$search_fk_soc='';
$search_address='';
$search_zip='';
$search_town='';
$search_csp_prop='';
$search_dt_carte_grise_DAY='';
$search_dt_carte_grise_MONTH='';
$search_dt_carte_grise_YEAR='';
$search_dt_carte_grise='';
$search_immat='';
$search_vin='';
$search_num_serie='';
$search_modele='';
$search_county='';
$search_fk_user='';
$search_fk_lead='';
$search_fk_order='';
}


if (empty($reshook))
{
    // Mass actions. Controls on number of lines checked
    $maxformassaction=1000;
    if (! empty($massaction) && count($toselect) < 1)
    {
        $error++;
        setEventMessages($langs->trans("NoLineChecked"), null, "warnings");
    }
    if (! $error && count($toselect) > $maxformassaction)
    {
        setEventMessages($langs->trans('TooManyRecordForMassAction',$maxformassaction), null, 'errors');
        $error++;
    }

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/immat/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
			else setEventMessages($object->error,null,'errors');
		}
	}
}


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Immat','');

$form=new Form($db);

// Put here content of your page
$title = $langs->trans('Suivis des immatriculations');

$sql = "SELECT";
$sql .= " t.rowid,";
$sql .= " t.genre,";
$sql .= " g.genre as genre_label,";
$sql .= " t.marque,";
$sql .= " m.marque as marque_label,";
$sql .= " t.carrosserie,";
$sql .= " c.carrosserie as carrosserie_label,";
$sql .= " t.const_dist,";
$sql .= " t.ptr,";
$sql .= " t.gvw,";
$sql .= " t.charutpl,";
$sql .= " t.puissfisc,";
$sql .= " t.fk_soc,";
$sql .= " s.nom,";
$sql .= " s.address,";
$sql .= " s.zip,";
$sql .= " s.town,";
$sql .= " t.csp_prop,";
$sql .= " t.dt_carte_grise,";
$sql .= " t.immat,";
$sql .= " t.vin,";
$sql .= " t.num_serie,";
$sql .= " t.modele,";
$sql .= " t.county,";
$sql .= " t.fk_user,";
$sql .= " t.fk_lead,";
$sql .= " t.fk_order,";
$sql .= " l.ref as lead,";
$sql .= " o.ref as commande";

// Add fields from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListSelect',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.= " FROM ".MAIN_DB_PREFIX."immat as t";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_volvo_genre as g ON g.rowid = t.genre" ;
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_volvo_marques as m ON m.rowid = t.marque" ;
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_volvo_carrosserie as c ON c.rowid = t.carrosserie" ;
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON s.rowid = t.fk_soc" ;
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."lead as l ON l.rowid = t.fk_lead" ;
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."commande as o ON o.rowid = t.fk_order" ;
$sql.= " WHERE 1 = 1";

if ($search_genre>0) $sql.= " AND t.genre = " . $search_genre;
if ($search_marque>0) $sql.= " AND t.marque = " . $search_marque;
if ($search_carrosserie>0) $sql.= " AND t.carrosserie = " . $search_carrosserie;
if ($search_const_dist) $sql.= natural_search("t.const_dist",$search_const_dist);
if ($search_ptr) $sql.= natural_search("t.ptr",$search_ptr);
if ($search_gvw) $sql.= natural_search("t.gvw",$search_gvw);
if ($search_charutpl) $sql.= natural_search("t.charutpl",$search_charutpl);
if ($search_puissfisc) $sql.= natural_search("t.puissfisc",$search_puissfisc);
if ($search_fk_soc>0) $sql.= " AND t.fk_soc = " . $search_fk_soc;
if ($search_address) $sql.= natural_search("s.address",$search_address);
if ($search_zip) $sql.= natural_search("s.zip",$search_zip);
if ($search_town) $sql.= natural_search("s.town",$search_town);
if ($search_csp_prop) $sql.= natural_search("t.csp_prop",$search_csp_prop);
if ($search_dt_carte_grise) $sql.=natural_search('t.dt_carte_grise', $search_dt_carte_grise);
if ($search_immat) $sql.= natural_search("t.immat",$search_immat);
if ($search_vin) $sql.= natural_search("t.vin",$search_vin);
if ($search_num_serie) $sql.= natural_search("t.num_serie",$search_num_serie);
if ($search_modele) $sql.= natural_search("t.modele",$search_modele);
if ($search_county) $sql.= natural_search("t.county",$search_county);
if ($search_fk_user>0) $sql.= " AND t.fk_user = " . $search_fk_user;
if ($search_fk_lead) $sql.= " AND t.fk_lead = " . $search_fk_lead;
if ($search_fk_order) $sql.= " AND t.fk_order = " . $search_fk_order;

// Add where from hooks
$parameters=array();
$reshook=$hookmanager->executeHooks('printFieldListWhere',$parameters);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;
$sql.=$db->order($sortfield,$sortorder);


// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($conf->liste_limit+1, $offset);


dol_syslog($script_file, LOG_DEBUG);
$resql=$db->query($sql);

if ($resql)
{
    $num = $db->num_rows($resql);

    $params='';
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;

	if ($search_genre != '' && $search_genre != -1) $params.= '&amp;search_genre='.urlencode($search_genre);
	if ($search_marque != '' && $search_marque != -1) $params.= '&amp;search_marque='.urlencode($search_marque);
	if ($search_carrosserie != '') $params.= '&amp;search_carrosserie='.urlencode($search_carrosserie);
	if ($search_const_dist != '') $params.= '&amp;search_const_dist='.urlencode($search_const_dist);
	if ($search_ptr != '') $params.= '&amp;search_ptr='.urlencode($search_ptr);
	if ($search_gvw != '') $params.= '&amp;search_gvw='.urlencode($search_gvw);
	if ($search_charutpl != '') $params.= '&amp;search_charutpl='.urlencode($search_charutpl);
	if ($search_puissfisc != '') $params.= '&amp;search_puissfisc='.urlencode($search_puissfisc);
	if ($search_fk_soc != '' && $search_fk_soc != -1) $params.= '&amp;search_fk_soc='.urlencode($search_fk_soc);
	if ($search_address != '') $params.= '&amp;search_address='.urlencode($search_address);
	if ($search_zip != '') $params.= '&amp;search_zip='.urlencode($search_zip);
	if ($search_town != '') $params.= '&amp;search_town='.urlencode($search_town);
	if ($search_csp_prop != '') $params.= '&amp;search_csp_prop='.urlencode($search_csp_prop);
	if ($search_dt_carte_grise_DAY != '') $params.= '&amp;search_dt_carte_grise_DAY='.urlencode($search_dt_carte_grise_DAY);
	if ($search_dt_carte_grise_MONTH != '') $params.= '&amp;search_dt_carte_grise_MONTH='.urlencode($search_dt_carte_grise_MONTH);
	if ($search_dt_carte_grise_YEAR != '') $params.= '&amp;search_dt_carte_grise_YEAR='.urlencode($search_dt_carte_grise_YEAR);
	if ($search_dt_carte_grise != '') $params.= '&amp;search_dt_carte_grise='.urlencode($search_dt_carte_grise);
	if ($search_immat != '') $params.= '&amp;search_immat='.urlencode($search_immat);
	if ($search_vin != '') $params.= '&amp;search_vin='.urlencode($search_vin);
	if ($search_num_serie != '') $params.= '&amp;search_num_serie='.urlencode($search_num_serie);
	if ($search_modele != '') $params.= '&amp;search_modele='.urlencode($search_modele);
	if ($search_county != '') $params.= '&amp;search_county='.urlencode($search_county);
	if ($search_fk_user != '') $params.= '&amp;search_fk_user='.urlencode($search_fk_user);
	if ($search_fk_lead != '') $params.= '&amp;search_fk_lead='.urlencode($search_fk_lead);
	if ($search_fk_order != '') $params.= '&amp;search_fk_order='.urlencode($search_fk_order);

    if ($optioncss != '') $param.='&optioncss='.$optioncss;

    print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $params, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

	print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

    $varpage=empty($contextpage)?$_SERVER["PHP_SELF"]:$contextpage;
    $selectedfields=$form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);	// This also change content of $arrayfields

	print '<table class="liste '.($moreforfilter?"listwithfilterbefore":"").'">';

    // Fields title
    print '<tr class="liste_titre">';
    print_liste_field_titre('ID',$_SERVER['PHP_SELF'],'t.rowid','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.genre']['checked'])) print_liste_field_titre($arrayfields['t.genre']['label'],$_SERVER['PHP_SELF'],'t.genre','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.marque']['checked'])) print_liste_field_titre($arrayfields['t.marque']['label'],$_SERVER['PHP_SELF'],'t.marque','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.carrosserie']['checked'])) print_liste_field_titre($arrayfields['t.carrosserie']['label'],$_SERVER['PHP_SELF'],'t.carrosserie','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.const_dist']['checked'])) print_liste_field_titre($arrayfields['t.const_dist']['label'],$_SERVER['PHP_SELF'],'t.const_dist','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.ptr']['checked'])) print_liste_field_titre($arrayfields['t.ptr']['label'],$_SERVER['PHP_SELF'],'t.ptr','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.gvw']['checked'])) print_liste_field_titre($arrayfields['t.gvw']['label'],$_SERVER['PHP_SELF'],'t.gvw','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.charutpl']['checked'])) print_liste_field_titre($arrayfields['t.charutpl']['label'],$_SERVER['PHP_SELF'],'t.charutpl','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.puissfisc']['checked'])) print_liste_field_titre($arrayfields['t.puissfisc']['label'],$_SERVER['PHP_SELF'],'t.puissfisc','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.fk_soc']['checked'])) print_liste_field_titre($arrayfields['t.fk_soc']['label'],$_SERVER['PHP_SELF'],'t.fk_soc','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['s.address']['checked'])) print_liste_field_titre($arrayfields['s.address']['label'],$_SERVER['PHP_SELF'],'s.address','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['s.zip']['checked'])) print_liste_field_titre($arrayfields['s.zip']['label'],$_SERVER['PHP_SELF'],'s.zip','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['s.town']['checked'])) print_liste_field_titre($arrayfields['s.town']['label'],$_SERVER['PHP_SELF'],'s.town','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.csp_prop']['checked'])) print_liste_field_titre($arrayfields['t.csp_prop']['label'],$_SERVER['PHP_SELF'],'t.csp_prop','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.dt_carte_grise']['checked'])) print_liste_field_titre($arrayfields['t.dt_carte_grise']['label'],$_SERVER['PHP_SELF'],'t.dt_carte_grise','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.immat']['checked'])) print_liste_field_titre($arrayfields['t.immat']['label'],$_SERVER['PHP_SELF'],'t.immat','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.vin']['checked'])) print_liste_field_titre($arrayfields['t.vin']['label'],$_SERVER['PHP_SELF'],'t.vin','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.num_serie']['checked'])) print_liste_field_titre($arrayfields['t.num_serie']['label'],$_SERVER['PHP_SELF'],'t.num_serie','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.modele']['checked'])) print_liste_field_titre($arrayfields['t.modele']['label'],$_SERVER['PHP_SELF'],'t.modele','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.county']['checked'])) print_liste_field_titre($arrayfields['t.county']['label'],$_SERVER['PHP_SELF'],'t.county','',$param,'',$sortfield,$sortorder);
    if (! empty($arrayfields['t.fk_user']['checked'])) print_liste_field_titre($arrayfields['t.fk_user']['label'],$_SERVER['PHP_SELF'],'t.fk_user','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre('Affaire',$_SERVER['PHP_SELF'],'t.fk_lead','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre('Commande',$_SERVER['PHP_SELF'],'t.fk_order','',$param,'',$sortfield,$sortorder);

    // Hook fields
	$parameters=array('arrayfields'=>$arrayfields);
    $reshook=$hookmanager->executeHooks('printFieldListTitle',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
	print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"],"",'','','align="right"',$sortfield,$sortorder,'maxwidthsearch ');
    print '</tr>'."\n";

    // Fields title search
	print '<tr class="liste_titre">';
print '<TD> </TD>';
if (! empty($arrayfields['t.genre']['checked'])) print '<td class="liste_titre">' . $form->selectarray('search_genre', $genre_array,$search_genre,1) . '</td>';
if (! empty($arrayfields['t.marque']['checked'])) print '<td class="liste_titre">' . $form->selectarray('search_marque', $marque_array,$search_marque,1) . '</td>';
if (! empty($arrayfields['t.carrosserie']['checked'])) print '<td class="liste_titre">' . $form->selectarray('search_carrosserie', $carrosserie_array,$search_carrosserie,1) . '</td>';
if (! empty($arrayfields['t.const_dist']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_const_dist" value="'.$search_const_dist.'" size="8"></td>';
if (! empty($arrayfields['t.ptr']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_ptr" value="'.$search_ptr.'" size="5"></td>';
if (! empty($arrayfields['t.gvw']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_gvw" value="'.$search_gvw.'" size="8"></td>';
if (! empty($arrayfields['t.charutpl']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_charutpl" value="'.$search_charutpl.'" size="5"></td>';
if (! empty($arrayfields['t.puissfisc']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_puissfisc" value="'.$search_puissfisc.'" size="5"></td>';
if (! empty($arrayfields['t.fk_soc']['checked'])) print '<td class="liste_titre">' . $form->select_thirdparty_list($search_fk_soc,'search_fk_soc','',1,0,0,array(),'',0,0,'minwidth100','style="width: 95%"') . '</td>';
if (! empty($arrayfields['s.address']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_address" value="'.$search_adress.'" size="20"></td>';
if (! empty($arrayfields['s.zip']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_zip" value="'.$search_zip.'" size="4"></td>';
if (! empty($arrayfields['s.town']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_town" value="'.$search_town.'" size="15"></td>';
if (! empty($arrayfields['t.csp_prop']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_csp_prop" value="'.$search_csp_prop.'" size="15"></td>';
if (! empty($arrayfields['t.dt_carte_grise']['checked'])) print '<td class="liste_titre">' . $form->select_date($search_dt_carte_grise, 'search_dt_carte_grise_',0,0,1,'',1,0,1) . '</td>';
if (! empty($arrayfields['t.immat']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_immat" value="'.$search_immat.'" size="6"></td>';
if (! empty($arrayfields['t.vin']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_vin" value="'.$search_vin.'" size="13"></td>';
if (! empty($arrayfields['t.num_serie']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_num_serie" value="'.$search_num_serie.'" size="6"></td>';
if (! empty($arrayfields['t.modele']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_modele" value="'.$search_modele.'" size="7"></td>';
if (! empty($arrayfields['t.county']['checked'])) print '<td class="liste_titre"><input type="text" class="flat" name="search_county" value="'.$search_county.'" size="4"></td>';
if (! empty($arrayfields['t.fk_user']['checked'])) print '<td class="liste_titre">' . $form->select_dolusers($search_fk_user,'search_fk_user',1) . '</td>';
print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_lead" value="'.$search_fk_lead.'" size="10"></td>';
print '<td class="liste_titre"><input type="text" class="flat" name="search_fk_order" value="'.$search_fk_order.'" size="10"></td>';



    // Fields from hook
	$parameters=array('arrayfields'=>$arrayfields);
    $reshook=$hookmanager->executeHooks('printFieldListOption',$parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;

    // Action column
	print '<td class="liste_titre" align="right">';
    $searchpitco=$form->showFilterAndCheckAddButtons(0);
    print $searchpitco;
    print '</td>';
	print '</tr>'."\n";


	$i=0;
	$var=true;
	$totalarray=array();
    while ($i < min($num, $limit))
    {
        $obj = $db->fetch_object($resql);
        if ($obj)
        {
            // You can use here results
            $is_to_fill=0;
            if($obj->marque==1 && $obj->fk_order==0){
            	$is_to_fill = 1;
            }
            $bouton = 0;

            $soc = New Societe($db);
            $commercial = New User($db);
            $lead = new Leadext($db);
            $cmd = new Commande($db);

            $sql2 = 'SELECT rowid, ref  FROM ' .MAIN_DB_PREFIX . 'lead WHERE fk_soc = ' . $obj->fk_soc ;
            $resql2 = $db->query($sql2);
            if ($resql2){
            	$lead_array = array();
              	while ($obj2=$db->fetch_object($resql2)){
              		$lead_array[$obj2->rowid] = $obj2->ref;
              	}
            }



            print '<tr>';
            print '<td>' . $obj->rowid . '</td>';
            if (! empty($arrayfields['t.genre']['checked']))
            {
                print '<td>'.$obj->genre_label.'</td>';
    		    if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.marque']['checked']))
            {
            	print '<td>'.$obj->marque_label.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.carrosserie']['checked']))
            {
            	print '<td>'.$obj->carrosserie_label.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.const_dist']['checked']))
            {
            	print '<td>'.$obj->const_dist.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.ptr']['checked']))
            {
            	print '<td>'.$obj->ptr.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.gvw']['checked']))
            {
            	print '<td>'.$obj->gvw.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.charutpl']['checked']))
            {
            	print '<td>'.$obj->charutpl.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.puissfisc']['checked']))
            {
            	print '<td>'.$obj->puissfisc.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.fk_soc']['checked']))
            {
            	$res = $soc->fetch($obj->fk_soc);
            	print '<td>' . $soc->getNomUrl(1) .'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['s.address']['checked']))
            {
            	print '<td>'.$obj->address.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['s.zip']['checked']))
            {
            	print '<td>'.$obj->zip.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['s.town']['checked']))
            {
            	print '<td>'.$obj->town.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.csp_prop']['checked']))
            {
            	print '<td>'.$obj->csp_prop.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.dt_carte_grise']['checked']))
            {
            	print '<td>'.$obj->dt_carte_grise.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.immat']['checked']))
            {
            	print '<td>'.$obj->immat.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.vin']['checked']))
            {
            	print '<td>'.$obj->vin.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.num_serie']['checked']))
            {
            	print '<td>'.$obj->num_serie.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.modele']['checked']))
            {
            	print '<td>'.$obj->modele.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.county']['checked']))
            {
            	print '<td>'.$obj->county.'</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if (! empty($arrayfields['t.fk_user']['checked']))
            {
            	$res = $commercial->fetch($obj->fk_user);
            	print '<td>' .  $commercial->getNomUrl(1) . '</td>';
            	if (! $i) $totalarray['nbfield']++;
            }
            if ($obj->fk_lead > 0){
            	$lead->fetch($obj->fk_lead);
	            print '<td>'. $lead->getNomUrl(1) .'</td>';
            }else{
            	if (count($lead_array)>0){
            		print '<td>' . $form->selectarray('lead' . $obj->rowid, $lead_array,'',1) . '</td>';
            	} else {
            		print '<td> No lead Found </td>';
            	}
            	print '<td>select de lead</td>'  ;
    			$bouton = 1;
            }
            if (! $i) $totalarray['nbfield']++;

            if ($is_to_fill == 1){
            	print '<td>select de order</td>';
            	$bouton = 1;
            }else{
    			if ($obj->fk_order > 0){
	    			$cmd->fetch($obj->fk_order);
    				print '<td>'.$cmd->getNomUrl(1).'</td>';
    			}else{
    				print '<td> </td>';
    			}
            }
            if (! $i) $totalarray['nbfield']++;

            // Fields from hook
    	    $parameters=array('arrayfields'=>$arrayfields, 'obj'=>$obj);
    		$reshook=$hookmanager->executeHooks('printFieldListValue',$parameters);    // Note that $action and $object may have been modified by hook
            print $hookmanager->resPrint;

            // Action column

            if($bouton>0){
            	print '<td>un petit bouton la</td>';
            } else {
            	print '<td></td>';
            }

            if (! $i) $totalarray['nbfield']++;

            print '</tr>';
        }
        $i++;
    }

    $db->free($resql);

	$parameters=array('sql' => $sql);
	$reshook=$hookmanager->executeHooks('printFieldListFooter',$parameters);    // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print "</table>\n";
	print "</form>\n";

	$db->free($result);
}
else
{
    $error++;
    dol_print_error($db);
}


// End of page
llxFooter();
$db->close();

