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
$confirm = GETPOST('confirm', 'alpha');
$id = GETPOST('id',int);
$ref_int = GETPOST('ref_int', 'alpha');
$socid = GETPOST('socid', 'int');
if ($socid == - 1)
	$socid = 0;
$userid = GETPOST('userid', 'int');
$leadstatus = GETPOST('leadstatus', 'int');
$leadtype = GETPOST('leadtype', 'int');
$amount_guess = GETPOST('amount_guess');
$description = GETPOST('description');
$deadline = dol_mktime(0, 0, 0, GETPOST('deadlinemonth'), GETPOST('deadlineday'), GETPOST('deadlineyear'));

$date_relance = dol_mktime(0, 0, 0, GETPOST('date_relancemonth'), GETPOST('date_relanceday'), GETPOST('date_relanceyear'));

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
	print '<td class="fieldrequired" width="300px">Commercial</td>';
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
	print '<div class="tabsAction">';
	print '<input type="submit" class="button" value="' . $langs->trans("Create") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';

	print '</form>';
	dol_fiche_end();

	?>
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
				</script>
	<?php
}
elseif ($action == 'edit') {

	$head = lead_prepare_head($object);
	dol_fiche_head($head, 'card', $langs->trans('Module103111Name'), 0, dol_buildpath('/lead/img/object_lead.png', 1), 1);

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

	print '<div class="tabsAction">';
	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';

	print '</form>';

	dol_fiche_end();
	?>
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
	</script>
<?php
}else{
	/*
	 * Show object in view mode
	 */
	$head = lead_prepare_head($object);
	dol_fiche_head($head, 'card', $langs->trans('Module103111Name'), 0, dol_buildpath('/lead/img/object_lead.png', 1), 1);

	// Confirm form
	$formconfirm = '';
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('LeadDelete'), $langs->trans('LeadConfirmDelete'), 'confirm_delete', '', 0, 1);
	}

	if ($action == 'close') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('LeadLost'), $langs->trans('LeadConfirmLost'), 'confirm_lost', '', 0, 1);
	}

	// Clone confirmation
	if ($action == 'clone') {
		$formquestion = array(
				array(
						'type' => 'text',
						'name' => 'ref_interne',
						'label' => $langs->trans("LeadRefInt"),
						'value' => $langs->trans('CopyOf') . ' ' . $object->ref_int
				)
		);
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('Clone'), $langs->trans('ConfirmCloneLead', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
	}

	$printformconfirm = false;
	if (empty($formconfirm)) {
		$parameters = array();
		$formconfirm = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if (! empty($formconfirm))
			$printformconfirm = true;
	}

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

	dol_fiche_end();

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
				,width:"98%"
				,height:$(window).height() - 50
				,close:function() {document.location.href='<?php echo dol_buildpath('/commande/card.php',2).'?id=';?>'+$('#ordercreatedid').val();}
			});
	  	}
		function popCreatecalendar() {
			$div = $('<div id="popCreateCalendar"><iframe width="100%" height="100%" frameborder="0" src="<?php echo dol_buildpath('/volvo/event/createcalendar.php?leadid='.$object->id,1) ?>"></iframe></div>');
			$div.dialog({
				modal:true
				,width:"98%"
				,height:$(window).height() - 50
				//,close:function() {document.location.href='<?php echo dol_buildpath('/lead/lead/card.php',2).'?id=';?>'+$('#ordercreatedid').val();}
			});
	  	}
	</script>
<?php
	}
	// Delete
	if ($user->rights->lead->write) {
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=edit">' . $langs->trans("Modifier") . "</a></div>\n";
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=clone">' . $langs->trans("Clone") . "</a></div>\n";
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=create_relance">' . $langs->trans("CreateRelance") . "</a></div>\n";
		if ($object->status[7] == $langs->trans('LeadStatus_LOST') && $object->fk_c_status != 7) {
			print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=close">' . $langs->trans("LeadLost") . "</a></div>\n";
		}
	} else {
		print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("anoughPermissions")) . '">' . $langs->trans("Edit") . "</a></div>";
		print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("anoughPermissions")) . '">' . $langs->trans("Clone") . "</a></div>";
		// print '<div class="inline-block divButAction"><font class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("NotEnoughPermissions")) . '">' . $langs->trans("LeadLost") . "</font></div>";
	}

	// Delete
	if ($user->rights->lead->delete) {
		print '<div class="inline-block divButAction"><a class="butActionDelete" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=delete">' . $langs->trans("Delete") . "</a></div>\n";
	} else {
		print '<div class="inline-block divButAction"><a class="butActionRefused" href="#" title="' . dol_escape_htmltag($langs->trans("anoughPermissions")) . '">' . $langs->trans("Delete") . "</a></div>";
	}
	print '</div>';

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
}
$db->close();
