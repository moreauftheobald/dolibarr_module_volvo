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
if (! $user->rights->lead->read)
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

if(empty($year)) $year = dol_print_date(dol_now(),'%Y');


$form = new Form($db);
$formother = new FormOther($db);


llxHeader('', $title);

// Count total nb of records
$nbtotalofrecords = 0;

print_barre_liste($title, $page, $_SERVER['PHP_SELF'], $option, $sortfield, $sortorder, '', $num, $nbtotalofrecords);
print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<th class="liste_titre" align="center">Année: ';
$formother->select_year($year,'year',0, 5, 0);
print '</th>';
print '<th class="liste_titre" align="center">Commercial: '. $form->select_dolusers($search_commercial,'search_commercial',1,array(),$search_commercial_disabled) . '</th>';
print '<th class="liste_titre" align="center">Periode: ';
print '<select class="flat" id="search_periode" name="search_periode">';
print '<option value="0"'.(empty($search_periode)?' selected':'').'> </option>';
print '<option value="1"'.($search_periode==1?' selected':'').'>1er Trimestre</option>';
print '<option value="2"'.($search_periode==2?' selected':'').'>2eme Trimestre</option>';
print '<option value="3"'.($search_periode==3?' selected':'').'>3eme Trimestre</option>';
print '<option value="4"'.($search_periode==4?' selected':'').'>4eme Trimestre</option>';
print '<option value="5"'.($search_periode==5?' selected':'').'>1er Semestre</option>';
print '<option value="6"'.($search_periode==6?' selected':'').'>2eme Semestre</option>';
print '</select>';
print '</th>';
print '<th class="liste_titre" align="center">';
print '<div align="left"><input class="liste_titre" type="image" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/search.png" value="' . dol_escape_htmltag($langs->trans("Search")) . '" title="' . dol_escape_htmltag($langs->trans("Search")) . '">';
print '&nbsp;<input type="image" class="liste_titre" name="button_removefilter" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/searchclear.png" value="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '" title="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '"></div>';
print '</th>';
print "</tr>";
print '</table>';
print '</br>';
print '</form>';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<th class="liste_titre" rowspan="2" align="center">Mois</th>';
print '<th class="liste_titre" rowspan="2" align="center">Nb</br>Factures</th>';
print '<th class="liste_titre" rowspan="2" align="center">C.A.</br>Total HT</th>';
print '<th class="liste_titre" rowspan="2" align="center">C.A. Fac.</br>Volvo</th>';
print '<th class="liste_titre" rowspan="2" align="center">Nb</br>Tracteurs</th>';
print '<th class="liste_titre" rowspan="2" align="center">Nb</br>Porteur</th>';
print '<th class="liste_titre" rowspan="2" align="center">%</br>Tracteur</th>';
print '<th class="liste_titre" rowspan="2" align="center">%</br>Porteur</th>';
print '<th class="liste_titre" colspan="5" align="center">Soft Offers</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge</br>Totale</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge</br>moyenne</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge réélle</br>Totale</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge réélle</br>Moyenne</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge réélle</br>Totale - Ecart</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge réélle</br>Moyenne - Ecart</th>';
print "</tr>";
print '<tr class="liste_titre">';
print '<th class="liste_titre" align="center">VCM</th>';
print '<th class="liste_titre" align="center">DFOL</th>';
print '<th class="liste_titre" align="center">DDED</th>';
print '<th class="liste_titre" align="center">VFS</th>';
print '<th class="liste_titre" align="center">Lixbail</th>';
print "</tr>";

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

