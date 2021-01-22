<?php
/* Copyright (C) 2012-2013  Charles-Fr BENKE		<charles.fr@benke.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 * \file htdocs/core/class/html.formvolvo.class.php
 * \ingroup core
 * \brief File of class with all html predefined components
 */

/**
 * Class to manage generation of HTML components for contract module
 */
class FormVolvo
{
	var $db;
	var $error;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db) {
		$this->db = $db;
	}

	/**
	 * Show a combo list with contracts qualified for a third party
	 *
	 * @param int $socid Id third party (-1=all, 0=only contracts not linked to a third party, id=contracts not linked or linked to third party id)
	 * @param int $selected Id contract preselected
	 * @param string $htmlname Nom de la zone html
	 * @param int $maxlength Maximum length of label
	 * @param int $showempty Show empty line
	 * @return int Nbr of project if OK, <0 if KO
	 */
	public function select_withcheckbox($htmlname = '', $values = array(), $selectedvalues = array(), $moreparam = '') {
		$nb = ceil(count($values)/35);

		$out = '<div align="left"><table class="nobordernopadding"><tr>';
		$i = 0;
		foreach ( $values as $key => $label ) {
			$out .= '<td><input class="flat" type="checkbox" align="left" name="' . $htmlname . '[]" ' . ($moreparam ? $moreparam : '');
			$out .= ' value="' . $key . '"';
			if (in_array($key, $selectedvalues)) {
				$out .= 'checked';
			}
			$out .= '/>' . $label . '</td>';
			$i++;
			if ($i == $nb){
				$out .= '</tr><tr>';
				$i = 0;
			}
		}

		$out .= '</tr></table></div>';

		return $out;
	}

	public function select_withcheckbox_flat($htmlname = '', $values = array(), $selectedvalues = array(), $moreparam = '') {
		$out = '<div align="left"><table class="nobordernopadding"><tr>';
		$i = 0;
		foreach ( $values as $key => $label ) {
			$out .= '<td><input class="flat" type="checkbox" align="left" name="' . $htmlname . '[]" ' . ($moreparam ? $moreparam : '');
			$out .= ' value="' . $key . '"';
			if (in_array($key, $selectedvalues)) {
				$out .= 'checked';
			}
			$out .= '/>' . $label . '</td>';
		}

		$out .= '</tr></table></div>';

		return $out;
	}


	public function selectFournPrice($htmlname = 'fournprice', $selectedvalue, $fk_product = 0, $nooutput = 1) {
		require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.product.class.php';

		$out = '<select id="' . $htmlname . '" name="' . $htmlname . '" class="flat">';

		$sp = new ProductFournisseur($this->db);
		$result = $sp->list_product_fournisseur_price($fk_product);
		if ($result == - 1) {
			setEventMessages(null, array(
					$sp->error
			), 'errors');
		} else {
			if (is_array($result) && count($result) > 0) {
				foreach ( $result as $line ) {
					if ($selectedvalue == $line->product_fourn_price_id) {
						$selected = " selected ";
					} else {
						$selected = '';
					}
					$out .= '<option value="' . $line->product_fourn_price_id . '" ' . $selected . '>' . $line->fourn_name . ' - ' . price($line->fourn_price) . ' (' . $line->ref_supplier . ')' . '</option>';
				}
			}
		}

		$out .= '</select>';

		if ($nooutput) {
			return $out;
		} else {
			print $out;
		}
	}

	/**
	 *
	 * @param unknown $key
	 * @param unknown $columndef
	 * @param unknown $destcolumn
	 */
	public function select_dest_column($key, $columndef = array(), $destcolumn = array()) {
		global $langs;
		$out .= '<select id="volvocol_' . $columndef['name'] . '" class="flat" name="volvocol_' . $columndef['name'] . '">';
		$out .= '<option value=-1"></option>';
		foreach ( $destcolumn as $key => $column ) {
			if ($columndef['label'] == $column['filecolumntitle']) {
				$out .= '<option value="' . $key . '" selected="selected">' . $column['columntrans'] . '(' . $column['tabletrans'] . ')' . '</option>';
			} else {
				$out .= '<option value="' . $key . '">' . $column['columntrans'] . '(' . $column['tabletrans'] . ')' . '</option>';
			}
		}

		$out .= '</select>';

		return $out;
	}

	/**
	 *
	 * @param unknown $key
	 * @param unknown $columndef
	 * @param unknown $srccolumn
	 */
	public function select_src_column($key, $columndef = array(), $srccolumn = array()) {
		global $langs;
		$out .= '<select id="volvocol_' . $key . '" class="flat" name="volvocol_' . $key . '">';
		$out .= '<option value=-1"></option>';
		foreach ( $srccolumn as $key => $column ) {
			if ($columndef['filecolumntitle'] == $column['label'] || $columndef['forcetmpcolumnname'] == $column['name']) {
				$out .= '<option value="' . $column['name'] . '" selected="selected">' . $column['label'] . '</option>';
			} else {
				$out .= '<option value="' . $column['name'] . '">' . $column['label'] . '</option>';
			}
		}

		$out .= '</select>';

		return $out;
	}

	/**
	 *
	 * @param unknown $arrayColumnDef
	 * @param string $columnName
	 * @param string $value
	 */
	public function importFieldData($arrayColumnDef = array(), $matchColmunArray = array(), $columnName = '', $value = '', $rowid = 0, $tableName = '', $integration_comment = '', $currentaction = '') {
		global $langs;

		$this->resPrint = '';
		$out_html_after = '';

		$coladdr = 'realaddress';
		foreach ( $arrayColumnDef as $key => $data ) {
			if (array_key_exists('iszip', $data) && ! empty($data['iszip'])) {
				$columnTmpName = $matchColmunArray[$key];
				$colzip = $columnTmpName;
			}
			if (array_key_exists('istown', $data) && ! empty($data['istown'])) {
				$columnTmpName = $matchColmunArray[$key];
				$coltown = $columnTmpName;
			}
		}

		var_dump($arrayColumnDef);
		 var_dump($matchColmunArray);
		 var_dump($columnName);
		$coldata = $arrayColumnDef[array_search($columnName, $matchColmunArray)];
		$actualvalue = '';
		// Find column error
		if (! empty($integration_comment)) {
			$integration_comment_array = json_decode($integration_comment, true);
			if (! is_array($integration_comment_array)) {
				$error ++;
				$this->error[] = 'CannotConvertIntegrationCommentIntoArray';
			} elseif (count($integration_comment_array > 0)) {
				foreach ( $integration_comment_array as $info_error ) {
					if ($info_error['column'] == $columnName && ! empty($info_error['outputincell'])) {
						$out_html = '<div name="' . $columnName . '_integrationpb_' . $rowid . '" id="' . $columnName . '_integrationpb_' . $rowid . '" style="white-space: nowrap;color:' . $info_error['color'] . '">';
						$out_html .= '<p>' . dol_html_entity_decode($info_error['message'], ENT_QUOTES, 'UTF-8') . '</p>';
						$out_html .= '</div>';
					}
					if ($info_error['column'] == $columnName && array_key_exists('actualvalue', $info_error)) {
						$actualvalue = $info_error['actualvalue'];
					}
				}
			}
		}

		if (! empty($coldata['editable']) && ! empty($out_html)) {
			$outputbutton = true;
			require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
			$form = new Form($this->db);

			if (array_key_exists('ispatronyme', $coldata) && ! empty($coldata['ispatronyme'])) {
				$colpatronyme = $columnName;
				$updatefindcolumn = $columnName;
				$columnName = 'thirdparty_id';
				$updatefindvalue = $this->db->escape($value);
			} else {
				$updatefindcolumn = 'rowid';
			}

			$out_js_head = '<script>' . "\n";
			$out_js_head .= '$(document).ready(function () { ' . "\n";
			$out_js_head .= '	var url = \'' . dol_buildpath('/volvo/import/ajax/update_temp_table.php', 2) . '\';' . "\n";
			$out_js_head .= '	var urlcustomer = \'' . dol_buildpath('/volvo/import/ajax/create_customer.php', 2) . '\';' . "\n";
			$out_js_head .= '	var rowid = \'' . $rowid . '\';' . "\n";
			$out_js_head .= '	var columname=\'' . $columnName . '\';' . "\n";
			$out_js_head .= '	var updatefindcolumn=\'' . $updatefindcolumn . '\';' . "\n";
			$out_js_head .= '	var updatefindvalue=\'' . $updatefindvalue . '\';' . "\n";
			$out_js_head .= '' . "\n";
			$out_js_head .= '	$(\'#' . $columnName . '_editmode_' . $rowid . '\').click(function(){' . "\n";
			$out_js_head .= '		$(\'#' . $columnName . '_view_' . $rowid . '\').toggle();' . "\n";
			$out_js_head .= '		$(\'#' . $columnName . '_edit_' . $rowid . '\').toggle();' . "\n";
			$out_js_head .= '		$(\'[name*="_editmode_"]\').toggle();';
			$out_js_head .= '	});' . "\n";
			$out_js_head .= '	$(\'#' . $columnName . '_cancel_' . $rowid . '\').click(function(){' . "\n";
			$out_js_head .= '		$(\'#' . $columnName . '_view_' . $rowid . '\').toggle();' . "\n";
			$out_js_head .= '		$(\'#' . $columnName . '_edit_' . $rowid . '\').toggle();' . "\n";
			$out_js_head .= '		$(\'[name*="_editmode_"]\').toggle();';
			$out_js_head .= '	});' . "\n";

			$out_js_footer = '});' . "\n";
			$out_js_footer .= '</script>' . "\n";

			$out_html .= '<div name="' . $columnName . '_edit_' . $rowid . '" id="' . $columnName . '_edit_' . $rowid . '" style="display:none; width:100%">';

			// Output diffrent htl input according data type
			if ($coldata['type'] == 'text' && ! array_key_exists('dict', $coldata) && ! array_key_exists('fk_table', $coldata)) {
				$out_html .= '<input type="text" value="' . $value . '" name="' . $columnName . '_input_' . $rowid . '" id="' . $columnName . '_input_' . $rowid . '" />';
			} elseif ($coldata['type'] == 'date' && ! array_key_exists('dict', $coldata)) {

				try {
					$datetime = new DateTime($value);
					$out_html .= $form->select_date($datetime->getTimestamp(), $columnName . '_input_' . $rowid, 0, 0, 0, "", 1, 1, 1);
				} catch ( Exception $e ) {
					$out_html .= $form->select_date('', $columnName . '_input_' . $rowid, 0, 0, 0, "", 1, 1, 1);
				}
			} elseif (array_key_exists('dict', $coldata)) {
				if ($coldata['dict'] != 'country') {
					$out_html .= $this->select_dict($coldata['dict'], $columnName . '_input_' . $rowid, $value, 'html', 'code', 'code');
				} else {
					$out_html .= $this->select_dict($coldata['dict'], $columnName . '_input_' . $rowid, $value, 'html', 'label', 'label');
				}
			} elseif (array_key_exists('ispatronyme', $coldata) && ! empty($coldata['ispatronyme'])) {
				$out_html .= $langs->trans('VolvoCustomerLinkTo', $value) . $form->select_company('', $columnName . '_input_' . $rowid, 's.client = 1 OR s.client = 3', 1, 0, 0, array(), 0, 'minwidth300');
				$out_html_createcust = '<BR>' . $langs->trans('VolvoOr') . '<BR>' . $langs->trans('VolvoCreateThirdparty', img_picto($langs->trans('VolvoCreateThirdparty'), 'refresh', ' name="' . $columnName . '_createcust_' . $rowid . '" id="' . $columnName . '_createcust_' . $rowid . '"'));
				$out_html_createcust .= '<input type="hidden" value="' . $value . '" name="' . $columnName . '_createcust_' . $rowid . '" id="' . $columnName . '_createcust_' . $rowid . '" />';
				$out_html_createcust .= '<BR>';
			} elseif (array_key_exists('fk_table', $coldata) && $coldata['fk_table'] == MAIN_DB_PREFIX . 'user') {
				$out_html .= $langs->trans('VolvoContactAdminToSolveThisLogin', $value);
				$outputbutton = false;
			}

			if ($outputbutton) {
				$out_html .= '&nbsp;&nbsp;&nbsp;' . img_picto($langs->trans('Save'), 'tick', ' name="' . $columnName . '_save_' . $rowid . '" id="' . $columnName . '_save_' . $rowid . '"');
			}
			$out_html .= '&nbsp;&nbsp;&nbsp;' . img_picto($langs->trans('Cancel'), 'editdelete', ' name="' . $columnName . '_cancel_' . $rowid . '" id="' . $columnName . '_cancel_' . $rowid . '"');
			$out_html .= $out_html_createcust;
			$out_html .= '</div>';
			// $out_html .= '<div style="display: inline;">';
			$out_html .= '<div id="' . $columnName . '_view_' . $rowid . '" name="' . $columnName . '_view_' . $rowid . '" style="display: inline; float: left;">';
			$out_html .= '<span id="' . $columnName . '_value_' . $rowid . '" name="' . $columnName . '_value_' . $rowid . '">' . $value . '</span>';
			if (! empty($actualvalue)) {
				$out_html .= '<span style="white-space: nowrap;color:red"> / ' . $actualvalue . '</span>';
			}
			$out_html .= img_picto($langs->trans('Edit'), 'edit', ' name="' . $columnName . '_editmode_' . $rowid . '" id="' . $columnName . '_editmode_' . $rowid . '"');
			$out_html .= '</div>';
			// $out_html .= '</div>';

			$out_js_action = '		$.get( url,' . "\n";
			$out_js_action .= '			{' . "\n";
			$out_js_action .= '				rowid: rowid,' . "\n";
			$out_js_action .= '				columname: columname,' . "\n";
			$out_js_action .= '				tablename: \'' . $tableName . '\',' . "\n";
			$out_js_action .= '				datatype: \'' . $coldata['type'] . '\',' . "\n";
			$out_js_action .= '				updatefindcolumn: updatefindcolumn,' . "\n";
			$out_js_action .= '				updatefindvalue: updatefindvalue,' . "\n";
			$out_js_action .= '				value: value' . "\n";
			$out_js_action .= '			})' . "\n";
			$out_js_action .= '			.done(function( data ) {' . "\n";
			$out_js_action .= '				if (data==1) {' . "\n";
			$out_js_action .= '					$(\'#' . $columnName . '_view_' . $rowid . '\').toggle();' . "\n";
			$out_js_action .= '					$(\'#' . $columnName . '_edit_' . $rowid . '\').toggle();' . "\n";

			// Output diffrent htl input according data type
			if ($coldata['type'] == 'text' && ! array_key_exists('dict', $coldata)) {
				$out_js_action .= '					$(\'#' . $columnName . '_value_' . $rowid . '\').text(value);' . "\n";
			} elseif ($coldata['type'] == 'date' && ! array_key_exists('dict', $coldata)) {
				$out_js_action .= '					$(\'#' . $columnName . '_value_' . $rowid . '\').text(value);' . "\n";
			} elseif (array_key_exists('dict', $coldata)) {
				$out_js_action .= '					$(\'#' . $columnName . '_value_' . $rowid . '\').text(value);' . "\n";
			} elseif (array_key_exists('fk_table', $coldata) && $coldata['fk_table'] == 'societe') {
				$out_js_action .= '					$(\'#' . $columnName . '_value_' . $rowid . '\').text(value);' . "\n";
			}
			$out_js_action .= '					$(\'[name*="_editmode_"]\').toggle();';
			$out_js_action .= '					$(\'#' . $columnName . '_editmode_' . $rowid . '\').hide();' . "\n";
			$out_js_action .= '					$(\'#recheck\').show();';
			$out_js_action .= '					$(\'#importdata\').hide();';
			$out_js_action .= '					$(\'#action\').val(\'' . $currentaction . '\');';
			$out_js_action .= '					$(\'#step\').val(\'6\');';
			$out_js_action .= '					$(\'#recheck\').show();';
			$out_js_action .= '				} else {alert("Error "+data)}' . "\n";
			$out_js_action .= '			})' . "\n";
			$out_js_action .= '			.fail(function( data ) {' . "\n";
			$out_js_action .= '			  alert( "Error ");' . "\n";
			$out_js_action .= '			});' . "\n";

			$out_js = '	$(\'#' . $columnName . '_save_' . $rowid . '\').click(function(){' . "\n";

			// Output diffrent htl input according data type
			if ($coldata['type'] == 'text' && ! array_key_exists('dict', $coldata)) {
				$out_js .= '	var value=$(\'#' . $columnName . '_input_' . $rowid . '\').val();' . "\n";
				$out_js .= $out_js_action;
			} elseif ($coldata['type'] == 'date') {
				$out_js .= ' 	var dt = new Date($(\'#' . $columnName . '_input_' . $rowid . 'year\').val(),$(\'#' . $columnName . '_input_' . $rowid . 'month\').val()-1,$(\'#' . $columnName . '_input_' . $rowid . 'day\').val());' . "\n";
				$out_js .= '	var value=formatDate(dt, \'yyyyMMdd\');' . "\n";
				$out_js .= $out_js_action;
			} elseif (array_key_exists('dict', $coldata)) {
				$out_js .= '	var value=$(\'#' . $columnName . '_input_' . $rowid . '\').val();' . "\n";
				$out_js .= $out_js_action;
			}
			$out_js .= '	});' . "\n";

			if (! empty($out_html_createcust)) {
				$out_js .= '	$(\'#' . $columnName . '_createcust_' . $rowid . '\').click(function(){' . "\n";
				$out_js .= '	var value=$(\'#' . $columnName . '_createcust_' . $rowid . '\').val();' . "\n";
				$out_js .= '		$.get( urlcustomer,' . "\n";
				$out_js .= '			{' . "\n";
				$out_js .= '				rowid: '.$rowid.',' . "\n";
				$out_js .= '				tablename: \'' . $tableName . '\',' . "\n";
				$out_js .= '				colpatronyme: \'' . $colpatronyme . '\',' . "\n";
				$out_js .= '				coladdr: \'' . $coladdr . '\',' . "\n";
				$out_js .= '				colzip: \'' . $colzip . '\',' . "\n";
				$out_js .= '				coltown: \'' . $coltown . '\',' . "\n";
				$out_js .= '			})' . "\n";
				$out_js .= '			.done(function( data ) {' . "\n";
				$out_js .= '				if (data==1) {' . "\n";
				$out_js .= '					$(\'#' . $columnName . '_view_' . $rowid . '\').toggle();' . "\n";
				$out_js .= '					$(\'#' . $columnName . '_edit_' . $rowid . '\').toggle();' . "\n";
				//$out_js .= '					$(\'#' . $columnName . '_value_' . $rowid . '\').text(value);' . "\n";
				$out_js .= '					$(\'[name*="_editmode_"]\').toggle();';
				$out_js .= '					$(\'#recheck\').show();';
				$out_js .= '					$(\'#importdata\').hide();';
				$out_js .= '					$(\'#action\').val(\'' . $currentaction . '\');';
				$out_js .= '					$(\'#' . $columnName . '_editmode_' . $rowid . '\').hide();' . "\n";
				$out_js .= '					$(\'#step\').val(\'6\');';
				$out_js .= '					$(\'#recheck\').show();';
				$out_js .= '				} else {alert("Error "+data)}' . "\n";
				$out_js .= '			})' . "\n";
				$out_js .= '			.fail(function( data ) {' . "\n";
				$out_js .= '			  alert( "Error ");' . "\n";
				$out_js .= '			});' . "\n";
				$out_js .= '	});' . "\n";
			}
		} else {
			$out_html .= $value;
		}

		$out = $out_js_head . $out_js . $out_js_footer . $out_html;
		$this->resPrint = $out;

		if (empty($error)) {
			return 1;
		} else {
			return - 1 * $error;
		}
	}

	/**
	 *
	 * @param unknown $file
	 * @return string
	 */
	public function select_tabs($filesource, $htmlname = '', $selectlabel = '', $outputformat = 'html', $outputlabel = 'code') {
		global $langs;

		require_once 'volvoimport.class.php';

		$object = new VolvoImport($this->db);
		$object->initFile($filesource, 'port');
		$result = $object->loadFile();
		if ($result < 0) {
			setEventMessages(null, $object->errors, 'errors');
		}

		if (is_array($object->sheetArray) && count($object->sheetArray) > 0) {
			if ($outputformat == 'html') {
				$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '">';
			}
			foreach ( $object->sheetArray as $key => $sheet ) {
				if (! empty($selectlabel) && $selectlabel == $sheet) {
					$out .= '<option value="' . $key . '" selected="selected">' . $sheet . '</option>';
				} else {
					$out .= '<option value="' . $key . '">' . $sheet . '</option>';
				}
			}
			if ($outputformat == 'html') {
				$out .= '</select>';
			}
		}

		return $out;
	}

	public function select_model( $htmlname = '', $selectlabel = ''){
		$sql = 'SELECT rowid, modele FROM ' .MAIN_DB_PREFIX . 'volvo_modele_fdd WHERE active = 1';
		$resql = $this->db->query($sql);
		if($resql){
			while ($object = $this->db->fetch_object($resql)){
				$arrayresult[$object->rowid] = $object->modele;
			}
		}
		if(is_array($arrayresult)){
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '">';
			foreach ($arrayresult as $key => $label){
				if (! empty($selectlabel) && $selectlabel == $key) {
					$out .= '<option value="' . $key . '" selected="selected">' . $label . '</option>';
				} else {
					$out .= '<option value="' . $key . '">' . $label . '</option>';
				}
			}
			$out .= '</select>';
		}
		return $out;
	}


}