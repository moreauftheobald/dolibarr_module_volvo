<?php
global $list_config,$conf,$db;

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

$form = new Form($db);
$formother = new FormOther($db);

$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;
$varpage=$list_config['context'];
if (GETPOST('formfilteraction') == 'listafterchangingselectedfields')
{
	$tabparam=array();

	if (GETPOST("selectedfields")) $tabparam["MAIN_SELECTEDFIELDS_".$varpage]=GETPOST("selectedfields");
	else $tabparam["MAIN_SELECTEDFIELDS_".$varpage]='';

	include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

	$result=dol_set_user_param($db, $conf, $user, $tabparam);
}

$selectfields = $form->multiSelectArrayWithCheckbox('selectedfields', $list_config['array_fields'], $varpage);

if(GETPOST("button_export_x")){
	$handler = fopen("php://output", "w");
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment;filename=' . $list_config['export_name'] . '.csv');
	fputs($handler, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

	$commercial = new user($db);
	if(!empty($search_commercial)){
		$commercial->fetch($search_commercial);
		$com = $commercial->firstname . ' ' . $commercial->lastname;
	}

	if(!empty($search_periode)){
		if($search_periode == 1){
			$periode = '1er Trimestre';
		}elseif($search_periode==2){
			$periode = '2eme Trimestre';
		}elseif($search_periode==3){
			$periode = '3eme Trimestre';
		}elseif($search_periode==4){
			$periode = '4eme Trimestre';
		}elseif($search_periode==5){
			$periode = '1er Semestre';
		}elseif($search_periode==6){
			$periode = '2eme Semestre';
		}
	}

	$h=array(
			'Année:',
			$year,
			'',
			'commercial:',
			$com,
			'',
			'Periode:',
			$periode
	);
	fputcsv($handler, $h, ';', '"');

	$h=array();

	foreach ($list_config['array_fields'] as $f){
		if($f['checked']==1) $h[] = $f['label'];
	}
	fputcsv($handler, $h, ';', '"');

	foreach ($list_config['array_data'] as $d) {
		$ligne=array();
		foreach ($d as $key=>$val){
			if($list_config['array_fields'][$key]['checked']==1) $ligne[] = strip_tags($val);
		}
		fputcsv($handler, $ligne, ';', '"');
	}

	exit;
}


//header
llxHeader('', $list_config['title']);

//affichage du tritre de la liste
print_barre_liste($list_config['title'], $list_config['page'], $_SERVER['PHP_SELF'], $list_config['option'], $list_config['sortfield'], $list_config['sortorder'], '', $list_config['num'], $list_config['nbtotalofrecords']);

//affichage de la barre d'outils et de recherche
if($list_config['tools_active']==1){
	print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$list_config['sortfield'].'">';
	print '<input type="hidden" name="sortorder" value="'. $list_config['sortorder'].'">';
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre" style="height:22px;">';
	print '<th class="liste_titre" align="center" style="white-space:nowrap; width:90px;">';
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
	if($list_config['tools']['select_fields_button']==1){
		print '<th class="liste_titre" align="center" style="white-space:nowrap; width:40px;">';
		print $selectfields;
		print '</th>';
	}

	if(is_array($list_config['tools']['extra _tools'])){
		foreach ($list_config['tools']['extra _tools'] as $key => $p){
			print '<th class="liste_titre" align="left" style="white-space:nowrap; width:1%;">';
			switch($p['type']){
				case 'select_user':
					if($p['value'] == -1) $p['value']="";

					if($p['see_all']!=1){
						$p['value'] = $user->id;
						$disabled = 1;
					}

					if(!empty($p['limit_to_group'])){
						$user_included=array();
						$sqlusers = "SELECT fk_user FROM " . MAIN_DB_PREFIX . "usergroup_user WHERE fk_usergroup IN(" . $p['limit_to_group'] . ") ";
						$resqlusers  = $db->query($sqlusers);
						if($resqlusers){
							while ($users = $db->fetch_object($resqlusers)){
								$user_included[] = $users->fk_user;
							}
						}
					}

					if(!empty($p['exclude_group'])){
						$user_excluded=array();
						$sqlusers = "SELECT fk_user FROM " . MAIN_DB_PREFIX . "usergroup_user WHERE fk_usergroup IN(" . $p['limit_to_group'] . ") ";
						$resqlusers  = $db->query($sqlusers);
						if($resqlusers){
							while ($users = $db->fetch_object($resqlusers)){
								$user_excluded[] = $users->fk_user;
							}
						}
					}

					print '&nbsp; &nbsp;' . $p['title'];
					print $form->select_dolusers($p['value'],$p['html_name'],$p['use_empty'],$user_excluded,$disabled,$user_included);
					break;
				case 'select_year':
					if($p['value'] == -1) $p['value']="";

					if(empty($p['value']) && !empty($p['default'])){
						$p['value'] = $p['default'];
					}
					print '&nbsp; &nbsp;' . $p['title'];
					$formother->select_year($p['value'],$p['html_name'],$p['use_empty'],$p['min_year'],$p['max_year']);
					break;
				case 'select_array':
					if($p['value'] == -1) $p['value']="";
					print '&nbsp; &nbsp;' . $p['title'];
					print $form->selectarray($p['html_name'], $p['array'],$p['value'],$p['use_empty']);
			}
			print '</th>';
		}
	}
	print '<th class="liste_titre"></th>';
	print "</tr>";
	print '</table>';
	print '</form>';
}

$group= array();
foreach ($list_config['array_fields'] as $f){
	if($f['checked']==1)$group[$f['sub_title']]+=1;
}

print '<table class="noborder" width="100%">';
if(is_array($list_config['sub_title'])){
	print '<tr class="liste_titre" style="height:22px;">';
	foreach ($list_config['array_fields'] as $f){
		if($f['sub_title']>0 && $f['checked'] == 1 && $groupdone[$f['sub_title']]==0){
			print '<th class="liste_titre" colspan="' . $group[$f['sub_title']] . '" align="center">' . $list_config['sub_title'][$f['sub_title']] . '</th>';
			$groupdone[$f['sub_title']] = 1;
		}elseif($f['sub_title']==0 && $f['checked'] == 1){
			print_liste_field_titre($f['label'],$_SERVER["PHP_SELF"],$f['field'],'',$list_config['option'],'rowspan="2" align="' . $f['align'] .'"',$list_config['sortfield'],$list_config['sortorder']);
		}
	}
	print "</tr>";
	print '<tr class="liste_titre" style="height:22px;">';
	foreach ($list_config['array_fields'] as $f){
		if($f['sub_title']>0 && $f['checked'] == 1){
			print_liste_field_titre($f['label'],$_SERVER["PHP_SELF"],$f['field'],'',$list_config['option'],'align="' . $f['align'] .'"',$list_config['sortfield'],$list_config['sortorder']);
		}
	}
	print "</tr>";
}else{
	print '<tr class="liste_titre" style="height:22px;">';
	foreach ($list_config['array_fields'] as $f){
		if($f['checked'] == 1){
			print_liste_field_titre($f['label'],$_SERVER["PHP_SELF"],$f['field'],'',$list_config['option'],'align="' . $f['align'] .'"',$list_config['sortfield'],$list_config['sortorder']);
		}
	}
	print "</tr>";
}


//var_dump($group);

foreach ($list_config['array_data'] as $l){
	print '<tr ' . $l['class'] . '>';
	$td_class = $l['class_td'];
	foreach ($l as $key => $val){
		if($list_config['array_fields'][$key]['checked']==1){
			print '<td ' . $td_class . ' align="' . $list_config['array_fields'][$key]['align'] . '" style="white-space:nowrap;">';
			if(!empty($val)) print $val .' ' . $list_config['array_fields'][$key]['unit'];
			print '</td>';
		}
	}
	print '</tr>';
}

print '</table>';



// footer
llxFooter();
$db->close();
?>