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


$form = new Form($db);
$formother = new FormOther($db);

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


if(GETPOST("button_export_x")){
	$handler = fopen("php://output", "w");
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment;filename=suivi_activite.csv');
	fputs($handler, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

	$commercial = new user($db);
	if(!empty($search_commercial)){
		$commercial->fetch($search_commercial);
		$com = $commercial->firstname . ' ' . $commercial->lastname;
	}

	if(!empty($search_periode)){
		if($search_periode == 1){
			$periode = '1er Trimestre';
		}elseif($search_periode==2){
			$periode = '2eme Trimestre';
		}elseif($search_periode==3){
			$periode = '3eme Trimestre';
		}elseif($search_periode==4){
			$periode = '4eme Trimestre';
		}elseif($search_periode==5){
			$periode = '1er Semestre';
		}elseif($search_periode==6){
			$periode = '2eme Semestre';
		}
	}

	$h=array(
			'Année:',
			$year,
			'',
			'commercial:',
			$com,
			'',
			'Periode:',
			$periode
	);

	fputcsv($handler, $h, ';', '"');

	$h = array(
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

	fputcsv($handler, $h, ';', '"');
	foreach ($arrayperiode as $m) {
		$ligne=array();
		$ligne[]=$month[$m];
		$ligne[]=$arrayresult1['nb_fact'][$m];

		if(!empty($arrayresult1['catotalht'][$m])){
			$ligne[]=price($arrayresult1['catotalht'][$m]) .' €';
		}else{
			$ligne[]='';
		}

		if(!empty($arrayresult3['cavolvo'][$m])){
			$ligne[]=price($arrayresult3['cavolvo'][$m]) .' €';
		}else{
			$ligne[]='';
		}

		$ligne[]=$arrayresult1['nbtracteur'][$m];
		$ligne[]=$arrayresult1['nbporteur'][$m];

		if(!empty($arrayresult1['nb_fact'][$m])){
			$ligne[]= round(($arrayresult1['nbtracteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2) . ' %';
			$ligne[]= round(($arrayresult1['nbporteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2) . ' %';
		}else{
			$ligne[]= '';
			$ligne[]= '';
		}

		$ligne[]= $arrayresult2['vcm'][$m];
		$ligne[]= $arrayresult2['dfol'][$m];
		$ligne[]= $arrayresult2['dded'][$m];
		$ligne[]= $arrayresult2['vfs'][$m];
		$ligne[]= $arrayresult2['lixbail'][$m];

		if(!empty($arrayresult4['margetheo'][$m])){
			$ligne[]= price($arrayresult4['margetheo'][$m]) .' €';
			$ligne[]= price(round($arrayresult4['margetheo'][$m]/$arrayresult1['nb_fact'][$m],2)) .' €';
		}else{
			$ligne[]='';
			$ligne[]='';
		}

		if(!empty($arrayresult4['margereal'][$m])){
			$ligne[]= price($arrayresult4['margereal'][$m]) .' €';
			$ligne[]= price(round($arrayresult4['margereal'][$m]/$arrayresult1['nb_fact'][$m],2)) .' €';
		}else{
			$ligne[]='';
			$ligne[]='';
		}

		if(!empty($arrayresult4['margetheo'][$m]) && !empty($arrayresult4['margereal'][$m])){
			$ligne[]= price(round($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m],2)) .' €';
			$ligne[]= price(round(($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m])/$arrayresult1['nb_fact'][$m],2)) .' €';
		}else{
			$ligne[]='';
			$ligne[]='';
		}

		fputcsv($handler, $ligne, ';', '"');
	}

	exit;
}



llxHeader('', $title);

$test='ca marche';

dol_include_once('/volvo/class/table_template.php');





llxFooter();
$db->close();
