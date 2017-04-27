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
 	$year = dol_print_date(dol_now(),'%Y');
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


$array_display=array();



$arrayfields=array(
		'dossier'=>array(
				'label'=>'Dossier',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'om'=>array(
				'label'=>'N° O.M.',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'client'=>array(
				'label'=>'Client',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'dt_cmd'=>array(
				'label'=>'Date de Commande',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'dt_liv'=>array(
				'label'=>'Date de livraison',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'dt_liv_usi'=>array(
				'label'=>'Date de sortie d\'usine',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'vin'=>array(
				'label'=>'N° de Chassis',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'type'=>array(
				'label'=>'type',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'%',
				'align'=>'center'
		),
		'genre'=>array(
				'label'=>'genre',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'%',
				'align'=>'center'
		),
		'sil'=>array(
				'label'=>'silouhette',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'pv'=>array(
				'label'=>'Prix de vente',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),

);

$extra_tools=array(
		1 => array(
				'type' => 'select_year',
				'title' => 'Année: ',
				'value' => $year,
				'html_name' => 'year',
				'use_empty' => 0,
				'min_year' => 5,
				'max_year' => 0
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
		'array_fields' => $arrayfields,
		'array_data' => $array_display,
		'export_name' => 'portefeuille',
		'context' => 'portefeuille',
);

dol_include_once('/volvo/class/table_template.php');






