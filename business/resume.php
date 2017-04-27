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
$arrayresult5 = stat_sell5($year, $search_commercial,$monthlist);

$array_display=array();

foreach ($arrayperiode as $m) {
	$link='<a href="resume_list.php' . '?year=' . $year . '&search_commercial=' .$search_commercial.'&search_month=' . $m .'">' . $month[$m] . '</a>';
	$var = ! $var;
	$total_fact+=$arrayresult1['nb_fact'][$m];
	$total_portfeuille+=$arrayresult5['nb_port'][$m];
	$total_caht+=$arrayresult1['catotalht'][$m];
	$total_tracteur+=$arrayresult1['nbtracteur'][$m];
	$total_porteur+=$arrayresult1['nbporteur'][$m];
	$total_vcm+=$arrayresult2['vcm'][$m];
	$total_dfol+=$arrayresult2['dfol'][$m];
	$total_dded+=$arrayresult2['dded'][$m];
	$total_vfs+=$arrayresult2['vfs'][$m];
	$total_lixbail+=$arrayresult2['lixbail'][$m];
	$total_cavolvo+=$arrayresult3['cavolvo'][$m];
	$total_margetheo+=$arrayresult4['margetheo'][$m];
	$total_margereal+=$arrayresult4['margereal'][$m];

	if(!empty($arrayresult1['nb_fact'][$m])){
		$tracteur_percent = round(($arrayresult1['nbtracteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2);
		$porteur_percent = round(($arrayresult1['nbporteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2);
		$m_moy = price(round($arrayresult4['margetheo'][$m]/$arrayresult1['nb_fact'][$m],2));
		$m_moy_r = price(round($arrayresult4['margereal'][$m]/$arrayresult1['nb_fact'][$m],2));
		$m_moy_e = price(round(($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m])/$arrayresult1['nb_fact'][$m],2));
	}else{
		$tracteur_percent = '';
		$porteur_percent = '';
		$m_moy = '';
		$m_moy_r = '';
		$m_moy_e = '';
	}


	$array_display[$m]=array(
			'class' => $bc[$var],
			'class_td' => '',
			'mois' => $link,
			'nb_facture' => $arrayresult1['nb_fact'][$m],
			'nb_portfeuille' => $arrayresult5['nb_port'][$m],
			'ca_total' => ($arrayresult1['catotalht'][$m]==0?"":price($arrayresult1['catotalht'][$m])),
			'ca_volvo'=> ($arrayresult3['cavolvo'][$m]==0?"":price($arrayresult3['cavolvo'][$m])),
			'nb_trt'=> $arrayresult1['nbtracteur'][$m],
			'nb_port'=> $arrayresult1['nbporteur'][$m],
			'precent_trt'=> $tracteur_percent,
			'percent_prt'=> $porteur_percent,
			'vcm'=> $arrayresult2['vcm'][$m],
			'dfol'=> $arrayresult2['dfol'][$m],
			'dded'=> $arrayresult2['dded'][$m],
			'vfs'=> $arrayresult2['vfs'][$m],
			'lixbail'=> $arrayresult2['lixbail'][$m],
			'm_tot'=> ($arrayresult4['margetheo'][$m]==0?"":price($arrayresult4['margetheo'][$m])),
			'm_moy'=> $m_moy,
			'm_tot_r'=> ($arrayresult4['margereal'][$m]==0?"":price($arrayresult4['margereal'][$m])),
			'm_moy_r'=> $m_moy_r,
			'm_tot_e'=> (($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m])==0?"":price(round($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m],2))),
			'm_moy_e'=> $m_moy_e
	);
}
if(!empty($total_fact)){
	$tracteur_percent = round(($total_tracteur /($total_fact))*100,2);
	$porteur_percent = round(($total_porteur /($total_fact))*100,2);
	$m_moy = price(round($total_margetheo/$total_fact,2));
	$m_moy_r = price(round($total_margereal/$total_fact,2));
	$m_moy_e = price(round(($total_margereal-$total_margetheo)/$total_fact,2));
}else{
	$tracteur_percent = '';
	$porteur_percent = '';
	$m_moy = '';
	$m_moy_r = '';
	$m_moy_e = '';
}


$array_display[13]=array(
		'class' => ' class="liste_titre"',
		'class_td' => ' class="liste_titre"',
		'mois' => 'Total',
		'nb_facture' => price($total_fact),
		'nb_portfeuille' => price($total_portfeuille),
		'ca_total' => price($total_caht),
		'ca_volvo'=> price($total_cavolvo),
		'nb_trt'=> $total_tracteur,
		'nb_port'=> $total_porteur,
		'precent_trt'=> $tracteur_percent,
		'percent_prt'=> $porteur_percent,
		'vcm'=> $total_vcm,
		'dfol'=> $total_dfol,
		'dded'=> $total_dded,
		'vfs'=> $total_vfs,
		'lixbail'=> $total_lixbail,
		'm_tot'=> price($total_margetheo),
		'm_moy'=> $m_moy,
		'm_tot_r'=> price($total_margereal),
		'm_moy_r'=> $m_moy_r,
		'm_tot_e'=> price(round($total_margereal-$total_margetheo,2)),
		'm_moy_e'=> $m_moy_e
);

$arrayfields=array(
		'mois'=>array(
				'label'=>'Mois',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'nb_facture'=>array(
				'label'=>'Nb Factures',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'nb_portfeuille'=>array(
				'label'=>'Nb portefeuille',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'ca_total'=>array(
				'label'=>'C.A. Total HT',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
				'align'=>'center'
		),
		'ca_volvo'=>array(
				'label'=>'C.A. Fac. Volvo',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
				'align'=>'center'
		),
		'nb_trt'=>array(
				'label'=>'Nb Tracteurs',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'nb_port'=>array(
				'label'=>'Nb Porteurs',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'precent_trt'=>array(
				'label'=>'% Tracteurs',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'%',
				'align'=>'center'
		),
		'percent_prt'=>array(
				'label'=>'% Porteurs',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'%',
				'align'=>'center'
		),
		'vcm'=>array(
				'label'=>'VCM',
				'checked'=>1,
				'sub_title'=>1,
				'align'=>'center'
		),
		'dfol'=>array(
				'label'=>'DFOL',
				'checked'=>1,
				'sub_title'=>1,
				'align'=>'center'
		),
		'dded'=>array(
				'label'=>'DDED',
				'checked'=>1,
				'sub_title'=>1,
				'align'=>'center'
		),
		'vfs'=>array(
				'label'=>'VFS',
				'checked'=>1,
				'sub_title'=>1
		),
		'lixbail'=>array(
				'label'=>'Lixbail',
				'checked'=>1,
				'sub_title'=>1,
				'align'=>'center'
		),
		'm_tot'=>array(
				'label'=>'Marge totale',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
				'align'=>'center'
		),
		'm_moy'=>array(
				'label'=>'Marge moyenne',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
				'align'=>'center'
		),
		'm_tot_r'=>array(
				'label'=>'Marge Totale Réélle',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
				'align'=>'center'
		),
		'm_moy_r'=>array(
				'label'=>'Marge Moyenne Réélle',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
				'align'=>'center'
		),
		'm_tot_e'=>array(
				'label'=>'Marge totale - Ecart',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
				'align'=>'center'
		),
		'm_moy_e'=>array(
				'label'=>'Marge Moyenne - Ecart',
				'checked'=>1,
				'sub_title'=>0,
				'unit'=>'€',
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
$subtitle = array(
		1=>'Soft Offers'
);

$list_config=array(
		'title' =>	 'Suivis d\'activité VN volvo',
		'sortfield' => GETPOST("sortfield",'alpha'),
		'sortorder' => GETPOST("sortorder",'alpha'),
		'page' => GETPOST("page",'int'),
		'tools_active' =>1,
		'tools' => $tools,
		'array_fields' => $arrayfields,
		'sub_title' => $subtitle,
		'array_data' => $array_display,
		'export_name' => 'suivi_activité_new',
		'context' => 'suivi_business',
);

dol_include_once('/volvo/class/table_template.php');






