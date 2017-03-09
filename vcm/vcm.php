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
	echo 'ret:' . $ret;
	if ($ret < 0) $error++;
	if ($error) $action = 'edit_extras';
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
print '<td height="10"><table width="100%" class="nobordernopadding"><tr><td align ="left">';
print $extrafields->attribute_label['vcm_deja'] . ': ';
if ($action == 'edit_extra' && GETPOST('attribute') =='vcm_deja') {
	print '<form enctype="multipart/form-data" action="' . $_SERVER["PHP_SELF"] . '" method="post" name="formextra">';
	print '<input type="hidden" name="action" value="update_extras">';
	print '<input type="hidden" name="attribute" value="vcm_deja">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="id" value="' . $object->id . '">';;
	print $form->selectyesno('options_vcm_deja',$object->array_options['options_vcm_deja'],1);
	print '<input type="submit" class="button" value="' . $langs->trans('Modify') . '">';
	print '</form>';
} else {
	print yn($object->array_options['options_vcm_deja']);
	print '</td>';
	print '<td align="center"><a href="' . $_SERVER["PHP_SELF"] . '?action=edit_extra&attribute=vcm_deja&id=' . $object->id . '">' . img_edit('', 1) . '</a></td>';
}
print '</td>';
print'</tr></table>';
print '</td>';

