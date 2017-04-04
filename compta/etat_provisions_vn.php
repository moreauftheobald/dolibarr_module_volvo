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
require_once DOL_DOCUMENT_ROOT . '/volvo/lib/compta.lib.php';

$title = 'Suivis des provision VN Volvo';

// Security check
if (! $user->rights->volvo->compta)
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

if(empty($year)) $year = dol_print_date(dol_now(),'%Y');


$form = new Form($db);
$formother = new FormOther($db);

llxHeader('', $title);

print_barre_liste($title, $page, $_SERVER['PHP_SELF'], $option, $sortfield, $sortorder, '', $num, $nbtotalofrecords);
print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<th class="liste_titre" align="center">Année: ';
$formother->select_year($year,'year',0, 5, 0);
print '</th>';
print '<th class="liste_titre" align="center">Commercial: '. $form->select_dolusers($search_commercial,'search_commercial',1,array(),0) . '</th>';
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
print '<th class="liste_titre" rowspan="2" align="center">Dossier</br>VN</th>';
print '<th class="liste_titre" rowspan="2" align="center">Commercial</th>';
print '<th class="liste_titre" rowspan="2" align="center">Client</th>';
print '<th class="liste_titre" rowspan="2" align="center">Factures</br>Externes</br>Commission</th>';
print '<th class="liste_titre" rowspan="2" align="center">Factures</br>Externes</br>Travaux</th>';
print '<th class="liste_titre" rowspan="2" align="center">Factures</br>Externes</br>Surest.</th>';
print '<th class="liste_titre" colspan="12" align="center">Factures a venir (Provisions)</th>';
print '<th class="liste_titre" colspan="12" align="center">Factures recues</th>';
print '<th class="liste_titre" rowspan="2" align="center">Prix</br>vente</br>Volvo</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge</br>réelle</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge</br>prévisionnelle</th>';
print "</tr>";
print '<tr class="liste_titre">';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Achat</th>';
print '<th class="liste_titre" align="center">Cession</br>Internes</br>Forfait Liv.</th>';
print '<th class="liste_titre" align="center">Cession</br>Internes</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Frais</br>externes</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>C.G.</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>T Services</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>TVI</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Carrosseries</th>';
print '<th class="liste_titre" align="center">Facture</br>Fictive</br>surest.</th>';
print '<th class="liste_titre" align="center">Cession</br>Interne</br>Garantie</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Garantie</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Commission</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Achat</th>';
print '<th class="liste_titre" align="center">Cession</br>Internes</br>Forfait Liv.</th>';
print '<th class="liste_titre" align="center">Cession</br>Internes</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Frais</br>externes</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>C.G.</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>T Services</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>TVI</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Carrosseries</th>';
print '<th class="liste_titre" align="center">Facture</br>Fictive</br>surest.</th>';
print '<th class="liste_titre" align="center">Cession</br>Interne</br>Garantie</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Garantie</th>';
print '<th class="liste_titre" align="center">Facture</br>Fournisseur</br>Commission</th>';
print "</tr>";

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

$arrayresult1 = stat_sell1($year, $search_commercial,$monthlist);


// foreach ($arrayperiode as $m) {
//  	$var = ! $var;
//  	$total_fact+=$arrayresult1['nb_fact'][$m];
// 	$total_caht+=$arrayresult1['catotalht'][$m];
// 	$total_tracteur+=$arrayresult1['nbtracteur'][$m];
// 	$total_porteur+=$arrayresult1['nbporteur'][$m];
// 	$total_vcm+=$arrayresult2['vcm'][$m];
// 	$total_dfol+=$arrayresult2['dfol'][$m];
// 	$total_dded+=$arrayresult2['dded'][$m];
// 	$total_vfs+=$arrayresult2['vfs'][$m];
// 	$total_lixbail+=$arrayresult2['lixbail'][$m];
// 	$total_cavolvo+=$arrayresult3['cavolvo'][$m];
// 	$total_margetheo+=$arrayresult4['margetheo'][$m];
// 	$total_margereal+=$arrayresult4['margereal'][$m];

