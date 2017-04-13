<?php
$res = @include '../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

dol_include_once('/lead/class/html.formlead.class.php');
dol_include_once('/lead/lib/lead.lib.php');
dol_include_once('/core/lib/date.lib.php');
dol_include_once('/core/class/extrafields.class.php');
dol_include_once('/core/class/doleditor.class.php');
dol_include_once('/core/class/html.form.class.php');
dol_include_once('/volvo/class/lead.extend.class.php');

if (! empty($conf->facture->enabled))
	require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
if (! empty($conf->contrat->enabled))
	require_once DOL_DOCUMENT_ROOT . '/contrat/class/contrat.class.php';
if (! empty($conf->commande->enabled))
	dol_include_once('/commande/class/commande.class.php');
if (! empty($conf->agenda->enabled))
	dol_include_once('/comm/action/class/actioncomm.class.php');

	// Security check
if (! $user->rights->lead->read)
	accessforbidden();

$langs->load('lead@lead');
if (! empty($conf->propal->enabled))
	$langs->load('propal');
if (! empty($conf->facture->enabled))
	$langs->load('bills');
if (! empty($conf->contrat->enabled))
	$langs->load('contracts');
if (! empty($conf->commande->enabled))
	$langs->load('order');

$action = GETPOST('action', 'alpha');
$commercial = GETPOST('commercial', 'int');
$confirm = GETPOST('confirm', 'alpha');
$id = GETPOST('id',int);

$form = new Form($db);
$object = new Leadext($db);
$extrafields = new ExtraFields($db);
$events = array();

$error = 0;

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
if ($id > 0) {
	$ret = $object->fetch($id);
if ($ret < 0)
	setEventMessages(null, $object->errors, 'errors');
if ($ret > 0)
	$ret = $object->fetch_thirdparty();
if ($ret < 0)
	setEventMessages(null, $object->errors, 'errors');
}


