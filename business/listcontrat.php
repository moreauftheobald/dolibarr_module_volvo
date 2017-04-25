<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2013      Cédric Salvador      <csalvador@gpcsolutions.fr>
 * Copyright (C) 2014      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2015	   Claudio Aschieri		<c.aschieri@19.coop>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2016      Ferran Marcet        <fmarcet@2byte.es>
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
 *       \file       htdocs/contrat/list.php
 *       \ingroup    contrat
 *       \brief      Page liste des contrats
 */

require ("../../main.inc.php");
require_once (DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

$langs->load("contracts");
$langs->load("products");
$langs->load("companies");
$langs->load("compta");

$search_name=GETPOST('search_name');
$search_contract=GETPOST('search_contract');
$search_ref_supplier=GETPOST('search_ref_supplier','alpha');
$search_ref_customer=GETPOST('search_ref_customer','alpha');
$search_status=GETPOST('search_status');
$socid=GETPOST('socid');
$search_user=GETPOST('search_user','int');
$search_sale=GETPOST('search_sale','int');
$search_product_category=GETPOST('search_product_category','int');
$day=GETPOST("day","int");
$year=GETPOST("year","int");
$month=GETPOST("month","int");
$action = GETPOST('action');
$element = GETPOST('element');
$id=GETPOST('id');

$optioncss = GETPOST('optioncss','alpha');

$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield='c.ref';
if (! $sortorder) $sortorder='DESC';

// Security check
if (! $user->rights->volvo->contrat)
	accessforbidden();



$staticcontrat=new Contrat($db);
$staticcontratligne=new ContratLigne($db);

$contextpage='contractlist';

/*
 * Action
 */

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter.x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
{
	$search_name="";
	$search_contract="";
	$search_ref_supplier="";
	$search_ref_customer="";
    $search_user='';
    $search_sale='';
    $search_product_category='';
	$sall="";
	$search_status="";
	$search_array_options=array();
	$day='';
	$month='';
	$year='';
}
$search_sale_disabled = 0;
if (empty($user->rights->volvo->stat_all)){
	$search_sale = $user->id;
	$search_sale_disabled = 1;
}

$user_included=array();
$sqlusers = "SELECT fk_user FROM " . MAIN_DB_PREFIX . "usergroup_user WHERE fk_usergroup = 1";
$resqlusers  = $db->query($sqlusers);
if($resqlusers){
	while ($users = $db->fetch_object($resqlusers)){
		$user_included[] = $users->fk_user;
	}
}

if($action=='confirm_set_date'){
	$contrat = New Contrat($db);
	$contrat->fetch($id);
	$contrat->array_options['options_' . $element]=dol_mktime(0, 0, 0, GETPOST('date_actionmonth'), GETPOST('date_actionday'), GETPOST('date_actionyear'));
	$contrat->insertExtraFields();
}


/*
 * View
 */

$now=dol_now();
$form=new Form($db);
$formother = new FormOther($db);
$socstatic = new Societe($db);

llxHeader();

$sql = 'SELECT';
$sql.= " c.rowid as cid, c.ref, c.datec, c.date_contrat, c.statut, c.ref_customer, c.ref_supplier,";
$sql.= " s.nom as name, s.rowid as socid, ef.dt_env_cli as dt_env_cli, ef.dt_ret_cli as dt_ret_cli, ef.dt_sig_the as dt_sig_the, ef.dt_env_vtf as dt_env_vtf, ef.dt_enr as dt_enr,";
$sql.= " ef.dt_ret_vtf as dt_ret_vtf, ef.dt_trait as dt_trait,";
$sql.= ' SUM('.$db->ifsql("cd.statut=0",1,0).') as nb_initial,';
$sql.= ' SUM('.$db->ifsql("cd.statut=4 AND (cd.date_fin_validite IS NULL OR cd.date_fin_validite >= '".$db->idate($now)."')",1,0).') as nb_running,';
$sql.= ' SUM('.$db->ifsql("cd.statut=4 AND (cd.date_fin_validite IS NOT NULL AND cd.date_fin_validite < '".$db->idate($now)."')",1,0).') as nb_expired,';
$sql.= ' SUM('.$db->ifsql("cd.statut=4 AND (cd.date_fin_validite IS NOT NULL AND cd.date_fin_validite < '".$db->idate($now - $conf->contrat->services->expires->warning_delay)."')",1,0).') as nb_late,';
$sql.= ' SUM('.$db->ifsql("cd.statut=5",1,0).') as nb_closed';
$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
if ($search_sale > 0 || (! $user->rights->societe->client->voir && ! $socid)) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
$sql.= ", ".MAIN_DB_PREFIX."contrat as c";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contratdet as cd ON c.rowid = cd.fk_contrat";
if ($search_product_category > 0) $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON cp.fk_product=cd.fk_product';
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."contrat_extrafields AS ef ON c.rowid = ef.fk_object";
$sql.= " WHERE c.fk_soc = s.rowid ";
$sql.= ' AND c.entity IN ('.getEntity('contract', 1).')';
if ($search_product_category > 0) $sql.=" AND cp.fk_categorie = ".$search_product_category;
if ($socid) $sql.= " AND s.rowid = ".$db->escape($socid);
if (!$user->rights->societe->client->voir && !$socid) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
if ($month > 0)
{
    if ($year > 0 && empty($day))
    $sql.= " AND ef.dt_enr BETWEEN '".$db->idate(dol_get_first_day($year,$month,false))."' AND '".$db->idate(dol_get_last_day($year,$month,false))."'";
    else if ($year > 0 && ! empty($day))
    $sql.= " AND ef.dt_enr BETWEEN '".$db->idate(dol_mktime(0, 0, 0, $month, $day, $year))."' AND '".$db->idate(dol_mktime(23, 59, 59, $month, $day, $year))."'";
    else
    $sql.= " AND date_format(ef.dt_enr, '%m') = '".$month."'";
}
else if ($year > 0)
{
	$sql.= " AND ef.dt_enr BETWEEN '".$db->idate(dol_get_first_day($year,1,false))."' AND '".$db->idate(dol_get_last_day($year,12,false))."'";
}
if ($search_contract) $sql .= natural_search(array('c.rowid', 'c.ref'), $search_contract);
if (!empty($search_ref_supplier)) $sql .= natural_search(array('c.ref_supplier'), $search_ref_supplier);
if (!empty($search_ref_customer)) $sql .= natural_search(array('c.ref_customer'), $search_ref_customer);
if (!empty($search_name)) $sql .= natural_search(array('s.nom'), $search_name);
if ($search_sale > 0)
{
	$sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$search_sale;
}
if($search_status == 1){
	$sql.= " AND ef.dt_env_cli IS NULL";
}elseif($search_status == 2){
	$sql.= " AND ef.dt_env_cli IS NOT NULL AND ef.dt_ret_cli IS NULL";
}elseif($search_status == 3){
	$sql.= " AND ef.dt_ret_cli IS NOT NULL AND ef.dt_sig_the IS NULL";
}elseif($search_status == 4){
	$sql.= " AND ef.dt_sig_the IS NOT NULL AND ef.dt_env_vtf IS NULL";
}elseif($search_status == 5){
	$sql.= " AND ef.dt_env_vtf IS NOT NULL AND ef.dt_enr IS NULL";
}elseif($search_status == 6){
	$sql.= " AND ef.dt_enr IS NOT NULL AND ef.dt_ret_vtf IS NULL";
}elseif($search_status == 7){
	$sql.= " AND ef.dt_enr IS NOT NULL AND ef.dt_ret_vtf IS NOT NULL AND ef.dt_trait IS NULL";
}elseif($search_status == 8){
	$sql.= " AND ef.dt_enr IS NOT NULL AND ef.dt_ret_vtf IS NOT NULL AND ef.dt_trait IS NOT NULL";
}
$sql.= " GROUP BY c.rowid, c.ref, c.datec, c.date_contrat, c.statut, c.ref_customer, c.ref_supplier, s.nom, s.rowid";

$totalnboflines=0;
$result=$db->query($sql);
if ($result)
{
    $totalnboflines = $db->num_rows($result);
}
$sql.= $db->order($sortfield,$sortorder);

$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
    $result = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($result);
}