//  	print '<tr ' . $bc[$var] . '>';
// 	print '<td align="center">' . $month[$m] . '</td>';
// 	print '<td align="center">' . $arrayresult1['nb_fact'][$m] . '</td>';
// 	if(!empty($arrayresult1['catotalht'][$m])){
// 		print '<td align="center">'. price($arrayresult1['catotalht'][$m]) .' €</td>';
// 	}else{
// 		print '<td align="center"></td>';
// 	}
// 	if(!empty($arrayresult3['cavolvo'][$m])){
// 		print '<td align="center">'. price($arrayresult3['cavolvo'][$m]) .' €</td>';
// 	}else{
// 		print '<td align="center"></td>';
// 	}
// 	print '<td align="center">' . $arrayresult1['nbtracteur'][$m] . '</td>';
// 	print '<td align="center">' . $arrayresult1['nbporteur'][$m] . '</td>';
// 	if(!empty($arrayresult1['nb_fact'][$m])){
// 		$tracteur_percent = round(($arrayresult1['nbtracteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2) . ' %';
// 		$porteur_percent = round(($arrayresult1['nbporteur'][$m] /($arrayresult1['nb_fact'][$m]))*100,2) . ' %';
// 	}else{
// 		$tracteur_percent = '';
// 		$porteur_percent = '';
// 	}
// 	print '<td align="center">' . $porteur_percent . '</td>';
// 	print '<td align="center">' . $tracteur_percent . '</td>';
// 	print '<td align="center">' . $arrayresult2['vcm'][$m] . '</td>';
// 	print '<td align="center">' . $arrayresult2['dfol'][$m] . '</td>';
// 	print '<td align="center">' . $arrayresult2['dded'][$m] . '</td>';
// 	print '<td align="center">' . $arrayresult2['vfs'][$m] . '</td>';
// 	print '<td align="center">' . $arrayresult2['lixbail'][$m] . '</td>';
// 	if(!empty($arrayresult4['margetheo'][$m])){
// 		print '<td align="center">'. price($arrayresult4['margetheo'][$m]) .' €</td>';
// 		print '<td align="center">'. price(round($arrayresult4['margetheo'][$m]/$arrayresult1['nb_fact'][$m],2)) .' €</td>';
// 	}else{
// 		print '<td align="center"></td>';
// 		print '<td align="center"></td>';
// 	}
// 	if(!empty($arrayresult4['margereal'][$m])){
// 		print '<td align="center">'. price($arrayresult4['margereal'][$m]) .' €</td>';
// 		print '<td align="center">'. price(round($arrayresult4['margereal'][$m]/$arrayresult1['nb_fact'][$m],2)) .' €</td>';
// 	}else{
// 		print '<td align="center"></td>';
// 		print '<td align="center"></td>';
// 	}
// 	if(!empty($arrayresult4['margetheo'][$m]) && !empty($arrayresult4['margereal'][$m])){
// 		print '<td align="center">'. price($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m]) .' €</td>';
// 		print '<td align="center">'. price(round(($arrayresult4['margereal'][$m]-$arrayresult4['margetheo'][$m])/$arrayresult1['nb_fact'][$m],2)) .' €</td>';
// 	}else{
// 		print '<td align="center"></td>';
// 		print '<td align="center"></td>';
// 	}

// 	print "</tr>\n";

// }

// print '<tr class="liste_titre">';
// print '<th class="liste_titre" align="center">Total</th>';
// print '<th class="liste_titre" align="center">' . $total_fact . '</th>';
// print '<th class="liste_titre" align="center">'. price($total_caht) .' €</th>';
// print '<th class="liste_titre" align="center">'. price($total_cavolvo) .' €</th>';
// print '<th class="liste_titre" align="center">' . $total_tracteur . '</th>';
// print '<th class="liste_titre" align="center">' . $total_porteur . '</th>';
// if(!empty($total_fact)){
// 	$tracteur_percent = round(($total_tracteur /($total_fact))*100,2);
// 	$porteur_percent = round(($total_porteur /($total_fact))*100,2);
// }else{
// 	$tracteur_percent = 0;
// 	$porteur_percent = 0;
// }
// print '<th class="liste_titre" align="center">' . price($tracteur_percent) . ' %</th>';
// print '<th class="liste_titre" align="center">' . price($tracteur_percent) . ' %</th>';
// print '<th class="liste_titre" align="center">' . $total_vcm . '</th>';
// print '<th class="liste_titre" align="center">' . $total_dfol . '</th>';
// print '<th class="liste_titre" align="center">' . $total_dded . '</th>';
// print '<th class="liste_titre" align="center">' . $total_vfs . '</th>';
// print '<th class="liste_titre" align="center">' . $total_lixbail . '</th>';
// print '<th class="liste_titre" align="center">' . price($total_margetheo) . ' €</th>';
// print '<th class="liste_titre" align="center">' . price(round($total_margetheo/$total_fact,2)) . ' €</th>';
// print '<th class="liste_titre" align="center">' . price($total_margereal) . '</th>';
// print '<th class="liste_titre" align="center">' . price(round($total_margereal/$total_fact,2)) . '</th>';
// print '<th class="liste_titre" align="center">' . price($total_margereal-$total_margetheo) . '</th>';
// print '<th class="liste_titre" align="center">' . price(round(($total_margereal-$total_margetheo)/$total_fact,2)) . '</th>';

// print "</tr>\n";


print "</table>";





llxFooter();
$db->close();
