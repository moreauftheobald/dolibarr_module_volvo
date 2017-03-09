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
require_once DOL_DOCUMENT_ROOT . '/volvo/class/lead.extend.class.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/reprise.class.php';

$langs->load('orders');


$object2 = new Leadext($db);
$object2->fetch($object->id);
$object = $object2;
$object->fetch_thirdparty();
$events = array();


if ($object->array_options["options_chaude"]) {
	$chaude = '<img src="' . DOL_URL_ROOT . '/volvo/img/recent.png" height="16" width="16">';
}

if ($object->array_options["options_new"]) {
	$new = '<img src="' . DOL_URL_ROOT . '/volvo/img/switch_on.png" height="16" width="49">';
} else {
	$new = '<img src="' . DOL_URL_ROOT . '/volvo/img/switch_off.png" height="16" width="49">';
}

if ($object->fk_c_status == 7 or $object->fk_c_status == 6 or $object->fk_c_status == 11) {
	$status = '<img src="' . DOL_URL_ROOT . '/volvo/img/statut' . $object->fk_c_status . '.png" height="16" width="16">';
}

if ($action == "ext_head_confirm_order" && $confirm == 'yes') {
	$price = GETPOST('price');
	$deliv = dol_mktime(0, 0, 0, GETPOST('del_datemonth'), GETPOST('del_dateday'), GETPOST('del_dateyear'));
	$object->price=$price;
	$result = $object->createcmd($deliv, $extrafields);
	//echo $result;
	if ($result < 0) {
		setEventMessages(null, $object->errors, 'errorst1');
		header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=nokorder' . $result);
		exit;
	} else {
		header('Location:' . DOL_URL_ROOT . "/commande/card.php?id=" . $result);
		exit();
	}
	$confirm = 'no';
}
?>

<!-- BEGIN PHP TEMPLATE PERSONNAL LEAD -->

