<?php
/* Volvo
 * Copyright (C) 2014-2015 Florian HENRY <florian.henry@open-concept.pro>
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
$res = @include '../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
dol_include_once('/volvo/class/costprice.class.php');

$langs->load('orders');
$langs->load('companies');
$langs->load('products');

$orderid = GETPOST('orderid', 'int');
$action = GETPOST('action', 'alpha');
$idlines = GETPOST('idlines', 'alpha');

$idprod = GETPOST('idprod', 'int');

$error = 0;

$order = new Commande($db);
$product = new Product($db);
$extrafieldslines = new Extrafields($db);
$extralabelslines = $extrafieldslines->fetch_name_optionals_label($order->table_element_line);

$form = new Form($db);


$result = $order->fetch($orderid);
if ($result < 0) {
	setEventMessages($order->error, null, 'errors');
}

$linedisplay_array = array();
if (is_array($order->lines) && count($order->lines)) {
	foreach ( $order->lines as $line ) {

		if ($line->product_type == 9 && $line->qty == 1) {

			if (count($linedisplay_array) > 0) {
				// Test if previous element is also an Sub Total
				$lastelement = end($linedisplay_array);
				if ($lastelement->product_type == 9) {
					// in this case remove last element because no need to display empty subtotal lines
					array_pop($linedisplay_array);
				}
			}
			$linedisplay_array[$line->id] = $line;
		} elseif ($line->product_type != 9) {
			if (! empty($line->fk_product)) {
				$prod = new Product($db);
				$result = $prod->fetch($line->fk_product);
				if ($result < 0) {
					setEventMessages($prod->error, null, 'errors');
				} elseif (empty($prod->array_options['options_notupdatecost'])) {
					// Do not include product that cannot be updated
					$linedisplay_array[$line->id] = $line;
				}
			}
		}
	}
	$lastelement = end($linedisplay_array);
	if ($lastelement->product_type == 9) {
		// in this case remove last element because no need to display empty subtotal lines
		array_pop($linedisplay_array);
	}
}

top_htmlhead('', '');

if ($action == 'updatecost') {
	$lineupdate_array = explode(',', $idlines);
	if (is_array($extralabelslines) && count($extralabelslines) > 0) {

		foreach ( $order->lines as $line ) {

			if (in_array($line->id, $lineupdate_array)) {

				$line->fetch_optionals($line->id, $extralabelslines);
				// update extrafield real buying cost information
				foreach ( $extralabelslines as $extralabels ) {
					$line->array_options['options_buyingprice_real'] = GETPOST('options_buyingprice_realline_' . $line->id);
					$dt_invoice = dol_mktime(0, 0, 0, GETPOST('options_dt_invoiceline_' . $line->id . 'month', 'int'), GETPOST('options_dt_invoiceline_' . $line->id . 'day', 'int'), GETPOST('options_dt_invoiceline_' . $line->id . 'year', 'int'));
					$line->array_options['options_dt_invoice'] = $dt_invoice;
					$line->array_options['options_fk_supplier'] = GETPOST('options_fk_supplierline_' . $line->id);
					$result = $line->insertExtraFields();
					if ($result < 0) {
						$error ++;
						$errors[] = $line->error;
					}
				}
			}
		}
		if (! empty($idprod)) {
			$dt_invoice = dol_mktime(0, 0, 0, GETPOST('options_dt_invoicemonth', 'int'), GETPOST('options_dt_invoiceday', 'int'), GETPOST('options_dt_invoiceyear', 'int'));
			$array_options = array(
					'options_buyingprice_real' => GETPOST('options_buyingprice_real'),
					'options_fk_supplier' => GETPOST('options_fk_supplier'),
					'options_dt_invoice' => $dt_invoice
			);
			$product->fetch($idprod);

			// Set ORder status in memory to draft to allow use of addline
			$current_status = $order->statut;
			$order->statut = $order::STATUS_DRAFT;

			$result = $order->addline('', 0, 1, 0, 0, 0, $product->id, 0, 0, 0, 'HT', 0, '', '', 0, - 1, '', '', '', 0, '', $array_options);
			if ($result < 0) {
				if (! empty($order->error)) {
					setEventMessages($order->error, null, 'errors');
				} else {
					setEventMessages('Error on add line', null, 'errors');
				}
			}

			// reset again order in memory status to correct one
			$order->statut = $current_status;
		}

		// recalculate all cost price when finnished
// 		$costprice = new CostPrice($db);
// 		$result = $costprice->calcCostPrice();
// 		if ($result < 0) {
// 			$error ++;
// 			$errors = array_merge($errors, $costprice->errors);
// 		}
	}

	if (! empty($error)) {
		setEventMessages(null, $errors, 'errors');
	} else {
		print '<script type="text/javascript">' . "\n";
		print '	$(document).ready(function () {' . "\n";
		print '	window.parent.$(\'#popUpateCost\').dialog(\'close\');' . "\n";
		print '	window.parent.$(\'#popUpateCost\').remove();' . "\n";
		print '});' . "\n";
		print '</script>' . "\n";
	}
}

print '<form name="updatecost" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="orderid" value="' . $orderid . '">';
print '<input type="hidden" name="action" value="updatecost">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<th align="center">' . $langs->trans('Description') . '</th>';
print '<th align="center">' . $langs->trans('PriceUHT') . '</th>';
print '<th align="center">' . $langs->trans('Qty') . '</th>';
print '<th align="center">' . $langs->trans('Prix Achat réél') . '</th>';
print '<th align="center">' . $langs->trans('Date facture') . '</th>';
print '<th align="center">' . $langs->trans('Supplier') . '</th>';
print '</tr>';

if (is_array($order->lines) && count($order->lines)) {
	$lineupdate_array = array();
	foreach ( $order->lines as $line ) {
		$var = ! $var;

		if ($line->product_type != 9) {
			if (array_key_exists($line->id, $linedisplay_array)) {
				$lineupdate_array[] = $line->id;

				$line->fetch_optionals($line->id, $extralabelslines);

				print '<tr ' . $bc[$var] . '>';

				if (! empty($line->fk_product)) {
					$productdesc = $line->product_ref . ' - ' . $line->product_label;
				} else {
					$productdesc = $line->label . ' ' . $line->description;
				}

				print '<td align="center">' . $productdesc . '</td>';
				print '<td align="center">' . price($line->total_ht) . '</td>';
				print '<td align="center">' . $line->qty . '</td>';
				print '<td align="center">' . $extrafieldslines->showInputField('buyingprice_real', $line->array_options['options_buyingprice_real'], '', 'line_' . $line->id) . '</td>';
				print '<td align="center">' . $extrafieldslines->showInputField('dt_invoice', $line->array_options['options_dt_invoice'], '', 'line_' . $line->id) . '</td>';
				print '<td align="center">' . $extrafieldslines->showInputField('fk_supplier', $line->array_options['options_fk_supplier'], '', 'line_' . $line->id) . '</td>';
				print '</tr>';
			}
		} elseif ($line->qty == 1 && array_key_exists($line->id, $linedisplay_array)) {
			print '<tr style="background-color:#adadcf;">';
			print '<td style="font-weight:bold;   font-style: italic;" colspan="6">' . $line->description . '</td>';
			print '</tr>';
		}
	}
}

print '</table>';
print_fiche_titre($langs->trans('AddNewLine'));
print '<table class="border" width="100%">';
print '<tr>';
print '<td colspan="3">' . $langs->trans('Product');
print $form->select_produits($idprod, 'idprod', '', $conf->product->limit_size, 0, 1, 2, '', 1, array(), 0) . '</td>';
print '</tr>';
print '<tr>';
print '<td>' . $langs->trans('Supplier');
print $extrafieldslines->showInputField("fk_supplier", '') . '</td>';
print '<td>' . $langs->trans('Date facture');
print $extrafieldslines->showInputField("dt_invoice", '') . '</td>';
print '<td>' . $langs->trans('Prix achat réel');
print $extrafieldslines->showInputField("buyingprice_real", '') . '</td>';
print '</tr>';
print '</table>';
print '<div class="tabsAction">';
print '<input type="hidden" name="idlines" value="' . implode(',', $lineupdate_array) . '"/>';
print '<input type="submit" align="center" class="button" value="' . $langs->trans('Save') . '" name="save" id="save"/>';
print '</div>';
print '</form>';

llxFooter();
$db->close();