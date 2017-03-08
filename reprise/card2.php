<?php
$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

dol_include_once('/volvo/class/reprise.class.php');
dol_include_once('/volvo/class/expertise.class.php');
dol_include_once('/volvo/lib/reprise.lib.php');
dol_include_once('/core/class/html.form.class.php');
dol_include_once('/core/class/doleditor.class.php');
dol_include_once('/core/lib/files.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
dol_include_once('/volvo/class/lead.extend.class.php');

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$confirm = GETPOST('confirm', 'alpha');
$exp = GETPOST('exp', 'int');
$lead = GETPOST('fk_lead','int');

$form = new Form($db);
$reprise = new Reprise($db);
$reprise->fetch($id);
$reprise->fetch_thirdparty();
$reprise->updatestatus($reprise->id);
$expertise = new Expertise($db);
$objlead = New Leadext($db);
if(!empty($lead)){
	$leadok = $objlead->fetch($lead);
}else{
	$leadok = $objlead->fetch($reprise->fk_lead);
}

$upload_dir = $conf->volvo->dir_output .'/'. dol_sanitizeFileName($reprise->ref);

include_once DOL_DOCUMENT_ROOT . '/volvo/template/document_actions_pre_headers2.tpl.php';

if ($action == 'update_valeur' && $user->rights->volvo->admin){
	$reprise->rachat = GETPOST('rachat', 'int');
	$reprise->update($user);
}

if ($action == 'update_estim' && $user->rights->volvo->admin){
	$reprise->estim = GETPOST('estim', 'int');
	$reprise->update($user);
}

if ($action == 'update_reprise' && $user->rights->volvo->modif_ig){

	foreach($reprise->value_array as $value){
		if (isset($_POST[$value])){
			$reprise->$value = GETPOST($value);
		}
	}

	if(isset($_POST['dateentree_'])){$reprise->date_entree = dol_mktime(0, 0, 0, GETPOST('dateentree_month'), GETPOST('dateentree_day'), GETPOST('dateentree_year'));}
	if(isset($_POST['circ_'])){$reprise->circ = dol_mktime(0, 0, 0, GETPOST('circ_month'), GETPOST('circ_day'), GETPOST('circ_year'));}
	if(isset($_POST['validmine_'])){$reprise->validmine = dol_mktime(0, 0, 0, GETPOST('validmine_month'), GETPOST('validmine_day'), GETPOST('validmine_year'));}
	if(isset($_POST['validtachy_'])){$reprise->validtachy = dol_mktime(0, 0, 0, GETPOST('validtachy_month'), GETPOST('validtachy_day'), GETPOST('validtachy_year'));}
	if(isset($_POST['date1_'])){$reprise->date1 = dol_mktime(0, 0, 0, GETPOST('date1_month'), GETPOST('date1_day'), GETPOST('date1_year'));}
	if(isset($_POST['date2_'])){$reprise->date2 = dol_mktime(0, 0, 0, GETPOST('date2_month'), GETPOST('date2_day'), GETPOST('date2_year'));}
	if(isset($_POST['dateagrement_'])){$reprise->dateagrement = dol_mktime(0, 0, 0, GETPOST('dateagrement_month'), GETPOST('dateagrement_day'), GETPOST('dateagrement_year'));}
	$res = $reprise->update($user);
	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $reprise->id);
}

if ($action == 'create_reprise' && $user->rights->volvo->modif_ig){
	foreach($reprise->value_array as $value){
		if (isset($_POST[$value])){
			$reprise->$value = GETPOST($value);
		}
	}
	$reprise->estim = 0;
	$reprise->rachat = 0;
	if(isset($_POST['dateentree_'])){$reprise->date_entree = dol_mktime(0, 0, 0, GETPOST('dateentree_month'), GETPOST('dateentree_day'), GETPOST('dateentree_year'));}
	if(isset($_POST['circ_'])){$reprise->circ = dol_mktime(0, 0, 0, GETPOST('circ_month'), GETPOST('circ_day'), GETPOST('circ_year'));}
	if(isset($_POST['validmine_'])){$reprise->validmine = dol_mktime(0, 0, 0, GETPOST('validmine_month'), GETPOST('validmine_day'), GETPOST('validmine_year'));}
	if(isset($_POST['validtachy_'])){$reprise->validtachy = dol_mktime(0, 0, 0, GETPOST('validtachy_month'), GETPOST('validtachy_day'), GETPOST('validtachy_year'));}
	if(isset($_POST['date1_'])){$reprise->date1 = dol_mktime(0, 0, 0, GETPOST('date1_month'), GETPOST('date1_day'), GETPOST('date1_year'));}
	if(isset($_POST['date2_'])){$reprise->date2 = dol_mktime(0, 0, 0, GETPOST('date2_month'), GETPOST('date2_day'), GETPOST('date2_year'));}
	if(isset($_POST['dateagrement_'])){$reprise->dateagrement = dol_mktime(0, 0, 0, GETPOST('dateagrement_month'), GETPOST('dateagrement_day'), GETPOST('dateagrement_year'));}
	$res = $reprise->create($user);
	header('Location:' . $_SERVER["PHP_SELF"] . '?id=' . $res);
}

if ($action == 'confirm_delete' && $confirm == 'yes' && $user->admin) {
	$reprise->delete($user);
	header('Location:' . dol_buildpath('/volvo/reprise/list.php',2));
}

if ($action == 'confirm_desactive' && $confirm == 'yes') {
	if($reprise->actif == 1){
		$reprise->actif=0;
	}else{
		$reprise->actif=1;
	}
	$reprise->status = $reprise->getstatus();
	$reprise->update($user);
}

llxHeader('', 'reprise');

$expertise->fetchAll('','',0,0,array('t.fk_reprise'=>$id));
$head = info_prepare_head2($expertise->liste_exp,$id);
dol_fiche_head($head, 'card', 'reprise', 0, dol_buildpath('/volvo/img/object_iron02.png', 1), 1);

