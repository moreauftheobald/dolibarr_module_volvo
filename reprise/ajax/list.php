<?php
/* Volvo
 * Copyright (C) 2015	Florian HENRY 		<florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 * \file volvo/reprise/ajax/list.php
 * \brief File to return datables output
 */
if (! defined('NOTOKENRENEWAL'))
	define('NOTOKENRENEWAL', '1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))
	define('NOREQUIREMENU', '1');
	// if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))
	define('NOREQUIREAJAX', '1');
	// if (! defined('NOREQUIRESOC')) define('NOREQUIRESOC','1');
	// if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');

$res = @include ("../../../main.inc.php"); // For root directory
if (! $res)
	$res = @include ("../../../../main.inc.php"); // For "custom" directory

dol_include_once('/volvo/class/reprise.class.php');
dol_include_once('/societe/class/societe.class.php');

$langs->load("volvo@volvo");

top_httphead();

$object = new Reprise($db);

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
if($user->rights->volvo->admin) {
	$aColumns = array(
		'id',
		'ref',
		'status',
		'ex_client',
		'genre',
		'marque',
		'type',
		'silouhette',
		'puissance',
		'cabine',
		'bv',
		'moteur',
		'capago',
		'ptc',
		'norme',
		'km',
		'op',
		'immat',
		'vin',
		'dt_1_circ',
		'',
		'estim',
		'rachat',
		'date_reception',
		'site',
		'nbj_stock',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'date_vente',
		'buyer',
		'dt_fac_ach',
		'pays_vente',
		'type_ach',
		'financeur'
	);
}else{
	$aColumns = array(
		'id',
		'ref',
		'status',
		'ex_client',
		'genre',
		'marque',
		'type',
		'silouhette',
		'puissance',
		'cabine',
		'bv',
		'moteur',
		'capago',
		'ptc',
		'norme',
		'km',
		'op',
		'immat',
		'vin',
		'dt_1_circ',
		'estim',
		'rachat',
		'date_reception',
		'site',
		'nbj_stock',
		'',
		'date_vente',
		'buyer',
		'dt_fac_ach',
		'pays_vente',
		'type_ach',
		'financeur'
	);
}
$filter_rplc =  array(
		'REF' => 'ref',
		'STA' => 'status',
		'PRO' => 'ex_client',
		'GEN' => 'genre',
		'SIL' => 'silouhette',
		'MRQ' => 'marque',
		'TYP' => 'type',
		'PCO' => 'puissance',
		'CAB' => 'cabine',
		'BV' => 'bv',
		'MOT' => 'moteur',
		'CGO' => 'capago',
		'PTC' => 'ptc',
		'EUR' => 'norme',
		'KMS' => 'km',
		'OPT' => 'op',
		'IMM' => 'immat',
		'VIN' => 'vin',
		'MEC' => 'dt_1_circ',
		'EST' => 'estim',
		'OAC' => 'rachat',
		'DST' => 'date_reception',
		'SIT' => 'site',
		'NJS' => 'nbj_stock',
		'DTV' => 'date_vente',
		'ACH' => 'buyer',
		'DTA' => 'dt_fac_ach',
		'PAV' => 'pays_vente',
		'TAC' => 'type_ach',
		'FIN' => 'financeur'

);

$limit = 0;
$offset = 0;

$sortorder = 'DESC';
$sortfield = 'r.rowid';

// Build filter array
$filter_array = array();

$search_string = urldecode(GETPOST('ssSearch'));
$ssStatus = urldecode(GETPOST('ssStatus'));
$ssStatus_array = unserialize($ssStatus);

 if(!empty($ssStatus_array)){
 	$filter = '(';
 	foreach ($ssStatus_array as $sta){
 		$filter.=$sta .',';
 	}
 	$filter = substr($filter, 0,-1);
 	$filter.= ')';
 	$filter_array['status'] = $filter;
 }

if ($search_string != "") {
	$search_string = str_replace('=>', ' AND ', $search_string);
	Foreach($filter_rplc as $key => $value){
		$search_string = str_replace($key, $value, $search_string);
	}
	$filterstrings = array();
	$filterstrings = explode("\r", $search_string);
 	if(count($filterstrings)>0){
 		foreach ($filterstrings as $filterelement){
 			if(strpos($filterelement, ':')>1){
 				$filter = explode(':', $filterelement);
 				$filter_array[trim($filter[0])]=trim($filter[1]);
 			}
 		}
 	}
}

/* Total data set length */
$result = $object->fetchAllforlist($sortorder, $sortfield, 0, 0, $filter_array, 'AND');
if ($result < 0) {
	setEventMessage($object->error, 'errors');
}
$iTotal = $result;
dol_syslog(__FILE__ . ' ' . ' $iTotal=' . $iTotal, LOG_DEBUG);

/* compute paging */
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {

	$limit = $_GET['iDisplayLength'] + 1;
	$offset = $db->escape($_GET['iDisplayStart']);
}

/* compute sorting */
if (isset($_GET['iSortCol_0'])) {
	$sortfield = $aColumns[intval($_GET['iSortCol_0'])];
	$sortorder = $db->escape($_GET['sSortDir_0']);
}

/* Total request data */
$result = $object->fetchAllforlist($sortorder, $sortfield, $limit, $offset, $filter_array, 'AND');
if ($result < 0) {
	setEventMessage($object->error, 'errors');
}
$iFilteredTotal = $result;
dol_syslog(__FILE__ . ' ' . ' $iFilteredTotal=' . $iFilteredTotal, LOG_DEBUG);

/*
 * Output
 */
$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iTotal,
		"aaData" => array()
);