if ($action == "add") {

	$error = 0;
	$soc= new Societe($db);
	$soc->fetch($socid);

	$object->ref = $object->getNextNumRef($userid,$soc);
	$object->ref_int = $object->ref;
	$object->fk_c_status = $leadstatus;
	$object->fk_c_type = $leadtype;
	$object->amount_prosp = price2num($amount_guess);
	$object->date_closure = $deadline;
	$object->fk_soc = $socid;
	$object->fk_user_resp = $userid;
	$object->fk_user_author = $userid;
	$object->description = $description;

	$extrafields->setOptionalsFromPost($extralabels, $object);

	$result = $object->create($user);
	if ($result < 0) {
		$action = 'create';
		setEventMessages(null, $object->errors, 'errors');
		$error ++;
	}

	$propalid = GETPOST('propalid', 'int');
	if (! empty($propalid)) {
		$tablename = 'propal';
		$elementselectid = $propalid;
		$result = $object->add_object_linked($tablename, $elementselectid);
		if ($result < 0) {
			setEventMessages(null, $object->errors, 'errors');
			$error ++;
		}
	}

	if ($date_relance) {

		$object->addRelance($date_relance);
	}

	if (empty($error)) {
		header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $object->id);
	}
} elseif ($action == "update") {
	$object->ref_int = $ref_int;
	$object->fk_c_status = $leadstatus;
	$object->fk_c_type = $leadtype;
	$object->amount_prosp = $amount_guess;
	$object->date_closure = $deadline;
	$object->fk_soc = $socid;
	$object->fk_user_resp = $userid;
	$object->description = $description;

	$extrafields->setOptionalsFromPost($extralabels, $object);

	$result = $object->update($user);
	if ($result < 0) {
		$action = 'edit';
		setEventMessages(null, $object->errors, 'errors');
	} else {
		header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $object->id);
	}
} elseif ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->lead->delete) {
	$result = $object->delete($user);
	if ($result < 0) {
		setEventMessages(null, $object->errors, 'errors');
	} else {
		header('Location:' . dol_buildpath('/lead/lead/list.php', 1));
	}
} elseif ($action == "addelement") {
	$tablename = GETPOST("tablename");
	$elementselectid = GETPOST("elementselect");
	$result = $object->add_object_linked($tablename, $elementselectid);
	if ($result < 0) {
		setEventMessages(null, $object->errors, 'errors');
	}
} elseif ($action == "unlink") {

	$sourceid = GETPOST('sourceid');
	$sourcetype = GETPOST('sourcetype');

	$result = $object->deleteObjectLinked($sourceid, $sourcetype);
	if ($result < 0) {
		setEventMessages(null, $object->errors, 'errors');
	}
} elseif ($action == "confirm_clone" && $confirm == 'yes') {

	$object_clone = new Lead($db);
	$object_clone->ref_int = GETPOST('ref_interne');
	$result = $object_clone->createFromClone($object->id);
	if ($result < 0) {
		setEventMessages(null, $object_clone->errors, 'errors');
	} else {
		header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $result);
	}
} elseif ($action == "confirm_lost" && $confirm == 'yes') {
	// Status 7=LOST hard coded, loaded by default in data.sql dictionnary (but check is done in this card that call this method)
	$object->fk_c_status = 7;
	$result = $object->update($user);
	if ($result < 0) {
		setEventMessages(null, $object->errors, 'errors');
	} else {
		header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $object->id);
	}
} else if ($action === 'confirm_relance' && GETPOST('confirm') === 'yes') {

	if ($date_relance) {
		$object->addRelance($date_relance);
		setEventMessage($langs->trans('relanceAdded'));
	}
}elseif ($action == "ext_head_confirm_order" && $confirm == 'yes') {
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
/*
 * View
 */

$form = new Form($db);
$formlead = new FormLead($db);

$now = dol_now();

top_htmlhead('', '');

if ($action === 'create_relance') {
	print $form->formconfirm("card.php?id=" . $object->id, $langs->trans("CreateRelance"), $langs->trans("ConfirmCreateRelance"), "confirm_relance", array(
			array(
					'type' => 'date',
					'name' => 'date_relance'
			)
	), '', 1);
}

// Add new proposal
if ($action == 'create' && $user->rights->lead->write) {
	dol_fiche_head();
	print '<form name="addlead" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="propalid" value="' . GETPOST('propalid', 'int') . '">';
	print '<input type="hidden" name="action" value="add">';

	print '<table class="border" width="100%">';

	print '<tr class="liste_titre"><td align="center" colspan="4">Descriptif Affaire</td></tr>';
	print '<tr>';
	print '<td class="fieldrequired" width="300px">Client</td>';
	print '<td width="690px">' . $form->select_thirdparty_list($socid, 'socid', 'client<>0', 1, 0, 0, $events) . '</td>';
	print '<td class="fieldrequired" width="150px">Commercial</td>';
	print '<td>' . $form->select_dolusers(empty($userid) ? $user->id : $userid, 'userid', 0, array(), 0, $includeuserlist, '', 0, 0, 0, '', 0, '', '', 1) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td>' . $extrafields->attribute_label["ctm"] . '</td>';
	print '<td>' . $extrafields->showInputField("ctm", 0) . '</td>';
	print '<td class="fieldrequired"> Canal de Vente </td>';
	print '<td>' . $formlead->select_lead_type($leadtype, 'leadtype', 0) . '</td>';
	print '</tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $langs->trans('LeadStatus') . '</td>';
	print '<td>' . $formlead->select_lead_status($leadstatus, 'leadstatus', 0) . '</td>';
	print '<td colspan="2"><table width="100%" class="nobordernopadding"><tr><td>' . $extrafields->attribute_label["chaude"] . '</td>';
	print '<td>' . $extrafields->showInputField("chaude", 1) . '</td>';
	print '<td>' . $extrafields->attribute_label["new"] . '</td>';
	print '<td>' . $extrafields->showInputField("new", 0) . '</td>';
	print '</tr></table></tr>';

	print '<tr class="liste_titre"><td align="center" colspan="4">Caracteristiques</td></tr>';

	print '<tr>';
	print '<td class="fieldrequired">' . $langs->trans('LeadAmountGuess') . '</td>';
	print '<td><input type="text" name="amount_guess" size="5" value="' . price2num($amount_guess) . '"/></td>';
	print '<td class="fieldrequired">' . $extrafields->attribute_label["nbchassis"] . '</td>';
	print '<td>' . $extrafields->showInputField("nbchassis", 0) . '</td>';
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
	print '<input type="submit" class="button" value="' . $langs->trans("Create") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';

	print '</form>';

	dol_fiche_end();
}
$db->close();
