<?php
$res = @include '../../main.inc.php'; // For root directory
if (! $res) $res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
dol_include_once('/volvo/class/reprise.class.php');
dol_include_once('/volvo/lib/volvo.lib.php');

$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$form = new Form($db);
$object = new Commande($db);
if(!empty($id)) $object->fetch($id);
$reprise = new Reprise($db);
$extrafields = new ExtraFields($db);

$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);
$res = $object->fetch_optionals($object->id, $extralabels);
if ($action == 'update_extras')	{
	$ret = $extrafields->setOptionalsFromPost($extralabels, $object, GETPOST('attribute'));
	if ($ret < 0) $error++;
	if ($error){
		$action = 'edit_extras';
	}else{
		$result = $object->insertExtraFields();
	}
}



llxHeader('', 'VCM');
$head = commande_prepare_head($object);
dol_fiche_head($head, 'vcm', $langs->trans("CustomerOrder"), 0, 'order');

print load_fiche_titre('Cotation VCM','',dol_buildpath('/volvo/img/iron02.png', 1));
print '<table class="border" width="100%">';
print '<tr class="liste_titre"><td align="center">Entretien et Maintenance du véhicule</td></tr>';
print '<tr><td>';
print '<table class="nobordernopadding" width="100%">';
print '<tr>';
// deja client
print '<td>';
print print_extra('vcm_deja','yesno',$action,$extrafields,$object,1);
print '</td>';
// vh deporté
print '<td>' . print_extra('vcm_deport','yesno',$action,$extrafields,$object,1) . '</td>';
//point de services
$key = 'vcm_site';
$label = $extrafields->attribute_label[$key];
include DOL_DOCUMENT_ROOT . '/volvo/template/extra_inline.php';
print '</tr><tr style="height:5px"><td colspan="4" style="height:5px"></td></tr>';

print '<tr>';
// atelier
print '<td>' . print_extra('vcm_atel','chkbox',$action,$extrafields,$object,1) . '</td>';
// type maintenance
print '<td colspan="2">' . print_extra('vcm_maint','chkbox',$action,$extrafields,$object,1) . '</td>';
print '</tr><tr style="height:5px"><td colspan="4" style="height:5px"></td></tr>';

print '<tr>';
// transfert vers gds
print '<td colspan="2">' . print_extra('vcm_trf_gds', 'bool', $action, $extrafields, $object,1) . '</td>';
// transfert vers DFOL
print '<td colspan="2">' . print_extra('vcm_trf_dfol', 'bool', $action, $extrafields, $object,1) . '</td>';
print '</tr><tr style="height:5px"><td colspan="4" style="height:5px"></td></tr><tr style="height:5px"><td colspan="4" style="height:5px"></td></tr>';
print '</table>';
print'</td></tr>';

print '<tr class="liste_titre"><td align="center">Termes de la Solution de Maintenance</td></tr>';

