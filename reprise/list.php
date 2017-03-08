<?php
/* Volvo
 * Copyright (C) 2015	Florian HENRY 		<florian.henry@open-concept.pro>
 *
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * \file		volvo/reprise/list.php
 * \ingroup	volvo
 */
$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory

// Load translation files required by the page
$langs->load("volvo@volvo");



// Security check
if (! $user->rights->volvo->lireeprise)
	accessforbidden();

$optioncss = GETPOST('optioncss', 'alpha');


/*$fk_thirdparty=GETPOST('fk_thirdparty');
$import_key=GETPOST('import_key');
$import_key_vcm=GETPOST('import_key_vcm');
$search_vin=trim(GETPOST('search_vin'));
$search_immat=trim(GETPOST('search_immat'));*/

/*
 * VIEW
*
* Put here all code to build page
*/
$title = $langs->trans('Reprise');
llxHeader('', $title);

$form = new Form($db);

echo load_fiche_titre($title, '', 'object_iron02@volvo');

include 'tpl/list.tpl.php';

// End of page
llxFooter();
$db->close();