$sql.= $db->plimit($limit + 1, $offset);

$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    $i = 0;

    $param='';
    if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.$contextpage;
    if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.$limit;
    if ($search_contract != '')     $param.='&search_contract='.$search_contract;
    if ($search_name != '')         $param.='&search_name='.$search_name;
    if ($search_ref_supplier != '') $param.='&search_ref_supplier='.$search_ref_supplier;
    if ($search_ref_customer != '') $param.='&search_ref_customer='.$search_ref_customer;
    if ($search_sale != '')         $param.='&search_sale=' .$search_sale;
	if ($search_status !='')		$param.='&search_status=' .$search_status;


	if ($action == 'set_date') {
		$form = new Form($db);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='. $id . '&element=' . $element . $param, "Valider et passer a l'étape suivante", '', 'confirm_set_date', array(array(
				'type' => 'date',
				'name' => 'date_action',
				'label'=> "date de l'action"
		)), '', 1);
	}

	if(!empty($formconfirm)) print $formconfirm;




    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

    print_barre_liste($langs->trans("ListOfContracts"), $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder,'',$num,$totalnboflines,'title_commercial.png', 0, '', '', $limit);

	print '<table class="tagtable liste">';
    print '<tr class="liste_titre">';

    print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "c.ref","","$param",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Immatriculation"), $_SERVER["PHP_SELF"], "c.ref_customer","","$param",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Chassis"), $_SERVER["PHP_SELF"], "c.ref_supplier","","$param",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Client"), $_SERVER["PHP_SELF"], "s.nom","","$param",'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("SalesRepresentative"), $_SERVER["PHP_SELF"], "","","$param",'',$sortfield,$sortorder);
    print '<th align="center">' . $langs->trans("Status") . '</th>';
    print_liste_field_titre($langs->trans("Date d'enr."), $_SERVER["PHP_SELF"], "c.date_contrat","","$param",'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($staticcontratligne->LibStatut(0,3), '', '', '', '', 'width="16"');
    print_liste_field_titre($staticcontratligne->LibStatut(4,3,0), '', '', '', '', 'width="16"');
    print_liste_field_titre($staticcontratligne->LibStatut(4,3,1), '', '', '', '', 'width="16"');
    print_liste_field_titre($staticcontratligne->LibStatut(5,3), '', '', '', '', 'width="16"');
    print '<td class="liste_titre"><input type="image" class="liste_titre" name="button_export" src="' . DOL_URL_ROOT . '/theme/common/mime/xls.png" value="export" title="Exporter"></td>';
    //print_liste_field_titre('',$_SERVER["PHP_SELF"],"",'','','',$sortfield,$sortorder,'maxwidthsearch ');
    print "</tr>\n";

    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<tr class="liste_titre">';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="10" name="search_contract" value="'.dol_escape_htmltag($search_contract).'">';
    print '</td>';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="7" name="search_ref_customer" value="'.dol_escape_htmltag($search_ref_customer).'">';
    print '</td>';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="7" name="search_ref_supplier" value="'.dol_escape_htmltag($search_ref_supplier).'">';
    print '</td>';
    print '<td class="liste_titre">';
    print '<input type="text" class="flat" size="15" name="search_name" value="'.dol_escape_htmltag($search_name).'">';
    print '</td>';
    print '<td>';
    print $form->select_dolusers($search_sale,'search_sale',1,array(),$search_sale_disabled,$user_included);
    print '</td>';
    print '<td>';
    print '<select class="flat" id="search_status" name="search_status">';
    print '<option value="0"'.(empty($search_status)?' selected':'').'> </option>';
    print '<option value="1"'.($search_status==1?' selected':'').'>En attente envoi Client</option>';
    print '<option value="2"'.($search_status==2?' selected':'').'>chez le Client</option>';
    print '<option value="3"'.($search_status==3?' selected':'').'>En cours de signature Théobald</option>';
    print '<option value="4"'.($search_status==4?' selected':'').'>En attente envoi VTF</option>';
    print '<option value="5"'.($search_status==5?' selected':'').'>En cours d\'enregistrment VTF</option>';
    print '<option value="6"'.($search_status==6?' selected':'').'>Enregistré En attente retour VTF</option>';
    print '<option value="7"'.($search_status==7?' selected':'').'>Enregistré recu a traiter</option>';
    print '<option value="8"'.($search_status==8?' selected':'').'>Enregistré</option>';
    print '</select>';
    print '</td>';
    // Date contract
    print '<td class="liste_titre center">';
  	print '<input class="flat" type="text" size="1" maxlength="2" name="month" value="'.$month.'">';
   	$syear = $year;
   	$formother->select_year($syear,'year',1, 20, 5);
    print '</td>';

    print '<td class="liste_titre" colspan="4" align="right"></td>';
    print '<td>';

    $searchpitco=$form->showFilterAndCheckAddButtons(0);
    print $searchpitco;
    print '</td>';
    print "</tr>\n";

    $var=true;
    while ($i < min($num,$limit))
    {
        $obj = $db->fetch_object($resql);
        $statut = '';
        if (empty($obj->dt_env_cli)){
        	$statut = 'En attente envoi Client';
        	$action_element = 'dt_env_cli';
        }elseif(!empty($obj->dt_env_cli) && empty($obj->dt_ret_cli)){
        	$statut = 'chez le Client';
        	$action_element = 'dt_ret_cli';
        }elseif(!empty($obj->dt_ret_cli) && empty($obj->dt_sig_the)){
        	$statut = 'En cours de signature Théobald';
        	$action_element = 'dt_sig_the';
        }elseif(!empty($obj->dt_sig_the) && empty($obj->dt_env_vtf)){
        	$statut = 'En attente envoi VTF';
        	$action_element = 'dt_env_vtf';
        }elseif(!empty($obj->dt_env_vtf) && empty($obj->dt_enr)){
        	$statut = "En cours d'enregistrment VTF";
        	$action_element = 'dt_enr';
        }elseif(!empty($obj->dt_enr)){
        	$statut = 'Enregistré';
        	$action_element='none';
        	if(empty($obj->dt_ret_vtf)){
        		$statut.= ' En attente retour VTF';
        		$action_element = 'dt_ret_vtf';
        	}elseif(!empty($obj->dt_ret_vtf) && empty($obj->dt_trait)){
        		$statut.= ' recu a traiter';
        		$action_element = 'dt_trait';
        	}
        }


        $var=!$var;
        print '<tr '.$bc[$var].'>';
        print '<td class="nowrap"><a href="../../contrat/card.php?id='.$obj->cid.'">';
        print img_object($langs->trans("ShowContract"),"contract").' '.(isset($obj->ref) ? $obj->ref : $obj->cid) .'</a>';
        if ($obj->nb_late) print img_warning($langs->trans("Late"));
        print '</td>';
        print '<td>'.$obj->ref_customer.'</td>';
        print '<td>'.$obj->ref_supplier.'</td>';
        print '<td><a href="../../comm/card.php?socid='.$obj->socid .'">'.img_object($langs->trans("ShowCompany"),"company").' '.$obj->name.'</a></td>';
         // Sales Rapresentatives
        print '<td>';
        if($obj->socid)
        {
        	$result=$socstatic->fetch($obj->socid);
        	if ($result < 0)
        	{
        		dol_print_error($db);
        		exit;
        	}
        	$listsalesrepresentatives=$socstatic->getSalesRepresentatives($user);
        	if ($listsalesrepresentatives < 0) dol_print_error($db);
        	$nbofsalesrepresentative=count($listsalesrepresentatives);
        	if ($nbofsalesrepresentative > 3)   // We print only number
        	{
        		print '<a href="'.DOL_URL_ROOT.'../../societe/commerciaux.php?socid='.$socstatic->id.'">';
        		print $nbofsalesrepresentative;
        		print '</a>';
        	}
        	else if ($nbofsalesrepresentative > 0)
        	{
        		$userstatic=new User($db);
        		$j=0;
        		foreach($listsalesrepresentatives as $val)
        		{
        			$userstatic->id=$val['id'];
        			$userstatic->lastname=$val['lastname'];
        			$userstatic->firstname=$val['firstname'];
        			print '<div class="float">'.$userstatic->getNomUrl(1);
        			$j++;
        			if ($j < $nbofsalesrepresentative) print ', ';
        			print '</div>';
        		}
        	}
        	//else print $langs->trans("NoSalesRepresentativeAffected");
        }
        else
        {
        	print '&nbsp';
        }
        print '</td>';

        print '<td align="center">';
        if($action_element != 'none'){
        	print '<a href="'.DOL_URL_ROOT.'/volvo/business/listcontrat.php?id='.$obj->cid . '&action=set_date&element='.$action_element. $param .'">';
        	print img_object("Action","cron");
        	print '</a>';
        }
        print ' ' . $statut;
        print '</td>';
        print '<td align="center">'.dol_print_date($db->jdate($obj->dt_enr), 'day').'</td>';
        print '<td align="center">'.($obj->nb_initial>0?$obj->nb_initial:'').'</td>';
        print '<td align="center">'.($obj->nb_running>0?$obj->nb_running:'').'</td>';
        print '<td align="center">'.($obj->nb_expired>0?$obj->nb_expired:'').'</td>';
        print '<td align="center">'.($obj->nb_closed>0 ?$obj->nb_closed:'').'</td>';
       	print '<td></td>';
        print "</tr>\n";
        $i++;
    }
    $db->free($resql);

    print '</table>';
    print '</form>';
}
else
{
    dol_print_error($db);
}


llxFooter();
$db->close();
