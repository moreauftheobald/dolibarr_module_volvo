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
require_once DOL_DOCUMENT_ROOT . '/volvo/class/lead.extend.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.commande.class.php';

$title = 'Suivis des affaires en cours';

// Security check
if (! $user->rights->lead->read)
	accessforbidden();

// Search criteria
$search_commercial = GETPOST("search_commercial", 'int');
$search_client = GETPOST("search_client");
$search_lead = GETPOST('search_lead');


// Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x")) {
 	$search_commercial = '';
 	$search_client = '';
 	$search_lead = '';
}
$search_commercial_disabled = 0;
if (empty($user->rights->volvo->stat_all)){
	$search_commercial = $user->id;
	$search_commercial_disabled = 1;
}

$filter = array();
if (! empty($search_commercial) && $search_commercial != -1) {
 	$filter['lead.fk_user_resp'] = $search_commercial;
 	$option .= '&search_commercial=' . $search_commercial;
}
if (! empty($search_client)) {
	$filter['soc.nom'] = $search_client;
 	$option .= '&search_client=' . $search_client;
}
if (! empty($search_lead)) {
 	$filter['lead.ref'] = $search_lead;
 	$option .= '&search_lead=' . $search_lead;
}

$form = new Form($db);
$object = new Leadext($db);
$formother = new FormOther($db);


llxHeader('', $title);

// Count total nb of records
$nbtotalofrecords = 0;


// $resql = $object->fetchAllfolow($sortorder, $sortfield, $conf->liste_limit, $offset, $filter);
// if ($resql != - 1) {
// $num = $resql;
  	print_barre_liste($title, $page, $_SERVER['PHP_SELF'], $option, $sortfield, $sortorder, '', $num, $nbtotalofrecords);
  	print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";

  	print '<table class="noborder" width="100%">';
  	print '<tr class="liste_titre">';
 	print '<th class="liste_titre" align="center">Année: ';
 	$formother->select_year($syear,'year',1, 20, 5);
 	print '</th>';
 	print '<th class="liste_titre" align="center">Commercial: '. $form->select_dolusers($search_commercial,'search_commercial',1,array(),$search_commercial_disabled) . '</th>';
 	print '<th class="liste_titre" align="center">Periode: ';
 	print '<select class="flat" id="search_status" name="search_status">';
 	print '<option value="0"'.(empty($search_status)?' selected':'').'> </option>';
 	print '<option value="1"'.($search_status==1?' selected':'').'>1er Trimestre</option>';
 	print '<option value="2"'.($search_status==2?' selected':'').'>2eme Trimestre</option>';
 	print '<option value="3"'.($search_status==3?' selected':'').'>3eme Trimestre</option>';
 	print '<option value="4"'.($search_status==4?' selected':'').'>4eme Trimestre</option>';
 	print '<option value="5"'.($search_status==5?' selected':'').'>1er Semestre</option>';
 	print '<option value="6"'.($search_status==6?' selected':'').'>2eme Semestre</option>';
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

 	$i = 0;

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
 	print "</tr>\n";


//  	$var = true;

//  	foreach ($object->business as $line) {
// 		;$var = ! $var;

//  		$comm = New User($db);
//  		$comm->fetch($line->commercial);

//  		$comfourn = new CommandeFournisseur($db);
//  		$result=$comfourn->fetch($line->fournid);

//  		$comcli = New Commande($db);
//  		$comcli->fetch($line->com);

//  		$lead = new Lead($db);
//  		$lead->fetch($line->lead);

//  		$soc = New Societe($db);
//  		$soc->fetch($line->societe);

//  		$om_label = '<a href="'.DOL_URL_ROOT.'/fourn/commande/card.php?id=';
//  		$om_label.= $comfourn->id .'">';
//  		if (!empty($line->numom)){
//  			$om_label.= $line->numom;
//  		}else{
//  			$om_label.= $comfourn->ref;
//  		}
//  		$om_label.= '</a>';

//  		print '<tr ' . $bc[$var] . '>';

//  		print '<td>'. $comm->getNomUrl(0) .'</td>';
//  		print '<td align="center">' . $om_label . '</td>';
//  		print '<td align="center">' . $comcli->getNomUrl(0) . '</td>';
//  		print '<td align="center">'. $lead->getNomUrl(0) .'</td>';
//  		print '<td>'.$soc->getNomUrl(0,'',33) . '</td>';
//  		print '<td align="center">' . $line->vin . '</td>';
//  		print '<td align="center">' . $line->immat . '</td>';

//  		print '<td align="center">' . dol_print_date($line->dt_env_usi,'day') . '</td>';
//  		print '<td align="center">' . dol_print_date($line->dt_blockupdate,'day') . '</td>';
//  		print '<td align="center">' . dol_print_date($line->dt_liv_cons,'day') . '</td>';
//  		print '<td align="center">' . dol_print_date($line->dt_recep,'day') . '</td>';

//  		print '<td align="center">' . dol_print_date($line->dt_valid_ana,'day') . '</td>';
//  		print '<td align="center">' . dol_print_date($line->dt_liv_dem_cli,'day') . '</td>';
//  		print '<td align="center">' . dol_print_date($line->dt_liv_cli,'day') . '</td>';
//  		print '<td align="center">' . dol_print_date($line->dt_fac,'day') . '</td>';
//  		print '<td align="center">' . dol_print_date($line->dt_pay,'day') . '</td>';

//  		if(!empty($line->dt_recep)){
//  			$text = $line->delai_cash . ' Jour(s)';
//  		}else{
//  			$text ='';
//  		}
//  		print '<td align="center">' . $text . '</td>';

//  		if(!empty($line->dt_liv_dem_cli) && !empty($line->dt_liv_cons)){
//  			$text = $line->delaiprep . ' Jour(s)';
//  		}else{
//  			$text ='';
//  		}
//  		print '<td align="center">' . $text . '</td>';

//  		if(!empty($line->dt_liv_cons) && !empty($line->dt_recep)){
//  			$text = $line->retard_recept . ' Jour(s)';
//  		}else{
//  			$text ='';
//  		}
//  		print '<td align="center">' . $text . '</td>';

//  		if(!empty($line->dt_liv_dem_cli) && !empty($line->dt_liv_cli)){
//  			$text = $line->retard_liv . ' Jour(s)';
//  		}else{
//  			$text ='';
//  		}
//  		print '<td align="center">' . $text . '</td>';

//  		print "</tr>\n";

//  		$i ++;
//  	}

	print "</table>";

// 	}



llxFooter();
$db->close();
