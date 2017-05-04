<?php
global $list_config,$conf,$db;

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

class Dyntable
{
	public $arrayfields = array();
	public $title;
	public $extra_tools=array();
	public $sortorder;
	public $sortfield;
	public $page;
	public $offset;
	public $filter = array();
	public $nbtotalofrecords;
	public $array_display = array();
	public $search_button;
	public $remove_filter_button;
	public $export_button;
	public $select_fields_button;
	public $num;
	public $option;
	public $tools_active;
	public $export_name;
	public $context;
	public $sub_title = array();

	function __construct($db)
	{
		$this->db = $db;

	}

	function multiSelectArrayWithCheckbox()
	{
		global $conf,$user;

		if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) return '';

		$tmpvar="MAIN_SELECTEDFIELDS_".$this->context;
		if (! empty($user->conf->$tmpvar))
		{
			$tmparray=explode(',', $user->conf->$tmpvar);
			foreach($this->arrayfields as $key => $val)
			{
				if (in_array($key, $tmparray)){
					$val->checked=1;
					$array[$key] = $val;
				}
				else {
					$val->checked=0;
					$array[$key]=$val;
				}
			}
		}

		$lis='';
		$listcheckedstring='';

		foreach($this->arrayfields as $key => $val)
		{
			if (isset($val->enabled) && ! $val->enabled)
			{
				unset($this->arrayfields[$key]);     // We don't want this field
				continue;
			}
			if ($val->label)
			{
				$lis.='<li><input type="checkbox" value="'.$key.'"'.(empty($val->checked)?'':' checked="checked"').'/>'.dol_escape_htmltag($val->label).'</li>';
				$listcheckedstring.=(empty($val->checked)?'':$key.',');
			}
		}

		$out ='<!-- Component multiSelectArrayWithCheckbox selectedfields -->

            <dl class="dropdown">
            <dt>
            <a href="#">
              '.img_picto('','list').'
            </a>
            <input type="hidden" class="selectedfields" name="selectedfields" value="'.$listcheckedstring.'">
            </dt>
            <dd>
                <div class="multiselectcheckboxselectedfields">
                    <ul class="ulselectedfields">
                    '.$lis.'
                    </ul>
                </div>
            </dd>
        </dl>

        <script type="text/javascript">
          $(".dropdown dt a").on(\'click\', function () {
              $(".dropdown dd ul").slideToggle(\'fast\');
          });

