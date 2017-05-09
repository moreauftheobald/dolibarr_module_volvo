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
	public $method;
	public $include;
	public $object;
	public $mode;
	public $result;
	public $limit;
	public $param0='none';
	public $param1='none';
	public $param2='none';
	public $param3='none';
	public $param4='none';
	public $param5='none';
	public $param6='none';
	public $param7='none';
	public $param8='none';
	public $param9='none';
	public $default_sortfield;


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

				if (GETPOST("selectedfields")) $tabparam["MAIN_SELECTEDFIELDS_".$this->context]=GETPOST("selectedfields");
				else $tabparam["MAIN_SELECTEDFIELDS_".$this->context]='';

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
					$p->draw_tool();
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

	function end_table(){
		print '</table>';

		// footer
		llxFooter();
		$this->db->close();
	}

	function data_array(){
		global $bc;
		if($this->mode=='object_methode'){
			dol_include_once($this->include);
			$methode = $this->method;
			$param0 = $this->param0;
			$param1 = $this->param1;
			$param2 = $this->param2;
			$param3 = $this->param3;
			$param4 = $this->param4;
			$param5 = $this->param5;
			$param6 = $this->param6;
			$param7 = $this->param7;
			$param8 = $this->param8;
			$param9 = $this->param9;
			$result = $this->result;
			$object = new $this->object($this->db);
			$object->$methode($this->$param0,$this->$param1,$this->$param2,$this->$param3,$this->$param4,$this->$param5,
					$this->$param6,$this->$param7,$this->$param8,$this->$param9);
			if(isset($this->limit)){
				$limit=$this->limit;
				$this->limit=0;
			}
			$this->nbtotalofrecords = $object->$methode($this->$param0,$this->$param1,$this->$param2,$this->$param3,$this->$param4,$this->$param5,
					$this->$param6,$this->$param7,$this->$param8,$this->$param9);
			if(isset($limit)){
				$this->limit = $limit;
			}else{
				$this->limit = $conf->liste_limit;
			}
			$this->num = $object->$methode($this->$param0,$this->$param1,$this->$param2,$this->$param3,$this->$param4,$this->$param5,
					$this->$param6,$this->$param7,$this->$param8,$this->$param9);

			$var = true;

			foreach ($object->$result as $line){
				$var = !$var;
				$line_array = array();
				$line_array['class'] = $bc[$var];
				$line_array['class_td'] = '';
				$line_array['option'] = $this->option;
				foreach ($this->arrayfields as $f){
					$champs = $f->alias;
					$line_array[$f->name] = $line->$champs;
				}
				$this->array_display[] = $line_array;
			}

		}elseif ($this->mode=='function_methode'){
			dol_include_once($this->include);
			$param0 = $this->param0;
			$param1 = $this->param1;
			$param2 = $this->param2;
			$param3 = $this->param3;
			$param4 = $this->param4;
			$param5 = $this->param5;
			$param6 = $this->param6;
			$param7 = $this->param7;
			$param8 = $this->param8;
			$param9 = $this->param9;
			$reflect = new ReflectionFunction($this->function);
			$result = $reflect->invoke($this->$param0,$this->$param1,$this->$param2,$this->$param3,
					$this->$param4,$this->$param5,$this->$param6,$this->$param7,$this->$param8,$this->$param9);

			foreach ($result as $line){
				$var = !$var;
				$line_array = array();
				$line_array['class'] = $bc[$var];
				$line_array['class_td'] = '';
				$line_array['option'] = $this->option;
				foreach ($this->arrayfields as $f){
					if($f->type == 'calc'){
						$line_array[$f->name] = $f->calcularray($line, $this->arrayfields);
					}else{
						$line_array[$f->name] = $line[$f->alias];
					}
				}
				$this->array_display[] = $line_array;
			}

		}
	}

	function draw_data_table(){
		foreach ($this->array_display as $l){
			print '<tr ' . $l['class'] . '>';
			$td_class = $l['class_td'];
			$l['option'] = $this->option;
			foreach ($this->arrayfields as $key => $val){
				if($val->checked ==1){
					print '<td ' . $td_class . ' align="' . $field->align . '" style="white-space:nowrap;">';
					if(!empty($l[$val->name])) print $val->traitement($l[$val->name],$l);
					print '</td>';
				}
			}
			print '</tr>';
		}
	}

	function post(){
		$this->sortorder = GETPOST('sortorder', 'alpha');
		$this->sortfield = GETPOST('sortfield', 'alpha');
		$this->page = GETPOST('page', 'int');

		$this->offset = ($conf->liste_limit+1) * $this->page;

		if (empty($this->sortorder))
			$this->sortorder = "ASC";
		if (empty($this->sortfield))
			$this->sortfield = $this->default_sortfield;

		if (GETPOST("button_removefilter_x")) {
			foreach ($this->extra_tools as $key => $p){
				$p->value = $p->default;
				$this->extra_tools[$key] = $p;
				unset($_POST[$p->html_name]);
			}
		}

		foreach ($this->extra_tools as $key => $p){
			$name = $p->html_name;
			$post = GETPOST($name);
			if(!empty($post)){
				$val = $post;
				if($val==-1) $val ="";
				$p->value = $val;
				$this->extra_tools[$key] = $p;
				if(!empty($val)){
					$this->filter[$p->filter] = $val;
					$this->option .= '&' . $name . '=' . $val;
				}
			}else{
				if($p->see_all==1){
					$p->value ='';
				}else {
					$p->value = $p->default;
				}
				$this->extra_tools[$key] = $p;
				if(!empty($p->value)){
					$this->filter[$p->filter] = $p->value;
					$this->option .= '&' . $name . '=' . $p->value;
				}
			}
		}
		if(GETPOST("button_export_x")){
			$this->export();
		}


	}


	function export(){
		$selectfields = $this->multiSelectArrayWithCheckbox();
		$this->limit = 0;
		$this->data_array();
		$handler = fopen("php://output", "w");
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $this->export_name . '.csv');
		fputs($handler, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

		$ligne=array();
		foreach ($this->arrayfields as $f){
			if($f->checked ==1) $ligne[]=$f->label;
		}
		fputcsv($handler, $ligne, ';', '"');
		foreach ($this->array_display as $disp){
			$ligne=array();
			foreach ($this->arrayfields as $f){
				if($f->checked == 1) $ligne[] = strip_tags($disp[$f->name]);
			}
			fputcsv($handler, $ligne, ';', '"');
		}
		exit;
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
				$form = new Form($this->db);
				if($this->value == -1) $this->value="";
				if(empty($this->value) && !empty($this->default)){
					$this->value = $this->default;
				}

				$disabled = 0;
				if(!$this->see_all){
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
				$formother = new FormOther($this->db);
				if($this->value == -1) $this->value="";

				if(empty($this->value) && !empty($this->default)){
					$this->value = $this->default;
				}
				print '&nbsp; &nbsp;' . $this->title;
				$formother->select_year($this->value,$this->html_name,$this->use_empty,$this->min_year,$this->max_year);
				break;

			case 'select_array':
				$form = new Form($this->db);
				if($this->value == -1) $this->value="";
				if(empty($this->value) && !empty($this->default)){
					$this->value = $this->default;
				}
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
	public $alias;
	public $post_traitement = array();
	public $search = array();
	public $type;
	public $formule;

	function __construct($db)
	{
		$this->db = $db;
	}

	function traitement($value,$line){
		switch ($this->post_traitement[0]){
			case 'date':
				$ret = dol_print_date($value,$this->post_traitement[1]);
				break;
			case 'num':
				$ret = round($value,$this->post_traitement[1]) . (isset($this->unit)?' ' . $this->unit:'');
				break;

			case 'substr':
				$ret = substr($value, $this->post_traitement[1],$this->post_traitement[2]) . (isset($this->unit)?' ' . $this->unit:'');
				break;

			case 'price':
				$ret = price($value,'','',0,-1,$this->post_traitement[1]). (isset($this->unit)?' ' . $this->unit:'');
				break;

			case 'link':
				$id = $this->post_traitement[3];
				$ret = '<a href="' . DOL_URL_ROOT.$this->post_traitement[1].$this->post_traitement[2].$line->$id.'">' . $value . (isset($this->unit)?' ' . $this->unit:'') . '</a>';
				break;

			case 'link_to':
				$ret = '<a href="' . DOL_URL_ROOT.$this->post_traitement[1].$line['option'].'">' . $value . (isset($this->unit)?' ' . $this->unit:'') . '</a>';
				break;

			default:
				$ret = $value . (isset($this->unit)?' ' . $this->unit:'');
		}

		return $ret;

	}

	function calcularray($line,$arrayfields){
// 		$formule = $this->formule;
// 		foreach ($arrayfields as $f){
// 			$replace = '#' . $f->alias . '#';
// 			$value = $line[$f->alias];
// 			if(empty($value)) $value = "0";
// 			$formule = str_replace($replace, $value, $formule);
// 		}
// 		error_reporting(0);

// 		$res = eval("return " . $formule . ";");
		$res = 1;
		return $res;

	}
}

?>