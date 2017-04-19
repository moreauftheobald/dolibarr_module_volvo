<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Marc Barilley / Ocebo <marc@ocebo.com>
 * Copyright (C) 2005-2011 Regis Houssin         <regis.houssin@capnetworks.com>
 * Copyright (C) 2013      Cédric Salvador       <csalvador@gpcsolutions.fr>
 * Copyright (C) 2015      Florian HENRY         <florian.henry@atm-consulting.fr>
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

dol_include_once('/lead/class/lead.class.php');
dol_include_once('/lead/class/html.formlead.class.php');
dol_include_once('/volvo/lib/leadexpress.lib.php');
dol_include_once('/core/lib/files.lib.php');
dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');

if (! empty($conf->facture->enabled))
	dol_include_once('/compta/facture/class/facture.class.php');
if (! empty($conf->contrat->enabled))
	dol_include_once('/contrat/class/contrat.class.php');
if (! empty($conf->commande->enabled))
	dol_include_once('/commande/class/commande.class.php');
if (! empty($conf->agenda->enabled))
	dol_include_once('/comm/action/class/actioncomm.class.php');

$langs->load('lead@lead');
$langs->load('other');

$id=GETPOST('id','int');


// Security check
if (! $user->rights->lead->read)
	accessforbidden();

// Get parameters

$object = new Lead($db);
if ($id > 0) {
	$ret = $object->fetch($id);
	if ($ret < 0)
		setEventMessage($object->error, 'errors');
	if ($ret > 0)
		$ret = $object->fetch_thirdparty();
	if ($ret < 0)
		setEventMessage($object->error, 'errors');
}

/*
 * Actions
 */
include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_pre_headers.tpl.php';


/*
 * View
 */

top_htmlhead('', '');

$form = new Form($db);
$formlead = new FormLead($db);

$head = leadexpress_prepare_head($object);
dol_fiche_head($head, 'documents', $langs->trans('Module103111Name'), 0, dol_buildpath('/lead/img/object_lead.png', 1), 1);

print_fiche_titre($langs->trans('LeadDocuments'), '', 'lead@lead');

foreach ( $object->listofreferent as $key => $value ) {
	$title = $value['title'];
	$classname = $value['class'];
	$tablename = $value['table'];
	$qualified = $value['test'];

	if ($qualified) {
		print '<br>';
		print_fiche_titre($langs->trans($title));

		$selectList = $formlead->select_element($tablename, $object);
		if ($selectList) {
			print '<form action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="post">';
			print '<input type="hidden" name="tablename" value="' . $tablename . '">';
			print '<input type="hidden" name="action" value="addelement">';
			print '<table><tr><td>' . $langs->trans("SelectElement") . '</td>';
			print '<td>' . $selectList . '</td>';
			print '<td><input type="submit" class="button" value="' . $langs->trans("LeadAddElement") . '"></td>';
			print '</tr></table>';
			print '</form>';
		}
		print '<table class="noborder" width="100%">';

		print '<tr class="liste_titre">';
		print '<td></td>';
		print '<td width="100">' . $langs->trans("Ref") . '</td>';
		print '<td width="100" align="center">' . $langs->trans("Date") . '</td>';
		print '<td>' . $langs->trans("ThirdParty") . '</td>';
		if (empty($value['disableamount']))
			print '<td align="right" width="120">' . $langs->trans("AmountHT") . '</td>';
		if (empty($value['disableamount']))
			print '<td align="right" width="120">' . $langs->trans("AmountTTC") . '</td>';
		print '<td align="right" width="200">' . $langs->trans("Status") . '</td>';
		print '</tr>';

		$ret = $object->fetchDocumentLink($object->id, $tablename);
		if ($ret < 0) {
			setEventMessages(null, $object->errors, 'errors');
		}

		$elementarray = array();
		$elementarray = $object->doclines;
		if (count($elementarray) > 0 && is_array($elementarray)) {
			$var = true;
			$total_ht = 0;
			$total_ttc = 0;
			$num = count($elementarray);
			foreach ( $elementarray as $line ) {
				/**
				 *
				 * @var CommonObject $element
				 */
				$element = new $classname($db);
				$element->fetch($line->fk_source);
				$element->fetch_thirdparty();
				$var = ! $var;
				print "<tr " . $bc[$var] . ">";

				print '<td width="1%">';
				print '<a href="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&action=unlink&sourceid=' . $element->id . '&sourcetype=' . $tablename . '">' . img_picto($langs->trans('LeadUnlinkDoc'), 'unlink.png@lead') . '</a>';
				print "</td>\n";

				// Ref
				print '<td align="left">';
				print $element->getNomUrl(1);
				print "</td>\n";

				// Date
				$date = $element->date;
				if (empty($date)) {
					$date = $element->datep;
				}
				if (empty($date)) {
					$date = $element->date_contrat;
				}
				if (empty($date)) {
					$date = $element->datev; // Fiche inter
				}
				print '<td align="center">' . dol_print_date($date, 'day') . '</td>';

				// Third party
				print '<td align="left">';
				if (is_object($element->thirdparty))
					print $element->thirdparty->getNomUrl(1, '', 48);
				print '</td>';

				// Amount
				if (empty($value['disableamount'])) {
					print '<td align="right">' . (isset($element->total_ht) ? price($element->total_ht) : '&nbsp;') . '</td>';
				}

				// Amount
				if (empty($value['disableamount'])) {
					print '<td align="right">' . (isset($element->total_ttc) ? price($element->total_ttc) : '&nbsp;') . '</td>';
				}

				// Status
				print '<td align="right">' . $element->getLibStatut(5) . '</td>';
				print '</tr>';

				$total_ht = $total_ht + $element->total_ht;
				$total_ttc = $total_ttc + $element->total_ttc;
			}

			print '<tr class="liste_total">';
			print '<td>&nbsp;</td>';
			print '<td colspan="3">' . $langs->trans("Number") . ': ' . $num . '</td>';
			if (empty($value['disableamount']))
				print '<td align="right" width="100">' . $langs->trans("TotalHT") . ' : ' . price($total_ht) . '</td>';
			if (empty($value['disableamount']))
				print '<td align="right" width="100">' . $langs->trans("TotalTTC") . ' : ' . price($total_ttc) . '</td>';
			print '<td>&nbsp;</td>';
			print '</tr>';
		}
		print "</table>";
	}
}


$db->close();

dol_fiche_end();

llxFooter();