          $(".dropdown dd ul li a").on(\'click\', function () {
              $(".dropdown dd ul").hide();
          });

          function getSelectedValue(id) {
               return $("#" + id).find("dt a span.value").html();
          }

          $(document).bind(\'click\', function (e) {
              var $clicked = $(e.target);
              if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
          });

          $(\'.multiselectcheckboxselectedfields input[type="checkbox"]\').on(\'click\', function () {
              console.log("A new field was added/removed")
              $("input:hidden[name=formfilteraction]").val(\'listafterchangingselectedfields\')
              var title = $(this).val() + ",";
              if ($(this).is(\':checked\')) {
                  $(\'.selectedfields\').val(title + $(\'.selectedfields\').val());
              }
              else {
                  $(\'.selectedfields\').val( $(\'.selectedfields\').val().replace(title, \'\') )
              }
              // Now, we submit page
              $(this).parents(\'form:first\').submit();
        });

        </script>

        ';
		return $out;
	}



	function header(){
		llxHeader('', $this->title);
		print_barre_liste($this->title, $this->page, $_SERVER['PHP_SELF'], $this->option, $this->sortfield, $this->sortorder, '', $this->num, $this->nbtotalofrecords);
	}

	function draw_tool_bar(){
		global $conf, $user;
		$form = new Form($this->db);

		print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
		print '<input type="hidden" name="sortfield" value="'.$this->sortfield.'">';
		print '<input type="hidden" name="sortorder" value="'. $this->sortorder.'">';
		print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre" style="height:22px;">';
		print '<th class="liste_titre" align="center" style="white-space:nowrap; width:90px;">';
		if($this->search_button==1){
			print '<input class="liste_titre" type="image" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/search.png" value="Search" title="Search">';
		}
		if($this->remove_filter_button==1){
			print '&nbsp;<input type="image" class="liste_titre" name="button_removefilter" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/searchclear.png" value="RemoveFilter" title="RemoveFilter">';
		}
		if($this->export_button==1){
			print '&nbsp;<input type="image" class="liste_titre" name="button_export" src="' . DOL_URL_ROOT . '/theme/common/mime/xls.png" value="export" title="Exporter" width="16px" height="16px">';
		}
		print '</th>';
		if($this->select_fields_button==1){
			if (GETPOST('formfilteraction') == 'listafterchangingselectedfields')
			{
				$tabparam=array();

				if (GETPOST("selectedfields")) $tabparam["MAIN_SELECTEDFIELDS_".$varpage]=GETPOST("selectedfields");
				else $tabparam["MAIN_SELECTEDFIELDS_".$varpage]='';

				include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

				$result=dol_set_user_param($this->db, $conf, $user, $tabparam);
			}

			$selectfields = $this->multiSelectArrayWithCheckbox();

			print '<th class="liste_titre" align="center" style="white-space:nowrap; width:40px;">';
			print $selectfields;
			print '</th>';
		}

		if(is_array($this->extra_tools)){
			foreach ($this->extra_tools as $key => $p){
				print '<th class="liste_titre" align="left" style="white-space:nowrap; width:1%;">';
					$p->draw_tool;
				print '</th>';
			}
		}
		print '<th class="liste_titre"></th>';
		print "</tr>";
		print '</table>';
		print '</form>';

	}

	function draw_table_head(){

		$group= array();
		foreach ($this->arrayfields as $f){
			if($f->checked==1) $group[$f->sub_title]+=1;
		}

		print '<table class="noborder" width="100%">';
		if(is_array($this->sub_title)){
			print '<tr class="liste_titre" style="height:22px;">';
			foreach ($this->arrayfields as $f){
				if($f->sub_title>0 && $f->checked == 1 && $groupdone[$f->sub_title]==0){
					print '<th class="liste_titre" colspan="' . $group[$f->sub_title] . '" align="center">' . $this->sub_title[$f->sub_title] . '</th>';
					$groupdone[$f->sub_title] = 1;
				}elseif($f->sub_title==0 && $f->checked == 1){
					print_liste_field_titre($f->label,$_SERVER["PHP_SELF"],$f->field,'',$this->option,'rowspan="2" align="' . $f->align .'"',$this->sortfield,$this->sortorder);
				}
			}
			print "</tr>";
			print '<tr class="liste_titre" style="height:22px;">';
			foreach ($this->arrayfields as $f){
				if($f->sub_title>0 && $f->checked == 1){
					print_liste_field_titre($f->label,$_SERVER["PHP_SELF"],$f->field,'',$this->option,'align="' . $f->align .'"',$this->sortfield,$this->sortorder);
				}
			}
			print "</tr>";
		}else{
			print '<tr class="liste_titre" style="height:22px;">';
			foreach ($this->arrayfields as $f){
				if($f->checked == 1){
					print_liste_field_titre($f->label,$_SERVER["PHP_SELF"],$f->field,'',$this->option,'align="' . $f->align .'"',$this->sortfield,$this->sortorder);
				}
			}
			print "</tr>";
		}

	}

	function draw_data(){

		foreach ($this->array_data as $l){
			print '<tr ' . $l['class'] . '>';
			$td_class = $l['class_td'];
			foreach ($l as $key => $val){
				if($this->array_fields[$key]->checked==1){
					print '<td ' . $td_class . ' align="' . $this->array_fields[$key]->align . '" style="white-space:nowrap;">';
					if(!empty($val)) print $val .' ' . $this->array_fields[$key]->unit;
					print '</td>';
				}
			}
			print '</tr>';
		}

	}

	function end_table(){
		print '</table>';

		// footer
		llxFooter();
		$this->db->close();
	}
}



class Dyntable_tools
{
	public $type;
	public $title;
	public $value;
	public $html_name;
	public $use_empty;
	public $min_year;
	public $max_year;
	public $default;
	public $filter;
	public $see_all;
	public $limit_to_group;
	public $exclude_group;
	public $array;

	function __construct($db)
	{
		$this->db = $db;

	}

	function draw_tool(){
	global $user;
		switch($this->type){
			case 'select_user':
				if($this->value == -1) $this->value="";

				if($this->see_all!=1){
					$this->value = $user->id;
					$disabled = 1;
				}

				if(!empty($this->limit_to_group)){
					$user_included=array();
					$sqlusers = "SELECT fk_user FROM " . MAIN_DB_PREFIX . "usergroup_user WHERE fk_usergroup IN(" . $this->limit_to_group . ") ";
					$resqlusers  = $this->db->query($sqlusers);
					if($resqlusers){
						while ($users = $this->db->fetch_object($resqlusers)){
							$user_included[] = $users->fk_user;
						}
					}
				}

				if(!empty($this->exclude_group)){
					$user_excluded=array();
					$sqlusers = "SELECT fk_user FROM " . MAIN_DB_PREFIX . "usergroup_user WHERE fk_usergroup IN(" . $this->exclude_group . ") ";
					$resqlusers  = $db->query($sqlusers);
					if($resqlusers){
						while ($users = $db->fetch_object($resqlusers)){
							$user_excluded[] = $users->fk_user;
						}
					}
				}
				print '&nbsp; &nbsp;' . $this->title;
				print $form->select_dolusers($this->value,$this->html_name,$this->use_empty,$user_excluded,$disabled,$user_included);
				break;

			case 'select_year':
				if($this->value == -1) $this->value="";

				if(empty($this->value) && !empty($this->default)){
					$this->value = $this->default;
				}
				print '&nbsp; &nbsp;' . $this->title;
				$formother->select_year($this->value,$this->html_name,$this->use_empty,$this->min_year,$this->max_year);
				break;

			case 'select_array':
						if($this->value == -1) $this->value="";
						print '&nbsp; &nbsp;' . $this->title;
						print $form->selectarray($this->html_name, $this->array,$this->value,$this->use_empty);
				}
	}


}



class Dyntable_fields
{
	public $name;
	public $label;
	public $checked;
	public $sub_title;
	public $field;
	public $unit;
	public $align;

	function __construct($db)
	{
		$this->db = $db;

	}
}



$form = new Form($db);
$formother = new FormOther($db);

$limit = GETPOST("limit")?GETPOST("limit","int"):$conf->liste_limit;

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


?>