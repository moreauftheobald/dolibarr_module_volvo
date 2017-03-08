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

/**
 * \file lead/lead/list.php
 * \ingroup lead
 * \brief list of lead
 */
$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/volvo/class/reprise.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/reception.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/expertise.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/lead.extend.class.php';


// Security check
if (! $user->rights->lead->read)
	accessforbidden();

$sortorder = GETPOST('sortorder', 'alpha');
$sortfield = GETPOST('sortfield', 'alpha');
$page = GETPOST('page', 'int');

// view type is special predefined filter
$viewtype=GETPOST('viewtype','alpha');

// Search criteria
$search_commercial = GETPOST("search_commercial");
$search_ref = GETPOST("search_ref");
$search_type = GETPOST('search_type');
$search_fk_silouhette = GETPOST('search_fk_silouhette', 'int');
$search_fk_marque = GETPOST('search_fk_marque', 'int');
$search_fk_genre = GETPOST('search_fk_genre', 'int');
$search_kmrestit = GETPOST('search_kmrestit', 'int');
$search_fk_norme = GETPOST('search_fk_norme', 'int');

// // Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x")) {
	$search_commercial = '';
	$search_ref = '';
	$search_type = '';
	$search_fk_silouhette = '';
	$search_fk_marque = '';
	$search_fk_genre = '';
	$search_kmrestit = '';
	$search_fk_norme ='';
}

if ($search_fk_norme == -1){$search_fk_norme ='';}
if ($search_commercial == -1){$search_commercial ='';}
if ($search_fk_silouhette == -1){$search_fk_silouhette ='';}
if ($search_fk_marque == -1){$search_fk_marque ='';}
if ($search_fk_genre == -1){$search_fk_genre ='';}

$filter = array();
if (! empty($search_commercial)) {
	$filter['l.fk_user_resp'] = $search_commercial;
	$option .= '&search_commercial=' . $search_commercial;
}
if (! empty($search_ref)) {
	$filter['t.ref'] = $search_ref;
	$option .= '&search_ref=' . $search_ref;
}
if (! empty($search_type)) {
	$filter['t.type'] = $search_type;
	$option .= '&search_type=' . $search_type;
}
if (! empty($search_fk_silouhette)) {
	$filter['t.fk_silouhette'] = $search_fk_silouhette;
	$option .= '&search_fk_silouhette=' . $search_fk_silouhette;
}
if (! empty($search_fk_marque)) {
	$filter['t.fk_marque'] = $search_fk_marque;
	$option .= '&search_fk_marque=' . $search_fk_marque;
}
if (! empty($search_fk_genre)) {
	$filter['t.fk_genre'] = $search_fk_genre;
	$option .= '&search_fk_genre=' . $search_fk_genre;
}
if (! empty($search_kmrestit)) {
	$filter['t.kmrestit'] = $search_kmrestit;
	$option .= '&search_kmrestit=' . $search_kmrestit;
}
if (! empty($search_fk_norme)) {
	$filter['t.fk_norme'] = $search_fk_norme;
	$option .= '&search_fk_normee=' . $search_fk_norme;
}

if (!empty($viewtype)) {
	if ($viewtype=='current') {
		$filter['t.fk_c_status !IN'] = '6,7';
	}
	if ($viewtype=='my') {
		$filter['l.fk_user_resp'] = $user->id;
	}
// 	if ($viewtype=='late') {
// 		$filter['t.fk_c_status !IN'] = '6,7';
// 		$filter['t.date_closure<'] = dol_now();
// 	}
	$option .= '&viewtype=' . $viewtype;
}

if (empty($user->rights->societe->client->voir)) {
	$filter['l.fk_user_resp'] = $user->id;
	$option .= '&search_commercial=' . $user->id;
}

if ($page == - 1) {
	$page = 0;
}

$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

$form = new Form($db);
$object = new reprise($db);
$formother = new FormOther($db);

if (empty($sortorder))
	$sortorder = "DESC";
if (empty($sortfield))
	$sortfield = "t.ref";

llxHeader('', $title);

// Count total nb of records
$nbtotalofrecords = 0;

if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
	$nbtotalofrecords = $object->fetchAll($sortorder, $sortfield, 0, 0, $filter);
}
$resql = $object->fetchAll($sortorder, $sortfield, $conf->liste_limit, $offset, $filter);

