<?php
/*
 * Copyright (C) 2014-2016 Florian HENRY <florian.henry@atm-consulting.fr>
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
 * \file		lib/lead.lib.php
 * \ingroup	lead
 * \brief		This file is an example module library
 * Put some comments here
 */

/**
 * Prepare page head
 *
 * @param Lead $object The lead
 *
 * @return array Header contents (tabs)
 */
function leadexpress_prepare_head($object)
{
	global $langs, $conf;

	$langs->load("lead@lead");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/volvo/lead/leadexpress.php", 1) . '?id=' . $object->id;
	$head[$h][1] = $langs->trans("LeadLead");
	$head[$h][2] = 'card';
	$h ++;

	$head[$h][0] = dol_buildpath("/volvo/lead/reprise.php", 1) . '?id=' . $object->id;
	$head[$h][1] = $langs->trans("Reprise");
	$head[$h][2] = 'reprise';
	$h ++;

	$head[$h][0] = dol_buildpath("/volvo/lead/document.php", 1) . '?id=' . $object->id;
	$head[$h][1] = $langs->trans("Documents");
	$head[$h][2] = 'documents';
	$h ++;

	$head[$h][0] = dol_buildpath("/volvo/lead/linked_object.php", 1) . '?id=' . $object->id;
	$head[$h][1] = $langs->trans("Objets liées");
	$head[$h][2] = 'object';
	$h ++;

	$nbNote = 0;
	if(!empty($object->note_private)) $nbNote++;
	if(!empty($object->note_public)) $nbNote++;
	$head[$h][0] = dol_buildpath("/volvo/lead/note.php", 1) . '?id=' . $object->id;
	$head[$h][1] = $langs->trans('Notes');
	if($nbNote > 0) $head[$h][1].= ' ('.$nbNote.')';
	$head[$h][2] = 'note';
	$h++;

	$head[$h][0] = dol_buildpath("/volvo/lead/info.php", 1) . '?id=' . $object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h ++;



	return $head;
}

