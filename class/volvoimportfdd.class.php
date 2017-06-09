<?php
/* Volvo
 * Copyright (C) 2015       Florian Henry		<florian.henry@open-concept.pro>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 * \file volvo/class/volvoimportcmcust.class.php
 * \ingroup volvo
 * \brief File to load import files with XSLX format
 */
require_once 'volvoimport.class.php';

/**
 * Class to import consogazoil CSV specific files
 */
class VolvoImportfdd extends VolvoImport
{
	public $lines = array();
	protected $db;
	public $error;
	public $errors = array();
	protected $filesource;
	public $objWorksheet;
	public $sheetArray = array();
	public $columnArray = array();
	protected $objPHPExcel;
	protected $startcell;
	protected $maxcol;
	protected $maxrow;
	public $columnData;
	protected $tempTable;
	public $targetInfoArray = array();

	/**
	 * Constructor
	 *
	 * @param DoliDB $db
	 */
	function __construct($db) {
		global $conf, $langs;

		$langs->load('volvo@volvo');
		$langs->load('companies');

		$this->db = $db;

		$this->targetInfoArray['cdb'] = array(
				'cell' => 'M11',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['client'] = array(
				'cell' => 'J6',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['vss'] = array(
				'cell' => 'M21',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['modele'] = array(
				'cell' => 'B32',
				'type' => 'calc',
				'oblig' => true,
		);

		$this->targetInfoArray['price'] = array(
				'cell' => 'G56',
				'type' => 'val',
				'oblig' => true,
		);

		$this->targetInfoArray['VNC'] = array(
				'cell' => 'G58',
				'type' => 'calc',
				'oblig' => true,
		);

		$this->targetInfoArray['comission'] = array(
				'cell' => 'G60',
				'type' => 'calc',
				'oblig' => true,
		);

		$this->targetInfoArray['flotte'] = array(
				'cell' => 'G71',
				'type' => 'calc',
				'oblig' => false,
		);

		$this->targetInfoArray['VNAC'] = array(
				'cell' => 'G72',
				'type' => 'calc',
				'oblig' => true,
		);

		$this->targetInfoArray['interne1'] = array(
				'cell' => 'N33',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne2'] = array(
				'cell' => 'N34',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne3'] = array(
				'cell' => 'N35',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne4'] = array(
				'cell' => 'N36',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne5'] = array(
				'cell' => 'N37',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne6'] = array(
				'cell' => 'N38',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne1_label'] = array(
				'cell' => 'I33',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne2_label'] = array(
				'cell' => 'I34',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne3_label'] = array(
				'cell' => 'I35',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne4_label'] = array(
				'cell' => 'I36',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne5_label'] = array(
				'cell' => 'I37',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['interne6_label'] = array(
				'cell' => 'I38',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['carrosserie1'] = array(
				'cell' => 'N41',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['carrosserie2'] = array(
				'cell' => 'N42',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['carrosserie1_label'] = array(
				'cell' => 'I41',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['carrosserie2_label'] = array(
				'cell' => 'I42',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['externe1'] = array(
				'cell' => 'N44',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['externe2'] = array(
				'cell' => 'N45',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['externe3'] = array(
				'cell' => 'N46',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['externe1_label'] = array(
				'cell' => 'I44',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['externe2_label'] = array(
				'cell' => 'I45',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['externe3_label'] = array(
				'cell' => 'I46',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local1'] = array(
				'cell' => 'N48',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local2'] = array(
				'cell' => 'N49',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local3'] = array(
				'cell' => 'N50',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local4'] = array(
				'cell' => 'N51',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local5'] = array(
				'cell' => 'N52',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local6'] = array(
				'cell' => 'N53',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local7'] = array(
				'cell' => 'N54',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local8'] = array(
				'cell' => 'N55',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local1_label'] = array(
				'cell' => 'I48',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local2_label'] = array(
				'cell' => 'I49',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local3_label'] = array(
				'cell' => 'I50',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local4_label'] = array(
				'cell' => 'I51',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local5_label'] = array(
				'cell' => 'I52',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local6_label'] = array(
				'cell' => 'I53',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local7_label'] = array(
				'cell' => 'I54',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['local8_label'] = array(
				'cell' => 'I55',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov1'] = array(
				'cell' => 'N60',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov2'] = array(
				'cell' => 'N61',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov3'] = array(
				'cell' => 'N62',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov4'] = array(
				'cell' => 'N63',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov5'] = array(
				'cell' => 'N64',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov1_label'] = array(
				'cell' => 'I60',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov2_label'] = array(
				'cell' => 'I61',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov3_label'] = array(
				'cell' => 'I62',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov4_label'] = array(
				'cell' => 'I63',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['prov5_label'] = array(
				'cell' => 'I64',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['VCM'] = array(
				'cell' => 'N65',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['VCM_label'] = array(
				'cell' => 'Combo!R77',
				'type' => 'calcr',
				'oblig' => false,
		);

		$this->targetInfoArray['rachat'] = array(
				'cell' => 'J67',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['offre'] = array(
				'cell' => 'L67',
				'type' => 'val',
				'oblig' => false,
		);

		$this->targetInfoArray['surres'] = array(
				'cell' => 'N67',
				'type' => 'calc',
				'oblig' => false,
		);
	}

	/**
	 *
	 * @return number
	 */
	public function loadData() {
		$error = 0;

		$this->objWorksheet->getCell('Combo!R77')->setValue('=RECHERCHEV(R75+1;P6:S42;3;FAUX)');

		dol_syslog(get_class($this) . '::' . __METHOD__, LOG_DEBUG);

		foreach ($this->targetInfoArray as $key => $info){
			if($info['type'] == 'calc'){
				$this->targetInfoArray[$key]['value']= $this->objWorksheet->getCell($info['cell'])->getOldCalculatedValue();
			}elseif($info['type'] == 'val'){
				$this->targetInfoArray[$key]['value']= $this->objWorksheet->getCell($info['cell'])->getValue();
			}elseif($info['type'] == 'calcr'){
				$this->targetInfoArray[$key]['value']= $this->objWorksheet->getCell($info['cell'])->getValue();
			}
		}

		foreach ($this->targetInfoArray as $key => $info){
			if($info['oblig']== true and empty($info['value'])){
				$this->errors[] = $key . ' must not be empty';
				$error ++;
			}

		}

		if (empty($error)) {
			return 1;
		} else {
			return - 1 * $error;
		}

	}



}