/* Contruct row data */
if (is_array($object->lines) && count($object->lines) > 0) {

	foreach ( $object->lines as $line ) {
		$row = array();
		$soc = new Societe($db);
		$soc->fetch($line->cd_ex_client);

		$row[] = $line->getNomUrl2(1, '', 'card2');
		$row[] = $line->ref;
		$row[] = $line->getLibStatut(2);
		$row[] = $soc->getNomUrl(1);
		$row[] = $line->genre_label;
		$row[] = $line->marque_label;
		$row[] = $line->type;
		$row[] = $line->silouhette_label;
		$row[] = $line->puiscom;
		$row[] = $line->cabine_label;
		$row[] = $line->bv_label;
		$row[] = $line->moteur;
		$row[] = $line->capago;
		$row[] = $line->ptc;
		$row[] = $line->norme_label;
		$row[] = $line->kmrestit;
		$row[] = $line->option_label;
		$row[] = $line->immat;
		$row[] = $line->vin;
		$row[] = dol_print_date($line->circ,'daytext');
		if($user->rights->volvo->admin) $row[] = price($line->prix_achat). $langs->getCurrencySymbol($conf->currency);
		$row[] = price($line->rachat) . $langs->getCurrencySymbol($conf->currency) ;
		$row[] = price($line->estim) . $langs->getCurrencySymbol($conf->currency);
		$row[] = dol_print_date($line->date_entree,'daytext');
		$row[] = $line->site_label;
		$row[] = $line->nbj_stock;
		if($user->rights->volvo->admin) $row[] = price($line->surest). $langs->getCurrencySymbol($conf->currency);
		if($user->rights->volvo->admin) $row[] = price($line->cession) . $langs->getCurrencySymbol($conf->currency);
		if($user->rights->volvo->admin) $row[] = price($line->frais_ext) . $langs->getCurrencySymbol($conf->currency);
		if($user->rights->volvo->admin) $row[] = price($line->vd) . $langs->getCurrencySymbol($conf->currency);
		if($user->rights->volvo->admin) $row[] = price($line->fac_av) . $langs->getCurrencySymbol($conf->currency);
		if($user->rights->volvo->admin) $row[] = price($line->prix_revient) . $langs->getCurrencySymbol($conf->currency);
		$row[] = price($line->prix_vente) . $langs->getCurrencySymbol($conf->currency);
		if (!empty($line->prix_vente)) {
		if($user->rights->volvo->admin) $row[] = price($line->margecom) . $langs->getCurrencySymbol($conf->currency);
		}else{
			$row[] = '';
		}

		$row[] = dol_print_date($line->date_vente,'daytext');
		$res = $soc->fetch($line->cd_buyer);
		if($res>0 && !empty($line->prix_vente)){
			$row[] = $soc->getNomUrl(1);
		}else{
			$row[] = '';
		}
		if (!empty($line->prix_vente)) {
			$row[] = dol_print_date($line->dt_fac_ach,'daytext');
		}else{
			$row[] = '';
		}
		if (!empty($line->prix_vente)) {
		$row[] = $line->pays_vente;
		}else{
			$row[] = '';
		}
		if (!empty($line->prix_vente)) {
		$row[] = $line->type_ach;
		}else{
			$row[] = '';
		}

		$res = $soc->fetch($line->financeur);
		if($res>0 && !empty($line->prix_vente)){
			$row[] = $soc->getNomUrl(1);
		}else{
			$row[] = '';
		}

		$output['aaData'][] = $row;
	}
}

echo json_encode($output);