if ($resql != - 1) {
	$num = $resql;

 	print_barre_liste($title, $page, $_SERVER['PHP_SELF'], $option, $sortfield, $sortorder, '', $num, $nbtotalofrecords);

 	print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";

 	if (! empty($sortfield))
 		print '<input type="hidden" name="sortfield" value="' . $sortfield . '"/>';
 	if (! empty($sortorder))
 		print '<input type="hidden" name="sortorder" value="' . $sortorder . '"/>';
 	if (! empty($page))
 		print '<input type="hidden" name="page" value="' . $page . '"/>';
 	if (! empty($viewtype))
 		print '<input type="hidden" name="viewtype" value="' . $viewtype . '"/>';
 	if (! empty($socid))
 		print '<input type="hidden" name="socid" value="' . $socid . '"/>';

 $moreforfilter .= 'Commercial : ' . $form->select_dolusers($search_commercial,'search_commercial',1);

 	if ($moreforfilter) {
 		print '<div class="liste_titre">';
 		print '<table class="nobordernopadding" width="100%">';
		print '<tr>';
		print '<td align="left" valign="center">' . $moreforfilter . '</td>';
		print '<td align="right" valign="center"><input class="liste_titre" type="image" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/search.png" value="' . dol_escape_htmltag($langs->trans("Search")) . '" title="' . dol_escape_htmltag($langs->trans("Search")) . '">';
		print '&nbsp;<input type="image" class="liste_titre" name="button_removefilter" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/searchclear.png" value="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '" title="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '">';
 		print '</td></tr></table></div>';
 	}
	$i = 0;
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre('ref', $_SERVEUR['PHP_SELF'], "t.ref", "", $option, '', $sortfield, $sortorder);
	print_liste_field_titre('Genre', $_SERVEUR['PHP_SELF'], "t.fk_genre", "", $option, 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre('Silouhette', $_SERVEUR['PHP_SELF'], "t.fk_silouhette", "", $option, 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre('Marque', $_SERVEUR['PHP_SELF'], "t.fk_marque", "", $option, 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre('Type', $_SERVEUR['PHP_SELF'], "t.type", "", $option, 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre('Norme', $_SERVEUR['PHP_SELF'], "t.fk_norme", "", $option, 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre('Kilometrage', $_SERVEUR['PHP_SELF'], "t.kmrestit", "", $option, 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre('Statut', $_SERVEUR['PHP_SELF'], "", "", $option, 'align="center"', $sortfield, $sortorder);
	//print '<td align="center"></td>';

	print "</tr>\n";

	print '<tr class="liste_titre">';

	print '<td><input type="text" class="flat" name="search_ref" value="' . $search_ref . '" size="10"></td>';

	print '<td align="center">' . $form->selectarray('search_fk_genre',$object->genre,$search_fk_genre,1) . '</td>';

	print '<td align="center">' . $form->selectarray('search_fk_silouhette',$object->silouhette,$search_fk_silouhette,1) . '</td>';

	print '<td align="center">' . $form->selectarray('search_fk_marque',$object->marque,$search_fk_marque,1) . '</td>';

	print '<td align="center"><input type="text" class="flat" name="search_type" value="' . $search_type . '" size="10"></td>';

	print '<td align="center">' . $form->selectarray('search_fk_norme',$object->norme,$search_fk_norme,1) . '</td>';

	print '<td align="center"><input type="text" class="flat" name="search_kmrestit" value="' . $search_kmrestit . '" size="10"></td>';

	print '<td class="liste_titre" align="right">';
	print '</td>';

	print "</tr>\n";
	print '</form>';

	$var = true;

	foreach ($object->lines as $line) {

		$var = ! $var;
		print '<tr ' . $bc[$var] . '>';

		print '<td><a href="card2.php?id=' . $line->id . '">' . $line->ref . '</a></td>';

		print '<td align="center">' . $line->genre[$line->fk_genre] . '</td>';

		print '<td align="center">' . $line->silouhette[$line->fk_silouhette] . '</td>';

		print '<td align="center">' . $line->marque[$line->fk_marque] . '</td>';

		print '<td align="center">' . $line->type . '</td>';

		print '<td align="center">' . $line->norme[$line->fk_norme] . '</td>';

		print '<td align="center">' . $line->kmrestit . '</td>';

		print '<td align="center">' . $line->status() . '</td>';

		print "</tr>\n";

		$i ++;
	}

	print "</table>";

 }



llxFooter();
$db->close();