foreach ($arrayperiode as $m) {
	$link='<a href="/resume_list.php' . '?year=' . $year . '&search_commercial=' .$search_commercial.'&search_month=' . $m .'">' . $month[$m] . '</a>';
 	$var = ! $var;
 	$total_fact+=$arrayresult1['nb_fact'][$m];
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

 	print '<tr ' . $bc[$var] . '>';
	print '<td align="center">' . $link . '</td>';
	print '<td align="center">' . $arrayresult1['nb_fact'][$m] . '</td>';
	if(!empty($arrayresult1['catotalht'][$m])){
		print '<td align="center">'. price($arrayresult1['catotalht'][$m]) .' €</td>';
	}else{
		print '<td align="center"></td>';
	}
	if(!empty($arrayresult3['cavolvo'][$m])){
		print '<td align="center">'. price($arrayresult3['cavolvo'][$m]) .' €</td>';
	}else{
		print '<td align="center"></td>';
	}
	print '<td align="center">' . $arrayresult1['nbtracteur'][$m] . '</td>';
	print '<td align="center">' . $arrayresult1['nbporteur'][$m] . '</td>';
	if(!empty($arrayresult1['nb_fact'][$m])){
		$tracteur_percent = round(($arrayresult1['nbtracteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2) . ' %';
		$porteur_percent = round(($arrayresult1['nbporteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2) . ' %';
	}else{
		$tracteur_percent = '';
		$porteur_percent = '';
	}
	print '<td align="center">' . $tracteur_percent . '</td>';
	print '<td align="center">' . $porteur_percent . '</td>';
	print '<td align="center">' . $arrayresult2['vcm'][$m] . '</td>';
	print '<td align="center">' . $arrayresult2['dfol'][$m] . '</td>';
	print '<td align="center">' . $arrayresult2['dded'][$m] . '</td>';
	print '<td align="center">' . $arrayresult2['vfs'][$m] . '</td>';
	print '<td align="center">' . $arrayresult2['lixbail'][$m] . '</td>';
	if(!empty($arrayresult4['margetheo'][$m])){
		print '<td align="center">'. price($arrayresult4['margetheo'][$m]) .' €</td>';
		print '<td align="center">'. price(round($arrayresult4['margetheo'][$m]/$arrayresult1['nb_fact'][$m],2)) .' €</td>';
	}else{
		print '<td align="center"></td>';
		print '<td align="center"></td>';
	}
	if(!empty($arrayresult4['margereal'][$m])){
		print '<td align="center">'. price($arrayresult4['margereal'][$m]) .' €</td>';
		print '<td align="center">'. price(round($arrayresult4['margereal'][$m]/$arrayresult1['nb_fact'][$m],2)) .' €</td>';
	}else{
		print '<td align="center"></td>';
		print '<td align="center"></td>';
	}
	if(!empty($arrayresult4['margetheo'][$m]) && !empty($arrayresult4['margereal'][$m])){
		print '<td align="center">'. price($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m]) .' €</td>';
		print '<td align="center">'. price(round(($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m])/$arrayresult1['nb_fact'][$m],2)) .' €</td>';
	}else{
		print '<td align="center"></td>';
		print '<td align="center"></td>';
	}

	print "</tr>\n";

}

print '<tr class="liste_titre">';
print '<th class="liste_titre" align="center">Total</th>';
print '<th class="liste_titre" align="center">' . $total_fact . '</th>';
print '<th class="liste_titre" align="center">'. price($total_caht) .' €</th>';
print '<th class="liste_titre" align="center">'. price($total_cavolvo) .' €</th>';
print '<th class="liste_titre" align="center">' . $total_tracteur . '</th>';
print '<th class="liste_titre" align="center">' . $total_porteur . '</th>';
if(!empty($total_fact)){
	$tracteur_percent = round(($total_tracteur /($total_fact))*100,2);
	$porteur_percent = round(($total_porteur /($total_fact))*100,2);
}else{
	$tracteur_percent = 0;
	$porteur_percent = 0;
}
print '<th class="liste_titre" align="center">' . price($tracteur_percent) . ' %</th>';
print '<th class="liste_titre" align="center">' . price($porteur_percent) . ' %</th>';
print '<th class="liste_titre" align="center">' . $total_vcm . '</th>';
print '<th class="liste_titre" align="center">' . $total_dfol . '</th>';
print '<th class="liste_titre" align="center">' . $total_dded . '</th>';
print '<th class="liste_titre" align="center">' . $total_vfs . '</th>';
print '<th class="liste_titre" align="center">' . $total_lixbail . '</th>';
print '<th class="liste_titre" align="center">' . price($total_margetheo) . ' €</th>';
print '<th class="liste_titre" align="center">' . price(round($total_margetheo/$total_fact,2)) . ' €</th>';
print '<th class="liste_titre" align="center">' . price($total_margereal) . '</th>';
print '<th class="liste_titre" align="center">' . price(round($total_margereal/$total_fact,2)) . '</th>';
print '<th class="liste_titre" align="center">' . price($total_margereal-$total_margetheo) . '</th>';
print '<th class="liste_titre" align="center">' . price(round(($total_margereal-$total_margetheo)/$total_fact,2)) . '</th>';

print "</tr>\n";


print "</table>";





llxFooter();
$db->close();
