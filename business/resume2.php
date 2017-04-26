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


require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/lib/volvo.lib.php';

$title = 'Suivis d\'activité VN volvo';

// Security check
if (! $user->rights->volvo->activite)
	accessforbidden();

// Search criteria
$search_commercial = GETPOST("search_commercial", 'int');
$search_periode = GETPOST("search_periode");
$year = GETPOST('year');


// Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x")) {
 	$search_commercial = '';
 	$search_periode = '';
 	$year = dol_print_date(dol_now(),'Y');
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

if(empty($year)) $year = dol_print_date(dol_now(),'%Y');




$var = true;
$month = array(
		1=>'Janvier',
		2=>'Fevrier',
		3=>'Mars',
		4=>'Avril',
		5=>'Mai',
		6=>'Juin',
		7=>'Juillet',
		8=>'Aout',
		9=>'Septembre',
		10=>'Octobre',
		11=>'Novembre',
		12=>'Décembre'
);

if(!empty($search_periode)){
	switch($search_periode){
		case 1:
			$monthlist = '1,2,3';
			break;
		case 2:
			$monthlist = '4,5,6';
			break;
		case 3:
			$monthlist = '7,8,9';
			break;
		case 4:
			$monthlist = '10,11,12';
			break;
		case 5:
			$monthlist = '1,2,3,4,5,6';
			break;
		case 6:
			$monthlist = '7,8,9,10,11,12';
			break;
	}
}
if(!empty($monthlist)){
	$arrayperiode = explode(',',$monthlist);
}else{
	$arrayperiode=array(1,2,3,4,5,6,7,8,9,10,11,12);
}

$arrayresult1 = stat_sell1($year, $search_commercial,$monthlist);
$arrayresult2 = stat_sell2($year, $search_commercial,$monthlist);
$arrayresult3 = stat_sell3($year, $search_commercial,$monthlist);
$arrayresult4 = stat_sell4($year, $search_commercial,$monthlist);

$colomun = array(
			'mois',
			'Nb Factures',
			'C.A. Total HT',
			'C.A. Fac. Volvo',
			'Nb Tracteurs',
			'Nb Porteur',
			'% Tracteur',
			'% Porteur',
			'VCM',
			'DFOL',
			'DDED',
			'VFS',
			'Lixbail',
			'Marge Totale',
			'Marge moyenne',
			'Marge réélle Totale',
			'Marge réélle Moyenne',
			'Marge réélle Totale - Ecart',
			'Marge réélle Moyenne - Ecart'
	);

$arrayfields=array(
		'mois'=>array(
				'label'=>'Mois',
				'checked'=>1,
				'enabled'=>0
		),
		'nb_facture'=>array('label'=>'Nb Factures', 'checked'=>1),
		'ca_total'=>array('label'=>'C.A. Total HT', 'checked'=>1),
		'ca_volvo'=>array('label'=>'C.A. Fac. Volvo', 'checked'=>1),
		'nb_trt'=>array('label'=>'Nb Tracteurs', 'checked'=>1),
		'nb_port'=>array('label'=>'Nb Porteurs', 'checked'=>1),
		'precent_trt'=>array('label'=>'% Tracteurs', 'checked'=>1),
		'percent_prt'=>array('label'=>'% Porteurs', 'checked'=>1),
		'vcm'=>array('label'=>'VCM', 'checked'=>1),
		'dfol'=>array('label'=>'DFOL', 'checked'=>1),
		'dded'=>array('label'=>'DDED', 'checked'=>1),
		'vfs'=>array('label'=>'VFS', 'checked'=>1),
		'lixbail'=>array('label'=>'Lixbail', 'checked'=>1),
		'm_tot'=>array('label'=>'Marge totale', 'checked'=>1),
		'm_moy'=>array('label'=>'Marge moyenne', 'checked'=>1),
		'm_tot_r'=>array('label'=>'Marge Totale Réélle', 'checked'=>1),
		'm_moy_r'=>array('label'=>'Marge Moyenne Réélle', 'checked'=>1),
		'm_tot_e'=>array('label'=>'Marge totale - Ecart', 'checked'=>1),
		'm_moy_e'=>array('label'=>'Marge Moyenne - Ecart', 'checked'=>1),
);

$extra_tools=array(
		1 => array(
				'type' => 'select_year',
				'title' => 'Année: ',
				'value' => $year,
				'html_name' => 'year',
				'use_empty' => 0,
				'min_year' => 0,
				'max_year' => 5
		),
		2 => array(
				'type' => 'select_user',
				'title' => 'Commercial: ',
				'value' => $search_commercial,
				'html_name' => 'search_commercial',
				'use_empty' => 1,
				'disabled' => $search_commercial_disabled,
				'excluded' => array(),
				'included' => $user_included
		),
		3 => array(
				'type' => 'select_array',
				'title' => 'Periode: ',
				'value' => $search_periode,
				'html_name' => 'search_periode',
				'use_empty' => 1,
				'array' => array(1=>'1er Trimestre', 2=> '2eme Trimestre', 3=>'3eme Trimestre', 4=>'4eme Trimestre', 5=>'1er Semestre',6=>'2eme Semestre'),
				'value' => $search_periode,
		)
);

$tools=array(
		'search_button' => 1,
		'remove_filter_button' => 1,
		'export_button' => 1,
		'select_fields_button' => 1,
		'extra _tools' => $extra_tools
);


$list_config=array(
		'title' =>	 'Suivis d\'activité VN volvo',
		'sortfield' => GETPOST("sortfield",'alpha'),
		'sortorder' => GETPOST("sortorder",'alpha'),
		'page' => GETPOST("page",'int'),
		'tools_active' =>1,
		'tools' => $tools,
		'array_fields' => $arrayfields
);



dol_include_once('/volvo/class/table_template.php');






