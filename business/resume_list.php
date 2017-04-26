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

$title = 'Suivis d\'activité VN volvo - détail';

// Security check
if (! $user->rights->volvo->activite)
	accessforbidden();

// Search criteria
$search_commercial = GETPOST("search_commercial", 'int');
$search_month = GETPOST("search_month");
$year = GETPOST('year');

$form = new Form($db);
$formother = new FormOther($db);

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


$monthlist=$search_month;

$arrayresult1 = stat_sell1($year, $search_commercial,$monthlist,'BY_REF');
$arrayresult2 = stat_sell2($year, $search_commercial,$monthlist,'BY_REF');
$arrayresult3 = stat_sell3($year, $search_commercial,$monthlist,'BY_REF');
$arrayresult4 = stat_sell4($year, $search_commercial,$monthlist,'BY_REF');

if(GETPOST("button_export_x")){
	$handler = fopen("php://output", "w");
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment;filename=suivi_activite_detail.csv');
	fputs($handler, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

	$commercial = new user($db);
	if(!empty($search_commercial)){
		$commercial->fetch($search_commercial);
		$com = $commercial->firstname . ' ' . $commercial->lastname;
	}

	$h=array(
			'Année:',
			$year,
			'',
			'commercial:',
			$com,
			'',
			'Mois:',
			$month[$monthlist]
	);
	fputcsv($handler, $h, ';', '"');

	$h = array(
			'Dossier',
			'C.A. Total HT',
			'C.A. Fac. Volvo',
			'VCM',
			'DFOL',
			'DDED',
			'VFS',
			'Lixbail',
			'Marge',
			'Marge réélle',
			'Marge réélle - Ecart'
	);
	fputcsv($handler, $h, ';', '"');

	foreach ($arrayresult1 as $key => $values) {
		$ligne=array();
		$ligne[]= $key;

		if(!empty($arrayresult1[$key]['catotalht'])){
			$ligne[]= price($values['catotalht']) .' €';
		}else{
			$ligne[]= '';
		}

		if(!empty($arrayresult3[$key]['cavolvo'])){
			$ligne[]= price($arrayresult3[$key]['cavolvo']) .' €';
		}else{
			$ligne[]= '';
		}

		$ligne[]= $arrayresult2[$key]['vcm'];
		$ligne[]= $arrayresult2[$key]['dfol'];
		$ligne[]= $arrayresult2[$key]['dded'];
		$ligne[]= $arrayresult2[$key]['vfs'];
		$ligne[]= $arrayresult2[$key]['lixbail'];

		if(!empty($arrayresult4[$key]['margetheo'])){
			$ligne[]= price($arrayresult4[$key]['margetheo']) .' €';
		}else{
			$ligne[]= '';
		}

		if(!empty($arrayresult4[$key]['margereal'])){
			$ligne[]= price($arrayresult4[$key]['margereal']) .' €';
		}else{
			$ligne[]= '';
		}

		if(!empty($arrayresult4[$key]['margetheo']) && !empty($arrayresult4[$key]['margereal'])){
			$ligne[]= price($arrayresult4[$key]['margereal']-$arrayresult4[$key]['margetheo']) .' €';
		}else{
			$ligne[]= '';
		}

		fputcsv($handler, $ligne, ';', '"');
	}
	exit;
}


llxHeader('', $title);

// Count total nb of records
$nbtotalofrecords = 0;

print_barre_liste($title, $page, $_SERVER['PHP_SELF'], $option, $sortfield, $sortorder, '', $num, $nbtotalofrecords);

print '<div class="inline-block divButAction"><a class="butAction" href="resume.php?search_commercial=' . $search_commercial . '&year=' . $year . '">Retour tableau mensuel</a>&nbsp;<input type="image" class="liste_titre" name="button_export" src="' . DOL_URL_ROOT . '/theme/common/mime/xls.png" value="export" title="Exporter"></div>';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<th class="liste_titre" rowspan="2" align="center">Dossier</th>';
print '<th class="liste_titre" rowspan="2" align="center">C.A.</br>Total HT</th>';
print '<th class="liste_titre" rowspan="2" align="center">C.A. Fac.</br>Volvo</th>';
print '<th class="liste_titre" colspan="5" align="center">Soft Offers</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge réélle</th>';
print '<th class="liste_titre" rowspan="2" align="center">Marge réélle</br>Ecart</th>';
print "</tr>";
print '<tr class="liste_titre">';
print '<th class="liste_titre" align="center">VCM</th>';
print '<th class="liste_titre" align="center">DFOL</th>';
print '<th class="liste_titre" align="center">DDED</th>';
print '<th class="liste_titre" align="center">VFS</th>';
print '<th class="liste_titre" align="center">Lixbail</th>';
print "</tr>";

$var = true;

foreach ($arrayresult1 as $key => $values) {
 	$var = ! $var;
 	$link = '<a href="../../commande/card.php?id=' . $values['id'] . '">' . $key . '</a>';
 	$total_caht+=$values['catotalht'];
	$total_vcm+=$arrayresult2[$key]['vcm'];
	$total_dfol+=$arrayresult2[$key]['dfol'];
	$total_dded+=$arrayresult2[$key]['dded'];
	$total_vfs+=$arrayresult2[$key]['vfs'];
	$total_lixbail+=$arrayresult2[$key]['lixbail'];
	$total_cavolvo+=$arrayresult3[$key]['cavolvo'];
	$total_margetheo+=$arrayresult4[$key]['margetheo'];
	$total_margereal+=$arrayresult4[$key]['margereal'];

 	print '<tr ' . $bc[$var] . '>';
	print '<td align="center">' . $link . '</td>';
	if(!empty($arrayresult1[$key]['catotalht'])){
		print '<td align="center">'. price($values['catotalht']) .' €</td>';
	}else{
		print '<td align="center"></td>';
	}
	if(!empty($arrayresult3[$key]['cavolvo'])){
		print '<td align="center">'. price($arrayresult3[$key]['cavolvo']) .' €</td>';
	}else{
		print '<td align="center"></td>';
	}
	print '<td align="center">' . $arrayresult2[$key]['vcm'] . '</td>';
	print '<td align="center">' . $arrayresult2[$key]['dfol'] . '</td>';
	print '<td align="center">' . $arrayresult2[$key]['dded'] . '</td>';
	print '<td align="center">' . $arrayresult2[$key]['vfs'] . '</td>';
	print '<td align="center">' . $arrayresult2[$key]['lixbail'] . '</td>';
	if(!empty($arrayresult4[$key]['margetheo'])){
		print '<td align="center">'. price($arrayresult4[$key]['margetheo']) .' €</td>';
	}else{
		print '<td align="center"></td>';
	}
	if(!empty($arrayresult4[$key]['margereal'])){
		print '<td align="center">'. price($arrayresult4[$key]['margereal']) .' €</td>';
	}else{
		print '<td align="center"></td>';
	}
	if(!empty($arrayresult4[$key]['margetheo']) && !empty($arrayresult4[$key]['margereal'])){
		print '<td align="center">'. price($arrayresult4[$key]['margereal']-$arrayresult4[$key]['margetheo']) .' €</td>';
	}else{
		print '<td align="center"></td>';
	}

	print "</tr>\n";

}

print '<tr class="liste_titre">';
print '<th class="liste_titre" align="center">Total</th>';
print '<th class="liste_titre" align="center">'. price($total_caht) .' €</th>';
print '<th class="liste_titre" align="center">'. price($total_cavolvo) .' €</th>';
print '<th class="liste_titre" align="center">' . $total_vcm . '</th>';
print '<th class="liste_titre" align="center">' . $total_dfol . '</th>';
print '<th class="liste_titre" align="center">' . $total_dded . '</th>';
print '<th class="liste_titre" align="center">' . $total_vfs . '</th>';
print '<th class="liste_titre" align="center">' . $total_lixbail . '</th>';
print '<th class="liste_titre" align="center">' . price($total_margetheo) . ' €</th>';
print '<th class="liste_titre" align="center">' . price($total_margereal) . '</th>';
print '<th class="liste_titre" align="center">' . price($total_margereal-$total_margetheo) . '</th>';

print "</tr>\n";


print "</table>";





llxFooter();
$db->close();
