<?php
/* Volvo
 * Copyright (C) 2015	Florian HENRY 		<florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 */
?>
<?php
dol_include_once('/volvo/class/html.formvolvo.class.php');
$form = new FormVolvo($db);
$array_status = array(
		0=>'A expertiser',
		1=>'A estimer',
		2=>"Offre d'achat a faire",
		3=>'Affaire perdue',
		4=>'Véhcule a rentrer',
		5=>'En stock',
		6=>'Mis hors Stock',
		7=>'Vendu',
		8=>'!!!Erreur!!!'
);
$ssSearch = GETPOST('ssSearch');
$ssStatus = array();
$ssStatus = GETPOST('ssStatus');
if(empty($ssStatus)) {
	$ssStatus=array(0,1,2,4,5,6);
}
$ssStatus_post = serialize($ssStatus);
?>
<!-- BEGIN PHP TEMPLATE -->
<script type="text/javascript">
$(document).ready(function() {
	$("#listreprise").DataTable( {
		<?php
		if ($optioncss=='print') {
		 	print '\'dom\': \'lfrtip\',';
		} else {
			print '\'dom\': \'Blfrtip\',';
		}
		?>
		"colReorder": true,
		'buttons': [
		          'colvis','copy', 'csv', 'excel', 'print'
		      ],
		"sPaginationType": "full_numbers",
		"bFilter": false,
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo $langs->trans('All'); ?>"]],
		"oLanguage": {
			"sLengthMenu": "<?php echo $langs->trans('Show'); ?> _MENU_ <?php echo $langs->trans('Entries'); ?>",
			"sSearch": "<?php echo $langs->trans('Search'); ?>:",
			"sZeroRecords": "<?php echo $langs->trans('NoRecordsToDisplay'); ?>",
			"sInfoEmpty": "<?php echo $langs->trans('NoEntriesToShow'); ?>",
			"sInfoFiltered": "(<?php echo $langs->trans('FilteredFrom'); ?> _MAX_ <?php echo $langs->trans('TotalEntries'); ?>)",
			"sInfo": "<?php echo $langs->trans('Showing'); ?> _START_ <?php echo $langs->trans('To'); ?> _END_ <?php echo $langs->trans('Of'); ?> _TOTAL_ <?php echo $langs->trans('Entries'); ?>",
			"oPaginate": {
				"sFirst": "<?php echo $langs->transnoentities('First'); ?>",
				"sLast": "<?php echo $langs->transnoentities('Last'); ?>",
				"sPrevious": "<?php echo $langs->transnoentities('Previous'); ?>",
				"sNext": "<?php echo $langs->transnoentities('Next'); ?>"
			}
		},
		"aaSorting": [[0,'desc']],
		"bProcessing": true,
		"stateSave": true,
		"bServerSide": true,
		"sAjaxSource": "<?php echo dol_buildpath('/volvo/reprise/ajax/list.php',1).'?ssSearch=' . urlencode($ssSearch) . '&ssStatus=' . urlencode($ssStatus_post); ?>",
		});
	});
</script>
<H3>Pour mémoire syntaxe a utiliser dans le champs de recherche : Parametre:valeur recherchée pour les parametres simple ou Parametre:Valeur min => Valeur max pour les paramètres numérique</H3>
<table width="100%"  class="nobordernopadding">
	<tr>
	<td width="50%" valign="middle">
	<form name="filtretable" id="filtretable" action="<?php echo $_SERVER["PHP_SELF"];?>" method="POST">
		<table>
			<tr>
				<td width="30%">
					<?php echo $form->select_withcheckbox("ssStatus",$array_status,$ssStatus); ?>
				</td>
				<td width="10%">
					<textarea rows="9" cols="80" name="ssSearch" id="ssSearch"><?php echo $ssSearch; ?></textarea>
				</td>
				<td>
					<input type="submit" class="button" value="Filtrer">
				</td>
			</tr>
		</table>
	</form>
	</td>
	<td>
		<?php if ($user->rights->volvo->admin) print '<div class="inline-block divButAction"><a class="butAction" href="' . dol_buildpath('\volvo\reprise\card2.php',2) . '?id=' . $object->id . '&action=create&fk_lead=1">Ajouter un V.O. hors affaire</a></div>'; ?>
	</td>
	</tr>
</table>
<br>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="listreprise">
	<thead>
		<tr>
			<?php echo getTitleFieldOfList('Voir',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Ref').'<br>(REF)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Statut').'<br>(STA)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Ex. Propr.').'<br>(PRO)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Genre').'<br>(GEN)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Marque').'<br>(MRQ)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Type').'<br>(TYP)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Silouhette').'<br>(SIL)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Puis. Com.').'<br>(PCO)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Cabine').'<br>(CAB)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Boite Vitesse').'<br>(BV)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Motorisation').'<br>(MOT)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Capa GO').'<br>(CGO)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('PTC').'<br>(PTC)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Norme').'<br>(EUR)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Kilometrage').'<br>(KMS)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Options').'<br>(OPT)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Immat.').'<br>(IMM)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Vin').'<br>(VIN)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Date de 1ere MEC').'<br>(MEC)',1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans("Prix d'achat"),1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Estimation valeur').'<br>(EST)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Montant offre de rachat').'<br>(OAC)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans("Date entrée en stock").'<br>(DST)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Site').'<br>(SIT)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Nb J Stock').'<br>(NJS)',1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans('Dépréciation.'),1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans('Cession.'),1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans('Frais Ext.'),1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans('Fac. Div.'),1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans('Fac. a venir'),1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans('Prix reviens.'),1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Prix vente'),1); ?>
			<?php if($user->rights->volvo->admin) echo getTitleFieldOfList($langs->trans('Marge Com.'),1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Date vente').'<br>(DTV)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Acheteur').'<br>(ACH)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Date fac. Achat').'<br>(DTA)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Pays Vente').'<br>(PAV)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Type Acheteur').'<br>(TAC)',1); ?>
			<?php echo getTitleFieldOfList($langs->trans('Financement').'<br>(FIN)',1); ?>

		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5" class="dataTables_empty"><?php echo $langs->trans('LoadingDataFromServer'); ?></td>
		</tr>
	</tbody>
</table>
<script>
jQuery(document).ready(function() {

<?php

if(!empty($user->array_options['options_list_vo'])){
	$toshow = array();
	$toshow = explode(',', $user->array_options['options_list_vo']);
	foreach($toshow as $compteur){
		print 'jQuery("#listreprise").dataTable().fnSetColumnVis('. $compteur.', false );'."\n";
	}
	print "});"."\n";
}
?>
});
</script>
<!-- END PHP TEMPLATE -->