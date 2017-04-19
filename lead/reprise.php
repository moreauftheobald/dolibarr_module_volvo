<?php
$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

dol_include_once('/volvo/class/reprise.class.php');
dol_include_once('/volvo/lib/reprise.lib.php');
dol_include_once('/volvo/class/lead.extend.class.php');
dol_include_once('/volvo/lib/leadexpress.lib.php');
dol_include_once('/societe/class/societe.class.php');

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$form = new Form($db);

$object = new Leadext($db);
$object->fetch($id);
$object->fetch_thirdparty();
$reprise = new Reprise($db);
$num = $reprise->fetchAll('','',0,0,array('t.fk_lead'=>$id));
$lines = $reprise->lines;


top_htmlhead('', '');
$head = leadexpress_prepare_head($object);
dol_fiche_head($head, 'reprise', 'Affaire', 0, dol_buildpath('/lead/img/object_lead.png', 1), 1);
$form = new Form($db);

print '<table class="border" width="100%">';
print '<tr class="liste_titre"><td align="center" colspan="4">Synthese des reprises</td></tr>';
print '<tr>';
print '<td width="25%">Montant total des estimations des reprises: ' . price($reprise->gettotalestim($object->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
print '<td width="25%">Montant total des engagement de rachat: ' . price($reprise->gettotalrachat($object->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
print '<td width="25%">Montant total des ventes de VO: ' . price($reprise->gettotalvente($object->id,17)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
print '<td width="25%">Montant total des surestimation VO: ' . price($reprise->gettotalsures($object->id)) . $langs->getCurrencySymbol($conf->currency) . '</td>';
print '</tr>';
print '</table>';
print '</br>';
print '</br>';
if ($user->rights->lead->write) print '<div class="inline-block divButAction"><a class="butAction" href="' . dol_buildpath('\volvo\reprise\card2.php',2) . '?id=&action=create&fk_lead=' . $id . '">Ajouter une reprise</a></div>';
print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<td>Visualiser</td>';
print '<td>Ex Propriétaire</td>';
print '<td>Marque</td>';
print '<td>Type</td>';
print '<td>Silouhette</td>';
print '<td>Status</td>';
print '<td>Estimation</td>';
print '<td>Offre Rachat</td>';
print '<td>Prix de Vente</td>';
print '<td>Surestimation</td>';
print '</tr>';
foreach ($reprise->lines as $line){
	$soc = new Societe($db);
	$soc->fetch($line->fk_soc);
	$pv = '';
	$rachat = '';
	$estim = '';
	if($line->rachat != 0){
		$rachat = $line->rachat . ' ' . $langs->getCurrencySymbol($conf->currency);
	}
	if($line->estim != 0){
		$estim = $line->estim . ' ' . $langs->getCurrencySymbol($conf->currency);
	}

	if($line->status == 7){
		$pv = $line->getvente($line->id,17) . $langs->getCurrencySymbol($conf->currency);
	}
	if($line->status == 7 && !empty($rachat)){
		$sures = $line->getsures()  . ' ' . $langs->getCurrencySymbol($conf->currency) . ' - Réél !';
	}elseif ($line->status != 3 && $line->status != 8 && !empty($rachat) && !empty($estim)){
		$sures = $line->getsures()  . ' ' . $langs->getCurrencySymbol($conf->currency);
	}else{
		$sures = '';
	}
	print'<tr>';
	print '<td>' . $line->getNomUrl2(1) . '</td>';
	print '<td>' . $soc->getNomUrl(1) . '</td>';
	print '<td>' . $line->marque_label . '</td>';
	print '<td>' . $line->type . '</td>';
	print '<td>' . $line->silouhette_label . '</td>';
	print '<td>' . $line->getLibStatut(2) . '</td>';
	print '<td>' . $estim . '</td>';
	print '<td>' . $rachat  . '</td>';
	print '<td>' . $pv . '</td>';
	print '<td>' . $sures . '</td>';
	print '</tr>';
}
print '</table>';
llxFooter();
$db->close();