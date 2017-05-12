<?php
global $conf,$db;

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
	public $filter_line;


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
		print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="search_form">' . "\n";
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
		print '<input type="hidden" name="sortfield" value="'.$this->sortfield.'">';
		print '<input type="hidden" name="sortorder" value="'. $this->sortorder.'">';
	}

	function draw_tool_bar(){
		global $conf, $user;
		$form = new Form($this->db);
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
					$p->draw_tool($this->option);
				print '</th>';
			}
		}
		print '<th class="liste_titre"></th>';
		print "</tr>";
		print '</table>';


	}

	function draw_table_head(){

		$group= array();
		foreach ($this->arrayfields as $f){
			if($f->checked==1) $group[$f->sub_title]+=1;
		}

		print '<table class="noborder" width="100%">';
		if(count($this->sub_title)>0){
			print '<tr class="liste_titre" style="height:22px;">';
			foreach ($this->arrayfields as $f){
				if($f->sub_title>0 && $f->checked == 1 && $groupdone[$f->sub_title]==0){
					print '<th class="liste_titre" colspan="' . $group[$f->sub_title] . '" align="center">' . $this->sub_title[$f->sub_title] . '</th>';
					$groupdone[$f->sub_title] = 1;
				}elseif($f->sub_title==0 && $f->checked == 1){
					print_liste_field_titre($f->label,$_SERVER["PHP_SELF"],$f->field,'',$this->option,'rowspan="2" align="' . $f->align .'" ' . $f->other_attribute ,$this->sortfield,$this->sortorder);
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
					print_liste_field_titre($f->label,$_SERVER["PHP_SELF"],$f->field,'',$this->option,'align="' . $f->align .'" ' . $f->other_attribute ,$this->sortfield,$this->sortorder);
				}
			}
			print "</tr>";
		}
		if($this->filter_line == 1){
			print '<tr class="liste_titre" style="height:22px;">';
			foreach ($this->arrayfields as $f){
				if($f->checked == 1){
					print '<td class="liste_titre" align="' . $f->align . '">';
					if(is_array($f->filter)){
						foreach ($f->filter as $c){
							$c->draw_tool($this->option);
						}
					}
					print '</td>';
				}
			}
			print "</tr>";
		}

	}

	function end_table(){
		print '</table>';
		print '</form>';

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
			$line_array_total = array();
			foreach ($object->$result as $line){
				$var = !$var;
				$line_array = array();
				$line_array['class'] = $bc[$var];
				$line_array['class_td'] = '';
				$line_array['option'] = $this->option;
				foreach ($this->arrayfields as $f){
					if(empty($f->type)){
						$champs = $f->alias;
						$line_array[$f->name] = $line->$champs;
						if(!empty($this->total_line) && $f->total == 'value'){
							$line_array_total[$f->name]+=$line_array[$f->name];
						}
					}
				}
				foreach ($this->arrayfields as $f){
					if($f->type == 'calc'){
						$line_array[$f->name] = $f->calcul($line_array, $this->arrayfields);
						if(!empty($this->total_line) && $f->total == 'value'){
							$line_array_total[$f->name]+=$line_array[$f->name];
						}
					}
				}

				foreach ($this->arrayfields as $f){
					if($f->type == 'button'){
						$line_array[$f->name] = $f->button($this->option,$line_array, $this->arrayfields);
					}
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

			$var =true;
			$line_array_total = array();
			foreach ($result as $line){
				$var = !$var;
				$line_array = array();
				$line_array['class'] = $bc[$var];
				$line_array['class_td'] = '';
				$line_array['option'] = $this->option;

				foreach ($this->arrayfields as $f){
					if(empty($f->type)){
						$line_array[$f->name] = $line[$f->alias];
						if(!empty($this->total_line) && $f->total == 'value'){
							$line_array_total[$f->name]+=$line_array[$f->name];
						}
					}
				}

				foreach ($this->arrayfields as $f){
					if($f->type == 'calc'){
						$line_array[$f->name] = $f->calcul($line_array, $this->arrayfields);
						if(!empty($this->total_line) && $f->total == 'value'){
							$line_array_total[$f->name]+=$line_array[$f->name];
						}
					}
				}

				foreach ($this->arrayfields as $f){
					if($f->type == 'button'){
						$line_array[$f->name] = $f->button($this->option,$line_array, $this->arrayfields);
					}
				}

				$this->array_display[] = $line_array;
			}


		}elseif ($this->mode=='sql_methode'){
			$this->sql_select = '';
			$this->sql_group = '';
			foreach ($this->arrayfields as $f){
				if(!empty($f->field) && !empty($f->alias)){
					$this->sql_select.=$f->field . ' AS ' .$f->alias . ', ';
					if($f->group ==1){
						$this->sql_group.= $f->field . ', ';
					}
				}
			}
			$this->sql_select = substr($this->sql_select,0, -2);
			if(strlen($this->sql_group)>0){
				$this->sql_group = substr($this->sql_group,0,-2);
			}
			if(count($this->filter)>0){
				$this->action_array= array();
				foreach ($this->sql_filter_action as $action){
					$temp =array();
					$temp = array_fill_keys($action['keys'], $action['action']);
					$this->action_array = array_merge($this->action_array,$temp);
				}

				foreach ($this->filter as $key => $value){
					if(array_key_exists($key, $this->action_array)){
						$clause = $this->action_array[$key];
						$clause = str_replace('#KEY#', $key, $clause);
						$clause = str_replace('#VALUE#', $value, $clause);
						if($this->filter_clause == 'WHERE'){
							if(!empty($this->sql_where)){
								$this->sql_where.= ' ' .  $this->filter_mode . $clause;
							}else{
								$this->sql_where.= $clause;
							}
						}elseif($this->filter_clause == 'HAVING'){
							if(!empty($this->sql_having)){
								$this->sql_having.= ' ' .  $this->filter_mode . $clause;
							}else{
								$this->sql_having.= $clause;
							}
						}
					}
				}
			}


			$this->sql = 'SELECT ' . $this->sql_select;
			$this->sql.= ' FROM ' . $this->sql_from;
			$this->sql.= (empty($this->sql_where)?'':' WHERE ' . $this->sql_where);
			$this->sql.= (empty($this->sql_group)?'':' GROUP BY ' . $this->sql_group);
			$this->sql.= (empty($this->sql_having)?'':' HAVING ' . $this->sql_having);
			$this->sql.= $this->db->order($this->sortfield,$this->sortorder);
			$this->nbtotalofrecords =0;
			$res = $this->db->query($this->sql);
			if($res) $this->nbtotalofrecords = $this->db->num_rows($res);
			if($this->limit > 0) $this->sql.= $this->db->plimit($this->limit+1, $this->offset);

			$this->num = 0;
			$resql = $this->db->query($this->sql);
			if($resql){
				$this->num = $this->db->num_rows($resql);
				$var =true;
				$line_array_total = array();
				while($obj = $this->db->fetch_object($resql)){
				$var = !$var;
				$line_array = array();
				$line_array['class'] = $bc[$var];
				$line_array['class_td'] = '';
				$line_array['option'] = $this->option;
				foreach ($this->arrayfields as $f){
					if(empty($f->type)){
						$champs = $f->alias;
						$line_array[$f->name] = $obj->$champs;;
						if(!empty($this->total_line) && $f->total == 'value'){
							$line_array_total[$f->name]+=$line_array[$f->name];
						}
					}
				}

				foreach ($this->arrayfields as $f){
					if($f->type == 'calc'){
						$line_array[$f->name] = $f->calcul($line_array, $this->arrayfields);
						if(!empty($this->total_line) && $f->total == 'value'){
							$line_array_total[$f->name]+=$line_array[$f->name];
						}
					}
				}

				foreach ($this->arrayfields as $f){
					if($f->type == 'button'){
						$option = $this->option . '&sortfield=' . $this->sortfield . '&sortorder=' . $this->sortorder;
						$option.= '&offset=' . $this->offset . '&page=' . $this->page;
						$line_array[$f->name] = $f->button($option,$line_array, $this->arrayfields);
					}
				}

				$this->array_display[] = $line_array;
				}
			}
		}

		if(!empty($this->total_line) && !empty($line_array_total)){
			foreach ($this->arrayfields as $f){
				if($f->total == 'value'){
					$line_array[$f->name] = $line_array_total[$f->name];
				}elseif($f->total == 'name'){
					$line_array[$f->name] = $this->total_line;
				}elseif($f->total =='calc'){
					$line_array[$f->name] = $f->calcul($line_array_total, $this->arrayfields);
				}elseif($f->total =='none'){
					$line_array[$f->name] = '';
				}
				$line_array['class'] =  'class="liste_titre"';
				$line_array['class_td'] = ' class="liste_titre"';
				$line_array['option'] = $this->option;
				$line_array['total'] = 1;
			}
			$this->array_display[] = $line_array;
		}


	}

	function draw_data_table(){

		foreach ($this->array_display as $l){
			print '<tr ' . $l['class'] . '>';
			$td_class = $l['class_td'];
			$l['option'] = $this->option;
			foreach ($this->arrayfields as $key => $val){
				if($val->checked ==1){
					print '<td ' . $td_class . ' align="' . $val->align . '" style="white-space:nowrap;">';
					if(!empty($l[$val->name])) print $val->traitement($l[$val->name],$l,$this->option);
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

		$this->offset = ($this->limit+1) * $this->page;

		if (empty($this->sortorder))
			$this->sortorder = "ASC";
		if (empty($this->sortfield))
			$this->sortfield = $this->default_sortfield;

		if (GETPOST("button_removefilter_x")) {
			foreach ($this->extra_tools as $key => $p){
				if(strpos($p->type, 'between')>0){
					$p->value = $p->default;
					$this->extra_tools[$key] = $p;
					unset($_POST[$p->html_name . 'min']);
					unset($_POST[$p->html_name . 'max']);
				}else{
					$p->value = $p->default;
					$this->extra_tools[$key] = $p;
					unset($_POST[$p->html_name]);
				}
			}
			if($this->filter_line ==1){
				foreach ($this->arrayfields as $key => $f){
					foreach ($f->filter as $keyfilter => $p){
						if(strpos($p->type, 'between')>0){
							$p->value = $p->default;
							$this->arrayfields[$key]->filter[$keyfilter] = $p;
							unset($_POST[$p->html_name . 'min']);
							unset($_POST[$p->html_name . 'max']);
							unset($_POST[$p->html_name . 'min_']);
							unset($_POST[$p->html_name . 'max_']);
							unset($_POST[$p->html_name . 'min_day']);
							unset($_POST[$p->html_name . 'max_day']);
							unset($_POST[$p->html_name . 'min_month']);
							unset($_POST[$p->html_name . 'max_month']);
							unset($_POST[$p->html_name . 'min_year']);
							unset($_POST[$p->html_name . 'max_year']);
						}else{
							$p->value = $p->default;
							$this->arrayfields[$key]->filter[$keyfilter] = $p;
							unset($_POST[$p->html_name]);
						}
					}
				}
			}
		}

		foreach ($this->extra_tools as $key => $p){
			if($p->type=='date_between'){
				$name1 = $p->html_name .'min_';
				$name2 = $p->html_name .'max_';
				$post1 = GETPOST($name1);
				$post2 = GETPOST($name2);
				if(strlen($post1)>0 && strlen($post2)>0){
					$val1 = dol_mktime(0, 0, 0, GETPOST($name1.'month'), GETPOST($name1.'day'), GETPOST($name1.'year'));
					$val2 = dol_mktime(0, 0, 0, GETPOST($name2.'month'), GETPOST($name2.'day'), GETPOST($name2.'year'));
					$p->value = array($val1,$val2);
					$this->extra_tools[$key] = $p;
					$this->filter[$p->filter] ="'" . $this->db->idate($val1) . "' AND '" . $this->db->idate($val2) ."'";
					$this->option.= '&' .$p->html_name .'min=' . $val1 . '&' . $p->html_name.'max='.$val2;
				}

			}elseif($p->type == 'text_between'){
				$name1 = $p->html_name .'min';
				$name2 = $p->html_name .'max';
				$post1 = GETPOST($name1);
				$post2 = GETPOST($name2);
				if(strlen($post1)>0 || strlen($post2)>0){
					if(empty($post1)) $post1 = '0';
					if(empty($post2)) $post2 = '0';
					$p->value = array($post1,$post2);
					$this->extra_tools[$key] = $p;
					$this->filter[$p->filter] ="'" . $post1 . "' AND '" . $post2 ."'";
					$this->option.= '&' .$name1 .'=' . $post1 . '&' . $name2.'='.$post2;
				}
			}else{
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
		}
		if($this->filter_line ==1){
			foreach ($this->arrayfields as $key => $f){
				foreach ($f->filter as $keyfilter => $p){
					if($p->type=='date_between'){
						$name1 = $p->html_name .'min_';
						$name2 = $p->html_name .'max_';
						$post1 = GETPOST($name1);
						$post2 = GETPOST($name2);
						if(strlen($post1)>0 && strlen($post2)>0){
							$val1 = dol_mktime(0, 0, 0, GETPOST($name1.'month'), GETPOST($name1.'day'), GETPOST($name1.'year'));
							$val2 = dol_mktime(0, 0, 0, GETPOST($name2.'month'), GETPOST($name2.'day'), GETPOST($name2.'year'));
							$p->value = array($val1,$val2);
							$this->arrayfields[$key]->filter[$keyfilter] = $p;
							$this->filter[$p->filter] ="'" . $this->db->idate($val1) . "' AND '" . $this->db->idate($val2) ."'";
							$this->option.= '&' .$p->html_name .'min=' . $val1 . '&' . $p->html_name.'max='.$val2;
						}

					}elseif($p->type == 'text_between'){
						$name1 = $p->html_name .'min';
						$name2 = $p->html_name .'max';
						$post1 = GETPOST($name1);
						$post2 = GETPOST($name2);
						if(strlen($post1)>0 && strlen($post2)>0){
							$p->value = array($post1,$post2);
							$this->arrayfields[$key]->filter[$keyfilter] = $p;
							$this->filter[$p->filter] ="'" . $post1 . "' AND '" . $post2 ."'";
							$this->option.= '&' .$name1 .'=' . $post1 . '&' . $name2.'='.$post2;
						}
					}else{
						$name = $p->html_name;
						$post = GETPOST($name);
						if(!empty($post)){
							$val = $post;
							if($val==-1) $val ="";
							$p->value = $val;
							$this->arrayfields[$key]->filter[$keyfilter] = $p;
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
							$this->arrayfields[$key]->filter[$keyfilter] = $p;
							if(!empty($p->value)){
								$this->filter[$p->filter] = $p->value;
								$this->option .= '&' . $name . '=' . $p->value;
							}
						}
					}
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
				if($f->checked == 1) $ligne[] = strip_tags($f->traitement($disp[$f->name],$disp,$this->option));
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
	public $size;
	public $add_now;

	function __construct($db)
	{
		$this->db = $db;

	}

	function draw_tool($option=''){
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
				if(!empty($this->title)){
					print '&nbsp; &nbsp;' . $this->title;
				}
				print $form->select_dolusers($this->value,$this->html_name,$this->use_empty,$user_excluded,$disabled,$user_included);
				break;

			case 'select_year':
				$formother = new FormOther($this->db);
				if($this->value == -1) $this->value="";

				if(empty($this->value) && !empty($this->default)){
					$this->value = $this->default;
				}
				if(!empty($this->title)){
					print '&nbsp; &nbsp;' . $this->title;
				}
				$formother->select_year($this->value,$this->html_name,$this->use_empty,$this->min_year,$this->max_year);
				break;

			case 'select_array':
				$form = new Form($this->db);
				if($this->value == -1) $this->value="";
				if(empty($this->value) && !empty($this->default)){
					$this->value = $this->default;
				}
				if(!empty($this->title)){
					print '&nbsp; &nbsp;' . $this->title;
				}
				print $form->selectarray($this->html_name, $this->array,$this->value,$this->use_empty);
				break;

			case 'hidden':
				print '<input type="hidden" name="' . $this->html_name . '" id="' . $this->html_name . '" value="' . $this->value . '">';
				break;

			case 'button':
				print '<a class="butAction" href="' . DOL_URL_ROOT . $this->link  . $option . '">' . $this->title . '</a>';
				break;

			case 'check':
				if($this->value == 1){
					$sel = ' checked';
				}else{
					$sel = '';
				}
				if(!empty($this->title)){
					print '&nbsp; &nbsp;' . $this->title . '&nbsp;';
				}
				print '<input type="checkbox" name="' . $this->html_name . '" value="1"' . $sel . '> ';
				break;

			case 'text':
				if(!empty($this->title)){
					print '&nbsp; &nbsp;' . $this->title . ': ';
				}
				print '<input type="text" class="flat" name="' . $this->html_name . '" value="' . $this->value . '" size="' . $this->size . '">';
				break;

			case 'date_between':
				$form = new Form($this->db);
				if(!empty($this->title)){
					print '&nbsp; &nbsp;' . $this->title . ': ';
				}
				print $form->select_date($this->value[0], $this->html_name. 'min_',0,0,1,'',1,$this->add_now,1,0,'','','');
				print $form->select_date($this->value[1], $this->html_name. 'max_',0,0,1,'',1,$this->add_now,1,0,'','','');
				break;

			case 'text_between':
				if(!empty($this->title)){
					print '&nbsp; &nbsp;' . $this->title . ': ';
				}
				print '<input type="text" class="flat" name="' . $this->html_name.'min' . '" value="' . $this->value[0] . '" size="' . $this->size . '">';
				print '<input type="text" class="flat" name="' . $this->html_name.'max' . '" value="' . $this->value[1] . '" size="' . $this->size . '">';
				break;
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
	public $filter = array();

	function __construct($db)
	{
		$this->db = $db;
	}

	function traitement($value,$line,$option){
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
				if($line['total'] == 1){
					$ret = $value . (isset($this->unit)?' ' . $this->unit:'');
				}else{
					$id = $this->post_traitement[3];
					$ret = '<a href="' . DOL_URL_ROOT.$this->post_traitement[1].$this->post_traitement[2].$line[$id].'">' . $value . (isset($this->unit)?' ' . $this->unit:'') . '</a>';
					break;
				}

			case 'link_to':
				if($line['total'] == 1){
					$ret = $value . (isset($this->unit)?' ' . $this->unit:'');
				}else{
					$id = $this->post_traitement[3];
					$ret = '<a href="' . DOL_URL_ROOT.$this->post_traitement[1].$this->post_traitement[2].$line[$id]. (isset($option)? $option:'') . '">' . $value . (isset($this->unit)?' ' . $this->unit:'') . '</a>';
					break;
				}

			default:
				$ret = $value . (isset($this->unit)?' ' . $this->unit:'');
		}

		return $ret;

	}

	function calcul($line_array,$arrayfields){
		$formule = $this->formule;
		foreach ($arrayfields as $f){
			$replace = '#' . $f->name . '#';
			$value = $line_array[$f->name];
			if(empty($value)) $value = "0";
			$formule = str_replace($replace, $value, $formule);
		}
		$error_level = error_reporting();
		error_reporting(0);

		$res = eval("return " . $formule . ";");
		if($res == FALSE) $res = '';
		error_reporting($error_level);
		return $res;

	}

	function button($option,$line_array,$arrayfields){
		if($this->right){
			$href = $this->href;
			foreach ($arrayfields as $f){
				$replace = '#' . $f->name . '#';
				$value = $line_array[$f->name];
				if(empty($value)) $value = "";
				$href = str_replace($replace, $value, $href);
			}
			$href = $href.$option;
			$res = '<a href="' . $href . '">' . $this->img . '</a>';
		}else{
			$res = '';
		}
		return $res;

	}
}

?>