print '<tr><td>';
print '<table class="border" width="100%">';
print '<tr>';
// date debut souhaitée
print '<td rowspan="2" valign="top" align="center">' . print_extra('vcm_dt_dem', 'date', $action, $extrafields, $object,1) . '</td>';
print '<td bgcolor="56aaff" class="liste_titre" align="center">Prévention</td>';
print '<td bgcolor="ff7f00" class="liste_titre" colspan=2 align="center">Entretien</td>';
print '<td bgcolor="#00ff00" class="liste_titre" colspan=2 align="center">Protection</td>';
print '<td class="liste_titre" rowspan="2"></td>';
// entete tableau offre VCM
print '<tr>';
print '<td bgcolor="56aaff" class="liste_titre" align="center">Suivi et diag</td>';
print '<td bgcolor="ff7f00" class="liste_titre" align="center">Entretien</br>périodique</td>';
print '<td bgcolor="ff7f00" class="liste_titre" align="center">' ."Pieces d'usures" . '</td>';
print '<td bgcolor="#00ff00" class="liste_titre" align="center">Chaîne</br>cinématique</br>+ echappement</td>';
print '<td bgcolor="#00ff00" class="liste_titre" align="center">Chaîne</br>chassîs Cabine</td>';
print '</tr>';
// pack prévention connecté
print '<tr style="height:5px"><td colspan="7" style="height:5px"></td></tr>';
print '<tr bgcolor="aaffd4">';
print '<td class="liste_titre">Prévention Connecté</td>';
print '<td align="center"> Inclus </td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td>' . print_extra('vcm_ppc', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr>';
// pack protection cinématique
print '<tr bgcolor="aaffd4">';
print '<td class="liste_titre">Protection Cinématique</td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td align="center"> Inclus </td>';
print '<td align="center"></td>';
print '<td>' . print_extra('vcm_pc', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr>';
// pack protection cinématique connecté
print '<tr bgcolor="aaffd4">';
print '<td class="liste_titre">Protection Cinématique Connecté</td>';
print '<td align="center"> Inclus </td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td align="center"> Inclus </td>';
print '<td align="center"></td>';
print '<td>' . print_extra('vcm_pcc', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr>';
// pack protection véhicule
print '<tr bgcolor="aaffd4">';
print '<td class="liste_titre">Protection Véhicule Connecté</td>';
print '<td align="center"> Inclus </td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td align="center"> Inclus </td>';
print '<td align="center">Inclus</td>';
print '<td>' . print_extra('vcm_pvc', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr><tr style="height:5px"><td colspan="7" style="height:5px"></td></tr>';
// blue
print '<tr bgcolor="aad4ff">';
print '<td class="liste_titre">Blue Connecté</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td align="center"></td>';
print '<td>' . print_extra('vcm_blue', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr>';
// silver
print '<tr bgcolor="cccccc">';
print '<td class="liste_titre">Silver Connecté</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center"></td>';
print '<td align="center">Inclus</td>';
print '<td align="center"></td>';
print '<td>' . print_extra('vcm_silver', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr>';
//silver+
print '<tr bgcolor="b2b2b2">';
print '<td class="liste_titre">Silver+ Connecté</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center"></td>';
print '<td>' . print_extra('vcm_silverp', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr>';
//gold
print '<tr bgcolor="ffffaa">';
print '<td class="liste_titre">Gold Connecté</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td align="center">Inclus</td>';
print '<td>' . print_extra('vcm_gold', 'bool', $action, $extrafields, $object,0) . '</td>';
print '</tr>';
print '</table>';

print '</br>';
// date de demmarrage
print '<table class="nobordernopadding" width="100%">';
print '<tr>';
Print '<td>' . print_extra('vcm_duree', 'chkbox', $action, $extrafields, $object,1) . '</td>';
print '</tr>';
print '</table>';

print '</br>';

print '<table class="nobordernopadding" width="100%">';
print '<tr>';
// consommation estimée
Print '<td bgcolor="ffaaaa">' . print_extra('vcm_conso', 'num', $action, $extrafields, $object,1,'l/100km') . '</td>';
// Kilométrage annuel
print '<td  align="Left">' . print_extra('vcm_km', 'num', $action, $extrafields, $object,1,'km/an') . '</td>';
// kilometre départ
print '<td  align="Left">' . print_extra('vcm_km_dep', 'num', $action, $extrafields, $object,1,'km') . '</td>';
// ptra
print '<td  align="Left">' . print_extra('vcm_ptra', 'num', $action, $extrafields, $object,1,'Tonnes') . '</td>';
print '</tr>';
print '</table>';

print '</br>';

print '<table class="nobordernopadding" width="100%">';
print '<tr>';
//type de prise de force
Print '<td align="left">'. print_extra('vcm_pto','chkbox',$action,$extrafields,$object,1).'</td>';
//nb heure pto
print '<td  align="Left">'.print_extra('vcm_pto_nbh','num',$action,$extrafields,$object,1,'H/an').'</td>';
// nb heure pto depart
print '<td  align="Left">' . print_extra('vcm_pto_hdep','num',$action,$extrafields,$object,1,'H') . '</td>';
print '</tr>';
print '</table>';

print '</br>';

print '<table class="nobordernopadding" width="100%">';
print '<tr>';
//hydraulique
Print '<td align="left">' . print_extra('vcm_hydro','chkbox',$action,$extrafields,$object,1) . '</td>';
print '</tr>';
print '<tr><td></td></tr>';
print '<tr><td> carrosserie et équipements: </td></tr>';
print '</table>';

print '</br>';
print'</td></tr>';
print '<tr class="liste_titre"><td align="center">Utilisation du Véhicule</td></tr>';

print '<tr><td>';
print '<table class="nobordernopadding" width="100%">';
	print '<tr>';
	Print '<td align="left" Colspan="2">'."Amplitude Hebdomadaire d'utilisation du véhicule : " . $reprise->show_picto(1) . ' 5jours/semaine - ' . $reprise->show_picto(1) . ' 6jours/semaine - '. $reprise->show_picto(1) .' 7jours/semaine</td>';
	print '</tr>';
	print '<tr>';
	Print '<td align="left" Colspan="2">'."Amplitude journalière d'utilisation du véhicule : " . $reprise->show_picto(1) . ' 8 heures/jour maximum - ' . $reprise->show_picto(1) . ' > à 8 heures /jour - '. $reprise->show_picto(1) .' > à 16 Heure /jour</td>';
	print '</tr>';
	print '<tr>';
	Print '<td align="left">'."Nombre de position de livraison ou chargement journalier : 15 Positions / jour" .'</td>';
	Print '<td align="left">'."Véhicule ayant une activité saisonnière ?" . $reprise->show_picto(1) . '</td>';
	print '</tr>';
	print '<tr>';
	Print '<td align="left">'."Superstructure typique pour les chantiers ?" . yn(1) .'</td>';
	Print '<td align="left">'."Le véhicule circule t il principalement en ville ?" . yn(1) . '</td>';
	print '</tr>';
	print '<tr>';
	Print '<td align="left">'."La distance moyenne entre le chargement et le déchargement est-elle inférieure à 50 km ?" . yn(1) .'</td>';
	Print '<td align="left">'."Le kilométrage annuel est il égal ou supérieur  à 100 000 kms ?" . yn(1) . '</td>';
	print '</tr>';
	print '<tr>';
	Print '<td align="left" Colspan="2">'."Cycle de transport: Distribution régional" . '</td>';
	print '</tr>';
	print '<tr>';
	Print '<td align="left">'."Zone géographique : Communauté Européenne" . '</td>';
	Print '<td align="left">'."Type de transport: Transport Traditionnel" . '</td>';
	print '</tr>';
	print '<tr>';
	Print '<td align="left">'."type de Carburant: 0.05% de soufre (carburant type 0)" . '</td>';
	Print '<td align="left">'."Topographie: Lisse" . '</td>';
	print '</tr>';
	print '</table>';
	print '</br>';
	print '</td></tr>';
	print '<tr class="liste_titre"><td align="center">Options</td></tr>';
	print '<tr><td></td></tr>';

	print '</table>';








dol_fiche_end();

?>