print '<td>' . $reprise->show_picto(1) . ' Véhicule Déporté</td>';
print '<td colspan="2">Point de service Volvo Trucks: </td>';
print '</tr>';
print '<tr style="height:5px"><td colspan="4" style="height:5px"></td></tr>';
print '<tr>';
print '<td colspan="2">Client ayant: ' . $reprise->show_picto(1) . ' Atelier mécanique - '. $reprise->show_picto(1) .' Atelier Carrosserie - ' .$reprise->show_picto(1).' Sans atelier</td>';
print '<td colspan="2">Interventions réalisées: ' . $reprise->show_picto(1) . ' Entretien et maintenance légère - '. $reprise->show_picto(1) .' Maintenace Lourde</td>';
print '</tr>';
print '<tr style="height:5px"><td colspan="4" style="height:5px"></td></tr>';
print '<tr>';
print '<td colspan="2">Transfert du calendrier vers GDS: ' . $reprise->show_picto(1) . '</td>';
print '<td colspan="2">Transfert du calendrier vers Dynafleet: ' . $reprise->show_picto(1) . '</td>';
print '</tr>';
print '<tr style="height:5px"><td colspan="4" style="height:5px"></td></tr>';
print '<tr style="height:5px"><td colspan="4" style="height:5px"></td></tr>';
print '</table>';
print'</td></tr>';
print '<tr class="liste_titre"><td align="center">Termes de la Solution de Maintenance</td></tr>';
print '<tr><td>';
print '<table class="border" width="100%">';
print '<tr>';
print '<td rowspan="2" valign="top" align="center">Date éventuelle de démarrage du contrat: </br> 17/07/2016</td>';
print '<td bgcolor="56aaff" class="liste_titre" align="center">Prévention</td>';
print '<td bgcolor="ff7f00" class="liste_titre" colspan=2 align="center">Entretien</td>';
print '<td bgcolor="#00ff00" class="liste_titre" colspan=2 align="center">Protection</td>';
print '<td class="liste_titre" rowspan="2"></td>';
print '<tr>';
	print '<td bgcolor="56aaff" class="liste_titre" align="center">Suivi et diag</td>';
	print '<td bgcolor="ff7f00" class="liste_titre" align="center">Entretien</br>périodique</td>';
	print '<td bgcolor="ff7f00" class="liste_titre" align="center">' ."Pieces d'usures" . '</td>';
	print '<td bgcolor="#00ff00" class="liste_titre" align="center">Chaîne</br>cinématique</br>+ echappement</td>';
	print '<td bgcolor="#00ff00" class="liste_titre" align="center">Chaîne</br>chassîs Cabine</td>';
	print '</tr>';
	print '<tr style="height:5px"><td colspan="7" style="height:5px"></td></tr>';
	print '<tr bgcolor="aaffd4">';
	print '<td class="liste_titre">Prévention Connecté</td>';
	print '<td align="center"> Inclus </td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '<tr bgcolor="aaffd4">';
	print '<td class="liste_titre">Protection Cinématique</td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td align="center"> Inclus </td>';
	print '<td align="center"></td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '<tr bgcolor="aaffd4">';
	print '<td class="liste_titre">Protection Cinématique Connecté</td>';
	print '<td align="center"> Inclus </td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td align="center"> Inclus </td>';
	print '<td align="center"></td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '</tr>';
	print '<tr bgcolor="aaffd4">';
	print '<td class="liste_titre">Protection Véhicule Connecté</td>';
	print '<td align="center"> Inclus </td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td align="center"> Inclus </td>';
	print '<td align="center">Inclus</td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '<tr style="height:5px"><td colspan="7" style="height:5px"></td></tr>';
	print '<tr bgcolor="aad4ff">';
	print '<td class="liste_titre">Blue Connecté</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td align="center"></td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '<tr bgcolor="cccccc">';
	print '<td class="liste_titre">Silver Connecté</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center"></td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center"></td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '<tr bgcolor="b2b2b2">';
	print '<td class="liste_titre">Silver+ Connecté</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center"></td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '<tr bgcolor="ffffaa">';
	print '<td class="liste_titre">Gold Connecté</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td align="center">Inclus</td>';
	print '<td>' . $reprise->show_picto(1) . ' oui</td>';
	print '</tr>';
	print '</table>';
	print '</br>';
	print '<table class="nobordernopadding" width="100%">';
	print '<tr>';
	Print '<td>Durée du contrat : </td>';
	print '<td  align="center">' . $reprise->show_picto(1) . ' 24 Mois</td>';
	print '<td  align="center">' . $reprise->show_picto(1) . ' 36 Mois</td>';
	print '<td  align="center">' . $reprise->show_picto(1) . ' 48 Mois</td>';
	print '<td  align="center">' . $reprise->show_picto(1) . ' 60 Mois</td>';
	print '<td  align="center">' . $reprise->show_picto(1) . ' 72 Mois</td>';
	print '<td  align="center">' . $reprise->show_picto(1) . ' 84 Mois</td>';
	print '<td  align="center">' . $reprise->show_picto(1) . ' 96 Mois</td>';
	print '</tr>';
	print '</table>';
	print '</br>';
	print '<table class="nobordernopadding" width="100%">';
	print '<tr>';
	Print '<td bgcolor="ffaaaa">Consommation estimée du véhicule : 60 l/100km</td>';
	print '<td  align="Left">Kilometrage annuel: 50 000 km</td>';
	print '<td  align="Left">Kilometrage de départ: 0 km</td>';
	print '<td  align="Left">Poids total roulant constaté: 80 Tonnes</td>';
	print '</tr>';
	print '</table>';
	print '</br>';
	print '<table class="nobordernopadding" width="100%">';
	print '<tr>';
	Print '<td align="left">Type de prise de force: ' . $reprise->show_picto(1) . ' Moteur - ' . $reprise->show_picto(1) . ' Boite</td>';
	print '<td  align="Left">'."Nombre d'heure annuel de prise de force 250 h/an".'</td>';
	print '<td  align="Left">Heures PTO de départ: 0 h </td>';
	print '</tr>';
	print '</table>';
	print '</br>';
	print '<table class="nobordernopadding" width="100%">';
	print '<tr>';
	Print '<td align="left">Equipement Hydraulique: ' . $reprise->show_picto(1) . ' Hydraulique VOAC monté sur chaine de montage - ' . $reprise->show_picto(1) . ' Autre type monté chez le réparateur agréé Volvo trucks</td>';
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