<?php
global $list_config,$conf,$db;

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';

$form = new Form($db);
$formother = new FormOther($db);

$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;

//header
llxHeader('', $list_config['title']);

//affichage du tritre de la liste
print_barre_liste($list_config['title'], $page, $_SERVER['PHP_SELF'], $list_config['option'], $list_config['sortfield'], $list_config['sortorder'], '', $list_config['num'], $list_config['nbtotalofrecords']);

//affichage de la barre d'outils et de recherche
if($list_config['tools_active']==1){
	print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre" style="height:22px;">';
	print '<th class="liste_titre" align="center" width="80px">';
	if($list_config['tools']['search_button']==1){
		print '<input class="liste_titre" type="image" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/search.png" value="Search" title="Search">';
	}
	if($list_config['tools']['remove_filter_button']==1){
		print '&nbsp;<input type="image" class="liste_titre" name="button_removefilter" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/searchclear.png" value="RemoveFilter" title="RemoveFilter">';
	}
	if($list_config['tools']['export_button']==1){
		print '&nbsp;<input type="image" class="liste_titre" name="button_export" src="' . DOL_URL_ROOT . '/theme/common/mime/xls.png" value="export" title="Exporter" width="16px" height="16px">';
	}
	print '</th>';
	if(is_array($list_config['tools']['extra _tools'])){
		foreach ($list_config['tools']['extra _tools'] as $key => $p){
			print '<th class="liste_titre" align="center">';
			switch($p['type']){
				case 'select_user':
					print $p['title'];
					print $form->select_dolusers($p['value'],$p['html_name'],$p['use_empty'],$p['excluded'],$p['disabled'],$p['included']);
					break;
				case 'select_year':
					print $p['title'];
					$formother->select_year($p['value'],$p['html_name'],$p['use_empty'],$p['min_year'],$p['max_year']);
					break;
				case 'select_array':
					print $p['title'];
					print $form->selectarray($p['html_name'], $p['array'],$p['value'],$p['use_empty']);
			}
			print '</th>';
		}
	}

	print "</tr>";
	print '</table>';
	print '</form>';
}




// footer
llxFooter();
$db->close();
?>