if ($action == 'create' && $user->rights->volvo->modif_ig){

print '<form name="estimreprise" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&repid=' . $repid . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="create_reprise">';
	print '<input type="hidden" name="fk_lead" value="' . $lead . '">';
	print '<input type="hidden" name="actif" value="1">';
	print '<input type="hidden" name="status" value="0">';

	print '<TABLE width="100%"  class="border">';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR><TD width="30%" rowspan="3"><img src="' . dol_buildpath('/volvo/img/logo theobald.png',2) . '" height="60" width="142"></TD><TD colspan="2"><H1>FICHE SIGNALETIQUE VEHICULE DE REPRISE</H1></TD></TR>';
					print '<TR><TD width="35%">Statut : En cour s de création</TD><TD width="35%">Affaire : ' . (!empty($objlead->id) ? $objlead->getNomUrl(1) :'') . '</TD></TR>';
					print '<TR><TD width="35%">Référence : <input type="text" name="ref" size="25" value=""/></TD><TD width="35%">Police : <input type="text" name="police" size="25" value=""/></TD></TR>';
				print '</TABLE>';
				print '</br>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" width="100%" valign="top">';
				print '<TABLE width="100%" class="nobordernopadding">';
					print '<TR>';
						print '<TD class="fieldrequired" width="70%">Proprietaire</TD>';
						print '<TD class="fieldrequired" width="15%">' . "Date d'entree prévue" . '</TD>';
						print '<TD class="valeur" width="15%">' . $form->select_date('', 'dateentree_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="valeur">' . $form->select_thirdparty_list('','fk_soc') . '</TD>';
						print '<TD class="fieldrequired">' . "Site de restitution" . '</TD>';
						print '<TD class="valeur">' . $form->selectarray('fk_restit',$reprise->sites,'',1) . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="50%" colspan="2"><b>Marque : </b>' . $form->selectarray('fk_marque',$reprise->marque,'',1) . '</TD>';
						print '<TD class="Valeur" width="50%" colspan="2"><b>Genre : </b>' . $form->selectarray('fk_genre',$reprise->genre,'',1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Type : </b><input type="text" name="type" size="20" value=""/></TD>';
						print '<TD class="Valeur" colspan="2"><b>Silouhette : </b>' . $form->selectarray('fk_silouhette',$reprise->silouhette,'',1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>N° de série : </b><input type="text" name="numserie" size="20" value=""/></TD>';
						print '<TD class="Valeur" colspan="2"><b>Norme : </b>' . $form->selectarray('fk_norme',$reprise->norme,'',1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="25%"><b>Carrosserie</b></TD>';
						print '<TD class="Valeur" colspan="3" width="75%">';
						$doleditor = new DolEditor('carrosserie', '', '', 80, 'dolibarr_details', 'In', false, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
						$doleditor->Create();
						print '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Puissance Commerciale : </b><input type="text" name="puiscom" size="5" value=""/>Cv</TD>';
						print '<TD class="Valeur" colspan="2"><b>Puissance Fiscale : </b><input type="text" name="puisfisc" size="5" value=""/>Cv</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>PTC : </b><input type="text" name="ptc" size="5" value=""/>Tonnes</TD>';
						print '<TD class="Valeur" colspan="2"><b>PV : </b><input type="text" name="pv" size="5" value=""/>Tonnes</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>PTR : </b><input type="text" name="ptr" size="5" value=""/>Tonnes</TD>';
						print '<TD class="Valeur" colspan="2"><b>Charge Utile : </b><input type="text" name="chargutil" size="5" value=""/>Tonnes</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Longueur Utile : </b><input type="text" name="longutil" size="5" value=""/>m</TD>';
						print '<TD class="Valeur" colspan="2"><b>Largeur Utile : </b><input type="text" name="largutil" size="5" value=""/>m</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Places assises : </b><input type="text" name="place" size="5" value=""/></TD>';
						print '<TD class="Valeur" colspan="2"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="46%" colspan="2"><b>immatriculation : </b><input type="text" name="immat" size="5" value=""/></TD>';
						print '<TD class="Valeur" width="54%" colspan="2"><b>1ere mise en circ. : </b>' .$form->select_date('', 'circ_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Kilometrage actuel : </b><input type="text" name="kmact" size="5" value=""/>km</TD>';
						print '<TD class="Valeur" colspan="2"><b>Km prévu a la restitution : </b><input type="text" name="kmrestit" size="5" value=""/>km</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="73%" colspan="3"><b>Validité des mines : (> 3 mois à l’entrée véhicule)  : </b></TD>';
						print '<TD class="Valeur" width="27%">' . $form->select_date('', 'validmine_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité du Tachygraphe (> 3 mois à l’entrée véhicule): </b></TD>';
						print '<TD class="Valeur">' . $form->select_date('', 'validtachy_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité <input type="text" name="valid1" size="27" value=""/>: (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . $form->select_date('', 'date1_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité <input type="text" name="valid2" size="27" value=""/>: (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . $form->select_date('', 'date2_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Certificat Agrément <input type="text" name="agrement" size="17" value=""/>: (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . $form->select_date('', 'dateagrement_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="45%" colspan="2"><b>Cabine : </b>' . $form->selectarray('fk_cabine',$reprise->cabine,'',1) . '</TD>';
						print '<TD class="Valeur" width="55%" colspan="2"><b>Suspension de cabine : </b>' . $form->selectarray('fk_suspcabine',$reprise->suspcabine,'',1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur"colspan="2"><b>Moteur : </b>' . $form->selectarray('fk_moteur',$reprise->moteur,'',1) . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Ralentisseur : </b>' . $form->selectarray('fk_ralentisseur',$reprise->ralentisseur,'',1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Boite de vitesses : </b>' . $form->selectarray('fk_bv',$reprise->bv,'',1) . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Rapport : </b><input type="text" name="rav" size="1" value=""> + <input type="text" name="rar" size="1" value=""/></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>pont : </b><INPUT type="checkbox" name="sr" value="1"/><b>SR</b>&emsp;';
						print '<INPUT type="checkbox" name="dr" value="1"/><b>DR</b>&emsp;';
						print '<INPUT type="checkbox" name="blocage" value="1"/><b>Blocage</b></TD>';
						print '<TD class="Valeur" colspan="2"><b>Rapport de pont : </b><input type="text" name="rapport" size="3" value=""></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Freinage : </b>' . $form->selectarray('fk_freinage',$reprise->freinage,'',1) . '</TD>';
						print '<TD class="Valeur" colspan="2">';
						print '<INPUT type="checkbox" name="abs1" value="1"/><b>ABS</b>&emsp;';
						print '<INPUT type="checkbox" name="asr" value="1"/><b>ASR</b>&emsp;';
						print '<INPUT type="checkbox" name="ebs" value="1"/><b>EBS</b>&emsp;';
						print '<INPUT type="checkbox" name="esp" value="1"/><b>ESP</b>&emsp;';
						print '<INPUT type="checkbox" name="dfr" value="1"/><b>DFR</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" rowspan="2"><b>Suspension Chassis : </b></TD>';
						print '<TD class="Valeur" width="11%"><b>Avant : </b></TD>';
						print '<TD class="Valeur" colspan="2"><input type="text" name="suspav" size="32" value=""/></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur"><b>Arrière : </b></TD>';
						print '<TD class="Valeur" colspan="2"><input type="text" name="suspar" size="32" value=""/></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
			print '<TD width="50%" align="center" valign="top">';
			print "<b>Informations sur l'état et la qualité des pneumatiques</b></br>";
				print '<TABLE width="98%" style="border-collapse:collapse">';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center">Emp.</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="16%" align="center">Marque</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="22%" align="center" colspan="3">Taille</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="50%" align="center">Profil</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center" colspan="2">Usure</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center">AV</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="20%" align="center">' . $form->selectarray('fk_mav',$reprise->marquepneu,'',1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="12%" align="center"><input type="text" name="tav" size="3" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="4%" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="8%" align="center"><input type="text" name="dav" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="40%" align="center"><input type="text" name="pav" size="17" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="8%" align="center"><input type="text" name="uav" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="4%" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR1</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $form->selectarray('fk_mar1',$reprise->marquepneu,'',1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="tar1" size="3" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="dar1" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="par1" size="17" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="uar1" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR2</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $form->selectarray('fk_mar2',$reprise->marquepneu,'',1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="tar2" size="3" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="dar2" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="par2" size="17" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="uar2" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR3</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $form->selectarray('fk_mar3',$reprise->marquepneu,'',1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="tar3" size="3" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="dar3" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="par3" size="17" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="uar3" size="1" value=""/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Chassis : </b>';
						print '<INPUT type="checkbox" name="nisoude" value="1"/><b> Ni soudé</b>&emsp;&emsp;';
						print '<INPUT type="checkbox" name="nifissure" value="1"/><b> Ni fissuré</b></TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Nb réservoir GO : </b><input type="text" name="nbreserv" size="1" value=""/></TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Capacité Gasoil : </b><input type="text" name="capago" size="4" value=""/>litres</TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Capacité AdBlue : </b><input type="text" name="adblue" size="3" value=""/>litres</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"><b>Etat mécanique : </b><input type="text" name="etatmeca" size="49" value=""/></TD>';
						print '<TD class="Valeur" colspan="4"><b>Présentation : </b><input type="text" name="pres" size="52" value=""/></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="lve" value="1"/><b> 2 LVE</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="rct" value="1"/><b> 2 RCT</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="gyro" value="1"/><b> Gyrophare</b></TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><INPUT type="checkbox" name="echapv" value="1"/><b> Echappement vertical</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="adr" value="1"/><b> adr</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="hydro" value="1"/><b> Hydrolique</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="compresseur" value="1"/><b> Compresseur</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="25%" Colspan="2"><INPUT type="checkbox" name="climtoit" value="1"/><b> Climatisation de toit</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="clim" value="1"/><b> Climatisation</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="webasto" value="1"/><b> Webasto</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="deflecteur" value="1"/><b> Déflecteurs</b></TD>';
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="jupes" value="1"/><b> Jupes</b></TD>';
						print '<TD class="Valeur" width="25%" Colspan="2"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="100%" Colspan="6" align=center><b>Documents a récupérer</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="15%"></TD>';
						print '<TD class="Valeur" width="15%"><INPUT type="checkbox" name="copiecg" value="1"/><b> Copie Carte Grise</b></TD>';
						print '<TD class="Valeur" width="25%"><INPUT type="checkbox" name="copiect" value="1"/><b> Copie dernier controle technique</b></TD>';
						print '<TD class="Valeur" width="20%"><INPUT type="checkbox" name="copieca" value="1"/><b> Copie certificat agrément</b></TD>';
						print '<TD class="Valeur" width="10%"><INPUT type="checkbox" name="photo" value="1"/><b> Photos</b></TD>';
						print '<TD class="Valeur" width="15%"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
	print '</TABLE>';

	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
	print '</form>';

} elseif ($action =='edit' && $user->rights->volvo->modif_ig){

	print '<form name="estimreprise" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_reprise">';
	print '<input type="hidden" name="fk_lead" value="' . $reprise->fk_lead . '">';

	print '<TABLE width="100%"  class="border">';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR><TD width="30%" rowspan="3"><img src="' . dol_buildpath('/volvo/img/logo theobald.png',2) . '" height="60" width="142"></TD><TD colspan="2"><H1>FICHE SIGNALETIQUE VEHICULE DE REPRISE</H1></TD></TR>';
					print '<TR><TD width="35%">Statut : ' . $reprise->getLibStatut(2) . '</TD><TD width="35%">Affaire : ' . (!empty($objlead->id) ? $objlead->getNomUrl(1) :'') . '</TD></TR>';
					print '<TR><TD width="35%">Référence : <input type="text" name="ref" size="25" value="' . $reprise->ref . '"/></TD><TD width="35%">Police : <input type="text" name="police" size="25" value="' . $reprise->police . '"/></TD></TR>';
				print '</TABLE>';
				print '</br>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" width="100%" valign="top">';
				print '<TABLE width="100%" class="nobordernopadding">';
					print '<TR>';
						print '<TD class="fieldrequired" width="70%">Proprietaire</TD>';
						print '<TD class="fieldrequired" width="15%">' . "Date d'entree prévue" . '</TD>';
						print '<TD class="valeur" width="15%">' . $form->select_date($reprise->date_entree, 'dateentree_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="valeur">' . $form->select_thirdparty_list($reprise->fk_soc,'fk_soc') . '</TD>';
						print '<TD class="fieldrequired">' . "Site de restitution" . '</TD>';
						print '<TD class="valeur">' . $form->selectarray('fk_restit',$reprise->sites,$reprise->fk_restit,1) . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="50%" colspan="2"><b>Marque : </b>' . $form->selectarray('fk_marque',$reprise->marque,$reprise->fk_marque,1) . '</TD>';
						print '<TD class="Valeur" width="50%" colspan="2"><b>Genre : </b>' . $form->selectarray('fk_genre',$reprise->genre,$reprise->fk_genre,1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Type : </b><input type="text" name="type" size="20" value="' . $reprise->type . '"/></TD>';
						print '<TD class="Valeur" colspan="2"><b>Silouhette : </b>' . $form->selectarray('fk_silouhette',$reprise->silouhette,$reprise->fk_silouhette,1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>N° de série : </b><input type="text" name="numserie" size="20" value="' . $reprise->numserie . '"/></TD>';
						print '<TD class="Valeur" colspan="2"><b>Norme : </b>' . $form->selectarray('fk_norme',$reprise->norme,$reprise->fk_norme,1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="25%"><b>Carrosserie</b></TD>';
						print '<TD class="Valeur" colspan="3" width="75%">';
						$doleditor = new DolEditor('carrosserie', $reprise->carrosserie, '', 80, 'dolibarr_details', 'In', false, false, $conf->global->FCKEDITOR_ENABLE || $conf->global->FCKEDITOR_ENABLE_SOCIETE, 4, 90);
						$doleditor->Create();
						print '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Puissance Commerciale : </b><input type="text" name="puiscom" size="5" value="' . $reprise->puiscom . '"/>Cv</TD>';
						print '<TD class="Valeur" colspan="2"><b>Puissance Fiscale : </b><input type="text" name="puisfisc" size="5" value="' . $reprise->puisfisc . '"/>Cv</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>PTC : </b><input type="text" name="ptc" size="5" value="' . $reprise->ptc . '"/>Tonnes</TD>';
						print '<TD class="Valeur" colspan="2"><b>PV : </b><input type="text" name="pv" size="5" value="' . $reprise->pv . '"/>Tonnes</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>PTR : </b><input type="text" name="ptr" size="5" value="' . $reprise->ptr . '"/>Tonnes</TD>';
						print '<TD class="Valeur" colspan="2"><b>Charge Utile : </b><input type="text" name="chargutil" size="5" value="' . $reprise->chargutil . '"/>Tonnes</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Longueur Utile : </b><input type="text" name="longutil" size="5" value="' . $reprise->longutil . '"/>m</TD>';
						print '<TD class="Valeur" colspan="2"><b>Largeur Utile : </b><input type="text" name="largutil" size="5" value="' . $reprise->largutil . '"/>m</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Places assises : </b><input type="text" name="place" size="5" value="' . $reprise->place . '"/></TD>';
						print '<TD class="Valeur" colspan="2"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="46%" colspan="2"><b>immatriculation : </b><input type="text" name="immat" size="5" value="' . $reprise->immat . '"/></TD>';
						print '<TD class="Valeur" width="54%" colspan="2"><b>1ere mise en circ. : </b>' .$form->select_date($reprise->circ, 'circ_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Kilometrage actuel : </b><input type="text" name="kmact" size="5" value="' . $reprise->kmact . '"/>km</TD>';
						print '<TD class="Valeur" colspan="2"><b>Km prévu a la restitution : </b><input type="text" name="kmrestit" size="5" value="' . $reprise->kmrestit . '"/>km</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="73%" colspan="3"><b>Validité des mines : (> 3 mois à l’entrée véhicule)  : </b></TD>';
						print '<TD class="Valeur" width="27%">' . $form->select_date($reprise->validmine, 'validmine_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité du Tachygraphe (> 3 mois à l’entrée véhicule): </b></TD>';
						print '<TD class="Valeur">' . $form->select_date($reprise->validtachy, 'validtachy_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité <input type="text" name="valid1" size="27" value="' . $reprise->valid1 . '"/>: (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . $form->select_date($reprise->date1, 'date1_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité <input type="text" name="valid2" size="27" value="' . $reprise->valid2 . '"/>: (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . $form->select_date($reprise->date2, 'date2_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Certificat Agrément <input type="text" name="agrement" size="17" value="' . $reprise->agrement . '"/>: (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . $form->select_date($reprise->dateagrement, 'dateagrement_',0,0,1,'',1,1,1,0,'','','') . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="45%" colspan="2"><b>Cabine : </b>' . $form->selectarray('fk_cabine',$reprise->cabine,$reprise->fk_cabine,1) . '</TD>';
						print '<TD class="Valeur" width="55%" colspan="2"><b>Suspension de cabine : </b>' . $form->selectarray('fk_suspcabine',$reprise->suspcabine,$reprise->fk_suspcabine,1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur"colspan="2"><b>Moteur : </b>' . $form->selectarray('fk_moteur',$reprise->moteur,$reprise->fk_moteur,1) . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Ralentisseur : </b>' . $form->selectarray('fk_ralentisseur',$reprise->ralentisseur,$reprise->fk_ralentisseur,1) . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Boite de vitesses : </b>' . $form->selectarray('fk_bv',$reprise->bv,$reprise->fk_bv,1) . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Rapport : </b><input type="text" name="rav" size="1" value="' . $reprise->rav . '"> + <input type="text" name="rar" size="1" value="' . $reprise->rar . '"/></TD>';
					print '</TR>';
					print '<TR>';
						if($reprise->sr==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" colspan="2"><b>pont : </b><INPUT type="checkbox" name="sr" value="1"' . $ck . '/><b>SR</b>&emsp;';
						if($reprise->dr==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="dr" value="1"' . $ck . '/><b>DR</b>&emsp;';
						if($reprise->blocage==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="blocage" value="1"' . $ck . '/><b>Blocage</b></TD>';
						print '<TD class="Valeur" colspan="2"><b>Rapport de pont : </b><input type="text" name="rapport" size="1" value="' . $reprise->rapport . '"></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Freinage : </b>' . $form->selectarray('fk_freinage',$reprise->freinage,$reprise->fk_freinage,1) . '</TD>';
						print '<TD class="Valeur" colspan="2">';
						if($reprise->abs1==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="abs1" value="1"' . $ck . '/><b>ABS</b>&emsp;';
						if($reprise->asr==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="asr" value="1"' . $ck . '/><b>ASR</b>&emsp;';
						if($reprise->ebs==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="ebs" value="1"' . $ck . '/><b>EBS</b>&emsp;';
						if($reprise->esp==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="esp" value="1"' . $ck . '/><b>ESP</b>&emsp;';
						if($reprise->dfr==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="dfr" value="1"' . $ck . '/><b>DFR</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" rowspan="2"><b>Suspension Chassis : </b></TD>';
						print '<TD class="Valeur" width="11%"><b>Avant : </b></TD>';
						print '<TD class="Valeur" colspan="2"><input type="text" name="suspav" size="32" value="' . $reprise->suspav . '"/></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur"><b>Arrière : </b></TD>';
						print '<TD class="Valeur" colspan="2"><input type="text" name="suspar" size="32" value="' . $reprise->suspar . '"/></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
			print '<TD width="50%" align="center" valign="top">';
			print "<b>Informations sur l'état et la qualité des pneumatiques</b></br>";
				print '<TABLE width="98%" style="border-collapse:collapse">';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center">Emp.</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="16%" align="center">Marque</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="22%" align="center" colspan="3">Taille</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="50%" align="center">Profil</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center" colspan="2">Usure</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center">AV</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="20%" align="center">' . $form->selectarray('fk_mav',$reprise->marquepneu,$reprise->fk_mav,1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="12%" align="center"><input type="text" name="tav" size="3" value="' . $reprise->tav . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="4%" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="8%" align="center"><input type="text" name="dav" size="1" value="' . $reprise->dav . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="40%" align="center"><input type="text" name="pav" size="17" value="' . $reprise->pav . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="8%" align="center"><input type="text" name="uav" size="1" value="' . $reprise->uav . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="4%" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR1</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $form->selectarray('fk_mar1',$reprise->marquepneu,$reprise->fk_mar1,1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="tar1" size="3" value="' . $reprise->tar1 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="dar1" size="1" value="' . $reprise->dar1 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="par1" size="17" value="' . $reprise->par1 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="uar1" size="1" value="' . $reprise->uar1 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR2</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $form->selectarray('fk_mar2',$reprise->marquepneu,$reprise->fk_mar2,1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="tar2" size="3" value="' . $reprise->tar2 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="dar2" size="1" value="' . $reprise->dar2 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="par2" size="17" value="' . $reprise->par2 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="uar2" size="1" value="' . $reprise->uar2 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR3</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $form->selectarray('fk_mar3',$reprise->marquepneu,$reprise->fk_mar3,1) . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="tar3" size="3" value="' . $reprise->tar3 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="dar3" size="1" value="' . $reprise->dar3 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="par3" size="17" value="' . $reprise->par3 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center"><input type="text" name="uar3" size="1" value="' . $reprise->uar3 . '"/></TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Chassis : </b>';
						if($reprise->nisoude==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="nisoude" value="1"' . $ck . '/><b> Ni soudé</b>&emsp;&emsp;';
						if($reprise->nifissure==1){$ck = ' checked="checked"';}
						print '<INPUT type="checkbox" name="nifissure" value="1"' . $ck . '/><b> Ni fissuré</b></TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Nb réservoir GO : </b><input type="text" name="nbreserv" size="1" value="' . $reprise->nbreserv . '"/></TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Capacité Gasoil : </b><input type="text" name="capago" size="4" value="' . $reprise->capago . '"/>litres</TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Capacité AdBlue : </b><input type="text" name="adblue" size="3" value="' . $reprise->adblue . '"/>litres</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"><b>Etat mécanique : </b><input type="text" name="etatmeca" size="49" value="' . $reprise->etatmeca . '"/></TD>';
						print '<TD class="Valeur" colspan="4"><b>Présentation : </b><input type="text" name="pres" size="52" value="' . $reprise->pres . '"/></TD>';
					print '</TR>';
					print '<TR>';
						if($reprise->lve==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="lve" value="1"' . $ck . '/><b> 2 LVE</b></TD>';
						if($reprise->rct==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="rct" value="1"' . $ck . '/><b> 2 RCT</b></TD>';
						if($reprise->gyro==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="gyro" value="1"' . $ck . '/><b> Gyrophare</b></TD>';
						if($reprise->echapv==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="25%" colspan="2"><INPUT type="checkbox" name="echapv" value="1"' . $ck . '/><b> Echappement vertical</b></TD>';
						if($reprise->adr==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="adr" value="1"' . $ck . '/><b> adr</b></TD>';
						if($reprise->hydro==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="hydro" value="1"' . $ck . '/><b> Hydrolique</b></TD>';
						if($reprise->compresseur==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="compresseur" value="1"' . $ck . '/><b> Compresseur</b></TD>';
					print '</TR>';
					print '<TR>';
						if($reprise->climtoit==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="25%" Colspan="2"><INPUT type="checkbox" name="climtoit" value="1"' . $ck . '/><b> Climatisation de toit</b></TD>';
						if($reprise->clim==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="clim" value="1"' . $ck . '/><b> Climatisation</b></TD>';
						if($reprise->webasto==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="webasto" value="1"' . $ck . '/><b> Webasto</b></TD>';
						if($reprise->deflecteur==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="deflecteur" value="1"' . $ck . '/><b> Déflecteurs</b></TD>';
						if($reprise->jupes==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="12.5%"><INPUT type="checkbox" name="jupes" value="1"' . $ck . '/><b> Jupes</b></TD>';
						print '<TD class="Valeur" width="25%" Colspan="2"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="100%" Colspan="6" align=center><b>Documents a récupérer</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="15%"></TD>';
						if($reprise->copiecg==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="15%"><INPUT type="checkbox" name="copiecg" value="1"' . $ck . '/><b> Copie Carte Grise</b></TD>';
						if($reprise->copiect==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="25%"><INPUT type="checkbox" name="copiect" value="1"' . $ck . '/><b> Copie dernier controle technique</b></TD>';
						if($reprise->copieca==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="20%"><INPUT type="checkbox" name="copieca" value="1"' . $ck . '/><b> Copie certificat agrément</b></TD>';
						if($reprise->photos==1){$ck = ' checked="checked"';}
						print '<TD class="Valeur" width="10%"><INPUT type="checkbox" name="photo" value="1"' . $ck . '/><b> Photos</b></TD>';
						print '<TD class="Valeur" width="15%"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
	print '</TABLE>';

	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
	print '</form>';

} elseif ($user->rights->volvo->lireeprise) {

	$formconfirm = '';
	if ($action == 'suppr' && $user->admin) {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'Supprimer', 'Supprimer la Reprise ?', 'confirm_delete', '', 0, 1);
	}

	if ($action == 'desactive') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, 'Désactivation', 'désactiver la reprise ?', 'confirm_desactive', '', 0, 1);
	}

	print $formconfirm;
	$reprise->getstatus();
	print '<TABLE width="100%"  class="border">';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR><TD width="30%" rowspan="3"><img src="' . dol_buildpath('/volvo/img/logo theobald.png',2) . '" height="60" width="142"></TD><TD colspan="2"><H1>FICHE SIGNALETIQUE VEHICULE DE REPRISE</H1></TD></TR>';
					print '<TR><TD width="35%">Statut : ' . $reprise->getLibStatut(2) . '</TD><TD width="35%">Affaire : ' . (!empty($objlead->id) ? $objlead->getNomUrl(1) :'') . '</TD></TR>';
					print '<TR><TD width="35%">Référence : ' . $reprise->ref . '</TD><TD width="35%">Police : ' . $reprise->police . '</TD></TR>';
				print '</TABLE>';
				print '</br>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" width="100%" valign="top">';
				print '<TABLE width="100%" class="nobordernopadding">';
					print '<TR>';
						print '<TD class="fieldrequired" width="25%">Proprietaire</TD>';
						print '<TD class="fieldrequired" width="10%" rowspan="2">Adresse</TD>';
						print '<TD class="valeur" width="35%" rowspan="2">' . dol_format_address($reprise->thirdparty,0,"</br>") . '</TD>';
						print '<TD class="fieldrequired" width="15%">' . "Date d'entree prévue" . '</TD>';
						print '<TD class="valeur" width="15%">' . dol_print_date($reprise->date_entree, 'daytext') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="valeur">' . $reprise->thirdparty->name . '</TD>';
						print '<TD class="fieldrequired">' . "Site de restitution" . '</TD>';
						print '<TD class="valeur">' . $reprise->sites[$reprise->fk_restit] . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="50%" colspan="2"><b>Marque : </b>' . $reprise->marque[$reprise->fk_marque] . '</TD>';
						print '<TD class="Valeur" width="50%" colspan="2"><b>Genre : </b>' . $reprise->genre[$reprise->fk_genre] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Type : </b>' . $reprise->type . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Silouhette : </b>' . $reprise->silouhette[$reprise->fk_silouhette] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>N° de série : </b>' . $reprise->numserie . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Norme : </b>' . $reprise->norme[$reprise->fk_norme] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="25%"><b>Carrosserie</b></TD>';
						print '<TD class="Valeur" colspan="3" width="75%">' . $reprise->carrosserie . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Puissance Commerciale : </b>' . $reprise->puiscom . ' Cv</TD>';
						print '<TD class="Valeur" colspan="2"><b>Puissance Fiscale : </b>' . $reprise->puisfisc . ' Cv</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>PTC : </b>' . $reprise->ptc . ' Tonnes</TD>';
						print '<TD class="Valeur" colspan="2"><b>PV : </b>' . $reprise->pv . ' Tonnes</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>PTR : </b>' . $reprise->ptr . ' Tonnes</TD>';
						print '<TD class="Valeur" colspan="2"><b>Charge Utile : </b>' . $reprise->chargutil . ' Tonnes</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Longueur Utile : </b>' . $reprise->longutil . ' m</TD>';
						print '<TD class="Valeur" colspan="2"><b>Largeur Utile : </b>' . $reprise->largutil . ' m</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Places assises : </b>' . $reprise->place . '</TD>';
						print '<TD class="Valeur" colspan="2"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="46%" colspan="2"><b>immatriculation : </b>' . $reprise->immat . '</TD>';
						print '<TD class="Valeur" width="54%" colspan="2"><b>1ere mise en circ. : </b>' . dol_print_date($reprise->circ, 'daytext') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Kilometrage actuel : </b>' . $reprise->kmact . ' km</TD>';
						print '<TD class="Valeur" colspan="2"><b>Km prévu a la restitution : </b>' . $reprise->kmrestit . ' km</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="73%" colspan="3"><b>Validité des mines : (> 3 mois à l’entrée véhicule)  : </b></TD>';
						print '<TD class="Valeur" width="27%">' . dol_print_date($reprise->validmine, 'daytext') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité du Tachygraphe (> 3 mois à l’entrée véhicule): </b></TD>';
						print '<TD class="Valeur">' . dol_print_date($reprise->validtachy, 'daytext') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité ' . $reprise->valid1 . ': (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . dol_print_date($reprise->date1, 'daytext') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Validité ' . $reprise->valid2 . ': (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . dol_print_date($reprise->date2, 'daytext') . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="3"><b>Certificat Agrément ' . $reprise->agrement . ': (> 3 mois)</b></TD>';
						print '<TD class="Valeur">' . dol_print_date($reprise->dateagrement, 'daytext') . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD width="50%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="45%" colspan="2"><b>Cabine : </b>' . $reprise->cabine[$reprise->fk_cabine] . '</TD>';
						print '<TD class="Valeur" width="55%" colspan="2"><b>Suspension de cabine : </b>' . $reprise->suspcabine[$reprise->fk_suspcabine] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur"colspan="2"><b>Moteur : </b>' . $reprise->moteur[$reprise->fk_moteur] . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Ralentisseur : </b>' . $reprise->ralentisseur[$reprise->fk_ralentisseur] . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Boite de vitesses : </b>' . $reprise->bv[$reprise->fk_bv] . '</TD>';
						print '<TD class="Valeur" colspan="2"><b>Rapport : </b>' . $reprise->rav . ' + ' . $reprise->rar . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>pont : </b>' . $reprise->show_picto($reprise->sr) . '<b> SR</b>&emsp;' . $reprise->show_picto($reprise->dr) . '<b> DR</b>&emsp;' . $reprise->show_picto($reprise->blocage) . '<b> Blocage</b></TD>';
						print '<TD class="Valeur" colspan="2"><b>Rapport de pont : </b>' . $reprise->rapport . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="2"><b>Freinage : </b>' . $reprise->freinage[$reprise->fk_freinage] . '</TD>';
						print '<TD class="Valeur" colspan="2">' . $reprise->show_picto($reprise->abs1) . '<b> ABS</b>&emsp;' . $reprise->show_picto($reprise->asr) .  '<b> ASR</b>&emsp;' .  $reprise->show_picto($reprise->ebs) . '<b> EBS</b>&emsp;' . $reprise->show_picto($reprise->esp) . '<b> ESP</b>&emsp;' . $reprise->show_picto($reprise->dfr) . '<b> DFR</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" rowspan="2"><b>Suspension Chassis : </b></TD>';
						print '<TD class="Valeur" width="11%"><b>Avant : </b></TD>';
						print '<TD class="Valeur" colspan="2">' . $reprise->suspav . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur"><b>Arrière : </b></TD>';
						print '<TD class="Valeur" colspan="2">' . $reprise->suspar . '</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
			print '<TD width="50%" align="center" valign="top">';
			print "<b>Informations sur l'état et la qualité des pneumatiques</b></br>";
				print '<TABLE width="98%" style="border-collapse:collapse">';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center">Emp.</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="16%" align="center">Marque</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="22%" align="center" colspan="3">Taille</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="50%" align="center">Profil</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center" colspan="2">Usure</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="12%" align="center">AV</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="20%" align="center">' . $reprise->marquepneu[$reprise->fk_mav] . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="12%" align="center">' . $reprise->tav . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="4%" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="8%" align="center">' . $reprise->dav . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="40%" align="center">' . $reprise->pav . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" width="8%" align="center">' . $reprise->uav . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" width="4%" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR1</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->marquepneu[$reprise->fk_mar1] . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->tar1 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->dar1 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->par1 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->uar1 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR2</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->marquepneu[$reprise->fk_mar2] . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->tar2 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->dar2 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->par2 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->uar2 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">AR3</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->marquepneu[$reprise->fk_mar3] . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->tar3 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">R</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->dar3 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur"align="center">' . $reprise->par3 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="valeur" align="center">' . $reprise->uar3 . '</TD>';
						print '<TD style="border-style:solid; border-color:black; border-width:1px" class="fieldrequired" align="center">%</TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Chassis : </b>' . $reprise->show_picto($reprise->nisoude) . '<b> Ni soudé</b>&emsp;&emsp;' . $reprise->show_picto($reprise->nifissure) . '<b> Ni fissuré</b></TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Nb réservoir GO : </b>' . $reprise->nbreserv . '</TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Capacité Gasoil : </b>' . $reprise->capago . ' litres</TD>';
						print '<TD class="Valeur" width="25%" colspan="2"><b>Capacité AdBlue : </b>' . $reprise->adblue . ' litres</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" colspan="4"><b>Etat mécanique : </b>' . $reprise->etatmeca . '</TD>';
						print '<TD class="Valeur" colspan="4"><b>Présentation : </b>' . $reprise->pres . '</TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->lve) . '<b> 2 LVE</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->rct) . '<b> 2 RCT</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->gyro) . '<b> Gyrophare</b></TD>';
						print '<TD class="Valeur" width="25%" colspan="2">' . $reprise->show_picto($reprise->echapv) . '<b> Echappement vertical</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->adr) . '<b> adr</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->hydro) . '<b> Hydrolique</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->compresseur) . '<b> Compresseur</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="25%" Colspan="2">' . $reprise->show_picto($reprise->climtoit) . '<b> Climatisation de toit</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->clim) . '<b> Climatisation</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->webasto) . '<b> Webasto</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->deflecteur) . '<b> Déflecteurs</b></TD>';
						print '<TD class="Valeur" width="12.5%">' . $reprise->show_picto($reprise->jupes) . '<b> Jupes</b></TD>';
						print '<TD class="Valeur" width="25%" Colspan="2"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
		print '<TR>';
			print '<TD colspan="2" valign="top" width="100%" valign="top">';
				print '<TABLE width="100%"  class="nobordernopadding">';
					print '<TR>';
						print '<TD class="Valeur" width="100%" Colspan="6" align=center><b>Documents a récupérer</b></TD>';
					print '</TR>';
					print '<TR>';
						print '<TD class="Valeur" width="15%"></TD>';
						print '<TD class="Valeur" width="15%">' . $reprise->show_picto($reprise->copiecg) . '<b> Copie Carte Grise</b></TD>';
						print '<TD class="Valeur" width="25%">' . $reprise->show_picto($reprise->copiect) . '<b> Copie dernier controle technique</b></TD>';
						print '<TD class="Valeur" width="20%">' . $reprise->show_picto($reprise->copieca) . '<b> Copie certificat agrément</b></TD>';
						print '<TD class="Valeur" width="10%">' . $reprise->show_picto($reprise->photo) . '<b> Photos</b></TD>';
						print '<TD class="Valeur" width="15%"></TD>';
					print '</TR>';
				print '</TABLE>';
			print '</TD>';
		print '</TR>';
	print '</TABLE>';
	print '<div class="tabsAction">';
	print '<script>';
	print 'function pop() {';
	print "window.open('".dol_buildpath('/volvo/reprise/print.php', 2) . "?repid=" . $id  . "','height=600,width=600,resible=no');";
	print "}";
	print '</script>';

	if($user->rights->volvo->modif_ig){
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=edit">' . 'Modifier' . "</a></div>\n";
	}

	if ($user->admin) {
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=suppr">' . 'Supprimer' . "</a></div>\n";
	}

	if ($user->rights->volvo->modif_ig && $reprise->getstatus() <5 && !$user->admin) {
		if($reprise->actif == 1){
			$label = 'affaire perdue';
		}else{
			$label = 'affaire non perdue';
		}
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=desactive">' . $label . "</a></div>\n";
	}
	if ($user->admin) {
		if($reprise->actif == 1 && $reprise->getstatus() <5){
			$label = 'affaire perdue';
		}elseif($reprise->actif == 1 && $reprise->getstatus() >4){
			$label = 'mettre le véhicule hors stock';
		}elseif($reprise->actif == 0 && $reprise->getstatus() <5){
			$label = 'affaire non perdue';
		}else{
			$label = 'affaire non perdue';
		}
		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=desactive">' . $label . "</a></div>\n";
	}
	print '<div class="inline-block divButAction"><a class="butAction" href="#" onClick="pop()">Imprimer</a></div>';
	print '</div>';

}

if ($action=='edit_valeur' && $user->rights->volvo->admin){
	print '<form name="estimrachat" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_valeur">';
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="4">Synthese de la reprise</td></tr>';
	print '<tr>';
	print '<td width="25%">Estimation de la reprise: ' . price($reprise->estim) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Engagement de rachat: <input type="text" name="rachat" size="5" value="' . price2num($reprise->rachat) . '"/></td>';
	print '<td width="25%">Montant de la vente VO: ' . price($reprise->getvente($reprise->id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Surestimation du VO: ' . price($reprise->getsures($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
 	print '</form>';

} elseif ($action=='estim' && $user->rights->volvo->admin){
 	print '<form name="estimreprise" action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="update_estim">';
	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="4">Synthese de la reprise</td></tr>';
	print '<tr>';
	print '<td width="25%">Estimation de la reprise: <input type="text" name="estim" size="5" value="' . price2num($reprise->estim) . '"/></td>';
	print '<td width="25%">Engagement de rachat: ' . price($reprise->rachat) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Montant de la vente VO: ' . price($reprise->getvente($reprise->id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Surestimation du VO: ' . price($reprise->getsures($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
 	print '<input type="submit" class="button" value="' . $langs->trans("Save") . '">';
	print '&nbsp;<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	print '</div>';
 	print '</form>';

} elseif($user->rights->volvo->lireeprise) {
 	print '<table class="border" width="100%">';
	print '<tr class="liste_titre"><td align="center" colspan="4">Synthese des reprises</td></tr>';
	print '<tr>';
	print '<td width="25%">Estimation de la reprise: ' . price($reprise->estim) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Engagement de rachat: ' . price($reprise->rachat) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Montant de la vente VO: ' . price($reprise->getvente($reprise->id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '<td width="25%">Surestimation du VO: ' . price($reprise->getsures($reprise->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
	print '</tr>';
	print '</table>';
 	print '<div class="tabsAction">';
 	if ($user->rights->volvo->admin) {
 		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=estim">' . 'Saisir une estimation' . "</a></div>\n";
 		print '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=edit_valeur">' . 'Saisir une valeur de rachat' . "</a></div>\n";
 	}

 	print '</div>';
 }


$action = GETPOST('action');
$filearray=dol_dir_list($upload_dir,"files",0,'','(\.meta|_preview\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
$modulepart = 'volvo';
$permission = 1;
$param = '&id=' . $id ;

include_once DOL_DOCUMENT_ROOT . '/volvo/template/document_actions_post_headers2.tpl.php';

llxFooter();
$db->close();