<?php
if ($action == 'create' && $user->rights->lead->write) {

	print '<form name="addlead" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="propalid" value="' . GETPOST('propalid', 'int') . '">';
	print '<input type="hidden" name="action" value="add">';

	print '<table class="border" width="100%">';

	print '<tr class="liste_titre"><td align="center" colspan="4">Descriptif Affaire</td></tr>';
	print '<tr>';
	print '<td width="50%" colspan="2">' . '' . '</td>';
	print '<td width="15%" class="fieldrequired">Numero de dossier</td>';
	print '<td width="35%"><input type="text" name="ref_int" size="10" value="' . $ref_int . '"/></td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired"> Canal de Vente </td>';
	print '<td>' . $formlead->select_lead_type($leadtype, 'leadtype', 0) . '</td>';
	print '<td class="fieldrequired">Commercial</td>';
	print '<td>' . $form->select_dolusers(empty($userid) ? $user->id : $userid, 'userid', 0, array(), 0, $includeuserlist, '', 0, 0, 0, '', 0, '', '', 1) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $langs->trans('LeadStatus') . '</td>';
	print '<td>' . $formlead->select_lead_status($leadstatus, 'leadstatus', 0) . '</td>';
	print '<td colspan="2"><table width="100%" class="nobordernopadding"><tr><td>' . $extrafields->attribute_label["chaude"] . '</td>';
	print '<td>' . $extrafields->showInputField("chaude", 1) . '</td>';
	print '<td>' . $extrafields->attribute_label["new"] . '</td>';
	print '<td>' . $extrafields->showInputField("new", 0) . '</td>';
	print '</tr></table></tr>';

	print '<tr>';
	print '<td class="fieldrequired">Client</td>';
	print '<td>' . $form->select_thirdparty_list($socid, 'socid', 'client<>0', 1, 1, 0, $events) . '</td>';
	print '<td>' . $extrafields->attribute_label["ctm"] . '</td>';
	print '<td>' . $extrafields->showInputField("ctm", 0) . '</td>';
	print '</tr>';

	print '<tr class="liste_titre"><td align="center" colspan="4">Caracteristiques</td></tr>';

	print '<tr>';
	print '<td width="15%" class="fieldrequired">' . $langs->trans('LeadAmountGuess') . '</td>';
	print '<td width="35%"><input type="text" name="amount_guess" size="5" value="' . price2num($amount_guess) . '"/></td>';
	print '<td width="15%" class="fieldrequired">' . $extrafields->attribute_label["nbchassis"] . '</td>';
	print '<td width="35%">' . $extrafields->showInputField("nbchassis", 0) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $extrafields->attribute_label["gamme"] . '</td>';
	print '<td>' . $extrafields->showInputField("gamme", 0) . '</td>';
	print '<td class="fieldrequired">' . $extrafields->attribute_label["silouhette"] . '</td>';
	print '<td>' . $extrafields->showInputField("silouhette", 0) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $extrafields->attribute_label["type"] . '</td>';
	print '<td>' . $extrafields->showInputField("type", 0) . '</td>';
	print '<td>' . $extrafields->attribute_label["carroserie"] . '</td>';
	print '<td>' . $extrafields->showInputField("carroserie", 0) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . ' ' . '</td>';
	print '<td>' . ' ' . '</td>';
	print '<td>' . $extrafields->attribute_label["specif"] . '</td>';
	print '<td>' . $extrafields->showInputField("specif", '') . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>';
	print $langs->trans('LeadDescription');
	print '</td>';
	print '<td colspan="3">';
	$doleditor = new DolEditor('description', $object->description, '', 160, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
	$doleditor->Create();
	print '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["soltrs"] . '</td>';
	print '<td colspan="3">' . $extrafields->showInputField("soltrs", 0) . '</td>';
	print '</tr>';


	print '</table>';

	$out .= "\n";
	$out .= '
				<script type="text/javascript">
				    jQuery(document).ready(function() {
				    	function showOptions(child_list, parent_list)
				    	{
				    		var val = $("select[name=\""+parent_list+"\"]").val();
				    		var parentVal = parent_list + ":" + val;
							if(val > 0) {
					    		$("select[name=\""+child_list+"\"] option[parent]").hide();
					    		$("select[name=\""+child_list+"\"] option[parent=\""+parentVal+"\"]").show();
							} else {
								$("select[name=\""+child_list+"\"] option").show();
							}
				    	}
						function setListDependencies() {
					    	$("select option[parent]").parent().each(function() {
					    		var child_list = $(this).attr("name");
								var parent = $(this).find("option[parent]:first").attr("parent");
								var infos = parent.split(":");
								var parent_list = infos[0];
								$("select[name=\""+parent_list+"\"]").change(function() {
									showOptions(child_list, parent_list);
								});
					    	});
						}

						setListDependencies();

						$("#leadtype").change();
				    });
				</script>' . "\n";
	$out .= '<!-- /showOptionalsInput --> ' . "\n";

	print $out;

	print '<div class="tabsAction">';

} elseif ($action == 'edit') {

	print '<form name="editlead" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update">';

	print '<table class="border" width="100%">';

	print '<tr class="liste_titre"><td align="center" colspan="4">Descriptif Affaire</td></tr>';
	print '<tr>';
	print '<td width="15%">' . $langs->trans('Ref') . '</td>';
	print '<td width="35%">' . $object->ref . '</td>';
	print '<td width="15%" class="fieldrequired">Numero de dossier</td>';
	print '<td width="35%"><input type="text" name="ref_int" size="10" value="' . $object->ref_int . '"/></td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired"> Canal de Vente </td>';
	print '<td>' . $formlead->select_lead_type($object->fk_c_type, 'leadtype', 0) . '</td>';
	print '<td class="fieldrequired">Commercial</td>';
	print '<td>' . $form->select_dolusers($object->fk_user_resp, 'userid', 0, array(), 0, $includeuserlist, '', 0, 0, 0, '', 0, '', '', 1) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $langs->trans('LeadStatus') . '</td>';
	print '<td>' . $formlead->select_lead_status($object->fk_c_status, 'leadstatus', 0) . '</td>';
	print '<td colspan="2"><table width="100%" class="nobordernopadding"><tr><td>' . $extrafields->attribute_label["chaude"] . '</td>';
	print '<td>' . $extrafields->showInputField("chaude", $object->array_options["options_chaude"]) . '</td>';
	print '<td>' . $extrafields->attribute_label["new"] . '</td>';
	print '<td>' . $extrafields->showInputField("new", $object->array_options["options_new"]) . '</td>';
	print '</tr></table></tr>';

	print '<tr>';
	print '<td class="fieldrequired">Client</td>';
	print '<td>' . $form->select_thirdparty_list($object->thirdparty->id, 'socid', 'client<>0', 1, 1, 0, $events) . '</td>';
	print '<td>' . $extrafields->attribute_label["ctm"] . '</td>';
	print '<td>' . $extrafields->showInputField("ctm", $object->array_options["options_ctm"]) . '</td>';
	print '</tr>';

	print '<tr class="liste_titre"><td align="center" colspan="4">Caracteristiques</td></tr>';

	print '<tr>';
	print '<td width="15%" class="fieldrequired">' . $langs->trans('LeadAmountGuess') . '</td>';
	print '<td width="35%"><input type="text" name="amount_guess" size="5" value="' . price2num($object->amount_prosp) . '"/></td>';
	print '<td width="15%" class="fieldrequired">' . $extrafields->attribute_label["nbchassis"] . '</td>';
	print '<td width="35%">' . $extrafields->showInputField("nbchassis", $object->array_options["options_nbchassis"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $extrafields->attribute_label["gamme"] . '</td>';
	print '<td>' . $extrafields->showInputField("gamme", $object->array_options["options_gamme"]) . '</td>';
	print '<td class="fieldrequired">' . $extrafields->attribute_label["silouhette"] . '</td>';
	print '<td>' . $extrafields->showInputField("silouhette", $object->array_options["options_silouhette"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $extrafields->attribute_label["type"] . '</td>';
	print '<td>' . $extrafields->showInputField("type", $object->array_options["options_type"]) . '</td>';
	print '<td>' . $extrafields->attribute_label["carroserie"] . '</td>';
	print '<td>' . $extrafields->showInputField("carroserie", $object->array_options["options_carroserie"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . ' ' . '</td>';
	print '<td>' . ' ' . '</td>';
	print '<td>' . $extrafields->attribute_label["specif"] . '</td>';
	print '<td>' . $extrafields->showInputField("specif", $object->array_options["options_specif"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $langs->trans('LeadDescription') . '</td>';
	print '<td colspan="3">';
	$doleditor = new DolEditor('description', $object->description, '', 160, 'dolibarr_notes', 'In', true, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
	$doleditor->Create();
	print '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["soltrs"] . '</td>';
	print '<td colspan="3">' . $extrafields->showInputField("soltrs", $object->array_options["options_soltrs"]) . '</td>';
	print '</tr>';

	print '<tr class="liste_titre"><td align="center" colspan="4">Cloture</td></tr>';

	print '<tr>';
	print '<td class="fieldrequired"  width="20%">';
	print $langs->trans('LeadDeadLine');
	print '</td>';
	print '<td>';
	print $form->select_date($object->date_closure, 'deadline', 0, 0, 0, "addlead", 1, 1, 0, 0);
	print '</td>';
	print '<td colspan ="2">' . ' ' . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . ' ' . '</td>';
	print '<td>' . ' ' . '</td>';
	print '<td width="15%"> </td>';
	print '<td width="35%"> </td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["motif"] . '</td>';
	print '<td>' . $extrafields->showInputField("motif", $object->array_options["options_motif"]) . '</td>';
	print '<td>' . $extrafields->attribute_label["marque"] . '</td>';
	print '<td>' . $extrafields->showInputField("marque", $object->array_options["options_marque"]) . '</td>';
	print '</tr>';

	print '</table>';

	$out .= "\n";
	$out .= '
					<script type="text/javascript">
				    jQuery(document).ready(function() {
				    	function showOptions(child_list, parent_list)
				    	{
				    		var val = $("select[name=\""+parent_list+"\"]").val();
				    		var parentVal = parent_list + ":" + val;
							if(val > 0) {
					    		$("select[name=\""+child_list+"\"] option[parent]").hide();
					    		$("select[name=\""+child_list+"\"] option[parent=\""+parentVal+"\"]").show();
							} else {
								$("select[name=\""+child_list+"\"] option").show();
							}
				    	}
						function setListDependencies() {
					    	jQuery("select option[parent]").parent().each(function() {
					    		var child_list = $(this).attr("name");
								var parent = $(this).find("option[parent]:first").attr("parent");
								var infos = parent.split(":");
								var parent_list = infos[0];
								$("select[name=\""+parent_list+"\"]").change(function() {
									showOptions(child_list, parent_list);
								});
					    	});
						}
						setListDependencies();

						$("#leadtype").change();
				    });
				</script>' . "\n";
	$out .= '<!-- /showOptionalsInput --> ' . "\n";

	print $out;

	print "</div>\n";

	print '<div class="tabsAction">';

} else {
	$formconfirm = '';
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('LeadDelete'), $langs->trans('LeadConfirmDelete'), 'confirm_delete', '', 0, 1);
	}

	if ($action == 'close') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('LeadLost'), $langs->trans('LeadConfirmLost'), 'confirm_lost', '', 0, 1);
	}

	$userstatic = new User($db);
	$result = $userstatic->fetch($object->fk_user_resp);
	if ($result < 0) {
		setEventMessages($userstatic->errors, 'errors');
	}

	if ($action == 'ext_order') {
		$sql = $formquestion = array(
				array(
						'type' => 'text',
						'name' => 'price',
						'label' => 'Prix total HT de la commande',
						'value' => ''
				),
				array(
						'type' => 'date',
						'name' => 'del_date',
						'label' => 'Date de livraison prévue',
						'value' => ''
				)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, 'Commander', 'Commander le véhicule ?', 'ext_head_confirm_order', $formquestion, 'yes', 1);
	}

	print $formconfirm;

	print '<table class="border" width="100%">';

	print '<tr class="liste_titre"><td align="center" colspan="4">Descriptif Affaire</td></tr>';
	print '<tr>';
	print '<td width="15%">' . $langs->trans('Ref') . '</td>';
	print '<td width="35%">' . $object->ref . '</td>';
	print '<td width="15%">Numero de dossier</td>';
	print '<td width="35%">' . $object->ref_int . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td> Canal de Vente </td>';
	print '<td>' . $object->type_label . '</td>';
	print '<td>Commercial</td>';
	print '<td>' . $userstatic->getFullName($langs) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $langs->trans('LeadStatus') . '</td>';
	print '<td>' . $object->status_label . '</td>';
	print '<td colspan="2"><table width="100%" class="nobordernopadding"><tr><td>' . $extrafields->attribute_label["chaude"] . '</td>';
	print '<td>' . $chaude . '</td>';
	print '<td>' . $extrafields->attribute_label["new"] . '</td>';
	print '<td>' . $new . '</td>';

	print '</tr></table></tr>';

	print '<tr>';
	print '<td>Client</td>';
	print '<td><a href="' . dol_buildpath('/lead/lead/list.php', 1) . '?socid=' . $object->thirdparty->id . '">' . $object->thirdparty->name . '</a></td>';
	print '<td>' . $extrafields->attribute_label["ctm"] . '</td>';
	print '<td>' . $extrafields->showOutputField("ctm", $object->array_options["options_ctm"]) . '</td>';
	print '</tr>';

	print '<tr class="liste_titre"><td align="center" colspan="4">Caracteristiques</td></tr>';

	print '<tr>';
	print '<td width="15%">' . $langs->trans('LeadAmountGuess') . '</td>';
	print '<td width="35%">' . price($object->amount_prosp, 'HTML') . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="15%">' . $extrafields->attribute_label["nbchassis"] . '</td>';
	print '<td width="35%">' . $extrafields->showOutputField("nbchassis", $object->array_options["options_nbchassis"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["gamme"] . '</td>';
	print '<td>' . $extrafields->showOutputField("gamme", $object->array_options["options_gamme"]) . '</td>';
	print '<td>' . $extrafields->attribute_label["silouhette"] . '</td>';
	print '<td>' . $extrafields->showOutputField("silouhette", $object->array_options["options_silouhette"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["type"] . '</td>';
	print '<td>' . $extrafields->showOutputField("type", $object->array_options["options_type"]) . '</td>';
	print '<td>' . $extrafields->attribute_label["carroserie"] . '</td>';
	print '<td>' . $extrafields->showOutputField("carroserie", $object->array_options["options_carroserie"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $langs->trans('LeadDescription') . '</td>';
	print '<td>' .  $object->description . '</td>';
	print '<td>' . $extrafields->attribute_label["specif"] . '</td>';
	print '<td>' . $extrafields->showOutputField("specif", $object->array_options["options_specif"]) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["soltrs"] . '</td>';
	print '<td colspan="3">' . $extrafields->showOutputField("soltrs", $object->array_options["options_soltrs"]) . '</td>';
	print '</tr>';

	print '<tr class="liste_titre"><td align="center" colspan="4">Cloture</td></tr>';

	print '<tr>';
	print '<td width="15%">' . $langs->trans('LeadDeadLine') . '</td>';
	print '<td width="35%">' . dol_print_date($object->date_closure, 'daytext') . '</td>';
	print '<td> Statut</td>';
	print '<td>' . $status . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td colspan="2"> Montant total commandé a date: ' . price($object->getRealAmount2(), 'HTML') . ' ' . $langs->getCurrencySymbol($conf->currency);
	print '<br>Nb de chassis commandé a date: ' . $object->getnbchassisreal() . ' </td>';
	print '<td colspan="2"> Marge réele totale a date: ' . price($object->getmargin('real'), 'HTML'). ' ' . $langs->getCurrencySymbol($conf->currency);
	print "<br>Marge totale de l'affaire a date: " . price($object->getmargin('theo'), 'HTML'). ' ' . $langs->getCurrencySymbol($conf->currency) . ' </td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["motif"] . '</td>';
	print '<td>' . $extrafields->showOutputField("motif", $object->array_options["options_motif"]) . '</td>';
	print '<td>' . $extrafields->attribute_label["marque"] . '</td>';
	print '<td>' . $extrafields->showOutputField("marque", $object->array_options["options_marque"]) . '</td>';
	print '</tr>';

	print '</table>';

	print "</div>\n";

	print '<div class="tabsAction">';
	if ( $user->rights->lead->write) {
		if ($object->fk_c_status == 6){
		print '<div class="inline-block divButAction"><a href="javascript:popCreateOrder()" class="butAction">Passer une commande</a></div>';
		}
		print '<div class="inline-block divButAction"><a href="javascript:popCreatecalendar()" class="butAction">Ettablir le calendrier</a></div>';
		print '<input type="hidden" name="ordercreatedid" id="ordercreatedid" />';
		print '<input type="hidden" name="calendarcreatedid" id="calendarcreatedid" />';
		?>
	<script type="text/javascript">
		function popCreateOrder() {
			$div = $('<div id="popCreateOrder"><iframe width="100%" height="100%" frameborder="0" src="<?php echo dol_buildpath('/volvo/orders/createorder.php?leadid='.$object->id,1) ?>"></iframe></div>');
			$div.dialog({
				modal:true
				,width:"90%"
				,height:$(window).height() - 50
				,close:function() {document.location.href='<?php echo dol_buildpath('/commande/card.php',2).'?id=';?>'+$('#ordercreatedid').val();}
			});
	  	}
		function popCreatecalendar() {
			$div = $('<div id="popCreateCalendar"><iframe width="100%" height="100%" frameborder="0" src="<?php echo dol_buildpath('/volvo/event/createcalendar.php?leadid='.$object->id,1) ?>"></iframe></div>');
			$div.dialog({
				modal:true
				,width:"90%"
				,height:$(window).height() - 50
				,close:function() {document.location.href='<?php echo dol_buildpath('/lead/lead/card.php',2).'?id=';?>'+$('#ordercreatedid').val();}
			});
	  	}
	</script>
<?php
	}
}
?>

<!-- END PHP TEMPLATE CONTACTS -->